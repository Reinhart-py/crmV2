<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<style>
.dash-kpi-card {
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-height: 60px;
    height: 60px;
    border-radius: 8px !important;
    padding: 7px 12px !important;
    color: #ffffff !important;
    margin-bottom: 6px;
    overflow: hidden;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    cursor: pointer;
    text-decoration: none !important;
    border: none !important;
}
.dash-kpi-card:hover {
    transform: translateY(-2px);
    text-decoration: none !important;
    color: #ffffff !important;
}
.dash-kpi-col1 {
    background: linear-gradient(135deg, #5f3abd 0%, #4a2bb3 100%) !important;
    box-shadow: 0 4px 12px rgba(95, 58, 189, 0.22) !important;
}
.dash-kpi-col1:hover {
    box-shadow: 0 8px 18px rgba(95, 58, 189, 0.35) !important;
}
.dash-kpi-col2 {
    background: linear-gradient(135deg, #01509e 0%, #003c7a 100%) !important;
    box-shadow: 0 4px 12px rgba(1, 80, 158, 0.22) !important;
}
.dash-kpi-col2:hover {
    box-shadow: 0 8px 18px rgba(1, 80, 158, 0.35) !important;
}
.dash-kpi-col3 {
    background: linear-gradient(135deg, #0086c5 0%, #006fa8 100%) !important;
    box-shadow: 0 4px 12px rgba(0, 134, 197, 0.22) !important;
}
.dash-kpi-col3:hover {
    box-shadow: 0 8px 18px rgba(0, 134, 197, 0.35) !important;
}
.dash-kpi-col4 {
    background: linear-gradient(135deg, #00b69c 0%, #009680 100%) !important;
    box-shadow: 0 4px 12px rgba(0, 182, 156, 0.22) !important;
}
.dash-kpi-col4:hover {
    box-shadow: 0 8px 18px rgba(0, 182, 156, 0.35) !important;
}
.dash-kpi-card *,
.dash-kpi-card .dash-kpi-label,
.dash-kpi-card .dash-kpi-value,
.dash-kpi-card .dash-kpi-sales {
    color: #ffffff !important;
}
.dash-kpi-value {
    font-size: 18px;
    font-weight: 700;
    line-height: 1.1;
    letter-spacing: -0.02em;
    margin-bottom: 2px;
}
.dash-kpi-label {
    font-size: 11.5px;
    font-weight: 600;
    line-height: 1.2;
    opacity: 0.95;
    text-transform: capitalize;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.dash-kpi-sales {
    font-size: 9.5px;
    font-weight: 500;
    line-height: 1.2;
    margin-top: 2px;
    opacity: 0.92;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.dash-kpi-icon {
    font-size: 19px;
    line-height: 1;
    opacity: 0.35;
    transition: transform 0.2s ease, opacity 0.2s ease;
    flex-shrink: 0;
    margin-left: 4px;
}
.dash-kpi-card:hover .dash-kpi-icon {
    transform: scale(1.1);
    opacity: 0.5;
}
.dash-round-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px 12px;
    margin-bottom: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    box-sizing: border-box;
}
.dash-top-chart-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-top: 2px solid #0084ff !important;
    border-radius: 8px !important;
    padding: 6px 12px !important;
    margin-bottom: 8px !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03) !important;
    overflow: hidden !important;
}
[data-theme="dark"] .dash-top-chart-card {
    background: #111827 !important;
    border-color: #1f2937 !important;
}
.dash-top-chart-body {
    position: relative !important;
    height: var(--c2-body, 172px) !important;
    max-height: var(--c2-body, 172px) !important;
    padding: 0 !important;
    overflow: hidden !important;
}
.dash-top-chart-body canvas {
    height: var(--c2-body, 172px) !important;
    max-height: var(--c2-body, 172px) !important;
    width: 100% !important;
}
[data-theme="dark"] .dash-round-meta {
    color: #94a3b8 !important;
}
[data-theme="dark"] .dash-round-meta strong {
    color: #f8fafc !important;
}
[data-theme="dark"] .dash-round-card {
    background: #111827;
    border-color: #1f2937;
}
.dash-round-title {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.01em;
    margin: 0 0 6px 0;
}
[data-theme="dark"] .dash-round-title {
    color: #f8fafc;
}
[data-theme="dark"] .donut-callout-text {
    fill: #f1f5f9 !important;
}
[data-theme="dark"] .donut-slice {
    stroke: #111827 !important;
}

/* Enhanced Text Visibility for Older Users & High Contrast */
.dash-section-title span:last-child {
    font-size: 17px !important;
    font-weight: 800 !important;
    color: #0f172a !important;
}
.crm-modern-stat-card .card-title {
    font-size: 14.5px !important;
    font-weight: 800 !important;
    color: #0f172a !important;
}
.crm-modern-stat-card .card-total-pill {
    font-size: 13px !important;
    font-weight: 700 !important;
    padding: 3px 10px !important;
    color: #0369a1 !important;
    background: #e0f2fe !important;
    border: 1px solid #bae6fd !important;
}
.crm-modern-stat-chip span:first-child {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
}
.crm-modern-stat-chip .badge {
    font-size: 12.5px !important;
    font-weight: 800 !important;
    padding: 3px 8px !important;
    min-width: 30px !important;
}
.crm-modern-stat-chip {
    padding: 6px 10px !important;
    background: #f8fafc !important;
    border: 1px solid #cbd5e1 !important;
}
.crm-modern-stat-chip:hover {
    background: #f1f5f9 !important;
    border-color: #94a3b8 !important;
}
.dash-donut-empty {
    width: 100%;
    min-height: 220px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    font-size: 13px;
    font-weight: 500;
}
.dash-round-card svg {
    max-width: 100%;
}
@media (max-width: 767px) {
    .col-xs-3:has(> .dash-kpi-card) { width: 50%; }
}

</style>

<?php
// Classic 5: small helpers for the extra blocks.
$c5 = isset($c5) && is_array($c5) ? $c5 : array();
if (!function_exists('c5_chg')) {
    // Records added this month so far against the same days of last month, as a small tag for the tiles.
    function c5_chg($row) {
        $now = isset($row['now']) ? (int)$row['now'] : 0;
        $last = isset($row['last']) ? (int)$row['last'] : 0;
        $tip = isset($row['now_label']) ? ('Added ' . $row['now_label'] . ': ' . $now . ', ' . $row['last_label'] . ': ' . $last) : ('Added this month: ' . $now . ', last month: ' . $last);
        if ($now === 0 && $last === 0) return '<span class="c5-chg is-flat" title="' . $tip . '">&ndash; 0%</span>';
        if ($last === 0) return '<span class="c5-chg is-up" title="' . $tip . '">&#9650; ' . $now . ' new</span>';
        $pct = (int)round(($now - $last) / $last * 100);
        $cls = $pct > 0 ? 'is-up' : ($pct < 0 ? 'is-down' : 'is-flat');
        $arrow = $pct > 0 ? '&#9650;' : ($pct < 0 ? '&#9660;' : '&ndash;');
        return '<span class="c5-chg ' . $cls . '" title="' . $tip . '">' . $arrow . ' ' . abs($pct) . '%</span>';
    }
}
?>
<div class="content-wrapper full-screen-dash dash-c2 dash-c5" id="c2Root">



    <div class="dash-content-body">

<!-- Classic 2: section links, Hide amounts -->
<nav class="c2-bar" id="c2Bar" aria-label="Dashboard sections">
    <div class="c2-bar-links">
        <a href="#c2-top" data-sec="c2-top">Overview</a>
        <a href="#c3-pipeline" data-sec="c3-pipeline">Pipeline</a>
        <a href="#c5-team" data-sec="c5-team">Team</a>
        <a href="#c2-activity" data-sec="c2-activity">Activity</a>
        <a href="#c2-database" data-sec="c2-database">Database</a>
        <a href="#c2-achievement" data-sec="c2-achievement">Achievement</a>
    </div>
<?php
$c5_attn = array(
    array('Missed follow-ups', isset($dashboard_f['total_missed']) ? (int)$dashboard_f['total_missed'] : 0, base_url() . 'manager/leads/followups?type=1', 'c5-red', 'fa-phone'),
    array('Follow-ups today', isset($dashboard_f['total_today']) ? (int)$dashboard_f['total_today'] : 0, base_url() . 'manager/leads/followups?type=3', 'c5-sky', 'fa-calendar'),
    array('Missed meetings', isset($dashboard_m['total_missed']) ? (int)$dashboard_m['total_missed'] : 0, base_url() . 'manager/leads/meetings?type=1', 'c5-orange', 'fa-users'),
    array('Open tickets', !empty($dashboard_tickets['total_all']) ? (int)$dashboard_tickets['total_all'] : 0, has_section_perm('tickets', 'read') ? base_url() . 'manager/tickets?assigned_user=' . (int)$this->session->userdata('manager_id') . '&status=Open' : '', 'c5-blue', 'fa-ticket'),
);
?>
    <div class="c5-attn" aria-label="Needs attention today">
        <?php foreach ($c5_attn as $a) { $tag = $a[2] !== '' ? 'a href="' . $a[2] . '"' : 'span'; ?>
        <<?php echo $tag; ?> class="c5-attn-i <?php echo $a[3] . ($a[1] === 0 ? ' is-zero' : ''); ?>" title="<?php echo $a[0] . ': ' . $a[1]; ?>"><i class="fa <?php echo $a[4]; ?>"></i><b><?php echo $a[1]; ?></b><span><?php echo $a[0]; ?></span></<?php echo $a[2] !== '' ? 'a' : 'span'; ?>>
        <?php } ?>
    </div>
    <span class="c5-upd" title="When this page was loaded">Updated <?php echo date('H:i'); ?></span>
    <button type="button" class="c5-refresh" id="c5Refresh" title="Reload the dashboard" aria-label="Reload the dashboard"><i class="fa fa-refresh"></i></button>
    <button type="button" class="c2-hide-btn" id="c2HideAmt" aria-pressed="false" title="Hide amounts on screen (for screen sharing)"><i class="fa fa-eye-slash"></i> <span>Hide amounts</span></button>
</nav>
<div id="c2-top" class="c2-anchor"></div>

<div id="donutHoverTooltip" style="display:none; position:fixed; z-index:99999; pointer-events:none; background:#12072b; color:#ffffff; border-radius:6px; padding:9px 13px; font-size:11.5px; box-shadow:0 10px 28px rgba(0,0,0,0.45); border:1px solid rgba(255,255,255,0.18); min-width:170px; line-height:1.4;">
    <div style="display:flex; justify-content:space-between; margin-bottom:3px; gap:12px;">
        <span style="color:#ffffff; font-weight:700;" id="donutTipTitle">Lead: Status</span>
        <span style="color:#ffffff; font-weight:700;" id="donutTipStage">Busy</span>
    </div>
    <div style="display:flex; justify-content:space-between; gap:12px;">
        <span style="color:#ffffff; font-weight:700;" id="donutTipType">Leads</span>
        <span style="color:#ffffff; font-weight:700;" id="donutTipVal">0 (0.0%)</span>
    </div>
</div>

<?php
if (!function_exists('render_donut_chart_svg')) {
    function render_donut_chart_svg($items, $w = 460, $h = 250, $cx = 230, $cy = 125, $r_out = 110, $r_in = 68, $type = 'Lead', $frame = null) {
        // $frame = array(half width, half height): draw in this space so several donuts come out the same size.
        // The space this donut needs is kept in $GLOBALS['crm_donut_frame'].
        $total = 0;
        foreach ($items as $it) {
            $total += (float)$it['count'];
        }
        if ($total <= 0) {
            // No data: blank grey ring, like the Operational Activity donuts.
            $pad = 12;
            $GLOBALS['crm_donut_frame'] = array($r_out + $pad, $r_out + $pad);
            $vb = ($cx - $r_out - $pad) . ' ' . ($cy - $r_out - $pad) . ' ' . (2 * ($r_out + $pad)) . ' ' . (2 * ($r_out + $pad));
            if ($frame) $vb = ($cx - $frame[0]) . ' ' . ($cy - $frame[1]) . ' ' . (2 * $frame[0]) . ' ' . (2 * $frame[1]);
            // Classic 2: a smaller ring with a short message in the middle.
            $empty_txt = 'No ' . strtolower($type) . 's yet';
            return '<svg viewBox="' . $vb . '" width="100%" height="100%" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" style="position:absolute; left:0; top:0; width:100%; height:100%; display:block;" aria-label="' . $empty_txt . '"><circle class="c2-empty-ring" cx="' . $cx . '" cy="' . $cy . '" r="' . round(($r_out + $r_in) / 2 * 0.62) . '" fill="none" stroke="#e2e8f0" stroke-width="' . round(($r_out - $r_in) * 0.45) . '" /><text class="c2-empty-text" x="' . $cx . '" y="' . ($cy + 5) . '" text-anchor="middle" font-size="15" font-weight="600" fill="#64748b" font-family="-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif">' . $empty_txt . '</text></svg>';
        }
        $n = count($items);
        $current_angle = -M_PI / 2;
        $paths = '';
        $callouts = '';
        $callout_list = array();
        $min_x = $cx - $r_out - 4;
        $max_x = $cx + $r_out + 4;
        $min_y = $cy - $r_out - 4;
        $max_y = $cy + $r_out + 4;
        foreach ($items as $it) {
            $pct = $total > 0 ? (($it['count'] / $total) * 100) : (100 / max(1, $n));
            $angle_delta = ($pct / 100) * (2 * M_PI);
            if ($angle_delta <= 0.001) {
                $current_angle += $angle_delta;
                continue;
            }
            $next_angle = $current_angle + $angle_delta;
            $large_arc = ($angle_delta > M_PI) ? 1 : 0;
            $x1_out = $cx + $r_out * cos($current_angle);
            $y1_out = $cy + $r_out * sin($current_angle);
            $x2_out = $cx + $r_out * cos($next_angle);
            $y2_out = $cy + $r_out * sin($next_angle);
            $x1_in = $cx + $r_in * cos($current_angle);
            $y1_in = $cy + $r_in * sin($current_angle);
            $x2_in = $cx + $r_in * cos($next_angle);
            $y2_in = $cy + $r_in * sin($next_angle);
            $d = sprintf("M %.1f %.1f A %d %d 0 %d 1 %.1f %.1f L %.1f %.1f A %d %d 0 %d 0 %.1f %.1f Z", $x1_out, $y1_out, $r_out, $r_out, $large_arc, $x2_out, $y2_out, $x2_in, $y2_in, $r_in, $r_in, $large_arc, $x1_in, $y1_in);
            $full_ring = ($angle_delta >= (2 * M_PI) - 0.001);
            if ($full_ring) {
                $d = sprintf("M %.1f %.1f A %d %d 0 1 1 %.1f %.1f A %d %d 0 1 1 %.1f %.1f Z M %.1f %.1f A %d %d 0 1 0 %.1f %.1f A %d %d 0 1 0 %.1f %.1f Z", $cx - $r_out, $cy, $r_out, $r_out, $cx + $r_out, $cy, $r_out, $r_out, $cx - $r_out, $cy, $cx - $r_in, $cy, $r_in, $r_in, $cx + $r_in, $cy, $r_in, $r_in, $cx - $r_in, $cy);
            }
            $display_pct = $total > 0 ? number_format(($it['count'] / $total) * 100, 1) . '%' : '0.0%';
            $paths .= '<path d="' . $d . '" fill-rule="evenodd" fill="' . $it['color'] . '" stroke="#ffffff" stroke-width="2.5" class="donut-slice" data-label="' . htmlspecialchars($it['label']) . '" data-count="' . (int)$it['count'] . '" data-pct="' . $display_pct . '" data-type="' . $type . '" style="cursor:pointer; transition:transform 0.15s ease, opacity 0.15s ease;" />';
            $mid = $current_angle + $angle_delta / 2;
            $p1_x = $cx + ($r_out + 2) * cos($mid);
            $p1_y = $cy + ($r_out + 2) * sin($mid);
            $p2_x = $cx + ($r_out + 9) * cos($mid);
            $p2_y = $cy + ($r_out + 9) * sin($mid);
            // Side labels wrap onto short lines (max ~11 characters) so they need little width and the donut can be larger;
            // labels at the top and bottom have room and stay on one line (saves height).
            $sin = sin($mid);
            $wrap_at = abs($sin) > 0.6 ? 40 : 11;
            $lines = array();
            $cur = '';
            foreach (preg_split('/\s+/', trim($it['label'])) as $word) {
                if ($cur !== '' && strlen($cur . ' ' . $word) > $wrap_at) { $lines[] = $cur; $cur = $word; }
                else { $cur = ($cur === '' ? $word : $cur . ' ' . $word); }
            }
            $tail = ': ' . (int)$it['count'];
            if (strlen($cur . $tail) <= $wrap_at + 2) { $lines[] = $cur . $tail; } else { $lines[] = $cur . ':'; $lines[] = (string)(int)$it['count']; }
            $lbl_w = 0;
            foreach ($lines as $ln) { $lbl_w = max($lbl_w, strlen($ln) * 7.2); }
            $callout_list[] = array(
                'p1' => array($p1_x, $p1_y), 'p2' => array($p2_x, $p2_y),
                'right' => cos($mid) >= 0, 'lines' => $lines, 'w' => $lbl_w,
                // upper labels grow upwards from the line, lower ones downwards, side ones are centred on it
                'align' => $sin < -0.5 ? 'up' : ($sin > 0.5 ? 'down' : 'mid'),
            );
            $current_angle = $next_angle;
        }

        // Place the label blocks; on each side, push blocks apart so they never overlap.
        $line_h = 13;
        foreach (array(true, false) as $side) {
            $group = array();
            foreach ($callout_list as $k => $c) {
                if ($c['right'] !== $side) continue;
                $h = count($c['lines']) * $line_h;
                $anchor_y = $c['p2'][1];
                $top = $c['align'] === 'up' ? $anchor_y - $h + 4 : ($c['align'] === 'down' ? $anchor_y - 6 : $anchor_y - $h / 2);
                $group[$k] = array('top' => $top, 'h' => $h);
            }
            uasort($group, function ($a, $b) { return $a['top'] < $b['top'] ? -1 : 1; });
            $prev_bottom = null;
            foreach ($group as $k => $g) {
                $top = ($prev_bottom !== null && $g['top'] < $prev_bottom + 4) ? $prev_bottom + 4 : $g['top'];
                $callout_list[$k]['top'] = $top;
                $prev_bottom = $top + $g['h'];
            }
        }
        foreach ($callout_list as $c) {
            $is_right = $c['right'];
            $h = count($c['lines']) * $line_h;
            $p3_x = $c['p2'][0] + ($is_right ? 8 : -8);
            $p3_y = min(max($c['p2'][1], $c['top'] + 5), $c['top'] + $h - 5);
            $tx = $p3_x + ($is_right ? 4 : -4);
            $first_y = $c['top'] + 10;
            $min_x = min($min_x, $is_right ? $tx : $tx - $c['w']);
            $max_x = max($max_x, $is_right ? $tx + $c['w'] : $tx);
            $min_y = min($min_y, $c['top'] - 1, $c['p2'][1] - 2);
            $max_y = max($max_y, $c['top'] + $h + 1, $c['p2'][1] + 2);
            $callouts .= sprintf('<polyline points="%.1f,%.1f %.1f,%.1f %.1f,%.1f" fill="none" stroke="#94a3b8" stroke-width="1.3" />', $c['p1'][0], $c['p1'][1], $c['p2'][0], $c['p2'][1], $p3_x, $p3_y);
            $spans = '';
            foreach ($c['lines'] as $i => $ln) {
                $spans .= sprintf('<tspan x="%.1f" dy="%s">%s</tspan>', $tx, $i === 0 ? '0' : $line_h, htmlspecialchars($ln));
            }
            $callouts .= sprintf('<text x="%.1f" y="%.1f" font-size="12" font-family="-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif" font-weight="700" fill="#0f172a" class="donut-callout-text" text-anchor="%s">%s</text>', $tx, $first_y, $is_right ? 'start' : 'end', $spans);
        }
        // Same space on both sides of the donut so it sits in the centre of the box; labels stay inside.
        $half_w = ceil(max($cx - $min_x, $max_x - $cx) + 4);
        $half_h = ceil(max($cy - $min_y, $max_y - $cy) + 2);
        $GLOBALS['crm_donut_frame'] = array($half_w, $half_h);
        if ($frame) { $half_w = max($half_w, $frame[0]); $half_h = max($half_h, $frame[1]); }
        $vb_x = $cx - $half_w;
        $vb_w = 2 * $half_w;
        $vb_y = $cy - $half_h;
        $vb_h = 2 * $half_h;
        // Fills the space left in the card (the wrapper is position:relative), as large as fits without cutting labels.
        return '<svg viewBox="' . $vb_x . ' ' . $vb_y . ' ' . $vb_w . ' ' . $vb_h . '" width="100%" height="100%" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" style="position:absolute; left:0; top:0; width:100%; height:100%; display:block; overflow:hidden;"><g>' . $paths . $callouts . '</g></svg>';
    }
}
$curr = !empty($user_currency) ? $user_currency : 'AED';
$my_db_total = 0;
if (!empty($dashboard_db_state)) {
    foreach($dashboard_db_state as $item) {
        $my_db_total += (int)$item['lead_count'];
    }
}
$team_db_total = 0;
if (!empty($dashboard_db_state_team)) {
    foreach($dashboard_db_state_team as $item) {
        $team_db_total += (int)$item['lead_count'];
    }
}
$my_lead_total = 0;
if (!empty($dashboard_lead_state)) {
    foreach($dashboard_lead_state as $item) {
        $my_lead_total += (int)$item['lead_count'];
    }
}
$team_lead_total = 0;
if (!empty($dashboard_lead_state_team)) {
    foreach($dashboard_lead_state_team as $item) {
        $team_lead_total += (int)$item['lead_count'];
    }
}
$my_order_total = 0;
if (!empty($dashboard_order_state)) {
    foreach($dashboard_order_state as $item) {
        $my_order_total += (int)$item['order_count'];
    }
} else {
    $my_order_total = isset($dashboard_count['count_orders']) ? (int)$dashboard_count['count_orders'] : 0;
}
$team_order_total = 0;
if (!empty($dashboard_order_state_team)) {
    foreach($dashboard_order_state_team as $item) {
        $team_order_total += (int)$item['order_count'];
    }
} else {
    $team_order_total = isset($dashboard_Tcount['count_orders']) ? (int)$dashboard_Tcount['count_orders'] : 0;
}

$lead_items = !empty($mylead_donut['items']) ? $mylead_donut['items'] : array(
    array('label' => 'Busy', 'count' => 0, 'color' => '#0072f0'),
    array('label' => 'DNCR', 'count' => 0, 'color' => '#00bbf9'),
    array('label' => 'Following Up', 'count' => 0, 'color' => '#6366f1'),
    array('label' => 'Future', 'count' => 0, 'color' => '#64748b'),
    array('label' => 'Not Interested', 'count' => 0, 'color' => '#ff5964'),
    array('label' => 'Not Valid', 'count' => 0, 'color' => '#00b4d8')
);

$order_items = !empty($myorder_donut['items']) ? $myorder_donut['items'] : array(
    array('label' => 'Submitted', 'count' => 0, 'color' => '#0072f0'),
    array('label' => 'Approved', 'count' => 0, 'color' => '#00bbf9'),
    array('label' => 'In Progress', 'count' => 0, 'color' => '#6366f1'),
    array('label' => 'Onboarding', 'count' => 0, 'color' => '#64748b'),
    array('label' => 'Under Review', 'count' => 0, 'color' => '#f59e0b'),
    array('label' => 'Completed', 'count' => 0, 'color' => '#10b981'),
    array('label' => 'Sales', 'count' => 0, 'color' => '#00b4d8'),
    array('label' => 'Canceled', 'count' => 0, 'color' => '#ff5964')
);
// My Leads and My Orders share one drawing space (the larger of the two), so both rings are the same size.
render_donut_chart_svg($lead_items, 480, 260, 240, 130, 120, 72, 'Lead');
$donut_frame = $GLOBALS['crm_donut_frame'];
render_donut_chart_svg($order_items, 480, 260, 240, 130, 120, 72, 'Order');
$donut_frame = array(max($donut_frame[0], $GLOBALS['crm_donut_frame'][0]), max($donut_frame[1], $GLOBALS['crm_donut_frame'][1]));
?>

        <div class="row c2-toprow" style="margin-top: 0; margin-bottom: 8px;">
            <div class="col-lg-8 col-md-7 col-xs-12" style="padding-left: 14px; padding-right: 4px;">
                <div class="row" style="margin-left:-4px; margin-right:-4px;">
                    <div class="col-xs-3" style="padding-left:4px; padding-right:4px;">
                        <a href="<?php echo base_url(); ?>manager/leads" class="dash-kpi-card dash-kpi-col1" id="cardMyDatabase">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                <div style="min-width:0; flex:1;">
                                    <div class="dash-kpi-value"><?php echo isset($dashboard_count['count_database']) ? (int)$dashboard_count['count_database'] : 0; ?></div>
                                    <div class="dash-kpi-label">My Database</div>
                                </div>
                                <div class="dash-kpi-icon"><i class="fa fa-database"></i></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xs-3" style="padding-left:4px; padding-right:4px;">
                        <a href="<?php echo base_url(); ?>manager/verticals" class="dash-kpi-card dash-kpi-col2" id="cardMyVerticals">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                <div style="min-width:0; flex:1;">
                                    <div class="dash-kpi-value"><?php echo isset($dashboard_count['count_verticals']) ? (int)$dashboard_count['count_verticals'] : 0; ?></div>
                                    <div class="dash-kpi-label">My Vertical</div>
                                </div>
                                <div class="dash-kpi-icon"><i class="ion ion-stats-bars"></i></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xs-3" style="padding-left:4px; padding-right:4px;">
                        <a href="<?php echo base_url(); ?>manager/team/members" class="dash-kpi-card dash-kpi-col3" id="cardTeamUsers">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                <div style="min-width:0; flex:1;">
                                    <div class="dash-kpi-value"><?php echo (isset($dashboard_Tcount['count_users']) && (int)$dashboard_Tcount['count_users'] > 0) ? (int)$dashboard_Tcount['count_users'] : (isset($dashboard_Tcount['count_user']) ? (int)$dashboard_Tcount['count_user'] : 0); ?></div>
                                    <div class="dash-kpi-label">Team Users</div>
                                </div>
                                <div class="dash-kpi-icon"><i class="ion ion-person-add"></i></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xs-3" style="padding-left:4px; padding-right:4px;">
                        <a href="<?php echo base_url(); ?>manager/team/verticals" class="dash-kpi-card dash-kpi-col4" id="cardTeamVerticals">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                <div style="min-width:0; flex:1;">
                                    <div class="dash-kpi-value"><?php echo isset($dashboard_Tcount['count_verticals']) ? (int)$dashboard_Tcount['count_verticals'] : 0; ?></div>
                                    <div class="dash-kpi-label">Team Verticals</div>
                                </div>
                                <div class="dash-kpi-icon"><i class="ion ion-stats-bars"></i></div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="row" style="margin-left:-4px; margin-right:-4px;">
                    <div class="col-xs-3" style="padding-left:4px; padding-right:4px;">
                        <a href="<?php echo base_url(); ?>manager/leads/view_leads" class="dash-kpi-card dash-kpi-col1" id="cardMyLeads">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                <div style="min-width:0; flex:1;">
                                    <div class="dash-kpi-value"><?php echo isset($dashboard_count['count_leads']) ? (int)$dashboard_count['count_leads'] : 0; ?></div>
                                    <div class="dash-kpi-label">My Leads <?php echo c5_chg(isset($c5['tile_change']['my_leads']) ? $c5['tile_change']['my_leads'] : array()); ?></div>
                                    <div class="dash-kpi-sales c2-amt"><?php echo $curr . ' ' . number_format((float)(isset($dashboard_count['sales_amount']) ? $dashboard_count['sales_amount'] : 0), 0); ?></div>
                                </div>
                                <div class="dash-kpi-icon"><i class="fa fa-bullseye"></i></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xs-3" style="padding-left:4px; padding-right:4px;">
                        <a href="<?php echo base_url(); ?>manager/orders/my" class="dash-kpi-card dash-kpi-col2" id="cardMyOrders">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                <div style="min-width:0; flex:1;">
                                    <div class="dash-kpi-value"><?php echo isset($dashboard_count['count_orders']) ? (int)$dashboard_count['count_orders'] : 0; ?></div>
                                    <div class="dash-kpi-label">My Orders <?php echo c5_chg(isset($c5['tile_change']['my_orders']) ? $c5['tile_change']['my_orders'] : array()); ?></div>
                                    <div class="dash-kpi-sales c2-amt"><?php echo $curr . ' ' . number_format((float)(isset($dashboard_count['sales_amount']) ? $dashboard_count['sales_amount'] : 0), 0); ?></div>
                                </div>
                                <div class="dash-kpi-icon"><i class="fa fa-shopping-cart"></i></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xs-3" style="padding-left:4px; padding-right:4px;">
                        <a href="<?php echo base_url(); ?>manager/team/leads" class="dash-kpi-card dash-kpi-col3" id="cardTeamLeads">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                <div style="min-width:0; flex:1;">
                                    <div class="dash-kpi-value"><?php echo isset($dashboard_Tcount['count_leads']) ? (int)$dashboard_Tcount['count_leads'] : 0; ?></div>
                                    <div class="dash-kpi-label">Team Leads <?php echo c5_chg(isset($c5['tile_change']['team_leads']) ? $c5['tile_change']['team_leads'] : array()); ?></div>
                                    <div class="dash-kpi-sales c2-amt"><?php echo $curr . ' ' . number_format((float)(isset($dashboard_Tcount['sales_amount']) ? $dashboard_Tcount['sales_amount'] : 0), 0); ?></div>
                                </div>
                                <div class="dash-kpi-icon"><i class="fa fa-bullseye"></i></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xs-3" style="padding-left:4px; padding-right:4px;">
                        <a href="<?php echo base_url(); ?>manager/team/orders" class="dash-kpi-card dash-kpi-col4" id="cardTeamOrders">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                <div style="min-width:0; flex:1;">
                                    <div class="dash-kpi-value"><?php echo isset($dashboard_Tcount['count_orders']) ? (int)$dashboard_Tcount['count_orders'] : 0; ?></div>
                                    <div class="dash-kpi-label">Team Orders <?php echo c5_chg(isset($c5['tile_change']['team_orders']) ? $c5['tile_change']['team_orders'] : array()); ?></div>
                                    <div class="dash-kpi-sales c2-amt"><?php echo $curr . ' ' . number_format((float)(isset($dashboard_Tcount['sales_amount']) ? $dashboard_Tcount['sales_amount'] : 0), 0); ?></div>
                                </div>
                                <div class="dash-kpi-icon"><i class="fa fa-shopping-cart"></i></div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="dash-top-chart-card" style="height: var(--c2-card, 210px); max-height: var(--c2-card, 210px); margin-bottom: 8px; padding: 6px 12px; border-radius: 8px; box-sizing: border-box;">
                    <div class="dash-chart-header" style="display:flex !important; justify-content:space-between !important; align-items:center !important; width:100% !important; margin-bottom: 2px !important; padding:2px 0 !important; border-bottom:none !important;">
                        <h4 class="dash-chart-title" style="margin:0 !important; font-size:12.5px; font-weight:700;">
                            Target vs Achievement
                        </h4>
<?php
// This month: achievement and projection as a share of the target (same numbers as the charts).
$c3_t = !empty($dashboard_achievement_my['target']) ? (float)end($dashboard_achievement_my['target']) : 0;
$c3_a = !empty($dashboard_achievement_my['achievement']) ? (float)end($dashboard_achievement_my['achievement']) : 0;
$c3_p = isset($dashboard_MyPro['lead_number']['pt_0']) ? (float)$dashboard_MyPro['lead_number']['pt_0'] : 0;
$c3_m = !empty($dashboard_achievement_my['labels']) ? end($dashboard_achievement_my['labels']) : date('M-y');
$c3_ap = $c3_t > 0 ? (int)round($c3_a / $c3_t * 100) : 0;
$c3_pp = $c3_t > 0 ? (int)round($c3_p / $c3_t * 100) : 0;
?>
                        <div class="c3-pct" title="<?php echo $c3_t > 0 ? htmlspecialchars($c3_m . ': achieved ' . $c3_ap . '% of target, projection ' . $c3_pp . '% of target') : 'No target set for ' . htmlspecialchars($c3_m); ?>">
                            <?php if ($c3_t > 0) { ?>
                            <span class="c3-pct-lbl"><?php echo htmlspecialchars($c3_m); ?> <b class="<?php echo $c3_ap >= 100 ? 'is-met' : ''; ?>"><?php echo $c3_ap; ?>%</b></span>
                            <span class="c3-pct-bar"><i style="width: <?php echo min(100, $c3_ap); ?>%;"></i><em style="left: <?php echo min(100, $c3_pp); ?>%;"></em></span>
                            <span class="c3-pct-pro">Projection <?php echo $c3_pp; ?>%</span>
                            <?php } else { ?>
                            <span class="c3-pct-lbl c3-pct-none">No target for <?php echo htmlspecialchars($c3_m); ?></span>
                            <?php } ?>
                        </div>
                        <div class="dash-chart-legend" style="display:inline-flex; align-items:center; gap:10px; font-size:10.5px; font-weight:600; color:#475569;">
                            <span style="display:inline-flex; align-items:center; gap:4px;"><span style="display:inline-block; width:8px; height:8px; background-color:#00c6f0; border-radius:50%;"></span> Target</span>
                            <span style="display:inline-flex; align-items:center; gap:4px;"><span style="display:inline-block; width:8px; height:8px; background-color:#16a34a; border-radius:50%;"></span> Achievement</span>
                        </div>
                    </div>
                    <div class="dash-top-chart-body" style="height: var(--c2-body, 172px) !important; max-height: var(--c2-body, 172px) !important;">
                        <canvas id="lineChartTargetAchieve" height="172"></canvas>
                    </div>
                </div>

                <div class="dash-top-chart-card" style="height: var(--c2-card, 210px); max-height: var(--c2-card, 210px); margin-bottom: 0px; padding: 6px 12px; border-radius: 8px; box-sizing: border-box;">
                    <div class="dash-chart-header" style="display:flex !important; justify-content:space-between !important; align-items:center !important; width:100% !important; margin-bottom: 2px !important; padding:2px 0 !important; border-bottom:none !important;">
                        <h4 class="dash-chart-title" style="margin:0 !important; font-size:12.5px; font-weight:700;">
                            Target vs Projection
                        </h4>
                        <div class="dash-chart-legend" style="display:inline-flex; align-items:center; gap:10px; font-size:10.5px; font-weight:600; color:#475569;">
                            <span style="display:inline-flex; align-items:center; gap:4px;"><span style="display:inline-block; width:8px; height:8px; background-color:#00c6f0; border-radius:50%;"></span> Target</span>
                            <span style="display:inline-flex; align-items:center; gap:4px;"><span style="display:inline-block; width:8px; height:8px; background-color:#0072f0; border-radius:50%;"></span> Projection</span>
                        </div>
                    </div>
                    <div class="dash-top-chart-body" style="height: var(--c2-body, 172px) !important; max-height: var(--c2-body, 172px) !important;">
                        <canvas id="lineChartTargetProject" height="172"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-5 col-xs-12" style="padding-left: 4px; padding-right: 14px;">
                <div class="dash-round-card" style="width: 100%; min-height: var(--c2-ring, 277px); height: var(--c2-ring, auto); margin-bottom: 8px; padding: 8px 12px; border-radius: 8px; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box;">
                    <div class="dash-round-title" style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0; font-size:13px; font-weight:700;">
                        <span>My Leads</span>
                        <div class="dash-round-meta" style="display:inline-flex; align-items:center; gap:6px; font-size:11.5px; font-weight:600; color:#64748b;">
                            <span><strong style="color:#0f172a; font-weight:700;"><?php echo isset($mylead_donut['total']) ? (int)$mylead_donut['total'] : 0; ?></strong> Total</span>
                        </div>
                    </div>
                    <div style="width:100%; display:flex; align-items:center; justify-content:center; flex:1; overflow:visible; position:relative; min-height:0;">
                        <?php echo render_donut_chart_svg($lead_items, 480, 260, 240, 130, 120, 72, 'Lead', $donut_frame); ?>
                    </div>
                </div>

                <div class="dash-round-card" style="width: 100%; min-height: var(--c2-ring, 277px); height: var(--c2-ring, auto); margin-bottom: 0px; padding: 8px 12px; border-radius: 8px; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box;">
                    <div class="dash-round-title" style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0; font-size:13px; font-weight:700;">
                        <span>My Orders</span>
                        <div class="dash-round-meta" style="display:inline-flex; align-items:center; gap:6px; font-size:11.5px; font-weight:600; color:#64748b;">
                            <span><strong style="color:#0f172a; font-weight:700;"><?php echo isset($myorder_donut['total']) ? (int)$myorder_donut['total'] : 0; ?></strong> Total</span>
                        </div>
                    </div>
                    <div style="width:100%; display:flex; align-items:center; justify-content:center; flex:1; overflow:visible; position:relative; min-height:0;">
                        <?php echo render_donut_chart_svg($order_items, 480, 260, 240, 130, 120, 72, 'Order', $donut_frame); ?>
                    </div>
                </div>
            </div>
        </div>
<script>
// Top area fits the screen: same 8px gaps, both columns end on one line, next section starts below.
(function () {
    var root = document.getElementById('c2Root'), row = root ? root.querySelector('.c2-toprow') : null;
    if (!row) return;
    var MIN = 170, MAX = 440, SPARE = 18, rowTop = null, cur = 0;
    function scrolled() { return (document.body.scrollTop || 0) + (window.pageYOffset || 0) > 0; }
    function clamp(v) { return Math.max(MIN, Math.min(MAX, v)); }
    function apply(card) {
        cur = card;
        root.style.setProperty('--c2-card', card + 'px');
        root.style.setProperty('--c2-body', (card - 38) + 'px');
        root.style.setProperty('--c2-ring', (card + 68) + 'px');
    }
    function fit(redraw) {
        var z = parseFloat(getComputedStyle(document.body).zoom) || 1, before = cur;
        if (rowTop === null || !scrolled()) rowTop = row.getBoundingClientRect().top / z;
        if (!window.matchMedia('(min-width: 992px)').matches) {
            cur = 0;
            root.style.removeProperty('--c2-card'); root.style.removeProperty('--c2-body'); root.style.removeProperty('--c2-ring');
            return;
        }
        // Left column: two tile rows (60 + 8 + 60 + 8), chart, 8, chart. Right column: ring, 8, ring.
        var avail = (window.innerHeight / z) - rowTop - SPARE;
        apply(clamp(Math.floor((avail - 144) / 2)));
        // Check against the real layout and shrink if anything still runs past the screen.
        if (!scrolled()) {
            var bottom = 0, kids = row.children;
            for (var i = 0; i < kids.length; i++) bottom = Math.max(bottom, kids[i].getBoundingClientRect().bottom);
            var over = (bottom - window.innerHeight) / z + SPARE;
            if (over > 0) apply(clamp(cur - Math.ceil(over / 2)));
        }
        // Charts drawn before a late change need a redraw at the new height.
        if (redraw === true && cur !== before) window.dispatchEvent(new Event('resize'));
    }
    fit();
    $(window).on('resize orientationchange', fit);
    $(window).on('load', function () { fit(true); setTimeout(function () { fit(true); }, 600); });
})();
</script>

<div class="dash-section-divider" style="margin-top: 28px; margin-bottom: 14px;">
            <h3 class="dash-section-title" style="margin-bottom:0;">
                <span class="dash-section-title-icon">
                    <span></span><span></span>
                </span>
                <span>Pipeline and Closures</span>
            </h3>
        </div>

<?php
$c3_pipe = isset($dashboard_pipeline) ? (array)$dashboard_pipeline : array();
$c3_steps = array(
    'database' => array('Database', 'fa-database'),
    'leads'    => array('Leads', 'fa-bullseye'),
    'orders'   => array('Open Orders', 'fa-shopping-cart'),
    'sales'    => array('Sales', 'fa-check-circle'),
);
$c3_max = 0;
foreach ($c3_steps as $k => $v) { $c3_max = max($c3_max, isset($c3_pipe[$k]['count']) ? (int)$c3_pipe[$k]['count'] : 0); }
$c3_clo_rows = array(
    'total_missed'   => array('All Missed', 'bg-red'),
    'total_lastweek' => array('Last 7 Days', 'bg-orange'),
    'total_today'    => array('Today', 'bg-skyblue'),
    'total_nextweek' => array('Next 7 Days', 'bg-blue'),
    'total_future'   => array('All Future', 'bg-green'),
);
$c3_clo_my = isset($dashboard_c) ? (array)$dashboard_c : array();
$c3_clo_team = isset($dashboard_Tc) ? (array)$dashboard_Tc : array();
?>
        <div class="row c3-pipe-row">
            <div class="col-lg-8 col-md-7 col-xs-12 c3-col-l">
                <div class="crm-modern-stat-card c3-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-filter" style="color:#0072f0;"></i> Sales Pipeline</h4>
                        <span class="c5-scope" role="group" aria-label="Whose pipeline"><button type="button" data-scope="team" class="is-on" aria-pressed="true"><?php echo !empty($c5['is_all']) ? 'All' : 'Team'; ?></button><button type="button" data-scope="my" aria-pressed="false">My</button></span>
                    </div>
                    <?php foreach (array('team' => $c3_pipe, 'my' => (isset($c5['pipeline_my']) ? $c5['pipeline_my'] : array())) as $c5_sk => $c5_pd) {
                        $c3_max = 0;
                        foreach ($c3_steps as $k => $v) { $c3_max = max($c3_max, isset($c5_pd[$k]['count']) ? (int)$c5_pd[$k]['count'] : 0); } ?>
                    <div class="c3-pipe" data-scope="<?php echo $c5_sk; ?>"<?php echo $c5_sk === 'my' ? ' hidden' : ''; ?>>
                        <?php $c3_i = 0; $c3_prev = null; foreach ($c3_steps as $k => $v) {
                            $cnt = isset($c5_pd[$k]['count']) ? (int)$c5_pd[$k]['count'] : 0;
                            $val = isset($c5_pd[$k]['value']) ? (float)$c5_pd[$k]['value'] : 0;
                            if ($c3_prev !== null) { ?>
                        <div class="c3-arrow" aria-hidden="true"><i class="fa fa-angle-right"></i></div>
                            <?php } ?>
                        <div class="c3-step c3-s<?php echo ++$c3_i; ?>">
                            <div class="c3-step-name"><i class="fa <?php echo $v[1]; ?>"></i> <?php echo $v[0]; ?></div>
                            <div class="c3-step-num"><?php echo number_format($cnt); ?></div>
                            <div class="c3-step-amt c2-amt"><?php echo $curr . ' ' . number_format($val, 0); ?></div>
                            <div class="c3-step-bar"><i style="width: <?php echo $c3_max > 0 ? max($cnt > 0 ? 3 : 0, (int)round($cnt / $c3_max * 100)) : 0; ?>%;"></i></div>
                        </div>
                        <?php $c3_prev = $cnt; } ?>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <div class="col-lg-4 col-md-5 col-xs-12 c3-col-r">
                <div class="crm-modern-stat-card c3-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-flag-checkered" style="color:#16a34a;"></i> Closures</h4>
                        <span class="card-total-pill">By close date</span>
                    </div>
                    <table class="c3-clo">
                        <thead><tr><th></th><th>My</th><th>Team</th></tr></thead>
                        <tbody>
                        <?php foreach ($c3_clo_rows as $k => $r) {
                            $my = isset($c3_clo_my[$k]) ? (int)$c3_clo_my[$k] : 0;
                            $tm = isset($c3_clo_team[$k]) ? (int)$c3_clo_team[$k] : 0; ?>
                            <tr><td><?php echo $r[0]; ?></td><td><span class="badge <?php echo $r[1] . ($my === 0 ? ' c3-zero' : ''); ?>"><?php echo $my; ?></span></td><td><span class="badge <?php echo $r[1] . ($tm === 0 ? ' c3-zero' : ''); ?>"><?php echo $tm; ?></span></td></tr>
                        <?php } ?>
                        </tbody>
                        <tfoot><tr><td>Total</td><td><?php echo isset($c3_clo_my['total_all']) ? (int)$c3_clo_my['total_all'] : 0; ?></td><td><?php echo isset($c3_clo_team['total_all']) ? (int)$c3_clo_team['total_all'] : 0; ?></td></tr></tfoot>
                    </table>
                </div>
            </div>
        </div>

<div class="dash-section-divider" style="margin-top: 20px; margin-bottom: 14px;">
            <h3 class="dash-section-title" style="margin-bottom:0;">
                <span class="dash-section-title-icon">
                    <span></span><span></span>
                </span>
                <span>Team and Trends</span>
            </h3>
        </div>

<?php
$c5_lb = isset($c5['leaderboard']) ? $c5['leaderboard'] : array();
$c5_lb_top = 0;
foreach ($c5_lb as $r) { $c5_lb_top = max($c5_lb_top, (float)$r['achieved']); }
$c5_wk = isset($c5['weekly']) ? $c5['weekly'] : array();
$c5_wk_max = 0; $c5_wk_sum = 0;
foreach ($c5_wk as $w) { $c5_wk_max = max($c5_wk_max, (int)$w['n']); $c5_wk_sum += (int)$w['n']; }
$c5_ag = isset($c5['ageing']) ? $c5['ageing'] : array('d7' => 0, 'd30' => 0, 'older' => 0);
$c5_ag_sum = (int)$c5_ag['d7'] + (int)$c5_ag['d30'] + (int)$c5_ag['older'];
$c5_cl = isset($c5['calls']) ? $c5['calls'] : array();
$c5_who = !empty($c5['is_all']) ? 'All' : 'Team';
if (!function_exists('c5_mins')) {
    function c5_mins($s) { $s = (int)$s; return $s < 60 ? $s . 's' : (floor($s / 3600) > 0 ? floor($s / 3600) . 'h ' : '') . floor(($s % 3600) / 60) . 'm'; }
}
?>
        <div class="row c5-team-row">
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="crm-modern-stat-card c5-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-trophy" style="color:#d97706;"></i> Top This Month</h4>
                        <span class="card-total-pill"><?php echo date('M-y'); ?></span>
                    </div>
                    <?php if (empty($c5_lb)) { ?>
                    <div class="c5-empty">No achievement yet this month</div>
                    <?php } else { ?>
                    <ol class="c5-lb">
                        <?php foreach ($c5_lb as $i => $r) { $tp = (float)$r['target'] > 0 ? (int)round($r['achieved'] / $r['target'] * 100) : null; ?>
                        <li>
                            <span class="c5-lb-rank"><?php echo $i + 1; ?></span>
                            <div class="c5-lb-main">
                                <div class="c5-lb-top"><span class="c5-lb-name" title="<?php echo htmlspecialchars($r['name']); ?>"><?php echo htmlspecialchars($r['name']); ?></span><span class="c5-lb-amt c2-amt"><?php echo $curr . ' ' . number_format((float)$r['achieved'], 0); ?></span></div>
                                <div class="c5-lb-bar"><i style="width: <?php echo $c5_lb_top > 0 ? max(3, (int)round($r['achieved'] / $c5_lb_top * 100)) : 0; ?>%;"></i></div>
                                <div class="c5-lb-sub"><?php echo $tp === null ? 'No target set' : $tp . '% of target'; ?></div>
                            </div>
                        </li>
                        <?php } ?>
                    </ol>
                    <?php } ?>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="crm-modern-stat-card c5-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-line-chart" style="color:#7c3aed;"></i> Added per Week</h4>
                        <span class="card-total-pill"><?php echo $c5_wk_sum; ?> in 8 weeks</span>
                    </div>
                    <?php
                    $c5_w = 260; $c5_h = 92; $c5_pad = 8; $c5_n = count($c5_wk); $c5_pts = array();
                    foreach ($c5_wk as $i => $w) {
                        $x = $c5_n > 1 ? $c5_pad + $i * ($c5_w - 2 * $c5_pad) / ($c5_n - 1) : $c5_w / 2;
                        $y = $c5_h - $c5_pad - ($c5_wk_max > 0 ? ((int)$w['n'] / $c5_wk_max) * ($c5_h - 2 * $c5_pad - 12) : 0);
                        $c5_pts[] = array(round($x, 1), round($y, 1), $w);
                    }
                    $c5_line = implode(' ', array_map(function ($p) { return $p[0] . ',' . $p[1]; }, $c5_pts));
                    ?>
                    <svg class="c5-spark" viewBox="0 0 <?php echo $c5_w; ?> <?php echo $c5_h; ?>" preserveAspectRatio="none" role="img" aria-label="New records added per week">
                        <?php if (!empty($c5_pts)) { ?>
                        <polygon class="c5-spark-area" points="<?php echo $c5_pts[0][0] . ',' . ($c5_h - $c5_pad) . ' ' . $c5_line . ' ' . $c5_pts[count($c5_pts) - 1][0] . ',' . ($c5_h - $c5_pad); ?>"></polygon>
                        <polyline class="c5-spark-line" points="<?php echo $c5_line; ?>"></polyline>
                        <?php foreach ($c5_pts as $p) { ?><circle class="c5-spark-dot" cx="<?php echo $p[0]; ?>" cy="<?php echo $p[1]; ?>" r="3"><title><?php echo date('d M', strtotime($p[2]['from'])) . ' - ' . date('d M', strtotime($p[2]['to'])) . ': ' . (int)$p[2]['n']; ?></title></circle><?php } ?>
                        <?php } ?>
                    </svg>
                    <div class="c5-spark-x"><span><?php echo !empty($c5_wk) ? $c5_wk[0]['label'] : ''; ?></span><span>This week: <b><?php echo !empty($c5_wk) ? (int)$c5_wk[count($c5_wk) - 1]['n'] : 0; ?></b></span></div>
                    <div class="c5-note">New records (database and leads) by the week they were added.</div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="crm-modern-stat-card c5-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-hourglass-half" style="color:#ea580c;"></i> Leads by Last Contact</h4>
                        <span class="card-total-pill"><?php echo $c5_ag_sum; ?> open</span>
                    </div>
                    <?php foreach (array(array('0 to 7 days', 'd7', 'c5-ag-ok'), array('8 to 30 days', 'd30', 'c5-ag-mid'), array('Over 30 days', 'older', 'c5-ag-old')) as $g) { $n = (int)$c5_ag[$g[1]]; $pc = $c5_ag_sum > 0 ? (int)round($n / $c5_ag_sum * 100) : 0; ?>
                    <div class="c5-ag <?php echo $g[2]; ?>">
                        <div class="c5-ag-top"><span><?php echo $g[0]; ?></span><span><b><?php echo $n; ?></b> <em><?php echo $pc; ?>%</em></span></div>
                        <div class="c5-ag-bar"><i style="width: <?php echo $n > 0 ? max(3, $pc) : 0; ?>%;"></i></div>
                    </div>
                    <?php } ?>
                    <div class="c5-note">Days since the last follow-up entry (or since the lead was added).</div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="crm-modern-stat-card c5-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-phone" style="color:#0f8a80;"></i> Calls Today</h4>
                        <span class="card-total-pill"><?php echo date('d M'); ?></span>
                    </div>
                    <table class="c3-clo c5-calls">
                        <thead><tr><th></th><th>My</th><th><?php echo $c5_who; ?></th></tr></thead>
                        <tbody>
                            <?php foreach (array(array('Made', 'total'), array('Answered', 'answered'), array('Not answered', 'not_answered')) as $r) { ?>
                            <tr><td><?php echo $r[0]; ?></td><td><?php echo isset($c5_cl['my'][$r[1]]) ? (int)$c5_cl['my'][$r[1]] : 0; ?></td><td><?php echo isset($c5_cl['team'][$r[1]]) ? (int)$c5_cl['team'][$r[1]] : 0; ?></td></tr>
                            <?php } ?>
                        </tbody>
                        <tfoot><tr><td>Talk time</td><td><?php echo c5_mins(isset($c5_cl['my']['secs']) ? $c5_cl['my']['secs'] : 0); ?></td><td><?php echo c5_mins(isset($c5_cl['team']['secs']) ? $c5_cl['team']['secs'] : 0); ?></td></tr></tfoot>
                    </table>
                    <div class="c5-note">Outgoing calls from the call log (3CX and manual).</div>
                </div>
            </div>
        </div>

<div class="dash-section-divider" style="margin-top: 28px; margin-bottom: 14px;">
            <h3 class="dash-section-title" style="margin-bottom:0;">
                <span class="dash-section-title-icon">
                    <span></span><span></span>
                </span>
                <span>Operational Activity</span>
            </h3>
        </div>

        <?php
            $total_my_tickets = !empty($dashboard_tickets['total_all']) ? (int)$dashboard_tickets['total_all'] : 0;
            $total_my_tasks = !empty($dashboard_tasks['total_all']) ? (int)$dashboard_tasks['total_all'] : (!empty($dashboard_tasks) ? (int)array_sum(array_slice($dashboard_tasks, 0, 5)) : 0);
            $total_my_f = !empty($dashboard_f) ? (int)array_sum(array_slice($dashboard_f, 0, 5)) : 0;
            $total_my_m = !empty($dashboard_m) ? (int)array_sum(array_slice($dashboard_m, 0, 5)) : 0;
            $total_my_vfu = !empty($dashboard_vfu) ? (int)array_sum(array_slice($dashboard_vfu, 0, 5)) : 0;

            $total_team_tickets = !empty($dashboard_Ttickets['total_all']) ? (int)$dashboard_Ttickets['total_all'] : 0;
            $total_team_tasks = !empty($dashboard_Ttasks['total_all']) ? (int)$dashboard_Ttasks['total_all'] : (!empty($dashboard_Ttasks) ? (int)array_sum(array_slice($dashboard_Ttasks, 0, 5)) : 0);
            $total_team_f = !empty($dashboard_Tf) ? (int)array_sum(array_slice($dashboard_Tf, 0, 5)) : 0;
            $total_team_m = !empty($dashboard_Tm) ? (int)array_sum(array_slice($dashboard_Tm, 0, 5)) : 0;
            $total_team_vfu = !empty($dashboard_Tvfu) ? (int)array_sum(array_slice($dashboard_Tvfu, 0, 5)) : 0; ?>

        <div class="crm-op-7col-row">
            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title">
                            <?php if(has_section_perm('tickets', 'read')){ ?>
                                <a href="<?php echo base_url(); ?>manager/tickets?assigned_user=<?php echo (int)$this->session->userdata('manager_id'); ?>&status=Open" style="color:inherit; text-decoration:none;"><i class="fa fa-ticket" style="color:#2563eb;"></i> My Tickets</a>
                            <?php } else { ?>
                                <span><i class="fa fa-ticket" style="color:#2563eb;"></i> My Tickets</span>
                            <?php } ?>
                        </h4>
                        <?php if(has_section_perm('tickets', 'read')){ ?>
                            <a href="<?php echo base_url(); ?>manager/tickets?assigned_user=<?php echo (int)$this->session->userdata('manager_id'); ?>&status=Open" class="card-total-pill" style="text-decoration:none;"><?php echo $total_my_tickets; ?> Total</a>
                        <?php } else { ?>
                            <span class="card-total-pill"><?php echo $total_my_tickets; ?> Total</span>
                        <?php } ?>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; min-height:100px; padding:6px 0;"><canvas id="pieCard_my_tickets" width="100" height="100" style="width:100px; height:100px;"></canvas></div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:14%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:16%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:20%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:30%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li>
                            <a href="<?php echo has_section_perm('tickets', 'read') ? base_url().'manager/tickets?overdue=1' : '#'; ?>" class="crm-modern-stat-chip">
                                <span>All Missed</span>
                                <span class="badge bg-red"><?php echo isset($dashboard_tickets['total_missed']) ? $dashboard_tickets['total_missed'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo has_section_perm('tickets', 'read') ? base_url().'manager/tickets?status=Open' : '#'; ?>" class="crm-modern-stat-chip">
                                <span>Last 7 Days</span>
                                <span class="badge bg-orange"><?php echo isset($dashboard_tickets['total_lastweek']) ? $dashboard_tickets['total_lastweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo has_section_perm('tickets', 'read') ? base_url().'manager/tickets?deadline='.date('d-m-Y') : '#'; ?>" class="crm-modern-stat-chip">
                                <span>Today</span>
                                <span class="badge bg-skyblue"><?php echo isset($dashboard_tickets['total_today']) ? $dashboard_tickets['total_today'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo has_section_perm('tickets', 'read') ? base_url().'manager/tickets?overdue=soon' : '#'; ?>" class="crm-modern-stat-chip">
                                <span>Next 7 Days</span>
                                <span class="badge bg-blue"><?php echo isset($dashboard_tickets['total_nextweek']) ? $dashboard_tickets['total_nextweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo has_section_perm('tickets', 'read') ? base_url().'manager/tickets?status=Open' : '#'; ?>" class="crm-modern-stat-chip">
                                <span>All Future</span>
                                <span class="badge bg-green"><?php echo isset($dashboard_tickets['total_future']) ? $dashboard_tickets['total_future'] : 0; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><a href="<?php echo base_url(); ?>manager/tasks?team=0" style="color:inherit; text-decoration:none;"><i class="fa fa-tasks"></i> My Tasks</a></h4>
                        <a href="<?php echo base_url(); ?>manager/tasks?team=0" class="card-total-pill" style="text-decoration:none;"><?php echo $total_my_tasks; ?> Total</a>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; min-height:100px; padding:6px 0;"><canvas id="pieCard_my_tasks" width="100" height="100" style="width:100px; height:100px;"></canvas></div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:14%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:16%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:20%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:30%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li>
                            <a href="<?php echo base_url(); ?>manager/tasks/followups?type=1" class="crm-modern-stat-chip" data-drilldown="my_tasks" data-type="1" data-label="My Tasks - All Missed">
                                <span>All Missed</span>
                                <span class="badge bg-red"><?php echo isset($dashboard_tasks['total_missed']) ? $dashboard_tasks['total_missed'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/tasks/followups?type=2" class="crm-modern-stat-chip" data-drilldown="my_tasks" data-type="2" data-label="My Tasks - Last 7 Days">
                                <span>Last 7 Days</span>
                                <span class="badge bg-orange"><?php echo isset($dashboard_tasks['total_lastweek']) ? $dashboard_tasks['total_lastweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/tasks/followups?type=3" class="crm-modern-stat-chip" data-drilldown="my_tasks" data-type="3" data-label="My Tasks - Today">
                                <span>Today</span>
                                <span class="badge bg-skyblue"><?php echo isset($dashboard_tasks['total_today']) ? $dashboard_tasks['total_today'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/tasks/followups?type=4" class="crm-modern-stat-chip" data-drilldown="my_tasks" data-type="4" data-label="My Tasks - Next 7 Days">
                                <span>Next 7 Days</span>
                                <span class="badge bg-blue"><?php echo isset($dashboard_tasks['total_nextweek']) ? $dashboard_tasks['total_nextweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/tasks/followups?type=5" class="crm-modern-stat-chip" data-drilldown="my_tasks" data-type="5" data-label="My Tasks - All Future">
                                <span>All Future</span>
                                <span class="badge bg-green"><?php echo isset($dashboard_tasks['total_future']) ? $dashboard_tasks['total_future'] : 0; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-calendar-check-o"></i> My Followups</h4>
                        <span class="card-total-pill"><?php echo $total_my_f; ?> Total</span>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; min-height:100px; padding:6px 0;"><canvas id="pieCard_my_f" width="100" height="100" style="width:100px; height:100px;"></canvas></div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:12%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:20%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:24%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:24%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads/followups?type=1" class="crm-modern-stat-chip" data-drilldown="my_followups" data-type="1" data-label="My Followups - All Missed">
                                <span>All Missed</span>
                                <span class="badge bg-red"><?php echo isset($dashboard_f['total_missed']) ? $dashboard_f['total_missed'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads/followups?type=2" class="crm-modern-stat-chip" data-drilldown="my_followups" data-type="2" data-label="My Followups - Last 7 Days">
                                <span>Last 7 Days</span>
                                <span class="badge bg-orange"><?php echo isset($dashboard_f['total_lastweek']) ? $dashboard_f['total_lastweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads/followups?type=3" class="crm-modern-stat-chip" data-drilldown="my_followups" data-type="3" data-label="My Followups - Today">
                                <span>Today</span>
                                <span class="badge bg-skyblue"><?php echo isset($dashboard_f['total_today']) ? $dashboard_f['total_today'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads/followups?type=4" class="crm-modern-stat-chip" data-drilldown="my_followups" data-type="4" data-label="My Followups - Next 7 Days">
                                <span>Next 7 Days</span>
                                <span class="badge bg-blue"><?php echo isset($dashboard_f['total_nextweek']) ? $dashboard_f['total_nextweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads/followups?type=5" class="crm-modern-stat-chip" data-drilldown="my_followups" data-type="5" data-label="My Followups - All Future">
                                <span>All Future</span>
                                <span class="badge bg-green"><?php echo isset($dashboard_f['total_future']) ? $dashboard_f['total_future'] : 0; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-handshake-o"></i> My Meetings</h4>
                        <span class="card-total-pill"><?php echo $total_my_m; ?> Total</span>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; min-height:100px; padding:6px 0;"><canvas id="pieCard_my_m" width="100" height="100" style="width:100px; height:100px;"></canvas></div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:10%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:18%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:20%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:26%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:26%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads/meetings?type=1" class="crm-modern-stat-chip" data-drilldown="my_meetings" data-type="1" data-label="My Meetings - All Missed">
                                <span>All Missed</span>
                                <span class="badge bg-red"><?php echo isset($dashboard_m['total_missed']) ? $dashboard_m['total_missed'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads/meetings?type=2" class="crm-modern-stat-chip" data-drilldown="my_meetings" data-type="2" data-label="My Meetings - Last 7 Days">
                                <span>Last 7 Days</span>
                                <span class="badge bg-orange"><?php echo isset($dashboard_m['total_lastweek']) ? $dashboard_m['total_lastweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads/meetings?type=3" class="crm-modern-stat-chip" data-drilldown="my_meetings" data-type="3" data-label="My Meetings - Today">
                                <span>Today</span>
                                <span class="badge bg-skyblue"><?php echo isset($dashboard_m['total_today']) ? $dashboard_m['total_today'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads/meetings?type=4" class="crm-modern-stat-chip" data-drilldown="my_meetings" data-type="4" data-label="My Meetings - Next 7 Days">
                                <span>Next 7 Days</span>
                                <span class="badge bg-blue"><?php echo isset($dashboard_m['total_nextweek']) ? $dashboard_m['total_nextweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads/meetings?type=5" class="crm-modern-stat-chip" data-drilldown="my_meetings" data-type="5" data-label="My Meetings - All Future">
                                <span>All Future</span>
                                <span class="badge bg-green"><?php echo isset($dashboard_m['total_future']) ? $dashboard_m['total_future'] : 0; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-briefcase"></i> My Vertical FU</h4>
                        <span class="card-total-pill"><?php echo $total_my_vfu; ?> Total</span>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; min-height:100px; padding:6px 0;"><canvas id="pieCard_my_vfu" width="100" height="100" style="width:100px; height:100px;"></canvas></div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:14%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:16%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:20%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:30%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads?vertical_fu=1" class="crm-modern-stat-chip" data-drilldown="my_vertical_fu" data-type="1" data-label="My Vertical FU - All Missed">
                                <span>All Missed</span>
                                <span class="badge bg-red"><?php echo isset($dashboard_vfu['total_missed']) ? $dashboard_vfu['total_missed'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads?vertical_fu=2" class="crm-modern-stat-chip" data-drilldown="my_vertical_fu" data-type="2" data-label="My Vertical FU - Last 7 Days">
                                <span>Last 7 Days</span>
                                <span class="badge bg-orange"><?php echo isset($dashboard_vfu['total_lastweek']) ? $dashboard_vfu['total_lastweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads?vertical_fu=3" class="crm-modern-stat-chip" data-drilldown="my_vertical_fu" data-type="3" data-label="My Vertical FU - Today">
                                <span>Today</span>
                                <span class="badge bg-skyblue"><?php echo isset($dashboard_vfu['total_today']) ? $dashboard_vfu['total_today'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads?vertical_fu=4" class="crm-modern-stat-chip" data-drilldown="my_vertical_fu" data-type="4" data-label="My Vertical FU - Next 7 Days">
                                <span>Next 7 Days</span>
                                <span class="badge bg-blue"><?php echo isset($dashboard_vfu['total_nextweek']) ? $dashboard_vfu['total_nextweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads?vertical_fu=5" class="crm-modern-stat-chip" data-drilldown="my_vertical_fu" data-type="5" data-label="My Vertical FU - All Future">
                                <span>All Future</span>
                                <span class="badge bg-green"><?php echo isset($dashboard_vfu['total_future']) ? $dashboard_vfu['total_future'] : 0; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-database" style="color:#0072f0;"></i> My Database</h4>
                        <span class="card-total-pill"><?php echo (int)$dashboard_db_ops_my['total_all']; ?> Total</span>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; min-height:100px; padding:6px 0;"><canvas id="pieCard_my_db" width="100" height="100" style="width:100px; height:100px;"></canvas></div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:14%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:16%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:20%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:30%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li><a href="<?php echo base_url(); ?>manager/leads/followups?type=1&stage=database" class="crm-modern-stat-chip"><span>All Missed</span><span class="badge bg-red"><?php echo (int)$dashboard_db_ops_my['total_missed']; ?></span></a></li>
                        <li><a href="<?php echo base_url(); ?>manager/leads/followups?type=2&stage=database" class="crm-modern-stat-chip"><span>Last 7 Days</span><span class="badge bg-orange"><?php echo (int)$dashboard_db_ops_my['total_lastweek']; ?></span></a></li>
                        <li><a href="<?php echo base_url(); ?>manager/leads/followups?type=3&stage=database" class="crm-modern-stat-chip"><span>Today</span><span class="badge bg-skyblue"><?php echo (int)$dashboard_db_ops_my['total_today']; ?></span></a></li>
                        <li><a href="<?php echo base_url(); ?>manager/leads/followups?type=4&stage=database" class="crm-modern-stat-chip"><span>Next 7 Days</span><span class="badge bg-blue"><?php echo (int)$dashboard_db_ops_my['total_nextweek']; ?></span></a></li>
                        <li><a href="<?php echo base_url(); ?>manager/leads/followups?type=5&stage=database" class="crm-modern-stat-chip"><span>All Future</span><span class="badge bg-green"><?php echo (int)$dashboard_db_ops_my['total_future']; ?></span></a></li>
                    </ul>
                </div>
            </div>

            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-bullseye" style="color:#0072f0;"></i> My Leads</h4>
                        <span class="card-total-pill"><?php echo (int)$dashboard_lead_ops_my['total_all']; ?> Total</span>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; min-height:100px; padding:6px 0;"><canvas id="pieCard_my_leads" width="100" height="100" style="width:100px; height:100px;"></canvas></div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:14%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:16%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:20%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:30%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li><a href="<?php echo base_url(); ?>manager/leads/followups?type=1&stage=lead" class="crm-modern-stat-chip"><span>All Missed</span><span class="badge bg-red"><?php echo (int)$dashboard_lead_ops_my['total_missed']; ?></span></a></li>
                        <li><a href="<?php echo base_url(); ?>manager/leads/followups?type=2&stage=lead" class="crm-modern-stat-chip"><span>Last 7 Days</span><span class="badge bg-orange"><?php echo (int)$dashboard_lead_ops_my['total_lastweek']; ?></span></a></li>
                        <li><a href="<?php echo base_url(); ?>manager/leads/followups?type=3&stage=lead" class="crm-modern-stat-chip"><span>Today</span><span class="badge bg-skyblue"><?php echo (int)$dashboard_lead_ops_my['total_today']; ?></span></a></li>
                        <li><a href="<?php echo base_url(); ?>manager/leads/followups?type=4&stage=lead" class="crm-modern-stat-chip"><span>Next 7 Days</span><span class="badge bg-blue"><?php echo (int)$dashboard_lead_ops_my['total_nextweek']; ?></span></a></li>
                        <li><a href="<?php echo base_url(); ?>manager/leads/followups?type=5&stage=lead" class="crm-modern-stat-chip"><span>All Future</span><span class="badge bg-green"><?php echo (int)$dashboard_lead_ops_my['total_future']; ?></span></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="crm-op-7col-row" style="margin-top:10px;">
            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title">
                            <?php if(has_section_perm('tickets', 'read')){ ?>
                                <a href="<?php echo base_url(); ?>manager/tickets?team=1&status=Open" style="color:inherit; text-decoration:none;"><i class="fa fa-ticket" style="color:#2563eb;"></i> Team Tickets</a>
                            <?php } else { ?>
                                <span><i class="fa fa-ticket" style="color:#2563eb;"></i> Team Tickets</span>
                            <?php } ?>
                        </h4>
                        <?php if(has_section_perm('tickets', 'read')){ ?>
                            <a href="<?php echo base_url(); ?>manager/tickets?team=1&status=Open" class="card-total-pill" style="text-decoration:none;"><?php echo $total_team_tickets; ?> Total</a>
                        <?php } else { ?>
                            <span class="card-total-pill"><?php echo $total_team_tickets; ?> Total</span>
                        <?php } ?>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; min-height:100px; padding:6px 0;"><canvas id="pieCard_team_tickets" width="100" height="100" style="width:100px; height:100px;"></canvas></div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:14%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:16%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:20%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:30%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li>
                            <a href="<?php echo has_section_perm('tickets', 'read') ? base_url().'manager/tickets?team=1&overdue=1' : '#'; ?>" class="crm-modern-stat-chip">
                                <span>All Missed</span>
                                <span class="badge bg-red"><?php echo isset($dashboard_Ttickets['total_missed']) ? $dashboard_Ttickets['total_missed'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo has_section_perm('tickets', 'read') ? base_url().'manager/tickets?team=1&status=Open' : '#'; ?>" class="crm-modern-stat-chip">
                                <span>Last 7 Days</span>
                                <span class="badge bg-orange"><?php echo isset($dashboard_Ttickets['total_lastweek']) ? $dashboard_Ttickets['total_lastweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo has_section_perm('tickets', 'read') ? base_url().'manager/tickets?team=1&deadline='.date('d-m-Y') : '#'; ?>" class="crm-modern-stat-chip">
                                <span>Today</span>
                                <span class="badge bg-skyblue"><?php echo isset($dashboard_Ttickets['total_today']) ? $dashboard_Ttickets['total_today'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo has_section_perm('tickets', 'read') ? base_url().'manager/tickets?team=1&overdue=soon' : '#'; ?>" class="crm-modern-stat-chip">
                                <span>Next 7 Days</span>
                                <span class="badge bg-blue"><?php echo isset($dashboard_Ttickets['total_nextweek']) ? $dashboard_Ttickets['total_nextweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo has_section_perm('tickets', 'read') ? base_url().'manager/tickets?team=1&status=Open' : '#'; ?>" class="crm-modern-stat-chip">
                                <span>All Future</span>
                                <span class="badge bg-green"><?php echo isset($dashboard_Ttickets['total_future']) ? $dashboard_Ttickets['total_future'] : 0; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><a href="<?php echo base_url(); ?>manager/team/tasks/internal" style="color:inherit; text-decoration:none;"><i class="fa fa-tasks"></i> Team Tasks</a></h4>
                        <a href="<?php echo base_url(); ?>manager/team/tasks/internal" class="card-total-pill" style="text-decoration:none;"><?php echo $total_team_tasks; ?> Total</a>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; min-height:100px; padding:6px 0;"><canvas id="pieCard_team_tasks" width="100" height="100" style="width:100px; height:100px;"></canvas></div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:14%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:16%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:20%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:30%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li>
                            <a href="<?php echo base_url(); ?>manager/tasks/followups?type=1&team=1" class="crm-modern-stat-chip" data-drilldown="team_tasks" data-type="1" data-label="Team Tasks - All Missed">
                                <span>All Missed</span>
                                <span class="badge bg-red"><?php echo isset($dashboard_Ttasks['total_missed']) ? $dashboard_Ttasks['total_missed'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/tasks/followups?type=2&team=1" class="crm-modern-stat-chip" data-drilldown="team_tasks" data-type="2" data-label="Team Tasks - Last 7 Days">
                                <span>Last 7 Days</span>
                                <span class="badge bg-orange"><?php echo isset($dashboard_Ttasks['total_lastweek']) ? $dashboard_Ttasks['total_lastweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/tasks/followups?type=3&team=1" class="crm-modern-stat-chip" data-drilldown="team_tasks" data-type="3" data-label="Team Tasks - Today">
                                <span>Today</span>
                                <span class="badge bg-skyblue"><?php echo isset($dashboard_Ttasks['total_today']) ? $dashboard_Ttasks['total_today'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/tasks/followups?type=4&team=1" class="crm-modern-stat-chip" data-drilldown="team_tasks" data-type="4" data-label="Team Tasks - Next 7 Days">
                                <span>Next 7 Days</span>
                                <span class="badge bg-blue"><?php echo isset($dashboard_Ttasks['total_nextweek']) ? $dashboard_Ttasks['total_nextweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/tasks/followups?type=5&team=1" class="crm-modern-stat-chip" data-drilldown="team_tasks" data-type="5" data-label="Team Tasks - All Future">
                                <span>All Future</span>
                                <span class="badge bg-green"><?php echo isset($dashboard_Ttasks['total_future']) ? $dashboard_Ttasks['total_future'] : 0; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-calendar-check-o"></i> Team Followups</h4>
                        <span class="card-total-pill"><?php echo $total_team_f; ?> Total</span>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; min-height:100px; padding:6px 0;"><canvas id="pieCard_team_f" width="100" height="100" style="width:100px; height:100px;"></canvas></div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:10%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:18%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:16%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:26%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:30%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/followups?type=1" class="crm-modern-stat-chip" data-drilldown="team_followups" data-type="1" data-label="Team Followups - All Missed">
                                <span>All Missed</span>
                                <span class="badge bg-red"><?php echo isset($dashboard_Tf['total_missed']) ? $dashboard_Tf['total_missed'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/followups?type=2" class="crm-modern-stat-chip" data-drilldown="team_followups" data-type="2" data-label="Team Followups - Last 7 Days">
                                <span>Last 7 Days</span>
                                <span class="badge bg-orange"><?php echo isset($dashboard_Tf['total_lastweek']) ? $dashboard_Tf['total_lastweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/followups?type=3" class="crm-modern-stat-chip" data-drilldown="team_followups" data-type="3" data-label="Team Followups - Today">
                                <span>Today</span>
                                <span class="badge bg-skyblue"><?php echo isset($dashboard_Tf['total_today']) ? $dashboard_Tf['total_today'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/followups?type=4" class="crm-modern-stat-chip" data-drilldown="team_followups" data-type="4" data-label="Team Followups - Next 7 Days">
                                <span>Next 7 Days</span>
                                <span class="badge bg-blue"><?php echo isset($dashboard_Tf['total_nextweek']) ? $dashboard_Tf['total_nextweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/followups?type=5" class="crm-modern-stat-chip" data-drilldown="team_followups" data-type="5" data-label="Team Followups - All Future">
                                <span>All Future</span>
                                <span class="badge bg-green"><?php echo isset($dashboard_Tf['total_future']) ? $dashboard_Tf['total_future'] : 0; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-handshake-o"></i> Team Meetings</h4>
                        <span class="card-total-pill"><?php echo $total_team_m; ?> Total</span>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; min-height:100px; padding:6px 0;"><canvas id="pieCard_team_m" width="100" height="100" style="width:100px; height:100px;"></canvas></div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:8%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:14%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:26%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:32%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/meetings?type=1" class="crm-modern-stat-chip" data-drilldown="team_meetings" data-type="1" data-label="Team Meetings - All Missed">
                                <span>All Missed</span>
                                <span class="badge bg-red"><?php echo isset($dashboard_Tm['total_missed']) ? $dashboard_Tm['total_missed'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/meetings?type=2" class="crm-modern-stat-chip" data-drilldown="team_meetings" data-type="2" data-label="Team Meetings - Last 7 Days">
                                <span>Last 7 Days</span>
                                <span class="badge bg-orange"><?php echo isset($dashboard_Tm['total_lastweek']) ? $dashboard_Tm['total_lastweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/meetings?type=3" class="crm-modern-stat-chip" data-drilldown="team_meetings" data-type="3" data-label="Team Meetings - Today">
                                <span>Today</span>
                                <span class="badge bg-skyblue"><?php echo isset($dashboard_Tm['total_today']) ? $dashboard_Tm['total_today'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/meetings?type=4" class="crm-modern-stat-chip" data-drilldown="team_meetings" data-type="4" data-label="Team Meetings - Next 7 Days">
                                <span>Next 7 Days</span>
                                <span class="badge bg-blue"><?php echo isset($dashboard_Tm['total_nextweek']) ? $dashboard_Tm['total_nextweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/meetings?type=5" class="crm-modern-stat-chip" data-drilldown="team_meetings" data-type="5" data-label="Team Meetings - All Future">
                                <span>All Future</span>
                                <span class="badge bg-green"><?php echo isset($dashboard_Tm['total_future']) ? $dashboard_Tm['total_future'] : 0; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-briefcase"></i> Team Vertical FU</h4>
                        <span class="card-total-pill"><?php echo $total_team_vfu; ?> Total</span>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; min-height:100px; padding:6px 0;"><canvas id="pieCard_team_vfu" width="100" height="100" style="width:100px; height:100px;"></canvas></div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:12%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:14%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:20%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:34%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/verticals?type=1" class="crm-modern-stat-chip" data-drilldown="team_verticals" data-type="1" data-label="Team Vertical FU - All Missed">
                                <span>All Missed</span>
                                <span class="badge bg-red"><?php echo isset($dashboard_Tvfu['total_missed']) ? $dashboard_Tvfu['total_missed'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/verticals?type=2" class="crm-modern-stat-chip" data-drilldown="team_verticals" data-type="2" data-label="Team Vertical FU - Last 7 Days">
                                <span>Last 7 Days</span>
                                <span class="badge bg-orange"><?php echo isset($dashboard_Tvfu['total_lastweek']) ? $dashboard_Tvfu['total_lastweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/verticals?type=3" class="crm-modern-stat-chip" data-drilldown="team_verticals" data-type="3" data-label="Team Vertical FU - Today">
                                <span>Today</span>
                                <span class="badge bg-skyblue"><?php echo isset($dashboard_Tvfu['total_today']) ? $dashboard_Tvfu['total_today'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/verticals?type=4" class="crm-modern-stat-chip" data-drilldown="team_verticals" data-type="4" data-label="Team Vertical FU - Next 7 Days">
                                <span>Next 7 Days</span>
                                <span class="badge bg-blue"><?php echo isset($dashboard_Tvfu['total_nextweek']) ? $dashboard_Tvfu['total_nextweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/verticals?type=5" class="crm-modern-stat-chip" data-drilldown="team_verticals" data-type="5" data-label="Team Vertical FU - All Future">
                                <span>All Future</span>
                                <span class="badge bg-green"><?php echo isset($dashboard_Tvfu['total_future']) ? $dashboard_Tvfu['total_future'] : 0; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-database" style="color:#0072f0;"></i> Team Database</h4>
                        <span class="card-total-pill"><?php echo (int)$dashboard_db_ops_team['total_all']; ?> Total</span>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; min-height:100px; padding:6px 0;"><canvas id="pieCard_team_db" width="100" height="100" style="width:100px; height:100px;"></canvas></div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:14%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:16%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:20%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:30%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li><a href="<?php echo base_url(); ?>manager/team/followups?type=1&stage=database" class="crm-modern-stat-chip"><span>All Missed</span><span class="badge bg-red"><?php echo (int)$dashboard_db_ops_team['total_missed']; ?></span></a></li>
                        <li><a href="<?php echo base_url(); ?>manager/team/followups?type=2&stage=database" class="crm-modern-stat-chip"><span>Last 7 Days</span><span class="badge bg-orange"><?php echo (int)$dashboard_db_ops_team['total_lastweek']; ?></span></a></li>
                        <li><a href="<?php echo base_url(); ?>manager/team/followups?type=3&stage=database" class="crm-modern-stat-chip"><span>Today</span><span class="badge bg-skyblue"><?php echo (int)$dashboard_db_ops_team['total_today']; ?></span></a></li>
                        <li><a href="<?php echo base_url(); ?>manager/team/followups?type=4&stage=database" class="crm-modern-stat-chip"><span>Next 7 Days</span><span class="badge bg-blue"><?php echo (int)$dashboard_db_ops_team['total_nextweek']; ?></span></a></li>
                        <li><a href="<?php echo base_url(); ?>manager/team/followups?type=5&stage=database" class="crm-modern-stat-chip"><span>All Future</span><span class="badge bg-green"><?php echo (int)$dashboard_db_ops_team['total_future']; ?></span></a></li>
                    </ul>
                </div>
            </div>

            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-bullseye" style="color:#0072f0;"></i> Team Leads</h4>
                        <span class="card-total-pill"><?php echo (int)$dashboard_lead_ops_team['total_all']; ?> Total</span>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; min-height:100px; padding:6px 0;"><canvas id="pieCard_team_leads" width="100" height="100" style="width:100px; height:100px;"></canvas></div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:14%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:16%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:20%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:30%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li><a href="<?php echo base_url(); ?>manager/team/followups?type=1&stage=lead" class="crm-modern-stat-chip"><span>All Missed</span><span class="badge bg-red"><?php echo (int)$dashboard_lead_ops_team['total_missed']; ?></span></a></li>
                        <li><a href="<?php echo base_url(); ?>manager/team/followups?type=2&stage=lead" class="crm-modern-stat-chip"><span>Last 7 Days</span><span class="badge bg-orange"><?php echo (int)$dashboard_lead_ops_team['total_lastweek']; ?></span></a></li>
                        <li><a href="<?php echo base_url(); ?>manager/team/followups?type=3&stage=lead" class="crm-modern-stat-chip"><span>Today</span><span class="badge bg-skyblue"><?php echo (int)$dashboard_lead_ops_team['total_today']; ?></span></a></li>
                        <li><a href="<?php echo base_url(); ?>manager/team/followups?type=4&stage=lead" class="crm-modern-stat-chip"><span>Next 7 Days</span><span class="badge bg-blue"><?php echo (int)$dashboard_lead_ops_team['total_nextweek']; ?></span></a></li>
                        <li><a href="<?php echo base_url(); ?>manager/team/followups?type=5&stage=lead" class="crm-modern-stat-chip"><span>All Future</span><span class="badge bg-green"><?php echo (int)$dashboard_lead_ops_team['total_future']; ?></span></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="dash-section-divider" style="margin-top: 18px; margin-bottom: 12px;">
            <h3 class="dash-section-title" style="margin-bottom:0;">
                <span class="dash-section-title-icon">
                    <span></span><span></span>
                </span>
                <span>Database, Leads and Orders</span>
            </h3>
        </div>

        <!-- Classic 5: Database, Leads and Orders side by side, My on the first row and Team on the second. -->
        <div class="row c5-states-row">
            <div class="col-md-4 col-xs-12">
                <div class="crm-modern-stat-card" style="margin-bottom: 12px;">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-database" style="color:#0072f0;"></i> My Database States</h4>
                        <span class="card-total-pill"><?php echo number_format($my_db_total); ?> Total</span>
                    </div>
                    <div class="crm-seg-bar">
                        <?php if (!empty($dashboard_db_state) && $my_db_total > 0) {
                            foreach($dashboard_db_state as $li) {
                                $pct = round(((int)$li['lead_count'] / $my_db_total) * 100, 1);
                                if ($pct > 0) {
                                    echo '<div class="crm-seg" style="width:' . $pct . '%; opacity:0.85;" title="' . htmlspecialchars($li['status']) . ': ' . (int)$li['lead_count'] . '"></div>';
                                }
                            }
                        } else { ?>
                            <div class="crm-seg" style="width:100%; opacity:0.3;"></div>
                        <?php } ?>
                    </div>
                    <ul class="crm-modern-stat-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(150px, 1fr)); gap:6px;">
                        <?php if (!empty($dashboard_db_state)) { foreach($dashboard_db_state as $li) { ?>
                            <li style="margin:0;">
                                <a href="<?php echo base_url(); ?>manager/leads?status%5B%5D=<?php echo $li['status_id']; ?>" class="crm-modern-stat-chip" style="padding:6px 9px;">
                                    <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-right:4px;" title="<?php echo htmlspecialchars($li['status']); ?>"><?php echo htmlspecialchars($li['status']); ?></span>
                                    <span class="badge" style="background:var(--crm-card-bg, #ffffff); color:var(--crm-text, #0f172a); border:1px solid var(--crm-border, #cbd5e1); font-weight:700; border-radius:4px; padding:2px 7px; font-size:11px; flex-shrink:0;"><?php echo (int)$li['lead_count']; ?></span>
                                </a>
                            </li>
                        <?php } } else { ?>
                            <li style="grid-column: 1 / -1;"><span class="text-muted" style="font-size:12px;">No database records found</span></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="crm-modern-stat-card" style="margin-bottom: 12px;">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-bullseye" style="color:#0072f0;"></i> My Lead States</h4>
                        <span class="card-total-pill"><?php echo number_format($my_lead_total); ?> Total</span>
                    </div>
                    <div class="crm-seg-bar">
                        <?php if (!empty($dashboard_lead_state) && $my_lead_total > 0) {
                            foreach($dashboard_lead_state as $li) {
                                $pct = round(((int)$li['lead_count'] / $my_lead_total) * 100, 1);
                                if ($pct > 0) {
                                    echo '<div class="crm-seg" style="width:' . $pct . '%; opacity:0.85;" title="' . htmlspecialchars($li['status']) . ': ' . (int)$li['lead_count'] . '"></div>';
                                }
                            }
                        } else { ?>
                            <div class="crm-seg" style="width:100%; opacity:0.3;"></div>
                        <?php } ?>
                    </div>
                    <ul class="crm-modern-stat-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(150px, 1fr)); gap:6px;">
                        <?php if (!empty($dashboard_lead_state)) { foreach($dashboard_lead_state as $li) { ?>
                            <li style="margin:0;">
                                <a href="<?php echo base_url(); ?>manager/leads/view_leads?status%5B%5D=<?php echo $li['status_id']; ?>" class="crm-modern-stat-chip" style="padding:6px 9px;">
                                    <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-right:4px;" title="<?php echo htmlspecialchars($li['status']); ?>"><?php echo htmlspecialchars($li['status']); ?></span>
                                    <span class="badge" style="background:var(--crm-card-bg, #ffffff); color:var(--crm-text, #0f172a); border:1px solid var(--crm-border, #cbd5e1); font-weight:700; border-radius:4px; padding:2px 7px; font-size:11px; flex-shrink:0;"><?php echo (int)$li['lead_count']; ?></span>
                                </a>
                            </li>
                        <?php } } else { ?>
                            <li style="grid-column: 1 / -1;"><span class="text-muted" style="font-size:12px;">No leads found</span></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="crm-modern-stat-card" style="margin-bottom: 12px;">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-shopping-cart" style="color:#0072f0;"></i> My Orders</h4>
                        <span class="card-total-pill"><?php echo number_format($my_order_total); ?> Total</span>
                    </div>
                    <div class="crm-seg-bar">
                        <?php if (!empty($dashboard_order_state) && $my_order_total > 0) {
                            foreach($dashboard_order_state as $li) {
                                $pct = round(((int)$li['order_count'] / $my_order_total) * 100, 1);
                                if ($pct > 0) {
                                    echo '<div class="crm-seg" style="width:' . $pct . '%; opacity:0.85;" title="' . htmlspecialchars($li['status']) . ': ' . (int)$li['order_count'] . '"></div>';
                                }
                            }
                        } else { ?>
                            <div class="crm-seg" style="width:100%; opacity:0.3;"></div>
                        <?php } ?>
                    </div>
                    <ul class="crm-modern-stat-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(150px, 1fr)); gap:6px;">
                        <?php if (!empty($dashboard_order_state)) { foreach($dashboard_order_state as $li) { ?>
                            <li style="margin:0;">
                                <a href="<?php echo base_url(); ?>manager/orders/my?status=<?php echo urlencode($li['status_key']); ?>" class="crm-modern-stat-chip" style="padding:6px 9px;">
                                    <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-right:4px;" title="<?php echo htmlspecialchars($li['status']); ?>"><?php echo htmlspecialchars($li['status']); ?></span>
                                    <span class="badge" style="background:var(--crm-card-bg, #ffffff); color:var(--crm-text, #0f172a); border:1px solid var(--crm-border, #cbd5e1); font-weight:700; border-radius:4px; padding:2px 7px; font-size:11px; flex-shrink:0;"><?php echo (int)$li['order_count']; ?></span>
                                </a>
                            </li>
                        <?php } } else { ?>
                            <li style="grid-column: 1 / -1;"><span class="text-muted" style="font-size:12px;">No orders found</span></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row c5-states-row" style="margin-top: 6px;">
            <div class="col-md-4 col-xs-12">
                <div class="crm-modern-stat-card" style="margin-bottom: 12px;">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-database" style="color:#0072f0;"></i> Team Database States</h4>
                        <span class="card-total-pill"><?php echo number_format($team_db_total); ?> Total</span>
                    </div>
                    <div class="crm-seg-bar">
                        <?php if (!empty($dashboard_db_state_team) && $team_db_total > 0) {
                            foreach($dashboard_db_state_team as $li) {
                                $pct = round(((int)$li['lead_count'] / $team_db_total) * 100, 1);
                                if ($pct > 0) {
                                    echo '<div class="crm-seg" style="width:' . $pct . '%; opacity:0.85;" title="' . htmlspecialchars($li['status']) . ': ' . (int)$li['lead_count'] . '"></div>';
                                }
                            }
                        } else { ?>
                            <div class="crm-seg" style="width:100%; opacity:0.3;"></div>
                        <?php } ?>
                    </div>
                    <ul class="crm-modern-stat-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(150px, 1fr)); gap:6px;">
                        <?php if (!empty($dashboard_db_state_team)) { foreach($dashboard_db_state_team as $li) { ?>
                            <li style="margin:0;">
                                <a href="<?php echo base_url(); ?>manager/team/database?status%5B%5D=<?php echo $li['status_id']; ?>" class="crm-modern-stat-chip" style="padding:6px 9px;">
                                    <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-right:4px;" title="<?php echo htmlspecialchars($li['status']); ?>"><?php echo htmlspecialchars($li['status']); ?></span>
                                    <span class="badge" style="background:var(--crm-card-bg, #ffffff); color:var(--crm-text, #0f172a); border:1px solid var(--crm-border, #cbd5e1); font-weight:700; border-radius:4px; padding:2px 7px; font-size:11px; flex-shrink:0;"><?php echo (int)$li['lead_count']; ?></span>
                                </a>
                            </li>
                        <?php } } else { ?>
                            <li style="grid-column: 1 / -1;"><span class="text-muted" style="font-size:12px;">No team database records found</span></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="crm-modern-stat-card" style="margin-bottom: 12px;">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-bullseye" style="color:#0072f0;"></i> Team Lead States</h4>
                        <span class="card-total-pill"><?php echo number_format($team_lead_total); ?> Total</span>
                    </div>
                    <div class="crm-seg-bar">
                        <?php if (!empty($dashboard_lead_state_team) && $team_lead_total > 0) {
                            foreach($dashboard_lead_state_team as $li) {
                                $pct = round(((int)$li['lead_count'] / $team_lead_total) * 100, 1);
                                if ($pct > 0) {
                                    echo '<div class="crm-seg" style="width:' . $pct . '%; opacity:0.85;" title="' . htmlspecialchars($li['status']) . ': ' . (int)$li['lead_count'] . '"></div>';
                                }
                            }
                        } else { ?>
                            <div class="crm-seg" style="width:100%; opacity:0.3;"></div>
                        <?php } ?>
                    </div>
                    <ul class="crm-modern-stat-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(150px, 1fr)); gap:6px;">
                        <?php if (!empty($dashboard_lead_state_team)) { foreach($dashboard_lead_state_team as $li) { ?>
                            <li style="margin:0;">
                                <a href="<?php echo base_url(); ?>manager/team/leads?status%5B%5D=<?php echo $li['status_id']; ?>" class="crm-modern-stat-chip" style="padding:6px 9px;">
                                    <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-right:4px;" title="<?php echo htmlspecialchars($li['status']); ?>"><?php echo htmlspecialchars($li['status']); ?></span>
                                    <span class="badge" style="background:var(--crm-card-bg, #ffffff); color:var(--crm-text, #0f172a); border:1px solid var(--crm-border, #cbd5e1); font-weight:700; border-radius:4px; padding:2px 7px; font-size:11px; flex-shrink:0;"><?php echo (int)$li['lead_count']; ?></span>
                                </a>
                            </li>
                        <?php } } else { ?>
                            <li style="grid-column: 1 / -1;"><span class="text-muted" style="font-size:12px;">No team leads found</span></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="crm-modern-stat-card" style="margin-bottom: 12px;">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-shopping-cart" style="color:#0072f0;"></i> Team Orders</h4>
                        <span class="card-total-pill"><?php echo number_format($team_order_total); ?> Total</span>
                    </div>
                    <div class="crm-seg-bar">
                        <?php if (!empty($dashboard_order_state_team) && $team_order_total > 0) {
                            foreach($dashboard_order_state_team as $li) {
                                $pct = round(((int)$li['order_count'] / $team_order_total) * 100, 1);
                                if ($pct > 0) {
                                    echo '<div class="crm-seg" style="width:' . $pct . '%; opacity:0.85;" title="' . htmlspecialchars($li['status']) . ': ' . (int)$li['order_count'] . '"></div>';
                                }
                            }
                        } else { ?>
                            <div class="crm-seg" style="width:100%; opacity:0.3;"></div>
                        <?php } ?>
                    </div>
                    <ul class="crm-modern-stat-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(150px, 1fr)); gap:6px;">
                        <?php if (!empty($dashboard_order_state_team)) { foreach($dashboard_order_state_team as $li) { ?>
                            <li style="margin:0;">
                                <a href="<?php echo base_url(); ?>manager/team/orders?status=<?php echo urlencode($li['status_key']); ?>" class="crm-modern-stat-chip" style="padding:6px 9px;">
                                    <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-right:4px;" title="<?php echo htmlspecialchars($li['status']); ?>"><?php echo htmlspecialchars($li['status']); ?></span>
                                    <span class="badge" style="background:var(--crm-card-bg, #ffffff); color:var(--crm-text, #0f172a); border:1px solid var(--crm-border, #cbd5e1); font-weight:700; border-radius:4px; padding:2px 7px; font-size:11px; flex-shrink:0;"><?php echo (int)$li['order_count']; ?></span>
                                </a>
                            </li>
                        <?php } } else { ?>
                            <li style="grid-column: 1 / -1;"><span class="text-muted" style="font-size:12px;">No team orders found</span></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="dash-section-divider" style="margin-top: 18px; margin-bottom: 12px;">
            <h3 class="dash-section-title" style="margin-bottom:0;">
                <span class="dash-section-title-icon">
                    <span></span><span></span>
                </span>
                <span>Achievement and Projection</span>
            </h3>
        </div>
<?php
// Last 12 months, from the same numbers as the two Achievement charts below.
$c5_sum = function ($a) {
    $t = isset($a['target']) ? array_map('floatval', (array)$a['target']) : array();
    $v = isset($a['achievement']) ? array_map('floatval', (array)$a['achievement']) : array();
    $l = isset($a['labels']) ? (array)$a['labels'] : array();
    $best = null;
    foreach ($v as $i => $x) { if ($x > 0 && ($best === null || $x > $v[$best])) $best = $i; }
    $n = count($v);
    $li = $n >= 2 ? $n - 2 : null; // last full month
    return array(
        'target' => array_sum($t), 'achieved' => array_sum($v),
        'best' => $best === null ? null : array('label' => isset($l[$best]) ? $l[$best] : '', 'value' => $v[$best]),
        'last' => $li === null ? null : array('label' => isset($l[$li]) ? $l[$li] : '', 'value' => $v[$li], 'target' => isset($t[$li]) ? $t[$li] : 0),
    );
};
$c5_ytd = array('My' => $c5_sum(isset($dashboard_achievement_my) ? $dashboard_achievement_my : array()), (!empty($c5['is_all']) ? 'All' : 'Team') => $c5_sum(isset($dashboard_achievement_team) ? $dashboard_achievement_team : array()));
?>
        <div class="row">
            <div class="col-xs-12">
                <div class="crm-modern-stat-card c5-card c5-ytd">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-calendar-o" style="color:#16a34a;"></i> Last 12 Months</h4>
                        <span class="card-total-pill"><?php echo date('M-y', strtotime('first day of -11 month')) . ' to ' . date('M-y'); ?></span>
                    </div>
                    <div class="c5-ytd-grid">
                        <?php foreach ($c5_ytd as $who => $y) { $pc = $y['target'] > 0 ? (int)round($y['achieved'] / $y['target'] * 100) : null; ?>
                        <div class="c5-ytd-col">
                            <div class="c5-ytd-who"><?php echo $who; ?></div>
                            <div class="c5-ytd-figs">
                                <div><span>Target</span><b class="c2-amt"><?php echo $curr . ' ' . number_format($y['target'], 0); ?></b></div>
                                <div><span>Achieved</span><b class="c2-amt"><?php echo $curr . ' ' . number_format($y['achieved'], 0); ?></b></div>
                                <div><span>Of target</span><b class="<?php echo $pc !== null && $pc >= 100 ? 'is-met' : ''; ?>"><?php echo $pc === null ? 'No target' : $pc . '%'; ?></b></div>
                            </div>
                            <div class="c3-pct-bar c5-ytd-bar"><i style="width: <?php echo $pc === null ? 0 : min(100, $pc); ?>%;"></i></div>
                            <div class="c5-ytd-line">
                                Best: <b><?php echo $y['best'] ? htmlspecialchars($y['best']['label']) : '&ndash;'; ?></b><?php if ($y['best']) { ?> <span class="c2-amt">(<?php echo $curr . ' ' . number_format($y['best']['value'], 0); ?>)</span><?php } ?>
                                <span class="c5-dot">&middot;</span>
                                Last month<?php echo $y['last'] ? ' (' . htmlspecialchars($y['last']['label']) . ')' : ''; ?>: <b><?php echo !$y['last'] ? '&ndash;' : ($y['last']['target'] > 0 ? (int)round($y['last']['value'] / $y['last']['target'] * 100) . '% of target' : 'no target'); ?></b>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-xs-12">
                <div class="dash-chart-card">
                    <div class="dash-chart-header" style="display:flex !important; justify-content:space-between !important; align-items:center !important; width:100% !important;">
                        <h4 class="dash-chart-title" style="margin:0 !important; margin-right:20px !important;">
                            <i class="fa fa-bar-chart"></i> My Achievement
                        </h4>
                        <div class="dash-chart-legend" style="display:inline-flex; align-items:center; gap:16px; font-size:12px; font-weight:600; color:#475569; margin-left:auto !important;">
                            <span style="display:inline-flex; align-items:center; gap:6px;">
                                <span style="display:inline-block; width:10px; height:10px; background-color:#0072f0; border-radius:2px;"></span>
                                <span>Target</span>
                            </span>
                            <span style="display:inline-flex; align-items:center; gap:6px;">
                                <span style="display:inline-block; width:10px; height:10px; background-color:#16a34a; border-radius:2px;"></span>
                                <span>Achievement</span>
                            </span>
                        </div>
                    </div>
                    <div class="dash-chart-body">
                        <canvas id="barChartAchieveMy" height="230"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xs-12">
                <div class="dash-chart-card">
                    <div class="dash-chart-header" style="display:flex !important; justify-content:space-between !important; align-items:center !important; width:100% !important;">
                        <h4 class="dash-chart-title" style="margin:0 !important; margin-right:20px !important;">
                            <i class="fa fa-bar-chart"></i> Team Achievement
                        </h4>
                        <div class="dash-chart-legend" style="display:inline-flex; align-items:center; gap:16px; font-size:12px; font-weight:600; color:#475569; margin-left:auto !important;">
                            <span style="display:inline-flex; align-items:center; gap:6px;">
                                <span style="display:inline-block; width:10px; height:10px; background-color:#0072f0; border-radius:2px;"></span>
                                <span>Target</span>
                            </span>
                            <span style="display:inline-flex; align-items:center; gap:6px;">
                                <span style="display:inline-block; width:10px; height:10px; background-color:#16a34a; border-radius:2px;"></span>
                                <span>Achievement</span>
                            </span>
                        </div>
                    </div>
                    <div class="dash-chart-body">
                        <canvas id="barChartAchieveTeam" height="230"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 14px;">
            <div class="col-md-6 col-xs-12">
                <div class="dash-chart-card">
                    <div class="dash-chart-header" style="display:flex !important; justify-content:space-between !important; align-items:center !important; width:100% !important;">
                        <h4 class="dash-chart-title" style="margin:0 !important; margin-right:20px !important;">
                            <i class="fa fa-line-chart"></i> My Projection
                        </h4>
                        <div class="dash-chart-legend" style="display:inline-flex; align-items:center; gap:16px; font-size:12px; font-weight:600; color:#475569; margin-left:auto !important;">
                            <span style="display:inline-flex; align-items:center; gap:6px;">
                                <span style="display:inline-block; width:10px; height:10px; background-color:#00c6f0; border-radius:2px;"></span>
                                <span>Target</span>
                            </span>
                            <span style="display:inline-flex; align-items:center; gap:6px;">
                                <span style="display:inline-block; width:10px; height:10px; background-color:#0072f0; border-radius:2px;"></span>
                                <span>Projection</span>
                            </span>
                        </div>
                    </div>
                    <div class="dash-chart-body">
                        <canvas id="barChart2" height="230"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xs-12">
                <div class="dash-chart-card">
                    <div class="dash-chart-header" style="display:flex !important; justify-content:space-between !important; align-items:center !important; width:100% !important;">
                        <h4 class="dash-chart-title" style="margin:0 !important; margin-right:20px !important;">
                            <i class="fa fa-line-chart"></i> Team Projection
                        </h4>
                        <div class="dash-chart-legend" style="display:inline-flex; align-items:center; gap:16px; font-size:12px; font-weight:600; color:#475569; margin-left:auto !important;">
                            <span style="display:inline-flex; align-items:center; gap:6px;">
                                <span style="display:inline-block; width:10px; height:10px; background-color:#00c6f0; border-radius:2px;"></span>
                                <span>Target</span>
                            </span>
                            <span style="display:inline-flex; align-items:center; gap:6px;">
                                <span style="display:inline-block; width:10px; height:10px; background-color:#0072f0; border-radius:2px;"></span>
                                <span>Projection</span>
                            </span>
                        </div>
                    </div>
                    <div class="dash-chart-body">
                        <canvas id="barChart4" height="230"></canvas>
                    </div>
                </div>
            </div>
        </div>

<div class="dash-drilldown-panel" id="dashDrilldownPanel">
            <div class="dash-drilldown-header">
                <h4 class="dash-drilldown-title">
                    <i class="fa fa-list-alt" style="color:#0072f0;"></i>
                    <span id="drilldownTitle">Operational Metric Drill-Down</span>
                    <span class="badge bg-blue" id="drilldownCountBadge" style="margin-left:8px; font-size:12px;">0 Records</span>
                </h4>
                <div style="display:flex; align-items:center; gap:12px;">
                    <a href="#" class="btn btn-default btn-xs" id="drilldownExternalLink" target="_blank" style="border-radius:4px; font-weight:600;">
                        <i class="fa fa-external-link"></i> Open Full Module
                    </a>
                    <button type="button" class="dash-drilldown-close" id="dashDrilldownClose" title="Close Drill-Down">&times;</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="dash-drilldown-table table table-hover">
                    <thead>
                        <tr>
                            <th>Ref #</th>
                            <th>Entity / Client</th>
                            <th>POC &amp; Phone</th>
                            <th>Target Date / Deadline</th>
                            <th>Status</th>
                            <th>Assigned Agent</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="drilldownTableBody">
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>


<script>
$(function () {
    function setDashTheme(theme) {
        if (theme === 'dark') {
            $('html, body, .main-header').attr('data-theme', 'dark');
            $('#dashThemeToggle').html('<i class="fa fa-sun-o"></i> <span>Light Mode</span>');
            localStorage.setItem('crm_theme_preference', 'dark');
        } else {
            $('html, body, .main-header').removeAttr('data-theme');
            $('#dashThemeToggle').html('<i class="fa fa-moon-o"></i> <span>Dark Mode</span>');
            localStorage.setItem('crm_theme_preference', 'light');
        }
    }

    var savedTheme = localStorage.getItem('crm_theme_preference') || 'light';
    setDashTheme(savedTheme);

    $('#dashThemeToggle').on('click', function(e) {
        e.preventDefault();
        var current = $('html').attr('data-theme');
        setDashTheme(current === 'dark' ? 'light' : 'dark');
    });

    var months = <?php 
        if (!empty($dashboard_MyPro['month_labels'])) {
            echo json_encode($dashboard_MyPro['month_labels']);
        } else {
            echo json_encode(array('Oct-26', 'Nov-26', 'Dec-26', 'Jan-27', 'Feb-27', 'Mar-27'));
        }
    ?>;
    
    var myProData = [
        <?php echo isset($dashboard_MyPro['lead_number']['pt_0']) ? (int)$dashboard_MyPro['lead_number']['pt_0'] : 0; ?>,
        <?php echo isset($dashboard_MyPro['lead_number']['pt_1']) ? (int)$dashboard_MyPro['lead_number']['pt_1'] : 0; ?>,
        <?php echo isset($dashboard_MyPro['lead_number']['pt_2']) ? (int)$dashboard_MyPro['lead_number']['pt_2'] : 0; ?>,
        <?php echo isset($dashboard_MyPro['lead_number']['pt_3']) ? (int)$dashboard_MyPro['lead_number']['pt_3'] : 0; ?>,
        <?php echo isset($dashboard_MyPro['lead_number']['pt_4']) ? (int)$dashboard_MyPro['lead_number']['pt_4'] : 0; ?>,
        <?php echo isset($dashboard_MyPro['lead_number']['pt_5']) ? (int)$dashboard_MyPro['lead_number']['pt_5'] : 0; ?>,
        <?php echo isset($dashboard_MyPro['lead_number']['pt_6']) ? (int)$dashboard_MyPro['lead_number']['pt_6'] : 0; ?>,
        <?php echo isset($dashboard_MyPro['lead_number']['pt_7']) ? (int)$dashboard_MyPro['lead_number']['pt_7'] : 0; ?>,
        <?php echo isset($dashboard_MyPro['lead_number']['pt_8']) ? (int)$dashboard_MyPro['lead_number']['pt_8'] : 0; ?>,
        <?php echo isset($dashboard_MyPro['lead_number']['pt_9']) ? (int)$dashboard_MyPro['lead_number']['pt_9'] : 0; ?>,
        <?php echo isset($dashboard_MyPro['lead_number']['pt_10']) ? (int)$dashboard_MyPro['lead_number']['pt_10'] : 0; ?>,
        <?php echo isset($dashboard_MyPro['lead_number']['pt_11']) ? (int)$dashboard_MyPro['lead_number']['pt_11'] : 0; ?>
    ];

    var teamProData = [
        <?php echo isset($dashboard_TeamPro['lead_number']['pt_0']) ? (int)$dashboard_TeamPro['lead_number']['pt_0'] : 0; ?>,
        <?php echo isset($dashboard_TeamPro['lead_number']['pt_1']) ? (int)$dashboard_TeamPro['lead_number']['pt_1'] : 0; ?>,
        <?php echo isset($dashboard_TeamPro['lead_number']['pt_2']) ? (int)$dashboard_TeamPro['lead_number']['pt_2'] : 0; ?>,
        <?php echo isset($dashboard_TeamPro['lead_number']['pt_3']) ? (int)$dashboard_TeamPro['lead_number']['pt_3'] : 0; ?>,
        <?php echo isset($dashboard_TeamPro['lead_number']['pt_4']) ? (int)$dashboard_TeamPro['lead_number']['pt_4'] : 0; ?>,
        <?php echo isset($dashboard_TeamPro['lead_number']['pt_5']) ? (int)$dashboard_TeamPro['lead_number']['pt_5'] : 0; ?>,
        <?php echo isset($dashboard_TeamPro['lead_number']['pt_6']) ? (int)$dashboard_TeamPro['lead_number']['pt_6'] : 0; ?>,
        <?php echo isset($dashboard_TeamPro['lead_number']['pt_7']) ? (int)$dashboard_TeamPro['lead_number']['pt_7'] : 0; ?>,
        <?php echo isset($dashboard_TeamPro['lead_number']['pt_8']) ? (int)$dashboard_TeamPro['lead_number']['pt_8'] : 0; ?>,
        <?php echo isset($dashboard_TeamPro['lead_number']['pt_9']) ? (int)$dashboard_TeamPro['lead_number']['pt_9'] : 0; ?>,
        <?php echo isset($dashboard_TeamPro['lead_number']['pt_10']) ? (int)$dashboard_TeamPro['lead_number']['pt_10'] : 0; ?>,
        <?php echo isset($dashboard_TeamPro['lead_number']['pt_11']) ? (int)$dashboard_TeamPro['lead_number']['pt_11'] : 0; ?>
    ];

    var isMobileAndroid = (window.innerWidth <= 768);
    if (typeof Chart !== 'undefined' && Chart.Scale) {
        var origCalcXRotation = Chart.Scale.prototype.calculateXLabelRotation;
        Chart.Scale.prototype.calculateXLabelRotation = function() {
            origCalcXRotation.apply(this, arguments);
            this.xLabelRotation = 0;
            if (this.xLabels && this.xLabels.length) {
                this.ctx.font = this.font;
                this.xScalePaddingRight = this.ctx.measureText(this.xLabels[this.xLabels.length - 1]).width / 2 + 4;
                this.xScalePaddingLeft = Math.max(this.xScalePaddingLeft, this.ctx.measureText(this.xLabels[0]).width / 2 + 4);
            }
        };
    }

    function formatChartLabels(list, canvasEl) {
        list = list || [];
        var plotWidth = canvasEl && canvasEl.parentNode ? canvasEl.parentNode.clientWidth - 56 : 0;
        var perLabel = (plotWidth > 0 && list.length) ? plotWidth / list.length : (isMobileAndroid ? 28 : 60);
        var mode = perLabel >= 54 ? 'full' : (perLabel >= 30 ? 'month' : 'alternate');
        return list.map(function(lbl, idx) {
            var parts = String(lbl).split('-');
            if (parts.length !== 2) { return lbl; }
            if (mode === 'full') { return parts[0] + " '" + parts[1]; }
            if (mode === 'month') { return parts[0]; }
            return (idx % 2 === 0) ? parts[0] : '';
        });
    }

    // Phones only: short month labels so they do not run together (desktop keeps the full labels).
    function barLabels(list, canvasEl) { return isMobileAndroid ? formatChartLabels(list, canvasEl) : list; }
    // Bar index from the clicked bar (labels may be shortened on phones).
    function barIndex(chart, bars) { return (chart && chart.datasets && chart.datasets[0] && bars && bars.length) ? chart.datasets[0].bars.indexOf(bars[0]) : -1; }
    var shortScale = "<%= Number(value) >= 1000000 ? (Number(value) / 1000000).toLocaleString() + 'M' : (Number(value) >= 1000 ? (Number(value) / 1000).toLocaleString() + 'K' : Number(value).toLocaleString()) %>";

    // Classic 5: chart text and grid colours follow light / dark mode.
    function c5Dark() { return document.documentElement.getAttribute('data-theme') === 'dark'; }
    function c5Ink() { return c5Dark() ? '#cbd5e1' : '#1e293b'; }
    function c5Grid() { return c5Dark() ? 'rgba(255,255,255,.07)' : 'rgba(0,0,0,.04)'; }

    var chartOptions = {
        scaleBeginAtZero: true,
        scaleShowGridLines: true,
        scaleGridLineColor: c5Grid(),
        scaleGridLineWidth: 1,
        scaleShowHorizontalLines: true,
        scaleShowVerticalLines: false,
        barShowStroke: true,
        barStrokeWidth: 1,
        barValueSpacing: isMobileAndroid ? 1 : 4,
        barDatasetSpacing: isMobileAndroid ? 0 : 1,
        responsive: true,
        maintainAspectRatio: false,
        scaleFontSize: isMobileAndroid ? 10 : 12,
        scaleFontStyle: '600',
        scaleFontColor: c5Ink(),
        scaleFontFamily: "'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif"
    };

    var projTargetMy = <?php echo json_encode(!empty($projection_target_my) ? array_values($projection_target_my) : array()); ?>;
    var projTargetTeam = <?php echo json_encode(!empty($projection_target_team) ? array_values($projection_target_team) : array()); ?>;
    var monthDates = <?php echo json_encode(!empty($dashboard_MyPro['month_dates']) ? $dashboard_MyPro['month_dates'] : array()); ?>;
    var teamMonthDates = <?php echo json_encode(!empty($dashboard_TeamPro['month_dates']) ? $dashboard_TeamPro['month_dates'] : array()); ?>;

    var achieveMonthsMy = <?php echo json_encode(!empty($dashboard_achievement_my['labels']) ? $dashboard_achievement_my['labels'] : array()); ?>;
    var achieveTargetMy = <?php echo json_encode(!empty($dashboard_achievement_my['target']) ? $dashboard_achievement_my['target'] : array()); ?>;
    var achieveDataMy = <?php echo json_encode(!empty($dashboard_achievement_my['achievement']) ? $dashboard_achievement_my['achievement'] : array()); ?>;
    var achieveDatesMy = <?php echo json_encode(!empty($dashboard_achievement_my['month_dates']) ? $dashboard_achievement_my['month_dates'] : array()); ?>;

    var achieveMonthsTeam = <?php echo json_encode(!empty($dashboard_achievement_team['labels']) ? $dashboard_achievement_team['labels'] : array()); ?>;
    var achieveTargetTeam = <?php echo json_encode(!empty($dashboard_achievement_team['target']) ? $dashboard_achievement_team['target'] : array()); ?>;
    var achieveDataTeam = <?php echo json_encode(!empty($dashboard_achievement_team['achievement']) ? $dashboard_achievement_team['achievement'] : array()); ?>;
    var achieveDatesTeam = <?php echo json_encode(!empty($dashboard_achievement_team['month_dates']) ? $dashboard_achievement_team['month_dates'] : array()); ?>;

    // My / Team Projection: target and projection side by side, amounts in AED.
    var projOptions = $.extend({}, chartOptions, {
        scaleLabel: isMobileAndroid ? shortScale : "<%= 'AED ' + Number(value).toLocaleString() %>",
        tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= 'AED ' + Number(value).toLocaleString() %>",
        multiTooltipTemplate: "<%= datasetLabel %>: <%= 'AED ' + Number(value).toLocaleString() %>"
    });
    var canvas2 = document.getElementById('barChart2');
    var chartInstance2 = null;
    if (canvas2) {
        canvas2.style.cursor = 'pointer';
        var ctx2 = canvas2.getContext('2d');
        chartInstance2 = new Chart(ctx2).Bar({
            labels: barLabels(months, canvas2),
            datasets: [{
                label: 'Target',
                fillColor: 'rgba(0, 198, 240, 0.85)',
                strokeColor: '#00c6f0',
                highlightFill: 'rgba(0, 198, 240, 1)',
                highlightStroke: '#0096cc',
                data: projTargetMy.length ? projTargetMy : months.map(function(){ return 0; })
            }, {
                label: 'Projection',
                fillColor: 'rgba(0, 114, 240, 0.85)',
                strokeColor: '#0072f0',
                highlightFill: 'rgba(0, 114, 240, 1)',
                highlightStroke: '#0059c0',
                data: myProData
            }]
        }, projOptions);

        canvas2.onclick = function(evt) {
            if (chartInstance2 && typeof chartInstance2.getBarsAtEvent === 'function') {
                var activeBars = chartInstance2.getBarsAtEvent(evt);
                if (activeBars && activeBars.length > 0) {
                    var idx = barIndex(chartInstance2, activeBars);
                    if (idx !== -1 && monthDates[idx]) {
                        window.location.href = '<?php echo base_url(); ?>manager/leads?exp_d1=' + encodeURIComponent(monthDates[idx].d1) + '&exp_d2=' + encodeURIComponent(monthDates[idx].d2);
                    }
                }
            }
        };
    }

    var canvas4 = document.getElementById('barChart4');
    var chartInstance4 = null;
    if (canvas4) {
        canvas4.style.cursor = 'pointer';
        var ctx4 = canvas4.getContext('2d');
        chartInstance4 = new Chart(ctx4).Bar({
            labels: barLabels(months, canvas4),
            datasets: [{
                label: 'Target',
                fillColor: 'rgba(0, 198, 240, 0.85)',
                strokeColor: '#00c6f0',
                highlightFill: 'rgba(0, 198, 240, 1)',
                highlightStroke: '#0096cc',
                data: projTargetTeam.length ? projTargetTeam : months.map(function(){ return 0; })
            }, {
                label: 'Projection',
                fillColor: 'rgba(0, 114, 240, 0.85)',
                strokeColor: '#0072f0',
                highlightFill: 'rgba(0, 114, 240, 1)',
                highlightStroke: '#0059c0',
                data: teamProData
            }]
        }, projOptions);

        canvas4.onclick = function(evt) {
            if (chartInstance4 && typeof chartInstance4.getBarsAtEvent === 'function') {
                var activeBars = chartInstance4.getBarsAtEvent(evt);
                if (activeBars && activeBars.length > 0) {
                    var idx = barIndex(chartInstance4, activeBars);
                    if (idx !== -1 && teamMonthDates[idx]) {
                        window.location.href = '<?php echo base_url(); ?>manager/team/leads?exp_d1=' + encodeURIComponent(teamMonthDates[idx].d1) + '&exp_d2=' + encodeURIComponent(teamMonthDates[idx].d2);
                    }
                }
            }
        };
    }

    function renderAchieveChart(canvasId, labels, target, achieve, dates, baseUrl) {
        var canvas = document.getElementById(canvasId);
        if (!canvas) return null;
        canvas.style.cursor = 'pointer';
        var ctx = canvas.getContext('2d');
        var achieveOptions = $.extend({}, chartOptions, {
            scaleLabel: isMobileAndroid ? shortScale : "<%= 'AED ' + Number(value).toLocaleString() %>",
            tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= 'AED ' + Number(value).toLocaleString() %>",
            multiTooltipTemplate: "<%= datasetLabel %>: <%= 'AED ' + Number(value).toLocaleString() %>"
        });
        var inst = new Chart(ctx).Bar({
            labels: barLabels(labels, canvas),
            datasets: [
                {
                    label: 'Target',
                    fillColor: 'rgba(0, 198, 240, 0.85)',
                    strokeColor: '#00c6f0',
                    highlightFill: 'rgba(0, 198, 240, 1)',
                    highlightStroke: '#0096cc',
                    data: target
                },
                {
                    label: 'Achievement',
                    fillColor: 'rgba(22, 163, 74, 0.85)',
                    strokeColor: '#16a34a',
                    highlightFill: 'rgba(22, 163, 74, 1)',
                    highlightStroke: '#15803d',
                    data: achieve
                }
            ]
        }, achieveOptions);

        canvas.onclick = function(evt) {
            if (inst && typeof inst.getBarsAtEvent === 'function') {
                var activeBars = inst.getBarsAtEvent(evt);
                if (activeBars && activeBars.length > 0) {
                    var idx = barIndex(inst, activeBars);
                    if (idx !== -1 && dates && dates[idx]) {
                        window.location.href = baseUrl + '?exp_d1=' + encodeURIComponent(dates[idx].d1) + '&exp_d2=' + encodeURIComponent(dates[idx].d2);
                    }
                }
            }
        };
        return inst;
    }

    renderAchieveChart('barChartAchieveMy', achieveMonthsMy, achieveTargetMy, achieveDataMy, achieveDatesMy, '<?php echo base_url(); ?>manager/leads');
    renderAchieveChart('barChartAchieveTeam', achieveMonthsTeam, achieveTargetTeam, achieveDataTeam, achieveDatesTeam, '<?php echo base_url(); ?>manager/team/leads');

    var targetLineCharts = [];
    function drawTargetCharts() {
    targetLineCharts.forEach(function(ch) { if (ch && typeof ch.destroy === 'function') { ch.destroy(); } });
    targetLineCharts = [];
    var canvasAchieveLine = document.getElementById('lineChartTargetAchieve');
    if (canvasAchieveLine) {
        var ctxAch = canvasAchieveLine.getContext('2d');
        targetLineCharts.push(new Chart(ctxAch).Line({
            labels: formatChartLabels((achieveMonthsMy && achieveMonthsMy.length > 0) ? achieveMonthsMy : months, canvasAchieveLine),
            datasets: [
                {
                    label: 'Target',
                    fillColor: 'rgba(0, 198, 240, 0.08)',
                    strokeColor: '#00c6f0',
                    pointColor: '#00c6f0',
                    pointStrokeColor: '#ffffff',
                    pointHighlightFill: '#ffffff',
                    pointHighlightStroke: '#00c6f0',
                    data: (achieveTargetMy && achieveTargetMy.length > 0) ? achieveTargetMy : months.map(function(){ return 0; })
                },
                {
                    label: 'Achievement',
                    fillColor: 'rgba(22, 163, 74, 0.08)',
                    strokeColor: '#16a34a',
                    pointColor: '#16a34a',
                    pointStrokeColor: '#ffffff',
                    pointHighlightFill: '#ffffff',
                    pointHighlightStroke: '#16a34a',
                    data: (achieveDataMy && achieveDataMy.length > 0) ? achieveDataMy : months.map(function(){ return 0; })
                }
            ]
        }, {
            responsive: true,
            maintainAspectRatio: false,
            bezierCurve: true,
            bezierCurveTension: 0.3,
            pointDot: true,
            pointDotRadius: 3,
            datasetStrokeWidth: 2,
            scaleGridLineColor: c5Grid(),
            scaleFontSize: isMobileAndroid ? 10 : 12,
            scaleFontStyle: '600',
            scaleFontColor: c5Ink(),
            scaleLabel: "<%= Number(value).toLocaleString() %>"
        }));
    }

    var canvasProjectLine = document.getElementById('lineChartTargetProject');
    if (canvasProjectLine) {
        var ctxProj = canvasProjectLine.getContext('2d');
        var projTargetData = (achieveTargetMy && achieveTargetMy.length > 0) ? achieveTargetMy.slice(0, months.length) : months.map(function(){ return 0; });
        targetLineCharts.push(new Chart(ctxProj).Line({
            labels: formatChartLabels(months, canvasProjectLine),
            datasets: [
                {
                    label: 'Target',
                    fillColor: 'rgba(0, 198, 240, 0.08)',
                    strokeColor: '#00c6f0',
                    pointColor: '#00c6f0',
                    pointStrokeColor: '#ffffff',
                    pointHighlightFill: '#ffffff',
                    pointHighlightStroke: '#00c6f0',
                    data: projTargetData
                },
                {
                    label: 'Projection',
                    fillColor: 'rgba(0, 114, 240, 0.08)',
                    strokeColor: '#0072f0',
                    pointColor: '#0072f0',
                    pointStrokeColor: '#ffffff',
                    pointHighlightFill: '#ffffff',
                    pointHighlightStroke: '#0072f0',
                    data: myProData
                }
            ]
        }, {
            responsive: true,
            maintainAspectRatio: false,
            bezierCurve: true,
            bezierCurveTension: 0.3,
            pointDot: true,
            pointDotRadius: 3,
            datasetStrokeWidth: 2,
            scaleGridLineColor: c5Grid(),
            scaleFontSize: isMobileAndroid ? 10 : 12,
            scaleFontStyle: '600',
            scaleFontColor: c5Ink(),
            scaleLabel: "<%= Number(value).toLocaleString() %>"
        }));
    }
    }
    drawTargetCharts();
    var targetChartsWidth = window.innerWidth;
    var targetChartsTimer = null;
    $(window).on('resize orientationchange', function() {
        clearTimeout(targetChartsTimer);
        targetChartsTimer = setTimeout(function() {
            if (Math.abs(window.innerWidth - targetChartsWidth) > 40) {
                targetChartsWidth = window.innerWidth;
                isMobileAndroid = (window.innerWidth <= 768);
                drawTargetCharts();
            }
        }, 300);
    });


    $('.crm-modern-stat-chip').on('click', function(e) {
        var targetUrl = $(this).attr('href');
        if (targetUrl && targetUrl !== '#' && targetUrl.indexOf('javascript') === -1) {
            window.location.href = targetUrl;
        }
    });

    var customOpTooltip = function(tooltip) {
        var el = $('#chartjs-op-tooltip');
        if (!el.length) {
            $('body').append('<div id="chartjs-op-tooltip" style="position: fixed; z-index: 99999999; pointer-events: none; opacity: 0; background: rgba(15, 23, 42, 0.96); color: #ffffff; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, sans-serif; white-space: nowrap; box-shadow: 0 4px 14px rgba(0,0,0,0.35); transition: opacity 0.08s ease;"></div>');
            el = $('#chartjs-op-tooltip');
        }
        if (!tooltip || !tooltip.text) {
            el.css({ opacity: 0 });
            return;
        }
        el.html(tooltip.text);
        var tipW = el.outerWidth() || 80;
        var tipH = el.outerHeight() || 28;
        var top = (tooltip.clientY || tooltip.y) - tipH - 12;
        var left = (tooltip.clientX || tooltip.x) - (tipW / 2);
        if (top < 10) top = (tooltip.clientY || tooltip.y) + 16;
        if (left < 10) left = 10;
        if (left + tipW > $(window).width() - 10) left = $(window).width() - tipW - 10;
        el.css({
            opacity: 1,
            top: top + 'px',
            left: left + 'px'
        });
    };

    function renderOpPie(canvasId, missed, last7, today, next7, future) {
        var cv = document.getElementById(canvasId);
        if (!cv) return;
        var ctx = cv.getContext('2d');
        var m = parseInt(missed) || 0;
        var l = parseInt(last7) || 0;
        var t = parseInt(today) || 0;
        var n = parseInt(next7) || 0;
        var f = parseInt(future) || 0;
        var data = [];
        if (m === 0 && l === 0 && t === 0 && n === 0 && f === 0) {
            data = [{ value: 1, color: '#e2e8f0', label: 'Empty' }];
        } else {
            if (m > 0) data.push({ value: m, color: '#dc2626', label: 'All Missed' });
            if (l > 0) data.push({ value: l, color: '#ea580c', label: 'Last 7 Days' });
            if (t > 0) data.push({ value: t, color: '#0284c7', label: 'Today' });
            if (n > 0) data.push({ value: n, color: '#2563eb', label: 'Next 7 Days' });
            if (f > 0) data.push({ value: f, color: '#15803d', label: 'All Future' });
        }
        var chartInst = new Chart(ctx).Doughnut(data, {
            percentageInnerCutout: 52,
            segmentShowStroke: true,
            segmentStrokeColor: '#ffffff',
            segmentStrokeWidth: 1,
            animation: false,
            responsive: false,
            maintainAspectRatio: true,
            showTooltips: false
        });

        $(cv).on('mousemove', function(e) {
            var pts = chartInst.getSegmentsAtEvent(e);
            if (pts && pts.length > 0) {
                var seg = pts[0];
                if (seg.label && seg.label !== 'Empty') {
                    customOpTooltip({
                        text: seg.label + ': ' + seg.value,
                        clientX: e.clientX,
                        clientY: e.clientY
                    });
                } else {
                    customOpTooltip(null);
                }
            } else {
                customOpTooltip(null);
            }
        }).on('mouseleave', function() {
            customOpTooltip(null);
        });
    }

    renderOpPie('pieCard_my_tickets', '<?php echo isset($dashboard_tickets["total_missed"]) ? (int)$dashboard_tickets["total_missed"] : 0; ?>', '<?php echo isset($dashboard_tickets["total_lastweek"]) ? (int)$dashboard_tickets["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_tickets["total_today"]) ? (int)$dashboard_tickets["total_today"] : 0; ?>', '<?php echo isset($dashboard_tickets["total_nextweek"]) ? (int)$dashboard_tickets["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_tickets["total_future"]) ? (int)$dashboard_tickets["total_future"] : 0; ?>');
    renderOpPie('pieCard_my_tasks', '<?php echo isset($dashboard_tasks["total_missed"]) ? (int)$dashboard_tasks["total_missed"] : 0; ?>', '<?php echo isset($dashboard_tasks["total_lastweek"]) ? (int)$dashboard_tasks["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_tasks["total_today"]) ? (int)$dashboard_tasks["total_today"] : 0; ?>', '<?php echo isset($dashboard_tasks["total_nextweek"]) ? (int)$dashboard_tasks["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_tasks["total_future"]) ? (int)$dashboard_tasks["total_future"] : 0; ?>');
    renderOpPie('pieCard_my_f', '<?php echo isset($dashboard_f["total_missed"]) ? (int)$dashboard_f["total_missed"] : 0; ?>', '<?php echo isset($dashboard_f["total_lastweek"]) ? (int)$dashboard_f["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_f["total_today"]) ? (int)$dashboard_f["total_today"] : 0; ?>', '<?php echo isset($dashboard_f["total_nextweek"]) ? (int)$dashboard_f["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_f["total_future"]) ? (int)$dashboard_f["total_future"] : 0; ?>');
    renderOpPie('pieCard_my_m', '<?php echo isset($dashboard_m["total_missed"]) ? (int)$dashboard_m["total_missed"] : 0; ?>', '<?php echo isset($dashboard_m["total_lastweek"]) ? (int)$dashboard_m["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_m["total_today"]) ? (int)$dashboard_m["total_today"] : 0; ?>', '<?php echo isset($dashboard_m["total_nextweek"]) ? (int)$dashboard_m["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_m["total_future"]) ? (int)$dashboard_m["total_future"] : 0; ?>');
    renderOpPie('pieCard_my_vfu', '<?php echo isset($dashboard_vfu["total_missed"]) ? (int)$dashboard_vfu["total_missed"] : 0; ?>', '<?php echo isset($dashboard_vfu["total_lastweek"]) ? (int)$dashboard_vfu["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_vfu["total_today"]) ? (int)$dashboard_vfu["total_today"] : 0; ?>', '<?php echo isset($dashboard_vfu["total_nextweek"]) ? (int)$dashboard_vfu["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_vfu["total_future"]) ? (int)$dashboard_vfu["total_future"] : 0; ?>');
    renderOpPie('pieCard_my_db', '<?php echo isset($dashboard_db_ops_my["total_missed"]) ? (int)$dashboard_db_ops_my["total_missed"] : 0; ?>', '<?php echo isset($dashboard_db_ops_my["total_lastweek"]) ? (int)$dashboard_db_ops_my["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_db_ops_my["total_today"]) ? (int)$dashboard_db_ops_my["total_today"] : 0; ?>', '<?php echo isset($dashboard_db_ops_my["total_nextweek"]) ? (int)$dashboard_db_ops_my["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_db_ops_my["total_future"]) ? (int)$dashboard_db_ops_my["total_future"] : 0; ?>');
    renderOpPie('pieCard_my_leads', '<?php echo isset($dashboard_lead_ops_my["total_missed"]) ? (int)$dashboard_lead_ops_my["total_missed"] : 0; ?>', '<?php echo isset($dashboard_lead_ops_my["total_lastweek"]) ? (int)$dashboard_lead_ops_my["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_lead_ops_my["total_today"]) ? (int)$dashboard_lead_ops_my["total_today"] : 0; ?>', '<?php echo isset($dashboard_lead_ops_my["total_nextweek"]) ? (int)$dashboard_lead_ops_my["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_lead_ops_my["total_future"]) ? (int)$dashboard_lead_ops_my["total_future"] : 0; ?>');
    renderOpPie('pieCard_team_tickets', '<?php echo isset($dashboard_Ttickets["total_missed"]) ? (int)$dashboard_Ttickets["total_missed"] : 0; ?>', '<?php echo isset($dashboard_Ttickets["total_lastweek"]) ? (int)$dashboard_Ttickets["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_Ttickets["total_today"]) ? (int)$dashboard_Ttickets["total_today"] : 0; ?>', '<?php echo isset($dashboard_Ttickets["total_nextweek"]) ? (int)$dashboard_Ttickets["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_Ttickets["total_future"]) ? (int)$dashboard_Ttickets["total_future"] : 0; ?>');
    renderOpPie('pieCard_team_tasks', '<?php echo isset($dashboard_Ttasks["total_missed"]) ? (int)$dashboard_Ttasks["total_missed"] : 0; ?>', '<?php echo isset($dashboard_Ttasks["total_lastweek"]) ? (int)$dashboard_Ttasks["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_Ttasks["total_today"]) ? (int)$dashboard_Ttasks["total_today"] : 0; ?>', '<?php echo isset($dashboard_Ttasks["total_nextweek"]) ? (int)$dashboard_Ttasks["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_Ttasks["total_future"]) ? (int)$dashboard_Ttasks["total_future"] : 0; ?>');
    renderOpPie('pieCard_team_f', '<?php echo isset($dashboard_Tf["total_missed"]) ? (int)$dashboard_Tf["total_missed"] : 0; ?>', '<?php echo isset($dashboard_Tf["total_lastweek"]) ? (int)$dashboard_Tf["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_Tf["total_today"]) ? (int)$dashboard_Tf["total_today"] : 0; ?>', '<?php echo isset($dashboard_Tf["total_nextweek"]) ? (int)$dashboard_Tf["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_Tf["total_future"]) ? (int)$dashboard_Tf["total_future"] : 0; ?>');
    renderOpPie('pieCard_team_m', '<?php echo isset($dashboard_Tm["total_missed"]) ? (int)$dashboard_Tm["total_missed"] : 0; ?>', '<?php echo isset($dashboard_Tm["total_lastweek"]) ? (int)$dashboard_Tm["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_Tm["total_today"]) ? (int)$dashboard_Tm["total_today"] : 0; ?>', '<?php echo isset($dashboard_Tm["total_nextweek"]) ? (int)$dashboard_Tm["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_Tm["total_future"]) ? (int)$dashboard_Tm["total_future"] : 0; ?>');
    renderOpPie('pieCard_team_vfu', '<?php echo isset($dashboard_Tvfu["total_missed"]) ? (int)$dashboard_Tvfu["total_missed"] : 0; ?>', '<?php echo isset($dashboard_Tvfu["total_lastweek"]) ? (int)$dashboard_Tvfu["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_Tvfu["total_today"]) ? (int)$dashboard_Tvfu["total_today"] : 0; ?>', '<?php echo isset($dashboard_Tvfu["total_nextweek"]) ? (int)$dashboard_Tvfu["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_Tvfu["total_future"]) ? (int)$dashboard_Tvfu["total_future"] : 0; ?>');
    renderOpPie('pieCard_team_db', '<?php echo isset($dashboard_db_ops_team["total_missed"]) ? (int)$dashboard_db_ops_team["total_missed"] : 0; ?>', '<?php echo isset($dashboard_db_ops_team["total_lastweek"]) ? (int)$dashboard_db_ops_team["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_db_ops_team["total_today"]) ? (int)$dashboard_db_ops_team["total_today"] : 0; ?>', '<?php echo isset($dashboard_db_ops_team["total_nextweek"]) ? (int)$dashboard_db_ops_team["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_db_ops_team["total_future"]) ? (int)$dashboard_db_ops_team["total_future"] : 0; ?>');
    renderOpPie('pieCard_team_leads', '<?php echo (int)$dashboard_lead_ops_team["total_missed"]; ?>', '<?php echo (int)$dashboard_lead_ops_team["total_lastweek"]; ?>', '<?php echo (int)$dashboard_lead_ops_team["total_today"]; ?>', '<?php echo (int)$dashboard_lead_ops_team["total_nextweek"]; ?>', '<?php echo (int)$dashboard_lead_ops_team["total_future"]; ?>');
});

    $(document).on('mouseenter', '.donut-slice', function(e) {
        var label = $(this).attr('data-label');
        var count = $(this).attr('data-count');
        var pct = $(this).attr('data-pct');
        var type = $(this).attr('data-type') || 'Record';
        $('#donutTipTitle').text(type + ': Status');
        $('#donutTipStage').text(label);
        $('#donutTipType').text(type + 's');
        $('#donutTipVal').text(count + ' (' + pct + ')');
        $('#donutHoverTooltip').show();
        $(this).attr('stroke', '#ffffff').attr('stroke-width', '3').css('opacity', '0.85');
    }).on('mousemove', '.donut-slice', function(e) {
        $('#donutHoverTooltip').css({
            top: (e.clientY - 55) + 'px',
            left: (e.clientX + 15) + 'px'
        });
    }).on('mouseleave', '.donut-slice', function() {
        $('#donutHoverTooltip').hide();
        $(this).attr('stroke-width', '2').css('opacity', '1');
    });
</script>

<?php
/* ---------------------------------------------------------------------------
   Classic 2 only: look and convenience. Charts, figures and links are unchanged.
   --------------------------------------------------------------------------- */
$c2_zero = function () {
    foreach (func_get_args() as $arr) {
        foreach ((array)$arr as $v) { if ((float)$v != 0) return false; }
    }
    return true;
};
$c2_ach_my = isset($dashboard_achievement_my) ? (array)$dashboard_achievement_my : array();
$c2_ach_team = isset($dashboard_achievement_team) ? (array)$dashboard_achievement_team : array();
$c2_pro_my = !empty($dashboard_MyPro['lead_number']) ? $dashboard_MyPro['lead_number'] : array();
$c2_pro_team = !empty($dashboard_TeamPro['lead_number']) ? $dashboard_TeamPro['lead_number'] : array();
// Which charts have nothing to show (every value is 0): they get a short message on top.
$c2_empty = array(
    'lineChartTargetAchieve' => $c2_zero(isset($c2_ach_my['target']) ? $c2_ach_my['target'] : array(), isset($c2_ach_my['achievement']) ? $c2_ach_my['achievement'] : array()) ? 'No target or achievement in these months' : '',
    'lineChartTargetProject' => $c2_zero(isset($c2_ach_my['target']) ? $c2_ach_my['target'] : array(), $c2_pro_my) ? 'No target or projection in these months' : '',
    'barChartAchieveMy' => $c2_zero(isset($c2_ach_my['target']) ? $c2_ach_my['target'] : array(), isset($c2_ach_my['achievement']) ? $c2_ach_my['achievement'] : array()) ? 'No target or achievement in these months' : '',
    'barChartAchieveTeam' => $c2_zero(isset($c2_ach_team['target']) ? $c2_ach_team['target'] : array(), isset($c2_ach_team['achievement']) ? $c2_ach_team['achievement'] : array()) ? 'No target or achievement in these months' : '',
    'barChart2' => $c2_zero(isset($projection_target_my) ? $projection_target_my : array(), $c2_pro_my) ? 'No target or projection in these months' : '',
    'barChart4' => $c2_zero(isset($projection_target_team) ? $projection_target_team : array(), $c2_pro_team) ? 'No target or projection in these months' : '',
);
?>
<style>
/* 1. Tiles: "My" in navy, "Team" in teal. */
.dash-c2 .dash-kpi-col1, .dash-c2 .dash-kpi-col2 { background: linear-gradient(135deg, rgba(30, 64, 175, .62) 0%, rgba(30, 58, 138, .86) 100%), #1e3a8a url("<?php echo base_url(); ?>assets/background-auth.jpg") 30% 40% / 460px auto no-repeat !important; box-shadow: 0 4px 12px rgba(30, 58, 138, .22) !important; }
.dash-c2 .dash-kpi-col3, .dash-c2 .dash-kpi-col4 { background: linear-gradient(135deg, rgba(15, 138, 128, .66) 0%, rgba(15, 118, 110, .88) 100%), #0f766e url("<?php echo base_url(); ?>assets/background-auth.jpg") 30% 40% / 460px auto no-repeat !important; box-shadow: 0 4px 12px rgba(15, 118, 110, .22) !important; }
.dash-c2 .dash-kpi-col1:hover, .dash-c2 .dash-kpi-col2:hover { box-shadow: 0 8px 18px rgba(30, 58, 138, .35) !important; }
.dash-c2 .dash-kpi-col3:hover, .dash-c2 .dash-kpi-col4:hover { box-shadow: 0 8px 18px rgba(15, 118, 110, .35) !important; }
/* Same wave picture as the sign-in page shows through the tiles. Page behind the cards is plain white. */
.dash-c2 .dash-kpi-col2, .dash-c2 .dash-kpi-col4 { background-position: 0 0, 70% 60% !important; }
html:not([data-theme="dark"]) .content-wrapper.dash-c2 { background: #ffffff !important; }
/* White cards: the same picture under a strong white wash, like the right side of the sign-in page. */
.dash-c2 .dash-top-chart-card, .dash-c2 .dash-round-card, .dash-c2 .dash-chart-card, .dash-c2 .crm-modern-stat-card { background: linear-gradient(rgba(255, 255, 255, .91), rgba(255, 255, 255, .91)), #ffffff url("<?php echo base_url(); ?>assets/background-auth.jpg") center / cover no-repeat !important; }
[data-theme="dark"] .dash-c2 .dash-top-chart-card, [data-theme="dark"] .dash-c2 .dash-round-card, [data-theme="dark"] .dash-c2 .dash-chart-card, [data-theme="dark"] .dash-c2 .crm-modern-stat-card { background: linear-gradient(rgba(17, 24, 39, .90), rgba(17, 24, 39, .90)), #111827 url("<?php echo base_url(); ?>assets/background-auth.jpg") center / cover no-repeat !important; }
/* 2. Bigger labels and amounts. */
.dash-c2 .dash-kpi-card { height: 60px; min-height: 60px; padding: 4px 12px !important; }
.dash-c2 .dash-kpi-value { font-size: 19px; margin-bottom: 1px; }
.dash-c2 .dash-kpi-label { font-size: 13px; }
.dash-c2 .dash-kpi-sales { font-size: 11.5px; font-weight: 600; }
/* Top area: one 8px gap everywhere, last cards flush with the column end. */
.dash-c2 .c2-toprow .dash-kpi-card { margin-bottom: 8px; }
.dash-c2 .c2-toprow .dash-top-chart-card:last-child { margin-bottom: 0 !important; }
@media (max-width: 991px) {
    .dash-c2 .c2-toprow > [class*="col-"] { padding-left: 14px !important; padding-right: 14px !important; }
    .dash-c2 .c2-toprow > [class*="col-"] + [class*="col-"] { margin-top: 8px; }
}

/* 3. Message over charts with nothing to show (the chart stays underneath). */
.dash-c2 .dash-top-chart-body, .dash-c2 .dash-chart-body { position: relative !important; }
.dash-c2 .c2-nodata { position: absolute; left: 50%; top: 45%; transform: translate(-50%, -50%); z-index: 2; padding: 6px 12px; border-radius: 6px; background: rgba(255, 255, 255, .92); border: 1px solid #e2e8f0; color: #64748b; font-size: 12.5px; font-weight: 600; white-space: nowrap; pointer-events: none; }
/* 4. Empty ring is smaller (drawn in PHP) and its text follows the theme. */
[data-theme="dark"] .dash-c2 .c2-empty-text { fill: #94a3b8; }
[data-theme="dark"] .dash-c2 .c2-empty-ring { stroke: #1f2937; }
.dash-c2 .c2-empty-ring { stroke: #cfd8e3; }

/* 5. Activity cards: titles shown in full. */
.dash-c2 .crm-op-7col-row .crm-modern-stat-card .card-top { flex-wrap: wrap !important; align-items: flex-start !important; }
.dash-c2 .crm-op-7col-row .crm-modern-stat-card .card-title { white-space: normal !important; overflow: visible !important; text-overflow: clip !important; font-size: 11.5px !important; flex: 1 1 auto; min-width: 0; }
.dash-c2 .crm-op-7col-row .crm-modern-stat-card .card-title a, .dash-c2 .crm-op-7col-row .crm-modern-stat-card .card-title span { white-space: normal; }
@media (max-width: 1279px) and (min-width: 768px) {
    .dash-c2 .crm-op-7col-row { flex-wrap: wrap !important; }
    .dash-c2 .crm-op-7col-row > .crm-col-7 { flex: 0 0 25% !important; width: 25% !important; max-width: 25% !important; min-width: 25% !important; margin-bottom: 6px; }
}

/* 6. State lists (Database, Leads, Orders): plain rows with a thin line. */
.dash-c2 .col-md-6 > .crm-modern-stat-card .crm-modern-stat-grid { column-gap: 18px !important; row-gap: 0 !important; }
.dash-c2 .col-md-6 > .crm-modern-stat-card .crm-modern-stat-chip { background: transparent !important; border: 0 !important; border-bottom: 1px solid #eef2f7 !important; border-radius: 0 !important; padding: 7px 2px !important; }
.dash-c2 .col-md-6 > .crm-modern-stat-card .crm-modern-stat-chip:hover { background: #f8fafc !important; }
/* 7. Zero in grey, other counts in bold. */
.dash-c2 .col-md-6 > .crm-modern-stat-card .crm-modern-stat-chip .badge { border: 0 !important; background: transparent !important; font-size: 13px !important; font-weight: 800 !important; color: #1e3a8a !important; padding: 2px 4px !important; }
.dash-c2 .col-md-6 > .crm-modern-stat-card .crm-modern-stat-chip.c2-zero span:first-child { color: #94a3b8 !important; font-weight: 600 !important; }
.dash-c2 .col-md-6 > .crm-modern-stat-card .crm-modern-stat-chip.c2-zero .badge { color: #cbd5e1 !important; font-weight: 600 !important; }

/* 8. Section bar (stays at the top while scrolling). */
.dash-c2 .c2-bar { position: sticky; top: var(--c2-top, 0px); z-index: 20; display: flex; align-items: center; gap: 8px; margin: 0 0 3px; padding: 2px 4px; background: rgba(255, 255, 255, .96); border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 1px 3px rgba(0, 0, 0, .04); }
.dash-c2 .c2-bar-links { display: flex; gap: 2px; flex: 1; min-width: 0; overflow-x: auto; scrollbar-width: none; }
.dash-c2 .c2-bar-links::-webkit-scrollbar { display: none; }
.dash-c2 .c2-bar-links a { flex: none; padding: 2px 10px; line-height: 18px; border-radius: 5px; color: #334155; font-size: 12.5px; font-weight: 600; text-decoration: none; white-space: nowrap; }
.dash-c2 .c2-bar-links a:hover { background: #f1f5f9; color: #0f172a; }
.dash-c2 .c2-bar-links a.is-on { background: #1e3a8a; color: #fff; }
.dash-c2 .c2-anchor { height: 0; }
.dash-c2 .c2-anchor, .dash-c2 .dash-section-divider { scroll-margin-top: calc(var(--c2-top, 0px) + 54px); }
/* 9. Hide amounts. */
.dash-c2 .c2-hide-btn { flex: none; display: inline-flex; align-items: center; gap: 5px; height: 22px; padding: 0 9px; border: 1px solid #cbd5e1; border-radius: 5px; background: #fff; color: #334155; font-size: 12px; font-weight: 600; cursor: pointer; }
.dash-c2 .c2-hide-btn:hover { border-color: #1e3a8a; color: #1e3a8a; }
.dash-c2 .c2-hide-btn[aria-pressed="true"] { background: #eef2ff; border-color: #c7d2fe; color: #1e3a8a; }
/* Tile amounts read "AED •••••" while hidden (text swapped in the script). */
.dash-c2.c2-hidden .c2-amt { letter-spacing: .08em; }
/* Charts with amounts: a plain panel covers the chart area; title and legend stay visible. */
.dash-c2 .c2-mask { display: none; position: absolute; inset: 0; z-index: 3; flex-direction: column; align-items: center; justify-content: center; gap: 6px; background: #f8fafc; border-radius: 6px; text-align: center; }
.dash-c2.c2-hidden .c2-money .c2-mask { display: flex; }
.dash-c2.c2-hidden .c2-money canvas, .dash-c2.c2-hidden .c2-money .c2-nodata { visibility: hidden; }
.dash-c2 .c2-mask .c2-mask-ic { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 50%; background: #e0e7ff; color: #1e3a8a; font-size: 15px; }
.dash-c2 .c2-mask b { font-size: 13px; font-weight: 700; color: #334155; }
.dash-c2 .c2-mask button { border: 0; background: none; padding: 0; color: #1e40af; font-size: 12.5px; font-weight: 600; cursor: pointer; }
.dash-c2 .c2-mask button:hover { text-decoration: underline; }
[data-theme="dark"] .dash-c2 .c2-mask { background: #111827; }
[data-theme="dark"] .dash-c2 .c2-mask .c2-mask-ic { background: rgba(59, 130, 246, .18); color: #93c5fd; }
[data-theme="dark"] .dash-c2 .c2-mask b { color: #e2e8f0; }
[data-theme="dark"] .dash-c2 .c2-mask button { color: #93c5fd; }
/* 10. Fold a section. */
.dash-c2 .c2-fold { flex: none; display: inline-flex; align-items: center; gap: 5px; height: 28px; padding: 0 10px; border: 1px solid #e2e8f0; border-radius: 6px; background: #fff; color: #475569; font-size: 12.5px; font-weight: 600; cursor: pointer; }
.dash-c2 .c2-fold:hover { border-color: #1e3a8a; color: #1e3a8a; }
.dash-c2 .c2-fold i { transition: transform .15s; }
.dash-c2 .dash-section-divider.is-folded .c2-fold i { transform: rotate(-90deg); }
.dash-c2 .c2-folded { display: none !important; }

@media (max-width: 767px) {
    .dash-c2 .c2-hide-btn span { display: none; }
}

/* Dark mode */
[data-theme="dark"] .dash-c2 .dash-section-title span:last-child, [data-theme="dark"] .dash-c2 .crm-modern-stat-card .card-title { color: #f1f5f9 !important; }
[data-theme="dark"] .dash-c2 .c2-bar { background: rgba(17, 24, 39, .96); border-color: #1f2937; }
[data-theme="dark"] .dash-c2 .c2-bar-links a { color: #cbd5e1; }
[data-theme="dark"] .dash-c2 .c2-bar-links a:hover { background: rgba(148, 163, 184, .12); color: #fff; }
[data-theme="dark"] .dash-c2 .c2-bar-links a.is-on { background: #3b82f6; border-color: #3b82f6; color: #fff; }
[data-theme="dark"] .dash-c2 .c2-hide-btn[aria-pressed="true"] { background: rgba(59, 130, 246, .18); border-color: rgba(59, 130, 246, .45); color: #bfdbfe; }
[data-theme="dark"] .dash-c2 .c2-hide-btn, [data-theme="dark"] .dash-c2 .c2-fold { background: transparent; border-color: #334155; color: #cbd5e1; }
[data-theme="dark"] .dash-c2 .c2-nodata { background: rgba(17, 24, 39, .92); border-color: #334155; color: #94a3b8; }
[data-theme="dark"] .dash-c2 .col-md-6 > .crm-modern-stat-card .crm-modern-stat-chip { border-bottom-color: #1f2937 !important; }
[data-theme="dark"] .dash-c2 .col-md-6 > .crm-modern-stat-card .crm-modern-stat-chip:hover { background: rgba(148, 163, 184, .06) !important; }
[data-theme="dark"] .dash-c2 .col-md-6 > .crm-modern-stat-card .crm-modern-stat-chip span:first-child { color: #e2e8f0 !important; }
[data-theme="dark"] .dash-c2 .col-md-6 > .crm-modern-stat-card .crm-modern-stat-chip .badge { color: #93c5fd !important; }
[data-theme="dark"] .dash-c2 .col-md-6 > .crm-modern-stat-card .crm-modern-stat-chip.c2-zero span:first-child { color: #64748b !important; }
[data-theme="dark"] .dash-c2 .col-md-6 > .crm-modern-stat-card .crm-modern-stat-chip.c2-zero .badge { color: #475569 !important; }
</style>
<script>
$(function () {
    var $root = $('#c2Root');
    function store(k, v) { try { if (v === undefined) return localStorage.getItem(k); localStorage.setItem(k, v); } catch (e) { return null; } }

    // 3. Charts with nothing to show.
    var empty = <?php echo json_encode($c2_empty); ?>;
    // 9. Charts that show amounts are covered by "Hide amounts".
    $.each(empty, function (id, msg) {
        var $body = $('#' + id).parent();
        $body.addClass('c2-money').append('<div class="c2-mask"><span class="c2-mask-ic"><i class="fa fa-eye-slash"></i></span><b>Amounts hidden</b><button type="button" class="c2-mask-show">Show amounts</button></div>');
        if (msg) $body.append($('<div class="c2-nodata"></div>').text(msg));
    });

    // 7. Rows with 0 are greyed out.
    $root.find('.col-md-6 > .crm-modern-stat-card .crm-modern-stat-chip, .col-md-4 > .crm-modern-stat-card .crm-modern-stat-chip').each(function () {
        $(this).toggleClass('c2-zero', $.trim($(this).find('.badge').text()) === '0');
    });

    // 8 and 10. Sections: ids for the bar, and a Hide / Show button on each heading.
    var ids = { 'Pipeline and Closures': 'c3-pipeline', 'Team and Trends': 'c5-team', 'Operational Activity': 'c2-activity', 'Database, Leads and Orders': 'c2-database', 'Achievement and Projection': 'c2-achievement' };
    $root.find('.dash-section-divider').each(function () {
        var $d = $(this), title = $.trim($d.find('.dash-section-title > span:last-child').text()), id = ids[title];
        if (!id) return;
        $d.attr('id', id);
        var $btn = $('<button type="button" class="c2-fold" aria-expanded="true"><i class="fa fa-chevron-down"></i> <span>Hide</span></button>');
        $d.append($btn);
        function fold(on, keep) {
            $d.toggleClass('is-folded', on);
            $d.nextUntil('.dash-section-divider').not('.dash-drilldown-panel').toggleClass('c2-folded', on);
            $btn.attr('aria-expanded', on ? 'false' : 'true').find('span').text(on ? 'Show' : 'Hide');
            if (keep !== false) store('crm_c2_fold_' + id, on ? '1' : '0');
        }
        $btn.on('click', function () { fold(!$d.hasClass('is-folded')); });
        if (store('crm_c2_fold_' + id) === '1') fold(true, false);
        $d.data('c2fold', fold);
    });

    // Keep the bar just under the top header.
    function setTop() {
        var $hd = $('.main-header').first(), pos = $hd.css('position');
        var h = (pos === 'fixed' || pos === 'sticky') ? ($hd[0] ? $hd[0].offsetHeight : 0) : 0;
        $root[0].style.setProperty('--c2-top', h + 'px');
    }
    setTop(); $(window).on('resize', setTop);

    // Section links: open a folded section, scroll to it, mark the one in view.
    $('#c2Bar').on('click', 'a[data-sec]', function (e) {
        var id = $(this).data('sec'), $t = $('#' + id);
        if (!$t.length) return;
        e.preventDefault();
        var $d = $t.hasClass('dash-section-divider') ? $t : $t.prevAll('.dash-section-divider').first();
        if ($d.hasClass('is-folded') && $d.data('c2fold')) $d.data('c2fold')(false);
        $t[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
    var secs = ['c2-top', 'c3-pipeline', 'c5-team', 'c2-activity', 'c2-database', 'c2-achievement'];
    function markOn() {
        var line = (parseInt(getComputedStyle($root[0]).getPropertyValue('--c2-top'), 10) || 0) + 90, cur = 'c2-top';
        $.each(secs, function (i, id) { var el = document.getElementById(id); if (el && !$(el).hasClass('c2-folded') && el.getBoundingClientRect().top <= line) cur = id; });
        $('#c2Bar a').removeClass('is-on').filter('[data-sec="' + cur + '"]').addClass('is-on');
    }
    // Any scrolling element (the page, or body when it scrolls itself).
    var markT = null;
    markOn(); document.addEventListener('scroll', function () { clearTimeout(markT); markT = setTimeout(markOn, 60); }, { passive: true, capture: true });

    // 9. Hide amounts, remembered on this computer.
    function setHide(on) {
        $root.toggleClass('c2-hidden', on);
        // Tile amounts: "AED 2,500" becomes "AED •••••" and comes back as it was.
        $root.find('.c2-amt').each(function () {
            var $a = $(this);
            if ($a.data('c2orig') === undefined) $a.data('c2orig', $a.text());
            var orig = String($a.data('c2orig'));
            $a.text(on ? orig.replace(/[\d.,]+[KMB]?/i, '•••••') : orig);
        });
        $('#c2HideAmt').attr('aria-pressed', on ? 'true' : 'false').find('span').text(on ? 'Show amounts' : 'Hide amounts');
        $('#c2HideAmt i').attr('class', 'fa ' + (on ? 'fa-eye' : 'fa-eye-slash'));
        store('crm_c2_hide_amt', on ? '1' : '0');
    }
    $('#c2HideAmt').on('click', function () { setHide(!$root.hasClass('c2-hidden')); });
    $root.on('click', '.c2-mask-show', function () { setHide(false); });
    setHide(store('crm_c2_hide_amt') === '1');
});
</script>

<style>
/* Classic 3 and 5: this month's % in the chart header, Sales Pipeline, Closures. */
.dash-c2 .c3-pct { display: inline-flex; align-items: center; gap: 7px; margin: 0 10px; min-width: 0; font-size: 10.5px; font-weight: 600; color: #475569; white-space: nowrap; }
.dash-c2 .c3-pct-lbl b { color: #0f172a; font-size: 11.5px; }
.dash-c2 .c3-pct-lbl b.is-met { color: #16a34a; }
.dash-c2 .c3-pct-none { color: #64748b; }
.dash-c2 .c3-pct-bar { position: relative; width: 110px; height: 7px; border-radius: 999px; background: #e2e8f0; }
.dash-c2 .c3-pct-bar i { position: absolute; left: 0; top: 0; bottom: 0; border-radius: 999px; background: linear-gradient(90deg, #22c55e, #16a34a); }
.dash-c2 .c3-pct-bar em { position: absolute; top: -3px; width: 2px; height: 13px; margin-left: -1px; background: #0072f0; border-radius: 1px; }
.dash-c2 .c3-pct-pro { color: #0072f0; }
@media (max-width: 1279px) { .dash-c2 .c3-pct-pro { display: none; } .dash-c2 .c3-pct-bar { width: 70px; } }
/* Narrow screens: the % goes on its own line under the title and the card grows, so the chart is not cut. */
@media (max-width: 991px) {
    .dash-c2 .c3-toprow-pct, .dash-c2 .c2-toprow .dash-top-chart-card { height: auto !important; max-height: none !important; }
    .dash-c2 .c2-toprow .dash-chart-header { flex-wrap: wrap; row-gap: 4px; }
    .dash-c2 .c3-pct { order: 3; flex: 1 0 100%; margin: 0; }
    .dash-c2 .c3-pct-pro { display: inline; }
    .dash-c2 .c3-pct-bar { width: 110px; }
}

.dash-c2 .c3-pipe-row { margin-left: 0; margin-right: 0; }
.dash-c2 .c3-col-l { padding-left: 14px; padding-right: 4px; }
.dash-c2 .c3-col-r { padding-left: 4px; padding-right: 14px; }
@media (min-width: 992px) {
    .dash-c2 .c3-pipe-row { display: flex; }
    .dash-c2 .c3-pipe-row > [class*="col-"] { float: none; display: flex; }
    .dash-c2 .c3-pipe-row .c3-card { width: 100%; }
}
@media (max-width: 991px) {
    .dash-c2 .c3-col-l, .dash-c2 .c3-col-r { padding-left: 14px; padding-right: 14px; }
}
.dash-c2 .c3-card { margin-bottom: 8px !important; display: flex; flex-direction: column; }
.dash-c2 .c3-card .card-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.dash-c2 .c3-pipe { display: flex; align-items: stretch; gap: 6px; flex: 1; }
.dash-c2 .c3-step { flex: 1 1 0; min-width: 0; padding: 10px 12px; border-radius: 8px; border: 1px solid #e2e8f0; background: rgba(255, 255, 255, .75); }
.dash-c2 .c3-step-name { font-size: 11.5px; font-weight: 700; color: #475569; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.dash-c2 .c3-step-num { font-size: 22px; font-weight: 800; line-height: 1.2; margin-top: 4px; color: #0f172a; }
.dash-c2 .c3-step-amt { font-size: 11.5px; font-weight: 600; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.dash-c2 .c3-step-bar { height: 5px; margin-top: 8px; border-radius: 999px; background: #eef2f7; overflow: hidden; }
.dash-c2 .c3-step-bar i { display: block; height: 100%; border-radius: 999px; }
.dash-c2 .c3-s1 { border-top: 3px solid #1e3a8a; } .dash-c2 .c3-s1 .c3-step-name i { color: #1e3a8a; } .dash-c2 .c3-s1 .c3-step-bar i { background: #1e3a8a; }
.dash-c2 .c3-s2 { border-top: 3px solid #0072f0; } .dash-c2 .c3-s2 .c3-step-name i { color: #0072f0; } .dash-c2 .c3-s2 .c3-step-bar i { background: #0072f0; }
.dash-c2 .c3-s3 { border-top: 3px solid #0f8a80; } .dash-c2 .c3-s3 .c3-step-name i { color: #0f8a80; } .dash-c2 .c3-s3 .c3-step-bar i { background: #0f8a80; }
.dash-c2 .c3-s4 { border-top: 3px solid #16a34a; } .dash-c2 .c3-s4 .c3-step-name i { color: #16a34a; } .dash-c2 .c3-s4 .c3-step-bar i { background: #16a34a; }
.dash-c2 .c3-arrow { flex: none; display: flex; align-items: center; justify-content: center; width: 22px; color: #64748b; }
.dash-c2 .c3-arrow i { font-size: 20px; line-height: 1; }
.dash-c2 .c3-arrow span { font-size: 10.5px; font-weight: 700; color: #475569; }
.dash-c2 .c3-note { margin-top: 8px; font-size: 10.5px; color: #64748b; }
.dash-c2 .c3-clo { width: 100%; border-collapse: collapse; font-size: 12px; }
.dash-c2 .c3-clo th { font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .03em; text-align: center; padding: 0 4px 6px; }
.dash-c2 .c3-clo td { padding: 5px 4px; border-top: 1px solid #eef2f7; color: #334155; font-weight: 600; }
.dash-c2 .c3-clo td + td { text-align: center; width: 70px; }
.dash-c2 .c3-clo .badge { min-width: 34px; }
.dash-c2 .c3-clo .badge.c3-zero { opacity: .45; }
.dash-c2 .c3-clo tfoot td { border-top: 2px solid #e2e8f0; font-weight: 800; color: #0f172a; }
@media (max-width: 767px) {
    .dash-c2 .c3-pipe { flex-wrap: wrap; }
    .dash-c2 .c3-step { flex: 1 1 40%; }
    .dash-c2 .c3-arrow { display: none; }
}
[data-theme="dark"] .dash-c2 .c3-pct { color: #94a3b8; }
[data-theme="dark"] .dash-c2 .c3-pct-lbl b { color: #f1f5f9; }
[data-theme="dark"] .dash-c2 .c3-pct-lbl b.is-met { color: #4ade80; }
[data-theme="dark"] .dash-c2 .c3-pct-bar, [data-theme="dark"] .dash-c2 .c3-step-bar { background: #1f2937; }
[data-theme="dark"] .dash-c2 .c3-pct-pro { color: #60a5fa; }
[data-theme="dark"] .dash-c2 .c3-pct-bar em { background: #60a5fa; }
[data-theme="dark"] .dash-c2 .c3-step { background: rgba(17, 24, 39, .7); border-color: #1f2937; }
[data-theme="dark"] .dash-c2 .c3-step-name, [data-theme="dark"] .dash-c2 .c3-arrow span { color: #cbd5e1; }
[data-theme="dark"] .dash-c2 .c3-step-num, [data-theme="dark"] .dash-c2 .c3-clo tfoot td { color: #f1f5f9; }
[data-theme="dark"] .dash-c2 .c3-step-amt, [data-theme="dark"] .dash-c2 .c3-clo th { color: #94a3b8; }
[data-theme="dark"] .dash-c2 .c3-clo td { color: #e2e8f0; border-top-color: #1f2937; }
[data-theme="dark"] .dash-c2 .c3-clo tfoot td { border-top-color: #334155; }
</style>


<script>
// Classic 5: pipeline My / Team, reload button, one colour per section.
$(function () {
    $('.c5-scope').on('click', 'button', function () {
        var sc = $(this).data('scope'), $card = $(this).closest('.c3-card');
        $(this).addClass('is-on').attr('aria-pressed', 'true').siblings().removeClass('is-on').attr('aria-pressed', 'false');
        $card.find('.c3-pipe').each(function () { this.hidden = $(this).data('scope') !== sc; });
        try { localStorage.setItem('crm_c5_pipe_scope', sc); } catch (e) {}
    });
    try { var sc = localStorage.getItem('crm_c5_pipe_scope'); if (sc === 'my') $('.c5-scope button[data-scope="my"]').trigger('click'); } catch (e) {}
    $('#c5Refresh').on('click', function () { $(this).find('i').addClass('fa-spin'); location.reload(); });
    // Charts already drawn take the new text colour when light / dark mode is switched.
    function c5ChartColours() {
        if (!window.Chart || !Chart.instances) return;
        var dark = document.documentElement.getAttribute('data-theme') === 'dark';
        var ink = dark ? '#cbd5e1' : '#1e293b', grid = dark ? 'rgba(255,255,255,.07)' : 'rgba(0,0,0,.04)';
        $.each(Chart.instances, function (id, ch) {
            if (!ch || !ch.scale) return;
            ch.scale.textColor = ink; ch.scale.gridLineColor = grid;
            ch.options.scaleFontColor = ink; ch.options.scaleGridLineColor = grid;
            ch.update();
        });
    }
    if (window.MutationObserver) new MutationObserver(c5ChartColours).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
    $(window).on('load', function () { setTimeout(c5ChartColours, 50); });
    $('#c2Root .dash-section-divider[id]').each(function () {
        var id = this.id;
        $(this).nextUntil('.dash-section-divider').addClass('c5-in-' + id);
    });
});
</script>
<style>
/* Classic 5 only. */
/* 2. One set of status colours: missed red, last 7 days orange, today sky blue, next 7 days blue, future green.
   Deep enough that white numbers on them stay readable in light and dark mode. */
.dash-c5 .badge.bg-red, .dash-c5 .crm-modern-stat-chip .badge.bg-red { background: #dc2626 !important; background-color: #dc2626 !important; color: #fff !important; }
.dash-c5 .badge.bg-orange, .dash-c5 .crm-modern-stat-chip .badge.bg-orange { background: #ea580c !important; background-color: #ea580c !important; color: #fff !important; }
.dash-c5 .badge.bg-skyblue, .dash-c5 .crm-modern-stat-chip .badge.bg-skyblue { background: #0284c7 !important; background-color: #0284c7 !important; color: #fff !important; }
.dash-c5 .badge.bg-blue, .dash-c5 .crm-modern-stat-chip .badge.bg-blue { background: #2563eb !important; background-color: #2563eb !important; color: #fff !important; }
.dash-c5 .badge.bg-green, .dash-c5 .crm-modern-stat-chip .badge.bg-green { background: #15803d !important; background-color: #15803d !important; color: #fff !important; }
.dash-c5 .crm-seg-bar .crm-seg { opacity: 1 !important; }
.dash-c5 .crm-seg-bar .crm-seg.seg-missed { background: #dc2626 !important; }
.dash-c5 .crm-seg-bar .crm-seg.seg-last7 { background: #ea580c !important; }
.dash-c5 .crm-seg-bar .crm-seg.seg-today { background: #0284c7 !important; }
.dash-c5 .crm-seg-bar .crm-seg.seg-next7 { background: #2563eb !important; }
.dash-c5 .crm-seg-bar .crm-seg.seg-future { background: #15803d !important; }

/* 3. One colour per section: card top line, heading mark and the dot in the section bar. */
.dash-c5 { --c5-pipe: #0072f0; --c5-team: #7c3aed; --c5-act: #0f8a80; --c5-db: #0891b2; --c5-ord: #ea580c; --c5-ach: #16a34a; }
.dash-c5 .c5-in-c3-pipeline .crm-modern-stat-card, .dash-c5 .c5-in-c3-pipeline .dash-chart-card { border-top-color: var(--c5-pipe) !important; }
.dash-c5 .c5-in-c5-team .crm-modern-stat-card { border-top-color: var(--c5-team) !important; }
.dash-c5 .c5-in-c2-activity .crm-modern-stat-card { border-top-color: var(--c5-act) !important; }
.dash-c5 .c5-in-c2-database .crm-modern-stat-card, .dash-c5 .c5-in-c2-database .dash-chart-card { border-top-color: var(--c5-db) !important; }
.dash-c5 .c5-in-c2-orders .crm-modern-stat-card, .dash-c5 .c5-in-c2-orders .dash-chart-card { border-top-color: var(--c5-ord) !important; }
.dash-c5 .c5-in-c2-achievement .crm-modern-stat-card, .dash-c5 .c5-in-c2-achievement .dash-chart-card { border-top-color: var(--c5-ach) !important; }
.dash-c5 #c3-pipeline .dash-section-title-icon span { background: var(--c5-pipe) !important; }
.dash-c5 #c5-team .dash-section-title-icon span { background: var(--c5-team) !important; }
.dash-c5 #c2-activity .dash-section-title-icon span { background: var(--c5-act) !important; }
.dash-c5 #c2-database .dash-section-title-icon span { background: var(--c5-db) !important; }
.dash-c5 #c2-orders .dash-section-title-icon span { background: var(--c5-ord) !important; }
.dash-c5 #c2-achievement .dash-section-title-icon span { background: var(--c5-ach) !important; }
.dash-c5 .c2-bar-links a[data-sec]::before { content: ""; display: inline-block; width: 6px; height: 6px; margin-right: 5px; border-radius: 50%; vertical-align: 1px; background: #1e3a8a; }
.dash-c5 .c2-bar-links a[data-sec="c3-pipeline"]::before { background: var(--c5-pipe); }
.dash-c5 .c2-bar-links a[data-sec="c5-team"]::before { background: var(--c5-team); }
.dash-c5 .c2-bar-links a[data-sec="c2-activity"]::before { background: var(--c5-act); }
.dash-c5 .c2-bar-links a[data-sec="c2-database"]::before, .dash-c5 .c2-bar-links a[data-sec="c2-leads"]::before { background: var(--c5-db); }
.dash-c5 .c2-bar-links a[data-sec="c2-orders"]::before { background: var(--c5-ord); }
.dash-c5 .c2-bar-links a[data-sec="c2-achievement"]::before { background: var(--c5-ach); }
.dash-c5 .c2-bar-links a.is-on::before { background: #fff !important; }

/* 1, 6. Needs attention today, updated time and reload in the section bar. */
.dash-c5 .c5-attn { flex: none; display: flex; align-items: center; gap: 4px; }
.dash-c5 .c5-attn-i { display: inline-flex; align-items: center; gap: 5px; height: 22px; padding: 0 8px; border-radius: 5px; font-size: 11.5px; font-weight: 600; text-decoration: none !important; white-space: nowrap; border: 1px solid transparent; }
.dash-c5 .c5-attn-i b { font-size: 12.5px; font-weight: 800; }
.dash-c5 .c5-attn-i i { font-size: 11px; }
.dash-c5 .c5-attn-i.c5-red { background: #fef2f2; border-color: #fecaca; color: #b91c1c; }
.dash-c5 .c5-attn-i.c5-sky { background: #f0f9ff; border-color: #bae6fd; color: #0369a1; }
.dash-c5 .c5-attn-i.c5-orange { background: #fff7ed; border-color: #fed7aa; color: #c2410c; }
.dash-c5 .c5-attn-i.c5-blue { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }
.dash-c5 .c5-attn-i.is-zero { background: transparent; border-color: #e2e8f0; color: #64748b; }
.dash-c5 a.c5-attn-i:hover { filter: brightness(.96); }
.dash-c5 .c5-upd { flex: none; font-size: 11px; font-weight: 600; color: #64748b; white-space: nowrap; }
.dash-c5 .c5-refresh { flex: none; width: 24px; height: 22px; padding: 0; border: 1px solid #cbd5e1; border-radius: 5px; background: #fff; color: #334155; cursor: pointer; }
.dash-c5 .c5-refresh:hover { background: #f1f5f9; }
@media (max-width: 1499px) { .dash-c5 .c5-attn-i span { display: none; } }
@media (max-width: 991px) { .dash-c5 .c5-upd { display: none; } }
@media (max-width: 767px) { .dash-c5 .c5-attn { display: none; } }

/* 8. Change against last month on the tiles. */
.dash-c5 .c5-chg { display: inline-block; margin-left: 4px; padding: 0 5px; border-radius: 999px; font-size: 9.5px; font-weight: 700; line-height: 15px; vertical-align: 1px; background: rgba(255, 255, 255, .2); color: #fff; white-space: nowrap; }
.dash-c5 .c5-chg.is-up { background: rgba(21, 128, 61, .75); }
.dash-c5 .c5-chg.is-down { background: rgba(185, 28, 28, .7); }

/* 9. My / Team on the pipeline. */
.dash-c5 .c5-scope { display: inline-flex; padding: 2px; border: 1px solid #cbd5e1; border-radius: 999px; background: #fff; }
.dash-c5 .c5-scope button { height: 20px; padding: 0 10px; border: 0; border-radius: 999px; background: transparent; color: #334155; font-size: 11px; font-weight: 700; cursor: pointer; }
.dash-c5 .c5-scope button.is-on { background: #0072f0; color: #fff; }
.dash-c5 .c3-pipe[hidden] { display: none !important; }

/* Database, Leads and Orders: the three cards in a row share one height. */
@media (min-width: 992px) { .dash-c5 .c5-states-row { display: flex; } .dash-c5 .c5-states-row > [class*="col-"] { float: none; display: flex; } .dash-c5 .c5-states-row .crm-modern-stat-card { width: 100%; } }

/* New cards. */
.dash-c5 .c5-team-row { margin-left: 0; margin-right: 0; }
.dash-c5 .c5-team-row > [class*="col-"] { padding-left: 4px; padding-right: 4px; }
.dash-c5 .c5-team-row > [class*="col-"]:first-child { padding-left: 14px; }
.dash-c5 .c5-team-row > [class*="col-"]:last-child { padding-right: 14px; }
@media (min-width: 1200px) { .dash-c5 .c5-team-row { display: flex; } .dash-c5 .c5-team-row > [class*="col-"] { float: none; display: flex; } .dash-c5 .c5-team-row .c5-card { width: 100%; } }
@media (min-width: 768px) and (max-width: 1199px) { .dash-c5 .c5-team-row > [class*="col-"]:nth-child(odd) { padding-left: 14px; } .dash-c5 .c5-team-row > [class*="col-"]:nth-child(even) { padding-right: 14px; } }
@media (max-width: 767px) { .dash-c5 .c5-team-row > [class*="col-"] { padding-left: 14px; padding-right: 14px; } }
.dash-c5 .c5-card { margin-bottom: 8px !important; display: flex; flex-direction: column; }
.dash-c5 .c5-card .card-top { display: flex; justify-content: space-between; align-items: center; gap: 6px; margin-bottom: 10px; }
.dash-c5 .c5-card .card-total-pill { flex: none; white-space: nowrap; }
.dash-c5 .c5-card .card-title { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; min-width: 0; }
.dash-c5 .c5-note { margin-top: auto; padding-top: 8px; font-size: 10.5px; color: #64748b; }
.dash-c5 .c5-empty { flex: 1; display: flex; align-items: center; justify-content: center; min-height: 120px; font-size: 12px; font-weight: 600; color: #64748b; }
.dash-c5 .c5-lb { list-style: none; margin: 0; padding: 0; }
.dash-c5 .c5-lb li { display: flex; gap: 8px; align-items: flex-start; padding: 5px 0; border-top: 1px solid #eef2f7; }
.dash-c5 .c5-lb li:first-child { border-top: 0; }
.dash-c5 .c5-lb-rank { flex: none; width: 20px; height: 20px; border-radius: 50%; background: #eef2f7; color: #475569; font-size: 11px; font-weight: 800; text-align: center; line-height: 20px; }
.dash-c5 .c5-lb li:nth-child(1) .c5-lb-rank { background: #fef3c7; color: #b45309; }
.dash-c5 .c5-lb-main { flex: 1; min-width: 0; }
.dash-c5 .c5-lb-top { display: flex; justify-content: space-between; gap: 6px; font-size: 12px; }
.dash-c5 .c5-lb-name { font-weight: 700; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.dash-c5 .c5-lb-amt { flex: none; font-weight: 700; color: #334155; }
.dash-c5 .c5-lb-bar, .dash-c5 .c5-ag-bar { height: 5px; margin-top: 4px; border-radius: 999px; background: #eef2f7; overflow: hidden; }
.dash-c5 .c5-lb-bar i, .dash-c5 .c5-ag-bar i { display: block; height: 100%; border-radius: 999px; background: #7c3aed; }
.dash-c5 .c5-lb-sub { margin-top: 2px; font-size: 10.5px; color: #64748b; }
.dash-c5 .c5-spark { width: 100%; height: 110px; display: block; }
.dash-c5 .c5-spark-area { fill: rgba(124, 58, 237, .12); }
.dash-c5 .c5-spark-line { fill: none; stroke: #7c3aed; stroke-width: 2; vector-effect: non-scaling-stroke; }
.dash-c5 .c5-spark-dot { fill: #fff; stroke: #7c3aed; stroke-width: 1.5; vector-effect: non-scaling-stroke; }
.dash-c5 .c5-spark-x { display: flex; justify-content: space-between; margin-top: 4px; font-size: 11px; color: #64748b; }
.dash-c5 .c5-spark-x b { color: #0f172a; }
.dash-c5 .c5-ag { padding: 6px 0; }
.dash-c5 .c5-ag-top { display: flex; justify-content: space-between; font-size: 12px; font-weight: 600; color: #334155; }
.dash-c5 .c5-ag-top b { color: #0f172a; font-size: 13px; }
.dash-c5 .c5-ag-top em { font-style: normal; font-size: 10.5px; color: #64748b; margin-left: 4px; }
.dash-c5 .c5-ag-ok .c5-ag-bar i { background: #16a34a; }
.dash-c5 .c5-ag-mid .c5-ag-bar i { background: #f97316; }
.dash-c5 .c5-ag-old .c5-ag-bar i { background: #dc2626; }
.dash-c5 .c5-calls td + td { width: 56px; font-weight: 700; }
.dash-c5 .c5-ytd { margin-bottom: 10px !important; }
.dash-c5 .c5-ytd-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 28px; }
@media (max-width: 767px) { .dash-c5 .c5-ytd-grid { grid-template-columns: 1fr; } }
.dash-c5 .c5-ytd-who { font-size: 11px; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; color: #64748b; margin-bottom: 4px; }
.dash-c5 .c5-ytd-figs { display: flex; gap: 18px; flex-wrap: wrap; }
.dash-c5 .c5-ytd-figs > div { display: flex; flex-direction: column; }
.dash-c5 .c5-ytd-figs span { font-size: 10.5px; font-weight: 600; color: #64748b; }
.dash-c5 .c5-ytd-figs b { font-size: 16px; font-weight: 800; color: #0f172a; }
.dash-c5 .c5-ytd-figs b.is-met { color: #16a34a; }
.dash-c5 .c5-ytd-bar { width: 100%; margin: 8px 0 6px; }
.dash-c5 .c5-ytd-line { font-size: 11.5px; color: #64748b; }
.dash-c5 .c5-ytd-line b { color: #0f172a; }
.dash-c5 .c5-dot { margin: 0 6px; color: #94a3b8; }

/* 13. Softer navy and teal tiles, with a thin light edge at the top. */
.dash-c5 .dash-kpi-col1, .dash-c5 .dash-kpi-col2 { background: linear-gradient(135deg, rgba(49, 82, 168, .66) 0%, rgba(36, 60, 128, .88) 100%), #253c80 url("<?php echo base_url(); ?>assets/background-auth.jpg") 30% 40% / 460px auto no-repeat !important; box-shadow: inset 0 1px 0 rgba(255, 255, 255, .22), 0 4px 12px rgba(36, 60, 128, .2) !important; }
.dash-c5 .dash-kpi-col3, .dash-c5 .dash-kpi-col4 { background: linear-gradient(135deg, rgba(28, 140, 131, .66) 0%, rgba(20, 110, 104, .88) 100%), #146e68 url("<?php echo base_url(); ?>assets/background-auth.jpg") 30% 40% / 460px auto no-repeat !important; box-shadow: inset 0 1px 0 rgba(255, 255, 255, .22), 0 4px 12px rgba(20, 110, 104, .2) !important; }
.dash-c5 .dash-kpi-col2, .dash-c5 .dash-kpi-col4 { background-position: 0 0, 70% 60% !important; }

/* 14. Small lift on hover. */
.dash-c5 .c3-step, .dash-c5 .c5-card { transition: transform .15s ease, box-shadow .15s ease; }
.dash-c5 .c3-step:hover { transform: translateY(-2px); box-shadow: 0 6px 14px rgba(15, 23, 42, .08); }
.dash-c5 .c3-clo tbody tr { transition: background-color .15s ease; }
.dash-c5 .c3-clo tbody tr:hover td { background: rgba(241, 245, 249, .8); }

/* Dark mode. */
[data-theme="dark"] .dash-c5 .dash-chart-legend, [data-theme="dark"] .dash-c5 .dash-chart-legend span { color: #cbd5e1 !important; }
[data-theme="dark"] .dash-c5 .c5-attn-i.c5-red { background: rgba(220, 38, 38, .15); border-color: rgba(248, 113, 113, .35); color: #fca5a5; }
[data-theme="dark"] .dash-c5 .c5-attn-i.c5-sky { background: rgba(14, 165, 233, .15); border-color: rgba(56, 189, 248, .35); color: #7dd3fc; }
[data-theme="dark"] .dash-c5 .c5-attn-i.c5-orange { background: rgba(249, 115, 22, .15); border-color: rgba(251, 146, 60, .35); color: #fdba74; }
[data-theme="dark"] .dash-c5 .c5-attn-i.c5-blue { background: rgba(37, 99, 235, .18); border-color: rgba(96, 165, 250, .35); color: #93c5fd; }
[data-theme="dark"] .dash-c5 .c5-attn-i.is-zero { background: transparent; border-color: #334155; color: #64748b; }
[data-theme="dark"] .dash-c5 .c5-refresh, [data-theme="dark"] .dash-c5 .c5-scope { background: #111827; border-color: #334155; color: #cbd5e1; }
[data-theme="dark"] .dash-c5 .c5-scope button { color: #cbd5e1; }
[data-theme="dark"] .dash-c5 .c5-scope button.is-on { background: #3b82f6; color: #fff; }
[data-theme="dark"] .dash-c5 .c5-lb li { border-top-color: #1f2937; }
[data-theme="dark"] .dash-c5 .c5-lb-rank, [data-theme="dark"] .dash-c5 .c5-lb-bar, [data-theme="dark"] .dash-c5 .c5-ag-bar { background: #1f2937; color: #cbd5e1; }
[data-theme="dark"] .dash-c5 .c5-lb-name, [data-theme="dark"] .dash-c5 .c5-spark-x b, [data-theme="dark"] .dash-c5 .c5-ag-top b, [data-theme="dark"] .dash-c5 .c5-ytd-figs b, [data-theme="dark"] .dash-c5 .c5-ytd-line b { color: #f1f5f9; }
[data-theme="dark"] .dash-c5 .c5-lb-amt, [data-theme="dark"] .dash-c5 .c5-ag-top { color: #cbd5e1; }
[data-theme="dark"] .dash-c5 .c5-ytd-figs b.is-met { color: #4ade80; }
[data-theme="dark"] .dash-c5 .c5-spark-dot { fill: #111827; }
[data-theme="dark"] .dash-c5 .c2-empty-ring { stroke: #334155; }
[data-theme="dark"] .dash-c5 .c3-clo tbody tr:hover td { background: rgba(31, 41, 55, .8); }
[data-theme="dark"] .dash-c5 .c3-step:hover { box-shadow: 0 6px 14px rgba(0, 0, 0, .35); }
</style>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
