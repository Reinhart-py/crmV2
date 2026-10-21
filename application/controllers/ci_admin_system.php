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
}