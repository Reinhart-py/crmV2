<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * POST api/suite/app-events: HR & Payroll tells the CRM about staff changes (see Suite_link_model).
 * Signed with the portal app secret; duplicates are skipped by X-Suite-Delivery.
 */
class Suite_events extends CI_Controller
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
		if (strlen($raw) > 262144) {
			http_response_code(413);
			echo json_encode(array('ok' => false, 'error' => 'Too large.'));
			return;
		}
		$this->load->database();
		$this->load->model(array('ci_admin_model', 'suite_link_model'));
		$h = function ($k) { return isset($_SERVER[$k]) ? (string)$_SERVER[$k] : ''; };
		list($code, $msg) = $this->suite_link_model->handle($raw, $h('HTTP_X_SUITE_TIMESTAMP'), $h('HTTP_X_SUITE_SIGNATURE'), $h('HTTP_X_SUITE_DELIVERY'));
		http_response_code($code);
		echo json_encode($code === 200 ? array('ok' => true, 'message' => $msg) : array('ok' => false, 'error' => $msg));
	}
}
