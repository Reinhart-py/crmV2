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

		$this->load->view('manager/live/index', $data);
	}

	public function presentation()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Live Presentation',
			'page_heading' => 'Live'
		);

		$manager_id = (int)$this->session->userdata('manager_id');
		$role = (int)$this->session->userdata('manager_role');
		$is_admin = ($role === 1 || $manager_id === 1);
		$data['is_admin'] = $is_admin;

		$user_row = $this->db->select('id, name, office, department')->where('id', $manager_id)->get('ci_user')->row_array();
		$data['current_user'] = $user_row;
		$data['user_office'] = !empty($user_row['office']) ? $user_row['office'] : '';

		$data['company_stats'] = $this->get_stats_block(array());
		$data['company_perf'] = $this->get_performers_block(array());

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
			COALESCE(SUM(CASE WHEN a.created_at >= '{$t_d1}' AND a.created_at <= '{$t_d2}' THEN 1 ELSE 0 END), 0) as today,
			COALESCE(SUM(CASE WHEN a.created_at >= '{$w_d1}' AND a.created_at <= '{$w_d2}' THEN 1 ELSE 0 END), 0) as seven_days,
			COALESCE(SUM(CASE WHEN a.created_at >= '{$m_d1}' AND a.created_at <= '{$m_d2}' THEN 1 ELSE 0 END), 0) as month,
			COALESCE(SUM(CASE WHEN a.created_at >= '{$q_d1}' AND a.created_at <= '{$q_d2}' THEN 1 ELSE 0 END), 0) as quarter
		FROM ci_leads a
		INNER JOIN ci_pipeline p ON p.lead_id = a.id AND p.pipeline_stage = 'lead' {$where_leads}")->row_array();

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

		$where_tick = $has_filter ? "AND (raised_by IN ({$u_str}) OR assigned_user IN ({$u_str}))" : "";
		$tick_row = $this->db->query("SELECT 
			COALESCE(SUM(CASE WHEN (updated_at >= '{$t_d1}' OR created >= '{$t_d1}') AND (updated_at <= '{$t_d2}' OR created <= '{$t_d2}') THEN 1 ELSE 0 END), 0) as today,
			COALESCE(SUM(CASE WHEN (updated_at >= '{$w_d1}' OR created >= '{$w_d1}') AND (updated_at <= '{$w_d2}' OR created <= '{$w_d2}') THEN 1 ELSE 0 END), 0) as seven_days,
			COALESCE(SUM(CASE WHEN (updated_at >= '{$m_d1}' OR created >= '{$m_d1}') AND (updated_at <= '{$m_d2}' OR created <= '{$m_d2}') THEN 1 ELSE 0 END), 0) as month,
			COALESCE(SUM(CASE WHEN (updated_at >= '{$q_d1}' OR created >= '{$q_d1}') AND (updated_at <= '{$q_d2}' OR created <= '{$q_d2}') THEN 1 ELSE 0 END), 0) as quarter
		FROM ci_tickets
		WHERE status = 'Resolved' {$where_tick}")->row_array();

		$where_vert = $has_filter ? "WHERE created_by IN ({$u_str})" : "";
		$vert_row = $this->db->query("SELECT 
			COALESCE(SUM(CASE WHEN created_at >= '{$t_d1}' AND created_at <= '{$t_d2}' THEN 1 ELSE 0 END), 0) as today,
			COALESCE(SUM(CASE WHEN created_at >= '{$w_d1}' AND created_at <= '{$w_d2}' THEN 1 ELSE 0 END), 0) as seven_days,
			COALESCE(SUM(CASE WHEN created_at >= '{$m_d1}' AND created_at <= '{$m_d2}' THEN 1 ELSE 0 END), 0) as month,
			COALESCE(SUM(CASE WHEN created_at >= '{$q_d1}' AND created_at <= '{$q_d2}' THEN 1 ELSE 0 END), 0) as quarter
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
}
