<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ci_admin_calls extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('session');
		$this->load->library('threecx');
		$this->load->library('groq_service');
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
		if ($role != 1 && $role != 4) {
			$this->db->where('L.user_id', $manager_id);
		}
		$this->db->where_not_in('L.call_status', array('Initiated', 'Dialing'));
		$this->db->where('L.created_at <=', date('Y-m-d H:i:s'));
		$this->db->order_by('L.id', 'DESC');
		$this->db->limit(50);
		$recent_logs = $this->db->get()->result_array();

		$allowed_statuses = $this->db->where_not_in('id', array(39, 42, 44))
			->order_by('id', 'ASC')
			->get('ci_status')
			->result_array();

		$user_dept_row = $this->db->select('department')->where('id', $manager_id)->get('ci_user')->row_array();
		$manager_dept = !empty($user_dept_row['department']) ? $user_dept_row['department'] : 'Sales';
		$active_lead = !empty($queue) ? $queue[0] : null;

		$data = array();
		$data['info'] = array('title' => 'Calls & Dialer', 'page_heading' => 'Calling Center');
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
		$data['manager_department'] = $manager_dept;
		$data['threecx_config'] = $this->threecx->get_config();
		$data['is_backend_configured'] = (!empty($data['threecx_config']['pbx_fqdn']) && !empty($data['threecx_config']['client_id']));
		$data['autodialer_seconds'] = !empty($data['threecx_config']['autodialer_seconds']) ? (int)$data['threecx_config']['autodialer_seconds'] : 60;

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
			$escaped_search = $this->db->escape_str($this->db->escape_like_str($search));
			$this->db->where("(A.customer LIKE '%$escaped_search%' OR A.poc LIKE '%$escaped_search%' OR A.contact1 LIKE '%$escaped_search%' OR A.alt_contact LIKE '%$escaped_search%' OR A.contact2 LIKE '%$escaped_search%' OR A.mobile LIKE '%$escaped_search%' OR A.lead_id LIKE '%$escaped_search%')");
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
		$is_configured = !empty($dispatch['configured']);

		echo json_encode(array(
			'status' => true,
			'configured' => $is_configured,
			'call_log_id' => $call_log_id,
			'phone' => $phone,
			'customer_name' => $customer_name,
			'dispatch' => $dispatch,
			'message' => $is_configured ? 'Call initiated via Calling API backend' : 'Calling backend not configured'
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

		$lower_outcome = strtolower($outcome);
		if ($status_id <= 0 && (strpos($lower_outcome, 'miss') !== false || strpos($lower_outcome, 'no answer') !== false || strpos($lower_outcome, 'busy') !== false || strpos($lower_outcome, 'abandon') !== false)) {
			$status_id = 38;
		}

		$vat_status = trim($this->input->post('vat_status'));
		$corporate_tax_status = trim($this->input->post('corporate_tax_status'));
		$vat_trn = trim($this->input->post('vat_trn'));
		$ct_trn = trim($this->input->post('ct_trn'));
		$business_category = trim($this->input->post('business_category'));
		if (empty($business_category)) {
			$business_category = trim($this->input->post('general_business_cat'));
		}
		$industry_category = trim($this->input->post('industry_category'));

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
				'call_type' => 'Outbound Call',
				'call_status' => 'Completed',
				'duration' => $duration,
				'disposition' => $outcome,
				'notes' => $remarks,
				'created_at' => date('Y-m-d H:i:s')
			));
		}

		if ($lead_id > 0) {
			$extra_tags = array();
			if (!empty($vat_status)) {
				$extra_tags[] = "VAT: " . $vat_status . (!empty($vat_trn) ? " (TRN: $vat_trn)" : "");
			}
			if (!empty($corporate_tax_status)) {
				$extra_tags[] = "CT: " . $corporate_tax_status . (!empty($ct_trn) ? " (TRN: $ct_trn)" : "");
			}
			$tag_str = !empty($extra_tags) ? " [" . implode(" | ", $extra_tags) . "]" : "";

			$remark_full = "[Call: $outcome" . ($duration > 0 ? " | {$duration}s" : "") . "]$tag_str " . $remarks;

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

			if (!empty($vat_status)) {
				$lead_update['vat_status'] = $vat_status;
			}
			if (!empty($corporate_tax_status)) {
				$lead_update['corporate_tax_status'] = $corporate_tax_status;
			}
			if (!empty($business_category)) {
				$lead_update['business_category'] = $business_category;
			}
			if (!empty($industry_category)) {
				$lead_update['industry_category'] = $industry_category;
			}
			if (!empty($vat_trn)) {
				$lead_update['vat_trn'] = $vat_trn;
			}
			if (!empty($ct_trn)) {
				$lead_update['ct_trn'] = $ct_trn;
			}

			$this->db->where('id', $lead_id);
			$this->db->update('ci_leads', $lead_update);

			$this->ci_admin_model->log_admin_action(
				'call_remark_save',
				"Logged Call with disposition '$outcome' for Lead ID #$lead_id",
				'SUCCESS',
				$manager_id,
				$this->session->userdata('manager_name')
			);
		}

		$this->db->select('A.id, A.lead_id, A.customer, A.poc, A.poc2, A.contact1, A.alt_contact, A.contact2, A.mobile, A.email, A.category, A.status, A.p_cat, A.tl_number, A.tl_authority, A.tl_emirate, A.tl_zone, A.r_amount, A.mrr, A.followup_date, A.created, A.remark, S.status as status_name');
		$this->db->from('ci_leads as A');
		$this->db->join('ci_status as S', 'S.id = A.status', 'left');
		$this->_apply_lead_filters('all');
		if ($lead_id > 0) {
			$this->db->where('A.id !=', $lead_id);
		}
		$this->db->order_by('A.id', 'DESC');
		$this->db->limit(1);
		$next_lead = $this->db->get()->row_array();
		if (!empty($next_lead)) {
			$extracted = $this->_extract_lead_phones($next_lead);
			$next_lead['phone_primary'] = $extracted['primary'];
			$next_lead['phone_alt'] = $extracted['alt'];
			$next_lead['has_two_numbers'] = $extracted['has_two'];
		}

		echo json_encode(array(
			'status' => true,
			'message' => 'Call log and remarks saved successfully',
			'outcome' => $outcome,
			'lead_id' => $lead_id,
			'duration' => $duration,
			'next_lead' => $next_lead
		));
	}

	public function ajax_get_call_logs()
	{
		$manager_id = (int)$this->session->userdata('manager_id');
		$role = (int)$this->session->userdata('manager_role');
		$this->db->select('L.*, C.customer as lead_customer, C.lead_id as lead_code');
		$this->db->from('ci_call_logs as L');
		$this->db->join('ci_leads as C', 'C.id = L.lead_id', 'left');
		if ($role != 1 && $role != 4) {
			$this->db->where('L.user_id', $manager_id);
		}
		$this->db->where_not_in('L.call_status', array('Initiated', 'Dialing', 'calling', 'ringing'));
		$this->db->where('L.created_at <=', date('Y-m-d H:i:s'));
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
			'auto_open_webclient' => $this->input->post('auto_open_webclient') ? 1 : 0,
			'autodialer_seconds' => (int)$this->input->post('autodialer_seconds')
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

	public function cdr_webhook()
	{
		$raw_data = file_get_contents('php://input');
		$payload = json_decode($raw_data, true);
		if (empty($payload)) {
			$payload = $_POST;
		}

		$caller = isset($payload['caller']) ? trim($payload['caller']) : (isset($payload['Caller']) ? trim($payload['Caller']) : '');
		$callee = isset($payload['callee']) ? trim($payload['callee']) : (isset($payload['Callee']) ? trim($payload['Callee']) : '');
		$status = isset($payload['status']) ? trim($payload['status']) : (isset($payload['Status']) ? trim($payload['Status']) : 'Completed');
		$duration = isset($payload['duration']) ? (int)$payload['duration'] : (isset($payload['Duration']) ? (int)$payload['Duration'] : 0);
		$call_type = isset($payload['type']) ? trim($payload['type']) : 'Inbound 3CX';
		$call_id = isset($payload['call_id']) ? trim($payload['call_id']) : (isset($payload['CallId']) ? trim($payload['CallId']) : (isset($payload['callid']) ? trim($payload['callid']) : ''));
		$recording_url = isset($payload['recording_url']) ? trim($payload['recording_url']) : (isset($payload['RecordingUrl']) ? trim($payload['RecordingUrl']) : (isset($payload['recording']) ? trim($payload['recording']) : ''));

		$phone = !empty($caller) ? $caller : $callee;
		$lead = null;
		if (!empty($phone)) {
			$clean_p = preg_replace('/[^\d]/', '', $phone);
			if (strlen($clean_p) >= 7) {
				$tail = substr($clean_p, -7);
				$escaped_tail = $this->db->escape_like_str($tail);
				$lead = $this->db->query("SELECT id, customer, lead_assign FROM ci_leads WHERE contact1 LIKE '%$escaped_tail%' OR alt_contact LIKE '%$escaped_tail%' OR mobile LIKE '%$escaped_tail%' LIMIT 1")->row_array();
			}
		}

		$disposition = ($duration > 0) ? 'Answered' : 'Missed Call';
		if (stripos($status, 'busy') !== false) {
			$disposition = 'Busy';
		} elseif (stripos($status, 'no answer') !== false || stripos($status, 'unanswered') !== false) {
			$disposition = 'Unanswered';
		}

		$lead_id = !empty($lead) ? (int)$lead['id'] : null;
		$user_id = !empty($lead) ? (int)$lead['lead_assign'] : 1;
		$customer_name = !empty($lead) ? $lead['customer'] : 'Inbound Caller (' . $phone . ')';

		$log_data = array(
			'user_id' => $user_id,
			'lead_id' => $lead_id,
			'customer_name' => $customer_name,
			'phone_number' => $phone,
			'call_type' => $call_type,
			'call_status' => $status,
			'duration' => $duration,
			'disposition' => $disposition,
			'notes' => '3CX Webhook CDR: ' . $disposition,
			'call_id' => !empty($call_id) ? $call_id : null,
			'recording_url' => !empty($recording_url) ? $recording_url : null,
			'ai_processing_status' => !empty($recording_url) ? 'pending' : 'pending',
			'created_at' => date('Y-m-d H:i:s')
		);

		$this->db->insert('ci_call_logs', $log_data);
		$call_log_id = $this->db->insert_id();

		if ($lead_id && $duration == 0) {
			$this->db->where('id', $lead_id)->update('ci_leads', array(
				'call_status' => 'Missed Call - Follow Up Needed',
				'status' => 38,
				'modified' => date('Y-m-d')
			));
		}

		if (!empty($recording_url) && $call_log_id > 0) {
			$this->_run_post_call_ai($call_log_id);
		}

		echo json_encode(array('status' => true, 'message' => 'CDR processed', 'call_log_id' => $call_log_id));
	}

	public function recording_webhook()
	{
		$raw_data = file_get_contents('php://input');
		$payload = json_decode($raw_data, true);
		if (empty($payload)) {
			$payload = $_POST;
		}

		$call_id = isset($payload['call_id']) ? trim($payload['call_id']) : (isset($payload['CallId']) ? trim($payload['CallId']) : '');
		$recording_url = isset($payload['recording_url']) ? trim($payload['recording_url']) : (isset($payload['RecordingUrl']) ? trim($payload['RecordingUrl']) : '');

		if (empty($call_id) && empty($recording_url)) {
			echo json_encode(array('status' => false, 'message' => 'Missing call_id and recording_url'));
			return;
		}

		$existing = null;
		if (!empty($call_id)) {
			$existing = $this->db->where('call_id', $call_id)->order_by('id', 'DESC')->get('ci_call_logs')->row_array();
		}

		if (!empty($existing)) {
			$this->db->where('id', $existing['id'])->update('ci_call_logs', array(
				'recording_url' => $recording_url,
				'ai_processing_status' => 'pending'
			));
			$this->_run_post_call_ai($existing['id']);
			echo json_encode(array('status' => true, 'message' => 'Recording attached and AI queued', 'call_log_id' => $existing['id']));
		} else {
			echo json_encode(array('status' => false, 'message' => 'Matching call log not found for call_id'));
		}
	}

	public function ajax_process_ai()
	{
		$call_log_id = (int)$this->input->post('call_log_id');
		if ($call_log_id <= 0) {
			echo json_encode(array('status' => false, 'message' => 'Invalid call log ID'));
			return;
		}

		$res = $this->_run_post_call_ai($call_log_id);
		echo json_encode($res);
	}

	public function ajax_retry_ai()
	{
		$this->ajax_process_ai();
	}

	private function _run_post_call_ai($call_log_id)
	{
		$log = $this->db->where('id', $call_log_id)->get('ci_call_logs')->row_array();
		if (empty($log)) {
			return array('status' => false, 'message' => 'Call log record not found');
		}

		$local_file = !empty($log['recording_file']) ? FCPATH . $log['recording_file'] : '';
		if (empty($local_file) || !file_exists($local_file)) {
			if (!empty($log['recording_url'])) {
				$dl_res = $this->groq_service->download_recording($log['recording_url'], $log['call_id']);
				if ($dl_res['status']) {
					$local_file = $dl_res['local_path'];
					$this->db->where('id', $call_log_id)->update('ci_call_logs', array(
						'recording_file' => $dl_res['relative_path']
					));
				} else {
					$this->db->where('id', $call_log_id)->update('ci_call_logs', array(
						'ai_processing_status' => 'failed',
						'ai_error_message' => 'Failed to download recording: ' . $dl_res['message']
					));
					return array('status' => false, 'message' => 'Recording download failed: ' . $dl_res['message']);
				}
			} else {
				return array('status' => false, 'message' => 'No recording URL or file attached to this call log');
			}
		}

		$this->db->where('id', $call_log_id)->update('ci_call_logs', array(
			'ai_processing_status' => 'transcribing'
		));

		$trans_res = $this->groq_service->transcribe_audio($local_file);
		if (!$trans_res['status']) {
			$this->db->where('id', $call_log_id)->update('ci_call_logs', array(
				'ai_processing_status' => 'failed',
				'ai_error_message' => 'Transcription failed: ' . $trans_res['message']
			));
			return array('status' => false, 'message' => 'Transcription failed: ' . $trans_res['message']);
		}

		$transcript = $trans_res['transcript'];
		$this->db->where('id', $call_log_id)->update('ci_call_logs', array(
			'transcript' => $transcript,
			'ai_processing_status' => 'analyzing'
		));

		$ai_res = $this->groq_service->analyze_transcript($transcript);
		if (!$ai_res['status']) {
			$this->db->where('id', $call_log_id)->update('ci_call_logs', array(
				'ai_processing_status' => 'failed',
				'ai_error_message' => 'AI analysis failed: ' . $ai_res['message']
			));
			return array('status' => false, 'message' => 'AI analysis failed: ' . $ai_res['message']);
		}

		$d = $ai_res['data'];
		$now = date('Y-m-d H:i:s');

		$update_data = array(
			'ai_summary' => $d['summary'],
			'ai_remarks' => $d['remarks'],
			'ai_status' => $d['next_status'],
			'ai_sentiment' => $d['sentiment'],
			'ai_follow_up_date' => $d['follow_up_date'],
			'ai_next_action' => $d['next_action'],
			'ai_language' => $d['language'],
			'ai_requirements' => json_encode($d['customer_requirements']),
			'ai_objections' => json_encode($d['objections']),
			'ai_raw_json' => $ai_res['raw_json'],
			'ai_processing_status' => 'completed',
			'ai_error_message' => null,
			'ai_processed_at' => $now
		);

		$this->db->where('id', $call_log_id)->update('ci_call_logs', $update_data);

		if (!empty($log['lead_id'])) {
			$ai_lead_note = "[AI Generated - {$d['sentiment']}] {$d['summary']} | Next Action: {$d['next_action']}";
			$this->db->insert('ci_followup', array(
				'lead_id' => $log['lead_id'],
				'lead_type' => 'lead',
				'user_id' => $log['user_id'] ?: (int)$this->session->userdata('manager_id'),
				'followup_date' => !empty($d['follow_up_date']) ? $d['follow_up_date'] : date('Y-m-d'),
				'followup_time' => date('H:i:s'),
				'followup_remark' => $ai_lead_note,
				'followup_result' => $d['next_status']
			));
		}

		return array(
			'status' => true,
			'message' => 'Call transcript and AI conversation analysis generated successfully',
			'data' => $update_data
		);
	}

	public function ajax_start_autodial_session()
	{
		$user_id = (int)$this->session->userdata('manager_id');
		$cooldown = (int)$this->input->post('cooldown') ?: 60;
		$status_filter = trim((string)$this->input->post('status_filter')) ?: 'all';

		$sess = $this->db->where('user_id', $user_id)->get('ci_dialer_sessions')->row_array();
		$now = date('Y-m-d H:i:s');

		if (empty($sess)) {
			$this->db->insert('ci_dialer_sessions', array(
				'user_id' => $user_id,
				'status' => 'calling',
				'cooldown_seconds' => $cooldown,
				'status_filter' => $status_filter,
				'dialed_lead_ids' => '',
				'total_dialed' => 0,
				'created_at' => $now,
				'updated_at' => $now
			));
			$sess_id = $this->db->insert_id();
			$dialed_arr = array();
		} else {
			$sess_id = $sess['id'];
			$dialed_arr = !empty($sess['dialed_lead_ids']) ? array_filter(array_map('intval', explode(',', $sess['dialed_lead_ids']))) : array();
			$this->db->where('id', $sess_id)->update('ci_dialer_sessions', array(
				'status' => 'calling',
				'cooldown_seconds' => $cooldown,
				'status_filter' => $status_filter,
				'updated_at' => $now
			));
		}

		$this->db->select('A.id, A.lead_id, A.customer, A.poc, A.poc2, A.contact1, A.alt_contact, A.contact2, A.mobile, A.email, A.status, A.remark, S.status as status_name');
		$this->db->from('ci_leads as A');
		$this->db->join('ci_status as S', 'S.id = A.status', 'left');
		$this->_apply_lead_filters($status_filter);
		if (!empty($dialed_arr)) {
			$this->db->where_not_in('A.id', $dialed_arr);
		}
		$this->db->order_by('A.id', 'DESC');
		$this->db->limit(1);
		$lead = $this->db->get()->row_array();

		if (empty($lead)) {
			$this->db->where('id', $sess_id)->update('ci_dialer_sessions', array('status' => 'completed', 'updated_at' => $now));
			echo json_encode(array('status' => false, 'message' => 'No eligible contacts found in queue to auto-dial'));
			return;
		}

		$extracted = $this->_extract_lead_phones($lead);
		$phone = !empty($extracted['primary']) ? $extracted['primary'] : $extracted['alt'];
		$dispatch = $this->threecx->build_call_dispatch($phone);

		$this->db->where('id', $sess_id)->update('ci_dialer_sessions', array(
			'current_lead_id' => $lead['id'],
			'updated_at' => $now
		));

		$lead['phone_primary'] = $extracted['primary'];
		$lead['phone_alt'] = $extracted['alt'];
		$lead['has_two_numbers'] = $extracted['has_two'];
		$lead['dispatch'] = $dispatch;

		echo json_encode(array(
			'status' => true,
			'session_id' => $sess_id,
			'lead' => $lead,
			'cooldown_seconds' => $cooldown
		));
	}

	public function ajax_autodial_call_ended()
	{
		$user_id = (int)$this->session->userdata('manager_id');
		$sess = $this->db->where('user_id', $user_id)->get('ci_dialer_sessions')->row_array();
		if (empty($sess) || $sess['status'] === 'stopped') {
			echo json_encode(array('status' => false, 'message' => 'No active autodial session'));
			return;
		}

		$cooldown = !empty($sess['cooldown_seconds']) ? (int)$sess['cooldown_seconds'] : 60;
		$now_ts = time();
		$wrapup_ends_ts = $now_ts + $cooldown;
		$started_at = date('Y-m-d H:i:s', $now_ts);
		$ends_at = date('Y-m-d H:i:s', $wrapup_ends_ts);

		$this->db->where('id', $sess['id'])->update('ci_dialer_sessions', array(
			'status' => 'wrapup',
			'wrapup_started_at' => $started_at,
			'wrapup_ends_at' => $ends_at,
			'updated_at' => $started_at
		));

		echo json_encode(array(
			'status' => true,
			'session_status' => 'wrapup',
			'cooldown_seconds' => $cooldown,
			'remaining_seconds' => $cooldown,
			'wrapup_ends_at' => $ends_at
		));
	}

	public function ajax_autodial_extend()
	{
		$user_id = (int)$this->session->userdata('manager_id');
		$extra = (int)$this->input->post('seconds') ?: 30;

		$sess = $this->db->where('user_id', $user_id)->get('ci_dialer_sessions')->row_array();
		if (empty($sess) || $sess['status'] !== 'wrapup') {
			echo json_encode(array('status' => false, 'message' => 'Session not in wrap-up state'));
			return;
		}

		$current_end = !empty($sess['wrapup_ends_at']) ? strtotime($sess['wrapup_ends_at']) : time();
		if ($current_end < time()) {
			$current_end = time();
		}
		$new_end = $current_end + $extra;
		$new_ends_at = date('Y-m-d H:i:s', $new_end);
		$rem = max(0, $new_end - time());

		$this->db->where('id', $sess['id'])->update('ci_dialer_sessions', array(
			'wrapup_ends_at' => $new_ends_at,
			'updated_at' => date('Y-m-d H:i:s')
		));

		echo json_encode(array('status' => true, 'remaining_seconds' => $rem, 'wrapup_ends_at' => $new_ends_at));
	}

	public function ajax_autodial_pause()
	{
		$user_id = (int)$this->session->userdata('manager_id');
		$this->db->where('user_id', $user_id)->update('ci_dialer_sessions', array(
			'status' => 'paused',
			'updated_at' => date('Y-m-d H:i:s')
		));
		echo json_encode(array('status' => true, 'session_status' => 'paused'));
	}

	public function ajax_autodial_resume()
	{
		$user_id = (int)$this->session->userdata('manager_id');
		$sess = $this->db->where('user_id', $user_id)->get('ci_dialer_sessions')->row_array();
		if (!empty($sess)) {
			$cooldown = !empty($sess['cooldown_seconds']) ? (int)$sess['cooldown_seconds'] : 60;
			$ends_at = date('Y-m-d H:i:s', time() + $cooldown);
			$this->db->where('id', $sess['id'])->update('ci_dialer_sessions', array(
				'status' => 'wrapup',
				'wrapup_started_at' => date('Y-m-d H:i:s'),
				'wrapup_ends_at' => $ends_at,
				'updated_at' => date('Y-m-d H:i:s')
			));
			echo json_encode(array('status' => true, 'session_status' => 'wrapup', 'remaining_seconds' => $cooldown));
		} else {
			echo json_encode(array('status' => false, 'message' => 'No session found'));
		}
	}

	public function ajax_autodial_stop()
	{
		$user_id = (int)$this->session->userdata('manager_id');
		$this->db->where('user_id', $user_id)->update('ci_dialer_sessions', array(
			'status' => 'stopped',
			'current_lead_id' => null,
			'updated_at' => date('Y-m-d H:i:s')
		));
		echo json_encode(array('status' => true, 'session_status' => 'stopped'));
	}

	public function ajax_autodial_next()
	{
		$user_id = (int)$this->session->userdata('manager_id');
		$sess = $this->db->where('user_id', $user_id)->get('ci_dialer_sessions')->row_array();
		if (empty($sess) || $sess['status'] === 'stopped') {
			echo json_encode(array('status' => false, 'message' => 'No active autodial session'));
			return;
		}

		$current_lead_id = (int)$sess['current_lead_id'];
		$dialed_arr = !empty($sess['dialed_lead_ids']) ? array_filter(array_map('intval', explode(',', $sess['dialed_lead_ids']))) : array();
		if ($current_lead_id > 0 && !in_array($current_lead_id, $dialed_arr)) {
			$dialed_arr[] = $current_lead_id;
		}

		$status_filter = !empty($sess['status_filter']) ? $sess['status_filter'] : 'all';

		$this->db->select('A.id, A.lead_id, A.customer, A.poc, A.poc2, A.contact1, A.alt_contact, A.contact2, A.mobile, A.email, A.status, A.remark, S.status as status_name');
		$this->db->from('ci_leads as A');
		$this->db->join('ci_status as S', 'S.id = A.status', 'left');
		$this->_apply_lead_filters($status_filter);
		if (!empty($dialed_arr)) {
			$this->db->where_not_in('A.id', $dialed_arr);
		}
		$this->db->order_by('A.id', 'DESC');
		$this->db->limit(1);
		$next_lead = $this->db->get()->row_array();

		$now = date('Y-m-d H:i:s');
		if (empty($next_lead)) {
			$this->db->where('id', $sess['id'])->update('ci_dialer_sessions', array(
				'status' => 'completed',
				'dialed_lead_ids' => implode(',', $dialed_arr),
				'current_lead_id' => null,
				'updated_at' => $now
			));
			echo json_encode(array('status' => true, 'completed' => true, 'message' => 'Auto-dial queue complete. All contacts dialed.'));
			return;
		}

		$extracted = $this->_extract_lead_phones($next_lead);
		$phone = !empty($extracted['primary']) ? $extracted['primary'] : $extracted['alt'];
		$dispatch = $this->threecx->build_call_dispatch($phone);

		$total_dialed = (int)$sess['total_dialed'] + 1;
		$this->db->where('id', $sess['id'])->update('ci_dialer_sessions', array(
			'status' => 'calling',
			'current_lead_id' => $next_lead['id'],
			'dialed_lead_ids' => implode(',', $dialed_arr),
			'total_dialed' => $total_dialed,
			'updated_at' => $now
		));

		$next_lead['phone_primary'] = $extracted['primary'];
		$next_lead['phone_alt'] = $extracted['alt'];
		$next_lead['has_two_numbers'] = $extracted['has_two'];
		$next_lead['dispatch'] = $dispatch;

		echo json_encode(array(
			'status' => true,
			'completed' => false,
			'session_status' => 'calling',
			'lead' => $next_lead,
			'total_dialed' => $total_dialed
		));
	}

	public function ajax_autodial_status()
	{
		$user_id = (int)$this->session->userdata('manager_id');
		$sess = $this->db->where('user_id', $user_id)->get('ci_dialer_sessions')->row_array();
		if (empty($sess)) {
			echo json_encode(array('status' => false, 'session_status' => 'idle'));
			return;
		}

		$rem = 0;
		if ($sess['status'] === 'wrapup' && !empty($sess['wrapup_ends_at'])) {
			$rem = max(0, strtotime($sess['wrapup_ends_at']) - time());
		}

		echo json_encode(array(
			'status' => true,
			'session_status' => $sess['status'],
			'current_lead_id' => (int)$sess['current_lead_id'],
			'cooldown_seconds' => (int)$sess['cooldown_seconds'],
			'remaining_seconds' => $rem,
			'total_dialed' => (int)$sess['total_dialed']
		));
	}
}

