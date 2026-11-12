<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Database > Lead Finder: leads sent from the Lead Finder app, checked against the database and
 * sorted into New / Possible duplicates / Duplicates, for the sender to approve.
 * System > Integrations > Lead Finder link: the link key and the duplicate rule.
 * See Lead_finder_model.
 */
class Ci_admin_lead_finder extends CI_Controller
{
	private $uid = 0;

	public function __construct()
	{
		parent::__construct();
		$this->load->library(array('Session'));
		$this->load->helper(array('form', 'url', 'text'));
		$this->load->model(array('ci_admin_model', 'comman_model', 'lead_finder_model'));
		require_once APPPATH . 'helpers/crm_permission_helper.php';
		$this->uid = (int)$this->session->userdata('manager_id');
		if ($this->uid <= 0) {
			// Come back to the page that was asked for after signing in.
			$this->session->set_userdata('lf_after_login', uri_string());
			redirect('manager/login');
			exit;
		}
		$this->lead_finder_model->ensure();
	}

	private function back($ok, $msg, $to = 'manager/leads/lead-finder')
	{
		if ($msg !== '') $this->session->set_flashdata($ok ? 'message' : 'error_message', $msg);
		redirect($to);
		exit;
	}

	private function need_right()
	{
		if (!crm_can('lead_finder')) $this->back(false, 'You do not have access to Lead Finder. Ask an administrator for the "Lead Finder" right.', 'manager/dashboard');
	}

	/** The sender of a batch, or someone who sees all company data, may open it. */
	private function own_batch($id)
	{
		$b = $this->lead_finder_model->batch($id);
		if (!$b) $this->back(false, 'That Lead Finder batch does not exist.');
		if ((int)$b['sent_by'] !== $this->uid && !crm_is_full_admin() && !crm_can('all_data')) {
			$this->ci_admin_model->log_admin_action('lead_finder', 'Tried to open Lead Finder batch LF-' . (int)$b['id'] . ' sent by someone else', 'FAILED');
			$this->back(false, 'This Lead Finder batch was sent by someone else. Only they (or an administrator) can review it.');
		}
		return $b;
	}

	public function index()
	{
		$this->need_right();
		$all = crm_is_full_admin() || crm_can('all_data');
		$data = array(
			'info' => array('title' => 'Lead Finder', 'page_heading' => 'Lead Finder'),
			'batches' => $this->lead_finder_model->batches($this->uid, $all),
			'all' => $all,
			'settings' => $this->lead_finder_model->settings(),
			'can_link' => crm_can('integrations'),
		);
		$this->load->view('manager/leads/lead_finder_list', $data);
	}

	public function review($id = 0)
	{
		$this->need_right();
		$b = $this->own_batch($id);
		$everyone = crm_is_full_admin() || crm_can('all_data');
		$data = array(
			'info' => array('title' => 'Lead Finder: check before adding', 'page_heading' => 'Lead Finder'),
			'batch' => $b,
			'rows' => $this->lead_finder_model->batch_rows($b['id']),
			'can_force' => crm_can('lead_finder', 'write'),
			'assignees' => $this->lead_finder_model->assignees($this->uid, $everyone),
			'settings' => $this->lead_finder_model->settings(),
			'labels' => Lead_finder_model::FIELD_LABELS,
			'expired' => $b['state'] === 'open' && strtotime($b['created_at']) < time() - Lead_finder_model::EXPIRE_DAYS * 86400,
		);
		$this->load->view('manager/leads/lead_finder_review', $data);
	}

