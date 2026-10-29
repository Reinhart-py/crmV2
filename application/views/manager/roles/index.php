<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<style>
.roles-page-container {
    padding: 0 16px 40px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: #000000;
}
.perm-tab-switcher {
    display: inline-flex;
    background: #f1f5f9;
    padding: 3px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    margin-bottom: 20px;
}
.perm-tab-btn {
    border: none;
    background: transparent;
    padding: 8px 20px;
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.perm-tab-btn.active {
    background: #ffffff;
    color: #000000;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}
.role-list-card, .user-perm-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    overflow: hidden;
}
.role-list-header, .user-perm-header {
    padding: 16px 22px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #ffffff;
    flex-wrap: wrap;
    gap: 12px;
}
.role-list-title, .user-perm-title {
    font-size: 16px;
    font-weight: 700;
    color: #000000;
    margin: 0;
}
.btn-add-role {
    background: #000000;
    color: #ffffff !important;
    border: 1px solid #000000;
    border-radius: 6px;
    padding: 8px 18px;
    font-size: 13px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none !important;
    transition: all 0.15s ease;
}
.btn-add-role:hover {
    background: #262626;
    border-color: #262626;
}
.role-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}
.role-table th {
    background: #f8fafc;
    color: #000000;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    padding: 12px 20px;
    border-bottom: 1px solid #e2e8f0;
}
.role-table td {
    padding: 14px 20px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 13.5px;
    color: #000000;
    vertical-align: middle;
}
.role-table tr:last-child td {
    border-bottom: none;
}
.role-table tr:hover td {
    background: #f8fafc;
}
.role-status-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 600;
}
.status-active {
    background: #000000;
    color: #ffffff;
}
.status-disabled {
    background: #ffffff;
    color: #64748b;
    border: 1px solid #cbd5e1;
}
.role-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #000000;
    transition: all 0.15s ease;
    margin-left: 6px;
    text-decoration: none !important;
}
.role-action-btn:hover {
    background: #000000;
    border-color: #000000;
    color: #ffffff;
}
.user-toolbar {
    padding: 16px 22px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
}
.user-select-control {
    min-width: 280px;
    max-width: 380px;
    height: 38px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 6px 12px;
    font-size: 13.5px;
    background: #ffffff;
    color: #000000;
    font-weight: 500;
    outline: none;
}
.user-select-control:focus {
    border-color: #000000;
}
.mode-toggle-group {
    display: inline-flex;
    background: #e2e8f0;
    padding: 3px;
    border-radius: 6px;
}
.mode-toggle-btn {
    border: none;
    background: transparent;
    padding: 6px 14px;
    font-size: 12.5px;
    font-weight: 600;
    color: #475569;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.15s ease;
}
.mode-toggle-btn.active {
    background: #000000;
    color: #ffffff;
}
.mode-toggle-btn.active-admin {
    background: #16a34a;
    color: #ffffff;
}
.mode-toggle-btn.active-custom {
    background: #0072f0;
    color: #ffffff;
}
.user-meta-bar {
    padding: 12px 22px;
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 13px;
    flex-wrap: wrap;
    gap: 10px;
}
.user-meta-tags {
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
}
.user-meta-item {
    color: #64748b;
}
.user-meta-item strong {
    color: #000000;
    font-weight: 600;
}
.user-custom-actions {
    padding: 12px 22px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}
.btn-action-sm {
    background: #ffffff;
    color: #000000;
    border: 1px solid #cbd5e1;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.15s ease;
}
.btn-action-sm:hover {
    background: #000000;
    border-color: #000000;
    color: #ffffff;
}
.btn-action-primary {
    background: #0072f0;
    color: #ffffff;
    border: 1px solid #0072f0;
    padding: 7px 18px;
    font-size: 13px;
    font-weight: 600;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-action-primary:hover {
    background: #005ec4;
    border-color: #005ec4;
}
.mod-grid {
    padding: 20px 22px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
    gap: 16px;
}
.mod-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 7px;
    overflow: hidden;
}
.mod-card-head {
    background: #f8fafc;
    padding: 10px 14px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.mod-card-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #000000;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.mod-card-title i {
    color: #64748b;
    font-size: 13px;
}
.mod-item-row {
    padding: 10px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #f1f5f9;
}
.mod-item-row:last-child {
    border-bottom: none;
}
.mod-item-name {
    font-size: 13px;
    font-weight: 500;
    color: #000000;
}
.perm-seg-ctrl {
    display: inline-flex;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 5px;
    overflow: hidden;
}
.perm-seg-btn {
    border: none;
    background: transparent;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    transition: all 0.12s ease;
}
.perm-seg-btn.val-no_access.active {
    background: #64748b;
    color: #ffffff;
}
.perm-seg-btn.val-read.active {
    background: #0072f0;
    color: #ffffff;
}
.perm-seg-btn.val-write.active {
    background: #16a34a;
    color: #ffffff;
}
.user-sticky-save {
    position: sticky;
    bottom: 16px;
    margin: 16px 22px 0;
    padding: 12px 20px;
    background: #000000;
    color: #ffffff;
    border-radius: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 14px rgba(0,0,0,0.25);
    z-index: 100;
}
</style>

