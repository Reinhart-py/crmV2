<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper"> <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1> <?php echo $info['page_heading']; ?> </h1>
  </section>
  <!-- Main content -->
  <section class="content"> <!-- /.row -->
    <div class="row"> <!-- form close-->
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
          <form class="form-inline head_filter" method="get">

             <div class="form-group">
       
        <div class="input-daterange input-group" id="datepicker" style="width: 100%;" >
          <?php if($this->uri->segment(3)=='followups') { ?>

            <select class="form-control" name="type">
            <option value="0" >Type</option>                
                <option <?php if(isset($_GET['type']) && !empty($_GET['type']) && $_GET['type']==1){ echo "selected"; } ?>  value="1">All Missed</option>
                <option <?php if(isset($_GET['type']) && !empty($_GET['type']) && $_GET['type']==2){ echo "selected"; } ?> value="2">Last 7 Days</option>                  
                <option <?php if(isset($_GET['type']) && !empty($_GET['type']) && $_GET['type']==3){ echo "selected"; } ?> value="3">Today</option>                  
                <option <?php if(isset($_GET['type']) && !empty($_GET['type']) && $_GET['type']==4){ echo "selected"; } ?> value="4">Next 7 Days</option>                  
                <option <?php if(isset($_GET['type']) && !empty($_GET['type']) && $_GET['type']==5){ echo "selected"; } ?> value="5">All Futures</option>                  
            </select>

          <?php }else{ ?> 

        <input type="text" class="input-md form-control datepicker" name="d1" placeholder="From Date" value="<?php if(isset($_GET['d1'])&&(!empty($_GET['d1']))){ echo $_GET['d1'];}?>" autocomplete="off"/>
        <span class="input-group-addon">To</span>
        <input type="text" class="input-md form-control datepicker" name="d2" placeholder="To Date" value="<?php if(isset($_GET['d2'])&&(!empty($_GET['d2']))){ echo $_GET['d2'];}?>" autocomplete="off"/>

        <?php } ?>

      </div>
     </div>   
        
      
        <div class="form-group">
  
            <select class="multiselect-ui form-control" multiple="multiple" name="category[]">
              <option value="" disabled>All</option>                
                  <option <?php if(isset($_GET['category']) && !empty($_GET['category']) && in_array('New', $_GET['category'])){ echo "selected"; } ?>  value="New">New</option>
                  <option <?php if(isset($_GET['category']) && !empty($_GET['category']) && in_array('Existing', $_GET['category'])){ echo "selected"; } ?> value="Existing">Existing</option>                  
                </select>

          </div>
  
         <div class="form-group">
      
       <select id="dates-field2" class="multiselect-ui form-control" multiple="multiple" name="status[]">
                <option value="" disabled>All Status</option>

                  <?php if(isset($st_list) && !empty($st_list)){
                    foreach($st_list as $cli){
                  ?>
                  <option <?php if(isset($_GET['status']) && !empty($_GET['status']) && in_array($cli['id'], $_GET['status'])){ echo "selected"; } ?> value="<?php echo $cli['id']; ?>" ><?php echo $cli['status']; ?></option>
              <?php } }  ?>
            </select>
              </div>
      
        
     <div class="form-group">
        <select class="form-control" name="order_by">
          <option disabled>Sort By</option>
          <option <?php if(!empty($_GET['order_by']) && ($_GET['order_by']=='lead_id')) { echo "selected"; } ?> value="lead_id">ID</option>
          <option <?php if(!empty($_GET['order_by']) && ($_GET['order_by']=='customer')) { echo "selected"; } ?> value="customer">Customer</option>
          <option <?php if(!empty($_GET['order_by']) && ($_GET['order_by']=='productcat')) { echo "selected"; } ?> value="productcat">Product Type</option> 
          <option <?php if(!empty($_GET['order_by']) && ($_GET['order_by']=='mrr')) { echo "selected"; } ?> value="mrr">MRR</option>
          <option <?php if(!empty($_GET['order_by']) && ($_GET['order_by']=='poc')) { echo "selected"; } ?> value="poc">POC Name</option>
        </select>
     </div>

      <div class="form-group">
        <select name="sort_by" class="form-control">
          <option disabled>Order</option>
          <option <?php if(!empty($_GET['sort_by']) && ($_GET['sort_by']=='DESC')) { echo "selected"; } ?> value="DESC">Descending</option>
          <option <?php if(!empty($_GET['sort_by']) && ($_GET['sort_by']=='ASC')) { echo "selected"; } ?> value="ASC">Ascending</option>
        </select>
     </div>

      <div class="form-group">
      <input type="text" class="input-md form-control" name="search" placeholder="Enter digits to search" value="<?php if(isset($_GET['search'])&&(!empty($_GET['search']))){ echo $_GET['search'];}?>" autocomplete="off"/>
     </div>


     <div class="form-group">
         <button type="submit" class="btn btn-success">Search</button>
                <a href="<?php echo base_url(); ?>manager/leads/followups" class="btn btn-danger"> Reset</a>
     </div>
    
        
               
              
             
                
               <!-- <a href="<?php //echo base_url(); ?>manager/players/add/" class="btn btn-info">Add New</a>-->
              </form>
              <div class="pull-right">
                <?php if($this->uri->segment(3)=='followups'){ ?>

              <!--  <a href="<?php echo base_url(); ?>manager/leads/followups/1" class="btn btn-danger">All Missed</a>
                <a href="<?php echo base_url(); ?>manager/leads/followups/2" class="btn btn-danger">Last 7 Days</a>
                <a href="<?php echo base_url(); ?>manager/leads/followups/3" class="btn btn-danger">Today</a>
                <a href="<?php echo base_url(); ?>manager/leads/followups/4" class="btn btn-danger">Next 7 Days</a>
                <a href="<?php echo base_url(); ?>manager/leads/followups/5" class="btn btn-danger">All Future</a> -->


                 <?php } 
                 elseif($this->uri->segment(3)=='meetings'){ ?>

              <a href="<?php echo base_url(); ?>manager/leads/meetings/1" class="btn btn-danger">All Missed</a>
              <a href="<?php echo base_url(); ?>manager/leads/meetings/2" class="btn btn-danger">Last 7 Days</a>
              <a href="<?php echo base_url(); ?>manager/leads/meetings/3" class="btn btn-danger">Today</a>
              <a href="<?php echo base_url(); ?>manager/leads/meetings/4" class="btn btn-danger">Next 7 Days</a>
              <a href="<?php echo base_url(); ?>manager/leads/meetings/5" class="btn btn-danger">All Future</a>

                <?php } else { ?>

                 <a href="<?php echo base_url(); ?>manager/leads/add" class="btn btn-success"> Add New</a>
                <?php } ?>
               

            </div>
          </div>
          <div class="box-body"> <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> <?php echo get_message($this->session->flashdata('message'),'message'); ?>
            <?php if(isset($product_list) && !empty($product_list))       {?>
            <div>
              <table class="table table-hover table-bordered table-striped" style="width:100%">
                <thead>
                  <tr>
                    <th>S.N.</th>
                    <th>ID</th>                    
                    <th>Customer</th>
                    <th>Product Type</th>
                    <th>MRR</th>
                    <th>POC Name</th>
                    <th>Contact Number</th>
                    <th>Next FU date</th>
                    <th>Created</th>
                    <th>Status</th>                                    
                    <th width="150">Action</th>
                   
                    
                   
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th></th>
                    <th></th>                    
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>                                    
                    <th></th>
                   
                    
                   
                  </tr>
                </tfoot>
                <tbody>
                  <?php foreach($product_list as $k=>$li){ ?>
                  <tr>
                    <td><?php echo ($k+1); ?></td>
                    <td><?php echo $li['lead_id']; ?></td>                  
                    <td><?php echo $li['customer']; ?></td>
                    <td><?php echo $li['productcat']; ?></td>
                    <td><?php echo $li['mrr']; ?></td>
                     <td><?php echo $li['poc']; ?></td>
                      <td><?php echo $li['contact1']; ?></td>
                      <td><span class="no_show"><?php echo date('Ymd',strtotime($li['followup_date'])); ?></span><?php echo simple_date($li['followup_date']); ?></td>
                    <td>
                      <?php echo $li['lead_status']; ?>
                      <?php 
                      $cat_label = !empty($li['status_category']) ? $li['status_category'] : (!empty($li['lost_reason']) ? $li['lost_reason'] : '');
                      if (!empty($cat_label)): ?>
                        <br><small class="text-muted" style="font-weight: 500;">Category: <?php echo htmlspecialchars($cat_label); ?></small>
                      <?php endif; ?>
                      <?php if (!empty($li['quote_status'])): ?>
                        <br><span class="label label-info" style="font-size: 10px;"><?php echo htmlspecialchars($li['quote_status']); ?></span>
                      <?php endif; ?>
                    </td>
                   <td>
                  <div class="btn-group" style="min-width: 85px;">
                  <button type="button" class="btn btn-sm btn-warning">Action</button>
                  <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li> <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($li['id']); ?>" data-toggle="tooltip" title="View">Preview</a> </li>                  
                    <li> <a href="<?php echo base_url(); ?>manager/leads/edit/<?php echo base64_encode($li['id']); ?>" data-toggle="tooltip" title="Edit">Edit</a>  </li>
                  </ul>
                </div>

             
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
              <?php if (isset($links)) {  echo $links; } ?>
            </div>
            <?php } else{  ?>
            
            <div class="alert alert-info"> No Data Found </div>
            <?php }?>
            <!-- /.box-body --> </div>
        </div>
        <!-- /.box --> </div>
    </div>
  </section>
</div>
<script>
function do_confirm(){    
job=confirm("Are you sure to delete this record... ");    
if(job!=true)    {        
return false;    
}}</script>
 <script type="text/javascript">       

 
  $(document).ready(function(){
  $(document).on("click","#btn_block", function(){ 
      
   var id = $(this).data("id");
   var admin_status = $(this).data("admin_status");
  
  if (confirm("Are you sure to unblock User?")) {
  
    $.ajax({
        type: "POST",
       url: '<?php  echo base_url(); ?>ci_admin_user/ajax_edit_status',
      data:"admin_status="+admin_status+"&id="+id, // as you are getting in php $_POST['action1'] 
       
        }).done(function(msg){
           
            if (msg == 'success') 
      {
             location.reload();
            }
      else
      {
         alert("Error : Not update Confirm status")
      }
    });
  }
   });

 }); 
 
  $(document).ready(function(){
  $(document).on("click","#btn_unblock", function(){ 
      
   var id = $(this).data("id");
   var admin_status = $(this).data("admin_status");
  
  if (confirm("Are you sure to block User?")) {
  
    $.ajax({
        type: "POST",
       url: '<?php  echo base_url(); ?>ci_admin_user/ajax_edit_status',
       data:"admin_status="+admin_status+"&id="+id, // as you are getting in php $_POST['action1'] 
       
        }).done(function(msg){
           
            if (msg == 'success') 
      {
             location.reload();
            }
      else
      {
         alert("Error : Not update Confirm status")
      }
    });
  }
   });

 }); 
   
 
$(".remove").click(function(){       
    var id= $(this).data('value');
       swal({
        title: "Are you sure?",
        text: "You want to delete this lead!",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: false,
        closeOnCancel: true
      },
      function(isConfirm) {
        if (isConfirm) {
          $.ajax({
             url: 'deleteleads/'+id,
             type:'GET',            
             error: function() {
                alert('Something is wrong');
             },
             success: function(data) {
                 // $("#"+id).remove();
                swal("Deleted!", "", "success");
                //location.reload();
                  //swal("Logged Out!", "", "success");
             }
          });
        } 
      });
     
    });

    $(document).ready(function() {
    $('#example_lead').DataTable( {
      "pageLength":50,
      responsive: true,
      
        initComplete: function () {
            this.api().columns([3,9]).every( function () {
              
                var column = this;
                var select = $('<select><option value="">Select </option></select>')
                    .appendTo( $(column.footer()).empty() )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
 
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );
 
                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );
            } );
        }
    } );
} );

  </script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>

 