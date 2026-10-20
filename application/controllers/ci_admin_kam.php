<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ci_admin_kam extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library(array('Session', 'Pagination'));
		$this->load->helper(array('form', 'url', 'text', 'date'));
		$this->load->model(array('ci_admin_model', 'comman_model'));
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Cache-Control: no-cache');
		$this->if_not_login();
	}

	public function if_not_login()
	{
		if ($this->session->userdata('manager_id') == '') {
			redirect('/manager/login');
		}
	}

	public function index()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'KAM - Key Account Management',
			'page_heading' => 'Key Account Management (KAM)'
		);

		$manager_id = (int)$this->session->userdata('manager_id');
		$role = (int)$this->session->userdata('manager_role');

		$this->db->select('A.*, S.status as status_name, U.name as owner_name');
		$this->db->from('ci_leads as A');
		$this->db->join('ci_status as S', 'S.id = A.status', 'left');
		$this->db->join('ci_user as U', 'U.id = A.lead_assign', 'left');
		$this->db->where('(A.company_status IS NULL OR LOWER(A.company_status) != "closed")');
		if ($role != 1 && $role != 4) {
			$this->db->where("(A.lead_assign = $manager_id OR A.created_by = $manager_id)");
		}
		$this->db->order_by('A.id', 'DESC');
		$this->db->limit(100);
		$data['clients'] = $this->db->get()->result_array();

		$this->load->view('manager/kam/index', $data);
	}

	public function my_client()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'KAM - My Clients',
			'page_heading' => 'My Clients'
		);

		$manager_id = (int)$this->session->userdata('manager_id');
		$role = (int)$this->session->userdata('manager_role');

		$this->db->select('A.*, S.status as lead_status, U.name as owner_name, E.title as servicecat');
		$this->db->from('ci_leads as A');
		$this->db->join('ci_status as S', 'S.id = A.status', 'left');
		$this->db->join('ci_user as U', 'U.id = A.created_by', 'left');
		$this->db->join('ci_terms as E', 'A.s_cat = E.id', 'left');
		$this->db->where('(A.company_status IS NULL OR LOWER(A.company_status) != "closed")');
		if ($role != 1 && $role != 4) {
			$this->db->where("(A.lead_assign = $manager_id OR A.created_by = $manager_id)");
		}
		$this->db->order_by('A.id', 'DESC');
		$this->db->limit(100);
		$data['product_list'] = $this->db->get()->result_array();

		$data['st_list'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_status');
		$data['pc_list'] = $this->comman_model->getAll(array('cat_type' => 'p_cat', 'parent_id' => 0), '', 'ci_terms');
		$data['lc_list'] = $this->comman_model->getAll(array('cat_type' => 'lead_cat', 'parent_id' => 0), '', 'ci_terms');

		$this->load->view('manager/kam/my_client', $data);
	}

	public function onboard()
	{
		redirect('manager/leads/add');
	}

	public function schedule()
	{
		redirect('manager/leads/meetings');
	}

	public function report()
	{
		redirect('manager/download-report');
	}
}
