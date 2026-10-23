<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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

	public function compute_occurrences($freq, $config, $start_date = null, $count = 5)
	{
		if (empty($start_date)) {
			$start_date = date('Y-m-d');
		}
		$occurrences = array();
		$current = new DateTime($start_date);
		
		if ($freq === 'Weekly' || $freq === 'Twice a Week' || $freq === 'Three Times a Week') {
			$days = isset($config['days']) && is_array($config['days']) ? $config['days'] : array('Mon');
			$day_nums = array();
			$map = array('Mon' => 1, 'Tue' => 2, 'Wed' => 3, 'Thu' => 4, 'Fri' => 5, 'Sat' => 6, 'Sun' => 7);
			foreach ($days as $d) {
				if (isset($map[$d])) {
					$day_nums[] = $map[$d];
				}
			}
			if (empty($day_nums)) {
				$day_nums = array(1);
			}
			sort($day_nums);

			$iter = clone $current;
			for ($step = 0; count($occurrences) < $count && $step < 90; $step++) {
				$iter->modify('+1 day');
				$w = (int)$iter->format('N');
				if (in_array($w, $day_nums)) {
					$occurrences[] = $iter->format('Y-m-d');
				}
			}
		} elseif ($freq === 'Monthly') {
			$target_day = isset($config['day']) ? (int)$config['day'] : 1;
			$is_last = !empty($config['is_last']);
			$year = (int)$current->format('Y');
			$month = (int)$current->format('n');
			
			for ($m = 0; count($occurrences) < $count && $m < 60; $m++) {
				$check_month = $month + $m;
				$check_year = $year + (int)floor(($check_month - 1) / 12);
				$norm_month = (($check_month - 1) % 12) + 1;
				
				$max_day = (int)date('t', strtotime(sprintf('%04d-%02d-01', $check_year, $norm_month)));
				$actual_day = $is_last ? $max_day : min($target_day, $max_day);
				$cand_date = sprintf('%04d-%02d-%02d', $check_year, $norm_month, $actual_day);
				
				if ($cand_date >= $current->format('Y-m-d')) {
					$occurrences[] = $cand_date;
				}
			}
		} elseif ($freq === 'Twice a Month' || $freq === 'Three Times a Month') {
			$days = isset($config['days']) && is_array($config['days']) ? $config['days'] : ($freq === 'Twice a Month' ? array(1, 15) : array(1, 10, 20));
			sort($days);
			$year = (int)$current->format('Y');
			$month = (int)$current->format('n');
			
			for ($m = 0; count($occurrences) < $count && $m < 60; $m++) {
				$check_month = $month + $m;
				$check_year = $year + (int)floor(($check_month - 1) / 12);
				$norm_month = (($check_month - 1) % 12) + 1;
				$max_day = (int)date('t', strtotime(sprintf('%04d-%02d-01', $check_year, $norm_month)));
				
				foreach ($days as $d) {
					$actual_day = max(1, min((int)$d, $max_day));
					$cand_date = sprintf('%04d-%02d-%02d', $check_year, $norm_month, $actual_day);
					if ($cand_date >= $current->format('Y-m-d') && !in_array($cand_date, $occurrences)) {
						$occurrences[] = $cand_date;
						if (count($occurrences) >= $count) {
							break 2;
						}
					}
				}
			}
		} elseif ($freq === 'Quarterly' || $freq === 'Half-Yearly' || $freq === 'Yearly') {
			$step_months = ($freq === 'Quarterly') ? 3 : (($freq === 'Half-Yearly') ? 6 : 12);
			$target_day = isset($config['day']) ? (int)$config['day'] : (int)$current->format('j');
			$start_month = isset($config['start_month']) ? (int)$config['start_month'] : (int)$current->format('n');
			$year = (int)$current->format('Y');

			for ($step = 0; count($occurrences) < $count && $step < 40; $step++) {
				$total_months = ($start_month - 1) + ($step * $step_months);
				$cand_year = $year + (int)floor($total_months / 12);
				$norm_month = ($total_months % 12) + 1;
				$max_day = (int)date('t', strtotime(sprintf('%04d-%02d-01', $cand_year, $norm_month)));
				$actual_day = min($target_day, $max_day);
				$cand_date = sprintf('%04d-%02d-%02d', $cand_year, $norm_month, $actual_day);
				
				if ($cand_date >= $current->format('Y-m-d')) {
					$occurrences[] = $cand_date;
				}
			}
		} else {
			$occurrences[] = $current->format('Y-m-d');
		}

		return array_slice($occurrences, 0, $count);
	}

	public function ajax_preview_recurrence()
	{
		$this->if_not_login();
		header('Content-Type: application/json');
		$freq = trim($this->input->get_post('freq'));
		$raw_config = $this->input->get_post('config');
		$config = is_array($raw_config) ? $raw_config : json_decode($raw_config, true);
		if (!is_array($config)) {
			$config = array();
		}
		if (empty($config)) {
			$day = $this->input->get_post('day_of_month');
			if ($day !== null && $day !== '') {
				$config['day'] = (int)$day;
			}
			$days_m = $this->input->get_post('days_of_month');
			if ($days_m !== null && $days_m !== '') {
				$config['days'] = is_array($days_m) ? $days_m : explode(',', $days_m);
			}
			$days_w = $this->input->get_post('days_of_week');
			if ($days_w !== null && $days_w !== '') {
				$config['days'] = is_array($days_w) ? $days_w : explode(',', $days_w);
			}
			$start_m = $this->input->get_post('start_month');
			if ($start_m !== null && $start_m !== '') {
				$config['start_month'] = (int)$start_m;
			}
		}
		$start_date = trim($this->input->get_post('start_date'));
		if (empty($start_date)) {
			$start_date = date('Y-m-d');
		} else {
			$start_date = date('Y-m-d', strtotime($start_date));
		}
		$time_str = trim($this->input->get_post('time'));
		$list = $this->compute_occurrences($freq, $config, $start_date, 5);
		$formatted = array();
		foreach ($list as $dt) {
			$str = date('D, d M Y', strtotime($dt));
			if (!empty($time_str)) {
				$str .= ' - ' . $time_str;
			}
			$formatted[] = $str;
		}
		echo json_encode(array(
			'status' => 'success',
			'dates' => $list,
			'formatted' => $formatted
		));
		exit;
	}

	public function index()
	{
		redirect('manager/tasks/internal');
	}

	public function internal()
	{
		$this->if_not_login();
		$this->ci_admin_model->check_permission('tasks', 'read', true);

		$role = (int)$this->session->userdata('manager_role');
		$manager_id = (int)$this->session->userdata('manager_id');

		$data = array();
		$data['info'] = array(
			'title' => 'Manager - Internal Tasks',
			'page_heading' => 'Internal Tasks'
		);
		$data['info2'] = $this->if_not_login();
		$data['can_create'] = ($role === 1 || has_section_perm('internal_tasks', 'write'));

		$data['st_list'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_status');
		$data['u_list'] = $this->comman_model->getAll('', array('name', 'ASC'), 'ci_user');

		$team = (int)$this->input->get('team');
		$d1 = trim($this->input->get('d1'));
		$d2 = trim($this->input->get('d2'));
		$status = trim($this->input->get('status'));
		$category = trim($this->input->get('category'));
		$invoiced = trim($this->input->get('invoiced'));
		$assigned = (int)$this->input->get('assigned_to');
		$search = trim($this->input->get('search'));

		$this->db->select('t.*, u.name as assigned_name, c.name as creator_name');
		$this->db->from('ci_tasks t');
		$this->db->join('ci_user u', 'u.id = t.assigned_to', 'left');
		$this->db->join('ci_user c', 'c.id = t.created_by', 'left');
		$this->db->where("(t.task_scope = 'internal' OR t.task_scope IS NULL)");

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

		if (!empty($category)) {
			$this->db->group_start();
			$this->db->where('t.category', $category);
			$this->db->or_where('t.a_department', $category);
			$this->db->group_end();
		}

		if ($invoiced === 'Yes' || $invoiced === 'No') {
			$this->db->where('t.invoiced', $invoiced);
		}

		if (!empty($assigned)) {
			$this->db->where('t.assigned_to', $assigned);
		}

		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('t.customer', $search);
			$this->db->or_like('t.poc', $search);
			$this->db->or_like('t.task_id', $search);
			$this->db->or_like('t.category', $search);
			$this->db->or_like('t.a_department', $search);
			$this->db->or_like('t.remark', $search);
			$this->db->group_end();
		}

		$this->db->order_by('t.id', 'DESC');
		$data['list'] = $this->db->get()->result_array();
		$data['team'] = $team;
		$data['current_tab'] = 'internal';

		$this->load->view('manager/tasks/internal', $data);
	}

	public function external()
	{
		$this->if_not_login();
		$this->ci_admin_model->check_permission('tasks', 'read', true);

		$role = (int)$this->session->userdata('manager_role');
		$manager_id = (int)$this->session->userdata('manager_id');

		$data = array();
		$data['info'] = array(
			'title' => 'Manager - External Tasks',
			'page_heading' => 'External Tasks'
		);
		$data['info2'] = $this->if_not_login();
		$data['can_create'] = ($role === 1 || $role === 4 || has_section_perm('external_tasks', 'write'));

		$data['st_list'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_status');
		$data['u_list'] = $this->comman_model->getAll('', array('name', 'ASC'), 'ci_user');

		$this->db->select('id, customer');
		$this->db->from('ci_leads');
		$this->db->where('(company_status IS NULL OR LOWER(company_status) != "closed")');
		if ($role !== 1 && $role !== 4) {
			$this->db->where("(lead_assign = {$manager_id} OR (created_by = {$manager_id} AND (lead_assign = 0 OR lead_assign IS NULL)) OR (agents IS NOT NULL AND agents != '' AND FIND_IN_SET({$manager_id}, agents) > 0))");
		}
		$this->db->order_by('customer', 'ASC');
		$data['clients'] = $this->db->get()->result_array();

		$team = (int)$this->input->get('team');
		$d1 = trim($this->input->get('d1'));
		$d2 = trim($this->input->get('d2'));
		$status = trim($this->input->get('status'));
		$client_id = (int)$this->input->get('client_id');
		$assigned = (int)$this->input->get('assigned_to');
		$search = trim($this->input->get('search'));

		$this->db->select('t.*, u.name as assigned_name, c.name as creator_name, l.customer as client_company');
		$this->db->from('ci_tasks t');
		$this->db->join('ci_user u', 'u.id = t.assigned_to', 'left');
		$this->db->join('ci_user c', 'c.id = t.created_by', 'left');
		$this->db->join('ci_leads l', 'l.id = t.client_id', 'left');
		$this->db->where('t.task_scope', 'external');

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

		if (!empty($client_id)) {
			$this->db->where('t.client_id', $client_id);
		}

		if (!empty($assigned)) {
			$this->db->where('t.assigned_to', $assigned);
		}

		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('t.customer', $search);
			$this->db->or_like('t.poc', $search);
			$this->db->or_like('t.contact1', $search);
			$this->db->or_like('t.task_id', $search);
			$this->db->or_like('t.invoice_ref', $search);
			$this->db->or_like('t.remark', $search);
			$this->db->or_like('l.customer', $search);
			$this->db->group_end();
		}

		$this->db->order_by('t.id', 'DESC');
		$data['list'] = $this->db->get()->result_array();
		$data['team'] = $team;
		$data['current_tab'] = 'external';

		$this->load->view('manager/tasks/external', $data);
	}

	public function add()
	{
		$this->if_not_login();
		$this->ci_admin_model->check_permission('tasks', 'write', true);

		$manager_id = (int)$this->session->userdata('manager_id');
		$role = (int)$this->session->userdata('manager_role');
		$can_internal_write = ($role === 1 || has_section_perm('internal_tasks', 'write'));
		$can_external_write = ($role === 1 || $role === 4 || has_section_perm('external_tasks', 'write'));

		$scope = trim($this->input->get('scope'));
		if ($scope === 'external') {
			if (!$can_external_write) {
				if ($can_internal_write) {
					redirect('manager/tasks/add?scope=internal');
					return;
				} else {
					$this->session->set_flashdata('error_message', 'Permission denied: Only Admin and Support can create external tasks.');
					redirect('manager/tasks/external');
					return;
				}
			}
		} else {
			$scope = 'internal';
			if (!$can_internal_write) {
				if ($can_external_write) {
					redirect('manager/tasks/add?scope=external');
					return;
				} else {
					$this->session->set_flashdata('error_message', 'Permission denied: Only Admin can create internal tasks.');
					redirect('manager/tasks/internal');
					return;
				}
			}
		}

		$data = array();
		$data['info'] = array(
			'title' => 'Manager - Tasks',
			'page_heading' => ($scope === 'external' ? 'Add External Task' : 'Add Internal Task')
		);
		$data['info2'] = $this->if_not_login();
		$data['default_scope'] = $scope;
		$data['can_internal_write'] = $can_internal_write;
		$data['can_external_write'] = $can_external_write;

		$data['st_list'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_status');
		$data['so_list'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_sources');
		$data['c_list'] = $this->comman_model->getAll('', array('id', 'ASC'), 'countries');  
		$data['u_list'] = $this->comman_model->getAll('', array('name', 'ASC'), 'ci_user');
		$data['telecom_cats'] = $this->comman_model->getAll(array('parent_id' => 57, 'status' => 1), '', 'ci_terms');

		$this->db->select('id, customer, poc, contact1, email');
		$this->db->from('ci_leads');
		$this->db->where('(company_status IS NULL OR LOWER(company_status) != "closed")');
		if ($role !== 1 && $role !== 4) {
			$this->db->where("(lead_assign = {$manager_id} OR (created_by = {$manager_id} AND (lead_assign = 0 OR lead_assign IS NULL)) OR (agents IS NOT NULL AND agents != '' AND FIND_IN_SET({$manager_id}, agents) > 0))");
		}
		$this->db->order_by('customer', 'ASC');
		$data['client_list'] = $this->db->get()->result_array();

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$task_scope = $this->input->post('task_scope') === 'external' ? 'external' : 'internal';
			
			if ($task_scope === 'internal' && !$can_internal_write) {
				$this->session->set_flashdata('error_message', 'Permission denied: Only Admin can create internal tasks.');
				redirect('manager/tasks/internal');
				return;
			}
			if ($task_scope === 'external' && !$can_external_write) {
				$this->session->set_flashdata('error_message', 'Permission denied: Only Admin and Support can create external tasks.');
				redirect('manager/tasks/external');
				return;
			}

			$task_id = 'TSK-' . date('Ym') . '-' . mt_rand(1000, 9999);
			$client_id = (int)$this->input->post('client_id');
			$customer_title = trim($this->input->post('customer'));
			$poc = trim($this->input->post('poc'));
			$contact1 = trim($this->input->post('contact1'));
			$email = trim($this->input->post('email'));

			if ($task_scope === 'external' && $client_id > 0) {
				$client_row = $this->db->get_where('ci_leads', array('id' => $client_id))->row_array();
				if (!empty($client_row)) {
					if (empty($customer_title)) {
						$customer_title = $client_row['customer'];
					}
					if (empty($poc)) {
						$poc = $client_row['poc'];
					}
					if (empty($contact1)) {
						$contact1 = $client_row['contact1'];
					}
					if (empty($email)) {
						$email = $client_row['email'];
					}
				}
			}

			if (empty($customer_title)) {
				$customer_title = 'Untitled Task';
			}

			$valid_depts = array('Procurement', 'Sales', 'HR', 'Admin', 'IT', 'Accounts', 'Operations');
			$a_dept = trim($this->input->post('a_department'));
			if (!in_array($a_dept, $valid_depts)) {
				$a_dept = 'Operations';
			}

			$c_date = trim($this->input->post('close_date'));
			if (!empty($c_date)) {
				$c_date = date('Y-m-d', strtotime($c_date));
			} else {
				$c_date = null;
			}

			$f_date = trim($this->input->post('followup_date'));
			if (!empty($f_date)) {
				$f_date = date('Y-m-d', strtotime($f_date));
			} else {
				$f_date = date('Y-m-d');
			}

			$is_recurring = (int)$this->input->post('is_recurring') === 1 ? 1 : 0;
			$recurrence_freq = null;
			$recurrence_config = null;
			$next_occurrence = null;

			if ($is_recurring === 1) {
				$recurrence_freq = trim($this->input->post('recurrence_freq'));
				$conf = array();

				if ($recurrence_freq === 'Weekly' || $recurrence_freq === 'Twice a Week' || $recurrence_freq === 'Three Times a Week') {
					$days_input = $this->input->post('rec_days');
					$selected_days = is_array($days_input) ? array_values($days_input) : array();
					$day_limit = ($recurrence_freq === 'Weekly') ? 1 : (($recurrence_freq === 'Twice a Week') ? 2 : 3);
					
					$cal_order = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
					$ordered_days = array();
					foreach ($cal_order as $cd) {
						if (in_array($cd, $selected_days)) {
							$ordered_days[] = $cd;
						}
					}
					
					if (count($ordered_days) !== $day_limit) {
						$this->session->set_flashdata('error_message', 'Please select exactly ' . $day_limit . ' weekday(s) for ' . $recurrence_freq . '.');
						redirect('manager/tasks/add?scope=' . $task_scope);
						return;
					}
					$conf['days'] = $ordered_days;
				} elseif ($recurrence_freq === 'Monthly') {
					$conf['day'] = (int)$this->input->post('rec_month_day');
					$conf['is_last'] = (int)$this->input->post('rec_is_last') === 1 ? 1 : 0;
				} elseif ($recurrence_freq === 'Twice a Month') {
					$d1 = (int)$this->input->post('rec_twice_d1');
					$d2 = (int)$this->input->post('rec_twice_d2');
					$conf['days'] = array($d1 > 0 ? $d1 : 1, $d2 > 0 ? $d2 : 15);
				} elseif ($recurrence_freq === 'Three Times a Month') {
					$d1 = (int)$this->input->post('rec_thrice_d1');
					$d2 = (int)$this->input->post('rec_thrice_d2');
					$d3 = (int)$this->input->post('rec_thrice_d3');
					$conf['days'] = array($d1 > 0 ? $d1 : 1, $d2 > 0 ? $d2 : 10, $d3 > 0 ? $d3 : 20);
				} elseif ($recurrence_freq === 'Quarterly' || $recurrence_freq === 'Half-Yearly' || $recurrence_freq === 'Yearly') {
					$conf['start_month'] = (int)$this->input->post('rec_start_month');
					$conf['day'] = (int)$this->input->post('rec_year_day');
				}

				$recurrence_config = json_encode($conf);
				$next_list = $this->compute_occurrences($recurrence_freq, $conf, $f_date, 1);
				if (!empty($next_list)) {
					$next_occurrence = $next_list[0];
				}
			}

			$invoiced = null;
			$invoice_ref = null;
			$invoice_date = null;
			$invoice_amount = null;
			$invoice_value = null;

			if ($a_dept === 'Accounts') {
				$invoiced = ($this->input->post('invoiced') === 'Yes') ? 'Yes' : 'No';
				if ($invoiced === 'Yes') {
					$invoice_ref = trim($this->input->post('invoice_ref'));
					$inv_dt = trim($this->input->post('invoice_date'));
					$invoice_date = !empty($inv_dt) ? date('Y-m-d', strtotime($inv_dt)) : null;
					$inv_amt = $this->input->post('invoice_amount');
					$invoice_amount = ($inv_amt !== null && $inv_amt !== '') ? floatval($inv_amt) : null;
					$inv_val = $this->input->post('invoice_value');
					$invoice_value = ($inv_val !== null && $inv_val !== '') ? floatval($inv_val) : null;
				}
			}

			$business_category = trim($this->input->post('business_category'));
			$s_cat = null;
			$p_cat = null;
			$p_details = null;
			$qty = null;
			$mrc = null;

			if ($task_scope === 'external' && $business_category === 'Telecom') {
				$s_cat = '57';
				$p_cat = trim($this->input->post('p_cat'));
				$p_details = trim($this->input->post('p_details'));
				$qty = trim($this->input->post('qty'));
				$mrc = trim($this->input->post('mrc'));
			}

			$insert_data = array(
				'task_id'           => $task_id,
				'task_scope'        => $task_scope,
				'client_id'         => $client_id,
				'customer'          => $customer_title,
				'poc'               => $poc,
				'contact1'          => $contact1,
				'alt_mobile'        => trim($this->input->post('alt_mobile')),
				'email'             => $email,
				'a_department'      => $a_dept,
				'category'          => $a_dept,
				'business_category' => $business_category,
				's_cat'             => $s_cat,
				'p_cat'             => $p_cat,
				'p_details'         => $p_details,
				'qty'               => $qty,
				'mrc'               => $mrc,
				'status'            => $this->input->post('status') ? trim($this->input->post('status')) : 'Open',
				'dnd_status'        => trim($this->input->post('dnd_status')),
				'close_date'        => $c_date,
				'remark'            => trim($this->input->post('remark')),
				'assigned_to'       => (int)$this->input->post('assigned_to'),
				'followup_date'     => $f_date,
				'followup_time'     => trim($this->input->post('followup_time')),
				'is_recurring'      => $is_recurring,
				'recurrence_freq'   => $recurrence_freq,
				'recurrence_config' => $recurrence_config,
				'next_occurrence'   => $next_occurrence,
				'invoice_amount'    => $invoice_amount,
				'invoiced'          => $invoiced,
				'invoice_ref'       => $invoice_ref,
				'invoice_date'      => $invoice_date,
				'invoice_value'     => $invoice_value,
				'inv_raf'           => null,
				'created_by'        => $manager_id,
				'created'           => date('Y-m-d H:i:s'),
				'modified'          => date('Y-m-d H:i:s')
			);

			$this->db->insert('ci_tasks', $insert_data);
			$this->session->set_flashdata('message', 'Task #' . $task_id . ' created successfully.');
			if ($task_scope === 'external') {
				redirect('manager/tasks/external');
			} else {
				redirect('manager/tasks/internal');
			}
			return;
		}

		$this->load->view('manager/tasks/add', $data);
	}

	public function view($id = '')
	{
		$this->if_not_login();
		$this->ci_admin_model->check_permission('tasks', 'read', true);

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

		$this->db->select('t.*, u.name as assigned_name, c.name as creator_name, cn.name as country_name, st.name as state_name, l.customer as client_company');
		$this->db->from('ci_tasks t');
		$this->db->join('ci_user u', 'u.id = t.assigned_to', 'left');
		$this->db->join('ci_user c', 'c.id = t.created_by', 'left');
		$this->db->join('countries cn', 'cn.id = t.country', 'left');
		$this->db->join('states st', 'st.id = t.state', 'left');
		$this->db->join('ci_leads l', 'l.id = t.client_id', 'left');
		$this->db->where('t.id', $id);
		$task = $this->db->get()->row_array();

		if (empty($task)) {
			$this->session->set_flashdata('error_message', 'Task not found.');
			redirect('manager/tasks');
			return;
		}

		$role = (int)$this->session->userdata('manager_role');
		$data['can_edit'] = ($task['task_scope'] === 'external') ? ($role === 1 || $role === 4 || has_section_perm('external_tasks', 'write')) : ($role === 1 || has_section_perm('internal_tasks', 'write'));

		$data['task'] = $task;
		$this->load->view('manager/tasks/view', $data);
	}

	public function edit($id = '')
	{
		$this->if_not_login();
		$this->ci_admin_model->check_permission('tasks', 'write', true);

		$id = (int)$id;
		if (empty($id)) {
			redirect('manager/tasks');
			return;
		}

		$manager_id = (int)$this->session->userdata('manager_id');
		$role = (int)$this->session->userdata('manager_role');
		$can_internal_write = ($role === 1 || has_section_perm('internal_tasks', 'write'));
		$can_external_write = ($role === 1 || $role === 4 || has_section_perm('external_tasks', 'write'));

		$this->db->where('id', $id);
		$task = $this->db->get('ci_tasks')->row_array();

		if (empty($task)) {
			$this->session->set_flashdata('error_message', 'Task not found.');
			redirect('manager/tasks');
			return;
		}

		$task_scope = ($task['task_scope'] === 'external') ? 'external' : 'internal';
		if ($task_scope === 'internal' && !$can_internal_write) {
			$this->session->set_flashdata('error_message', 'Permission denied: Only Admin can edit internal tasks.');
			redirect('manager/tasks/internal');
			return;
		}
		if ($task_scope === 'external' && !$can_external_write) {
			$this->session->set_flashdata('error_message', 'Permission denied: Only Admin and Support can edit external tasks.');
			redirect('manager/tasks/external');
			return;
		}

		$data = array();
		$data['info'] = array(
			'title' => 'Manager - Tasks',
			'page_heading' => 'Edit Task'
		);
		$data['info2'] = $this->if_not_login();
		$data['can_internal_write'] = $can_internal_write;
		$data['can_external_write'] = $can_external_write;

		$data['st_list'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_status');
		$data['so_list'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_sources');
		$data['c_list'] = $this->comman_model->getAll('', array('id', 'ASC'), 'countries');  
		$data['u_list'] = $this->comman_model->getAll('', array('name', 'ASC'), 'ci_user');
		$data['telecom_cats'] = $this->comman_model->getAll(array('parent_id' => 57, 'status' => 1), '', 'ci_terms');

		$this->db->select('id, customer, poc, contact1, email');
		$this->db->from('ci_leads');
		$this->db->where('(company_status IS NULL OR LOWER(company_status) != "closed")');
		if ($role !== 1 && $role !== 4) {
			$this->db->where("(lead_assign = {$manager_id} OR (created_by = {$manager_id} AND (lead_assign = 0 OR lead_assign IS NULL)) OR (agents IS NOT NULL AND agents != '' AND FIND_IN_SET({$manager_id}, agents) > 0))");
		}
		$this->db->order_by('customer', 'ASC');
		$data['client_list'] = $this->db->get()->result_array();

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$posted_scope = $this->input->post('task_scope') === 'external' ? 'external' : 'internal';
			
			if ($posted_scope === 'internal' && !$can_internal_write) {
				$this->session->set_flashdata('error_message', 'Permission denied: Only Admin can create/edit internal tasks.');
				redirect('manager/tasks/internal');
				return;
			}
			if ($posted_scope === 'external' && !$can_external_write) {
				$this->session->set_flashdata('error_message', 'Permission denied: Only Admin and Support can create/edit external tasks.');
				redirect('manager/tasks/external');
				return;
			}

			$client_id = (int)$this->input->post('client_id');
			$customer_title = trim($this->input->post('customer'));
			$poc = trim($this->input->post('poc'));
			$contact1 = trim($this->input->post('contact1'));
			$email = trim($this->input->post('email'));

			if ($posted_scope === 'external' && $client_id > 0) {
				$client_row = $this->db->get_where('ci_leads', array('id' => $client_id))->row_array();
				if (!empty($client_row)) {
					if (empty($customer_title)) {
						$customer_title = $client_row['customer'];
					}
					if (empty($poc)) {
						$poc = $client_row['poc'];
					}
					if (empty($contact1)) {
						$contact1 = $client_row['contact1'];
					}
					if (empty($email)) {
						$email = $client_row['email'];
					}
				}
			}

			if (empty($customer_title)) {
				$customer_title = 'Untitled Task';
			}

			$valid_depts = array('Procurement', 'Sales', 'HR', 'Admin', 'IT', 'Accounts', 'Operations');
			$a_dept = trim($this->input->post('a_department'));
			if (!in_array($a_dept, $valid_depts)) {
				$a_dept = 'Operations';
			}

			$c_date = trim($this->input->post('close_date'));
			if (!empty($c_date)) {
				$c_date = date('Y-m-d', strtotime($c_date));
			} else {
				$c_date = null;
			}

			$f_date = trim($this->input->post('followup_date'));
			if (!empty($f_date)) {
				$f_date = date('Y-m-d', strtotime($f_date));
			} else {
				$f_date = date('Y-m-d');
			}

			$is_recurring = (int)$this->input->post('is_recurring') === 1 ? 1 : 0;
			$recurrence_freq = null;
			$recurrence_config = null;
			$next_occurrence = null;

			if ($is_recurring === 1) {
				$recurrence_freq = trim($this->input->post('recurrence_freq'));
				$conf = array();

				if ($recurrence_freq === 'Weekly' || $recurrence_freq === 'Twice a Week' || $recurrence_freq === 'Three Times a Week') {
					$days_input = $this->input->post('rec_days');
					$selected_days = is_array($days_input) ? array_values($days_input) : array();
					$day_limit = ($recurrence_freq === 'Weekly') ? 1 : (($recurrence_freq === 'Twice a Week') ? 2 : 3);
					
					$cal_order = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
					$ordered_days = array();
					foreach ($cal_order as $cd) {
						if (in_array($cd, $selected_days)) {
							$ordered_days[] = $cd;
						}
					}
					
					if (count($ordered_days) !== $day_limit) {
						$this->session->set_flashdata('error_message', 'Please select exactly ' . $day_limit . ' weekday(s) for ' . $recurrence_freq . '.');
						redirect('manager/tasks/edit/' . $id);
						return;
					}
					$conf['days'] = $ordered_days;
				} elseif ($recurrence_freq === 'Monthly') {
					$conf['day'] = (int)$this->input->post('rec_month_day');
					$conf['is_last'] = (int)$this->input->post('rec_is_last') === 1 ? 1 : 0;
				} elseif ($recurrence_freq === 'Twice a Month') {
					$d1 = (int)$this->input->post('rec_twice_d1');
					$d2 = (int)$this->input->post('rec_twice_d2');
					$conf['days'] = array($d1 > 0 ? $d1 : 1, $d2 > 0 ? $d2 : 15);
				} elseif ($recurrence_freq === 'Three Times a Month') {
					$d1 = (int)$this->input->post('rec_thrice_d1');
					$d2 = (int)$this->input->post('rec_thrice_d2');
					$d3 = (int)$this->input->post('rec_thrice_d3');
					$conf['days'] = array($d1 > 0 ? $d1 : 1, $d2 > 0 ? $d2 : 10, $d3 > 0 ? $d3 : 20);
				} elseif ($recurrence_freq === 'Quarterly' || $recurrence_freq === 'Half-Yearly' || $recurrence_freq === 'Yearly') {
					$conf['start_month'] = (int)$this->input->post('rec_start_month');
					$conf['day'] = (int)$this->input->post('rec_year_day');
				}

				$recurrence_config = json_encode($conf);
				$next_list = $this->compute_occurrences($recurrence_freq, $conf, $f_date, 1);
				if (!empty($next_list)) {
					$next_occurrence = $next_list[0];
				}
			}

			$invoiced = null;
			$invoice_ref = null;
			$invoice_date = null;
			$invoice_amount = null;
			$invoice_value = null;

			if ($a_dept === 'Accounts') {
				$invoiced = ($this->input->post('invoiced') === 'Yes') ? 'Yes' : 'No';
				if ($invoiced === 'Yes') {
					$invoice_ref = trim($this->input->post('invoice_ref'));
					$inv_dt = trim($this->input->post('invoice_date'));
					$invoice_date = !empty($inv_dt) ? date('Y-m-d', strtotime($inv_dt)) : null;
					$inv_amt = $this->input->post('invoice_amount');
					$invoice_amount = ($inv_amt !== null && $inv_amt !== '') ? floatval($inv_amt) : null;
					$inv_val = $this->input->post('invoice_value');
					$invoice_value = ($inv_val !== null && $inv_val !== '') ? floatval($inv_val) : null;
				}
			}

			$business_category = trim($this->input->post('business_category'));
			$s_cat = null;
			$p_cat = null;
			$p_details = null;
			$qty = null;
			$mrc = null;

			if ($posted_scope === 'external' && $business_category === 'Telecom') {
				$s_cat = '57';
				$p_cat = trim($this->input->post('p_cat'));
				$p_details = trim($this->input->post('p_details'));
				$qty = trim($this->input->post('qty'));
				$mrc = trim($this->input->post('mrc'));
			}

			$update_data = array(
				'task_scope'        => $posted_scope,
				'client_id'         => $client_id,
				'customer'          => $customer_title,
				'poc'               => $poc,
				'contact1'          => $contact1,
				'alt_mobile'        => trim($this->input->post('alt_mobile')),
				'email'             => $email,
				'a_department'      => $a_dept,
				'category'          => $a_dept,
				'business_category' => $business_category,
				's_cat'             => $s_cat,
				'p_cat'             => $p_cat,
				'p_details'         => $p_details,
				'qty'               => $qty,
				'mrc'               => $mrc,
				'status'            => $this->input->post('status') ? trim($this->input->post('status')) : 'Open',
				'dnd_status'        => trim($this->input->post('dnd_status')),
				'close_date'        => $c_date,
				'remark'            => trim($this->input->post('remark')),
				'assigned_to'       => (int)$this->input->post('assigned_to'),
				'followup_date'     => $f_date,
				'followup_time'     => trim($this->input->post('followup_time')),
				'is_recurring'      => $is_recurring,
				'recurrence_freq'   => $recurrence_freq,
				'recurrence_config' => $recurrence_config,
				'next_occurrence'   => $next_occurrence,
				'invoice_amount'    => $invoice_amount,
				'invoiced'          => $invoiced,
				'invoice_ref'       => $invoice_ref,
				'invoice_date'      => $invoice_date,
				'invoice_value'     => $invoice_value,
				'inv_raf'           => null,
				'modified'          => date('Y-m-d H:i:s')
			);

			$this->db->where('id', $id);
			$this->db->update('ci_tasks', $update_data);
			$this->session->set_flashdata('message', 'Task updated successfully.');
			if ($posted_scope === 'external') {
				redirect('manager/tasks/external');
			} else {
				redirect('manager/tasks/internal');
			}
			return;
		}

		$data['task'] = $task;
		$this->load->view('manager/tasks/edit', $data);
	}

	public function followups()
	{
		$this->if_not_login();
		$this->ci_admin_model->check_permission('tasks', 'read', true);

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
