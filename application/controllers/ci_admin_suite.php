<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * System > Integrations > HR link: switch the link to HR & Payroll on or off, approve people HR added,
 * see leavers and leave, and send approved order commissions to HR payroll. See Suite_link_model.
 */
class Ci_admin_suite extends CI_Controller
{
	private $uid = 0;

	public function __construct()
	{
		parent::__construct();
		$this->load->library(array('Session'));
		$this->load->helper(array('form', 'url', 'text'));
		$this->load->model(array('ci_admin_model', 'comman_model', 'suite_link_model'));
		require_once APPPATH . 'helpers/crm_permission_helper.php';
		$this->uid = (int)$this->session->userdata('manager_id');
		if ($this->uid <= 0) { redirect('manager/login'); exit; }
	}

	private function back($ok, $msg, $to = 'manager/system/hr-link')
	{
		if ($msg !== '') $this->session->set_flashdata($ok ? 'message' : 'error_message', $msg);
		redirect($to);
		exit;
	}

	private function need_write()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') $this->back(false, '');
		if (!crm_can('integrations', 'write')) $this->back(false, 'You need Full access to Third-Party API to change the HR link.');
	}

	public function index()
	{
		if (!crm_can('integrations')) $this->back(false, 'You do not have access to Integrations.', 'manager/dashboard');
		$this->suite_link_model->ensure();
		$data = $this->suite_link_model->lists();
		$data['info'] = array('title' => 'HR link', 'page_heading' => 'HR link');
		$data['row'] = $this->suite_link_model->row();
		$data['hr_url'] = $this->suite_link_model->hr_url();
		$data['missing'] = $this->suite_link_model->missing();
		$data['trust'] = $this->suite_link_model->trust();
		$data['can_edit'] = crm_can('integrations', 'write');
		$data['is_admin'] = crm_is_full_admin();
		$data['managers'] = $this->db->query('SELECT id, name FROM ci_user WHERE status = 1 AND admin_status = 1 AND id NOT IN (SELECT user_id FROM ci_user_exit) ORDER BY name')->result_array();
		$data['events_url'] = base_url() . 'api/suite/app-events';
		$this->load->view('manager/system/hr_link', $data);
	}

	public function save()
	{
		$this->need_write();
		$err = $this->suite_link_model->save_settings((int)$this->input->post('status') === 1, (string)$this->input->post('endpoint'));
		$this->back($err === '', $err !== '' ? $err : 'HR link settings saved.');
	}

	public function ping()
	{
		$this->need_write();
		$r = $this->suite_link_model->ping();
		$this->back($r[0], $r[0] ? 'HR answered: the link works.' : $r[3]);
	}

	public function load()
	{
		$this->need_write();
		if (!$this->suite_link_model->is_on()) $this->back(false, 'Switch the HR link on first.');
		$r = $this->suite_link_model->load_directory();
		$this->back($r[0], $r[1]);
	}

	public function approve()
	{
		$this->need_write();
		if (!crm_is_full_admin()) $this->back(false, 'Only an administrator can give someone a CRM login.');
		$r = $this->suite_link_model->approve((string)$this->input->post('hr_id'), (int)$this->input->post('manager'), $this->uid);
		$this->back($r[0], $r[1]);
	}

	public function dismiss()
	{
		$this->need_write();
		$r = $this->suite_link_model->dismiss((string)$this->input->post('hr_id'), $this->uid);
		$this->back($r[0], $r[1]);
	}

	/** Order page: approve and send, or withdraw, the commission (administrators only). */
	public function commission($order_id = 0)
	{
		$order_id = (int)$order_id;
		$to = 'manager/orders/view/' . $order_id;
		if ($this->input->server('REQUEST_METHOD') !== 'POST' || $order_id <= 0) $this->back(false, '', $to);
		if (!crm_is_full_admin()) $this->back(false, 'Only an administrator can approve a commission.', $to);
		$order = $this->db->where('id', $order_id)->get('ci_orders')->row_array();
		if (!$order) $this->back(false, 'Order not found.', 'manager/orders');
		if ($this->input->post('do') === 'withdraw') {
			$r = $this->suite_link_model->withdraw_commission($order_id, $this->uid, 'withdrawn by an admin');
			$this->back($r[0], $r[1] !== '' ? $r[1] : 'Nothing to withdraw.', $to);
		}
		$r = $this->suite_link_model->send_commission($order, (int)$this->input->post('user_id'), (string)$this->input->post('month'), $this->input->post('amount'), $this->uid);
		$this->back($r[0], $r[1], $to);
	}
}
