<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ci_admin_live extends CI_Controller
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
	}

	public function if_not_login()
	{
		$manager_id = $this->session->userdata('manager_id');
		if (empty($manager_id)) {
			redirect('/manager/login');
		}
	}

	public function index()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'LIVE Commercial Dashboard',
			'page_heading' => 'LIVE Commercial Operations'
		);

		$manager_id = (int)$this->session->userdata('manager_id');
		$role = (int)$this->session->userdata('manager_role');
		$is_admin = ($role === 1 || $manager_id === 1);
		$data['is_admin'] = $is_admin;

		$team_list = $this->ci_admin_model->fetchTeamList($manager_id, '');
		$data['has_team'] = (!empty($team_list) || $is_admin);

		$this->load->view('manager/live/index', $data);
	}

	public function ajax_get_data()
	{
		$manager_id = (int)$this->session->userdata('manager_id');
		$role = (int)$this->session->userdata('manager_role');
		$is_admin = ($role === 1 || $manager_id === 1);

		$timeframe = trim((string)$this->input->get_post('timeframe'));
		if (!in_array($timeframe, array('today', 'last7', 'month', 'quarter'))) {
			$timeframe = 'today';
		}

		$scope = trim((string)$this->input->get_post('scope'));
		if (!in_array($scope, array('my', 'team'))) {
			$scope = 'my';
		}

		$today = date('Y-m-d');
		if ($timeframe === 'today') {
			$d1 = $today . ' 00:00:00';
			$d2 = $today . ' 23:59:59';
			$date_only_1 = $today;
			$date_only_2 = $today;
		} elseif ($timeframe === 'last7') {
			$date_only_1 = date('Y-m-d', strtotime('-7 days'));
			$date_only_2 = $today;
			$d1 = $date_only_1 . ' 00:00:00';
			$d2 = $today . ' 23:59:59';
		} elseif ($timeframe === 'month') {
			$date_only_1 = date('Y-m-01');
			$date_only_2 = date('Y-m-t');
			$d1 = $date_only_1 . ' 00:00:00';
			$d2 = $date_only_2 . ' 23:59:59';
		} else {
			$m = (int)date('n');
			$q = (int)ceil($m / 3);
			$start_m = sprintf('%02d', ($q - 1) * 3 + 1);
			$end_m = sprintf('%02d', $q * 3);
			$date_only_1 = date('Y-') . $start_m . '-01';
			$date_only_2 = date('Y-') . $end_m . '-' . date('t', strtotime(date('Y-') . $end_m . '-01'));
			$d1 = $date_only_1 . ' 00:00:00';
			$d2 = $date_only_2 . ' 23:59:59';
		}

		$user_ids = array($manager_id);
		if ($scope === 'team') {
			if ($is_admin) {
				$all_users = $this->db->select('id')->where('status', 1)->get('ci_user')->result_array();
				if (!empty($all_users)) {
					$user_ids = array_map(function($u) { return (int)$u['id']; }, $all_users);
				}
			} else {
				$team_list = $this->ci_admin_model->fetchTeamList($manager_id, '');
				if (!empty($team_list)) {
					$user_ids = array_unique(array_merge(array($manager_id), array_map(function($u) { return (int)$u['id']; }, $team_list)));
				}
			}
		}

		$user_in = implode(',', array_map('intval', $user_ids));

		$this->db->from('ci_call_logs');
		$this->db->where("user_id IN ({$user_in})", null, false);
		$this->db->where('created_at >=', $d1);
		$this->db->where('created_at <=', $d2);
		$calls_total = $this->db->count_all_results();

		$this->db->from('ci_call_logs');
		$this->db->where("user_id IN ({$user_in})", null, false);
		$this->db->where('created_at >=', $d1);
		$this->db->where('created_at <=', $d2);
		$this->db->group_start();
		$this->db->where('duration >', 0);
		$this->db->or_where('status', 'connected');
		$this->db->or_where('status', 'completed');
		$this->db->group_end();
		$calls_connected = $this->db->count_all_results();

		$this->db->from('ci_call_logs');
		$this->db->where("user_id IN ({$user_in})", null, false);
		$this->db->where('created_at >=', $d1);
		$this->db->where('created_at <=', $d2);
		$this->db->group_start();
		$this->db->where('status', 'calling');
		$this->db->or_where('status', 'ringing');
		$this->db->or_where('status', 'in_process');
		$this->db->group_end();
		$calls_in_process = $this->db->count_all_results();

		$calls_other = max(0, $calls_total - ($calls_connected + $calls_in_process));

		$latest_calls = $this->db->select('c.*, u.name as agent_name')
			->from('ci_call_logs c')
			->join('ci_user u', 'c.user_id = u.id', 'left')
			->where("c.user_id IN ({$user_in})", null, false)
			->order_by('c.id', 'DESC')
			->limit(6)
			->get()
			->result_array();

		$this->db->from('ci_leads');
		$this->db->where("(created_by IN ({$user_in}) OR lead_assign IN ({$user_in}))", null, false);
		$this->db->where('created >=', $date_only_1);
		$this->db->where('created <=', $date_only_2);
		$leads_total = $this->db->count_all_results();

		$this->db->from('ci_leads');
		$this->db->where("(created_by IN ({$user_in}) OR lead_assign IN ({$user_in}))", null, false);
		$this->db->where('created >=', $date_only_1);
		$this->db->where('created <=', $date_only_2);
		$this->db->where('lead_stage', 'Lead');
		$leads_qualified = $this->db->count_all_results();

		$leads_database = max(0, $leads_total - $leads_qualified);

		$status_counts = array();
		$status_rows = $this->db->select('s.status, COUNT(l.id) as cnt')
			->from('ci_status s')
			->join('ci_leads l', "l.status = s.id AND (l.created_by IN ({$user_in}) OR l.lead_assign IN ({$user_in})) AND l.created >= '{$date_only_1}' AND l.created <= '{$date_only_2}'", 'left')
			->group_by('s.id')
			->order_by('cnt', 'DESC')
			->limit(6)
			->get()
			->result_array();
		if (!empty($status_rows)) {
			foreach ($status_rows as $sr) {
				$status_counts[] = array('name' => $sr['status'], 'count' => (int)$sr['cnt']);
			}
		}

		$latest_leads = $this->db->select('id, lead_id, customer, poc, contact1, status, lead_stage, created')
			->from('ci_leads')
			->where("(created_by IN ({$user_in}) OR lead_assign IN ({$user_in}))", null, false)
			->order_by('id', 'DESC')
			->limit(5)
			->get()
			->result_array();

		$this->db->from('ci_orders');
		$this->db->where("(created_by IN ({$user_in}) OR assigned_to IN ({$user_in}))", null, false);
		$this->db->where('created_at >=', $d1);
		$this->db->where('created_at <=', $d2);
		$orders_total = $this->db->count_all_results();

		$this->db->from('ci_orders');
		$this->db->where("(created_by IN ({$user_in}) OR assigned_to IN ({$user_in}))", null, false);
		$this->db->where('created_at >=', $d1);
		$this->db->where('created_at <=', $d2);
		$this->db->group_start();
		$this->db->where('status', 'Pending');
		$this->db->or_where('status', 'Pending Process');
		$this->db->or_where('status', 'Pending Sales Approval');
		$this->db->or_where('order_request_status', 'pending');
		$this->db->or_where('order_request_status', 'pending_sales_approval');
		$this->db->group_end();
		$orders_pending = $this->db->count_all_results();

		$this->db->from('ci_orders');
		$this->db->where("(created_by IN ({$user_in}) OR assigned_to IN ({$user_in}))", null, false);
		$this->db->where('created_at >=', $d1);
		$this->db->where('created_at <=', $d2);
		$this->db->group_start();
		$this->db->where('status', 'Completed');
		$this->db->or_where('status', 'Completed Orders');
		$this->db->group_end();
		$orders_completed = $this->db->count_all_results();

		$this->db->from('ci_orders');
		$this->db->where("(created_by IN ({$user_in}) OR assigned_to IN ({$user_in}))", null, false);
		$this->db->where('created_at >=', $d1);
		$this->db->where('created_at <=', $d2);
		$this->db->group_start();
		$this->db->where('status', 'Sales');
		$this->db->or_where('status', 'Closed / Sold');
		$this->db->or_where('sales_ref_no IS NOT NULL', null, false);
		$this->db->group_end();
		$sales_count = $this->db->count_all_results();

		$sales_rev_row = $this->db->select('SUM(CAST(total_amount AS DECIMAL(12,2))) as rev_sum')
			->from('ci_orders')
			->where("(created_by IN ({$user_in}) OR assigned_to IN ({$user_in}))", null, false)
			->where('created_at >=', $d1)
			->where('created_at <=', $d2)
			->group_start()
			->where('status', 'Sales')
			->or_where('status', 'Closed / Sold')
			->or_where('sales_ref_no IS NOT NULL', null, false)
			->group_end()
			->get()
			->row_array();
		$sales_revenue = !empty($sales_rev_row['rev_sum']) ? (float)$sales_rev_row['rev_sum'] : 0.00;

		$this->db->from('ci_followup');
		$this->db->where("user_id IN ({$user_in})", null, false);
		$this->db->where('followup_date >=', $date_only_1);
		$this->db->where('followup_date <=', $date_only_2);
		$followups_total = $this->db->count_all_results();

		$this->db->from('ci_followup');
		$this->db->where("user_id IN ({$user_in})", null, false);
		$this->db->where('followup_date <', $today);
		$this->db->where("(is_done = 0 OR is_done IS NULL)", null, false);
		$followups_missed = $this->db->count_all_results();

		$this->db->from('ci_followup');
		$this->db->where("user_id IN ({$user_in})", null, false);
		$this->db->where('followup_date', $today);
		$followups_today = $this->db->count_all_results();

		$this->db->from('ci_meetings');
		$this->db->where("(created_by IN ({$user_in}) OR a1 IN ({$user_in}) OR a2 IN ({$user_in}))", null, false);
		$this->db->where('m_date >=', $date_only_1);
		$this->db->where('m_date <=', $date_only_2);
		$meetings_total = $this->db->count_all_results();

		$this->db->from('ci_tickets');
		$this->db->where("(raised_by IN ({$user_in}) OR assigned_user IN ({$user_in}))", null, false);
		$this->db->where('created >=', $d1);
		$this->db->where('created <=', $d2);
		$tickets_total = $this->db->count_all_results();

		$this->db->from('ci_tickets');
		$this->db->where("(raised_by IN ({$user_in}) OR assigned_user IN ({$user_in}))", null, false);
		$this->db->where('status', 'Open');
		$tickets_open = $this->db->count_all_results();

		$this->db->from('ci_tickets');
		$this->db->where("(raised_by IN ({$user_in}) OR assigned_user IN ({$user_in}))", null, false);
		$this->db->where('status', 'Resolved');
		$tickets_resolved = $this->db->count_all_results();

		$articles_total = $this->db->count_all('ci_verticals');

		$response = array(
			'status' => 'success',
			'timestamp' => date('H:i:s'),
			'timeframe' => $timeframe,
			'scope' => $scope,
			'calls' => array(
				'total' => $calls_total,
				'connected' => $calls_connected,
				'in_process' => $calls_in_process,
				'other' => $calls_other,
				'latest' => $latest_calls
			),
			'leads' => array(
				'total' => $leads_total,
				'qualified' => $leads_qualified,
				'database' => $leads_database,
				'statuses' => $status_counts,
				'latest' => $latest_leads
			),
			'orders' => array(
				'total' => $orders_total,
				'pending' => $orders_pending,
				'completed' => $orders_completed,
				'sales' => $sales_count
			),
			'sales' => array(
				'count' => $sales_count,
				'revenue' => $sales_revenue,
				'revenue_formatted' => 'AED ' . number_format($sales_revenue, 2)
			),
			'followups' => array(
				'total' => $followups_total,
				'missed' => $followups_missed,
				'today' => $followups_today
			),
			'meetings' => array(
				'total' => $meetings_total
			),
			'tickets' => array(
				'total' => $tickets_total,
				'open' => $tickets_open,
				'resolved' => $tickets_resolved
			),
			'articles' => array(
				'total' => $articles_total
			)
		);

		header('Content-Type: application/json');
		echo json_encode($response);
		exit;
	}
}
