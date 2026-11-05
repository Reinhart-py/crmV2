<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$p = $p_info;
$can_write = !empty($can_write);
$val = function ($k) use ($p) {
	$v = isset($p[$k]) ? trim((string)$p[$k]) : '';
	if ($v === '' || $v === '0000-00-00' || $v === '0000-00-00 00:00:00') return '';
	return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
};
$phone = function ($code, $num) use ($val) {
	$n = $val($num);
	if ($n === '') return '';
	$c = $val($code);
	return ($c !== '' ? '+' . ltrim($c, '+') . ' ' : '') . $n;
};
// Expiry dates get a coloured note: expired, or how many days are left when 60 or fewer
$exp_alerts = array();
$expv = function ($k, $label) use ($val, &$exp_alerts) {
	$v = $val($k);
	if ($v === '' || !($t = strtotime($v))) return $v;
	$days = (int)floor(($t - strtotime(date('Y-m-d'))) / 86400);
	if ($days < 0) { $exp_alerts[] = $label . ' expired'; return $v . ' <span class="uv-chip off">Expired</span>'; }
	if ($days <= 60) { $exp_alerts[] = $label . ' expires in ' . $days . ' day' . ($days == 1 ? '' : 's'); return $v . ' <span class="uv-chip ' . ($days <= 30 ? 'warn' : 'soon') . '">' . $days . ' days left</span>'; }
	return $v;
};
// Contact fields: green "Verified" or amber "Not verified" next to a filled value
$vmark = function ($v, $k) use ($p) {
	if ($v === '') return '';
	if (empty($p[$k . '_verified'])) return $v . ' <span class="uv-chip warn">Not verified</span>';
	$tip = 'Verified' . (!empty($p[$k . '_verified_at']) ? ' on ' . date('d-M-Y', strtotime($p[$k . '_verified_at'])) : '');
	return $v . ' <span class="uv-chip on" title="' . htmlspecialchars($tip, ENT_QUOTES, 'UTF-8') . '"><i class="fa fa-check-circle"></i> Verified</span>';
};
// Reporting details: the chosen email / number with a green "Verified" mark (who and when on hover)
$who_cache = array();
$repv = function ($k) use ($val, $p, &$who_cache) {
	$v = $val($k);
	if ($v === '') return '';
	if (empty($p[$k . '_verified'])) return $v . ' <span class="uv-chip warn">Not verified</span>';
	$tip = 'Verified';
	if (!empty($p[$k . '_verified_at'])) $tip .= ' on ' . date('d-M-Y', strtotime($p[$k . '_verified_at']));
	return $v . ' <span class="uv-chip on" title="' . htmlspecialchars($tip, ENT_QUOTES, 'UTF-8') . '"><i class="fa fa-check-circle"></i> Verified</span>';
};
$manager_name = '';
if (!empty($p['manager'])) {
	$m = $this->db->select('name')->where('id', (int)$p['manager'])->get('ci_user')->row_array();
	if (!empty($m['name'])) $manager_name = htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8');
}
$offices = function_exists('crm_user_live_offices') ? crm_user_live_offices($p) : array();
// Sections in the same order as the form. Salary and bank details only for people who may edit staff.
$sections = array(
	'Login & Role' => array(
		'Login Email' => $val('c_username'), 'User Type' => $val('role_title'), 'Upline / Manager' => $manager_name,
		'Added On' => (!empty($p['created']) ? simple_date($p['created']) : ''),
	),
	'Personal Details' => array(
		'Name' => $val('name'), 'Gender' => $val('gender'), 'Date of Birth' => $val('dob'), 'Blood Group' => $val('blood'),
		'Email' => $vmark($val('email'), 'email'), 'Contact No' => $vmark($phone('country_code', 'mobile'), 'mobile'), 'WhatsApp No' => $vmark($phone('whatsapp_code', 'whatsapp'), 'wa'), 'Alternate No' => $vmark($phone('country_code2', 'uae_mobile'), 'alt'),
		'Address' => $val('address'), 'City' => $val('city'), 'Country' => $val('country_name'), 'State / Emirate' => $val('state_name'),
	),
	'Details for Reporting' => array(
		'Email' => $repv('report_email'), 'Phone No' => $repv('report_phone'), 'WhatsApp' => $repv('report_whatsapp'),
	),
	'Company Details' => array(
		'Employee ID' => $val('emp_id'), 'Designation' => $val('designation'), 'Department' => $val('department'), 'Office / Location' => $val('office'),
		'Date of Joining' => $val('c_doj'), 'Date of Leaving' => $val('c_dol'),
	),
	'Identity & Visa' => array(
		'Nationality' => $val('national'), 'Passport No' => $val('p_no'), 'Passport Expiry' => $expv('p_exdate', 'Passport'), 'Emirates ID' => $val('eid_no'), 'ID Expiry' => $expv('eid_expiry', 'Emirates ID'),
		'Visa Type' => $val('visa_type'), 'Visa Ref. No' => $val('visa_no'), 'Visa Issued By' => $val('visa_by'), 'Visa Expiry' => $expv('visa_expiry', 'Visa'),
	),
	'Emergency Contact' => array(
		'Contact Name' => $val('h_contact_name'), 'Relation' => $val('h_contact_relation'), 'Contact No' => $val('h_contact_no'), 'Alternate No' => $val('h_contact_no2'),
	),
);
if ($can_write) {
	$sections['Company Details']['Salary'] = $val('c_salary');
	$sections['Bank Details'] = array(
		'Bank Name' => $val('bank'), 'Account No' => $val('bank_no'), 'IBAN' => $val('iban'), 'SWIFT' => $val('swift'),
		'IFSC / Branch' => $val('ifsc'), 'Bank Country' => $val('bank_country'),
	);
}
if (!empty($offices)) $sections['Login & Role']['Office Codes'] = htmlspecialchars(implode(', ', $offices), ENT_QUOTES, 'UTF-8');
$filled = 0; $total = 0;
foreach ($sections as $rows) foreach ($rows as $v) { $total++; if ($v !== '') $filled++; }
$initials = '';
foreach (preg_split('/\s+/', trim((string)$p['name'])) as $w) { if ($w !== '' && strlen($initials) < 2) $initials .= strtoupper($w[0]); }
$enabled = ((int)$p['admin_status'] === 1);
?>
<style>
/* View User: one profile card + compact label/value grid, same groups as the form */
.uview .content{padding-top:0}
.uview .uv-wrap{max-width:1500px;margin:0 auto}
.uview .uv-card{background:var(--crm-surface,#fff);border:1px solid var(--crm-line,#e2e8f0);border-radius:10px;margin-bottom:10px}
.uview .uv-head{display:flex;align-items:center;gap:14px;padding:12px 14px;flex-wrap:wrap}
.uview .uv-av{width:52px;height:52px;border-radius:50%;background:var(--crm-ocean,#1e3a8a);color:#fff;display:flex;align-items:center;justify-content:center;font-size:19px;font-weight:700;flex:0 0 auto}
.uview .uv-id{flex:1 1 240px;min-width:0}
.uview .uv-id h2{margin:0;font-size:19px;font-weight:700;color:var(--crm-ink,#0f172a);text-transform:capitalize}
.uview .uv-meta{display:flex;flex-wrap:wrap;gap:6px 14px;margin-top:4px;font-size:12.5px;color:var(--crm-muted,#64748b)}
.uview .uv-meta i{margin-right:4px}
.uview .uv-chip{display:inline-block;padding:2px 9px;border-radius:999px;font-size:11.5px;font-weight:600;background:var(--crm-ocean-soft,rgba(30,58,138,.1));color:var(--crm-ocean,#1e3a8a)}
.uview .uv-chip.on{background:rgba(22,163,74,.12);color:#15803d}
.uview .uv-chip.off{background:rgba(220,38,38,.12);color:#b91c1c}
.uview .uv-chip.warn{background:rgba(245,158,11,.16);color:#b45309}
.uview .uv-chip.soon{background:rgba(234,179,8,.14);color:#854d0e}
.uview .uv-alert{margin:0 14px 10px;padding:7px 10px;border-radius:8px;background:rgba(220,38,38,.07);border:1px solid rgba(220,38,38,.22);font-size:12.5px;color:var(--crm-ink,#0f172a)}
.uview .uv-actions{display:flex;gap:8px;flex-wrap:wrap}
.uview .uv-actions .btn{height:32px;font-weight:600}
.uview .uv-fill{display:flex;align-items:center;gap:8px;font-size:12px;color:var(--crm-ink-2,#334155);padding:0 14px 10px}
.uview .uv-fill .bar{width:140px;height:6px;border-radius:3px;background:var(--crm-line,#e2e8f0);overflow:hidden}
.uview .uv-fill .bar span{display:block;height:100%;background:#f59e0b}
.uview .uv-fill.done .bar span{background:#16a34a}
.uview .uv-sec-h{padding:7px 14px;border-bottom:1px solid var(--crm-line-soft,#eef2f7);border-left:3px solid var(--crm-ocean,#1e3a8a);border-radius:10px 0 0 0;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--crm-ink,#0f172a);display:flex;justify-content:space-between}
.uview .uv-sec-h small{font-weight:600;text-transform:none;letter-spacing:0;color:var(--crm-muted,#64748b)}
.uview .uv-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:2px 14px;padding:8px 14px 10px;margin:0}
.uview .uv-grid > div{display:grid;grid-template-columns:112px minmax(0,1fr);gap:8px;align-items:baseline;padding:4px 0;border-bottom:1px dashed var(--crm-line-soft,#eef2f7)}
.uview .uv-grid dt{font-size:12px;font-weight:600;color:var(--crm-muted,#64748b);text-align:right}
.uview .uv-grid dd{margin:0;font-size:13px;color:var(--crm-ink,#0f172a);word-break:break-word}
.uview .uv-grid dd.empty{color:var(--crm-muted,#94a3b8);font-style:italic}
@media (max-width:1399px){.uview .uv-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media (max-width:991px){.uview .uv-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media (max-width:575px){.uview .uv-grid{grid-template-columns:1fr}.uview .uv-grid > div{grid-template-columns:110px minmax(0,1fr)}}
/* Presentation: one font, larger values, softer labels */
.uview, .uview .content-header h1, .uview .btn{font-family:"Plus Jakarta Sans","Inter",-apple-system,"Segoe UI",sans-serif}
.uview .content-header h1{text-align:left;font-size:22px;font-weight:700;letter-spacing:-.02em;color:var(--crm-ink,#0f172a)}
.uview .uv-id h2{font-size:21px;letter-spacing:-.02em}
.uview .uv-av{width:56px;height:56px;font-size:20px;letter-spacing:.02em}
.uview .uv-sec-h{font-size:12.5px;letter-spacing:.06em;padding:9px 14px}
.uview .uv-grid > div{padding:6px 0}
.uview .uv-grid dt{font-size:12.5px;font-weight:500}
.uview .uv-grid dd{font-size:13.5px;font-weight:500}
.uview .uv-grid dd.empty{font-weight:400}
</style>
<div class="content-wrapper uview">
  <section class="content-header">
    <h1>View User</h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?php echo base_url().$base; ?>"><?php echo ($base === 'manager/hr/staff') ? 'Staff' : 'Users'; ?></a></li>
      <li class="active">View</li>
    </ol>
  </section>
  <section class="content">
  <div class="uv-wrap">
    <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'),'message'); ?>
    <div class="uv-card">
      <div class="uv-head">
        <div class="uv-av"><?php echo htmlspecialchars($initials ? $initials : '?', ENT_QUOTES, 'UTF-8'); ?></div>
        <div class="uv-id">
          <h2><?php echo $val('name'); ?></h2>
          <div class="uv-meta">
            <span class="uv-chip"><?php echo sprintf('EMP-%05d', $p['id']); ?></span>
            <?php if ($val('role_title') !== '') { ?><span class="uv-chip"><?php echo $val('role_title'); ?></span><?php } ?>
            <span class="uv-chip <?php echo $enabled ? 'on' : 'off'; ?>"><?php echo $enabled ? 'Enabled' : 'Disabled'; ?></span>
            <?php if ($val('designation') !== '') { ?><span><i class="fa fa-briefcase"></i><?php echo $val('designation'); ?></span><?php } ?>
            <?php if ($val('department') !== '') { ?><span><i class="fa fa-building-o"></i><?php echo $val('department'); ?></span><?php } ?>
            <?php if ($val('office') !== '') { ?><span><i class="fa fa-map-marker"></i><?php echo $val('office'); ?></span><?php } ?>
            <?php if ($manager_name !== '') { ?><span><i class="fa fa-level-up"></i>Reports to <?php echo $manager_name; ?></span><?php } ?>
          </div>
        </div>
        <div class="uv-actions">
          <?php if ($can_write) { ?>
          <a href="<?php echo base_url().$base; ?>/edit/<?php echo (int)$p['id']; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i> Edit</a>
          <a href="<?php echo base_url().$base; ?>/cedit/<?php echo (int)$p['id']; ?>" class="btn btn-default"><i class="fa fa-key"></i> Login &amp; Password</a>
          <?php } ?>
          <a href="<?php echo base_url().$base; ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>
        </div>
      </div>
      <?php if ($exp_alerts) { ?><div class="uv-alert"><i class="fa fa-exclamation-triangle" style="color:#dc2626"></i> <b>Document expiry:</b> <?php echo htmlspecialchars(implode(' · ', $exp_alerts), ENT_QUOTES, 'UTF-8'); ?></div><?php } ?>
      <div class="uv-fill<?php echo ($filled === $total) ? ' done' : ''; ?>">
        <div class="bar"><span style="width:<?php echo $total ? round($filled * 100 / $total) : 0; ?>%"></span></div>
        <?php echo $filled; ?> of <?php echo $total; ?> details filled
      </div>
    </div>
    <?php foreach ($sections as $title => $rows) {
      $empty = 0; foreach ($rows as $v) if ($v === '') $empty++; ?>
    <div class="uv-card">
      <div class="uv-sec-h"><span><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></span><?php if ($empty) { ?><small><?php echo $empty; ?> empty</small><?php } ?></div>
      <dl class="uv-grid">
        <?php foreach ($rows as $label => $v) { ?>
        <div><dt><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></dt><dd<?php echo ($v === '') ? ' class="empty"' : ''; ?>><?php echo ($v === '') ? 'Not filled' : $v; ?></dd></div>
        <?php } ?>
      </dl>
    </div>
    <?php } ?>
  </div>
  </section>
</div>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
