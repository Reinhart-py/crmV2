<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
/* Modern 6: a short version of Classic 6. Same views and rights (Admin, Sales, Back Office, KAM), one screen each:
   four tiles and six cards. Data from dashboard6_model::build_short(). */
$v = $m6['view'];
$m = base_url() . 'manager/';
$curr = !empty($user_currency) ? $user_currency : 'AED';
$who = !empty($m6['is_all']) ? 'All' : 'Team';
$e = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$amt = function ($n) use ($curr) { return '<span class="m6-amt">' . $curr . ' ' . number_format((float)$n, 0) . '</span>'; };
$dd = function ($d) { $t = strtotime((string)$d); return ($d && $t && $d >= '2000-01-01') ? date('d M', $t) : '-'; };
$plural = function ($n, $one, $many) { return (int)$n . ' ' . ((int)$n === 1 ? $one : $many); };
$lead = function ($id) { return base_url() . 'manager/leads/view/' . base64_encode($id); };
$names = $m6['names'];
$titles = array('a' => 'Whole company', 's' => 'My sales', 'b' => 'Order processing', 'k' => 'My clients');

/* ---- small builders ---- */
$tile = function ($href, $tone, $value, $label, $sub, $icon) {
    return '<a href="' . $href . '" class="m6-tile m6-t-' . $tone . '"><span class="m6-tile-ic"><i class="fa ' . $icon . '"></i></span><b>' . $value . '</b><span class="m6-tile-l">' . $label . '</span><span class="m6-tile-s">' . $sub . '</span></a>';
};
$card = function ($title, $icon, $color, $pill, $body, $note = '') {
    return '<section class="m6-card" style="--m6-c:' . $color . ';"><header><h4><i class="fa ' . $icon . '"></i> ' . $title . '</h4>' . ($pill === '' ? '' : (substr($pill, 0, 1) === '<' ? $pill : '<span class="m6-pill">' . $pill . '</span>')) . '</header><div class="m6-body">' . $body . '</div>' . ($note !== '' ? '<p class="m6-note">' . $note . '</p>' : '') . '</section>';
};
$bars = function ($rows) use ($e) {
    $max = 0; foreach ($rows as $r) $max = max($max, (int)$r[1]);
    $h = '';
    foreach ($rows as $r) {
        $w = $max > 0 && (int)$r[1] > 0 ? max(3, (int)round($r[1] / $max * 100)) : 0;
        $lab = !empty($r[3]) ? '<a href="' . $r[3] . '">' . $e($r[0]) . '</a>' : $e($r[0]);
        $h .= '<div class="m6-row m6-' . (isset($r[4]) ? $r[4] : 'blue') . '"><div><span>' . $lab . '</span><span><b>' . (int)$r[1] . '</b>' . (!empty($r[2]) ? ' <em>' . $r[2] . '</em>' : '') . '</span></div><i><u style="width:' . $w . '%"></u></i></div>';
    }
    return $h;
};
$empty = function ($t) { return '<div class="m6-empty">' . $t . '</div>'; };
$badge = function ($n, $tone, $href = '') { $c = 'm6-b m6-b-' . ($n ? $tone : 'zero'); return $href !== '' ? '<a href="' . $href . '" class="' . $c . '">' . (int)$n . '</a>' : '<span class="' . $c . '">' . (int)$n . '</span>'; };

