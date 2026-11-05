<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Events & Festivals.
 *  - index():  view-only page for everyone with the "Events & Festivals" right (Roles > Core Modules):
 *              company holidays, festivals, company events and awards for last / this / next year.
 *  - manage(): publishing page for Admin, HR and roles with Full on "Manage Events & Festivals".
 */
class Ci_admin_events extends CI_Controller {

	// Stored event_type => label, colour
	public static $types = array(
		'public_holiday' => array('Public Holiday', '#dc2626'),
		'festival'       => array('Festival', '#d97706'),
		'company_event'  => array('Company Event', '#2563eb'),
	);

	private $cur_uid = 0;
	private $can_manage = false;

	public function __construct()
	{
		parent::__construct();
		$this->load->library(array('Session'));
		$this->load->helper(array('form', 'url', 'text', 'date'));
		$this->load->model(array('comman_model', 'ci_admin_model'));
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Pragma: no-cache');

		$this->cur_uid = (int)$this->session->userdata('manager_id');
		if ($this->cur_uid === 0) {
			redirect('/manager/login');
			exit;
		}
		$role = (int)$this->session->userdata('manager_role');
		$is_admin = ($role === 1 || $this->cur_uid === 1);
		$this->can_manage = ($is_admin || $role === 7 || crm_can('events_list', 'write'));

		$method = $this->router->fetch_method();
		if ($method === 'index') {
			if (!$is_admin && !$this->can_manage && !crm_can('events_view', 'read')) {
				$this->session->set_flashdata('error_message', 'You do not have access to Events & Festivals. Ask your administrator if you need it.');
				redirect(crm_landing_url());
				exit;
			}
		} elseif (!$this->can_manage) {
			$this->session->set_flashdata('error_message', 'Only HR and administrators can manage Events & Festivals.');
			redirect('manager/events');
			exit;
		}
	}

	/** Selected year, limited to last / this / next year. */
	private function _year()
	{
		$now = (int)date('Y');
		$y = (int)$this->input->get('year');
		return ($y >= $now - 1 && $y <= $now + 1) ? $y : $now;
	}

	/** Company-wide events and awards of one year, sorted by date. */
	private function _items($year)
	{
		$items = array();
		$events = $this->db->select('id, title, description, event_type, start_date, end_date, all_day, location')
			->from('ci_events')
			->where('visibility', 'public')
			->where("(status IS NULL OR status = 'active')", null, false)
			->where('start_date >=', $year . '-01-01 00:00:00')
			->where('start_date <=', $year . '-12-31 23:59:59')
			->order_by('start_date', 'ASC')
			->get()->result_array();
		foreach ($events as $e) {
			$t = strtolower((string)$e['event_type']);
			if ($t === 'holiday') $t = 'public_holiday';
			if (!isset(self::$types[$t])) $t = 'company_event';
			$items[] = array(
				'kind' => 'event', 'id' => (int)$e['id'], 'type' => $t,
				'title' => $e['title'], 'description' => (string)$e['description'],
				'start' => $e['start_date'], 'end' => $e['end_date'], 'all_day' => (int)$e['all_day'],
				'location' => (string)$e['location'],
			);
		}
		$awards = $this->db->select('A.id, A.title, A.category, A.award_date, A.description, A.user_id, U.name AS employee')
			->from('ci_awards A')
			->join('ci_user U', 'U.id = A.user_id', 'left')
			->where('A.award_date >=', $year . '-01-01')
			->where('A.award_date <=', $year . '-12-31')
			->order_by('A.award_date', 'ASC')
			->get()->result_array();
		foreach ($awards as $a) {
			$items[] = array(
				'kind' => 'award', 'id' => (int)$a['id'], 'type' => 'award',
				'title' => $a['title'], 'description' => (string)$a['description'],
				'start' => $a['award_date'] . ' 00:00:00', 'end' => null, 'all_day' => 1, 'location' => '',
				'employee' => (string)$a['employee'], 'user_id' => (int)$a['user_id'], 'category' => (string)$a['category'],
			);
		}
		usort($items, function ($x, $y) { return strcmp($x['start'], $y['start']); });
		return $items;
	}

	public function index()
	{
		$year = $this->_year();
		$data = array();
		$data['info'] = array('title' => 'Events & Festivals', 'page_heading' => 'Events & Festivals');
		$data['year'] = $year;
		$data['items'] = $this->_items($year);
		$data['types'] = self::$types;
		$data['can_manage'] = $this->can_manage;
		// Today and the next few items (from today on), whatever year is selected.
		$today = date('Y-m-d');
		$soon = array_merge($this->_items((int)date('Y')), $this->_items((int)date('Y') + 1));
		$data['today_items'] = array_values(array_filter($soon, function ($it) use ($today) {
			$s = substr($it['start'], 0, 10);
			$e = !empty($it['end']) ? substr($it['end'], 0, 10) : $s;
			return $s <= $today && $e >= $today;
		}));
		$data['next_items'] = array_slice(array_values(array_filter($soon, function ($it) use ($today) {
			return substr($it['start'], 0, 10) > $today;
		})), 0, 3);
		$this->load->view('manager/events/index', $data);
	}

