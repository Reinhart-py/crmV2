<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Sales Dashboard - Live Presentation</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/logo.png" type="image/png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/font-awesome.min.css">
  <script src="<?php echo base_url(); ?>assets/plugins/jQuery/jquery-2.2.3.min.js"></script>

  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      background: #e2e8f0;
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      color: #0f172a;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      padding: 16px;
      overflow-x: hidden;
    }
    .tv-wrapper {
      max-width: 1720px;
      width: 100%;
      margin: 0 auto;
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    .tv-card {
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.08);
      border: 1px solid #cbd5e1;
      padding: 24px 30px;
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    .tv-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 22px;
      border-bottom: 1px solid #f1f5f9;
      padding-bottom: 16px;
    }
    .tv-title-wrap {
      display: flex;
      align-items: baseline;
      gap: 12px;
    }
    .tv-main-title {
      font-size: 26px;
      font-weight: 800;
      letter-spacing: -0.5px;
      color: #0f172a;
      text-transform: uppercase;
    }
    .tv-subtitle {
      font-size: 16px;
      font-weight: 500;
      color: #64748b;
      text-transform: uppercase;
    }
    .tv-header-right {
      display: flex;
      align-items: center;
      gap: 18px;
    }
    .tv-clock {
      font-size: 15px;
      font-weight: 600;
      color: #475569;
      letter-spacing: -0.2px;
    }
    .tv-controls {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .tv-select {
      background: #f8fafc;
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      padding: 6px 10px;
      font-size: 12.5px;
      font-weight: 600;
      color: #334155;
      outline: none;
      cursor: pointer;
    }
    .tv-btn {
      background: #0072f0;
      border: 1px solid #0072f0;
      border-radius: 6px;
      padding: 6px 12px;
      font-size: 12.5px;
      font-weight: 600;
      color: #ffffff;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: background 0.15s ease;
    }
    .tv-btn:hover {
      background: #0056b3;
    }
    .tv-btn-sec {
      background: #f8fafc;
      border: 1px solid #cbd5e1;
      color: #334155;
    }
    .tv-btn-sec:hover {
      background: #e2e8f0;
    }

    /* Grid Layout */
    .tv-dashboard-grid {
      display: grid;
      grid-template-columns: 240px 1fr 340px;
      gap: 20px;
      flex: 1;
    }
    @media (max-width: 1280px) {
      .tv-dashboard-grid {
        grid-template-columns: 220px 1fr 300px;
        gap: 16px;
      }
    }
    @media (max-width: 992px) {
      .tv-dashboard-grid {
        grid-template-columns: 1fr;
      }
    }

    /* Left Stats Column */
    .tv-kpi-column {
      display: flex;
      flex-direction: column;
      gap: 14px;
    }
    .tv-kpi-card {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 16px 18px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      flex: 1;
    }
    .tv-kpi-label {
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #64748b;
      margin-bottom: 6px;
    }
    .tv-kpi-value {
      font-size: 28px;
      font-weight: 800;
      color: #0f172a;
      line-height: 1.1;
      letter-spacing: -0.5px;
      margin-bottom: 6px;
    }
    .tv-kpi-growth {
      font-size: 12.5px;
      font-weight: 600;
      color: #16a34a;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }
    .tv-kpi-growth.neg {
      color: #dc2626;
    }

    /* Middle Column */
    .tv-middle-column {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
    .tv-panel {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 18px 20px;
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    .tv-panel-title {
      font-size: 13px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #475569;
      margin-bottom: 14px;
    }

    /* Right Column */
    .tv-right-column {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    /* Products Bars */
    .tv-prod-row {
      display: flex;
      align-items: center;
      margin-bottom: 12px;
      gap: 12px;
    }
    .tv-prod-name {
      width: 140px;
      font-size: 13px;
      font-weight: 600;
      color: #1e293b;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .tv-prod-bar-wrap {
      flex: 1;
      background: #e2e8f0;
      border-radius: 4px;
      height: 22px;
      overflow: hidden;
      position: relative;
    }
    .tv-prod-bar-fill {
      background: #0072f0;
      height: 100%;
      border-radius: 4px;
      transition: width 0.4s ease;
    }
    .tv-prod-val {
      width: 75px;
      text-align: right;
      font-size: 13px;
      font-weight: 700;
      color: #0f172a;
    }

    /* Donut Chart Simulation */
    .tv-donut-wrap {
      display: flex;
      align-items: center;
      justify-content: space-around;
      flex: 1;
      gap: 14px;
    }
    .tv-donut-svg {
      width: 140px;
      height: 140px;
      transform: rotate(-90deg);
    }
    .tv-donut-legend {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .tv-legend-item {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 12.5px;
      font-weight: 600;
      color: #334155;
    }
    .tv-legend-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      flex-shrink: 0;
    }

    /* Rep Table */
    .tv-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 12.5px;
    }
    .tv-table th {
      text-align: left;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      color: #64748b;
      padding: 6px 8px;
      border-bottom: 1px solid #e2e8f0;
    }
    .tv-table td {
      padding: 9px 8px;
      border-bottom: 1px solid #f1f5f9;
      color: #1e293b;
      font-weight: 500;
    }
    .tv-table tr:last-child td {
      border-bottom: none;
    }
    .tv-rep-name {
      font-weight: 600;
      color: #0f172a;
    }
    .tv-rep-val {
      font-weight: 700;
      color: #0f172a;
    }
    .tv-rep-growth {
      font-weight: 600;
      color: #16a34a;
      display: inline-flex;
      align-items: center;
      gap: 2px;
    }
    .tv-rep-growth.neg {
      color: #dc2626;
    }

    /* Tab switcher for Company Executive Matrix */
    .tv-tab-toggle {
      display: flex;
      align-items: center;
      background: #f1f5f9;
      border-radius: 6px;
      padding: 3px;
      gap: 4px;
    }
    .tv-tab-btn {
      border: none;
      background: transparent;
      padding: 5px 12px;
      border-radius: 4px;
      font-size: 12px;
      font-weight: 600;
      color: #64748b;
      cursor: pointer;
    }
    .tv-tab-btn.active {
      background: #ffffff;
      color: #0072f0;
      box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
  </style>
</head>
<body>
  <div class="tv-wrapper">
    <div class="tv-card">
      <div class="tv-header">
        <div class="tv-title-wrap">
          <h1 class="tv-main-title">Sales</h1>
          <span class="tv-subtitle">Dashboard</span>
        </div>

        <div class="tv-header-right">
          <div class="tv-tab-toggle">
            <button type="button" class="tv-tab-btn active" id="tabBtnDashboard" onclick="switchView('dashboard')">TV Dashboard</button>
            <button type="button" class="tv-tab-btn" id="tabBtnCompany" onclick="switchView('company')">Company Matrix</button>
          </div>

          <div class="tv-clock" id="liveClock">Loading time...</div>

          <div class="tv-controls">
            <select class="tv-select" id="refreshRateSelect" onchange="changeRefreshRate()">
              <option value="10000">10s Refresh</option>
              <option value="30000" selected>30s Refresh</option>
              <option value="60000">1m Refresh</option>
              <option value="300000">5m Refresh</option>
              <option value="0">Manual</option>
            </select>
            <button type="button" class="tv-btn" onclick="fetchLiveData(true)" title="Refresh data now">
              <i class="fa fa-refresh"></i> Refresh
            </button>
            <button type="button" class="tv-btn tv-btn-sec" onclick="toggleFullscreen()" title="Toggle Fullscreen">
              <i class="fa fa-arrows-alt"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Main TV Dashboard View -->
      <div id="viewDashboard" class="tv-dashboard-grid">
        <!-- 1. Left 4 KPI Cards -->
        <div class="tv-kpi-column">
          <div class="tv-kpi-card">
            <span class="tv-kpi-label">Total Sales</span>
            <span class="tv-kpi-value" id="kpiTotalSales">$742,432</span>
            <span class="tv-kpi-growth" id="kpiSalesGrowth"><i class="fa fa-caret-up"></i> 12.4% vs last month</span>
          </div>

          <div class="tv-kpi-card">
            <span class="tv-kpi-label">Orders</span>
            <span class="tv-kpi-value" id="kpiOrders">1,247</span>
            <span class="tv-kpi-growth" id="kpiOrdersGrowth"><i class="fa fa-caret-up"></i> 8.7% vs last month</span>
          </div>

          <div class="tv-kpi-card">
            <span class="tv-kpi-label">Average Order Value</span>
            <span class="tv-kpi-value" id="kpiAov">$595</span>
            <span class="tv-kpi-growth" id="kpiAovGrowth"><i class="fa fa-caret-up"></i> 3.1% vs last month</span>
          </div>

          <div class="tv-kpi-card">
            <span class="tv-kpi-label">New Customers</span>
            <span class="tv-kpi-value" id="kpiCustomers">312</span>
            <span class="tv-kpi-growth" id="kpiCustomersGrowth"><i class="fa fa-caret-up"></i> 15.2% vs last month</span>
          </div>
        </div>

        <!-- 2. Middle Column: Sales Over Time & Top Products -->
        <div class="tv-middle-column">
          <div class="tv-panel">
            <div class="tv-panel-title">Sales Over Time</div>
            <div style="flex:1; min-height:160px; position:relative; width:100%;">
              <svg id="salesLineSvg" viewBox="0 0 540 180" style="width:100%; height:100%; overflow:visible;">
                <!-- Grid Lines -->
                <line x1="40" y1="20" x2="520" y2="20" stroke="#f1f5f9" stroke-width="1" />
                <line x1="40" y1="60" x2="520" y2="60" stroke="#f1f5f9" stroke-width="1" />
                <line x1="40" y1="100" x2="520" y2="100" stroke="#f1f5f9" stroke-width="1" />
                <line x1="40" y1="140" x2="520" y2="140" stroke="#e2e8f0" stroke-width="1" />
                
                <!-- Y Labels -->
                <text x="10" y="24" fill="#94a3b8" font-size="10" font-weight="600">100K</text>
                <text x="10" y="64" fill="#94a3b8" font-size="10" font-weight="600">60K</text>
                <text x="10" y="104" fill="#94a3b8" font-size="10" font-weight="600">20K</text>
                <text x="25" y="144" fill="#94a3b8" font-size="10" font-weight="600">0</text>

                <!-- Chart Path -->
                <path id="salesLinePath" d="M 60,130 L 130,95 L 205,108 L 280,75 L 355,90 L 430,65 L 505,35" fill="none" stroke="#0072f0" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                
                <!-- Data Points -->
                <g id="salesPoints">
                  <circle cx="60" cy="130" r="4" fill="#0072f0" />
                  <circle cx="130" cy="95" r="4" fill="#0072f0" />
                  <circle cx="205" cy="108" r="4" fill="#0072f0" />
                  <circle cx="280" cy="75" r="4" fill="#0072f0" />
                  <circle cx="355" cy="90" r="4" fill="#0072f0" />
                  <circle cx="430" cy="65" r="4" fill="#0072f0" />
                  <circle cx="505" cy="35" r="4" fill="#0072f0" />
                </g>

                <!-- X Axis Labels -->
                <g id="salesXLabels">
                  <text x="50" y="165" fill="#64748b" font-size="10.5" font-weight="600">Jun 18</text>
                  <text x="120" y="165" fill="#64748b" font-size="10.5" font-weight="600">Jun 19</text>
                  <text x="195" y="165" fill="#64748b" font-size="10.5" font-weight="600">Jun 20</text>
                  <text x="270" y="165" fill="#64748b" font-size="10.5" font-weight="600">Jun 21</text>
                  <text x="345" y="165" fill="#64748b" font-size="10.5" font-weight="600">Jun 22</text>
                  <text x="420" y="165" fill="#64748b" font-size="10.5" font-weight="600">Jun 23</text>
                  <text x="495" y="165" fill="#64748b" font-size="10.5" font-weight="600">Jun 24</text>
                </g>
              </svg>
            </div>
          </div>

          <div class="tv-panel">
            <div class="tv-panel-title">Top Products</div>
            <div id="topProductsList">
              <div class="tv-prod-row">
                <span class="tv-prod-name">Accounting &amp; VAT</span>
                <div class="tv-prod-bar-wrap"><div class="tv-prod-bar-fill" style="width: 100%;"></div></div>
                <span class="tv-prod-val">$98,432</span>
              </div>
              <div class="tv-prod-row">
                <span class="tv-prod-name">Telecom (Du/Etisalat)</span>
                <div class="tv-prod-bar-wrap"><div class="tv-prod-bar-fill" style="width: 74%;"></div></div>
                <span class="tv-prod-val">$72,680</span>
              </div>
              <div class="tv-prod-row">
                <span class="tv-prod-name">IT Services &amp; Cloud</span>
                <div class="tv-prod-bar-wrap"><div class="tv-prod-bar-fill" style="width: 57%;"></div></div>
                <span class="tv-prod-val">$56,214</span>
              </div>
              <div class="tv-prod-row">
                <span class="tv-prod-name">Corporate Services</span>
                <div class="tv-prod-bar-wrap"><div class="tv-prod-bar-fill" style="width: 43%;"></div></div>
                <span class="tv-prod-val">$41,987</span>
              </div>
              <div class="tv-prod-row">
                <span class="tv-prod-name">Business Setup</span>
                <div class="tv-prod-bar-wrap"><div class="tv-prod-bar-fill" style="width: 33%;"></div></div>
                <span class="tv-prod-val">$32,456</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 3. Right Column: Sales by Channel & Top Sales by Rep -->
        <div class="tv-right-column">
          <div class="tv-panel">
            <div class="tv-panel-title">Sales by Channel</div>
            <div class="tv-donut-wrap">
              <div style="position:relative; width:130px; height:130px; display:flex; align-items:center; justify-content:center;">
                <svg class="tv-donut-svg" viewBox="0 0 42 42">
                  <circle cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="#0072f0" stroke-width="6" stroke-dasharray="46 54" stroke-dashoffset="0"></circle>
                  <circle cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="#16a34a" stroke-width="6" stroke-dasharray="28 72" stroke-dashoffset="-46"></circle>
                  <circle cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="#f59e0b" stroke-width="6" stroke-dasharray="17 83" stroke-dashoffset="-74"></circle>
                  <circle cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="#7c3aed" stroke-width="6" stroke-dasharray="9 91" stroke-dashoffset="-91"></circle>
                </svg>
                <div style="position:absolute; text-align:center;">
                  <div style="font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase;">Total</div>
                  <div id="donutCenterTotal" style="font-size:12.5px; font-weight:800; color:#0f172a;">$742,432</div>
                </div>
              </div>

              <div class="tv-donut-legend">
                <div class="tv-legend-item"><span class="tv-legend-dot" style="background:#0072f0;"></span> <span>Direct CRM</span> <span style="margin-left:auto; font-weight:700;">46%</span></div>
                <div class="tv-legend-item"><span class="tv-legend-dot" style="background:#16a34a;"></span> <span>Field Sales</span> <span style="margin-left:auto; font-weight:700;">28%</span></div>
                <div class="tv-legend-item"><span class="tv-legend-dot" style="background:#f59e0b;"></span> <span>Referrals</span> <span style="margin-left:auto; font-weight:700;">17%</span></div>
                <div class="tv-legend-item"><span class="tv-legend-dot" style="background:#7c3aed;"></span> <span>Corporate</span> <span style="margin-left:auto; font-weight:700;">9%</span></div>
              </div>
            </div>
          </div>

          <div class="tv-panel">
            <div class="tv-panel-title">Top Sales by Rep</div>
            <table class="tv-table">
              <thead>
                <tr>
                  <th>Rep</th>
                  <th>Sales</th>
                  <th>vs Last Month</th>
                </tr>
              </thead>
              <tbody id="topRepsTbody">
                <tr>
                  <td class="tv-rep-name">Sarah Johnson</td>
                  <td class="tv-rep-val">$126,752</td>
                  <td><span class="tv-rep-growth"><i class="fa fa-caret-up"></i> 18.2%</span></td>
                </tr>
                <tr>
                  <td class="tv-rep-name">Michael Chen</td>
                  <td class="tv-rep-val">$98,432</td>
                  <td><span class="tv-rep-growth"><i class="fa fa-caret-up"></i> 10.5%</span></td>
                </tr>
                <tr>
                  <td class="tv-rep-name">Emily Davis</td>
                  <td class="tv-rep-val">$87,631</td>
                  <td><span class="tv-rep-growth"><i class="fa fa-caret-up"></i> 8.7%</span></td>
                </tr>
                <tr>
                  <td class="tv-rep-name">David Martinez</td>
                  <td class="tv-rep-val">$78,942</td>
                  <td><span class="tv-rep-growth neg"><i class="fa fa-caret-down"></i> -2.1%</span></td>
                </tr>
                <tr>
                  <td class="tv-rep-name">Jessica Lee</td>
                  <td class="tv-rep-val">$65,123</td>
                  <td><span class="tv-rep-growth"><i class="fa fa-caret-up"></i> 5.4%</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Alternative Company Matrix View -->
      <div id="viewCompany" style="display:none; flex:1; overflow-y:auto;">
        <h3 style="font-size:16px; font-weight:700; color:#0f172a; margin-bottom:14px; text-transform:uppercase;">Overall Company Live Statistics</h3>
        <table class="tv-table" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:6px; overflow:hidden;">
          <thead style="background:#f8fafc;">
            <tr>
              <th style="padding:12px 14px;">Metric</th>
              <th style="padding:12px 14px;">Today</th>
              <th style="padding:12px 14px;">Last 7 Days</th>
              <th style="padding:12px 14px;">This Month</th>
              <th style="padding:12px 14px;">This Quarter</th>
            </tr>
          </thead>
          <tbody id="companyStatsTbody">
            <?php if (!empty($company_stats)) {
              foreach ($company_stats as $st) { ?>
                <tr>
                  <td style="padding:12px 14px; font-weight:700; color:#0f172a;"><?php echo htmlspecialchars($st['metric']); ?></td>
                  <td style="padding:12px 14px; font-weight:600;"><?php echo is_numeric($st['today']) ? number_format($st['today']) : htmlspecialchars($st['today']); ?></td>
                  <td style="padding:12px 14px; font-weight:600;"><?php echo is_numeric($st['seven_days']) ? number_format($st['seven_days']) : htmlspecialchars($st['seven_days']); ?></td>
                  <td style="padding:12px 14px; font-weight:700; color:#0072f0;"><?php echo is_numeric($st['month']) ? number_format($st['month']) : htmlspecialchars($st['month']); ?></td>
                  <td style="padding:12px 14px; font-weight:600;"><?php echo is_numeric($st['quarter']) ? number_format($st['quarter']) : htmlspecialchars($st['quarter']); ?></td>
                </tr>
            <?php } } ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>

  <script>
    var currentRefreshTimer = null;

    function updateLiveClock() {
      var now = new Date();
      var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
      var m = months[now.getMonth()];
      var d = now.getDate();
      var y = now.getFullYear();
      var hours = now.getHours();
      var mins = now.getMinutes();
      var ampm = hours >= 12 ? 'PM' : 'AM';
      hours = hours % 12;
      hours = hours ? hours : 12;
      mins = mins < 10 ? '0' + mins : mins;
      var str = m + ' ' + d + ', ' + y + '   ' + hours + ':' + mins + ' ' + ampm;
      $('#liveClock').text(str);
    }
    updateLiveClock();
    setInterval(updateLiveClock, 1000);

    function switchView(mode) {
      if (mode === 'company') {
        $('#viewDashboard').hide();
        $('#viewCompany').show();
        $('#tabBtnDashboard').removeClass('active');
        $('#tabBtnCompany').addClass('active');
      } else {
        $('#viewCompany').hide();
        $('#viewDashboard').show();
        $('#tabBtnCompany').removeClass('active');
        $('#tabBtnDashboard').addClass('active');
      }
    }

    function toggleFullscreen() {
      if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(function(err) {});
      } else {
        if (document.exitFullscreen) {
          document.exitFullscreen();
        }
      }
    }

    function fetchLiveData(manual) {
      $.ajax({
        url: '<?php echo base_url(); ?>manager/live/ajax_presentation_data',
        type: 'GET',
        dataType: 'json',
        success: function(res) {
          if (res && res.status && res.kpis) {
            var k = res.kpis;
            $('#kpiTotalSales').text(k.total_sales_fmt || '$' + k.total_sales);
            $('#kpiOrders').text(k.orders_fmt || k.orders);
            $('#kpiAov').text(k.aov_fmt || '$' + k.aov);
            $('#kpiCustomers').text(k.customers_fmt || k.customers);
            $('#donutCenterTotal').text(k.total_sales_fmt || '$' + k.total_sales);

            var sUp = k.sales_growth >= 0;
            $('#kpiSalesGrowth').html('<i class="fa fa-caret-' + (sUp ? 'up' : 'down') + '"></i> ' + Math.abs(k.sales_growth) + '% vs last month').toggleClass('neg', !sUp);

            var oUp = k.orders_growth >= 0;
            $('#kpiOrdersGrowth').html('<i class="fa fa-caret-' + (oUp ? 'up' : 'down') + '"></i> ' + Math.abs(k.orders_growth) + '% vs last month').toggleClass('neg', !oUp);

            var aUp = k.aov_growth >= 0;
            $('#kpiAovGrowth').html('<i class="fa fa-caret-' + (aUp ? 'up' : 'down') + '"></i> ' + Math.abs(k.aov_growth) + '% vs last month').toggleClass('neg', !aUp);

            var cUp = k.customers_growth >= 0;
            $('#kpiCustomersGrowth').html('<i class="fa fa-caret-' + (cUp ? 'up' : 'down') + '"></i> ' + Math.abs(k.customers_growth) + '% vs last month').toggleClass('neg', !cUp);

            // Update Top Reps
            if (k.top_reps && k.top_reps.length > 0) {
              var rHtml = '';
              $.each(k.top_reps, function(i, r) {
                var isNeg = (r.vs_last && r.vs_last.indexOf('-') !== -1);
                rHtml += '<tr>';
                rHtml += '  <td class="tv-rep-name">' + $('<div>').text(r.rep_name).html() + '</td>';
                rHtml += '  <td class="tv-rep-val">$' + Number(r.rep_sales).toLocaleString() + '</td>';
                rHtml += '  <td><span class="tv-rep-growth ' + (isNeg ? 'neg' : '') + '"><i class="fa fa-caret-' + (isNeg ? 'down' : 'up') + '"></i> ' + $('<div>').text(r.vs_last).html() + '</span></td>';
                rHtml += '</tr>';
              });
              $('#topRepsTbody').html(rHtml);
            }

            // Update Company Matrix table
            if (res.company_stats && res.company_stats.length > 0) {
              var cHtml = '';
              $.each(res.company_stats, function(i, s) {
                cHtml += '<tr>';
                cHtml += '  <td style="padding:12px 14px; font-weight:700; color:#0f172a;">' + $('<div>').text(s.metric).html() + '</td>';
                cHtml += '  <td style="padding:12px 14px; font-weight:600;">' + (isNaN(s.today) ? s.today : Number(s.today).toLocaleString()) + '</td>';
                cHtml += '  <td style="padding:12px 14px; font-weight:600;">' + (isNaN(s.seven_days) ? s.seven_days : Number(s.seven_days).toLocaleString()) + '</td>';
                cHtml += '  <td style="padding:12px 14px; font-weight:700; color:#0072f0;">' + (isNaN(s.month) ? s.month : Number(s.month).toLocaleString()) + '</td>';
                cHtml += '  <td style="padding:12px 14px; font-weight:600;">' + (isNaN(s.quarter) ? s.quarter : Number(s.quarter).toLocaleString()) + '</td>';
                cHtml += '</tr>';
              });
              $('#companyStatsTbody').html(cHtml);
            }
          }
        }
      });
    }

    function changeRefreshRate() {
      if (currentRefreshTimer) {
        clearInterval(currentRefreshTimer);
        currentRefreshTimer = null;
      }
      var ms = parseInt($('#refreshRateSelect').val(), 10);
      if (ms > 0) {
        currentRefreshTimer = setInterval(function() {
          fetchLiveData(false);
        }, ms);
      }
    }

    // Initialize with 30s polling
    changeRefreshRate();
  </script>
</body>
</html>
