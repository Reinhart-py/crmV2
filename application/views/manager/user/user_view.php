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
		'Login Email' => $val('c_username'), 'Role' => $val('role_title'), 'Upline / Manager' => $manager_name,
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
.uview .uv-docs{padding:8px 14px 12px}
.uview .uv-doclist{list-style:none;margin:0 0 10px;padding:0}
.uview .uv-doclist li{display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--crm-line-soft,#eef2f7)}
.uview .uv-doclist .ic{width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;flex:0 0 auto}
.uview .uv-doclist .ic.pdf{background:rgba(220,38,38,.1);color:#b91c1c}
.uview .uv-doclist .ic.img{background:rgba(30,58,138,.1);color:#1e3a8a}
.uview .uv-doclist .tx{flex:1;min-width:0}
.uview .uv-doclist .tx b{display:block;font-size:14px}
.uview .uv-doclist .tx small{display:block;font-size:12px;color:var(--crm-muted,#64748b);word-break:break-all}
.uview .uv-doclist .ac{display:flex;gap:4px;flex:0 0 auto}
.uview .uv-doclist .ac form{margin:0}
.uview .uv-doclist .ac a,.uview .uv-doclist .ac button{width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;border:1px solid var(--crm-line,#e2e8f0);background:var(--crm-surface,#fff);color:var(--crm-ink-2,#334155);padding:0}
.uview .uv-doclist .ac button:hover{border-color:#dc2626;color:#dc2626}
.uview .uv-docnone{font-size:13px;color:var(--crm-muted,#64748b);margin:4px 0 10px}
.uview .uv-docup{display:flex;flex-wrap:wrap;gap:8px;align-items:center}
.uview .uv-docup select{width:200px;height:34px}
.uview .uv-docup input[type=file]{flex:1;min-width:200px;height:34px;padding:5px 8px}
.uview .uv-docup .btn{height:34px;font-weight:600}
.uview .uv-docup .hint{width:100%;font-size:12px;color:var(--crm-muted,#64748b)}
.uview .uv-hist{padding:8px 14px 12px}
.uview .uv-hbar{margin-bottom:8px}
.uview .uv-hbar .form-control{width:auto;max-width:100%;min-width:220px;height:34px;font-size:13.5px;display:inline-block}
.uview .uv-tl{list-style:none;margin:0 0 8px;padding:0 0 0 14px;border-left:2px solid var(--crm-line,#e2e8f0)}
.uview .uv-tl > li{position:relative;padding:4px 0 10px 10px}
.uview .uv-tl > li:before{content:'';position:absolute;left:-21px;top:9px;width:12px;height:12px;border-radius:50%;background:var(--crm-surface,#fff);border:2px solid var(--crm-ocean,#1e3a8a)}
.uview .uv-tl .when{font-size:12.5px;color:var(--crm-muted,#64748b);margin-bottom:3px}
.uview .uv-tl .when b{color:var(--crm-ink,#0f172a);font-weight:700}
.uview .uv-tl .chg{font-size:13.5px;line-height:1.5;word-break:break-word}
.uview .uv-tl .chg .fl{font-weight:600;margin-right:6px}
.uview .uv-tl .chg .old{color:#b91c1c;text-decoration:line-through;text-decoration-color:rgba(185,28,28,.45)}
.uview .uv-tl .chg .new{color:#15803d;font-weight:600}
.uview .uv-tl .chg i{color:var(--crm-muted,#94a3b8);margin:0 3px}
.uview .uv-alink{margin-left:8px;font-weight:600;white-space:nowrap}
@media (max-width:767px){.uview .uv-docup select,.uview .uv-docup input[type=file],.uview .uv-docup .btn{width:100%;flex:none}}
.uview .uv-chip.lft{background:rgba(100,116,139,.14);color:#475569;border-color:rgba(100,116,139,.3)}
.uview .uv-left{margin:0 14px 10px;padding:9px 12px;border-radius:8px;background:rgba(100,116,139,.08);border:1px solid rgba(100,116,139,.25);font-size:13px;display:flex;gap:12px;justify-content:space-between;align-items:flex-start;flex-wrap:wrap}
.uview .uv-left .nt{margin-top:3px;color:var(--crm-ink-2,#334155)}
.uview .uv-left .by{margin-top:3px;font-size:12px;color:var(--crm-muted,#64748b)}
.uview .uv-left .ow{margin-top:5px;font-size:12.5px;color:#b45309}
.uview .uv-left .ow a{font-weight:600}
.uview .uv-left .btn{height:32px;font-weight:600}
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
    <h1>Staff Profile</h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?php echo base_url().$base; ?>"><?php echo ($base === 'manager/hr/staff') ? 'Staff' : 'Staff'; ?></a></li>
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
            <?php if (!empty($exit)) { ?><span class="uv-chip lft">Left</span><?php } else { ?><span class="uv-chip <?php echo $enabled ? 'on' : 'off'; ?>"><?php echo $enabled ? 'Enabled' : 'Disabled'; ?></span><?php } ?>
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
          <?php if (empty($exit) && empty($is_self)) { ?><a href="javascript:void(0);" class="btn btn-default js-left" data-id="<?php echo (int)$p['id']; ?>" data-name="<?php echo $val('name'); ?>" style="color:#b91c1c"><i class="fa fa-sign-out"></i> Mark as left</a><?php } ?>
          <?php } ?>
          <a href="<?php echo base_url().$base; ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>
        </div>
      </div>
      <?php if (!empty($exit)) {
        $own = !empty($owned['total']) ? (int)$owned['total'] : 0; ?>
      <div class="uv-left">
        <div><i class="fa fa-sign-out"></i> <b>Left on <?php echo date('d-M-Y', strtotime($exit['left_on'])); ?></b> &middot; <?php echo htmlspecialchars($exit['reason'], ENT_QUOTES, 'UTF-8'); ?>
          <?php if ($exit['note'] !== '') { ?><div class="nt"><?php echo nl2br(htmlspecialchars($exit['note'], ENT_QUOTES, 'UTF-8')); ?></div><?php } ?>
          <div class="by">Marked<?php echo $exit['marked_by_name'] !== '' ? ' by ' . htmlspecialchars($exit['marked_by_name'], ENT_QUOTES, 'UTF-8') : ''; ?><?php echo !empty($exit['marked_at']) ? ' on ' . date('d-M-Y H:i', strtotime($exit['marked_at'])) : ''; ?>. Login is switched off.</div>
          <?php if ($own > 0) { ?><div class="ow"><i class="fa fa-exclamation-triangle"></i> Still owns <?php echo $own; ?> record(s) (<?php echo (int)$owned['leads']; ?> leads, <?php echo (int)$owned['kam']; ?> database, <?php echo (int)$owned['tasks']; ?> tasks).
            <?php if ($this->session->userdata('manager_role') == 1) { ?><a href="<?php echo base_url(); ?>manager/database-transfer?from_user=<?php echo (int)$p['id']; ?>">Transfer them</a><?php } else { ?>Ask an admin to transfer them.<?php } ?></div><?php } ?>
        </div>
        <?php if ($can_write) { ?>
        <form method="post" action="<?php echo base_url().$base; ?>/rejoin/<?php echo (int)$p['id']; ?>" onsubmit="return confirm('Bring this person back? The leaving record is removed and the login is enabled.');">
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
          <button type="submit" class="btn btn-default"><i class="fa fa-undo"></i> Rejoin</button>
        </form>
        <?php } ?>
      </div>
      <?php } ?>
      <?php if ($exp_alerts) { ?><div class="uv-alert"><i class="fa fa-exclamation-triangle" style="color:#dc2626"></i> <b>Document expiry:</b> <?php echo htmlspecialchars(implode(' · ', $exp_alerts), ENT_QUOTES, 'UTF-8'); ?><?php if ($can_write) { foreach (array('Passport', 'Visa', 'Emirates ID') as $xt) { $hit = false; foreach ($exp_alerts as $ea) if (strpos($ea, $xt . ' ') === 0) $hit = true; if ($hit && !empty($docs)) foreach ($docs as $dd) { if ($dd['doc_type'] === $xt) { ?> <a class="uv-alink" href="<?php echo base_url().$base; ?>/doc_file/<?php echo (int)$dd['id']; ?>" target="_blank" rel="noopener"><i class="fa fa-paperclip"></i> <?php echo $xt; ?> file</a><?php break; } } } } ?></div><?php } ?>
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
    <?php if ($can_write) {
      $doc_by_type = array(); foreach ($docs as $dd) if (!isset($doc_by_type[$dd['doc_type']])) $doc_by_type[$dd['doc_type']] = $dd; ?>
    <div class="uv-card" id="docs">
      <div class="uv-sec-h"><span>Documents</span><small><?php echo count($docs); ?> file<?php echo count($docs) == 1 ? '' : 's'; ?></small></div>
      <div class="uv-docs">
        <?php if ($docs) { ?>
        <ul class="uv-doclist">
          <?php foreach ($docs as $dd) { $isPdf = ($dd['mime'] === 'application/pdf'); ?>
          <li>
            <span class="ic <?php echo $isPdf ? 'pdf' : 'img'; ?>"><i class="fa <?php echo $isPdf ? 'fa-file-pdf-o' : 'fa-file-image-o'; ?>"></i></span>
            <span class="tx"><b><?php echo htmlspecialchars($dd['doc_type'], ENT_QUOTES, 'UTF-8'); ?></b>
              <small><?php echo htmlspecialchars($dd['orig_name'], ENT_QUOTES, 'UTF-8'); ?> &middot; <?php echo $dd['size'] >= 1024 * 100 ? round($dd['size'] / 1048576, 2) . ' MB' : max(1, round($dd['size'] / 1024)) . ' KB'; ?> &middot; <?php echo date('d-M-Y', strtotime($dd['uploaded_at'])); ?><?php echo !empty($dd['uploader']) ? ' by ' . htmlspecialchars($dd['uploader'], ENT_QUOTES, 'UTF-8') : ''; ?></small></span>
            <span class="ac">
              <a href="<?php echo base_url().$base; ?>/doc_file/<?php echo (int)$dd['id']; ?>" target="_blank" rel="noopener" title="Open"><i class="fa fa-eye"></i></a>
              <a href="<?php echo base_url().$base; ?>/doc_file/<?php echo (int)$dd['id']; ?>?dl=1" title="Download"><i class="fa fa-download"></i></a>
              <form method="post" action="<?php echo base_url().$base; ?>/doc_delete/<?php echo (int)$dd['id']; ?>" onsubmit="return confirm('Delete this document?');"><input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>"><button type="submit" title="Delete"><i class="fa fa-trash"></i></button></form>
            </span>
          </li>
          <?php } ?>
        </ul>
        <?php } else { ?><p class="uv-docnone">No documents yet.</p><?php } ?>
        <form class="uv-docup" method="post" enctype="multipart/form-data" action="<?php echo base_url().$base; ?>/doc_upload/<?php echo (int)$p['id']; ?>">
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
          <select name="doc_type" class="form-control" required><option value="">Document type</option><?php foreach ($doc_types as $dt) { ?><option><?php echo htmlspecialchars($dt, ENT_QUOTES, 'UTF-8'); ?></option><?php } ?></select>
          <input type="file" name="doc" class="form-control" accept=".jpg,.jpeg,.png,.webp,.pdf,image/jpeg,image/png,image/webp,application/pdf" required>
          <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> Upload</button>
          <div class="hint">JPG, PNG or PDF, <b>max 1 MB</b> each. Large photos are made smaller automatically; a PDF over 1 MB has to be compressed first.</div>
        </form>
      </div>
    </div>
    <script>
    // Keep every upload at or under 1 MB: photos are scaled down in the browser, other files over 1 MB are refused.
    (function () {
      var MAX = 1048576, form = document.querySelector('.uv-docup'); if (!form) return;
      var inp = form.querySelector('[name=doc]'), hint = form.querySelector('.hint'), base = hint.innerHTML, busy = false;
      function say(t, bad) { hint.innerHTML = t; hint.style.color = bad ? '#b91c1c' : ''; }
      function shrink(file, cb) {
        var img = new Image(), url = URL.createObjectURL(file);
        img.onload = function () {
          var tries = [[2000, .82], [1600, .78], [1400, .72], [1200, .66], [1000, .6]], i = 0;
          (function next() {
            if (i >= tries.length) { URL.revokeObjectURL(url); return cb(null); }
            var m = tries[i][0], q = tries[i][1], k = Math.min(1, m / Math.max(img.width, img.height)), c = document.createElement('canvas');
            c.width = Math.round(img.width * k); c.height = Math.round(img.height * k);
            var g = c.getContext('2d'); g.fillStyle = '#fff'; g.fillRect(0, 0, c.width, c.height); g.drawImage(img, 0, 0, c.width, c.height);
            c.toBlob(function (b) { i++; if (b && b.size <= MAX) { URL.revokeObjectURL(url); cb(b); } else next(); }, 'image/jpeg', q);
          })();
        };
        img.onerror = function () { URL.revokeObjectURL(url); cb(null); };
        img.src = url;
      }
      inp.addEventListener('change', function () {
        var f = inp.files[0]; say(base);
        if (!f || f.size <= MAX) return;
        if (!/^image\/(jpeg|png|webp)$/.test(f.type)) { inp.value = ''; return say('This file is ' + (f.size / 1048576).toFixed(1) + ' MB. The limit is 1 MB - please compress the PDF and try again.', true); }
        busy = true; say('Making the photo smaller...');
        shrink(f, function (b) {
          busy = false;
          if (!b) { inp.value = ''; return say('This photo could not be made smaller than 1 MB. Please use a smaller image.', true); }
          var nf = new File([b], f.name.replace(/\.[^.]+$/, '') + '.jpg', { type: 'image/jpeg' }), dt = new DataTransfer();
          dt.items.add(nf); inp.files = dt.files;
          say('Photo reduced from ' + (f.size / 1048576).toFixed(1) + ' MB to ' + Math.round(b.size / 1024) + ' KB - ready to upload.');
        });
      });
      form.addEventListener('submit', function (e) {
        var f = inp.files[0];
        if (busy || (f && f.size > MAX)) { e.preventDefault(); say(busy ? 'Please wait, the photo is still being made smaller.' : 'The file is larger than 1 MB.', !busy); }
      });
    })();
    </script>
    <?php } ?>
    <?php if ($can_write) {
      // group the changes made in one save (same person, same minute) into one entry
      $hgroups = array();
      foreach ($history as $hh) { $key = substr($hh['changed_at'], 0, 19) . '|' . (int)$hh['changed_by']; $hgroups[$key][] = $hh; }
      foreach ($hgroups as &$hgi) $hgi = array_reverse($hgi); unset($hgi);
      $hx = function ($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };
      $hfields = array(); foreach ($history as $hh) $hfields[$hh['field']] = true; ksort($hfields); ?>
    <div class="uv-card" id="history">
      <div class="uv-sec-h"><span>Change History</span><small><?php echo count($history); ?> change<?php echo count($history) == 1 ? '' : 's'; ?></small></div>
      <div class="uv-hist">
        <?php if (!$history) { ?><p class="uv-docnone">No changes recorded yet. From now on every change to this profile is listed here.</p><?php } else { ?>
        <div class="uv-hbar"><select class="form-control js-hf"><option value="">All details</option><?php foreach (array_keys($hfields) as $fl) { ?><option><?php echo $hx($fl); ?></option><?php } ?></select></div>
        <ol class="uv-tl">
          <?php $gi = 0; foreach ($hgroups as $items) { $first = $items[0]; $gi++; ?>
          <li class="js-hg<?php echo $gi > 8 ? ' more' : ''; ?>">
            <div class="when"><b><?php echo date('d-M-Y', strtotime($first['changed_at'])); ?></b> <?php echo date('H:i', strtotime($first['changed_at'])); ?> &middot; <?php echo $first['by_name'] !== null && $first['by_name'] !== '' ? $hx($first['by_name']) : 'System'; ?></div>
            <?php foreach ($items as $hh) { ?>
            <div class="chg" data-f="<?php echo $hx($hh['field']); ?>"><span class="fl"><?php echo $hx($hh['field']); ?></span>
              <?php if ($hh['old_value'] !== '') { ?><span class="old"><?php echo $hx($hh['old_value']); ?></span> <i class="fa fa-long-arrow-right"></i> <?php } ?>
              <span class="new"><?php echo $hh['new_value'] !== '' ? $hx($hh['new_value']) : '<em>removed</em>'; ?></span></div>
            <?php } ?>
          </li>
          <?php } ?>
        </ol>
        <?php if ($gi > 8) { ?><button type="button" class="btn btn-default btn-sm js-hmore">Show all <?php echo $gi; ?> entries</button><?php } ?>
        <?php } ?>
      </div>
    </div>
    <script>
    (function () {
      var box = document.getElementById('history'); if (!box) return;
      var sel = box.querySelector('.js-hf'), more = box.querySelector('.js-hmore');
      function run() {
        var f = sel ? sel.value : '';
        box.querySelectorAll('.js-hg').forEach(function (g) {
          var any = false;
          g.querySelectorAll('.chg').forEach(function (c) { var ok = !f || c.getAttribute('data-f') === f; c.style.display = ok ? '' : 'none'; if (ok) any = true; });
          g.style.display = any && (f || !g.classList.contains('more') || box.classList.contains('all')) ? '' : 'none';
        });
        if (more) more.style.display = (f || box.classList.contains('all')) ? 'none' : '';
      }
      if (sel) sel.addEventListener('change', run);
      if (more) more.addEventListener('click', function () { box.classList.add('all'); run(); });
      run();
    })();
    </script>
    <?php } ?>
  </div>
  </section>
</div>
<?php if ($can_write) require(APPPATH . "views/manager/user/_left_modal.php"); ?>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
