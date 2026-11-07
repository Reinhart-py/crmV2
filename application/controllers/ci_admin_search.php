<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Header search: one box that finds Database / Lead records, Orders and Tickets by
 * company, contact name, phone, e-mail or reference number.
 * Every section returns only what the person could open from its own page
 * (same rules as leads/view, orders/view and tickets/view).
 */
class Ci_admin_search extends CI_Controller
{
	const PER_GROUP = 6;

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('url'));
		$this->load->model(array('ci_admin_model'));
	}

	public function index()
	{
		header('Content-Type: application/json; charset=utf-8');
		$uid = (int)$this->session->userdata('manager_id');
		if ($uid <= 0) { http_response_code(401); echo json_encode(array('status' => 'error', 'message' => 'Please sign in again.')); return; }
		if (crm_is_presentation_user()) { echo json_encode(array('status' => 'ok', 'groups' => array())); return; }

		$q = trim((string)$this->input->get('q'));
		if (function_exists('mb_substr')) $q = mb_substr($q, 0, 80);
		if (strlen($q) < 2) { echo json_encode(array('status' => 'ok', 'groups' => array())); return; }

		$digits = preg_replace('/\D/', '', $q);
		$phone = (strlen($digits) >= 4 && strlen($digits) >= strlen(preg_replace('/[\s+\-()]/', '', $q)) - 1) ? substr($digits, -9) : '';
		$like = $this->db->escape_like_str($q);

		$groups = array();
		$g = $this->_records($uid, $like, $phone);
		if ($g) $groups[] = $g;
		$g = $this->_orders($uid, $like, $phone);
		if ($g) $groups[] = $g;
		$g = $this->_tickets($uid, $like, $phone);
		if ($g) $groups[] = $g;

		echo json_encode(array('status' => 'ok', 'q' => $q, 'groups' => $groups));
	}

	/** Text match on several columns, plus a digits-only match on phone columns. */
	private function _match($cols, $like, $phone, $phone_cols)
	{
		$or = array();
		foreach ($cols as $c) $or[] = $c . " LIKE '%" . $like . "%'";
		if ($phone !== '') foreach ($phone_cols as $c) $or[] = "REGEXP_REPLACE(IFNULL(" . $c . ",''), '[^0-9]', '') LIKE '%" . $phone . "%'";
		return '(' . implode(' OR ', $or) . ')';
	}

	private function _team_ids($uid)
	{
		$t = $this->ci_admin_model->fetchTeamList($uid, '');
		return array_values(array_unique(array_merge(array((int)$uid), array_map('intval', is_array($t) ? $t : array()))));
	}

	private function _records($uid, $like, $phone)
	{
		if (!crm_can('database') && !crm_can('leads') && !crm_can('all_data')) return null;
		$role = (int)$this->session->userdata('manager_role');
		$see_all = ($role === 1 || $uid === 1 || crm_can('all_data') || has_section_perm('back_office') || has_section_perm('lead_review'));
		$where = $this->_match(array('A.customer', 'A.poc', 'A.email', 'A.lead_id'), $like, $phone, array('A.contact1', 'A.contact2', 'A.alt_contact', 'A.mobile'));
		if (!$see_all) {
			$ids = implode(',', $this->_team_ids($uid));
			$where .= " AND (IF(A.lead_assign > 0, A.lead_assign, A.created_by) IN (" . $ids . ") OR FIND_IN_SET(" . $uid . ", IFNULL(A.agents,'')) > 0)";
		}
		$rows = $this->db->query("SELECT A.id, A.lead_id, A.customer, A.poc, A.contact1, A.email, P.pipeline_stage, S.status AS status_name, U.name AS owner
			FROM ci_leads A LEFT JOIN ci_pipeline P ON P.lead_id = A.id LEFT JOIN ci_status S ON S.id = A.status
			LEFT JOIN ci_user U ON U.id = IF(A.lead_assign > 0, A.lead_assign, A.created_by)
			WHERE " . $where . " ORDER BY A.id DESC LIMIT " . (self::PER_GROUP + 1))->result_array();
		if (!$rows) return null;
		$items = array();
		foreach (array_slice($rows, 0, self::PER_GROUP) as $r) {
			$stage = ($r['pipeline_stage'] === 'lead') ? 'Lead' : (($r['pipeline_stage'] && $r['pipeline_stage'] !== 'database') ? ucfirst($r['pipeline_stage']) : 'Database');
			$sub = array_filter(array($r['poc'], $r['contact1'], $r['email']));
			$items[] = array(
				'title' => $r['customer'] !== '' && $r['customer'] !== null ? $r['customer'] : ('Record ' . $r['lead_id']),
				'sub' => implode(' · ', $sub),
				'tag' => $stage . ($r['status_name'] && strcasecmp($r['status_name'], $stage) !== 0 ? ' · ' . $r['status_name'] : ''),
				'meta' => $r['owner'] ? $r['owner'] : '',
				'url' => base_url() . 'manager/leads/view/' . base64_encode($r['id']),
			);
		}
		return array('key' => 'records', 'label' => 'Database & Leads', 'icon' => 'fa-database', 'items' => $items, 'more' => count($rows) > self::PER_GROUP);
	}

	private function _orders($uid, $like, $phone)
	{
		$role = (int)$this->session->userdata('manager_role');
		$see_all = ($role === 1 || $uid === 1 || crm_can('all_data') || has_section_perm('back_office') || has_section_perm('kam', 'write'));
		if (!$see_all && !crm_can('orders') && !crm_can('sales')) return null;
		$where = $this->_match(array('O.order_no', 'O.sales_ref_no', 'O.company_name', 'O.client_name', 'O.db_ref_no', 'O.lead_ref_no', 'A.customer', 'A.email'), $like, $phone, array('A.contact1', 'A.contact2'));
		if (!$see_all) {
			$where .= " AND (O.created_by = {$uid} OR O.assigned_to = {$uid} OR A.created_by = {$uid} OR A.lead_assign = {$uid} OR FIND_IN_SET({$uid}, IFNULL(A.agents,'')) > 0)";
		}
		$rows = $this->db->query("SELECT O.id, O.order_no, O.company_name, O.client_name, O.service_title, O.status, A.customer
			FROM ci_orders O LEFT JOIN ci_leads A ON A.id = O.lead_id
			WHERE " . $where . " ORDER BY O.id DESC LIMIT " . (self::PER_GROUP + 1))->result_array();
		if (!$rows) return null;
		$items = array();
		foreach (array_slice($rows, 0, self::PER_GROUP) as $r) {
			$name = $r['company_name'] ? $r['company_name'] : ($r['customer'] ? $r['customer'] : $r['client_name']);
			$items[] = array(
				'title' => trim(($r['order_no'] ? $r['order_no'] . ' · ' : '') . $name, ' ·'),
				'sub' => (string)$r['service_title'],
				'tag' => (string)$r['status'],
				'meta' => '',
				'url' => base_url() . 'manager/orders/view/' . (int)$r['id'],
			);
		}
		return array('key' => 'orders', 'label' => 'Orders', 'icon' => 'fa-shopping-cart', 'items' => $items, 'more' => count($rows) > self::PER_GROUP);
	}

	private function _tickets($uid, $like, $phone)
	{
		$role = (int)$this->session->userdata('manager_role');
		$manage = ($role === 1 || (!crm_is_full_admin() && crm_can('support_desk')));
		$where = $this->_match(array('T.ticket_number', 'T.subject', 'T.ticket_title', 'T.customer', 'T.company_name', 'T.email'), $like, $phone, array('T.contact1', 'T.contact2'));
		if (!$manage) {
			$where .= " AND (T.created_by = {$uid} OR T.raised_by = {$uid} OR T.assigned_to = {$uid} OR FIND_IN_SET({$uid}, IFNULL(T.assigned_users,'')) > 0 OR FIND_IN_SET({$uid}, IFNULL(T.tagged_users,'')) > 0)";
		}
		$rows = $this->db->query("SELECT T.id, T.ticket_number, T.subject, T.ticket_title, T.company_name, T.customer, T.status
			FROM ci_tickets T WHERE " . $where . " ORDER BY T.id DESC LIMIT " . (self::PER_GROUP + 1))->result_array();
		if (!$rows) return null;
		$items = array();
		foreach (array_slice($rows, 0, self::PER_GROUP) as $r) {
			$title = $r['ticket_title'] ? $r['ticket_title'] : $r['subject'];
			$items[] = array(
				'title' => trim(($r['ticket_number'] ? $r['ticket_number'] . ' · ' : '') . $title, ' ·'),
				'sub' => (string)($r['company_name'] ? $r['company_name'] : $r['customer']),
				'tag' => (string)$r['status'],
				'meta' => '',
				'url' => base_url() . 'manager/tickets/view/' . (int)$r['id'],
			);
		}
		return array('key' => 'tickets', 'label' => 'Tickets', 'icon' => 'fa-ticket', 'items' => $items, 'more' => count($rows) > self::PER_GROUP);
	}
}
