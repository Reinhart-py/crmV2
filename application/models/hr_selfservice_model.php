<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Employee self-service data: leave checks, training modules with per-employee
 * progress, and the HR knowledge base (Search Topics).
 * Tables are created on first use, like dialer_campaign_model.
 */
class Hr_selfservice_model extends CI_Model {

	private static $ready = false;

	public function __construct()
	{
		parent::__construct();
		$this->ensure_tables();
	}

	public function ensure_tables()
	{
		if (self::$ready) {
			return;
		}
		self::$ready = true;

		$this->db->query("CREATE TABLE IF NOT EXISTS ci_training_modules (
			id INT(11) NOT NULL AUTO_INCREMENT,
			title VARCHAR(255) NOT NULL,
			category VARCHAR(100) NOT NULL DEFAULT 'General',
			description TEXT NULL,
			content_url VARCHAR(500) NULL,
			duration_minutes INT(11) NOT NULL DEFAULT 0,
			is_mandatory TINYINT(1) NOT NULL DEFAULT 0,
			audience_role_id INT(11) NOT NULL DEFAULT 0,
			due_date DATE NULL,
			status VARCHAR(20) NOT NULL DEFAULT 'active',
			created_by INT(11) NOT NULL DEFAULT 0,
			created_at DATETIME NULL,
			updated_at DATETIME NULL,
			PRIMARY KEY (id),
			KEY idx_status (status),
			KEY idx_audience (audience_role_id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");

		$this->db->query("CREATE TABLE IF NOT EXISTS ci_training_progress (
			id INT(11) NOT NULL AUTO_INCREMENT,
			module_id INT(11) NOT NULL,
			user_id INT(11) NOT NULL,
			status VARCHAR(20) NOT NULL DEFAULT 'In Progress',
			started_at DATETIME NULL,
			completed_at DATETIME NULL,
			PRIMARY KEY (id),
			UNIQUE KEY uq_module_user (module_id, user_id),
			KEY idx_user (user_id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");

		$this->db->query("CREATE TABLE IF NOT EXISTS ci_hr_topics (
			id INT(11) NOT NULL AUTO_INCREMENT,
			title VARCHAR(255) NOT NULL,
			category VARCHAR(100) NOT NULL DEFAULT 'General',
			summary TEXT NULL,
			body TEXT NULL,
			status VARCHAR(20) NOT NULL DEFAULT 'active',
			created_by INT(11) NOT NULL DEFAULT 0,
			created_at DATETIME NULL,
			updated_at DATETIME NULL,
			PRIMARY KEY (id),
			KEY idx_status (status)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");

		// Carry over the topics that were previously hard-coded in the controller.
		if ((int)$this->db->count_all('ci_hr_topics') === 0) {
			$now = date('Y-m-d H:i:s');
			$seed = array(
				array('Leave Policy & Annual Holiday Entitlements', 'Leaves & Attendance', 'Guidelines on statutory paid annual leave, sick leave certificate requirements, and advance approval notice.'),
				array('Working Hours, Shift Rosters & Grace Periods', 'Operations', 'Overview of 9-hour daily shifts, check-in punch limits, overtime authorization, and weekly off schedules.'),
				array('Employee Health Insurance & Medical Coverage', 'Benefits', 'Comprehensive policy on health network cards, direct billing clinics, dental riders, and dependent coverage.'),
				array('Performance Appraisals, KPIs & Commission Payouts', 'Compensation', 'Calculation methodology for monthly sales incentives, quarterly target achievements, and yearly reviews.'),
				array('Code of Conduct, IT Usage & Data Privacy Standard', 'Governance', 'Rules governing CRM client confidentiality, password security, device maintenance, and professional ethics.')
			);
			foreach ($seed as $s) {
				$this->db->insert('ci_hr_topics', array(
					'title' => $s[0], 'category' => $s[1], 'summary' => $s[2], 'body' => '',
					'status' => 'active', 'created_by' => 1, 'created_at' => $now, 'updated_at' => $now
				));
			}
		}
	}

	/* ---------- Leave ---------- */

	public function leave_overlap($user_id, $from, $to, $exclude_id = 0)
	{
		$this->db->from('ci_leaves')
			->where('user_id', (int)$user_id)
			->where_in('status', array('Pending', 'Approved'))
			->where('from_date <=', $to)
			->where('to_date >=', $from);
		if ((int)$exclude_id > 0) {
			$this->db->where('id !=', (int)$exclude_id);
		}
		return $this->db->get()->row_array();
	}

	/* ---------- Training ---------- */

	private function _audience_sql($role_id)
	{
		return '(M.audience_role_id = 0 OR M.audience_role_id = ' . (int)$role_id . ')';
	}

	/** Active modules this employee should take, with their own progress. */
	public function my_training($user_id, $role_id)
	{
		$uid = (int)$user_id;
		return $this->db->select('M.*, P.status AS my_status, P.started_at, P.completed_at, R.title AS audience_title, (M.due_date IS NULL) AS no_due', false)
			->from('ci_training_modules M')
			->join('ci_training_progress P', 'P.module_id = M.id AND P.user_id = ' . $uid, 'left')
			->join('ci_roles R', 'R.id = M.audience_role_id', 'left')
			->where('M.status', 'active')
			->where($this->_audience_sql($role_id), null, false)
			->order_by('M.is_mandatory', 'DESC')
			->order_by('no_due', 'ASC')
			->order_by('M.due_date', 'ASC')
			->order_by('M.id', 'DESC')
			->get()->result_array();
	}

	public function training_summary($user_id, $role_id)
	{
		$rows = $this->my_training($user_id, $role_id);
		$out = array('assigned' => count($rows), 'completed' => 0, 'in_progress' => 0, 'mandatory_open' => 0);
		foreach ($rows as $r) {
			if ($r['my_status'] === 'Completed') {
				$out['completed']++;
			} else {
				if ($r['my_status'] === 'In Progress') {
					$out['in_progress']++;
				}
				if ((int)$r['is_mandatory'] === 1) {
					$out['mandatory_open']++;
				}
			}
		}
		return $out;
	}

	/** All modules with completion counts, for HR management. */
	public function all_training()
	{
		$mods = $this->db->select("M.*, R.title AS audience_title, (M.status = 'active') AS is_active", false)
			->from('ci_training_modules M')
			->join('ci_roles R', 'R.id = M.audience_role_id', 'left')
			->order_by('is_active', 'DESC')
			->order_by('M.id', 'DESC')
			->get()->result_array();
		foreach ($mods as &$m) {
			$aud = $this->db->from('ci_user')->where('status', 1);
			if ((int)$m['audience_role_id'] > 0) {
				$aud->where('user_type', (int)$m['audience_role_id']);
			}
			$m['audience_count'] = (int)$aud->count_all_results();
			$m['completed_count'] = (int)$this->db->where('module_id', (int)$m['id'])->where('status', 'Completed')->count_all_results('ci_training_progress');
			$m['started_count'] = (int)$this->db->where('module_id', (int)$m['id'])->count_all_results('ci_training_progress');
		}
		unset($m);
		return $mods;
	}

	public function module_visible_to($module_id, $role_id)
	{
		return $this->db->from('ci_training_modules M')
			->where('M.id', (int)$module_id)
			->where('M.status', 'active')
			->where($this->_audience_sql($role_id), null, false)
			->get()->row_array();
	}

	public function set_progress($module_id, $user_id, $status)
	{
		$now = date('Y-m-d H:i:s');
		$row = $this->db->where('module_id', (int)$module_id)->where('user_id', (int)$user_id)->get('ci_training_progress')->row_array();
		if (empty($row)) {
			$this->db->insert('ci_training_progress', array(
				'module_id' => (int)$module_id,
				'user_id' => (int)$user_id,
				'status' => $status,
				'started_at' => $now,
				'completed_at' => ($status === 'Completed') ? $now : null
			));
			return;
		}
		$upd = array('status' => $status);
		$upd['completed_at'] = ($status === 'Completed') ? $now : null;
		$this->db->where('id', (int)$row['id'])->update('ci_training_progress', $upd);
	}

	/* ---------- Search Topics ---------- */

	public function topics($query = '', $category = '', $include_archived = false)
	{
		$this->db->from('ci_hr_topics');
		if (!$include_archived) {
			$this->db->where('status', 'active');
		}
		if ($category !== '') {
			$this->db->where('category', $category);
		}
		if ($query !== '') {
			foreach (preg_split('/\s+/', $query) as $word) {
				if ($word === '') {
					continue;
				}
				$w = $this->db->escape_like_str($word);
				$this->db->where("(title LIKE '%{$w}%' OR category LIKE '%{$w}%' OR summary LIKE '%{$w}%' OR body LIKE '%{$w}%')", null, false);
			}
		}
		return $this->db->order_by('status', 'ASC')->order_by('category', 'ASC')->order_by('title', 'ASC')->get()->result_array();
	}

	public function topic_categories()
	{
		$rows = $this->db->distinct()->select('category')->where('status', 'active')->order_by('category', 'ASC')->get('ci_hr_topics')->result_array();
		return array_map(function ($r) { return $r['category']; }, $rows);
	}
}
