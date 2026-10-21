<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>

<style>
.calling-dashboard {
    padding: 10px 0 35px 0;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}
.calling-topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 15px;
}
.calling-stat-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    margin-bottom: 15px;
}
.calling-stat-icon {
    width: 36px;
    height: 36px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}
.calling-stat-icon.blue { background: #eff6ff; color: #2563eb; }
.calling-stat-icon.purple { background: #faf5ff; color: #9333ea; }
.calling-stat-icon.green { background: #f0fdf4; color: #16a34a; }
.calling-stat-num {
    font-size: 18px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.1;
}
.calling-stat-txt {
    font-size: 11px;
    color: #64748b;
    text-transform: uppercase;
    font-weight: 600;
}
.c-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    margin-bottom: 20px;
}
.c-card-header {
    padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
    background: #ffffff;
    border-radius: 8px 8px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.c-card-title {
    font-size: 14.5px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.c-card-body {
    padding: 16px;
}
.queue-scroll {
    max-height: 520px;
    overflow-y: auto;
}
.lead-item-row {
    padding: 11px 13px;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    transition: background 0.15s;
    border-left: 3px solid transparent;
}
.lead-item-row:hover {
    background: #f8fafc;
}
.lead-item-row.active {
    background: #eff6ff;
    border-left-color: #2563eb;
}
.lead-title-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13.5px;
    font-weight: 600;
    color: #0f172a;
}
.lead-meta-row {
    font-size: 12px;
    color: #64748b;
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.status-pill-badge {
    font-size: 10.5px;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 4px;
    background: #e2e8f0;
    color: #334155;
}
.status-follow { background: #fef3c7; color: #92400e; }
.status-prospect { background: #e0e7ff; color: #3730a3; }
.status-lead { background: #dcfce7; color: #166534; }
.number-selector-box {
    display: flex;
    gap: 8px;
    margin-bottom: 12px;
    flex-wrap: wrap;
}
.btn-num-choice {
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 6px 14px;
    font-size: 13px;
    font-weight: 600;
    color: #334155;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.15s;
}
.btn-num-choice:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.btn-num-choice.active {
    background: #2563eb;
    border-color: #1d4ed8;
    color: #ffffff;
}
.dialer-input-wrap {
    position: relative;
    margin-bottom: 12px;
}
.dialer-phone-input {
    width: 100%;
    height: 48px;
    font-size: 22px;
    font-weight: 700;
    letter-spacing: 1px;
    color: #0f172a;
    padding: 8px 105px 8px 14px;
    background: #ffffff;
    border: 2px solid #cbd5e1;
    border-radius: 8px;
    outline: none;
    transition: border-color 0.15s;
    font-family: inherit;
}
.dialer-phone-input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
}
.dialer-input-actions {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    gap: 4px;
}
.dialer-action-btn {
    border: none;
    background: #f1f5f9;
    color: #475569;
    width: 34px;
    height: 34px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.15s;
}
.dialer-action-btn:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.dialpad-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    max-width: 270px;
    margin: 10px auto 16px auto;
}
.keypad-btn {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 0;
    text-align: center;
    cursor: pointer;
    transition: all 0.12s;
    user-select: none;
}
.keypad-btn:hover {
    background: #e2e8f0;
    border-color: #cbd5e1;
}
.keypad-btn:active {
    background: #cbd5e1;
    transform: scale(0.96);
}
.keypad-btn-num {
    font-size: 19px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1;
}
.keypad-btn-sub {
    font-size: 9px;
    color: #94a3b8;
    text-transform: uppercase;
    font-weight: 600;
    margin-top: 2px;
}
.call-btn-green {
    background: #16a34a;
    color: #ffffff;
    border: none;
    padding: 12px 28px;
    font-size: 15px;
    font-weight: 700;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    transition: background 0.15s;
    box-shadow: 0 2px 6px rgba(22,163,74,0.3);
}
.call-btn-green:hover {
    background: #15803d;
    color: #ffffff;
}
.call-btn-red {
    background: #dc2626;
    color: #ffffff;
    border: none;
    padding: 12px 28px;
    font-size: 15px;
    font-weight: 700;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    transition: background 0.15s;
    box-shadow: 0 2px 6px rgba(220,38,38,0.3);
}
.call-btn-red:hover {
    background: #b91c1c;
    color: #ffffff;
}
.call-timer-badge {
    background: #0f172a;
    color: #38bdf8;
    font-family: 'Consolas', 'Courier New', monospace;
    font-size: 22px;
    font-weight: 700;
    padding: 4px 14px;
    border-radius: 6px;
    letter-spacing: 1.5px;
    display: inline-block;
}
.live-pulse {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 6px;
}
.live-pulse.online { background: #16a34a; box-shadow: 0 0 6px rgba(22,163,74,0.6); }
.live-pulse.offline { background: #94a3b8; }
.live-pulse.active-call { background: #16a34a; animation: pulseCall 1.2s infinite; }
@keyframes pulseCall {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.3); opacity: 0.6; }
    100% { transform: scale(1); opacity: 1; }
}
.logs-table th {
    background: #f8fafc;
    color: #475569;
    font-size: 11.5px;
    text-transform: uppercase;
    font-weight: 600;
    padding: 8px 10px;
    border-bottom: 1px solid #e2e8f0;
}
.logs-table td {
    font-size: 12.5px;
    color: #1e293b;
    padding: 8px 10px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}
.mobile-view-nav {
    display: none;
    margin-bottom: 14px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 4px;
}
.mobile-nav-btn {
    flex: 1;
    border: none;
    background: transparent;
    padding: 8px 6px;
    font-size: 12.5px;
    font-weight: 600;
    color: #475569;
    border-radius: 6px;
    text-align: center;
    cursor: pointer;
}
.mobile-nav-btn.active {
    background: #2563eb;
    color: #ffffff;
}
@media (max-width: 991px) {
    .mobile-view-nav {
        display: flex;
    }
    .col-calling-left, .col-calling-right {
        width: 100% !important;
    }
    .dialer-phone-input {
        font-size: 20px;
        height: 46px;
    }
    .dialpad-container {
        max-width: 100%;
        gap: 10px;
    }
    .keypad-btn {
        padding: 13px 0;
    }
    .keypad-btn-num {
        font-size: 22px;
    }
    .call-btn-green, .call-btn-red {
        width: 100%;
        font-size: 16px;
        padding: 14px 20px;
    }
    .calling-stat-box {
        margin-bottom: 8px;
    }
}
@media (min-width: 992px) {
    .col-calling-left {
        width: 40%;
        float: left;
        padding-right: 12px;
    }
    .col-calling-right {
        width: 60%;
        float: left;
        padding-left: 12px;
    }
    .mobile-pane-hidden-desktop {
        display: block !important;
    }
}
</style>

<div class="content-wrapper px-4">
    <section class="content-header" style="padding-bottom: 0;">
        <div class="calling-topbar">
            <div>
                <h1 style="font-size: 22px; font-weight: 700; color: #0f172a; margin: 0;">
                    <i class="fa fa-phone" style="color: #2563eb; margin-right: 6px;"></i> Operations Calling & Dialer
                </h1>
                <p style="margin: 2px 0 0 0; color: #64748b; font-size: 13px;">
                    Database Calling & Manual Dial Pad &bull; Integrated with 3CX PBX
                </p>
            </div>
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <span class="calling-stat-box" style="margin: 0; padding: 6px 12px;">
                    <span class="live-pulse <?php echo (!empty($threecx_config['pbx_fqdn']) && !empty($threecx_config['client_id'])) ? 'online' : 'offline'; ?>"></span>
                    <strong style="color: #0f172a; font-size: 12px;">3CX:</strong>
                    <span id="topPbxFqdn" style="color: #64748b; font-size: 12px; margin-left: 3px;">
                        <?php echo (!empty($threecx_config['pbx_fqdn'])) ? htmlspecialchars($threecx_config['pbx_fqdn']) : 'Configure'; ?>
                    </span>
                </span>
                <button type="button" class="btn btn-default btn-sm" id="btnOpenSettings" style="font-weight: 600;">
                    <i class="fa fa-cog text-primary"></i> 3CX Settings
                </button>
            </div>
        </div>
    </section>

    <section class="content calling-dashboard">
        <div class="row">
            <div class="col-sm-4 col-xs-12">
                <div class="calling-stat-box">
                    <div class="calling-stat-icon blue"><i class="fa fa-database"></i></div>
                    <div>
                        <div class="calling-stat-num" id="statTotalLeads"><?php echo number_format($total_my_leads); ?></div>
                        <div class="calling-stat-txt">My Database Leads</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-xs-12">
                <div class="calling-stat-box">
                    <div class="calling-stat-icon purple"><i class="fa fa-phone"></i></div>
                    <div>
                        <div class="calling-stat-num" id="statCallsToday"><?php echo number_format($calls_today); ?></div>
                        <div class="calling-stat-txt">Calls Made Today</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-xs-12">
                <div class="calling-stat-box">
                    <div class="calling-stat-icon green"><i class="fa fa-check-circle"></i></div>
                    <div>
                        <div class="calling-stat-num" id="statCallsConnected"><?php echo number_format($calls_connected); ?></div>
                        <div class="calling-stat-txt">Connected Calls</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mobile-view-nav">
            <button type="button" class="mobile-nav-btn active" data-target="paneDialer">
                <i class="fa fa-phone"></i> Phone & Dialer
            </button>
            <button type="button" class="mobile-nav-btn" data-target="paneQueue">
                <i class="fa fa-list"></i> Database (<span id="mobileQueueCount"><?php echo $total_queue; ?></span>)
            </button>
            <button type="button" class="mobile-nav-btn" data-target="paneLogs">
                <i class="fa fa-history"></i> Recent Logs
            </button>
        </div>

        <div class="row" style="margin: 0 -12px;">
            <div class="col-calling-left" id="paneQueueWrap">
                <div class="c-card">
                    <div class="c-card-header" style="flex-wrap: wrap; gap: 8px;">
                        <div style="flex: 1; min-width: 160px;">
                            <label style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 600; margin-bottom: 2px; display: block;">Select Whom to Call</label>
                            <select id="selectQueueStatus" class="form-control input-sm" style="font-weight: 600; color: #0f172a; border-radius: 4px;">
                                <option value="all" <?php echo ($selected_status == 'all') ? 'selected' : ''; ?>>All Active Leads (Excl. Lost / Won / DNCR)</option>
                                <option value="follow_up" <?php echo ($selected_status == 'follow_up') ? 'selected' : ''; ?>>Follow up / Call Back</option>
                                <option value="prospective" <?php echo ($selected_status == 'prospective') ? 'selected' : ''; ?>>Prospective</option>
                                <option value="in_process" <?php echo ($selected_status == 'in_process') ? 'selected' : ''; ?>>In Process</option>
                                <option value="database" <?php echo ($selected_status == 'database') ? 'selected' : ''; ?>>Database Leads</option>
                                <option value="expected" <?php echo ($selected_status == 'expected') ? 'selected' : ''; ?>>Expected</option>
                                <option value="lead" <?php echo ($selected_status == 'lead') ? 'selected' : ''; ?>>Proposal / Interested</option>
                                <option value="submitted" <?php echo ($selected_status == 'submitted') ? 'selected' : ''; ?>>Submitted</option>
                                <option value="pending" <?php echo ($selected_status == 'pending') ? 'selected' : ''; ?>>Pending Activation</option>
                            </select>
                        </div>
                        <div>
                            <button type="button" class="btn btn-default btn-sm" id="btnManualDialMode" style="font-weight: 600; color: #2563eb; border-color: #93c5fd; background: #eff6ff;" title="Type number manually">
                                <i class="fa fa-keyboard-o"></i> Type Number
                            </button>
                        </div>
                    </div>

                    <div style="padding: 10px 14px; border-bottom: 1px solid #f1f5f9; background: #fafafa;">
                        <div class="input-group input-group-sm">
                            <span class="input-group-addon" style="background: #ffffff; border-right: none;"><i class="fa fa-search text-muted"></i></span>
                            <input type="text" id="inputQueueSearch" class="form-control" placeholder="Search my database..." style="border-left: none;">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" id="btnQueueRefresh" title="Refresh"><i class="fa fa-refresh"></i></button>
                            </span>
                        </div>
                    </div>

                    <div class="queue-scroll" id="queueListContainer">
                        <?php if (!empty($queue)): ?>
                            <?php foreach ($queue as $idx => $lead): ?>
                                <?php 
                                    $p1 = !empty($lead['contact1']) ? trim($lead['contact1']) : '';
                                    $p2 = !empty($lead['alt_contact']) ? trim($lead['alt_contact']) : (!empty($lead['contact2']) ? trim($lead['contact2']) : (!empty($lead['mobile']) ? trim($lead['mobile']) : ''));
                                    if ($p1 === $p2) { $p2 = ''; }
                                    $disp_num = !empty($p1) ? $p1 : $p2;
                                    $has_two = (!empty($p1) && !empty($p2));

                                    $st_class = 'status-pill-badge';
                                    $st_name = !empty($lead['status_name']) ? $lead['status_name'] : 'Database';
                                    if (stripos($st_name, 'follow') !== false || (int)$lead['status'] == 38) {
                                        $st_class .= ' status-follow';
                                    } elseif (stripos($st_name, 'prospect') !== false || (int)$lead['status'] == 37) {
                                        $st_class .= ' status-prospect';
                                    }
                                ?>
                                <div class="lead-item-row <?php echo ($idx === 0) ? 'active' : ''; ?>" data-id="<?php echo $lead['id']; ?>" data-index="<?php echo $idx; ?>">
                                    <div class="lead-title-row">
                                        <span><?php echo htmlspecialchars($lead['customer']); ?></span>
                                        <span class="<?php echo $st_class; ?>"><?php echo htmlspecialchars($st_name); ?></span>
                                    </div>
                                    <div class="lead-meta-row">
                                        <span><i class="fa fa-user" style="color: #94a3b8;"></i> <?php echo !empty($lead['poc']) ? htmlspecialchars($lead['poc']) : 'No POC'; ?></span>
                                        <?php if ($has_two): ?>
                                            <span style="display: inline-flex; gap: 4px; align-items: center;">
                                                <span class="badge" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 10px; font-weight: 600; cursor: pointer; padding: 2px 6px;" onclick="event.stopPropagation(); pickQueueNumber('<?php echo addslashes($p1); ?>', '<?php echo addslashes($p2); ?>', 'primary', <?php echo $lead['id']; ?>);"><i class="fa fa-phone"></i> <?php echo htmlspecialchars($p1); ?></span>
                                                <span class="badge" style="background: #f8fafc; color: #475569; border: 1px solid #cbd5e1; font-size: 10px; font-weight: 600; cursor: pointer; padding: 2px 6px;" onclick="event.stopPropagation(); pickQueueNumber('<?php echo addslashes($p1); ?>', '<?php echo addslashes($p2); ?>', 'alt', <?php echo $lead['id']; ?>);"><i class="fa fa-mobile"></i> <?php echo htmlspecialchars($p2); ?></span>
                                            </span>
                                        <?php else: ?>
                                            <span><i class="fa fa-phone" style="color: #2563eb;"></i> <?php echo !empty($disp_num) ? htmlspecialchars($disp_num) : '—'; ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="padding: 35px 20px; text-align: center; color: #94a3b8;">
                                <i class="fa fa-inbox fa-3x" style="margin-bottom: 8px; display: block; opacity: 0.4;"></i>
                                <p style="font-weight: 600; color: #64748b; margin-bottom: 4px;">No leads match this status</p>
                                <p style="font-size: 12px; margin: 0;">Select another status above or click "Type Number" to dial manually.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-calling-right" id="paneDialerWrap">
                <div class="c-card">
                    <div class="c-card-header">
                        <div>
                            <h3 class="c-card-title" id="cardClientTitle">
                                <?php echo !empty($active_lead) ? htmlspecialchars($active_lead['customer']) : 'Manual Dial Pad'; ?>
                            </h3>
                            <span class="badge" style="background: #f1f5f9; color: #475569; font-size: 10.5px; margin-top: 3px;" id="cardClientCode">
                                <?php echo !empty($active_lead) ? '#' . htmlspecialchars($active_lead['lead_id']) : 'Direct Number Input'; ?>
                            </span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div class="call-timer-badge" id="callLiveTimer">00:00</div>
                        </div>
                    </div>

                    <div class="c-card-body">
                        <?php 
                            $init_p1 = !empty($active_lead['contact1']) ? trim($active_lead['contact1']) : '';
                            $init_p2 = !empty($active_lead['alt_contact']) ? trim($active_lead['alt_contact']) : (!empty($active_lead['contact2']) ? trim($active_lead['contact2']) : (!empty($active_lead['mobile']) ? trim($active_lead['mobile']) : ''));
                            if ($init_p1 === $init_p2) { $init_p2 = ''; }
                            $init_has_two = (!empty($init_p1) && !empty($init_p2));
                        ?>
                        <div id="dualNumberSelector" class="number-selector-box" style="<?php echo ($init_has_two) ? 'display: flex;' : 'display: none;'; ?>">
                            <span style="font-size: 11.5px; color: #64748b; font-weight: 600; display: flex; align-items: center; margin-right: 4px;">Choose Number:</span>
                            <button type="button" class="btn-num-choice active" id="btnPickPrimary" data-target="primary">
                                <i class="fa fa-phone"></i> Primary: <span id="txtPrimaryNum"><?php echo htmlspecialchars($init_p1); ?></span>
                            </button>
                            <button type="button" class="btn-num-choice" id="btnPickAlt" data-target="alt">
                                <i class="fa fa-mobile"></i> Alternate: <span id="txtAltNum"><?php echo htmlspecialchars($init_p2); ?></span>
                            </button>
                        </div>

                        <div class="dialer-input-wrap">
                            <input type="text" id="phoneDialerInput" class="dialer-phone-input" placeholder="Type phone number or select lead..." value="<?php echo htmlspecialchars(!empty($init_p1) ? $init_p1 : $init_p2); ?>">
                            <div class="dialer-input-actions">
                                <button type="button" class="dialer-action-btn" id="btnToggleKeypad" title="Show / Hide Keypad">
                                    <i class="fa fa-th"></i>
                                </button>
                                <button type="button" class="dialer-action-btn" id="btnClearPhone" title="Clear Number">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <div id="keypadDropdown" style="display: none; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; margin-bottom: 14px;">
                            <div class="dialpad-container">
                                <div class="keypad-btn" data-key="1"><div class="keypad-btn-num">1</div><div class="keypad-btn-sub">&nbsp;</div></div>
                                <div class="keypad-btn" data-key="2"><div class="keypad-btn-num">2</div><div class="keypad-btn-sub">ABC</div></div>
                                <div class="keypad-btn" data-key="3"><div class="keypad-btn-num">3</div><div class="keypad-btn-sub">DEF</div></div>
                                <div class="keypad-btn" data-key="4"><div class="keypad-btn-num">4</div><div class="keypad-btn-sub">GHI</div></div>
                                <div class="keypad-btn" data-key="5"><div class="keypad-btn-num">5</div><div class="keypad-btn-sub">JKL</div></div>
                                <div class="keypad-btn" data-key="6"><div class="keypad-btn-num">6</div><div class="keypad-btn-sub">MNO</div></div>
                                <div class="keypad-btn" data-key="7"><div class="keypad-btn-num">7</div><div class="keypad-btn-sub">PQRS</div></div>
                                <div class="keypad-btn" data-key="8"><div class="keypad-btn-num">8</div><div class="keypad-btn-sub">TUV</div></div>
                                <div class="keypad-btn" data-key="9"><div class="keypad-btn-num">9</div><div class="keypad-btn-sub">WXYZ</div></div>
                                <div class="keypad-btn" data-key="*"><div class="keypad-btn-num">*</div><div class="keypad-btn-sub">&nbsp;</div></div>
                                <div class="keypad-btn" data-key="0"><div class="keypad-btn-num">0</div><div class="keypad-btn-sub">+</div></div>
                                <div class="keypad-btn" data-key="#"><div class="keypad-btn-num">#</div><div class="keypad-btn-sub">&nbsp;</div></div>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px;">
                            <div>
                                <span id="callStateBadge" style="font-size: 13px; font-weight: 600; color: #475569;">
                                    <span class="live-pulse offline"></span> Ready to Call
                                </span>
                            </div>
                            <div style="display: flex; gap: 8px;">
                                <button type="button" id="btnCallNow" class="call-btn-green">
                                    <i class="fa fa-phone"></i> Call via 3CX
                                </button>
                                <button type="button" id="btnEndNow" class="call-btn-red" style="display: none;">
                                    <i class="fa fa-phone"></i> End Call
                                </button>
                                <a id="linkTelBackup" href="javascript:void(0);" class="btn btn-default btn-sm" title="Open Tel" style="display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa fa-external-link"></i> Tel
                                </a>
                            </div>
                        </div>

                        <div id="leadMetaSummary" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; margin-bottom: 16px; font-size: 12.5px;">
                            <div class="row">
                                <div class="col-xs-6">
                                    <span style="color: #64748b;">POC:</span> <strong id="lblPoc"><?php echo !empty($active_lead['poc']) ? htmlspecialchars($active_lead['poc']) : '—'; ?></strong>
                                </div>
                                <div class="col-xs-6">
                                    <span style="color: #64748b;">Email:</span> <span id="lblEmail"><?php echo !empty($active_lead['email']) ? htmlspecialchars($active_lead['email']) : '—'; ?></span>
                                </div>
                            </div>
                        </div>

                        <form id="callDispositionForm">
                            <input type="hidden" id="formLeadId" name="lead_id" value="<?php echo !empty($active_lead) ? $active_lead['id'] : 0; ?>">
                            <input type="hidden" id="formCustomerName" name="customer_name" value="<?php echo !empty($active_lead) ? htmlspecialchars($active_lead['customer']) : ''; ?>">
                            <input type="hidden" id="formCallLogId" name="call_log_id" value="0">
                            <input type="hidden" id="formDuration" name="call_duration" value="0">

                            <div class="row">
                                <div class="col-sm-6 form-group">
                                    <label style="font-size: 12px; color: #334155; font-weight: 600;">Outcome / Disposition <span class="text-danger">*</span></label>
                                    <select class="form-control input-sm" id="dispositionSelect" name="call_disposition" required>
                                        <option value="Connected">Connected / Conversation Held</option>
                                        <option value="No Answer">No Answer / Ringing</option>
                                        <option value="Busy">Busy / Call Waiting</option>
                                        <option value="Follow-up Required">Follow-up Required</option>
                                        <option value="Meeting Scheduled">Meeting Scheduled</option>
                                        <option value="Wrong Number">Wrong / Invalid Number</option>
                                        <option value="Not Interested">Not Interested</option>
                                    </select>
                                </div>
                                <div class="col-sm-6 form-group" id="groupStatusUpdate">
                                    <label style="font-size: 12px; color: #334155; font-weight: 600;">Update Lead Status</label>
                                    <select class="form-control input-sm" id="leadStatusSelect" name="status">
                                        <?php foreach ($statuses as $st): ?>
                                            <option value="<?php echo $st['id']; ?>" <?php echo (!empty($active_lead) && $active_lead['status'] == $st['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($st['status']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row" id="groupFollowupDateRow">
                                <div class="col-sm-6 form-group">
                                    <label style="font-size: 12px; color: #334155; font-weight: 600;">Next Follow-up Date</label>
                                    <input type="date" class="form-control input-sm" id="nextDateInput" name="next_followup">
                                </div>
                                <div class="col-sm-6 form-group">
                                    <label style="font-size: 12px; color: #334155; font-weight: 600;">Next Follow-up Time</label>
                                    <input type="time" class="form-control input-sm" id="nextTimeInput" name="next_followup_time">
                                </div>
                            </div>

                            <div class="form-group">
                                <label style="font-size: 12px; color: #334155; font-weight: 600;">Discussion Remarks & Notes <span class="text-danger">*</span></label>
                                <textarea class="form-control" rows="2" id="remarksTextarea" name="remarks" placeholder="Enter conversation details, client requirements, next action items..." required></textarea>
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                                <button type="submit" class="btn btn-primary" id="btnSaveSubmit" style="font-weight: 600;">
                                    <i class="fa fa-arrow-right"></i> Save & Load Next
                                </button>
                                <button type="button" class="btn btn-default" id="btnSaveLogOnly" style="font-weight: 600;">
                                    <i class="fa fa-save"></i> Save Log Only
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="c-card" id="paneLogsWrap">
                    <div class="c-card-header">
                        <h4 class="c-card-title">
                            <i class="fa fa-history text-primary"></i> Recent Call Logs
                        </h4>
                        <button type="button" class="btn btn-default btn-xs" id="btnRefreshLogsTable">
                            <i class="fa fa-refresh"></i> Refresh
                        </button>
                    </div>
                    <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                        <table class="table logs-table" style="margin: 0;">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Contact / Client</th>
                                    <th>Number</th>
                                    <th>Duration</th>
                                    <th>Outcome</th>
                                    <th>Notes</th>
                                    <th style="text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="logsTableBody">
                                <?php if (!empty($recent_logs)): ?>
                                    <?php foreach ($recent_logs as $log): ?>
                                        <tr>
                                            <td><?php echo date('d M, h:i A', strtotime($log['created_at'])); ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars(!empty($log['customer_name']) ? $log['customer_name'] : (!empty($log['lead_customer']) ? $log['lead_customer'] : '—')); ?></strong>
                                            </td>
                                            <td style="font-family: monospace; font-weight: 600; color: #2563eb;"><?php echo htmlspecialchars($log['phone_number']); ?></td>
                                            <td><?php echo sprintf('%02d:%02d', floor($log['duration'] / 60), $log['duration'] % 60); ?></td>
                                            <td>
                                                <span class="badge" style="background: #e2e8f0; color: #1e293b; font-size: 10.5px;">
                                                    <?php echo htmlspecialchars(!empty($log['disposition']) ? $log['disposition'] : 'Dialed'); ?>
                                                </span>
                                            </td>
                                            <td style="max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                <?php echo htmlspecialchars($log['notes']); ?>
                                            </td>
                                            <td style="text-align: right;">
                                                <button type="button" class="btn btn-xs btn-default btn-quick-redial" data-phone="<?php echo htmlspecialchars($log['phone_number']); ?>" data-name="<?php echo htmlspecialchars($log['customer_name']); ?>" data-lead-id="<?php echo (int)$log['lead_id']; ?>">
                                                    <i class="fa fa-phone text-success"></i> Redial
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 25px; color: #94a3b8;">
                                            No calls logged yet. Start calling leads or type a number.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="modal3cxSettings" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 8px;">
            <div class="modal-header" style="background: #f8fafc; border-radius: 8px 8px 0 0; border-bottom: 1px solid #e2e8f0;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" style="font-size: 16px; font-weight: 700; color: #0f172a;">
                    <i class="fa fa-cogs text-primary" style="margin-right: 6px;"></i> 3CX PBX Connection Settings
                </h4>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <div id="settingsAlertBox"></div>
                <form id="form3cxSettings">
                    <div class="form-group">
                        <label style="font-size: 12.5px; color: #334155; font-weight: 600;">3CX PBX Domain / FQDN <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="cfgPbxFqdn" name="pbx_fqdn" placeholder="e.g. rolex.3cx.ae or pbx.mycompany.com" value="<?php echo htmlspecialchars($threecx_config['pbx_fqdn']); ?>" required>
                        <small class="text-muted">Enter your 3CX Fully Qualified Domain Name (without http/https).</small>
                    </div>

                    <div class="form-group">
                        <label style="font-size: 12.5px; color: #334155; font-weight: 600;">Client ID (Server Principal ID) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="cfgClientId" name="client_id" placeholder="Server Principal Client ID" value="<?php echo htmlspecialchars($threecx_config['client_id']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label style="font-size: 12.5px; color: #334155; font-weight: 600;">Client Secret (Secret Key) <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="cfgClientSecret" name="client_secret" placeholder="3CX API Secret Key" value="<?php echo htmlspecialchars($threecx_config['client_secret']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label style="font-size: 12.5px; color: #334155; font-weight: 600;">Default Extension (Optional)</label>
                        <input type="text" class="form-control" id="cfgDefaultExt" name="default_extension" placeholder="e.g. 100 or 101" value="<?php echo htmlspecialchars($threecx_config['default_extension']); ?>">
                    </div>

                    <div class="checkbox">
                        <label style="font-weight: 600; color: #334155;">
                            <input type="checkbox" id="cfgAutoOpen" name="auto_open_webclient" <?php echo (!empty($threecx_config['auto_open_webclient'])) ? 'checked' : ''; ?>> Automatically open 3CX Web Client on dialing
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="background: #f8fafc; border-radius: 0 0 8px 8px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between;">
                <button type="button" class="btn btn-default" id="btnTest3cxConn" style="font-weight: 600;">
                    <i class="fa fa-plug text-primary"></i> Test 3CX Connection
                </button>
                <div>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btnSave3cxSettings" style="font-weight: 600;">Save Settings</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var callTimer = null;
    var callSeconds = 0;
    var currentQueueIdx = 0;
    var activeCallLogId = 0;
    var primaryNumber = '<?php echo !empty($init_p1) ? addslashes($init_p1) : ""; ?>';
    var altNumber = '<?php echo !empty($init_p2) ? addslashes($init_p2) : ""; ?>';

    function formatTime(s) {
        var m = Math.floor(s / 60);
        var sec = s % 60;
        return (m < 10 ? '0' + m : m) + ':' + (sec < 10 ? '0' + sec : sec);
    }

    $('.mobile-nav-btn').click(function() {
        var target = $(this).data('target');
        $('.mobile-nav-btn').removeClass('active');
        $(this).addClass('active');

        if (target === 'paneQueue') {
            $('#paneQueueWrap').show();
            $('#paneDialerWrap').hide();
            $('#paneLogsWrap').hide();
        } else if (target === 'paneLogs') {
            $('#paneQueueWrap').hide();
            $('#paneDialerWrap').show();
            $('#paneLogsWrap').show();
            loadCallLogsTable();
        } else {
            $('#paneQueueWrap').hide();
            $('#paneDialerWrap').show();
            $('#paneLogsWrap').hide();
        }
    });

    $(window).resize(function() {
        if ($(window).width() >= 992) {
            $('#paneQueueWrap').show();
            $('#paneDialerWrap').show();
            $('#paneLogsWrap').show();
        }
    });

    $('#btnToggleKeypad').click(function() {
        $('#keypadDropdown').slideToggle(120);
    });

    $('.keypad-btn').click(function() {
        var key = $(this).data('key');
        var cur = $('#phoneDialerInput').val();
        $('#phoneDialerInput').val(cur + key);
    });

    $('#btnClearPhone').click(function() {
        $('#phoneDialerInput').val('');
    });

    $('#btnManualDialMode').click(function() {
        switchToManualDial();
    });

    function switchToManualDial() {
        $('.lead-item-row').removeClass('active');
        $('#cardClientTitle').text('Manual Direct Call');
        $('#cardClientCode').text('Direct Dialing Mode');
        $('#formLeadId').val('0');
        $('#formCustomerName').val('Direct Dial');
        $('#leadMetaSummary').hide();
        $('#dualNumberSelector').hide();
        $('#phoneDialerInput').val('').focus();
        $('#linkTelBackup').hide();
        primaryNumber = '';
        altNumber = '';

        if ($(window).width() < 992) {
            $('.mobile-nav-btn[data-target="paneDialer"]').trigger('click');
        }
    }

    $('#btnOpenSettings').click(function() {
        $('#settingsAlertBox').html('');
        $('#modal3cxSettings').modal('show');
    });

    $('#btnTest3cxConn').click(function() {
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Testing...');
        $('#settingsAlertBox').html('');

        $.ajax({
            url: '<?php echo base_url(); ?>manager/calls/ajax_test_3cx',
            type: 'POST',
            data: $('#form3cxSettings').serialize(),
            dataType: 'json',
            success: function(res) {
                btn.prop('disabled', false).html('<i class="fa fa-plug text-primary"></i> Test 3CX Connection');
                if (res.status) {
                    $('#settingsAlertBox').html('<div class="alert alert-success" style="font-size: 13px; padding: 10px 14px;"><i class="fa fa-check-circle"></i> ' + res.message + '</div>');
                    $('#topPbxFqdn').text($('#cfgPbxFqdn').val() || 'Connected');
                } else {
                    $('#settingsAlertBox').html('<div class="alert alert-danger" style="font-size: 13px; padding: 10px 14px;"><i class="fa fa-exclamation-triangle"></i> ' + (res.message || 'Connection failed') + '</div>');
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<i class="fa fa-plug text-primary"></i> Test 3CX Connection');
                $('#settingsAlertBox').html('<div class="alert alert-danger" style="font-size: 13px; padding: 10px 14px;">Error reaching 3CX test service.</div>');
            }
        });
    });

    $('#btnSave3cxSettings').click(function() {
        var btn = $(this);
        btn.prop('disabled', true).text('Saving...');
        $.ajax({
            url: '<?php echo base_url(); ?>manager/calls/ajax_save_3cx_config',
            type: 'POST',
            data: $('#form3cxSettings').serialize(),
            dataType: 'json',
            success: function(res) {
                btn.prop('disabled', false).text('Save Settings');
                if (res.status) {
                    $('#settingsAlertBox').html('<div class="alert alert-success" style="font-size: 13px; padding: 10px 14px;"><i class="fa fa-check"></i> ' + res.message + '</div>');
                    setTimeout(function() {
                        $('#modal3cxSettings').modal('hide');
                    }, 800);
                } else {
                    $('#settingsAlertBox').html('<div class="alert alert-danger" style="font-size: 13px; padding: 10px 14px;">' + res.message + '</div>');
                }
            },
            error: function() {
                btn.prop('disabled', false).text('Save Settings');
                $('#settingsAlertBox').html('<div class="alert alert-danger" style="font-size: 13px; padding: 10px 14px;">Failed to save settings.</div>');
            }
        });
    });

    $('#selectQueueStatus').change(function() {
        reloadQueue();
    });

    $('#btnQueueRefresh').click(function() {
        reloadQueue();
    });

    $('#inputQueueSearch').on('keyup', function() {
        var q = $(this).val().toLowerCase();
        $('.lead-item-row').each(function() {
            var txt = $(this).text().toLowerCase();
            if (txt.indexOf(q) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    function reloadQueue() {
        var st = $('#selectQueueStatus').val();
        var search = $('#inputQueueSearch').val();
        $('#queueListContainer').html('<div style="padding: 30px; text-align: center; color: #94a3b8;"><i class="fa fa-spinner fa-spin fa-2x"></i><p style="margin-top: 8px;">Loading database leads...</p></div>');

        $.ajax({
            url: '<?php echo base_url(); ?>manager/calls/ajax_get_queue',
            type: 'GET',
            data: { status: st, search: search },
            dataType: 'json',
            success: function(res) {
                if (res.status && res.leads && res.leads.length > 0) {
                    var html = '';
                    $.each(res.leads, function(idx, l) {
                        var p1 = l.contact1 || '';
                        var p2 = l.alt_contact || l.contact2 || l.mobile || '';
                        if (p1 === p2) { p2 = ''; }
                        var disp = p1 || p2 || '—';
                        var hasTwo = (p1 && p2);

                        var stClass = 'status-pill-badge';
                        var stName = l.status_name || 'Database';
                        if (stName.toLowerCase().indexOf('follow') !== -1) {
                            stClass += ' status-follow';
                        } else if (stName.toLowerCase().indexOf('prospect') !== -1) {
                            stClass += ' status-prospect';
                        }

                        html += '<div class="lead-item-row ' + (idx === 0 ? 'active' : '') + '" data-id="' + l.id + '" data-index="' + idx + '">';
                        html += '  <div class="lead-title-row">';
                        html += '    <span>' + escapeHtml(l.customer) + '</span>';
                        html += '    <span class="' + stClass + '">' + escapeHtml(stName) + '</span>';
                        html += '  </div>';
                        html += '  <div class="lead-meta-row">';
                        html += '    <span><i class="fa fa-user" style="color: #94a3b8;"></i> ' + escapeHtml(l.poc || 'No POC') + '</span>';
                        if (hasTwo) {
                            html += '    <span style="display: inline-flex; gap: 4px; align-items: center;">';
                            html += '      <span class="badge" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 10px; font-weight: 600; cursor: pointer; padding: 2px 6px;" onclick="event.stopPropagation(); pickQueueNumber(\'' + escapeHtml(p1) + '\', \'' + escapeHtml(p2) + '\', \'primary\', ' + l.id + ');"><i class="fa fa-phone"></i> ' + escapeHtml(p1) + '</span>';
                            html += '      <span class="badge" style="background: #f8fafc; color: #475569; border: 1px solid #cbd5e1; font-size: 10px; font-weight: 600; cursor: pointer; padding: 2px 6px;" onclick="event.stopPropagation(); pickQueueNumber(\'' + escapeHtml(p1) + '\', \'' + escapeHtml(p2) + '\', \'alt\', ' + l.id + ');"><i class="fa fa-mobile"></i> ' + escapeHtml(p2) + '</span>';
                            html += '    </span>';
                        } else {
                            html += '    <span><i class="fa fa-phone" style="color: #2563eb;"></i> ' + escapeHtml(disp) + '</span>';
                        }
                        html += '  </div>';
                        html += '</div>';
                    });
                    $('#queueListContainer').html(html);
                    $('#mobileQueueCount').text(res.leads.length);
                    loadLeadInfo(res.leads[0].id);
                } else {
                    $('#queueListContainer').html('<div style="padding: 35px 20px; text-align: center; color: #94a3b8;"><i class="fa fa-inbox fa-3x" style="margin-bottom: 8px; display: block; opacity: 0.4;"></i><p style="font-weight: 600; color: #64748b;">No leads found</p></div>');
                    $('#mobileQueueCount').text('0');
                }
            }
        });
    }

    $(document).on('click', '.lead-item-row', function() {
        var id = $(this).data('id');
        var idx = $(this).data('index');
        currentQueueIdx = idx;
        $('.lead-item-row').removeClass('active');
        $(this).addClass('active');
        loadLeadInfo(id);

        if ($(window).width() < 992) {
            $('.mobile-nav-btn[data-target="paneDialer"]').trigger('click');
        }
    });

    window.pickQueueNumber = function(p1, p2, choice, leadId) {
        $('.lead-item-row').removeClass('active');
        $('.lead-item-row[data-id="' + leadId + '"]').addClass('active');
        loadLeadInfo(leadId, function() {
            if (choice === 'alt') {
                $('#btnPickAlt').trigger('click');
            } else {
                $('#btnPickPrimary').trigger('click');
            }
        });
        if ($(window).width() < 992) {
            $('.mobile-nav-btn[data-target="paneDialer"]').trigger('click');
        }
    };

    function loadLeadInfo(leadId, cb) {
        $.ajax({
            url: '<?php echo base_url(); ?>manager/calls/ajax_get_lead/' + leadId,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.status && res.lead) {
                    var l = res.lead;
                    primaryNumber = l.phone_primary || '';
                    altNumber = l.phone_alt || '';

                    $('#cardClientTitle').text(l.customer || '—');
                    $('#cardClientCode').text(l.lead_id ? '#' + l.lead_id : '');
                    $('#formLeadId').val(l.id);
                    $('#formCustomerName').val(l.customer || '');
                    $('#lblPoc').text(l.poc || '—');
                    $('#lblEmail').text(l.email || '—');
                    $('#leadMetaSummary').show();

                    if (l.has_two_numbers) {
                        $('#txtPrimaryNum').text(primaryNumber);
                        $('#txtAltNum').text(altNumber);
                        $('#btnPickPrimary').addClass('active');
                        $('#btnPickAlt').removeClass('active');
                        $('#dualNumberSelector').show();
                        $('#phoneDialerInput').val(primaryNumber);
                    } else {
                        $('#dualNumberSelector').hide();
                        $('#phoneDialerInput').val(primaryNumber || altNumber);
                    }

                    var dialNum = $('#phoneDialerInput').val();
                    if (dialNum) {
                        $('#linkTelBackup').attr('href', 'tel:' + dialNum).show();
                    } else {
                        $('#linkTelBackup').hide();
                    }

                    if (l.status) {
                        $('#leadStatusSelect').val(l.status);
                    }

                    stopCallTimer();
                    $('#callStateBadge').html('<span class="live-pulse offline"></span> Ready to Call');
                    $('#btnCallNow').show();
                    $('#btnEndNow').hide();
                    if (typeof cb === 'function') {
                        cb();
                    }
                }
            }
        });
    }

    $('#btnPickPrimary').click(function() {
        $('.btn-num-choice').removeClass('active');
        $(this).addClass('active');
        if (primaryNumber) {
            $('#phoneDialerInput').val(primaryNumber);
            $('#linkTelBackup').attr('href', 'tel:' + primaryNumber);
        }
    });

    $('#btnPickAlt').click(function() {
        $('.btn-num-choice').removeClass('active');
        $(this).addClass('active');
        if (altNumber) {
            $('#phoneDialerInput').val(altNumber);
            $('#linkTelBackup').attr('href', 'tel:' + altNumber);
        }
    });

    function startCallTimer() {
        clearInterval(callTimer);
        callSeconds = 0;
        $('#callLiveTimer').text('00:00');
        callTimer = setInterval(function() {
            callSeconds++;
            $('#callLiveTimer').text(formatTime(callSeconds));
            $('#formDuration').val(callSeconds);
        }, 1000);
    }

    function stopCallTimer() {
        clearInterval(callTimer);
        return callSeconds;
    }

    $('#btnCallNow').click(function() {
        var phone = $('#phoneDialerInput').val().trim();
        var leadId = $('#formLeadId').val();
        var customerName = $('#formCustomerName').val();

        if (!phone) {
            alert('Please enter or select a phone number to call.');
            $('#phoneDialerInput').focus();
            return;
        }

        $('#btnCallNow').hide();
        $('#btnEndNow').show();
        $('#callStateBadge').html('<span class="live-pulse active-call"></span> Calling via 3CX...');
        startTimerAndLog(phone, leadId, customerName);
    });

    function startTimerAndLog(phone, leadId, customerName) {
        startCallTimer();
        $.ajax({
            url: '<?php echo base_url(); ?>manager/calls/ajax_initiate_call',
            type: 'POST',
            data: { phone: phone, lead_id: leadId, customer_name: customerName },
            dataType: 'json',
            success: function(res) {
                if (res.status) {
                    activeCallLogId = res.call_log_id;
                    $('#formCallLogId').val(activeCallLogId);

                    var isMobile = $(window).width() < 992;
                    if (isMobile) {
                        if (res.dispatch && res.dispatch.tel_url) {
                            window.location.href = res.dispatch.tel_url;
                        } else if (res.dispatch && res.dispatch.webclient_url) {
                            window.open(res.dispatch.webclient_url, '_blank');
                        }
                    } else {
                        if (res.dispatch && res.dispatch.webclient_url) {
                            window.open(res.dispatch.webclient_url, '_blank');
                        } else if (res.dispatch && res.dispatch.tel_url) {
                            window.location.href = res.dispatch.tel_url;
                        }
                    }
                }
            }
        });
    }

    $('#btnEndNow').click(function() {
        stopCallTimer();
        $('#btnCallNow').show();
        $('#btnEndNow').hide();
        $('#callStateBadge').html('<span class="live-pulse offline"></span> Call Ended &bull; Enter Outcome');
    });

    $('#callDispositionForm').on('submit', function(e) {
        e.preventDefault();
        saveCurrentCall(true);
    });

    $('#btnSaveLogOnly').click(function() {
        saveCurrentCall(false);
    });

    function saveCurrentCall(loadNext) {
        var phone = $('#phoneDialerInput').val().trim();
        var formData = $('#callDispositionForm').serialize() + '&phone=' + encodeURIComponent(phone);
        var submitBtn = $('#btnSaveSubmit');
        submitBtn.prop('disabled', true);

        $.ajax({
            url: '<?php echo base_url(); ?>manager/calls/ajax_save_call',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                submitBtn.prop('disabled', false);
                if (res.status) {
                    $('#remarksTextarea').val('');
                    stopCallTimer();
                    $('#callLiveTimer').text('00:00');
                    $('#formDuration').val('0');
                    $('#formCallLogId').val('0');
                    activeCallLogId = 0;
                    $('#callStateBadge').html('<span class="live-pulse offline"></span> Ready to Call');

                    var todayCount = parseInt($('#statCallsToday').text().replace(/,/g, '')) || 0;
                    $('#statCallsToday').text(todayCount + 1);

                    if (res.duration > 0) {
                        var connCount = parseInt($('#statCallsConnected').text().replace(/,/g, '')) || 0;
                        $('#statCallsConnected').text(connCount + 1);
                    }

                    loadCallLogsTable();

                    if (loadNext && $('#formLeadId').val() > 0) {
                        var nextRow = $('.lead-item-row[data-index="' + (currentQueueIdx + 1) + '"]');
                        if (nextRow.length) {
                            nextRow.trigger('click');
                        } else {
                            alert('Call log saved. Reached end of current queue.');
                        }
                    } else {
                        alert('Call log saved successfully.');
                    }
                } else {
                    alert('Failed to save call: ' + (res.message || 'Error'));
                }
            },
            error: function() {
                submitBtn.prop('disabled', false);
                alert('Server error while saving call record.');
            }
        });
    }

    function loadCallLogsTable() {
        $.ajax({
            url: '<?php echo base_url(); ?>manager/calls/ajax_get_call_logs',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.status && res.logs && res.logs.length > 0) {
                    var html = '';
                    $.each(res.logs, function(i, log) {
                        var d = new Date(log.created_at);
                        var dateStr = d.toLocaleDateString([], {month:'short', day:'numeric'}) + ', ' + d.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                        var durSec = parseInt(log.duration) || 0;
                        var name = log.customer_name || log.lead_customer || 'Direct Call';

                        html += '<tr>';
                        html += '  <td>' + dateStr + '</td>';
                        html += '  <td><strong>' + escapeHtml(name) + '</strong></td>';
                        html += '  <td style="font-family: monospace; font-weight: 600; color: #2563eb;">' + escapeHtml(log.phone_number) + '</td>';
                        html += '  <td>' + formatTime(durSec) + '</td>';
                        html += '  <td><span class="badge" style="background: #e2e8f0; color: #1e293b; font-size: 10.5px;">' + escapeHtml(log.disposition || 'Dialed') + '</span></td>';
                        html += '  <td style="max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' + escapeHtml(log.notes || '') + '</td>';
                        html += '  <td style="text-align: right;"><button type="button" class="btn btn-xs btn-default btn-quick-redial" data-phone="' + escapeHtml(log.phone_number) + '" data-name="' + escapeHtml(name) + '" data-lead-id="' + (log.lead_id || 0) + '"><i class="fa fa-phone text-success"></i> Redial</button></td>';
                        html += '</tr>';
                    });
                    $('#logsTableBody').html(html);
                }
            }
        });
    }

    $('#btnRefreshLogsTable').click(function() {
        loadCallLogsTable();
    });

    $(document).on('click', '.btn-quick-redial', function() {
        var phone = $(this).data('phone');
        var name = $(this).data('name');
        var leadId = $(this).data('lead-id');

        if (leadId && leadId > 0) {
            loadLeadInfo(leadId);
        } else {
            switchToManualDial();
            $('#phoneDialerInput').val(phone);
            $('#cardClientTitle').text(name || 'Direct Call');
        }

        if ($(window).width() < 992) {
            $('.mobile-nav-btn[data-target="paneDialer"]').trigger('click');
        }
    });

    function escapeHtml(str) {
        if (!str) return '';
        var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return str.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
    }
});
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
