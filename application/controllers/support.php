<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Public Support page (linked from the sign-in screen). No login needed.
 * Live chat with the support team, or an email to IT when nobody is available.
 */
class Support extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library(array('session'));
		$this->load->helper(array('url'));
		$this->load->model(array('support_model'));
		header('X-Content-Type-Options: nosniff');
		header('X-Frame-Options: SAMEORIGIN');
		header('Referrer-Policy: strict-origin-when-cross-origin');
	}

	public function index()
	{
		$data = array();
		$data['info'] = array('title' => 'Support - Sales CRM');
		$data['online'] = $this->support_model->agents_online() > 0;
		$data['it_email'] = $this->support_model->it_email();
		$this->load->view('support/index', $data);
	}

	/** GET: is anyone available right now? */
	public function status()
	{
		$this->json(array('online' => $this->support_model->agents_online() > 0));
	}

	/** POST: name, phone, email, issue -> starts a chat (or emails IT when offline). */
	public function start()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') {
			return $this->json(array('ok' => false, 'error' => 'Invalid request.'), 405);
		}
		// Bots fill the hidden "website" field.
		if (trim((string)$this->input->post('website')) !== '') {
			return $this->json(array('ok' => false, 'error' => 'Invalid request.'), 400);
		}

		$name = $this->clean($this->input->post('name'), 120);
		$phone = $this->clean($this->input->post('phone'), 40);
		$email = $this->clean($this->input->post('email'), 190);
		$issue = trim(strip_tags((string)$this->input->post('issue')));

		// Support online: the chat opens straight away and the first message is the issue.
		$quick = (string)$this->input->post('chat') === '1' && $this->support_model->agents_online() > 0;
		if ($quick) {
			$issue = mb_substr($issue, 0, 2000);
			if ($issue === '') return $this->json(array('ok' => false, 'error' => 'Please type a message.'), 400);
			$name = 'Visitor'; $phone = ''; $email = '';
		}

		$errors = array();
		if (!$quick) {
		if (mb_strlen($name) < 2) $errors['name'] = 'Please enter your name.';
		if (!preg_match('/^\+?[0-9][0-9\s\-()]{5,30}$/', $phone)) $errors['phone'] = 'Please enter a valid phone number.';
		if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Please enter a valid email address.';
		if (mb_strlen($issue) < 10) $errors['issue'] = 'Please describe the issue (at least 10 characters).';
		if (mb_strlen($issue) > 3000) $errors['issue'] = 'Please keep the description under 3000 characters.';
		}
		if (!empty($errors)) {
			return $this->json(array('ok' => false, 'errors' => $errors), 400);
		}

		$ip = $this->input->ip_address();
		if ($this->support_model->rate_limited($ip)) {
			return $this->json(array('ok' => false, 'error' => 'Too many requests from this network. Please email ' . $this->support_model->it_email() . ' instead.'), 403);
		}

		$back = $this->support_model->returning_chat($this->input->post('prev'), $phone);
		if ($back) {
			$chat = $this->support_model->reopen_chat($back, $issue, $quick ? array() : array('name' => $name, 'phone' => $phone, 'email' => $email));
		} else {
			$chat = $this->support_model->create_chat($name, $phone, $email, $issue, $ip, $this->input->user_agent());
		}
		$online = $this->support_model->agents_online() > 0;
		if ($online) {
			$this->support_model->add_message($chat['id'], 'system', $this->support_model->welcome_text($chat));
		} else {
			$this->support_model->email_it($chat, 'Live support was offline when this request was sent.');
		}
		$chat = $this->support_model->get_chat($chat['id']);

		return $this->json(array(
			'ok' => true,
			'token' => $chat['token'],
			'ref_no' => $chat['ref_no'],
			'status' => $chat['status'],
			'online' => $online,
		));
	}

	/** GET: token, after -> new messages and chat status. */
	public function poll()
	{
		$chat = $this->support_model->chat_by_token($this->input->get('token'));
		if (empty($chat)) {
			return $this->json(array('ok' => false, 'error' => 'Chat not found.'), 404);
		}
		if ($this->support_model->handover_if_unanswered($chat)) {
			$chat = $this->support_model->get_chat($chat['id']);
		}
		$msgs = array();
		foreach ($this->support_model->messages($chat['id'], (int)$this->input->get('after')) as $m) {
			$msgs[] = array(
				'id' => (int)$m['id'],
				'sender' => $m['sender'],
				'name' => $m['sender'] === 'agent' ? ($m['agent_name'] ? $m['agent_name'] : 'Support') : ($m['sender'] === 'visitor' ? 'You' : ''),
				'body' => $m['body'],
				'time' => substr($m['created_at'], 11, 5),
			);
		}
		return $this->json(array('ok' => true, 'status' => $chat['status'], 'ref_no' => $chat['ref_no'], 'messages' => $msgs));
	}

	/** POST: token, body */
	public function send()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') {
			return $this->json(array('ok' => false, 'error' => 'Invalid request.'), 405);
		}
		$chat = $this->support_model->chat_by_token($this->input->post('token'));
		if (empty($chat)) {
			return $this->json(array('ok' => false, 'error' => 'Chat not found.'), 404);
		}
		if (!in_array($chat['status'], array('waiting', 'active'), true)) {
			return $this->json(array('ok' => false, 'error' => 'This chat has ended.'), 409);
		}
		$body = trim(strip_tags((string)$this->input->post('body')));
		if ($body === '' || mb_strlen($body) > 2000) {
			return $this->json(array('ok' => false, 'error' => 'Message must be 1 to 2000 characters.'), 400);
		}
		$id = $this->support_model->add_message($chat['id'], 'visitor', $body);
		return $this->json(array('ok' => true, 'id' => $id));
	}

	/** POST: token -> visitor ends the chat. */
	public function end()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') {
			return $this->json(array('ok' => false, 'error' => 'Invalid request.'), 405);
		}
		$chat = $this->support_model->chat_by_token($this->input->post('token'));
		if (!empty($chat) && in_array($chat['status'], array('waiting', 'active'), true)) {
			$this->support_model->add_message($chat['id'], 'system', 'The visitor ended the chat.');
			$this->support_model->set_status($chat['id'], 'closed');
		}
		return $this->json(array('ok' => true));
	}

	private function clean($v, $max)
	{
		$v = trim(preg_replace('/\s+/', ' ', strip_tags((string)$v)));
		return mb_substr($v, 0, $max);
	}

	private function json($payload, $code = 200)
	{
		$this->output->set_status_header($code);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($payload));
	}
}