/* Follow-ups and meetings: one small table, My and Team, missed / today / next 7 days. */
$fu_card = function () use ($m6, $m, $who, $badge, $card) {
    $rows = array(array('Missed', 'total_missed', 'red', 1), array('Today', 'total_today', 'sky', 3), array('Next 7 days', 'total_nextweek', 'blue', 4));
    $h = '<table class="m6-tbl m6-fm"><thead><tr><th></th><th colspan="2">Follow-ups</th><th colspan="2">Meetings</th></tr><tr><th></th><th>My</th><th>' . $who . '</th><th>My</th><th>' . $who . '</th></tr></thead><tbody>';
    foreach ($rows as $r) {
        $h .= '<tr><td>' . $r[0] . '</td>'
            . '<td>' . $badge(isset($m6['fu_my'][$r[1]]) ? (int)$m6['fu_my'][$r[1]] : 0, $r[2], $m . 'leads/followups?type=' . $r[3]) . '</td>'
            . '<td>' . $badge(isset($m6['fu_team'][$r[1]]) ? (int)$m6['fu_team'][$r[1]] : 0, $r[2], $m . 'team/followups?type=' . $r[3]) . '</td>'
            . '<td>' . $badge(isset($m6['m_my'][$r[1]]) ? (int)$m6['m_my'][$r[1]] : 0, $r[2], $m . 'leads/meetings?type=' . $r[3]) . '</td>'
            . '<td>' . $badge(isset($m6['m_team'][$r[1]]) ? (int)$m6['m_team'][$r[1]] : 0, $r[2], $m . 'team/meetings?type=' . $r[3]) . '</td></tr>';
    }
    $h .= '</tbody></table>';
    $fd = $m6['fu_done'];
    return $card('Follow-ups and Meetings', 'fa-calendar-check-o', '#0284c7', 'By date', $h, 'Follow-ups logged today: ' . (int)$fd['my_today'] . ' by me, ' . (int)$fd['team_today'] . ' by the ' . strtolower($who === 'All' ? 'company' : 'team') . '.');
};
/* Today's list: due today and missed this week, My / Team. */
$today_card = function () use ($m6, $who, $e, $dd, $lead, $card) {
    $sw = '<span class="m6-scope" role="group" aria-label="Whose list"><button type="button" data-scope="my" class="is-on" aria-pressed="true">My</button><button type="button" data-scope="team" aria-pressed="false">' . $who . '</button></span>';
    $h = '';
    foreach (array('my' => $m6['agenda_my'], 'team' => $m6['agenda_team']) as $sk => $list) {
        $h .= '<div class="m6-list" data-scope="' . $sk . '"' . ($sk === 'team' ? ' hidden' : '') . '>';
        if (empty($list)) { $h .= '<div class="m6-empty">Nothing due today or missed this week</div>'; }
        else {
            $h .= '<ul class="m6-items">';
            foreach ($list as $it) {
                $late = !empty($it['late']); $kind = $it['kind'];
                $name = $it['customer'] ? $it['customer'] : ($it['poc'] ? $it['poc'] : $it['lead_id']);
                $h .= '<li class="' . ($late ? 'is-late' : '') . '"><i class="fa ' . ($kind === 'fu' ? 'fa-phone' : 'fa-users') . ' m6-k-' . $kind . '" title="' . ($kind === 'fu' ? 'Follow-up' : 'Meeting') . '"></i>'
                    . '<div><a href="' . $lead($it['id']) . '">' . $e($name) . '</a><small>' . $e($sk === 'team' ? $it['owner'] : ($kind === 'fu' ? 'Follow-up' : 'Meeting')) . '</small></div>'
                    . '<span class="m6-when">' . ($it['d'] === date('Y-m-d') ? ($it['tm'] ? $e(substr($it['tm'], 0, 5)) : 'Today') : $dd($it['d'])) . ($late ? '<em>Missed</em>' : '') . '</span></li>';
            }
            $h .= '</ul>';
        }
        $h .= '</div>';
    }
    return ($card("Today's List", 'fa-list-ul', '#dc2626', $sw, $h));
};

