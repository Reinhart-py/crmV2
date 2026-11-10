<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Chatbot (Live Support) pages for the person who answers the public Support chat.
 *
 * Access is user-wise only (System > Roles > User-wise > Chatbot) and is NOT
 * implied by Admin or any role. Rights: access, status, reply, close, history, settings.
 */
class Ci_admin_support extends CI_Controller {

	private $uid = 0;

	public function __construct()
	{
		parent::__construct();
		$this->load->library(array('Session'));
		$this->load->helper(array('form', 'url', 'text'));
		$this->load->model(array('ci_admin_model', 'comman_model', 'support_model'));
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Pragma: no-cache');
	}

	/** Page guard: redirects when signed out or without the right. */
	private function guard_page($right = 'access')
	{
		if ($this->session->userdata('manager_id') == '') {
			redirect('manager/login');
		}
		$this->ci_admin_model->check_auth_timeout();
		if (!chatbot_can($right)) {
			$this->session->set_flashdata('error_message', chatbot_can('access') ? 'You do not have this Chatbot right. Ask your administrator.' : 'You do not have access to the Chatbot.');
			redirect(chatbot_can('access') ? 'manager/chatbot' : 'manager/dashboard');
		}
		$this->uid = (int)$this->session->userdata('manager_id');
	}

	/** AJAX guard: JSON error instead of a redirect. */
	private function guard_ajax($right = 'access')
	{
		if ($this->session->userdata('manager_id') == '') {
			$this->json(array('ok' => false, 'error' => 'Session expired. Please sign in again.'), 401);
			return false;
		}
		if (!chatbot_can($right)) {
			$this->json(array('ok' => false, 'error' => 'You do not have permission for this Chatbot action.'), 403);
			return false;
		}
		$this->uid = (int)$this->session->userdata('manager_id');
		return true;
	}

	private function common_data($title)
	{
		return array(
			'info' => array('title' => $title . ' - Chatbot', 'page_heading' => $title),
			'rights' => chatbot_user_rights(),
			'settings' => $this->support_model->settings(),
			'counts' => $this->support_model->status_counts(),
		);
	}

	/* ---------------- Inbox ---------------- */

	public function index()
	{
		$this->chat_list('open');
	}

	public function closed()
	{
		$this->chat_list('closed');
	}

	/** Open Chats (new + not closed) and Closed Chats pages share one view. */
	private function chat_list($tab)
	{
		$this->guard_page('access');
		$data = $this->common_data($tab === 'closed' ? 'Closed Chats' : 'Open Chats');
		$data['list_tab'] = $tab;
		$data['available'] = chatbot_can('status') ? $this->support_model->agent_state($this->uid) : 0;
		$data['it_email'] = $this->support_model->it_email();
		$data['open_chat'] = (int)$this->input->get('chat');
		$this->load->view($tab === 'closed' ? 'manager/support/closed_chats' : 'manager/support/open_chats', $data);
	}

	/** Old URL. */
	public function legacy()
	{
		redirect('manager/chatbot');
	}

	/** GET: heartbeat + chat list (+ messages of the open chat). */
	public function poll()
	{
		if (!$this->guard_ajax('access')) return;
		// Inbox polling keeps the CRM session alive while the page is open.
		$this->session->set_userdata('last_activity_time', time());
		$can_status = chatbot_can('status');
		// Someone whose "Go Active" right was removed is taken off duty.
		$this->support_model->agent_heartbeat($this->uid, $can_status ? null : false);

		$chat_id = (int)$this->input->get('chat');
		if ($chat_id > 0) $this->support_model->mark_read($this->uid, $chat_id);
		$unread = $this->support_model->unread_counts($this->uid);

		$chats = $this->support_model->inbox_chats();
		foreach ($chats as &$c) {
			if ($c['status'] === 'waiting' && $this->support_model->handover_if_unanswered($this->support_model->get_chat($c['id']))) {
				$c['status'] = 'emailed';
			}
			$c['last_message'] = mb_substr((string)$c['last_message'], 0, 90);
			$c['unread'] = isset($unread[(int)$c['id']]) ? $unread[(int)$c['id']] : 0;
		}
		unset($c);

		$settings = $this->support_model->settings();
		$out = array(
			'ok' => true,
			'available' => $can_status ? $this->support_model->agent_state($this->uid) : 0,
			'online_agents' => $this->support_model->agents_online(),
			'enabled' => (int)$settings['enabled'],
			'chats' => $chats,
			'unread_total' => array_sum($unread),
		);
		if ($chat_id > 0) {
			$chat = $this->support_model->get_chat($chat_id);
			if (!empty($chat)) {
				unset($chat['token']);
				$out['chat'] = $chat;
				$out['messages'] = $this->format_messages($chat, $this->support_model->messages($chat_id, (int)$this->input->get('after')));
			}
		}
		$this->json($out);
	}