<div class="content-wrapper px-4"> 
  <section class="content-header" style="padding: 20px 0 16px 0;">
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <div>
        <h1 style="font-size: 22px; font-weight: 700; color: #000000; margin:0; letter-spacing: -0.02em;">
          Roles &amp; Permissions
        </h1>
      </div>
      <div>
        <a href="<?php echo base_url(); ?>manager/roles/add" class="btn-add-role" id="topBtnAddRole">
          <i class="fa fa-plus"></i> Add Role
        </a>
      </div>
    </div>
  </section>

  <section class="content roles-page-container" style="padding-left:0; padding-right:0;"> 
    <div class="perm-tab-switcher">
      <button type="button" class="perm-tab-btn active" id="tabRoleWise" onclick="switchPermissionsView('role-wise')">
        <i class="fa fa-shield"></i> Role-Wise Permissions
      </button>
      <button type="button" class="perm-tab-btn" id="tabUserWise" onclick="switchPermissionsView('user-wise')">
        <i class="fa fa-user"></i> User-Wise Permissions
      </button>
    </div>

    <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
    <?php echo get_message($this->session->flashdata('message'),'message'); ?>

    <div id="sectionRoleWise">
      <div class="role-list-card">
        <div class="role-list-header">
          <div>
            <h2 class="role-list-title">All Roles</h2>
          </div>
          <div style="font-size: 13px; color: #64748b;">
            Total Roles: <strong style="color:#000000; font-weight:700;"><?php echo !empty($user_list) ? count($user_list) : 0; ?></strong>
          </div>
        </div>

        <?php if((isset($user_list)) && (!empty($user_list))) { ?>
        <div class="table-responsive" style="margin-bottom:0;">
          <table class="role-table">
            <thead>
              <tr>
                <th style="width: 50px;">#</th>                  
                <th>Role Name</th> 
                <th style="width: 140px;">Hierarchy Level</th>                 
                <th style="width: 130px;">Status</th>                 
                <th style="width: 140px;">Created Date</th>
                <th style="width: 110px; text-align:right;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($user_list as $k=>$li) { ?>
              <tr>
                <td style="color:#64748b; font-weight:500;"><?php echo $k+1; ?></td>
                <td>
                  <strong style="font-weight:600; font-size:14px; color:#000000;"><?php echo htmlspecialchars($li['title']); ?></strong>
                  <?php if ((int)$li['id'] === 1) { ?>
                    <span style="font-size:11px; background:#000000; color:#ffffff; padding:2px 8px; border-radius:4px; margin-left:8px; font-weight:600;">
                      Administrator
                    </span>
                  <?php } elseif ((int)$li['id'] === 2) { ?>
                    <span style="font-size:11px; background:#f1f5f9; color:#000000; border:1px solid #cbd5e1; padding:2px 8px; border-radius:4px; margin-left:8px; font-weight:600;">
                      Back Office
                    </span>
                  <?php } ?>
                </td>      
                <td>
                  <span style="background:#f1f5f9; color:#000000; border:1px solid #e2e8f0; padding:3px 8px; border-radius:4px; font-size:12px; font-weight:600;">
                    Level <?php echo (int)$li['level']; ?>
                  </span>
                </td> 
                <td>
                  <?php if($li['status']==0) { ?>
                    <span class="role-status-badge status-disabled">Disabled</span>
                  <?php } else { ?>
                    <span class="role-status-badge status-active">Active</span>
                  <?php } ?>
                </td>
                <td style="color:#64748b;"><?php echo date('d-M-Y',strtotime($li['created'])); ?></td>
                <td style="text-align:right;">
                  <a href="<?php echo base_url();?>manager/roles/edit/<?php echo $li['id']; ?>" class="role-action-btn" title="Edit Role &amp; Permissions">
                    <i class="fa fa-pencil"></i>
                  </a>
                  <?php if($li['deletable']==1){ ?> 
                    <a href="<?php echo base_url();?>manager/roles/delete/<?php echo $li['id']; ?>" onclick="return do_confirm();" class="role-action-btn btn-delete" title="Remove Role">
                      <i class="fa fa-trash-o"></i>
                    </a>
                  <?php } ?>
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <?php } else { ?>
        <div class="alert alert-info" style="margin:20px;">No Data Found</div>
        <?php } ?> 
      </div>
    </div>

    <div id="sectionUserWise" style="display:none;">
      <div class="user-perm-card">
        <div class="user-toolbar">
          <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            <label style="font-size:13px; font-weight:700; margin:0; color:#000000;">Select User:</label>
            <select id="userSelector" class="user-select-control" onchange="loadSelectedUser(this.value)">
              <option value="">-- Choose User --</option>
              <?php if(!empty($active_users)) {
                foreach($active_users as $u) {
                  $mode_tag = (!empty($u['permission_mode']) && $u['permission_mode'] !== 'role') ? ' ['.strtoupper($u['permission_mode']).']' : '';
              ?>
                <option value="<?php echo (int)$u['user_id']; ?>">
                  <?php echo htmlspecialchars($u['name']); ?> (<?php echo htmlspecialchars($u['role_title'] ? $u['role_title'] : 'User'); ?><?php echo $mode_tag; ?>)
                </option>
              <?php } } ?>
            </select>
          </div>

          <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
            <label style="font-size:13px; font-weight:700; margin:0; color:#000000;">Permission Mode:</label>
            <div class="mode-toggle-group">
              <button type="button" class="mode-toggle-btn active" id="btnModeRole" onclick="setPermissionMode('role')">Role Default</button>
              <button type="button" class="mode-toggle-btn" id="btnModeAdmin" onclick="setPermissionMode('admin')">Full Admin Rights</button>
              <button type="button" class="mode-toggle-btn" id="btnModeCustom" onclick="setPermissionMode('custom')">Custom</button>
            </div>
          </div>
        </div>

        <div class="user-meta-bar" id="userMetaBar" style="display:none;">
          <div class="user-meta-tags">
            <span class="user-meta-item">User: <strong id="metaUserName">-</strong></span>
            <span class="user-meta-item">Role: <strong id="metaUserRole">-</strong></span>
            <span class="user-meta-item">Department: <strong id="metaUserDept">-</strong></span>
            <span class="user-meta-item">Status: <span id="metaModeBadge" style="padding:2px 8px; border-radius:4px; font-size:11.5px; font-weight:600;">-</span></span>
          </div>
          <div>
            <button type="button" class="btn-action-primary" onclick="saveUserPermissions()">
              <i class="fa fa-check"></i> Save Permissions
            </button>
          </div>
        </div>

        <div class="user-custom-actions" id="userCustomActions" style="display:none;">
          <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <label style="font-size:12.5px; font-weight:600; margin:0;">Copy Preset:</label>
            <select id="rolePresetSelect" class="user-select-control" style="min-width:200px; height:32px; font-size:12.5px;">
              <option value="">-- Choose Role Preset --</option>
              <?php if(!empty($user_list)) {
                foreach($user_list as $r) { ?>
                  <option value="<?php echo (int)$r['id']; ?>" data-perms='<?php echo htmlspecialchars($r['permissions'] ? $r['permissions'] : "{}"); ?>'>
                    <?php echo htmlspecialchars($r['title']); ?>
                  </option>
              <?php } } ?>
            </select>
            <button type="button" class="btn-action-sm" onclick="applyRolePreset()">Apply Preset</button>
          </div>

          <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
            <button type="button" class="btn-action-sm" onclick="bulkSetPermissions('no_access')">Set All: No Access</button>
            <button type="button" class="btn-action-sm" onclick="bulkSetPermissions('read')">Set All: View Only</button>
            <button type="button" class="btn-action-sm" onclick="bulkSetPermissions('write')">Set All: Full Access</button>
          </div>
        </div>

        <div id="userPermPlaceholder" style="padding:48px 20px; text-align:center; color:#64748b;">
          <i class="fa fa-user-circle-o" style="font-size:42px; margin-bottom:12px; color:#cbd5e1; display:block;"></i>
          <span style="font-size:14px; font-weight:600; color:#000000;">Select a user from the dropdown above to view or customize their permissions</span>
        </div>

        <?php
          $all_modules_matrix = array(
            'core' => array(
              'title' => 'Core Systems',
              'icon' => 'fa fa-cube',
              'items' => array(
                'dashboard' => 'Dashboard',
                'calls' => 'Dialer & Softphone'
              )
            ),
            'hr' => array(
              'title' => 'HR & People Management',
              'icon' => 'fa fa-user-circle-o',
              'items' => array(
                'hr' => 'HR Management',
                'hr_targets' => 'Assign Targets',
                'hr_attendance' => 'Attendance & Geolocation',
                'hr_shifts' => 'Shift Management',
                'hr_leaves' => 'Leave & Holidays',
                'hr_payroll' => 'Payroll & Compensation',
                'hr_appraisals' => 'Performance & Appraisals',
                'hr_lifecycle' => 'Employee Lifecycle'
              )
            ),
            'my_zone' => array(
              'title' => 'My Zone',
              'icon' => 'fa fa-th-large',
              'items' => array(
                'database' => 'Database',
                'leads' => 'Leads',
                'orders' => 'Orders',
                'sales' => 'Sales',
                'followups' => 'My Follow-Up',
                'meetings' => 'My Meetings',
                'verticals' => 'Verticals',
                'vertical_followups' => 'Vertical Follow-up',
                'internal_tasks' => 'Internal Tasks',
                'external_tasks' => 'External Tasks',
                'database_transfer' => 'Database Transfer'
              )
            ),
            'team' => array(
              'title' => 'Team Operations',
              'icon' => 'fa fa-users',
              'items' => array(
                'team_members' => 'Members',
                'team_chart' => 'Chart',
                'team_database' => 'Database',
                'team_leads' => 'Leads',
                'team_orders' => 'Orders',
                'team_verticals' => 'Verticals',
                'team_followups' => 'Follow up',
                'team_meetings' => 'Meetings',
                'team_assignmeetings' => 'Assigned Meetings',
                'team_assignleads' => 'Assign Database',
                'team_tasks' => 'Tasks',
                'team_tickets' => 'Tickets'
              )
            ),
            'back_office' => array(
              'title' => 'Back Office',
              'icon' => 'fa fa-briefcase',
              'items' => array(
                'lead_review' => 'Pending Leads Approval',
                'orders_pending' => 'Pending Order Process',
                'kam_onboard' => 'Onboard Client',
                'orders_management' => 'All Orders'
              )
            ),
            'kam' => array(
              'title' => 'KAM (Key Account Management)',
              'icon' => 'fa fa-handshake-o',
              'items' => array(
                'kam_clients' => 'My Client',
                'kam_schedule' => 'Create Schedule',
                'kam_task_followup' => 'Task Follow-up',
                'kam_reminders' => 'Reminders'
              )
            ),
            'reports' => array(
              'title' => 'Reports & Analytics',
              'icon' => 'fa fa-bar-chart',
              'items' => array(
                'reports_revenue' => 'Revenue List',
                'reports_projections' => 'Company Projections',
                'reports_download' => 'Reports'
              )
            ),
            'system' => array(
              'title' => 'Administration & System',
              'icon' => 'fa fa-cog',
              'items' => array(
                'users' => 'Users',
                'roles' => 'Roles',
                'terms' => 'Category',
                'leads_import' => 'Import Leads',
                'leads_export' => 'Export Leads',
                'assigned_leads' => 'Allotted Database',
                'admin_logs' => 'Admin Logs',
                'system_assignee' => 'Assignee Management',
                'kam_clients' => 'KAM Client',
                'calling_api' => 'Calling API',
                'integrations' => 'Third-Party API'
              )
            )
          );
        ?>

        <div id="userPermMatrix" class="mod-grid" style="display:none;">
          <?php foreach($all_modules_matrix as $group_key => $group_data) { ?>
            <div class="mod-card">
              <div class="mod-card-head">
                <span class="mod-card-title"><i class="<?php echo $group_data['icon']; ?>"></i> <?php echo $group_data['title']; ?></span>
                <div style="display:flex; gap:4px;">
                  <button type="button" class="btn-action-sm" style="padding:2px 6px; font-size:10.5px;" onclick="setGroupPerm('<?php echo $group_key; ?>', 'no_access')">None</button>
                  <button type="button" class="btn-action-sm" style="padding:2px 6px; font-size:10.5px;" onclick="setGroupPerm('<?php echo $group_key; ?>', 'read')">View</button>
                  <button type="button" class="btn-action-sm" style="padding:2px 6px; font-size:10.5px;" onclick="setGroupPerm('<?php echo $group_key; ?>', 'write')">Full</button>
                </div>
              </div>
              <div data-group="<?php echo $group_key; ?>">
                <?php foreach($group_data['items'] as $item_key => $item_label) { ?>
                  <div class="mod-item-row" data-perm-key="<?php echo $item_key; ?>">
                    <span class="mod-item-name"><?php echo $item_label; ?></span>
                    <div class="perm-seg-ctrl" data-key="<?php echo $item_key; ?>">
                      <button type="button" class="perm-seg-btn val-no_access active" onclick="setUserPermValue('<?php echo $item_key; ?>', 'no_access')">None</button>
                      <button type="button" class="perm-seg-btn val-read" onclick="setUserPermValue('<?php echo $item_key; ?>', 'read')">View</button>
                      <button type="button" class="perm-seg-btn val-write" onclick="setUserPermValue('<?php echo $item_key; ?>', 'write')">Full</button>
                    </div>
                  </div>
                <?php } ?>
              </div>
            </div>
          <?php } ?>
        </div>

        <div class="user-sticky-save" id="userStickySave" style="display:none;">
          <div>
            <span style="font-weight:700; font-size:13.5px;" id="stickyUserSummary">Saving permissions for User</span>
          </div>
          <div style="display:flex; gap:10px;">
            <button type="button" class="btn-action-primary" onclick="saveUserPermissions()">
              <i class="fa fa-check"></i> Save Changes
            </button>
          </div>
        </div>

      </div>
    </div>
  </section>
