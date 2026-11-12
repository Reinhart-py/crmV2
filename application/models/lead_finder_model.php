<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Lead Finder link: leads found by the Lead Finder app come here to be checked against the database
 * before anyone adds them.
 *
 * Trust: a secret made on System > Integrations > Lead Finder link and pasted into the Lead Finder app.
 *   X-Suite-Timestamp (unix seconds, at most 5 minutes off), X-Suite-Delivery (unique id, used once) and
 *   X-Suite-Signature = "sha256=" + HMAC-SHA256 hex of "<timestamp>.<raw body>".
 * People: the Lead Finder sends the email of the person who searched. Only an active CRM user with that
 *   email (login email or company email) and the "Lead Finder" right can send leads; the review page
 *   opens only for that same person (or an administrator) signed in to the CRM.
 *
 * Duplicate check: every lead is compared with every record in ci_leads on 9 fields:
 *   company name, phone (any of 5 phone columns), email, website domain, listing id
 *   (Google / 2GIS / directory id inside the source link), address, map location (within 120 m), city, country.
 *   Name, phone and email use the same rules as Import Database (helpers/crm_dedupe_helper.php).
 *   - Duplicate, as in Import Database: the same phone, or the same email, or the same company name
 *     (ignoring LLC, FZE, Trading...). Also: the threshold (default 5) or more fields match, the same
 *     listing id, or a similar name with the same website.
 *   - Possible: other matches on an identifying field (similar name, website, address, location).
 *   - New: nothing identifying matches any record (city and country alone do not count).
 * Inside one batch, as in an import file: a later lead with the same phone, email, company name or listing
 * as an earlier lead is a duplicate of it.
 *
 * Settings: ci_integration_settings, service 'lead_finder': api_secret, status 1 = on,
 *   config_json {threshold, max_batch}.
 */
require_once APPPATH . 'helpers/crm_dedupe_helper.php';

class Lead_finder_model extends CI_Model
{
	const SERVICE = 'lead_finder';
	const WINDOW = 300;
	const EXPIRE_DAYS = 7;
	const KEEP_DAYS = 30;
	const FIELD_LABELS = array(
		'name' => 'Company name', 'phone' => 'Phone', 'email' => 'Email', 'website' => 'Website',
		'listing' => 'Listing ID', 'address' => 'Address', 'location' => 'Map location', 'city' => 'City', 'country' => 'Country',
	);

	private $ready = false;

	/* ---------------- tables ---------------- */