	/** GET: unread message count for the Chatbot menu badge. */
	public function unread()
	{
		if (!$this->guard_ajax('access')) return;
		$this->json(array('ok' => true, 'unread' => array_sum($this->support_model->unread_counts($this->uid))));
	}

	/* ---------------- My Support (any signed-in user, profile menu) ---------------- */

	private function me()
	{
		$this->uid = (int)$this->session->userdata('manager_id');
		return $this->uid > 0;
	}

	/** Help & Support (profile menu): help topics to search plus the user's own chat with support. */
	public function help()
	{
		if (!$this->me()) redirect('manager/login');
		$this->ci_admin_model->check_auth_timeout();
		if (!crm_can('help_support', 'read')) {
			$this->session->set_flashdata('error_message', 'You do not have access to Help & Support. Ask your administrator if you need it.');
			redirect(crm_landing_url());
		}
		$this->load->model('hr_selfservice_model');
		$role = (int)$this->session->userdata('manager_role');
		$data = array('info' => array('title' => 'Help & Support', 'page_heading' => 'Help & Support'));
		$data['topics'] = $this->hr_selfservice_model->topics('', '', false);
		$data['can_manage_topics'] = ($role === 1 || $this->uid === 1 || has_section_perm('hr', 'write') || crm_can('hr_topics', 'write'));
		$this->load->view('manager/support/help', $data);
	}

	/** Old Support page: now part of Help & Support. */
	public function my_support()
	{
		redirect('manager/help#chat');
	}

	/** GET: after -> new messages of the user's chat (marks them read). */
	public function my_support_poll()
	{
		if (!$this->me()) return $this->json(array('ok' => false, 'error' => 'Session expired. Please sign in again.'), 401);
		$this->session->set_userdata('last_activity_time', time());
		$chat = $this->support_model->user_chat($this->uid);
		if (!$chat) return $this->json(array('ok' => true, 'status' => '', 'messages' => array()));
		$msgs = $this->format_messages($chat, $this->support_model->messages($chat['id'], (int)$this->input->get('after')));
		$this->support_model->user_mark_read($chat['id']);
		$this->json(array('ok' => true, 'status' => $chat['status'], 'ref_no' => $chat['ref_no'], 'messages' => $msgs));
	}

	/** POST: body -> message to support; starts or reopens the user's chat. */
	public function my_support_send()
	{
		if (!$this->me()) return $this->json(array('ok' => false, 'error' => 'Session expired. Please sign in again.'), 401);
		if ($this->input->server('REQUEST_METHOD') !== 'POST') return $this->json(array('ok' => false, 'error' => 'Invalid request.'), 405);
		$body = trim(strip_tags((string)$this->input->post('body')));
		if ($body === '' || mb_strlen($body) > 2000) return $this->json(array('ok' => false, 'error' => 'Message must be 1 to 2000 characters.'), 400);

		$chat = $this->support_model->user_chat($this->uid);
		if ($chat && in_array($chat['status'], array('waiting', 'active'), true)) {
			$this->support_model->add_message($chat['id'], 'visitor', $body);
		} else {
			$u = $this->db->select('name, mobile, email, c_email')->where('id', $this->uid)->get('ci_user')->row_array();
			$details = array(
				'name' => !empty($u['name']) ? $u['name'] : 'CRM user',
				'phone' => !empty($u['mobile']) ? $u['mobile'] : '',
				'email' => !empty($u['c_email']) ? $u['c_email'] : (!empty($u['email']) ? $u['email'] : ''),
			);
			if ($chat) {
				$chat = $this->support_model->reopen_chat($chat, $body, $details);
			} else {
				$chat = $this->support_model->create_chat($details['name'], $details['phone'], $details['email'], $body, $this->input->ip_address(), $this->input->user_agent());
				$this->db->where('id', (int)$chat['id'])->update('ci_support_chats', array('user_id' => $this->uid));
			}
			$this->support_model->add_message($chat['id'], 'system', $this->support_model->agents_online() > 0
				? 'You are connected to support. Someone will reply here shortly.'
				: 'Support is not online right now. Your message is saved and support will reply here.');
		}
		$this->json(array('ok' => true));
	}

