<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Query builder with record tracking. See hooks/crm_track_db.php.
 * Only the tables listed in $crm_tracked are watched; every other query runs exactly as before.
 */
class CI_DB extends CI_DB_active_record
{
	/** table => record type shown on the tracking page */
	public static $crm_tracked = array(
		'ci_leads' => 'lead',
		'ci_orders' => 'order',
		'ci_kam_client_details' => 'kam',
		'ci_boleads' => 'backoffice',
		'ci_tickets' => 'ticket',
	);
	/** fields that change on every save and say nothing about the work */
	public static $crm_skip = array('modified', 'modified_at', 'updated_at', 'updated', 'last_updated_date', 'last_active_at', 'is_online', 'current_session_token', 'salesperson_original_data', 'edited_by');

	public static $crm_buffer = array();
	private static $crm_flush_set = false;
	private $crm_busy = false;

	private function crm_table($table)
	{
		if (!is_string($table) || $table === '') return '';
		$t = strtolower(trim(str_replace('`', '', $table)));
		$t = preg_replace('/\s.*$/', '', $t); // "ci_leads A" -> ci_leads
		return isset(self::$crm_tracked[$t]) ? $t : '';
	}

	/** Runs a read without stopping the page if it fails. */
	private function crm_rows($sql)
	{
		$debug = $this->db_debug;
		$this->db_debug = FALSE;
		$this->crm_busy = true;
		$q = $this->query($sql);
		$this->crm_busy = false;
		$this->db_debug = $debug;
		return (is_object($q) && method_exists($q, 'result_array')) ? $q->result_array() : array();
	}

	public function update($table = '', $set = NULL, $where = NULL, $limit = NULL)
	{
		$t = $this->crm_table($table !== '' ? $table : (isset($this->ar_from[0]) ? $this->ar_from[0] : ''));
		if ($t === '' || $this->crm_busy) {
			return parent::update($table, $set, $where, $limit);
		}
		if ($where != NULL) {
			$this->where($where);
			$where = NULL;
		}
		$before = array();
		if (!empty($this->ar_where)) {
			$before = $this->crm_rows('SELECT * FROM `' . $t . '` WHERE ' . implode("\n", $this->ar_where) . ' LIMIT 301');
		}
		$res = parent::update($table, $set, $where, $limit);
		if ($res && $before && count($before) <= 300) {
			$this->crm_diff($t, $before);
		}
		return $res;
	}

	public function insert($table = '', $set = NULL)
	{
		$res = parent::insert($table, $set);
		$t = $this->crm_table($table);
		if ($res && $t !== '' && !$this->crm_busy) {
			$id = (int)$this->insert_id();
			if ($id > 0) self::crm_add($t, $id, 'create', '', '', '');
		}
		return $res;
	}

	public function delete($table = '', $where = '', $limit = NULL, $reset_data = TRUE)
	{
		$t = $this->crm_table($table);
		if ($t === '' || $this->crm_busy) {
			return parent::delete($table, $where, $limit, $reset_data);
		}
		if ($where != '') {
			$this->where($where);
			$where = '';
		}
		$rows = array();
		if (!empty($this->ar_where)) {
			$rows = $this->crm_rows('SELECT * FROM `' . $t . '` WHERE ' . implode("\n", $this->ar_where) . ' LIMIT 51');
		}
		$res = parent::delete($table, $where, $limit, $reset_data);
		if ($res) {
			foreach (array_slice($rows, 0, 50) as $r) {
				if (isset($r['id'])) self::crm_add($t, $r['id'], 'delete', '', self::crm_name($r), '');
			}
		}
		return $res;
	}

	private function crm_diff($t, $before)
	{
		$map = array();
		foreach ($before as $r) {
			if (isset($r['id'])) $map[(int)$r['id']] = $r;
		}
		if (!$map) return;
		$after = $this->crm_rows('SELECT * FROM `' . $t . '` WHERE id IN (' . implode(',', array_keys($map)) . ')');
		foreach ($after as $a) {
			$b = isset($map[(int)$a['id']]) ? $map[(int)$a['id']] : null;
			if (!$b) continue;
			foreach ($a as $f => $v) {
				if (!array_key_exists($f, $b) || in_array($f, self::$crm_skip, true)) continue;
				$old = $b[$f];
				if ((string)$old === (string)$v || self::crm_same($old, $v)) continue;
				// JSON fields (KAM details, schedules): one line per changed entry
				$jo = (is_string($old) && $old !== '' && ($old[0] === '{' || $old[0] === '[')) ? json_decode($old, true) : null;
				$jn = (is_string($v) && $v !== '' && ($v[0] === '{' || $v[0] === '[')) ? json_decode($v, true) : null;
				if ((is_array($jo) || $old === null || $old === '') && is_array($jn) && (is_array($jo) || $jn)) {
					$fo = self::crm_flat(is_array($jo) ? $jo : array());
					$fn = self::crm_flat($jn);
					$n = 0;
					foreach (array_unique(array_merge(array_keys($fo), array_keys($fn))) as $k) {
						$ov = isset($fo[$k]) ? $fo[$k] : '';
						$nv = isset($fn[$k]) ? $fn[$k] : '';
						if ((string)$ov === (string)$nv) continue;
						if (++$n > 40) break;
						self::crm_add($t, $a['id'], 'update', $f . '.' . $k, $ov, $nv);
					}
					continue;
				}
				self::crm_add($t, $a['id'], 'update', $f, $old, $v);
			}
		}
	}

