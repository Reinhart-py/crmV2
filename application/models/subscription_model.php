<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Sales CRM subscription on the subscription portal (where the client signed up).
 *
 * Settings: System > Integrations > Third-Party API > Subscription (ci_integration_settings, service 'subscription'):
 *   endpoint            portal address, e.g. https://portal.example.com or http://localhost:8000
 *   api_secret          the portal's app secret (portal/data/app-secret)
 *   config.account_email the subscribing company's account email on the portal
 *   config.product      product on the portal (default 'crm')
 *   status              1 = check on, 0 = off (the CRM then works on its own, as before)
 *
 * The portal answers GET <endpoint>/api/entitlement?product=crm&email=... (Bearer app secret)
 * with { allowed, reason, subscribeUrl, accountUrl, status, plan, validUntil }.
 * An "allowed" answer is kept for 60 seconds; a refusal is never kept (asked again on the next page).
 * If the portal can't be reached, the last good answer counts for 24 hours.
 * The portal also pushes every change to api/subscription/events (see handle_event).
 */
class Subscription_model extends CI_Model
{
	const SERVICE = 'subscription';
	const FRESH_SEC = 60;
	const OFFLINE_OK_SEC = 86400;

	public function row()
	{
		if (!$this->db->table_exists('ci_integration_settings')) return null;
		$r = $this->db->where('service_name', self::SERVICE)->get('ci_integration_settings')->row_array();
		if ($r) {
			$c = json_decode((string)$r['config_json'], true);
			$r['config'] = is_array($c) ? $c : array();
		}
		return $r;
	}

	public function enabled($r = null)
	{
		$r = $r ?: $this->row();
		return $r && (int)$r['status'] === 1 && trim((string)$r['endpoint']) !== '' && trim((string)$r['api_secret']) !== '' && !empty($r['config']['account_email']);
	}

	/**
	 * The portal company this CRM belongs to (0 until the portal has answered once).
	 * One portal sign-in can own several companies; this CRM serves only this one.
	 */
	public function company_id($r = null)
	{
		$r = $r ?: $this->row();
		if (!$r) return 0;
		$c = $r['config'];
		if (!empty($c['company_id'])) return (int)$c['company_id'];
		return isset($c['last_result']['companyId']) ? (int)$c['last_result']['companyId'] : 0;
	}