	/** GET: is anyone on live chat now, and does the user have a chat that is still open? */
	public function my_support_status()
	{
		if (!$this->me()) return $this->json(array('ok' => false, 'error' => 'Session expired. Please sign in again.'), 401);
		$chat = $this->support_model->user_chat($this->uid);
		$u = $this->db->select('email, c_email')->where('id', $this->uid)->get('ci_user')->row_array();
		$this->json(array(
			'ok' => true,
			'online' => $this->support_model->agents_online() > 0,
			'open' => $chat && in_array($chat['status'], array('waiting', 'active'), true),
			'email' => !empty($u['c_email']) ? $u['c_email'] : (!empty($u['email']) ? $u['email'] : ''),
		));
	}

	/** POST: email, body -> live chat is offline: the message is saved with the user's chat and emailed to the support mailbox. */
	public function my_support_message()
	{
		if (!$this->me()) return $this->json(array('ok' => false, 'error' => 'Session expired. Please sign in again.'), 401);
		if ($this->input->server('REQUEST_METHOD') !== 'POST') return $this->json(array('ok' => false, 'error' => 'Invalid request.'), 405);
		$body = trim(strip_tags((string)$this->input->post('body')));
		$email = trim((string)$this->input->post('email'));
		$errors = array();
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Please enter a valid email address.';
		if (mb_strlen($body) < 10) $errors['body'] = 'Please write your message (at least 10 characters).';
		if (mb_strlen($body) > 3000) $errors['body'] = 'Please keep the message under 3000 characters.';
		if ($errors) return $this->json(array('ok' => false, 'errors' => $errors), 400);

		$chat = $this->support_model->user_chat($this->uid);
		// One message every 2 minutes per person
		if ($chat && $chat['status'] === 'emailed' && !empty($chat['emailed_at']) && strtotime($chat['emailed_at']) > time() - 120) {
			return $this->json(array('ok' => false, 'error' => 'Your last message was sent a moment ago. Please wait two minutes before sending another.'), 429);
		}
		// Someone came online meanwhile: it goes to live chat instead
		if ($this->support_model->agents_online() > 0) {
			return $this->json(array('ok' => false, 'online' => true, 'error' => 'Live chat is now available. Please send your message in the chat.'), 409);
		}

		$u = $this->db->select('name, mobile')->where('id', $this->uid)->get('ci_user')->row_array();
		$details = array('name' => !empty($u['name']) ? $u['name'] : 'CRM user', 'phone' => !empty($u['mobile']) ? $u['mobile'] : '', 'email' => $email);
		if ($chat && in_array($chat['status'], array('waiting', 'active'), true)) {
			$this->support_model->add_message($chat['id'], 'visitor', $body);
			$this->db->where('id', (int)$chat['id'])->update('ci_support_chats', array('email' => $email));
		} elseif ($chat) {
			$chat = $this->support_model->reopen_chat($chat, $body, $details);
		} else {
			$chat = $this->support_model->create_chat($details['name'], $details['phone'], $email, $body, $this->input->ip_address(), $this->input->user_agent());
			$this->db->where('id', (int)$chat['id'])->update('ci_support_chats', array('user_id' => $this->uid));
		}
		$this->db->where('id', (int)$chat['id'])->update('ci_support_chats', array('issue' => $body)); // the email shows this message as the issue
		$chat = $this->support_model->get_chat($chat['id']);
		$sent = $this->support_model->email_it($chat, 'Live chat was offline when this message was sent.', 'a signed-in CRM user (Help & Support)',
			'Live chat was offline. Your message has been sent to the support team, who will get back to you at ' . $email . ' at the earliest.');
		$this->ci_admin_model->log_admin_action('support', 'Support message sent while live chat was offline (' . $chat['ref_no'] . ')');
		$this->json(array('ok' => true, 'sent' => (bool)$sent, 'ref_no' => $chat['ref_no']));
	}

