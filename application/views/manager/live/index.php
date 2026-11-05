<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<style>
.live-sheet-wrapper {
    background: #f8fafc;
    min-height: calc(100vh - 80px);
    padding: 20px 24px 60px 24px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}
.live-sheet-topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 14px;
    margin-bottom: 20px;
    padding-bottom: 14px;
    border-bottom: 1px solid #e2e8f0;
}
.live-sheet-title {
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.02em;
    margin: 0;
}
.live-btn-action {
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #0f172a;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.live-btn-action:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
}
.live-sheet-section-title {
    font-size: 15px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: 0.02em;
    margin: 22px 0 10px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.live-sheet-section-title::before {
    content: '';
    display: inline-block;
    width: 4px;
    height: 16px;
    background: #1e3a8a;
    border-radius: 2px;
}
.live-sheet-table-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    margin-bottom: 24px;
}
.live-excel-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    text-align: left;
}
.live-excel-table th {
    background: #f8fafc;
    color: #0f172a;
    font-weight: 700;
    font-size: 12px;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    padding: 12px 18px;
    border-bottom: 1.5px solid #cbd5e1;
    border-right: 1px solid #e2e8f0;
}
.live-excel-table th:last-child {
    border-right: none;
}
.live-excel-table th.col-num {
    text-align: right;
    width: 18%;
}
.live-excel-table td {
    padding: 11px 18px;
    border-bottom: 1px solid #e2e8f0;
    border-right: 1px solid #e2e8f0;
    color: #1e293b;
    vertical-align: middle;
}
.live-excel-table td:last-child {
    border-right: none;
}
.live-excel-table tr:last-child td {
    border-bottom: none;
}
.live-excel-table td.col-metric {
    font-weight: 600;
    color: #0f172a;
    background: #ffffff;
}
.live-excel-table td.col-val {
    text-align: right;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
    letter-spacing: -0.01em;
}

.live-excel-table th.col-header-today {
    background: #f8fafc !important;
    color: #065f46 !important;
    border-bottom: 2px solid #0f172a !important;
}
.live-excel-table td.col-val-today {
    background: #f8fafc !important;
    color: #0f172a !important;
}

.live-excel-table th.col-header-7d {
    background: #f8fafc !important;
    color: #334155 !important;
    border-bottom: 2px solid #475569 !important;
}
.live-excel-table td.col-val-7d {
    background: #fefce8 !important;
    color: #b45309 !important;
}

.live-excel-table th.col-header-month {
    background: #eff6ff !important;
    color: #1e40af !important;
    border-bottom: 2px solid #1e3a8a !important;
}
.live-excel-table td.col-val-month {
    background: #eff6ff !important;
    color: #1e3a8a !important;
}

.live-excel-table th.col-header-quarter {
    background: #f5f3ff !important;
    color: #5b21b6 !important;
    border-bottom: 2px solid #1e3a8a !important;
}
.live-excel-table td.col-val-quarter {
    background: #faf5ff !important;
    color: #1e3a8a !important;
}

.live-excel-table tr:hover td {
    filter: brightness(0.96);
}

.live-excel-table td.col-perf-title {
    font-weight: 700;
    color: #0f172a;
    width: 32%;
}
.live-excel-table td.col-perf-name {
    font-weight: 600;
    color: #1e3a8a;
    width: 34%;
}

