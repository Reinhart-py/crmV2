<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>

<style>
.apple-calling-container {
  background: #090a0f radial-gradient(circle at 50% 0%, rgba(37, 99, 235, 0.12), transparent 60%);
  min-height: calc(100vh - 100px);
  padding: 16px 20px;
  color: #f8fafc;
  font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "SF Pro Text", "Segoe UI", Roboto, sans-serif;
  box-sizing: border-box;
  position: relative;
  overflow-x: hidden;
}

.apple-top-nav {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: rgba(18, 24, 38, 0.75);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 16px;
  padding: 10px 18px;
  margin-bottom: 18px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
}

.apple-top-title h2 {
  margin: 0;
  font-size: 17px;
  font-weight: 600;
  color: #ffffff;
  letter-spacing: -0.2px;
}

.apple-top-actions {
  display: flex;
  align-items: center;
  gap: 10px;
}

.apple-btn-pill {
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: #f1f5f9;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: all 0.18s ease;
}

.apple-btn-pill:hover {
  background: rgba(255, 255, 255, 0.12);
  color: #ffffff;
}

.apple-btn-pill.active {
  background: #2563eb;
  border-color: #2563eb;
  color: #ffffff;
}

.apple-stage-grid {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 20px;
  max-width: 1100px;
  margin: 0 auto;
}

@media (max-width: 900px) {
  .apple-stage-grid {
    grid-template-columns: 1fr;
  }
  .apple-dialer-card {
    order: -1;
  }
}

.apple-card {
  background: rgba(18, 24, 38, 0.75);
  backdrop-filter: blur(24px);
  -webkit-backdrop-filter: blur(24px);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 22px;
  padding: 24px;
  box-shadow: 0 20px 45px rgba(0, 0, 0, 0.4);
  position: relative;
}

.apple-client-header {
  border-bottom: 1px solid rgba(255, 255, 255, 0.07);
  padding-bottom: 18px;
  margin-bottom: 18px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.apple-client-name {
  font-size: 22px;
  font-weight: 700;
  color: #ffffff;
  margin: 0 0 4px 0;
  letter-spacing: -0.3px;
}

.apple-client-sub {
  font-size: 13px;
  color: #94a3b8;
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.apple-badge {
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.1);
  padding: 2px 8px;
  border-radius: 6px;
  font-size: 11px;
  color: #cbd5e1;
  font-weight: 500;
}

.apple-badge-cat {
  background: rgba(37, 99, 235, 0.18);
  border-color: rgba(37, 99, 235, 0.35);
  color: #93c5fd;
}

.apple-queue-nav {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  color: #94a3b8;
}

.apple-nav-btn {
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: #f1f5f9;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.15s ease;
}

.apple-nav-btn:hover {
  background: rgba(255, 255, 255, 0.15);
}

.apple-dual-phones-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-bottom: 16px;
}

@media (max-width: 600px) {
  .apple-dual-phones-row {
    grid-template-columns: 1fr;
  }
}

.apple-phone-card {
  background: rgba(0, 0, 0, 0.25);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 14px;
  padding: 10px 14px;
  cursor: pointer;
  transition: all 0.15s ease;
  position: relative;
}

.apple-phone-card:hover {
  background: rgba(255, 255, 255, 0.06);
  border-color: rgba(37, 99, 235, 0.5);
}

.apple-phone-card.active-target {
  background: rgba(37, 99, 235, 0.15);
  border-color: #2563eb;
  box-shadow: 0 0 14px rgba(37, 99, 235, 0.25);
}

.apple-phone-card-label {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 10px;
  font-weight: 600;
  color: #94a3b8;
  text-transform: uppercase;
  margin-bottom: 4px;
}

.apple-phone-card-num {
  font-size: 16px;
  font-weight: 600;
  color: #ffffff;
  letter-spacing: 0.5px;
}

.apple-remarks-preview {
  background: rgba(255, 255, 255, 0.03);
  border-left: 3px solid #2563eb;
  padding: 12px 16px;
  border-radius: 0 12px 12px 0;
  margin-bottom: 16px;
}

.apple-remarks-preview-label {
  font-size: 11px;
  font-weight: 600;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 4px;
}

.apple-remarks-preview-text {
  font-size: 13px;
  color: #e2e8f0;
  line-height: 1.45;
  margin: 0;
  max-height: 60px;
  overflow-y: auto;
}

.apple-meta-strip {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
}

.apple-meta-item {
  background: rgba(0, 0, 0, 0.2);
  border: 1px solid rgba(255, 255, 255, 0.04);
  border-radius: 10px;
  padding: 8px 12px;
}

.apple-meta-item-label {
  font-size: 10px;
  color: #64748b;
  text-transform: uppercase;
  margin-bottom: 2px;
}

.apple-meta-item-val {
  font-size: 12px;
  font-weight: 600;
  color: #f1f5f9;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.apple-dialer-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.apple-dialer-screen {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: rgba(0, 0, 0, 0.35);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 14px;
  padding: 10px 14px;
  margin-bottom: 14px;
}

.apple-dialer-input {
  background: transparent;
  border: none;
  font-size: 22px;
  font-weight: 600;
  color: #ffffff;
  outline: none;
  width: 100%;
  text-align: center;
  letter-spacing: 1px;
}

.apple-dialer-backspace {
  background: transparent;
  border: none;
  color: #64748b;
  font-size: 16px;
  cursor: pointer;
  padding: 4px;
  transition: color 0.15s ease;
}

.apple-dialer-backspace:hover {
  color: #ffffff;
}

.apple-backend-alert {
  width: 100%;
  background: rgba(220, 38, 38, 0.12);
  border: 1px solid rgba(220, 38, 38, 0.3);
  border-radius: 12px;
  padding: 10px 14px;
  margin-bottom: 14px;
  font-size: 12px;
  color: #fca5a5;
  text-align: center;
  display: none;
}

.apple-keypad-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
  margin-bottom: 18px;
}

.apple-key-btn {
  width: 68px;
  height: 68px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.09);
  color: #ffffff;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.12s ease;
  user-select: none;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

.apple-key-btn:hover {
  background: rgba(255, 255, 255, 0.14);
  transform: translateY(-1px);
}

.apple-key-btn:active {
  transform: scale(0.94);
  background: rgba(255, 255, 255, 0.22);
}

.apple-key-digit {
  font-size: 22px;
  font-weight: 500;
  line-height: 1;
}

.apple-key-letters {
  font-size: 8px;
  color: #94a3b8;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  margin-top: 3px;
}

.apple-call-action-row {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
}

.apple-call-btn {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  border: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 26px;
  color: #ffffff;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  box-shadow: 0 8px 24px rgba(34, 197, 94, 0.4);
}

.apple-call-btn.btn-start {
  background: #22c55e;
}

.apple-call-btn.btn-start:hover {
  background: #16a34a;
  transform: scale(1.05);
  box-shadow: 0 10px 30px rgba(34, 197, 94, 0.55);
}

.apple-call-btn.btn-end {
  background: #ef4444;
  box-shadow: 0 8px 24px rgba(239, 68, 68, 0.4);
  animation: applePulseRed 1.8s infinite;
}

