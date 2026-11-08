<?php
/* Classic 6: Admin (a), Back Office (b) and KAM (k) views. Same cards, tiles and section headings as Classic 5.
   Included from manager/dashboard_classic6.php; the Sales view (s) is the Classic 5 content in that file. */
$c6v = $c6['view'];
$curr = !empty($user_currency) ? $user_currency : 'AED';
$m = base_url() . 'manager/';
$c6_who = !empty($c6['is_all']) ? 'All' : 'Team';
if (!function_exists('c6_h')) {
    function c6_h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
    function c6_lead($id) { return base_url() . 'manager/leads/view/' . base64_encode($id); }
    function c6_d($d) { $t = strtotime((string)$d); return ($d && $t && $d >= '2000-01-01') ? date('d M', $t) : '-'; }
    function c6_tile($href, $col, $value, $label, $sub, $icon, $amt = false) {
        return '<div class="col-sm-3 col-xs-6 c6-tile-col"><a href="' . $href . '" class="dash-kpi-card ' . $col . '"><div style="display:flex; justify-content:space-between; align-items:flex-start;"><div style="min-width:0; flex:1;">'
            . '<div class="dash-kpi-value' . ($amt ? ' c2-amt' : '') . '">' . $value . '</div><div class="dash-kpi-label">' . $label . '</div>'
            . ($sub !== '' ? '<div class="dash-kpi-sales' . ($amt ? '' : '') . '">' . $sub . '</div>' : '')
            . '</div><div class="dash-kpi-icon"><i class="fa ' . $icon . '"></i></div></div></a></div>';
    }
    function c6_divider($id, $title, $top = 20) {
        return '<div class="dash-section-divider" id="' . $id . '" style="margin-top: ' . (int)$top . 'px; margin-bottom: 14px;"><h3 class="dash-section-title" style="margin-bottom:0;"><span class="dash-section-title-icon"><span></span><span></span></span><span>' . $title . '</span></h3></div>';
    }
    function c6_bars($rows, $cls = '') {
        $max = 0; foreach ($rows as $r) $max = max($max, (int)$r[1]);
        $h = '';
        foreach ($rows as $r) {
            $w = $max > 0 && (int)$r[1] > 0 ? max(3, (int)round($r[1] / $max * 100)) : 0;
            $lab = isset($r[3]) && $r[3] !== '' ? '<a href="' . $r[3] . '">' . c6_h($r[0]) . '</a>' : c6_h($r[0]);
            $h .= '<div class="c5-ag ' . (isset($r[4]) ? $r[4] : $cls) . '"><div class="c5-ag-top"><span>' . $lab . '</span><span><b>' . (int)$r[1] . '</b>' . (isset($r[2]) && $r[2] !== '' ? ' <em>' . $r[2] . '</em>' : '') . '</span></div><div class="c5-ag-bar"><i style="width: ' . $w . '%;"></i></div></div>';
        }
        return $h;
    }
}
?>
<div id="c2-top" class="c2-anchor"></div>
<?php if ($c6v === 'a') { $co = $c6['co']; $t = $co['tiles']; $pct = $t['target'] > 0 ? (int)round($t['sales_amt'] / $t['target'] * 100) : null; ?>
<div class="c6-tiles">
    <div class="row c6-tile-row">
        <?php
        echo c6_tile($m . 'users', 'dash-kpi-col1', number_format($t['staff']), 'Active Staff', $t['online'] . ' online now', 'fa-users');
        echo c6_tile($m . 'database', 'dash-kpi-col2', number_format($t['db']), 'Database Records', 'Whole company', 'fa-database');
        echo c6_tile($m . 'leads/view_leads', 'dash-kpi-col3', number_format($t['leads']), 'Open Leads', $t['fu_missed'] . ' missed follow-ups', 'fa-bullseye');
        echo c6_tile($m . 'orders/open', 'dash-kpi-col4', number_format($t['open']), 'Open Orders', $t['late'] . ' not moved for 3+ days', 'fa-shopping-cart');
        ?>
    </div>
    <div class="row c6-tile-row">
        <?php
        echo c6_tile($m . 'orders/sales', 'dash-kpi-col1', number_format($t['sales_n']), 'Sales ' . $co['month'], '<span class="c2-amt">' . $curr . ' ' . number_format($t['sales_amt'], 0) . '</span>', 'fa-check-circle');
        echo c6_tile($m . 'hr/targets', 'dash-kpi-col2', $pct === null ? '-' : $pct . '%', 'Of Target ' . $co['month'], '<span class="c2-amt">' . $curr . ' ' . number_format($t['target'], 0) . '</span> target', 'fa-crosshairs');
        echo c6_tile($m . 'kam/my-client', 'dash-kpi-col3', number_format($co['kam']['total']), 'KAM Clients', ($co['kam']['buckets']['older'] + $co['kam']['buckets']['never']) . ' not contacted for 30+ days', 'fa-handshake-o');
        echo c6_tile($m . 'orders/open', 'dash-kpi-col4', number_format(array_sum(array_map(function ($f) { return (int)$f['n']; }, $co['flow']))), 'With Back Office', 'Open orders in processing', 'fa-briefcase');
        ?>
    </div>
</div>

<?php echo c6_divider('c6-branch', 'Branches'); ?>
<div class="row c6-row">
    <div class="col-xs-12">
        <div class="crm-modern-stat-card c5-card">
            <div class="card-top">
                <h4 class="card-title"><i class="fa fa-building-o" style="color:#0891b2;"></i> By Branch</h4>
                <span class="card-total-pill">Office set on each user &middot; sales <?php echo $co['month']; ?></span>
            </div>
            <?php $bmax = 0; foreach ($co['branch'] as $b) $bmax = max($bmax, $b['sales_amt']); ?>
            <div class="c6-tbl-wrap">
            <table class="c3-clo c6-tbl">
                <thead><tr><th>Branch</th><th>Staff</th><th>Online</th><th>Database</th><th>Open leads</th><th>Missed follow-ups</th><th>Open orders</th><th>Sales</th><th class="c6-amt-col">Amount</th></tr></thead>
                <tbody>
                <?php if (empty($co['branch'])) { ?><tr><td colspan="9" class="c6-none">No data yet</td></tr><?php } ?>
                <?php foreach ($co['branch'] as $b) { ?>
                    <tr>
                        <td><?php echo c6_h($b['label']); ?></td><td><?php echo $b['staff']; ?></td><td><?php echo $b['online']; ?></td>
                        <td><?php echo number_format($b['db']); ?></td><td><?php echo number_format($b['leads']); ?></td>
                        <td><span class="badge bg-red<?php echo $b['fu_missed'] ? '' : ' c3-zero'; ?>"><?php echo $b['fu_missed']; ?></span></td>
                        <td><?php echo $b['open']; ?></td><td><?php echo $b['sales_n']; ?></td>
                        <td class="c6-amt-col"><span class="c2-amt"><?php echo $curr . ' ' . number_format($b['sales_amt'], 0); ?></span><div class="c5-lb-bar"><i style="width: <?php echo $bmax > 0 && $b['sales_amt'] > 0 ? max(3, (int)round($b['sales_amt'] / $bmax * 100)) : 0; ?>%;"></i></div></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
            <div class="c5-note">Leads count under the branch of their owner (assigned person, else the creator); orders under the branch of the person who made them. Users without an office are under "Not set".</div>
        </div>
    </div>
</div>

<?php echo c6_divider('c6-cat', 'Business Categories and Departments'); ?>
<div class="row c6-row c6-eq">
    <div class="col-md-6 col-xs-12">
        <div class="crm-modern-stat-card c5-card">
            <div class="card-top">
                <h4 class="card-title"><i class="fa fa-tags" style="color:#7c3aed;"></i> By Business Category</h4>
                <span class="card-total-pill">Vertical on the order</span>
            </div>
            <div class="c6-tbl-wrap">
            <table class="c3-clo c6-tbl">
                <thead><tr><th>Category</th><th>Orders</th><th>Open</th><th>Sales (all)</th><th>Sales <?php echo $co['month']; ?></th><th class="c6-amt-col">Amount</th><th>Cancelled</th></tr></thead>
                <tbody>
                <?php if (empty($co['category'])) { ?><tr><td colspan="7" class="c6-none">No orders yet</td></tr><?php } ?>
                <?php foreach ($co['category'] as $c) { ?>
                    <tr><td><?php echo c6_h($c['label']); ?></td><td><?php echo (int)$c['n']; ?></td><td><?php echo (int)$c['open_n']; ?></td><td><?php echo (int)$c['sales_all']; ?></td><td><?php echo (int)$c['sales_n']; ?></td><td class="c6-amt-col"><span class="c2-amt"><?php echo $curr . ' ' . number_format((float)$c['sales_amt'], 0); ?></span></td><td><?php echo (int)$c['cancelled']; ?></td></tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xs-12">
        <div class="crm-modern-stat-card c5-card">
            <div class="card-top">
                <h4 class="card-title"><i class="fa fa-sitemap" style="color:#0f8a80;"></i> By Department</h4>
                <span class="card-total-pill">Role of each user</span>
            </div>
            <div class="c6-tbl-wrap">
            <table class="c3-clo c6-tbl">
                <thead><tr><th>Department</th><th>Staff</th><th>Online</th><th>Open leads</th><th>Missed follow-ups</th><th>Calls today</th><th class="c6-amt-col">Sales <?php echo $co['month']; ?></th></tr></thead>
                <tbody>
                <?php foreach ($co['dept'] as $d) { ?>
                    <tr><td><?php echo c6_h($d['label']); ?></td><td><?php echo $d['staff']; ?></td><td><?php echo $d['online']; ?></td><td><?php echo number_format($d['leads']); ?></td><td><span class="badge bg-red<?php echo $d['fu_missed'] ? '' : ' c3-zero'; ?>"><?php echo $d['fu_missed']; ?></span></td><td><?php echo $d['calls']; ?></td><td class="c6-amt-col"><?php echo $d['sales_n']; ?> &middot; <span class="c2-amt"><?php echo $curr . ' ' . number_format($d['sales_amt'], 0); ?></span></td></tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

<?php echo c6_divider('c6-dept', 'Back Office, KAM and People'); ?>
<div class="row c5-team-row">
    <div class="col-lg-4 col-sm-6 col-xs-12">
        <div class="crm-modern-stat-card c5-card">
            <div class="card-top">
                <h4 class="card-title"><i class="fa fa-briefcase" style="color:#ea580c;"></i> Back Office: Orders by Stage</h4>
                <span class="card-total-pill">Late = 3+ days</span>
            </div>
            <?php if (empty($co['flow'])) { ?><div class="c5-empty">No open orders</div><?php } else {
                $rows = array(); foreach ($co['flow'] as $f) $rows[] = array($f['status'], (int)$f['n'], (int)$f['late'] ? (int)$f['late'] . ' late' : '', $m . 'orders/open', (int)$f['late'] ? 'c5-ag-old' : 'c5-ag-ok');
                echo c6_bars($rows); } ?>
            <div class="c5-note">Open orders (not yet a sale) by status.</div>
        </div>
    </div>
    <div class="col-lg-4 col-sm-6 col-xs-12">
        <div class="crm-modern-stat-card c5-card">
            <div class="card-top">
                <h4 class="card-title"><i class="fa fa-handshake-o" style="color:#7c3aed;"></i> KAM: Clients by Last Contact</h4>
                <span class="card-total-pill"><?php echo $co['kam']['total']; ?> clients</span>
            </div>
            <?php $kb = $co['kam']['buckets']; echo c6_bars(array(
                array('0 to 7 days', $kb['d7'], '', '', 'c5-ag-ok'), array('8 to 30 days', $kb['d30'], '', '', 'c5-ag-mid'),
                array('Over 30 days', $kb['older'], '', '', 'c5-ag-old'), array('Never contacted', $kb['never'], '', '', 'c5-ag-old'))); ?>
            <div class="c5-note">Last follow-up entry or call on each onboarded client. Open client tasks: <?php echo $co['kam']['tasks']; ?> (<?php echo $co['kam']['tasks_late']; ?> late).</div>
        </div>
    </div>
    <div class="col-lg-4 col-sm-12 col-xs-12">
        <div class="crm-modern-stat-card c5-card">
            <div class="card-top">
                <h4 class="card-title"><i class="fa fa-user-circle-o" style="color:#dc2626;"></i> People to Check</h4>
                <span class="card-total-pill">Most behind first</span>
            </div>
            <?php if (empty($co['track'])) { ?><div class="c5-empty">Nobody has missed follow-ups or late tasks</div><?php } else { ?>
            <table class="c3-clo c6-tbl">
                <thead><tr><th>Name</th><th>Missed follow-ups</th><th>Late tasks</th><th>Calls today</th></tr></thead>
                <tbody>
                <?php foreach ($co['track'] as $p) { ?>
                    <tr><td><span class="c6-name" title="<?php echo c6_h($p['name'] . ($p['role'] ? ' (' . $p['role'] . ')' : '')); ?>"><?php echo c6_h($p['name']); ?></span></td><td><span class="badge bg-red<?php echo $p['fu_overdue'] ? '' : ' c3-zero'; ?>"><?php echo $p['fu_overdue']; ?></span></td><td><span class="badge bg-orange<?php echo $p['tasks_late'] ? '' : ' c3-zero'; ?>"><?php echo $p['tasks_late']; ?></span></td><td><?php echo $p['calls']; ?></td></tr>
                <?php } ?>
                </tbody>
            </table>
            <?php } ?>
        </div>
    </div>
</div>

<?php } elseif ($c6v === 'b') { $bo = $c6['bo']; $t = $bo['tiles']; ?>
<div class="c6-tiles">
    <div class="row c6-tile-row">
        <?php
        echo c6_tile($m . 'orders/open', 'dash-kpi-col1', number_format($t['open']), 'Open Orders', 'In processing', 'fa-shopping-cart');
        echo c6_tile($m . 'orders/open', 'dash-kpi-col2', number_format($t['late']), 'Not Moved 3+ Days', 'Need a push', 'fa-hourglass-half');
        echo c6_tile($m . 'orders/my', 'dash-kpi-col3', number_format($t['mine']), 'Assigned to Me', 'Open orders', 'fa-user');
        echo c6_tile($m . 'back-office', 'dash-kpi-col4', number_format($t['reviews']), 'Waiting for Review', 'Conversions, requests, approvals', 'fa-check-square-o');
        ?>
    </div>
    <div class="row c6-tile-row">
        <?php
        echo c6_tile($m . 'orders', 'dash-kpi-col1', number_format($t['done']['new_today']), 'New Orders Today', number_format($bo['hand']['new_month']) . ' this month', 'fa-inbox');
        echo c6_tile($m . 'orders/open', 'dash-kpi-col2', number_format($bo['docs']['orders']), 'Documents Pending', $bo['docs']['items'] . ' items to collect', 'fa-file-text-o');
        echo c6_tile($m . 'orders/pending_sales_approval', 'dash-kpi-col3', number_format($bo['hand']['onboarding'] + $bo['hand']['approval']), 'With KAM', $bo['hand']['onboarding'] . ' onboarding, ' . $bo['hand']['approval'] . ' approval', 'fa-share');
        echo c6_tile($m . 'orders/sales', 'dash-kpi-col4', number_format($t['done']['sales_month']), 'Completed This Month', $t['done']['sales_today'] . ' today', 'fa-check-circle');
        ?>
    </div>
</div>

<?php echo c6_divider('c6-orders', 'Order Processing'); ?>
<div class="row c5-team-row">
    <div class="col-lg-3 col-sm-6 col-xs-12">
        <div class="crm-modern-stat-card c5-card">
            <div class="card-top">
                <h4 class="card-title"><i class="fa fa-tasks" style="color:#ea580c;"></i> Orders by Stage</h4>
                <span class="card-total-pill"><?php echo $t['open']; ?> open</span>
            </div>
            <?php if (empty($bo['flow'])) { ?><div class="c5-empty">No open orders</div><?php } else {
                $rows = array(); foreach ($bo['flow'] as $f) $rows[] = array($f['status'], (int)$f['n'], (int)$f['late'] ? (int)$f['late'] . ' late' : '', $m . 'orders/open', (int)$f['late'] ? 'c5-ag-old' : 'c5-ag-ok');
                echo c6_bars($rows); } ?>
            <div class="c5-note">Late = no change for more than 3 days.</div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 col-xs-12">
        <div class="crm-modern-stat-card c5-card">
            <div class="card-top">
                <h4 class="card-title"><i class="fa fa-check-square-o" style="color:#0284c7;"></i> Waiting for Review</h4>
                <span class="card-total-pill"><?php echo $t['reviews']; ?> waiting</span>
            </div>
            <table class="c3-clo c6-tbl">
                <tbody>
                <?php foreach ($bo['reviews'] as $r) { ?>
                    <tr><td><a href="<?php echo base_url() . $r['url']; ?>"><?php echo c6_h($r['label']); ?></a></td><td><span class="badge bg-skyblue<?php echo $r['n'] ? '' : ' c3-zero'; ?>"><?php echo (int)$r['n']; ?></span></td></tr>
                <?php } ?>
                    <tr><td><a href="<?php echo $m; ?>orders/open">Orders in Onboarding</a></td><td><span class="badge bg-blue<?php echo $bo['hand']['onboarding'] ? '' : ' c3-zero'; ?>"><?php echo $bo['hand']['onboarding']; ?></span></td></tr>
                </tbody>
            </table>
            <div class="c5-note">Back Office checks and passes orders on; KAM onboards the client and approves the sale.</div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 col-xs-12">
        <div class="crm-modern-stat-card c5-card">
            <div class="card-top">
                <h4 class="card-title"><i class="fa fa-tags" style="color:#7c3aed;"></i> Open by Category</h4>
                <span class="card-total-pill">Vertical</span>
            </div>
            <?php if (empty($bo['category'])) { ?><div class="c5-empty">No open orders</div><?php } else {
                $names = array('accounting' => 'Accounting', 'telecom' => 'Telecom', 'it' => 'IT', 'other' => 'Other');
                $rows = array(); foreach ($bo['category'] as $c) $rows[] = array(isset($names[$c['k']]) ? $names[$c['k']] : ucwords($c['k']), (int)$c['n'], '', '', 'c6-ag-cat');
                echo c6_bars($rows); } ?>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 col-xs-12">
        <div class="crm-modern-stat-card c5-card">
            <?php $wk = $bo['weekly']; $wmax = 0; $wsum = 0; foreach ($wk as $w) { $wmax = max($wmax, $w['n']); $wsum += $w['n']; } ?>
            <div class="card-top">
                <h4 class="card-title"><i class="fa fa-line-chart" style="color:#7c3aed;"></i> Orders per Week</h4>
                <span class="card-total-pill"><?php echo $wsum; ?> in 8 weeks</span>
            </div>
            <?php
            $W = 260; $H = 92; $P = 8; $n = count($wk); $pts = array();
            foreach ($wk as $i => $w) { $x = $n > 1 ? $P + $i * ($W - 2 * $P) / ($n - 1) : $W / 2; $y = $H - $P - ($wmax > 0 ? ($w['n'] / $wmax) * ($H - 2 * $P - 12) : 0); $pts[] = array(round($x, 1), round($y, 1), $w); }
            $line = implode(' ', array_map(function ($p) { return $p[0] . ',' . $p[1]; }, $pts));
            ?>
            <svg class="c5-spark" viewBox="0 0 <?php echo $W . ' ' . $H; ?>" preserveAspectRatio="none" role="img" aria-label="Orders received per week">
                <?php if ($pts) { ?>
                <polygon class="c5-spark-area" points="<?php echo $pts[0][0] . ',' . ($H - $P) . ' ' . $line . ' ' . $pts[count($pts) - 1][0] . ',' . ($H - $P); ?>"></polygon>
                <polyline class="c5-spark-line" points="<?php echo $line; ?>"></polyline>
                <?php foreach ($pts as $p) { ?><circle class="c5-spark-dot" cx="<?php echo $p[0]; ?>" cy="<?php echo $p[1]; ?>" r="3"><title><?php echo date('d M', strtotime($p[2]['from'])) . ' - ' . date('d M', strtotime($p[2]['to'])) . ': ' . (int)$p[2]['n']; ?></title></circle><?php } ?>
                <?php } ?>
            </svg>
            <div class="c5-spark-x"><span><?php echo $wk ? $wk[0]['label'] : ''; ?></span><span>This week: <b><?php echo $wk ? (int)$wk[count($wk) - 1]['n'] : 0; ?></b></span></div>
            <div class="c5-note">New orders by the week they came in.</div>
        </div>
    </div>
</div>

<?php echo c6_divider('c6-docs', 'Documents and Late Orders'); ?>
<div class="row c6-row c6-eq">
    <div class="col-md-6 col-xs-12">
        <div class="crm-modern-stat-card c5-card">
            <div class="card-top">
                <h4 class="card-title"><i class="fa fa-file-text-o" style="color:#0891b2;"></i> Documents Pending</h4>
                <span class="card-total-pill"><?php echo $bo['docs']['orders']; ?> orders</span>
            </div>
            <?php if (empty($bo['docs']['list'])) { ?><div class="c5-empty">All documents collected</div><?php } else { ?>
            <div class="c6-tbl-wrap">
            <table class="c3-clo c6-tbl c6-list">
                <thead><tr><th>Order</th><th>Client</th><th>Still to collect</th><th>Assigned</th></tr></thead>
                <tbody>
                <?php foreach ($bo['docs']['list'] as $o) { ?>
                    <tr><td><a href="<?php echo $m . 'orders/view/' . $o['id']; ?>"><?php echo c6_h($o['order_no'] ? $o['order_no'] : '#' . $o['id']); ?></a></td><td><span class="c6-name"><?php echo c6_h($o['client']); ?></span></td><td><span class="badge bg-orange"><?php echo count($o['pending']); ?> / <?php echo $o['total']; ?></span> <span class="c6-sub" title="<?php echo c6_h(implode(', ', $o['pending'])); ?>"><?php echo c6_h(implode(', ', $o['pending'])); ?></span></td><td><?php echo c6_h($o['assignee']); ?></td></tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
            <?php } ?>
        </div>
    </div>
    <div class="col-md-6 col-xs-12">
        <div class="crm-modern-stat-card c5-card">
            <div class="card-top">
                <h4 class="card-title"><i class="fa fa-hourglass-half" style="color:#dc2626;"></i> Orders Not Moved</h4>
                <span class="card-total-pill">Oldest first</span>
            </div>
            <?php if (empty($bo['late_list'])) { ?><div class="c5-empty">No order is waiting more than 3 days</div><?php } else { ?>
            <div class="c6-tbl-wrap">
            <table class="c3-clo c6-tbl c6-list">
                <thead><tr><th>Order</th><th>Client</th><th>Status</th><th>Days</th><th>Assigned</th></tr></thead>
                <tbody>
                <?php foreach ($bo['late_list'] as $o) { $days = (int)floor((time() - strtotime($o['moved_at'])) / 86400); ?>
                    <tr><td><a href="<?php echo $m . 'orders/view/' . (int)$o['id']; ?>"><?php echo c6_h($o['order_no'] ? $o['order_no'] : '#' . $o['id']); ?></a></td><td><span class="c6-name"><?php echo c6_h($o['client_name'] ? $o['client_name'] : $o['company_name']); ?></span></td><td><?php echo c6_h($o['status']); ?></td><td><span class="badge bg-red"><?php echo $days; ?></span></td><td><?php echo c6_h($o['assignee']); ?></td></tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php } elseif ($c6v === 'k') { $km = $c6['km']; $s = $km['sum']; $b = $s['buckets']; $u = $km['upsell']; ?>
<div class="c6-tiles">
    <div class="row c6-tile-row">
        <?php
        echo c6_tile($m . 'kam/my-client', 'dash-kpi-col1', number_format($s['total']), 'My Clients', 'Onboarded, I am the account manager', 'fa-handshake-o');
        echo c6_tile($m . 'kam/my-client', 'dash-kpi-col2', number_format($b['d7']), 'Contacted in 7 Days', $b['d30'] . ' in 8 to 30 days', 'fa-phone');
        echo c6_tile($m . 'kam/my-client', 'dash-kpi-col3', number_format($b['older'] + $b['never']), 'Not Contacted 30+ Days', $b['never'] . ' never contacted', 'fa-exclamation-circle');
        echo c6_tile($m . 'kam/task-followup', 'dash-kpi-col4', number_format($s['tasks']), 'Open Client Tasks', $s['tasks_late'] . ' late', 'fa-check-square-o');
        ?>
    </div>
    <div class="row c6-tile-row">
        <?php
        echo c6_tile($m . 'orders/open', 'dash-kpi-col1', number_format($u['open']), 'Upsell Orders Open', 'On my existing clients', 'fa-level-up');
        echo c6_tile($m . 'orders/sales', 'dash-kpi-col2', number_format($u['sales_n']), 'Upsell Sales ' . date('M-y'), '<span class="c2-amt">' . $curr . ' ' . number_format($u['sales_amt'], 0) . '</span>', 'fa-check-circle');
        echo c6_tile($m . 'kam/my-client', 'dash-kpi-col3', number_format($u['one_service']), 'Clients With One Service', 'Room for more services', 'fa-plus-circle');
        echo c6_tile($m . 'orders/pending_sales_approval', 'dash-kpi-col4', number_format($km['wait']['onboarding'] + $km['wait']['approval']), 'Waiting on KAM', $km['wait']['onboarding'] . ' onboarding, ' . $km['wait']['approval'] . ' approval', 'fa-share');
        ?>
    </div>
</div>

<?php echo c6_divider('c6-clients', 'My Clients'); ?>
<?php if ($c6['own'] !== 'k') { ?><div class="c6-hint"><i class="fa fa-info-circle"></i> Each client shows only to its account manager, so this view lists your own clients. Company totals for KAM are on the Admin view.</div><?php } ?>
<div class="row c6-row c6-eq">
    <div class="col-md-4 col-xs-12">
        <div class="crm-modern-stat-card c5-card">
            <div class="card-top">
                <h4 class="card-title"><i class="fa fa-hourglass-half" style="color:#ea580c;"></i> Clients by Last Contact</h4>
                <span class="card-total-pill"><?php echo $s['total']; ?> clients</span>
            </div>
            <?php echo c6_bars(array(
                array('0 to 7 days', $b['d7'], '', '', 'c5-ag-ok'), array('8 to 30 days', $b['d30'], '', '', 'c5-ag-mid'),
                array('Over 30 days', $b['older'], '', '', 'c5-ag-old'), array('Never contacted', $b['never'], '', '', 'c5-ag-old'))); ?>
            <div class="c5-note">Last follow-up entry or call on each client.</div>
        </div>
    </div>
    <div class="col-md-8 col-xs-12">
        <div class="crm-modern-stat-card c5-card">
            <div class="card-top">
                <h4 class="card-title"><i class="fa fa-phone" style="color:#0f8a80;"></i> Clients to Contact</h4>
                <span class="card-total-pill">Longest without contact first</span>
            </div>
            <?php if (empty($km['list'])) { ?><div class="c5-empty">No clients yet</div><?php } else { ?>
            <div class="c6-tbl-wrap">
            <table class="c3-clo c6-tbl c6-list">
                <thead><tr><th>Client</th><th>Group</th><th>Last contact</th><th>Days</th><th>Next follow-up</th><th>Open tasks</th></tr></thead>
                <tbody>
                <?php foreach ($km['list'] as $c) { $dd = $c['days_since']; $cls = $dd === null || $dd > 30 ? 'bg-red' : ($dd > 7 ? 'bg-orange' : 'bg-green'); ?>
                    <tr><td><a href="<?php echo $m . 'kam/view/' . (int)$c['lead_id']; ?>" class="c6-name"><?php echo c6_h($c['client']); ?></a></td><td><span class="c6-name"><?php echo c6_h($c['group_name']); ?></span></td><td><?php echo c6_d($c['last_contact']); ?></td><td><span class="badge <?php echo $cls; ?>"><?php echo $dd === null ? 'Never' : $dd; ?></span></td><td><?php echo c6_d($c['followup_date']); ?></td><td><?php echo (int)$c['tasks_open']; ?><?php echo $c['tasks_late'] ? ' <span class="badge bg-red">' . (int)$c['tasks_late'] . ' late</span>' : ''; ?></td></tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php echo c6_divider('c6-upsell', 'Upselling and Client Tasks'); ?>
<div class="row c6-row c6-eq">
    <div class="col-md-4 col-xs-12">
        <div class="crm-modern-stat-card c5-card">
            <div class="card-top">
                <h4 class="card-title"><i class="fa fa-level-up" style="color:#16a34a;"></i> Upselling</h4>
                <span class="card-total-pill"><?php echo date('M-y'); ?></span>
            </div>
            <table class="c3-clo c6-tbl">
                <tbody>
                    <tr><td>Clients with a new order after onboarding</td><td><?php echo $u['clients']; ?></td></tr>
                    <tr><td>New orders still open</td><td><?php echo $u['open']; ?></td></tr>
                    <tr><td>New sales this month</td><td><?php echo $u['sales_n']; ?></td></tr>
                    <tr><td>Amount this month</td><td><span class="c2-amt"><?php echo $curr . ' ' . number_format($u['sales_amt'], 0); ?></span></td></tr>
                    <tr><td>Clients with only one service</td><td><span class="badge bg-blue<?php echo $u['one_service'] ? '' : ' c3-zero'; ?>"><?php echo $u['one_service']; ?></span></td></tr>
                </tbody>
            </table>
            <div class="c5-note">Example: a bookkeeping client who could also take audit.</div>
        </div>
    </div>
    <div class="col-md-4 col-xs-12">
        <div class="crm-modern-stat-card c5-card">
            <div class="card-top">
                <h4 class="card-title"><i class="fa fa-share" style="color:#0284c7;"></i> Waiting on KAM</h4>
                <span class="card-total-pill">From Back Office</span>
            </div>
            <table class="c3-clo c6-tbl">
                <tbody>
                    <tr><td><a href="<?php echo $m; ?>orders/open">Orders in Onboarding</a></td><td><span class="badge bg-blue<?php echo $km['wait']['onboarding'] ? '' : ' c3-zero'; ?>"><?php echo $km['wait']['onboarding']; ?></span></td></tr>
                    <tr><td><a href="<?php echo $m; ?>orders/pending_sales_approval">Waiting for sales approval</a></td><td><span class="badge bg-skyblue<?php echo $km['wait']['approval'] ? '' : ' c3-zero'; ?>"><?php echo $km['wait']['approval']; ?></span></td></tr>
                </tbody>
            </table>
            <div class="c5-note">Check the order and the client details, onboard the client, then approve the sale.</div>
        </div>
    </div>
    <div class="col-md-4 col-xs-12">
        <div class="crm-modern-stat-card c5-card">
            <div class="card-top">
                <h4 class="card-title"><i class="fa fa-check-square-o" style="color:#ea580c;"></i> Client Tasks</h4>
                <span class="card-total-pill"><?php echo $s['tasks']; ?> open</span>
            </div>
            <?php if (empty($km['tasks'])) { ?><div class="c5-empty">No open tasks on your clients</div><?php } else { ?>
            <table class="c3-clo c6-tbl c6-list">
                <tbody>
                <?php foreach ($km['tasks'] as $tk) { $late = $tk['followup_date'] >= '2000-01-01' && substr($tk['followup_date'], 0, 10) < date('Y-m-d'); ?>
                    <tr><td><a href="<?php echo $m; ?>kam/task-followup" class="c6-name"><?php echo c6_h($tk['customer'] ? $tk['customer'] : ($tk['task_id'] ? $tk['task_id'] : 'Task')); ?></a><div class="c6-sub"><?php echo c6_h($tk['category']); ?></div></td><td><span class="badge <?php echo $late ? 'bg-red' : 'bg-blue'; ?>"><?php echo c6_d($tk['followup_date']); ?></span></td></tr>
                <?php } ?>
                </tbody>
            </table>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>

<?php /* Follow-ups and meetings: on every department view. */
$c6_rows = array(
    'total_missed'   => array('All Missed', 'bg-red', 1),
    'total_lastweek' => array('Last 7 Days', 'bg-orange', 2),
    'total_today'    => array('Today', 'bg-skyblue', 3),
    'total_nextweek' => array('Next 7 Days', 'bg-blue', 4),
    'total_future'   => array('All Future', 'bg-green', 5),
);
$fd = $c6['fu_done'];
echo c6_divider('c6-track', 'Follow-ups and Meetings');
?>
<div class="row c5-team-row">
    <?php foreach (array(array('Follow-ups', 'fa-phone', '#0284c7', isset($dashboard_f) ? (array)$dashboard_f : array(), $c6['fu_team'], 'leads/followups', 'team/followups'), array('Meetings', 'fa-users', '#ea580c', isset($dashboard_m) ? (array)$dashboard_m : array(), $c6['m_team'], 'leads/meetings', 'team/meetings')) as $g) { ?>
    <div class="col-lg-3 col-sm-6 col-xs-12">
        <div class="crm-modern-stat-card c5-card">
            <div class="card-top">
                <h4 class="card-title"><i class="fa <?php echo $g[1]; ?>" style="color:<?php echo $g[2]; ?>;"></i> <?php echo $g[0]; ?></h4>
                <span class="card-total-pill">By date</span>
            </div>
            <table class="c3-clo">
                <thead><tr><th></th><th>My</th><th><?php echo $c6_who; ?></th></tr></thead>
                <tbody>
                <?php foreach ($c6_rows as $k => $r) { $my = isset($g[3][$k]) ? (int)$g[3][$k] : 0; $tm = isset($g[4][$k]) ? (int)$g[4][$k] : 0; ?>
                    <tr><td><?php echo $r[0]; ?></td><td><a href="<?php echo $m . $g[5] . '?type=' . $r[2]; ?>" class="badge <?php echo $r[1] . ($my === 0 ? ' c3-zero' : ''); ?>"><?php echo $my; ?></a></td><td><a href="<?php echo $m . $g[6] . '?type=' . $r[2]; ?>" class="badge <?php echo $r[1] . ($tm === 0 ? ' c3-zero' : ''); ?>"><?php echo $tm; ?></a></td></tr>
                <?php } ?>
                </tbody>
                <?php if ($g[0] === 'Follow-ups') { ?>
                <tfoot><tr><td>Logged today</td><td><?php echo (int)$fd['my_today']; ?></td><td><?php echo (int)$fd['team_today']; ?></td></tr></tfoot>
                <?php } ?>
            </table>
            <?php if ($g[0] === 'Follow-ups') { ?><div class="c5-note">Logged in the last 7 days: <?php echo (int)$fd['my_week']; ?> by me, <?php echo (int)$fd['team_week']; ?> by the team.</div><?php } ?>
        </div>
    </div>
    <?php } ?>
    <?php foreach (array(array("Today's List", 'fa-list-ul', '#dc2626', $c6['agenda_my'], $c6['agenda_team'], 'Nothing due today or missed this week'), array('Meetings Next 7 Days', 'fa-calendar', '#2563eb', $c6['meet_next'], $c6['meet_next_team'], 'No meetings in the next 7 days')) as $g) { ?>
    <div class="col-lg-3 col-sm-6 col-xs-12">
        <div class="crm-modern-stat-card c5-card c6-agenda">
            <div class="card-top">
                <h4 class="card-title"><i class="fa <?php echo $g[1]; ?>" style="color:<?php echo $g[2]; ?>;"></i> <?php echo $g[0]; ?></h4>
                <span class="c5-scope c6-scope" role="group" aria-label="Whose list"><button type="button" data-scope="my" class="is-on" aria-pressed="true">My</button><button type="button" data-scope="team" aria-pressed="false"><?php echo $c6_who; ?></button></span>
            </div>
            <?php foreach (array('my' => $g[3], 'team' => $g[4]) as $sk => $list) { ?>
            <div class="c6-ag-list" data-scope="<?php echo $sk; ?>"<?php echo $sk === 'team' ? ' hidden' : ''; ?>>
                <?php if (empty($list)) { ?><div class="c5-empty"><?php echo $g[5]; ?></div><?php } else { ?>
                <ul class="c6-items">
                    <?php foreach ($list as $it) { $kind = isset($it['kind']) ? $it['kind'] : 'mt'; $late = !empty($it['late']); ?>
                    <li class="<?php echo $late ? 'is-late' : ''; ?>">
                        <i class="fa <?php echo $kind === 'fu' ? 'fa-phone' : 'fa-users'; ?> c6-k-<?php echo $kind; ?>" title="<?php echo $kind === 'fu' ? 'Follow-up' : 'Meeting'; ?>"></i>
                        <div class="c6-it-main">
                            <a href="<?php echo c6_lead($it['id']); ?>" class="c6-name"><?php echo c6_h($it['customer'] ? $it['customer'] : ($it['poc'] ? $it['poc'] : $it['lead_id'])); ?></a>
                            <div class="c6-sub"><?php echo c6_h(trim(($sk === 'team' && !empty($it['owner']) ? $it['owner'] : '') . ($it['poc'] && $it['customer'] && $sk !== 'team' ? $it['poc'] : ''))); ?></div>
                        </div>
                        <span class="c6-when"><?php echo $it['d'] === date('Y-m-d') ? ($it['tm'] ? c6_h(substr($it['tm'], 0, 5)) : 'Today') : c6_d($it['d']); ?><?php echo $late ? '<em>Missed</em>' : ''; ?></span>
                    </li>
                    <?php } ?>
                </ul>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
</div>
