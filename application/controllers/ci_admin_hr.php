<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ci_admin_hr extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('ci_admin_model');
		$this->load->model('comman_model');
		$this->load->library('session');
		$this->load->helper(array('url', 'form', 'text', 'date'));
		$this->if_not_login();

		$uid = (int)$this->session->userdata('manager_id');
		$role = (int)$this->session->userdata('manager_role');
		$this->cur_uid = $uid;
		$this->cur_role = $role;
		$this->is_admin = ($role === 1 || $uid === 1);

		// Employee self-service pages (opened from the profile menu) are open to
		// every signed-in user; each method limits non-HR users to their own data.
		// "Managing" them (everyone's data) needs Full on that area's permission.
		$method = $this->router->fetch_method();
		$self_service = array(
			'leaves' => 'hr_leaves', 'apply_leave' => 'hr_leaves', 'cancel_leave' => 'hr_leaves', 'leave_action' => 'hr_leaves',
			'events_plans' => 'events_view',
			'training' => 'self_training',
			'search_topics' => 'hr_topics'
		);
		if (isset($self_service[$method])) {
			$this->is_hr = ($this->is_admin || crm_can($self_service[$method], 'write'));
			return;
		}
		// HR pages: each needs its own permission (View to open, Full to change).
		$page_keys = array(
			'index' => 'hr',
			'targets' => 'hr_targets', 'add_target' => 'hr_targets', 'delete_target' => 'hr_targets',
			'lifecycle' => 'hr_lifecycle', 'update_lifecycle' => 'hr_lifecycle',
			'attendance' => 'hr_attendance', 'ajax_clock' => 'hr_attendance', 'attendance_save' => 'hr_attendance',
			'shifts' => 'hr_shifts', 'add_shift' => 'hr_shifts', 'assign_shift' => 'hr_shifts',
			'leave_balances' => 'hr_leaves', 'leave_adjust' => 'hr_leaves', 'leave_calendar' => 'hr_leaves', 'leave_types' => 'hr_leaves', 'leave_type_save' => 'hr_leaves',
			'payroll' => 'hr_payroll', 'generate_payroll' => 'hr_payroll', 'payroll_status' => 'hr_payroll',
			'taxation' => 'hr_taxation',
			'appraisals' => 'hr_appraisals', 'add_appraisal' => 'hr_appraisals',
			'documents' => 'hr_documents', 'add_document' => 'hr_documents',
			'employees' => 'hr_employees', 'employee_save' => 'hr_employees', 'salary_add' => 'hr_employees', 'salary_delete' => 'hr_employees',
			'settings' => 'hr_settings', 'settings_save' => 'hr_settings',
			'topic_save' => 'hr_topics', 'topic_status' => 'hr_topics',
		);
		$key = isset($page_keys[$method]) ? $page_keys[$method] : 'hr';
		// The HR file's save actions check their own right (Employees or Salary & Bank Details, Full) inside the method
		$own_check = in_array($method, array('employee_save', 'salary_add', 'salary_delete'), true);
		$changes = !$own_check && (($this->input->server('REQUEST_METHOD') === 'POST') || preg_match('/^(add_|delete_|update_|generate_|assign_|leave_adjust|leave_type_save|attendance_save|payroll_status|topic_)/', $method));
		$this->is_hr = ($this->is_admin || crm_can($key, $changes ? 'write' : 'read'));
		if ($method !== 'ajax_clock' && !$this->is_hr) {
			$this->session->set_flashdata('error_message', 'You do not have ' . ($changes ? 'Full' : 'View') . ' access to this HR page. Ask your administrator if you need it.');
			redirect(crm_landing_url());
			exit;
		}
	}

	public $cur_uid = 0;
	public $cur_role = 0;
	public $is_admin = false;
	public $is_hr = false;

	/** Active staff only: login enabled and not marked as left (Users / Staff > Mark as left).
	 *  ci_user.status is 1 for everyone, so it cannot tell who is still working. */
	private function _active_sql($alias = '')
	{
		$p = $alias !== '' ? $alias . '.' : '';
		$sql = $p . 'admin_status = 1';
		if ($this->db->table_exists('ci_user_exit')) $sql .= ' AND ' . $p . 'id NOT IN (SELECT user_id FROM ci_user_exit)';
		return '(' . $sql . ')';
	}

	private function _require_hr()
	{
		if (!$this->is_hr) {
			$this->session->set_flashdata('error_message', 'Only HR or Administrators can do this.');
			redirect('manager/profile');
			exit;
		}
	}

	private function _back($default)
	{
		$ret = (string)$this->input->post('return');
		if ($ret !== '' && preg_match('#^manager/[a-z0-9_/\-]+(\#[a-z0-9_\-]+)?$#i', $ret)) {
			redirect($ret);
		}
		redirect($default);
	}

	private function _ymd($value)
	{
		$value = trim((string)$value);
		if ($value === '') {
			return '';
		}
		$ts = strtotime($value);
		return $ts ? date('Y-m-d', $ts) : '';
	}

	private function if_not_login()
	{
		if (!$this->session->userdata('manager_id')) {
			redirect('manager/login');
		}
	}

	private function _check_hr_access()
	{
		$uid = (int)$this->session->userdata('manager_id');
		$role = (int)$this->session->userdata('manager_role');
		$is_admin = ($role === 1 || $uid === 1);
		$is_hr = ($is_admin || has_section_perm('hr'));
		if (!$is_hr) {
			$this->session->set_flashdata('error_message', 'Access denied. HR Management is restricted to HR personnel and Administrators.');
			redirect('manager/dashboard');
			exit;
		}
	}

	public function index()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'HR Management - Seven Strokes CRM',
			'page_heading' => 'HR Management'
		);
		$data['active_tab'] = 'overview';

		$data['total_employees'] = $this->db->where($this->_active_sql(), NULL, FALSE)->count_all_results('ci_user');
		$data['today_attendance'] = $this->db->where('attendance_date', date('Y-m-d'))->count_all_results('ci_attendance');
		$data['pending_leaves'] = $this->db->where('status', 'Pending')->count_all_results('ci_leaves');
		$data['active_targets'] = $this->db->where('status', 'Active')->count_all_results('ci_hr_targets');

		$raw_att = $this->db->select('A.*, U.name as employee_name, U.email as employee_email')
			->from('ci_attendance A')
			->join('ci_user U', 'U.id = A.user_id', 'left')
			->order_by('A.id', 'DESC')
			->limit(10)
			->get()
			->result_array();

		foreach ($raw_att as &$ra) {
			$ra['clock_in'] = !empty($ra['in_time']) ? $ra['in_time'] : '';
			$ra['clock_out'] = !empty($ra['out_time']) ? $ra['out_time'] : '';
		}
		unset($ra);
		$data['recent_attendance'] = $raw_att;

		$data['recent_targets'] = $this->db->select('T.*, U.name as employee_name, Assigner.name as assigner_name')
			->from('ci_hr_targets T')
			->join('ci_user U', 'U.id = T.user_id', 'left')
			->join('ci_user Assigner', 'Assigner.id = T.assigned_by', 'left')
			->order_by('T.id', 'DESC')
			->limit(8)
			->get()
			->result_array();

		$raw_leaves = $this->db->select('L.*, U.name as employee_name')
			->from('ci_leaves L')
			->join('ci_user U', 'U.id = L.user_id', 'left')
			->where('L.status', 'Pending')
			->order_by('L.id', 'DESC')
			->limit(8)
			->get()
			->result_array();

		foreach ($raw_leaves as &$rl) {
			$rl['start_date'] = !empty($rl['from_date']) ? $rl['from_date'] : '';
			$rl['end_date'] = !empty($rl['to_date']) ? $rl['to_date'] : '';
		}
		unset($rl);
		$data['pending_leave_list'] = $raw_leaves;

		$this->load->view('manager/hr/index', $data);
	}

	public function targets()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Assign & Manage Targets - HR',
			'page_heading' => 'Targets Management'
		);
		$data['active_tab'] = 'targets';

		$filter_user = (int)$this->input->get('user_id');
		$filter_month = trim((string)$this->input->get('month'));

		$this->db->select('T.*, U.name as employee_name, U.email as employee_email, Assigner.name as assigner_name')
			->from('ci_hr_targets T')
			->join('ci_user U', 'U.id = T.user_id', 'left')
			->join('ci_user Assigner', 'Assigner.id = T.assigned_by', 'left');

		if ($filter_user > 0) {
			$this->db->where('T.user_id', $filter_user);
		}
		if (!empty($filter_month)) {
			$this->db->where('T.period_month', $filter_month);
		}

		$targets = $this->db->order_by('T.id', 'DESC')->get()->result_array();

		foreach ($targets as &$t) {
			$uid = (int)$t['user_id'];
			$s_date = !empty($t['start_date']) ? $t['start_date'] : (!empty($t['period_month']) ? $t['period_month'] . '-01' : date('Y-m-01'));
			$e_date = !empty($t['end_date']) ? $t['end_date'] : (!empty($t['period_month']) ? date('Y-m-t', strtotime($s_date)) : date('Y-m-t'));

			$calls_count = $this->db->where('user_id', $uid)
				->where('call_status', 'Completed')
				->where('created_at >=', $s_date . ' 00:00:00')
				->where('created_at <=', $e_date . ' 23:59:59')
				->count_all_results('ci_call_logs');

			$leads_count = $this->db->where('action_by', $uid)
				->where('action', 'convert_to_lead')
				->where('created_at >=', $s_date . ' 00:00:00')
				->where('created_at <=', $e_date . ' 23:59:59')
				->count_all_results('ci_pipeline_logs');

			$meetings_count = $this->db->where('created_by', $uid)
				->where('created >=', $s_date)
				->where('created <=', $e_date)
				->count_all_results('ci_meetings');

			$sales_sum = $this->db->select_sum('pricing_amount')
				->where('created_by', $uid)
				->where('status', 'Sales')
				->where('created_at >=', $s_date . ' 00:00:00')
				->where('created_at <=', $e_date . ' 23:59:59')
				->get('ci_orders')
				->row_array();

			$t['achieved_calls'] = (int)$calls_count;
			$t['achieved_leads'] = (int)$leads_count;
			$t['achieved_meetings'] = (int)$meetings_count;
			$t['achieved_amount'] = !empty($sales_sum['pricing_amount']) ? (float)$sales_sum['pricing_amount'] : 0.00;

			$t['target_revenue'] = floatval($t['target_amount']);
			$t['actual_revenue'] = $t['achieved_amount'];
			$t['actual_calls'] = $t['achieved_calls'];
			$t['actual_leads'] = $t['achieved_leads'];
			$t['actual_meetings'] = $t['achieved_meetings'];

			$t['revenue_pct'] = ($t['target_revenue'] > 0) ? round(($t['actual_revenue'] / $t['target_revenue']) * 100, 1) : 0;
			$t['calls_pct'] = ($t['target_calls'] > 0) ? round(($t['actual_calls'] / $t['target_calls']) * 100, 1) : 0;
			$t['leads_pct'] = ($t['target_leads'] > 0) ? round(($t['actual_leads'] / $t['target_leads']) * 100, 1) : 0;

			$this->db->where('id', $t['id'])->update('ci_hr_targets', array(
				'achieved_calls' => $t['achieved_calls'],
				'achieved_leads' => $t['achieved_leads'],
				'achieved_meetings' => $t['achieved_meetings'],
				'achieved_amount' => $t['achieved_amount'],
				'updated_at' => date('Y-m-d H:i:s')
			));
		}
		unset($t);

		$data['targets'] = $targets;
		$data['employees'] = $this->db->select('id, name, email, user_type')
			->where($this->_active_sql(), NULL, FALSE)
			->order_by('name', 'ASC')
			->get('ci_user')
			->result_array();

		$this->load->view('manager/hr/index', $data);
	}

	public function add_target()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') {
			redirect('manager/hr/targets');
			return;
		}

		$user_id = (int)$this->input->post('user_id');
		$target_type = trim((string)$this->input->post('target_type')) ?: 'monthly';
		$period_month = trim((string)$this->input->post('period_month')) ?: date('Y-m');

		$start_date = trim((string)$this->input->post('start_date'));
		if (empty($start_date)) {
			$start_date = $period_month . '-01';
		}
		$end_date = trim((string)$this->input->post('end_date'));
		if (empty($end_date)) {
			$end_date = date('Y-m-t', strtotime($start_date));
		}

		$target_amount = floatval($this->input->post('target_revenue'));
		if ($target_amount <= 0) {
			$target_amount = floatval($this->input->post('target_amount'));
		}
		$target_calls = (int)$this->input->post('target_calls');
		$target_leads = (int)$this->input->post('target_leads');
		$target_meetings = (int)$this->input->post('target_meetings');
		$notes = trim((string)$this->input->post('notes'));
		$cur_uid = (int)$this->session->userdata('manager_id');
		$cur_name = (string)$this->session->userdata('manager_name');

		if ($user_id <= 0) {
			$this->session->set_flashdata('error_message', 'Please select a valid employee.');
			redirect('manager/hr/targets');
			return;
		}

		$target_data = array(
			'user_id' => $user_id,
			'target_type' => $target_type,
			'period_month' => $period_month,
			'start_date' => $start_date,
			'end_date' => $end_date,
			'target_amount' => $target_amount,
			'target_calls' => $target_calls,
			'target_leads' => $target_leads,
			'target_meetings' => $target_meetings,
			'notes' => $notes,
			'status' => 'Active',
			'assigned_by' => $cur_uid,
			'created_at' => date('Y-m-d H:i:s')
		);

		$this->db->insert('ci_hr_targets', $target_data);

		$this->ci_admin_model->add_notification(
			$user_id,
			'target_assigned',
			'New Target Assigned',
			"HR ({$cur_name}) has assigned your target for {$period_month}. Goal: AED " . number_format($target_amount, 2) . ", Calls: {$target_calls}, Leads: {$target_leads}.",
			base_url('manager/hr/targets'),
			'fa fa-bullseye',
			'#0072f0'
		);

		$this->session->set_flashdata('message', 'Target successfully assigned to employee.');
		redirect('manager/hr/targets');
	}

	public function delete_target($id = 0)
	{
		$id = (int)$id;
		if ($id > 0) {
			$this->db->where('id', $id)->delete('ci_hr_targets');
			$this->session->set_flashdata('message', 'Target successfully deleted.');
		}
		redirect('manager/hr/targets');
	}

	/** Lifecycle stages HR can set. "Exited" is not set here: it comes from Users / Staff > Mark as left. */
	public static function lifecycle_stages()
	{
		return array('Onboarding', 'Probation', 'Confirmed', 'Promotion', 'Transfer', 'Notice');
	}

	public function lifecycle()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Employee Lifecycle - HR',
			'page_heading' => 'Employee Lifecycle'
		);
		$data['active_tab'] = 'lifecycle';

		// Name, designation, department, joining date and contacts come from the staff profile (one place to keep them);
		// the lifecycle record only adds the stage, probation / confirmation dates, contract type and notes.
		$has_exit = $this->db->table_exists('ci_user_exit');
		$has_des = $this->db->table_exists('ci_user_designation');
		$this->db->select('U.id, U.id as user_id, U.emp_id, U.name, U.email, U.mobile, U.country_code, U.created, U.department as user_dept, U.office, U.c_doj, U.h_contact_name, U.h_contact_no, U.admin_status, R.title as role_title, L.stage, L.probation_end_date, L.confirmation_date, L.contract_type, L.notes as lifecycle_notes')
			->from('ci_user U')
			->join('ci_roles R', 'R.id = U.user_type', 'left')
			->join('ci_hr_lifecycle L', 'L.user_id = U.id', 'left');
		if ($has_des) { $this->db->select('G.designation'); $this->db->join('ci_user_designation G', 'G.user_id = U.id', 'left'); }
		if ($has_exit) {
			$this->db->select('X.left_on, X.reason as left_reason');
			$this->db->join('ci_user_exit X', 'X.user_id = U.id', 'left');
			$this->db->where('(U.admin_status = 1 OR X.user_id IS NOT NULL)', NULL, FALSE); // working staff, plus those who left
		} else {
			$this->db->where('U.admin_status', 1);
		}
		$raw_list = $this->db->order_by('U.name', 'ASC')->get()->result_array();

		$counts = array(); $depts = array();
		foreach ($raw_list as &$item) {
			$item['left'] = !empty($item['left_on']);
			$item['stage'] = $item['left'] ? 'Exited' : (!empty($item['stage']) ? $item['stage'] : 'Confirmed');
			$item['designation'] = !empty($item['designation']) ? $item['designation'] : '';
			$item['department'] = trim((string)$item['user_dept']);
			$item['date_of_joining'] = !empty($item['c_doj']) ? $item['c_doj'] : '';
			$item['contact'] = trim((string)$item['mobile']) !== '' ? trim(($item['country_code'] !== '' ? '+' . ltrim($item['country_code'], '+') . ' ' : '') . $item['mobile']) : '';
			$counts[$item['stage']] = isset($counts[$item['stage']]) ? $counts[$item['stage']] + 1 : 1;
			if ($item['department'] !== '') $depts[$item['department']] = true;
		}
		unset($item);
		ksort($depts);

		$data['lifecycle_list'] = $raw_list;
		$data['employees'] = $raw_list;
		$data['lc_stages'] = self::lifecycle_stages();
		$data['lc_counts'] = $counts;
		$data['lc_departments'] = array_keys($depts);
		$data['lc_stage'] = trim((string)$this->input->get('stage'));
		$data['lc_dept'] = trim((string)$this->input->get('department'));
		// Link names to the staff profile when the viewer may open it
		$data['profile_base'] = ($this->is_admin || crm_can('hr_staff', 'read')) ? 'manager/hr/staff/view/' : (crm_can('users', 'read') ? 'manager/users/view/' : '');

		$this->load->view('manager/hr/index', $data);
	}

	public function update_lifecycle()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') {
			redirect('manager/hr/lifecycle');
			return;
		}

		$user_id = (int)$this->input->post('user_id');
		if ($user_id <= 0 || !$this->db->where('id', $user_id)->count_all_results('ci_user')) {
			redirect('manager/hr/lifecycle');
			return;
		}

		$stage = trim((string)$this->input->post('stage'));
		if (!in_array($stage, self::lifecycle_stages(), true)) $stage = 'Confirmed';
		$ymd = function ($v) { $v = trim((string)$v); return ($v !== '' && ($t = strtotime($v))) ? date('Y-m-d', $t) : NULL; };
		$contract = trim((string)$this->input->post('contract_type'));
		if (!in_array($contract, array('Permanent', 'Fixed term', 'Part time', 'Intern', 'Consultant'), true)) $contract = 'Permanent';

		// Only the lifecycle's own details; designation, department, joining date and contacts stay in the staff profile
		$save_data = array(
			'user_id' => $user_id,
			'stage' => $stage,
			'probation_end_date' => $ymd($this->input->post('probation_end_date')),
			'confirmation_date' => $ymd($this->input->post('confirmation_date')),
			'contract_type' => $contract,
			'notes' => mb_substr(trim((string)$this->input->post('notes')), 0, 1000),
			'updated_at' => date('Y-m-d H:i:s')
		);

		$exist = $this->db->where('user_id', $user_id)->get('ci_hr_lifecycle')->row_array();
		if (!empty($exist)) {
			$this->db->where('user_id', $user_id)->update('ci_hr_lifecycle', $save_data);
		} else {
			$save_data['created_at'] = date('Y-m-d H:i:s');
			$this->db->insert('ci_hr_lifecycle', $save_data);
		}

		$this->session->set_flashdata('message', 'Employee lifecycle record updated.');
		redirect('manager/hr/lifecycle');
	}

	public function ajax_clock()
	{
		$cur_uid = (int)$this->session->userdata('manager_id');
		$type = trim((string)$this->input->post('type')) ?: 'in';
		$lat = floatval($this->input->post('latitude'));
		$lng = floatval($this->input->post('longitude'));
		$acc = floatval($this->input->post('accuracy'));
		$address = trim((string)$this->input->post('address'));
		$today = date('Y-m-d');
		$now_time = date('H:i:s');
		$ip = $this->input->ip_address();

		$record = $this->db->where('user_id', $cur_uid)->where('attendance_date', $today)->get('ci_attendance')->row_array();

		if ($type === 'in') {
			if (!empty($record)) {
				echo json_encode(array('status' => true, 'message' => 'You have already clocked in today at ' . $record['in_time']));
				return;
			}
			$this->db->insert('ci_attendance', array(
				'user_id' => $cur_uid,
				'attendance_date' => $today,
				'in_time' => $now_time,
				'latitude' => ($lat != 0) ? $lat : NULL,
				'longitude' => ($lng != 0) ? $lng : NULL,
				'accuracy' => ($acc != 0) ? $acc : NULL,
				'location_address' => $address,
				'clock_in_ip' => $ip,
				'status' => $this->_clock_status($cur_uid, $today, $now_time),
				'created_at' => date('Y-m-d H:i:s')
			));
			if ($lat != 0 && $lng != 0) {
				$exist_loc = $this->db->where('user_id', $cur_uid)->get('ci_user_live_locations')->row_array();
				$loc_data = array(
					'user_id' => $cur_uid,
					'latitude' => $lat,
					'longitude' => $lng,
					'accuracy' => ($acc != 0) ? $acc : NULL,
					'tracking_active' => 1,
					'updated_at' => date('Y-m-d H:i:s')
				);
				if (!empty($exist_loc)) {
					$this->db->where('user_id', $cur_uid)->update('ci_user_live_locations', $loc_data);
				} else {
					$this->db->insert('ci_user_live_locations', $loc_data);
				}
			}
			$this->ci_admin_model->log_admin_action('clock_in', 'Staff Clocked In via HR GPS', 'SUCCESS', $cur_uid, ($lat != 0) ? $lat : null, ($lng != 0) ? $lng : null, ($acc != 0) ? $acc : null);
			echo json_encode(array('status' => true, 'message' => 'Clock In recorded successfully at ' . $now_time));
		} else {
			if (empty($record)) {
				echo json_encode(array('status' => false, 'message' => 'No active Clock In found for today. Please clock in first.'));
				return;
			}
			$in_ts = strtotime($record['attendance_date'] . ' ' . $record['in_time']);
			$out_ts = time();
			$hours = round(($out_ts - $in_ts) / 3600, 2);

			$this->db->where('id', $record['id'])->update('ci_attendance', array(
				'out_time' => $now_time,
				'total_hours' => $hours,
				'clock_out_ip' => $ip
			));
			if ($lat != 0 && $lng != 0) {
				$this->db->where('user_id', $cur_uid)->update('ci_user_live_locations', array(
					'latitude' => $lat,
					'longitude' => $lng,
					'accuracy' => ($acc != 0) ? $acc : NULL,
					'tracking_active' => 0,
					'updated_at' => date('Y-m-d H:i:s')
				));
			} else {
				$this->db->where('user_id', $cur_uid)->update('ci_user_live_locations', array(
					'tracking_active' => 0,
					'updated_at' => date('Y-m-d H:i:s')
				));
			}
			$this->ci_admin_model->log_admin_action('clock_out', 'Staff Clocked Out via HR GPS (Hours: ' . $hours . ')', 'SUCCESS', $cur_uid, ($lat != 0) ? $lat : null, ($lng != 0) ? $lng : null, ($acc != 0) ? $acc : null);
			echo json_encode(array('status' => true, 'message' => 'Clock Out recorded at ' . $now_time . ' (Total hours: ' . $hours . ')'));
		}
	}

	public function shifts()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Shift Schedules - HR',
			'page_heading' => 'Shift Management'
		);
		$data['active_tab'] = 'shifts';

		$shifts = $this->db->order_by('id', 'ASC')->get('ci_hr_shifts')->result_array();
		foreach ($shifts as &$s) {
			$s['name'] = !empty($s['shift_name']) ? $s['shift_name'] : 'Shift #' . $s['id'];
		}
		unset($s);
		$data['shifts'] = $shifts;

		$assignments = $this->db->select('SA.*, S.shift_name, S.start_time, S.end_time, U.name as employee_name')
			->from('ci_hr_shift_assignments SA')
			->join('ci_hr_shifts S', 'S.id = SA.shift_id', 'left')
			->join('ci_user U', 'U.id = SA.user_id', 'left')
			->order_by('SA.id', 'DESC')
			->get()
			->result_array();

		$data['assignments'] = $assignments;
		$data['employees'] = $this->db->select('id, name, email')->where($this->_active_sql(), NULL, FALSE)->order_by('name', 'ASC')->get('ci_user')->result_array();

		$this->load->view('manager/hr/index', $data);
	}

	public function add_shift()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') {
			redirect('manager/hr/shifts');
			return;
		}

		$shift_name = trim((string)$this->input->post('shift_name'));
		if (empty($shift_name)) {
			$shift_name = trim((string)$this->input->post('name'));
		}
		$start_time = trim((string)$this->input->post('start_time'));
		$end_time = trim((string)$this->input->post('end_time'));
		$grace = (int)$this->input->post('grace_period_mins') ?: 15;
		$hours = floatval($this->input->post('working_hours')) ?: 8.00;

		if (!empty($shift_name) && !empty($start_time) && !empty($end_time)) {
			$this->db->insert('ci_hr_shifts', array(
				'shift_name' => $shift_name,
				'start_time' => $start_time,
				'end_time' => $end_time,
				'grace_period_mins' => $grace,
				'working_hours' => $hours,
				'status' => 'Active',
				'created_at' => date('Y-m-d H:i:s')
			));
			$this->session->set_flashdata('message', 'New shift added successfully.');
		}
		redirect('manager/hr/shifts');
	}

	public function assign_shift()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') {
			redirect('manager/hr/shifts');
			return;
		}

		$user_id = (int)$this->input->post('user_id');
		$shift_id = (int)$this->input->post('shift_id');
		$effective_from = trim((string)$this->input->post('effective_from')) ?: date('Y-m-d');

		if ($user_id > 0 && $shift_id > 0) {
			$this->db->insert('ci_hr_shift_assignments', array(
				'user_id' => $user_id,
				'shift_id' => $shift_id,
				'effective_from' => $effective_from,
				'created_at' => date('Y-m-d H:i:s')
			));
			$this->session->set_flashdata('message', 'Employee shift assigned.');
		}
		redirect('manager/hr/shifts');
	}

	/* ------------------------------------------------------------------
	 * Time & Leave: attendance (daily / monthly), leave types, balances,
	 * requests with approval by HR or the person's manager, team calendar.
	 * Public holidays are the "Public Holiday" entries of Events & Festivals.
	 * ------------------------------------------------------------------ */

	/** UAE leave types (Federal Decree-Law 33 of 2021). pay: full / half / unpaid / sick_uae / maternity_uae; count: working / calendar. */
	public static function leave_type_defaults()
	{
		return array(
			array('Annual Leave', 30, 'full', 'working', 'uae_annual', 0, 'any', 0, 'After 6 months: 2 days a month; after 1 year: 30 days a year'),
			array('Sick Leave', 90, 'sick_uae', 'calendar', '', 1, 'any', 3, '15 days full pay, 30 days half pay, 45 days unpaid; medical certificate needed'),
			array('Maternity Leave', 60, 'maternity_uae', 'calendar', '', 1, 'female', 0, '45 days full pay, 15 days half pay'),
			array('Parental Leave', 5, 'full', 'working', '', 0, 'any', 0, 'Within 6 months of the birth'),
			array('Bereavement Leave', 5, 'full', 'calendar', '', 0, 'any', 0, '5 days for a spouse, 3 days for a close relative'),
			array('Hajj Leave', 30, 'unpaid', 'calendar', '', 0, 'any', 0, 'Once during employment'),
			array('Study Leave', 10, 'full', 'working', '', 1, 'any', 24, 'For exams, after 2 years of service'),
			array('Casual Leave', 0, 'full', 'working', '', 0, 'any', 0, 'Company leave; set a yearly limit if you use it'),
			array('Emergency Leave', 0, 'full', 'working', '', 0, 'any', 0, 'Company leave; set a yearly limit if you use it'),
			array('Unpaid Leave', 0, 'unpaid', 'working', '', 0, 'any', 0, 'No limit; deducted from salary'),
		);
	}

	public static function leave_pay_labels()
	{
		return array('full' => 'Full pay', 'half' => 'Half pay', 'unpaid' => 'Unpaid', 'sick_uae' => 'Sick leave pay (UAE)', 'maternity_uae' => 'Maternity pay (UAE)');
	}

	public static function attendance_statuses()
	{
		return array('Present', 'Late', 'Half Day', 'Work From Home', 'Absent', 'On Leave');
	}

	private function _leave_tables()
	{
		static $done = false;
		if ($done) return;
		$done = true;
		$this->_hr_tables();
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_hr_leave_types (
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			name VARCHAR(50) NOT NULL,
			days_per_year DECIMAL(5,1) NOT NULL DEFAULT 0,
			pay VARCHAR(20) NOT NULL DEFAULT 'full',
			count_mode VARCHAR(10) NOT NULL DEFAULT 'working',
			accrual VARCHAR(20) NOT NULL DEFAULT '',
			needs_document TINYINT(1) NOT NULL DEFAULT 0,
			gender VARCHAR(10) NOT NULL DEFAULT 'any',
			min_service_months INT NOT NULL DEFAULT 0,
			carry_max DECIMAL(5,1) NOT NULL DEFAULT 0,
			note VARCHAR(255) NOT NULL DEFAULT '',
			active TINYINT(1) NOT NULL DEFAULT 1,
			sort INT NOT NULL DEFAULT 0,
			UNIQUE KEY name (name)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_hr_leave_adjust (
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			user_id INT NOT NULL, year INT NOT NULL, leave_type VARCHAR(50) NOT NULL,
			days DECIMAL(5,1) NOT NULL, reason VARCHAR(255) NOT NULL DEFAULT '',
			created_by INT NULL, created_at DATETIME NULL,
			KEY user_year (user_id, year)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		if (!$this->db->count_all_results('ci_hr_leave_types')) {
			foreach (self::leave_type_defaults() as $i => $t) {
				$this->db->insert('ci_hr_leave_types', array('name' => $t[0], 'days_per_year' => $t[1], 'pay' => $t[2], 'count_mode' => $t[3], 'accrual' => $t[4],
					'needs_document' => $t[5], 'gender' => $t[6], 'min_service_months' => $t[7], 'note' => $t[8], 'sort' => $i + 1));
			}
		}
		// Half days, and who approved or rejected with a note
		$cols = array();
		foreach ($this->db->query('SHOW COLUMNS FROM ci_leaves')->result_array() as $c) $cols[$c['Field']] = $c['Type'];
		if (isset($cols['total_days']) && stripos($cols['total_days'], 'decimal') === false) $this->db->query('ALTER TABLE ci_leaves MODIFY total_days DECIMAL(5,1) NOT NULL DEFAULT 1');
		if (!isset($cols['action_note'])) $this->db->query("ALTER TABLE ci_leaves ADD action_note VARCHAR(255) NULL, ADD actioned_at DATETIME NULL");
		$this->db->data_cache = array();
	}

	/** Leave types by name (active only unless $all). */
	private function _leave_types($all = false)
	{
		$this->_leave_tables();
		if (!$all) $this->db->where('active', 1);
		$out = array();
		foreach ($this->db->order_by('sort ASC, name ASC')->get('ci_hr_leave_types')->result_array() as $t) $out[$t['name']] = $t;
		return $out;
	}

	/** Public holidays (Events & Festivals > Public Holiday) as date => title. */
	private function _holidays($from, $to)
	{
		$out = array();
		$rows = $this->db->select('title, start_date, end_date')->from('ci_events')
			->where("LOWER(event_type) IN ('public_holiday', 'holiday')", null, false)
			->where("(status IS NULL OR status = 'active')", null, false)
			->where('start_date <=', $to . ' 23:59:59')
			->where("(COALESCE(end_date, start_date) >= " . $this->db->escape($from . ' 00:00:00') . ")", null, false)
			->get()->result_array();
		foreach ($rows as $r) {
			$d = strtotime(substr($r['start_date'], 0, 10));
			$e = strtotime(substr($r['end_date'] ? $r['end_date'] : $r['start_date'], 0, 10));
			for (; $d <= $e; $d += 86400) {
				$k = date('Y-m-d', $d);
				if ($k >= $from && $k <= $to) $out[$k] = $r['title'];
			}
		}
		return $out;
	}

	private function _weekend()
	{
		$s = $this->_hr_settings();
		return array_filter(explode(',', $s['weekend']));
	}

	/** Days a leave takes: calendar days, or working days (no weekend, no public holidays). */
	private function _count_leave_days($from, $to, $mode, $half = false)
	{
		if ($half) return 0.5;
		if ($mode !== 'working') return (float)((strtotime($to) - strtotime($from)) / 86400 + 1);
		$wk = $this->_weekend();
		$hol = $this->_holidays($from, $to);
		$n = 0;
		for ($d = strtotime($from); $d <= strtotime($to); $d += 86400) {
			if (in_array(date('D', $d), $wk, true) || isset($hol[date('Y-m-d', $d)])) continue;
			$n++;
		}
		return (float)$n;
	}

	/** Completed months of service on a date. */
	private static function _service_months($doj, $on)
	{
		$t = strtotime((string)$doj);
		if (!$doj || !$t || $t > strtotime($on)) return 0;
		$a = new DateTime(date('Y-m-d', $t));
		$b = new DateTime($on);
		$d = $a->diff($b);
		return $d->y * 12 + $d->m;
	}

	/**
	 * Leave balances for a year: [user_id][type] = entitled (null = no limit), adjust, taken, pending, left.
	 * Annual leave (UAE): nothing in the first 6 months, then 2 days a month, then the yearly days after one year.
	 */
	private function _leave_balances($users, $year)
	{
		$types = $this->_leave_types();
		$set = $this->_hr_settings();
		$ids = array();
		foreach ($users as $u) $ids[] = (int)$u['id'];
		if (!$ids) return array();
		$emp = array();
		foreach ($this->db->select('user_id, annual_leave_days')->where_in('user_id', $ids)->get('ci_hr_employee')->result_array() as $r) $emp[(int)$r['user_id']] = $r;
		$used = array();
		foreach ($this->db->select('user_id, leave_type, status, SUM(total_days) AS d', false)->where_in('user_id', $ids)->where_in('status', array('Approved', 'Pending'))
			->where('YEAR(from_date) = ' . (int)$year, null, false)->group_by('user_id, leave_type, status')->get('ci_leaves')->result_array() as $r)
			$used[(int)$r['user_id']][$r['leave_type']][$r['status']] = (float)$r['d'];
		$adj = array();
		foreach ($this->db->select('user_id, leave_type, SUM(days) AS d', false)->where_in('user_id', $ids)->where('year', (int)$year)->group_by('user_id, leave_type')->get('ci_hr_leave_adjust')->result_array() as $r)
			$adj[(int)$r['user_id']][$r['leave_type']] = (float)$r['d'];
		$on = ((int)$year < (int)date('Y')) ? $year . '-12-31' : date('Y-m-d');
		$out = array();
		foreach ($users as $u) {
			$uid = (int)$u['id'];
			$months = self::_service_months(isset($u['c_doj']) ? $u['c_doj'] : '', $on);
			foreach ($types as $name => $t) {
				$g = strtolower((string)(isset($u['gender']) ? $u['gender'] : ''));
				if ($t['gender'] !== 'any' && $g !== '' && $g !== $t['gender']) continue;
				$ent = (float)$t['days_per_year'] > 0 ? (float)$t['days_per_year'] : null;
				if ($t['accrual'] === 'uae_annual') {
					$yearly = (isset($emp[$uid]) && $emp[$uid]['annual_leave_days'] !== null && $emp[$uid]['annual_leave_days'] !== '') ? (float)$emp[$uid]['annual_leave_days'] : ((float)$set['annual_leave_days'] ?: (float)$t['days_per_year']);
					$known = !empty($u['c_doj']) && strtotime($u['c_doj']);
					$ent = !$known ? $yearly : ($months < 6 ? 0.0 : ($months < 12 ? min($yearly, 2.0 * $months) : $yearly));
				} elseif ($ent !== null && (int)$t['min_service_months'] > 0 && !empty($u['c_doj']) && $months < (int)$t['min_service_months']) {
					$ent = 0.0;
				}
				$a = isset($adj[$uid][$name]) ? $adj[$uid][$name] : 0.0;
				$tk = isset($used[$uid][$name]['Approved']) ? $used[$uid][$name]['Approved'] : 0.0;
				$pd = isset($used[$uid][$name]['Pending']) ? $used[$uid][$name]['Pending'] : 0.0;
				$out[$uid][$name] = array('entitled' => $ent, 'adjust' => $a, 'taken' => $tk, 'pending' => $pd, 'left' => $ent === null ? null : $ent + $a - $tk - $pd);
			}
		}
		return $out;
	}

	/** Working people for HR lists (no system logins). */
	private function _people($with_left = false)
	{
		$this->db->select('U.id, U.name, U.emp_id, U.department, U.c_doj, U.gender, U.manager, U.user_type')->from('ci_user U')
			->join('ci_roles R', 'R.id = U.user_type', 'left')->where("(R.role_type IS NULL OR R.role_type <> 'system')", null, false);
		if (!$with_left) $this->db->where($this->_active_sql('U'), NULL, FALSE);
		return $this->db->order_by('U.name', 'ASC')->get()->result_array();
	}

	/** Shift of each person on a date range: [user_id] => list of assignments (latest first) with the shift times. */
	private function _shift_map()
	{
		$out = array();
		$rows = $this->db->select('SA.user_id, SA.effective_from, SA.effective_to, S.shift_name, S.start_time, S.end_time, S.grace_period_mins, S.working_hours')
			->from('ci_hr_shift_assignments SA')->join('ci_hr_shifts S', 'S.id = SA.shift_id')
			->order_by('SA.effective_from DESC, SA.id DESC')->get()->result_array();
		foreach ($rows as $r) $out[(int)$r['user_id']][] = $r;
		return $out;
	}

	private static function _shift_on($map, $uid, $date)
	{
		if (empty($map[$uid])) return null;
		foreach ($map[$uid] as $s) {
			if ($s['effective_from'] <= $date && (empty($s['effective_to']) || $s['effective_to'] >= $date)) return $s;
		}
		return null;
	}

	/** Day status for one person: code (P present, L late, H half day, W work from home, A absent, LV leave, HO holiday, WE weekend, NY not in yet, F future, NJ not joined) and label. */
	private function _day_status($rec, $date, $shift, $leave, $holiday, $is_weekend, $doj)
	{
		$today = date('Y-m-d');
		if ($rec) {
			$st = (string)$rec['status'];
			if ($st === 'Present' && $shift && !empty($rec['in_time'])) {
				$limit = strtotime($date . ' ' . $shift['start_time']) + 60 * (int)$shift['grace_period_mins'];
				if (strtotime($date . ' ' . $rec['in_time']) > $limit) $st = 'Late';
			}
			$codes = array('Present' => 'P', 'Late' => 'L', 'Half Day' => 'H', 'Work From Home' => 'W', 'Absent' => 'A', 'On Leave' => 'LV');
			return array(isset($codes[$st]) ? $codes[$st] : 'P', $st);
		}
		if ($doj && strtotime($doj) && $date < date('Y-m-d', strtotime($doj))) return array('NJ', 'Not joined yet');
		if ($leave) return array('LV', $leave['leave_type'] . ($leave['status'] === 'Pending' ? ' (pending)' : ''));
		if ($holiday !== null) return array('HO', $holiday);
		if ($is_weekend) return array('WE', 'Weekend');
		if ($date > $today) return array('F', '');
		if ($date === $today) {
			$start = $shift ? strtotime($today . ' ' . $shift['start_time']) + 60 * (int)$shift['grace_period_mins'] : strtotime($today . ' 12:00:00');
			if (time() < $start) return array('NY', 'Not in yet');
		}
		return array('A', 'Absent');
	}

	/** Leaves (approved and pending) touching a date range: [user_id][date] = leave row. */
	private function _leave_days_map($from, $to, $statuses = array('Approved'))
	{
		$out = array();
		$rows = $this->db->where_in('status', $statuses)->where('from_date <=', $to)->where('to_date >=', $from)->get('ci_leaves')->result_array();
		foreach ($rows as $l) {
			for ($d = max(strtotime($l['from_date']), strtotime($from)); $d <= min(strtotime($l['to_date']), strtotime($to)); $d += 86400) {
				$k = date('Y-m-d', $d);
				if (!isset($out[(int)$l['user_id']][$k]) || $l['status'] === 'Approved') $out[(int)$l['user_id']][$k] = $l;
			}
		}
		return $out;
	}

	public function attendance()
	{
		$this->_leave_tables();
		$view = $this->input->get('view') === 'month' ? 'month' : 'day';
		$date = $this->_ymd($this->input->get('date')) ?: date('Y-m-d');
		$month = preg_match('/^\d{4}-\d{2}$/', (string)$this->input->get('month')) ? $this->input->get('month') : date('Y-m');
		$from = $view === 'day' ? $date : $month . '-01';
		$to = $view === 'day' ? $date : date('Y-m-t', strtotime($from));

		$people = $this->_people();
		$recs = array();
		foreach ($this->db->where('attendance_date >=', $from)->where('attendance_date <=', $to)->get('ci_attendance')->result_array() as $r) $recs[(int)$r['user_id']][$r['attendance_date']] = $r;
		$leaves = $this->_leave_days_map($from, $to);
		$hol = $this->_holidays($from, $to);
		$wk = $this->_weekend();
		$shifts = $this->_shift_map();

		$days = array();
		for ($d = strtotime($from); $d <= strtotime($to); $d += 86400) $days[] = date('Y-m-d', $d);
		$rows = array();
		$k = array('P' => 0, 'L' => 0, 'A' => 0, 'LV' => 0, 'out' => 0, 'NY' => 0, 'W' => 0, 'H' => 0);
		foreach ($people as $u) {
			$uid = (int)$u['id'];
			$row = array('u' => $u, 'days' => array(), 'tot' => array('P' => 0, 'L' => 0, 'H' => 0, 'W' => 0, 'A' => 0, 'LV' => 0, 'hours' => 0.0));
			foreach ($days as $dd) {
				$rec = isset($recs[$uid][$dd]) ? $recs[$uid][$dd] : null;
				$shift = self::_shift_on($shifts, $uid, $dd);
				$st = $this->_day_status($rec, $dd, $shift, isset($leaves[$uid][$dd]) ? $leaves[$uid][$dd] : null, isset($hol[$dd]) ? $hol[$dd] : null, in_array(date('D', strtotime($dd)), $wk, true), $u['c_doj']);
				$row['days'][$dd] = array('code' => $st[0], 'label' => $st[1], 'rec' => $rec, 'shift' => $shift);
				if (isset($row['tot'][$st[0]])) $row['tot'][$st[0]]++;
				if ($rec) $row['tot']['hours'] += (float)$rec['total_hours'];
				if ($view === 'day') {
					if (isset($k[$st[0]])) $k[$st[0]]++;
					if ($rec && !empty($rec['in_time']) && empty($rec['out_time']) && $dd < date('Y-m-d')) $k['out']++;
				}
			}
			if ($view === 'day' && $row['days'][$date]['code'] === 'NJ') continue;
			$rows[] = $row;
		}

		if ($view === 'month' && $this->input->get('csv')) {
			$this->ci_admin_model->log_admin_action('hr_attendance_export', 'Downloaded attendance for ' . $month, 'SUCCESS');
			while (ob_get_level()) ob_end_clean();
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename="attendance_' . $month . '.csv"');
			$out = fopen('php://output', 'w');
			fwrite($out, "\xEF\xBB\xBF");
			$head = array('Employee', 'Employee ID', 'Department');
			foreach ($days as $dd) $head[] = date('d', strtotime($dd));
			fputcsv($out, array_merge($head, array('Present', 'Late', 'Half day', 'From home', 'Absent', 'Leave', 'Hours')));
			$safe = function ($v) { $v = (string)$v; return ($v !== '' && strpos('=+-@', $v[0]) !== false) ? "'" . $v : $v; };
			foreach ($rows as $r) {
				$line = array($safe($r['u']['name']), $safe($r['u']['emp_id']), $safe($r['u']['department']));
				foreach ($days as $dd) { $c = $r['days'][$dd]['code']; $line[] = in_array($c, array('F', 'NJ', 'NY'), true) ? '' : $c; }
				$t = $r['tot'];
				fputcsv($out, array_merge($line, array($t['P'], $t['L'], $t['H'], $t['W'], $t['A'], $t['LV'], round($t['hours'], 2))));
			}
			fputcsv($out, array());
			fputcsv($out, array('P present, L late, H half day, W work from home, A absent, LV leave, HO public holiday, WE weekend'));
			fclose($out);
			exit;
		}

		$me = (int)$this->cur_uid;
		$today_rec = $this->db->where('user_id', $me)->where('attendance_date', date('Y-m-d'))->get('ci_attendance')->row_array();
		$data = array();
		$data['info'] = array('title' => 'Attendance - HR', 'page_heading' => 'Attendance');
		$data['view'] = $view;
		$data['date'] = $date;
		$data['month'] = $month;
		$data['days'] = $days;
		$data['rows'] = $rows;
		$data['kpi'] = $k;
		$data['holidays'] = $hol;
		$data['weekend'] = $wk;
		$data['today_rec'] = $today_rec;
		$data['can_edit'] = $this->is_admin || crm_can('hr_attendance', 'write');
		$data['statuses'] = self::attendance_statuses();
		$data['depts'] = array_values(array_unique(array_filter(array_map(function ($u) { return trim((string)$u['department']); }, $people))));
		sort($data['depts']);
		$this->load->view('manager/hr/attendance', $data);
	}

	/** HR correction: add or change one person's attendance for a day. */
	public function attendance_save()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') redirect('manager/hr/attendance');
		$uid = (int)$this->input->post('user_id');
		$date = $this->_ymd($this->input->post('date'));
		$back = 'manager/hr/attendance?date=' . $date;
		$u = $uid ? $this->db->select('id, name')->where('id', $uid)->get('ci_user')->row_array() : array();
		if (empty($u) || $date === '' || $date > date('Y-m-d')) {
			$this->session->set_flashdata('error_message', 'Choose an employee and a date that is not in the future.');
			redirect('manager/hr/attendance');
		}
		$status = (string)$this->input->post('status');
		if (!in_array($status, self::attendance_statuses(), true)) $status = 'Present';
		$tm = function ($k) { $v = trim((string)$this->input->post($k)); return preg_match('/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', $v) ? (strlen($v) === 5 ? $v . ':00' : $v) : null; };
		$in = $tm('in_time');
		$out = $tm('out_time');
		if (in_array($status, array('Absent', 'On Leave'), true)) { $in = null; $out = null; }
		if ($in && $out && $out < $in) {
			$this->session->set_flashdata('error_message', 'The clock-out time is before the clock-in time.');
			redirect($back);
		}
		$hours = ($in && $out) ? round((strtotime($date . ' ' . $out) - strtotime($date . ' ' . $in)) / 3600, 2) : 0;
		$note = mb_substr(trim((string)$this->input->post('note')), 0, 200);
		$old = $this->db->where('user_id', $uid)->where('attendance_date', $date)->get('ci_attendance')->row_array();
		$row = array('in_time' => $in, 'out_time' => $out, 'total_hours' => $hours, 'status' => $status,
			'notes' => trim('Set by ' . $this->session->userdata('manager_name') . ' on ' . date('d-m-Y') . ($note !== '' ? ': ' . $note : '')));
		if ($old) $this->db->where('id', $old['id'])->update('ci_attendance', $row);
		else { $row += array('user_id' => $uid, 'attendance_date' => $date, 'created_at' => date('Y-m-d H:i:s')); $this->db->insert('ci_attendance', $row); }
		$this->ci_admin_model->log_admin_action('hr_attendance_edit', ($old ? 'Changed' : 'Added') . ' attendance of ' . $u['name'] . ' (#' . $uid . ') on ' . $date . ': ' . $status . ($in ? ' ' . substr($in, 0, 5) : '') . ($out ? '-' . substr($out, 0, 5) : '')
			. ($old ? ' (was ' . $old['status'] . ' ' . substr((string)$old['in_time'], 0, 5) . '-' . substr((string)$old['out_time'], 0, 5) . ')' : ''), 'SUCCESS');
		$this->session->set_flashdata('message', 'Attendance of ' . $u['name'] . ' on ' . date('d-m-Y', strtotime($date)) . ' saved.');
		redirect($back);
	}

	/** Leave requests: HR sees and decides everyone's; others see their own and their team's (people who report to them). */
	public function leaves()
	{
		$this->_leave_tables();
		$me = (int)$this->cur_uid;
		$see_all = $this->is_hr || crm_can('hr_leaves', 'read');
		$team = array();
		foreach ($this->db->select('id')->where('manager', $me)->where('id <>', $me)->get('ci_user')->result_array() as $r) $team[] = (int)$r['id'];

		$this->db->select('L.*, U.name AS employee_name, U.emp_id, U.department, U.manager, A.name AS approver_name')->from('ci_leaves L')
			->join('ci_user U', 'U.id = L.user_id', 'left')->join('ci_user A', 'A.id = L.approved_by', 'left');
		if (!$see_all) {
			$ids = array_merge(array($me), $team);
			$this->db->where_in('L.user_id', $ids);
		}
		$rows = $this->db->order_by('L.from_date', 'DESC')->order_by('L.id', 'DESC')->limit(1000)->get()->result_array();
		usort($rows, function ($a, $b) { return ($b['status'] === 'Pending') - ($a['status'] === 'Pending'); });
		foreach ($rows as &$r) {
			$r['can_decide'] = $r['status'] === 'Pending' && ((int)$r['user_id'] !== $me || $this->is_admin) && ($this->is_hr || (int)$r['manager'] === $me);
			$r['can_cancel'] = ($r['status'] === 'Pending' && ((int)$r['user_id'] === $me || $this->is_hr)) || ($r['status'] === 'Approved' && $this->is_hr && $r['to_date'] >= date('Y-m-d'));
		}
		unset($r);

		$types = $this->_leave_types();
		$people = $this->is_hr ? $this->_people() : array();
		$meu = $this->db->select('id, c_doj, gender')->where('id', $me)->get('ci_user')->row_array();
		$bal = $this->_leave_balances(array($meu), (int)date('Y'));
		$data = array();
		$data['info'] = array('title' => 'Leave Requests - HR', 'page_heading' => $see_all ? 'Leave Requests' : 'My Holiday Requests');
		$data['rows'] = $rows;
		$data['is_hr'] = $this->is_hr;
		$data['see_all'] = $see_all;
		$data['has_team'] = !empty($team);
		$data['me'] = $me;
		$data['types'] = $types;
		$data['people'] = $people;
		$data['my_bal'] = isset($bal[$me]) ? $bal[$me] : array();
		$data['pay_labels'] = self::leave_pay_labels();
		$data['holidays'] = $this->_holidays(date('Y-01-01'), (date('Y') + 1) . '-12-31');
		$data['weekend'] = $this->_weekend();
		$data['pending_n'] = count(array_filter($rows, function ($r) { return $r['can_decide']; }));
		$this->load->view('manager/hr/leaves', $data);
	}

	public function apply_leave()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') redirect('manager/hr/leaves');
		$this->_leave_tables();
		// Employees request leave for themselves; HR may file for anyone (and then it is approved straight away if they tick it)
		$user_id = $this->cur_uid;
		if ($this->is_hr && (int)$this->input->post('user_id') > 0) $user_id = (int)$this->input->post('user_id');
		$types = $this->_leave_types();
		$leave_type = trim((string)$this->input->post('leave_type'));
		if (!isset($types[$leave_type])) {
			$this->session->set_flashdata('error_message', 'Choose a leave type.');
			$this->_back('manager/hr/leaves');
		}
		$t = $types[$leave_type];
		$from_date = $this->_ymd($this->input->post('from_date') ?: $this->input->post('start_date'));
		$to_date = $this->_ymd($this->input->post('to_date') ?: $this->input->post('end_date'));
		$reason = mb_substr(trim((string)$this->input->post('reason')), 0, 1000);
		if ($from_date === '' || $to_date === '') {
			$this->session->set_flashdata('error_message', 'Please choose a valid start and end date.');
			$this->_back('manager/hr/leaves');
		}
		if ($to_date < $from_date) {
			$this->session->set_flashdata('error_message', 'The end date cannot be before the start date.');
			$this->_back('manager/hr/leaves');
		}
		$half = $this->input->post('half_day') && $from_date === $to_date;
		$this->load->model('hr_selfservice_model');
		$clash = $this->hr_selfservice_model->leave_overlap($user_id, $from_date, $to_date);
		if (!empty($clash)) {
			$this->session->set_flashdata('error_message', 'These dates overlap an existing ' . strtolower($clash['status']) . ' request (' . date('d M Y', strtotime($clash['from_date'])) . ' to ' . date('d M Y', strtotime($clash['to_date'])) . ').');
			$this->_back('manager/hr/leaves');
		}
		$days = $this->_count_leave_days($from_date, $to_date, $t['count_mode'], $half);
		if ($days <= 0) {
			$this->session->set_flashdata('error_message', 'These dates are all weekend or public holidays, so no leave is needed.');
			$this->_back('manager/hr/leaves');
		}
		$emp = $this->db->select('id, name, c_doj, gender, manager')->where('id', $user_id)->get('ci_user')->row_array();
		$bal = $this->_leave_balances(array($emp), (int)substr($from_date, 0, 4));
		$b = isset($bal[$user_id][$leave_type]) ? $bal[$user_id][$leave_type] : null;
		if ($b === null) {
			$this->session->set_flashdata('error_message', $leave_type . ' is not available for this employee.');
			$this->_back('manager/hr/leaves');
		}
		$over = ($b['left'] !== null && $days > $b['left']);
		if ($over && !$this->is_hr) {
			$this->session->set_flashdata('error_message', 'Not enough ' . $leave_type . ': ' . self::_fmt_days($b['left']) . ' left, this request needs ' . self::_fmt_days($days) . '. Ask HR, or choose Unpaid Leave for the rest.');
			$this->_back('manager/hr/leaves');
		}
		$approve_now = $this->is_hr && $user_id !== $this->cur_uid && $this->input->post('approve_now');
		$this->db->insert('ci_leaves', array(
			'user_id' => $user_id, 'leave_type' => $leave_type, 'from_date' => $from_date, 'to_date' => $to_date,
			'total_days' => $days, 'reason' => $reason, 'status' => $approve_now ? 'Approved' : 'Pending',
			'approved_by' => $approve_now ? $this->cur_uid : null, 'actioned_at' => $approve_now ? date('Y-m-d H:i:s') : null,
			'action_note' => $approve_now ? 'Entered by HR' . ($over ? ' (more than the balance)' : '') : null,
			'created_at' => date('Y-m-d H:i:s')
		));
		$emp_name = !empty($emp['name']) ? $emp['name'] : ('employee ID #' . $user_id);
		$period = date('d M Y', strtotime($from_date)) . ($to_date !== $from_date ? ' to ' . date('d M Y', strtotime($to_date)) : '');
		if (!$approve_now) {
			$msg = "{$emp_name} requested " . self::_fmt_days($days) . " of {$leave_type}: {$period}.";
			$this->ci_admin_model->notify_role(array(1, 7), 'leave_requested', 'New Leave Request', $msg, base_url('manager/hr/leaves'), 'fa fa-calendar-check-o', '#0072f0');
			if (!empty($emp['manager']) && (int)$emp['manager'] !== $user_id) $this->ci_admin_model->add_notification((int)$emp['manager'], 'leave_requested', 'Leave request from your team', $msg, base_url('manager/hr/leaves'), 'fa fa-calendar-check-o', '#0072f0');
		} else {
			$this->ci_admin_model->add_notification($user_id, 'leave_update', 'Leave recorded', "HR recorded {$leave_type} for you: {$period}.", base_url('manager/hr/leaves'), 'fa fa-check-circle', '#16a34a');
		}
		$this->ci_admin_model->log_admin_action('hr_leave_request', ($approve_now ? 'Recorded approved ' : 'Requested ') . $leave_type . ' for ' . $emp_name . ': ' . $period . ' (' . self::_fmt_days($days) . ')', 'SUCCESS');
		$this->session->set_flashdata('message', $approve_now ? $leave_type . ' recorded for ' . $emp_name . ' (' . self::_fmt_days($days) . ').' : 'Holiday request for ' . self::_fmt_days($days) . ' sent for approval.');
		$this->_back('manager/hr/leaves');
	}

	private static function _fmt_days($d)
	{
		$d = (float)$d;
		$s = (floor($d) == $d) ? (string)(int)$d : rtrim(rtrim(number_format($d, 1), '0'), '.');
		return $s . ' day' . ($d == 1 ? '' : 's');
	}

	public function cancel_leave($id = 0)
	{
		$this->_leave_tables();
		$id = (int)$id;
		if ($this->input->server('REQUEST_METHOD') !== 'POST' || $id <= 0) {
			redirect('manager/hr/leaves');
			return;
		}
		$leave = $this->db->where('id', $id)->get('ci_leaves')->row_array();
		if (empty($leave) || ((int)$leave['user_id'] !== $this->cur_uid && !$this->is_hr)) {
			$this->session->set_flashdata('error_message', 'Leave request not found.');
		} elseif ($leave['status'] === 'Pending' || ($leave['status'] === 'Approved' && $this->is_hr && $leave['to_date'] >= date('Y-m-d'))) {
			$this->db->where('id', $id)->update('ci_leaves', array('status' => 'Cancelled', 'actioned_at' => date('Y-m-d H:i:s'),
				'action_note' => $leave['status'] === 'Approved' ? 'Cancelled by ' . $this->session->userdata('manager_name') : $leave['action_note']));
			if ((int)$leave['user_id'] !== $this->cur_uid) $this->ci_admin_model->add_notification((int)$leave['user_id'], 'leave_update', 'Leave cancelled', 'Your ' . $leave['leave_type'] . ' from ' . date('d M Y', strtotime($leave['from_date'])) . ' was cancelled by HR.', base_url('manager/hr/leaves'), 'fa fa-times-circle', '#dc2626');
			$this->ci_admin_model->log_admin_action('hr_leave_cancel', 'Cancelled ' . strtolower($leave['status']) . ' ' . $leave['leave_type'] . ' #' . $id . ' of user ID #' . (int)$leave['user_id'], 'SUCCESS');
			$this->session->set_flashdata('message', 'Holiday request cancelled.');
		} else {
			$this->session->set_flashdata('error_message', 'Only pending requests can be cancelled' . ($this->is_hr ? ', or approved leave that has not ended yet.' : '.'));
		}
		$this->_back('manager/hr/leaves');
	}

	/** Approve or reject (POST): HR, or the employee's own manager. */
	public function leave_action($param1 = '', $param2 = '')
	{
		$this->_leave_tables();
		if (is_numeric($param1)) { $id = (int)$param1; $action = strtolower(trim((string)$param2)); }
		else { $action = strtolower(trim((string)$param1)); $id = (int)$param2; }
		if ($this->input->server('REQUEST_METHOD') !== 'POST') redirect('manager/hr/leaves');
		$cur_uid = $this->cur_uid;
		$leave = $this->db->select('L.*, U.manager, U.name')->from('ci_leaves L')->join('ci_user U', 'U.id = L.user_id', 'left')->where('L.id', $id)->get()->row_array();
		if (empty($leave) || !in_array($action, array('approve', 'approved', 'reject', 'rejected'), true)) redirect('manager/hr/leaves');
		if (!$this->is_hr && (int)$leave['manager'] !== $cur_uid) {
			$this->session->set_flashdata('error_message', 'Only HR or the employee\'s manager can decide this request.');
			redirect('manager/hr/leaves');
		}
		if ($leave['status'] !== 'Pending') {
			$this->session->set_flashdata('error_message', 'This request was already ' . strtolower($leave['status']) . '.');
			redirect('manager/hr/leaves');
		}
		if ((int)$leave['user_id'] === $cur_uid && !$this->is_admin) {
			$this->session->set_flashdata('error_message', 'You cannot approve or reject your own leave request.');
			redirect('manager/hr/leaves');
		}
		$new_status = in_array($action, array('approve', 'approved'), true) ? 'Approved' : 'Rejected';
		$note = mb_substr(trim((string)$this->input->post('note')), 0, 255);
		$this->db->where('id', $id)->where('status', 'Pending')->update('ci_leaves', array('status' => $new_status, 'approved_by' => $cur_uid,
			'actioned_at' => date('Y-m-d H:i:s'), 'action_note' => $note !== '' ? $note : null));
		$this->ci_admin_model->add_notification((int)$leave['user_id'], 'leave_update', "Leave Request {$new_status}",
			'Your ' . $leave['leave_type'] . ' from ' . date('d M Y', strtotime($leave['from_date'])) . ' to ' . date('d M Y', strtotime($leave['to_date'])) . " was {$new_status}" . ($note !== '' ? ': ' . $note : '.'),
			base_url('manager/hr/leaves'), ($new_status === 'Approved') ? 'fa fa-check-circle' : 'fa fa-times-circle', ($new_status === 'Approved') ? '#16a34a' : '#dc2626');
		$this->ci_admin_model->log_admin_action('hr_leave_' . strtolower($new_status), $new_status . ' ' . $leave['leave_type'] . ' #' . $id . ' of ' . $leave['name'], 'SUCCESS');
		$this->session->set_flashdata('message', $leave['name'] . "'s request " . strtolower($new_status) . '.');
		redirect('manager/hr/leaves');
	}

	public function leave_balances()
	{
		$this->_leave_tables();
		$year = (int)$this->input->get('year') ?: (int)date('Y');
		$people = $this->_people();
		$data = array();
		$data['info'] = array('title' => 'Leave Balances - HR', 'page_heading' => 'Leave Balances');
		$data['year'] = $year;
		$data['people'] = $people;
		$data['bal'] = $this->_leave_balances($people, $year);
		$data['types'] = $this->_leave_types();
		$data['can_edit'] = $this->is_admin || crm_can('hr_leaves', 'write');
		$data['adjust'] = $this->db->query('SELECT A.*, U.name, B.name AS by_name FROM ci_hr_leave_adjust A LEFT JOIN ci_user U ON U.id = A.user_id LEFT JOIN ci_user B ON B.id = A.created_by WHERE A.year = ? ORDER BY A.id DESC LIMIT 200', array($year))->result_array();
		$data['depts'] = array_values(array_unique(array_filter(array_map(function ($u) { return trim((string)$u['department']); }, $people))));
		sort($data['depts']);
		$this->load->view('manager/hr/leave_balances', $data);
	}

	/** Add or remove days (opening balance, carried forward, correction). */
	public function leave_adjust()
	{
		$this->_leave_tables();
		if ($this->input->server('REQUEST_METHOD') !== 'POST') redirect('manager/hr/leave_balances');
		$uid = (int)$this->input->post('user_id');
		$year = (int)$this->input->post('year');
		$type = (string)$this->input->post('leave_type');
		$days = round((float)$this->input->post('days') * 2) / 2;
		$reason = mb_substr(trim((string)$this->input->post('reason')), 0, 255);
		$types = $this->_leave_types(true);
		$u = $uid ? $this->db->select('name')->where('id', $uid)->get('ci_user')->row_array() : array();
		if (empty($u) || !isset($types[$type]) || $days == 0 || $year < 2000 || $reason === '') {
			$this->session->set_flashdata('error_message', 'Choose the employee, leave type, a number of days (not 0) and a reason.');
			redirect('manager/hr/leave_balances?year=' . $year);
		}
		$this->db->insert('ci_hr_leave_adjust', array('user_id' => $uid, 'year' => $year, 'leave_type' => $type, 'days' => $days, 'reason' => $reason, 'created_by' => $this->cur_uid, 'created_at' => date('Y-m-d H:i:s')));
		$this->ci_admin_model->log_admin_action('hr_leave_adjust', ($days > 0 ? 'Added ' : 'Removed ') . self::_fmt_days(abs($days)) . ' of ' . $type . ' ' . $year . ' for ' . $u['name'] . ': ' . $reason, 'SUCCESS');
		$this->session->set_flashdata('message', ($days > 0 ? 'Added ' : 'Removed ') . self::_fmt_days(abs($days)) . ' of ' . $type . ' for ' . $u['name'] . '.');
		redirect('manager/hr/leave_balances?year=' . $year);
	}

	public function leave_calendar()
	{
		$this->_leave_tables();
		$month = preg_match('/^\d{4}-\d{2}$/', (string)$this->input->get('month')) ? $this->input->get('month') : date('Y-m');
		$from = $month . '-01';
		$to = date('Y-m-t', strtotime($from));
		$rows = $this->db->select('L.*, U.name, U.department')->from('ci_leaves L')->join('ci_user U', 'U.id = L.user_id', 'left')
			->where_in('L.status', array('Approved', 'Pending'))->where('L.from_date <=', $to)->where('L.to_date >=', $from)
			->order_by('U.name ASC, L.from_date ASC')->get()->result_array();
		$by = array();
		foreach ($rows as $r) {
			$uid = (int)$r['user_id'];
			if (!isset($by[$uid])) $by[$uid] = array('name' => $r['name'], 'department' => $r['department'], 'leaves' => array());
			$by[$uid]['leaves'][] = $r;
		}
		$days = array();
		for ($d = strtotime($from); $d <= strtotime($to); $d += 86400) $days[] = date('Y-m-d', $d);
		$off = array();
		foreach ($rows as $r) foreach ($days as $dd) if ($r['status'] === 'Approved' && $dd >= $r['from_date'] && $dd <= $r['to_date']) $off[$dd] = (isset($off[$dd]) ? $off[$dd] : 0) + 1;
		$data = array();
		$data['info'] = array('title' => 'Leave Calendar - HR', 'page_heading' => 'Leave Calendar');
		$data['month'] = $month;
		$data['days'] = $days;
		$data['people'] = $by;
		$data['off'] = $off;
		$data['holidays'] = $this->_holidays($from, $to);
		$data['weekend'] = $this->_weekend();
		$data['year_holidays'] = $this->_holidays(substr($month, 0, 4) . '-01-01', substr($month, 0, 4) . '-12-31');
		$data['can_events'] = $this->is_admin || crm_can('events_list', 'write');
		$this->load->view('manager/hr/leave_calendar', $data);
	}

	public function leave_types()
	{
		$data = array();
		$data['info'] = array('title' => 'Leave Types - HR', 'page_heading' => 'Leave Types');
		$data['types'] = $this->_leave_types(true);
		$used = array();
		foreach ($this->db->select('leave_type, COUNT(*) AS n', false)->group_by('leave_type')->get('ci_leaves')->result_array() as $r) $used[$r['leave_type']] = (int)$r['n'];
		$data['used'] = $used;
		$data['pay_labels'] = self::leave_pay_labels();
		$data['can_edit'] = $this->is_admin || crm_can('hr_leaves', 'write');
		$data['set'] = $this->_hr_settings();
		$this->load->view('manager/hr/leave_types', $data);
	}

	public function leave_type_save()
	{
		$this->_leave_tables();
		if ($this->input->server('REQUEST_METHOD') !== 'POST') redirect('manager/hr/leave_types');
		$id = (int)$this->input->post('id');
		$old = $id ? $this->db->where('id', $id)->get('ci_hr_leave_types')->row_array() : array();
		$name = mb_substr(trim(preg_replace('/\s+/', ' ', (string)$this->input->post('name'))), 0, 50);
		if ($name === '') { $this->session->set_flashdata('error_message', 'Give the leave type a name.'); redirect('manager/hr/leave_types'); }
		$dup = $this->db->where('name', $name)->where('id <>', $id)->count_all_results('ci_hr_leave_types');
		if ($dup) { $this->session->set_flashdata('error_message', 'There is already a leave type called ' . $name . '.'); redirect('manager/hr/leave_types'); }
		$pay = (string)$this->input->post('pay');
		$row = array(
			'name' => $name,
			'days_per_year' => max(0, min(365, round((float)$this->input->post('days_per_year') * 2) / 2)),
			'pay' => array_key_exists($pay, self::leave_pay_labels()) ? $pay : 'full',
			'count_mode' => $this->input->post('count_mode') === 'calendar' ? 'calendar' : 'working',
			'needs_document' => $this->input->post('needs_document') ? 1 : 0,
			'gender' => in_array($this->input->post('gender'), array('female', 'male'), true) ? $this->input->post('gender') : 'any',
			'min_service_months' => max(0, min(120, (int)$this->input->post('min_service_months'))),
			'note' => mb_substr(trim((string)$this->input->post('note')), 0, 255),
			'active' => $this->input->post('active') ? 1 : 0,
		);
		if ($old) {
			$this->db->where('id', $id)->update('ci_hr_leave_types', $row);
			// Requests keep the type's name: rename them too
			if ($old['name'] !== $name) {
				$this->db->where('leave_type', $old['name'])->update('ci_leaves', array('leave_type' => $name));
				$this->db->where('leave_type', $old['name'])->update('ci_hr_leave_adjust', array('leave_type' => $name));
			}
		} else {
			$row['sort'] = 1 + (int)$this->db->select_max('sort')->get('ci_hr_leave_types')->row()->sort;
			$this->db->insert('ci_hr_leave_types', $row);
		}
		$this->ci_admin_model->log_admin_action('hr_leave_type', ($old ? 'Changed' : 'Added') . ' leave type ' . $name . ($old && $old['name'] !== $name ? ' (was ' . $old['name'] . ')' : ''), 'SUCCESS');
		$this->session->set_flashdata('message', 'Leave type ' . $name . ' saved.');
		redirect('manager/hr/leave_types');
	}

	/** Present or Late for a clock-in, from the person's shift and its grace minutes. */
	private function _clock_status($uid, $date, $time)
	{
		$shift = self::_shift_on($this->_shift_map(), (int)$uid, $date);
		if (!$shift) return 'Present';
		return strtotime($date . ' ' . $time) > strtotime($date . ' ' . $shift['start_time']) + 60 * (int)$shift['grace_period_mins'] ? 'Late' : 'Present';
	}

	public function payroll()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Payroll & Compensation - HR',
			'page_heading' => 'Payroll Processing'
		);
		$data['active_tab'] = 'payroll';

		$filter_month = trim((string)$this->input->get('month')) ?: date('Y-m');

		$this->db->select('P.*, U.name as employee_name, U.email as employee_email')
			->from('ci_hr_payroll P')
			->join('ci_user U', 'U.id = P.user_id', 'left')
			->where('P.month_year', $filter_month);

		$raw_pay = $this->db->order_by('P.id', 'DESC')->get()->result_array();
		foreach ($raw_pay as &$p) {
			$p['period_month'] = $p['month_year'];
			$p['allowances'] = floatval($p['housing_allowance']) + floatval($p['transport_allowance']) + floatval($p['other_allowance']);
			$p['deductions'] = floatval($p['tax_deduction']) + floatval($p['other_deductions']);
		}
		unset($p);

		$data['payroll_records'] = $raw_pay;
		$data['filter_month'] = $filter_month;
		$data['selected_month'] = $filter_month;
		$data['employees'] = $this->db->select('id, name, email, c_salary')->where($this->_active_sql(), NULL, FALSE)->order_by('name', 'ASC')->get('ci_user')->result_array();

		$this->load->view('manager/hr/index', $data);
	}

	public function generate_payroll()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') {
			redirect('manager/hr/payroll');
			return;
		}

		$month = trim((string)$this->input->post('month_year'));
		if (empty($month)) {
			$month = trim((string)$this->input->post('period_month')) ?: date('Y-m');
		}

		$cur_uid = (int)$this->session->userdata('manager_id');
		$employees = $this->db->select('id, name, c_salary')->where($this->_active_sql(), NULL, FALSE)->get('ci_user')->result_array();

		$gen_count = 0;
		foreach ($employees as $emp) {
			$uid = (int)$emp['id'];
			$exist = $this->db->where('user_id', $uid)->where('month_year', $month)->count_all_results('ci_hr_payroll');
			if ($exist == 0) {
				$emp_salary = floatval($emp['c_salary']);
				$basic = ($emp_salary > 0) ? round($emp_salary * 0.60, 2) : 6000.00;
				$housing = ($emp_salary > 0) ? round($emp_salary * 0.25, 2) : 2500.00;
				$trans = ($emp_salary > 0) ? round($emp_salary * 0.15, 2) : 1500.00;
				$gross = $basic + $housing + $trans;
				$tax = 0.00;
				$deduct = 0.00;
				$net = $gross - $tax - $deduct;

				$this->db->insert('ci_hr_payroll', array(
					'user_id' => $uid,
					'month_year' => $month,
					'basic_salary' => $basic,
					'housing_allowance' => $housing,
					'transport_allowance' => $trans,
					'other_allowance' => 0.00,
					'gross_salary' => $gross,
					'tax_deduction' => $tax,
					'other_deductions' => $deduct,
					'net_salary' => $net,
					'status' => 'Pending',
					'created_by' => $cur_uid,
					'created_at' => date('Y-m-d H:i:s')
				));
				$gen_count++;
			}
		}

		$this->session->set_flashdata('message', "Generated payroll slips for {$gen_count} employees for {$month}.");
		redirect('manager/hr/payroll?month=' . urlencode($month));
	}

	public function payroll_status($id = 0, $status = '')
	{
		$id = (int)$id;
		$allowed = array('Paid', 'Pending', 'Cancelled');
		if ($id > 0 && in_array($status, $allowed)) {
			$up = array('status' => $status);
			if ($status === 'Paid') {
				$up['payment_date'] = date('Y-m-d');
			}
			$this->db->where('id', $id)->update('ci_hr_payroll', $up);
			$this->session->set_flashdata('message', "Payroll record marked {$status}.");
		}
		redirect('manager/hr/payroll');
	}

	public function taxation()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Taxation & Benefits - HR',
			'page_heading' => 'Taxation & Statutory Benefits'
		);
		$data['active_tab'] = 'taxation';

		$stat_row = $this->db->select('COUNT(*) as total_records, SUM(gross_salary) as total_gross, SUM(tax_deduction + other_deductions) as total_deductions, SUM(net_salary) as total_net')->get('ci_hr_payroll')->row_array();

		$data['payroll_stats'] = array(
			'total_records' => (int)($stat_row['total_records'] ?? 0),
			'total_gross' => floatval($stat_row['total_gross'] ?? 0),
			'total_deductions' => floatval($stat_row['total_deductions'] ?? 0),
			'total_net' => floatval($stat_row['total_net'] ?? 0)
		);

		$data['employees'] = $this->db->select('id, name, email, c_salary')->where($this->_active_sql(), NULL, FALSE)->order_by('name', 'ASC')->get('ci_user')->result_array();

		$this->load->view('manager/hr/index', $data);
	}

	public function appraisals()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Performance & Appraisals - HR',
			'page_heading' => 'Performance Appraisals'
		);
		$data['active_tab'] = 'appraisals';

		$raw_app = $this->db->select('A.*, U.name as employee_name, Reviewer.name as reviewer_name')
			->from('ci_hr_appraisals A')
			->join('ci_user U', 'U.id = A.user_id', 'left')
			->join('ci_user Reviewer', 'Reviewer.id = A.reviewer_id', 'left')
			->order_by('A.id', 'DESC')
			->get()
			->result_array();

		foreach ($raw_app as &$ap) {
			$ap['score'] = floatval($ap['kpi_score']);
			$ap['appraisal_cycle'] = $ap['cycle_name'];
			$ap['review_date'] = substr($ap['created_at'], 0, 10);
		}
		unset($ap);

		$data['appraisals'] = $raw_app;
		$data['employees'] = $this->db->select('id, name, email')->where($this->_active_sql(), NULL, FALSE)->order_by('name', 'ASC')->get('ci_user')->result_array();

		$this->load->view('manager/hr/index', $data);
	}

	public function add_appraisal()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') {
			redirect('manager/hr/appraisals');
			return;
		}

		$user_id = (int)$this->input->post('user_id');
		$reviewer_id = (int)$this->session->userdata('manager_id');
		$cycle = trim((string)$this->input->post('cycle_name'));
		if (empty($cycle)) {
			$cycle = trim((string)$this->input->post('appraisal_cycle')) ?: 'Q4 Review';
		}

		$rating = floatval($this->input->post('rating'));
		$kpi = floatval($this->input->post('kpi_score'));
		if ($kpi <= 0) {
			$kpi = floatval($this->input->post('score'));
		}
		$feedback = trim((string)$this->input->post('feedback'));

		if ($user_id > 0) {
			$this->db->insert('ci_hr_appraisals', array(
				'user_id' => $user_id,
				'reviewer_id' => $reviewer_id,
				'cycle_name' => $cycle,
				'rating' => $rating,
				'kpi_score' => $kpi,
				'feedback' => $feedback,
				'status' => 'Completed',
				'created_at' => date('Y-m-d H:i:s')
			));

			$this->ci_admin_model->add_notification(
				$user_id,
				'appraisal_submitted',
				'Performance Appraisal Completed',
				"Your appraisal for {$cycle} has been submitted with a rating of {$rating}/5.0.",
				base_url('manager/hr/appraisals'),
				'fa fa-star',
				'#16a34a'
			);

			$this->session->set_flashdata('message', 'Performance appraisal submitted.');
		}
		redirect('manager/hr/appraisals');
	}

	/* ------------------------------------------------------------------
	 * People: company HR settings, employee list, each employee's HR file
	 * and the documents & expiry list.
	 * Personal details stay in the staff profile (ci_user); the HR file adds
	 * job & contract (shared with Employee Lifecycle), identity documents,
	 * salary structure history and bank / salary transfer (WPS) details.
	 * ------------------------------------------------------------------ */

	/** Company HR settings with their defaults (UAE). */
	public static function hr_setting_defaults()
	{
		return array(
			'company_name' => '', 'trade_licence_no' => '', 'mol_establishment_id' => '',
			'employer_bank_name' => '', 'employer_routing_code' => '', 'currency' => 'AED', 'salary_day' => '28',
			'weekend' => 'Sat,Sun', 'work_hours' => '8',
			'probation_months' => '6', 'notice_days' => '30', 'annual_leave_days' => '30', 'doc_alert_days' => '60',
		);
	}

	public static function contract_types()
	{
		return array('Permanent', 'Fixed term', 'Part time', 'Intern', 'Consultant');
	}

	public static function payment_methods()
	{
		return array('Bank transfer (WPS)', 'Exchange house (WPS)', 'Cash', 'Cheque');
	}

	private function _hr_tables()
	{
		static $done = false;
		if ($done) return;
		$done = true;
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_hr_settings (k VARCHAR(60) NOT NULL PRIMARY KEY, v VARCHAR(500) NOT NULL DEFAULT '', updated_by INT NULL, updated_at DATETIME NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_hr_employee (
			user_id INT NOT NULL PRIMARY KEY,
			grade VARCHAR(60) NOT NULL DEFAULT '',
			contract_start DATE NULL, contract_end DATE NULL,
			notice_days INT NULL, annual_leave_days INT NULL,
			labour_card_no VARCHAR(60) NOT NULL DEFAULT '', labour_card_expiry DATE NULL,
			mol_person_id VARCHAR(30) NOT NULL DEFAULT '',
			insurance_provider VARCHAR(120) NOT NULL DEFAULT '', insurance_no VARCHAR(60) NOT NULL DEFAULT '', insurance_expiry DATE NULL,
			payment_method VARCHAR(40) NOT NULL DEFAULT '', wps_routing_code VARCHAR(20) NOT NULL DEFAULT '',
			updated_by INT NULL, updated_at DATETIME NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_hr_salary (
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			user_id INT NOT NULL,
			effective_from DATE NOT NULL,
			basic DECIMAL(12,2) NOT NULL DEFAULT 0, housing DECIMAL(12,2) NOT NULL DEFAULT 0,
			transport DECIMAL(12,2) NOT NULL DEFAULT 0, other DECIMAL(12,2) NOT NULL DEFAULT 0,
			extras TEXT NULL, total DECIMAL(12,2) NOT NULL DEFAULT 0,
			reason VARCHAR(200) NOT NULL DEFAULT '',
			created_by INT NULL, created_at DATETIME NULL,
			KEY user_from (user_id, effective_from)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_user_history (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, field VARCHAR(60) NOT NULL, old_value VARCHAR(500) NOT NULL DEFAULT '', new_value VARCHAR(500) NOT NULL DEFAULT '', changed_by INT NULL, changed_at DATETIME NOT NULL, KEY user_when (user_id, changed_at)) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_user_designation (user_id INT NOT NULL PRIMARY KEY, designation VARCHAR(150) NOT NULL DEFAULT '') ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->data_cache = array();
	}

	/** All company HR settings (saved values over the defaults). */
	private function _hr_settings()
	{
		$this->_hr_tables();
		$out = self::hr_setting_defaults();
		foreach ($this->db->get('ci_hr_settings')->result_array() as $r) if (array_key_exists($r['k'], $out)) $out[$r['k']] = $r['v'];
		return $out;
	}

	/** One line in the person's Change History (same list as the staff profile). */
	private function _hist($uid, $field, $old, $new)
	{
		if ((string)$old === (string)$new) return;
		$this->db->insert('ci_user_history', array('user_id' => (int)$uid, 'field' => mb_substr($field, 0, 60), 'old_value' => mb_substr((string)$old, 0, 500),
			'new_value' => mb_substr((string)$new, 0, 500), 'changed_by' => $this->cur_uid, 'changed_at' => date('Y-m-d H:i:s')));
	}

	private function _days_left($date)
	{
		$date = trim((string)$date);
		if ($date === '' || $date === '0000-00-00' || !($t = strtotime($date))) return null;
		return (int)floor(($t - strtotime(date('Y-m-d'))) / 86400);
	}

	/** expired / soon (within the alert days) / valid / none */
	private function _doc_state($days, $alert)
	{
		if ($days === null) return 'none';
		if ($days < 0) return 'expired';
		return $days <= $alert ? 'soon' : 'valid';
	}

	/** Current salary structure per person: the latest one that has started (or the first one, if all start later). */
	private function _current_salaries($ids = null)
	{
		$this->_hr_tables();
		$this->db->order_by('user_id ASC, effective_from ASC, id ASC');
		if ($ids !== null) { if (!$ids) return array(); $this->db->where_in('user_id', $ids); }
		$out = array();
		$today = date('Y-m-d');
		foreach ($this->db->get('ci_hr_salary')->result_array() as $s) {
			$u = (int)$s['user_id'];
			if (!isset($out[$u]) || $s['effective_from'] <= $today) $out[$u] = $s;
		}
		return $out;
	}

	/** HR settings page (Full on Company HR Settings to save). */
	public function settings()
	{
		$data = array();
		$data['info'] = array('title' => 'HR Settings - HR', 'page_heading' => 'HR Settings');
		$data['set'] = $this->_hr_settings();
		$data['can_edit'] = $this->is_admin || crm_can('hr_settings', 'write');
		$last = $this->db->select('S.updated_at, U.name')->from('ci_hr_settings S')->join('ci_user U', 'U.id = S.updated_by', 'left')->order_by('S.updated_at', 'DESC')->limit(1)->get()->row_array();
		$data['last'] = $last;
		$this->load->view('manager/hr/settings', $data);
	}

	public function settings_save()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') redirect('manager/hr/settings');
		$def = self::hr_setting_defaults();
		$old = $this->_hr_settings();
		$p = function ($k) { return trim((string)$this->input->post($k)); };
		$num = function ($v, $min, $max, $d) { return (is_numeric($v) && $v >= $min && $v <= $max) ? (string)(0 + $v) : $d; };
		$days = array_values(array_intersect(array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'), (array)$this->input->post('weekend')));
		$new = array(
			'company_name' => mb_substr($p('company_name'), 0, 150),
			'trade_licence_no' => mb_substr($p('trade_licence_no'), 0, 60),
			'mol_establishment_id' => preg_replace('/\D/', '', $p('mol_establishment_id')),
			'employer_bank_name' => mb_substr($p('employer_bank_name'), 0, 120),
			'employer_routing_code' => preg_replace('/\D/', '', $p('employer_routing_code')),
			'currency' => preg_match('/^[A-Z]{3}$/', strtoupper($p('currency'))) ? strtoupper($p('currency')) : $def['currency'],
			'salary_day' => $num($p('salary_day'), 1, 31, $def['salary_day']),
			'weekend' => implode(',', $days),
			'work_hours' => $num($p('work_hours'), 1, 24, $def['work_hours']),
			'probation_months' => $num($p('probation_months'), 0, 12, $def['probation_months']),
			'notice_days' => $num($p('notice_days'), 0, 180, $def['notice_days']),
			'annual_leave_days' => $num($p('annual_leave_days'), 0, 60, $def['annual_leave_days']),
			'doc_alert_days' => $num($p('doc_alert_days'), 1, 365, $def['doc_alert_days']),
		);
		$changed = array();
		foreach ($new as $k => $v) {
			if ((string)$old[$k] === (string)$v) continue;
			$changed[] = $k;
			$this->db->query('REPLACE INTO ci_hr_settings (k, v, updated_by, updated_at) VALUES (?, ?, ?, ?)', array($k, $v, $this->cur_uid, date('Y-m-d H:i:s')));
		}
		if ($changed) $this->ci_admin_model->log_admin_action('hr_settings', 'Changed HR settings: ' . implode(', ', $changed), 'SUCCESS');
		$this->session->set_flashdata('message', $changed ? 'HR settings saved.' : 'Nothing was changed.');
		redirect('manager/hr/settings');
	}

	/** Employee list, or one employee's HR file (manager/hr/employees/<id>). */
	public function employees($id = 0)
	{
		$this->_hr_tables();
		if ((int)$id > 0) return $this->_employee_file((int)$id);

		$set = $this->_hr_settings();
		$alert = (int)$set['doc_alert_days'];
		$see_pay = $this->is_admin || crm_can('hr_salary', 'read');
		$has_exit = $this->db->table_exists('ci_user_exit');

		$this->db->select('U.id, U.name, U.emp_id, U.department, U.office, U.c_doj, U.admin_status, U.user_type, U.c_salary, U.p_exdate, U.visa_expiry, U.eid_expiry, R.title AS role_title, G.designation, L.stage, L.probation_end_date, L.contract_type, E.contract_end, E.labour_card_expiry, E.insurance_expiry, E.grade')
			->from('ci_user U')
			->join('ci_roles R', 'R.id = U.user_type', 'left')
			->join('ci_user_designation G', 'G.user_id = U.id', 'left')
			->join('ci_hr_lifecycle L', 'L.user_id = U.id', 'left')
			->join('ci_hr_employee E', 'E.user_id = U.id', 'left')
			->where("(R.role_type IS NULL OR R.role_type <> 'system')", null, false);
		if ($has_exit) { $this->db->select('X.left_on'); $this->db->join('ci_user_exit X', 'X.user_id = U.id', 'left'); }
		if (!$this->is_admin) $this->db->where('U.user_type <>', 1);
		$rows = $this->db->order_by('U.name', 'ASC')->get()->result_array();

		$sal = $see_pay ? $this->_current_salaries() : array();
		$today = date('Y-m-d');
		$month = date('Y-m');
		$k = array('working' => 0, 'probation' => 0, 'joined' => 0, 'docs' => 0, 'nosalary' => 0);
		$depts = array();
		foreach ($rows as &$r) {
			$r['left'] = !empty($r['left_on']);
			$r['status'] = $r['left'] ? 'left' : ((int)$r['admin_status'] === 1 ? 'working' : 'disabled');
			$r['probation'] = !$r['left'] && !empty($r['probation_end_date']) && $r['probation_end_date'] >= $today;
			$worst = null; $worst_label = '';
			foreach (array('Passport' => 'p_exdate', 'Visa' => 'visa_expiry', 'Emirates ID' => 'eid_expiry', 'Labour card' => 'labour_card_expiry', 'Insurance' => 'insurance_expiry') as $lbl => $f) {
				$d = $this->_days_left($r[$f]);
				if ($d !== null && ($worst === null || $d < $worst)) { $worst = $d; $worst_label = $lbl; }
			}
			$r['doc_days'] = $worst;
			$r['doc_label'] = $worst_label;
			$r['doc_state'] = $this->_doc_state($worst, $alert);
			$s = isset($sal[(int)$r['id']]) ? $sal[(int)$r['id']] : null;
			$r['salary'] = $s ? (float)$s['total'] : null;
			$r['salary_old'] = (!$s && is_numeric($r['c_salary']) && (float)$r['c_salary'] > 1) ? (float)$r['c_salary'] : null;
			if ($r['status'] === 'working') {
				$k['working']++;
				if ($r['probation']) $k['probation']++;
				if (substr((string)$r['c_doj'], 0, 7) === $month) $k['joined']++;
				if (in_array($r['doc_state'], array('expired', 'soon'), true)) $k['docs']++;
				if ($see_pay && !$s) $k['nosalary']++;
			}
			$dep = trim((string)$r['department']);
			if ($dep !== '') $depts[$dep] = true;
		}
		unset($r);
		ksort($depts);

		$data = array();
		$data['info'] = array('title' => 'Employees - HR', 'page_heading' => 'Employees');
		$data['rows'] = $rows;
		$data['kpi'] = $k;
		$data['depts'] = array_keys($depts);
		$data['see_pay'] = $see_pay;
		$data['currency'] = $set['currency'];
		$data['alert_days'] = $alert;
		$data['can_add'] = $this->is_admin || crm_can('hr_staff', 'write');
		$this->load->view('manager/hr/employees', $data);
	}

	private function _employee_file($id)
	{
		$u = $this->ci_admin_model->getSingleUser($id);
		if (empty($u) || (!$this->is_admin && (int)$u['user_type'] === 1)) {
			$this->session->set_flashdata('error_message', empty($u) ? 'That employee was not found.' : 'Only an admin can open an Admin account.');
			redirect('manager/hr/employees');
			return;
		}
		$set = $this->_hr_settings();
		$data = array();
		$data['info'] = array('title' => $u['name'] . ' - HR file', 'page_heading' => 'HR file');
		$data['u'] = $u;
		$data['set'] = $set;
		$data['emp'] = $this->db->where('user_id', $id)->get('ci_hr_employee')->row_array();
		$data['life'] = $this->db->where('user_id', $id)->get('ci_hr_lifecycle')->row_array();
		$data['exit'] = $this->db->table_exists('ci_user_exit') ? $this->db->where('user_id', $id)->get('ci_user_exit')->row_array() : array();
		$mgr = (int)$u['manager'] ? $this->db->select('id, name')->where('id', (int)$u['manager'])->get('ci_user')->row_array() : array();
		$data['manager'] = $mgr;
		$data['can_edit'] = $this->is_admin || crm_can('hr_employees', 'write');
		$data['see_pay'] = $this->is_admin || crm_can('hr_salary', 'read');
		$data['edit_pay'] = $this->is_admin || crm_can('hr_salary', 'write');
		$data['salaries'] = $data['see_pay'] ? $this->db->query('SELECT S.*, U.name AS by_name FROM ci_hr_salary S LEFT JOIN ci_user U ON U.id = S.created_by WHERE S.user_id = ? ORDER BY S.effective_from DESC, S.id DESC', array($id))->result_array() : array();
		$cur = $data['see_pay'] ? $this->_current_salaries(array($id)) : array();
		$data['salary_now'] = isset($cur[$id]) ? $cur[$id] : null;
		$data['staff_full'] = $this->is_admin || crm_can('hr_staff', 'write') || crm_can('users', 'write');
		$data['staff_view'] = $data['staff_full'] || crm_can('hr_staff', 'read');
		$data['files'] = ($data['staff_full'] && $this->db->table_exists('ci_user_docs')) ? $this->db->query('SELECT D.*, U.name AS uploader FROM ci_user_docs D LEFT JOIN ci_user U ON U.id = D.uploaded_by WHERE D.user_id = ? ORDER BY D.doc_type, D.uploaded_at DESC', array($id))->result_array() : array();
		$data['history'] = $this->db->query('SELECT H.*, U.name AS by_name FROM ci_user_history H LEFT JOIN ci_user U ON U.id = H.changed_by WHERE H.user_id = ? ORDER BY H.changed_at DESC, H.id DESC LIMIT 200', array($id))->result_array();
		if (!$data['see_pay']) {
			$hidden = array('Salary', 'Salary structure', 'Bank', 'IBAN', 'Bank account no', 'SWIFT', 'Bank routing code (WPS)', 'Salary payment method', 'MOL person ID');
			$data['history'] = array_values(array_filter($data['history'], function ($h) use ($hidden) { return !in_array($h['field'], $hidden, true); }));
		}
		$data['designations'] = array_map(function ($r) { return $r['designation']; }, $this->db->query("SELECT DISTINCT designation FROM ci_user_designation WHERE designation <> '' ORDER BY designation")->result_array());
		$data['departments'] = array_map(function ($r) { return $r['department']; }, $this->db->query("SELECT DISTINCT department FROM ci_user WHERE department <> '' ORDER BY department")->result_array());
		$data['grades'] = array_map(function ($r) { return $r['grade']; }, $this->db->query("SELECT DISTINCT grade FROM ci_hr_employee WHERE grade <> '' ORDER BY grade")->result_array());
		$data['stages'] = self::lifecycle_stages();
		$data['contract_types'] = self::contract_types();
		$data['pay_methods'] = self::payment_methods();
		$data['alert_days'] = (int)$set['doc_alert_days'];
		$data['countries'] = $this->db->table_exists('countries') ? array_map(function ($r) { return $r['name']; }, $this->db->select('name')->order_by('name', 'ASC')->get('countries')->result_array()) : array();
		$this->load->view('manager/hr/employee', $data);
	}

	/** Saves one section of the HR file: job, identity, bank (POST hr/employee_save/<id>/<section>). */
	public function employee_save($id = 0, $section = '')
	{
		$this->_hr_tables();
		$id = (int)$id;
		$back = 'manager/hr/employees/' . $id;
		$u = $id ? $this->db->where('id', $id)->get('ci_user')->row_array() : array();
		if (empty($u) || $this->input->server('REQUEST_METHOD') !== 'POST' || (!$this->is_admin && (int)$u['user_type'] === 1)) redirect('manager/hr/employees');
		$need = ($section === 'bank') ? 'hr_salary' : 'hr_employees';
		if (!$this->is_admin && !crm_can($need, 'write')) {
			$this->session->set_flashdata('error_message', 'You do not have Full access to change this part of the HR file.');
			redirect($back);
		}
		$p = function ($k, $max = 150) { return mb_substr(trim(preg_replace('/\s+/', ' ', (string)$this->input->post($k))), 0, $max); };
		$ymd = function ($k) { $v = trim((string)$this->input->post($k)); return ($v !== '' && ($t = strtotime($v))) ? date('Y-m-d', $t) : ''; };
		$emp = $this->db->where('user_id', $id)->get('ci_hr_employee')->row_array();
		$emp_new = array();
		$user_new = array();
		$labels = array();

		if ($section === 'job') {
			$des_old = $this->db->where('user_id', $id)->get('ci_user_designation')->row_array();
			$des = $p('designation');
			$this->_hist($id, 'Designation', !empty($des_old['designation']) ? $des_old['designation'] : '', $des);
			$this->db->query('REPLACE INTO ci_user_designation (user_id, designation) VALUES (?, ?)', array($id, $des));
			$user_new = array('department' => $p('department', 100), 'office' => $p('office', 100), 'c_doj' => $ymd('c_doj'));
			$labels = array('department' => 'Department', 'office' => 'Office / Location', 'c_doj' => 'Date of Joining');
			$ct = (string)$this->input->post('contract_type');
			$emp_new = array('grade' => $p('grade', 60), 'contract_start' => $ymd('contract_start') ?: null, 'contract_end' => $ymd('contract_end') ?: null,
				'notice_days' => is_numeric($this->input->post('notice_days')) ? max(0, min(365, (int)$this->input->post('notice_days'))) : null,
				'annual_leave_days' => is_numeric($this->input->post('annual_leave_days')) ? max(0, min(90, (int)$this->input->post('annual_leave_days'))) : null);
			$elabels = array('grade' => 'Grade', 'contract_start' => 'Contract start', 'contract_end' => 'Contract end', 'notice_days' => 'Notice period (days)', 'annual_leave_days' => 'Annual leave (days)');
			// Stage, probation, confirmation and contract type are the Employee Lifecycle record
			$life = $this->db->where('user_id', $id)->get('ci_hr_lifecycle')->row_array();
			$stage = (string)$this->input->post('stage');
			$life_new = array(
				'stage' => in_array($stage, self::lifecycle_stages(), true) ? $stage : (!empty($life['stage']) ? $life['stage'] : 'Confirmed'),
				'probation_end_date' => $ymd('probation_end_date') ?: null,
				'confirmation_date' => $ymd('confirmation_date') ?: null,
				'contract_type' => in_array($ct, self::contract_types(), true) ? $ct : 'Permanent',
				'updated_at' => date('Y-m-d H:i:s'),
			);
			foreach (array('stage' => 'Stage', 'probation_end_date' => 'Probation ends', 'confirmation_date' => 'Confirmed on', 'contract_type' => 'Contract type') as $f => $l)
				$this->_hist($id, $l, !empty($life[$f]) ? $life[$f] : '', (string)$life_new[$f]);
			if ($life) $this->db->where('user_id', $id)->update('ci_hr_lifecycle', $life_new);
			else { $life_new['user_id'] = $id; $life_new['created_at'] = date('Y-m-d H:i:s'); $this->db->insert('ci_hr_lifecycle', $life_new); }
		} elseif ($section === 'identity') {
			$user_new = array('national' => $p('national', 100), 'p_no' => $p('p_no', 40), 'p_date' => $ymd('p_date'), 'p_exdate' => $ymd('p_exdate'),
				'visa_no' => $p('visa_no', 40), 'visa_type' => $p('visa_type', 60), 'visa_by' => $p('visa_by', 100), 'visa_expiry' => $ymd('visa_expiry'),
				'eid_no' => $p('eid_no', 40), 'eid_expiry' => $ymd('eid_expiry'));
			$labels = array('national' => 'Nationality', 'p_no' => 'Passport No', 'p_date' => 'Passport Issue Date', 'p_exdate' => 'Passport Expiry',
				'visa_no' => 'Visa No', 'visa_type' => 'Visa Type', 'visa_by' => 'Visa Sponsor', 'visa_expiry' => 'Visa Expiry', 'eid_no' => 'Emirates ID No', 'eid_expiry' => 'Emirates ID Expiry');
			$emp_new = array('labour_card_no' => $p('labour_card_no', 60), 'labour_card_expiry' => $ymd('labour_card_expiry') ?: null,
				'insurance_provider' => $p('insurance_provider', 120), 'insurance_no' => $p('insurance_no', 60), 'insurance_expiry' => $ymd('insurance_expiry') ?: null);
			$elabels = array('labour_card_no' => 'Labour Card No', 'labour_card_expiry' => 'Labour Card Expiry', 'insurance_provider' => 'Insurance provider', 'insurance_no' => 'Insurance No', 'insurance_expiry' => 'Insurance Expiry');
		} elseif ($section === 'bank') {
			$iban = strtoupper(preg_replace('/\s+/', '', (string)$this->input->post('iban')));
			if ($iban !== '' && !preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{10,30}$/', $iban)) {
				$this->session->set_flashdata('error_message', 'The IBAN does not look right (UAE IBANs are AE followed by 21 digits).');
				redirect($back . '#bank');
			}
			$user_new = array('bank' => $p('bank', 100), 'iban' => $iban, 'bank_no' => $p('bank_no', 40), 'swift' => strtoupper($p('swift', 20)));
			$labels = array('bank' => 'Bank', 'iban' => 'IBAN', 'bank_no' => 'Bank account no', 'swift' => 'SWIFT');
			$pm = (string)$this->input->post('payment_method');
			$emp_new = array('payment_method' => in_array($pm, self::payment_methods(), true) ? $pm : '',
				'wps_routing_code' => preg_replace('/\D/', '', (string)$this->input->post('wps_routing_code')),
				'mol_person_id' => preg_replace('/\D/', '', (string)$this->input->post('mol_person_id')));
			$elabels = array('payment_method' => 'Salary payment method', 'wps_routing_code' => 'Bank routing code (WPS)', 'mol_person_id' => 'MOL person ID');
		} else {
			redirect($back);
		}

		foreach ($user_new as $f => $v) $this->_hist($id, $labels[$f], isset($u[$f]) ? $u[$f] : '', $v);
		if ($user_new) { $user_new['modified'] = date('Y-m-d'); $this->db->where('id', $id)->update('ci_user', $user_new); }
		foreach ($emp_new as $f => $v) $this->_hist($id, $elabels[$f], isset($emp[$f]) ? (string)$emp[$f] : '', (string)$v);
		$emp_new['updated_by'] = $this->cur_uid;
		$emp_new['updated_at'] = date('Y-m-d H:i:s');
		if ($emp) $this->db->where('user_id', $id)->update('ci_hr_employee', $emp_new);
		else { $emp_new['user_id'] = $id; $this->db->insert('ci_hr_employee', $emp_new); }

		$names = array('job' => 'Job & contract', 'identity' => 'Identity documents', 'bank' => 'Bank & salary transfer');
		$this->ci_admin_model->log_admin_action('hr_employee_update', 'Updated ' . $names[$section] . ' in the HR file of user ID #' . $id, 'SUCCESS');
		$this->session->set_flashdata('message', $names[$section] . ' saved.');
		redirect($back . '#' . $section);
	}

	/** Adds a salary structure from a date (keeps the earlier ones as history). Needs Full on Salary & Bank Details. */
	public function salary_add($id = 0)
	{
		$this->_hr_tables();
		$id = (int)$id;
		$back = 'manager/hr/employees/' . $id;
		$u = $id ? $this->db->where('id', $id)->get('ci_user')->row_array() : array();
		if (empty($u) || $this->input->server('REQUEST_METHOD') !== 'POST' || (!$this->is_admin && (int)$u['user_type'] === 1)) redirect('manager/hr/employees');
		if (!$this->is_admin && !crm_can('hr_salary', 'write')) {
			$this->session->set_flashdata('error_message', 'You do not have Full access to Salary & Bank Details.');
			redirect($back);
		}
		$money = function ($v) { $v = str_replace(',', '', trim((string)$v)); return (is_numeric($v) && $v >= 0) ? round((float)$v, 2) : 0.0; };
		$from = trim((string)$this->input->post('effective_from'));
		$from = ($from !== '' && ($t = strtotime($from))) ? date('Y-m-d', $t) : '';
		if ($from === '') {
			$this->session->set_flashdata('error_message', 'Choose the date this salary starts from.');
			redirect($back . '#salary');
		}
		$row = array('basic' => $money($this->input->post('basic')), 'housing' => $money($this->input->post('housing')),
			'transport' => $money($this->input->post('transport')), 'other' => $money($this->input->post('other')));
		$extras = array();
		$en = (array)$this->input->post('extra_name');
		$ea = (array)$this->input->post('extra_amount');
		foreach ($en as $i => $n) {
			$n = mb_substr(trim((string)$n), 0, 60);
			$a = isset($ea[$i]) ? $money($ea[$i]) : 0;
			if ($n !== '' && $a > 0) $extras[] = array('name' => $n, 'amount' => $a);
		}
		$total = array_sum($row);
		foreach ($extras as $x) $total += $x['amount'];
		if ($row['basic'] <= 0) {
			$this->session->set_flashdata('error_message', 'Basic salary is required (end-of-service gratuity is worked out from it).');
			redirect($back . '#salary');
		}
		$prev = $this->_current_salaries(array($id));
		$prev = isset($prev[$id]) ? $prev[$id] : null;
		$row += array('user_id' => $id, 'effective_from' => $from, 'extras' => $extras ? json_encode($extras) : null, 'total' => round($total, 2),
			'reason' => mb_substr(trim((string)$this->input->post('reason')), 0, 200), 'created_by' => $this->cur_uid, 'created_at' => date('Y-m-d H:i:s'));
		$this->db->insert('ci_hr_salary', $row);

		$fmt = function ($s) { return 'Basic ' . number_format($s['basic'], 2) . ', housing ' . number_format($s['housing'], 2) . ', transport ' . number_format($s['transport'], 2) . ', other ' . number_format($s['other'], 2) . ' = ' . number_format($s['total'], 2); };
		$this->_hist($id, 'Salary structure', $prev ? $fmt($prev) . ' (from ' . $prev['effective_from'] . ')' : '', $fmt($row) . ' (from ' . $from . ')');
		// The staff profile shows one salary figure: keep it the same as the salary in force today
		$now = $this->_current_salaries(array($id));
		if (isset($now[$id])) {
			$v = (string)(0 + $now[$id]['total']);
			if ((string)$u['c_salary'] !== $v) {
				$this->_hist($id, 'Salary', $u['c_salary'], $v);
				$this->db->where('id', $id)->update('ci_user', array('c_salary' => $v));
			}
		}
		$this->ci_admin_model->log_admin_action('hr_salary_change', 'Salary structure from ' . $from . ' added for user ID #' . $id, 'SUCCESS');
		$this->session->set_flashdata('message', 'Salary structure saved (from ' . date('d-m-Y', strtotime($from)) . ').');
		redirect($back . '#salary');
	}

	/** Removes a salary structure entered by mistake. */
	public function salary_delete($sid = 0)
	{
		$this->_hr_tables();
		$s = (int)$sid ? $this->db->where('id', (int)$sid)->get('ci_hr_salary')->row_array() : array();
		if (empty($s) || $this->input->server('REQUEST_METHOD') !== 'POST') redirect('manager/hr/employees');
		$id = (int)$s['user_id'];
		$u = $this->db->where('id', $id)->get('ci_user')->row_array();
		if ((!$this->is_admin && !crm_can('hr_salary', 'write')) || (!$this->is_admin && !empty($u) && (int)$u['user_type'] === 1)) {
			$this->session->set_flashdata('error_message', 'You do not have Full access to Salary & Bank Details.');
			redirect('manager/hr/employees/' . $id . '#salary');
		}
		$this->db->where('id', (int)$s['id'])->delete('ci_hr_salary');
		$this->_hist($id, 'Salary structure', 'Total ' . number_format($s['total'], 2) . ' from ' . $s['effective_from'], 'Deleted');
		$now = $this->_current_salaries(array($id));
		if (isset($now[$id]) && !empty($u) && (string)$u['c_salary'] !== (string)(0 + $now[$id]['total'])) {
			$this->_hist($id, 'Salary', $u['c_salary'], (string)(0 + $now[$id]['total']));
			$this->db->where('id', $id)->update('ci_user', array('c_salary' => (string)(0 + $now[$id]['total'])));
		}
		$this->ci_admin_model->log_admin_action('hr_salary_change', 'Deleted salary structure from ' . $s['effective_from'] . ' of user ID #' . $id, 'SUCCESS');
		$this->session->set_flashdata('message', 'Salary structure deleted.');
		redirect('manager/hr/employees/' . $id . '#salary');
	}

	public function documents()
	{
		$this->_hr_tables();
		$set = $this->_hr_settings();
		$alert = (int)$set['doc_alert_days'];
		$data = array();
		$data['info'] = array('title' => 'Documents & Expiry - HR', 'page_heading' => 'Documents & Expiry');

		// Every working person's documents: identity details from the HR file / staff profile,
		// the files uploaded on the staff profile, and older number-only registrations.
		$this->db->select('U.id, U.name, U.emp_id, U.department, U.p_no, U.p_exdate, U.visa_no, U.visa_expiry, U.eid_no, U.eid_expiry, E.labour_card_no, E.labour_card_expiry, E.insurance_no, E.insurance_expiry')
			->from('ci_user U')->join('ci_hr_employee E', 'E.user_id = U.id', 'left')->join('ci_roles R', 'R.id = U.user_type', 'left')
			->where($this->_active_sql('U'), NULL, FALSE)->where("(R.role_type IS NULL OR R.role_type <> 'system')", null, false);
		if (!$this->is_admin) $this->db->where('U.user_type <>', 1);
		$staff = $this->db->order_by('U.name', 'ASC')->get()->result_array();
		$files = array();
		if ($this->db->table_exists('ci_user_docs')) {
			foreach ($this->db->order_by('uploaded_at', 'DESC')->get('ci_user_docs')->result_array() as $fd) $files[(int)$fd['user_id']][] = $fd;
		}
		$legacy = array();
		foreach ($this->db->order_by('id', 'DESC')->get('ci_hr_documents')->result_array() as $ld) $legacy[(int)$ld['user_id']][] = $ld;

		$rows = array();
		$add = function ($u, $type, $num, $exp, $file) use (&$rows, $alert) {
			$d = $this->_days_left($exp);
			$rows[] = array('user_id' => (int)$u['id'], 'name' => $u['name'], 'emp_id' => $u['emp_id'], 'department' => $u['department'], 'type' => $type, 'number' => (string)$num,
				'expiry' => ($d === null ? '' : date('Y-m-d', strtotime($exp))), 'days' => $d, 'state' => $this->_doc_state($d, $alert), 'file' => $file);
		};
		$missing = array();
		foreach ($staff as $u) {
			$uid = (int)$u['id']; $mine = isset($files[$uid]) ? $files[$uid] : array(); $used = array();
			$pick = function ($type) use ($mine, &$used) { foreach ($mine as $i => $fd) if ($fd['doc_type'] === $type && !isset($used[$i])) { $used[$i] = 1; return $fd; } return null; };
			foreach (array(array('Passport', $u['p_no'], $u['p_exdate']), array('Visa', $u['visa_no'], $u['visa_expiry']), array('Emirates ID', $u['eid_no'], $u['eid_expiry']),
				array('Labour Card', $u['labour_card_no'], $u['labour_card_expiry']), array('Insurance', $u['insurance_no'], $u['insurance_expiry'])) as $x) {
				$fd = $pick($x[0]);
				if (trim((string)$x[1]) !== '' || trim((string)$x[2]) !== '' || $fd) $add($u, $x[0], $x[1], $x[2], $fd);
				elseif (in_array($x[0], array('Passport', 'Visa', 'Emirates ID'), true)) $missing[] = array('user_id' => $uid, 'name' => $u['name'], 'emp_id' => $u['emp_id'], 'department' => $u['department'], 'type' => $x[0]);
			}
			foreach ($mine as $i => $fd) if (!isset($used[$i])) $add($u, $fd['doc_type'], '', '', $fd);
			if (isset($legacy[$uid])) foreach ($legacy[$uid] as $ld) $add($u, $ld['doc_type'] !== '' ? $ld['doc_type'] : 'Document', $ld['doc_number'], $ld['expiry_date'], null);
		}
		usort($rows, function ($a, $b) {
			$x = $a['days'] === null ? PHP_INT_MAX : $a['days']; $y = $b['days'] === null ? PHP_INT_MAX : $b['days'];
			return $x === $y ? strcasecmp($a['name'], $b['name']) : ($x < $y ? -1 : 1);
		});
		$counts = array('all' => count($rows), 'expired' => 0, 'soon' => 0, 'valid' => 0, 'none' => 0, 'missing' => count($missing));
		foreach ($rows as $r) $counts[$r['state']]++;

		if ($this->input->get('csv')) {
			$this->ci_admin_model->log_admin_action('hr_documents_export', 'Downloaded the documents & expiry list (' . count($rows) . ' documents)', 'SUCCESS');
			while (ob_get_level()) ob_end_clean();
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename="employee_documents_' . date('Y-m-d') . '.csv"');
			$out = fopen('php://output', 'w');
			fwrite($out, "\xEF\xBB\xBF");
			$cell = function ($v) { if (is_int($v)) return $v; $v = (string)$v; return ($v !== '' && strpos('=+-@', $v[0]) !== false) ? "'" . $v : $v; };
			fputcsv($out, array('Employee', 'Employee ID', 'Department', 'Document', 'Number', 'Expiry', 'Days left', 'Status', 'File uploaded'));
			$st = array('expired' => 'Expired', 'soon' => 'Expiring soon', 'valid' => 'Valid', 'none' => 'No expiry date');
			foreach ($rows as $r) fputcsv($out, array_map($cell, array($r['name'], $r['emp_id'], $r['department'], $r['type'], $r['number'], $r['expiry'] ? date('d-m-Y', strtotime($r['expiry'])) : '', $r['days'] === null ? '' : $r['days'], $st[$r['state']], $r['file'] ? 'Yes' : 'No')));
			foreach ($missing as $m) fputcsv($out, array_map($cell, array($m['name'], $m['emp_id'], $m['department'], $m['type'], '', '', '', 'Missing', 'No')));
			fclose($out);
			exit;
		}

		$data['documents'] = $rows;
		$data['missing'] = $missing;
		$data['doc_counts'] = $counts;
		$data['alert_days'] = $alert;
		$data['employees'] = $staff;
		$data['can_add'] = $this->is_admin || crm_can('hr_documents', 'write');
		$data['file_base'] = ($this->is_admin || crm_can('hr_staff', 'write')) ? 'manager/hr/staff/' : '';
		$data['hr_file'] = $this->is_admin || crm_can('hr_employees', 'read');
		$this->load->view('manager/hr/documents', $data);
	}

	public function add_document()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') {
			redirect('manager/hr/documents');
			return;
		}

		$user_id = (int)$this->input->post('user_id');
		$doc_type = trim((string)$this->input->post('doc_type'));
		if (empty($doc_type)) {
			$doc_type = trim((string)$this->input->post('document_type'));
		}
		$doc_number = trim((string)$this->input->post('doc_number'));
		if (empty($doc_number)) {
			$doc_number = trim((string)$this->input->post('document_number'));
		}
		$issue_date = trim((string)$this->input->post('issue_date')) ?: NULL;
		$expiry_date = trim((string)$this->input->post('expiry_date')) ?: NULL;

		if ($user_id > 0 && !empty($doc_type)) {
			$this->db->insert('ci_hr_documents', array(
				'user_id' => $user_id,
				'doc_type' => $doc_type,
				'doc_number' => $doc_number,
				'issue_date' => $issue_date,
				'expiry_date' => $expiry_date,
				'status' => 'Valid',
				'created_at' => date('Y-m-d H:i:s')
			));
			$this->session->set_flashdata('message', 'Employee document registered.');
		}
		redirect('manager/hr/documents');
	}

	/* ------------------------------------------------------------------
	 * Events & Planning: company events (public) plus each employee's own
	 * plans (private). Any employee can add a personal plan; HR/Admin can
	 * also publish company-wide events.
	 * ------------------------------------------------------------------ */
	// Events & Planning was merged into Events & Festivals (profile menu).
	public function events_plans()
	{
		redirect('manager/events');
	}

	// Training moved to Self Training (profile menu).
	public function training()
	{
		redirect('manager/self-training');
	}

	/* ------------------------------------------------------------------
	 * Search Topics: HR knowledge base stored in ci_hr_topics.
	 * ------------------------------------------------------------------ */
	public function search_topics()
	{
		$this->load->model('hr_selfservice_model');
		$data = array();
		$data['info'] = array(
			'title' => 'Search Topics - HR & Training',
			'page_heading' => 'Search Topics'
		);
		$query = trim((string)$this->input->get('q'));
		$category = trim((string)$this->input->get('category'));
		$data['query'] = $query;
		$data['category'] = $category;
		$data['is_hr'] = $this->is_hr;
		$manage = ($this->is_hr && $this->input->get('manage') === '1');
		$data['manage'] = $manage;
		$data['topics'] = $this->hr_selfservice_model->topics($query, $category, $manage);
		$data['categories'] = $this->hr_selfservice_model->topic_categories();
		$data['edit_topic'] = array();
		if ($this->is_hr) {
			$edit_id = (int)$this->input->get('edit');
			if ($edit_id > 0) {
				$data['edit_topic'] = $this->db->where('id', $edit_id)->get('ci_hr_topics')->row_array();
			}
		}
		$this->load->view('manager/hr/search_topics', $data);
	}

	public function topic_save()
	{
		$this->load->model('hr_selfservice_model');
		if ($this->input->server('REQUEST_METHOD') !== 'POST') {
			redirect('manager/hr/search_topics');
			return;
		}
		$id = (int)$this->input->post('id');
		$title = trim((string)$this->input->post('title'));
		if ($title === '') {
			$this->session->set_flashdata('error_message', 'Please enter a topic title.');
			redirect('manager/hr/search_topics?manage=1');
			return;
		}
		$row = array(
			'title' => $title,
			'category' => trim((string)$this->input->post('category')) ?: 'General',
			'summary' => trim((string)$this->input->post('summary')),
			'body' => trim((string)$this->input->post('body')),
			'updated_at' => date('Y-m-d H:i:s')
		);
		if ($id > 0) {
			$this->db->where('id', $id)->update('ci_hr_topics', $row);
			$this->session->set_flashdata('message', 'Topic updated.');
		} else {
			$row['status'] = 'active';
			$row['created_by'] = $this->cur_uid;
			$row['created_at'] = date('Y-m-d H:i:s');
			$this->db->insert('ci_hr_topics', $row);
			$this->session->set_flashdata('message', 'Topic published.');
		}
		redirect('manager/hr/search_topics?manage=1');
	}

	public function topic_status($id = 0, $status = '')
	{
		$this->load->model('hr_selfservice_model');
		$id = (int)$id;
		if ($this->input->server('REQUEST_METHOD') === 'POST' && $id > 0 && in_array($status, array('active', 'archived'), true)) {
			$this->db->where('id', $id)->update('ci_hr_topics', array('status' => $status, 'updated_at' => date('Y-m-d H:i:s')));
			$this->session->set_flashdata('message', $status === 'archived' ? 'Topic hidden from employees.' : 'Topic restored.');
		}
		redirect('manager/hr/search_topics?manage=1');
	}

}
