<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>

<style>
html, body {
  height: 100% !important;
  min-height: 100% !important;
  overflow: hidden !important;
  margin: 0 !important;
  padding: 0 !important;
  background: #f2f2f7 !important;
}
.wrapper {
  height: 100vh !important;
  max-height: 100vh !important;
  overflow: hidden !important;
  display: flex !important;
  flex-direction: column !important;
  background: #f2f2f7 !important;
  margin: 0 !important;
  padding: 0 !important;
}
.main-sidebar, .left-side, .main-footer, .crm-footer {
  display: none !important;
}
.content-wrapper.glass-calling-wrapper {
  background: #f2f2f7 !important;
  position: relative !important;
  top: 0 !important;
  left: 0 !important;
  right: 0 !important;
  bottom: 0 !important;
  width: 100% !important;
  max-width: 100% !important;
  flex: 1 1 0% !important;
  height: auto !important;
  min-height: 0 !important;
  max-height: 100% !important;
  padding: 10px 14px !important;
  color: #1d1d1f;
  font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text", "SF Pro Display", "Helvetica Neue", Arial, sans-serif;
  box-sizing: border-box !important;
  overflow: hidden !important;
  display: flex !important;
  flex-direction: row !important;
  gap: 12px !important;
  align-items: stretch !important;
  justify-content: flex-start !important;
  margin: 0 !important;
  z-index: 800 !important;
}

.crm-dialer-dock {
  width: 64px;
  flex-shrink: 0;
  height: 100% !important;
  max-height: 100% !important;
  background: #ffffff;
  border: 1px solid #e5e5ea;
  border-radius: 14px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
  padding: 10px 6px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  box-sizing: border-box !important;
  overflow: visible !important;
  z-index: 1000;
  position: relative;
}

.dock-item-wrapper {
  position: relative;
  width: 100%;
  display: flex;
  justify-content: center;
}

.crm-dock-btn {
  width: 54px;
  height: 52px;
  border-radius: 12px;
  background: transparent;
  border: none;
  color: #636366;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 4px;
  cursor: pointer;
  transition: all 0.14s ease;
  position: relative;
  text-decoration: none !important;
}

.crm-dock-btn:hover {
  background: #f2f2f7;
  color: #0072f0;
}

.crm-dock-btn.active {
  background: #0072f0 !important;
  color: #ffffff !important;
  box-shadow: 0 3px 12px rgba(0, 114, 240, 0.35);
}

.crm-dock-btn i {
  font-size: 18px;
}

.crm-dock-btn .dock-lbl {
  font-size: 10px;
  font-weight: 600;
  letter-spacing: 0.1px;
}

.dock-flyout-menu {
  display: none;
  position: absolute;
  left: 58px;
  top: 0;
  min-width: 190px;
  background: #ffffff;
  border: 1px solid #e5e5ea;
  border-radius: 12px;
  padding: 6px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
  z-index: 10000;
}

.dock-flyout-menu::before {
  content: '';
  position: absolute;
  top: -10px;
  bottom: -10px;
  left: -20px;
  width: 25px;
  background: transparent;
}

.dock-item-wrapper:hover .dock-flyout-menu {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.dock-flyout-item {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  border-radius: 8px;
  color: #1d1d1f;
  font-size: 13px;
  font-weight: 500;
  text-decoration: none !important;
  transition: all 0.12s ease;
  background: transparent;
}

.dock-flyout-item:hover {
  background: #f2f2f7;
  color: #0072f0;
}

.apple-segmented-group {
  display: inline-flex;
  align-items: center;
  background: #ebebf0;
  border-radius: 9px;
  padding: 2px;
  gap: 2px;
  border: none;
}

.apple-segment-btn {
  padding: 5px 12px;
  border-radius: 7px;
  background: transparent;
  border: none;
  color: #636366;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.14s ease;
}

.apple-segment-btn:hover {
  color: #1d1d1f;
}

.apple-segment-btn.active {
  background: #ffffff !important;
  color: #1d1d1f !important;
  font-weight: 600;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
}

#middleContentArea {
  display: none;
  flex: 1 1 0% !important;
  min-width: 0 !important;
  height: 100% !important;
  max-height: 100% !important;
  background: #ffffff;
  border: 1px solid #e5e5ea;
  border-radius: 14px;
  box-shadow: 0 2px 14px rgba(0, 0, 0, 0.03);
  box-sizing: border-box !important;
  flex-direction: column !important;
  overflow: hidden !important;
  z-index: 900;
}

.middle-header {
  padding: 14px 20px;
  background: #ffffff;
  border-bottom: 1px solid #f2f2f7;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-shrink: 0;
}

.middle-header-left {
  display: flex;
  align-items: center;
  gap: 10px;
}

.middle-title {
  font-size: 18px;
  font-weight: 700;
  color: #1d1d1f;
  margin: 0;
  letter-spacing: -0.02em;
}

.btn-close-middle {
  background: #f2f2f7;
  border: none;
  color: #8e8e93;
  border-radius: 50%;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.14s ease;
  font-size: 13px;
}

.btn-close-middle:hover {
  background: #e5e5ea;
  color: #1d1d1f;
}

.middle-body {
  flex: 1;
  min-height: 0;
  overflow-y: auto;
  padding: 18px 20px;
  box-sizing: border-box !important;
  background: #ffffff;
}

.middle-filter-bar {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 16px;
}

.dialer-filter-select {
  background: #f2f2f7;
  border: 1px solid transparent;
  border-radius: 8px;
  color: #1d1d1f;
  font-size: 12.5px;
  font-weight: 500;
  padding: 8px 14px;
  outline: none;
  transition: all 0.14s ease;
}

.dialer-search-input {
  flex: 1;
  background: #f2f2f7;
  border: 1px solid transparent;
  border-radius: 8px;
  color: #1d1d1f;
  font-size: 13px;
  font-weight: 400;
  padding: 8px 14px;
  outline: none;
  transition: all 0.14s ease;
}

.dialer-search-input:focus, .dialer-filter-select:focus {
  background: #ffffff;
  border-color: #0072f0;
  box-shadow: 0 0 0 3px rgba(0, 114, 240, 0.12);
}

.queue-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 12.5px;
}

.queue-table th {
  background: #fafafc;
  color: #8e8e93;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 11px;
  letter-spacing: 0.5px;
  padding: 10px 14px;
  border-bottom: 1px solid #e5e5ea;
  text-align: left;
}

.queue-table td {
  padding: 12px 14px;
  border-bottom: 1px solid #f2f2f7;
  color: #1d1d1f;
  vertical-align: middle;
}

.queue-table tr:hover td {
  background: #fafafc;
}

.queue-table tr.active-row td {
  background: #f0f7ff !important;
}

.apple-quick-call-btn {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #34c759;
  color: #ffffff;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  box-shadow: 0 2px 6px rgba(52, 199, 89, 0.35);
  transition: transform 0.1s ease, background 0.1s ease;
}

.apple-quick-call-btn:hover {
  background: #30b753;
  transform: scale(1.05);
}

.apple-quick-call-btn:active {
  transform: scale(0.95);
}

.btn-edit-row {
  background: #f2f2f7;
  color: #1d1d1f;
  border: none;
  border-radius: 7px;
  padding: 6px 12px;
  font-size: 11.5px;
  font-weight: 600;
  cursor: pointer;
  margin-left: 6px;
  transition: all 0.12s ease;
}

.btn-edit-row:hover {
  background: #e5e5ea;
}

.colleague-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 16px;
  border-radius: 10px;
  border: 1px solid #f2f2f7;
  background: #ffffff;
  margin-bottom: 8px;
  transition: all 0.12s ease;
}

.colleague-card:hover {
  background: #fafafc;
  border-color: #e5e5ea;
}

.colleague-avatar {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: #f2f2f7;
  color: #1d1d1f;
  font-size: 13px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.sheet-stat-card {
  background: #fafafc;
  border: 1px solid #f2f2f7;
  border-radius: 12px;
  padding: 14px 16px;
  text-align: center;
}

.sheet-stat-val {
  font-size: 24px;
  font-weight: 700;
  color: #1d1d1f;
  line-height: 1.1;
  font-variant-numeric: tabular-nums;
}

.sheet-stat-lbl {
  font-size: 11px;
  font-weight: 600;
  color: #8e8e93;
  text-transform: uppercase;
  letter-spacing: 0.4px;
  margin-top: 4px;
}

.sheet-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 12.5px;
  border: 1px solid #e5e5ea;
  border-radius: 10px;
  overflow: hidden;
}

.sheet-table th {
  background: #fafafc;
  color: #8e8e93;
  font-weight: 600;
  font-size: 11px;
  padding: 10px 14px;
  border-bottom: 1px solid #e5e5ea;
  text-align: left;
}

.sheet-table td {
  padding: 10px 14px;
  border-bottom: 1px solid #f2f2f7;
  color: #1d1d1f;
  font-weight: 500;
}

.sheet-table tr:last-child td {
  border-bottom: none;
}

.crm-dialer-panel {
  width: 300px;
  min-width: 280px;
  max-width: 310px;
  flex-shrink: 0;
  height: 100% !important;
  max-height: 100% !important;
  background: #ffffff;
  border: 1px solid #e5e5ea;
  border-radius: 14px;
  box-shadow: 0 2px 14px rgba(0, 0, 0, 0.03);
  box-sizing: border-box !important;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  z-index: 950;
  margin-left: auto;
}

.dialer-window-header {
  padding: 14px 18px 10px 18px;
  background: #ffffff;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.dialer-window-header-icons {
  display: flex;
  align-items: center;
  gap: 12px;
  color: #8e8e93;
  font-size: 14px;
}

.dialer-top-display {
  padding: 10px 18px 16px 18px;
  background: #ffffff;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.dialer-active-chip {
  display: none;
  font-size: 12px;
  font-weight: 600;
  color: #0072f0;
  align-items: center;
  gap: 6px;
  max-width: 90%;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.dialer-num-input {
  width: 100%;
  border: none;
  background: transparent;
  color: #1d1d1f;
  font-size: 32px;
  font-weight: 500;
  text-align: center;
  outline: none;
  letter-spacing: -0.02em;
  font-variant-numeric: tabular-nums;
  padding: 4px 0;
}

.dialer-num-input::placeholder {
  color: #c7c7cc;
  font-weight: 400;
  font-size: 18px;
}

.dialer-status-sub {
  font-size: 12px;
  font-weight: 500;
  color: #8e8e93;
  min-height: 16px;
}

.dialer-suggestions-popover {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: #ffffff;
  border: 1px solid #e5e5ea;
  border-radius: 12px;
  box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
  max-height: 220px;
  overflow-y: auto;
  z-index: 10000;
  margin-top: 6px;
  padding: 4px;
  text-align: left;
}

.dialer-suggestion-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 10px;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.12s ease;
}

.dialer-suggestion-row:hover {
  background: #f2f2f7;
}

.dialer-suggestion-avatar {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: #f2f2f7;
  color: #1d1d1f;
  font-size: 11px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.dialer-keypad-container {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 6px 16px;
}

.dialer-keypad-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 8px 14px;
  width: 100%;
  max-width: 250px;
}

.dialer-key-btn {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: #f2f2f7;
  border: none;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: transform 0.08s ease, background 0.08s ease;
  user-select: none;
  margin: 0 auto;
}

.dialer-key-btn:hover {
  background: #e5e5ea;
}

.dialer-key-btn:active {
  background: #d1d1d6;
  transform: scale(0.94);
}

.dialer-key-digit {
  font-size: 22px;
  font-weight: 500;
  color: #1d1d1f;
  line-height: 1;
}

.dialer-key-sub {
  font-size: 8.5px;
  font-weight: 600;
  color: #8e8e93;
  letter-spacing: 1px;
  text-transform: uppercase;
  margin-top: 1px;
  line-height: 1;
}

.dialer-bottom-actions {
  padding: 8px 20px 12px 20px;
  background: #ffffff;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.keypad-tool-btn {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: transparent;
  border: none;
  color: #8e8e93;
  font-size: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.12s ease;
}

.keypad-tool-btn:hover {
  background: #f2f2f7;
  color: #1d1d1f;
}

.btn-green-call {
  width: 58px;
  height: 58px;
  border-radius: 50%;
  background: #34c759;
  border: none;
  color: #ffffff;
  font-size: 22px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: transform 0.12s ease, background 0.12s ease;
  box-shadow: 0 4px 14px rgba(52, 199, 89, 0.4);
}

.btn-green-call:hover {
  background: #30b753;
  transform: scale(1.04);
}

.btn-green-call:active {
  transform: scale(0.95);
}

.btn-green-call.btn-connected {
  background: #ff3b30 !important;
  box-shadow: 0 4px 14px rgba(255, 59, 48, 0.4) !important;
}

.btn-green-call.btn-calling {
  background: #ff9500 !important;
  box-shadow: 0 4px 14px rgba(255, 149, 0, 0.4) !important;
}

.dialer-toast {
  position: fixed;
  bottom: 24px;
  right: 24px;
  background: rgba(29, 29, 31, 0.92);
  backdrop-filter: blur(10px);
  color: #ffffff;
  padding: 10px 18px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 500;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18);
  display: none;
  z-index: 99999;
}
</style>