</div>

<script>
var currentUserId = 0;
var currentMode = 'role';
var userRolePerms = {};
var userCustomPerms = {};

function do_confirm(){    
  var job = confirm("Are you sure to delete this role?");    
  if(!job) {        
    return false;    
  }
}

function switchPermissionsView(view) {
  if (view === 'user-wise') {
    $('#tabRoleWise').removeClass('active');
    $('#tabUserWise').addClass('active');
    $('#sectionRoleWise').hide();
    $('#sectionUserWise').show();
    $('#topBtnAddRole').hide();
    window.location.hash = 'user-wise';
  } else {
    $('#tabUserWise').removeClass('active');
    $('#tabRoleWise').addClass('active');
    $('#sectionUserWise').hide();
    $('#sectionRoleWise').show();
    $('#topBtnAddRole').show();
    window.location.hash = 'role-wise';
  }
}

function loadSelectedUser(userId) {
  userId = parseInt(userId, 10);
  if (!userId || userId <= 0) {
    currentUserId = 0;
    $('#userMetaBar, #userCustomActions, #userPermMatrix, #userStickySave').hide();
    $('#userPermPlaceholder').show();
    return;
  }
  currentUserId = userId;

  $.ajax({
    url: '<?php echo base_url(); ?>manager/roles/ajax_get_user_permissions/' + userId,
    type: 'GET',
    dataType: 'json',
    success: function(res) {
      if (res && res.status === 'success') {
        $('#metaUserName').text(res.user.name);
        $('#metaUserRole').text(res.user.role_title);
        $('#metaUserDept').text(res.user.department);
        $('#stickyUserSummary').text('Permissions for ' + res.user.name + ' (' + res.user.role_title + ')');

        userRolePerms = res.role_permissions || {};
        userCustomPerms = res.custom_permissions || {};
        currentMode = res.mode || 'role';

        $('#userPermPlaceholder').hide();
        $('#userMetaBar, #userPermMatrix, #userStickySave').show();

        updateModeUI(currentMode);
        renderPermissionsMatrix();
      } else {
        alert(res.message || 'Failed to load user permissions');
      }
    },
    error: function() {
      alert('Network error loading user permissions');
    }
  });
}

