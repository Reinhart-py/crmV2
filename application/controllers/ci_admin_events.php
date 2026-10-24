<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ci_admin_events extends CI_Controller {

	public function __construct() 
	{
		parent::__construct();
		$this->load->library(array('Session'));
		$this->load->helper(array('form', 'url', 'directory', 'text', 'date'));
		$this->load->model(array('comman_model', 'ci_admin_model'));
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
		header("Cache-Control: no-store, no-cache, must-revalidate"); 
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Cache-Control: no-cache');
	}

	public function if_not_login($url = '')
	{
		if ($this->session->userdata('manager_id') == '') {
			if ($url != '') {
				redirect($url);
			} else {
				redirect('/manager/login');
			}
		}
	}

	public function index()
	{
		$this->if_not_login();
		$manager_id = (int)$this->session->userdata('manager_id');
		$manager_role = (int)$this->session->userdata('manager_role');

		$data = array();
		$data['info'] = array(
			'title' => 'Event and Awards',
			'page_heading' => 'Events & Awards Calendar'
		);

		$type = trim($this->input->get('type'));
		$month = trim($this->input->get('month'));
		if (empty($month)) {
			$month = date('Y-m');
		}

		$this->db->select('E.*, U.name as creator_name');
		$this->db->from('ci_events as E');
		$this->db->join('ci_user as U', 'U.id = E.created_by', 'left');
		if (!empty($type)) {
			$this->db->where('E.event_type', $type);
		}
		if (!empty($month)) {
			$this->db->like('E.start_date', $month, 'after');
		}
		$this->db->order_by('E.start_date', 'ASC');
		$data['events'] = $this->db->get()->result_array();

		$data['upcoming_events'] = array();
		$data['current_month'] = $month;
		$data['filter_type'] = $type;
		$data['can_manage'] = ($manager_role == 1 || $manager_role == 4 || $this->session->userdata('is_manager'));
		$data['users_list'] = $this->db->select('id, name, department')->where('status', 1)->order_by('name', 'ASC')->get('ci_user')->result_array();

		$this->load->view('manager/events/index', $data);
	}

	public function add()
	{
		$this->if_not_login();
		$manager_id = (int)$this->session->userdata('manager_id');

		if ($this->input->post()) {
			$title = trim($this->input->post('title'));
			$event_type = trim($this->input->post('event_type'));
			$start_date = trim($this->input->post('start_date'));
			$end_date = trim($this->input->post('end_date'));
			$all_day = (int)$this->input->post('all_day');
			$location = trim($this->input->post('location'));
			$description = trim($this->input->post('description'));

			if (!empty($title) && !empty($start_date)) {
				$insert_data = array(
					'title' => $title,
					'description' => $description,
					'event_type' => $event_type ?: 'Meeting',
					'start_date' => date('Y-m-d H:i:s', strtotime($start_date)),
					'end_date' => !empty($end_date) ? date('Y-m-d H:i:s', strtotime($end_date)) : date('Y-m-d H:i:s', strtotime($start_date . ' +1 hour')),
					'all_day' => $all_day,
					'location' => $location,
					'created_by' => $manager_id,
					'created_at' => date('Y-m-d H:i:s'),
					'status' => 'active'
				);
				$this->db->insert('ci_events', $insert_data);
				$this->session->set_flashdata('message', 'Event scheduled successfully.');
			} else {
				$this->session->set_flashdata('error_message', 'Title and Start Date are required.');
			}
		}
		redirect('manager/events');
	}

	public function delete($id = 0)
	{
		$this->if_not_login();
		$id = (int)$id;
		if ($id > 0) {
			$this->db->where('id', $id)->delete('ci_events');
			$this->session->set_flashdata('message', 'Event removed.');
		}
		redirect('manager/events');
	}

	public function ajax_get_events()
	{
		$this->if_not_login();
		$start = $this->input->get('start');
		$end = $this->input->get('end');

		$calendar_events = array();

		$cur_role = (int)$this->session->userdata('manager_role');
		$cur_uid = (int)$this->session->userdata('manager_id');
		$is_admin = ($cur_role === 1 || $cur_uid === 1);

		$this->db->select('E.*, U.name as creator_name');
		$this->db->from('ci_events as E');
		$this->db->join('ci_user as U', 'U.id = E.created_by', 'left');
		if (!empty($start)) {
			$this->db->where('E.start_date >=', date('Y-m-d 00:00:00', strtotime($start)));
		}
		if (!empty($end)) {
			$this->db->where('E.start_date <=', date('Y-m-d 23:59:59', strtotime($end)));
		}
		if (!$is_admin) {
			$this->db->where("(E.visibility = 'public' OR E.created_by = {$cur_uid} OR E.assigned_user_id = {$cur_uid})");
		}
		$events_rows = $this->db->get()->result_array();

		foreach ($events_rows as $r) {
			$color = '#2563eb';
			if ($r['event_type'] == 'Training') $color = '#0284c7';
			elseif ($r['event_type'] == 'Company Event') $color = '#16a34a';
			elseif ($r['event_type'] == 'Milestone') $color = '#8b5cf6';
			elseif ($r['event_type'] == 'Holiday') $color = '#dc2626';
			elseif ($r['event_type'] == 'Reminder') $color = '#0d9488';

			$calendar_events[] = array(
				'id' => 'evt_' . $r['id'],
				'title' => $r['title'],
				'start' => date('Y-m-d\TH:i:s', strtotime($r['start_date'])),
				'end' => !empty($r['end_date']) ? date('Y-m-d\TH:i:s', strtotime($r['end_date'])) : date('Y-m-d\TH:i:s', strtotime($r['start_date'])),
				'allDay' => (bool)$r['all_day'],
				'color' => $color,
				'textColor' => '#ffffff',
				'className' => 'crm-event-pill',
				'raw_id' => (int)$r['id'],
				'item_type' => 'event',
				'event_type' => $r['event_type'],
				'location' => $r['location'] ?: '',
				'description' => $r['description'] ?: '',
				'creator' => $r['creator_name'] ?: ''
			);
		}

		$this->db->select('A.*, U.name as employee_name, U.department, C.name as creator_name');
		$this->db->from('ci_awards as A');
		$this->db->join('ci_user as U', 'U.id = A.user_id', 'left');
		$this->db->join('ci_user as C', 'C.id = A.created_by', 'left');
		if (!empty($start)) {
			$this->db->where('A.award_date >=', date('Y-m-d', strtotime($start)));
		}
		if (!empty($end)) {
			$this->db->where('A.award_date <=', date('Y-m-d', strtotime($end)));
		}
		$awards_rows = $this->db->get()->result_array();

		foreach ($awards_rows as $a) {
			$emp = !empty($a['employee_name']) ? $a['employee_name'] : 'Employee';
			$pts = !empty($a['reward_points']) ? ' (' . $a['reward_points'] . ' pts)' : '';
			$calendar_events[] = array(
				'id' => 'award_' . $a['id'],
				'title' => '🏆 ' . $emp . ': ' . $a['title'] . $pts,
				'start' => date('Y-m-d', strtotime($a['award_date'])),
				'allDay' => true,
				'color' => '#d97706',
				'textColor' => '#ffffff',
				'className' => 'crm-award-pill',
				'raw_id' => (int)$a['id'],
				'item_type' => 'award',
				'event_type' => 'Award',
				'employee' => $emp,
				'category' => $a['category'],
				'points' => (int)$a['reward_points'],
				'description' => $a['description'] ?: '',
				'creator' => $a['creator_name'] ?: ''
			);
		}

		$ref_year = !empty($start) ? date('Y', strtotime($start)) : date('Y');
		$users = $this->db->select('id, name, dob, department')->where('status', 1)->where('dob IS NOT NULL')->where('dob !=', '')->get('ci_user')->result_array();
		$seen_birthdays = array();
		foreach ($users as $u) {
			$dob_ts = strtotime($u['dob']);
			if ($dob_ts !== false && $dob_ts > 0) {
				$m_d = date('m-d', $dob_ts);
				$years_to_check = array($ref_year, (int)$ref_year + 1, (int)$ref_year - 1);
				foreach ($years_to_check as $yr) {
					$b_date = $yr . '-' . $m_d;
					$b_key = $u['id'] . '_' . $b_date;
					if (isset($seen_birthdays[$b_key])) {
						continue;
					}
					if ((empty($start) || $b_date >= date('Y-m-d', strtotime($start))) && (empty($end) || $b_date <= date('Y-m-d', strtotime($end)))) {
						$seen_birthdays[$b_key] = true;
						$calendar_events[] = array(
							'id' => 'bday_' . $u['id'] . '_' . $yr,
							'title' => '🎂 Birthday: ' . $u['name'],
							'start' => $b_date,
							'allDay' => true,
							'color' => '#ec4899',
							'textColor' => '#ffffff',
							'className' => 'crm-birthday-pill',
							'raw_id' => (int)$u['id'],
							'item_type' => 'birthday',
							'event_type' => 'Birthday',
							'employee' => $u['name'],
							'department' => $u['department'] ?: '',
							'description' => 'Employee birthday on ' . date('M j', $dob_ts)
						);
					}
				}
			}
		}

		header('Content-Type: application/json');
		echo json_encode($calendar_events);
		exit();
	}

	public function ajax_save_event()
	{
		$this->if_not_login();
		$manager_id = (int)$this->session->userdata('manager_id');

		$id = (int)$this->input->post('id');
		$title = trim($this->input->post('title'));
		$event_type = trim($this->input->post('event_type'));
		$start_date = trim($this->input->post('start_date'));
		$end_date = trim($this->input->post('end_date'));
		$all_day = (int)$this->input->post('all_day');
		$location = trim($this->input->post('location'));
		$description = trim($this->input->post('description'));

		if (empty($title) || empty($start_date)) {
			header('Content-Type: application/json');
			echo json_encode(array('success' => false, 'message' => 'Title and Start Date are required.'));
			exit();
		}

		$cur_role = (int)$this->session->userdata('manager_role');
		$cur_uid = (int)$this->session->userdata('manager_id');
		$is_admin = ($cur_role === 1 || $cur_uid === 1);

		$visibility = trim($this->input->post('visibility', true));
		$assigned_user_id = (int)$this->input->post('assigned_user_id', true);

		if ($is_admin) {
			$visibility = in_array($visibility, array('public', 'individual', 'private')) ? $visibility : 'public';
			if ($visibility !== 'individual') {
				$assigned_user_id = 0;
			}
		} elseif ($cur_role === 4 || has_perm('back_office') || has_perm('support')) {
			$visibility = 'public';
			$assigned_user_id = 0;
		} else {
			$visibility = 'private';
			$assigned_user_id = $manager_id;
		}

		$payload = array(
			'title' => $title,
			'description' => $description,
			'event_type' => $event_type ?: 'Meeting',
			'start_date' => date('Y-m-d H:i:s', strtotime($start_date)),
			'end_date' => !empty($end_date) ? date('Y-m-d H:i:s', strtotime($end_date)) : date('Y-m-d H:i:s', strtotime($start_date . ' +1 hour')),
			'all_day' => $all_day,
			'location' => $location,
			'visibility' => $visibility,
			'assigned_user_id' => $assigned_user_id,
			'status' => 'active'
		);

		if ($id > 0) {
			$this->db->where('id', $id)->update('ci_events', $payload);
			$msg = 'Event updated successfully.';
		} else {
			$payload['created_by'] = $manager_id;
			$payload['created_at'] = date('Y-m-d H:i:s');
			$this->db->insert('ci_events', $payload);
			$msg = 'Event scheduled successfully.';
		}

		header('Content-Type: application/json');
		echo json_encode(array('success' => true, 'message' => $msg));
		exit();
	}

	public function ajax_save_award()
	{
		$this->if_not_login();
		$manager_id = (int)$this->session->userdata('manager_id');
		$manager_role = (int)$this->session->userdata('manager_role');

		if ($manager_role != 1 && $manager_role != 4 && !$this->session->userdata('is_manager')) {
			header('Content-Type: application/json');
			echo json_encode(array('success' => false, 'message' => 'Permission denied.'));
			exit();
		}

		$user_id = (int)$this->input->post('user_id');
		$title = trim($this->input->post('title'));
		$category = trim($this->input->post('category'));
		$award_date = trim($this->input->post('award_date'));
		$description = trim($this->input->post('description'));
		$reward_points = (int)$this->input->post('reward_points');

		if ($user_id <= 0 || empty($title) || empty($award_date)) {
			header('Content-Type: application/json');
			echo json_encode(array('success' => false, 'message' => 'Employee, Title, and Award Date are required.'));
			exit();
		}

		$payload = array(
			'user_id' => $user_id,
			'title' => $title,
			'category' => $category ?: 'Top Performer',
			'award_date' => date('Y-m-d', strtotime($award_date)),
			'description' => $description,
			'reward_points' => ($reward_points > 0) ? $reward_points : 100,
			'created_by' => $manager_id,
			'created_at' => date('Y-m-d H:i:s')
		);

		$this->db->insert('ci_awards', $payload);

		header('Content-Type: application/json');
		echo json_encode(array('success' => true, 'message' => 'Award recorded successfully.'));
		exit();
	}

	public function ajax_delete_item()
	{
		$this->if_not_login();
		$type = trim($this->input->post('type'));
		$id = (int)$this->input->post('id');

		if ($id <= 0) {
			header('Content-Type: application/json');
			echo json_encode(array('success' => false, 'message' => 'Invalid ID.'));
			exit();
		}

		if ($type === 'award') {
			$this->db->where('id', $id)->delete('ci_awards');
			$msg = 'Award deleted.';
		} else {
			$this->db->where('id', $id)->delete('ci_events');
			$msg = 'Event deleted.';
		}

		header('Content-Type: application/json');
		echo json_encode(array('success' => true, 'message' => $msg));
		exit();
	}

	public function awards()
	{
		$this->if_not_login();
		redirect('manager/events');
	}

	public function add_award()
	{
		$this->if_not_login();
		$manager_id = (int)$this->session->userdata('manager_id');
		$manager_role = (int)$this->session->userdata('manager_role');

		if ($manager_role != 1 && $manager_role != 4 && !$this->session->userdata('is_manager')) {
			$this->session->set_flashdata('error_message', 'Permission denied.');
			redirect('manager/events');
		}

		if ($this->input->post()) {
			$user_id = (int)$this->input->post('user_id');
			$title = trim($this->input->post('title'));
			$category = trim($this->input->post('category'));
			$award_date = trim($this->input->post('award_date'));
			$description = trim($this->input->post('description'));
			$reward_points = (int)$this->input->post('reward_points');

			if ($user_id > 0 && !empty($title) && !empty($award_date)) {
				$insert_data = array(
					'user_id' => $user_id,
					'title' => $title,
					'category' => $category ?: 'Top Performer',
					'award_date' => date('Y-m-d', strtotime($award_date)),
					'description' => $description,
					'reward_points' => ($reward_points > 0) ? $reward_points : 100,
					'created_by' => $manager_id,
					'created_at' => date('Y-m-d H:i:s')
				);
				$this->db->insert('ci_awards', $insert_data);
				$this->session->set_flashdata('message', 'Achievement award conferred successfully.');
			} else {
				$this->session->set_flashdata('error_message', 'Employee, Title, and Award Date are required.');
			}
		}
		redirect('manager/events');
	}

	public function delete_award($id = 0)
	{
		$this->if_not_login();
		$manager_role = (int)$this->session->userdata('manager_role');
		if ($manager_role != 1 && $manager_role != 4 && !$this->session->userdata('is_manager')) {
			$this->session->set_flashdata('error_message', 'Permission denied.');
			redirect('manager/events');
		}

		$id = (int)$id;
		if ($id > 0) {
			$this->db->where('id', $id)->delete('ci_awards');
			$this->session->set_flashdata('message', 'Award removed.');
		}
		redirect('manager/events');
	}
}
