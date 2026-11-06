<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$can_write = !empty($can_write);
$is_hr = ($base === 'manager/hr/staff');
$rows = (isset($product_list) && is_array($product_list)) ? $product_list : array();
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$pres_id = function_exists('crm_presentation_role_id') ? (int)crm_presentation_role_id() : 0;
$today = strtotime(date('Y-m-d'));
// Mandatory details (same * fields as the form) and all profile details used for "% filled"
$mandatory = array('name' => 'Name', 'email' => 'Email', 'dob' => 'Date of Birth', 'mobile' => 'Contact No', 'country' => 'Country', 'state' => 'State / Emirate',
	'emp_id' => 'Employee ID', 'c_doj' => 'Date of Joining', 'department' => 'Department', 'office' => 'Office / Location',
	'national' => 'Nationality', 'p_no' => 'Passport No', 'h_contact_name' => 'Emergency Contact Name', 'h_contact_no' => 'Emergency Contact No', 'designation' => 'Designation');
$profile_keys = array_merge(array_keys($mandatory), array('whatsapp', 'uae_mobile', 'gender', 'blood', 'address', 'city', 'p_exdate', 'eid_no', 'eid_expiry',
	'visa_type', 'visa_no', 'visa_by', 'visa_expiry', 'h_contact_relation', 'h_contact_no2'));
$docs = array('p_exdate' => 'Passport', 'visa_expiry' => 'Visa', 'eid_expiry' => 'Emirates ID');
$is_set = function ($li, $k) { $v = isset($li[$k]) ? trim((string)$li[$k]) : ''; return !($v === '' || $v === '0' || $v === '0000-00-00'); };

$list = array(); $alert = array('Expired' => 0, 'Within 30 days' => 0, 'Within 60 days' => 0);
$f = array('mgr' => array(), 'cat' => array(), 'des' => array(), 'dep' => array(), 'off' => array(), 'st' => array('Enabled' => 0, 'Disabled' => 0, 'Left' => 0),
	'exp' => array('Expired' => 0, 'Within 30 days' => 0, 'Within 60 days' => 0, 'Valid' => 0, 'No dates' => 0),
	'prof' => array('Missing mandatory details' => 0, 'Mandatory details complete' => 0),
	'rpt' => array('Email verified' => 0, 'Email not verified' => 0, 'Email not set' => 0, 'Phone verified' => 0, 'Phone not verified' => 0, 'Phone not set' => 0, 'WhatsApp verified' => 0, 'WhatsApp not verified' => 0, 'WhatsApp not set' => 0));
