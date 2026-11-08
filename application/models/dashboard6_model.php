<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Classic 6: one page with a view per department.
 *   a = Admin (whole company), s = Sales (Classic 5 content), b = Back Office (order processing), k = KAM (clients, upselling).
 * Every number is a real query; nothing is filled in when a table is empty.
 */
class Dashboard6_model extends CI_Model {

	private $uid = 0;
	private $is_all = false;
	private $today = '';

	public function __construct()
	{
		parent::__construct();
		$this->load->model('ci_admin_model');
		$this->load->model('dashboard_model', 'dash');
		$this->uid = (int)$this->session->userdata('manager_id');
		$this->is_all = crm_is_full_admin() || crm_can('all_data');
		$this->today = date('Y-m-d');
	}

	/** View names in the order they show on the switch. */
	public function names()
	{
		return array('a' => 'Admin', 's' => 'Sales', 'b' => 'Back Office', 'k' => 'KAM');
	}

	/**
	 * Views this user may open, and which one opens first.
	 * Admin sees all four. Everyone else gets their own department's view plus Sales (sales is open to every role),
	 * and any view given under Special rights (Dashboard: Company / Back Office / KAM view).
	 */
	public function allowed()
	{
		$names = $this->names();
		$own = substr($this->dash->role_key((int)$this->session->userdata('manager_role')), 0, 1);
		if (crm_is_full_admin()) return array('views' => array_keys($names), 'own' => 'a');
		$v = array($own, 's');
		if (crm_can('dash6_company')) $v[] = 'a';
		if (crm_can('dash6_backoffice')) $v[] = 'b';
		if (crm_can('dash6_kam')) $v[] = 'k';
		$v = array_values(array_intersect(array_keys($names), array_unique($v)));
		return array('views' => $v, 'own' => $own);
	}

	/* ---------------------------------------------------------------- shared: follow-ups and meetings */

	/** People whose follow-ups and meetings count as "Team" here. */
	private function team_ids()
	{
		return $this->dash->scope_ids($this->uid, $this->is_all);
	}

	private function in($ids)
	{
		$ids = array_values(array_unique(array_map('intval', (array)$ids)));
		return $ids ? implode(',', $ids) : '0';
	}

	/** Same owner rule as the Follow-ups and Meetings pages: assigned, created and unassigned, or co-agent. */
	private function mine_sql($a = 'a')
	{
		$u = $this->uid;
		return "({$a}.lead_assign = $u OR ({$a}.created_by = $u AND ({$a}.lead_assign = 0 OR {$a}.lead_assign IS NULL)) OR ({$a}.agents IS NOT NULL AND {$a}.agents != '' AND FIND_IN_SET($u, {$a}.agents) > 0))";
	}

	private function open_sql($a = 'a')
	{
		return "{$a}.status NOT IN (42,43,44) AND ({$a}.company_status IS NULL OR LOWER({$a}.company_status) != 'closed')";
	}

	/**
	 * Today's agenda: follow-ups and meetings due today, plus missed ones from the last 7 days.
	 * Mine, or the team's when $team is true.
	 */
	public function agenda($team = false, $limit = 14)
	{
		$from = date('Y-m-d', strtotime('-7 days'));
		$who = $team ? ("(CASE WHEN a.lead_assign > 0 THEN a.lead_assign ELSE a.created_by END) IN (" . $this->in($this->team_ids()) . ")") : $this->mine_sql('a');
		$t = $this->today;
		$rows = $this->db->query("SELECT * FROM (
				SELECT 'fu' AS kind, a.id, a.lead_id, a.customer, a.poc, a.followup_date AS d, a.followup_time AS tm, U.name AS owner
				FROM ci_leads a LEFT JOIN ci_user U ON U.id = (CASE WHEN a.lead_assign > 0 THEN a.lead_assign ELSE a.created_by END)
				WHERE " . $this->open_sql('a') . " AND a.followup_date >= '$from' AND a.followup_date <= '$t' AND $who
				UNION ALL
				SELECT 'mt' AS kind, a.id, a.lead_id, a.customer, a.poc, a.m_date AS d, a.m_time AS tm, U.name AS owner
				FROM ci_leads a LEFT JOIN ci_user U ON U.id = (CASE WHEN a.lead_assign > 0 THEN a.lead_assign ELSE a.created_by END)
				WHERE " . $this->open_sql('a') . " AND a.m_date != '' AND a.m_date >= '$from' AND a.m_date <= '$t' AND $who
			) X ORDER BY (X.d = '$t') DESC, X.d ASC, X.tm ASC LIMIT " . (int)$limit)->result_array();
		foreach ($rows as &$r) $r['late'] = $r['d'] < $t;
		unset($r);
		return $rows;
	}

