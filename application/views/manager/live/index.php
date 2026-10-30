<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<div class="content-wrapper" style="background:#f8fafc; min-height:920px;">
    <section class="content-header" style="padding:16px 20px 8px 20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div>
                <h1 style="font-size:22px; font-weight:700; color:#0f172a; margin:0; display:flex; align-items:center; gap:10px;">
                    <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#10b981; box-shadow:0 0 0 3px rgba(16,185,129,0.25); animation:livePulseDot 2s infinite;"></span>
                    LIVE Operations Console
                </h1>
                <p style="font-size:12px; color:#64748b; margin:4px 0 0 0;">Real-time feed updated continuously &bull; Last sync: <span id="liveLastSync">--:--:--</span></p>
            </div>
            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <div class="btn-group" role="group" id="liveScopeGroup" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:3px; box-shadow:0 1px 2px rgba(0,0,0,0.04);">
                    <button type="button" class="btn btn-xs live-scope-btn active" data-scope="my" style="border-radius:6px; font-weight:600; padding:6px 14px; border:none; background:#00c6f0; color:#ffffff;">My Activity</button>
                    <?php if(!empty($has_team)){ ?>
                    <button type="button" class="btn btn-xs live-scope-btn" data-scope="team" style="border-radius:6px; font-weight:600; padding:6px 14px; border:none; background:transparent; color:#475569;">Team Activity</button>
                    <?php } ?>
                </div>

                <div class="btn-group" role="group" id="liveTimeframeGroup" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:3px; box-shadow:0 1px 2px rgba(0,0,0,0.04);">
                    <button type="button" class="btn btn-xs live-tf-btn active" data-tf="today" style="border-radius:6px; font-weight:600; padding:6px 12px; border:none; background:#0f172a; color:#ffffff;">Today</button>
                    <button type="button" class="btn btn-xs live-tf-btn" data-tf="last7" style="border-radius:6px; font-weight:600; padding:6px 12px; border:none; background:transparent; color:#475569;">Last 7d</button>
                    <button type="button" class="btn btn-xs live-tf-btn" data-tf="month" style="border-radius:6px; font-weight:600; padding:6px 12px; border:none; background:transparent; color:#475569;">Month</button>
                    <button type="button" class="btn btn-xs live-tf-btn" data-tf="quarter" style="border-radius:6px; font-weight:600; padding:6px 12px; border:none; background:transparent; color:#475569;">Quarter</button>
                </div>

                <button type="button" id="btnLiveRefreshNow" class="btn btn-default btn-sm" style="border-radius:8px; border:1px solid #e2e8f0; font-weight:600; color:#0f172a;" title="Refresh now">
                    <i class="fa fa-refresh" id="liveRefreshIcon"></i>
                </button>
            </div>
        </div>
    </section>

    <section class="content" style="padding:12px 20px 24px 20px;">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12" style="margin-bottom:14px;">
                <div style="background:#ffffff; border:1px solid #e2e8f0; border-left:4px solid #00c6f0; border-radius:10px; padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">Calls Made</span>
                        <div style="width:34px; height:34px; border-radius:8px; background:rgba(0,198,240,0.12); display:flex; align-items:center; justify-content:center; color:#00a2d6;">
                            <i class="fa fa-phone" style="font-size:15px;"></i>
                        </div>
                    </div>
                    <div style="margin-top:10px; display:flex; align-items:baseline; justify-content:space-between;">
                        <span id="liveCallsTotal" style="font-size:28px; font-weight:800; color:#0f172a; line-height:1;">0</span>
                        <span style="font-size:11px; color:#10b981; font-weight:600;"><span id="liveCallsConnected">0</span> Connected</span>
                    </div>
                    <div style="margin-top:10px; height:4px; background:#f1f5f9; border-radius:2px; overflow:hidden; display:flex;">
                        <div id="liveBarConnected" style="background:#10b981; width:0%;"></div>
                        <div id="liveBarInProcess" style="background:#00c6f0; width:0%;"></div>
                        <div id="liveBarOther" style="background:#cbd5e1; width:0%;"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12" style="margin-bottom:14px;">
                <div style="background:#ffffff; border:1px solid #e2e8f0; border-left:4px solid #00c6f0; border-radius:10px; padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">Leads Generated</span>
                        <div style="width:34px; height:34px; border-radius:8px; background:rgba(0,198,240,0.12); display:flex; align-items:center; justify-content:center; color:#00a2d6;">
                            <i class="fa fa-bullhorn" style="font-size:15px;"></i>
                        </div>
                    </div>
                    <div style="margin-top:10px; display:flex; align-items:baseline; justify-content:space-between;">
                        <span id="liveLeadsTotal" style="font-size:28px; font-weight:800; color:#0f172a; line-height:1;">0</span>
                        <span style="font-size:11px; color:#0284c7; font-weight:600;"><span id="liveLeadsQualified">0</span> Qualified</span>
                    </div>
                    <div style="margin-top:10px; height:4px; background:#f1f5f9; border-radius:2px; overflow:hidden; display:flex;">
                        <div id="liveBarLeadsQual" style="background:#0284c7; width:0%;"></div>
                        <div id="liveBarLeadsDb" style="background:#94a3b8; width:0%;"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12" style="margin-bottom:14px;">
                <div style="background:#ffffff; border:1px solid #e2e8f0; border-left:4px solid #00c6f0; border-radius:10px; padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">Orders Submitted</span>
                        <div style="width:34px; height:34px; border-radius:8px; background:rgba(0,198,240,0.12); display:flex; align-items:center; justify-content:center; color:#00a2d6;">
                            <i class="fa fa-shopping-cart" style="font-size:15px;"></i>
                        </div>
                    </div>
                    <div style="margin-top:10px; display:flex; align-items:baseline; justify-content:space-between;">
                        <span id="liveOrdersTotal" style="font-size:28px; font-weight:800; color:#0f172a; line-height:1;">0</span>
                        <span style="font-size:11px; color:#f59e0b; font-weight:600;"><span id="liveOrdersPending">0</span> Pending</span>
                    </div>
                    <div style="margin-top:10px; height:4px; background:#f1f5f9; border-radius:2px; overflow:hidden; display:flex;">
                        <div id="liveBarOrdersDone" style="background:#10b981; width:0%;"></div>
                        <div id="liveBarOrdersPend" style="background:#f59e0b; width:0%;"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12" style="margin-bottom:14px;">
                <div style="background:#ffffff; border:1px solid #e2e8f0; border-left:4px solid #00c6f0; border-radius:10px; padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">Sales Achieved</span>
                        <div style="width:34px; height:34px; border-radius:8px; background:rgba(0,198,240,0.12); display:flex; align-items:center; justify-content:center; color:#00a2d6;">
                            <i class="fa fa-trophy" style="font-size:15px;"></i>
                        </div>
                    </div>
                    <div style="margin-top:10px; display:flex; align-items:baseline; justify-content:space-between;">
                        <span id="liveSalesRevenue" style="font-size:24px; font-weight:800; color:#0f172a; line-height:1;">AED 0.00</span>
                        <span style="font-size:11px; color:#10b981; font-weight:700;"><span id="liveSalesCount">0</span> Deals</span>
                    </div>
                    <div style="margin-top:10px; height:4px; background:#10b981; border-radius:2px;"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12" style="margin-bottom:14px;">
                <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px; display:flex; align-items:center; justify-content:space-between;">
                    <div>
                        <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:#64748b;">Follow-ups</div>
                        <div style="font-size:22px; font-weight:800; color:#0f172a; margin-top:2px;" id="liveFollowupsTotal">0</div>
                        <div style="font-size:11px; color:#ef4444; margin-top:2px;"><span id="liveFollowupsMissed">0</span> Missed &bull; <span id="liveFollowupsToday" style="color:#0284c7;">0 Today</span></div>
                    </div>
                    <div style="width:40px; height:40px; border-radius:8px; background:#f8fafc; border:1px solid #e2e8f0; display:flex; align-items:center; justify-content:center; color:#64748b;">
                        <i class="fa fa-clock-o" style="font-size:17px;"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12" style="margin-bottom:14px;">
                <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px; display:flex; align-items:center; justify-content:space-between;">
                    <div>
                        <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:#64748b;">Meetings Scheduled</div>
                        <div style="font-size:22px; font-weight:800; color:#0f172a; margin-top:2px;" id="liveMeetingsTotal">0</div>
                        <div style="font-size:11px; color:#64748b; margin-top:2px;">In selected timeframe</div>
                    </div>
                    <div style="width:40px; height:40px; border-radius:8px; background:#f8fafc; border:1px solid #e2e8f0; display:flex; align-items:center; justify-content:center; color:#64748b;">
                        <i class="fa fa-calendar" style="font-size:17px;"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12" style="margin-bottom:14px;">
                <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px; display:flex; align-items:center; justify-content:space-between;">
                    <div>
                        <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:#64748b;">Tickets</div>
                        <div style="font-size:22px; font-weight:800; color:#0f172a; margin-top:2px;" id="liveTicketsTotal">0</div>
                        <div style="font-size:11px; color:#ef4444; margin-top:2px;"><span id="liveTicketsOpen">0</span> Open &bull; <span id="liveTicketsResolved" style="color:#10b981;">0 Resolved</span></div>
                    </div>
                    <div style="width:40px; height:40px; border-radius:8px; background:#f8fafc; border:1px solid #e2e8f0; display:flex; align-items:center; justify-content:center; color:#64748b;">
                        <i class="fa fa-ticket" style="font-size:17px;"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12" style="margin-bottom:14px;">
                <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px; display:flex; align-items:center; justify-content:space-between;">
                    <div>
                        <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:#64748b;">Active Verticals / Articles</div>
                        <div style="font-size:22px; font-weight:800; color:#0f172a; margin-top:2px;" id="liveArticlesTotal">0</div>
                        <div style="font-size:11px; color:#64748b; margin-top:2px;">Live catalog items</div>
                    </div>
                    <div style="width:40px; height:40px; border-radius:8px; background:#f8fafc; border:1px solid #e2e8f0; display:flex; align-items:center; justify-content:center; color:#64748b;">
                        <i class="fa fa-cubes" style="font-size:17px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top:6px;">
            <div class="col-md-6 col-xs-12" style="margin-bottom:16px;">
                <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:18px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #f1f5f9; padding-bottom:12px;">
                        <h4 style="font-size:15px; font-weight:700; color:#0f172a; margin:0;">
                            <i class="fa fa-phone" style="color:#00c6f0; margin-right:6px;"></i> Live Call Statuses
                        </h4>
                        <span class="label" id="liveCallBadge" style="background:#0f172a; color:#ffffff; font-size:11px; padding:4px 8px; border-radius:4px;">0 Total</span>
                    </div>

                    <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:10px; margin-top:16px;">
                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; text-align:center;">
                            <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:#10b981;">Connected</div>
                            <div id="liveCallStatusConnected" style="font-size:24px; font-weight:800; color:#0f172a; margin-top:4px;">0</div>
                        </div>
                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; text-align:center;">
                            <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:#00a2d6;">In Process</div>
                            <div id="liveCallStatusInProcess" style="font-size:24px; font-weight:800; color:#0f172a; margin-top:4px;">0</div>
                        </div>
                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; text-align:center;">
                            <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:#64748b;">Other / Busy</div>
                            <div id="liveCallStatusOther" style="font-size:24px; font-weight:800; color:#0f172a; margin-top:4px;">0</div>
                        </div>
                    </div>

                    <div style="margin-top:16px;">
                        <div style="font-size:12px; font-weight:700; color:#475569; margin-bottom:8px;">Recent Live Calls</div>
                        <div id="liveRecentCallsList" style="min-height:110px;">
                            <div style="text-align:center; padding:30px 0; color:#94a3b8; font-size:13px;">No calls logged for this filter</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xs-12" style="margin-bottom:16px;">
                <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:18px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #f1f5f9; padding-bottom:12px;">
                        <h4 style="font-size:15px; font-weight:700; color:#0f172a; margin:0;">
                            <i class="fa fa-bullhorn" style="color:#00c6f0; margin-right:6px;"></i> Live Leads Pipeline Tracking
                        </h4>
                        <span class="label" id="liveLeadBadge" style="background:#0f172a; color:#ffffff; font-size:11px; padding:4px 8px; border-radius:4px;">0 Total</span>
                    </div>

                    <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:10px; margin-top:16px;">
                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; text-align:center;">
                            <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:#0284c7;">Qualified Leads</div>
                            <div id="liveLeadStatusQual" style="font-size:24px; font-weight:800; color:#0f172a; margin-top:4px;">0</div>
                        </div>
                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; text-align:center;">
                            <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:#64748b;">Database Entries</div>
                            <div id="liveLeadStatusDb" style="font-size:24px; font-weight:800; color:#0f172a; margin-top:4px;">0</div>
                        </div>
                    </div>

                    <div style="margin-top:16px;">
                        <div style="font-size:12px; font-weight:700; color:#475569; margin-bottom:8px;">Pipeline Stage Breakdown</div>
                        <div id="liveLeadStagesList" style="display:flex; flex-wrap:wrap; gap:8px;">
                            <span style="color:#94a3b8; font-size:12px;">Loading stages...</span>
                        </div>
                    </div>

                    <div style="margin-top:16px;">
                        <div style="font-size:12px; font-weight:700; color:#475569; margin-bottom:8px;">Latest Leads Added</div>
                        <div id="liveRecentLeadsList" style="min-height:90px;">
                            <div style="text-align:center; padding:20px 0; color:#94a3b8; font-size:13px;">No leads recorded</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
