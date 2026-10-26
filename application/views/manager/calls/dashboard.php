<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>

<style>
html, body {
  height: 100vh !important;
  max-height: 100vh !important;
  overflow: hidden !important;
  margin: 0 !important;
  padding: 0 !important;
}
.wrapper {
  height: 100vh !important;
  max-height: 100vh !important;
  overflow: hidden !important;
  display: flex !important;
  flex-direction: column !important;
}
.main-sidebar, .left-side, .main-footer, .crm-footer {
  display: none !important;
}
.content-wrapper.glass-calling-wrapper {
  background: url('<?php echo base_url(); ?>assets/bg-dialer.png') no-repeat center center fixed;
  background-size: cover;
  position: fixed !important;
  top: 50px !important;
  left: 0 !important;
  right: 0 !important;
  bottom: 0 !important;
  width: 100vw !important;
  max-width: 100vw !important;
  height: calc(100vh - 50px) !important;
  max-height: calc(100vh - 50px) !important;
  min-height: 0 !important;
  padding: 12px 18px !important;
  color: #f8fafc;
  font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "SF Pro Text", "Segoe UI", Roboto, sans-serif;
  box-sizing: border-box !important;
  overflow: hidden !important;
  display: flex !important;
  gap: 16px !important;
  align-items: stretch !important;
  justify-content: center !important;
  margin: 0 !important;
  z-index: 800 !important;
}

.glass-side-dock {
  width: 76px;
  flex-shrink: 0;
  height: 100% !important;
  max-height: 100% !important;
  background: rgba(18, 18, 24, 0.72);
  backdrop-filter: blur(28px);
  -webkit-backdrop-filter: blur(28px);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 26px;
  padding: 14px 8px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  box-shadow: 0 20px 45px rgba(0, 0, 0, 0.55);
  box-sizing: border-box !important;
  overflow-y: auto;
  z-index: 100;
}

.glass-dock-brand {
  width: 48px;
  height: 48px;
  border-radius: 18px;
  background: #ffffff;
  color: #000000;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  margin-bottom: 8px;
  box-shadow: 0 4px 16px rgba(255, 255, 255, 0.25);
}

.glass-dock-btn {
  width: 52px;
  height: 52px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: #ffffff;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 3px;
  font-size: 16px;
  cursor: pointer;
  transition: all 0.18s ease;
  text-decoration: none;
  position: relative;
  user-select: none;
}

.glass-dock-btn span.dock-lbl {
  font-size: 9px;
  font-weight: 600;
  letter-spacing: 0.3px;
  color: #cbd5e1;
}

.glass-dock-btn:hover {
  background: rgba(255, 255, 255, 0.16);
  color: #ffffff;
  transform: translateY(-1px);
}

.glass-dock-btn.active {
  background: #ffffff;
  border-color: #ffffff;
  color: #000000;
}

.glass-dock-btn.active span.dock-lbl {
  color: #000000;
}

.glass-dock-badge {
  position: absolute;
  top: -4px;
  right: -4px;
  background: #000000;
  color: #ffffff;
  border: 1px solid rgba(255, 255, 255, 0.4);
  border-radius: 10px;
  font-size: 10px;
  font-weight: 700;
  padding: 1px 6px;
  line-height: 1.2;
}

.glass-main-stage {
  flex: 1 1 960px;
  max-width: 1080px;
  height: 100% !important;
  max-height: 100% !important;
  overflow: hidden !important;
  display: flex;
  flex-direction: column;
  gap: 10px;
  box-sizing: border-box !important;
}

#viewCardDialer,
#viewCardDetailFill {
  flex: 1 1 auto;
  height: calc(100vh - 120px) !important;
  max-height: calc(100vh - 120px) !important;
  overflow-y: auto !important;
  overflow-x: hidden !important;
  padding-right: 4px;
}

#viewCardDialer::-webkit-scrollbar,
#viewCardDetailFill::-webkit-scrollbar,
.glass-side-dock::-webkit-scrollbar,
.glass-slide-body::-webkit-scrollbar {
  width: 5px;
}

#viewCardDialer::-webkit-scrollbar-thumb,
#viewCardDetailFill::-webkit-scrollbar-thumb,
.glass-side-dock::-webkit-scrollbar-thumb,
.glass-slide-body::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.22);
  border-radius: 10px;
}

.glass-top-tabs {
  display: inline-flex;
  align-items: center;
  background: rgba(18, 18, 24, 0.72);
  backdrop-filter: blur(28px);
  -webkit-backdrop-filter: blur(28px);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 20px;
  padding: 5px;
  gap: 6px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
  align-self: flex-start;
}

.glass-tab-pill {
  padding: 8px 20px;
  border-radius: 16px;
  background: transparent;
  border: none;
  color: #cbd5e1;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all 0.18s ease;
  user-select: none;
}

.glass-tab-pill:hover {
  color: #ffffff;
  background: rgba(255, 255, 255, 0.08);
}

.glass-tab-pill.active {
  background: #ffffff;
  color: #000000;
  box-shadow: 0 4px 14px rgba(255, 255, 255, 0.2);
}

.glass-card {
  background: rgba(22, 20, 26, 0.72);
  backdrop-filter: blur(32px);
  -webkit-backdrop-filter: blur(32px);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 28px;
  padding: 24px;
  box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.15);
  position: relative;
}

.glass-banner {
  background: rgba(18, 18, 24, 0.85);
  backdrop-filter: blur(24px);
  -webkit-backdrop-filter: blur(24px);
  border: 1px solid rgba(255, 255, 255, 0.16);
  border-radius: 20px;
  padding: 14px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
  flex-wrap: wrap;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.45);
}

.glass-timer-circle {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: #000000;
  border: 2px solid #ffffff;
  color: #ffffff;
  font-size: 18px;
  font-weight: 800;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
}

.glass-btn-white {
  background: #ffffff;
  border: 1px solid #ffffff;
  color: #000000;
  padding: 8px 18px;
  border-radius: 14px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: all 0.18s ease;
  text-decoration: none;
}

.glass-btn-white:hover {
  background: #e2e8f0;
  color: #000000;
  transform: translateY(-1px);
}

.glass-btn-dark {
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.16);
  color: #ffffff;
  padding: 8px 16px;
  border-radius: 14px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: all 0.18s ease;
  text-decoration: none;
}

.glass-btn-dark:hover {
  background: rgba(255, 255, 255, 0.18);
  color: #ffffff;
}

.glass-dialer-grid {
  display: grid;
  grid-template-columns: 1fr 390px;
  gap: 20px;
}

@media (max-width: 991px) {
  .glass-calling-wrapper {
    flex-direction: column;
  }
  .glass-side-dock {
    width: 100%;
    flex-direction: row;
    justify-content: center;
    position: relative;
    top: 0;
  }
  .glass-dialer-grid {
    grid-template-columns: 1fr;
  }
}

.glass-client-header {
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  padding-bottom: 16px;
  margin-bottom: 18px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.glass-client-name {
  font-size: 24px;
  font-weight: 700;
  color: #ffffff;
  margin: 0 0 6px 0;
  letter-spacing: -0.3px;
}

.glass-client-sub {
  font-size: 13px;
  color: #94a3b8;
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.glass-badge {
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.14);
  padding: 3px 10px;
  border-radius: 8px;
  font-size: 11px;
  color: #ffffff;
  font-weight: 500;
}

.glass-badge.active-badge {
  background: #ffffff;
  color: #000000;
  border-color: #ffffff;
  font-weight: 700;
}

.glass-dual-phones-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-bottom: 18px;
}

.glass-phone-card {
  background: rgba(0, 0, 0, 0.35);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 18px;
  padding: 12px 16px;
  cursor: pointer;
  transition: all 0.15s ease;
}

.glass-phone-card:hover {
  background: rgba(255, 255, 255, 0.08);
  border-color: rgba(255, 255, 255, 0.3);
}

