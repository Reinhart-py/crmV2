<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<style>
.roles-page-container {
    padding: 0 16px 40px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: #0f172a;
}
.role-list-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    overflow: hidden;
}
.role-list-header {
    padding: 20px 24px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #ffffff;
}
.role-list-title {
    font-size: 17px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    letter-spacing: -0.01em;
    display: flex;
    align-items: center;
    gap: 8px;
}
.role-list-subtitle {
    font-size: 13px;
    color: #64748b;
    margin-top: 4px;
}
.btn-add-role {
    background: #0072f0;
    color: #ffffff !important;
    border: 1px solid #0060d0;
    border-radius: 8px;
    padding: 9px 20px;
    font-size: 13px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none !important;
    box-shadow: 0 2px 6px rgba(0, 114, 240, 0.2);
    transition: all 0.15s ease;
}
.btn-add-role:hover {
    background: #0060d0;
    border-color: #0050b5;
    box-shadow: 0 4px 12px rgba(0, 114, 240, 0.3);
}
.role-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}
.role-table th {
    background: #f8fafc;
    color: #64748b;
    font-size: 11.5px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 12px 20px;
    border-bottom: 1px solid #e2e8f0;
}
.role-table td {
    padding: 14px 20px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 13px;
    color: #0f172a;
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
    gap: 5px;
    padding: 3px 10px;
    border-radius: 980px;
    font-size: 11.5px;
    font-weight: 600;
}
.status-active {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
}
.status-disabled {
    background: #f1f5f9;
    color: #64748b;
    border: 1px solid #e2e8f0;
}
.role-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    transition: all 0.15s ease;
    margin-left: 6px;
    text-decoration: none !important;
}
.role-action-btn:hover {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #0072f0;
}
.role-action-btn.btn-delete:hover {
    background: #fef2f2;
    border-color: #fecaca;
    color: #dc2626;
}
</style>

<div class="content-wrapper px-4"> 
  <section class="content-header" style="padding: 20px 0 16px 0;">
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <div>
        <h1 style="font-size: 24px; font-weight: 700; color: #0f172a; margin:0; letter-spacing: -0.02em;">
          <?php echo htmlspecialchars($info['page_heading']); ?>
        </h1>
      </div>
      <div>
        <a href="<?php echo base_url(); ?>manager/roles/add" class="btn-add-role">
          <i class="fa fa-plus"></i> Add New Role
        </a>
      </div>
    </div>
  </section>

  <section class="content roles-page-container" style="padding-left:0; padding-right:0;"> 
    <div class="row"> 
      <div class="col-xs-12">
        <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
        <?php echo get_message($this->session->flashdata('message'),'message'); ?>

        <div class="role-list-card">
          <div class="role-list-header">
            <div>
              <h2 class="role-list-title">
                <i class="fa fa-shield" style="color: #0072f0;"></i> System Roles &amp; Security Tiers
              </h2>
              <div class="role-list-subtitle">Manage granular module permissions and access control for company staff</div>
            </div>
            <div style="font-size: 13px; color: #64748b;">
              Total Configured Roles: <strong style="color:#0f172a; font-weight:700;"><?php echo !empty($user_list) ? count($user_list) : 0; ?></strong>
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
                  <td style="color:#94a3b8; font-weight:500;"><?php echo $k+1; ?></td>
                  <td>
                    <strong style="font-weight:600; font-size:14px; color:#0f172a;"><?php echo htmlspecialchars($li['title']); ?></strong>
                    <?php if ((int)$li['id'] === 1) { ?>
                      <span style="font-size:11px; background:#eff6ff; color:#0072f0; border:1px solid #bfdbfe; padding:2px 8px; border-radius:12px; margin-left:8px; font-weight:600;">
                        <i class="fa fa-star"></i> Administrator
                      </span>
                    <?php } elseif ((int)$li['id'] === 2) { ?>
                      <span style="font-size:11px; background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; padding:2px 8px; border-radius:12px; margin-left:8px; font-weight:600;">
                        <i class="fa fa-cogs"></i> Operations Control
                      </span>
                    <?php } ?>
                  </td>      
                  <td>
                    <span style="background:#f1f5f9; color:#475569; padding:3px 8px; border-radius:6px; font-size:12px; font-weight:600;">
                      Level <?php echo (int)$li['level']; ?>
                    </span>
                  </td> 
                  <td>
                    <?php if($li['status']==0) { ?>
                      <span class="role-status-badge status-disabled">
                        <i class="fa fa-ban"></i> Disabled
                      </span>
                    <?php } else { ?>
                      <span class="role-status-badge status-active">
                        <i class="fa fa-check-circle"></i> Active
                      </span>
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
    </div>
  </section>
</div>

<script>
function do_confirm(){    
  var job = confirm("Are you sure to delete this role?");    
  if(!job) {        
    return false;    
  }
}
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
