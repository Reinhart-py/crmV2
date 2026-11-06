<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php require(APPPATH."views/manager/report/_rp.php"); ?>
<?php
$by = array(); $leads = array();
foreach ($list as $li) { $by[(string)$li['by_name']] = 1; $leads[(int)$li['lead_id']] = 1; }
?>
<div class="content-wrapper">
  <div class="rp-head">
    <h1><?php echo $e($info['page_heading']); ?></h1>
    <?php if ($list) { ?><a class="btn btn-default btn-sm" href="<?php echo $e($csv_url()); ?>"><i class="fa fa-download"></i> Export CSV</a><?php } ?>
  </div>
  <section class="content" style="padding-top:0;">
    <div class="rp-box">
      <form class="rp-filter" method="get" action="<?php echo base_url(); ?>manager/reports/followups">
        <div><label for="frD1">From</label><input id="frD1" type="date" class="form-control" name="d1" value="<?php echo $e($f['d1']); ?>"></div>
        <div><label for="frD2">To</label><input id="frD2" type="date" class="form-control" name="d2" value="<?php echo $e($f['d2']); ?>"></div>
        <?php if (count($people) > 1) { ?>
        <div><label for="frWho">Person</label>
          <select id="frWho" class="form-control" name="person">
            <option value="">All</option>
            <?php foreach ($people as $p) { ?><option value="<?php echo (int)$p['id']; ?>" <?php echo (int)$f['person'] === (int)$p['id'] ? 'selected' : ''; ?>><?php echo $e($p['name']); ?></option><?php } ?>
          </select>
        </div>
        <?php } ?>
        <?php if ($results) { ?>
        <div><label for="frRes">Outcome</label>
          <select id="frRes" class="form-control" name="result">
            <option value="">All</option>
            <?php foreach ($results as $r) { ?><option <?php echo $f['result'] === $r ? 'selected' : ''; ?>><?php echo $e($r); ?></option><?php } ?>
          </select>
        </div>
        <?php } ?>
        <div class="rp-btns"><button type="submit" class="btn btn-primary">Show</button><a class="btn btn-default" href="<?php echo base_url(); ?>manager/reports/followups">Reset</a></div>
      </form>
    </div>

    <div class="rp-kpis">
      <div class="rp-kpi"><span>Follow-ups</span><b><?php echo $num(count($list)); ?></b></div>
      <div class="rp-kpi"><span>Leads</span><b><?php echo $num(count($leads)); ?></b></div>
      <div class="rp-kpi"><span>People</span><b><?php echo $num(count($by)); ?></b></div>
      <div class="rp-kpi"><span>Period</span><b class="rp-sm"><?php echo $e($day($f['d1']) . ' – ' . $day($f['d2'])); ?></b></div>
    </div>

    <div class="rp-box">
      <?php if (!$list) { ?>
        <div class="alert alert-info rp-empty">No follow-ups for this selection.</div>
      <?php } else { ?>
      <div class="table-responsive">
        <table class="table table-bordered table-striped rp-table" id="frTable">
          <thead><tr><th>Date</th><th>Lead ID</th><th>Customer</th><th>By</th><th>Outcome</th><th>Lead status</th><th>Remark</th></tr></thead>
          <tbody>
          <?php foreach ($list as $li) { ?>
            <tr>
              <td class="nw"><?php echo $e($day($li['fdate'])); ?><?php if (trim((string)$li['followup_time']) !== '') { ?> <span class="rp-muted"><?php echo $e($li['followup_time']); ?></span><?php } ?></td>
              <td class="nw"><?php if ($li['lead_ref'] !== null && $li['lead_ref'] !== '') { ?><a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($li['lead_id']); ?>"><?php echo $e($li['lead_ref']); ?></a><?php } else { ?><span class="rp-muted">–</span><?php } ?></td>
              <td><?php echo $e($li['customer']); ?></td>
              <td><?php echo $e($li['by_name']); ?></td>
              <td><?php echo $e($li['followup_result']); ?></td>
              <td><?php echo $e($li['status_name']); ?></td>
              <td class="rp-wrap"><?php echo $e($li['followup_remark']); ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
      <?php } ?>
    </div>
  </section>
</div>
<script src="<?php echo base_url(); ?>assets/dist/js/crm-pager.js?v=20261106_2"></script>
<script>crmPager('#frTable', 50);</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
