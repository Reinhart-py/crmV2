<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
/* Analysis Reports: one report at a time, with a chart, the main totals and a table. Data from analysis_model. */
$e = function ($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };
$curr = !empty($user_currency) ? $user_currency : 'AED';
$base = base_url() . 'manager/reports/analysis/';
$meta = $cat[$slug];
$uses_range = !empty($meta[3]);
$uses_person = !empty($meta[4]) && count($people) > 1;
$q = array_filter(array('d1' => $d1, 'd2' => $d2, 'person' => $person ?: ''));
$qs = $q ? '?' . http_build_query($q) : '';
$fmt = function ($v, $type) use ($e, $curr) {
    if ($v === null || $v === '') return $type === 'pct' ? '<span class="an-mute">-</span>' : '';
    switch ($type) {
        case 'num': return number_format((float)$v, 0);
        case 'dec': return number_format((float)$v, 1);
        case 'amt': return '<span class="an-amt">' . number_format((float)$v, 0) . '</span>';
        case 'pct': $p = (int)$v; $c = $p >= 100 ? 'g' : ($p >= 60 ? 'o' : 'r'); return '<span class="an-pct an-pct-' . $c . '">' . $p . '%</span>';
        case 'date': $t = strtotime((string)$v); return ($t && $v >= '2000-01-01') ? date('d M y', $t) : '';
        default: return $e($v);
    }
};
/* Main totals as tiles: the first four number or amount columns of the total row. */
$kpis = array();
if (!empty($rep['total'])) {
    foreach ($rep['cols'] as $i => $c) {
        if ($i === 0 || !in_array($c[2], array('num', 'amt', 'pct', 'dec'), true)) continue;
        if (!isset($rep['total'][$c[0]]) || $rep['total'][$c[0]] === '' || $rep['total'][$c[0]] === null) continue;
        $kpis[] = $c;
        if (count($kpis) === 4) break;
    }
}
$presets = array(
    'This month' => array(date('Y-m-01'), date('Y-m-d')),
    'Last month' => array(date('Y-m-01', strtotime('first day of last month')), date('Y-m-t', strtotime('first day of last month'))),
    'Last 3 months' => array(date('Y-m-01', strtotime('first day of -2 months')), date('Y-m-d')),
    'This year' => array(date('Y-01-01'), date('Y-m-d')),
    'Last 7 days' => array(date('Y-m-d', strtotime('-6 days')), date('Y-m-d')),
);
?>
<div class="content-wrapper an" id="anRoot">
    <div class="an-bar">
        <h1>Analysis Reports</h1>
        <?php if (count($views) > 1) { ?>
        <nav class="an-views" aria-label="Reports for">
            <?php foreach ($views as $vk) { ?><a href="<?php echo $base . $vk; ?>" class="<?php echo $vk === $view ? 'is-on' : ''; ?>"<?php echo $vk === $view ? ' aria-current="page"' : ''; ?>><?php echo $vk === $view ? '<i class="fa fa-check"></i> ' : ''; ?><?php echo $e($names[$vk]); ?></a><?php } ?>
        </nav>
        <?php } ?>
        <span class="an-scope"><i class="fa fa-eye"></i> <?php
            if ($view === 'b') echo 'Every order';
            elseif ($view === 'a') echo 'Whole company';
            elseif ($view === 'k') echo $is_all ? 'Every client' : 'Your clients';
            else echo $is_all ? 'Everyone' : (count($people) > 1 ? 'You and your team' : 'Only you');
        ?></span>
    </div>

    <div class="an-wrap">
        <aside class="an-list">
            <label class="an-pick">Report
                <select onchange="location.href=this.value">
                    <?php foreach ($cat as $k => $r) { ?><option value="<?php echo $base . $view . '/' . $k . $qs; ?>"<?php echo $k === $slug ? ' selected' : ''; ?>><?php echo $e($r[0]); ?></option><?php } ?>
                </select>
            </label>
            <?php foreach ($cat as $k => $r) { ?>
            <a href="<?php echo $base . $view . '/' . $k . $qs; ?>" class="an-item<?php echo $k === $slug ? ' is-on' : ''; ?>"<?php echo $k === $slug ? ' aria-current="page"' : ''; ?>>
                <i class="fa <?php echo $r[1]; ?>"></i><span><b><?php echo $e($r[0]); ?></b><small><?php echo $e($r[2]); ?></small></span>
            </a>
            <?php } ?>
        </aside>

        <main class="an-main">
            <div class="an-head">
                <div>
                    <h2><i class="fa <?php echo $meta[1]; ?>"></i> <?php echo $e($meta[0]); ?></h2>
                    <p><?php echo $e($meta[2]); ?></p>
                </div>
                <div class="an-act">
                    <button type="button" class="an-btn" id="anHide" aria-pressed="false"><i class="fa fa-eye-slash"></i> <span>Hide amounts</span></button>
                    <button type="button" class="an-btn" onclick="window.print()"><i class="fa fa-print"></i> Print</button>
                    <?php if ($can_export) { ?><a class="an-btn" href="<?php echo $base . $view . '/' . $slug . '?' . http_build_query($q + array('export' => 'csv')); ?>"><i class="fa fa-download"></i> CSV</a><?php } ?>
                </div>
            </div>

            <?php if ($uses_range || $uses_person) { ?>
            <form class="an-filter" method="get" action="<?php echo $base . $view . '/' . $slug; ?>">
                <?php if ($uses_range) { ?>
                <label>From<input type="date" name="d1" value="<?php echo $e($d1); ?>" class="form-control"></label>
                <label>To<input type="date" name="d2" value="<?php echo $e($d2); ?>" class="form-control"></label>
                <?php } else { ?><input type="hidden" name="d1" value="<?php echo $e($d1); ?>"><label>Up to month ending<input type="date" name="d2" value="<?php echo $e($d2); ?>" class="form-control"></label><?php } ?>
                <?php if ($uses_person) { ?>
                <label>Person
                    <select name="person" class="form-control">
                        <option value="">Everyone (<?php echo count($people); ?>)</option>
                        <?php foreach ($people as $pid => $p) { ?><option value="<?php echo (int)$pid; ?>"<?php echo $pid === $person ? ' selected' : ''; ?>><?php echo $e($p['name']); ?></option><?php } ?>
                    </select>
                </label>
                <?php } ?>
                <button type="submit" class="an-btn an-go"><i class="fa fa-search"></i> Show</button>
                <?php if ($uses_range) { ?>
                <span class="an-presets">
                    <?php foreach ($presets as $lab => $pr) { $on = ($pr[0] === $d1 && $pr[1] === $d2); ?><a href="<?php echo $base . $view . '/' . $slug . '?' . http_build_query(array_filter(array('d1' => $pr[0], 'd2' => $pr[1], 'person' => $person ?: ''))); ?>" class="<?php echo $on ? 'is-on' : ''; ?>"><?php echo $lab; ?></a><?php } ?>
                </span>
                <?php } ?>
            </form>
            <?php } ?>
            <p class="an-period"><?php echo $uses_range ? 'Period: ' . date('d M Y', strtotime($d1)) . ' to ' . date('d M Y', strtotime($d2)) : 'As of ' . date('d M Y'); ?><?php echo $person && isset($people[$person]) ? ' &middot; ' . $e($people[$person]['name']) : ''; ?> &middot; Amounts in <?php echo $e($curr); ?></p>

            <?php if (!$rep) { ?>
            <div class="an-card an-empty">This report could not be opened.</div>
            <?php } else { ?>
                <?php if ($kpis) { ?>
                <div class="an-kpis">
                    <?php foreach ($kpis as $c) { ?><div class="an-kpi"><span><?php echo $e($c[1]); ?></span><b><?php echo $fmt($rep['total'][$c[0]], $c[2]); ?></b></div><?php } ?>
                </div>
                <?php } ?>

                <?php if (!empty($rep['chart']) && !empty($rep['chart']['labels'])) { $ch = $rep['chart']; ?>
                <section class="an-card">
                    <div class="an-leg"><?php foreach ($ch['sets'] as $st) { ?><span><i style="background:<?php echo $st['color']; ?>"></i><?php echo $e($st['label']); ?></span><?php } ?></div>
                    <div class="an-chart<?php echo !empty($ch['amt']) ? ' is-amt' : ''; ?>"><canvas id="anChart"></canvas></div>
                </section>
                <?php } ?>

                <section class="an-card an-tcard">
                    <?php if (empty($rep['rows'])) { ?>
                    <div class="an-empty">No data for this <?php echo $uses_range ? 'period' : 'report yet'; ?>.</div>
                    <?php } else { ?>
                    <div class="an-tools"><input type="search" id="anFind" placeholder="Search in the table" aria-label="Search in the table"><span id="anCount"><?php echo count($rep['rows']); ?> rows</span></div>
                    <div class="an-scroll">
                    <table class="an-tbl" id="anTbl">
                        <thead><tr><?php foreach ($rep['cols'] as $i => $c) { ?><th class="<?php echo $c[2] === 'text' || $c[2] === 'date' ? '' : 'n'; ?>" data-type="<?php echo $c[2]; ?>" title="Sort"><?php echo $e($c[1]); ?><?php echo $c[2] === 'amt' ? ' <small>(' . $e($curr) . ')</small>' : ''; ?></th><?php } ?></tr></thead>
                        <tbody>
                        <?php foreach ($rep['rows'] as $r) { ?>
                            <tr><?php foreach ($rep['cols'] as $i => $c) { $v = isset($r[$c[0]]) ? $r[$c[0]] : ''; $cell = $fmt($v, $c[2]); if ($i === 0 && !empty($r['_href'])) $cell = '<a href="' . base_url() . $e($r['_href']) . '">' . $cell . '</a>'; ?><td class="<?php echo $c[2] === 'text' || $c[2] === 'date' ? '' : 'n'; ?>" data-v="<?php echo $e(is_numeric($v) ? $v : ($c[2] === 'pct' && $v === null ? -1 : $v)); ?>"><?php echo $cell; ?></td><?php } ?></tr>
                        <?php } ?>
                        </tbody>
                        <?php if (!empty($rep['total'])) { ?>
                        <tfoot><tr><?php foreach ($rep['cols'] as $i => $c) { ?><td class="<?php echo $c[2] === 'text' || $c[2] === 'date' ? '' : 'n'; ?>"><?php echo $fmt(isset($rep['total'][$c[0]]) ? $rep['total'][$c[0]] : '', $c[2]); ?></td><?php } ?></tr></tfoot>
                        <?php } ?>
                    </table>
                    </div>
                    <?php } ?>
                    <?php if (!empty($rep['note'])) { ?><p class="an-note"><i class="fa fa-info-circle"></i> <?php echo $e($rep['note']); ?></p><?php } ?>
                </section>
            <?php } ?>
        </main>
    </div>
