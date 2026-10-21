<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>

<style>
:root {
  --crm-bg: #f8fafc;
  --crm-card-bg: #ffffff;
  --crm-border: #e2e8f0;
  --crm-border-dark: #cbd5e1;
  --crm-text: #0f172a;
  --crm-text-muted: #64748b;
  --crm-primary: #2563eb;
  --crm-primary-hover: #1d4ed8;
  --crm-call: #16a34a;
  --crm-call-hover: #15803d;
  --crm-endcall: #dc2626;
  --crm-endcall-hover: #b91c1c;
  --crm-pill-bg: #f1f5f9;
}

[data-theme="dark"] {
  --crm-bg: #090e17;
  --crm-card-bg: #111827;
  --crm-border: #1f2937;
  --crm-border-dark: #374151;
  --crm-text: #f3f4f6;
  --crm-text-muted: #9ca3af;
  --crm-primary: #3b82f6;
  --crm-primary-hover: #2563eb;
  --crm-call: #22c55e;
  --crm-call-hover: #16a34a;
  --crm-endcall: #ef4444;
  --crm-endcall-hover: #dc2626;
  --crm-pill-bg: #1f2937;
}

.calling-screen-wrapper {
  background: var(--crm-bg);
  height: calc(100vh - 75px);
  padding: 8px 16px 10px 16px;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  color: var(--crm-text);
}

@media (max-height: 720px) {
  .calling-screen-wrapper {
    overflow-y: auto;
    height: auto;
    min-height: calc(100vh - 75px);
  }
}

.cw-topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 8px;
  flex-shrink: 0;
}

.cw-search-box {
  position: relative;
  flex: 1;
  max-width: 420px;
}

.cw-search-box i {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--crm-text-muted);
  font-size: 13px;
  pointer-events: none;
}

.cw-search-input-field {
  width: 100%;
  padding: 6px 12px 6px 32px;
  background: var(--crm-card-bg);
  border: 1px solid var(--crm-border);
  border-radius: 8px;
  font-size: 12px;
  color: var(--crm-text);
  outline: none;
  transition: all 0.15s ease;
}

.cw-search-input-field:focus {
  border-color: var(--crm-primary);
  box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.12);
}

.cw-topbar-controls {
  display: flex;
  align-items: center;
  gap: 8px;
}

.cw-topbar-select {
  padding: 6px 10px;
  background: var(--crm-card-bg);
  border: 1px solid var(--crm-border);
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  color: var(--crm-text);
  outline: none;
  cursor: pointer;
}

.cw-btn-top {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  border: 1px solid var(--crm-border);
  background: var(--crm-card-bg);
  color: var(--crm-text);
  cursor: pointer;
  transition: all 0.15s ease;
  text-decoration: none;
}

.cw-btn-top:hover {
  background: var(--crm-pill-bg);
  color: var(--crm-text);
}

.cw-btn-top.primary {
  background: var(--crm-primary);
  color: #ffffff;
  border-color: var(--crm-primary);
}

.cw-btn-top.primary:hover {
  background: var(--crm-primary-hover);
  color: #ffffff;
}

.cw-workstation-body {
  display: grid;
  grid-template-columns: 320px 1fr;
  gap: 12px;
  flex: 1;
  min-height: 0;
  overflow: hidden;
}

@media (max-width: 900px) {
  .calling-screen-wrapper {
    height: auto;
    overflow-y: auto;
  }
  .cw-workstation-body {
    grid-template-columns: 1fr;
    overflow: visible;
  }
}

.cw-card {
  background: var(--crm-card-bg);
  border: 1px solid var(--crm-border);
  border-radius: 10px;
  padding: 12px 14px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
  box-sizing: border-box;
}

.cw-left-column {
  display: flex;
  flex-direction: column;
  gap: 10px;
  height: 100%;
}

.cw-dialer-card {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 12px;
}

.cw-tab-nav {
  display: flex;
  background: var(--crm-pill-bg);
  border-radius: 8px;
  padding: 3px;
  margin-bottom: 8px;
}

.cw-tab-btn {
  flex: 1;
  padding: 6px 8px;
  font-size: 11px;
  font-weight: 600;
  border: none;
  background: transparent;
  color: var(--crm-text-muted);
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.15s ease;
  text-align: center;
}

.cw-tab-btn.active {
  background: var(--crm-card-bg);
  color: var(--crm-text);
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.cw-phone-input-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: var(--crm-pill-bg);
  border-radius: 8px;
  padding: 6px 12px;
  margin-bottom: 8px;
  border: 1px solid var(--crm-border);
}

.cw-phone-text {
  width: 100%;
  background: transparent;
  border: none;
  font-size: 19px;
  font-weight: 700;
  color: var(--crm-text);
  outline: none;
  letter-spacing: 0.5px;
}

