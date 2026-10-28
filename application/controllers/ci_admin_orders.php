<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ci_admin_orders extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('ci_admin_model');
		$this->load->model('comman_model');
		$this->load->library('session');
		$this->load->helper(array('url', 'form', 'text', 'date'));
		$this->if_not_login();
	}

	private function if_not_login()
	{
		if (!$this->session->userdata('manager_id')) {
			redirect('manager/login');
		}
	}

	public function index()
	{
		$data = array();
		$cur_uid = (int)$this->session->userdata('manager_id');
		$cur_role = (int)$this->session->userdata('manager_role');
		$is_backoffice = ($cur_role === 1 || $cur_role === 2 || $cur_role === 4 || $cur_uid === 1 || has_section_perm('kam', 'write') || has_section_perm('back_office'));

		$client_filter = $this->input->get('client', true);
		$vertical_filter = $this->input->get('vertical', true);
		$service_filter = $this->input->get('service', true);
		$status_filter = $this->input->get('status', true);
		$dept_filter = $this->input->get('department', true);
		$staff_filter = $this->input->get('staff', true);
		$d1 = $this->input->get('d1', true);
		$d2 = $this->input->get('d2', true);
		$scope = $this->uri->segment(3) === 'my' ? 'my' : ($this->uri->segment(3) === 'team' ? 'team' : $this->input->get('scope', true));
		if ($this->uri->segment(2) === 'sales') {
			$status_filter = 'sales';
		}

		$can_edit_orders = false;
		$reset_url = base_url('manager/orders');

		$this->db->select('O.*, U.name as staff_name, A.customer as lead_customer, A.lead_id as lead_code');
		$this->db->from('ci_orders O');
		$this->db->join('ci_user U', 'O.assigned_to = U.id', 'LEFT');
		$this->db->join('ci_leads A', 'O.lead_id = A.id', 'LEFT');

		if ($scope === 'my') {
			$data['info'] = array(
				'title' => 'My Orders - Seven Strokes CRM',
				'page_heading' => 'My Orders'
			);
			$this->db->where("(O.created_by = {$cur_uid} OR O.assigned_to = {$cur_uid} OR A.created_by = {$cur_uid} OR A.lead_assign = {$cur_uid} OR FIND_IN_SET({$cur_uid}, A.agents) > 0 OR O.lead_id IN (SELECT lead_id FROM ci_lead_transfers WHERE from_user_id = {$cur_uid} OR to_user_id = {$cur_uid}) OR O.lead_id IN (SELECT lead_id FROM ci_pipeline WHERE conversion_requested_by = {$cur_uid} OR order_requested_by = {$cur_uid}) OR O.lead_id IN (SELECT lead_id FROM ci_pipeline_logs WHERE action_by = {$cur_uid}))");
			$can_edit_orders = false;
			$reset_url = base_url('manager/orders/my');
		} elseif ($scope === 'team') {
			$data['info'] = array(
				'title' => 'Team Orders - Seven Strokes CRM',
				'page_heading' => 'Team Orders'
			);
			$can_edit_orders = false;
			$reset_url = base_url('manager/team/orders');
			$team_members = $this->ci_admin_model->fetchTeamList($cur_uid, '');
			if (!is_array($team_members)) {
				$team_members = array();
			}
			$team_members = array_unique(array_filter(array_map('intval', $team_members)));
			if (!empty($team_members)) {
				$team_str = implode(',', $team_members);
				$this->db->where("(FIND_IN_SET(O.created_by, '{$team_str}') > 0 OR FIND_IN_SET(O.assigned_to, '{$team_str}') > 0)");
			} else {
				$this->db->where('1 = 0', NULL, FALSE);
			}
		} elseif ($status_filter === 'sales' || $status_filter === 'Sales') {
			$data['info'] = array(
				'title' => 'View Sales - Seven Strokes CRM',
				'page_heading' => 'View Sales'
			);
			$this->db->where('O.status', 'Sales');
			if (!$is_backoffice) {
				$this->db->where("(O.created_by = {$cur_uid} OR O.assigned_to = {$cur_uid} OR A.created_by = {$cur_uid} OR A.lead_assign = {$cur_uid} OR FIND_IN_SET({$cur_uid}, A.agents) > 0 OR O.lead_id IN (SELECT lead_id FROM ci_lead_transfers WHERE from_user_id = {$cur_uid} OR to_user_id = {$cur_uid}) OR O.lead_id IN (SELECT lead_id FROM ci_pipeline WHERE conversion_requested_by = {$cur_uid} OR order_requested_by = {$cur_uid}) OR O.lead_id IN (SELECT lead_id FROM ci_pipeline_logs WHERE action_by = {$cur_uid}))");
				$can_edit_orders = false;
			} else {
				$can_edit_orders = true;
			}
			$reset_url = base_url('manager/sales');
		} elseif ($status_filter === 'ongoing') {
			$data['info'] = array(
				'title' => 'Ongoing Orders - Back Office',
				'page_heading' => 'Ongoing Orders'
			);
			$this->db->where_not_in('O.status', array('Completed', 'Fulfilled', 'Sales', 'Rejected', 'Lost', 'Cancelled'));
			$can_edit_orders = $is_backoffice;
			$reset_url = base_url('manager/orders/ongoing');
		} elseif ($status_filter === 'completed') {
			$data['info'] = array(
				'title' => 'Completed Orders - Back Office',
				'page_heading' => 'Completed Orders'
			);
			$this->db->where_in('O.status', array('Fulfilled', 'Completed'));
			$this->db->where("(O.sales_ref_no IS NULL OR O.sales_ref_no = '')");
			$this->db->where("O.status != 'Sales'");
			$can_edit_orders = $is_backoffice;
			$reset_url = base_url('manager/orders/completed');
		} elseif ($status_filter === 'closed') {
			$data['info'] = array(
				'title' => 'Closed Orders - Back Office',
				'page_heading' => 'Closed Orders'
			);
			$this->db->where("(O.status = 'Sales' OR (O.sales_ref_no IS NOT NULL AND O.sales_ref_no != ''))");
			$can_edit_orders = $is_backoffice;
			$reset_url = base_url('manager/orders/closed');
		} elseif ($status_filter === 'rejected_lost') {
			$data['info'] = array(
				'title' => 'Rejected / Lost Orders - Back Office',
				'page_heading' => 'Rejected / Lost Orders'
			);
			$this->db->where_in('O.status', array('Rejected', 'Lost', 'Cancelled'));
			$can_edit_orders = $is_backoffice;
			$reset_url = base_url('manager/orders/rejected_lost');
		} elseif ($status_filter === 'open') {
			$data['info'] = array(
				'title' => 'Open Orders - Back Office',
				'page_heading' => 'Open Orders'
			);
			$this->db->where_not_in('O.status', array('Fulfilled', 'Completed', 'Sales', 'Cancelled', 'Rejected'));
			$can_edit_orders = $is_backoffice;
			$reset_url = base_url('manager/orders/open');
		} else {
			if (!$is_backoffice) {
				redirect('manager/orders/my');
				return;
			}
			$data['info'] = array(
				'title' => 'View All Orders - Back Office',
				'page_heading' => 'View All Orders'
			);
			$can_edit_orders = true;
			$reset_url = base_url('manager/orders');
		}

		if (!empty($client_filter)) {
			$this->db->where('(O.client_name LIKE "%'.$this->db->escape_like_str($client_filter).'%" OR O.order_no LIKE "%'.$this->db->escape_like_str($client_filter).'%" OR O.client_id = '.(int)$client_filter.')');
		}
		if (!empty($vertical_filter)) {
			$this->db->where('O.vertical_code', $vertical_filter);
		}
		if (!empty($service_filter)) {
			$this->db->like('O.service_title', $service_filter);
		}
		$preset_filters = array('sales', 'Sales', 'completed', 'closed', 'rejected_lost', 'open', 'followups');
		if (!empty($status_filter) && !in_array($status_filter, $preset_filters)) {
			if ($status_filter === 'ongoing') {
				$this->db->where_in('O.status', array('Approved', 'In Progress', 'Under Review'));
			} else {
				$this->db->where('O.status', $status_filter);
			}
		}
		if (!empty($dept_filter)) {
			$this->db->where('O.assigned_dept', $dept_filter);
		}
		if (!empty($staff_filter)) {
			$this->db->where('O.assigned_to', (int)$staff_filter);
		}
		if (!empty($d1) && !empty($d2)) {
			$d1_clean = date('Y-m-d 00:00:00', strtotime($d1));
			$d2_clean = date('Y-m-d 23:59:59', strtotime($d2));
			$this->db->where("O.created_at >= '{$d1_clean}' AND O.created_at <= '{$d2_clean}'");
		}

		$this->db->order_by('O.id', 'DESC');
		$orders_res = $this->db->get()->result_array();

		$data['orders_list'] = $orders_res;
		$data['can_edit_orders'] = $can_edit_orders;
		$data['status_filter'] = $status_filter;
		$data['scope_filter'] = $scope;
		$data['reset_url'] = $reset_url;
		$data['staff_list'] = $this->db->select('id, name, department')->where('status', 1)->order_by('name', 'ASC')->get('ci_user')->result_array();
		$data['clients_list'] = $this->db->select('id, customer, lead_id')->order_by('customer', 'ASC')->get('ci_leads')->result_array();

		$this->load->view('manager/orders/index', $data);
	}

	public function add()
	{
		$cur_uid = (int)$this->session->userdata('manager_id');
		$cur_role = (int)$this->session->userdata('manager_role');
		$cur_name = (string)$this->session->userdata('manager_name');

		if ($this->input->post()) {
			$client_id = (int)$this->input->post('client_id');
			$client_name = trim((string)$this->input->post('client_name'));
			$lead_id = (int)$this->input->post('lead_id');
			$vertical_code = trim((string)$this->input->post('vertical_code'));
			$service_title = trim((string)$this->input->post('service_title'));
			$service_scope = trim((string)$this->input->post('service_scope'));
			$pricing_amount = (float)$this->input->post('pricing_amount');
			$mrc = (float)$this->input->post('mrc');
			$otc = (float)$this->input->post('otc');
			$mrr = (float)$this->input->post('mrr');
			$currency = trim((string)$this->input->post('currency')) ?: 'AED';
			$billing_type = trim((string)$this->input->post('billing_type')) ?: 'Monthly';
			$assigned_dept = trim((string)$this->input->post('assigned_dept'));
			$assigned_to = (int)$this->input->post('assigned_to');
			$operational_instructions = trim((string)$this->input->post('operational_instructions'));

			if (empty($client_name) && !empty($client_id)) {
				$cl_row = $this->db->select('customer')->where('id', $client_id)->get('ci_leads')->row_array();
				if (!empty($cl_row['customer'])) {
					$client_name = $cl_row['customer'];
				}
			}

			if (empty($service_title)) {
				$this->session->set_flashdata('error_message', 'Service Title is required to create an Order.');
				redirect('manager/orders/add');
				return;
			}

			$order_no = $this->ci_admin_model->generate_next_ref('order');

			$default_docs = array();
			if ($vertical_code === 'accounting') {
				$default_docs = array(
					'trade_license' => array('title' => 'Trade License', 'status' => 'pending'),
					'vat_cert' => array('title' => 'VAT / Tax Certificate', 'status' => 'pending'),
					'bank_statement' => array('title' => 'Bank Statement (3 Months)', 'status' => 'pending'),
					'passport_copy' => array('title' => 'Owner Passport & Emirates ID', 'status' => 'pending')
				);
			} elseif ($vertical_code === 'telecom') {
				$default_docs = array(
					'trade_license' => array('title' => 'Valid Trade License', 'status' => 'pending'),
					'ejari' => array('title' => 'Ejari / Tenancy Contract', 'status' => 'pending'),
					'authorized_signatory' => array('title' => 'Authorized Signatory EID', 'status' => 'pending'),
					'loa' => array('title' => 'Letter of Authorization', 'status' => 'pending')
				);
			} elseif ($vertical_code === 'it') {
				$default_docs = array(
					'network_survey' => array('title' => 'Network & Infrastructure Survey', 'status' => 'pending'),
					'service_agreement' => array('title' => 'Service Level Agreement', 'status' => 'pending'),
					'access_creds' => array('title' => 'System Access Authorization', 'status' => 'pending')
				);
			} else {
				$default_docs = array(
					'id_proof' => array('title' => 'Identity Document', 'status' => 'pending'),
					'service_scope' => array('title' => 'Scope Document', 'status' => 'pending')
				);
			}

			$now = date('Y-m-d H:i:s');
			$db_ref = 'DB-' . $lead_id;
			$lead_ref = 'LEAD-' . $lead_id;
			if ($lead_id > 0) {
				$pipe = $this->db->get_where('ci_pipeline', array('lead_id' => $lead_id))->row_array();
				if (!empty($pipe['db_ref_no'])) {
					$db_ref = $pipe['db_ref_no'];
				}
				if (!empty($pipe['lead_ref_no'])) {
					$lead_ref = $pipe['lead_ref_no'];
				}
			}

			$order_data = array(
				'order_no' => $order_no,
				'db_ref_no' => $db_ref,
				'lead_ref_no' => $lead_ref,
				'client_id' => $client_id,
				'client_name' => $client_name,
				'lead_id' => $lead_id,
				'vertical_code' => $vertical_code,
				'service_title' => $service_title,
				'service_scope' => $service_scope,
				'pricing_amount' => $pricing_amount,
				'mrc' => $mrc,
				'otc' => $otc,
				'mrr' => $mrr,
				'currency' => $currency,
				'billing_type' => $billing_type,
				'documents_status' => json_encode($default_docs),
				'assigned_dept' => $assigned_dept,
				'assigned_to' => $assigned_to,
				'operational_instructions' => $operational_instructions,
				'status' => 'Submitted',
				'approval_status' => 'pending',
				'approval_history' => json_encode(array(
					array(
						'action' => 'created',
						'user_id' => $cur_uid,
						'user_name' => $cur_name,
						'timestamp' => $now,
						'remarks' => 'Order created and submitted for Back Office approval'
					)
				)),
				'created_by' => $cur_uid,
				'created_at' => $now,
				'updated_at' => $now
			);

			$this->db->insert('ci_orders', $order_data);
			$new_order_id = $this->db->insert_id();
			$order_no = sprintf('O%010d', $new_order_id);
			$this->db->where('id', $new_order_id)->update('ci_orders', array('order_no' => $order_no));

			if ($lead_id > 0) {
				$this->db->where('lead_id', $lead_id)->update('ci_pipeline', array(
					'order_request_status' => 'ordered',
					'is_locked' => 1,
					'locked_reason' => "Order #{$order_no} created and being processed",
					'updated_at' => $now
				));
				$this->db->insert('ci_pipeline_logs', array(
					'lead_id' => $lead_id,
					'action' => 'order_created',
					'stage_from' => 'lead',
					'stage_to' => 'order',
					'action_by' => $cur_uid,
					'action_by_name' => $cur_name,
					'remarks' => "Order #{$order_no} created in Back Office",
					'created_at' => $now
				));
			}

			$this->db->insert('ci_order_history', array(
				'order_id' => $new_order_id,
				'action' => 'order_created',
				'notes' => "Order #{$order_no} created for service '{$service_title}'",
				'action_by' => $cur_uid,
				'action_by_name' => $cur_name,
				'created_at' => $now
			));

			$this->ci_admin_model->log_admin_action('order_created', "Created Order #{$order_no}", 'SUCCESS', $cur_uid, $cur_name);
			$this->session->set_flashdata('message', "Order #{$order_no} has been created successfully.");
			redirect('manager/orders/view/' . $new_order_id);
			return;
		}

		$data = array();
		$data['info'] = array(
			'title' => 'Back Office - Create Order',
			'page_heading' => 'Create New Client Order'
		);
		$data['staff_list'] = $this->db->select('id, name, department')->where('status', 1)->order_by('name', 'ASC')->get('ci_user')->result_array();
		$data['clients_list'] = $this->db->select('id, customer, lead_id')->order_by('customer', 'ASC')->get('ci_leads')->result_array();

		$this->load->view('manager/orders/add', $data);
	}

	public function view($id = 0)
	{
		$id = (int)$id;
		if ($id <= 0) {
			redirect('manager/orders');
			return;
		}

		$this->db->select('O.*, U.name as staff_name, A.customer as lead_customer, A.contact1 as lead_contact, A.email as lead_email');
		$this->db->from('ci_orders O');
		$this->db->join('ci_user U', 'O.assigned_to = U.id', 'LEFT');
		$this->db->join('ci_leads A', 'O.lead_id = A.id', 'LEFT');
		if (is_numeric($id)) {
			$this->db->where('O.id', (int)$id);
		} else {
			$this->db->where('O.order_no', $id);
		}
		$order = $this->db->get()->row_array();

		if (empty($order)) {
			$this->session->set_flashdata('error_message', 'Order not found.');
			redirect('manager/orders');
			return;
		}

		$cur_uid = (int)$this->session->userdata('manager_id');
		$cur_role = (int)$this->session->userdata('manager_role');
		$is_backoffice = ($cur_role === 1 || $cur_role === 2 || $cur_role === 4 || $cur_uid === 1 || has_section_perm('kam', 'write') || has_section_perm('back_office'));

		if (!$is_backoffice) {
			$has_ownership = false;
			if ((int)$order['created_by'] === $cur_uid || (int)$order['assigned_to'] === $cur_uid) {
				$has_ownership = true;
			}
			if (!$has_ownership && !empty($order['lead_id'])) {
				$lid = (int)$order['lead_id'];
				$lead_row = $this->db->select('created_by, manager_id, agents')->where('id', $lid)->get('ci_leads')->row_array();
				if (!empty($lead_row)) {
					if ((int)$lead_row['created_by'] === $cur_uid || (int)$lead_row['manager_id'] === $cur_uid) {
						$has_ownership = true;
					} elseif (!empty($lead_row['agents']) && in_array((string)$cur_uid, explode(',', $lead_row['agents']))) {
						$has_ownership = true;
					}
				}
				if (!$has_ownership) {
					$transfer_exists = $this->db->where('lead_id', $lid)
						->where("(from_user_id = {$cur_uid} OR to_user_id = {$cur_uid})")
						->count_all_results('ci_lead_transfers');
					if ($transfer_exists > 0) {
						$has_ownership = true;
					}
				}
				if (!$has_ownership) {
					$pipe_exists = $this->db->where('lead_id', $lid)
						->where("(conversion_requested_by = {$cur_uid} OR order_requested_by = {$cur_uid} OR action_by = {$cur_uid})")
						->count_all_results('ci_pipeline_logs');
					if ($pipe_exists > 0) {
						$has_ownership = true;
					}
				}
			}

			if (!$has_ownership) {
				$this->session->set_flashdata('error_message', 'Access denied.');
				redirect('manager/orders/my');
				return;
			}
		}

		$can_edit = $is_backoffice;
		$can_approve = ($is_backoffice && ($cur_role === 1 || $cur_role === 2 || $cur_role === 4 || $cur_uid === 1 || has_section_perm('back_office') || has_section_perm('kam', 'write')));

		$data = array();
		$data['info'] = array(
			'title' => 'Order #' . $order['order_no'],
			'page_heading' => 'Order Details: ' . $order['order_no']
		);
		$data['order'] = $order;
		$data['docs'] = !empty($order['documents_status']) ? json_decode($order['documents_status'], true) : array();
		$data['approval_history'] = !empty($order['approval_history']) ? json_decode($order['approval_history'], true) : array();
		$data['activity_logs'] = $this->db->where('order_id', $id)->order_by('id', 'DESC')->get('ci_order_history')->result_array();
		$data['can_approve'] = $can_approve;
		$data['can_edit'] = $can_edit;
		$data['staff_list'] = $this->db->select('id, name, department')->where('status', 1)->order_by('name', 'ASC')->get('ci_user')->result_array();

		$this->load->view('manager/orders/view', $data);
	}

	public function approve($id = 0)
	{
		$id = (int)$id;
		$cur_uid = (int)$this->session->userdata('manager_id');
		$cur_role = (int)$this->session->userdata('manager_role');
		$cur_name = (string)$this->session->userdata('manager_name');
		$remarks = trim((string)$this->input->post('remarks'));

		$is_backoffice = ($cur_role === 1 || $cur_role === 2 || $cur_role === 4 || $cur_uid === 1 || has_section_perm('kam', 'write') || has_section_perm('back_office'));
		if (!$is_backoffice) {
			$this->session->set_flashdata('error_message', 'Access denied. Only Back Office / Admin can approve orders.');
			redirect('manager/orders/view/' . $id);
			return;
		}

		$order = $this->db->where('id', $id)->get('ci_orders')->row_array();
		if (empty($order)) {
			redirect('manager/orders');
			return;
		}

		$history = !empty($order['approval_history']) ? json_decode($order['approval_history'], true) : array();
		$now = date('Y-m-d H:i:s');
		$history[] = array(
			'action' => 'approved',
			'user_id' => $cur_uid,
			'user_name' => $cur_name,
			'timestamp' => $now,
			'remarks' => !empty($remarks) ? $remarks : 'Order approved by Back Office'
		);

		$this->db->where('id', $id)->update('ci_orders', array(
			'approval_status' => 'approved',
			'status' => 'Approved',
			'approved_by' => $cur_uid,
			'approved_at' => $now,
			'approval_remarks' => $remarks,
			'approval_history' => json_encode($history),
			'updated_at' => $now
		));

		$this->db->insert('ci_order_history', array(
			'order_id' => $id,
			'action' => 'order_approved',
			'notes' => !empty($remarks) ? "Approved: {$remarks}" : "Order approved by {$cur_name}",
			'action_by' => $cur_uid,
			'action_by_name' => $cur_name,
			'created_at' => $now
		));

		$this->ci_admin_model->log_admin_action('order_approved', "Approved Order #{$order['order_no']}", 'SUCCESS', $cur_uid, $cur_name);
		$this->session->set_flashdata('message', "Order #{$order['order_no']} has been approved.");
		redirect('manager/orders/view/' . $id);
	}

	public function reject($id = 0)
	{
		$id = (int)$id;
		$cur_uid = (int)$this->session->userdata('manager_id');
		$cur_role = (int)$this->session->userdata('manager_role');
		$cur_name = (string)$this->session->userdata('manager_name');
		$remarks = trim((string)$this->input->post('remarks'));

		$is_backoffice = ($cur_role === 1 || $cur_role === 2 || $cur_role === 4 || $cur_uid === 1 || has_section_perm('kam', 'write') || has_section_perm('back_office'));
		if (!$is_backoffice) {
			$this->session->set_flashdata('error_message', 'Access denied.');
			redirect('manager/orders/view/' . $id);
			return;
		}

		$order = $this->db->where('id', $id)->get('ci_orders')->row_array();
		if (empty($order)) {
			redirect('manager/orders');
			return;
		}

		$history = !empty($order['approval_history']) ? json_decode($order['approval_history'], true) : array();
		$now = date('Y-m-d H:i:s');
		$history[] = array(
			'action' => 'rejected',
			'user_id' => $cur_uid,
			'user_name' => $cur_name,
			'timestamp' => $now,
			'remarks' => !empty($remarks) ? $remarks : 'Order rejected'
		);

		$this->db->where('id', $id)->update('ci_orders', array(
			'approval_status' => 'rejected',
			'status' => 'Draft',
			'approval_remarks' => $remarks,
			'approval_history' => json_encode($history),
			'updated_at' => $now
		));

		$this->db->insert('ci_order_history', array(
			'order_id' => $id,
			'action' => 'order_rejected',
			'notes' => !empty($remarks) ? "Rejected: {$remarks}" : "Order rejected by {$cur_name}",
			'action_by' => $cur_uid,
			'action_by_name' => $cur_name,
			'created_at' => $now
		));

		$this->session->set_flashdata('message', "Order #{$order['order_no']} has been marked rejected.");
		redirect('manager/orders/view/' . $id);
	}

	public function update_status($id = 0)
	{
		$id = (int)$id;
		$status = trim((string)$this->input->post('status'));
		$notes = trim((string)$this->input->post('notes'));
		$cur_uid = (int)$this->session->userdata('manager_id');
		$cur_role = (int)$this->session->userdata('manager_role');
		$cur_name = (string)$this->session->userdata('manager_name');

		$is_backoffice = ($cur_role === 1 || $cur_role === 2 || $cur_role === 4 || $cur_uid === 1 || has_section_perm('kam', 'write') || has_section_perm('back_office'));
		if (!$is_backoffice) {
			$this->session->set_flashdata('error_message', 'Access denied. Only Back Office and Admin can update order status.');
			redirect('manager/orders/view/' . $id);
			return;
		}

		$allowed = array('Draft', 'Submitted', 'Under Review', 'Approved', 'In Progress', 'Fulfilled', 'Cancelled');
		if (!in_array($status, $allowed)) {
			redirect('manager/orders/view/' . $id);
			return;
		}

		$now = date('Y-m-d H:i:s');
		$this->db->where('id', $id)->update('ci_orders', array(
			'status' => $status,
			'updated_at' => $now
		));

		$this->db->insert('ci_order_history', array(
			'order_id' => $id,
			'action' => 'status_changed',
			'notes' => "Status updated to '{$status}'. " . (!empty($notes) ? "Note: {$notes}" : ''),
			'action_by' => $cur_uid,
			'action_by_name' => $cur_name,
			'created_at' => $now
		));

		$this->session->set_flashdata('message', "Order status updated to '{$status}'.");
		redirect('manager/orders/view/' . $id);
	}

	public function ajax_toggle_doc()
	{
		$order_id = (int)$this->input->post('order_id');
		$doc_key = trim((string)$this->input->post('doc_key'));
		$new_status = trim((string)$this->input->post('status'));
		$cur_uid = (int)$this->session->userdata('manager_id');
		$cur_role = (int)$this->session->userdata('manager_role');
		$cur_name = (string)$this->session->userdata('manager_name');

		$is_backoffice = ($cur_role === 1 || $cur_role === 2 || $cur_role === 4 || $cur_uid === 1 || has_section_perm('kam', 'write') || has_section_perm('back_office'));
		if (!$is_backoffice) {
			echo json_encode(array('success' => false, 'message' => 'Access denied. Only Back Office and Admin can edit order documents.'));
			return;
		}
		$cur_name = (string)$this->session->userdata('manager_name');

		$order = $this->db->where('id', $order_id)->get('ci_orders')->row_array();
		if (empty($order) || empty($doc_key)) {
			echo json_encode(array('success' => false, 'message' => 'Invalid order or document key'));
			return;
		}

		$docs = !empty($order['documents_status']) ? json_decode($order['documents_status'], true) : array();
		if (!isset($docs[$doc_key])) {
			echo json_encode(array('success' => false, 'message' => 'Document item not found in checklist'));
			return;
		}

		$docs[$doc_key]['status'] = ($new_status === 'collected') ? 'collected' : 'pending';
		$docs[$doc_key]['updated_at'] = date('Y-m-d H:i:s');
		$docs[$doc_key]['updated_by'] = $cur_name;

		$now = date('Y-m-d H:i:s');
		$this->db->where('id', $order_id)->update('ci_orders', array(
			'documents_status' => json_encode($docs),
			'updated_at' => $now
		));

		$doc_title = $docs[$doc_key]['title'] ?? $doc_key;
		$this->db->insert('ci_order_history', array(
			'order_id' => $order_id,
			'action' => 'document_updated',
			'notes' => "Document '{$doc_title}' marked as '{$docs[$doc_key]['status']}'",
			'action_by' => $cur_uid,
			'action_by_name' => $cur_name,
			'created_at' => $now
		));

		echo json_encode(array('success' => true, 'new_status' => $docs[$doc_key]['status']));
	}

	public function add_note($id = 0)
	{
		$id = (int)$id;
		$notes = trim((string)$this->input->post('notes'));
		$cur_uid = (int)$this->session->userdata('manager_id');
		$cur_role = (int)$this->session->userdata('manager_role');
		$cur_name = (string)$this->session->userdata('manager_name');

		$is_backoffice = ($cur_role === 1 || $cur_role === 2 || $cur_role === 4 || $cur_uid === 1 || has_section_perm('kam', 'write') || has_section_perm('back_office'));
		if (!$is_backoffice) {
			$this->session->set_flashdata('error_message', 'Access denied.');
			redirect('manager/orders/view/' . $id);
			return;
		}

		if (!empty($notes) && $id > 0) {
			$this->db->insert('ci_order_history', array(
				'order_id' => $id,
				'action' => 'note_added',
				'notes' => $notes,
				'action_by' => $cur_uid,
				'action_by_name' => $cur_name,
				'created_at' => date('Y-m-d H:i:s')
			));
			$this->session->set_flashdata('message', 'Activity note recorded successfully.');
		}
		redirect('manager/orders/view/' . $id);
	}

	public function pending_process()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Pending Order Process - Back Office',
			'page_heading' => 'Pending Order Process'
		);

		$cur_uid = (int)$this->session->userdata('manager_id');
		$cur_role = (int)$this->session->userdata('manager_role');

		$is_backoffice = ($cur_role === 1 || $cur_role === 2 || $cur_role === 4 || $cur_uid === 1 || has_section_perm('kam', 'write') || has_section_perm('back_office'));
		if (!$is_backoffice) {
			$this->session->set_flashdata('error_message', 'Access denied. Pending Order Process is restricted to Back Office and KAM staff.');
			redirect('manager/dashboard');
			return;
		}

		$this->db->select('L.*, P.db_ref_no, P.lead_ref_no, P.order_request_status, P.order_requested_at, P.order_request_remarks, U.name as requested_by_name');
		$this->db->from('ci_leads L');
		$this->db->join('ci_pipeline P', 'L.id = P.lead_id', 'INNER');
		$this->db->join('ci_user U', 'P.order_requested_by = U.id', 'LEFT');
		$this->db->where('P.order_request_status', 'pending');

		$client = trim((string)$this->input->get('client'));
		if (!empty($client)) {
			$this->db->group_start();
			$this->db->like('L.customer', $client);
			$this->db->or_like('L.company_name', $client);
			$this->db->or_like('P.db_ref_no', $client);
			$this->db->or_like('P.lead_ref_no', $client);
			$this->db->group_end();
		}

		$d1 = trim((string)$this->input->get('d1'));
		$d2 = trim((string)$this->input->get('d2'));
		if (!empty($d1) && !empty($d2)) {
			$from = date('Y-m-d 00:00:00', strtotime($d1));
			$to = date('Y-m-d 23:59:59', strtotime($d2));
			$this->db->where('P.order_requested_at >=', $from);
			$this->db->where('P.order_requested_at <=', $to);
		} elseif (!empty($d1)) {
			$from = date('Y-m-d 00:00:00', strtotime($d1));
			$this->db->where('P.order_requested_at >=', $from);
		}

		$this->db->order_by('P.order_requested_at', 'DESC');
		$data['pending_leads'] = $this->db->get()->result_array();
		$data['reset_url'] = base_url('manager/orders/pending_process');

		$this->load->view('manager/orders/pending_process', $data);
	}

	public function convert_to_sales($id = 0)
	{
		$id = (int)$id;
		$cur_uid = (int)$this->session->userdata('manager_id');
		$cur_role = (int)$this->session->userdata('manager_role');
		$cur_name = (string)$this->session->userdata('manager_name');

		$is_backoffice = ($cur_role === 1 || $cur_role === 2 || $cur_role === 4 || $cur_uid === 1 || has_section_perm('kam', 'write') || has_section_perm('back_office'));
		if (!$is_backoffice) {
			$this->session->set_flashdata('error_message', 'Only authorized KAM, Back Office staff, or Admin can convert an Order to Sales.');
			redirect('manager/orders/view/' . $id);
			return;
		}

		$order = $this->db->where('id', $id)->get('ci_orders')->row_array();
		if (empty($order)) {
			$this->session->set_flashdata('error_message', 'Order record not found.');
			redirect('manager/orders');
			return;
		}

		if ($order['status'] === 'Sales') {
			$this->session->set_flashdata('error_message', 'This Order is already converted to Sales.');
			redirect('manager/orders/view/' . $id);
			return;
		}

		$now = date('Y-m-d H:i:s');
		$sales_ref_no = !empty($order['sales_ref_no']) ? $order['sales_ref_no'] : $this->ci_admin_model->generate_next_ref('sales');
		$notes = trim((string)$this->input->post('notes'));

		$this->db->where('id', $id)->update('ci_orders', array(
			'status' => 'Sales',
			'sales_ref_no' => $sales_ref_no,
			'sales_converted_at' => $now,
			'sales_converted_by' => $cur_uid,
			'updated_at' => $now
		));

		$this->db->insert('ci_order_history', array(
			'order_id' => $id,
			'action' => 'converted_to_sales',
			'notes' => "Order converted to Sales (#{$sales_ref_no}). " . (!empty($notes) ? "Note: {$notes}" : 'Customer won, payment & completion requirements verified.'),
			'action_by' => $cur_uid,
			'action_by_name' => $cur_name,
			'created_at' => $now
		));

		if (!empty($order['lead_id'])) {
			$this->db->insert('ci_pipeline_logs', array(
				'lead_id' => (int)$order['lead_id'],
				'action' => 'convert_to_sales',
				'stage_from' => 'order',
				'stage_to' => 'sales',
				'action_by' => $cur_uid,
				'action_by_name' => $cur_name,
				'remarks' => "Converted to Sales #{$sales_ref_no}",
				'created_at' => $now
			));
		}

		$this->ci_admin_model->log_admin_action('convert_to_sales', "Order #{$order['order_no']} converted to Sales #{$sales_ref_no}", 'SUCCESS', $cur_uid, $cur_name);
		$this->session->set_flashdata('message', "Order #{$order['order_no']} successfully converted to Sales ({$sales_ref_no}).");
		redirect('manager/orders/view/' . $id);
	}

	public function quick_accept($id = 0)
	{
		$this->if_not_login();
		$id = (int)$id;
		$cur_uid = (int)$this->session->userdata('manager_id');
		$cur_role = (int)$this->session->userdata('manager_role');
		$cur_name = (string)$this->session->userdata('manager_name');

		$is_backoffice = ($cur_role === 1 || $cur_role === 2 || $cur_role === 4 || $cur_uid === 1 || has_section_perm('kam', 'write') || has_section_perm('back_office'));
		if (!$is_backoffice) {
			$this->session->set_flashdata('error_message', 'Access denied.');
			redirect('manager/orders');
			return;
		}

		$order = $this->db->where('id', $id)->get('ci_orders')->row_array();
		if (empty($order)) {
			$this->session->set_flashdata('error_message', 'Order not found.');
			redirect('manager/orders');
			return;
		}

		$now = date('Y-m-d H:i:s');
		$this->db->where('id', $id)->update('ci_orders', array(
			'approval_status' => 'approved',
			'status' => 'Approved',
			'updated_at' => $now
		));

		$this->db->insert('ci_order_history', array(
			'order_id' => $id,
			'action' => 'approved',
			'notes' => 'Order accepted and approved by Back Office.',
			'action_by' => $cur_uid,
			'action_by_name' => $cur_name,
			'created_at' => $now
		));

		$this->ci_admin_model->log_admin_action('approve_order', "Order #{$order['order_no']} accepted", 'SUCCESS', $cur_uid, $cur_name);
		$this->session->set_flashdata('message', "Order #{$order['order_no']} has been accepted and approved.");
		redirect('manager/orders');
	}

	public function edit_pending($id = 0)
	{
		$this->if_not_login();
		$id = (int)$id;
		$cur_uid = (int)$this->session->userdata('manager_id');
		$cur_role = (int)$this->session->userdata('manager_role');
		$cur_name = (string)$this->session->userdata('manager_name');

		$is_backoffice = ($cur_role === 1 || $cur_role === 2 || $cur_role === 4 || $cur_uid === 1 || has_section_perm('kam', 'write') || has_section_perm('back_office'));
		if (!$is_backoffice) {
			$this->session->set_flashdata('error_message', 'Access denied. Order processing is restricted to KAM and Admin.');
			redirect('manager/dashboard');
			return;
		}

		$this->db->select("L.*, COALESCE(NULLIF(E.title, ''), NULLIF(P.vertical_code, ''), 'General') as servicecat, P.db_ref_no, P.lead_ref_no, P.order_request_status, P.order_requested_at, P.order_request_remarks, P.order_requested_by, U.name as requested_by_name", FALSE);
		$this->db->from('ci_leads L');
		$this->db->join('ci_pipeline P', 'L.id = P.lead_id', 'INNER');
		$this->db->join('ci_user U', 'P.order_requested_by = U.id', 'LEFT');
		$this->db->join('ci_terms E', 'L.p_cat = E.id', 'LEFT');
		$this->db->where('L.id', $id);
		$lead = $this->db->get()->row_array();

		if (empty($lead)) {
			$this->session->set_flashdata('error_message', 'Lead record not found.');
			redirect('manager/orders/pending_process');
			return;
		}

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$action_type = $this->input->post('action_type', true);
			$now = date('Y-m-d H:i:s');

			if ($action_type === 'add_remark') {
				$new_remark = trim($this->input->post('new_remark', true));
				if (!empty($new_remark)) {
					$this->db->insert('ci_followup', array(
						'lead_id' => $id,
						'lead_type' => 'lead',
						'user_id' => $cur_uid,
						'followup_date' => date('Y-m-d'),
						'followup_time' => date('H:i:s'),
						'followup_remark' => $new_remark,
						'followup_result' => 'Review Note'
					));
					$this->session->set_flashdata('message', 'Review remark added successfully.');
				}
				redirect('manager/orders/edit_pending/' . $id);
				return;
			}

			if ($action_type === 'reject_order') {
				$reason = trim($this->input->post('reject_reason', true));
				$this->db->where('lead_id', $id)->update('ci_pipeline', array(
					'order_request_status' => 'rejected',
					'is_locked' => 0,
					'updated_at' => $now
				));
				$this->db->insert('ci_followup', array(
					'lead_id' => $id,
					'lead_type' => 'lead',
					'user_id' => $cur_uid,
					'followup_date' => date('Y-m-d'),
					'followup_time' => date('H:i:s'),
					'followup_remark' => "Order Request Rejected. Reason: " . ($reason ?: 'Not specified'),
					'followup_result' => 'Rejected'
				));
				$this->db->insert('ci_pipeline_logs', array(
					'lead_id' => $id,
					'action' => 'reject_order_request',
					'stage_from' => 'lead',
					'stage_to' => 'lead',
					'action_by' => $cur_uid,
					'action_by_name' => $cur_name,
					'remarks' => $reason,
					'created_at' => $now
				));
				$this->ci_admin_model->log_admin_action('reject_order_request', "Rejected order request for Lead #{$id}", 'SUCCESS', $cur_uid, $cur_name);
				$this->session->set_flashdata('message', 'Lead-to-Order request has been rejected. Lead unlocked.');
				redirect('manager/orders/pending_process');
				return;
			}

			if ($action_type === 'approve_order') {
				$vertical_code = $this->input->post('vertical_code', true);
				$service_title = trim($this->input->post('service_title', true));
				$service_scope = trim($this->input->post('service_scope', true));
				$pricing_amount = (float)$this->input->post('pricing_amount', true);
				$mrr = (float)$this->input->post('mrr', true);
				$assigned_dept = $this->input->post('assigned_dept', true);
				$assigned_to = (int)$this->input->post('assigned_to', true);
				$approval_notes = trim($this->input->post('approval_notes', true));

				$order_no = $this->ci_admin_model->generate_next_ref('order');

				$db_ref = !empty($lead['db_ref_no']) ? $lead['db_ref_no'] : '';
				$lead_ref = !empty($lead['lead_ref_no']) ? $lead['lead_ref_no'] : '';

				$order_data = array(
					'order_no' => $order_no,
					'client_id' => $id,
					'client_name' => $lead['customer'],
					'lead_id' => $id,
					'db_ref_no' => $db_ref,
					'lead_ref_no' => $lead_ref,
					'vertical_code' => $vertical_code ?: 'general',
					'service_title' => $service_title ?: ($lead['servicecat'] ?: 'General Service'),
					'service_scope' => $service_scope,
					'pricing_amount' => $pricing_amount,
					'currency' => 'AED',
					'mrr' => $mrr,
					'billing_type' => 'monthly',
					'assigned_to' => ($assigned_to > 0) ? $assigned_to : $cur_uid,
					'assigned_dept' => $assigned_dept ?: 'Back Office',
					'status' => 'Approved',
					'approval_status' => 'approved',
					'approved_by' => $cur_uid,
					'approved_at' => $now,
					'created_by' => !empty($lead['order_requested_by']) ? (int)$lead['order_requested_by'] : $cur_uid,
					'created_at' => $now,
					'updated_at' => $now
				);

				$this->db->insert('ci_orders', $order_data);
				$new_order_id = $this->db->insert_id();

				$this->db->where('lead_id', $id)->update('ci_pipeline', array(
					'order_request_status' => 'processed',
					'is_locked' => 0,
					'updated_at' => $now
				));

				$this->db->insert('ci_order_history', array(
					'order_id' => $new_order_id,
					'action' => 'order_created_from_lead',
					'notes' => "Order #{$order_no} created and approved by Back Office. " . (!empty($approval_notes) ? "Notes: {$approval_notes}" : ''),
					'action_by' => $cur_uid,
					'action_by_name' => $cur_name,
					'created_at' => $now
				));

				$this->db->insert('ci_pipeline_logs', array(
					'lead_id' => $id,
					'action' => 'convert_to_order',
					'stage_from' => 'lead',
					'stage_to' => 'order',
					'action_by' => $cur_uid,
					'action_by_name' => $cur_name,
					'remarks' => "Converted to Order #{$order_no}",
					'created_at' => $now
				));

				$this->ci_admin_model->log_admin_action('convert_to_order', "Lead #{$id} converted to Order #{$order_no}", 'SUCCESS', $cur_uid, $cur_name);
				$this->session->set_flashdata('message', "Order #{$order_no} successfully created and processed.");
				redirect('manager/orders/view/' . $new_order_id);
				return;
			}
		}

		$data = array();
		$data['info'] = array(
			'title' => 'Back Office - Edit Pending Order',
			'page_heading' => 'Process Order Conversion'
		);
		$data['lead'] = $lead;
		$data['remarks_history'] = $this->db->select("F.*, F.followup_remark as remark, F.followup_result as status, CONCAT(F.followup_date, ' ', F.followup_time) as created, U.name as user_name", FALSE)
			->from('ci_followup F')
			->join('ci_user U', 'F.user_id = U.id', 'LEFT')
			->where('F.lead_id', $id)
			->order_by('F.id', 'DESC')
			->get()
			->result_array();
		$data['staff_list'] = $this->db->select('id, name, department')->where('status', 1)->order_by('name', 'ASC')->get('ci_user')->result_array();

		$this->load->view('manager/orders/edit_pending', $data);
	}

	public function my()
	{
		$_GET['scope'] = 'my';
		$this->index();
	}

	public function team()
	{
		$_GET['scope'] = 'team';
		$this->index();
	}

	public function sales()
	{
		$_GET['status'] = 'sales';
		$this->index();
	}

	public function invoices()
	{
		$this->if_not_login();
		$data = array();
		$data['info'] = array(
			'title' => 'Back Office - Invoices',
			'page_heading' => 'View Invoices'
		);

		$search = trim($this->input->get('search'));
		$status = trim($this->input->get('status'));
		$d1 = trim($this->input->get('d1'));
		$d2 = trim($this->input->get('d2'));

		$this->db->select('*')->from('ci_invoices');
		if (!empty($status)) {
			$this->db->where('status', $status);
		}
		if (!empty($search)) {
			$clean = $this->db->escape_str($this->db->escape_like_str($search));
			$this->db->where("(invoice_no LIKE '%$clean%' OR customer_name LIKE '%$clean%')");
		}
		if (!empty($d1) && !empty($d2)) {
			$this->db->where('invoice_date >=', $d1);
			$this->db->where('invoice_date <=', $d2);
		}
		$this->db->order_by('id', 'DESC');
		$data['invoices'] = $this->db->get()->result_array();

		$this->load->view('manager/orders/invoices', $data);
	}

	public function payments()
	{
		$this->if_not_login();
		$data = array();
		$data['info'] = array(
			'title' => 'Back Office - Payments',
			'page_heading' => 'View Payments'
		);

		$search = trim($this->input->get('search'));
		$mode = trim($this->input->get('payment_mode'));
		$d1 = trim($this->input->get('d1'));
		$d2 = trim($this->input->get('d2'));

		$this->db->select('*')->from('ci_payments');
		if (!empty($mode)) {
			$this->db->where('payment_mode', $mode);
		}
		if (!empty($search)) {
			$clean = $this->db->escape_str($this->db->escape_like_str($search));
			$this->db->where("(receipt_no LIKE '%$clean%' OR invoice_no LIKE '%$clean%' OR customer_name LIKE '%$clean%')");
		}
		if (!empty($d1) && !empty($d2)) {
			$this->db->where('payment_date >=', $d1);
			$this->db->where('payment_date <=', $d2);
		}
		$this->db->order_by('id', 'DESC');
		$data['payments'] = $this->db->get()->result_array();

		$this->load->view('manager/orders/payments', $data);
	}

	public function open()
	{
		$_GET['status'] = 'open';
		$this->index();
	}

	public function followups()
	{
		$data = array();
		$cur_uid = (int)$this->session->userdata('manager_id');
		$cur_role = (int)$this->session->userdata('manager_role');
		$is_backoffice = ($cur_role === 1 || $cur_role === 4 || $cur_uid === 1 || has_section_perm('back_office'));
		if (!$is_backoffice) {
			redirect('manager/orders/my');
			return;
		}

		$data['info'] = array(
			'title' => 'Orders Follow-up - Back Office',
			'page_heading' => 'Orders Follow-up'
		);
		$data['reset_url'] = base_url('manager/orders/followups');

		$this->db->select('O.*, U.name as staff_name, A.customer as lead_customer, A.lead_id as lead_code');
		$this->db->from('ci_orders O');
		$this->db->join('ci_user U', 'O.assigned_to = U.id', 'LEFT');
		$this->db->join('ci_leads A', 'O.lead_id = A.id', 'LEFT');
		$this->db->where_not_in('O.status', array('Completed', 'Fulfilled', 'Sales', 'Cancelled', 'Rejected'));

		$client_filter = $this->input->get('client', true);
		if (!empty($client_filter)) {
			$this->db->where('(O.client_name LIKE "%'.$this->db->escape_like_str($client_filter).'%" OR O.order_no LIKE "%'.$this->db->escape_like_str($client_filter).'%" OR O.client_id = '.(int)$client_filter.')');
		}
		$d1 = $this->input->get('d1', true);
		$d2 = $this->input->get('d2', true);
		if (!empty($d1) && !empty($d2)) {
			$d1_clean = date('Y-m-d 00:00:00', strtotime($d1));
			$d2_clean = date('Y-m-d 23:59:59', strtotime($d2));
			$this->db->where("O.created_at >= '{$d1_clean}' AND O.created_at <= '{$d2_clean}'");
		}

		$this->db->order_by('O.id', 'DESC');
		$data['orders_list'] = $this->db->get()->result_array();
		$data['can_edit_orders'] = true;
		$data['status_filter'] = 'followups';
		$data['scope_filter'] = 'backoffice';
		$data['staff_list'] = $this->db->select('id, name, department')->where('status', 1)->order_by('name', 'ASC')->get('ci_user')->result_array();
		$data['clients_list'] = $this->db->select('id, customer, lead_id')->order_by('customer', 'ASC')->get('ci_leads')->result_array();

		$this->load->view('manager/orders/index', $data);
	}

	public function ongoing()
	{
		$_GET['status'] = 'ongoing';
		$this->index();
	}

	public function completed()
	{
		$_GET['status'] = 'completed';
		$this->index();
	}

	public function closed()
	{
		$_GET['status'] = 'closed';
		$this->index();
	}

	public function rejected_lost()
	{
		$_GET['status'] = 'rejected_lost';
		$this->index();
	}

	public function ajax_save_followup()
	{
		$cur_uid = (int)$this->session->userdata('manager_id');
		$cur_name = (string)$this->session->userdata('manager_name');

		$order_id = (int)$this->input->post('order_id');
		$remarks = trim((string)$this->input->post('remarks'));
		$followup_date = trim((string)$this->input->post('followup_date'));
		$followup_time = trim((string)$this->input->post('followup_time'));
		$order_status = trim((string)$this->input->post('order_status'));

		if (!$order_id) {
			header('Content-Type: application/json');
			echo json_encode(array('success' => false, 'message' => 'Invalid order ID'));
			exit;
		}

		$order = $this->db->where('id', $order_id)->get('ci_orders')->row_array();
		if (empty($order)) {
			header('Content-Type: application/json');
			echo json_encode(array('success' => false, 'message' => 'Order not found'));
			exit;
		}

		$now = date('Y-m-d H:i:s');
		$update_data = array(
			'updated_at' => $now
		);

		if (!empty($followup_date)) {
			$update_data['followup_date'] = date('Y-m-d', strtotime($followup_date));
		}
		if (!empty($followup_time)) {
			$update_data['followup_time'] = $followup_time;
		}
		if (!empty($order_status) && in_array($order_status, array('Approved', 'In Progress', 'Under Review', 'Completed', 'Fulfilled', 'Sales', 'Rejected', 'Lost', 'Cancelled'))) {
			$update_data['status'] = $order_status;
		}
		if (!empty($remarks)) {
			$update_data['operational_instructions'] = $remarks;
		}

		$this->db->where('id', $order_id)->update('ci_orders', $update_data);

		if (!empty($remarks)) {
			$this->db->insert('ci_order_history', array(
				'order_id' => $order_id,
				'action' => 'followup',
				'notes' => $remarks . (!empty($followup_date) ? " (Next Follow-up: {$followup_date} {$followup_time})" : ""),
				'action_by' => $cur_uid,
				'action_by_name' => $cur_name,
				'created_at' => $now
			));
		}

		header('Content-Type: application/json');
		echo json_encode(array('success' => true, 'message' => 'Follow-up saved successfully'));
		exit;
	}
}
