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
		if (!$this->db->field_exists('user_id', 'ci_support_chats')) {
			$this->db->query("ALTER TABLE ci_support_chats ADD user_id INT(11) NOT NULL DEFAULT 0, ADD user_last_read INT(11) NOT NULL DEFAULT 0, ADD KEY user_id (user_id)");
		}
		if (!$this->db->field_exists('reopened_at', 'ci_support_chats')) {
			$this->db->query("ALTER TABLE ci_support_chats ADD reopened_at DATETIME DEFAULT NULL");
		}
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_support_reads (
			user_id INT(11) NOT NULL,
			chat_id INT(11) NOT NULL,
			last_id INT(11) NOT NULL DEFAULT 0,
			PRIMARY KEY (user_id, chat_id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
	}

	/* ---------- signed-in user's own support chat (profile menu > Support) ---------- */

	/** The user's latest support chat (any status), or null. */
	public function user_chat($user_id)
	{
		$c = $this->db->where('user_id', (int)$user_id)->order_by('id', 'DESC')->limit(1)->get('ci_support_chats')->row_array();
		return empty($c) ? null : $c;
	}

	/** Support replies the user has not seen yet. */
	public function user_unread($user_id)
	{
		$c = $this->user_chat($user_id);
		if (!$c) return 0;
		return (int)$this->db->where('chat_id', (int)$c['id'])->where('sender', 'agent')->where('id >', (int)$c['user_last_read'])->count_all_results('ci_support_messages');
	}

	public function user_mark_read($chat_id)
	{
		$cid = (int)$chat_id;
		$this->db->query("UPDATE ci_support_chats SET user_last_read = (SELECT COALESCE(MAX(id), 0) FROM ci_support_messages WHERE chat_id = $cid) WHERE id = $cid");
	}

	/* ---------- unread (per agent) ---------- */

	/** chat_id => number of visitor messages this agent has not seen, in open chats not taken by someone else. */
	public function unread_counts($user_id)
	{
		$uid = (int)$user_id;
		$rows = $this->db->query("SELECT m.chat_id, COUNT(*) AS n FROM ci_support_messages m
			JOIN ci_support_chats c ON c.id = m.chat_id AND c.status IN ('waiting','active') AND (c.agent_id = 0 OR c.agent_id = $uid)
			LEFT JOIN ci_support_reads r ON r.user_id = $uid AND r.chat_id = m.chat_id
			WHERE m.sender = 'visitor' AND m.id > COALESCE(r.last_id, 0)
			GROUP BY m.chat_id")->result_array();
		$out = array();
		foreach ($rows as $row) $out[(int)$row['chat_id']] = (int)$row['n'];
		return $out;
	}

	/** Marks every message of a chat as seen by this agent. */
	public function mark_read($user_id, $chat_id)
	{
		$uid = (int)$user_id; $cid = (int)$chat_id;
		$this->db->query("INSERT INTO ci_support_reads (user_id, chat_id, last_id)
			SELECT $uid, $cid, COALESCE(MAX(id), 0) FROM ci_support_messages WHERE chat_id = $cid
			ON DUPLICATE KEY UPDATE last_id = VALUES(last_id)");
	}

	/* ---------- settings ---------- */

	/**
	 * Chatbot settings (Chatbot > Settings), stored in ci_integration_settings as service "chatbot".
	 */
	public function settings()
	{
		static $cache = null;
		if ($cache !== null) return $cache;
		$defaults = array(
			'enabled' => 1,
			'it_email' => '',
			'no_reply_minutes' => 3,
			'welcome_message' => 'Thanks {name}. You are connected to live support. An agent will reply shortly.',
			'offline_message' => 'Support is not available right now. Your request has been sent to our IT team, who will contact you on {phone}.',
		);
		$row = $this->db->where('service_name', 'chatbot')->get('ci_integration_settings')->row_array();
		$cfg = !empty($row['config_json']) ? json_decode($row['config_json'], true) : array();
		$cfg = is_array($cfg) ? array_merge($defaults, array_intersect_key($cfg, $defaults)) : $defaults;
		$cfg['enabled'] = (int)$cfg['enabled'] === 1 ? 1 : 0;
		$cfg['no_reply_minutes'] = max(1, min(30, (int)$cfg['no_reply_minutes']));
		return $cache = $cfg;
	}

	public function save_settings($in)
	{
		$cfg = array(
			'enabled' => !empty($in['enabled']) ? 1 : 0,
			'it_email' => (isset($in['it_email']) && filter_var(trim($in['it_email']), FILTER_VALIDATE_EMAIL)) ? trim($in['it_email']) : '',
			'no_reply_minutes' => max(1, min(30, (int)(isset($in['no_reply_minutes']) ? $in['no_reply_minutes'] : 3))),
			'welcome_message' => mb_substr(trim(strip_tags((string)(isset($in['welcome_message']) ? $in['welcome_message'] : ''))), 0, 300),
			'offline_message' => mb_substr(trim(strip_tags((string)(isset($in['offline_message']) ? $in['offline_message'] : ''))), 0, 300),
		);
		$now = date('Y-m-d H:i:s');
		$exists = $this->db->where('service_name', 'chatbot')->count_all_results('ci_integration_settings') > 0;
		if ($exists) {
			$this->db->where('service_name', 'chatbot')->update('ci_integration_settings', array('config_json' => json_encode($cfg), 'status' => $cfg['enabled'], 'updated_at' => $now));
		} else {
			$this->db->insert('ci_integration_settings', array('service_name' => 'chatbot', 'config_json' => json_encode($cfg), 'status' => $cfg['enabled'], 'created_at' => $now, 'updated_at' => $now));
		}
		return $cfg;
	}

	private function fill($text, $chat)
	{
		return strtr($text, array('{name}' => $chat['name'], '{phone}' => $chat['phone'], '{ref}' => $chat['ref_no']));
	}

	public function welcome_text($chat)
	{
		$s = $this->settings();
		return $this->fill($s['welcome_message'] !== '' ? $s['welcome_message'] : 'Thanks {name}. You are connected to live support. An agent will reply shortly.', $chat);
	}

	public function offline_text($chat)
	{
		$s = $this->settings();
		return $this->fill($s['offline_message'] !== '' ? $s['offline_message'] : 'Support is not available right now. Your request has been sent to our IT team, who will contact you on {phone}.', $chat);
	}

	public function no_reply_seconds()
	{
		$s = $this->settings();
		return 60 * $s['no_reply_minutes'];
	}

	/** IT mailbox: Chatbot settings, else Integrations > Helpdesk "support email", else it@7strokes.ae. */
	public function it_email()
	{
		$s = $this->settings();
		if (!empty($s['it_email'])) {
			return $s['it_email'];
		}
		$row = $this->db->where('service_name', 'helpdesk')->get('ci_integration_settings')->row_array();
		$cfg = !empty($row['config_json']) ? json_decode($row['config_json'], true) : array();
		if (!empty($cfg['support_email']) && filter_var($cfg['support_email'], FILTER_VALIDATE_EMAIL)) {
			return $cfg['support_email'];
		}
		return self::DEFAULT_IT_EMAIL;
	}

	/* ---------- agents ---------- */

	/** Agents marked Active, seen recently, and still holding the Chatbot "Go Active" right. 0 when the chatbot is off. */
	public function agents_online()
	{
		$s = $this->settings();
		if (!$s['enabled']) {
			return 0;
		}
		if (!function_exists('chatbot_can')) {
			$this->load->model('ci_admin_model'); // defines chatbot_can() (the public Support page does not load it)
		}
		$since = date('Y-m-d H:i:s', time() - self::AGENT_TIMEOUT_SECONDS);
		$rows = $this->db->select('user_id')->where('available', 1)->where('last_seen >=', $since)->get('ci_support_agents')->result_array();
		$n = 0;
		foreach ($rows as $r) {
			if (chatbot_can('status', (int)$r['user_id'])) {
				$n++;
			}
		}
		return $n;
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

	/** The visitor's last closed/emailed chat: by the browser's previous chat token, else by phone number. */
	public function returning_chat($token, $phone)
	{
		$token = (string)$token;
		if (preg_match('/^[a-f0-9]{48}$/', $token)) {
			$c = $this->db->where('token', $token)->where_in('status', array('closed', 'emailed'))->get('ci_support_chats')->row_array();
			if (!empty($c)) return $c;
		}
		if ($phone !== '') {
			$c = $this->db->where('phone', $phone)->where_in('status', array('closed', 'emailed'))->order_by('id', 'DESC')->limit(1)->get('ci_support_chats')->row_array();
			if (!empty($c)) return $c;
		}
		return null;
	}

	/** Same visitor needs help again: the chat moves back to Open with the new message. */
	public function reopen_chat($chat, $issue, $details = array())
	{
		$now = date('Y-m-d H:i:s');
		$this->db->where('id', (int)$chat['id'])->update('ci_support_chats', array_merge($details, array(
			'status' => 'waiting', 'agent_id' => 0, 'closed_at' => null, 'reopened_at' => $now, 'updated_at' => $now,
		)));
		$this->add_message($chat['id'], 'system', 'The visitor came back and needs help again.');
		$this->add_message($chat['id'], 'visitor', $issue);
		return $this->get_chat($chat['id']);
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
		if (empty($chat) || $chat['status'] !== 'waiting' || !empty($chat['user_id'])) {
			return false; // signed-in users' chats are never handed over by email
		}
		$since = !empty($chat['reopened_at']) ? $chat['reopened_at'] : $chat['created_at'];
		if (time() - strtotime($since) < $this->no_reply_seconds()) {
			return false;
		}
		$this->email_it($chat, 'No support agent answered the live chat within ' . round($this->no_reply_seconds() / 60) . ' minutes.');
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
			? $this->offline_text($chat)
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

	/* ---------- history ---------- */

	/** Filtered chat list for the History page. Returns array(rows, total). */
	public function history($f, $limit = 25, $offset = 0)
	{
		$apply = function ($db) use ($f) {
			$db->from('ci_support_chats c')->join('ci_user u', 'u.id = c.agent_id', 'left');
			if (!empty($f['q'])) {
				$q = $db->escape_like_str($f['q']);
				$db->where("(c.ref_no LIKE '%{$q}%' OR c.name LIKE '%{$q}%' OR c.phone LIKE '%{$q}%' OR c.email LIKE '%{$q}%' OR c.issue LIKE '%{$q}%')", null, false);
			}
			if (!empty($f['status']) && in_array($f['status'], array('waiting', 'active', 'emailed', 'closed'), true)) {
				$db->where('c.status', $f['status']);
			}
			if (!empty($f['from'])) $db->where('c.created_at >=', $f['from'] . ' 00:00:00');
			if (!empty($f['to'])) $db->where('c.created_at <=', $f['to'] . ' 23:59:59');
			if (!empty($f['agent'])) $db->where('c.agent_id', (int)$f['agent']);
		};
		$apply($this->db);
		$total = (int)$this->db->count_all_results();
		$this->db->select('c.id, c.ref_no, c.name, c.phone, c.email, c.status, c.created_at, c.updated_at, c.closed_at, c.emailed_at, u.name AS agent_name, (SELECT COUNT(*) FROM ci_support_messages m WHERE m.chat_id = c.id) AS msg_count', false);
		$apply($this->db);
		$rows = $this->db->order_by('c.id', 'desc')->limit($limit, $offset)->get()->result_array();
		return array($rows, $total);
	}

	public function history_agents()
	{
		return $this->db->query("SELECT DISTINCT u.id, u.name FROM ci_support_chats c JOIN ci_user u ON u.id = c.agent_id ORDER BY u.name")->result_array();
	}

	public function status_counts()
	{
		$out = array('waiting' => 0, 'active' => 0, 'emailed' => 0, 'closed' => 0, 'today' => 0);
		foreach ($this->db->query("SELECT status, COUNT(*) n FROM ci_support_chats GROUP BY status")->result_array() as $r) {
			$out[$r['status']] = (int)$r['n'];
		}
		$out['today'] = (int)$this->db->where('created_at >=', date('Y-m-d') . ' 00:00:00')->count_all_results('ci_support_chats');
		return $out;
	}
}
