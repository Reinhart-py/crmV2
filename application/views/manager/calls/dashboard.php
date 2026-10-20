<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>

<style>
.call-card {
    background: #ffffff;
    border-radius: 6px;
    border: 1px solid #e0e6ed;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    margin-bottom: 20px;
}
.call-card-header {
    padding: 14px 18px;
    border-bottom: 1px solid #edf2f7;
    background: #f8fafc;
    border-radius: 6px 6px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.call-card-title {
    font-size: 16px;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}
.call-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}
.call-badge-idle { background: #e2e8f0; color: #475569; }
.call-badge-active { background: #dcfce7; color: #166534; animation: pulse 1.5s infinite; }
.call-badge-wrapup { background: #fef3c7; color: #92400e; }
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.6; }
    100% { opacity: 1; }
}
.call-btn-dial {
    background: #16a34a;
    color: #ffffff;
    border: none;
    padding: 10px 22px;
    font-size: 15px;
    font-weight: 600;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
}
.call-btn-dial:hover {
    background: #15803d;
    color: #ffffff;
    text-decoration: none;
}
.call-btn-hangup {
    background: #dc2626;
    color: #ffffff;
    border: none;
    padding: 10px 22px;
    font-size: 15px;
    font-weight: 600;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.2s;
}
.call-btn-hangup:hover {
    background: #b91c1c;
    color: #ffffff;
}
.call-timer-box {
    background: #0f172a;
    color: #38bdf8;
    font-family: 'Courier New', Courier, monospace;
    font-size: 26px;
    font-weight: 700;
    padding: 6px 16px;
    border-radius: 6px;
    letter-spacing: 2px;
    display: inline-block;
}
.wrapup-bar-container {
    height: 8px;
    background: #e2e8f0;
    border-radius: 4px;
    overflow: hidden;
    margin-top: 10px;
    margin-bottom: 6px;
}
.wrapup-bar {
    height: 100%;
    width: 100%;
    background: #f59e0b;
    transition: width 1s linear;
}
.queue-item {
    padding: 10px 14px;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    transition: background 0.15s;
}
.queue-item:hover {
    background: #f8fafc;
}
.queue-item.active {
    background: #eff6ff;
    border-left: 4px solid #2563eb;
}
.queue-item-name {
    font-weight: 600;
    color: #1e293b;
    font-size: 13px;
}
.queue-item-meta {
    font-size: 11.5px;
    color: #64748b;
    margin-top: 3px;
}
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 22px;
    vertical-align: middle;
}
.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #cbd5e1;
    transition: .3s;
    border-radius: 22px;
}
.toggle-slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
}
input:checked + .toggle-slider {
    background-color: #2563eb;
}
input:checked + .toggle-slider:before {
    transform: translateX(22px);
}
</style>

