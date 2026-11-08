<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Extra blocks for the Classic 5 dashboard only.
 * Scope follows the existing dashboard: admins (or all_data) see everyone, others see themselves and their team.
 * Achievement follows dashboard_achievement_graph (closed leads by close date + orders by created date).
 */
class Dashboard5_model extends CI_Model {

	private $uid = 0;
	private $is_all = false;
	private $team = array();      // team members, without me
	private $team_me = array();   // team members and me

	public function __construct()
	{
		parent::__construct();
		$this->load->model('ci_admin_model');
	}

	private function init()
	{
		$this->uid = (int)$this->session->userdata('manager_id');
		$role = (int)$this->session->userdata('manager_role');
		$this->is_all = ($role === 1 || crm_can('all_data') || $this->uid === 1);
		$t = $this->ci_admin_model->fetchTeamList($this->uid, '');
		$t = is_array($t) ? array_values(array_unique(array_filter(array_map('intval', $t)))) : array();
		$this->team = array_values(array_diff($t, array($this->uid)));
		$this->team_me = array_values(array_unique(array_merge($this->team, array($this->uid))));
	}

	public function extras()
	{
		$this->init();
		$out = array('is_all' => $this->is_all);
		$out['pipeline_my'] = $this->pipeline_my();
		$out['tile_change'] = $this->tile_change();
		$out['leaderboard'] = $this->leaderboard();
		$out['weekly'] = $this->weekly_added();
		$out['ageing'] = $this->ageing();
		$out['calls'] = $this->calls_today();
		return $out;
	}

	private function ids_sql($ids)
	{
		$ids = array_filter(array_map('intval', (array)$ids));
		return empty($ids) ? '0' : implode(',', $ids);
	}

	/** Lead scope used by the existing pipeline block (me and my team, or everything for admins). */
	private function lead_scope($alias)
	{
		if ($this->is_all) return '';
		$m = $this->uid;
		$ts = $this->ids_sql($this->team_me);
		return " AND ({$alias}.lead_assign = {$m} OR ({$alias}.created_by = {$m} AND ({$alias}.lead_assign = 0 OR {$alias}.lead_assign IS NULL)) OR ({$alias}.agents IS NOT NULL AND {$alias}.agents != '' AND FIND_IN_SET({$m}, {$alias}.agents) > 0) OR {$alias}.created_by IN ({$ts}) OR {$alias}.lead_assign IN ({$ts}))";
	}

	/** Same four steps as the pipeline block, for my own records only. */
	private function pipeline_my()
	{
		$m = $this->uid;
		$ls = " AND (L.lead_assign = {$m} OR (L.created_by = {$m} AND (L.lead_assign = 0 OR L.lead_assign IS NULL)) OR (L.agents IS NOT NULL AND L.agents != '' AND FIND_IN_SET({$m}, L.agents) > 0))";
		$os = " AND (O.created_by = {$m} OR O.assigned_to = {$m})";
		$open = " AND (L.company_status IS NULL OR LOWER(L.company_status) != 'closed')";
		$db = $this->db->query("SELECT COUNT(L.id) cnt, COALESCE(SUM(CAST(L.mrr AS DECIMAL(12,2))), 0) val FROM ci_leads L INNER JOIN ci_pipeline P ON P.lead_id = L.id WHERE P.pipeline_stage = 'database'{$open}{$ls}")->row_array();
		$ld = $this->db->query("SELECT COUNT(L.id) cnt, COALESCE(SUM(CAST(L.mrr AS DECIMAL(12,2))), 0) val FROM ci_leads L INNER JOIN ci_pipeline P ON P.lead_id = L.id WHERE " . crm_stage_lead_sql('L', 'P') . "{$open}{$ls}")->row_array();
		$or = $this->db->query("SELECT COUNT(O.id) cnt, COALESCE(SUM(CAST(O.pricing_amount AS DECIMAL(12,2))), 0) val FROM ci_orders O WHERE " . crm_order_open_sql('O') . " AND O.status != 'Cancelled'{$os}")->row_array();
		$sl = $this->db->query("SELECT COUNT(O.id) cnt, COALESCE(SUM(CAST(O.pricing_amount AS DECIMAL(12,2))), 0) val FROM ci_orders O WHERE " . crm_order_is_sale_sql('O') . "{$os}")->row_array();
		return array(
			'database' => array('count' => (int)$db['cnt'], 'value' => (float)$db['val']),
			'leads'    => array('count' => (int)$ld['cnt'], 'value' => (float)$ld['val']),
			'orders'   => array('count' => (int)$or['cnt'], 'value' => (float)$or['val']),
			'sales'    => array('count' => (int)$sl['cnt'], 'value' => (float)$sl['val']),
		);
	}

