<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
/* Focus dashboard: the same figures and rules as the Modern dashboard (dash/index.php), laid out differently:
   the month and a "Needs attention" list on top, then the sections; staff shown as cards with a side panel. */
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$b = base_url();
$m = base_url() . 'manager/';
$num = function ($n) { return number_format((float)$n, 0); };
$money_t = function ($n) { $n = (float)$n; return 'AED ' . ($n >= 1000000 ? number_format($n / 1000000, 2) . 'M' : number_format($n, 0)); };
$money = function ($n) use ($money_t) { return '<span class="db-amt">' . $money_t($n) . '</span>'; };
$pct = function ($a, $t) { return $t > 0 ? (int)round($a * 100 / $t) : 0; };
$ago = function ($d) {
  if (empty($d) || $d < '2000') return 'Never';
  $s = time() - strtotime($d);
  if ($s < 300) return 'Online';
  if ($s < 3600) return floor($s / 60) . ' min ago';
  if ($s < 86400) return floor($s / 3600) . ' h ago';
  $dd = floor($s / 86400);
  return $dd == 1 ? 'Yesterday' : $dd . ' days ago';
};
$days_late = function ($d) { if (empty($d) || $d < '2000') return null; return (int)floor((strtotime(date('Y-m-d')) - strtotime(substr($d, 0, 10))) / 86400); };
$age = function ($d) { if ($d === null) return ''; if ($d < 60) return $d . ' days'; if ($d < 730) return floor($d / 30) . ' months'; return floor($d / 365) . ' years'; };
$ini = function ($n) { $p = preg_split('/\s+/', trim((string)$n)); return strtoupper(substr($p[0], 0, 1) . (isset($p[1]) ? substr($p[1], 0, 1) : '')); };
$fmt_d = function ($d) { if (empty($d) || $d < '2000') return '-'; $x = strtotime($d); return date(date('Y', $x) === date('Y') ? 'd M' : 'd M Y', $x); };

$names = array('admin' => 'Business overview', 'sales' => 'Sales', 'backoffice' => 'Back Office', 'kam' => 'Key accounts');
$q = function ($over = array()) use ($dkey, $win, $only_me, $is_admin) {
  $p = array();
  $a = array_key_exists('as', $over) ? $over['as'] : ($is_admin && $dkey !== 'admin' ? $dkey : '');
  if ($a !== '') $p['as'] = $a;
  $mm = array_key_exists('m', $over) ? $over['m'] : ($win['ym'] !== date('Y-m') ? $win['ym'] : '');
  if ($mm !== '') $p['m'] = $mm;
  $me = array_key_exists('me', $over) ? $over['me'] : ($only_me ? '1' : '');
  if ($me !== '') $p['me'] = $me;
  return base_url() . 'manager/dashboard' . ($p ? '?' . http_build_query($p) : '');
};
$prev_m = date('Y-m', strtotime($win['start'] . ' -1 month'));
$next_m = date('Y-m', strtotime($win['start'] . ' +1 month'));
$is_now = $win['ym'] === date('Y-m');
$mon = date('F', strtotime($win['start']));
$t = $targets;
$late_tasks = count(array_filter($my_tasks, function ($x) { return $x['_late']; }));
$fu_url = $m . 'leads/followups?type=';
$scope_lbl = $dkey === 'kam' && !$is_admin ? 'my numbers' : ($has_team && !$only_me ? ($is_admin ? 'whole company' : 'me and my team') : 'my numbers');

$tr_over = 0; $tr_idle = 0; $tr_today = 0; $tr_done = 0; $tr_calls = 0; $tr_late = 0;
foreach ($tracker as $r) {
  $tr_over += $r['fu_overdue']; $tr_today += $r['fu_today']; $tr_done += $r['fu_done']; $tr_calls += $r['calls']; $tr_late += $r['tasks_late'];
  if ($is_now && $r['calls'] == 0 && $r['fu_done'] == 0) $tr_idle++;
}
$fl_late = 0; $fl_open = 0;
if (!empty($flow)) foreach ($flow as $f) { $fl_late += (int)$f['late']; $fl_open += (int)$f['n']; }
$cl_cold = 0;
if (!empty($kam_clients)) foreach ($kam_clients as $c) { if ($c['days_since'] === null || $c['days_since'] > 30) $cl_cold++; }

$tg = $t['target']; $ac = $t['achieved']; $pr = $t['projection'];
$gap = max(0, $tg - $ac);
$ap = $pct($ac, $tg); $pp = $pct($ac + $pr, $tg); $pace = $pct($t['pace'], $tg);
$behind = $tg > 0 && $is_now && $ac < $t['pace'];

/* Needs attention: [severity r|a, label, count, href, tab, filter]. Only what this role sees on the Modern dashboard. */
$att = array();
if ($my_fu['overdue']) $att[] = array('r', 'My overdue follow-ups', $my_fu['overdue'], $fu_url . '1', '', '');
if ($behind && $dkey !== 'backoffice') $att[] = array('r', 'Behind the month target', null, '', 'month', '');
if ($tr_over) $att[] = array('r', ($is_admin && !$only_me ? 'Overdue follow-ups, all staff' : 'Overdue follow-ups in my team'), $tr_over, '', 'team', 'late');
if ($fl_late) $att[] = array('r', 'Orders not moved for ' . (int)$late_days . '+ days', $fl_late, '', 'orders', '');
if ($cl_cold) $att[] = array('r', 'Clients with no contact in 30 days', $cl_cold, '', 'clients', '');
if ($my_fu['today']) $att[] = array('a', 'My follow-ups due today', $my_fu['today'], $fu_url . '3', '', '');
if ($late_tasks) $att[] = array('a', 'My late tasks', $late_tasks, '', 'mine', '');
if ($tr_idle) $att[] = array('a', 'People with no activity today', $tr_idle, '', 'team', 'idle');
if (!empty($reviews)) {
  // Same review counters each role sees on the Modern dashboard.
  $rv_show = $dkey === 'kam' ? array(2) : array_keys($reviews);
  foreach ($rv_show as $i) if (!empty($reviews[$i]['n'])) $att[] = array('a', $reviews[$i]['label'], $reviews[$i]['n'], $b . $reviews[$i]['url'], '', '');
}

