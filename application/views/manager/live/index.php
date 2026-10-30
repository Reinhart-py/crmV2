<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<div class="content-wrapper" style="background:#f8fafc; min-height:920px; padding-bottom:40px;">
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
                    Auto-updates every 10s &bull; Last updated: <span id="liveLastSync" style="font-weight:700; color:#0f172a;">--:--:--</span>
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
                    <div style="font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:0.04em; color:#64748b;">Live Calls</div>
                    <div style="display:flex; align-items:baseline; gap:8px; margin:10px 0 12px 0;">
                        <span id="liveCallsTotal" style="font-size:36px; font-weight:800; color:#0f172a; line-height:1; letter-spacing:-0.03em;">0</span>
                        <span style="font-size:13px; font-weight:600; color:#64748b;">Total Placed</span>
                    </div>
                    <div style="height:6px; background:#f1f5f9; border-radius:3px; display:flex; overflow:hidden; margin:14px 0 12px 0;">
                        <div id="liveBarConnected" style="background:#00c6f0; width:0%; transition:width 0.4s ease;"></div>
                        <div id="liveBarInProcess" style="background:#38bdf8; width:0%; transition:width 0.4s ease;"></div>
                        <div id="liveBarOther" style="background:#cbd5e1; width:0%; transition:width 0.4s ease;"></div>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center; font-size:12px; margin-top:14px; flex-wrap:wrap; gap:8px;">
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
                    <div style="font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:0.04em; color:#64748b;">Live Pipeline Flow</div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin:10px 0 12px 0;">
                        <div>
                            <div style="font-size:26px; font-weight:800; color:#0f172a; line-height:1;" id="liveLeadsTotal">0</div>
                            <div style="font-size:12px; font-weight:600; color:#00a2d6; margin-top:4px;"><span id="liveLeadsQualified">0</span> Leads</div>
                        </div>
                        <div style="color:#cbd5e1; font-size:20px; font-weight:300;">&rarr;</div>
                        <div>
                            <div style="font-size:26px; font-weight:800; color:#0f172a; line-height:1;" id="liveOrdersTotal">0</div>
                            <div style="font-size:12px; font-weight:600; color:#475569; margin-top:4px;"><span id="liveOrdersPending">0</span> Orders</div>
                        </div>
                        <div style="color:#cbd5e1; font-size:20px; font-weight:300;">&rarr;</div>
                        <div>
                            <div style="font-size:26px; font-weight:800; color:#0f172a; line-height:1;" id="liveSalesCount">0</div>
                            <div style="font-size:12px; font-weight:600; color:#16a34a; margin-top:4px;">Sales Deals</div>
                        </div>
                    </div>
                    <div style="margin-top:16px; padding-top:12px; border-top:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; font-size:11px; color:#64748b;">
                        <span>Lead Conversion: <strong id="liveLeadsRate" style="color:#0f172a; font-weight:700;">0.0%</strong></span>
                        <span>Order Win Rate: <strong id="liveOrdersRate" style="color:#0f172a; font-weight:700;">0.0%</strong></span>
                    </div>
                </div>

                <div class="col-md-3 col-xs-12" style="padding:0 0 0 24px;">
                    <div style="font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:0.04em; color:#64748b;">Sales Revenue Achieved</div>
                    <div id="liveSalesRevenue" style="font-size:28px; font-weight:800; color:#0f172a; letter-spacing:-0.03em; margin:10px 0 6px 0; line-height:1;">AED 0.00</div>
                    <div style="font-size:12px; color:#64748b; margin-top:6px;">
                        Avg Closed Deal: <strong id="liveSalesAvg" style="color:#00a2d6;">AED 0.00</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-xs-12" style="margin-bottom:20px;">
                <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:22px 24px; box-shadow:0 1px 3px rgba(0,0,0,0.02); height:100%;">
                    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #f1f5f9; padding-bottom:14px; margin-bottom:16px;">
                        <div style="font-size:13px; font-weight:800; color:#0f172a; text-transform:uppercase; letter-spacing:0.04em;">Call Status &amp; Breakdown</div>
                        <span id="liveCallBadge" style="font-size:11px; font-weight:700; color:#00c6f0; background:rgba(0,198,240,0.08); padding:3px 10px; border-radius:4px;">0 Calls</span>
                    </div>

                    <div style="display:flex; flex-direction:column; gap:14px; padding:6px 0;">
                        <div>
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px; font-size:13px;">
                                <span style="font-weight:600; color:#0f172a; display:flex; align-items:center; gap:8px;">
                                    <span style="width:10px; height:10px; border-radius:2px; background:#00c6f0; display:inline-block;"></span>
                                    Connected Calls
                                </span>
                                <span style="font-weight:700; color:#0f172a;"><span id="statConnectedVal">0</span> <span style="font-weight:500; font-size:11px; color:#64748b;">(<span id="statConnectedPct">0%</span>)</span></span>
                            </div>
                            <div style="height:6px; background:#f1f5f9; border-radius:3px; overflow:hidden;">
                                <div id="barStatConnected" style="background:#00c6f0; width:0%; height:100%; transition:width 0.4s ease;"></div>
                            </div>
                        </div>

                        <div>
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px; font-size:13px;">
                                <span style="font-weight:600; color:#0f172a; display:flex; align-items:center; gap:8px;">
                                    <span style="width:10px; height:10px; border-radius:2px; background:#38bdf8; display:inline-block;"></span>
                                    In Process / Ringing
                                </span>
                                <span style="font-weight:700; color:#0f172a;"><span id="statInProcessVal">0</span> <span style="font-weight:500; font-size:11px; color:#64748b;">(<span id="statInProcessPct">0%</span>)</span></span>
                            </div>
                            <div style="height:6px; background:#f1f5f9; border-radius:3px; overflow:hidden;">
                                <div id="barStatInProcess" style="background:#38bdf8; width:0%; height:100%; transition:width 0.4s ease;"></div>
                            </div>
                        </div>

                        <div>
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px; font-size:13px;">
                                <span style="font-weight:600; color:#0f172a; display:flex; align-items:center; gap:8px;">
                                    <span style="width:10px; height:10px; border-radius:2px; background:#94a3b8; display:inline-block;"></span>
                                    Other / Not Responding
                                </span>
                                <span style="font-weight:700; color:#0f172a;"><span id="statOtherVal">0</span> <span style="font-weight:500; font-size:11px; color:#64748b;">(<span id="statOtherPct">0%</span>)</span></span>
                            </div>
                            <div style="height:6px; background:#f1f5f9; border-radius:3px; overflow:hidden;">
                                <div id="barStatOther" style="background:#94a3b8; width:0%; height:100%; transition:width 0.4s ease;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xs-12" style="margin-bottom:20px;">
                <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:22px 24px; box-shadow:0 1px 3px rgba(0,0,0,0.02); height:100%;">
                    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #f1f5f9; padding-bottom:14px; margin-bottom:16px;">
                        <div style="font-size:13px; font-weight:800; color:#0f172a; text-transform:uppercase; letter-spacing:0.04em;">Lead Stages Breakdown</div>
                        <span id="liveLeadBadge" style="font-size:11px; font-weight:700; color:#00c6f0; background:rgba(0,198,240,0.08); padding:3px 10px; border-radius:4px;">0 Leads</span>
                    </div>

                    <div id="liveLeadStagesList" style="display:flex; flex-direction:column; gap:12px; padding:6px 0;">
                        <div style="color:#94a3b8; font-size:13px; text-align:center; padding:18px 0;">Loading lead stages...</div>
                    </div>
                </div>
            </div>
        </div>

        <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:22px 28px; box-shadow:0 1px 3px rgba(0,0,0,0.02);">
            <div style="font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; color:#0f172a; margin-bottom:18px; border-bottom:1px solid #f1f5f9; padding-bottom:10px;">
                Operational Activities
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
                        Scheduled Meetings
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
    background: #00c6f0;
    border-radius: 50%;
    display: inline-block;
    animation: livePulseAura 2s infinite;
}
.live-scope-btn.active {
    background: #0f172a !important;
    color: #ffffff !important;
}
.live-tf-btn.active {
    background: #00c6f0 !important;
    color: #ffffff !important;
}
</style>

