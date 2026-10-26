<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<div class="content-wrapper px-4"> 
  <section class="content-header">
    <h1> <?php echo $info['page_heading']; ?> </h1>
  </section>
  <section class="content"> 
    <div class="row"> 
      <div class="col-xs-12">
      <div class="box p-4">
      <div class="x_row">
      <div class="x_col"> 
            <div class="pull-right">
              <a href="<?php echo base_url(); ?>manager/users/add" class="btn btn-success"> Add New</a>
            </div>
      </div> 
      </div>
      </div>
        <div class="box">
          <div class="box-body"> 
          <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
          <?php echo get_message($this->session->flashdata('message'),'message'); ?>
            <?php if(isset($product_list) && !empty($product_list)) { ?>
            <div>
              <table id="example_user" class="table table-hover table-bordered table-striped" style="width:100%" >
                <thead>
                  <tr>
                    <th>S.N.</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Manager/Upline</th>
                    <th>Category</th>
                    <th>Created Date</th>   
                    <th>Status</th>                                                  
                    <th width="200">Action</th>              
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($product_list as $k=>$li){ ?>
                  <tr>
                    <td><span class="label label-primary" style="font-size:11.5px; letter-spacing:0.5px;"><?php echo sprintf('EMP-%05d', $li['id']); ?></span></td>
                    <td style="text-transform:capitalize;"><?php echo $li['name']; ?> </td>
                    <td><a href="mailto:<?php echo $li['c_username']; ?>"><?php echo $li['c_username']; ?></a></td>
                    <td><?php echo $li['manager_name']; ?></td>
                    <td><?php echo $li['role']; ?></td>
                    <td><span class="no_show"><?php echo date('Ymd',strtotime($li['created'])); ?></span><?php echo simple_date($li['created']); ?></td>
                    <td><?php if($li['admin_status']==1){?>
                      <span class="label label-success round">Enabled</span>
                      <?php }elseif($li['admin_status']==0){?>
                      <span class="label label-danger round">Disabled</span>
                      <?php } ?></td>
                  <td>
                    <div class="btn-group">
                  <button type="button" class="btn btn-sm btn-warning">Action</button>
                  <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li> <a href="<?php echo base_url(); ?>manager/users/view/<?php echo $li['id']; ?>" data-toggle="tooltip" title="View">Preview</a> </li>                  
                    <li> <a href="<?php echo base_url(); ?>manager/users/edit/<?php echo $li['id']; ?>" data-toggle="tooltip" title="Edit">Edit</a> </li>
                    <li> <a href="<?php echo base_url(); ?>manager/users/cedit/<?php echo $li['id']; ?>" data-toggle="tooltip" title="Credential">Credential</a> </li>
                    <li class="divider"></li>
                    <li><?php if($li['admin_status']==0){?>
                      <a href="javascript:void(0);" data-id="<?php echo $li['id'];?>" data-admin_status="0" class="btn-block-user" data-toggle="tooltip" title="Enable User">Enable</a> 
                      <?php }else{?>
                      <a href="javascript:void(0);" data-id="<?php echo $li['id'];?>" data-admin_status="1" class="btn-unblock-user" data-toggle="tooltip" title="Disable User">Disable</a> <?php } ?> </li>
                    <li><a href="<?php echo base_url(); ?>manager/users/delete/<?php echo $li['id']; ?>" onclick="return do_confirm();" data-toggle="tooltip" title="Delete" style="color:#dc2626;"><i class="fa fa-trash"></i> Delete</a></li>
                  </ul>
                </div>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
              <?php if (isset($links)) { echo $links; } ?>
            </div>
            <?php } else { ?>
            <div class="alert alert-info"> No Data Found </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<script>
function do_confirm(){    
  var job = confirm("Are you sure to delete this record?");    
  if(!job) {        
    return false;    
  }
}
$(document).ready(function(){
  $("#example_user").DataTable({
    "pageLength": 50,     
    rowReorder: {
      selector: 'td:nth-child(2)'
    },
    responsive: true,
    columnDefs: [
      { targets: [5,7], className:"dt-nowrap"}       
    ]
  });

  $(document).on("click",".btn-block-user", function(){ 
    var id = $(this).data("id");
    var admin_status = $(this).data("admin_status");
    if (confirm("Are you sure to enable User?")) {
      $.ajax({
        type: "POST",
        url: "<?php echo base_url(); ?>ci_admin_user/ajax_edit_status",     
        data: "admin_status=" + admin_status + "&id=" + id,     
        success: function(result){
          if (result == 'ok'){
            location.reload();
          } else {
            alert("Error: Unable to update status");
          }
        }      
      });
    }
  });

  $(document).on("click",".btn-unblock-user", function(){ 
    var id = $(this).data("id");
    var admin_status = $(this).data("admin_status");
    if (confirm("Are you sure to disable User?")) {
      $.ajax({
        type: "POST",
        url: "<?php echo base_url(); ?>ci_admin_user/ajax_edit_status",     
        data: "admin_status=" + admin_status + "&id=" + id,     
        success: function(result){
          if (result == 'ok'){
            location.reload();
          } else {
            alert("Error: Unable to update status");
          }
        }      
      });
    }
  });
});
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>