/* Sections under the overview. The first one opens by default. */
$tabs = array();
if ($dkey === 'sales') $tabs['mine'] = array('My work', $my_fu['overdue'] + $my_fu['today']);
if ($dkey === 'backoffice') $tabs['orders'] = array('Late orders', $fl_late);
if ($dkey === 'kam') $tabs['clients'] = array('Clients', $cl_cold);
if ($dkey === 'admin') $tabs['orders'] = array('Orders', $fl_late);
if ($tracker) $tabs['team'] = array($is_admin && !$only_me ? 'Staff' : 'My team', $tr_over);
if ($dkey !== 'sales') $tabs['mine'] = array('My work', $my_fu['overdue'] + $my_fu['today'] + $late_tasks);
$first_tab = key($tabs);

$kpi = function ($cls, $href, $icon, $val, $label, $tab = '') {
  return '<a class="sx-kpi ' . $cls . '" href="' . $href . '"' . ($tab ? ' data-go="' . $tab . '"' : '') . '><span class="ic"><i class="fa ' . $icon . '"></i></span><div><b>' . $val . '</b><span>' . $label . '</span></div></a>';
};
// Ring for the month: achieved, then projection on top of it.
$C = 2 * M_PI * 46;
$ring_a = $C * min(100, $ap) / 100;
$ring_p = $C * max(0, min(100, $pp) - min(100, $ap)) / 100;
?>
<link rel="stylesheet" href="<?php echo $b; ?>assets/dist/css/crm-sys.css?v=20261106_7">
<link rel="stylesheet" href="<?php echo $b; ?>assets/dist/css/crm-dash.css?v=20261107_6">
<link rel="stylesheet" href="<?php echo $b; ?>assets/dist/css/crm-dash-focus.css?v=20261108_3">
<div class="content-wrapper sx db fx" id="dbRoot" data-key="<?php echo $h($dkey); ?>">
  <section class="sx-head">
    <h1><?php echo $h($names[$dkey]); ?><small><?php echo $h(date('F Y', strtotime($win['start']))); ?><?php echo $is_now ? ' &middot; day ' . (int)$win['passed'] . ' of ' . (int)$win['days'] : ''; ?><?php echo $has_team ? ' &middot; ' . $h($scope_lbl) : ''; ?></small></h1>
    <div class="sx-btns">
      <?php if ($is_admin) { ?>
      <div class="sx-chips db-as" title="Open another role's dashboard">
        <?php foreach (array('' => 'Admin', 'sales' => 'Sales', 'backoffice' => 'Back Office', 'kam' => 'KAM') as $k => $lbl) { ?>
        <a href="<?php echo $h($q(array('as' => $k))); ?>" class="<?php echo ($k === '' ? 'admin' : $k) === $dkey ? 'is-on' : ''; ?>"><?php echo $lbl; ?></a>
        <?php } ?>
      </div>
      <?php } ?>
      <?php if ($has_team) { ?>
      <div class="sx-chips">
        <a href="<?php echo $h($q(array('me' => ''))); ?>" class="<?php echo !$only_me ? 'is-on' : ''; ?>"><?php echo $is_admin ? 'Company' : 'Team'; ?></a>
        <a href="<?php echo $h($q(array('me' => '1'))); ?>" class="<?php echo $only_me ? 'is-on' : ''; ?>">Me</a>
      </div>
      <?php } ?>
      <div class="db-month">
        <a class="btn btn-default" href="<?php echo $h($q(array('m' => $prev_m))); ?>" title="Previous month" aria-label="Previous month"><i class="fa fa-angle-left"></i></a>
        <span><?php echo $h(date('M Y', strtotime($win['start']))); ?></span>
        <a class="btn btn-default<?php echo $is_now ? ' disabled' : ''; ?>" href="<?php echo $h($q(array('m' => $next_m === date('Y-m') ? '' : $next_m))); ?>" title="Next month" aria-label="Next month"><i class="fa fa-angle-right"></i></a>
      </div>
      <button type="button" class="btn btn-default db-hide-btn" id="dbHideAmt" aria-pressed="false" title="Hide amounts on screen (for screen sharing or a shared screen)"><i class="fa fa-eye-slash"></i><span>Hide amounts</span></button>
    </div>
  </section>

  <section class="content">

    <div class="sx-kpis">
