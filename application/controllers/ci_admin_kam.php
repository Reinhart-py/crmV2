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

	public function index()
	{
		redirect('manager/kam/onboard');
	}

	public function onboard()
	{
		$this->if_not_login();

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
		$this->if_not_login();

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
		$this->if_not_login();

		$data = array();
		$data['info'] = array(
			'title' => 'Manager - KAM Report',
			'page_heading' => 'KAM Report'
		);
		$data['info2'] = $this->if_not_login();

		$this->load->view('manager/kam/report', $data);
	}
}