.apple-call-btn.btn-end:hover {
  background: #dc2626;
  transform: scale(1.05);
  box-shadow: 0 10px 30px rgba(239, 68, 68, 0.55);
}

@keyframes applePulseRed {
  0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.6); }
  70% { box-shadow: 0 0 0 14px rgba(239, 68, 68, 0); }
  100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
}

.apple-drawer-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.65);
  backdrop-filter: blur(6px);
  -webkit-backdrop-filter: blur(6px);
  z-index: 1050;
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s ease;
}

.apple-drawer-backdrop.show {
  opacity: 1;
  visibility: visible;
}

.apple-slide-panel {
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  width: 540px;
  max-width: 100vw;
  height: 100vh;
  max-height: 100vh;
  background: #0f131d;
  border-left: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: -15px 0 40px rgba(0, 0, 0, 0.7);
  z-index: 1060;
  transform: translateX(100%);
  transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.apple-slide-panel.show {
  transform: translateX(0);
}

.apple-slide-header {
  flex-shrink: 0;
  padding: 16px 20px;
  background: rgba(18, 24, 38, 0.98);
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.apple-slide-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: #ffffff;
}

.apple-close-btn {
  background: transparent;
  border: none;
  color: #94a3b8;
  font-size: 18px;
  cursor: pointer;
  padding: 4px;
}

.apple-close-btn:hover {
  color: #ffffff;
}

.apple-slide-body {
  flex: 1 1 auto;
  padding: 18px 20px;
  overflow-y: auto;
  overflow-x: hidden;
  -webkit-overflow-scrolling: touch;
}

.apple-slide-footer {
  flex-shrink: 0;
  padding: 14px 20px;
  background: #0f131d;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  position: sticky;
  bottom: 0;
  z-index: 10;
}

.apple-form-group {
  margin-bottom: 14px;
}

.apple-form-label {
  display: block;
  font-size: 11px;
  font-weight: 600;
  color: #cbd5e1;
  margin-bottom: 5px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.apple-form-input,
.apple-form-select,
.apple-form-textarea {
  width: 100%;
  background: rgba(0, 0, 0, 0.4);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 10px;
  padding: 9px 12px;
  font-size: 13px;
  color: #ffffff;
  outline: none;
  transition: border-color 0.15s ease, box-shadow 0.15s ease;
  box-sizing: border-box;
}

.apple-form-input:focus,
.apple-form-select:focus,
.apple-form-textarea:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.25);
}

.apple-form-select option {
  background: #111827;
  color: #ffffff;
}

.apple-btn-primary {
  background: #2563eb;
  border: 1px solid #2563eb;
  color: #ffffff;
  padding: 10px 18px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all 0.15s ease;
}

.apple-btn-primary:hover {
  background: #1d4ed8;
  border-color: #1d4ed8;
}

.apple-btn-secondary {
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: #cbd5e1;
  padding: 10px 16px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.15s ease;
}

.apple-btn-secondary:hover {
  background: rgba(255, 255, 255, 0.15);
  color: #ffffff;
}

.apple-pills-row {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}

.apple-radio-pill {
  padding: 5px 10px;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  font-size: 11px;
  color: #cbd5e1;
  cursor: pointer;
  user-select: none;
  transition: all 0.15s ease;
}

.apple-radio-pill:hover {
  background: rgba(255, 255, 255, 0.12);
}

.apple-radio-pill.selected {
  background: #2563eb;
  border-color: #2563eb;
  color: #ffffff;
  font-weight: 600;
}

.apple-toast {
  position: fixed;
  bottom: 24px;
  right: 24px;
  background: rgba(18, 24, 38, 0.95);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 14px;
  padding: 12px 18px;
  font-size: 13px;
  color: #ffffff;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
  z-index: 2000;
  display: none;
  align-items: center;
  gap: 10px;
}

.apple-phone-log-item {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 14px;
  padding: 14px 16px;
  margin-bottom: 10px;
  transition: all 0.15s ease;
}

.apple-phone-log-item:hover {
  background: rgba(255, 255, 255, 0.07);
  border-color: rgba(255, 255, 255, 0.12);
}

.apple-phone-log-top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 4px;
}

.apple-phone-log-name {
  font-size: 14px;
  font-weight: 600;
  color: #ffffff;
}

.apple-phone-log-meta {
  font-size: 11px;
  color: #94a3b8;
  display: flex;
  align-items: center;
  gap: 6px;
  margin-bottom: 10px;
}

.apple-phone-log-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.apple-log-action-btn {
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.09);
  color: #e2e8f0;
  padding: 5px 10px;
  border-radius: 8px;
  font-size: 11px;
  font-weight: 500;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  transition: all 0.15s ease;
}

.apple-log-action-btn:hover {
  background: rgba(255, 255, 255, 0.14);
  color: #ffffff;
}

.apple-log-action-btn.btn-dial {
  background: rgba(34, 197, 94, 0.18);
  border-color: rgba(34, 197, 94, 0.35);
  color: #4ade80;
}

.apple-log-action-btn.btn-dial:hover {
  background: #22c55e;
  color: #ffffff;
}

