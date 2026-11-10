<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * HR link: the Sales CRM and HR & Payroll talk to each other inside the 7strokes suite.
 *
 * Trust: the portal app secret from Third-Party API > Subscription. Every message is signed:
 *   X-Suite-Timestamp (unix seconds, at most 5 minutes off) and
 *   X-Suite-Signature = "sha256=" + HMAC-SHA256 hex of "<timestamp>.<raw body>" (for a signed GET: "<timestamp>.GET <path?query>").
 * Company: only messages for this client's portal company (companyId from the portal's answer) are used.
 * Settings: ci_integration_settings, service 'hr_link': endpoint = HR address, status 1 = on.
 *
 * What HR may do here (and nothing more):
 *  - employee.created / directory: link to a CRM user with the same email, else wait for an admin to approve.
 *    Nobody gets a login without an admin; approved people start with the Sales role.
 *  - employee.updated: name, phone and job title of the linked user. Never role, team, manager or rights.
 *  - employee.exited: the linked user is blocked and signed out at once; leads and deals stay for an admin
 *    to reassign. Admin accounts are never switched off from HR, only flagged.
 *  - leave.approved: the rep is skipped in lead sharing and the auto-dialler on those days, shown "On leave".
 * The CRM sends HR only approved commissions (email, month, amount, order number).
 */
class Suite_link_model extends CI_Model
{
	const SERVICE = 'hr_link';
	const DEFAULT_HR = 'http://localhost:4300';
	const WINDOW = 300;

	private $ready = false;