.glass-phone-card.active-target {
  background: rgba(255, 255, 255, 0.14);
  border-color: #ffffff;
  box-shadow: 0 0 20px rgba(255, 255, 255, 0.18);
}

.glass-phone-card-label {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 10px;
  font-weight: 700;
  color: #94a3b8;
  text-transform: uppercase;
  margin-bottom: 4px;
  letter-spacing: 0.5px;
}

.glass-phone-card-num {
  font-size: 17px;
  font-weight: 600;
  color: #ffffff;
  letter-spacing: 0.5px;
}

.glass-remarks-preview {
  background: rgba(0, 0, 0, 0.3);
  border-left: 3px solid #ffffff;
  padding: 14px 18px;
  border-radius: 0 16px 16px 0;
  margin-bottom: 18px;
}

.glass-remarks-preview-label {
  font-size: 11px;
  font-weight: 700;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 4px;
}

.glass-remarks-preview-text {
  font-size: 13px;
  color: #e2e8f0;
  line-height: 1.5;
  margin: 0;
  max-height: 70px;
  overflow-y: auto;
}

.glass-meta-strip {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
  margin-bottom: 18px;
}

.glass-meta-item {
  background: rgba(0, 0, 0, 0.3);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 14px;
  padding: 10px 14px;
}

.glass-meta-item-label {
  font-size: 10px;
  color: #94a3b8;
  text-transform: uppercase;
  margin-bottom: 2px;
  font-weight: 600;
}

.glass-meta-item-val {
  font-size: 13px;
  font-weight: 600;
  color: #ffffff;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.glass-dialer-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.glass-dialer-screen {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: rgba(0, 0, 0, 0.45);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 18px;
  padding: 12px 18px;
  margin-bottom: 16px;
}

.glass-dialer-input {
  background: transparent;
  border: none;
  font-size: 24px;
  font-weight: 600;
  color: #ffffff;
  outline: none;
  width: 100%;
  text-align: center;
  letter-spacing: 1px;
}

.glass-dialer-backspace {
  background: transparent;
  border: none;
  color: #94a3b8;
  font-size: 18px;
  cursor: pointer;
  padding: 4px;
  transition: color 0.15s ease;
}

.glass-dialer-backspace:hover {
  color: #ffffff;
}

.glass-keypad-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
  margin-bottom: 20px;
}

.glass-key-btn {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.15);
  color: #ffffff;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.12s ease;
  user-select: none;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
}

.glass-key-btn:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: translateY(-1px);
}

.glass-key-btn:active {
  transform: scale(0.94);
  background: #ffffff;
  color: #000000;
}

.glass-key-btn:active .glass-key-digit,
.glass-key-btn:active .glass-key-letters {
  color: #000000;
}

.glass-key-digit {
  font-size: 24px;
  font-weight: 600;
  line-height: 1;
  color: #ffffff;
}

.glass-key-letters {
  font-size: 8px;
  color: #94a3b8;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  margin-top: 3px;
  font-weight: 700;
}

.glass-call-btn {
  width: 76px;
  height: 76px;
  border-radius: 50%;
  border: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  user-select: none;
}

.glass-call-btn.btn-start {
  background: #ffffff;
  color: #000000;
  box-shadow: 0 10px 30px rgba(255, 255, 255, 0.35);
}

.glass-call-btn.btn-start:hover {
  background: #f1f5f9;
  transform: scale(1.06);
  box-shadow: 0 14px 38px rgba(255, 255, 255, 0.5);
}

.glass-call-btn.btn-end {
  background: #000000;
  color: #ffffff;
  border: 2px solid #ffffff;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
  animation: glassPulseWhite 1.8s infinite;
}

.glass-call-btn.btn-end:hover {
  background: #111111;
  transform: scale(1.06);
}

@keyframes glassPulseWhite {
  0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.6); }
  70% { box-shadow: 0 0 0 14px rgba(255, 255, 255, 0); }
  100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
}

.glass-form-group {
  margin-bottom: 16px;
}

.glass-form-label {
  display: block;
  font-size: 11px;
  font-weight: 700;
  color: #cbd5e1;
  margin-bottom: 6px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.glass-form-input,
.glass-form-select,
.glass-form-textarea {
  width: 100%;
  background: rgba(0, 0, 0, 0.45);
  border: 1px solid rgba(255, 255, 255, 0.14);
  border-radius: 14px;
  padding: 10px 14px;
  font-size: 13px;
  color: #ffffff;
  outline: none;
  transition: all 0.15s ease;
  box-sizing: border-box;
}

.glass-form-input:focus,
.glass-form-select:focus,
.glass-form-textarea:focus {
  border-color: #ffffff;
  box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.25);
}

.glass-form-select option {
  background: #12141a;
  color: #ffffff;
}

.glass-radio-pill {
  padding: 6px 14px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.14);
  border-radius: 12px;
  font-size: 12px;
  color: #ffffff;
  cursor: pointer;
  user-select: none;
  transition: all 0.15s ease;
}

.glass-radio-pill:hover {
  background: rgba(255, 255, 255, 0.18);
}

.glass-radio-pill.selected,
.glass-radio-pill.active {
  background: #ffffff;
  color: #000000;
  border-color: #ffffff;
  font-weight: 700;
}

.glass-slide-panel {
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  width: 520px;
  max-width: 100vw;
  height: 100vh;
  background: rgba(18, 18, 24, 0.95);
  backdrop-filter: blur(28px);
  -webkit-backdrop-filter: blur(28px);
  border-left: 1px solid rgba(255, 255, 255, 0.12);
  box-shadow: -15px 0 50px rgba(0, 0, 0, 0.7);
  z-index: 1060;
  transform: translateX(100%);
  transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.glass-slide-panel.show {
  transform: translateX(0);
}

.glass-drawer-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.65);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  z-index: 1050;
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s ease;
}

.glass-drawer-backdrop.show {
  opacity: 1;
  visibility: visible;
}

