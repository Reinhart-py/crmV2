<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<style>
.role-edit-container {
    padding: 0 16px 80px 16px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: #0f172a;
}
.role-meta-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    padding: 24px;
    margin-bottom: 24px;
}
.role-field-label {
    font-size: 13px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 8px;
    display: block;
}
.role-input, .role-select {
    width: 100%;
    height: 42px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 8px 14px;
    font-size: 14px;
    color: #0f172a;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
    outline: none;
    box-sizing: border-box;
}
.role-input:focus, .role-select:focus {
    border-color: #0072f0;
    box-shadow: 0 0 0 3px rgba(0, 114, 240, 0.15);
}
.matrix-global-bar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.matrix-title {
    font-size: 17px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    letter-spacing: -0.01em;
    display: flex;
    align-items: center;
    gap: 8px;
}
.matrix-subtitle {
    font-size: 13px;
    color: #64748b;
    margin-top: 4px;
}
.perm-search-box {
    position: relative;
    min-width: 280px;
}
.perm-search-box i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 13px;
}
.perm-search-input {
    width: 100%;
    height: 38px;
    padding: 8px 12px 8px 34px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    color: #0f172a;
    outline: none;
    transition: all 0.15s ease;
}
.perm-search-input:focus {
    background: #ffffff;
    border-color: #0072f0;
    box-shadow: 0 0 0 3px rgba(0, 114, 240, 0.12);
}
.btn-quick-bulk {
    background: #ffffff;
    color: #334155;
    border: 1px solid #cbd5e1;
    padding: 8px 14px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-quick-bulk:hover {
    background: #f8fafc;
    border-color: #94a3b8;
    color: #0f172a;
}
.btn-bulk-blue {
    background: #eff6ff;
    color: #0072f0 !important;
    border-color: #bfdbfe;
}
.btn-bulk-blue:hover {
    background: #dbeafe;
    border-color: #93c5fd;
    color: #005bb5 !important;
}
.btn-bulk-green {
    background: #f0fdf4;
    color: #16a34a !important;
    border-color: #bbf7d0;
}
.btn-bulk-green:hover {
    background: #dcfce7;
    border-color: #86efac;
    color: #15803d !important;
}
.btn-bulk-red {
    background: #fef2f2;
    color: #dc2626 !important;
    border-color: #fecaca;
}
.btn-bulk-red:hover {
    background: #fee2e2;
    border-color: #fca5a5;
    color: #b91c1c !important;
}
.module-group-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-left: 4px solid #0072f0;
    border-radius: 12px;
    margin-bottom: 22px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    transition: box-shadow 0.2s ease;
}
.module-group-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
}
.module-group-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}
.module-group-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}
.module-group-title i {
    color: #0072f0;
    font-size: 16px;
}
.mod-stat-badge {
    font-size: 11px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.mod-stat-total { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
.mod-stat-full { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.mod-stat-view { background: #eff6ff; color: #0072f0; border: 1px solid #bfdbfe; }
.mod-stat-none { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
.module-quick-pill {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 5px 12px;
    font-size: 11.5px;
    font-weight: 600;
    color: #475569;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.module-quick-pill:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.pill-hover-red:hover {
    background: #fee2e2;
    border-color: #fca5a5;
    color: #dc2626;
}
.pill-hover-blue:hover {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #0072f0;
}
.pill-hover-green:hover {
    background: #dcfce7;
    border-color: #86efac;
    color: #16a34a;
}
.perm-row {
    padding: 14px 20px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    transition: background 0.15s ease;
}
.perm-row:last-child {
    border-bottom: none;
}
.perm-row:hover {
    background: #f8fafc;
}
.perm-page-name {
    font-size: 14px;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 2px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.perm-page-desc {
    font-size: 12px;
    color: #64748b;
    margin: 0;
    line-height: 1.4;
}
.seg-switch {
    display: inline-flex;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 3px;
    gap: 3px;
    flex-shrink: 0;
}
.seg-option {
    margin: 0;
    padding: 6px 14px;
    font-size: 12px;
    font-weight: 500;
    color: #64748b;
    border: 1px solid transparent;
    border-radius: 6px;
    cursor: pointer;
    user-select: none;
    transition: all 0.15s ease;
    display: flex;
    align-items: center;
    gap: 5px;
}
.seg-option input[type="radio"] {
    display: none;
}
.seg-option:hover:not(.active) {
    background: #ffffff;
    color: #1e293b;
}
.seg-option.opt-no_access.active {
    background: #fef2f2;
    color: #dc2626;
    border-color: #fecaca;
    font-weight: 600;
    box-shadow: 0 1px 2px rgba(220, 38, 38, 0.08);
}
.seg-option.opt-read.active {
    background: #eff6ff;
    color: #0072f0;
    border-color: #bfdbfe;
    font-weight: 600;
    box-shadow: 0 1px 2px rgba(0, 114, 240, 0.12);
}
.seg-option.opt-write.active {
    background: #f0fdf4;
    color: #16a34a;
    border-color: #bbf7d0;
    font-weight: 600;
    box-shadow: 0 1px 2px rgba(22, 163, 74, 0.12);
}
.sticky-save-bar {
    position: sticky;
    bottom: 0;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    padding: 16px 24px;
    margin: 30px -16px -80px -16px;
    box-shadow: 0 -4px 16px rgba(0,0,0,0.06);
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 99;
}
.btn-save-role {
    background: #0072f0;
    color: #ffffff !important;
    border: 1px solid #0060d0;
    border-radius: 8px;
    padding: 10px 28px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0, 114, 240, 0.25);
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none !important;
}
.btn-save-role:hover {
    background: #0060d0;
    border-color: #0050b5;
    box-shadow: 0 4px 12px rgba(0, 114, 240, 0.35);
}
.btn-cancel-role {
    background: #ffffff;
    color: #475569 !important;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 10px 22px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none !important;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-cancel-role:hover {
    background: #f8fafc;
    color: #0f172a !important;
    border-color: #94a3b8;
}
</style>

<div class="content-wrapper role-edit-container"> 
  <section class="content-header" style="padding: 20px 0 16px 0;">
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <div>
        <h1 style="font-size: 22px; font-weight: 700; color: #0f172a; margin: 0; letter-spacing: -0.02em;">
          Create New Role
        </h1>
      </div>
      <div>
        <a href="<?php echo base_url(); ?>manager/roles" class="btn-cancel-role" style="padding: 8px 16px; font-size: 13px;">
          <i class="fa fa-arrow-left"></i> Back to Roles
        </a>
      </div>
    </div>
  </section>

  <section class="content" style="padding: 0;">
    <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
    <?php echo get_message($this->session->flashdata('message'),'message'); ?> 

    <form method="post" action="<?php echo base_url(); ?>manager/roles/add" id="roleAddForm">
      <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

      <div class="role-meta-card">
        <div class="row">
          <div class="col-md-4 col-sm-6">
            <div class="form-group" style="margin-bottom: 0;">
              <label class="role-field-label">Role Title *</label>
              <input type="text" name="title" class="role-input" placeholder="e.g. Sales Executive" required />
            </div>                  
          </div>

          <div class="col-md-4 col-sm-6">
            <div class="form-group" style="margin-bottom: 0;">
              <label class="role-field-label">Parent Hierarchy Role</label>
              <select class="role-select" name="parent_id">
                <option value="0">Root Level (Top Level)</option>
                <?php if(isset($r_list) && !empty($r_list)){
                  foreach($r_list as $cli){ ?>
                  <option value="<?php echo $cli['id']; ?>"><?php echo htmlspecialchars($cli['title']); ?></option>
                <?php } } ?>
              </select>
            </div> 
          </div>

          <div class="col-md-4 col-sm-12">
            <div class="form-group" style="margin-bottom: 0;">
              <label class="role-field-label">Role Status</label>
              <select class="role-select" name="status">
                <option value="1" selected>Active</option>
                <option value="0">Disabled</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <?php
        $all_modules = array(
          'dashboard' => array(
            'title' => 'Dashboard',
            'icon'  => 'fa fa-dashboard',
            'desc'  => 'Operational analytics, KPI cards, operations progress, and activity logs',
            'pages' => array(
              'dashboard'           => array('title' => 'Dashboard Overview', 'desc' => 'High-level business KPIs, pipeline metrics, and revenue charts')
            )
          ),
          'my_zone' => array(
            'title' => 'My Zone (Personal Portal)',
            'icon'  => 'fa fa-th-large',
            'desc'  => 'Personal database, assigned pipeline leads, orders, sales, and follow-ups',
            'pages' => array(
              'database'            => array('title' => 'Add & View Database', 'desc' => 'Browse and register unverified customer/company records'),
              'leads'               => array('title' => 'View Leads', 'desc' => 'Access personal converted pipeline leads'),
              'orders'              => array('title' => 'View Orders', 'desc' => 'Track personal submitted client orders'),
              'sales'               => array('title' => 'View Sales', 'desc' => 'Access confirmed closed sales records'),
              'followups'           => array('title' => 'My Follow-Up', 'desc' => 'Personal scheduled follow-up calls and tasks'),
              'meetings'            => array('title' => 'My Meetings', 'desc' => 'Personal scheduled meetings calendar'),
              'verticals'           => array('title' => 'Add & View Verticals', 'desc' => 'Manage specialized vertical products & opportunities'),
              'vertical_followups'  => array('title' => 'Vertical Follow-up', 'desc' => 'Product-specific client follow-up scheduling'),
              'internal_tasks'      => array('title' => 'Internal Tasks', 'desc' => 'Internal office and corporate operations tasks'),
              'external_tasks'      => array('title' => 'External Tasks', 'desc' => 'External fieldwork, client visits, and vendor tasks'),
              'database_transfer'   => array('title' => 'Database Transfer', 'desc' => 'Transfer personal records to other team members')
            )
          ),
          'team' => array(
            'title' => 'Team Management',
            'icon'  => 'fa fa-users',
            'desc'  => 'Subordinate supervision, team database, team pipeline, and reallocations',
            'pages' => array(
              'team_members'        => array('title' => 'Members Directory', 'desc' => 'View team members, profiles, and reporting structures'),
              'team_chart'          => array('title' => 'Organization Chart', 'desc' => 'Visual company hierarchy and manager reporting trees'),
              'team_database'       => array('title' => 'Team Database', 'desc' => 'Supervise all database records owned by subordinates'),
              'team_leads'          => array('title' => 'Team Leads', 'desc' => 'Monitor subordinate converted pipeline leads'),
              'team_orders'         => array('title' => 'Team Orders', 'desc' => 'Track orders submitted across subordinate team members'),
              'team_verticals'      => array('title' => 'Team Verticals', 'desc' => 'Subordinate vertical product opportunities'),
              'team_followups'      => array('title' => 'Team Follow-ups', 'desc' => 'Monitor overdue and upcoming team follow-ups'),
              'team_meetings'       => array('title' => 'Team Meetings', 'desc' => 'Supervise client appointments booked by team members'),
              'team_assignmeetings' => array('title' => 'Assigned Meetings', 'desc' => 'Distribute inbound meetings to team members'),
              'team_assignleads'    => array('title' => 'Assign Database', 'desc' => 'Batch reassign and allocate database records'),
              'team_tasks'          => array('title' => 'Team Tasks', 'desc' => 'Assign, track, and reallocate team tasks'),
              'team_tickets'        => array('title' => 'Team Tickets', 'desc' => 'Oversee support and customer service tickets')
            )
          ),
          'back_office' => array(
            'title' => 'Back Office Operations',
            'icon'  => 'fa fa-briefcase',
            'desc'  => 'Verification workflows, lead conversion approvals, order processing, and client onboarding',
            'pages' => array(
              'lead_review'         => array('title' => 'Pending Leads Approval', 'desc' => 'Approve or reject database conversion requests into Leads'),
              'orders_pending'      => array('title' => 'Pending Order Process', 'desc' => 'Review and process submitted client orders'),
              'kam_onboard'         => array('title' => 'Onboard Client', 'desc' => 'Transition completed orders into client onboarding'),
              'orders_management'   => array('title' => 'All Orders Management', 'desc' => 'Full visibility and status management over all company orders')
            )
          ),
          'kam' => array(
            'title' => 'KAM (Key Account Management)',
            'icon'  => 'fa fa-handshake-o',
            'desc'  => 'Corporate client portfolio, schedules, milestones, and account renewal reminders',
            'pages' => array(
              'kam_clients'         => array('title' => 'My Clients Portfolio', 'desc' => 'View and manage dedicated corporate client accounts'),
              'kam_schedule'        => array('title' => 'Create & View Schedules', 'desc' => 'Manage recurring client visits and delivery appointments'),
              'kam_task_followup'   => array('title' => 'Task Follow-up', 'desc' => 'Track client task milestones and completion statuses'),
              'kam_reminders'       => array('title' => 'Account Reminders', 'desc' => 'Contract renewal alerts and corporate notifications')
            )
          ),
          'system' => array(
            'title' => 'System & Administration',
            'icon'  => 'fa fa-cog',
            'desc'  => 'Security access control, user provisioning, categories, import/export, logs, and APIs',
            'pages' => array(
              'users'               => array('title' => 'User Management', 'desc' => 'Create, view, and update staff user accounts'),
              'roles'               => array('title' => 'Roles & Permissions', 'desc' => 'Manage role definitions and security permission matrices'),
              'terms'               => array('title' => 'Categories & Taxonomy', 'desc' => 'Manage industries, call statuses, and categories'),
              'leads_import'        => array('title' => 'Import Leads', 'desc' => 'Bulk upload leads from CSV spreadsheets'),
              'leads_export'        => array('title' => 'Export Leads', 'desc' => 'Export filtered leads data to CSV format'),
              'assigned_leads'      => array('title' => 'Allotted / Non-Allotted Database', 'desc' => 'Manage unassigned database pools and allocations'),
              'admin_logs'          => array('title' => 'Admin Audit Logs', 'desc' => 'View administrative and security action logs'),
              'calling_api'         => array('title' => 'Calling API & Telephony', 'desc' => 'Configure 3CX softphone and CDR integrations'),
              'integrations'        => array('title' => 'Third-Party Integrations', 'desc' => 'API credentials, webhooks, and third-party services')
            )
          ),
          'reports' => array(
            'title' => 'Reports & Analytics',
            'icon'  => 'fa fa-bar-chart',
            'desc'  => 'Financial tracking, collection reports, forecasts, and downloadable analytics',
            'pages' => array(
              'reports_revenue'     => array('title' => 'Revenue List', 'desc' => 'Invoiced transactions and sales performance reports'),
              'reports_projections' => array('title' => 'Company Projections', 'desc' => 'Target forecasting, revenue pipelines, and sales goals'),
              'reports_download'    => array('title' => 'Download Reports', 'desc' => 'Export comprehensive CRM performance reports')
            )
          )
        );
      ?>

      <div class="matrix-global-bar">
        <div>
          <h2 class="matrix-title">
            <i class="fa fa-shield" style="color: #0072f0;"></i> System Permissions Matrix
          </h2>
          <div class="matrix-subtitle">Configure initial access levels across all CRM modules and functional pages</div>
        </div>

        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
          <div class="perm-search-box">
            <i class="fa fa-search"></i>
            <input type="text" id="permSearch" class="perm-search-input" placeholder="Search page (e.g. Orders, Leads, API...)" onkeyup="filterPerms(this.value)">
          </div>

          <div style="display: flex; gap: 6px; flex-wrap: wrap;">
            <button type="button" class="btn-quick-bulk btn-bulk-red" onclick="setAllPermissions('no_access')">
              <i class="fa fa-ban"></i> All None
            </button>
            <button type="button" class="btn-quick-bulk btn-bulk-blue" onclick="setAllPermissions('read')">
              <i class="fa fa-eye"></i> All View
            </button>
            <button type="button" class="btn-quick-bulk btn-bulk-green" onclick="setAllPermissions('write')">
              <i class="fa fa-check-circle"></i> All Full
            </button>
          </div>
        </div>
      </div>

      <?php foreach ($all_modules as $mod_key => $mod_data) { ?>
        <div class="module-group-card" data-module="<?php echo $mod_key; ?>">
          <div class="module-group-header">
            <div>
              <h3 class="module-group-title">
                <i class="<?php echo $mod_data['icon']; ?>"></i>
                <?php echo htmlspecialchars($mod_data['title']); ?>
                <span class="mod-stat-badge mod-stat-total" id="badge-<?php echo $mod_key; ?>-total">
                  <?php echo count($mod_data['pages']); ?> Pages
                </span>
                <span class="mod-stat-badge mod-stat-full" id="badge-<?php echo $mod_key; ?>-full">0 Full</span>
                <span class="mod-stat-badge mod-stat-view" id="badge-<?php echo $mod_key; ?>-view">0 View</span>
                <span class="mod-stat-badge mod-stat-none" id="badge-<?php echo $mod_key; ?>-none">0 None</span>
              </h3>
              <div style="font-size: 12px; color: #64748b; margin-top: 4px;">
                <?php echo htmlspecialchars($mod_data['desc']); ?>
              </div>
            </div>
            <div style="display: flex; gap: 6px;">
              <button type="button" class="module-quick-pill pill-hover-red" onclick="setModulePermissions('<?php echo $mod_key; ?>', 'no_access')">
                <i class="fa fa-ban"></i> None
              </button>
              <button type="button" class="module-quick-pill pill-hover-blue" onclick="setModulePermissions('<?php echo $mod_key; ?>', 'read')">
                <i class="fa fa-eye"></i> View
              </button>
              <button type="button" class="module-quick-pill pill-hover-green" onclick="setModulePermissions('<?php echo $mod_key; ?>', 'write')">
                <i class="fa fa-check"></i> Full
              </button>
            </div>
          </div>

          <div class="module-group-body">
            <?php foreach ($mod_data['pages'] as $page_key => $page_info) { 
              if ($mod_key === 'system' || $mod_key === 'back_office') {
                $cur_perm = 'no_access';
              } else {
                $cur_perm = 'read';
              }
            ?>
              <div class="perm-row" data-page-key="<?php echo $page_key; ?>" data-title="<?php echo strtolower(htmlspecialchars($page_info['title'])); ?>" data-desc="<?php echo strtolower(htmlspecialchars($page_info['desc'])); ?>">
                <div style="flex: 1; min-width: 240px;">
                  <h4 class="perm-page-name">
                    <?php echo htmlspecialchars($page_info['title']); ?>
                  </h4>
                  <p class="perm-page-desc"><?php echo htmlspecialchars($page_info['desc']); ?></p>
                </div>

                <div class="seg-switch" data-key="<?php echo $page_key; ?>">
                  <label class="seg-option opt-no_access <?php if ($cur_perm === 'no_access') echo 'active'; ?>" onclick="applySeg(this, 'no_access')">
                    <input type="radio" name="permissions[<?php echo $page_key; ?>]" value="no_access" <?php if ($cur_perm === 'no_access') echo 'checked'; ?>>
                    <i class="fa fa-ban" style="font-size: 11px;"></i> No Access
                  </label>
                  <label class="seg-option opt-read <?php if ($cur_perm === 'read') echo 'active'; ?>" onclick="applySeg(this, 'read')">
                    <input type="radio" name="permissions[<?php echo $page_key; ?>]" value="read" <?php if ($cur_perm === 'read') echo 'checked'; ?>>
                    <i class="fa fa-eye" style="font-size: 11px;"></i> View Only
                  </label>
                  <label class="seg-option opt-write <?php if ($cur_perm === 'write') echo 'active'; ?>" onclick="applySeg(this, 'write')">
                    <input type="radio" name="permissions[<?php echo $page_key; ?>]" value="write" <?php if ($cur_perm === 'write') echo 'checked'; ?>>
                    <i class="fa fa-check-circle" style="font-size: 11px;"></i> Full Access
                  </label>
                </div>
              </div>
            <?php } ?>
          </div>
        </div>
      <?php } ?>

      <div class="sticky-save-bar">
        <div>
          <span style="font-size: 13.5px; font-weight: 600; color: #0f172a;" id="permCounter">Calculating permissions...</span>
          <span style="font-size: 12px; color: #64748b; margin-left: 10px;">Immediate security rule update</span>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
          <a href="<?php echo base_url(); ?>manager/roles" class="btn-cancel-role">
            <i class="fa fa-times"></i> Cancel
          </a>
          <button type="submit" class="btn-save-role">
            <i class="fa fa-check"></i> Create Role &amp; Permissions
          </button>
        </div>
      </div>

    </form>
  </section>
</div>

<script>
function applySeg(labelEl, val) {
  var parentSeg = labelEl.closest('.seg-switch');
  if (parentSeg) {
    var allLabels = parentSeg.querySelectorAll('.seg-option');
    allLabels.forEach(function(l) { l.classList.remove('active'); });
    labelEl.classList.add('active');
    var input = labelEl.querySelector('input[type="radio"]');
    if (input) {
      input.checked = true;
    }
  }
  updateCounter();
}

function setModulePermissions(moduleKey, level) {
  var card = document.querySelector('.module-group-card[data-module="' + moduleKey + '"]');
  if (card) {
    var segs = card.querySelectorAll('.seg-switch');
    segs.forEach(function(seg) {
      var targetInput = seg.querySelector('input[value="' + level + '"]');
      if (targetInput) {
        targetInput.checked = true;
        var parentLabel = targetInput.closest('.seg-option');
        var allLabels = seg.querySelectorAll('.seg-option');
        allLabels.forEach(function(l) { l.classList.remove('active'); });
        if (parentLabel) parentLabel.classList.add('active');
      }
    });
  }
  updateCounter();
}

function setAllPermissions(level) {
  var allInputs = document.querySelectorAll('.seg-switch input[value="' + level + '"]');
  allInputs.forEach(function(inp) {
    inp.checked = true;
    var parentLabel = inp.closest('.seg-option');
    var seg = inp.closest('.seg-switch');
    if (seg) {
      var allLabels = seg.querySelectorAll('.seg-option');
      allLabels.forEach(function(l) { l.classList.remove('active'); });
      if (parentLabel) parentLabel.classList.add('active');
    }
  });
  updateCounter();
}

function filterPerms(query) {
  var q = (query || '').toLowerCase().trim();
  var rows = document.querySelectorAll('.perm-row');
  var cards = document.querySelectorAll('.module-group-card');
  
  if (!q) {
    rows.forEach(function(r) { r.style.display = ''; });
    cards.forEach(function(c) { c.style.display = ''; });
    return;
  }
  
  cards.forEach(function(card) {
    var cardRows = card.querySelectorAll('.perm-row');
    var matchedAny = false;
    cardRows.forEach(function(row) {
      var title = row.getAttribute('data-title') || '';
      var desc = row.getAttribute('data-desc') || '';
      var key = row.getAttribute('data-page-key') || '';
      if (title.indexOf(q) !== -1 || desc.indexOf(q) !== -1 || key.indexOf(q) !== -1) {
        row.style.display = '';
        matchedAny = true;
      } else {
        row.style.display = 'none';
      }
    });
    card.style.display = matchedAny ? '' : 'none';
  });
}

function updateCounter() {
  var total = document.querySelectorAll('.seg-switch').length;
  var full = document.querySelectorAll('.seg-switch input[value="write"]:checked').length;
  var view = document.querySelectorAll('.seg-switch input[value="read"]:checked').length;
  var none = document.querySelectorAll('.seg-switch input[value="no_access"]:checked').length;
  
  var el = document.getElementById('permCounter');
  if (el) {
    el.innerHTML = '<span style="color:#16a34a;"><i class="fa fa-check-circle"></i> <strong>' + full + '</strong> Full Access</span> &nbsp;&bull;&nbsp; <span style="color:#0072f0;"><i class="fa fa-eye"></i> <strong>' + view + '</strong> View Only</span> &nbsp;&bull;&nbsp; <span style="color:#dc2626;"><i class="fa fa-ban"></i> <strong>' + none + '</strong> No Access</span>';
  }
  
  var cards = document.querySelectorAll('.module-group-card');
  cards.forEach(function(card) {
    var modKey = card.getAttribute('data-module');
    var mFull = card.querySelectorAll('.seg-switch input[value="write"]:checked').length;
    var mView = card.querySelectorAll('.seg-switch input[value="read"]:checked').length;
    var mNone = card.querySelectorAll('.seg-switch input[value="no_access"]:checked').length;
    
    var bFull = document.getElementById('badge-' + modKey + '-full');
    var bView = document.getElementById('badge-' + modKey + '-view');
    var bNone = document.getElementById('badge-' + modKey + '-none');
    
    if (bFull) bFull.textContent = mFull + ' Full';
    if (bView) bView.textContent = mView + ' View';
    if (bNone) bNone.textContent = mNone + ' None';
  });
}

document.addEventListener('DOMContentLoaded', function() {
  updateCounter();
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