<div class="content-wrapper glass-calling-wrapper">

  <div class="crm-dialer-dock">

    <div class="dock-item-wrapper">
      <button type="button" class="crm-dock-btn active" id="dockBtnCallHome" title="Dialer Home" onclick="openDialerHome()">
        <i class="fa fa-phone"></i>
      </button>
    </div>

    <div class="dock-item-wrapper">
      <button type="button" class="crm-dock-btn" id="dockBtnTeam" title="Team Colleagues" onclick="openTeamColleagues()">
        <i class="fa fa-users"></i>
        <span class="dock-lbl">Team</span>
      </button>
    </div>

    <div class="dock-item-wrapper">
      <button type="button" class="crm-dock-btn" id="dockBtnFollowup" title="Follow-up" onclick="openMenuQueue('followup', 'today', 'Today Follow-ups')">
        <i class="fa fa-calendar-check-o"></i>
        <span class="dock-lbl">Follow-up</span>
      </button>
      <div class="dock-flyout-menu" id="flyoutFollowup">
        <a href="javascript:void(0)" class="dock-flyout-item" onclick="openMenuQueue('followup', 'missed', 'Missed Today')">
          <span>Missed Today</span>
        </a>
        <a href="javascript:void(0)" class="dock-flyout-item" onclick="openMenuQueue('followup', 'today', 'Today Follow-ups')">
          <span>Today</span>
        </a>
        <a href="javascript:void(0)" class="dock-flyout-item" onclick="openMenuQueue('followup', 'last7', 'Last 7 Days')">
          <span>Last Seven Days</span>
        </a>
        <a href="javascript:void(0)" class="dock-flyout-item" onclick="openMenuQueue('followup', 'next7', 'Next 7 Days')">
          <span>Next Seven Days</span>
        </a>
        <a href="javascript:void(0)" class="dock-flyout-item" onclick="openFollowupAllFeaturesMode()">
          <span>All Features</span>
        </a>
      </div>
    </div>

    <div class="dock-item-wrapper">
      <button type="button" class="crm-dock-btn" id="dockBtnDatabase" title="Database" onclick="openAutoDialerMode()">
        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; margin:0 auto 2px auto;">
          <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
          <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
          <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
        </svg>
        <span class="dock-lbl">Database</span>
      </button>
      <div class="dock-flyout-menu" id="flyoutDatabase">
        <a href="javascript:void(0)" class="dock-flyout-item" onclick="openAutoDialerMode()">
          <i class="fa fa-play-circle" style="margin-right:8px; color:#0072f0;"></i>
          <span>Auto Dialer</span>
        </a>
        <a href="javascript:void(0)" class="dock-flyout-item" onclick="openManualDialerMode()">
          <i class="fa fa-keyboard-o" style="margin-right:8px; color:#64748b;"></i>
          <span>Manual Dialer</span>
        </a>
      </div>
    </div>

    <div class="dock-item-wrapper">
      <button type="button" class="crm-dock-btn" id="dockBtnLeads" title="Leads" onclick="openLeadsDialerMode()">
        <i class="fa fa-bullhorn"></i>
        <span class="dock-lbl">Leads</span>
      </button>
    </div>

    <div class="dock-item-wrapper" style="margin-top:auto;">
      <button type="button" class="crm-dock-btn" id="dockBtnLogs" title="Logs" onclick="openMenuLogs()">
        <i class="fa fa-list-alt"></i>
        <span class="dock-lbl">Logs</span>
      </button>
    </div>

  </div>

  <div id="middleContentArea">

    <div class="middle-header">
      <div class="middle-header-left">
        <h2 class="middle-title" id="middleAreaTitle">Queue Records</h2>
      </div>

      <div class="apple-segmented-group" id="subnavFollowup" style="display:none;">
        <button type="button" class="apple-segment-btn active" data-filter="today" onclick="openMenuQueue('followup', 'today', 'Today Follow-ups')">Today</button>
        <button type="button" class="apple-segment-btn" data-filter="missed" onclick="openMenuQueue('followup', 'missed', 'Missed Follow-ups')">Missed</button>
        <button type="button" class="apple-segment-btn" data-filter="last7" onclick="openMenuQueue('followup', 'last7', 'Last 7 Days')">Last 7d</button>
        <button type="button" class="apple-segment-btn" data-filter="next7" onclick="openMenuQueue('followup', 'next7', 'Next 7 Days')">Next 7d</button>
        <button type="button" class="apple-segment-btn" data-filter="all" onclick="openFollowupAllFeaturesMode()">All Features</button>
      </div>

      <div class="apple-segmented-group" id="subnavLogsPeriod" style="display:none;">
        <button type="button" class="apple-segment-btn active" data-period="day" onclick="changeLogsPeriod('day')">Day</button>
        <button type="button" class="apple-segment-btn" data-period="week" onclick="changeLogsPeriod('week')">Week</button>
        <button type="button" class="apple-segment-btn" data-period="month" onclick="changeLogsPeriod('month')">Month</button>
        <button type="button" class="apple-segment-btn" data-period="year" onclick="changeLogsPeriod('year')">Year</button>
        <button type="button" class="apple-segment-btn" data-period="all" onclick="changeLogsPeriod('all')">All</button>
      </div>

      <div>
        <button type="button" class="btn-close-middle" id="btnCloseMiddleArea" onclick="closeMiddleArea()" title="Close">
          <i class="fa fa-times"></i>
        </button>
      </div>
    </div>

    <div class="middle-body">

      <div id="middleViewTeam" style="display:none;">
        <div class="middle-filter-bar">
          <input type="text" id="inputTeamSearch" class="dialer-search-input" placeholder="Type at least 3 letters to search colleague or department..." oninput="filterTeamCards()">
        </div>
        <div id="teamColleaguesContainer">
          <div id="teamSearchPrompt" style="text-align:center; padding:50px 20px; color:#8e8e93; font-size:13px; font-weight:500;">Type at least 3 letters to search colleagues or departments</div>
        </div>
      </div>

      <div id="middleViewList" style="display:none;">
        <div class="middle-filter-bar">
          <div id="statusFilterContainer" style="display:none;">
            <select id="selectQueueStatus" class="dialer-filter-select" onchange="reloadCurrentQueue()">
              <option value="all">All Statuses</option>
              <?php if(!empty($statuses)){
                foreach($statuses as $st){ ?>
                  <option value="<?php echo $st['id']; ?>"><?php echo htmlspecialchars($st['status']); ?></option>
              <?php } } ?>
            </select>
          </div>

          <input type="text" id="inputQueueSearch" class="dialer-search-input" placeholder="Search by name, phone or company..." onkeyup="filterQueueTable()">
        </div>

        <div style="overflow-x:auto;">
          <table class="queue-table" id="tableQueueRecords">
            <thead>
              <tr>
                <th>Company / Contact</th>
                <th>Phone Numbers</th>
                <th>Status</th>
                <th>Schedule</th>
                <th style="text-align:right;">Action</th>
              </tr>
            </thead>
            <tbody id="tbodyQueueRecords">
              <tr><td colspan="5" style="text-align:center; padding:25px; color:#8e8e93;">Loading records...</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <div id="middleViewDetails" style="display:none;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
          <h3 style="font-size:16px; font-weight:700; color:#1d1d1f; margin:0;" id="detailsHeaderName">Contact Details</h3>
          <button type="button" class="btn-edit-row" onclick="backToListFromDetails()"><i class="fa fa-arrow-left"></i> Back</button>
        </div>

        <form id="formRecordDetails">
          <input type="hidden" name="lead_id" id="editLeadId" value="0">
          <input type="hidden" name="call_log_id" id="editCallLogId" value="0">
          <input type="hidden" name="call_duration" id="editCallDuration" value="0">

          <div class="row">
            <div class="col-md-6">
              <div class="form-group" style="margin-bottom:12px;">
                <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Company Name</label>
                <input type="text" name="customer" id="editCustomer" class="form-control" style="border-radius:8px; border:1px solid #e5e5ea; font-size:13px; height:38px;">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group" style="margin-bottom:12px;">
                <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Point of Contact</label>
                <input type="text" name="poc" id="editPoc" class="form-control" style="border-radius:8px; border:1px solid #e5e5ea; font-size:13px; height:38px;">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group" style="margin-bottom:12px;">
                <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Primary Phone</label>
                <input type="text" name="contact1" id="editContact1" class="form-control" style="border-radius:8px; border:1px solid #e5e5ea; font-size:13px; height:38px;">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group" style="margin-bottom:12px;">
                <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Alt Phone</label>
                <input type="text" name="alt_contact" id="editAltContact" class="form-control" style="border-radius:8px; border:1px solid #e5e5ea; font-size:13px; height:38px;">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group" style="margin-bottom:12px;">
                <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Email Address</label>
                <input type="email" name="email" id="editEmail" class="form-control" style="border-radius:8px; border:1px solid #e5e5ea; font-size:13px; height:38px;">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group" style="margin-bottom:12px;">
                <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Authority</label>
                <select name="tl_authority" id="editAuthority" class="form-control" style="border-radius:8px; border:1px solid #e5e5ea; font-size:13px; height:38px;">
                  <option value="">Select Authority</option>
                  <option value="DED">DED</option>
                  <option value="DMCC">DMCC</option>
                  <option value="SHAMS">SHAMS</option>
                  <option value="IFZA">IFZA</option>
                  <option value="RAKEZ">RAKEZ</option>
                  <option value="Other">Other</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group" style="margin-bottom:12px;">
                <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Location</label>
                <input type="text" name="location" id="editLocation" class="form-control" style="border-radius:8px; border:1px solid #e5e5ea; font-size:13px; height:38px;">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group" style="margin-bottom:12px;">
                <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">DNCR Registry</label>
                <select name="dnd_status" id="editDndStatus" class="form-control" onchange="toggleDncrDateRow()" style="border-radius:8px; border:1px solid #e5e5ea; font-size:13px; height:38px;">
                  <option value="No">No (Clean)</option>
                  <option value="Yes">Yes (DND Active)</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row" id="wrapDncrDate" style="display:none;">
            <div class="col-md-12">
              <div class="form-group" style="margin-bottom:12px;">
                <label style="font-size:12px; font-weight:600; color:#dc2626; margin-bottom:4px; display:block;">DNCR Check Date</label>
                <input type="text" name="dnd_check_date" id="editDndDate" class="form-control datepicker" style="border-radius:8px; border:1px solid #fca5a5; font-size:13px; height:38px;">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group" style="margin-bottom:12px;">
                <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Quantity</label>
                <input type="number" name="qty" id="editQty" value="1" min="1" class="form-control" oninput="calculateMrrTotal()" style="border-radius:8px; border:1px solid #e5e5ea; font-size:13px; height:38px;">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group" style="margin-bottom:12px;">
                <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">MRC</label>
                <input type="text" name="mrc" id="editMrc" class="form-control" oninput="calculateMrrTotal()" style="border-radius:8px; border:1px solid #e5e5ea; font-size:13px; height:38px;">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group" style="margin-bottom:12px;">
                <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Revenue</label>
                <input type="text" name="revenue" id="editRevenue" class="form-control" oninput="calculateMrrTotal()" style="border-radius:8px; border:1px solid #e5e5ea; font-size:13px; height:38px;">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group" style="margin-bottom:12px;">
                <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Call Status</label>
                <select name="status" id="editStatus" class="form-control" style="border-radius:8px; border:1px solid #e5e5ea; font-size:13px; height:38px;">
                  <?php if(!empty($statuses)){
                    foreach($statuses as $st){ ?>
                      <option value="<?php echo $st['id']; ?>"><?php echo htmlspecialchars($st['status']); ?></option>
                  <?php } } ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group" style="margin-bottom:12px;">
                <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Calculated MRR</label>
                <input type="text" name="mrr" id="editMrr" readonly class="form-control" style="background:#f2f2f7; border-radius:8px; border:1px solid #e5e5ea; font-size:13px; height:38px; font-weight:700;">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group" style="margin-bottom:12px;">
                <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Next Follow-up Date</label>
                <input type="text" name="followup_date" id="editNextFollowup" class="form-control datepicker" style="border-radius:8px; border:1px solid #e5e5ea; font-size:13px; height:38px;">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group" style="margin-bottom:12px;">
                <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Follow-up Time</label>
                <input type="text" name="followup_time" id="editNextFollowupTime" class="form-control" value="11:00 AM" style="border-radius:8px; border:1px solid #e5e5ea; font-size:13px; height:38px;">
              </div>
            </div>
          </div>

          <div class="form-group" style="margin-bottom:16px;">
            <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Call Remark</label>
            <textarea name="remark" id="editRemark" rows="2" class="form-control" style="border-radius:8px; border:1px solid #e5e5ea; font-size:13px;"></textarea>
          </div>

          <div style="display:flex; justify-content:flex-end; gap:8px;">
            <button type="button" class="btn-edit-row" onclick="backToListFromDetails()">Cancel</button>
            <button type="button" class="btn-edit-row" style="background:#0072f0; color:#ffffff;" onclick="saveRecordDetailsForm()"><i class="fa fa-save"></i> Save</button>
          </div>
        </form>
      </div>

      <div id="middleViewAutoDialer" style="display:none; padding:15px; height:100%; overflow-y:auto;">
        <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:18px 20px; box-shadow:0 1px 3px rgba(0,0,0,0.04); margin-bottom:16px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:8px;">
            <h3 style="font-size:16px; font-weight:700; color:#0f172a; margin:0; letter-spacing:-0.01em;" id="dialerSetupHeading">
              <i class="fa fa-play-circle" style="color:#0072f0; margin-right:6px;" id="dialerSetupIcon"></i> <span id="dialerSetupTitle">Auto Dialer</span>
            </h3>
            <div style="display:flex; gap:6px;">
              <button type="button" class="btn btn-xs btn-default" onclick="selectAllAutoStages(true)" style="border-radius:6px; font-size:11px; font-weight:600; padding:4px 10px;">Select All</button>
              <button type="button" class="btn btn-xs btn-default" onclick="selectAllAutoStages(false)" style="border-radius:6px; font-size:11px; font-weight:600; padding:4px 10px;">Clear All</button>
            </div>
          </div>

          <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(135px, 1fr)); gap:8px; margin-bottom:14px; padding:12px; background:#f8fafc; border-radius:10px; border:1px solid #f1f5f9;">
            <?php if(!empty($statuses)){ foreach($statuses as $st){ ?>
              <label style="display:flex; align-items:center; gap:8px; font-size:12px; font-weight:500; color:#334155; margin:0; cursor:pointer;">
                <input type="checkbox" class="cbx-auto-stage" value="<?php echo (int)$st['id']; ?>" onchange="updateAutoDialerQueueCount()" style="margin:0; width:15px; height:15px; accent-color:#0072f0;">
                <span><?php echo htmlspecialchars($st['status']); ?></span>
              </label>
            <?php } } ?>
          </div>

          <div style="display:flex; flex-wrap:wrap; gap:16px; align-items:flex-end; margin-bottom:16px;">
            <div style="flex:1 1 200px; min-width:180px;" id="wrapAutoDialTimerDelay">
              <label style="font-size:12px; font-weight:600; color:#334155; margin-bottom:4px; display:block;">Call Delay Interval</label>
              <select id="autoDialTimerDelay" class="form-control" style="border-radius:8px; border:1px solid #e2e8f0; font-size:13px; height:36px;">
                <option value="3">3 Seconds</option>
                <option value="60" selected>60 Seconds</option>
                <option value="90">90 Seconds</option>
                <option value="120">120 Seconds</option>
              </select>
            </div>
            <div style="flex:1 1 220px; min-width:200px;">
              <label style="font-size:12px; font-weight:600; color:#334155; margin-bottom:4px; display:block;">Range (Last 3 Digits)</label>
              <div style="display:flex; align-items:center; gap:8px;">
                <input type="number" id="dialerRangeFrom" min="0" max="999" placeholder="001" class="form-control" style="border-radius:8px; border:1px solid #e2e8f0; font-size:13px; height:36px;" oninput="updateAutoDialerQueueCount()">
                <span style="font-size:12px; color:#64748b; font-weight:600;">to</span>
                <input type="number" id="dialerRangeTo" min="0" max="999" placeholder="200" class="form-control" style="border-radius:8px; border:1px solid #e2e8f0; font-size:13px; height:36px;" oninput="updateAutoDialerQueueCount()">
              </div>
            </div>
          </div>

          <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; padding-top:8px; border-top:1px solid #f1f5f9;">
            <div style="font-size:13px; color:#475569;">
              Queue Status: <strong id="autoDialerQueueCount" style="color:#0f172a; font-weight:700;">0 records ready</strong>
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
              <button type="button" id="btnDialerBreak" class="btn btn-sm btn-default btn-dialer-break" onclick="toggleDialerBreak()" style="border-radius:8px; font-weight:600; padding:7px 16px; border:1px solid #cbd5e1;">
                <i class="fa fa-coffee"></i> Break
              </button>
              <button type="button" id="btnStartAutoDial" class="btn btn-sm btn-primary" onclick="startAutoDialSession()" style="background:#0072f0; border-color:#0072f0; font-weight:600; padding:7px 20px; border-radius:8px;">
                <i class="fa fa-play"></i> Start Auto Dialing
              </button>
              <button type="button" id="btnStartManualDial" class="btn btn-sm btn-primary" onclick="startManualDialSession()" style="display:none; background:#0072f0; border-color:#0072f0; font-weight:600; padding:7px 20px; border-radius:8px;">
                <i class="fa fa-play"></i> Start Manual Dialing
              </button>
              <button type="button" id="btnPauseAutoDial" class="btn btn-sm btn-default" onclick="toggleAutoDialPause()" style="display:none; border-radius:8px; font-weight:600; padding:7px 16px;">
                <i class="fa fa-pause"></i> Pause
              </button>
              <button type="button" id="btnNextAutoDial" class="btn btn-sm btn-default" onclick="skipToNextAutoLead()" style="display:none; border-radius:8px; font-weight:600; padding:7px 16px;">
                <i class="fa fa-forward"></i> Skip / Next
              </button>
              <button type="button" id="btnStopAutoDial" class="btn btn-sm btn-danger" onclick="stopAutoDialSession()" style="display:none; border-radius:8px; font-weight:600; padding:7px 16px;">
                <i class="fa fa-stop"></i> Stop Session
              </button>
            </div>
          </div>
        </div>

        <div id="autoDialerLiveCard"  style="display:none; background:#ffffff; border:1px solid #0072f0; border-radius:12px; padding:20px; box-shadow:0 4px 14px rgba(0,114,240,0.08);">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#0072f0; background:#e6f1fe; padding:3px 10px; border-radius:12px;" id="autoDialProgressBadge">
              Lead 1 of 0
            </span>
            <span style="font-size:12px; color:#64748b;" id="autoDialStageBadge">Stage: -</span>
          </div>
          <h4 style="font-size:18px; font-weight:700; color:#0f172a; margin:0 0 6px 0;" id="autoDialClientName">-</h4>
          <p style="font-size:14px; font-weight:600; color:#334155; margin:0 0 12px 0;" id="autoDialPhone">-</p>
          <div style="font-size:12px; color:#64748b;" id="autoDialStatusText">Preparing connection...</div>

          <div id="autoDialCountdownWrap" style="display:none; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:10px 14px; margin-top:12px; align-items:center; justify-content:space-between;">
            <div style="font-size:12px; font-weight:600; color:#166534;">
              <i class="fa fa-clock-o"></i> Next call dialing in <strong id="autoDialCountdownNum" style="font-size:15px; color:#15803d;">60</strong>s...
            </div>
            <div style="display:flex; gap:6px;">
              <button type="button" class="btn btn-xs btn-success" onclick="triggerImmediateNextAutoLead()" style="border-radius:6px; font-weight:600;"><i class="fa fa-phone"></i> Dial Now</button>
              <button type="button" class="btn btn-xs btn-default" onclick="toggleAutoDialPause()" style="border-radius:6px; font-weight:600;" id="btnPauseCountdown">Pause</button>
            </div>
          </div>
        </div>
      </div><div id="middleViewManualDialer" style="display:none; padding:15px; height:100%; overflow-y:auto;">
        <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:18px 20px; box-shadow:0 1px 3px rgba(0,0,0,0.04); margin-bottom:16px;">
          
          <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:16px; padding-bottom:12px; border-bottom:1px solid #f1f5f9;">
            <div style="display:flex; align-items:center; gap:10px;">
              <button type="button" class="btn btn-xs btn-default" onclick="backToDialerSetup()" style="border-radius:6px; font-weight:600; padding:4px 10px; border:1px solid #cbd5e1;">
                <i class="fa fa-arrow-left"></i> Setup
              </button>
              <h3 style="font-size:16px; font-weight:700; color:#0f172a; margin:0;" id="manualDetailsHeaderName">Contact Details</h3>
              <span id="manualDialProgressBadge" style="font-size:11px; font-weight:700; color:#0072f0; background:#e6f1fe; padding:3px 10px; border-radius:12px;">Contact 0 of 0</span>
            </div>
            
            <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
              <button type="button" class="btn btn-sm btn-default" onclick="prevManualLead()" title="Previous Contact" style="border-radius:6px; height:32px; padding:4px 10px; border:1px solid #cbd5e1;">
                <i class="fa fa-chevron-left"></i>
              </button>
              <button type="button" class="btn btn-sm btn-warning" onclick="skipManualLead()" title="Skip to next contact" style="background:#f59e0b; border-color:#d97706; color:#ffffff; border-radius:6px; font-weight:600; height:32px; padding:4px 12px;">
                <i class="fa fa-forward"></i> Skip
              </button>
              <button type="button" class="btn btn-sm btn-default" onclick="saveManualRecordDetails(false)" title="Save changes" style="border-radius:6px; font-weight:600; height:32px; padding:4px 12px; border:1px solid #cbd5e1;">
                <i class="fa fa-save"></i> Save
              </button>
              <button type="button" class="btn btn-sm btn-success" onclick="saveAndNextManualLead()" title="Save details and call next contact" style="background:#16a34a; border-color:#16a34a; color:#ffffff; border-radius:6px; font-weight:600; height:32px; padding:4px 14px;">
                <i class="fa fa-arrow-right"></i> Save &amp; Next
              </button>
              <button type="button" class="btn btn-sm btn-primary" onclick="dialActiveManualContact()" title="Dial active contact number" style="background:#0072f0; border-color:#0072f0; border-radius:6px; font-weight:600; height:32px; padding:4px 14px;">
                <i class="fa fa-phone"></i> Call Now
              </button>
              <button type="button" class="btn btn-sm btn-default btn-dialer-break" onclick="toggleDialerBreak()" style="border-radius:6px; font-weight:600; height:32px; padding:4px 12px; border:1px solid #cbd5e1;">
                <i class="fa fa-coffee"></i> Break
              </button>
            </div>
          </div>

          <form id="formManualDetails">
            <input type="hidden" name="lead_id" id="manualEditLeadId" value="0">
            <input type="hidden" name="call_log_id" id="manualEditCallLogId" value="0">
            <input type="hidden" name="call_duration" id="manualEditCallDuration" value="0">

            <div style="background:#f8fafc; border:1px solid #f1f5f9; border-radius:10px; padding:14px; margin-bottom:14px;">
              <h5 style="margin:0 0 10px 0; font-size:12px; font-weight:700; text-transform:uppercase; color:#475569; letter-spacing:0.5px;">1. Client &amp; Contact Information</h5>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group" style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Company Name</label>
                    <input type="text" name="customer" id="manualEditCustomer" class="form-control" style="border-radius:8px; border:1px solid #e2e8f0; font-size:13px; height:36px;">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group" style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Point of Contact (POC)</label>
                    <input type="text" name="poc" id="manualEditPoc" class="form-control" style="border-radius:8px; border:1px solid #e2e8f0; font-size:13px; height:36px;">
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group" style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Primary Phone</label>
                    <div style="display:flex; gap:6px;">
                      <input type="text" name="contact1" id="manualEditContact1" class="form-control" style="border-radius:8px; border:1px solid #e2e8f0; font-size:13px; height:36px; font-weight:600;">
                      <button type="button" class="btn btn-default" onclick="dialManualPhoneField('primary')" title="Dial Primary Phone" style="border-radius:8px; height:36px; color:#0072f0; border:1px solid #cbd5e1;"><i class="fa fa-phone"></i></button>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group" style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Alt Phone</label>
                    <div style="display:flex; gap:6px;">
                      <input type="text" name="alt_contact" id="manualEditAltContact" class="form-control" style="border-radius:8px; border:1px solid #e2e8f0; font-size:13px; height:36px;">
                      <button type="button" class="btn btn-default" onclick="dialManualPhoneField('alt')" title="Dial Alt Phone" style="border-radius:8px; height:36px; color:#0072f0; border:1px solid #cbd5e1;"><i class="fa fa-phone"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group" style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Email Address</label>
                    <input type="email" name="email" id="manualEditEmail" class="form-control" style="border-radius:8px; border:1px solid #e2e8f0; font-size:13px; height:36px;">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group" style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Location</label>
                    <input type="text" name="location" id="manualEditLocation" class="form-control" style="border-radius:8px; border:1px solid #e2e8f0; font-size:13px; height:36px;">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group" style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">DNCR Registry</label>
                    <select name="dnd_status" id="manualEditDndStatus" class="form-control" onchange="toggleManualDncrDateRow()" style="border-radius:8px; border:1px solid #e2e8f0; font-size:13px; height:36px;">
                      <option value="No">No (Clean)</option>
                      <option value="Yes">Yes (DND Active)</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <div style="background:#f8fafc; border:1px solid #f1f5f9; border-radius:10px; padding:14px; margin-bottom:14px;">
              <h5 style="margin:0 0 10px 0; font-size:12px; font-weight:700; text-transform:uppercase; color:#475569; letter-spacing:0.5px;">2. Business Scope &amp; Commercials</h5>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group" style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Authority</label>
                    <select name="tl_authority" id="manualEditAuthority" class="form-control" style="border-radius:8px; border:1px solid #e2e8f0; font-size:13px; height:36px;">
                      <option value="">Select Authority</option>
                      <option value="DED">DED</option>
                      <option value="DMCC">DMCC</option>
                      <option value="SHAMS">SHAMS</option>
                      <option value="IFZA">IFZA</option>
                      <option value="RAKEZ">RAKEZ</option>
                      <option value="Other">Other</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group" style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Quantity</label>
                    <input type="number" name="qty" id="manualEditQty" value="1" min="1" class="form-control" oninput="calculateManualMrrTotal()" style="border-radius:8px; border:1px solid #e2e8f0; font-size:13px; height:36px;">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group" style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">MRC</label>
                    <input type="text" name="mrc" id="manualEditMrc" class="form-control" oninput="calculateManualMrrTotal()" style="border-radius:8px; border:1px solid #e2e8f0; font-size:13px; height:36px;">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group" style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Calculated MRR</label>
                    <input type="text" name="mrr" id="manualEditMrr" readonly class="form-control" style="background:#f1f5f9; border-radius:8px; border:1px solid #e2e8f0; font-size:13px; height:36px; font-weight:700; color:#16a34a;">
                  </div>
                </div>
              </div>
            </div>

            <div style="background:#f8fafc; border:1px solid #f1f5f9; border-radius:10px; padding:14px; margin-bottom:14px;">
              <h5 style="margin:0 0 10px 0; font-size:12px; font-weight:700; text-transform:uppercase; color:#475569; letter-spacing:0.5px;">3. Status, Follow-Up &amp; Remarks</h5>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group" style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Call / Lead Status</label>
                    <select name="status" id="manualEditStatus" class="form-control" style="border-radius:8px; border:1px solid #e2e8f0; font-size:13px; height:36px;">
                      <?php if(!empty($statuses)){
                        foreach($statuses as $st){ ?>
                          <option value="<?php echo $st['id']; ?>"><?php echo htmlspecialchars($st['status']); ?></option>
                      <?php } } ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group" style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Next Follow-up Date</label>
                    <input type="text" name="followup_date" id="manualEditNextFollowup" class="form-control datepicker" style="border-radius:8px; border:1px solid #e2e8f0; font-size:13px; height:36px;">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group" style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Follow-up Time</label>
                    <input type="text" name="followup_time" id="manualEditNextFollowupTime" class="form-control" value="11:00 AM" style="border-radius:8px; border:1px solid #e2e8f0; font-size:13px; height:36px;">
                  </div>
                </div>
              </div>

              <div class="form-group" style="margin-bottom:0;">
                <label style="font-size:12px; font-weight:600; color:#1d1d1f; margin-bottom:4px; display:block;">Call Remark / Discussion Notes</label>
                <textarea name="remark" id="manualEditRemark" rows="3" class="form-control" placeholder="Discussion notes, commitments, objections..." style="border-radius:8px; border:1px solid #e2e8f0; font-size:13px;"></textarea>
              </div>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; padding-top:8px;">
              <div style="display:flex; gap:8px;">
                <button type="button" class="btn btn-sm btn-default" onclick="newBlankManualContact()" style="border-radius:6px; border:1px solid #cbd5e1;">
                  <i class="fa fa-user-plus"></i> Blank Record
                </button>
                <button type="button" class="btn btn-sm btn-default" onclick="prevManualLead()" style="border-radius:6px; border:1px solid #cbd5e1;">
                  <i class="fa fa-chevron-left"></i> Previous
                </button>
              </div>
              <div style="display:flex; gap:8px;">
                <button type="button" class="btn btn-sm btn-warning" onclick="skipManualLead()" style="background:#f59e0b; border-color:#d97706; color:#ffffff; font-weight:600; border-radius:6px; padding:6px 14px;">
                  <i class="fa fa-forward"></i> Skip
                </button>
                <button type="button" class="btn btn-sm btn-default" onclick="saveManualRecordDetails(false)" style="font-weight:600; border-radius:6px; padding:6px 14px; border:1px solid #cbd5e1;">
                  <i class="fa fa-save"></i> Save
                </button>
                <button type="button" class="btn btn-sm btn-success" onclick="saveAndNextManualLead()" style="background:#16a34a; border-color:#16a34a; color:#ffffff; font-weight:600; border-radius:6px; padding:6px 16px;">
                  <i class="fa fa-arrow-right"></i> Save &amp; Next
                </button>
                <button type="button" class="btn btn-sm btn-primary" onclick="dialActiveManualContact()" style="background:#0072f0; border-color:#0072f0; font-weight:600; border-radius:6px; padding:6px 16px;">
                  <i class="fa fa-phone"></i> Call Now
                </button>
              </div>
            </div>
          </form>
        </div>
      </div></div><div id="middleViewLogs" style="display:none;">
        <div style="display:grid; grid-template-columns: repeat(5, 1fr); gap:12px; margin-bottom:18px;">
          <div class="sheet-stat-card">
            <div class="sheet-stat-val" id="logStatTotal">0</div>
            <div class="sheet-stat-lbl">Total Outbound</div>
          </div>
          <div class="sheet-stat-card">
            <div class="sheet-stat-val" id="logStatConnected" style="color:#34c759;">0</div>
            <div class="sheet-stat-lbl">Connected</div>
          </div>
          <div class="sheet-stat-card">
            <div class="sheet-stat-val" id="logStatNewCalls" style="color:#0072f0;">0</div>
            <div class="sheet-stat-lbl">New Calls</div>
          </div>
          <div class="sheet-stat-card">
            <div class="sheet-stat-val" id="logStatFollowups" style="color:#ff9500;">0</div>
            <div class="sheet-stat-lbl">Follow-ups</div>
          </div>
          <div class="sheet-stat-card">
            <div class="sheet-stat-val" id="logStatDuration">0m</div>
            <div class="sheet-stat-lbl">Talk Time</div>
          </div>
        </div>

        <div style="margin-bottom:20px;">
          <h4 style="font-size:13px; font-weight:700; color:#1d1d1f; margin:0 0 10px 0; text-transform:uppercase; letter-spacing:0.5px;">Status Breakdown Sheet</h4>
          <table class="sheet-table" id="tableStatusBreakdown">
            <thead>
              <tr>
                <th>Call Status / Outcome</th>
                <th style="text-align:center;">New Calls</th>
                <th style="text-align:center;">Follow-up Calls</th>
                <th style="text-align:center;">Total Calls</th>
                <th style="text-align:right;">% Share</th>
              </tr>
            </thead>
            <tbody id="tbodyStatusBreakdown">
              <tr><td colspan="5" style="text-align:center; padding:15px; color:#8e8e93;">Loading breakdown...</td></tr>
            </tbody>
          </table>
        </div>

        <div>
          <h4 style="font-size:13px; font-weight:700; color:#1d1d1f; margin:0 0 10px 0; text-transform:uppercase; letter-spacing:0.5px;">Recent Outbound Call History</h4>
          <div style="overflow-x:auto;">
            <table class="sheet-table" id="tableRecentLogs">
              <thead>
                <tr>
                  <th>Date & Time</th>
                  <th>Customer / Contact</th>
                  <th>Phone Number</th>
                  <th>Category</th>
                  <th>Outcome</th>
                  <th>Duration</th>
                  <th style="text-align:right;">Action</th>
                </tr>
              </thead>
              <tbody id="tbodyRecentLogs">
                <tr><td colspan="7" style="text-align:center; padding:15px; color:#8e8e93;">Loading history...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>

  </div>

  <div class="crm-dialer-panel">

    <div class="dialer-window-header">
      <span style="font-size:13px; font-weight:700; color:#1d1d1f; letter-spacing:-0.01em;">Phone</span>
      <div class="dialer-window-header-icons">
        <i class="fa fa-headphones" title="Headset"></i>
        <i class="fa fa-volume-up" title="Audio"></i>
      </div>
    </div>

    <div class="dialer-top-display">
      <div class="dialer-active-chip" id="dialerContactChip">
        <i class="fa fa-user" style="font-size:11px;"></i>
        <span id="dialerChipName">Select contact</span>
      </div>

      <div style="position:relative; width:100%;">
        <input type="text" class="dialer-num-input" id="dialerInput" placeholder="Enter number or name" autocomplete="off" oninput="onDialerInputChange()" onkeyup="handleDialerInputKey(event)">
        <div id="dialerSuggestions" class="dialer-suggestions-popover" style="display:none;"></div>
      </div>
      <div class="dialer-status-sub" id="dialerStatusSubtitle">Ready to call</div>
    </div>

    <div class="dialer-keypad-container">
      <div class="dialer-keypad-grid">
        <button type="button" class="dialer-key-btn" onclick="pressDialerKey('1')">
          <span class="dialer-key-digit">1</span>
          <span class="dialer-key-sub">&nbsp;</span>
        </button>
        <button type="button" class="dialer-key-btn" onclick="pressDialerKey('2')">
          <span class="dialer-key-digit">2</span>
          <span class="dialer-key-sub">ABC</span>
        </button>
        <button type="button" class="dialer-key-btn" onclick="pressDialerKey('3')">
          <span class="dialer-key-digit">3</span>
          <span class="dialer-key-sub">DEF</span>
        </button>

        <button type="button" class="dialer-key-btn" onclick="pressDialerKey('4')">
          <span class="dialer-key-digit">4</span>
          <span class="dialer-key-sub">GHI</span>
        </button>
        <button type="button" class="dialer-key-btn" onclick="pressDialerKey('5')">
          <span class="dialer-key-digit">5</span>
          <span class="dialer-key-sub">JKL</span>
        </button>
        <button type="button" class="dialer-key-btn" onclick="pressDialerKey('6')">
          <span class="dialer-key-digit">6</span>
          <span class="dialer-key-sub">MNO</span>
        </button>

        <button type="button" class="dialer-key-btn" onclick="pressDialerKey('7')">
          <span class="dialer-key-digit">7</span>
          <span class="dialer-key-sub">PQRS</span>
        </button>
        <button type="button" class="dialer-key-btn" onclick="pressDialerKey('8')">
          <span class="dialer-key-digit">8</span>
          <span class="dialer-key-sub">TUV</span>
        </button>
        <button type="button" class="dialer-key-btn" onclick="pressDialerKey('9')">
          <span class="dialer-key-digit">9</span>
          <span class="dialer-key-sub">WXYZ</span>
        </button>

        <button type="button" class="dialer-key-btn" onclick="pressDialerKey('*')">
          <span class="dialer-key-digit">*</span>
          <span class="dialer-key-sub">&nbsp;</span>
        </button>
        <button type="button" class="dialer-key-btn" onclick="pressDialerKey('0')">
          <span class="dialer-key-digit">0</span>
          <span class="dialer-key-sub">+</span>
        </button>
        <button type="button" class="dialer-key-btn" onclick="pressDialerKey('#')">
          <span class="dialer-key-digit">#</span>
          <span class="dialer-key-sub">&nbsp;</span>
        </button>
      </div>
    </div>

    <div class="dialer-bottom-actions">
      <button type="button" class="keypad-tool-btn" title="Settings" onclick="showToast('Audio Ready')">
        <i class="fa fa-sliders"></i>
      </button>

      <button type="button" class="btn-green-call" id="btnToggleCall" onclick="handleCallButtonClick()" title="Call">
        <i class="fa fa-phone"></i>
      </button>

      <button type="button" class="keypad-tool-btn" onclick="dialerBackspace()" title="Delete">
        <i class="fa fa-chevron-left"></i>
      </button>
    </div>

  </div>

