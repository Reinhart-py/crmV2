<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$cur = $h($set['currency']);
$money = function ($v) { return number_format((float)$v, 2); };
$chip = array('Draft' => 'w', 'Approved' => 'on', 'Paid' => 'ok');
$last = $runs ? $runs[0] : null;
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<div class="content-wrapper sx">
  <?php echo crm_tabbar('hr_pay', 'hr/payroll'); ?>
  <section class="sx-head">
    <h1>Payroll<small>Each month: work out pay, add overtime and other items, approve, pay through WPS, and staff see their payslips</small></h1>
    <?php if ($can_edit) { ?>
    <form class="sx-btns" method="post" action="<?php echo base_url(); ?>manager/hr/payroll/generate" style="align-items:center;">
      <input type="month" name="month_year" class="form-control" style="height:34px;width:auto;" value="<?php echo $h($next_month); ?>" required aria-label="Month">
      <button type="submit" class="btn btn-primary"><i class="fa fa-play"></i> Start payroll</button>
    </form>
    <?php } ?>
  </section>
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <?php if ($last) { ?>
    <div class="sx-kpis">
      <a class="sx-kpi" href="<?php echo base_url(); ?>manager/hr/payroll_run/<?php echo (int)$last['id']; ?>"><span class="ic"><i class="fa fa-calendar"></i></span><div><b><?php echo $h(date('M Y', strtotime($last['month'] . '-01'))); ?></b><span>Latest payroll: <?php echo $h(strtolower($last['status'])); ?></span></div></a>
      <div class="sx-kpi s"><span class="ic"><i class="fa fa-users"></i></span><div><b><?php echo (int)$last['people']; ?></b><span>People paid</span></div></div>
      <div class="sx-kpi g"><span class="ic"><i class="fa fa-money"></i></span><div><b><?php echo $money($last['net']); ?></b><span>Net pay, <?php echo $cur; ?></span></div></div>
      <a class="sx-kpi a" href="<?php echo base_url(); ?>manager/hr/loans"><span class="ic"><i class="fa fa-credit-card"></i></span><div><b><?php echo (int)$loans_open; ?></b><span>Loans being repaid</span></div></a>
    </div>
    <?php } ?>

    <div class="sx-card">
      <div class="sx-card-h"><h3><i class="fa fa-list"></i> Pay runs</h3></div>
      <?php if (!$runs) { ?>
        <div class="sx-card-b"><div class="sx-empty"><i class="fa fa-money"></i> No payroll yet. Choose a month and press Start payroll. Salaries come from each person's HR file (Employees &gt; Salary).</div></div>
      <?php } else { ?>
      <div class="sx-table-wrap">
        <table class="sx-table sx-cards">
          <thead><tr><th>Month</th><th>Status</th><th>People</th><th class="text-right">Gross</th><th class="text-right">Deductions</th><th class="text-right">Net pay</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($runs as $r) { ?>
            <tr>
              <td data-l="Month"><b><?php echo $h(date('F Y', strtotime($r['month'] . '-01'))); ?></b></td>
              <td data-l="Status"><span class="chip <?php echo $chip[$r['status']]; ?>"><?php echo $h($r['status']); ?></span><?php if ($r['status'] !== 'Draft' && $r['approved_name']) { ?><span class="text-muted" style="display:block;font-size:12px;">by <?php echo $h($r['approved_name']); ?></span><?php } ?></td>
              <td data-l="People"><?php echo (int)$r['people']; ?></td>
              <td data-l="Gross" class="text-right mono"><?php echo $money($r['gross']); ?></td>
              <td data-l="Deductions" class="text-right mono"><?php echo $money($r['deductions']); ?></td>
              <td data-l="Net pay" class="text-right mono"><b><?php echo $money($r['net']); ?></b></td>
              <td data-l="" class="nw"><a class="btn btn-default btn-sm" href="<?php echo base_url(); ?>manager/hr/payroll_run/<?php echo (int)$r['id']; ?>"><i class="fa fa-folder-open-o"></i> Open</a></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
      <?php } ?>
    </div>

    <?php if ($old) { ?>
    <div class="sx-card">
      <div class="sx-card-h"><h3><i class="fa fa-archive"></i> Earlier payroll records</h3></div>
      <div class="sx-card-b" style="padding-bottom:0;"><p class="sx-help" style="margin:0;">Records made with the old payroll screen. They are kept as they were; download a month as CSV.</p></div>
      <div class="sx-table-wrap">
        <table class="sx-table sx-cards">
          <thead><tr><th>Month</th><th>People</th><th>Paid</th><th class="text-right">Net</th><th></th></tr></thead>
          <tbody><?php foreach ($old as $o) { ?>
            <tr><td data-l="Month"><?php echo $h(preg_match('/^\d{4}-\d{2}$/', $o['month_year']) ? date('F Y', strtotime($o['month_year'] . '-01')) : $o['month_year']); ?></td><td data-l="People"><?php echo (int)$o['n']; ?></td><td data-l="Paid"><?php echo (int)$o['paid']; ?></td><td data-l="Net" class="text-right mono"><?php echo $money($o['net']); ?></td>
            <td data-l="" class="nw"><a class="btn btn-default btn-sm" href="<?php echo base_url(); ?>manager/hr/payroll_old_csv?month=<?php echo urlencode($o['month_year']); ?>"><i class="fa fa-download"></i> CSV</a></td></tr>
          <?php } ?></tbody>
        </table>
      </div>
    </div>
    <?php } ?>
    <p class="sx-help"><i class="fa fa-info-circle"></i> Pay is worked out from the salary in force on the last day of the month, less unpaid leave (UAE sick leave: 15 days full pay, 30 half, then unpaid), loan repayments and other deductions. People who join or leave during the month are paid for their days. Rates and rules are in HR Settings.</p>
  </section>
</div>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