<script>
$(document).ready(function() {
    var curScope = 'my';
    var curTf = 'today';
    var refreshTimer = null;

    function formatNumber(num) {
        return (num || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function formatCurrency(val) {
        var n = parseFloat(val) || 0;
        return 'AED ' + n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function fetchLiveData() {
        $('#liveRefreshIcon').addClass('fa-spin');
        $.ajax({
            url: '<?php echo base_url(); ?>manager/live/ajax_get_data',
            type: 'POST',
            data: { scope: curScope, timeframe: curTf },
            dataType: 'json',
            success: function(res) {
                $('#liveRefreshIcon').removeClass('fa-spin');
                if (!res || res.status !== 'success') return;

                $('#liveLastSync').text(res.timestamp || '--:--:--');

                var cTotal = res.calls ? parseInt(res.calls.total) || 0 : 0;
                var cConn = res.calls ? parseInt(res.calls.connected) || 0 : 0;
                var cProc = res.calls ? parseInt(res.calls.in_process) || 0 : 0;
                var cOther = res.calls ? parseInt(res.calls.other) || 0 : 0;
                var cRate = cTotal > 0 ? ((cConn / cTotal) * 100).toFixed(1) : '0.0';

                $('#liveCallsTotal').text(formatNumber(cTotal));
                $('#liveCallsConnected').text(formatNumber(cConn));
                $('#liveCallsInProcess').text(formatNumber(cProc));
                $('#liveCallsOther').text(formatNumber(cOther));
                $('#liveCallsRate').text(cRate + '%');
                $('#liveCallBadge').text(formatNumber(cTotal) + ' Calls');

                var pConn = cTotal > 0 ? ((cConn / cTotal) * 100).toFixed(1) : 0;
                var pProc = cTotal > 0 ? ((cProc / cTotal) * 100).toFixed(1) : 0;
                var pOth = cTotal > 0 ? ((cOther / cTotal) * 100).toFixed(1) : 0;

                $('#liveBarConnected').css('width', pConn + '%');
                $('#liveBarInProcess').css('width', pProc + '%');
                $('#liveBarOther').css('width', pOth + '%');

                $('#statConnectedVal').text(formatNumber(cConn));
                $('#statConnectedPct').text(pConn + '%');
                $('#barStatConnected').css('width', pConn + '%');

                $('#statInProcessVal').text(formatNumber(cProc));
                $('#statInProcessPct').text(pProc + '%');
                $('#barStatInProcess').css('width', pProc + '%');

                $('#statOtherVal').text(formatNumber(cOther));
                $('#statOtherPct').text(pOth + '%');
                $('#barStatOther').css('width', pOth + '%');

                var lTotal = res.leads ? parseInt(res.leads.total) || 0 : 0;
                var lQual = res.leads ? parseInt(res.leads.qualified) || 0 : 0;
                var lRate = lTotal > 0 ? ((lQual / lTotal) * 100).toFixed(1) : '0.0';

                $('#liveLeadsTotal').text(formatNumber(lTotal));
                $('#liveLeadsQualified').text(formatNumber(lQual));
                $('#liveLeadsRate').text(lRate + '%');
                $('#liveLeadBadge').text(formatNumber(lTotal) + ' Leads');

                var oTotal = res.orders ? parseInt(res.orders.total) || 0 : 0;
                var oPending = res.orders ? parseInt(res.orders.pending) || 0 : 0;
                var oComp = res.orders ? parseInt(res.orders.completed) || 0 : 0;
                var oRate = oTotal > 0 ? (((oComp) / oTotal) * 100).toFixed(1) : '0.0';

                $('#liveOrdersTotal').text(formatNumber(oTotal));
                $('#liveOrdersPending').text(formatNumber(oPending));
                $('#liveOrdersRate').text(oRate + '%');

                var sCount = res.sales ? parseInt(res.sales.count) || 0 : 0;
                var sRev = res.sales ? parseFloat(res.sales.revenue) || 0.0 : 0.0;
                var sAvg = sCount > 0 ? (sRev / sCount) : 0.0;

                $('#liveSalesCount').text(formatNumber(sCount));
                $('#liveSalesRevenue').text(formatCurrency(sRev));
                $('#liveSalesAvg').text(formatCurrency(sAvg));

                if (res.leads && res.leads.statuses && res.leads.statuses.length > 0) {
                    var sHtml = '';
                    var maxCnt = 1;
                    $.each(res.leads.statuses, function(i, st) {
                        if (st.count > maxCnt) maxCnt = st.count;
                    });
                    $.each(res.leads.statuses, function(i, st) {
                        var pct = Math.round((st.count / (lTotal > 0 ? lTotal : 1)) * 100);
                        var barW = Math.round((st.count / maxCnt) * 100);
                        sHtml += '<div>' +
                            '<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:5px; font-size:12.5px;">' +
                                '<span style="font-weight:600; color:#0f172a;">' + st.name + '</span>' +
                                '<span style="font-weight:700; color:#0f172a;">' + formatNumber(st.count) + ' <span style="font-weight:500; font-size:11px; color:#64748b;">(' + pct + '%)</span></span>' +
                            '</div>' +
                            '<div style="height:5px; background:#f1f5f9; border-radius:3px; overflow:hidden;">' +
                                '<div style="background:#00c6f0; width:' + barW + '%; height:100%; transition:width 0.4s ease;"></div>' +
                            '</div>' +
                        '</div>';
                    });
                    $('#liveLeadStagesList').html(sHtml);
                } else {
                    $('#liveLeadStagesList').html('<div style="color:#94a3b8; font-size:13px; text-align:center; padding:18px 0;">No lead stages recorded</div>');
                }

                $('#liveFollowupsTotal').text(formatNumber(res.followups ? res.followups.total : 0));
                $('#liveFollowupsToday').text((res.followups ? res.followups.today : 0) + ' Today');
                $('#liveFollowupsMissed').text((res.followups ? res.followups.missed : 0) + ' Missed');

                $('#liveMeetingsTotal').text(formatNumber(res.meetings ? res.meetings.total : 0));

                $('#liveTicketsTotal').text(formatNumber(res.tickets ? res.tickets.total : 0));
                $('#liveTicketsOpen').text((res.tickets ? res.tickets.open : 0) + ' Open');
                $('#liveTicketsResolved').text((res.tickets ? res.tickets.resolved : 0) + ' Resolved');

                $('#liveArticlesTotal').text(formatNumber(res.articles ? res.articles.total : 0));
            },
            error: function() {
                $('#liveRefreshIcon').removeClass('fa-spin');
            }
        });
    }

    $('.live-scope-btn').on('click', function() {
        $('.live-scope-btn').removeClass('active').css({ 'background': 'transparent', 'color': '#64748b' });
        $(this).addClass('active').css({ 'background': '#0f172a', 'color': '#ffffff' });
        curScope = $(this).data('scope');
        fetchLiveData();
    });

    $('.live-tf-btn').on('click', function() {
        $('.live-tf-btn').removeClass('active').css({ 'background': 'transparent', 'color': '#475569' });
        $(this).addClass('active').css({ 'background': '#00c6f0', 'color': '#ffffff' });
        curTf = $(this).data('tf');
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