<div class="content-wrapper px-4">
    <section class="content-header">
        <h1>
            <i class="fa fa-phone-square text-primary"></i> Calling Dashboard
            <small>Operations Client Call Center</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="javascript:void(0);">Operations</a></li>
            <li class="active">Calls</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-8 col-sm-12">
                <div class="call-card">
                    <div class="call-card-header">
                        <div>
                            <span class="call-card-title" id="dispCustomerName">
                                <?php echo !empty($active_lead) ? htmlspecialchars($active_lead['customer']) : 'No Lead Selected'; ?>
                            </span>
                            <span class="badge" style="margin-left: 8px; background: #64748b;" id="dispLeadId">
                                <?php echo !empty($active_lead) ? '#' . htmlspecialchars($active_lead['lead_id']) : ''; ?>
                            </span>
                        </div>
                        <div>
                            <span id="callStatusBadge" class="call-badge call-badge-idle">
                                <i class="fa fa-circle"></i> Ready
                            </span>
                        </div>
                    </div>
                    <div class="box-body" style="padding: 20px;">
                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col-sm-6">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div class="call-timer-box" id="callDuration">00:00</div>
                                    <div>
                                        <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 600;">Call Duration</div>
                                        <div style="font-size: 12px; color: #334155;" id="callStatusText">Idle</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 text-right">
                                <div id="callControls">
                                    <button type="button" id="btnStartCall" class="call-btn-dial">
                                        <i class="fa fa-phone"></i> Start Call
                                    </button>
                                    <button type="button" id="btnEndCall" class="call-btn-hangup" style="display: none;">
                                        <i class="fa fa-phone"></i> End Call
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="wrapupNotice" style="display: none; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 6px; padding: 12px 16px; margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <strong style="color: #92400e;"><i class="fa fa-hourglass-half"></i> Post-Call Wrap-up Time:</strong>
                                    <span style="color: #b45309; font-weight: 700; margin-left: 6px;" id="wrapupCountdown">60s remaining</span>
                                </div>
                                <div>
                                    <button type="button" id="btnSkipTimer" class="btn btn-default btn-xs">
                                        <i class="fa fa-forward"></i> Skip Timer
                                    </button>
                                </div>
                            </div>
                            <div class="wrapup-bar-container">
                                <div class="wrapup-bar" id="wrapupProgress"></div>
                            </div>
                            <div style="font-size: 11.5px; color: #78350f;">
                                Please enter your call remarks and notes below. Next call will load when saved or timer expires.
                            </div>
                        </div>

                        <div class="panel panel-default" style="border-radius: 6px; border: 1px solid #e2e8f0;">
                            <div class="panel-heading" style="background: #f8fafc; font-weight: 600; font-size: 13px; color: #334155;">
                                <i class="fa fa-user"></i> Client Information
                            </div>
                            <div class="panel-body" style="padding: 15px;">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p style="margin-bottom: 8px;">
                                            <span style="color: #64748b; font-size: 12px; display: block;">Primary Contact (POC)</span>
                                            <strong style="font-size: 14px; color: #0f172a;" id="dispPoc">
                                                <?php echo !empty($active_lead['poc']) ? htmlspecialchars($active_lead['poc']) : '—'; ?>
                                            </strong>
                                        </p>
                                        <p style="margin-bottom: 8px;">
                                            <span style="color: #64748b; font-size: 12px; display: block;">Phone Number</span>
                                            <span style="font-size: 15px; font-weight: 600; color: #2563eb;" id="dispPhone">
                                                <?php echo !empty($active_lead['contact1']) ? htmlspecialchars($active_lead['contact1']) : '—'; ?>
                                            </span>
                                            <?php if (!empty($active_lead['contact1'])): ?>
                                                <a id="linkTel" href="tel:<?php echo htmlspecialchars($active_lead['contact1']); ?>" class="btn btn-default btn-xs" style="margin-left: 8px;">
                                                    <i class="fa fa-external-link"></i> Dial
                                                </a>
                                            <?php endif; ?>
                                        </p>
                                        <p style="margin-bottom: 8px;">
                                            <span style="color: #64748b; font-size: 12px; display: block;">Alt Phone Number</span>
                                            <span style="font-size: 13px; color: #334155;" id="dispAltPhone">
                                                <?php echo !empty($active_lead['alt_contact']) ? htmlspecialchars($active_lead['alt_contact']) : '—'; ?>
                                            </span>
                                        </p>
                                        <p style="margin-bottom: 8px;">
                                            <span style="color: #64748b; font-size: 12px; display: block;">Email</span>
                                            <span style="font-size: 13px; color: #334155;" id="dispEmail">
                                                <?php echo !empty($active_lead['email']) ? htmlspecialchars($active_lead['email']) : '—'; ?>
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p style="margin-bottom: 8px;">
                                            <span style="color: #64748b; font-size: 12px; display: block;">Trade License Emirates & Zone</span>
                                            <span style="font-size: 13px; color: #334155;" id="dispEmirateZone">
                                                <?php 
                                                    $ez = array_filter(array(!empty($active_lead['tl_emirate']) ? $active_lead['tl_emirate'] : '', !empty($active_lead['tl_zone']) ? $active_lead['tl_zone'] : ''));
                                                    echo !empty($ez) ? htmlspecialchars(implode(' / ', $ez)) : '—';
                                                ?>
                                            </span>
                                        </p>
                                        <p style="margin-bottom: 8px;">
                                            <span style="color: #64748b; font-size: 12px; display: block;">Authority / License #</span>
                                            <span style="font-size: 13px; color: #334155;" id="dispAuthLicense">
                                                <?php 
                                                    $al = array_filter(array(!empty($active_lead['tl_authority']) ? $active_lead['tl_authority'] : '', !empty($active_lead['tl_number']) ? $active_lead['tl_number'] : ''));
                                                    echo !empty($al) ? htmlspecialchars(implode(' - ', $al)) : '—';
                                                ?>
                                            </span>
                                        </p>
                                        <p style="margin-bottom: 8px;">
                                            <span style="color: #64748b; font-size: 12px; display: block;">Current Status</span>
                                            <span class="label label-info" id="dispCurrentStatus">
                                                <?php echo !empty($active_lead['status_name']) ? htmlspecialchars($active_lead['status_name']) : 'New'; ?>
                                            </span>
                                        </p>
                                        <p style="margin-bottom: 8px;">
                                            <span style="color: #64748b; font-size: 12px; display: block;">Revenue / MRR</span>
                                            <span style="font-size: 13px; color: #166534; font-weight: 600;" id="dispRevenue">
                                                AED <?php echo !empty($active_lead['r_amount']) ? number_format((float)$active_lead['r_amount'], 2) : '0.00'; ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default" style="border-radius: 6px; border: 1px solid #e2e8f0;">
                            <div class="panel-heading" style="background: #f8fafc; font-weight: 600; font-size: 13px; color: #334155;">
                                <i class="fa fa-pencil-square-o"></i> Post-Call Remarks & Disposition
                            </div>
                            <div class="panel-body" style="padding: 15px;">
                                <form id="callRemarksForm">
                                    <input type="hidden" id="formLeadId" name="lead_id" value="<?php echo !empty($active_lead) ? $active_lead['id'] : 0; ?>">
                                    <input type="hidden" id="formCallDuration" name="call_duration" value="0">

                                    <div class="row">
                                        <div class="col-sm-6 form-group">
                                            <label style="font-size: 12px; color: #334155;">Call Disposition <span class="text-danger">*</span></label>
                                            <select class="form-control input-sm" id="callDisposition" name="call_disposition" required>
                                                <option value="Connected">Connected / Answered</option>
                                                <option value="Not Answered">No Answer / Ringing</option>
                                                <option value="Busy">Busy / Call Waiting</option>
                                                <option value="Follow-up Required">Follow-up Required</option>
                                                <option value="Meeting Scheduled">Meeting Scheduled</option>
                                                <option value="Wrong Number">Wrong / Invalid Number</option>
                                                <option value="Not Interested">Not Interested / Closed</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <label style="font-size: 12px; color: #334155;">Update Lead Status</label>
                                            <select class="form-control input-sm" id="leadStatus" name="status">
                                                <?php foreach ($statuses as $st): ?>
                                                    <option value="<?php echo $st['id']; ?>" <?php echo (!empty($active_lead) && $active_lead['status'] == $st['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($st['status']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6 form-group">
                                            <label style="font-size: 12px; color: #334155;">Next Follow-up Date (Optional)</label>
                                            <input type="date" class="form-control input-sm" id="nextFollowup" name="next_followup">
                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <label style="font-size: 12px; color: #334155;">Follow-up Time</label>
                                            <input type="time" class="form-control input-sm" id="nextFollowupTime" name="next_followup_time">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label style="font-size: 12px; color: #334155;">Call Notes & Remarks <span class="text-danger">*</span></label>
                                        <textarea class="form-control" rows="3" id="callRemarks" name="remarks" placeholder="Enter conversation details, client feedback, requested documents..." required></textarea>
                                    </div>

                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                                        <div>
                                            <button type="submit" id="btnSaveRemarks" class="btn btn-primary btn-sm" style="font-weight: 600;">
                                                <i class="fa fa-check"></i> Save Remarks & Load Next
                                            </button>
                                            <button type="button" id="btnOnlySave" class="btn btn-default btn-sm" style="margin-left: 6px;">
                                                <i class="fa fa-save"></i> Save Only
                                            </button>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <label class="toggle-switch">
                                                <input type="checkbox" id="autoStartToggle">
                                                <span class="toggle-slider"></span>
                                            </label>
                                            <span style="font-size: 12px; color: #475569; font-weight: 600;">Auto-start next call</span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-12">
                <div class="call-card">
                    <div class="call-card-header">
                        <span class="call-card-title">
                            <i class="fa fa-list-ol text-primary"></i> Call Queue (<?php echo $total_queue; ?>)
                        </span>
                        <div>
                            <input type="text" id="queueSearch" placeholder="Filter..." class="form-control input-sm" style="width: 120px; display: inline-block;">
                        </div>
                    </div>
                    <div class="box-body" style="padding: 0; max-height: 600px; overflow-y: auto;" id="queueList">
                        <?php if (!empty($queue)): ?>
                            <?php foreach ($queue as $idx => $lead): ?>
                                <div class="queue-item <?php echo ($idx === 0) ? 'active' : ''; ?>" data-lead-id="<?php echo $lead['id']; ?>" data-index="<?php echo $idx; ?>">
                                    <div class="queue-item-name">
                                        <?php echo htmlspecialchars($lead['customer']); ?>
                                        <span class="pull-right badge" style="background: #e2e8f0; color: #334155; font-size: 10.5px;">
                                            #<?php echo htmlspecialchars($lead['lead_id']); ?>
                                        </span>
                                    </div>
                                    <div class="queue-item-meta">
                                        <i class="fa fa-user"></i> <?php echo !empty($lead['poc']) ? htmlspecialchars($lead['poc']) : '—'; ?> &nbsp;|&nbsp; 
                                        <i class="fa fa-phone"></i> <?php echo !empty($lead['contact1']) ? htmlspecialchars($lead['contact1']) : (!empty($lead['alt_contact']) ? htmlspecialchars($lead['alt_contact']) : 'No phone'); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="padding: 20px; text-align: center; color: #94a3b8;">
                                <i class="fa fa-info-circle fa-2x"></i>
                                <p style="margin-top: 8px;">No calls in queue</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    var callState = 'IDLE';
    var callSeconds = 0;
    var callInterval = null;
    var wrapupSeconds = 60;
    var wrapupInterval = null;
    var currentQueueIndex = 0;

    function formatTime(sec) {
        var m = Math.floor(sec / 60);
        var s = sec % 60;
        return (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
    }

    function setCallState(state) {
        callState = state;
        var badge = $('#callStatusBadge');
        var statusText = $('#callStatusText');

        if (state === 'IN_CALL') {
            badge.removeClass('call-badge-idle call-badge-wrapup').addClass('call-badge-active').html('<i class="fa fa-circle"></i> On Call');
            statusText.text('Active conversation');
            $('#btnStartCall').hide();
            $('#btnEndCall').show();
            $('#wrapupNotice').hide();
        } else if (state === 'WRAPUP') {
            badge.removeClass('call-badge-idle call-badge-active').addClass('call-badge-wrapup').html('<i class="fa fa-hourglass-half"></i> Wrap-up');
            statusText.text('Enter notes / remarks');
            $('#btnStartCall').show();
            $('#btnEndCall').hide();
            $('#wrapupNotice').slideDown();
        } else {
            badge.removeClass('call-badge-active call-badge-wrapup').addClass('call-badge-idle').html('<i class="fa fa-circle"></i> Ready');
            statusText.text('Idle');
            $('#btnStartCall').show();
            $('#btnEndCall').hide();
            $('#wrapupNotice').hide();
        }
    }

    function startCallTimer() {
        clearInterval(callInterval);
        clearInterval(wrapupInterval);
        callSeconds = 0;
        $('#callDuration').text('00:00');
        setCallState('IN_CALL');

        callInterval = setInterval(function() {
            callSeconds++;
            $('#callDuration').text(formatTime(callSeconds));
            $('#formCallDuration').val(callSeconds);
        }, 1000);
    }

    function endCall() {
        clearInterval(callInterval);
        setCallState('WRAPUP');
        startWrapupTimer();
    }

    function startWrapupTimer() {
        clearInterval(wrapupInterval);
        wrapupSeconds = 60;
        $('#wrapupCountdown').text(wrapupSeconds + 's remaining');
        $('#wrapupProgress').css('width', '100%');

        wrapupInterval = setInterval(function() {
            wrapupSeconds--;
            if (wrapupSeconds <= 0) {
                clearInterval(wrapupInterval);
                $('#wrapupCountdown').text('Time up');
                $('#wrapupProgress').css('width', '0%');
                if ($('#autoStartToggle').is(':checked')) {
                    saveRemarksAndNext(true);
                }
            } else {
                $('#wrapupCountdown').text(wrapupSeconds + 's remaining');
                var pct = (wrapupSeconds / 60) * 100;
                $('#wrapupProgress').css('width', pct + '%');
            }
        }, 1000);
    }

    $('#btnStartCall').click(function() {
        startCallTimer();
    });

    $('#btnEndCall').click(function() {
        endCall();
    });

    $('#btnSkipTimer').click(function() {
        clearInterval(wrapupInterval);
        $('#wrapupNotice').slideUp();
        setCallState('IDLE');
    });

    $('.queue-item').click(function() {
        var leadId = $(this).data('lead-id');
        var idx = $(this).data('index');
        currentQueueIndex = idx;
        $('.queue-item').removeClass('active');
        $(this).addClass('active');
        loadLeadDetails(leadId);
    });

    function loadLeadDetails(leadId) {
        $.ajax({
            url: '<?php echo base_url(); ?>manager/calls/ajax_get_lead/' + leadId,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.status && res.lead) {
                    var l = res.lead;
                    $('#dispCustomerName').text(l.customer || '—');
                    $('#dispLeadId').text('#' + (l.lead_id || l.id));
                    $('#dispPoc').text(l.poc || '—');
                    $('#dispPhone').text(l.contact1 || '—');
                    if (l.contact1) {
                        $('#linkTel').attr('href', 'tel:' + l.contact1).show();
                    } else {
                        $('#linkTel').hide();
                    }
                    $('#dispAltPhone').text(l.alt_contact || '—');
                    $('#dispEmail').text(l.email || '—');
                    var ez = [l.tl_emirate, l.tl_zone].filter(Boolean).join(' / ');
                    $('#dispEmirateZone').text(ez || '—');
                    var al = [l.tl_authority, l.tl_number].filter(Boolean).join(' - ');
                    $('#dispAuthLicense').text(al || '—');
                    $('#dispCurrentStatus').text(l.status_name || 'New');
                    var rev = parseFloat(l.r_amount || 0).toFixed(2);
                    $('#dispRevenue').text('AED ' + rev);
                    $('#formLeadId').val(l.id);
                    if (l.status) {
                        $('#leadStatus').val(l.status);
                    }
                    clearInterval(callInterval);
                    clearInterval(wrapupInterval);
                    callSeconds = 0;
                    $('#callDuration').text('00:00');
                    setCallState('IDLE');
                }
            }
        });
    }

    function saveRemarksAndNext(autoDialNext) {
        var formData = $('#callRemarksForm').serialize();
        $.ajax({
            url: '<?php echo base_url(); ?>manager/calls/ajax_save_call',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                clearInterval(wrapupInterval);
                $('#callRemarks').val('');
                $('#wrapupNotice').hide();
                setCallState('IDLE');

                var nextItem = $('.queue-item[data-index="' + (currentQueueIndex + 1) + '"]');
                if (nextItem.length) {
                    nextItem.trigger('click');
                    if (autoDialNext || $('#autoStartToggle').is(':checked')) {
                        setTimeout(function() {
                            startCallTimer();
                        }, 500);
                    }
                } else {
                    alert('Queue completed or no more leads in list.');
                }
            },
            error: function() {
                var nextItem = $('.queue-item[data-index="' + (currentQueueIndex + 1) + '"]');
                if (nextItem.length) {
                    nextItem.trigger('click');
                }
            }
        });
    }

    $('#callRemarksForm').on('submit', function(e) {
        e.preventDefault();
        saveRemarksAndNext(false);
    });

    $('#btnOnlySave').click(function() {
        var formData = $('#callRemarksForm').serialize();
        $.ajax({
            url: '<?php echo base_url(); ?>manager/calls/ajax_save_call',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                alert('Call remarks saved successfully.');
                clearInterval(wrapupInterval);
                $('#wrapupNotice').hide();
                setCallState('IDLE');
            }
        });
    });

    $('#queueSearch').on('keyup', function() {
        var q = $(this).val().toLowerCase();
        $('.queue-item').each(function() {
            var txt = $(this).text().toLowerCase();
            if (txt.indexOf(q) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
