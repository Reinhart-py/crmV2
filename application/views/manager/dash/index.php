<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$b = base_url();
$m = base_url() . 'manager/';
$num = function ($n) { return number_format((float)$n, 0); };
// Amount as plain text (for tooltips) and as a marked span, so "Hide amounts" can cover it.
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
$t = $targets;
$late_tasks = count(array_filter($my_tasks, function ($x) { return $x['_late']; }));
$fu_url = $m . 'leads/followups?type=';
$scope_lbl = $dkey === 'kam' && !$is_admin ? 'my numbers' : ($has_team && !$only_me ? ($is_admin ? 'whole company' : 'me and my team') : 'my numbers');

// Totals used by tiles and tab badges.
$tr_over = 0; $tr_idle = 0; $tr_today = 0; $tr_done = 0; $tr_calls = 0; $tr_late = 0;
foreach ($tracker as $r) {
  $tr_over += $r['fu_overdue']; $tr_today += $r['fu_today']; $tr_done += $r['fu_done']; $tr_calls += $r['calls']; $tr_late += $r['tasks_late'];
  if ($is_now && $r['calls'] == 0 && $r['fu_done'] == 0) $tr_idle++;
}
$fl_late = 0; $fl_open = 0;
if (!empty($flow)) foreach ($flow as $f) { $fl_late += (int)$f['late']; $fl_open += (int)$f['n']; }
$cl_cold = 0;
if (!empty($kam_clients)) foreach ($kam_clients as $c) { if ($c['days_since'] === null || $c['days_since'] > 30) $cl_cold++; }

// Tabs for this role: key => [label, badge, badge style]. The first one opens by default.
$tabs = array();
if ($dkey === 'sales') $tabs['mine'] = array('My work', $my_fu['overdue'] + $my_fu['today'], 'r');
if ($dkey === 'backoffice') $tabs['orders'] = array('Orders', $fl_late, 'r');
if ($dkey === 'kam') $tabs['clients'] = array('Clients', $cl_cold, 'r');
if ($dkey !== 'backoffice') $tabs['target'] = array($dkey === 'admin' ? 'Overview' : 'Target', 0, '');
if ($dkey === 'admin') $tabs['orders'] = array('Orders', $fl_late, 'r');
if ($tracker) $tabs['team'] = array($is_admin && !$only_me ? 'Staff' : 'My team', $tr_over, 'r');
if ($dkey !== 'sales') $tabs['mine'] = array('My work', $my_fu['overdue'] + $my_fu['today'] + $late_tasks, 'r');
$first_tab = key($tabs);
$kpi = function ($cls, $href, $icon, $val, $label, $tab = '') {
  return '<a class="sx-kpi ' . $cls . '" href="' . $href . '"' . ($tab ? ' data-go="' . $tab . '"' : '') . '><span class="ic"><i class="fa ' . $icon . '"></i></span><div><b>' . $val . '</b><span>' . $label . '</span></div></a>';
};
?>
<link rel="stylesheet" href="<?php echo $b; ?>assets/dist/css/crm-sys.css?v=20261106_7">
<link rel="stylesheet" href="<?php echo $b; ?>assets/dist/css/crm-dash.css?v=20261107_6">
<div class="content-wrapper sx db" id="dbRoot" data-key="<?php echo $h($dkey); ?>">
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
/* ----------------------------------------------------------- KPI tiles per role */
if ($dkey === 'backoffice') {
  $rv = $reviews;
  echo $kpi(($rv[0]['n'] + $rv[1]['n']) ? 'a' : 'g', $m . 'leads/review_leads', 'fa-inbox', $num($rv[0]['n'] + $rv[1]['n']), 'Waiting for your review');
  echo $kpi('', '#orders', 'fa-cogs', $num($fl_open), 'Orders in process', 'orders');
  echo $kpi($fl_late ? 'r' : 'g', '#orders', 'fa-hourglass-half', $num($fl_late), 'Not moved for ' . (int)$late_days . '+ days', 'orders');
  echo $kpi('g', $m . 'orders', 'fa-check', $num($orders_done['sales_month']), 'Became sales this month');
} elseif ($dkey === 'admin') {
  $wait = 0; foreach ($reviews as $r) $wait += (int)$r['n'];
  echo $kpi('g', '#target', 'fa-line-chart', $money($t['achieved']), 'Sales in ' . $h(date('F', strtotime($win['start']))) . ($t['target'] > 0 ? ' &middot; ' . $pct($t['achieved'], $t['target']) . '% of target' : ''), 'target');
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
  echo $kpi($t['target'] > 0 && $is_now && $t['achieved'] < $t['pace'] ? 'r' : 'g', '#target', 'fa-bullseye', $money($t['achieved']), $t['target'] > 0 ? $pct($t['achieved'], $t['target']) . '% of ' . $money($t['target']) . ' target' : 'Sales this month', 'target');
}
?>
    </div>

    <nav class="db-tabs" role="tablist" aria-label="Dashboard sections">
      <?php foreach ($tabs as $k => $tb) { ?>
      <button type="button" role="tab" id="dbTab-<?php echo $k; ?>" aria-controls="dbPane-<?php echo $k; ?>" data-tab="<?php echo $k; ?>" class="<?php echo $k === $first_tab ? 'is-on' : ''; ?>" aria-selected="<?php echo $k === $first_tab ? 'true' : 'false'; ?>"><?php echo $h($tb[0]); ?><?php if ($tb[1] > 0) { ?><span class="n <?php echo $tb[2]; ?>"><?php echo $num($tb[1]); ?></span><?php } ?></button>
      <?php } ?>
    </nav>