.cw-backspace-btn {
  background: transparent;
  border: none;
  color: var(--crm-text-muted);
  cursor: pointer;
  padding: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.cw-backspace-btn:hover {
  color: var(--crm-text);
}

.cw-dial-matrix {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 6px;
  margin-bottom: 8px;
}

.cw-num-btn {
  height: 38px;
  background: var(--crm-pill-bg);
  border: 1px solid var(--crm-border);
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  transition: all 0.12s ease;
  user-select: none;
}

.cw-num-btn:hover {
  background: var(--crm-border);
  transform: translateY(-1px);
}

.cw-num-btn:active {
  transform: scale(0.97);
}

.cw-num-digit {
  font-size: 15px;
  font-weight: 700;
  color: var(--crm-text);
  line-height: 1;
}

.cw-num-letters {
  font-size: 8px;
  font-weight: 600;
  color: var(--crm-text-muted);
  text-transform: uppercase;
  margin-top: 1px;
}

.cw-call-button {
  width: 100%;
  height: 38px;
  background: var(--crm-call);
  color: #ffffff;
  border: none;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  cursor: pointer;
  transition: all 0.15s ease;
  box-shadow: 0 2px 6px rgba(22, 163, 74, 0.25);
}

.cw-call-button:hover {
  background: var(--crm-call-hover);
}

.cw-audio-status-pill {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 12px;
  background: var(--crm-card-bg);
  border: 1px solid var(--crm-border);
  border-radius: 8px;
  font-size: 11px;
  color: var(--crm-text-muted);
}

.cw-audio-status-pill strong {
  color: var(--crm-text);
}

.cw-right-column {
  display: flex;
  flex-direction: column;
  gap: 8px;
  height: 100%;
  overflow: hidden;
}

.cw-active-banner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 14px;
  border-radius: 10px;
  background: var(--crm-card-bg);
  border: 1px solid var(--crm-border);
  flex-shrink: 0;
  gap: 12px;
  flex-wrap: wrap;
}

.cw-active-left {
  display: flex;
  align-items: center;
  gap: 10px;
}

.cw-avatar-initials {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  background: #eff6ff;
  color: #2563eb;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 700;
  flex-shrink: 0;
  border: 1px solid #dbeafe;
}

.cw-active-info-col {
  display: flex;
  flex-direction: column;
}

.cw-active-customer-name {
  font-size: 15px;
  font-weight: 700;
  color: var(--crm-text);
  line-height: 1.2;
}

.cw-active-status-line {
  font-size: 11px;
  color: var(--crm-text-muted);
  display: flex;
  align-items: center;
  gap: 6px;
  margin-top: 2px;
}

.cw-status-indicator-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #94a3b8;
}

.cw-status-indicator-dot.pulsing {
  background: #16a34a;
  box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.25);
  animation: cwIndicatorPulse 1.5s infinite;
}

@keyframes cwIndicatorPulse {
  0% { transform: scale(0.95); opacity: 0.9; }
  50% { transform: scale(1.15); opacity: 1; }
  100% { transform: scale(0.95); opacity: 0.9; }
}

.cw-call-action-btns {
  display: flex;
  align-items: center;
  gap: 6px;
}

.cw-ctrl-btn {
  padding: 5px 10px;
  background: var(--crm-pill-bg);
  border: 1px solid var(--crm-border);
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
  color: var(--crm-text);
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  transition: all 0.15s ease;
}

.cw-ctrl-btn:hover {
  background: var(--crm-border);
}

.cw-ctrl-btn.active {
  background: #fee2e2;
  color: #dc2626;
  border-color: #fca5a5;
}

.cw-btn-hangup {
  padding: 5px 14px;
  background: var(--crm-endcall);
  color: #ffffff;
  border: none;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 700;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  transition: all 0.15s ease;
}

.cw-btn-hangup:hover {
  background: var(--crm-endcall-hover);
}

.cw-lead-meta-strip {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 8px;
  flex-shrink: 0;
}

.cw-meta-box {
  background: var(--crm-card-bg);
  border: 1px solid var(--crm-border);
  border-radius: 8px;
  padding: 6px 10px;
  display: flex;
  flex-direction: column;
}

.cw-meta-label {
  font-size: 9px;
  font-weight: 700;
  text-transform: uppercase;
  color: var(--crm-text-muted);
  letter-spacing: 0.3px;
}

.cw-meta-content {
  font-size: 12px;
  font-weight: 600;
  color: var(--crm-text);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-top: 1px;
}

.cw-meta-content a {
  color: var(--crm-primary);
  text-decoration: none;
}

.cw-meta-content a:hover {
  text-decoration: underline;
}

.cw-form-and-history-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 8px;
  flex: 1;
  min-height: 0;
}

.cw-notes-container {
  display: flex;
  flex-direction: column;
  padding: 10px 14px;
  justify-content: space-between;
}

.cw-form-top-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 6px;
  gap: 10px;
}

.cw-card-title {
  font-size: 13px;
  font-weight: 700;
  color: var(--crm-text);
  display: flex;
  align-items: center;
  gap: 6px;
}

.cw-disposition-dropdown {
  padding: 5px 10px;
  background: var(--crm-pill-bg);
  border: 1px solid var(--crm-border-dark);
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  color: var(--crm-text);
  outline: none;
  cursor: pointer;
}

.cw-notes-textarea {
  width: 100%;
  height: 60px;
  padding: 8px 10px;
  background: var(--crm-pill-bg);
  border: 1px solid var(--crm-border);
  border-radius: 6px;
  font-size: 12px;
  color: var(--crm-text);
  outline: none;
  resize: none;
  font-family: inherit;
  box-sizing: border-box;
}

.cw-notes-textarea:focus {
  border-color: var(--crm-primary);
  background: var(--crm-card-bg);
}

.cw-form-bottom-row {
  display: grid;
  grid-template-columns: 1fr 1fr auto;
  gap: 8px;
  align-items: end;
  margin-top: 8px;
}

