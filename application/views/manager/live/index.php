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
    background: #0072f0;
    border-radius: 2px;
}
.live-sheet-table-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
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
    border-right: 1px solid #f1f5f9;
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
    border-bottom: 1px solid #f1f5f9;
    border-right: 1px solid #f1f5f9;
    color: #1e293b;
    vertical-align: middle;
}
.live-excel-table td:last-child {
    border-right: none;
}
.live-excel-table tr:last-child td {
    border-bottom: none;
}
.live-excel-table tr:nth-child(even) td {
    background: #fcfdfe;
}
.live-excel-table tr:hover td {
    background: #f0f7ff;
}
.live-excel-table td.col-metric {
    font-weight: 600;
    color: #0f172a;
}
.live-excel-table td.col-val {
    text-align: right;
    font-weight: 700;
    color: #0f172a;
    font-variant-numeric: tabular-nums;
    letter-spacing: -0.01em;
}
.live-excel-table td.col-perf-title {
    font-weight: 700;
    color: #0f172a;
    width: 32%;
}
.live-excel-table td.col-perf-name {
    font-weight: 600;
    color: #0072f0;
    width: 34%;
}
</style>

<div class="content-wrapper live-sheet-wrapper" id="liveSheetWrapper">
    <div class="live-sheet-topbar">
        <div style="display:flex; align-items:center;">
            <h1 class="live-sheet-title">Live</h1>
        </div>

        <div style="display:flex; align-items:center; gap:10px;">
            <button type="button" class="live-btn-action" onclick="toggleFullScreenSheet()" title="Toggle Full Screen">
                <i class="fa fa-arrows-alt"></i>
            </button>
        </div>
    </div>

    <div>
        <div class="live-sheet-section-title">My Stats</div>
        <div class="live-sheet-table-card">
            <table class="live-excel-table" id="tableMyStats">
                <thead>
                    <tr>
                        <th style="width:28%;">Activity</th>
                        <th class="col-num">Today</th>
                        <th class="col-num">7 Days</th>
                        <th class="col-num">This Month</th>
                        <th class="col-num">This Quarter</th>
                    </tr>
                </thead>
                <tbody id="tbodyMyStats">
                    <?php if (!empty($my_stats)) { foreach ($my_stats as $idx => $row) { ?>
                        <tr>
                            <td class="col-metric"><?php echo htmlspecialchars($row['metric']); ?></td>
                            <td class="col-val" id="my_val_<?php echo $idx; ?>_today"><?php echo htmlspecialchars((string)$row['today']); ?></td>
                            <td class="col-val" id="my_val_<?php echo $idx; ?>_7d"><?php echo htmlspecialchars((string)$row['seven_days']); ?></td>
                            <td class="col-val" id="my_val_<?php echo $idx; ?>_month"><?php echo htmlspecialchars((string)$row['month']); ?></td>
                            <td class="col-val" id="my_val_<?php echo $idx; ?>_quarter"><?php echo htmlspecialchars((string)$row['quarter']); ?></td>
                        </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>

        <div class="live-sheet-section-title">Team Stats</div>
        <div class="live-sheet-table-card">
            <table class="live-excel-table" id="tableTeamStats">
                <thead>
                    <tr>
                        <th style="width:28%;">Activity</th>
                        <th class="col-num">Today</th>
                        <th class="col-num">7 Days</th>
                        <th class="col-num">This Month</th>
                        <th class="col-num">This Quarter</th>
                    </tr>
                </thead>
                <tbody id="tbodyTeamStats">
                    <?php if (!empty($team_stats)) { foreach ($team_stats as $idx => $row) { ?>
                        <tr>
                            <td class="col-metric"><?php echo htmlspecialchars($row['metric']); ?></td>
                            <td class="col-val" id="team_val_<?php echo $idx; ?>_today"><?php echo htmlspecialchars((string)$row['today']); ?></td>
                            <td class="col-val" id="team_val_<?php echo $idx; ?>_7d"><?php echo htmlspecialchars((string)$row['seven_days']); ?></td>
                            <td class="col-val" id="team_val_<?php echo $idx; ?>_month"><?php echo htmlspecialchars((string)$row['month']); ?></td>
                            <td class="col-val" id="team_val_<?php echo $idx; ?>_quarter"><?php echo htmlspecialchars((string)$row['quarter']); ?></td>
                        </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>

        <div class="live-sheet-section-title">Performers</div>
        <div class="live-sheet-table-card">
            <table class="live-excel-table" id="tablePerformers">
                <thead>
                    <tr>
                        <th style="width:32%;">Award</th>
                        <th style="width:34%;">Current</th>
                        <th style="width:34%;">Last</th>
                    </tr>
                </thead>
                <tbody id="tbodyPerformers">
                    <?php if (!empty($performers)) { foreach ($performers as $idx => $p) { ?>
                        <tr>
                            <td class="col-perf-title"><?php echo htmlspecialchars(!empty($p['title']) ? $p['title'] : (!empty($p['award']) ? $p['award'] : '')); ?></td>
                            <td class="col-perf-name" id="perf_val_<?php echo $idx; ?>_cur"><?php echo htmlspecialchars($p['current']); ?></td>
                            <td class="col-perf-name" id="perf_val_<?php echo $idx; ?>_last"><?php echo htmlspecialchars($p['last']); ?></td>
                        </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleFullScreenSheet() {
    var elem = document.getElementById("liveSheetWrapper");
    if (!document.fullscreenElement) {
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.webkitRequestFullscreen) {
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
        }
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        }
    }
}

function silentRefreshLiveData() {
    $.ajax({
        url: '<?php echo base_url(); ?>manager/live/ajax_get_data',
        type: 'GET',
        dataType: 'json',
        cache: false,
        success: function(res) {
            if (!res || !res.status) return;

            if (res.my_stats && Array.isArray(res.my_stats)) {
                res.my_stats.forEach(function(row, idx) {
                    $('#my_val_' + idx + '_today').text(row.today);
                    $('#my_val_' + idx + '_7d').text(row.seven_days);
                    $('#my_val_' + idx + '_month').text(row.month);
                    $('#my_val_' + idx + '_quarter').text(row.quarter);
                });
            }

            if (res.team_stats && Array.isArray(res.team_stats)) {
                res.team_stats.forEach(function(row, idx) {
                    $('#team_val_' + idx + '_today').text(row.today);
                    $('#team_val_' + idx + '_7d').text(row.seven_days);
                    $('#team_val_' + idx + '_month').text(row.month);
                    $('#team_val_' + idx + '_quarter').text(row.quarter);
                });
            }

            if (res.performers && Array.isArray(res.performers)) {
                res.performers.forEach(function(p, idx) {
                    $('#perf_val_' + idx + '_cur').text(p.current);
                    $('#perf_val_' + idx + '_last').text(p.last);
                });
            }
        }
    });
}

$(document).ready(function() {
    setInterval(silentRefreshLiveData, 10000);
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
