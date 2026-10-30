<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
  <section class="content-header" style="display:flex; justify-content:space-between; align-items:center;">
    <h1 style="margin:0;"> <?php echo $info['page_heading']; ?> </h1>
    <div>
      <a href="<?php echo base_url(); ?>manager/team/chart" class="btn btn-default" style="border-radius:8px; font-weight:600; padding:6px 14px; border:1px solid #cbd5e1; color:#0f172a; text-decoration:none;">
        <i class="fa fa-sitemap" style="margin-right:6px; color:#0072f0;"></i> Back to Chart View
      </a>
    </div>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <div class="pull-right">
            </div>
          </div>
          <div class="box-body"> <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> <?php echo get_message($this->session->flashdata('message'),'message'); ?>
            <?php if(isset($product_list) && !empty($product_list))				{?>
            <div>
              <table id="example_user" class="table table-hover table-bordered table-striped" style="width:100%">
                <thead>
                  <tr>
                    <th>S.N.</th>
                    <th>Name</th>
                    <th>Designation</th>   
                 	  <th>Department</th>          
                    <th>Contact No</th>
                    <th>Email</th>
                    <th>Location</th>
                    <th>Manager</th>           
                    <th>Action</th>           
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($product_list as $k=>$li){ ?>
                  <tr>
                    <td><span class="label label-primary" style="font-size:11.5px; letter-spacing:0.5px;"><?php echo sprintf('EMP-%05d', $li['id']); ?></span></td>
                    <td style="text-transform:capitalize;"><?php echo $li['name']; ?> </td>
                    <td><?php echo $li['role']; ?></td>
                    <td><?php echo $li['department']; ?></td>
                    <td><?php echo $li['mobile']; ?></td>
                    <td><?php echo $li['email']; ?></td>
                    <td><?php echo $li['office']; ?></td>
                    <td><?php echo $li['manager_name']; ?></td>
                    <td><a href="<?php echo base_url(); ?>manager/team/members/details/<?php echo $li['id']; ?>" type="button" class="btn btn-xs btn-info">Members</a></td>
                  </tr>
                  <?php	} ?>
                </tbody>
              </table>
              <?php if (isset($links)) {  echo $links; } ?>
            </div>
            <?php }	else{	 ?>
            <div class="alert alert-info"> No Data Found </div>
            <?php }?>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
function do_confirm(){    
  var job = confirm("Are you sure to delete this record... ");    
  if(job != true) {        
    return false;    
  }
}
</script>

<script type="text/javascript">       
$(document).ready(function(){
  $("#example_user").DataTable({
    "pageLength": 50,     
    rowReorder: {
      selector: 'td:nth-child(2)'
    },
    responsive: true
  });

  $(document).on("click", "#btn_block", function(){ 
    var id = $(this).data("id");
    var admin_status = $(this).data("admin_status");
    if (confirm("Are you sure to unblock User?")) {
      $.ajax({
        type: "POST",
        url: '<?php echo base_url(); ?>ci_admin_user/ajax_edit_status',
        data: "admin_status=" + admin_status + "&id=" + id
      }).done(function(msg){
        if (msg == 'success') {
          location.reload();
        } else {
          alert("Error : Not update Confirm status");
        }
      });
    }
  });

  $(document).on("click", "#btn_unblock", function(){ 
    var id = $(this).data("id");
    var admin_status = $(this).data("admin_status");
    if (confirm("Are you sure to block User?")) {
      $.ajax({
        type: "POST",
        url: '<?php echo base_url(); ?>ci_admin_user/ajax_edit_status',
        data: "admin_status=" + admin_status + "&id=" + id
      }).done(function(msg){
        if (msg == 'success') {
          location.reload();
        } else {
          alert("Error : Not update Confirm status");
        }
      });
    }
  });
}); 
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>