.cw-inline-field {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.cw-inline-label {
  font-size: 10px;
  font-weight: 700;
  color: var(--crm-text-muted);
  text-transform: uppercase;
}

.cw-inline-input {
  padding: 5px 8px;
  background: var(--crm-pill-bg);
  border: 1px solid var(--crm-border);
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
  color: var(--crm-text);
  outline: none;
}

.cw-inline-buttons {
  display: flex;
  align-items: center;
  gap: 6px;
}

.cw-btn-save {
  padding: 6px 14px;
  background: var(--crm-primary);
  color: #ffffff;
  border: none;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  transition: all 0.15s ease;
}

.cw-btn-save:hover {
  background: var(--crm-primary-hover);
}

.cw-btn-next {
  padding: 6px 10px;
  background: var(--crm-pill-bg);
  border: 1px solid var(--crm-border-dark);
  color: var(--crm-text);
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.cw-btn-next:hover {
  background: var(--crm-border);
}

.cw-activity-panel {
  padding: 8px 12px;
  overflow-y: auto;
  max-height: 115px;
}

.cw-activity-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 4px 6px;
  border-bottom: 1px solid var(--crm-border);
  font-size: 11px;
}

.cw-activity-row:last-child {
  border-bottom: none;
}

.cw-activity-icon {
  margin-right: 6px;
  color: var(--crm-primary);
}

.cw-drawer-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(15, 23, 42, 0.45);
  backdrop-filter: blur(2px);
  z-index: 99998;
  display: none;
}

.cw-side-drawer {
  position: fixed;
  top: 0;
  right: -440px;
  width: 420px;
  max-width: 90vw;
  height: 100vh;
  background: var(--crm-card-bg);
  box-shadow: -6px 0 24px rgba(0, 0, 0, 0.15);
  z-index: 99999;
  display: flex;
  flex-direction: column;
  transition: right 0.28s cubic-bezier(0.16, 1, 0.3, 1);
  border-left: 1px solid var(--crm-border);
}

.cw-side-drawer.open {
  right: 0;
}

.cw-drawer-header {
  padding: 14px 18px;
  border-bottom: 1px solid var(--crm-border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: var(--crm-pill-bg);
}

.cw-drawer-title {
  font-size: 14px;
  font-weight: 700;
  color: var(--crm-text);
  display: flex;
  align-items: center;
  gap: 8px;
}

.cw-drawer-close {
  background: transparent;
  border: none;
  font-size: 16px;
  color: var(--crm-text-muted);
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 4px;
}

.cw-drawer-close:hover {
  color: var(--crm-text);
  background: var(--crm-border);
}

.cw-drawer-body {
  padding: 12px 16px;
  overflow-y: auto;
  flex: 1;
}

.cw-queue-card-item {
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid var(--crm-border);
  margin-bottom: 8px;
  cursor: pointer;
  background: var(--crm-card-bg);
  transition: all 0.12s ease;
}

.cw-queue-card-item:hover {
  background: var(--crm-pill-bg);
  border-color: var(--crm-primary);
  transform: translateY(-1px);
}

.cw-queue-card-item.active-lead {
  border-color: var(--crm-primary);
  background: #eff6ff;
  box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
}

.cw-qcard-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 12px;
}

.cw-qcard-top strong {
  color: var(--crm-text);
  font-size: 13px;
}

.cw-qcard-badge {
  font-size: 10px;
  font-weight: 700;
  padding: 2px 6px;
  border-radius: 4px;
  background: #dbeafe;
  color: #1e40af;
}

.cw-qcard-mid {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 11px;
  color: var(--crm-text-muted);
  margin-top: 4px;
}

.cw-qcard-phone {
  font-weight: 600;
  color: var(--crm-text);
}
</style>

