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

	// Suggestions only; "Load models" in Connections lists what each key can use.
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
		// Several accounts per platform. Settings here; tokens stay in ci_integration_settings (row named in secret_ref).
		if (!$this->db->table_exists('ci_mkt_connections')) {
			$this->db->query("CREATE TABLE `ci_mkt_connections` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`platform` VARCHAR(20) NOT NULL DEFAULT 'meta',
				`name` VARCHAR(100) NOT NULL DEFAULT '',
				`source_name` VARCHAR(150) NOT NULL DEFAULT '',
				`ref_month` VARCHAR(100) NOT NULL DEFAULT '',
				`config_json` TEXT NULL,
				`secret_ref` VARCHAR(60) NOT NULL DEFAULT '',
				`webhook_key` VARCHAR(64) NOT NULL DEFAULT '',
				`verify_token` VARCHAR(64) NOT NULL DEFAULT '',
				`status` TINYINT(1) NOT NULL DEFAULT 1,
				`created_at` DATETIME NOT NULL,
				`updated_at` DATETIME NOT NULL,
				PRIMARY KEY (`id`),
				KEY `idx_mkt_conn_key` (`webhook_key`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		}
		if (!$this->db->field_exists('connection_id', 'ci_mkt_leads')) {
			$this->db->query("ALTER TABLE `ci_mkt_leads` ADD `connection_id` INT(11) NOT NULL DEFAULT 0 AFTER `source`, ADD KEY `idx_mkt_conn` (`connection_id`)");
		}
		if (!$this->db->field_exists('connection_id', 'ci_mkt_posts')) {
			$this->db->query("ALTER TABLE `ci_mkt_posts` ADD `connection_id` INT(11) NOT NULL DEFAULT 0 AFTER `id`, ADD `auto` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`");
		}
		if (!$this->db->field_exists('image_ref', 'ci_mkt_posts')) {
			$this->db->query("ALTER TABLE `ci_mkt_posts` ADD `image_ref` VARCHAR(100) NOT NULL DEFAULT '' AFTER `image_url`");
		}
		if ((int)$this->db->count_all('ci_mkt_connections') === 0) $this->migrate_single_meta();
	}

	/** The one Meta connection of the first version becomes account no. 1 (same webhook key, so outside links keep working). */
	private function migrate_single_meta()
	{
		$row = $this->row('meta');
		$now = date('Y-m-d H:i:s');
		$cfg = !empty($row['config_json']) ? json_decode($row['config_json'], true) : array();
		if (!is_array($cfg)) $cfg = array();
		$key = !empty($cfg['webhook_key']) ? $cfg['webhook_key'] : '';
		if ($row && (!empty($row['api_key']) || $key !== '')) {
			$keep = array_diff_key($cfg, array_flip(array('webhook_key', 'verify_token')));
			$this->db->insert('ci_mkt_connections', array(
				'platform' => 'meta', 'name' => !empty($cfg['page_name']) ? substr($cfg['page_name'], 0, 100) : 'Meta',
				'source_name' => 'Social Media', 'ref_month' => '', 'config_json' => json_encode($keep), 'secret_ref' => 'meta',
				'webhook_key' => $key !== '' ? $key : bin2hex(random_bytes(16)),
				'verify_token' => !empty($cfg['verify_token']) ? $cfg['verify_token'] : bin2hex(random_bytes(12)),
				'status' => !empty($row['status']) ? 1 : 0, 'created_at' => $now, 'updated_at' => $now
			));
			$id = (int)$this->db->insert_id();
			$this->db->where('connection_id', 0)->update('ci_mkt_leads', array('connection_id' => $id));
			$this->db->where('connection_id', 0)->update('ci_mkt_posts', array('connection_id' => $id));
		}
		if ($key !== '') $this->global_cfg(array('cron_key' => $key)); // the cron link stays the same
	}

	/* ---------- global marketing settings (cron key) ---------- */

	public function global_cfg($changes = null)
	{
		$row = $this->row('marketing');
		$cfg = !empty($row['config_json']) ? json_decode($row['config_json'], true) : array();
		if (!is_array($cfg)) $cfg = array();
		if ($changes === null) return $cfg;
		$cfg = array_merge($cfg, $changes);
		$now = date('Y-m-d H:i:s');
		if ($row) $this->db->where('service_name', 'marketing')->update('ci_integration_settings', array('config_json' => json_encode($cfg), 'updated_at' => $now));
		else $this->db->insert('ci_integration_settings', array('service_name' => 'marketing', 'api_key' => '', 'api_secret' => '', 'endpoint' => '', 'config_json' => json_encode($cfg), 'status' => 1, 'created_at' => $now, 'updated_at' => $now));
		return $cfg;
	}

	public function cron_key($renew = false)
	{
		$cfg = $this->global_cfg();
		if ($renew || empty($cfg['cron_key'])) $cfg = $this->global_cfg(array('cron_key' => bin2hex(random_bytes(16))));
		return $cfg['cron_key'];
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

	/* ---------- accounts (connections) ---------- */

	public static $platforms = array('meta' => 'Meta (Facebook / Instagram)', 'google' => 'Google Ads', 'web' => 'Website / Zapier', 'other' => 'Other platform');

	private static $defaults = array(
		'app_id' => '', 'page_id' => '', 'page_name' => '', 'ig_id' => '', 'ig_name' => '', 'ig_ads_id' => '', 'ig_checked' => '', 'ad_account_id' => '', 'graph_version' => 'v23.0',
		'route' => 'fixed', 'owner_id' => 0, 'rr_users' => array(), 'rr_next' => 0, 'agents' => array(),
		'website' => '', 'privacy_url' => '', 'auto_pause' => 0, 'max_cpl' => 0, 'currency' => 'AED', 'last_sync' => '', 'last_rule' => '',
		// Claude autopilot
		'ap_posts' => 'off', 'ap_per_week' => 3, 'ap_time' => '10:00', 'ap_topics' => '', 'ap_images' => '', 'ap_channels' => array('fb'), 'ap_lang' => 'en',
		'ap_img_next' => 0, 'ap_reuse' => 1, 'ap_last_plan' => '', 'ap_ads' => 'off', 'ap_max_daily' => 0, 'ap_step' => 20, 'ap_min_spend' => 0, 'ap_last_ads' => '', 'ap_last_report' => ''
	);
	private static $row_fields = array('id', 'platform', 'name', 'source_name', 'ref_month', 'webhook_key', 'verify_token', 'secret_ref', 'token', 'app_secret', 'enabled', 'exists');

	private $cur = 0;

	private function norm($r)
	{
		$cfg = !empty($r['config_json']) ? json_decode($r['config_json'], true) : array();
		if (!is_array($cfg)) $cfg = array();
		$s = array_merge(self::$defaults, array_diff_key($cfg, array_flip(self::$row_fields)));
		$sec = $r['secret_ref'] !== '' ? $this->row($r['secret_ref']) : null;
		return array_merge($s, array(
			'id' => (int)$r['id'], 'platform' => $r['platform'], 'name' => $r['name'], 'source_name' => $r['source_name'], 'ref_month' => $r['ref_month'],
			'webhook_key' => $r['webhook_key'], 'verify_token' => $r['verify_token'], 'secret_ref' => $r['secret_ref'],
			'token' => !empty($sec['api_key']) ? $sec['api_key'] : '', 'app_secret' => !empty($sec['api_secret']) ? $sec['api_secret'] : '',
			'enabled' => (int)$r['status'] === 1, 'exists' => true
		));
	}

	/** All accounts (id => settings), optionally of one platform. */
	public function connections($platform = '')
	{
		if (!isset($this->cache['conns'])) {
			$this->cache['conns'] = array();
			foreach ($this->db->order_by('id', 'ASC')->get('ci_mkt_connections')->result_array() as $r) $this->cache['conns'][(int)$r['id']] = $this->norm($r);
		}
		if ($platform === '') return $this->cache['conns'];
		return array_filter($this->cache['conns'], function ($c) use ($platform) { return $c['platform'] === $platform; });
	}

	/** Makes one account the current one for meta(), graph(), intake() and the rest. */
	public function use_conn($id)
	{
		if ((int)$id === 0) { $this->cur = 0; $this->page_token = null; return true; }
		$all = $this->connections();
		if (!isset($all[(int)$id])) return false;
		if ($this->cur !== (int)$id) $this->page_token = null;
		$this->cur = (int)$id;
		return true;
	}

	public function cur_id() { return $this->cur; }

	/** Settings of the current account; with none chosen, the first Meta account (an empty one if there is none). */
	public function meta()
	{
		$all = $this->connections();
		if ($this->cur && isset($all[$this->cur])) return $all[$this->cur];
		foreach (array(true, false) as $need_on) foreach ($all as $c) {
			if ($c['platform'] === 'meta' && (!$need_on || $c['enabled'])) { $this->cur = $c['id']; $this->page_token = null; return $c; }
		}
		return array_merge(self::$defaults, array('id' => 0, 'platform' => 'meta', 'name' => '', 'source_name' => '', 'ref_month' => '', 'webhook_key' => '', 'verify_token' => '',
			'secret_ref' => '', 'token' => '', 'app_secret' => '', 'enabled' => false, 'exists' => false));
	}

	/** New account; becomes the current one. Returns its id. */
	public function create_conn($platform, $fields = array())
	{
		$now = date('Y-m-d H:i:s');
		$this->db->insert('ci_mkt_connections', array(
			'platform' => isset(self::$platforms[$platform]) ? $platform : 'other',
			'name' => substr(trim((string)($fields['name'] ?? '')), 0, 100), 'source_name' => substr(trim((string)($fields['source_name'] ?? '')), 0, 150),
			'ref_month' => substr(trim((string)($fields['ref_month'] ?? '')), 0, 100), 'config_json' => '{}', 'secret_ref' => '',
			'webhook_key' => bin2hex(random_bytes(16)), 'verify_token' => bin2hex(random_bytes(12)), 'status' => 1, 'created_at' => $now, 'updated_at' => $now
		));
		$id = (int)$this->db->insert_id();
		unset($this->cache['conns']);
		$this->use_conn($id);
		return $id;
	}

	/**
	 * Saves the current account: config keys, optional token / secret, on-off, and row fields (name, source_name, ref_month).
	 * Creates a Meta account first when there is none.
	 */
	public function save_meta($cfg_changes, $token = '', $secret = '', $enabled = null, $fields = array())
	{
		$old = $this->meta();
		if (!$old['exists']) { $this->create_conn('meta', $fields + array('name' => 'Meta', 'source_name' => 'Social Media')); $old = $this->meta(); }
		$id = $old['id'];
		$cfg = array_merge(array_diff_key($old, array_flip(self::$row_fields)), array_diff_key($cfg_changes, array_flip(self::$row_fields)));
		$now = date('Y-m-d H:i:s');
		$row = array('config_json' => json_encode($cfg), 'updated_at' => $now);
		foreach (array('name' => 100, 'source_name' => 150, 'ref_month' => 100) as $k => $max) if (isset($fields[$k])) $row[$k] = substr(trim((string)$fields[$k]), 0, $max);
		if ($enabled !== null) $row['status'] = $enabled ? 1 : 0;
		if ($token !== '' || $secret !== '') {
			$ref = $old['secret_ref'];
			if ($ref === '') { $ref = 'mkt_conn_' . $id; $row['secret_ref'] = $ref; }
			$sec = array('updated_at' => $now, 'status' => 1);
			if ($token !== '') $sec['api_key'] = $token;
			if ($secret !== '') $sec['api_secret'] = $secret;
			if ($this->row($ref)) $this->db->where('service_name', $ref)->update('ci_integration_settings', $sec);
			else $this->db->insert('ci_integration_settings', $sec + array('service_name' => $ref, 'api_key' => '', 'api_secret' => '', 'endpoint' => '', 'config_json' => '{}', 'created_at' => $now));
		}
		$this->db->where('id', $id)->update('ci_mkt_connections', $row);
		unset($this->cache['conns']);
		$this->page_token = null;
	}

	/** Removes the token and app secret of the current account and switches it off. */
	public function disconnect()
	{
		$m = $this->meta();
		if (!$m['exists']) return;
		if ($m['secret_ref'] !== '') $this->db->where('service_name', $m['secret_ref'])->update('ci_integration_settings', array('api_key' => '', 'api_secret' => '', 'status' => 0, 'updated_at' => date('Y-m-d H:i:s')));
		$this->db->where('id', $m['id'])->update('ci_mkt_connections', array('status' => 0, 'updated_at' => date('Y-m-d H:i:s')));
		unset($this->cache['conns']);
		$this->page_token = null;
	}

	/** Deletes the current account and its keys. Leads already taken in stay; its unpublished posts are stopped. */
	public function delete_conn()
	{
		$m = $this->meta();
		if (!$m['exists']) return false;
		if ($m['secret_ref'] !== '') {
			if ($m['secret_ref'] === 'meta') $this->db->where('service_name', 'meta')->update('ci_integration_settings', array('api_key' => '', 'api_secret' => '', 'status' => 0));
			else $this->db->where('service_name', $m['secret_ref'])->delete('ci_integration_settings');
		}
		$this->db->where('connection_id', $m['id'])->where_in('status', array('Draft', 'Scheduled', 'Review'))->update('ci_mkt_posts', array('status' => 'Failed', 'error' => 'The account was removed.'));
		$this->db->where('id', $m['id'])->delete('ci_mkt_connections');
		unset($this->cache['conns']);
		$this->cur = 0;
		$this->page_token = null;
		return true;
	}

	public function webhook_key()
	{
		return $this->meta()['webhook_key'];
	}

	public function renew_keys()
	{
		$m = $this->meta();
		if (!$m['exists']) return;
		$this->db->where('id', $m['id'])->update('ci_mkt_connections', array('webhook_key' => bin2hex(random_bytes(16)), 'verify_token' => bin2hex(random_bytes(12)), 'updated_at' => date('Y-m-d H:i:s')));
		unset($this->cache['conns']);
	}

	/** Account whose webhook key this is (0 if none). */
	public function conn_by_key($key)
	{
		$key = (string)$key;
		if ($key === '') return 0;
		foreach ($this->connections() as $c) if ($c['webhook_key'] !== '' && hash_equals($c['webhook_key'], $key)) return $c['id'];
		return 0;
	}

	/** Meta account of a Facebook page (0 if none). */
	public function conn_by_page($page_id)
	{
		foreach ($this->connections('meta') as $c) if ($c['page_id'] !== '' && (string)$c['page_id'] === (string)$page_id) return $c['id'];
		return 0;
	}

	/** Puts the source name in the CRM's Data Source list so it can be picked and filtered on everywhere. */
	public function ensure_source_term($title)
	{
		$title = trim((string)$title);
		if ($title === '') return;
		$has = $this->db->where('cat_type', 'data_source')->where('title', $title)->where('delete_status', 0)->count_all_results('ci_terms');
		if (!$has) $this->db->insert('ci_terms', array('parent_id' => 0, 'cat_type' => 'data_source', 'title' => substr($title, 0, 500), 'created' => date('Y-m-d H:i:s'), 'status' => 1, 'delete_status' => 0));
	}

	public function source_terms()
	{
		return array_column($this->db->select('title')->where('cat_type', 'data_source')->where('status', 1)->where('delete_status', 0)->order_by('title', 'ASC')->get('ci_terms')->result_array(), 'title');
	}

	/** Batch / Ref for a new record: the account's Ref, or "Mon-YY/Source" (the CRM's own pattern) when empty. {month} becomes Mon-YY. */
	public function ref_for($c)
	{
		$mon = date('M-y');
		$ref = trim((string)($c['ref_month'] ?? ''));
		if ($ref === '') return $mon . '/' . (($c['source_name'] ?? '') !== '' ? $c['source_name'] : 'Online');
		return str_replace('{month}', $mon, $ref);
	}

	/* ---------- AI providers (writing, ad reviews, autopilot) ---------- */

	/**
	 * Providers the marketing tools can write with. Keys live only in ci_integration_settings.
	 * Groq reuses the key the dialer already has (row 'groq'); only the marketing model is kept in the 'marketing' row.
	 */
	public static $ai_providers = array(
		'anthropic' => array('label' => 'Claude (Anthropic)', 'row' => 'anthropic', 'model' => 'claude-opus-5-5', 'hint' => 'sk-ant-… from console.anthropic.com', 'key_re' => '/^sk-ant-[A-Za-z0-9_.\-]{20,250}$/'),
		'gemini' => array('label' => 'Gemini (Google)', 'row' => 'gemini', 'model' => 'gemini-2.5-flash', 'hint' => 'AIza… from aistudio.google.com', 'key_re' => '/^[A-Za-z0-9_.\-]{20,300}$/'),
		'openai' => array('label' => 'OpenAI (ChatGPT)', 'row' => 'openai', 'model' => 'gpt-5-mini', 'hint' => 'sk-… from platform.openai.com', 'key_re' => '/^sk-[A-Za-z0-9_.\-]{20,300}$/'),
		'groq' => array('label' => 'Groq', 'row' => 'groq', 'model' => 'openai/gpt-oss-120b', 'hint' => 'Uses the Groq key from the dialer settings', 'key_re' => '')
	);

	/** Company details, tone, main provider and fallback. Brand and tone stay in the 'anthropic' row as before. */
	public function claude()
	{
		if (isset($this->cache['claude'])) return $this->cache['claude'];
		$row = $this->row('anthropic');
		$cfg = !empty($row['config_json']) ? json_decode($row['config_json'], true) : array();
		if (!is_array($cfg)) $cfg = array();
		$s = array_merge(array('model' => 'claude-opus-5-5', 'brand' => '', 'tone' => 'Professional and friendly'), $cfg);
		$s['api_key'] = !empty($row['api_key']) ? $row['api_key'] : '';
		$s['exists'] = !empty($row);
		$g = $this->global_cfg();
		$s['provider'] = isset($g['ai_provider'], self::$ai_providers[$g['ai_provider']]) ? $g['ai_provider'] : 'anthropic';
		$s['fallback'] = !isset($g['ai_fallback']) || !empty($g['ai_fallback']);
		return $this->cache['claude'] = $s;
	}

	/** One provider's key and model. */
	public function ai($p)
	{
		if (isset($this->cache['ai_' . $p])) return $this->cache['ai_' . $p];
		$d = self::$ai_providers[$p];
		$row = $this->row($d['row']);
		$cfg = !empty($row['config_json']) ? json_decode($row['config_json'], true) : array();
		if (!is_array($cfg)) $cfg = array();
		$key = !empty($row['api_key']) ? $row['api_key'] : '';
		if ($p === 'groq') {
			// Same order as Groq_service: environment, then the DB row, then a constant.
			$env = getenv('GROQ_API_KEY');
			if (!empty($env)) $key = $env;
			elseif ($key === '' && defined('GROQ_API_KEY')) $key = GROQ_API_KEY;
			$g = $this->global_cfg();
			$model = !empty($g['ai_groq_model']) ? $g['ai_groq_model'] : $d['model'];
		} else {
			$model = !empty($cfg['model']) ? $cfg['model'] : $d['model'];
		}
		return $this->cache['ai_' . $p] = array('provider' => $p, 'label' => $d['label'], 'key' => (string)$key, 'model' => (string)$model, 'exists' => !empty($row));
	}

	/** Providers to try: the main one first, then (with fallback on) the others that have a key. */
	public function ai_order()
	{
		$c = $this->claude();
		$out = array();
		if ($this->ai($c['provider'])['key'] !== '') $out[] = $c['provider'];
		foreach (array_keys(self::$ai_providers) as $p) if ($p !== $c['provider'] && $this->ai($p)['key'] !== '') $out[] = $p;
		return $c['fallback'] ? $out : array_slice($out, 0, 1);
	}

	public function ai_ready()
	{
		return count($this->ai_order()) > 0;
	}

	/**
	 * $cfg: brand, tone, provider, fallback, models[provider]. $keys[provider]: a new key ('' keeps the saved one).
	 * The Groq key is never changed here: it belongs to the dialer settings.
	 */
	public function save_ai($cfg, $keys = array())
	{
		$now = date('Y-m-d H:i:s');
		$models = isset($cfg['models']) && is_array($cfg['models']) ? $cfg['models'] : array();
		$clean = function ($v, $def) { $v = trim((string)$v); return preg_match('/^[A-Za-z0-9][A-Za-z0-9_.:\/\-]{1,99}$/', $v) ? $v : $def; };
		foreach (array('anthropic', 'gemini', 'openai') as $p) {
			$d = self::$ai_providers[$p];
			$row = $this->row($d['row']);
			$old = !empty($row['config_json']) ? json_decode($row['config_json'], true) : array();
			if (!is_array($old)) $old = array();
			$new = $old;
			$new['model'] = $clean($models[$p] ?? '', !empty($old['model']) ? $old['model'] : $d['model']);
			if ($p === 'anthropic') {
				$new['brand'] = substr(trim((string)($cfg['brand'] ?? '')), 0, 1500);
				$new['tone'] = substr(trim((string)($cfg['tone'] ?? '')), 0, 100);
			}
			$key = trim((string)($keys[$p] ?? ''));
			if (!$row && $key === '' && $p !== 'anthropic' && $new['model'] === $d['model']) continue;
			$upd = array('config_json' => json_encode($new), 'updated_at' => $now, 'status' => 1);
			if ($key !== '') $upd['api_key'] = $key;
			if ($row) $this->db->where('service_name', $d['row'])->update('ci_integration_settings', $upd);
			else $this->db->insert('ci_integration_settings', $upd + array('service_name' => $d['row'], 'api_key' => '', 'api_secret' => '', 'endpoint' => '', 'created_at' => $now));
		}
		$this->global_cfg(array(
			'ai_provider' => isset(self::$ai_providers[$cfg['provider'] ?? '']) ? $cfg['provider'] : 'anthropic',
			'ai_fallback' => !empty($cfg['fallback']) ? 1 : 0,
			'ai_groq_model' => $clean($models['groq'] ?? '', $this->ai('groq')['model'])
		));
		$this->cache = array_diff_key($this->cache, array_flip(array('claude', 'ai_anthropic', 'ai_gemini', 'ai_openai', 'ai_groq')));
	}

	/** Remove one provider's key (not Groq's: the dialer uses it). */
	public function remove_ai_key($p)
	{
		if (!in_array($p, array('anthropic', 'gemini', 'openai'), true)) return false;
		$this->db->where('service_name', self::$ai_providers[$p]['row'])->update('ci_integration_settings', array('api_key' => '', 'updated_at' => date('Y-m-d H:i:s')));
		$this->cache = array_diff_key($this->cache, array_flip(array('claude', 'ai_' . $p)));
		return true;
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

	/**
	 * Finds the Instagram account of the current account's page and saves it: the linked Instagram business account
	 * (posts and ads) and the page-backed Instagram account (ads only, when nothing is linked). Returns array(linked, message).
	 */
	public function detect_instagram($save = true)
	{
		$m = $this->meta();
		if (!$m['exists'] || $m['platform'] !== 'meta' || $m['page_id'] === '' || $m['token'] === '') return array(false, 'Choose the Facebook page first.');
		list($ok, $r, $err) = $this->graph('GET', $m['page_id'], array('fields' => 'instagram_business_account{id,username}'), 'page');
		if (!$ok) return array(false, 'Instagram could not be checked: ' . $err);
		$ig = !empty($r['instagram_business_account']['id']) ? $r['instagram_business_account'] : null;
		list($ok2, $r2) = $this->graph('GET', $m['page_id'] . '/page_backed_instagram_accounts', array('fields' => 'id'), 'page');
		$pb = $ok2 && !empty($r2['data'][0]['id']) ? (string)$r2['data'][0]['id'] : '';
		$ch = array('ig_ads_id' => $pb, 'ig_checked' => date('Y-m-d H:i:s'), 'ig_id' => $ig ? (string)$ig['id'] : '', 'ig_name' => $ig ? (string)($ig['username'] ?? '') : '');
		if ($ig && $ch['ig_name'] === '') {
			list($ok3, $r3) = $this->graph('GET', $ch['ig_id'], array('fields' => 'username'), 'page');
			if ($ok3 && !empty($r3['username'])) $ch['ig_name'] = (string)$r3['username'];
		}
		if ($save) $this->save_meta($ch);
		if ($ig) return array(true, 'Instagram ' . ($ch['ig_name'] !== '' ? '@' . $ch['ig_name'] : $ch['ig_id']) . ' found and set: posts and ads go to Instagram too.');
		return array(false, 'No Instagram business account is linked to the page "' . $m['page_name'] . '"' . ($pb !== '' ? '. Ads will still show on Instagram through the page; to post on Instagram, link the account in Meta Business Suite › Settings › Linked accounts.' : '. Link it in Meta Business Suite › Settings › Linked accounts.'));
	}

	/**
	 * Earlier creatives of the current account: page posts with their text and picture, and ad creatives of the ad account.
	 * Each: ref (post:ID / ad:ID), kind, name, text, headline, image_url, image_hash, date. Returns array(items, error text).
	 */
	public function past_creatives($limit = 30)
	{
		$m = $this->meta();
		$out = array();
		$errs = array();
		if ($m['exists'] && $m['page_id'] !== '') {
			list($ok, $r, $err) = $this->graph('GET', $m['page_id'] . '/published_posts', array('fields' => 'id,message,full_picture,created_time', 'limit' => (int)$limit), 'page');
			if ($ok) foreach ($r['data'] ?? array() as $p) {
				$text = trim((string)($p['message'] ?? ''));
				if ($text === '' && empty($p['full_picture'])) continue;
				$out[] = array('ref' => 'post:' . $p['id'], 'kind' => 'Page post', 'name' => $text !== '' ? mb_substr(preg_replace('/\s+/', ' ', $text), 0, 70) : 'Picture post',
					'text' => $text, 'headline' => '', 'image_url' => (string)($p['full_picture'] ?? ''), 'image_hash' => '', 'date' => substr((string)($p['created_time'] ?? ''), 0, 10));
			} else $errs[] = 'Page posts: ' . preg_replace('/^Meta: /', '', $err);
		}
		if ($m['exists'] && $this->act() !== '') {
			list($ok, $r, $err) = $this->graph('GET', $this->act() . '/adcreatives', array('fields' => 'id,name,title,body,image_hash,image_url,thumbnail_url,object_story_spec', 'limit' => (int)$limit));
			if ($ok) foreach ($r['data'] ?? array() as $c) {
				$ld = $c['object_story_spec']['link_data'] ?? array();
				$text = trim((string)($c['body'] ?? ($ld['message'] ?? '')));
				$hash = (string)($c['image_hash'] ?? ($ld['image_hash'] ?? ''));
				$url = (string)($c['image_url'] ?? ($ld['picture'] ?? ($c['thumbnail_url'] ?? '')));
				if ($text === '' && $url === '' && $hash === '') continue;
				$out[] = array('ref' => 'ad:' . $c['id'], 'kind' => 'Ad', 'name' => (string)($c['name'] ?? 'Ad creative'), 'text' => $text,
					'headline' => trim((string)($c['title'] ?? ($ld['name'] ?? ''))), 'image_url' => $url, 'image_hash' => $hash, 'date' => '');
			} else $errs[] = 'Ad creatives: ' . preg_replace('/^Meta: /', '', $err);
		}
		return array($out, implode(' ', $errs));
	}

	/** A fresh picture link for an earlier post or ad (post:ID / ad:ID); '' if it cannot be read. */
	public function fresh_image($ref)
	{
		if (preg_match('/^post:([\d_]+)$/', $ref, $x)) { list($ok, $r) = $this->graph('GET', $x[1], array('fields' => 'full_picture'), 'page'); return $ok ? (string)($r['full_picture'] ?? '') : ''; }
		if (preg_match('/^ad:(\d+)$/', $ref, $x)) { list($ok, $r) = $this->graph('GET', $x[1], array('fields' => 'image_url')); return $ok ? (string)($r['image_url'] ?? '') : ''; }
		return '';
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
		$in = array_merge(array('source' => 'web', 'external_id' => '', 'form_id' => '', 'form_name' => '', 'ad_id' => '', 'ad_name' => '', 'campaign_name' => '', 'platform' => '', 'full_name' => '', 'phone' => '', 'email' => '', 'company' => '', 'city' => '', 'other' => array(), 'no_conn' => false), $in);
		// the current account (set with use_conn) owns the lead; manual leads may have none
		$conn = null;
		if (empty($in['no_conn'])) { $c = $this->meta(); if ($c['exists']) $conn = $c; }
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
			'city' => substr(trim($in['city']), 0, 100), 'fields_json' => json_encode($in['other'], JSON_UNESCAPED_UNICODE), 'received_at' => date('Y-m-d H:i:s'),
			'connection_id' => $conn ? $conn['id'] : 0
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
		$owner = !empty($in['owner']) ? (int)$in['owner'] : $this->next_owner();
		$labels = array('meta' => 'Meta Lead Ads', 'google' => 'Google Ads', 'web' => 'Website', 'manual' => 'Marketing', 'other' => 'Marketing');
		$label = $labels[$row['source']] ?? 'Marketing';
		$co = $conn ? array_values(array_diff(array_map('intval', (array)$conn['agents']), array((int)$owner, 0))) : array();
		$note = array();
		if ($row['campaign_name'] !== '') $note[] = 'Campaign: ' . $row['campaign_name'];
		if ($row['ad_name'] !== '') $note[] = 'Ad: ' . $row['ad_name'];
		if ($row['form_name'] !== '') $note[] = 'Form: ' . $row['form_name'];
		foreach ($in['other'] as $k => $v) $note[] = ucfirst(str_replace('_', ' ', $k)) . ': ' . (is_array($v) ? implode(', ', $v) : $v);
		$lead = array(
			'customer' => $row['company'] !== '' ? $row['company'] : ($row['full_name'] !== '' ? $row['full_name'] : $row['phone']),
			'poc' => $row['full_name'], 'contact1' => $row['phone'], 'mobile' => $row['phone'], 'email' => $row['email'], 'city' => $row['city'],
			'status' => 36, 'lead_assign' => $owner,
			'lead_source' => $conn && $conn['name'] !== '' ? $conn['name'] : $label,
			'data_source' => $conn && $conn['source_name'] !== '' ? $conn['source_name'] : $label,
			'created_by' => $owner, 'admin_status' => 1, 'created' => date('Y-m-d'), 'c_partner' => 'Seven Strokes', 'country' => 0, 'state' => 0
		);
		if ($conn) {
			$lead['data_batch_referral_no'] = substr($this->ref_for($conn), 0, 100);
			$lead['month'] = date('M-y');
			if ($co) $lead['agents'] = implode(',', $co);
		}
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
			if (!$dup) $this->db->insert('ci_mkt_leads', array('source' => 'meta', 'connection_id' => (int)$this->meta()['id'], 'external_id' => substr((string)$leadgen_id, 0, 64), 'form_id' => substr((string)($hint['form_id'] ?? ''), 0, 64), 'status' => 'Error', 'error' => substr('Could not read the lead from Meta. ' . $err, 0, 255), 'received_at' => date('Y-m-d H:i:s')));
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

	/** Pulls new leads of every Meta account that is on. Returns array(new, message). */
	public function sync_all()
	{
		$keep = $this->cur;
		$new = 0;
		$errs = array();
		foreach ($this->connections('meta') as $c) {
			if (!$c['enabled'] || $c['token'] === '' || $c['page_id'] === '') continue;
			$this->use_conn($c['id']);
			list($n, $msg) = $this->sync_leads();
			$new += $n;
			if (!$n && $msg !== 'No new leads.') $errs[] = $c['name'] . ': ' . $msg;
		}
		$this->cur = $keep;
		$this->page_token = null;
		$msg = $new ? $new . ' new lead' . ($new === 1 ? '' : 's') . ' taken in.' : 'No new leads.';
		return array($new, $msg . ($errs ? ' ' . implode(' ', $errs) : ''));
	}

	/** Leads of all accounts with the record's current owner and co-workers. */
	public function leads($where = '1=1', $limit = 200)
	{
		return $this->db->query("SELECT M.*, C.name AS conn_name, C.platform AS conn_platform, L.lead_id AS ref, L.data_source AS crm_source, L.data_batch_referral_no AS batch, L.agents,
				IF(L.id IS NULL, M.assigned_to, IF(L.lead_assign > 0, L.lead_assign, L.created_by)) AS owner_id, U.name AS owner_name
			FROM ci_mkt_leads M
			LEFT JOIN ci_mkt_connections C ON C.id = M.connection_id
			LEFT JOIN ci_leads L ON L.id = M.lead_row_id
			LEFT JOIN ci_user U ON U.id = IF(L.id IS NULL, M.assigned_to, IF(L.lead_assign > 0, L.lead_assign, L.created_by))
			WHERE {$where} ORDER BY M.id DESC LIMIT " . (int)$limit)->result_array();
	}

	/**
	 * Gives marketing leads (ci_mkt_leads ids) to one owner plus co-workers who can open and work the record too.
	 * Only records this module created; "Already in CRM" leads belong to someone else's record. Returns the number changed.
	 */
	public function assign($ids, $owner, $agents)
	{
		$ids = array_values(array_filter(array_map('intval', (array)$ids)));
		$owner = (int)$owner;
		$co = array_values(array_unique(array_diff(array_filter(array_map('intval', (array)$agents)), array($owner))));
		if (!$ids || $owner <= 0) return 0;
		$n = 0;
		foreach ($this->db->select('id, lead_row_id')->where_in('id', $ids)->where('status', 'Created')->where('lead_row_id >', 0)->get('ci_mkt_leads')->result_array() as $r) {
			$this->db->where('id', (int)$r['lead_row_id'])->update('ci_leads', array('lead_assign' => $owner, 'agents' => implode(',', $co)));
			$this->db->where('id', (int)$r['id'])->update('ci_mkt_leads', array('assigned_to' => $owner));
			$n++;
		}
		return $n;
	}

	/**
	 * Report rows grouped by owner, account or source for leads received between two dates.
	 * Owner = the CRM record's owner now; "co_work" counts records where the person is a co-worker.
	 */
	public function report($from, $to, $group = 'owner', $conn_id = 0)
	{
		$from = date('Y-m-d 00:00:00', strtotime($from));
		$to = date('Y-m-d 23:59:59', strtotime($to));
		$w = "M.received_at BETWEEN " . $this->db->escape($from) . " AND " . $this->db->escape($to);
		if ($conn_id > 0) $w .= " AND M.connection_id = " . (int)$conn_id;
		elseif ($conn_id === -1) $w .= " AND M.connection_id = 0";
		$rows = $this->db->query("SELECT M.id, M.connection_id, M.status, M.lead_row_id, C.name AS conn_name, C.source_name AS conn_source, L.data_source, L.agents,
				IF(L.id IS NULL, M.assigned_to, IF(L.lead_assign > 0, L.lead_assign, L.created_by)) AS owner_id, P.pipeline_stage, IFNULL(O.n, 0) AS orders
			FROM ci_mkt_leads M
			LEFT JOIN ci_mkt_connections C ON C.id = M.connection_id
			LEFT JOIN ci_leads L ON L.id = M.lead_row_id
			LEFT JOIN ci_pipeline P ON P.lead_id = L.id
			LEFT JOIN (SELECT lead_id, COUNT(*) AS n FROM ci_orders WHERE lead_id > 0 GROUP BY lead_id) O ON O.lead_id = L.id
			WHERE {$w}")->result_array();
		$names = array();
		foreach ($this->db->select('id, name')->get('ci_user')->result_array() as $u) $names[(int)$u['id']] = $u['name'];
		$blank = array('received' => 0, 'created' => 0, 'dup' => 0, 'err' => 0, 'database' => 0, 'lead' => 0, 'further' => 0, 'orders' => 0, 'co_work' => 0);
		$out = array();
		$add = function ($key, $label) use (&$out, $blank) { if (!isset($out[$key])) $out[$key] = array('key' => $key, 'label' => $label) + $blank; };
		foreach ($rows as $r) {
			$oid = (int)$r['owner_id'];
			if ($group === 'account') { $k = 'c' . (int)$r['connection_id']; $label = $r['connection_id'] ? ($r['conn_name'] !== null ? $r['conn_name'] : 'Removed account') : 'Added here'; }
			elseif ($group === 'source') { $label = $r['data_source'] ?: ($r['conn_source'] ?: 'Marketing'); $k = 's' . strtolower($label); }
			else { $k = 'u' . $oid; $label = $oid ? ($names[$oid] ?? 'User ' . $oid) : 'No owner'; }
			$add($k, $label);
			$o = &$out[$k];
			$o['received']++;
			if ($r['status'] === 'Created') {
				$o['created']++;
				$st = $r['pipeline_stage'] ?: 'database';
				if ($st === 'database') $o['database']++; elseif ($st === 'lead') $o['lead']++; else $o['further']++;
				$o['orders'] += (int)$r['orders'];
			} elseif ($r['status'] === 'Duplicate') $o['dup']++;
			elseif ($r['status'] === 'Error') $o['err']++;
			unset($o);
			if ($group === 'owner' && $r['status'] === 'Created' && $r['agents'] !== null && $r['agents'] !== '') {
				foreach (array_unique(array_filter(array_map('intval', explode(',', $r['agents'])))) as $a) {
					if ($a === $oid) continue;
					$add('u' . $a, $names[$a] ?? 'User ' . $a);
					$out['u' . $a]['co_work']++;
				}
			}
		}
		usort($out, function ($a, $b) { return ($b['received'] <=> $a['received']) ?: strcasecmp($a['label'], $b['label']); });
		return $out;
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

	/** Posts of one Meta account (the current one by default). */
	public function posts($limit = 100, $conn_id = null)
	{
		$cid = $conn_id === null ? (int)$this->meta()['id'] : (int)$conn_id;
		return $this->db->query("SELECT P.*, U.name AS by_name FROM ci_mkt_posts P LEFT JOIN ci_user U ON U.id = P.created_by WHERE P.connection_id = {$cid}
			ORDER BY (P.status = 'Review') DESC, (P.status = 'Scheduled') DESC, COALESCE(P.posted_at, P.scheduled_at, P.created_at) DESC LIMIT " . (int)$limit)->result_array();
	}

	/** Publishes one post row now (on the account it belongs to). Returns array(ok, message). */
	public function publish($p)
	{
		if (!empty($p['connection_id'])) $this->use_conn((int)$p['connection_id']);
		$m = $this->meta();
		if (!$m['exists'] || (!empty($p['connection_id']) && (int)$m['id'] !== (int)$p['connection_id'])) {
			$this->db->where('id', (int)$p['id'])->update('ci_mkt_posts', array('status' => 'Failed', 'error' => 'The account was removed.'));
			return array(false, 'The account was removed.');
		}
		// an earlier post or ad image: Meta image links expire, so fetch a fresh one now
		if (!empty($p['image_ref'])) {
			$u = $this->fresh_image($p['image_ref']);
			if ($u !== '' && $u !== $p['image_url']) { $p['image_url'] = $u; $this->db->where('id', (int)$p['id'])->update('ci_mkt_posts', array('image_url' => substr($u, 0, 500))); }
		}
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
		if (!empty($in['image_hash'])) { $ok = true; $hash = $in['image_hash']; } // image of an earlier ad
		else list($ok, $hash) = $this->ad_image($in['image_url']);
		if (!$ok) { $undo(); return array(false, $hash, ''); }
		$link = array(
			'message' => $in['text'], 'name' => $in['headline'], 'link' => $m['website'] !== '' ? $m['website'] : 'https://www.facebook.com/' . $m['page_id'],
			'image_hash' => $hash, 'call_to_action' => array('type' => $in['cta'], 'value' => array('lead_gen_form_id' => $in['form_id']))
		);
		$spec = array('page_id' => $m['page_id'], 'link_data' => $link);
		if ($m['ig_id'] !== '') $spec['instagram_user_id'] = $m['ig_id'];
		elseif ($m['ig_ads_id'] !== '') $spec['instagram_user_id'] = $m['ig_ads_id']; // page-backed Instagram account: the ad still shows on Instagram
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

	/* ---------- AI requests ---------- */

	public $ai_used = '';

	/**
	 * One writing request. The main provider answers; if it fails and fallback is on, the next provider with a key tries.
	 * Returns array(ok, text or message). With $schema the text is JSON for that schema.
	 */
	public function ask_ai($system, $prompt, $max_tokens = 4000, $schema = null)
	{
		$order = $this->ai_order();
		$this->ai_used = '';
		if (!$order) return array(false, 'Add an AI key (Claude, Gemini, OpenAI or Groq) in Marketing > Connections first.');
		$errs = array();
		foreach ($order as $p) {
			list($ok, $t) = $this->ask_one($p, $system, $prompt, $max_tokens, $schema);
			if ($ok) { $this->ai_used = $p; return array(true, $t); }
			$errs[] = $t;
		}
		return array(false, implode(' ', $errs));
	}

	/** Older name, same behaviour. */
	public function ask_claude($system, $prompt, $max_tokens = 4000, $schema = null)
	{
		return $this->ask_ai($system, $prompt, $max_tokens, $schema);
	}

	public function ask_one($p, $system, $prompt, $max_tokens = 4000, $schema = null)
	{
		$a = $this->ai($p);
		if ($a['key'] === '') return array(false, $a['label'] . ': no key saved.');
		if ($p === 'anthropic') return $this->ask_anthropic($a, $system, $prompt, $max_tokens, $schema);
		if ($schema) $prompt .= "\n\nAnswer with one JSON object only, no other text, matching this JSON Schema:\n" . json_encode($schema);
		if ($p === 'gemini') list($ok, $t) = $this->ask_gemini($a, $system, $prompt, $max_tokens, (bool)$schema);
		else list($ok, $t) = $this->ask_openai_style($a, $system, $prompt, $max_tokens, (bool)$schema);
		if ($ok && $schema) $t = self::json_only($t);
		return array($ok, $t);
	}

	/** Strip ``` fences or text around a JSON object. */
	public static function json_only($t)
	{
		$t = trim($t);
		if (preg_match('/^```(?:json)?\s*(.*?)\s*```$/s', $t, $mm)) $t = $mm[1];
		if (json_decode($t, true) === null) {
			$a = strpos($t, '{'); $b = strrpos($t, '}');
			if ($a !== false && $b > $a) $t = substr($t, $a, $b - $a + 1);
		}
		return $t;
	}

	private function ai_error($label, $code, $msg)
	{
		if ($code === 401 || $code === 403) return $label . ' refused the API key. Check it in Connections.';
		if ($code === 429) return $label . ' is busy or out of credit (HTTP 429). Try again later.';
		if ($code === 404) return $label . ': model not found. Choose another model in Connections.';
		return $label . ': ' . ($msg !== '' ? $msg : 'error ' . $code);
	}

	/** Gemini generateContent (REST). */
	private function ask_gemini($a, $system, $prompt, $max_tokens, $json)
	{
		$body = array(
			'system_instruction' => array('parts' => array(array('text' => $system))),
			'contents' => array(array('role' => 'user', 'parts' => array(array('text' => $prompt)))),
			// Thinking models count their thinking against this limit, so leave room.
			'generationConfig' => array('maxOutputTokens' => max(2048, (int)$max_tokens * 2))
		);
		if ($json) $body['generationConfig']['responseMimeType'] = 'application/json';
		$url = 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode(preg_replace('#^models/#', '', $a['model'])) . ':generateContent';
		list($code, $raw, $err) = $this->curl($url, 'POST', array('Content-Type: application/json', 'x-goog-api-key: ' . $a['key']), json_encode($body), 120);
		if ($err !== '') return array(false, 'Could not reach Gemini: ' . $err);
		$r = json_decode($raw, true);
		if (!is_array($r)) return array(false, 'Gemini sent an empty answer (HTTP ' . $code . ').');
		if (isset($r['error'])) {
			$msg = (string)($r['error']['message'] ?? '');
			if ($code === 400 && stripos($msg, 'API key') !== false) $code = 401;
			return array(false, $this->ai_error('Gemini', $code, $msg));
		}
		$cand = $r['candidates'][0] ?? array();
		$text = '';
		foreach ((array)($cand['content']['parts'] ?? array()) as $part) if (isset($part['text']) && empty($part['thought'])) $text .= $part['text'];
		$text = trim($text);
		if ($text === '') {
			$why = $cand['finishReason'] ?? ($r['promptFeedback']['blockReason'] ?? '');
			return array(false, 'Gemini returned no text' . ($why !== '' ? ' (' . $why . ')' : '') . '.');
		}
		return array(true, $text);
	}

	/** OpenAI and Groq: the same chat completions format. */
	private function ask_openai_style($a, $system, $prompt, $max_tokens, $json)
	{
		$groq = $a['provider'] === 'groq';
		$url = $groq ? 'https://api.groq.com/openai/v1/chat/completions' : 'https://api.openai.com/v1/chat/completions';
		$reasoning = (bool)preg_match('#^(gpt-5|o\d|openai/gpt-oss)#', $a['model']);
		$body = array(
			'model' => $a['model'],
			'messages' => array(array('role' => 'system', 'content' => $system), array('role' => 'user', 'content' => $prompt)),
			'max_completion_tokens' => $reasoning ? max(4000, (int)$max_tokens * 2) : (int)$max_tokens
		);
		if ($reasoning) $body['reasoning_effort'] = 'low';
		if ($json) $body['response_format'] = array('type' => 'json_object');
		list($code, $raw, $err) = $this->curl($url, 'POST', array('Content-Type: application/json', 'Authorization: Bearer ' . $a['key']), json_encode($body), 120);
		if ($err !== '') return array(false, 'Could not reach ' . $a['label'] . ': ' . $err);
		$r = json_decode($raw, true);
		if (!is_array($r)) return array(false, $a['label'] . ' sent an empty answer (HTTP ' . $code . ').');
		if (isset($r['error'])) return array(false, $this->ai_error($a['label'], $code, (string)(is_array($r['error']) ? ($r['error']['message'] ?? '') : $r['error'])));
		$ch = $r['choices'][0] ?? array();
		$text = trim((string)($ch['message']['content'] ?? ''));
		if ($text === '') {
			if (!empty($ch['message']['refusal'])) return array(false, $a['label'] . ' declined this request. Change the wording and try again.');
			return array(false, $a['label'] . ' returned no text' . (!empty($ch['finish_reason']) ? ' (' . $ch['finish_reason'] . ')' : '') . '.');
		}
		return array(true, $text);
	}

	/** Models the saved key can use, for the model suggestions in Connections. Returns array(ok, list of ids or message). */
	public function list_models($p)
	{
		$a = $this->ai($p);
		if ($a['key'] === '') return array(false, 'Save the ' . $a['label'] . ' key first.');
		if ($p === 'anthropic') { $url = 'https://api.anthropic.com/v1/models?limit=100'; $h = array('x-api-key: ' . $a['key'], 'anthropic-version: 2026-10-21'); }
		elseif ($p === 'gemini') { $url = 'https://generativelanguage.googleapis.com/v1beta/models?pageSize=200'; $h = array('x-goog-api-key: ' . $a['key']); }
		elseif ($p === 'openai') { $url = 'https://api.openai.com/v1/models'; $h = array('Authorization: Bearer ' . $a['key']); }
		else { $url = 'https://api.groq.com/openai/v1/models'; $h = array('Authorization: Bearer ' . $a['key']); }
		list($code, $raw, $err) = $this->curl($url, 'GET', $h, null, 20);
		if ($err !== '') return array(false, 'Could not reach ' . $a['label'] . ': ' . $err);
		$r = json_decode($raw, true);
		if (!is_array($r) || isset($r['error'])) return array(false, $this->ai_error($a['label'], $code, is_array($r) && is_array($r['error'] ?? null) ? (string)($r['error']['message'] ?? '') : ''));
		$out = array();
		if ($p === 'gemini') {
			foreach ((array)($r['models'] ?? array()) as $m) {
				if (!in_array('generateContent', (array)($m['supportedGenerationMethods'] ?? array()), true)) continue;
				$id = preg_replace('#^models/#', '', (string)$m['name']);
				if (preg_match('/embed|aqa|imagen|veo|tts|image|audio|live|robotics|computer/i', $id)) continue;
				$out[] = $id;
			}
		} else {
			foreach ((array)($r['data'] ?? array()) as $m) {
				$id = (string)($m['id'] ?? '');
				if ($id === '') continue;
				if ($p === 'openai' && (!preg_match('/^(gpt-|o\d|chatgpt)/', $id) || preg_match('/audio|realtime|transcribe|tts|image|search|instruct|codex/', $id))) continue;
				if ($p === 'groq' && preg_match('/whisper|tts|guard|playai|orpheus/i', $id)) continue;
				$out[] = $id;
			}
		}
		if ($p !== 'anthropic') rsort($out, SORT_NATURAL);
		return array(true, array_values(array_unique($out)));
	}

	/** Check one provider (or the main one). */
	public function test_ai($p = '')
	{
		if ($p === '') $p = $this->claude()['provider'];
		if (!isset(self::$ai_providers[$p])) return array(false, 'Unknown provider.');
		$a = $this->ai($p);
		list($ok, $t) = $this->ask_one($p, 'Reply with the single word OK.', 'Connection check', 1000);
		return $ok ? array(true, $a['label'] . ' is connected (' . $a['model'] . ').') : array(false, $t);
	}

	/** One Claude Messages API call (raw HTTP: this CodeIgniter 2 app has no Composer, so the PHP SDK is not available). */
	private function ask_anthropic($a, $system, $prompt, $max_tokens = 4000, $schema = null)
	{
		$c = array('api_key' => $a['key'], 'model' => $a['model']);
		$body = array(
			'model' => $c['model'],
			'max_tokens' => (int)$max_tokens,
			'system' => $system,
			'messages' => array(array('role' => 'user', 'content' => $prompt))
		);
		$headers = array('Content-Type: application/json', 'x-api-key: ' . $c['api_key'], 'anthropic-version: 2026-10-21');
		if (isset(self::$claude_models[$c['model']]) && $c['model'] !== 'claude-haiku-5-5') {
			// Short marketing copy: keep thinking light; if a request is declined, retry on Anthropic's recommended model.
			$body['output_config'] = array('effort' => 'low');
			$body['fallbacks'] = 'default';
			$headers[] = 'anthropic-beta: server-side-fallback-2026-07-01';
		}
		// Structured output: the answer is JSON that matches the schema.
		if ($schema) $body['output_config'] = (isset($body['output_config']) ? $body['output_config'] : array()) + array('format' => array('type' => 'json_schema', 'schema' => $schema));
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
		return $this->test_ai();
	}

	/* ---------- connection check ---------- */

	/**
	 * Checks the current Meta account end to end, plus the AI providers and the cron job.
	 * Returns a list of array(area, state ok|warn|bad, text). Saves a newly found Instagram account.
	 */
	public function health_check($ai = true)
	{
		$m = $this->meta();
		$out = array();
		$add = function ($area, $st, $t) use (&$out) { $out[] = array('area' => $area, 'state' => $st, 'text' => $t); };
		if ($m['exists'] && $m['platform'] === 'meta') {
			if (!$m['enabled']) $add('Account', 'warn', 'This account is switched off: no leads are taken in.');
			if ($m['token'] === '') {
				$add('Token', 'bad', 'No access token saved.');
			} else {
				list($ok, $d, $err) = $this->graph('GET', 'debug_token', array('input_token' => $m['token']));
				$d = $ok ? ($d['data'] ?? array()) : array();
				if (!$ok || empty($d['is_valid'])) {
					$add('Token', 'bad', 'The token does not work' . ($err !== '' ? ': ' . preg_replace('/^Meta: /', '', $err) : (!empty($d['error']['message']) ? ': ' . $d['error']['message'] : '.')) . ' Make a new one and save it.');
				} else {
					$exp = (int)($d['expires_at'] ?? 0);
					$type = ($d['type'] ?? '') === 'SYSTEM_USER' ? 'System user token' : (ucfirst(strtolower((string)($d['type'] ?? 'User'))) . ' token');
					$app = !empty($d['application']) ? ' for the app "' . $d['application'] . '"' : '';
					if ($exp === 0) $add('Token', 'ok', $type . $app . ', never expires.');
					else {
						$days = (int)floor(($exp - time()) / 86400);
						$add('Token', $days < 14 ? 'bad' : 'warn', $type . $app . ', expires on ' . date('d M Y', $exp) . ' (' . $days . ' days). In Business Settings › System users, make a token with expiry "Never" and save it here.');
					}
					$need = array('pages_show_list', 'pages_read_engagement', 'pages_manage_metadata', 'pages_manage_posts', 'leads_retrieval', 'ads_management', 'ads_read', 'instagram_basic', 'instagram_content_publish', 'business_management');
					$miss = array_values(array_diff($need, (array)($d['scopes'] ?? array())));
					$add('Permissions', $miss ? 'warn' : 'ok', $miss ? 'Missing: ' . implode(', ', $miss) . '. Add them to the token, otherwise some parts do not work.' : 'All needed permissions are given.');
				}
			}
			$add('App secret', $m['app_secret'] !== '' ? 'ok' : 'warn', $m['app_secret'] !== '' ? 'Saved: webhook calls are checked and API calls are signed.' : 'Not saved: webhook calls are not checked. Add it from the app’s Settings › Basic.');
			if ($m['page_id'] === '') {
				$add('Facebook page', 'bad', 'No page chosen. Press "Connect and find my pages".');
			} elseif ($m['token'] !== '') {
				list($ok, $p, $err) = $this->graph('GET', $m['page_id'], array('fields' => 'name,access_token'));
				if (!$ok) $add('Facebook page', 'bad', 'The page "' . $m['page_name'] . '" cannot be reached: ' . preg_replace('/^Meta: /', '', $err));
				else $add('Facebook page', !empty($p['access_token']) ? 'ok' : 'warn', '"' . ($p['name'] ?? $m['page_name']) . '"' . (!empty($p['access_token']) ? ', page access works.' : ': no page access token, so posting and lead forms fail. Give the system user full control of the page.'));
				list($ig, $msg) = $this->detect_instagram();
				$m = $this->meta();
				$add('Instagram', $ig ? 'ok' : 'warn', $msg);
				list($ok, $forms, $err) = $this->forms();
				if (!$ok) $add('Lead forms', 'bad', 'Cannot read the lead forms: ' . preg_replace('/^Meta: /', '', $err));
				else {
					$active = 0; $leads = 0;
					foreach ($forms as $f) { if (($f['status'] ?? '') === 'ACTIVE') $active++; $leads += (int)($f['leads_count'] ?? 0); }
					$add('Lead forms', $forms ? 'ok' : 'warn', $forms ? count($forms) . ' forms (' . $active . ' active), ' . $leads . ' leads in Meta so far.' : 'No lead forms on this page yet. Create one in Meta Ads or Meta Business Suite.');
				}
				list($ok, $s, $err) = $this->graph('GET', $m['page_id'] . '/subscribed_apps', array(), 'page');
				$sub = false;
				if ($ok) foreach ((array)($s['data'] ?? array()) as $x) if (($m['app_id'] === '' || (string)($x['id'] ?? '') === (string)$m['app_id']) && in_array('leadgen', (array)($x['subscribed_fields'] ?? array()), true)) $sub = true;
				$add('Lead webhook', $sub ? 'ok' : 'warn', !$ok ? 'Cannot read the page subscription: ' . preg_replace('/^Meta: /', '', $err) : ($sub ? 'The page sends new leads to the app straight away.' : 'The page is not subscribed for leads: press "Send page leads to the CRM". Until then leads come in with the 15-minute sync only.'));
			}
			if ($m['ad_account_id'] === '') {
				list($ok, $a) = $m['token'] !== '' ? $this->graph('GET', 'me/adaccounts', array('fields' => 'account_id,name', 'limit' => 25)) : array(false, null);
				$n = $ok ? count((array)($a['data'] ?? array())) : 0;
				$add('Ad account', 'warn', $n ? $n . ' ad account(s) available: choose one in this account and save to run ads.' : 'The token reaches no ad account, so Meta Ads and the ads autopilot are off. Give the system user access to the ad account in Business Settings.');
			} else {
				list($ok, $a, $err) = $this->graph('GET', $this->act(), array('fields' => 'name,account_status,currency,amount_spent'));
				if (!$ok) $add('Ad account', 'bad', 'The ad account cannot be reached: ' . preg_replace('/^Meta: /', '', $err));
				else $add('Ad account', (int)($a['account_status'] ?? 0) === 1 ? 'ok' : 'warn', '"' . ($a['name'] ?? '') . '" · ' . ($a['currency'] ?? '') . ((int)($a['account_status'] ?? 0) === 1 ? ', active.' : ', not active (status ' . (int)($a['account_status'] ?? 0) . '). Check payment in Meta.'));
			}
			$add('Lead routing', $m['route'] === 'round' ? (count($m['rr_users']) ? 'ok' : 'bad') : ((int)$m['owner_id'] ? 'ok' : 'bad'), $m['route'] === 'round' ? count($m['rr_users']) . ' people in turn.' : ((int)$m['owner_id'] ? 'New leads go to one owner.' : 'No owner chosen: choose who gets the new leads.'));
		}
		if ($ai) {
			$c = $this->claude();
			$order = $this->ai_order();
			if (!$order) $add('AI', 'warn', 'No AI key saved: writing help and the autopilot are off.');
			foreach ($order as $p) {
				list($ok, $t) = $this->test_ai($p);
				$add('AI · ' . self::$ai_providers[$p]['label'], $ok ? 'ok' : 'bad', ($p === $c['provider'] ? 'Main. ' : 'Fallback. ') . $t);
			}
		}
		$g = $this->global_cfg();
		$last = !empty($g['last_cron']) ? strtotime($g['last_cron']) : 0;
		$add('Cron job', $last && $last > time() - 1800 ? 'ok' : 'warn', $last ? 'Last run ' . date('d M Y H:i', $last) . ($last > time() - 1800 ? '.' : ': it has not run for a while. Check the cron job on the server.') : 'Has never run. Add the cron link below to the server, every 5 minutes.');
		return $out;
	}

	/* ---------- Claude autopilot (per Meta account, run by the cron job) ---------- */

	private function lines($text, $https = false)
	{
		$out = array();
		foreach (preg_split('/\r\n|\r|\n/', (string)$text) as $l) {
			$l = trim($l);
			if ($l === '' || ($https && !preg_match('~^https://\S+$~i', $l))) continue;
			$out[] = $l;
		}
		return $out;
	}

	/** Post times for the next 7 days at $time, one a day at most, skipping days in $taken, spread evenly. */
	public static function ap_slots($taken, $n, $time, $now)
	{
		$days = array();
		for ($i = 0; $i < 7; $i++) {
			$d = date('Y-m-d', strtotime("+{$i} day", $now));
			$t = strtotime($d . ' ' . $time);
			if ($t === false || $t < $now + 1800 || in_array($d, $taken, true)) continue;
			$days[] = $t;
		}
		if ($n <= 0 || !$days) return array();
		if ($n >= count($days)) return $days;
		$out = array();
		$step = count($days) / $n;
		for ($k = 0; $k < $n; $k++) $out[] = $days[(int)floor($k * $step)];
		return $out;
	}

	/**
	 * Claude keeps the next 7 days filled with posts for the current account.
	 * 'auto' schedules them, 'review' leaves them for approval. Returns array(made, message).
	 */
	public function ap_plan_posts($force = false)
	{
		$m = $this->meta();
		if (!$m['exists'] || $m['platform'] !== 'meta' || $m['ap_posts'] === 'off') return array(0, 'Post autopilot is off.');
		if (!$force && $m['ap_last_plan'] !== '' && strtotime($m['ap_last_plan']) > time() - 20 * 3600) return array(0, 'Already planned today.');
		$topics = $this->lines($m['ap_topics']);
		if (!$topics) return array(0, 'Add topics for the autopilot first.');
		if (!$this->ai_ready()) return array(0, 'Add an AI key in Connections first.');
		$now = time();
		$up = $this->db->query("SELECT scheduled_at FROM ci_mkt_posts WHERE connection_id = ? AND status IN ('Scheduled','Review') AND scheduled_at BETWEEN ? AND ?",
			array($m['id'], date('Y-m-d H:i:s', $now), date('Y-m-d H:i:s', $now + 7 * 86400)))->result_array();
		$taken = array_map(function ($r) { return substr($r['scheduled_at'], 0, 10); }, $up);
		$want = max(1, min(7, (int)$m['ap_per_week']));
		$slots = self::ap_slots($taken, $want - count($up), preg_match('/^\d{2}:\d{2}$/', $m['ap_time']) ? $m['ap_time'] : '10:00', $now);
		if (!$slots) { $this->save_meta(array('ap_last_plan' => date('Y-m-d H:i:s'))); return array(0, 'The next 7 days already have posts.'); }
		$recent = array_column($this->db->query("SELECT message FROM ci_mkt_posts WHERE connection_id = ? AND message <> '' ORDER BY id DESC LIMIT 8", array($m['id']))->result_array(), 'message');
		// earlier page posts and ads: their text guides the style, their pictures are reused
		$past = array();
		if (!empty($m['ap_reuse'])) {
			list($past) = $this->past_creatives(25);
			foreach (array_slice($past, 0, 6) as $p) if ($p['text'] !== '') $recent[] = $p['text'];
		}
		$ch = array_values(array_intersect(array('fb', 'ig'), (array)$m['ap_channels'])) ?: array('fb');
		$where = in_array('ig', $ch, true) ? (in_array('fb', $ch, true) ? 'Facebook and Instagram' : 'Instagram') : 'Facebook';
		$lang = $m['ap_lang'] === 'ar' ? 'Arabic' : ($m['ap_lang'] === 'both' ? 'English, then the same text in Arabic' : 'English');
		$n = count($slots);
		$prompt = "Write {$n} different {$where} posts in {$lang} for the page \"{$m['page_name']}\". Goal: get people to send their details (call, message or fill the form" . ($m['website'] !== '' ? ', or visit ' . $m['website'] : '') . ").\n\n"
			. "Topics to rotate through (one main topic per post):\n- " . implode("\n- ", $topics) . "\n\n"
			. "Each post: up to 120 words, short paragraphs, 1-3 fitting emojis at most, one clear call to action, then 3-6 relevant hashtags on the last line."
			. ($recent ? "\n\nEarlier posts and ads of this page. Keep a similar voice, but do not repeat them:\n---\n" . implode("\n---\n", array_map(function ($t) { return mb_substr($t, 0, 300); }, $recent)) : '');
		$schema = array('type' => 'object', 'additionalProperties' => false, 'required' => array('posts'), 'properties' => array(
			'posts' => array('type' => 'array', 'items' => array('type' => 'object', 'additionalProperties' => false, 'required' => array('topic', 'text'),
				'properties' => array('topic' => array('type' => 'string'), 'text' => array('type' => 'string'))))));
		list($ok, $raw) = $this->ask_claude($this->claude_system(), $prompt, 8000, $schema);
		if (!$ok) { $this->log('ap_posts', $m['name'] . ': ' . $raw); return array(0, $raw); }
		$j = json_decode($raw, true);
		$posts = is_array($j) && isset($j['posts']) && is_array($j['posts']) ? $j['posts'] : array();
		// pictures: your links first, then (when reuse is on) the pictures of earlier posts and ads
		$imgs = array();
		foreach ($this->lines($m['ap_images'], true) as $u) $imgs[] = array($u, '');
		foreach ($past as $p) if ($p['image_url'] !== '') $imgs[] = array($p['image_url'], $p['ref']);
		$ii = (int)$m['ap_img_next'];
		$made = 0;
		$st = $m['ap_posts'] === 'auto' ? 'Scheduled' : 'Review';
		foreach ($slots as $k => $t) {
			$text = trim((string)($posts[$k]['text'] ?? ''));
			if ($text === '') continue;
			list($img, $iref) = $imgs ? $imgs[$ii++ % count($imgs)] : array('', '');
			$pc = $img === '' ? array_values(array_diff($ch, array('ig'))) : $ch; // Instagram needs an image
			if ($m['ig_id'] === '') $pc = array_values(array_diff($pc, array('ig')));
			if (!$pc) continue;
			$this->db->insert('ci_mkt_posts', array('connection_id' => $m['id'], 'channels' => implode(',', $pc), 'message' => mb_substr($text, 0, 5000), 'image_url' => substr($img, 0, 500), 'image_ref' => $iref,
				'link' => $m['website'], 'status' => $st, 'auto' => 1, 'scheduled_at' => date('Y-m-d H:i:00', $t), 'created_by' => 0, 'created_at' => date('Y-m-d H:i:s')));
			$made++;
		}
		$this->save_meta(array('ap_last_plan' => date('Y-m-d H:i:s'), 'ap_img_next' => $imgs ? $ii % count($imgs) : 0));
		$msg = $made ? $made . ' post' . ($made === 1 ? '' : 's') . ' written' . ($st === 'Review' ? ', waiting for your approval.' : ' and scheduled.') : 'Claude returned no posts.';
		$this->log('ap_posts', $m['name'] . ': ' . $msg);
		return array($made, $msg);
	}

	/**
	 * Safety limits for Claude's campaign changes. Only running campaigns with enough spend; pause or budget changes only (never starts
	 * a campaign); each budget moves at most ap_step % a day; increases are dropped while running budgets would pass ap_max_daily.
	 */
	public static function ap_guard($rows, $actions, $cfg)
	{
		$by = array();
		foreach ($rows as $r) $by[(string)$r['id']] = $r;
		$step = max(5, min(50, (float)$cfg['ap_step'])) / 100;
		$min_spend = (float)$cfg['ap_min_spend'];
		$cap = (float)$cfg['ap_max_daily'];
		$out = array();
		$seen = array();
		foreach ((array)$actions as $a) {
			$id = (string)($a['campaign_id'] ?? '');
			if (!isset($by[$id]) || isset($seen[$id])) continue;
			$seen[$id] = 1;
			$r = $by[$id];
			$act = (string)($a['action'] ?? 'keep');
			$why = mb_substr(trim((string)($a['reason'] ?? '')), 0, 200);
			if ($r['status'] !== 'ACTIVE' || $act === 'keep' || $r['spend'] < $min_spend) continue;
			if ($act === 'pause') { $out[] = array('id' => $id, 'name' => $r['name'], 'action' => 'pause', 'reason' => $why); continue; }
			if ($act !== 'budget' || $r['daily_budget'] === null) continue;
			$old = (float)$r['daily_budget'];
			$new = max($old * (1 - $step), min($old * (1 + $step), (float)($a['daily_budget'] ?? 0)));
			$new = max(5, round($new));
			if (abs($new - $old) < 1) continue;
			$out[] = array('id' => $id, 'name' => $r['name'], 'action' => 'budget', 'old' => $old, 'budget' => $new, 'reason' => $why);
		}
		if ($cap > 0) {
			$paused = $newb = array();
			foreach ($out as $o) { if ($o['action'] === 'pause') $paused[$o['id']] = 1; else $newb[$o['id']] = $o['budget']; }
			$total = 0;
			foreach ($rows as $r) if ($r['status'] === 'ACTIVE' && !isset($paused[(string)$r['id']]) && $r['daily_budget'] !== null) $total += $newb[(string)$r['id']] ?? (float)$r['daily_budget'];
			foreach ($out as $k => $o) {
				if ($total <= $cap) break;
				if ($o['action'] === 'budget' && $o['budget'] > $o['old']) { $total -= $o['budget'] - $o['old']; unset($out[$k]); }
			}
			$out = array_values($out);
		}
		return $out;
	}

	/**
	 * Claude reviews the current account's campaigns (last 7 days). 'auto' applies the changes that pass ap_guard,
	 * 'suggest' only writes them down. Returns array(ok, message).
	 */
	public function ap_review_ads($force = false)
	{
		$m = $this->meta();
		if (!$m['exists'] || $m['platform'] !== 'meta' || $m['ap_ads'] === 'off') return array(false, 'Ads autopilot is off.');
		if (!$force && $m['ap_last_ads'] !== '' && strtotime($m['ap_last_ads']) > time() - 20 * 3600) return array(false, 'Already reviewed today.');
		if (!$this->ai_ready()) return array(false, 'Add an AI key in Connections first.');
		list($ok, $rows, $err) = $this->campaigns('last_7d');
		if (!$ok) return array(false, $err);
		$this->save_meta(array('ap_last_ads' => date('Y-m-d H:i:s')));
		$active = array_values(array_filter($rows, function ($r) { return $r['status'] === 'ACTIVE'; }));
		if (!$active) { $this->save_meta(array('ap_last_report' => date('d M H:i') . ': no running campaigns.')); return array(true, 'No running campaigns to review.'); }
		$cur = $m['currency'];
		$lines = array();
		foreach ($active as $r) {
			$lines[] = sprintf('- id %s | %s | budget %s | spend %s %.2f | %d impressions | %d clicks | CTR %.2f%% | %d leads | CPL %s',
				$r['id'], $r['name'], $r['daily_budget'] !== null ? $cur . ' ' . number_format($r['daily_budget'], 2) . '/day' : 'set on ad sets (cannot change here)', $cur, $r['spend'], $r['impressions'], $r['clicks'], $r['ctr'], $r['leads'], $r['cpl'] !== null ? $cur . ' ' . $r['cpl'] : 'n/a');
		}
		$rules = "Limits: each budget may move at most " . (int)$m['ap_step'] . "% today; all running daily budgets together must stay under {$cur} " . number_format((float)$m['ap_max_daily'], 2)
			. ((float)$m['max_cpl'] > 0 ? "; the target cost per lead is under {$cur} " . number_format((float)$m['max_cpl'], 2) : '')
			. ((float)$m['ap_min_spend'] > 0 ? "; do not judge campaigns that spent under {$cur} " . number_format((float)$m['ap_min_spend'], 2) : '') . '.';
		$prompt = "Running Meta lead campaigns, last 7 days:\n" . implode("\n", $lines) . "\n\n{$rules}\n\n"
			. "For each campaign choose one action: keep, pause (clearly wasting money), or budget (give the new daily budget; raise campaigns with a good cost per lead, lower weak ones). "
			. "When there is too little data, keep. Then write a summary of at most 4 short sentences for the business owner.";
		$schema = array('type' => 'object', 'additionalProperties' => false, 'required' => array('actions', 'summary'), 'properties' => array(
			'summary' => array('type' => 'string'),
			'actions' => array('type' => 'array', 'items' => array('type' => 'object', 'additionalProperties' => false, 'required' => array('campaign_id', 'action', 'daily_budget', 'reason'), 'properties' => array(
				'campaign_id' => array('type' => 'string'), 'action' => array('type' => 'string', 'enum' => array('keep', 'pause', 'budget')),
				'daily_budget' => array('type' => 'number'), 'reason' => array('type' => 'string'))))));
		list($ok, $raw) = $this->ask_claude("You are a careful Meta Ads performance marketer for a B2B company in the UAE. Base decisions only on the numbers given.", $prompt, 8000, $schema);
		if (!$ok) { $this->log('ap_ads', $m['name'] . ': ' . $raw); return array(false, $raw); }
		$j = json_decode($raw, true);
		if (!is_array($j)) return array(false, 'Claude did not return a usable answer.');
		$acts = self::ap_guard($rows, $j['actions'] ?? array(), $m);
		$auto = $m['ap_ads'] === 'auto';
		$done = array();
		foreach ($acts as $a) {
			$what = $a['action'] === 'pause' ? 'pause' : 'budget ' . $cur . ' ' . number_format($a['old'], 0) . ' → ' . number_format($a['budget'], 0) . '/day';
			if ($auto) {
				list($ok2, $msg) = $a['action'] === 'pause' ? $this->set_status($a['id'], 'PAUSED') : $this->set_budget($a['id'], $a['budget']);
				$line = $a['name'] . ': ' . $what . ($ok2 ? '' : ' (failed: ' . $msg . ')') . ($a['reason'] !== '' ? ' — ' . $a['reason'] : '');
				$this->log('ap_ads', $line);
			} else {
				$line = $a['name'] . ': suggest ' . $what . ($a['reason'] !== '' ? ' — ' . $a['reason'] : '');
			}
			$done[] = $line;
		}
		$report = date('d M H:i') . ($auto ? ' · changes made' : ' · suggestions only') . "\n" . trim((string)($j['summary'] ?? '')) . ($done ? "\n- " . implode("\n- ", $done) : "\nNo changes.");
		$this->save_meta(array('ap_last_report' => mb_substr($report, 0, 3000)));
		if (!$auto) $this->log('ap_ads', $m['name'] . ': ' . count($done) . ' suggestion(s)');
		return array(true, $auto ? (count($done) ? count($done) . ' change(s) made.' : 'Reviewed; nothing to change.') : (count($done) ? count($done) . ' suggestion(s) written.' : 'Reviewed; nothing to change.'));
	}
}