<?php
/* ----------------------------------------------------------- Target / projection / achievement */
if (isset($tabs['target'])) {
  $tg = $t['target']; $ac = $t['achieved']; $pr = $t['projection'];
  $gap = max(0, $tg - $ac);
  $ap = $pct($ac, $tg); $pp = $pct($ac + $pr, $tg); $pace = $pct($t['pace'], $tg);
  $behind = $tg > 0 && $is_now && $ac < $t['pace'];
?>
    <div class="db-pane" id="dbPane-target" role="tabpanel" aria-labelledby="dbTab-target"<?php echo $first_tab === 'target' ? '' : ' hidden'; ?>>
    <div class="db-row">
      <div class="sx-card db-target">
        <div class="sx-card-h"><h3>Target and achievement <small><?php echo $h($scope_lbl); ?></small></h3>
          <?php if ($is_admin) { ?><div class="sx-tools"><a class="btn btn-default btn-sm" href="<?php echo $m; ?>hr/targets">Set targets</a></div><?php } ?></div>
        <div class="sx-card-b">
          <div class="db-money">
            <div><span>Target</span><b><?php echo $tg > 0 ? $money($tg) : '<em>Not set</em>'; ?></b></div>
            <div><span>Achieved</span><b class="ok"><?php echo $money($ac); ?></b><small><?php echo $num($t['sales_n']); ?> sale<?php echo $t['sales_n'] == 1 ? '' : 's'; ?><?php echo $tg > 0 ? ' &middot; ' . $ap . '%' : ''; ?></small></div>
            <div><span>Projection</span><b><?php echo $money($pr); ?></b><small><?php echo $num($t['projection_n']); ?> open lead<?php echo $t['projection_n'] == 1 ? '' : 's'; ?> closing this month</small></div>
            <div><span>Still needed</span><b class="<?php echo $gap > 0 ? 'bad' : 'ok'; ?>"><?php echo $tg > 0 ? $money($gap) : '-'; ?></b><?php if ($tg > 0) { ?><small><?php echo $ac + $pr >= $tg ? 'Projection covers it' : 'Projection short by ' . $money($tg - $ac - $pr); ?></small><?php } ?></div>
          </div>
          <?php if ($tg > 0) { ?>
          <div class="db-meter" role="img" aria-label="<?php echo $ap; ?>% achieved, <?php echo $pp; ?>% with projection">
            <i class="a" style="width:<?php echo min(100, $ap); ?>%"></i>
            <i class="p" style="left:<?php echo min(100, $ap); ?>%;width:<?php echo max(0, min(100, $pp) - min(100, $ap)); ?>%"></i>
            <?php if ($is_now) { ?><s style="left:<?php echo min(100, $pace); ?>%" title="Where the month should be by today"></s><?php } ?>
          </div>
          <div class="db-legend"><span><i class="a"></i>Achieved</span><span><i class="p"></i>Projection</span><?php if ($is_now) { ?><span><i class="t"></i>Should be by today: <?php echo $money($t['pace']); ?></span><?php } ?>
            <?php if ($behind) { ?><span class="chip x">Behind by <?php echo $money($t['pace'] - $ac); ?></span><?php } elseif ($is_now) { ?><span class="chip ok">On track</span><?php } ?></div>
          <?php } else { ?>
          <p class="sx-help"><i class="fa fa-info-circle"></i>No target for <?php echo $h(date('F', strtotime($win['start']))); ?>. Targets are set on HR &rsaquo; Targets.</p>
          <?php } ?>
          <table class="db-mini">
            <thead><tr><th></th><th>Done</th><th>Target</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($t['rows'] as $r) { $p = $pct($r['done'], $r['target']); ?>
              <tr><td><?php echo $h($r['label']); ?></td><td class="n"><?php echo $num($r['done']); ?></td><td class="n"><?php echo $r['target'] ? $num($r['target']) : '-'; ?></td>
                <td><?php if ($r['target']) { ?><span class="db-bar"><i style="width:<?php echo min(100, $p); ?>%" class="<?php echo $p >= 100 ? 'ok' : ''; ?>"></i></span><em><?php echo $p; ?>%</em><?php } ?></td></tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
      </div>

      <?php if ($dkey === 'admin' || $dkey === 'sales') { $mx = max(1, max($by_day)); ?>
      <div class="sx-card db-side">
        <div class="sx-card-h"><h3>Pipeline</h3></div>
        <div class="sx-card-b">
          <div class="db-funnel">
            <?php foreach ($funnel as $f) { ?>
            <a href="<?php echo $b . $f['url']; ?>"><b><?php echo $num($f['n']); ?></b><span><?php echo $h($f['label']); ?></span></a>
            <?php } ?>
          </div>
          <div class="db-days-h">Sales by day</div>
          <div class="db-days" role="img" aria-label="Sales by day this month">
            <?php foreach ($by_day as $d => $v) { ?><i style="height:<?php echo $v > 0 ? max(4, round($v * 100 / $mx)) : 0; ?>%" class="<?php echo $is_now && $d == (int)date('j') ? 'today' : ''; ?>" data-day="<?php echo (int)$d . ' ' . $h(date('M', strtotime($win['start']))); ?>" data-amt="<?php echo $h($money_t($v)); ?>"></i><?php } ?>
          </div>
          <div class="db-days-x"><span>1</span><span><?php echo (int)ceil($win['days'] / 2); ?></span><span><?php echo (int)$win['days']; ?></span></div>
        </div>
      </div>
      <?php } elseif ($dkey === 'kam') { ?>
      <div class="sx-card db-side">
        <div class="sx-card-h"><h3>Follow-ups</h3></div>
        <div class="sx-card-b">
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
<?php } ?>

<?php
/* ----------------------------------------------------------- Orders: flow, reviews, late orders */
if (isset($tabs['orders'])) {
  $fmx = 1; foreach ($flow as $f) $fmx = max($fmx, (int)$f['n']);
?>
    <div class="db-pane" id="dbPane-orders" role="tabpanel" aria-labelledby="dbTab-orders"<?php echo $first_tab === 'orders' ? '' : ' hidden'; ?>>
    <div class="db-row">
      <div class="sx-card">
        <div class="sx-card-h"><h3>Orders in process <small>by stage</small></h3><div class="sx-tools"><a class="btn btn-default btn-sm" href="<?php echo $m; ?>orders/open">Open orders</a></div></div>
        <div class="sx-card-b">
          <?php if (!$flow) { ?><div class="sx-empty"><i class="fa fa-check-circle-o"></i>No orders in process.</div><?php } ?>
          <?php foreach ($flow as $f) { ?>
          <div class="db-flow">
            <span class="l"><?php echo $h($f['status'] !== '' ? $f['status'] : 'No status'); ?></span>
            <span class="db-bar"><i style="width:<?php echo round($f['n'] * 100 / $fmx); ?>%"></i></span>
            <b><?php echo $num($f['n']); ?></b>
            <?php if ($f['late']) { ?><span class="chip x" title="Not moved for <?php echo (int)$late_days; ?>+ days"><?php echo $num($f['late']); ?> late</span><?php } else { ?><span class="chip na">&nbsp;</span><?php } ?>
          </div>
          <?php } ?>
        </div>
      </div>
      <div class="sx-card db-side">
        <div class="sx-card-h"><h3>Waiting for a decision</h3></div>
        <div class="sx-card-b db-list">
          <?php foreach ($reviews as $r) { ?>
          <a href="<?php echo $b . $r['url']; ?>"><span><?php echo $h($r['label']); ?></span><b class="<?php echo $r['n'] ? 'warn' : ''; ?>"><?php echo $num($r['n']); ?></b></a>
          <?php } ?>
          <a href="<?php echo $m; ?>orders"><span>New orders today</span><b><?php echo $num($orders_done['new_today']); ?></b></a>
          <a href="<?php echo $m; ?>orders"><span>Became sales today</span><b><?php echo $num($orders_done['sales_today']); ?></b></a>
        </div>
      </div>
    </div>

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
    </div>
<?php } ?>

<?php
/* ----------------------------------------------------------- KAM: clients by last contact */
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
/* ----------------------------------------------------------- Staff tracker */
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
          <input type="search" class="form-control input-sm db-q" data-t="#dbTrackTable" placeholder="Search name" aria-label="Search name">
        </div>
      </div>
      <div class="sx-table-wrap db-scroll">
        <table class="sx-table sx-cards db-track" id="dbTrackTable">
          <thead><tr><th>Person</th><th class="num">Overdue</th><th class="num">Due today</th><th class="num">Done today</th><th class="num">Calls today</th><th class="num">Late tasks</th><th>Sales this month</th><th>Last seen</th></tr></thead>
          <tbody>
          <?php foreach ($tracker as $r) {
            $idle = $is_now && $r['calls'] == 0 && $r['fu_done'] == 0;
            $od = $r['fu_oldest'] ? $days_late($r['fu_oldest']) : null;
            $sp = $pct($r['sales'], $r['target']); ?>
            <tr class="db-p" data-id="<?php echo (int)$r['id']; ?>" data-f="<?php echo ($r['fu_overdue'] ? 'late ' : '') . ($idle ? 'idle' : ''); ?>" data-q="<?php echo $h(strtolower($r['name'] . ' ' . $r['role'])); ?>" tabindex="0" aria-expanded="false">
              <td data-l="Person"><div class="who"><span class="av"><?php echo $h($ini($r['name'])); ?></span><div><span class="nm"><?php echo $h($r['name']); ?></span><span class="sub"><?php echo $h($r['role']); ?></span></div></div></td>
              <td data-l="Overdue" class="num"><?php if ($r['fu_overdue']) { ?><b class="bad"><?php echo $num($r['fu_overdue']); ?></b><span class="sub">oldest <?php echo $h($age($od)); ?></span><?php } else { ?><span class="text-muted">0</span><?php } ?></td>
              <td data-l="Due today" class="num"><?php echo $num($r['fu_today']); ?></td>
              <td data-l="Done today" class="num"><?php echo $r['fu_done'] ? '<b class="ok">' . $num($r['fu_done']) . '</b>' : '<span class="text-muted">0</span>'; ?></td>
              <td data-l="Calls today" class="num"><?php echo $r['calls'] ? $num($r['calls']) . '<span class="sub">' . (int)round($r['talk'] / 60) . ' min talk</span>' : '<span class="text-muted">0</span>'; ?></td>
              <td data-l="Late tasks" class="num"><?php echo $r['tasks_late'] ? '<b class="warn">' . $num($r['tasks_late']) . '</b>' : '<span class="text-muted">0</span>'; ?><?php echo $r['tasks_open'] ? '<span class="sub">of ' . (int)$r['tasks_open'] . ' open</span>' : ''; ?></td>
              <td data-l="Sales this month" class="db-sales"><?php if ($r['target'] > 0) { ?><span class="db-bar"><i style="width:<?php echo min(100, $sp); ?>%" class="<?php echo $sp >= 100 ? 'ok' : ''; ?>"></i></span><span class="sub"><?php echo $money($r['sales']); ?> of <?php echo $money($r['target']); ?></span><?php } else { echo $r['sales'] > 0 ? $money($r['sales']) : '<span class="text-muted">-</span>'; ?><span class="sub">No target</span><?php } ?></td>
              <td data-l="Last seen" class="nw"><?php $ls = $ago($r['last_active_at']); ?><span class="db-seen<?php echo $ls === 'Online' ? ' on' : ''; ?>"><?php echo $h($ls); ?></span></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
        <div class="sx-empty db-none" style="display:none">Nobody matches.</div>
      </div>
      <div class="sx-card-f"><span class="sx-help"><i class="fa fa-hand-pointer-o"></i>Click a person to see their overdue follow-ups, late tasks and recent calls.</span></div>
    </div>
    </div>
<?php } ?>

<?php
/* ----------------------------------------------------------- My work today */
?>
    <div class="db-pane" id="dbPane-mine" role="tabpanel" aria-labelledby="dbTab-mine"<?php echo $first_tab === 'mine' ? '' : ' hidden'; ?>>
    <div class="db-row">
      <div class="sx-card">
        <div class="sx-card-h"><h3>My follow-ups to do <small>overdue and today, oldest first</small></h3><div class="sx-tools"><a class="btn btn-default btn-sm" href="<?php echo $fu_url; ?>1">All overdue</a></div></div>
        <?php if (!$my_list) { ?><div class="sx-empty"><i class="fa fa-check-circle-o"></i>Nothing overdue or due today.</div><?php } else { ?>
        <div class="sx-table-wrap db-scroll">
          <table class="sx-table sx-cards">
            <thead><tr><th>Customer</th><th>Due</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($my_list as $l) { $dl = $days_late($l['followup_date']); ?>
              <tr>
                <td data-l="Customer"><a class="nm" href="<?php echo $m; ?>leads/view/<?php echo base64_encode($l['id']); ?>"><?php echo $h($l['customer'] ? $l['customer'] : $l['poc']); ?></a><span class="sub"><?php echo $h(trim($l['lead_id'] . ' · ' . ucfirst($l['stage']), ' ·')); ?></span></td>
                <td data-l="Due" class="nw"><?php echo $h($fmt_d($l['followup_date'])); ?> <?php echo $dl > 0 ? '<span class="chip x">' . (int)$dl . ' days late</span>' : '<span class="chip w">today' . ($l['followup_time'] ? ' ' . $h($l['followup_time']) : '') . '</span>'; ?></td>
                <td data-l="Status"><span class="chip s"><?php echo $h($l['status_name']); ?></span></td>
                <td class="acts"><a href="<?php echo $m; ?>leads/view/<?php echo base64_encode($l['id']); ?>" title="Open"><i class="fa fa-eye"></i></a></td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
        <?php } ?>
      </div>
      <div class="sx-card db-side">
        <div class="sx-card-h"><h3>My tasks</h3><div class="sx-tools"><a class="btn btn-default btn-sm" href="<?php echo $m; ?>tasks">All tasks</a></div></div>
        <?php if (!$my_tasks) { ?><div class="sx-empty"><i class="fa fa-check-square-o"></i>No open tasks.</div><?php } else { ?>
        <div class="sx-card-b db-list">
          <?php foreach (array_slice($my_tasks, 0, 8) as $tk) { ?>
          <a href="<?php echo $m; ?>tasks/view/<?php echo (int)$tk['id']; ?>"><span><?php echo $h($tk['customer'] ? $tk['customer'] : $tk['task_id']); ?><small><?php echo $h($tk['category']); ?></small></span><em class="<?php echo $tk['_late'] ? 'bad' : ''; ?>"><?php echo $h($fmt_d($tk['followup_date'])); ?></em></a>
          <?php } ?>
        </div>
        <?php } ?>
      </div>
    </div>
    </div>

  </section>
</div>

<script>
(function () {
  var base = '<?php echo $m; ?>';
  var $root = $('#dbRoot'), key = $root.data('key');
  function store(k, v) { try { if (v === undefined) return localStorage.getItem(k); localStorage.setItem(k, v); } catch (e) { return null; } }
  function esc(s) { return $('<div>').text(s == null ? '' : String(s)).html(); }
  function dd(d) { if (!d || d < '2000') return '-'; var x = new Date(String(d).substr(0, 10) + 'T00:00:00'); if (isNaN(x)) return esc(d); var o = { day: '2-digit', month: 'short' }; if (x.getFullYear() !== new Date().getFullYear()) o.year = 'numeric'; return x.toLocaleDateString('en-GB', o); }

  // Tabs: remember the last one per dashboard; a tile can open a tab.
  function showTab(k, keep) {
    var $b = $('.db-tabs [data-tab="' + k + '"]');
    if (!$b.length) return false;
    $('.db-tabs [data-tab]').removeClass('is-on').attr('aria-selected', 'false');
    $b.addClass('is-on').attr('aria-selected', 'true');
    $('.db-pane').attr('hidden', true);
    $('#dbPane-' + k).removeAttr('hidden');
    if (keep !== false) store('crm_dash_tab_' + key, k);
    return true;
  }
  $('.db-tabs').on('click', '[data-tab]', function () { showTab($(this).data('tab')); });
  $('.db-tabs').on('keydown', '[data-tab]', function (e) {
    if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') return;
    var $all = $('.db-tabs [data-tab]'), i = $all.index(this) + (e.key === 'ArrowRight' ? 1 : -1);
    var $n = $all.eq((i + $all.length) % $all.length); $n.focus(); showTab($n.data('tab'));
  });
  $('.sx-kpis').on('click', '[data-go]', function (e) { if (showTab($(this).data('go'))) { e.preventDefault(); $('.db-tabs')[0].scrollIntoView({ block: 'nearest' }); } });
  var h = (location.hash || '').replace('#', '');
  if (!(h && showTab(h, false))) { var last = store('crm_dash_tab_' + key); if (last) showTab(last, false); }

  // Hide amounts (for screen sharing or a shared screen); remembered on this computer.
  function setHide(on) {
    $root.toggleClass('db-hide', on);
    $('#dbHideAmt').attr('aria-pressed', on ? 'true' : 'false').find('span').text(on ? 'Show amounts' : 'Hide amounts');
    $('#dbHideAmt i').attr('class', 'fa ' + (on ? 'fa-eye' : 'fa-eye-slash'));
    $('.db-days i').each(function () { $(this).attr('title', $(this).data('day') + (on ? '' : ': ' + $(this).data('amt'))); });
    store('crm_dash_hide_amt', on ? '1' : '0');
  }
  $('#dbHideAmt').on('click', function () { setHide(!$root.hasClass('db-hide')); });
  setHide(store('crm_dash_hide_amt') === '1');

  // Search boxes filter their table by data-q.
  function refreshEmpty($t) { $t.siblings('.db-none').toggle($t.find('tbody tr[data-q]:not(.db-hide-q):not(.db-hide-f)').length === 0); }
  $('.db-q').on('input', function () {
    var v = $.trim($(this).val()).toLowerCase(), $t = $($(this).data('t'));
    $t.find('tbody tr[data-q]').each(function () { $(this).toggleClass('db-hide-q', v !== '' && String($(this).data('q')).indexOf(v) < 0); });
    $t.find('tr.db-x').remove(); $t.find('tr.db-p').attr('aria-expanded', 'false');
    refreshEmpty($t);
  });
  $('.db-tf').on('click', 'button', function () {
    var f = $(this).data('f'), $t = $('#dbTrackTable');
    $(this).addClass('is-on').siblings().removeClass('is-on');
    $t.find('tr.db-x').remove(); $t.find('tr.db-p').attr('aria-expanded', 'false');
    $t.find('tbody tr.db-p').each(function () { $(this).toggleClass('db-hide-f', !!f && (' ' + $(this).data('f') + ' ').indexOf(' ' + f + ' ') < 0); });
    refreshEmpty($t);
  });

  // Person drill-down.
  function open($tr) {
    var $n = $tr.next('tr.db-x');
    if ($n.length) { $n.remove(); $tr.attr('aria-expanded', 'false'); return; }
    $('#dbTrackTable tr.db-x').remove(); $('#dbTrackTable tr.db-p').attr('aria-expanded', 'false');
    var cols = $tr.children('td').length;
    var $x = $('<tr class="db-x"><td colspan="' + cols + '"><div class="db-xin">Loading...</div></td></tr>').insertAfter($tr);
    $tr.attr('aria-expanded', 'true');
    $.ajax({ url: base + 'dashboard/person/' + $tr.data('id'), dataType: 'json', cache: false }).done(function (r) {
      if (!r || !r.ok) { $x.find('.db-xin').text((r && r.msg) || 'Could not load.'); return; }
      var h = '<div class="db-xg">';
      h += '<div><h4>Follow-ups to do <small>' + r.followups.length + (r.followups.length >= 15 ? '+' : '') + '</small></h4>';
      if (!r.followups.length) h += '<p class="db-xe">Nothing overdue.</p>';
      $.each(r.followups, function (i, l) { h += '<a href="' + base + 'leads/view/' + l.enc + '"><span>' + esc(l.customer || l.poc || l.lead_id) + '</span><em>' + dd(l.followup_date) + '</em></a>'; });
      h += '</div><div><h4>Late tasks <small>' + r.late_tasks.length + '</small></h4>';
      if (!r.late_tasks.length) h += '<p class="db-xe">No late tasks.</p>';
      $.each(r.late_tasks, function (i, t) { h += '<a href="' + base + 'tasks/view/' + t.id + '"><span>' + esc(t.customer || t.task_id) + '</span><em>' + dd(t.followup_date) + '</em></a>'; });
      h += '</div><div><h4>Recent calls</h4>';
      if (!r.calls.length) h += '<p class="db-xe">No calls logged.</p>';
      $.each(r.calls, function (i, c) { h += '<p><span>' + esc(c.customer_name || c.phone_number) + '<small>' + esc(c.disposition || c.call_status) + '</small></span><em>' + dd(c.created_at) + ' ' + esc(String(c.created_at).substr(11, 5)) + '</em></p>'; });
      h += '</div><div><h4>Follow-ups done</h4>';
      if (!r.done.length) h += '<p class="db-xe">None logged.</p>';
      $.each(r.done, function (i, f) { h += '<p><span>' + esc(f.customer || ('#' + f.lead_id)) + '<small>' + esc(f.followup_result || f.followup_remark) + '</small></span><em>' + dd(f.followup_date) + '</em></p>'; });
      h += '</div></div>';
      $x.find('.db-xin').html(h);
    }).fail(function (x) { $x.find('.db-xin').text(x.status === 403 ? 'You can only open people in your team.' : 'Could not load.'); });
  }
  $('#dbTrackTable').on('click', 'tr.db-p', function (e) { if (!$(e.target).closest('a').length) open($(this)); })
    .on('keydown', 'tr.db-p', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open($(this)); } });
})();
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
