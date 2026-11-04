<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<?php
  $kpis = !empty($tv_kpis) ? $tv_kpis : array(
    'total_sales' => 742432,
    'sales_growth' => 12.4,
    'orders' => 1247,
    'orders_growth' => 8.7,
    'aov' => 595,
    'aov_growth' => 3.1,
    'customers' => 312,
    'customers_growth' => 15.2,
    'time_labels' => array('Jun 18', 'Jun 19', 'Jun 20', 'Jun 21', 'Jun 22', 'Jun 23', 'Jun 24'),
    'time_values' => array(30, 42, 38, 55, 45, 60, 78),
    'top_products' => array(
      array('prod_name' => 'Accounting & VAT', 'total_val' => 98432),
      array('prod_name' => 'Telecom (Du/Etisalat)', 'total_val' => 72680),
      array('prod_name' => 'IT Services & Cloud', 'total_val' => 56214),
      array('prod_name' => 'Corporate Services', 'total_val' => 41987),
      array('prod_name' => 'Business Setup', 'total_val' => 32456)
    ),
    'channel_data' => array(
      array('ch_name' => 'Online Store', 'total_val' => 46),
      array('ch_name' => 'Retail Store', 'total_val' => 28),
      array('ch_name' => 'Marketplace', 'total_val' => 17),
      array('ch_name' => 'Wholesale', 'total_val' => 9)
    ),
    'top_reps' => array(
      array('rep_name' => 'Sarah Johnson', 'rep_sales' => 126752, 'vs_last' => '18.2%'),
      array('rep_name' => 'Michael Chen', 'rep_sales' => 98432, 'vs_last' => '10.5%'),
      array('rep_name' => 'Emily Davis', 'rep_sales' => 87631, 'vs_last' => '8.7%'),
      array('rep_name' => 'David Martinez', 'rep_sales' => 78942, 'vs_last' => '-2.1%'),
      array('rep_name' => 'Jessica Lee', 'rep_sales' => 65123, 'vs_last' => '5.4%')
    )
  );
?>

<style>
.tv-pres-body {
    background: #f1f5f9;
    min-height: calc(100vh - 60px);
    padding: 20px 24px 40px 24px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: #0f172a;
}
.tv-pres-container {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.06);
    border: 1px solid #e2e8f0;
    padding: 24px 30px;
    margin-bottom: 24px;
}
.tv-topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 22px;
}
.tv-brand-title {
    font-size: 26px;
    font-weight: 800;
    letter-spacing: -0.02em;
    color: #0f172a;
    margin: 0;
    text-transform: uppercase;
}
.tv-brand-title span {
    font-weight: 500;
    color: #475569;
}
.tv-datetime {
    font-size: 15px;
    font-weight: 600;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 16px;
}
.tv-btn-fs {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 6px 12px;
    color: #0f172a;
    font-size: 13px;
    cursor: pointer;
    font-weight: 600;
}
.tv-btn-fs:hover {
    background: #e2e8f0;
}
.tv-grid {
    display: grid;
    grid-template-columns: 240px 1fr 340px;
    gap: 20px;
}
@media (max-width: 1200px) {
    .tv-grid {
        grid-template-columns: 1fr;
    }
}
.tv-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 16px 18px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}
.tv-stat-col {
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.tv-stat-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 14px 18px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-height: 96px;
}
.tv-stat-title {
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 4px;
}
.tv-stat-num {
    font-size: 26px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
    margin-bottom: 4px;
    font-variant-numeric: tabular-nums;
}
.tv-stat-trend {
    font-size: 11.5px;
    font-weight: 600;
    color: #16a34a;
    display: flex;
    align-items: center;
    gap: 4px;
}
.tv-stat-trend.down {
    color: #dc2626;
}
.tv-card-header {
    font-size: 12px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 12px;
}
.tv-chart-box {
    position: relative;
    height: 190px;
    width: 100%;
}
.tv-prod-row {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    gap: 12px;
}
.tv-prod-label {
    width: 130px;
    font-size: 12px;
    font-weight: 600;
    color: #334155;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.tv-prod-bar-wrap {
    flex: 1;
    background: #f1f5f9;
    border-radius: 4px;
    height: 18px;
    overflow: hidden;
    position: relative;
}
.tv-prod-bar-fill {
    height: 100%;
    background: #0284c7;
    border-radius: 4px;
}
.tv-prod-val {
    width: 75px;
    text-align: right;
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
    font-variant-numeric: tabular-nums;
}
.tv-donut-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    height: 175px;
}
.tv-donut-chart {
    position: relative;
    width: 140px;
    height: 140px;
}
.tv-donut-center {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    pointer-events: none;
}
.tv-donut-center-lbl {
    font-size: 9px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
}
.tv-donut-center-val {
    font-size: 13px;
    font-weight: 800;
    color: #0f172a;
}
.tv-donut-legend {
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1;
}
.tv-legend-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 12px;
    font-weight: 600;
}
.tv-legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 6px;
}
.tv-rep-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}
.tv-rep-table th {
    font-size: 10.5px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    padding-bottom: 8px;
    border-bottom: 1px solid #e2e8f0;
}
.tv-rep-table td {
    padding: 7px 0;
    border-bottom: 1px solid #f1f5f9;
    color: #1e293b;
    font-weight: 500;
}
.tv-rep-table td.rep-name {
    font-weight: 600;
}
.tv-rep-table td.rep-sales {
    font-weight: 700;
    font-variant-numeric: tabular-nums;
}
.tv-rep-table td.rep-vs {
    text-align: right;
    font-weight: 700;
    color: #16a34a;
}
.tv-rep-table td.rep-vs.down {
    color: #dc2626;
}
.tv-breakdown-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    margin-bottom: 20px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}
.tv-breakdown-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 12px 18px;
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
}
.tv-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.tv-table th {
    background: #f8fafc;
    color: #475569;
    font-weight: 700;
    padding: 10px 16px;
    border-bottom: 1px solid #e2e8f0;
}
.tv-table td {
    padding: 10px 16px;
    border-bottom: 1px solid #f1f5f9;
    color: #1e293b;
}
.tv-table td.col-num {
    text-align: right;
    font-weight: 700;
    color: #0284c7;
}
</style>

