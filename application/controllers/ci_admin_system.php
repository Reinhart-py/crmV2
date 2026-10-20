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

		$this->db->start_cache();
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
		$this->db->stop_cache();

		$total_rows = $this->db->count_all_results('ci_admin_logs');

		$per_page = 25;
		$page = (int)$page;
		if ($page < 0) $page = 0;

		$config['base_url'] = base_url() . 'manager/admin-logs';
		$config['total_rows'] = $total_rows;
		$config['per_page'] = $per_page;
		$config['uri_segment'] = 3;
		$config['reuse_query_string'] = TRUE;
		$config['full_tag_open'] = '<ul class="pagination pagination-sm no-margin pull-right">';
		$config['full_tag_close'] = '</ul>';
		$config['first_link'] = 'First';
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_link'] = 'Last';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		$config['next_link'] = '&raquo;';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_link'] = '&laquo;';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a href="#">';
		$config['cur_tag_close'] = '</a></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';

		$this->pagination->initialize($config);

		$this->db->order_by('id', 'DESC');
		$this->db->limit($per_page, $page);
		$query = $this->db->get('ci_admin_logs');
		$data['logs'] = ($query && is_object($query)) ? $query->result_array() : array();
		$this->db->flush_cache();

		$data['pagination'] = $this->pagination->create_links();
		$data['total_logs'] = $total_rows;
		$data['users_list'] = $this->db->select('id, name, email')->get('ci_user')->result_array();
		$data['activities'] = array('login', 'login_failed', 'logout', 'session_timeout', 'lead_create', 'lead_update', 'lead_delete', 'lead_import', 'lead_export', 'lead_reassign', 'user_create', 'user_update');

		$this->load->view('manager/system/admin_logs', $data);
	}
}