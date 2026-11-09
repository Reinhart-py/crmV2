<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php require(APPPATH."views/manager/report/_rp.php"); ?>
<?php
$base = base_url() . 'manager/reports/calls';
$talk = function ($s) { $s = (int)$s; if ($s <= 0) return '0m'; $h = floor($s / 3600); $m = floor($s / 60) % 60; $x = $s % 60; return $h > 0 ? $h . 'h ' . $m . 'm' : ($m > 0 ? $m . 'm ' . $x . 's' : $x . 's'); };
$one = function ($x) { return rtrim(rtrim(number_format((float)$x, 1), "0"), "."); };
$rate = function ($c, $n) { return $n > 0 ? round($c * 100 / $n) . '%' : '–'; };
$keep = function ($extra) use ($f) {
  $q = array('by' => $f['by']);
  if ($f['person']) $q['person'] = $f['person'];
  if ($f['internal']) $q['internal'] = '1';
  return '?' . http_build_query(array_merge($q, $extra));
};
$label = function ($r) use ($f) {
  if ($f['by'] === 'month') return date('M Y', strtotime($r['p']));
  if ($f['by'] === 'week') return date('d M', strtotime($r['from'])) . ' – ' . date('d M Y', strtotime($r['to']));
  return date('D, d M Y', strtotime($r['p']));
};
$tot = array('calls' => 0, 'connected' => 0, 'talk' => 0, 'followups' => 0);
$max = 0;
foreach ($periods as $r) { foreach ($tot as $k => $v) $tot[$k] += $r[$k]; $max = max($max, $r['calls']); }
$active = 0; foreach ($periods as $r) if ($r['calls'] > 0) $active++;
$unit = $f['by'] === 'month' ? 'month' : ($f['by'] === 'week' ? 'week' : 'day');
$csv = function ($part) { $q = $_GET; $q['export'] = 'csv'; if ($part) $q['part'] = $part; else unset($q['part']); return '?' . http_build_query($q); };
?>
<style>
.cr-modes{display:inline-flex;gap:2px;padding:3px;border:1px solid var(--crm-border,#e2e8f0);border-radius:10px;background:var(--crm-surface,#fff)}
.cr-modes a{padding:6px 14px;border-radius:8px;font-size:13px;font-weight:600;color:var(--crm-text-muted,#475569);text-decoration:none}
.cr-modes a:hover{color:var(--crm-text,#0f172a);background:rgba(100,116,139,.08)}
.cr-modes a.is-on{background:#2563eb;color:#fff}
html.crm-modern .cr-modes a.is-on{background:var(--md2-fill)}
.rp-filter label.cr-check{display:flex;align-items:center;gap:6px;height:34px;font-size:13px;font-weight:500 !important;margin:0 !important;white-space:nowrap}
.cr-bar{display:flex;height:8px;border-radius:999px;background:rgba(100,116,139,.12);overflow:hidden}
.cr-bar i{display:block;height:100%}
.cr-bar .ok{background:#22a06b}
.cr-bar .no{background:#f2a65a}
.cr-key{display:flex;gap:14px;font-size:12px;color:var(--crm-text-muted,#64748b);margin:0 0 8px;flex-wrap:wrap}
.cr-key i{display:inline-block;width:10px;height:10px;border-radius:3px;margin-right:5px;vertical-align:-1px}
.cr-h2{display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;margin:0 0 8px}
.cr-h2 h2{margin:0;font-size:15px;font-weight:600;color:var(--crm-text,#0f172a)}
.rp-table tr.cr-zero td,.rp-table tr.cr-zero td.cr-ok{color:#94a3b8 !important}
.cr-ok{color:#15803d}
[data-theme="dark"] .cr-ok{color:#4ade80}
[data-theme="dark"] .cr-modes{background:transparent;border-color:rgba(148,163,184,.3)}
[data-theme="dark"] .cr-modes a{color:#cbd5e1}
[data-theme="dark"] .cr-modes a.is-on{color:#fff}
[data-theme="dark"] .rp-table tr.cr-zero td,[data-theme="dark"] .rp-table tr.cr-zero td.cr-ok{color:#64748b !important}
</style>
<div class="content-wrapper">
  <div class="rp-head">
    <h1><?php echo $e($info['page_heading']); ?></h1>
    <div class="cr-modes" role="group" aria-label="Show by">
      <a href="<?php echo $e($base . $keep(array('by' => 'day'))); ?>" class="<?php echo $f['by'] === 'day' ? 'is-on' : ''; ?>">Daily</a>
      <a href="<?php echo $e($base . $keep(array('by' => 'week'))); ?>" class="<?php echo $f['by'] === 'week' ? 'is-on' : ''; ?>">Weekly</a>
      <a href="<?php echo $e($base . $keep(array('by' => 'month'))); ?>" class="<?php echo $f['by'] === 'month' ? 'is-on' : ''; ?>">Monthly</a>
    </div>
  </div>
  <section class="content" style="padding-top:0;">
    <div class="rp-box">
      <form class="rp-filter" method="get" action="<?php echo $e($base); ?>">
        <input type="hidden" name="by" value="<?php echo $e($f['by']); ?>">
        <div><label for="crD1">From</label><input id="crD1" type="date" class="form-control" name="d1" value="<?php echo $e($f['d1']); ?>"></div>
        <div><label for="crD2">To</label><input id="crD2" type="date" class="form-control" name="d2" value="<?php echo $e($f['d2']); ?>"></div>
        <?php if (count($people) > 1) { ?>
        <div><label for="crWho">Person</label>
          <select id="crWho" class="form-control" name="person">
            <option value="">Everyone</option>
            <?php foreach ($people as $p) { ?><option value="<?php echo (int)$p['id']; ?>" <?php echo (int)$f['person'] === (int)$p['id'] ? 'selected' : ''; ?>><?php echo $e($p['name']); ?></option><?php } ?>
          </select>
        </div>
        <?php } ?>
        <div><label class="cr-check"><input type="checkbox" name="internal" value="1" <?php echo $f['internal'] ? 'checked' : ''; ?>> Include calls to colleagues</label></div>
        <div class="rp-btns"><button type="submit" class="btn btn-primary">Show</button><a class="btn btn-default" href="<?php echo $e($base . '?by=' . $f['by']); ?>">Reset</a></div>
      </form>
    </div>

    <div class="rp-kpis">
      <div class="rp-kpi"><span>Calls</span><b><?php echo $num($tot['calls']); ?></b></div>
      <div class="rp-kpi"><span>Connected</span><b><?php echo $num($tot['connected']); ?></b></div>
      <div class="rp-kpi"><span>Not connected</span><b><?php echo $num($tot['calls'] - $tot['connected']); ?></b></div>
      <div class="rp-kpi"><span>Connect rate</span><b><?php echo $rate($tot['connected'], $tot['calls']); ?></b></div>
      <div class="rp-kpi"><span>Talk time</span><b><?php echo $e($talk($tot['talk'])); ?></b></div>
      <div class="rp-kpi"><span>Avg per <?php echo $unit; ?></span><b><?php echo $active ? $one($tot['calls'] / max(1, count($periods))) : '0'; ?></b></div>
      <div class="rp-kpi"><span>Period</span><b class="rp-sm"><?php echo $e($day($f['d1']) . ' – ' . $day($f['d2'])); ?></b></div>
    </div>

    <div class="rp-box">
      <div class="cr-h2">
        <h2><?php echo $f['by'] === 'day' ? 'Day by day' : ($f['by'] === 'week' ? 'Week by week' : 'Month by month'); ?></h2>
        <?php if ($tot['calls']) { ?><a class="btn btn-default btn-sm" href="<?php echo $e($csv('')); ?>"><i class="fa fa-download"></i> Export CSV</a><?php } ?>
      </div>
      <div class="cr-key"><span><i style="background:#22a06b"></i>Connected</span><span><i style="background:#f2a65a"></i>Not connected</span><?php if ($f['by'] !== 'day') { ?><span>Click a <?php echo $unit; ?> to see its days</span><?php } else { ?><span>Click a day to see who called</span><?php } ?></div>
      <div class="table-responsive">
        <table class="table table-bordered rp-table" id="crPeriods">
          <thead><tr><th><?php echo ucfirst($unit); ?></th><th class="n">Calls</th><th class="n">Connected</th><th class="n">Not connected</th><th class="n">Connect rate</th><th class="n">Talk time</th><th class="n">Avg call</th><th class="n">Follow-up calls</th><th class="n">People calling</th><th style="min-width:110px"></th></tr></thead>
          <tbody>
          <?php foreach ($periods as $r) { $no = $r['calls'] - $r['connected']; ?>
            <tr class="<?php echo $r['calls'] ? '' : 'cr-zero'; ?>">
              <td class="nw"><a href="<?php echo $e($base . $keep(array('by' => 'day', 'd1' => $r['from'], 'd2' => $r['to']))); ?>"><?php echo $e($label($r)); ?></a></td>
              <td class="n"><b><?php echo $num($r['calls']); ?></b></td>
              <td class="n cr-ok"><?php echo $num($r['connected']); ?></td>
              <td class="n"><?php echo $num($no); ?></td>
              <td class="n"><?php echo $rate($r['connected'], $r['calls']); ?></td>
              <td class="n"><?php echo $e($talk($r['talk'])); ?></td>
              <td class="n"><?php echo $r['connected'] ? $e($talk(round($r['talk'] / $r['connected']))) : '–'; ?></td>
              <td class="n"><?php echo $num($r['followups']); ?></td>
              <td class="n"><?php echo $num($r['people']); ?></td>
              <td><?php if ($r['calls']) { ?><div class="cr-bar" style="width:<?php echo max(6, round($r['calls'] * 100 / max(1, $max))); ?>%" title="<?php echo (int)$r['connected']; ?> connected, <?php echo (int)$no; ?> not connected"><i class="ok" style="width:<?php echo round($r['connected'] * 100 / $r['calls']); ?>%"></i><i class="no" style="width:<?php echo round($no * 100 / $r['calls']); ?>%"></i></div><?php } ?></td>
            </tr>
          <?php } ?>
          </tbody>
          <tfoot><tr><td>Total</td><td class="n"><?php echo $num($tot['calls']); ?></td><td class="n"><?php echo $num($tot['connected']); ?></td><td class="n"><?php echo $num($tot['calls'] - $tot['connected']); ?></td><td class="n"><?php echo $rate($tot['connected'], $tot['calls']); ?></td><td class="n"><?php echo $e($talk($tot['talk'])); ?></td><td class="n"><?php echo $tot['connected'] ? $e($talk(round($tot['talk'] / $tot['connected']))) : '–'; ?></td><td class="n"><?php echo $num($tot['followups']); ?></td><td class="n"><?php echo $num(count($rows)); ?></td><td></td></tr></tfoot>
        </table>
      </div>
    </div>

    <div class="rp-box">
      <div class="cr-h2">
        <h2>By person</h2>
        <?php if ($rows) { ?><a class="btn btn-default btn-sm" href="<?php echo $e($csv('people')); ?>"><i class="fa fa-download"></i> Export CSV</a><?php } ?>
      </div>
      <?php if (!$rows) { ?>
        <div class="alert alert-info rp-empty">No calls for this selection.</div>
      <?php } else { ?>
      <div class="table-responsive">
        <table class="table table-bordered table-striped rp-table" id="crPeople">
          <thead><tr><th>Person</th><th class="n">Calls</th><th class="n">Connected</th><th class="n">Not connected</th><th class="n">Connect rate</th><th class="n">Talk time</th><th class="n">Avg call</th><th class="n">Follow-up calls</th><th class="n">Days with calls</th><th class="n">Calls per day</th><th>Last call</th></tr></thead>
          <tbody>
          <?php foreach ($rows as $r) { ?>
            <tr>
              <td class="nw"><?php if (count($people) > 1) { ?><a href="<?php echo $e($base . $keep(array('d1' => $f['d1'], 'd2' => $f['d2'], 'person' => $r['uid']))); ?>"><?php echo $e($r['name']); ?></a><?php } else { echo $e($r['name']); } ?></td>
              <td class="n"><b><?php echo $num($r['calls']); ?></b></td>
              <td class="n cr-ok"><?php echo $num($r['connected']); ?></td>
              <td class="n"><?php echo $num($r['calls'] - $r['connected']); ?></td>
              <td class="n"><?php echo $rate($r['connected'], $r['calls']); ?></td>
              <td class="n"><?php echo $e($talk($r['talk'])); ?></td>
              <td class="n"><?php echo $r['connected'] ? $e($talk(round($r['talk'] / $r['connected']))) : '–'; ?></td>
              <td class="n"><?php echo $num($r['followups']); ?></td>
              <td class="n"><?php echo $num($r['days']); ?></td>
              <td class="n"><?php echo $r['days'] ? $one($r['calls'] / $r['days']) : '–'; ?></td>
              <td class="nw"><?php echo $e($day($r['last_call'])); ?> <span class="rp-muted"><?php echo $e(substr((string)$r['last_call'], 11, 5)); ?></span></td>
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
<script>crmPager('#crPeriods', 62); <?php if ($rows) { ?>crmPager('#crPeople', 50);<?php } ?></script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
