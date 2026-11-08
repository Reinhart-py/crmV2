<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Analysis reports: Sales, Back Office, KAM and Admin reports with a chart and a table each.
 * Same rules as the Classic 6 / Modern 6 dashboards:
 *   Sales     - me and my team (everyone for Admin or "All company data").
 *   Back Office - every order (order processing is one queue).
 *   KAM       - my own clients (every client for Admin or "All company data").
 *   Admin     - whole company.
 * Every report returns: title, desc, cols [key, label, type], rows, total (or null), chart (or null), note.
 * Column types: text, num, amt, pct, dec, date.
 */
class Analysis_model extends CI_Model
{
	private $uid = 0;
	private $is_all = false;
	private $ids = null;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('ci_admin_model');
		$this->load->model('dashboard_model', 'dash');
		$this->uid = (int)$this->session->userdata('manager_id');
		$this->is_all = crm_is_full_admin() || crm_can('all_data');
	}

	/* ---------------------------------------------------------------- views and catalogue */

	public function names()
	{
		return array('s' => 'Sales', 'b' => 'Back Office', 'k' => 'KAM', 'a' => 'Admin');
	}

	/**
	 * Views this user may open. Admin: all. Others: their own department's view plus Sales,
	 * and any view given in Access Rights (Reports: Admin / Back Office / KAM reports).
	 */
	public function allowed()
	{
		$names = $this->names();
		$own = substr($this->dash->role_key((int)$this->session->userdata('manager_role')), 0, 1);
		if (crm_is_full_admin()) return array('views' => array_keys($names), 'own' => 'a');
		$v = array($own, 's');
		if (crm_can('rep_company')) $v[] = 'a';
		if (crm_can('rep_backoffice')) $v[] = 'b';
		if (crm_can('rep_kam')) $v[] = 'k';
		return array('views' => array_values(array_intersect(array_keys($names), array_unique($v))), 'own' => $own);
	}

	/** Reports per view: slug => [title, icon, short description, uses the date range, can pick one person]. */
	public function catalog()
	{
		return array(
			's' => array(
				'sales-month' => array('Sales by Month', 'fa-line-chart', 'Orders, sales and target for the 12 months up to the end date.', false, true),
				'sales-person' => array('Sales by Person', 'fa-trophy', 'Orders, sales, average sale and target for each person.', true, false),
				'target-6m' => array('Target Achievement, 6 Months', 'fa-crosshairs', '% of target reached by each person, month by month.', false, false),
				'pipeline-person' => array('Pipeline by Person', 'fa-filter', 'Database, leads, open orders and sales each person holds now.', false, false),
				'followups-person' => array('Follow-up Discipline', 'fa-calendar-check-o', 'Follow-ups logged in the period and follow-ups missed now, by person.', true, false),
				'calls-person' => array('Calls by Person', 'fa-phone', 'Calls made, answered, answer rate and talk time.', true, false),
				'activity-day' => array('Daily Activity', 'fa-bar-chart', 'Records added, follow-ups logged, calls and orders, day by day.', true, true),
				'lead-ageing' => array('Lead Ageing by Person', 'fa-hourglass-half', 'Open leads by days since the last follow-up.', false, false),
				'lead-status' => array('Leads by Status', 'fa-tags', 'Records added in the period, by their status now.', true, true),
				'lost-leads' => array('Lost Leads by Reason', 'fa-times-circle', 'Leads marked Lost, by the reason given.', true, true),
				'record-category' => array('Records by Database Category', 'fa-database', 'Records added in the period, by database category.', true, true),
				'category-sales' => array('Sales by Business Category', 'fa-pie-chart', 'Orders and sales by business category and service.', true, true),
				'top-clients' => array('Top Clients', 'fa-star', 'Clients with the most sales in the period.', true, true),
			),
			'b' => array(
				'orders-status' => array('Open Orders by Stage and Age', 'fa-tasks', 'Open orders in each stage, by days since they last moved.', false, false),
				'orders-trend' => array('Orders Received and Completed', 'fa-line-chart', 'Received, completed and cancelled, week by week (month by month for long periods).', true, false),
				'processing-time' => array('Processing Time', 'fa-clock-o', 'Days from order to sale for orders completed in the period, by category.', true, false),
				'workload' => array('Workload by Assignee', 'fa-users', 'Open, late, completed and cancelled orders for each person.', true, false),
				'documents' => array('Documents Pending', 'fa-file-text-o', 'Documents still to collect on open orders, by document.', false, false),
				'orders-category' => array('Orders by Category and Status', 'fa-th', 'Orders received in the period: category against status.', true, false),
				'cancelled' => array('Cancelled Orders', 'fa-ban', 'Orders cancelled in the period.', true, false),
				'order-register' => array('Order Register', 'fa-list', 'Every order received in the period with its status and age.', true, false),
			),
			'k' => array(
				'client-contact' => array('Client Contact Status', 'fa-handshake-o', 'Every client with the last contact, next follow-up and open tasks.', false, false),
				'kam-summary' => array('Clients by Account Manager', 'fa-sitemap', 'Clients, contact gaps and open tasks for each account manager.', false, false),
				'upsell-month' => array('Upselling by Month', 'fa-level-up', 'Sales on clients after onboarding, 12 months up to the end date.', false, false),
				'client-orders' => array('Orders by Client', 'fa-shopping-cart', 'Orders and sales on each client in the period.', true, false),
				'one-service' => array('Clients With One Service', 'fa-plus-circle', 'Clients that buy only one kind of service: room to offer more.', false, false),
				'client-tasks' => array('Client Tasks', 'fa-check-square-o', 'Open and late tasks on clients, by category and status.', false, false),
			),
			'a' => array(
				'branch' => array('Branch Summary', 'fa-building-o', 'Activity, orders, sales and target for each branch.', true, false),
				'department' => array('Department Summary', 'fa-sitemap', 'Activity, orders and sales for each department (role).', true, false),
				'staff-activity' => array('Staff Activity', 'fa-user', 'Records, follow-ups, calls, orders and sales for every person.', true, false),
				'company-trend' => array('Company Trend, 12 Months', 'fa-area-chart', 'Records, follow-ups, calls, orders and sales, month by month.', false, false),
				'no-activity' => array('Staff With No Activity', 'fa-user-times', 'People with no records, follow-ups or calls in the period.', true, false),
				'people-check' => array('Missed Follow-ups and Late Tasks', 'fa-exclamation-triangle', 'Who is behind now, most behind first.', false, false),
			),
		);
	}

	/* ---------------------------------------------------------------- helpers */

	private function in($ids)
	{
		$ids = array_values(array_unique(array_filter(array_map('intval', (array)$ids), function ($x) { return $x > 0; })));
		return $ids ? implode(',', $ids) : '0';
	}

	/** People in my scope (Admin: every active user). */
	public function scope()
	{
		if ($this->ids === null) $this->ids = $this->dash->scope_ids($this->uid, $this->is_all);
		return $this->ids;
	}

	/** Active people in scope: id => [name, office, role]. */
	public function people($ids = null)
	{
		$ids = $ids === null ? $this->scope() : $ids;
		if (!$ids) return array();
		$out = array();
		foreach ($this->db->query("SELECT U.id, U.name, U.office, U.user_type, U.last_active_at, R.title AS role FROM ci_user U LEFT JOIN ci_roles R ON R.id = U.user_type
			WHERE U.status = 1 AND U.id IN (" . $this->in($ids) . ") ORDER BY U.name")->result_array() as $r) $out[(int)$r['id']] = $r;
		return $out;
	}

	public function is_all() { return $this->is_all; }

	private function owner($a = 'A') { return "(CASE WHEN {$a}.lead_assign > 0 THEN {$a}.lead_assign ELSE {$a}.created_by END)"; }
	private function open_lead($a = 'A') { return "({$a}.company_status IS NULL OR LOWER({$a}.company_status) != 'closed')"; }
	private function sd($o = 'O') { return "COALESCE({$o}.sales_converted_at, {$o}.created_at)"; }
	private function done_d($o = 'O') { return "COALESCE({$o}.sales_converted_at, {$o}.updated_at, {$o}.created_at)"; }
	private function t1($d) { return $this->db->escape($d . ' 00:00:00'); }
	private function t2($d) { return $this->db->escape($d . ' 23:59:59'); }
	private function sale($o = 'O') { return crm_order_is_sale_sql($o); }
	private function open_order($o = 'O') { return '(' . crm_order_open_sql($o) . " AND {$o}.status != 'Cancelled')"; }

	/** Run a grouped query and return rows keyed by column k. */
	private function keyed($sql, $k = 'k')
	{
		$out = array();
		foreach ($this->db->query($sql)->result_array() as $r) $out[(string)$r[$k]] = $r;
		return $out;
	}

	/** Months (Y-m) from d1 to d2, or the 12 months ending in d2's month. */
	private function months($d1, $d2, $last12 = false)
	{
		$end = strtotime(date('Y-m-01', strtotime($d2)));
		$start = $last12 ? strtotime('-11 months', $end) : strtotime(date('Y-m-01', strtotime($d1)));
		$out = array();
		for ($t = $start; $t <= $end && count($out) < 36; $t = strtotime('+1 month', $t)) $out[date('Y-m', $t)] = date('M-y', $t);
		return $out;
	}

	private function cat_label($k)
	{
		$n = array('accounting' => 'Accounting', 'telecom' => 'Telecom', 'it' => 'IT', 'other' => 'Other');
		return isset($n[$k]) ? $n[$k] : ucwords((string)$k);
	}

	private function cat_sql($o = 'O') { return "LOWER(COALESCE(NULLIF({$o}.vertical_code, ''), 'other'))"; }

	/** Sum the numeric columns of rows into a total row (label in the first column). */
	private function total($rows, $cols, $label = 'Total')
	{
		if (!$rows) return null;
		$t = array();
		foreach ($cols as $i => $c) {
			if ($i === 0) { $t[$c[0]] = $label; continue; }
			$t[$c[0]] = in_array($c[2], array('num', 'amt'), true) ? array_sum(array_map(function ($r) use ($c) { return isset($r[$c[0]]) ? (float)$r[$c[0]] : 0; }, $rows)) : '';
		}
		return $t;
	}

	private function pct($a, $b) { return $b > 0 ? round($a * 100 / $b) : null; }

	private function chart($type, $labels, $sets, $amt = false)
	{
		return array('type' => $type, 'labels' => array_values($labels), 'amt' => $amt, 'sets' => $sets);
	}

	private function set($label, $color, $data) { return array('label' => $label, 'color' => $color, 'data' => array_map('floatval', array_values($data))); }

	/* ---------------------------------------------------------------- run */

	/**
	 * $f: d1, d2 (Y-m-d), person (0 = everyone in scope).
	 */
	public function run($view, $slug, $f)
	{
		$cat = $this->catalog();
		if (!isset($cat[$view][$slug])) return null;
		$m = 'r_' . $view . '_' . str_replace('-', '_', $slug);
		if (!method_exists($this, $m)) return null;
		$ids = $this->scope();
		if (!empty($f['person']) && in_array((int)$f['person'], array_map('intval', $ids), true)) $ids = array((int)$f['person']);
		$r = $this->$m($f['d1'], $f['d2'], $ids);
		$r += array('title' => $cat[$view][$slug][0], 'desc' => $cat[$view][$slug][2], 'total' => null, 'chart' => null, 'note' => '');
		return $r;
	}

	/* ================================================================ SALES */

	private function r_s_sales_month($d1, $d2, $ids)
	{
		$mo = $this->months($d1, $d2, true);
		$a = $this->t1(key($mo) . '-01'); end($mo); $b = $this->t2(date('Y-m-t', strtotime(key($mo) . '-01'))); reset($mo);
		$in = $this->in($ids);
		$o = $this->keyed("SELECT DATE_FORMAT(O.created_at, '%Y-%m') k, COUNT(*) n FROM ci_orders O WHERE O.created_by IN ($in) AND O.created_at BETWEEN $a AND $b GROUP BY k");
		$s = $this->keyed("SELECT DATE_FORMAT(" . $this->sd() . ", '%Y-%m') k, COUNT(*) n, SUM(O.pricing_amount) amt FROM ci_orders O WHERE O.created_by IN ($in) AND " . $this->sale() . " AND " . $this->sd() . " BETWEEN $a AND $b GROUP BY k");
		$t = $this->keyed("SELECT period_month k, SUM(target_amount) amt FROM ci_hr_targets WHERE status = 'Active' AND user_id IN ($in) AND period_month IN ('" . implode("','", array_keys($mo)) . "') GROUP BY period_month");
		$rows = array();
		foreach ($mo as $k => $lab) {
			$sa = isset($s[$k]) ? (float)$s[$k]['amt'] : 0; $tg = isset($t[$k]) ? (float)$t[$k]['amt'] : 0;
			$rows[] = array('m' => $lab, 'orders' => isset($o[$k]) ? (int)$o[$k]['n'] : 0, 'sales' => isset($s[$k]) ? (int)$s[$k]['n'] : 0, 'amt' => $sa, 'target' => $tg, 'pct' => $this->pct($sa, $tg), 'gap' => $tg > 0 ? max(0, $tg - $sa) : null);
		}
		$cols = array(array('m', 'Month', 'text'), array('orders', 'Orders received', 'num'), array('sales', 'Sales', 'num'), array('amt', 'Sales amount', 'amt'), array('target', 'Target', 'amt'), array('pct', '% of target', 'pct'), array('gap', 'Short of target', 'amt'));
		$tot = $this->total($rows, $cols); $tot['pct'] = $this->pct($tot['amt'], $tot['target']); $tot['gap'] = '';
		return array('cols' => $cols, 'rows' => $rows, 'total' => $tot,
			'chart' => $this->chart('bar', array_values($mo), array($this->set('Target', '#00c6f0', array_column($rows, 'target')), $this->set('Sales', '#16a34a', array_column($rows, 'amt'))), true),
			'note' => 'Orders by the person who created them. Sales by the date the order became a sale.');
	}

	private function r_s_sales_person($d1, $d2, $ids)
	{
		$in = $this->in($ids); $a = $this->t1($d1); $b = $this->t2($d2);
		$mo = array_keys($this->months($d1, $d2));
		$o = $this->keyed("SELECT O.created_by k, COUNT(*) n FROM ci_orders O WHERE O.created_by IN ($in) AND O.created_at BETWEEN $a AND $b GROUP BY k");
		$s = $this->keyed("SELECT O.created_by k, COUNT(*) n, SUM(O.pricing_amount) amt FROM ci_orders O WHERE O.created_by IN ($in) AND " . $this->sale() . " AND " . $this->sd() . " BETWEEN $a AND $b GROUP BY k");
		$t = $this->keyed("SELECT user_id k, SUM(target_amount) amt FROM ci_hr_targets WHERE status = 'Active' AND user_id IN ($in) AND period_month IN ('" . implode("','", $mo) . "') GROUP BY user_id");
		$rows = array();
		foreach ($this->people($ids) as $id => $p) {
			$n = isset($s[$id]) ? (int)$s[$id]['n'] : 0; $sa = isset($s[$id]) ? (float)$s[$id]['amt'] : 0; $tg = isset($t[$id]) ? (float)$t[$id]['amt'] : 0;
			$on = isset($o[$id]) ? (int)$o[$id]['n'] : 0;
			if (!$on && !$n && !$tg && count($ids) > 1) continue;
			$rows[] = array('name' => $p['name'], 'orders' => $on, 'sales' => $n, 'amt' => $sa, 'avg' => $n ? $sa / $n : 0, 'target' => $tg, 'pct' => $this->pct($sa, $tg));
		}
		usort($rows, function ($x, $y) { return $y['amt'] <=> $x['amt'] ?: $y['orders'] <=> $x['orders']; });
		$cols = array(array('name', 'Person', 'text'), array('orders', 'Orders', 'num'), array('sales', 'Sales', 'num'), array('amt', 'Sales amount', 'amt'), array('avg', 'Average sale', 'amt'), array('target', 'Target', 'amt'), array('pct', '% of target', 'pct'));
		$tot = $this->total($rows, $cols); if ($tot) { $tot['pct'] = $this->pct($tot['amt'], $tot['target']); $tot['avg'] = $tot['sales'] ? $tot['amt'] / $tot['sales'] : 0; }
		$top = array_slice($rows, 0, 15);
		return array('cols' => $cols, 'rows' => $rows, 'total' => $tot,
			'chart' => $top ? $this->chart('bar', array_column($top, 'name'), array($this->set('Target', '#00c6f0', array_column($top, 'target')), $this->set('Sales', '#16a34a', array_column($top, 'amt'))), true) : null,
			'note' => 'People with no orders, sales or target in the period are left out.');
	}

	private function r_s_target_6m($d1, $d2, $ids)
	{
		$mo = array_slice($this->months($d1, $d2, true), -6, 6, true);
		$in = $this->in($ids);
		$a = $this->t1(key($mo) . '-01'); end($mo); $b = $this->t2(date('Y-m-t', strtotime(key($mo) . '-01'))); reset($mo);
		$s = array(); $t = array();
		foreach ($this->db->query("SELECT O.created_by u, DATE_FORMAT(" . $this->sd() . ", '%Y-%m') k, SUM(O.pricing_amount) amt FROM ci_orders O WHERE O.created_by IN ($in) AND " . $this->sale() . " AND " . $this->sd() . " BETWEEN $a AND $b GROUP BY u, k")->result_array() as $r) $s[$r['u']][$r['k']] = (float)$r['amt'];
		foreach ($this->db->query("SELECT user_id u, period_month k, SUM(target_amount) amt FROM ci_hr_targets WHERE status = 'Active' AND user_id IN ($in) AND period_month IN ('" . implode("','", array_keys($mo)) . "') GROUP BY u, k")->result_array() as $r) $t[$r['u']][$r['k']] = (float)$r['amt'];
		$cols = array(array('name', 'Person', 'text'));
		foreach ($mo as $k => $lab) $cols[] = array('m' . $k, $lab, 'pct');
		$cols[] = array('sum', '6 months', 'pct');
		$rows = array();
		foreach ($this->people($ids) as $id => $p) {
			if (empty($t[$id]) && empty($s[$id])) continue;
			$r = array('name' => $p['name']); $ss = 0; $tt = 0;
			foreach ($mo as $k => $lab) { $x = isset($s[$id][$k]) ? $s[$id][$k] : 0; $y = isset($t[$id][$k]) ? $t[$id][$k] : 0; $r['m' . $k] = $this->pct($x, $y); $ss += $x; $tt += $y; }
			$r['sum'] = $this->pct($ss, $tt);
			$rows[] = $r;
		}
		return array('cols' => $cols, 'rows' => $rows, 'note' => 'Empty cell = no target set that month. Only people with a target or a sale are listed.');
	}

	private function r_s_pipeline_person($d1, $d2, $ids)
	{
		$in = $this->in($ids); $own = $this->owner('A');
		$l = $this->keyed("SELECT $own k, SUM(COALESCE(P.pipeline_stage, 'database') = 'database') db, SUM(" . crm_stage_lead_sql('A', 'P') . ") leads
			FROM ci_leads A LEFT JOIN ci_pipeline P ON P.lead_id = A.id WHERE " . $this->open_lead('A') . " AND $own IN ($in) GROUP BY k");
		$o = $this->keyed("SELECT O.created_by k, SUM(" . $this->open_order() . ") open_n, SUM(CASE WHEN " . $this->open_order() . " THEN O.pricing_amount ELSE 0 END) open_amt,
				SUM(" . $this->sale() . ") sales_n, SUM(CASE WHEN " . $this->sale() . " THEN O.pricing_amount ELSE 0 END) sales_amt
			FROM ci_orders O WHERE O.created_by IN ($in) GROUP BY k");
		$rows = array();
		foreach ($this->people($ids) as $id => $p) {
			$r = array('name' => $p['name'], 'db' => isset($l[$id]) ? (int)$l[$id]['db'] : 0, 'leads' => isset($l[$id]) ? (int)$l[$id]['leads'] : 0,
				'open' => isset($o[$id]) ? (int)$o[$id]['open_n'] : 0, 'open_amt' => isset($o[$id]) ? (float)$o[$id]['open_amt'] : 0,
				'sales' => isset($o[$id]) ? (int)$o[$id]['sales_n'] : 0, 'sales_amt' => isset($o[$id]) ? (float)$o[$id]['sales_amt'] : 0);
			if (!$r['db'] && !$r['leads'] && !$r['open'] && !$r['sales'] && count($ids) > 1) continue;
			$rows[] = $r;
		}
		usort($rows, function ($x, $y) { return $y['leads'] <=> $x['leads'] ?: $y['db'] <=> $x['db']; });
		$cols = array(array('name', 'Person', 'text'), array('db', 'Database', 'num'), array('leads', 'Leads', 'num'), array('open', 'Open orders', 'num'), array('open_amt', 'Open order value', 'amt'), array('sales', 'Sales (all time)', 'num'), array('sales_amt', 'Sales amount', 'amt'));
		$top = array_slice($rows, 0, 15);
		return array('cols' => $cols, 'rows' => $rows, 'total' => $this->total($rows, $cols),
			'chart' => $top ? $this->chart('bar', array_column($top, 'name'), array($this->set('Leads', '#0072f0', array_column($top, 'leads')), $this->set('Open orders', '#0f8a80', array_column($top, 'open')), $this->set('Sales', '#16a34a', array_column($top, 'sales')))) : null,
			'note' => 'Records count for the person they are assigned to (else who created them). Closed companies are left out.');
	}

	private function r_s_followups_person($d1, $d2, $ids)
	{
		$in = $this->in($ids); $own = $this->owner('a'); $today = date('Y-m-d'); $n7 = date('Y-m-d', strtotime('+7 days'));
		$days = max(1, (int)floor((strtotime(min($d2, $today)) - strtotime($d1)) / 86400) + 1);
		$lg = $this->keyed("SELECT user_id k, COUNT(*) n, COUNT(DISTINCT lead_id) leads FROM ci_followup WHERE user_id IN ($in) AND followup_date BETWEEN " . $this->db->escape($d1) . " AND " . $this->db->escape($d2) . " GROUP BY k");
		$due = $this->keyed("SELECT $own k, SUM(a.followup_date < '$today') missed, SUM(a.followup_date = '$today') today, SUM(a.followup_date > '$today' AND a.followup_date <= '$n7') next7
			FROM ci_leads a WHERE " . $this->open_lead('a') . " AND a.status NOT IN (42,43,44) AND a.followup_date >= '2000-01-01' AND $own IN ($in) GROUP BY k");
		$rows = array();
		foreach ($this->people($ids) as $id => $p) {
			$n = isset($lg[$id]) ? (int)$lg[$id]['n'] : 0;
			$r = array('name' => $p['name'], 'logged' => $n, 'leads' => isset($lg[$id]) ? (int)$lg[$id]['leads'] : 0, 'per_day' => round($n / $days, 1),
				'missed' => isset($due[$id]) ? (int)$due[$id]['missed'] : 0, 'today' => isset($due[$id]) ? (int)$due[$id]['today'] : 0, 'next7' => isset($due[$id]) ? (int)$due[$id]['next7'] : 0);
			if (!$n && !$r['missed'] && !$r['today'] && !$r['next7'] && count($ids) > 1) continue;
			$rows[] = $r;
		}
		usort($rows, function ($x, $y) { return $y['missed'] <=> $x['missed'] ?: $y['logged'] <=> $x['logged']; });
		$cols = array(array('name', 'Person', 'text'), array('logged', 'Logged in period', 'num'), array('leads', 'Records followed up', 'num'), array('per_day', 'Per day', 'dec'), array('missed', 'Missed now', 'num'), array('today', 'Due today', 'num'), array('next7', 'Next 7 days', 'num'));
		$tot = $this->total($rows, $cols); if ($tot) $tot['per_day'] = round($tot['logged'] / $days, 1);
		$top = array_slice($rows, 0, 15);
		return array('cols' => $cols, 'rows' => $rows, 'total' => $tot,
			'chart' => $top ? $this->chart('bar', array_column($top, 'name'), array($this->set('Logged', '#16a34a', array_column($top, 'logged')), $this->set('Missed now', '#dc2626', array_column($top, 'missed')))) : null,
			'note' => 'Missed = follow-up date passed on an open record. Per day = logged / ' . $days . ' days.');
	}

	private function r_s_calls_person($d1, $d2, $ids)
	{
		$in = $this->in($ids);
		$c = $this->keyed("SELECT user_id k, COUNT(*) n, SUM(call_status = 'Completed') ok, SUM(COALESCE(duration, 0)) secs, COUNT(DISTINCT lead_id) leads
			FROM ci_call_logs WHERE user_id IN ($in) AND created_at BETWEEN " . $this->t1($d1) . " AND " . $this->t2($d2) . " GROUP BY k");
		$rows = array();
		foreach ($this->people($ids) as $id => $p) {
			if (!isset($c[$id]) && count($ids) > 1) continue;
			$n = isset($c[$id]) ? (int)$c[$id]['n'] : 0; $ok = isset($c[$id]) ? (int)$c[$id]['ok'] : 0; $s = isset($c[$id]) ? (int)$c[$id]['secs'] : 0;
			$rows[] = array('name' => $p['name'], 'n' => $n, 'ok' => $ok, 'no' => $n - $ok, 'rate' => $this->pct($ok, $n), 'mins' => round($s / 60, 1), 'avg' => $ok ? round($s / $ok) : 0, 'leads' => isset($c[$id]) ? (int)$c[$id]['leads'] : 0);
		}
		usort($rows, function ($x, $y) { return $y['n'] <=> $x['n']; });
		$cols = array(array('name', 'Person', 'text'), array('n', 'Calls made', 'num'), array('ok', 'Answered', 'num'), array('no', 'Not answered', 'num'), array('rate', 'Answer rate', 'pct'), array('mins', 'Talk time (min)', 'dec'), array('avg', 'Average answered call (sec)', 'num'), array('leads', 'Different numbers / records', 'num'));
		$tot = $this->total($rows, $cols); if ($tot) { $tot['rate'] = $this->pct($tot['ok'], $tot['n']); $tot['mins'] = round(array_sum(array_column($rows, 'mins')), 1); $tot['avg'] = ''; }
		$top = array_slice($rows, 0, 15);
		return array('cols' => $cols, 'rows' => $rows, 'total' => $tot,
			'chart' => $top ? $this->chart('bar', array_column($top, 'name'), array($this->set('Answered', '#16a34a', array_column($top, 'ok')), $this->set('Not answered', '#f97316', array_column($top, 'no')))) : null,
			'note' => 'People with no calls in the period are left out.');
	}

	private function r_s_activity_day($d1, $d2, $ids)
	{
		$in = $this->in($ids);
		$days = array(); $t = strtotime($d1); $e = strtotime($d2);
		for ($i = 0; $t <= $e && $i < 93; $i++, $t = strtotime('+1 day', $t)) $days[date('Y-m-d', $t)] = date('d M', $t);
		end($days); $last = key($days); reset($days);
		$a = $this->t1($d1); $b = $this->t2($last);
		$rec = $this->keyed("SELECT LEFT(A.created, 10) k, COUNT(*) n FROM ci_leads A WHERE A.created_by IN ($in) AND A.created BETWEEN $a AND $b GROUP BY k");
		$fu = $this->keyed("SELECT followup_date k, COUNT(*) n FROM ci_followup WHERE user_id IN ($in) AND followup_date BETWEEN " . $this->db->escape($d1) . " AND " . $this->db->escape($last) . " GROUP BY k");
		$cl = $this->keyed("SELECT DATE(created_at) k, COUNT(*) n, SUM(call_status = 'Completed') ok FROM ci_call_logs WHERE user_id IN ($in) AND created_at BETWEEN $a AND $b GROUP BY k");
		$or = $this->keyed("SELECT DATE(O.created_at) k, COUNT(*) n FROM ci_orders O WHERE O.created_by IN ($in) AND O.created_at BETWEEN $a AND $b GROUP BY k");
		$sa = $this->keyed("SELECT DATE(" . $this->sd() . ") k, COUNT(*) n, SUM(O.pricing_amount) amt FROM ci_orders O WHERE O.created_by IN ($in) AND " . $this->sale() . " AND " . $this->sd() . " BETWEEN $a AND $b GROUP BY k");
		$rows = array();
		foreach ($days as $k => $lab) {
			$rows[] = array('d' => $lab . ' ' . date('D', strtotime($k)), 'rec' => isset($rec[$k]) ? (int)$rec[$k]['n'] : 0, 'fu' => isset($fu[$k]) ? (int)$fu[$k]['n'] : 0,
				'calls' => isset($cl[$k]) ? (int)$cl[$k]['n'] : 0, 'ok' => isset($cl[$k]) ? (int)$cl[$k]['ok'] : 0, 'orders' => isset($or[$k]) ? (int)$or[$k]['n'] : 0,
				'sales' => isset($sa[$k]) ? (int)$sa[$k]['n'] : 0, 'amt' => isset($sa[$k]) ? (float)$sa[$k]['amt'] : 0);
		}
		$cols = array(array('d', 'Day', 'text'), array('rec', 'Records added', 'num'), array('fu', 'Follow-ups logged', 'num'), array('calls', 'Calls', 'num'), array('ok', 'Answered', 'num'), array('orders', 'Orders', 'num'), array('sales', 'Sales', 'num'), array('amt', 'Sales amount', 'amt'));
		return array('cols' => $cols, 'rows' => $rows, 'total' => $this->total($rows, $cols),
			'chart' => $this->chart('line', array_values($days), array($this->set('Records added', '#7c3aed', array_column($rows, 'rec')), $this->set('Follow-ups', '#0072f0', array_column($rows, 'fu')), $this->set('Calls', '#0f8a80', array_column($rows, 'calls')), $this->set('Orders', '#ea580c', array_column($rows, 'orders')))),
			'note' => count($days) >= 93 ? 'Shows the first 93 days of the period.' : '');
	}

	private function r_s_lead_ageing($d1, $d2, $ids)
	{
		$in = $this->in($ids); $own = $this->owner('L'); $today = date('Y-m-d');
		$q = $this->keyed("SELECT k, SUM(age <= 7) a, SUM(age BETWEEN 8 AND 30) b, SUM(age BETWEEN 31 AND 90) c, SUM(age > 90) d, COUNT(*) n FROM (
				SELECT $own k, DATEDIFF('$today', COALESCE(F.last_d, LEFT(L.created, 10))) age
				FROM ci_leads L INNER JOIN ci_pipeline P ON P.lead_id = L.id
				LEFT JOIN (SELECT lead_id, MAX(followup_date) last_d FROM ci_followup WHERE followup_date <= '$today' GROUP BY lead_id) F ON F.lead_id = L.id
				WHERE " . crm_stage_lead_sql('L', 'P') . " AND L.status NOT IN (42, 43, 44) AND " . $this->open_lead('L') . " AND $own IN ($in)
			) t GROUP BY k");
		$rows = array();
		foreach ($this->people($ids) as $id => $p) {
			if (!isset($q[$id])) continue;
			$r = $q[$id];
			$rows[] = array('name' => $p['name'], 'n' => (int)$r['n'], 'a' => (int)$r['a'], 'b' => (int)$r['b'], 'c' => (int)$r['c'], 'd' => (int)$r['d'], 'old' => $this->pct((int)$r['c'] + (int)$r['d'], (int)$r['n']));
		}
		usort($rows, function ($x, $y) { return $y['n'] <=> $x['n']; });
		$cols = array(array('name', 'Person', 'text'), array('n', 'Open leads', 'num'), array('a', '0 to 7 days', 'num'), array('b', '8 to 30 days', 'num'), array('c', '31 to 90 days', 'num'), array('d', 'Over 90 days', 'num'), array('old', '% over 30 days', 'pct'));
		$tot = $this->total($rows, $cols); if ($tot) $tot['old'] = $this->pct($tot['c'] + $tot['d'], $tot['n']);
		return array('cols' => $cols, 'rows' => $rows, 'total' => $tot,
			'chart' => $tot ? $this->chart('bar', array('0 to 7 days', '8 to 30 days', '31 to 90 days', 'Over 90 days'), array($this->set('Open leads', '#ea580c', array($tot['a'], $tot['b'], $tot['c'], $tot['d'])))) : null,
			'note' => 'Days since the last follow-up was logged (or since the record was added, when none).');
	}

	private function r_s_lead_status($d1, $d2, $ids)
	{
		$in = $this->in($ids); $own = $this->owner('A');
		$q = $this->db->query("SELECT COALESCE(S.status, 'No status') k, COUNT(*) n FROM ci_leads A LEFT JOIN ci_status S ON S.id = A.status
			WHERE $own IN ($in) AND A.created BETWEEN " . $this->t1($d1) . " AND " . $this->t2($d2) . " GROUP BY k ORDER BY n DESC")->result_array();
		$sum = array_sum(array_column($q, 'n'));
		$rows = array();
		foreach ($q as $r) $rows[] = array('s' => $r['k'], 'n' => (int)$r['n'], 'share' => $this->pct((int)$r['n'], $sum));
		$cols = array(array('s', 'Status now', 'text'), array('n', 'Records', 'num'), array('share', 'Share', 'pct'));
		$top = array_slice($rows, 0, 15);
		return array('cols' => $cols, 'rows' => $rows, 'total' => $rows ? array('s' => 'Total', 'n' => $sum, 'share' => 100) : null,
			'chart' => $top ? $this->chart('bar', array_column($top, 's'), array($this->set('Records', '#0072f0', array_column($top, 'n')))) : null);
	}

	private function r_s_lost_leads($d1, $d2, $ids)
	{
		$in = $this->in($ids); $own = $this->owner('A');
		$q = $this->db->query("SELECT COALESCE(NULLIF(TRIM(A.lost_reason), ''), 'No reason given') k, COUNT(*) n, SUM(CAST(COALESCE(NULLIF(A.mrr, ''), 0) AS DECIMAL(12,2))) mrr
			FROM ci_leads A WHERE A.status = 43 AND $own IN ($in) AND COALESCE(A.modified, A.created) BETWEEN " . $this->t1($d1) . " AND " . $this->t2($d2) . " GROUP BY k ORDER BY n DESC")->result_array();
		$sum = array_sum(array_column($q, 'n'));
		$rows = array();
		foreach ($q as $r) $rows[] = array('r' => $r['k'], 'n' => (int)$r['n'], 'share' => $this->pct((int)$r['n'], $sum), 'mrr' => (float)$r['mrr']);
		$cols = array(array('r', 'Lost reason', 'text'), array('n', 'Leads', 'num'), array('share', 'Share', 'pct'), array('mrr', 'Monthly value (MRR)', 'amt'));
		$tot = $this->total($rows, $cols); if ($tot) $tot['share'] = 100;
		return array('cols' => $cols, 'rows' => $rows, 'total' => $tot,
			'chart' => $rows ? $this->chart('bar', array_column(array_slice($rows, 0, 12), 'r'), array($this->set('Leads', '#dc2626', array_column(array_slice($rows, 0, 12), 'n')))) : null,
			'note' => 'Leads with status Lost, last changed in the period.');
	}

	private function r_s_record_category($d1, $d2, $ids)
	{
		$in = $this->in($ids); $own = $this->owner('A');
		$q = $this->db->query("SELECT COALESCE(NULLIF(A.database_category, ''), 'Not set') k, COUNT(*) n, SUM(" . crm_stage_lead_sql('A', 'P') . ") leads, SUM(A.status = 43) lost
			FROM ci_leads A LEFT JOIN ci_pipeline P ON P.lead_id = A.id WHERE $own IN ($in) AND A.created BETWEEN " . $this->t1($d1) . " AND " . $this->t2($d2) . " GROUP BY k ORDER BY n DESC")->result_array();
		$rows = array();
		foreach ($q as $r) $rows[] = array('c' => $r['k'], 'n' => (int)$r['n'], 'leads' => (int)$r['leads'], 'lost' => (int)$r['lost'], 'conv' => $this->pct((int)$r['leads'], (int)$r['n']));
		$cols = array(array('c', 'Database category', 'text'), array('n', 'Records added', 'num'), array('leads', 'Leads now', 'num'), array('lost', 'Lost', 'num'), array('conv', '% now leads', 'pct'));
		$tot = $this->total($rows, $cols); if ($tot) $tot['conv'] = $this->pct($tot['leads'], $tot['n']);
		return array('cols' => $cols, 'rows' => $rows, 'total' => $tot,
			'chart' => $rows ? $this->chart('bar', array_column($rows, 'c'), array($this->set('Records added', '#7c3aed', array_column($rows, 'n')), $this->set('Leads now', '#0072f0', array_column($rows, 'leads')))) : null);
	}

	private function r_s_category_sales($d1, $d2, $ids)
	{
		$in = $this->in($ids); $a = $this->t1($d1); $b = $this->t2($d2); $cs = $this->cat_sql();
		$q = $this->db->query("SELECT $cs c, COALESCE(NULLIF(O.service_title, ''), '-') sv,
				SUM(O.created_at BETWEEN $a AND $b) orders,
				SUM(" . $this->sale() . " AND " . $this->sd() . " BETWEEN $a AND $b) sales,
				SUM(CASE WHEN " . $this->sale() . " AND " . $this->sd() . " BETWEEN $a AND $b THEN O.pricing_amount ELSE 0 END) amt
			FROM ci_orders O WHERE O.created_by IN ($in) AND (O.created_at BETWEEN $a AND $b OR " . $this->sd() . " BETWEEN $a AND $b)
			GROUP BY c, sv ORDER BY c, amt DESC")->result_array();
		$rows = array(); $byc = array();
		foreach ($q as $r) {
			$rows[] = array('c' => $this->cat_label($r['c']), 'sv' => $r['sv'], 'orders' => (int)$r['orders'], 'sales' => (int)$r['sales'], 'amt' => (float)$r['amt']);
			$l = $this->cat_label($r['c']); if (!isset($byc[$l])) $byc[$l] = 0; $byc[$l] += (float)$r['amt'];
		}
		$sum = array_sum($byc);
		foreach ($rows as &$r) $r['share'] = $this->pct($r['amt'], $sum);
		unset($r);
		$cols = array(array('c', 'Business category', 'text'), array('sv', 'Service', 'text'), array('orders', 'Orders received', 'num'), array('sales', 'Sales', 'num'), array('amt', 'Sales amount', 'amt'), array('share', 'Share of sales', 'pct'));
		$tot = $this->total($rows, $cols); if ($tot) { $tot['sv'] = ''; $tot['share'] = $sum > 0 ? 100 : null; }
		return array('cols' => $cols, 'rows' => $rows, 'total' => $tot,
			'chart' => $byc ? $this->chart('bar', array_keys($byc), array($this->set('Sales amount', '#16a34a', array_values($byc))), true) : null);
	}

	private function r_s_top_clients($d1, $d2, $ids)
	{
		$in = $this->in($ids); $a = $this->t1($d1); $b = $this->t2($d2);
		$q = $this->db->query("SELECT COALESCE(NULLIF(O.company_name, ''), NULLIF(O.client_name, ''), CONCAT('Order #', O.id)) cl, COUNT(*) n, SUM(O.pricing_amount) amt,
				COUNT(DISTINCT " . $this->cat_sql() . ") cats, MAX(" . $this->sd() . ") last_d, MAX(U.name) who
			FROM ci_orders O LEFT JOIN ci_user U ON U.id = O.created_by
			WHERE O.created_by IN ($in) AND " . $this->sale() . " AND " . $this->sd() . " BETWEEN $a AND $b
			GROUP BY cl ORDER BY amt DESC LIMIT 50")->result_array();
		$rows = array();
		foreach ($q as $r) $rows[] = array('cl' => $r['cl'], 'n' => (int)$r['n'], 'amt' => (float)$r['amt'], 'cats' => (int)$r['cats'], 'last' => substr((string)$r['last_d'], 0, 10), 'who' => $r['who']);
		$cols = array(array('cl', 'Client', 'text'), array('n', 'Sales', 'num'), array('amt', 'Sales amount', 'amt'), array('cats', 'Categories', 'num'), array('last', 'Last sale', 'date'), array('who', 'Sold by', 'text'));
		$tot = $this->total($rows, $cols); if ($tot) $tot['cats'] = '';
		$top = array_slice($rows, 0, 12);
		return array('cols' => $cols, 'rows' => $rows, 'total' => $tot,
			'chart' => $top ? $this->chart('bar', array_column($top, 'cl'), array($this->set('Sales amount', '#16a34a', array_column($top, 'amt'))), true) : null,
			'note' => 'Top 50 clients by sales amount.');
	}

	/* ================================================================ BACK OFFICE (every order) */

	private function r_b_orders_status($d1, $d2, $ids)
	{
		$m = "COALESCE(O.updated_at, O.created_at)";
		$q = $this->db->query("SELECT O.status k, COUNT(*) n, SUM(DATEDIFF(NOW(), $m) <= 3) a, SUM(DATEDIFF(NOW(), $m) BETWEEN 4 AND 7) b,
				SUM(DATEDIFF(NOW(), $m) BETWEEN 8 AND 14) c, SUM(DATEDIFF(NOW(), $m) > 14) d, SUM(O.pricing_amount) amt, ROUND(AVG(DATEDIFF(NOW(), O.created_at)), 1) age
			FROM ci_orders O WHERE " . $this->open_order() . " GROUP BY O.status ORDER BY n DESC")->result_array();
		$rows = array();
		foreach ($q as $r) $rows[] = array('s' => $r['k'] ? $r['k'] : '-', 'n' => (int)$r['n'], 'a' => (int)$r['a'], 'b' => (int)$r['b'], 'c' => (int)$r['c'], 'd' => (int)$r['d'], 'age' => (float)$r['age'], 'amt' => (float)$r['amt']);
		$cols = array(array('s', 'Stage', 'text'), array('n', 'Open orders', 'num'), array('a', 'Moved in 3 days', 'num'), array('b', '4 to 7 days', 'num'), array('c', '8 to 14 days', 'num'), array('d', 'Over 14 days', 'num'), array('age', 'Average age (days)', 'dec'), array('amt', 'Value', 'amt'));
		$tot = $this->total($rows, $cols); if ($tot) $tot['age'] = '';
		return array('cols' => $cols, 'rows' => $rows, 'total' => $tot,
			'chart' => $rows ? $this->chart('bar', array_column($rows, 's'), array($this->set('Moved in 3 days', '#16a34a', array_column($rows, 'a')), $this->set('4 to 7 days', '#f59e0b', array_column($rows, 'b')), $this->set('8 to 14 days', '#f97316', array_column($rows, 'c')), $this->set('Over 14 days', '#dc2626', array_column($rows, 'd')))) : null,
			'note' => 'Days since the order last changed. Average age = days since the order was received.');
	}

	private function r_b_orders_trend($d1, $d2, $ids)
	{
		$long = (strtotime($d2) - strtotime($d1)) > 120 * 86400;
		$buckets = array();
		if ($long) { foreach ($this->months($d1, $d2) as $k => $l) $buckets[$k] = array($l, $k . '-01', date('Y-m-t', strtotime($k . '-01'))); }
		else {
			$t = strtotime('monday this week', strtotime($d1));
			while ($t <= strtotime($d2)) { $f = date('Y-m-d', $t); $buckets[$f] = array(date('d M', $t), $f, date('Y-m-d', strtotime('+6 days', $t))); $t = strtotime('+7 days', $t); }
		}
		$first = reset($buckets); $lastb = end($buckets);
		$a = $this->t1($first[1]); $b = $this->t2($lastb[2]);
		$rows = array();
		$rcv = $this->db->query("SELECT DATE(created_at) d, COUNT(*) n FROM ci_orders WHERE created_at BETWEEN $a AND $b GROUP BY d")->result_array();
		$dn = $this->db->query("SELECT DATE(COALESCE(sales_converted_at, updated_at)) d, COUNT(*) n, SUM(pricing_amount) amt FROM ci_orders WHERE status = 'Sales' AND COALESCE(sales_converted_at, updated_at) BETWEEN $a AND $b GROUP BY d")->result_array();
		$cn = $this->db->query("SELECT DATE(COALESCE(updated_at, created_at)) d, COUNT(*) n FROM ci_orders WHERE status = 'Cancelled' AND COALESCE(updated_at, created_at) BETWEEN $a AND $b GROUP BY d")->result_array();
		foreach ($buckets as $k => $bk) $rows[$k] = array('p' => $bk[0], 'rcv' => 0, 'done' => 0, 'amt' => 0.0, 'cxl' => 0);
		$put = function ($list, $col, $vcol = 'n') use (&$rows, $buckets) {
			foreach ($list as $r) foreach ($buckets as $k => $bk) if ($r['d'] >= $bk[1] && $r['d'] <= $bk[2]) { $rows[$k][$col] += (float)$r[$vcol]; break; }
		};
		$put($rcv, 'rcv'); $put($dn, 'done'); $put($dn, 'amt', 'amt'); $put($cn, 'cxl');
		$rows = array_values($rows);
		foreach ($rows as &$r) $r['net'] = $r['rcv'] - $r['done'] - $r['cxl'];
		unset($r);
		$cols = array(array('p', $long ? 'Month' : 'Week of', 'text'), array('rcv', 'Received', 'num'), array('done', 'Completed (sale)', 'num'), array('cxl', 'Cancelled', 'num'), array('net', 'Added to the queue', 'num'), array('amt', 'Completed value', 'amt'));
		return array('cols' => $cols, 'rows' => $rows, 'total' => $this->total($rows, $cols),
			'chart' => $this->chart('line', array_column($rows, 'p'), array($this->set('Received', '#0072f0', array_column($rows, 'rcv')), $this->set('Completed', '#16a34a', array_column($rows, 'done')), $this->set('Cancelled', '#dc2626', array_column($rows, 'cxl')))),
			'note' => 'Added to the queue = received minus completed and cancelled. Above 0 means the queue is growing.');
	}

	private function r_b_processing_time($d1, $d2, $ids)
	{
		$dd = "DATEDIFF(" . $this->done_d() . ", O.created_at)";
		$q = $this->db->query("SELECT " . $this->cat_sql() . " k, COUNT(*) n, ROUND(AVG($dd), 1) avg_d, MIN($dd) min_d, MAX($dd) max_d, SUM($dd <= 7) fast, SUM(O.pricing_amount) amt
			FROM ci_orders O WHERE O.status = 'Sales' AND " . $this->done_d() . " BETWEEN " . $this->t1($d1) . " AND " . $this->t2($d2) . " GROUP BY k ORDER BY n DESC")->result_array();
		$rows = array();
		foreach ($q as $r) $rows[] = array('c' => $this->cat_label($r['k']), 'n' => (int)$r['n'], 'avg' => (float)$r['avg_d'], 'min' => (int)$r['min_d'], 'max' => (int)$r['max_d'], 'fast' => $this->pct((int)$r['fast'], (int)$r['n']), 'amt' => (float)$r['amt']);
		$cols = array(array('c', 'Business category', 'text'), array('n', 'Completed', 'num'), array('avg', 'Average days', 'dec'), array('min', 'Fastest', 'num'), array('max', 'Slowest', 'num'), array('fast', '% done within 7 days', 'pct'), array('amt', 'Value', 'amt'));
		$all = $this->db->query("SELECT ROUND(AVG($dd), 1) a, MIN($dd) mn, MAX($dd) mx, SUM($dd <= 7) f, COUNT(*) n FROM ci_orders O WHERE O.status = 'Sales' AND " . $this->done_d() . " BETWEEN " . $this->t1($d1) . " AND " . $this->t2($d2))->row_array();
		$tot = $rows ? array('c' => 'All', 'n' => (int)$all['n'], 'avg' => (float)$all['a'], 'min' => (int)$all['mn'], 'max' => (int)$all['mx'], 'fast' => $this->pct((int)$all['f'], (int)$all['n']), 'amt' => array_sum(array_column($rows, 'amt'))) : null;
		return array('cols' => $cols, 'rows' => $rows, 'total' => $tot,
			'chart' => $rows ? $this->chart('bar', array_column($rows, 'c'), array($this->set('Average days', '#7c3aed', array_column($rows, 'avg')))) : null,
			'note' => 'Days from the order being received to it becoming a sale.');
	}

	private function r_b_workload($d1, $d2, $ids)
	{
		$a = $this->t1($d1); $b = $this->t2($d2); $cut = $this->db->escape(date('Y-m-d H:i:s', strtotime('-3 days')));
		$dd = "DATEDIFF(" . $this->done_d() . ", O.created_at)";
		$q = $this->db->query("SELECT COALESCE(O.assigned_to, 0) k, MAX(U.name) nm,
				SUM(" . $this->open_order() . ") open_n,
				SUM(" . $this->open_order() . " AND COALESCE(O.updated_at, O.created_at) < $cut) late,
				SUM(O.status = 'Sales' AND " . $this->done_d() . " BETWEEN $a AND $b) done,
				ROUND(AVG(CASE WHEN O.status = 'Sales' AND " . $this->done_d() . " BETWEEN $a AND $b THEN $dd END), 1) avg_d,
				SUM(O.status = 'Cancelled' AND COALESCE(O.updated_at, O.created_at) BETWEEN $a AND $b) cxl
			FROM ci_orders O LEFT JOIN ci_user U ON U.id = O.assigned_to GROUP BY k ORDER BY open_n DESC")->result_array();
		$rows = array();
		foreach ($q as $r) {
			if (!(int)$r['open_n'] && !(int)$r['done'] && !(int)$r['cxl']) continue;
			$rows[] = array('nm' => (int)$r['k'] && $r['nm'] ? $r['nm'] : 'Not assigned', 'open' => (int)$r['open_n'], 'late' => (int)$r['late'], 'done' => (int)$r['done'], 'avg' => $r['avg_d'] === null ? null : (float)$r['avg_d'], 'cxl' => (int)$r['cxl']);
		}
		$cols = array(array('nm', 'Assigned to', 'text'), array('open', 'Open now', 'num'), array('late', 'Not moved 3+ days', 'num'), array('done', 'Completed in period', 'num'), array('avg', 'Average days to complete', 'dec'), array('cxl', 'Cancelled in period', 'num'));
		$tot = $this->total($rows, $cols); if ($tot) $tot['avg'] = '';
		return array('cols' => $cols, 'rows' => $rows, 'total' => $tot,
			'chart' => $rows ? $this->chart('bar', array_column($rows, 'nm'), array($this->set('Open now', '#0072f0', array_column($rows, 'open')), $this->set('Not moved 3+ days', '#dc2626', array_column($rows, 'late')), $this->set('Completed', '#16a34a', array_column($rows, 'done')))) : null);
	}

	private function r_b_documents($d1, $d2, $ids)
	{
		$docs = array(); $orders = 0;
		foreach ($this->db->query("SELECT O.documents_status FROM ci_orders O WHERE " . $this->open_order() . " AND O.documents_status IS NOT NULL AND O.documents_status != ''")->result_array() as $o) {
			$d = json_decode((string)$o['documents_status'], true);
			if (!is_array($d)) continue;
			$has = false;
			foreach ($d as $it) {
				if (!is_array($it)) continue;
				$t = trim(isset($it['title']) ? (string)$it['title'] : '');
				if ($t === '') $t = 'Untitled';
				if (!isset($docs[$t])) $docs[$t] = array('t' => $t, 'pend' => 0, 'got' => 0);
				if ((isset($it['status']) ? $it['status'] : '') === 'collected') $docs[$t]['got']++; else { $docs[$t]['pend']++; $has = true; }
			}
			if ($has) $orders++;
		}
		$rows = array_values($docs);
		foreach ($rows as &$r) { $r['all'] = $r['pend'] + $r['got']; $r['pct'] = $this->pct($r['got'], $r['all']); }
		unset($r);
		usort($rows, function ($x, $y) { return $y['pend'] <=> $x['pend']; });
		$cols = array(array('t', 'Document', 'text'), array('pend', 'Still to collect', 'num'), array('got', 'Collected', 'num'), array('all', 'On open orders', 'num'), array('pct', '% collected', 'pct'));
		$tot = $this->total($rows, $cols); if ($tot) $tot['pct'] = $this->pct($tot['got'], $tot['all']);
		$top = array_slice($rows, 0, 15);
		return array('cols' => $cols, 'rows' => $rows, 'total' => $tot,
			'chart' => $top ? $this->chart('bar', array_column($top, 't'), array($this->set('Still to collect', '#f97316', array_column($top, 'pend')), $this->set('Collected', '#16a34a', array_column($top, 'got')))) : null,
			'note' => $orders . ' open ' . ($orders === 1 ? 'order has' : 'orders have') . ' documents still to collect.');
	}

	private function r_b_orders_category($d1, $d2, $ids)
	{
		$q = $this->db->query("SELECT " . $this->cat_sql() . " c, COALESCE(NULLIF(O.status, ''), '-') s, COUNT(*) n FROM ci_orders O
			WHERE O.created_at BETWEEN " . $this->t1($d1) . " AND " . $this->t2($d2) . " GROUP BY c, s")->result_array();
		$st = array(); $mx = array();
		foreach ($q as $r) { $st[$r['s']] = true; $mx[$r['c']][$r['s']] = (int)$r['n']; }
		$st = array_keys($st); sort($st);
		$cols = array(array('c', 'Business category', 'text'));
		foreach ($st as $i => $s) $cols[] = array('s' . $i, $s, 'num');
		$cols[] = array('n', 'Total', 'num');
		$rows = array();
		foreach ($mx as $c => $list) {
			$r = array('c' => $this->cat_label($c)); $n = 0;
			foreach ($st as $i => $s) { $r['s' . $i] = isset($list[$s]) ? $list[$s] : 0; $n += $r['s' . $i]; }
			$r['n'] = $n; $rows[] = $r;
		}
		usort($rows, function ($x, $y) { return $y['n'] <=> $x['n']; });
		return array('cols' => $cols, 'rows' => $rows, 'total' => $this->total($rows, $cols),
			'chart' => $rows ? $this->chart('bar', array_column($rows, 'c'), array($this->set('Orders', '#0072f0', array_column($rows, 'n')))) : null);
	}

	private function r_b_cancelled($d1, $d2, $ids)
	{
		$q = $this->db->query("SELECT O.id, O.order_no, COALESCE(NULLIF(O.company_name, ''), O.client_name) cl, " . $this->cat_sql() . " c, O.service_title, O.pricing_amount, O.created_at, COALESCE(O.updated_at, O.created_at) cx, C.name who, O.approval_remarks
			FROM ci_orders O LEFT JOIN ci_user C ON C.id = O.created_by
			WHERE O.status = 'Cancelled' AND COALESCE(O.updated_at, O.created_at) BETWEEN " . $this->t1($d1) . " AND " . $this->t2($d2) . " ORDER BY cx DESC")->result_array();
		$rows = array();
		foreach ($q as $r) $rows[] = array('o' => $r['order_no'] ? $r['order_no'] : '#' . $r['id'], '_href' => 'manager/orders/view/' . (int)$r['id'], 'cl' => $r['cl'], 'c' => $this->cat_label($r['c']), 'sv' => $r['service_title'], 'amt' => (float)$r['pricing_amount'], 'rcv' => substr($r['created_at'], 0, 10), 'cx' => substr($r['cx'], 0, 10), 'who' => $r['who'], 'rm' => $r['approval_remarks']);
		$cols = array(array('o', 'Order', 'text'), array('cl', 'Client', 'text'), array('c', 'Category', 'text'), array('sv', 'Service', 'text'), array('amt', 'Value', 'amt'), array('rcv', 'Received', 'date'), array('cx', 'Cancelled', 'date'), array('who', 'Created by', 'text'), array('rm', 'Remarks', 'text'));
		$tot = $rows ? array('o' => count($rows) . ' orders', 'amt' => array_sum(array_column($rows, 'amt'))) : null;
		return array('cols' => $cols, 'rows' => $rows, 'total' => $tot);
	}

	private function r_b_order_register($d1, $d2, $ids)
	{
		$q = $this->db->query("SELECT O.id, O.order_no, COALESCE(NULLIF(O.company_name, ''), O.client_name) cl, " . $this->cat_sql() . " c, O.service_title, O.status, O.pricing_amount, O.created_at,
				COALESCE(O.updated_at, O.created_at) mv, C.name who, A.name asg, " . $this->sale() . " is_sale
			FROM ci_orders O LEFT JOIN ci_user C ON C.id = O.created_by LEFT JOIN ci_user A ON A.id = O.assigned_to
			WHERE O.created_at BETWEEN " . $this->t1($d1) . " AND " . $this->t2($d2) . " ORDER BY O.created_at DESC LIMIT 2000")->result_array();
		$rows = array(); $now = time();
		foreach ($q as $r) {
			$open = !(int)$r['is_sale'] && $r['status'] !== 'Cancelled';
			$rows[] = array('o' => $r['order_no'] ? $r['order_no'] : '#' . $r['id'], '_href' => 'manager/orders/view/' . (int)$r['id'], 'cl' => $r['cl'], 'c' => $this->cat_label($r['c']), 'sv' => $r['service_title'], 's' => $r['status'],
				'amt' => (float)$r['pricing_amount'], 'rcv' => substr($r['created_at'], 0, 10), 'age' => $open ? (int)floor(($now - strtotime($r['created_at'])) / 86400) : null,
				'still' => $open ? (int)floor(($now - strtotime($r['mv'])) / 86400) : null, 'who' => $r['who'], 'asg' => $r['asg']);
		}
		$cols = array(array('o', 'Order', 'text'), array('cl', 'Client', 'text'), array('c', 'Category', 'text'), array('sv', 'Service', 'text'), array('s', 'Status', 'text'), array('amt', 'Value', 'amt'), array('rcv', 'Received', 'date'), array('age', 'Days open', 'num'), array('still', 'Days since last move', 'num'), array('who', 'Created by', 'text'), array('asg', 'Assigned to', 'text'));
		$tot = $rows ? array('o' => count($rows) . ' orders', 'amt' => array_sum(array_column($rows, 'amt'))) : null;
		return array('cols' => $cols, 'rows' => $rows, 'total' => $tot, 'note' => 'Days open and days since last move are shown for open orders only.');
	}

	/* ================================================================ KAM (my clients; everyone's for Admin) */

	private function clients()
	{
		return $this->dash->kam_clients($this->uid, $this->is_all);
	}

	private function r_k_client_contact($d1, $d2, $ids)
	{
		$rows = array(); $b = array(0, 0, 0, 0);
		foreach ($this->clients() as $c) {
			$d = $c['days_since'];
			if ($d === null) $b[3]++; elseif ($d <= 7) $b[0]++; elseif ($d <= 30) $b[1]++; else $b[2]++;
			$rows[] = array('cl' => $c['client'], '_href' => 'manager/kam/view/' . (int)$c['lead_id'], 'g' => $c['group_name'], 'kam' => $c['executive_name'], 'last' => $c['last_contact'], 'days' => $d,
				'next' => $c['followup_date'] >= '2000-01-01' ? $c['followup_date'] : '', 'tasks' => (int)$c['tasks_open'], 'late' => (int)$c['tasks_late']);
		}
		$cols = array(array('cl', 'Client', 'text'), array('g', 'Group', 'text'), array('kam', 'Account manager', 'text'), array('last', 'Last contact', 'date'), array('days', 'Days since', 'num'), array('next', 'Next follow-up', 'date'), array('tasks', 'Open tasks', 'num'), array('late', 'Late tasks', 'num'));
		return array('cols' => $cols, 'rows' => $rows, 'total' => $rows ? array('cl' => count($rows) . ' clients', 'tasks' => array_sum(array_column($rows, 'tasks')), 'late' => array_sum(array_column($rows, 'late'))) : null,
			'chart' => $this->chart('bar', array('0 to 7 days', '8 to 30 days', 'Over 30 days', 'Never contacted'), array($this->set('Clients', '#0f8a80', $b))),
			'note' => 'Last contact = last follow-up logged or call made on the client. ' . ($this->is_all ? 'Every client.' : 'Your own clients.'));
	}

	private function r_k_kam_summary($d1, $d2, $ids)
	{
		$g = array();
		foreach ($this->clients() as $c) {
			$k = $c['executive_name'] ? $c['executive_name'] : 'Not set';
			if (!isset($g[$k])) $g[$k] = array('kam' => $k, 'n' => 0, 'a' => 0, 'b' => 0, 'c' => 0, 'd' => 0, 'tasks' => 0, 'late' => 0);
			$d = $c['days_since']; $g[$k]['n']++;
			if ($d === null) $g[$k]['d']++; elseif ($d <= 7) $g[$k]['a']++; elseif ($d <= 30) $g[$k]['b']++; else $g[$k]['c']++;
			$g[$k]['tasks'] += (int)$c['tasks_open']; $g[$k]['late'] += (int)$c['tasks_late'];
		}
		$rows = array_values($g);
		foreach ($rows as &$r) $r['gap'] = $this->pct($r['c'] + $r['d'], $r['n']);
		unset($r);
		usort($rows, function ($x, $y) { return $y['n'] <=> $x['n']; });
		$cols = array(array('kam', 'Account manager', 'text'), array('n', 'Clients', 'num'), array('a', 'Contacted in 7 days', 'num'), array('b', '8 to 30 days', 'num'), array('c', 'Over 30 days', 'num'), array('d', 'Never contacted', 'num'), array('gap', '% not contacted 30+ days', 'pct'), array('tasks', 'Open tasks', 'num'), array('late', 'Late tasks', 'num'));
		$tot = $this->total($rows, $cols); if ($tot) $tot['gap'] = $this->pct($tot['c'] + $tot['d'], $tot['n']);
		return array('cols' => $cols, 'rows' => $rows, 'total' => $tot,
			'chart' => $rows ? $this->chart('bar', array_column($rows, 'kam'), array($this->set('Contacted in 7 days', '#16a34a', array_column($rows, 'a')), $this->set('Over 30 days or never', '#dc2626', array_map(function ($r) { return $r['c'] + $r['d']; }, $rows)))) : null);
	}

	private function client_ids()
	{
		return array_map(function ($c) { return (int)$c['lead_id']; }, $this->clients());
	}

	private function r_k_upsell_month($d1, $d2, $ids)
	{
		$mo = $this->months($d1, $d2, true);
		$in = $this->in($this->client_ids());
		$a = $this->t1(key($mo) . '-01'); end($mo); $b = $this->t2(date('Y-m-t', strtotime(key($mo) . '-01'))); reset($mo);
		$u = $this->keyed("SELECT DATE_FORMAT(" . $this->sd() . ", '%Y-%m') k, COUNT(*) n, SUM(O.pricing_amount) amt, COUNT(DISTINCT O.lead_id) cl
			FROM ci_orders O JOIN ci_kam_client_details K ON K.lead_id = O.lead_id LEFT JOIN ci_pipeline P ON P.lead_id = O.lead_id
			WHERE O.lead_id IN ($in) AND O.created_at > COALESCE(P.onboarded_at, K.created_at) AND " . $this->sale() . " AND " . $this->sd() . " BETWEEN $a AND $b GROUP BY k");
		$o = $this->keyed("SELECT DATE_FORMAT(O.created_at, '%Y-%m') k, COUNT(*) n FROM ci_orders O WHERE O.lead_id IN ($in) AND O.created_at BETWEEN $a AND $b GROUP BY k");
		$rows = array();
		foreach ($mo as $k => $l) $rows[] = array('m' => $l, 'orders' => isset($o[$k]) ? (int)$o[$k]['n'] : 0, 'n' => isset($u[$k]) ? (int)$u[$k]['n'] : 0, 'amt' => isset($u[$k]) ? (float)$u[$k]['amt'] : 0, 'cl' => isset($u[$k]) ? (int)$u[$k]['cl'] : 0);
		$cols = array(array('m', 'Month', 'text'), array('orders', 'Orders on clients', 'num'), array('n', 'Upselling sales', 'num'), array('amt', 'Upselling amount', 'amt'), array('cl', 'Clients who bought more', 'num'));
		$tot = $this->total($rows, $cols); if ($tot) $tot['cl'] = '';
		return array('cols' => $cols, 'rows' => $rows, 'total' => $tot,
			'chart' => $this->chart('bar', array_values($mo), array($this->set('Upselling amount', '#16a34a', array_column($rows, 'amt'))), true),
			'note' => 'Upselling = sales on orders made after the client was onboarded.');
	}

	private function r_k_client_orders($d1, $d2, $ids)
	{
		$cl = array(); foreach ($this->clients() as $c) $cl[(int)$c['lead_id']] = $c;
		$in = $this->in(array_keys($cl)); $a = $this->t1($d1); $b = $this->t2($d2);
		$q = $this->db->query("SELECT O.lead_id k, COUNT(*) n, SUM(" . $this->sale() . ") s, SUM(CASE WHEN " . $this->sale() . " THEN O.pricing_amount ELSE 0 END) amt,
				SUM(" . $this->open_order() . ") open_n, COUNT(DISTINCT " . $this->cat_sql() . ") cats, MAX(O.created_at) last_o
			FROM ci_orders O WHERE O.lead_id IN ($in) AND O.created_at BETWEEN $a AND $b GROUP BY k ORDER BY amt DESC, n DESC")->result_array();
		$rows = array();
		foreach ($q as $r) { $c = $cl[(int)$r['k']]; $rows[] = array('cl' => $c['client'], '_href' => 'manager/kam/view/' . (int)$r['k'], 'kam' => $c['executive_name'], 'n' => (int)$r['n'], 's' => (int)$r['s'], 'open' => (int)$r['open_n'], 'amt' => (float)$r['amt'], 'cats' => (int)$r['cats'], 'last' => substr($r['last_o'], 0, 10)); }
		$cols = array(array('cl', 'Client', 'text'), array('kam', 'Account manager', 'text'), array('n', 'Orders', 'num'), array('s', 'Sales', 'num'), array('open', 'Still open', 'num'), array('amt', 'Sales amount', 'amt'), array('cats', 'Categories', 'num'), array('last', 'Last order', 'date'));
		$tot = $this->total($rows, $cols); if ($tot) { $tot['kam'] = ''; $tot['cats'] = ''; }
		$top = array_slice($rows, 0, 12);
		return array('cols' => $cols, 'rows' => $rows, 'total' => $tot,
			'chart' => $top ? $this->chart('bar', array_column($top, 'cl'), array($this->set('Orders', '#0072f0', array_column($top, 'n')), $this->set('Sales', '#16a34a', array_column($top, 's')))) : null,
			'note' => 'Clients with no orders in the period are not listed.');
	}

	private function r_k_one_service($d1, $d2, $ids)
	{
		$cl = array(); foreach ($this->clients() as $c) $cl[(int)$c['lead_id']] = $c;
		$sv = array();
		if ($cl) foreach ($this->db->query("SELECT O.lead_id k, COUNT(DISTINCT " . $this->cat_sql() . ") cats, GROUP_CONCAT(DISTINCT COALESCE(NULLIF(O.service_title, ''), O.vertical_code) SEPARATOR ', ') svc, COUNT(*) n, SUM(CASE WHEN " . $this->sale() . " THEN O.pricing_amount ELSE 0 END) amt
			FROM ci_orders O WHERE O.lead_id IN (" . $this->in(array_keys($cl)) . ") AND O.status != 'Cancelled' GROUP BY k")->result_array() as $r) $sv[(int)$r['k']] = $r;
		$rows = array();
		foreach ($cl as $id => $c) {
			$s = isset($sv[$id]) ? $sv[$id] : null;
			if ($s && (int)$s['cats'] > 1) continue;
			$rows[] = array('cl' => $c['client'], '_href' => 'manager/kam/view/' . $id, 'kam' => $c['executive_name'], 'svc' => $s ? $s['svc'] : 'No orders yet', 'n' => $s ? (int)$s['n'] : 0, 'amt' => $s ? (float)$s['amt'] : 0, 'last' => $c['last_contact']);
		}
		$cols = array(array('cl', 'Client', 'text'), array('kam', 'Account manager', 'text'), array('svc', 'Buys now', 'text'), array('n', 'Orders', 'num'), array('amt', 'Sales amount', 'amt'), array('last', 'Last contact', 'date'));
		return array('cols' => $cols, 'rows' => $rows, 'total' => $rows ? array('cl' => count($rows) . ' of ' . count($cl) . ' clients', 'n' => array_sum(array_column($rows, 'n')), 'amt' => array_sum(array_column($rows, 'amt'))) : null,
			'note' => 'Clients whose orders are all in one business category (or who have no orders yet).');
	}

	private function r_k_client_tasks($d1, $d2, $ids)
	{
		$in = $this->in($this->client_ids()); $today = date('Y-m-d');
		$q = $this->db->query("SELECT COALESCE(NULLIF(T.category, ''), 'No category') c, COALESCE(NULLIF(T.status, ''), 'No status') s, COUNT(*) n,
				SUM(T.followup_date >= '2000-01-01' AND T.followup_date < '$today' AND (T.status IS NULL OR T.status NOT IN ('Completed','Closed','Done','Cancelled'))) late
			FROM ci_tasks T WHERE T.client_id IN ($in) GROUP BY c, s ORDER BY c, n DESC")->result_array();
		$rows = array();
		foreach ($q as $r) $rows[] = array('c' => $r['c'], 's' => $r['s'], 'n' => (int)$r['n'], 'late' => (int)$r['late']);
		$cols = array(array('c', 'Category', 'text'), array('s', 'Status', 'text'), array('n', 'Tasks', 'num'), array('late', 'Late', 'num'));
		$tot = $this->total($rows, $cols); if ($tot) $tot['s'] = '';
		$byc = array(); foreach ($rows as $r) { if (!isset($byc[$r['c']])) $byc[$r['c']] = array(0, 0); $byc[$r['c']][0] += $r['n']; $byc[$r['c']][1] += $r['late']; }
		return array('cols' => $cols, 'rows' => $rows, 'total' => $tot,
			'chart' => $byc ? $this->chart('bar', array_keys($byc), array($this->set('Tasks', '#7c3aed', array_column($byc, 0)), $this->set('Late', '#dc2626', array_column($byc, 1)))) : null,
			'note' => 'Late = follow-up date passed and the task is not completed, closed or cancelled.');
	}

	/* ================================================================ ADMIN (whole company) */

	/** Per-person activity in the period: records, follow-ups, calls, orders, sales. */
	private function activity($ids, $d1, $d2)
	{
		$in = $this->in($ids); $a = $this->t1($d1); $b = $this->t2($d2);
		return array(
			'rec' => $this->keyed("SELECT A.created_by k, COUNT(*) n FROM ci_leads A WHERE A.created_by IN ($in) AND A.created BETWEEN $a AND $b GROUP BY k"),
			'fu' => $this->keyed("SELECT user_id k, COUNT(*) n FROM ci_followup WHERE user_id IN ($in) AND followup_date BETWEEN " . $this->db->escape($d1) . " AND " . $this->db->escape($d2) . " GROUP BY k"),
			'calls' => $this->keyed("SELECT user_id k, COUNT(*) n, SUM(call_status = 'Completed') ok FROM ci_call_logs WHERE user_id IN ($in) AND created_at BETWEEN $a AND $b GROUP BY k"),
			'orders' => $this->keyed("SELECT O.created_by k, COUNT(*) n FROM ci_orders O WHERE O.created_by IN ($in) AND O.created_at BETWEEN $a AND $b GROUP BY k"),
			'sales' => $this->keyed("SELECT O.created_by k, COUNT(*) n, SUM(O.pricing_amount) amt FROM ci_orders O WHERE O.created_by IN ($in) AND " . $this->sale() . " AND " . $this->sd() . " BETWEEN $a AND $b GROUP BY k"),
			'target' => $this->keyed("SELECT user_id k, SUM(target_amount) amt FROM ci_hr_targets WHERE status = 'Active' AND user_id IN ($in) AND period_month IN ('" . implode("','", array_keys($this->months($d1, $d2))) . "') GROUP BY user_id"),
		);
	}

	private function v($act, $k, $id, $col = 'n') { return isset($act[$k][$id]) ? (float)$act[$k][$id][$col] : 0; }

	private function group_summary($d1, $d2, $keyfn, $label)
	{
		$people = $this->people($this->dash->scope_ids($this->uid, true));
		$act = $this->activity(array_keys($people), $d1, $d2);
		$g = array();
		foreach ($people as $id => $p) {
			$k = $keyfn($p);
			if (!isset($g[$k])) $g[$k] = array('g' => $k, 'staff' => 0, 'rec' => 0, 'fu' => 0, 'calls' => 0, 'orders' => 0, 'sales' => 0, 'amt' => 0.0, 'target' => 0.0);
			$g[$k]['staff']++;
			$g[$k]['rec'] += $this->v($act, 'rec', $id); $g[$k]['fu'] += $this->v($act, 'fu', $id); $g[$k]['calls'] += $this->v($act, 'calls', $id);
			$g[$k]['orders'] += $this->v($act, 'orders', $id); $g[$k]['sales'] += $this->v($act, 'sales', $id); $g[$k]['amt'] += $this->v($act, 'sales', $id, 'amt'); $g[$k]['target'] += $this->v($act, 'target', $id, 'amt');
		}
		$rows = array_values($g);
		foreach ($rows as &$r) $r['pct'] = $this->pct($r['amt'], $r['target']);
		unset($r);
		usort($rows, function ($x, $y) { return $y['amt'] <=> $x['amt'] ?: $y['staff'] <=> $x['staff']; });
		$cols = array(array('g', $label, 'text'), array('staff', 'Staff', 'num'), array('rec', 'Records added', 'num'), array('fu', 'Follow-ups logged', 'num'), array('calls', 'Calls', 'num'), array('orders', 'Orders', 'num'), array('sales', 'Sales', 'num'), array('amt', 'Sales amount', 'amt'), array('target', 'Target', 'amt'), array('pct', '% of target', 'pct'));
		$tot = $this->total($rows, $cols); if ($tot) $tot['pct'] = $this->pct($tot['amt'], $tot['target']);
		return array('cols' => $cols, 'rows' => $rows, 'total' => $tot,
			'chart' => $rows ? $this->chart('bar', array_column($rows, 'g'), array($this->set('Target', '#00c6f0', array_column($rows, 'target')), $this->set('Sales', '#16a34a', array_column($rows, 'amt'))), true) : null,
			'note' => 'Each person counts in the ' . strtolower($label) . ' set on their user record. Sales by the person who created the order.');
	}

	private function r_a_branch($d1, $d2, $ids)
	{
		return $this->group_summary($d1, $d2, function ($p) { return $p['office'] ? $p['office'] : 'Not set'; }, 'Branch');
	}

	private function r_a_department($d1, $d2, $ids)
	{
		return $this->group_summary($d1, $d2, function ($p) { return $p['role'] ? $p['role'] : 'No role'; }, 'Department');
	}

	private function r_a_staff_activity($d1, $d2, $ids)
	{
		$people = $this->people($this->dash->scope_ids($this->uid, true));
		$act = $this->activity(array_keys($people), $d1, $d2);
		$rows = array();
		foreach ($people as $id => $p) {
			$rows[] = array('name' => $p['name'], 'role' => $p['role'], 'office' => $p['office'], 'rec' => (int)$this->v($act, 'rec', $id), 'fu' => (int)$this->v($act, 'fu', $id),
				'calls' => (int)$this->v($act, 'calls', $id), 'ok' => (int)$this->v($act, 'calls', $id, 'ok'), 'orders' => (int)$this->v($act, 'orders', $id),
				'sales' => (int)$this->v($act, 'sales', $id), 'amt' => $this->v($act, 'sales', $id, 'amt'), 'seen' => $p['last_active_at'] ? substr($p['last_active_at'], 0, 10) : '');
		}
		usort($rows, function ($x, $y) { return ($y['fu'] + $y['calls'] + $y['rec']) <=> ($x['fu'] + $x['calls'] + $x['rec']); });
		$cols = array(array('name', 'Person', 'text'), array('role', 'Role', 'text'), array('office', 'Branch', 'text'), array('rec', 'Records added', 'num'), array('fu', 'Follow-ups logged', 'num'), array('calls', 'Calls', 'num'), array('ok', 'Answered', 'num'), array('orders', 'Orders', 'num'), array('sales', 'Sales', 'num'), array('amt', 'Sales amount', 'amt'), array('seen', 'Last active', 'date'));
		$tot = $this->total($rows, $cols); if ($tot) { $tot['role'] = ''; $tot['office'] = ''; $tot['seen'] = ''; }
		$top = array_slice($rows, 0, 15);
		return array('cols' => $cols, 'rows' => $rows, 'total' => $tot,
			'chart' => $top ? $this->chart('bar', array_column($top, 'name'), array($this->set('Follow-ups', '#0072f0', array_column($top, 'fu')), $this->set('Calls', '#0f8a80', array_column($top, 'calls')), $this->set('Records added', '#7c3aed', array_column($top, 'rec')))) : null,
			'note' => 'Most active first. Chart shows the 15 most active.');
	}

	private function r_a_company_trend($d1, $d2, $ids)
	{
		$mo = $this->months($d1, $d2, true);
		$a = $this->t1(key($mo) . '-01'); end($mo); $b = $this->t2(date('Y-m-t', strtotime(key($mo) . '-01'))); reset($mo);
		$fd = $this->db->escape(key($mo) . '-01');
		$rec = $this->keyed("SELECT LEFT(created, 7) k, COUNT(*) n FROM ci_leads WHERE created BETWEEN $a AND $b GROUP BY k");
		$fu = $this->keyed("SELECT LEFT(followup_date, 7) k, COUNT(*) n FROM ci_followup WHERE followup_date >= $fd AND followup_date <= CURDATE() GROUP BY k");
		$cl = $this->keyed("SELECT DATE_FORMAT(created_at, '%Y-%m') k, COUNT(*) n FROM ci_call_logs WHERE created_at BETWEEN $a AND $b GROUP BY k");
		$or = $this->keyed("SELECT DATE_FORMAT(created_at, '%Y-%m') k, COUNT(*) n FROM ci_orders WHERE created_at BETWEEN $a AND $b GROUP BY k");
		$sa = $this->keyed("SELECT DATE_FORMAT(" . $this->sd() . ", '%Y-%m') k, COUNT(*) n, SUM(O.pricing_amount) amt FROM ci_orders O WHERE " . $this->sale() . " AND " . $this->sd() . " BETWEEN $a AND $b GROUP BY k");
		$rows = array();
		foreach ($mo as $k => $l) $rows[] = array('m' => $l, 'rec' => isset($rec[$k]) ? (int)$rec[$k]['n'] : 0, 'fu' => isset($fu[$k]) ? (int)$fu[$k]['n'] : 0, 'calls' => isset($cl[$k]) ? (int)$cl[$k]['n'] : 0,
			'orders' => isset($or[$k]) ? (int)$or[$k]['n'] : 0, 'sales' => isset($sa[$k]) ? (int)$sa[$k]['n'] : 0, 'amt' => isset($sa[$k]) ? (float)$sa[$k]['amt'] : 0);
		$cols = array(array('m', 'Month', 'text'), array('rec', 'Records added', 'num'), array('fu', 'Follow-ups logged', 'num'), array('calls', 'Calls', 'num'), array('orders', 'Orders', 'num'), array('sales', 'Sales', 'num'), array('amt', 'Sales amount', 'amt'));
		return array('cols' => $cols, 'rows' => $rows, 'total' => $this->total($rows, $cols),
			'chart' => $this->chart('line', array_values($mo), array($this->set('Records added', '#7c3aed', array_column($rows, 'rec')), $this->set('Follow-ups', '#0072f0', array_column($rows, 'fu')), $this->set('Calls', '#0f8a80', array_column($rows, 'calls')), $this->set('Orders', '#ea580c', array_column($rows, 'orders')))));
	}

	private function r_a_no_activity($d1, $d2, $ids)
	{
		$people = $this->people($this->dash->scope_ids($this->uid, true));
		$act = $this->activity(array_keys($people), $d1, $d2);
		$rows = array();
		foreach ($people as $id => $p) {
			if ($this->v($act, 'rec', $id) || $this->v($act, 'fu', $id) || $this->v($act, 'calls', $id) || $this->v($act, 'orders', $id)) continue;
			$rows[] = array('name' => $p['name'], 'role' => $p['role'], 'office' => $p['office'], 'seen' => $p['last_active_at'] ? substr($p['last_active_at'], 0, 10) : '');
		}
		$cols = array(array('name', 'Person', 'text'), array('role', 'Role', 'text'), array('office', 'Branch', 'text'), array('seen', 'Last active', 'date'));
		return array('cols' => $cols, 'rows' => $rows, 'total' => $rows ? array('name' => count($rows) . ' of ' . count($people) . ' people') : null,
			'note' => 'No records added, follow-ups logged, calls or orders in the period. Some roles (HR, IT, Back Office) do not use these, so check before acting.');
	}

	private function r_a_people_check($d1, $d2, $ids)
	{
		$rows = array();
		foreach ($this->dash->tracker($this->dash->scope_ids($this->uid, true), $this->dash->month_window(date('Y-m'))) as $p) {
			if (!$p['fu_overdue'] && !$p['tasks_late']) continue;
			$rows[] = array('name' => $p['name'], 'role' => $p['role'], 'missed' => (int)$p['fu_overdue'], 'oldest' => $p['fu_oldest'], 'today' => (int)$p['fu_today'], 'done' => (int)$p['fu_done'], 'tasks' => (int)$p['tasks_open'], 'late' => (int)$p['tasks_late'], 'calls' => (int)$p['calls']);
		}
		usort($rows, function ($x, $y) { return ($y['missed'] + $y['late']) <=> ($x['missed'] + $x['late']); });
		$cols = array(array('name', 'Person', 'text'), array('role', 'Role', 'text'), array('missed', 'Missed follow-ups', 'num'), array('oldest', 'Oldest missed', 'date'), array('today', 'Due today', 'num'), array('done', 'Logged today', 'num'), array('tasks', 'Open tasks', 'num'), array('late', 'Late tasks', 'num'), array('calls', 'Calls today', 'num'));
		$tot = $this->total($rows, $cols); if ($tot) { $tot['role'] = ''; $tot['oldest'] = ''; }
		$top = array_slice($rows, 0, 15);
		return array('cols' => $cols, 'rows' => $rows, 'total' => $tot,
			'chart' => $top ? $this->chart('bar', array_column($top, 'name'), array($this->set('Missed follow-ups', '#dc2626', array_column($top, 'missed')), $this->set('Late tasks', '#f97316', array_column($top, 'late')))) : null);
	}
}
