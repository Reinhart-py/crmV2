<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$base = base_url() . 'manager/hr/leave_calendar';
$max_off = $off ? max($off) : 0;
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_8">
<style>
  .lc-grid { border-collapse: separate; border-spacing: 0; font-size: 12px; }
  .lc-grid th, .lc-grid td { padding: 4px 2px !important; text-align: center !important; border-bottom: 1px solid var(--crm-line-soft, #eef2f7); min-width: 26px; }
  .lc-grid .nm { position: sticky; left: 0; z-index: 2; background: var(--crm-surface, #fff); text-align: left !important; padding-left: 10px !important; min-width: 170px; white-space: nowrap; }
  .lc-grid .we { background: var(--crm-bg-soft, #f8fafc); }
  .lc-grid .ho { background: rgba(220, 38, 38, .06); }
  .lc-grid .td { box-shadow: inset 0 0 0 2px var(--crm-ocean, #1e3a8a); }
  .lc-b { display: block; height: 18px; border-radius: 4px; background: #7c3aed; }
  .lc-b.p { background: repeating-linear-gradient(45deg, #c4b5fd, #c4b5fd 4px, #ede9fe 4px, #ede9fe 8px); }
  .lc-n { font-weight: 700; }
  .lc-n.hi { color: #b91c1c; }
  .lc-hol { list-style: none; margin: 0; padding: 0; display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 8px; }
  .lc-hol li { border: 1px solid var(--crm-line, #e2e8f0); border-radius: 10px; padding: 8px 12px; font-size: 13.5px; }
  .lc-hol li.past { opacity: .55; }
  .lc-hol b { display: block; }
  .lc-legend { display: flex; flex-wrap: wrap; gap: 14px; padding: 10px 14px; font-size: 12.5px; color: var(--crm-muted, #64748b); align-items: center; }
  .lc-legend i.sw { display: inline-block; width: 18px; height: 12px; border-radius: 3px; vertical-align: middle; margin-right: 4px; }
</style>
<div class="content-wrapper sx sx-hr">
  <?php echo crm_tabbar('hr_time', 'hr/leave_calendar'); ?>
  <section class="sx-head">
    <h1>Leave Calendar<small><?php echo $h(date('F Y', strtotime($month . '-01'))); ?></small></h1>
    <div class="sx-btns" style="align-items:center;">
      <a class="btn btn-default" href="<?php echo $base; ?>?month=<?php echo date('Y-m', strtotime($month . '-01 -1 month')); ?>" aria-label="Previous month"><i class="fa fa-angle-left"></i></a>
      <form method="get" style="margin:0;"><input type="month" name="month" class="form-control" style="height:34px;" value="<?php echo $h($month); ?>" onchange="this.form.submit()" aria-label="Month"></form>
      <a class="btn btn-default" href="<?php echo $base; ?>?month=<?php echo date('Y-m', strtotime($month . '-01 +1 month')); ?>" aria-label="Next month"><i class="fa fa-angle-right"></i></a>
    </div>
  </section>
  <section class="content">
    <div class="sx-card">
      <?php if (!$people) { ?><div class="sx-card-b"><div class="sx-empty"><i class="fa fa-calendar-check-o"></i> Nobody has leave this month.</div></div><?php } else { ?>
      <div class="sx-table-wrap">
        <table class="sx-table lc-grid">
          <thead>
            <tr><th class="nm">Employee</th><?php foreach ($days as $dd) { $cls = (isset($holidays[$dd]) ? 'ho' : (in_array(date('D', strtotime($dd)), $weekend, true) ? 'we' : '')) . ($dd === date('Y-m-d') ? ' td' : ''); ?><th class="<?php echo $cls; ?>" title="<?php echo $h(date('D d M', strtotime($dd)) . (isset($holidays[$dd]) ? ': ' . $holidays[$dd] : '')); ?>"><?php echo date('j', strtotime($dd)); ?><br><small><?php echo substr(date('D', strtotime($dd)), 0, 2); ?></small></th><?php } ?></tr>
          </thead>
          <tbody>
          <?php foreach ($people as $p) { ?>
            <tr><td class="nm"><b><?php echo $h($p['name']); ?></b><span class="text-muted" style="display:block;font-size:11.5px;"><?php echo $h($p['department']); ?></span></td>
            <?php foreach ($days as $dd) { $hit = null; foreach ($p['leaves'] as $l) if ($dd >= $l['from_date'] && $dd <= $l['to_date']) { $hit = $l; if ($l['status'] === 'Approved') break; }
              $cls = isset($holidays[$dd]) ? 'ho' : (in_array(date('D', strtotime($dd)), $weekend, true) ? 'we' : ''); ?>
              <td class="<?php echo $cls; ?>"><?php if ($hit) { ?><span class="lc-b<?php echo $hit['status'] === 'Pending' ? ' p' : ''; ?>" title="<?php echo $h($hit['leave_type'] . ' (' . strtolower($hit['status']) . '): ' . date('d M', strtotime($hit['from_date'])) . ' - ' . date('d M', strtotime($hit['to_date']))); ?>"></span><?php } ?></td>
            <?php } ?></tr>
          <?php } ?>
          <tr><td class="nm"><b>Away (approved)</b></td><?php foreach ($days as $dd) { $n = isset($off[$dd]) ? $off[$dd] : 0; ?><td><?php if ($n) { ?><span class="lc-n<?php echo $n === $max_off && $n > 1 ? ' hi' : ''; ?>"><?php echo $n; ?></span><?php } ?></td><?php } ?></tr>
          </tbody>
        </table>
      </div>
      <?php } ?>
      <div class="lc-legend"><span><i class="sw" style="background:#7c3aed"></i>Approved</span><span><i class="sw" style="background:repeating-linear-gradient(45deg,#c4b5fd,#c4b5fd 4px,#ede9fe 4px,#ede9fe 8px)"></i>Waiting for approval</span><span><i class="sw" style="background:rgba(220,38,38,.12)"></i>Public holiday</span><span><i class="sw" style="background:#f1f5f9"></i>Weekend</span></div>
    </div>

    <div class="sx-card">
      <div class="sx-card-h"><h3><i class="fa fa-flag"></i> Public holidays <?php echo $h(substr($month, 0, 4)); ?></h3><?php if (!empty($can_events)) { ?><div class="sx-tools"><a class="btn btn-default btn-sm" href="<?php echo base_url(); ?>manager/events/manage"><i class="fa fa-plus"></i> Add in Events &amp; Festivals</a></div><?php } ?></div>
      <div class="sx-card-b">
        <?php if (!$year_holidays) { ?><div class="sx-empty">No public holidays yet. Add them in Events &amp; Festivals with the type "Public Holiday"; they are then left out of leave days and nobody is marked absent on them.</div>
        <?php } else { $grp = array(); foreach ($year_holidays as $d => $t) $grp[$t][] = $d; ?>
          <ul class="lc-hol"><?php foreach ($grp as $t => $ds) { $a = reset($ds); $z = end($ds); ?><li class="<?php echo $z < date('Y-m-d') ? 'past' : ''; ?>"><b><?php echo $h($t); ?></b><?php echo $h(date('D d M', strtotime($a)) . ($z !== $a ? ' - ' . date('D d M', strtotime($z)) : '')); ?><?php echo count($ds) > 1 ? ' (' . count($ds) . ' days)' : ''; ?></li><?php } ?></ul>
        <?php } ?>
      </div>
    </div>
  </section>
</div>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
