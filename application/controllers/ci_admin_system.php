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
		
		$role = $this->session->userdata('manager_role');
		if ($role != 1) {
			$this->session->set_flashdata('error_message', 'Access denied. Admin logs are restricted to administrators.');
			redirect('manager/dashboard');
			return;
		}

		$user_id = $this->input->get('user_id');
		$user_name = $this->input->get('user_name');
		$activity = $this->input->get('activity');
		$status_input = $this->input->get('status');
		$online_status_input = $this->input->get('online_status');
		$d1 = $this->input->get('d1');
		$d2 = $this->input->get('d2');

		$status_list = is_array($status_input) ? $status_input : (!empty($status_input) ? array($status_input) : array());
		if (!empty($online_status_input)) {
			$status_list[] = $online_status_input;
		}

		$is_filter_online = in_array('online', $status_list);
		$is_filter_offline = in_array('offline', $status_list);

		$db_status = array();
		foreach ($status_list as $st) {
			if ($st !== 'online' && $st !== 'offline' && !empty($st)) {
				$db_status[] = $st;
			}
		}

		$users_with_status = $this->db->select('id, name, email, is_online, last_active_at, last_ip')
			->order_by('name', 'ASC')
			->get('ci_user')
			->result_array();

		$user_status_map = array();
		$online_count = 0;
		$threshold_time = time() - 600;
		$online_uids = array();
		$offline_uids = array();

		foreach ($users_with_status as $u) {
			$is_active_recently = (!empty($u['last_active_at']) && strtotime($u['last_active_at']) >= $threshold_time);
			$is_online = ($u['is_online'] == 1 && $is_active_recently);
			if ($is_online) {
				$online_count++;
				$online_uids[] = (int)$u['id'];
			} else {
				$offline_uids[] = (int)$u['id'];
			}
			$user_status_map[$u['id']] = array(
				'name' => $u['name'],
				'email' => $u['email'],
				'is_online' => $is_online,
				'last_active_at' => $u['last_active_at'],
				'last_ip' => $u['last_ip']
			);
		}
		$data['users_with_status'] = $users_with_status;
		$data['user_status_map'] = $user_status_map;
		$data['online_users_count'] = $online_count;
		$data['total_users_count'] = count($users_with_status);

		$this->db->start_cache();
		if (!empty($user_id)) {
			$this->db->where('ci_admin_logs.user_id', (int)$user_id);
		}
		if (!empty($user_name)) {
			$this->db->like('ci_admin_logs.username', $user_name);
		}
		if ($is_filter_online) {
			if (!empty($online_uids)) {
				$this->db->where_in('ci_admin_logs.user_id', $online_uids);
			} else {
				$this->db->where('ci_admin_logs.user_id', -1);
			}
		} elseif ($is_filter_offline) {
			if (!empty($offline_uids)) {
				$this->db->where_in('ci_admin_logs.user_id', $offline_uids);
			}
		}
		if (!empty($activity)) {
			if (is_array($activity)) {
				$this->db->where_in('ci_admin_logs.activity', $activity);
			} else {
				$this->db->where('ci_admin_logs.activity', $activity);
			}
		}
		if (!empty($db_status)) {
			$this->db->where_in('ci_admin_logs.status', $db_status);
		}
		if (!empty($d1)) {
			$this->db->where('DATE(ci_admin_logs.created_at) >=', date('Y-m-d', strtotime($d1)));
		}
		if (!empty($d2)) {
			$this->db->where('DATE(ci_admin_logs.created_at) <=', date('Y-m-d', strtotime($d2)));
		}
		$this->db->stop_cache();

		$total_rows = $this->db->count_all_results('ci_admin_logs');

		$this->db->order_by('ci_admin_logs.id', 'DESC');
		$this->db->limit(1000);
		$query = $this->db->get('ci_admin_logs');
		$data['logs'] = ($query && is_object($query)) ? $query->result_array() : array();
		$this->db->flush_cache();

		$data['total_logs'] = $total_rows;
		$data['users_list'] = $users_with_status;
		$data['activities'] = array('login', 'login_failed', 'logout', 'session_timeout', 'lead_create', 'lead_update', 'lead_delete', 'lead_import', 'lead_export', 'lead_reassign', 'user_create', 'user_update');

		$this->load->view('manager/system/admin_logs', $data);
	}
}