</div>

<div class="dialer-toast" id="dialerToast">
  <span id="dialerToastText">Notification</span>
</div>

<script>
var activeLead = null;
var activeCallLogId = 0;
var currentQueue = [];
var currentIndex = 0;
var currentLogsPeriod = 'day';
var activeQueueType = 'followup';
var activeQueueFilter = 'today';
var callState = 'idle';
var callTimerInterval = null;
var callConnectedSeconds = 0;

var allColleagues = [];
var autoDialerActive = false;
var autoDialerPaused = false;
var autoDialerIndex = 0;
var autoDialerQueue = [];
var autoDialerCountdownInterval = null;
var autoDialerCountdownSeconds = 0;
var manualSearchTimeout = null;
var manualDialerQueue = [];
var manualDialerIndex = 0;
var manualQueueType = 'database';

function hideAllMiddleViews() {
  $('#middleViewTeam').hide();
  $('#middleViewList').hide();
  $('#middleViewDetails').hide();
  $('#middleViewLogs').hide();
  $('#middleViewAutoDialer').hide();
  $('#middleViewManualDialer').hide();
}

var audioCtx = null;
function getAudioContext() {
  if (!audioCtx) {
    var AudioCtxClass = window.AudioContext || window.webkitAudioContext;
    if (AudioCtxClass) {
      audioCtx = new AudioCtxClass();
    }
  }
  return audioCtx;
}

