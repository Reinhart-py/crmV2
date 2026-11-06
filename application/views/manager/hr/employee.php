<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$ymd = function ($v) { $v = trim((string)$v); return ($v !== '' && $v !== '0000-00-00' && ($t = strtotime($v))) ? date('Y-m-d', $t) : ''; };
$dmy = function ($v) use ($ymd) { $v = $ymd($v); return $v ? date('d-m-Y', strtotime($v)) : ''; };
$emp = !empty($emp) ? $emp : array();
$life = !empty($life) ? $life : array();
$e = function ($k) use ($emp) { return isset($emp[$k]) ? $emp[$k] : ''; };
$l = function ($k) use ($life) { return isset($life[$k]) ? $life[$k] : ''; };
$cur = $set['currency'];
$uid = (int)$u['id'];
$dis = empty($can_edit) ? ' disabled' : '';
$pdis = empty($edit_pay) ? ' disabled' : '';
$today = strtotime(date('Y-m-d'));
$days = function ($v) use ($ymd, $today) { $v = $ymd($v); return $v ? (int)floor((strtotime($v) - $today) / 86400) : null; };
$exp_chip = function ($v) use ($days, $alert_days) {
  $d = $days($v);
  if ($d === null) return '';
  if ($d < 0) return '<span class="chip x">Expired ' . abs($d) . ' days ago</span>';
  if ($d <= $alert_days) return '<span class="chip w">Expires in ' . $d . ' days</span>';
  return '<span class="chip ok">Valid</span>';
};
$left = !empty($exit);
$status = $left ? array('na', 'Left on ' . $dmy($exit['left_on'])) : ((int)$u['admin_status'] === 1 ? array('ok', 'Working') : array('na', 'Login disabled'));
$doj = $ymd($u['c_doj']);
$service = '';
if ($doj && strtotime($doj) <= $today) { $m = (int)floor(($today - strtotime($doj)) / (86400 * 30.4375)); $service = $m >= 12 ? floor($m / 12) . ' years' . ($m % 12 ? ' ' . ($m % 12) . ' months' : '') : $m . ' months'; }

// Things that need attention
$alerts = array();
foreach (array('Passport' => $u['p_exdate'], 'Visa' => $u['visa_expiry'], 'Emirates ID' => $u['eid_expiry'], 'Labour card' => $e('labour_card_expiry'), 'Insurance' => $e('insurance_expiry')) as $lbl => $v) {
  $d = $days($v);
  if ($d !== null && $d < 0) $alerts[] = array('x', $lbl . ' expired on ' . $dmy($v));
  elseif ($d !== null && $d <= $alert_days) $alerts[] = array('w', $lbl . ' expires on ' . $dmy($v) . ' (' . $d . ' days)');
}
$pd = $days($l('probation_end_date'));
if (!$left && $pd !== null && $pd >= 0 && $pd <= 30) $alerts[] = array('w', 'Probation ends on ' . $dmy($l('probation_end_date')) . ': confirm or extend');
$cd = $days($e('contract_end'));
if (!$left && $cd !== null && $cd <= $alert_days) $alerts[] = array($cd < 0 ? 'x' : 'w', 'Contract ' . ($cd < 0 ? 'ended' : 'ends') . ' on ' . $dmy($e('contract_end')));
if (!empty($see_pay) && empty($salary_now)) $alerts[] = array('w', 'No salary structure yet: add one under Salary', 'To do');
if (!empty($see_pay) && trim((string)$u['iban']) === '') $alerts[] = array('w', 'No IBAN: salary cannot be paid through WPS', 'To do');
foreach (array('Passport' => $u['p_no'], 'Visa' => $u['visa_no'], 'Emirates ID' => $u['eid_no']) as $lbl => $v) if (trim((string)$v) === '') $alerts[] = array('na', $lbl . ' number is missing');

