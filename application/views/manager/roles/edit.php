<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper"> 
  <section class="content-header">
    <h1><?php echo $info['page_heading']; ?></h1>
  </section>

  <section class="content">
    <div class="container">
      <div class="row"> 
        <div class="col-md-12">
          <div class="nav-tabs-custom">
            <div class="tab-content">
              <div class="active tab-pane" style="padding: 15px;"> 
                <div class="form-group success_alert"> 
                  <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
                  <?php echo get_message($this->session->flashdata('message'),'message'); ?> 
                </div>

                <form method="post" action="<?php echo base_url(); ?>manager/roles/edit/<?php echo $role['id']; ?>">
                  <div class="row">
                    <div class="col-lg-4 col-sm-6">
                      <div class="form-group">
                        <label>Parent Role<span class="text-danger">*</span></label>
                        <select class="form-control" name="parent_id">
                          <option value="0">Root</option>
                          <?php if(isset($r_list) && !empty($r_list)){
                            foreach($r_list as $cli){ ?>
                            <option <?php if($role['parent_id']==$cli['id']){ echo "selected"; } ?> value="<?php echo $cli['id']; ?>"><?php echo htmlspecialchars($cli['title']); ?></option>
                          <?php } } ?>
                        </select>
                      </div> 
                    </div>

                    <div class="col-lg-4 col-sm-6">
                      <div class="form-group">
                        <label>Role Name<span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="Role Name" value="<?php echo htmlspecialchars($role['title']); ?>" required="required" />
                      </div>                  
                    </div>

                    <div class="col-lg-4 col-sm-6">
                      <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status">
                          <option value="1" <?php if($role['status']==1){ echo "selected"; } ?>>Active</option>
                          <option value="0" <?php if($role['status']==0){ echo "selected"; } ?>>Inactive</option>
                        </select>
                      </div>
                    </div>

                    <?php
                      $sections = array(
                        'dashboard'      => 'Dashboard',
                        'my_zone'        => 'My Zone',
                        'database'       => 'Database Records',
                        'leads'          => 'Leads',
                        'lead_review'    => 'Lead Review',
                        'orders'         => 'Orders & Back Office',
                        'calls'          => 'Calls & Auto-Dialer',
                        'team'           => 'Team',
                        'kam'            => 'KAM Schedules',
                        'tasks'          => 'Tasks',
                        'tickets'        => 'Tickets',
                        'internal_tasks' => 'Internal Tasks',
                        'external_tasks' => 'External Tasks',
                        'reports'        => 'Reports & Projections',
                        'events'         => 'Events Calendar',
                        'awards'         => 'Awards & Achievements',
                        'integrations'   => 'Third-Party API',
                        'system'         => 'System',
                        'users'          => 'User Management',
                        'roles'          => 'Role Management'
                      );
                      $current_perms = !empty($role['permissions']) ? json_decode($role['permissions'], true) : array();
                    ?>

                    <div class="col-sm-12">
                      <div class="box box-solid" style="border:1px solid #e2e8f0; border-radius:6px; margin-top:15px; margin-bottom:20px;">
                        <div class="box-header with-border" style="background:#f8fafc; padding:10px 15px;">
                          <div class="row align-items-center">
                            <div class="col-xs-6">
                              <h3 class="box-title" style="font-size:15px; font-weight:600; color:#1e293b; margin:4px 0;">Section Permissions</h3>
                            </div>
                            <div class="col-xs-6 text-right">
                              <div class="btn-group btn-group-xs">
                                <button type="button" class="btn btn-default" onclick="setAllPerms('no_access')">All No Access</button>
                                <button type="button" class="btn btn-default" onclick="setAllPerms('read')">All Read</button>
                                <button type="button" class="btn btn-default" onclick="setAllPerms('write')">All Write</button>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="box-body table-responsive no-padding">
                          <table class="table table-hover table-bordered" style="margin-bottom:0;">
                            <thead>
                              <tr style="background:#f1f5f9;">
                                <th style="width:40%;">CRM Section</th>
                                <th style="width:20%; text-align:center;">No Access</th>
                                <th style="width:20%; text-align:center;">Read</th>
                                <th style="width:20%; text-align:center;">Write</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php foreach ($sections as $s_key => $s_title) { 
                                $cur = isset($current_perms[$s_key]) ? $current_perms[$s_key] : ((int)$role['id'] === 1 ? 'write' : 'read');
                              ?>
                              <tr>
                                <td style="vertical-align:middle;"><strong><?php echo htmlspecialchars($s_title); ?></strong></td>
                                <td style="text-align:center; vertical-align:middle;">
                                  <label style="cursor:pointer; font-weight:normal; margin-bottom:0; display:inline-block; padding:4px 8px;">
                                    <input type="radio" name="permissions[<?php echo $s_key; ?>]" value="no_access" class="perm-radio perm-no-access" <?php if ($cur === 'no_access') echo 'checked'; ?>> No Access
                                  </label>
                                </td>
                                <td style="text-align:center; vertical-align:middle;">
                                  <label style="cursor:pointer; font-weight:normal; margin-bottom:0; display:inline-block; padding:4px 8px;">
                                    <input type="radio" name="permissions[<?php echo $s_key; ?>]" value="read" class="perm-radio perm-read" <?php if ($cur === 'read') echo 'checked'; ?>> Read
                                  </label>
                                </td>
                                <td style="text-align:center; vertical-align:middle;">
                                  <label style="cursor:pointer; font-weight:normal; margin-bottom:0; display:inline-block; padding:4px 8px;">
                                    <input type="radio" name="permissions[<?php echo $s_key; ?>]" value="write" class="perm-radio perm-write" <?php if ($cur === 'write') echo 'checked'; ?>> Write
                                  </label>
                                </td>
                              </tr>
                              <?php } ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                        <button type="submit" name="submit" class="btn btn-primary" style="background:#2563eb; border-color:#2563eb; padding:8px 24px; font-weight:600;">Save Role &amp; Permissions</button>
                        <a href="<?php echo base_url(); ?>manager/roles" class="btn btn-default" style="margin-left:8px;">Cancel</a>
                      </div>
                    </div>
                  </div>
                </form>

                <script>
                function setAllPerms(val) {
                  var radios = document.querySelectorAll('.perm-' + (val === 'no_access' ? 'no-access' : val));
                  radios.forEach(function(r) {
                    r.checked = true;
                  });
                }
                </script>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