	/** Tables (made on first use). */
	public function ensure()
	{
		if ($this->ready) return;
		$this->ready = true;
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_suite_inbox (delivery_id VARCHAR(100) NOT NULL PRIMARY KEY, event VARCHAR(60) NOT NULL DEFAULT '', result VARCHAR(255) NOT NULL DEFAULT '', received_at DATETIME NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_suite_staff (hr_id VARCHAR(64) NOT NULL PRIMARY KEY, emp_code VARCHAR(40) NOT NULL DEFAULT '', full_name VARCHAR(150) NOT NULL DEFAULT '', email VARCHAR(190) NOT NULL DEFAULT '', phone VARCHAR(40) NOT NULL DEFAULT '', designation VARCHAR(150) NOT NULL DEFAULT '', department VARCHAR(150) NOT NULL DEFAULT '', hr_status VARCHAR(30) NOT NULL DEFAULT '', join_date VARCHAR(20) NOT NULL DEFAULT '', looks_sales TINYINT(1) NOT NULL DEFAULT 0, state VARCHAR(20) NOT NULL DEFAULT 'pending', note VARCHAR(255) NOT NULL DEFAULT '', user_id INT NOT NULL DEFAULT 0, exit_date VARCHAR(20) NOT NULL DEFAULT '', exit_reason VARCHAR(120) NOT NULL DEFAULT '', open_records INT NOT NULL DEFAULT 0, updated_at DATETIME NOT NULL, KEY st (state), KEY uid (user_id), KEY em (email)) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_suite_leave (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL DEFAULT 0, email VARCHAR(190) NOT NULL DEFAULT '', full_name VARCHAR(150) NOT NULL DEFAULT '', leave_type VARCHAR(60) NOT NULL DEFAULT '', start_date DATE NOT NULL, end_date DATE NOT NULL, days DECIMAL(6,1) NOT NULL DEFAULT 0, received_at DATETIME NOT NULL, UNIQUE KEY one (email, start_date, end_date, leave_type), KEY ud (user_id, start_date, end_date)) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_suite_commission (order_id INT NOT NULL PRIMARY KEY, external_ref VARCHAR(60) NOT NULL, user_id INT NOT NULL, email VARCHAR(190) NOT NULL, month CHAR(7) NOT NULL, amount DECIMAL(12,2) NOT NULL, description VARCHAR(255) NOT NULL DEFAULT '', state VARCHAR(20) NOT NULL DEFAULT 'sent', hr_message VARCHAR(255) NOT NULL DEFAULT '', approved_by INT NOT NULL DEFAULT 0, approved_at DATETIME NULL, updated_at DATETIME NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_suite_log (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, at DATETIME NOT NULL, dir VARCHAR(3) NOT NULL DEFAULT 'in', event VARCHAR(60) NOT NULL DEFAULT '', ok TINYINT(1) NOT NULL DEFAULT 1, summary VARCHAR(500) NOT NULL DEFAULT '', KEY at (at)) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_user_exit (user_id INT NOT NULL PRIMARY KEY, left_on DATE NOT NULL, reason VARCHAR(60) NOT NULL DEFAULT '', note VARCHAR(500) NOT NULL DEFAULT '', marked_by INT NULL, marked_at DATETIME NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_user_history (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, field VARCHAR(60) NOT NULL, old_value VARCHAR(500) NOT NULL DEFAULT '', new_value VARCHAR(500) NOT NULL DEFAULT '', changed_by INT NULL, changed_at DATETIME NOT NULL, KEY user_when (user_id, changed_at)) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_user_designation (user_id INT NOT NULL PRIMARY KEY, designation VARCHAR(150) NOT NULL DEFAULT '') ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->data_cache = array();
	}

	/* ---------------- settings ---------------- */

	public function row()
	{
		$r = $this->db->where('service_name', self::SERVICE)->get('ci_integration_settings')->row_array();
		if (!$r) $r = array('id' => 0, 'endpoint' => '', 'status' => 0, 'config_json' => '{}');
		$c = json_decode((string)$r['config_json'], true);
		$r['config'] = is_array($c) ? $c : array();
		return $r;
	}

	public function is_on()
	{
		$r = $this->row();
		return (int)$r['status'] === 1;
	}

	public function hr_url()
	{
		$r = $this->row();
		$u = rtrim(trim((string)$r['endpoint']), '/');
		return $u !== '' ? $u : self::DEFAULT_HR;
	}

	/** The portal app secret and this client's portal company (from Subscription). */
	public function trust()
	{
		$this->load->model('subscription_model');
		$s = $this->subscription_model->row();
		$secret = $s ? trim((string)$s['api_secret']) : '';
		$last = ($s && isset($s['config']['last_result']) && is_array($s['config']['last_result'])) ? $s['config']['last_result'] : array();
		$cid = isset($last['companyId']) ? trim((string)$last['companyId']) : '';
		$name = isset($last['company']) ? (string)$last['company'] : '';
		return array('secret' => $secret, 'company_id' => $cid, 'company' => $name);
	}

	/** What is missing before the link can work ('' when nothing). */
	public function missing()
	{
		$t = $this->trust();
		if ($t['secret'] === '') return 'Set the portal app secret in Third-Party API > Subscription first.';
		if ($t['company_id'] === '') return 'The CRM does not know its portal company yet. Fill in Subscription and click "Check subscription now".';
		return '';
	}

	public function save_settings($status, $endpoint)
	{
		$endpoint = rtrim(trim((string)$endpoint), '/');
		if ($endpoint !== '' && !preg_match('~^https?://[^\s/]+(:\d+)?(/[^\s]*)?$~i', $endpoint)) return 'The HR address must start with http:// or https://.';
		$r = $this->row();
		$row = array('endpoint' => $endpoint, 'status' => $status ? 1 : 0, 'updated_at' => date('Y-m-d H:i:s'));
		if ((int)$r['id'] > 0) $this->db->where('id', (int)$r['id'])->update('ci_integration_settings', $row);
		else $this->db->insert('ci_integration_settings', $row + array('service_name' => self::SERVICE, 'api_key' => '', 'api_secret' => '', 'config_json' => '{}', 'created_at' => date('Y-m-d H:i:s')));
		$this->log('out', 'settings', true, 'HR link switched ' . ($status ? 'on' : 'off') . ', HR address ' . ($endpoint !== '' ? $endpoint : self::DEFAULT_HR));
		return '';
	}

	/* ---------------- signing ---------------- */

	public function sign($message, $secret)
	{
		return 'sha256=' . hash_hmac('sha256', $message, $secret);
	}

	/** Checks timestamp and signature. Returns '' when good, else the reason. */
	public function verify($raw, $ts, $sig)
	{
		$t = $this->trust();
		if ($t['secret'] === '') return 'not set up';
		$ts = (int)$ts;
		if ($ts <= 0 || abs(time() - $ts) > self::WINDOW) return 'old or missing timestamp';
		if (!is_string($sig) || !hash_equals($this->sign($ts . '.' . $raw, $t['secret']), trim($sig))) return 'bad signature';
		return '';
	}

	/* ---------------- incoming (HR -> CRM) ---------------- */

	/** POST api/suite/app-events. Returns array(http code, message). */
	public function handle($raw, $ts, $sig, $delivery)
	{
		$bad = $this->verify($raw, $ts, $sig);
		if ($bad === 'not set up') return array(404, 'The HR link is not set up.');
		if ($bad !== '') { $this->log('in', '?', false, 'Refused: ' . $bad); return array(401, ucfirst($bad) . '.'); }
		$this->ensure();

		$e = json_decode($raw, true);
		if (!is_array($e) || empty($e['event']) || !is_string($e['event'])) return array(400, 'No event.');
		$event = substr($e['event'], 0, 60);
		$d = isset($e['data']) && is_array($e['data']) ? $e['data'] : array();

		$t = $this->trust();
		$cid = isset($e['company']['id']) ? trim((string)$e['company']['id']) : '';
		if ($t['company_id'] === '' || $cid === '' || !hash_equals($t['company_id'], $cid)) return array(200, 'Other company, ignored.');

		$delivery = trim((string)$delivery);
		if ($delivery === '' && !empty($e['id'])) $delivery = (string)$e['id'];
		$delivery = substr($delivery, 0, 100);
		// Recorded only after it is handled, so a delivery that failed half-way is done again on HR's retry
		if ($delivery !== '' && $this->db->where('delivery_id', $delivery)->count_all_results('ci_suite_inbox') > 0) return array(200, 'Already handled.');

		if ($event === 'suite.ping') $res = 'pong';
		elseif (!$this->is_on()) $res = 'The HR link is off in the CRM, ignored.';
		elseif (!isset($e['source']) || $e['source'] !== 'hr') $res = 'Only HR events are used, ignored.';
		elseif ($event === 'employee.created') $res = $this->employee_seen($d, true);
		elseif ($event === 'employee.updated') $res = $this->employee_updated($d);
		elseif ($event === 'employee.exited') $res = $this->employee_exited($d);
		elseif ($event === 'leave.approved') $res = $this->leave_approved($d);
		else $res = 'Not used by the CRM, ignored.';

		if ($delivery !== '') $this->db->query('INSERT IGNORE INTO ci_suite_inbox (delivery_id, event, result, received_at) VALUES (?, ?, ?, ?)', array($delivery, $event, substr($res, 0, 255), date('Y-m-d H:i:s')));
		if ($event !== 'suite.ping') $this->log('in', $event, true, $res);
		return array(200, $res);
	}

	private function s($d, $k, $max = 150)
	{
		return isset($d[$k]) && is_scalar($d[$k]) ? mb_substr(trim(preg_replace('/\s+/', ' ', (string)$d[$k])), 0, $max) : '';
	}

	private function email_of($d)
	{
		$m = strtolower($this->s($d, 'email', 190));
		return filter_var($m, FILTER_VALIDATE_EMAIL) ? $m : '';
	}

	private function name_of($d)
	{
		$n = $this->s($d, 'full_name');
		if ($n === '') $n = trim($this->s($d, 'first_name', 70) . ' ' . $this->s($d, 'last_name', 70));
		return $n;
	}

	public static function looks_sales($designation, $department)
	{
		return (bool)preg_match('~sales|business\s*dev|\bbd[em]?\b|telesales|tele.?sales|account\s*(exec|manager)|\bkam\b|key\s*account|relationship\s*manager|pre.?sales~i', $designation . ' | ' . $department);
	}

	/** CRM users with this email (login or email field). */
	private function users_by_email($email)
	{
		if ($email === '') return array();
		return $this->db->query('SELECT id, name, user_type FROM ci_user WHERE status = 1 AND (LOWER(TRIM(email)) = ? OR LOWER(TRIM(c_username)) = ?)', array($email, $email))->result_array();
	}

	private function staff($hr_id)
	{
		return $hr_id === '' ? array() : (array)$this->db->where('hr_id', $hr_id)->get('ci_suite_staff')->row_array();
	}

	/** employee.created and every row of the directory: store, and link by email when there is exactly one match. */
	public function employee_seen($d, $from_event = false)
	{
		$hr_id = $this->s($d, 'id', 64);
		if ($hr_id === '') return 'No employee id, ignored.';
		$email = $this->email_of($d);
		$name = $this->name_of($d);
		$row = array(
			'hr_id' => $hr_id, 'emp_code' => $this->s($d, 'emp_code', 40), 'full_name' => $name, 'email' => $email,
			'phone' => $this->s($d, 'phone', 40), 'designation' => $this->s($d, 'designation'), 'department' => $this->s($d, 'department'),
			'hr_status' => $this->s($d, 'status', 30), 'join_date' => $this->s($d, 'join_date', 20), 'updated_at' => date('Y-m-d H:i:s'),
		);
		$row['looks_sales'] = self::looks_sales($row['designation'], $row['department']) ? 1 : 0;
		$old = $this->staff($hr_id);
		if ($old && in_array($old['state'], array('linked', 'exited', 'dismissed'), true)) {
			$this->db->where('hr_id', $hr_id)->update('ci_suite_staff', $row);
			return $name . ': already known (' . $old['state'] . ').';
		}
		$match = $this->users_by_email($email);
		$taken = count($match) === 1 && $this->db->where('user_id', (int)$match[0]['id'])->where('hr_id <>', $hr_id)->where_in('state', array('linked', 'exited'))->count_all_results('ci_suite_staff') > 0;
		if (count($match) === 1 && !$taken) {
			$row['state'] = 'linked'; $row['user_id'] = (int)$match[0]['id']; $row['note'] = '';
			$this->db->replace('ci_suite_staff', $row);
			$this->history((int)$match[0]['id'], 'HR link', '', 'Linked to HR' . ($row['emp_code'] !== '' ? ' (' . $row['emp_code'] . ')' : ''));
			return $name . ': linked to CRM user #' . (int)$match[0]['id'] . ' by email. Rights not changed.';
		}
		$row['state'] = 'pending'; $row['user_id'] = 0;
		$row['note'] = $taken ? 'A CRM user with this email is already linked to another HR record; check in HR.' : (count($match) > 1 ? 'More than one CRM user has this email; link by hand.' : ($email === '' ? 'No email from HR.' : ''));
		$this->db->replace('ci_suite_staff', $row);
		return $name . ': waiting for an admin to approve' . ($row['looks_sales'] ? ' (looks like sales)' : '') . '.';
	}

	/** employee.updated: only name, phone and job title of a linked, working user. */
	public function employee_updated($d)
	{
		$hr_id = $this->s($d, 'id', 64);
		$old = $this->staff($hr_id);
		if (!$old) return $this->employee_seen($d);
		if ($old['state'] !== 'linked') return $this->employee_seen($d);
		$this->employee_seen($d); // refresh the stored HR details
		$uid = (int)$old['user_id'];
		$u = $this->db->select('id, name, mobile, country_code')->where('id', $uid)->get('ci_user')->row_array();
		if (!$u) return 'Linked CRM user not found.';
		$done = array();
		$name = $this->name_of($d);
		if ($name !== '' && $name !== trim((string)$u['name'])) {
			$this->db->where('id', $uid)->update('ci_user', array('name' => $name));
			$this->history($uid, 'Name', $u['name'], $name . ' (from HR)'); $done[] = 'name';
		}
		$phone = $this->s($d, 'phone', 40);
		$digits = function ($s) { return preg_replace('/\D+/', '', (string)$s); };
		$have = $digits($u['country_code'] . $u['mobile']);
		$want = $digits($phone);
		if ($want !== '' && strlen($want) >= 7 && $want !== $have && substr($have, -strlen($want)) !== $want && substr($want, -strlen($digits($u['mobile']))) !== $digits($u['mobile'])) {
			$this->db->where('id', $uid)->update('ci_user', array('mobile' => $phone, 'country_code' => ''));
			$this->history($uid, 'Contact No', trim($u['country_code'] . ' ' . $u['mobile']), $phone . ' (from HR)'); $done[] = 'phone';
		}
		$title = $this->s($d, 'designation');
		if ($title !== '') {
			$cur = $this->db->select('designation')->where('user_id', $uid)->get('ci_user_designation')->row_array();
			$cur = $cur ? (string)$cur['designation'] : '';
			if ($cur !== $title) {
				$this->db->query('REPLACE INTO ci_user_designation (user_id, designation) VALUES (?, ?)', array($uid, $title));
				$this->history($uid, 'Designation', $cur, $title . ' (from HR)'); $done[] = 'job title';
			}
		}
		return ($name !== '' ? $name : 'User #' . $uid) . ': ' . ($done ? 'updated ' . implode(', ', $done) : 'nothing to change') . '. Role, team and rights are only changed in the CRM.';
	}

	/** employee.exited: block and sign out the linked user (not admins), keep their records for reassignment. */
	public function employee_exited($d)
	{
		$hr_id = $this->s($d, 'id', 64);
		$old = $this->staff($hr_id);
		if (!$old || $old['state'] === 'pending' || $old['state'] === 'dismissed') {
			$this->employee_seen($d);
			$old = $this->staff($hr_id);
		}
		$on = strtotime($this->s($d, 'exit_date', 20)); $on = $on ? date('Y-m-d', $on) : date('Y-m-d');
		$reason = $this->s($d, 'exit_reason', 120);
		$name = $this->name_of($d);
		$upd = array('exit_date' => $on, 'exit_reason' => $reason, 'updated_at' => date('Y-m-d H:i:s'));
		if (!$old || $old['state'] !== 'linked' && $old['state'] !== 'exited' || (int)$old['user_id'] <= 0) {
			$this->db->where('hr_id', $hr_id)->update('ci_suite_staff', $upd + array('state' => 'dismissed', 'note' => 'Left in HR; never had a CRM login.'));
			return $name . ': left in HR, no CRM user to block.';
		}
		$uid = (int)$old['user_id'];
		if (!isset($this->ci_admin_model)) $this->load->model('ci_admin_model');
		if ($this->is_admin_account($uid)) {
			$this->db->where('hr_id', $hr_id)->update('ci_suite_staff', $upd + array('state' => 'linked', 'note' => 'Admin account: left in HR on ' . $on . ' but not switched off. An admin must do it.'));
			return $name . ': admin account, not switched off from HR. Flagged for an admin.';
		}
		$u = $this->db->select('id, name, admin_status, c_dol')->where('id', $uid)->get('ci_user')->row_array();
		if (!$u) return 'Linked CRM user not found.';
		$map = array('resign' => 'Resigned', 'terminat' => 'Terminated', 'contract' => 'Contract ended', 'abscond' => 'Absconded', 'retire' => 'Retired');
		$why = 'Other';
		foreach ($map as $k => $v) if (stripos($reason, $k) !== false) { $why = $v; break; }
		$this->db->replace('ci_user_exit', array('user_id' => $uid, 'left_on' => $on, 'reason' => $why, 'note' => mb_substr('From HR' . ($reason !== '' ? ': ' . $reason : ''), 0, 500), 'marked_by' => null, 'marked_at' => date('Y-m-d H:i:s')));
		$this->db->where('id', $uid)->update('ci_user', array('admin_status' => 0, 'c_dol' => $on, 'is_online' => 0, 'current_session_token' => null));
		$this->history($uid, 'Status', (int)$u['admin_status'] === 1 ? 'Enabled' : 'Disabled', 'Left on ' . date('d-M-Y', strtotime($on)) . ' (' . $why . ', from HR)');
		$owned = $this->ci_admin_model->get_user_owned_records_count($uid);
		$n = isset($owned['total']) ? (int)$owned['total'] : 0;
		$this->db->where('hr_id', $hr_id)->update('ci_suite_staff', $upd + array('state' => 'exited', 'open_records' => $n, 'note' => $n > 0 ? 'Owns ' . $n . ' record(s) to reassign.' : ''));
		return $u['name'] . ': left on ' . $on . '. Sign-in blocked and signed out' . ($n > 0 ? '; ' . $n . ' record(s) wait to be reassigned.' : '.');
	}

	/** Admin account (Admin role, user #1, or admin permission mode); works without a signed-in session. */
	private function is_admin_account($uid)
	{
		$uid = (int)$uid;
		if ($uid === 1) return true;
		$u = $this->db->select('user_type')->where('id', $uid)->get('ci_user')->row_array();
		if ($u && (int)$u['user_type'] === 1) return true;
		if (!isset($this->ci_admin_model)) $this->load->model('ci_admin_model');
		$pd = $this->ci_admin_model->get_user_permission_data($uid);
		return is_array($pd) && isset($pd['mode']) && $pd['mode'] === 'admin';
	}

	/** leave.approved: the rep is unavailable on those days. */
	public function leave_approved($d)
	{
		$emp = isset($d['employee']) && is_array($d['employee']) ? $d['employee'] : array();
		$email = $this->email_of($emp);
		$name = $this->name_of($emp);
		$from = strtotime($this->s($d, 'start_date', 20)); $to = strtotime($this->s($d, 'end_date', 20));
		if (!$from || !$to || $to < $from || $to - $from > 366 * 86400) return 'Leave dates not valid, ignored.';
		$uid = 0;
		if (!empty($emp['id'])) { $st = $this->staff($this->s($emp, 'id', 64)); if ($st && $st['state'] === 'linked') $uid = (int)$st['user_id']; }
		if (!$uid) { $m = $this->users_by_email($email); if (count($m) === 1) $uid = (int)$m[0]['id']; }
		$type = $this->s($d, 'leave_type', 60);
		$this->db->query('INSERT INTO ci_suite_leave (user_id, email, full_name, leave_type, start_date, end_date, days, received_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE user_id = VALUES(user_id), full_name = VALUES(full_name), days = VALUES(days), received_at = VALUES(received_at)',
			array($uid, $email, $name, $type, date('Y-m-d', $from), date('Y-m-d', $to), (float)(isset($d['days']) ? $d['days'] : 0), date('Y-m-d H:i:s')));
		return ($name !== '' ? $name : $email) . ': ' . ($type !== '' ? $type . ' ' : '') . 'leave ' . date('d M', $from) . ' to ' . date('d M Y', $to) . ($uid ? '. Skipped in lead sharing and the auto-dialler on those days.' : '. No CRM user with this email.');
	}

	/* ---------------- admin actions ---------------- */

	/** Approve someone from HR: a new login with the Sales role under the chosen manager. */
	public function approve($hr_id, $manager_id, $by)
	{
		$this->ensure();
		$st = $this->staff($hr_id);
		if (!$st || $st['state'] !== 'pending') return array(false, 'This person is not waiting for approval.');
		if ($st['email'] === '') return array(false, 'HR has no email for this person, so a login cannot be made.');
		if ($this->users_by_email($st['email'])) return array(false, 'A CRM user with this email already exists. Use Load staff from HR to link them.');
		if ($this->db->where('c_username', $st['email'])->count_all_results('ci_user') > 0) return array(false, 'This email is already used as a login.');
		$role = $this->db->select('id')->where('title', 'Sales')->where('status', 1)->get('ci_roles')->row_array();
		if (!$role) return array(false, 'The Sales role was not found.');
		$mgr = $this->db->select('id, user_level')->where('id', (int)$manager_id)->where('status', 1)->where('admin_status', 1)->get('ci_user')->row_array();
		if (!$mgr) return array(false, 'Choose an active manager.');
		$row = array(
			'name' => $st['full_name'] !== '' ? $st['full_name'] : $st['email'], 'email' => $st['email'], 'mobile' => $st['phone'], 'country_code' => '',
			'c_username' => $st['email'], 'c_password' => sha1(bin2hex(random_bytes(24))), 'emp_id' => $st['emp_code'],
			'department' => $st['department'], 'c_doj' => $st['join_date'], 'status' => 1, 'admin_status' => 1,
			'address' => '', 'country_code2' => '', 'c_email' => '', 'c_mobile' => '', 'c_salary' => '', 'c_dol' => '', 'national' => '', 'p_no' => '', 'p_date' => '', 'p_exdate' => '',
			'user_type' => (int)$role['id'], 'manager' => (int)$mgr['id'], 'user_level' => (int)$mgr['user_level'] + 1, 'created' => date('Y-m-d'),
		);
		$this->db->insert('ci_user', $row);
		$uid = (int)$this->db->insert_id();
		if ($uid <= 0) return array(false, 'The user could not be saved.');
		if ($st['designation'] !== '') $this->db->query('REPLACE INTO ci_user_designation (user_id, designation) VALUES (?, ?)', array($uid, $st['designation']));
		$this->db->where('hr_id', $hr_id)->update('ci_suite_staff', array('state' => 'linked', 'user_id' => $uid, 'note' => '', 'updated_at' => date('Y-m-d H:i:s')));
		$this->history($uid, 'Profile', '', 'Added from HR, approved by an admin (Sales role)', $by);
		$this->log('in', 'approve', true, $row['name'] . ': approved by user #' . (int)$by . ', CRM user #' . $uid . ' with the Sales role.');
		return array(true, $row['name'] . ' can now sign in with ' . $st['email'] . ' as login. They set their password with "Forgot Password?" on the sign-in page, or an admin sets it on their profile.');
	}

	public function dismiss($hr_id, $by)
	{
		$this->ensure();
		$st = $this->staff($hr_id);
		if (!$st || $st['state'] !== 'pending') return array(false, 'This person is not waiting for approval.');
		$this->db->where('hr_id', $hr_id)->update('ci_suite_staff', array('state' => 'dismissed', 'note' => 'No CRM login needed (admin #' . (int)$by . ').', 'updated_at' => date('Y-m-d H:i:s')));
		$this->log('in', 'dismiss', true, $st['full_name'] . ': no CRM login needed (admin #' . (int)$by . ').');
		return array(true, $st['full_name'] . ' is removed from the waiting list.');
	}

	/* ---------------- outgoing (CRM -> HR) ---------------- */

	/** Signed POST to HR's app-events. Returns array(ok, http code, decoded answer, error text). */
	public function send($event, $data)
	{
		$miss = $this->missing();
		if ($miss !== '') return array(false, 0, null, $miss);
		if (!$this->is_on()) return array(false, 0, null, 'The HR link is off.');
		$t = $this->trust();
		$id = $this->uuid();
		$body = json_encode(array('id' => $id, 'event' => $event, 'occurred_at' => gmdate('Y-m-d\TH:i:s\Z'), 'source' => 'crm',
			'company' => array('id' => $t['company_id'], 'name' => $t['company']), 'data' => (object)$data), JSON_UNESCAPED_SLASHES);
		$ts = time();
		return $this->http('POST', $this->hr_url() . '/api/suite/app-events', $body, array(
			'Content-Type: application/json', 'X-Suite-Timestamp: ' . $ts, 'X-Suite-Signature: ' . $this->sign($ts . '.' . $body, $t['secret']),
			'X-Suite-Delivery: ' . $id, 'X-Suite-Event: ' . $event,
		));
	}

	/** Signed GET from HR (message "<ts>.GET <path?query>"). */
	public function get($path_query)
	{
		$miss = $this->missing();
		if ($miss !== '') return array(false, 0, null, $miss);
		$t = $this->trust();
		$ts = time();
		return $this->http('GET', $this->hr_url() . $path_query, null, array(
			'Accept: application/json', 'X-Suite-Company: ' . $t['company_id'], 'X-Suite-Timestamp: ' . $ts, 'X-Suite-Signature: ' . $this->sign($ts . '.GET ' . $path_query, $t['secret']),
		));
	}

	private function http($method, $url, $body, $headers)
	{
		$ch = curl_init($url);
		$opt = array(CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10, CURLOPT_CONNECTTIMEOUT => 5, CURLOPT_HTTPHEADER => $headers, CURLOPT_FOLLOWLOCATION => false);
		if ($method === 'POST') { $opt[CURLOPT_POST] = true; $opt[CURLOPT_POSTFIELDS] = $body; }
		curl_setopt_array($ch, $opt);
		$res = curl_exec($ch);
		$code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$err = curl_error($ch);
		curl_close($ch);
		$j = is_string($res) ? json_decode($res, true) : null;
		if ($code >= 200 && $code < 300) return array(true, $code, $j, '');
		$msg = is_array($j) && !empty($j['error']) && is_string($j['error']) ? $j['error'] : ($err !== '' ? $err : 'answer ' . $code);
		return array(false, $code, $j, 'HR could not be reached or refused (' . mb_substr($msg, 0, 150) . ').');
	}

	public function ping()
	{
		$r = $this->send('suite.ping', array());
		$this->log('out', 'suite.ping', $r[0], $r[0] ? 'HR answered.' : $r[3]);
		return $r;
	}

	/** First sync: HR's whole active staff list, linked by email or put on the waiting list. */
	public function load_directory()
	{
		$this->ensure();
		$r = $this->get('/api/suite/directory?status=active');
		if (!$r[0]) { $this->log('out', 'directory', false, $r[3]); return array(false, $r[3]); }
		$j = $r[2];
		$list = is_array($j) && isset($j[0]) ? $j : (is_array($j) ? (isset($j['employees']) ? $j['employees'] : (isset($j['items']) ? $j['items'] : (isset($j['data']) ? $j['data'] : array()))) : array());
		if (!is_array($list)) $list = array();
		$c = array('linked' => 0, 'pending' => 0, 'known' => 0);
		foreach (array_slice($list, 0, 5000) as $emp) {
			if (!is_array($emp)) continue;
			$res = $this->employee_seen($emp);
			if (strpos($res, 'linked to CRM user') !== false) $c['linked']++;
			elseif (strpos($res, 'waiting') !== false) $c['pending']++;
			else $c['known']++;
		}
		$msg = count($list) . ' people from HR: ' . $c['linked'] . ' linked by email, ' . $c['pending'] . ' waiting for approval, ' . $c['known'] . ' already known.';
		$this->log('out', 'directory', true, $msg);
		return array(true, $msg);
	}

	/* ---------------- commissions ---------------- */

	public function commission($order_id)
	{
		$this->ensure();
		return (array)$this->db->where('order_id', (int)$order_id)->get('ci_suite_commission')->row_array();
	}

	/** Approve a commission on an order and send it to HR payroll (re-sending updates the same line). */
	public function send_commission($order, $user_id, $month, $amount, $by)
	{
		$this->ensure();
		$oid = (int)$order['id'];
		if (strtolower((string)$order['approval_status']) !== 'approved' || in_array($order['status'], array('Cancelled', 'Rejected', 'Lost'), true)) return array(false, 'Only an approved order that is not cancelled can have a commission.');
		$amount = round((float)$amount, 2);
		if ($amount <= 0 || $amount > 1000000) return array(false, 'Enter an amount above 0.');
		if ((float)$order['pricing_amount'] > 0 && $amount > (float)$order['pricing_amount']) return array(false, 'The commission cannot be more than the order amount (' . number_format((float)$order['pricing_amount'], 2) . ').');
		if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', (string)$month)) return array(false, 'Choose the payroll month.');
		$mt = strtotime($month . '-01');
		if ($mt < strtotime(date('Y-m-01') . ' -12 months') || $mt > strtotime(date('Y-m-01') . ' +3 months')) return array(false, 'The payroll month must be within the last 12 or next 3 months.');
		$u = $this->db->select('id, name, email, c_username, admin_status')->where('id', (int)$user_id)->where('status', 1)->get('ci_user')->row_array();
		if (!$u) return array(false, 'Choose the salesperson.');
		$email = strtolower(trim((string)$u['email']));
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $email = strtolower(trim((string)$u['c_username']));
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return array(false, $u['name'] . ' has no email in the CRM, so HR cannot find them.');
		$ref = 'crm-order-' . $oid;
		$desc = 'Commission order #' . $order['order_no'];
		$r = $this->send('payroll.adjustment.upsert', array('items' => array(array('external_ref' => $ref, 'employee' => array('email' => $email), 'month' => $month, 'kind' => 'addition', 'amount' => $amount, 'description' => $desc))));
		$ok = $r[0]; $msg = $r[3];
		if ($ok) {
			$line = is_array($r[2]) && isset($r[2]['results'][0]) && is_array($r[2]['results'][0]) ? $r[2]['results'][0] : null;
			if ($line && empty($line['ok'])) { $ok = false; $msg = 'HR did not take it: ' . (!empty($line['error']) && is_string($line['error']) ? mb_substr($line['error'], 0, 150) : 'no reason given') . '.'; }
			elseif (!$line) { $ok = false; $msg = 'HR gave no answer for this line.'; }
		}
		$this->db->replace('ci_suite_commission', array('order_id' => $oid, 'external_ref' => $ref, 'user_id' => (int)$u['id'], 'email' => $email, 'month' => $month, 'amount' => $amount, 'description' => $desc,
			'state' => $ok ? 'sent' : 'failed', 'hr_message' => $ok ? 'In HR payroll for ' . $month . '.' : mb_substr($msg, 0, 255), 'approved_by' => (int)$by, 'approved_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')));
		$this->log('out', 'payroll.adjustment.upsert', $ok, 'Order #' . $order['order_no'] . ': ' . number_format($amount, 2) . ' for ' . $u['name'] . ' (' . $month . ') ' . ($ok ? 'sent to HR.' : '- ' . $msg));
		$this->order_note($oid, $ok ? 'Commission ' . number_format($amount, 2) . ' for ' . $u['name'] . ' (' . $month . ') approved and sent to HR payroll.' : 'Commission could not be sent to HR: ' . $msg, $by);
		return array($ok, $ok ? 'Commission sent to HR payroll for ' . $month . '.' : $msg);
	}

	/** Take a commission back out of HR payroll (only while that month is a draft in HR). */
	public function withdraw_commission($order_id, $by, $why = '')
	{
		$c = $this->commission($order_id);
		if (!$c || !in_array($c['state'], array('sent', 'withdraw_failed'), true)) return array(true, '');
		$r = $this->send('payroll.adjustment.delete', array('external_ref' => $c['external_ref']));
		$ok = $r[0];
		$msg = $ok ? 'Withdrawn from HR payroll' . ($why !== '' ? ' (' . $why . ')' : '') . '.' : 'Could not withdraw from HR: ' . $r[3] . ' Ask HR to remove it by hand if that month is already final.';
		$this->db->where('order_id', (int)$order_id)->update('ci_suite_commission', array('state' => $ok ? 'withdrawn' : 'withdraw_failed', 'hr_message' => mb_substr($msg, 0, 255), 'updated_at' => date('Y-m-d H:i:s')));
		$this->log('out', 'payroll.adjustment.delete', $ok, $c['external_ref'] . ': ' . $msg);
		$this->order_note((int)$order_id, 'Commission ' . $msg, $by);
		return array($ok, $msg);
	}

	private function order_note($order_id, $text, $by)
	{
		if (!$this->db->table_exists('ci_order_history')) return;
		$f = $this->db->list_fields('ci_order_history');
		$row = array();
		if (in_array('order_id', $f, true)) $row['order_id'] = (int)$order_id;
		if (in_array('action', $f, true)) $row['action'] = 'hr_commission';
		if (in_array('notes', $f, true)) $row['notes'] = mb_substr($text, 0, 1000);
		if (in_array('action_by', $f, true)) $row['action_by'] = (int)$by;
		if (in_array('action_by_name', $f, true)) { $n = (int)$by ? $this->db->select('name')->where('id', (int)$by)->get('ci_user')->row_array() : null; $row['action_by_name'] = $n ? $n['name'] : 'HR link'; }
		if (in_array('created_at', $f, true)) $row['created_at'] = date('Y-m-d H:i:s');
		if (isset($row['order_id'])) $this->db->insert('ci_order_history', $row);
	}

	/* ---------------- leave and leavers (used across the CRM) ---------------- */

	/** CRM user ids on approved HR leave on $day (Y-m-d). */
	public function on_leave_ids($day = null)
	{
		$this->ensure();
		$day = $day ?: date('Y-m-d');
		return array_map('intval', array_column($this->db->query('SELECT DISTINCT user_id FROM ci_suite_leave WHERE user_id > 0 AND start_date <= ? AND end_date >= ?', array($day, $day))->result_array(), 'user_id'));
	}

	/** True when HR says this user left and they have not rejoined in the CRM. */
	public function blocked($uid)
	{
		$this->ensure();
		return $this->db->query("SELECT 1 FROM ci_suite_staff S JOIN ci_user_exit X ON X.user_id = S.user_id WHERE S.user_id = ? AND S.state = 'exited' LIMIT 1", array((int)$uid))->num_rows() > 0;
	}

	/* ---------------- lists for the page ---------------- */

	public function lists()
	{
		$this->ensure();
		$today = date('Y-m-d');
		return array(
			'pending' => $this->db->query("SELECT * FROM ci_suite_staff WHERE state = 'pending' ORDER BY looks_sales DESC, full_name")->result_array(),
			'exited' => $this->db->query("SELECT S.*, U.name AS crm_name, U.admin_status FROM ci_suite_staff S LEFT JOIN ci_user U ON U.id = S.user_id WHERE S.state = 'exited' OR (S.state = 'linked' AND S.exit_date <> '') ORDER BY S.updated_at DESC LIMIT 100")->result_array(),
			'leave' => $this->db->query('SELECT L.*, U.name AS crm_name FROM ci_suite_leave L LEFT JOIN ci_user U ON U.id = L.user_id WHERE L.end_date >= ? ORDER BY L.start_date LIMIT 100', array($today))->result_array(),
			'linked' => (int)$this->db->query("SELECT COUNT(*) n FROM ci_suite_staff WHERE state = 'linked'")->row()->n,
			'log' => $this->db->query('SELECT * FROM ci_suite_log ORDER BY id DESC LIMIT 60')->result_array(),
			'commissions' => $this->db->query('SELECT C.*, U.name AS rep, O.order_no FROM ci_suite_commission C LEFT JOIN ci_user U ON U.id = C.user_id LEFT JOIN ci_orders O ON O.id = C.order_id ORDER BY C.updated_at DESC LIMIT 50')->result_array(),
		);
	}

	/* ---------------- helpers ---------------- */

	public function log($dir, $event, $ok, $summary)
	{
		$this->ensure();
		$this->db->insert('ci_suite_log', array('at' => date('Y-m-d H:i:s'), 'dir' => $dir, 'event' => mb_substr((string)$event, 0, 60), 'ok' => $ok ? 1 : 0, 'summary' => mb_substr((string)$summary, 0, 500)));
	}

	private function history($uid, $field, $old, $new, $by = null)
	{
		$this->db->insert('ci_user_history', array('user_id' => (int)$uid, 'field' => mb_substr($field, 0, 60), 'old_value' => mb_substr((string)$old, 0, 500), 'new_value' => mb_substr((string)$new, 0, 500),
			'changed_by' => $by ? (int)$by : null, 'changed_at' => date('Y-m-d H:i:s')));
	}

	private function uuid()
	{
		$b = random_bytes(16);
		$b[6] = chr(ord($b[6]) & 0x0f | 0x40);
		$b[8] = chr(ord($b[8]) & 0x3f | 0x80);
		return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($b), 4));
	}
}
