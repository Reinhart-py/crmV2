<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper px-4 tlv"> <!-- Content Header (Page header) -->
<?php echo crm_mz_tabbar(); ?>
  <section class="content-header">
    <h1> <?php echo $info['page_heading']; ?> </h1>
  </section>
  <!-- Main content -->
  <section class="content"> <!-- /.row -->
    <div class="row"> <!-- form close-->
      <div class="col-xs-12">

      <div class="box  p-4">
      <div class="x_row">
      <div class="x_col">  

      <form class="form-inline form-inline-mob" method="get">

        <div class="form-group">

        <div class="input-daterange input-group" id="datepicker" style="width: 100%;" >


        <select class="form-control" name="type">
        <option value="" >Time Period</option>                
          <option <?php if(isset($_GET['type']) && !empty($_GET['type']) && $_GET['type']==1){ echo "selected"; } ?>  value="1">All Missed</option>
          <option <?php if(isset($_GET['type']) && !empty($_GET['type']) && $_GET['type']==2){ echo "selected"; } ?> value="2">Last 7 Days</option>                  
          <option <?php if(isset($_GET['type']) && !empty($_GET['type']) && $_GET['type']==3){ echo "selected"; } ?> value="3">Today</option>                  
          <option <?php if(isset($_GET['type']) && !empty($_GET['type']) && $_GET['type']==4){ echo "selected"; } ?> value="4">Next 7 Days</option>                  
          <option <?php if(isset($_GET['type']) && !empty($_GET['type']) && $_GET['type']==5){ echo "selected"; } ?> value="5">All Futures</option>                  
        </select>
        </div>
        </div>   

        <div class="form-group">

        <select id="dates-field2" class="multiselect-ui form-control" multiple="multiple" name="status[]" data-placeholder="Status">
            <?php if(isset($st_list) && !empty($st_list)){
              foreach($st_list as $cli){
            ?>
            <option <?php if(isset($_GET['status']) && !empty($_GET['status']) && in_array($cli['id'], $_GET['status'])){ echo "selected"; } ?> value="<?php echo $cli['id']; ?>" ><?php echo $cli['title']; ?></option>
        <?php } }  ?>
        </select>
        </div>

        <div class="form-group">
        <button type="submit" class="btn btn-success">Search</button>
          <a href="<?php echo base_url(); ?>manager/verticals" class="btn btn-danger"> Reset</a>
        </div>

        </form>

      </div>
      </div>
      </div>

        <div class="box">
          <div class="box-body"> 
          <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
          <?php echo get_message($this->session->flashdata('message'),'message'); ?>
          
            <?php if(isset($product_list) && !empty($product_list))       {?>
            <div>
              <table id="example_lead" class="table table-hover table-bordered table-striped" style="width:100%">
                <thead>
                  <tr>
                    <th>S.N.</th>
                    <th>ID</th>                    
                    <th>Name</th>
                    <th>POC</th>
                    <th>Contact</th>
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
                  </tr>
                </tfoot>
                <tbody>
                  <?php foreach($product_list as $k=>$li){ ?>
                  <tr>
                    <td><?php echo ($k+1); ?></td>
                    <td><?php echo $li['v_id']; ?></td>                  
                    <td><?php echo $li['company_name']; ?></td>
                    <td><?php echo $li['poc']; ?></td>
                    <td><?php echo $li['contact1']; ?></td>
                      <td><span class="no_show"><?php echo date('Ymd',strtotime($li['followup_date'])); ?></span><?php echo simple_date($li['followup_date']); ?></td>
                      <td><span class="no_show"><?php echo date('Ymd',strtotime($li['created'])); ?></span><?php echo simple_date($li['created']); ?></td>
                   <td><?php echo $li['vertical_status']; ?></td>                                  
                    
                  
                  <td>

                     <div class="btn-group">
                  <button type="button" class="btn btn-sm btn-warning">Action</button>
                  <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li> <a href="<?php echo base_url(); ?>manager/verticals/view/<?php echo base64_encode($li['id']); ?>" data-toggle="tooltip" title="View">Preview</a> </li>                  
                    <li> <a href="<?php echo base_url(); ?>manager/verticals/edit/<?php echo base64_encode($li['id']); ?>" data-toggle="tooltip" title="Edit">Edit</a>  </li>
                   
                   
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
      responsive: window.innerWidth >= 768, // phones: each record is a card (crm-tlist.css)
        initComplete: function () {
            this.api().columns([7]).every( function () {
              
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
        },
        columnDefs: [
        	{ targets: [5,6,8], className:"dt-nowrap",}       
    	]
    } );
} );

  </script>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-tlist.css?v=20261106_2">
<script src="<?php echo base_url(); ?>assets/dist/js/crm-tlist.js?v=20261106_2"></script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>

 