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
 * The answer is kept for an hour; if the portal can't be reached, the last good answer counts for 24 hours.
 */
class Subscription_model extends CI_Model
{
	const SERVICE = 'subscription';
	const FRESH_SEC = 3600;
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

	/** Ask the portal now. Returns the portal's answer plus 'reachable'. */
	public function ask($r)
	{
		$product = !empty($r['config']['product']) ? $r['config']['product'] : 'crm';
		$url = rtrim(trim($r['endpoint']), '/') . '/api/entitlement?product=' . rawurlencode($product) . '&email=' . rawurlencode(strtolower(trim($r['config']['account_email'])));
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
		if (!$force && $last && time() - $at < self::FRESH_SEC) return $last + array('checked_at' => $at);

		$ans = $this->ask($r);
		if (!$ans['reachable']) {
			// Portal down: keep working on the last good answer for a day
			$ok_at = isset($c['last_ok_at']) ? (int)$c['last_ok_at'] : 0;
			if ($last && !empty($last['allowed']) && time() - $ok_at < self::OFFLINE_OK_SEC) return $last + array('checked_at' => $at, 'offline' => true);
		}
		unset($ans['reachable']);
		$c['last_result'] = $ans;
		$c['last_check_at'] = time();
		if (!empty($ans['allowed'])) $c['last_ok_at'] = time();
		$this->db->where('id', (int)$r['id'])->update('ci_integration_settings', array('config_json' => json_encode($c)));
		return $ans + array('checked_at' => time());
	}

	/** Days left on the subscription (null when unknown). */
	public function days_left($st)
	{
		if (empty($st['validUntil'])) return null;
		$t = strtotime($st['validUntil']);
		return $t ? (int)floor(($t - time()) / 86400) : null;
	}
}
