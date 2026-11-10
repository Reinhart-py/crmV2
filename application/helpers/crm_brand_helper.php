<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Whose CRM this is: the client's company name and logo, as entered when they signed up on the
 * subscription portal. Taken from the portal's last answer (Third-Party API > Subscription).
 * Until a client is linked, the CRM shows the plain "Sales CRM" mark.
 */

if (!function_exists('crm_brand')) {
	/** array('name' => company or 'Sales CRM', 'company' => company or '', 'logo' => client logo URL or '') */
	function crm_brand()
	{
		static $b = null;
		if ($b !== null) return $b;
		$b = array('name' => 'Sales CRM', 'company' => '', 'logo' => '');
		$ci =& get_instance();
		if (!isset($ci->db)) $ci->load->database();
		if (!$ci->db->table_exists('ci_integration_settings')) return $b;
		$r = $ci->db->select('endpoint, config_json')->where('service_name', 'subscription')->get('ci_integration_settings')->row_array();
		if (!$r) return $b;
		$c = json_decode((string)$r['config_json'], true);
		$last = (is_array($c) && isset($c['last_result']) && is_array($c['last_result'])) ? $c['last_result'] : array();
		$company = isset($last['company']) ? trim(substr((string)$last['company'], 0, 120)) : '';
		if ($company !== '') { $b['company'] = $company; $b['name'] = $company; }
		$b['logo'] = crm_brand_logo_ok(isset($last['logoUrl']) ? $last['logoUrl'] : '', $r['endpoint']);
		return $b;
	}
}

if (!function_exists('crm_brand_company')) {
	/** The client's company name from onboarding, or '' when no client is linked yet. */
	function crm_brand_company()
	{
		$b = crm_brand();
		return $b['company'];
	}
}

if (!function_exists('crm_brand_logo_ok')) {
	/** A logo is used only when the portal set in Subscription serves it (…/brand/<24 hex>). */
	function crm_brand_logo_ok($url, $endpoint)
	{
		$url = (string)$url;
		$portal = rtrim(trim((string)$endpoint), '/');
		if ($portal === '' || strpos($url, $portal . '/brand/') !== 0 || !preg_match('~/brand/[a-f0-9]{24}$~', $url)) return '';
		return $url;
	}
}

if (!function_exists('crm_brand_mark')) {
	/**
	 * The logo block: the client's logo, else the Sales CRM mark with the company name (or "Sales CRM").
	 * $size: 'header' (top bar / side pane), 'auth' (sign-in pages), 'tv' (live screen).
	 */
	function crm_brand_mark($size = 'header')
	{
		$b = crm_brand();
		$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
		$css = '';
		static $styled = false;
		if (!$styled) {
			$styled = true;
			$css = '<style>'
				. '.crm-mark{display:inline-flex;align-items:center;gap:9px;text-decoration:none;color:inherit;max-width:100%;}'
				. '.crm-mark img.crm-mark-ico{width:32px;height:32px;flex:none;border-radius:9px;}'
				. '.crm-mark b{font-family:"Plus Jakarta Sans","Inter",sans-serif;font-weight:800;font-size:16px;letter-spacing:-.01em;color:#1e3a8a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}'
				. '[data-theme="dark"] .crm-mark b{color:#e2e8f0;}'
				. '.crm-mark.is-auth img.crm-mark-ico{width:48px;height:48px;border-radius:13px;}.crm-mark.is-auth b{font-size:22px;}'
				. '.crm-mark.is-tv img.crm-mark-ico{width:40px;height:40px;}.crm-mark.is-tv b{font-size:18px;color:inherit;}'
				. 'img.crm-client-logo{display:block;width:auto;height:auto;object-fit:contain;max-width:100%;}'
				. '.crm-client-logo.is-header{max-height:40px;max-width:170px;}.crm-client-logo.is-auth{max-height:72px;max-width:220px;}.crm-client-logo.is-tv{max-height:44px;max-width:180px;}'
				. '</style>';
		}
		if ($b['logo'] !== '') {
			return $css . '<img class="crm-client-logo is-' . $h($size) . '" src="' . $h($b['logo']) . '" alt="' . $h($b['name']) . '">';
		}
		return $css . '<span class="crm-mark is-' . $h($size) . '" title="' . $h($b['name']) . '"><img class="crm-mark-ico" src="' . base_url() . 'assets/sales-crm-mark.svg" alt=""><b>' . $h($b['name']) . '</b></span>';
	}
}