[data-theme="dark"] .live-sheet-wrapper {
    background: #0f172a !important;
}
[data-theme="dark"] .live-sheet-title {
    color: #f8fafc !important;
}
[data-theme="dark"] .live-sheet-section-title {
    color: #f8fafc !important;
}
[data-theme="dark"] .live-sheet-table-card {
    background: #1e293b !important;
    border-color: #334155 !important;
}
[data-theme="dark"] .live-excel-table th {
    background: #1e293b !important;
    color: #f8fafc !important;
    border-color: #334155 !important;
}
[data-theme="dark"] .live-excel-table td {
    border-color: #334155 !important;
    color: #f8fafc !important;
}
[data-theme="dark"] .live-excel-table td.col-metric {
    background: #1e293b !important;
    color: #f8fafc !important;
}
[data-theme="dark"] .live-excel-table th.col-header-today {
    background: rgba(16, 185, 129, 0.2) !important;
    color: #34d399 !important;
}
[data-theme="dark"] .live-excel-table td.col-val-today {
    background: rgba(16, 185, 129, 0.12) !important;
    color: #6ee7b7 !important;
}
[data-theme="dark"] .live-excel-table th.col-header-7d {
    background: rgba(245, 158, 11, 0.2) !important;
    color: #fbbf24 !important;
}
[data-theme="dark"] .live-excel-table td.col-val-7d {
    background: rgba(245, 158, 11, 0.12) !important;
    color: #fcd34d !important;
}
[data-theme="dark"] .live-excel-table th.col-header-month {
    background: rgba(59, 130, 246, 0.2) !important;
    color: #60a5fa !important;
}
[data-theme="dark"] .live-excel-table td.col-val-month {
    background: rgba(59, 130, 246, 0.12) !important;
    color: #93c5fd !important;
}
[data-theme="dark"] .live-excel-table th.col-header-quarter {
    background: rgba(139, 92, 246, 0.2) !important;
    color: #a78bfa !important;
}
[data-theme="dark"] .live-excel-table td.col-val-quarter {
    background: rgba(139, 92, 246, 0.12) !important;
    color: #c4b5fd !important;
}
[data-theme="dark"] .live-btn-action {
    background: #1e293b !important;
    border-color: #334155 !important;
    color: #f8fafc !important;
}

