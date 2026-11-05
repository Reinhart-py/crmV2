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

		$role = (int)$this->session->userdata('manager_role');
		$uid = (int)$this->session->userdata('manager_id');
		if ($role !== 1 && $uid !== 1 && !crm_can('live', 'read')) {
			if (strpos($this->router->fetch_method(), 'ajax_') === 0) {
				header('HTTP/1.1 403 Forbidden');
				header('Content-Type: application/json');
				echo json_encode(array('status' => false, 'message' => 'No access to Live.'));
				exit;
			}
			$this->session->set_flashdata('error_message', 'You do not have access to Live. Ask your administrator if you need it.');
			redirect(crm_landing_url());
			exit;
		}
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
			'title' => 'Live',
			'page_heading' => 'Live'
		);

		$manager_id = (int)$this->session->userdata('manager_id');
		$role = (int)$this->session->userdata('manager_role');
		$is_admin = ($role === 1 || $manager_id === 1);
		$data['is_admin'] = $is_admin;

		$user_row = $this->db->select('id, name, office, department')->where('id', $manager_id)->get('ci_user')->row_array();
		$data['current_user'] = $user_row;
		$data['user_office'] = !empty($user_row['office']) ? $user_row['office'] : '';

		$team_list = $this->ci_admin_model->fetchTeamList($manager_id, '');
		$data['has_team'] = (!empty($team_list) || $is_admin);

		$data['my_stats'] = $this->get_stats_block(array($manager_id));
		$data['team_stats'] = $this->get_stats_block($is_admin ? array() : $team_list);
		$data['performers'] = $this->get_performers_block($is_admin ? array() : $team_list);

		$centers_query = $this->db->query("SELECT DISTINCT office FROM ci_user WHERE office IS NOT NULL AND office != '' ORDER BY office ASC");
		$centers_data = array();
		if ($centers_query && $centers_query->num_rows() > 0) {
			foreach ($centers_query->result_array() as $c) {
				$off = $c['office'];
				$u_ids = array();
				$u_rows = $this->db->query("SELECT id FROM ci_user WHERE office = " . $this->db->escape($off))->result_array();
				foreach ($u_rows as $ur) {
					$u_ids[] = (int)$ur['id'];
				}
				$centers_data[] = array(
					'name' => $off,
					'stats' => $this->get_stats_block($u_ids),
					'perf' => $this->get_performers_block($u_ids)
				);
			}
		}
		$data['centers_data'] = $centers_data;
		$data['company_stats'] = $this->get_stats_block(array());
		$data['company_perf'] = $this->get_performers_block(array());

		$this->load->view('manager/live/index', $data);
	}

	public function presentation()
	{
		$data = array();
		$data['info'] = array('title' => 'Live Presentation', 'page_heading' => 'Live');
		$this->load->view('manager/live/presentation', $data);
	}

	private function get_stats_block($user_ids = array())
	{
		$today = date('Y-m-d');
		$t_d1 = $today . ' 00:00:00';
		$t_d2 = $today . ' 23:59:59';

		$w_d1 = date('Y-m-d 00:00:00', strtotime('-7 days'));
		$w_d2 = $t_d2;

		$m_d1 = date('Y-m-01 00:00:00');
		$m_d2 = date('Y-m-t 23:59:59');

		$q_num = (int)ceil((int)date('n') / 3);
		$q_start_m = sprintf('%02d', ($q_num - 1) * 3 + 1);
		$q_end_m = sprintf('%02d', $q_num * 3);
		$q_d1 = date('Y-') . $q_start_m . '-01 00:00:00';
		$q_d2 = date('Y-') . $q_end_m . '-' . date('t', strtotime(date('Y-') . $q_end_m . '-01')) . ' 23:59:59';

		$date_today = $today;
		$date_7d = date('Y-m-d', strtotime('-7 days'));
		$date_month_start = date('Y-m-01');
		$date_month_end = date('Y-m-t');
		$date_q_start = date('Y-') . $q_start_m . '-01';
		$date_q_end = date('Y-') . $q_end_m . '-' . date('t', strtotime(date('Y-') . $q_end_m . '-01'));

		$has_filter = !empty($user_ids);
		$u_list = array_values(array_filter(array_map('intval', $user_ids)));
		if ($has_filter && empty($u_list)) {
			$u_list = array(0);
		}
		$u_str = implode(',', $u_list);

		$where_calls = $has_filter ? "WHERE user_id IN ({$u_str})" : "";
		$calls_row = $this->db->query("SELECT 
			COALESCE(SUM(CASE WHEN created_at >= '{$t_d1}' AND created_at <= '{$t_d2}' THEN 1 ELSE 0 END), 0) as today,
			COALESCE(SUM(CASE WHEN created_at >= '{$w_d1}' AND created_at <= '{$w_d2}' THEN 1 ELSE 0 END), 0) as seven_days,
			COALESCE(SUM(CASE WHEN created_at >= '{$m_d1}' AND created_at <= '{$m_d2}' THEN 1 ELSE 0 END), 0) as month,
			COALESCE(SUM(CASE WHEN created_at >= '{$q_d1}' AND created_at <= '{$q_d2}' THEN 1 ELSE 0 END), 0) as quarter
		FROM ci_call_logs {$where_calls}")->row_array();

		$where_leads = $has_filter ? "WHERE (FIND_IN_SET(a.created_by, '{$u_str}') > 0 OR (a.lead_assign > 0 AND FIND_IN_SET(a.lead_assign, '{$u_str}') > 0))" : "";
		$leads_row = $this->db->query("SELECT 
			COALESCE(SUM(CASE WHEN a.created >= '{$t_d1}' AND a.created <= '{$t_d2}' THEN 1 ELSE 0 END), 0) as today,
			COALESCE(SUM(CASE WHEN a.created >= '{$w_d1}' AND a.created <= '{$w_d2}' THEN 1 ELSE 0 END), 0) as seven_days,
			COALESCE(SUM(CASE WHEN a.created >= '{$m_d1}' AND a.created <= '{$m_d2}' THEN 1 ELSE 0 END), 0) as month,
			COALESCE(SUM(CASE WHEN a.created >= '{$q_d1}' AND a.created <= '{$q_d2}' THEN 1 ELSE 0 END), 0) as quarter
		FROM ci_leads a
		INNER JOIN ci_pipeline p ON p.lead_id = a.id AND " . crm_stage_lead_sql('a', 'p') . " {$where_leads}")->row_array();

		$where_orders = $has_filter ? "WHERE created_by IN ({$u_str})" : "";
		$orders_row = $this->db->query("SELECT 
			COALESCE(SUM(CASE WHEN created_at >= '{$t_d1}' AND created_at <= '{$t_d2}' THEN 1 ELSE 0 END), 0) as today,
			COALESCE(SUM(CASE WHEN created_at >= '{$w_d1}' AND created_at <= '{$w_d2}' THEN 1 ELSE 0 END), 0) as seven_days,
			COALESCE(SUM(CASE WHEN created_at >= '{$m_d1}' AND created_at <= '{$m_d2}' THEN 1 ELSE 0 END), 0) as month,
			COALESCE(SUM(CASE WHEN created_at >= '{$q_d1}' AND created_at <= '{$q_d2}' THEN 1 ELSE 0 END), 0) as quarter
		FROM ci_orders {$where_orders}")->row_array();

		$where_sales = $has_filter ? "AND created_by IN ({$u_str})" : "";
		$sales_row = $this->db->query("SELECT 
			COALESCE(SUM(CASE WHEN created_at >= '{$t_d1}' AND created_at <= '{$t_d2}' THEN 1 ELSE 0 END), 0) as cnt_today,
			COALESCE(SUM(CASE WHEN created_at >= '{$t_d1}' AND created_at <= '{$t_d2}' THEN CAST(COALESCE(NULLIF(pricing_amount, 0), NULLIF(mrr, 0), 0) AS DECIMAL(12,2)) ELSE 0 END), 0) as val_today,
			COALESCE(SUM(CASE WHEN created_at >= '{$w_d1}' AND created_at <= '{$w_d2}' THEN 1 ELSE 0 END), 0) as cnt_7d,
			COALESCE(SUM(CASE WHEN created_at >= '{$w_d1}' AND created_at <= '{$w_d2}' THEN CAST(COALESCE(NULLIF(pricing_amount, 0), NULLIF(mrr, 0), 0) AS DECIMAL(12,2)) ELSE 0 END), 0) as val_7d,
			COALESCE(SUM(CASE WHEN created_at >= '{$m_d1}' AND created_at <= '{$m_d2}' THEN 1 ELSE 0 END), 0) as cnt_month,
			COALESCE(SUM(CASE WHEN created_at >= '{$m_d1}' AND created_at <= '{$m_d2}' THEN CAST(COALESCE(NULLIF(pricing_amount, 0), NULLIF(mrr, 0), 0) AS DECIMAL(12,2)) ELSE 0 END), 0) as val_month,
			COALESCE(SUM(CASE WHEN created_at >= '{$q_d1}' AND created_at <= '{$q_d2}' THEN 1 ELSE 0 END), 0) as cnt_quarter,
			COALESCE(SUM(CASE WHEN created_at >= '{$q_d1}' AND created_at <= '{$q_d2}' THEN CAST(COALESCE(NULLIF(pricing_amount, 0), NULLIF(mrr, 0), 0) AS DECIMAL(12,2)) ELSE 0 END), 0) as val_quarter
		FROM ci_orders
		WHERE (status IN ('Sales', 'Closed / Sold', 'Completed') OR (sales_ref_no IS NOT NULL AND sales_ref_no != '')) {$where_sales}")->row_array();

		$format_sales = function($cnt, $val) {
			$c = (int)$cnt;
			$v = (float)$val;
			if ($c === 0 && $v <= 0) {
				return '0';
			}
			if ($v > 0) {
				return $c . ' (AED ' . number_format($v, 0) . ')';
			}
			return (string)$c;
		};

		$sales_display = array(
			'today' => $format_sales($sales_row['cnt_today'], $sales_row['val_today']),
			'seven_days' => $format_sales($sales_row['cnt_7d'], $sales_row['val_7d']),
			'month' => $format_sales($sales_row['cnt_month'], $sales_row['val_month']),
			'quarter' => $format_sales($sales_row['cnt_quarter'], $sales_row['val_quarter'])
		);

		$where_fu = $has_filter ? "WHERE user_id IN ({$u_str})" : "";
		$fu_row = $this->db->query("SELECT 
			COALESCE(SUM(CASE WHEN followup_date = '{$date_today}' THEN 1 ELSE 0 END), 0) as today,
			COALESCE(SUM(CASE WHEN followup_date >= '{$date_7d}' AND followup_date <= '{$date_today}' THEN 1 ELSE 0 END), 0) as seven_days,
			COALESCE(SUM(CASE WHEN followup_date >= '{$date_month_start}' AND followup_date <= '{$date_month_end}' THEN 1 ELSE 0 END), 0) as month,
			COALESCE(SUM(CASE WHEN followup_date >= '{$date_q_start}' AND followup_date <= '{$date_q_end}' THEN 1 ELSE 0 END), 0) as quarter
		FROM ci_followup {$where_fu}")->row_array();

		$where_meet = $has_filter ? "WHERE (created_by IN ({$u_str}) OR a1 IN ({$u_str}) OR a2 IN ({$u_str}))" : "";
		$meet_row = $this->db->query("SELECT 
			COALESCE(SUM(CASE WHEN m_date = '{$date_today}' THEN 1 ELSE 0 END), 0) as today,
			COALESCE(SUM(CASE WHEN m_date >= '{$date_7d}' AND m_date <= '{$date_today}' THEN 1 ELSE 0 END), 0) as seven_days,
			COALESCE(SUM(CASE WHEN m_date >= '{$date_month_start}' AND m_date <= '{$date_month_end}' THEN 1 ELSE 0 END), 0) as month,
			COALESCE(SUM(CASE WHEN m_date >= '{$date_q_start}' AND m_date <= '{$date_q_end}' THEN 1 ELSE 0 END), 0) as quarter
		FROM ci_meetings {$where_meet}")->row_array();

		$where_tick = $has_filter ? "AND (raised_by IN ({$u_str}) OR assigned_to IN ({$u_str}))" : "";
		$tick_row = $this->db->query("SELECT 
			COALESCE(SUM(CASE WHEN updated_at >= '{$t_d1}' AND updated_at <= '{$t_d2}' THEN 1 ELSE 0 END), 0) as today,
			COALESCE(SUM(CASE WHEN updated_at >= '{$w_d1}' AND updated_at <= '{$w_d2}' THEN 1 ELSE 0 END), 0) as seven_days,
			COALESCE(SUM(CASE WHEN updated_at >= '{$m_d1}' AND updated_at <= '{$m_d2}' THEN 1 ELSE 0 END), 0) as month,
			COALESCE(SUM(CASE WHEN updated_at >= '{$q_d1}' AND updated_at <= '{$q_d2}' THEN 1 ELSE 0 END), 0) as quarter
		FROM ci_tickets
		WHERE status = 'Resolved' {$where_tick}")->row_array();

		$where_vert = $has_filter ? "WHERE created_by IN ({$u_str})" : "";
		$vert_row = $this->db->query("SELECT 
			COALESCE(SUM(CASE WHEN created >= '{$t_d1}' AND created <= '{$t_d2}' THEN 1 ELSE 0 END), 0) as today,
			COALESCE(SUM(CASE WHEN created >= '{$w_d1}' AND created <= '{$w_d2}' THEN 1 ELSE 0 END), 0) as seven_days,
			COALESCE(SUM(CASE WHEN created >= '{$m_d1}' AND created <= '{$m_d2}' THEN 1 ELSE 0 END), 0) as month,
			COALESCE(SUM(CASE WHEN created >= '{$q_d1}' AND created <= '{$q_d2}' THEN 1 ELSE 0 END), 0) as quarter
		FROM ci_verticals {$where_vert}")->row_array();

		return array(
			array('metric' => 'Calls Made', 'today' => (int)$calls_row['today'], 'seven_days' => (int)$calls_row['seven_days'], 'month' => (int)$calls_row['month'], 'quarter' => (int)$calls_row['quarter']),
			array('metric' => 'Leads Generated', 'today' => (int)$leads_row['today'], 'seven_days' => (int)$leads_row['seven_days'], 'month' => (int)$leads_row['month'], 'quarter' => (int)$leads_row['quarter']),
			array('metric' => 'Orders Submitted', 'today' => (int)$orders_row['today'], 'seven_days' => (int)$orders_row['seven_days'], 'month' => (int)$orders_row['month'], 'quarter' => (int)$orders_row['quarter']),
			array('metric' => 'Sales Achieved', 'today' => $sales_display['today'], 'seven_days' => $sales_display['seven_days'], 'month' => $sales_display['month'], 'quarter' => $sales_display['quarter']),
			array('metric' => 'Follow up', 'today' => (int)$fu_row['today'], 'seven_days' => (int)$fu_row['seven_days'], 'month' => (int)$fu_row['month'], 'quarter' => (int)$fu_row['quarter']),
			array('metric' => 'Meetings', 'today' => (int)$meet_row['today'], 'seven_days' => (int)$meet_row['seven_days'], 'month' => (int)$meet_row['month'], 'quarter' => (int)$meet_row['quarter']),
			array('metric' => 'Tickets Resolved', 'today' => (int)$tick_row['today'], 'seven_days' => (int)$tick_row['seven_days'], 'month' => (int)$tick_row['month'], 'quarter' => (int)$tick_row['quarter']),
			array('metric' => 'Verticals Introduced', 'today' => (int)$vert_row['today'], 'seven_days' => (int)$vert_row['seven_days'], 'month' => (int)$vert_row['month'], 'quarter' => (int)$vert_row['quarter'])
		);
	}

	private function get_single_top_performer($start_dt, $end_dt, $user_ids = array())
	{
		$has_filter = !empty($user_ids);
		$u_list = array_values(array_filter(array_map('intval', $user_ids)));
		if ($has_filter && empty($u_list)) {
			$u_list = array(0);
		}
		$u_str = implode(',', $u_list);

		$where_user = $has_filter ? "AND o.created_by IN ({$u_str})" : "";
		$sql = "SELECT u.name, SUM(CAST(COALESCE(NULLIF(o.pricing_amount, 0), NULLIF(o.mrr, 0), 0) AS DECIMAL(12,2))) as total_rev
			FROM ci_orders o
			INNER JOIN ci_user u ON u.id = o.created_by
			WHERE o.created_at >= '{$start_dt}' AND o.created_at <= '{$end_dt}'
			AND (o.status IN ('Sales', 'Closed / Sold', 'Completed') OR (o.sales_ref_no IS NOT NULL AND o.sales_ref_no != ''))
			{$where_user}
			GROUP BY o.created_by
			ORDER BY total_rev DESC
			LIMIT 1";

		$row = $this->db->query($sql)->row_array();
		if (!empty($row) && !empty($row['name'])) {
			return $row['name'];
		}
		return '-';
	}

	private function get_performers_block($user_ids = array())
	{
		$cur_month_start = date('Y-m-01 00:00:00');
		$cur_month_end = date('Y-m-t 23:59:59');
		$last_month_start = date('Y-m-01 00:00:00', strtotime('-1 month'));
		$last_month_end = date('Y-m-t 23:59:59', strtotime('-1 month'));

		$q_num = (int)ceil((int)date('n') / 3);
		$q_start_m = sprintf('%02d', ($q_num - 1) * 3 + 1);
		$q_end_m = sprintf('%02d', $q_num * 3);
		$cur_q_start = date('Y-') . $q_start_m . '-01 00:00:00';
		$cur_q_end = date('Y-') . $q_end_m . '-' . date('t', strtotime(date('Y-') . $q_end_m . '-01')) . ' 23:59:59';

		$last_q_num = $q_num - 1;
		$last_q_year = (int)date('Y');
		if ($last_q_num < 1) {
			$last_q_num = 4;
			$last_q_year--;
		}
		$last_q_start_m = sprintf('%02d', ($last_q_num - 1) * 3 + 1);
		$last_q_end_m = sprintf('%02d', $last_q_num * 3);
		$last_q_start = $last_q_year . '-' . $last_q_start_m . '-01 00:00:00';
		$last_q_end = $last_q_year . '-' . $last_q_end_m . '-' . date('t', strtotime($last_q_year . '-' . $last_q_end_m . '-01')) . ' 23:59:59';

		$cur_year_start = date('Y-01-01 00:00:00');
		$cur_year_end = date('Y-12-31 23:59:59');
		$last_year = (int)date('Y') - 1;
		$last_year_start = $last_year . '-01-01 00:00:00';
		$last_year_end = $last_year . '-12-31 23:59:59';

		return array(
			array(
				'title' => 'Performer of the Month',
				'current' => $this->get_single_top_performer($cur_month_start, $cur_month_end, $user_ids),
				'last' => $this->get_single_top_performer($last_month_start, $last_month_end, $user_ids)
			),
			array(
				'title' => 'Performer of Quarter',
				'current' => $this->get_single_top_performer($cur_q_start, $cur_q_end, $user_ids),
				'last' => $this->get_single_top_performer($last_q_start, $last_q_end, $user_ids)
			),
			array(
				'title' => 'Performer of Year',
				'current' => $this->get_single_top_performer($cur_year_start, $cur_year_end, $user_ids),
				'last' => $this->get_single_top_performer($last_year_start, $last_year_end, $user_ids)
			)
		);
	}

	public function ajax_get_data()
	{
		$manager_id = (int)$this->session->userdata('manager_id');
		$role = (int)$this->session->userdata('manager_role');
		$is_admin = ($role === 1 || $manager_id === 1);

		$my_stats = $this->get_stats_block(array($manager_id));

		$team_list = $this->ci_admin_model->fetchTeamList($manager_id, '');
		$team_uids = array();
		if (!empty($team_list)) {
			$team_uids = array_values(array_filter(array_map(function($u) { return (int)$u['id']; }, $team_list)));
		}
		if (empty($team_uids) && $is_admin) {
			$all_users = $this->db->select('id')->where('status', 1)->get('ci_user')->result_array();
			$team_uids = array_map(function($u) { return (int)$u['id']; }, $all_users);
		}
		$team_stats = $this->get_stats_block($team_uids);

		$performers_team = $this->get_performers_block($team_uids);

		$company_stats = $this->get_stats_block(array());
		$performers_company = $this->get_performers_block(array());

		$centers_data = array();
		$centers_query = $this->db->query("SELECT DISTINCT office FROM ci_user WHERE office IS NOT NULL AND office != '' ORDER BY office ASC")->result_array();
		if (!empty($centers_query)) {
			$idx = 1;
			foreach ($centers_query as $crow) {
				$office_name = trim($crow['office']);
				if ($office_name === '') continue;
				$c_users = $this->db->select('id')->from('ci_user')->where('office', $office_name)->where('status', 1)->get()->result_array();
				if (!empty($c_users)) {
					$c_uids = array_map(function($u) { return (int)$u['id']; }, $c_users);
					$c_stats = $this->get_stats_block($c_uids);
					$c_perf = $this->get_performers_block($c_uids);
					$centers_data[] = array(
						'center_code' => 'Center - ' . $idx,
						'center_name' => $office_name,
						'user_count' => count($c_uids),
						'stats' => $c_stats,
						'performers' => $c_perf
					);
					$idx++;
				}
			}
		}

		$current_user_row = $this->db->select('office')->where('id', $manager_id)->get('ci_user')->row_array();
		$user_center_name = !empty($current_user_row['office']) ? trim($current_user_row['office']) : '';
		$center_wise_only = null;
		if ($user_center_name !== '') {
			$center_users = $this->db->select('id')->from('ci_user')->where('office', $user_center_name)->where('status', 1)->get()->result_array();
			if (!empty($center_users)) {
				$c_uids = array_map(function($u) { return (int)$u['id']; }, $center_users);
				$center_wise_only = array(
					'center_name' => $user_center_name,
					'stats' => $this->get_stats_block($c_uids),
					'performers' => $this->get_performers_block($c_uids)
				);
			}
		}

		$response = array(
			'status' => 'success',
			'timestamp' => date('H:i:s'),
			'date_formatted' => date('d M Y'),
			'is_admin' => $is_admin,
			'my_stats' => $my_stats,
			'team_stats' => $team_stats,
			'performers' => $performers_team,
			'performers_team' => $performers_team,
			'company_stats' => $company_stats,
			'performers_company' => $performers_company,
			'centers' => $centers_data,
			'center_wise_only' => $center_wise_only
		);

		header('Content-Type: application/json');
		echo json_encode($response);
		exit;
	}

	/** Money value of an order, the same way as everywhere on Live. */
	private function _amount_sql($o = 'o')
	{
		return "CAST(COALESCE(NULLIF({$o}.pricing_amount, 0), NULLIF({$o}.mrr, 0), 0) AS DECIMAL(12,2))";
	}

	private function _is_sale_sql($o = 'o')
	{
		return "({$o}.status IN ('Sales', 'Closed / Sold', 'Completed') OR ({$o}.sales_ref_no IS NOT NULL AND {$o}.sales_ref_no != ''))";
	}

	private function _growth($cur, $prev)
	{
		$cur = (float)$cur; $prev = (float)$prev;
		if ($prev <= 0) return null; // nothing to compare with
		return round((($cur - $prev) / $prev) * 100, 1);
	}

	/** TV screen data: company KPIs, monthly target, 7-day sales, top services, centers, leaderboard, latest sales. */
	public function ajax_presentation_data()
	{
		$amt = $this->_amount_sql('o');
		$sale = $this->_is_sale_sql('o');
		$today = date('Y-m-d');
		$yday = date('Y-m-d', strtotime('-1 day'));
		$m1 = date('Y-m-01 00:00:00'); $m2 = date('Y-m-t 23:59:59');
		$lm1 = date('Y-m-01 00:00:00', strtotime('first day of last month')); $lm2 = date('Y-m-t 23:59:59', strtotime('first day of last month'));
		$t1 = $today . ' 00:00:00'; $t2 = $today . ' 23:59:59';
		$y1 = $yday . ' 00:00:00'; $y2 = $yday . ' 23:59:59';
		$month_key = date('Y-m');
		$md1 = date('Y-m-01'); $md2 = date('Y-m-t');
		$q = function ($sql) { $r = $this->db->query($sql); return ($r && is_object($r)) ? $r->row_array() : array(); };
		$qa = function ($sql) { $r = $this->db->query($sql); return ($r && is_object($r)) ? $r->result_array() : array(); };

		// KPIs
		$s_cur = $q("SELECT COUNT(*) c, COALESCE(SUM({$amt}), 0) v FROM ci_orders o WHERE {$sale} AND o.created_at BETWEEN '{$m1}' AND '{$m2}'");
		$s_prev = $q("SELECT COUNT(*) c, COALESCE(SUM({$amt}), 0) v FROM ci_orders o WHERE {$sale} AND o.created_at BETWEEN '{$lm1}' AND '{$lm2}'");
		$s_today = $q("SELECT COUNT(*) c, COALESCE(SUM({$amt}), 0) v FROM ci_orders o WHERE {$sale} AND o.created_at BETWEEN '{$t1}' AND '{$t2}'");
		$o_cur = $q("SELECT COUNT(*) c FROM ci_orders o WHERE o.created_at BETWEEN '{$m1}' AND '{$m2}'");
		$o_prev = $q("SELECT COUNT(*) c FROM ci_orders o WHERE o.created_at BETWEEN '{$lm1}' AND '{$lm2}'");
		$lead_join = "FROM ci_leads a INNER JOIN ci_pipeline p ON p.lead_id = a.id AND p.pipeline_stage = 'lead'";
		$l_cur = $q("SELECT COUNT(DISTINCT a.id) c {$lead_join} WHERE a.created BETWEEN '{$m1}' AND '{$m2}'");
		$l_prev = $q("SELECT COUNT(DISTINCT a.id) c {$lead_join} WHERE a.created BETWEEN '{$lm1}' AND '{$lm2}'");
		$c_today = $q("SELECT COUNT(*) c FROM ci_call_logs WHERE created_at BETWEEN '{$t1}' AND '{$t2}'");
		$c_yday = $q("SELECT COUNT(*) c FROM ci_call_logs WHERE created_at BETWEEN '{$y1}' AND '{$y2}'");
		$period = "T.status = 'Active' AND (T.period_month = '{$month_key}' OR ('{$md1}' <= T.end_date AND '{$md2}' >= T.start_date))";
		$tgt = $q("SELECT COALESCE(SUM(T.target_amount), 0) v FROM ci_hr_targets T WHERE {$period}");

		$kpi = array(
			'sales_month' => (float)$s_cur['v'], 'sales_count' => (int)$s_cur['c'],
			'sales_growth' => $this->_growth($s_cur['v'], $s_prev['v']),
			'sales_today' => (float)$s_today['v'], 'sales_today_count' => (int)$s_today['c'],
			'orders_month' => (int)$o_cur['c'], 'orders_growth' => $this->_growth($o_cur['c'], $o_prev['c']),
			'leads_month' => (int)$l_cur['c'], 'leads_growth' => $this->_growth($l_cur['c'], $l_prev['c']),
			'calls_today' => (int)$c_today['c'], 'calls_yesterday' => (int)$c_yday['c'],
			'target_month' => (float)$tgt['v'],
		);

		// Sales of the last 7 days
		$trend = array();
		$rows = $qa("SELECT DATE(o.created_at) d, COALESCE(SUM({$amt}), 0) v, COUNT(*) c FROM ci_orders o WHERE {$sale} AND o.created_at >= '" . date('Y-m-d', strtotime('-6 days')) . " 00:00:00' GROUP BY DATE(o.created_at)");
		$by_day = array();
		foreach ($rows as $r) $by_day[$r['d']] = $r;
		for ($i = 6; $i >= 0; $i--) {
			$d = date('Y-m-d', strtotime("-{$i} days"));
			$trend[] = array('label' => date('D d', strtotime($d)), 'value' => isset($by_day[$d]) ? (float)$by_day[$d]['v'] : 0, 'count' => isset($by_day[$d]) ? (int)$by_day[$d]['c'] : 0, 'today' => ($i === 0));
		}

		// Top services this month
		$products = array();
		foreach ($qa("SELECT MIN(o.service_title) n, COALESCE(SUM({$amt}), 0) v, COUNT(*) c FROM ci_orders o WHERE {$sale} AND o.created_at BETWEEN '{$m1}' AND '{$m2}' AND o.service_title != '' GROUP BY LOWER(TRIM(o.service_title)) ORDER BY v DESC, c DESC LIMIT 5") as $r) {
			$products[] = array('name' => $r['n'], 'value' => (float)$r['v'], 'count' => (int)$r['c']);
		}

		// Per person (active users)
		$users = array();
		foreach ($qa("SELECT id, name, office, avatar FROM ci_user WHERE status = 1") as $u) {
			$av = (!empty($u['avatar']) && is_file(FCPATH . $u['avatar'])) ? base_url($u['avatar']) : '';
			$users[(int)$u['id']] = array('name' => $u['name'], 'office' => trim((string)$u['office']), 'avatar' => $av,
				'sales_month' => 0.0, 'sales_count' => 0, 'sales_last' => 0.0, 'orders_month' => 0, 'calls_today' => 0, 'leads_month' => 0, 'target' => 0.0);
		}
		$add = function ($rows, $key, $float = false) use (&$users) {
			foreach ($rows as $r) {
				$id = (int)$r['uid'];
				if (isset($users[$id])) $users[$id][$key] = $float ? (float)$r['v'] : (int)$r['v'];
			}
		};
		$add($qa("SELECT o.created_by uid, COALESCE(SUM({$amt}), 0) v FROM ci_orders o WHERE {$sale} AND o.created_at BETWEEN '{$m1}' AND '{$m2}' GROUP BY o.created_by"), 'sales_month', true);
		$add($qa("SELECT o.created_by uid, COUNT(*) v FROM ci_orders o WHERE {$sale} AND o.created_at BETWEEN '{$m1}' AND '{$m2}' GROUP BY o.created_by"), 'sales_count');
		$add($qa("SELECT o.created_by uid, COALESCE(SUM({$amt}), 0) v FROM ci_orders o WHERE {$sale} AND o.created_at BETWEEN '{$lm1}' AND '{$lm2}' GROUP BY o.created_by"), 'sales_last', true);
		$add($qa("SELECT o.created_by uid, COUNT(*) v FROM ci_orders o WHERE o.created_at BETWEEN '{$m1}' AND '{$m2}' GROUP BY o.created_by"), 'orders_month');
		$add($qa("SELECT user_id uid, COUNT(*) v FROM ci_call_logs WHERE created_at BETWEEN '{$t1}' AND '{$t2}' GROUP BY user_id"), 'calls_today');
		$add($qa("SELECT a.created_by uid, COUNT(DISTINCT a.id) v {$lead_join} WHERE a.created BETWEEN '{$m1}' AND '{$m2}' GROUP BY a.created_by"), 'leads_month');
		$add($qa("SELECT T.user_id uid, COALESCE(SUM(T.target_amount), 0) v FROM ci_hr_targets T WHERE {$period} GROUP BY T.user_id"), 'target', true);

		// Leaderboard: top 10 by sales this month, then orders, then calls today
		$board = array_filter($users, function ($u) { return $u['sales_month'] > 0 || $u['sales_count'] > 0 || $u['orders_month'] > 0 || $u['calls_today'] > 0 || $u['leads_month'] > 0; });
		uasort($board, function ($a, $b) {
			foreach (array('sales_month', 'sales_count', 'orders_month', 'calls_today', 'leads_month') as $k) {
				if ($a[$k] != $b[$k]) return ($a[$k] < $b[$k]) ? 1 : -1;
			}
			return strcasecmp($a['name'], $b['name']);
		});
		$leaderboard = array();
		foreach (array_slice($board, 0, 10, true) as $u) {
			$u['growth'] = $this->_growth($u['sales_month'], $u['sales_last']);
			$u['target_pct'] = $u['target'] > 0 ? round($u['sales_month'] * 100 / $u['target']) : null;
			$leaderboard[] = $u;
		}

		// Centers (offices)
		$centers = array();
		foreach ($users as $u) {
			if ($u['office'] === '') continue;
			$k = $u['office'];
			if (!isset($centers[$k])) $centers[$k] = array('name' => $k, 'staff' => 0, 'sales_month' => 0.0, 'sales_count' => 0, 'orders_month' => 0, 'calls_today' => 0, 'leads_month' => 0, 'target' => 0.0);
			$centers[$k]['staff']++;
			foreach (array('sales_month', 'sales_count', 'orders_month', 'calls_today', 'leads_month', 'target') as $f) $centers[$k][$f] += $u[$f];
		}
		$centers = array_values($centers);
		usort($centers, function ($a, $b) { return $a['sales_month'] == $b['sales_month'] ? strcmp($a['name'], $b['name']) : ($a['sales_month'] < $b['sales_month'] ? 1 : -1); });

		// Latest sales (newest first)
		$latest = array();
		foreach ($qa("SELECT o.id, o.client_name, o.company_name, o.service_title, {$amt} v, o.created_at, o.created_by FROM ci_orders o WHERE {$sale} ORDER BY o.id DESC LIMIT 6") as $r) {
			$uid = (int)$r['created_by'];
			$latest[] = array(
				'id' => (int)$r['id'],
				'rep' => isset($users[$uid]) ? $users[$uid]['name'] : '-',
				'avatar' => isset($users[$uid]) ? $users[$uid]['avatar'] : '',
				'client' => (string)($r['company_name'] ?: $r['client_name']),
				'service' => (string)$r['service_title'],
				'amount' => (float)$r['v'],
				'when' => date('d M, h:i A', strtotime($r['created_at'])),
			);
		}

		header('Content-Type: application/json');
		echo json_encode(array(
			'status' => true,
			'updated' => date('h:i:s A'),
			'month' => date('F Y'),
			'kpi' => $kpi,
			'trend' => $trend,
			'products' => $products,
			'leaderboard' => $leaderboard,
			'centers' => $centers,
			'latest' => $latest,
			'company_stats' => $this->get_stats_block(array()),
		));
		exit;
	}
}