<?php
if ($dkey === 'backoffice') {
  $rv = $reviews;
  echo $kpi(($rv[0]['n'] + $rv[1]['n']) ? 'a' : 'g', $m . 'leads/review_leads', 'fa-inbox', $num($rv[0]['n'] + $rv[1]['n']), 'Waiting for your review');
  echo $kpi('', '#month', 'fa-cogs', $num($fl_open), 'Orders in process', 'month');
  echo $kpi($fl_late ? 'r' : 'g', '#orders', 'fa-hourglass-half', $num($fl_late), 'Not moved for ' . (int)$late_days . '+ days', 'orders');
  echo $kpi('g', $m . 'orders', 'fa-check', $num($orders_done['sales_month']), 'Became sales this month');
} elseif ($dkey === 'admin') {
  $wait = 0; foreach ($reviews as $r) $wait += (int)$r['n'];
  echo $kpi('g', '#month', 'fa-line-chart', $money($ac), 'Sales in ' . $h($mon) . ($tg > 0 ? ' &middot; ' . $ap . '% of target' : ''), 'month');
  echo $kpi($tr_over ? 'r' : 'g', '#team', 'fa-exclamation-circle', $num($tr_over), 'Overdue follow-ups, all staff', $tracker ? 'team' : '');
  echo $kpi($fl_late ? 'a' : '', '#orders', 'fa-cogs', $num($fl_open), 'Orders in process' . ($fl_late ? ', ' . $fl_late . ' stuck' : ''), 'orders');
  echo $kpi($wait ? 'a' : '', $m . 'leads/review_leads', 'fa-inbox', $num($wait), 'Waiting for a decision');
} elseif ($dkey === 'kam') {
  echo $kpi('', '#clients', 'fa-briefcase', $num(count($kam_clients)), 'My clients', 'clients');
  echo $kpi($cl_cold ? 'r' : 'g', '#clients', 'fa-phone-square', $num($cl_cold), 'No contact in 30 days', 'clients');
  echo $kpi($late_tasks ? 'a' : 'g', '#mine', 'fa-check-square-o', $num(count($my_tasks)), 'Open tasks' . ($late_tasks ? ', ' . $late_tasks . ' late' : ''), 'mine');
  echo $kpi($reviews[2]['n'] ? 'a' : '', $m . 'orders/pending_sales_approval', 'fa-gavel', $num($reviews[2]['n']), 'Waiting for sales approval');
} else {
  echo $kpi($my_fu['overdue'] ? 'r' : 'g', $fu_url . '1', 'fa-exclamation-circle', $num($my_fu['overdue']), 'Overdue follow-ups' . ($my_fu['oldest'] ? ' &middot; since ' . $h($fmt_d($my_fu['oldest'])) : ''));
  echo $kpi($my_fu['today'] ? 'a' : '', $fu_url . '3', 'fa-calendar-check-o', $num($my_fu['today']), 'Due today');
  echo $kpi('', $fu_url . '4', 'fa-calendar', $num($my_fu['next7']), 'Next 7 days');
  echo $kpi($tg > 0 && $is_now && $ac < $t['pace'] ? 'r' : 'g', '#month', 'fa-bullseye', $money($ac), $tg > 0 ? $ap . '% of ' . $money($tg) . ' target' : 'Sales this month', 'month');
}
?>
    </div>

    <div class="fx-top">
