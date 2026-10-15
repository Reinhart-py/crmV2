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
			$data = array();
			$data['back_url'] = $this->agent->referrer();
			return $data;
		}
	}

	/**
	 * Helper: Check if current user has Support role
	 */
	public function is_support_user()
	{
		$role = (int)$this->session->userdata('manager_role');
		if ($role === 4) return true;
		$role_row = $this->db->get_where('ci_roles', array('id' => $role))->row_array();
		return (!empty($role_row['title']) && strtolower(trim($role_row['title'])) === 'support');
	}

	/**
	 * Helper: Check if current user is Admin or Support
	 */
	public function is_admin_or_support()
	{
		$role = (int)$this->session->userdata('manager_role');
		return ($role === 1 || $this->is_support_user());
	}

	/**
	 * List all tickets (Admin and Support see all, others see their own)
	 */
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
		$data['st_list'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_status');
		$data['pc_list'] = $this->comman_model->getAll(array('parent_id' => 0, 'status' => 1, 'cat_type' => 'p_cat'), '', 'ci_terms');
		$data['lc_list'] = $this->comman_model->getAll(array('cat_type' => 'lead_cat', 'parent_id' => 0), '', 'ci_terms');

		$status_filter = $this->input->get('status');
		$search_query = $this->input->get('search');

		// Query tickets with creator info
		$this->db->select('t.*, u.name as creator_name, u.email as creator_email, r.name as resolver_name');
		$this->db->from('ci_tickets t');
		$this->db->join('ci_user u', 'u.id = t.created_by', 'left');
		$this->db->join('ci_user r', 'r.id = t.resolved_by', 'left');

		if (!$can_manage) {
			// Regular managers/users only see their own tickets
			$this->db->where('t.created_by', $manager_id);
		}

		if (!empty($status_filter)) {
			$this->db->where('t.status', $status_filter);
		}

		if (!empty($search_query)) {
			$this->db->group_start();
			$this->db->like('t.ticket_number', $search_query);
			$this->db->or_like('t.subject', $search_query);
			$this->db->or_like('t.description', $search_query);
			$this->db->or_like('u.name', $search_query);
			$this->db->group_end();
		}

		$this->db->order_by('t.id', 'DESC');
		$data['tickets'] = $this->db->get()->result_array();

		// Count summary statistics
		$this->db->select('status, COUNT(*) as cnt');
		$this->db->from('ci_tickets');
		if (!$can_manage) {
			$this->db->where('created_by', $manager_id);
		}
		$this->db->group_by('status');
		$counts_res = $this->db->get()->result_array();

		$data['count_open'] = 0;
		$data['count_in_progress'] = 0;
		$data['count_resolved'] = 0;
		$data['count_closed'] = 0;
		$data['count_all'] = 0;

		foreach ($counts_res as $cr) {
			$st = strtolower($cr['status']);
			if ($st == 'open') $data['count_open'] = (int)$cr['cnt'];
			if ($st == 'in progress') $data['count_in_progress'] = (int)$cr['cnt'];
			if ($st == 'resolved') $data['count_resolved'] = (int)$cr['cnt'];
			if ($st == 'closed') $data['count_closed'] = (int)$cr['cnt'];
			$data['count_all'] += (int)$cr['cnt'];
		}

		$this->load->view('manager/tickets/index', $data);
	}

	/**
	 * Raise a new ticket directly from Help Desk page
	 */
	public function add()
	{
		$this->if_not_login();
		$manager_id = (int)$this->session->userdata('manager_id');

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$subject = trim($this->input->post('subject'));
			$category = trim($this->input->post('category'));
			$priority = trim($this->input->post('priority'));
			$description = trim($this->input->post('description'));

			$customer = trim($this->input->post('customer'));
			if (!empty($customer) && empty($subject)) {
				$subject = 'Ticket for ' . $customer;
			}
			if (empty($description)) {
				$description = trim($this->input->post('remark'));
				if (empty($description)) {
					$description = 'Ticket raised from portal.';
				}
			}

			if (empty($subject) || empty($description)) {
				$this->session->set_flashdata('error_message', 'Please fill in the required fields for your ticket.');
				redirect('manager/tickets/add');
				return;
			}

			// Generate unique ticket number: TKT-YYYYMM-XXXX
			$total_t = $this->db->count_all('ci_tickets') + 1;
			$ticket_number = 'TKT-' . date('Ym') . '-' . sprintf('%04d', $total_t);
			
			// Verify uniqueness
			$check = $this->db->get_where('ci_tickets', array('ticket_number' => $ticket_number))->row_array();
			if (!empty($check)) {
				$ticket_number = 'TKT-' . date('Ym') . '-' . mt_rand(1000, 9999);
			}

			$insert_data = array(
				'ticket_number' => $ticket_number,
				'subject' => $subject,
				'category' => !empty($category) ? $category : 'General Issue',
				'priority' => !empty($priority) ? $priority : 'Medium',
				'description' => $description,
				'admin_response' => '',
				'status' => 'Open',
				'created_by' => $manager_id,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);

			$this->db->insert('ci_tickets', $insert_data);
			$new_ticket_id = $this->db->insert_id();

			// Store initial issue in message chat table
			$u_row = $this->db->get_where('ci_user', array('id' => $manager_id))->row_array();
			$user_name = !empty($u_row['name']) ? $u_row['name'] : 'User';
			$this->db->insert('ci_ticket_messages', array(
				'ticket_id' => $new_ticket_id,
				'sender_id' => $manager_id,
				'sender_role' => (int)$this->session->userdata('manager_role'),
				'sender_name' => $user_name,
				'message' => $description,
				'created_at' => date('Y-m-d H:i:s')
			));

			$this->session->set_flashdata('message', 'Ticket #' . $ticket_number . ' has been successfully submitted! It will remain open until resolved.');
			redirect('manager/tickets');
			return;
		}

		$data = array();
		$data['info'] = array(
			'title' => 'Manager - Tickets',
			'page_heading' => 'Raise Ticket'
		);
		$data['info2'] = $this->if_not_login();

		$data['st_list'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_status');
		$data['so_list'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_sources');
		$data['p_cat'] = $this->comman_model->getAll(array('parent_id' => 0, 'status' => 1, 'cat_type' => 'p_cat'), '', 'ci_terms');
		$data['lc_list'] = $this->comman_model->getAll(array('cat_type' => 'lead_cat', 'parent_id' => 0), '', 'ci_terms');
		$data['c_list'] = $this->comman_model->getAll('', array('id', 'ASC'), 'countries');  
		$data['u_list'] = $this->comman_model->getAll('', array('id', 'ASC'), 'ci_user');
		$data['v_list'] = $this->ci_admin_model->getShortVerticals($this->session->userdata('manager_id'));

		$this->load->view('manager/tickets/add', $data);
	}

	/**
	 * Follow-up Ticket: Reuses existing followups layout
	 */
	public function followups()
	{
		$this->if_not_login();
		$data = array();
		$data['info'] = array(
			'title' => 'Manager - Ticket Follow-up',
			'page_heading' => 'Follow-up Ticket'
		);
		$data['info2'] = $this->if_not_login();
		$data['st_list'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_status');
		$data['list'] = array();

		$this->load->view('manager/tickets/followups', $data);
	}

	/**
	 * View a single ticket details and chat conversation
	 */
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

		$this->db->select('t.*, u.name as creator_name, u.email as creator_email, u.mobile as creator_mobile, r.name as resolver_name');
		$this->db->from('ci_tickets t');
		$this->db->join('ci_user u', 'u.id = t.created_by', 'left');
		$this->db->join('ci_user r', 'r.id = t.resolved_by', 'left');
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

		// Security: Non-admin/Non-support can only view their own tickets
		if (!$can_manage && $ticket['created_by'] != $manager_id) {
			show_error('Access Denied: You do not have permission to view this ticket.', 403);
			return;
		}

		// Fetch chat messages
		$messages = $this->db->where('ticket_id', $ticket['id'])->order_by('id', 'ASC')->get('ci_ticket_messages')->result_array();

		// If no chat records yet in table, ensure initial description exists
		if (empty($messages) && !empty($ticket['description'])) {
			$init_msg = array(
				'id' => 0,
				'ticket_id' => $ticket['id'],
				'sender_id' => $ticket['created_by'],
				'sender_role' => 0,
				'sender_name' => !empty($ticket['creator_name']) ? $ticket['creator_name'] : 'User',
				'message' => $ticket['description'],
				'created_at' => $ticket['created_at']
			);
			$messages[] = $init_msg;

			if (!empty($ticket['admin_response'])) {
				$messages[] = array(
					'id' => 1,
					'ticket_id' => $ticket['id'],
					'sender_id' => 0,
					'sender_role' => 1,
					'sender_name' => 'Support Team',
					'message' => $ticket['admin_response'],
					'created_at' => $ticket['updated_at']
				);
			}
		}

		$data = array();
		$data['info'] = array(
			'title' => 'Help Desk - Ticket #' . $ticket['ticket_number'],
			'page_heading' => 'Ticket #' . $ticket['ticket_number']
		);
		$data['info2'] = $this->if_not_login();
		$data['ticket'] = $ticket;
		$data['messages'] = $messages;
		$data['is_admin'] = ($role === 1);
		$data['is_support'] = $this->is_support_user();
		$data['can_manage_tickets'] = $can_manage;

		$this->load->view('manager/tickets/view', $data);
	}

	/**
	 * Send reply / message in ticket chat (User, Support, or Admin)
	 */
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

		// Security: Only Admin, Support, or the ticket creator can reply
		if (!$can_manage && $ticket['created_by'] != $manager_id) {
			show_error('Access Denied: You do not have permission to post messages on this ticket.', 403);
			return;
		}

		if (empty($response_msg)) {
			$this->session->set_flashdata('error_message', 'Message cannot be empty.');
			redirect('manager/tickets/view/' . $ticket_id);
			return;
		}

		$current_user = $this->db->get_where('ci_user', array('id' => $manager_id))->row_array();
		$sender_name = !empty($current_user['name']) ? $current_user['name'] : 'User';
		if ($role === 1) {
			$display_sender = $sender_name . ' (Admin)';
		} elseif ($this->is_support_user()) {
			$display_sender = $sender_name . ' (Support)';
		} else {
			$display_sender = $sender_name;
		}

		// 1. Insert chat message
		$this->db->insert('ci_ticket_messages', array(
			'ticket_id' => $ticket_id,
			'sender_id' => $manager_id,
			'sender_role' => $role,
			'sender_name' => $display_sender,
			'message' => $response_msg,
			'created_at' => date('Y-m-d H:i:s')
		));

		// 2. Prepare ticket update
		$update_data = array(
			'updated_at' => date('Y-m-d H:i:s')
		);

		if ($can_manage && !empty($new_status)) {
			if (in_array($new_status, array('Open', 'In Progress', 'Resolved', 'Closed'))) {
				$update_data['status'] = $new_status;
			}
			if (in_array($new_status, array('Resolved', 'Closed'))) {
				$update_data['resolved_by'] = $manager_id;
				if (empty($ticket['resolved_at']) || $ticket['resolved_at'] == '0000-00-00 00:00:00') {
					$update_data['resolved_at'] = date('Y-m-d H:i:s');
				}
			}
			// Maintain admin_response string for backwards compatibility
			$timestamp = date('d-M-Y h:i A');
			$entry = "[$timestamp by $display_sender]:\n" . $response_msg;
			$existing_responses = $ticket['admin_response'];
			$update_data['admin_response'] = !empty($existing_responses) ? ($existing_responses . "\n\n" . $entry) : $entry;
		} else {
			// Regular user replied
			if ($ticket['status'] == 'Resolved') {
				// Reopen if user follows up on resolved ticket
				$update_data['status'] = 'Open';
			}
		}

		$this->db->where('id', $ticket_id)->update('ci_tickets', $update_data);
		$this->session->set_flashdata('message', 'Message sent successfully.');
		redirect('manager/tickets/view/' . $ticket_id);
	}

	/**
	/**
	 * Direct Resolve / Clear Action (Admin & Support)
	 */
	public function close($id = '')
	{
		$this->if_not_login();
		return $this->status($id, 'Resolved');
	}

	/**
	 * Update Ticket Status directly (Admin, Support, or Creator for Re-Open/Close)
	 */
	public function status($id = '', $target_status = '')
	{
		$this->if_not_login();
		$role = (int)$this->session->userdata('manager_role');
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

		$status = urldecode($target_status);
		$status = trim($status);

		$allowed = array('Open', 'In Progress', 'Resolved', 'Closed');
		if (!in_array($status, $allowed)) {
			$this->session->set_flashdata('error_message', 'Invalid ticket status requested.');
			redirect('manager/tickets');
			return;
		}

		// Security: Non-staff can only re-open (Open) or mark Closed their own tickets
		if (!$can_manage) {
			if ($ticket['created_by'] != $manager_id) {
				show_error('Access Denied: You do not have permission to alter this ticket.', 403);
				return;
			}
			if (!in_array($status, array('Open', 'Closed'))) {
				show_error('Access Denied: Only Support or Administrators can set status to ' . $status, 403);
				return;
			}
		}

		$update_data = array(
			'status' => $status,
			'updated_at' => date('Y-m-d H:i:s')
		);

		if (in_array($status, array('Resolved', 'Closed'))) {
			$update_data['resolved_by'] = $manager_id;
			if (empty($ticket['resolved_at']) || $ticket['resolved_at'] == '0000-00-00 00:00:00') {
				$update_data['resolved_at'] = date('Y-m-d H:i:s');
			}
		}

		$this->db->where('id', (int)$id)->update('ci_tickets', $update_data);

		$u_row = $this->db->get_where('ci_user', array('id' => $manager_id))->row_array();
		$actor_name = !empty($u_row['name']) ? $u_row['name'] : 'User';
		if ($role === 1) {
			$actor_display = $actor_name . ' (Admin)';
		} elseif ($this->is_support_user()) {
			$actor_display = $actor_name . ' (Support)';
		} else {
			$actor_display = $actor_name;
		}

		$this->db->insert('ci_ticket_messages', array(
			'ticket_id' => (int)$id,
			'sender_id' => $manager_id,
			'sender_role' => $role,
			'sender_name' => $actor_display,
			'message' => 'Status changed: Marked ticket as ' . $status . '.',
			'created_at' => date('Y-m-d H:i:s')
		));

		$this->session->set_flashdata('message', 'Ticket #' . $ticket['ticket_number'] . ' status updated to ' . $status . '.');
		
		$ref = $this->agent->referrer();
		if (!empty($ref) && strpos($ref, 'manager/tickets/view') !== false) {
			redirect('manager/tickets/view/' . (int)$id);
		} else {
			redirect('manager/tickets');
		}
	}

	/**
	 * Delete a Ticket (Restricted strictly to Admin)
	 */
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
			$this->session->set_flashdata('message', 'Ticket #' . $ticket['ticket_number'] . ' and its conversation have been deleted.');
		}

		redirect('manager/tickets');
	}
}
