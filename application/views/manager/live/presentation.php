<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<style>
.live-pres-wrapper {
    background: #0f172a;
    min-height: calc(100vh - 60px);
    padding: 24px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: #f8fafc;
}
.live-pres-topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid #1e293b;
}
.live-pres-title {
    font-size: 22px;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.02em;
    margin: 0;
}
.live-pres-card {
    background: #1e293b;
    border: 1px solid #334155;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 30px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.25);
}
.live-pres-card-header {
    background: #0f172a;
    padding: 14px 20px;
    font-size: 16px;
    font-weight: 800;
    color: #38bdf8;
    border-bottom: 1px solid #334155;
    display: flex;
    align-items: center;
    gap: 10px;
}
.live-pres-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
    text-align: left;
}
.live-pres-table th {
    background: #1e293b;
    color: #94a3b8;
    font-weight: 700;
    font-size: 12px;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    padding: 12px 20px;
    border-bottom: 1px solid #334155;
    border-right: 1px solid #334155;
}
.live-pres-table th:last-child {
    border-right: none;
}
.live-pres-table th.col-num {
    text-align: right;
    width: 18%;
}
.live-pres-table td {
    padding: 12px 20px;
    border-bottom: 1px solid #334155;
    border-right: 1px solid #334155;
    color: #e2e8f0;
    vertical-align: middle;
}
.live-pres-table td:last-child {
    border-right: none;
}
.live-pres-table tr:last-child td {
    border-bottom: none;
}
.live-pres-table tr:nth-child(even) td {
    background: #182234;
}
.live-pres-table td.col-metric {
    font-weight: 600;
    color: #ffffff;
}
.live-pres-table td.col-val {
    text-align: right;
    font-weight: 800;
    color: #38bdf8;
    font-variant-numeric: tabular-nums;
}
.live-pres-table td.col-perf-title {
    font-weight: 700;
    color: #ffffff;
    width: 32%;
}
.live-pres-table td.col-perf-name {
    font-weight: 700;
    color: #4ade80;
    width: 34%;
}
.btn-pres-fs {
    padding: 8px 14px;
    border-radius: 8px;
    border: 1px solid #334155;
    background: #1e293b;
    color: #ffffff;
    font-size: 13px;
    cursor: pointer;
}
.btn-pres-fs:hover {
    background: #334155;
}
</style>

<div class="content-wrapper live-pres-wrapper" id="livePresWrapper">
    <div class="live-pres-topbar">
        <div>
            <h1 class="live-pres-title">Live Presentation</h1>
        </div>
        <div>
            <button type="button" class="btn-pres-fs" onclick="toggleFullScreenPres()" title="Toggle Fullscreen">
                <i class="fa fa-arrows-alt"></i>
            </button>
        </div>
    </div>

    <div class="live-pres-card">
        <div class="live-pres-card-header">
            <i class="fa fa-globe"></i> Company Overall
        </div>
        <table class="live-pres-table">
            <thead>
                <tr>
                    <th style="width:28%;">Activity</th>
                    <th class="col-num">Today</th>
                    <th class="col-num">7 Days</th>
                    <th class="col-num">This Month</th>
                    <th class="col-num">This Quarter</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($company_stats)) { foreach ($company_stats as $row) { ?>
                    <tr>
                        <td class="col-metric"><?php echo htmlspecialchars($row['metric']); ?></td>
                        <td class="col-val"><?php echo htmlspecialchars((string)$row['today']); ?></td>
                        <td class="col-val"><?php echo htmlspecialchars((string)$row['seven_days']); ?></td>
                        <td class="col-val"><?php echo htmlspecialchars((string)$row['month']); ?></td>
                        <td class="col-val"><?php echo htmlspecialchars((string)$row['quarter']); ?></td>
                    </tr>
                <?php } } ?>
            </tbody>
        </table>
        <?php if (!empty($company_perf)) { ?>
            <div style="padding:10px 20px; background:#0f172a; border-top:1px solid #334155; border-bottom:1px solid #334155; font-size:12px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Company Performers</div>
            <table class="live-pres-table">
                <thead>
                    <tr>
                        <th style="width:32%;">Award</th>
                        <th style="width:34%;">Current</th>
                        <th style="width:34%;">Last</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($company_perf as $p) { ?>
                        <tr>
                            <td class="col-perf-title"><?php echo htmlspecialchars($p['award']); ?></td>
                            <td class="col-perf-name"><?php echo htmlspecialchars($p['current']); ?></td>
                            <td class="col-perf-name"><?php echo htmlspecialchars($p['last']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>
    </div>

    <?php if (!empty($centers_data)) { foreach ($centers_data as $c) { ?>
        <div class="live-pres-card">
            <div class="live-pres-card-header">
                <i class="fa fa-building-o"></i> Center: <?php echo htmlspecialchars($c['name']); ?>
            </div>
            <table class="live-pres-table">
                <thead>
                    <tr>
                        <th style="width:28%;">Activity</th>
                        <th class="col-num">Today</th>
                        <th class="col-num">7 Days</th>
                        <th class="col-num">This Month</th>
                        <th class="col-num">This Quarter</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($c['stats'])) { foreach ($c['stats'] as $row) { ?>
                        <tr>
                            <td class="col-metric"><?php echo htmlspecialchars($row['metric']); ?></td>
                            <td class="col-val"><?php echo htmlspecialchars((string)$row['today']); ?></td>
                            <td class="col-val"><?php echo htmlspecialchars((string)$row['seven_days']); ?></td>
                            <td class="col-val"><?php echo htmlspecialchars((string)$row['month']); ?></td>
                            <td class="col-val"><?php echo htmlspecialchars((string)$row['quarter']); ?></td>
                        </tr>
                    <?php } } ?>
                </tbody>
            </table>
            <?php if (!empty($c['perf'])) { ?>
                <div style="padding:10px 20px; background:#0f172a; border-top:1px solid #334155; border-bottom:1px solid #334155; font-size:12px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Center Performers</div>
                <table class="live-pres-table">
                    <thead>
                        <tr>
                            <th style="width:32%;">Award</th>
                            <th style="width:34%;">Current</th>
                            <th style="width:34%;">Last</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($c['perf'] as $p) { ?>
                            <tr>
                                <td class="col-perf-title"><?php echo htmlspecialchars($p['award']); ?></td>
                                <td class="col-perf-name"><?php echo htmlspecialchars($p['current']); ?></td>
                                <td class="col-perf-name"><?php echo htmlspecialchars($p['last']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>
    <?php } } ?>
</div>

<script>
function toggleFullScreenPres() {
    var elem = document.getElementById("livePresWrapper");
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
setTimeout(function() {
    window.location.reload();
}, 15000);
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
