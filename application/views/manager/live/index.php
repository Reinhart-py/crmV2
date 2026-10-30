<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<div class="content-wrapper" style="background:#f8fafc; min-height:920px;">
    <section class="content-header" style="padding:16px 20px 10px 20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div>
                <h1 style="font-size:20px; font-weight:800; color:#0f172a; margin:0; letter-spacing:-0.02em;">
                    LIVE <span style="color:#00c6f0;">OPERATIONS REPORT</span>
                </h1>
                <div style="font-size:12px; color:#64748b; margin-top:3px;">
                    Continuous synchronization &bull; Last synchronized: <span id="liveLastSync" style="font-weight:600; color:#0f172a;">--:--:--</span>
                </div>
            </div>
            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                <div class="btn-group" role="group" id="liveScopeGroup" style="background:#ffffff; border:1px solid #00c6f0; border-radius:6px; padding:2px;">
                    <button type="button" class="btn btn-xs live-scope-btn active" data-scope="my" style="border-radius:4px; font-weight:700; font-size:11px; padding:5px 12px; border:none; background:#00c6f0; color:#ffffff;">My Activity</button>
                    <?php if(!empty($has_team)){ ?>
                    <button type="button" class="btn btn-xs live-scope-btn" data-scope="team" style="border-radius:4px; font-weight:700; font-size:11px; padding:5px 12px; border:none; background:transparent; color:#00c6f0;">Team Activity</button>
                    <?php } ?>
                </div>

                <div class="btn-group" role="group" id="liveTimeframeGroup" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:6px; padding:2px;">
                    <button type="button" class="btn btn-xs live-tf-btn active" data-tf="today" style="border-radius:4px; font-weight:700; font-size:11px; padding:5px 12px; border:none; background:#00c6f0; color:#ffffff;">Today</button>
                    <button type="button" class="btn btn-xs live-tf-btn" data-tf="last7" style="border-radius:4px; font-weight:700; font-size:11px; padding:5px 12px; border:none; background:transparent; color:#475569;">Last 7 Days</button>
                    <button type="button" class="btn btn-xs live-tf-btn" data-tf="month" style="border-radius:4px; font-weight:700; font-size:11px; padding:5px 12px; border:none; background:transparent; color:#475569;">Month</button>
                    <button type="button" class="btn btn-xs live-tf-btn" data-tf="quarter" style="border-radius:4px; font-weight:700; font-size:11px; padding:5px 12px; border:none; background:transparent; color:#475569;">Quarter</button>
                </div>

                <button type="button" id="btnLiveRefreshNow" class="btn btn-default btn-xs" style="border-radius:6px; border:1px solid #e2e8f0; background:#ffffff; font-weight:700; color:#00c6f0; padding:6px 12px;" title="Refresh now">
                    <i class="fa fa-refresh" id="liveRefreshIcon"></i>
                </button>
            </div>
        </div>
    </section>

    <section class="content" style="padding:10px 20px 24px 20px;">
        <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; margin-bottom:18px; box-shadow:0 1px 3px rgba(0,0,0,0.02); overflow:hidden;">
            <div style="padding:12px 18px; background:#ffffff; border-bottom:2px solid #00c6f0; display:flex; justify-content:space-between; align-items:center;">
                <span style="font-size:13px; font-weight:800; color:#0f172a; text-transform:uppercase; letter-spacing:0.04em;">Commercial Operations &amp; Velocity Report</span>
                <span style="font-size:11px; font-weight:700; color:#00a2d6;">Dual-Palette Telemetry</span>
            </div>
            <div class="table-responsive" style="margin:0;">
                <table class="table table-hover" style="margin-bottom:0; font-size:13px;">
                    <thead>
                        <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0; color:#475569; font-size:11px; text-transform:uppercase; letter-spacing:0.05em;">
                            <th style="padding:10px 18px; font-weight:700; width:24%;">Operational Stream</th>
                            <th style="padding:10px 18px; font-weight:700; text-align:right; width:16%;">Total Activity</th>
                            <th style="padding:10px 18px; font-weight:700; width:30%;">Live Status Breakdown</th>
                            <th style="padding:10px 18px; font-weight:700; width:18%;">Secondary Breakdown</th>
                            <th style="padding:10px 18px; font-weight:700; text-align:right; width:12%;">Attainment / Rate</th>
                        </tr>
                    </thead>
                    <tbody style="color:#0f172a;">
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:12px 18px;">
                                <div style="font-weight:700;">Outbound Calling</div>
                                <div style="font-size:11px; color:#64748b;">Phone dialer &amp; customer connection volume</div>
                            </td>
                            <td style="padding:12px 18px; text-align:right;">
                                <span id="liveCallsTotal" style="font-size:17px; font-weight:800; color:#0f172a;">0</span>
                            </td>
                            <td style="padding:12px 18px;">
                                <span id="liveCallsConnected" style="font-weight:700; color:#00a2d6;">0</span> Connected &bull; <span id="liveCallsInProcess" style="font-weight:600; color:#475569;">0</span> In Process
                            </td>
                            <td style="padding:12px 18px;">
                                <span id="liveCallsOther" style="font-weight:600; color:#64748b;">0</span> Other / Ringing
                            </td>
                            <td style="padding:12px 18px; text-align:right;">
                                <span id="liveCallsRate" style="font-weight:800; color:#00c6f0;">0.0%</span>
                            </td>
                        </tr>

                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:12px 18px;">
                                <div style="font-weight:700;">Leads &amp; Commercial Pipeline</div>
                                <div style="font-size:11px; color:#64748b;">Qualified prospects &amp; database entries</div>
                            </td>
                            <td style="padding:12px 18px; text-align:right;">
                                <span id="liveLeadsTotal" style="font-size:17px; font-weight:800; color:#0f172a;">0</span>
                            </td>
                            <td style="padding:12px 18px;">
                                <span id="liveLeadsQualified" style="font-weight:700; color:#00a2d6;">0</span> Qualified Leads
                            </td>
                            <td style="padding:12px 18px;">
                                <span id="liveLeadsDatabase" style="font-weight:600; color:#64748b;">0</span> Database Records
                            </td>
                            <td style="padding:12px 18px; text-align:right;">
                                <span id="liveLeadsRate" style="font-weight:800; color:#00c6f0;">0.0%</span>
                            </td>
                        </tr>

                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:12px 18px;">
                                <div style="font-weight:700;">Orders Submitted</div>
                                <div style="font-size:11px; color:#64748b;">Commercial orders submitted to Back Office</div>
                            </td>
                            <td style="padding:12px 18px; text-align:right;">
                                <span id="liveOrdersTotal" style="font-size:17px; font-weight:800; color:#0f172a;">0</span>
                            </td>
                            <td style="padding:12px 18px;">
                                <span id="liveOrdersPending" style="font-weight:700; color:#00a2d6;">0</span> In Processing
                            </td>
                            <td style="padding:12px 18px;">
                                <span id="liveOrdersCompleted" style="font-weight:600; color:#64748b;">0</span> Completed
                            </td>
                            <td style="padding:12px 18px; text-align:right;">
                                <span id="liveOrdersRate" style="font-weight:800; color:#00c6f0;">0.0%</span>
                            </td>
                        </tr>

                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:12px 18px;">
                                <div style="font-weight:700;">Sales Achieved</div>
                                <div style="font-size:11px; color:#64748b;">Closed / Won deals and confirmed booking revenue</div>
                            </td>
                            <td style="padding:12px 18px; text-align:right;">
                                <span id="liveSalesRevenue" style="font-size:17px; font-weight:800; color:#0f172a;">AED 0.00</span>
                            </td>
                            <td style="padding:12px 18px;">
                                <span id="liveSalesCount" style="font-weight:700; color:#00a2d6;">0</span> Converted Sales Deals
                            </td>
                            <td style="padding:12px 18px;">
                                Verified Closed / Won
                            </td>
                            <td style="padding:12px 18px; text-align:right;">
                                <span id="liveSalesAvg" style="font-weight:800; color:#00c6f0;">AED 0.00</span>
                            </td>
                        </tr>

                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:12px 18px;">
                                <div style="font-weight:700;">Client Follow-ups</div>
                                <div style="font-size:11px; color:#64748b;">Active customer outreach schedule</div>
                            </td>
                            <td style="padding:12px 18px; text-align:right;">
                                <span id="liveFollowupsTotal" style="font-size:17px; font-weight:800; color:#0f172a;">0</span>
                            </td>
                            <td style="padding:12px 18px;">
                                <span id="liveFollowupsToday" style="font-weight:700; color:#00a2d6;">0</span> Scheduled Today
                            </td>
                            <td style="padding:12px 18px;">
                                <span id="liveFollowupsMissed" style="font-weight:600; color:#dc2626;">0</span> Overdue / Missed
                            </td>
                            <td style="padding:12px 18px; text-align:right;">
                                <span style="font-weight:700; color:#00c6f0;">Scheduled</span>
                            </td>
                        </tr>

                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:12px 18px;">
                                <div style="font-weight:700;">Meetings &amp; Client Visits</div>
                                <div style="font-size:11px; color:#64748b;">Face-to-face and virtual consultations</div>
                            </td>
                            <td style="padding:12px 18px; text-align:right;">
                                <span id="liveMeetingsTotal" style="font-size:17px; font-weight:800; color:#0f172a;">0</span>
                            </td>
                            <td style="padding:12px 18px;">
                                Logged Meetings
                            </td>
                            <td style="padding:12px 18px;">
                                Period Calendar
                            </td>
                            <td style="padding:12px 18px; text-align:right;">
                                <span style="font-weight:700; color:#00c6f0;">Active</span>
                            </td>
                        </tr>

                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:12px 18px;">
                                <div style="font-weight:700;">Support &amp; Service Tickets</div>
                                <div style="font-size:11px; color:#64748b;">Client issue tickets &amp; operational resolutions</div>
                            </td>
                            <td style="padding:12px 18px; text-align:right;">
                                <span id="liveTicketsTotal" style="font-size:17px; font-weight:800; color:#0f172a;">0</span>
                            </td>
                            <td style="padding:12px 18px;">
                                <span id="liveTicketsOpen" style="font-weight:700; color:#00a2d6;">0</span> Active Open
                            </td>
                            <td style="padding:12px 18px;">
                                <span id="liveTicketsResolved" style="font-weight:600; color:#64748b;">0</span> Resolved
                            </td>
                            <td style="padding:12px 18px; text-align:right;">
                                <span id="liveTicketsRate" style="font-weight:800; color:#00c6f0;">0.0%</span>
                            </td>
                        </tr>

                        <tr>
                            <td style="padding:12px 18px;">
                                <div style="font-weight:700;">Product Verticals &amp; Articles</div>
                                <div style="font-size:11px; color:#64748b;">Live corporate services &amp; knowledge articles</div>
                            </td>
                            <td style="padding:12px 18px; text-align:right;">
                                <span id="liveArticlesTotal" style="font-size:17px; font-weight:800; color:#0f172a;">0</span>
                            </td>
                            <td style="padding:12px 18px;">
                                Active Catalog Entries
                            </td>
                            <td style="padding:12px 18px;">
                                Enterprise Solutions
                            </td>
                            <td style="padding:12px 18px; text-align:right;">
                                <span style="font-weight:700; color:#00c6f0;">100%</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-xs-12" style="margin-bottom:16px;">
                <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.02);">
                    <div style="padding:12px 16px; background:#ffffff; border-bottom:2px solid #00c6f0; display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:13px; font-weight:800; color:#0f172a; text-transform:uppercase;">Recent Outbound Call Activity</span>
                        <span id="liveCallBadge" style="font-size:11px; font-weight:700; color:#00c6f0;">0 Calls</span>
                    </div>
                    <div class="table-responsive" style="margin:0;">
                        <table class="table table-hover" style="margin-bottom:0; font-size:12px;">
                            <thead>
                                <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0; color:#475569; font-size:11px; text-transform:uppercase;">
                                    <th style="padding:8px 14px; font-weight:700;">Customer</th>
                                    <th style="padding:8px 14px; font-weight:700;">Phone</th>
                                    <th style="padding:8px 14px; font-weight:700;">Status</th>
                                    <th style="padding:8px 14px; font-weight:700; text-align:right;">Duration</th>
                                </tr>
                            </thead>
                            <tbody id="liveRecentCallsTbody">
                                <tr>
                                    <td colspan="4" style="text-align:center; padding:24px 0; color:#94a3b8;">No calls logged for this filter</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xs-12" style="margin-bottom:16px;">
                <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.02);">
                    <div style="padding:12px 16px; background:#ffffff; border-bottom:2px solid #00c6f0; display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:13px; font-weight:800; color:#0f172a; text-transform:uppercase;">Recent Lead Pipeline Activity</span>
                        <span id="liveLeadBadge" style="font-size:11px; font-weight:700; color:#00c6f0;">0 Leads</span>
                    </div>
                    <div class="table-responsive" style="margin:0;">
                        <table class="table table-hover" style="margin-bottom:0; font-size:12px;">
                            <thead>
                                <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0; color:#475569; font-size:11px; text-transform:uppercase;">
                                    <th style="padding:8px 14px; font-weight:700;">Customer / Company</th>
                                    <th style="padding:8px 14px; font-weight:700;">Contact POC</th>
                                    <th style="padding:8px 14px; font-weight:700;">Stage</th>
                                    <th style="padding:8px 14px; font-weight:700; text-align:right;">Date</th>
                                </tr>
                            </thead>
                            <tbody id="liveRecentLeadsTbody">
                                <tr>
                                    <td colspan="4" style="text-align:center; padding:24px 0; color:#94a3b8;">No leads recorded</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

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
                    $('#liveCallsInProcess').text(resp.calls.in_process);
                    $('#liveCallsOther').text(resp.calls.other);
                    $('#liveCallBadge').text(resp.calls.total + ' Calls');
                    var callRate = resp.calls.total > 0 ? ((resp.calls.connected / resp.calls.total) * 100).toFixed(1) : '0.0';
                    $('#liveCallsRate').text(callRate + '%');

                    var callsHtml = '';
                    if (resp.calls.latest && resp.calls.latest.length > 0) {
                        for (var i = 0; i < resp.calls.latest.length; i++) {
                            var cl = resp.calls.latest[i];
                            var stColor = cl.status === 'connected' ? '#00a2d6' : (cl.status === 'in_process' ? '#00c6f0' : '#64748b');
                            callsHtml += '<tr style="border-bottom:1px solid #f1f5f9;">' +
                                '<td style="padding:8px 14px; font-weight:600;">' + (cl.customer_name ? cl.customer_name : 'Unknown') + '</td>' +
                                '<td style="padding:8px 14px; color:#475569;">' + (cl.phone_number ? cl.phone_number : '') + '</td>' +
                                '<td style="padding:8px 14px; font-weight:700; color:' + stColor + '; text-transform:capitalize;">' + cl.status + '</td>' +
                                '<td style="padding:8px 14px; text-align:right; color:#0f172a;">' + (cl.duration ? cl.duration + 's' : '0s') + '</td>' +
                            '</tr>';
                        }
                    } else {
                        callsHtml = '<tr><td colspan="4" style="text-align:center; padding:24px 0; color:#94a3b8;">No recent calls found</td></tr>';
                    }
                    $('#liveRecentCallsTbody').html(callsHtml);

                    $('#liveLeadsTotal').text(resp.leads.total);
                    $('#liveLeadsQualified').text(resp.leads.qualified);
                    $('#liveLeadsDatabase').text(resp.leads.database);
                    $('#liveLeadBadge').text(resp.leads.total + ' Leads');
                    var leadRate = resp.leads.total > 0 ? ((resp.leads.qualified / resp.leads.total) * 100).toFixed(1) : '0.0';
                    $('#liveLeadsRate').text(leadRate + '%');

                    var leadsHtml = '';
                    if (resp.leads.latest && resp.leads.latest.length > 0) {
                        for (var k = 0; k < resp.leads.latest.length; k++) {
                            var ld = resp.leads.latest[k];
                            leadsHtml += '<tr style="border-bottom:1px solid #f1f5f9;">' +
                                '<td style="padding:8px 14px; font-weight:600;">' + (ld.customer ? ld.customer : 'N/A') + '</td>' +
                                '<td style="padding:8px 14px; color:#475569;">' + (ld.poc ? ld.poc : '-') + '</td>' +
                                '<td style="padding:8px 14px; font-weight:600; color:#00a2d6;">' + (ld.lead_stage ? ld.lead_stage : 'Database') + '</td>' +
                                '<td style="padding:8px 14px; text-align:right; color:#64748b;">' + (ld.created ? ld.created : '-') + '</td>' +
                            '</tr>';
                        }
                    } else {
                        leadsHtml = '<tr><td colspan="4" style="text-align:center; padding:24px 0; color:#94a3b8;">No recent leads</td></tr>';
                    }
                    $('#liveRecentLeadsTbody').html(leadsHtml);

                    $('#liveOrdersTotal').text(resp.orders.total);
                    $('#liveOrdersPending').text(resp.orders.pending);
                    $('#liveOrdersCompleted').text(resp.orders.completed);
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
                    var tktRate = resp.tickets.total > 0 ? ((resp.tickets.resolved / resp.tickets.total) * 100).toFixed(1) : '0.0';
                    $('#liveTicketsRate').text(tktRate + '%');

                    $('#liveArticlesTotal').text(resp.articles.total);
                }
            },
            error: function() {
                $('#liveRefreshIcon').removeClass('fa-spin');
            }
        });
    }

    $('.live-scope-btn').on('click', function() {
        $('.live-scope-btn').removeClass('active').css({ 'background': 'transparent', 'color': '#00c6f0' });
        $(this).addClass('active').css({ 'background': '#00c6f0', 'color': '#ffffff' });
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