<?php if ($dkey !== 'backoffice') { ?>
      <!-- The month: target, achieved, projection -->
      <div class="sx-card fx-month" id="fxMonth">
        <div class="sx-card-h"><h3><?php echo $h($mon); ?> target <small><?php echo $h($scope_lbl); ?></small></h3>
          <div class="sx-tools">
            <?php if ($behind) { ?><span class="chip x">Behind by <?php echo $money($t['pace'] - $ac); ?></span><?php } elseif ($tg > 0 && $is_now) { ?><span class="chip ok">On track</span><?php } ?>
            <?php if ($is_admin) { ?><a class="btn btn-default btn-sm" href="<?php echo $m; ?>hr/targets">Set targets</a><?php } ?>
          </div></div>
        <div class="sx-card-b">
          <div class="fx-month-b">
            <div class="fx-ring" role="img" aria-label="<?php echo $tg > 0 ? $ap . '% of target achieved, ' . $pp . '% with projection' : 'No target set'; ?>">
              <svg viewBox="0 0 110 110" width="128" height="128" aria-hidden="true">
                <circle class="bg" cx="55" cy="55" r="46"/>
                <?php if ($tg > 0) { ?>
                <circle class="p" cx="55" cy="55" r="46" stroke-dasharray="<?php echo round($ring_p, 2) . ' ' . round($C, 2); ?>" stroke-dashoffset="<?php echo round(-$ring_a, 2); ?>"/>
                <circle class="a" cx="55" cy="55" r="46" stroke-dasharray="<?php echo round($ring_a, 2) . ' ' . round($C, 2); ?>"/>
                <?php } ?>
              </svg>
              <div class="fx-ring-c"><b><?php echo $tg > 0 ? $ap . '%' : '-'; ?></b><span><?php echo $tg > 0 ? 'of target' : 'No target'; ?></span></div>
            </div>
            <div class="fx-month-r">
              <div class="db-money">
                <div><span>Target</span><b><?php echo $tg > 0 ? $money($tg) : '<em>Not set</em>'; ?></b></div>
                <div><span>Achieved</span><b class="ok"><?php echo $money($ac); ?></b><small><?php echo $num($t['sales_n']); ?> sale<?php echo $t['sales_n'] == 1 ? '' : 's'; ?></small></div>
                <div><span>Projection</span><b><?php echo $money($pr); ?></b><small><?php echo $num($t['projection_n']); ?> open lead<?php echo $t['projection_n'] == 1 ? '' : 's'; ?> closing this month</small></div>
                <div><span>Still needed</span><b class="<?php echo $gap > 0 ? 'bad' : 'ok'; ?>"><?php echo $tg > 0 ? $money($gap) : '-'; ?></b><?php if ($tg > 0) { ?><small><?php echo $ac + $pr >= $tg ? 'Projection covers it' : 'Projection short by ' . $money($tg - $ac - $pr); ?></small><?php } ?></div>
              </div>
              <?php if ($tg > 0) { ?>
              <div class="db-meter" role="img" aria-label="<?php echo $ap; ?>% achieved, <?php echo $pp; ?>% with projection">
                <i class="a" style="width:<?php echo min(100, $ap); ?>%"></i>
                <i class="p" style="left:<?php echo min(100, $ap); ?>%;width:<?php echo max(0, min(100, $pp) - min(100, $ap)); ?>%"></i>
                <?php if ($is_now) { ?><s style="left:<?php echo min(100, $pace); ?>%" title="Where the month should be by today"></s><?php } ?>
              </div>
              <div class="db-legend"><span><i class="a"></i>Achieved</span><span><i class="p"></i>Projection</span><?php if ($is_now) { ?><span><i class="t"></i>Should be by today: <?php echo $money($t['pace']); ?></span><?php } ?></div>
              <?php } else { ?>
              <p class="sx-help"><i class="fa fa-info-circle"></i>No target for <?php echo $h($mon); ?>. Targets are set on HR &rsaquo; Targets.</p>
              <?php } ?>
            </div>
          </div>

          <div class="fx-acts">
            <?php foreach ($t['rows'] as $r) { $p = $pct($r['done'], $r['target']); ?>
            <div class="fx-act">
              <span><?php echo $h($r['label']); ?></span>
              <b><?php echo $num($r['done']); ?><?php if ($r['target']) { ?><small> / <?php echo $num($r['target']); ?></small><?php } ?></b>
              <?php if ($r['target']) { ?><span class="db-bar"><i style="width:<?php echo min(100, $p); ?>%" class="<?php echo $p >= 100 ? 'ok' : ''; ?>"></i></span><em><?php echo $p; ?>%</em><?php } else { ?><em>No target</em><?php } ?>
            </div>
            <?php } ?>
          </div>

          <?php if ($dkey === 'admin' || $dkey === 'sales') { $mx = max(1, max($by_day)); ?>
          <div class="fx-pipe">
            <div class="fx-pipe-l">
              <div class="db-days-h">Pipeline</div>
              <div class="db-funnel fx-funnel">
                <?php foreach ($funnel as $f) { ?>
                <a href="<?php echo $b . $f['url']; ?>"><b><?php echo $num($f['n']); ?></b><span><?php echo $h($f['label']); ?></span></a>
                <?php } ?>
              </div>
            </div>
            <div class="fx-pipe-r">
              <div class="db-days-h">Sales by day</div>
              <div class="db-days" role="img" aria-label="Sales by day this month">
                <?php foreach ($by_day as $d => $v) { ?><i style="height:<?php echo $v > 0 ? max(4, round($v * 100 / $mx)) : 0; ?>%" class="<?php echo $is_now && $d == (int)date('j') ? 'today' : ''; ?>" data-day="<?php echo (int)$d . ' ' . $h(date('M', strtotime($win['start']))); ?>" data-amt="<?php echo $h($money_t($v)); ?>"></i><?php } ?>
              </div>
              <div class="db-days-x"><span>1</span><span><?php echo (int)ceil($win['days'] / 2); ?></span><span><?php echo (int)$win['days']; ?></span></div>
            </div>
          </div>
          <?php } elseif ($dkey === 'kam') { ?>
          <div class="fx-pipe one">
            <div class="fx-pipe-l">
              <div class="db-days-h">Follow-ups</div>
              <div class="db-funnel three">
                <a href="<?php echo $fu_url; ?>1"><b class="<?php echo $my_fu['overdue'] ? 'bad' : ''; ?>"><?php echo $num($my_fu['overdue']); ?></b><span>Overdue</span></a>
                <a href="<?php echo $fu_url; ?>3"><b><?php echo $num($my_fu['today']); ?></b><span>Due today</span></a>
                <a href="<?php echo $fu_url; ?>4"><b><?php echo $num($my_fu['next7']); ?></b><span>Next 7 days</span></a>
              </div>
            </div>
          </div>
          <?php } ?>
        </div>
      </div>
<?php } else { $fmx = 1; foreach ($flow as $f) $fmx = max($fmx, (int)$f['n']); ?>
      <!-- Back Office: orders by stage -->
      <div class="sx-card fx-month" id="fxMonth">
        <div class="sx-card-h"><h3>Orders in process <small>by stage</small></h3><div class="sx-tools"><a class="btn btn-default btn-sm" href="<?php echo $m; ?>orders/open">Open orders</a></div></div>
        <div class="sx-card-b">
          <div class="fx-stages">
            <?php if (!$flow) { ?><div class="sx-empty"><i class="fa fa-check-circle-o"></i>No orders in process.</div><?php } ?>
            <?php foreach ($flow as $f) { ?>
            <div class="fx-stage<?php echo $f['late'] ? ' late' : ''; ?>">
              <span class="l"><?php echo $h($f['status'] !== '' ? $f['status'] : 'No status'); ?></span>
              <b><?php echo $num($f['n']); ?></b>
              <span class="db-bar"><i style="width:<?php echo round($f['n'] * 100 / $fmx); ?>%"></i></span>
              <?php if ($f['late']) { ?><span class="chip x" title="Not moved for <?php echo (int)$late_days; ?>+ days"><?php echo $num($f['late']); ?> late</span><?php } else { ?><span class="chip ok">Moving</span><?php } ?>
            </div>
            <?php } ?>
          </div>
          <div class="fx-acts three">
            <div class="fx-act"><span>New orders today</span><b><?php echo $num($orders_done['new_today']); ?></b></div>
            <div class="fx-act"><span>Became sales today</span><b><?php echo $num($orders_done['sales_today']); ?></b></div>
            <div class="fx-act"><span>Became sales this month</span><b><?php echo $num($orders_done['sales_month']); ?></b></div>
          </div>
        </div>
      </div>
<?php } ?>

      <!-- Needs attention -->
      <div class="sx-card fx-att">
        <div class="sx-card-h"><h3>Needs attention</h3></div>
        <div class="sx-card-b">
          <?php if (!$att) { ?><div class="sx-empty"><i class="fa fa-check-circle-o"></i>Nothing needs attention right now.</div><?php } ?>
          <?php foreach ($att as $a) {
            $href = $a[3] !== '' ? $a[3] : '#' . $a[4]; ?>
          <a class="fx-att-i <?php echo $a[0]; ?>" href="<?php echo $h($href); ?>"<?php echo $a[4] ? ' data-go="' . $a[4] . '"' : ''; ?><?php echo $a[5] ? ' data-f="' . $a[5] . '"' : ''; ?>>
            <i class="dot"></i><span><?php echo $h($a[1]); ?></span>
            <?php if ($a[2] !== null) { ?><b><?php echo $num($a[2]); ?></b><?php } ?>
            <i class="fa fa-angle-right"></i>
          </a>
          <?php } ?>
        </div>
      </div>
    </div>

    <?php if (count($tabs) > 1) { ?>
    <nav class="fx-tabs" role="tablist" aria-label="Dashboard sections">
      <?php foreach ($tabs as $k => $tb) { ?>
      <button type="button" role="tab" id="dbTab-<?php echo $k; ?>" aria-controls="dbPane-<?php echo $k; ?>" data-tab="<?php echo $k; ?>" class="<?php echo $k === $first_tab ? 'is-on' : ''; ?>" aria-selected="<?php echo $k === $first_tab ? 'true' : 'false'; ?>"><?php echo $h($tb[0]); ?><?php if ($tb[1] > 0) { ?><span class="n"><?php echo $num($tb[1]); ?></span><?php } ?></button>
      <?php } ?>
    </nav>
    <?php } ?>