$tiles = ''; $cards = array();
if ($v === 's') {
    $sa = $m6['sa'];
    $pct = $sa['target'] > 0 ? (int)round($sa['achieved'] / $sa['target'] * 100) : null;
    $tiles = $tile($m . 'leads/view_leads', 'navy', number_format($sa['leads']), 'My Leads', number_format($sa['db']) . ' in my database', 'fa-bullseye')
        . $tile($m . 'orders/my', 'navy', number_format($sa['orders']), 'My Orders', 'Open and closed', 'fa-shopping-cart')
        . $tile($m . 'orders/sales', 'teal', $amt($sa['sales_amt']), 'My Sales', 'All time', 'fa-check-circle')
        . $tile($m . 'hr/targets', 'teal', $pct === null ? '-' : $pct . '%', 'Target ' . date('M-y'), $amt($sa['achieved']) . ' of ' . $amt($sa['target']), 'fa-crosshairs');
    $p = $sa['pipe'];
    $steps = array('database' => array('Database', 'manager/database', 'navy'), 'leads' => array('Leads', 'manager/leads/view_leads', 'blue'), 'orders' => array('Open orders', 'manager/orders/open', 'teal'), 'sales' => array('Sales', 'manager/orders/sales', 'green'));
    $rows = array();
    foreach ($steps as $k => $s) $rows[] = array($s[0], isset($p[$k]['count']) ? (int)$p[$k]['count'] : 0, isset($p[$k]['value']) ? $amt($p[$k]['value']) : '', base_url() . $s[1], $s[2]);
    $cards[] = $card('My Pipeline', 'fa-filter', '#0072f0', 'Now', $bars($rows));
    $cards[] = $fu_card();
    $cards[] = $today_card();
    if (empty($sa['top'])) $top = $empty('No achievement yet this month');
    else { $rows = array(); foreach ($sa['top'] as $i => $r) $rows[] = array(($i + 1) . '. ' . $r['name'], (int)round($r['achieved']), $r['target'] > 0 ? (int)round($r['achieved'] / $r['target'] * 100) . '%' : '', '', 'violet'); $top = $bars($rows); }
    $cards[] = $card('Top This Month', 'fa-trophy', '#d97706', date('M-y'), $top, 'Amount achieved (' . $curr . ') and % of target.');
    $ag = $sa['ageing'];
    $cards[] = $card('Leads by Last Contact', 'fa-hourglass-half', '#ea580c', $plural($ag['d7'] + $ag['d30'] + $ag['older'], 'open', 'open'), $bars(array(array('0 to 7 days', $ag['d7'], '', '', 'green'), array('8 to 30 days', $ag['d30'], '', '', 'orange'), array('Over 30 days', $ag['older'], '', '', 'red'))));
    $cl = $sa['calls'];
    $h = '<table class="m6-tbl"><thead><tr><th></th><th>My</th><th>' . $who . '</th></tr></thead><tbody>';
    foreach (array(array('Made', 'total'), array('Answered', 'answered'), array('Not answered', 'not_answered')) as $r) $h .= '<tr><td>' . $r[0] . '</td><td>' . (int)$cl['my'][$r[1]] . '</td><td>' . (int)$cl['team'][$r[1]] . '</td></tr>';
    $h .= '</tbody></table>';
    $cards[] = $card('Calls Today', 'fa-phone', '#0f8a80', date('d M'), $h);
} elseif ($v === 'a') {
    $co = $m6['co']; $t = $co['tiles'];
    $pct = $t['target'] > 0 ? (int)round($t['sales_amt'] / $t['target'] * 100) . '% of target' : 'No target set';
    $tiles = $tile($m . 'users', 'navy', number_format($t['staff']), 'Active Staff', $t['online'] . ' online now', 'fa-users')
        . $tile($m . 'leads/view_leads', 'navy', number_format($t['leads']), 'Open Leads', $t['fu_missed'] . ' missed follow-ups', 'fa-bullseye')
        . $tile($m . 'orders/open', 'teal', number_format($t['open']), 'Open Orders', $t['late'] . ' not moved for 3+ days', 'fa-shopping-cart')
        . $tile($m . 'orders/sales', 'teal', $amt($t['sales_amt']), 'Sales ' . $co['month'], $t['sales_n'] . ' sales, ' . $pct, 'fa-check-circle');
    $h = '<table class="m6-tbl"><thead><tr><th>Branch</th><th>Staff</th><th>Leads</th><th>Missed</th><th>Sales</th></tr></thead><tbody>';
    foreach ($co['branch'] as $b) $h .= '<tr><td title="' . $e($b['label']) . '">' . $e($b['label']) . '</td><td>' . $b['staff'] . '</td><td>' . number_format($b['leads']) . '</td><td>' . $badge($b['fu_missed'], 'red') . '</td><td>' . $amt($b['sales_amt']) . '</td></tr>';
    $h .= '</tbody></table>';
    $cards[] = $card('By Branch', 'fa-building-o', '#0891b2', 'Sales ' . $co['month'], $h, 'Missed = follow-ups past their date.');
    $h = '<table class="m6-tbl"><thead><tr><th>Department</th><th>Staff</th><th>Leads</th><th>Missed</th><th>Sales</th></tr></thead><tbody>';
    foreach ($co['dept'] as $d) $h .= '<tr><td>' . $e($d['label']) . '</td><td>' . $d['staff'] . '</td><td>' . number_format($d['leads']) . '</td><td>' . $badge($d['fu_missed'], 'red') . '</td><td>' . $amt($d['sales_amt']) . '</td></tr>';
    $h .= '</tbody></table>';
    $cards[] = $card('By Department', 'fa-sitemap', '#0f8a80', 'Role of each user', $h);
    if (empty($co['track'])) $h = $empty('Nobody has missed follow-ups or late tasks');
    else { $h = '<table class="m6-tbl"><thead><tr><th>Name</th><th>Missed</th><th>Late tasks</th><th>Calls</th></tr></thead><tbody>'; foreach (array_slice($co['track'], 0, 6) as $p) $h .= '<tr><td>' . $e($p['name']) . '</td><td>' . $badge($p['fu_overdue'], 'red') . '</td><td>' . $badge($p['tasks_late'], 'orange') . '</td><td>' . (int)$p['calls'] . '</td></tr>'; $h .= '</tbody></table>'; }
    $cards[] = $card('People to Check', 'fa-user-circle-o', '#dc2626', 'Most behind first', $h);
    if (empty($co['category'])) $h = $empty('No orders yet');
    else { $h = '<table class="m6-tbl"><thead><tr><th>Category</th><th>Orders</th><th>Open</th><th>Sales ' . $co['month'] . '</th></tr></thead><tbody>'; foreach ($co['category'] as $c) $h .= '<tr><td>' . $e($c['label']) . '</td><td>' . (int)$c['n'] . '</td><td>' . (int)$c['open_n'] . '</td><td>' . $amt($c['sales_amt']) . '</td></tr>'; $h .= '</tbody></table>'; }
    $cards[] = $card('By Business Category', 'fa-tags', '#7c3aed', 'Vertical on the order', $h);
    $cards[] = $fu_card();
    $cards[] = $today_card();
} elseif ($v === 'b') {
    $bo = $m6['bo']; $t = $bo['tiles'];
    $tiles = $tile($m . 'orders/open', 'navy', number_format($t['open']), 'Open Orders', $t['mine'] . ' assigned to me', 'fa-shopping-cart')
        . $tile($m . 'orders/open', 'navy', number_format($t['late']), 'Not Moved 3+ Days', 'Need a push', 'fa-hourglass-half')
        . $tile($m . 'back-office', 'teal', number_format($t['reviews']), 'Waiting for Review', $bo['docs']['orders'] . ' with documents pending', 'fa-check-square-o')
        . $tile($m . 'orders/sales', 'teal', number_format($t['done']['sales_month']), 'Completed ' . date('M-y'), $t['done']['sales_today'] . ' today, ' . $t['done']['new_today'] . ' new today', 'fa-check-circle');
    if (empty($bo['flow'])) $h = $empty('No open orders');
    else { $rows = array(); foreach ($bo['flow'] as $f) $rows[] = array($f['status'], (int)$f['n'], (int)$f['late'] ? (int)$f['late'] . ' late' : '', $m . 'orders/open', (int)$f['late'] ? 'red' : 'green'); $h = $bars($rows); }
    $cards[] = $card('Orders by Stage', 'fa-tasks', '#ea580c', $plural($t['open'], 'open', 'open'), $h, 'Late = no change for more than 3 days.');
    if (empty($bo['late_list'])) $h = $empty('No order is waiting more than 3 days');
    else { $h = '<table class="m6-tbl"><thead><tr><th>Order</th><th>Status</th><th>Days</th><th>Assigned</th></tr></thead><tbody>'; foreach (array_slice($bo['late_list'], 0, 6) as $o) $h .= '<tr><td><a href="' . $m . 'orders/view/' . (int)$o['id'] . '" title="' . $e($o['client_name'] ? $o['client_name'] : $o['company_name']) . '">' . $e($o['order_no'] ? $o['order_no'] : '#' . $o['id']) . '</a></td><td>' . $e($o['status']) . '</td><td>' . $badge((int)floor((time() - strtotime($o['moved_at'])) / 86400), 'red') . '</td><td>' . $e($o['assignee']) . '</td></tr>'; $h .= '</tbody></table>'; }
    $cards[] = $card('Orders Not Moved', 'fa-hourglass-half', '#dc2626', 'Oldest first', $h);
    $cards[] = $today_card();
    $h = '<table class="m6-tbl">';
    foreach ($bo['reviews'] as $r) $h .= '<tr><td><a href="' . base_url() . $r['url'] . '">' . $e($r['label']) . '</a></td><td>' . $badge($r['n'], 'sky') . '</td></tr>';
    $h .= '<tr><td><a href="' . $m . 'orders/open">Orders in Onboarding (with KAM)</a></td><td>' . $badge($bo['hand']['onboarding'], 'blue') . '</td></tr></table>';
    $cards[] = $card('Waiting for Review', 'fa-check-square-o', '#0284c7', $t['reviews'] . ' waiting', $h);
    if (empty($bo['docs']['list'])) $h = $empty('All documents collected');
    else { $h = '<table class="m6-tbl"><thead><tr><th>Order</th><th>To collect</th></tr></thead><tbody>'; foreach (array_slice($bo['docs']['list'], 0, 6) as $o) $h .= '<tr><td><a href="' . $m . 'orders/view/' . $o['id'] . '">' . $e($o['order_no'] ? $o['order_no'] : '#' . $o['id']) . '</a></td><td title="' . $e(implode(', ', $o['pending'])) . '">' . $badge(count($o['pending']), 'orange') . ' of ' . $o['total'] . '</td></tr>'; $h .= '</tbody></table>'; }
    $cards[] = $card('Documents Pending', 'fa-file-text-o', '#0891b2', $plural($bo['docs']['orders'], 'order', 'orders'), $h);
    $cards[] = $fu_card();
} else {
    $km = $m6['km']; $s = $km['sum']; $b = $s['buckets']; $u = $km['upsell'];
    $tiles = $tile($m . 'kam/my-client', 'navy', number_format($s['total']), 'My Clients', $b['d7'] . ' contacted in 7 days', 'fa-handshake-o')
        . $tile($m . 'kam/my-client', 'navy', number_format($b['older'] + $b['never']), 'Not Contacted 30+ Days', $b['never'] . ' never contacted', 'fa-exclamation-circle')
        . $tile($m . 'orders/sales', 'teal', $amt($u['sales_amt']), 'Upselling ' . date('M-y'), $u['sales_n'] . ' sales, ' . $u['open'] . ' open', 'fa-level-up')
        . $tile($m . 'orders/pending_sales_approval', 'teal', number_format($km['wait']['onboarding'] + $km['wait']['approval']), 'Waiting on KAM', $km['wait']['onboarding'] . ' onboarding, ' . $km['wait']['approval'] . ' approval', 'fa-share');
    $cards[] = $card('Clients by Last Contact', 'fa-hourglass-half', '#ea580c', $plural($s['total'], 'client', 'clients'), $bars(array(array('0 to 7 days', $b['d7'], '', '', 'green'), array('8 to 30 days', $b['d30'], '', '', 'orange'), array('Over 30 days', $b['older'], '', '', 'red'), array('Never contacted', $b['never'], '', '', 'red'))));
    if (empty($km['list'])) $h = $empty('No clients yet');
    else { $h = '<table class="m6-tbl"><thead><tr><th>Client</th><th>Last contact</th><th>Days</th></tr></thead><tbody>'; foreach (array_slice($km['list'], 0, 6) as $c) { $d = $c['days_since']; $h .= '<tr><td><a href="' . $m . 'kam/view/' . (int)$c['lead_id'] . '" title="' . $e($c['client']) . '">' . $e($c['client']) . '</a></td><td>' . $dd($c['last_contact']) . '</td><td>' . ($d === null ? '<span class="m6-b m6-b-red">Never</span>' : $badge($d, $d > 30 ? 'red' : ($d > 7 ? 'orange' : 'green'))) . '</td></tr>'; } $h .= '</tbody></table>'; }
    $cards[] = $card('Clients to Contact', 'fa-phone', '#0f8a80', 'Longest gap first', $h);
    $cards[] = $today_card();
    $h = '<table class="m6-tbl"><tr><td>Clients with a new order after onboarding</td><td>' . $u['clients'] . '</td></tr><tr><td>New orders still open</td><td>' . $u['open'] . '</td></tr><tr><td>Clients with only one service</td><td>' . $badge($u['one_service'], 'blue') . '</td></tr><tr><td>Open client tasks</td><td>' . $s['tasks'] . ($s['tasks_late'] ? ' ' . $badge($s['tasks_late'], 'red') : '') . '</td></tr></table>';
    $cards[] = $card('Upselling', 'fa-level-up', '#16a34a', date('M-y'), $h);
    $h = '<table class="m6-tbl"><tr><td><a href="' . $m . 'orders/open">Orders in Onboarding</a></td><td>' . $badge($km['wait']['onboarding'], 'blue') . '</td></tr><tr><td><a href="' . $m . 'orders/pending_sales_approval">Waiting for sales approval</a></td><td>' . $badge($km['wait']['approval'], 'sky') . '</td></tr></table>';
    $cards[] = $card('Waiting on KAM', 'fa-share', '#0284c7', 'From Back Office', $h, 'Check the order and client details, onboard the client, then approve the sale.');
    $cards[] = $fu_card();
}
?>
<div class="content-wrapper m6" id="m6Root">
    <div class="m6-bar">
        <?php if (count($m6['views']) > 1) { ?>
        <nav class="m6-views" aria-label="Dashboard view">
            <?php foreach ($m6['views'] as $vk) { ?><a href="<?php echo $m; ?>dashboard?view=<?php echo $vk; ?>" class="<?php echo $vk === $v ? 'is-on' : ''; ?>"<?php echo $vk === $v ? ' aria-current="page"' : ''; ?>><?php echo $vk === $v ? '<i class="fa fa-check"></i> ' : ''; ?><?php echo $names[$vk]; ?></a><?php } ?>
        </nav>
        <?php } ?>
        <h2 class="m6-title"><?php echo $names[$v]; ?> <span><?php echo $titles[$v] . ' &middot; ' . date('D d M'); ?></span></h2>
        <span class="m6-upd">Updated <?php echo date('H:i'); ?></span>
        <button type="button" class="m6-btn" id="m6Reload" title="Reload" aria-label="Reload"><i class="fa fa-refresh"></i></button>
        <button type="button" class="m6-btn" id="m6Hide" aria-pressed="false" title="Hide amounts (for screen sharing)"><i class="fa fa-eye-slash"></i> <span>Hide amounts</span></button>
    </div>
    <div class="m6-tiles"><?php echo $tiles; ?></div>
    <div class="m6-grid"><?php echo implode('', $cards); ?></div>
