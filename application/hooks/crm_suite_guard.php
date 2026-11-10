<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Closed accounts (post_controller_constructor hook): someone whose login is disabled or who is marked
 * as left (staff profile or HR link) is signed out on their next click, refresh or background call.
 * Enabling the login, or Rejoin on the staff profile, lets them back in.
 */
function crm_suite_guard()
{
	$ci =& get_instance();
	if (!isset($ci->uri) || $ci->uri->segment(1) !== 'manager') return;
	if (!isset($ci->session)) return;
	$uid = (int)$ci->session->userdata('manager_id');
	if ($uid <= 0) return;
	if (in_array((string)$ci->uri->segment(2), array('login', 'logout', 'forgot', 'reset'), true)) return;
	require_once APPPATH . 'helpers/crm_permission_helper.php';
	$msg = crm_account_closed_msg($uid);
	if ($msg === '') return;

	$ci->db->where('id', $uid)->update('ci_user', array('is_online' => 0, 'current_session_token' => null));
	$ci->session->sess_destroy();
	if ($ci->input->is_ajax_request()) {
		if (!headers_sent()) { http_response_code(401); header('Content-Type: application/json; charset=utf-8'); }
		echo json_encode(array('code' => 'SUBSCRIPTION_INACTIVE', 'error' => $msg, 'redirect' => base_url() . 'manager/login', 'status' => 'error', 'message' => $msg));
		exit;
	}
	$ci->session->sess_create();
	$ci->session->set_flashdata('error_message', $msg);
	redirect('manager/login');
	exit;
}
