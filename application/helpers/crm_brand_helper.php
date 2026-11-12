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
		$b = array('name' => 'Sales CRM', 'company' => '', 'logo' => '', 'portal' => '', 'plan' => '', 'status' => '', 'valid_until' => '', 'account_url' => '');
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
		$b['portal'] = rtrim(trim((string)$r['endpoint']), '/');
		foreach (array('plan' => 'plan', 'status' => 'status', 'valid_until' => 'validUntil') as $k => $src) {
			$b[$k] = isset($last[$src]) && is_scalar($last[$src]) ? substr((string)$last[$src], 0, 40) : '';
		}
		$acc = isset($last['accountUrl']) ? (string)$last['accountUrl'] : '';
		if ($b['portal'] !== '' && strpos($acc, $b['portal'] . '/') === 0) $b['account_url'] = $acc;
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

if (!function_exists('crm_app_mark')) {
	/** Top bar and side pane: the app itself (Sales CRM icon and name); the company sits in the company menu. */
	function crm_app_mark()
	{
		return '<span class="crm-app-mark" title="Sales CRM"><img class="crm-app-ico" src="' . base_url() . 'assets/sales-crm-mark.svg" alt=""><b>Sales CRM</b></span>';
	}
}

if (!function_exists('crm_suite_apps')) {
	/** The other apps of the suite, opened from the company menu. Links go to each app's page on the portal. */
	function crm_suite_apps()
	{
		$b = crm_brand();
		if ($b['portal'] === '') return array();
		$p = $b['portal'];
		$hr = $p . '/hr';
		$ci =& get_instance();
		$r = $ci->db->select('endpoint')->where('service_name', 'hr_link')->get('ci_integration_settings')->row_array();
		if ($r && preg_match('~^https?://~i', trim((string)$r['endpoint']))) $hr = rtrim(trim((string)$r['endpoint']), '/') . '/';
		return array(
			array('crm', 'Sales CRM', 'fa-line-chart', '#12a594', base_url() . 'manager/dashboard', true),
			array('hr', 'HR & Payroll', 'fa-users', '#7a5af5', $hr, false),
			array('accounting', 'Accounting', 'fa-calculator', '#0b6fa4', $p . '/accounting', false),
			array('einvoicing', 'E-Invoicing', 'fa-file-text-o', '#e0861a', $p . '/einvoicing', false),
		);
	}
}

if (!function_exists('crm_org_switcher')) {
	/**
	 * Company menu in the top bar (right side): the client's company, its plan for admins, and the other suite apps.
	 * Shown only when the CRM is linked to the subscription portal.
	 */
	function crm_org_switcher($is_admin = false)
	{
		$b = crm_brand();
		if ($b['portal'] === '' || $b['company'] === '') return '';
		$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
		$initial = strtoupper(mb_substr($b['company'], 0, 1));
		$badge = $b['logo'] !== '' ? '<img class="crm-org-logo" src="' . $h($b['logo']) . '" alt="">' : '<span class="crm-org-initial">' . $h($initial) . '</span>';
		$plan = '';
		if ($is_admin) {
			$bits = array();
			if ($b['plan'] !== '') $bits[] = ucfirst($b['plan']) . ' plan';
			if ($b['status'] === 'trialing') $bits[] = 'trial';
			$t = $b['valid_until'] !== '' ? strtotime($b['valid_until']) : 0;
			if ($t) $bits[] = ($b['status'] === 'trialing' ? 'ends ' : 'renews ') . date('d M Y', $t);
			$plan = implode(' · ', $bits);
		}
		$o = '<div class="crm-org" id="crmOrg">'
			. '<button type="button" class="crm-org-btn" id="crmOrgBtn" aria-haspopup="true" aria-expanded="false" title="' . $h($b['company']) . '">'
			. $badge . '<span class="crm-org-name">' . $h($b['company']) . '</span><i class="fa fa-angle-down crm-org-caret" aria-hidden="true"></i></button>'
			. '<div class="crm-org-menu" id="crmOrgMenu" role="menu">'
			. '<div class="crm-org-head">' . $badge . '<div><b>' . $h($b['company']) . '</b>' . ($plan !== '' ? '<small>' . $h($plan) . '</small>' : '') . '</div><i class="fa fa-check crm-org-tick" title="Current company"></i></div>';
		$apps = crm_suite_apps();
		if ($apps) {
			$o .= '<div class="crm-org-label">Apps</div><div class="crm-org-apps">';
			foreach ($apps as $a) {
				$o .= '<a class="crm-org-app' . ($a[5] ? ' is-on' : '') . '" href="' . $h($a[4]) . '"' . ($a[5] ? '' : ' target="_blank" rel="noopener"') . '>'
					. '<span class="ico" style="background:' . $h($a[3]) . '"><i class="fa ' . $h($a[2]) . '"></i></span><span>' . $h($a[1]) . '</span></a>';
			}
			$o .= '</div>';
		}
		if ($is_admin) {
			$o .= '<a class="crm-org-link" href="' . $h($b['account_url'] !== '' ? $b['account_url'] : $b['portal']) . '" target="_blank" rel="noopener"><i class="fa fa-id-card-o"></i> Company account and subscription</a>';
		}
		$o .= '</div></div>';
		return $o;
	}
}
