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
		$this->_check_hr_access();
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
		$is_hr = ($is_admin || $role === 7 || has_section_perm('hr'));
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

		$data['total_employees'] = $this->db->where('status', 1)->count_all_results('ci_user');
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
			->where('status', 1)
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

	public function lifecycle()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Employee Lifecycle - HR',
			'page_heading' => 'Employee Lifecycle'
		);
		$data['active_tab'] = 'lifecycle';

		$filter_stage = trim((string)$this->input->get('stage'));
		$filter_dept = trim((string)$this->input->get('department'));

		$this->db->select('U.id, U.id as user_id, U.emp_id, U.name, U.email, U.mobile, U.c_mobile, U.user_type, U.created, U.department as user_dept, U.c_salary, U.c_doj, U.c_dol, U.h_contact_name, U.h_contact_no, R.title as role_title, L.stage, L.department as lc_department, L.designation as lc_designation, L.date_of_joining as lc_doj, L.probation_end_date, L.confirmation_date, L.contract_type, L.emergency_contact_name as lc_e_name, L.emergency_contact_phone as lc_e_phone, L.notes as lifecycle_notes')
			->from('ci_user U')
			->join('ci_roles R', 'R.id = U.user_type', 'left')
			->join('ci_hr_lifecycle L', 'L.user_id = U.id', 'left')
			->where('U.status', 1);

		if (!empty($filter_stage)) {
			$this->db->where('L.stage', $filter_stage);
		}
		if (!empty($filter_dept)) {
			$this->db->where('U.department', $filter_dept);
		}

		$raw_list = $this->db->order_by('U.name', 'ASC')->get()->result_array();
		foreach ($raw_list as &$item) {
			$item['stage'] = !empty($item['stage']) ? $item['stage'] : 'Confirmed';
			$item['department'] = !empty($item['lc_department']) ? $item['lc_department'] : (!empty($item['user_dept']) ? $item['user_dept'] : 'General');
			$item['designation'] = !empty($item['lc_designation']) ? $item['lc_designation'] : (!empty($item['role_title']) ? $item['role_title'] : 'Staff');
			$item['date_of_joining'] = !empty($item['lc_doj']) ? $item['lc_doj'] : (!empty($item['c_doj']) ? $item['c_doj'] : substr($item['created'], 0, 10));
			$item['join_date'] = $item['date_of_joining'];
			$item['emergency_contact_name'] = !empty($item['lc_e_name']) ? $item['lc_e_name'] : (!empty($item['h_contact_name']) ? $item['h_contact_name'] : '');
			$item['emergency_contact_phone'] = !empty($item['lc_e_phone']) ? $item['lc_e_phone'] : (!empty($item['h_contact_no']) ? $item['h_contact_no'] : '');
		}
		unset($item);

		$data['lifecycle_list'] = $raw_list;
		$data['employees'] = $raw_list;

		$this->load->view('manager/hr/index', $data);
	}

	public function update_lifecycle()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') {
			redirect('manager/hr/lifecycle');
			return;
		}

		$user_id = (int)$this->input->post('user_id');
		if ($user_id <= 0) {
			redirect('manager/hr/lifecycle');
			return;
		}

		$stage = trim((string)$this->input->post('stage')) ?: 'Confirmed';
		$department = trim((string)$this->input->post('department'));
		$designation = trim((string)$this->input->post('designation'));
		$date_of_joining = trim((string)$this->input->post('date_of_joining')) ?: NULL;
		$probation_end_date = trim((string)$this->input->post('probation_end_date')) ?: NULL;
		$confirmation_date = trim((string)$this->input->post('confirmation_date')) ?: NULL;
		$contract_type = trim((string)$this->input->post('contract_type')) ?: 'Permanent';
		$emergency_contact_name = trim((string)$this->input->post('emergency_contact_name'));
		$emergency_contact_phone = trim((string)$this->input->post('emergency_contact_phone'));
		$notes = trim((string)$this->input->post('notes'));

		$exist = $this->db->where('user_id', $user_id)->get('ci_hr_lifecycle')->row_array();
		$save_data = array(
			'user_id' => $user_id,
			'stage' => $stage,
			'department' => $department,
			'designation' => $designation,
			'date_of_joining' => !empty($date_of_joining) ? date('Y-m-d', strtotime($date_of_joining)) : NULL,
			'probation_end_date' => !empty($probation_end_date) ? date('Y-m-d', strtotime($probation_end_date)) : NULL,
			'confirmation_date' => !empty($confirmation_date) ? date('Y-m-d', strtotime($confirmation_date)) : NULL,
			'contract_type' => $contract_type,
			'emergency_contact_name' => $emergency_contact_name,
			'emergency_contact_phone' => $emergency_contact_phone,
			'notes' => $notes,
			'updated_at' => date('Y-m-d H:i:s')
		);

		if (!empty($exist)) {
			$this->db->where('user_id', $user_id)->update('ci_hr_lifecycle', $save_data);
		} else {
			$save_data['created_at'] = date('Y-m-d H:i:s');
			$this->db->insert('ci_hr_lifecycle', $save_data);
		}

		$this->session->set_flashdata('message', 'Employee lifecycle record updated.');
		redirect('manager/hr/lifecycle');
	}

	public function attendance()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Attendance & Geolocation - HR',
			'page_heading' => 'Attendance with Geolocation'
		);
		$data['active_tab'] = 'attendance';

		$cur_uid = (int)$this->session->userdata('manager_id');
		$today_date = date('Y-m-d');
		$today_rec = $this->db->where('user_id', $cur_uid)->where('attendance_date', $today_date)->get('ci_attendance')->row_array();

		$data['today_punched'] = !empty($today_rec);
		$data['today_punch_in'] = (!empty($today_rec) && !empty($today_rec['in_time'])) ? $today_rec['in_time'] : '';
		$data['today_punch_out'] = (!empty($today_rec) && !empty($today_rec['out_time'])) ? $today_rec['out_time'] : '';

		$filter_date = trim((string)$this->input->get('date'));
		$filter_user = (int)$this->input->get('user_id');

		$this->db->select('A.*, U.name as employee_name, U.email as employee_email')
			->from('ci_attendance A')
			->join('ci_user U', 'U.id = A.user_id', 'left');

		if (!empty($filter_date)) {
			$this->db->where('A.attendance_date', $filter_date);
		}
		if ($filter_user > 0) {
			$this->db->where('A.user_id', $filter_user);
		}

		$raw_att = $this->db->order_by('A.id', 'DESC')->limit(100)->get()->result_array();
		foreach ($raw_att as &$ar) {
			$ar['clock_in'] = !empty($ar['in_time']) ? $ar['in_time'] : '';
			$ar['clock_out'] = !empty($ar['out_time']) ? $ar['out_time'] : '';
		}
		unset($ar);

		$data['attendance_records'] = $raw_att;
		$data['filter_date'] = $filter_date;
		$data['employees'] = $this->db->select('id, name, email')->where('status', 1)->order_by('name', 'ASC')->get('ci_user')->result_array();

		$this->load->view('manager/hr/index', $data);
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
				'status' => 'Present',
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
		$data['employees'] = $this->db->select('id, name, email')->where('status', 1)->order_by('name', 'ASC')->get('ci_user')->result_array();

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

	public function leaves()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Leave Management - HR',
			'page_heading' => 'Leaves & Time Off'
		);
		$data['active_tab'] = 'leaves';

		$filter_status = trim((string)$this->input->get('status'));

		$this->db->select('L.*, U.name as employee_name, Approver.name as approver_name')
			->from('ci_leaves L')
			->join('ci_user U', 'U.id = L.user_id', 'left')
			->join('ci_user Approver', 'Approver.id = L.approved_by', 'left');

		if (!empty($filter_status)) {
			$this->db->where('L.status', $filter_status);
		}

		$raw_leaves = $this->db->order_by('L.id', 'DESC')->get()->result_array();
		foreach ($raw_leaves as &$l) {
			$l['start_date'] = !empty($l['from_date']) ? $l['from_date'] : '';
			$l['end_date'] = !empty($l['to_date']) ? $l['to_date'] : '';
		}
		unset($l);

		$data['leaves'] = $raw_leaves;
		$data['employees'] = $this->db->select('id, name, email')->where('status', 1)->order_by('name', 'ASC')->get('ci_user')->result_array();

		$this->load->view('manager/hr/index', $data);
	}

	public function apply_leave()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') {
			redirect('manager/hr/leaves');
			return;
		}

		$user_id = (int)$this->input->post('user_id') ?: (int)$this->session->userdata('manager_id');
		$leave_type = trim((string)$this->input->post('leave_type')) ?: 'Annual Leave';

		$from_date = trim((string)$this->input->post('from_date'));
		if (empty($from_date)) {
			$from_date = trim((string)$this->input->post('start_date'));
		}
		$to_date = trim((string)$this->input->post('to_date'));
		if (empty($to_date)) {
			$to_date = trim((string)$this->input->post('end_date'));
		}
		$reason = trim((string)$this->input->post('reason'));

		$days = 1;
		if (!empty($from_date) && !empty($to_date)) {
			$days = max(1, round((strtotime($to_date) - strtotime($from_date)) / 86400) + 1);
		}

		if (!empty($from_date) && !empty($to_date)) {
			$this->db->insert('ci_leaves', array(
				'user_id' => $user_id,
				'leave_type' => $leave_type,
				'from_date' => $from_date,
				'to_date' => $to_date,
				'total_days' => $days,
				'reason' => $reason,
				'status' => 'Pending',
				'created_at' => date('Y-m-d H:i:s')
			));

			$this->ci_admin_model->notify_role(
				array(1, 7),
				'leave_requested',
				'New Leave Request',
				"Leave request ({$days} day(s), {$leave_type}) submitted by employee ID #{$user_id}",
				base_url('manager/hr/leaves'),
				'fa fa-calendar-check-o',
				'#0072f0'
			);

			$this->session->set_flashdata('message', 'Leave application submitted for approval.');
		}
		redirect('manager/hr/leaves');
	}

	public function leave_action($param1 = '', $param2 = '')
	{
		if (is_numeric($param1)) {
			$id = (int)$param1;
			$action = strtolower(trim((string)$param2));
		} else {
			$action = strtolower(trim((string)$param1));
			$id = (int)$param2;
		}

		$cur_uid = (int)$this->session->userdata('manager_id');
		$leave = $this->db->where('id', $id)->get('ci_leaves')->row_array();

		if (!empty($leave) && in_array($action, array('approve', 'approved', 'reject', 'rejected'))) {
			$new_status = in_array($action, array('approve', 'approved')) ? 'Approved' : 'Rejected';
			$this->db->where('id', $id)->update('ci_leaves', array(
				'status' => $new_status,
				'approved_by' => $cur_uid
			));

			$this->ci_admin_model->add_notification(
				(int)$leave['user_id'],
				'leave_update',
				"Leave Request {$new_status}",
				"Your leave request from {$leave['from_date']} to {$leave['to_date']} has been {$new_status}.",
				base_url('manager/hr/leaves'),
				($new_status === 'Approved') ? 'fa fa-check-circle' : 'fa fa-times-circle',
				($new_status === 'Approved') ? '#16a34a' : '#dc2626'
			);

			$this->session->set_flashdata('message', "Leave request marked {$new_status}.");
		}
		redirect('manager/hr/leaves');
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
		$data['employees'] = $this->db->select('id, name, email, c_salary')->where('status', 1)->order_by('name', 'ASC')->get('ci_user')->result_array();

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
		$employees = $this->db->select('id, name, c_salary')->where('status', 1)->get('ci_user')->result_array();

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

		$data['employees'] = $this->db->select('id, name, email, c_salary')->where('status', 1)->order_by('name', 'ASC')->get('ci_user')->result_array();

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
		$data['employees'] = $this->db->select('id, name, email')->where('status', 1)->order_by('name', 'ASC')->get('ci_user')->result_array();

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

	public function documents()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Document Vault - HR',
			'page_heading' => 'Employee Documents'
		);
		$data['active_tab'] = 'documents';

		$docs = $this->db->select('D.*, U.name as employee_name')
			->from('ci_hr_documents D')
			->join('ci_user U', 'U.id = D.user_id', 'left')
			->order_by('D.id', 'DESC')
			->get()
			->result_array();

		$u_docs = $this->db->select('id, name, p_no, p_date, p_exdate, visa_no, visa_type, visa_expiry, eid_no, eid_expiry')
			->where('status', 1)
			->get('ci_user')
			->result_array();

		foreach ($u_docs as $ud) {
			if (!empty($ud['p_no'])) {
				$docs[] = array(
					'id' => 100000 + (int)$ud['id'],
					'user_id' => $ud['id'],
					'employee_name' => $ud['name'],
					'doc_type' => 'Passport',
					'document_type' => 'Passport',
					'title' => 'Passport Copy',
					'doc_number' => $ud['p_no'],
					'document_number' => $ud['p_no'],
					'issue_date' => $ud['p_date'],
					'expiry_date' => $ud['p_exdate'],
					'status' => 'Valid'
				);
			}
			if (!empty($ud['eid_no'])) {
				$docs[] = array(
					'id' => 200000 + (int)$ud['id'],
					'user_id' => $ud['id'],
					'employee_name' => $ud['name'],
					'doc_type' => 'Emirates ID',
					'document_type' => 'Emirates ID',
					'title' => 'Emirates ID Card',
					'doc_number' => $ud['eid_no'],
					'document_number' => $ud['eid_no'],
					'issue_date' => '',
					'expiry_date' => $ud['eid_expiry'],
					'status' => 'Valid'
				);
			}
			if (!empty($ud['visa_no'])) {
				$docs[] = array(
					'id' => 300000 + (int)$ud['id'],
					'user_id' => $ud['id'],
					'employee_name' => $ud['name'],
					'doc_type' => 'Visa',
					'document_type' => 'Visa',
					'title' => 'UAE Residence Visa (' . ($ud['visa_type'] ?: 'Employment') . ')',
					'doc_number' => $ud['visa_no'],
					'document_number' => $ud['visa_no'],
					'issue_date' => '',
					'expiry_date' => $ud['visa_expiry'],
					'status' => 'Valid'
				);
			}
		}

		foreach ($docs as &$d) {
			$d['document_type'] = !empty($d['doc_type']) ? $d['doc_type'] : (!empty($d['document_type']) ? $d['document_type'] : 'Document');
			$d['document_number'] = !empty($d['doc_number']) ? $d['doc_number'] : (!empty($d['document_number']) ? $d['document_number'] : '');
			$d['title'] = !empty($d['title']) ? $d['title'] : $d['document_type'] . ' - ' . $d['document_number'];
		}
		unset($d);

		$data['documents'] = $docs;
		$data['employees'] = $this->db->select('id, name, email')->where('status', 1)->order_by('name', 'ASC')->get('ci_user')->result_array();

		$this->load->view('manager/hr/index', $data);
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
}
