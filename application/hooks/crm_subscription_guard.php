<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Sales CRM subscription guard (post_controller_constructor hook).
 *
 * Only works when the check is switched on (System > Integrations > Third-Party API > Subscription).
 * If the subscription portal says the subscription is not active, staff who are signed in are signed out
 * with a message; full admins stay in (they see a notice and can renew). The portal's answer is kept
 * for an hour, so this costs one settings read per page.
 */
function crm_subscription_guard()
{
	$ci =& get_instance();
	if (!isset($ci->uri) || $ci->uri->segment(1) !== 'manager') return;
	if (!isset($ci->session) || (int)$ci->session->userdata('manager_id') <= 0) return;
	$seg2 = (string)$ci->uri->segment(2);
	if (in_array($seg2, array('login', 'logout', 'forgot', 'reset'), true)) return;
	if (!isset($ci->db)) $ci->load->database();

	$ci->load->model('subscription_model');
	$st = $ci->subscription_model->state();
	if (!empty($st['allowed'])) return;

	require_once APPPATH . 'helpers/crm_permission_helper.php';
	if (crm_is_full_admin()) return;

	$uid = (int)$ci->session->userdata('manager_id');
	$ci->db->where('id', $uid)->update('ci_user', array('is_online' => 0, 'current_session_token' => null));
	$ci->session->sess_destroy();
	$msg = 'The Sales CRM subscription is not active' . (!empty($st['reason']) ? ' (' . rtrim($st['reason'], '. ') . ')' : '') . '. Please ask your administrator to renew it.';
	if ($ci->input->is_ajax_request()) {
		if (!headers_sent()) { http_response_code(401); header('Content-Type: application/json; charset=utf-8'); }
		echo json_encode(array('status' => 'error', 'message' => $msg));
		exit;
	}
	$ci->session->sess_create();
	$ci->session->set_flashdata('error_message', $msg);
	redirect('manager/login');
	exit;
}