function playDtmfTone(freq1, freq2, durationMs) {
  var ctx = getAudioContext();
  if (!ctx) return;
  if (ctx.state === 'suspended') {
    ctx.resume();
  }

  var osc1 = ctx.createOscillator();
  var osc2 = ctx.createOscillator();
  var gain = ctx.createGain();

  osc1.type = 'sine';
  osc2.type = 'sine';
  osc1.frequency.value = freq1;
  osc2.frequency.value = freq2;

  gain.gain.setValueAtTime(0.06, ctx.currentTime);
  gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + (durationMs / 1000));

  osc1.connect(gain);
  osc2.connect(gain);
  gain.connect(ctx.destination);

  osc1.start();
  osc2.start();
  osc1.stop(ctx.currentTime + (durationMs / 1000));
  osc2.stop(ctx.currentTime + (durationMs / 1000));
}

var dtmfFreqs = {
  '1': [697, 1209], '2': [697, 1336], '3': [697, 1477],
  '4': [770, 1209], '5': [770, 1336], '6': [770, 1477],
  '7': [852, 1209], '8': [852, 1336], '9': [852, 1477],
  '*': [941, 1209], '0': [941, 1336], '#': [941, 1477]
};

function playKeyTone(key) {
  if (dtmfFreqs[key]) {
    playDtmfTone(dtmfFreqs[key][0], dtmfFreqs[key][1], 90);
  }
}

