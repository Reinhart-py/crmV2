<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Live Presentation</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/logo.png" type="image/png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/font-awesome.min.css">
  <script src="<?php echo base_url(); ?>assets/plugins/jQuery/jquery-2.2.3.min.js"></script>
  <style>
    :root {
      --bg:#e2e8f0; --card:#ffffff; --panel:#f8fafc; --line:#e2e8f0; --text:#0f172a; --muted:#64748b;
      --brand:#1e3a8a; --brand2:#2563eb; --good:#16a34a; --bad:#dc2626; --track:#e2e8f0; --gold:#d97706;
    }
    body.tv-dark {
      --bg:#020617; --card:#0f172a; --panel:#111c33; --line:#1e293b; --text:#f8fafc; --muted:#94a3b8;
      --brand:#60a5fa; --brand2:#3b82f6; --good:#4ade80; --bad:#f87171; --track:#1e293b; --gold:#fbbf24;
    }
    * { box-sizing:border-box; margin:0; padding:0; }
    html, body { height:100%; }
    body { background:var(--bg); color:var(--text); font-family:'Plus Jakarta Sans', -apple-system, "Segoe UI", Roboto, sans-serif; padding:14px; overflow-x:hidden; }
    .tv { max-width:1800px; margin:0 auto; min-height:calc(100vh - 28px); background:var(--card); border:1px solid var(--line); border-radius:14px; padding:18px 24px; display:flex; flex-direction:column; }
    .tv-head { display:flex; justify-content:space-between; align-items:center; gap:14px; flex-wrap:nowrap; padding:10px 0 12px; border-bottom:1px solid var(--line); margin:-10px 0 14px; position:sticky; top:0; z-index:10; background:var(--card); }
    .tv-brand h1, .tv-brand .tv-live { white-space:nowrap; }
    .tv-brand { display:flex; align-items:center; gap:12px; }
    .tv-brand img { height:38px; }
    body.tv-dark .tv-brand img { background:#fff; border-radius:6px; padding:3px; }
    .tv-brand h1 { font-size:24px; font-weight:800; letter-spacing:-.4px; }
    .tv-brand .tv-live { display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:800; color:var(--bad); letter-spacing:.08em; }
    .tv-brand .tv-live i { width:8px; height:8px; border-radius:50%; background:var(--bad); animation:pulse 1.6s infinite; }
    @keyframes pulse { 50% { opacity:.25; } }
    .tv-right { display:flex; align-items:center; gap:14px; flex-wrap:nowrap; min-width:0; }
    .tv-tabs button, .tv-clock b, .tv-clock span { white-space:nowrap; }
    .tv-clock { text-align:right; }
    .tv-clock b { display:block; font-size:22px; font-weight:800; font-variant-numeric:tabular-nums; }
    .tv-clock span { font-size:12px; color:var(--muted); font-weight:600; }
    .tv-tabs { display:flex; gap:4px; background:var(--panel); border:1px solid var(--line); border-radius:10px; padding:4px; }
    .tv-tabs button { border:0; background:none; color:var(--muted); font:inherit; font-size:13px; font-weight:700; padding:7px 12px; border-radius:7px; cursor:pointer; }
    .tv-tabs button.active { background:var(--brand); color:#fff; }
    body.tv-dark .tv-tabs button.active { color:#0f172a; }
    .tv-controls { display:flex; gap:6px; align-items:center; transition:opacity .3s; }
    .tv-btn, .tv-select { height:34px; border:1px solid var(--line); background:var(--panel); color:var(--text); border-radius:8px; font:inherit; font-size:13px; font-weight:700; padding:0 10px; cursor:pointer; display:inline-flex; align-items:center; gap:6px; }
    .tv-btn:hover { border-color:var(--brand); }
    .tv-updated { font-size:12px; color:var(--muted); font-weight:600; white-space:nowrap; }
    body.tv-idle .tv-controls, body.tv-idle .tv-tabs { opacity:0; pointer-events:none; }
    body.tv-idle { cursor:none; }

    .tv-slide { display:none; flex:1; min-height:0; animation:fade .5s; }
    .tv-slide.active { display:flex; flex-direction:column; }
    .fit { flex:1 0 auto; display:flex; flex-direction:column; transform-origin:top left; min-width:0; }
    @media (min-width: 701px) {
      html, body { overflow:hidden; }
      .tv { height:calc(100vh - 28px); min-height:0; overflow:hidden; }
      .tv-slide.active { overflow:hidden; }
    }
    @keyframes fade { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:none; } }
    .tv-slide-title { font-size:15px; font-weight:800; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); margin-bottom:12px; display:flex; align-items:center; gap:8px; }
    .tv-slide-title i { color:var(--brand); }

    .tv-panel { background:var(--panel); border:1px solid var(--line); border-radius:12px; padding:16px 18px; min-width:0; }
    .tv-panel h3 { font-size:13px; font-weight:800; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); margin-bottom:12px; display:flex; justify-content:space-between; gap:8px; }
    .tv-empty { color:var(--muted); font-size:14px; font-weight:600; padding:18px 0; text-align:center; }

    /* Overview */
    .ov-grid { display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:14px; margin-bottom:14px; }
    .kpi .kpi-label { font-size:13px; font-weight:800; color:var(--muted); text-transform:uppercase; letter-spacing:.04em; }
    .kpi .kpi-value { display:block; font-size:clamp(28px, 2.6vw, 44px); font-weight:800; letter-spacing:-1px; margin:6px 0 4px; font-variant-numeric:tabular-nums; }
    .kpi .kpi-sub { font-size:13px; font-weight:700; color:var(--muted); }
    .up { color:var(--good); } .down { color:var(--bad); }
    .flash { animation:flash 1.4s; }
    @keyframes flash { 0% { background:rgba(37,99,235,.25); } 100% { background:transparent; } }
    .ov-main { display:grid; grid-template-columns:minmax(0, 7fr) minmax(0, 5fr); gap:14px; flex:1; }
    .ov-col { display:flex; flex-direction:column; gap:14px; min-width:0; }
    .tgt-row { display:flex; justify-content:space-between; align-items:baseline; gap:10px; flex-wrap:wrap; margin-bottom:8px; }
    .tgt-row b { font-size:26px; font-weight:800; }
    .tgt-row span { font-size:14px; color:var(--muted); font-weight:700; }
    .bar { height:16px; background:var(--track); border-radius:10px; overflow:hidden; position:relative; }
    .bar i { display:block; height:100%; background:linear-gradient(90deg, var(--brand2), var(--brand)); border-radius:10px; transition:width 1s ease; }
    .bar.small { height:8px; }
    .chart { flex:1; display:flex; flex-direction:column; }
    .chart #trendChart { flex:1; min-height:140px; position:relative; }
    .chart svg { position:absolute; inset:0; width:100%; height:100%; display:block; }
    .prod-row { display:grid; grid-template-columns:minmax(0, 2fr) minmax(0, 3fr) auto; gap:12px; align-items:center; padding:7px 0; font-weight:700; font-size:14px; }
    .prod-row .n { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .prod-row .v { text-align:right; font-variant-numeric:tabular-nums; }
    .feed { display:flex; flex-direction:column; gap:8px; }
    .feed-item { display:flex; align-items:center; gap:12px; padding:10px 12px; background:var(--card); border:1px solid var(--line); border-radius:10px; }
    .feed-item .who { flex:1; min-width:0; }
    .feed-item .who b { display:block; font-size:15px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .feed-item .who span { font-size:12.5px; color:var(--muted); font-weight:600; display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .feed-item .amt { text-align:right; font-weight:800; font-size:15px; white-space:nowrap; }
    .feed-item .amt span { display:block; font-size:11.5px; color:var(--muted); font-weight:600; }
    .feed-item.is-new { border-color:var(--good); box-shadow:0 0 0 2px var(--good); }
    .av { width:40px; height:40px; border-radius:50%; background:var(--brand); color:#fff; display:inline-flex; align-items:center; justify-content:center; font-weight:800; font-size:15px; flex-shrink:0; overflow:hidden; }
    body.tv-dark .av { color:#0f172a; }
    .av img { width:100%; height:100%; object-fit:cover; }

    /* Leaderboard */
    .lb { width:100%; border-collapse:separate; border-spacing:0 6px; }
    .lb th { text-align:left; font-size:12px; font-weight:800; color:var(--muted); text-transform:uppercase; letter-spacing:.05em; padding:0 14px; }
    .lb th.r, .lb td.r { text-align:right; }
    .lb td { background:var(--panel); padding:10px 14px; font-size:17px; font-weight:700; font-variant-numeric:tabular-nums; border-top:1px solid var(--line); border-bottom:1px solid var(--line); }
    .lb td:first-child { border-left:1px solid var(--line); border-radius:10px 0 0 10px; width:64px; text-align:center; }
    .lb td:last-child { border-right:1px solid var(--line); border-radius:0 10px 10px 0; }
    .lb .rank { font-size:20px; font-weight:800; color:var(--muted); }
    .lb tr.top1 td { background:rgba(217,119,6,.12); } .lb tr.top1 .rank { color:var(--gold); }
    .lb .person { display:flex; align-items:center; gap:12px; min-width:0; }
    .lb .person b { display:block; }
    .lb .person span { display:block; font-size:12.5px; color:var(--muted); font-weight:600; }
    .lb .tp { min-width:120px; }
    .lb .tp small { display:block; font-size:12px; color:var(--muted); margin-top:3px; }

    /* Centers */
    .ct-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(360px, 1fr)); gap:14px; }
    .ct-card h4 { font-size:19px; font-weight:800; margin-bottom:4px; }
    .ct-card .ct-staff { font-size:13px; color:var(--muted); font-weight:600; margin-bottom:12px; }
    .ct-stats { display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:8px; margin-top:12px; }
    .ct-stats div { background:var(--card); border:1px solid var(--line); border-radius:8px; padding:8px 10px; }
    .ct-stats span { display:block; font-size:11px; color:var(--muted); font-weight:800; text-transform:uppercase; }
    .ct-stats b { font-size:20px; font-weight:800; font-variant-numeric:tabular-nums; }

    /* Activity */
    .act { width:100%; border-collapse:collapse; }
    .act th, .act td { padding:13px 16px; border-bottom:1px solid var(--line); text-align:right; font-variant-numeric:tabular-nums; }
    .act th { font-size:12px; color:var(--muted); font-weight:800; text-transform:uppercase; letter-spacing:.05em; }
    .act th:first-child, .act td:first-child { text-align:left; }
    .act td { font-size:18px; font-weight:700; }
    .act td.today { color:var(--brand); font-weight:800; }
    .bc-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(min(100%, 430px), 1fr)); gap:14px; }
    .bc-grid h4 { font-size:17px; font-weight:800; margin:4px 8px 2px; display:flex; justify-content:space-between; gap:8px; }
    .bc-grid h4 small { font-size:12px; color:var(--muted); font-weight:700; }
    .bc-grid .act th, .bc-grid .act td { padding:8px 10px; }
    .bc-grid .act td { font-size:16px; }

    .tv-banner { display:none; position:fixed; top:14px; left:50%; transform:translateX(-50%); background:var(--bad); color:#fff; font-weight:700; padding:8px 16px; border-radius:8px; z-index:20; }
    /* New sale celebration: photo + name for 20 seconds */
    .cele { position:fixed; inset:0; z-index:50; display:none; align-items:center; justify-content:center; background:rgba(2,6,23,.72); overflow:hidden; padding:16px; }
    .cele.show { display:flex; animation:celeIn .35s ease-out; }
    .cele.out { animation:celeOut .4s ease-in forwards; }
    @keyframes celeIn { from { opacity:0; } to { opacity:1; } }
    @keyframes celeOut { to { opacity:0; } }
    .cele-card { position:relative; z-index:2; text-align:center; color:#fff; animation:celePop .6s cubic-bezier(.2,1.4,.4,1); max-width:92vw; }
    @keyframes celePop { from { transform:scale(.4); opacity:0; } to { transform:none; opacity:1; } }
    .cele-tag { display:inline-flex; align-items:center; gap:10px; background:linear-gradient(90deg, #f59e0b, #fbbf24); color:#1f1300; font-weight:800; font-size:clamp(18px, 2.4vw, 30px); letter-spacing:.06em; text-transform:uppercase; padding:10px 26px; border-radius:999px; box-shadow:0 10px 30px rgba(245,158,11,.45); }
    .cele-photo { position:relative; width:clamp(150px, 22vw, 260px); height:clamp(150px, 22vw, 260px); margin:28px auto 22px; }
    .cele-photo::before, .cele-photo::after { content:''; position:absolute; inset:-14px; border-radius:50%; border:4px solid #fbbf24; animation:celeRing 1.6s ease-out infinite; }
    .cele-photo::after { animation-delay:.8s; }
    @keyframes celeRing { from { transform:scale(.9); opacity:1; } to { transform:scale(1.35); opacity:0; } }
    .cele-photo .av { width:100%; height:100%; font-size:clamp(54px, 8vw, 96px); border:6px solid #fff; box-shadow:0 18px 50px rgba(0,0,0,.45); background:var(--brand2); color:#fff; }
    .cele-name { font-size:clamp(32px, 5vw, 64px); font-weight:800; line-height:1.1; text-shadow:0 4px 20px rgba(0,0,0,.4); }
    .cele-sub { margin-top:10px; font-size:clamp(16px, 2vw, 24px); font-weight:600; opacity:.9; }
    .cele-amt { margin-top:14px; font-size:clamp(28px, 4vw, 52px); font-weight:800; color:#4ade80; }
    .cele-bit { position:absolute; top:-20px; width:12px; height:18px; border-radius:3px; opacity:.95; animation:celeFall linear infinite; animation-fill-mode:backwards; z-index:1; }
    @keyframes celeFall { to { transform:translateY(110vh) rotate(720deg); } }
    @media (prefers-reduced-motion: reduce) { .cele-card, .cele-photo::before, .cele-photo::after, .cele-bit { animation:none !important; } .cele-bit { display:none; } }

    @media (max-width: 1500px) { .tv-updated { display:none; } }
    @media (max-width: 1200px) {
      .tv-head, .tv-right { gap:10px; }
      .tv-tabs button { padding:7px 9px; }
      .tv-clock span { display:none; }
      .tv-clock b { font-size:18px; }
      .tv-brand img { height:30px; }
      .tv-brand h1 { font-size:20px; }
      .tv-btn .lbl { display:none; }
      .tv-btn, .tv-select { padding:0 7px; }
      .tv { padding:14px 16px; }
      .tv-tabs button { padding:7px 8px; font-size:12.5px; }
      .tv-controls { gap:4px; }
      .ov-grid { grid-template-columns:repeat(2, minmax(0, 1fr)); }
      .ov-main { grid-template-columns:1fr; }
    }
    @media (max-width: 700px) {
      body { padding:8px; }
      .tv { padding:14px; }
      .tv-head { flex-direction:column; align-items:flex-start; flex-wrap:wrap; position:static; margin-top:0; }
      .tv-right { flex-wrap:wrap; }
      .tv-clock span { display:inline; }
      .tv-clock { text-align:left; }
      .tv-right { width:100%; }
      .tv-tabs { max-width:100%; overflow-x:auto; }
      .tv-tabs button { padding:7px 9px; white-space:nowrap; }
      .tv-controls { flex-wrap:wrap; }
      .ov-grid { grid-template-columns:1fr 1fr; gap:10px; }
      .ct-grid { grid-template-columns:1fr; }
      .ct-stats { grid-template-columns:1fr 1fr; }
      .lb td, .lb th { padding:8px; font-size:14px; }
      .lb .hide-sm { display:none; }
      .act th, .act td { padding:9px 8px; font-size:14px; }
    }
  </style>
</head>
<body>
  <div class="tv-banner" id="tvBanner"><i class="fa fa-exclamation-triangle"></i> Connection lost. Showing the last numbers; retrying...</div>
  <div class="cele" id="tvCele" aria-live="assertive"></div>

  <div class="tv">
    <div class="tv-head">
      <div class="tv-brand">
        <img src="<?php echo base_url(); ?>assets/logo.png" alt="">
        <div>
          <h1>Live Dashboard</h1>
        </div>
      </div>
      <div class="tv-right">
        <div class="tv-tabs" id="tvTabs" role="tablist"></div>
        <div class="tv-controls">
          <span class="tv-updated" id="tvUpdated">Loading...</span>
          <select class="tv-select" id="tvRate" title="Refresh every" aria-label="Refresh every">
            <option value="10000" selected>10s</option>
            <option value="30000">30s</option>
            <option value="60000">1m</option>
            <option value="0">Manual</option>
          </select>
          <button type="button" class="tv-btn" id="tvRefresh" title="Refresh now" aria-label="Refresh now"><i class="fa fa-refresh"></i></button>
          <button type="button" class="tv-btn" id="tvPlay" title="Pause slides"><i class="fa fa-pause"></i></button>
          <button type="button" class="tv-btn" id="tvTheme" title="Dark / light"><i class="fa fa-moon-o"></i></button>
          <button type="button" class="tv-btn" id="tvFull" title="Full screen"><i class="fa fa-arrows-alt"></i></button>
          <a class="tv-btn" id="tvLogout" href="<?php echo base_url('manager/logout'); ?>" title="Log out" onclick="return confirm('Log out of this screen?');" style="text-decoration:none;"><i class="fa fa-sign-out"></i><span class="lbl"> Logout</span></a>
        </div>
        <div class="tv-clock"><b id="tvTime">--:--</b><span id="tvDate"></span></div>
      </div>
    </div>

    <!-- 1. Overview -->
    <section class="tv-slide" data-slide="overview" data-title="Overview">
      <div class="ov-grid">
        <div class="tv-panel kpi"><span class="kpi-label">Sales this month</span><b class="kpi-value" id="kSales">-</b><span class="kpi-sub" id="kSalesSub"></span></div>
        <div class="tv-panel kpi"><span class="kpi-label">Orders this month</span><b class="kpi-value" id="kOrders">-</b><span class="kpi-sub" id="kOrdersSub"></span></div>
        <div class="tv-panel kpi"><span class="kpi-label">New leads this month</span><b class="kpi-value" id="kLeads">-</b><span class="kpi-sub" id="kLeadsSub"></span></div>
        <div class="tv-panel kpi"><span class="kpi-label">Calls today</span><b class="kpi-value" id="kCalls">-</b><span class="kpi-sub" id="kCallsSub"></span></div>
      </div>
      <div class="ov-main">
        <div class="ov-col">
          <div class="tv-panel">
            <h3><span>Monthly target</span><span id="tgtPct"></span></h3>
            <div class="tgt-row"><b id="tgtDone">-</b><span id="tgtOf"></span></div>
            <div class="bar"><i id="tgtBar" style="width:0"></i></div>
          </div>
          <div class="tv-panel chart"><h3><span id="trendTitle">Sales, last 7 days</span></h3><div id="trendChart"></div></div>
        </div>
        <div class="ov-col">
          <div class="tv-panel" style="flex:1;"><h3><span>Latest sales</span></h3><div class="feed" id="feed"></div></div>
          <div class="tv-panel"><h3><span>Top services this month</span></h3><div id="prodList"></div></div>
        </div>
      </div>
    </section>

    <!-- 2. Leaderboard -->
    <section class="tv-slide" data-slide="leaders" data-title="Leaderboard">
      <div class="tv-slide-title"><i class="fa fa-trophy"></i> Leaderboard &middot; <span class="js-month"></span></div>
      <table class="lb">
        <thead><tr><th>#</th><th>Name</th><th class="r">Sales (AED)</th><th class="r hide-sm">vs last month</th><th class="hide-sm">Target</th><th class="r">Orders</th><th class="r hide-sm">Calls today</th></tr></thead>
        <tbody id="lbBody"></tbody>
      </table>
    </section>

    <!-- 3. Centers -->
    <section class="tv-slide" data-slide="centers" data-title="Centers">
      <div class="tv-slide-title"><i class="fa fa-building"></i> Centers &middot; <span class="js-month"></span></div>
      <div class="ct-grid" id="ctGrid"></div>
    </section>

    <!-- 4. By Center (only when the screen has 2 or more centers) -->
    <section class="tv-slide is-off" data-slide="bycenter" data-title="By Center">
      <div class="tv-slide-title"><i class="fa fa-th-large"></i> Activity by center</div>
      <div class="bc-grid" id="bcGrid"></div>
    </section>

    <!-- 5. Activity -->
    <section class="tv-slide" data-slide="activity" data-title="Activity">
      <div class="tv-slide-title"><i class="fa fa-bar-chart"></i> <span id="actTitle">Company activity</span></div>
      <div class="tv-panel" style="padding:4px 8px;">
        <table class="act">
          <thead><tr><th>Activity</th><th>Today</th><th>7 days</th><th>This month</th><th>This quarter</th></tr></thead>
          <tbody id="actBody"></tbody>
        </table>
      </div>
    </section>
  </div>

<script>
(function () {
  var DATA_URL = '<?php echo base_url('manager/live/ajax_presentation_data'); ?>';
  var SLIDE_MS = 20000;
  var store = { get: function (k) { try { return localStorage.getItem(k); } catch (e) { return null; } }, set: function (k, v) { try { localStorage.setItem(k, v); } catch (e) {} } };
  var data = null, lastSaleId = null, prevVals = {};
  var refreshTimer = null, slideTimer = null, slideStart = 0, playing = true, cur = 0;

  function esc(s) { return $('<div>').text(s == null ? '' : String(s)).html(); }
  function money(v) { return 'AED ' + Math.round(Number(v) || 0).toLocaleString('en-US'); }
  function num(v) { return (Number(v) || 0).toLocaleString('en-US'); }
  function compact(v) { v = Number(v) || 0; if (v >= 1e6) return (v / 1e6).toFixed(1).replace(/\.0$/, '') + 'M'; if (v >= 1e3) return (v / 1e3).toFixed(1).replace(/\.0$/, '') + 'K'; return String(Math.round(v)); }
  function growth(g, label) {
    if (g === null || g === undefined) return '<span>No ' + label + ' to compare</span>';
    var up = g >= 0;
    return '<span class="' + (up ? 'up' : 'down') + '"><i class="fa fa-caret-' + (up ? 'up' : 'down') + '"></i> ' + Math.abs(g) + '%</span> vs ' + label;
  }
  function initials(n) { return String(n || '?').replace(/[^A-Za-z ]/g, ' ').trim().split(/\s+/).slice(0, 2).map(function (w) { return w.charAt(0).toUpperCase(); }).join('') || '?'; }
  function avatar(url, name) { return '<span class="av">' + (url ? '<img src="' + esc(url) + '" alt="">' : esc(initials(name))) + '</span>'; }
  // Set a value and flash it when it changed since the last refresh.
  function setVal(id, html) {
    var el = $('#' + id);
    if (prevVals[id] !== undefined && prevVals[id] !== html) { el.removeClass('flash'); void el[0].offsetWidth; el.addClass('flash'); }
    prevVals[id] = html;
    el.html(html);
  }

  /* ---------- Clock ---------- */
  function clock() {
    var d = new Date();
    $('#tvTime').text(d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }));
    $('#tvDate').text(d.toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }));
  }
  clock(); setInterval(clock, 1000);

  /* ---------- Render ---------- */
  function render(d) {
    var k = d.kpi;
    $('#tvMonth, .js-month').text(d.month);
    // Presentation users: the centers this screen shows.
    var scope = (d.scope && d.scope.length) ? d.scope.join(', ') : '';
    $('#actTitle').text(scope ? 'Activity · ' + scope : 'Company activity');
    setVal('kSales', money(k.sales_month));
    setVal('kSalesSub', k.sales_count + ' sale' + (k.sales_count === 1 ? '' : 's') + ' &middot; ' + growth(k.sales_growth, 'last month'));
    setVal('kOrders', num(k.orders_month));
    setVal('kOrdersSub', growth(k.orders_growth, 'last month'));
    setVal('kLeads', num(k.leads_month));
    setVal('kLeadsSub', growth(k.leads_growth, 'last month'));
    setVal('kCalls', num(k.calls_today));
    setVal('kCallsSub', num(k.calls_yesterday) + ' yesterday');

    if (k.target_month > 0) {
      var pct = Math.round(k.sales_month * 100 / k.target_month);
      setVal('tgtDone', money(k.sales_month));
      $('#tgtOf').text('of ' + money(k.target_month));
      $('#tgtPct').text(pct + '%');
      $('#tgtBar').css('width', Math.min(100, pct) + '%');
    } else {
      setVal('tgtDone', money(k.sales_month));
      $('#tgtOf').text('No target set for this month');
      $('#tgtPct').text('');
      $('#tgtBar').css('width', '0');
    }

    // 7-day bar chart
    var t = d.trend || [], max = 0, byCount = false;
    t.forEach(function (p) { if (p.value > max) max = p.value; });
    if (max === 0) { byCount = true; t = t.map(function (p) { return $.extend({}, p, { value: p.count }); }); t.forEach(function (p) { if (p.value > max) max = p.value; }); }
    $('#trendTitle').text(byCount ? 'Sales, last 7 days (number of sales)' : 'Sales, last 7 days (AED)');
    var W = 700, H = 220, base = 180, top = 26, bw = W / t.length, svg = '';
    t.forEach(function (p, i) {
      var h = max > 0 ? Math.max(p.value > 0 ? 4 : 0, (p.value / max) * (base - top)) : 0;
      var x = i * bw + bw * 0.2, w = bw * 0.6;
      svg += '<rect x="' + x + '" y="' + (base - 2) + '" width="' + w + '" height="2" fill="var(--track)" />';
      svg += '<rect x="' + x + '" y="' + (base - h) + '" width="' + w + '" height="' + h + '" rx="6" fill="' + (p.today ? 'var(--brand2)' : 'var(--brand)') + '" opacity="' + (p.today ? 1 : .8) + '" />';
      svg += '<text x="' + (x + w / 2) + '" y="' + (base - h - 8) + '" text-anchor="middle" font-size="15" font-weight="800" fill="var(--text)">' + (byCount ? p.value : compact(p.value)) + '</text>';
      svg += '<text x="' + (x + w / 2) + '" y="' + (base + 24) + '" text-anchor="middle" font-size="14" font-weight="700" fill="var(--muted)">' + esc(p.today ? 'Today' : p.label) + '</text>';
    });
    $('#trendChart').html('<svg viewBox="0 0 ' + W + ' ' + H + '" preserveAspectRatio="xMidYMid meet" role="img" aria-label="Sales of the last 7 days">' + svg + '</svg>');

    // Top services
    var pr = d.products || [], pmax = 0, ph = '';
    pr.forEach(function (p) { if (p.value > pmax) pmax = p.value; });
    pr.forEach(function (p) {
      var w = pmax > 0 ? Math.max(3, p.value * 100 / pmax) : 100;
      ph += '<div class="prod-row"><span class="n">' + esc(p.name) + '</span><div class="bar small"><i style="width:' + w + '%"></i></div><span class="v">' + (p.value > 0 ? money(p.value) : p.count + ' sale' + (p.count > 1 ? 's' : '')) + '</span></div>';
    });
    $('#prodList').html(ph || '<div class="tv-empty">No sales this month yet.</div>');

    // Latest sales (+ "new sale" toast)
    var fh = '', newest = d.latest && d.latest.length ? d.latest[0] : null;
    (d.latest || []).slice(0, 4).forEach(function (s) {
      var isNew = lastSaleId !== null && s.id > lastSaleId;
      fh += '<div class="feed-item' + (isNew ? ' is-new' : '') + '">' + avatar(s.avatar, s.rep) +
        '<div class="who"><b>' + esc(s.rep) + '</b><span>' + esc(s.service || 'Sale') + '</span></div>' +
        '<div class="amt">' + (s.amount > 0 ? money(s.amount) : '&nbsp;') + '<span>' + esc(s.when) + '</span></div></div>';
    });
    $('#feed').html(fh || '<div class="tv-empty">No sales yet.</div>');
    // Every new sale since the last refresh gets its own celebration, oldest first.
    if (lastSaleId !== null) {
      (d.latest || []).filter(function (s) { return s.id > lastSaleId; }).reverse().forEach(function (s) { celeQueue.push(s); });
      playCele();
    }
    if (newest) lastSaleId = Math.max(lastSaleId || 0, newest.id);

    // Leaderboard
    var lh = '';
    (d.leaderboard || []).forEach(function (u, i) {
      var tp = u.target_pct === null ? '<span style="color:var(--muted); font-size:13px;">No target</span>'
        : '<div class="bar small"><i style="width:' + Math.min(100, u.target_pct) + '%"></i></div><small>' + u.target_pct + '% of ' + money(u.target) + '</small>';
      var g = u.growth === null ? '<span style="color:var(--muted);">-</span>' : '<span class="' + (u.growth >= 0 ? 'up' : 'down') + '"><i class="fa fa-caret-' + (u.growth >= 0 ? 'up' : 'down') + '"></i> ' + Math.abs(u.growth) + '%</span>';
      lh += '<tr class="' + (i === 0 ? 'top1' : '') + '"><td><span class="rank">' + (i === 0 ? '<i class="fa fa-trophy"></i>' : (i + 1)) + '</span></td>' +
        '<td><div class="person">' + avatar(u.avatar, u.name) + '<div><b>' + esc(u.name) + '</b><span>' + esc(u.office || '') + '</span></div></div></td>' +
        '<td class="r">' + (u.sales_month > 0 ? Math.round(u.sales_month).toLocaleString('en-US') : '0') + '<br><small style="color:var(--muted); font-size:12px;">' + u.sales_count + ' sale' + (u.sales_count === 1 ? '' : 's') + '</small></td>' +
        '<td class="r hide-sm">' + g + '</td><td class="tp hide-sm">' + tp + '</td>' +
        '<td class="r">' + num(u.orders_month) + '</td><td class="r hide-sm">' + num(u.calls_today) + '</td></tr>';
    });
    $('#lbBody').html(lh || '<tr><td colspan="7"><div class="tv-empty">No activity this month yet.</div></td></tr>');

    // Centers
    var ch = '';
    (d.centers || []).forEach(function (c) {
      var pct = c.target > 0 ? Math.round(c.sales_month * 100 / c.target) : null;
      ch += '<div class="tv-panel ct-card"><h4>' + esc(c.name) + '</h4><div class="ct-staff">' + c.staff + ' staff</div>' +
        '<div class="tgt-row"><b>' + money(c.sales_month) + '</b><span>' + (pct === null ? 'No target' : pct + '% of ' + money(c.target)) + '</span></div>' +
        '<div class="bar"><i style="width:' + (pct === null ? 0 : Math.min(100, pct)) + '%"></i></div>' +
        '<div class="ct-stats"><div><span>Sales</span><b>' + num(c.sales_count) + '</b></div><div><span>Orders</span><b>' + num(c.orders_month) + '</b></div>' +
        '<div><span>Leads</span><b>' + num(c.leads_month) + '</b></div><div><span>Calls today</span><b>' + num(c.calls_today) + '</b></div></div></div>';
    });
    $('#ctGrid').html(ch || '<div class="tv-empty">No centers are set on the users yet.</div>');

    // Activity
    var ah = '';
    (d.company_stats || []).forEach(function (s) {
      var f = function (v) { return isNaN(v) ? esc(v).replace(/\(AED/, '<br><small style="color:var(--muted); font-size:13px;">(AED') : num(v); };
      ah += '<tr><td>' + esc(s.metric) + '</td><td class="today">' + f(s.today) + '</td><td>' + f(s.seven_days) + '</td><td>' + f(s.month) + '</td><td>' + f(s.quarter) + '</td></tr>';
    });
    $('#actBody').html(ah);

    // By Center
    var cs = d.center_stats || [], bh = '';
    cs.forEach(function (c) {
      var rows = '';
      (c.stats || []).forEach(function (s) {
        var f = function (v) { return isNaN(v) ? esc(v).replace(/\(AED/, '<br><small style="color:var(--muted); font-size:12px;">(AED') : num(v); };
        rows += '<tr><td>' + esc(s.metric) + '</td><td class="today">' + f(s.today) + '</td><td>' + f(s.seven_days) + '</td><td>' + f(s.month) + '</td><td>' + f(s.quarter) + '</td></tr>';
      });
      bh += '<div class="tv-panel" style="padding:8px;"><h4>' + esc(c.name) + '<small>' + c.staff + ' staff</small></h4><table class="act"><thead><tr><th>Activity</th><th>Today</th><th>7 days</th><th>Month</th><th>Quarter</th></tr></thead><tbody>' + rows + '</tbody></table></div>';
    });
    $('#bcGrid').html(bh);
    // 3 or more centers: all side by side (two rows when more than 4).
    $('#bcGrid').css('grid-template-columns', cs.length > 2 && window.innerWidth > 700 ? 'repeat(' + (cs.length > 4 ? Math.ceil(cs.length / 2) : cs.length) + ', minmax(0, 1fr))' : '');
    setOff('bycenter', cs.length < 2);
    fit();
  }

  /* ---------- New sale celebration (20 seconds each) ---------- */
  var celeQueue = [], celeBusy = false;
  // Celebration sound, made in the browser (no sound file). Browsers only allow sound
  // after the screen has been clicked or a key pressed once, so the first touch unlocks it.
  var audio = null;
  function audioCtx() {
    if (!audio) { var A = window.AudioContext || window.webkitAudioContext; if (!A) return null; audio = new A(); }
    if (audio.state === 'suspended') audio.resume();
    return audio;
  }
  $(document).one('click keydown touchstart', function () { audioCtx(); });
  function saleSound() {
    var ac = audioCtx(); if (!ac) return;
    var t = ac.currentTime + 0.05, master = ac.createGain();
    master.gain.value = 0.35; master.connect(ac.destination);
    function note(freq, at, len, type, vol) {
      var o = ac.createOscillator(), g = ac.createGain();
      o.type = type; o.frequency.value = freq;
      g.gain.setValueAtTime(0.0001, at);
      g.gain.exponentialRampToValueAtTime(vol, at + 0.02);
      g.gain.exponentialRampToValueAtTime(0.0001, at + len);
      o.connect(g); g.connect(master); o.start(at); o.stop(at + len + 0.05);
    }
    // Rising fanfare, then a bright chord with a bell on top.
    [523.25, 659.25, 783.99].forEach(function (f, i) { note(f, t + i * 0.11, 0.25, 'triangle', 0.6); });
    [523.25, 659.25, 783.99, 1046.5].forEach(function (f) { note(f, t + 0.36, 1.1, 'triangle', 0.45); });
    note(2093, t + 0.36, 1.4, 'sine', 0.25);
    note(1568, t + 0.5, 1.2, 'sine', 0.18);
  }
  function playCele() {
    if (celeBusy || !celeQueue.length) return;
    celeBusy = true;
    var s = celeQueue.shift(), box = $('#tvCele');
    var bits = '', colors = ['#f59e0b', '#22c55e', '#3b82f6', '#ef4444', '#a855f7', '#fbbf24', '#ffffff'];
    for (var i = 0; i < 70; i++) {
      bits += '<i class="cele-bit" style="left:' + (Math.random() * 100).toFixed(1) + '%; background:' + colors[i % colors.length] +
        '; animation-duration:' + (2.6 + Math.random() * 2.2).toFixed(2) + 's; animation-delay:' + (Math.random() * 3).toFixed(2) + 's; transform:rotate(' + Math.round(Math.random() * 360) + 'deg);"></i>';
    }
    box.html(bits + '<div class="cele-card"><span class="cele-tag"><i class="fa fa-trophy"></i> New sale!</span>' +
      '<div class="cele-photo">' + avatar(s.avatar, s.rep) + '</div>' +
      '<div class="cele-name">' + esc(s.rep) + '</div>' +
      (s.service ? '<div class="cele-sub">' + esc(s.service) + '</div>' : '') +
      (s.amount > 0 ? '<div class="cele-amt">' + money(s.amount) + '</div>' : '') + '</div>')
      .removeClass('out').addClass('show');
    saleSound();
    setTimeout(function () {
      box.addClass('out');
      setTimeout(function () { box.removeClass('show out').empty(); celeBusy = false; playCele(); }, 400);
    }, 19600);
  }
  // A click hides the current one early.
  $('#tvCele').on('click', function () { $(this).removeClass('show out').empty(); });

  /* ---------- Data ---------- */
  function load() {
    $('#tvRefresh i').addClass('fa-spin');
    $.ajax({ url: DATA_URL, dataType: 'json', cache: false, timeout: 15000 })
      .done(function (d) {
        if (!d || !d.status) { $('#tvBanner').show(); return; }
        $('#tvBanner').hide();
        data = d;
        render(d);
        $('#tvUpdated').text('Updated ' + new Date().toLocaleTimeString('en-US'));
      })
      .fail(function (x) {
        if (x.status === 403 || x.status === 401) { location.href = '<?php echo base_url('manager/live'); ?>'; return; }
        $('#tvBanner').show();
      })
      .always(function () { $('#tvRefresh i').removeClass('fa-spin'); });
  }
  function setRate() {
    if (refreshTimer) clearInterval(refreshTimer);
    var ms = parseInt($('#tvRate').val(), 10);
    store.set('liveTvRate', String(ms));
    refreshTimer = ms > 0 ? setInterval(load, ms) : null;
  }
  var savedRate = store.get('liveTvRate');
  if (savedRate && $('#tvRate option[value="' + savedRate + '"]').length) $('#tvRate').val(savedRate);
  $('#tvRate').on('change', setRate);
  $('#tvRefresh').on('click', load);

  /* ---------- Slides ---------- */
  var slides = $('.tv-slide');
  slides.each(function () { $(this).wrapInner('<div class="fit"></div>'); });
  slides.each(function (i) {
    $('#tvTabs').append('<button type="button" data-i="' + i + '">' + $(this).data('title') + '</button>');
    if ($(this).hasClass('is-off')) $('#tvTabs button').last().hide();
  });
  function setOff(name, off) {
    var idx = slides.index(slides.filter('[data-slide="' + name + '"]'));
    slides.eq(idx).toggleClass('is-off', off);
    $('#tvTabs button').eq(idx).toggle(!off);
    if (off && cur === idx) show(cur + 1);
  }
  function show(i, dir) {
    dir = dir || 1;
    cur = (i + slides.length) % slides.length;
    for (var n = 0; n < slides.length && slides.eq(cur).hasClass('is-off'); n++) cur = (cur + dir + slides.length) % slides.length;
    slides.removeClass('active').eq(cur).addClass('active');
    $('#tvTabs button').removeClass('active').eq(cur).addClass('active');
    slideStart = Date.now();
    fit();
  }
  function tick() {
    if (!playing) return;
    if (Date.now() - slideStart >= SLIDE_MS) show(cur + 1);
  }
  $('#tvTabs').on('click', 'button', function () { show(+$(this).data('i')); });
  $('#tvPlay').on('click', function () {
    playing = !playing;
    slideStart = Date.now();
    $(this).attr('title', playing ? 'Pause slides' : 'Play slides').find('i').attr('class', 'fa ' + (playing ? 'fa-pause' : 'fa-play'));
  });
  // Shrink the shown slide until it fits the screen (wider layout first, then smaller).
  function fit() {
    var sl = slides.eq(cur), box = sl.children('.fit');
    box.css({ transform: '', width: '' });
    if (window.innerWidth <= 700 || !sl.hasClass('active')) return;
    var avail = sl[0].clientHeight, k = 1;
    if (!avail) return;
    if (box[0].offsetHeight <= avail + 1) return;
    // The wider the box, the shorter it gets: settle on a scale, then make sure it fits.
    for (var n = 0; n < 6; n++) {
      var k2 = Math.max(0.4, Math.min(1, avail / box[0].offsetHeight));
      if (Math.abs(k2 - k) < 0.01) break;
      k = k2;
      box.css({ transform: 'scale(' + k + ')', width: (100 / k) + '%' });
    }
    var h = box[0].offsetHeight;
    if (h * k > avail + 1) box.css('transform', 'scale(' + Math.max(0.4, avail / h) + ')');
  }
  var fitTimer = null;
  $(window).on('resize', function () { clearTimeout(fitTimer); fitTimer = setTimeout(fit, 150); });
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(fit);
  show(0);
  slideTimer = setInterval(tick, 200);

  /* ---------- Theme ---------- */
  function setTheme(dark) {
    $('body').toggleClass('tv-dark', dark);
    $('#tvTheme i').attr('class', 'fa ' + (dark ? 'fa-sun-o' : 'fa-moon-o'));
    store.set('liveTvTheme', dark ? 'dark' : 'light');
  }
  setTheme(store.get('liveTvTheme') === 'dark');
  $('#tvTheme').on('click', function () { setTheme(!$('body').hasClass('tv-dark')); });

  /* ---------- Full screen: keep the screen awake, hide the controls when the mouse rests ---------- */
  var wakeLock = null, idleTimer = null;
  function keepAwake() {
    if ('wakeLock' in navigator && document.visibilityState === 'visible') {
      navigator.wakeLock.request('screen').then(function (l) { wakeLock = l; }).catch(function () {});
    }
  }
  $('#tvFull').on('click', function () {
    if (!document.fullscreenElement) { (document.documentElement.requestFullscreen || function () {}).call(document.documentElement); }
    else if (document.exitFullscreen) { document.exitFullscreen(); }
  });
  document.addEventListener('fullscreenchange', function () {
    var on = !!document.fullscreenElement;
    $('#tvFull i').attr('class', 'fa ' + (on ? 'fa-compress' : 'fa-arrows-alt'));
    setTimeout(fit, 200);
    if (on) { keepAwake(); armIdle(); } else { $('body').removeClass('tv-idle'); if (wakeLock) { wakeLock.release(); wakeLock = null; } }
  });
  document.addEventListener('visibilitychange', function () { if (document.fullscreenElement && document.visibilityState === 'visible') keepAwake(); });
  function armIdle() {
    $('body').removeClass('tv-idle');
    clearTimeout(idleTimer);
    if (document.fullscreenElement) idleTimer = setTimeout(function () { $('body').addClass('tv-idle'); }, 3000);
  }
  $(document).on('mousemove keydown touchstart', armIdle);
  // Keys: arrows change slide, space pauses.
  $(document).on('keydown', function (e) {
    if ($(e.target).is('select, input')) return;
    if (e.key === 'ArrowRight') show(cur + 1);
    else if (e.key === 'ArrowLeft') show(cur - 1, -1);
    else if (e.key === ' ') { e.preventDefault(); $('#tvPlay').click(); }
  });

  load();
  setRate();
})();
</script>
</body>
</html>
