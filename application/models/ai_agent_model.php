<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * AI calling agents: agent profiles, the AI call queue, the voice provider (Vapi, Bland or a custom endpoint)
 * and the results that come back by webhook. Provider keys live only in ci_integration_settings (service ai_voice).
 */
class Ai_agent_model extends CI_Model
{
	private $schema_checked = false;
	private $settings_cache = null;

	public static $languages = array('en' => 'English', 'ar' => 'Arabic', 'multi' => 'English + Arabic (auto)', 'hi' => 'Hindi', 'ur' => 'Urdu');
	public static $voices = array('female' => 'Female', 'male' => 'Male');
	public static $goals = array(
		'book_meeting' => 'Book a meeting',
		'qualify' => 'Qualify interest',
		'callback' => 'Arrange a call back',
		'offer' => 'Send an offer / quote',
		'survey' => 'Survey / feedback'
	);
	public static $providers = array('' => 'Not connected', 'google' => 'Google AI agent (Conversational Agents / Dialogflow CX)', 'vapi' => 'Vapi', 'bland' => 'Bland AI', 'custom' => 'Custom endpoint (n8n, Make, own server)');
	/** Google agent regions (Conversational Agents / Dialogflow CX). */
	public static $google_locations = array('global' => 'Global', 'europe-west1' => 'Europe West 1 (Belgium)', 'europe-west2' => 'Europe West 2 (London)', 'me-central1' => 'Middle East (Doha)', 'me-central2' => 'Middle East (Dammam)', 'me-west1' => 'Middle East (Tel Aviv)', 'asia-south1' => 'Asia South (Mumbai)', 'us-central1' => 'US Central');
	public static $outcomes = array('Meeting booked', 'Interested', 'Call back', 'Not interested', 'No answer', 'Voicemail', 'Wrong number', 'Do not call', 'Failed');
	/** Outcomes that come back to people as follow-ups. */
	public static $hot = array('Meeting booked', 'Interested', 'Call back');

