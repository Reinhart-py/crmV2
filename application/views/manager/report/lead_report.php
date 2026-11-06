<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php require(APPPATH."views/manager/report/_rp.php"); ?>
<?php
$mrr = 0; foreach ($list as $li) $mrr += (float)$li['mrr'];
$owners = array(); foreach ($list as $li) if (!empty($li['owner_name'])) $owners[$li['owner_name']] = 1;
?>
<div class="content-wrapper">
  <div class="rp-head">
    <h1><?php echo $e($info['page_heading']); ?></h1>
    <?php if ($list) { ?><a class="btn btn-default btn-sm" href="<?php echo $e($csv_url()); ?>"><i class="fa fa-download"></i> Export CSV</a><?php } ?>
  </div>
  <section class="content" style="padding-top:0;">
    <div class="rp-box">
      <form class="rp-filter" method="get" action="<?php echo base_url(); ?>manager/leads/report">
        <div><label for="lrD1">Created from</label><input id="lrD1" type="date" class="form-control" name="d1" value="<?php echo $e($f['d1']); ?>"></div>
        <div><label for="lrD2">Created to</label><input id="lrD2" type="date" class="form-control" name="d2" value="<?php echo $e($f['d2']); ?>"></div>
        <div><label for="lrF1">Follow-up from</label><input id="lrF1" type="date" class="form-control" name="f1" value="<?php echo $e($f['f1']); ?>"></div>
        <div><label for="lrF2">Follow-up to</label><input id="lrF2" type="date" class="form-control" name="f2" value="<?php echo $e($f['f2']); ?>"></div>
        <div><label for="lrSt">Status</label>
          <select id="lrSt" class="form-control" name="status">
            <option value="">All</option>
            <?php foreach ($st_list as $s) { ?><option value="<?php echo (int)$s['id']; ?>" <?php echo (string)$f['status'] === (string)$s['id'] ? 'selected' : ''; ?>><?php echo $e($s['status']); ?></option><?php } ?>
          </select>
        </div>
        <div><label for="lrType">Type</label>
          <select id="lrType" class="form-control" name="category">
            <option value="">All</option>
            <?php foreach (array('New', 'Existing') as $c) { ?><option <?php echo $f['category'] === $c ? 'selected' : ''; ?>><?php echo $c; ?></option><?php } ?>
          </select>
        </div>
        <?php if (count($u_list) > 1) { ?>
        <div><label for="lrOwner">Owner</label>
          <select id="lrOwner" class="form-control" name="owner">
            <option value="">All</option>
            <?php foreach ($u_list as $u) { ?><option value="<?php echo (int)$u['id']; ?>" <?php echo (string)$f['owner'] === (string)$u['id'] ? 'selected' : ''; ?>><?php echo $e($u['name']); ?></option><?php } ?>
          </select>
        </div>
        <?php } ?>
        <div class="rp-btns"><button type="submit" class="btn btn-primary">Show</button><a class="btn btn-default" href="<?php echo base_url(); ?>manager/leads/report">Reset</a></div>
      </form>
    </div>

    <div class="rp-kpis">
      <div class="rp-kpi"><span>Leads</span><b><?php echo $num(count($list)); ?></b></div>
      <div class="rp-kpi"><span>Total MRR</span><b><?php echo $aed($mrr); ?></b></div>
      <div class="rp-kpi"><span>Owners</span><b><?php echo $num(count($owners)); ?></b></div>
      <div class="rp-kpi"><span>Created</span><b class="rp-sm"><?php echo $f['d1'] !== '' ? $e($day($f['d1']) . ' – ' . $day($f['d2'])) : 'All dates'; ?></b></div>
    </div>

    <div class="rp-box">
      <?php if (!$list) { ?>
        <div class="alert alert-info rp-empty">No leads for this selection.</div>
      <?php } else { ?>
      <div class="table-responsive">
        <table class="table table-bordered table-striped rp-table" id="lrTable">
          <thead><tr><th>Lead ID</th><th>Customer</th><th>POC</th><th>Contact</th><th>Type</th><th class="n">MRR</th><th>Status</th><th>Owner</th><th>Next follow-up</th><th>Created</th></tr></thead>
          <tbody>
          <?php foreach ($list as $li) { ?>
            <tr>
              <td class="nw"><a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($li['id']); ?>"><?php echo $e($li['lead_id']); ?></a></td>
              <td><?php echo $e($li['customer']); ?></td>
              <td><?php echo $e($li['poc']); ?></td>
              <td class="nw"><?php echo $e($li['contact1']); ?></td>
              <td><?php echo $e($li['category']); ?></td>
              <td class="n"><?php echo (float)$li['mrr'] > 0 ? $num($li['mrr']) : '<span class="rp-muted">–</span>'; ?></td>
              <td><?php echo $e($li['status_name']); ?></td>
              <td><?php echo $e($li['owner_name']); ?></td>
              <td class="nw"><?php echo $e($day($li['followup_date'])); ?></td>
              <td class="nw"><?php echo $e($day($li['created'])); ?></td>
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
<script>crmPager('#lrTable', 50);</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
