<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$money = function ($v) { return number_format((float)$v, 2); };
$cur = $h($set['currency']);
$rid = (int)$run['id'];
$mname = date('F Y', strtotime($run['month'] . '-01'));
$draft = $run['status'] === 'Draft';
$chip = array('Draft' => 'w', 'Approved' => 'on', 'Paid' => 'ok');
$base = base_url() . 'manager/hr/';
$step = function ($label, $icon, $cls, $confirm) use ($base, $rid) {
  return '<form method="post" action="' . $base . 'payroll_step/' . $rid . '/' . $label . '" style="display:inline;" onsubmit="return confirm(' . htmlspecialchars(json_encode($confirm), ENT_QUOTES) . ');"><button type="submit" class="btn ' . $cls . '">' . $icon . '</button></form>';
};
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<style>
  .pr-steps { display: flex; gap: 0; margin: 0 0 14px; flex-wrap: wrap; }
  .pr-steps div { flex: 1 1 160px; padding: 10px 14px; border: 1px solid var(--crm-line, #e2e8f0); background: var(--crm-surface, #fff); font-size: 13px; color: var(--crm-muted, #64748b); }
  .pr-steps div:first-child { border-radius: 10px 0 0 10px; }
  .pr-steps div:last-child { border-radius: 0 10px 10px 0; }
  .pr-steps div + div { border-left: 0; }
  .pr-steps b { display: block; font-size: 14px; color: var(--crm-ink, #0f172a); }
  .pr-steps .done b:before { content: "\f00c"; font-family: FontAwesome; color: #15803d; margin-right: 6px; }
  .pr-steps .now { background: var(--crm-ocean-soft, rgba(30, 58, 138, .06)); border-color: var(--crm-ocean, #1e3a8a); }
  .pr-sub { display: block; font-size: 12px; color: var(--crm-muted, #64748b); }
  .pr-lines { display: none; }
  .pr-lines.open { display: table-row; }
  .pr-lines td { background: var(--crm-bg-soft, #f8fafc); }
  .pr-lines table { width: 100%; font-size: 12.5px; }
  .pr-lines table td { padding: 2px 6px !important; background: none; border: 0 !important; }
  .pr-warn { border-left: 4px solid #f59e0b; }
  .pr-warn ul { margin: 6px 0 0; padding-left: 18px; }
  @media (max-width: 767px) {
    .pr-steps div, .pr-steps div:first-child, .pr-steps div:last-child { border-radius: 0; flex-basis: 100%; border-left: 1px solid var(--crm-line, #e2e8f0); }
    .pr-steps div + div { border-top: 0; }
    .pr-lines.open { display: block; }
  }
</style>
<div class="content-wrapper sx">
  <?php echo crm_tabbar('hr_pay', 'hr/payroll'); ?>
  <section class="sx-head">
    <h1><a href="<?php echo $base; ?>payroll" class="text-muted" aria-label="Back to payroll" style="margin-right:6px;"><i class="fa fa-angle-left"></i></a>Payroll <?php echo $h($mname); ?> <span class="chip <?php echo $chip[$run['status']]; ?>" style="vertical-align:middle;"><?php echo $h($run['status']); ?></span>
      <small><?php echo $run['calculated_at'] ? 'Worked out ' . $h(date('d M Y H:i', strtotime($run['calculated_at']))) : ''; ?></small></h1>
    <div class="sx-btns">
      <a class="btn btn-default" href="<?php echo $base; ?>payroll_csv/<?php echo $rid; ?>"><i class="fa fa-download"></i> CSV</a>
      <?php if (!$draft) { ?><a class="btn btn-default" href="<?php echo $base; ?>payroll_sif/<?php echo $rid; ?>"><i class="fa fa-university"></i> WPS file</a><?php } ?>
      <?php if ($draft && $can_edit) { ?><form method="post" action="<?php echo $base; ?>payroll_recalc/<?php echo $rid; ?>" style="display:inline;"><button type="submit" class="btn btn-default"><i class="fa fa-refresh"></i> Work out again</button></form><?php } ?>
      <?php if ($draft && $can_approve) echo $step('approve', '<i class="fa fa-check"></i> Approve', 'btn-primary', 'Approve the payroll for ' . $mname . '? It is worked out once more and then locked.'); ?>
      <?php if ($run['status'] === 'Approved' && $can_edit) echo $step('paid', '<i class="fa fa-check-circle"></i> Mark as paid', 'btn-primary', 'Mark ' . $mname . ' as paid? Everyone is told their payslip is ready, and this cannot be undone.'); ?>
      <?php if ($run['status'] === 'Approved' && $can_approve) echo $step('reopen', '<i class="fa fa-unlock"></i> Reopen', 'btn-default', 'Reopen the payroll for ' . $mname . ' as a draft?'); ?>
      <?php if ($draft && $can_edit) echo $step('delete', '<i class="fa fa-trash-o"></i>', 'btn-default', 'Delete this draft payroll? Overtime and other items stay for the month.'); ?>
    </div>
  </section>
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="pr-steps">
      <div class="<?php echo $draft ? 'now' : 'done'; ?>"><b>1. Check</b>Prepared<?php echo $who['created_by'] ? ' by ' . $h($who['created_by']) : ''; ?>; add overtime and other items</div>
      <div class="<?php echo $draft ? '' : ($run['status'] === 'Approved' ? 'now done' : 'done'); ?>"><b>2. Approve</b><?php echo $run['approved_at'] ? $h(date('d M Y', strtotime($run['approved_at']))) . ($who['approved_by'] ? ' by ' . $h($who['approved_by']) : '') : 'Needs the Approve Payroll right'; ?></div>
      <div class="<?php echo $run['status'] === 'Paid' ? 'done now' : ''; ?>"><b>3. Pay</b><?php echo $run['paid_at'] ? 'Paid ' . $h(date('d M Y', strtotime($run['paid_at']))) . ($who['paid_by'] ? ' by ' . $h($who['paid_by']) : '') : 'Send the WPS file to the bank, then mark as paid'; ?></div>
    </div>

    <?php if ($missing || $wps_missing || !$wps_company) { ?>
    <div class="sx-card pr-warn"><div class="sx-card-b">
      <?php if ($missing) { ?><b><i class="fa fa-exclamation-triangle" style="color:#b45309;"></i> <?php echo count($missing); ?> <?php echo count($missing) === 1 ? 'person is' : 'people are'; ?> not in this payroll because no salary is set:</b>
        <div style="margin:6px 0 10px;"><?php foreach ($missing as $i => $p) { if ($i === 12) echo '<span id="prMore" style="display:none;">'; echo ($i ? ', ' : '') . '<a href="' . $base . 'employees/' . (int)$p['id'] . '#salary">' . $h($p['name']) . '</a>'; } if (count($missing) > 12) echo '</span> <a href="#" onclick="document.getElementById(\'prMore\').style.display=\'inline\';this.remove();return false;">and ' . (count($missing) - 12) . ' more</a>'; ?></div><?php } ?>
      <?php if (!$wps_company) { ?><div style="margin-bottom:8px;"><b><i class="fa fa-university" style="color:#b45309;"></i> WPS file:</b> add the 13-digit MOL establishment ID and the company bank routing code in <a href="<?php echo $base; ?>settings">HR Settings</a>.</div><?php } ?>
      <?php if ($wps_missing) { ?><b><i class="fa fa-university" style="color:#b45309;"></i> Left out of the WPS file until their details are complete:</b>
        <ul><?php foreach ($wps_missing as $w) { ?><li><a href="<?php echo $base; ?>employees/<?php echo (int)$w['user_id']; ?>#bank"><?php echo $h($w['name']); ?></a>: <?php echo $h(implode(', ', $w['miss'])); ?></li><?php } ?></ul><?php } ?>
    </div></div>
    <?php } ?>

    <div class="sx-kpis">
      <div class="sx-kpi s"><span class="ic"><i class="fa fa-users"></i></span><div><b><?php echo (int)$run['people']; ?></b><span>People</span></div></div>
      <div class="sx-kpi"><span class="ic"><i class="fa fa-plus"></i></span><div><b><?php echo $money($run['gross']); ?></b><span>Gross, <?php echo $cur; ?></span></div></div>
      <div class="sx-kpi r"><span class="ic"><i class="fa fa-minus"></i></span><div><b><?php echo $money($run['deductions']); ?></b><span>Deductions</span></div></div>
      <div class="sx-kpi g"><span class="ic"><i class="fa fa-money"></i></span><div><b><?php echo $money($run['net']); ?></b><span>Net pay</span></div></div>
    </div>

    <div class="sx-card">
      <div class="sx-bar">
        <input type="search" class="form-control sx-q" id="prQ" placeholder="Employee" autocomplete="off">
        <span class="sx-count" id="prCount"></span>
      </div>
      <div class="sx-table-wrap">
        <table class="sx-table sx-cards" id="prTable">
          <thead><tr><th>Employee</th><th>Days</th><th class="text-right">Salary</th><th class="text-right">Extra</th><th class="text-right">Deductions</th><th class="text-right">Net pay</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($slips as $s) { $i = $s['i']; ?>
            <tr class="pr-row" data-q="<?php echo $h(strtolower($i['name'] . ' ' . $i['emp_id'] . ' ' . $i['department'])); ?>">
              <td data-l="Employee"><b><?php echo $h($i['name']); ?></b><span class="pr-sub"><?php echo $h(trim($i['designation'] . ($i['designation'] && $i['department'] ? ', ' : '') . $i['department'])); ?></span>
                <?php foreach ((array)$i['notes'] as $n) { ?><span class="pr-sub" style="color:#b45309;"><i class="fa fa-info-circle"></i> <?php echo $h($n); ?></span><?php } ?></td>
              <td data-l="Days"><?php echo 0 + $s['days_paid']; ?>/<?php echo (int)$s['days_in_month']; ?><?php if ((float)$s['leave_days'] > 0) { ?><span class="pr-sub"><?php echo 0 + $s['leave_days']; ?> on leave</span><?php } ?></td>
              <td data-l="Salary" class="text-right mono"><?php echo $money($s['fixed']); ?></td>
              <td data-l="Extra" class="text-right mono"><?php echo (float)$s['variable'] ? $money($s['variable']) : '<span class="text-muted">-</span>'; ?></td>
              <td data-l="Deductions" class="text-right mono"><?php echo (float)$s['total_deductions'] ? '<span style="color:#b91c1c;">' . $money($s['total_deductions']) . '</span>' : '<span class="text-muted">-</span>'; ?></td>
              <td data-l="Net pay" class="text-right mono"><b><?php echo $money($s['net']); ?></b></td>
              <td data-l="" class="nw"><button type="button" class="btn btn-default btn-sm prMore" aria-expanded="false"><i class="fa fa-list"></i> Lines</button> <a class="btn btn-default btn-sm" href="<?php echo $base; ?>payslip/<?php echo (int)$s['id']; ?>" target="_blank" rel="noopener"><i class="fa fa-file-text-o"></i> Payslip</a></td>
            </tr>
            <tr class="pr-lines"><td colspan="7"><table>
              <?php foreach ((array)$s['e'] as $l) { ?><tr><td><?php echo $h($l[0]); ?></td><td class="text-right mono"><?php echo $money($l[1]); ?></td></tr><?php } ?>
              <?php foreach ((array)$s['d'] as $l) { ?><tr><td style="color:#b91c1c;">Less: <?php echo $h($l[0]); ?></td><td class="text-right mono" style="color:#b91c1c;">-<?php echo $money($l[1]); ?></td></tr><?php } ?>
              <tr><td><b>Net pay</b><?php if ($i['payment_method']) { ?> <span class="text-muted">by <?php echo $h($i['payment_method']); ?></span><?php } ?></td><td class="text-right mono"><b><?php echo $money($s['net']); ?></b></td></tr>
            </table></td></tr>
          <?php } ?>
          </tbody>
        </table>
        <?php if (!$slips) { ?><div class="sx-empty">Nobody to pay this month. Set salaries in each person's HR file, then work it out again.</div><?php } ?>
      </div>
    </div>

    <div class="sx-card">
      <div class="sx-card-h"><h3><i class="fa fa-plus-square-o"></i> Overtime and other items for <?php echo $h($mname); ?></h3>
        <?php if ($draft && $can_edit) { ?><div class="sx-tools"><button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#prItem"><i class="fa fa-plus"></i> Add</button></div><?php } ?></div>
      <?php if (!$items) { ?><div class="sx-card-b"><div class="sx-empty">No overtime, bonus or deduction added for this month.</div></div><?php } else { ?>
      <div class="sx-table-wrap"><table class="sx-table sx-cards">
        <thead><tr><th>Employee</th><th>Type</th><th>Description</th><th class="text-right">Hours / amount</th><?php if ($draft && $can_edit) { ?><th></th><?php } ?></tr></thead>
        <tbody><?php foreach ($items as $it) { $ot = $it['kind'] === 'ot' || $it['kind'] === 'ot_rest'; ?>
          <tr><td data-l="Employee"><?php echo $h($it['name']); ?></td><td data-l="Type"><?php echo $h(isset($item_kinds[$it['kind']]) ? preg_replace('/ \(.*\)$/', '', $item_kinds[$it['kind']]) : $it['kind']); ?></td><td data-l="Description"><?php echo $h($it['label']); ?></td>
          <td data-l="Hours / amount" class="text-right mono"><?php echo $ot ? (0 + $it['hours']) . ' h' . ((float)$it['amount'] > 0 ? ' = ' . $money($it['amount']) : '') : ($it['kind'] === 'deduction' ? '-' : '') . $money($it['amount']); ?></td>
          <?php if ($draft && $can_edit) { ?><td data-l="" class="nw"><form method="post" action="<?php echo $base; ?>payroll_item_delete/<?php echo (int)$it['id']; ?>" onsubmit="return confirm('Remove this item?');"><button type="submit" class="btn btn-default btn-sm" aria-label="Remove"><i class="fa fa-trash-o"></i></button></form></td><?php } ?></tr>
        <?php } ?></tbody>
      </table></div>
      <?php } ?>
    </div>
  </section>
</div>

<?php if ($draft && $can_edit) { ?>
<div class="modal fade" id="prItem" tabindex="-1" role="dialog" aria-labelledby="prItemT">
  <div class="modal-dialog" role="document"><form class="modal-content" method="post" action="<?php echo $base; ?>payroll_item_add">
    <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="prItemT">Add to <?php echo $h($mname); ?> payroll</h4></div>
    <div class="modal-body sx">
      <input type="hidden" name="month" value="<?php echo $h($run['month']); ?>">
      <div class="form-group"><label for="pi1">Employee</label><select id="pi1" name="user_id" class="form-control" required><option value="">Choose</option><?php foreach ($people as $p) { ?><option value="<?php echo (int)$p['id']; ?>"><?php echo $h($p['name']); ?></option><?php } ?></select></div>
      <div class="form-group"><label for="pi2">Type</label><select id="pi2" name="kind" class="form-control"><?php foreach ($item_kinds as $k => $v) { ?><option value="<?php echo $k; ?>"><?php echo $h($v); ?></option><?php } ?></select></div>
      <div class="form-group"><label for="pi3">Description</label><input id="pi3" name="label" class="form-control" maxlength="100" placeholder="e.g. Sales commission"></div>
      <div class="form-group pi-ot"><label for="pi4">Hours</label><input id="pi4" type="number" step="0.25" min="0" max="300" name="hours" class="form-control"><p class="help-block">Paid at basic pay / 30 / <?php echo $h($set['work_hours']); ?> hours x <span id="piRate"><?php echo $h($set['ot_rate']); ?></span>. Leave the amount empty to use this.</p></div>
      <div class="form-group"><label for="pi5">Amount (<?php echo $cur; ?>)</label><input id="pi5" type="number" step="0.01" min="0" name="amount" class="form-control"></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Add</button></div>
  </form></div>
</div>
<?php } ?>
<script>
$(function () {
  var rows = [].slice.call(document.querySelectorAll('#prTable tr.pr-row'));
  function run() {
    var s = ($('#prQ').val() || '').trim().toLowerCase(), n = 0;
    rows.forEach(function (r) { var on = !s || r.getAttribute('data-q').indexOf(s) !== -1; r.style.display = on ? '' : 'none'; if (!on) r.nextElementSibling.classList.remove('open'); if (on) n++; });
    $('#prCount').text(n + (n === 1 ? ' person' : ' people'));
  }
  $('#prQ').on('input', run); run();
  $(document).on('click', '.prMore', function () { var l = $(this).closest('tr').next('.pr-lines').toggleClass('open'); $(this).attr('aria-expanded', l.hasClass('open')); });
  var rates = { ot: '<?php echo $h($set['ot_rate']); ?>', ot_rest: '<?php echo $h($set['ot_rest_rate']); ?>' };
  function kind() {
    var k = $('#pi2').val(), ot = k === 'ot' || k === 'ot_rest';
    $('.pi-ot').toggle(ot); $('#pi4').prop('required', ot); $('#pi3').prop('required', !ot); $('#pi5').prop('required', !ot);
    $('#pi3').attr('placeholder', ot ? 'e.g. Stock count weekend' : (k === 'deduction' ? 'e.g. Traffic fine' : 'e.g. Sales commission'));
    if (ot) $('#piRate').text(rates[k]);
  }
  $('#pi2').on('change', kind); kind();
});
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