<div class="content-wrapper tv-pres-body" id="livePresWrapper">
    <div class="tv-pres-container">
        <div class="tv-topbar">
            <div>
                <h1 class="tv-brand-title">Sales <span>Dashboard</span></h1>
            </div>
            <div class="tv-datetime">
                <span id="liveClockDisplay"><?php echo date('F d, Y   h:i A'); ?></span>
                <button type="button" class="tv-btn-fs" onclick="toggleFullScreenPres()" title="Toggle Fullscreen">
                    <i class="fa fa-arrows-alt"></i> Fullscreen
                </button>
            </div>
        </div>

        <div class="tv-grid">
            <!-- Left 4 KPI Cards -->
            <div class="tv-stat-col">
                <div class="tv-stat-card">
                    <div class="tv-stat-title">Total Sales</div>
                    <div class="tv-stat-num">AED <?php echo number_format($kpis['total_sales']); ?></div>
                    <div class="tv-stat-trend <?php echo ($kpis['sales_growth'] < 0) ? 'down' : ''; ?>">
                        <i class="fa fa-caret-<?php echo ($kpis['sales_growth'] < 0) ? 'down' : 'up'; ?>"></i> <?php echo abs($kpis['sales_growth']); ?>% vs last month
                    </div>
                </div>

                <div class="tv-stat-card">
                    <div class="tv-stat-title">Orders</div>
                    <div class="tv-stat-num"><?php echo number_format($kpis['orders']); ?></div>
                    <div class="tv-stat-trend <?php echo ($kpis['orders_growth'] < 0) ? 'down' : ''; ?>">
                        <i class="fa fa-caret-<?php echo ($kpis['orders_growth'] < 0) ? 'down' : 'up'; ?>"></i> <?php echo abs($kpis['orders_growth']); ?>% vs last month
                    </div>
                </div>

                <div class="tv-stat-card">
                    <div class="tv-stat-title">Average Order Value</div>
                    <div class="tv-stat-num">AED <?php echo number_format($kpis['aov']); ?></div>
                    <div class="tv-stat-trend <?php echo ($kpis['aov_growth'] < 0) ? 'down' : ''; ?>">
                        <i class="fa fa-caret-<?php echo ($kpis['aov_growth'] < 0) ? 'down' : 'up'; ?>"></i> <?php echo abs($kpis['aov_growth']); ?>% vs last month
                    </div>
                </div>

                <div class="tv-stat-card">
                    <div class="tv-stat-title">New Customers</div>
                    <div class="tv-stat-num"><?php echo number_format($kpis['customers']); ?></div>
                    <div class="tv-stat-trend <?php echo ($kpis['customers_growth'] < 0) ? 'down' : ''; ?>">
                        <i class="fa fa-caret-<?php echo ($kpis['customers_growth'] < 0) ? 'down' : 'up'; ?>"></i> <?php echo abs($kpis['customers_growth']); ?>% vs last month
                    </div>
                </div>
            </div>

            <!-- Middle Column: Sales Over Time + Top Products -->
            <div style="display:flex; flex-direction:column; gap:20px;">
                <div class="tv-card">
                    <div class="tv-card-header">Sales Over Time</div>
                    <div class="tv-chart-box">
                        <canvas id="tvLineChart"></canvas>
                    </div>
                </div>

                <div class="tv-card">
                    <div class="tv-card-header">Top Products</div>
                    <div>
                        <?php 
                          $max_prod_val = 1;
                          foreach($kpis['top_products'] as $p) {
                            if($p['total_val'] > $max_prod_val) $max_prod_val = $p['total_val'];
                          }
                          foreach($kpis['top_products'] as $p){ 
                            $pct = round(($p['total_val'] / $max_prod_val) * 100);
                        ?>
                            <div class="tv-prod-row">
                                <div class="tv-prod-label" title="<?php echo htmlspecialchars($p['prod_name']); ?>"><?php echo htmlspecialchars($p['prod_name']); ?></div>
                                <div class="tv-prod-bar-wrap">
                                    <div class="tv-prod-bar-fill" style="width: <?php echo $pct; ?>%;"></div>
                                </div>
                                <div class="tv-prod-val">AED <?php echo number_format($p['total_val']); ?></div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Right Column: Sales By Channel + Top Sales by Rep -->
            <div style="display:flex; flex-direction:column; gap:20px;">
                <div class="tv-card">
                    <div class="tv-card-header">Sales By Channel</div>
                    <div class="tv-donut-wrap">
                        <div class="tv-donut-chart">
                            <canvas id="tvDonutCanvas" width="140" height="140"></canvas>
                            <div class="tv-donut-center">
                                <div class="tv-donut-center-lbl">Total</div>
                                <div class="tv-donut-center-val">AED <?php echo number_format($kpis['total_sales']); ?></div>
                            </div>
                        </div>
                        <div class="tv-donut-legend">
                            <?php 
                              $colors = array('#2563eb', '#16a34a', '#f59e0b', '#7c3aed');
                              foreach($kpis['channel_data'] as $idx => $ch){ 
                                $c = $colors[$idx % count($colors)];
                            ?>
                                <div class="tv-legend-item">
                                    <div>
                                        <span class="tv-legend-dot" style="background:<?php echo $c; ?>;"></span>
                                        <span><?php echo htmlspecialchars($ch['ch_name']); ?></span>
                                    </div>
                                    <div><?php echo (int)$ch['total_val']; ?>%</div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="tv-card">
                    <div class="tv-card-header">Top Sales By Rep</div>
                    <table class="tv-rep-table">
                        <thead>
                            <tr>
                                <th style="width:40%;">Rep</th>
                                <th style="width:35%;">Sales</th>
                                <th style="width:25%; text-align:right;">Vs Last Month</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($kpis['top_reps'] as $rep){ 
                              $trend_val = !empty($rep['vs_last']) ? $rep['vs_last'] : '+5.0%';
                              $is_down = strpos($trend_val, '-') !== false;
                            ?>
                                <tr>
                                    <td class="rep-name"><?php echo htmlspecialchars($rep['rep_name']); ?></td>
                                    <td class="rep-sales">AED <?php echo number_format((float)$rep['rep_sales']); ?></td>
                                    <td class="rep-vs <?php echo $is_down ? 'down' : ''; ?>">
                                        <i class="fa fa-caret-<?php echo $is_down ? 'down' : 'up'; ?>"></i> <?php echo htmlspecialchars($trend_val); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Statistics Breakdown Section (Keeps all existing functionality) -->
    <div class="tv-breakdown-card">
        <div class="tv-breakdown-header">
            <i class="fa fa-globe" style="color:#0284c7;"></i> Company Operational Statistics
        </div>
        <div class="table-responsive">
            <table class="tv-table">
                <thead>
                    <tr>
                        <th style="width:28%;">Activity</th>
                        <th style="text-align:right; width:18%;">Today</th>
                        <th style="text-align:right; width:18%;">Last 7 Days</th>
                        <th style="text-align:right; width:18%;">This Month</th>
                        <th style="text-align:right; width:18%;">This Quarter</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($company_stats)) { foreach ($company_stats as $row) { ?>
                        <tr>
                            <td style="font-weight:600;"><?php echo htmlspecialchars($row['metric']); ?></td>
                            <td class="col-num"><?php echo htmlspecialchars((string)$row['today']); ?></td>
                            <td class="col-num"><?php echo htmlspecialchars((string)$row['seven_days']); ?></td>
                            <td class="col-num"><?php echo htmlspecialchars((string)$row['month']); ?></td>
                            <td class="col-num"><?php echo htmlspecialchars((string)$row['quarter']); ?></td>
                        </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($company_perf)) { ?>
            <div style="padding:10px 18px; background:#f8fafc; border-top:1px solid #e2e8f0; font-size:12px; font-weight:700; color:#475569; text-transform:uppercase;">
                Company Performers
            </div>
            <div class="table-responsive">
                <table class="tv-table">
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
                                <td style="font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($p['award']); ?></td>
                                <td style="font-weight:700; color:#16a34a;"><?php echo htmlspecialchars($p['current']); ?></td>
                                <td style="font-weight:700; color:#64748b;"><?php echo htmlspecialchars($p['last']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </div>

    <?php if (!empty($centers_data)) { foreach ($centers_data as $c) { ?>
        <div class="tv-breakdown-card">
            <div class="tv-breakdown-header">
                <i class="fa fa-building-o" style="color:#0284c7;"></i> Center: <?php echo htmlspecialchars($c['name']); ?>
            </div>
            <div class="table-responsive">
                <table class="tv-table">
                    <thead>
                        <tr>
                            <th style="width:28%;">Activity</th>
                            <th style="text-align:right; width:18%;">Today</th>
                            <th style="text-align:right; width:18%;">Last 7 Days</th>
                            <th style="text-align:right; width:18%;">This Month</th>
                            <th style="text-align:right; width:18%;">This Quarter</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($c['stats'])) { foreach ($c['stats'] as $row) { ?>
                            <tr>
                                <td style="font-weight:600;"><?php echo htmlspecialchars($row['metric']); ?></td>
                                <td class="col-num"><?php echo htmlspecialchars((string)$row['today']); ?></td>
                                <td class="col-num"><?php echo htmlspecialchars((string)$row['seven_days']); ?></td>
                                <td class="col-num"><?php echo htmlspecialchars((string)$row['month']); ?></td>
                                <td class="col-num"><?php echo htmlspecialchars((string)$row['quarter']); ?></td>
                            </tr>
                        <?php } } ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php } } ?>
</div>

<script>
function toggleFullScreenPres() {
    var el = document.getElementById('livePresWrapper');
    if (!document.fullscreenElement) {
        if (el.requestFullscreen) { el.requestFullscreen(); }
        else if (el.mozRequestFullScreen) { el.mozRequestFullScreen(); }
        else if (el.webkitRequestFullscreen) { el.webkitRequestFullscreen(); }
        else if (el.msRequestFullscreen) { el.msRequestFullscreen(); }
    } else {
        if (document.exitFullscreen) { document.exitFullscreen(); }
    }
}

function updateLiveClock() {
    var now = new Date();
    var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var m = months[now.getMonth()];
    var d = now.getDate();
    var y = now.getFullYear();
    var hours = now.getHours();
    var minutes = now.getMinutes();
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    minutes = minutes < 10 ? '0' + minutes : minutes;
    var str = m + ' ' + d + ', ' + y + '   ' + hours + ':' + minutes + ' ' + ampm;
    var el = document.getElementById('liveClockDisplay');
    if (el) el.innerText = str;
}
setInterval(updateLiveClock, 1000);

$(document).ready(function() {
    // Line Chart
    var lineCanvas = document.getElementById('tvLineChart');
    if (lineCanvas && typeof Chart !== 'undefined') {
        var lineCtx = lineCanvas.getContext('2d');
        var lineData = {
            labels: <?php echo json_encode($kpis['time_labels']); ?>,
            datasets: [{
                label: 'Sales Over Time',
                fillColor: 'rgba(2, 132, 199, 0.08)',
                strokeColor: '#0284c7',
                pointColor: '#0284c7',
                pointStrokeColor: '#ffffff',
                pointHighlightFill: '#ffffff',
                pointHighlightStroke: '#0284c7',
                data: <?php echo json_encode($kpis['time_values']); ?>
            }]
        };
        new Chart(lineCtx).Line(lineData, {
            responsive: true,
            maintainAspectRatio: false,
            scaleShowGridLines: true,
            scaleGridLineColor: 'rgba(0,0,0,.05)',
            scaleFontColor: '#64748b',
            scaleFontSize: 11,
            bezierCurve: true,
            bezierCurveTension: 0.35,
            pointDot: true,
            pointDotRadius: 4
        });
    }

    // Donut Chart
    var donutCanvas = document.getElementById('tvDonutCanvas');
    if (donutCanvas && typeof Chart !== 'undefined') {
        var donutCtx = donutCanvas.getContext('2d');
        var donutData = [
            <?php 
              $donut_colors = array('#2563eb', '#16a34a', '#f59e0b', '#7c3aed');
              foreach($kpis['channel_data'] as $idx => $ch){ 
                $c = $donut_colors[$idx % count($donut_colors)];
            ?>
            {
                value: <?php echo (float)$ch['total_val']; ?>,
                color: '<?php echo $c; ?>',
                highlight: '<?php echo $c; ?>',
                label: '<?php echo htmlspecialchars($ch['ch_name']); ?>'
            },
            <?php } ?>
        ];
        new Chart(donutCtx).Doughnut(donutData, {
            responsive: false,
            percentageInnerCutout: 68,
            animationSteps: 60,
            showTooltips: true
        });
    }

    // Auto-refresh every 30 seconds for live board
    setTimeout(function() {
        location.reload();
    }, 30000);
});
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