	public function manage()
	{
		$year = $this->_year();
		$data = array();
		$data['info'] = array('title' => 'Manage Events & Festivals', 'page_heading' => 'Manage Events & Festivals');
		$data['year'] = $year;
		$data['items'] = $this->_items($year);
		$data['types'] = self::$types;
		$data['users'] = $this->db->select('id, name')->where('status', 1)->order_by('name', 'ASC')->get('ci_user')->result_array();
		$this->load->view('manager/events/manage', $data);
	}

	private function _date($v)
	{
		$v = trim((string)$v);
		return (preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) && strtotime($v) !== false) ? $v : '';
	}

	private function _back()
	{
		$y = (int)$this->input->post('year');
		redirect('manager/events/manage' . ($y > 0 ? '?year=' . $y : ''));
	}

	public function save_event()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') redirect('manager/events/manage');
		$id = (int)$this->input->post('id');
		$title = trim((string)$this->input->post('title'));
		$type = (string)$this->input->post('event_type');
		$from = $this->_date($this->input->post('start_date'));
		$to = $this->_date($this->input->post('end_date'));
		$time = trim((string)$this->input->post('start_time'));
		$end_time = trim((string)$this->input->post('end_time'));
		if ($title === '' || $from === '' || !isset(self::$types[$type])) {
			$this->session->set_flashdata('error_message', 'Please enter a title, a type and a start date.');
			$this->_back();
		}
		if ($to !== '' && $to < $from) $to = $from;
		$timed = (bool)preg_match('/^\d{2}:\d{2}$/', $time);
		$payload = array(
			'title' => $title,
			'description' => trim((string)$this->input->post('description')),
			'event_type' => $type,
			'visibility' => 'public',
			'assigned_user_id' => 0,
			'start_date' => $from . ' ' . ($timed ? $time . ':00' : '00:00:00'),
			'end_date' => ($to !== '' && $to !== $from) ? $to . ' 23:59:59' : (($timed && preg_match('/^\d{2}:\d{2}$/', $end_time) && $end_time > $time) ? $from . ' ' . $end_time . ':00' : null),
			'all_day' => $timed ? 0 : 1,
			'location' => trim((string)$this->input->post('location')),
			'status' => 'active',
		);
		if ($id > 0) {
			$this->db->where('id', $id)->update('ci_events', $payload);
			$this->session->set_flashdata('message', 'Event updated.');
		} else {
			$payload['created_by'] = $this->cur_uid;
			$payload['created_at'] = date('Y-m-d H:i:s');
			$this->db->insert('ci_events', $payload);
			$this->session->set_flashdata('message', 'Event published for everyone.');
		}
		$this->_back();
	}

	public function delete_event($id = 0)
	{
		$id = (int)$id;
		if ($this->input->server('REQUEST_METHOD') === 'POST' && $id > 0) {
			$this->db->where('id', $id)->update('ci_events', array('status' => 'cancelled'));
			$this->session->set_flashdata('message', 'Event removed.');
		}
		$this->_back();
	}

	public function save_award()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') redirect('manager/events/manage');
		$id = (int)$this->input->post('id');
		$user_id = (int)$this->input->post('user_id');
		$title = trim((string)$this->input->post('title'));
		$date = $this->_date($this->input->post('award_date'));
		if ($user_id <= 0 || $title === '' || $date === '') {
			$this->session->set_flashdata('error_message', 'Please choose the employee and enter the award title and date.');
			$this->_back();
		}
		$payload = array(
			'user_id' => $user_id,
			'title' => $title,
			'category' => trim((string)$this->input->post('category')) ?: 'Recognition',
			'award_date' => $date,
			'description' => trim((string)$this->input->post('description')),
		);
		if ($id > 0) {
			$this->db->where('id', $id)->update('ci_awards', $payload);
			$this->session->set_flashdata('message', 'Award updated.');
		} else {
			$payload['reward_points'] = 0;
			$payload['created_by'] = $this->cur_uid;
			$payload['created_at'] = date('Y-m-d H:i:s');
			$this->db->insert('ci_awards', $payload);
			$this->session->set_flashdata('message', 'Award published for everyone.');
		}
		$this->_back();
	}

	public function delete_award($id = 0)
	{
		$id = (int)$id;
		if ($this->input->server('REQUEST_METHOD') === 'POST' && $id > 0) {
			$this->db->where('id', $id)->delete('ci_awards');
			$this->session->set_flashdata('message', 'Award removed.');
		}
		$this->_back();
	}
}
