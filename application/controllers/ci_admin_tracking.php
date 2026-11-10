<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * System > Activity: Record Tracking (what happened to each record, and who did it)
 * and User Activity (when each person was working or idle). Admins, or roles given these rights.
 */
class Ci_admin_tracking extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library(array('Session'));
		$this->load->helper(array('form', 'url', 'text', 'date'));
		$this->load->model(array('ci_admin_model', 'comman_model', 'tracking_model'));
		header('Cache-Control: no-store, no-cache, must-revalidate');
	}

	private function need($right)
	{
		if ((int)$this->session->userdata('manager_id') <= 0) { redirect('/manager/login'); exit; }
		if (!crm_can($right)) { redirect('manager/dashboard'); exit; }
	}

	private function day($v, $def)
	{
		$v = trim((string)$v);
		return preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) && strtotime($v) ? $v : $def;
	}

	/** POST from every page, once a minute while the person is using the CRM. */
	public function ping()
	{
		$uid = (int)$this->session->userdata('manager_id');
		header('Content-Type: application/json');
		if ($uid <= 0 || $this->input->server('REQUEST_METHOD') !== 'POST') { echo '{"ok":false}'; return; }
		$this->tracking_model->ping($uid, (string)$this->input->post('page'));
		echo '{"ok":true}';
	}

	public function records()
	{
		$this->need('record_tracking');
		$f = array(
			'type' => (string)$this->input->get('type'),
			'dept' => (int)$this->input->get('dept'),
			'user' => (int)$this->input->get('user'),
			'action' => in_array($this->input->get('action'), array('create', 'update', 'delete'), true) ? $this->input->get('action') : '',
			'q' => trim((string)$this->input->get('q')),
			'from' => $this->day($this->input->get('from'), date('Y-m-d', strtotime('-6 days'))),
			'to' => $this->day($this->input->get('to'), date('Y-m-d')),
		);
		if ($f['from'] > $f['to']) list($f['from'], $f['to']) = array($f['to'], $f['from']);
		$page = max(1, (int)$this->input->get('page'));
		list($feed, $more) = $this->tracking_model->change_feed($f, $page);
		$data = array(
			'info' => array('title' => 'Record Tracking', 'page_heading' => 'Record Tracking'),
			'f' => $f, 'page' => $page, 'more' => $more, 'feed' => $feed,
			'kpi' => $this->tracking_model->change_kpis($f),
			'departments' => $this->tracking_model->departments(),
			'people' => $this->tracking_model->staff($f['dept'], '', false),
			'types' => Tracking_model::$types,
			'since' => $this->tracking_model->since(),
		);
		$this->load->view('manager/system/record_tracking', $data);
	}

	public function record($type = '', $id = 0)
	{
		$this->need('record_tracking');
		$rec = $this->tracking_model->record((string)$type, (int)$id);
		if (!$rec) { redirect('manager/system/record-tracking'); return; }
		$data = array(
			'info' => array('title' => 'Record history', 'page_heading' => 'Record history'),
			'rec' => $rec, 'types' => Tracking_model::$types,
		);
		$this->load->view('manager/system/record_history', $data);
	}

	public function users()
	{
		$this->need('user_activity');
		$from = $this->day($this->input->get('from'), date('Y-m-d'));
		$to = $this->day($this->input->get('to'), $from);
		if ($from > $to) list($from, $to) = array($to, $from);
		if ((strtotime($to) - strtotime($from)) > 31 * 86400) $from = date('Y-m-d', strtotime($to . ' -31 days'));
		$dept = (int)$this->input->get('dept');
		$q = trim((string)$this->input->get('q'));
		$data = array(
			'info' => array('title' => 'User Activity', 'page_heading' => 'User Activity'),
			'from' => $from, 'to' => $to, 'dept' => $dept, 'q' => $q,
			'rows' => $this->tracking_model->activity($from, $to, $dept, $q),
			'departments' => $this->tracking_model->departments(),
			'tm' => $this->tracking_model,
		);
		$this->load->view('manager/system/user_activity', $data);
	}

	public function user($uid = 0)
	{
		$this->need('user_activity');
		$day = $this->day($this->input->get('date'), date('Y-m-d'));
		$d = $this->tracking_model->user_day((int)$uid, $day);
		if (!$d) { redirect('manager/system/user-activity'); return; }
		$data = array(
			'info' => array('title' => 'User Activity', 'page_heading' => 'User Activity'),
			'day' => $day, 'd' => $d, 'tm' => $this->tracking_model,
		);
		$this->load->view('manager/system/user_day', $data);
	}
}