	private static function crm_flat($arr, $pre = '')
	{
		$out = array();
		foreach ((array)$arr as $k => $v) {
			$key = $pre === '' ? (string)$k : $pre . '.' . $k;
			if (is_array($v)) {
				if (count($out) > 200) break;
				$out += self::crm_flat($v, $key);
			} else {
				$out[$key] = is_bool($v) ? ($v ? 'Yes' : 'No') : (string)$v;
			}
		}
		return $out;
	}

	/** Empty and zero count as the same (an empty amount saved as 0 is not a change). */
	private static function crm_same($a, $b)
	{
		$z = function ($x) { $x = trim((string)$x); return $x === '' || (is_numeric($x) && (float)$x == 0) || $x === '0000-00-00' || $x === '0000-00-00 00:00:00'; };
		return $z($a) && $z($b);
	}

	private static function crm_name($r)
	{
		foreach (array('customer', 'client_name', 'company_name', 'order_no', 'ticket_number', 'subject') as $k) {
			if (!empty($r[$k])) return (string)$r[$k];
		}
		return '';
	}

	public static function crm_add($t, $id, $action, $field, $old, $new)
	{
		if (count(self::$crm_buffer) >= 3000) return;
		$uid = null;
		$uname = 'System';
		if (class_exists('CI_Controller', false) && function_exists('get_instance')) {
			$CI = get_instance();
			if (is_object($CI) && isset($CI->session) && is_object($CI->session)) {
				$u = (int)$CI->session->userdata('manager_id');
				if ($u > 0) {
					$uid = $u;
					$n = $CI->session->userdata('manager_name');
					$uname = $n ? (string)$n : 'User ' . $u;
				}
			}
		}
		$page = isset($_SERVER['REQUEST_URI']) ? (string)parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : (PHP_SAPI === 'cli' ? 'cli' : '');
		$page = preg_replace('#^.*?/(manager|api)/#', '$1/', $page);
		self::$crm_buffer[] = array(
			self::$crm_tracked[$t], (int)$id, $action, substr((string)$field, 0, 100),
			mb_substr((string)$old, 0, 2000), mb_substr((string)$new, 0, 2000),
			$uid, mb_substr($uname, 0, 150), mb_substr((string)$page, 0, 190), date('Y-m-d H:i:s'),
		);
		if (!self::$crm_flush_set) {
			self::$crm_flush_set = true;
			register_shutdown_function(array('CI_DB', 'crm_flush'));
		}
	}

	public static function crm_ensure($db)
	{
		$db->query("CREATE TABLE IF NOT EXISTS ci_record_changes (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT,
			rtype VARCHAR(20) NOT NULL,
			rid INT NOT NULL,
			action VARCHAR(10) NOT NULL DEFAULT 'update',
			field VARCHAR(100) NOT NULL DEFAULT '',
			old_value TEXT NULL,
			new_value TEXT NULL,
			user_id INT NULL,
			user_name VARCHAR(150) NOT NULL DEFAULT '',
			page VARCHAR(190) NOT NULL DEFAULT '',
			created_at DATETIME NOT NULL,
			PRIMARY KEY (id),
			KEY rec (rtype, rid, id),
			KEY who (user_id, created_at),
			KEY at (created_at)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
	}

	/** Writes the stored changes once the page has finished. */
	public static function crm_flush()
	{
		if (!self::$crm_buffer || !function_exists('get_instance')) return;
		$CI = get_instance();
		if (!is_object($CI) || !isset($CI->db) || !is_object($CI->db)) return;
		$db = $CI->db;
		$debug = $db->db_debug;
		$db->db_debug = FALSE;
		if (!$db->conn_id) $db->initialize();
		if ($db->conn_id) {
			self::crm_ensure($db);
			$rows = self::$crm_buffer;
			self::$crm_buffer = array();
			foreach (array_chunk($rows, 100) as $chunk) {
				$vals = array();
				foreach ($chunk as $r) {
					$vals[] = '(' . implode(',', array_map(array($db, 'escape'), $r)) . ')';
				}
				$db->query('INSERT INTO ci_record_changes (rtype, rid, action, field, old_value, new_value, user_id, user_name, page, created_at) VALUES ' . implode(',', $vals));
			}
		}
		$db->db_debug = $debug;
	}
}
