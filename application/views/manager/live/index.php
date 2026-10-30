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
.live-sheet-title-group {
    display: flex;
    align-items: center;
    gap: 12px;
}
.live-pulse-dot {
    width: 10px;
    height: 10px;
    background-color: #0072f0;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 0 0 rgba(0, 114, 240, 0.7);
    animation: livePulseRing 2s infinite;
}
@keyframes livePulseRing {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 114, 240, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(0, 114, 240, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 114, 240, 0); }
}
.live-sheet-title {
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.02em;
    margin: 0;
}
.live-sheet-sync-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    color: #475569;
    margin-left: 10px;
}
.live-segmented-view {
    display: inline-flex;
    align-items: center;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 3px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.02);
}
.live-seg-btn {
    padding: 6px 16px;
    border-radius: 6px;
    border: none;
    background: transparent;
    color: #475569;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.live-seg-btn:hover {
    color: #0f172a;
}
.live-seg-btn.active {
    background: #0072f0;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(0, 114, 240, 0.25);
}
.live-btn-action {
    padding: 7px 14px;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #0f172a;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
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
.live-loading-state {
    text-align: center;
    padding: 30px;
    color: #64748b;
    font-size: 13px;
    font-weight: 600;
}
</style>

<div class="content-wrapper live-sheet-wrapper">
    <div class="live-sheet-topbar">
        <div class="live-sheet-title-group">
            <span class="live-pulse-dot"></span>
            <h1 class="live-sheet-title">LIVE OPERATIONS</h1>
            <div class="live-sheet-sync-pill">
                <span>Refresh Rate: 10s</span>
                <span>&bull;</span>
                <span>Synced: <strong id="liveSyncClock" style="color:#0f172a;">--:--:--</strong></span>
            </div>
        </div>

        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
            <div class="live-segmented-view" id="viewModeGroup">
                <button type="button" class="live-seg-btn active" id="btnShowRegular" onclick="switchViewMode('regular')">
                    <i class="fa fa-user"></i> My &amp; Team Stats
                </button>
                <button type="button" class="live-seg-btn" id="btnShowPresentation" onclick="switchViewMode('presentation')">
                    <i class="fa fa-television"></i> Presentation (TV Mode)
                </button>
            </div>

            <button type="button" class="live-btn-action" onclick="fetchLiveSheetData(true)" title="Force Refresh">
                <i class="fa fa-refresh" id="liveRefreshIcon" style="color:#0072f0;"></i> Refresh
            </button>

            <button type="button" class="live-btn-action" onclick="toggleLiveFullscreen()" title="Fullscreen TV Display">
                <i class="fa fa-arrows-alt" id="liveFsIcon"></i> Fullscreen
            </button>
        </div>
    </div>

    <div id="containerRegularUsers">
        <div class="live-sheet-section-title">My Stats</div>
        <div class="live-sheet-table-card">
            <table class="live-excel-table">
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
                    <tr><td colspan="5" class="live-loading-state">Loading My Stats...</td></tr>
                </tbody>
            </table>
        </div>

        <div class="live-sheet-section-title">Team Stats</div>
        <div class="live-sheet-table-card">
            <table class="live-excel-table">
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
                    <tr><td colspan="5" class="live-loading-state">Loading Team Stats...</td></tr>
                </tbody>
            </table>
        </div>

        <div class="live-sheet-section-title">Performers</div>
        <div class="live-sheet-table-card">
            <table class="live-excel-table">
                <thead>
                    <tr>
                        <th style="width:32%;">Award Category</th>
                        <th style="width:34%;">Current</th>
                        <th style="width:34%;">Last</th>
                    </tr>
                </thead>
                <tbody id="tbodyPerformersTeam">
                    <tr><td colspan="3" class="live-loading-state">Loading Performers...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="containerPresentationUser" style="display:none;">
        <div class="live-sheet-section-title">Company Overview</div>
        <div class="live-sheet-table-card">
            <table class="live-excel-table">
                <thead>
                    <tr>
                        <th style="width:28%;">Activity</th>
                        <th class="col-num">Today</th>
                        <th class="col-num">7 Days</th>
                        <th class="col-num">This Month</th>
                        <th class="col-num">This Quarter</th>
                    </tr>
                </thead>
                <tbody id="tbodyCompanyStats">
                    <tr><td colspan="5" class="live-loading-state">Loading Company Stats...</td></tr>
                </tbody>
            </table>
        </div>

        <div class="live-sheet-section-title">Company Performers</div>
        <div class="live-sheet-table-card">
            <table class="live-excel-table">
                <thead>
                    <tr>
                        <th style="width:32%;">Award Category</th>
                        <th style="width:34%;">Current</th>
                        <th style="width:34%;">Last</th>
                    </tr>
                </thead>
                <tbody id="tbodyCompanyPerformers">
                    <tr><td colspan="3" class="live-loading-state">Loading Company Performers...</td></tr>
                </tbody>
            </table>
        </div>

        <div id="dynamicCentersArea"></div>
    </div>
</div>

<script>
var liveIntervalTimer = null;
var currentLiveViewMode = 'regular';

function switchViewMode(mode) {
    currentLiveViewMode = mode;
    if (mode === 'regular') {
        $('#btnShowRegular').addClass('active');
        $('#btnShowPresentation').removeClass('active');
        $('#containerRegularUsers').show();
        $('#containerPresentationUser').hide();
    } else {
        $('#btnShowRegular').removeClass('active');
        $('#btnShowPresentation').addClass('active');
        $('#containerRegularUsers').hide();
        $('#containerPresentationUser').show();
    }
}

function renderStatsRows(items) {
    if (!items || !items.length) {
        return '<tr><td colspan="5" class="live-loading-state">No Data Found</td></tr>';
    }
    var html = '';
    for (var i = 0; i < items.length; i++) {
        var row = items[i];
        html += '<tr>';
        html += '<td class="col-metric">' + escapeLiveHtml(row.metric) + '</td>';
        html += '<td class="col-val">' + escapeLiveHtml(row.today) + '</td>';
        html += '<td class="col-val">' + escapeLiveHtml(row.seven_days) + '</td>';
        html += '<td class="col-val">' + escapeLiveHtml(row.month) + '</td>';
        html += '<td class="col-val">' + escapeLiveHtml(row.quarter) + '</td>';
        html += '</tr>';
    }
    return html;
}

function renderPerformersRows(items) {
    if (!items || !items.length) {
        return '<tr><td colspan="3" class="live-loading-state">No Performers Recorded</td></tr>';
    }
    var html = '';
    for (var i = 0; i < items.length; i++) {
        var p = items[i];
        html += '<tr>';
        html += '<td class="col-perf-title">' + escapeLiveHtml(p.title) + '</td>';
        html += '<td class="col-perf-name">' + escapeLiveHtml(p.current) + '</td>';
        html += '<td class="col-perf-name" style="color:#475569;">' + escapeLiveHtml(p.last) + '</td>';
        html += '</tr>';
    }
    return html;
}

function escapeLiveHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function fetchLiveSheetData(isManual) {
    if (isManual) {
        $('#liveRefreshIcon').addClass('fa-spin');
    }
    $.ajax({
        url: '<?php echo base_url(); ?>manager/live/ajax_get_data',
        type: 'GET',
        dataType: 'json',
        cache: false,
        success: function(res) {
            $('#liveRefreshIcon').removeClass('fa-spin');
            if (!res || res.status !== 'success') return;

            if (res.timestamp) {
                $('#liveSyncClock').text(res.timestamp);
            }

            $('#tbodyMyStats').html(renderStatsRows(res.my_stats));
            $('#tbodyTeamStats').html(renderStatsRows(res.team_stats));
            $('#tbodyPerformersTeam').html(renderPerformersRows(res.performers_team));

            $('#tbodyCompanyStats').html(renderStatsRows(res.company_stats));
            $('#tbodyCompanyPerformers').html(renderPerformersRows(res.performers_company));

            var centersHtml = '';
            if (res.centers && res.centers.length > 0) {
                for (var c = 0; c < res.centers.length; c++) {
                    var center = res.centers[c];
                    centersHtml += '<div class="live-sheet-section-title">' + escapeLiveHtml(center.center_code) + ' &bull; ' + escapeLiveHtml(center.center_name) + ' (' + center.user_count + ' Staff)</div>';
                    centersHtml += '<div class="live-sheet-table-card">';
                    centersHtml += '<table class="live-excel-table">';
                    centersHtml += '<thead><tr><th style="width:28%;">Activity</th><th class="col-num">Today</th><th class="col-num">7 Days</th><th class="col-num">This Month</th><th class="col-num">This Quarter</th></tr></thead>';
                    centersHtml += '<tbody>' + renderStatsRows(center.stats) + '</tbody>';
                    centersHtml += '</table>';
                    centersHtml += '</div>';

                    centersHtml += '<div class="live-sheet-section-title" style="margin-top:14px;">' + escapeLiveHtml(center.center_code) + ' Performers</div>';
                    centersHtml += '<div class="live-sheet-table-card">';
                    centersHtml += '<table class="live-excel-table">';
                    centersHtml += '<thead><tr><th style="width:32%;">Award Category</th><th style="width:34%;">Current</th><th style="width:34%;">Last</th></tr></thead>';
                    centersHtml += '<tbody>' + renderPerformersRows(center.performers) + '</tbody>';
                    centersHtml += '</table>';
                    centersHtml += '</div>';
                }
            } else if (res.center_wise_only) {
                var co = res.center_wise_only;
                centersHtml += '<div class="live-sheet-section-title">Center Wise Only: ' + escapeLiveHtml(co.center_name) + '</div>';
                centersHtml += '<div class="live-sheet-table-card">';
                centersHtml += '<table class="live-excel-table">';
                centersHtml += '<thead><tr><th style="width:28%;">Activity</th><th class="col-num">Today</th><th class="col-num">7 Days</th><th class="col-num">This Month</th><th class="col-num">This Quarter</th></tr></thead>';
                centersHtml += '<tbody>' + renderStatsRows(co.stats) + '</tbody>';
                centersHtml += '</table>';
                centersHtml += '</div>';

                centersHtml += '<div class="live-sheet-section-title" style="margin-top:14px;">Performers</div>';
                centersHtml += '<div class="live-sheet-table-card">';
                centersHtml += '<table class="live-excel-table">';
                centersHtml += '<thead><tr><th style="width:32%;">Award Category</th><th style="width:34%;">Current</th><th style="width:34%;">Last</th></tr></thead>';
                centersHtml += '<tbody>' + renderPerformersRows(co.performers) + '</tbody>';
                centersHtml += '</table>';
                centersHtml += '</div>';
            }
            $('#dynamicCentersArea').html(centersHtml);
        },
        error: function() {
            $('#liveRefreshIcon').removeClass('fa-spin');
        }
    });
}

function toggleLiveFullscreen() {
    var elem = document.documentElement;
    if (!document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
        } else if (elem.mozRequestFullScreen) {
            elem.mozRequestFullScreen();
        } else if (elem.webkitRequestFullscreen) {
            elem.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
        }
        $('#liveFsIcon').removeClass('fa-arrows-alt').addClass('fa-compress');
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        }
        $('#liveFsIcon').removeClass('fa-compress').addClass('fa-arrows-alt');
    }
}

$(document).ready(function() {
    fetchLiveSheetData(false);
    if (liveIntervalTimer) {
        clearInterval(liveIntervalTimer);
    }
    liveIntervalTimer = setInterval(function() {
        fetchLiveSheetData(false);
    }, 10000);
});
</script>