</div>

<script>
$(function () {
    var $r = $('#m6Root');
    function store(k, v) { try { if (v === undefined) return localStorage.getItem(k); localStorage.setItem(k, v); } catch (e) { return null; } }
    function setHide(on) {
        $r.toggleClass('m6-hidden', on);
        $('#m6Hide').attr('aria-pressed', on ? 'true' : 'false').find('span').text(on ? 'Show amounts' : 'Hide amounts');
        $('#m6Hide i').attr('class', 'fa ' + (on ? 'fa-eye' : 'fa-eye-slash'));
        store('crm_c2_hide_amt', on ? '1' : '0');
    }
    $('#m6Hide').on('click', function () { setHide(!$r.hasClass('m6-hidden')); });
    setHide(store('crm_c2_hide_amt') === '1');
    // Keep the bar just under the top header when that header stays on screen.
    function setTop() {
        var $hd = $('.main-header').first(), pos = $hd.css('position');
        $r[0].style.setProperty('--m6-top', ((pos === 'fixed' || pos === 'sticky') && $hd[0] ? $hd[0].offsetHeight : 0) + 'px');
    }
    setTop(); $(window).on('resize', setTop);
    $('#m6Reload').on('click', function () { $(this).find('i').addClass('fa-spin'); location.reload(); });
    function setScope(sc) {
        $r.find('.m6-scope button').each(function () { var on = $(this).data('scope') === sc; $(this).toggleClass('is-on', on).attr('aria-pressed', on ? 'true' : 'false'); });
        $r.find('.m6-list').each(function () { this.hidden = $(this).data('scope') !== sc; });
    }
    $r.on('click', '.m6-scope button', function () { var sc = $(this).data('scope'); setScope(sc); store('crm_c6_list_scope', sc); });
    if (store('crm_c6_list_scope') === 'team') setScope('team');
});
</script>
<style>
.m6 { --m6-bg: #f4f6fa; --m6-card: #ffffff; --m6-line: #e2e8f0; --m6-soft: #eef2f7; --m6-ink: #0f172a; --m6-ink2: #334155; --m6-mute: #64748b; --m6-link: #1d4ed8; padding: 10px 14px 18px !important; background: var(--m6-bg); }
[data-theme="dark"] .m6 { --m6-bg: #0b1220; --m6-card: #111827; --m6-line: #1f2937; --m6-soft: #1f2937; --m6-ink: #f1f5f9; --m6-ink2: #cbd5e1; --m6-mute: #94a3b8; --m6-link: #93c5fd; }
.m6 .m6-bar { position: sticky; top: var(--m6-top, 0px); z-index: 20; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 10px; padding: 6px 8px; background: var(--m6-card); border: 1px solid var(--m6-line); border-radius: 10px; }
.m6 .m6-views { display: inline-flex; gap: 2px; padding: 2px; border: 1px solid var(--m6-line); border-radius: 8px; background: var(--m6-bg); }
.m6 .m6-views a { padding: 3px 12px; border-radius: 6px; font-size: 12.5px; font-weight: 700; color: var(--m6-ink2); text-decoration: none; white-space: nowrap; }
.m6 .m6-views a:hover { background: var(--m6-soft); color: var(--m6-ink); }
.m6 .m6-views a.is-on { background: #0f766e; color: #fff; box-shadow: 0 1px 4px rgba(15, 118, 110, .45); }
.m6 .m6-views a.is-on i { font-size: 10px; margin-right: 2px; }
[data-theme="dark"] .m6 .m6-views a.is-on { background: #14b8a6; color: #04201d; box-shadow: 0 1px 6px rgba(20, 184, 166, .5); }
.m6 .m6-title { flex: 1; min-width: 0; margin: 0; font-size: 16px; font-weight: 800; color: var(--m6-ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.m6 .m6-title span { margin-left: 6px; font-size: 12px; font-weight: 600; color: var(--m6-mute); }
.m6 .m6-upd { font-size: 11px; font-weight: 600; color: var(--m6-mute); white-space: nowrap; }
.m6 .m6-btn { height: 26px; padding: 0 9px; border: 1px solid var(--m6-line); border-radius: 6px; background: var(--m6-card); color: var(--m6-ink2); font-size: 12px; font-weight: 600; cursor: pointer; white-space: nowrap; }
.m6 .m6-btn:hover { background: var(--m6-soft); }

.m6 .m6-tiles { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; margin-bottom: 10px; }
.m6 .m6-tile { position: relative; display: flex; flex-direction: column; min-width: 0; padding: 10px 14px; border-radius: 10px; color: #fff !important; text-decoration: none !important; overflow: hidden; transition: transform .15s ease, box-shadow .15s ease; }
.m6 .m6-tile:hover { transform: translateY(-2px); }
.m6 .m6-t-navy { background: linear-gradient(135deg, rgba(49, 82, 168, .66), rgba(36, 60, 128, .88)), #253c80 url("<?php echo base_url(); ?>assets/background-auth.jpg") 30% 40% / 460px auto no-repeat; box-shadow: inset 0 1px 0 rgba(255, 255, 255, .22), 0 4px 12px rgba(36, 60, 128, .2); }
.m6 .m6-t-teal { background: linear-gradient(135deg, rgba(28, 140, 131, .66), rgba(20, 110, 104, .88)), #146e68 url("<?php echo base_url(); ?>assets/background-auth.jpg") 70% 60% / 460px auto no-repeat; box-shadow: inset 0 1px 0 rgba(255, 255, 255, .22), 0 4px 12px rgba(20, 110, 104, .2); }
.m6 .m6-tile b { font-size: 22px; font-weight: 800; line-height: 1.15; }
.m6 .m6-tile-l { font-size: 13px; font-weight: 700; }
.m6 .m6-tile-s { font-size: 11px; opacity: .9; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.m6 .m6-tile-ic { position: absolute; right: 12px; top: 10px; font-size: 22px; opacity: .35; }

.m6 .m6-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; }
.m6 .m6-card { display: flex; flex-direction: column; min-width: 0; padding: 12px 14px; background: var(--m6-card); border: 1px solid var(--m6-line); border-top: 3px solid var(--m6-c, #0084ff); border-radius: 10px; box-shadow: 0 1px 3px rgba(0, 0, 0, .04); }
.m6 .m6-card header { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 8px; }
.m6 .m6-card h4 { margin: 0; min-width: 0; font-size: 14px; font-weight: 800; color: var(--m6-ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.m6 .m6-card h4 i { color: var(--m6-c); margin-right: 3px; }
.m6 .m6-pill { flex: none; padding: 1px 9px; border-radius: 999px; font-size: 11px; font-weight: 700; color: #0369a1; background: #e0f2fe; border: 1px solid #bae6fd; white-space: nowrap; }
[data-theme="dark"] .m6 .m6-pill { color: #7dd3fc; background: rgba(14, 165, 233, .12); border-color: rgba(56, 189, 248, .3); }
.m6 .m6-body { flex: 1; max-height: 240px; overflow: auto; }
.m6 .m6-note { margin: 8px 0 0; font-size: 11px; color: var(--m6-mute); }
.m6 .m6-empty { display: flex; align-items: center; justify-content: center; min-height: 110px; font-size: 12px; font-weight: 600; color: var(--m6-mute); text-align: center; }

.m6 .m6-tbl { width: 100%; border-collapse: collapse; font-size: 12px; }
.m6 .m6-tbl th { padding: 0 4px 5px; font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; color: var(--m6-mute); text-align: center; white-space: nowrap; }
.m6 .m6-tbl th:first-child { text-align: left; }
.m6 .m6-tbl td { padding: 5px 4px; border-top: 1px solid var(--m6-soft); color: var(--m6-ink2); font-weight: 600; text-align: center; white-space: nowrap; }
.m6 .m6-tbl td:first-child { text-align: left; max-width: 170px; overflow: hidden; text-overflow: ellipsis; }
.m6 .m6-tbl tr:first-child td { border-top: 0; }
.m6 .m6-tbl thead + tbody tr:first-child td { border-top: 1px solid var(--m6-soft); }
.m6 .m6-tbl a { color: var(--m6-link); font-weight: 700; text-decoration: none; }
.m6 .m6-tbl a:hover { text-decoration: underline; }
.m6 .m6-fm th[colspan] { padding-bottom: 0; }

.m6 .m6-b { display: inline-block; min-width: 24px; padding: 1px 7px; border-radius: 999px; font-size: 11.5px; font-weight: 800; line-height: 17px; text-align: center; color: #fff !important; text-decoration: none !important; }
.m6 .m6-b-red { background: #dc2626; } .m6 .m6-b-orange { background: #ea580c; } .m6 .m6-b-sky { background: #0284c7; }
.m6 .m6-b-blue { background: #2563eb; } .m6 .m6-b-green { background: #15803d; }
.m6 .m6-b-zero { background: var(--m6-soft); color: var(--m6-mute) !important; }

.m6 .m6-row { padding: 5px 0; }
.m6 .m6-row > div { display: flex; justify-content: space-between; gap: 8px; font-size: 12px; font-weight: 600; color: var(--m6-ink2); }
.m6 .m6-row > div span:first-child { min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.m6 .m6-row > div span:last-child { flex: none; }
.m6 .m6-row b { color: var(--m6-ink); font-size: 13px; }
.m6 .m6-row em { font-style: normal; font-size: 10.5px; color: var(--m6-mute); margin-left: 4px; }
.m6 .m6-row a { color: inherit; text-decoration: none; }
.m6 .m6-row a:hover { text-decoration: underline; }
.m6 .m6-row i { display: block; height: 5px; margin-top: 4px; border-radius: 999px; background: var(--m6-soft); overflow: hidden; }
.m6 .m6-row u { display: block; height: 100%; border-radius: 999px; text-decoration: none; background: #2563eb; }
.m6 .m6-navy u { background: #1e3a8a; } .m6 .m6-teal u { background: #0f8a80; } .m6 .m6-green u { background: #16a34a; }
.m6 .m6-orange u { background: #f97316; } .m6 .m6-red u { background: #dc2626; } .m6 .m6-violet u { background: #7c3aed; }
[data-theme="dark"] .m6 .m6-navy u { background: #3b82f6; }

.m6 .m6-scope { flex: none; display: inline-flex; padding: 2px; border: 1px solid var(--m6-line); border-radius: 999px; background: var(--m6-card); }
.m6 .m6-scope button { height: 20px; padding: 0 10px; border: 0; border-radius: 999px; background: transparent; color: var(--m6-ink2); font-size: 11px; font-weight: 700; cursor: pointer; }
.m6 .m6-scope button.is-on { background: #0072f0; color: #fff; }
.m6 .m6-list[hidden] { display: none !important; }
.m6 .m6-items { list-style: none; margin: 0; padding: 0; }
.m6 .m6-items li { display: flex; align-items: center; gap: 8px; padding: 5px 0; border-top: 1px solid var(--m6-soft); }
.m6 .m6-items li:first-child { border-top: 0; }
.m6 .m6-items li > i { flex: none; width: 22px; height: 22px; border-radius: 50%; text-align: center; line-height: 22px; font-size: 11px; color: #fff; }
.m6 .m6-k-fu { background: #0284c7; } .m6 .m6-k-mt { background: #ea580c; } .m6 .m6-items li.is-late > i { background: #dc2626; }
.m6 .m6-items li > div { flex: 1; min-width: 0; }
.m6 .m6-items a { display: block; font-size: 12px; font-weight: 700; color: var(--m6-ink); text-decoration: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.m6 .m6-items small { display: block; font-size: 10.5px; color: var(--m6-mute); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.m6 .m6-when { flex: none; text-align: right; font-size: 11px; font-weight: 700; color: var(--m6-ink2); }
.m6 .m6-when em { display: block; font-style: normal; font-size: 9.5px; font-weight: 800; color: #dc2626; text-transform: uppercase; }

.m6.m6-hidden .m6-amt { filter: blur(6px); user-select: none; }

@media (max-width: 1199px) { .m6 .m6-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .m6 .m6-upd { display: none; } }
@media (max-width: 767px) {
    .m6 { padding: 8px 10px 16px !important; }
    .m6 .m6-tiles { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
    .m6 .m6-grid { grid-template-columns: minmax(0, 1fr); }
    .m6 .m6-views { flex: 1 0 100%; } .m6 .m6-views a { flex: 1; text-align: center; padding: 3px 4px; }
    .m6 .m6-title span { display: none; }
    .m6 .m6-btn span { display: none; }
    .m6 .m6-tile b { font-size: 19px; }
    .m6 .m6-tile-l { white-space: normal; line-height: 1.2; }
    .m6 .m6-body { max-height: none; }
}
</style>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
