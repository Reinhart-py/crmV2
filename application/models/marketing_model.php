<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Marketing (Admin, System > Marketing): Meta (Facebook / Instagram) connection, lead intake from
 * Meta lead ads, Google Ads lead forms and website forms, page posts, Meta Ads control, and the
 * Claude writing helper. Keys live only in ci_integration_settings (services 'meta' and 'anthropic').
 */
class Marketing_model extends CI_Model
{
	private $schema_checked = false;
	private $cache = array();
	private $page_token = null;

	public static $claude_models = array(
		'claude-opus-5-5' => 'Claude Opus 5.5 (best)',
		'claude-sonnet-5-5' => 'Claude Sonnet 5.5 (faster, lower cost)',
		'claude-haiku-5-5' => 'Claude Haiku 5.5 (lowest cost)'
	);
	public static $ctas = array('SIGN_UP' => 'Sign up', 'GET_QUOTE' => 'Get quote', 'LEARN_MORE' => 'Learn more', 'APPLY_NOW' => 'Apply now', 'CONTACT_US' => 'Contact us', 'SUBSCRIBE' => 'Subscribe');
	public static $countries = array('AE' => 'United Arab Emirates', 'SA' => 'Saudi Arabia', 'QA' => 'Qatar', 'KW' => 'Kuwait', 'BH' => 'Bahrain', 'OM' => 'Oman', 'IN' => 'India', 'GB' => 'United Kingdom');