function showToast(msg) {
  var t = document.getElementById('dialerToast');
  var txt = document.getElementById('dialerToastText');
  if (!t || !txt) return;
  txt.textContent = msg;
  t.style.display = 'flex';
  setTimeout(function() {
    t.style.display = 'none';
  }, 2400);
}

function pressDialerKey(k) {
  playKeyTone(k);
  var inp = document.getElementById('dialerInput');
  if (inp) {
    inp.value += k;
    onDialerInputChange();
  }
}

function dialerBackspace() {
  var inp = document.getElementById('dialerInput');
  if (inp && inp.value.length > 0) {
    inp.value = inp.value.slice(0, -1);
    onDialerInputChange();
  }
}

function onDialerInputChange() {
  var val = $('#dialerInput').val().trim();
  if (val.length < 3) {
    $('#dialerSuggestions').hide().empty();
    return;
  }

  if (!allColleagues || allColleagues.length === 0) {
    loadTeamColleaguesData(true, function() {
      onDialerInputChange();
    });
    return;
  }

  var q = val.toLowerCase();
  var matches = [];
  for (var i = 0; i < allColleagues.length; i++) {
    var c = allColleagues[i];
    var searchStr = ((c.name || '') + ' ' + (c.department || '') + ' ' + (c.extension || '') + ' ' + (c.phone || '') + ' ' + (c.dial_target || '')).toLowerCase();
    if (searchStr.indexOf(q) > -1) {
      matches.push(c);
      if (matches.length >= 8) break;
    }
  }

  if (matches.length === 0) {
    $('#dialerSuggestions').hide().empty();
    return;
  }

  var html = '';
  for (var j = 0; j < matches.length; j++) {
    var m = matches[j];
    var initials = m.name ? m.name.split(' ').map(function(n){ return n[0]; }).slice(0, 2).join('').toUpperCase() : 'TM';
    var dialNum = m.dial_target || m.extension || m.phone || '';

    html += '<div class="dialer-suggestion-row" onclick="selectDialerSuggestion(\'' + escapeJs(m.name) + '\', \'' + escapeJs(dialNum) + '\', \'' + escapeJs(m.department || '') + '\')">';
    html += '  <div style="display:flex; align-items:center; gap:8px; min-width:0;">';
    html += '    <div class="dialer-suggestion-avatar">' + initials + '</div>';
    html += '    <div style="min-width:0;">';
    html += '      <div style="font-size:12.5px; font-weight:600; color:#1d1d1f; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">' + escapeHtml(m.name) + '</div>';
    html += '      <div style="font-size:10.5px; color:#8e8e93; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">' + escapeHtml(m.department || 'Team') + (m.extension ? ' &bull; ' + escapeHtml(m.extension) : '') + '</div>';
    html += '    </div>';
    html += '  </div>';
    if (dialNum) {
      html += '  <span class="apple-quick-call-btn" style="width:26px; height:26px; font-size:11px; flex-shrink:0;"><i class="fa fa-phone"></i></span>';
    }
    html += '</div>';
  }

  $('#dialerSuggestions').html(html).show();
}

function selectDialerSuggestion(name, targetNum, dept) {
  $('#dialerSuggestions').hide().empty();
  if (targetNum) {
    $('#dialerInput').val(targetNum);
  }
  $('#dialerChipName').text(name + (dept ? ' (' + dept + ')' : ''));
  $('#dialerContactChip').css('display', 'inline-flex');

  activeLead = {
    id: 0,
    customer: name + ' (Colleague)',
    is_internal: 1,
    phone_primary: targetNum,
    status_name: 'Internal Team'
  };
}

function handleDialerInputKey(e) {
  if (e.key === 'Escape') {
    $('#dialerSuggestions').hide().empty();
    return;
  }
  if (e.key === 'Enter') {
    var firstRow = $('#dialerSuggestions .dialer-suggestion-row').first();
    if (firstRow.length > 0 && isNaN($('#dialerInput').val().trim())) {
      firstRow.trigger('click');
      startDialing();
      return;
    }
    $('#dialerSuggestions').hide().empty();
    startDialing();
  }
}

$(document).on('click', function(e) {
  if (!$(e.target).closest('.dialer-top-display').length) {
    $('#dialerSuggestions').hide();
  }
});

function openDialerHome() {
  $('.crm-dock-btn').removeClass('active');
  $('#dockBtnCallHome').addClass('active');
  hideAllMiddleViews();
  $('#middleContentArea').hide();
  $('#subnavFollowup').hide();
  $('#subnavLogsPeriod').hide();
  $('#dialerSuggestions').hide();
  $('#dialerInput').focus();
}

function openManualDialerMode() {
  $('.crm-dock-btn').removeClass('active');
  $('#dockBtnDatabase').addClass('active');
  $('#middleAreaTitle').text('Manual Dialer');
  $('#subnavFollowup').hide();
  $('#subnavLogsPeriod').hide();
  hideAllMiddleViews();
  $('#middleViewManualDialer').show();
  openMiddleArea();
  if (!manualDialerQueue || manualDialerQueue.length === 0) {
    loadManualDialerQueue($('#manualDialQueueSource').val() || 'database');
  } else {
    displayManualLead(manualDialerIndex);
  }
}

var dialerMode = 'auto';
var isOnBreak = false;

function openAutoDialerMode() {
  dialerMode = 'auto';
  $('.crm-dock-btn').removeClass('active');
  $('#dockBtnDatabase').addClass('active');
  $('#middleAreaTitle').text('Auto Dialer');
  $('#dialerSetupTitle').text('Auto Dialer - Setup');
  $('#dialerSetupIcon').attr('class', 'fa fa-play-circle');
  $('#subnavFollowup').hide();
  $('#subnavLogsPeriod').hide();
  hideAllMiddleViews();
  $('#wrapAutoDialTimerDelay').show();
  $('#btnStartAutoDial').show();
  $('#btnStartManualDial').hide();
  $('#middleViewAutoDialer').show();
  openMiddleArea();
  updateAutoDialerQueueCount();
}

function openManualDialerMode() {
  dialerMode = 'manual';
  $('.crm-dock-btn').removeClass('active');
  $('#dockBtnDatabase').addClass('active');
  $('#middleAreaTitle').text('Manual Dialer');
  $('#dialerSetupTitle').text('Manual Dialer - Setup');
  $('#dialerSetupIcon').attr('class', 'fa fa-keyboard-o');
  $('#subnavFollowup').hide();
  $('#subnavLogsPeriod').hide();
  hideAllMiddleViews();
  $('#wrapAutoDialTimerDelay').hide();
  $('#btnStartAutoDial').hide();
  $('#btnStartManualDial').show();
  $('#middleViewAutoDialer').show();
  openMiddleArea();
  updateAutoDialerQueueCount();
}

function openLeadsDialerMode() {
  dialerMode = 'leads';
  $('.crm-dock-btn').removeClass('active');
  $('#dockBtnLeads').addClass('active');
  $('#middleAreaTitle').text('Leads Dialer');
  $('#dialerSetupTitle').text('Leads Dialer - Selection');
  $('#dialerSetupIcon').attr('class', 'fa fa-bullhorn');
  $('#subnavFollowup').hide();
  $('#subnavLogsPeriod').hide();
  hideAllMiddleViews();
  $('#btnStartAutoDial').show();
  $('#btnStartManualDial').hide();
  $('#middleViewAutoDialer').show();
  openMiddleArea();
  updateAutoDialerQueueCount();
}

function openFollowupAllFeaturesMode() {
  dialerMode = 'followup';
  $('.crm-dock-btn').removeClass('active');
  $('#dockBtnFollowup').addClass('active');
  $('#middleAreaTitle').text('Follow-up - All Features');
  $('#dialerSetupTitle').text('Follow-up - All Features');
  $('#dialerSetupIcon').attr('class', 'fa fa-calendar-check-o');
  $('#subnavFollowup').hide();
  $('#subnavLogsPeriod').hide();
  hideAllMiddleViews();
  $('#btnStartAutoDial').show();
  $('#btnStartManualDial').hide();
  $('#middleViewAutoDialer').show();
  openMiddleArea();
  updateAutoDialerQueueCount();
}

function backToDialerSetup() {
  hideAllMiddleViews();
  $('#middleViewAutoDialer').show();
  openMiddleArea();
}

function startManualDialSession() {
  if (!autoDialerQueue || autoDialerQueue.length === 0) {
    showToast('Please select stages and ensure records exist in the chosen criteria');
    return;
  }
  manualDialerQueue = autoDialerQueue.slice();
  manualDialerIndex = 0;
  hideAllMiddleViews();
  $('#middleViewManualDialer').show();
  displayManualLead(0);
}

function toggleDialerBreak() {
  var nextAction = isOnBreak ? 'resume' : 'start';
  $.ajax({
    url: '<?php echo base_url(); ?>manager/calls/ajax_log_break',
    type: 'POST',
    data: { action: nextAction },
    dataType: 'json',
    success: function(res) {
      isOnBreak = !isOnBreak;
      if (isOnBreak) {
        if (autoDialerActive && !autoDialerPaused) {
          toggleAutoDialPause();
        }
        if (autoDialerCountdownInterval) {
          clearInterval(autoDialerCountdownInterval);
          autoDialerCountdownInterval = null;
        }
        $('.btn-dialer-break').html('<i class="fa fa-coffee" style="color:#d97706;"></i> <strong>On Break</strong>').css({
          'background': '#fef3c7',
          'border-color': '#f59e0b',
          'color': '#92400e'
        });
        showToast('Break started - Break status recorded in Admin Logs');
      } else {
        $('.btn-dialer-break').html('<i class="fa fa-coffee"></i> Break').css({
          'background': '#ffffff',
          'border-color': '#cbd5e1',
          'color': '#334155'
        });
        var durMsg = res.duration_minutes ? (' (Duration: ' + res.duration_minutes + 'm)') : '';
        showToast('Resumed work from break' + durMsg);
        if (autoDialerActive && autoDialerPaused) {
          toggleAutoDialPause();
        }
      }
    }
  });
}

function selectAllAutoStages(check) {
  $('.cbx-auto-stage').prop('checked', !!check);
  updateAutoDialerQueueCount();
}

function updateAutoDialerQueueCount() {
  var selectedStages = [];
  $('.cbx-auto-stage:checked').each(function() {
    selectedStages.push($(this).val());
  });
  if (selectedStages.length === 0) {
    $('#autoDialerQueueCount').text('0 records ready (select stages above)');
    autoDialerQueue = [];
    return;
  }
  var qType = 'database';
  if (dialerMode === 'leads') {
    qType = 'lead';
  } else if (dialerMode === 'followup') {
    qType = 'followup';
  }
  var rFrom = $('#dialerRangeFrom').val() ? parseInt($('#dialerRangeFrom').val(), 10) : '';
  var rTo = $('#dialerRangeTo').val() ? parseInt($('#dialerRangeTo').val(), 10) : '';

  $('#autoDialerQueueCount').html('<i class="fa fa-spinner fa-spin"></i> Calculating queue...');
  $.ajax({
    url: '<?php echo base_url(); ?>manager/calls/ajax_get_queue',
    type: 'GET',
    data: {
      type: qType,
      filter: 'all',
      status: selectedStages.join(','),
      range_from: rFrom,
      range_to: rTo
    },
    dataType: 'json',
    success: function(res) {
      if (res && res.status && res.leads) {
        autoDialerQueue = res.leads;
        var suffix = (rFrom !== '' || rTo !== '') ? (' (Range: ' + (rFrom !== '' ? rFrom : '0') + '-' + (rTo !== '' ? rTo : '999') + ')') : '';
        $('#autoDialerQueueCount').text(res.leads.length + ' records ready to dial' + suffix);
      } else {
        autoDialerQueue = [];
        $('#autoDialerQueueCount').text('0 records in selected criteria');
      }
    },
    error: function() {
      autoDialerQueue = [];
      $('#autoDialerQueueCount').text('Error fetching queue');
    }
  });
}

function startAutoDialSession() {
  if (!autoDialerQueue || autoDialerQueue.length === 0) {
    showToast('Please select at least one stage with available contacts');
    return;
  }
  autoDialerActive = true;
  autoDialerPaused = false;
  autoDialerIndex = 0;

  $('#btnStartAutoDial').hide();
  $('#btnPauseAutoDial').show();
  $('#btnNextAutoDial').show();
  $('#btnStopAutoDial').show();
  $('#autoDialerLiveCard').show();

  dialCurrentAutoLead();
}

