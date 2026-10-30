<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ci_admin_system extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->library(array('Session','Pagination'));
		$this->load->helper(array('form','url','text','date'));
		$this->load->model(array('ci_admin_model','comman_model'));
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Cache-Control: no-cache');
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
			return $data;
		}
	}

	public function is_user_login($url = '')
	{
		if ($this->session->userdata('manager_id') != '') {
			if ($url != '') {
				redirect($url);
			} else {
				redirect('manager/dashboard');
			}
		}
	}

	public function index()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Manager - System',
			'page_heading' => 'System Overview'
		);
		$data['info2'] = $this->if_not_login();
		redirect('manager/admin-logs');
	}

	public function logs($page = 0)
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Admin Logs - System Audit',
			'page_heading' => 'Admin Logs'
		);
		$data['info2'] = $this->if_not_login();
		
		$role = (int)$this->session->userdata('manager_role');
		$cur_uid = (int)$this->session->userdata('manager_id');
		if ($role !== 1 && $cur_uid !== 1) {
			$this->session->set_flashdata('error_message', 'Access denied. Admin logs are restricted to administrators.');
			redirect('manager/dashboard');
			return;
		}

		$user_id = $this->input->get('user_id');
		$activity = $this->input->get('activity');
		$status = $this->input->get('status');
		$d1 = $this->input->get('d1');
		$d2 = $this->input->get('d2');

		if (!empty($user_id)) {
			$this->db->where('user_id', (int)$user_id);
		}
		if (!empty($activity)) {
			$this->db->where('activity', trim($activity));
		}
		if (!empty($status)) {
			$this->db->where('status', trim($status));
		}
		if (!empty($d1)) {
			$this->db->where('DATE(created_at) >=', date('Y-m-d', strtotime($d1)));
		}
		if (!empty($d2)) {
			$this->db->where('DATE(created_at) <=', date('Y-m-d', strtotime($d2)));
		}

		$this->db->order_by('id', 'DESC');
		$this->db->limit(2000);
		$query = $this->db->get('ci_admin_logs');
		$data['logs'] = ($query && is_object($query)) ? $query->result_array() : array();

		$data['total_logs'] = count($data['logs']);
		$data['users_list'] = $this->db->select('id, name, email')->get('ci_user')->result_array();
		$data['activities'] = array('login', 'login_failed', 'logout', 'session_timeout', 'lead_create', 'lead_update', 'lead_delete', 'lead_import', 'lead_export', 'lead_reassign', 'user_create', 'user_update');

		$this->load->view('manager/system/admin_logs', $data);
	}

	public function live_location()
	{
		$this->if_not_login();
		$cur_role = (int)$this->session->userdata('manager_role');
		$cur_uid = (int)$this->session->userdata('manager_id');
		if ($cur_role !== 1 && $cur_role !== 4 && $cur_uid !== 1) {
			redirect('manager/dashboard');
			return;
		}

		$data = array();
		$data['info'] = array(
			'title' => 'Live Location Tracking',
			'page_heading' => 'Staff Live Location'
		);

		$search = trim((string)$this->input->get('search'));
		$dept = trim((string)$this->input->get('department'));

		$sql = "SELECT U.id as user_id, U.name, U.email, U.department, U.mobile, U.is_online,
			COALESCE(L.latitude, A.latitude) as latitude,
			COALESCE(L.longitude, A.longitude) as longitude,
			COALESCE(L.accuracy, A.accuracy) as accuracy,
			COALESCE(L.updated_at, A.created_at) as last_seen,
			L.tracking_active,
			A.in_time as clock_in_time,
			A.out_time as clock_out_time,
			A.location_address,
			A.status as attendance_status,
			R.title as role_title
		FROM ci_user U
		LEFT JOIN ci_roles R ON R.id = U.user_type
		LEFT JOIN ci_user_live_locations L ON L.user_id = U.id
		LEFT JOIN ci_attendance A ON A.user_id = U.id AND A.attendance_date = CURDATE()
		WHERE U.status = 1";

		if (!empty($search)) {
			$sql .= " AND U.name LIKE " . $this->db->escape('%' . $search . '%');
		}
		if (!empty($dept)) {
			$sql .= " AND U.department = " . $this->db->escape($dept);
		}

		$sql .= " ORDER BY U.name ASC";
		$data['locations'] = $this->db->query($sql)->result_array();

		$data['departments'] = $this->db->query("SELECT DISTINCT department FROM ci_user WHERE department IS NOT NULL AND department != '' ORDER BY department ASC")->result_array();
		$data['load_map'] = true;
		$this->load->view('manager/system/live_location', $data);
	}

	public function ajax_update_live_location()
	{
		$this->if_not_login();
		$uid = (int)$this->session->userdata('manager_id');
		$lat = $this->input->post('latitude');
		$lng = $this->input->post('longitude');
		$acc = $this->input->post('accuracy');
		$active = (int)$this->input->post('tracking_active');

		if ($uid > 0 && !empty($lat) && !empty($lng) && is_numeric($lat) && is_numeric($lng)) {
			$this->session->set_userdata('user_last_latitude', $lat);
			$this->session->set_userdata('user_last_longitude', $lng);

			$existing = $this->db->where('user_id', $uid)->get('ci_user_live_locations')->row_array();
			$payload = array(
				'user_id' => $uid,
				'latitude' => $lat,
				'longitude' => $lng,
				'accuracy' => !empty($acc) && is_numeric($acc) ? (float)$acc : null,
				'tracking_active' => ($active === 1 ? 1 : 0),
				'updated_at' => date('Y-m-d H:i:s')
			);
			if (!empty($existing)) {
				$this->db->where('user_id', $uid)->update('ci_user_live_locations', $payload);
			} else {
				$this->db->insert('ci_user_live_locations', $payload);
			}
			header('Content-Type: application/json');
			echo json_encode(array('success' => true));
			exit();
		}
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Invalid coordinates'));
		exit();
	}

	public function ajax_get_live_location()
	{
		$this->if_not_login();
		$cur_role = (int)$this->session->userdata('manager_role');
		$cur_uid = (int)$this->session->userdata('manager_id');
		if ($cur_role !== 1 && $cur_role !== 4 && $cur_uid !== 1) {
			header('Content-Type: application/json');
			echo json_encode(array('success' => false, 'message' => 'Unauthorized'));
			exit();
		}

		$sql = "SELECT U.id as user_id, U.name, U.email, U.department, U.mobile, U.is_online,
			COALESCE(L.latitude, A.latitude) as latitude,
			COALESCE(L.longitude, A.longitude) as longitude,
			COALESCE(L.accuracy, A.accuracy) as accuracy,
			COALESCE(L.updated_at, A.created_at) as last_seen,
			L.tracking_active,
			A.in_time as clock_in_time,
			A.out_time as clock_out_time,
			A.location_address,
			A.status as attendance_status,
			R.title as role_title
		FROM ci_user U
		LEFT JOIN ci_roles R ON R.id = U.user_type
		LEFT JOIN ci_user_live_locations L ON L.user_id = U.id
		LEFT JOIN ci_attendance A ON A.user_id = U.id AND A.attendance_date = CURDATE()
		WHERE U.status = 1
		ORDER BY U.name ASC";
		$rows = $this->db->query($sql)->result_array();

		header('Content-Type: application/json');
		echo json_encode(array('success' => true, 'locations' => $rows));
		exit();
	}

	private function _sync_kam_clients()
	{
		$won_leads = $this->db->query("SELECT L.id, L.customer, L.poc, L.lead_assign, L.agents, L.created, U.name as assign_name 
			FROM ci_leads L 
			LEFT JOIN ci_user U ON L.lead_assign = U.id 
			WHERE (L.status IN (39, 42, 44) OR L.id IN (SELECT DISTINCT lead_id FROM ci_orders WHERE lead_id > 0)) 
			AND L.customer IS NOT NULL AND TRIM(L.customer) != ''
			AND TRIM(L.customer) NOT IN (SELECT DISTINCT TRIM(client_name) FROM ci_kam_client_details WHERE client_name IS NOT NULL)
			GROUP BY TRIM(L.customer)
			ORDER BY L.id ASC")->result_array();

		if (!empty($won_leads)) {
			foreach ($won_leads as $wl) {
				$now = date('Y-m-d H:i:s');
				$this->db->insert('ci_kam_client_details', array(
					'lead_id' => (int)$wl['id'],
					'client_name' => trim($wl['customer']),
					'owner_name' => !empty($wl['poc']) ? trim($wl['poc']) : '',
					'executive_id' => (int)$wl['lead_assign'],
					'executive_name' => !empty($wl['assign_name']) ? $wl['assign_name'] : '',
					'collaborators' => !empty($wl['agents']) ? $wl['agents'] : '',
					'created_at' => !empty($wl['created']) ? date('Y-m-d H:i:s', strtotime($wl['created'])) : $now,
					'updated_at' => $now
				));
			}
		}
	}

	public function assignee()
	{
		$this->if_not_login();
		$this->_sync_kam_clients();
		$cur_role = (int)$this->session->userdata('manager_role');
		$cur_uid = (int)$this->session->userdata('manager_id');

		$has_access = ($cur_role === 1 || $cur_uid === 1 || has_section_perm('system_assignee', 'read') || has_section_perm('system', 'read'));
		if (!$has_access) {
			$this->session->set_flashdata('error_message', 'Permission denied. You do not have access to Assignee management.');
			redirect('manager/dashboard');
			return;
		}

		$can_edit = ($cur_role === 1 || $cur_uid === 1 || has_section_perm('system_assignee', 'write'));

		$data = array();
		$data['info'] = array(
			'title' => 'System - Assignee Management',
			'page_heading' => 'Onboarded Client Assignees'
		);
		$data['can_edit'] = $can_edit;

		$this->db->select('K.*, L.poc as lead_poc, L.contact1 as lead_contact, L.email as lead_email, L.category, L.company_status, U.name as primary_assignee_name, U.avatar as primary_assignee_avatar');
		$this->db->from('ci_kam_client_details K');
		$this->db->join('ci_leads L', 'K.lead_id = L.id', 'LEFT');
		$this->db->join('ci_user U', 'K.executive_id = U.id', 'LEFT');

		$search = trim((string)$this->input->get('search'));
		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('K.client_name', $search);
			$this->db->or_like('K.owner_name', $search);
			$this->db->or_like('L.poc', $search);
			$this->db->or_like('L.contact1', $search);
			$this->db->group_end();
		}

		$d1 = trim((string)$this->input->get('d1'));
		$d2 = trim((string)$this->input->get('d2'));
		if (!empty($d1) && !empty($d2)) {
			$this->db->where('K.created_at >=', date('Y-m-d 00:00:00', strtotime($d1)));
			$this->db->where('K.created_at <=', date('Y-m-d 23:59:59', strtotime($d2)));
		} elseif (!empty($d1)) {
			$this->db->where('K.created_at >=', date('Y-m-d 00:00:00', strtotime($d1)));
		}

		$this->db->order_by('K.id', 'DESC');
		$data['clients'] = $this->db->get()->result_array();

		$data['users_list'] = $this->db->select('id, name, department, email')->where('status', 1)->order_by('name', 'ASC')->get('ci_user')->result_array();

		$user_map = array();
		foreach ($data['users_list'] as $u) {
			$user_map[$u['id']] = $u['name'];
		}
		$data['user_map'] = $user_map;
		$data['reset_url'] = base_url('manager/system/assignee');

		$this->load->view('manager/system/assignee', $data);
	}

	public function ajax_search_all_companies()
	{
		$this->if_not_login();
		$term = trim((string)$this->input->get('term', true));
		if (empty($term)) {
			$term = trim((string)$this->input->get('q', true));
		}
		$results = array();
		if (strlen($term) >= 2) {
			$rows = $this->db->query("SELECT DISTINCT customer as cname, id as lead_id, poc FROM ci_leads WHERE customer LIKE " . $this->db->escape('%' . $term . '%') . " AND customer != '' ORDER BY customer ASC LIMIT 30")->result_array();
			foreach ($rows as $r) {
				$results[] = array(
					'id' => $r['cname'],
					'lead_id' => (int)$r['lead_id'],
					'owner' => !empty($r['poc']) ? $r['poc'] : '',
					'text' => $r['cname'] . (!empty($r['poc']) ? ' (' . $r['poc'] . ')' : '')
				);
			}
		}
		header('Content-Type: application/json');
		echo json_encode(array('results' => $results));
		exit();
	}

	public function ajax_update_assignee()
	{
		$this->if_not_login();
		$cur_role = (int)$this->session->userdata('manager_role');
		$cur_uid = (int)$this->session->userdata('manager_id');

		$can_edit = ($cur_role === 1 || $cur_uid === 1 || has_section_perm('system_assignee', 'write'));
		if (!$can_edit) {
			header('Content-Type: application/json');
			echo json_encode(array('success' => false, 'message' => 'Permission denied. Normal KAM and Back Office users cannot change these assignments without permission.'));
			exit();
		}

		$kam_id = (int)$this->input->post('kam_id');
		$primary_assignee = (int)$this->input->post('primary_assignee');
		$collaborators_input = $this->input->post('collaborators');
		$company_name = trim((string)$this->input->post('company_name'));
		$lead_id = (int)$this->input->post('lead_id');

		if ($primary_assignee <= 0) {
			header('Content-Type: application/json');
			echo json_encode(array('success' => false, 'message' => 'Please select a primary assignee.'));
			exit();
		}

		$primary_user = $this->db->where('id', $primary_assignee)->where('status', 1)->get('ci_user')->row_array();
		if (empty($primary_user)) {
			header('Content-Type: application/json');
			echo json_encode(array('success' => false, 'message' => 'Primary assignee user must be an active system user.'));
			exit();
		}

		$clean_collabs = array();
		if (!empty($collaborators_input) && is_array($collaborators_input)) {
			foreach ($collaborators_input as $cid) {
				$c_int = (int)$cid;
				if ($c_int > 0 && $c_int !== $primary_assignee && !in_array($c_int, $clean_collabs)) {
					$clean_collabs[] = $c_int;
				}
			}
		}
		$collabs_str = implode(',', $clean_collabs);
		$now = date('Y-m-d H:i:s');

		if ($kam_id > 0) {
			$kam_rec = $this->db->where('id', $kam_id)->get('ci_kam_client_details')->row_array();
			if (empty($kam_rec)) {
				header('Content-Type: application/json');
				echo json_encode(array('success' => false, 'message' => 'Client record not found.'));
				exit();
			}
			$this->db->where('id', $kam_id)->update('ci_kam_client_details', array(
				'executive_id' => $primary_assignee,
				'executive_name' => $primary_user['name'],
				'collaborators' => $collabs_str,
				'updated_at' => $now
			));
			if (!empty($kam_rec['lead_id'])) {
				$this->db->where('id', (int)$kam_rec['lead_id'])->update('ci_leads', array(
					'lead_assign' => $primary_assignee,
					'agents' => $collabs_str,
					'modified' => date('Y-m-d')
				));
			}
			$client_name = $kam_rec['client_name'];
		} else {
			if (empty($company_name)) {
				header('Content-Type: application/json');
				echo json_encode(array('success' => false, 'message' => 'Company Name is required.'));
				exit();
			}
			$lead_rec = null;
			if ($lead_id > 0) {
				$lead_rec = $this->db->where('id', $lead_id)->get('ci_leads')->row_array();
			}
			if (empty($lead_rec)) {
				$lead_rec = $this->db->where('customer', $company_name)->order_by('id', 'DESC')->get('ci_leads')->row_array();
			}
			$owner_name = !empty($lead_rec['poc']) ? $lead_rec['poc'] : trim((string)$this->input->post('owner_name'));
			$resolved_lead_id = !empty($lead_rec['id']) ? (int)$lead_rec['id'] : null;

			$existing_kam = $this->db->where('client_name', $company_name)->get('ci_kam_client_details')->row_array();
			if (!empty($existing_kam)) {
				$this->db->where('id', $existing_kam['id'])->update('ci_kam_client_details', array(
					'executive_id' => $primary_assignee,
					'executive_name' => $primary_user['name'],
					'collaborators' => $collabs_str,
					'updated_at' => $now
				));
			} else {
				$this->db->insert('ci_kam_client_details', array(
					'lead_id' => $resolved_lead_id,
					'client_name' => $company_name,
					'owner_name' => $owner_name,
					'executive_id' => $primary_assignee,
					'executive_name' => $primary_user['name'],
					'collaborators' => $collabs_str,
					'created_at' => $now,
					'updated_at' => $now
				));
			}
			if ($resolved_lead_id) {
				$this->db->where('id', $resolved_lead_id)->update('ci_leads', array(
					'lead_assign' => $primary_assignee,
					'agents' => $collabs_str,
					'modified' => date('Y-m-d')
				));
			}
			$client_name = $company_name;
		}

		$this->ci_admin_model->log_admin_action('update_assignee', "Assigned company '{$client_name}' to {$primary_user['name']}", 'SUCCESS', $cur_uid, (string)$this->session->userdata('manager_name'));

		header('Content-Type: application/json');
		echo json_encode(array('success' => true, 'message' => "Company '{$client_name}' successfully assigned to {$primary_user['name']}."));
		exit();
	}

	public function kam_clients()
	{
		$this->if_not_login();
		$this->_sync_kam_clients();
		$cur_role = (int)$this->session->userdata('manager_role');
		$cur_uid = (int)$this->session->userdata('manager_id');

		$has_access = ($cur_role === 1 || $cur_uid === 1 || has_section_perm('kam_clients', 'read') || has_section_perm('system', 'read'));
		if (!$has_access) {
			$this->session->set_flashdata('error_message', 'Permission denied. You do not have access to KAM Client.');
			redirect('manager/dashboard');
			return;
		}

		$data = array();
		$data['info'] = array(
			'title' => 'System - KAM Client',
			'page_heading' => 'KAM Clients'
		);

		$search = trim((string)$this->input->get('company'));
		if (empty($search)) {
			$search = trim((string)$this->input->get('search'));
		}

		$this->db->select('K.*, L.poc as lead_poc, L.contact1 as lead_contact, L.email as lead_email, L.category, L.company_status, L.r_amount, L.mrr, L.location, L.tl_number, L.tl_emirate, U.name as assignee_name');
		$this->db->from('ci_kam_client_details K');
		$this->db->join('ci_leads L', 'K.lead_id = L.id', 'LEFT');
		$this->db->join('ci_user U', 'K.executive_id = U.id', 'LEFT');
		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('K.client_name', $search);
			$this->db->or_like('K.owner_name', $search);
			$this->db->or_like('L.poc', $search);
			$this->db->group_end();
		}
		$this->db->order_by('K.id', 'DESC');
		$clients = $this->db->get()->result_array();
		$searched = !empty($search);

		$data['clients'] = $clients;
		$data['searched'] = $searched;
		$data['search_term'] = $search;
		$data['cur_uid'] = $cur_uid;
		$data['cur_role'] = $cur_role;
		$data['is_admin'] = ($cur_role === 1 || $cur_uid === 1);
		$data['reset_url'] = base_url('manager/system/kam_clients');

		$this->load->view('manager/system/kam_clients', $data);
	}

	public function ajax_search_kam_companies()
	{
		$this->if_not_login();
		$term = trim((string)$this->input->get('term', true));
		if (empty($term)) {
			$term = trim((string)$this->input->get('q', true));
		}
		$results = array();
		if (strlen($term) >= 3) {
			$this->db->select('DISTINCT(client_name) as cname');
			$this->db->from('ci_kam_client_details');
			$this->db->like('client_name', $term);
			$this->db->limit(20);
			$q = $this->db->get()->result_array();
			foreach ($q as $row) {
				$c = trim((string)$row['cname']);
				if (!empty($c) && !in_array($c, $results)) {
					$results[] = $c;
				}
			}
		}
		$formatted = array();
		foreach ($results as $item) {
			$formatted[] = array('id' => $item, 'text' => $item);
		}
		header('Content-Type: application/json');
		echo json_encode(array('results' => $formatted));
		exit();
	}
}