	public function ensure_schema()
	{
		if ($this->schema_checked) return;
		$this->schema_checked = true;
		if (!$this->db->table_exists('ci_ai_agents')) {
			$this->db->query("CREATE TABLE `ci_ai_agents` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`name` VARCHAR(100) NOT NULL,
				`status` VARCHAR(10) NOT NULL DEFAULT 'Draft',
				`language` VARCHAR(10) NOT NULL DEFAULT 'en',
				`voice` VARCHAR(20) NOT NULL DEFAULT 'female',
				`voice_id` VARCHAR(100) NOT NULL DEFAULT '',
				`disclose_ai` TINYINT(1) NOT NULL DEFAULT 1,
				`goal` VARCHAR(20) NOT NULL DEFAULT 'book_meeting',
				`pitch` TEXT NULL,
				`opening` TEXT NULL,
				`questions` TEXT NULL,
				`objections` TEXT NULL,
				`closing` TEXT NULL,
				`transfer_number` VARCHAR(30) NOT NULL DEFAULT '',
				`dnd_on_request` TINYINT(1) NOT NULL DEFAULT 1,
				`max_minutes` INT(11) NOT NULL DEFAULT 5,
				`call_from` CHAR(5) NOT NULL DEFAULT '09:00',
				`call_to` CHAR(5) NOT NULL DEFAULT '18:00',
				`call_days` VARCHAR(20) NOT NULL DEFAULT '1,2,3,4,5',
				`daily_limit` INT(11) NOT NULL DEFAULT 100,
				`created_by` INT(11) NOT NULL,
				`created_at` DATETIME NOT NULL,
				`updated_at` DATETIME NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		}
		if (!$this->db->table_exists('ci_ai_calls')) {
			$this->db->query("CREATE TABLE `ci_ai_calls` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`agent_id` INT(11) NOT NULL,
				`campaign_id` INT(11) NOT NULL DEFAULT 0,
				`lead_id` INT(11) NOT NULL DEFAULT 0,
				`owner_id` INT(11) NOT NULL DEFAULT 0,
				`phone` VARCHAR(30) NOT NULL,
				`contact` VARCHAR(150) NOT NULL DEFAULT '',
				`customer` VARCHAR(255) NOT NULL DEFAULT '',
				`status` VARCHAR(12) NOT NULL DEFAULT 'Queued',
				`outcome` VARCHAR(30) NOT NULL DEFAULT '',
				`provider` VARCHAR(10) NOT NULL DEFAULT '',
				`provider_call_id` VARCHAR(100) NOT NULL DEFAULT '',
				`duration` INT(11) NOT NULL DEFAULT 0,
				`summary` TEXT NULL,
				`transcript` MEDIUMTEXT NULL,
				`sentiment` VARCHAR(20) NOT NULL DEFAULT '',
				`next_action` VARCHAR(255) NOT NULL DEFAULT '',
				`followup_date` DATE NULL,
				`recording_url` VARCHAR(500) NOT NULL DEFAULT '',
				`error` VARCHAR(255) NOT NULL DEFAULT '',
				`is_test` TINYINT(1) NOT NULL DEFAULT 0,
				`handled_by` INT(11) NOT NULL DEFAULT 0,
				`handled_at` DATETIME NULL,
				`queued_by` INT(11) NOT NULL DEFAULT 0,
				`queued_at` DATETIME NOT NULL,
				`started_at` DATETIME NULL,
				`ended_at` DATETIME NULL,
				PRIMARY KEY (`id`),
				KEY `idx_ai_status` (`status`),
				KEY `idx_ai_agent` (`agent_id`),
				KEY `idx_ai_campaign_lead` (`campaign_id`, `lead_id`),
				KEY `idx_ai_owner` (`owner_id`),
				KEY `idx_ai_provider_call` (`provider_call_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		}
		if ($this->db->table_exists('ci_dialer_campaigns') && !$this->db->field_exists('ai_agent_id', 'ci_dialer_campaigns')) {
			$this->db->query("ALTER TABLE `ci_dialer_campaigns` ADD `ai_agent_id` INT(11) NOT NULL DEFAULT 0");
		}
	}

	/* ---------- settings (ci_integration_settings, service ai_voice) ---------- */

	public function settings()
	{
		if ($this->settings_cache !== null) return $this->settings_cache;
		$row = $this->db->where('service_name', 'ai_voice')->get('ci_integration_settings')->row_array();
		$cfg = !empty($row['config_json']) ? json_decode($row['config_json'], true) : array();
		if (!is_array($cfg)) $cfg = array();
		$s = array_merge(array(
			'provider' => '', 'phone_number_id' => '', 'from_number' => '', 'country_code' => '971', 'company' => '7 Strokes',
			'timezone' => 'Asia/Dubai', 'concurrency' => 3, 'webhook_key' => '', 'model' => 'gpt-4o-mini',
			'google_project' => '', 'google_location' => 'global', 'google_agent_id' => '', 'google_language' => 'en'
		), $cfg);
		$s['enabled'] = !empty($row['status']);
		$s['api_key'] = !empty($row['api_key']) ? $row['api_key'] : '';
		// Google service account key (JSON); only used server side to check the agent
		$s['google_sa'] = !empty($row['api_secret']) ? $row['api_secret'] : '';
		$s['endpoint'] = !empty($row['endpoint']) ? $row['endpoint'] : '';
		$s['exists'] = !empty($row);
		return $this->settings_cache = $s;
	}

	/** Ready to place real calls? Returns '' when ready, else the reason. */
	public function not_ready_reason($s = null)
	{
		$s = $s ?: $this->settings();
		if ($s['provider'] === '') return 'No voice provider is connected.';
		if (!$s['enabled']) return 'AI calling is switched off in Settings.';
		if ($s['provider'] === 'google') {
			if ($s['google_project'] === '' || $s['google_agent_id'] === '') return 'The Google project ID and agent ID are missing.';
			if (!preg_match('~^https://~i', $s['endpoint'])) return 'The Google calling bridge must be an https:// address.';
			return '';
		}
		if ($s['provider'] !== 'custom' && $s['api_key'] === '') return 'The provider API key is missing.';
		if ($s['provider'] === 'vapi' && $s['phone_number_id'] === '') return 'The Vapi phone number ID is missing.';
		if ($s['provider'] === 'custom' && !preg_match('~^https://~i', $s['endpoint'])) return 'The custom endpoint must be an https:// address.';
		return '';
	}

	public function save_settings($in, $api_key, $google_sa = '')
	{
		$old = $this->settings();
		$in = array_merge(array('google_project' => $old['google_project'], 'google_location' => $old['google_location'], 'google_agent_id' => $old['google_agent_id'], 'google_language' => $old['google_language']), $in);
		$cfg = array(
			'provider' => isset(self::$providers[$in['provider']]) ? $in['provider'] : '',
			'phone_number_id' => substr(trim($in['phone_number_id']), 0, 100),
			'from_number' => $this->e164($in['from_number'], preg_replace('/\D/', '', $in['country_code']) ?: '971'),
			'country_code' => substr(preg_replace('/\D/', '', $in['country_code']), 0, 4) ?: '971',
			'company' => substr(trim($in['company']), 0, 100) ?: '7 Strokes',
			'timezone' => in_array($in['timezone'], timezone_identifiers_list(), true) ? $in['timezone'] : 'Asia/Dubai',
			'concurrency' => max(1, min(20, (int)$in['concurrency'])),
			'model' => preg_match('~^[\w.\-]{2,60}$~', $in['model']) ? $in['model'] : 'gpt-4o-mini',
			'webhook_key' => $old['webhook_key'] !== '' ? $old['webhook_key'] : bin2hex(random_bytes(16)),
			'google_project' => preg_match('~^[a-z][a-z0-9\-]{4,62}$~', trim($in['google_project'])) ? trim($in['google_project']) : '',
			'google_location' => isset(self::$google_locations[$in['google_location']]) ? $in['google_location'] : 'global',
			'google_agent_id' => preg_match('~^[\w\-]{8,64}$~', trim($in['google_agent_id'])) ? trim($in['google_agent_id']) : '',
			'google_language' => preg_match('~^[a-z]{2}(-[A-Z]{2})?$~', trim($in['google_language'])) ? trim($in['google_language']) : 'en'
		);
		if ($in['from_number'] === '') $cfg['from_number'] = '';
		$row = array(
			'endpoint' => substr(trim($in['endpoint']), 0, 255),
			'config_json' => json_encode($cfg),
			'status' => $in['enabled'] ? 1 : 0,
			'updated_at' => date('Y-m-d H:i:s')
		);
		if ($api_key !== '') $row['api_key'] = $api_key;
		if ($google_sa !== '') $row['api_secret'] = $google_sa;
		if ($old['exists']) {
			$this->db->where('service_name', 'ai_voice')->update('ci_integration_settings', $row);
		} else {
			$row['service_name'] = 'ai_voice';
			$row['api_secret'] = '';
			if (!isset($row['api_key'])) $row['api_key'] = '';
			$row['created_at'] = $row['updated_at'];
			$this->db->insert('ci_integration_settings', $row);
		}
		$this->settings_cache = null;
	}

	public function renew_webhook_key()
	{
		$s = $this->settings();
		if (!$s['exists']) $this->save_settings(array('provider' => '', 'phone_number_id' => '', 'from_number' => '', 'country_code' => '971', 'company' => '7 Strokes', 'timezone' => 'Asia/Dubai', 'concurrency' => 3, 'model' => 'gpt-4o-mini', 'endpoint' => '', 'enabled' => 0), '');
		$row = $this->db->where('service_name', 'ai_voice')->get('ci_integration_settings')->row_array();
		$cfg = json_decode($row['config_json'], true) ?: array();
		$cfg['webhook_key'] = bin2hex(random_bytes(16));
		$this->db->where('service_name', 'ai_voice')->update('ci_integration_settings', array('config_json' => json_encode($cfg)));
		$this->settings_cache = null;
		return $cfg['webhook_key'];
	}

	public function webhook_key()
	{
		$s = $this->settings();
		return $s['webhook_key'] !== '' ? $s['webhook_key'] : $this->renew_webhook_key();
	}

	/* ---------- agents ---------- */

	public function agents()
	{
		$this->ensure_schema();
		$rows = $this->db->query("SELECT * FROM ci_ai_agents ORDER BY FIELD(status, 'Active', 'Draft', 'Paused'), name ASC")->result_array();
		if (!$rows) return array();
		$ids = array_map(function ($r) { return (int)$r['id']; }, $rows);
		$week = date('Y-m-d H:i:s', strtotime('-7 days'));
		$hot = "'" . implode("','", self::$hot) . "'";
		$stats = array();
		foreach ($this->db->query("SELECT agent_id, COUNT(*) calls, SUM(duration > 0 AND outcome NOT IN ('Voicemail', 'No answer', 'Failed')) talked, SUM(outcome IN ({$hot})) hot, SUM(status = 'Queued') queued
			FROM ci_ai_calls WHERE agent_id IN (" . implode(',', $ids) . ") AND is_test = 0 AND (queued_at >= '{$week}' OR status = 'Queued') GROUP BY agent_id")->result_array() as $r) {
			$stats[(int)$r['agent_id']] = $r;
		}
		$camps = array();
		foreach ($this->db->select('id, name, ai_agent_id')->where_in('ai_agent_id', $ids)->where('status !=', 'Completed')->get('ci_dialer_campaigns')->result_array() as $c) {
			$camps[(int)$c['ai_agent_id']][] = $c;
		}
		foreach ($rows as &$r) {
			$st = isset($stats[(int)$r['id']]) ? $stats[(int)$r['id']] : array();
			$r['calls7'] = (int)(isset($st['calls']) ? $st['calls'] : 0) - (int)(isset($st['queued']) ? $st['queued'] : 0);
			$r['talked7'] = (int)(isset($st['talked']) ? $st['talked'] : 0);
			$r['hot7'] = (int)(isset($st['hot']) ? $st['hot'] : 0);
			$r['queued'] = (int)(isset($st['queued']) ? $st['queued'] : 0);
			$r['campaigns'] = isset($camps[(int)$r['id']]) ? $camps[(int)$r['id']] : array();
		}
		return $rows;
	}

	public function agent($id)
	{
		$this->ensure_schema();
		return $this->db->where('id', (int)$id)->get('ci_ai_agents')->row_array() ?: null;
	}

	public function save_agent($data, $id = 0)
	{
		$data['updated_at'] = date('Y-m-d H:i:s');
		if ((int)$id > 0) {
			$this->db->where('id', (int)$id)->update('ci_ai_agents', $data);
			return (int)$id;
		}
		$data['created_at'] = $data['updated_at'];
		$this->db->insert('ci_ai_agents', $data);
		return (int)$this->db->insert_id();
	}

	/** The instructions the voice AI gets for one call. */
	public function build_prompt($a, $call = array())
	{
		$s = $this->settings();
		$company = $s['company'];
		$fill = function ($t) use ($a, $call, $company) {
			return strtr((string)$t, array(
				'{contact}' => !empty($call['contact']) ? $call['contact'] : 'the customer',
				'{customer}' => !empty($call['customer']) ? $call['customer'] : 'their company',
				'{agent}' => $a['name'], '{company}' => $company
			));
		};
		$lang = array('en' => 'English', 'ar' => 'Arabic (Gulf dialect where natural)', 'multi' => 'English; switch to Arabic if the customer speaks Arabic', 'hi' => 'Hindi; switch to English if the customer prefers', 'ur' => 'Urdu; switch to English if the customer prefers');
		$goal = array(
			'book_meeting' => 'Book a meeting with our sales team. Agree a day and time and confirm it back.',
			'qualify' => 'Find out whether they need our service now, their timeline and who decides. Do not push for a sale.',
			'callback' => 'Arrange a convenient time for a sales person to call them back.',
			'offer' => 'Get agreement to send an offer or quote, and confirm the email address to send it to.',
			'survey' => 'Ask the questions below and record the answers politely. Do not sell.'
		);
		$lines = array();
		$lines[] = "You are {$a['name']}, calling on behalf of {$company}. You are on a live phone call with {$fill('{contact}')} from {$fill('{customer}')}.";
		if (!empty($a['disclose_ai'])) $lines[] = 'At the start, say plainly that you are an AI assistant calling for ' . $company . '. If asked, never pretend to be human.';
		$lines[] = 'Language: ' . (isset($lang[$a['language']]) ? $lang[$a['language']] : 'English') . '.';
		$lines[] = 'Goal: ' . (isset($goal[$a['goal']]) ? $goal[$a['goal']] : $goal['qualify']);
		if (trim((string)$a['pitch']) !== '') $lines[] = "What we offer:\n" . $fill($a['pitch']);
		if (trim((string)$a['questions']) !== '') $lines[] = "Questions to ask, one at a time:\n" . $fill($a['questions']);
		if (trim((string)$a['objections']) !== '') $lines[] = "How to answer objections:\n" . $fill($a['objections']);
		if (trim((string)$a['closing']) !== '') $lines[] = "Closing:\n" . $fill($a['closing']);
		$rules = array(
			'Speak naturally in short sentences. Ask one question at a time and listen.',
			'Never invent prices, discounts, dates or promises. If you do not know, say a colleague will follow up.',
			'If the person asks not to be called again, apologise, confirm they will not be called again, and end the call.',
			'If it is the wrong number or the person is busy, apologise and end the call politely.',
			'Keep the call under ' . max(1, (int)$a['max_minutes']) . ' minutes.'
		);
		if (trim((string)$a['transfer_number']) !== '') $rules[] = 'If the customer is interested and wants to speak to a person now, transfer the call.';
		$lines[] = "Rules:\n- " . implode("\n- ", $rules);
		return implode("\n\n", $lines);
	}

	public function first_message($a, $call = array())
	{
		$s = $this->settings();
		$t = trim((string)$a['opening']) !== '' ? $a['opening'] : 'Hello, is this {contact}? This is {agent} from {company}.';
		return strtr($t, array(
			'{contact}' => !empty($call['contact']) ? $call['contact'] : 'there',
			'{customer}' => !empty($call['customer']) ? $call['customer'] : 'your company',
			'{agent}' => $a['name'], '{company}' => $s['company']
		));
	}

	/** Is it inside the agent's calling hours right now (in the configured time zone)? */
	public function in_hours($a)
	{
		$s = $this->settings();
		try { $now = new DateTime('now', new DateTimeZone($s['timezone'])); } catch (Exception $e) { $now = new DateTime(); }
		$days = array_filter(array_map('intval', explode(',', (string)$a['call_days'])));
		if (!in_array((int)$now->format('N'), $days, true)) return false;
		$hm = $now->format('H:i');
		return $hm >= $a['call_from'] && $hm < $a['call_to'];
	}

	/* ---------- phone numbers ---------- */

	public function e164($raw, $cc = '971')
	{
		$raw = trim((string)$raw);
		if ($raw === '') return '';
		$plus = strpos($raw, '+') === 0;
		$d = preg_replace('/\D/', '', $raw);
		if ($d === '') return '';
		if ($plus) return '+' . $d;
		if (strpos($d, '00') === 0) return '+' . substr($d, 2);
		if (strpos($d, $cc) === 0 && strlen($d) > 9) return '+' . $d;
		if (strpos($d, '0') === 0) $d = substr($d, 1);
		return '+' . $cc . $d;
	}

	private function lead_phone($l, $cc)
	{
		foreach (array('mobile', 'contact1', 'alt_contact', 'contact2', 'alt_mobile') as $k) {
			if (!empty($l[$k])) {
				$p = $this->e164(preg_split('~[,/;]~', $l[$k])[0], $cc);
				if (strlen($p) >= 9 && strlen($p) <= 16) return $p;
			}
		}
		return '';
	}

	/* ---------- queue ---------- */

	/** Puts up to $limit campaign records in the AI queue. Skips DND, records already queued, and the campaign's retry rules. */
	public function queue_campaign($campaign, $agent, $limit, $uid)
	{
		$this->ensure_schema();
		$M = $this->dialer_campaign_model;
		$cid = (int)$campaign['id'];
		$s = $this->settings();
		$skip = array();
		$rows = $this->db->query("SELECT lead_id, COUNT(*) n, MAX(COALESCE(ended_at, queued_at)) last, SUM(status IN ('Queued', 'Calling')) open
			FROM ci_ai_calls WHERE campaign_id = {$cid} AND is_test = 0 AND status != 'Cancelled' GROUP BY lead_id")->result_array();
		$max = (int)$campaign['max_attempts'];
		$wait = time() - (int)$campaign['retry_hours'] * 3600;
		foreach ($rows as $r) {
			if ((int)$r['open'] > 0 || ($max > 0 && (int)$r['n'] >= $max) || ((int)$campaign['retry_hours'] > 0 && strtotime($r['last']) > $wait)) $skip[(int)$r['lead_id']] = true;
		}
		// Human calls in the campaign count as tries too
		foreach ($M->attempts_map($cid) as $lid => $a) {
			if (($max > 0 && $a[0] >= $max) || ((int)$campaign['retry_hours'] > 0 && strtotime($a[1]) > $wait)) $skip[$lid] = true;
		}
		$conds = array();
		foreach ($M->participant_ids($campaign) as $pid) $conds[] = "A.lead_assign = {$pid} OR FIND_IN_SET({$pid}, A.agents) > 0";
		$this->db->select('A.id, A.lead_assign, A.customer, A.poc, A.mobile, A.contact1, A.alt_contact, A.contact2, A.alt_mobile');
		$this->db->from('ci_leads A');
		$this->db->join('ci_pipeline P', 'P.lead_id = A.id', 'left');
		$this->db->where('(' . implode(' OR ', $conds) . ')', null, false);
		$this->db->where('(A.contact1 != "" OR A.alt_contact != "" OR A.mobile != "" OR A.contact2 != "")', null, false);
		$this->db->where('(A.dnd_status IS NULL OR LOWER(A.dnd_status) NOT IN ("dncr", "dnd", "yes", "y"))', null, false);
		$this->db->where('A.status !=', 46);
		$M->apply_filters($campaign);
		$this->db->order_by('A.id', 'ASC');
		$now = date('Y-m-d H:i:s');
		$added = 0;
		$no_phone = 0;
		foreach ($this->db->get()->result_array() as $l) {
			if ($added >= $limit) break;
			if (isset($skip[(int)$l['id']])) continue;
			$phone = $this->lead_phone($l, $s['country_code']);
			if ($phone === '') { $no_phone++; continue; }
			$this->db->insert('ci_ai_calls', array(
				'agent_id' => (int)$agent['id'], 'campaign_id' => $cid, 'lead_id' => (int)$l['id'], 'owner_id' => (int)$l['lead_assign'],
				'phone' => $phone, 'contact' => mb_substr(trim((string)$l['poc']), 0, 150), 'customer' => mb_substr(trim((string)$l['customer']), 0, 255),
				'status' => 'Queued', 'queued_by' => (int)$uid, 'queued_at' => $now
			));
			$added++;
		}
		return array('added' => $added, 'skipped' => count($skip), 'no_phone' => $no_phone);
	}

	public function cancel_queue($where)
	{
		$this->db->where($where)->where('status', 'Queued')->update('ci_ai_calls', array('status' => 'Cancelled', 'ended_at' => date('Y-m-d H:i:s')));
		return $this->db->affected_rows();
	}

	public function campaign_ai_stats($cid)
	{
		$hot = "'" . implode("','", self::$hot) . "'";
		$r = $this->db->query("SELECT SUM(status = 'Queued') queued, SUM(status = 'Calling') calling, SUM(status IN ('Completed', 'Failed')) done,
			SUM(duration > 0 AND outcome NOT IN ('Voicemail', 'No answer', 'Failed')) talked, SUM(outcome IN ({$hot})) hot, SUM(outcome = 'Do not call') dnd
			FROM ci_ai_calls WHERE campaign_id = " . (int)$cid . " AND is_test = 0")->row_array();
		return array_map('intval', $r ?: array());
	}

	/* ---------- dispatch to the provider ---------- */

	/** Starts queued calls within calling hours, daily limits and the concurrency cap. Returns the number started. */
	public function dispatch($max_start = 10)
	{
		$this->ensure_schema();
		$s = $this->settings();
		// Calls with no result long after they started are closed
		$this->db->query("UPDATE ci_ai_calls SET status = 'Failed', outcome = 'Failed', error = 'No result came back from the provider', ended_at = '" . date('Y-m-d H:i:s') . "'
			WHERE status = 'Calling' AND started_at < '" . date('Y-m-d H:i:s', strtotime('-45 minutes')) . "'");
		if ($this->not_ready_reason($s) !== '') return 0;
		$running = (int)$this->db->where('status', 'Calling')->count_all_results('ci_ai_calls');
		$free = min($max_start, (int)$s['concurrency'] - $running);
		if ($free <= 0) return 0;
		$started = 0;
		$today = date('Y-m-d 00:00:00');
		foreach ($this->db->where('status', 'Active')->get('ci_ai_agents')->result_array() as $a) {
			if ($started >= $free) break;
			if (!$this->in_hours($a)) continue;
			$used = (int)$this->db->where('agent_id', (int)$a['id'])->where('is_test', 0)->where('started_at >=', $today)->count_all_results('ci_ai_calls');
			$room = min($free - $started, (int)$a['daily_limit'] - $used);
			if ($room <= 0) continue;
			$q = $this->db->query("SELECT Q.* FROM ci_ai_calls Q LEFT JOIN ci_dialer_campaigns C ON C.id = Q.campaign_id
				WHERE Q.agent_id = " . (int)$a['id'] . " AND Q.status = 'Queued'
				AND (Q.campaign_id = 0 OR (C.status = 'Active' AND (C.start_date IS NULL OR C.start_date <= '" . date('Y-m-d') . "') AND (C.end_date IS NULL OR C.end_date >= '" . date('Y-m-d') . "')))
				ORDER BY FIELD(C.priority, 'High', 'Normal', 'Low'), Q.id ASC LIMIT " . (int)$room)->result_array();
			foreach ($q as $call) {
				// claim the row so two dispatchers never start the same call
				$this->db->where('id', (int)$call['id'])->where('status', 'Queued')->update('ci_ai_calls', array('status' => 'Calling', 'provider' => $s['provider'], 'started_at' => date('Y-m-d H:i:s')));
				if ($this->db->affected_rows() !== 1) continue;
				$this->start_call($a, $call, $s);
				$started++;
			}
		}
		return $started;
	}

	/** Places one call with the provider. Updates the row with the provider call id, or marks it Failed. */
	public function start_call($a, $call, $s = null)
	{
		$s = $s ?: $this->settings();
		$hook = base_url() . 'api/ai-calls/webhook?key=' . rawurlencode($this->webhook_key());
		$meta = array('crm_call_id' => (int)$call['id'], 'lead_id' => (int)$call['lead_id'], 'campaign_id' => (int)$call['campaign_id']);
		$prompt = $this->build_prompt($a, $call);
		$first = $this->first_message($a, $call);
		$lang = $a['language'] === 'multi' ? 'multi' : $a['language'];
		$url = '';
		$headers = array('Content-Type: application/json');
		$body = array();
		if ($s['provider'] === 'vapi') {
			$url = 'https://api.vapi.ai/call';
			$headers[] = 'Authorization: Bearer ' . $s['api_key'];
			$model = array('provider' => 'openai', 'model' => $s['model'], 'messages' => array(array('role' => 'system', 'content' => $prompt)));
			if ($a['transfer_number'] !== '') {
				$model['tools'] = array(array('type' => 'transferCall', 'destinations' => array(array('type' => 'number', 'number' => $this->e164($a['transfer_number'], $s['country_code'])))));
			}
			$assistant = array(
				'name' => mb_substr($a['name'], 0, 40), 'firstMessage' => $first, 'model' => $model,
				'transcriber' => array('provider' => 'deepgram', 'model' => 'nova-2', 'language' => $lang === 'multi' ? 'multi' : $lang),
				'maxDurationSeconds' => max(60, (int)$a['max_minutes'] * 60), 'endCallFunctionEnabled' => true,
				'voicemailDetection' => array('provider' => 'vapi'),
				'server' => array('url' => $hook), 'serverMessages' => array('end-of-call-report'), 'metadata' => $meta
			);
			if ($a['voice_id'] !== '') $assistant['voice'] = array('provider' => '11labs', 'voiceId' => $a['voice_id']);
			$body = array('phoneNumberId' => $s['phone_number_id'], 'customer' => array('number' => $call['phone'], 'name' => mb_substr($call['contact'] ?: $call['customer'], 0, 40)), 'assistant' => $assistant);
		} elseif ($s['provider'] === 'bland') {
			$url = 'https://api.bland.ai/v1/calls';
			$headers[] = 'authorization: ' . $s['api_key'];
			$body = array(
				'phone_number' => $call['phone'], 'task' => $prompt, 'first_sentence' => $first, 'wait_for_greeting' => true, 'record' => true,
				'language' => $lang === 'multi' ? 'en' : $lang, 'max_duration' => max(1, (int)$a['max_minutes']), 'webhook' => $hook, 'metadata' => $meta,
				'answered_by_enabled' => true
			);
			$body['voice'] = $a['voice_id'] !== '' ? $a['voice_id'] : ($a['voice'] === 'male' ? 'josh' : 'maya');
			if ($s['from_number'] !== '') $body['from'] = $s['from_number'];
			if ($a['transfer_number'] !== '') $body['transfer_phone_number'] = $this->e164($a['transfer_number'], $s['country_code']);
		} elseif ($s['provider'] === 'google') {
			// Google agents answer calls but do not dial out: the calling bridge (3CX SIP / telephony partner / Cloud Run)
			// places the call and connects it to the agent with this session id and these parameters.
			$url = $s['endpoint'];
			if ($s['api_key'] !== '') $headers[] = 'Authorization: Bearer ' . $s['api_key'];
			$agent_path = 'projects/' . $s['google_project'] . '/locations/' . $s['google_location'] . '/agents/' . $s['google_agent_id'];
			$body = array(
				'event' => 'start_call', 'crm_call_id' => (int)$call['id'], 'phone' => $call['phone'],
				'google' => array(
					'agent' => $agent_path,
					'session' => $agent_path . '/sessions/crm-' . (int)$call['id'],
					'language_code' => $this->google_lang($a, $s),
					'webhook_url' => $hook
				),
				'parameters' => $this->google_params($a, $call),
				'prompt' => $prompt, 'first_message' => $first, 'webhook_url' => $hook, 'metadata' => $meta
			);
		} else {
			$url = $s['endpoint'];
			if ($s['api_key'] !== '') $headers[] = 'Authorization: Bearer ' . $s['api_key'];
			$body = array(
				'event' => 'start_call', 'crm_call_id' => (int)$call['id'], 'phone' => $call['phone'], 'contact' => $call['contact'], 'customer' => $call['customer'],
				'agent' => array('name' => $a['name'], 'language' => $a['language'], 'voice' => $a['voice'], 'voice_id' => $a['voice_id'], 'max_minutes' => (int)$a['max_minutes'],
					'transfer_number' => $a['transfer_number'] !== '' ? $this->e164($a['transfer_number'], $s['country_code']) : ''),
				'prompt' => $prompt, 'first_message' => $first, 'webhook_url' => $hook, 'metadata' => $meta
			);
		}
		list($code, $res, $err) = $this->http($url, $headers, $body);
		$id = '';
		if (is_array($res)) {
			foreach (array('id', 'call_id', 'callId') as $k) if (!empty($res[$k]) && is_scalar($res[$k])) { $id = (string)$res[$k]; break; }
		}
		if ($code >= 200 && $code < 300 && ($id !== '' || in_array($s['provider'], array('custom', 'google'), true))) {
			$this->db->where('id', (int)$call['id'])->update('ci_ai_calls', array('provider_call_id' => substr($id, 0, 100)));
			return array(true, '');
		}
		$msg = $err ?: (is_array($res) ? (isset($res['message']) ? (is_array($res['message']) ? implode('; ', $res['message']) : $res['message']) : (isset($res['error']) ? (is_array($res['error']) ? json_encode($res['error']) : $res['error']) : 'HTTP ' . $code)) : 'HTTP ' . $code);
		$this->db->where('id', (int)$call['id'])->update('ci_ai_calls', array('status' => 'Failed', 'outcome' => 'Failed', 'error' => mb_substr('Provider: ' . $msg, 0, 255), 'ended_at' => date('Y-m-d H:i:s')));
		return array(false, $msg);
	}

	/** Certificate bundle for HTTPS checks when PHP has none configured (e.g. XAMPP under Apache). */
	private function ca_opts()
	{
		$ini = ini_get('curl.cainfo');
		if ($ini && is_file($ini)) return array();
		foreach (array(dirname(PHP_BINARY) . '/../apache/bin/curl-ca-bundle.crt', 'C:/xampp/apache/bin/curl-ca-bundle.crt', '/etc/ssl/certs/ca-certificates.crt', '/etc/pki/tls/certs/ca-bundle.crt') as $p) {
			if (is_file($p)) return array(CURLOPT_CAINFO => $p);
		}
		return array();
	}

	private function http($url, $headers, $body = null, $method = 'POST')
	{
		$ch = curl_init($url);
		curl_setopt_array($ch, $this->ca_opts());
		curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 20, CURLOPT_CONNECTTIMEOUT => 8, CURLOPT_HTTPHEADER => $headers, CURLOPT_CUSTOMREQUEST => $method));
		if ($body !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
		$raw = curl_exec($ch);
		$code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$err = curl_error($ch);
		curl_close($ch);
		return array($code, json_decode((string)$raw, true), $err);
	}

	/** Checks the API key with a read-only request. Returns array(ok, message). */
	public function test_connection()
	{
		$s = $this->settings();
		if ($s['provider'] === 'vapi') {
			list($code, $res, $err) = $this->http('https://api.vapi.ai/phone-number', array('Authorization: Bearer ' . $s['api_key']), null, 'GET');
			if ($code === 200 && is_array($res)) {
				$found = false;
				foreach ($res as $p) if (isset($p['id']) && $p['id'] === $s['phone_number_id']) $found = true;
				return array($found, $found ? 'Connected. The phone number ID was found.' : 'The key works, but the phone number ID was not found in this Vapi account.');
			}
			return array(false, $err ?: 'Vapi refused the key (HTTP ' . $code . ').');
		}
		if ($s['provider'] === 'bland') {
			list($code, $res, $err) = $this->http('https://api.bland.ai/v1/me', array('authorization: ' . $s['api_key']), null, 'GET');
			return $code === 200 ? array(true, 'Connected to Bland AI.') : array(false, $err ?: 'Bland AI refused the key (HTTP ' . $code . ').');
		}
		if ($s['provider'] === 'google') {
			if ($s['google_sa'] === '') return array(false, 'Add the service account key to check the Google agent.');
			list($token, $err) = $this->google_token($s['google_sa']);
			if (!$token) return array(false, $err);
			$host = $s['google_location'] === 'global' ? 'dialogflow.googleapis.com' : $s['google_location'] . '-dialogflow.googleapis.com';
			$url = 'https://' . $host . '/v3/projects/' . rawurlencode($s['google_project']) . '/locations/' . rawurlencode($s['google_location']) . '/agents/' . rawurlencode($s['google_agent_id']);
			list($code, $res, $err) = $this->http($url, array('Authorization: Bearer ' . $token), null, 'GET');
			if ($code === 200 && !empty($res['displayName'])) return array(true, 'Connected to the Google agent "' . $res['displayName'] . '". Calls go through the calling bridge.');
			$msg = isset($res['error']['message']) ? $res['error']['message'] : ($err ?: 'HTTP ' . $code);
			return array(false, 'Google: ' . $msg);
		}
		if ($s['provider'] === 'custom') return array(true, 'Custom endpoints are not checked. Place a test call to try it.');
		return array(false, 'Choose a provider first.');
	}

	/* ---------- Google AI agent ---------- */

	private function google_lang($a, $s)
	{
		$map = array('en' => 'en', 'ar' => 'ar', 'hi' => 'hi', 'ur' => 'ur', 'multi' => $s['google_language']);
		return isset($map[$a['language']]) ? $map[$a['language']] : $s['google_language'];
	}

	/** Session parameters the Google agent can use in its flows ($session.params.contact etc.). */
	public function google_params($a, $call)
	{
		$s = $this->settings();
		return array(
			'crm_call_id' => (int)$call['id'], 'contact' => (string)$call['contact'], 'customer' => (string)$call['customer'],
			'agent_name' => $a['name'], 'company' => $s['company'], 'goal' => isset(self::$goals[$a['goal']]) ? self::$goals[$a['goal']] : $a['goal'],
			'opening' => $this->first_message($a, $call), 'pitch' => (string)$a['pitch'], 'questions' => (string)$a['questions'],
			'objections' => (string)$a['objections'], 'closing' => (string)$a['closing'], 'disclose_ai' => (bool)$a['disclose_ai'],
			'transfer_number' => $a['transfer_number'] !== '' ? $this->e164($a['transfer_number'], $s['country_code']) : '',
			'max_minutes' => (int)$a['max_minutes'], 'instructions' => $this->build_prompt($a, $call)
		);
	}

	/** OAuth access token from a service account key (JWT bearer flow). Returns array(token, error). */
	private function google_token($sa_json)
	{
		$sa = json_decode($sa_json, true);
		if (empty($sa['client_email']) || empty($sa['private_key'])) return array('', 'The service account key is not valid JSON from Google Cloud.');
		$b64 = function ($d) { return rtrim(strtr(base64_encode($d), '+/', '-_'), '='); };
		$now = time();
		$head = $b64(json_encode(array('alg' => 'RS256', 'typ' => 'JWT')));
		$claim = $b64(json_encode(array('iss' => $sa['client_email'], 'scope' => 'https://www.googleapis.com/auth/dialogflow', 'aud' => 'https://oauth2.googleapis.com/token', 'iat' => $now, 'exp' => $now + 3600)));
		$sig = '';
		if (!openssl_sign($head . '.' . $claim, $sig, $sa['private_key'], 'sha256WithRSAEncryption')) return array('', 'The service account private key could not be read.');
		$ch = curl_init('https://oauth2.googleapis.com/token');
		curl_setopt_array($ch, $this->ca_opts());
		curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15, CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => http_build_query(array('grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer', 'assertion' => $head . '.' . $claim . '.' . $b64($sig)))));
		$res = json_decode((string)curl_exec($ch), true);
		$err = curl_error($ch);
		curl_close($ch);
		if (!empty($res['access_token'])) return array($res['access_token'], '');
		return array('', 'Google refused the service account: ' . (isset($res['error_description']) ? $res['error_description'] : ($err ?: 'no token')));
	}

	/** Free-text result from an agent ("customer agreed to a meeting") to one of our outcomes. */
	public function map_outcome($t)
	{
		$t = strtolower(trim((string)$t));
		if ($t === '') return '';
		foreach (self::$outcomes as $o) if ($t === strtolower($o)) return $o;
		$rules = array(
			'Do not call' => '~do.?not.?call|dnc|dnd|stop calling|remove~', 'Wrong number' => '~wrong~', 'Voicemail' => '~voice.?mail|machine~',
			'No answer' => '~no.?answer|busy|unreachable|not reachable~', 'Not interested' => '~not.?interest|declin|reject|no need~',
			'Meeting booked' => '~meeting|appointment|demo|visit~', 'Call back' => '~call.?back|later|follow~', 'Interested' => '~interest|quote|offer|yes~',
			'Failed' => '~fail|error~'
		);
		foreach ($rules as $o => $re) if (preg_match($re, $t)) return $o;
		return '';
	}

	/**
	 * Webhook from a Google agent (Dialogflow CX WebhookRequest). Tags:
	 *  crm_context - returns the customer and pitch as session parameters
	 *  call_end    - saves outcome / summary / transcript / duration / recording_url from the session parameters
	 * Always returns a valid WebhookResponse.
	 */
	public function dialogflow_webhook($p)
	{
		$empty = array('fulfillmentResponse' => array('messages' => array()));
		$tag = isset($p['fulfillmentInfo']['tag']) ? (string)$p['fulfillmentInfo']['tag'] : '';
		$prm = isset($p['sessionInfo']['parameters']) && is_array($p['sessionInfo']['parameters']) ? $p['sessionInfo']['parameters'] : array();
		$session = isset($p['sessionInfo']['session']) ? (string)$p['sessionInfo']['session'] : '';
		$id = !empty($prm['crm_call_id']) ? (int)$prm['crm_call_id'] : (preg_match('~/sessions/crm-(\d+)$~', $session, $m) ? (int)$m[1] : 0);
		if ($id <= 0) return $empty;
		$row = $this->db->where('id', $id)->get('ci_ai_calls')->row_array();
		// only calls placed through Google, and the session must belong to that call
		if (!$row || $row['provider'] !== 'google' || ($session !== '' && !preg_match('~/sessions/crm-' . $id . '$~', $session))) return $empty;
		$a = $this->agent($row['agent_id']);
		if ($tag === 'crm_context' && $a) {
			return array('sessionInfo' => array('parameters' => $this->google_params($a, $row)), 'fulfillmentResponse' => array('messages' => array()));
		}
		if (in_array($tag, array('call_end', 'end_call', 'crm_result'), true)) {
			$tr = isset($prm['transcript']) ? $prm['transcript'] : '';
			$f = array(
				'status' => 'Completed', 'duration' => isset($prm['duration']) ? (int)$prm['duration'] : ($row['started_at'] ? max(0, time() - strtotime($row['started_at'])) : 0),
				'transcript' => is_array($tr) ? json_encode($tr) : (string)$tr, 'summary' => isset($prm['summary']) ? (string)$prm['summary'] : '',
				'recording_url' => isset($prm['recording_url']) ? (string)$prm['recording_url'] : '',
				'outcome' => $this->map_outcome(isset($prm['outcome']) ? $prm['outcome'] : ''), 'error' => ''
			);
			if ($f['outcome'] === 'Failed') { $f['status'] = 'Failed'; $f['error'] = 'Reported by the Google agent'; }
			if (!empty($prm['followup_date']) && strtotime($prm['followup_date'])) $f['followup_date'] = date('Y-m-d', strtotime($prm['followup_date']));
			$this->finish_call($row, $f);
		}
		return $empty;
	}

	/* ---------- results from the provider ---------- */

	/** Turns a provider webhook into our fields. Returns array(row or null, fields) or null to ignore. */
	public function parse_webhook($p)
	{
		if (!is_array($p)) return null;
		$f = array('status' => 'Completed', 'duration' => 0, 'transcript' => '', 'summary' => '', 'recording_url' => '', 'outcome' => '', 'error' => '');
		$our = 0;
		$pid = '';
		if (isset($p['message']) && is_array($p['message'])) {
			// Vapi server message
			$m = $p['message'];
			if (!isset($m['type']) || $m['type'] !== 'end-of-call-report') return null;
			$call = isset($m['call']) ? $m['call'] : array();
			$pid = isset($call['id']) ? (string)$call['id'] : '';
			$meta = isset($call['assistant']['metadata']) ? $call['assistant']['metadata'] : (isset($m['assistant']['metadata']) ? $m['assistant']['metadata'] : array());
			$our = isset($meta['crm_call_id']) ? (int)$meta['crm_call_id'] : 0;
			$art = isset($m['artifact']) ? $m['artifact'] : array();
			$f['transcript'] = isset($art['transcript']) ? $art['transcript'] : (isset($m['transcript']) ? $m['transcript'] : '');
			$f['recording_url'] = isset($art['recordingUrl']) ? $art['recordingUrl'] : (isset($m['recordingUrl']) ? $m['recordingUrl'] : '');
			$f['summary'] = isset($m['analysis']['summary']) ? $m['analysis']['summary'] : (isset($m['summary']) ? $m['summary'] : '');
			$f['duration'] = isset($m['durationSeconds']) ? (int)round($m['durationSeconds']) : 0;
			$reason = isset($m['endedReason']) ? strtolower($m['endedReason']) : '';
			if (strpos($reason, 'voicemail') !== false) $f['outcome'] = 'Voicemail';
			elseif (preg_match('~did-not-answer|no-answer|busy~', $reason)) $f['outcome'] = 'No answer';
			elseif (preg_match('~error|failed|invalid~', $reason)) { $f['status'] = 'Failed'; $f['outcome'] = 'Failed'; $f['error'] = mb_substr($reason, 0, 255); }
		} elseif (isset($p['call_id']) && (isset($p['concatenated_transcript']) || isset($p['call_length']) || isset($p['answered_by']))) {
			// Bland AI
			$pid = (string)$p['call_id'];
			$our = isset($p['metadata']['crm_call_id']) ? (int)$p['metadata']['crm_call_id'] : 0;
			$f['transcript'] = isset($p['concatenated_transcript']) ? (string)$p['concatenated_transcript'] : '';
			$f['summary'] = isset($p['summary']) ? (string)$p['summary'] : '';
			$f['recording_url'] = isset($p['recording_url']) ? (string)$p['recording_url'] : '';
			$f['duration'] = isset($p['call_length']) ? (int)round((float)$p['call_length'] * 60) : 0;
			$ans = isset($p['answered_by']) ? strtolower((string)$p['answered_by']) : '';
			$st = isset($p['status']) ? strtolower((string)$p['status']) : '';
			if ($ans === 'voicemail') $f['outcome'] = 'Voicemail';
			elseif ($ans === 'no-answer' || in_array($st, array('no-answer', 'busy'), true)) $f['outcome'] = 'No answer';
			elseif ($st === 'failed' || !empty($p['error_message'])) { $f['status'] = 'Failed'; $f['outcome'] = 'Failed'; $f['error'] = mb_substr((string)(isset($p['error_message']) ? $p['error_message'] : 'failed'), 0, 255); }
		} else {
			// Custom endpoint: { crm_call_id, status, outcome, duration, transcript, summary, recording_url, call_id }
			$our = isset($p['crm_call_id']) ? (int)$p['crm_call_id'] : 0;
			$pid = isset($p['call_id']) ? (string)$p['call_id'] : '';
			if (!$our && $pid === '') return null;
			$f['transcript'] = isset($p['transcript']) ? (string)$p['transcript'] : '';
			$f['summary'] = isset($p['summary']) ? (string)$p['summary'] : '';
			$f['recording_url'] = isset($p['recording_url']) ? (string)$p['recording_url'] : '';
			$f['duration'] = isset($p['duration']) ? (int)$p['duration'] : 0;
			if (isset($p['outcome']) && in_array($p['outcome'], self::$outcomes, true)) $f['outcome'] = $p['outcome'];
			if (isset($p['status']) && strtolower($p['status']) === 'failed') { $f['status'] = 'Failed'; $f['outcome'] = 'Failed'; $f['error'] = mb_substr((string)(isset($p['error']) ? $p['error'] : 'failed'), 0, 255); }
		}
		if (is_array($f['transcript'])) $f['transcript'] = json_encode($f['transcript']);
		$row = null;
		if ($our > 0) $row = $this->db->where('id', $our)->get('ci_ai_calls')->row_array();
		if (empty($row) && $pid !== '') $row = $this->db->where('provider_call_id', $pid)->get('ci_ai_calls')->row_array();
		// the metadata id must match the provider id we stored, so a guessed crm_call_id cannot overwrite another call
		if (!empty($row) && $pid !== '' && $row['provider_call_id'] !== '' && $row['provider_call_id'] !== $pid) return null;
		if (!empty($row) && $pid !== '' && $row['provider_call_id'] === '') $f['provider_call_id'] = substr($pid, 0, 100);
		return empty($row) ? null : array($row, $f);
	}

	/** Saves the result, works out the outcome and hands hot leads back to their owner as follow-ups. */
	public function finish_call($row, $f)
	{
		if (in_array($row['status'], array('Completed', 'Failed', 'Cancelled'), true) && $row['ended_at']) return $row;
		$now = date('Y-m-d H:i:s');
		$upd = array(
			'status' => $f['status'], 'duration' => max(0, (int)$f['duration']), 'transcript' => $f['transcript'], 'summary' => mb_substr((string)$f['summary'], 0, 5000),
			'recording_url' => preg_match('~^https://~i', $f['recording_url']) ? mb_substr($f['recording_url'], 0, 500) : '', 'error' => $f['error'], 'ended_at' => $now
		);
		if (isset($f['provider_call_id'])) $upd['provider_call_id'] = $f['provider_call_id'];
		if (!empty($f['followup_date'])) $upd['followup_date'] = $f['followup_date'];
		$outcome = $f['outcome'];
		$t = strtolower((string)$f['transcript']);
		if ($outcome === '' && preg_match("~(do not|don't|dont|stop) call|remove (my|this) number|not call (me|again)|take me off~", $t)) $outcome = 'Do not call';
		if ($outcome === '' && trim($t) !== '') {
			$this->load->library('groq_service');
			if ($this->groq_service->is_configured()) {
				$ai = $this->groq_service->analyze_transcript($f['transcript']);
				if (!empty($ai['status'])) {
					$d = $ai['data'];
					$map = array('Interested' => 'Interested', 'Deal Closed' => 'Interested', 'Callback Scheduled' => 'Call back', 'Follow-up Required' => 'Call back',
						'Not Interested' => 'Not interested', 'Wrong Number' => 'Wrong number', 'No Response' => 'No answer', 'Not Reachable' => 'No answer', 'Requirement Not Clear' => 'Call back');
					$outcome = isset($map[$d['next_status']]) ? $map[$d['next_status']] : '';
					if ($outcome === 'Interested' && preg_match('~meeting|appointment|visit~i', (string)$d['next_action'] . ' ' . (string)$d['summary'])) $outcome = 'Meeting booked';
					if ($upd['summary'] === '') $upd['summary'] = (string)$d['summary'];
					$upd['sentiment'] = (string)$d['sentiment'];
					$upd['next_action'] = mb_substr((string)$d['next_action'], 0, 255);
					if (!empty($d['follow_up_date']) && strtotime($d['follow_up_date'])) $upd['followup_date'] = date('Y-m-d', strtotime($d['follow_up_date']));
				}
			}
		}
		if ($outcome === '') $outcome = $upd['duration'] > 0 && trim($t) !== '' ? 'Call back' : 'No answer';
		$upd['outcome'] = $outcome;
		$this->db->where('id', (int)$row['id'])->update('ci_ai_calls', $upd);
		$row = array_merge($row, $upd);

		if (!empty($row['is_test']) || (int)$row['lead_id'] <= 0) return $row;
		$agent = $this->agent($row['agent_id']);
		if ($outcome === 'Do not call' && !empty($agent['dnd_on_request'])) {
			$this->db->where('id', (int)$row['lead_id'])->update('ci_leads', array('dnd_status' => 'DND', 'dnd_check_date' => date('Y-m-d')));
			$this->cancel_queue(array('lead_id' => (int)$row['lead_id']));
		}
		if (in_array($outcome, self::$hot, true) && (int)$row['owner_id'] > 0) {
			$this->db->insert('ci_followup', array(
				'lead_id' => (int)$row['lead_id'], 'lead_type' => 'lead', 'user_id' => (int)$row['owner_id'],
				'followup_date' => !empty($upd['followup_date']) ? $upd['followup_date'] : date('Y-m-d', strtotime('+1 day')),
				'followup_time' => '10:00:00',
				'followup_remark' => mb_substr('[AI call - ' . $outcome . '] ' . ($upd['summary'] !== '' ? $upd['summary'] : 'See the AI call transcript.') . ($upd['next_action'] ?? '' ? ' | Next: ' . $upd['next_action'] : ''), 0, 2000),
				'followup_result' => $outcome
			));
		}
		return $row;
	}

	/* ---------- lists ---------- */

	/** SQL condition limiting AI calls to records the user may see (own + team; everything for managers). */
	public function scope_sql($uid, $role)
	{
		$M = $this->dialer_campaign_model;
		if ($M->is_manager_role($role, $uid)) return '1=1';
		$ids = array_merge(array((int)$uid), $M->team_ids($uid));
		return '(Q.owner_id IN (' . implode(',', $ids) . ') OR Q.queued_by = ' . (int)$uid . ')';
	}

	public function calls($where_sql, $limit = 100)
	{
		return $this->db->query("SELECT Q.id, Q.agent_id, Q.campaign_id, Q.lead_id, Q.owner_id, Q.phone, Q.contact, Q.customer, Q.status, Q.outcome, Q.duration, Q.summary, Q.sentiment,
			Q.next_action, Q.followup_date, Q.recording_url, Q.error, Q.is_test, Q.handled_by, Q.queued_at, Q.started_at, Q.ended_at, (Q.transcript IS NOT NULL AND Q.transcript != '') has_transcript,
			G.name agent_name, C.name campaign_name, U.name owner_name
			FROM ci_ai_calls Q LEFT JOIN ci_ai_agents G ON G.id = Q.agent_id LEFT JOIN ci_dialer_campaigns C ON C.id = Q.campaign_id LEFT JOIN ci_user U ON U.id = Q.owner_id
			WHERE {$where_sql} ORDER BY COALESCE(Q.ended_at, Q.started_at, Q.queued_at) DESC, Q.id DESC LIMIT " . (int)$limit)->result_array();
	}

	public function summary($scope)
	{
		$today = date('Y-m-d 00:00:00');
		$week = date('Y-m-d H:i:s', strtotime('-7 days'));
		$hot = "'" . implode("','", self::$hot) . "'";
		$r = $this->db->query("SELECT SUM(Q.started_at >= '{$today}') today, SUM(Q.started_at >= '{$week}' AND Q.status IN ('Completed', 'Failed')) week,
			SUM(Q.started_at >= '{$week}' AND (duration > 0 AND outcome NOT IN ('Voicemail', 'No answer', 'Failed'))) talked, SUM(Q.outcome IN ({$hot}) AND Q.handled_by = 0) open_hot,
			SUM(Q.status = 'Queued') queued, SUM(Q.status = 'Calling') calling
			FROM ci_ai_calls Q WHERE Q.is_test = 0 AND {$scope}")->row_array();
		return array_map('intval', $r ?: array());
	}
}