function dialCurrentAutoLead() {
  if (!autoDialerActive) return;
  if (autoDialerIndex >= autoDialerQueue.length) {
    showToast('Auto dialer completed all queued leads!');
    stopAutoDialSession();
    return;
  }

  var l = autoDialerQueue[autoDialerIndex];
  var phone = l.phone_primary || l.contact1 || l.alt_contact || '';
  if (!phone) {
    autoDialerIndex++;
    dialCurrentAutoLead();
    return;
  }

  $('#autoDialProgressBadge').text('Lead ' + (autoDialerIndex + 1) + ' of ' + autoDialerQueue.length);
  $('#autoDialStageBadge').text('Stage: ' + (l.status_name || 'Active'));
  $('#autoDialClientName').text(l.customer || 'Unnamed Contact');
  $('#autoDialPhone').text(phone);
  $('#autoDialStatusText').text('Connecting...');
  $('#autoDialCountdownWrap').hide();

  var fillingMode = $('#autoDialFillingMode').val();
  if (fillingMode === 'manual') {
    activeLead = l;
    currentIndex = autoDialerIndex;
    $('#dialerChipName').text(l.customer || 'No Contact');
    $('#dialerContactChip').css('display', 'inline-flex');
    $('#dialerInput').val(phone);
    $('#editRemark').val('');
  } else {
    applyLeadToDialer(l, autoDialerIndex);
  }

  setTimeout(function() {
    if (autoDialerActive && !autoDialerPaused) {
      $('#autoDialStatusText').text('Dialing...');
      startDialing();
    }
  }, 1200);
}

function startAutoDialCountdown() {
  if (isOnBreak) return;
  if (autoDialerCountdownInterval) {
    clearInterval(autoDialerCountdownInterval);
    autoDialerCountdownInterval = null;
  }
  var delay = parseInt($('#autoDialTimerDelay').val(), 10) || 60;
  autoDialerCountdownSeconds = delay;
  $('#autoDialCountdownNum').text(autoDialerCountdownSeconds);
  $('#autoDialCountdownWrap').css('display', 'flex');
  $('#autoDialStatusText').text('Waiting for next interval...');

  autoDialerCountdownInterval = setInterval(function() {
    if (autoDialerPaused) return;
    autoDialerCountdownSeconds--;
    $('#autoDialCountdownNum').text(autoDialerCountdownSeconds);
    if (autoDialerCountdownSeconds <= 0) {
      clearInterval(autoDialerCountdownInterval);
      autoDialerCountdownInterval = null;
      $('#autoDialCountdownWrap').hide();
      if (autoDialerActive && !autoDialerPaused) {
        dialCurrentAutoLead();
      }
    }
  }, 1000);
}

function triggerImmediateNextAutoLead() {
  if (autoDialerCountdownInterval) {
    clearInterval(autoDialerCountdownInterval);
    autoDialerCountdownInterval = null;
  }
  $('#autoDialCountdownWrap').hide();
  dialCurrentAutoLead();
}

function toggleAutoDialPause() {
  if (!autoDialerActive) return;
  autoDialerPaused = !autoDialerPaused;
  if (autoDialerPaused) {
    $('#btnPauseAutoDial').html('<i class="fa fa-play"></i> Resume');
    $('#btnPauseCountdown').text('Resume');
    $('#autoDialStatusText').text('Auto dialer paused');
    showToast('Auto dialer paused');
  } else {
    $('#btnPauseAutoDial').html('<i class="fa fa-pause"></i> Pause');
    $('#btnPauseCountdown').text('Pause');
    $('#autoDialStatusText').text('Resuming...');
    showToast('Auto dialer resumed');
    if (callState === 'idle') {
      if (autoDialerCountdownInterval) {
      } else {
        dialCurrentAutoLead();
      }
    }
  }
}

function skipToNextAutoLead() {
  if (!autoDialerActive) return;
  if (autoDialerCountdownInterval) {
    clearInterval(autoDialerCountdownInterval);
    autoDialerCountdownInterval = null;
    $('#autoDialCountdownWrap').hide();
  }
  if (callState === 'calling' || callState === 'connected') {
    endActiveCall();
  }
  autoDialerIndex++;
  dialCurrentAutoLead();
}

function stopAutoDialSession() {
  if (autoDialerCountdownInterval) {
    clearInterval(autoDialerCountdownInterval);
    autoDialerCountdownInterval = null;
  }
  autoDialerActive = false;
  autoDialerPaused = false;
  autoDialerIndex = 0;

  $('#btnStartAutoDial').show();
  $('#btnPauseAutoDial').hide().html('<i class="fa fa-pause"></i> Pause');
  $('#btnNextAutoDial').hide();
  $('#btnStopAutoDial').hide();
  $('#autoDialerLiveCard').hide();
  $('#autoDialCountdownWrap').hide();
  showToast('Auto dial session ended');
}

function closeMiddleArea() {
  hideAllMiddleViews();
  $('#middleContentArea').hide();
  $('#subnavFollowup').hide();
  $('#subnavLogsPeriod').hide();
  $('.crm-dock-btn').removeClass('active');
}

function openMiddleArea() {
  $('#middleContentArea').css('display', 'flex');
}

function openTeamColleagues() {
  $('.crm-dock-btn').removeClass('active');
  $('#dockBtnTeam').addClass('active');

  $('#middleAreaTitle').text('Team Members');
  $('#subnavFollowup').hide();
  $('#subnavLogsPeriod').hide();

  hideAllMiddleViews();
  $('#middleViewTeam').show();

  openMiddleArea();
  $('#inputTeamSearch').val('').focus();
  $('#teamColleaguesContainer').html('<div id="teamSearchPrompt" style="text-align:center; padding:50px 20px; color:#8e8e93; font-size:13px; font-weight:500;">Type at least 3 letters to search colleagues or departments</div>');
  if (!allColleagues || allColleagues.length === 0) {
    loadTeamColleaguesData(true);
  }
}

function loadTeamColleaguesData(silent, callback) {
  if (!silent) {
    $('#teamColleaguesContainer').html('<div style="text-align:center; padding:30px; color:#8e8e93;"><i class="fa fa-spinner fa-spin"></i> Loading directory...</div>');
  }

  $.ajax({
    url: '<?php echo base_url(); ?>manager/calls/ajax_get_team_colleagues',
    type: 'GET',
    dataType: 'json',
    success: function(res) {
      if (res && res.status && res.colleagues) {
        allColleagues = res.colleagues;
        if (typeof callback === 'function') {
          callback(allColleagues);
        }
      } else {
        allColleagues = [];
        if (!silent) {
          $('#teamColleaguesContainer').html('<div style="text-align:center; padding:30px; color:#ff3b30;">Failed to load team directory.</div>');
        }
      }
    },
    error: function() {
      allColleagues = [];
      if (!silent) {
        $('#teamColleaguesContainer').html('<div style="text-align:center; padding:30px; color:#ff3b30;">Network error while loading team.</div>');
      }
    }
  });
}

function filterTeamCards() {
  var q = $('#inputTeamSearch').val().trim().toLowerCase();
  if (q.length < 3) {
    $('#teamColleaguesContainer').html('<div id="teamSearchPrompt" style="text-align:center; padding:50px 20px; color:#8e8e93; font-size:13px; font-weight:500;">Type at least 3 letters to search colleagues or departments</div>');
    return;
  }

  if (!allColleagues || allColleagues.length === 0) {
    $('#teamColleaguesContainer').html('<div style="text-align:center; padding:30px; color:#8e8e93;"><i class="fa fa-spinner fa-spin"></i> Loading directory...</div>');
    loadTeamColleaguesData(false, function() {
      filterTeamCards();
    });
    return;
  }

  var matches = [];
  for (var i = 0; i < allColleagues.length; i++) {
    var c = allColleagues[i];
    var searchStr = ((c.name || '') + ' ' + (c.department || '') + ' ' + (c.extension || '') + ' ' + (c.phone || '') + ' ' + (c.dial_target || '')).toLowerCase();
    if (searchStr.indexOf(q) > -1) {
      matches.push(c);
    }
  }

  if (matches.length === 0) {
    $('#teamColleaguesContainer').html('<div style="text-align:center; padding:40px 20px; color:#8e8e93; font-size:13px;">No colleagues or departments matching "' + escapeHtml(q) + '"</div>');
    return;
  }

  var html = '';
  for (var j = 0; j < matches.length; j++) {
    var m = matches[j];
    var initials = m.name ? m.name.split(' ').map(function(n){ return n[0]; }).slice(0, 2).join('').toUpperCase() : 'TM';
    var dialNum = m.dial_target || m.extension || m.phone || '';

    html += '<div class="colleague-card">';
    html += '  <div style="display:flex; align-items:center; gap:12px;">';
    html += '    <div class="colleague-avatar">' + initials + '</div>';
    html += '    <div>';
    html += '      <div style="font-size:13.5px; font-weight:600; color:#1d1d1f;">' + escapeHtml(m.name) + '</div>';
    html += '      <div style="font-size:11.5px; color:#8e8e93; font-weight:500;">' + escapeHtml(m.department || 'General') + (m.extension ? ' &bull; Ext: ' + escapeHtml(m.extension) : '') + '</div>';
    html += '    </div>';
    html += '  </div>';

    html += '  <div style="display:flex; align-items:center; gap:8px;">';
    if (dialNum) {
      html += '    <button type="button" class="apple-quick-call-btn" title="Call ' + escapeJs(m.name) + '" onclick="dialColleague(\'' + escapeJs(m.name) + '\', \'' + escapeJs(dialNum) + '\')"><i class="fa fa-phone"></i></button>';
      html += '    <button type="button" class="btn-edit-row" title="Copy Number" onclick="copyNumber(\'' + escapeJs(dialNum) + '\')"><i class="fa fa-copy"></i></button>';
    } else {
      html += '    <span style="font-size:11.5px; color:#8e8e93;">No phone</span>';
    }
    html += '  </div>';
    html += '</div>';
  }
  $('#teamColleaguesContainer').html(html);
}

function dialColleague(name, targetNum) {
  activeLead = {
    id: 0,
    customer: name + ' (Colleague)',
    is_internal: 1,
    phone_primary: targetNum,
    status_name: 'Internal Team'
  };

  $('#dialerInput').val(targetNum);
  $('#dialerChipName').text(name + ' (Colleague)');
  $('#dialerContactChip').css('display', 'inline-flex');
  startDialing();
}

function copyNumber(num) {
  if (navigator.clipboard) {
    navigator.clipboard.writeText(num).then(function() {
      showToast('Copied ' + num);
    });
  } else {
    showToast('Number: ' + num);
  }
}

function openMenuQueue(type, filter, titleText) {
  activeQueueType = type;
  activeQueueFilter = filter;

  $('.crm-dock-btn').removeClass('active');
  if (type === 'followup') {
    $('#dockBtnFollowup').addClass('active');
    $('#subnavFollowup').show();
    $('#subnavLogsPeriod').hide();
    $('#subnavFollowup .apple-segment-btn').removeClass('active');
    $('#subnavFollowup .apple-segment-btn[data-filter="' + filter + '"]').addClass('active');
  } else if (type === 'database') {
    $('#dockBtnDatabase').addClass('active');
    $('#subnavFollowup').hide();
    $('#subnavLogsPeriod').hide();
  } else if (type === 'lead') {
    $('#dockBtnLeads').addClass('active');
    $('#subnavFollowup').hide();
    $('#subnavLogsPeriod').hide();
  }

  $('#middleAreaTitle').text(titleText);
  hideAllMiddleViews();
  $('#middleViewList').show();

  if (type === 'database' || type === 'lead') {
    $('#statusFilterContainer').show();
  } else {
    $('#statusFilterContainer').hide();
  }

  openMiddleArea();
  loadQueueData(type, filter, $('#selectQueueStatus').val(), $('#inputQueueSearch').val());
}

function reloadCurrentQueue() {
  loadQueueData(activeQueueType, activeQueueFilter, $('#selectQueueStatus').val(), $('#inputQueueSearch').val());
}

function loadQueueData(type, filter, statusVal, searchVal) {
  $('#tbodyQueueRecords').html('<tr><td colspan="5" style="text-align:center; padding:25px; color:#8e8e93;"><i class="fa fa-spinner fa-spin"></i> Loading records...</td></tr>');

  $.ajax({
    url: '<?php echo base_url(); ?>manager/calls/ajax_get_queue',
    type: 'GET',
    data: {
      type: type,
      filter: filter,
      status: statusVal,
      search: searchVal
    },
    dataType: 'json',
    success: function(res) {
      if (res && res.status && res.leads) {
        currentQueue = res.leads;
        renderQueueTable(res.leads);

        if (res.leads.length > 0 && (!activeLead || !activeLead.id)) {
          applyLeadToDialer(res.leads[0], 0);
        }
      } else {
        currentQueue = [];
        $('#tbodyQueueRecords').html('<tr><td colspan="5" style="text-align:center; padding:25px; color:#8e8e93;">No matching records found.</td></tr>');
      }
    },
    error: function() {
      $('#tbodyQueueRecords').html('<tr><td colspan="5" style="text-align:center; padding:25px; color:#ff3b30;">Failed to load records.</td></tr>');
    }
  });
}