	/** GET: number of support replies the user has not seen (profile badge). */
	public function my_support_unread()
	{
		if (!$this->me()) return $this->json(array('ok' => false), 401);
		$this->json(array('ok' => true, 'unread' => $this->support_model->user_unread($this->uid)));
	}

	/** POST: available = 1|0 (Active / Inactive) */
	public function availability()
	{
		if (!$this->guard_ajax('status')) return;
		$on = (int)$this->input->post('available') === 1;
		$this->support_model->agent_heartbeat($this->uid, $on);
		$this->ci_admin_model->log_admin_action('chatbot', 'Chatbot status: ' . ($on ? 'active' : 'inactive'));
		$this->json(array('ok' => true, 'available' => $on ? 1 : 0, 'online_agents' => $this->support_model->agents_online()));
	}

	/** POST: chat_id, body */
	public function reply()
	{
		if (!$this->guard_ajax('reply')) return;
		$chat = $this->support_model->get_chat((int)$this->input->post('chat_id'));
		if (empty($chat)) {
			return $this->json(array('ok' => false, 'error' => 'Chat not found.'), 404);
		}
		if ($chat['status'] === 'closed') {
			return $this->json(array('ok' => false, 'error' => 'This chat is closed.'), 409);
		}
		$body = trim(strip_tags((string)$this->input->post('body')));
		if ($body === '' || mb_strlen($body) > 2000) {
			return $this->json(array('ok' => false, 'error' => 'Message must be 1 to 2000 characters.'), 400);
		}
		// First reply takes the chat; a chat already emailed to IT can still be picked up.
		if (in_array($chat['status'], array('waiting', 'emailed'), true) || (int)$chat['agent_id'] === 0) {
			$this->support_model->set_status($chat['id'], 'active', array('agent_id' => $this->uid));
		}
		$id = $this->support_model->add_message($chat['id'], 'agent', $body, $this->uid);
		$this->json(array('ok' => true, 'id' => $id));
	}

	/** POST: chat_id -> close the chat. */
	public function close()
	{
		if (!$this->guard_ajax('close')) return;
		$chat = $this->support_model->get_chat((int)$this->input->post('chat_id'));
		if (empty($chat)) {
			return $this->json(array('ok' => false, 'error' => 'Chat not found.'), 404);
		}
		if ($chat['status'] !== 'closed') {
			$this->support_model->add_message($chat['id'], 'system', 'Support closed the chat. Thank you for contacting us.');
			$this->support_model->set_status($chat['id'], 'closed', (int)$chat['agent_id'] === 0 ? array('agent_id' => $this->uid) : array());
		}
		$this->json(array('ok' => true));
	}

	/* ---------------- History ---------------- */

	public function history()
	{
		$this->guard_page('history');
		$data = $this->common_data('Chat History');
		$f = array(
			'q' => trim((string)$this->input->get('q', true)),
			'status' => (string)$this->input->get('status', true),
			'from' => $this->ymd($this->input->get('from')),
			'to' => $this->ymd($this->input->get('to')),
			'agent' => (int)$this->input->get('agent'),
		);
		$per = 25;
		$page = max(1, (int)$this->input->get('page'));
		list($rows, $total) = $this->support_model->history($f, $per, ($page - 1) * $per);
		$data['filters'] = $f;
		$data['rows'] = $rows;
		$data['total'] = $total;
		$data['page'] = $page;
		$data['pages'] = max(1, (int)ceil($total / $per));
		$data['agents'] = $this->support_model->history_agents();
		$this->load->view('manager/support/history', $data);
	}

