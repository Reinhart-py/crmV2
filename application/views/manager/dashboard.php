<?php require_once(APPPATH."views/manager/elements/header.php"); ?>


<div class="content-wrapper full-screen-dash">

    
    <div class="dash-header-bar">
        <h1 class="dash-header-title">
            <svg class="crm-svg-ico" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0072f0" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-3px; margin-right:8px;"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="5" rx="1"></rect><rect x="14" y="12" width="7" height="9" rx="1"></rect><rect x="3" y="16" width="7" height="5" rx="1"></rect></svg> Dashboard
        </h1>
        <div>
            <button type="button" class="dash-theme-btn" id="dashThemeToggle" title="Toggle Light / Dark Mode">
                <i class="fa fa-moon-o"></i> <span>Dark / Light Mode</span>
            </button>
        </div>
    </div>

    
    <div class="dash-content-body">

        
        <div class="row">
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <a href="<?php echo base_url(); ?>manager/leads" class="dash-kpi-card dash-kpi-navy" id="cardMyDatabase">
                    <div class="dash-kpi-top">
                        <div>
                            <div class="dash-kpi-value"><?php echo isset($dashboard_count['count_database']) ? (int)$dashboard_count['count_database'] : 0; ?></div>
                            <div class="dash-kpi-label">My Database</div>
                        </div>
                        <div class="dash-kpi-icon"><i class="fa fa-database"></i></div>
                    </div>
                    <div class="dash-kpi-footer">
                        <span>View Database</span> <i class="fa fa-arrow-circle-right"></i>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-sm-6 col-xs-12">
                <a href="<?php echo base_url(); ?>manager/verticals" class="dash-kpi-card dash-kpi-forest" id="cardMyVerticals">
                    <div class="dash-kpi-top">
                        <div>
                            <div class="dash-kpi-value"><?php echo isset($dashboard_count['count_verticals']) ? (int)$dashboard_count['count_verticals'] : (isset($dashboard_count['count_vetricals']) ? (int)$dashboard_count['count_vetricals'] : 0); ?></div>
                            <div class="dash-kpi-label">My Vertical</div>
                        </div>
                        <div class="dash-kpi-icon"><i class="ion ion-stats-bars"></i></div>
                    </div>
                    <div class="dash-kpi-footer">
                        <span>View Verticals</span> <i class="fa fa-arrow-circle-right"></i>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-sm-6 col-xs-12">
                <a href="<?php echo base_url(); ?>manager/team/members" class="dash-kpi-card dash-kpi-amber" id="cardTeamUsers">
                    <div class="dash-kpi-top">
                        <div>
                            <div class="dash-kpi-value"><?php echo isset($dashboard_Tcount['count_user']) ? (int)$dashboard_Tcount['count_user'] : 0; ?></div>
                            <div class="dash-kpi-label">Team Users</div>
                        </div>
                        <div class="dash-kpi-icon"><i class="ion ion-person-add"></i></div>
                    </div>
                    <div class="dash-kpi-footer">
                        <span>View Members</span> <i class="fa fa-arrow-circle-right"></i>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-sm-6 col-xs-12">
                <a href="<?php echo base_url(); ?>manager/team/verticals" class="dash-kpi-card dash-kpi-crimson" id="cardTeamVerticals">
                    <div class="dash-kpi-top">
                        <div>
                            <div class="dash-kpi-value"><?php echo isset($dashboard_Tcount['count_verticals']) ? (int)$dashboard_Tcount['count_verticals'] : (isset($dashboard_Tcount['count_vetricals']) ? (int)$dashboard_Tcount['count_vetricals'] : 0); ?></div>
                            <div class="dash-kpi-label">Team Verticals</div>
                        </div>
                        <div class="dash-kpi-icon"><i class="fa fa-cubes"></i></div>
                    </div>
                    <div class="dash-kpi-footer">
                        <span>View Verticals</span> <i class="fa fa-arrow-circle-right"></i>
                    </div>
                </a>
            </div>
        </div>

        <div class="row" style="margin-top: 10px; margin-bottom: 5px;">
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <a href="<?php echo base_url(); ?>manager/leads/view_leads" class="dash-kpi-card dash-kpi-navy" id="cardMyLeads">
                    <div class="dash-kpi-top">
                        <div>
                            <div class="dash-kpi-value"><?php echo isset($dashboard_count['count_leads']) ? (int)$dashboard_count['count_leads'] : 0; ?></div>
                            <div class="dash-kpi-label">My Leads</div>
                        </div>
                        <div class="dash-kpi-icon"><i class="fa fa-bullseye"></i></div>
                    </div>
                    <div class="dash-kpi-footer">
                        <span>View Leads</span> <i class="fa fa-arrow-circle-right"></i>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-sm-6 col-xs-12">
                <a href="<?php echo base_url(); ?>manager/orders/my" class="dash-kpi-card dash-kpi-forest" id="cardMyOrders">
                    <div class="dash-kpi-top">
                        <div>
                            <div class="dash-kpi-value"><?php echo isset($dashboard_count['count_orders']) ? (int)$dashboard_count['count_orders'] : 0; ?></div>
                            <div class="dash-kpi-label">My Orders</div>
                        </div>
                        <div class="dash-kpi-icon"><i class="fa fa-shopping-cart"></i></div>
                    </div>
                    <div class="dash-kpi-footer">
                        <span>View Orders</span> <i class="fa fa-arrow-circle-right"></i>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-sm-6 col-xs-12">
                <a href="<?php echo base_url(); ?>manager/team/leads" class="dash-kpi-card dash-kpi-amber" id="cardTeamLeads">
                    <div class="dash-kpi-top">
                        <div>
                            <div class="dash-kpi-value"><?php echo isset($dashboard_Tcount['count_leads']) ? (int)$dashboard_Tcount['count_leads'] : 0; ?></div>
                            <div class="dash-kpi-label">Team Leads</div>
                        </div>
                        <div class="dash-kpi-icon"><i class="ion ion-pie-graph"></i></div>
                    </div>
                    <div class="dash-kpi-footer">
                        <span>View Leads</span> <i class="fa fa-arrow-circle-right"></i>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-sm-6 col-xs-12">
                <a href="<?php echo base_url(); ?>manager/team/orders" class="dash-kpi-card dash-kpi-crimson" id="cardTeamOrders">
                    <div class="dash-kpi-top">
                        <div>
                            <div class="dash-kpi-value"><?php echo isset($dashboard_Tcount['count_orders']) ? (int)$dashboard_Tcount['count_orders'] : 0; ?></div>
                            <div class="dash-kpi-label">Team Orders</div>
                        </div>
                        <div class="dash-kpi-icon"><i class="fa fa-truck"></i></div>
                    </div>
                    <div class="dash-kpi-footer">
                        <span>View Orders</span> <i class="fa fa-arrow-circle-right"></i>
                    </div>
                </a>
            </div>
        </div>


        
        <?php
            $my_db_total = 0;
            if (!empty($dashboard_db_state)) {
                foreach($dashboard_db_state as $item) {
                    $my_db_total += (int)$item['lead_count'];
                }
            }
            $team_db_total = 0;
            if (!empty($dashboard_db_state_team)) {
                foreach($dashboard_db_state_team as $item) {
                    $team_db_total += (int)$item['lead_count'];
                }
            }
            $my_lead_total = 0;
            if (!empty($dashboard_lead_state)) {
                foreach($dashboard_lead_state as $item) {
                    $my_lead_total += (int)$item['lead_count'];
                }
            }
            $team_lead_total = 0;
            if (!empty($dashboard_lead_state_team)) {
                foreach($dashboard_lead_state_team as $item) {
                    $team_lead_total += (int)$item['lead_count'];
                }
            }
        ?>

        <div class="dash-section-divider" style="margin-top: 18px; margin-bottom: 12px; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h3 class="dash-section-title" style="margin-bottom:0;">
                    <span class="dash-section-title-icon">
                        <span></span><span></span>
                    </span>
                    <span id="titleStates">Database State</span>
                </h3>
            </div>
            <div class="crm-pager-controls" data-track="#pagerTrackStates" data-title="#titleStates" data-t1="Database State" data-t2="Lead State">
                <div class="crm-pager-dots">
                    <span class="crm-pager-dot active" data-page="0" title="Database State"></span>
                    <span class="crm-pager-dot" data-page="1" title="Lead State"></span>
                </div>
                <div class="crm-pager-arrows">
                    <button type="button" class="crm-pager-arrow btn-prev" title="Previous Page"><i class="fa fa-chevron-left"></i></button>
                    <button type="button" class="crm-pager-arrow btn-next" title="Next Page"><i class="fa fa-chevron-right"></i></button>
                </div>
            </div>
        </div>

        <div class="crm-pager-container" style="position:relative; margin-bottom:12px;">
            <button type="button" class="crm-side-float-arrow arrow-right" data-track="#pagerTrackStates" data-dir="next" title="Swipe to Lead State"><i class="fa fa-chevron-right"></i></button>
            <button type="button" class="crm-side-float-arrow arrow-left" data-track="#pagerTrackStates" data-dir="prev" style="display:none;" title="Swipe to Database State"><i class="fa fa-chevron-left"></i></button>
            <div class="crm-pager-track" id="pagerTrackStates" data-page="0">
                <div class="crm-pager-page">
                    <div class="row">
                        <div class="col-md-6 col-xs-12">
                            <div class="crm-modern-stat-card" style="margin-bottom: 12px;">
                                <div class="card-top">
                                    <h4 class="card-title"><i class="fa fa-database" style="color:#0072f0;"></i> My Database State</h4>
                                    <span class="card-total-pill"><?php echo number_format($my_db_total); ?> Total</span>
                                </div>
                                <div class="crm-seg-bar">
                                    <?php if (!empty($dashboard_db_state) && $my_db_total > 0) {
                                        foreach($dashboard_db_state as $li) {
                                            $pct = round(((int)$li['lead_count'] / $my_db_total) * 100, 1);
                                            if ($pct > 0) {
                                                echo '<div class="crm-seg" style="width:' . $pct . '%; opacity:0.85;" title="' . htmlspecialchars($li['status']) . ': ' . (int)$li['lead_count'] . '"></div>';
                                            }
                                        }
                                    } else { ?>
                                        <div class="crm-seg" style="width:100%; opacity:0.3;"></div>
                                    <?php } ?>
                                </div>
                                <ul class="crm-modern-stat-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(150px, 1fr)); gap:6px;">
                                    <?php if (!empty($dashboard_db_state)) { foreach($dashboard_db_state as $li) { ?>
                                        <li style="margin:0;">
                                            <a href="<?php echo base_url(); ?>manager/leads?status%5B%5D=<?php echo $li['status_id']; ?>" class="crm-modern-stat-chip" style="padding:6px 9px;">
                                                <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-right:4px;" title="<?php echo htmlspecialchars($li['status']); ?>"><?php echo htmlspecialchars($li['status']); ?></span>
                                                <span class="badge" style="background:var(--crm-card-bg, #ffffff); color:var(--crm-text, #0f172a); border:1px solid var(--crm-border, #cbd5e1); font-weight:700; border-radius:4px; padding:2px 7px; font-size:11px; flex-shrink:0;"><?php echo (int)$li['lead_count']; ?></span>
                                            </a>
                                        </li>
                                    <?php } } else { ?>
                                        <li style="grid-column: 1 / -1;"><span class="text-muted" style="font-size:12px;">No database records found</span></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-6 col-xs-12">
                            <div class="crm-modern-stat-card" style="margin-bottom: 12px;">
                                <div class="card-top">
                                    <h4 class="card-title"><i class="fa fa-database" style="color:#0072f0;"></i> Team Database State</h4>
                                    <span class="card-total-pill"><?php echo number_format($team_db_total); ?> Total</span>
                                </div>
                                <div class="crm-seg-bar">
                                    <?php if (!empty($dashboard_db_state_team) && $team_db_total > 0) {
                                        foreach($dashboard_db_state_team as $li) {
                                            $pct = round(((int)$li['lead_count'] / $team_db_total) * 100, 1);
                                            if ($pct > 0) {
                                                echo '<div class="crm-seg" style="width:' . $pct . '%; opacity:0.85;" title="' . htmlspecialchars($li['status']) . ': ' . (int)$li['lead_count'] . '"></div>';
                                            }
                                        }
                                    } else { ?>
                                        <div class="crm-seg" style="width:100%; opacity:0.3;"></div>
                                    <?php } ?>
                                </div>
                                <ul class="crm-modern-stat-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(150px, 1fr)); gap:6px;">
                                    <?php if (!empty($dashboard_db_state_team)) { foreach($dashboard_db_state_team as $li) { ?>
                                        <li style="margin:0;">
                                            <a href="<?php echo base_url(); ?>manager/team/database?status%5B%5D=<?php echo $li['status_id']; ?>" class="crm-modern-stat-chip" style="padding:6px 9px;">
                                                <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-right:4px;" title="<?php echo htmlspecialchars($li['status']); ?>"><?php echo htmlspecialchars($li['status']); ?></span>
                                                <span class="badge" style="background:var(--crm-card-bg, #ffffff); color:var(--crm-text, #0f172a); border:1px solid var(--crm-border, #cbd5e1); font-weight:700; border-radius:4px; padding:2px 7px; font-size:11px; flex-shrink:0;"><?php echo (int)$li['lead_count']; ?></span>
                                            </a>
                                        </li>
                                    <?php } } else { ?>
                                        <li style="grid-column: 1 / -1;"><span class="text-muted" style="font-size:12px;">No team database records found</span></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="crm-pager-page">
                    <div class="row">
                        <div class="col-md-6 col-xs-12">
                            <div class="crm-modern-stat-card" style="margin-bottom: 12px;">
                                <div class="card-top">
                                    <h4 class="card-title"><i class="fa fa-bullseye" style="color:#0072f0;"></i> My Lead State</h4>
                                    <span class="card-total-pill"><?php echo number_format($my_lead_total); ?> Total</span>
                                </div>
                                <div class="crm-seg-bar">
                                    <?php if (!empty($dashboard_lead_state) && $my_lead_total > 0) {
                                        foreach($dashboard_lead_state as $li) {
                                            $pct = round(((int)$li['lead_count'] / $my_lead_total) * 100, 1);
                                            if ($pct > 0) {
                                                echo '<div class="crm-seg" style="width:' . $pct . '%; opacity:0.85;" title="' . htmlspecialchars($li['status']) . ': ' . (int)$li['lead_count'] . '"></div>';
                                            }
                                        }
                                    } else { ?>
                                        <div class="crm-seg" style="width:100%; opacity:0.3;"></div>
                                    <?php } ?>
                                </div>
                                <ul class="crm-modern-stat-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(150px, 1fr)); gap:6px;">
                                    <?php if (!empty($dashboard_lead_state)) { foreach($dashboard_lead_state as $li) { ?>
                                        <li style="margin:0;">
                                            <a href="<?php echo base_url(); ?>manager/leads/view_leads?status%5B%5D=<?php echo $li['status_id']; ?>" class="crm-modern-stat-chip" style="padding:6px 9px;">
                                                <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-right:4px;" title="<?php echo htmlspecialchars($li['status']); ?>"><?php echo htmlspecialchars($li['status']); ?></span>
                                                <span class="badge" style="background:var(--crm-card-bg, #ffffff); color:var(--crm-text, #0f172a); border:1px solid var(--crm-border, #cbd5e1); font-weight:700; border-radius:4px; padding:2px 7px; font-size:11px; flex-shrink:0;"><?php echo (int)$li['lead_count']; ?></span>
                                            </a>
                                        </li>
                                    <?php } } else { ?>
                                        <li style="grid-column: 1 / -1;"><span class="text-muted" style="font-size:12px;">No leads found</span></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-6 col-xs-12">
                            <div class="crm-modern-stat-card" style="margin-bottom: 12px;">
                                <div class="card-top">
                                    <h4 class="card-title"><i class="fa fa-bullseye" style="color:#0072f0;"></i> Team Lead State</h4>
                                    <span class="card-total-pill"><?php echo number_format($team_lead_total); ?> Total</span>
                                </div>
                                <div class="crm-seg-bar">
                                    <?php if (!empty($dashboard_lead_state_team) && $team_lead_total > 0) {
                                        foreach($dashboard_lead_state_team as $li) {
                                            $pct = round(((int)$li['lead_count'] / $team_lead_total) * 100, 1);
                                            if ($pct > 0) {
                                                echo '<div class="crm-seg" style="width:' . $pct . '%; opacity:0.85;" title="' . htmlspecialchars($li['status']) . ': ' . (int)$li['lead_count'] . '"></div>';
                                            }
                                        }
                                    } else { ?>
                                        <div class="crm-seg" style="width:100%; opacity:0.3;"></div>
                                    <?php } ?>
                                </div>
                                <ul class="crm-modern-stat-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(150px, 1fr)); gap:6px;">
                                    <?php if (!empty($dashboard_lead_state_team)) { foreach($dashboard_lead_state_team as $li) { ?>
                                        <li style="margin:0;">
                                            <a href="<?php echo base_url(); ?>manager/team/leads?status%5B%5D=<?php echo $li['status_id']; ?>" class="crm-modern-stat-chip" style="padding:6px 9px;">
                                                <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-right:4px;" title="<?php echo htmlspecialchars($li['status']); ?>"><?php echo htmlspecialchars($li['status']); ?></span>
                                                <span class="badge" style="background:var(--crm-card-bg, #ffffff); color:var(--crm-text, #0f172a); border:1px solid var(--crm-border, #cbd5e1); font-weight:700; border-radius:4px; padding:2px 7px; font-size:11px; flex-shrink:0;"><?php echo (int)$li['lead_count']; ?></span>
                                            </a>
                                        </li>
                                    <?php } } else { ?>
                                        <li style="grid-column: 1 / -1;"><span class="text-muted" style="font-size:12px;">No team leads found</span></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dash-section-divider" style="margin-top: 10px; margin-bottom: 12px; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h3 class="dash-section-title" style="margin-bottom:0;">
                    <span class="dash-section-title-icon">
                        <span></span><span></span>
                    </span>
                    <span id="titleProj">Projections</span>
                </h3>
            </div>
            <div class="crm-pager-controls" data-track="#pagerTrackProj" data-title="#titleProj" data-t1="Projections" data-t2="Historical Achievement vs Target">
                <div class="crm-pager-dots">
                    <span class="crm-pager-dot active" data-page="0" title="Projections Forecast"></span>
                    <span class="crm-pager-dot" data-page="1" title="Historical Achievement"></span>
                </div>
                <div class="crm-pager-arrows">
                    <button type="button" class="crm-pager-arrow btn-prev" title="Previous Page"><i class="fa fa-chevron-left"></i></button>
                    <button type="button" class="crm-pager-arrow btn-next" title="Next Page"><i class="fa fa-chevron-right"></i></button>
                </div>
            </div>
        </div>

        <div class="crm-pager-container" style="position:relative; margin-bottom:12px;">
            <button type="button" class="crm-side-float-arrow arrow-right" data-track="#pagerTrackProj" data-dir="next" title="Swipe to Achievement Graph"><i class="fa fa-chevron-right"></i></button>
            <button type="button" class="crm-side-float-arrow arrow-left" data-track="#pagerTrackProj" data-dir="prev" style="display:none;" title="Swipe to Projections"><i class="fa fa-chevron-left"></i></button>
            <div class="crm-pager-track" id="pagerTrackProj" data-page="0">
                <div class="crm-pager-page">
                    <div class="row">
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
                </div>

                <div class="crm-pager-page">
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <div class="dash-chart-card">
                                <div class="dash-chart-header" style="display:flex; justify-content:space-between; align-items:center;">
                                    <h4 class="dash-chart-title">
                                        <i class="fa fa-bar-chart"></i> Past 10 Months Achievement vs Target (Team)
                                    </h4>
                                    <div style="display:flex; gap:12px; font-size:11px; font-weight:600;">
                                        <span><span style="display:inline-block; width:10px; height:10px; background:#0072f0; border-radius:2px; vertical-align:-1px; margin-right:4px;"></span>Target</span>
                                        <span><span style="display:inline-block; width:10px; height:10px; background:#16a34a; border-radius:2px; vertical-align:-1px; margin-right:4px;"></span>Met Target</span>
                                        <span><span style="display:inline-block; width:10px; height:10px; background:#dc2626; border-radius:2px; vertical-align:-1px; margin-right:4px;"></span>Below Target</span>
                                    </div>
                                </div>
                                <div class="dash-chart-body">
                                    <canvas id="barChartAchieve" height="230"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dash-section-divider" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h3 class="dash-section-title" style="margin-bottom:0;">
                    <span class="dash-section-title-icon">
                        <span></span><span></span>
                    </span>
                    <span id="titleOps">Operational Activity and Breakdown</span>
                </h3>
            </div>
            <div class="crm-pager-controls" data-track="#pagerTrackOps" data-title="#titleOps" data-t1="Operational Activity and Breakdown" data-t2="Database &amp; Lead Operations">
                <div class="crm-pager-dots">
                    <span class="crm-pager-dot active" data-page="0" title="Core Operations"></span>
                    <span class="crm-pager-dot" data-page="1" title="Database &amp; Lead Operations"></span>
                </div>
                <div class="crm-pager-arrows">
                    <button type="button" class="crm-pager-arrow btn-prev" title="Previous Page"><i class="fa fa-chevron-left"></i></button>
                    <button type="button" class="crm-pager-arrow btn-next" title="Next Page"><i class="fa fa-chevron-right"></i></button>
                </div>
            </div>
        </div>

        <?php
            $total_my_tickets = !empty($dashboard_tickets['total_all']) ? (int)$dashboard_tickets['total_all'] : 0;
            $total_my_tasks = !empty($dashboard_tasks['total_all']) ? (int)$dashboard_tasks['total_all'] : (!empty($dashboard_tasks) ? (int)array_sum(array_slice($dashboard_tasks, 0, 5)) : 0);
            $total_my_f = !empty($dashboard_f) ? (int)array_sum(array_slice($dashboard_f, 0, 5)) : 0;
            $total_my_m = !empty($dashboard_m) ? (int)array_sum(array_slice($dashboard_m, 0, 5)) : 0;
            $total_my_vfu = !empty($dashboard_vfu) ? (int)array_sum(array_slice($dashboard_vfu, 0, 5)) : 0;

            $total_team_tickets = !empty($dashboard_Ttickets['total_all']) ? (int)$dashboard_Ttickets['total_all'] : 0;
            $total_team_tasks = !empty($dashboard_Ttasks['total_all']) ? (int)$dashboard_Ttasks['total_all'] : (!empty($dashboard_Ttasks) ? (int)array_sum(array_slice($dashboard_Ttasks, 0, 5)) : 0);
            $total_team_f = !empty($dashboard_Tf) ? (int)array_sum(array_slice($dashboard_Tf, 0, 5)) : 0;
            $total_team_m = !empty($dashboard_Tm) ? (int)array_sum(array_slice($dashboard_Tm, 0, 5)) : 0;
            $total_team_vfu = !empty($dashboard_Tvfu) ? (int)array_sum(array_slice($dashboard_Tvfu, 0, 5)) : 0; ?>

        <div class="crm-pager-container" style="position:relative;">
            <button type="button" class="crm-side-float-arrow arrow-right" data-track="#pagerTrackOps" data-dir="next" title="Swipe to Database & Leads"><i class="fa fa-chevron-right"></i></button>
            <button type="button" class="crm-side-float-arrow arrow-left" data-track="#pagerTrackOps" data-dir="prev" style="display:none;" title="Swipe to Core Operations"><i class="fa fa-chevron-left"></i></button>
            <div class="crm-pager-track" id="pagerTrackOps" data-page="0">
                <div class="crm-pager-page">
        <div class="row crm-row-5cols">
            <div class="crm-col-5 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title">
                            <?php if(has_section_perm('tickets', 'read')){ ?>
                                <a href="<?php echo base_url(); ?>manager/tickets?assigned_user=<?php echo (int)$this->session->userdata('manager_id'); ?>&status=Open" style="color:inherit; text-decoration:none;"><i class="fa fa-ticket" style="color:#2563eb;"></i> My Tickets</a>
                            <?php } else { ?>
                                <span><i class="fa fa-ticket" style="color:#2563eb;"></i> My Tickets</span>
                            <?php } ?>
                        </h4>
                        <?php if(has_section_perm('tickets', 'read')){ ?>
                            <a href="<?php echo base_url(); ?>manager/tickets?assigned_user=<?php echo (int)$this->session->userdata('manager_id'); ?>&status=Open" class="card-total-pill" style="text-decoration:none;"><?php echo $total_my_tickets; ?> Total</a>
                        <?php } else { ?>
                            <span class="card-total-pill"><?php echo $total_my_tickets; ?> Total</span>
                        <?php } ?>
                    </div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:14%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:16%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:20%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:30%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li>
                            <a href="<?php echo has_section_perm('tickets', 'read') ? base_url().'manager/tickets?overdue=1' : '#'; ?>" class="crm-modern-stat-chip">
                                <span>All Missed</span>
                                <span class="badge bg-red"><?php echo isset($dashboard_tickets['total_missed']) ? $dashboard_tickets['total_missed'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo has_section_perm('tickets', 'read') ? base_url().'manager/tickets?status=Open' : '#'; ?>" class="crm-modern-stat-chip">
                                <span>Last 7 Days</span>
                                <span class="badge bg-orange"><?php echo isset($dashboard_tickets['total_lastweek']) ? $dashboard_tickets['total_lastweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo has_section_perm('tickets', 'read') ? base_url().'manager/tickets?deadline='.date('d-m-Y') : '#'; ?>" class="crm-modern-stat-chip">
                                <span>Today</span>
                                <span class="badge bg-skyblue"><?php echo isset($dashboard_tickets['total_today']) ? $dashboard_tickets['total_today'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo has_section_perm('tickets', 'read') ? base_url().'manager/tickets?overdue=soon' : '#'; ?>" class="crm-modern-stat-chip">
                                <span>Next 7 Days</span>
                                <span class="badge bg-blue"><?php echo isset($dashboard_tickets['total_nextweek']) ? $dashboard_tickets['total_nextweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo has_section_perm('tickets', 'read') ? base_url().'manager/tickets?status=Open' : '#'; ?>" class="crm-modern-stat-chip">
                                <span>All Future</span>
                                <span class="badge bg-green"><?php echo isset($dashboard_tickets['total_future']) ? $dashboard_tickets['total_future'] : 0; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="crm-col-5 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><a href="<?php echo base_url(); ?>manager/tasks?team=0" style="color:inherit; text-decoration:none;"><i class="fa fa-tasks"></i> My Tasks</a></h4>
                        <a href="<?php echo base_url(); ?>manager/tasks?team=0" class="card-total-pill" style="text-decoration:none;"><?php echo $total_my_tasks; ?> Total</a>
                    </div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:14%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:16%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:20%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:30%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li>
                            <a href="<?php echo base_url(); ?>manager/tasks/followups?type=1" class="crm-modern-stat-chip" data-drilldown="my_tasks" data-type="1" data-label="My Tasks - All Missed">
                                <span>All Missed</span>
                                <span class="badge bg-red"><?php echo isset($dashboard_tasks['total_missed']) ? $dashboard_tasks['total_missed'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/tasks/followups?type=2" class="crm-modern-stat-chip" data-drilldown="my_tasks" data-type="2" data-label="My Tasks - Last 7 Days">
                                <span>Last 7 Days</span>
                                <span class="badge bg-orange"><?php echo isset($dashboard_tasks['total_lastweek']) ? $dashboard_tasks['total_lastweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/tasks/followups?type=3" class="crm-modern-stat-chip" data-drilldown="my_tasks" data-type="3" data-label="My Tasks - Today">
                                <span>Today</span>
                                <span class="badge bg-skyblue"><?php echo isset($dashboard_tasks['total_today']) ? $dashboard_tasks['total_today'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/tasks/followups?type=4" class="crm-modern-stat-chip" data-drilldown="my_tasks" data-type="4" data-label="My Tasks - Next 7 Days">
                                <span>Next 7 Days</span>
                                <span class="badge bg-blue"><?php echo isset($dashboard_tasks['total_nextweek']) ? $dashboard_tasks['total_nextweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/tasks/followups?type=5" class="crm-modern-stat-chip" data-drilldown="my_tasks" data-type="5" data-label="My Tasks - All Future">
                                <span>All Future</span>
                                <span class="badge bg-green"><?php echo isset($dashboard_tasks['total_future']) ? $dashboard_tasks['total_future'] : 0; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="crm-col-5 col-xs-12">
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

            <div class="crm-col-5 col-xs-12">
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

            <div class="crm-col-5 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-briefcase"></i> My Vertical FU</h4>
                        <span class="card-total-pill"><?php echo $total_my_vfu; ?> Total</span>
                    </div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:14%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:16%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:20%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:30%;" title="All Future"></div>
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

        <div class="row crm-row-5cols">
            <div class="crm-col-5 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title">
                            <?php if(has_section_perm('tickets', 'read')){ ?>
                                <a href="<?php echo base_url(); ?>manager/tickets?team=1&status=Open" style="color:inherit; text-decoration:none;"><i class="fa fa-ticket" style="color:#2563eb;"></i> Team Tickets</a>
                            <?php } else { ?>
                                <span><i class="fa fa-ticket" style="color:#2563eb;"></i> Team Tickets</span>
                            <?php } ?>
                        </h4>
                        <?php if(has_section_perm('tickets', 'read')){ ?>
                            <a href="<?php echo base_url(); ?>manager/tickets?team=1&status=Open" class="card-total-pill" style="text-decoration:none;"><?php echo $total_team_tickets; ?> Total</a>
                        <?php } else { ?>
                            <span class="card-total-pill"><?php echo $total_team_tickets; ?> Total</span>
                        <?php } ?>
                    </div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:14%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:16%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:20%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:30%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li>
                            <a href="<?php echo has_section_perm('tickets', 'read') ? base_url().'manager/tickets?team=1&overdue=1' : '#'; ?>" class="crm-modern-stat-chip">
                                <span>All Missed</span>
                                <span class="badge bg-red"><?php echo isset($dashboard_Ttickets['total_missed']) ? $dashboard_Ttickets['total_missed'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo has_section_perm('tickets', 'read') ? base_url().'manager/tickets?team=1&status=Open' : '#'; ?>" class="crm-modern-stat-chip">
                                <span>Last 7 Days</span>
                                <span class="badge bg-orange"><?php echo isset($dashboard_Ttickets['total_lastweek']) ? $dashboard_Ttickets['total_lastweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo has_section_perm('tickets', 'read') ? base_url().'manager/tickets?team=1&deadline='.date('d-m-Y') : '#'; ?>" class="crm-modern-stat-chip">
                                <span>Today</span>
                                <span class="badge bg-skyblue"><?php echo isset($dashboard_Ttickets['total_today']) ? $dashboard_Ttickets['total_today'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo has_section_perm('tickets', 'read') ? base_url().'manager/tickets?team=1&overdue=soon' : '#'; ?>" class="crm-modern-stat-chip">
                                <span>Next 7 Days</span>
                                <span class="badge bg-blue"><?php echo isset($dashboard_Ttickets['total_nextweek']) ? $dashboard_Ttickets['total_nextweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo has_section_perm('tickets', 'read') ? base_url().'manager/tickets?team=1&status=Open' : '#'; ?>" class="crm-modern-stat-chip">
                                <span>All Future</span>
                                <span class="badge bg-green"><?php echo isset($dashboard_Ttickets['total_future']) ? $dashboard_Ttickets['total_future'] : 0; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="crm-col-5 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><a href="<?php echo base_url(); ?>manager/tasks?team=1" style="color:inherit; text-decoration:none;"><i class="fa fa-tasks"></i> Team Tasks</a></h4>
                        <a href="<?php echo base_url(); ?>manager/tasks?team=1" class="card-total-pill" style="text-decoration:none;"><?php echo $total_team_tasks; ?> Total</a>
                    </div>
                    <div class="crm-seg-bar">
                        <div class="crm-seg seg-missed" style="width:14%;" title="Missed"></div>
                        <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                        <div class="crm-seg seg-today" style="width:16%;" title="Today"></div>
                        <div class="crm-seg seg-next7" style="width:20%;" title="Next 7 Days"></div>
                        <div class="crm-seg seg-future" style="width:30%;" title="All Future"></div>
                    </div>
                    <ul class="crm-modern-stat-grid">
                        <li>
                            <a href="<?php echo base_url(); ?>manager/tasks/followups?type=1&team=1" class="crm-modern-stat-chip" data-drilldown="team_tasks" data-type="1" data-label="Team Tasks - All Missed">
                                <span>All Missed</span>
                                <span class="badge bg-red"><?php echo isset($dashboard_Ttasks['total_missed']) ? $dashboard_Ttasks['total_missed'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/tasks/followups?type=2&team=1" class="crm-modern-stat-chip" data-drilldown="team_tasks" data-type="2" data-label="Team Tasks - Last 7 Days">
                                <span>Last 7 Days</span>
                                <span class="badge bg-orange"><?php echo isset($dashboard_Ttasks['total_lastweek']) ? $dashboard_Ttasks['total_lastweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/tasks/followups?type=3&team=1" class="crm-modern-stat-chip" data-drilldown="team_tasks" data-type="3" data-label="Team Tasks - Today">
                                <span>Today</span>
                                <span class="badge bg-skyblue"><?php echo isset($dashboard_Ttasks['total_today']) ? $dashboard_Ttasks['total_today'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/tasks/followups?type=4&team=1" class="crm-modern-stat-chip" data-drilldown="team_tasks" data-type="4" data-label="Team Tasks - Next 7 Days">
                                <span>Next 7 Days</span>
                                <span class="badge bg-blue"><?php echo isset($dashboard_Ttasks['total_nextweek']) ? $dashboard_Ttasks['total_nextweek'] : 0; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/tasks/followups?type=5&team=1" class="crm-modern-stat-chip" data-drilldown="team_tasks" data-type="5" data-label="Team Tasks - All Future">
                                <span>All Future</span>
                                <span class="badge bg-green"><?php echo isset($dashboard_Ttasks['total_future']) ? $dashboard_Ttasks['total_future'] : 0; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="crm-col-5 col-xs-12">
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

            <div class="crm-col-5 col-xs-12">
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

            <div class="crm-col-5 col-xs-12">
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
                </div>

                <div class="crm-pager-page">
                    <div class="row" style="margin-left:-6px; margin-right:-6px;">
                        <div class="col-md-4 col-sm-6 col-xs-12" style="padding-left:6px; padding-right:6px;">
                            <div class="crm-modern-stat-card" style="margin-bottom:12px;">
                                <div class="card-top">
                                    <h4 class="card-title"><i class="fa fa-database" style="color:#0072f0;"></i> My Database</h4>
                                    <span class="card-total-pill"><?php echo (int)$dashboard_db_ops_my['total_all']; ?> Total</span>
                                </div>
                                <div class="crm-seg-bar">
                                    <div class="crm-seg seg-missed" style="width:14%;" title="Missed"></div>
                                    <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                                    <div class="crm-seg seg-today" style="width:16%;" title="Today"></div>
                                    <div class="crm-seg seg-next7" style="width:20%;" title="Next 7 Days"></div>
                                    <div class="crm-seg seg-future" style="width:30%;" title="All Future"></div>
                                </div>
                                <ul class="crm-modern-stat-grid">
                                    <li><a href="<?php echo base_url(); ?>manager/leads/followups?type=1&stage=database" class="crm-modern-stat-chip"><span>All Missed</span><span class="badge bg-red"><?php echo (int)$dashboard_db_ops_my['total_missed']; ?></span></a></li>
                                    <li><a href="<?php echo base_url(); ?>manager/leads/followups?type=2&stage=database" class="crm-modern-stat-chip"><span>Last 7 Days</span><span class="badge bg-orange"><?php echo (int)$dashboard_db_ops_my['total_lastweek']; ?></span></a></li>
                                    <li><a href="<?php echo base_url(); ?>manager/leads/followups?type=3&stage=database" class="crm-modern-stat-chip"><span>Today</span><span class="badge bg-skyblue"><?php echo (int)$dashboard_db_ops_my['total_today']; ?></span></a></li>
                                    <li><a href="<?php echo base_url(); ?>manager/leads/followups?type=4&stage=database" class="crm-modern-stat-chip"><span>Next 7 Days</span><span class="badge bg-blue"><?php echo (int)$dashboard_db_ops_my['total_nextweek']; ?></span></a></li>
                                    <li><a href="<?php echo base_url(); ?>manager/leads/followups?type=5&stage=database" class="crm-modern-stat-chip"><span>All Future</span><span class="badge bg-green"><?php echo (int)$dashboard_db_ops_my['total_future']; ?></span></a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6 col-xs-12" style="padding-left:6px; padding-right:6px;">
                            <div class="crm-modern-stat-card" style="margin-bottom:12px;">
                                <div class="card-top">
                                    <h4 class="card-title"><i class="fa fa-bullseye" style="color:#0072f0;"></i> My Leads</h4>
                                    <span class="card-total-pill"><?php echo (int)$dashboard_lead_ops_my['total_all']; ?> Total</span>
                                </div>
                                <div class="crm-seg-bar">
                                    <div class="crm-seg seg-missed" style="width:14%;" title="Missed"></div>
                                    <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                                    <div class="crm-seg seg-today" style="width:16%;" title="Today"></div>
                                    <div class="crm-seg seg-next7" style="width:20%;" title="Next 7 Days"></div>
                                    <div class="crm-seg seg-future" style="width:30%;" title="All Future"></div>
                                </div>
                                <ul class="crm-modern-stat-grid">
                                    <li><a href="<?php echo base_url(); ?>manager/leads/followups?type=1&stage=lead" class="crm-modern-stat-chip"><span>All Missed</span><span class="badge bg-red"><?php echo (int)$dashboard_lead_ops_my['total_missed']; ?></span></a></li>
                                    <li><a href="<?php echo base_url(); ?>manager/leads/followups?type=2&stage=lead" class="crm-modern-stat-chip"><span>Last 7 Days</span><span class="badge bg-orange"><?php echo (int)$dashboard_lead_ops_my['total_lastweek']; ?></span></a></li>
                                    <li><a href="<?php echo base_url(); ?>manager/leads/followups?type=3&stage=lead" class="crm-modern-stat-chip"><span>Today</span><span class="badge bg-skyblue"><?php echo (int)$dashboard_lead_ops_my['total_today']; ?></span></a></li>
                                    <li><a href="<?php echo base_url(); ?>manager/leads/followups?type=4&stage=lead" class="crm-modern-stat-chip"><span>Next 7 Days</span><span class="badge bg-blue"><?php echo (int)$dashboard_lead_ops_my['total_nextweek']; ?></span></a></li>
                                    <li><a href="<?php echo base_url(); ?>manager/leads/followups?type=5&stage=lead" class="crm-modern-stat-chip"><span>All Future</span><span class="badge bg-green"><?php echo (int)$dashboard_lead_ops_my['total_future']; ?></span></a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6 col-xs-12" style="padding-left:6px; padding-right:6px;">
                            <div class="crm-modern-stat-card" style="margin-bottom:12px;">
                                <div class="card-top">
                                    <h4 class="card-title"><i class="fa fa-check-circle" style="color:#0072f0;"></i> My Closures</h4>
                                    <span class="card-total-pill"><?php echo !empty($dashboard_c['total_all']) ? (int)$dashboard_c['total_all'] : 0; ?> Total</span>
                                </div>
                                <div class="crm-seg-bar">
                                    <div class="crm-seg seg-missed" style="width:14%;" title="Missed"></div>
                                    <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                                    <div class="crm-seg seg-today" style="width:16%;" title="Today"></div>
                                    <div class="crm-seg seg-next7" style="width:20%;" title="Next 7 Days"></div>
                                    <div class="crm-seg seg-future" style="width:30%;" title="All Future"></div>
                                </div>
                                <ul class="crm-modern-stat-grid">
                                    <li><a href="<?php echo base_url(); ?>manager/leads?closer=1" class="crm-modern-stat-chip"><span>All Missed</span><span class="badge bg-red"><?php echo isset($dashboard_c['total_missed']) ? (int)$dashboard_c['total_missed'] : 0; ?></span></a></li>
                                    <li><a href="<?php echo base_url(); ?>manager/leads?closer=2" class="crm-modern-stat-chip"><span>Last 7 Days</span><span class="badge bg-orange"><?php echo isset($dashboard_c['total_lastweek']) ? (int)$dashboard_c['total_lastweek'] : 0; ?></span></a></li>
                                    <li><a href="<?php echo base_url(); ?>manager/leads?closer=3" class="crm-modern-stat-chip"><span>Today</span><span class="badge bg-skyblue"><?php echo isset($dashboard_c['total_today']) ? (int)$dashboard_c['total_today'] : 0; ?></span></a></li>
                                    <li><a href="<?php echo base_url(); ?>manager/leads?closer=4" class="crm-modern-stat-chip"><span>Next 7 Days</span><span class="badge bg-blue"><?php echo isset($dashboard_c['total_nextweek']) ? (int)$dashboard_c['total_nextweek'] : 0; ?></span></a></li>
                                    <li><a href="<?php echo base_url(); ?>manager/leads?closer=5" class="crm-modern-stat-chip"><span>All Future</span><span class="badge bg-green"><?php echo isset($dashboard_c['total_future']) ? (int)$dashboard_c['total_future'] : 0; ?></span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-left:-6px; margin-right:-6px;">
                        <div class="col-md-4 col-sm-6 col-xs-12" style="padding-left:6px; padding-right:6px;">
                            <div class="crm-modern-stat-card" style="margin-bottom:12px;">
                                <div class="card-top">
                                    <h4 class="card-title"><i class="fa fa-database" style="color:#0072f0;"></i> Team Database</h4>
                                    <span class="card-total-pill"><?php echo (int)$dashboard_db_ops_team['total_all']; ?> Total</span>
                                </div>
                                <div class="crm-seg-bar">
                                    <div class="crm-seg seg-missed" style="width:14%;" title="Missed"></div>
                                    <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                                    <div class="crm-seg seg-today" style="width:16%;" title="Today"></div>
                                    <div class="crm-seg seg-next7" style="width:20%;" title="Next 7 Days"></div>
                                    <div class="crm-seg seg-future" style="width:30%;" title="All Future"></div>
                                </div>
                                <ul class="crm-modern-stat-grid">
                                    <li><a href="<?php echo base_url(); ?>manager/team/followups?type=1&stage=database" class="crm-modern-stat-chip"><span>All Missed</span><span class="badge bg-red"><?php echo (int)$dashboard_db_ops_team['total_missed']; ?></span></a></li>
                                    <li><a href="<?php echo base_url(); ?>manager/team/followups?type=2&stage=database" class="crm-modern-stat-chip"><span>Last 7 Days</span><span class="badge bg-orange"><?php echo (int)$dashboard_db_ops_team['total_lastweek']; ?></span></a></li>
                                    <li><a href="<?php echo base_url(); ?>manager/team/followups?type=3&stage=database" class="crm-modern-stat-chip"><span>Today</span><span class="badge bg-skyblue"><?php echo (int)$dashboard_db_ops_team['total_today']; ?></span></a></li>
                                    <li><a href="<?php echo base_url(); ?>manager/team/followups?type=4&stage=database" class="crm-modern-stat-chip"><span>Next 7 Days</span><span class="badge bg-blue"><?php echo (int)$dashboard_db_ops_team['total_nextweek']; ?></span></a></li>
                                    <li><a href="<?php echo base_url(); ?>manager/team/followups?type=5&stage=database" class="crm-modern-stat-chip"><span>All Future</span><span class="badge bg-green"><?php echo (int)$dashboard_db_ops_team['total_future']; ?></span></a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6 col-xs-12" style="padding-left:6px; padding-right:6px;">
                            <div class="crm-modern-stat-card" style="margin-bottom:12px;">
                                <div class="card-top">
                                    <h4 class="card-title"><i class="fa fa-bullseye" style="color:#0072f0;"></i> Team Leads</h4>
                                    <span class="card-total-pill"><?php echo (int)$dashboard_lead_ops_team['total_all']; ?> Total</span>
                                </div>
                                <div class="crm-seg-bar">
                                    <div class="crm-seg seg-missed" style="width:14%;" title="Missed"></div>
                                    <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                                    <div class="crm-seg seg-today" style="width:16%;" title="Today"></div>
                                    <div class="crm-seg seg-next7" style="width:20%;" title="Next 7 Days"></div>
                                    <div class="crm-seg seg-future" style="width:30%;" title="All Future"></div>
                                </div>
                                <ul class="crm-modern-stat-grid">
                                    <li><a href="<?php echo base_url(); ?>manager/team/followups?type=1&stage=lead" class="crm-modern-stat-chip"><span>All Missed</span><span class="badge bg-red"><?php echo (int)$dashboard_lead_ops_team['total_missed']; ?></span></a></li>
                                    <li><a href="<?php echo base_url(); ?>manager/team/followups?type=2&stage=lead" class="crm-modern-stat-chip"><span>Last 7 Days</span><span class="badge bg-orange"><?php echo (int)$dashboard_lead_ops_team['total_lastweek']; ?></span></a></li>
                                    <li><a href="<?php echo base_url(); ?>manager/team/followups?type=3&stage=lead" class="crm-modern-stat-chip"><span>Today</span><span class="badge bg-skyblue"><?php echo (int)$dashboard_lead_ops_team['total_today']; ?></span></a></li>
                                    <li><a href="<?php echo base_url(); ?>manager/team/followups?type=4&stage=lead" class="crm-modern-stat-chip"><span>Next 7 Days</span><span class="badge bg-blue"><?php echo (int)$dashboard_lead_ops_team['total_nextweek']; ?></span></a></li>
                                    <li><a href="<?php echo base_url(); ?>manager/team/followups?type=5&stage=lead" class="crm-modern-stat-chip"><span>All Future</span><span class="badge bg-green"><?php echo (int)$dashboard_lead_ops_team['total_future']; ?></span></a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6 col-xs-12" style="padding-left:6px; padding-right:6px;">
                            <div class="crm-modern-stat-card" style="margin-bottom:12px;">
                                <div class="card-top">
                                    <h4 class="card-title"><i class="fa fa-check-circle" style="color:#0072f0;"></i> Team Closures</h4>
                                    <span class="card-total-pill"><?php echo !empty($dashboard_Tc['total_all']) ? (int)$dashboard_Tc['total_all'] : 0; ?> Total</span>
                                </div>
                                <div class="crm-seg-bar">
                                    <div class="crm-seg seg-missed" style="width:14%;" title="Missed"></div>
                                    <div class="crm-seg seg-last7" style="width:20%;" title="Last 7 Days"></div>
                                    <div class="crm-seg seg-today" style="width:16%;" title="Today"></div>
                                    <div class="crm-seg seg-next7" style="width:20%;" title="Next 7 Days"></div>
                                    <div class="crm-seg seg-future" style="width:30%;" title="All Future"></div>
                                </div>
                                <ul class="crm-modern-stat-grid">
                                    <li><a href="<?php echo base_url(); ?>manager/team/leads?closer=1" class="crm-modern-stat-chip"><span>All Missed</span><span class="badge bg-red"><?php echo isset($dashboard_Tc['total_missed']) ? (int)$dashboard_Tc['total_missed'] : 0; ?></span></a></li>
                                    <li><a href="<?php echo base_url(); ?>manager/team/leads?closer=2" class="crm-modern-stat-chip"><span>Last 7 Days</span><span class="badge bg-orange"><?php echo isset($dashboard_Tc['total_lastweek']) ? (int)$dashboard_Tc['total_lastweek'] : 0; ?></span></a></li>
                                    <li><a href="<?php echo base_url(); ?>manager/team/leads?closer=3" class="crm-modern-stat-chip"><span>Today</span><span class="badge bg-skyblue"><?php echo isset($dashboard_Tc['total_today']) ? (int)$dashboard_Tc['total_today'] : 0; ?></span></a></li>
                                    <li><a href="<?php echo base_url(); ?>manager/team/leads?closer=4" class="crm-modern-stat-chip"><span>Next 7 Days</span><span class="badge bg-blue"><?php echo isset($dashboard_Tc['total_nextweek']) ? (int)$dashboard_Tc['total_nextweek'] : 0; ?></span></a></li>
                                    <li><a href="<?php echo base_url(); ?>manager/team/leads?closer=5" class="crm-modern-stat-chip"><span>All Future</span><span class="badge bg-green"><?php echo isset($dashboard_Tc['total_future']) ? (int)$dashboard_Tc['total_future'] : 0; ?></span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="dash-drilldown-panel" id="dashDrilldownPanel">
            <div class="dash-drilldown-header">
                <h4 class="dash-drilldown-title">
                    <i class="fa fa-list-alt" style="color:#0072f0;"></i>
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
                        
                    </tbody>
                </table>
            </div>
        </div>



    </div>
</div>


<script>
$(function () {
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

    var months = <?php 
        if (!empty($dashboard_MyPro['month_labels'])) {
            echo json_encode($dashboard_MyPro['month_labels']);
        } else {
            echo json_encode(array('OCT 26', 'NOV 26', 'DEC 26', 'JAN 27', 'FEB 27', 'MAR 27'));
        }
    ?>;
    
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

    if (typeof Chart !== 'undefined' && Chart.Scale) {
        var origCalcXRotation = Chart.Scale.prototype.calculateXLabelRotation;
        Chart.Scale.prototype.calculateXLabelRotation = function() {
            origCalcXRotation.apply(this, arguments);
            this.xLabelRotation = 0;
        };
    }

    var chartOptions = {
        scaleBeginAtZero: true,
        scaleShowGridLines: true,
        scaleGridLineColor: 'rgba(0,0,0,.04)',
        scaleGridLineWidth: 1,
        scaleShowHorizontalLines: true,
        scaleShowVerticalLines: false,
        barShowStroke: true,
        barStrokeWidth: 1,
        barValueSpacing: 4,
        barDatasetSpacing: 1,
        responsive: true,
        maintainAspectRatio: false,
        scaleFontSize: 9,
        scaleFontStyle: '600',
        scaleFontColor: '#475569',
        scaleFontFamily: "'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif"
    };

    var monthDates = <?php echo json_encode(!empty($dashboard_MyPro['month_dates']) ? $dashboard_MyPro['month_dates'] : array()); ?>;
    var teamMonthDates = <?php echo json_encode(!empty($dashboard_TeamPro['month_dates']) ? $dashboard_TeamPro['month_dates'] : array()); ?>;
    var achieveMonths = <?php echo json_encode(!empty($dashboard_achievement_team['labels']) ? $dashboard_achievement_team['labels'] : array()); ?>;
    var achieveTarget = <?php echo json_encode(!empty($dashboard_achievement_team['target']) ? $dashboard_achievement_team['target'] : array()); ?>;
    var achieveData = <?php echo json_encode(!empty($dashboard_achievement_team['achievement']) ? $dashboard_achievement_team['achievement'] : array()); ?>;
    var achieveDates = <?php echo json_encode(!empty($dashboard_achievement_team['month_dates']) ? $dashboard_achievement_team['month_dates'] : array()); ?>;

    var isSwapped = false;
    var chartInstanceAchieve = null;

    var canvas2 = document.getElementById('barChart2');
    var chartInstance2 = null;
    if (canvas2) {
        canvas2.style.cursor = 'pointer';
        var ctx2 = canvas2.getContext('2d');
        chartInstance2 = new Chart(ctx2).Bar({
            labels: months,
            datasets: [{
                label: 'My Projection',
                fillColor: '#0084ff',
                strokeColor: '#0062ea',
                highlightFill: '#00a0ff',
                highlightStroke: '#0062ea',
                data: myProData
            }]
        }, chartOptions);

        canvas2.onclick = function(evt) {
            if (chartInstance2 && typeof chartInstance2.getBarsAtEvent === 'function') {
                var activeBars = chartInstance2.getBarsAtEvent(evt);
                if (activeBars && activeBars.length > 0) {
                    var barLabel = activeBars[0].label;
                    var idx = months.indexOf(barLabel);
                    if (idx !== -1) {
                        if (isSwapped && teamMonthDates[idx]) {
                            window.location.href = '<?php echo base_url(); ?>manager/team/leads?exp_d1=' + encodeURIComponent(teamMonthDates[idx].d1) + '&exp_d2=' + encodeURIComponent(teamMonthDates[idx].d2);
                        } else if (!isSwapped && monthDates[idx]) {
                            window.location.href = '<?php echo base_url(); ?>manager/leads?exp_d1=' + encodeURIComponent(monthDates[idx].d1) + '&exp_d2=' + encodeURIComponent(monthDates[idx].d2);
                        }
                    }
                }
            }
        };
    }

    var canvas4 = document.getElementById('barChart4');
    var chartInstance4 = null;
    if (canvas4) {
        canvas4.style.cursor = 'pointer';
        var ctx4 = canvas4.getContext('2d');
        chartInstance4 = new Chart(ctx4).Bar({
            labels: months,
            datasets: [{
                label: 'Team Projection',
                fillColor: '#0084ff',
                strokeColor: '#0062ea',
                highlightFill: '#00a0ff',
                highlightStroke: '#0062ea',
                data: teamProData
            }]
        }, chartOptions);

        canvas4.onclick = function(evt) {
            if (chartInstance4 && typeof chartInstance4.getBarsAtEvent === 'function') {
                var activeBars = chartInstance4.getBarsAtEvent(evt);
                if (activeBars && activeBars.length > 0) {
                    var barLabel = activeBars[0].label;
                    var idx = months.indexOf(barLabel);
                    if (idx !== -1 && teamMonthDates[idx]) {
                        window.location.href = '<?php echo base_url(); ?>manager/team/leads?exp_d1=' + encodeURIComponent(teamMonthDates[idx].d1) + '&exp_d2=' + encodeURIComponent(teamMonthDates[idx].d2);
                    }
                }
            }
        };
    }

    function initOrUpdateAchieveChart() {
        if (!chartInstanceAchieve) {
            var canvasAchieve = document.getElementById('barChartAchieve');
            if (canvasAchieve) {
                canvasAchieve.style.cursor = 'pointer';
                var ctxAchieve = canvasAchieve.getContext('2d');
                var achieveOptions = $.extend({}, chartOptions, {
                    scaleLabel: "<%= 'AED ' + Number(value).toLocaleString() %>",
                    tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= 'AED ' + Number(value).toLocaleString() %>",
                    multiTooltipTemplate: "<%= datasetLabel %>: <%= 'AED ' + Number(value).toLocaleString() %>"
                });
                chartInstanceAchieve = new Chart(ctxAchieve).Bar({
                    labels: achieveMonths,
                    datasets: [
                        {
                            label: 'Target',
                            fillColor: 'rgba(0, 114, 240, 0.85)',
                            strokeColor: '#0072f0',
                            highlightFill: 'rgba(0, 114, 240, 1)',
                            highlightStroke: '#0072f0',
                            data: achieveTarget
                        },
                        {
                            label: 'Achievement',
                            fillColor: 'rgba(22, 163, 74, 0.85)',
                            strokeColor: '#16a34a',
                            highlightFill: 'rgba(22, 163, 74, 1)',
                            highlightStroke: '#16a34a',
                            data: achieveData
                        }
                    ]
                }, achieveOptions);

                if (chartInstanceAchieve && chartInstanceAchieve.datasets && chartInstanceAchieve.datasets[1]) {
                    for (var b = 0; b < chartInstanceAchieve.datasets[1].bars.length; b++) {
                        var val = Number(achieveData[b]) || 0;
                        var tgt = Number(achieveTarget[b]) || 0;
                        if (val < tgt) {
                            chartInstanceAchieve.datasets[1].bars[b].fillColor = '#dc2626';
                            chartInstanceAchieve.datasets[1].bars[b].strokeColor = '#b91c1c';
                            chartInstanceAchieve.datasets[1].bars[b].highlightFill = '#ef4444';
                            chartInstanceAchieve.datasets[1].bars[b].highlightStroke = '#b91c1c';
                        } else {
                            chartInstanceAchieve.datasets[1].bars[b].fillColor = '#16a34a';
                            chartInstanceAchieve.datasets[1].bars[b].strokeColor = '#15803d';
                            chartInstanceAchieve.datasets[1].bars[b].highlightFill = '#22c55e';
                            chartInstanceAchieve.datasets[1].bars[b].highlightStroke = '#15803d';
                        }
                    }
                    chartInstanceAchieve.update();
                }

                canvasAchieve.onclick = function(evt) {
                    if (chartInstanceAchieve && typeof chartInstanceAchieve.getBarsAtEvent === 'function') {
                        var activeBars = chartInstanceAchieve.getBarsAtEvent(evt);
                        if (activeBars && activeBars.length > 0) {
                            var barLabel = activeBars[0].label;
                            var idx = achieveMonths.indexOf(barLabel);
                            if (idx !== -1 && achieveDates[idx]) {
                                window.location.href = '<?php echo base_url(); ?>manager/team/leads?exp_d1=' + encodeURIComponent(achieveDates[idx].d1) + '&exp_d2=' + encodeURIComponent(achieveDates[idx].d2);
                            }
                        }
                    }
                };
            }
        }
    }

    function switchPagerPage(trackId, pageIndex) {
        var $track = $(trackId);
        if (!$track.length) return;
        $track.attr('data-page', pageIndex);
        var $controls = $('.crm-pager-controls[data-track="' + trackId + '"]');
        if ($controls.length) {
            $controls.find('.crm-pager-dot').removeClass('active');
            $controls.find('.crm-pager-dot[data-page="' + pageIndex + '"]').addClass('active');
            var titleSel = $controls.attr('data-title');
            var subSel = $controls.attr('data-sub');
            if (pageIndex === 0) {
                if (titleSel) $(titleSel).text($controls.attr('data-t1') || '');
                if (subSel) $(subSel).text($controls.attr('data-s1') || '');
            } else {
                if (titleSel) $(titleSel).text($controls.attr('data-t2') || '');
                if (subSel) $(subSel).text($controls.attr('data-s2') || '');
            }
        }
        var $container = $track.closest('.crm-pager-container');
        if ($container.length) {
            var $btnLeft = $container.children('.crm-side-float-arrow.arrow-left');
            var $btnRight = $container.children('.crm-side-float-arrow.arrow-right');
            if (pageIndex === 0) {
                $btnLeft.hide();
                $btnRight.show();
            } else {
                $btnLeft.show();
                $btnRight.hide();
            }
        }
        if (trackId === '#pagerTrackProj' && pageIndex === 1) {
            initOrUpdateAchieveChart();
        }
    }

    $(document).on('click', '.crm-pager-dot', function(e) {
        e.preventDefault();
        var page = parseInt($(this).attr('data-page'), 10) || 0;
        var trackId = $(this).closest('.crm-pager-controls').attr('data-track');
        switchPagerPage(trackId, page);
    });

    $(document).on('click', '.crm-pager-arrow', function(e) {
        e.preventDefault();
        var $controls = $(this).closest('.crm-pager-controls');
        var trackId = $controls.attr('data-track');
        var newPage = $(this).hasClass('btn-next') ? 1 : 0;
        switchPagerPage(trackId, newPage);
    });

    $(document).on('click', '.crm-side-float-arrow', function(e) {
        e.preventDefault();
        var trackId = $(this).attr('data-track');
        var dir = $(this).attr('data-dir');
        var newPage = (dir === 'next') ? 1 : 0;
        switchPagerPage(trackId, newPage);
    });

    $('.crm-pager-container').each(function() {
        var startX = 0;
        var container = this;
        var $track = $(container).children('.crm-pager-track');
        if (!$track.length) return;
        var trackId = '#' + $track.attr('id');
        container.addEventListener('touchstart', function(e) {
            startX = e.changedTouches[0].screenX;
        }, { passive: true });
        container.addEventListener('touchend', function(e) {
            var endX = e.changedTouches[0].screenX;
            var diff = startX - endX;
            if (Math.abs(diff) > 40) {
                switchPagerPage(trackId, diff > 0 ? 1 : 0);
            }
        }, { passive: true });
    });

    $('.crm-modern-stat-chip').on('click', function(e) {
        var targetUrl = $(this).attr('href');
        if (targetUrl && targetUrl !== '#' && targetUrl.indexOf('javascript') === -1) {
            window.location.href = targetUrl;
        }
    });
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
