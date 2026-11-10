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
			'search_topics' => 'hr_topics',
			'payslip' => 'hr_payroll',
			'me' => 'hr', 'my_pay' => 'hr', 'my_doc' => 'hr',
			'letter_request' => 'hr_letters', 'letter' => 'hr_letters',
			'review' => 'hr_appraisals', 'review_save' => 'hr_appraisals',
			'checklist_tick' => 'hr_lifecycle'
		);
		if (isset($self_service[$method])) {
			$this->is_hr = ($this->is_admin || crm_can($self_service[$method], 'write'));
			return;
		}
		// HR pages: each needs its own permission (View to open, Full to change).
		$page_keys = array(
			'index' => 'hr',
			'targets' => 'hr_targets', 'add_target' => 'hr_targets', 'delete_target' => 'hr_targets', 'target_bulk' => 'hr_targets', 'target_bulk_save' => 'hr_targets',
			'lifecycle' => 'hr_lifecycle', 'update_lifecycle' => 'hr_lifecycle',
			'attendance' => 'hr_attendance', 'ajax_clock' => 'hr_attendance', 'attendance_save' => 'hr_attendance',
			'shifts' => 'hr_shifts', 'add_shift' => 'hr_shifts', 'assign_shift' => 'hr_shifts',
			'leave_balances' => 'hr_leaves', 'leave_adjust' => 'hr_leaves', 'leave_calendar' => 'hr_leaves', 'leave_types' => 'hr_leaves', 'leave_type_save' => 'hr_leaves',
			'payroll' => 'hr_payroll', 'generate_payroll' => 'hr_payroll', 'payroll_status' => 'hr_payroll', 'payroll_old_csv' => 'hr_payroll',
			'payroll_run' => 'hr_payroll', 'payroll_recalc' => 'hr_payroll', 'payroll_item_add' => 'hr_payroll', 'payroll_item_delete' => 'hr_payroll',
			'payroll_step' => 'hr_payroll', 'payroll_csv' => 'hr_payroll', 'payroll_sif' => 'hr_payroll',
			'loans' => 'hr_payroll', 'loan_save' => 'hr_payroll', 'loan_close' => 'hr_payroll',
			'taxation' => 'hr_taxation', 'settlement' => 'hr_taxation', 'settlement_save' => 'hr_taxation', 'settlement_step' => 'hr_taxation',
			'appraisals' => 'hr_appraisals', 'add_appraisal' => 'hr_appraisals', 'review_cycle_close' => 'hr_appraisals',
			'letters' => 'hr_letters', 'letter_new' => 'hr_letters', 'letter_save' => 'hr_letters',
			'checklists' => 'hr_lifecycle', 'checklist' => 'hr_lifecycle', 'checklist_new' => 'hr_lifecycle', 'checklist_item_add' => 'hr_lifecycle', 'checklist_delete' => 'hr_lifecycle', 'checklist_template_save' => 'hr_lifecycle',
			'documents' => 'hr_documents', 'add_document' => 'hr_documents',
			'employees' => 'hr_employees', 'employee_save' => 'hr_employees', 'salary_add' => 'hr_employees', 'salary_delete' => 'hr_employees',
			'settings' => 'hr_settings', 'settings_save' => 'hr_settings',
			'topic_save' => 'hr_topics', 'topic_status' => 'hr_topics',
		);
		$key = isset($page_keys[$method]) ? $page_keys[$method] : 'hr';
		// The HR file's save actions check their own right (Employees or Salary & Bank Details, Full) inside the method
		$own_check = in_array($method, array('employee_save', 'salary_add', 'salary_delete', 'payroll_step', 'settlement_step'), true);
		$changes = !$own_check && (($this->input->server('REQUEST_METHOD') === 'POST') || preg_match('/^(add_|delete_|update_|generate_|assign_|leave_adjust|leave_type_save|attendance_save|payroll_status|payroll_recalc|payroll_item_|loan_save|loan_close|settlement_save|letter_new|letter_save|review_cycle_|checklist_new|checklist_item_|checklist_delete|checklist_template_|topic_)/', $method));
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
			'title' => 'HR Management - Sales CRM',
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

	/** Set targets for many people and many months on one page. */
	public function target_bulk()
	{
		$this->_hr_tables();
		$data = array();
		$data['info'] = array('title' => 'Set Targets - HR', 'page_heading' => 'Set Targets');
		$data['can_edit'] = $this->is_admin || crm_can('hr_targets', 'write');
		$data['see_pay'] = $this->is_admin || crm_can('hr_salary', 'read');

		$this->db->select('U.id, U.name, U.department, U.office, R.title AS role_title')
			->from('ci_user U')
			->join('ci_roles R', 'R.id = U.user_type', 'left')
			->where($this->_active_sql('U'), null, false)
			->where("(R.role_type IS NULL OR R.role_type <> 'system')", null, false);
		$people = $this->db->order_by('U.name', 'ASC')->get()->result_array();

		$ids = array();
		foreach ($people as $p) $ids[] = (int)$p['id'];
		$sal = $data['see_pay'] ? $this->_salaries_on($people, date('Y-m-t')) : array();

		// Each person's latest target (to copy) and the targets already set from this month on
		$last = array(); $set = array();
		if ($ids) {
			$rows = $this->db->select('user_id, period_month, target_amount, target_calls, target_leads, target_meetings')
				->where_in('user_id', $ids)->where('status', 'Active')
				->order_by('period_month ASC, id ASC')->get('ci_hr_targets')->result_array();
			foreach ($rows as $r) {
				$u = (int)$r['user_id'];
				$last[$u] = $r;
				if ($r['period_month'] >= date('Y-m')) $set[$u][] = $r['period_month'];
			}
		}
		foreach ($people as &$p) {
			$u = (int)$p['id'];
			$p['salary'] = isset($sal[$u]) ? (float)$sal[$u]['total'] : null;
			$p['last'] = isset($last[$u]) ? $last[$u] : null;
			$p['set'] = isset($set[$u]) ? array_values(array_unique($set[$u])) : array();
		}
		unset($p);
		$data['people'] = $people;
		$data['set'] = $this->_hr_settings();
		$this->load->view('manager/hr/target_bulk', $data);
	}

	/** Save the targets from the Set Targets page: one row per person and month. */
	public function target_bulk_save()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') { redirect('manager/hr/targets/set'); return; }
		$back = 'manager/hr/targets/set';
		$from = (string)$this->input->post('from_month');
		$to = (string)$this->input->post('to_month');
		if (!preg_match('/^\d{4}-\d{2}$/', $from) || !preg_match('/^\d{4}-\d{2}$/', $to)) {
			$this->session->set_flashdata('error_message', 'Please choose the months.');
			redirect($back); return;
		}
		if ($to < $from) { $x = $from; $from = $to; $to = $x; }
		$months = array();
		for ($m = $from; $m <= $to && count($months) < 24; $m = date('Y-m', strtotime($m . '-01 +1 month'))) $months[] = $m;

		$replace = $this->input->post('existing') !== 'keep';
		$notes = trim((string)$this->input->post('notes'));
		$rows = (array)$this->input->post('t');
		$cur_uid = (int)$this->session->userdata('manager_id');
		$cur_name = (string)$this->session->userdata('manager_name');
		$valid = array();
		foreach ($this->db->select('id')->where($this->_active_sql(), null, false)->get('ci_user')->result_array() as $r) $valid[(int)$r['id']] = true;

		$added = 0; $updated = 0; $kept = 0; $people = 0;
		$num = function ($v) { return max(0, (float)str_replace(',', '', (string)$v)); };
		foreach ($rows as $uid => $r) {
			$uid = (int)$uid;
			if (empty($r['on']) || !isset($valid[$uid])) continue;
			$vals = array(
				'target_amount' => round($num(isset($r['amount']) ? $r['amount'] : 0), 2),
				'target_calls' => (int)$num(isset($r['calls']) ? $r['calls'] : 0),
				'target_leads' => (int)$num(isset($r['leads']) ? $r['leads'] : 0),
				'target_meetings' => (int)$num(isset($r['meetings']) ? $r['meetings'] : 0),
			);
			if (!array_sum($vals)) continue;
			$people++;
			foreach ($months as $m) {
				$old = $this->db->where('user_id', $uid)->where('period_month', $m)->where('status', 'Active')
					->order_by('id', 'ASC')->get('ci_hr_targets')->result_array();
				if ($old) {
					if (!$replace) { $kept++; continue; }
					$this->db->where('id', (int)$old[0]['id'])->update('ci_hr_targets', $vals + array('notes' => $notes, 'assigned_by' => $cur_uid, 'updated_at' => date('Y-m-d H:i:s')));
					// Dashboards add up every active target of a month, so a second one for the same month would count twice
					for ($i = 1; $i < count($old); $i++) $this->db->where('id', (int)$old[$i]['id'])->update('ci_hr_targets', array('status' => 'Replaced'));
					$updated++;
				} else {
					$this->db->insert('ci_hr_targets', $vals + array(
						'user_id' => $uid, 'target_type' => 'monthly', 'period_month' => $m,
						'start_date' => $m . '-01', 'end_date' => date('Y-m-t', strtotime($m . '-01')),
						'notes' => $notes, 'status' => 'Active', 'assigned_by' => $cur_uid, 'created_at' => date('Y-m-d H:i:s'),
					));
					$added++;
				}
			}
			$when = count($months) > 1 ? date('M Y', strtotime($from . '-01')) . ' to ' . date('M Y', strtotime($to . '-01')) : date('F Y', strtotime($from . '-01'));
			$this->ci_admin_model->add_notification($uid, 'target_assigned', 'Target set',
				"{$cur_name} set your target for {$when}: AED " . number_format($vals['target_amount'], 2) . " a month, calls {$vals['target_calls']}, leads {$vals['target_leads']}, meetings {$vals['target_meetings']}.",
				base_url('manager/hr/targets'), 'fa fa-bullseye', '#0072f0');
		}
		if (!$people) {
			$this->session->set_flashdata('error_message', 'Nothing saved: tick at least one person and enter a target.');
			redirect($back); return;
		}
		$msg = "Targets saved for {$people} " . ($people == 1 ? 'person' : 'people') . ' over ' . count($months) . ' ' . (count($months) == 1 ? 'month' : 'months') . ": {$added} new";
		if ($updated) $msg .= ", {$updated} changed";
		if ($kept) $msg .= ", {$kept} left as they were";
		$this->session->set_flashdata('message', $msg . '.');
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

	/* ======================= Payroll (HR part 3) ======================= */

	public static function pay_item_kinds()
	{
		return array('addition' => 'Addition (bonus, commission, allowance)', 'deduction' => 'Deduction (fine, damage, other)', 'ot' => 'Overtime, normal', 'ot_rest' => 'Overtime, night or rest day');
	}

	public static function settlement_reasons()
	{
		return array('Resignation', 'Termination', 'End of contract', 'Retirement', 'Other');
	}

	private function _pay_tables()
	{
		static $done = false;
		if ($done) return;
		$done = true;
		$this->_leave_tables();
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_hr_pay_runs (
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			month CHAR(7) NOT NULL,
			status VARCHAR(12) NOT NULL DEFAULT 'Draft',
			people INT NOT NULL DEFAULT 0,
			gross DECIMAL(14,2) NOT NULL DEFAULT 0, deductions DECIMAL(14,2) NOT NULL DEFAULT 0, net DECIMAL(14,2) NOT NULL DEFAULT 0,
			created_by INT NULL, created_at DATETIME NULL, calculated_at DATETIME NULL,
			approved_by INT NULL, approved_at DATETIME NULL, paid_by INT NULL, paid_at DATETIME NULL,
			UNIQUE KEY month (month)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_hr_payslips (
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			run_id INT NOT NULL, user_id INT NOT NULL,
			info TEXT NULL, earnings TEXT NULL, deductions TEXT NULL,
			days_in_month INT NOT NULL DEFAULT 30, days_paid DECIMAL(5,1) NOT NULL DEFAULT 0, leave_days DECIMAL(5,1) NOT NULL DEFAULT 0,
			fixed DECIMAL(12,2) NOT NULL DEFAULT 0, variable DECIMAL(12,2) NOT NULL DEFAULT 0,
			gross DECIMAL(12,2) NOT NULL DEFAULT 0, total_deductions DECIMAL(12,2) NOT NULL DEFAULT 0, net DECIMAL(12,2) NOT NULL DEFAULT 0,
			UNIQUE KEY run_user (run_id, user_id), KEY user_id (user_id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_hr_pay_items (
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			user_id INT NOT NULL, month CHAR(7) NOT NULL, kind VARCHAR(12) NOT NULL,
			label VARCHAR(100) NOT NULL DEFAULT '', hours DECIMAL(6,2) NOT NULL DEFAULT 0, amount DECIMAL(12,2) NOT NULL DEFAULT 0,
			created_by INT NULL, created_at DATETIME NULL,
			KEY month_user (month, user_id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_hr_loans (
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			user_id INT NOT NULL, kind VARCHAR(20) NOT NULL DEFAULT 'Loan',
			amount DECIMAL(12,2) NOT NULL, monthly DECIMAL(12,2) NOT NULL, start_month CHAR(7) NOT NULL,
			reason VARCHAR(255) NOT NULL DEFAULT '', status VARCHAR(12) NOT NULL DEFAULT 'Active',
			created_by INT NULL, created_at DATETIME NULL, closed_by INT NULL, closed_at DATETIME NULL, close_note VARCHAR(255) NOT NULL DEFAULT '',
			KEY user_id (user_id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_hr_loan_pay (
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			loan_id INT NOT NULL, user_id INT NOT NULL, run_id INT NULL, settlement_id INT NULL, month CHAR(7) NOT NULL, amount DECIMAL(12,2) NOT NULL,
			KEY loan_id (loan_id), KEY run_id (run_id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_hr_settlements (
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			user_id INT NOT NULL, last_day DATE NOT NULL, reason VARCHAR(40) NOT NULL DEFAULT '',
			basic DECIMAL(12,2) NOT NULL DEFAULT 0, gross DECIMAL(12,2) NOT NULL DEFAULT 0,
			service_days INT NOT NULL DEFAULT 0, unpaid_days DECIMAL(6,1) NOT NULL DEFAULT 0,
			gratuity DECIMAL(12,2) NOT NULL DEFAULT 0,
			leave_days DECIMAL(5,1) NOT NULL DEFAULT 0, leave_pay DECIMAL(12,2) NOT NULL DEFAULT 0,
			salary_days DECIMAL(5,1) NOT NULL DEFAULT 0, salary_pay DECIMAL(12,2) NOT NULL DEFAULT 0,
			notice_pay DECIMAL(12,2) NOT NULL DEFAULT 0, loans DECIMAL(12,2) NOT NULL DEFAULT 0,
			extra_lines TEXT NULL, total DECIMAL(12,2) NOT NULL DEFAULT 0,
			note VARCHAR(500) NOT NULL DEFAULT '', status VARCHAR(12) NOT NULL DEFAULT 'Draft',
			created_by INT NULL, created_at DATETIME NULL, approved_by INT NULL, approved_at DATETIME NULL, paid_at DATETIME NULL,
			KEY user_id (user_id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->data_cache = array();
	}

	/** Approving payroll and settlements is its own right (Admin always has it). */
	private function _can_approve_pay()
	{
		return $this->is_admin || crm_can('hr_payroll_approve', 'read');
	}

	private function _can_edit_pay()
	{
		return $this->is_admin || crm_can('hr_payroll', 'write');
	}

	/** Salary structure in force on a date: [user_id] => row. People with no structure fall back to the staff salary as basic. */
	private function _salaries_on($users, $date)
	{
		$ids = array();
		foreach ($users as $u) $ids[] = (int)$u['id'];
		if (!$ids) return array();
		$out = array();
		foreach ($this->db->where_in('user_id', $ids)->where('effective_from <=', $date)->order_by('user_id ASC, effective_from ASC, id ASC')->get('ci_hr_salary')->result_array() as $s) $out[(int)$s['user_id']] = $s;
		$cs = array();
		foreach ($this->db->select('id, c_salary')->where_in('id', $ids)->get('ci_user')->result_array() as $r) $cs[(int)$r['id']] = $r['c_salary'];
		foreach ($ids as $id) {
			if (isset($out[$id])) { $out[$id]['source'] = 'structure'; continue; }
			$v = (float)str_replace(',', '', (string)(isset($cs[$id]) ? $cs[$id] : 0));
			if ($v > 0) $out[$id] = array('basic' => $v, 'housing' => 0, 'transport' => 0, 'other' => 0, 'extras' => null, 'total' => $v, 'source' => 'staff');
		}
		return $out;
	}

	/** Salary lines of a structure: list of [label, amount]. */
	private static function _salary_lines($s)
	{
		$l = array(array('Basic salary', (float)$s['basic']));
		if ((float)$s['housing'] > 0) $l[] = array('Housing allowance', (float)$s['housing']);
		if ((float)$s['transport'] > 0) $l[] = array('Transport allowance', (float)$s['transport']);
		if ((float)$s['other'] > 0) $l[] = array('Other allowance', (float)$s['other']);
		foreach ((array)json_decode((string)$s['extras'], true) as $x) if (!empty($x['name'])) $l[] = array($x['name'], (float)$x['amount']);
		return $l;
	}

	/**
	 * Leave pay cuts of one person between two dates: how many days of pay are lost to unpaid / half-pay leave.
	 * UAE sick leave: first 15 days full pay, next 30 half pay, then unpaid; maternity: 45 full, 15 half, then unpaid (counted over the year).
	 */
	private function _leave_cuts($leaves, $types, $start, $end, $hol, $wk)
	{
		$cum = array();
		$by = array();
		$leave_days = 0.0;
		foreach ($leaves as $l) {
			$name = $l['leave_type'];
			$t = isset($types[$name]) ? $types[$name] : array('pay' => 'full', 'count_mode' => 'working');
			if (!isset($cum[$name])) $cum[$name] = 0.0;
			$units = array();
			if ((float)$l['total_days'] == 0.5 && $l['from_date'] === $l['to_date']) $units[$l['from_date']] = 0.5;
			else for ($d = strtotime($l['from_date']); $d <= strtotime($l['to_date']); $d += 86400) {
				$k = date('Y-m-d', $d);
				if ($t['count_mode'] === 'working' && (in_array(date('D', $d), $wk, true) || isset($hol[$k]))) continue;
				$units[$k] = 1.0;
			}
			foreach ($units as $k => $u) {
				$p = $cum[$name];
				switch ($t['pay']) {
					case 'unpaid': $cut = 1.0; break;
					case 'half': $cut = 0.5; break;
					case 'sick_uae': $cut = $p < 15 ? 0.0 : ($p < 45 ? 0.5 : 1.0); break;
					case 'maternity_uae': $cut = $p < 45 ? 0.0 : ($p < 60 ? 0.5 : 1.0); break;
					default: $cut = 0.0;
				}
				$cum[$name] += $u;
				if ($k < $start || $k > $end) continue;
				$leave_days += $u;
				if (!isset($by[$name])) $by[$name] = array('days' => 0.0, 'cut' => 0.0);
				$by[$name]['days'] += $u;
				$by[$name]['cut'] += $u * $cut;
			}
		}
		return array('by' => $by, 'leave_days' => $leave_days);
	}

	/** Everything a pay run needs for one month, loaded once. */
	private function _pay_context($month, $run_id)
	{
		$from = $month . '-01';
		$to = date('Y-m-t', strtotime($from));
		$year = substr($month, 0, 4);
		$ctx = array('month' => $month, 'from' => $from, 'to' => $to, 'dim' => (int)date('t', strtotime($from)), 'run_id' => (int)$run_id);
		$ctx['set'] = $this->_hr_settings();
		$ctx['types'] = $this->_leave_types(true);
		$ctx['wk'] = $this->_weekend();
		$ctx['hol'] = $this->_holidays($year . '-01-01', $year . '-12-31');
		$ctx['exit'] = array();
		if ($this->db->table_exists('ci_user_exit')) foreach ($this->db->get('ci_user_exit')->result_array() as $r) $ctx['exit'][(int)$r['user_id']] = $r['left_on'];
		// people on the payroll: still working, or left during or after this month
		$people = array();
		$on = array();
		foreach ($this->db->select('id, admin_status')->get('ci_user')->result_array() as $r) $on[(int)$r['id']] = (int)$r['admin_status'] === 1;
		foreach ($this->_people(true) as $u) {
			$uid = (int)$u['id'];
			if (isset($ctx['exit'][$uid]) && $ctx['exit'][$uid] < $from) continue;
			if (!isset($ctx['exit'][$uid]) && empty($on[$uid])) continue;
			if (!empty($u['c_doj']) && strtotime($u['c_doj']) && date('Y-m-d', strtotime($u['c_doj'])) > $to) continue;
			$people[$uid] = $u;
		}
		$ctx['people'] = $people;
		$ids = array_keys($people);
		$ctx['sal'] = $this->_salaries_on($people, $to);
		$ctx['leaves'] = array();
		$ctx['items'] = array();
		$ctx['loans'] = array();
		$ctx['emp'] = array();
		$ctx['att'] = array();
		if ($ids) {
			foreach ($this->db->where_in('user_id', $ids)->where('status', 'Approved')->where('from_date <=', $to)->where('to_date >=', $year . '-01-01')->order_by('from_date ASC, id ASC')->get('ci_leaves')->result_array() as $l) $ctx['leaves'][(int)$l['user_id']][] = $l;
			foreach ($this->db->where_in('user_id', $ids)->where('month', $month)->order_by('id ASC')->get('ci_hr_pay_items')->result_array() as $r) $ctx['items'][(int)$r['user_id']][] = $r;
			foreach ($this->db->where_in('user_id', $ids)->where('status', 'Active')->where('start_month <=', $month)->get('ci_hr_loans')->result_array() as $r) {
				$paid = $this->db->query("SELECT COALESCE(SUM(P.amount),0) AS s FROM ci_hr_loan_pay P LEFT JOIN ci_hr_pay_runs R ON R.id = P.run_id WHERE P.loan_id = ? AND (P.settlement_id IS NOT NULL OR (P.run_id <> ? AND R.status IN ('Approved','Paid')))", array((int)$r['id'], (int)$run_id))->row_array();
				$r['left'] = round((float)$r['amount'] - (float)$paid['s'], 2);
				if ($r['left'] > 0) $ctx['loans'][(int)$r['user_id']][] = $r;
			}
			foreach ($this->db->where_in('user_id', $ids)->get('ci_hr_employee')->result_array() as $r) $ctx['emp'][(int)$r['user_id']] = $r;
			if ($ctx['set']['deduct_absence'] === '1') foreach ($this->db->select('user_id, attendance_date')->where_in('user_id', $ids)->where('attendance_date >=', $from)->where('attendance_date <=', $to)->get('ci_attendance')->result_array() as $r) $ctx['att'][(int)$r['user_id']][$r['attendance_date']] = 1;
			$ctx['users'] = array();
			foreach ($this->db->select('id, name, emp_id, department, c_doj, iban, bank, bank_no')->where_in('id', $ids)->get('ci_user')->result_array() as $r) $ctx['users'][(int)$r['id']] = $r;
			$ctx['desig'] = array();
			foreach ($this->db->where_in('user_id', $ids)->get('ci_user_designation')->result_array() as $r) $ctx['desig'][(int)$r['user_id']] = $r['designation'];
		}
		return $ctx;
	}

	/** One payslip (not saved): null when the person has no salary or did not work in the month. */
	private function _calc_slip($uid, $ctx)
	{
		if (empty($ctx['sal'][$uid])) return null;
		$s = $ctx['sal'][$uid];
		$u = $ctx['users'][$uid];
		$set = $ctx['set'];
		$start = $ctx['from'];
		$end = $ctx['to'];
		if (!empty($u['c_doj']) && strtotime($u['c_doj'])) $start = max($start, date('Y-m-d', strtotime($u['c_doj'])));
		if (isset($ctx['exit'][$uid])) $end = min($end, $ctx['exit'][$uid]);
		if ($start > $end) return null;
		$dim = $ctx['dim'];
		$days = (int)round((strtotime($end) - strtotime($start)) / 86400) + 1;
		$f = $days >= $dim ? 1.0 : $days / $dim;
		$notes = array();
		if ($f < 1) $notes[] = 'Paid for ' . $days . ' of ' . $dim . ' days (' . ($start > $ctx['from'] ? 'joined ' . date('d M', strtotime($start)) : 'last day ' . date('d M', strtotime($end))) . ').';
		if ($s['source'] === 'staff') $notes[] = 'No salary structure in the HR file: the staff salary is used as basic.';

		$earn = array();
		$fixed = 0.0;
		foreach (self::_salary_lines($s) as $l) { $a = round($l[1] * $f, 2); if ($a > 0) { $earn[] = array($l[0], $a); $fixed += $a; } }
		$variable = 0.0;
		$ded = array();
		$hourly = (float)$s['basic'] / 30 / max(1, (float)$set['work_hours']);
		foreach (isset($ctx['items'][$uid]) ? $ctx['items'][$uid] : array() as $it) {
			if ($it['kind'] === 'ot' || $it['kind'] === 'ot_rest') {
				$rate = (float)($it['kind'] === 'ot' ? $set['ot_rate'] : $set['ot_rest_rate']);
				$a = (float)$it['amount'] > 0 ? (float)$it['amount'] : round($hourly * (float)$it['hours'] * $rate, 2);
				$earn[] = array(($it['label'] !== '' ? $it['label'] : 'Overtime') . ' (' . (0 + $it['hours']) . ' h x ' . (0 + $rate) . ')', $a);
				$variable += $a;
			} elseif ($it['kind'] === 'addition') {
				$earn[] = array($it['label'], (float)$it['amount']);
				$variable += (float)$it['amount'];
			} else {
				$ded[] = array($it['label'], (float)$it['amount']);
			}
		}
		$daily = (float)$s['total'] / 30;
		$lc = $this->_leave_cuts(isset($ctx['leaves'][$uid]) ? $ctx['leaves'][$uid] : array(), $ctx['types'], $start, $end, $ctx['hol'], $ctx['wk']);
		foreach ($lc['by'] as $name => $b) {
			if ($b['cut'] <= 0) continue;
			$fd = rtrim(rtrim(number_format($b['days'], 1), '0'), '.');
			$label = $name . ', ' . $fd . ' day' . ($b['days'] == 1 ? '' : 's') . ($b['cut'] < $b['days'] ? ' (' . rtrim(rtrim(number_format($b['cut'], 1), '0'), '.') . ' unpaid)' : ' unpaid');
			$ded[] = array($label, round($daily * $b['cut'], 2));
		}
		if ($set['deduct_absence'] === '1') {
			$abs = 0;
			$last = min($end, date('Y-m-d', strtotime('-1 day')));
			$on_leave = array();
			foreach (isset($ctx['leaves'][$uid]) ? $ctx['leaves'][$uid] : array() as $l) for ($d = strtotime($l['from_date']); $d <= strtotime($l['to_date']); $d += 86400) $on_leave[date('Y-m-d', $d)] = 1;
			for ($d = strtotime($start); $d <= strtotime($last); $d += 86400) {
				$k = date('Y-m-d', $d);
				if (isset($ctx['att'][$uid][$k]) || isset($on_leave[$k]) || isset($ctx['hol'][$k]) || in_array(date('D', $d), $ctx['wk'], true)) continue;
				$abs++;
			}
			if ($abs > 0) $ded[] = array('Absent without leave, ' . $abs . ' day' . ($abs === 1 ? '' : 's'), round($daily * $abs, 2));
		}
		$gross = round($fixed + $variable, 2);
		$sum = 0.0;
		foreach ($ded as $d) $sum += $d[1];
		$loan_pay = array();
		foreach (isset($ctx['loans'][$uid]) ? $ctx['loans'][$uid] : array() as $ln) {
			$a = round(min((float)$ln['monthly'], (float)$ln['left'], max(0, $gross - $sum)), 2);
			if ($a <= 0) continue;
			$ded[] = array($ln['kind'] . ' repayment (' . number_format((float)$ln['left'] - $a, 2) . ' left after this)', $a);
			$sum += $a;
			$loan_pay[] = array('loan_id' => (int)$ln['id'], 'amount' => $a);
		}
		$sum = round($sum, 2);
		$net = round($gross - $sum, 2);
		if ($net < 0) { $notes[] = 'Deductions are more than the pay; net set to 0.'; $net = 0.0; }
		$e = isset($ctx['emp'][$uid]) ? $ctx['emp'][$uid] : array();
		$info = array('name' => $u['name'], 'emp_id' => $u['emp_id'], 'department' => $u['department'], 'designation' => isset($ctx['desig'][$uid]) ? $ctx['desig'][$uid] : '',
			'doj' => $u['c_doj'], 'iban' => $u['iban'], 'bank' => $u['bank'], 'payment_method' => isset($e['payment_method']) ? $e['payment_method'] : '',
			'routing' => isset($e['wps_routing_code']) ? $e['wps_routing_code'] : '', 'mol_id' => isset($e['mol_person_id']) ? $e['mol_person_id'] : '',
			'start' => $start, 'end' => $end, 'currency' => $set['currency'], 'notes' => $notes);
		return array('slip' => array('user_id' => $uid, 'info' => json_encode($info), 'earnings' => json_encode($earn), 'deductions' => json_encode($ded),
			'days_in_month' => $dim, 'days_paid' => $days, 'leave_days' => $lc['leave_days'], 'fixed' => round($fixed, 2), 'variable' => round($variable, 2),
			'gross' => $gross, 'total_deductions' => $sum, 'net' => $net), 'loan_pay' => $loan_pay);
	}

	/** Work out every payslip of a draft run again. */
	private function _calc_run($run)
	{
		$rid = (int)$run['id'];
		$ctx = $this->_pay_context($run['month'], $rid);
		$this->db->trans_start();
		$this->db->where('run_id', $rid)->delete('ci_hr_payslips');
		$this->db->where('run_id', $rid)->delete('ci_hr_loan_pay');
		$t = array('people' => 0, 'gross' => 0.0, 'deductions' => 0.0, 'net' => 0.0);
		foreach (array_keys($ctx['people']) as $uid) {
			$r = $this->_calc_slip($uid, $ctx);
			if (!$r) continue;
			$this->db->insert('ci_hr_payslips', $r['slip'] + array('run_id' => $rid));
			foreach ($r['loan_pay'] as $lp) $this->db->insert('ci_hr_loan_pay', $lp + array('user_id' => $uid, 'run_id' => $rid, 'month' => $run['month']));
			$t['people']++;
			$t['gross'] += $r['slip']['gross'];
			$t['deductions'] += $r['slip']['total_deductions'];
			$t['net'] += $r['slip']['net'];
		}
		$this->db->where('id', $rid)->update('ci_hr_pay_runs', array('people' => $t['people'], 'gross' => round($t['gross'], 2), 'deductions' => round($t['deductions'], 2), 'net' => round($t['net'], 2), 'calculated_at' => date('Y-m-d H:i:s')));
		$this->db->trans_complete();
		return $t;
	}

	private function _run($id)
	{
		$this->_pay_tables();
		$run = (int)$id > 0 ? $this->db->where('id', (int)$id)->get('ci_hr_pay_runs')->row_array() : array();
		if (empty($run)) {
			$this->session->set_flashdata('error_message', 'Pay run not found.');
			redirect('manager/hr/payroll');
		}
		return $run;
	}

	private function _post_only($back)
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') redirect($back);
	}

	private function _need($ok, $msg, $back)
	{
		if ($ok) return;
		$this->session->set_flashdata('error_message', $msg);
		redirect($back);
	}

	/** Payroll home: pay runs, and the older payroll records kept from before. */
	public function payroll()
	{
		$this->_pay_tables();
		$data = array();
		$data['info'] = array('title' => 'Payroll - HR', 'page_heading' => 'Payroll');
		$data['runs'] = $this->db->query('SELECT R.*, A.name AS approved_name FROM ci_hr_pay_runs R LEFT JOIN ci_user A ON A.id = R.approved_by ORDER BY R.month DESC')->result_array();
		$have = array();
		foreach ($data['runs'] as $r) $have[$r['month']] = 1;
		$next = date('Y-m');
		while (isset($have[$next])) $next = date('Y-m', strtotime($next . '-01 +1 month'));
		$data['next_month'] = $next;
		$data['old'] = $this->db->query('SELECT month_year, COUNT(*) AS n, SUM(net_salary) AS net, SUM(status = \'Paid\') AS paid FROM ci_hr_payroll GROUP BY month_year ORDER BY month_year DESC')->result_array();
		$data['set'] = $this->_hr_settings();
		$data['can_edit'] = $this->_can_edit_pay();
		$data['can_approve'] = $this->_can_approve_pay();
		$data['loans_open'] = (int)$this->db->where('status', 'Active')->count_all_results('ci_hr_loans');
		$this->load->view('manager/hr/payroll', $data);
	}

	/** Older payroll records (before pay runs), one month as CSV. */
	public function payroll_old_csv()
	{
		$m = preg_match('/^\d{4}-\d{2}$/', (string)$this->input->get('month')) ? $this->input->get('month') : '';
		if ($m === '') redirect('manager/hr/payroll');
		$rows = $this->db->query('SELECT U.name, U.emp_id, P.* FROM ci_hr_payroll P LEFT JOIN ci_user U ON U.id = P.user_id WHERE P.month_year = ? ORDER BY U.name', array($m))->result_array();
		$out = fopen('php://temp', 'w+');
		fputcsv($out, array('Employee', 'Employee ID', 'Basic', 'Housing', 'Transport', 'Other', 'Gross', 'Deductions', 'Net', 'Status', 'Paid on'));
		foreach ($rows as $r) fputcsv($out, array($r['name'], $r['emp_id'], $r['basic_salary'], $r['housing_allowance'], $r['transport_allowance'], $r['other_allowance'], $r['gross_salary'], (float)$r['tax_deduction'] + (float)$r['other_deductions'], $r['net_salary'], $r['status'], $r['payment_date']));
		rewind($out);
		$csv = stream_get_contents($out);
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="payroll-old-' . $m . '.csv"');
		echo "\xEF\xBB\xBF" . $csv;
		exit;
	}

	/** Start a pay run for a month (POST month_year) and work it out. */
	public function generate_payroll()
	{
		$this->_pay_tables();
		$this->_post_only('manager/hr/payroll');
		$month = trim((string)($this->input->post('month_year') ?: $this->input->post('period_month')));
		$this->_need(preg_match('/^\d{4}-\d{2}$/', $month), 'Choose the month.', 'manager/hr/payroll');
		$run = $this->db->where('month', $month)->get('ci_hr_pay_runs')->row_array();
		if ($run) redirect('manager/hr/payroll_run/' . $run['id']);
		$this->db->insert('ci_hr_pay_runs', array('month' => $month, 'status' => 'Draft', 'created_by' => $this->cur_uid, 'created_at' => date('Y-m-d H:i:s')));
		$run = $this->db->where('id', $this->db->insert_id())->get('ci_hr_pay_runs')->row_array();
		$t = $this->_calc_run($run);
		$this->ci_admin_model->log_admin_action('hr_payroll_run', 'Started payroll for ' . $month . ': ' . $t['people'] . ' people', 'SUCCESS');
		$this->session->set_flashdata('message', 'Payroll for ' . date('F Y', strtotime($month . '-01')) . ' worked out for ' . $t['people'] . ' people. Check it, add overtime or other items, then approve.');
		redirect('manager/hr/payroll_run/' . $run['id']);
	}

	/** The old status links: now part of the pay run. */
	public function payroll_status($id = 0, $status = '')
	{
		redirect('manager/hr/payroll');
	}

	public function payroll_run($id = 0)
	{
		$run = $this->_run($id);
		$data = array();
		$data['info'] = array('title' => 'Payroll ' . date('F Y', strtotime($run['month'] . '-01')) . ' - HR', 'page_heading' => 'Payroll');
		$data['run'] = $run;
		$data['slips'] = $this->db->query('SELECT S.* FROM ci_hr_payslips S WHERE S.run_id = ? ORDER BY S.id', array((int)$run['id']))->result_array();
		foreach ($data['slips'] as &$s) { $s['i'] = json_decode($s['info'], true); $s['e'] = json_decode($s['earnings'], true); $s['d'] = json_decode($s['deductions'], true); }
		unset($s);
		usort($data['slips'], function ($a, $b) { return strcasecmp($a['i']['name'], $b['i']['name']); });
		$data['items'] = $this->db->query('SELECT I.*, U.name FROM ci_hr_pay_items I LEFT JOIN ci_user U ON U.id = I.user_id WHERE I.month = ? ORDER BY U.name, I.id', array($run['month']))->result_array();
		$ctx = $this->_pay_context($run['month'], (int)$run['id']);
		$paid = array();
		foreach ($data['slips'] as $s) $paid[(int)$s['user_id']] = 1;
		$data['missing'] = array();
		foreach ($ctx['people'] as $uid => $p) if (!isset($paid[$uid]) && empty($ctx['sal'][$uid])) $data['missing'][] = $p;
		$data['people'] = $ctx['people'];
		$data['set'] = $ctx['set'];
		// WPS check: people paid through WPS need the MOL person ID, the agent routing code and the IBAN
		$wps = array();
		foreach ($data['slips'] as $s) {
			$i = $s['i'];
			if (stripos((string)$i['payment_method'], 'wps') === false && $i['payment_method'] !== '') continue;
			$miss = array();
			if (!preg_match('/^\d{14}$/', (string)$i['mol_id'])) $miss[] = 'MOL person ID (14 digits)';
			if (!preg_match('/^\d{9}$/', (string)($i['routing'] !== '' ? $i['routing'] : $ctx['set']['employer_routing_code']))) $miss[] = 'routing code';
			if (!preg_match('/^AE\d{21}$/', strtoupper(str_replace(' ', '', (string)$i['iban'])))) $miss[] = 'IBAN';
			if ($miss) $wps[] = array('user_id' => $s['user_id'], 'name' => $i['name'], 'miss' => $miss);
		}
		$data['wps_missing'] = $wps;
		$data['wps_company'] = preg_match('/^\d{13}$/', (string)$ctx['set']['mol_establishment_id']) && preg_match('/^\d{9}$/', (string)$ctx['set']['employer_routing_code']);
		$data['item_kinds'] = self::pay_item_kinds();
		$data['can_edit'] = $this->_can_edit_pay();
		$data['can_approve'] = $this->_can_approve_pay();
		foreach (array('created_by', 'approved_by', 'paid_by') as $k) {
			$data['who'][$k] = '';
			if (!empty($run[$k])) { $w = $this->db->select('name')->where('id', (int)$run[$k])->get('ci_user')->row_array(); $data['who'][$k] = $w ? $w['name'] : ''; }
		}
		$this->load->view('manager/hr/payroll_run', $data);
	}

	public function payroll_recalc($id = 0)
	{
		$run = $this->_run($id);
		$back = 'manager/hr/payroll_run/' . $run['id'];
		$this->_post_only($back);
		$this->_need($run['status'] === 'Draft', 'Only a draft can be worked out again. Reopen it first.', $back);
		$t = $this->_calc_run($run);
		$this->session->set_flashdata('message', 'Worked out again: ' . $t['people'] . ' payslips.');
		redirect($back);
	}

	/** Overtime, bonus or deduction for one person in a month (POST). */
	public function payroll_item_add()
	{
		$this->_pay_tables();
		$this->_post_only('manager/hr/payroll');
		$month = (string)$this->input->post('month');
		$run = $this->db->where('month', $month)->get('ci_hr_pay_runs')->row_array();
		$back = $run ? 'manager/hr/payroll_run/' . $run['id'] : 'manager/hr/payroll';
		$this->_need(!$run || $run['status'] === 'Draft', 'This payroll is ' . strtolower($run ? $run['status'] : '') . '. Reopen it to make changes.', $back);
		$uid = (int)$this->input->post('user_id');
		$kind = (string)$this->input->post('kind');
		$kinds = self::pay_item_kinds();
		$hours = round((float)$this->input->post('hours'), 2);
		$amount = round((float)str_replace(',', '', (string)$this->input->post('amount')), 2);
		$label = mb_substr(trim((string)$this->input->post('label')), 0, 100);
		$ot = ($kind === 'ot' || $kind === 'ot_rest');
		$ok = $uid > 0 && isset($kinds[$kind]) && preg_match('/^\d{4}-\d{2}$/', $month) && ($ot ? $hours > 0 && $hours <= 300 : ($amount > 0 && $label !== '')) && $amount >= 0;
		$this->_need($ok, $ot ? 'Enter the overtime hours.' : 'Enter a description and an amount above 0.', $back);
		$this->db->insert('ci_hr_pay_items', array('user_id' => $uid, 'month' => $month, 'kind' => $kind, 'label' => $label, 'hours' => $ot ? $hours : 0, 'amount' => $amount,
			'created_by' => $this->cur_uid, 'created_at' => date('Y-m-d H:i:s')));
		if ($run) $this->_calc_run($run);
		$this->ci_admin_model->log_admin_action('hr_payroll_item', 'Added ' . $kinds[$kind] . ' for user ID #' . $uid . ' in ' . $month . ($ot ? ': ' . $hours . ' h' : ': ' . $amount), 'SUCCESS');
		$this->session->set_flashdata('message', 'Added' . ($run ? ' and payslip updated.' : '.'));
		redirect($back);
	}

	public function payroll_item_delete($id = 0)
	{
		$this->_pay_tables();
		$this->_post_only('manager/hr/payroll');
		$it = $this->db->where('id', (int)$id)->get('ci_hr_pay_items')->row_array();
		$run = $it ? $this->db->where('month', $it['month'])->get('ci_hr_pay_runs')->row_array() : array();
		$back = $run ? 'manager/hr/payroll_run/' . $run['id'] : 'manager/hr/payroll';
		$this->_need($it && (!$run || $run['status'] === 'Draft'), 'This item cannot be removed now.', $back);
		$this->db->where('id', (int)$id)->delete('ci_hr_pay_items');
		if ($run) $this->_calc_run($run);
		$this->session->set_flashdata('message', 'Removed' . ($run ? ' and payslip updated.' : '.'));
		redirect($back);
	}

	/** Draft -> Approved (needs Approve Payroll), Approved -> Paid, Approved -> Draft (reopen), Draft -> deleted. */
	public function payroll_step($id = 0, $step = '')
	{
		$run = $this->_run($id);
		$back = 'manager/hr/payroll_run/' . $run['id'];
		$this->_post_only($back);
		$m = date('F Y', strtotime($run['month'] . '-01'));
		$now = date('Y-m-d H:i:s');
		if ($step === 'approve') {
			$this->_need($this->_can_approve_pay(), 'You do not have the Approve Payroll right.', $back);
			$this->_need($run['status'] === 'Draft', 'Only a draft can be approved.', $back);
			$this->_calc_run($run);
			$this->db->where('id', $run['id'])->update('ci_hr_pay_runs', array('status' => 'Approved', 'approved_by' => $this->cur_uid, 'approved_at' => $now));
			$msg = 'Payroll for ' . $m . ' approved. Download the WPS file for the bank, then mark it as paid.';
		} elseif ($step === 'reopen') {
			$this->_need($this->_can_approve_pay(), 'You do not have the Approve Payroll right.', $back);
			$this->_need($run['status'] === 'Approved', 'Only an approved (not paid) payroll can be reopened.', $back);
			$this->db->where('id', $run['id'])->update('ci_hr_pay_runs', array('status' => 'Draft', 'approved_by' => null, 'approved_at' => null));
			$msg = 'Payroll for ' . $m . ' reopened as a draft.';
		} elseif ($step === 'paid') {
			$this->_need($this->_can_edit_pay(), 'You need Full access to Payroll to do this.', $back);
			$this->_need($run['status'] === 'Approved', 'Approve the payroll first.', $back);
			$this->db->where('id', $run['id'])->update('ci_hr_pay_runs', array('status' => 'Paid', 'paid_by' => $this->cur_uid, 'paid_at' => $now));
			foreach ($this->db->select('id, user_id, net')->where('run_id', $run['id'])->get('ci_hr_payslips')->result_array() as $s)
				$this->ci_admin_model->add_notification((int)$s['user_id'], 'payslip', 'Payslip ready', 'Your payslip for ' . $m . ' is ready.', base_url('manager/hr/payslip/' . $s['id']), 'fa fa-money', '#16a34a');
			// loans fully repaid are closed
			foreach ($this->db->query("SELECT L.id, L.amount, COALESCE(SUM(CASE WHEN R.status IN ('Approved','Paid') OR P.settlement_id IS NOT NULL THEN P.amount END),0) AS paid FROM ci_hr_loans L LEFT JOIN ci_hr_loan_pay P ON P.loan_id = L.id LEFT JOIN ci_hr_pay_runs R ON R.id = P.run_id WHERE L.status = 'Active' GROUP BY L.id")->result_array() as $l)
				if ((float)$l['paid'] >= (float)$l['amount'] - 0.001) $this->db->where('id', $l['id'])->update('ci_hr_loans', array('status' => 'Repaid', 'closed_at' => $now));
			$msg = 'Payroll for ' . $m . ' marked as paid. Everyone can now see their payslip.';
		} elseif ($step === 'delete') {
			$this->_need($this->_can_edit_pay() && $run['status'] === 'Draft', 'Only a draft can be deleted.', $back);
			$this->db->where('run_id', $run['id'])->delete('ci_hr_payslips');
			$this->db->where('run_id', $run['id'])->delete('ci_hr_loan_pay');
			$this->db->where('id', $run['id'])->delete('ci_hr_pay_runs');
			$this->ci_admin_model->log_admin_action('hr_payroll_delete', 'Deleted draft payroll for ' . $run['month'], 'SUCCESS');
			$this->session->set_flashdata('message', 'Draft payroll for ' . $m . ' deleted. Overtime and other items for the month are kept.');
			redirect('manager/hr/payroll');
		} else {
			redirect($back);
		}
		$this->ci_admin_model->log_admin_action('hr_payroll_' . $step, $msg, 'SUCCESS');
		$this->session->set_flashdata('message', $msg);
		redirect($back);
	}

	/** Pay run as CSV (one line per person, every pay line in its own column). */
	public function payroll_csv($id = 0)
	{
		$run = $this->_run($id);
		$slips = $this->db->where('run_id', $run['id'])->get('ci_hr_payslips')->result_array();
		$ecols = array(); $dcols = array();
		foreach ($slips as &$s) {
			$s['i'] = json_decode($s['info'], true); $s['e'] = array(); $s['d'] = array();
			foreach ((array)json_decode($s['earnings'], true) as $l) { $k = preg_replace('/ \(.*$/', '', $l[0]); $ecols[$k] = 1; $s['e'][$k] = (isset($s['e'][$k]) ? $s['e'][$k] : 0) + $l[1]; }
			foreach ((array)json_decode($s['deductions'], true) as $l) { $k = preg_replace('/(,| \().*$/', '', $l[0]); $dcols[$k] = 1; $s['d'][$k] = (isset($s['d'][$k]) ? $s['d'][$k] : 0) + $l[1]; }
		}
		unset($s);
		usort($slips, function ($a, $b) { return strcasecmp($a['i']['name'], $b['i']['name']); });
		$out = fopen('php://temp', 'w+');
		fputcsv($out, array_merge(array('Employee', 'Employee ID', 'Department', 'Days paid'), array_keys($ecols), array('Gross'), array_keys($dcols), array('Total deductions', 'Net pay', 'Payment method', 'IBAN')));
		foreach ($slips as $s) {
			$row = array($s['i']['name'], $s['i']['emp_id'], $s['i']['department'], 0 + $s['days_paid']);
			foreach (array_keys($ecols) as $k) $row[] = isset($s['e'][$k]) ? round($s['e'][$k], 2) : '';
			$row[] = $s['gross'];
			foreach (array_keys($dcols) as $k) $row[] = isset($s['d'][$k]) ? round($s['d'][$k], 2) : '';
			array_push($row, $s['total_deductions'], $s['net'], $s['i']['payment_method'], $s['i']['iban']);
			fputcsv($out, $row);
		}
		rewind($out);
		$csv = stream_get_contents($out);
		$this->ci_admin_model->log_admin_action('hr_payroll_export', 'Downloaded payroll ' . $run['month'], 'SUCCESS');
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="payroll-' . $run['month'] . '.csv"');
		echo "\xEF\xBB\xBF" . $csv;
		exit;
	}

	/**
	 * WPS Salary Information File (UAE): one EDR line per employee paid through WPS and one SCR line for the company.
	 * EDR,person ID,agent routing code,IBAN,pay start,pay end,days,fixed pay,variable pay,days on leave
	 * SCR,establishment ID,routing code,file date,file time,salary month (MMYYYY),EDR count,total,currency,reference
	 */
	public function payroll_sif($id = 0)
	{
		$run = $this->_run($id);
		$back = 'manager/hr/payroll_run/' . $run['id'];
		$this->_need($run['status'] !== 'Draft', 'Approve the payroll before making the WPS file.', $back);
		$set = $this->_hr_settings();
		$this->_need(preg_match('/^\d{13}$/', $set['mol_establishment_id']) && preg_match('/^\d{9}$/', $set['employer_routing_code']), 'Add the 13-digit MOL establishment ID and the 9-digit company bank routing code in HR Settings first.', $back);
		$lines = array();
		$total = 0.0;
		$skipped = 0;
		foreach ($this->db->where('run_id', $run['id'])->where('net >', 0)->get('ci_hr_payslips')->result_array() as $s) {
			$i = json_decode($s['info'], true);
			if ($i['payment_method'] !== '' && stripos($i['payment_method'], 'wps') === false) continue;
			$iban = strtoupper(str_replace(' ', '', (string)$i['iban']));
			$routing = $i['routing'] !== '' ? $i['routing'] : $set['employer_routing_code'];
			if (!preg_match('/^\d{14}$/', (string)$i['mol_id']) || !preg_match('/^\d{9}$/', (string)$routing) || !preg_match('/^AE\d{21}$/', $iban)) { $skipped++; continue; }
			// fixed = net pay less the variable part (variable pay cannot be more than the net)
			$var = min((float)$s['variable'], (float)$s['net']);
			$fix = round((float)$s['net'] - $var, 2);
			$lines[] = implode(',', array('EDR', $i['mol_id'], $routing, $iban, $i['start'], $i['end'], (int)round((float)$s['days_paid']), number_format($fix, 2, '.', ''), number_format($var, 2, '.', ''), (int)round((float)$s['leave_days'])));
			$total += (float)$s['net'];
		}
		$this->_need(count($lines) > 0, 'Nobody in this payroll has complete WPS details (MOL person ID, routing code and IBAN).', $back);
		$now = time();
		$lines[] = implode(',', array('SCR', $set['mol_establishment_id'], $set['employer_routing_code'], date('Y-m-d', $now), date('Hi', $now), date('mY', strtotime($run['month'] . '-01')), count($lines), number_format($total, 2, '.', ''), $set['currency'], 'SAL' . str_replace('-', '', $run['month'])));
		$this->ci_admin_model->log_admin_action('hr_payroll_wps', 'Made WPS file for ' . $run['month'] . ': ' . (count($lines) - 1) . ' people' . ($skipped ? ', ' . $skipped . ' left out (details missing)' : ''), 'SUCCESS');
		header('Content-Type: text/plain; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . $set['mol_establishment_id'] . date('ymdHis', $now) . '.SIF"');
		echo implode("\r\n", $lines) . "\r\n";
		exit;
	}

	/** One payslip, ready to print or save as PDF. HR with Payroll access, or the employee once the payroll is approved. */
	public function payslip($id = 0)
	{
		$this->_pay_tables();
		$s = $this->db->query('SELECT S.*, R.month, R.status AS run_status FROM ci_hr_payslips S JOIN ci_hr_pay_runs R ON R.id = S.run_id WHERE S.id = ?', array((int)$id))->row_array();
		$own = $s && (int)$s['user_id'] === $this->cur_uid && in_array($s['run_status'], array('Approved', 'Paid'), true);
		$hr = $this->is_admin || crm_can('hr_payroll', 'read');
		if (!$s || (!$own && !$hr)) {
			$this->session->set_flashdata('error_message', 'Payslip not found.');
			redirect($hr ? 'manager/hr/payroll' : 'manager/hr/my_pay');
		}
		$data = array('s' => $s, 'i' => json_decode($s['info'], true), 'e' => json_decode($s['earnings'], true), 'd' => json_decode($s['deductions'], true), 'set' => $this->_hr_settings(),
			'back' => $hr && !$own ? 'manager/hr/payroll_run/' . $s['run_id'] : 'manager/hr/my_pay');
		$this->load->view('manager/hr/payslip', $data);
	}

	/* ---------- Loans and salary advances ---------- */

	private function _loan_rows($where = '', $args = array())
	{
		return $this->db->query("SELECT L.*, U.name, U.emp_id,
			COALESCE((SELECT SUM(P.amount) FROM ci_hr_loan_pay P LEFT JOIN ci_hr_pay_runs R ON R.id = P.run_id WHERE P.loan_id = L.id AND (R.status IN ('Approved','Paid') OR P.settlement_id IS NOT NULL)), 0) AS repaid
			FROM ci_hr_loans L LEFT JOIN ci_user U ON U.id = L.user_id " . $where . " ORDER BY FIELD(L.status, 'Active') DESC, L.id DESC", $args)->result_array();
	}

	public function loans()
	{
		$this->_pay_tables();
		$data = array();
		$data['info'] = array('title' => 'Loans & Advances - HR', 'page_heading' => 'Loans & Advances');
		$data['loans'] = $this->_loan_rows();
		$data['people'] = $this->_people();
		$data['set'] = $this->_hr_settings();
		$data['can_edit'] = $this->_can_edit_pay();
		$this->load->view('manager/hr/loans', $data);
	}

	public function loan_save()
	{
		$this->_pay_tables();
		$this->_post_only('manager/hr/loans');
		$uid = (int)$this->input->post('user_id');
		$kind = $this->input->post('kind') === 'Advance' ? 'Advance' : 'Loan';
		$amount = round((float)str_replace(',', '', (string)$this->input->post('amount')), 2);
		$monthly = round((float)str_replace(',', '', (string)$this->input->post('monthly')), 2);
		$start = (string)$this->input->post('start_month');
		if ($kind === 'Advance' && $monthly <= 0) $monthly = $amount;
		$u = $uid ? $this->db->select('name')->where('id', $uid)->get('ci_user')->row_array() : array();
		$this->_need($u && $amount > 0 && $monthly > 0 && $monthly <= $amount && preg_match('/^\d{4}-\d{2}$/', $start), 'Choose the employee, the amount, a monthly repayment (not more than the amount) and the first month.', 'manager/hr/loans');
		$this->db->insert('ci_hr_loans', array('user_id' => $uid, 'kind' => $kind, 'amount' => $amount, 'monthly' => $monthly, 'start_month' => $start,
			'reason' => mb_substr(trim((string)$this->input->post('reason')), 0, 255), 'status' => 'Active', 'created_by' => $this->cur_uid, 'created_at' => date('Y-m-d H:i:s')));
		$this->ci_admin_model->log_admin_action('hr_loan_add', $kind . ' of ' . number_format($amount, 2) . ' for ' . $u['name'] . ', ' . number_format($monthly, 2) . ' a month from ' . $start, 'SUCCESS');
		$this->session->set_flashdata('message', $kind . ' added for ' . $u['name'] . '. It is taken from salary from ' . date('F Y', strtotime($start . '-01')) . '.');
		redirect('manager/hr/loans');
	}

	/** Stop repayments (written off or settled outside payroll). */
	public function loan_close($id = 0)
	{
		$this->_pay_tables();
		$this->_post_only('manager/hr/loans');
		$l = $this->db->where('id', (int)$id)->get('ci_hr_loans')->row_array();
		$this->_need($l && $l['status'] === 'Active', 'Loan not found or already closed.', 'manager/hr/loans');
		$note = mb_substr(trim((string)$this->input->post('note')), 0, 255);
		$this->db->where('id', $l['id'])->update('ci_hr_loans', array('status' => 'Closed', 'closed_by' => $this->cur_uid, 'closed_at' => date('Y-m-d H:i:s'), 'close_note' => $note));
		$this->ci_admin_model->log_admin_action('hr_loan_close', 'Closed ' . $l['kind'] . ' #' . $l['id'] . ' of user ID #' . $l['user_id'] . ($note !== '' ? ': ' . $note : ''), 'SUCCESS');
		$this->session->set_flashdata('message', $l['kind'] . ' closed. No more repayments will be taken.');
		redirect('manager/hr/loans');
	}

	/* ---------- Payouts to verticals (referral partners) ---------- */

	private function _can_pay_verticals()
	{
		return $this->is_admin || crm_can('hr_vertical_payouts', 'write');
	}

	/** Each vertical with its clients, the agreed payout ("Amount to Pay" on its clients), what was paid and what is left. */
	public function vertical_payouts()
	{
		if (!($this->is_admin || crm_can('hr_vertical_payouts', 'read'))) { $this->session->set_flashdata('error_message', 'You do not have access to Vertical Payouts.'); redirect('manager/hr'); }
		$this->ci_admin_model->vertical_payout_table();
		$data = array();
		$data['info'] = array('title' => 'Vertical Payouts - HR', 'page_heading' => 'Vertical Payouts');
		$data['set'] = $this->_hr_settings();
		$data['can_edit'] = $this->_can_pay_verticals();
		$data['verticals'] = $this->db->query("SELECT V.id, V.v_id, V.company_name, V.poc,
				(SELECT COUNT(*) FROM ci_leads L WHERE L.vertical_id = V.id) AS clients,
				(SELECT COALESCE(SUM(CAST(NULLIF(REPLACE(L.v_amount, ',', ''), '') AS DECIMAL(14,2))), 0) FROM ci_leads L WHERE L.vertical_id = V.id) AS owed,
				(SELECT COALESCE(SUM(P.amount), 0) FROM ci_vertical_payouts P WHERE P.vertical_id = V.id) AS paid,
				(SELECT MAX(P.pay_date) FROM ci_vertical_payouts P WHERE P.vertical_id = V.id) AS last_paid
			FROM ci_verticals V ORDER BY V.company_name ASC")->result_array();
		$data['clients'] = array();
		foreach ($this->db->query("SELECT id, vertical_id, customer, v_amount FROM ci_leads WHERE vertical_id > 0 ORDER BY customer ASC")->result_array() as $c) {
			$data['clients'][(int)$c['vertical_id']][] = array('id' => (int)$c['id'], 'n' => $c['customer'], 'a' => (float)str_replace(',', '', (string)$c['v_amount']));
		}
		$vf = (int)$this->input->get('vertical');
		$data['vf'] = $vf;
		$data['payouts'] = $this->db->query("SELECT P.*, V.company_name, V.v_id, L.customer, U.name AS recorded_name
			FROM ci_vertical_payouts P LEFT JOIN ci_verticals V ON V.id = P.vertical_id LEFT JOIN ci_leads L ON L.id = P.lead_id LEFT JOIN ci_user U ON U.id = P.recorded_by"
			. ($vf ? ' WHERE P.vertical_id = ' . $vf : '') . " ORDER BY P.pay_date DESC, P.id DESC LIMIT 300")->result_array();
		$this->load->view('manager/hr/vertical_payouts', $data);
	}

	public function vertical_payout_save()
	{
		$back = 'manager/hr/vertical_payouts';
		$this->_post_only($back);
		$this->_need($this->_can_pay_verticals(), 'You do not have Full access to Vertical Payouts.', $back);
		$this->ci_admin_model->vertical_payout_table();
		$vid = (int)$this->input->post('vertical_id');
		$lid = (int)$this->input->post('lead_id');
		$amount = round((float)str_replace(',', '', (string)$this->input->post('amount')), 2);
		$date = (string)$this->input->post('pay_date');
		$v = $vid ? $this->db->select('id, company_name')->where('id', $vid)->get('ci_verticals')->row_array() : array();
		$back_v = $back . ($vid ? '?vertical=' . $vid : '');
		$this->_need($v && $amount > 0 && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) && strtotime($date), 'Choose the vertical, the date and an amount above zero.', $back_v);
		$this->_need($date <= date('Y-m-d'), 'The payment date cannot be in the future.', $back_v);
		if ($lid) {
			$l = $this->db->select('id')->where('id', $lid)->where('vertical_id', $vid)->get('ci_leads')->row_array();
			$this->_need(!empty($l), 'That client is not linked to this vertical.', $back_v);
		}
		$methods = array('Bank transfer', 'Cash', 'Cheque', 'Other');
		$method = in_array($this->input->post('method'), $methods, true) ? $this->input->post('method') : 'Bank transfer';
		$this->db->insert('ci_vertical_payouts', array('vertical_id' => $vid, 'lead_id' => $lid ? $lid : null, 'pay_date' => $date, 'amount' => $amount, 'method' => $method,
			'reference' => mb_substr(trim((string)$this->input->post('reference')), 0, 100), 'note' => mb_substr(trim((string)$this->input->post('note')), 0, 255),
			'recorded_by' => $this->cur_uid, 'created_at' => date('Y-m-d H:i:s')));
		$this->ci_admin_model->log_admin_action('vertical_payout_add', 'Payout of ' . number_format($amount, 2) . ' to vertical ' . $v['company_name'] . ' (#' . $vid . ') on ' . $date, 'SUCCESS');
		$this->session->set_flashdata('message', 'Payout of ' . number_format($amount, 2) . ' to ' . $v['company_name'] . ' recorded.');
		redirect($back_v);
	}

	public function vertical_payout_delete()
	{
		$back = 'manager/hr/vertical_payouts';
		$this->_post_only($back);
		$this->_need($this->_can_pay_verticals(), 'You do not have Full access to Vertical Payouts.', $back);
		$this->ci_admin_model->vertical_payout_table();
		$p = $this->db->where('id', (int)$this->input->post('id'))->get('ci_vertical_payouts')->row_array();
		$this->_need(!empty($p), 'Payout not found.', $back);
		$this->db->where('id', $p['id'])->delete('ci_vertical_payouts');
		$this->ci_admin_model->log_admin_action('vertical_payout_delete', 'Removed payout #' . $p['id'] . ' of ' . number_format((float)$p['amount'], 2) . ' to vertical #' . $p['vertical_id'] . ' dated ' . $p['pay_date'], 'SUCCESS');
		$this->session->set_flashdata('message', 'Payout removed.');
		redirect($back . '?vertical=' . (int)$p['vertical_id']);
	}

	/* ---------- Gratuity and final settlement (UAE) ---------- */

	/**
	 * End-of-service gratuity (UAE Labour Law): nothing under one year; 21 days of basic pay for each of the first 5 years,
	 * 30 days for each year after; parts of a year count pro rata; unpaid leave is not service; at most 2 years of pay.
	 */
	private function _gratuity($basic, $doj, $last_day, $unpaid_days, $set)
	{
		$out = array('days' => 0, 'years' => 0.0, 'amount' => 0.0, 'daily' => round((float)$basic / 30, 4));
		if (!$doj || !strtotime($doj) || (float)$basic <= 0) return $out;
		$days = (int)round((strtotime($last_day) - strtotime(date('Y-m-d', strtotime($doj)))) / 86400) + 1 - (int)round($unpaid_days);
		if ($days <= 0) return $out;
		$years = $days / 365;
		$out['days'] = $days;
		$out['years'] = $years;
		if ($years < 1) return $out;
		$d1 = (float)$set['gratuity_days_first5'];
		$d2 = (float)$set['gratuity_days_after5'];
		$gdays = min($years, 5) * $d1 + max(0, $years - 5) * $d2;
		$out['amount'] = round(min($gdays * $basic / 30, 24 * $basic), 2);
		return $out;
	}

	/** Unpaid leave days taken (approved) between two dates. */
	private function _unpaid_days($uid, $from, $to)
	{
		$names = array();
		foreach ($this->_leave_types(true) as $n => $t) if ($t['pay'] === 'unpaid') $names[] = $n;
		if (!$names) return 0.0;
		$r = $this->db->select('COALESCE(SUM(total_days),0) AS d', false)->where('user_id', (int)$uid)->where('status', 'Approved')->where_in('leave_type', $names)
			->where('from_date >=', $from)->where('to_date <=', $to)->get('ci_leaves')->row_array();
		return (float)$r['d'];
	}

	/** Gratuity owed today to everyone (the company's end-of-service liability), and the settlements. */
	public function taxation()
	{
		$this->_pay_tables();
		$set = $this->_hr_settings();
		$people = $this->_people();
		$today = date('Y-m-d');
		$sal = $this->_salaries_on($people, $today);
		$rows = array();
		$total = 0.0;
		foreach ($people as $p) {
			$uid = (int)$p['id'];
			$basic = isset($sal[$uid]) ? (float)$sal[$uid]['basic'] : 0.0;
			$doj = !empty($p['c_doj']) && strtotime($p['c_doj']) ? date('Y-m-d', strtotime($p['c_doj'])) : '';
			$g = $this->_gratuity($basic, $doj, $today, $doj ? $this->_unpaid_days($uid, $doj, $today) : 0, $set);
			$total += $g['amount'];
			$rows[] = array('id' => $uid, 'name' => $p['name'], 'emp_id' => $p['emp_id'], 'department' => $p['department'], 'doj' => $doj, 'basic' => $basic, 'source' => isset($sal[$uid]) ? $sal[$uid]['source'] : '', 'g' => $g);
		}
		$data = array();
		$data['info'] = array('title' => 'Gratuity & Settlement - HR', 'page_heading' => 'Gratuity & Settlement');
		$data['rows'] = $rows;
		$data['total'] = round($total, 2);
		$data['set'] = $set;
		$data['settlements'] = $this->db->query('SELECT S.*, U.name, U.emp_id FROM ci_hr_settlements S LEFT JOIN ci_user U ON U.id = S.user_id ORDER BY S.id DESC LIMIT 200')->result_array();
		$data['all_people'] = $this->_people(true);
		$data['can_edit'] = $this->_can_edit_pay();
		$this->load->view('manager/hr/gratuity', $data);
	}

	/** Final settlement: new for a person (user id, ?last_day=), or an existing one (?id=). */
	public function settlement($uid = 0)
	{
		$this->_pay_tables();
		$set = $this->_hr_settings();
		$sid = (int)$this->input->get('id');
		$st = $sid ? $this->db->where('id', $sid)->get('ci_hr_settlements')->row_array() : array();
		if ($st) $uid = (int)$st['user_id'];
		$u = (int)$uid ? $this->db->select('id, name, emp_id, department, c_doj, gender, iban, bank')->where('id', (int)$uid)->get('ci_user')->row_array() : array();
		if (!$u) { $this->session->set_flashdata('error_message', 'Choose the employee.'); redirect('manager/hr/taxation'); }
		$uid = (int)$u['id'];
		$exit = $this->db->table_exists('ci_user_exit') ? $this->db->where('user_id', $uid)->get('ci_user_exit')->row_array() : array();
		$last = $st ? $st['last_day'] : $this->_ymd($this->input->get('last_day'));
		if ($last === '') $last = $exit ? $exit['left_on'] : date('Y-m-d');
		$doj = !empty($u['c_doj']) && strtotime($u['c_doj']) ? date('Y-m-d', strtotime($u['c_doj'])) : '';
		$sal = $this->_salaries_on(array($u), $last);
		$s = isset($sal[$uid]) ? $sal[$uid] : null;
		$basic = $s ? (float)$s['basic'] : 0.0;
		$gross = $s ? (float)$s['total'] : 0.0;
		$unpaid = $doj ? $this->_unpaid_days($uid, $doj, $last) : 0.0;
		$g = $this->_gratuity($basic, $doj, $last, $unpaid, $set);
		// suggestions for a new settlement
		$bal = $this->_leave_balances(array($u), (int)substr($last, 0, 4));
		$leave_left = 0.0;
		foreach ($this->_leave_types() as $n => $t) if ($t['accrual'] === 'uae_annual' && isset($bal[$uid][$n])) { $b = $bal[$uid][$n]; $leave_left = max(0, (float)$b['entitled'] + (float)$b['adjust'] - (float)$b['taken']); }
		$m = substr($last, 0, 7);
		$paid_month = (int)$this->db->query("SELECT COUNT(*) AS n FROM ci_hr_payslips S JOIN ci_hr_pay_runs R ON R.id = S.run_id WHERE S.user_id = ? AND R.month = ? AND R.status IN ('Approved','Paid')", array($uid, $m))->row()->n;
		$salary_days = $paid_month ? 0 : (int)date('j', strtotime($last));
		$loans = 0.0;
		foreach ($this->_loan_rows('WHERE L.user_id = ? AND L.status = \'Active\'', array($uid)) as $l) $loans += max(0, (float)$l['amount'] - (float)$l['repaid']);
		$data = array();
		$data['info'] = array('title' => 'Final Settlement - ' . $u['name'], 'page_heading' => 'Final Settlement');
		$data['u'] = $u;
		$data['st'] = $st;
		$data['exit'] = $exit;
		$data['last'] = $last;
		$data['doj'] = $doj;
		$data['basic'] = $basic;
		$data['gross'] = $gross;
		$data['has_salary'] = (bool)$s;
		$data['unpaid'] = $unpaid;
		$data['g'] = $g;
		$data['suggest'] = array('leave_days' => round($leave_left * 2) / 2, 'salary_days' => $salary_days, 'loans' => round($loans, 2), 'paid_month' => $paid_month);
		$data['set'] = $set;
		$data['reasons'] = self::settlement_reasons();
		$data['can_edit'] = $this->_can_edit_pay() && (!$st || $st['status'] === 'Draft');
		$data['can_approve'] = $this->_can_approve_pay();
		if ($st && !empty($st['approved_by'])) { $w = $this->db->select('name')->where('id', (int)$st['approved_by'])->get('ci_user')->row_array(); $data['approved_name'] = $w ? $w['name'] : ''; }
		$this->load->view('manager/hr/settlement', $data);
	}

	public function settlement_save()
	{
		$this->_pay_tables();
		$this->_post_only('manager/hr/taxation');
		$sid = (int)$this->input->post('id');
		$uid = (int)$this->input->post('user_id');
		$st = $sid ? $this->db->where('id', $sid)->get('ci_hr_settlements')->row_array() : array();
		if ($st) $uid = (int)$st['user_id'];
		$back = 'manager/hr/settlement/' . $uid . ($sid ? '?id=' . $sid : '');
		$this->_need(!$st || $st['status'] === 'Draft', 'This settlement is ' . strtolower($st ? $st['status'] : '') . ' and cannot be changed.', $back);
		$u = $uid ? $this->db->select('id, name, c_doj')->where('id', $uid)->get('ci_user')->row_array() : array();
		$last = $this->_ymd($this->input->post('last_day'));
		$this->_need($u && $last !== '', 'Choose the last working day.', $back);
		$set = $this->_hr_settings();
		$sal = $this->_salaries_on(array($u), $last);
		$s = isset($sal[$uid]) ? $sal[$uid] : null;
		$this->_need((bool)$s, 'This person has no salary in the HR file, so nothing can be worked out.', $back);
		$basic = (float)$s['basic'];
		$gross = (float)$s['total'];
		$doj = !empty($u['c_doj']) && strtotime($u['c_doj']) ? date('Y-m-d', strtotime($u['c_doj'])) : '';
		$unpaid = $doj ? $this->_unpaid_days($uid, $doj, $last) : 0.0;
		$g = $this->_gratuity($basic, $doj, $last, $unpaid, $set);
		$num = function ($k) { return round((float)str_replace(',', '', (string)$this->input->post($k)), 2); };
		$leave_days = max(0, round($num('leave_days') * 2) / 2);
		$salary_days = max(0, min(31, round($num('salary_days') * 2) / 2));
		$gratuity = $this->input->post('gratuity_override') !== null && $this->input->post('gratuity_override') !== '' ? max(0, $num('gratuity_override')) : $g['amount'];
		$leave_pay = round($leave_days * $basic / 30, 2);
		$salary_pay = round($salary_days * $gross / 30, 2);
		$notice = $num('notice_pay');
		$loans = max(0, $num('loans'));
		$lines = array();
		$ln = (array)$this->input->post('line_label');
		$la = (array)$this->input->post('line_amount');
		$extra = 0.0;
		foreach ($ln as $i => $l) {
			$l = mb_substr(trim((string)$l), 0, 100);
			$a = isset($la[$i]) ? round((float)str_replace(',', '', (string)$la[$i]), 2) : 0;
			if ($l !== '' && $a != 0) { $lines[] = array($l, $a); $extra += $a; }
		}
		$total = round($gratuity + $leave_pay + $salary_pay + $notice - $loans + $extra, 2);
		$reasons = self::settlement_reasons();
		$row = array('user_id' => $uid, 'last_day' => $last, 'reason' => in_array($this->input->post('reason'), $reasons, true) ? $this->input->post('reason') : 'Other',
			'basic' => $basic, 'gross' => $gross, 'service_days' => $g['days'], 'unpaid_days' => $unpaid, 'gratuity' => $gratuity,
			'leave_days' => $leave_days, 'leave_pay' => $leave_pay, 'salary_days' => $salary_days, 'salary_pay' => $salary_pay,
			'notice_pay' => $notice, 'loans' => $loans, 'extra_lines' => $lines ? json_encode($lines) : null, 'total' => $total,
			'note' => mb_substr(trim((string)$this->input->post('note')), 0, 500));
		if ($st) $this->db->where('id', $sid)->update('ci_hr_settlements', $row);
		else { $this->db->insert('ci_hr_settlements', $row + array('status' => 'Draft', 'created_by' => $this->cur_uid, 'created_at' => date('Y-m-d H:i:s'))); $sid = (int)$this->db->insert_id(); }
		$this->ci_admin_model->log_admin_action('hr_settlement_save', 'Final settlement #' . $sid . ' for ' . $u['name'] . ': ' . number_format($total, 2), 'SUCCESS');
		$this->session->set_flashdata('message', 'Final settlement saved as a draft: ' . $set['currency'] . ' ' . number_format($total, 2) . '.');
		redirect('manager/hr/settlement/' . $uid . '?id=' . $sid);
	}

	/** approve (Approve Payroll right) / paid / draft (reopen) / delete (draft only). */
	public function settlement_step($id = 0, $step = '')
	{
		$this->_pay_tables();
		$st = $this->db->where('id', (int)$id)->get('ci_hr_settlements')->row_array();
		if (!$st) redirect('manager/hr/taxation');
		$back = 'manager/hr/settlement/' . $st['user_id'] . '?id=' . $st['id'];
		$this->_post_only($back);
		$now = date('Y-m-d H:i:s');
		if ($step === 'approve') {
			$this->_need($this->_can_approve_pay() && $st['status'] === 'Draft', 'You cannot approve this settlement.', $back);
			$this->db->where('id', $st['id'])->update('ci_hr_settlements', array('status' => 'Approved', 'approved_by' => $this->cur_uid, 'approved_at' => $now));
			$msg = 'Final settlement approved.';
		} elseif ($step === 'draft') {
			$this->_need($this->_can_approve_pay() && $st['status'] === 'Approved', 'Only an approved (not paid) settlement can be reopened.', $back);
			$this->db->where('id', $st['id'])->update('ci_hr_settlements', array('status' => 'Draft', 'approved_by' => null, 'approved_at' => null));
			$msg = 'Settlement reopened as a draft.';
		} elseif ($step === 'paid') {
			$this->_need($this->_can_edit_pay() && $st['status'] === 'Approved', 'Approve the settlement first.', $back);
			$this->db->where('id', $st['id'])->update('ci_hr_settlements', array('status' => 'Paid', 'paid_at' => $now));
			// what is left of loans is recovered in the settlement
			$left = (float)$st['loans'];
			foreach ($this->_loan_rows('WHERE L.user_id = ? AND L.status = \'Active\'', array((int)$st['user_id'])) as $l) {
				$a = round(min($left, max(0, (float)$l['amount'] - (float)$l['repaid'])), 2);
				if ($a > 0) { $this->db->insert('ci_hr_loan_pay', array('loan_id' => $l['id'], 'user_id' => $st['user_id'], 'settlement_id' => $st['id'], 'month' => substr($st['last_day'], 0, 7), 'amount' => $a)); $left -= $a; }
				$this->db->where('id', $l['id'])->update('ci_hr_loans', array('status' => $a >= (float)$l['amount'] - (float)$l['repaid'] - 0.001 ? 'Repaid' : 'Closed', 'closed_at' => $now, 'close_note' => 'Final settlement #' . $st['id']));
			}
			$msg = 'Final settlement marked as paid.';
		} elseif ($step === 'delete') {
			$this->_need($this->_can_edit_pay() && $st['status'] === 'Draft', 'Only a draft can be deleted.', $back);
			$this->db->where('id', $st['id'])->delete('ci_hr_settlements');
			$this->session->set_flashdata('message', 'Draft settlement deleted.');
			redirect('manager/hr/taxation');
		} else {
			redirect($back);
		}
		$this->ci_admin_model->log_admin_action('hr_settlement_' . $step, $msg . ' #' . $st['id'] . ' (user ID #' . $st['user_id'] . ')', 'SUCCESS');
		$this->session->set_flashdata('message', $msg);
		redirect($back);
	}

	/* ======================= Self-service, reviews, letters, checklists (HR part 4) ======================= */

	public static function review_criteria()
	{
		return array('Quality of work', 'Productivity', 'Job knowledge', 'Teamwork', 'Communication', 'Initiative', 'Attendance and punctuality');
	}

	public static function letter_types()
	{
		return array('salary' => 'Salary certificate', 'employment' => 'Employment letter', 'noc' => 'No objection certificate (NOC)', 'experience' => 'Experience letter');
	}

	public static function checklist_owners()
	{
		return array('HR', 'Manager', 'IT', 'Finance', 'Employee');
	}

	/** Default steps: title, who does it, days from the joining date / last working day. */
	public static function checklist_defaults()
	{
		return array(
			'onboarding' => array(
				array('Offer letter signed', 'HR', -7), array('Contract signed and registered with MOHRE', 'HR', 0), array('Entry permit or work permit applied', 'HR', 0),
				array('Medical test and Emirates ID biometrics', 'Employee', 14), array('Health insurance added', 'HR', 7), array('Bank account and IBAN collected for WPS', 'Finance', 7),
				array('Laptop, email and phone ready', 'IT', 0), array('CRM login created with the right role', 'IT', 0), array('Welcome and team introduction', 'Manager', 0),
				array('Company policies read and accepted', 'Employee', 3), array('Probation goals agreed', 'Manager', 7), array('Probation review', 'Manager', 170),
			),
			'offboarding' => array(
				array('Resignation or termination letter on file', 'HR', -30), array('Last working day and notice agreed', 'HR', -30), array('Work handed over', 'Manager', -3),
				array('Exit interview', 'HR', -2), array('Laptop, phone, access card and keys returned', 'IT', 0), array('CRM, email and system access removed', 'IT', 0),
				array('Health insurance cancelled', 'HR', 7), array('Work permit cancelled with MOHRE', 'HR', 14), array('Visa cancelled', 'HR', 14),
				array('Final settlement paid', 'Finance', 14), array('Experience letter issued', 'HR', 14),
			),
		);
	}

	private function _ss_tables()
	{
		static $done = false;
		if ($done) return;
		$done = true;
		$this->_pay_tables();
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_hr_review_cycles (
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL,
			period_from DATE NULL, period_to DATE NULL, due_date DATE NULL, status VARCHAR(10) NOT NULL DEFAULT 'Open',
			created_by INT NULL, created_at DATETIME NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$cols = array();
		foreach ($this->db->query('SHOW COLUMNS FROM ci_hr_appraisals')->result_array() as $c) $cols[$c['Field']] = 1;
		if (!isset($cols['cycle_id'])) {
			$this->db->query("ALTER TABLE ci_hr_appraisals ADD cycle_id INT NULL, ADD stage VARCHAR(20) NULL, ADD self_text TEXT NULL, ADD self_rating DECIMAL(3,1) NULL,
				ADD scores TEXT NULL, ADD goals TEXT NULL, ADD self_at DATETIME NULL, ADD manager_at DATETIME NULL, ADD ack_at DATETIME NULL, ADD ack_note VARCHAR(500) NULL, ADD KEY cycle_id (cycle_id)");
		}
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_hr_letters (
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, type VARCHAR(20) NOT NULL,
			addressed_to VARCHAR(150) NOT NULL DEFAULT '', purpose VARCHAR(255) NOT NULL DEFAULT '',
			status VARCHAR(12) NOT NULL DEFAULT 'Requested', ref_no VARCHAR(30) NOT NULL DEFAULT '', body TEXT NULL, note VARCHAR(255) NOT NULL DEFAULT '',
			requested_by INT NULL, created_at DATETIME NULL, issued_by INT NULL, issued_at DATETIME NULL,
			KEY user_id (user_id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_hr_checklists (
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, kind VARCHAR(12) NOT NULL, start_date DATE NOT NULL,
			status VARCHAR(8) NOT NULL DEFAULT 'Open', created_by INT NULL, created_at DATETIME NULL, done_at DATETIME NULL, KEY user_id (user_id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_hr_checklist_items (
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, checklist_id INT NOT NULL, title VARCHAR(150) NOT NULL, owner VARCHAR(12) NOT NULL DEFAULT 'HR',
			due_date DATE NULL, done_by INT NULL, done_at DATETIME NULL, sort INT NOT NULL DEFAULT 0, KEY checklist_id (checklist_id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_hr_checklist_templates (
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, kind VARCHAR(12) NOT NULL, title VARCHAR(150) NOT NULL, owner VARCHAR(12) NOT NULL DEFAULT 'HR',
			days_offset INT NOT NULL DEFAULT 0, sort INT NOT NULL DEFAULT 0
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		if (!$this->db->count_all_results('ci_hr_checklist_templates')) {
			foreach (self::checklist_defaults() as $kind => $rows) foreach ($rows as $i => $r)
				$this->db->insert('ci_hr_checklist_templates', array('kind' => $kind, 'title' => $r[0], 'owner' => $r[1], 'days_offset' => $r[2], 'sort' => $i + 1));
		}
		$this->db->data_cache = array();
	}

	private function _name($uid)
	{
		$r = (int)$uid ? $this->db->select('name')->where('id', (int)$uid)->get('ci_user')->row_array() : array();
		return $r ? $r['name'] : '';
	}

	/* ---------- My HR (every signed-in person, own data only) ---------- */

	public function me()
	{
		$this->_ss_tables();
		$uid = $this->cur_uid;
		$u = $this->db->select('U.*, M.name AS manager_name')->from('ci_user U')->join('ci_user M', 'M.id = U.manager', 'left')->where('U.id', $uid)->get()->row_array();
		$set = $this->_hr_settings();
		$data = array();
		$data['info'] = array('title' => 'My HR', 'page_heading' => 'My HR');
		$data['u'] = $u;
		$data['set'] = $set;
		$des = $this->db->where('user_id', $uid)->get('ci_user_designation')->row_array();
		$data['designation'] = $des ? $des['designation'] : '';
		$data['emp'] = $this->db->where('user_id', $uid)->get('ci_hr_employee')->row_array();
		$data['life'] = $this->db->table_exists('ci_hr_lifecycle') ? $this->db->where('user_id', $uid)->get('ci_hr_lifecycle')->row_array() : array();
		// identity documents and their expiry
		$alert = (int)$set['doc_alert_days'];
		$ids = array();
		foreach (array(array('Passport', 'p_no', 'p_exdate'), array('Residence visa', 'visa_no', 'visa_expiry'), array('Emirates ID', 'eid_no', 'eid_expiry')) as $d) {
			$left = $this->_days_left($u[$d[2]]);
			$ids[] = array('label' => $d[0], 'no' => $u[$d[1]], 'expiry' => $u[$d[2]], 'left' => $left, 'state' => $this->_doc_state($left, $alert));
		}
		if (!empty($data['emp']['labour_card_expiry'])) { $left = $this->_days_left($data['emp']['labour_card_expiry']); $ids[] = array('label' => 'Labour card', 'no' => $data['emp']['labour_card_no'], 'expiry' => $data['emp']['labour_card_expiry'], 'left' => $left, 'state' => $this->_doc_state($left, $alert)); }
		if (!empty($data['emp']['insurance_expiry'])) { $left = $this->_days_left($data['emp']['insurance_expiry']); $ids[] = array('label' => 'Health insurance', 'no' => $data['emp']['insurance_no'], 'expiry' => $data['emp']['insurance_expiry'], 'left' => $left, 'state' => $this->_doc_state($left, $alert)); }
		$data['ids'] = $ids;
		$data['files'] = $this->db->table_exists('ci_user_docs') ? $this->db->where('user_id', $uid)->order_by('uploaded_at', 'DESC')->get('ci_user_docs')->result_array() : array();
		// leave
		$bal = $this->_leave_balances(array($u), (int)date('Y'));
		$data['bal'] = isset($bal[$uid]) ? $bal[$uid] : array();
		$data['leaves'] = $this->db->where('user_id', $uid)->order_by('from_date', 'DESC')->limit(5)->get('ci_leaves')->result_array();
		// attendance this month
		$from = date('Y-m-01');
		$att = array('Present' => 0, 'Late' => 0, 'Absent' => 0, 'Half Day' => 0, 'Work From Home' => 0, 'On Leave' => 0);
		foreach ($this->db->select('status, COUNT(*) AS n', false)->where('user_id', $uid)->where('attendance_date >=', $from)->group_by('status')->get('ci_attendance')->result_array() as $r) $att[$r['status']] = (int)$r['n'];
		$data['att'] = $att;
		$data['today'] = $this->db->where('user_id', $uid)->where('attendance_date', date('Y-m-d'))->get('ci_attendance')->row_array();
		// pay
		$data['slips'] = $this->db->query("SELECT S.id, S.net, S.gross, R.month, R.status FROM ci_hr_payslips S JOIN ci_hr_pay_runs R ON R.id = S.run_id WHERE S.user_id = ? AND R.status IN ('Approved','Paid') ORDER BY R.month DESC LIMIT 24", array($uid))->result_array();
		$data['loans'] = $this->_loan_rows("WHERE L.user_id = ? AND L.status = 'Active'", array($uid));
		// letters, reviews, checklist
		$data['letters'] = $this->db->where('user_id', $uid)->order_by('id', 'DESC')->limit(20)->get('ci_hr_letters')->result_array();
		$data['letter_types'] = self::letter_types();
		$data['reviews'] = $this->db->query("SELECT A.*, C.name AS cname, C.due_date, R.name AS reviewer_name FROM ci_hr_appraisals A LEFT JOIN ci_hr_review_cycles C ON C.id = A.cycle_id LEFT JOIN ci_user R ON R.id = A.reviewer_id WHERE A.user_id = ? ORDER BY A.id DESC", array($uid))->result_array();
		$data['team_reviews'] = $this->db->query("SELECT A.*, U.name, C.due_date FROM ci_hr_appraisals A JOIN ci_user U ON U.id = A.user_id LEFT JOIN ci_hr_review_cycles C ON C.id = A.cycle_id WHERE A.reviewer_id = ? AND A.user_id <> ? AND A.stage IN ('Self review','Manager review') ORDER BY C.due_date", array($uid, $uid))->result_array();
		$cl = $this->db->where('user_id', $uid)->where('status', 'Open')->order_by('id', 'DESC')->get('ci_hr_checklists')->row_array();
		$data['checklist'] = $cl;
		$data['cl_items'] = $cl ? $this->db->where('checklist_id', $cl['id'])->order_by('sort, id')->get('ci_hr_checklist_items')->result_array() : array();
		$this->load->view('manager/hr/me', $data);
	}

	public function my_pay()
	{
		redirect('manager/hr/me#pay');
	}

	/** Own file from the HR file (passport copy, contract...). */
	public function my_doc($id = 0)
	{
		$d = $this->db->where('id', (int)$id)->where('user_id', $this->cur_uid)->get('ci_user_docs')->row_array();
		if (!$d) show_404();
		$path = APPPATH . 'uploads/staff_docs/' . (int)$d['user_id'] . '/' . basename($d['file_name']);
		if (!is_file($path)) show_404();
		while (ob_get_level()) ob_end_clean();
		header('Content-Type: ' . $d['mime']);
		header('Content-Length: ' . filesize($path));
		header('Content-Disposition: ' . ($this->input->get('dl') ? 'attachment' : 'inline') . '; filename="' . preg_replace('/[^\w.\-]+/', '_', $d['doc_type']) . '.' . pathinfo($d['file_name'], PATHINFO_EXTENSION) . '"');
		header('X-Content-Type-Options: nosniff');
		header('Cache-Control: private, no-store');
		readfile($path);
		exit;
	}

	/* ---------- Letters ---------- */

	/** Employee asks for a letter (POST type, addressed_to, purpose). */
	public function letter_request()
	{
		$this->_ss_tables();
		$this->_post_only('manager/hr/me');
		$types = self::letter_types();
		$type = (string)$this->input->post('type');
		$this->_need(isset($types[$type]), 'Choose the letter you need.', 'manager/hr/me#letters');
		$open = $this->db->where('user_id', $this->cur_uid)->where('type', $type)->where('status', 'Requested')->count_all_results('ci_hr_letters');
		$this->_need(!$open, 'You already asked for a ' . strtolower($types[$type]) . '; HR will issue it soon.', 'manager/hr/me#letters');
		$this->db->insert('ci_hr_letters', array('user_id' => $this->cur_uid, 'type' => $type, 'addressed_to' => mb_substr(trim((string)$this->input->post('addressed_to')), 0, 150),
			'purpose' => mb_substr(trim((string)$this->input->post('purpose')), 0, 255), 'status' => 'Requested', 'requested_by' => $this->cur_uid, 'created_at' => date('Y-m-d H:i:s')));
		$id = (int)$this->db->insert_id();
		$me = $this->_name($this->cur_uid);
		$this->ci_admin_model->notify_role(array(1, 7), 'letter_request', 'Letter requested', $me . ' asked for a ' . strtolower($types[$type]) . '.', base_url('manager/hr/letter/' . $id), 'fa fa-envelope-o', '#0072f0');
		$this->session->set_flashdata('message', 'Request sent to HR. You will be told when the letter is ready.');
		redirect('manager/hr/me#letters');
	}

	public function letters()
	{
		$this->_ss_tables();
		$data = array();
		$data['info'] = array('title' => 'Letters - HR', 'page_heading' => 'Letters');
		$data['rows'] = $this->db->query('SELECT L.*, U.name, U.emp_id, I.name AS issued_name FROM ci_hr_letters L LEFT JOIN ci_user U ON U.id = L.user_id LEFT JOIN ci_user I ON I.id = L.issued_by ORDER BY FIELD(L.status, \'Requested\') DESC, L.id DESC LIMIT 500')->result_array();
		$data['types'] = self::letter_types();
		$data['people'] = $this->_people(true);
		$data['can_edit'] = $this->is_admin || crm_can('hr_letters', 'write');
		$this->load->view('manager/hr/letters', $data);
	}

	/** HR starts a letter for someone, then edits and issues it. */
	public function letter_new()
	{
		$this->_ss_tables();
		$this->_post_only('manager/hr/letters');
		$types = self::letter_types();
		$uid = (int)$this->input->post('user_id');
		$type = (string)$this->input->post('type');
		$this->_need($uid > 0 && isset($types[$type]) && $this->_name($uid) !== '', 'Choose the employee and the letter.', 'manager/hr/letters');
		$this->db->insert('ci_hr_letters', array('user_id' => $uid, 'type' => $type, 'addressed_to' => mb_substr(trim((string)$this->input->post('addressed_to')), 0, 150),
			'purpose' => mb_substr(trim((string)$this->input->post('purpose')), 0, 255), 'status' => 'Requested', 'requested_by' => $this->cur_uid, 'created_at' => date('Y-m-d H:i:s')));
		redirect('manager/hr/letter/' . (int)$this->db->insert_id());
	}

	/** The letter text made from the person's HR file. */
	private function _letter_text($l)
	{
		$set = $this->_hr_settings();
		$uid = (int)$l['user_id'];
		$u = $this->db->where('id', $uid)->get('ci_user')->row_array();
		$des = $this->db->where('user_id', $uid)->get('ci_user_designation')->row_array();
		$life = $this->db->table_exists('ci_hr_lifecycle') ? $this->db->where('user_id', $uid)->get('ci_hr_lifecycle')->row_array() : array();
		$exit = $this->db->table_exists('ci_user_exit') ? $this->db->where('user_id', $uid)->get('ci_user_exit')->row_array() : array();
		$company = $set['company_name'] !== '' ? $set['company_name'] : 'the company';
		$name = $u['name'];
		$role = $des && $des['designation'] !== '' ? $des['designation'] : ($u['department'] ? 'a member of the ' . $u['department'] . ' team' : 'an employee');
		$doj = !empty($u['c_doj']) && strtotime($u['c_doj']) ? date('d F Y', strtotime($u['c_doj'])) : '[joining date]';
		$pp = trim((string)$u['p_no']) !== '' ? ', ' . (trim((string)$u['national']) !== '' ? trim($u['national']) . ' national, ' : '') . 'passport number ' . trim($u['p_no']) : '';
		$purpose = trim((string)$l['purpose']);
		$for = $purpose !== '' ? ' for the purpose of ' . rtrim($purpose, '.') : '';
		$t = '';
		switch ($l['type']) {
			case 'salary':
				$sal = $this->_salaries_on(array($u), date('Y-m-d'));
				$s = isset($sal[$uid]) ? $sal[$uid] : null;
				$t = "This is to certify that {$name}{$pp}, has been working with {$company} as {$role} since {$doj}.\n\n";
				if ($s) {
					$t .= 'The current monthly salary is ' . $set['currency'] . ' ' . number_format((float)$s['total'], 2) . ", made up as follows:\n";
					foreach (self::_salary_lines($s) as $line) $t .= '  ' . $line[0] . ': ' . $set['currency'] . ' ' . number_format($line[1], 2) . "\n";
					$t .= "\n";
				} else {
					$t .= "The current monthly salary is " . $set['currency'] . " [amount].\n\n";
				}
				$t .= "This certificate is issued at the employee's request{$for}, without any liability on the part of the company.";
				break;
			case 'employment':
				$ct = $life && !empty($life['contract_type']) ? ' on a ' . strtolower($life['contract_type']) . ' contract' : '';
				$t = "This is to confirm that {$name}{$pp}, is employed with {$company} as {$role}{$ct} since {$doj}, and is still in our employment.\n\nThis letter is issued at the employee's request{$for}, without any liability on the part of the company.";
				break;
			case 'noc':
				$t = "{$company} has no objection to {$name}{$pp}, working with us as {$role} since {$doj}, " . ($purpose !== '' ? rtrim($purpose, '.') : '[what the employee is allowed to do, e.g. travelling to ... from ... to ...]') . ".\n\nThe employee remains in our employment and will return to duty as agreed. This certificate is issued at the employee's request, without any liability on the part of the company.";
				break;
			case 'experience':
				$to = $exit ? date('d F Y', strtotime($exit['left_on'])) : 'date';
				$t = "This is to certify that {$name}{$pp}, worked with {$company} as {$role} from {$doj} " . ($exit ? "to {$to}" : 'to date') . ".\n\nDuring this time {$name} carried out their duties with dedication and professionalism. We thank them for their work and wish them every success in the future.";
				break;
		}
		return $t;
	}

	/** One letter: HR edits and issues it; the employee sees it once issued (print / save as PDF). */
	public function letter($id = 0)
	{
		$this->_ss_tables();
		$l = $this->db->where('id', (int)$id)->get('ci_hr_letters')->row_array();
		$hr = $this->is_admin || crm_can('hr_letters', 'read');
		$own = $l && (int)$l['user_id'] === $this->cur_uid;
		if (!$l || (!$hr && !($own && $l['status'] === 'Issued'))) {
			$this->session->set_flashdata('error_message', 'Letter not found.');
			redirect($hr ? 'manager/hr/letters' : 'manager/hr/me#letters');
		}
		$data = array('l' => $l, 'set' => $this->_hr_settings(), 'types' => self::letter_types(), 'u' => $this->db->select('id, name, emp_id, department')->where('id', (int)$l['user_id'])->get('ci_user')->row_array(),
			'can_edit' => ($this->is_admin || crm_can('hr_letters', 'write')) && $l['status'] === 'Requested', 'back' => $hr && !$own ? 'manager/hr/letters' : 'manager/hr/me#letters');
		$data['body'] = $l['status'] === 'Issued' ? $l['body'] : ($l['body'] ? $l['body'] : $this->_letter_text($l));
		$data['issued_name'] = $this->_name($l['issued_by']);
		$data['info'] = array('title' => $data['types'][$l['type']] . ' - ' . $data['u']['name'], 'page_heading' => 'Letter');
		$this->load->view('manager/hr/letter', $data);
	}

	/** issue (with the edited text) or reject (with a reason). */
	public function letter_save($id = 0)
	{
		$this->_ss_tables();
		$this->_post_only('manager/hr/letters');
		$l = $this->db->where('id', (int)$id)->get('ci_hr_letters')->row_array();
		$back = 'manager/hr/letter/' . (int)$id;
		$this->_need($l && $l['status'] === 'Requested', 'This letter was already ' . strtolower($l ? $l['status'] : 'removed') . '.', 'manager/hr/letters');
		$types = self::letter_types();
		$act = (string)$this->input->post('act');
		$note = mb_substr(trim((string)$this->input->post('note')), 0, 255);
		if ($act === 'reject') {
			$this->_need($note !== '', 'Give a reason so the employee knows why.', $back);
			$this->db->where('id', $l['id'])->update('ci_hr_letters', array('status' => 'Rejected', 'note' => $note, 'issued_by' => $this->cur_uid, 'issued_at' => date('Y-m-d H:i:s')));
			$this->ci_admin_model->add_notification((int)$l['user_id'], 'letter', 'Letter request declined', 'Your request for a ' . strtolower($types[$l['type']]) . ' was declined: ' . $note, base_url('manager/hr/me#letters'), 'fa fa-times-circle', '#dc2626');
			$this->session->set_flashdata('message', 'Request declined.');
			redirect('manager/hr/letters');
		}
		$body = trim((string)$this->input->post('body'));
		$this->_need($body !== '', 'The letter is empty.', $back);
		$ref = 'HR/' . date('Y') . '/' . str_pad((string)$l['id'], 4, '0', STR_PAD_LEFT);
		$this->db->where('id', $l['id'])->update('ci_hr_letters', array('status' => 'Issued', 'body' => mb_substr($body, 0, 20000), 'addressed_to' => mb_substr(trim((string)$this->input->post('addressed_to')), 0, 150),
			'ref_no' => $ref, 'issued_by' => $this->cur_uid, 'issued_at' => date('Y-m-d H:i:s')));
		$this->ci_admin_model->add_notification((int)$l['user_id'], 'letter', 'Letter ready', 'Your ' . strtolower($types[$l['type']]) . ' is ready to download.', base_url('manager/hr/letter/' . $l['id']), 'fa fa-envelope-o', '#16a34a');
		$this->ci_admin_model->log_admin_action('hr_letter_issue', 'Issued ' . $types[$l['type']] . ' ' . $ref . ' for user ID #' . $l['user_id'], 'SUCCESS');
		$this->session->set_flashdata('message', 'Letter issued (' . $ref . '). The employee can download it from My HR.');
		redirect($back);
	}

	/* ---------- Performance reviews ---------- */

	/** Review cycles and every review (HR). */
	public function appraisals()
	{
		$this->_ss_tables();
		$data = array();
		$data['info'] = array('title' => 'Performance Reviews - HR', 'page_heading' => 'Performance Reviews');
		$data['cycles'] = $this->db->query("SELECT C.*, COUNT(A.id) AS n, SUM(A.stage = 'Completed') AS done FROM ci_hr_review_cycles C LEFT JOIN ci_hr_appraisals A ON A.cycle_id = C.id GROUP BY C.id ORDER BY C.id DESC")->result_array();
		$data['rows'] = $this->db->query('SELECT A.*, U.name, U.department, R.name AS reviewer_name, C.name AS cname, C.due_date FROM ci_hr_appraisals A LEFT JOIN ci_user U ON U.id = A.user_id LEFT JOIN ci_user R ON R.id = A.reviewer_id LEFT JOIN ci_hr_review_cycles C ON C.id = A.cycle_id ORDER BY A.id DESC LIMIT 1000')->result_array();
		$data['people'] = $this->_people();
		$data['depts'] = array_values(array_unique(array_filter(array_map(function ($u) { return trim((string)$u['department']); }, $data['people']))));
		sort($data['depts']);
		$data['can_edit'] = $this->is_admin || crm_can('hr_appraisals', 'write');
		$this->load->view('manager/hr/appraisals', $data);
	}

	/** Start a review cycle for the chosen people (POST name, period, due date, user_ids[] or department). */
	public function add_appraisal()
	{
		$this->_ss_tables();
		$this->_post_only('manager/hr/appraisals');
		$name = mb_substr(trim((string)($this->input->post('name') ?: $this->input->post('cycle_name'))), 0, 100);
		$from = $this->_ymd($this->input->post('period_from'));
		$to = $this->_ymd($this->input->post('period_to'));
		$due = $this->_ymd($this->input->post('due_date'));
		$ids = array_filter(array_map('intval', (array)$this->input->post('user_ids')));
		$dept = trim((string)$this->input->post('department'));
		$people = $this->_people();
		if ($this->input->post('who') === 'all' || ($this->input->post('who') === 'dept' && $dept !== '')) {
			$ids = array();
			foreach ($people as $p) if ($this->input->post('who') === 'all' || strcasecmp(trim((string)$p['department']), $dept) === 0) $ids[] = (int)$p['id'];
		}
		$this->_need($name !== '' && $ids, 'Give the review a name and choose who is reviewed.', 'manager/hr/appraisals');
		$this->db->insert('ci_hr_review_cycles', array('name' => $name, 'period_from' => $from ?: null, 'period_to' => $to ?: null, 'due_date' => $due ?: null, 'status' => 'Open', 'created_by' => $this->cur_uid, 'created_at' => date('Y-m-d H:i:s')));
		$cid = (int)$this->db->insert_id();
		$mgr = array();
		foreach ($people as $p) $mgr[(int)$p['id']] = (int)$p['manager'];
		$n = 0;
		foreach (array_unique($ids) as $uid) {
			if (!isset($mgr[$uid])) continue;
			$rev = $mgr[$uid] && $mgr[$uid] !== $uid ? $mgr[$uid] : $this->cur_uid;
			$this->db->insert('ci_hr_appraisals', array('user_id' => $uid, 'reviewer_id' => $rev, 'cycle_name' => $name, 'cycle_id' => $cid, 'stage' => 'Self review', 'status' => 'Open', 'rating' => 0, 'kpi_score' => 0, 'created_at' => date('Y-m-d H:i:s')));
			$this->ci_admin_model->add_notification($uid, 'review', 'Performance review: ' . $name, 'Please fill in your self review' . ($due ? ' by ' . date('d M Y', strtotime($due)) : '') . '.', base_url('manager/hr/review/' . $this->db->insert_id()), 'fa fa-star-o', '#0072f0');
			$n++;
		}
		$this->ci_admin_model->log_admin_action('hr_review_cycle', 'Started review "' . $name . '" for ' . $n . ' people', 'SUCCESS');
		$this->session->set_flashdata('message', 'Review "' . $name . '" started for ' . $n . ' people. Each one is asked for a self review first, then their manager reviews.');
		redirect('manager/hr/appraisals');
	}

	/** Close a cycle: reviews not finished stay as they are but nobody is reminded any more. */
	public function review_cycle_close($id = 0)
	{
		$this->_ss_tables();
		$this->_post_only('manager/hr/appraisals');
		$this->db->where('id', (int)$id)->update('ci_hr_review_cycles', array('status' => 'Closed'));
		$this->session->set_flashdata('message', 'Review closed.');
		redirect('manager/hr/appraisals');
	}

	private function _review_access($a)
	{
		$hr_read = $this->is_admin || crm_can('hr_appraisals', 'read');
		$hr_write = $this->is_admin || crm_can('hr_appraisals', 'write');
		$own = (int)$a['user_id'] === $this->cur_uid;
		$reviewer = (int)$a['reviewer_id'] === $this->cur_uid && !$own;
		return array('see' => $own || $reviewer || $hr_read, 'own' => $own, 'reviewer' => $reviewer || ($hr_write && !$own), 'hr' => $hr_write);
	}

	public function review($id = 0)
	{
		$this->_ss_tables();
		$a = $this->db->query('SELECT A.*, U.name, U.department, U.c_doj, R.name AS reviewer_name, C.name AS cname, C.period_from, C.period_to, C.due_date FROM ci_hr_appraisals A LEFT JOIN ci_user U ON U.id = A.user_id LEFT JOIN ci_user R ON R.id = A.reviewer_id LEFT JOIN ci_hr_review_cycles C ON C.id = A.cycle_id WHERE A.id = ?', array((int)$id))->row_array();
		$acc = $a ? $this->_review_access($a) : array('see' => false);
		if (!$a || !$acc['see']) { $this->session->set_flashdata('error_message', 'Review not found.'); redirect('manager/hr/me#reviews'); }
		if (empty($a['stage'])) $a['stage'] = 'Completed';
		$data = array('a' => $a, 'acc' => $acc, 'criteria' => self::review_criteria(), 'scores' => (array)json_decode((string)$a['scores'], true));
		$des = $this->db->where('user_id', (int)$a['user_id'])->get('ci_user_designation')->row_array();
		$data['designation'] = $des ? $des['designation'] : '';
		$data['info'] = array('title' => 'Review - ' . $a['name'], 'page_heading' => 'Performance Review');
		$this->load->view('manager/hr/review', $data);
	}

	/** act: self_save, self_submit, mgr_save, mgr_submit, ack, reopen (HR). */
	public function review_save($id = 0)
	{
		$this->_ss_tables();
		$back = 'manager/hr/review/' . (int)$id;
		$this->_post_only($back);
		$a = $this->db->where('id', (int)$id)->get('ci_hr_appraisals')->row_array();
		$acc = $a ? $this->_review_access($a) : array('see' => false);
		$this->_need($a && $acc['see'], 'Review not found.', 'manager/hr/me');
		$act = (string)$this->input->post('act');
		$now = date('Y-m-d H:i:s');
		$txt = function ($k, $n = 5000) { return mb_substr(trim((string)$this->input->post($k)), 0, $n); };
		$rate = function ($v) { $v = round((float)$v * 2) / 2; return ($v >= 1 && $v <= 5) ? $v : null; };
		$name = $this->_name($a['user_id']);
		if ($act === 'self_save' || $act === 'self_submit') {
			$this->_need($acc['own'] && $a['stage'] === 'Self review', 'The self review cannot be changed now.', $back);
			$up = array('self_text' => $txt('self_text'), 'self_rating' => $rate($this->input->post('self_rating')));
			if ($act === 'self_submit') {
				$this->_need($up['self_text'] !== '' && $up['self_rating'] !== null, 'Write your self review and give yourself a rating before sending.', $back);
				$up += array('stage' => 'Manager review', 'self_at' => $now);
				$this->ci_admin_model->add_notification((int)$a['reviewer_id'], 'review', 'Review to do: ' . $name, $name . ' finished their self review for ' . $a['cycle_name'] . '. Your review is next.', base_url($back), 'fa fa-star-o', '#0072f0');
			}
			$this->db->where('id', $a['id'])->update('ci_hr_appraisals', $up);
			$msg = $act === 'self_submit' ? 'Self review sent to your manager.' : 'Saved. Send it when you are ready.';
		} elseif ($act === 'mgr_save' || $act === 'mgr_submit' || $act === 'skip_self') {
			$this->_need($acc['reviewer'] && $a['stage'] !== 'Completed', 'You cannot review this now.', $back);
			if ($act === 'skip_self') {
				$this->_need($acc['hr'] && $a['stage'] === 'Self review', 'Only HR can skip the self review.', $back);
				$this->db->where('id', $a['id'])->update('ci_hr_appraisals', array('stage' => 'Manager review'));
				$this->session->set_flashdata('message', 'Self review skipped; the manager can review now.');
				redirect($back);
			}
			$this->_need($a['stage'] === 'Manager review' || $acc['hr'], 'Wait for the self review first.', $back);
			$scores = array();
			$in = (array)$this->input->post('score');
			foreach (self::review_criteria() as $i => $c) { $v = isset($in[$i]) ? $rate($in[$i]) : null; if ($v !== null) $scores[$c] = $v; }
			$overall = $rate($this->input->post('rating'));
			if ($overall === null && $scores) $overall = round(array_sum($scores) / count($scores) * 2) / 2;
			$up = array('scores' => $scores ? json_encode($scores) : null, 'rating' => $overall !== null ? $overall : 0, 'kpi_score' => min(100, max(0, (float)$this->input->post('kpi_score'))),
				'strengths' => $txt('strengths'), 'improvements' => $txt('improvements'), 'goals' => $txt('goals'), 'feedback' => $txt('feedback'));
			if ($act === 'mgr_submit') {
				$this->_need($overall !== null && $up['feedback'] !== '', 'Give the scores (or an overall rating) and a summary before finishing.', $back);
				$up += array('stage' => 'Completed', 'status' => 'Completed', 'manager_at' => $now);
				if ($a['stage'] === 'Self review') $up['self_at'] = null;
				$this->ci_admin_model->add_notification((int)$a['user_id'], 'review', 'Your review is ready', 'Your ' . $a['cycle_name'] . ' review is complete (' . $overall . ' / 5). Read it and confirm you have seen it.', base_url($back), 'fa fa-star', '#16a34a');
			}
			$this->db->where('id', $a['id'])->update('ci_hr_appraisals', $up);
			$msg = $act === 'mgr_submit' ? 'Review finished and shared with ' . $name . '.' : 'Saved.';
		} elseif ($act === 'ack') {
			$this->_need($acc['own'] && $a['stage'] === 'Completed' && empty($a['ack_at']), 'Nothing to confirm.', $back);
			$this->db->where('id', $a['id'])->update('ci_hr_appraisals', array('ack_at' => $now, 'ack_note' => $txt('ack_note', 500)));
			$msg = 'Thank you. Your manager and HR can see that you have read your review.';
		} elseif ($act === 'reopen') {
			$this->_need($acc['hr'] && $a['stage'] === 'Completed', 'Only HR can reopen a finished review.', $back);
			$this->db->where('id', $a['id'])->update('ci_hr_appraisals', array('stage' => 'Manager review', 'status' => 'Open', 'manager_at' => null, 'ack_at' => null));
			$msg = 'Review reopened for the manager.';
		} else {
			redirect($back);
		}
		$this->session->set_flashdata('message', $msg);
		redirect($back);
	}

	/* ---------- Onboarding and exit checklists ---------- */

	public function checklists()
	{
		$this->_ss_tables();
		$data = array();
		$data['info'] = array('title' => 'Onboarding & Exit - HR', 'page_heading' => 'Onboarding & Exit');
		$data['lists'] = $this->db->query("SELECT C.*, U.name, U.department, COUNT(I.id) AS n, SUM(I.done_at IS NOT NULL) AS done, SUM(I.done_at IS NULL AND I.due_date < CURDATE()) AS late
			FROM ci_hr_checklists C LEFT JOIN ci_user U ON U.id = C.user_id LEFT JOIN ci_hr_checklist_items I ON I.checklist_id = C.id GROUP BY C.id ORDER BY C.status = 'Open' DESC, C.start_date DESC LIMIT 300")->result_array();
		$data['templates'] = array('onboarding' => array(), 'offboarding' => array());
		foreach ($this->db->order_by('kind, sort, id')->get('ci_hr_checklist_templates')->result_array() as $t) $data['templates'][$t['kind']][] = $t;
		$data['people'] = $this->_people(true);
		$data['owners'] = self::checklist_owners();
		$data['can_edit'] = $this->is_admin || crm_can('hr_lifecycle', 'write');
		$this->load->view('manager/hr/checklists', $data);
	}

	public function checklist_new()
	{
		$this->_ss_tables();
		$this->_post_only('manager/hr/checklists');
		$uid = (int)$this->input->post('user_id');
		$kind = $this->input->post('kind') === 'offboarding' ? 'offboarding' : 'onboarding';
		$start = $this->_ymd($this->input->post('start_date'));
		$u = $uid ? $this->db->select('id, name, c_doj')->where('id', $uid)->get('ci_user')->row_array() : array();
		if ($u && $start === '') {
			if ($kind === 'onboarding' && !empty($u['c_doj']) && strtotime($u['c_doj'])) $start = date('Y-m-d', strtotime($u['c_doj']));
			if ($kind === 'offboarding' && $this->db->table_exists('ci_user_exit') && ($e = $this->db->where('user_id', $uid)->get('ci_user_exit')->row_array())) $start = $e['left_on'];
		}
		$this->_need($u && $start !== '', 'Choose the employee and the ' . ($kind === 'onboarding' ? 'joining date' : 'last working day') . '.', 'manager/hr/checklists');
		$open = $this->db->where('user_id', $uid)->where('kind', $kind)->where('status', 'Open')->get('ci_hr_checklists')->row_array();
		if ($open) redirect('manager/hr/checklist/' . $open['id']);
		$this->db->insert('ci_hr_checklists', array('user_id' => $uid, 'kind' => $kind, 'start_date' => $start, 'status' => 'Open', 'created_by' => $this->cur_uid, 'created_at' => date('Y-m-d H:i:s')));
		$cid = (int)$this->db->insert_id();
		foreach ($this->db->where('kind', $kind)->order_by('sort, id')->get('ci_hr_checklist_templates')->result_array() as $i => $t)
			$this->db->insert('ci_hr_checklist_items', array('checklist_id' => $cid, 'title' => $t['title'], 'owner' => $t['owner'], 'due_date' => date('Y-m-d', strtotime($start . ' ' . ((int)$t['days_offset'] >= 0 ? '+' : '') . (int)$t['days_offset'] . ' days')), 'sort' => $i + 1));
		$this->ci_admin_model->log_admin_action('hr_checklist_new', ucfirst($kind) . ' checklist for ' . $u['name'], 'SUCCESS');
		$this->session->set_flashdata('message', ($kind === 'onboarding' ? 'Onboarding' : 'Exit') . ' checklist started for ' . $u['name'] . '.');
		redirect('manager/hr/checklist/' . $cid);
	}

	public function checklist($id = 0)
	{
		$this->_ss_tables();
		$c = $this->db->query('SELECT C.*, U.name, U.department, U.c_doj FROM ci_hr_checklists C LEFT JOIN ci_user U ON U.id = C.user_id WHERE C.id = ?', array((int)$id))->row_array();
		if (!$c) { $this->session->set_flashdata('error_message', 'Checklist not found.'); redirect('manager/hr/checklists'); }
		$data = array('c' => $c, 'owners' => self::checklist_owners(), 'can_edit' => $this->is_admin || crm_can('hr_lifecycle', 'write'));
		$data['items'] = $this->db->query('SELECT I.*, D.name AS done_name FROM ci_hr_checklist_items I LEFT JOIN ci_user D ON D.id = I.done_by WHERE I.checklist_id = ? ORDER BY I.sort, I.id', array((int)$c['id']))->result_array();
		$data['info'] = array('title' => ($c['kind'] === 'onboarding' ? 'Onboarding' : 'Exit') . ' - ' . $c['name'], 'page_heading' => 'Checklist');
		$this->load->view('manager/hr/checklist', $data);
	}

	/** Tick or untick a step (JSON). HR for every step; the employee for their own "Employee" steps. */
	public function checklist_tick()
	{
		$this->_ss_tables();
		$it = $this->db->query('SELECT I.*, C.user_id, C.status AS cstatus FROM ci_hr_checklist_items I JOIN ci_hr_checklists C ON C.id = I.checklist_id WHERE I.id = ?', array((int)$this->input->post('id')))->row_array();
		$hr = $this->is_admin || crm_can('hr_lifecycle', 'write');
		$ok = $it && $this->input->server('REQUEST_METHOD') === 'POST' && ($hr || ((int)$it['user_id'] === $this->cur_uid && $it['owner'] === 'Employee'));
		header('Content-Type: application/json');
		if (!$ok) { echo json_encode(array('ok' => false, 'message' => 'You cannot change this step.')); return; }
		$done = (int)$this->input->post('done') === 1;
		$this->db->where('id', $it['id'])->update('ci_hr_checklist_items', $done ? array('done_by' => $this->cur_uid, 'done_at' => date('Y-m-d H:i:s')) : array('done_by' => null, 'done_at' => null));
		$left = (int)$this->db->where('checklist_id', $it['checklist_id'])->where('done_at IS NULL', null, false)->count_all_results('ci_hr_checklist_items');
		$this->db->where('id', $it['checklist_id'])->update('ci_hr_checklists', $left ? array('status' => 'Open', 'done_at' => null) : array('status' => 'Done', 'done_at' => date('Y-m-d H:i:s')));
		echo json_encode(array('ok' => true, 'left' => $left, 'by' => $done ? $this->_name($this->cur_uid) : '', 'at' => $done ? date('d M Y') : ''));
	}

	/** Add a step to one checklist (POST id, title, owner, due_date) or remove one (POST remove). */
	public function checklist_item_add($id = 0)
	{
		$this->_ss_tables();
		$back = 'manager/hr/checklist/' . (int)$id;
		$this->_post_only($back);
		if ((int)$this->input->post('remove') > 0) {
			$this->db->where('id', (int)$this->input->post('remove'))->where('checklist_id', (int)$id)->delete('ci_hr_checklist_items');
			redirect($back);
		}
		$title = mb_substr(trim((string)$this->input->post('title')), 0, 150);
		$owner = in_array($this->input->post('owner'), self::checklist_owners(), true) ? $this->input->post('owner') : 'HR';
		$this->_need($title !== '', 'Write the step.', $back);
		$max = $this->db->select_max('sort')->where('checklist_id', (int)$id)->get('ci_hr_checklist_items')->row_array();
		$this->db->insert('ci_hr_checklist_items', array('checklist_id' => (int)$id, 'title' => $title, 'owner' => $owner, 'due_date' => $this->_ymd($this->input->post('due_date')) ?: null, 'sort' => (int)$max['sort'] + 1));
		$this->db->where('id', (int)$id)->update('ci_hr_checklists', array('status' => 'Open', 'done_at' => null));
		redirect($back);
	}

	public function checklist_delete($id = 0)
	{
		$this->_ss_tables();
		$this->_post_only('manager/hr/checklists');
		$this->db->where('checklist_id', (int)$id)->delete('ci_hr_checklist_items');
		$this->db->where('id', (int)$id)->delete('ci_hr_checklists');
		$this->session->set_flashdata('message', 'Checklist deleted.');
		redirect('manager/hr/checklists');
	}

	/** Replace the default steps of one kind (POST kind, title[], owner[], days[]). */
	public function checklist_template_save()
	{
		$this->_ss_tables();
		$this->_post_only('manager/hr/checklists');
		$kind = $this->input->post('kind') === 'offboarding' ? 'offboarding' : 'onboarding';
		$titles = (array)$this->input->post('title');
		$owners = (array)$this->input->post('owner');
		$days = (array)$this->input->post('days');
		$rows = array();
		foreach ($titles as $i => $t) {
			$t = mb_substr(trim((string)$t), 0, 150);
			if ($t === '') continue;
			$rows[] = array('kind' => $kind, 'title' => $t, 'owner' => isset($owners[$i]) && in_array($owners[$i], self::checklist_owners(), true) ? $owners[$i] : 'HR', 'days_offset' => isset($days[$i]) ? max(-365, min(365, (int)$days[$i])) : 0, 'sort' => count($rows) + 1);
		}
		$this->_need(count($rows) > 0, 'Keep at least one step.', 'manager/hr/checklists');
		$this->db->where('kind', $kind)->delete('ci_hr_checklist_templates');
		$this->db->insert_batch('ci_hr_checklist_templates', $rows);
		$this->ci_admin_model->log_admin_action('hr_checklist_template', 'Changed the default ' . $kind . ' steps (' . count($rows) . ')', 'SUCCESS');
		$this->session->set_flashdata('message', 'Default ' . ($kind === 'onboarding' ? 'onboarding' : 'exit') . ' steps saved. New checklists use them.');
		redirect('manager/hr/checklists');
	}

	/** Company HR settings with their defaults (UAE). */
	public static function hr_setting_defaults()
	{
		return array(
			'company_name' => '', 'trade_licence_no' => '', 'mol_establishment_id' => '',
			'employer_bank_name' => '', 'employer_routing_code' => '', 'currency' => 'AED', 'salary_day' => '28',
			'weekend' => 'Sat,Sun', 'work_hours' => '8',
			'probation_months' => '6', 'notice_days' => '30', 'annual_leave_days' => '30', 'doc_alert_days' => '60',
			'ot_rate' => '1.25', 'ot_rest_rate' => '1.5', 'deduct_absence' => '0', 'gratuity_days_first5' => '21', 'gratuity_days_after5' => '30',
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
			'ot_rate' => $num($p('ot_rate'), 1, 5, $def['ot_rate']),
			'ot_rest_rate' => $num($p('ot_rest_rate'), 1, 5, $def['ot_rest_rate']),
			'deduct_absence' => $this->input->post('deduct_absence') ? '1' : '0',
			'gratuity_days_first5' => $num($p('gratuity_days_first5'), 0, 60, $def['gratuity_days_first5']),
			'gratuity_days_after5' => $num($p('gratuity_days_after5'), 0, 60, $def['gratuity_days_after5']),
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
