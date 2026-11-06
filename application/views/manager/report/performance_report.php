<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php require(APPPATH."views/manager/report/_rp.php"); ?>
<?php
$cols = array('leads' => 'Leads added', 'calls' => 'Calls', 'followups' => 'Follow-ups', 'meetings' => 'Meetings', 'meetings_missed' => 'Meetings missed', 'orders' => 'Orders', 'sales' => 'Sales', 'sales_aed' => 'Sales (AED)', 'overdue' => 'Overdue follow-ups');
$zero = array_fill_keys(array_keys($cols), 0);
$total = $zero; $groups = array();
foreach ($rows as $r) {
	if (!isset($groups[$r['dept']])) $groups[$r['dept']] = array('rows' => array(), 'sum' => $zero);
	$groups[$r['dept']]['rows'][] = $r;
	foreach ($cols as $k => $x) { $groups[$r['dept']]['sum'][$k] += $r[$k]; $total[$k] += $r[$k]; }
}
ksort($groups);
$ind = base_url() . 'manager/performance-report/individual?' . http_build_query(array('d1' => $f['d1'], 'd2' => $f['d2'])) . '&manager_id=';
$cell = function ($k, $v) use ($num, $aed) { if ($k === 'sales_aed') return $v > 0 ? $num($v) : '<span class="rp-muted">0</span>'; return $v > 0 ? $num($v) : '<span class="rp-muted">0</span>'; };
?>
<div class="content-wrapper">
  <div class="rp-head">
    <h1><?php echo $e($info['page_heading']); ?></h1>
    <?php if ($rows) { ?><a class="btn btn-default btn-sm" href="<?php echo $e($csv_url()); ?>"><i class="fa fa-download"></i> Export CSV</a><?php } ?>
  </div>
  <section class="content" style="padding-top:0;">
    <div class="rp-box">
      <form class="rp-filter" method="get" action="<?php echo base_url(); ?>manager/performance-report">
        <div><label for="dpD1">From</label><input id="dpD1" type="date" class="form-control" name="d1" value="<?php echo $e($f['d1']); ?>"></div>
        <div><label for="dpD2">To</label><input id="dpD2" type="date" class="form-control" name="d2" value="<?php echo $e($f['d2']); ?>"></div>
        <?php if (count($departments) > 1) { ?>
        <div><label for="dpDep">Department</label>
          <select id="dpDep" class="form-control" name="department">
            <option value="">All</option>
            <?php foreach ($departments as $id => $name) { ?><option value="<?php echo (int)$id; ?>" <?php echo (int)$f['department'] === (int)$id ? 'selected' : ''; ?>><?php echo $e($name); ?></option><?php } ?>
          </select>
        </div>
        <?php } ?>
        <div><label for="dpAll">People</label>
          <select id="dpAll" class="form-control" name="all">
            <option value="">With activity</option>
            <option value="1" <?php echo $f['all'] ? 'selected' : ''; ?>>Everyone</option>
          </select>
        </div>
        <div class="rp-btns"><button type="submit" class="btn btn-primary">Show</button><a class="btn btn-default" href="<?php echo base_url(); ?>manager/performance-report">Reset</a></div>
      </form>
    </div>

    <div class="rp-kpis">
      <div class="rp-kpi"><span>Leads added</span><b><?php echo $num($total['leads']); ?></b></div>
      <div class="rp-kpi"><span>Calls</span><b><?php echo $num($total['calls']); ?></b></div>
      <div class="rp-kpi"><span>Follow-ups</span><b><?php echo $num($total['followups']); ?></b></div>
      <div class="rp-kpi"><span>Meetings</span><b><?php echo $num($total['meetings']); ?></b></div>
      <div class="rp-kpi"><span>Sales</span><b><?php echo $num($total['sales']); ?></b></div>
      <div class="rp-kpi"><span>Sales value</span><b><?php echo $aed($total['sales_aed']); ?></b></div>
    </div>

    <div class="rp-box">
      <?php if (!$rows) { ?>
        <div class="alert alert-info rp-empty">No activity for this selection.</div>
      <?php } else { ?>
      <div class="table-responsive">
        <table class="table table-bordered rp-table" id="dpTable">
          <thead><tr><th>Name</th><?php foreach ($cols as $label) { ?><th class="n"><?php echo $label; ?></th><?php } ?></tr></thead>
          <tbody>
          <?php foreach ($groups as $dept => $g) { ?>
            <tr class="rp-grp"><td><?php echo $e($dept); ?> <span class="rp-muted" style="font-weight:400;">(<?php echo count($g['rows']); ?>)</span></td><?php foreach ($cols as $k => $x) { ?><td class="n"><?php echo $cell($k, $g['sum'][$k]); ?></td><?php } ?></tr>
            <?php foreach ($g['rows'] as $r) { ?>
            <tr>
              <td style="padding-left:22px;"><a href="<?php echo $e($ind . (int)$r['id']); ?>"><?php echo $e($r['name']); ?></a></td>
              <?php foreach ($cols as $k => $x) { ?><td class="n"><?php echo $cell($k, $r[$k]); ?></td><?php } ?>
            </tr>
            <?php } ?>
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
