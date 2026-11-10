<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$base = base_url() . 'manager/system/user-activity';
$one_day = ($from === $to);
$is_today = $one_day && $from === date('Y-m-d');
$k_active = 0; $k_now = 0; $k_work = 0; $k_idle = 0; $k_none = 0;
foreach ($rows as $r) {
  if ($r['stats']['active'] > 0) $k_active++; else $k_none++;
  if ($r['now'] === 'active') $k_now++;
  $k_work += $r['stats']['work']; $k_idle += $r['stats']['idle'];
}
$tm_ = function ($m) use ($tm) { return $m ? date('h:i A', $tm->ts($m)) : ''; };
$range_q = 'from=' . $from . '&to=' . $to . ($dept ? '&dept=' . $dept : '') . ($q !== '' ? '&q=' . rawurlencode($q) : '');
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<style>
  .ua-now { display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:600; white-space:nowrap; }
  .ua-now::before { content:""; width:8px; height:8px; border-radius:50%; background:#cbd5e1; }
  .ua-now.n-active { color:#15803d; } .ua-now.n-active::before { background:#22c55e; }
  .ua-now.n-idle { color:#b45309; } .ua-now.n-idle::before { background:#f59e0b; }
  .ua-now.n-off { color:#64748b; }
  .ua-bar { height:6px; border-radius:6px; background:#e2e8f0; overflow:hidden; display:flex; min-width:90px; margin-top:4px; }
  .ua-bar i { display:block; height:100%; background:#22c55e; } .ua-bar i.idle { background:#f59e0b; }
  .ua-flag { font-size:11px; font-weight:700; color:#b45309; background:#fef3c7; border-radius:6px; padding:1px 6px; margin-left:4px; white-space:nowrap; }
  .ua-flag.none { color:#991b1b; background:#fee2e2; }
  .ua-num { font-variant-numeric:tabular-nums; }
  .ua-note { font-size:12.5px; color:#64748b; margin:0 0 12px; }
  [data-theme="dark"] .ua-bar { background:rgba(255,255,255,.1); }
  [data-theme="dark"] .ua-flag { background:rgba(245,158,11,.15); color:#fcd34d; }
  [data-theme="dark"] .ua-flag.none { background:rgba(239,68,68,.15); color:#fca5a5; }
  [data-theme="dark"] .ua-now.n-active { color:#86efac; } [data-theme="dark"] .ua-now.n-idle { color:#fcd34d; }
</style>
<div class="content-wrapper sx">
  <?php echo crm_tabbar('activity', 'system/user-activity'); ?>
  <section class="sx-head">
    <h1>User Activity<small>When each person was working or idle, with their calls and record changes. Idle = no clicks, typing or calls for <?php echo Tracking_model::IDLE_MIN; ?> minutes or more.</small></h1>
  </section>

  <section class="content">
    <div class="sx-kpis">
      <div class="sx-kpi g"><span class="ic"><i class="fa fa-user"></i></span><div><b><?php echo number_format($k_active); ?></b><span>Worked in this period</span></div></div>
      <?php if ($is_today) { ?><div class="sx-kpi s"><span class="ic"><i class="fa fa-circle"></i></span><div><b><?php echo number_format($k_now); ?></b><span>Active right now</span></div></div><?php } ?>
      <div class="sx-kpi"><span class="ic"><i class="fa fa-clock-o"></i></span><div><b><?php echo $tm->dur($k_work * 60); ?></b><span>Working time (all)</span></div></div>
      <div class="sx-kpi a"><span class="ic"><i class="fa fa-pause"></i></span><div><b><?php echo $tm->dur($k_idle * 60); ?></b><span>Idle time (all)</span></div></div>
      <div class="sx-kpi r"><span class="ic"><i class="fa fa-user-times"></i></span><div><b><?php echo number_format($k_none); ?></b><span>No activity</span></div></div>
    </div>

    <div class="sx-card">
      <form class="sx-bar" method="get" action="<?php echo $base; ?>">
        <input type="text" class="form-control sx-q" name="q" value="<?php echo $h($q); ?>" placeholder="Search staff name">
        <select name="dept" class="form-control" aria-label="Department">
          <option value="">All departments</option>
          <?php foreach ($departments as $d) { ?><option value="<?php echo (int)$d['id']; ?>" <?php echo $dept === (int)$d['id'] ? 'selected' : ''; ?>><?php echo $h($d['title']); ?></option><?php } ?>
        </select>
        <input type="date" class="form-control" name="from" value="<?php echo $h($from); ?>" aria-label="From">
        <input type="date" class="form-control" name="to" value="<?php echo $h($to); ?>" aria-label="To">
        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Show</button>
        <a href="<?php echo $base; ?>" class="btn btn-default">Today</a>
        <a href="<?php echo $base . '?from=' . date('Y-m-d', strtotime('-1 day')) . ($dept ? '&dept=' . $dept : ''); ?>" class="btn btn-default">Yesterday</a>
        <a href="<?php echo $base . '?from=' . date('Y-m-d', strtotime('-6 days')) . '&to=' . date('Y-m-d') . ($dept ? '&dept=' . $dept : ''); ?>" class="btn btn-default">Last 7 days</a>
      </form>

      <?php if (!$rows) { ?>
        <div class="sx-empty"><i class="fa fa-users"></i>No staff found.</div>
      <?php } else { ?>
      <div class="sx-table-wrap">
        <table class="sx-table sx-cards" id="uaTable" style="width:100%;">
          <thead>
            <tr>
              <th>Staff member</th>
              <?php if ($is_today) { ?><th>Now</th><?php } ?>
              <th><?php echo $one_day ? 'First / last activity' : 'Days worked'; ?></th>
              <th>Working time</th>
              <th>Idle time</th>
              <th>Longest idle</th>
              <th>Calls</th>
              <th>Records changed</th>
              <th>Sign-ins</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($rows as $r) {
            $s = $r['stats'];
            $span = $s['work'] + $s['idle'];
            $link = $base . '/' . $r['id'] . '?date=' . ($one_day ? $from : $to);
          ?>
            <tr>
              <td data-l="Staff" class="full" data-order="<?php echo $h($r['name']); ?>">
                <div class="who">
                  <span class="av"><?php echo $h(strtoupper(substr(trim($r['name']) !== '' ? trim($r['name']) : '?', 0, 1))); ?></span>
                  <div>
                    <a class="nm" href="<?php echo $link; ?>"><?php echo $h($r['name']); ?></a>
                    <div class="sub"><?php echo $h($r['dept'] ? $r['dept'] : 'No department'); ?></div>
                  </div>
                </div>
              </td>
              <?php if ($is_today) {
                $n = $r['now']; $cls = 'n-off'; $txt = 'Offline';
                if ($n === 'active') { $cls = 'n-active'; $txt = 'Active'; }
                elseif (strpos($n, 'idle:') === 0) { $cls = 'n-idle'; $txt = 'Idle ' . $tm->dur((int)substr($n, 5) * 60); }
                elseif ($n === 'online') { $cls = 'n-idle'; $txt = 'Signed in, no activity'; }
              ?>
              <td data-l="Now" data-order="<?php echo $n === 'active' ? 3 : ($n === 'off' || $n === '' ? 0 : 2); ?>"><span class="ua-now <?php echo $cls; ?>"><?php echo $h($txt); ?></span></td>
              <?php } ?>
              <td data-l="<?php echo $one_day ? 'First / last' : 'Days'; ?>" class="nw" data-order="<?php echo $one_day ? (int)$s['first'] : (int)$s['days']; ?>">
                <?php if (!$s['active']) { ?><span class="ua-flag none">No activity</span>
                <?php } elseif ($one_day) { echo $tm_($s['first']) . ' &ndash; ' . $tm_($s['last']); } else { echo (int)$s['days'] . ' day' . ($s['days'] == 1 ? '' : 's'); } ?>
              </td>
              <td data-l="Working" data-order="<?php echo (int)$s['work']; ?>">
                <span class="ua-num"><?php echo $tm->dur($s['work'] * 60); ?></span>
                <?php if ($span > 0) { ?><div class="ua-bar" title="Working <?php echo round($s['work'] * 100 / $span); ?>%, idle <?php echo round($s['idle'] * 100 / $span); ?>%"><i style="width:<?php echo round($s['work'] * 100 / $span, 1); ?>%"></i><i class="idle" style="width:<?php echo round($s['idle'] * 100 / $span, 1); ?>%"></i></div><?php } ?>
              </td>
              <td data-l="Idle" data-order="<?php echo (int)$s['idle']; ?>"><span class="ua-num"><?php echo $tm->dur($s['idle'] * 60); ?></span><?php if ($span > 0 && $s['idle'] * 100 / $span >= 40) { ?><span class="ua-flag">High</span><?php } ?></td>
              <td data-l="Longest idle" data-order="<?php echo (int)$s['longest']; ?>"><span class="ua-num"><?php echo $s['longest'] ? $tm->dur($s['longest'] * 60) : '-'; ?></span></td>
              <td data-l="Calls" data-order="<?php echo (int)$r['calls']; ?>"><span class="ua-num"><?php echo (int)$r['calls']; ?></span><?php if ($r['calls']) { ?><div class="sub"><?php echo (int)$r['connected']; ?> connected &middot; <?php echo $tm->dur($r['talk']); ?></div><?php } ?></td>
              <td data-l="Records" data-order="<?php echo (int)$r['records']; ?>"><span class="ua-num"><?php echo (int)$r['records']; ?></span><?php if ($r['changes']) { ?><div class="sub"><?php echo (int)$r['changes']; ?> field changes</div><?php } ?></td>
              <td data-l="Sign-ins" data-order="<?php echo (int)$r['logins']; ?>"><span class="ua-num"><?php echo (int)$r['logins']; ?></span><?php if ($one_day && $r['first_login']) { ?><div class="sub">first <?php echo date('h:i A', strtotime($r['first_login'])); ?></div><?php } ?></td>
              <td class="acts"><a href="<?php echo $link; ?>" title="Day details"><i class="fa fa-chevron-right"></i></a></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
      <?php } ?>
    </div>
    <p class="ua-note"><i class="fa fa-info-circle"></i> Activity is counted from the minute the CRM was in use (clicks, typing, scrolling, or a call in the dialler). Working time runs from first to last activity, less the idle periods.</p>
  </section>
</div>
<script>
$(function () {
  if ($.fn.DataTable && $('#uaTable').length) {
    $('#uaTable').DataTable({ paging: false, info: false, searching: false, order: [[<?php echo $is_today ? 3 : 2; ?>, 'desc']], columnDefs: [{ orderable: false, targets: -1 }] });
  }
});
</script>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
