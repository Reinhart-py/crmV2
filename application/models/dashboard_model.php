<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Data for the role dashboards (Admin, Sales, Back Office, KAM).
 * Every number here is a real query; nothing is filled in when a table is empty.
 * Achievement follows the HR Targets page: completed calls, convert_to_lead logs,
 * meetings created, and orders with status Sales (pricing_amount), all by created date.
 */
class Dashboard_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('ci_admin_model');
		// Each user's choice of dashboard: modern (default) or classic. Kept in its own table (ci_user is at its row-size limit).
		if (!$this->db->table_exists('ci_user_prefs')) {
			$this->db->query("CREATE TABLE IF NOT EXISTS ci_user_prefs (user_id INT(11) NOT NULL PRIMARY KEY, dash_style VARCHAR(10) NOT NULL DEFAULT 'modern', updated_at DATETIME NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		}
	}

	public function get_style($uid)
	{
		$r = $this->db->select('dash_style')->where('user_id', (int)$uid)->get('ci_user_prefs')->row_array();
		return (!empty($r) && in_array($r['dash_style'], $this->styles(), true)) ? $r['dash_style'] : 'modern';
	}

	/** Dashboards a user can pick (modern is the default). */
	public function styles()
	{
		return array('modern', 'classic', 'classic2', 'classic3');
	}

	public function set_style($uid, $style)
	{
		$style = in_array($style, $this->styles(), true) ? $style : 'modern';
		$this->db->query('INSERT INTO ci_user_prefs (user_id, dash_style, updated_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE dash_style = VALUES(dash_style), updated_at = NOW()', array((int)$uid, $style));
		return $style;
	}

	/** Which dashboard a role gets. */
	public function role_key($user_type)
	{
		$t = (int)$user_type;
		if ($t === 1) return 'admin';
		if ($t === 2) return 'backoffice';
		if ($t === 10) return 'kam';
		return 'sales';
	}

	/** Users this person may see on the dashboard: everyone active for admins, else self plus team. */
	public function scope_ids($uid, $all)
	{
		$uid = (int)$uid;
		if ($all) {
			$pid = function_exists('crm_presentation_role_id') ? (int)crm_presentation_role_id() : 0;
			$this->db->select('id')->from('ci_user')->where('status', 1);
			if ($pid > 0) $this->db->where('user_type !=', $pid);
			$ids = array_map('intval', array_column($this->db->get()->result_array(), 'id'));
			return $ids ? $ids : array($uid);
		}
		$team = $this->ci_admin_model->fetchTeamList($uid, '');
		$ids = array_merge(array($uid), array_map('intval', (array)$team));
		return array_values(array_unique($ids));
	}

	public function people($ids)
	{
		if (!$ids) return array();
		$rows = $this->db->query("SELECT U.id, U.name, U.user_type, U.department, U.last_active_at, U.is_online, R.title AS role_name
			FROM ci_user U LEFT JOIN ci_roles R ON R.id = U.user_type
			WHERE U.id IN (" . $this->in($ids) . ") AND U.status = 1 ORDER BY U.name")->result_array();
		$out = array();
		foreach ($rows as $r) $out[(int)$r['id']] = $r;
		return $out;
	}

	/** Month window: [start, end, days in month, days passed]. */
	public function month_window($ym)
	{
		if (!preg_match('/^\d{4}-\d{2}$/', (string)$ym)) $ym = date('Y-m');
		$s = $ym . '-01';
		$e = date('Y-m-t', strtotime($s));
		$dim = (int)date('t', strtotime($s));
		if ($ym === date('Y-m')) $passed = (int)date('j');
		elseif ($ym < date('Y-m')) $passed = $dim;
		else $passed = 0;
		return array('ym' => $ym, 'start' => $s, 'end' => $e, 'days' => $dim, 'passed' => $passed);
	}

	/* ---------------------------------------------------------------- follow-ups */

	/** Owner of a lead's follow-up: the assignee, or the creator when nobody is assigned. */
	private function owner_sql($a = 'a')
	{
		return "(CASE WHEN {$a}.lead_assign > 0 THEN {$a}.lead_assign ELSE {$a}.created_by END)";
	}

	/** Leads that still need a follow-up (open, real date). */
	private function open_fu_sql($a = 'a')
	{
		return "({$a}.company_status IS NULL OR LOWER({$a}.company_status) != 'closed') AND {$a}.status NOT IN (42,43,44)
			AND {$a}.followup_date >= '2000-01-01'";
	}

	/** Follow-up counts for one person (same scope as the Follow-ups page: assigned, created-unassigned, or co-agent). */
	public function my_followups($uid)
	{
		$uid = (int)$uid;
		$today = date('Y-m-d');
		$w = date('Y-m-d', strtotime('+7 days'));
		$r = $this->db->query("SELECT
				SUM(a.followup_date < '$today') AS overdue,
				SUM(a.followup_date = '$today') AS today,
				SUM(a.followup_date > '$today' AND a.followup_date <= '$w') AS next7,
				MIN(CASE WHEN a.followup_date < '$today' THEN a.followup_date END) AS oldest
			FROM ci_leads a
			WHERE (a.lead_assign = $uid OR (a.created_by = $uid AND (a.lead_assign = 0 OR a.lead_assign IS NULL))
				OR (a.agents != '' AND FIND_IN_SET($uid, a.agents) > 0))
				AND " . $this->open_fu_sql('a'))->row_array();
		return array(
			'overdue' => (int)$r['overdue'], 'today' => (int)$r['today'], 'next7' => (int)$r['next7'],
			'oldest' => $r['oldest'],
		);
	}

	/** The person's call list: overdue and due today, oldest first. */
	public function my_followup_list($uid, $limit = 12)
	{
		$uid = (int)$uid;
		$today = date('Y-m-d');
		return $this->db->query("SELECT a.id, a.lead_id, a.customer, a.poc, a.contact1, a.followup_date, a.followup_time, S.status AS status_name,
				COALESCE(P.pipeline_stage, 'database') AS stage
			FROM ci_leads a
			LEFT JOIN ci_status S ON S.id = a.status
			LEFT JOIN ci_pipeline P ON P.lead_id = a.id
			WHERE (a.lead_assign = $uid OR (a.created_by = $uid AND (a.lead_assign = 0 OR a.lead_assign IS NULL))
				OR (a.agents != '' AND FIND_IN_SET($uid, a.agents) > 0))
				AND " . $this->open_fu_sql('a') . " AND a.followup_date <= '$today'
			ORDER BY a.followup_date = '$today' DESC, a.followup_date ASC, a.followup_time ASC
			LIMIT " . (int)$limit)->result_array();
	}

	/* ---------------------------------------------------------------- staff tracker */

	/**
	 * One row per person: follow-ups overdue / due today / done today, calls today,
	 * tasks open / late, sales this month against target, last seen.
	 */
	public function tracker($ids, $win)
	{
		$people = $this->people($ids);
		if (!$people) return array();
		$in = $this->in(array_keys($people));
		$today = date('Y-m-d');
		$rows = array();
		foreach ($people as $id => $p) {
			$rows[$id] = array(
				'id' => $id, 'name' => $p['name'], 'role' => $p['role_name'], 'user_type' => (int)$p['user_type'],
				'last_active_at' => $p['last_active_at'], 'is_online' => (int)$p['is_online'],
				'fu_overdue' => 0, 'fu_today' => 0, 'fu_oldest' => null, 'fu_done' => 0,
				'calls' => 0, 'calls_ok' => 0, 'talk' => 0,
				'tasks_open' => 0, 'tasks_late' => 0,
				'sales' => 0.0, 'target' => 0.0, 'meetings' => 0, 'leads' => 0,
			);
		}
		$own = $this->owner_sql('a');
		foreach ($this->db->query("SELECT $own AS uid,
				SUM(a.followup_date < '$today') AS overdue, SUM(a.followup_date = '$today') AS today,
				MIN(CASE WHEN a.followup_date < '$today' THEN a.followup_date END) AS oldest
			FROM ci_leads a WHERE " . $this->open_fu_sql('a') . " AND a.followup_date <= '$today' AND $own IN ($in)
			GROUP BY uid")->result_array() as $r) {
			$u = (int)$r['uid'];
			$rows[$u]['fu_overdue'] = (int)$r['overdue'];
			$rows[$u]['fu_today'] = (int)$r['today'];
			$rows[$u]['fu_oldest'] = $r['oldest'];
		}
		foreach ($this->db->query("SELECT user_id AS uid, COUNT(*) AS n FROM ci_followup
			WHERE followup_date = '$today' AND user_id IN ($in) GROUP BY user_id")->result_array() as $r) {
			$rows[(int)$r['uid']]['fu_done'] = (int)$r['n'];
		}
		foreach ($this->db->query("SELECT user_id AS uid, COUNT(*) AS n, SUM(call_status = 'Completed') AS ok, SUM(duration) AS talk
			FROM ci_call_logs WHERE created_at >= '$today 00:00:00' AND created_at <= '$today 23:59:59' AND user_id IN ($in)
			GROUP BY user_id")->result_array() as $r) {
			$u = (int)$r['uid'];
			$rows[$u]['calls'] = (int)$r['n'];
			$rows[$u]['calls_ok'] = (int)$r['ok'];
			$rows[$u]['talk'] = (int)$r['talk'];
		}
		foreach ($this->open_tasks(array_keys($people)) as $t) {
			foreach ($t['_users'] as $u) {
				if (!isset($rows[$u])) continue;
				$rows[$u]['tasks_open']++;
				if ($t['_late']) $rows[$u]['tasks_late']++;
			}
		}
		$s = $win['start']; $e = $win['end'];
		foreach ($this->db->query("SELECT created_by AS uid, SUM(pricing_amount) AS amt FROM ci_orders
			WHERE status = 'Sales' AND created_at >= '$s 00:00:00' AND created_at <= '$e 23:59:59' AND created_by IN ($in)
			GROUP BY created_by")->result_array() as $r) {
			$rows[(int)$r['uid']]['sales'] = (float)$r['amt'];
		}
		foreach ($this->db->query("SELECT created_by AS uid, COUNT(*) AS n FROM ci_meetings
			WHERE created >= '$s' AND created <= '$e 23:59:59' AND created_by IN ($in) GROUP BY created_by")->result_array() as $r) {
			$rows[(int)$r['uid']]['meetings'] = (int)$r['n'];
		}
		foreach ($this->db->query("SELECT action_by AS uid, COUNT(*) AS n FROM ci_pipeline_logs
			WHERE action = 'convert_to_lead' AND created_at >= '$s 00:00:00' AND created_at <= '$e 23:59:59' AND action_by IN ($in)
			GROUP BY action_by")->result_array() as $r) {
			$rows[(int)$r['uid']]['leads'] = (int)$r['n'];
		}
		foreach ($this->db->query("SELECT user_id AS uid, SUM(target_amount) AS amt FROM ci_hr_targets
			WHERE status = 'Active' AND period_month = " . $this->db->escape($win['ym']) . " AND user_id IN ($in)
			GROUP BY user_id")->result_array() as $r) {
			$rows[(int)$r['uid']]['target'] = (float)$r['amt'];
		}
		// Most behind first: overdue follow-ups, then late tasks.
		uasort($rows, function ($a, $b) {
			if ($a['fu_overdue'] !== $b['fu_overdue']) return $b['fu_overdue'] - $a['fu_overdue'];
			if ($a['tasks_late'] !== $b['tasks_late']) return $b['tasks_late'] - $a['tasks_late'];
			return strcasecmp($a['name'], $b['name']);
		});
		return array_values($rows);
	}

	/** Open tasks of these people (assigned_to or one of assigned_users). */
	public function open_tasks($ids)
	{
		if (!$ids) return array();
		$set = array_flip(array_map('intval', $ids));
		$today = date('Y-m-d');
		$rows = $this->db->query("SELECT T.id, T.task_id, T.customer, T.category, T.status, T.followup_date, T.assigned_to, T.assigned_users, T.client_id
			FROM ci_tasks T WHERE (T.status IS NULL OR T.status NOT IN ('Completed','Closed','Done','Cancelled'))
			ORDER BY T.followup_date ASC")->result_array();
		$out = array();
		foreach ($rows as $r) {
			$users = array();
			if ((int)$r['assigned_to'] > 0) $users[] = (int)$r['assigned_to'];
			foreach (preg_split('/[\s,]+/', (string)$r['assigned_users'], -1, PREG_SPLIT_NO_EMPTY) as $u) $users[] = (int)$u;
			$users = array_values(array_unique(array_filter($users, function ($u) use ($set) { return isset($set[$u]); })));
			if (!$users) continue;
			$r['_users'] = $users;
			$r['_late'] = !empty($r['followup_date']) && $r['followup_date'] >= '2000-01-01' && substr($r['followup_date'], 0, 10) < $today;
			$out[] = $r;
		}
		return $out;
	}

	/* ---------------------------------------------------------------- target, projection, achievement */

	public function targets($ids, $win)
	{
		$in = $this->in($ids);
		$s = $win['start']; $e = $win['end'];
		$t = $this->db->query("SELECT SUM(target_amount) AS amt, SUM(target_calls) AS calls, SUM(target_leads) AS leads, SUM(target_meetings) AS meetings
			FROM ci_hr_targets WHERE status = 'Active' AND period_month = " . $this->db->escape($win['ym']) . " AND user_id IN ($in)")->row_array();
		$sales = $this->db->query("SELECT SUM(pricing_amount) AS amt, COUNT(*) AS n FROM ci_orders
			WHERE status = 'Sales' AND created_at >= '$s 00:00:00' AND created_at <= '$e 23:59:59' AND created_by IN ($in)")->row_array();
		$calls = $this->db->query("SELECT COUNT(*) AS n FROM ci_call_logs
			WHERE call_status = 'Completed' AND created_at >= '$s 00:00:00' AND created_at <= '$e 23:59:59' AND user_id IN ($in)")->row_array();
		$leads = $this->db->query("SELECT COUNT(*) AS n FROM ci_pipeline_logs
			WHERE action = 'convert_to_lead' AND created_at >= '$s 00:00:00' AND created_at <= '$e 23:59:59' AND action_by IN ($in)")->row_array();
		$meet = $this->db->query("SELECT COUNT(*) AS n FROM ci_meetings
			WHERE created >= '$s' AND created <= '$e 23:59:59' AND created_by IN ($in)")->row_array();
		// Projection: open leads (Lead stage) expected to close this month, by their MRR.
		$proj = $this->db->query("SELECT SUM(CAST(NULLIF(A.mrr, '') AS DECIMAL(14,2))) AS amt, COUNT(*) AS n
			FROM ci_leads A JOIN ci_pipeline P ON P.lead_id = A.id
			WHERE " . crm_stage_lead_sql('A', 'P') . " AND A.status NOT IN (42,43,44)
				AND A.close_date >= '$s' AND A.close_date <= '$e'
				AND (A.created_by IN ($in) OR A.lead_assign IN ($in))")->row_array();
		$out = array(
			'target' => (float)$t['amt'], 'achieved' => (float)$sales['amt'], 'sales_n' => (int)$sales['n'],
			'projection' => (float)$proj['amt'], 'projection_n' => (int)$proj['n'],
			'rows' => array(
				array('k' => 'calls', 'label' => 'Calls completed', 'target' => (int)$t['calls'], 'done' => (int)$calls['n']),
				array('k' => 'leads', 'label' => 'Leads converted', 'target' => (int)$t['leads'], 'done' => (int)$leads['n']),
				array('k' => 'meetings', 'label' => 'Meetings', 'target' => (int)$t['meetings'], 'done' => (int)$meet['n']),
			),
		);
		// Where the month should be by today if work were spread evenly.
		$out['pace'] = $win['days'] > 0 ? $out['target'] * $win['passed'] / $win['days'] : 0;
		return $out;
	}

	/** Funnel: database records, open leads, open orders, sales this month. */
	public function funnel($ids, $win)
	{
		$in = $this->in($ids);
		$s = $win['start']; $e = $win['end'];
		$own = "(A.created_by IN ($in) OR A.lead_assign IN ($in))";
		$st = $this->db->query("SELECT
				SUM(COALESCE(P.pipeline_stage, 'database') = 'database') AS db,
				SUM(" . crm_stage_lead_sql('A', 'P') . ") AS lead
			FROM ci_leads A LEFT JOIN ci_pipeline P ON P.lead_id = A.id
			WHERE $own AND A.status NOT IN (42,43,44)")->row_array();
		$ord = $this->db->query("SELECT COUNT(*) AS n FROM ci_orders O
			WHERE " . crm_order_open_sql('O') . " AND O.status != 'Cancelled' AND (O.created_by IN ($in) OR O.assigned_to IN ($in))")->row_array();
		$sal = $this->db->query("SELECT COUNT(*) AS n FROM ci_orders O
			WHERE O.status = 'Sales' AND O.created_at >= '$s 00:00:00' AND O.created_at <= '$e 23:59:59' AND O.created_by IN ($in)")->row_array();
		return array(
			array('label' => 'Database', 'n' => (int)$st['db'], 'url' => 'manager/database'),
			array('label' => 'Leads', 'n' => (int)$st['lead'], 'url' => 'manager/leads'),
			array('label' => 'Open orders', 'n' => (int)$ord['n'], 'url' => 'manager/orders/open'),
			array('label' => 'Sales this month', 'n' => (int)$sal['n'], 'url' => 'manager/orders'),
		);
	}

	/** Sales by day this month, for the small bar chart. */
	public function sales_by_day($ids, $win)
	{
		$in = $this->in($ids);
		$s = $win['start']; $e = $win['end'];
		$out = array();
		for ($d = 1; $d <= $win['days']; $d++) $out[$d] = 0.0;
		foreach ($this->db->query("SELECT DAY(created_at) AS d, SUM(pricing_amount) AS amt FROM ci_orders
			WHERE status = 'Sales' AND created_at >= '$s 00:00:00' AND created_at <= '$e 23:59:59' AND created_by IN ($in)
			GROUP BY DAY(created_at)")->result_array() as $r) {
			$out[(int)$r['d']] = (float)$r['amt'];
		}
		return $out;
	}

	/* ---------------------------------------------------------------- back office */

	/** Open orders by status, with how many have not moved for more than $late days. */
	public function order_flow($late = 3, $uid = 0)
	{
		$cut = date('Y-m-d H:i:s', strtotime('-' . (int)$late . ' days'));
		$mine = $uid ? " AND O.assigned_to = " . (int)$uid : '';
		return $this->db->query("SELECT O.status, COUNT(*) AS n, SUM(COALESCE(O.updated_at, O.created_at) < '$cut') AS late
			FROM ci_orders O WHERE " . crm_order_open_sql('O') . " AND O.status != 'Cancelled' $mine
			GROUP BY O.status ORDER BY n DESC")->result_array();
	}

	/** Orders not moved for more than $late days, oldest first. */
	public function late_orders($late = 3, $limit = 15)
	{
		$cut = date('Y-m-d H:i:s', strtotime('-' . (int)$late . ' days'));
		return $this->db->query("SELECT O.id, O.order_no, O.client_name, O.company_name, O.service_title, O.status, O.pricing_amount,
				O.assigned_to, U.name AS assignee, C.name AS creator, COALESCE(O.updated_at, O.created_at) AS moved_at
			FROM ci_orders O LEFT JOIN ci_user U ON U.id = O.assigned_to LEFT JOIN ci_user C ON C.id = O.created_by
			WHERE " . crm_order_open_sql('O') . " AND O.status != 'Cancelled' AND COALESCE(O.updated_at, O.created_at) < '$cut'
			ORDER BY moved_at ASC LIMIT " . (int)$limit)->result_array();
	}

	/** Items waiting for a Back Office or KAM decision. */
	public function pending_reviews()
	{
		$c = $this->db->query("SELECT SUM(conversion_status = 'pending') AS conv, SUM(order_request_status = 'pending') AS ord FROM ci_pipeline")->row_array();
		$s = $this->db->query("SELECT COUNT(*) AS n FROM ci_orders WHERE status = 'Pending Sales Approval'")->row_array();
		return array(
			array('label' => 'Lead conversions to review', 'n' => (int)$c['conv'], 'url' => 'manager/leads/review_leads'),
			array('label' => 'Order requests to review', 'n' => (int)$c['ord'], 'url' => 'manager/leads/review_leads'),
			array('label' => 'Waiting for sales approval', 'n' => (int)$s['n'], 'url' => 'manager/orders/pending_sales_approval'),
		);
	}

	/** Orders moved today / this month (Back Office output). */
	public function orders_done($win)
	{
		$today = date('Y-m-d');
		$s = $win['start']; $e = $win['end'];
		$r = $this->db->query("SELECT
				SUM(created_at >= '$today 00:00:00') AS new_today,
				SUM(status = 'Sales' AND COALESCE(sales_converted_at, updated_at) >= '$today 00:00:00') AS sales_today,
				SUM(status = 'Sales' AND COALESCE(sales_converted_at, updated_at) >= '$s 00:00:00' AND COALESCE(sales_converted_at, updated_at) <= '$e 23:59:59') AS sales_month
			FROM ci_orders")->row_array();
		return array('new_today' => (int)$r['new_today'], 'sales_today' => (int)$r['sales_today'], 'sales_month' => (int)$r['sales_month']);
	}

	/* ---------------------------------------------------------------- KAM */

	/** KAM clients of this person with last contact (follow-up logged or call) and open tasks. */
	public function kam_clients($uid, $all = false)
	{
		$uid = (int)$uid;
		$w = $all ? '1=1' : "(K.executive_id = $uid OR ((K.executive_id IS NULL OR K.executive_id = 0) AND A.lead_assign = $uid))";
		$rows = $this->db->query("SELECT K.lead_id, COALESCE(NULLIF(K.client_name, ''), A.customer) AS client, K.group_name, K.executive_name,
				A.lead_id AS ref, A.followup_date, A.mrr,
				(SELECT MAX(F.followup_date) FROM ci_followup F WHERE F.lead_id = K.lead_id AND F.followup_date >= '2000-01-01') AS last_fu,
				(SELECT MAX(DATE(L.created_at)) FROM ci_call_logs L WHERE L.lead_id = K.lead_id) AS last_call
			FROM ci_kam_client_details K LEFT JOIN ci_leads A ON A.id = K.lead_id
			WHERE $w ORDER BY client")->result_array();
		$tasks = array();
		foreach ($this->db->query("SELECT client_id, COUNT(*) AS n, SUM(followup_date >= '2000-01-01' AND followup_date < CURDATE()) AS late
			FROM ci_tasks WHERE client_id > 0 AND (status IS NULL OR status NOT IN ('Completed','Closed','Done','Cancelled'))
			GROUP BY client_id")->result_array() as $t) {
			$tasks[(int)$t['client_id']] = $t;
		}
		$today = strtotime(date('Y-m-d'));
		foreach ($rows as &$r) {
			$last = max((string)$r['last_fu'], (string)$r['last_call']);
			$r['last_contact'] = $last !== '' ? $last : null;
			$r['days_since'] = $last !== '' ? (int)floor(($today - strtotime($last)) / 86400) : null;
			$lid = (int)$r['lead_id'];
			$r['tasks_open'] = isset($tasks[$lid]) ? (int)$tasks[$lid]['n'] : 0;
			$r['tasks_late'] = isset($tasks[$lid]) ? (int)$tasks[$lid]['late'] : 0;
		}
		unset($r);
		// Longest without contact first (never contacted at the top).
		usort($rows, function ($a, $b) {
			$x = $a['days_since'] === null ? PHP_INT_MAX : $a['days_since'];
			$y = $b['days_since'] === null ? PHP_INT_MAX : $b['days_since'];
			return $y <=> $x;
		});
		return $rows;
	}

	/* ---------------------------------------------------------------- one person (drill-down) */

	public function person_detail($uid)
	{
		$uid = (int)$uid;
		$fu = $this->my_followup_list($uid, 15);
		$tasks = array_values(array_filter($this->open_tasks(array($uid)), function ($t) { return $t['_late']; }));
		$calls = $this->db->query("SELECT L.created_at, L.customer_name, L.phone_number, L.call_status, L.disposition, L.duration, L.lead_id
			FROM ci_call_logs L WHERE L.user_id = $uid ORDER BY L.id DESC LIMIT 8")->result_array();
		$done = $this->db->query("SELECT F.followup_date, F.followup_time, F.followup_remark, F.followup_result, F.lead_id, F.lead_type, A.customer
			FROM ci_followup F LEFT JOIN ci_leads A ON A.id = F.lead_id AND F.lead_type = 'lead'
			WHERE F.user_id = $uid ORDER BY F.id DESC LIMIT 8")->result_array();
		return array('followups' => $fu, 'late_tasks' => array_slice($tasks, 0, 10), 'calls' => $calls, 'done' => $done);
	}

	private function in($ids)
	{
		$ids = array_values(array_unique(array_map('intval', (array)$ids)));
		return $ids ? implode(',', $ids) : '0';
	}
}
