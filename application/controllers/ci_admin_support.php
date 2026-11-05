<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Live Support inbox for support agents (Admin, Support role, or the
 * "live_support" permission). Agents switch "Available" on to receive chats
 * from the public Support page; the open inbox keeps them marked online.
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

	public static function can_answer()
	{
		$ci =& get_instance();
		$role = (int)$ci->session->userdata('manager_role');
		if ($role === 1 || $role === 4) {
			return true;
		}
		return function_exists('has_perm') && has_perm('live_support', 'write');
	}

	private function guard($ajax = false)
	{
		if ($this->session->userdata('manager_id') == '') {
			if ($ajax) {
				$this->json(array('ok' => false, 'error' => 'Session expired. Please sign in again.'), 401);
				return false;
			}
			redirect('manager/login');
		}
		if (!self::can_answer()) {
			if ($ajax) {
				$this->json(array('ok' => false, 'error' => 'You do not have access to Live Support.'), 403);
				return false;
			}
			$this->session->set_flashdata('error_message', 'You do not have access to Live Support.');
			redirect('manager/dashboard');
		}
		$this->uid = (int)$this->session->userdata('manager_id');
		return true;
	}

	public function index()
	{
		$this->guard();
		$this->ci_admin_model->check_auth_timeout();
		$data = array();
		$data['info'] = array('title' => 'Live Support', 'page_heading' => 'Live Support');
		$data['available'] = $this->support_model->agent_state($this->uid);
		$data['it_email'] = $this->support_model->it_email();
		$this->load->view('manager/support/inbox', $data);
	}

	/** GET: heartbeat + chat list (+ messages of the open chat). */
	public function poll()
	{
		if (!$this->guard(true)) return;
		// The inbox polling keeps the agent's CRM session alive as well.
		$this->session->set_userdata('last_activity_time', time());
		$this->support_model->agent_heartbeat($this->uid);

		$chats = $this->support_model->inbox_chats();
		foreach ($chats as &$c) {
			// Hand over chats nobody answered (also when no visitor is polling any more).
			if ($c['status'] === 'waiting' && $this->support_model->handover_if_unanswered($this->support_model->get_chat($c['id']))) {
				$c['status'] = 'emailed';
			}
			$c['last_message'] = mb_substr((string)$c['last_message'], 0, 90);
		}
		unset($c);

		$out = array(
			'ok' => true,
			'available' => $this->support_model->agent_state($this->uid),
			'online_agents' => $this->support_model->agents_online(),
			'chats' => $chats,
		);
		$chat_id = (int)$this->input->get('chat');
		if ($chat_id > 0) {
			$chat = $this->support_model->get_chat($chat_id);
			if (!empty($chat)) {
				unset($chat['token']);
				$out['chat'] = $chat;
				$out['messages'] = array();
				foreach ($this->support_model->messages($chat_id, (int)$this->input->get('after')) as $m) {
					$out['messages'][] = array(
						'id' => (int)$m['id'],
						'sender' => $m['sender'],
						'name' => $m['sender'] === 'agent' ? ($m['agent_name'] ? $m['agent_name'] : 'Support') : ($m['sender'] === 'visitor' ? $chat['name'] : ''),
						'body' => $m['body'],
						'time' => substr($m['created_at'], 11, 5),
					);
				}
			}
		}
		$this->json($out);
	}

	/** POST: available = 1|0 */
	public function availability()
	{
		if (!$this->guard(true)) return;
		$on = (int)$this->input->post('available') === 1;
		$this->support_model->agent_heartbeat($this->uid, $on);
		$this->ci_admin_model->log_admin_action('live_support', 'Live support availability: ' . ($on ? 'available' : 'away'));
		$this->json(array('ok' => true, 'available' => $on ? 1 : 0, 'online_agents' => $this->support_model->agents_online()));
	}

	/** POST: chat_id, body */
	public function reply()
	{
		if (!$this->guard(true)) return;
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
		if (!$this->guard(true)) return;
		$chat = $this->support_model->get_chat((int)$this->input->post('chat_id'));
		if (empty($chat)) {
			return $this->json(array('ok' => false, 'error' => 'Chat not found.'), 404);
		}
		if ($chat['status'] !== 'closed') {
			$this->support_model->add_message($chat['id'], 'system', 'Support closed the chat. Thank you for contacting Seven Strokes.');
			$this->support_model->set_status($chat['id'], 'closed', (int)$chat['agent_id'] === 0 ? array('agent_id' => $this->uid) : array());
		}
		$this->json(array('ok' => true));
	}

	private function json($payload, $code = 200)
	{
		$this->output->set_status_header($code);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($payload));
	}
}
