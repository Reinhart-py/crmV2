<?php
/**
 * Public live support chat (Support page on the sign-in screen).
 *
 * Flow: a visitor leaves name, phone, email and the issue. If a support agent is
 * available (Live Support inbox open with "Available" on), a live chat starts.
 * If nobody is available, or nobody answers within NO_REPLY_SECONDS, the request
 * is emailed to the IT team from the CRM's SMTP mailbox (noreply@...).
 */
class Support_model extends CI_Model {

	const AGENT_TIMEOUT_SECONDS = 90;   // agent counts as online if the inbox pinged within this time
	const NO_REPLY_SECONDS = 180;       // waiting chat with no agent reply is emailed to IT after this
	const MAX_REQUESTS_PER_HOUR = 5;    // per IP address
	const DEFAULT_IT_EMAIL = 'it@7strokes.ae';

	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->ensure_tables();
	}

	private function ensure_tables()
	{
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_support_chats (
			id INT(11) NOT NULL AUTO_INCREMENT,
			ref_no VARCHAR(20) DEFAULT NULL,
			token CHAR(48) NOT NULL,
			name VARCHAR(120) NOT NULL,
			phone VARCHAR(40) NOT NULL,
			email VARCHAR(190) DEFAULT NULL,
			issue TEXT NOT NULL,
			status VARCHAR(20) NOT NULL DEFAULT 'waiting',
			agent_id INT(11) NOT NULL DEFAULT 0,
			ip VARCHAR(45) DEFAULT NULL,
			user_agent VARCHAR(255) DEFAULT NULL,
			emailed_at DATETIME DEFAULT NULL,
			email_error VARCHAR(255) DEFAULT NULL,
			created_at DATETIME NOT NULL,
			updated_at DATETIME NOT NULL,
			closed_at DATETIME DEFAULT NULL,
			PRIMARY KEY (id),
			UNIQUE KEY token (token),
			KEY status (status),
			KEY ip_created (ip, created_at)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_support_messages (
			id INT(11) NOT NULL AUTO_INCREMENT,
			chat_id INT(11) NOT NULL,
			sender VARCHAR(10) NOT NULL,
			agent_id INT(11) NOT NULL DEFAULT 0,
			body TEXT NOT NULL,
			created_at DATETIME NOT NULL,
			PRIMARY KEY (id),
			KEY chat_id (chat_id, id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_support_agents (
			user_id INT(11) NOT NULL,
			available TINYINT(1) NOT NULL DEFAULT 0,
			last_seen DATETIME DEFAULT NULL,
			PRIMARY KEY (user_id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
	}

	/* ---------- settings ---------- */

	/** IT mailbox: System > Integrations > Helpdesk "support email", else it@7strokes.ae. */
	public function it_email()
	{
		$row = $this->db->where('service_name', 'helpdesk')->get('ci_integration_settings')->row_array();
		$cfg = !empty($row['config_json']) ? json_decode($row['config_json'], true) : array();
		if (!empty($cfg['support_email']) && filter_var($cfg['support_email'], FILTER_VALIDATE_EMAIL)) {
			return $cfg['support_email'];
		}
		return self::DEFAULT_IT_EMAIL;
	}

	/* ---------- agents ---------- */

	public function agents_online()
	{
		$since = date('Y-m-d H:i:s', time() - self::AGENT_TIMEOUT_SECONDS);
		return (int)$this->db->where('available', 1)->where('last_seen >=', $since)->count_all_results('ci_support_agents');
	}

	public function agent_state($user_id)
	{
		$row = $this->db->where('user_id', (int)$user_id)->get('ci_support_agents')->row_array();
		return !empty($row) ? (int)$row['available'] : 0;
	}

	public function agent_heartbeat($user_id, $available = null)
	{
		$user_id = (int)$user_id;
		$now = date('Y-m-d H:i:s');
		$exists = $this->db->where('user_id', $user_id)->count_all_results('ci_support_agents') > 0;
		$data = array('last_seen' => $now);
		if ($available !== null) {
			$data['available'] = $available ? 1 : 0;
		}
		if ($exists) {
			$this->db->where('user_id', $user_id)->update('ci_support_agents', $data);
		} else {
			$data['user_id'] = $user_id;
			if (!isset($data['available'])) {
				$data['available'] = 0;
			}
			$this->db->insert('ci_support_agents', $data);
		}
	}

	/* ---------- chats ---------- */

	public function rate_limited($ip)
	{
		$since = date('Y-m-d H:i:s', time() - 3600);
		return $this->db->where('ip', $ip)->where('created_at >=', $since)->count_all_results('ci_support_chats') >= self::MAX_REQUESTS_PER_HOUR;
	}

	public function create_chat($name, $phone, $email, $issue, $ip, $ua)
	{
		$now = date('Y-m-d H:i:s');
		$token = bin2hex(random_bytes(24));
		$this->db->insert('ci_support_chats', array(
			'token' => $token,
			'name' => $name,
			'phone' => $phone,
			'email' => $email,
			'issue' => $issue,
			'status' => 'waiting',
			'ip' => $ip,
			'user_agent' => substr((string)$ua, 0, 255),
			'created_at' => $now,
			'updated_at' => $now,
		));
		$id = (int)$this->db->insert_id();
		$ref = 'SUP-' . str_pad($id, 5, '0', STR_PAD_LEFT);
		$this->db->where('id', $id)->update('ci_support_chats', array('ref_no' => $ref));
		$this->add_message($id, 'visitor', $issue);
		return $this->get_chat($id);
	}

	public function get_chat($id)
	{
		return $this->db->where('id', (int)$id)->get('ci_support_chats')->row_array();
	}

	public function chat_by_token($token)
	{
		if (!preg_match('/^[a-f0-9]{48}$/', (string)$token)) {
			return null;
		}
		return $this->db->where('token', $token)->get('ci_support_chats')->row_array();
	}

	public function add_message($chat_id, $sender, $body, $agent_id = 0)
	{
		$now = date('Y-m-d H:i:s');
		$this->db->insert('ci_support_messages', array(
			'chat_id' => (int)$chat_id,
			'sender' => $sender,
			'agent_id' => (int)$agent_id,
			'body' => $body,
			'created_at' => $now,
		));
		$id = (int)$this->db->insert_id();
		$this->db->where('id', (int)$chat_id)->update('ci_support_chats', array('updated_at' => $now));
		return $id;
	}

	public function messages($chat_id, $after_id = 0)
	{
		return $this->db->select('m.id, m.sender, m.body, m.created_at, u.name AS agent_name', false)
			->from('ci_support_messages m')
			->join('ci_user u', 'u.id = m.agent_id', 'left')
			->where('m.chat_id', (int)$chat_id)
			->where('m.id >', (int)$after_id)
			->order_by('m.id', 'asc')
			->get()->result_array();
	}

	public function set_status($chat_id, $status, $extra = array())
	{
		$data = array_merge(array('status' => $status, 'updated_at' => date('Y-m-d H:i:s')), $extra);
		if ($status === 'closed') {
			$data['closed_at'] = date('Y-m-d H:i:s');
		}
		$this->db->where('id', (int)$chat_id)->update('ci_support_chats', $data);
	}

	/** Waiting chat with no agent reply for NO_REPLY_SECONDS -> email IT. Returns true if it was handed over now. */
	public function handover_if_unanswered($chat)
	{
		if (empty($chat) || $chat['status'] !== 'waiting') {
			return false;
		}
		if (time() - strtotime($chat['created_at']) < self::NO_REPLY_SECONDS) {
			return false;
		}
		$this->email_it($chat, 'No support agent answered the live chat within ' . round(self::NO_REPLY_SECONDS / 60) . ' minutes.');
		return true;
	}

	/** Sends the request (and any chat so far) to the IT mailbox, then marks the chat as emailed. */
	public function email_it($chat, $reason)
	{
		$this->load->model('mail_model');
		$to = $this->it_email();
		$h = function ($v) { return nl2br(htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8')); };

		$rows = '';
		foreach (array('Reference' => $chat['ref_no'], 'Name' => $chat['name'], 'Phone' => $chat['phone'], 'Email' => $chat['email'] ?: 'not given', 'Submitted' => $chat['created_at'], 'IP address' => $chat['ip']) as $k => $v) {
			$rows .= '<tr><td style="padding:6px 12px 6px 0;color:#64748b;white-space:nowrap;vertical-align:top;">' . $k . '</td><td style="padding:6px 0;color:#0f172a;font-weight:bold;">' . $h($v) . '</td></tr>';
		}
		$transcript = '';
		foreach ($this->messages($chat['id']) as $m) {
			$who = $m['sender'] === 'visitor' ? $chat['name'] : ($m['sender'] === 'agent' ? ($m['agent_name'] ?: 'Support') : 'System');
			$transcript .= '<p style="margin:0 0 8px;"><span style="color:#64748b;font-size:12px;">' . $h(substr($m['created_at'], 11, 5)) . ' ' . $h($who) . ':</span><br>' . $h($m['body']) . '</p>';
		}
		$msg = '<p><strong>New support request from the CRM sign-in page.</strong></p>'
			. '<p style="color:#64748b;">' . $h($reason) . ' Please contact the person below.</p>'
			. '<table cellpadding="0" cellspacing="0" style="margin:12px 0 18px;font-size:14px;">' . $rows . '</table>'
			. '<p style="margin:0 0 6px;"><strong>Issue</strong></p>'
			. '<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:12px;margin-bottom:18px;">' . $h($chat['issue']) . '</div>'
			. '<p style="margin:0 0 6px;"><strong>Conversation</strong></p>'
			. '<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:12px;">' . $transcript . '</div>';

		$opts = array();
		if (!empty($chat['email']) && filter_var($chat['email'], FILTER_VALIDATE_EMAIL)) {
			$opts['reply_to'] = array($chat['email'], $chat['name']);
		}
		$resp = $this->mail_model->sendmail($to, 'Support request ' . $chat['ref_no'] . ' - ' . $chat['name'], $opts, $msg, 'IT Team');
		$ok = ($resp === 'ok');
		$this->set_status($chat['id'], 'emailed', array(
			'emailed_at' => $ok ? date('Y-m-d H:i:s') : null,
			'email_error' => $ok ? null : substr($this->mail_model->last_error, 0, 255),
		));
		$this->add_message($chat['id'], 'system', $ok
			? 'Support is not available right now. Your request has been sent to our IT team, who will contact you on ' . $chat['phone'] . '.'
			: 'Support is not available right now and we could not send your request automatically. Please email ' . $to . ' with your details.');
		return $ok;
	}

	/* ---------- agent inbox ---------- */

	public function inbox_chats()
	{
		$since = date('Y-m-d H:i:s', time() - 7 * 86400);
		return $this->db->select("c.id, c.ref_no, c.name, c.phone, c.email, c.status, c.agent_id, c.created_at, c.updated_at, c.emailed_at, u.name AS agent_name,
				FIELD(c.status,'waiting','active','emailed','closed') AS status_rank,
				(SELECT m.body FROM ci_support_messages m WHERE m.chat_id = c.id ORDER BY m.id DESC LIMIT 1) AS last_message,
				(SELECT m.sender FROM ci_support_messages m WHERE m.chat_id = c.id ORDER BY m.id DESC LIMIT 1) AS last_sender", false)
			->from('ci_support_chats c')
			->join('ci_user u', 'u.id = c.agent_id', 'left')
			->where("(c.status IN ('waiting','active') OR c.updated_at >= " . $this->db->escape($since) . ")", null, false)
			->order_by('status_rank', 'asc')
			->order_by('c.updated_at', 'desc')
			->limit(100)
			->get()->result_array();
	}
}