.apple-queue-item {
  padding: 12px 14px;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 12px;
  margin-bottom: 8px;
  cursor: pointer;
  transition: all 0.15s ease;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.apple-queue-item:hover {
  background: rgba(255, 255, 255, 0.08);
  border-color: rgba(37, 99, 235, 0.4);
}

.apple-queue-item.active {
  background: rgba(37, 99, 235, 0.15);
  border-color: #2563eb;
}

.apple-ai-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 2px 7px;
  border-radius: 9999px;
  font-size: 10px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.apple-ai-badge-pos {
  background: rgba(22, 163, 74, 0.2);
  color: #4ade80;
  border: 1px solid rgba(22, 163, 74, 0.4);
}
.apple-ai-badge-neu {
  background: rgba(59, 130, 246, 0.2);
  color: #60a5fa;
  border: 1px solid rgba(59, 130, 246, 0.4);
}
.apple-ai-badge-neg {
  background: rgba(220, 38, 38, 0.2);
  color: #f87171;
  border: 1px solid rgba(220, 38, 38, 0.4);
}

.apple-log-ai-summary {
  margin-top: 8px;
  padding: 8px 10px;
  background: rgba(15, 23, 42, 0.6);
  border-left: 2px solid #3b82f6;
  border-radius: 6px;
  font-size: 11px;
  color: #cbd5e1;
  line-height: 1.45;
}

.apple-transcript-box {
  margin-top: 6px;
  padding: 8px 10px;
  background: rgba(0, 0, 0, 0.4);
  border-radius: 6px;
  font-size: 11px;
  color: #94a3b8;
  max-height: 140px;
  overflow-y: auto;
  white-space: pre-wrap;
  display: none;
}

body:not([data-theme="dark"]) .apple-calling-container {
  background: #f8fafc;
  color: #0f172a;
}
body:not([data-theme="dark"]) .apple-top-nav {
  background: #ffffff;
  border-color: #e2e8f0;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
body:not([data-theme="dark"]) .apple-top-title h2 {
  color: #0f172a;
}
body:not([data-theme="dark"]) .apple-btn-pill {
  background: #f1f5f9;
  border-color: #cbd5e1;
  color: #334155;
}
body:not([data-theme="dark"]) .apple-btn-pill:hover {
  background: #e2e8f0;
  color: #0f172a;
}
body:not([data-theme="dark"]) .apple-card {
  background: #ffffff;
  border-color: #e2e8f0;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
}
body:not([data-theme="dark"]) .apple-client-header {
  border-bottom-color: #e2e8f0;
}
body:not([data-theme="dark"]) .apple-client-name {
  color: #0f172a;
}
body:not([data-theme="dark"]) .apple-client-sub {
  color: #64748b;
}
body:not([data-theme="dark"]) .apple-badge {
  background: #f1f5f9;
  border-color: #e2e8f0;
  color: #475569;
}
body:not([data-theme="dark"]) .apple-badge-cat {
  background: #eff6ff;
  border-color: #bfdbfe;
  color: #1d4ed8;
}
body:not([data-theme="dark"]) .apple-nav-btn {
  background: #f1f5f9;
  border-color: #cbd5e1;
  color: #334155;
}
body:not([data-theme="dark"]) .apple-nav-btn:hover {
  background: #e2e8f0;
  color: #0f172a;
}
body:not([data-theme="dark"]) .apple-phone-card {
  background: #f8fafc;
  border-color: #e2e8f0;
}
body:not([data-theme="dark"]) .apple-phone-card:hover {
  background: #f1f5f9;
  border-color: #93c5fd;
}
body:not([data-theme="dark"]) .apple-phone-card.active-target {
  background: #eff6ff;
  border-color: #2563eb;
  box-shadow: 0 0 12px rgba(37,99,235,0.15);
}
body:not([data-theme="dark"]) .apple-phone-card-label {
  color: #64748b;
}
body:not([data-theme="dark"]) .apple-phone-card-num {
  color: #0f172a;
}
body:not([data-theme="dark"]) .apple-remarks-preview {
  background: #f8fafc;
  border-left-color: #2563eb;
}
body:not([data-theme="dark"]) .apple-remarks-preview-label {
  color: #64748b;
}
body:not([data-theme="dark"]) .apple-remarks-preview-text {
  color: #334155;
}
body:not([data-theme="dark"]) .apple-meta-item {
  background: #f8fafc;
  border-color: #e2e8f0;
}
body:not([data-theme="dark"]) .apple-meta-item-label {
  color: #64748b;
}
body:not([data-theme="dark"]) .apple-meta-item-val {
  color: #0f172a;
}
body:not([data-theme="dark"]) .apple-dialer-screen {
  background: #f8fafc;
  border-color: #cbd5e1;
}
body:not([data-theme="dark"]) .apple-dialer-input {
  color: #0f172a;
}
body:not([data-theme="dark"]) .apple-dialer-backspace {
  color: #64748b;
}
body:not([data-theme="dark"]) .apple-dialer-backspace:hover {
  color: #0f172a;
}
body:not([data-theme="dark"]) .apple-key-btn {
  background: #ffffff;
  border-color: #e2e8f0;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
body:not([data-theme="dark"]) .apple-key-btn:hover {
  background: #f1f5f9;
  border-color: #cbd5e1;
}
body:not([data-theme="dark"]) .apple-key-btn .apple-key-num,
body:not([data-theme="dark"]) .apple-key-btn .apple-key-digit {
  color: #0f172a !important;
}
body:not([data-theme="dark"]) .apple-key-btn .apple-key-letters {
  color: #64748b;
}
body:not([data-theme="dark"]) .apple-slide-panel {
  background: #ffffff;
  border-left-color: #e2e8f0;
  box-shadow: -5px 0 25px rgba(0,0,0,0.1);
}
body:not([data-theme="dark"]) .apple-slide-header {
  background: #f8fafc;
  border-bottom-color: #e2e8f0;
}
body:not([data-theme="dark"]) .apple-slide-header h3 {
  color: #0f172a;
}
body:not([data-theme="dark"]) .apple-slide-footer {
  background: #f8fafc;
  border-top-color: #e2e8f0;
}
body:not([data-theme="dark"]) .apple-form-label {
  color: #475569;
}
body:not([data-theme="dark"]) .apple-form-input,
body:not([data-theme="dark"]) .apple-form-select,
body:not([data-theme="dark"]) .apple-form-textarea {
  background: #ffffff;
  border-color: #cbd5e1;
  color: #0f172a;
}
body:not([data-theme="dark"]) .apple-form-select option {
  background: #ffffff;
  color: #0f172a;
}
body:not([data-theme="dark"]) .apple-log-item,
body:not([data-theme="dark"]) .apple-queue-item {
  background: #ffffff;
  border-color: #e2e8f0;
  box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}
body:not([data-theme="dark"]) .apple-log-item:hover,
body:not([data-theme="dark"]) .apple-queue-item:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
}
body:not([data-theme="dark"]) .apple-log-name,
body:not([data-theme="dark"]) .apple-queue-name {
  color: #0f172a;
}
body:not([data-theme="dark"]) .apple-transcript-box {
  background: #f8fafc;
  border-color: #e2e8f0;
  color: #334155;
}
body:not([data-theme="dark"]) #autodialerWrapupBanner {
  background: #eff6ff !important;
  border-color: #3b82f6 !important;
}
body:not([data-theme="dark"]) #autodialerWrapupBanner > div > div > div:first-child {
  color: #1e3a8a !important;
}
body:not([data-theme="dark"]) #autodialerBannerSub {
  color: #475569 !important;
}
body[data-theme="dark"] .apple-calling-container {
  background: #0f172a;
}
body[data-theme="dark"] .apple-card {
  background: #1e293b;
  border-color: #334155;
}
body[data-theme="dark"] .apple-top-nav {
  background: #1e293b;
  border-color: #334155;
}
body[data-theme="dark"] .apple-key-btn {
  background: #1e293b;
  border-color: #334155;
}
body[data-theme="dark"] .apple-key-btn:hover {
  background: #334155;
}
body[data-theme="dark"] .apple-dialer-screen {
  background: #0f172a;
  border-color: #334155;
}
body[data-theme="dark"] .apple-slide-panel {
  background: #1e293b;
  border-left-color: #334155;
}
body[data-theme="dark"] .apple-slide-header,
body[data-theme="dark"] .apple-slide-footer {
  background: #0f172a;
  border-color: #334155;
}
</style>