	/**
	 * Tiles: records added this month and last month, with the same rules as each tile's count
	 * (My Leads, My Orders, Team Leads, Team Orders).
	 */
	private function tile_change()
	{
		$m = $this->uid;
		// This month so far against the same days of last month (1st to today's date).
		$t1 = date('Y-m-01'); $t2 = date('Y-m-d');
		$l1 = date('Y-m-01', strtotime('first day of last month'));
		$l2 = date('Y-m-', strtotime($l1)) . sprintf('%02d', min((int)date('j'), (int)date('t', strtotime($l1))));
		$team = $this->team;
		if (empty($team) && $this->is_all) {
			$q = $this->db->query("SELECT id FROM ci_user WHERE status = 1 AND id != {$m}");
			foreach ($q->result_array() as $u) $team[] = (int)$u['id'];
		}
		$ts = $this->ids_sql($team);
		$my_lead = crm_stage_lead_sql('A', 'P') . " AND (A.lead_assign = {$m} OR (A.created_by = {$m} AND (A.lead_assign = 0 OR A.lead_assign IS NULL)) OR (A.agents IS NOT NULL AND A.agents != '' AND FIND_IN_SET({$m}, A.agents) > 0)) AND (A.database_category != 'KAM Onboarded' OR A.database_category IS NULL)";
		$tm_lead = crm_stage_lead_sql('A', 'P') . " AND ((A.lead_assign > 0 AND A.lead_assign IN ({$ts})) OR ((A.lead_assign = 0 OR A.lead_assign IS NULL) AND A.created_by IN ({$ts}))) AND (A.company_status IS NULL OR LOWER(A.company_status) != 'closed')";
		$my_ord = $this->ci_admin_model->my_orders_scope_sql($m);
		$tm_ord = "O.created_by IN ({$ts}) AND " . crm_order_open_sql('O');
		$res = array();
		foreach (array('my_leads' => $my_lead, 'team_leads' => $tm_lead) as $k => $w) {
			$r = $this->db->query("SELECT
				SUM(LEFT(A.created, 10) BETWEEN '{$t1}' AND '{$t2}') now_n,
				SUM(LEFT(A.created, 10) BETWEEN '{$l1}' AND '{$l2}') last_n
				FROM ci_leads A LEFT JOIN ci_pipeline P ON A.id = P.lead_id WHERE {$w}")->row_array();
			$res[$k] = array('now' => (int)$r['now_n'], 'last' => (int)$r['last_n'], 'now_label' => date('j', strtotime($t1)) . '-' . date('j M', strtotime($t2)), 'last_label' => date('j', strtotime($l1)) . '-' . date('j M', strtotime($l2)));
		}
		foreach (array('my_orders' => array($my_ord, true), 'team_orders' => array($tm_ord, false)) as $k => $w) {
			$join = $w[1] ? ' LEFT JOIN ci_leads A ON O.lead_id = A.id' : '';
			$r = $this->db->query("SELECT
				COUNT(DISTINCT CASE WHEN DATE(O.created_at) BETWEEN '{$t1}' AND '{$t2}' THEN O.id END) now_n,
				COUNT(DISTINCT CASE WHEN DATE(O.created_at) BETWEEN '{$l1}' AND '{$l2}' THEN O.id END) last_n
				FROM ci_orders O{$join} WHERE {$w[0]}")->row_array();
			$res[$k] = array('now' => (int)$r['now_n'], 'last' => (int)$r['last_n'], 'now_label' => date('j', strtotime($t1)) . '-' . date('j M', strtotime($t2)), 'last_label' => date('j', strtotime($l1)) . '-' . date('j M', strtotime($l2)));
		}
		return $res;
	}

	/** Top 5 people this month by amount achieved, with their target. */
	private function leaderboard()
	{
		$d1 = date('Y-m-01'); $d2 = date('Y-m-t'); $mk = date('Y-m');
		if ($this->is_all) {
			$users = array();
			foreach ($this->db->query("SELECT id FROM ci_user WHERE status = 1")->result_array() as $u) $users[] = (int)$u['id'];
		} else {
			$users = $this->team_me;
		}
		$us = $this->ids_sql($users);
		$ach = array();
		$q = $this->db->query("SELECT uid, SUM(v) s FROM (
				SELECT created_by uid, id, CAST(mrr AS DECIMAL(12,2)) v FROM ci_leads WHERE close_date >= '{$d1}' AND close_date <= '{$d2}' AND close_date != '' AND close_date != '0000-00-00' AND status IN (39, 42) AND created_by IN ({$us})
				UNION
				SELECT lead_assign uid, id, CAST(mrr AS DECIMAL(12,2)) v FROM ci_leads WHERE close_date >= '{$d1}' AND close_date <= '{$d2}' AND close_date != '' AND close_date != '0000-00-00' AND status IN (39, 42) AND lead_assign IN ({$us})
			) t GROUP BY uid");
		foreach ($q->result_array() as $r) $ach[(int)$r['uid']] = (float)$r['s'];
		$q = $this->db->query("SELECT created_by uid, COALESCE(SUM(CAST(pricing_amount AS DECIMAL(12,2))), 0) s FROM ci_orders WHERE created_at >= '{$d1} 00:00:00' AND created_at <= '{$d2} 23:59:59' AND status IN ('completed', 'accepted', 'active', 'processing') AND created_by IN ({$us}) GROUP BY created_by");
		foreach ($q->result_array() as $r) $ach[(int)$r['uid']] = (isset($ach[(int)$r['uid']]) ? $ach[(int)$r['uid']] : 0) + (float)$r['s'];
		$ach = array_filter($ach, function ($v) { return $v > 0; });
		arsort($ach);
		$ach = array_slice($ach, 0, 5, true);
		if (empty($ach)) return array();
		$ids = $this->ids_sql(array_keys($ach));
		$names = array();
		foreach ($this->db->query("SELECT id, name FROM ci_user WHERE id IN ({$ids})")->result_array() as $u) $names[(int)$u['id']] = $u['name'];
		$targets = array();
		foreach ($this->db->query("SELECT user_id, target_amount FROM ci_hr_targets WHERE user_id IN ({$ids}) AND status = 'Active' AND (period_month = '{$mk}' OR ('{$d1}' <= end_date AND '{$d2}' >= start_date)) ORDER BY id DESC")->result_array() as $t) {
			if (!isset($targets[(int)$t['user_id']])) $targets[(int)$t['user_id']] = (float)$t['target_amount'];
		}
		$rows = array();
		foreach ($ach as $id => $v) {
			$rows[] = array('id' => $id, 'name' => isset($names[$id]) ? $names[$id] : ('User ' . $id), 'achieved' => $v, 'target' => isset($targets[$id]) ? $targets[$id] : 0);
		}
		return $rows;
	}

	/** Records added per week, last 8 weeks (Monday to Sunday), same scope as the pipeline. */
	private function weekly_added()
	{
		$start = date('Y-m-d', strtotime('monday this week -7 weeks'));
		$q = $this->db->query("SELECT LEFT(L.created, 10) d, COUNT(L.id) n FROM ci_leads L WHERE LEFT(L.created, 10) >= '{$start}'" . $this->lead_scope('L') . " GROUP BY LEFT(L.created, 10)");
		$weeks = array();
		for ($i = 7; $i >= 0; $i--) {
			$mon = date('Y-m-d', strtotime("monday this week -{$i} weeks"));
			$weeks[$mon] = array('label' => date('d M', strtotime($mon)), 'from' => $mon, 'to' => date('Y-m-d', strtotime($mon . ' +6 days')), 'n' => 0);
		}
		foreach ($q->result_array() as $r) {
			$mon = date('Y-m-d', strtotime('monday this week', strtotime($r['d'])));
			if (isset($weeks[$mon])) $weeks[$mon]['n'] += (int)$r['n'];
		}
		return array_values($weeks);
	}

	/** Open leads by days since last contact (newest follow-up entry up to today, else the date the lead was added). */
	private function ageing()
	{
		$today = date('Y-m-d');
		$q = $this->db->query("SELECT
				SUM(age <= 7) a, SUM(age BETWEEN 8 AND 30) b, SUM(age > 30) c
			FROM (
				SELECT DATEDIFF('{$today}', COALESCE(F.last_d, LEFT(L.created, 10))) age
				FROM ci_leads L
				INNER JOIN ci_pipeline P ON P.lead_id = L.id
				LEFT JOIN (SELECT lead_id, MAX(followup_date) last_d FROM ci_followup WHERE lead_type = 'lead' AND followup_date <= '{$today}' GROUP BY lead_id) F ON F.lead_id = L.id
				WHERE " . crm_stage_lead_sql('L', 'P') . " AND L.status NOT IN (42, 43, 44) AND (L.company_status IS NULL OR LOWER(L.company_status) != 'closed')" . $this->lead_scope('L') . "
			) t")->row_array();
		return array('d7' => (int)$q['a'], 'd30' => (int)$q['b'], 'older' => (int)$q['c']);
	}

	/** Calls made today from the call log (3CX and manual), mine and my team's. */
	private function calls_today()
	{
		$d = date('Y-m-d');
		$out = array();
		$team = $this->is_all ? null : $this->ids_sql($this->team_me);
		foreach (array('my' => 'user_id = ' . $this->uid, 'team' => ($team === null ? '1=1' : "user_id IN ({$team})")) as $k => $w) {
			$r = $this->db->query("SELECT COUNT(*) n, SUM(call_status = 'Completed') ok, COALESCE(SUM(CASE WHEN call_status = 'Completed' THEN duration ELSE 0 END), 0) secs FROM ci_call_logs WHERE created_at >= '{$d} 00:00:00' AND created_at <= '{$d} 23:59:59' AND {$w}")->row_array();
			$out[$k] = array('total' => (int)$r['n'], 'answered' => (int)$r['ok'], 'not_answered' => (int)$r['n'] - (int)$r['ok'], 'secs' => (int)$r['secs']);
		}
		return $out;
	}
}
