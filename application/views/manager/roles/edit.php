<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<style>
.role-edit-container {
    padding: 0 16px 80px 16px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: #000000;
}
.role-meta-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    padding: 24px;
    margin-bottom: 24px;
}
.role-field-label {
    font-size: 13px;
    font-weight: 600;
    color: #000000;
    margin-bottom: 8px;
    display: block;
}
.role-input, .role-select {
    width: 100%;
    height: 40px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 8px 14px;
    font-size: 14px;
    color: #000000;
    transition: border-color 0.15s ease;
    outline: none;
    box-sizing: border-box;
}
.role-input:focus, .role-select:focus {
    border-color: #000000;
}
.matrix-global-bar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 18px 24px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.matrix-title {
    font-size: 16px;
    font-weight: 700;
    color: #000000;
    margin: 0;
}
.btn-quick-bulk {
    background: #ffffff;
    color: #000000;
    border: 1px solid #cbd5e1;
    padding: 7px 14px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
}
.btn-quick-bulk:hover {
    background: #000000;
    border-color: #000000;
    color: #ffffff;
}
.btn-quick-bulk-dark {
    background: #000000;
    color: #ffffff;
    border-color: #000000;
}
.btn-quick-bulk-dark:hover {
    background: #262626;
    border-color: #262626;
}
.module-group-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    margin-bottom: 20px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.module-group-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 14px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}
.module-group-title {
    font-size: 14.5px;
    font-weight: 700;
    color: #000000;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.module-group-title i {
    color: #000000;
    font-size: 15px;
}
.module-quick-pill {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 4px 10px;
    font-size: 11.5px;
    font-weight: 600;
    color: #000000;
    cursor: pointer;
    transition: all 0.15s ease;
}
.module-quick-pill:hover {
    background: #000000;
    border-color: #000000;
    color: #ffffff;
}
.perm-row {
    padding: 12px 20px;
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
    font-size: 13.5px;
    font-weight: 600;
    color: #000000;
    margin: 0;
}
.seg-switch {
    display: inline-flex;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 3px;
    gap: 2px;
    flex-shrink: 0;
}
.seg-option {
    margin: 0;
    padding: 6px 14px;
    font-size: 12px;
    font-weight: 500;
    color: #64748b;
    border-radius: 4px;
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
.seg-option.active {
    background: #000000;
    color: #ffffff;
    font-weight: 600;
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
    background: #000000;
    color: #ffffff !important;
    border: 1px solid #000000;
    border-radius: 6px;
    padding: 10px 24px;
    font-size: 13.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none !important;
}
.btn-save-role:hover {
    background: #262626;
    border-color: #262626;
}
.btn-cancel-role {
    background: #ffffff;
    color: #000000 !important;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 10px 20px;
    font-size: 13.5px;
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
    border-color: #000000;
}
</style>

<div class="content-wrapper role-edit-container"> 
  <section class="content-header" style="padding: 20px 0 16px 0;">
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <div>
        <h1 style="font-size: 22px; font-weight: 700; color: #000000; margin: 0; letter-spacing: -0.02em;">
          Edit Role: <?php echo htmlspecialchars($role['title']); ?>
        </h1>
      </div>
      <div>
        <a href="<?php echo base_url(); ?>manager/roles" class="btn-cancel-role" style="padding: 7px 16px; font-size: 13px;">
          <i class="fa fa-arrow-left"></i> Back to Roles
        </a>
      </div>
    </div>
  </section>

  <section class="content" style="padding: 0;">
    <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
    <?php echo get_message($this->session->flashdata('message'),'message'); ?> 

    <form method="post" action="<?php echo base_url(); ?>manager/roles/edit/<?php echo $role['id']; ?>" id="roleForm">
      <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

      <div class="role-meta-card">
        <div class="row">
          <div class="col-md-4 col-sm-6">
            <div class="form-group" style="margin-bottom: 0;">
              <label class="role-field-label">Role Title *</label>
              <input type="text" name="title" class="role-input" placeholder="Role Title" value="<?php echo htmlspecialchars($role['title']); ?>" required />
            </div>                  
          </div>

          <div class="col-md-4 col-sm-6">
            <div class="form-group" style="margin-bottom: 0;">
              <label class="role-field-label">Parent Hierarchy Role</label>
              <select class="role-select" name="parent_id">
                <option value="0">Root Level (Top Level)</option>
                <?php if(isset($r_list) && !empty($r_list)){
                  foreach($r_list as $cli){ ?>
                  <option <?php if($role['parent_id']==$cli['id']){ echo "selected"; } ?> value="<?php echo $cli['id']; ?>"><?php echo htmlspecialchars($cli['title']); ?></option>
                <?php } } ?>
              </select>
            </div> 
          </div>

          <div class="col-md-4 col-sm-12">
            <div class="form-group" style="margin-bottom: 0;">
              <label class="role-field-label">Role Status</label>
              <select class="role-select" name="status">
                <option value="1" <?php if($role['status']==1){ echo "selected"; } ?>>Active</option>
                <option value="0" <?php if($role['status']==0){ echo "selected"; } ?>>Disabled</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <?php
        $current_perms = !empty($role['permissions']) ? json_decode($role['permissions'], true) : array();
        if (!is_array($current_perms)) {
          $current_perms = array();
        }

                $all_modules = array(
          'dashboard' => array(
            'title' => 'Dashboard',
            'icon'  => 'fa fa-dashboard',
            'pages' => array(
              'dashboard'           => 'Dashboard'
            )
          ),
          'calls' => array(
            'title' => 'Phone Dialer & Calling',
            'icon'  => 'fa fa-phone',
            'pages' => array(
              'calls'               => 'Phone Dialer & Softphone',
              'calls_autodialer'    => 'Auto Dialer',
              'calls_manualdialer'  => 'Manual Dialer',
              'calls_logs'          => 'Call Logs & Statistics'
            )
          ),
          'live' => array(
            'title' => 'Live Operations',
            'icon'  => 'fa fa-line-chart',
            'pages' => array(
              'live'                => 'Live Operations Dashboard',
              'live_presentation'   => 'Live Presentation TV Mode'
            )
          ),
          'my_zone' => array(
            'title' => 'My Zone',
            'icon'  => 'fa fa-th-large',
            'pages' => array(
              'database'            => 'Add & View Database',
              'leads'               => 'View Leads',
              'orders'              => 'View Orders',
              'sales'               => 'View Sales',
              'followups'           => 'My Follow-Up',
              'meetings'            => 'My Meetings',
              'verticals'           => 'Add & View Verticals',
              'vertical_followups'  => 'Vertical Follow-up',
              'internal_tasks'      => 'Internal Tasks',
              'external_tasks'      => 'External Tasks',
              'database_transfer'   => 'Database Transfer'
            )
          ),
          'team' => array(
            'title' => 'Team',
            'icon'  => 'fa fa-users',
            'pages' => array(
              'team_members'        => 'Members',
              'team_chart'          => 'Chart',
              'team_database'       => 'Database',
              'team_leads'          => 'Leads',
              'team_orders'         => 'Orders',
              'team_verticals'      => 'Verticals',
              'team_followups'      => 'Follow up',
              'team_meetings'       => 'Meetings',
              'team_assignmeetings' => 'Assigned Meetings',
              'team_assignleads'    => 'Assign Database',
              'team_tasks'          => 'Task',
              'team_tickets'        => 'Tickets'
            )
          ),
          'back_office' => array(
            'title' => 'Back Office',
            'icon'  => 'fa fa-briefcase',
            'pages' => array(
              'lead_review'         => 'Pending Leads Approval',
              'orders_pending'      => 'Pending Order Process',
              'orders_pending_sales'=> 'Pending Sales Approval',
              'kam_onboard'         => 'Onboard Client',
              'orders_open'         => 'Open Orders',
              'orders_followups'    => 'Orders Follow-up',
              'orders_completed'    => 'Completed Orders',
              'orders_closed'       => 'Closed Orders',
              'orders_rejected'     => 'Rejected / Lost Orders',
              'orders_management'   => 'All Orders'
            )
          ),
          'kam' => array(
            'title' => 'KAM (Key Accounts)',
            'icon'  => 'fa fa-handshake-o',
            'pages' => array(
              'kam_clients'         => 'My Client',
              'kam_schedule'        => 'Create Schedule',
              'kam_task_followup'   => 'Task Follow-up',
              'kam_reminders'       => 'Reminders',
              'kam_report'          => 'KAM Report',
              'kam_assignee_mgmt'   => 'Client Assignee Management'
            )
          ),
          'hr' => array(
            'title' => 'HR Operations',
            'icon'  => 'fa fa-id-card-o',
            'pages' => array(
              'hr'                  => 'HR Overview',
              'hr_targets'          => 'Assign Targets',
              'hr_attendance'       => 'Attendance & Geolocation',
              'hr_shifts'           => 'Shift Management',
              'hr_leaves'           => 'Leave Management',
              'hr_payroll'          => 'Payroll & Compensation',
              'hr_taxation'         => 'Taxation & Gratuity',
              'hr_appraisals'       => 'Performance & Appraisals',
              'hr_lifecycle'        => 'Employee Lifecycle',
              'hr_documents'        => 'Document Vault'
            )
          ),
          'reports' => array(
            'title' => 'Reports & Analytics',
            'icon'  => 'fa fa-bar-chart',
            'pages' => array(
              'reports_revenue'     => 'Revenue List',
              'reports_projections' => 'Company Projections',
              'reports_download'    => 'Download Reports Center',
              'reports_admin'       => 'Admin Reports',
              'reports_leads'       => 'Leads Report',
              'reports_perf_dep'    => 'Department Performance',
              'reports_perf_ind'    => 'Individual Performance'
            )
          ),
          'events' => array(
            'title' => 'Events & Awards',
            'icon'  => 'fa fa-trophy',
            'pages' => array(
              'events_list'         => 'Events & Announcements',
              'awards_list'         => 'Awards Recognition'
            )
          ),
          'system' => array(
            'title' => 'System Management',
            'icon'  => 'fa fa-cog',
            'pages' => array(
              'users'               => 'Users View & Add',
              'roles'               => 'Roles & Permissions',
              'terms'               => 'Category & Terms',
              'leads_import'        => 'Import Leads',
              'leads_export'        => 'Export Leads',
              'assigned_leads'      => 'Allotted & Non-Allotted Database',
              'admin_logs'          => 'Admin Logs Audit',
              'system_assignee'     => 'Assignee Management',
              'calling_api'         => 'Calling API Gateway',
              'integrations'        => 'Third-Party API Integrations',
              'live_location'       => 'Live Location Tracking',
              'qc_panel'            => 'Quality Check Panel'
            )
          )
        );
      ?>

      <div class="matrix-global-bar">
        <div>
          <h2 class="matrix-title">Permissions</h2>
        </div>

        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
          <button type="button" class="btn-quick-bulk" onclick="setAllPermissions('no_access')">
            Set All: No Access
          </button>
          <button type="button" class="btn-quick-bulk" onclick="setAllPermissions('read')">
            Set All: View Only
          </button>
          <button type="button" class="btn-quick-bulk btn-quick-bulk-dark" onclick="setAllPermissions('write')">
            Set All: Full Access
          </button>
        </div>
      </div>

      <?php foreach ($all_modules as $mod_key => $mod_data) { ?>
        <div class="module-group-card" data-module="<?php echo $mod_key; ?>">
          <div class="module-group-header">
            <div>
              <h3 class="module-group-title">
                <i class="<?php echo $mod_data['icon']; ?>"></i>
                <?php echo htmlspecialchars($mod_data['title']); ?>
              </h3>
            </div>
            <div style="display: flex; gap: 6px;">
              <button type="button" class="module-quick-pill" onclick="setModulePermissions('<?php echo $mod_key; ?>', 'no_access')">
                None
              </button>
              <button type="button" class="module-quick-pill" onclick="setModulePermissions('<?php echo $mod_key; ?>', 'read')">
                View
              </button>
              <button type="button" class="module-quick-pill" onclick="setModulePermissions('<?php echo $mod_key; ?>', 'write')">
                Full
              </button>
            </div>
          </div>

          <div class="module-group-body">
            <?php foreach ($mod_data['pages'] as $page_key => $page_title) { 
              $cur_perm = isset($current_perms[$page_key]) ? $current_perms[$page_key] : null;
              if ($cur_perm === null) {
                if ((int)$role['id'] === 1) {
                  $cur_perm = 'write';
                } elseif ((int)$role['id'] === 2 && $mod_key !== 'system') {
                  $cur_perm = 'write';
                } elseif (isset($current_perms[$mod_key])) {
                  $cur_perm = $current_perms[$mod_key];
                } else {
                  $cur_perm = ($mod_key === 'system') ? 'no_access' : 'read';
                }
              }
            ?>
              <div class="perm-row">
                <div style="flex: 1; min-width: 240px;">
                  <h4 class="perm-page-name">
                    <?php echo htmlspecialchars($page_title); ?>
                  </h4>
                </div>

                <div class="seg-switch" data-key="<?php echo $page_key; ?>">
                  <label class="seg-option <?php if ($cur_perm === 'no_access') echo 'active'; ?>" onclick="applySeg(this, 'no_access')">
                    <input type="radio" name="permissions[<?php echo $page_key; ?>]" value="no_access" <?php if ($cur_perm === 'no_access') echo 'checked'; ?>>
                    No Access
                  </label>
                  <label class="seg-option <?php if ($cur_perm === 'read') echo 'active'; ?>" onclick="applySeg(this, 'read')">
                    <input type="radio" name="permissions[<?php echo $page_key; ?>]" value="read" <?php if ($cur_perm === 'read') echo 'checked'; ?>>
                    View Only
                  </label>
                  <label class="seg-option <?php if ($cur_perm === 'write') echo 'active'; ?>" onclick="applySeg(this, 'write')">
                    <input type="radio" name="permissions[<?php echo $page_key; ?>]" value="write" <?php if ($cur_perm === 'write') echo 'checked'; ?>>
                    Full Access
                  </label>
                </div>
              </div>
            <?php } ?>
          </div>
        </div>
      <?php } ?>

      <div class="sticky-save-bar">
        <div>
          <span style="font-size: 13px; font-weight: 600; color: #000000;" id="permCounter">Reviewing permissions</span>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
          <a href="<?php echo base_url(); ?>manager/roles" class="btn-cancel-role">
            Cancel
          </a>
          <button type="submit" class="btn-save-role">
            Save Role
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

function updateCounter() {
  var full = document.querySelectorAll('.seg-switch input[value="write"]:checked').length;
  var view = document.querySelectorAll('.seg-switch input[value="read"]:checked').length;
  var none = document.querySelectorAll('.seg-switch input[value="no_access"]:checked').length;
  
  var el = document.getElementById('permCounter');
  if (el) {
    el.innerHTML = '<strong>' + full + '</strong> Full Access &bull; <strong>' + view + '</strong> View Only &bull; <strong>' + none + '</strong> No Access';
  }
}

document.addEventListener('DOMContentLoaded', function() {
  updateCounter();
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