<div class="calling-screen-wrapper">
  
  <div class="cw-topbar">
    <div class="cw-search-box">
      <i class="fa fa-search"></i>
      <input type="text" class="cw-search-input-field" id="cwSearchLeadsInput" placeholder="Search database records or phone numbers..." autocomplete="off" />
    </div>

    <div class="cw-topbar-controls">
      <select class="cw-topbar-select" id="cwQueueFilterSelect">
        <option value="all" <?php echo ($selected_status == 'all') ? 'selected' : ''; ?>>All Database (<?php echo $total_my_leads; ?>)</option>
        <option value="follow_up" <?php echo ($selected_status == 'follow_up') ? 'selected' : ''; ?>>Follow-up / Call Back</option>
        <option value="expected" <?php echo ($selected_status == 'expected') ? 'selected' : ''; ?>>Expected / Prospective</option>
        <option value="database" <?php echo ($selected_status == 'database') ? 'selected' : ''; ?>>Database Only</option>
        <option value="in_process" <?php echo ($selected_status == 'in_process') ? 'selected' : ''; ?>>In Process</option>
        <option value="submitted" <?php echo ($selected_status == 'submitted') ? 'selected' : ''; ?>>Submitted</option>
      </select>

      <button type="button" class="cw-btn-top" id="btnToggleQueueDrawer" title="Toggle Next Calls Queue Info">
        <i class="fa fa-list-ol" style="color: #2563eb;"></i>
        <span>Next Calls (<span id="queueRemainingBadge"><?php echo count($queue); ?></span> left)</span>
      </button>

      <button type="button" class="cw-btn-top primary" id="btnQuickNewCall">
        <i class="fa fa-plus"></i>
        <span>New Call</span>
      </button>
    </div>
  </div>

  <?php
    $cur_name = !empty($active_lead['customer']) ? $active_lead['customer'] : 'Ready to Dial';
    $cur_phone = !empty($active_lead['phone_primary']) ? $active_lead['phone_primary'] : (!empty($active_lead['phone_alt']) ? $active_lead['phone_alt'] : '');
    $cur_email = !empty($active_lead['email']) ? $active_lead['email'] : '—';
    $cur_poc = !empty($active_lead['poc']) ? $active_lead['poc'] : 'POC / Manager';
    $cur_lead_id = !empty($active_lead['id']) ? (int)$active_lead['id'] : 0;
    $cur_lead_code = !empty($active_lead['lead_id']) ? $active_lead['lead_id'] : '—';
    $cur_category = !empty($active_lead['category']) ? $active_lead['category'] : 'Corporate';
    $cur_initial = strtoupper(substr($cur_name, 0, 1));
  ?>

  <div class="cw-workstation-body">
    
    <div class="cw-left-column">
      <div class="cw-card cw-dialer-card">
        <div>
          <div class="cw-tab-nav">
            <button type="button" class="cw-tab-btn active" id="tabKeypadView">Keypad</button>
            <button type="button" class="cw-tab-btn" id="tabRecentView">Recent Calls</button>
          </div>

          <div id="panelKeypad">
            <div class="cw-phone-input-bar">
              <input type="text" class="cw-phone-text" id="cwDialerPhoneInput" value="<?php echo htmlspecialchars($cur_phone); ?>" placeholder="Enter number..." />
              <button type="button" class="cw-backspace-btn" id="cwBtnBackspace" title="Backspace">
                <i class="fa fa-arrow-left"></i>
              </button>
            </div>

            <div class="cw-dial-matrix">
              <button type="button" class="cw-num-btn" data-val="1">
                <span class="cw-num-digit">1</span>
                <span class="cw-num-letters">&nbsp;</span>
              </button>
              <button type="button" class="cw-num-btn" data-val="2">
                <span class="cw-num-digit">2</span>
                <span class="cw-num-letters">ABC</span>
              </button>
              <button type="button" class="cw-num-btn" data-val="3">
                <span class="cw-num-digit">3</span>
                <span class="cw-num-letters">DEF</span>
              </button>
              <button type="button" class="cw-num-btn" data-val="4">
                <span class="cw-num-digit">4</span>
                <span class="cw-num-letters">GHI</span>
              </button>
              <button type="button" class="cw-num-btn" data-val="5">
                <span class="cw-num-digit">5</span>
                <span class="cw-num-letters">JKL</span>
              </button>
              <button type="button" class="cw-num-btn" data-val="6">
                <span class="cw-num-digit">6</span>
                <span class="cw-num-letters">MNO</span>
              </button>
              <button type="button" class="cw-num-btn" data-val="7">
                <span class="cw-num-digit">7</span>
                <span class="cw-num-letters">PQRS</span>
              </button>
              <button type="button" class="cw-num-btn" data-val="8">
                <span class="cw-num-digit">8</span>
                <span class="cw-num-letters">TUV</span>
              </button>
              <button type="button" class="cw-num-btn" data-val="9">
                <span class="cw-num-digit">9</span>
                <span class="cw-num-letters">WXYZ</span>
              </button>
              <button type="button" class="cw-num-btn" data-val="*">
                <span class="cw-num-digit">*</span>
                <span class="cw-num-letters">&nbsp;</span>
              </button>
              <button type="button" class="cw-num-btn" data-val="0">
                <span class="cw-num-digit">0</span>
                <span class="cw-num-letters">+</span>
              </button>
              <button type="button" class="cw-num-btn" data-val="#">
                <span class="cw-num-digit">#</span>
                <span class="cw-num-letters">&nbsp;</span>
              </button>
            </div>

            <button type="button" class="cw-call-button" id="cwBtnStartCall">
              <i class="fa fa-phone"></i>
              <span>Call</span>
            </button>
          </div>

          <div id="panelRecent" style="display: none; max-height: 240px; overflow-y: auto;">
            <?php if (!empty($recent_logs)) { ?>
              <?php foreach (array_slice($recent_logs, 0, 8) as $r_item) { 
                $r_conn = ((int)$r_item['duration'] > 0);
                $r_icon = $r_conn ? 'fa-phone text-success' : 'fa-phone text-danger';
                $r_time = !empty($r_item['created_at']) ? date('h:i A', strtotime($r_item['created_at'])) : '';
              ?>
                <div class="cw-queue-card-item" data-phone="<?php echo htmlspecialchars($r_item['phone_number']); ?>" data-customer="<?php echo htmlspecialchars($r_item['customer_name']); ?>" data-lead-id="<?php echo (int)$r_item['lead_id']; ?>">
                  <div class="cw-qcard-top">
                    <strong><i class="fa <?php echo $r_icon; ?>" style="margin-right: 4px;"></i><?php echo htmlspecialchars($r_item['customer_name']); ?></strong>
                    <span class="cw-qcard-badge"><?php echo htmlspecialchars(!empty($r_item['disposition']) ? $r_item['disposition'] : 'Call'); ?></span>
                  </div>
                  <div class="cw-qcard-mid">
                    <span class="cw-qcard-phone"><?php echo htmlspecialchars($r_item['phone_number']); ?></span>
                    <span><?php echo $r_time; ?></span>
                  </div>
                </div>
              <?php } ?>
            <?php } else { ?>
              <div style="padding: 20px; text-align: center; color: var(--crm-text-muted); font-size: 11px;">
                No recent calls recorded today.
              </div>
            <?php } ?>
          </div>
        </div>

        <div class="cw-audio-status-pill">
          <span><i class="fa fa-headphones" style="margin-right: 4px;"></i> Audio Line</span>
          <strong>3CX Connected</strong>
        </div>
      </div>
    </div>

    <div class="cw-right-column">
      
      <div class="cw-active-banner">
        <div class="cw-active-left">
          <div class="cw-avatar-initials" id="cwAvatarBadge">
            <?php echo $cur_initial; ?>
          </div>
          <div class="cw-active-info-col">
            <span class="cw-active-customer-name" id="cwDisplayCustomerName"><?php echo htmlspecialchars($cur_name); ?></span>
            <div class="cw-active-status-line">
              <span class="cw-status-indicator-dot" id="cwIndicatorDot"></span>
              <span id="cwDisplayCallStatus">Ready to Call</span>
              <strong id="cwLiveTimerDisplay" style="margin-left: 4px; display: none; color: #16a34a;">00:00</strong>
            </div>
          </div>
        </div>

        <div class="cw-call-action-btns">
          <button type="button" class="cw-ctrl-btn" id="btnActionMute" title="Mute Microphone">
            <i class="fa fa-microphone"></i> <span>Mute</span>
          </button>
          <button type="button" class="cw-ctrl-btn" id="btnActionHold" title="Hold Call">
            <i class="fa fa-pause"></i> <span>Hold</span>
          </button>
          <button type="button" class="cw-ctrl-btn" id="btnActionSpeaker" title="Speaker">
            <i class="fa fa-volume-up"></i> <span>Speaker</span>
          </button>
          <button type="button" class="cw-btn-hangup" id="btnActionEndCall" style="display: none;">
            <i class="fa fa-phone-slash"></i> <span>End Call</span>
          </button>
          <button type="button" class="cw-ctrl-btn" id="btnQueuePrev" title="Previous Database Lead">
            <i class="fa fa-chevron-left"></i>
          </button>
          <button type="button" class="cw-ctrl-btn" id="btnQueueNext" title="Next Database Lead">
            <i class="fa fa-chevron-right"></i>
          </button>
        </div>
      </div>

      <div class="cw-lead-meta-strip">
        <div class="cw-meta-box">
          <span class="cw-meta-label">Primary Phone</span>
          <span class="cw-meta-content" id="cwMetaPhoneBox">
            <a href="#" id="cwLinkDialNow"><?php echo htmlspecialchars(!empty($cur_phone) ? $cur_phone : '—'); ?></a>
          </span>
        </div>
        <div class="cw-meta-box">
          <span class="cw-meta-label">POC &amp; Email</span>
          <span class="cw-meta-content" id="cwMetaPocBox"><?php echo htmlspecialchars($cur_poc); ?> &bull; <?php echo htmlspecialchars($cur_email); ?></span>
        </div>
        <div class="cw-meta-box">
          <span class="cw-meta-label">Category / Tag</span>
          <span class="cw-meta-content" id="cwMetaCatBox"><?php echo htmlspecialchars($cur_category); ?></span>
        </div>
        <div class="cw-meta-box">
          <span class="cw-meta-label">Lead Code</span>
          <span class="cw-meta-content" id="cwMetaCodeBox"><?php echo htmlspecialchars($cur_lead_code); ?></span>
        </div>
      </div>

      <div class="cw-form-and-history-grid">
        <div class="cw-card cw-notes-container" id="cwNotesOutcomeCard">
          <form id="cwSaveCallForm">
            <input type="hidden" name="lead_id" id="hiddenLeadId" value="<?php echo $cur_lead_id; ?>" />
            <input type="hidden" name="call_log_id" id="hiddenCallLogId" value="0" />
            <input type="hidden" name="customer_name" id="hiddenCustomerName" value="<?php echo htmlspecialchars($cur_name); ?>" />
            <input type="hidden" name="call_duration" id="hiddenCallDuration" value="0" />

            <div class="cw-form-top-row">
              <span class="cw-card-title">
                <i class="fa fa-pencil-square-o" style="color: #2563eb;"></i> Call Outcome &amp; Notes
              </span>

              <div style="display: flex; align-items: center; gap: 6px;">
                <label class="cw-inline-label" style="margin: 0;">Status:</label>
                <select class="cw-disposition-dropdown" name="status" id="selectUnifiedStatus">
                  <?php if (!empty($statuses)) { ?>
                    <?php foreach ($statuses as $st_item) { ?>
                      <option value="<?php echo (int)$st_item['id']; ?>" data-name="<?php echo htmlspecialchars($st_item['status']); ?>" <?php echo (!empty($active_lead['status']) && $active_lead['status'] == $st_item['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($st_item['status']); ?>
                      </option>
                    <?php } ?>
                  <?php } ?>
                </select>
              </div>
            </div>

            <textarea class="cw-notes-textarea" name="remarks" id="textareaNotes" placeholder="Type notes or call summary here..."></textarea>

            <div class="cw-form-bottom-row">
              <div class="cw-inline-field">
                <label class="cw-inline-label">Follow-up Date</label>
                <input type="date" name="next_followup" class="cw-inline-input" id="inputFollowupDate" value="<?php echo date('Y-m-d', strtotime('+2 days')); ?>" />
              </div>

              <div class="cw-inline-field">
                <label class="cw-inline-label">Time</label>
                <input type="time" name="next_followup_time" class="cw-inline-input" id="inputFollowupTime" value="11:00" />
              </div>

              <div class="cw-inline-buttons">
                <button type="submit" class="cw-btn-save" id="btnSubmitCallLog">
                  <i class="fa fa-floppy-o"></i> <span>Save</span>
                </button>
                <button type="button" class="cw-btn-next" id="btnSaveAndAdvanceQueue" title="Save outcome and move to next lead">
                  <i class="fa fa-step-forward"></i> <span>Next</span>
                </button>
              </div>
            </div>
          </form>
        </div>

        <div class="cw-card" style="padding: 10px 14px; flex-shrink: 0;">
          <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
            <span class="cw-card-title"><i class="fa fa-history" style="color: #64748b;"></i> Lead History &amp; Remarks</span>
            <span style="font-size: 11px; color: var(--crm-text-muted);" id="cwHistoryLeadLabel">Lead #<?php echo htmlspecialchars($cur_lead_code); ?></span>
          </div>

          <div class="cw-activity-panel" id="cwHistoryPanel">
            <?php if (!empty($active_lead['remark'])) { ?>
              <div class="cw-activity-row">
                <div>
                  <i class="fa fa-comment-o cw-activity-icon"></i>
                  <strong>Latest Remark:</strong> <?php echo htmlspecialchars($active_lead['remark']); ?>
                </div>
                <span style="color: var(--crm-text-muted); font-size: 10px;"><?php echo htmlspecialchars(!empty($active_lead['status_name']) ? $active_lead['status_name'] : 'Active'); ?></span>
              </div>
            <?php } else { ?>
              <div style="color: var(--crm-text-muted); font-size: 11px; padding: 4px;">
                No previous follow-up remarks recorded for this lead.
              </div>
            <?php } ?>
          </div>
        </div>
      </div>

    </div>

  </div>
</div>

<div class="cw-drawer-backdrop" id="queueDrawerBackdrop"></div>

<div class="cw-side-drawer" id="nextCallsQueueDrawer">
  <div class="cw-drawer-header">
    <span class="cw-drawer-title">
      <i class="fa fa-list-ol" style="color: #2563eb;"></i>
      <span>Next Calls Queue (<span id="drawerRemainingCount"><?php echo count($queue); ?></span> Remaining)</span>
    </span>
    <button type="button" class="cw-drawer-close" id="btnCloseQueueDrawer">&times;</button>
  </div>

  <div style="padding: 10px 16px; border-bottom: 1px solid var(--crm-border);">
    <input type="text" class="cw-search-input-field" id="inputQueueFilterSearch" placeholder="Filter queue leads..." />
  </div>

  <div class="cw-drawer-body" id="queueCardsContainer">
    <?php if (!empty($queue)) { ?>
      <?php foreach ($queue as $idx => $q_lead) { 
        $is_lead_active = ($idx === 0);
        $lead_phone = !empty($q_lead['phone_primary']) ? $q_lead['phone_primary'] : (!empty($q_lead['phone_alt']) ? $q_lead['phone_alt'] : 'No Phone');
        $lead_status_lbl = !empty($q_lead['status_name']) ? $q_lead['status_name'] : 'Database';
      ?>
        <div class="cw-queue-card-item <?php echo $is_lead_active ? 'active-lead' : ''; ?>" data-queue-idx="<?php echo $idx; ?>" data-lead-id="<?php echo (int)$q_lead['id']; ?>" data-phone="<?php echo htmlspecialchars($lead_phone); ?>" data-customer="<?php echo htmlspecialchars($q_lead['customer']); ?>">
          <div class="cw-qcard-top">
            <strong>#<?php echo ($idx + 1); ?> <?php echo htmlspecialchars($q_lead['customer']); ?></strong>
            <span class="cw-qcard-badge"><?php echo htmlspecialchars($lead_status_lbl); ?></span>
          </div>
          <div class="cw-qcard-mid">
            <span class="cw-qcard-phone"><i class="fa fa-phone" style="font-size: 10px; margin-right: 3px;"></i><?php echo htmlspecialchars($lead_phone); ?></span>
            <span><?php echo htmlspecialchars(!empty($q_lead['category']) ? $q_lead['category'] : 'Corporate'); ?></span>
          </div>
        </div>
      <?php } ?>
    <?php } else { ?>
      <div style="padding: 30px; text-align: center; color: var(--crm-text-muted); font-size: 12px;">
        No database records in current queue.
      </div>
    <?php } ?>
  </div>
</div>

<script>
$(document).ready(function() {
  var leadQueue = <?php echo json_encode(!empty($queue) ? $queue : array()); ?>;
  var queueIndex = 0;
  var timerInterval = null;
  var totalSeconds = 0;
  var activeLogId = 0;

  function updateQueueCountDisplays() {
    var total = leadQueue.length;
    var remaining = Math.max(0, total - queueIndex);
    $('#queueRemainingBadge').text(remaining);
    $('#drawerRemainingCount').text(remaining);
  }

  $('#tabKeypadView').on('click', function() {
    $(this).addClass('active');
    $('#tabRecentView').removeClass('active');
    $('#panelKeypad').show();
    $('#panelRecent').hide();
  });

  $('#tabRecentView').on('click', function() {
    $(this).addClass('active');
    $('#tabKeypadView').removeClass('active');
    $('#panelKeypad').hide();
    $('#panelRecent').show();
  });

  $('.cw-num-btn').on('click', function() {
    var digit = $(this).attr('data-val');
    if (digit) {
      var curVal = $('#cwDialerPhoneInput').val();
      $('#cwDialerPhoneInput').val(curVal + digit);
    }
  });

  $('#cwBtnBackspace').on('click', function() {
    var val = $('#cwDialerPhoneInput').val();
    $('#cwDialerPhoneInput').val(val.slice(0, -1));
  });

  $('#btnActionMute').on('click', function() {
    $(this).toggleClass('active');
  });

  $('#btnActionHold').on('click', function() {
    $(this).toggleClass('active');
  });

  $('#btnActionSpeaker').on('click', function() {
    $(this).toggleClass('active');
  });

  function startLiveTimer() {
    clearInterval(timerInterval);
    totalSeconds = 0;
    $('#cwLiveTimerDisplay').text('00:00').show();
    timerInterval = setInterval(function() {
      totalSeconds++;
      var m = Math.floor(totalSeconds / 60);
      var s = totalSeconds % 60;
      var str = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
      $('#cwLiveTimerDisplay').text(str);
      $('#hiddenCallDuration').val(totalSeconds);
    }, 1000);
  }

  function stopLiveTimer() {
    clearInterval(timerInterval);
  }

  $('#cwBtnStartCall').on('click', function() {
    var phone = $('#cwDialerPhoneInput').val().trim();
    var leadId = $('#hiddenLeadId').val();
    var custName = $('#hiddenCustomerName').val();

    if (!phone) {
      alert('Please enter or select a phone number to place a call.');
      $('#cwDialerPhoneInput').focus();
      return;
    }

    $('#cwIndicatorDot').addClass('pulsing');
    $('#cwDisplayCallStatus').text('In call');
    $('#btnActionEndCall').show();
    $('#cwBtnStartCall').hide();

    startLiveTimer();

    $.ajax({
      url: '<?php echo base_url(); ?>manager/calls/ajax_initiate_call',
      type: 'POST',
      data: { phone: phone, lead_id: leadId, customer_name: custName },
      dataType: 'json',
      success: function(res) {
        if (res.status) {
          activeLogId = res.call_log_id;
          $('#hiddenCallLogId').val(activeLogId);
          if (res.dispatch && res.dispatch.webclient_url) {
            window.open(res.dispatch.webclient_url, '_blank');
          } else if (res.dispatch && res.dispatch.tel_url) {
            window.location.href = res.dispatch.tel_url;
          }
        }
      }
    });
  });

  $('#btnActionEndCall').on('click', function() {
    stopLiveTimer();
    $('#cwIndicatorDot').removeClass('pulsing');
    $('#cwDisplayCallStatus').text('Call Ended · Save Outcome');
    $('#btnActionEndCall').hide();
    $('#cwBtnStartCall').show();
    $('#textareaNotes').focus();
  });

  function loadLeadData(lead, idx) {
    if (!lead) return;
    if (typeof idx !== 'undefined') {
      queueIndex = idx;
    }
    var name = lead.customer || 'Unknown Contact';
    var phone = lead.phone_primary || lead.contact1 || lead.alt_contact || lead.mobile || '';
    var email = lead.email || '—';
    var poc = lead.poc || 'POC / Manager';
    var code = lead.lead_id || '—';
    var cat = lead.category || 'Corporate';

    $('#cwAvatarBadge').text(name.charAt(0).toUpperCase());
    $('#cwDisplayCustomerName').text(name);
    $('#cwDialerPhoneInput').val(phone);
    $('#cwLinkDialNow').text(phone ? phone : '—');
    $('#cwMetaPocBox').text(poc + ' • ' + email);
    $('#cwMetaCatBox').text(cat);
    $('#cwMetaCodeBox').text(code);
    $('#cwHistoryLeadLabel').text('Lead #' + code);

    $('#hiddenLeadId').val(lead.id || 0);
    $('#hiddenCustomerName').val(name);

    if (lead.status) {
      $('#selectUnifiedStatus').val(lead.status);
    }

    if (lead.remark) {
      $('#cwHistoryPanel').html(
        '<div class="cw-activity-row">' +
          '<div><i class="fa fa-comment-o cw-activity-icon"></i><strong>Remark:</strong> ' + lead.remark + '</div>' +
          '<span style="color: var(--crm-text-muted); font-size: 10px;">' + (lead.status_name ? lead.status_name : 'Active') + '</span>' +
        '</div>'
      );
    } else {
      $('#cwHistoryPanel').html(
        '<div style="color: var(--crm-text-muted); font-size: 11px; padding: 4px;">No previous remarks for this lead.</div>'
      );
    }

    $('.cw-queue-card-item').removeClass('active-lead');
    $('.cw-queue-card-item[data-queue-idx="' + queueIndex + '"]').addClass('active-lead');
    updateQueueCountDisplays();
  }

  $('#btnQueueNext').on('click', function() {
    if (leadQueue.length > 0 && queueIndex < leadQueue.length - 1) {
      queueIndex++;
      loadLeadData(leadQueue[queueIndex], queueIndex);
    } else {
      alert('Reached end of database queue.');
    }
  });

  $('#btnQueuePrev').on('click', function() {
    if (queueIndex > 0) {
      queueIndex--;
      loadLeadData(leadQueue[queueIndex], queueIndex);
    }
  });

  $('#cwLinkDialNow').on('click', function(e) {
    e.preventDefault();
    var p = $(this).text().trim();
    if (p && p !== '—') {
      $('#cwDialerPhoneInput').val(p);
      $('#cwBtnStartCall').trigger('click');
    }
  });

  $('#btnQuickNewCall').on('click', function() {
    $('#cwDialerPhoneInput').val('').focus();
    $('#cwAvatarBadge').text('+');
    $('#cwDisplayCustomerName').text('Direct Outbound Call');
    $('#cwLinkDialNow').text('—');
    $('#cwMetaPocBox').text('Direct Dialing');
    $('#hiddenLeadId').val('0');
    $('#hiddenCustomerName').val('Direct Dialing');
  });

  function submitOutcome(advanceNext) {
    var phone = $('#cwDialerPhoneInput').val().trim();
    var outcomeName = $('#selectUnifiedStatus option:selected').text().trim();
    var dataStr = $('#cwSaveCallForm').serialize() + '&phone=' + encodeURIComponent(phone) + '&call_disposition=' + encodeURIComponent(outcomeName);
    var btn = $('#btnSubmitCallLog');
    btn.prop('disabled', true);

    $.ajax({
      url: '<?php echo base_url(); ?>manager/calls/ajax_save_call',
      type: 'POST',
      data: dataStr,
      dataType: 'json',
      success: function(res) {
        btn.prop('disabled', false);
        if (res.status) {
          stopLiveTimer();
          $('#cwLiveTimerDisplay').hide();
          $('#cwIndicatorDot').removeClass('pulsing');
          $('#cwDisplayCallStatus').text('Ready to Call');
          $('#textareaNotes').val('');
          $('#hiddenCallLogId').val('0');
          $('#hiddenCallDuration').val('0');
          activeLogId = 0;

          var outcomeStr = res.outcome || outcomeName;
          var durStr = (res.duration > 0) ? res.duration + 's' : '0s';
          var newRow = 
            '<div class="cw-activity-row">' +
              '<div><i class="fa fa-check-circle text-success cw-activity-icon"></i><strong>' + outcomeStr + ':</strong> ' + (res.duration > 0 ? durStr : 'Completed') + '</div>' +
              '<span style="color: var(--crm-text-muted); font-size: 10px;">Just now</span>' +
            '</div>';
          $('#cwHistoryPanel').prepend(newRow);

          if (advanceNext) {
            $('#btnQueueNext').trigger('click');
          } else {
            alert('Call outcome and status saved successfully.');
          }
        } else {
          alert('Failed to save outcome: ' + (res.message || 'Error'));
        }
      },
      error: function() {
        btn.prop('disabled', false);
        alert('Server communication error.');
      }
    });
  }

  $('#cwSaveCallForm').on('submit', function(e) {
    e.preventDefault();
    submitOutcome(false);
  });

  $('#btnSaveAndAdvanceQueue').on('click', function() {
    submitOutcome(true);
  });

  $('#cwQueueFilterSelect').on('change', function() {
    var filter = $(this).val();
    window.location.href = '<?php echo base_url(); ?>manager/calls?status=' + filter;
  });

  $('#cwSearchLeadsInput').on('keyup', function() {
    var term = $(this).val().toLowerCase();
    if (term.length < 2) return;

    $.ajax({
      url: '<?php echo base_url(); ?>manager/calls/ajax_get_queue',
      type: 'GET',
      data: { search: term, status: $('#cwQueueFilterSelect').val() },
      dataType: 'json',
      success: function(res) {
        if (res.status && res.leads && res.leads.length > 0) {
          leadQueue = res.leads;
          queueIndex = 0;
          renderQueueDrawerList(leadQueue);
          loadLeadData(leadQueue[0], 0);
        }
      }
    });
  });

  function renderQueueDrawerList(list) {
    var html = '';
    for (var i = 0; i < list.length; i++) {
      var item = list[i];
      var p = item.phone_primary || item.contact1 || item.alt_contact || 'No Phone';
      var st = item.status_name || 'Database';
      var isAct = (i === queueIndex) ? 'active-lead' : '';
      html += 
        '<div class="cw-queue-card-item ' + isAct + '" data-queue-idx="' + i + '" data-lead-id="' + item.id + '" data-phone="' + p + '" data-customer="' + item.customer + '">' +
          '<div class="cw-qcard-top">' +
            '<strong>#' + (i + 1) + ' ' + item.customer + '</strong>' +
            '<span class="cw-qcard-badge">' + st + '</span>' +
          '</div>' +
          '<div class="cw-qcard-mid">' +
            '<span class="cw-qcard-phone"><i class="fa fa-phone" style="font-size: 10px; margin-right: 3px;"></i>' + p + '</span>' +
            '<span>' + (item.category || 'Corporate') + '</span>' +
          '</div>' +
        '</div>';
    }
    $('#queueCardsContainer').html(html);
    updateQueueCountDisplays();
  }

  $('#btnToggleQueueDrawer').on('click', function() {
    $('#nextCallsQueueDrawer').addClass('open');
    $('#queueDrawerBackdrop').fadeIn(200);
  });

  $('#btnCloseQueueDrawer, #queueDrawerBackdrop').on('click', function() {
    $('#nextCallsQueueDrawer').removeClass('open');
    $('#queueDrawerBackdrop').fadeOut(200);
  });

  $(document).on('click', '.cw-queue-card-item', function() {
    var idx = parseInt($(this).attr('data-queue-idx'));
    if (!isNaN(idx) && leadQueue[idx]) {
      loadLeadData(leadQueue[idx], idx);
      $('#nextCallsQueueDrawer').removeClass('open');
      $('#queueDrawerBackdrop').fadeOut(200);
    }
  });

  $('#inputQueueFilterSearch').on('keyup', function() {
    var q = $(this).val().toLowerCase();
    $('#queueCardsContainer .cw-queue-card-item').each(function() {
      var text = $(this).text().toLowerCase();
      if (text.indexOf(q) > -1) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  });

  updateQueueCountDisplays();
});
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
