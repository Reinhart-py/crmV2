<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ci_admin_calls extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('session');
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

	public function index()
	{
		$manager_id = (int)$this->session->userdata('manager_id');
		$role = (int)$this->session->userdata('manager_role');

		$this->db->select('A.id, A.lead_id, A.customer, A.poc, A.contact1, A.alt_contact, A.email, A.category, A.status, A.tl_number, A.tl_authority, A.tl_emirate, A.tl_zone, A.r_amount, A.mrr, A.followup_date, A.created, S.status as status_name');
		$this->db->from('ci_leads as A');
		$this->db->join('ci_status as S', 'S.id = A.status', 'left');
		$this->db->where('(A.company_status IS NULL OR LOWER(A.company_status) != "closed")');
		$this->db->where('(A.contact1 != "" OR A.alt_contact != "")');
		if ($role != 1 && $role != 4) {
			$this->db->where("(A.lead_assign = $manager_id OR A.created_by = $manager_id)");
		}
		$this->db->order_by('A.id', 'DESC');
		$this->db->limit(60);
		$queue = $this->db->get()->result_array();

		$data = array();
		$data['queue'] = $queue;
		$data['total_queue'] = count($queue);
		$data['active_lead'] = !empty($queue) ? $queue[0] : null;
		$data['statuses'] = $this->db->order_by('id', 'ASC')->get('ci_status')->result_array();
		$data['manager_role'] = $role;
		$data['manager_id'] = $manager_id;
		$data['manager_name'] = $this->session->userdata('manager_name');

		$this->load->view('manager/calls/dashboard', $data);
	}

	public function ajax_get_lead($lead_id = 0)
	{
		$lead_id = (int)$lead_id;
		if ($lead_id <= 0) {
			echo json_encode(array('status' => false, 'message' => 'Invalid Lead ID'));
			return;
		}

		$this->db->select('A.*, S.status as status_name');
		$this->db->from('ci_leads as A');
		$this->db->join('ci_status as S', 'S.id = A.status', 'left');
		$this->db->where('A.id', $lead_id);
		$this->db->where('(A.company_status IS NULL OR LOWER(A.company_status) != "closed")');
		$lead = $this->db->get()->row_array();

		if (empty($lead)) {
			echo json_encode(array('status' => false, 'message' => 'Lead not found or company is closed'));
			return;
		}

		$this->db->select('F.*, U.name as agent_name');
		$this->db->from('ci_followup as F');
		$this->db->join('ci_user as U', 'U.id = F.user_id', 'left');
		$this->db->where('F.lead_id', $lead_id);
		$this->db->order_by('F.id', 'DESC');
		$this->db->limit(5);
		$history = $this->db->get()->result_array();

		$lead['history'] = $history;
		echo json_encode(array('status' => true, 'lead' => $lead));
	}

	public function ajax_save_call()
	{
		$lead_id = (int)$this->input->post('lead_id');
		$outcome = trim($this->input->post('call_disposition'));
		if (empty($outcome)) {
			$outcome = trim($this->input->post('outcome'));
		}
		if (empty($outcome)) {
			$outcome = 'Connected';
		}
		$remarks = trim($this->input->post('remarks'));
		$next_date = trim($this->input->post('next_followup'));
		if (empty($next_date)) {
			$next_date = trim($this->input->post('next_date'));
		}
		$next_time = trim($this->input->post('next_followup_time'));
		if (empty($next_time)) {
			$next_time = trim($this->input->post('next_time'));
		}
		$duration = (int)$this->input->post('call_duration');
		if ($duration <= 0) {
			$duration = (int)$this->input->post('duration');
		}
		$status_id = (int)$this->input->post('status');
		if ($status_id <= 0) {
			$status_id = (int)$this->input->post('status_id');
		}
		$manager_id = (int)$this->session->userdata('manager_id');

		if ($lead_id <= 0) {
			echo json_encode(array('status' => false, 'message' => 'Lead ID required'));
			return;
		}

		$remark_full = "[Call: $outcome" . ($duration > 0 ? " | Duration: {$duration}s" : "") . "] " . $remarks;

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
		if ($status_id > 0) {
			$lead_update['status'] = $status_id;
		}
		$this->db->where('id', $lead_id);
		$this->db->update('ci_leads', $lead_update);

		$this->ci_admin_model->log_admin_action(
			'call_remark_save',
			"Saved call disposition '$outcome' with remarks for Lead ID #$lead_id",
			'SUCCESS',
			$manager_id,
			$this->session->userdata('manager_name')
		);

		echo json_encode(array(
			'status' => true,
			'message' => 'Call remarks saved successfully',
			'outcome' => $outcome,
			'lead_id' => $lead_id,
			'duration' => $duration
		));
	}
}