	public function ensure()
	{
		if ($this->ready) return;
		$this->ready = true;
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_lf_batches (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			delivery_id VARCHAR(100) NOT NULL,
			sent_by INT NOT NULL DEFAULT 0,
			sent_email VARCHAR(190) NOT NULL DEFAULT '',
			engine VARCHAR(40) NOT NULL DEFAULT '',
			search_query VARCHAR(255) NOT NULL DEFAULT '',
			country_code CHAR(2) NOT NULL DEFAULT '',
			total INT NOT NULL DEFAULT 0,
			n_new INT NOT NULL DEFAULT 0,
			n_possible INT NOT NULL DEFAULT 0,
			n_dup INT NOT NULL DEFAULT 0,
			state VARCHAR(12) NOT NULL DEFAULT 'open',
			created_at DATETIME NOT NULL,
			finished_at DATETIME NULL,
			finished_by INT NULL,
			import_batch_id INT NULL,
			n_added INT NOT NULL DEFAULT 0,
			n_merged INT NOT NULL DEFAULT 0,
			n_forced INT NOT NULL DEFAULT 0,
			n_skipped INT NOT NULL DEFAULT 0,
			UNIQUE KEY delivery (delivery_id),
			KEY sent_by (sent_by, state)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_lf_rows (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			batch_id INT UNSIGNED NOT NULL,
			line INT NOT NULL DEFAULT 0,
			category VARCHAR(10) NOT NULL DEFAULT 'new',
			score TINYINT NOT NULL DEFAULT 0,
			match_lead_id INT NOT NULL DEFAULT 0,
			match_json TEXT NULL,
			data_json MEDIUMTEXT NULL,
			decision VARCHAR(10) NOT NULL DEFAULT '',
			result_lead_id INT NOT NULL DEFAULT 0,
			note VARCHAR(255) NOT NULL DEFAULT '',
			KEY batch (batch_id, category)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		// Same tables as Import Database, so Lead Finder batches can be undone from that page.
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_import_batches (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			file_name VARCHAR(255) NOT NULL DEFAULT '',
			created_by INT NOT NULL DEFAULT 0,
			created_at DATETIME NOT NULL,
			total_rows INT NOT NULL DEFAULT 0,
			imported INT NOT NULL DEFAULT 0,
			skipped_dups INT NOT NULL DEFAULT 0,
			errors INT NOT NULL DEFAULT 0,
			undone_at DATETIME NULL,
			undone_by INT NULL,
			undo_removed INT NOT NULL DEFAULT 0,
			undo_kept INT NOT NULL DEFAULT 0,
			KEY created_by (created_by)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_import_batch_leads (
			batch_id INT UNSIGNED NOT NULL,
			lead_id INT NOT NULL,
			PRIMARY KEY (batch_id, lead_id),
			KEY lead_id (lead_id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->tidy();
	}

	/** Unreviewed batches expire after 7 days; the lead details of any batch are removed after 30 days. */
	private function tidy()
	{
		if (mt_rand(1, 20) !== 1) return;
		$this->db->query("UPDATE ci_lf_batches SET state = 'expired' WHERE state = 'open' AND created_at < ?", array(date('Y-m-d H:i:s', time() - self::EXPIRE_DAYS * 86400)));
		$this->db->query("DELETE r FROM ci_lf_rows r JOIN ci_lf_batches b ON b.id = r.batch_id WHERE b.state <> 'open' AND b.created_at < ?", array(date('Y-m-d H:i:s', time() - self::KEEP_DAYS * 86400)));
	}

	/* ---------------- settings ---------------- */

	public function row()
	{
		$r = $this->db->where('service_name', self::SERVICE)->get('ci_integration_settings')->row_array();
		if (!$r) $r = array('id' => 0, 'api_secret' => '', 'status' => 0, 'config_json' => '{}', 'updated_at' => '');
		$c = json_decode((string)$r['config_json'], true);
		$r['config'] = is_array($c) ? $c : array();
		return $r;
	}

	public function settings()
	{
		$r = $this->row();
		$t = isset($r['config']['threshold']) ? (int)$r['config']['threshold'] : 5;
		$m = isset($r['config']['max_batch']) ? (int)$r['config']['max_batch'] : 2000;
		return array(
			'on' => (int)$r['status'] === 1,
			'secret' => trim((string)$r['api_secret']),
			'threshold' => max(3, min(9, $t)),
			'max_batch' => max(50, min(5000, $m)),
		);
	}

	public function save_settings($on, $threshold, $max_batch)
	{
		$r = $this->row();
		$cfg = $r['config'];
		$cfg['threshold'] = max(3, min(9, (int)$threshold));
		$cfg['max_batch'] = max(50, min(5000, (int)$max_batch));
		$row = array('status' => $on ? 1 : 0, 'config_json' => json_encode($cfg), 'updated_at' => date('Y-m-d H:i:s'));
		if ($on && trim((string)$r['api_secret']) === '') $row['api_secret'] = $this->new_secret();
		if ((int)$r['id'] > 0) $this->db->where('id', (int)$r['id'])->update('ci_integration_settings', $row);
		else $this->db->insert('ci_integration_settings', $row + array('service_name' => self::SERVICE, 'api_key' => '', 'api_secret' => isset($row['api_secret']) ? $row['api_secret'] : '', 'endpoint' => '', 'created_at' => date('Y-m-d H:i:s')));
	}

	/** Makes a new secret; the Lead Finder must be given the new one. */
	public function regenerate()
	{
		$this->save_settings($this->settings()['on'], $this->settings()['threshold'], $this->settings()['max_batch']);
		$r = $this->row();
		$this->db->where('id', (int)$r['id'])->update('ci_integration_settings', array('api_secret' => $this->new_secret(), 'updated_at' => date('Y-m-d H:i:s')));
	}

	private function new_secret()
	{
		return 'lf_' . bin2hex(openssl_random_pseudo_bytes(24));
	}

	/* ---------------- trust ---------------- */

	public function sign($message, $secret)
	{
		return 'sha256=' . hash_hmac('sha256', $message, $secret);
	}

	/** '' when the call is signed with the current secret and fresh, else the reason. */
	public function verify($raw, $ts, $sig)
	{
		$s = $this->settings();
		if (!$s['on'] || $s['secret'] === '') return 'The Lead Finder link is switched off in the CRM.';
		$ts = (int)$ts;
		if ($ts <= 0 || abs(time() - $ts) > self::WINDOW) return 'The request time is missing or more than 5 minutes off. Check the clock of the Lead Finder server.';
		if (!is_string($sig) || !hash_equals($this->sign($ts . '.' . $raw, $s['secret']), trim($sig))) return 'The Lead Finder link key does not match the CRM. Copy it again from System > Integrations > Lead Finder link.';
		return '';
	}

	/**
	 * The CRM user who may send leads: active, not marked as left, with the Lead Finder right.
	 * Returns array(user row or null, reason).
	 */
	public function sender($email)
	{
		$email = strtolower(trim((string)$email));
		if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) return array(null, 'The Lead Finder did not say who is sending.');
		$rows = $this->db->query("SELECT id, name, email, c_email, status, user_type FROM ci_user WHERE LOWER(TRIM(email)) = ? OR LOWER(TRIM(c_email)) = ?", array($email, $email))->result_array();
		if (!$rows) return array(null, 'No CRM user has the email ' . $email . '.');
		$active = array();
		foreach ($rows as $u) if ((int)$u['status'] === 1) $active[] = $u;
		if (!$active) return array(null, 'The CRM user ' . $email . ' is blocked.');
		if (count($active) > 1) return array(null, 'More than one active CRM user has the email ' . $email . '. Ask an administrator to fix the user list.');
		$u = $active[0];
		if ($this->db->table_exists('ci_user_exit') && $this->db->where('user_id', (int)$u['id'])->count_all_results('ci_user_exit') > 0) return array(null, 'The CRM user ' . $email . ' is marked as having left the company.');
		$this->load->helper('crm_permission');
		if (!crm_can('lead_finder', 'read', (int)$u['id'])) return array(null, $u['name'] . ' does not have the Lead Finder right in the CRM (Roles & Permissions).');
		$u['can_force'] = crm_can('lead_finder', 'write', (int)$u['id']);
		return array($u, '');
	}

	/* ---------------- normalising ---------------- */

	private static $free_mail = array('gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'live.com', 'icloud.com', 'aol.com', 'msn.com', 'rediffmail.com', 'ymail.com', 'gmx.com', 'mail.com', 'proton.me', 'protonmail.com', 'emirates.net.ae', 'eim.ae', 'yahoo.co.in', 'hotmail.co.uk');
	private static $listing_hosts = array('google.', 'goo.gl', '2gis.', 'facebook.com', 'instagram.com', 'linkedin.com', 'twitter.com', 'x.com', 'yellowpages', 'justdial.com', 'indiamart.com', 'openstreetmap.org', 'foursquare.com', 'yelp.', 'tiktok.com', 'youtube.com', 'wa.me', 'whatsapp.com', 'bit.ly', 'linktr.ee');

	/* Name, phone and email: the shared rules of Import Database (helpers/crm_dedupe_helper.php). */
	public static function name_core($s) { return crm_dup_name_core($s); }
	public static function phone_key($s) { return crm_dup_phone_key($s); }
	public static function phone_keys($s) { return crm_dup_phone_keys($s); }
	public static function emails($s) { return crm_dup_emails($s); }

	/** The company's own web domain (not a map, directory or social site). */
	public static function domain($url)
	{
		$url = trim((string)$url);
		if ($url === '') return '';
		if (!preg_match('~^[a-z][a-z0-9+.\-]*://~i', $url)) $url = 'http://' . $url;
		$h = strtolower((string)parse_url($url, PHP_URL_HOST));
		$h = preg_replace('/^www\d*\./', '', $h);
		if ($h === '' || strpos($h, '.') === false) return '';
		foreach (self::$listing_hosts as $x) if (strpos($h, $x) !== false) return '';
		return $h;
	}

	public static function email_domain($email)
	{
		$p = strrpos((string)$email, '@');
		if ($p === false) return '';
		$d = strtolower(substr($email, $p + 1));
		return in_array($d, self::$free_mail, true) ? '' : $d;
	}

	/** Ids of map / directory listings found in a text (source links, remarks). */
	public static function listing_ids($s)
	{
		$s = (string)$s;
		$out = array();
		if (preg_match_all('~/firm/(\d{8,})~', $s, $m)) foreach ($m[1] as $v) $out['2gis:' . $v] = true;
		if (preg_match_all('~\b(ChIJ[\w\-]{12,})~', $s, $m)) foreach ($m[1] as $v) $out['g:' . $v] = true;
		if (preg_match_all('~[?&]cid=(\d{6,})~', $s, $m)) foreach ($m[1] as $v) $out['cid:' . $v] = true;
		if (preg_match_all('~Listing ID:\s*([a-z0-9]+:[\w:\-/.]+)~i', $s, $m)) foreach ($m[1] as $v) $out[strtolower(rtrim($v, '.'))] = true;
		return array_keys($out);
	}

	/** Listing id of a Lead Finder lead, same shape as listing_ids(). */
	public static function lead_listing_id($engine, $place_id)
	{
		$p = trim((string)$place_id);
		if ($p === '') return '';
		if (preg_match('/^ChIJ[\w\-]{12,}$/', $p)) return 'g:' . $p;
		if ($engine === '2gis' && preg_match('/^(\d{8,})/', $p, $m)) return '2gis:' . $m[1];
		return strtolower(strpos($p, ':') !== false ? $p : $engine . ':' . $p);
	}

	public static function addr_norm($s)
	{
		$s = strtolower((string)$s);
		$s = preg_replace('/\b(united arab emirates|uae|india|street|st|road|rd|building|bldg|floor|flr|office|shop|no|near|opp|opposite)\b/', ' ', $s);
		$w = preg_split('/[^a-z0-9]+/', $s, -1, PREG_SPLIT_NO_EMPTY);
		return $w;
	}

	public static function addr_match($a, $b)
	{
		$wa = self::addr_norm($a);
		$wb = self::addr_norm($b);
		if (count($wa) < 2 || count($wb) < 2) return false;
		$ja = implode('', $wa);
		$jb = implode('', $wb);
		if ($ja === $jb) return true;
		if (strlen($ja) >= 12 && strlen($jb) >= 12 && (strpos($ja, $jb) !== false || strpos($jb, $ja) !== false)) return true;
		$i = count(array_intersect(array_unique($wa), array_unique($wb)));
		$u = count(array_unique(array_merge($wa, $wb)));
		return $u > 0 && $i >= 3 && $i / $u >= 0.7;
	}

	/** lat/lng from "25.2,55.3", a 2GIS link (lng%2Clat) or a Google link (@lat,lng). */
	public static function coords($s)
	{
		$s = urldecode((string)$s);
		if (preg_match('~/firm/\d+/(-?\d{1,3}\.\d+),(-?\d{1,3}\.\d+)~', $s, $m)) return array((float)$m[2], (float)$m[1]);
		if (preg_match('~@(-?\d{1,2}\.\d{3,}),(-?\d{1,3}\.\d{3,})~', $s, $m)) return array((float)$m[1], (float)$m[2]);
		if (preg_match('~^\s*(-?\d{1,2}(?:\.\d+)?)\s*,\s*(-?\d{1,3}(?:\.\d+)?)\s*$~', $s, $m)) return array((float)$m[1], (float)$m[2]);
		return null;
	}

	public static function metres($a, $b)
	{
		$r = 6371000;
		$la1 = deg2rad($a[0]); $la2 = deg2rad($b[0]);
		$dl = deg2rad($b[0] - $a[0]); $dn = deg2rad($b[1] - $a[1]);
		$h = sin($dl / 2) * sin($dl / 2) + cos($la1) * cos($la2) * sin($dn / 2) * sin($dn / 2);
		return 2 * $r * asin(min(1, sqrt($h)));
	}

	public static function names_match($a, $b)
	{
		if ($a === '' || $b === '') return false;
		if ($a === $b) return true;
		$la = strlen($a); $lb = strlen($b);
		if (min($la, $lb) >= 6 && (strpos($a, $b) === 0 || strpos($b, $a) === 0) && min($la, $lb) / max($la, $lb) >= 0.6) return true;
		if (min($la, $lb) >= 8) { similar_text($a, $b, $pct); if ($pct >= 92) return true; }
		return false;
	}

	/* ---------------- incoming leads ---------------- */

	private function country_id($name_or_code)
	{
		static $cache = array();
		$k = strtolower(trim((string)$name_or_code));
		if ($k === '') return 0;
		if (isset($cache[$k])) return $cache[$k];
		$alias = array('uae' => 'united arab emirates', 'usa' => 'united states', 'uk' => 'united kingdom', 'ksa' => 'saudi arabia');
		if (isset($alias[$k])) $k = $alias[$k];
		$r = strlen($k) === 2
			? $this->db->query('SELECT id FROM countries WHERE LOWER(sortname) = ? LIMIT 1', array($k))->row_array()
			: $this->db->query('SELECT id FROM countries WHERE LOWER(name) = ? LIMIT 1', array($k))->row_array();
		return $cache[$k] = $r ? (int)$r['id'] : 0;
	}

	private static function s($v, $max = 255)
	{
		if (is_array($v) || is_object($v)) return '';
		$v = trim(preg_replace('/\s+/u', ' ', strip_tags((string)$v)));
		// Spreadsheet formula guard, same as Import Database.
		if ($v !== '' && in_array($v[0], array('=', '+', '-', '@'), true) && !preg_match('/^\+?[\d\s\-()]+$/', $v)) $v = "'" . $v;
		return mb_substr($v, 0, $max);
	}

	/** One Lead Finder result -> the fields the CRM keeps (still without owner and dates). */
	public function map_lead($l, $engine, $country_code)
	{
		$engine = preg_replace('/[^a-z0-9]/', '', strtolower((string)$engine));
		$g = function ($k, $max = 255) use ($l) { return isset($l[$k]) ? self::s($l[$k], $max) : ''; };
		$lat = isset($l['latitude']) && is_numeric($l['latitude']) ? (float)$l['latitude'] : null;
		$lng = isset($l['longitude']) && is_numeric($l['longitude']) ? (float)$l['longitude'] : null;
		$website = $g('website', 500);
		$maps = $g('url', 1000);
		$listing = self::lead_listing_id($engine, $g('place_id', 200));
		$listings = array_values(array_unique(array_filter(array_merge(array($listing), self::listing_ids($maps)))));
		if ($listing === '' && $listings) $listing = $listings[0];
		$cid = $this->country_id($g('country', 100));
		if (!$cid && $country_code !== '') $cid = $this->country_id($country_code);
		$remark = array();
		if ($g('rating', 10) !== '') $remark[] = 'Rating ' . $g('rating', 10) . ($g('reviews_count', 10) !== '' ? ' (' . $g('reviews_count', 10) . ' reviews)' : '');
		if ($g('listed_at', 40) !== '') $remark[] = 'Added to ' . self::engine_name($engine) . ' ' . substr($g('listed_at', 40), 0, 10);
		if ($maps !== '' && $website !== '') $remark[] = 'Map: ' . $maps;
		if ($listing !== '') $remark[] = 'Listing ID: ' . $listing;
		return array(
			'customer' => $g('title', 255),
			'contact1' => $g('phone_1', 60),
			'alt_contact' => $g('phone_2', 60),
			'email' => strtolower($g('email', 190)),
			'lead_source' => $website !== '' ? $website : $maps,
			'address' => $g('address', 500),
			'city' => $g('city', 100),
			'country' => $cid,
			'location' => ($lat !== null && $lng !== null) ? round($lat, 6) . ',' . round($lng, 6) : '',
			'business_category' => $g('category', 150),
			'remark' => implode(' | ', $remark),
			'_website' => $website,
			'_maps' => $maps,
			'_listing' => $listing,
			'_listings' => $listings,
			'_country_name' => $g('country', 100),
		);
	}

	public static function engine_name($e)
	{
		$n = array('gmaps' => 'Google Maps', '2gis' => '2GIS', 'osm' => 'OpenStreetMap', 'foursquare' => 'Foursquare', 'ypuae' => 'Yellow Pages UAE', 'indiamart' => 'IndiaMART', 'justdial' => 'JustDial');
		return isset($n[$e]) ? $n[$e] : ucfirst($e);
	}

	/* ---------------- the duplicate check ---------------- */

	/** Lookup tables over every record in the database (loaded once per batch, in slices). */
	private function build_index()
	{
		$ix = array('phone' => array(), 'name' => array(), 'prefix' => array(), 'email' => array(), 'domain' => array(), 'listing' => array());
		$last = 0;
		do {
			$rows = $this->db->query("SELECT id, customer, contact1, contact2, alt_contact, mobile, alt_mobile, email, lead_source,
					IF(remark LIKE '%Listing ID:%', remark, '') AS lrem
				FROM ci_leads WHERE id > ? ORDER BY id LIMIT 20000", array($last))->result_array();
			foreach ($rows as $r) {
				$id = (int)$r['id'];
				$last = $id;
				foreach (array('contact1', 'contact2', 'alt_contact', 'mobile', 'alt_mobile') as $c) foreach (self::phone_keys($r[$c]) as $k) $ix['phone'][$k][$id] = true;
				$core = self::name_core($r['customer']);
				if ($core !== '') {
					$ix['name'][$core][$id] = true;
					$ix['prefix'][substr($core, 0, 5)][$id] = true;
				}
				foreach (self::emails($r['email']) as $e) {
					$ix['email'][$e][$id] = true;
					$d = self::email_domain($e);
					if ($d !== '') $ix['domain'][$d][$id] = true;
				}
				$d = self::domain($r['lead_source']);
				if ($d !== '') $ix['domain'][$d][$id] = true;
				foreach (self::listing_ids($r['lead_source'] . ' ' . $r['lrem']) as $k) $ix['listing'][$k][$id] = true;
			}
		} while (count($rows) === 20000);
		return $ix;
	}

	/** Candidate record ids for one mapped lead. */
	private function candidates($ix, $m)
	{
		$ids = array();
		$add = function ($bucket, $key) use (&$ids, $ix) { if ($key !== '' && isset($ix[$bucket][$key])) foreach ($ix[$bucket][$key] as $id => $t) $ids[$id] = true; };
		foreach (array_merge(self::phone_keys($m['contact1']), self::phone_keys($m['alt_contact'])) as $k) $add('phone', $k);
		$core = self::name_core($m['customer']);
		$add('name', $core);
		if (strlen($core) >= 6) $add('prefix', substr($core, 0, 5));
		foreach (self::emails($m['email']) as $e) { $add('email', $e); $add('domain', self::email_domain($e)); }
		$add('domain', self::domain($m['_website']));
		foreach ($m['_listings'] as $k) $add('listing', $k);
		return array_keys($ids);
	}

	/** Which of the 9 fields of lead $m match record $r. */
	public function compare($m, $r)
	{
		$hit = array();
		$hit['name'] = self::names_match(self::name_core($m['customer']), self::name_core($r['customer']));
		$rp = array();
		foreach (array('contact1', 'contact2', 'alt_contact', 'mobile', 'alt_mobile') as $c) foreach (self::phone_keys($r[$c]) as $k) $rp[$k] = true;
		$hit['phone'] = false;
		foreach (array_merge(self::phone_keys($m['contact1']), self::phone_keys($m['alt_contact'])) as $k) if (isset($rp[$k])) $hit['phone'] = true;
		$re = self::emails($r['email']);
		$hit['email'] = (bool)array_intersect(self::emails($m['email']), $re);
		$md = array_filter(array(self::domain($m['_website'])) + array(1 => self::email_domain($m['email'])));
		$rd = array_filter(array_merge(array(self::domain($r['lead_source'])), array_map(array(__CLASS__, 'email_domain'), $re)));
		$hit['website'] = (bool)array_intersect($md, $rd);
		$hit['listing'] = (bool)array_intersect($m['_listings'], self::listing_ids($r['lead_source'] . ' ' . $r['remark']));
		$hit['address'] = $m['address'] !== '' && self::addr_match($m['address'], $r['address']);
		$a = self::coords($m['location']);
		$b = self::coords($r['location']);
		if (!$b) $b = self::coords($r['lead_source']);
		$hit['location'] = $a && $b && self::metres($a, $b) <= 120;
		$hit['city'] = $m['city'] !== '' && strcasecmp(trim($m['city']), trim((string)$r['city'])) === 0;
		$hit['country'] = (int)$m['country'] > 0 && (int)$m['country'] === (int)$r['country'];
		return array_keys(array_filter($hit));
	}

	/**
	 * 'dup', 'possible' or 'new' for the fields that matched. $same_name: the company names are equal after
	 * removing LLC, FZE, Trading... (not just similar), which Import Database treats as a duplicate.
	 */
	public static function classify($fields, $threshold, $same_name = false)
	{
		$f = array_flip($fields);
		$ident = array_intersect($fields, array('name', 'phone', 'email', 'website', 'listing', 'address', 'location'));
		if (!$ident) return 'new';
		// The Import Database rules: same phone, same email or same company name.
		if (isset($f['phone']) || isset($f['email']) || ($same_name && isset($f['name']))) return 'dup';
		if (count($fields) >= $threshold) return 'dup';
		if (isset($f['listing'])) return 'dup';
		if (isset($f['name']) && isset($f['website'])) return 'dup';
		return 'possible';
	}

	/**
	 * Stores a batch sent by $user. $payload: engine, query, country_code, leads[].
	 * Returns array(ok, message or batch row).
	 */
	public function stage($payload, $user, $delivery)
	{
		$this->ensure();
		$set = $this->settings();
		$leads = isset($payload['leads']) && is_array($payload['leads']) ? array_values($payload['leads']) : array();
		if (!$leads) return array(false, 'No leads were sent.');
		if (count($leads) > $set['max_batch']) return array(false, 'Too many leads in one go (' . count($leads) . '). The CRM accepts up to ' . $set['max_batch'] . ' at a time.');
		$delivery = substr(preg_replace('/[^\w\-.:]/', '', (string)$delivery), 0, 100);
		if ($delivery === '') return array(false, 'Missing delivery id.');
		if ($this->db->where('delivery_id', $delivery)->count_all_results('ci_lf_batches') > 0) {
			$b = $this->db->where('delivery_id', $delivery)->get('ci_lf_batches')->row_array();
			return array(true, $b);
		}
		$engine = preg_replace('/[^a-z0-9]/', '', strtolower((string)(isset($payload['engine']) ? $payload['engine'] : '')));
		$cc = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', (string)(isset($payload['country_code']) ? $payload['country_code'] : '')), 0, 2));

		$ix = $this->build_index();
		$mapped = array();
		$seen = array('phone' => array(), 'name' => array(), 'listing' => array(), 'email' => array());
		$counts = array('new' => 0, 'possible' => 0, 'dup' => 0);
		$rows = array();
		foreach ($leads as $i => $l) {
			if (!is_array($l)) continue;
			$m = $this->map_lead($l, $engine, $cc);
			if ($m['customer'] === '') continue;
			$line = $i + 1;
			$best = array('fields' => array(), 'id' => 0, 'cat' => 'new');
			$core = self::name_core($m['customer']);
			$cand = $this->candidates($ix, $m);
			if ($cand) {
				foreach (array_chunk($cand, 300) as $chunk) {
					$recs = $this->db->select('id, lead_id, customer, contact1, contact2, alt_contact, mobile, alt_mobile, email, lead_source, address, city, country, location, remark, lead_assign, created')
						->where_in('id', $chunk)->get('ci_leads')->result_array();
					foreach ($recs as $r) {
						$f = $this->compare($m, $r);
						$cat = self::classify($f, $set['threshold'], $core !== '' && $core === self::name_core($r['customer']));
						$rank = array('new' => 0, 'possible' => 1, 'dup' => 2);
						// Stronger category first, then more matching fields, then the oldest record (as Import Database does).
						if ($rank[$cat] > $rank[$best['cat']] || ($rank[$cat] === $rank[$best['cat']] && (count($f) > count($best['fields']) || ($cat !== 'new' && count($f) === count($best['fields']) && (int)$r['id'] < $best['id'])))) {
							$best = array('fields' => $f, 'id' => (int)$r['id'], 'cat' => $cat, 'rec' => $r);
						}
					}
				}
			}
			// Inside this batch, as in an import file: the same phone, email, company name or listing as an
			// earlier lead makes the later one a duplicate of it.
			$same = 0;
			$bkeys = array(
				'name' => $core !== '' ? array($core) : array(),
				'listing' => $m['_listings'],
				'phone' => array_merge(self::phone_keys($m['contact1']), self::phone_keys($m['alt_contact'])),
				'email' => self::emails($m['email']),
			);
			foreach ($bkeys as $k => $vals) foreach ($vals as $v) if (!$same && $v !== '' && isset($seen[$k][$v])) $same = $seen[$k][$v];
			foreach ($bkeys as $k => $vals) foreach ($vals as $v) if ($v !== '' && !isset($seen[$k][$v])) $seen[$k][$v] = $line;

			$match = array('fields' => $best['fields'], 'in_batch' => 0);
			if (!empty($best['rec'])) {
				$r = $best['rec'];
				$match['record'] = array('id' => (int)$r['id'], 'lead_id' => $r['lead_id'], 'customer' => $r['customer'], 'phone' => $r['contact1'], 'email' => $r['email'], 'city' => $r['city'], 'owner' => (int)$r['lead_assign'], 'created' => $r['created']);
			}
			$cat = $best['cat'];
			if ($same && $cat !== 'dup') {
				$cat = 'dup';
				$match['in_batch'] = $same;
			}
			$counts[$cat]++;
			$rows[] = array('line' => $line, 'category' => $cat, 'score' => count($best['fields']), 'match_lead_id' => $best['id'], 'match_json' => json_encode($match), 'data_json' => json_encode($m));
		}
		if (!$rows) return array(false, 'None of the leads had a company name.');

		$this->db->trans_start();
		$this->db->insert('ci_lf_batches', array(
			'delivery_id' => $delivery, 'sent_by' => (int)$user['id'], 'sent_email' => substr(strtolower((string)$user['email']), 0, 190),
			'engine' => $engine, 'search_query' => mb_substr(self::s(isset($payload['query']) ? $payload['query'] : ''), 0, 255), 'country_code' => $cc,
			'total' => count($rows), 'n_new' => $counts['new'], 'n_possible' => $counts['possible'], 'n_dup' => $counts['dup'],
			'state' => 'open', 'created_at' => date('Y-m-d H:i:s'),
		));
		$bid = (int)$this->db->insert_id();
		foreach (array_chunk($rows, 200) as $chunk) {
			foreach ($chunk as &$r) $r['batch_id'] = $bid;
			unset($r);
			$this->db->insert_batch('ci_lf_rows', $chunk);
		}
		$this->db->trans_complete();
		if (!$this->db->trans_status()) return array(false, 'The CRM could not save the leads. Try again.');
		$this->ci_admin_model->log_admin_action('lead_finder', 'Lead Finder sent ' . count($rows) . ' leads (' . self::engine_name($engine) . ', "' . mb_substr((string)(isset($payload['query']) ? $payload['query'] : ''), 0, 80) . '"): ' . $counts['new'] . ' new, ' . $counts['possible'] . ' possible, ' . $counts['dup'] . ' duplicates. Batch #' . $bid, 'SUCCESS', (int)$user['id'], $user['name']);
		return array(true, $this->db->where('id', $bid)->get('ci_lf_batches')->row_array());
	}

	/* ---------------- review ---------------- */

	public function batch($id)
	{
		$this->ensure();
		return $this->db->where('id', (int)$id)->get('ci_lf_batches')->row_array();
	}

	public function batch_rows($id)
	{
		$rows = $this->db->where('batch_id', (int)$id)->order_by('line')->get('ci_lf_rows')->result_array();
		foreach ($rows as &$r) {
			$r['data'] = json_decode((string)$r['data_json'], true) ?: array();
			$r['match'] = json_decode((string)$r['match_json'], true) ?: array();
			unset($r['data_json'], $r['match_json']);
		}
		return $rows;
	}

	/** Batches this person may see: their own, or everyone's for people who see all data. */
	public function batches($uid, $all)
	{
		$this->ensure();
		if (!$all) $this->db->where('sent_by', (int)$uid);
		return $this->db->select('b.*, u.name AS sender_name')->from('ci_lf_batches b')->join('ci_user u', 'u.id = b.sent_by', 'left')
			->order_by('b.id', 'DESC')->limit(50)->get()->result_array();
	}

	/** Staff a reviewer may hand the leads to: themselves, their team, or anyone active for admins / all-data. */
	public function assignees($uid, $everyone)
	{
		$ids = $everyone ? null : array_merge(array((int)$uid), $this->ci_admin_model->fetchTeamList((int)$uid, ''));
		$this->db->select('id, name')->from('ci_user')->where('status', 1);
		if ($this->db->table_exists('ci_user_exit')) $this->db->where('id NOT IN (SELECT user_id FROM ci_user_exit)', null, false);
		if ($ids !== null) $this->db->where_in('id', $ids ?: array(0));
		return $this->db->order_by('name')->get()->result_array();
	}

	/** Fills only the empty fields of record $lead_id from mapped lead $m. Returns the fields filled. */
	private function fill_blanks($lead_id, $m, $uid)
	{
		$r = $this->db->where('id', (int)$lead_id)->get('ci_leads')->row_array();
		if (!$r) return array();
		$up = array();
		$blank = function ($v) { return trim((string)$v) === '' || (string)$v === '0'; };
		foreach (array('email', 'address', 'city', 'location', 'business_category', 'lead_source') as $k) {
			if ($m[$k] !== '' && $blank($r[$k])) $up[$k] = $m[$k];
		}
		if ((int)$m['country'] > 0 && (int)$r['country'] === 0) $up['country'] = (int)$m['country'];
		if ($m['business_category'] !== '' && $blank($r['industry_category'])) $up['industry_category'] = $m['business_category'];
		// New phone numbers go to the first empty phone column.
		$have = array();
		foreach (array('contact1', 'contact2', 'alt_contact', 'mobile', 'alt_mobile') as $c) foreach (self::phone_keys($r[$c]) as $k) $have[$k] = true;
		$free = array();
		foreach (array('contact1', 'alt_contact', 'contact2', 'alt_mobile') as $c) if ($blank($r[$c])) $free[] = $c;
		foreach (array($m['contact1'], $m['alt_contact']) as $p) {
			$k = self::phone_key($p);
			if ($k === '' || isset($have[$k]) || !$free) continue;
			$up[array_shift($free)] = $p;
			$have[$k] = true;
		}
		if (!$up) return array();
		$up['edited_by'] = (int)$uid;
		$up['modified'] = date('Y-m-d H:i:s');
		$this->db->where('id', (int)$lead_id)->update('ci_leads', $up);
		unset($up['edited_by'], $up['modified']);
		return array_keys($up);
	}

	/**
	 * Data Source must be a value of the managed list (Lists > Data Source, ci_terms cat_type 'data_source'),
	 * like every other record, so filters, reports and campaigns group Lead Finder leads correctly.
	 * Google Maps uses the list's "Google Maps"; another platform uses its own name, added to the list
	 * once (logged) when an administrator has not added it yet. A disabled list value is switched back on only
	 * by an administrator, so a disabled one falls back to "Other".
	 */
	public function data_source_for($engine, $uid)
	{
		$name = self::engine_name($engine);
		$terms = $this->db->query("SELECT id, title, status FROM ci_terms WHERE cat_type = 'data_source'")->result_array();
		$find = function ($title) use ($terms) {
			foreach ($terms as $t) if (strcasecmp(trim($t['title']), $title) === 0) return $t;
			return null;
		};
		$t = $find($name);
		if ($t === null && $engine === 'gmaps') $t = $find('Google Map');
		if ($t !== null) {
			if ((int)$t['status'] === 1) return trim($t['title']);
			$o = $find('Other');
			return $o ? trim($o['title']) : 'Other';
		}
		$this->db->insert('ci_terms', array('title' => $name, 'cat_type' => 'data_source', 'status' => 1, 'parent_id' => 0));
		$this->ci_admin_model->log_admin_action('terms', 'Added "' . $name . '" to Lists > Data Source (first Lead Finder import from ' . $name . ')', 'SUCCESS', (int)$uid);
		return $name;
	}

	/**
	 * Batch / Referral No: "<Mon-yy>/<Data Source>/LF-<batch>", e.g. "Nov-26/Google Maps/LF-12".
	 * Starts like the Database report's batch grouping (Mon-yy/Source) and ends with the Lead Finder batch,
	 * so every lead can be traced back to the batch it was approved in.
	 */
	public static function batch_ref($data_source, $batch_id)
	{
		return mb_substr(date('M-y') . '/' . $data_source . '/LF-' . (int)$batch_id, 0, 100);
	}

	/**
	 * Applies the reviewer's choices. $choices: row id => add|skip|merge|force; $reasons: row id => text.
	 * Returns array(ok, message).
	 */
	public function confirm($batch, $choices, $reasons, $assign_to, $uid, $can_force)
	{
		$rows = $this->batch_rows($batch['id']);
		$assign_to = (int)$assign_to;
		$data_source = $this->data_source_for($batch['engine'], $uid);
		$data_batch = self::batch_ref($data_source, (int)$batch['id']);
		$ok_choices = array('new' => array('add', 'skip'), 'possible' => array('add', 'skip', 'merge'), 'dup' => array('skip', 'merge', 'force'));
		$plan = array();
		foreach ($rows as $r) {
			if ($r['decision'] !== '') continue;
			$c = isset($choices[$r['id']]) ? (string)$choices[$r['id']] : 'skip';
			if (!in_array($c, $ok_choices[$r['category']], true)) $c = 'skip';
			if (in_array($c, array('merge', 'force'), true) && !$can_force) return array(false, 'You need Full access to Lead Finder to add duplicates or fill in existing records.');
			if ($c === 'merge' && (int)$r['match_lead_id'] <= 0) $c = 'skip';
			$why = isset($reasons[$r['id']]) ? trim(self::s($reasons[$r['id']], 200)) : '';
			if ($c === 'force' && mb_strlen($why) < 5) return array(false, 'Row ' . (int)$r['line'] . ' (' . $r['data']['customer'] . '): write why this duplicate should be added anyway.');
			$plan[] = array($r, $c, $why);
		}

		$this->db->trans_start();
		$n = array('add' => 0, 'force' => 0, 'merge' => 0, 'skip' => 0);
		$import_id = 0;
		$merged_ids = array();
		$forced_ids = array();
		foreach ($plan as $p) {
			list($r, $c, $why) = $p;
			$m = $r['data'];
			$result = 0;
			$note = '';
			if ($c === 'add' || $c === 'force') {
				if (!$import_id) {
					$this->db->insert('ci_import_batches', array(
						'file_name' => mb_substr('Lead Finder: ' . self::engine_name($batch['engine']) . ' "' . $batch['search_query'] . '" (LF-' . (int)$batch['id'] . ')', 0, 255),
						'created_by' => (int)$uid, 'created_at' => date('Y-m-d H:i:s'), 'total_rows' => (int)$batch['total'], 'errors' => 0,
					));
					$import_id = (int)$this->db->insert_id();
				}
				$data = array(
					'lead_id' => 100000000, 'customer' => $m['customer'], 'contact1' => $m['contact1'], 'alt_contact' => $m['alt_contact'], 'alt_mobile' => $m['alt_contact'],
					'email' => $m['email'], 'lead_source' => $m['lead_source'], 'address' => $m['address'], 'city' => $m['city'], 'country' => (int)$m['country'], 'state' => 0,
					'location' => $m['location'], 'business_category' => $m['business_category'], 'industry_category' => $m['business_category'],
					'remark' => mb_substr($m['remark'] . ($c === 'force' ? ' | Added although a duplicate of record ' . (int)$r['match_lead_id'] . ': ' . $why : ''), 0, 2000),
					'category' => 'New', 'status' => 36, 'company_status' => 'Active', 'dnd_status' => 'No',
					'corporate_tax_status' => 'Not Registered', 'vat_status' => 'Not Registered',
					'data_source' => $data_source, 'data_batch_referral_no' => $data_batch,
					'database_category' => $assign_to > 0 ? 'Allotted Database' : 'Non-Allotted Database',
					'c_partner' => function_exists('crm_brand_company') ? crm_brand_company() : '',
					'lead_assign' => $assign_to, 'created_by' => (int)$uid, 'admin_status' => 1, 'created' => date('Y-m-d'),
				);
				$result = (int)$this->comman_model->saveData('ci_leads', $data);
				if ($result) {
					$this->comman_model->updateData('id', $result, array('lead_id' => 100000000 + $result), 'ci_leads');
					$this->db->insert('ci_import_batch_leads', array('batch_id' => $import_id, 'lead_id' => $result));
					$n[$c]++;
					if ($c === 'force') { $forced_ids[] = $result; $note = $why; }
				}
			} elseif ($c === 'merge') {
				$filled = $this->fill_blanks((int)$r['match_lead_id'], $m, $uid);
				$result = (int)$r['match_lead_id'];
				$note = $filled ? 'Filled: ' . implode(', ', $filled) : 'Nothing was empty';
				if ($filled) { $n['merge']++; $merged_ids[] = $result . ' (' . implode(', ', $filled) . ')'; } else $n['skip']++;
			} else {
				$n['skip']++;
			}
			$this->db->where('id', (int)$r['id'])->update('ci_lf_rows', array('decision' => $c, 'result_lead_id' => $result, 'note' => mb_substr($note, 0, 255)));
		}
		if ($import_id) $this->db->where('id', $import_id)->update('ci_import_batches', array('imported' => $n['add'] + $n['force'], 'skipped_dups' => $n['skip']));
		$this->db->where('id', (int)$batch['id'])->update('ci_lf_batches', array(
			'state' => 'done', 'finished_at' => date('Y-m-d H:i:s'), 'finished_by' => (int)$uid, 'import_batch_id' => $import_id ?: null,
			'n_added' => $n['add'], 'n_forced' => $n['force'], 'n_merged' => $n['merge'], 'n_skipped' => $n['skip'],
		));
		$this->db->trans_complete();
		if (!$this->db->trans_status()) return array(false, 'Nothing was saved: the database reported an error. Try again.');

		$who = $assign_to > 0 ? $this->db->select('name')->where('id', $assign_to)->get('ci_user')->row('name') : 'Non-Allotted Database';
		$this->ci_admin_model->log_admin_action('lead_import', 'Lead Finder batch LF-' . (int)$batch['id'] . ': ' . $n['add'] . ' added, ' . $n['force'] . ' duplicates added on purpose, ' . $n['merge'] . ' existing records filled in, ' . $n['skip'] . ' skipped. Given to ' . $who . ($import_id ? '. Import batch #' . $import_id : ''), 'SUCCESS');
		if ($forced_ids) $this->ci_admin_model->log_admin_action('lead_finder_duplicates', 'Added duplicates on purpose from LF-' . (int)$batch['id'] . ': records ' . implode(', ', $forced_ids), 'SUCCESS');
		if ($merged_ids) $this->ci_admin_model->log_admin_action('lead_finder_fill', 'Filled empty fields from LF-' . (int)$batch['id'] . ' on records ' . implode('; ', $merged_ids), 'SUCCESS');
		return array(true, $n + array('import_id' => $import_id));
	}

	public function cancel($batch, $uid)
	{
		$this->db->where('id', (int)$batch['id'])->where('state', 'open')->update('ci_lf_batches', array('state' => 'cancelled', 'finished_at' => date('Y-m-d H:i:s'), 'finished_by' => (int)$uid, 'n_skipped' => (int)$batch['total']));
		$this->db->where('batch_id', (int)$batch['id'])->update('ci_lf_rows', array('data_json' => null));
	}
}