.glass-slide-header {
  padding: 18px 22px;
  background: rgba(0, 0, 0, 0.3);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.glass-slide-body {
  flex: 1 1 auto;
  padding: 18px 22px;
  overflow-y: auto;
}

.glass-phone-log-item,
.glass-queue-item {
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 16px;
  padding: 14px 16px;
  margin-bottom: 10px;
  transition: all 0.15s ease;
}

.glass-phone-log-item:hover,
.glass-queue-item:hover {
  background: rgba(255, 255, 255, 0.09);
  border-color: rgba(255, 255, 255, 0.2);
}

.glass-queue-item.active {
  background: rgba(255, 255, 255, 0.15);
  border-color: #ffffff;
}

.glass-toast {
  position: fixed;
  bottom: 24px;
  right: 24px;
  background: #000000;
  color: #ffffff;
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 14px;
  padding: 12px 20px;
  font-size: 13px;
  font-weight: 600;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
  z-index: 2000;
  display: none;
  align-items: center;
  gap: 10px;
}
</style>

<div class="content-wrapper glass-calling-wrapper">

  <div class="glass-side-dock">
    <div class="glass-dock-brand" title="Calling Center">
      <i class="fa fa-phone"></i>
    </div>

    <button type="button" class="glass-dock-btn active" id="dockBtnDialer" title="Dialer Screen">
      <i class="fa fa-keyboard-o"></i>
      <span class="dock-lbl">Dialer</span>
    </button>

    <button type="button" class="glass-dock-btn" id="dockBtnDetails" title="Fill Client Details">
      <i class="fa fa-pencil-square-o"></i>
      <span class="dock-lbl">Details</span>
    </button>

    <button type="button" class="glass-dock-btn" id="dockBtnQueue" title="Calling Queue">
      <i class="fa fa-list-ul"></i>
      <span class="dock-lbl">Queue</span>
      <span class="glass-dock-badge" id="queueCountBadge"><?php echo isset($total_queue) ? (int)$total_queue : 0; ?></span>
    </button>

    <button type="button" class="glass-dock-btn" id="dockBtnLogs" title="Call Logs">
      <i class="fa fa-history"></i>
      <span class="dock-lbl">Logs</span>
    </button>

    <button type="button" class="glass-dock-btn" id="dockBtnAutoDialToggle" title="Auto-Dialer Mode">
      <i class="fa fa-bolt"></i>
      <span class="dock-lbl" id="autoDialDockLbl">60s</span>
    </button>

    <a href="<?php echo base_url(); ?>manager/dashboard" class="glass-dock-btn" style="margin-top:auto;" title="Exit Calling Center">
      <i class="fa fa-arrow-left"></i>
      <span class="dock-lbl">Exit</span>
    </a>
  </div>

  <div class="glass-main-stage">

    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
      <div class="glass-top-tabs">
        <button type="button" class="glass-tab-pill active" id="pillTabDialer">
          <i class="fa fa-phone"></i> Dialer Screen
        </button>
        <button type="button" class="glass-tab-pill" id="pillTabDetails">
          <i class="fa fa-pencil-square-o"></i> Detail Fill & Outcome
        </button>
      </div>

      <div style="display:flex; align-items:center; gap:8px;">
        <span class="glass-badge" id="queueNavIndicator">
          Contact 1 of <?php echo max(1, (int)$total_queue); ?>
        </span>
        <button type="button" class="glass-btn-dark" id="btnPrevLead" style="padding:6px 12px;">
          <i class="fa fa-chevron-left"></i>
        </button>
        <button type="button" class="glass-btn-dark" id="btnNextLead" style="padding:6px 12px;">
          <i class="fa fa-chevron-right"></i>
        </button>
      </div>
    </div>

    <div id="autodialerWrapupBanner" class="glass-banner" style="display:none; padding:10px 18px;">
      <div style="display:flex; align-items:center; gap:12px;">
        <div class="glass-timer-circle" id="autodialerTimerDisplay">
          <?php echo (int)$autodialer_seconds; ?>
        </div>
      </div>
      <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
        <button type="button" class="glass-btn-dark" id="btnAutodialExtend"><i class="fa fa-plus"></i> +30s Extend</button>
        <button type="button" class="glass-btn-dark" id="btnAutodialPause"><i class="fa fa-pause"></i> Pause</button>
        <button type="button" class="glass-btn-white" id="btnAutodialNextNow"><i class="fa fa-forward"></i> Dial Next Now</button>
        <button type="button" class="glass-btn-dark" id="btnAutodialStop" style="border-color:rgba(255,255,255,0.3);"><i class="fa fa-stop"></i> Stop</button>
      </div>
    </div>

    <div id="viewCardDialer">
      <div class="glass-dialer-grid">

        <div class="glass-card">
          <div class="glass-client-header">
            <div>
              <h1 class="glass-client-name" id="displayCustomerName">
                <?php echo !empty($active_lead['customer']) ? htmlspecialchars($active_lead['customer']) : 'No Contact Selected'; ?>
              </h1>
              <div class="glass-client-sub">
                <span id="displayLeadCode" class="glass-badge">
                  #<?php echo !empty($active_lead['lead_id']) ? htmlspecialchars($active_lead['lead_id']) : '—'; ?>
                </span>
                <span id="displayCategoryPill" class="glass-badge">
                  <?php echo !empty($active_lead['category']) ? htmlspecialchars($active_lead['category']) : 'General Contact'; ?>
                </span>
                <span id="displayLeadStatus" class="glass-badge">
                  <?php echo !empty($active_lead['status_name']) ? htmlspecialchars($active_lead['status_name']) : 'Open'; ?>
                </span>
              </div>
            </div>
          </div>

          <div class="glass-dual-phones-row">
            <div class="glass-phone-card active-target" id="cardPrimaryPhone" onclick="selectPhoneTarget('primary')">
              <div class="glass-phone-card-label">
                <span>Primary Contact</span>
                <span id="badgePrimaryActive" class="glass-badge active-badge">Selected</span>
              </div>
              <div class="glass-phone-card-num" id="displayPrimaryPhone">
                <?php echo !empty($active_lead['phone_primary']) ? htmlspecialchars($active_lead['phone_primary']) : (!empty($active_lead['contact1']) ? htmlspecialchars($active_lead['contact1']) : 'No Number'); ?>
              </div>
            </div>

            <div class="glass-phone-card" id="cardAltPhone" onclick="selectPhoneTarget('alt')">
              <div class="glass-phone-card-label">
                <span>Secondary / Alt</span>
                <span id="badgeAltActive" class="glass-badge active-badge" style="display:none;">Selected</span>
              </div>
              <div class="glass-phone-card-num" id="displayAltPhone">
                <?php echo !empty($active_lead['phone_alt']) ? htmlspecialchars($active_lead['phone_alt']) : '—'; ?>
              </div>
            </div>
          </div>

          <div class="glass-remarks-preview">
            <div class="glass-remarks-preview-label"><i class="fa fa-commenting-o"></i> Previous Call Remarks</div>
            <p class="glass-remarks-preview-text" id="displayPreviousRemarks">
              <?php echo !empty($active_lead['remark']) ? htmlspecialchars($active_lead['remark']) : 'No previous call notes recorded for this contact.'; ?>
            </p>
          </div>

          <div class="glass-meta-strip">
            <div class="glass-meta-item">
              <div class="glass-meta-item-label">POC Name</div>
              <div class="glass-meta-item-val" id="displayPoc">
                <?php echo !empty($active_lead['poc']) ? htmlspecialchars($active_lead['poc']) : '—'; ?>
              </div>
            </div>

            <div class="glass-meta-item">
              <div class="glass-meta-item-label">Authority / Emirate</div>
              <div class="glass-meta-item-val" id="displayAuthority">
                <?php 
                  $loc = array();
                  if(!empty($active_lead['tl_authority'])) $loc[] = $active_lead['tl_authority'];
                  if(!empty($active_lead['tl_emirate'])) $loc[] = $active_lead['tl_emirate'];
                  echo !empty($loc) ? htmlspecialchars(implode(', ', $loc)) : '—';
                ?>
              </div>
            </div>

            <div class="glass-meta-item">
              <div class="glass-meta-item-label">Sales Department</div>
              <div class="glass-meta-item-val" id="displayUserDept">
                <?php echo htmlspecialchars($manager_department); ?>
              </div>
            </div>
          </div>

          <div style="display:flex; justify-content:space-between; align-items:center; margin-top:14px; border-top:1px solid rgba(255,255,255,0.08); padding-top:14px;">
            <span style="font-size:12px; color:#94a3b8;">Want to take call notes or set follow-up?</span>
            <button type="button" class="glass-btn-white" onclick="switchToCard('details')">
              <span>Go to Detail Fill Page</span> <i class="fa fa-arrow-right"></i>
            </button>
          </div>
        </div>

        <div class="glass-card glass-dialer-card">
          <div class="glass-dialer-screen">
            <input type="text" id="dialerInput" class="glass-dialer-input" placeholder="Enter Phone..." autocomplete="off" value="<?php echo !empty($active_lead['phone_primary']) ? htmlspecialchars($active_lead['phone_primary']) : (!empty($active_lead['contact1']) ? htmlspecialchars($active_lead['contact1']) : ''); ?>">
            <button type="button" id="btnDialerBackspace" class="glass-dialer-backspace" title="Delete">
              <i class="fa fa-times-circle"></i>
            </button>
          </div>

          <div id="backendOfflineAlert" style="display:none; width:100%; background:rgba(0,0,0,0.5); border:1px solid rgba(255,255,255,0.3); border-radius:14px; padding:10px 14px; margin-bottom:14px; font-size:12px; text-align:center;">
            <i class="fa fa-exclamation-triangle" style="margin-right:4px;"></i>
            <span id="backendOfflineMsg">Calling backend is not configured (API key / PBX missing).</span>
            <div style="margin-top:6px;">
              <a id="btnFallbackTel" href="tel:" class="glass-btn-white" style="font-size:11px; padding:4px 10px;">
                <i class="fa fa-phone"></i> Launch Device Dialer (tel:)
              </a>
            </div>
          </div>

          <div class="glass-keypad-grid">
            <div class="glass-key-btn" data-key="1"><div class="glass-key-digit">1</div><div class="glass-key-letters"></div></div>
            <div class="glass-key-btn" data-key="2"><div class="glass-key-digit">2</div><div class="glass-key-letters">ABC</div></div>
            <div class="glass-key-btn" data-key="3"><div class="glass-key-digit">3</div><div class="glass-key-letters">DEF</div></div>
            <div class="glass-key-btn" data-key="4"><div class="glass-key-digit">4</div><div class="glass-key-letters">GHI</div></div>
            <div class="glass-key-btn" data-key="5"><div class="glass-key-digit">5</div><div class="glass-key-letters">JKL</div></div>
            <div class="glass-key-btn" data-key="6"><div class="glass-key-digit">6</div><div class="glass-key-letters">MNO</div></div>
            <div class="glass-key-btn" data-key="7"><div class="glass-key-digit">7</div><div class="glass-key-letters">PQRS</div></div>
            <div class="glass-key-btn" data-key="8"><div class="glass-key-digit">8</div><div class="glass-key-letters">TUV</div></div>
            <div class="glass-key-btn" data-key="9"><div class="glass-key-digit">9</div><div class="glass-key-letters">WXYZ</div></div>
            <div class="glass-key-btn" data-key="*"><div class="glass-key-digit">*</div><div class="glass-key-letters"></div></div>
            <div class="glass-key-btn" data-key="0"><div class="glass-key-digit">0</div><div class="glass-key-letters">+</div></div>
            <div class="glass-key-btn" data-key="#"><div class="glass-key-digit">#</div><div class="glass-key-letters"></div></div>
          </div>

          <div style="display:flex; flex-direction:column; align-items:center; gap:8px;">
            <button type="button" id="btnToggleCall" class="glass-call-btn btn-start" title="Call Contact">
              <i class="fa fa-phone"></i>
            </button>
            <div id="liveCallSubtitle" style="font-size:12px; color:#cbd5e1; height:18px;">Tap to place call</div>
          </div>
        </div>

      </div>
    </div>

    <div id="viewCardDetailFill" style="display:none;">
      <div class="glass-card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:14px;">
          <div>
            <h2 style="font-size:20px; font-weight:700; margin:0 0 4px 0; color:#ffffff;">Manual Detail Fill & Call Outcome</h2>
            <div style="font-size:12px; color:#94a3b8;">
              Active: <strong id="detailsCardCustomer" style="color:#ffffff;"><?php echo !empty($active_lead['customer']) ? htmlspecialchars($active_lead['customer']) : 'No Contact'; ?></strong>
              • Duration: <span id="followupCallDurationBadge">00:00</span>
            </div>
          </div>
          <button type="button" class="glass-btn-dark" onclick="switchToCard('dialer')">
            <i class="fa fa-arrow-left"></i> Back to Dialer
          </button>
        </div>

        <form id="formCallFollowup" onsubmit="return false;">
          <input type="hidden" name="lead_id" id="hiddenLeadId" value="<?php echo !empty($active_lead['id']) ? (int)$active_lead['id'] : 0; ?>">
          <input type="hidden" name="call_log_id" id="hiddenCallLogId" value="0">
          <input type="hidden" name="customer_name" id="hiddenCustomerName" value="<?php echo !empty($active_lead['customer']) ? htmlspecialchars($active_lead['customer']) : ''; ?>">
          <input type="hidden" name="phone" id="hiddenPhone" value="<?php echo !empty($active_lead['phone_primary']) ? htmlspecialchars($active_lead['phone_primary']) : ''; ?>">
          <input type="hidden" name="call_duration" id="hiddenDuration" value="0">
          <input type="hidden" name="call_disposition" id="hiddenDisposition" value="Connected">

          <div class="row">
            <div class="col-md-6">
              <div class="glass-form-group">
                <label class="glass-form-label">Call Outcome / Status</label>
                <select name="status" id="followupStatusSelect" class="glass-form-select" required>
                  <?php if(!empty($statuses)){
                    foreach($statuses as $st){ ?>
                      <option value="<?php echo $st['id']; ?>" <?php echo (!empty($active_lead['status']) && $active_lead['status'] == $st['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($st['status']); ?>
                      </option>
                  <?php } } ?>
                </select>
              </div>
            </div>

            <div class="col-md-6">
              <div id="followupScheduleBlock" class="glass-form-group" style="display:none;">
                <label class="glass-form-label"><i class="fa fa-calendar"></i> Next Follow-up Schedule</label>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                  <input type="text" name="next_followup" id="nextFollowupDate" class="glass-form-input datepicker" placeholder="YYYY-MM-DD" autocomplete="off">
                  <input type="text" name="next_followup_time" id="nextFollowupTime" class="glass-form-input timepicker" placeholder="11:00 AM" value="11:00 AM">
                </div>
              </div>
            </div>
          </div>

          <div class="glass-form-group">
            <label class="glass-form-label">Discussion Remarks / Call Notes</label>
            <textarea name="remarks" id="followupRemarks" class="glass-form-textarea" rows="3" placeholder="Key points discussed during the call..." required></textarea>
          </div>

          <div class="glass-form-group">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
              <label class="glass-form-label" style="margin:0;">Vertical Information</label>
              <div style="display:flex; gap:6px;">
                <span class="glass-radio-pill active" id="tabVerticalAccounting" onclick="switchVerticalTab('accounting')">Accounting & Tax</span>
                <span class="glass-radio-pill" id="tabVerticalTelecom" onclick="switchVerticalTab('telecom')">Telecom</span>
                <span class="glass-radio-pill" id="tabVerticalGeneral" onclick="switchVerticalTab('general')">General</span>
              </div>
            </div>

            <div id="verticalAccountingSection" style="background:rgba(0,0,0,0.3); border:1px solid rgba(255,255,255,0.08); padding:16px; border-radius:18px;">
              <div class="row">
                <div class="col-md-6" style="margin-bottom:10px;">
                  <label class="glass-form-label" style="font-size:10px;">VAT Registration</label>
                  <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:6px;">
                    <span class="glass-radio-pill vat-pill" data-val="Registered">Registered</span>
                    <span class="glass-radio-pill vat-pill" data-val="Not Registered">Not Registered</span>
                    <span class="glass-radio-pill vat-pill" data-val="Pending">Pending</span>
                    <span class="glass-radio-pill vat-pill" data-val="Exempt">Exempt</span>
                  </div>
                  <input type="hidden" name="vat_status" id="vatStatusVal" value="">
                  <input type="text" name="vat_trn" id="vatTrnInput" class="glass-form-input" placeholder="VAT TRN Number (if registered)...">
                </div>

                <div class="col-md-6" style="margin-bottom:10px;">
                  <label class="glass-form-label" style="font-size:10px;">Corporate Tax (CT)</label>
                  <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:6px;">
                    <span class="glass-radio-pill ct-pill" data-val="Registered">Registered</span>
                    <span class="glass-radio-pill ct-pill" data-val="Not Registered">Not Registered</span>
                    <span class="glass-radio-pill ct-pill" data-val="Pending">Pending</span>
                    <span class="glass-radio-pill ct-pill" data-val="Exempt">Exempt</span>
                  </div>
                  <input type="hidden" name="corporate_tax_status" id="corporateTaxStatusVal" value="">
                  <input type="text" name="ct_trn" id="ctTrnInput" class="glass-form-input" placeholder="Corporate Tax TRN / Ref (if registered)...">
                </div>
              </div>
            </div>

            <div id="verticalTelecomSection" style="display:none; background:rgba(0,0,0,0.3); border:1px solid rgba(255,255,255,0.08); padding:16px; border-radius:18px;">
              <div class="row">
                <div class="col-md-6" style="margin-bottom:10px;">
                  <label class="glass-form-label" style="font-size:10px;">Current Telecom Provider</label>
                  <select name="business_category" id="telecomProviderSelect" class="glass-form-select">
                    <option value="">-- Select Provider --</option>
                    <option value="Etisalat">Etisalat by e&</option>
                    <option value="du">du Telecom</option>
                    <option value="Virgin">Virgin Mobile</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
                <div class="col-md-6" style="margin-bottom:10px;">
                  <label class="glass-form-label" style="font-size:10px;">Lines / Monthly Spend</label>
                  <input type="text" name="industry_category" id="telecomLinesInput" class="glass-form-input" placeholder="e.g. 15 Lines | AED 2,500/mo">
                </div>
              </div>
            </div>

            <div id="verticalGeneralSection" style="display:none; background:rgba(0,0,0,0.3); border:1px solid rgba(255,255,255,0.08); padding:16px; border-radius:18px;">
              <div>
                <label class="glass-form-label" style="font-size:10px;">Business Category</label>
                <input type="text" name="general_business_cat" id="generalBusinessCatInput" class="glass-form-input" placeholder="e.g. Real Estate, Retail, Consulting...">
              </div>
            </div>
          </div>

          <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid rgba(255,255,255,0.1); padding-top:16px;">
            <button type="button" class="glass-btn-dark" id="btnSaveOnly">
              <i class="fa fa-check"></i> Save Details
            </button>
            <button type="button" class="glass-btn-white" id="btnSaveAndNextContact">
              <span>Save & Next Contact</span> <i class="fa fa-arrow-right"></i>
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>

  <div id="drawerBackdrop" class="glass-drawer-backdrop"></div>

  <div id="queueSlidePanel" class="glass-slide-panel">
    <div class="glass-slide-header">
      <h3 style="margin:0; font-size:16px; font-weight:700; color:#fff;">Calling Queue (<span id="panelQueueCount"><?php echo count($queue); ?></span>)</h3>
      <button type="button" class="glass-dialer-backspace" id="btnCloseQueueDrawer"><i class="fa fa-times"></i></button>
    </div>
    <div class="glass-slide-body">
      <div style="margin-bottom:14px;">
        <input type="text" id="queueSearchInput" class="glass-form-input" placeholder="Search contact, phone or company...">
      </div>
      <div id="queueListContainer">
        <?php if(!empty($queue)){
          foreach($queue as $idx => $q){ ?>
            <div class="glass-queue-item <?php echo $idx === 0 ? 'active' : ''; ?>" data-lead-idx="<?php echo $idx; ?>">
              <div>
                <div style="font-size:14px; font-weight:700; color:#fff;"><?php echo htmlspecialchars($q['customer']); ?></div>
                <div style="font-size:11px; color:#94a3b8;"><?php echo !empty($q['phone_primary']) ? htmlspecialchars($q['phone_primary']) : htmlspecialchars($q['contact1']); ?></div>
              </div>
              <div>
                <span class="glass-badge"><?php echo !empty($q['status_name']) ? htmlspecialchars($q['status_name']) : 'Open'; ?></span>
              </div>
            </div>
        <?php } } else { ?>
          <div style="padding:20px; text-align:center; color:#94a3b8;">No contacts in queue</div>
        <?php } ?>
      </div>
    </div>
  </div>

  <div id="logsSlidePanel" class="glass-slide-panel">
    <div class="glass-slide-header">
      <h3 style="margin:0; font-size:16px; font-weight:700; color:#fff;">Past Call History</h3>
      <button type="button" class="glass-dialer-backspace" id="btnCloseLogsDrawer"><i class="fa fa-times"></i></button>
    </div>
    <div class="glass-slide-body">
      <div id="logsListContainer">
        <?php if(!empty($recent_logs)){
          foreach($recent_logs as $log){ 
            $raw_phone = !empty($log['phone_number']) ? $log['phone_number'] : '';
            $clean_phone = preg_replace('/[^\d\+]/', '', $raw_phone);
          ?>
            <div class="glass-phone-log-item">
              <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:4px;">
                <span style="font-size:14px; font-weight:700; color:#ffffff;"><?php echo htmlspecialchars($log['customer_name']); ?></span>
                <span class="glass-badge"><?php echo (int)$log['duration'] > 0 ? (int)$log['duration'] . 's' : '0s'; ?></span>
              </div>
              <div style="font-size:11px; color:#94a3b8; margin-bottom:10px;">
                <span><i class="fa fa-phone"></i> <?php echo htmlspecialchars($raw_phone); ?></span>
                <span>•</span>
                <span><?php echo htmlspecialchars($log['disposition']); ?></span>
                <span>•</span>
                <span><?php echo date('M d, H:i', strtotime($log['created_at'])); ?></span>
              </div>
              <div style="display:flex; gap:8px;">
                <button type="button" class="glass-btn-white" style="padding:4px 10px; font-size:11px;" onclick="dialFromLog('<?php echo htmlspecialchars(addslashes($clean_phone)); ?>', '<?php echo htmlspecialchars(addslashes($log['customer_name'])); ?>', <?php echo !empty($log['lead_id']) ? (int)$log['lead_id'] : 0; ?>)">
                  <i class="fa fa-phone"></i> Call
                </button>
                <button type="button" class="glass-btn-dark" style="padding:4px 10px; font-size:11px;" onclick="copyNumberToClipboard('<?php echo htmlspecialchars(addslashes($clean_phone)); ?>')">
                  <i class="fa fa-copy"></i> Copy
                </button>
                <?php if(!empty($log['lead_id'])){ ?>
                  <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($log['lead_id']); ?>" target="_blank" class="glass-btn-dark" style="padding:4px 10px; font-size:11px;">
                    <i class="fa fa-user"></i> View Lead
                  </a>
                <?php } ?>
              </div>
            </div>
        <?php } } else { ?>
          <div style="padding:20px; text-align:center; color:#94a3b8;">No past completed calls recorded</div>
        <?php } ?>
      </div>
    </div>
  </div>

  <div id="appleToast" class="glass-toast">
    <i class="fa fa-info-circle"></i>
    <span id="appleToastText">Notice message</span>
  </div>

</div>

<script>
var callingQueue = <?php echo json_encode(!empty($queue) ? $queue : array()); ?>;
var isBackendConfigured = <?php echo !empty($is_backend_configured) ? 'true' : 'false'; ?>;
var configuredDialerSeconds = <?php echo !empty($autodialer_seconds) ? (int)$autodialer_seconds : 60; ?>;
var currentIndex = 0;
var callState = 'idle';
var callTimerInterval = null;
var callConnectedSeconds = 0;
var activeCallLogId = 0;
var audioCtx = null;
var activePhoneType = 'primary';
var autoDialerActive = true;
var autoDialerCountdown = null;
var autoDialerSeconds = configuredDialerSeconds;
var autoDialerPaused = false;
var csrfTokenName = '<?php echo $this->security->get_csrf_token_name(); ?>';
var csrfTokenHash = '<?php echo $this->security->get_csrf_hash(); ?>';

$.ajaxSetup({
  beforeSend: function(xhr, settings) {
    if (settings.type === 'POST') {
      if (typeof settings.data === 'string') {
        if (settings.data.indexOf(csrfTokenName) === -1) {
          settings.data += (settings.data ? '&' : '') + encodeURIComponent(csrfTokenName) + '=' + encodeURIComponent(csrfTokenHash);
        }
      } else if (typeof settings.data === 'object' && settings.data !== null && !(settings.data instanceof FormData)) {
        settings.data[csrfTokenName] = csrfTokenHash;
      }
    }
  }
});

function getAudioContext() {
  if (!audioCtx) {
    var AudioCtx = window.AudioContext || window.webkitAudioContext;
    if (AudioCtx) {
      audioCtx = new AudioCtx();
    }
  }
  if (audioCtx && audioCtx.state === 'suspended') {
    audioCtx.resume();
  }
  return audioCtx;
}

function playDtmfTone(freq1, freq2, durationMs) {
  var ctx = getAudioContext();
  if (!ctx) return;
  var osc1 = ctx.createOscillator();
  var osc2 = ctx.createOscillator();
  var gain = ctx.createGain();

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
  } else {
    playDtmfTone(700, 900, 50);
  }
}

function showAppleToast(msg) {
  var toast = document.getElementById('appleToast');
  var toastText = document.getElementById('appleToastText');
  if (!toast || !toastText) return;
  toastText.textContent = msg;
  toast.style.display = 'flex';
  setTimeout(function() {
    toast.style.display = 'none';
  }, 2800);
}

function copyNumberToClipboard(num) {
  if (!num) return;
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(num).then(function() {
      showAppleToast('Copied ' + num + ' to clipboard');
    });
  } else {
    var ta = document.createElement('textarea');
    ta.value = num;
    document.body.appendChild(ta);
    ta.select();
    document.execCommand('copy');
    document.body.removeChild(ta);
    showAppleToast('Copied ' + num + ' to clipboard');
  }
}

function formatTimer(secs) {
  var m = Math.floor(secs / 60);
  var s = secs % 60;
  return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
}

function switchToCard(cardName) {
  if (cardName === 'details') {
    $('#viewCardDialer').hide();
    $('#viewCardDetailFill').fadeIn(150);
    $('#pillTabDialer').removeClass('active');
    $('#pillTabDetails').addClass('active');
    $('#dockBtnDialer').removeClass('active');
    $('#dockBtnDetails').addClass('active');
    checkFollowupScheduleRequirement();
  } else {
    $('#viewCardDetailFill').hide();
    $('#viewCardDialer').fadeIn(150);
    $('#pillTabDetails').removeClass('active');
    $('#pillTabDialer').addClass('active');
    $('#dockBtnDetails').removeClass('active');
    $('#dockBtnDialer').addClass('active');
  }
}

function selectPhoneTarget(type) {
  activePhoneType = type;
  var lead = callingQueue[currentIndex] || {};
  var num = '';

  if (type === 'alt') {
    num = lead.phone_alt || '';
    $('#cardAltPhone').addClass('active-target');
    $('#cardPrimaryPhone').removeClass('active-target');
    $('#badgeAltActive').show();
    $('#badgePrimaryActive').hide();
  } else {
    num = lead.phone_primary || lead.contact1 || '';
    $('#cardPrimaryPhone').addClass('active-target');
    $('#cardAltPhone').removeClass('active-target');
    $('#badgePrimaryActive').show();
    $('#badgeAltActive').hide();
  }

  if (num) {
    document.getElementById('dialerInput').value = num;
    document.getElementById('hiddenPhone').value = num;
    document.getElementById('btnFallbackTel').href = 'tel:' + num.replace(/[^\d\+]/g, '');
  }
}

function loadContactAt(idx) {
  if (callingQueue.length === 0) return;
  if (idx < 0) idx = 0;
  if (idx >= callingQueue.length) idx = callingQueue.length - 1;
  currentIndex = idx;

  var lead = callingQueue[currentIndex];
  document.getElementById('displayCustomerName').textContent = lead.customer || 'Unnamed Contact';
  document.getElementById('detailsCardCustomer').textContent = lead.customer || 'Unnamed Contact';
  document.getElementById('displayLeadCode').textContent = '#' + (lead.lead_id || lead.id || '—');
  document.getElementById('displayCategoryPill').textContent = lead.category || lead.p_cat || 'General Contact';
  document.getElementById('displayLeadStatus').textContent = lead.status_name || 'Open';

  var primaryPhone = lead.phone_primary || lead.contact1 || '';
  var altPhone = lead.phone_alt || '';

  document.getElementById('displayPrimaryPhone').textContent = primaryPhone || 'No Number';
  document.getElementById('displayAltPhone').textContent = altPhone || '—';

  selectPhoneTarget('primary');

  document.getElementById('displayPreviousRemarks').textContent = lead.remark || 'No previous call notes recorded for this contact.';
  document.getElementById('displayPoc').textContent = lead.poc || '—';

  var locParts = [];
  if (lead.tl_authority) locParts.push(lead.tl_authority);
  if (lead.tl_emirate) locParts.push(lead.tl_emirate);
  document.getElementById('displayAuthority').textContent = locParts.length ? locParts.join(', ') : '—';

  document.getElementById('queueNavIndicator').textContent = 'Contact ' + (currentIndex + 1) + ' of ' + callingQueue.length;

  document.getElementById('hiddenLeadId').value = lead.id || 0;
  document.getElementById('hiddenCustomerName').value = lead.customer || '';

  $('.glass-queue-item').removeClass('active');
  $('.glass-queue-item[data-lead-idx="' + currentIndex + '"]').addClass('active');

  var catLower = (lead.category || lead.p_cat || '').toLowerCase();
  if (catLower.indexOf('telecom') !== -1) {
    switchVerticalTab('telecom');
  } else if (catLower.indexOf('account') !== -1 || catLower.indexOf('tax') !== -1 || catLower.indexOf('vat') !== -1) {
    switchVerticalTab('accounting');
  }
}

function switchVerticalTab(tabName) {
  $('.glass-radio-pill#tabVerticalAccounting, .glass-radio-pill#tabVerticalTelecom, .glass-radio-pill#tabVerticalGeneral').removeClass('active');
  $('#verticalAccountingSection, #verticalTelecomSection, #verticalGeneralSection').hide();

  if (tabName === 'telecom') {
    $('#tabVerticalTelecom').addClass('active');
    $('#verticalTelecomSection').show();
  } else if (tabName === 'general') {
    $('#tabVerticalGeneral').addClass('active');
    $('#verticalGeneralSection').show();
  } else {
    $('#tabVerticalAccounting').addClass('active');
    $('#verticalAccountingSection').show();
  }
}

function checkFollowupScheduleRequirement() {
  var sel = document.getElementById('followupStatusSelect');
  var txt = sel.options[sel.selectedIndex] ? sel.options[sel.selectedIndex].text.toLowerCase() : '';
  var block = document.getElementById('followupScheduleBlock');
  if (txt.indexOf('call') !== -1 || txt.indexOf('follow') !== -1 || txt.indexOf('meeting') !== -1 || txt.indexOf('prospective') !== -1 || txt.indexOf('interested') !== -1) {
    block.style.display = 'block';
  } else {
    block.style.display = 'none';
  }
}

function dialFromLog(phone, customerName, leadId) {
  $('#logsSlidePanel').removeClass('show');
  $('#drawerBackdrop').removeClass('show');

  document.getElementById('dialerInput').value = phone;
  document.getElementById('displayCustomerName').textContent = customerName || 'Direct Dial';
  document.getElementById('detailsCardCustomer').textContent = customerName || 'Direct Dial';
  document.getElementById('displayPrimaryPhone').textContent = phone;
  document.getElementById('hiddenPhone').value = phone;
  document.getElementById('hiddenCustomerName').value = customerName || 'Direct Dial';
  document.getElementById('hiddenLeadId').value = leadId || 0;

  switchToCard('dialer');
  startCrmCall();
}

function startCrmCall() {
  var phone = document.getElementById('dialerInput').value.trim();
  if (!phone) {
    showAppleToast('Please enter or select a phone number');
    return;
  }

  stopWrapupCountdown();
  $('#autodialerWrapupBanner').slideUp(180);

  var leadId = document.getElementById('hiddenLeadId').value;
  var customer = document.getElementById('hiddenCustomerName').value;

  $.ajax({
    type: 'POST',
    url: '<?php echo base_url(); ?>manager/calls/ajax_initiate_call',
    data: {
      phone: phone,
      lead_id: leadId,
      customer_name: customer
    },
    dataType: 'json'
  }).done(function(res) {
    if (res && res.call_log_id) {
      activeCallLogId = res.call_log_id;
      document.getElementById('hiddenCallLogId').value = activeCallLogId;
    }

    if (res && !res.configured) {
      callState = 'idle';
      var cleanP = phone.replace(/[^\d\+]/g, '');
      document.getElementById('btnFallbackTel').href = 'tel:' + cleanP;
      document.getElementById('backendOfflineMsg').textContent = 'Calling API is not configured (API key / PBX not connected). Dial directly via device dialer below.';
      $('#backendOfflineAlert').slideDown(180);
      document.getElementById('liveCallSubtitle').textContent = 'Backend offline: use phone fallback';
      return;
    }

    $('#backendOfflineAlert').slideUp(180);
    callState = 'calling';
    callConnectedSeconds = 0;
    if (callTimerInterval) {
      clearInterval(callTimerInterval);
      callTimerInterval = null;
    }

    var btn = document.getElementById('btnToggleCall');
    btn.className = 'glass-call-btn btn-end';
    btn.innerHTML = '<i class="fa fa-phone-slash"></i>';
    document.getElementById('liveCallSubtitle').textContent = 'Dialing ' + phone + ' via 3CX...';

    setTimeout(function() {
      if (callState === 'calling') {
        callState = 'connected';
        document.getElementById('liveCallSubtitle').textContent = 'Connected • 00:00';
        callTimerInterval = setInterval(function() {
          if (callState === 'connected') {
            callConnectedSeconds++;
            document.getElementById('liveCallSubtitle').textContent = 'Connected • ' + formatTimer(callConnectedSeconds);
            document.getElementById('hiddenDuration').value = callConnectedSeconds;
            document.getElementById('followupCallDurationBadge').textContent = formatTimer(callConnectedSeconds);
          }
        }, 1000);
      }
    }, 2000);

    if (res && res.dispatch && res.dispatch.webclient_url) {
      window.open(res.dispatch.webclient_url, '_blank');
    }
  }).fail(function() {
    showAppleToast('Server communication error');
  });
}

function endCrmCall() {
  if (callState === 'idle') return;

  callState = 'idle';
  if (callTimerInterval) {
    clearInterval(callTimerInterval);
    callTimerInterval = null;
  }

  var btn = document.getElementById('btnToggleCall');
  btn.className = 'glass-call-btn btn-start';
  btn.innerHTML = '<i class="fa fa-phone"></i>';

  document.getElementById('liveCallSubtitle').textContent = 'Call Ended • Ready for notes';

  var leadId = parseInt($('#hiddenLeadId').val(), 10) || 0;
  if (autoDialerActive) {
    $.ajax({
      type: 'POST',
      url: '<?php echo base_url(); ?>manager/calls/ajax_autodial_call_ended',
      data: { lead_id: leadId, duration: callConnectedSeconds },
      dataType: 'json'
    });
    startWrapupCountdown(configuredDialerSeconds);
  }

  if (activeCallLogId > 0) {
    runAiAnalysis(activeCallLogId, true);
  }

  switchToCard('details');
}

function runAiAnalysis(callLogId, silent) {
  if (!callLogId) return;
  if (!silent) {
    showAppleToast('Analyzing call with Groq AI...');
  }
  $.ajax({
    type: 'POST',
    url: '<?php echo base_url(); ?>manager/calls/ajax_process_ai',
    data: { call_log_id: callLogId },
    dataType: 'json'
  }).done(function(res) {
    if (res && res.status) {
      if (!silent) {
        showAppleToast('AI Analysis completed: ' + (res.data ? res.data.next_status : ''));
      }
      refreshCallLogs();
    } else if (!silent) {
      showAppleToast('AI Notice: ' + (res.message || 'Processing queue'));
    }
  });
}

function startWrapupCountdown(seconds) {
  stopWrapupCountdown();
  autoDialerSeconds = seconds || configuredDialerSeconds;
  autoDialerPaused = false;
  $('#autodialerTimerDisplay').text(autoDialerSeconds);
  $('#autodialerWrapupBanner').slideDown(180);

  autoDialerCountdown = setInterval(function() {
    if (autoDialerPaused) return;
    autoDialerSeconds--;
    $('#autodialerTimerDisplay').text(autoDialerSeconds);

    if (autoDialerSeconds <= 0) {
      stopWrapupCountdown();
      autoDialNextLead();
    }
  }, 1000);
}

function stopWrapupCountdown() {
  if (autoDialerCountdown) {
    clearInterval(autoDialerCountdown);
    autoDialerCountdown = null;
  }
}

function autoDialNextLead() {
  stopWrapupCountdown();
  $('#autodialerWrapupBanner').slideUp(180);

  saveCallDetails(function() {
    dispatchNextAutodialLead();
  }, true);
}

function dispatchNextAutodialLead() {
  $.ajax({
    type: 'POST',
    url: '<?php echo base_url(); ?>manager/calls/ajax_autodial_next',
    dataType: 'json'
  }).done(function(res) {
    if (res && res.status && res.lead) {
      var nextLead = res.lead;
      var foundIdx = -1;
      for (var i = 0; i < callingQueue.length; i++) {
        if (callingQueue[i].id == nextLead.id) {
          foundIdx = i;
          break;
        }
      }
      if (foundIdx >= 0) {
        loadContactAt(foundIdx);
      } else {
        callingQueue.unshift(nextLead);
        loadContactAt(0);
      }
      switchToCard('dialer');
      showAppleToast('Auto-Dialing: ' + (nextLead.customer || 'Next Contact'));
      setTimeout(function() {
        startCrmCall();
      }, 700);
    } else if (currentIndex < callingQueue.length - 1) {
      loadContactAt(currentIndex + 1);
      switchToCard('dialer');
      showAppleToast('Auto-Dialing: ' + (callingQueue[currentIndex].customer || 'Next Contact'));
      setTimeout(function() {
        startCrmCall();
      }, 700);
    } else {
      showAppleToast('Auto-Dialer queue completed');
    }
  }).fail(function() {
    if (currentIndex < callingQueue.length - 1) {
      loadContactAt(currentIndex + 1);
      switchToCard('dialer');
      showAppleToast('Auto-Dialing: ' + (callingQueue[currentIndex].customer || 'Next Contact'));
      setTimeout(function() {
        startCrmCall();
      }, 700);
    } else {
      showAppleToast('Auto-Dialer queue completed');
    }
  });
}

function refreshCallLogs() {
  $.ajax({
    type: 'GET',
    url: '<?php echo base_url(); ?>manager/calls/ajax_get_call_logs',
    dataType: 'json'
  }).done(function(resp) {
    if (resp && resp.logs) {
      var container = document.getElementById('logsListContainer');
      if (!container) return;
      if (resp.logs.length === 0) {
        container.innerHTML = '<div style="padding:20px; text-align:center; color:#94a3b8;">No past completed calls recorded</div>';
        return;
      }
      var html = '';
      resp.logs.forEach(function(log) {
        var rawPhone = log.phone_number || '';
        var cleanPhone = rawPhone.replace(/[^\d\+]/g, '');
        var dur = parseInt(log.duration || 0, 10);

        html += '<div class="glass-phone-log-item">';
        html += '<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:4px;">';
        html += '<span style="font-size:14px; font-weight:700; color:#ffffff;">' + (log.customer_name || 'Direct Call') + '</span>';
        html += '<span class="glass-badge">' + dur + 's</span>';
        html += '</div>';

        html += '<div style="font-size:11px; color:#94a3b8; margin-bottom:10px;">';
        html += '<span><i class="fa fa-phone"></i> ' + rawPhone + '</span>';
        html += '<span>•</span>';
        html += '<span>' + (log.disposition || 'Completed') + '</span>';
        html += '<span>•</span>';
        html += '<span>' + (log.created_at || '') + '</span>';
        html += '</div>';

        html += '<div style="display:flex; gap:8px;">';
        html += '<button type="button" class="glass-btn-white" style="padding:4px 10px; font-size:11px;" onclick="dialFromLog(\'' + cleanPhone + '\', \'' + (log.customer_name || '').replace(/'/g, "\\'") + '\', ' + (log.lead_id || 0) + ')"><i class="fa fa-phone"></i> Call</button>';
        html += '<button type="button" class="glass-btn-dark" style="padding:4px 10px; font-size:11px;" onclick="copyNumberToClipboard(\'' + cleanPhone + '\')"><i class="fa fa-copy"></i> Copy</button>';
        if (log.lead_id) {
          html += '<a href="<?php echo base_url(); ?>manager/leads/view/' + btoa(log.lead_id) + '" target="_blank" class="glass-btn-dark" style="padding:4px 10px; font-size:11px;"><i class="fa fa-user"></i> View Lead</a>';
        }
        html += '</div>';
        html += '</div>';
      });
      container.innerHTML = html;
    }
  });
}

function saveCallDetails(callbackAfterSave, isAuto) {
  var form = $('#formCallFollowup');
  var remarks = $('#followupRemarks').val().trim();
  if (!remarks) {
    if (isAuto) {
      remarks = 'Auto-logged call completion';
      $('#followupRemarks').val(remarks);
    } else {
      showAppleToast('Please enter call remarks');
      $('#followupRemarks').focus();
      return;
    }
  }

  var formData = form.serialize();
  formData += '&call_duration=' + callConnectedSeconds;
  formData += '&phone=' + encodeURIComponent($('#dialerInput').val().trim());

  var btnNext = $('#btnSaveAndNextContact');
  btnNext.prop('disabled', true).text('Saving...');

  $.ajax({
    type: 'POST',
    url: '<?php echo base_url(); ?>manager/calls/ajax_save_call',
    data: formData,
    dataType: 'json'
  }).done(function(resp) {
    btnNext.prop('disabled', false).html('<span>Save & Next Contact</span> <i class="fa fa-arrow-right"></i>');
    if (resp && resp.status) {
      showAppleToast('Call details saved successfully');
      $('#followupRemarks').val('');
      refreshCallLogs();

      if (typeof callbackAfterSave === 'function') {
        callbackAfterSave(resp);
      }
    } else {
      showAppleToast('Error saving call log');
      if (isAuto && typeof callbackAfterSave === 'function') {
        callbackAfterSave(null);
      }
    }
  }).fail(function() {
    btnNext.prop('disabled', false).html('<span>Save & Next Contact</span> <i class="fa fa-arrow-right"></i>');
    showAppleToast('Network error while saving');
    if (isAuto && typeof callbackAfterSave === 'function') {
      callbackAfterSave(null);
    }
  });
}

$(document).ready(function() {

  $('.datepicker').datepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
    todayHighlight: true
  });

  $('.glass-key-btn').on('click', function() {
    var key = $(this).data('key');
    playKeyTone(key);
    var input = document.getElementById('dialerInput');
    input.value += key;
  });

  $('#btnDialerBackspace').on('click', function() {
    var input = document.getElementById('dialerInput');
    input.value = input.value.slice(0, -1);
    playDtmfTone(350, 400, 50);
  });

  $('#btnToggleCall').on('click', function() {
    if (callState === 'idle') {
      startCrmCall();
    } else {
      endCrmCall();
    }
  });

  $('#pillTabDialer, #dockBtnDialer').on('click', function() {
    switchToCard('dialer');
  });

  $('#pillTabDetails, #dockBtnDetails').on('click', function() {
    switchToCard('details');
  });

  $('#btnPrevLead').on('click', function() {
    if (currentIndex > 0) {
      loadContactAt(currentIndex - 1);
    }
  });

  $('#btnNextLead').on('click', function() {
    if (currentIndex < callingQueue.length - 1) {
      loadContactAt(currentIndex + 1);
    }
  });

  $('#followupStatusSelect').on('change', function() {
    checkFollowupScheduleRequirement();
  });

  $('.vat-pill').on('click', function() {
    $('.vat-pill').removeClass('selected');
    $(this).addClass('selected');
    $('#vatStatusVal').val($(this).data('val'));
  });

  $('.ct-pill').on('click', function() {
    $('.ct-pill').removeClass('selected');
    $(this).addClass('selected');
    $('#corporateTaxStatusVal').val($(this).data('val'));
  });

  $('#btnSaveAndNextContact').on('click', function() {
    saveCallDetails(function(resp) {
      if (currentIndex < callingQueue.length - 1) {
        loadContactAt(currentIndex + 1);
        switchToCard('dialer');
        showAppleToast('Loaded next contact: ' + (callingQueue[currentIndex].customer || ''));
        if (autoDialerActive) {
          startWrapupCountdown(configuredDialerSeconds);
        }
      } else {
        showAppleToast('You have reached the end of the calling queue');
      }
    });
  });

  $('#btnSaveOnly').on('click', function() {
    saveCallDetails(function() {
      showAppleToast('Details saved for current contact');
    });
  });

  $('#dockBtnAutoDialToggle').on('click', function() {
    autoDialerActive = !autoDialerActive;
    if (autoDialerActive) {
      $('#autoDialDockLbl').text(configuredDialerSeconds + 's');
      showAppleToast('Auto-Dialer active (' + configuredDialerSeconds + 's countdown)');
      startWrapupCountdown(configuredDialerSeconds);
    } else {
      $('#autoDialDockLbl').text('Off');
      stopWrapupCountdown();
      $('#autodialerWrapupBanner').slideUp(180);
      showAppleToast('Auto-Dialer stopped');
    }
  });

  $('#btnAutodialExtend').on('click', function() {
    autoDialerSeconds += 30;
    $('#autodialerTimerDisplay').text(autoDialerSeconds);
    showAppleToast('Extended by +30 seconds');
    $.ajax({
      type: 'POST',
      url: '<?php echo base_url(); ?>manager/calls/ajax_autodial_extend',
      dataType: 'json'
    });
  });

  $('#btnAutodialPause').on('click', function() {
    autoDialerPaused = !autoDialerPaused;
    if (autoDialerPaused) {
      $(this).html('<i class="fa fa-play"></i> Resume');
      showAppleToast('Timer paused');
      $.ajax({
        type: 'POST',
        url: '<?php echo base_url(); ?>manager/calls/ajax_autodial_pause',
        dataType: 'json'
      });
    } else {
      $(this).html('<i class="fa fa-pause"></i> Pause');
      showAppleToast('Timer resumed');
      $.ajax({
        type: 'POST',
        url: '<?php echo base_url(); ?>manager/calls/ajax_autodial_resume',
        dataType: 'json'
      });
    }
  });

  $('#btnAutodialNextNow').on('click', function() {
    autoDialNextLead();
  });

  $('#btnAutodialStop').on('click', function() {
    autoDialerActive = false;
    $('#autoDialDockLbl').text('Off');
    stopWrapupCountdown();
    $('#autodialerWrapupBanner').slideUp(180);
    showAppleToast('Auto-Dialer stopped');
  });

  $('#dockBtnQueue').on('click', function() {
    $('#drawerBackdrop').addClass('show');
    $('#queueSlidePanel').addClass('show');
  });

  $('#btnCloseQueueDrawer, #drawerBackdrop').on('click', function() {
    $('#queueSlidePanel').removeClass('show');
    $('#logsSlidePanel').removeClass('show');
    $('#drawerBackdrop').removeClass('show');
  });

  $('#dockBtnLogs').on('click', function() {
    refreshCallLogs();
    $('#drawerBackdrop').addClass('show');
    $('#logsSlidePanel').addClass('show');
  });

  $('#btnCloseLogsDrawer').on('click', function() {
    $('#logsSlidePanel').removeClass('show');
    $('#drawerBackdrop').removeClass('show');
  });

  $(document).on('click', '.glass-queue-item', function() {
    var idx = parseInt($(this).data('lead-idx'), 10);
    loadContactAt(idx);
    $('#queueSlidePanel').removeClass('show');
    $('#drawerBackdrop').removeClass('show');
  });

  $('#queueSearchInput').on('input', function() {
    var q = $(this).val().toLowerCase().trim();
    $('.glass-queue-item').each(function() {
      var txt = $(this).text().toLowerCase();
      if (!q || txt.indexOf(q) !== -1) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  });

  if (callingQueue.length > 0) {
    loadContactAt(0);
  }
});
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
