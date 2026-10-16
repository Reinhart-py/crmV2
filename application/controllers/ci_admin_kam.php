<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ci_admin_kam extends CI_Controller {

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

	private function _check_kam_access($action = '')
	{
		$this->if_not_login();
		$role = (int)$this->session->userdata('manager_role');
		if ($role !== 1 && $role !== 2 && $role !== 4) {
			show_error('Access Denied: You do not have permission to access KAM.', 403);
			return false;
		}
		if ($role === 2 && $action !== 'my_client') {
			show_error('Access Denied: You do not have permission to access this KAM section.', 403);
			return false;
		}
		return true;
	}

	public function index()
	{
		$this->if_not_login();
		$role = (int)$this->session->userdata('manager_role');
		if ($role === 2) {
			redirect('manager/kam/my-client');
		} elseif ($role === 1 || $role === 4) {
			redirect('manager/kam/my-client');
		} else {
			show_error('Access Denied: You do not have permission to access KAM.', 403);
		}
	}

	public function my_client()
	{
		if (!$this->_check_kam_access('my_client')) return;

		$data = array();
		$data['info'] = array(
			'title' => 'Manager - KAM My Client',
			'page_heading' => 'My Client'
		);
		$data['info2'] = $this->if_not_login();

		$this->load->view('manager/kam/my_client', $data);
	}

	public function onboard()
	{
		if (!$this->_check_kam_access('onboard')) return;

		$data = array();
		$data['info'] = array(
			'title' => 'Manager - KAM Onboard Client',
			'page_heading' => 'Onboard Client'
		);
		$data['info2'] = $this->if_not_login();
		$data['product_list'] = array();

		$this->load->view('manager/kam/onboard', $data);
	}

	public function schedule()
	{
		if (!$this->_check_kam_access('schedule')) return;

		$data = array();
		$data['info'] = array(
			'title' => 'Manager - KAM Create Schedule',
			'page_heading' => 'Create Schedule'
		);
		$data['info2'] = $this->if_not_login();

		$this->load->view('manager/kam/schedule', $data);
	}

	public function pitch_schedule()
	{
		$this->schedule();
	}

	public function report()
	{
		if (!$this->_check_kam_access('report')) return;

		$data = array();
		$data['info'] = array(
			'title' => 'Manager - KAM Report',
			'page_heading' => 'KAM Report'
		);
		$data['info2'] = $this->if_not_login();

		$this->load->view('manager/kam/report', $data);
	}
}
