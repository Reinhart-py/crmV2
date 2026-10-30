<?php require_once(APPPATH."views/manager/elements/header.php"); ?>


<style>
#cardMyLeads, #cardMyOrders, #cardTeamLeads, #cardTeamOrders,
#cardMyLeads *, #cardMyOrders *, #cardTeamLeads *, #cardTeamOrders * {
    color: #ffffff !important;
}
#cardMyLeads .dash-kpi-icon i, #cardMyOrders .dash-kpi-icon i,
#cardTeamLeads .dash-kpi-icon i, #cardTeamOrders .dash-kpi-icon i {
    color: rgba(255, 255, 255, 0.35) !important;
}
</style>

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
                            <div class="dash-kpi-value"><?php echo isset($dashboard_count['count_verticals']) ? (int)$dashboard_count['count_verticals'] : 0; ?></div>
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
                            <div class="dash-kpi-value"><?php echo (isset($dashboard_Tcount['count_users']) && (int)$dashboard_Tcount['count_users'] > 0) ? (int)$dashboard_Tcount['count_users'] : (isset($dashboard_Tcount['count_user']) ? (int)$dashboard_Tcount['count_user'] : 0); ?></div>
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
                            <div class="dash-kpi-value"><?php echo isset($dashboard_Tcount['count_verticals']) ? (int)$dashboard_Tcount['count_verticals'] : 0; ?></div>
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
                            <div class="dash-kpi-value" style="color:#ffffff !important;"><?php echo isset($dashboard_count['count_leads']) ? (int)$dashboard_count['count_leads'] : 0; ?></div>
                            <div class="dash-kpi-label" style="color:#ffffff !important;">My Leads</div>
                            <div class="dash-kpi-sales" style="font-size:11.5px; font-weight:600; color:#ffffff !important; margin-top:4px;">
                                Sales: AED <?php echo number_format((float)(isset($dashboard_count['sales_amount']) ? $dashboard_count['sales_amount'] : 0), 2); ?>
                            </div>
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
                            <div class="dash-kpi-value" style="color:#ffffff !important;"><?php echo isset($dashboard_count['count_orders']) ? (int)$dashboard_count['count_orders'] : 0; ?></div>
                            <div class="dash-kpi-label" style="color:#ffffff !important;">My Orders</div>
                            <div class="dash-kpi-sales" style="font-size:11.5px; font-weight:600; color:#ffffff !important; margin-top:4px;">
                                Sales: AED <?php echo number_format((float)(isset($dashboard_count['sales_amount']) ? $dashboard_count['sales_amount'] : 0), 2); ?>
                            </div>
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
                            <div class="dash-kpi-value" style="color:#ffffff !important;"><?php echo isset($dashboard_Tcount['count_leads']) ? (int)$dashboard_Tcount['count_leads'] : 0; ?></div>
                            <div class="dash-kpi-label" style="color:#ffffff !important;">Team Leads</div>
                            <div class="dash-kpi-sales" style="font-size:11.5px; font-weight:600; color:#ffffff !important; margin-top:4px;">
                                Sales: AED <?php echo number_format((float)(isset($dashboard_Tcount['sales_amount']) ? $dashboard_Tcount['sales_amount'] : 0), 2); ?>
                            </div>
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
                            <div class="dash-kpi-value" style="color:#ffffff !important;"><?php echo isset($dashboard_Tcount['count_orders']) ? (int)$dashboard_Tcount['count_orders'] : 0; ?></div>
                            <div class="dash-kpi-label" style="color:#ffffff !important;">Team Orders</div>
                            <div class="dash-kpi-sales" style="font-size:11.5px; font-weight:600; color:#ffffff !important; margin-top:4px;">
                                Sales: AED <?php echo number_format((float)(isset($dashboard_Tcount['sales_amount']) ? $dashboard_Tcount['sales_amount'] : 0), 2); ?>
                            </div>
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

        <div class="dash-section-divider" style="margin-top: 18px; margin-bottom: 12px;">
            <h3 class="dash-section-title" style="margin-bottom:0;">
                <span class="dash-section-title-icon">
                    <span></span><span></span>
                </span>
                <span>Database &amp; Lead States</span>
            </h3>
        </div>

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

                    <div class="row" style="margin-top: 6px;">
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

        <div class="dash-section-divider" style="margin-top: 10px; margin-bottom: 12px;">
            <h3 class="dash-section-title" style="margin-bottom:0;">
                <span class="dash-section-title-icon">
                    <span></span><span></span>
                </span>
                <span>Projections &amp; Achievement</span>
            </h3>
        </div>

        <div class="row">
            <div class="col-md-6 col-xs-12">
                <div class="dash-chart-card">
                    <div class="dash-chart-header" style="display:flex !important; justify-content:space-between !important; align-items:center !important; width:100% !important;">
                        <h4 class="dash-chart-title" style="margin:0 !important; margin-right:20px !important;">
                            <i class="fa fa-line-chart"></i> My Projection
                        </h4>
                        <div class="dash-chart-legend" style="display:inline-flex; align-items:center; gap:8px; font-size:12px; font-weight:600; color:#475569; margin-left:auto !important;">
                            <span style="display:inline-block; width:10px; height:10px; background-color:#0072f0; border-radius:2px;"></span>
                            <span>Projection</span>
                        </div>
                    </div>
                    <div class="dash-chart-body">
                        <canvas id="barChart2" height="230"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xs-12">
                <div class="dash-chart-card">
                    <div class="dash-chart-header" style="display:flex !important; justify-content:space-between !important; align-items:center !important; width:100% !important;">
                        <h4 class="dash-chart-title" style="margin:0 !important; margin-right:20px !important;">
                            <i class="fa fa-line-chart"></i> Team Projection
                        </h4>
                        <div class="dash-chart-legend" style="display:inline-flex; align-items:center; gap:8px; font-size:12px; font-weight:600; color:#475569; margin-left:auto !important;">
                            <span style="display:inline-block; width:10px; height:10px; background-color:#0072f0; border-radius:2px;"></span>
                            <span>Projection</span>
                        </div>
                    </div>
                    <div class="dash-chart-body">
                        <canvas id="barChart4" height="230"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 14px;">
            <div class="col-md-6 col-xs-12">
                <div class="dash-chart-card">
                    <div class="dash-chart-header" style="display:flex !important; justify-content:space-between !important; align-items:center !important; width:100% !important;">
                        <h4 class="dash-chart-title" style="margin:0 !important; margin-right:20px !important;">
                            <i class="fa fa-bar-chart"></i> My Achievement
                        </h4>
                        <div class="dash-chart-legend" style="display:inline-flex; align-items:center; gap:16px; font-size:12px; font-weight:600; color:#475569; margin-left:auto !important;">
                            <span style="display:inline-flex; align-items:center; gap:6px;">
                                <span style="display:inline-block; width:10px; height:10px; background-color:#0072f0; border-radius:2px;"></span>
                                <span>Target</span>
                            </span>
                            <span style="display:inline-flex; align-items:center; gap:6px;">
                                <span style="display:inline-block; width:10px; height:10px; background-color:#16a34a; border-radius:2px;"></span>
                                <span>Achieved</span>
                            </span>
                        </div>
                    </div>
                    <div class="dash-chart-body">
                        <canvas id="barChartAchieveMy" height="230"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xs-12">
                <div class="dash-chart-card">
                    <div class="dash-chart-header" style="display:flex !important; justify-content:space-between !important; align-items:center !important; width:100% !important;">
                        <h4 class="dash-chart-title" style="margin:0 !important; margin-right:20px !important;">
                            <i class="fa fa-bar-chart"></i> Team Achievement
                        </h4>
                        <div class="dash-chart-legend" style="display:inline-flex; align-items:center; gap:16px; font-size:12px; font-weight:600; color:#475569; margin-left:auto !important;">
                            <span style="display:inline-flex; align-items:center; gap:6px;">
                                <span style="display:inline-block; width:10px; height:10px; background-color:#0072f0; border-radius:2px;"></span>
                                <span>Target</span>
                            </span>
                            <span style="display:inline-flex; align-items:center; gap:6px;">
                                <span style="display:inline-block; width:10px; height:10px; background-color:#16a34a; border-radius:2px;"></span>
                                <span>Achieved</span>
                            </span>
                        </div>
                    </div>
                    <div class="dash-chart-body">
                        <canvas id="barChartAchieveTeam" height="230"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="dash-section-divider" style="margin-top: 18px; margin-bottom: 12px;">
            <h3 class="dash-section-title" style="margin-bottom:0;">
                <span class="dash-section-title-icon">
                    <span></span><span></span>
                </span>
                <span>Operational Activity and Breakdown</span>
            </h3>
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

        <div class="crm-op-7col-row">
            <div class="crm-col-7 col-xs-12">
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
                    <div style="display:flex; justify-content:center; align-items:center; padding:6px 0 6px 0;"><canvas id="pieCard_my_tickets" width="96" height="96" style="width:96px; height:96px;"></canvas></div>
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

            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><a href="<?php echo base_url(); ?>manager/tasks?team=0" style="color:inherit; text-decoration:none;"><i class="fa fa-tasks"></i> My Tasks</a></h4>
                        <a href="<?php echo base_url(); ?>manager/tasks?team=0" class="card-total-pill" style="text-decoration:none;"><?php echo $total_my_tasks; ?> Total</a>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; padding:6px 0 6px 0;"><canvas id="pieCard_my_tasks" width="96" height="96" style="width:96px; height:96px;"></canvas></div>
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

            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-calendar-check-o"></i> My Followups</h4>
                        <span class="card-total-pill"><?php echo $total_my_f; ?> Total</span>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; padding:6px 0 6px 0;"><canvas id="pieCard_my_f" width="96" height="96" style="width:96px; height:96px;"></canvas></div>
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

            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-handshake-o"></i> My Meetings</h4>
                        <span class="card-total-pill"><?php echo $total_my_m; ?> Total</span>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; padding:6px 0 6px 0;"><canvas id="pieCard_my_m" width="96" height="96" style="width:96px; height:96px;"></canvas></div>
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

            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-briefcase"></i> My Vertical FU</h4>
                        <span class="card-total-pill"><?php echo $total_my_vfu; ?> Total</span>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; padding:6px 0 6px 0;"><canvas id="pieCard_my_vfu" width="96" height="96" style="width:96px; height:96px;"></canvas></div>
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

            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-database" style="color:#0072f0;"></i> My Database</h4>
                        <span class="card-total-pill"><?php echo (int)$dashboard_db_ops_my['total_all']; ?> Total</span>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; padding:6px 0 6px 0;"><canvas id="pieCard_my_db" width="96" height="96" style="width:96px; height:96px;"></canvas></div>
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

            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-bullseye" style="color:#0072f0;"></i> My Leads</h4>
                        <span class="card-total-pill"><?php echo (int)$dashboard_lead_ops_my['total_all']; ?> Total</span>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; padding:6px 0 6px 0;"><canvas id="pieCard_my_leads" width="96" height="96" style="width:96px; height:96px;"></canvas></div>
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
        </div>

        <div class="crm-op-7col-row" style="margin-top:10px;">
            <div class="crm-col-7 col-xs-12">
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
                    <div style="display:flex; justify-content:center; align-items:center; padding:6px 0 6px 0;"><canvas id="pieCard_team_tickets" width="96" height="96" style="width:96px; height:96px;"></canvas></div>
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

            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><a href="<?php echo base_url(); ?>manager/tasks?team=1" style="color:inherit; text-decoration:none;"><i class="fa fa-tasks"></i> Team Tasks</a></h4>
                        <a href="<?php echo base_url(); ?>manager/tasks?team=1" class="card-total-pill" style="text-decoration:none;"><?php echo $total_team_tasks; ?> Total</a>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; padding:6px 0 6px 0;"><canvas id="pieCard_team_tasks" width="96" height="96" style="width:96px; height:96px;"></canvas></div>
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

            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-calendar-check-o"></i> Team Followups</h4>
                        <span class="card-total-pill"><?php echo $total_team_f; ?> Total</span>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; padding:6px 0 6px 0;"><canvas id="pieCard_team_f" width="96" height="96" style="width:96px; height:96px;"></canvas></div>
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

            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-handshake-o"></i> Team Meetings</h4>
                        <span class="card-total-pill"><?php echo $total_team_m; ?> Total</span>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; padding:6px 0 6px 0;"><canvas id="pieCard_team_m" width="96" height="96" style="width:96px; height:96px;"></canvas></div>
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

            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-briefcase"></i> Team Vertical FU</h4>
                        <span class="card-total-pill"><?php echo $total_team_vfu; ?> Total</span>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; padding:6px 0 6px 0;"><canvas id="pieCard_team_vfu" width="96" height="96" style="width:96px; height:96px;"></canvas></div>
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

            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-database" style="color:#0072f0;"></i> Team Database</h4>
                        <span class="card-total-pill"><?php echo (int)$dashboard_db_ops_team['total_all']; ?> Total</span>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; padding:6px 0 6px 0;"><canvas id="pieCard_team_db" width="96" height="96" style="width:96px; height:96px;"></canvas></div>
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

            <div class="crm-col-7 col-xs-12">
                <div class="crm-modern-stat-card">
                    <div class="card-top">
                        <h4 class="card-title"><i class="fa fa-bullseye" style="color:#0072f0;"></i> Team Leads</h4>
                        <span class="card-total-pill"><?php echo (int)$dashboard_lead_ops_team['total_all']; ?> Total</span>
                    </div>
                    <div style="display:flex; justify-content:center; align-items:center; padding:6px 0 6px 0;"><canvas id="pieCard_team_leads" width="96" height="96" style="width:96px; height:96px;"></canvas></div>
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
            echo json_encode(array('Oct-26', 'Nov-26', 'Dec-26', 'Jan-27', 'Feb-27', 'Mar-27'));
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

    var achieveMonthsMy = <?php echo json_encode(!empty($dashboard_achievement_my['labels']) ? $dashboard_achievement_my['labels'] : array()); ?>;
    var achieveTargetMy = <?php echo json_encode(!empty($dashboard_achievement_my['target']) ? $dashboard_achievement_my['target'] : array()); ?>;
    var achieveDataMy = <?php echo json_encode(!empty($dashboard_achievement_my['achievement']) ? $dashboard_achievement_my['achievement'] : array()); ?>;
    var achieveDatesMy = <?php echo json_encode(!empty($dashboard_achievement_my['month_dates']) ? $dashboard_achievement_my['month_dates'] : array()); ?>;

    var achieveMonthsTeam = <?php echo json_encode(!empty($dashboard_achievement_team['labels']) ? $dashboard_achievement_team['labels'] : array()); ?>;
    var achieveTargetTeam = <?php echo json_encode(!empty($dashboard_achievement_team['target']) ? $dashboard_achievement_team['target'] : array()); ?>;
    var achieveDataTeam = <?php echo json_encode(!empty($dashboard_achievement_team['achievement']) ? $dashboard_achievement_team['achievement'] : array()); ?>;
    var achieveDatesTeam = <?php echo json_encode(!empty($dashboard_achievement_team['month_dates']) ? $dashboard_achievement_team['month_dates'] : array()); ?>;

    var canvas2 = document.getElementById('barChart2');
    var chartInstance2 = null;
    if (canvas2) {
        canvas2.style.cursor = 'pointer';
        var ctx2 = canvas2.getContext('2d');
        chartInstance2 = new Chart(ctx2).Bar({
            labels: months,
            datasets: [{
                label: 'My Projection',
                fillColor: '#00c6f0',
                strokeColor: '#0096cc',
                highlightFill: '#38d9f8',
                highlightStroke: '#0096cc',
                data: myProData
            }]
        }, chartOptions);

        canvas2.onclick = function(evt) {
            if (chartInstance2 && typeof chartInstance2.getBarsAtEvent === 'function') {
                var activeBars = chartInstance2.getBarsAtEvent(evt);
                if (activeBars && activeBars.length > 0) {
                    var barLabel = activeBars[0].label;
                    var idx = months.indexOf(barLabel);
                    if (idx !== -1 && monthDates[idx]) {
                        window.location.href = '<?php echo base_url(); ?>manager/leads?exp_d1=' + encodeURIComponent(monthDates[idx].d1) + '&exp_d2=' + encodeURIComponent(monthDates[idx].d2);
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
                fillColor: '#00c6f0',
                strokeColor: '#0096cc',
                highlightFill: '#38d9f8',
                highlightStroke: '#0096cc',
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

    function renderAchieveChart(canvasId, labels, target, achieve, dates, baseUrl) {
        var canvas = document.getElementById(canvasId);
        if (!canvas) return null;
        canvas.style.cursor = 'pointer';
        var ctx = canvas.getContext('2d');
        var achieveOptions = $.extend({}, chartOptions, {
            scaleLabel: "<%= 'AED ' + Number(value).toLocaleString() %>",
            tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= 'AED ' + Number(value).toLocaleString() %>",
            multiTooltipTemplate: "<%= datasetLabel %>: <%= 'AED ' + Number(value).toLocaleString() %>"
        });
        var inst = new Chart(ctx).Bar({
            labels: labels,
            datasets: [
                {
                    label: 'Target',
                    fillColor: 'rgba(0, 198, 240, 0.85)',
                    strokeColor: '#00c6f0',
                    highlightFill: 'rgba(0, 198, 240, 1)',
                    highlightStroke: '#0096cc',
                    data: target
                },
                {
                    label: 'Achievement',
                    fillColor: 'rgba(22, 163, 74, 0.85)',
                    strokeColor: '#16a34a',
                    highlightFill: 'rgba(22, 163, 74, 1)',
                    highlightStroke: '#15803d',
                    data: achieve
                }
            ]
        }, achieveOptions);

        canvas.onclick = function(evt) {
            if (inst && typeof inst.getBarsAtEvent === 'function') {
                var activeBars = inst.getBarsAtEvent(evt);
                if (activeBars && activeBars.length > 0) {
                    var barLabel = activeBars[0].label;
                    var idx = labels.indexOf(barLabel);
                    if (idx !== -1 && dates && dates[idx]) {
                        window.location.href = baseUrl + '?exp_d1=' + encodeURIComponent(dates[idx].d1) + '&exp_d2=' + encodeURIComponent(dates[idx].d2);
                    }
                }
            }
        };
        return inst;
    }

    renderAchieveChart('barChartAchieveMy', achieveMonthsMy, achieveTargetMy, achieveDataMy, achieveDatesMy, '<?php echo base_url(); ?>manager/leads');
    renderAchieveChart('barChartAchieveTeam', achieveMonthsTeam, achieveTargetTeam, achieveDataTeam, achieveDatesTeam, '<?php echo base_url(); ?>manager/team/leads');

    $('.crm-modern-stat-chip').on('click', function(e) {
        var targetUrl = $(this).attr('href');
        if (targetUrl && targetUrl !== '#' && targetUrl.indexOf('javascript') === -1) {
            window.location.href = targetUrl;
        }
    });

    var customOpTooltip = function(tooltip) {
        var el = $('#chartjs-op-tooltip');
        if (!el.length) {
            $('body').append('<div id="chartjs-op-tooltip" style="position: fixed; z-index: 99999999; pointer-events: none; opacity: 0; background: rgba(15, 23, 42, 0.96); color: #ffffff; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, sans-serif; white-space: nowrap; box-shadow: 0 4px 14px rgba(0,0,0,0.35); transition: opacity 0.08s ease;"></div>');
            el = $('#chartjs-op-tooltip');
        }
        if (!tooltip || !tooltip.text) {
            el.css({ opacity: 0 });
            return;
        }
        el.html(tooltip.text);
        var tipW = el.outerWidth() || 80;
        var tipH = el.outerHeight() || 28;
        var top = (tooltip.clientY || tooltip.y) - tipH - 12;
        var left = (tooltip.clientX || tooltip.x) - (tipW / 2);
        if (top < 10) top = (tooltip.clientY || tooltip.y) + 16;
        if (left < 10) left = 10;
        if (left + tipW > $(window).width() - 10) left = $(window).width() - tipW - 10;
        el.css({
            opacity: 1,
            top: top + 'px',
            left: left + 'px'
        });
    };

    function renderOpPie(canvasId, missed, last7, today, next7, future) {
        var cv = document.getElementById(canvasId);
        if (!cv) return;
        var ctx = cv.getContext('2d');
        var m = parseInt(missed) || 0;
        var l = parseInt(last7) || 0;
        var t = parseInt(today) || 0;
        var n = parseInt(next7) || 0;
        var f = parseInt(future) || 0;
        var data = [];
        if (m === 0 && l === 0 && t === 0 && n === 0 && f === 0) {
            data = [{ value: 1, color: '#e2e8f0', label: 'Empty' }];
        } else {
            if (m > 0) data.push({ value: m, color: '#dd4b39', label: 'All Missed' });
            if (l > 0) data.push({ value: l, color: '#ff851b', label: 'Last 7 Days' });
            if (t > 0) data.push({ value: t, color: '#00c0ef', label: 'Today' });
            if (n > 0) data.push({ value: n, color: '#0164e8', label: 'Next 7 Days' });
            if (f > 0) data.push({ value: f, color: '#00a65a', label: 'All Future' });
        }
        var chartInst = new Chart(ctx).Doughnut(data, {
            percentageInnerCutout: 52,
            segmentShowStroke: true,
            segmentStrokeColor: '#ffffff',
            segmentStrokeWidth: 1,
            animation: false,


            responsive: false,
            maintainAspectRatio: true,
            showTooltips: false
        });

        $(cv).on('mousemove', function(e) {
            var pts = chartInst.getSegmentsAtEvent(e);
            if (pts && pts.length > 0) {
                var seg = pts[0];
                if (seg.label && seg.label !== 'Empty') {
                    customOpTooltip({
                        text: seg.label + ': ' + seg.value,
                        clientX: e.clientX,
                        clientY: e.clientY
                    });
                } else {
                    customOpTooltip(null);
                }
            } else {
                customOpTooltip(null);
            }
        }).on('mouseleave', function() {
            customOpTooltip(null);
        });
    }

    renderOpPie('pieCard_my_tickets', '<?php echo isset($dashboard_tickets["total_missed"]) ? (int)$dashboard_tickets["total_missed"] : 0; ?>', '<?php echo isset($dashboard_tickets["total_lastweek"]) ? (int)$dashboard_tickets["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_tickets["total_today"]) ? (int)$dashboard_tickets["total_today"] : 0; ?>', '<?php echo isset($dashboard_tickets["total_nextweek"]) ? (int)$dashboard_tickets["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_tickets["total_future"]) ? (int)$dashboard_tickets["total_future"] : 0; ?>');
    renderOpPie('pieCard_my_tasks', '<?php echo isset($dashboard_tasks["total_missed"]) ? (int)$dashboard_tasks["total_missed"] : 0; ?>', '<?php echo isset($dashboard_tasks["total_lastweek"]) ? (int)$dashboard_tasks["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_tasks["total_today"]) ? (int)$dashboard_tasks["total_today"] : 0; ?>', '<?php echo isset($dashboard_tasks["total_nextweek"]) ? (int)$dashboard_tasks["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_tasks["total_future"]) ? (int)$dashboard_tasks["total_future"] : 0; ?>');
    renderOpPie('pieCard_my_f', '<?php echo isset($dashboard_f["total_missed"]) ? (int)$dashboard_f["total_missed"] : 0; ?>', '<?php echo isset($dashboard_f["total_lastweek"]) ? (int)$dashboard_f["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_f["total_today"]) ? (int)$dashboard_f["total_today"] : 0; ?>', '<?php echo isset($dashboard_f["total_nextweek"]) ? (int)$dashboard_f["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_f["total_future"]) ? (int)$dashboard_f["total_future"] : 0; ?>');
    renderOpPie('pieCard_my_m', '<?php echo isset($dashboard_m["total_missed"]) ? (int)$dashboard_m["total_missed"] : 0; ?>', '<?php echo isset($dashboard_m["total_lastweek"]) ? (int)$dashboard_m["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_m["total_today"]) ? (int)$dashboard_m["total_today"] : 0; ?>', '<?php echo isset($dashboard_m["total_nextweek"]) ? (int)$dashboard_m["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_m["total_future"]) ? (int)$dashboard_m["total_future"] : 0; ?>');
    renderOpPie('pieCard_my_vfu', '<?php echo isset($dashboard_vfu["total_missed"]) ? (int)$dashboard_vfu["total_missed"] : 0; ?>', '<?php echo isset($dashboard_vfu["total_lastweek"]) ? (int)$dashboard_vfu["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_vfu["total_today"]) ? (int)$dashboard_vfu["total_today"] : 0; ?>', '<?php echo isset($dashboard_vfu["total_nextweek"]) ? (int)$dashboard_vfu["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_vfu["total_future"]) ? (int)$dashboard_vfu["total_future"] : 0; ?>');
    renderOpPie('pieCard_my_db', '<?php echo isset($dashboard_db_ops_my["total_missed"]) ? (int)$dashboard_db_ops_my["total_missed"] : 0; ?>', '<?php echo isset($dashboard_db_ops_my["total_lastweek"]) ? (int)$dashboard_db_ops_my["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_db_ops_my["total_today"]) ? (int)$dashboard_db_ops_my["total_today"] : 0; ?>', '<?php echo isset($dashboard_db_ops_my["total_nextweek"]) ? (int)$dashboard_db_ops_my["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_db_ops_my["total_future"]) ? (int)$dashboard_db_ops_my["total_future"] : 0; ?>');
    renderOpPie('pieCard_my_leads', '<?php echo isset($dashboard_lead_ops_my["total_missed"]) ? (int)$dashboard_lead_ops_my["total_missed"] : 0; ?>', '<?php echo isset($dashboard_lead_ops_my["total_lastweek"]) ? (int)$dashboard_lead_ops_my["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_lead_ops_my["total_today"]) ? (int)$dashboard_lead_ops_my["total_today"] : 0; ?>', '<?php echo isset($dashboard_lead_ops_my["total_nextweek"]) ? (int)$dashboard_lead_ops_my["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_lead_ops_my["total_future"]) ? (int)$dashboard_lead_ops_my["total_future"] : 0; ?>');
    renderOpPie('pieCard_team_tickets', '<?php echo isset($dashboard_Ttickets["total_missed"]) ? (int)$dashboard_Ttickets["total_missed"] : 0; ?>', '<?php echo isset($dashboard_Ttickets["total_lastweek"]) ? (int)$dashboard_Ttickets["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_Ttickets["total_today"]) ? (int)$dashboard_Ttickets["total_today"] : 0; ?>', '<?php echo isset($dashboard_Ttickets["total_nextweek"]) ? (int)$dashboard_Ttickets["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_Ttickets["total_future"]) ? (int)$dashboard_Ttickets["total_future"] : 0; ?>');
    renderOpPie('pieCard_team_tasks', '<?php echo isset($dashboard_Ttasks["total_missed"]) ? (int)$dashboard_Ttasks["total_missed"] : 0; ?>', '<?php echo isset($dashboard_Ttasks["total_lastweek"]) ? (int)$dashboard_Ttasks["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_Ttasks["total_today"]) ? (int)$dashboard_Ttasks["total_today"] : 0; ?>', '<?php echo isset($dashboard_Ttasks["total_nextweek"]) ? (int)$dashboard_Ttasks["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_Ttasks["total_future"]) ? (int)$dashboard_Ttasks["total_future"] : 0; ?>');
    renderOpPie('pieCard_team_f', '<?php echo isset($dashboard_Tf["total_missed"]) ? (int)$dashboard_Tf["total_missed"] : 0; ?>', '<?php echo isset($dashboard_Tf["total_lastweek"]) ? (int)$dashboard_Tf["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_Tf["total_today"]) ? (int)$dashboard_Tf["total_today"] : 0; ?>', '<?php echo isset($dashboard_Tf["total_nextweek"]) ? (int)$dashboard_Tf["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_Tf["total_future"]) ? (int)$dashboard_Tf["total_future"] : 0; ?>');
    renderOpPie('pieCard_team_m', '<?php echo isset($dashboard_Tm["total_missed"]) ? (int)$dashboard_Tm["total_missed"] : 0; ?>', '<?php echo isset($dashboard_Tm["total_lastweek"]) ? (int)$dashboard_Tm["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_Tm["total_today"]) ? (int)$dashboard_Tm["total_today"] : 0; ?>', '<?php echo isset($dashboard_Tm["total_nextweek"]) ? (int)$dashboard_Tm["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_Tm["total_future"]) ? (int)$dashboard_Tm["total_future"] : 0; ?>');
    renderOpPie('pieCard_team_vfu', '<?php echo isset($dashboard_Tvfu["total_missed"]) ? (int)$dashboard_Tvfu["total_missed"] : 0; ?>', '<?php echo isset($dashboard_Tvfu["total_lastweek"]) ? (int)$dashboard_Tvfu["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_Tvfu["total_today"]) ? (int)$dashboard_Tvfu["total_today"] : 0; ?>', '<?php echo isset($dashboard_Tvfu["total_nextweek"]) ? (int)$dashboard_Tvfu["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_Tvfu["total_future"]) ? (int)$dashboard_Tvfu["total_future"] : 0; ?>');
    renderOpPie('pieCard_team_db', '<?php echo isset($dashboard_db_ops_team["total_missed"]) ? (int)$dashboard_db_ops_team["total_missed"] : 0; ?>', '<?php echo isset($dashboard_db_ops_team["total_lastweek"]) ? (int)$dashboard_db_ops_team["total_lastweek"] : 0; ?>', '<?php echo isset($dashboard_db_ops_team["total_today"]) ? (int)$dashboard_db_ops_team["total_today"] : 0; ?>', '<?php echo isset($dashboard_db_ops_team["total_nextweek"]) ? (int)$dashboard_db_ops_team["total_nextweek"] : 0; ?>', '<?php echo isset($dashboard_db_ops_team["total_future"]) ? (int)$dashboard_db_ops_team["total_future"] : 0; ?>');
    renderOpPie('pieCard_team_leads', '<?php echo (int)$dashboard_lead_ops_team["total_missed"]; ?>', '<?php echo (int)$dashboard_lead_ops_team["total_lastweek"]; ?>', '<?php echo (int)$dashboard_lead_ops_team["total_today"]; ?>', '<?php echo (int)$dashboard_lead_ops_team["total_nextweek"]; ?>', '<?php echo (int)$dashboard_lead_ops_team["total_future"]; ?>');
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