<div class="content-wrapper apple-calling-container">

  <div class="apple-top-nav">
    <div class="apple-top-title">
      <h2>Calling Center</h2>
    </div>

    <div class="apple-top-actions">
      <button type="button" class="apple-btn-pill" id="btnToggleAutoDialer" style="border-color:#16a34a; color:#4ade80;">
        <i class="fa fa-bolt"></i> Auto-Dialer (<span id="autoDialerStatusText">Off</span>)
      </button>

      <button type="button" class="apple-btn-pill" id="btnToggleQueueDrawer">
        <i class="fa fa-list-ul"></i> Queue (<span id="queueCountBadge"><?php echo isset($total_queue) ? (int)$total_queue : 0; ?></span>)
      </button>

      <button type="button" class="apple-btn-pill" id="btnToggleLogsDrawer">
        <i class="fa fa-history"></i> Call Logs
      </button>

      <a href="<?php echo base_url(); ?>manager/dashboard" class="apple-btn-pill">
        <i class="fa fa-chevron-left"></i> Exit
      </a>
    </div>
  </div>

  <div id="autodialerWrapupBanner" style="display:none; background:linear-gradient(135deg, #0f172a, #1e293b); border:1px solid #3b82f6; border-radius:12px; padding:12px 18px; margin-bottom:16px; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
    <div style="display:flex; align-items:center; gap:12px;">
      <div style="display:inline-flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:50%; background:rgba(59, 130, 246, 0.15); border:2px solid #3b82f6; color:#60a5fa; font-weight:700; font-size:16px;" id="autodialerTimerDisplay">
        60
      </div>
      <div>
        <div style="font-size:13px; font-weight:600; color:#ffffff;">Auto-Dialer: Post-Call Wrap-up & Notes</div>
        <div style="font-size:11px; color:#94a3b8;" id="autodialerBannerSub">Take notes and select call outcome. Next contact dials automatically when countdown ends.</div>
      </div>
    </div>
    <div style="display:flex; align-items:center; gap:8px;">
      <button type="button" class="apple-btn-secondary" id="btnAutodialExtend" style="padding:6px 12px; font-size:12px;"><i class="fa fa-plus"></i> +30s Extend</button>
      <button type="button" class="apple-btn-secondary" id="btnAutodialPause" style="padding:6px 12px; font-size:12px;"><i class="fa fa-pause"></i> Pause</button>
      <button type="button" class="apple-btn-primary" id="btnAutodialNextNow" style="padding:6px 14px; font-size:12px; background:#16a34a; border-color:#16a34a;"><i class="fa fa-forward"></i> Save & Next Call</button>
      <button type="button" class="apple-btn-secondary" id="btnAutodialStop" style="padding:6px 12px; font-size:12px; color:#f87171;"><i class="fa fa-stop"></i> Stop</button>
    </div>
  </div>

  <div class="apple-stage-grid">

    <div class="apple-card">

      <div class="apple-client-header">
        <div>
          <h1 class="apple-client-name" id="displayCustomerName">
            <?php echo !empty($active_lead['customer']) ? htmlspecialchars($active_lead['customer']) : 'No Contact Selected'; ?>
          </h1>
          <div class="apple-client-sub">
            <span id="displayLeadCode" class="apple-badge">
              #<?php echo !empty($active_lead['lead_id']) ? htmlspecialchars($active_lead['lead_id']) : '—'; ?>
            </span>
            <span id="displayCategoryPill" class="apple-badge apple-badge-cat">
              <?php echo !empty($active_lead['category']) ? htmlspecialchars($active_lead['category']) : 'General Contact'; ?>
            </span>
            <span id="displayLeadStatus" class="apple-badge">
              <?php echo !empty($active_lead['status_name']) ? htmlspecialchars($active_lead['status_name']) : 'Open'; ?>
            </span>
          </div>
        </div>

        <div class="apple-queue-nav">
          <button type="button" class="apple-nav-btn" id="btnPrevLead" title="Previous Contact">
            <i class="fa fa-chevron-left"></i>
          </button>
          <span id="queuePositionText">1 of <?php echo max(1, (int)$total_queue); ?></span>
          <button type="button" class="apple-nav-btn" id="btnNextLead" title="Next Contact">
            <i class="fa fa-chevron-right"></i>
          </button>
        </div>
      </div>

      <div class="apple-dual-phones-row">
        <div class="apple-phone-card active-target" id="cardPrimaryPhone" onclick="selectPhoneTarget('primary')">
          <div class="apple-phone-card-label">
            <span>Primary Contact</span>
            <span id="badgePrimaryActive" class="apple-badge" style="background:#2563eb; color:#fff; border-color:#2563eb;">Selected</span>
          </div>
          <div class="apple-phone-card-num" id="displayPrimaryPhone">
            <?php echo !empty($active_lead['phone_primary']) ? htmlspecialchars($active_lead['phone_primary']) : (!empty($active_lead['contact1']) ? htmlspecialchars($active_lead['contact1']) : 'No Number'); ?>
          </div>
        </div>

        <div class="apple-phone-card" id="cardAltPhone" onclick="selectPhoneTarget('alt')">
          <div class="apple-phone-card-label">
            <span>Secondary / Alt</span>
            <span id="badgeAltActive" class="apple-badge" style="display:none;">Selected</span>
          </div>
          <div class="apple-phone-card-num" id="displayAltPhone">
            <?php echo !empty($active_lead['phone_alt']) ? htmlspecialchars($active_lead['phone_alt']) : '—'; ?>
          </div>
        </div>
      </div>

      <div class="apple-remarks-preview">
        <div class="apple-remarks-preview-label"><i class="fa fa-commenting-o"></i> Previous Call Remarks</div>
        <p class="apple-remarks-preview-text" id="displayPreviousRemarks">
          <?php echo !empty($active_lead['remark']) ? htmlspecialchars($active_lead['remark']) : 'No previous call notes recorded for this contact.'; ?>
        </p>
      </div>

      <div class="apple-meta-strip">
        <div class="apple-meta-item">
          <div class="apple-meta-item-label">POC Name</div>
          <div class="apple-meta-item-val" id="displayPoc">
            <?php echo !empty($active_lead['poc']) ? htmlspecialchars($active_lead['poc']) : '—'; ?>
          </div>
        </div>

        <div class="apple-meta-item">
          <div class="apple-meta-item-label">Authority / Emirate</div>
          <div class="apple-meta-item-val" id="displayAuthority">
            <?php 
              $loc = array();
              if(!empty($active_lead['tl_authority'])) $loc[] = $active_lead['tl_authority'];
              if(!empty($active_lead['tl_emirate'])) $loc[] = $active_lead['tl_emirate'];
              echo !empty($loc) ? htmlspecialchars(implode(', ', $loc)) : '—';
            ?>
          </div>
        </div>

        <div class="apple-meta-item">
          <div class="apple-meta-item-label">Sales Department</div>
          <div class="apple-meta-item-val" id="displayUserDept">
            <?php echo htmlspecialchars($manager_department); ?>
          </div>
        </div>
      </div>

    </div>

    <div class="apple-card apple-dialer-card">

      <div class="apple-dialer-screen">
        <input type="text" id="dialerInput" class="apple-dialer-input" placeholder="Enter Phone..." autocomplete="off" value="<?php echo !empty($active_lead['phone_primary']) ? htmlspecialchars($active_lead['phone_primary']) : (!empty($active_lead['contact1']) ? htmlspecialchars($active_lead['contact1']) : ''); ?>">
        <button type="button" id="btnDialerBackspace" class="apple-dialer-backspace" title="Delete">
          <i class="fa fa-times-circle"></i>
        </button>
      </div>

      <div id="backendOfflineAlert" class="apple-backend-alert">
        <i class="fa fa-exclamation-triangle" style="margin-right:4px;"></i>
        <span id="backendOfflineMsg">Calling backend is not configured (API key / PBX missing).</span>
        <div style="margin-top:6px;">
          <a id="btnFallbackTel" href="tel:" class="apple-log-action-btn btn-dial" style="font-weight:600; padding:4px 10px;">
            <i class="fa fa-phone"></i> Launch Phone Dialer (tel:)
          </a>
        </div>
      </div>

      <div class="apple-keypad-grid">
        <div class="apple-key-btn" data-key="1"><div class="apple-key-digit">1</div><div class="apple-key-letters"></div></div>
        <div class="apple-key-btn" data-key="2"><div class="apple-key-digit">2</div><div class="apple-key-letters">ABC</div></div>
        <div class="apple-key-btn" data-key="3"><div class="apple-key-digit">3</div><div class="apple-key-letters">DEF</div></div>
        <div class="apple-key-btn" data-key="4"><div class="apple-key-digit">4</div><div class="apple-key-letters">GHI</div></div>
        <div class="apple-key-btn" data-key="5"><div class="apple-key-digit">5</div><div class="apple-key-letters">JKL</div></div>
        <div class="apple-key-btn" data-key="6"><div class="apple-key-digit">6</div><div class="apple-key-letters">MNO</div></div>
        <div class="apple-key-btn" data-key="7"><div class="apple-key-digit">7</div><div class="apple-key-letters">PQRS</div></div>
        <div class="apple-key-btn" data-key="8"><div class="apple-key-digit">8</div><div class="apple-key-letters">TUV</div></div>
        <div class="apple-key-btn" data-key="9"><div class="apple-key-digit">9</div><div class="apple-key-letters">WXYZ</div></div>
        <div class="apple-key-btn" data-key="*"><div class="apple-key-digit">*</div><div class="apple-key-letters"></div></div>
        <div class="apple-key-btn" data-key="0"><div class="apple-key-digit">0</div><div class="apple-key-letters">+</div></div>
        <div class="apple-key-btn" data-key="#"><div class="apple-key-digit">#</div><div class="apple-key-letters"></div></div>
      </div>

      <div class="apple-call-action-row">
        <button type="button" id="btnToggleCall" class="apple-call-btn btn-start" title="Call">
          <i class="fa fa-phone"></i>
        </button>
        <div id="liveCallSubtitle" style="font-size:12px; color:#94a3b8; height:18px;">Tap to place call</div>
      </div>

    </div>

  </div>

  <div id="drawerBackdrop" class="apple-drawer-backdrop"></div>

  <div id="followupSlidePanel" class="apple-slide-panel">
    <div class="apple-slide-header">
      <div>
        <h3>Call Follow-up & Outcome</h3>
        <span id="followupCallDurationBadge" class="apple-badge" style="margin-top:4px; display:inline-block;">Duration: 00:00</span>
      </div>
      <button type="button" class="apple-close-btn" id="btnCloseFollowupPanel">
        <i class="fa fa-times"></i>
      </button>
    </div>

    <form id="formCallFollowup" onsubmit="return false;" style="display:flex; flex-direction:column; height:calc(100% - 65px);">
      <input type="hidden" name="lead_id" id="hiddenLeadId" value="<?php echo !empty($active_lead['id']) ? (int)$active_lead['id'] : 0; ?>">
      <input type="hidden" name="call_log_id" id="hiddenCallLogId" value="0">
      <input type="hidden" name="customer_name" id="hiddenCustomerName" value="<?php echo !empty($active_lead['customer']) ? htmlspecialchars($active_lead['customer']) : ''; ?>">
      <input type="hidden" name="phone" id="hiddenPhone" value="<?php echo !empty($active_lead['phone_primary']) ? htmlspecialchars($active_lead['phone_primary']) : ''; ?>">
      <input type="hidden" name="call_duration" id="hiddenDuration" value="0">
      <input type="hidden" name="call_disposition" id="hiddenDisposition" value="Connected">

      <div class="apple-slide-body">

        <div class="apple-form-group">
          <label class="apple-form-label">Call Outcome / Status</label>
          <select name="status" id="followupStatusSelect" class="apple-form-select" required>
            <?php if(!empty($statuses)){
              foreach($statuses as $st){ ?>
                <option value="<?php echo $st['id']; ?>" <?php echo (!empty($active_lead['status']) && $active_lead['status'] == $st['id']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($st['status']); ?>
                </option>
            <?php } } ?>
          </select>
        </div>

        <div id="followupScheduleBlock" class="apple-form-group" style="display:none; background:rgba(0,0,0,0.25); border:1px solid rgba(255,255,255,0.06); padding:12px; border-radius:12px;">
          <label class="apple-form-label"><i class="fa fa-calendar"></i> Next Follow-up Schedule</label>
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
            <div>
              <input type="text" name="next_followup" id="nextFollowupDate" class="apple-form-input datepicker" placeholder="YYYY-MM-DD" autocomplete="off">
            </div>
            <div>
              <div class="input-group bootstrap-timepicker timepicker" style="width:100%;">
                <input type="text" name="next_followup_time" id="nextFollowupTime" class="form-control input-small timepicker apple-form-input" placeholder="11:00 AM" value="11:00 AM">
                <span class="input-group-addon" style="background:#1e293b; border-color:rgba(255,255,255,0.12); color:#94a3b8; cursor:pointer;"><i class="glyphicon glyphicon-time"></i></span>
              </div>
            </div>
          </div>
        </div>

        <div class="apple-form-group">
          <label class="apple-form-label">Discussion Remarks</label>
          <textarea name="remarks" id="followupRemarks" class="apple-form-textarea" rows="3" placeholder="Key points discussed during the call..." required></textarea>
        </div>

        <div class="apple-form-group">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <label class="apple-form-label" style="margin:0;">Vertical Information</label>
            <div class="apple-pills-row">
              <span class="apple-radio-pill active" id="tabVerticalAccounting" onclick="switchVerticalTab('accounting')">Accounting & Tax</span>
              <span class="apple-radio-pill" id="tabVerticalTelecom" onclick="switchVerticalTab('telecom')">Telecom</span>
              <span class="apple-radio-pill" id="tabVerticalGeneral" onclick="switchVerticalTab('general')">General</span>
            </div>
          </div>

          <div id="verticalAccountingSection" style="background:rgba(0,0,0,0.25); border:1px solid rgba(255,255,255,0.06); padding:12px; border-radius:12px;">
            <div style="margin-bottom:10px;">
              <label class="apple-form-label" style="font-size:10px;">VAT Registration</label>
              <div class="apple-pills-row" style="margin-bottom:6px;">
                <span class="apple-radio-pill vat-pill" data-val="Registered">Registered</span>
                <span class="apple-radio-pill vat-pill" data-val="Not Registered">Not Registered</span>
                <span class="apple-radio-pill vat-pill" data-val="Pending">Pending</span>
                <span class="apple-radio-pill vat-pill" data-val="Exempt">Exempt</span>
              </div>
              <input type="hidden" name="vat_status" id="vatStatusVal" value="">
              <input type="text" name="vat_trn" id="vatTrnInput" class="apple-form-input" placeholder="VAT TRN Number (if registered)...">
            </div>

            <div>
              <label class="apple-form-label" style="font-size:10px;">Corporate Tax (CT)</label>
              <div class="apple-pills-row" style="margin-bottom:6px;">
                <span class="apple-radio-pill ct-pill" data-val="Registered">Registered</span>
                <span class="apple-radio-pill ct-pill" data-val="Not Registered">Not Registered</span>
                <span class="apple-radio-pill ct-pill" data-val="Pending">Pending</span>
                <span class="apple-radio-pill ct-pill" data-val="Exempt">Exempt</span>
              </div>
              <input type="hidden" name="corporate_tax_status" id="corporateTaxStatusVal" value="">
              <input type="text" name="ct_trn" id="ctTrnInput" class="apple-form-input" placeholder="Corporate Tax TRN / Ref (if registered)...">
            </div>
          </div>

          <div id="verticalTelecomSection" style="display:none; background:rgba(0,0,0,0.25); border:1px solid rgba(255,255,255,0.06); padding:12px; border-radius:12px;">
            <div style="margin-bottom:10px;">
              <label class="apple-form-label" style="font-size:10px;">Current Telecom Provider</label>
              <select name="business_category" id="telecomProviderSelect" class="apple-form-select">
                <option value="">-- Select Provider --</option>
                <option value="Etisalat">Etisalat by e&</option>
                <option value="du">du Telecom</option>
                <option value="Virgin">Virgin Mobile</option>
                <option value="Other">Other</option>
              </select>
            </div>
            <div>
              <label class="apple-form-label" style="font-size:10px;">Lines / Monthly Spend</label>
              <input type="text" name="industry_category" id="telecomLinesInput" class="apple-form-input" placeholder="e.g. 15 Lines | AED 2,500/mo">
            </div>
          </div>

          <div id="verticalGeneralSection" style="display:none; background:rgba(0,0,0,0.25); border:1px solid rgba(255,255,255,0.06); padding:12px; border-radius:12px;">
            <div>
              <label class="apple-form-label" style="font-size:10px;">Business Category</label>
              <input type="text" name="general_business_cat" id="generalBusinessCatInput" class="apple-form-input" placeholder="e.g. Real Estate, Retail, Consulting...">
            </div>
          </div>
        </div>

      </div>

      <div class="apple-slide-footer">
        <button type="button" class="apple-btn-secondary" id="btnSaveOnly">
          <i class="fa fa-check"></i> Save Details
        </button>
        <button type="button" class="apple-btn-primary" id="btnSaveAndNextContact">
          <span>Save & Next Contact</span> <i class="fa fa-arrow-right"></i>
        </button>
      </div>
    </form>
  </div>

  <div id="queueSlidePanel" class="apple-slide-panel">
    <div class="apple-slide-header">
      <h3>Calling Queue (<span id="panelQueueCount"><?php echo count($queue); ?></span>)</h3>
      <button type="button" class="apple-close-btn" id="btnCloseQueueDrawer">
        <i class="fa fa-times"></i>
      </button>
    </div>
    <div class="apple-slide-body">
      <div style="margin-bottom:14px;">
        <input type="text" id="queueSearchInput" class="apple-form-input" placeholder="Search contact, phone or company...">
      </div>
      <div id="queueListContainer">
        <?php if(!empty($queue)){
          foreach($queue as $idx => $q){ ?>
            <div class="apple-queue-item <?php echo $idx === 0 ? 'active' : ''; ?>" data-lead-idx="<?php echo $idx; ?>">
              <div>
                <div style="font-size:13px; font-weight:600; color:#fff;"><?php echo htmlspecialchars($q['customer']); ?></div>
                <div style="font-size:11px; color:#94a3b8;"><?php echo !empty($q['phone_primary']) ? htmlspecialchars($q['phone_primary']) : htmlspecialchars($q['contact1']); ?></div>
              </div>
              <div>
                <span class="apple-badge"><?php echo !empty($q['status_name']) ? htmlspecialchars($q['status_name']) : 'Open'; ?></span>
              </div>
            </div>
        <?php } } else { ?>
          <div style="padding:20px; text-align:center; color:#64748b;">No contacts currently in queue</div>
        <?php } ?>
      </div>
    </div>
  </div>

  <div id="logsSlidePanel" class="apple-slide-panel">
    <div class="apple-slide-header">
      <h3>Call Logs</h3>
      <button type="button" class="apple-close-btn" id="btnCloseLogsDrawer">
        <i class="fa fa-times"></i>
      </button>
    </div>
    <div class="apple-slide-body">
      <div id="logsListContainer">
        <?php if(!empty($recent_logs)){
          foreach($recent_logs as $log){ 
            $raw_phone = !empty($log['phone_number']) ? $log['phone_number'] : '';
            $clean_phone = preg_replace('/[^\d\+]/', '', $raw_phone);
          ?>
            <div class="apple-phone-log-item">
              <div class="apple-phone-log-top">
                <span class="apple-phone-log-name"><?php echo htmlspecialchars($log['customer_name']); ?></span>
                <span class="apple-badge"><?php echo (int)$log['duration'] > 0 ? (int)$log['duration'] . 's' : '0s'; ?></span>
              </div>
              <div class="apple-phone-log-meta">
                <span><i class="fa fa-phone" style="font-size:10px;"></i> <?php echo htmlspecialchars($raw_phone); ?></span>
                <span>•</span>
                <span><?php echo htmlspecialchars($log['disposition']); ?></span>
                <span>•</span>
                <span><?php echo date('M d, H:i', strtotime($log['created_at'])); ?></span>
              </div>
              <div class="apple-phone-log-actions">
                <button type="button" class="apple-log-action-btn btn-dial" onclick="dialFromLog('<?php echo htmlspecialchars(addslashes($clean_phone)); ?>', '<?php echo htmlspecialchars(addslashes($log['customer_name'])); ?>', <?php echo !empty($log['lead_id']) ? (int)$log['lead_id'] : 0; ?>)">
                  <i class="fa fa-phone"></i> Call
                </button>
                <button type="button" class="apple-log-action-btn" onclick="copyNumberToClipboard('<?php echo htmlspecialchars(addslashes($clean_phone)); ?>')">
                  <i class="fa fa-copy"></i> Copy
                </button>
                <?php if(!empty($log['lead_id'])){ ?>
                  <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($log['lead_id']); ?>" target="_blank" class="apple-log-action-btn">
                    <i class="fa fa-user"></i> View Lead
                  </a>
                <?php } else { ?>
                  <button type="button" class="apple-log-action-btn" onclick="addUnallottedToDialer('<?php echo htmlspecialchars(addslashes($clean_phone)); ?>', '<?php echo htmlspecialchars(addslashes($log['customer_name'])); ?>')">
                    <i class="fa fa-user-plus"></i> Load
                  </button>
                <?php } ?>
              </div>
            </div>
        <?php } } else { ?>
          <div style="padding:20px; text-align:center; color:#64748b;">No past completed calls recorded</div>
        <?php } ?>
      </div>
    </div>
  </div>

  <div id="appleToast" class="apple-toast">
    <i class="fa fa-info-circle" style="color:#38bdf8;"></i>
    <span id="appleToastText">Notice message</span>
  </div>

</div>

<script>
var callingQueue = <?php echo json_encode(!empty($queue) ? $queue : array()); ?>;
var isBackendConfigured = <?php echo !empty($is_backend_configured) ? 'true' : 'false'; ?>;
var currentIndex = 0;
var callState = 'idle';
var callTimerInterval = null;
var callConnectedSeconds = 0;
var activeCallLogId = 0;
var audioCtx = null;
var activePhoneType = 'primary';
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

function selectPhoneTarget(type) {
  activePhoneType = type;
  var lead = callingQueue[currentIndex] || {};
  var num = '';

  if (type === 'alt') {
    num = lead.phone_alt || '';
    $('#cardAltPhone').addClass('active-target');
    $('#cardPrimaryPhone').removeClass('active-target');
    $('#badgeAltActive').show().css({'background':'#2563eb', 'color':'#fff', 'border-color':'#2563eb'});
    $('#badgePrimaryActive').hide();
  } else {
    num = lead.phone_primary || lead.contact1 || '';
    $('#cardPrimaryPhone').addClass('active-target');
    $('#cardAltPhone').removeClass('active-target');
    $('#badgePrimaryActive').show().css({'background':'#2563eb', 'color':'#fff', 'border-color':'#2563eb'});
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

  document.getElementById('queuePositionText').textContent = (currentIndex + 1) + ' of ' + callingQueue.length;

  document.getElementById('hiddenLeadId').value = lead.id || 0;
  document.getElementById('hiddenCustomerName').value = lead.customer || '';

  $('.apple-queue-item').removeClass('active');
  $('.apple-queue-item[data-lead-idx="' + currentIndex + '"]').addClass('active');

  var catLower = (lead.category || lead.p_cat || '').toLowerCase();
  if (catLower.indexOf('telecom') !== -1) {
    switchVerticalTab('telecom');
  } else if (catLower.indexOf('account') !== -1 || catLower.indexOf('tax') !== -1 || catLower.indexOf('vat') !== -1) {
    switchVerticalTab('accounting');
  }
}

function switchVerticalTab(tabName) {
  $('.apple-radio-pill#tabVerticalAccounting, .apple-radio-pill#tabVerticalTelecom, .apple-radio-pill#tabVerticalGeneral').removeClass('active');
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

function openFollowupPanel() {
  document.getElementById('drawerBackdrop').classList.add('show');
  document.getElementById('followupSlidePanel').classList.add('show');
  document.getElementById('followupCallDurationBadge').textContent = 'Duration: ' + formatTimer(callConnectedSeconds);
  document.getElementById('hiddenDuration').value = callConnectedSeconds;
  checkFollowupScheduleRequirement();
}

function closeFollowupPanel() {
  document.getElementById('drawerBackdrop').classList.remove('show');
  document.getElementById('followupSlidePanel').classList.remove('show');
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
  document.getElementById('displayPrimaryPhone').textContent = phone;
  document.getElementById('hiddenPhone').value = phone;
  document.getElementById('hiddenCustomerName').value = customerName || 'Direct Dial';
  document.getElementById('hiddenLeadId').value = leadId || 0;

  startCrmCall();
}

function addUnallottedToDialer(phone, customerName) {
  $('#logsSlidePanel').removeClass('show');
  $('#drawerBackdrop').removeClass('show');

  document.getElementById('dialerInput').value = phone;
  document.getElementById('displayPrimaryPhone').textContent = phone;
  document.getElementById('displayCustomerName').textContent = customerName || 'Direct Dial';
  document.getElementById('hiddenPhone').value = phone;
  document.getElementById('hiddenCustomerName').value = customerName || 'Direct Dial';
  document.getElementById('hiddenLeadId').value = 0;
  showAppleToast('Loaded contact into dialer');
}

function startCrmCall() {
  var phone = document.getElementById('dialerInput').value.trim();
  if (!phone) {
    showAppleToast('Please enter or select a phone number');
    return;
  }

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
    btn.className = 'apple-call-btn btn-end';
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
  btn.className = 'apple-call-btn btn-start';
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
    startWrapupCountdown(60);
  }

  if (activeCallLogId > 0) {
    runAiAnalysis(activeCallLogId, true);
  }

  setTimeout(function() {
    openFollowupPanel();
  }, 250);
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

var autoDialerActive = false;
var autoDialerCountdown = null;
var autoDialerSeconds = 60;
var autoDialerPaused = false;

function toggleAutoDialer() {
  autoDialerActive = !autoDialerActive;
  var btn = $('#btnToggleAutoDialer');
  var stText = $('#autoDialerStatusText');
  if (autoDialerActive) {
    btn.css({ 'border-color': '#22c55e', 'background': 'rgba(34, 197, 94, 0.2)', 'color': '#ffffff' });
    stText.text('Active');
    showAppleToast('Auto-Dialer activated: 60s wrap-up between calls');
    $.ajax({
      type: 'POST',
      url: '<?php echo base_url(); ?>manager/calls/ajax_start_autodial_session',
      data: { filter_status: $('#selectedStatusInput').val() || 'all' },
      dataType: 'json'
    });
  } else {
    btn.css({ 'border-color': '#16a34a', 'background': '', 'color': '#4ade80' });
    stText.text('Off');
    stopWrapupCountdown();
    $('#autodialerWrapupBanner').slideUp(180);
    showAppleToast('Auto-Dialer stopped');
    $.ajax({
      type: 'POST',
      url: '<?php echo base_url(); ?>manager/calls/ajax_autodial_stop',
      dataType: 'json'
    });
  }
}

function startWrapupCountdown(seconds) {
  stopWrapupCountdown();
  autoDialerSeconds = seconds || 60;
  autoDialerPaused = false;
  $('#autodialerTimerDisplay').text(autoDialerSeconds);
  $('#autodialerBannerSub').text('Take notes and select call outcome. Next contact dials in ' + autoDialerSeconds + 's.');
  $('#autodialerWrapupBanner').slideDown(180);

  autoDialerCountdown = setInterval(function() {
    if (autoDialerPaused) return;
    autoDialerSeconds--;
    $('#autodialerTimerDisplay').text(autoDialerSeconds);
    $('#autodialerBannerSub').text('Take notes and select call outcome. Next contact dials in ' + autoDialerSeconds + 's.');

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

  var remarks = $('#followupRemarks').val().trim();
  if (remarks) {
    saveCallDetails(function() {
      dispatchNextAutodialLead();
    });
  } else {
    closeFollowupPanel();
    dispatchNextAutodialLead();
  }
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
      showAppleToast('Auto-Dialing: ' + (nextLead.customer || 'Next Contact'));
      setTimeout(function() {
        startCrmCall();
      }, 600);
    } else {
      showAppleToast('Auto-Dialer queue completed');
      toggleAutoDialer();
    }
  }).fail(function() {
    showAppleToast('Error fetching next auto-dialer lead');
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
        container.innerHTML = '<div style="padding:20px; text-align:center; color:#64748b;">No past completed calls recorded</div>';
        return;
      }
      var html = '';
      resp.logs.forEach(function(log) {
        var rawPhone = log.phone_number || '';
        var cleanPhone = rawPhone.replace(/[^\d\+]/g, '');
        var dur = parseInt(log.duration || 0, 10);
        var sentClass = 'apple-ai-badge-neu';
        if (log.ai_sentiment === 'Positive') sentClass = 'apple-ai-badge-pos';
        if (log.ai_sentiment === 'Negative') sentClass = 'apple-ai-badge-neg';

        html += '<div class="apple-phone-log-item">';
        html += '<div class="apple-phone-log-top">';
        html += '<span class="apple-phone-log-name">' + escapeHtml(log.customer_name || 'Direct Call') + '</span>';
        html += '<div style="display:flex; align-items:center; gap:6px;">';
        if (log.ai_sentiment) {
          html += '<span class="apple-ai-badge ' + sentClass + '">' + escapeHtml(log.ai_sentiment) + '</span>';
        }
        if (log.ai_status) {
          html += '<span class="apple-badge">' + escapeHtml(log.ai_status) + '</span>';
        }
        html += '<span class="apple-badge">' + dur + 's</span>';
        html += '</div>';
        html += '</div>';

        html += '<div class="apple-phone-log-meta">';
        html += '<span><i class="fa fa-phone" style="font-size:10px;"></i> ' + escapeHtml(rawPhone) + '</span>';
        html += '<span>•</span>';
        html += '<span>' + escapeHtml(log.disposition || 'Completed') + '</span>';
        html += '<span>•</span>';
        html += '<span>' + escapeHtml(log.created_at || '') + '</span>';
        html += '</div>';

        if (log.ai_summary) {
          html += '<div class="apple-log-ai-summary"><strong>AI Summary:</strong> ' + escapeHtml(log.ai_summary) + '</div>';
        }

        if (log.recording_file || log.recording_url) {
          var audioSrc = log.recording_file ? ('<?php echo base_url(); ?>' + log.recording_file) : log.recording_url;
          html += '<div style="margin-top:8px;"><audio controls style="width:100%; height:30px; border-radius:6px;" src="' + audioSrc + '"></audio></div>';
        }

        html += '<div class="apple-phone-log-actions" style="margin-top:10px; flex-wrap:wrap;">';
        html += '<button type="button" class="apple-log-action-btn btn-dial" onclick="dialFromLog(\'' + cleanPhone + '\', \'' + escapeHtml(log.customer_name || '').replace(/'/g, "\\'") + '\', ' + (log.lead_id || 0) + ')"><i class="fa fa-phone"></i> Call</button>';
        html += '<button type="button" class="apple-log-action-btn" onclick="copyNumberToClipboard(\'' + cleanPhone + '\')"><i class="fa fa-copy"></i> Copy</button>';
        if (log.lead_id) {
          html += '<a href="<?php echo base_url(); ?>manager/leads/view/' + btoa(log.lead_id) + '" target="_blank" class="apple-log-action-btn"><i class="fa fa-user"></i> View Lead</a>';
        } else {
          html += '<button type="button" class="apple-log-action-btn" onclick="addUnallottedToDialer(\'' + cleanPhone + '\', \'' + escapeHtml(log.customer_name || '').replace(/'/g, "\\'") + '\')"><i class="fa fa-user-plus"></i> Load</button>';
        }

        if (log.transcript) {
          html += '<button type="button" class="apple-log-action-btn" onclick="$(this).parent().next(\'.apple-transcript-box\').slideToggle(140);"><i class="fa fa-file-text-o"></i> Transcript</button>';
        }

        html += '<button type="button" class="apple-log-action-btn" onclick="runAiAnalysis(' + log.id + ', false)"><i class="fa fa-magic"></i> AI Analysis</button>';
        html += '</div>';

        if (log.transcript) {
          html += '<div class="apple-transcript-box">' + escapeHtml(log.transcript) + '</div>';
        }

        html += '</div>';
      });
      container.innerHTML = html;
    }
  });
}

function saveCallDetails(callbackAfterSave) {
  var form = $('#formCallFollowup');
  var remarks = $('#followupRemarks').val().trim();
  if (!remarks) {
    showAppleToast('Please enter call remarks');
    $('#followupRemarks').focus();
    return;
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
      closeFollowupPanel();
      refreshCallLogs();

      if (typeof callbackAfterSave === 'function') {
        callbackAfterSave(resp);
      }
    } else {
      showAppleToast('Error saving call log');
    }
  }).fail(function() {
    btnNext.prop('disabled', false).html('<span>Save & Next Contact</span> <i class="fa fa-arrow-right"></i>');
    showAppleToast('Network error while saving');
  });
}

