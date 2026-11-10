<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Record Tracking and User Activity (System > Activity), admins only.
 * - ci_record_changes: every change to a lead, order, KAM client, back office or ticket record (see hooks/crm_track_db.php)
 * - ci_user_activity: one row per person per minute in which they used the CRM (see the script at the end of elements/header.php)
 */
class Tracking_model extends CI_Model
{
	const IDLE_MIN = 10; // minutes without any activity that count as idle

	public static $types = array(
		'lead' => array('Lead / Database', 'ci_leads', 'fa-address-card-o'),
		'order' => array('Order', 'ci_orders', 'fa-shopping-cart'),
		'kam' => array('KAM client', 'ci_kam_client_details', 'fa-briefcase'),
		'backoffice' => array('Back office', 'ci_boleads', 'fa-folder-open-o'),
		'ticket' => array('Ticket', 'ci_tickets', 'fa-ticket'),
	);

	private static $labels = array(
		'customer' => 'Company', 'poc' => 'Contact person', 'poc2' => 'Second contact', 'contact1' => 'Primary phone', 'contact2' => 'Second phone',
		'alt_contact' => 'Alt phone', 'alt_mobile' => 'Alt mobile', 'mobile' => 'Mobile', 'email' => 'Email', 'status' => 'Status', 'remark' => 'Remark',
		'lead_assign' => 'Owner', 'agents' => 'Shared with', 'created_by' => 'Created by', 'followup_date' => 'Follow-up date', 'followup_time' => 'Follow-up time',
		'm_date' => 'Meeting date', 'm_time' => 'Meeting time', 'close_date' => 'Close date', 'dnd_status' => 'DNCR / DND', 'dnd_check_date' => 'DNCR date',
		'lost_reason' => 'Lost reason', 'status_category' => 'Status detail', 'status_remarks' => 'Status remarks', 'company_status' => 'Company status',
		'call_status' => 'Call status', 'qty' => 'Quantity', 'mrc' => 'MRC', 'mrr' => 'MRR', 'otc' => 'OTC', 'revenue' => 'Revenue', 'lead_source' => 'Source',
		'lead_cat' => 'Lead category', 's_cat' => 'Service category', 'p_cat' => 'Product', 'p_details' => 'Product details', 'business_category' => 'Business category',
		'industry_category' => 'Industry', 'database_category' => 'Database category', 'location' => 'Location', 'address' => 'Address', 'city' => 'City',
		'quote_status' => 'Quote status', 'quote_amount' => 'Quote amount', 'quote_mode' => 'Quote mode', 'vertical_id' => 'Vertical', 'admin_status' => 'Approval',
		'tl_number' => 'Trade licence', 'tl_authority' => 'Licence authority', 'tl_expire_date' => 'Licence expiry', 'followup_by' => 'Follow-up by',
		'assigned_to' => 'Assigned to', 'assigned_dept' => 'Department', 'approval_status' => 'Approval', 'approved_by' => 'Approved by', 'approval_remarks' => 'Approval remarks',
		'documents_status' => 'Documents', 'pricing_amount' => 'Price', 'service_title' => 'Service', 'client_name' => 'Client', 'company_name' => 'Company',
		'owner_name' => 'Owner name', 'executive_id' => 'Executive', 'executive_name' => 'Executive name', 'group_name' => 'Group', 'collaborators' => 'Collaborators',
		'bo_status' => 'Back office status', 'bo_remark' => 'Back office remark', 'bo_cat' => 'Back office category', 'bo_date' => 'Back office date',
		'priority' => 'Priority', 'subject' => 'Subject', 'resolved_by' => 'Resolved by', 'assigned_users' => 'Assigned users', 'deadline_date' => 'Deadline',
	);
	private static $user_fields = array('lead_assign', 'created_by', 'followup_by', 'assigned_to', 'approved_by', 'executive_id', 'resolved_by', 'sales_converted_by', 'last_updated_by', 'raised_by');
	private $user_names = null;
	private $status_names = null;

