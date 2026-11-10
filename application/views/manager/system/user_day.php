<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$base = base_url() . 'manager/system/';
$u = $d['user'];
$s = $d['stats'];
$span = $s['work'] + $s['idle'];
// Hours shown: 8 AM to 8 PM, widened to cover any activity
$h0 = 8; $h1 = 20;
foreach ($d['hours'] as $hr => $n) { if ($n) { $h0 = min($h0, $hr); $h1 = max($h1, $hr + 1); } }
$prev = date('Y-m-d', strtotime($day . ' -1 day'));
$next = date('Y-m-d', strtotime($day . ' +1 day'));
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<style>
  .ud-nav { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
  .ud-nav input { width:auto; }
  .ud-hours { display:grid; gap:4px; padding:14px 16px 6px; }
  .ud-hour { display:flex; flex-direction:column; align-items:center; gap:4px; font-size:11px; color:#64748b; }
  .ud-hour .b { width:100%; height:56px; border-radius:6px; background:#f1f5f9; position:relative; overflow:hidden; }
  .ud-hour .b i { position:absolute; left:0; right:0; bottom:0; background:linear-gradient(180deg, #4ade80, #16a34a); border-radius:6px 6px 0 0; }
  .ud-legend { padding:0 16px 12px; font-size:12px; color:#64748b; }
  .ud-ev { display:grid; grid-template-columns:72px 34px minmax(0, 1fr); gap:10px; padding:8px 16px; align-items:start; border-top:1px solid #f1f5f9; }
  .ud-time { font-size:12px; color:#64748b; padding-top:7px; text-align:right; }
  .ud-ic { width:30px; height:30px; border-radius:50%; display:grid; place-items:center; background:#eef2ff; color:#3730a3; font-size:13px; }
  .ud-ev.is-idle { background:#fffbeb; }
  .ud-ev.is-idle .ud-ic { background:#fef3c7; color:#b45309; }
  .ud-t { font-size:13.5px; font-weight:600; color:#0f172a; }
  .ud-t a { color:inherit; }
  .ud-x { font-size:12.5px; color:#475569; margin-top:2px; word-wrap:break-word; }
  .ud-pages { padding:6px 16px 14px; font-size:12.5px; color:#475569; display:flex; flex-wrap:wrap; gap:6px; }
  .ud-pages span { background:#f1f5f9; border-radius:999px; padding:3px 10px; }
  [data-theme="dark"] .ud-hour .b { background:rgba(255,255,255,.07); }
  [data-theme="dark"] .ud-ev { border-color:rgba(255,255,255,.06); }
  [data-theme="dark"] .ud-ev.is-idle { background:rgba(245,158,11,.08); }
  [data-theme="dark"] .ud-ic { background:rgba(99,102,241,.18); color:#c7d2fe; }
  [data-theme="dark"] .ud-ev.is-idle .ud-ic { background:rgba(245,158,11,.18); color:#fcd34d; }
  [data-theme="dark"] .ud-t { color:#e2e8f0; } [data-theme="dark"] .ud-x { color:#cbd5e1; }
  [data-theme="dark"] .ud-pages span { background:rgba(255,255,255,.07); color:#cbd5e1; }
</style>
<div class="content-wrapper sx">
  <?php echo crm_tabbar('activity', 'system/user-activity'); ?>
  <section class="sx-head">
    <h1><?php echo $h($u['name']); ?><small><?php echo $h($u['dept'] ? $u['dept'] : 'No department'); ?> &middot; <?php echo date('l, d M Y', strtotime($day)); ?></small></h1>
    <div class="sx-btns ud-nav">
      <a class="btn btn-default" href="<?php echo $base; ?>user-activity?from=<?php echo $day; ?>"><i class="fa fa-arrow-left"></i> Everyone</a>
      <a class="btn btn-default" href="?date=<?php echo $prev; ?>" title="Previous day"><i class="fa fa-chevron-left"></i></a>
      <form method="get" style="margin:0;"><input type="date" class="form-control" name="date" value="<?php echo $h($day); ?>" onchange="this.form.submit()" aria-label="Day"></form>
      <?php if ($next <= date('Y-m-d')) { ?><a class="btn btn-default" href="?date=<?php echo $next; ?>" title="Next day"><i class="fa fa-chevron-right"></i></a><?php } ?>
      <a class="btn btn-default" href="<?php echo $base; ?>record-tracking?user=<?php echo (int)$u['id']; ?>&from=<?php echo $day; ?>&to=<?php echo $day; ?>"><i class="fa fa-search"></i> Their record changes</a>
    </div>
  </section>

  <section class="content">
    <div class="sx-kpis">
      <div class="sx-kpi"><span class="ic"><i class="fa fa-sign-in"></i></span><div><b><?php echo $s['first'] ? date('h:i A', $tm->ts($s['first'])) : '-'; ?></b><span>First activity</span></div></div>
      <div class="sx-kpi"><span class="ic"><i class="fa fa-sign-out"></i></span><div><b><?php echo $s['last'] ? date('h:i A', $tm->ts($s['last'])) : '-'; ?></b><span>Last activity</span></div></div>
      <div class="sx-kpi g"><span class="ic"><i class="fa fa-clock-o"></i></span><div><b><?php echo $tm->dur($s['work'] * 60); ?></b><span>Working time</span></div></div>
      <div class="sx-kpi a"><span class="ic"><i class="fa fa-pause"></i></span><div><b><?php echo $tm->dur($s['idle'] * 60); ?></b><span>Idle time (<?php echo count($s['gaps']); ?> times)</span></div></div>
      <div class="sx-kpi r"><span class="ic"><i class="fa fa-hourglass-half"></i></span><div><b><?php echo $s['longest'] ? $tm->dur($s['longest'] * 60) : '-'; ?></b><span>Longest idle</span></div></div>
    </div>

    <div class="sx-card">
      <div class="sx-card-h"><h3>Active minutes per hour</h3></div>
      <?php if (!$s['active']) { ?>
        <div class="sx-empty"><i class="fa fa-moon-o"></i>No activity on this day.</div>
      <?php } else { ?>
      <div class="ud-hours" style="grid-template-columns:repeat(<?php echo $h1 - $h0; ?>, minmax(0, 1fr));">
        <?php for ($i = $h0; $i < $h1; $i++) { $n = $d['hours'][$i]; ?>
          <div class="ud-hour" title="<?php echo date('g A', mktime($i, 0, 0)); ?>: active <?php echo (int)$n; ?> of 60 minutes">
            <div class="b"><i style="height:<?php echo round($n * 100 / 60); ?>%"></i></div>
            <span><?php echo date('ga', mktime($i, 0, 0)); ?></span>
          </div>
        <?php } ?>
      </div>
      <div class="ud-legend">Bar height = minutes in that hour with activity. Working <?php echo $span ? round($s['work'] * 100 / $span) : 0; ?>% and idle <?php echo $span ? round($s['idle'] * 100 / $span) : 0; ?>% of the time between first and last activity.</div>
      <?php if ($d['pages']) { ?>
        <div class="ud-pages"><b style="margin-right:4px;">Most used:</b><?php foreach ($d['pages'] as $p) { ?><span><?php echo $h($p['page'] !== '' ? $p['page'] : 'dashboard'); ?> &middot; <?php echo $tm->dur($p['n'] * 60); ?></span><?php } ?></div>
      <?php } ?>
      <?php } ?>
    </div>

    <div class="sx-card">
      <div class="sx-card-h"><h3>What they did <small><?php echo count($d['events']); ?> entries</small></h3></div>
      <?php if (!$d['events']) { ?>
        <div class="sx-empty"><i class="fa fa-list"></i>Nothing recorded on this day.</div>
      <?php } else { foreach ($d['events'] as $e) {
        $link = $e['link'] !== '' ? $base . 'record-tracking/' . $e['link'] : '';
      ?>
        <div class="ud-ev<?php echo !empty($e['idle']) ? ' is-idle' : ''; ?>">
          <div class="ud-time"><?php echo date('h:i A', strtotime($e['at'])); ?></div>
          <div class="ud-ic"><i class="fa <?php echo $h($e['icon']); ?>"></i></div>
          <div>
            <div class="ud-t"><?php echo $link ? '<a href="' . $link . '">' . $h($e['title']) . '</a>' : $h($e['title']); ?></div>
            <?php if ($e['text'] !== '') { ?><div class="ud-x"><?php echo $h(mb_strimwidth($e['text'], 0, 300, '...')); ?></div><?php } ?>
          </div>
        </div>
      <?php } } ?>
    </div>
  </section>
</div>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