function renderQueueTable(leads) {
  var html = '';
  if (!leads || leads.length === 0) {
    $('#tbodyQueueRecords').html('<tr><td colspan="5" style="text-align:center; padding:25px; color:#8e8e93;">No contacts found in this list.</td></tr>');
    return;
  }

  for (var i = 0; i < leads.length; i++) {
    var l = leads[i];
    var isCurrent = (activeLead && activeLead.id == l.id);
    var p1 = l.phone_primary || l.contact1 || '';
    var p2 = l.phone_alt || l.alt_contact || '';
    var dateTxt = l.followup_date ? (l.followup_date + (l.followup_time ? ' ' + l.followup_time : '')) : 'Not scheduled';

    html += '<tr class="' + (isCurrent ? 'active-row' : '') + '" id="rowLead_' + l.id + '">';
    html += '<td>';
    html += '<strong style="color:#1d1d1f; font-size:13px;">' + escapeHtml(l.customer || 'Unnamed') + '</strong>';
    if (l.poc) {
      html += '<div style="font-size:11.5px; color:#8e8e93;">' + escapeHtml(l.poc) + '</div>';
    }
    html += '</td>';

    html += '<td>';
    if (p1) {
      html += '<div><a href="javascript:void(0)" style="color:#0072f0; text-decoration:none; font-weight:600;" onclick="quickDialNumber(\'' + escapeJs(p1) + '\', ' + l.id + ')"><i class="fa fa-phone"></i> ' + escapeHtml(p1) + '</a></div>';
    }
    if (p2) {
      html += '<div><a href="javascript:void(0)" style="color:#8e8e93; text-decoration:none;" onclick="quickDialNumber(\'' + escapeJs(p2) + '\', ' + l.id + ')"><i class="fa fa-mobile"></i> ' + escapeHtml(p2) + '</a></div>';
    }
    if (!p1 && !p2) {
      html += '<span style="color:#8e8e93;">No Phone</span>';
    }
    html += '</td>';

    html += '<td><span style="font-weight:600; color:#1d1d1f;">' + escapeHtml(l.status_name || 'Open') + '</span></td>';
    html += '<td style="font-size:11.5px; color:#8e8e93; font-weight:500;">' + escapeHtml(dateTxt) + '</td>';

    html += '<td style="text-align:right; white-space:nowrap;">';
    html += '<button type="button" class="apple-quick-call-btn" title="Call" onclick="selectLeadRow(' + i + ', true)"><i class="fa fa-phone"></i></button>';
    html += '<button type="button" class="btn-edit-row" onclick="viewLeadDetailsById(' + l.id + ')"><i class="fa fa-pencil"></i> Details</button>';
    html += '</td>';
    html += '</tr>';
  }

  $('#tbodyQueueRecords').html(html);
}

function filterQueueTable() {
  var q = $('#inputQueueSearch').val().toLowerCase();
  $('#tbodyQueueRecords tr').each(function() {
    var txt = $(this).text().toLowerCase();
    if (txt.indexOf(q) > -1) {
      $(this).show();
    } else {
      $(this).hide();
    }
  });
}

function selectLeadRow(idx, andDial) {
  if (currentQueue && currentQueue[idx]) {
    currentIndex = idx;
    applyLeadToDialer(currentQueue[idx], idx);
    $('.queue-table tr').removeClass('active-row');
    $('#rowLead_' + currentQueue[idx].id).addClass('active-row');
    if (andDial) {
      startDialing();
    }
  }
}

function quickDialNumber(num, leadId) {
  $('#dialerInput').val(num);
  if (leadId) {
    for (var i = 0; i < currentQueue.length; i++) {
      if (currentQueue[i].id == leadId) {
        applyLeadToDialer(currentQueue[i], i);
        break;
      }
    }
  }
  startDialing();
}

function applyLeadToDialer(lead, idx) {
  activeLead = lead;
  currentIndex = idx || 0;

  var custName = lead.customer || 'No Contact';
  var p1 = lead.phone_primary || lead.contact1 || '';

  $('#dialerChipName').text(custName);
  $('#dialerContactChip').css('display', 'inline-flex');
  $('#dialerInput').val(p1);

  populateDetailsForm(lead);
}

function populateDetailsForm(l) {
  if (!l) return;
  $('#editLeadId').val(l.id || 0);
  $('#editCallLogId').val(0);
  $('#detailsHeaderName').text(l.customer || 'Contact Details');

  $('#editCustomer').val(l.customer || '');
  $('#editPoc').val(l.poc || '');
  $('#editContact1').val(l.contact1 || l.phone_primary || '');
  $('#editAltContact').val(l.alt_contact || l.phone_alt || '');
  $('#editEmail').val(l.email || '');
  $('#editAuthority').val(l.tl_authority || '');
  $('#editLocation').val(l.location || l.address || '');

  var dnd = l.dnd_status || 'No';
  $('#editDndStatus').val(dnd);
  if (dnd === 'Yes') {
    $('#wrapDncrDate').show();
    $('#editDndDate').val(l.dnd_check_date || '');
  } else {
    $('#wrapDncrDate').hide();
    $('#editDndDate').val('');
  }

  if (l.s_cat) $('#editSCat').val(l.s_cat);
  if (l.p_cat) $('#editPCat').val(l.p_cat);
  $('#editTlNumber').val(l.tl_number || '');

  $('#editQty').val(l.qty || 1);
  $('#editMrc').val(l.mrc || '');
  $('#editRevenue').val(l.revenue || '');
  calculateMrrTotal();

  if (l.status) $('#editStatus').val(l.status);
  $('#editRemark').val(l.remark || '');
  $('#editNextFollowup').val(l.followup_date || '');
  $('#editNextFollowupTime').val(l.followup_time || '11:00 AM');
}

function toggleDncrDateRow() {
  if ($('#editDndStatus').val() === 'Yes') {
    $('#wrapDncrDate').show();
  } else {
    $('#wrapDncrDate').hide();
  }
}

function calculateMrrTotal() {
  var qty = parseFloat($('#editQty').val()) || 1;
  var mrc = parseFloat($('#editMrc').val()) || 0;
  var rev = parseFloat($('#editRevenue').val()) || 0;
  var total = (mrc * qty) + rev;
  $('#editMrr').val(total.toFixed(2));
}

function viewLeadDetailsById(id) {
  $.ajax({
    url: '<?php echo base_url(); ?>manager/calls/ajax_get_lead/' + id,
    type: 'GET',
    dataType: 'json',
    success: function(res) {
      if (res && res.status && res.lead) {
        populateDetailsForm(res.lead);
        hideAllMiddleViews();
        $('#middleViewDetails').show();
      } else {
        showToast('Failed to load contact details');
      }
    }
  });
}

function backToListFromDetails() {
  hideAllMiddleViews();
  if (activeQueueType === 'team') {
    $('#middleViewTeam').show();
  } else {
    $('#middleViewList').show();
  }
}

function saveRecordDetailsForm() {
  var fd = $('#formRecordDetails').serialize();
  $.ajax({
    url: '<?php echo base_url(); ?>manager/calls/ajax_save_record_details',
    type: 'POST',
    data: fd,
    dataType: 'json',
    success: function(res) {
      if (res && res.status) {
        showToast('Record details saved');
        backToListFromDetails();
        reloadCurrentQueue();
      } else {
        showToast(res && res.message ? res.message : 'Error saving record');
      }
    },
    error: function() {
      showToast('Network error while saving details');
    }
  });
}

function openMenuLogs() {
  $('.crm-dock-btn').removeClass('active');
  $('#dockBtnLogs').addClass('active');

  $('#subnavFollowup').hide();
  $('#subnavLogsPeriod').show();

  $('#middleAreaTitle').text('Calling Reports');
  hideAllMiddleViews();
  $('#middleViewLogs').show();
  openMiddleArea();

  loadDailyLogsData(currentLogsPeriod);
}

function changeLogsPeriod(period) {
  currentLogsPeriod = period;
  $('#subnavLogsPeriod .apple-segment-btn').removeClass('active');
  $('#subnavLogsPeriod .apple-segment-btn[data-period="' + period + '"]').addClass('active');
  loadDailyLogsData(period);
}

function loadDailyLogsData(period) {
  period = period || currentLogsPeriod || 'day';
  $('#tbodyStatusBreakdown').html('<tr><td colspan="5" style="text-align:center; padding:15px; color:#8e8e93;"><i class="fa fa-spinner fa-spin"></i> Loading breakdown...</td></tr>');
  $('#tbodyRecentLogs').html('<tr><td colspan="7" style="text-align:center; padding:15px; color:#8e8e93;"><i class="fa fa-spinner fa-spin"></i> Loading history...</td></tr>');

  $.ajax({
    url: '<?php echo base_url(); ?>manager/calls/ajax_get_daily_logs',
    type: 'GET',
    data: { period: period },
    dataType: 'json',
    success: function(res) {
      if (res && res.status) {
        if (res.stats) {
          $('#logStatTotal').text(res.stats.total_calls || 0);
          $('#logStatConnected').text(res.stats.connected_calls || 0);
          $('#logStatNewCalls').text(res.stats.new_calls || 0);
          $('#logStatFollowups').text(res.stats.followup_calls || 0);
          var durSec = parseInt(res.stats.total_duration) || 0;
          var durMin = Math.round(durSec / 60);
          $('#logStatDuration').text(durMin + 'm');
        }

        renderStatusBreakdown(res.status_breakdown);
        renderRecentLogs(res.logs);
      }
    },
    error: function() {
      $('#tbodyStatusBreakdown').html('<tr><td colspan="5" style="text-align:center; padding:15px; color:#ff3b30;">Failed to load statistics.</td></tr>');
    }
  });
}

function renderStatusBreakdown(items) {
  var html = '';
  if (!items || items.length === 0) {
    html = '<tr><td colspan="5" style="text-align:center; padding:15px; color:#8e8e93;">No calling activity found for this period.</td></tr>';
  } else {
    for (var i = 0; i < items.length; i++) {
      var item = items[i];
      html += '<tr>';
      html += '  <td><strong style="color:#1d1d1f;">' + escapeHtml(item.status_name) + '</strong></td>';
      html += '  <td style="text-align:center; color:#0072f0; font-weight:600;">' + item.new_calls + '</td>';
      html += '  <td style="text-align:center; color:#ff9500; font-weight:600;">' + item.followup_calls + '</td>';
      html += '  <td style="text-align:center; font-weight:700; color:#1d1d1f;">' + item.total_calls + '</td>';
      html += '  <td style="text-align:right; font-weight:600; color:#1d1d1f;">' + item.percentage + '%</td>';
      html += '</tr>';
    }
  }
  $('#tbodyStatusBreakdown').html(html);
}

function renderRecentLogs(logs) {
  var html = '';
  if (!logs || logs.length === 0) {
    html = '<tr><td colspan="7" style="text-align:center; padding:15px; color:#8e8e93;">No recent calls found.</td></tr>';
  } else {
    for (var i = 0; i < logs.length; i++) {
      var log = logs[i];
      var phone = log.phone_number || '';
      var cust = log.customer_name || (log.lead_customer ? log.lead_customer : 'Direct Call');
      var cat = log.call_category === 'followup' ? '<span style="color:#ff9500; font-weight:600;">Follow-up</span>' : '<span style="color:#0072f0; font-weight:600;">New Call</span>';

      html += '<tr>';
      html += '  <td style="font-size:11.5px; color:#8e8e93; font-weight:500;">' + escapeHtml(log.created_at) + '</td>';
      html += '  <td><strong style="color:#1d1d1f;">' + escapeHtml(cust) + '</strong></td>';
      html += '  <td><a href="javascript:void(0)" style="color:#0072f0; text-decoration:none; font-weight:600;" onclick="quickDialNumber(\'' + escapeJs(phone) + '\')"><i class="fa fa-phone"></i> ' + escapeHtml(phone) + '</a></td>';
      html += '  <td>' + cat + '</td>';
      html += '  <td><span style="font-weight:600; color:#1d1d1f;">' + escapeHtml(log.disposition || log.call_status) + '</span></td>';
      html += '  <td>' + (log.duration || 0) + 's</td>';
      html += '  <td style="text-align:right;">';
      html += '    <button type="button" class="apple-quick-call-btn" title="Call" onclick="quickDialNumber(\'' + escapeJs(phone) + '\')"><i class="fa fa-phone"></i></button>';
      html += '    <button type="button" class="btn-edit-row" onclick="copyNumber(\'' + escapeJs(phone) + '\')"><i class="fa fa-copy"></i></button>';
      html += '  </td>';
      html += '</tr>';
    }
  }
  $('#tbodyRecentLogs').html(html);
}

function handleCallButtonClick() {
  if (callState === 'idle') {
    startDialing();
  } else if (callState === 'calling' || callState === 'connected') {
    endActiveCall();
  }
}