@keyframes livePulseDot {
    0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
    100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}
.live-scope-btn.active {
    background: #00c6f0 !important;
    color: #ffffff !important;
}
.live-tf-btn.active {
    background: #0f172a !important;
    color: #ffffff !important;
}
.live-call-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 10px;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 6px;
    margin-bottom: 6px;
    font-size: 12px;
}
.live-lead-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 10px;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 6px;
    margin-bottom: 6px;
    font-size: 12px;
}
</style>

<script>
$(document).ready(function() {
    var currentTimeframe = 'today';
    var currentScope = 'my';
    var refreshTimer = null;

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
                    $('#liveCallStatusConnected').text(resp.calls.connected);
                    $('#liveCallStatusInProcess').text(resp.calls.in_process);
                    $('#liveCallStatusOther').text(resp.calls.other);
                    $('#liveCallBadge').text(resp.calls.total + ' Calls');

                    var callTot = resp.calls.total > 0 ? resp.calls.total : 1;
                    $('#liveBarConnected').css('width', Math.round((resp.calls.connected / callTot) * 100) + '%');
                    $('#liveBarInProcess').css('width', Math.round((resp.calls.in_process / callTot) * 100) + '%');
                    $('#liveBarOther').css('width', Math.round((resp.calls.other / callTot) * 100) + '%');

                    var callsHtml = '';
                    if (resp.calls.latest && resp.calls.latest.length > 0) {
                        for (var i = 0; i < resp.calls.latest.length; i++) {
                            var cl = resp.calls.latest[i];
                            var stColor = cl.status === 'connected' ? '#10b981' : (cl.status === 'in_process' ? '#00c6f0' : '#64748b');
                            callsHtml += '<div class="live-call-item">' +
                                '<div><strong>' + (cl.customer_name ? cl.customer_name : 'Unknown') + '</strong> &bull; ' + (cl.phone_number ? cl.phone_number : '') + '</div>' +
                                '<div><span style="font-weight:700; color:' + stColor + '; text-transform:capitalize;">' + cl.status + '</span> &bull; ' + (cl.duration ? cl.duration + 's' : '0s') + '</div>' +
                            '</div>';
                        }
                    } else {
                        callsHtml = '<div style="text-align:center; padding:24px 0; color:#94a3b8; font-size:12px;">No recent calls found</div>';
                    }
                    $('#liveRecentCallsList').html(callsHtml);

                    $('#liveLeadsTotal').text(resp.leads.total);
                    $('#liveLeadsQualified').text(resp.leads.qualified);
                    $('#liveLeadStatusQual').text(resp.leads.qualified);
                    $('#liveLeadStatusDb').text(resp.leads.database);
                    $('#liveLeadBadge').text(resp.leads.total + ' Leads');

                    var leadTot = resp.leads.total > 0 ? resp.leads.total : 1;
                    $('#liveBarLeadsQual').css('width', Math.round((resp.leads.qualified / leadTot) * 100) + '%');
                    $('#liveBarLeadsDb').css('width', Math.round((resp.leads.database / leadTot) * 100) + '%');

                    var stagesHtml = '';
                    if (resp.leads.statuses && resp.leads.statuses.length > 0) {
                        for (var j = 0; j < resp.leads.statuses.length; j++) {
                            var st = resp.leads.statuses[j];
                            stagesHtml += '<span style="background:#f1f5f9; border:1px solid #e2e8f0; border-radius:6px; padding:4px 10px; font-size:11px; font-weight:600; color:#334155;">' +
                                st.name + ': <strong style="color:#00a2d6;">' + st.count + '</strong></span>';
                        }
                    } else {
                        stagesHtml = '<span style="color:#94a3b8; font-size:12px;">No active stage data</span>';
                    }
                    $('#liveLeadStagesList').html(stagesHtml);

                    var leadsHtml = '';
                    if (resp.leads.latest && resp.leads.latest.length > 0) {
                        for (var k = 0; k < resp.leads.latest.length; k++) {
                            var ld = resp.leads.latest[k];
                            leadsHtml += '<div class="live-lead-item">' +
                                '<div><strong>' + (ld.customer ? ld.customer : 'N/A') + '</strong> &bull; ' + (ld.poc ? ld.poc : '') + '</div>' +
                                '<div><span class="label label-info" style="font-size:10px;">' + (ld.lead_stage ? ld.lead_stage : 'Database') + '</span></div>' +
                            '</div>';
                        }
                    } else {
                        leadsHtml = '<div style="text-align:center; padding:20px 0; color:#94a3b8; font-size:12px;">No recent leads</div>';
                    }
                    $('#liveRecentLeadsList').html(leadsHtml);

                    $('#liveOrdersTotal').text(resp.orders.total);
                    $('#liveOrdersPending').text(resp.orders.pending);
                    var ordTot = resp.orders.total > 0 ? resp.orders.total : 1;
                    $('#liveBarOrdersDone').css('width', Math.round((resp.orders.completed / ordTot) * 100) + '%');
                    $('#liveBarOrdersPend').css('width', Math.round((resp.orders.pending / ordTot) * 100) + '%');

                    $('#liveSalesRevenue').text(resp.sales.revenue_formatted);
                    $('#liveSalesCount').text(resp.sales.count);

                    $('#liveFollowupsTotal').text(resp.followups.total);
                    $('#liveFollowupsMissed').text(resp.followups.missed);
                    $('#liveFollowupsToday').text(resp.followups.today + ' Today');

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
        $('.live-scope-btn').removeClass('active').css({ 'background': 'transparent', 'color': '#475569' });
        $(this).addClass('active').css({ 'background': '#00c6f0', 'color': '#ffffff' });
        currentScope = $(this).data('scope');
        fetchLiveData();
    });

    $('.live-tf-btn').on('click', function() {
        $('.live-tf-btn').removeClass('active').css({ 'background': 'transparent', 'color': '#475569' });
        $(this).addClass('active').css({ 'background': '#0f172a', 'color': '#ffffff' });
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