// Details for Reporting: each person has a state for email, phone and WhatsApp
$rep_keys = array('report_email' => 'Email', 'report_phone' => 'Phone', 'report_whatsapp' => 'WhatsApp');
$bump = function (&$arr, $k) { $arr[$k] = isset($arr[$k]) ? $arr[$k] + 1 : 1; };
foreach ($rows as $li) {
	$r = array('li' => $li);
	$r['on'] = ((int)$li['admin_status'] === 1);
	$r['left'] = !empty($li['left_on']);
	$r['stl'] = $r['left'] ? 'Left' : ($r['on'] ? 'Enabled' : 'Disabled');
	$r['mgr'] = trim((string)$li['manager_name']) !== '' ? trim($li['manager_name']) : '(No manager)';
	$r['cat'] = trim((string)$li['role']) !== '' ? trim($li['role']) : '(No role)';
	$r['des'] = (isset($li['designation']) && trim((string)$li['designation']) !== '') ? trim($li['designation']) : '(Not set)';
	$r['dep'] = trim((string)$li['department']) !== '' ? trim($li['department']) : '(Not set)';
	$r['off'] = trim((string)$li['office']) !== '' ? trim($li['office']) : '(Not set)';
	$r['pres'] = ($pres_id > 0 && (int)$li['user_type'] === $pres_id);
	// Documents: the soonest expiry decides the row's alert
	$r['docs'] = array(); $soon = null;
	foreach ($docs as $k => $label) {
		if (!$is_set($li, $k) || !($t = strtotime($li[$k]))) continue;
		$days = (int)floor(($t - $today) / 86400);
		$r['docs'][] = array($label, date('d-M-Y', $t), $days);
		if ($soon === null || $days < $soon[2]) $soon = array($label, date('d-M-Y', $t), $days);
	}
	$r['soon'] = $soon;
	if ($soon === null) $r['exp'] = 'No dates';
	elseif ($soon[2] < 0) $r['exp'] = 'Expired';
	elseif ($soon[2] <= 30) $r['exp'] = 'Within 30 days';
	elseif ($soon[2] <= 60) $r['exp'] = 'Within 60 days';
	else $r['exp'] = 'Valid';
	if (isset($alert[$r['exp']]) && $r['on']) $alert[$r['exp']]++;
	// Profile: % of details filled and which mandatory ones are missing
	$filled = 0; foreach ($profile_keys as $k) if ($is_set($li, $k)) $filled++;
	$r['pct'] = (int)round($filled * 100 / count($profile_keys));
	$r['missing'] = array(); foreach ($mandatory as $k => $label) if (!$is_set($li, $k)) $r['missing'][] = $label;
	$r['prof'] = $r['pres'] ? 'Mandatory details complete' : ($r['missing'] ? 'Missing mandatory details' : 'Mandatory details complete');
	$bump($f['mgr'], $r['mgr']); $bump($f['cat'], $r['cat']); $bump($f['des'], $r['des']); $bump($f['dep'], $r['dep']); $bump($f['off'], $r['off']);
	$f['st'][$r['stl']]++; $f['exp'][$r['exp']]++; $f['prof'][$r['prof']]++;
	$r['rpt'] = array();
	foreach ($rep_keys as $rk => $rl) {
		$set = isset($li[$rk]) && trim((string)$li[$rk]) !== '';
		$state = !$set ? 'not set' : (!empty($li[$rk . '_verified']) ? 'verified' : 'not verified');
		$r['rpt'][$rk] = array($rl, $set ? $li[$rk] : '', $state);
		$f['rpt'][$rl . ' ' . $state]++;
	}
	$list[] = $r;
}
foreach (array('mgr', 'cat', 'des', 'dep', 'off') as $k) ksort($f[$k], SORT_NATURAL | SORT_FLAG_CASE);
$filters = array('mgr' => 'Manager', 'cat' => 'Role', 'des' => 'Designation', 'st' => 'Status', 'dep' => 'Department', 'off' => 'Office', 'exp' => 'Expiry', 'prof' => 'Profile', 'rpt' => 'Reporting');
?>
<style>
/* View Users: compact list, sortable columns, tick-box filters, expiry alerts */
.ulist .content-header{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px 15px 6px}
.ulist .content-header h1{font-size:20px;margin:0}
.ulist .content-header .hd-btns{display:flex;gap:8px}
.ulist .content-header .btn{height:32px;font-weight:600}
.ulist .content{padding-top:0}
.ulist .ul-card{background:var(--crm-surface,#fff);border:1px solid var(--crm-line,#e2e8f0);border-radius:10px;padding:0}
.ulist .ul-alert{display:flex;align-items:center;flex-wrap:wrap;gap:8px;margin:0 0 10px;padding:8px 12px;border-radius:10px;border:1px solid rgba(220,38,38,.25);background:rgba(220,38,38,.06);font-size:13px;color:var(--crm-ink,#0f172a)}
.ulist .ul-alert.warn{border-color:rgba(217,119,6,.3);background:rgba(245,158,11,.08)}
.ulist .ul-alert b{margin-right:4px}
.ulist .ul-alert a{cursor:pointer;padding:2px 9px;border-radius:999px;font-weight:600;font-size:12px;text-decoration:none}
.ulist .ul-alert a.x{background:rgba(220,38,38,.14);color:#b91c1c}
.ulist .ul-alert a.w{background:rgba(245,158,11,.18);color:#b45309}
.ulist .ul-alert a.y{background:rgba(234,179,8,.16);color:#854d0e}
.ulist .ul-alert small{color:var(--crm-muted,#64748b);margin-left:auto}
.ulist .ul-bar{display:flex;align-items:center;flex-wrap:wrap;gap:6px;padding:9px 12px;border-bottom:1px solid var(--crm-line-soft,#eef2f7)}
.ulist .ul-bar .lbl{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--crm-muted,#64748b);margin-right:2px}
.ulist .ul-f{position:relative}
.ulist .ul-f > button{height:30px;padding:0 10px;border:1px solid var(--crm-line,#cbd5e1);border-radius:6px;background:var(--crm-canvas,#f8fafc);color:var(--crm-ink,#0f172a);font-size:12.5px;font-weight:600;display:flex;align-items:center;gap:6px}
.ulist .ul-f > button .n{display:none;min-width:18px;height:18px;padding:0 5px;border-radius:9px;background:var(--crm-ocean,#1e3a8a);color:#fff;font-size:11px;line-height:18px;text-align:center}
.ulist .ul-f.active > button{border-color:var(--crm-ocean,#1e3a8a);color:var(--crm-ocean,#1e3a8a)}
.ulist .ul-f.active > button .n{display:inline-block}
.ulist .ul-pop{display:none;position:absolute;top:34px;left:0;z-index:30;min-width:230px;max-width:300px;background:var(--crm-surface,#fff);border:1px solid var(--crm-line,#e2e8f0);border-radius:8px;box-shadow:0 10px 24px rgba(15,23,42,.14);padding:6px}
.ulist .ul-f.open .ul-pop{display:block}
.ulist .ul-pop .q{width:100%;height:28px;border:1px solid var(--crm-line,#cbd5e1);border-radius:5px;padding:0 8px;font-size:12.5px;margin-bottom:4px;background:var(--crm-canvas,#f8fafc);color:inherit}
.ulist .ul-pop .opts{max-height:260px;overflow:auto}
.ulist .ul-pop label{display:flex;align-items:center;gap:7px;margin:0;padding:4px 6px;border-radius:5px;font-size:12.5px;font-weight:500;cursor:pointer;color:var(--crm-ink,#0f172a)}
.ulist .ul-pop label:hover{background:var(--crm-ocean-soft,rgba(30,58,138,.08))}
.ulist .ul-pop label .c{margin-left:auto;color:var(--crm-muted,#64748b);font-size:11.5px}
.ulist .ul-pop .ft{display:flex;justify-content:space-between;border-top:1px solid var(--crm-line-soft,#eef2f7);margin-top:4px;padding:5px 4px 1px;font-size:12px}
.ulist .ul-pop .ft a{cursor:pointer}
.ulist .ul-clear{font-size:12px;cursor:pointer;display:none}
.ulist .ul-count{margin-left:auto;font-size:12px;color:var(--crm-muted,#64748b)}
.ulist .ul-body{padding:6px 12px 10px}
.ulist table.dataTable{margin-top:4px !important;border-collapse:collapse !important}
.ulist #example_user thead th{font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:7px 22px 7px 8px !important;white-space:nowrap}
.ulist #example_user tbody td{padding:5px 8px !important;font-size:13px;vertical-align:middle;border-color:var(--crm-line-soft,#eef2f7) !important}
.ulist #example_user tbody tr:hover td{background:var(--crm-ocean-soft,rgba(30,58,138,.05))}
.ulist .emp{font-family:ui-monospace,Consolas,monospace;font-size:12px;color:var(--crm-ocean,#1e3a8a);font-weight:600;white-space:nowrap}
.ulist .nm{font-weight:600;color:var(--crm-ink,#0f172a);text-transform:capitalize}
.ulist .nm:hover{color:var(--crm-ocean,#1e3a8a)}
.ulist .lg{display:block;font-size:11.5px;color:var(--crm-muted,#64748b);line-height:1.2}
.ulist .chip{display:inline-block;padding:1px 8px;border-radius:999px;font-size:11.5px;font-weight:600;white-space:nowrap;background:var(--crm-ocean-soft,rgba(30,58,138,.1));color:var(--crm-ocean,#1e3a8a)}
.ulist .chip.on{background:rgba(22,163,74,.12);color:#15803d}
.ulist .chip.off,.ulist .chip.x{background:rgba(220,38,38,.12);color:#b91c1c}
.ulist .chip.w{background:rgba(245,158,11,.16);color:#b45309}
.ulist .chip.y{background:rgba(234,179,8,.14);color:#854d0e}
.ulist .chip.ok{background:rgba(22,163,74,.1);color:#15803d}
.ulist .chip.na{background:transparent;color:var(--crm-muted,#94a3b8);font-weight:500;padding:0}
.ulist .pf{display:flex;align-items:center;gap:6px;white-space:nowrap;font-size:12px}
.ulist .pf .bar{width:46px;height:5px;border-radius:3px;background:var(--crm-line,#e2e8f0);overflow:hidden}
.ulist .pf .bar span{display:block;height:100%;background:#f59e0b}
.ulist .pf.full .bar span{background:#16a34a}
.ulist .pf .miss{color:#b91c1c;font-weight:600}
.ulist .pf .rp{display:inline-flex;gap:5px;margin-left:4px;font-size:12.5px}
.ulist .pf .rp .not-set{color:var(--crm-line,#cbd5e1)}
.ulist .pf .rp .not-verified{color:#d97706}
.ulist .pf .rp .verified{color:#16a34a}
.ulist .acts{display:flex;gap:4px;white-space:nowrap}
.ulist .acts a{width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;border:1px solid var(--crm-line,#e2e8f0);color:var(--crm-ink-2,#334155);background:var(--crm-surface,#fff)}
.ulist .acts a:hover{border-color:var(--crm-ocean,#1e3a8a);color:var(--crm-ocean,#1e3a8a)}
.ulist .acts a.danger:hover{border-color:#dc2626;color:#dc2626}
.ulist .dataTables_wrapper .dataTables_filter input,.ulist .dataTables_wrapper .dataTables_length select{height:30px;border-radius:6px}
@media (max-width:767px){.ulist .ul-count{margin-left:0;width:100%}.ulist .ul-pop{min-width:200px}.ulist .ul-alert small{margin-left:0;width:100%}}
/* Presentation: one font, clear title, summary tiles, left-aligned readable table */
.ulist, .ulist .content-header h1, .ulist table, .ulist .btn, .ulist input, .ulist select{font-family:"Plus Jakarta Sans","Inter",-apple-system,"Segoe UI",sans-serif}
.ulist .content-header{padding:14px 15px 10px}
.ulist .content-header h1{text-align:left;font-size:22px;font-weight:700;letter-spacing:-.02em;line-height:1.2;color:var(--crm-ink,#0f172a)}
.ulist .content-header h1 small{display:block;margin-top:3px;font-size:12.5px;font-weight:500;letter-spacing:0;color:var(--crm-muted,#64748b)}
.ulist .ul-kpis{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:10px;margin:0 0 10px}
.ulist .ul-kpi{display:flex;align-items:center;gap:12px;padding:11px 14px;border-radius:10px;border:1px solid var(--crm-line,#e2e8f0);background:var(--crm-surface,#fff);cursor:pointer;transition:border-color .15s,box-shadow .15s;text-align:left;width:100%;font:inherit;color:inherit}
.ulist .ul-kpi:hover{border-color:var(--crm-ocean,#1e3a8a);box-shadow:0 4px 14px rgba(15,23,42,.06)}
.ulist .ul-kpi .ic{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:15px;flex:0 0 auto;background:var(--crm-ocean-soft,rgba(30,58,138,.1));color:var(--crm-ocean,#1e3a8a)}
.ulist .ul-kpi.g .ic{background:rgba(22,163,74,.12);color:#15803d}
.ulist .ul-kpi.r .ic{background:rgba(220,38,38,.1);color:#b91c1c}
.ulist .ul-kpi.a .ic{background:rgba(245,158,11,.14);color:#b45309}
.ulist .ul-kpi.s .ic{background:rgba(100,116,139,.14);color:#475569}
.ulist .chip.lft{background:rgba(100,116,139,.14);color:#475569;border-color:rgba(100,116,139,.3)}
.ulist .ul-kpi b{display:block;font-size:20px;font-weight:700;line-height:1.1;letter-spacing:-.02em;font-variant-numeric:tabular-nums;color:var(--crm-ink,#0f172a)}
.ulist .ul-kpi span{display:block;font-size:12px;font-weight:500;color:var(--crm-muted,#64748b);margin-top:2px}
@media (max-width:991px){.ulist .ul-kpis{grid-template-columns:repeat(2,minmax(0,1fr))}}
.ulist #example_user thead th,.ulist #example_user tbody td{text-align:left !important}
.ulist #example_user thead th{font-size:11px;letter-spacing:.06em;padding:9px 22px 9px 10px !important}
.ulist #example_user tbody td{padding:8px 10px !important;font-size:13px;line-height:1.35;font-variant-numeric:tabular-nums}
.ulist #example_user tbody tr:nth-child(even) td{background:var(--crm-canvas,#fafbfd)}
.ulist .who{display:flex;align-items:center;gap:10px;min-width:0}
.ulist .av{width:32px;height:32px;border-radius:50%;flex:0 0 auto;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;letter-spacing:.02em;color:#fff;background:var(--crm-ocean,#1e3a8a)}
.ulist .who > div{min-width:0}
.ulist .nm{font-size:13.5px;font-weight:600;line-height:1.25}
.ulist .lg{font-size:12px;margin-top:1px}
.ulist .emp{font-size:12px;letter-spacing:.02em}
.ulist .chip{font-size:11.5px;padding:2px 9px}
.ulist #example_user.dtr-inline.collapsed > tbody > tr > td.dtr-control{padding-left:34px !important;position:relative}
.ulist .chip.m-st{display:none}
.ulist input.pick{width:16px;height:16px;margin:0 2px 0 0;flex:0 0 auto;cursor:pointer;vertical-align:middle;accent-color:var(--crm-ocean,#1e3a8a)}
.ulist .ul-bulk{display:flex;flex-wrap:wrap;align-items:center;gap:6px;margin:4px 0 8px;padding:8px 10px;border-radius:8px;background:var(--crm-ocean-soft,rgba(30,58,138,.08));border:1px solid rgba(30,58,138,.25)}
.ulist .ul-bulk b{font-size:13.5px;margin-right:4px}
.ulist .ul-bulk .btn{height:30px;font-weight:600}
.ulist .ul-bulk .grp{display:inline-flex;gap:4px}
.ulist .ul-bulk select{height:30px;min-width:190px;font-size:13px}
.ulist .ul-bulk a{cursor:pointer;font-size:13px;margin-left:4px}
.ulist #example_user tbody tr.picked td{background:var(--crm-ocean-soft,rgba(30,58,138,.07)) !important}
@media (max-width:767px){.ulist .ul-bulk .grp{width:100%}.ulist .ul-bulk select{flex:1;min-width:0}}
.ulist .clip{display:inline-block;margin-left:6px;font-size:12.5px;font-weight:600;color:var(--crm-ocean,#1e3a8a);white-space:nowrap}
/* Readability: key fields (ID, name, manager, status) larger; secondary details stay smaller and muted */
.ulist #example_user thead th{font-size:12px}
.ulist #example_user tbody td{font-size:14px;padding:9px 10px !important}
.ulist .emp{font-size:13px}
.ulist .nm{font-size:15px;font-weight:700}
.ulist .av{width:36px;height:36px;font-size:13px}
.ulist .lg{font-size:12.5px}
.ulist .chip{font-size:12.5px;padding:2px 10px}
.ulist .chip.na{font-size:12.5px}
.ulist .pf{font-size:13px}
.ulist .pf .rp{font-size:14px}
.ulist .acts a{width:30px;height:30px;font-size:14px}
.ulist .ul-f > button{font-size:13px;height:32px}
.ulist .ul-pop label{font-size:13.5px}
.ulist .ul-count,.ulist .ul-clear{font-size:13px}
.ulist .dataTables_wrapper .dataTables_info,.ulist .dataTables_wrapper .dataTables_paginate,.ulist .dataTables_wrapper .dataTables_length,.ulist .dataTables_wrapper .dataTables_filter{font-size:13px}
.ulist .ul-kpi span{font-size:13px}
.ulist .ul-kpi b{font-size:22px}
@media (max-width:767px){
  .ulist .content-header{flex-wrap:wrap;padding:12px 12px 8px}
  .ulist .content-header h1{font-size:19px}
  .ulist .content-header h1 small{display:block;margin-top:2px}
  .ulist .content-header .hd-btns{width:100%}
  .ulist .content-header .hd-btns .btn{flex:1}
  .ulist .content{padding-left:10px;padding-right:10px}
  .ulist .ul-kpis{gap:8px}
  .ulist .ul-kpi{padding:9px 10px;gap:9px}
  .ulist .ul-kpi .ic{width:32px;height:32px;font-size:14px}
  .ulist .ul-kpi b{font-size:19px}
  .ulist .ul-kpi span{font-size:12px}
  .ulist .ul-bar{padding:8px 10px}
  .ulist .ul-pop{position:fixed;left:12px !important;right:12px;top:auto;max-width:none;min-width:0}
  .ulist .ul-body{padding:6px 8px 10px}
  .ulist .dataTables_wrapper .dataTables_length,.ulist .dataTables_wrapper .dataTables_filter{text-align:left;float:none}
  .ulist .dataTables_wrapper .dataTables_filter input{width:100%;margin-left:0}
  .ulist .nm{font-size:14.5px}
  .ulist .ul-alert{font-size:12.5px}
  .ulist .ul-kpis .ul-kpi:last-child{grid-column:1 / -1}
  .ulist .ul-f > button{height:30px;font-size:12.5px;padding:0 8px}
  .ulist .ul-bar{gap:5px}
  .ulist .av{width:30px;height:30px;font-size:11.5px}
  .ulist .who{gap:8px}
  .ulist .lg{word-break:break-all}
  .ulist #example_user tbody td{padding:8px 6px !important}
  .ulist .chip.m-st{display:inline-block;margin-top:3px;font-size:11px;padding:1px 8px}
  .ulist .acts{display:grid;grid-template-columns:repeat(3,28px);gap:3px}
  .ulist .acts a{width:28px;height:28px;font-size:13px}
}
/* HR > Staff: compact figures */
.ulist.ul-hr .content-header{padding-top:8px;padding-bottom:6px}
.ulist.ul-hr .ul-kpis{display:flex;flex-wrap:wrap;gap:6px;margin:0 0 8px}
.ulist.ul-hr .ul-kpi{flex:1 1 auto;width:auto;gap:8px;padding:6px 12px;border-radius:8px}
.ulist.ul-hr .ul-kpi .ic{width:26px;height:26px;border-radius:7px;font-size:12px}
.ulist.ul-hr .ul-kpi > div:last-child{display:flex;align-items:baseline;gap:6px}
.ulist.ul-hr .ul-kpi b{font-size:17px}
.ulist.ul-hr .ul-kpi span{margin:0;font-size:12px;white-space:nowrap}
@media (max-width:767px){.ulist.ul-hr .ul-kpi{flex:1 1 45%}.ulist.ul-hr .ul-kpi .ic{display:none}.ulist.ul-hr .ul-kpi span{white-space:normal}}
</style>
<div class="content-wrapper ulist<?php echo !empty($is_hr) ? " ul-hr" : ""; ?>">
  <?php if ($is_hr) { $ul_tabs = crm_tabbar('hr_people', 'hr/staff'); if ($ul_tabs) { ?><link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_8"><div class="sx ul-tabs"><?php echo $ul_tabs; ?></div><?php } } ?>
  <section class="content-header">
    <h1>Staff</h1>
    <div class="hd-btns">
      <?php if (!empty($list)) { ?><button type="button" class="btn btn-default js-export" title="Download the list as it is filtered now (opens in Excel)"><i class="fa fa-file-excel-o"></i> Export</button><?php } ?>
      <?php if ($can_write) { ?><a href="<?php echo base_url().$base; ?>/add" class="btn btn-primary"><i class="fa fa-user-plus"></i> Add Staff</a><?php } ?>
    </div>
  </section>
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'),'message'); ?>
    <?php if (!empty($list)) { ?>
    <div class="ul-kpis">
      <button type="button" class="ul-kpi js-kpi" data-f="" title="Show everyone"><div class="ic"><i class="fa fa-users"></i></div><div><b><?php echo count($list); ?></b><span>Total staff</span></div></button>
      <button type="button" class="ul-kpi g js-kpi" data-f="st" data-v="Enabled" title="Show enabled only"><div class="ic"><i class="fa fa-check-circle"></i></div><div><b><?php echo (int)$f['st']['Enabled']; ?></b><span>Enabled</span></div></button>
      <button type="button" class="ul-kpi r js-kpi" data-f="st" data-v="Disabled" title="Show disabled only"><div class="ic"><i class="fa fa-ban"></i></div><div><b><?php echo (int)$f['st']['Disabled']; ?></b><span>Disabled</span></div></button>
      <button type="button" class="ul-kpi s js-kpi" data-f="st" data-v="Left" title="Show people who have left"><div class="ic"><i class="fa fa-sign-out"></i></div><div><b><?php echo (int)$f['st']['Left']; ?></b><span>Left</span></div></button>
      <button type="button" class="ul-kpi a js-kpi" data-f="prof" data-v="Missing mandatory details" title="Show who still has mandatory details to fill"><div class="ic"><i class="fa fa-pencil-square-o"></i></div><div><b><?php echo (int)$f['prof']['Missing mandatory details']; ?></b><span>Missing mandatory details</span></div></button>
    </div>
    <?php } ?>
    <?php if (array_sum($alert) > 0) { ?>
    <div class="ul-alert<?php echo $alert['Expired'] ? '' : ' warn'; ?>">
      <b><i class="fa fa-exclamation-triangle"></i> Document expiry:</b>
      <?php if ($alert['Expired']) { ?><a class="x js-exp" data-v="Expired"><?php echo $alert['Expired']; ?> expired</a><?php } ?>
      <?php if ($alert['Within 30 days']) { ?><a class="w js-exp" data-v="Within 30 days"><?php echo $alert['Within 30 days']; ?> within 30 days</a><?php } ?>
      <?php if ($alert['Within 60 days']) { ?><a class="y js-exp" data-v="Within 60 days"><?php echo $alert['Within 60 days']; ?> within 60 days</a><?php } ?>
      <small>Passport, visa and Emirates ID of enabled staff. Click to see who.</small>
    </div>
    <?php } ?>
    <div class="ul-card">
      <?php if (!empty($list)) { ?>
      <div class="ul-bar">
        <span class="lbl"><i class="fa fa-filter"></i> Filter</span>
        <?php foreach ($filters as $fk => $fname) { ?>
        <div class="ul-f" data-f="<?php echo $fk; ?>">
          <button type="button"><?php echo $fname; ?> <span class="n">0</span> <i class="fa fa-caret-down"></i></button>
          <div class="ul-pop">
            <?php if (count($f[$fk]) > 8) { ?><input type="text" class="q" placeholder="Find <?php echo strtolower($fname); ?>..."><?php } ?>
            <div class="opts">
              <?php foreach ($f[$fk] as $opt => $cnt) { if ($fk === 'exp' && !$cnt) continue; ?>
              <label><input type="checkbox" value="<?php echo $h($opt); ?>"> <span><?php echo $h($opt); ?></span> <span class="c"><?php echo (int)$cnt; ?></span></label>
              <?php } ?>
            </div>
            <div class="ft"><a class="all">Tick all</a><a class="none">Clear</a></div>
          </div>
        </div>
        <?php } ?>
        <a class="ul-clear">Clear filters</a>
        <span class="ul-count"></span>
      </div>
      <div class="ul-body">
          <?php if ($can_write) { ?>
          <form class="ul-bulk js-bulk" method="post" action="<?php echo base_url().$base; ?>/bulk" style="display:none">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="bulk_action" value="">
            <span class="ids"></span>
            <b class="js-bulk-n">0 selected</b>
            <button type="button" class="btn btn-default btn-sm js-bulk-do" data-a="enable"><i class="fa fa-check"></i> Enable</button>
            <button type="button" class="btn btn-default btn-sm js-bulk-do" data-a="disable"><i class="fa fa-ban"></i> Disable</button>
            <span class="grp"><select name="manager" class="form-control input-sm"><option value="">Change manager to...</option><?php foreach ($bulk_managers as $bm) { ?><option value="<?php echo (int)$bm['id']; ?>"><?php echo $h($bm['name']); ?></option><?php } ?></select><button type="button" class="btn btn-default btn-sm js-bulk-do" data-a="manager">Apply</button></span>
            <span class="grp"><select name="role" class="form-control input-sm"><option value="">Change role to...</option><?php foreach ($bulk_roles as $br) { ?><option value="<?php echo (int)$br['id']; ?>"><?php echo $h($br['title']); ?></option><?php } ?></select><button type="button" class="btn btn-default btn-sm js-bulk-do" data-a="role">Apply</button></span>
            <button type="button" class="btn btn-default btn-sm js-bulk-export"><i class="fa fa-file-excel-o"></i> Export selected</button>
            <a class="js-bulk-clear">Clear</a>
          </form>
          <?php } ?>
        <table id="example_user" class="table table-hover" style="width:100%">
          <thead>
            <tr>
              <th>Emp. No</th>
              <th><?php if ($can_write) { ?><input type="checkbox" class="pick js-pick-all" title="Tick everyone shown by the filters"> <?php } ?>Name / Login</th>
              <th>Manager</th>
              <th>Role / Designation</th>
              <th>Profile</th>
              <th>Documents</th>
              <th>Added</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($list as $r) { $li = $r['li']; $id = (int)$li['id'];
              $exp_csv = array(); foreach ($r['docs'] as $d) $exp_csv[$d[0]] = $d[1];
              $export = array('emp' => sprintf('EMP-%05d', $id), 'name' => $li['name'], 'login' => $li['c_username'], 'emp_id' => $li['emp_id'], 'mgr' => $li['manager_name'], 'cat' => $li['role'], 'des' => ($r['des'] === '(Not set)' ? '' : $r['des']),
                'dep' => $li['department'], 'off' => $li['office'], 'status' => $r['stl'] . ($r['left'] ? ' on ' . date('d-M-Y', strtotime($li['left_on'])) . ' (' . $li['left_reason'] . ')' : ''), 'pct' => $r['pres'] ? '' : $r['pct'] . '%',
                'missing' => $r['pres'] ? '' : implode(', ', $r['missing']), 'pp' => isset($exp_csv['Passport']) ? $exp_csv['Passport'] : '',
                'visa' => isset($exp_csv['Visa']) ? $exp_csv['Visa'] : '', 'eid' => isset($exp_csv['Emirates ID']) ? $exp_csv['Emirates ID'] : '', 'added' => simple_date($li['created']),
                'wa' => (isset($li['whatsapp']) && $li['whatsapp'] !== '' ? (($li['whatsapp_code'] !== '' ? '+' . $li['whatsapp_code'] . ' ' : '') . $li['whatsapp']) : ''),
                're' => $r['rpt']['report_email'][1], 'rev' => $r['rpt']['report_email'][2], 'rp' => $r['rpt']['report_phone'][1], 'rpv' => $r['rpt']['report_phone'][2], 'rw' => $r['rpt']['report_whatsapp'][1], 'rwv' => $r['rpt']['report_whatsapp'][2]);
              $rpt_attr = array(); foreach ($r['rpt'] as $x) $rpt_attr[] = $x[0] . ' ' . $x[2]; ?>
            <tr data-mgr="<?php echo $h($r['mgr']); ?>" data-cat="<?php echo $h($r['cat']); ?>" data-des="<?php echo $h($r['des']); ?>" data-st="<?php echo $r['stl']; ?>" data-dep="<?php echo $h($r['dep']); ?>" data-off="<?php echo $h($r['off']); ?>" data-exp="<?php echo $h($r['exp']); ?>" data-prof="<?php echo $h($r['prof']); ?>" data-rpt="<?php echo $h(implode('|', $rpt_attr)); ?>" data-row="<?php echo $h(json_encode($export)); ?>">
              <td><span class="emp"><?php echo sprintf('EMP-%05d', $id); ?></span></td>
              <td data-order="<?php echo $h(strtolower($li['name'])); ?>"><?php $ini = ''; foreach (preg_split('/\s+/', trim((string)$li['name'])) as $w) { if ($w !== '' && strlen($ini) < 2) $ini .= strtoupper($w[0]); } $avc = array('#1e3a8a','#0f766e','#7c3aed','#b45309','#be185d','#0369a1','#4d7c0f','#9f1239'); ?><div class="who"><?php if ($can_write) { ?><input type="checkbox" class="pick js-pick" value="<?php echo $id; ?>" aria-label="Tick <?php echo $h($li['name']); ?>"><?php } ?><span class="av" style="background:<?php echo $avc[abs(crc32((string)$li['name'])) % count($avc)]; ?>"><?php echo $h($ini !== '' ? $ini : '?'); ?></span><div><a class="nm" href="<?php echo base_url().$base; ?>/view/<?php echo $id; ?>"><?php echo $h($li['name']); ?></a><span class="lg"><?php echo $h($li['c_username']); ?></span><span class="chip m-st <?php echo $r['left'] ? 'lft' : ($r['on'] ? 'on' : 'off'); ?>"><?php echo $r['stl']; ?></span></div></div></td>
              <td><?php echo $h($li['manager_name']); ?></td>
              <td><span class="chip"><?php echo $h($li['role']); ?></span><?php if ($r['des'] !== '(Not set)') { ?><span class="lg"><?php echo $h($r['des']); ?></span><?php } ?></td>
              <?php if ($r['pres']) { ?>
              <td data-order="101"><span class="chip na">Not needed</span></td>
              <?php } else { ?>
              <td data-order="<?php echo $r['pct']; ?>"><div class="pf<?php echo $r['missing'] ? '' : ' full'; ?>"<?php if ($r['missing']) { ?> title="<?php echo $h('Missing: ' . implode(', ', $r['missing'])); ?>"<?php } ?>><div class="bar"><span style="width:<?php echo $r['pct']; ?>%"></span></div><?php echo $r['pct']; ?>%<?php if ($r['missing']) { ?> <span class="miss"><?php echo count($r['missing']); ?> missing</span><?php } ?><?php $ri = array('report_email' => 'fa-envelope', 'report_phone' => 'fa-phone', 'report_whatsapp' => 'fa-whatsapp'); ?><span class="rp"><?php foreach ($r['rpt'] as $rk => $x) { ?><i class="fa <?php echo $ri[$rk]; ?> <?php echo str_replace(' ', '-', $x[2]); ?>" title="<?php echo $h('Reporting ' . $x[0] . ': ' . ($x[1] !== '' ? $x[1] . ' (' . $x[2] . ')' : 'not set')); ?>"></i><?php } ?></span></div></td>
              <?php } ?>
              <?php if ($r['soon'] === null) { ?>
              <td data-order="999999"><span class="chip na">No dates</span><?php if ($can_write && !empty($doc_counts[$id])) { ?><span class="clip" title="<?php echo $h('Files: ' . $doc_counts[$id]['t']); ?>"><i class="fa fa-paperclip"></i> <?php echo (int)$doc_counts[$id]['n']; ?></span><?php } ?></td>
              <?php } else {
                $d = $r['soon']; $cls = ($d[2] < 0) ? 'x' : (($d[2] <= 30) ? 'w' : (($d[2] <= 60) ? 'y' : 'ok'));
                $txt = ($d[2] < 0) ? $d[0] . ' expired' : (($d[2] <= 60) ? $d[0] . ' in ' . $d[2] . ' day' . ($d[2] == 1 ? '' : 's') : 'Valid');
                $tip = array(); foreach ($r['docs'] as $x) $tip[] = $x[0] . ': ' . $x[1] . ($x[2] < 0 ? ' (expired)' : ''); ?>
              <td data-order="<?php echo $d[2] + 100000; ?>"><span class="chip <?php echo $cls; ?>" title="<?php echo $h(implode(' | ', $tip)); ?>"><?php echo $h($txt); ?></span><?php if ($can_write && !empty($doc_counts[$id])) { ?><span class="clip" title="<?php echo $h('Files: ' . $doc_counts[$id]['t']); ?>"><i class="fa fa-paperclip"></i> <?php echo (int)$doc_counts[$id]['n']; ?></span><?php } ?></td>
              <?php } ?>
              <td data-order="<?php echo date('YmdHis', strtotime($li['created'])); ?>" style="white-space:nowrap"><?php echo simple_date($li['created']); ?></td>
              <td><?php if ($r['left']) { ?><span class="chip lft" title="<?php echo $h('Left on ' . date('d-M-Y', strtotime($li['left_on'])) . ' - ' . $li['left_reason']); ?>">Left</span><span class="lg"><?php echo date('d-M-Y', strtotime($li['left_on'])); ?></span><?php } else { ?><span class="chip <?php echo $r['on'] ? 'on' : 'off'; ?>"><?php echo $r['on'] ? 'Enabled' : 'Disabled'; ?></span><?php } ?></td>
              <td>
                <div class="acts">
                  <a href="<?php echo base_url().$base; ?>/view/<?php echo $id; ?>" title="View details"><i class="fa fa-eye"></i></a>
                  <?php if ($can_write) { ?>
                  <a href="<?php echo base_url().$base; ?>/edit/<?php echo $id; ?>" title="Edit user"><i class="fa fa-pencil"></i></a>
                  <a href="<?php echo base_url().$base; ?>/cedit/<?php echo $id; ?>" title="Change login or password"><i class="fa fa-key"></i></a>
                  <?php if (!$r['left']) { ?>
                  <a href="javascript:void(0);" class="js-status" data-id="<?php echo $id; ?>" data-admin_status="<?php echo $r['on'] ? 1 : 0; ?>" title="<?php echo $r['on'] ? 'Disable user' : 'Enable user'; ?>"><i class="fa <?php echo $r['on'] ? 'fa-ban' : 'fa-check'; ?>"></i></a>
                  <?php if ($id !== (int)$this->session->userdata('manager_id')) { ?><a href="javascript:void(0);" class="js-left" data-id="<?php echo $id; ?>" data-name="<?php echo $h($li['name']); ?>" title="Mark as left (exit)"><i class="fa fa-sign-out"></i></a><?php } ?>
                  <?php } ?>
                  <a href="<?php echo base_url().$base; ?>/delete/<?php echo $id; ?>" class="danger" onclick="return do_confirm();" title="Delete user"><i class="fa fa-trash"></i></a>
                  <?php } ?>
                </div>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <?php } else { ?>
      <div class="alert alert-info" style="margin:12px">No users found.</div>
      <?php } ?>
    </div>
  </section>
</div>
<script>
function do_confirm(){
  return confirm("Are you sure to delete this user?");
}
$(document).ready(function(){
  if (!$("#example_user").length) return;
  // Tick-box filters: a row shows when it matches every filter that has something ticked
  var picked = {};
  // A row may carry several values (e.g. Reporting: "Email verified|Phone not set"); any ticked one matches
  function hit(list, attr){ return (attr || '').split('|').some(function(v){ return list.indexOf(v) !== -1; }); }
  $('.ul-f').each(function(){ picked[$(this).data('f')] = []; });
  $.fn.dataTable.ext.search.push(function(settings, data, idx){
    if (settings.nTable.id !== 'example_user') return true;
    var tr = settings.aoData[idx].nTr;
    for (var k in picked) {
      if (picked[k].length && !hit(picked[k], tr.getAttribute('data-' + k))) return false;
    }
    return true;
  });
  var table = $("#example_user").DataTable({
    pageLength: 50,
    order: [[0, 'desc']],
    autoWidth: false,
    responsive: true,
    columnDefs: [
      { targets: [8], orderable: false, searchable: false },
      // On phones the name, status and actions stay visible; the rest opens under the row
      { targets: [1], responsivePriority: 1 }, { targets: [8], responsivePriority: 2 }, { targets: [7], responsivePriority: 3 }, { targets: [0], responsivePriority: 4 }, { targets: [2], responsivePriority: 5 }, { targets: [3], responsivePriority: 6 }, { targets: [4], responsivePriority: 7 }, { targets: [5], responsivePriority: 8 }, { targets: [6], responsivePriority: 10 }
    ]
  });
  function count(){
    var shown = table.rows({search: 'applied'}).count(), all = table.rows().count();
    $('.ul-count').text(shown === all ? all + ' users' : shown + ' of ' + all + ' users');
  }
  // Each filter only offers what is left after the other filters: counts follow the other ticks,
  // and choices with nothing left are hidden (ticked ones stay so they can be unticked).
  function refreshOptions(){
    var nodes = table.rows().nodes().toArray();
    $('.ul-f').each(function(){
      var k = $(this).data('f'), counts = {};
      nodes.forEach(function(tr){
        for (var j in picked) {
          if (j !== k && picked[j].length && !hit(picked[j], tr.getAttribute('data-' + j))) return;
        }
        (tr.getAttribute('data-' + k) || '').split('|').forEach(function(v){ counts[v] = (counts[v] || 0) + 1; });
      });
      $(this).find('.opts label').each(function(){
        var inp = $(this).find('input')[0], n = counts[inp.value] || 0;
        $(this).find('.c').text(n);
        $(this).toggleClass('zero', n === 0 && !inp.checked);
      });
      showOpts($(this));
    });
  }
  function showOpts(f){
    var q = (f.find('.q').val() || '').toLowerCase();
    f.find('.opts label').each(function(){
      $(this).toggle(!$(this).hasClass('zero') && $(this).text().toLowerCase().indexOf(q) !== -1);
    });
  }
  function apply(){
    var any = false;
    $('.ul-f').each(function(){
      var k = $(this).data('f');
      picked[k] = $(this).find('.opts input:checked').map(function(){ return this.value; }).get();
      $(this).toggleClass('active', picked[k].length > 0).find('.n').text(picked[k].length);
      if (picked[k].length) any = true;
    });
    $('.ul-clear').toggle(any);
    table.draw();
    refreshOptions();
    count();
  }
  $('.ul-f > button').on('click', function(e){
    e.stopPropagation();
    var f = $(this).parent();
    $('.ul-f').not(f).removeClass('open');
    f.toggleClass('open');
    f.find('.q').trigger('focus');
  });
  $('.ul-pop').on('click', function(e){ e.stopPropagation(); });
  $(document).on('click', function(){ $('.ul-f').removeClass('open'); });
  $(document).on('keydown', function(e){ if (e.key === 'Escape') $('.ul-f').removeClass('open'); });
  $('.ul-pop .opts input').on('change', apply);
  $('.ul-pop .all').on('click', function(){ $(this).closest('.ul-pop').find('.opts label:visible input').prop('checked', true); apply(); });
  $('.ul-pop .none').on('click', function(){ $(this).closest('.ul-pop').find('.opts input').prop('checked', false); apply(); });
  $('.ul-clear').on('click', function(){ $('.ul-pop .opts input').prop('checked', false); apply(); });
  $('.ul-pop .q').on('input', function(){
    var q = this.value.toLowerCase();
    showOpts($(this).closest('.ul-f'));
  });
  // Summary tiles: show that group only (Total clears the filters)
  $('.js-kpi').on('click', function(){
    $('.ul-pop .opts input').prop('checked', false);
    var k = $(this).data('f'), v = $(this).data('v');
    if (k) $('.ul-f[data-f=' + k + '] .opts input').each(function(){ if (this.value === v) this.checked = true; });
    apply();
    $('html, body').animate({ scrollTop: $('.ul-card').offset().top - 70 }, 200);
  });
  // Expiry banner: show those people (enabled staff only)
  $('.js-exp').on('click', function(){
    $('.ul-pop .opts input').prop('checked', false);
    var v = $(this).data('v');
    $('.ul-f[data-f=exp] .opts input').each(function(){ if (this.value === v) this.checked = true; });
    $('.ul-f[data-f=st] .opts input').each(function(){ if (this.value === 'Enabled') this.checked = true; });
    apply();
  });
  table.on('search.dt', function(){ setTimeout(count, 0); });
  count();
  refreshOptions();

  // Export what is shown now (filters and search applied) as a CSV file that opens in Excel
  function exportRows(nodes, suffix){
    var cols = [['emp','Emp. No'],['name','Name'],['login','Login'],['emp_id','Employee ID'],['mgr','Manager'],['cat','Role'],['des','Designation'],['dep','Department'],['off','Office / Location'],
      ['status','Status'],['pct','Profile filled'],['missing','Missing mandatory details'],['pp','Passport Expiry'],['visa','Visa Expiry'],['eid','Emirates ID Expiry'],['added','Added'],['wa','WhatsApp No'],
      ['re','Reporting Email'],['rev','Email check'],['rp','Reporting Phone'],['rpv','Phone check'],['rw','Reporting WhatsApp'],['rwv','WhatsApp check']];
    var esc = function(v){ v = (v == null ? '' : String(v)); if (/^[=+\-@]/.test(v)) v = "'" + v; return '"' + v.replace(/"/g, '""') + '"'; };
    var lines = [cols.map(function(c){ return esc(c[1]); }).join(',')];
    $.each(nodes, function(i, tr){
      var d = JSON.parse(tr.getAttribute('data-row') || '{}');
      lines.push(cols.map(function(c){ return esc(d[c[0]]); }).join(','));
    });
    var blob = new Blob(['﻿' + lines.join('\r\n')], {type: 'text/csv;charset=utf-8'});
    var a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = '<?php echo $is_hr ? 'staff' : 'users'; ?>' + (suffix || '') + '-' + new Date().toISOString().slice(0, 10) + '.csv';
    document.body.appendChild(a); a.click();
    setTimeout(function(){ URL.revokeObjectURL(a.href); a.remove(); }, 500);
  }
  $('.js-export').on('click', function(){ exportRows(table.rows({search: 'applied', order: 'applied'}).nodes().toArray()); });

  // Bulk actions: tick people (the header box ticks everyone the filters show, on every page), then act on them together
  var picked = {};
  function pickedRows(){ return table.rows({order: 'applied'}).nodes().toArray().filter(function(tr){ var c = tr.querySelector('.js-pick'); return c && picked[c.value]; }); }
  function paint(){
    var n = Object.keys(picked).length, f = $('.js-bulk');
    table.rows().nodes().each(function(tr){ var c = tr.querySelector('.js-pick'); if (c) { c.checked = !!picked[c.value]; tr.classList.toggle('picked', c.checked); } });
    f.toggle(n > 0); f.find('.js-bulk-n').text(n + ' selected');
    var vis = table.rows({search: 'applied'}).nodes().toArray(), all = vis.length && vis.every(function(tr){ var c = tr.querySelector('.js-pick'); return !c || picked[c.value]; });
    $('.js-pick-all').prop('checked', !!(n && all)).prop('indeterminate', !!(n && !all));
  }
  $(document).on('click', '.js-pick, .js-pick-all', function(e){ e.stopPropagation(); });
  $(document).on('change', '.js-pick', function(){ if (this.checked) picked[this.value] = 1; else delete picked[this.value]; paint(); });
  $('.js-pick-all').on('change', function(){
    var on = this.checked;
    table.rows({search: 'applied'}).nodes().each(function(tr){ var c = tr.querySelector('.js-pick'); if (c) { if (on) picked[c.value] = 1; else delete picked[c.value]; } });
    paint();
  });
  table.on('draw', paint);
  $('.js-bulk-clear').on('click', function(){ picked = {}; paint(); });
  $('.js-bulk-export').on('click', function(){ exportRows(pickedRows(), '-selected'); });
  $('.js-bulk-do').on('click', function(){
    var f = $('.js-bulk'), a = $(this).data('a'), ids = Object.keys(picked), n = ids.length;
    if (!n) return;
    if (a === 'manager' && !f.find('[name=manager]').val()) { f.find('[name=manager]').focus(); return; }
    if (a === 'role' && !f.find('[name=role]').val()) { f.find('[name=role]').focus(); return; }
    var what = a === 'enable' ? 'Enable' : (a === 'disable' ? 'Disable' : (a === 'role' ? 'Give the role ' + f.find('[name=role] option:selected').text() + ' to' : 'Move to ' + f.find('[name=manager] option:selected').text() + ':'));
    if (!confirm(what + ' ' + n + (n == 1 ? ' person' : ' people') + '?')) return;
    f.find('[name=bulk_action]').val(a);
    f.find('.ids').html(ids.map(function(id){ return '<input type="hidden" name="ids[]" value="' + parseInt(id, 10) + '">'; }).join(''));
    f[0].submit();
  });

  $(document).on("click", ".js-status", function(){
    var id = $(this).data("id"), st = $(this).data("admin_status");
    if (!confirm(st == 1 ? "Are you sure to disable this user?" : "Are you sure to enable this user?")) return;
    $.ajax({
      type: "POST",
      url: "<?php echo base_url(); ?>ci_admin_user/ajax_edit_status",
      data: { admin_status: st, id: id, <?php echo $this->security->get_csrf_token_name(); ?>: "<?php echo $this->security->get_csrf_hash(); ?>" },
      success: function(result){
        if ($.trim(result) === 'ok') location.reload();
        else alert("Error: Unable to update status");
      }
    });
  });
});
</script>
<?php if ($can_write) require(APPPATH . "views/manager/user/_left_modal.php"); ?>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
