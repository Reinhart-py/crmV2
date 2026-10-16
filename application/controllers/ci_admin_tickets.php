<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ci_admin_tickets extends CI_Controller {

	public function __construct() 
	{
		parent::__construct();
		$this->load->library(array('Session','Pagination','User_agent'));
		$this->load->helper(array('form','url','text','date'));
		$this->load->model(array('ci_admin_model','comman_model'));
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
		header("Cache-Control: no-store, no-cache, must-revalidate"); 
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	}

	public function if_not_login($url = '')
	{
		if ($this->session->userdata('manager_id') == '') {
			if ($url != '') {
				redirect($url);
			} else {
				redirect('/manager/login');
			}
		} else {
			$this->ci_admin_model->check_auth_timeout();
			$data = array();
			$data['back_url'] = $this->agent->referrer();
			return $data;
		}
	}

	public function is_support_user()
	{
		$role = (int)$this->session->userdata('manager_role');
		if ($role === 4) return true;
		$role_row = $this->db->get_where('ci_roles', array('id' => $role))->row_array();
		return (!empty($role_row['title']) && strtolower(trim($role_row['title'])) === 'support');
	}

	public function is_admin_or_support()
	{
		$role = (int)$this->session->userdata('manager_role');
		return ($role === 1 || $this->is_support_user());
	}

	public function log_ticket_history($ticket_id, $action, $remarks = '', $old_status = null, $new_status = null, $old_assigned = null, $new_assigned = null)
	{
		$manager_id = (int)$this->session->userdata('manager_id');
		$user_row = $this->db->get_where('ci_user', array('id' => $manager_id))->row_array();
		$user_name = !empty($user_row['name']) ? $user_row['name'] : $this->session->userdata('manager_name');
		$role = (int)$this->session->userdata('manager_role');
		$role_name = ($role == 1) ? 'Admin' : ($this->is_support_user() ? 'Support' : 'Staff');

		$this->db->insert('ci_ticket_history', array(
			'ticket_id' => (int)$ticket_id,
			'action' => $action,
			'old_status' => $old_status,
			'new_status' => $new_status,
			'old_assigned' => $old_assigned,
			'new_assigned' => $new_assigned,
			'remarks' => $remarks,
			'user_id' => $manager_id,
			'user_name' => $user_name ? $user_name : 'User',
			'user_role' => $role_name,
			'created_at' => date('Y-m-d H:i:s')
		));
	}

	public function index()
	{
		$this->if_not_login();
		$role = (int)$this->session->userdata('manager_role');
		$manager_id = (int)$this->session->userdata('manager_id');
		$can_manage = $this->is_admin_or_support();

		$data = array();
		$data['info'] = array(
			'title' => 'Manager - Tickets',
			'page_heading' => 'View Ticket'
		);
		$data['info2'] = $this->if_not_login();
		$data['is_admin'] = ($role === 1);
		$data['is_support'] = $this->is_support_user();
		$data['can_manage_tickets'] = $can_manage;

		$data['users_list'] = $this->db->select('id, name, email')->order_by('name', 'ASC')->get('ci_user')->result_array();
		$data['companies_list'] = $this->ci_admin_model->get_all_companies_dropdown();

		$client_type = $this->input->get('client_type');
		$company = $this->input->get('company');
		$ticket_type = $this->input->get('ticket_type');
		$category = $this->input->get('category');
		$status_filter = $this->input->get('status');
		$priority = $this->input->get('priority');
		$assigned_user = $this->input->get('assigned_user');
		$raised_by = $this->input->get('raised_by');
		$created_by = $this->input->get('created_by');
		if (empty($created_by)) {
			$created_by = $raised_by;
		}
		$department = $this->input->get('department');
		$deadline = $this->input->get('deadline');
		$expected_close_date = $this->input->get('expected_close_date');
		$overdue_filter = $this->input->get('overdue');
		$search_query = $this->input->get('search');
		$d1 = $this->input->get('d1');
		$d2 = $this->input->get('d2');

		$this->db->select('t.*, u.name as creator_name, u.email as creator_email, r.name as resolver_name, l.name as updater_name');
		$this->db->from('ci_tickets t');
		$this->db->join('ci_user u', 'u.id = t.created_by', 'left');
		$this->db->join('ci_user r', 'r.id = t.resolved_by', 'left');
		$this->db->join('ci_user l', 'l.id = t.last_updated_by', 'left');

		if (!$can_manage) {
			$this->db->group_start();
			$this->db->where('t.created_by', $manager_id);
			$this->db->or_where('t.raised_by', $manager_id);
			$this->db->or_where("FIND_IN_SET('$manager_id', t.assigned_users) >", 0);
			$this->db->or_where("FIND_IN_SET('$manager_id', t.tagged_users) >", 0);
			$this->db->or_where('t.assigned_to', $manager_id);
			$this->db->group_end();
		}

		if (!empty($client_type)) {
			$this->db->where('t.client_type', $client_type);
		}
		if (!empty($company)) {
			$this->db->group_start();
			$this->db->where('t.company_name', $company);
			$this->db->or_where('t.customer', $company);
			$this->db->group_end();
		}
		if (!empty($ticket_type)) {
			$this->db->where('t.ticket_type', $ticket_type);
		}
		if (!empty($category)) {
			$this->db->where('t.category', $category);
		}
		if (!empty($status_filter)) {
			$this->db->where('t.status', $status_filter);
		}
		if (!empty($priority)) {
			$this->db->where('t.priority', $priority);
		}
		if (!empty($assigned_user)) {
			$this->db->group_start();
			$this->db->where("FIND_IN_SET('$assigned_user', t.assigned_users) >", 0);
			$this->db->or_where('t.assigned_to', (int)$assigned_user);
			$this->db->group_end();
		}
		if (!empty($created_by)) {
			$this->db->group_start();
			$this->db->where('t.created_by', (int)$created_by);
			$this->db->or_where('t.raised_by', (int)$created_by);
			$this->db->group_end();
		}
		if (!empty($department)) {
			$this->db->where('t.department', $department);
		}
		if (!empty($deadline)) {
			$this->db->where('t.deadline_date', date('Y-m-d', strtotime($deadline)));
		}
		if (!empty($expected_close_date)) {
			$this->db->where('t.expected_close_date', date('Y-m-d', strtotime($expected_close_date)));
		}
		if ($overdue_filter === '1') {
			$today = date('Y-m-d');
			$this->db->where('t.deadline_date IS NOT NULL', null, false);
			$this->db->where('t.deadline_date <', $today);
			$this->db->where_not_in('t.status', array('Resolved', 'Closed'));
		} elseif ($overdue_filter === 'soon') {
			$today = date('Y-m-d');
			$soon = date('Y-m-d', strtotime('+2 days'));
			$this->db->where('t.deadline_date IS NOT NULL', null, false);
			$this->db->where('t.deadline_date >=', $today);
			$this->db->where('t.deadline_date <=', $soon);
			$this->db->where_not_in('t.status', array('Resolved', 'Closed'));
		}
		if (!empty($d1)) {
			$this->db->where('DATE(t.created_at) >=', date('Y-m-d', strtotime($d1)));
		}
		if (!empty($d2)) {
			$this->db->where('DATE(t.created_at) <=', date('Y-m-d', strtotime($d2)));
		}

		if (!empty($search_query)) {
			$this->db->group_start();
			$this->db->like('t.ticket_number', $search_query);
			$this->db->or_like('t.ticket_title', $search_query);
			$this->db->or_like('t.subject', $search_query);
			$this->db->or_like('t.company_name', $search_query);
			$this->db->or_like('t.customer', $search_query);
			$this->db->or_like('t.description', $search_query);
			$this->db->or_like('u.name', $search_query);
			$this->db->group_end();
		}

		$this->db->order_by('t.id', 'DESC');
		$data['tickets'] = $this->db->get()->result_array();

		$today = date('Y-m-d');
		$this->db->select('status, deadline_date, COUNT(*) as cnt');
		$this->db->from('ci_tickets');
		if (!$can_manage) {
			$this->db->group_start();
			$this->db->where('created_by', $manager_id);
			$this->db->or_where('raised_by', $manager_id);
			$this->db->or_where("FIND_IN_SET('$manager_id', assigned_users) >", 0);
			$this->db->or_where("FIND_IN_SET('$manager_id', tagged_users) >", 0);
			$this->db->or_where('assigned_to', $manager_id);
			$this->db->group_end();
		}
		$this->db->group_by('status, (deadline_date IS NOT NULL AND deadline_date < "'.$today.'")');
		$counts_res = $this->db->get()->result_array();

		$data['count_all'] = 0;
		$data['count_open'] = 0;
		$data['count_assigned'] = 0;
		$data['count_in_progress'] = 0;
		$data['count_pending'] = 0;
		$data['count_on_hold'] = 0;
		$data['count_resolved'] = 0;
		$data['count_closed'] = 0;
		$data['count_overdue'] = 0;

		foreach ($data['tickets'] as $tk) {
			$st = strtolower(trim($tk['status']));
			$data['count_all']++;
			if ($st === 'open') $data['count_open']++;
			elseif ($st === 'assigned') $data['count_assigned']++;
			elseif ($st === 'in progress') $data['count_in_progress']++;
			elseif ($st === 'pending') $data['count_pending']++;
			elseif ($st === 'on hold') $data['count_on_hold']++;
			elseif ($st === 'resolved') $data['count_resolved']++;
			elseif ($st === 'closed') $data['count_closed']++;

			if (!empty($tk['deadline_date']) && $tk['deadline_date'] < $today && !in_array($st, array('resolved', 'closed'))) {
				$data['count_overdue']++;
			}
		}

		$this->load->view('manager/tickets/index', $data);
	}

	public function add()
	{
		$this->if_not_login();
		$manager_id = (int)$this->session->userdata('manager_id');

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$client_type = trim($this->input->post('client_type'));
			if (empty($client_type)) $client_type = 'Onboarded';

			$company_name = trim($this->input->post('company_name'));
			if (empty($company_name)) {
				$company_name = trim($this->input->post('new_company_name'));
			}

			$department = trim($this->input->post('department'));
			$ticket_type = trim($this->input->post('ticket_type'));
			$category = trim($this->input->post('category'));
			$priority = trim($this->input->post('priority'));
			if (empty($priority)) $priority = 'Medium';

			$ticket_title = trim($this->input->post('ticket_title'));
			if (empty($ticket_title)) {
				$ticket_title = trim($this->input->post('subject'));
			}
			$description = trim($this->input->post('description'));
			$internal_notes = trim($this->input->post('internal_notes'));

			$deadline_raw = trim($this->input->post('deadline_date'));
			$deadline_date = !empty($deadline_raw) ? date('Y-m-d', strtotime($deadline_raw)) : null;

			$expected_close_raw = trim($this->input->post('expected_close_date'));
			$expected_close_date = !empty($expected_close_raw) ? date('Y-m-d', strtotime($expected_close_raw)) : null;

			$poc = trim($this->input->post('poc'));
			$contact1 = trim($this->input->post('contact1'));
			$alt_contact = trim($this->input->post('alt_contact'));
			$email = trim($this->input->post('email'));
			$city = trim($this->input->post('city'));
			$lead_source = trim($this->input->post('lead_source'));

			$followup_date_raw = trim($this->input->post('followup_date'));
			$followup_date = !empty($followup_date_raw) ? date('Y-m-d', strtotime($followup_date_raw)) : null;
			$followup_time = trim($this->input->post('followup_time'));

			$assigned_arr = $this->input->post('assigned_users');
			$assigned_users_str = '';
			if (!empty($assigned_arr) && is_array($assigned_arr)) {
				$assigned_users_str = implode(',', array_filter(array_map('intval', $assigned_arr)));
			}

			$tagged_arr = $this->input->post('tagged_users');
			$tagged_users_str = '';
			if (!empty($tagged_arr) && is_array($tagged_arr)) {
				$tagged_users_str = implode(',', array_filter(array_map('intval', $tagged_arr)));
			}

			if (empty($ticket_title) || empty($description)) {
				$this->session->set_flashdata('error_message', 'Ticket Title and Description are required.');
				redirect('manager/tickets/add');
				return;
			}

			$attachment_file = '';
			if (!empty($_FILES['attachment']['name'])) {
				$upload_dir = FCPATH . 'assets/uploads/tickets/';
				if (!is_dir($upload_dir)) {
					@mkdir($upload_dir, 0777, true);
				}
				$config['upload_path'] = $upload_dir;
				$config['allowed_types'] = 'pdf|doc|docx|xls|xlsx|csv|jpg|jpeg|png|txt|zip';
				$config['max_size'] = 15360;
				$config['encrypt_name'] = TRUE;
				$this->load->library('upload', $config);
				if ($this->upload->do_upload('attachment')) {
					$upload_data = $this->upload->data();
					$attachment_file = $upload_data['file_name'];
				}
			}

			$total_t = $this->db->count_all('ci_tickets') + 1;
			$ticket_number = 'TKT-' . date('Ym') . '-' . sprintf('%04d', $total_t);
			$check = $this->db->get_where('ci_tickets', array('ticket_number' => $ticket_number))->row_array();
			if (!empty($check)) {
				$ticket_number = 'TKT-' . date('Ym') . '-' . mt_rand(1000, 9999);
			}

			$initial_status = !empty($assigned_users_str) ? 'Assigned' : 'Open';

			$now = date('Y-m-d H:i:s');
			$insert_data = array(
				'ticket_number' => $ticket_number,
				'client_type' => $client_type,
				'company_name' => $company_name,
				'customer' => $company_name,
				'poc' => $poc,
				'contact1' => $contact1,
				'alt_contact' => $alt_contact,
				'email' => $email,
				'city' => $city,
				'lead_source' => !empty($lead_source) ? $lead_source : 'Internal Request',
				'department' => $department,
				'ticket_type' => !empty($ticket_type) ? $ticket_type : 'Technical Support',
				'category' => !empty($category) ? $category : 'General',
				'priority' => $priority,
				'ticket_title' => $ticket_title,
				'subject' => $ticket_title,
				'description' => $description,
				'internal_notes' => $internal_notes,
				'attachment' => $attachment_file,
				'status' => $initial_status,
				'assigned_users' => $assigned_users_str,
				'assigned_to' => !empty($assigned_arr[0]) ? (int)$assigned_arr[0] : 0,
				'tagged_users' => $tagged_users_str,
				'raised_by' => $manager_id,
				'created_by' => $manager_id,
				'created_date' => $now,
				'created_at' => $now,
				'last_updated_date' => $now,
				'updated_at' => $now,
				'last_updated_by' => $manager_id,
				'deadline_date' => $deadline_date,
				'expected_close_date' => $expected_close_date,
				'followup_date' => $followup_date,
				'followup_time' => $followup_time
			);

			$this->db->insert('ci_tickets', $insert_data);
			$new_ticket_id = $this->db->insert_id();

			$this->log_ticket_history($new_ticket_id, 'Ticket Created', 'Ticket raised with initial status ' . $initial_status, null, $initial_status, null, $assigned_users_str);

			$u_row = $this->db->get_where('ci_user', array('id' => $manager_id))->row_array();
			$user_name = !empty($u_row['name']) ? $u_row['name'] : 'User';
			$this->db->insert('ci_ticket_messages', array(
				'ticket_id' => $new_ticket_id,
				'sender_id' => $manager_id,
				'sender_role' => (int)$this->session->userdata('manager_role'),
				'sender_name' => $user_name,
				'message' => $description,
				'created_at' => $now
			));

			$this->session->set_flashdata('message', 'Ticket #' . $ticket_number . ' has been successfully submitted!');
			redirect('manager/tickets/view/' . $new_ticket_id);
			return;
		}

		$data = array();
		$data['info'] = array(
			'title' => 'Manager - Tickets',
			'page_heading' => 'Raise Ticket'
		);
		$data['info2'] = $this->if_not_login();
		$data['users_list'] = $this->db->select('id, name, email')->order_by('name', 'ASC')->get('ci_user')->result_array();
		$data['companies_list'] = $this->ci_admin_model->get_all_companies_dropdown();

		$this->load->view('manager/tickets/add', $data);
	}

	public function edit($id = '')
	{
		$this->if_not_login();
		$manager_id = (int)$this->session->userdata('manager_id');
		$can_manage = $this->is_admin_or_support();

		if (empty($id)) {
			redirect('manager/tickets');
			return;
		}

		$ticket = $this->db->get_where('ci_tickets', array('id' => (int)$id))->row_array();
		if (empty($ticket)) {
			$this->session->set_flashdata('error_message', 'Ticket not found.');
			redirect('manager/tickets');
			return;
		}

		if (!$can_manage && $ticket['created_by'] != $manager_id && $ticket['raised_by'] != $manager_id) {
			show_error('Access Denied: You do not have permission to edit this ticket.', 403);
			return;
		}

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$client_type = trim($this->input->post('client_type'));
			$company_name = trim($this->input->post('company_name'));
			if (empty($company_name)) {
				$company_name = trim($this->input->post('new_company_name'));
			}
			$department = trim($this->input->post('department'));
			$ticket_type = trim($this->input->post('ticket_type'));
			$category = trim($this->input->post('category'));
			$priority = trim($this->input->post('priority'));
			$ticket_title = trim($this->input->post('ticket_title'));
			$description = trim($this->input->post('description'));
			$internal_notes = trim($this->input->post('internal_notes'));

			$deadline_raw = trim($this->input->post('deadline_date'));
			$deadline_date = !empty($deadline_raw) ? date('Y-m-d', strtotime($deadline_raw)) : null;

			$expected_close_raw = trim($this->input->post('expected_close_date'));
			$expected_close_date = !empty($expected_close_raw) ? date('Y-m-d', strtotime($expected_close_raw)) : null;

			$poc = trim($this->input->post('poc'));
			$contact1 = trim($this->input->post('contact1'));
			$alt_contact = trim($this->input->post('alt_contact'));
			$email = trim($this->input->post('email'));
			$city = trim($this->input->post('city'));
			$lead_source = trim($this->input->post('lead_source'));

			$followup_date_raw = trim($this->input->post('followup_date'));
			$followup_date = !empty($followup_date_raw) ? date('Y-m-d', strtotime($followup_date_raw)) : null;
			$followup_time = trim($this->input->post('followup_time'));

			$assigned_arr = $this->input->post('assigned_users');
			$assigned_users_str = '';
			if (!empty($assigned_arr) && is_array($assigned_arr)) {
				$assigned_users_str = implode(',', array_filter(array_map('intval', $assigned_arr)));
			}

			$tagged_arr = $this->input->post('tagged_users');
			$tagged_users_str = '';
			if (!empty($tagged_arr) && is_array($tagged_arr)) {
				$tagged_users_str = implode(',', array_filter(array_map('intval', $tagged_arr)));
			}

			$attachment_file = $ticket['attachment'];
			if (!empty($_FILES['attachment']['name'])) {
				$upload_dir = FCPATH . 'assets/uploads/tickets/';
				if (!is_dir($upload_dir)) {
					@mkdir($upload_dir, 0777, true);
				}
				$config['upload_path'] = $upload_dir;
				$config['allowed_types'] = 'pdf|doc|docx|xls|xlsx|csv|jpg|jpeg|png|txt|zip';
				$config['max_size'] = 15360;
				$config['encrypt_name'] = TRUE;
				$this->load->library('upload', $config);
				if ($this->upload->do_upload('attachment')) {
					$upload_data = $this->upload->data();
					$attachment_file = $upload_data['file_name'];
				}
			}

			$now = date('Y-m-d H:i:s');
			$update_data = array(
				'client_type' => $client_type,
				'company_name' => $company_name,
				'customer' => $company_name,
				'poc' => $poc,
				'contact1' => $contact1,
				'alt_contact' => $alt_contact,
				'email' => $email,
				'city' => $city,
				'lead_source' => !empty($lead_source) ? $lead_source : 'Internal Request',
				'department' => $department,
				'ticket_type' => $ticket_type,
				'category' => $category,
				'priority' => $priority,
				'ticket_title' => $ticket_title,
				'subject' => $ticket_title,
				'description' => $description,
				'internal_notes' => $internal_notes,
				'attachment' => $attachment_file,
				'assigned_users' => $assigned_users_str,
				'assigned_to' => !empty($assigned_arr[0]) ? (int)$assigned_arr[0] : 0,
				'tagged_users' => $tagged_users_str,
				'deadline_date' => $deadline_date,
				'expected_close_date' => $expected_close_date,
				'followup_date' => $followup_date,
				'followup_time' => $followup_time,
				'last_updated_date' => $now,
				'updated_at' => $now,
				'last_updated_by' => $manager_id
			);

			$this->db->where('id', (int)$id)->update('ci_tickets', $update_data);

			if ($ticket['assigned_users'] !== $assigned_users_str) {
				$this->log_ticket_history($id, 'Reassigned', 'Assigned users updated', null, null, $ticket['assigned_users'], $assigned_users_str);
			} else {
				$this->log_ticket_history($id, 'Ticket Details Updated', 'Updated ticket fields and deadlines');
			}

			$this->session->set_flashdata('message', 'Ticket #' . $ticket['ticket_number'] . ' updated successfully.');
			redirect('manager/tickets/view/' . (int)$id);
			return;
		}

		$data = array();
		$data['info'] = array(
			'title' => 'Manager - Edit Ticket',
			'page_heading' => 'Edit Ticket #' . $ticket['ticket_number']
		);
		$data['info2'] = $this->if_not_login();
		$data['ticket'] = $ticket;
		$data['users_list'] = $this->db->select('id, name, email')->order_by('name', 'ASC')->get('ci_user')->result_array();
		$data['companies_list'] = $this->ci_admin_model->get_all_companies_dropdown();

		$this->load->view('manager/tickets/edit', $data);
	}

	public function view($id = '')
	{
		$this->if_not_login();
		$role = (int)$this->session->userdata('manager_role');
		$manager_id = (int)$this->session->userdata('manager_id');
		$can_manage = $this->is_admin_or_support();

		if (empty($id)) {
			redirect('manager/tickets');
			return;
		}

		$this->db->select('t.*, u.name as creator_name, u.email as creator_email, u.mobile as creator_mobile, r.name as resolver_name, rev.name as reviewer_name, l.name as updater_name');
		$this->db->from('ci_tickets t');
		$this->db->join('ci_user u', 'u.id = t.created_by', 'left');
		$this->db->join('ci_user r', 'r.id = t.resolved_by', 'left');
		$this->db->join('ci_user rev', 'rev.id = t.resolution_reviewed_by', 'left');
		$this->db->join('ci_user l', 'l.id = t.last_updated_by', 'left');

		if (is_numeric($id)) {
			$this->db->where('t.id', (int)$id);
		} else {
			$this->db->where('t.ticket_number', $id);
		}

		$ticket = $this->db->get()->row_array();

		if (empty($ticket)) {
			$this->session->set_flashdata('error_message', 'The requested ticket could not be found.');
			redirect('manager/tickets');
			return;
		}

		$assigned_ids = !empty($ticket['assigned_users']) ? explode(',', $ticket['assigned_users']) : array();
		$tagged_ids = !empty($ticket['tagged_users']) ? explode(',', $ticket['tagged_users']) : array();
		$is_assigned = in_array((string)$manager_id, $assigned_ids) || ($ticket['assigned_to'] == $manager_id);
		$is_tagged = in_array((string)$manager_id, $tagged_ids);
		$is_creator = ($ticket['created_by'] == $manager_id || $ticket['raised_by'] == $manager_id);

		if (!$can_manage && !$is_creator && !$is_assigned && !$is_tagged) {
			show_error('Access Denied: You do not have permission to view this ticket.', 403);
			return;
		}

		$assigned_user_names = array();
		if (!empty($assigned_ids)) {
			$users_q = $this->db->select('id, name, email')->where_in('id', $assigned_ids)->get('ci_user')->result_array();
			foreach ($users_q as $uq) {
				$assigned_user_names[] = $uq;
			}
		}

		$tagged_user_names = array();
		if (!empty($tagged_ids)) {
			$tag_q = $this->db->select('id, name, email')->where_in('id', $tagged_ids)->get('ci_user')->result_array();
			foreach ($tag_q as $tq) {
				$tagged_user_names[] = $tq;
			}
		}

		$this->db->select('f.*, u.name as assigned_name, c.name as creator_name');
		$this->db->from('ci_ticket_followups f');
		$this->db->join('ci_user u', 'u.id = f.assigned_user_id', 'left');
		$this->db->join('ci_user c', 'c.id = f.created_by', 'left');
		$this->db->where('f.ticket_id', $ticket['id']);
		$this->db->order_by('f.id', 'DESC');
		$followups = $this->db->get()->result_array();

		$history = $this->db->where('ticket_id', $ticket['id'])->order_by('id', 'DESC')->get('ci_ticket_history')->result_array();

		$messages = $this->db->where('ticket_id', $ticket['id'])->order_by('id', 'ASC')->get('ci_ticket_messages')->result_array();

		$data = array();
		$data['info'] = array(
			'title' => 'Help Desk - Ticket #' . $ticket['ticket_number'],
			'page_heading' => 'Ticket #' . $ticket['ticket_number']
		);
		$data['info2'] = $this->if_not_login();
		$data['ticket'] = $ticket;
		$data['assigned_user_names'] = $assigned_user_names;
		$data['tagged_user_names'] = $tagged_user_names;
		$data['followups'] = $followups;
		$data['history'] = $history;
		$data['messages'] = $messages;
		$data['is_admin'] = ($role === 1);
		$data['is_support'] = $this->is_support_user();
		$data['can_manage_tickets'] = $can_manage;
		$data['is_assigned'] = $is_assigned;
		$data['is_creator'] = $is_creator;
		$data['users_list'] = $this->db->select('id, name, email')->order_by('name', 'ASC')->get('ci_user')->result_array();

		$this->load->view('manager/tickets/view', $data);
	}

	public function add_followup($ticket_id = '')
	{
		$this->if_not_login();
		$manager_id = (int)$this->session->userdata('manager_id');
		$ticket_id = (int)$this->input->post('ticket_id') ? (int)$this->input->post('ticket_id') : (int)$ticket_id;

		$ticket = $this->db->get_where('ci_tickets', array('id' => $ticket_id))->row_array();
		if (empty($ticket)) {
			$this->session->set_flashdata('error_message', 'Ticket not found.');
			redirect('manager/tickets');
			return;
		}

		$followup_date_raw = trim($this->input->post('followup_date'));
		$followup_date = !empty($followup_date_raw) ? date('Y-m-d', strtotime($followup_date_raw)) : date('Y-m-d');

		$next_followup_date_raw = trim($this->input->post('next_followup_date'));
		$next_followup_date = !empty($next_followup_date_raw) ? date('Y-m-d', strtotime($next_followup_date_raw)) : null;

		$assigned_user_id = (int)$this->input->post('assigned_user_id');
		$remarks = trim($this->input->post('remarks'));
		$new_status = trim($this->input->post('status'));
		$next_action = trim($this->input->post('next_action'));

		if (empty($remarks)) {
			$this->session->set_flashdata('error_message', 'Follow-up remarks are required.');
			redirect('manager/tickets/view/' . $ticket_id);
			return;
		}

		$now = date('Y-m-d H:i:s');
		$insert_followup = array(
			'ticket_id' => $ticket_id,
			'followup_date' => $followup_date,
			'next_followup_date' => $next_followup_date,
			'assigned_user_id' => $assigned_user_id ? $assigned_user_id : $manager_id,
			'remarks' => $remarks,
			'status' => !empty($new_status) ? $new_status : $ticket['status'],
			'next_action' => $next_action,
			'created_by' => $manager_id,
			'created_at' => $now
		);

		$this->db->insert('ci_ticket_followups', $insert_followup);

		$update_ticket = array(
			'followup_date' => $next_followup_date ? $next_followup_date : $followup_date,
			'last_updated_date' => $now,
			'updated_at' => $now,
			'last_updated_by' => $manager_id
		);

		if (!empty($new_status) && $new_status !== $ticket['status']) {
			if ($new_status === 'Closed' && !$this->is_admin_or_support()) {
			} else {
				$update_ticket['status'] = $new_status;
			}
		}

		$this->db->where('id', $ticket_id)->update('ci_tickets', $update_ticket);

		$this->log_ticket_history($ticket_id, 'Follow-up Added', $remarks, $ticket['status'], isset($update_ticket['status']) ? $update_ticket['status'] : $ticket['status']);

		$this->session->set_flashdata('message', 'Follow-up entry added successfully.');
		redirect('manager/tickets/view/' . $ticket_id);
	}

	public function update_status()
	{
		$this->if_not_login();
		$role = (int)$this->session->userdata('manager_role');
		$manager_id = (int)$this->session->userdata('manager_id');
		$can_close = $this->is_admin_or_support();

		$ticket_id = (int)$this->input->post('ticket_id');
		$status = trim($this->input->post('status'));
		$remarks = trim($this->input->post('remarks'));
		$resolution_remarks = trim($this->input->post('resolution_remarks'));
		$confirm_review = (int)$this->input->post('confirm_review');

		$ticket = $this->db->get_where('ci_tickets', array('id' => $ticket_id))->row_array();
		if (empty($ticket)) {
			$this->session->set_flashdata('error_message', 'Ticket not found.');
			redirect('manager/tickets');
			return;
		}

		$allowed_statuses = array('Open', 'Assigned', 'In Progress', 'Pending', 'On Hold', 'Resolved', 'Closed');
		if (!in_array($status, $allowed_statuses)) {
			$this->session->set_flashdata('error_message', 'Invalid status requested.');
			redirect('manager/tickets/view/' . $ticket_id);
			return;
		}

		if ($status === 'Closed') {
			if (!$can_close) {
				$this->session->set_flashdata('error_message', 'Permission Denied: Only users with the Support role or Administrators can review and close tickets.');
				redirect('manager/tickets/view/' . $ticket_id);
				return;
			}

			if ($confirm_review !== 1) {
				$this->session->set_flashdata('error_message', 'Resolution review required: You must confirm resolution review before closing this ticket.');
				redirect('manager/tickets/view/' . $ticket_id);
				return;
			}
		}

		$now = date('Y-m-d H:i:s');
		$update_data = array(
			'status' => $status,
			'last_updated_date' => $now,
			'updated_at' => $now,
			'last_updated_by' => $manager_id
		);

		if (!empty($remarks)) {
			$update_data['remark'] = $remarks;
		}

		if ($status === 'Resolved' || $status === 'Closed') {
			$update_data['resolved_by'] = $manager_id;
			if (empty($ticket['resolved_at']) || $ticket['resolved_at'] == '0000-00-00 00:00:00') {
				$update_data['resolved_at'] = $now;
			}
			if (!empty($resolution_remarks)) {
				$update_data['resolution_remarks'] = $resolution_remarks;
			}
		}

		if ($status === 'Closed') {
			$update_data['resolution_reviewed_by'] = $manager_id;
			$update_data['resolution_confirmed_at'] = $now;
			if (!empty($resolution_remarks)) {
				$update_data['resolution_remarks'] = $resolution_remarks;
			}
		}

		$this->db->where('id', $ticket_id)->update('ci_tickets', $update_data);

		$action_title = ($status === 'Closed') ? 'Resolution Confirmed & Ticket Closed' : 'Status Changed to ' . $status;
		$log_comment = !empty($resolution_remarks) ? $resolution_remarks : (!empty($remarks) ? $remarks : 'Status updated');
		$this->log_ticket_history($ticket_id, $action_title, $log_comment, $ticket['status'], $status);

		$u_row = $this->db->get_where('ci_user', array('id' => $manager_id))->row_array();
		$actor_name = !empty($u_row['name']) ? $u_row['name'] : 'User';
		$actor_display = $actor_name . ($can_close ? ' (Support Review)' : '');
		$msg_text = 'Status updated to **' . $status . '**.';
		if (!empty($remarks)) $msg_text .= "\n" . $remarks;
		if (!empty($resolution_remarks)) $msg_text .= "\n*Resolution Details: " . $resolution_remarks . '*';

		$this->db->insert('ci_ticket_messages', array(
			'ticket_id' => $ticket_id,
			'sender_id' => $manager_id,
			'sender_role' => $role,
			'sender_name' => $actor_display,
			'message' => $msg_text,
			'created_at' => $now
		));

		$this->session->set_flashdata('message', 'Ticket #' . $ticket['ticket_number'] . ' status updated to ' . $status . '.');
		redirect('manager/tickets/view/' . $ticket_id);
	}

	public function respond()
	{
		$this->if_not_login();
		$role = (int)$this->session->userdata('manager_role');
		$manager_id = (int)$this->session->userdata('manager_id');
		$can_manage = $this->is_admin_or_support();

		$ticket_id = (int)$this->input->post('ticket_id');
		$response_msg = trim($this->input->post('response'));
		$new_status = trim($this->input->post('status'));

		$ticket = $this->db->get_where('ci_tickets', array('id' => $ticket_id))->row_array();
		if (empty($ticket)) {
			$this->session->set_flashdata('error_message', 'Ticket not found.');
			redirect('manager/tickets');
			return;
		}

		if (empty($response_msg)) {
			$this->session->set_flashdata('error_message', 'Message cannot be empty.');
			redirect('manager/tickets/view/' . $ticket_id);
			return;
		}

		$current_user = $this->db->get_where('ci_user', array('id' => $manager_id))->row_array();
		$sender_name = !empty($current_user['name']) ? $current_user['name'] : 'User';
		$display_sender = $sender_name . ($can_manage ? ' (Support)' : '');

		$now = date('Y-m-d H:i:s');
		$this->db->insert('ci_ticket_messages', array(
			'ticket_id' => $ticket_id,
			'sender_id' => $manager_id,
			'sender_role' => $role,
			'sender_name' => $display_sender,
			'message' => $response_msg,
			'created_at' => $now
		));

		$update_data = array(
			'last_updated_date' => $now,
			'updated_at' => $now,
			'last_updated_by' => $manager_id
		);

		if (!empty($new_status)) {
			if ($new_status === 'Closed' && !$can_manage) {
			} else {
				$update_data['status'] = $new_status;
			}
		}

		$this->db->where('id', $ticket_id)->update('ci_tickets', $update_data);
		$this->log_ticket_history($ticket_id, 'Message Posted', substr($response_msg, 0, 200));

		$this->session->set_flashdata('message', 'Message sent successfully.');
		redirect('manager/tickets/view/' . $ticket_id);
	}

	public function status($id = '', $target_status = '')
	{
		$this->if_not_login();
		$role = (int)$this->session->userdata('manager_role');
		$manager_id = (int)$this->session->userdata('manager_id');
		$can_close = $this->is_admin_or_support();

		if (empty($id)) {
			redirect('manager/tickets');
			return;
		}

		$ticket = $this->db->get_where('ci_tickets', array('id' => (int)$id))->row_array();
		if (empty($ticket)) {
			$this->session->set_flashdata('error_message', 'Ticket not found.');
			redirect('manager/tickets');
			return;
		}

		$status = urldecode($target_status);
		$status = trim(str_replace('_', ' ', $status));

		if (strtolower($status) === 'in progress') $status = 'In Progress';
		elseif (strtolower($status) === 'on hold') $status = 'On Hold';
		else $status = ucfirst(strtolower($status));

		if ($status === 'Closed') {
			if (!$can_close) {
				$this->session->set_flashdata('error_message', 'Only authorized Support staff can review and close tickets.');
				redirect('manager/tickets/view/' . (int)$id);
				return;
			}
			$this->session->set_flashdata('error_message', 'Please review the resolution and confirm closure below.');
			redirect('manager/tickets/view/' . (int)$id . '#review_close_section');
			return;
		}

		$now = date('Y-m-d H:i:s');
		$update_data = array(
			'status' => $status,
			'last_updated_date' => $now,
			'updated_at' => $now,
			'last_updated_by' => $manager_id
		);

		if ($status === 'Resolved') {
			$update_data['resolved_by'] = $manager_id;
			$update_data['resolved_at'] = $now;
		}

		$this->db->where('id', (int)$id)->update('ci_tickets', $update_data);
		$this->log_ticket_history($id, 'Status Changed to ' . $status, 'Quick status action', $ticket['status'], $status);

		$this->session->set_flashdata('message', 'Ticket #' . $ticket['ticket_number'] . ' status updated to ' . $status . '.');
		redirect('manager/tickets');
	}

	public function followups()
	{
		$this->if_not_login();
		$role = (int)$this->session->userdata('manager_role');
		$manager_id = (int)$this->session->userdata('manager_id');
		$can_manage = $this->is_admin_or_support();

		$data = array();
		$data['info'] = array(
			'title' => 'Manager - Open Tickets',
			'page_heading' => 'Open Tickets'
		);
		$data['info2'] = $this->if_not_login();
		$data['is_admin'] = ($role === 1);
		$data['is_support'] = $this->is_support_user();
		$data['can_manage_tickets'] = $can_manage;

		$data['users_list'] = $this->db->select('id, name, email')->order_by('name', 'ASC')->get('ci_user')->result_array();
		$data['companies_list'] = $this->ci_admin_model->get_all_companies_dropdown();

		$client_type = $this->input->get('client_type');
		$company = $this->input->get('company');
		$ticket_type = $this->input->get('ticket_type');
		$category = $this->input->get('category');
		$priority = $this->input->get('priority');
		$assigned_user = $this->input->get('assigned_user');
		$created_by = $this->input->get('created_by');
		if (empty($created_by)) {
			$created_by = $this->input->get('raised_by');
		}
		$department = $this->input->get('department');
		$search = $this->input->get('search');
		$d1 = $this->input->get('d1');
		$d2 = $this->input->get('d2');

		$this->db->select('t.*, u.name as creator_name, u.email as creator_email, r.name as resolver_name, l.name as updater_name');
		$this->db->from('ci_tickets t');
		$this->db->join('ci_user u', 'u.id = t.created_by', 'left');
		$this->db->join('ci_user r', 'r.id = t.resolved_by', 'left');
		$this->db->join('ci_user l', 'l.id = t.last_updated_by', 'left');

		$this->db->where('t.status', 'Open');

		if (!$can_manage) {
			$this->db->group_start();
			$this->db->where('t.created_by', $manager_id);
			$this->db->or_where('t.raised_by', $manager_id);
			$this->db->or_where("FIND_IN_SET('$manager_id', t.assigned_users) >", 0);
			$this->db->or_where("FIND_IN_SET('$manager_id', t.tagged_users) >", 0);
			$this->db->or_where('t.assigned_to', $manager_id);
			$this->db->group_end();
		}

		if (!empty($client_type)) {
			$this->db->where('t.client_type', $client_type);
		}
		if (!empty($company)) {
			$this->db->group_start();
			$this->db->where('t.company_name', $company);
			$this->db->or_where('t.customer', $company);
			$this->db->group_end();
		}
		if (!empty($ticket_type)) {
			$this->db->where('t.ticket_type', $ticket_type);
		}
		if (!empty($category)) {
			$this->db->where('t.category', $category);
		}
		if (!empty($priority)) {
			$this->db->where('t.priority', $priority);
		}
		if (!empty($department)) {
			$this->db->where('t.department', $department);
		}
		if (!empty($assigned_user)) {
			$this->db->group_start();
			$this->db->where("FIND_IN_SET('$assigned_user', t.assigned_users) >", 0);
			$this->db->or_where('t.assigned_to', $assigned_user);
			$this->db->group_end();
		}
		if (!empty($created_by)) {
			$this->db->group_start();
			$this->db->where('t.created_by', (int)$created_by);
			$this->db->or_where('t.raised_by', (int)$created_by);
			$this->db->group_end();
		}
		if (!empty($d1)) {
			$this->db->where('t.created_date >=', date('Y-m-d 00:00:00', strtotime($d1)));
		}
		if (!empty($d2)) {
			$this->db->where('t.created_date <=', date('Y-m-d 23:59:59', strtotime($d2)));
		}
		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('t.ticket_number', $search);
			$this->db->or_like('t.ticket_title', $search);
			$this->db->or_like('t.subject', $search);
			$this->db->or_like('t.company_name', $search);
			$this->db->or_like('t.customer', $search);
			$this->db->or_like('t.poc', $search);
			$this->db->or_like('t.contact1', $search);
			$this->db->or_like('t.email', $search);
			$this->db->group_end();
		}

		$this->db->order_by('t.id', 'DESC');
		$data['tickets'] = $this->db->get()->result_array();
		$data['list'] = $data['tickets'];

		$this->load->view('manager/tickets/followups', $data);
	}

	public function delete($id = '')
	{
		$this->if_not_login();
		$role = (int)$this->session->userdata('manager_role');
		if ($role !== 1) {
			show_error('Access Denied: Only Administrators are authorized to delete tickets.', 403);
			return;
		}

		if (empty($id)) {
			redirect('manager/tickets');
			return;
		}

		$ticket = $this->db->get_where('ci_tickets', array('id' => (int)$id))->row_array();
		if (!empty($ticket)) {
			$this->db->where('id', (int)$id)->delete('ci_tickets');
			$this->db->where('ticket_id', (int)$id)->delete('ci_ticket_messages');
			$this->db->where('ticket_id', (int)$id)->delete('ci_ticket_history');
			$this->db->where('ticket_id', (int)$id)->delete('ci_ticket_followups');
			$this->session->set_flashdata('message', 'Ticket #' . $ticket['ticket_number'] . ' and all related records have been deleted.');
		}

		redirect('manager/tickets');
	}
}