	public function ensure_schema()
	{
		if ($this->schema_checked) return;
		$this->schema_checked = true;
		if (!$this->db->table_exists('ci_mkt_leads')) {
			$this->db->query("CREATE TABLE `ci_mkt_leads` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`source` VARCHAR(20) NOT NULL DEFAULT 'meta',
				`external_id` VARCHAR(64) NOT NULL DEFAULT '',
				`form_id` VARCHAR(64) NOT NULL DEFAULT '',
				`form_name` VARCHAR(150) NOT NULL DEFAULT '',
				`ad_id` VARCHAR(64) NOT NULL DEFAULT '',
				`ad_name` VARCHAR(255) NOT NULL DEFAULT '',
				`campaign_name` VARCHAR(255) NOT NULL DEFAULT '',
				`platform` VARCHAR(20) NOT NULL DEFAULT '',
				`full_name` VARCHAR(150) NOT NULL DEFAULT '',
				`phone` VARCHAR(40) NOT NULL DEFAULT '',
				`email` VARCHAR(150) NOT NULL DEFAULT '',
				`company` VARCHAR(255) NOT NULL DEFAULT '',
				`city` VARCHAR(100) NOT NULL DEFAULT '',
				`fields_json` TEXT NULL,
				`lead_row_id` INT(11) NOT NULL DEFAULT 0,
				`assigned_to` INT(11) NOT NULL DEFAULT 0,
				`status` VARCHAR(12) NOT NULL DEFAULT 'New',
				`error` VARCHAR(255) NOT NULL DEFAULT '',
				`received_at` DATETIME NOT NULL,
				PRIMARY KEY (`id`),
				KEY `idx_mkt_ext` (`source`, `external_id`),
				KEY `idx_mkt_received` (`received_at`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		}
		if (!$this->db->table_exists('ci_mkt_posts')) {
			$this->db->query("CREATE TABLE `ci_mkt_posts` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`channels` VARCHAR(10) NOT NULL DEFAULT 'fb',
				`message` TEXT NULL,
				`image_url` VARCHAR(500) NOT NULL DEFAULT '',
				`link` VARCHAR(500) NOT NULL DEFAULT '',
				`status` VARCHAR(12) NOT NULL DEFAULT 'Draft',
				`scheduled_at` DATETIME NULL,
				`posted_at` DATETIME NULL,
				`fb_post_id` VARCHAR(80) NOT NULL DEFAULT '',
				`ig_media_id` VARCHAR(80) NOT NULL DEFAULT '',
				`error` VARCHAR(255) NOT NULL DEFAULT '',
				`created_by` INT(11) NOT NULL DEFAULT 0,
				`created_at` DATETIME NOT NULL,
				PRIMARY KEY (`id`),
				KEY `idx_mkt_post_status` (`status`, `scheduled_at`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		}
		if (!$this->db->table_exists('ci_mkt_log')) {
			$this->db->query("CREATE TABLE `ci_mkt_log` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`action` VARCHAR(40) NOT NULL,
				`detail` VARCHAR(500) NOT NULL DEFAULT '',
				`user_id` INT(11) NOT NULL DEFAULT 0,
				`created_at` DATETIME NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		}
	}

	public function log($action, $detail, $uid = 0)
	{
		$this->db->insert('ci_mkt_log', array('action' => substr($action, 0, 40), 'detail' => substr($detail, 0, 500), 'user_id' => (int)$uid, 'created_at' => date('Y-m-d H:i:s')));
	}

	public function recent_log($n = 30)
	{
		return $this->db->query("SELECT L.*, U.name FROM ci_mkt_log L LEFT JOIN ci_user U ON U.id = L.user_id ORDER BY L.id DESC LIMIT " . (int)$n)->result_array();
	}

	/* ---------- settings ---------- */

	private function row($service)
	{
		return $this->db->where('service_name', $service)->get('ci_integration_settings')->row_array();
	}

	public function meta()
	{
		if (isset($this->cache['meta'])) return $this->cache['meta'];
		$row = $this->row('meta');
		$cfg = !empty($row['config_json']) ? json_decode($row['config_json'], true) : array();
		if (!is_array($cfg)) $cfg = array();
		$s = array_merge(array(
			'app_id' => '', 'page_id' => '', 'page_name' => '', 'ig_id' => '', 'ig_name' => '', 'ad_account_id' => '', 'graph_version' => 'v23.0',
			'verify_token' => '', 'webhook_key' => '', 'route' => 'fixed', 'owner_id' => 0, 'rr_users' => array(), 'rr_next' => 0,
			'website' => '', 'privacy_url' => '', 'auto_pause' => 0, 'max_cpl' => 0, 'currency' => 'AED', 'last_sync' => ''
		), $cfg);
		$s['token'] = !empty($row['api_key']) ? $row['api_key'] : '';
		$s['app_secret'] = !empty($row['api_secret']) ? $row['api_secret'] : '';
		$s['enabled'] = !empty($row['status']);
		$s['exists'] = !empty($row);
		return $this->cache['meta'] = $s;
	}

	/** Writes config keys (and optionally token / secret) for 'meta' without touching the rest. */
	public function save_meta($cfg_changes, $token = '', $secret = '', $enabled = null)
	{
		$old = $this->meta();
		$keep = array_diff_key($old, array_flip(array('token', 'app_secret', 'enabled', 'exists')));
		$cfg = array_merge($keep, $cfg_changes);
		if ($cfg['verify_token'] === '') $cfg['verify_token'] = bin2hex(random_bytes(12));
		if ($cfg['webhook_key'] === '') $cfg['webhook_key'] = bin2hex(random_bytes(16));
		$row = array('config_json' => json_encode($cfg), 'updated_at' => date('Y-m-d H:i:s'));
		if ($token !== '') $row['api_key'] = $token;
		if ($secret !== '') $row['api_secret'] = $secret;
		if ($enabled !== null) $row['status'] = $enabled ? 1 : 0;
		if ($old['exists']) {
			$this->db->where('service_name', 'meta')->update('ci_integration_settings', $row);
		} else {
			$row += array('service_name' => 'meta', 'api_key' => '', 'api_secret' => '', 'endpoint' => '', 'status' => 1, 'created_at' => $row['updated_at']);
			$this->db->insert('ci_integration_settings', $row);
		}
		unset($this->cache['meta']);
		$this->page_token = null;
	}

	public function webhook_key()
	{
		$m = $this->meta();
		if ($m['webhook_key'] === '' || $m['verify_token'] === '') { $this->save_meta(array()); $m = $this->meta(); }
		return $m['webhook_key'];
	}

	public function renew_keys()
	{
		$this->save_meta(array('webhook_key' => bin2hex(random_bytes(16)), 'verify_token' => bin2hex(random_bytes(12))));
	}

	public function claude()
	{
		if (isset($this->cache['claude'])) return $this->cache['claude'];
		$row = $this->row('anthropic');
		$cfg = !empty($row['config_json']) ? json_decode($row['config_json'], true) : array();
		if (!is_array($cfg)) $cfg = array();
		$s = array_merge(array('model' => 'claude-opus-5-5', 'brand' => '', 'tone' => 'Professional and friendly'), $cfg);
		if (!isset(self::$claude_models[$s['model']])) $s['model'] = 'claude-opus-5-5';
		$s['api_key'] = !empty($row['api_key']) ? $row['api_key'] : '';
		$s['exists'] = !empty($row);
		return $this->cache['claude'] = $s;
	}

	public function save_claude($cfg, $key = '')
	{
		$old = $this->claude();
		$row = array('config_json' => json_encode(array(
			'model' => isset(self::$claude_models[$cfg['model']]) ? $cfg['model'] : 'claude-opus-5-5',
			'brand' => substr(trim($cfg['brand']), 0, 1500),
			'tone' => substr(trim($cfg['tone']), 0, 100)
		)), 'updated_at' => date('Y-m-d H:i:s'), 'status' => 1);
		if ($key !== '') $row['api_key'] = $key;
		if ($old['exists']) {
			$this->db->where('service_name', 'anthropic')->update('ci_integration_settings', $row);
		} else {
			$row += array('service_name' => 'anthropic', 'api_key' => '', 'api_secret' => '', 'endpoint' => 'https://api.anthropic.com/v1/messages', 'created_at' => $row['updated_at']);
			$this->db->insert('ci_integration_settings', $row);
		}
		unset($this->cache['claude']);
	}

	/* ---------- HTTP ---------- */

	private function ca_opts()
	{
		$ini = ini_get('curl.cainfo');
		if ($ini && is_file($ini)) return array();
		foreach (array(dirname(PHP_BINARY) . '/../apache/bin/curl-ca-bundle.crt', 'C:/xampp/apache/bin/curl-ca-bundle.crt', '/etc/ssl/certs/ca-certificates.crt', '/etc/pki/tls/certs/ca-bundle.crt') as $p) {
			if (is_file($p)) return array(CURLOPT_CAINFO => $p);
		}
		return array();
	}

	private function curl($url, $method, $headers, $body, $timeout = 25)
	{
		$ch = curl_init($url);
		curl_setopt_array($ch, $this->ca_opts());
		curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => $timeout, CURLOPT_CONNECTTIMEOUT => 8, CURLOPT_HTTPHEADER => $headers, CURLOPT_CUSTOMREQUEST => $method));
		if ($body !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		$raw = curl_exec($ch);
		$code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$err = curl_error($ch);
		curl_close($ch);
		return array($code, $raw === false ? '' : $raw, $err);
	}

	/**
	 * Graph API call. Returns array(ok, data, error message).
	 * $token: 'page' uses the page token, otherwise the saved (user / system user) token.
	 */
	public function graph($method, $path, $params = array(), $token = 'user')
	{
		$m = $this->meta();
		if ($m['token'] === '') return array(false, null, 'Meta is not connected yet.');
		$tok = $token === 'page' ? $this->page_token() : $m['token'];
		$params['access_token'] = $tok;
		if ($m['app_secret'] !== '') $params['appsecret_proof'] = hash_hmac('sha256', $tok, $m['app_secret']);
		foreach ($params as $k => $v) if (is_array($v)) $params[$k] = json_encode($v);
		$url = 'https://graph.facebook.com/' . $m['graph_version'] . '/' . ltrim($path, '/');
		if ($method === 'GET' || $method === 'DELETE') {
			list($code, $raw, $err) = $this->curl($url . '?' . http_build_query($params), $method, array(), null);
		} else {
			list($code, $raw, $err) = $this->curl($url, $method, array('Content-Type: application/x-www-form-urlencoded'), http_build_query($params), 60);
		}
		$res = json_decode($raw, true);
		if ($err !== '') return array(false, null, 'Could not reach Meta: ' . $err);
		if (!is_array($res)) return array(false, null, 'Meta sent an empty answer (HTTP ' . $code . ').');
		if (isset($res['error'])) {
			$e = $res['error'];
			$msg = !empty($e['error_user_msg']) ? $e['error_user_msg'] : (isset($e['message']) ? $e['message'] : 'Meta error');
			return array(false, $res, 'Meta: ' . $msg);
		}
		return array(true, $res, '');
	}

	/** Page access token from the saved token (a page token works as-is). */
	public function page_token()
	{
		if ($this->page_token !== null) return $this->page_token;
		$m = $this->meta();
		$this->page_token = $m['token'];
		if ($m['page_id'] !== '' && $m['token'] !== '') {
			$q = array('fields' => 'access_token', 'access_token' => $m['token']);
			if ($m['app_secret'] !== '') $q['appsecret_proof'] = hash_hmac('sha256', $m['token'], $m['app_secret']);
			list($code, $raw) = $this->curl('https://graph.facebook.com/' . $m['graph_version'] . '/' . rawurlencode($m['page_id']) . '?' . http_build_query($q), 'GET', array(), null);
			$r = json_decode($raw, true);
			if (!empty($r['access_token'])) $this->page_token = $r['access_token'];
		}
		return $this->page_token;
	}

	/** What the token can reach: pages (with linked Instagram) and ad accounts. */
	public function discover()
	{
		list($ok, $me, $err) = $this->graph('GET', 'me', array('fields' => 'id,name'));
		if (!$ok) return array(false, $err);
		$out = array('me' => $me['name'] ?? '', 'pages' => array(), 'ad_accounts' => array());
		list($ok, $p) = $this->graph('GET', 'me/accounts', array('fields' => 'id,name,instagram_business_account{id,username}', 'limit' => 50));
		if ($ok && !empty($p['data'])) foreach ($p['data'] as $x) {
			$out['pages'][] = array('id' => $x['id'], 'name' => $x['name'], 'ig_id' => $x['instagram_business_account']['id'] ?? '', 'ig_name' => $x['instagram_business_account']['username'] ?? '');
		}
		list($ok, $a) = $this->graph('GET', 'me/adaccounts', array('fields' => 'account_id,name,currency,account_status', 'limit' => 50));
		if ($ok && !empty($a['data'])) foreach ($a['data'] as $x) {
			$out['ad_accounts'][] = array('id' => $x['account_id'], 'name' => $x['name'], 'currency' => $x['currency'] ?? 'AED', 'active' => (int)($x['account_status'] ?? 0) === 1);
		}
		return array(true, $out);
	}

	/** Lets the page send new leads to this app's webhook. */
	public function subscribe_page()
	{
		$m = $this->meta();
		if ($m['page_id'] === '') return array(false, 'Choose the Facebook page first.');
		list($ok, $r, $err) = $this->graph('POST', $m['page_id'] . '/subscribed_apps', array('subscribed_fields' => 'leadgen'), 'page');
		return $ok ? array(true, 'The page now sends new leads to the CRM.') : array(false, $err);
	}

	/* ---------- leads ---------- */

	private function digits($p) { return preg_replace('/\D/', '', (string)$p); }

	/** Maps Meta field_data to name / phone / email / company / city plus the rest. */
	public function map_fields($field_data)
	{
		$f = array('full_name' => '', 'phone' => '', 'email' => '', 'company' => '', 'city' => '', 'other' => array());
		$first = $last = '';
		foreach ((array)$field_data as $x) {
			$k = strtolower((string)($x['name'] ?? ''));
			$v = trim((string)(is_array($x['values'] ?? null) ? implode(', ', $x['values']) : ($x['values'] ?? '')));
			if ($v === '') continue;
			if (in_array($k, array('full_name', 'name'), true)) $f['full_name'] = $v;
			elseif ($k === 'first_name') $first = $v;
			elseif ($k === 'last_name') $last = $v;
			elseif (in_array($k, array('phone_number', 'phone', 'mobile', 'work_phone_number'), true)) { if ($f['phone'] === '') $f['phone'] = $v; else $f['other'][$k] = $v; }
			elseif (in_array($k, array('email', 'work_email'), true)) { if ($f['email'] === '') $f['email'] = $v; else $f['other'][$k] = $v; }
			elseif (in_array($k, array('company_name', 'company'), true)) $f['company'] = $v;
			elseif ($k === 'city') $f['city'] = $v;
			else $f['other'][$k] = $v;
		}
		if ($f['full_name'] === '') $f['full_name'] = trim($first . ' ' . $last);
		return $f;
	}

	/** Next owner for a new lead (fixed person or round robin). */
	private function next_owner()
	{
		$m = $this->meta();
		$users = array_values(array_filter(array_map('intval', (array)$m['rr_users'])));
		if ($m['route'] === 'round' && $users) {
			$active = array_map('intval', array_column($this->db->select('id')->where('status', 1)->where_in('id', $users)->get('ci_user')->result_array(), 'id'));
			$users = array_values(array_intersect($users, $active));
			if ($users) {
				$i = ((int)$m['rr_next']) % count($users);
				$this->save_meta(array('rr_next' => $i + 1));
				return $users[$i];
			}
		}
		return (int)$m['owner_id'];
	}

	/**
	 * Stores one incoming lead and creates the CRM record unless the phone or e-mail is already in the CRM.
	 * $in: source, external_id, form_id, form_name, ad_id, ad_name, campaign_name, platform, full_name, phone, email, company, city, other (array)
	 * Returns array(status, mkt_lead_id).
	 */
	public function intake($in)
	{
		$in = array_merge(array('source' => 'web', 'external_id' => '', 'form_id' => '', 'form_name' => '', 'ad_id' => '', 'ad_name' => '', 'campaign_name' => '', 'platform' => '', 'full_name' => '', 'phone' => '', 'email' => '', 'company' => '', 'city' => '', 'other' => array()), $in);
		if ($in['external_id'] !== '') {
			$dup = $this->db->select('id, status')->where('source', $in['source'])->where('external_id', $in['external_id'])->get('ci_mkt_leads')->row_array();
			if ($dup && $dup['status'] !== 'Error') return array('Seen', (int)$dup['id']);
			if ($dup) $this->db->where('id', (int)$dup['id'])->delete('ci_mkt_leads'); // failed before: try again
		}
		$row = array(
			'source' => substr($in['source'], 0, 20), 'external_id' => substr($in['external_id'], 0, 64), 'form_id' => substr($in['form_id'], 0, 64),
			'form_name' => substr($in['form_name'], 0, 150), 'ad_id' => substr($in['ad_id'], 0, 64), 'ad_name' => substr($in['ad_name'], 0, 255),
			'campaign_name' => substr($in['campaign_name'], 0, 255), 'platform' => substr($in['platform'], 0, 20), 'full_name' => substr(trim($in['full_name']), 0, 150),
			'phone' => substr(trim($in['phone']), 0, 40), 'email' => substr(trim($in['email']), 0, 150), 'company' => substr(trim($in['company']), 0, 255),
			'city' => substr(trim($in['city']), 0, 100), 'fields_json' => json_encode($in['other'], JSON_UNESCAPED_UNICODE), 'received_at' => date('Y-m-d H:i:s')
		);
		if ($row['phone'] === '' && $row['email'] === '') {
			$row['status'] = 'Error';
			$row['error'] = 'No phone or e-mail in the form.';
			$this->db->insert('ci_mkt_leads', $row);
			return array('Error', (int)$this->db->insert_id());
		}
		// Already in the CRM? Match the last 9 digits of the phone, or the e-mail.
		$existing = null;
		$d = substr($this->digits($row['phone']), -9);
		if (strlen($d) >= 7) {
			$like = $this->db->escape_like_str($d);
			$existing = $this->db->query("SELECT id, lead_assign FROM ci_leads WHERE REPLACE(REPLACE(REPLACE(contact1,' ',''),'-',''),'+','') LIKE '%{$like}' OR REPLACE(REPLACE(REPLACE(mobile,' ',''),'-',''),'+','') LIKE '%{$like}' ORDER BY id DESC LIMIT 1")->row_array();
		}
		if (!$existing && $row['email'] !== '') {
			$existing = $this->db->select('id, lead_assign')->where('email', $row['email'])->order_by('id', 'DESC')->limit(1)->get('ci_leads')->row_array();
		}
		if ($existing) {
			$row['status'] = 'Duplicate';
			$row['lead_row_id'] = (int)$existing['id'];
			$row['assigned_to'] = (int)$existing['lead_assign'];
			$this->db->insert('ci_mkt_leads', $row);
			return array('Duplicate', (int)$this->db->insert_id());
		}
		$owner = $this->next_owner();
		$labels = array('meta' => 'Meta Lead Ads', 'google' => 'Google Ads', 'web' => 'Website', 'manual' => 'Marketing');
		$note = array();
		if ($row['campaign_name'] !== '') $note[] = 'Campaign: ' . $row['campaign_name'];
		if ($row['ad_name'] !== '') $note[] = 'Ad: ' . $row['ad_name'];
		if ($row['form_name'] !== '') $note[] = 'Form: ' . $row['form_name'];
		foreach ($in['other'] as $k => $v) $note[] = ucfirst(str_replace('_', ' ', $k)) . ': ' . (is_array($v) ? implode(', ', $v) : $v);
		$lead = array(
			'customer' => $row['company'] !== '' ? $row['company'] : ($row['full_name'] !== '' ? $row['full_name'] : $row['phone']),
			'poc' => $row['full_name'], 'contact1' => $row['phone'], 'mobile' => $row['phone'], 'email' => $row['email'], 'city' => $row['city'],
			'status' => 36, 'lead_assign' => $owner, 'lead_source' => $labels[$row['source']] ?? 'Marketing', 'data_source' => $labels[$row['source']] ?? 'Marketing',
			'created_by' => $owner, 'admin_status' => 1, 'created' => date('Y-m-d'), 'c_partner' => 'Seven Strokes', 'country' => 0, 'state' => 0
		);
		if ($this->db->field_exists('remark', 'ci_leads')) $lead['remark'] = substr(implode("\n", $note), 0, 2000);
		$this->db->insert('ci_leads', $lead);
		$lid = (int)$this->db->insert_id();
		if ($lid > 0) {
			$this->db->where('id', $lid)->update('ci_leads', array('lead_id' => (string)(100000000 + $lid)));
			$row['status'] = 'Created';
			$row['lead_row_id'] = $lid;
			$row['assigned_to'] = $owner;
		} else {
			$row['status'] = 'Error';
			$row['error'] = 'The CRM record could not be saved.';
		}
		$this->db->insert('ci_mkt_leads', $row);
		return array($row['status'], (int)$this->db->insert_id());
	}

	/** Reads one Meta lead by its leadgen id and takes it in. */
	public function intake_meta_lead($leadgen_id, $hint = array())
	{
		list($ok, $L, $err) = $this->graph('GET', rawurlencode($leadgen_id), array('fields' => 'created_time,field_data,ad_id,ad_name,campaign_name,form_id,platform'), 'page');
		if (!$ok) {
			// keep a trace; the next sync (or webhook retry) takes it in
			$dup = $this->db->select('id')->where('source', 'meta')->where('external_id', (string)$leadgen_id)->get('ci_mkt_leads')->row_array();
			if (!$dup) $this->db->insert('ci_mkt_leads', array('source' => 'meta', 'external_id' => substr((string)$leadgen_id, 0, 64), 'form_id' => substr((string)($hint['form_id'] ?? ''), 0, 64), 'status' => 'Error', 'error' => substr('Could not read the lead from Meta. ' . $err, 0, 255), 'received_at' => date('Y-m-d H:i:s')));
			return array('Error', $err);
		}
		$f = $this->map_fields($L['field_data'] ?? array());
		return $this->intake(array(
			'source' => 'meta', 'external_id' => (string)$leadgen_id, 'form_id' => (string)($L['form_id'] ?? ($hint['form_id'] ?? '')),
			'form_name' => (string)($hint['form_name'] ?? ''), 'ad_id' => (string)($L['ad_id'] ?? ''), 'ad_name' => (string)($L['ad_name'] ?? ''),
			'campaign_name' => (string)($L['campaign_name'] ?? ''), 'platform' => (string)($L['platform'] ?? ''),
			'full_name' => $f['full_name'], 'phone' => $f['phone'], 'email' => $f['email'], 'company' => $f['company'], 'city' => $f['city'], 'other' => $f['other']
		));
	}

	/** Lead forms on the page. */
	public function forms()
	{
		$m = $this->meta();
		if ($m['page_id'] === '') return array(false, array(), 'Choose the Facebook page first.');
		list($ok, $r, $err) = $this->graph('GET', $m['page_id'] . '/leadgen_forms', array('fields' => 'id,name,status,leads_count,created_time', 'limit' => 100), 'page');
		return array($ok, $ok ? ($r['data'] ?? array()) : array(), $err);
	}

	/** Pulls the newest leads of every active form (backup for the webhook). Returns array(new, message). */
	public function sync_leads()
	{
		list($ok, $forms, $err) = $this->forms();
		if (!$ok) return array(0, $err);
		$new = 0;
		$m = $this->meta();
		$since = $m['last_sync'] !== '' ? strtotime($m['last_sync']) - 3600 : strtotime('-30 days');
		foreach ($forms as $fm) {
			if (($fm['status'] ?? '') === 'ARCHIVED') continue;
			list($ok, $r) = $this->graph('GET', $fm['id'] . '/leads', array(
				'fields' => 'id,created_time,field_data,ad_id,ad_name,campaign_name,platform', 'limit' => 100,
				'filtering' => array(array('field' => 'time_created', 'operator' => 'GREATER_THAN', 'value' => $since))
			), 'page');
			if (!$ok) continue;
			foreach ($r['data'] ?? array() as $L) {
				$f = $this->map_fields($L['field_data'] ?? array());
				list($st) = $this->intake(array(
					'source' => 'meta', 'external_id' => (string)$L['id'], 'form_id' => (string)$fm['id'], 'form_name' => (string)$fm['name'],
					'ad_id' => (string)($L['ad_id'] ?? ''), 'ad_name' => (string)($L['ad_name'] ?? ''), 'campaign_name' => (string)($L['campaign_name'] ?? ''),
					'platform' => (string)($L['platform'] ?? ''), 'full_name' => $f['full_name'], 'phone' => $f['phone'], 'email' => $f['email'],
					'company' => $f['company'], 'city' => $f['city'], 'other' => $f['other']
				));
				if ($st !== 'Seen') $new++;
			}
		}
		$this->save_meta(array('last_sync' => date('Y-m-d H:i:s')));
		return array($new, $new ? $new . ' new lead' . ($new === 1 ? '' : 's') . ' taken in.' : 'No new leads.');
	}

	/** Google Ads lead form webhook body (user_column_data) to intake fields. */
	public function map_google($p)
	{
		$f = array('full_name' => '', 'phone' => '', 'email' => '', 'company' => '', 'city' => '', 'other' => array());
		$first = $last = '';
		foreach ((array)($p['user_column_data'] ?? array()) as $c) {
			$id = strtoupper((string)($c['column_id'] ?? ''));
			$v = trim((string)($c['string_value'] ?? ''));
			if ($v === '') continue;
			if ($id === 'FULL_NAME') $f['full_name'] = $v;
			elseif ($id === 'FIRST_NAME') $first = $v;
			elseif ($id === 'LAST_NAME') $last = $v;
			elseif ($id === 'PHONE_NUMBER') $f['phone'] = $v;
			elseif ($id === 'EMAIL') $f['email'] = $v;
			elseif ($id === 'COMPANY_NAME') $f['company'] = $v;
			elseif ($id === 'CITY') $f['city'] = $v;
			else $f['other'][strtolower($c['column_name'] ?? $id)] = $v;
		}
		if ($f['full_name'] === '') $f['full_name'] = trim($first . ' ' . $last);
		return $f + array('source' => 'google', 'external_id' => (string)($p['lead_id'] ?? ''), 'form_id' => (string)($p['form_id'] ?? ''), 'campaign_name' => (string)($p['campaign_id'] ?? ''), 'platform' => 'google');
	}

	public function leads($where = '1=1', $limit = 200)
	{
		return $this->db->query("SELECT M.*, U.name AS owner_name, L.lead_id AS ref FROM ci_mkt_leads M LEFT JOIN ci_user U ON U.id = M.assigned_to LEFT JOIN ci_leads L ON L.id = M.lead_row_id WHERE {$where} ORDER BY M.id DESC LIMIT " . (int)$limit)->result_array();
	}

	public function lead_stats()
	{
		$m0 = date('Y-m-01 00:00:00');
		$d0 = date('Y-m-d 00:00:00');
		return $this->db->query("SELECT COUNT(*) AS total,
			SUM(received_at >= '{$m0}') AS month,
			SUM(received_at >= '{$d0}') AS today,
			SUM(status = 'Created' AND received_at >= '{$m0}') AS created,
			SUM(status = 'Duplicate' AND received_at >= '{$m0}') AS dup,
			SUM(source = 'meta' AND received_at >= '{$m0}') AS meta
			FROM ci_mkt_leads")->row_array();
	}

	/* ---------- posts ---------- */

	public function posts($limit = 100)
	{
		return $this->db->query("SELECT P.*, U.name AS by_name FROM ci_mkt_posts P LEFT JOIN ci_user U ON U.id = P.created_by ORDER BY (P.status = 'Scheduled') DESC, COALESCE(P.posted_at, P.scheduled_at, P.created_at) DESC LIMIT " . (int)$limit)->result_array();
	}

	/** Publishes one post row now. Returns array(ok, message). */
	public function publish($p)
	{
		$m = $this->meta();
		$ch = explode(',', $p['channels']);
		$errs = array();
		$upd = array();
		if (in_array('fb', $ch, true) && $p['fb_post_id'] === '') {
			if ($m['page_id'] === '') $errs[] = 'No Facebook page chosen.';
			else {
				if ($p['image_url'] !== '') {
					list($ok, $r, $err) = $this->graph('POST', $m['page_id'] . '/photos', array('url' => $p['image_url'], 'caption' => trim($p['message'] . ($p['link'] !== '' ? "\n\n" . $p['link'] : ''))), 'page');
				} else {
					$q = array('message' => $p['message']);
					if ($p['link'] !== '') $q['link'] = $p['link'];
					list($ok, $r, $err) = $this->graph('POST', $m['page_id'] . '/feed', $q, 'page');
				}
				if ($ok) $upd['fb_post_id'] = (string)($r['post_id'] ?? ($r['id'] ?? ''));
				else $errs[] = 'Facebook: ' . preg_replace('/^Meta: /', '', $err);
			}
		}
		if (in_array('ig', $ch, true) && $p['ig_media_id'] === '') {
			if ($m['ig_id'] === '') $errs[] = 'No Instagram account linked to the page.';
			elseif ($p['image_url'] === '') $errs[] = 'Instagram needs an image.';
			else {
				list($ok, $r, $err) = $this->graph('POST', $m['ig_id'] . '/media', array('image_url' => $p['image_url'], 'caption' => $p['message']), 'page');
				if ($ok && !empty($r['id'])) {
					$cid = $r['id'];
					$ok2 = false;
					for ($i = 0; $i < 5; $i++) {
						list($ok2, $r2, $err) = $this->graph('POST', $m['ig_id'] . '/media_publish', array('creation_id' => $cid), 'page');
						if ($ok2) break;
						sleep(2);
					}
					if ($ok2) $upd['ig_media_id'] = (string)$r2['id'];
					else $errs[] = 'Instagram: ' . preg_replace('/^Meta: /', '', $err);
				} else $errs[] = 'Instagram: ' . preg_replace('/^Meta: /', '', $err);
			}
		}
		$fb_done = !in_array('fb', $ch, true) || !empty($upd['fb_post_id']) || $p['fb_post_id'] !== '';
		$ig_done = !in_array('ig', $ch, true) || !empty($upd['ig_media_id']) || $p['ig_media_id'] !== '';
		$upd['status'] = ($fb_done && $ig_done) ? 'Posted' : 'Failed';
		$upd['error'] = substr(implode(' ', $errs), 0, 255);
		if ($upd['status'] === 'Posted') $upd['posted_at'] = date('Y-m-d H:i:s');
		$this->db->where('id', (int)$p['id'])->update('ci_mkt_posts', $upd);
		return array($upd['status'] === 'Posted', $upd['status'] === 'Posted' ? 'Posted.' : ($upd['error'] ?: 'Not posted.'));
	}

	public function publish_due()
	{
		$n = 0;
		foreach ($this->db->where('status', 'Scheduled')->where('scheduled_at <=', date('Y-m-d H:i:s'))->limit(10)->get('ci_mkt_posts')->result_array() as $p) {
			// claim the row so two cron runs never post twice
			$this->db->where('id', $p['id'])->where('status', 'Scheduled')->update('ci_mkt_posts', array('status' => 'Posting'));
			if ($this->db->affected_rows() !== 1) continue;
			$this->publish($p);
			$n++;
		}
		return $n;
	}

	/* ---------- Meta Ads ---------- */

	public function act()
	{
		$a = preg_replace('/\D/', '', $this->meta()['ad_account_id']);
		return $a !== '' ? 'act_' . $a : '';
	}

	private function lead_count($ins)
	{
		$n = 0;
		foreach ((array)($ins['actions'] ?? array()) as $a) {
			if (in_array($a['action_type'], array('lead', 'onsite_conversion.lead_grouped', 'leadgen_grouped'), true)) $n = max($n, (int)$a['value']);
		}
		return $n;
	}

	/** Campaigns with the last 7 days' results. */
	public function campaigns($preset = 'last_7d')
	{
		$act = $this->act();
		if ($act === '') return array(false, array(), 'Choose the ad account first.');
		$preset = in_array($preset, array('today', 'yesterday', 'last_7d', 'last_30d', 'this_month'), true) ? $preset : 'last_7d';
		list($ok, $r, $err) = $this->graph('GET', $act . '/campaigns', array(
			'fields' => "id,name,status,effective_status,objective,daily_budget,lifetime_budget,created_time,insights.date_preset({$preset}){spend,impressions,clicks,ctr,actions}",
			'limit' => 100
		));
		if (!$ok) return array(false, array(), $err);
		$out = array();
		foreach ($r['data'] ?? array() as $c) {
			$ins = $c['insights']['data'][0] ?? array();
			$spend = (float)($ins['spend'] ?? 0);
			$leads = $this->lead_count($ins);
			$out[] = array(
				'id' => $c['id'], 'name' => $c['name'], 'status' => $c['status'], 'effective' => $c['effective_status'] ?? $c['status'], 'objective' => $c['objective'] ?? '',
				'daily_budget' => isset($c['daily_budget']) ? (int)$c['daily_budget'] / 100 : null, 'lifetime_budget' => isset($c['lifetime_budget']) ? (int)$c['lifetime_budget'] / 100 : null,
				'spend' => $spend, 'impressions' => (int)($ins['impressions'] ?? 0), 'clicks' => (int)($ins['clicks'] ?? 0), 'ctr' => (float)($ins['ctr'] ?? 0),
				'leads' => $leads, 'cpl' => $leads > 0 ? round($spend / $leads, 2) : null
			);
		}
		usort($out, function ($a, $b) { $o = array('ACTIVE' => 0, 'PAUSED' => 1); return (($o[$a['status']] ?? 2) <=> ($o[$b['status']] ?? 2)) ?: ($b['spend'] <=> $a['spend']); });
		return array(true, $out, '');
	}

	public function adsets($campaign_id)
	{
		list($ok, $r, $err) = $this->graph('GET', rawurlencode($campaign_id) . '/adsets', array(
			'fields' => 'id,name,status,daily_budget,lifetime_budget,targeting{geo_locations,age_min,age_max},insights.date_preset(last_7d){spend,actions}', 'limit' => 50
		));
		if (!$ok) return array(false, array(), $err);
		$out = array();
		foreach ($r['data'] ?? array() as $s) {
			$ins = $s['insights']['data'][0] ?? array();
			$leads = $this->lead_count($ins);
			$spend = (float)($ins['spend'] ?? 0);
			$out[] = array('id' => $s['id'], 'name' => $s['name'], 'status' => $s['status'], 'daily_budget' => isset($s['daily_budget']) ? (int)$s['daily_budget'] / 100 : null,
				'spend' => $spend, 'leads' => $leads, 'cpl' => $leads ? round($spend / $leads, 2) : null,
				'countries' => implode(', ', (array)($s['targeting']['geo_locations']['countries'] ?? array())), 'ages' => ($s['targeting']['age_min'] ?? '') . '–' . ($s['targeting']['age_max'] ?? ''));
		}
		return array(true, $out, '');
	}

	/** Pause or run a campaign, ad set or ad. */
	public function set_status($object_id, $status)
	{
		if (!in_array($status, array('ACTIVE', 'PAUSED'), true)) return array(false, 'Unknown status.');
		list($ok, $r, $err) = $this->graph('POST', rawurlencode($object_id), array('status' => $status));
		return $ok ? array(true, $status === 'ACTIVE' ? 'Running.' : 'Paused.') : array(false, $err);
	}

	/** Daily budget in account currency (whole units). */
	public function set_budget($object_id, $amount)
	{
		$minor = (int)round((float)$amount * 100);
		if ($minor < 100) return array(false, 'The daily budget is too low.');
		list($ok, $r, $err) = $this->graph('POST', rawurlencode($object_id), array('daily_budget' => $minor));
		return $ok ? array(true, 'Budget saved.') : array(false, $err);
	}

	/** Uploads an image (from a URL) to the ad account. Returns array(ok, hash or message). */
	private function ad_image($url)
	{
		list($code, $bytes, $err) = $this->curl($url, 'GET', array(), null, 30);
		if ($code !== 200 || $bytes === '' || strlen($bytes) > 8 * 1024 * 1024) return array(false, 'The image could not be downloaded (use a public https link to a JPG or PNG under 8 MB).');
		if (!@getimagesizefromstring($bytes)) return array(false, 'The link is not an image.');
		list($ok, $r, $err) = $this->graph('POST', $this->act() . '/adimages', array('bytes' => base64_encode($bytes)));
		if (!$ok) return array(false, $err);
		foreach ((array)($r['images'] ?? array()) as $img) if (!empty($img['hash'])) return array(true, $img['hash']);
		return array(false, 'Meta did not return the image.');
	}

	/** Creates a simple lead form on the page. */
	public function create_form($name, $privacy_url, $questions, $thanks = '')
	{
		$m = $this->meta();
		if ($m['page_id'] === '') return array(false, 'Choose the Facebook page first.');
		if (!preg_match('~^https?://~i', $privacy_url)) return array(false, 'A privacy policy link (https://…) is required by Meta.');
		$allowed = array('FULL_NAME', 'PHONE', 'EMAIL', 'COMPANY_NAME', 'CITY', 'JOB_TITLE');
		$qs = array();
		foreach ($questions as $q) if (in_array($q, $allowed, true)) $qs[] = array('type' => $q);
		if (!$qs) $qs = array(array('type' => 'FULL_NAME'), array('type' => 'PHONE'));
		$params = array(
			'name' => substr($name, 0, 100), 'questions' => $qs, 'locale' => 'en_US',
			'privacy_policy' => array('url' => $privacy_url, 'link_text' => 'Privacy policy'),
			'follow_up_action_url' => $m['website'] !== '' ? $m['website'] : $privacy_url
		);
		if ($thanks !== '') $params['thank_you_page'] = array('title' => 'Thank you', 'body' => substr($thanks, 0, 300), 'button_type' => 'VIEW_WEBSITE', 'website_url' => $m['website'] !== '' ? $m['website'] : $privacy_url);
		list($ok, $r, $err) = $this->graph('POST', $m['page_id'] . '/leadgen_forms', $params, 'page');
		return $ok ? array(true, (string)$r['id']) : array(false, $err);
	}

	/**
	 * Builds a full lead campaign (campaign > ad set > creative > ad). Everything starts PAUSED.
	 * $in: name, daily_budget, countries[], age_min, age_max, form_id, text, headline, image_url, cta, start
	 * Returns array(ok, message, campaign_id).
	 */
	public function create_lead_campaign($in)
	{
		$m = $this->meta();
		$act = $this->act();
		if ($act === '' || $m['page_id'] === '') return array(false, 'Choose the Facebook page and ad account first.', '');
		$made = array();
		$undo = function () use (&$made) { foreach (array_reverse($made) as $id) $this->graph('DELETE', $id); };
		list($ok, $c, $err) = $this->graph('POST', $act . '/campaigns', array(
			'name' => $in['name'], 'objective' => 'OUTCOME_LEADS', 'status' => 'PAUSED', 'special_ad_categories' => array(), 'is_adset_budget_sharing_enabled' => 'false'
		));
		if (!$ok) return array(false, $err, '');
		$cid = $c['id'];
		$made[] = $cid;
		$adset = array(
			'name' => $in['name'] . ' – audience', 'campaign_id' => $cid, 'daily_budget' => (int)round($in['daily_budget'] * 100),
			'billing_event' => 'IMPRESSIONS', 'optimization_goal' => 'LEAD_GENERATION', 'bid_strategy' => 'LOWEST_COST_WITHOUT_CAP',
			'destination_type' => 'ON_AD', 'promoted_object' => array('page_id' => $m['page_id']), 'status' => 'PAUSED',
			'targeting' => array('geo_locations' => array('countries' => $in['countries']), 'age_min' => $in['age_min'], 'age_max' => $in['age_max'], 'targeting_automation' => array('advantage_audience' => 0))
		);
		if (!empty($in['start'])) $adset['start_time'] = date('c', strtotime($in['start']));
		list($ok, $s, $err) = $this->graph('POST', $act . '/adsets', $adset);
		if (!$ok) { $undo(); return array(false, 'Ad set: ' . $err, ''); }
		$made[] = $s['id'];
		list($ok, $hash) = $this->ad_image($in['image_url']);
		if (!$ok) { $undo(); return array(false, $hash, ''); }
		$link = array(
			'message' => $in['text'], 'name' => $in['headline'], 'link' => $m['website'] !== '' ? $m['website'] : 'https://www.facebook.com/' . $m['page_id'],
			'image_hash' => $hash, 'call_to_action' => array('type' => $in['cta'], 'value' => array('lead_gen_form_id' => $in['form_id']))
		);
		$spec = array('page_id' => $m['page_id'], 'link_data' => $link);
		if ($m['ig_id'] !== '') $spec['instagram_user_id'] = $m['ig_id'];
		list($ok, $cr, $err) = $this->graph('POST', $act . '/adcreatives', array('name' => $in['name'] . ' – creative', 'object_story_spec' => $spec));
		if (!$ok) { $undo(); return array(false, 'Ad creative: ' . $err, ''); }
		list($ok, $ad, $err) = $this->graph('POST', $act . '/ads', array('name' => $in['name'] . ' – ad', 'adset_id' => $s['id'], 'creative' => array('creative_id' => $cr['id']), 'status' => 'PAUSED'));
		if (!$ok) { $undo(); return array(false, 'Ad: ' . $err, ''); }
		return array(true, 'Campaign created and paused. Check it, then press Run.', $cid);
	}

	/** Pauses campaigns whose cost per lead (last 7 days) is above the limit. Returns names paused. */
	public function auto_pause()
	{
		$m = $this->meta();
		if (empty($m['auto_pause']) || (float)$m['max_cpl'] <= 0) return array();
		list($ok, $rows) = $this->campaigns('last_7d');
		if (!$ok) return array();
		$max = (float)$m['max_cpl'];
		$paused = array();
		foreach ($rows as $c) {
			if ($c['status'] !== 'ACTIVE' || $c['spend'] < $max * 2) continue;
			$bad = $c['leads'] === 0 ? $c['spend'] >= $max * 2 : $c['cpl'] > $max;
			if (!$bad) continue;
			list($ok) = $this->set_status($c['id'], 'PAUSED');
			if ($ok) {
				$paused[] = $c['name'];
				$this->log('auto_pause', $c['name'] . ': ' . $m['currency'] . ' ' . number_format($c['spend'], 2) . ' spent, ' . $c['leads'] . ' leads (limit ' . $max . ' a lead)');
			}
		}
		return $paused;
	}

	/* ---------- Claude ---------- */

	/**
	 * One Claude Messages API call. Returns array(ok, text or message).
	 * Raw HTTP: this CodeIgniter 2 app has no Composer, so the PHP SDK is not available.
	 */
	public function ask_claude($system, $prompt, $max_tokens = 4000)
	{
		$c = $this->claude();
		if ($c['api_key'] === '') return array(false, 'Add the Claude API key in Marketing > Connections first.');
		$body = array(
			'model' => $c['model'],
			'max_tokens' => (int)$max_tokens,
			'system' => $system,
			'messages' => array(array('role' => 'user', 'content' => $prompt))
		);
		$headers = array('Content-Type: application/json', 'x-api-key: ' . $c['api_key'], 'anthropic-version: 2026-10-21');
		if ($c['model'] !== 'claude-haiku-5-5') {
			// Short marketing copy: keep thinking light; if a request is declined, retry on Anthropic's recommended model.
			$body['output_config'] = array('effort' => 'low');
			$body['fallbacks'] = 'default';
			$headers[] = 'anthropic-beta: server-side-fallback-2026-07-01';
		}
		list($code, $raw, $err) = $this->curl('https://api.anthropic.com/v1/messages', 'POST', $headers, json_encode($body), 120);
		if ($err !== '') return array(false, 'Could not reach Claude: ' . $err);
		$r = json_decode($raw, true);
		if (!is_array($r)) return array(false, 'Claude sent an empty answer (HTTP ' . $code . ').');
		if (isset($r['error'])) {
			$t = $r['error']['type'] ?? '';
			if ($code === 401 || $t === 'authentication_error') return array(false, 'Claude refused the API key. Check it in Connections.');
			if ($code === 429) return array(false, 'Claude is busy (rate limit). Try again in a minute.');
			return array(false, 'Claude: ' . ($r['error']['message'] ?? 'error ' . $code));
		}
		if (($r['stop_reason'] ?? '') === 'refusal') return array(false, 'Claude declined this request. Change the wording and try again.');
		$text = '';
		foreach ((array)($r['content'] ?? array()) as $b) if (($b['type'] ?? '') === 'text') $text .= $b['text'];
		$text = trim($text);
		return $text !== '' ? array(true, $text) : array(false, 'Claude returned no text.');
	}

	public function claude_system()
	{
		$c = $this->claude();
		$m = $this->meta();
		$s = "You write marketing copy for a B2B company in the UAE. Write ready-to-publish text only: no preamble, no notes, no markdown headings, no quotation marks around the result.";
		if ($c['brand'] !== '') $s .= "\n\nAbout the company:\n" . $c['brand'];
		if ($c['tone'] !== '') $s .= "\n\nTone: " . $c['tone'] . '.';
		if ($m['website'] !== '') $s .= "\nWebsite: " . $m['website'];
		$s .= "\nFollow Meta advertising policies and UAE advertising rules: no false claims, no guaranteed results, no personal attributes (\"Are you…\").";
		return $s;
	}

	public function test_claude()
	{
		list($ok, $t) = $this->ask_claude('Reply with the single word OK.', 'Connection check', 1000);
		return $ok ? array(true, 'Claude is connected.') : array(false, $t);
	}
}