	/** Meetings coming up in the next 7 days (mine, or the team's). */
	public function meetings_next($team = false, $limit = 8)
	{
		$to = date('Y-m-d', strtotime('+7 days'));
		$t = $this->today;
		$who = $team ? ("(CASE WHEN a.lead_assign > 0 THEN a.lead_assign ELSE a.created_by END) IN (" . $this->in($this->team_ids()) . ")") : $this->mine_sql('a');
		return $this->db->query("SELECT a.id, a.lead_id, a.customer, a.poc, a.m_date AS d, a.m_time AS tm, U.name AS owner
			FROM ci_leads a LEFT JOIN ci_user U ON U.id = (CASE WHEN a.lead_assign > 0 THEN a.lead_assign ELSE a.created_by END)
			WHERE " . $this->open_sql('a') . " AND a.m_date != '' AND a.m_date > '$t' AND a.m_date <= '$to' AND $who
			ORDER BY a.m_date ASC, a.m_time ASC LIMIT " . (int)$limit)->result_array();
	}

	/**
	 * Team (or everyone, for Admin and All company data) follow-ups or meetings by date, same buckets as the Classic cards.
	 * Counted by owner: the assigned person, else the creator.
	 */
	public function team_counts($col)
	{
		$col = $col === 'm_date' ? 'm_date' : 'followup_date';
		$t = $this->today;
		$l = date('Y-m-d', strtotime('-7 days'));
		$n = date('Y-m-d', strtotime('+7 days'));
		$in = $this->in($this->team_ids());
		$r = $this->db->query("SELECT
				SUM(a.$col < '$t') AS total_missed, SUM(a.$col < '$t' AND a.$col >= '$l') AS total_lastweek,
				SUM(a.$col = '$t') AS total_today, SUM(a.$col > '$t' AND a.$col <= '$n') AS total_nextweek, SUM(a.$col > '$t') AS total_future
			FROM ci_leads a WHERE " . $this->open_sql('a') . " AND a.$col >= '2000-01-01'
				AND (CASE WHEN a.lead_assign > 0 THEN a.lead_assign ELSE a.created_by END) IN ($in)")->row_array();
		return array_map('intval', (array)$r);
	}

	/** Follow-ups logged (done) today and in the last 7 days: mine and the team's. */
	public function fu_done()
	{
		$t = $this->today;
		$w = date('Y-m-d', strtotime('-6 days'));
		$in = $this->in($this->team_ids());
		$u = $this->uid;
		$r = $this->db->query("SELECT
				SUM(user_id = $u AND followup_date = '$t') AS my_today, SUM(user_id = $u AND followup_date >= '$w') AS my_week,
				SUM(followup_date = '$t') AS team_today, COUNT(*) AS team_week
			FROM ci_followup WHERE followup_date >= '$w' AND followup_date <= '$t' AND user_id IN ($in)")->row_array();
		return array_map('intval', (array)$r);
	}

	/* ---------------------------------------------------------------- 6A: whole company */

	public function company()
	{
		$win = $this->dash->month_window(date('Y-m'));
		$s = $win['start']; $e = $win['end']; $t = $this->today;
		$offices = function_exists('crm_office_codes') ? crm_office_codes() : array();
		$own = "(CASE WHEN A.lead_assign > 0 THEN A.lead_assign ELSE A.created_by END)";
		$pid = function_exists('crm_presentation_role_id') ? (int)crm_presentation_role_id() : 0;
		$staff_w = "U.status = 1" . ($pid > 0 ? " AND U.user_type != $pid" : '');

		// Totals for the tiles.
		$tot = $this->db->query("SELECT
				SUM(COALESCE(P.pipeline_stage, 'database') = 'database') AS db,
				SUM(" . crm_stage_lead_sql('A', 'P') . ") AS leads,
				SUM(A.followup_date >= '2000-01-01' AND A.followup_date < '$t') AS fu_missed
			FROM ci_leads A LEFT JOIN ci_pipeline P ON P.lead_id = A.id WHERE " . $this->open_sql('A'))->row_array();
		$ord = $this->db->query("SELECT
				SUM(" . crm_order_open_sql('O') . " AND O.status != 'Cancelled') AS open_n,
				SUM(" . crm_order_open_sql('O') . " AND O.status != 'Cancelled' AND COALESCE(O.updated_at, O.created_at) < '" . date('Y-m-d H:i:s', strtotime('-3 days')) . "') AS late_n,
				SUM(" . crm_order_is_sale_sql('O') . " AND COALESCE(O.sales_converted_at, O.created_at) >= '$s 00:00:00' AND COALESCE(O.sales_converted_at, O.created_at) <= '$e 23:59:59') AS sales_n,
				SUM(CASE WHEN " . crm_order_is_sale_sql('O') . " AND COALESCE(O.sales_converted_at, O.created_at) >= '$s 00:00:00' AND COALESCE(O.sales_converted_at, O.created_at) <= '$e 23:59:59' THEN O.pricing_amount ELSE 0 END) AS sales_amt
			FROM ci_orders O")->row_array();
		$staff = $this->db->query("SELECT COUNT(*) AS n, SUM(U.is_online = 1) AS online FROM ci_user U WHERE $staff_w")->row_array();
		$target = $this->db->query("SELECT SUM(target_amount) AS amt FROM ci_hr_targets WHERE status = 'Active' AND period_month = " . $this->db->escape($win['ym']))->row_array();

		// By branch (the office set on each user).
		$branch = array();
		$add = function (&$list, $key, $label) { if (!isset($list[$key])) $list[$key] = array('label' => $label, 'staff' => 0, 'online' => 0, 'db' => 0, 'leads' => 0, 'fu_missed' => 0, 'open' => 0, 'sales_n' => 0, 'sales_amt' => 0.0); };
		foreach ($this->db->query("SELECT COALESCE(NULLIF(U.office, ''), '') AS k, COUNT(*) AS n, SUM(U.is_online = 1) AS online FROM ci_user U WHERE $staff_w GROUP BY k")->result_array() as $r) {
			$add($branch, $r['k'], $r['k'] === '' ? 'Not set' : $r['k']);
			$branch[$r['k']]['staff'] = (int)$r['n']; $branch[$r['k']]['online'] = (int)$r['online'];
		}
		foreach ($this->db->query("SELECT COALESCE(NULLIF(U.office, ''), '') AS k,
				SUM(COALESCE(P.pipeline_stage, 'database') = 'database') AS db, SUM(" . crm_stage_lead_sql('A', 'P') . ") AS leads,
				SUM(A.followup_date >= '2000-01-01' AND A.followup_date < '$t') AS fu_missed
			FROM ci_leads A LEFT JOIN ci_pipeline P ON P.lead_id = A.id LEFT JOIN ci_user U ON U.id = $own
			WHERE " . $this->open_sql('A') . " GROUP BY k")->result_array() as $r) {
			$add($branch, $r['k'], $r['k'] === '' ? 'Not set' : $r['k']);
			$branch[$r['k']]['db'] = (int)$r['db']; $branch[$r['k']]['leads'] = (int)$r['leads']; $branch[$r['k']]['fu_missed'] = (int)$r['fu_missed'];
		}
		foreach ($this->db->query("SELECT COALESCE(NULLIF(U.office, ''), '') AS k,
				SUM(" . crm_order_open_sql('O') . " AND O.status != 'Cancelled') AS open_n,
				SUM(" . crm_order_is_sale_sql('O') . " AND COALESCE(O.sales_converted_at, O.created_at) >= '$s 00:00:00' AND COALESCE(O.sales_converted_at, O.created_at) <= '$e 23:59:59') AS sales_n,
				SUM(CASE WHEN " . crm_order_is_sale_sql('O') . " AND COALESCE(O.sales_converted_at, O.created_at) >= '$s 00:00:00' AND COALESCE(O.sales_converted_at, O.created_at) <= '$e 23:59:59' THEN O.pricing_amount ELSE 0 END) AS sales_amt
			FROM ci_orders O LEFT JOIN ci_user U ON U.id = O.created_by GROUP BY k")->result_array() as $r) {
			$add($branch, $r['k'], $r['k'] === '' ? 'Not set' : $r['k']);
			$branch[$r['k']]['open'] = (int)$r['open_n']; $branch[$r['k']]['sales_n'] = (int)$r['sales_n']; $branch[$r['k']]['sales_amt'] = (float)$r['sales_amt'];
		}
		// Branches from the office list first (in their order), then anything else, "Not set" last.
		$sorted = array();
		foreach ((array)$offices as $k => $v) { $code = is_int($k) ? $v : $k; if (isset($branch[$code])) { $sorted[$code] = $branch[$code]; unset($branch[$code]); } }
		$nos = isset($branch['']) ? $branch[''] : null; unset($branch['']);
		$sorted += $branch;
		if ($nos) $sorted[''] = $nos;

		// By business category (the vertical on the order).
		$cat = $this->db->query("SELECT LOWER(COALESCE(NULLIF(O.vertical_code, ''), 'other')) AS k, MAX(O.service_title) AS service,
				COUNT(*) AS n,
				SUM(" . crm_order_open_sql('O') . " AND O.status != 'Cancelled') AS open_n,
				SUM(" . crm_order_is_sale_sql('O') . ") AS sales_all,
				SUM(" . crm_order_is_sale_sql('O') . " AND COALESCE(O.sales_converted_at, O.created_at) >= '$s 00:00:00' AND COALESCE(O.sales_converted_at, O.created_at) <= '$e 23:59:59') AS sales_n,
				SUM(CASE WHEN " . crm_order_is_sale_sql('O') . " AND COALESCE(O.sales_converted_at, O.created_at) >= '$s 00:00:00' AND COALESCE(O.sales_converted_at, O.created_at) <= '$e 23:59:59' THEN O.pricing_amount ELSE 0 END) AS sales_amt,
				SUM(O.status = 'Cancelled') AS cancelled
			FROM ci_orders O GROUP BY k ORDER BY n DESC")->result_array();
		$cat_names = array('accounting' => 'Accounting', 'telecom' => 'Telecom', 'it' => 'IT', 'other' => 'Other');
		foreach ($cat as &$c) $c['label'] = isset($cat_names[$c['k']]) ? $cat_names[$c['k']] : ucwords($c['k']);
		unset($c);

		// By department (role).
		$dept = array();
		foreach ($this->db->query("SELECT R.id, R.title, COUNT(U.id) AS n, SUM(U.is_online = 1) AS online
			FROM ci_user U LEFT JOIN ci_roles R ON R.id = U.user_type WHERE $staff_w GROUP BY R.id, R.title ORDER BY n DESC")->result_array() as $r) {
			$dept[(int)$r['id']] = array('label' => $r['title'] ? $r['title'] : 'No role', 'staff' => (int)$r['n'], 'online' => (int)$r['online'], 'leads' => 0, 'fu_missed' => 0, 'sales_n' => 0, 'sales_amt' => 0.0, 'calls' => 0);
		}
		foreach ($this->db->query("SELECT U.user_type AS k, SUM(" . crm_stage_lead_sql('A', 'P') . ") AS leads,
				SUM(A.followup_date >= '2000-01-01' AND A.followup_date < '$t') AS fu_missed
			FROM ci_leads A LEFT JOIN ci_pipeline P ON P.lead_id = A.id JOIN ci_user U ON U.id = $own
			WHERE " . $this->open_sql('A') . " AND $staff_w GROUP BY U.user_type")->result_array() as $r) {
			if (isset($dept[(int)$r['k']])) { $dept[(int)$r['k']]['leads'] = (int)$r['leads']; $dept[(int)$r['k']]['fu_missed'] = (int)$r['fu_missed']; }
		}
		foreach ($this->db->query("SELECT U.user_type AS k, COUNT(*) AS n, SUM(O.pricing_amount) AS amt
			FROM ci_orders O JOIN ci_user U ON U.id = O.created_by
			WHERE " . crm_order_is_sale_sql('O') . " AND COALESCE(O.sales_converted_at, O.created_at) >= '$s 00:00:00' AND COALESCE(O.sales_converted_at, O.created_at) <= '$e 23:59:59' AND $staff_w
			GROUP BY U.user_type")->result_array() as $r) {
			if (isset($dept[(int)$r['k']])) { $dept[(int)$r['k']]['sales_n'] = (int)$r['n']; $dept[(int)$r['k']]['sales_amt'] = (float)$r['amt']; }
		}
		foreach ($this->db->query("SELECT U.user_type AS k, COUNT(*) AS n FROM ci_call_logs L JOIN ci_user U ON U.id = L.user_id
			WHERE L.created_at >= '$t 00:00:00' AND L.created_at <= '$t 23:59:59' AND $staff_w GROUP BY U.user_type")->result_array() as $r) {
			if (isset($dept[(int)$r['k']])) $dept[(int)$r['k']]['calls'] = (int)$r['n'];
		}

		// Department health: Back Office queue and KAM contact.
		$kam = $this->kam_summary($this->dash->kam_clients($this->uid, true));

		// People to check: most missed follow-ups and late tasks first.
		$track = array_slice(array_values(array_filter($this->dash->tracker($this->team_ids(), $win), function ($r) { return $r['fu_overdue'] > 0 || $r['tasks_late'] > 0; })), 0, 8);

		return array(
			'tiles' => array(
				'staff' => (int)$staff['n'], 'online' => (int)$staff['online'],
				'db' => (int)$tot['db'], 'leads' => (int)$tot['leads'], 'fu_missed' => (int)$tot['fu_missed'],
				'open' => (int)$ord['open_n'], 'late' => (int)$ord['late_n'],
				'sales_n' => (int)$ord['sales_n'], 'sales_amt' => (float)$ord['sales_amt'], 'target' => (float)$target['amt'],
			),
			'branch' => array_values($sorted),
			'category' => $cat,
			'dept' => array_values($dept),
			'flow' => $this->dash->order_flow(3),
			'kam' => $kam,
			'track' => $track,
			'month' => date('M-y'),
		);
	}

	/* ---------------------------------------------------------------- 6B: Back Office */

	public function backoffice()
	{
		$win = $this->dash->month_window(date('Y-m'));
		$flow = $this->dash->order_flow(3);
		$open = 0; $late = 0;
		foreach ($flow as $f) { $open += (int)$f['n']; $late += (int)$f['late']; }
		$mine = 0;
		foreach ($this->dash->order_flow(3, $this->uid) as $f) $mine += (int)$f['n'];
		$reviews = $this->dash->pending_reviews();
		$rv = 0;
		foreach ($reviews as $r) $rv += (int)$r['n'];

		// Documents: open orders whose checklist still has items pending.
		$docs = array('orders' => 0, 'items' => 0, 'list' => array());
		foreach ($this->db->query("SELECT O.id, O.order_no, O.client_name, O.company_name, O.status, O.documents_status, U.name AS assignee
			FROM ci_orders O LEFT JOIN ci_user U ON U.id = O.assigned_to
			WHERE " . crm_order_open_sql('O') . " AND O.status != 'Cancelled' AND O.documents_status IS NOT NULL AND O.documents_status != ''
			ORDER BY O.created_at ASC")->result_array() as $o) {
			$d = json_decode((string)$o['documents_status'], true);
			if (!is_array($d)) continue;
			$pend = array();
			foreach ($d as $it) { if (is_array($it) && (isset($it['status']) ? $it['status'] : '') !== 'collected') $pend[] = isset($it['title']) ? $it['title'] : ''; }
			if (!$pend) continue;
			$docs['orders']++; $docs['items'] += count($pend);
			if (count($docs['list']) < 8) $docs['list'][] = array('id' => (int)$o['id'], 'order_no' => $o['order_no'], 'client' => $o['client_name'] ? $o['client_name'] : $o['company_name'], 'status' => $o['status'], 'pending' => $pend, 'total' => count($d), 'assignee' => $o['assignee']);
		}

		// Moved to KAM / sale: orders in Onboarding or Pending Sales Approval, and sales made this month.
		$s = $win['start']; $e = $win['end'];
		$hand = $this->db->query("SELECT SUM(status = 'Onboarding') AS onboarding, SUM(status = 'Pending Sales Approval') AS approval,
				SUM(created_at >= '$s 00:00:00' AND created_at <= '$e 23:59:59') AS new_month
			FROM ci_orders")->row_array();

		// Orders received per week (8 weeks) for the trend line.
		$weeks = array();
		$mon = strtotime('monday this week');
		for ($i = 7; $i >= 0; $i--) { $f = date('Y-m-d', strtotime('-' . $i . ' weeks', $mon)); $weeks[$f] = array('from' => $f, 'to' => date('Y-m-d', strtotime($f . ' +6 days')), 'label' => date('d M', strtotime($f)), 'n' => 0); }
		$first = key($weeks);
		foreach ($this->db->query("SELECT DATE(created_at) AS d, COUNT(*) AS n FROM ci_orders WHERE created_at >= '$first 00:00:00' GROUP BY DATE(created_at)")->result_array() as $r) {
			foreach ($weeks as $k => $w) { if ($r['d'] >= $w['from'] && $r['d'] <= $w['to']) { $weeks[$k]['n'] += (int)$r['n']; break; } }
		}

		// Open orders by business category.
		$cat = $this->db->query("SELECT LOWER(COALESCE(NULLIF(vertical_code, ''), 'other')) AS k, COUNT(*) AS n
			FROM ci_orders O WHERE " . crm_order_open_sql('O') . " AND O.status != 'Cancelled' GROUP BY k ORDER BY n DESC")->result_array();

		return array(
			'tiles' => array('open' => $open, 'late' => $late, 'mine' => $mine, 'reviews' => $rv, 'done' => $this->dash->orders_done($win)),
			'flow' => $flow,
			'late_list' => $this->dash->late_orders(3, 8),
			'reviews' => $reviews,
			'docs' => $docs,
			'hand' => array_map('intval', (array)$hand),
			'weekly' => array_values($weeks),
			'category' => $cat,
		);
	}

	/* ---------------------------------------------------------------- 6K: KAM */

	private function kam_summary($clients)
	{
		$b = array('d7' => 0, 'd30' => 0, 'older' => 0, 'never' => 0);
		$tasks = 0; $tasks_late = 0;
		foreach ($clients as $c) {
			if ($c['days_since'] === null) $b['never']++;
			elseif ($c['days_since'] <= 7) $b['d7']++;
			elseif ($c['days_since'] <= 30) $b['d30']++;
			else $b['older']++;
			$tasks += (int)$c['tasks_open']; $tasks_late += (int)$c['tasks_late'];
		}
		return array('total' => count($clients), 'buckets' => $b, 'tasks' => $tasks, 'tasks_late' => $tasks_late);
	}

	public function kam()
	{
		// My Clients rule: a client shows only to its account manager. Admin's own view here is the same rule.
		$clients = $this->dash->kam_clients($this->uid, false);
		$sum = $this->kam_summary($clients);
		$ids = array_map(function ($c) { return (int)$c['lead_id']; }, $clients);
		$in = $this->in($ids);
		$win = $this->dash->month_window(date('Y-m'));
		$s = $win['start']; $e = $win['end'];

		if (!$ids) {
			// No clients: nothing to count (an empty list must not match orders or tasks with no client).
			$wait = $this->db->query("SELECT SUM(status = 'Onboarding') AS onboarding, SUM(status = 'Pending Sales Approval') AS approval FROM ci_orders")->row_array();
			return array('sum' => $sum, 'list' => array(), 'upsell' => array('open' => 0, 'sales_n' => 0, 'sales_amt' => 0.0, 'clients' => 0, 'one_service' => 0),
				'wait' => array_map('intval', (array)$wait), 'tasks' => array());
		}
		// Upselling: orders on my existing clients made after onboarding (onboarded date, else when the KAM client record was made).
		$up = $this->db->query("SELECT
				SUM(" . crm_order_open_sql('O') . " AND O.status != 'Cancelled') AS open_n,
				SUM(" . crm_order_is_sale_sql('O') . " AND COALESCE(O.sales_converted_at, O.created_at) >= '$s 00:00:00' AND COALESCE(O.sales_converted_at, O.created_at) <= '$e 23:59:59') AS sales_n,
				SUM(CASE WHEN " . crm_order_is_sale_sql('O') . " AND COALESCE(O.sales_converted_at, O.created_at) >= '$s 00:00:00' AND COALESCE(O.sales_converted_at, O.created_at) <= '$e 23:59:59' THEN O.pricing_amount ELSE 0 END) AS sales_amt,
				COUNT(DISTINCT O.lead_id) AS clients
			FROM ci_orders O JOIN ci_kam_client_details K ON K.lead_id = O.lead_id LEFT JOIN ci_pipeline P ON P.lead_id = O.lead_id
			WHERE O.lead_id IN ($in) AND O.created_at > COALESCE(P.onboarded_at, K.created_at)")->row_array();
		$services = array();
		foreach ($this->db->query("SELECT O.lead_id, COUNT(DISTINCT LOWER(COALESCE(NULLIF(O.vertical_code, ''), 'other'))) AS n
			FROM ci_orders O WHERE O.lead_id IN ($in) AND O.status != 'Cancelled' GROUP BY O.lead_id")->result_array() as $r) {
			$services[(int)$r['lead_id']] = (int)$r['n'];
		}
		$one = 0;
		foreach ($ids as $id) { if (!isset($services[$id]) || $services[$id] <= 1) $one++; }

		// Waiting on KAM: orders to onboard / approve as a sale.
		$wait = $this->db->query("SELECT SUM(status = 'Onboarding') AS onboarding, SUM(status = 'Pending Sales Approval') AS approval FROM ci_orders")->row_array();

		// Open tasks on my clients.
		$tasks = $this->db->query("SELECT T.id, T.task_id, T.customer, T.category, T.status, T.followup_date
			FROM ci_tasks T WHERE T.client_id IN ($in) AND (T.status IS NULL OR T.status NOT IN ('Completed','Closed','Done','Cancelled'))
			ORDER BY T.followup_date ASC LIMIT 8")->result_array();

		return array(
			'sum' => $sum,
			'list' => array_slice($clients, 0, 10),
			'upsell' => array('open' => (int)$up['open_n'], 'sales_n' => (int)$up['sales_n'], 'sales_amt' => (float)$up['sales_amt'], 'clients' => (int)$up['clients'], 'one_service' => $one),
			'wait' => array_map('intval', (array)$wait),
			'tasks' => $tasks,
		);
	}

	/** Everything the chosen view needs. */
	public function build($view)
	{
		$out = array('view' => $view);
		if ($view === 's') return $out;
		$out['agenda_my'] = $this->agenda(false);
		$out['agenda_team'] = $this->agenda(true);
		$out['meet_next'] = $this->meetings_next(false);
		$out['meet_next_team'] = $this->meetings_next(true);
		$out['fu_done'] = $this->fu_done();
		$out['fu_team'] = $this->team_counts('followup_date');
		$out['m_team'] = $this->team_counts('m_date');
		$out['is_all'] = $this->is_all;
		if ($view === 'a') $out['co'] = $this->company();
		if ($view === 'b') $out['bo'] = $this->backoffice();
		if ($view === 'k') $out['km'] = $this->kam();
		return $out;
	}
}
