<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$money = function ($v) { return number_format((float)$v, 2); };
$cur = $h($set['currency']);
$uid = (int)$u['id'];
$sid = $st ? (int)$st['id'] : 0;
$chip = array('Draft' => 'w', 'Approved' => 'on', 'Paid' => 'ok');
$base = base_url() . 'manager/hr/';
$v = array(
  'reason' => $st ? $st['reason'] : ($exit && in_array($exit['reason'], $reasons, true) ? $exit['reason'] : 'Resignation'),
  'leave_days' => $st ? 0 + $st['leave_days'] : $suggest['leave_days'],
  'salary_days' => $st ? 0 + $st['salary_days'] : $suggest['salary_days'],
  'notice_pay' => $st ? 0 + $st['notice_pay'] : 0,
  'loans' => $st ? 0 + $st['loans'] : $suggest['loans'],
  'gratuity' => $st ? 0 + $st['gratuity'] : $g['amount'],
  'note' => $st ? $st['note'] : '',
  'lines' => $st && $st['extra_lines'] ? json_decode($st['extra_lines'], true) : array(),
);
$g_over = $st && abs((float)$st['gratuity'] - $g['amount']) > 0.009;
$svc_y = floor($g['days'] / 365); $svc_m = floor(($g['days'] - $svc_y * 365) / 30.4);
$step = function ($s, $label, $cls, $confirm) use ($base, $sid) {
  return '<form method="post" action="' . $base . 'settlement_step/' . $sid . '/' . $s . '" style="display:inline;" onsubmit="return confirm(' . htmlspecialchars(json_encode($confirm), ENT_QUOTES) . ');"><button type="submit" class="btn ' . $cls . '">' . $label . '</button></form>';
};
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<style>
  .fs-sum td { padding: 7px 0 !important; border-bottom: 1px solid var(--crm-line-soft, #eef2f7); }
  .fs-sum td.n { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
  .fs-sum tr.tot td { font-weight: 700; font-size: 16px; border-bottom: 0; border-top: 2px solid var(--crm-ocean, #1e3a8a); }
  .fs-sub { display: block; font-size: 12px; color: var(--crm-muted, #64748b); }
  .fs-line { display: grid; grid-template-columns: 1fr 140px 36px; gap: 6px; margin-bottom: 6px; }
  #fsPrint .hd { display: none; }
  @media print {
    body * { visibility: hidden; }
    #fsPrint, #fsPrint * { visibility: visible; }
    #fsPrint { position: absolute; left: 0; top: 0; width: 100%; border: 0; box-shadow: none; }
    #fsPrint .hd { display: block; margin-bottom: 12px; }
    #fsPrint .no-print { display: none; }
  }
</style>
<div class="content-wrapper sx">
  <?php echo crm_tabbar('hr_pay', 'hr/taxation'); ?>
  <section class="sx-head">
    <h1><a href="<?php echo $base; ?>taxation" class="text-muted" aria-label="Back" style="margin-right:6px;"><i class="fa fa-angle-left"></i></a>Final settlement: <?php echo $h($u['name']); ?><?php if ($st) { ?> <span class="chip <?php echo $chip[$st['status']]; ?>" style="vertical-align:middle;"><?php echo $h($st['status']); ?></span><?php } ?>
      <small><?php echo $doj ? 'Joined ' . $h(date('d M Y', strtotime($doj))) : 'Joining date not set'; ?><?php echo $exit ? ' · Marked as left on ' . $h(date('d M Y', strtotime($exit['left_on']))) : ''; ?></small></h1>
    <div class="sx-btns">
      <?php if ($st) { ?><button type="button" class="btn btn-default" onclick="window.print()"><i class="fa fa-print"></i> Print / PDF</button><?php } ?>
      <?php if ($st && $st['status'] === 'Draft' && $can_approve) echo $step('approve', '<i class="fa fa-check"></i> Approve', 'btn-primary', 'Approve this final settlement?'); ?>
      <?php if ($st && $st['status'] === 'Approved' && $this->is_admin || ($st && $st['status'] === 'Approved' && crm_can('hr_payroll', 'write'))) echo $step('paid', '<i class="fa fa-check-circle"></i> Mark as paid', 'btn-primary', 'Mark as paid? Loans still owed are closed with this settlement. This cannot be undone.'); ?>
      <?php if ($st && $st['status'] === 'Approved' && $can_approve) echo $step('draft', '<i class="fa fa-unlock"></i> Reopen', 'btn-default', 'Reopen as a draft?'); ?>
      <?php if ($st && $st['status'] === 'Draft' && $can_edit) echo $step('delete', '<i class="fa fa-trash-o"></i>', 'btn-default', 'Delete this draft settlement?'); ?>
    </div>
  </section>
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>
    <?php if (!$has_salary) { ?><div class="sx-card" style="border-left:4px solid #f59e0b;"><div class="sx-card-b"><i class="fa fa-exclamation-triangle" style="color:#b45309;"></i> No salary is set for <?php echo $h($u['name']); ?>. Add it in the <a href="<?php echo $base; ?>employees/<?php echo $uid; ?>#salary">HR file</a> first.</div></div><?php } ?>
    <?php if (!$exit && !$st) { ?><p class="sx-help" style="margin-top:0;"><i class="fa fa-info-circle"></i> <?php echo $h($u['name']); ?> is still working. When they leave, also mark them as left in Staff so their login stops.</p><?php } ?>

    <div class="sx-grid" style="grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start;">
      <?php if ($can_edit && $has_salary) { ?>
      <form class="sx-card" method="post" action="<?php echo $base; ?>settlement_save" id="fsForm" style="margin:0;">
        <input type="hidden" name="id" value="<?php echo $sid; ?>"><input type="hidden" name="user_id" value="<?php echo $uid; ?>">
        <div class="sx-card-h"><h3><i class="fa fa-pencil"></i> Work it out</h3></div>
        <div class="sx-card-b">
          <div class="sx-grid" style="grid-template-columns:1fr 1fr;">
            <div class="form-group"><label for="fs1">Last working day</label><input id="fs1" type="date" name="last_day" class="form-control" value="<?php echo $h($last); ?>" required></div>
            <div class="form-group"><label for="fs2">Reason</label><select id="fs2" name="reason" class="form-control"><?php foreach ($reasons as $r) { ?><option<?php echo $r === $v['reason'] ? ' selected' : ''; ?>><?php echo $h($r); ?></option><?php } ?></select></div>
          </div>
          <p class="fs-sub" id="fsDateHint" style="display:none;margin:-6px 0 10px;"><i class="fa fa-info-circle"></i> Gratuity and service are worked out again for the new date when you save.</p>
          <div class="form-group"><label for="fs3">Unused annual leave (days)</label><input id="fs3" type="number" step="0.5" min="0" name="leave_days" class="form-control fs-in" value="<?php echo $h($v['leave_days']); ?>"><p class="help-block">Paid at basic / 30 = <?php echo $money($basic / 30); ?> a day. Suggested from the leave balance: <?php echo $h($suggest['leave_days']); ?>.</p></div>
          <div class="form-group"><label for="fs4">Salary days not yet paid</label><input id="fs4" type="number" step="0.5" min="0" max="31" name="salary_days" class="form-control fs-in" value="<?php echo $h($v['salary_days']); ?>"><p class="help-block">Paid at full salary / 30 = <?php echo $money($gross / 30); ?> a day.<?php echo $suggest['paid_month'] ? ' The payroll for this month already includes them.' : ''; ?></p></div>
          <div class="form-group"><label for="fs5">Notice pay (<?php echo $cur; ?>)</label><input id="fs5" type="number" step="0.01" name="notice_pay" class="form-control fs-in" value="<?php echo $h($v['notice_pay']); ?>"><p class="help-block">Plus if the company pays notice in lieu; minus if the employee did not work their notice.</p></div>
          <div class="form-group"><label for="fs6">Loans and advances to recover (<?php echo $cur; ?>)</label><input id="fs6" type="number" step="0.01" min="0" name="loans" class="form-control fs-in" value="<?php echo $h($v['loans']); ?>"></div>
          <div class="form-group"><label for="fs7">Gratuity (<?php echo $cur; ?>)</label><input id="fs7" type="number" step="0.01" min="0" name="gratuity_override" class="form-control fs-in" value="<?php echo $g_over ? $h($v['gratuity']) : ''; ?>" placeholder="<?php echo $money($g['amount']); ?> (worked out)"><p class="help-block">Leave empty to use the amount worked out by law.</p></div>
          <span class="sx-label">Other lines (minus for deductions)</span>
          <div id="fsLines"><?php foreach ($v['lines'] ? $v['lines'] : array(array('', '')) as $l) { ?><div class="fs-line"><input name="line_label[]" class="form-control" maxlength="100" placeholder="e.g. Air ticket" value="<?php echo $h($l[0]); ?>" aria-label="Description"><input name="line_amount[]" type="number" step="0.01" class="form-control fs-in" value="<?php echo $h($l[1]); ?>" aria-label="Amount"><button type="button" class="btn btn-default fsDel" aria-label="Remove line"><i class="fa fa-times"></i></button></div><?php } ?></div>
          <button type="button" class="btn btn-default btn-sm" id="fsAdd"><i class="fa fa-plus"></i> Add line</button>
          <div class="form-group" style="margin-top:12px;"><label for="fs8">Note</label><textarea id="fs8" name="note" class="form-control" rows="2" maxlength="500"><?php echo $h($v['note']); ?></textarea></div>
        </div>
        <div class="sx-card-f" style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;"><span>Total: <b id="fsLive" class="mono"></b> <?php echo $cur; ?></span><button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Save draft</button></div>
      </form>
      <?php } ?>

      <div class="sx-card" id="fsPrint" style="margin:0;">
        <div class="hd"><h2 style="margin:0;font-size:20px;"><?php echo $h($set['company_name']); ?></h2><div>Final settlement</div></div>
        <div class="sx-card-h"><h3><i class="fa fa-file-text-o"></i> <?php echo $st ? 'Settlement statement' : 'Worked out by law'; ?></h3></div>
        <div class="sx-card-b">
          <table class="fs-sum" style="width:100%;">
            <tr><td>Employee</td><td class="n"><?php echo $h($u['name']); ?><?php echo $u['emp_id'] ? ' (' . $h($u['emp_id']) . ')' : ''; ?></td></tr>
            <tr><td>Joined / last day</td><td class="n"><?php echo $doj ? $h(date('d M Y', strtotime($doj))) : '-'; ?> / <?php echo $h(date('d M Y', strtotime($st ? $st['last_day'] : $last))); ?></td></tr>
            <tr><td>Service<span class="fs-sub"><?php echo $g['days']; ?> days<?php echo $unpaid > 0 ? ', less ' . (0 + $unpaid) . ' days unpaid leave' : ''; ?></span></td><td class="n"><?php echo $svc_y; ?> yr <?php echo $svc_m; ?> mo</td></tr>
            <tr><td>Basic / full monthly salary</td><td class="n"><?php echo $money($st ? $st['basic'] : $basic); ?> / <?php echo $money($st ? $st['gross'] : $gross); ?></td></tr>
            <tr><td>End-of-service gratuity<?php if ($g['years'] < 1) { ?><span class="fs-sub">Under one year of service</span><?php } ?></td><td class="n" data-k="gratuity"><?php echo $money($v['gratuity']); ?></td></tr>
            <tr><td>Unused annual leave<span class="fs-sub" data-k="leave_t"><?php echo 0 + $v['leave_days']; ?> days</span></td><td class="n" data-k="leave"><?php echo $money($st ? $st['leave_pay'] : $v['leave_days'] * $basic / 30); ?></td></tr>
            <tr><td>Salary not yet paid<span class="fs-sub" data-k="salary_t"><?php echo 0 + $v['salary_days']; ?> days</span></td><td class="n" data-k="salary"><?php echo $money($st ? $st['salary_pay'] : $v['salary_days'] * $gross / 30); ?></td></tr>
            <tr><td>Notice pay</td><td class="n" data-k="notice"><?php echo $money($v['notice_pay']); ?></td></tr>
            <?php foreach ($v['lines'] as $l) { ?><tr class="fs-x"><td><?php echo $h($l[0]); ?></td><td class="n"><?php echo $money($l[1]); ?></td></tr><?php } ?>
            <tr><td>Less: loans and advances</td><td class="n" data-k="loans">-<?php echo $money($v['loans']); ?></td></tr>
            <tr class="tot"><td>Total to pay</td><td class="n"><?php echo $cur; ?> <span data-k="total"><?php echo $money($st ? $st['total'] : 0); ?></span></td></tr>
          </table>
          <?php if ($v['note'] !== '') { ?><p style="margin-top:10px;"><?php echo nl2br($h($v['note'])); ?></p><?php } ?>
          <?php if ($st && $st['status'] !== 'Draft') { ?><p class="fs-sub" style="margin-top:10px;">Approved <?php echo $h(date('d M Y', strtotime($st['approved_at']))); ?><?php echo !empty($approved_name) ? ' by ' . $h($approved_name) : ''; ?><?php echo $st['paid_at'] ? ' · Paid ' . $h(date('d M Y', strtotime($st['paid_at']))) : ''; ?></p><?php } ?>
          <?php if (!$st) { ?><p class="fs-sub no-print" style="margin-top:10px;">Not saved yet.</p><?php } ?>
          <div class="hd" style="margin-top:40px;display:none;"></div>
        </div>
      </div>
    </div>
  </section>
</div>
<script>
$(function () {
  var basic = <?php echo json_encode((float)$basic); ?>, gross = <?php echo json_encode((float)$gross); ?>, legal = <?php echo json_encode((float)$g['amount']); ?>;
  var f = document.getElementById('fsForm');
  if (!f) return;
  function n(sel) { return parseFloat($(sel).val()) || 0; }
  function fmt(x) { return x.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
  function calc() {
    var gr = $('#fs7').val() === '' ? legal : n('#fs7'), lv = Math.round(n('#fs3') * basic / 30 * 100) / 100, sa = Math.round(n('#fs4') * gross / 30 * 100) / 100, no = n('#fs5'), lo = n('#fs6'), ex = 0;
    $('#fsLines input[name="line_amount[]"]').each(function () { ex += parseFloat(this.value) || 0; });
    var t = gr + lv + sa + no - lo + ex;
    $('#fsLive').text(fmt(t));
    $('[data-k=gratuity]').text(fmt(gr)); $('[data-k=leave]').text(fmt(lv)); $('[data-k=salary]').text(fmt(sa)); $('[data-k=notice]').text(fmt(no)); $('[data-k=loans]').text('-' + fmt(lo)); $('[data-k=total]').text(fmt(t));
    $('[data-k=leave_t]').text(n('#fs3') + ' days'); $('[data-k=salary_t]').text(n('#fs4') + ' days');
  }
  $(f).on('input change', '.fs-in, #fs7', calc);
  $('#fsAdd').on('click', function () { var l = $('#fsLines .fs-line').first().clone(); l.find('input').val(''); $('#fsLines').append(l); });
  $(document).on('click', '.fsDel', function () { if ($('#fsLines .fs-line').length > 1) $(this).closest('.fs-line').remove(); else $(this).closest('.fs-line').find('input').val(''); calc(); });
  $('#fs1').on('change', function () {
    <?php if (!$st) { ?>if (this.value) location.href = '<?php echo $base; ?>settlement/<?php echo $uid; ?>?last_day=' + encodeURIComponent(this.value);<?php } else { ?>$('#fsDateHint').show();<?php } ?>
  });
  calc();
});
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
