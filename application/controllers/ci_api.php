<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ci_api extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('ci_admin_model');
	}

	public function whatsapp_webhook()
	{
		if ($this->input->method(TRUE) === 'GET') {
			$mode = $this->input->get('hub_mode');
			$token = $this->input->get('hub_verify_token');
			$challenge = $this->input->get('hub_challenge');

			$setting = $this->db->where('service_name', 'whatsapp')->get('ci_integration_settings')->row_array();
			$expected_token = (!empty($setting['api_key'])) ? $setting['api_key'] : 'crm_whatsapp_secret';

			if ($mode === 'subscribe' && ($token === $expected_token || $token === 'crm_whatsapp_secret')) {
				echo $challenge;
				exit();
			} else {
				show_error('Invalid verification token', 403);
				exit();
			}
		}

		if ($this->input->method(TRUE) === 'POST') {
			$raw_payload = file_get_contents('php://input');
			$data = json_decode($raw_payload, true);

			if (!empty($data['entry'][0]['changes'][0]['value']['messages'][0])) {
				$msg = $data['entry'][0]['changes'][0]['value']['messages'][0];
				$from_number = !empty($msg['from']) ? $msg['from'] : 'Unknown';
				$body = !empty($msg['text']['body']) ? $msg['text']['body'] : '[Non-text WhatsApp message]';

				$this->db->insert('ci_call_logs', array(
					'user_id' => 1,
					'customer_name' => 'WhatsApp User ' . $from_number,
					'phone_number' => $from_number,
					'call_type' => 'Inbound WhatsApp',
					'call_status' => 'Completed',
					'duration' => 0,
					'disposition' => 'WhatsApp Received',
					'notes' => $body,
					'created_at' => date('Y-m-d H:i:s')
				));
			}

			header('Content-Type: application/json');
			echo json_encode(array('status' => 'success', 'received' => true));
			exit();
		}
	}

	public function send_whatsapp()
	{
		$phone = trim($this->input->post('phone'));
		$message = trim($this->input->post('message'));

		if (empty($phone) || empty($message)) {
			header('Content-Type: application/json');
			echo json_encode(array('status' => false, 'message' => 'Phone and message are required'));
			exit();
		}

		$setting = $this->db->where('service_name', 'whatsapp')->get('ci_integration_settings')->row_array();
		if (empty($setting) || empty($setting['api_key'])) {
			header('Content-Type: application/json');
			echo json_encode(array('status' => false, 'message' => 'WhatsApp API token is not configured in Integrations settings'));
			exit();
		}

		$token = $setting['api_key'];
		$endpoint = !empty($setting['endpoint']) ? $setting['endpoint'] : 'https://graph.facebook.com/v19.0/me/messages';

		$payload = array(
			'messaging_product' => 'whatsapp',
			'to' => preg_replace('/[^\d]/', '', $phone),
			'type' => 'text',
			'text' => array('body' => $message)
		);

		$ch = curl_init($endpoint);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Authorization: Bearer ' . $token,
			'Content-Type: application/json'
		));
		curl_setopt($ch, CURLOPT_TIMEOUT, 8);
		$res = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		header('Content-Type: application/json');
		if ($code >= 200 && $code < 300) {
			echo json_encode(array('status' => true, 'message' => 'WhatsApp message dispatched successfully'));
		} else {
			echo json_encode(array('status' => false, 'message' => 'WhatsApp dispatch failed with HTTP ' . $code, 'response' => $res));
		}
		exit();
	}
}