/* ---------- Live page: cards, people and centers ---------- */
.live-sub { font-size: 12.5px; color: #64748b; font-weight: 600; }
.live-updated { font-size: 12px; color: #64748b; font-weight: 600; white-space: nowrap; }
.live-kpis { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 14px; }
.live-kpi { background: #fff; border: 1px solid #cbd5e1; border-radius: 10px; padding: 14px 16px; min-width: 0; }
.live-kpi .lbl { font-size: 11.5px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: .05em; }
.live-kpi .val { display: block; font-size: 26px; font-weight: 800; color: #0f172a; margin: 4px 0 2px; font-variant-numeric: tabular-nums; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.live-kpi .cmp { font-size: 12.5px; font-weight: 600; color: #64748b; }
.live-up { color: #15803d; font-weight: 800; } .live-down { color: #b91c1c; font-weight: 800; }
.live-bar { height: 7px; background: #e2e8f0; border-radius: 6px; overflow: hidden; margin-top: 7px; }
.live-bar i { display: block; height: 100%; background: linear-gradient(90deg, #2563eb, #1e3a8a); border-radius: 6px; transition: width .6s; }
.live-scroll { overflow-x: auto; }
.live-people td, .live-people th { white-space: nowrap; }
.live-people td.num, .live-people th.num { text-align: right; font-variant-numeric: tabular-nums; }
.live-people tr.is-me td { background: #eff6ff; }
.live-person { display: flex; align-items: center; gap: 10px; min-width: 0; }
.live-av { width: 30px; height: 30px; border-radius: 50%; background: #1e3a8a; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-weight: 800; font-size: 12px; flex-shrink: 0; overflow: hidden; }
.live-av img { width: 100%; height: 100%; object-fit: cover; }
.live-person b { font-weight: 700; color: #0f172a; }
.live-person small { display: block; color: #64748b; font-size: 11.5px; }
.live-tp { min-width: 110px; }
.live-tp small { display: block; font-size: 11.5px; color: #64748b; margin-top: 2px; }
.live-filter { height: 34px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0 10px; font-size: 13px; width: 220px; max-width: 100%; background: #fff; color: #0f172a; }
.live-head-row { display: flex; justify-content: space-between; align-items: flex-end; gap: 10px; flex-wrap: wrap; margin: 22px 0 10px; }
.live-head-row .live-sheet-section-title { margin: 0; }
.live-empty { padding: 18px; text-align: center; color: #64748b; font-weight: 600; }
.live-loading { opacity: .55; }

[data-theme="dark"] .live-kpi, [data-theme="dark"] .live-filter { background: #1e293b !important; border-color: #334155 !important; color: #f8fafc !important; }
[data-theme="dark"] .live-kpi .val, [data-theme="dark"] .live-person b { color: #f8fafc !important; }
[data-theme="dark"] .live-bar { background: #334155; }
[data-theme="dark"] .live-people tr.is-me td { background: rgba(59,130,246,.15) !important; }

@media (max-width: 991px) { .live-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
@media (max-width: 640px) {
  .live-sheet-wrapper { padding: 14px 12px 40px; }
  .live-kpi .val { font-size: 21px; }
  .live-excel-table th, .live-excel-table td { padding: 9px 8px; font-size: 12.5px; }
  /* People and centers as cards on phones */
  .live-cards thead { display: none; }
  .live-cards, .live-cards tbody, .live-cards tr, .live-cards td { display: block; width: 100%; }
  .live-cards tr { border-bottom: 1px solid #e2e8f0; padding: 8px 4px; }
  .live-cards td { border: 0 !important; padding: 3px 8px !important; text-align: right !important; white-space: normal; }
  .live-cards td::before { content: attr(data-label); float: left; font-weight: 700; color: #64748b; font-size: 12px; }
  .live-cards td.first { text-align: left !important; }
  .live-cards td.first::before { content: none; }
  .live-filter { width: 100%; }
}
</style>

<div class="content-wrapper live-sheet-wrapper" id="liveSheetWrapper">
    <div class="live-sheet-topbar">
        <div>
            <h1 class="live-sheet-title">Live</h1>
            <span class="live-sub"><?php echo htmlspecialchars((string)$me_name); ?> &middot; <span id="liveMonth"><?php echo date('F Y'); ?></span></span>
        </div>
        <div style="display:flex; align-items:center; gap:10px;">
            <span class="live-updated" id="liveUpdated">Loading...</span>
            <a href="<?php echo base_url('manager/live/presentation'); ?>" target="_blank" rel="noopener" class="live-btn-action" style="text-decoration:none; gap:6px;" title="Open the TV presentation in a new tab">
                <i class="fa fa-television"></i> Presentation
            </a>
            <button type="button" class="live-btn-action" onclick="toggleFullScreenSheet()" title="Full screen" aria-label="Full screen">
                <i class="fa fa-arrows-alt"></i>
            </button>
        </div>
    </div>

    <div id="liveBody" class="live-loading">
        <!-- Me -->
        <div class="live-sheet-section-title">My Stats</div>
        <div class="live-kpis" id="myKpis"></div>
        <div class="live-sheet-table-card live-scroll"><table class="live-excel-table" id="tableMyStats"></table></div>

        <?php if ($has_team) { ?>
        <!-- Team (team leaders: people under them; admin: everyone) -->
        <div class="live-sheet-section-title"><span id="teamTitle">Team</span> Stats</div>
        <div class="live-kpis" id="teamKpis"></div>
        <div class="live-sheet-table-card live-scroll"><table class="live-excel-table" id="tableTeamStats"></table></div>

        <div class="live-head-row">
            <div class="live-sheet-section-title"><span id="membersTitle">Team members</span></div>
            <input type="search" class="live-filter" id="memberFilter" placeholder="Search a name..." aria-label="Search a name">
        </div>
        <div class="live-sheet-table-card live-scroll">
            <table class="live-excel-table live-people live-cards">
                <thead><tr><th>Name</th><th class="num">Calls today</th><th class="num">Orders today</th><th class="num">Orders this month</th><th class="num">Sales this month</th><th>Target</th></tr></thead>
                <tbody id="membersBody"></tbody>
            </table>
        </div>
        <?php } ?>

        <?php if ($is_admin) { ?>
        <!-- Centers (admin only) -->
        <div class="live-sheet-section-title">Centers</div>
        <div class="live-sheet-table-card live-scroll">
            <table class="live-excel-table live-people live-cards">
                <thead><tr><th>Center</th><th class="num">Staff</th><th class="num">Calls today</th><th class="num">Orders this month</th><th class="num">Sales this month</th><th>Target</th></tr></thead>
                <tbody id="centersBody"></tbody>
            </table>
        </div>
        <?php } ?>

        <!-- Performers: names only -->
        <div class="live-sheet-section-title">Performers &middot; <span id="perfScope"></span></div>
        <div class="live-sheet-table-card live-scroll">
            <table class="live-excel-table">
                <thead><tr><th style="width:32%;">Award</th><th style="width:34%;">This period</th><th style="width:34%;">Last period</th></tr></thead>
                <tbody id="tbodyPerformers"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleFullScreenSheet() {
    var elem = document.getElementById("liveSheetWrapper");
    if (!document.fullscreenElement) {
        (elem.requestFullscreen || elem.webkitRequestFullscreen || elem.msRequestFullscreen || function () {}).call(elem);
    } else if (document.exitFullscreen) {
        document.exitFullscreen();
    }
}

(function () {
    var URL = '<?php echo base_url('manager/live/ajax_get_data'); ?>';
    var timer = null, members = [];

    function esc(s) { return $('<div>').text(s == null ? '' : String(s)).html(); }
    function num(v) { return (Number(v) || 0).toLocaleString('en-US'); }
    function aed(v) { return 'AED ' + Math.round(Number(v) || 0).toLocaleString('en-US'); }
    function initials(n) { return String(n || '?').replace(/[^A-Za-z ]/g, ' ').trim().split(/\s+/).slice(0, 2).map(function (w) { return w.charAt(0).toUpperCase(); }).join('') || '?'; }
    function avatar(p) { return '<span class="live-av">' + (p.avatar ? '<img src="' + esc(p.avatar) + '" alt="">' : esc(initials(p.name))) + '</span>'; }
    // "▲ 3 vs yesterday" (difference) or "▲ 12% vs last week" (percent)
    function cmp(now, prev, label, pct) {
        now = Number(now) || 0; prev = Number(prev) || 0;
        if (pct) {
            if (prev <= 0) return '<span>' + (now > 0 ? 'Nothing ' + label + ' to compare' : 'No calls ' + label.replace('vs ', '')) + '</span>';
            var g = Math.round((now - prev) * 100 / prev);
            return '<span class="' + (g >= 0 ? 'live-up' : 'live-down') + '"><i class="fa fa-caret-' + (g >= 0 ? 'up' : 'down') + '"></i> ' + Math.abs(g) + '%</span> ' + label;
        }
        var d = now - prev;
        if (d === 0) return '<span>Same as yesterday (' + num(prev) + ')</span>';
        return '<span class="' + (d > 0 ? 'live-up' : 'live-down') + '"><i class="fa fa-caret-' + (d > 0 ? 'up' : 'down') + '"></i> ' + num(Math.abs(d)) + '</span> ' + label + ' (' + num(prev) + ')';
    }
    function target(k) {
        if (k.target_pct === null || k.target_pct === undefined) return '<span class="live-sub">No target</span>';
        return '<div class="live-bar"><i style="width:' + Math.min(100, k.target_pct) + '%"></i></div><small>' + k.target_pct + '% of ' + aed(k.target) + '</small>';
    }
    function kpis(id, k, week) {
        var salesSub = num(k.sales_count) + ' sale' + (k.sales_count === 1 ? '' : 's');
        var tgt = (k.target_pct === null || k.target_pct === undefined) ? '<div class="cmp">' + salesSub + ' &middot; No target set</div>'
            : '<div class="cmp">' + salesSub + ' &middot; ' + k.target_pct + '% of ' + aed(k.target) + '</div><div class="live-bar"><i style="width:' + Math.min(100, k.target_pct) + '%"></i></div>';
        $('#' + id).html(
            '<div class="live-kpi"><span class="lbl">Calls today</span><b class="val">' + num(k.calls_today) + '</b><div class="cmp">' + cmp(k.calls_today, k.calls_yday, 'vs yesterday') + '</div></div>' +
            '<div class="live-kpi"><span class="lbl">Orders today</span><b class="val">' + num(k.orders_today) + '</b><div class="cmp">' + cmp(k.orders_today, k.orders_yday, 'vs yesterday') + '</div></div>' +
            '<div class="live-kpi"><span class="lbl">Sales this month</span><b class="val">' + aed(k.sales_month) + '</b>' + tgt + '</div>' +
            '<div class="live-kpi"><span class="lbl">Calls, last 7 days</span><b class="val">' + num(week.now) + '</b><div class="cmp">' + cmp(week.now, week.prev, 'vs last week', true) + '</div></div>'
        );
    }
    function statsTable(id, rows) {
        var h = '<thead><tr><th style="width:28%;">Activity</th><th class="col-num col-header-today">Today</th><th class="col-num col-header-7d">Last 7 days</th><th class="col-num col-header-month">This month</th><th class="col-num col-header-quarter">This quarter</th></tr></thead><tbody>';
        (rows || []).forEach(function (r) {
            h += '<tr><td class="col-metric">' + esc(r.metric) + '</td><td class="col-val col-val-today">' + esc(r.today) + '</td><td class="col-val col-val-7d">' + esc(r.seven_days) + '</td><td class="col-val col-val-month">' + esc(r.month) + '</td><td class="col-val col-val-quarter">' + esc(r.quarter) + '</td></tr>';
        });
        $('#' + id).html(h + '</tbody>');
    }
    function peopleRows(list, isCenter) {
        var h = '';
        list.forEach(function (p) {
            var first = isCenter ? '<b>' + esc(p.name) + '</b>'
                : '<div class="live-person">' + avatar(p) + '<div><b>' + esc(p.name) + (p.is_me ? ' (you)' : '') + '</b>' + (p.office ? '<small>' + esc(p.office) + '</small>' : '') + '</div></div>';
            h += '<tr' + (p.is_me ? ' class="is-me"' : '') + '><td class="first">' + first + '</td>' +
                (isCenter ? '<td class="num" data-label="Staff">' + num(p.staff) + '</td>' : '') +
                '<td class="num" data-label="Calls today">' + num(p.calls_today) + '</td>' +
                (isCenter ? '' : '<td class="num" data-label="Orders today">' + num(p.orders_today) + '</td>') +
                '<td class="num" data-label="Orders this month">' + num(p.orders_month) + '</td>' +
                '<td class="num" data-label="Sales this month">' + aed(p.sales_month) + '<br><small class="live-sub">' + num(p.sales_count) + ' sale' + (p.sales_count === 1 ? '' : 's') + '</small></td>' +
                '<td class="live-tp" data-label="Target">' + target(p) + '</td></tr>';
        });
        return h;
    }
    function drawMembers() {
        var q = $.trim($('#memberFilter').val() || '').toLowerCase();
        var list = q ? members.filter(function (m) { return String(m.name).toLowerCase().indexOf(q) !== -1; }) : members;
        $('#membersBody').html(peopleRows(list, false) || '<tr><td colspan="6" class="live-empty">' + (q ? 'No one matches.' : 'No team members.') + '</td></tr>');
    }

    function render(d) {
        $('#liveMonth').text(d.month);
        kpis('myKpis', d.me.kpi, d.me.week);
        statsTable('tableMyStats', d.me.stats);
        if (d.team) {
            $('#teamTitle').text(d.team.label);
            $('#membersTitle').text(d.team.label === 'Company' ? 'Everyone' : 'Team members');
            kpis('teamKpis', d.team.kpi, d.team.week);
            statsTable('tableTeamStats', d.team.stats);
            members = d.team.members || [];
            drawMembers();
        }
        if (d.centers) $('#centersBody').html(peopleRows(d.centers, true) || '<tr><td colspan="6" class="live-empty">No centers.</td></tr>');
        $('#perfScope').text(d.performers.label);
        var ph = '';
        (d.performers.rows || []).forEach(function (p) {
            ph += '<tr><td class="col-perf-title">' + esc(p.title) + '</td><td class="col-perf-name">' + esc(p.current) + '</td><td class="col-perf-name">' + esc(p.last) + '</td></tr>';
        });
        $('#tbodyPerformers').html(ph);
    }

    function load() {
        $.ajax({ url: URL, dataType: 'json', cache: false, timeout: 20000 })
            .done(function (d) {
                if (!d || d.status !== 'success') return;
                render(d);
                $('#liveBody').removeClass('live-loading');
                $('#liveUpdated').text('Updated ' + new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }));
            })
            .fail(function () { $('#liveUpdated').text('Could not refresh. Retrying...'); });
    }
    function start() { if (!timer) timer = setInterval(load, 10000); }
    function stop() { clearInterval(timer); timer = null; }
    // Refresh every 10 seconds while the page is open; pause while the tab is hidden.
    document.addEventListener('visibilitychange', function () { if (document.hidden) stop(); else { load(); start(); } });
    $('#memberFilter').on('input', drawMembers);
    $(function () { load(); start(); });
})();
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
