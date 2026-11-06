<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** Data Creation (top bar icon). Admin, or anyone given "Data Creation" in Roles > Core Modules. */
class Ci_admin_data_creation extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library(array('Session'));
		$this->load->helper(array('form', 'url'));
		$this->load->model(array('comman_model', 'ci_admin_model'));
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Pragma: no-cache');
		if ((int)$this->session->userdata('manager_id') === 0) {
			redirect('/manager/login');
			exit;
		}
	}

	public function index()
	{
		$data = array();
		$data['info'] = array('title' => 'Data Creation', 'page_heading' => 'Data Creation');
		$this->load->view('manager/data_creation/index', $data);
	}
}
