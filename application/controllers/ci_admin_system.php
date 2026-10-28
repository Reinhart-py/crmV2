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
}