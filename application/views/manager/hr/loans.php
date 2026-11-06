<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$money = function ($v) { return number_format((float)$v, 2); };
$cur = $h($set['currency']);
$chip = array('Active' => 'on', 'Repaid' => 'ok', 'Closed' => 'na');
$open = 0.0; $n_open = 0;
foreach ($loans as $l) if ($l['status'] === 'Active') { $open += max(0, (float)$l['amount'] - (float)$l['repaid']); $n_open++; }
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<style>
  .ln-bar { height: 6px; border-radius: 3px; background: var(--crm-line, #e2e8f0); overflow: hidden; margin-top: 5px; min-width: 90px; }
  .ln-bar i { display: block; height: 100%; background: #16a34a; }
  .ln-sub { display: block; font-size: 12px; color: var(--crm-muted, #64748b); }
</style>
<div class="content-wrapper sx">
  <?php echo crm_tabbar('hr_pay', 'hr/loans'); ?>
  <section class="sx-head">
    <h1>Loans &amp; Advances<small>Money lent to staff, taken back from salary each month in payroll</small></h1>
    <?php if ($can_edit) { ?><div class="sx-btns"><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#lnModal"><i class="fa fa-plus"></i> Add loan or advance</button></div><?php } ?>
  </section>
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>
    <div class="sx-kpis">
      <div class="sx-kpi a"><span class="ic"><i class="fa fa-credit-card"></i></span><div><b><?php echo $n_open; ?></b><span>Being repaid</span></div></div>
      <div class="sx-kpi"><span class="ic"><i class="fa fa-money"></i></span><div><b><?php echo $money($open); ?></b><span>Still owed, <?php echo $cur; ?></span></div></div>
    </div>
    <div class="sx-card">
      <?php if (!$loans) { ?><div class="sx-card-b"><div class="sx-empty"><i class="fa fa-credit-card"></i> No loans or salary advances.</div></div><?php } else { ?>
      <div class="sx-table-wrap">
        <table class="sx-table sx-cards">
          <thead><tr><th>Employee</th><th>Type</th><th class="text-right">Amount</th><th class="text-right">Each month</th><th>Repaid</th><th>Status</th><?php if ($can_edit) { ?><th></th><?php } ?></tr></thead>
          <tbody>
          <?php foreach ($loans as $l) { $left = max(0, (float)$l['amount'] - (float)$l['repaid']); $pc = (float)$l['amount'] > 0 ? min(100, round(100 * (float)$l['repaid'] / (float)$l['amount'])) : 0; ?>
            <tr>
              <td data-l="Employee"><b><?php echo $h($l['name']); ?></b><?php if ($l['reason'] !== '') { ?><span class="ln-sub"><?php echo $h($l['reason']); ?></span><?php } ?></td>
              <td data-l="Type"><?php echo $h($l['kind']); ?><span class="ln-sub">from <?php echo $h(date('M Y', strtotime($l['start_month'] . '-01'))); ?></span></td>
              <td data-l="Amount" class="text-right mono"><?php echo $money($l['amount']); ?></td>
              <td data-l="Each month" class="text-right mono"><?php echo $money($l['monthly']); ?></td>
              <td data-l="Repaid"><?php echo $money($l['repaid']); ?> <span class="ln-sub"><?php echo $money($left); ?> left</span><div class="ln-bar" aria-hidden="true"><i style="width:<?php echo $pc; ?>%"></i></div></td>
              <td data-l="Status"><span class="chip <?php echo $chip[$l['status']]; ?>"><?php echo $h($l['status']); ?></span><?php if ($l['close_note'] !== '') { ?><span class="ln-sub"><?php echo $h($l['close_note']); ?></span><?php } ?></td>
              <?php if ($can_edit) { ?><td data-l="" class="nw"><?php if ($l['status'] === 'Active') { ?><button type="button" class="btn btn-default btn-sm lnClose" data-id="<?php echo (int)$l['id']; ?>" data-n="<?php echo $h($l['name']); ?>"><i class="fa fa-stop-circle-o"></i> Stop</button><?php } ?></td><?php } ?>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
      <?php } ?>
    </div>
    <p class="sx-help"><i class="fa fa-info-circle"></i> Repayments count once a month's payroll is approved. A loan is marked Repaid when the last part is paid. On a final settlement, what is still owed is taken from the settlement.</p>
  </section>
</div>

<?php if ($can_edit) { ?>
<div class="modal fade" id="lnModal" tabindex="-1" role="dialog" aria-labelledby="lnModalT">
  <div class="modal-dialog" role="document"><form class="modal-content" method="post" action="<?php echo base_url(); ?>manager/hr/loan_save">
    <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="lnModalT">Add loan or salary advance</h4></div>
    <div class="modal-body sx">
      <div class="form-group"><label for="ln1">Employee</label><select id="ln1" name="user_id" class="form-control" required><option value="">Choose</option><?php foreach ($people as $p) { ?><option value="<?php echo (int)$p['id']; ?>"><?php echo $h($p['name']); ?></option><?php } ?></select></div>
      <div class="form-group"><label for="ln2">Type</label><select id="ln2" name="kind" class="form-control"><option value="Loan">Loan (repaid over several months)</option><option value="Advance">Salary advance (taken back next payroll)</option></select></div>
      <div class="sx-grid" style="grid-template-columns:1fr 1fr;">
        <div class="form-group"><label for="ln3">Amount (<?php echo $cur; ?>)</label><input id="ln3" type="number" step="0.01" min="1" name="amount" class="form-control" required></div>
        <div class="form-group"><label for="ln4">Repay each month</label><input id="ln4" type="number" step="0.01" min="1" name="monthly" class="form-control"></div>
      </div>
      <p class="help-block" id="lnHint" style="margin-top:-4px;"></p>
      <div class="form-group"><label for="ln5">First repayment in</label><input id="ln5" type="month" name="start_month" class="form-control" required value="<?php echo date('Y-m'); ?>"></div>
      <div class="form-group"><label for="ln6">Reason</label><input id="ln6" name="reason" class="form-control" maxlength="255"></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
  </form></div>
</div>
<div class="modal fade" id="lnStop" tabindex="-1" role="dialog" aria-labelledby="lnStopT">
  <div class="modal-dialog modal-sm" role="document"><form class="modal-content" method="post" id="lnStopF">
    <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="lnStopT">Stop repayments</h4></div>
    <div class="modal-body sx"><p id="lnStopN"></p><div class="form-group"><label for="ln7">Why</label><input id="ln7" name="note" class="form-control" maxlength="255" required placeholder="e.g. Paid back in cash"></div></div>
    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Stop</button></div>
  </form></div>
</div>
<script>
$(function () {
  function hint() {
    var a = parseFloat($('#ln3').val()) || 0, m = parseFloat($('#ln4').val()) || 0, adv = $('#ln2').val() === 'Advance';
    $('#ln4').prop('required', !adv).closest('.form-group').toggle(!adv);
    $('#lnHint').text(adv ? 'The whole amount is taken from the next payroll.' : (a && m ? 'Repaid in ' + Math.ceil(a / m) + ' month' + (Math.ceil(a / m) === 1 ? '' : 's') + '.' : ''));
  }
  $('#ln2,#ln3,#ln4').on('input change', hint); hint();
  $(document).on('click', '.lnClose', function () { $('#lnStopF').attr('action', '<?php echo base_url(); ?>manager/hr/loan_close/' + $(this).data('id')); $('#lnStopN').text($(this).data('n') + ': no more repayments will be taken from salary.'); $('#lnStop').modal('show'); });
});
</script>
<?php } ?>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