function startDialing() {
  var phone = $('#dialerInput').val().trim();
  if (!phone) {
    showToast('Please enter a phone number');
    return;
  }

  callState = 'calling';
  $('#btnToggleCall').removeClass('btn-connected').addClass('btn-calling');
  $('#dialerStatusSubtitle').text('Dialing ' + phone + '...');

  var isInternal = (activeLead && activeLead.is_internal) ? 1 : 0;
  var category = isInternal ? 'internal' : (activeQueueType === 'followup' ? 'followup' : 'new_call');

  $.ajax({
    url: '<?php echo base_url(); ?>manager/calls/ajax_initiate_call',
    type: 'POST',
    data: {
      phone: phone,
      lead_id: activeLead ? activeLead.id : 0,
      customer_name: activeLead ? activeLead.customer : 'Direct Call',
      is_internal: isInternal,
      call_category: category
    },
    dataType: 'json',
    success: function(res) {
      if (res && res.status) {
        activeCallLogId = res.call_log_id || 0;
        onCallConnected();
      } else {
        setTimeout(function() {
          onCallConnected();
        }, 1200);
      }
    },
    error: function() {
      setTimeout(function() {
        onCallConnected();
      }, 1200);
    }
  });
}

function onCallConnected() {
  callState = 'connected';
  callConnectedSeconds = 0;
  $('#btnToggleCall').removeClass('btn-calling').addClass('btn-connected');
  $('#dialerStatusSubtitle').text('Connected 00:00');

  clearInterval(callTimerInterval);
  callTimerInterval = setInterval(function() {
    callConnectedSeconds++;
    var m = Math.floor(callConnectedSeconds / 60);
    var s = callConnectedSeconds % 60;
    var timeStr = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
    $('#dialerStatusSubtitle').text('Connected ' + timeStr);
    $('#editCallDuration').val(callConnectedSeconds);
  }, 1000);
}

function endActiveCall() {
  clearInterval(callTimerInterval);
  callState = 'idle';
  $('#btnToggleCall').removeClass('btn-calling btn-connected');
  $('#dialerStatusSubtitle').text('Call ended (' + callConnectedSeconds + 's)');

  var leadId = activeLead ? activeLead.id : 0;
  var phone = $('#dialerInput').val().trim();
  var isInternal = (activeLead && activeLead.is_internal) ? 1 : 0;
  var category = isInternal ? 'internal' : (activeQueueType === 'followup' ? 'followup' : 'new_call');
  var remarks = $('#editRemark').val() || $('#manualDialRemarks').val() || '';

  $.ajax({
    url: '<?php echo base_url(); ?>manager/calls/ajax_save_call',
    type: 'POST',
    data: {
      call_log_id: activeCallLogId,
      lead_id: leadId,
      customer_name: activeLead ? activeLead.customer : 'Direct Call',
      phone: phone,
      call_duration: callConnectedSeconds,
      call_disposition: 'Connected',
      is_internal: isInternal,
      call_category: category,
      remarks: remarks || 'Call completed'
    },
    dataType: 'json',
    success: function() {
      showToast('Call logged');
      loadRecentManualCalls();
    }
  });

  var useGroqAi = $('#autoDialGroqAi').is(':checked');
  if (useGroqAi && callConnectedSeconds > 0) {
    $.ajax({
      url: '<?php echo base_url(); ?>manager/calls/ajax_groq_quick_summary',
      type: 'POST',
      data: {
        call_log_id: activeCallLogId,
        lead_id: leadId,
        customer_name: activeLead ? activeLead.customer : '',
        phone: phone,
        call_duration: callConnectedSeconds,
        call_disposition: 'Connected',
        remarks: remarks
      },
      dataType: 'json',
      success: function(res) {
        if (res && res.status && res.summary) {
          $('#autoDialAiBadge').html('<strong><i class="fa fa-magic" style="color:#0072f0;"></i> Groq AI Summary:</strong> ' + escapeHtml(res.summary) + '<br><small style="color:#475569;"><strong>Next Action:</strong> ' + escapeHtml(res.next_action) + '</small>').show();
          if ($('#editRemark').length) {
            $('#editRemark').val(res.summary);
          }
        }
      }
    });
  }

  if (autoDialerActive && !autoDialerPaused && !isOnBreak) {
    autoDialerIndex++;
    startAutoDialCountdown();
  }
}

function toggleManualDncrDateRow() {
  if ($('#manualEditDndStatus').val() === 'Yes') {
    $('#manualWrapDncrDate').show();
  } else {
    $('#manualWrapDncrDate').hide();
  }
}

function calculateManualMrrTotal() {
  var qty = parseFloat($('#manualEditQty').val()) || 1;
  var mrc = parseFloat($('#manualEditMrc').val()) || 0;
  var rev = parseFloat($('#manualEditRevenue').val()) || 0;
  var total = (qty * mrc) + rev;
  $('#manualEditMrr').val(total > 0 ? total.toFixed(2) : '');
}

function changeManualDialQueue(qType) {
  manualQueueType = qType || 'database';
  loadManualDialerQueue(manualQueueType);
}

function loadManualDialerQueue(qType) {
  manualQueueType = qType || 'database';
  $('#manualDialProgressBadge').text('Loading queue...');
  $.ajax({
    url: '<?php echo base_url(); ?>manager/calls/ajax_get_queue',
    type: 'GET',
    data: {
      type: manualQueueType,
      filter: 'all'
    },
    dataType: 'json',
    success: function(res) {
      if (res && res.status && res.leads && res.leads.length > 0) {
        manualDialerQueue = res.leads;
        manualDialerIndex = 0;
        displayManualLead(0);
      } else {
        manualDialerQueue = [];
        manualDialerIndex = 0;
        newBlankManualContact();
        $('#manualDialProgressBadge').text('0 contacts in queue');
      }
    },
    error: function() {
      manualDialerQueue = [];
      $('#manualDialProgressBadge').text('Error loading queue');
    }
  });
}

function displayManualLead(idx) {
  if (!manualDialerQueue || manualDialerQueue.length === 0) {
    newBlankManualContact();
    return;
  }
  if (idx < 0) idx = 0;
  if (idx >= manualDialerQueue.length) {
    showToast('Reached end of queue');
    return;
  }
  manualDialerIndex = idx;
  var l = manualDialerQueue[idx];

  $('#manualDetailsHeaderName').text(l.customer ? ('Manual Call - ' + l.customer) : 'Manual Call');
  $('#manualDialProgressBadge').text('Contact ' + (idx + 1) + ' of ' + manualDialerQueue.length);

  $('#manualEditLeadId').val(l.id || 0);
  $('#manualEditCallLogId').val(0);
  $('#manualEditCallDuration').val(0);

  $('#manualEditCustomer').val(l.customer || '');
  $('#manualEditPoc').val(l.poc || '');
  $('#manualEditContact1').val(l.contact1 || l.phone_primary || '');
  $('#manualEditAltContact').val(l.alt_contact || l.phone_alt || '');
  $('#manualEditEmail').val(l.email || '');
  $('#manualEditAuthority').val(l.tl_authority || '');
  $('#manualEditLocation').val(l.location || l.address || '');

  var dnd = l.dnd_status || 'No';
  $('#manualEditDndStatus').val(dnd);
  if (dnd === 'Yes') {
    $('#manualWrapDncrDate').show();
    $('#manualEditDndDate').val(l.dnd_check_date || '');
  } else {
    $('#manualWrapDncrDate').hide();
    $('#manualEditDndDate').val('');
  }

  $('#manualEditQty').val(l.qty || 1);
  $('#manualEditMrc').val(l.mrc || '');
  $('#manualEditRevenue').val(l.revenue || '');
  calculateManualMrrTotal();

  if (l.status) {
    $('#manualEditStatus').val(l.status);
  }
  $('#manualEditNextFollowup').val(l.followup_date || '');
  $('#manualEditNextFollowupTime').val(l.followup_time || '11:00 AM');
  $('#manualEditRemark').val(l.remark || '');

  applyLeadToDialer(l, idx);
}

function dialActiveManualContact() {
  var phone = $('#manualEditContact1').val().trim() || $('#manualEditAltContact').val().trim();
  if (!phone) {
    showToast('Please enter a phone number to dial');
    return;
  }
  $('#dialerInput').val(phone);
  var cust = $('#manualEditCustomer').val().trim() || 'Manual Contact';
  $('#dialerChipName').text(cust);
  $('#dialerContactChip').css('display', 'inline-flex');
  startDialing();
}

function dialManualPhoneField(which) {
  var phone = (which === 'primary') ? $('#manualEditContact1').val().trim() : $('#manualEditAltContact').val().trim();
  if (!phone) {
    showToast('No phone number entered in this field');
    return;
  }
  $('#dialerInput').val(phone);
  startDialing();
}

function saveManualRecordDetails(andNext) {
  var leadId = parseInt($('#manualEditLeadId').val(), 10) || 0;
  var phone = $('#manualEditContact1').val().trim();
  var cust = $('#manualEditCustomer').val().trim();

  if (!phone && !cust) {
    showToast('Please enter at least a company name or phone number');
    return;
  }

  if (leadId > 0) {
    var fd = $('#formManualDetails').serialize();
    $.ajax({
      url: '<?php echo base_url(); ?>manager/calls/ajax_save_record_details',
      type: 'POST',
      data: fd,
      dataType: 'json',
      success: function(res) {
        if (res && res.status) {
          if (andNext) {
            proceedToNextManualLead(true);
          } else {
            showToast('Contact details saved');
          }
        } else {
          showToast(res && res.message ? res.message : 'Failed to save details');
        }
      },
      error: function() {
        showToast('Error saving contact details');
      }
    });
  } else {
    $.ajax({
      url: '<?php echo base_url(); ?>manager/calls/ajax_save_manual_contact',
      type: 'POST',
      data: {
        phone: phone,
        customer: cust,
        poc: $('#manualEditPoc').val().trim(),
        email: $('#manualEditEmail').val().trim(),
        status: $('#manualEditStatus').val(),
        remark: $('#manualEditRemark').val().trim(),
        followup_date: $('#manualEditNextFollowup').val().trim()
      },
      dataType: 'json',
      success: function(res) {
        if (res && res.status) {
          showToast('New Database Record: ' + res.ref_no);
          if (andNext) {
            proceedToNextManualLead(true);
          }
        } else {
          showToast(res && res.message ? res.message : 'Failed to create record');
        }
      },
      error: function() {
        showToast('Error creating database record');
      }
    });
  }
}

function saveAndNextManualLead() {
  if (callState === 'calling' || callState === 'connected') {
    endActiveCall();
  }
  saveManualRecordDetails(true);
}

function proceedToNextManualLead(autoDial) {
  if (manualDialerQueue && manualDialerQueue.length > 0 && (manualDialerIndex + 1) < manualDialerQueue.length) {
    manualDialerIndex++;
    displayManualLead(manualDialerIndex);
    if (autoDial) {
      setTimeout(function() {
        dialActiveManualContact();
      }, 800);
    }
  } else {
    showToast('Queue completed! All contacts called.');
  }
}

function skipManualLead() {
  if (callState === 'calling' || callState === 'connected') {
    endActiveCall();
  }
  if (manualDialerQueue && manualDialerQueue.length > 0 && (manualDialerIndex + 1) < manualDialerQueue.length) {
    manualDialerIndex++;
    displayManualLead(manualDialerIndex);
    showToast('Skipped to next contact');
    setTimeout(function() {
      dialActiveManualContact();
    }, 600);
  } else {
    showToast('Reached end of queue');
  }
}

function prevManualLead() {
  if (manualDialerQueue && manualDialerQueue.length > 0 && manualDialerIndex > 0) {
    manualDialerIndex--;
    displayManualLead(manualDialerIndex);
  } else {
    showToast('Already at the first contact');
  }
}

function newBlankManualContact() {
  $('#manualDetailsHeaderName').text('New Manual Prospect');
  $('#manualEditLeadId').val(0);
  $('#manualEditCallLogId').val(0);
  $('#manualEditCallDuration').val(0);
  $('#manualEditCustomer').val('');
  $('#manualEditPoc').val('');
  $('#manualEditContact1').val('');
  $('#manualEditAltContact').val('');
  $('#manualEditEmail').val('');
  $('#manualEditAuthority').val('');
  $('#manualEditLocation').val('');
  $('#manualEditDndStatus').val('No');
  $('#manualWrapDncrDate').hide();
  $('#manualEditDndDate').val('');
  $('#manualEditQty').val(1);
  $('#manualEditMrc').val('');
  $('#manualEditRevenue').val('');
  $('#manualEditMrr').val('');
  $('#manualEditNextFollowup').val('');
  $('#manualEditNextFollowupTime').val('11:00 AM');
  $('#manualEditRemark').val('');
  $('#manualDialProgressBadge').text('Manual Entry');
  $('#manualEditCustomer').focus();
}

function escapeHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function escapeJs(str) {
  if (!str) return '';
  return String(str)
    .replace(/\\/g, '\\\\')
    .replace(/'/g, "\\'")
    .replace(/"/g, '\\"')
    .replace(/\n/g, '\\n')
    .replace(/\r/g, '');
}

$(document).ready(function() {
  if ($('.datepicker').length && $.fn.datepicker) {
    $('.datepicker').datepicker({
      format: 'yyyy-mm-dd',
      autoclose: true
    });
  }
  loadTeamColleaguesData(true);
});
</script>
