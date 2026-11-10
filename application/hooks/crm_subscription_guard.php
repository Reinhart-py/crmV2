<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Sales CRM subscription guard (post_controller_constructor hook), on every page and background call.
 *
 * Only works when the check is switched on (System > Integrations > Third-Party API > Subscription).
 * - The portal says the subscription has stopped (payment failed, trial ended, cancelled): everyone is
 *   signed out on their next click or refresh and sent to the portal's renew page.
 * - The portal can't be asked properly (wrong app secret, down for more than 24 hours): staff are
 *   signed out, admins stay in so they can fix the settings.
 * Background (AJAX) calls get 401 {code: "SUBSCRIPTION_INACTIVE", error, redirect}; the page script then opens redirect.
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

	$paused = !empty($st['portal_refused']);
	if (!$paused) {
		require_once APPPATH . 'helpers/crm_permission_helper.php';
		if (crm_is_full_admin()) return;
	}

	$uid = (int)$ci->session->userdata('manager_id');
	$ci->db->where('id', $uid)->update('ci_user', array('is_online' => 0, 'current_session_token' => null));
	$ci->session->sess_destroy();

	$renew = $paused ? $ci->subscription_model->renew_url($st) : '';
	$msg = $paused
		? 'Access paused: payment issue with the Sales CRM subscription. Please renew it, or check with your administrator.'
		: 'The Sales CRM subscription is not active' . (!empty($st['reason']) ? ' (' . rtrim($st['reason'], '. ') . ')' : '') . '. Please ask your administrator to renew it.';
	$to = $renew !== '' ? $renew : base_url() . 'manager/login';

	if ($ci->input->is_ajax_request()) {
		if (!headers_sent()) { http_response_code(401); header('Content-Type: application/json; charset=utf-8'); }
		echo json_encode(array('code' => 'SUBSCRIPTION_INACTIVE', 'error' => $msg, 'redirect' => $to, 'status' => 'error', 'message' => $msg));
		exit;
	}
	if ($renew === '') {
		$ci->session->sess_create();
		$ci->session->set_flashdata('error_message', $msg);
	}
	redirect($to);
	exit;
}