</div>

<script>
var anChart = <?php echo json_encode(!empty($rep['chart']) ? $rep['chart'] : null); ?>, anCurr = <?php echo json_encode($curr); ?>;
$(function () {
    var $r = $('#anRoot');
    function store(k, v) { try { if (v === undefined) return localStorage.getItem(k); localStorage.setItem(k, v); } catch (e) { return null; } }
    function setHide(on) {
        $r.toggleClass('an-hidden', on);
        $('#anHide').attr('aria-pressed', on ? 'true' : 'false').find('span').text(on ? 'Show amounts' : 'Hide amounts');
        $('#anHide i').attr('class', 'fa ' + (on ? 'fa-eye' : 'fa-eye-slash'));
        store('crm_c2_hide_amt', on ? '1' : '0');
    }
    $('#anHide').on('click', function () { setHide(!$r.hasClass('an-hidden')); });
    setHide(store('crm_c2_hide_amt') === '1');

    // Search and sort the table.
    $('#anFind').on('input', function () {
        var q = this.value.toLowerCase(), n = 0;
        $('#anTbl tbody tr').each(function () { var show = !q || $(this).text().toLowerCase().indexOf(q) >= 0; this.style.display = show ? '' : 'none'; if (show) n++; });
        $('#anCount').text(n + ' rows');
    });
    $('#anTbl thead th').on('click', function () {
        var i = $(this).index(), type = $(this).data('type'), asc = !$(this).hasClass('is-asc');
        $(this).siblings().removeClass('is-asc is-desc'); $(this).toggleClass('is-asc', asc).toggleClass('is-desc', !asc);
        var rows = $('#anTbl tbody tr').get();
        rows.sort(function (a, b) {
            var x = $(a).children().eq(i).attr('data-v'), y = $(b).children().eq(i).attr('data-v');
            if (type !== 'text' && type !== 'date') { x = parseFloat(x); y = parseFloat(y); x = isNaN(x) ? -1e15 : x; y = isNaN(y) ? -1e15 : y; return asc ? x - y : y - x; }
            return asc ? String(x).localeCompare(String(y)) : String(y).localeCompare(String(x));
        });
        $('#anTbl tbody').append(rows);
    });

    // Chart (Chart.js 1.x), redrawn when light / dark mode changes.
    var drawn = null;
    function dark() { return document.documentElement.getAttribute('data-theme') === 'dark' || document.body.getAttribute('data-theme') === 'dark'; }
    function rgba(hex, a) { var n = parseInt(hex.slice(1), 16); return 'rgba(' + (n >> 16) + ',' + ((n >> 8) & 255) + ',' + (n & 255) + ',' + a + ')'; }
    window.anShort = function (v) { v = Number(v); var a = Math.abs(v); return a >= 1000000 ? (v / 1000000).toFixed(1).replace('.0', '') + 'M' : (a >= 1000 ? Math.round(v / 1000) + 'k' : v.toLocaleString()); };
    function draw() {
        var el = document.getElementById('anChart');
        if (!el || !anChart || typeof Chart === 'undefined') return;
        if (drawn) drawn.destroy();
        var dk = dark(), line = anChart.type === 'line';
        var labels = $.map(anChart.labels, function (l) { l = String(l); return l.length > 16 ? l.slice(0, 15) + '…' : l; });
        var data = { labels: labels, datasets: $.map(anChart.sets, function (s) {
            return line ? { label: s.label, fillColor: rgba(s.color, .1), strokeColor: s.color, pointColor: s.color, pointStrokeColor: dk ? '#111827' : '#fff', data: s.data }
                        : { label: s.label, fillColor: rgba(s.color, .82), strokeColor: s.color, highlightFill: s.color, highlightStroke: s.color, data: s.data };
        }) };
        var o = { responsive: true, maintainAspectRatio: false, animation: false, scaleBeginAtZero: true,
            scaleFontColor: dk ? '#cbd5e1' : '#334155', scaleFontSize: 11, scaleFontStyle: '600', scaleGridLineColor: dk ? 'rgba(255,255,255,.07)' : 'rgba(0,0,0,.05)',
            scaleShowVerticalLines: false, barValueSpacing: 6, barDatasetSpacing: 1, barStrokeWidth: 1, bezierCurveTension: .3, pointDotRadius: 3, datasetStrokeWidth: 2 };
        if (anChart.amt) {
            o.scaleLabel = "<%= window.anShort(value) %>";
            o.tooltipTemplate = "<%if (label){%><%=label%>: <%}%>" + anCurr + " <%= Number(value).toLocaleString() %>";
            o.multiTooltipTemplate = "<%= datasetLabel %>: " + anCurr + " <%= Number(value).toLocaleString() %>";
        } else {
            o.multiTooltipTemplate = "<%= datasetLabel %>: <%= value %>";
        }
        drawn = line ? new Chart(el.getContext('2d')).Line(data, o) : new Chart(el.getContext('2d')).Bar(data, o);
    }
    draw();
    if (window.MutationObserver) {
        var mo = new MutationObserver(draw);
        mo.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
        mo.observe(document.body, { attributes: true, attributeFilter: ['data-theme'] });
    }
});
</script>
<style>
.an { --an-bg: #f4f6fa; --an-card: #fff; --an-line: #e2e8f0; --an-soft: #eef2f7; --an-ink: #0f172a; --an-ink2: #334155; --an-mute: #64748b; --an-link: #1d4ed8; padding: 10px 14px 20px !important; background: var(--an-bg); line-height: 1.35; }
[data-theme="dark"] .an { --an-bg: #0b1220; --an-card: #111827; --an-line: #1f2937; --an-soft: #1f2937; --an-ink: #f1f5f9; --an-ink2: #cbd5e1; --an-mute: #94a3b8; --an-link: #93c5fd; }
.an .an-bar { display: flex; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 10px; padding: 7px 10px; background: var(--an-card); border: 1px solid var(--an-line); border-radius: 8px; }
.an .an-bar h1 { margin: 0; font-size: 17px; font-weight: 800; color: var(--an-ink); }
.an .an-views { display: inline-flex; gap: 2px; padding: 2px; border: 1px solid var(--an-line); border-radius: 7px; background: var(--an-bg); }
.an .an-views a { padding: 3px 12px; border-radius: 5px; font-size: 12.5px; font-weight: 700; color: var(--an-ink2); text-decoration: none; white-space: nowrap; }
.an .an-views a:hover { background: var(--an-soft); }
.an .an-views a.is-on { background: #0f766e; color: #fff; box-shadow: 0 1px 4px rgba(15, 118, 110, .45); }
.an .an-views a.is-on i { font-size: 10px; }
[data-theme="dark"] .an .an-views a.is-on { background: #14b8a6; color: #04201d; }
.an .an-scope { margin-left: auto; font-size: 12px; font-weight: 600; color: var(--an-mute); }

.an .an-wrap { display: grid; grid-template-columns: 270px minmax(0, 1fr); gap: 12px; align-items: start; }
.an .an-list { position: sticky; top: 70px; display: flex; flex-direction: column; gap: 4px; max-height: calc(100vh - 90px); overflow: auto; padding: 6px; background: var(--an-card); border: 1px solid var(--an-line); border-radius: 8px; }
.an .an-pick { display: none; }
.an .an-item { display: flex; gap: 9px; padding: 7px 8px; border-radius: 6px; color: var(--an-ink2); text-decoration: none !important; border-left: 3px solid transparent; }
.an .an-item:hover { background: var(--an-soft); }
.an .an-item > i { flex: none; width: 18px; margin-top: 2px; text-align: center; color: #0f766e; }
.an .an-item b { display: block; font-size: 12.5px; color: var(--an-ink); }
.an .an-item small { display: block; font-size: 11px; color: var(--an-mute); line-height: 1.3; }
.an .an-item.is-on { background: rgba(15, 118, 110, .1); border-left-color: #0f766e; }
[data-theme="dark"] .an .an-item.is-on { background: rgba(20, 184, 166, .14); border-left-color: #14b8a6; }

.an .an-main { min-width: 0; }
.an .an-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; flex-wrap: wrap; }
.an .an-head h2 { margin: 0; font-size: 18px; font-weight: 800; color: var(--an-ink); }
.an .an-head h2 i { color: #0f766e; }
.an .an-head p { margin: 2px 0 0; font-size: 12.5px; color: var(--an-mute); }
.an .an-act { display: flex; gap: 6px; flex-wrap: wrap; }
.an .an-btn { display: inline-flex; align-items: center; gap: 5px; height: 30px; padding: 0 11px; border: 1px solid var(--an-line); border-radius: 6px; background: var(--an-card); color: var(--an-ink2) !important; font-size: 12px; font-weight: 700; cursor: pointer; text-decoration: none !important; white-space: nowrap; }
.an .an-btn:hover { background: var(--an-soft); }
.an .an-go { background: #0f766e; border-color: #0f766e; color: #fff !important; }
.an .an-go:hover { background: #115e59; }

.an .an-filter { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 8px; margin: 10px 0 4px; padding: 8px 10px; background: var(--an-card); border: 1px solid var(--an-line); border-radius: 8px; }
.an .an-filter label { display: flex; flex-direction: column; gap: 2px; margin: 0; font-size: 11px; font-weight: 700; color: var(--an-mute); }
.an .an-filter .form-control { height: 30px; padding: 3px 8px; font-size: 12.5px; min-width: 140px; background: var(--an-card); color: var(--an-ink); border-color: var(--an-line); }
.an .an-presets { display: inline-flex; flex-wrap: wrap; gap: 4px; margin-left: auto; }
.an .an-presets a { padding: 3px 9px; border: 1px solid var(--an-line); border-radius: 999px; font-size: 11.5px; font-weight: 700; color: var(--an-ink2); text-decoration: none; }
.an .an-presets a:hover { background: var(--an-soft); }
.an .an-presets a.is-on { background: #0f766e; border-color: #0f766e; color: #fff; }
.an .an-period { margin: 6px 0 8px; font-size: 12px; font-weight: 600; color: var(--an-mute); }

.an .an-kpis { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px; margin-bottom: 10px; }
.an .an-kpi { padding: 8px 12px; border-radius: 8px; color: #fff; background: linear-gradient(135deg, #2f4f9e, #243c80); }
.an .an-kpi:nth-child(even) { background: linear-gradient(135deg, #1c8c83, #146e68); }
.an .an-kpi span { display: block; font-size: 11.5px; font-weight: 700; opacity: .92; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.an .an-kpi b { font-size: 20px; font-weight: 800; }
.an .an-kpi .an-pct { background: rgba(255, 255, 255, .2) !important; color: #fff !important; font-size: 18px; padding: 0 6px; }

.an .an-card { margin-bottom: 10px; padding: 10px 12px; background: var(--an-card); border: 1px solid var(--an-line); border-radius: 8px; }
.an .an-leg { display: flex; flex-wrap: wrap; justify-content: flex-end; gap: 4px 12px; font-size: 11.5px; font-weight: 700; color: var(--an-ink2); }
.an .an-leg span { display: inline-flex; align-items: center; gap: 5px; }
.an .an-leg i { width: 9px; height: 9px; border-radius: 50%; }
.an .an-chart { position: relative; height: 270px; margin-top: 4px; }
.an .an-empty { padding: 40px 10px; text-align: center; font-size: 13px; font-weight: 600; color: var(--an-mute); }

.an .an-tools { display: flex; align-items: center; gap: 10px; margin-bottom: 6px; }
.an .an-tools input { height: 28px; width: 240px; max-width: 100%; padding: 2px 9px; border: 1px solid var(--an-line); border-radius: 6px; background: var(--an-card); color: var(--an-ink); font-size: 12.5px; }
.an .an-tools span { font-size: 11.5px; font-weight: 600; color: var(--an-mute); }
.an .an-scroll { overflow: auto; max-height: 620px; }
.an .an-tbl { width: 100%; border-collapse: collapse; font-size: 12.5px; }
.an .an-tbl th { position: sticky; top: 0; z-index: 1; padding: 6px 8px; background: var(--an-soft); color: var(--an-ink); font-size: 11.5px; font-weight: 800; text-align: left; white-space: nowrap; cursor: pointer; user-select: none; }
.an .an-tbl th small { font-weight: 600; color: var(--an-mute); }
.an .an-tbl th.is-asc::after { content: " \25B2"; font-size: 8px; } .an .an-tbl th.is-desc::after { content: " \25BC"; font-size: 8px; }
.an .an-tbl td { padding: 5px 8px; border-top: 1px solid var(--an-soft); color: var(--an-ink2); font-weight: 600; }
.an .an-tbl .n { text-align: right; white-space: nowrap; }
.an .an-tbl tbody tr:hover td { background: var(--an-bg); }
.an .an-tbl tfoot td { position: sticky; bottom: 0; background: var(--an-soft); color: var(--an-ink); font-weight: 800; border-top: 2px solid var(--an-line); }
.an .an-tbl a { color: var(--an-link); font-weight: 700; }
.an .an-pct { display: inline-block; min-width: 40px; padding: 0 6px; border-radius: 999px; font-size: 11.5px; font-weight: 800; text-align: center; }
.an .an-pct-g { background: #dcfce7; color: #15803d; } .an .an-pct-o { background: #fef3c7; color: #b45309; } .an .an-pct-r { background: #fee2e2; color: #b91c1c; }
[data-theme="dark"] .an .an-pct-g { background: rgba(34, 197, 94, .16); color: #86efac; } [data-theme="dark"] .an .an-pct-o { background: rgba(245, 158, 11, .16); color: #fcd34d; } [data-theme="dark"] .an .an-pct-r { background: rgba(239, 68, 68, .16); color: #fca5a5; }
.an .an-mute { color: var(--an-mute); }
.an .an-note { margin: 8px 0 0; font-size: 11.5px; color: var(--an-mute); }
.an.an-hidden .an-amt { filter: blur(6px); user-select: none; }
.an.an-hidden .an-chart.is-amt canvas { filter: blur(6px); }

@media (max-width: 991px) {
    .an .an-wrap { grid-template-columns: minmax(0, 1fr); }
    .an .an-list { position: static; max-height: none; padding: 8px; }
    .an .an-item { display: none; }
    .an .an-pick { display: flex; flex-direction: column; gap: 3px; margin: 0; font-size: 11px; font-weight: 700; color: var(--an-mute); }
    .an .an-pick select { height: 34px; border: 1px solid var(--an-line); border-radius: 6px; background: var(--an-card); color: var(--an-ink); font-size: 13px; font-weight: 700; padding: 0 8px; }
    .an .an-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .an .an-presets { margin-left: 0; }
}
@media (max-width: 600px) {
    .an { padding: 8px 8px 16px !important; }
    .an .an-scope { margin-left: 0; width: 100%; }
    .an .an-views { width: 100%; } .an .an-views a { flex: 1; text-align: center; padding: 3px 4px; }
    .an .an-filter label, .an .an-filter .form-control { width: 100%; }
    .an .an-chart { height: 220px; }
}
@media print {
    .an .an-list, .an .an-act, .an .an-filter, .an .an-views, .an .an-tools, .main-header, .main-sidebar { display: none !important; }
    .an .an-wrap { grid-template-columns: 1fr; }
    .an .an-scroll { max-height: none; overflow: visible; }
}
</style>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