	/** Ask the portal now. Returns the portal's answer plus 'reachable'. $email: whose answer (default: the account email). */
	public function ask($r, $email = null)
	{
		$product = !empty($r['config']['product']) ? $r['config']['product'] : 'crm';
		$email = $email !== null ? $email : $r['config']['account_email'];
		$url = rtrim(trim($r['endpoint']), '/') . '/api/entitlement?product=' . rawurlencode($product) . '&email=' . rawurlencode(strtolower(trim($email)));
		$cid = $this->company_id($r);
		if ($cid > 0) $url .= '&company=' . $cid;
		$ch = curl_init($url);
		curl_setopt_array($ch, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 6,
			CURLOPT_CONNECTTIMEOUT => 4,
			CURLOPT_HTTPHEADER => array('Authorization: Bearer ' . trim($r['api_secret']), 'Accept: application/json'),
		));
		$body = curl_exec($ch);
		$code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$err = curl_error($ch);
		curl_close($ch);
		$j = is_string($body) ? json_decode($body, true) : null;
		if ($code === 200 && is_array($j) && array_key_exists('allowed', $j)) {
			$j['allowed'] = (bool)$j['allowed'];
			$j['reachable'] = true;
			// The portal itself says the subscription has stopped (payment failed, trial ended, cancelled...)
			$j['portal_refused'] = !$j['allowed'];
			return $j;
		}
		if ($code === 401) return array('allowed' => false, 'reachable' => true, 'reason' => 'The portal did not accept the app secret. Check it on the Subscription settings.');
		return array('allowed' => false, 'reachable' => false, 'reason' => 'The subscription portal could not be reached' . ($err ? ' (' . $err . ')' : ($code ? ' (answer ' . $code . ')' : '')) . '.');
	}

	/**
	 * Current state: array('allowed' => bool, 'reason', 'subscribeUrl', 'accountUrl', 'validUntil', 'status', 'plan', 'checked_at', 'skipped' => bool)
	 * $force asks the portal even if the last answer is recent.
	 */
	public function state($force = false)
	{
		$r = $this->row();
		if (!$this->enabled($r)) return array('allowed' => true, 'skipped' => true);
		$c = $r['config'];
		$last = isset($c['last_result']) && is_array($c['last_result']) ? $c['last_result'] : null;
		$at = isset($c['last_check_at']) ? (int)$c['last_check_at'] : 0;
		if (!$force && $last && empty($last['portal_refused']) && time() - $at < self::FRESH_SEC) return $last + array('checked_at' => $at);

		$ans = $this->ask($r);
		if (!$ans['reachable']) {
			// Portal down: keep working on the last good answer for a day (asked again after a minute, so pages stay quick)
			$ok_at = isset($c['last_ok_at']) ? (int)$c['last_ok_at'] : 0;
			if ($last && !empty($last['allowed']) && time() - $ok_at < self::OFFLINE_OK_SEC) {
				$c['last_check_at'] = time();
				$this->db->where('id', (int)$r['id'])->update('ci_integration_settings', array('config_json' => json_encode($c)));
				return $last + array('checked_at' => time(), 'offline' => true);
			}
		}
		unset($ans['reachable']);
		// Remember this CRM's company the first time the portal names it, so later answers are always for it
		if (empty($c['company_id']) && !empty($ans['companyId'])) $c['company_id'] = (int)$ans['companyId'];
		$c['last_result'] = $ans;
		$c['last_check_at'] = time();
		if (!empty($ans['allowed'])) $c['last_ok_at'] = time();
		$this->db->where('id', (int)$r['id'])->update('ci_integration_settings', array('config_json' => json_encode($c)));
		return $ans + array('checked_at' => time());
	}

	/** The portal's renew page for this answer, only when it is on the portal set in Subscription (…/renew). */
	public function renew_url($st)
	{
		$r = $this->row();
		$portal = $r ? rtrim(trim((string)$r['endpoint']), '/') : '';
		$u = !empty($st['subscribeUrl']) ? (string)$st['subscribeUrl'] : '';
		if ($portal === '' || $u === '') return '';
		if ($u !== $portal . '/renew' && strpos($u, $portal . '/renew?') !== 0) return '';
		return $u;
	}

	/**
	 * A change pushed by the portal (POST api/subscription/events).
	 * Returns array(http code, message). Signature: X-Suite-Signature = "sha256=" HMAC-SHA256 of "<timestamp>.<raw body>"
	 * with the app secret; X-Suite-Timestamp no more than 5 minutes old.
	 */
	public function handle_event($raw, $ts, $sig)
	{
		$r = $this->row();
		if (!$r || trim((string)$r['api_secret']) === '') return array(404, 'Subscription is not set up.');
		$ts = (int)$ts;
		if ($ts <= 0 || abs(time() - $ts) > 300) return array(401, 'Old or missing timestamp.');
		$want = 'sha256=' . hash_hmac('sha256', $ts . '.' . $raw, trim($r['api_secret']));
		if (!is_string($sig) || !hash_equals($want, trim($sig))) return array(401, 'Bad signature.');

		$e = json_decode($raw, true);
		$d = is_array($e) && isset($e['data']) && is_array($e['data']) ? $e['data'] : null;
		if (!$d || !array_key_exists('allowed', $d)) return array(400, 'No subscription data.');
		$c = $r['config'];
		$product = !empty($c['product']) ? $c['product'] : 'crm';
		if (isset($d['product']) && $d['product'] !== $product) return array(200, 'Other product, ignored.');
		// Only this company's subscription counts (the account email set in Subscription)
		$mine = strtolower(trim(isset($c['account_email']) ? (string)$c['account_email'] : ''));
		$emails = isset($d['emails']) && is_array($d['emails']) ? array_map(function ($x) { return strtolower(trim((string)$x)); }, $d['emails']) : array();
		if ($mine === '' || !in_array($mine, $emails, true)) return array(200, 'Other company, ignored.');
		// The account email may own other companies too: only this CRM's company counts
		$cid = $this->company_id($r);
		if ($cid > 0 && isset($e['company']['id']) && (int)$e['company']['id'] !== $cid) return array(200, 'Other company, ignored.');

		if (empty($d['allowed'])) {
			$last = isset($c['last_result']) && is_array($c['last_result']) ? $c['last_result'] : array();
			$c['last_result'] = array(
				'allowed' => false, 'portal_refused' => true,
				'reason' => isset($d['reason']) ? (string)$d['reason'] : 'The subscription is not active.',
				'status' => isset($d['status']) ? (string)$d['status'] : '',
				'subscribeUrl' => isset($d['renewUrl']) ? (string)$d['renewUrl'] : '',
				'company' => isset($e['company']['name']) ? (string)$e['company']['name'] : (isset($last['company']) ? $last['company'] : ''),
				'logoUrl' => isset($last['logoUrl']) ? $last['logoUrl'] : '',
			);
		}
		// Either way the next page asks the portal again
		$c['last_check_at'] = 0;
		$this->db->where('id', (int)$r['id'])->update('ci_integration_settings', array('config_json' => json_encode($c)));
		return array(200, 'ok');
	}

	/**
	 * The portal companies the signed-in user belongs to, for the company menu:
	 * list of array(id, name, logoUrl, appUrl, current, allowed). Kept 5 minutes in the user's session.
	 * Links are kept only when they point to this portal's /go/ page.
	 */
	public function companies_for($email)
	{
		$email = strtolower(trim((string)$email));
		$r = $this->row();
		if ($email === '' || !$this->enabled($r)) return array();
		$cache = $this->session->userdata('crm_portal_companies');
		if (is_array($cache) && isset($cache['email'], $cache['at'], $cache['list']) && $cache['email'] === $email && time() - (int)$cache['at'] < 300) return $cache['list'];
		$ans = $this->ask($r, $email);
		$portal = rtrim(trim((string)$r['endpoint']), '/');
		$cid = $this->company_id($r);
		$list = array();
		if (!empty($ans['reachable']) && isset($ans['companies']) && is_array($ans['companies'])) {
			foreach (array_slice($ans['companies'], 0, 50) as $x) {
				if (!is_array($x) || empty($x['id'])) continue;
				$url = isset($x['appUrl']) ? (string)$x['appUrl'] : '';
				if (strpos($url, $portal . '/go/') !== 0) $url = '';
				$logo = isset($x['logoUrl']) ? (string)$x['logoUrl'] : '';
				if ($logo !== '' && !preg_match('~^' . preg_quote($portal, '~') . '/brand/[a-f0-9]{24}$~', $logo)) $logo = '';
				$list[] = array(
					'id' => (int)$x['id'],
					'name' => substr(trim((string)(isset($x['name']) ? $x['name'] : '')), 0, 120),
					'logoUrl' => $logo,
					'appUrl' => $url,
					'current' => (int)$x['id'] === $cid,
					'allowed' => !empty($x['allowed']),
				);
			}
		}
		$this->session->set_userdata('crm_portal_companies', array('email' => $email, 'at' => time(), 'list' => $list));
		return $list;
	}

	/**
	 * Where this company's own copies of HR & Payroll and E-Invoicing live (each client has its own copy).
	 * Asks GET <portal>/api/apps?company=<id> (app secret). Returns array('hr' => url or '', 'einvoicing' => url or ''),
	 * '' when the company has no copy yet; null when the portal could not be asked (not set up, or no answer).
	 * Kept 5 minutes in the user's session (1 minute when there was no answer), so pages stay quick.
	 */
	public function app_copies()
	{
		$r = $this->row();
		$cid = $r ? $this->company_id($r) : 0;
		if (!$this->enabled($r) || $cid <= 0) return null;
		$portal = rtrim(trim($r['endpoint']), '/');
		$cache = $this->session->userdata('crm_portal_apps');
		if (is_array($cache) && isset($cache['company'], $cache['portal'], $cache['at']) && array_key_exists('apps', $cache) && (int)$cache['company'] === $cid
			&& $cache['portal'] === $portal && time() - (int)$cache['at'] < ($cache['apps'] === null ? 60 : 300)) return $cache['apps'];
		$ch = curl_init($portal . '/api/apps?company=' . $cid);
		curl_setopt_array($ch, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 4,
			CURLOPT_CONNECTTIMEOUT => 3,
			CURLOPT_HTTPHEADER => array('Authorization: Bearer ' . trim($r['api_secret']), 'Accept: application/json'),
		));
		$body = curl_exec($ch);
		$code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		$j = $code === 200 && is_string($body) ? json_decode($body, true) : null;
		$apps = null;
		if (is_array($j) && isset($j['apps']) && is_array($j['apps']) && (int)(isset($j['company']) ? $j['company'] : 0) === $cid) {
			$apps = array();
			foreach (array('hr', 'einvoicing') as $k) {
				$a = isset($j['apps'][$k]) && is_array($j['apps'][$k]) ? $j['apps'][$k] : array();
				$u = !empty($a['ready']) && isset($a['appUrl']) && is_string($a['appUrl']) ? trim($a['appUrl']) : '';
				$apps[$k] = preg_match('~^https?://[^\s"\'<>]+$~i', $u) ? $u : '';
			}
		}
		$this->session->set_userdata('crm_portal_apps', array('company' => $cid, 'portal' => $portal, 'at' => time(), 'apps' => $apps));
		return $apps;
	}

	/** Days left on the subscription (null when unknown). */
	public function days_left($st)
	{
		if (empty($st['validUntil'])) return null;
		$t = strtotime($st['validUntil']);
		return $t ? (int)floor(($t - time()) / 86400) : null;
	}
}
