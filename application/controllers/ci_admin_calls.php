<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ci_admin_calls extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('session');
		$this->load->library('threecx');
		$this->load->model('ci_admin_model');
		$this->load->model('comman_model');
		$this->load->helper(array('form', 'url', 'text', 'date'));
		$this->if_not_login();
	}

	public function if_not_login()
	{
		if ($this->session->userdata('manager_id') == '') {
			redirect('/manager/login');
		}
	}

	private function _apply_lead_filters($status_filter = 'all')
	{
		$manager_id = (int)$this->session->userdata('manager_id');
		$role = (int)$this->session->userdata('manager_role');

		if ($role != 1 && $role != 4) {
			$this->db->where('A.lead_assign', $manager_id);
		} else {
			$assigned_row = $this->db->query("SELECT COUNT(*) AS total FROM ci_leads WHERE lead_assign = " . (int)$manager_id . " AND (contact1 != '' OR alt_contact != '' OR mobile != '' OR contact2 != '')")->row_array();
			$assigned_cnt = !empty($assigned_row['total']) ? (int)$assigned_row['total'] : 0;
			if ($assigned_cnt > 0) {
				$this->db->where('A.lead_assign', $manager_id);
			}
		}

		$this->db->where('(A.contact1 != "" OR A.alt_contact != "" OR A.mobile != "" OR A.contact2 != "")');

		$this->db->where_not_in('A.status', array(43, 44));
		$this->db->where('(A.remark IS NULL OR LOWER(A.remark) NOT LIKE "%lost%")');
		$this->db->where('(A.status_category IS NULL OR LOWER(A.status_category) NOT LIKE "%lost%")');

		$this->db->where_not_in('A.status', array(39, 42));
		$this->db->where('(A.company_status IS NULL OR LOWER(A.company_status) != "closed")');
		$this->db->where('(A.status_category IS NULL OR (LOWER(A.status_category) NOT LIKE "%won%" AND LOWER(A.status_category) NOT LIKE "%closed%"))');

		$this->db->where('(A.dnd_status IS NULL OR (LOWER(A.dnd_status) != "dncr" AND LOWER(A.dnd_status) != "dnd" AND LOWER(A.dnd_status) != "yes" AND LOWER(A.dnd_status) != "y"))');
		$this->db->where('(A.remark IS NULL OR (LOWER(A.remark) NOT LIKE "%dncr%" AND LOWER(A.remark) NOT LIKE "%dnd%"))');

		switch ($status_filter) {
			case 'follow_up':
				$this->db->where('(A.status = 38 OR LOWER(A.remark) LIKE "%follow up%" OR LOWER(A.remark) LIKE "%callback%" OR LOWER(A.status_category) LIKE "%follow up%")');
				break;
			case 'prospective':
				$this->db->where('(A.status = 37 OR LOWER(A.remark) LIKE "%prospective%" OR LOWER(A.status_category) LIKE "%prospective%")');
				break;
			case 'in_process':
				$this->db->where('A.status', 38);
				break;
			case 'database':
				$this->db->where('A.status', 36);
				break;
			case 'expected':
				$this->db->where('A.status', 37);
				break;
			case 'lead':
				$this->db->where('(A.status = 36 OR LOWER(A.remark) LIKE "%lead%" OR LOWER(A.remark) LIKE "%proposal%" OR LOWER(A.remark) LIKE "%interested%")');
				break;
			case 'submitted':
				$this->db->where('A.status', 40);
				break;
			case 'pending':
				$this->db->where('A.status', 41);
				break;
			default:
				break;
		}
	}

	private function _extract_lead_phones($lead)
	{
		$raw_phones = array();
		if (!empty($lead['contact1'])) {
			$raw_phones[] = trim($lead['contact1']);
		}
		if (!empty($lead['alt_contact'])) {
			$raw_phones[] = trim($lead['alt_contact']);
		}
		if (!empty($lead['contact2'])) {
			$raw_phones[] = trim($lead['contact2']);
		}
		if (!empty($lead['mobile'])) {
			$raw_phones[] = trim($lead['mobile']);
		}

		$unique_phones = array();
		foreach ($raw_phones as $p) {
			$p_clean = preg_replace('/[^\d\+]/', '', $p);
			if (!empty($p_clean) && !in_array($p, $unique_phones)) {
				$unique_phones[] = $p;
			}
		}

		$phone1 = isset($unique_phones[0]) ? $unique_phones[0] : '';
		$phone2 = isset($unique_phones[1]) ? $unique_phones[1] : '';

		return array(
			'primary' => $phone1,
			'alt' => $phone2,
			'has_two' => (!empty($phone1) && !empty($phone2))
		);
	}

	public function index()
	{
		$manager_id = (int)$this->session->userdata('manager_id');
		$role = (int)$this->session->userdata('manager_role');
		$selected_status = trim($this->input->get('status'));
		if (empty($selected_status)) {
			$selected_status = 'all';
		}

		$this->db->select('A.id, A.lead_id, A.customer, A.poc, A.poc2, A.contact1, A.alt_contact, A.contact2, A.mobile, A.email, A.category, A.status, A.tl_number, A.tl_authority, A.tl_emirate, A.tl_zone, A.r_amount, A.mrr, A.followup_date, A.created, A.remark, S.status as status_name');
		$this->db->from('ci_leads as A');
		$this->db->join('ci_status as S', 'S.id = A.status', 'left');
		$this->_apply_lead_filters($selected_status);
		$this->db->order_by('A.id', 'DESC');
		$this->db->limit(100);
		$queue = $this->db->get()->result_array();

		foreach ($queue as &$q_item) {
			$extracted = $this->_extract_lead_phones($q_item);
			$q_item['phone_primary'] = $extracted['primary'];
			$q_item['phone_alt'] = $extracted['alt'];
			$q_item['has_two_numbers'] = $extracted['has_two'];
		}
		unset($q_item);

		$this->db->from('ci_leads as A');
		$this->_apply_lead_filters('all');
		$total_my_leads = $this->db->count_all_results();

		$today_start = date('Y-m-d 00:00:00');
		$today_end = date('Y-m-d 23:59:59');
		$this->db->from('ci_call_logs');
		$this->db->where('user_id', $manager_id);
		$this->db->where('created_at >=', $today_start);
		$this->db->where('created_at <=', $today_end);
		$calls_today = $this->db->count_all_results();

		$this->db->from('ci_call_logs');
		$this->db->where('user_id', $manager_id);
		$this->db->where('duration >', 0);
		$calls_connected = $this->db->count_all_results();

		$this->db->select('L.*, C.customer as lead_customer, C.lead_id as lead_code');
		$this->db->from('ci_call_logs as L');
		$this->db->join('ci_leads as C', 'C.id = L.lead_id', 'left');
		$this->db->where('L.user_id', $manager_id);
		$this->db->order_by('L.id', 'DESC');
		$this->db->limit(40);
		$recent_logs = $this->db->get()->result_array();

		$allowed_statuses = $this->db->where_not_in('id', array(39, 42, 44))
			->order_by('id', 'ASC')
			->get('ci_status')
			->result_array();

		$active_lead = !empty($queue) ? $queue[0] : null;

		$data = array();
		$data['info'] = array('title' => 'Calls & 3CX Dialer', 'page_heading' => 'Calling Center');
		$data['queue'] = $queue;
		$data['total_queue'] = count($queue);
		$data['total_my_leads'] = $total_my_leads;
		$data['calls_today'] = $calls_today;
		$data['calls_connected'] = $calls_connected;
		$data['active_lead'] = $active_lead;
		$data['selected_status'] = $selected_status;
		$data['statuses'] = $allowed_statuses;
		$data['recent_logs'] = $recent_logs;
		$data['manager_role'] = $role;
		$data['manager_id'] = $manager_id;
		$data['manager_name'] = $this->session->userdata('manager_name');
		$data['threecx_config'] = $this->threecx->get_config();

		$this->load->view('manager/calls/dashboard', $data);
	}

	public function ajax_get_queue()
	{
		$status_filter = trim($this->input->get('status'));
		if (empty($status_filter)) {
			$status_filter = 'all';
		}
		$search = trim($this->input->get('search'));

		$this->db->select('A.id, A.lead_id, A.customer, A.poc, A.poc2, A.contact1, A.alt_contact, A.contact2, A.mobile, A.email, A.status, A.remark, S.status as status_name');
		$this->db->from('ci_leads as A');
		$this->db->join('ci_status as S', 'S.id = A.status', 'left');
		$this->_apply_lead_filters($status_filter);

		if (!empty($search)) {
			$this->db->where("(A.customer LIKE '%$search%' OR A.poc LIKE '%$search%' OR A.contact1 LIKE '%$search%' OR A.alt_contact LIKE '%$search%' OR A.contact2 LIKE '%$search%' OR A.mobile LIKE '%$search%' OR A.lead_id LIKE '%$search%')");
		}

		$this->db->order_by('A.id', 'DESC');
		$this->db->limit(120);
		$leads = $this->db->get()->result_array();

		foreach ($leads as &$l_item) {
			$extracted = $this->_extract_lead_phones($l_item);
			$l_item['phone_primary'] = $extracted['primary'];
			$l_item['phone_alt'] = $extracted['alt'];
			$l_item['has_two_numbers'] = $extracted['has_two'];
		}
		unset($l_item);

		echo json_encode(array(
			'status' => true,
			'count' => count($leads),
			'leads' => $leads
		));
	}

	public function ajax_get_lead($lead_id = 0)
	{
		$lead_id = (int)$lead_id;
		$manager_id = (int)$this->session->userdata('manager_id');
		$role = (int)$this->session->userdata('manager_role');

		if ($lead_id <= 0) {
			echo json_encode(array('status' => false, 'message' => 'Invalid Lead ID'));
			return;
		}

		$this->db->select('A.*, S.status as status_name');
		$this->db->from('ci_leads as A');
		$this->db->join('ci_status as S', 'S.id = A.status', 'left');
		$this->db->where('A.id', $lead_id);

		if ($role != 1 && $role != 4) {
			$this->db->where('A.lead_assign', $manager_id);
		} else {
			$assigned_row = $this->db->query("SELECT COUNT(*) AS total FROM ci_leads WHERE lead_assign = " . (int)$manager_id . " AND (contact1 != '' OR alt_contact != '' OR mobile != '' OR contact2 != '')")->row_array();
			$assigned_cnt = !empty($assigned_row['total']) ? (int)$assigned_row['total'] : 0;
			if ($assigned_cnt > 0) {
				$this->db->where('A.lead_assign', $manager_id);
			}
		}

		$this->db->where_not_in('A.status', array(39, 42, 43, 44));
		$this->db->where('(A.company_status IS NULL OR LOWER(A.company_status) != "closed")');
		$lead = $this->db->get()->row_array();

		if (empty($lead)) {
			echo json_encode(array('status' => false, 'message' => 'Lead not found in your allotted database or lead is inactive.'));
			return;
		}

		$this->db->select('F.*, U.name as agent_name');
		$this->db->from('ci_followup as F');
		$this->db->join('ci_user as U', 'U.id = F.user_id', 'left');
		$this->db->where('F.lead_id', $lead_id);
		$this->db->order_by('F.id', 'DESC');
		$this->db->limit(10);
		$history = $this->db->get()->result_array();

		$extracted = $this->_extract_lead_phones($lead);
		$phone1 = $extracted['primary'];
		$phone2 = $extracted['alt'];

		$dispatch = $this->threecx->build_call_dispatch(!empty($phone1) ? $phone1 : $phone2);

		$lead['phone_primary'] = $phone1;
		$lead['phone_alt'] = $phone2;
		$lead['has_two_numbers'] = $extracted['has_two'];
		$lead['history'] = $history;
		$lead['dispatch'] = $dispatch;

		echo json_encode(array('status' => true, 'lead' => $lead));
	}

	public function ajax_initiate_call()
	{
		$manager_id = (int)$this->session->userdata('manager_id');
		$phone = trim($this->input->post('phone'));
		$lead_id = (int)$this->input->post('lead_id');
		$customer_name = trim($this->input->post('customer_name'));

		if (empty($phone)) {
			echo json_encode(array('status' => false, 'message' => 'Phone number is required to place a call'));
			return;
		}

		if ($lead_id > 0 && empty($customer_name)) {
			$lead_row = $this->db->select('customer')->where('id', $lead_id)->get('ci_leads')->row_array();
			if (!empty($lead_row)) {
				$customer_name = $lead_row['customer'];
			}
		}

		if (empty($customer_name)) {
			$customer_name = 'Direct Call (' . $phone . ')';
		}

		$log_data = array(
			'user_id' => $manager_id,
			'lead_id' => ($lead_id > 0) ? $lead_id : null,
			'customer_name' => $customer_name,
			'phone_number' => $phone,
			'call_type' => 'Outbound 3CX',
			'call_status' => 'Initiated',
			'duration' => 0,
			'disposition' => 'Dialing',
			'notes' => '',
			'created_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('ci_call_logs', $log_data);
		$call_log_id = $this->db->insert_id();

		$dispatch = $this->threecx->build_call_dispatch($phone);

		echo json_encode(array(
			'status' => true,
			'call_log_id' => $call_log_id,
			'phone' => $phone,
			'customer_name' => $customer_name,
			'dispatch' => $dispatch,
			'message' => 'Call initiated via 3CX'
		));
	}

	public function ajax_save_call()
	{
		$manager_id = (int)$this->session->userdata('manager_id');
		$call_log_id = (int)$this->input->post('call_log_id');
		$lead_id = (int)$this->input->post('lead_id');
		$customer_name = trim($this->input->post('customer_name'));
		$phone = trim($this->input->post('phone'));

		$status_id = (int)$this->input->post('status');
		$outcome = trim($this->input->post('call_disposition'));
		if (empty($outcome)) {
			$outcome = trim($this->input->post('outcome'));
		}

		if ($status_id > 0) {
			$st_obj = $this->db->select('status')->where('id', $status_id)->get('ci_status')->row_array();
			if (!empty($st_obj['status'])) {
				$outcome = $st_obj['status'];
			}
		} elseif (!empty($outcome)) {
			$st_obj = $this->db->select('id')->where('status', $outcome)->get('ci_status')->row_array();
			if (!empty($st_obj['id'])) {
				$status_id = (int)$st_obj['id'];
			}
		}

		if (empty($outcome)) {
			$outcome = 'Connected';
		}

		$remarks = trim($this->input->post('remarks'));
		$next_date = trim($this->input->post('next_followup'));
		$next_time = trim($this->input->post('next_followup_time'));
		$duration = (int)$this->input->post('call_duration');

		if ($call_log_id > 0) {
			$this->db->where('id', $call_log_id);
			$this->db->update('ci_call_logs', array(
				'call_status' => 'Completed',
				'duration' => $duration,
				'disposition' => $outcome,
				'notes' => $remarks
			));
		} else {
			$this->db->insert('ci_call_logs', array(
				'user_id' => $manager_id,
				'lead_id' => ($lead_id > 0) ? $lead_id : null,
				'customer_name' => !empty($customer_name) ? $customer_name : 'Direct Call',
				'phone_number' => !empty($phone) ? $phone : 'Unknown',
				'call_type' => 'Outbound 3CX',
				'call_status' => 'Completed',
				'duration' => $duration,
				'disposition' => $outcome,
				'notes' => $remarks,
				'created_at' => date('Y-m-d H:i:s')
			));
		}

		if ($lead_id > 0) {
			$remark_full = "[3CX Call: $outcome" . ($duration > 0 ? " | {$duration}s" : "") . "] " . $remarks;

			$followup_data = array(
				'lead_id' => $lead_id,
				'lead_type' => 'lead',
				'user_id' => $manager_id,
				'followup_date' => !empty($next_date) ? db_date($next_date) : date('Y-m-d'),
				'followup_time' => !empty($next_time) ? $next_time : date('H:i:s'),
				'followup_remark' => $remark_full,
				'followup_result' => $outcome
			);
			$this->db->insert('ci_followup', $followup_data);

			$lead_update = array(
				'remark' => $remark_full,
				'edited_by' => $manager_id,
				'modified' => date('Y-m-d')
			);
			if (!empty($next_date)) {
				$lead_update['followup_date'] = db_date($next_date);
			}
			if (!empty($next_time)) {
				$lead_update['followup_time'] = $next_time;
			}
			if ($status_id > 0 && !in_array($status_id, array(39, 42, 44))) {
				$lead_update['status'] = $status_id;
			}
			$lead_update['call_status'] = $outcome;
			$this->db->where('id', $lead_id);
			$this->db->update('ci_leads', $lead_update);

			$this->ci_admin_model->log_admin_action(
				'call_remark_save',
				"Logged 3CX Call with disposition '$outcome' for Lead ID #$lead_id",
				'SUCCESS',
				$manager_id,
				$this->session->userdata('manager_name')
			);
		}

		echo json_encode(array(
			'status' => true,
			'message' => 'Call log and remarks saved successfully',
			'outcome' => $outcome,
			'lead_id' => $lead_id,
			'duration' => $duration
		));
	}

	public function ajax_get_call_logs()
	{
		$manager_id = (int)$this->session->userdata('manager_id');
		$this->db->select('L.*, C.customer as lead_customer, C.lead_id as lead_code');
		$this->db->from('ci_call_logs as L');
		$this->db->join('ci_leads as C', 'C.id = L.lead_id', 'left');
		$this->db->where('L.user_id', $manager_id);
		$this->db->order_by('L.id', 'DESC');
		$this->db->limit(60);
		$logs = $this->db->get()->result_array();

		echo json_encode(array('status' => true, 'logs' => $logs));
	}

	public function ajax_test_3cx()
	{
		$fqdn = trim($this->input->post('pbx_fqdn'));
		$client_id = trim($this->input->post('client_id'));
		$client_secret = trim($this->input->post('client_secret'));

		$result = $this->threecx->test_connection($fqdn, $client_id, $client_secret);
		echo json_encode($result);
	}

	public function ajax_save_3cx_config()
	{
		$data = array(
			'pbx_fqdn' => trim($this->input->post('pbx_fqdn')),
			'client_id' => trim($this->input->post('client_id')),
			'client_secret' => trim($this->input->post('client_secret')),
			'default_extension' => trim($this->input->post('default_extension')),
			'multiple_lines' => trim($this->input->post('multiple_lines')),
			'auto_open_webclient' => $this->input->post('auto_open_webclient') ? 1 : 0
		);

		$result = $this->threecx->save_config($data);
		echo json_encode($result);
	}

	public function calling_api()
	{
		$role = (int)$this->session->userdata('manager_role');
		if ($role != 1 && $role != 4) {
			redirect('manager/dashboard');
			return;
		}

		$data = array();
		$data['info'] = array('title' => 'System - Calling API Settings', 'page_heading' => 'Calling API Integration');
		$data['threecx_config'] = $this->threecx->get_config();
		$data['manager_role'] = $role;
		$data['manager_name'] = $this->session->userdata('manager_name');

		$this->load->view('manager/system/calling_api', $data);
	}
}

