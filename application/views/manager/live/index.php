<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<div class="content-wrapper" style="background:#f8fafc; min-height:940px; padding-bottom:40px;">
    <section class="content-header" style="padding:20px 24px 12px 24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
            <div>
                <div style="display:flex; align-items:center; gap:10px;">
                    <span class="live-pulse-dot"></span>
                    <h1 style="font-size:22px; font-weight:800; color:#0f172a; margin:0; letter-spacing:-0.02em;">
                        LIVE <span style="color:#00c6f0;">DASHBOARD</span>
                    </h1>
                </div>
                <div style="font-size:12px; color:#64748b; margin-top:4px;">
                    Last updated: <span id="liveLastSync" style="font-weight:700; color:#0f172a;">--:--:--</span>
                </div>
            </div>
            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <div class="btn-group" role="group" id="liveScopeGroup" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:3px; box-shadow:0 1px 2px rgba(0,0,0,0.03);">
                    <button type="button" class="btn btn-xs live-scope-btn active" data-scope="my" style="border-radius:6px; font-weight:700; font-size:11px; padding:6px 14px; border:none; background:#0f172a; color:#ffffff; transition:all 0.15s ease;">My Activity</button>
                    <?php if(!empty($has_team)){ ?>
                    <button type="button" class="btn btn-xs live-scope-btn" data-scope="team" style="border-radius:6px; font-weight:700; font-size:11px; padding:6px 14px; border:none; background:transparent; color:#64748b; transition:all 0.15s ease;">Team Activity</button>
                    <?php } ?>
                </div>

                <div class="btn-group" role="group" id="liveTimeframeGroup" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:3px; box-shadow:0 1px 2px rgba(0,0,0,0.03);">
                    <button type="button" class="btn btn-xs live-tf-btn active" data-tf="today" style="border-radius:6px; font-weight:700; font-size:11px; padding:6px 12px; border:none; background:#00c6f0; color:#ffffff; transition:all 0.15s ease;">Today</button>
                    <button type="button" class="btn btn-xs live-tf-btn" data-tf="last7" style="border-radius:6px; font-weight:700; font-size:11px; padding:6px 12px; border:none; background:transparent; color:#475569; transition:all 0.15s ease;">Last 7 Days</button>
                    <button type="button" class="btn btn-xs live-tf-btn" data-tf="month" style="border-radius:6px; font-weight:700; font-size:11px; padding:6px 12px; border:none; background:transparent; color:#475569; transition:all 0.15s ease;">Month</button>
                    <button type="button" class="btn btn-xs live-tf-btn" data-tf="quarter" style="border-radius:6px; font-weight:700; font-size:11px; padding:6px 12px; border:none; background:transparent; color:#475569; transition:all 0.15s ease;">Quarter</button>
                </div>

                <button type="button" id="btnLiveRefreshNow" class="btn btn-default btn-xs" style="border-radius:8px; border:1px solid #e2e8f0; background:#ffffff; font-weight:700; color:#0f172a; padding:6px 14px;" title="Refresh now">
                    <i class="fa fa-refresh" id="liveRefreshIcon" style="color:#00c6f0; margin-right:4px;"></i> Refresh
                </button>
            </div>
        </div>
    </section>

    <section class="content" style="padding:10px 24px 24px 24px;">
        <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:24px 28px; margin-bottom:22px; box-shadow:0 1px 3px rgba(0,0,0,0.02);">
            <div class="row" style="margin:0;">
                <div class="col-md-4 col-xs-12" style="padding:0 24px 0 0; border-right:1px solid #f1f5f9;">
                    <div style="font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:0.04em; color:#64748b;">Calls</div>
                    <div style="display:flex; align-items:baseline; gap:8px; margin:10px 0 12px 0;">
                        <span id="liveCallsTotal" style="font-size:36px; font-weight:800; color:#0f172a; line-height:1; letter-spacing:-0.03em;">0</span>
                        <span style="font-size:13px; font-weight:600; color:#64748b;">Total</span>
                    </div>
                    <div style="height:6px; background:#f1f5f9; border-radius:3px; display:flex; overflow:hidden; margin:14px 0 12px 0;">
                        <div id="liveBarConnected" style="background:#00c6f0; width:0%; transition:width 0.4s ease;"></div>
                        <div id="liveBarInProcess" style="background:#38bdf8; width:0%; transition:width 0.4s ease;"></div>
                        <div id="liveBarOther" style="background:#cbd5e1; width:0%; transition:width 0.4s ease;"></div>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center; font-size:12px; margin-top:14px;">
                        <div>
                            <span style="display:inline-block; width:8px; height:8px; border-radius:2px; background:#00c6f0; margin-right:4px;"></span>
                            <span style="color:#64748b;">Connected:</span> <strong id="liveCallsConnected" style="color:#0f172a;">0</strong> <span style="color:#00a2d6; font-weight:700;">(<span id="liveCallsRate">0%</span>)</span>
                        </div>
                        <div>
                            <span style="display:inline-block; width:8px; height:8px; border-radius:2px; background:#38bdf8; margin-right:4px;"></span>
                            <span style="color:#64748b;">In Process:</span> <strong id="liveCallsInProcess" style="color:#0f172a;">0</strong>
                        </div>
                        <div>
                            <span style="display:inline-block; width:8px; height:8px; border-radius:2px; background:#cbd5e1; margin-right:4px;"></span>
                            <span style="color:#64748b;">Other:</span> <strong id="liveCallsOther" style="color:#64748b;">0</strong>
                        </div>
                    </div>
                </div>

                <div class="col-md-5 col-xs-12" style="padding:0 24px; border-right:1px solid #f1f5f9;">
                    <div style="font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:0.04em; color:#64748b;">Pipeline</div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin:10px 0 12px 0;">
                        <div>
                            <div style="font-size:26px; font-weight:800; color:#0f172a; line-height:1;" id="liveLeadsTotal">0</div>
                            <div style="font-size:12px; font-weight:600; color:#00a2d6; margin-top:4px;"><span id="liveLeadsQualified">0</span> Leads</div>
                        </div>
                        <div style="color:#cbd5e1; font-size:20px; font-weight:300;">&rarr;</div>
                        <div>
                            <div style="font-size:26px; font-weight:800; color:#0f172a; line-height:1;" id="liveOrdersTotal">0</div>
                            <div style="font-size:12px; font-weight:600; color:#475569; margin-top:4px;"><span id="liveOrdersPending">0</span> Pending</div>
                        </div>
                        <div style="color:#cbd5e1; font-size:20px; font-weight:300;">&rarr;</div>
                        <div>
                            <div style="font-size:26px; font-weight:800; color:#0f172a; line-height:1;" id="liveSalesCount">0</div>
                            <div style="font-size:12px; font-weight:600; color:#16a34a; margin-top:4px;">Sales</div>
                        </div>
                    </div>
                    <div style="margin-top:16px; padding-top:12px; border-top:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; font-size:11px; color:#64748b;">
                        <span>Lead Rate: <strong id="liveLeadsRate" style="color:#0f172a; font-weight:700;">0.0%</strong></span>
                        <span>Order Rate: <strong id="liveOrdersRate" style="color:#0f172a; font-weight:700;">0.0%</strong></span>
                    </div>
                </div>

                <div class="col-md-3 col-xs-12" style="padding:0 0 0 24px;">
                    <div style="font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:0.04em; color:#64748b;">Sales Achieved</div>
                    <div id="liveSalesRevenue" style="font-size:30px; font-weight:800; color:#0f172a; letter-spacing:-0.03em; margin:10px 0 6px 0; line-height:1;">AED 0.00</div>
                    <div style="font-size:12px; color:#64748b; margin-top:6px;">
                        Avg Deal: <strong id="liveSalesAvg" style="color:#00a2d6;">AED 0.00</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-xs-12" style="margin-bottom:20px;">
                <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:22px 24px; box-shadow:0 1px 3px rgba(0,0,0,0.02); height:100%;">
                    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #f1f5f9; padding-bottom:14px; margin-bottom:16px;">
                        <div style="font-size:13px; font-weight:800; color:#0f172a; text-transform:uppercase; letter-spacing:0.04em;">Call Status</div>
                        <span id="liveCallBadge" style="font-size:11px; font-weight:700; color:#00c6f0; background:rgba(0,198,240,0.08); padding:3px 10px; border-radius:4px;">0 Calls</span>
                    </div>

                    <div style="display:flex; align-items:center; gap:24px; padding:6px 0 16px 0; border-bottom:1px solid #f1f5f9;">
                        <div style="position:relative; width:110px; height:110px; flex-shrink:0;">
                            <canvas id="liveCallsDonutChart" width="110" height="110" style="width:110px; height:110px;"></canvas>
                        </div>
                        <div style="flex:1;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; font-size:12px;">
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <span style="width:10px; height:10px; border-radius:2px; background:#00c6f0; display:inline-block;"></span>
                                    <span style="font-weight:600; color:#0f172a;">Connected</span>
                                </div>
                                <span id="donutConnectedVal" style="font-weight:700; color:#00a2d6;">0</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; font-size:12px;">
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <span style="width:10px; height:10px; border-radius:2px; background:#38bdf8; display:inline-block;"></span>
                                    <span style="font-weight:600; color:#0f172a;">In Process</span>
                                </div>
                                <span id="donutInProcessVal" style="font-weight:700; color:#475569;">0</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; align-items:center; font-size:12px;">
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <span style="width:10px; height:10px; border-radius:2px; background:#cbd5e1; display:inline-block;"></span>
                                    <span style="font-weight:600; color:#0f172a;">Other</span>
                                </div>
                                <span id="donutOtherVal" style="font-weight:700; color:#64748b;">0</span>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top:16px;">
                        <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#64748b; margin-bottom:10px;">Recent Calls</div>
                        <div class="table-responsive" style="margin:0;">
                            <table class="table" style="margin-bottom:0; font-size:12px;">
                                <thead>
                                    <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0; color:#475569; font-size:11px; text-transform:uppercase;">
                                        <th style="padding:8px 12px; font-weight:700;">Customer</th>
                                        <th style="padding:8px 12px; font-weight:700;">Phone</th>
                                        <th style="padding:8px 12px; font-weight:700;">Agent</th>
                                        <th style="padding:8px 12px; font-weight:700;">Status</th>
                                        <th style="padding:8px 12px; font-weight:700; text-align:right;">Duration</th>
                                    </tr>
                                </thead>
                                <tbody id="liveRecentCallsTbody">
                                    <tr>
                                        <td colspan="5" style="text-align:center; padding:24px 0; color:#94a3b8;">No recent calls</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xs-12" style="margin-bottom:20px;">
                <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:22px 24px; box-shadow:0 1px 3px rgba(0,0,0,0.02); height:100%;">
                    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #f1f5f9; padding-bottom:14px; margin-bottom:16px;">
                        <div style="font-size:13px; font-weight:800; color:#0f172a; text-transform:uppercase; letter-spacing:0.04em;">Lead Stages</div>
                        <span id="liveLeadBadge" style="font-size:11px; font-weight:700; color:#00c6f0; background:rgba(0,198,240,0.08); padding:3px 10px; border-radius:4px;">0 Leads</span>
                    </div>

                    <div style="padding:6px 0 16px 0; border-bottom:1px solid #f1f5f9;">
                        <div id="liveLeadStagesList" style="display:flex; flex-wrap:wrap; gap:8px;">
                            <span style="color:#94a3b8; font-size:12px;">Loading stages...</span>
                        </div>
                    </div>

                    <div style="margin-top:16px;">
                        <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#64748b; margin-bottom:10px;">Recent Leads</div>
                        <div class="table-responsive" style="margin:0;">
                            <table class="table" style="margin-bottom:0; font-size:12px;">
                                <thead>
                                    <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0; color:#475569; font-size:11px; text-transform:uppercase;">
                                        <th style="padding:8px 12px; font-weight:700;">Customer</th>
                                        <th style="padding:8px 12px; font-weight:700;">POC</th>
                                        <th style="padding:8px 12px; font-weight:700;">Stage</th>
                                        <th style="padding:8px 12px; font-weight:700;">Status</th>
                                        <th style="padding:8px 12px; font-weight:700; text-align:right;">Date</th>
                                    </tr>
                                </thead>
                                <tbody id="liveRecentLeadsTbody">
                                    <tr>
                                        <td colspan="5" style="text-align:center; padding:24px 0; color:#94a3b8;">No recent leads</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:22px 28px; box-shadow:0 1px 3px rgba(0,0,0,0.02);">
            <div style="font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; color:#0f172a; margin-bottom:18px; border-bottom:1px solid #f1f5f9; padding-bottom:10px;">
                Activity
            </div>
            <div class="row" style="margin:0;">
                <div class="col-md-3 col-sm-6 col-xs-12" style="padding:0 20px 0 0; border-right:1px solid #f1f5f9;">
                    <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:#64748b;">Follow-ups</div>
                    <div style="font-size:26px; font-weight:800; color:#0f172a; margin:6px 0 4px 0;" id="liveFollowupsTotal">0</div>
                    <div style="font-size:12px; color:#64748b;">
                        <span id="liveFollowupsToday" style="font-weight:700; color:#00a2d6;">0 Today</span> &bull; <span id="liveFollowupsMissed" style="font-weight:700; color:#dc2626;">0 Missed</span>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 col-xs-12" style="padding:0 20px; border-right:1px solid #f1f5f9;">
                    <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:#64748b;">Meetings</div>
                    <div style="font-size:26px; font-weight:800; color:#0f172a; margin:6px 0 4px 0;" id="liveMeetingsTotal">0</div>
                    <div style="font-size:12px; color:#64748b;">
                        Scheduled
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 col-xs-12" style="padding:0 20px; border-right:1px solid #f1f5f9;">
                    <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:#64748b;">Tickets</div>
                    <div style="font-size:26px; font-weight:800; color:#0f172a; margin:6px 0 4px 0;" id="liveTicketsTotal">0</div>
                    <div style="font-size:12px; color:#64748b;">
                        <span id="liveTicketsOpen" style="font-weight:700; color:#00a2d6;">0 Open</span> &bull; <span id="liveTicketsResolved" style="font-weight:700; color:#16a34a;">0 Resolved</span>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 col-xs-12" style="padding:0 0 0 20px;">
                    <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:#64748b;">Verticals</div>
                    <div style="font-size:26px; font-weight:800; color:#0f172a; margin:6px 0 4px 0;" id="liveArticlesTotal">0</div>
                    <div style="font-size:12px; color:#64748b;">
                        Active Catalog
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
@keyframes livePulseAura {
    0% { box-shadow: 0 0 0 0 rgba(0, 198, 240, 0.6); }
    70% { box-shadow: 0 0 0 10px rgba(0, 198, 240, 0); }
    100% { box-shadow: 0 0 0 0 rgba(0, 198, 240, 0); }
}
.live-pulse-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #00c6f0;
    display: inline-block;
    animation: livePulseAura 2s infinite ease-in-out;
}
.live-stream-row {
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s ease;
}
.live-stream-row:hover {
    background: #f8fafc;
}
</style>