$(document).ready(function() {

  $('.datepicker').datepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
    todayHighlight: true
  });

  $('.apple-key-btn').on('click', function() {
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
        showAppleToast('Loaded next contact: ' + (callingQueue[currentIndex].customer || ''));
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

  $('#btnToggleAutoDialer').on('click', function() {
    toggleAutoDialer();
  });

  $('#btnAutodialExtend').on('click', function() {
    autoDialerSeconds += 30;
    $('#autodialerTimerDisplay').text(autoDialerSeconds);
    $('#autodialerBannerSub').text('Take notes. Extended by +30s (remaining: ' + autoDialerSeconds + 's).');
    showAppleToast('Extended wrap-up cooldown by 30 seconds');
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
      $('#autodialerBannerSub').text('Cooldown paused. Take your time to write notes.');
      showAppleToast('Auto-Dialer wrap-up paused');
      $.ajax({
        type: 'POST',
        url: '<?php echo base_url(); ?>manager/calls/ajax_autodial_pause',
        dataType: 'json'
      });
    } else {
      $(this).html('<i class="fa fa-pause"></i> Pause');
      $('#autodialerBannerSub').text('Cooldown resumed. Dials next contact when timer finishes.');
      showAppleToast('Auto-Dialer wrap-up resumed');
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
    toggleAutoDialer();
  });

  $('#btnCloseFollowupPanel, #drawerBackdrop').on('click', function() {
    closeFollowupPanel();
    $('#queueSlidePanel').removeClass('show');
    $('#logsSlidePanel').removeClass('show');
    $('#drawerBackdrop').removeClass('show');
  });

  $('#btnToggleQueueDrawer').on('click', function() {
    $('#drawerBackdrop').addClass('show');
    $('#queueSlidePanel').addClass('show');
  });

  $('#btnCloseQueueDrawer').on('click', function() {
    $('#queueSlidePanel').removeClass('show');
    $('#drawerBackdrop').removeClass('show');
  });

  $('#btnToggleLogsDrawer').on('click', function() {
    refreshCallLogs();
    $('#drawerBackdrop').addClass('show');
    $('#logsSlidePanel').addClass('show');
  });

  $('#btnCloseLogsDrawer').on('click', function() {
    $('#logsSlidePanel').removeClass('show');
    $('#drawerBackdrop').removeClass('show');
  });

  $(document).on('click', '.apple-queue-item', function() {
    var idx = parseInt($(this).data('lead-idx'), 10);
    loadContactAt(idx);
    $('#queueSlidePanel').removeClass('show');
    $('#drawerBackdrop').removeClass('show');
  });

  $('#queueSearchInput').on('input', function() {
    var q = $(this).val().toLowerCase().trim();
    $('.apple-queue-item').each(function() {
      var txt = $(this).text().toLowerCase();
      if (!q || txt.indexOf(q) !== -1) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  });

  $('#nextFollowupTime').timepicker({ showInputs: false });

  if (callingQueue.length > 0) {
    loadContactAt(0);
  }
});
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