	public function transcript($id = 0)
	{
		$this->guard_page('history');
		$chat = $this->support_model->get_chat((int)$id);
		if (empty($chat)) {
			$this->session->set_flashdata('error_message', 'Conversation not found.');
			redirect('manager/chatbot/history');
		}
		$data = $this->common_data('Conversation ' . $chat['ref_no']);
		$agent = (int)$chat['agent_id'] ? $this->db->select('name')->where('id', (int)$chat['agent_id'])->get('ci_user')->row_array() : array();
		$chat['agent_name'] = !empty($agent['name']) ? $agent['name'] : '';
		unset($chat['token']);
		$data['chat'] = $chat;
		$data['messages'] = $this->format_messages($chat, $this->support_model->messages($chat['id']));
		$this->load->view('manager/support/transcript', $data);
	}

	/* ---------------- Settings ---------------- */

	public function settings()
	{
		$this->guard_page('settings');
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$in = array(
				'enabled' => $this->input->post('enabled'),
				'it_email' => $this->input->post('it_email', true),
				'no_reply_minutes' => $this->input->post('no_reply_minutes'),
				'welcome_message' => $this->input->post('welcome_message', true),
				'offline_message' => $this->input->post('offline_message', true),
			);
			if (trim((string)$in['it_email']) !== '' && !filter_var(trim($in['it_email']), FILTER_VALIDATE_EMAIL)) {
				$this->session->set_flashdata('error_message', 'IT email address is not valid.');
				redirect('manager/chatbot/settings');
			}
			$cfg = $this->support_model->save_settings($in);
			$this->ci_admin_model->log_admin_action('chatbot', 'Chatbot settings saved (chatbot ' . ($cfg['enabled'] ? 'on' : 'off') . ')');
			$this->session->set_flashdata('message', 'Chatbot settings saved.');
			redirect('manager/chatbot/settings');
		}
		$data = $this->common_data('Chatbot Settings');
		$data['it_email_effective'] = $this->support_model->it_email();
		$data['agents'] = $this->db->query("SELECT u.id, u.name, a.available, a.last_seen FROM ci_user_permissions p JOIN ci_user u ON u.id = p.user_id AND u.status = 1 LEFT JOIN ci_support_agents a ON a.user_id = u.id WHERE p.chatbot_permissions LIKE '%\"access\"%' ORDER BY u.name")->result_array();
		$this->load->view('manager/support/settings', $data);
	}

	/* ---------------- helpers ---------------- */

	private function format_messages($chat, $msgs)
	{
		$out = array();
		foreach ($msgs as $m) {
			$out[] = array(
				'id' => (int)$m['id'],
				'sender' => $m['sender'],
				'name' => $m['sender'] === 'agent' ? ($m['agent_name'] ? $m['agent_name'] : 'Support') : ($m['sender'] === 'visitor' ? $chat['name'] : ''),
				'body' => $m['body'],
				'time' => substr($m['created_at'], 11, 5),
				'date' => substr($m['created_at'], 0, 10),
			);
		}
		return $out;
	}

	private function ymd($v)
	{
		$v = trim((string)$v);
		return preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) ? $v : '';
	}

	/** One background check for every page (header): notifications, the user's support replies and, with the Chatbot right, waiting chats.
	    Replaces three separate timers. sup=0 / cb=0 leave a part out (the page shows it itself). */
	public function pulse()
	{
		if (!$this->me()) return $this->json(array('ok' => false, 'signed_out' => true));
		$this->ci_admin_model->check_auth_timeout();
		$notifs = $this->ci_admin_model->get_user_notifications($this->uid, 15);
		$unread = 0;
		foreach ((array)$notifs as $n) if (isset($n['is_read']) && (int)$n['is_read'] === 0) $unread++;
		$out = array('ok' => true, 'notif' => array('status' => true, 'unread_count' => $unread, 'notifications' => $notifs));
		if ($this->input->get('sup') !== '0') $out['sup'] = (int)$this->support_model->user_unread($this->uid);
		if ($this->input->get('cb') !== '0' && chatbot_can('access')) $out['cb'] = (int)array_sum($this->support_model->unread_counts($this->uid));
		$this->json($out);
	}

	private function json($payload, $code = 200)
	{
		$this->output->set_status_header($code);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($payload));
	}
}
