<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Page guard (post_controller_constructor hook).
 *
 * Every manager page listed in the permission catalogue
 * (application/helpers/crm_permission_helper.php) needs its right: View for
 * the page, Full for add/create pages. Opening one without the right, by link
 * or by typing the address, shows a message and goes to the first page the
 * person may open. Full admins (Admin role, user #1, "Full admin" mode) pass.
 *
 * AJAX calls get the same check (the X-Requested-With header is set by the browser script and
 * can be added by anyone, so it must not open a way around the rights); a refused AJAX call gets
 * HTTP 403 with a short JSON message instead of a redirect.
 */
function crm_permission_guard()
{
	$ci =& get_instance();
	if (!isset($ci->uri) || $ci->uri->segment(1) !== 'manager') return;
	if (!isset($ci->session) || (int)$ci->session->userdata('manager_id') <= 0) return;
	require_once APPPATH . 'helpers/crm_permission_helper.php';
	crm_sync_session_user();
	$is_ajax = $ci->input->is_ajax_request();

	$segs = $ci->uri->segment_array();
	array_shift($segs);
	$path = implode('/', $segs);
	if ($path === '') return;

	require_once APPPATH . 'helpers/crm_permission_helper.php';
	$rule = crm_page_rule($path, $_GET);
	if ($rule === null) return;
	if (!isset($ci->db)) $ci->load->database();
	if (crm_can($rule[0], $rule[1])) return;

	$items = crm_perm_items();
	$label = isset($items[$rule[0]]['label']) ? $items[$rule[0]]['label'] : $rule[0];
	$msg = 'You do not have ' . ($rule[1] === 'write' ? 'Full' : 'View') . ' access to ' . $label . '. Ask your administrator if you need it.';
	if ($is_ajax) {
		if (!headers_sent()) { http_response_code(403); header('Content-Type: application/json; charset=utf-8'); }
		echo json_encode(array('status' => 'error', 'message' => $msg));
		exit;
	}
	$ci->session->set_flashdata('error_message', $msg);
	$to = crm_landing_url();
	if (trim($to, '/') === 'manager/' . $path) $to = 'manager/profile';
	redirect($to);
	exit;
}