	public function confirm($id = 0)
	{
		$this->need_right();
		if ($this->input->server('REQUEST_METHOD') !== 'POST') $this->back(false, '');
		$b = $this->own_batch($id);
		$to = 'manager/leads/lead-finder/review/' . (int)$b['id'];
		if ($b['state'] !== 'open') $this->back(false, 'This batch was already ' . ($b['state'] === 'done' ? 'added' : $b['state']) . '.', $to);
		if (strtotime($b['created_at']) < time() - Lead_finder_model::EXPIRE_DAYS * 86400) $this->back(false, 'This batch is more than ' . Lead_finder_model::EXPIRE_DAYS . ' days old. Send the leads again from Lead Finder so they are checked against today\'s database.', $to);

		$assign = (int)$this->input->post('assign_to');
		if ($assign > 0) {
			$okIds = array();
			foreach ($this->lead_finder_model->assignees($this->uid, crm_is_full_admin() || crm_can('all_data')) as $a) $okIds[(int)$a['id']] = true;
			if (!isset($okIds[$assign])) $this->back(false, 'You cannot give leads to that person (not active, or not in your team).', $to);
		}
		$choices = $this->input->post('choice');
		$reasons = $this->input->post('reason');
		list($ok, $res) = $this->lead_finder_model->confirm($b, is_array($choices) ? $choices : array(), is_array($reasons) ? $reasons : array(), $assign, $this->uid, crm_can('lead_finder', 'write'));
		if (!$ok) $this->back(false, $res, $to);
		$parts = array();
		if ($res['add']) $parts[] = number_format($res['add']) . ' added';
		if ($res['force']) $parts[] = number_format($res['force']) . ' duplicate' . ($res['force'] === 1 ? '' : 's') . ' added on purpose';
		if ($res['merge']) $parts[] = number_format($res['merge']) . ' existing record' . ($res['merge'] === 1 ? '' : 's') . ' filled in';
		$parts[] = number_format($res['skip']) . ' skipped';
		$this->back(true, 'Lead Finder batch LF-' . (int)$b['id'] . ': ' . implode(', ', $parts) . '.' . ($res['import_id'] ? ' Undo is on Database > Import if needed.' : ''), $to);
	}

	public function cancel($id = 0)
	{
		$this->need_right();
		if ($this->input->server('REQUEST_METHOD') !== 'POST') $this->back(false, '');
		$b = $this->own_batch($id);
		if ($b['state'] !== 'open') $this->back(false, 'This batch is already closed.');
		$this->lead_finder_model->cancel($b, $this->uid);
		$this->ci_admin_model->log_admin_action('lead_finder', 'Discarded Lead Finder batch LF-' . (int)$b['id'] . ' (' . (int)$b['total'] . ' leads, nothing added)', 'SUCCESS');
		$this->back(true, 'Batch LF-' . (int)$b['id'] . ' discarded. Nothing was added.');
	}

	/* ---------------- System > Integrations > Lead Finder link ---------------- */

	public function settings()
	{
		if (!crm_can('integrations')) $this->back(false, 'You do not have access to Integrations.', 'manager/dashboard');
		$data = array(
			'info' => array('title' => 'Lead Finder link', 'page_heading' => 'Lead Finder link'),
			'row' => $this->lead_finder_model->row(),
			'settings' => $this->lead_finder_model->settings(),
			'can_edit' => crm_can('integrations', 'write'),
			'crm_url' => rtrim(base_url(), '/'),
			'users_with_right' => $this->users_with_right(),
		);
		$this->load->view('manager/system/lead_finder_link', $data);
	}

	/** Active staff who have the Lead Finder right (shown so admins see who can send leads). */
	private function users_with_right()
	{
		$out = array();
		$rows = $this->db->query('SELECT id, name, email FROM ci_user WHERE status = 1 ORDER BY name')->result_array();
		foreach ($rows as $u) if (crm_can('lead_finder', 'read', (int)$u['id'])) $out[] = $u + array('full' => crm_can('lead_finder', 'write', (int)$u['id']));
		return $out;
	}

	private function need_link_write()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') $this->back(false, '', 'manager/system/lead-finder');
		if (!crm_can('integrations', 'write')) $this->back(false, 'You need Full access to Third-Party API to change the Lead Finder link.', 'manager/system/lead-finder');
	}

	public function save()
	{
		$this->need_link_write();
		$on = (int)$this->input->post('status') === 1;
		$this->lead_finder_model->save_settings($on, (int)$this->input->post('threshold'), (int)$this->input->post('max_batch'));
		$s = $this->lead_finder_model->settings();
		$this->ci_admin_model->log_admin_action('lead_finder', 'Lead Finder link switched ' . ($on ? 'on' : 'off') . '; duplicate when ' . $s['threshold'] . '+ fields match; up to ' . $s['max_batch'] . ' leads per batch', 'SUCCESS');
		$this->back(true, 'Lead Finder link saved.', 'manager/system/lead-finder');
	}

	public function regenerate()
	{
		$this->need_link_write();
		$this->lead_finder_model->regenerate();
		$this->ci_admin_model->log_admin_action('lead_finder', 'Made a new Lead Finder link key (the old one stops working)', 'SUCCESS');
		$this->back(true, 'New link key made. Paste it into Lead Finder: the old key no longer works.', 'manager/system/lead-finder');
	}
}