function updateModeUI(mode) {
  currentMode = mode;
  $('#btnModeRole, #btnModeAdmin, #btnModeCustom').removeClass('active active-admin active-custom');

  if (mode === 'admin') {
    $('#btnModeAdmin').addClass('active-admin');
    $('#metaModeBadge').text('Full Admin Rights').css({ 'background': '#16a34a', 'color': '#ffffff' });
    $('#userCustomActions').hide();
  } else if (mode === 'custom') {
    $('#btnModeCustom').addClass('active-custom');
    $('#metaModeBadge').text('Custom Permissions').css({ 'background': '#0072f0', 'color': '#ffffff' });
    $('#userCustomActions').show();
  } else {
    $('#btnModeRole').addClass('active');
    $('#metaModeBadge').text('Role Default (' + $('#metaUserRole').text() + ')').css({ 'background': '#f1f5f9', 'color': '#000000', 'border': '1px solid #cbd5e1' });
    $('#userCustomActions').hide();
  }
}

function setPermissionMode(mode) {
  if (!currentUserId) {
    alert('Please select a user first.');
    return;
  }
  updateModeUI(mode);
  renderPermissionsMatrix();
}

function renderPermissionsMatrix() {
  $('.perm-seg-ctrl').each(function() {
    var key = $(this).data('key');
    var val = 'no_access';

    if (currentMode === 'admin') {
      val = 'write';
    } else if (currentMode === 'custom') {
      if (userCustomPerms.hasOwnProperty(key)) {
        val = userCustomPerms[key];
      } else if (userRolePerms.hasOwnProperty(key)) {
        val = userRolePerms[key];
      }
    } else {
      if (userRolePerms.hasOwnProperty(key)) {
        val = userRolePerms[key];
      }
    }

    $(this).find('.perm-seg-btn').removeClass('active');
    $(this).find('.perm-seg-btn.val-' + val).addClass('active');
  });
}

