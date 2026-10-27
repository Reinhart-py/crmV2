<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<style>
.roles-page-container {
    padding: 0 10px 40px;
}
.apple-card {
    background: #ffffff;
    border: 1px solid #e5e5ea;
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    overflow: hidden;
    padding: 24px;
}
.apple-form-label {
    font-size: 13px;
    font-weight: 600;
    color: #1d1d1f;
    margin-bottom: 6px;
    display: block;
}
.apple-input, .apple-select {
    width: 100%;
    height: 40px;
    background: #ffffff;
    border: 1px solid #d2d2d7;
    border-radius: 10px;
    padding: 8px 14px;
    font-size: 14px;
    color: #1d1d1f;
    transition: all 0.15s ease;
    outline: none;
    box-sizing: border-box;
}
.apple-input:focus, .apple-select:focus {
    border-color: #000000;
    box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.08);
}
.apple-btn-black {
    background: #000000;
    color: #ffffff !important;
    border: 1px solid #000000;
    border-radius: 980px;
    padding: 9px 24px;
    font-size: 14px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none !important;
    cursor: pointer;
    transition: all 0.15s ease;
}
.apple-btn-black:hover {
    background: #2c2c2e;
    border-color: #2c2c2e;
}
.apple-btn-ghost {
    background: transparent;
    color: #6e6e73 !important;
    border: 1px solid #d2d2d7;
    border-radius: 980px;
    padding: 9px 22px;
    font-size: 14px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    text-decoration: none !important;
    margin-left: 10px;
    transition: all 0.15s ease;
}
.apple-btn-ghost:hover {
    color: #1d1d1f !important;
    border-color: #86868b;
    background: #f5f5f7;
}
.perm-matrix-card {
    background: #ffffff;
    border: 1px solid #e5e5ea;
    border-radius: 12px;
    margin-top: 24px;
    margin-bottom: 24px;
    overflow: hidden;
}
.perm-matrix-header {
    background: #fafafa;
    border-bottom: 1px solid #e5e5ea;
    padding: 14px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.perm-matrix-title {
    font-size: 14px;
    font-weight: 600;
    color: #1d1d1f;
    margin: 0;
}
.apple-pill-btn {
    background: #ffffff;
    border: 1px solid #d2d2d7;
    border-radius: 980px;
    padding: 4px 12px;
    font-size: 11px;
    font-weight: 500;
    color: #1d1d1f;
    cursor: pointer;
    transition: all 0.15s ease;
}
.apple-pill-btn:hover {
    background: #000000;
    border-color: #000000;
    color: #ffffff;
}
.perm-table {
    width: 100%;
    border-collapse: collapse;
}
.perm-table th {
    padding: 10px 18px;
    font-size: 11px;
    font-weight: 600;
    color: #86868b;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    background: #fafafa;
    border-bottom: 1px solid #e5e5ea;
}
.perm-table td {
    padding: 12px 18px;
    border-bottom: 1px solid #f2f2f7;
    vertical-align: middle;
}
.perm-table tr:last-child td {
    border-bottom: none;
}
.perm-seg-control {
    display: inline-flex;
    background: #f2f2f7;
    border-radius: 980px;
    padding: 2px;
    border: 1px solid #e5e5ea;
}
.perm-seg-label {
    margin: 0;
    padding: 4px 12px;
    font-size: 11px;
    font-weight: 500;
    color: #6e6e73;
    border-radius: 980px;
    cursor: pointer;
    user-select: none;
    transition: all 0.15s ease;
    display: flex;
    align-items: center;
    gap: 4px;
}
.perm-seg-label input[type="radio"] {
    display: none;
}
.perm-seg-label.active {
    background: #000000;
    color: #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
}
</style>

<div class="content-wrapper px-4"> 
  <section class="content-header" style="padding-bottom: 14px;">
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <div>
        <h1 style="font-size: 24px; font-weight: 600; color: #1d1d1f; margin:0; letter-spacing: -0.02em;"><?php echo htmlspecialchars($info['page_heading']); ?></h1>
      </div>
      <div>
        <a href="<?php echo base_url(); ?>manager/roles" class="apple-btn-ghost">
          <i class="fa fa-arrow-left" style="margin-right:6px;"></i> Back to Roles
        </a>
      </div>
    </div>
  </section>

  <section class="content roles-page-container">
    <div class="row"> 
      <div class="col-xs-12">
        <div class="form-group success_alert"> 
          <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
          <?php echo get_message($this->session->flashdata('message'),'message'); ?> 
        </div>

        <div class="apple-card">
          <form method="post" action="<?php echo base_url(); ?>manager/roles/edit/<?php echo $role['id']; ?>">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="row">
              <div class="col-md-4 col-sm-6">
                <div class="form-group">
                  <label class="apple-form-label">Parent Hierarchy Role</label>
                  <select class="apple-select" name="parent_id">
                    <option value="0">Root Level</option>
                    <?php if(isset($r_list) && !empty($r_list)){
                      foreach($r_list as $cli){ ?>
                      <option <?php if($role['parent_id']==$cli['id']){ echo "selected"; } ?> value="<?php echo $cli['id']; ?>"><?php echo htmlspecialchars($cli['title']); ?></option>
                    <?php } } ?>
                  </select>
                </div> 
              </div>

              <div class="col-md-4 col-sm-6">
                <div class="form-group">
                  <label class="apple-form-label">Role Name *</label>
                  <input type="text" name="title" class="apple-input" placeholder="e.g. Sales Executive" value="<?php echo htmlspecialchars($role['title']); ?>" required="required" />
                </div>                  
              </div>

              <div class="col-md-4 col-sm-6">
                <div class="form-group">
                  <label class="apple-form-label">Role Status</label>
                  <select class="apple-select" name="status">
                    <option value="1" <?php if($role['status']==1){ echo "selected"; } ?>>Active</option>
                    <option value="0" <?php if($role['status']==0){ echo "selected"; } ?>>Disabled</option>
                  </select>
                </div>
              </div>

              <?php
                $sections = array(
                  'dashboard'      => 'Dashboard & Key Metrics',
                  'my_zone'        => 'My Zone Portal',
                  'database'       => 'Database Records',
                  'leads'          => 'Leads & Pipeline',
                  'lead_review'    => 'Lead Approvals & Conversion',
                  'orders'         => 'Orders & Back Office Processing',
                  'calls'          => 'Calls & Softphone Dialer',
                  'team'           => 'Team Management & Chart',
                  'kam'            => 'KAM Schedules & Accounts',
                  'tasks'          => 'Tasks Management',
                  'tickets'        => 'Tickets & Support',
                  'internal_tasks' => 'Internal Tasks',
                  'external_tasks' => 'External Tasks',
                  'reports'        => 'Reports & Projections',
                  'events'         => 'Events Calendar',
                  'awards'         => 'Awards & Achievements',
                  'integrations'   => 'Third-Party API & Integrations',
                  'system'         => 'System Settings & Health',
                  'users'          => 'User Accounts Management',
                  'roles'          => 'Role Access Permissions'
                );
                $current_perms = !empty($role['permissions']) ? json_decode($role['permissions'], true) : array();
              ?>

              <div class="col-sm-12">
                <div class="perm-matrix-card">
                  <div class="perm-matrix-header">
                    <div>
                      <h3 class="perm-matrix-title">Section Access Matrix</h3>
                      <div style="font-size:12px; color:#86868b; margin-top:2px;">Select access level for each functional area</div>
                    </div>
                    <div style="display:flex; gap:6px;">
                      <button type="button" class="apple-pill-btn" onclick="bulkSetPerms('no_access')">No Access All</button>
                      <button type="button" class="apple-pill-btn" onclick="bulkSetPerms('read')">Read All</button>
                      <button type="button" class="apple-pill-btn" onclick="bulkSetPerms('write')">Write All</button>
                    </div>
                  </div>

                  <div class="table-responsive">
                    <table class="perm-table">
                      <thead>
                        <tr>
                          <th style="width: 50%;">CRM Section</th>
                          <th style="width: 50%; text-align:right;">Access Permission</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($sections as $s_key => $s_title) { 
                          $cur = isset($current_perms[$s_key]) ? $current_perms[$s_key] : ((int)$role['id'] === 1 || (int)$role['id'] === 2 ? 'write' : 'read');
                        ?>
                        <tr>
                          <td>
                            <strong style="font-size:13px; color:#1d1d1f; font-weight:600;"><?php echo htmlspecialchars($s_title); ?></strong>
                          </td>
                          <td style="text-align:right;">
                            <div class="perm-seg-control">
                              <label class="perm-seg-label <?php if ($cur === 'no_access') echo 'active'; ?>">
                                <input type="radio" name="permissions[<?php echo $s_key; ?>]" value="no_access" <?php if ($cur === 'no_access') echo 'checked'; ?> onchange="updateSegmentUI(this)">
                                No Access
                              </label>
                              <label class="perm-seg-label <?php if ($cur === 'read') echo 'active'; ?>">
                                <input type="radio" name="permissions[<?php echo $s_key; ?>]" value="read" <?php if ($cur === 'read') echo 'checked'; ?> onchange="updateSegmentUI(this)">
                                Read
                              </label>
                              <label class="perm-seg-label <?php if ($cur === 'write') echo 'active'; ?>">
                                <input type="radio" name="permissions[<?php echo $s_key; ?>]" value="write" <?php if ($cur === 'write') echo 'checked'; ?> onchange="updateSegmentUI(this)">
                                Write
                              </label>
                            </div>
                          </td>
                        </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="col-sm-12" style="padding-top:10px;">
                <button type="submit" name="submit" class="apple-btn-black">
                  Save Role &amp; Permissions
                </button>
                <a href="<?php echo base_url(); ?>manager/roles" class="apple-btn-ghost">Cancel</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
function updateSegmentUI(input) {
  var parentControl = input.closest('.perm-seg-control');
  if (parentControl) {
    var labels = parentControl.querySelectorAll('.perm-seg-label');
    labels.forEach(function(l) { l.classList.remove('active'); });
    input.closest('.perm-seg-label').classList.add('active');
  }
}

function bulkSetPerms(level) {
  var inputs = document.querySelectorAll('input[type="radio"][value="' + level + '"]');
  inputs.forEach(function(inp) {
    inp.checked = true;
    updateSegmentUI(inp);
  });
}
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
