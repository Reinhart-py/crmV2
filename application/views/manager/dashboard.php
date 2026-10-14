<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<!-- Full Screen Executive Dashboard Container -->
<div class="content-wrapper full-screen-dash">

    <!-- Dashboard Top Header Bar with Theme Switcher -->
    <div class="dash-header-bar">
        <h1 class="dash-header-title">
            <i class="fa fa-dashboard" style="color:#b91c1c;"></i> Dashboard
        </h1>
        <div>
            <button type="button" class="dash-theme-btn" id="dashThemeToggle" title="Toggle Light / Dark Mode">
                <i class="fa fa-moon-o"></i> <span>Dark / Light Mode</span>
            </button>
        </div>
    </div>

    <!-- Main Dashboard Content -->
    <div class="dash-content-body">

        <!-- 1. Top 4 Statistic Cards Row -->
        <div class="row">
            <!-- MY LEADS (Navy) -->
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <a href="<?php echo base_url(); ?>manager/leads" class="dash-kpi-card dash-kpi-navy" id="cardMyLeads">
                    <div class="dash-kpi-top">
                        <div>
                            <div class="dash-kpi-value"><?php echo isset($dashboard_count['count_leads']) ? (int)$dashboard_count['count_leads'] : 0; ?></div>
                            <div class="dash-kpi-label">MY LEADS</div>
                        </div>
                        <div class="dash-kpi-icon"><i class="ion ion-bag"></i></div>
                    </div>
                    <div class="dash-kpi-footer">
                        <span>View Details</span> <i class="fa fa-arrow-circle-right"></i>
                    </div>
                </a>
            </div>

            <!-- MY VERTICALS (Forest Green) -->
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <a href="<?php echo base_url(); ?>manager/verticals" class="dash-kpi-card dash-kpi-forest" id="cardMyVerticals">
                    <div class="dash-kpi-top">
                        <div>
                            <div class="dash-kpi-value"><?php echo isset($dashboard_count['count_vetricals']) ? (int)$dashboard_count['count_vetricals'] : 0; ?></div>
                            <div class="dash-kpi-label">MY VERTICALS</div>
                        </div>
                        <div class="dash-kpi-icon"><i class="ion ion-stats-bars"></i></div>
                    </div>
                    <div class="dash-kpi-footer">
                        <span>View Details</span> <i class="fa fa-arrow-circle-right"></i>
                    </div>
                </a>
            </div>

            <!-- TEAM USERS (Warm Brown) -->
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <a href="<?php echo base_url(); ?>manager/team/members" class="dash-kpi-card dash-kpi-amber" id="cardTeamUsers">
                    <div class="dash-kpi-top">
                        <div>
                            <div class="dash-kpi-value"><?php echo isset($dashboard_Tcount['count_user']) ? (int)$dashboard_Tcount['count_user'] : 0; ?></div>
                            <div class="dash-kpi-label">TEAM USERS</div>
                        </div>
                        <div class="dash-kpi-icon"><i class="ion ion-person-add"></i></div>
                    </div>
                    <div class="dash-kpi-footer">
                        <span>View Details</span> <i class="fa fa-arrow-circle-right"></i>
                    </div>
                </a>
            </div>

            <!-- TEAM LEADS (Dark Crimson) -->
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <a href="<?php echo base_url(); ?>manager/team/leads" class="dash-kpi-card dash-kpi-crimson" id="cardTeamLeads">
                    <div class="dash-kpi-top">
                        <div>
                            <div class="dash-kpi-value"><?php echo isset($dashboard_Tcount['count_leads']) ? (int)$dashboard_Tcount['count_leads'] : 0; ?></div>
                            <div class="dash-kpi-label">TEAM LEADS</div>
                        </div>
                        <div class="dash-kpi-icon"><i class="ion ion-pie-graph"></i></div>
                    </div>
                    <div class="dash-kpi-footer">
                        <span>View Details</span> <i class="fa fa-arrow-circle-right"></i>
                    </div>
                </a>
            </div>
        </div>

        <!-- 2. Projections Charts Row (My Projection & Team Projection) -->
        <div class="row">
            <!-- My Projection -->
            <div class="col-md-6 col-xs-12">
                <div class="dash-chart-card">
                    <div class="dash-chart-header">
                        <h4 class="dash-chart-title">
                            <i class="fa fa-line-chart"></i> My Projection
                        </h4>
                    </div>
                    <div class="dash-chart-body">
                        <canvas id="barChart2" height="230"></canvas>
                    </div>
                </div>
            </div>

            <!-- Team Projection -->
            <div class="col-md-6 col-xs-12">
                <div class="dash-chart-card">
                    <div class="dash-chart-header">
                        <h4 class="dash-chart-title">
                            <i class="fa fa-line-chart"></i> Team Projection
                        </h4>
                    </div>
                    <div class="dash-chart-body">
                        <canvas id="barChart4" height="230"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Section Header: Operational Activity & Closures Breakdown -->
        <div class="dash-section-divider">
            <h3 class="dash-section-title">
                <span class="dash-section-title-icon">
                    <span></span><span></span>
                </span>
                Operational Activity &amp; Closures Breakdown
            </h3>
            <span class="dash-section-subtitle">Real-time Pipeline Overview</span>
        </div>

        <?php
            // Calculate totals strictly from real database metrics
            $total_my_f = !empty($dashboard_f) ? (int)array_sum(array_slice($dashboard_f, 0, 5)) : 0;
            $total_my_m = !empty($dashboard_m) ? (int)array_sum(array_slice($dashboard_m, 0, 5)) : 0;
            $total_my_c = !empty($dashboard_c['total_all']) ? (int)$dashboard_c['total_all'] : (!empty($dashboard_c) ? (int)array_sum(array_slice($dashboard_c, 0, 5)) : 0);
            $total_my_vfu = !empty($dashboard_vfu) ? (int)array_sum(array_slice($dashboard_vfu, 0, 5)) : 0;

            $total_team_f = !empty($dashboard_Tf) ? (int)array_sum(array_slice($dashboard_Tf, 0, 5)) : 0;
            $total_team_m = !empty($dashboard_Tm) ? (int)array_sum(array_slice($dashboard_Tm, 0, 5)) : 0;
            $total_team_c = !empty($dashboard_Tc['total_all']) ? (int)$dashboard_Tc['total_all'] : (!empty($dashboard_Tc) ? (int)array_sum(array_slice($dashboard_Tc, 0, 5)) : 0);
            $total_team_vfu = !empty($dashboard_Tvfu) ? (int)array_sum(array_slice($dashboard_Tvfu, 0, 5)) : 0; ?>

        <!-- 4. Row 1: My Operational Metrics (4 Cards) -->
        <div class="row">
            <!-- Card 1: My Followups -->
            <div class="col-sm-3 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-calendar-check-o"></i> My Followups</h4>
                        <span class="card-total-pill"><?php echo $total_my_f; ?> Total</span>
                    </div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:12%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:20%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:24%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:24%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads/followups?type=1" class="crm-modern-stat-chip" data-drilldown="my_followups" data-type="1" data-label="My Followups - All Missed">
                                <span>All Missed</span>
                                <span class="badge bg-red"><?php echo isset($dashboard_f['total_missed']) ? $dashboard_f['total_missed'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads/followups?type=2" class="crm-modern-stat-chip" data-drilldown="my_followups" data-type="2" data-label="My Followups - Last 7 Days">
                                <span>Last 7 Days</span>
                                <span class="badge bg-orange"><?php echo isset($dashboard_f['total_lastweek']) ? $dashboard_f['total_lastweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads/followups?type=3" class="crm-modern-stat-chip" data-drilldown="my_followups" data-type="3" data-label="My Followups - Today">
                                <span>Today</span>
                                <span class="badge bg-skyblue"><?php echo isset($dashboard_f['total_today']) ? $dashboard_f['total_today'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads/followups?type=4" class="crm-modern-stat-chip" data-drilldown="my_followups" data-type="4" data-label="My Followups - Next 7 Days">
                                <span>Next 7 Days</span>
                                <span class="badge bg-blue"><?php echo isset($dashboard_f['total_nextweek']) ? $dashboard_f['total_nextweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads/followups?type=5" class="crm-modern-stat-chip" data-drilldown="my_followups" data-type="5" data-label="My Followups - All Future">
                                <span>All Future</span>
                                <span class="badge bg-green"><?php echo isset($dashboard_f['total_future']) ? $dashboard_f['total_future'] : 0; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Card 2: My Meetings -->
            <div class="col-sm-3 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-handshake-o"></i> My Meetings</h4>
                        <span class="card-total-pill"><?php echo $total_my_m; ?> Total</span>
                    </div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:10%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:18%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:22%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:26%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:24%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads/meetings?type=1" class="crm-modern-stat-chip" data-drilldown="my_meetings" data-type="1" data-label="My Meetings - All Missed">
                                <span>All Missed</span>
                                <span class="badge bg-red"><?php echo isset($dashboard_m['total_missed']) ? $dashboard_m['total_missed'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads/meetings?type=2" class="crm-modern-stat-chip" data-drilldown="my_meetings" data-type="2" data-label="My Meetings - Last 7 Days">
                                <span>Last 7 Days</span>
                                <span class="badge bg-orange"><?php echo isset($dashboard_m['total_lastweek']) ? $dashboard_m['total_lastweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads/meetings?type=3" class="crm-modern-stat-chip" data-drilldown="my_meetings" data-type="3" data-label="My Meetings - Today">
                                <span>Today</span>
                                <span class="badge bg-skyblue"><?php echo isset($dashboard_m['total_today']) ? $dashboard_m['total_today'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads/meetings?type=4" class="crm-modern-stat-chip" data-drilldown="my_meetings" data-type="4" data-label="My Meetings - Next 7 Days">
                                <span>Next 7 Days</span>
                                <span class="badge bg-blue"><?php echo isset($dashboard_m['total_nextweek']) ? $dashboard_m['total_nextweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads/meetings?type=5" class="crm-modern-stat-chip" data-drilldown="my_meetings" data-type="5" data-label="My Meetings - All Future">
                                <span>All Future</span>
                                <span class="badge bg-green"><?php echo isset($dashboard_m['total_future']) ? $dashboard_m['total_future'] : 0; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Card 3: My Closures -->
            <div class="col-sm-3 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-trophy"></i> My Closures</h4>
                        <span class="card-total-pill"><?php echo $total_my_c; ?> Total</span>
                    </div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:16%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:16%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:16%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:20%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:32%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads?closure=1" class="crm-modern-stat-chip" data-drilldown="my_closures" data-type="1" data-label="My Closures - All Missed">
                                <span>All Missed</span>
                                <span class="badge bg-red"><?php echo isset($dashboard_c['total_missed']) ? $dashboard_c['total_missed'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads?closure=2" class="crm-modern-stat-chip" data-drilldown="my_closures" data-type="2" data-label="My Closures - Last 7 Days">
                                <span>Last 7 Days</span>
                                <span class="badge bg-orange"><?php echo isset($dashboard_c['total_lastweek']) ? $dashboard_c['total_lastweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads?closure=3" class="crm-modern-stat-chip" data-drilldown="my_closures" data-type="3" data-label="My Closures - Today">
                                <span>Today</span>
                                <span class="badge bg-skyblue"><?php echo isset($dashboard_c['total_today']) ? $dashboard_c['total_today'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads?closure=4" class="crm-modern-stat-chip" data-drilldown="my_closures" data-type="4" data-label="My Closures - Next 7 Days">
                                <span>Next 7 Days</span>
                                <span class="badge bg-blue"><?php echo isset($dashboard_c['total_nextweek']) ? $dashboard_c['total_nextweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads?closure=5" class="crm-modern-stat-chip" data-drilldown="my_closures" data-type="5" data-label="My Closures - All Future">
                                <span>All Future</span>
                                <span class="badge bg-green"><?php echo isset($dashboard_c['total_future']) ? $dashboard_c['total_future'] : 0; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Card 4: My Vertical FU -->
            <div class="col-sm-3 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-briefcase"></i> My Vertical FU</h4>
                        <span class="card-total-pill"><?php echo $total_my_vfu; ?> Total</span>
                    </div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:10%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:20%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:30%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:20%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads?vertical_fu=1" class="crm-modern-stat-chip" data-drilldown="my_vertical_fu" data-type="1" data-label="My Vertical FU - All Missed">
                                <span>All Missed</span>
                                <span class="badge bg-red"><?php echo isset($dashboard_vfu['total_missed']) ? $dashboard_vfu['total_missed'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads?vertical_fu=2" class="crm-modern-stat-chip" data-drilldown="my_vertical_fu" data-type="2" data-label="My Vertical FU - Last 7 Days">
                                <span>Last 7 Days</span>
                                <span class="badge bg-orange"><?php echo isset($dashboard_vfu['total_lastweek']) ? $dashboard_vfu['total_lastweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads?vertical_fu=3" class="crm-modern-stat-chip" data-drilldown="my_vertical_fu" data-type="3" data-label="My Vertical FU - Today">
                                <span>Today</span>
                                <span class="badge bg-skyblue"><?php echo isset($dashboard_vfu['total_today']) ? $dashboard_vfu['total_today'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads?vertical_fu=4" class="crm-modern-stat-chip" data-drilldown="my_vertical_fu" data-type="4" data-label="My Vertical FU - Next 7 Days">
                                <span>Next 7 Days</span>
                                <span class="badge bg-blue"><?php echo isset($dashboard_vfu['total_nextweek']) ? $dashboard_vfu['total_nextweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/leads?vertical_fu=5" class="crm-modern-stat-chip" data-drilldown="my_vertical_fu" data-type="5" data-label="My Vertical FU - All Future">
                                <span>All Future</span>
                                <span class="badge bg-green"><?php echo isset($dashboard_vfu['total_future']) ? $dashboard_vfu['total_future'] : 0; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- 5. Row 2: Team Operational Metrics (4 Cards) -->
        <div class="row">
            <!-- Card 5: Team Followups -->
            <div class="col-sm-3 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-calendar-check-o"></i> Team Followups</h4>
                        <span class="card-total-pill"><?php echo $total_team_f; ?> Total</span>
                    </div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:10%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:18%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:16%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:26%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:30%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/followups?type=1" class="crm-modern-stat-chip" data-drilldown="team_followups" data-type="1" data-label="Team Followups - All Missed">
                                <span>All Missed</span>
                                <span class="badge bg-red"><?php echo isset($dashboard_Tf['total_missed']) ? $dashboard_Tf['total_missed'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/followups?type=2" class="crm-modern-stat-chip" data-drilldown="team_followups" data-type="2" data-label="Team Followups - Last 7 Days">
                                <span>Last 7 Days</span>
                                <span class="badge bg-orange"><?php echo isset($dashboard_Tf['total_lastweek']) ? $dashboard_Tf['total_lastweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/followups?type=3" class="crm-modern-stat-chip" data-drilldown="team_followups" data-type="3" data-label="Team Followups - Today">
                                <span>Today</span>
                                <span class="badge bg-skyblue"><?php echo isset($dashboard_Tf['total_today']) ? $dashboard_Tf['total_today'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/followups?type=4" class="crm-modern-stat-chip" data-drilldown="team_followups" data-type="4" data-label="Team Followups - Next 7 Days">
                                <span>Next 7 Days</span>
                                <span class="badge bg-blue"><?php echo isset($dashboard_Tf['total_nextweek']) ? $dashboard_Tf['total_nextweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/followups?type=5" class="crm-modern-stat-chip" data-drilldown="team_followups" data-type="5" data-label="Team Followups - All Future">
                                <span>All Future</span>
                                <span class="badge bg-green"><?php echo isset($dashboard_Tf['total_future']) ? $dashboard_Tf['total_future'] : 0; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Card 6: Team Meetings -->
            <div class="col-sm-3 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-handshake-o"></i> Team Meetings</h4>
                        <span class="card-total-pill"><?php echo $total_team_m; ?> Total</span>
                    </div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:8%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:14%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:26%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:32%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/meetings?type=1" class="crm-modern-stat-chip" data-drilldown="team_meetings" data-type="1" data-label="Team Meetings - All Missed">
                                <span>All Missed</span>
                                <span class="badge bg-red"><?php echo isset($dashboard_Tm['total_missed']) ? $dashboard_Tm['total_missed'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/meetings?type=2" class="crm-modern-stat-chip" data-drilldown="team_meetings" data-type="2" data-label="Team Meetings - Last 7 Days">
                                <span>Last 7 Days</span>
                                <span class="badge bg-orange"><?php echo isset($dashboard_Tm['total_lastweek']) ? $dashboard_Tm['total_lastweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/meetings?type=3" class="crm-modern-stat-chip" data-drilldown="team_meetings" data-type="3" data-label="Team Meetings - Today">
                                <span>Today</span>
                                <span class="badge bg-skyblue"><?php echo isset($dashboard_Tm['total_today']) ? $dashboard_Tm['total_today'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/meetings?type=4" class="crm-modern-stat-chip" data-drilldown="team_meetings" data-type="4" data-label="Team Meetings - Next 7 Days">
                                <span>Next 7 Days</span>
                                <span class="badge bg-blue"><?php echo isset($dashboard_Tm['total_nextweek']) ? $dashboard_Tm['total_nextweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/meetings?type=5" class="crm-modern-stat-chip" data-drilldown="team_meetings" data-type="5" data-label="Team Meetings - All Future">
                                <span>All Future</span>
                                <span class="badge bg-green"><?php echo isset($dashboard_Tm['total_future']) ? $dashboard_Tm['total_future'] : 0; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Card 7: Team Closures -->
            <div class="col-sm-3 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-trophy"></i> Team Closures</h4>
                        <span class="card-total-pill"><?php echo $total_team_c; ?> Total</span>
                    </div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:15%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:10%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:10%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:15%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:50%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/leads?closure=1" class="crm-modern-stat-chip" data-drilldown="team_closures" data-type="1" data-label="Team Closures - All Missed">
                                <span>All Missed</span>
                                <span class="badge bg-red"><?php echo isset($dashboard_Tc['total_missed']) ? $dashboard_Tc['total_missed'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/leads?closure=2" class="crm-modern-stat-chip" data-drilldown="team_closures" data-type="2" data-label="Team Closures - Last 7 Days">
                                <span>Last 7 Days</span>
                                <span class="badge bg-orange"><?php echo isset($dashboard_Tc['total_lastweek']) ? $dashboard_Tc['total_lastweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/leads?closure=3" class="crm-modern-stat-chip" data-drilldown="team_closures" data-type="3" data-label="Team Closures - Today">
                                <span>Today</span>
                                <span class="badge bg-skyblue"><?php echo isset($dashboard_Tc['total_today']) ? $dashboard_Tc['total_today'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/leads?closure=4" class="crm-modern-stat-chip" data-drilldown="team_closures" data-type="4" data-label="Team Closures - Next 7 Days">
                                <span>Next 7 Days</span>
                                <span class="badge bg-blue"><?php echo isset($dashboard_Tc['total_nextweek']) ? $dashboard_Tc['total_nextweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/leads?closure=5" class="crm-modern-stat-chip" data-drilldown="team_closures" data-type="5" data-label="Team Closures - All Future">
                                <span>All Future</span>
                                <span class="badge bg-green"><?php echo isset($dashboard_Tc['total_future']) ? $dashboard_Tc['total_future'] : 0; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Card 8: Team Vertical FU -->
            <div class="col-sm-3 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-briefcase"></i> Team Vertical FU</h4>
                        <span class="card-total-pill"><?php echo $total_team_vfu; ?> Total</span>
                    </div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:12%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:14%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:20%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:34%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/verticals?type=1" class="crm-modern-stat-chip" data-drilldown="team_verticals" data-type="1" data-label="Team Vertical FU - All Missed">
                                <span>All Missed</span>
                                <span class="badge bg-red"><?php echo isset($dashboard_Tvfu['total_missed']) ? $dashboard_Tvfu['total_missed'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/verticals?type=2" class="crm-modern-stat-chip" data-drilldown="team_verticals" data-type="2" data-label="Team Vertical FU - Last 7 Days">
                                <span>Last 7 Days</span>
                                <span class="badge bg-orange"><?php echo isset($dashboard_Tvfu['total_lastweek']) ? $dashboard_Tvfu['total_lastweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/verticals?type=3" class="crm-modern-stat-chip" data-drilldown="team_verticals" data-type="3" data-label="Team Vertical FU - Today">
                                <span>Today</span>
                                <span class="badge bg-skyblue"><?php echo isset($dashboard_Tvfu['total_today']) ? $dashboard_Tvfu['total_today'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/verticals?type=4" class="crm-modern-stat-chip" data-drilldown="team_verticals" data-type="4" data-label="Team Vertical FU - Next 7 Days">
                                <span>Next 7 Days</span>
                                <span class="badge bg-blue"><?php echo isset($dashboard_Tvfu['total_nextweek']) ? $dashboard_Tvfu['total_nextweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/team/verticals?type=5" class="crm-modern-stat-chip" data-drilldown="team_verticals" data-type="5" data-label="Team Vertical FU - All Future">
                                <span>All Future</span>
                                <span class="badge bg-green"><?php echo isset($dashboard_Tvfu['total_future']) ? $dashboard_Tvfu['total_future'] : 0; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- 6. Dedicated Interactive Dashboard Drill-Down View Section -->
        <div class="dash-drilldown-panel" id="dashDrilldownPanel">
            <div class="dash-drilldown-header">
                <h4 class="dash-drilldown-title">
                    <i class="fa fa-list-alt" style="color:#b91c1c;"></i>
                    <span id="drilldownTitle">Operational Metric Drill-Down</span>
                    <span class="badge bg-blue" id="drilldownCountBadge" style="margin-left:8px; font-size:12px;">0 Records</span>
                </h4>
                <div style="display:flex; align-items:center; gap:12px;">
                    <a href="#" class="btn btn-default btn-xs" id="drilldownExternalLink" target="_blank" style="border-radius:4px; font-weight:600;">
                        <i class="fa fa-external-link"></i> Open Full Module
                    </a>
                    <button type="button" class="dash-drilldown-close" id="dashDrilldownClose" title="Close Drill-Down">&times;</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="dash-drilldown-table table table-hover">
                    <thead>
                        <tr>
                            <th>Ref #</th>
                            <th>Entity / Client</th>
                            <th>POC &amp; Phone</th>
                            <th>Target Date / Deadline</th>
                            <th>Status</th>
                            <th>Assigned Agent</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="drilldownTableBody">
                        <!-- Populated dynamically on chip click -->
                    </tbody>
                </table>
            </div>
        </div>



    </div>
</div>

<!-- Projection Chart Configuration & Scripting -->
<script>
$(function () {
    // 1. Dark / Light Mode Toggle logic
    function setDashTheme(theme) {
        if (theme === 'dark') {
            $('html, body, .main-header').attr('data-theme', 'dark');
            $('#dashThemeToggle').html('<i class="fa fa-sun-o"></i> <span>Light Mode</span>');
            localStorage.setItem('crm_theme_preference', 'dark');
        } else {
            $('html, body, .main-header').removeAttr('data-theme');
            $('#dashThemeToggle').html('<i class="fa fa-moon-o"></i> <span>Dark Mode</span>');
            localStorage.setItem('crm_theme_preference', 'light');
        }
    }

    var savedTheme = localStorage.getItem('crm_theme_preference') || 'light';
    setDashTheme(savedTheme);

    $('#dashThemeToggle').on('click', function(e) {
        e.preventDefault();
        var current = $('html').attr('data-theme');
        setDashTheme(current === 'dark' ? 'light' : 'dark');
    });

    // 2. Bar Chart Data Preparation (Current month & Future months only)
    var months = <?php 
        if (!empty($dashboard_MyPro['month_labels'])) {
            echo json_encode($dashboard_MyPro['month_labels']);
        } else {
            echo json_encode(array('Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'));
        }
    ?>;
    
    // My Projection Data (Jan - Dec)
    var myProData = [
        <?php echo isset($dashboard_MyPro['lead_number']['pt_0']) ? (int)$dashboard_MyPro['lead_number']['pt_0'] : 0; ?>,
        <?php echo isset($dashboard_MyPro['lead_number']['pt_1']) ? (int)$dashboard_MyPro['lead_number']['pt_1'] : 0; ?>,
        <?php echo isset($dashboard_MyPro['lead_number']['pt_2']) ? (int)$dashboard_MyPro['lead_number']['pt_2'] : 0; ?>,
        <?php echo isset($dashboard_MyPro['lead_number']['pt_3']) ? (int)$dashboard_MyPro['lead_number']['pt_3'] : 0; ?>,
        <?php echo isset($dashboard_MyPro['lead_number']['pt_4']) ? (int)$dashboard_MyPro['lead_number']['pt_4'] : 0; ?>,
        <?php echo isset($dashboard_MyPro['lead_number']['pt_5']) ? (int)$dashboard_MyPro['lead_number']['pt_5'] : 0; ?>,
        <?php echo isset($dashboard_MyPro['lead_number']['pt_6']) ? (int)$dashboard_MyPro['lead_number']['pt_6'] : 0; ?>,
        <?php echo isset($dashboard_MyPro['lead_number']['pt_7']) ? (int)$dashboard_MyPro['lead_number']['pt_7'] : 0; ?>,
        <?php echo isset($dashboard_MyPro['lead_number']['pt_8']) ? (int)$dashboard_MyPro['lead_number']['pt_8'] : 0; ?>,
        <?php echo isset($dashboard_MyPro['lead_number']['pt_9']) ? (int)$dashboard_MyPro['lead_number']['pt_9'] : 0; ?>,
        <?php echo isset($dashboard_MyPro['lead_number']['pt_10']) ? (int)$dashboard_MyPro['lead_number']['pt_10'] : 0; ?>,
        <?php echo isset($dashboard_MyPro['lead_number']['pt_11']) ? (int)$dashboard_MyPro['lead_number']['pt_11'] : 0; ?>
    ];

    // Team Projection Data (Jan - Dec)
    var teamProData = [
        <?php echo isset($dashboard_TeamPro['lead_number']['pt_0']) ? (int)$dashboard_TeamPro['lead_number']['pt_0'] : 0; ?>,
        <?php echo isset($dashboard_TeamPro['lead_number']['pt_1']) ? (int)$dashboard_TeamPro['lead_number']['pt_1'] : 0; ?>,
        <?php echo isset($dashboard_TeamPro['lead_number']['pt_2']) ? (int)$dashboard_TeamPro['lead_number']['pt_2'] : 0; ?>,
        <?php echo isset($dashboard_TeamPro['lead_number']['pt_3']) ? (int)$dashboard_TeamPro['lead_number']['pt_3'] : 0; ?>,
        <?php echo isset($dashboard_TeamPro['lead_number']['pt_4']) ? (int)$dashboard_TeamPro['lead_number']['pt_4'] : 0; ?>,
        <?php echo isset($dashboard_TeamPro['lead_number']['pt_5']) ? (int)$dashboard_TeamPro['lead_number']['pt_5'] : 0; ?>,
        <?php echo isset($dashboard_TeamPro['lead_number']['pt_6']) ? (int)$dashboard_TeamPro['lead_number']['pt_6'] : 0; ?>,
        <?php echo isset($dashboard_TeamPro['lead_number']['pt_7']) ? (int)$dashboard_TeamPro['lead_number']['pt_7'] : 0; ?>,
        <?php echo isset($dashboard_TeamPro['lead_number']['pt_8']) ? (int)$dashboard_TeamPro['lead_number']['pt_8'] : 0; ?>,
        <?php echo isset($dashboard_TeamPro['lead_number']['pt_9']) ? (int)$dashboard_TeamPro['lead_number']['pt_9'] : 0; ?>,
        <?php echo isset($dashboard_TeamPro['lead_number']['pt_10']) ? (int)$dashboard_TeamPro['lead_number']['pt_10'] : 0; ?>,
        <?php echo isset($dashboard_TeamPro['lead_number']['pt_11']) ? (int)$dashboard_TeamPro['lead_number']['pt_11'] : 0; ?>
    ];

    var chartOptions = {
        scaleBeginAtZero: true,
        scaleShowGridLines: true,
        scaleGridLineColor: 'rgba(0,0,0,.04)',
        scaleGridLineWidth: 1,
        scaleShowHorizontalLines: true,
        scaleShowVerticalLines: false,
        barShowStroke: true,
        barStrokeWidth: 1,
        barValueSpacing: 8,
        barDatasetSpacing: 2,
        responsive: true,
        maintainAspectRatio: false
    };

    // Initialize My Projection Chart
    var canvas2 = document.getElementById('barChart2');
    if (canvas2) {
        var ctx2 = canvas2.getContext('2d');
        new Chart(ctx2).Bar({
            labels: months,
            datasets: [{
                label: 'My Projection',
                fillColor: '#991b1b', // Exact deep crimson bars matching screenshot
                strokeColor: '#7f1d1d',
                highlightFill: '#b91c1c',
                highlightStroke: '#7f1d1d',
                data: myProData
            }]
        }, chartOptions);
    }

    // Initialize Team Projection Chart
    var canvas4 = document.getElementById('barChart4');
    if (canvas4) {
        var ctx4 = canvas4.getContext('2d');
        new Chart(ctx4).Bar({
            labels: months,
            datasets: [{
                label: 'Team Projection',
                fillColor: '#991b1b', // Exact deep crimson bars matching screenshot
                strokeColor: '#7f1d1d',
                highlightFill: '#b91c1c',
                highlightStroke: '#7f1d1d',
                data: teamProData
            }]
        }, chartOptions);
    }

    // 3. Stat chips and cards navigate cleanly
    $('.crm-modern-stat-chip').on('click', function(e) {
        var targetUrl = $(this).attr('href');
        if (targetUrl && targetUrl !== '#' && targetUrl.indexOf('javascript') === -1) {
            window.location.href = targetUrl;
        }
    });
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