	public function ensure()
	{
		CI_DB::crm_ensure($this->db);
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_user_activity (
			user_id INT NOT NULL,
			minute DATETIME NOT NULL,
			page VARCHAR(120) NOT NULL DEFAULT '',
			PRIMARY KEY (user_id, minute),
			KEY at (minute)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
	}

	public function ping($uid, $page)
	{
		$uid = (int)$uid;
		if ($uid <= 0) return;
		$sql = 'INSERT IGNORE INTO ci_user_activity (user_id, minute, page) VALUES (?, ?, ?)';
		$args = array($uid, date('Y-m-d H:i:00'), mb_substr((string)$page, 0, 120));
		$debug = $this->db->db_debug;
		$this->db->db_debug = FALSE;
		if (!$this->db->query($sql, $args)) {
			$this->ensure();
			$this->db->query($sql, $args);
		}
		if (mt_rand(1, 2000) === 1) { // keep a year
			$this->db->query("DELETE FROM ci_user_activity WHERE minute < ?", array(date('Y-m-d', strtotime('-365 days'))));
		}
		$this->db->db_debug = $debug;
	}

	/* ---------- shared lists ---------- */

	public function departments()
	{
		return $this->db->query("SELECT id, title FROM ci_roles WHERE status = 1 OR status IS NULL ORDER BY title")->result_array();
	}

	public function staff($dept = 0, $q = '', $active_only = true)
	{
		$this->db->select('U.id, U.name, U.email, U.user_type, U.is_online, R.title AS dept')
			->from('ci_user U')->join('ci_roles R', 'R.id = U.user_type', 'left');
		if ($active_only) $this->db->where('U.status', 1);
		if ($dept > 0) $this->db->where('U.user_type', (int)$dept);
		if ($q !== '') $this->db->like('U.name', $q);
		return $this->db->order_by('U.name')->get()->result_array();
	}

	public function user_name($id)
	{
		if ($this->user_names === null) {
			$this->user_names = array();
			foreach ($this->db->query('SELECT id, name FROM ci_user')->result_array() as $r) $this->user_names[(int)$r['id']] = $r['name'];
		}
		return isset($this->user_names[(int)$id]) ? $this->user_names[(int)$id] : '';
	}

	private $lookups = array();
	private function lookup($table, $col, $id)
	{
		if (!isset($this->lookups[$table])) {
			$this->lookups[$table] = array();
			foreach ($this->db->query("SELECT id, `$col` AS v FROM `$table`")->result_array() as $r) $this->lookups[$table][(int)$r['id']] = $r['v'];
		}
		return isset($this->lookups[$table][(int)$id]) ? (string)$this->lookups[$table][(int)$id] : '';
	}

	private function status_name($id)
	{
		if ($this->status_names === null) {
			$this->status_names = array();
			foreach ($this->db->query('SELECT id, status FROM ci_status')->result_array() as $r) $this->status_names[(int)$r['id']] = $r['status'];
		}
		return isset($this->status_names[(int)$id]) ? $this->status_names[(int)$id] : $id;
	}

	/** Field name and values in plain words. */
	public function nice($rtype, $field, $old, $new)
	{
		$base = preg_replace('/\..*$/', '', $field);
		$label = isset(self::$labels[$base]) ? self::$labels[$base] : ucfirst(str_replace('_', ' ', $base));
		if ($base !== $field) $label .= ': ' . ucfirst(str_replace(array('_', '.'), array(' ', ' > '), substr($field, strlen($base) + 1)));
		$fmt = function ($v) use ($rtype, $base) {
			$v = (string)$v;
			if ($v === '') return '';
			if (in_array($base, self::$user_fields, true) && ctype_digit($v)) { $n = $this->user_name($v); return $n !== '' ? $n : 'User ' . $v; }
			if ($base === 'agents') {
				$out = array();
				foreach (array_filter(explode(',', $v)) as $id) { $n = $this->user_name(trim($id)); $out[] = $n !== '' ? $n : trim($id); }
				return implode(', ', $out);
			}
			if ($base === 'status' && $rtype === 'lead' && ctype_digit($v)) return $this->status_name($v);
			if (ctype_digit($v) && in_array($base, array('s_cat', 'p_cat', 'lead_cat', 'business_category', 'industry_category', 'database_category', 'lead_source', 'c_partner'), true)) { $n = $this->lookup('ci_terms', 'title', $v); return $n !== '' ? $n : $v; }
			if (ctype_digit($v) && ($base === 'country')) { $n = $this->lookup('countries', 'name', $v); return $n !== '' ? $n : $v; }
			return $v;
		};
		return array($label, $fmt($old), $fmt($new));
	}

	/* ---------- Record Tracking ---------- */

	private function change_where($f)
	{
		$w = array('1=1');
		$e = function ($v) { return $this->db->escape($v); };
		if (!empty($f['type']) && isset(self::$types[$f['type']])) $w[] = 'C.rtype = ' . $e($f['type']);
		if (!empty($f['user'])) $w[] = 'C.user_id = ' . (int)$f['user'];
		if (!empty($f['dept'])) $w[] = 'U.user_type = ' . (int)$f['dept'];
		if (!empty($f['from'])) $w[] = 'C.created_at >= ' . $e($f['from'] . ' 00:00:00');
		if (!empty($f['to'])) $w[] = 'C.created_at <= ' . $e($f['to'] . ' 23:59:59');
		if (!empty($f['action'])) $w[] = 'C.action = ' . $e($f['action']);
		if (isset($f['q']) && $f['q'] !== '') {
			$q = trim($f['q']);
			$like = $e('%' . $q . '%');
			$or = array();
			if (ctype_digit($q)) $or[] = 'C.rid = ' . (int)$q;
			$or[] = "(C.rtype = 'lead' AND C.rid IN (SELECT id FROM (SELECT id FROM ci_leads WHERE customer LIKE $like OR lead_id LIKE $like OR poc LIKE $like OR contact1 LIKE $like LIMIT 500) x1))";
			$or[] = "(C.rtype = 'order' AND C.rid IN (SELECT id FROM (SELECT id FROM ci_orders WHERE order_no LIKE $like OR client_name LIKE $like OR company_name LIKE $like LIMIT 500) x2))";
			$or[] = "(C.rtype = 'kam' AND C.rid IN (SELECT id FROM (SELECT id FROM ci_kam_client_details WHERE client_name LIKE $like OR group_name LIKE $like LIMIT 500) x3))";
			$or[] = "(C.rtype = 'ticket' AND C.rid IN (SELECT id FROM (SELECT id FROM ci_tickets WHERE ticket_number LIKE $like OR subject LIKE $like OR customer LIKE $like LIMIT 500) x4))";
			$or[] = 'C.old_value LIKE ' . $like . ' OR C.new_value LIKE ' . $like;
			$w[] = '(' . implode(' OR ', $or) . ')';
		}
		return implode(' AND ', $w);
	}

	/** When field changes started to be recorded. */
	public function since()
	{
		$this->ensure();
		$r = $this->db->query('SELECT MIN(created_at) AS m FROM ci_record_changes')->row_array();
		return $r ? $r['m'] : null;
	}

	public function change_kpis($f)
	{
		$this->ensure();
		$w = $this->change_where($f);
		return $this->db->query("SELECT COUNT(*) AS changes, COUNT(DISTINCT C.rtype, C.rid) AS records, COUNT(DISTINCT C.user_id) AS people,
			SUM(C.action = 'delete') AS deleted, SUM(C.action = 'create') AS created
			FROM ci_record_changes C LEFT JOIN ci_user U ON U.id = C.user_id WHERE $w")->row_array();
	}

	/** Changes grouped into one entry per save (same person, record and second). */
	public function change_feed($f, $page, $per = 40)
	{
		$this->ensure();
		$w = $this->change_where($f);
		$off = max(0, ($page - 1) * $per);
		$saves = $this->db->query("SELECT C.rtype, C.rid, C.user_id, C.created_at, MIN(C.id) AS first_id, COUNT(*) AS n
			FROM ci_record_changes C LEFT JOIN ci_user U ON U.id = C.user_id WHERE $w
			GROUP BY C.rtype, C.rid, C.user_id, C.created_at ORDER BY C.created_at DESC, first_id DESC LIMIT " . ($per + 1) . " OFFSET $off")->result_array();
		$more = count($saves) > $per;
		$saves = array_slice($saves, 0, $per);
		return array($this->fill_saves($saves, $w), $more);
	}

	private function fill_saves($saves, $w = '1=1')
	{
		if (!$saves) return array();
		$keys = array();
		foreach ($saves as $s) $keys[] = '(C.rtype = ' . $this->db->escape($s['rtype']) . ' AND C.rid = ' . (int)$s['rid'] . ' AND C.created_at = ' . $this->db->escape($s['created_at']) . ')';
		$rows = $this->db->query("SELECT C.*, U.name AS uname, R.title AS dept FROM ci_record_changes C
			LEFT JOIN ci_user U ON U.id = C.user_id LEFT JOIN ci_roles R ON R.id = U.user_type
			WHERE (" . implode(' OR ', $keys) . ") ORDER BY C.id")->result_array();
		$by = array();
		foreach ($rows as $r) $by[$r['rtype'] . ':' . $r['rid'] . ':' . (int)$r['user_id'] . ':' . $r['created_at']][] = $r;
		$names = $this->record_names($saves);
		$out = array();
		foreach ($saves as $s) {
			$k = $s['rtype'] . ':' . $s['rid'] . ':' . (int)$s['user_id'] . ':' . $s['created_at'];
			if (empty($by[$k])) continue;
			$first = $by[$k][0];
			$items = array();
			$action = 'update';
			foreach ($by[$k] as $r) {
				if ($r['action'] !== 'update') { $action = $r['action']; if ($r['action'] === 'delete' && $r['old_value'] !== '') $items[] = array('Record', $r['old_value'], ''); continue; }
				$items[] = $this->nice($r['rtype'], $r['field'], $r['old_value'], $r['new_value']);
			}
			$out[] = array(
				'rtype' => $s['rtype'], 'rid' => (int)$s['rid'], 'at' => $s['created_at'], 'action' => $action,
				'user_id' => (int)$first['user_id'], 'user' => $first['uname'] ? $first['uname'] : $first['user_name'], 'dept' => (string)$first['dept'],
				'page' => $first['page'], 'items' => $items,
				'name' => isset($names[$s['rtype'] . ':' . $s['rid']]) ? $names[$s['rtype'] . ':' . $s['rid']] : '',
			);
		}
		return $out;
	}

	/** "type:id" => display name, for the records in a list. */
	public function record_names($list)
	{
		$ids = array();
		foreach ($list as $s) $ids[$s['rtype']][] = (int)$s['rid'];
		$sql = array(
			'lead' => "SELECT id, CONCAT(IFNULL(NULLIF(customer, ''), 'No name'), IF(IFNULL(lead_id, '') <> '', CONCAT(' (', lead_id, ')'), '')) AS nm FROM ci_leads WHERE id IN (%s)",
			'order' => "SELECT id, CONCAT(IFNULL(NULLIF(order_no, ''), CONCAT('Order ', id)), ' - ', IFNULL(NULLIF(company_name, ''), IFNULL(client_name, ''))) AS nm FROM ci_orders WHERE id IN (%s)",
			'kam' => "SELECT id, IFNULL(NULLIF(client_name, ''), CONCAT('KAM client ', id)) AS nm FROM ci_kam_client_details WHERE id IN (%s)",
			'backoffice' => "SELECT B.id, CONCAT('Back office: ', IFNULL(L.customer, CONCAT('record ', B.id))) AS nm FROM ci_boleads B LEFT JOIN ci_leads L ON L.id = B.lead_id WHERE B.id IN (%s)",
			'ticket' => "SELECT id, CONCAT(IFNULL(ticket_number, ''), ' ', IFNULL(NULLIF(subject, ''), IFNULL(customer, ''))) AS nm FROM ci_tickets WHERE id IN (%s)",
		);
		$out = array();
		foreach ($ids as $t => $list_ids) {
			if (!isset($sql[$t])) continue;
			foreach ($this->db->query(sprintf($sql[$t], implode(',', array_unique($list_ids))))->result_array() as $r) $out[$t . ':' . $r['id']] = trim($r['nm']);
		}
		return $out;
	}

	/** One record: its details and everything that happened to it, newest first. */
	public function record($type, $id)
	{
		$this->ensure();
		if (!isset(self::$types[$type])) return null;
		$id = (int)$id;
		$row = $this->db->where('id', $id)->get(self::$types[$type][1])->row_array();
		$names = $this->record_names(array(array('rtype' => $type, 'rid' => $id)));
		$rec = array('type' => $type, 'id' => $id, 'row' => $row, 'name' => isset($names[$type . ':' . $id]) ? $names[$type . ':' . $id] : ucfirst($type) . ' ' . $id, 'facts' => array(), 'lead_id' => 0);
		$ev = array();
		$add = function ($at, $kind, $who, $title, $lines = array(), $icon = 'fa-circle') use (&$ev) {
			if (!$at || $at === '0000-00-00 00:00:00') return;
			$ev[] = array('at' => $at, 'kind' => $kind, 'who' => (string)$who, 'title' => $title, 'lines' => $lines, 'icon' => $icon);
		};

		if ($row) {
			if ($type === 'lead') {
				$rec['lead_id'] = $id;
				$rec['facts'] = array('Ref' => $row['lead_id'], 'Owner' => $this->user_name($row['lead_assign']), 'Status' => $this->status_name($row['status']), 'Created' => $row['created'], 'Created by' => $this->user_name($row['created_by']));
				if (!empty($row['dnd_status']) && strtolower($row['dnd_status']) === 'yes') $rec['facts']['DNCR'] = 'Yes - do not call';
				$add($row['created'], 'create', $this->user_name($row['created_by']), 'Record created', array(), 'fa-plus');
			} elseif ($type === 'order') {
				$rec['lead_id'] = (int)$row['lead_id'];
				$rec['facts'] = array('Order no' => $row['order_no'], 'Company' => $row['company_name'], 'Status' => $row['status'], 'Approval' => $row['approval_status'], 'Assigned to' => $this->user_name($row['assigned_to']), 'Created' => $row['created_at']);
				$add($row['created_at'], 'create', $this->user_name($row['created_by']), 'Order created', array(), 'fa-plus');
			} elseif ($type === 'kam') {
				$rec['lead_id'] = (int)$row['lead_id'];
				$rec['facts'] = array('Client' => $row['client_name'], 'Group' => $row['group_name'], 'Executive' => $row['executive_name'], 'Created' => $row['created_at']);
				$add($row['created_at'], 'create', '', 'KAM client added', array(), 'fa-plus');
			} elseif ($type === 'backoffice') {
				$rec['lead_id'] = (int)$row['lead_id'];
				$rec['facts'] = array('Category' => $row['bo_cat'], 'Status' => $row['bo_status'], 'Ref' => $row['bo_ref'], 'Created' => $row['created']);
				$add($row['created'], 'create', '', 'Back office record created', array(), 'fa-plus');
			} elseif ($type === 'ticket') {
				$rec['facts'] = array('Ticket' => $row['ticket_number'], 'Subject' => $row['subject'], 'Status' => $row['status'], 'Created' => $row['created_at']);
				$add($row['created_at'], 'create', $this->user_name($row['created_by']), 'Ticket created', array(), 'fa-plus');
			}
		}

		// Field changes, one entry per save
		$saves = $this->db->query("SELECT rtype, rid, user_id, created_at, MIN(id) AS first_id FROM ci_record_changes WHERE rtype = ? AND rid = ? GROUP BY rtype, rid, user_id, created_at ORDER BY created_at DESC LIMIT 400", array($type, $id))->result_array();
		foreach ($this->fill_saves($saves) as $s) {
			if ($s['action'] === 'create') continue; // shown from the record itself
			$lines = array();
			foreach ($s['items'] as $it) $lines[] = $it;
			$add($s['at'], $s['action'] === 'delete' ? 'delete' : 'change', $s['user'], $s['action'] === 'delete' ? 'Record deleted' : (count($lines) === 1 ? 'Changed ' . $lines[0][0] : 'Changed ' . count($lines) . ' fields'), $lines, $s['action'] === 'delete' ? 'fa-trash' : 'fa-pencil');
		}

		$lid = (int)$rec['lead_id'];
		if ($lid > 0) {
			foreach ($this->db->query("SELECT * FROM ci_pipeline_logs WHERE lead_id = ? ORDER BY id DESC LIMIT 200", array($lid))->result_array() as $p) {
				$lines = array();
				if ($p['stage_from'] !== '' || $p['stage_to'] !== '') $lines[] = array('Stage', $p['stage_from'], $p['stage_to']);
				if (!empty($p['remarks'])) $lines[] = array('Remark', '', $p['remarks']);
				$add($p['created_at'], 'pipeline', $p['action_by_name'] ? $p['action_by_name'] : $this->user_name($p['action_by']), ucfirst(str_replace('_', ' ', $p['action'])), $lines, 'fa-random');
			}
			foreach ($this->db->query("SELECT * FROM ci_call_logs WHERE lead_id = ? ORDER BY id DESC LIMIT 200", array($lid))->result_array() as $c) {
				$d = (int)$c['duration'];
				$lines = array(array('Number', '', $c['phone_number']), array('Result', '', trim($c['disposition'] . ($d > 0 ? ' - ' . gmdate($d >= 3600 ? 'H:i:s' : 'i:s', $d) : ''))));
				if (!empty($c['notes'])) $lines[] = array('Notes', '', $c['notes']);
				$add($c['created_at'], 'call', $this->user_name($c['user_id']), 'Call', $lines, 'fa-phone');
			}
			foreach ($this->db->query("SELECT * FROM ci_lead_transfers WHERE lead_id = ? ORDER BY id DESC LIMIT 100", array($lid))->result_array() as $t) {
				$add($t['created_at'], 'transfer', $this->user_name($t['transferred_by']), 'Transferred', array(array('Owner', $this->user_name($t['from_user_id']), $this->user_name($t['to_user_id']))), 'fa-exchange');
			}
			foreach ($this->db->query("SELECT * FROM ci_followup WHERE lead_id = ? ORDER BY id DESC LIMIT 200", array($lid))->result_array() as $fu) {
				$at = trim($fu['followup_date'] . ' ' . (preg_match('/\d/', (string)$fu['followup_time']) ? date('H:i:s', strtotime($fu['followup_time'])) : '00:00:00'));
				$lines = array();
				if (!empty($fu['followup_remark'])) $lines[] = array('Remark', '', $fu['followup_remark']);
				if (!empty($fu['followup_result'])) $lines[] = array('Result', '', $fu['followup_result']);
				$add($at, 'followup', $this->user_name($fu['user_id']), 'Follow-up', $lines, 'fa-clock-o');
			}
			if ($type !== 'order') {
				foreach ($this->db->query("SELECT id, order_no, status, created_at, created_by FROM ci_orders WHERE lead_id = ? ORDER BY id DESC LIMIT 50", array($lid))->result_array() as $o) {
					$add($o['created_at'], 'order', $this->user_name($o['created_by']), 'Order ' . $o['order_no'] . ' created', array(array('Status', '', $o['status'])), 'fa-shopping-cart');
				}
			}
		}
		if ($type === 'order') {
			foreach ($this->db->query("SELECT * FROM ci_order_history WHERE order_id = ? ORDER BY id DESC LIMIT 200", array($id))->result_array() as $h) {
				$add($h['created_at'], 'order', $h['action_by_name'] ? $h['action_by_name'] : $this->user_name($h['action_by']), ucfirst(str_replace('_', ' ', $h['action'])), $h['notes'] !== '' && $h['notes'] !== null ? array(array('Notes', '', $h['notes'])) : array(), 'fa-shopping-cart');
			}
		}
		usort($ev, function ($a, $b) { return strcmp($b['at'], $a['at']); });
		$rec['events'] = $ev;
		return $rec;
	}

	/* ---------- User Activity ---------- */

	/** Minutes each person was active, by day: user_id => date => sorted list of minute numbers. Read in a stream. */
	private function minutes($from, $to, $uids)
	{
		$out = array();
		if (!$uids) return $out;
		$this->ensure();
		$sql = "SELECT user_id, TIMESTAMPDIFF(MINUTE, '2020-01-01 00:00:00', minute) FROM ci_user_activity WHERE minute >= '" . $from . " 00:00:00' AND minute <= '" . $to . " 23:59:59' AND user_id IN (" . implode(',', array_map('intval', $uids)) . ") ORDER BY user_id, minute";
		$conn = $this->db->conn_id;
		$res = mysqli_query($conn, $sql, MYSQLI_USE_RESULT);
		if (!$res) return $out;
		while ($r = mysqli_fetch_row($res)) {
			$m = (int)$r[1];
			$out[(int)$r[0]][date('Y-m-d', $this->ts($m))][] = $m;
		}
		mysqli_free_result($res);
		return $out;
	}

	/** First / last minute, working time, idle periods for one list of active minutes. */
	public function day_stats($mins)
	{
		$s = array('first' => 0, 'last' => 0, 'active' => 0, 'work' => 0, 'idle' => 0, 'longest' => 0, 'gaps' => array());
		if (!$mins) return $s;
		$s['first'] = $mins[0];
		$s['last'] = end($mins);
		$s['active'] = count($mins);
		$prev = $mins[0];
		foreach ($mins as $m) {
			$gap = $m - $prev - 1; // minutes with nothing in between
			if ($gap >= self::IDLE_MIN) {
				$s['idle'] += $gap;
				$s['gaps'][] = array($prev + 1, $m - 1);
				if ($gap > $s['longest']) $s['longest'] = $gap;
			}
			$prev = $m;
		}
		$s['work'] = ($s['last'] - $s['first'] + 1) - $s['idle'];
		return $s;
	}

	/** One row per person for the date range. */
	public function activity($from, $to, $dept = 0, $q = '')
	{
		$this->ensure();
		$staff = $this->staff($dept, $q);
		$uids = array();
		foreach ($staff as $u) $uids[] = (int)$u['id'];
		if (!$uids) return array();
		$in = implode(',', $uids);
		$a = $this->db->escape($from . ' 00:00:00');
		$b = $this->db->escape($to . ' 23:59:59');
		$calls = array();
		foreach ($this->db->query("SELECT user_id, COUNT(*) n, SUM(disposition = 'Connected' OR duration > 0) ok, SUM(duration) talk FROM ci_call_logs WHERE created_at BETWEEN $a AND $b AND user_id IN ($in) AND IFNULL(is_internal, 0) = 0 GROUP BY user_id")->result_array() as $r) $calls[(int)$r['user_id']] = $r;
		$changes = array();
		foreach ($this->db->query("SELECT user_id, COUNT(*) n, COUNT(DISTINCT rtype, rid) recs FROM ci_record_changes WHERE created_at BETWEEN $a AND $b AND user_id IN ($in) GROUP BY user_id")->result_array() as $r) $changes[(int)$r['user_id']] = $r;
		$logins = array();
		foreach ($this->db->query("SELECT user_id, COUNT(*) n, MIN(created_at) first_at FROM ci_admin_logs WHERE activity = 'login' AND created_at BETWEEN $a AND $b AND user_id IN ($in) GROUP BY user_id")->result_array() as $r) $logins[(int)$r['user_id']] = $r;
		$pipe = array();
		foreach ($this->db->query("SELECT action_by, COUNT(*) n FROM ci_pipeline_logs WHERE created_at BETWEEN $a AND $b AND action_by IN ($in) GROUP BY action_by")->result_array() as $r) $pipe[(int)$r['action_by']] = (int)$r['n'];
		$last_ping = array();
		foreach ($this->db->query("SELECT user_id, MAX(minute) m FROM ci_user_activity WHERE minute >= ? AND user_id IN ($in) GROUP BY user_id", array(date('Y-m-d 00:00:00')))->result_array() as $r) $last_ping[(int)$r['user_id']] = strtotime($r['m']);

		$mins = $this->minutes($from, $to, $uids);
		$rows = array();
		foreach ($staff as $u) {
			$id = (int)$u['id'];
			$t = array('days' => 0, 'active' => 0, 'work' => 0, 'idle' => 0, 'longest' => 0, 'first' => 0, 'last' => 0);
			if (!empty($mins[$id])) {
				foreach ($mins[$id] as $d => $list) {
					$s = $this->day_stats($list);
					$t['days']++;
					$t['active'] += $s['active'];
					$t['work'] += $s['work'];
					$t['idle'] += $s['idle'];
					$t['longest'] = max($t['longest'], $s['longest']);
					if (!$t['first']) $t['first'] = $s['first'];
					$t['last'] = $s['last'];
				}
			}
			$now = '';
			if (isset($last_ping[$id])) {
				$ago = (int)floor((time() - $last_ping[$id]) / 60);
				$now = $ago <= 2 ? 'active' : ((int)$u['is_online'] === 1 ? 'idle:' . $ago : 'off');
			} elseif ((int)$u['is_online'] === 1) {
				$now = 'online';
			}
			$rows[] = array(
				'id' => $id, 'name' => $u['name'], 'dept' => $u['dept'], 'now' => $now, 'stats' => $t,
				'calls' => isset($calls[$id]) ? (int)$calls[$id]['n'] : 0, 'connected' => isset($calls[$id]) ? (int)$calls[$id]['ok'] : 0, 'talk' => isset($calls[$id]) ? (int)$calls[$id]['talk'] : 0,
				'changes' => isset($changes[$id]) ? (int)$changes[$id]['n'] : 0, 'records' => isset($changes[$id]) ? (int)$changes[$id]['recs'] : 0,
				'pipeline' => isset($pipe[$id]) ? $pipe[$id] : 0,
				'logins' => isset($logins[$id]) ? (int)$logins[$id]['n'] : 0, 'first_login' => isset($logins[$id]) ? $logins[$id]['first_at'] : '',
			);
		}
		return $rows;
	}

	/** One person, one day: hour by hour, idle periods and everything they did. */
	public function user_day($uid, $day)
	{
		$this->ensure();
		$uid = (int)$uid;
		$u = $this->db->select('U.id, U.name, U.email, U.is_online, R.title AS dept')->from('ci_user U')->join('ci_roles R', 'R.id = U.user_type', 'left')->where('U.id', $uid)->get()->row_array();
		if (!$u) return null;
		$mins = $this->minutes($day, $day, array($uid));
		$list = isset($mins[$uid][$day]) ? $mins[$uid][$day] : array();
		$stats = $this->day_stats($list);
		$hours = array_fill(0, 24, 0);
		foreach ($list as $m) $hours[(int)date('G', $this->ts($m))]++;
		$pages = array();
		foreach ($this->db->query("SELECT page, COUNT(*) n FROM ci_user_activity WHERE user_id = ? AND minute BETWEEN ? AND ? GROUP BY page ORDER BY n DESC LIMIT 8", array($uid, $day . ' 00:00:00', $day . ' 23:59:59'))->result_array() as $p) $pages[] = $p;

		$a = $day . ' 00:00:00';
		$b = $day . ' 23:59:59';
		$ev = array();
		foreach ($this->db->query("SELECT activity, details, created_at FROM ci_admin_logs WHERE user_id = ? AND created_at BETWEEN ? AND ? ORDER BY id LIMIT 300", array($uid, $a, $b))->result_array() as $l) {
			$act = $l['activity'];
			$title = array('login' => 'Signed in', 'logout' => 'Signed out', 'session_timeout' => 'Signed out (no activity)', 'concurrent_logout' => 'Signed out (signed in elsewhere)', 'login_failed' => 'Sign-in failed')[$act] ?? ucfirst(str_replace('_', ' ', $act));
			$ev[] = array('at' => $l['created_at'], 'icon' => in_array($act, array('login', 'logout', 'session_timeout', 'concurrent_logout'), true) ? 'fa-sign-in' : 'fa-history', 'title' => $title, 'text' => (string)$l['details'], 'link' => '');
		}
		foreach ($this->db->query("SELECT lead_id, customer_name, phone_number, disposition, duration, created_at, is_internal FROM ci_call_logs WHERE user_id = ? AND created_at BETWEEN ? AND ? ORDER BY id LIMIT 500", array($uid, $a, $b))->result_array() as $c) {
			$d = (int)$c['duration'];
			$ev[] = array('at' => $c['created_at'], 'icon' => 'fa-phone', 'title' => ((int)$c['is_internal'] === 1 ? 'Call to colleague' : 'Call') . ': ' . $c['customer_name'], 'text' => trim($c['phone_number'] . ' - ' . $c['disposition'] . ($d > 0 ? ' (' . gmdate($d >= 3600 ? 'H:i:s' : 'i:s', $d) . ')' : '')), 'link' => (int)$c['lead_id'] > 0 ? 'lead/' . (int)$c['lead_id'] : '');
		}
		$saves = $this->db->query("SELECT rtype, rid, user_id, created_at, MIN(id) AS first_id FROM ci_record_changes WHERE user_id = ? AND created_at BETWEEN ? AND ? GROUP BY rtype, rid, user_id, created_at ORDER BY created_at LIMIT 400", array($uid, $a, $b))->result_array();
		foreach ($this->fill_saves($saves) as $s) {
			$txt = array();
			foreach (array_slice($s['items'], 0, 4) as $it) $txt[] = $it[0] . ($it[2] !== '' ? ': ' . mb_strimwidth($it[2], 0, 60, '...') : '');
			$verb = $s['action'] === 'create' ? 'Created' : ($s['action'] === 'delete' ? 'Deleted' : 'Updated');
			$ev[] = array('at' => $s['at'], 'icon' => $s['action'] === 'delete' ? 'fa-trash' : 'fa-pencil', 'title' => $verb . ' ' . strtolower(self::$types[$s['rtype']][0]) . ': ' . ($s['name'] !== '' ? $s['name'] : '#' . $s['rid']), 'text' => implode(' · ', $txt) . (count($s['items']) > 4 ? ' · +' . (count($s['items']) - 4) . ' more' : ''), 'link' => $s['rtype'] . '/' . $s['rid']);
		}
		foreach ($this->db->query("SELECT P.lead_id, P.stage_from, P.stage_to, P.action, P.created_at, L.customer FROM ci_pipeline_logs P LEFT JOIN ci_leads L ON L.id = P.lead_id WHERE P.action_by = ? AND P.created_at BETWEEN ? AND ? ORDER BY P.id LIMIT 300", array($uid, $a, $b))->result_array() as $p) {
			$ev[] = array('at' => $p['created_at'], 'icon' => 'fa-random', 'title' => ucfirst(str_replace('_', ' ', $p['action'])) . ': ' . $p['customer'], 'text' => trim($p['stage_from'] . ' > ' . $p['stage_to'], ' >'), 'link' => 'lead/' . (int)$p['lead_id']);
		}
		foreach ($stats['gaps'] as $g) {
			$ev[] = array('at' => date('Y-m-d H:i:s', $this->ts($g[0])), 'icon' => 'fa-pause', 'title' => 'Idle for ' . $this->dur(($g[1] - $g[0] + 1) * 60), 'text' => 'No activity from ' . date('h:i A', $this->ts($g[0])) . ' to ' . date('h:i A', $this->ts($g[1] + 1)), 'link' => '', 'idle' => true);
		}
		usort($ev, function ($x, $y) { return strcmp($x['at'], $y['at']); });
		return array('user' => $u, 'stats' => $stats, 'hours' => $hours, 'events' => $ev, 'pages' => $pages);
	}

	/** Minute number (minutes since 2020-01-01, local time) to a timestamp. */
	public function ts($m)
	{
		static $epoch = null;
		if ($epoch === null) $epoch = strtotime('2020-01-01 00:00:00');
		return $epoch + (int)$m * 60;
	}

	public function dur($sec)
	{
		$sec = (int)$sec;
		if ($sec <= 0) return '0m';
		$h = intdiv($sec, 3600);
		$m = intdiv($sec % 3600, 60);
		return $h > 0 ? $h . 'h ' . str_pad($m, 2, '0', STR_PAD_LEFT) . 'm' : $m . 'm';
	}
}
