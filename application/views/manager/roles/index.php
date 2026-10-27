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
}
.apple-card-header {
    padding: 18px 24px;
    border-bottom: 1px solid #f2f2f7;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.apple-card-title {
    font-size: 17px;
    font-weight: 600;
    color: #1d1d1f;
    margin: 0;
    letter-spacing: -0.01em;
}
.apple-card-subtitle {
    font-size: 13px;
    color: #86868b;
    margin-top: 3px;
}
.apple-btn-black {
    background: #000000;
    color: #ffffff !important;
    border: 1px solid #000000;
    border-radius: 980px;
    padding: 8px 18px;
    font-size: 13px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none !important;
    transition: all 0.15s ease;
}
.apple-btn-black:hover {
    background: #2c2c2e;
    border-color: #2c2c2e;
    color: #ffffff !important;
}
.apple-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}
.apple-table th {
    background: #fafafa;
    color: #86868b;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 12px 20px;
    border-bottom: 1px solid #e5e5ea;
}
.apple-table td {
    padding: 14px 20px;
    border-bottom: 1px solid #f2f2f7;
    font-size: 13px;
    color: #1d1d1f;
    vertical-align: middle;
}
.apple-table tr:last-child td {
    border-bottom: none;
}
.apple-table tr:hover td {
    background: #fbfbfd;
}
.apple-status-pill {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 980px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.02em;
}
.apple-status-active {
    background: #000000;
    color: #ffffff;
}
.apple-status-disabled {
    background: #e5e5ea;
    color: #86868b;
}
.apple-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 8px;
    border: 1px solid #e5e5ea;
    background: #ffffff;
    color: #1d1d1f;
    transition: all 0.15s ease;
    margin-right: 4px;
    text-decoration: none !important;
}
.apple-action-btn:hover {
    background: #000000;
    border-color: #000000;
    color: #ffffff;
}
.apple-action-btn.btn-delete:hover {
    background: #1d1d1f;
    border-color: #1d1d1f;
    color: #ffffff;
}
</style>

<div class="content-wrapper px-4"> 
  <section class="content-header" style="padding-bottom: 14px;">
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <div>
        <h1 style="font-size: 24px; font-weight: 600; color: #1d1d1f; margin:0; letter-spacing: -0.02em;"><?php echo htmlspecialchars($info['page_heading']); ?></h1>
      </div>
      <div>
        <a href="<?php echo base_url(); ?>manager/roles/add" class="apple-btn-black">
          <i class="fa fa-plus"></i> Add Role
        </a>
      </div>
    </div>
  </section>

  <section class="content roles-page-container"> 
    <div class="row"> 
      <div class="col-xs-12">
        <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
        <?php echo get_message($this->session->flashdata('message'),'message'); ?>

        <div class="apple-card">
          <div class="apple-card-header">
            <div>
              <h2 class="apple-card-title">System Roles &amp; Access Tiers</h2>
              <div class="apple-card-subtitle">Manage access permissions across all sections of the CRM</div>
            </div>
            <div style="font-size: 12px; color: #86868b;">
              Total Roles: <strong style="color:#1d1d1f;"><?php echo !empty($user_list) ? count($user_list) : 0; ?></strong>
            </div>
          </div>

          <?php if((isset($user_list)) && (!empty($user_list))) { ?>
          <div class="table-responsive" style="margin-bottom:0;">
            <table class="apple-table">
              <thead>
                <tr>
                  <th style="width: 50px;">#</th>                  
                  <th>Role Name</th> 
                  <th style="width: 120px;">Hierarchy Level</th>                 
                  <th style="width: 120px;">Status</th>                 
                  <th style="width: 140px;">Created Date</th>
                  <th style="width: 110px; text-align:right;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($user_list as $k=>$li) { ?>
                <tr>
                  <td style="color:#86868b;"><?php echo $k+1; ?></td>
                  <td>
                    <strong style="font-weight:600; font-size:14px; color:#1d1d1f;"><?php echo htmlspecialchars($li['title']); ?></strong>
                    <?php if ((int)$li['id'] === 1) { ?>
                      <span style="font-size:10px; background:#f2f2f7; color:#1d1d1f; padding:2px 6px; border-radius:4px; margin-left:6px; font-weight:600;">Full System Access</span>
                    <?php } elseif ((int)$li['id'] === 2) { ?>
                      <span style="font-size:10px; background:#f2f2f7; color:#1d1d1f; padding:2px 6px; border-radius:4px; margin-left:6px; font-weight:600;">Operations Control</span>
                    <?php } ?>
                  </td>      
                  <td>Level <?php echo (int)$li['level']; ?></td> 
                  <td>
                    <?php if($li['status']==0) { ?>
                      <span class="apple-status-pill apple-status-disabled">Disabled</span>
                    <?php } else { ?>
                      <span class="apple-status-pill apple-status-active">Active</span>
                    <?php } ?>
                  </td>
                  <td style="color:#6e6e73;"><?php echo date('d-M-Y',strtotime($li['created'])); ?></td>
                  <td style="text-align:right;">
                    <a href="<?php echo base_url();?>manager/roles/edit/<?php echo $li['id']; ?>" class="apple-action-btn" title="Edit Role &amp; Permissions">
                      <i class="fa fa-pencil"></i>
                    </a>
                    <?php if($li['deletable']==1){ ?> 
                      <a href="<?php echo base_url();?>manager/roles/delete/<?php echo $li['id']; ?>" onclick="return do_confirm();" class="apple-action-btn btn-delete" title="Remove Role">
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
