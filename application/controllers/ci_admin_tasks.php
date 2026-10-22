<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ci_admin_tasks extends CI_Controller {

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
		$this->if_not_login();
		$role = (int)$this->session->userdata('manager_role');
		$manager_id = (int)$this->session->userdata('manager_id');

		$data = array();
		$data['info'] = array(
			'title' => 'Manager - Tasks',
			'page_heading' => 'Task View'
		);
		$data['info2'] = $this->if_not_login();

		$data['st_list'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_status');
		$data['pc_list'] = $this->comman_model->getAll(array('parent_id' => 0, 'status' => 1, 'cat_type' => 'p_cat'), '', 'ci_terms');
		$data['lc_list'] = $this->comman_model->getAll(array('cat_type' => 'lead_cat', 'parent_id' => 0), '', 'ci_terms');

		$team = (int)$this->input->get('team');
		$d1 = trim($this->input->get('d1'));
		$d2 = trim($this->input->get('d2'));
		$status = trim($this->input->get('status'));
		$search = trim($this->input->get('search'));

		$this->db->select('t.*, u.name as assigned_name, c.name as creator_name');
		$this->db->from('ci_tasks t');
		$this->db->join('ci_user u', 'u.id = t.assigned_to', 'left');
		$this->db->join('ci_user c', 'c.id = t.created_by', 'left');

		if ($team === 1) {
			if ($role !== 1 && $role !== 4) {
				$team_array = $this->ci_admin_model->fetchTeamList($manager_id, '');
				if (!is_array($team_array)) {
					$team_array = array();
				}
				if (!in_array($manager_id, $team_array)) {
					$team_array[] = $manager_id;
				}
				$team_str = implode(',', $team_array);
				if (empty($team_str)) {
					$team_str = (string)$manager_id;
				}
				$this->db->where("(FIND_IN_SET(t.created_by, '".$team_str."') > 0 OR FIND_IN_SET(t.assigned_to, '".$team_str."') > 0)");
			}
		} else {
			if ($role !== 1 && $role !== 4) {
				$this->db->group_start();
				$this->db->where('t.created_by', $manager_id);
				$this->db->or_where('t.assigned_to', $manager_id);
				$this->db->group_end();
			}
		}

		if (!empty($d1) && !empty($d2)) {
			$date1 = date('Y-m-d 00:00:00', strtotime($d1));
			$date2 = date('Y-m-d 23:59:59', strtotime($d2));
			$this->db->where("t.created BETWEEN '{$date1}' AND '{$date2}'");
		} elseif (!empty($d1)) {
			$date1 = date('Y-m-d 00:00:00', strtotime($d1));
			$this->db->where("t.created >= '{$date1}'");
		} elseif (!empty($d2)) {
			$date2 = date('Y-m-d 23:59:59', strtotime($d2));
			$this->db->where("t.created <= '{$date2}'");
		}

		if (!empty($status)) {
			$this->db->where('t.status', $status);
		}

		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('t.customer', $search);
			$this->db->or_like('t.poc', $search);
			$this->db->or_like('t.contact1', $search);
			$this->db->or_like('t.task_id', $search);
			$this->db->or_like('t.category', $search);
			$this->db->group_end();
		}

		$this->db->order_by('t.id', 'DESC');
		$data['list'] = $this->db->get()->result_array();
		$data['team'] = $team;

		$this->load->view('manager/tasks/index', $data);
	}

	public function add()
	{
		$this->if_not_login();
		$manager_id = (int)$this->session->userdata('manager_id');
		$data = array();
		$data['info'] = array(
			'title' => 'Manager - Tasks',
			'page_heading' => 'Task Add'
		);
		$data['info2'] = $this->if_not_login();

		$data['st_list'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_status');
		$data['so_list'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_sources');
		$data['p_cat'] = $this->comman_model->getAll(array('parent_id' => 0, 'status' => 1, 'cat_type' => 'p_cat'), '', 'ci_terms');
		$data['lc_list'] = $this->comman_model->getAll(array('cat_type' => 'lead_cat', 'parent_id' => 0), '', 'ci_terms');
		$data['c_list'] = $this->comman_model->getAll('', array('id', 'ASC'), 'countries');  
		$data['u_list'] = $this->comman_model->getAll('', array('id', 'ASC'), 'ci_user');
		$data['v_list'] = $this->ci_admin_model->getShortVerticals($manager_id);

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$post = $this->input->post();
			$task_id = 'TSK-' . date('Ym') . '-' . mt_rand(1000, 9999);

			$c_date = trim($this->input->post('close_date'));
			if (!empty($c_date)) {
				$c_date = date('Y-m-d', strtotime($c_date));
			}
			$f_date = trim($this->input->post('followup_date'));
			if (!empty($f_date)) {
				$f_date = date('Y-m-d', strtotime($f_date));
			}

			$insert_data = array(
				'task_id'           => $task_id,
				'customer'          => (!empty($this->input->post('title')) ? trim($this->input->post('title')) : (!empty($this->input->post('customer')) ? trim($this->input->post('customer')) : 'Untitled Task')),
				'poc'               => trim($this->input->post('poc')),
				'contact1'          => trim($this->input->post('contact1')),
				'alt_mobile'        => trim($this->input->post('alt_mobile')),
				'alt_contact'       => trim($this->input->post('alt_contact')),
				'email'             => trim($this->input->post('email')),
				'a_number'          => trim($this->input->post('a_number')),
				'a_department'      => trim($this->input->post('a_department')),
				'r_amount'          => trim($this->input->post('r_amount')),
				'r_date'            => !empty($this->input->post('r_date')) ? db_date(trim($this->input->post('r_date'))) : '',
				'category'          => trim($this->input->post('category')),
				'business_category' => trim($this->input->post('business_category')),
				's_cat'             => trim($this->input->post('s_cat')),
				'p_cat'             => trim($this->input->post('p_cat')),
				'p_details'         => trim($this->input->post('p_details')),
				'tl_number'         => trim($this->input->post('tl_number')),
				'tl_authority'      => trim($this->input->post('tl_authority')),
				'tl_issue_date'     => !empty($this->input->post('tl_issue_date')) ? db_date(trim($this->input->post('tl_issue_date'))) : '',
				'tl_expire_date'    => !empty($this->input->post('tl_expire_date')) ? db_date(trim($this->input->post('tl_expire_date'))) : '',
				'qty'               => trim($this->input->post('qty')),
				'mrc'               => trim($this->input->post('mrc')),
				'revenue'           => trim($this->input->post('revenue')),
				'mrr'               => trim($this->input->post('mrr')),
				'location'          => trim($this->input->post('location')),
				'status'            => $this->input->post('status') ? trim($this->input->post('status')) : 'Open',
				'dnd_status'        => trim($this->input->post('dnd_status')),
				'dnd_check_date'    => !empty($this->input->post('dnd_check_date')) ? db_date(trim($this->input->post('dnd_check_date'))) : '',
				'close_date'        => $c_date,
				'remark'            => trim($this->input->post('remark')),
				'address'           => trim($this->input->post('address')),
				'country'           => (int)$this->input->post('country'),
				'state'             => (int)$this->input->post('state'),
				'city'              => trim($this->input->post('city')),
				'poc2'              => trim($this->input->post('poc2')),
				'contact2'          => trim($this->input->post('contact2')),
				'c_partner'         => trim($this->input->post('c_partner')),
				'otc'               => trim($this->input->post('otc')),
				'lead_cat'          => trim($this->input->post('lead_cat')),
				'assigned_to'       => (int)$this->input->post('assigned_to'),
				'lead_source'       => trim($this->input->post('lead_source')),
				'followup_date'     => $f_date,
				'followup_time'     => trim($this->input->post('followup_time')),
				'm_time'            => trim($this->input->post('m_time')),
				'created_by'        => $manager_id,
				'created'           => date('Y-m-d H:i:s'),
				'modified'          => date('Y-m-d H:i:s')
			);

			$this->db->insert('ci_tasks', $insert_data);
			$this->session->set_flashdata('message', 'Task #' . $task_id . ' created successfully.');
			redirect('manager/tasks');
			return;
		}

		$this->load->view('manager/tasks/add', $data);
	}

	public function view($id = '')
	{
		$this->if_not_login();
		$id = (int)$id;
		if (empty($id)) {
			redirect('manager/tasks');
			return;
		}

		$data = array();
		$data['info'] = array(
			'title' => 'Manager - Tasks',
			'page_heading' => 'Task Details'
		);
		$data['info2'] = $this->if_not_login();

		$this->db->select('t.*, u.name as assigned_name, c.name as creator_name, cn.name as country_name, st.name as state_name');
		$this->db->from('ci_tasks t');
		$this->db->join('ci_user u', 'u.id = t.assigned_to', 'left');
		$this->db->join('ci_user c', 'c.id = t.created_by', 'left');
		$this->db->join('countries cn', 'cn.id = t.country', 'left');
		$this->db->join('states st', 'st.id = t.state', 'left');
		$this->db->where('t.id', $id);
		$task = $this->db->get()->row_array();

		if (empty($task)) {
			$this->session->set_flashdata('error_message', 'Task not found.');
			redirect('manager/tasks');
			return;
		}

		$data['task'] = $task;
		$this->load->view('manager/tasks/view', $data);
	}

	public function edit($id = '')
	{
		$this->if_not_login();
		$id = (int)$id;
		if (empty($id)) {
			redirect('manager/tasks');
			return;
		}

		$data = array();
		$data['info'] = array(
			'title' => 'Manager - Tasks',
			'page_heading' => 'Edit Task'
		);
		$data['info2'] = $this->if_not_login();

		$this->db->where('id', $id);
		$task = $this->db->get('ci_tasks')->row_array();

		if (empty($task)) {
			$this->session->set_flashdata('error_message', 'Task not found.');
			redirect('manager/tasks');
			return;
		}

		$data['st_list'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_status');
		$data['so_list'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_sources');
		$data['p_cat'] = $this->comman_model->getAll(array('parent_id' => 0, 'status' => 1, 'cat_type' => 'p_cat'), '', 'ci_terms');
		$data['lc_list'] = $this->comman_model->getAll(array('cat_type' => 'lead_cat', 'parent_id' => 0), '', 'ci_terms');
		$data['c_list'] = $this->comman_model->getAll('', array('id', 'ASC'), 'countries');  
		$data['u_list'] = $this->comman_model->getAll('', array('id', 'ASC'), 'ci_user');
		if (!empty($task['country'])) {
			$data['state_list'] = $this->comman_model->getAll(array('country_id' => $task['country']), '', 'states');
		}

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$c_date = trim($this->input->post('close_date'));
			if (!empty($c_date)) {
				$c_date = date('Y-m-d', strtotime($c_date));
			}
			$f_date = trim($this->input->post('followup_date'));
			if (!empty($f_date)) {
				$f_date = date('Y-m-d', strtotime($f_date));
			}

			$update_data = array(
				'customer'          => trim($this->input->post('customer')),
				'poc'               => trim($this->input->post('poc')),
				'contact1'          => trim($this->input->post('contact1')),
				'alt_mobile'        => trim($this->input->post('alt_mobile')),
				'alt_contact'       => trim($this->input->post('alt_contact')),
				'email'             => trim($this->input->post('email')),
				'a_number'          => trim($this->input->post('a_number')),
				'a_department'      => trim($this->input->post('a_department')),
				'r_amount'          => trim($this->input->post('r_amount')),
				'r_date'            => !empty($this->input->post('r_date')) ? db_date(trim($this->input->post('r_date'))) : '',
				'category'          => trim($this->input->post('category')),
				'business_category' => trim($this->input->post('business_category')),
				's_cat'             => trim($this->input->post('s_cat')),
				'p_cat'             => trim($this->input->post('p_cat')),
				'p_details'         => trim($this->input->post('p_details')),
				'tl_number'         => trim($this->input->post('tl_number')),
				'tl_authority'      => trim($this->input->post('tl_authority')),
				'tl_issue_date'     => !empty($this->input->post('tl_issue_date')) ? db_date(trim($this->input->post('tl_issue_date'))) : '',
				'tl_expire_date'    => !empty($this->input->post('tl_expire_date')) ? db_date(trim($this->input->post('tl_expire_date'))) : '',
				'qty'               => trim($this->input->post('qty')),
				'mrc'               => trim($this->input->post('mrc')),
				'revenue'           => trim($this->input->post('revenue')),
				'mrr'               => trim($this->input->post('mrr')),
				'location'          => trim($this->input->post('location')),
				'status'            => $this->input->post('status') ? trim($this->input->post('status')) : 'Open',
				'dnd_status'        => trim($this->input->post('dnd_status')),
				'dnd_check_date'    => !empty($this->input->post('dnd_check_date')) ? db_date(trim($this->input->post('dnd_check_date'))) : '',
				'close_date'        => $c_date,
				'remark'            => trim($this->input->post('remark')),
				'address'           => trim($this->input->post('address')),
				'country'           => (int)$this->input->post('country'),
				'state'             => (int)$this->input->post('state'),
				'city'              => trim($this->input->post('city')),
				'poc2'              => trim($this->input->post('poc2')),
				'contact2'          => trim($this->input->post('contact2')),
				'c_partner'         => trim($this->input->post('c_partner')),
				'otc'               => trim($this->input->post('otc')),
				'lead_cat'          => trim($this->input->post('lead_cat')),
				'assigned_to'       => (int)$this->input->post('assigned_to'),
				'lead_source'       => trim($this->input->post('lead_source')),
				'followup_date'     => $f_date,
				'followup_time'     => trim($this->input->post('followup_time')),
				'modified'          => date('Y-m-d H:i:s')
			);

			$this->db->where('id', $id);
			$this->db->update('ci_tasks', $update_data);
			$this->session->set_flashdata('message', 'Task updated successfully.');
			redirect('manager/tasks');
			return;
		}

		$data['task'] = $task;
		$this->load->view('manager/tasks/edit', $data);
	}

	public function followups()
	{
		$this->if_not_login();
		$role = (int)$this->session->userdata('manager_role');
		$manager_id = (int)$this->session->userdata('manager_id');
		$type = (int)$this->input->get('type');
		$team = (int)$this->input->get('team');
		$d1 = trim($this->input->get('d1'));
		$d2 = trim($this->input->get('d2'));
		$status = trim($this->input->get('status'));
		$search = trim($this->input->get('search'));

		$data = array();
		$data['info'] = array(
			'title' => 'Manager - Task Follow-up',
			'page_heading' => 'Task Follow-up'
		);
		$data['info2'] = $this->if_not_login();

		$data['st_list'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_status');
		
		$this->db->select('t.*, u.name as assigned_name, c.name as creator_name');
		$this->db->from('ci_tasks t');
		$this->db->join('ci_user u', 'u.id = t.assigned_to', 'left');
		$this->db->join('ci_user c', 'c.id = t.created_by', 'left');
		$this->db->where('t.followup_date !=', '');
		$this->db->where('t.followup_date !=', '0000-00-00');
		$this->db->where('t.followup_date IS NOT NULL', NULL, FALSE);
		if (!empty($status)) {
			$this->db->where('t.status', $status);
		} else {
			$this->db->where_not_in('t.status', array('Completed', 'Closed'));
		}

		if ($team === 1) {
			if ($role !== 1 && $role !== 4) {
				$team_array = $this->ci_admin_model->fetchTeamList($manager_id, '');
				if (!is_array($team_array)) {
					$team_array = array();
				}
				if (!in_array($manager_id, $team_array)) {
					$team_array[] = $manager_id;
				}
				$team_str = implode(',', $team_array);
				if (empty($team_str)) {
					$team_str = (string)$manager_id;
				}
				$this->db->where("(FIND_IN_SET(t.created_by, '".$team_str."') > 0 OR FIND_IN_SET(t.assigned_to, '".$team_str."') > 0)");
			}
		} else {
			if ($role !== 1 && $role !== 4) {
				$this->db->group_start();
				$this->db->where('t.created_by', $manager_id);
				$this->db->or_where('t.assigned_to', $manager_id);
				$this->db->group_end();
			}
		}

		$today = date('Y-m-d');
		$last_date = date('Y-m-d', strtotime("-7 day"));
		$next_date = date('Y-m-d', strtotime("+7 day"));

		if ($type === 1) {
			$this->db->where('t.followup_date <', $today);
		} elseif ($type === 2) {
			$this->db->where('t.followup_date >=', $last_date);
			$this->db->where('t.followup_date <', $today);
		} elseif ($type === 3) {
			$this->db->where('t.followup_date', $today);
		} elseif ($type === 4) {
			$this->db->where('t.followup_date >', $today);
			$this->db->where('t.followup_date <=', $next_date);
		} elseif ($type === 5) {
			$this->db->where('t.followup_date >', $today);
		}

		if (!empty($d1) && !empty($d2)) {
			$date1 = date('Y-m-d', strtotime($d1));
			$date2 = date('Y-m-d', strtotime($d2));
			$this->db->where("t.followup_date BETWEEN '{$date1}' AND '{$date2}'");
		}

		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('t.customer', $search);
			$this->db->or_like('t.poc', $search);
			$this->db->or_like('t.contact1', $search);
			$this->db->or_like('t.task_id', $search);
			$this->db->group_end();
		}

		$this->db->order_by('t.followup_date', 'ASC');
		$data['list'] = $this->db->get()->result_array();
		$data['team'] = $team;
		$data['type'] = $type;

		$this->load->view('manager/tasks/followups', $data);
	}
}