$extras = (!empty($salary_now) && !empty($salary_now['extras'])) ? (array)json_decode($salary_now['extras'], true) : array();
$tabs = array('overview' => array('fa-user', 'Overview'), 'job' => array('fa-briefcase', 'Job & contract'), 'identity' => array('fa-credit-card', 'Identity'));
if (!empty($see_pay)) { $tabs['salary'] = array('fa-money', 'Salary'); $tabs['bank'] = array('fa-university', 'Bank & WPS'); }
if (!empty($staff_full)) $tabs['files'] = array('fa-paperclip', 'Files');
$tabs['history'] = array('fa-history', 'History');
$initials = strtoupper(implode('', array_map(function ($w) { return mb_substr($w, 0, 1); }, array_slice(preg_split('/\s+/', trim($u['name'])), 0, 2))));
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_8">
<style>
  .hf-top { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
  .hf-av { width: 58px; height: 58px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 700; background: var(--crm-ocean-soft, rgba(30, 58, 138, .1)); color: var(--crm-ocean, #1e3a8a); flex: 0 0 auto; }
  .hf-top h2 { margin: 0; font-size: 20px; font-weight: 700; }
  .hf-top .sub { color: var(--crm-muted, #64748b); font-size: 13.5px; margin-top: 2px; }
  .hf-facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 12px 16px; margin: 0; }
  .hf-facts dt { font-size: 12px; font-weight: 600; color: var(--crm-muted, #64748b); text-transform: uppercase; letter-spacing: .04em; }
  .hf-facts dd { margin: 2px 0 0; font-size: 14px; font-weight: 500; overflow-wrap: anywhere; }
  .hf-alerts { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 6px; }
  .hf-alerts li { display: flex; gap: 8px; align-items: flex-start; font-size: 13.5px; }
  .hf-sec { display: none; }
  .hf-sec.is-on { display: block; }
  .hf-exp { margin-top: 5px; min-height: 22px; }
  .hf-money { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 10px; }
  .hf-money div { border: 1px solid var(--crm-line, #e2e8f0); border-radius: 10px; padding: 10px 12px; }
  .hf-money span { display: block; font-size: 12px; color: var(--crm-muted, #64748b); }
  .hf-money b { font-size: 16px; }
  .hf-money .tot { background: var(--crm-ocean-soft, rgba(30, 58, 138, .06)); border-color: var(--crm-ocean, #1e3a8a); }
  .hf-x { display: grid; grid-template-columns: 1fr 140px 36px; gap: 8px; margin-bottom: 8px; }
  .hf-total { font-size: 15px; }
  .hf-total b { font-size: 18px; }
  .sx .sx-grid .help-block { font-size: 12px; margin: 4px 0 0; color: var(--crm-muted, #64748b); }
  @media (max-width: 767px) { .hf-x { grid-template-columns: 1fr 110px 36px; } }
</style>
<div class="content-wrapper sx sx-hr">
  <?php echo crm_tabbar('hr_people', 'hr/employees'); ?>
  <section class="sx-head">
    <h1>HR file<small><a href="<?php echo base_url(); ?>manager/hr/employees"><i class="fa fa-angle-left"></i> All employees</a></small></h1>
    <div class="sx-btns">
      <?php if (!empty($staff_view)) { ?><a href="<?php echo base_url(); ?>manager/hr/staff/view/<?php echo $uid; ?>" class="btn btn-default"><i class="fa fa-credit-card"></i> Staff profile</a><?php } ?>
      <?php if (!empty($staff_full)) { ?><a href="<?php echo base_url(); ?>manager/hr/staff/edit/<?php echo $uid; ?>" class="btn btn-default"><i class="fa fa-pencil"></i> Edit personal details</a><?php } ?>
    </div>
  </section>
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="sx-card">
      <div class="sx-card-b hf-top">
        <span class="hf-av" aria-hidden="true"><?php echo $h($initials); ?></span>
        <div style="flex:1;min-width:200px;">
          <h2><?php echo $h($u['name']); ?> <span class="chip <?php echo $status[0]; ?>" style="vertical-align:middle;"><?php echo $h($status[1]); ?></span></h2>
          <div class="sub"><?php echo $h(implode(' · ', array_filter(array(!empty($u['designation']) ? $u['designation'] : '', $u['department'], $u['emp_id'] !== '' ? 'Employee ID ' . $u['emp_id'] : '', $u['role_title'])))); ?></div>
        </div>
      </div>
    </div>

    <nav class="sx-tabs" id="hfTabs" style="padding-left:0;padding-right:0;margin-bottom:12px;" aria-label="HR file sections">
      <?php foreach ($tabs as $k => $t) { ?><a href="#<?php echo $k; ?>" data-k="<?php echo $k; ?>"><i class="fa <?php echo $t[0]; ?>"></i> <?php echo $t[1]; ?></a><?php } ?>
    </nav>

    <!-- Overview -->
    <div class="hf-sec" id="sec-overview">
      <div class="sx-card">
        <div class="sx-card-h"><h3><i class="fa fa-info-circle"></i> Summary</h3></div>
        <div class="sx-card-b">
          <dl class="hf-facts">
            <div><dt>Joined</dt><dd><?php echo $doj ? $h($dmy($doj)) . ($service ? ' (' . $h($service) . ')' : '') : '-'; ?></dd></div>
            <div><dt>Reports to</dt><dd><?php echo !empty($manager['name']) ? $h($manager['name']) : '-'; ?></dd></div>
            <div><dt>Stage</dt><dd><?php echo $left ? 'Exited' : $h($l('stage') ? $l('stage') : 'Confirmed'); ?></dd></div>
            <div><dt>Contract</dt><dd><?php echo $h($l('contract_type') ? $l('contract_type') : 'Permanent') . ($e('contract_end') ? ' to ' . $h($dmy($e('contract_end'))) : ''); ?></dd></div>
            <div><dt>Office / location</dt><dd><?php echo $u['office'] !== '' ? $h($u['office']) : '-'; ?></dd></div>
            <div><dt>Nationality</dt><dd><?php echo $u['national'] !== '' ? $h($u['national']) : '-'; ?></dd></div>
            <?php if (!empty($see_pay)) { ?><div><dt>Salary / month</dt><dd><?php echo !empty($salary_now) ? $h($cur) . ' ' . number_format($salary_now['total'], 2) : '-'; ?></dd></div><?php } ?>
            <div><dt>Annual leave</dt><dd><?php echo ($e('annual_leave_days') !== '' && $e('annual_leave_days') !== null ? (int)$e('annual_leave_days') : (int)$set['annual_leave_days']) . ' days a year'; ?></dd></div>
          </dl>
        </div>
      </div>
      <div class="sx-card">
        <div class="sx-card-h"><h3><i class="fa fa-bell-o"></i> Needs attention</h3></div>
        <div class="sx-card-b">
          <?php if (!$alerts) { ?><div class="sx-empty"><i class="fa fa-check-circle"></i> Nothing needs attention.</div>
          <?php } else { ?><ul class="hf-alerts"><?php foreach ($alerts as $a) { ?><li><span class="chip <?php echo $a[0]; ?>"><?php echo isset($a[2]) ? $a[2] : ($a[0] === 'x' ? 'Overdue' : ($a[0] === 'w' ? 'Soon' : 'Missing')); ?></span> <?php echo $h($a[1]); ?></li><?php } ?></ul><?php } ?>
        </div>
      </div>
    </div>

    <!-- Job & contract -->
    <div class="hf-sec" id="sec-job">
      <form class="sx-card" method="post" action="<?php echo base_url(); ?>manager/hr/employee_save/<?php echo $uid; ?>/job">
        <div class="sx-card-h"><h3><i class="fa fa-briefcase"></i> Job</h3></div>
        <div class="sx-card-b sx-grid">
          <div class="form-group"><label for="j1">Designation</label><input id="j1" class="form-control" name="designation" list="dlDes" maxlength="150" value="<?php echo $h(!empty($u['designation']) ? $u['designation'] : ''); ?>"<?php echo $dis; ?>></div>
          <div class="form-group"><label for="j2">Department</label><input id="j2" class="form-control" name="department" list="dlDep" maxlength="100" value="<?php echo $h($u['department']); ?>"<?php echo $dis; ?>></div>
          <div class="form-group"><label for="j3">Grade / band</label><input id="j3" class="form-control" name="grade" list="dlGrade" maxlength="60" value="<?php echo $h($e('grade')); ?>"<?php echo $dis; ?>></div>
          <div class="form-group"><label for="j4">Office / location</label><input id="j4" class="form-control" name="office" maxlength="100" value="<?php echo $h($u['office']); ?>"<?php echo $dis; ?>></div>
          <div class="form-group"><label for="j5">Date of joining</label><input id="j5" type="date" class="form-control" name="c_doj" value="<?php echo $h($doj); ?>"<?php echo $dis; ?>></div>
          <div class="form-group"><label for="j6">Stage</label><select id="j6" class="form-control" name="stage"<?php echo $dis; ?>><?php foreach ($stages as $s) { ?><option<?php echo ($l('stage') ? $l('stage') : 'Confirmed') === $s ? ' selected' : ''; ?>><?php echo $h($s); ?></option><?php } ?></select></div>
        </div>
        <div class="sx-card-h" style="border-top:1px solid var(--crm-line-soft,#eef2f7);"><h3><i class="fa fa-file-text-o"></i> Contract</h3></div>
        <div class="sx-card-b sx-grid">
          <div class="form-group"><label for="j7">Contract type</label><select id="j7" class="form-control" name="contract_type"<?php echo $dis; ?>><?php foreach ($contract_types as $c) { ?><option<?php echo ($l('contract_type') ? $l('contract_type') : 'Permanent') === $c ? ' selected' : ''; ?>><?php echo $h($c); ?></option><?php } ?></select></div>
          <div class="form-group"><label for="j8">Contract start</label><input id="j8" type="date" class="form-control" name="contract_start" value="<?php echo $h($ymd($e('contract_start'))); ?>"<?php echo $dis; ?>></div>
          <div class="form-group"><label for="j9">Contract end</label><input id="j9" type="date" class="form-control" name="contract_end" value="<?php echo $h($ymd($e('contract_end'))); ?>"<?php echo $dis; ?>><p class="help-block">Leave empty for an open-ended contract.</p></div>
          <div class="form-group"><label for="j10">Probation ends</label><input id="j10" type="date" class="form-control" name="probation_end_date" value="<?php echo $h($ymd($l('probation_end_date'))); ?>"<?php echo $dis; ?>>
            <?php if (empty($dis) && (int)$set['probation_months'] > 0) { ?><p class="help-block"><a href="#" id="jProb">Set to <?php echo (int)$set['probation_months']; ?> months after joining</a></p><?php } ?></div>
          <div class="form-group"><label for="j11">Confirmed on</label><input id="j11" type="date" class="form-control" name="confirmation_date" value="<?php echo $h($ymd($l('confirmation_date'))); ?>"<?php echo $dis; ?>></div>
          <div class="form-group"><label for="j12">Notice period (days)</label><input id="j12" type="number" min="0" max="365" class="form-control" name="notice_days" placeholder="<?php echo (int)$set['notice_days']; ?> (company default)" value="<?php echo $h($e('notice_days')); ?>"<?php echo $dis; ?>></div>
          <div class="form-group"><label for="j13">Annual leave (days a year)</label><input id="j13" type="number" min="0" max="90" class="form-control" name="annual_leave_days" placeholder="<?php echo (int)$set['annual_leave_days']; ?> (company default)" value="<?php echo $h($e('annual_leave_days')); ?>"<?php echo $dis; ?>></div>
        </div>
        <?php if (!empty($can_edit)) { ?><div class="sx-card-f"><button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Save job &amp; contract</button></div><?php } ?>
      </form>
      <datalist id="dlDes"><?php foreach ($designations as $d) { ?><option value="<?php echo $h($d); ?>"><?php } ?></datalist>
      <datalist id="dlDep"><?php foreach ($departments as $d) { ?><option value="<?php echo $h($d); ?>"><?php } ?></datalist>
      <datalist id="dlGrade"><?php foreach ($grades as $d) { ?><option value="<?php echo $h($d); ?>"><?php } ?></datalist>
    </div>

    <!-- Identity -->
    <div class="hf-sec" id="sec-identity">
      <form class="sx-card" method="post" action="<?php echo base_url(); ?>manager/hr/employee_save/<?php echo $uid; ?>/identity">
        <div class="sx-card-h"><h3><i class="fa fa-credit-card"></i> Identity documents</h3></div>
        <div class="sx-card-b sx-grid">
          <div class="form-group"><label for="i0">Nationality</label>
            <?php if ($countries) { ?><select id="i0" class="form-control" name="national"<?php echo $dis; ?>><option value="">-</option><?php $found = false; foreach ($countries as $c) { $sel = ($c === $u['national']); $found = $found || $sel; ?><option<?php echo $sel ? ' selected' : ''; ?>><?php echo $h($c); ?></option><?php } if (!$found && $u['national'] !== '') { ?><option selected><?php echo $h($u['national']); ?></option><?php } ?></select>
            <?php } else { ?><input id="i0" class="form-control" name="national" value="<?php echo $h($u['national']); ?>"<?php echo $dis; ?>><?php } ?></div>
        </div>
        <?php
        $blocks = array(
          array('Passport', array(array('p_no', 'Passport number', $u['p_no'], 'text'), array('p_date', 'Issue date', $ymd($u['p_date']), 'date'), array('p_exdate', 'Expiry date', $ymd($u['p_exdate']), 'date'))),
          array('Residence visa', array(array('visa_no', 'Visa / file number', $u['visa_no'], 'text'), array('visa_type', 'Visa type', $u['visa_type'], 'text'), array('visa_by', 'Sponsor', $u['visa_by'], 'text'), array('visa_expiry', 'Expiry date', $ymd($u['visa_expiry']), 'date'))),
          array('Emirates ID', array(array('eid_no', 'Emirates ID number', $u['eid_no'], 'text'), array('eid_expiry', 'Expiry date', $ymd($u['eid_expiry']), 'date'))),
          array('Labour card (MOHRE work permit)', array(array('labour_card_no', 'Labour card number', $e('labour_card_no'), 'text'), array('labour_card_expiry', 'Expiry date', $ymd($e('labour_card_expiry')), 'date'))),
          array('Medical insurance', array(array('insurance_provider', 'Provider', $e('insurance_provider'), 'text'), array('insurance_no', 'Policy / card number', $e('insurance_no'), 'text'), array('insurance_expiry', 'Expiry date', $ymd($e('insurance_expiry')), 'date'))),
        );
        foreach ($blocks as $b) { ?>
          <div class="sx-card-b" style="border-top:1px solid var(--crm-line-soft,#eef2f7);padding-top:12px;">
            <div class="sx-label" style="font-size:13.5px;margin-bottom:8px;"><?php echo $h($b[0]); ?></div>
            <div class="sx-grid">
              <?php foreach ($b[1] as $f) { ?>
                <div class="form-group"><label for="f_<?php echo $f[0]; ?>"><?php echo $h($f[1]); ?></label>
                  <input id="f_<?php echo $f[0]; ?>" type="<?php echo $f[3]; ?>" class="form-control" name="<?php echo $f[0]; ?>" value="<?php echo $h($f[2]); ?>"<?php echo $dis; ?>>
                  <?php if (substr($f[0], -6) === 'expiry' || $f[0] === 'p_exdate') { ?><div class="hf-exp"><?php echo $exp_chip($f[2]); ?></div><?php } ?>
                </div>
              <?php } ?>
            </div>
          </div>
        <?php } ?>
        <?php if (!empty($can_edit)) { ?><div class="sx-card-f"><button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Save identity documents</button></div><?php } ?>
      </form>
    </div>

    <?php if (!empty($see_pay)) { ?>
    <!-- Salary -->
    <div class="hf-sec" id="sec-salary">
      <div class="sx-card">
        <div class="sx-card-h"><h3><i class="fa fa-money"></i> Salary now</h3><?php if (!empty($salary_now)) { ?><div class="sx-tools text-muted" style="font-size:12.5px;">Since <?php echo $h($dmy($salary_now['effective_from'])); ?></div><?php } ?></div>
        <div class="sx-card-b">
          <?php if (empty($salary_now)) { ?>
            <div class="sx-empty"><i class="fa fa-money"></i> No salary structure yet<?php echo is_numeric($u['c_salary']) && (float)$u['c_salary'] > 1 ? ' (the staff profile shows a total of ' . $h($cur) . ' ' . number_format((float)$u['c_salary'], 2) . ' without a breakdown)' : ''; ?>.</div>
          <?php } else { ?>
            <div class="hf-money">
              <div><span>Basic</span><b><?php echo number_format($salary_now['basic'], 2); ?></b></div>
              <div><span>Housing</span><b><?php echo number_format($salary_now['housing'], 2); ?></b></div>
              <div><span>Transport</span><b><?php echo number_format($salary_now['transport'], 2); ?></b></div>
              <div><span>Other</span><b><?php echo number_format($salary_now['other'], 2); ?></b></div>
              <?php foreach ($extras as $x) { ?><div><span><?php echo $h($x['name']); ?></span><b><?php echo number_format($x['amount'], 2); ?></b></div><?php } ?>
              <div class="tot"><span>Total / month (<?php echo $h($cur); ?>)</span><b><?php echo number_format($salary_now['total'], 2); ?></b></div>
            </div>
          <?php } ?>
        </div>
      </div>

      <?php if (!empty($edit_pay)) { ?>
      <form class="sx-card" method="post" action="<?php echo base_url(); ?>manager/hr/salary_add/<?php echo $uid; ?>" id="salForm">
        <div class="sx-card-h"><h3><i class="fa fa-plus"></i> <?php echo empty($salary_now) ? 'Set salary' : 'Change salary (increment, promotion, correction)'; ?></h3></div>
        <div class="sx-card-b sx-grid">
          <div class="form-group"><label for="sf">Starts from</label><input id="sf" type="date" class="form-control" name="effective_from" required value="<?php echo empty($salary_now) && $doj ? $h($doj) : date('Y-m-01'); ?>"></div>
          <div class="form-group"><label for="sb">Basic</label><input id="sb" type="number" step="0.01" min="0" class="form-control sal" name="basic" required value="<?php echo !empty($salary_now) ? $h(0 + $salary_now['basic']) : ''; ?>"><p class="help-block">Gratuity is based on basic.</p></div>
          <div class="form-group"><label for="sh">Housing</label><input id="sh" type="number" step="0.01" min="0" class="form-control sal" name="housing" value="<?php echo !empty($salary_now) ? $h(0 + $salary_now['housing']) : ''; ?>"></div>
          <div class="form-group"><label for="st">Transport</label><input id="st" type="number" step="0.01" min="0" class="form-control sal" name="transport" value="<?php echo !empty($salary_now) ? $h(0 + $salary_now['transport']) : ''; ?>"></div>
          <div class="form-group"><label for="so">Other allowance</label><input id="so" type="number" step="0.01" min="0" class="form-control sal" name="other" value="<?php echo !empty($salary_now) ? $h(0 + $salary_now['other']) : ''; ?>"></div>
        </div>
        <div class="sx-card-b" style="padding-top:0;">
          <span class="sx-label">More allowances (phone, food, fuel ...)</span>
          <div id="salX">
            <?php foreach ($extras ? $extras : array(array('name' => '', 'amount' => '')) as $x) { ?>
              <div class="hf-x"><input class="form-control" name="extra_name[]" maxlength="60" placeholder="Name" value="<?php echo $h($x['name']); ?>" aria-label="Allowance name"><input type="number" step="0.01" min="0" class="form-control sal" name="extra_amount[]" placeholder="Amount" value="<?php echo $h($x['amount']); ?>" aria-label="Allowance amount"><button type="button" class="btn btn-default salDel" aria-label="Remove allowance"><i class="fa fa-times"></i></button></div>
            <?php } ?>
          </div>
          <button type="button" class="btn btn-default btn-sm" id="salAdd"><i class="fa fa-plus"></i> Add allowance</button>
          <div class="form-group" style="margin-top:12px;"><label for="sr">Reason</label><input id="sr" class="form-control" name="reason" maxlength="200" placeholder="e.g. Annual increment, promotion"></div>
        </div>
        <div class="sx-card-f" style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
          <div class="hf-total">Total <b id="salTot">0.00</b> <?php echo $h($cur); ?> / month</div>
          <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Save salary</button>
        </div>
      </form>
      <?php } ?>

      <div class="sx-card">
        <div class="sx-card-h"><h3><i class="fa fa-line-chart"></i> Salary history</h3></div>
        <?php if (!$salaries) { ?><div class="sx-card-b"><div class="sx-empty">No salary structures yet.</div></div><?php } else { ?>
        <div class="sx-table-wrap"><table class="sx-table sx-cards">
          <thead><tr><th>From</th><th>Basic</th><th>Housing</th><th>Transport</th><th>Other</th><th>Total</th><th>Reason</th><th>Entered by</th><?php if (!empty($edit_pay)) { ?><th></th><?php } ?></tr></thead>
          <tbody><?php foreach ($salaries as $s) { $xs = $s['extras'] ? (array)json_decode($s['extras'], true) : array(); $xo = 0; foreach ($xs as $x) $xo += $x['amount']; ?>
            <tr>
              <td data-l="From" class="nw"><?php echo $h($dmy($s['effective_from'])); ?><?php if (!empty($salary_now) && (int)$salary_now['id'] === (int)$s['id']) { ?> <span class="chip ok">Now</span><?php } elseif ($s['effective_from'] > date('Y-m-d')) { ?> <span class="chip">Upcoming</span><?php } ?></td>
              <td data-l="Basic"><?php echo number_format($s['basic'], 2); ?></td>
              <td data-l="Housing"><?php echo number_format($s['housing'], 2); ?></td>
              <td data-l="Transport"><?php echo number_format($s['transport'], 2); ?></td>
              <td data-l="Other" title="<?php echo $h(implode(', ', array_map(function ($x) { return $x['name'] . ' ' . $x['amount']; }, $xs))); ?>"><?php echo number_format($s['other'] + $xo, 2); ?></td>
              <td data-l="Total"><b><?php echo number_format($s['total'], 2); ?></b></td>
              <td data-l="Reason"><?php echo $h($s['reason']); ?></td>
              <td data-l="Entered by"><?php echo $h($s['by_name']); ?><span class="text-muted" style="display:block;font-size:12px;"><?php echo $h($dmy($s['created_at'])); ?></span></td>
              <?php if (!empty($edit_pay)) { ?><td data-l="" class="nw"><form method="post" action="<?php echo base_url(); ?>manager/hr/salary_delete/<?php echo (int)$s['id']; ?>" class="hfDel" style="margin:0;"><button type="submit" class="btn btn-default btn-sm" aria-label="Delete this salary structure"><i class="fa fa-trash-o"></i></button></form></td><?php } ?>
            </tr>
          <?php } ?></tbody>
        </table></div>
        <?php } ?>
      </div>
    </div>

    <!-- Bank & WPS -->
    <div class="hf-sec" id="sec-bank">
      <form class="sx-card" method="post" action="<?php echo base_url(); ?>manager/hr/employee_save/<?php echo $uid; ?>/bank">
        <div class="sx-card-h"><h3><i class="fa fa-university"></i> Bank account &amp; salary transfer (WPS)</h3></div>
        <div class="sx-card-b sx-grid">
          <div class="form-group"><label for="b1">Payment method</label><select id="b1" class="form-control" name="payment_method"<?php echo $pdis; ?>><option value="">-</option><?php foreach ($pay_methods as $m) { ?><option<?php echo $e('payment_method') === $m ? ' selected' : ''; ?>><?php echo $h($m); ?></option><?php } ?></select></div>
          <div class="form-group"><label for="b2">Bank</label><input id="b2" class="form-control" name="bank" maxlength="100" value="<?php echo $h($u['bank']); ?>"<?php echo $pdis; ?>></div>
          <div class="form-group"><label for="b3">IBAN</label><input id="b3" class="form-control" name="iban" maxlength="40" style="text-transform:uppercase" placeholder="AE07 0331 2345 6789 0123 456" value="<?php echo $h($u['iban']); ?>"<?php echo $pdis; ?>></div>
          <div class="form-group"><label for="b4">Account number</label><input id="b4" class="form-control" name="bank_no" maxlength="40" value="<?php echo $h($u['bank_no']); ?>"<?php echo $pdis; ?>></div>
          <div class="form-group"><label for="b5">SWIFT</label><input id="b5" class="form-control" name="swift" maxlength="20" style="text-transform:uppercase" value="<?php echo $h($u['swift']); ?>"<?php echo $pdis; ?>></div>
          <div class="form-group"><label for="b6">Bank routing code</label><input id="b6" class="form-control" name="wps_routing_code" inputmode="numeric" maxlength="9" value="<?php echo $h($e('wps_routing_code')); ?>"<?php echo $pdis; ?>><p class="help-block">9-digit code of the employee's bank, needed for the WPS file.</p></div>
          <div class="form-group"><label for="b7">MOHRE person ID</label><input id="b7" class="form-control" name="mol_person_id" inputmode="numeric" maxlength="20" value="<?php echo $h($e('mol_person_id')); ?>"<?php echo $pdis; ?>><p class="help-block">14-digit number on the labour card.</p></div>
        </div>
        <?php if (!empty($edit_pay)) { ?><div class="sx-card-f"><button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Save bank details</button></div><?php } ?>
      </form>
    </div>
    <?php } ?>

    <?php if (!empty($staff_full)) { ?>
    <!-- Files -->
    <div class="hf-sec" id="sec-files">
      <div class="sx-card">
        <div class="sx-card-h"><h3><i class="fa fa-paperclip"></i> Files</h3><div class="sx-tools"><a href="<?php echo base_url(); ?>manager/hr/staff/view/<?php echo $uid; ?>#docs" class="btn btn-default btn-sm"><i class="fa fa-upload"></i> Upload or delete on the staff profile</a></div></div>
        <?php if (!$files) { ?><div class="sx-card-b"><div class="sx-empty"><i class="fa fa-paperclip"></i> No files uploaded yet.</div></div><?php } else { ?>
        <div class="sx-table-wrap"><table class="sx-table sx-cards">
          <thead><tr><th>Document</th><th>File</th><th>Uploaded</th><th></th></tr></thead>
          <tbody><?php foreach ($files as $f) { ?>
            <tr>
              <td data-l="Document"><b><?php echo $h($f['doc_type']); ?></b></td>
              <td data-l="File"><?php echo $h($f['orig_name']); ?><span class="text-muted" style="display:block;font-size:12px;"><?php echo number_format($f['size'] / 1024, 0); ?> KB</span></td>
              <td data-l="Uploaded"><?php echo $h($dmy($f['uploaded_at'])); ?><span class="text-muted" style="display:block;font-size:12px;"><?php echo $h($f['uploader']); ?></span></td>
              <td data-l="" class="nw"><a class="btn btn-default btn-sm" target="_blank" rel="noopener" href="<?php echo base_url(); ?>manager/hr/staff/doc_file/<?php echo (int)$f['id']; ?>"><i class="fa fa-eye"></i> View</a> <a class="btn btn-default btn-sm" href="<?php echo base_url(); ?>manager/hr/staff/doc_file/<?php echo (int)$f['id']; ?>?dl=1" aria-label="Download"><i class="fa fa-download"></i></a></td>
            </tr>
          <?php } ?></tbody>
        </table></div>
        <?php } ?>
      </div>
    </div>
    <?php } ?>

    <!-- History -->
    <div class="hf-sec" id="sec-history">
      <div class="sx-card">
        <div class="sx-card-h"><h3><i class="fa fa-history"></i> Change history</h3></div>
        <?php if (!$history) { ?><div class="sx-card-b"><div class="sx-empty">No changes recorded yet.</div></div><?php } else { ?>
        <div class="sx-table-wrap"><table class="sx-table sx-cards">
          <thead><tr><th>When</th><th>What</th><th>Before</th><th>After</th><th>By</th></tr></thead>
          <tbody><?php foreach ($history as $r) { ?>
            <tr><td data-l="When" class="nw"><?php echo $h(date('d-m-Y H:i', strtotime($r['changed_at']))); ?></td><td data-l="What"><b><?php echo $h($r['field']); ?></b></td><td data-l="Before" class="text-muted"><?php echo $h($r['old_value']); ?></td><td data-l="After"><?php echo $h($r['new_value']); ?></td><td data-l="By"><?php echo $h($r['by_name']); ?></td></tr>
          <?php } ?></tbody>
        </table></div>
        <?php } ?>
      </div>
    </div>
  </section>
</div>
<script>
(function () {
  var tabs = document.querySelectorAll('#hfTabs a');
  function show(k) {
    if (!document.getElementById('sec-' + k)) k = 'overview';
    document.querySelectorAll('.hf-sec').forEach(function (s) { s.classList.toggle('is-on', s.id === 'sec-' + k); });
    tabs.forEach(function (a) { var on = a.getAttribute('data-k') === k; a.classList.toggle('is-on', on); if (on) a.setAttribute('aria-current', 'page'); else a.removeAttribute('aria-current'); });
  }
  tabs.forEach(function (a) { a.addEventListener('click', function (ev) { ev.preventDefault(); history.replaceState(null, '', '#' + a.getAttribute('data-k')); show(a.getAttribute('data-k')); }); });
  show(location.hash.replace('#', '') || 'overview');

  var pb = document.getElementById('jProb');
  if (pb) pb.addEventListener('click', function (ev) {
    ev.preventDefault();
    var j = document.getElementById('j5').value; if (!j) { alert('Enter the date of joining first.'); return; }
    var d = new Date(j + 'T00:00:00'); d.setMonth(d.getMonth() + <?php echo (int)$set['probation_months']; ?>); d.setDate(d.getDate() - 1);
    document.getElementById('j10').value = d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2);
  });

  var f = document.getElementById('salForm');
  if (f) {
    var tot = function () { var t = 0; f.querySelectorAll('.sal').forEach(function (i) { t += parseFloat(i.value) || 0; }); document.getElementById('salTot').textContent = t.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }); };
    f.addEventListener('input', tot); tot();
    document.getElementById('salAdd').addEventListener('click', function () {
      var row = f.querySelector('.hf-x').cloneNode(true); row.querySelectorAll('input').forEach(function (i) { i.value = ''; });
      document.getElementById('salX').appendChild(row);
    });
    document.getElementById('salX').addEventListener('click', function (ev) {
      var b = ev.target.closest('.salDel'); if (!b) return;
      var rows = f.querySelectorAll('.hf-x');
      if (rows.length > 1) b.closest('.hf-x').remove(); else rows[0].querySelectorAll('input').forEach(function (i) { i.value = ''; });
      tot();
    });
  }
  document.querySelectorAll('form.hfDel').forEach(function (x) { x.addEventListener('submit', function (ev) { if (!confirm('Delete this salary structure? Use this only for a mistake; for a raise, add a new one instead.')) ev.preventDefault(); }); });
})();
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
