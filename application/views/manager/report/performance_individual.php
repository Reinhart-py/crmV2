<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php require(APPPATH."views/manager/report/_rp.php"); ?>
<?php
$cols = array('leads' => 'Leads added', 'calls' => 'Calls', 'followups' => 'Follow-ups', 'meetings' => 'Meetings', 'orders' => 'Orders', 'sales' => 'Sales', 'sales_aed' => 'Sales (AED)');
$total = array_fill_keys(array_keys($cols), 0);
foreach ($days as $d) foreach ($cols as $k => $x) $total[$k] += $d[$k];
?>
<div class="content-wrapper">
  <div class="rp-head">
    <h1><?php echo $e($info['page_heading']); ?><?php echo $person ? ' – ' . $e($person['name']) : ''; ?></h1>
    <?php if ($days) { ?><a class="btn btn-default btn-sm" href="<?php echo $e($csv_url()); ?>"><i class="fa fa-download"></i> Export CSV</a><?php } ?>
  </div>
  <section class="content" style="padding-top:0;">
    <div class="rp-box">
      <form class="rp-filter" method="get" action="<?php echo base_url(); ?>manager/performance-report/individual">
        <div><label for="ipD1">From</label><input id="ipD1" type="date" class="form-control" name="d1" value="<?php echo $e($f['d1']); ?>"></div>
        <div><label for="ipD2">To</label><input id="ipD2" type="date" class="form-control" name="d2" value="<?php echo $e($f['d2']); ?>"></div>
        <?php if (count($people) > 1) { ?>
        <div><label for="ipWho">Person</label>
          <select id="ipWho" class="form-control" name="manager_id">
            <?php foreach ($people as $p) { ?><option value="<?php echo (int)$p['id']; ?>" <?php echo (int)$f['manager_id'] === (int)$p['id'] ? 'selected' : ''; ?>><?php echo $e($p['name']); ?></option><?php } ?>
          </select>
        </div>
        <?php } ?>
        <div class="rp-btns"><button type="submit" class="btn btn-primary">Show</button><a class="btn btn-default" href="<?php echo base_url(); ?>manager/performance-report/individual">Reset</a></div>
      </form>
    </div>

    <?php if ($sum) { ?>
    <div class="rp-kpis">
      <div class="rp-kpi"><span>Department</span><b class="rp-sm"><?php echo $e($person['dept']); ?></b></div>
      <div class="rp-kpi"><span>Leads added</span><b><?php echo $num($sum['leads']); ?></b></div>
      <div class="rp-kpi"><span>Calls</span><b><?php echo $num($sum['calls']); ?></b></div>
      <div class="rp-kpi"><span>Follow-ups</span><b><?php echo $num($sum['followups']); ?></b></div>
      <div class="rp-kpi"><span>Meetings</span><b><?php echo $num($sum['meetings']); ?><?php if ($sum['meetings_missed'] > 0) { ?> <small class="rp-muted" style="font-size:12px;"><?php echo $num($sum['meetings_missed']); ?> missed</small><?php } ?></b></div>
      <div class="rp-kpi"><span>Sales</span><b><?php echo $num($sum['sales']); ?> <small class="rp-muted" style="font-size:12px;">of <?php echo $num($sum['orders']); ?> orders</small></b></div>
      <div class="rp-kpi"><span>Sales value</span><b><?php echo $aed($sum['sales_aed']); ?></b></div>
      <div class="rp-kpi"><span>Overdue follow-ups</span><b><?php echo $num($sum['overdue']); ?></b></div>
    </div>
    <?php } ?>

    <div class="rp-box">
      <?php if (!$days) { ?>
        <div class="alert alert-info rp-empty">No activity for this selection.</div>
      <?php } else { ?>
      <div class="table-responsive">
        <table class="table table-bordered table-striped rp-table" id="ipTable">
          <thead><tr><th>Date</th><?php foreach ($cols as $label) { ?><th class="n"><?php echo $label; ?></th><?php } ?></tr></thead>
          <tbody>
          <?php foreach ($days as $d) { ?>
            <tr>
              <td class="nw"><?php echo $e(date('D, d-M-Y', strtotime($d['d']))); ?></td>
              <?php foreach ($cols as $k => $x) { ?><td class="n"><?php echo $d[$k] > 0 ? $num($d[$k]) : '<span class="rp-muted">0</span>'; ?></td><?php } ?>
            </tr>
          <?php } ?>
          </tbody>
          <tfoot><tr><td>Total</td><?php foreach ($cols as $k => $x) { ?><td class="n"><?php echo $num($total[$k]); ?></td><?php } ?></tr></tfoot>
        </table>
      </div>
      <?php } ?>
    </div>
  </section>
</div>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