function setUserPermValue(key, val) {
  if (currentMode !== 'custom') {
    updateModeUI('custom');
  }
  userCustomPerms[key] = val;
  var $ctrl = $('.perm-seg-ctrl[data-key="' + key + '"]');
  $ctrl.find('.perm-seg-btn').removeClass('active');
  $ctrl.find('.perm-seg-btn.val-' + val).addClass('active');
}

function setGroupPerm(groupKey, val) {
  if (currentMode !== 'custom') {
    updateModeUI('custom');
  }
  $('div[data-group="' + groupKey + '"] .perm-seg-ctrl').each(function() {
    var key = $(this).data('key');
    userCustomPerms[key] = val;
    $(this).find('.perm-seg-btn').removeClass('active');
    $(this).find('.perm-seg-btn.val-' + val).addClass('active');
  });
}

function bulkSetPermissions(val) {
  if (currentMode !== 'custom') {
    updateModeUI('custom');
  }
  $('.perm-seg-ctrl').each(function() {
    var key = $(this).data('key');
    userCustomPerms[key] = val;
    $(this).find('.perm-seg-btn').removeClass('active');
    $(this).find('.perm-seg-btn.val-' + val).addClass('active');
  });
}

function applyRolePreset() {
  var $sel = $('#rolePresetSelect option:selected');
  var roleId = $sel.val();
  if (!roleId) {
    alert('Please choose a role preset first.');
    return;
  }
  var permsJson = $sel.attr('data-perms');
  var perms = {};
  try {
    perms = JSON.parse(permsJson || '{}');
  } catch(e) {
    perms = {};
  }

  updateModeUI('custom');
  $('.perm-seg-ctrl').each(function() {
    var key = $(this).data('key');
    var val = perms.hasOwnProperty(key) ? perms[key] : 'no_access';
    userCustomPerms[key] = val;
    $(this).find('.perm-seg-btn').removeClass('active');
    $(this).find('.perm-seg-btn.val-' + val).addClass('active');
  });
}

function saveUserPermissions() {
  if (!currentUserId) {
    alert('Please select a user first.');
    return;
  }

  var payload = {
    user_id: currentUserId,
    permission_mode: currentMode,
    permissions: {}
  };

  $('.perm-seg-ctrl').each(function() {
    var key = $(this).data('key');
    var activeVal = $(this).find('.perm-seg-btn.active').hasClass('val-write') ? 'write' : ($(this).find('.perm-seg-btn.active').hasClass('val-read') ? 'read' : 'no_access');
    payload.permissions[key] = activeVal;
  });

  $.ajax({
    url: '<?php echo base_url(); ?>manager/roles/ajax_save_user_permissions',
    type: 'POST',
    data: payload,
    dataType: 'json',
    success: function(res) {
      if (res && res.status === 'success') {
        alert(res.message || 'User permissions saved successfully!');
      } else {
        alert(res.message || 'Error saving user permissions.');
      }
    },
    error: function() {
      alert('Network error saving permissions.');
    }
  });
}

$(document).ready(function() {
  if (window.location.hash === '#user-wise') {
    switchPermissionsView('user-wise');
  }
});
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
