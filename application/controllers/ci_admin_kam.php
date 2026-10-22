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
		$this->_ensure_schema();
	}

	public function if_not_login()
	{
		$manager_id = $this->session->userdata('manager_id');
		if (empty($manager_id)) {
			redirect('/manager/login');
		}
	}

	private function _ensure_schema()
	{
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_kam_client_details (
			id INT AUTO_INCREMENT PRIMARY KEY,
			lead_id INT NOT NULL UNIQUE,
			client_name VARCHAR(255) DEFAULT NULL,
			group_name VARCHAR(255) DEFAULT NULL,
			executive_id INT DEFAULT NULL,
			executive_name VARCHAR(255) DEFAULT NULL,
			v_man VARCHAR(255) DEFAULT NULL,
			v_exe VARCHAR(255) DEFAULT NULL,
			kam_data LONGTEXT,
			schedule_data LONGTEXT,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			KEY (lead_id),
			KEY (client_name)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

		$this->db->query("CREATE TABLE IF NOT EXISTS ci_kam_schedules (
			id INT AUTO_INCREMENT PRIMARY KEY,
			client_id INT DEFAULT NULL,
			client_name VARCHAR(255) DEFAULT NULL,
			title VARCHAR(255) NOT NULL,
			service_type VARCHAR(100) DEFAULT NULL,
			frequency VARCHAR(50) DEFAULT 'Once',
			repeat_days VARCHAR(100) DEFAULT NULL,
			repeat_day_month INT DEFAULT NULL,
			start_date DATE DEFAULT NULL,
			end_date DATE DEFAULT NULL,
			start_time VARCHAR(20) DEFAULT NULL,
			end_time VARCHAR(20) DEFAULT NULL,
			assigned_to INT DEFAULT NULL,
			assigned_name VARCHAR(255) DEFAULT NULL,
			remarks TEXT,
			status VARCHAR(50) DEFAULT 'Scheduled',
			created_by INT DEFAULT NULL,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			KEY (client_id),
			KEY (assigned_to),
			KEY (start_date)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
	}

	public function index()
	{
		redirect('manager/kam/my-client');
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

		$d1 = trim($this->input->get('d1'));
		$d2 = trim($this->input->get('d2'));
		$mrr1 = trim($this->input->get('mrr1'));
		$mrr2 = trim($this->input->get('mrr2'));
		$status = $this->input->get('status');
		$category = $this->input->get('category');
		$lCat = $this->input->get('lCat');
		$user_name = trim($this->input->get('user_name'));
		$country = $this->input->get('country');
		$state = $this->input->get('state');
		$search = trim($this->input->get('search'));

		$this->db->select('A.*, S.status as lead_status, U.name as owner_name, E.title as servicecat, K.client_name as kam_client_name, K.executive_name, K.group_name');
		$this->db->from('ci_leads as A');
		$this->db->join('ci_kam_client_details as K', 'K.lead_id = A.id', 'inner');
		$this->db->join('ci_status as S', 'S.id = A.status', 'left');
		$this->db->join('ci_user as U', 'U.id = A.lead_assign', 'left');
		$this->db->join('ci_terms as E', 'A.s_cat = E.id', 'left');
		$this->db->where('(A.company_status IS NULL OR LOWER(A.company_status) != "closed")');

		if ($role == 1 || $role == 4) {
		} else {
			$this->db->where("(A.lead_assign = $manager_id OR (A.agents IS NOT NULL AND A.agents != '' AND FIND_IN_SET($manager_id, A.agents) > 0) OR K.executive_id = $manager_id)", NULL, FALSE);
		}

		if (!empty($d1) && !empty($d2)) {
			$this->db->where('A.created >=', date('Y-m-d', strtotime($d1)));
			$this->db->where('A.created <=', date('Y-m-d', strtotime($d2)));
		}
		if ($mrr1 !== '' && $mrr2 !== '') {
			$this->db->where('A.mrr >=', floatval($mrr1));
			$this->db->where('A.mrr <=', floatval($mrr2));
		}
		if (!empty($category)) {
			if (is_array($category)) {
				$this->db->where_in('A.s_cat', $category);
			} else {
				$this->db->where('A.s_cat', $category);
			}
		}
		if (!empty($lCat)) {
			if (is_array($lCat)) {
				$this->db->where_in('A.lead_cat', $lCat);
			} else {
				$this->db->where('A.lead_cat', $lCat);
			}
		}
		if (!empty($status)) {
			if (is_array($status)) {
				$this->db->where_in('A.status', $status);
			} else {
				$this->db->where('A.status', $status);
			}
		}
		if (!empty($user_name)) {
			$this->db->like('U.name', $user_name);
		}
		if (!empty($country)) {
			if (is_array($country)) {
				$this->db->where_in('A.country', $country);
			} else {
				$this->db->where('A.country', $country);
			}
		}
		if (!empty($state)) {
			if (is_array($state)) {
				$this->db->where_in('A.state', $state);
			} else {
				$this->db->where('A.state', $state);
			}
		}
		if (!empty($search)) {
			$this->db->where('(A.lead_id LIKE "%' . $this->db->escape_like_str($search) . '%" OR A.customer LIKE "%' . $this->db->escape_like_str($search) . '%" OR K.client_name LIKE "%' . $this->db->escape_like_str($search) . '%" OR A.contact1 LIKE "%' . $this->db->escape_like_str($search) . '%")');
		}

		$this->db->order_by('A.id', 'DESC');
		$data['product_list'] = $this->db->get()->result_array();

		$data['st_list'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_status');
		$data['pc_list'] = $this->comman_model->getAll(array('cat_type' => 'p_cat', 'parent_id' => 0), '', 'ci_terms');
		$data['lc_list'] = $this->comman_model->getAll(array('cat_type' => 'lead_cat', 'parent_id' => 0), '', 'ci_terms');
		$data['c_list'] = $this->comman_model->getAll('', array('id', 'ASC'), 'countries');
		$data['state_list'] = array();
		if (!empty($country)) {
			$c_id = is_array($country) ? $country[0] : $country;
			$data['state_list'] = $this->comman_model->getAll(array('country_id' => $c_id), '', 'states');
		}

		$this->load->view('manager/kam/my_client', $data);
	}

	public function onboard()
	{
		$manager_id = (int)$this->session->userdata('manager_id');
		$role = (int)$this->session->userdata('manager_role');

		if ($role != 1) {
			$this->session->set_flashdata('error_message', 'Access denied: Only administrators can onboard clients.');
			redirect('manager/kam/my-client');
			return;
		}

		$data = array();
		$data['info'] = array(
			'title' => 'KAM - Onboard Client',
			'page_heading' => 'Onboard Client'
		);

		$data['u_list'] = $this->comman_model->getAll(array('status' => 1), array('name', 'ASC'), 'ci_user');
		$data['st_list'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_status');
		$data['p_cat'] = $this->comman_model->getAll(array('parent_id' => 0, 'status' => 1, 'cat_type' => 'p_cat'), '', 'ci_terms');
		$data['business_categories'] = $this->comman_model->getAll(array('cat_type' => 'business_cat', 'status' => 1), '', 'ci_terms');
		$data['c_list'] = $this->comman_model->getAll('', array('id', 'ASC'), 'countries');

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$client_name = trim($this->input->post('customer'));
			if (empty($client_name)) {
				$client_name = trim($this->input->post('client_name'));
			}

			if (empty($client_name)) {
				$this->session->set_flashdata('error_message', 'Client Name is required.');
				redirect('manager/kam/onboard');
				return;
			}

			$group_name = trim($this->input->post('group_name'));
			$status_category = trim($this->input->post('status_category'));
			$client_status = trim($this->input->post('client_status'));
			if (empty($client_status)) {
				$client_status = 'Active';
			}

			$eos_date = trim($this->input->post('eos_date'));
			$onboarding_date = trim($this->input->post('onboarding_date'));
			$el_expiry = trim($this->input->post('el_expiry'));
			$el_due = trim($this->input->post('el_due'));
			$inactive_reason = trim($this->input->post('inactive_reason'));
			$amount = trim($this->input->post('amount'));
			$f_20_oct_26 = trim($this->input->post('f_20_oct_26'));
			$f_18_jan_27 = trim($this->input->post('f_18_jan_27'));
			$group_yn = trim($this->input->post('group_yn'));
			$executive_post = trim($this->input->post('executive'));

			$primary_assignee = (int)$this->input->post('lead_assign');
			if ($primary_assignee <= 0) {
				$primary_assignee = $manager_id;
			}

			$collaborators_input = $this->input->post('agents');
			$assigned_agents = '';
			if (!empty($collaborators_input)) {
				if (is_array($collaborators_input)) {
					$clean_collabs = array_values(array_diff(array_unique(array_filter(array_map('intval', $collaborators_input))), array($primary_assignee)));
					$assigned_agents = implode(',', $clean_collabs);
				} else {
					$single_c = (int)$collaborators_input;
					if ($single_c > 0 && $single_c !== $primary_assignee) {
						$assigned_agents = (string)$single_c;
					}
				}
			}

			$exec_user = $this->comman_model->getpostdata2(array('id' => $primary_assignee), '', 'ci_user');
			$executive_name = !empty($exec_user['name']) ? $exec_user['name'] : (!empty($executive_post) ? $executive_post : '');

			$ma_dec_25 = trim($this->input->post('ma_dec_25'));
			$ma_jan_26 = trim($this->input->post('ma_jan_26'));
			$ma_feb_26 = trim($this->input->post('ma_feb_26'));
			$ma_mar_26 = trim($this->input->post('ma_mar_26'));

			$tl_number = trim($this->input->post('tl_number'));
			$tl_expire_date = trim($this->input->post('tl_expire_date'));
			$tl_due = trim($this->input->post('tl_due'));
			$tl_authority = trim($this->input->post('tl_authority'));
			$last_mol = trim($this->input->post('last_mol'));
			$no_of_mol_employee = trim($this->input->post('no_of_mol_employee'));
			$year_ending = trim($this->input->post('year_ending'));
			$ct_reg = trim($this->input->post('ct_reg'));
			$trc_reg = trim($this->input->post('trc_reg'));
			$vat_qtr = trim($this->input->post('vat_qtr'));
			$fta_remark = trim($this->input->post('fta_remark'));
			$due_date = trim($this->input->post('due_date'));
			$month = trim($this->input->post('month'));

			$wa = trim($this->input->post('wa'));
			$db_field = trim($this->input->post('db_field'));
			$db_storage = trim($this->input->post('db_storage'));
			$sft = trim($this->input->post('sft'));
			$aml = trim($this->input->post('aml'));
			$icv = trim($this->input->post('icv'));
			$fta_update = trim($this->input->post('fta_update'));
			$fta_username = trim($this->input->post('fta_username'));
			$fta_password = trim($this->input->post('fta_password'));
			$audit_2025 = trim($this->input->post('audit_2025'));
			$sbr = trim($this->input->post('sbr'));
			$revenue = trim($this->input->post('revenue'));
			$expected_date = trim($this->input->post('expected_date'));
			$remarks = trim($this->input->post('remarks'));

			$software_name = trim($this->input->post('software_name'));
			$software_access = trim($this->input->post('software_access'));
			$soft_id = trim($this->input->post('soft_id'));
			$soft_pwd = trim($this->input->post('soft_pwd'));

			$pending_task = trim($this->input->post('pending_task'));
			$pending_task_meeting = trim($this->input->post('pending_task_meeting'));
			$fee_discussion = trim($this->input->post('fee_discussion'));
			$last_ma_update = trim($this->input->post('last_ma_update'));
			$last_review_date = trim($this->input->post('last_review_date'));
			$cfo = trim($this->input->post('cfo'));
			$audit_review = trim($this->input->post('audit_review'));
			$accounting = trim($this->input->post('accounting'));
			$bookkeeping = trim($this->input->post('bookkeeping'));
			$ye = trim($this->input->post('ye'));
			$vat = trim($this->input->post('vat'));
			$ma_frequency = trim($this->input->post('ma_frequency'));
			$mis = trim($this->input->post('mis'));
			$queries_review = trim($this->input->post('queries_review'));
			$visits = trim($this->input->post('visits'));
			$stock = trim($this->input->post('stock'));
			$operations = trim($this->input->post('operations'));
			$aml_task = trim($this->input->post('aml_task'));
			$cfo_consulting = trim($this->input->post('cfo_consulting'));
			$internal_review = trim($this->input->post('internal_review'));

			$location = trim($this->input->post('location'));
			$number = trim($this->input->post('number'));
			if (empty($number)) {
				$number = trim($this->input->post('contact1'));
			}
			$email = trim($this->input->post('email'));
			$v_man = trim($this->input->post('v_man'));
			$v_exe = trim($this->input->post('v_exe'));
			$frequency = trim($this->input->post('frequency'));
			$monthly_visits = trim($this->input->post('monthly_visits'));
			$day = trim($this->input->post('day'));

			$schedule_inputs = array();
			$months_days = array(
				array('Feb', 28),
				array('Mar', 31),
				array('Apr', 30),
				array('May', 7)
			);
			foreach ($months_days as $md) {
				$m_name = $md[0];
				$m_count = $md[1];
				for ($d = 1; $d <= $m_count; $d++) {
					$key = sprintf('%02d-%s', $d, $m_name);
					$safe_key = sprintf('sch_%02d_%s', $d, strtolower($m_name));
					$val = $this->input->post($safe_key);
					if ($val === null) {
						$val = $this->input->post($key);
					}
					$schedule_inputs[$key] = ($val !== null) ? trim($val) : '';
				}
			}

			$numeric_amount = !empty($amount) ? floatval(str_replace(',', '', $amount)) : 0;
			$numeric_revenue = !empty($revenue) ? floatval(str_replace(',', '', $revenue)) : 0;
			$total_mrr = ($numeric_amount > 0) ? $numeric_amount : $numeric_revenue;

			$created_date = !empty($onboarding_date) ? db_date($onboarding_date) : date('Y-m-d');

			$lead_record = array(
				'lead_id' => 100000000,
				'customer' => $client_name,
				'category' => !empty($status_category) ? $status_category : 'Existing',
				'company_status' => $client_status,
				'status' => 36,
				'r_amount' => $numeric_amount,
				'revenue' => $numeric_revenue,
				'mrr' => $total_mrr,
				'email' => $email,
				'contact1' => $number,
				'mobile' => $number,
				'location' => $location,
				'tl_number' => $tl_number,
				'tl_expire_date' => !empty($tl_expire_date) ? db_date($tl_expire_date) : '',
				'tl_authority' => $tl_authority,
				'month' => $month,
				'remark' => $remarks,
				'lead_assign' => $primary_assignee,
				'agents' => $assigned_agents,
				'created_by' => $manager_id,
				'created' => $created_date,
				'status_category' => $status_category,
				'database_category' => 'KAM Onboarded'
			);

			$inserted_lead_id = $this->comman_model->saveData('ci_leads', $lead_record);

			if ($inserted_lead_id) {
				$custom_lead_code = 100000000 + (int)$inserted_lead_id;
				$this->db->where('id', $inserted_lead_id)->update('ci_leads', array('lead_id' => $custom_lead_code));

				$kam_data_array = array(
					'group_name' => $group_name,
					'status_category' => $status_category,
					'client_status' => $client_status,
					'eos_date' => $eos_date,
					'onboarding_date' => $onboarding_date,
					'el_expiry' => $el_expiry,
					'el_due' => $el_due,
					'inactive_reason' => $inactive_reason,
					'amount' => $amount,
					'20-Oct-26' => $f_20_oct_26,
					'18-Jan-27' => $f_18_jan_27,
					'group_yn' => $group_yn,
					'executive' => $executive_name,
					'ma_dec_25' => $ma_dec_25,
					'ma_jan_26' => $ma_jan_26,
					'ma_feb_26' => $ma_feb_26,
					'ma_mar_26' => $ma_mar_26,
					'tl_due' => $tl_due,
					'last_mol' => $last_mol,
					'no_of_mol_employee' => $no_of_mol_employee,
					'year_ending' => $year_ending,
					'ct_reg' => $ct_reg,
					'trc_reg' => $trc_reg,
					'vat_qtr' => $vat_qtr,
					'fta_remark' => $fta_remark,
					'due_date' => $due_date,
					'month' => $month,
					'wa' => $wa,
					'db' => $db_field,
					'db_storage' => $db_storage,
					'sft' => $sft,
					'aml' => $aml,
					'icv' => $icv,
					'fta_update' => $fta_update,
					'fta_username' => $fta_username,
					'fta_password' => $fta_password,
					'audit_2025' => $audit_2025,
					'sbr' => $sbr,
					'expected_date' => $expected_date,
					'software_name' => $software_name,
					'software_access' => $software_access,
					'soft_id' => $soft_id,
					'soft_pwd' => $soft_pwd,
					'pending_task' => $pending_task,
					'pending_task_meeting' => $pending_task_meeting,
					'fee_discussion' => $fee_discussion,
					'last_ma_update' => $last_ma_update,
					'last_review_date' => $last_review_date,
					'cfo' => $cfo,
					'audit_review' => $audit_review,
					'accounting' => $accounting,
					'bookkeeping' => $bookkeeping,
					'ye' => $ye,
					'vat' => $vat,
					'ma_frequency' => $ma_frequency,
					'mis' => $mis,
					'queries_review' => $queries_review,
					'visits' => $visits,
					'stock' => $stock,
					'operations' => $operations,
					'aml_task' => $aml_task,
					'cfo_consulting' => $cfo_consulting,
					'internal_review' => $internal_review,
					'location' => $location,
					'number' => $number,
					'email' => $email,
					'v_man' => $v_man,
					'v_exe' => $v_exe,
					'frequency' => $frequency,
					'monthly_visits' => $monthly_visits,
					'day' => $day
				);

				$this->db->insert('ci_kam_client_details', array(
					'lead_id' => (int)$inserted_lead_id,
					'client_name' => $client_name,
					'group_name' => $group_name,
					'executive_id' => $primary_assignee,
					'executive_name' => $executive_name,
					'v_man' => $v_man,
					'v_exe' => $v_exe,
					'kam_data' => json_encode($kam_data_array),
					'schedule_data' => json_encode($schedule_inputs),
					'created_at' => date('Y-m-d H:i:s')
				));

				$this->ci_admin_model->log_admin_action(
					'kam_client_onboard',
					'Onboarded KAM client "' . $client_name . '" (ID #' . $custom_lead_code . ') assigned to user ID #' . $primary_assignee,
					'SUCCESS',
					$manager_id,
					$this->session->userdata('manager_name')
				);

				$this->session->set_flashdata('message', 'Client "' . htmlspecialchars($client_name) . '" onboarded successfully!');
				redirect('manager/kam/my-client');
				return;
			} else {
				$this->session->set_flashdata('error_message', 'Failed to onboard client. Please try again.');
				redirect('manager/kam/onboard');
				return;
			}
		}

		$this->load->view('manager/kam/onboard', $data);
	}

	public function schedule()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'KAM - Create Schedule',
			'page_heading' => 'Create Schedule'
		);

		$manager_id = (int)$this->session->userdata('manager_id');
		$role = (int)$this->session->userdata('manager_role');

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$client_name = trim($this->input->post('client_name'));
			if (empty($client_name)) {
				$client_name = trim($this->input->post('customer'));
			}
			$title = trim($this->input->post('title'));
			if (empty($title)) {
				$title = 'Scheduled Service - ' . (!empty($client_name) ? $client_name : 'Client');
			}
			$service_type = trim($this->input->post('service_type'));
			$frequency = trim($this->input->post('frequency'));
			if (empty($frequency)) {
				$frequency = 'Once';
			}
			$repeat_days = '';
			if ($frequency === 'Weekly' && $this->input->post('repeat_days')) {
				$days = $this->input->post('repeat_days');
				$repeat_days = is_array($days) ? implode(',', $days) : (string)$days;
			}
			$repeat_day_month = (int)$this->input->post('repeat_day_month');
			$start_date = trim($this->input->post('start_date'));
			$start_date_db = !empty($start_date) ? date('Y-m-d', strtotime($start_date)) : date('Y-m-d');
			$end_date = trim($this->input->post('end_date'));
			$end_date_db = !empty($end_date) ? date('Y-m-d', strtotime($end_date)) : null;

			$start_time = trim($this->input->post('start_time'));
			$end_time = trim($this->input->post('end_time'));
			$assigned_to = (int)$this->input->post('assigned_to');
			if ($assigned_to <= 0) {
				$assigned_to = $manager_id;
			}
			$assigned_user = $this->comman_model->getpostdata2(array('id' => $assigned_to), '', 'ci_user');
			$assigned_name = !empty($assigned_user['name']) ? $assigned_user['name'] : '';

			$remarks = trim($this->input->post('remarks'));
			$status = trim($this->input->post('status'));
			if (empty($status)) {
				$status = 'Scheduled';
			}

			$client_record = $this->db->where('customer', $client_name)->get('ci_leads')->row_array();
			$client_id = !empty($client_record['id']) ? (int)$client_record['id'] : null;

			$insert_schedule = array(
				'client_id' => $client_id,
				'client_name' => $client_name,
				'title' => $title,
				'service_type' => $service_type,
				'frequency' => $frequency,
				'repeat_days' => $repeat_days,
				'repeat_day_month' => $repeat_day_month,
				'start_date' => $start_date_db,
				'end_date' => $end_date_db,
				'start_time' => $start_time,
				'end_time' => $end_time,
				'assigned_to' => $assigned_to,
				'assigned_name' => $assigned_name,
				'remarks' => $remarks,
				'status' => $status,
				'created_by' => $manager_id,
				'created_at' => date('Y-m-d H:i:s')
			);

			$this->db->insert('ci_kam_schedules', $insert_schedule);
			$this->ci_admin_model->log_admin_action('kam_schedule_create', 'Created KAM schedule "' . $title . '" for ' . $client_name, 'SUCCESS', $manager_id, $this->session->userdata('manager_name'));
			$this->session->set_flashdata('message', 'Schedule "' . htmlspecialchars($title) . '" created successfully.');
			redirect('manager/kam/schedule');
			return;
		}

		$data['u_list'] = $this->comman_model->getAll(array('status' => 1), array('name', 'ASC'), 'ci_user');
		$data['p_cat'] = $this->comman_model->getAll(array('parent_id' => 0, 'status' => 1, 'cat_type' => 'p_cat'), '', 'ci_terms');
		$this->db->order_by('id', 'DESC');
		if ($role != 1 && $role != 4) {
			$this->db->where("(assigned_to = $manager_id OR created_by = $manager_id)");
		}
		$data['schedules'] = $this->db->get('ci_kam_schedules')->result_array();

		$this->load->view('manager/kam/schedule', $data);
	}

	public function report()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'KAM - Report',
			'page_heading' => 'KAM Reports'
		);
		$this->load->view('manager/kam/report', $data);
	}
}