<?php
/* ----------------------------------------------------------- Orders */
if (isset($tabs['orders'])) { ?>
    <div class="db-pane" id="dbPane-orders" role="tabpanel" aria-labelledby="dbTab-orders"<?php echo $first_tab === 'orders' ? '' : ' hidden'; ?>>
    <?php if ($dkey === 'admin') { ?>
    <div class="sx-card">
      <div class="sx-card-h"><h3>Orders in process <small>by stage</small></h3><div class="sx-tools"><a class="btn btn-default btn-sm" href="<?php echo $m; ?>orders/open">Open orders</a></div></div>
      <div class="sx-card-b">
        <div class="fx-board">
          <?php if (!$flow) { ?><div class="sx-empty"><i class="fa fa-check-circle-o"></i>No orders in process.</div><?php } ?>
          <?php foreach ($flow as $f) { ?>
          <div class="fx-col<?php echo $f['late'] ? ' late' : ''; ?>">
            <span class="l"><?php echo $h($f['status'] !== '' ? $f['status'] : 'No status'); ?></span>
            <b><?php echo $num($f['n']); ?></b>
            <?php if ($f['late']) { ?><span class="chip x" title="Not moved for <?php echo (int)$late_days; ?>+ days"><?php echo $num($f['late']); ?> late</span><?php } else { ?><span class="chip ok">Moving</span><?php } ?>
          </div>
          <?php } ?>
        </div>
        <div class="fx-acts">
          <?php foreach ($reviews as $r) { ?>
          <a class="fx-act" href="<?php echo $b . $r['url']; ?>"><span><?php echo $h($r['label']); ?></span><b class="<?php echo $r['n'] ? 'warn' : ''; ?>"><?php echo $num($r['n']); ?></b></a>
          <?php } ?>
          <a class="fx-act" href="<?php echo $m; ?>orders"><span>New orders today</span><b><?php echo $num($orders_done['new_today']); ?></b></a>
          <a class="fx-act" href="<?php echo $m; ?>orders"><span>Became sales today</span><b><?php echo $num($orders_done['sales_today']); ?></b></a>
        </div>
      </div>
    </div>
    <?php } ?>
    <div class="sx-card">
      <div class="sx-card-h"><h3>Orders not moved for <?php echo (int)$late_days; ?>+ days <small>oldest first</small></h3></div>
      <?php if (!$late_orders) { ?><div class="sx-empty"><i class="fa fa-check-circle-o"></i>Every open order moved in the last <?php echo (int)$late_days; ?> days.</div><?php } else { ?>
      <div class="sx-table-wrap db-scroll">
        <table class="sx-table sx-cards">
          <thead><tr><th>Order</th><th>Client</th><th>Stage</th><th>With</th><th>Last moved</th><th class="num">Value</th></tr></thead>
          <tbody>
          <?php foreach ($late_orders as $o) { $dl = $days_late($o['moved_at']); ?>
            <tr>
              <td data-l="Order" class="nw"><a class="mono" href="<?php echo $m; ?>orders/view/<?php echo (int)$o['id']; ?>"><b><?php echo $h($o['order_no'] ? $o['order_no'] : '#' . $o['id']); ?></b></a></td>
              <td data-l="Client"><span class="nm"><?php echo $h($o['company_name'] ? $o['company_name'] : $o['client_name']); ?></span><span class="sub"><?php echo $h($o['service_title']); ?></span></td>
              <td data-l="Stage"><span class="chip s"><?php echo $h($o['status']); ?></span></td>
              <td data-l="With"><?php echo $h($o['assignee'] ? $o['assignee'] : 'Not assigned'); ?><span class="sub">by <?php echo $h($o['creator']); ?></span></td>
              <td data-l="Last moved" class="nw"><?php echo $h($fmt_d($o['moved_at'])); ?> <span class="chip x"><?php echo (int)$dl; ?> days</span></td>
              <td data-l="Value" class="num nw"><?php echo (float)$o['pricing_amount'] > 0 ? $money($o['pricing_amount']) : '-'; ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
      <?php } ?>
    </div>
    <?php if ($dkey === 'backoffice') { ?>
    <div class="sx-card">
      <div class="sx-card-h"><h3>Waiting for a decision</h3></div>
      <div class="sx-card-b fx-acts">
        <?php foreach ($reviews as $r) { ?>
        <a class="fx-act" href="<?php echo $b . $r['url']; ?>"><span><?php echo $h($r['label']); ?></span><b class="<?php echo $r['n'] ? 'warn' : ''; ?>"><?php echo $num($r['n']); ?></b></a>
        <?php } ?>
      </div>
    </div>
    <?php } ?>
    </div>
<?php } ?>

<?php
/* ----------------------------------------------------------- KAM clients */
if (isset($tabs['clients'])) { ?>
    <div class="db-pane" id="dbPane-clients" role="tabpanel" aria-labelledby="dbTab-clients"<?php echo $first_tab === 'clients' ? '' : ' hidden'; ?>>
    <div class="sx-card">
      <div class="sx-card-h"><h3>My clients <small>longest without contact first</small></h3>
        <div class="sx-tools"><input type="search" class="form-control input-sm db-q" data-t="#dbClientTable" placeholder="Search client" aria-label="Search client"><a class="btn btn-default btn-sm" href="<?php echo $m; ?>kam">All clients</a></div></div>
      <?php if (!$kam_clients) { ?><div class="sx-empty"><i class="fa fa-briefcase"></i>No clients are assigned to you yet.</div><?php } else { ?>
      <div class="sx-table-wrap db-scroll">
        <table class="sx-table sx-cards" id="dbClientTable">
          <thead><tr><th>Client</th><th>Last contact</th><th>Next follow-up</th><th>Open tasks</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($kam_clients as $c) { $ds = $c['days_since']; $nf = $c['followup_date']; ?>
            <tr data-q="<?php echo $h(strtolower($c['client'] . ' ' . $c['group_name'] . ' ' . $c['ref'])); ?>">
              <td data-l="Client"><a class="nm" href="<?php echo $m; ?>kam/view/<?php echo (int)$c['lead_id']; ?>"><?php echo $h($c['client']); ?></a><span class="sub"><?php echo $h(trim($c['ref'] . ($c['group_name'] ? ' · ' . $c['group_name'] : ''), ' ·')); ?></span></td>
              <td data-l="Last contact" class="nw"><?php if ($ds === null) { ?><span class="chip x">Never</span><?php } else { echo $h($fmt_d($c['last_contact'])); ?> <span class="chip <?php echo $ds > 30 ? 'x' : ($ds > 14 ? 'w' : 'ok'); ?>"><?php echo $ds == 0 ? 'today' : (int)$ds . ' days'; ?></span><?php } ?></td>
              <td data-l="Next follow-up" class="nw"><?php echo (!empty($nf) && $nf >= '2000') ? $h($fmt_d($nf)) . ($nf < date('Y-m-d') ? ' <span class="chip x">overdue</span>' : '') : '<span class="text-muted">None set</span>'; ?></td>
              <td data-l="Open tasks"><?php echo (int)$c['tasks_open']; ?><?php echo $c['tasks_late'] ? ' <span class="chip w">' . (int)$c['tasks_late'] . ' late</span>' : ''; ?></td>
              <td class="acts"><a href="<?php echo $m; ?>kam/view/<?php echo (int)$c['lead_id']; ?>" title="Open client"><i class="fa fa-eye"></i></a></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
        <div class="sx-empty db-none" style="display:none">Nobody matches.</div>
      </div>
      <?php } ?>
    </div>
    </div>
<?php } ?>

<?php
/* ----------------------------------------------------------- Staff as cards */
if (isset($tabs['team'])) { ?>
    <div class="db-pane" id="dbPane-team" role="tabpanel" aria-labelledby="dbTab-team"<?php echo $first_tab === 'team' ? '' : ' hidden'; ?>>
    <div class="sx-card" id="dbTrack">
      <div class="sx-card-h">
        <div class="db-sum">
          <span><b class="bad"><?php echo $num($tr_over); ?></b> overdue</span>
          <span><b><?php echo $num($tr_today); ?></b> due today</span>
          <span><b><?php echo $num($tr_done); ?></b> done today</span>
          <span><b><?php echo $num($tr_calls); ?></b> calls today</span>
          <span><b class="<?php echo $tr_late ? 'warn' : ''; ?>"><?php echo $num($tr_late); ?></b> late tasks</span>
        </div>
        <div class="sx-tools">
          <div class="sx-chips db-tf">
            <button type="button" class="is-on" data-f="">All <span class="n"><?php echo count($tracker); ?></span></button>
            <button type="button" data-f="late">Overdue</button>
            <button type="button" data-f="idle">No activity today <span class="n"><?php echo (int)$tr_idle; ?></span></button>
          </div>
          <input type="search" class="form-control input-sm" id="fxPeopleQ" placeholder="Search name" aria-label="Search name">
        </div>
      </div>
      <div class="sx-card-b">
        <div class="fx-people" id="fxPeople">
          <?php foreach ($tracker as $r) {
            $idle = $is_now && $r['calls'] == 0 && $r['fu_done'] == 0;
            $od = $r['fu_oldest'] ? $days_late($r['fu_oldest']) : null;
            $sp = $pct($r['sales'], $r['target']);
            $ls = $ago($r['last_active_at']); ?>
          <button type="button" class="fx-p<?php echo $r['fu_overdue'] ? ' is-late' : ($idle ? ' is-idle' : ''); ?>" data-id="<?php echo (int)$r['id']; ?>" data-f="<?php echo ($r['fu_overdue'] ? 'late ' : '') . ($idle ? 'idle' : ''); ?>" data-q="<?php echo $h(strtolower($r['name'] . ' ' . $r['role'])); ?>" data-name="<?php echo $h($r['name']); ?>" data-role="<?php echo $h($r['role']); ?>" data-ini="<?php echo $h($ini($r['name'])); ?>" aria-haspopup="dialog">
            <span class="fx-p-h"><span class="av"><?php echo $h($ini($r['name'])); ?></span><span class="fx-p-n"><b><?php echo $h($r['name']); ?></b><small><?php echo $h($r['role']); ?></small></span><span class="db-seen<?php echo $ls === 'Online' ? ' on' : ''; ?>"><?php echo $h($ls); ?></span></span>
            <span class="fx-p-s">
              <span<?php echo $r['fu_overdue'] && $od !== null ? ' title="Oldest ' . $h($age($od)) . '"' : ''; ?>><b class="<?php echo $r['fu_overdue'] ? 'bad' : ''; ?>"><?php echo $num($r['fu_overdue']); ?></b><small>Overdue</small></span>
              <span><b><?php echo $num($r['fu_today']); ?></b><small>Due today</small></span>
              <span><b class="<?php echo $r['fu_done'] ? 'ok' : ''; ?>"><?php echo $num($r['fu_done']); ?></b><small>Done today</small></span>
              <span><b><?php echo $num($r['calls']); ?></b><small>Calls<?php echo $r['calls'] ? ' &middot; ' . (int)round($r['talk'] / 60) . ' min' : ''; ?></small></span>
            </span>
            <span class="fx-p-f">
              <?php if ($r['target'] > 0) { ?><span class="db-bar"><i style="width:<?php echo min(100, $sp); ?>%" class="<?php echo $sp >= 100 ? 'ok' : ''; ?>"></i></span><small><?php echo $money($r['sales']); ?> of <?php echo $money($r['target']); ?></small><?php } else { ?><small><?php echo $r['sales'] > 0 ? $money($r['sales']) . ' sales' : 'No sales'; ?> &middot; no target</small><?php } ?>
              <?php if ($r['tasks_late']) { ?><span class="chip w"><?php echo $num($r['tasks_late']); ?> late task<?php echo $r['tasks_late'] == 1 ? '' : 's'; ?></span><?php } ?>
            </span>
          </button>
          <?php } ?>
        </div>
        <div class="sx-empty db-none" id="fxPeopleNone" style="display:none">Nobody matches.</div>
      </div>
      <div class="sx-card-f"><span class="sx-help"><i class="fa fa-hand-pointer-o"></i>Click a person to see their overdue follow-ups, late tasks and recent calls.</span></div>
    </div>
    </div>
<?php } ?>

    <!-- My work -->
    <div class="db-pane" id="dbPane-mine" role="tabpanel" aria-labelledby="dbTab-mine"<?php echo $first_tab === 'mine' ? '' : ' hidden'; ?>>
    <div class="fx-two">
      <div class="sx-card">
        <div class="sx-card-h"><h3>My follow-ups to do <small>overdue and today, oldest first</small></h3><div class="sx-tools"><a class="btn btn-default btn-sm" href="<?php echo $fu_url; ?>1">All overdue</a></div></div>
        <?php if (!$my_list) { ?><div class="sx-empty"><i class="fa fa-check-circle-o"></i>Nothing overdue or due today.</div><?php } else { ?>
        <div class="sx-card-b fx-list">
          <?php foreach ($my_list as $l) { $dl = $days_late($l['followup_date']); ?>
          <a href="<?php echo $m; ?>leads/view/<?php echo base64_encode($l['id']); ?>">
            <span><b><?php echo $h($l['customer'] ? $l['customer'] : $l['poc']); ?></b><small><?php echo $h(trim($l['lead_id'] . ' · ' . ucfirst($l['stage']) . ' · ' . $l['status_name'], ' ·')); ?></small></span>
            <?php echo $dl > 0 ? '<span class="chip x">' . (int)$dl . ' days late</span>' : '<span class="chip w">today' . ($l['followup_time'] ? ' ' . $h($l['followup_time']) : '') . '</span>'; ?>
          </a>
          <?php } ?>
        </div>
        <?php } ?>
      </div>
      <div class="sx-card">
        <div class="sx-card-h"><h3>My tasks</h3><div class="sx-tools"><a class="btn btn-default btn-sm" href="<?php echo $m; ?>tasks">All tasks</a></div></div>
        <?php if (!$my_tasks) { ?><div class="sx-empty"><i class="fa fa-check-square-o"></i>No open tasks.</div><?php } else { ?>
        <div class="sx-card-b fx-list">
          <?php foreach (array_slice($my_tasks, 0, 8) as $tk) { ?>
          <a href="<?php echo $m; ?>tasks/view/<?php echo (int)$tk['id']; ?>"><span><b><?php echo $h($tk['customer'] ? $tk['customer'] : $tk['task_id']); ?></b><small><?php echo $h($tk['category']); ?></small></span><em class="<?php echo $tk['_late'] ? 'bad' : ''; ?>"><?php echo $h($fmt_d($tk['followup_date'])); ?></em></a>
          <?php } ?>
        </div>
        <?php } ?>
      </div>
    </div>
    </div>

  </section>

  <!-- One person's details (staff cards) -->
  <div class="fx-drawer" id="fxDrawer" hidden>
    <div class="fx-dr-bg" data-close></div>
    <div class="fx-dr-panel" role="dialog" aria-modal="true" aria-labelledby="fxDrName" tabindex="-1">
      <div class="fx-dr-h">
        <span class="av" id="fxDrIni"></span>
        <div><b id="fxDrName"></b><small id="fxDrRole"></small></div>
        <button type="button" class="fx-dr-x" data-close aria-label="Close"><i class="fa fa-times"></i></button>
      </div>
      <div class="fx-dr-b" id="fxDrBody"></div>
    </div>
  </div>
</div>

<script>
(function () {
  var base = '<?php echo $m; ?>';
  var $root = $('#dbRoot'), key = $root.data('key');
  function store(k, v) { try { if (v === undefined) return localStorage.getItem(k); localStorage.setItem(k, v); } catch (e) { return null; } }
  function esc(s) { return $('<div>').text(s == null ? '' : String(s)).html(); }
  function dd(d) { if (!d || d < '2000') return '-'; var x = new Date(String(d).substr(0, 10) + 'T00:00:00'); if (isNaN(x)) return esc(d); var o = { day: '2-digit', month: 'short' }; if (x.getFullYear() !== new Date().getFullYear()) o.year = 'numeric'; return x.toLocaleDateString('en-GB', o); }

  // Sections: remember the last one; tiles and "Needs attention" can open one.
  function showTab(k, keep) {
    var $p = $('#dbPane-' + k);
    if (!$p.length) return false;
    $('.fx-tabs [data-tab]').removeClass('is-on').attr('aria-selected', 'false');
    $('.fx-tabs [data-tab="' + k + '"]').addClass('is-on').attr('aria-selected', 'true');
    $('.db-pane').attr('hidden', true);
    $p.removeAttr('hidden');
    if (keep !== false) store('crm_dash_fx_tab_' + key, k);
    return true;
  }
  $('.fx-tabs').on('click', '[data-tab]', function () { showTab($(this).data('tab')); });
  $('.fx-tabs').on('keydown', '[data-tab]', function (e) {
    if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') return;
    var $all = $('.fx-tabs [data-tab]'), i = $all.index(this) + (e.key === 'ArrowRight' ? 1 : -1);
    var $n = $all.eq((i + $all.length) % $all.length); $n.focus(); showTab($n.data('tab'));
  });
  function go(k, f) {
    if (k === 'month') { var el = document.getElementById('fxMonth'); if (el) { el.scrollIntoView({ block: 'nearest', behavior: 'smooth' }); return true; } return false; }
    if (!showTab(k)) return false;
    if (k === 'team' && f !== undefined) $('.db-tf [data-f="' + (f || '') + '"]').trigger('click');
    var $p = $('#dbPane-' + k); ($('.fx-tabs').length ? $('.fx-tabs') : $p)[0].scrollIntoView({ block: 'start', behavior: 'smooth' });
    return true;
  }
  $root.on('click', '[data-go]', function (e) { if (go($(this).data('go'), $(this).data('f'))) e.preventDefault(); });
  var hs = (location.hash || '').replace('#', '');
  if (!(hs && showTab(hs, false))) { var last = store('crm_dash_fx_tab_' + key); if (last) showTab(last, false); }

  // Hide amounts; same setting as the Modern dashboard.
  function setHide(on) {
    $root.toggleClass('db-hide', on);
    $('#dbHideAmt').attr('aria-pressed', on ? 'true' : 'false').find('span').text(on ? 'Show amounts' : 'Hide amounts');
    $('#dbHideAmt i').attr('class', 'fa ' + (on ? 'fa-eye' : 'fa-eye-slash'));
    $('.db-days i').each(function () { $(this).attr('title', $(this).data('day') + (on ? '' : ': ' + $(this).data('amt'))); });
    store('crm_dash_hide_amt', on ? '1' : '0');
  }
  $('#dbHideAmt').on('click', function () { setHide(!$root.hasClass('db-hide')); });
  setHide(store('crm_dash_hide_amt') === '1');

  // Client search.
  $('.db-q').on('input', function () {
    var v = $.trim($(this).val()).toLowerCase(), $t = $($(this).data('t'));
    $t.find('tbody tr[data-q]').each(function () { $(this).toggleClass('db-hide-q', v !== '' && String($(this).data('q')).indexOf(v) < 0); });
    $t.siblings('.db-none').toggle($t.find('tbody tr[data-q]:not(.db-hide-q)').length === 0);
  });

  // Staff cards: filter and search.
  var flt = '';
  function people() {
    var v = $.trim($('#fxPeopleQ').val() || '').toLowerCase(), shown = 0;
    $('#fxPeople .fx-p').each(function () {
      var ok = (!flt || (' ' + $(this).data('f') + ' ').indexOf(' ' + flt + ' ') >= 0) && (!v || String($(this).data('q')).indexOf(v) >= 0);
      $(this).toggle(ok); if (ok) shown++;
    });
    $('#fxPeopleNone').toggle(shown === 0);
  }
  $('.db-tf').on('click', 'button', function () { flt = $(this).data('f') || ''; $(this).addClass('is-on').siblings().removeClass('is-on'); people(); });
  $('#fxPeopleQ').on('input', people);

  // Side panel with one person's open work.
  var $dr = $('#fxDrawer'), lastBtn = null;
  function closeDr() { $dr.attr('hidden', true); $('body').removeClass('fx-noscroll'); if (lastBtn) lastBtn.focus(); }
  function sec(title, n, rows, empty) { return '<section><h4>' + title + (n !== null ? ' <small>' + n + '</small>' : '') + '</h4>' + (rows || '<p class="db-xe">' + empty + '</p>') + '</section>'; }
  $('#fxPeople').on('click', '.fx-p', function () {
    var $p = $(this); lastBtn = this;
    $('#fxDrIni').text($p.data('ini')); $('#fxDrName').text($p.data('name')); $('#fxDrRole').text($p.data('role'));
    $('#fxDrBody').html('<p class="db-xe">Loading...</p>');
    $dr.removeAttr('hidden'); $('body').addClass('fx-noscroll'); $dr.find('.fx-dr-panel').focus();
    $.ajax({ url: base + 'dashboard/person/' + $p.data('id'), dataType: 'json', cache: false }).done(function (r) {
      if (!r || !r.ok) { $('#fxDrBody').html('<p class="db-xe">' + esc((r && r.msg) || 'Could not load.') + '</p>'); return; }
      var a = '', b = '', c = '', d = '';
      $.each(r.followups, function (i, l) { a += '<a href="' + base + 'leads/view/' + l.enc + '"><span>' + esc(l.customer || l.poc || l.lead_id) + '</span><em>' + dd(l.followup_date) + '</em></a>'; });
      $.each(r.late_tasks, function (i, t) { b += '<a href="' + base + 'tasks/view/' + t.id + '"><span>' + esc(t.customer || t.task_id) + '</span><em>' + dd(t.followup_date) + '</em></a>'; });
      $.each(r.calls, function (i, x) { c += '<p><span>' + esc(x.customer_name || x.phone_number) + '<small>' + esc(x.disposition || x.call_status) + '</small></span><em>' + dd(x.created_at) + ' ' + esc(String(x.created_at).substr(11, 5)) + '</em></p>'; });
      $.each(r.done, function (i, f) { d += '<p><span>' + esc(f.customer || ('#' + f.lead_id)) + '<small>' + esc(f.followup_result || f.followup_remark) + '</small></span><em>' + dd(f.followup_date) + '</em></p>'; });
      $('#fxDrBody').html(
        sec('Follow-ups to do', r.followups.length + (r.followups.length >= 15 ? '+' : ''), a, 'Nothing overdue.') +
        sec('Late tasks', r.late_tasks.length, b, 'No late tasks.') +
        sec('Recent calls', null, c, 'No calls logged.') +
        sec('Follow-ups done', null, d, 'None logged.'));
    }).fail(function (x) { $('#fxDrBody').html('<p class="db-xe">' + (x.status === 403 ? 'You can only open people in your team.' : 'Could not load.') + '</p>'); });
  });
  $dr.on('click', '[data-close]', closeDr);
  $(document).on('keydown', function (e) { if (e.key === 'Escape' && !$dr.is('[hidden]')) closeDr(); });
})();
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