<script>
$(document).ready(function() {
    var currentTimeframe = 'today';
    var currentScope = 'my';
    var refreshTimer = null;
    var callsDonutInstance = null;

    function renderCallsDonut(connected, inProcess, other) {
        var cv = document.getElementById('liveCallsDonutChart');
        if (!cv) return;
        var ctx = cv.getContext('2d');
        var c = parseInt(connected) || 0;
        var p = parseInt(inProcess) || 0;
        var o = parseInt(other) || 0;
        var data = [];

        if (c === 0 && p === 0 && o === 0) {
            data = [{ value: 1, color: '#e2e8f0', label: 'No Calls' }];
        } else {
            if (c > 0) data.push({ value: c, color: '#00c6f0', label: 'Connected' });
            if (p > 0) data.push({ value: p, color: '#38bdf8', label: 'In Process' });
            if (o > 0) data.push({ value: o, color: '#cbd5e1', label: 'Other' });
        }

        if (callsDonutInstance) {
            try { callsDonutInstance.destroy(); } catch(e) {}
        }

        callsDonutInstance = new Chart(ctx).Doughnut(data, {
            percentageInnerCutout: 68,
            segmentShowStroke: true,
            segmentStrokeColor: '#ffffff',
            segmentStrokeWidth: 2,
            animationSteps: 25,
            animationEasing: 'easeOutQuart',
            animateRotate: true,
            responsive: false,
            maintainAspectRatio: true,
            showTooltips: true
        });
    }

    function fetchLiveData() {
        $('#liveRefreshIcon').addClass('fa-spin');
        $.ajax({
            url: '<?php echo base_url(); ?>manager/live/ajax_get_data',
            type: 'POST',
            data: {
                timeframe: currentTimeframe,
                scope: currentScope
            },
            dataType: 'json',
            success: function(resp) {
                $('#liveRefreshIcon').removeClass('fa-spin');
                if (resp && resp.status === 'success') {
                    $('#liveLastSync').text(resp.timestamp);

                    $('#liveCallsTotal').text(resp.calls.total);
                    $('#liveCallsConnected').text(resp.calls.connected);
                    $('#liveCallsInProcess').text(resp.calls.in_process);
                    $('#liveCallsOther').text(resp.calls.other);
                    $('#liveCallBadge').text(resp.calls.total + ' Calls');

                    var callTot = resp.calls.total > 0 ? resp.calls.total : 1;
                    var callRate = resp.calls.total > 0 ? ((resp.calls.connected / resp.calls.total) * 100).toFixed(1) : '0.0';
                    $('#liveCallsRate').text(callRate + '%');

                    $('#liveBarConnected').css('width', Math.round((resp.calls.connected / callTot) * 100) + '%');
                    $('#liveBarInProcess').css('width', Math.round((resp.calls.in_process / callTot) * 100) + '%');
                    $('#liveBarOther').css('width', Math.round((resp.calls.other / callTot) * 100) + '%');

                    $('#donutConnectedVal').text(resp.calls.connected);
                    $('#donutInProcessVal').text(resp.calls.in_process);
                    $('#donutOtherVal').text(resp.calls.other);

                    renderCallsDonut(resp.calls.connected, resp.calls.in_process, resp.calls.other);

                    var callsHtml = '';
                    if (resp.calls.latest && resp.calls.latest.length > 0) {
                        for (var i = 0; i < resp.calls.latest.length; i++) {
                            var cl = resp.calls.latest[i];
                            var stColor = cl.status === 'connected' ? '#00c6f0' : (cl.status === 'in_process' ? '#38bdf8' : '#64748b');
                            callsHtml += '<tr class="live-stream-row">' +
                                '<td style="padding:8px 12px; font-weight:700; color:#0f172a;">' + (cl.customer_name ? cl.customer_name : 'Unknown') + '</td>' +
                                '<td style="padding:8px 12px; color:#475569;">' + (cl.phone_number ? cl.phone_number : '-') + '</td>' +
                                '<td style="padding:8px 12px; color:#64748b;">' + (cl.agent_name ? cl.agent_name : '-') + '</td>' +
                                '<td style="padding:8px 12px; font-weight:700; color:' + stColor + '; text-transform:capitalize;">' + cl.status + '</td>' +
                                '<td style="padding:8px 12px; text-align:right; font-weight:600; color:#0f172a;">' + (cl.duration ? cl.duration + 's' : '0s') + '</td>' +
                            '</tr>';
                        }
                    } else {
                        callsHtml = '<tr><td colspan="5" style="text-align:center; padding:24px 0; color:#94a3b8;">No recent calls</td></tr>';
                    }
                    $('#liveRecentCallsTbody').html(callsHtml);

                    $('#liveLeadsTotal').text(resp.leads.total);
                    $('#liveLeadsQualified').text(resp.leads.qualified);
                    $('#liveLeadBadge').text(resp.leads.total + ' Leads');
                    var leadRate = resp.leads.total > 0 ? ((resp.leads.qualified / resp.leads.total) * 100).toFixed(1) : '0.0';
                    $('#liveLeadsRate').text(leadRate + '%');

                    var stagesHtml = '';
                    if (resp.leads.statuses && resp.leads.statuses.length > 0) {
                        for (var j = 0; j < resp.leads.statuses.length; j++) {
                            var st = resp.leads.statuses[j];
                            stagesHtml += '<span style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:4px 10px; font-size:11px; font-weight:600; color:#334155;">' +
                                st.name + ': <strong style="color:#00a2d6;">' + st.count + '</strong></span>';
                        }
                    } else {
                        stagesHtml = '<span style="color:#94a3b8; font-size:12px;">No active stages</span>';
                    }
                    $('#liveLeadStagesList').html(stagesHtml);

                    var leadsHtml = '';
                    if (resp.leads.latest && resp.leads.latest.length > 0) {
                        for (var k = 0; k < resp.leads.latest.length; k++) {
                            var ld = resp.leads.latest[k];
                            var stName = ld.status_name ? ld.status_name : (ld.status ? ld.status : 'New');
                            leadsHtml += '<tr class="live-stream-row">' +
                                '<td style="padding:8px 12px; font-weight:700; color:#0f172a;">' + (ld.customer ? ld.customer : 'N/A') + '</td>' +
                                '<td style="padding:8px 12px; color:#475569;">' + (ld.poc ? ld.poc : '-') + '</td>' +
                                '<td style="padding:8px 12px; font-weight:600; color:#00a2d6;">' + (ld.lead_stage ? ld.lead_stage : 'Database') + '</td>' +
                                '<td style="padding:8px 12px; color:#475569;">' + stName + '</td>' +
                                '<td style="padding:8px 12px; text-align:right; color:#64748b;">' + (ld.created ? ld.created : '-') + '</td>' +
                            '</tr>';
                        }
                    } else {
                        leadsHtml = '<tr><td colspan="5" style="text-align:center; padding:24px 0; color:#94a3b8;">No recent leads</td></tr>';
                    }
                    $('#liveRecentLeadsTbody').html(leadsHtml);

                    $('#liveOrdersTotal').text(resp.orders.total);
                    $('#liveOrdersPending').text(resp.orders.pending);
                    var ordRate = resp.orders.total > 0 ? ((resp.orders.completed / resp.orders.total) * 100).toFixed(1) : '0.0';
                    $('#liveOrdersRate').text(ordRate + '%');

                    $('#liveSalesRevenue').text(resp.sales.revenue_formatted);
                    $('#liveSalesCount').text(resp.sales.count);
                    var avgSales = resp.sales.count > 0 ? (resp.sales.revenue / resp.sales.count) : 0;
                    $('#liveSalesAvg').text('AED ' + avgSales.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));

                    $('#liveFollowupsTotal').text(resp.followups.total);
                    $('#liveFollowupsMissed').text(resp.followups.missed);
                    $('#liveFollowupsToday').text(resp.followups.today);

                    $('#liveMeetingsTotal').text(resp.meetings.total);
                    $('#liveTicketsTotal').text(resp.tickets.total);
                    $('#liveTicketsOpen').text(resp.tickets.open);
                    $('#liveTicketsResolved').text(resp.tickets.resolved);

                    $('#liveArticlesTotal').text(resp.articles.total);
                }
            },
            error: function() {
                $('#liveRefreshIcon').removeClass('fa-spin');
            }
        });
    }

    $('.live-scope-btn').on('click', function() {
        $('.live-scope-btn').removeClass('active').css({ 'background': 'transparent', 'color': '#64748b' });
        $(this).addClass('active').css({ 'background': '#0f172a', 'color': '#ffffff' });
        currentScope = $(this).data('scope');
        fetchLiveData();
    });

    $('.live-tf-btn').on('click', function() {
        $('.live-tf-btn').removeClass('active').css({ 'background': 'transparent', 'color': '#475569' });
        $(this).addClass('active').css({ 'background': '#00c6f0', 'color': '#ffffff' });
        currentTimeframe = $(this).data('tf');
        fetchLiveData();
    });

    $('#btnLiveRefreshNow').on('click', function() {
        fetchLiveData();
    });

    fetchLiveData();
    refreshTimer = setInterval(fetchLiveData, 10000);
});
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
