<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * POST api/subscription/events: the subscription portal tells the CRM straight away when the
 * Sales CRM subscription changes (paid, payment failed, trial ended, cancelled...).
 * Signed with the app secret from Third-Party API > Subscription; see Subscription_model::handle_event().
 */
class Subscription_events extends CI_Controller
{
	public function index()
	{
		header('Content-Type: application/json; charset=utf-8');
		if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
			http_response_code(405);
			echo json_encode(array('ok' => false, 'error' => 'POST only.'));
			return;
		}
		$raw = (string)file_get_contents('php://input');
		if (strlen($raw) > 65536) {
			http_response_code(413);
			echo json_encode(array('ok' => false, 'error' => 'Too large.'));
			return;
		}
		$this->load->database();
		$this->load->model('subscription_model');
		$ts = isset($_SERVER['HTTP_X_SUITE_TIMESTAMP']) ? $_SERVER['HTTP_X_SUITE_TIMESTAMP'] : '';
		$sig = isset($_SERVER['HTTP_X_SUITE_SIGNATURE']) ? $_SERVER['HTTP_X_SUITE_SIGNATURE'] : '';
		list($code, $msg) = $this->subscription_model->handle_event($raw, $ts, $sig);
		http_response_code($code);
		echo json_encode($code === 200 ? array('ok' => true, 'message' => $msg) : array('ok' => false, 'error' => $msg));
	}
}
