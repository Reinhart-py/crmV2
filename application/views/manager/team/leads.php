<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper px-4"> <!-- Content Header (Page header) -->
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
          <form class="form-inline head_filter form-inline-mob" method="get">

              <div class="form-group">

              <div class="input-daterange input-group" id="datepicker" style="width: 100%;" >             

              <input type="text" class="input-md form-control datepicker" name="d1" placeholder="Create From" value="<?php if(isset($_GET['d1'])&&(!empty($_GET['d1']))){ echo $_GET['d1'];}?>" autocomplete="off"/>
              <span class="input-group-addon">To</span>
              <input type="text" class="input-md form-control datepicker" name="d2" placeholder="Create To" value="<?php if(isset($_GET['d2'])&&(!empty($_GET['d2']))){ echo $_GET['d2'];}?>" autocomplete="off"/>

              </div>
              </div>

              <div class="form-group">
                  <div class="input-daterange input-group" style="width: 100%;">
                  <input type="text" class="input-md form-control only_number" name="mrr1" placeholder="MRR Min" value="<?php if(isset($_GET['mrr1'])&&(!empty($_GET['mrr1']) OR $_GET['mrr1']=='0')){ echo $_GET['mrr1'];}?>"  autocomplete="off" />
                  <span class="input-group-addon">To</span>
                  <input type="text" class="input-md form-control only_number" name="mrr2" placeholder="MRR Max" value="<?php if(isset($_GET['mrr2'])&&(!empty($_GET['mrr2']))){ echo $_GET['mrr2'];}?>"  autocomplete="off" />                  
                  </div>
              </div>

              <div class="form-group">
              <select class="multiselect-ui form-control" multiple="multiple" name="category[]" data-placeholder="Service Type">
                 <?php if(isset($pc_list) && !empty($pc_list)){
                    foreach($pc_list as $pli){
                  ?>
                    <option <?php if(isset($_GET['category']) && !empty($_GET['category']) && in_array($pli['id'], $_GET['category'])){ echo "selected"; } ?> value="<?php echo $pli['id']; ?>" ><?php echo $pli['title']; ?></option>
                <?php } }  ?>
                </select>
              </div>
              
              <div class="form-group">
                <select id="dates-field2" class="multiselect-ui form-control" multiple="multiple"
                    name="lCat[]" data-placeholder="Lead Category">
                    <?php if(isset($lc_list) && !empty($lc_list)){
                      foreach($lc_list as $cli){
                      ?>
                    <option
                        <?php if(isset($_GET['lCat']) && !empty($_GET['lCat']) && in_array($cli['id'], $_GET['lCat'])){ echo "selected"; } ?>
                        value="<?php echo $cli['id']; ?>"><?php echo $cli['title']; ?></option>
                    <?php } }  ?>
                </select>
              </div>

              <div class="form-group">
              <select id="dates-field2" class="multiselect-ui form-control" multiple="multiple" name="status[]" data-placeholder="Status">
                  <?php if(isset($st_list) && !empty($st_list)){
                    foreach($st_list as $cli){
                  ?>
                  <option <?php if(isset($_GET['status']) && !empty($_GET['status']) && in_array($cli['id'], $_GET['status'])){ echo "selected"; } ?> value="<?php echo $cli['id']; ?>" ><?php echo $cli['status']; ?></option>
              <?php } }  ?>
              </select>
              </div>


              <div class="form-group">
                  <input type="text" class="input-md form-control" name="user_name" placeholder="User Name" value="<?php if(isset($_GET['user_name'])&&(!empty($_GET['user_name']))){ echo $_GET['user_name'];}?>"  autocomplete="off" />
              </div>

              <div class="form-group">
                  <select class="form-control select2" name="country[]" data-placeholder="Select Country" id="select_val" onchange="ajax_get_state()">
                  <option value="">Select Country</option>
                      <?php if(isset($c_list) && !empty($c_list)){
                      foreach($c_list as $c_list){
                      ?>
                      <option <?php if(isset($_GET['country']) && !empty($_GET['country']) && in_array($c_list['id'], $_GET['country'])){ echo "selected"; } ?>
                          value="<?php echo $c_list['id']; ?>"><?php echo $c_list['name']; ?></option>
                      <?php } }  ?>
                  </select>
              </div>


              <div class="form-group" >
                  <select  class="form-control multiselect-ui select3" multiple="multiple" name="state[]"  data-placeholder="Select State" id="select_state">
                  <?php if(isset($state_list) && !empty($state_list)){
                      foreach($state_list as $state_list){
                      ?>
                      <option <?php if(isset($_GET['state']) && !empty($_GET['state']) && in_array($state_list['id'], $_GET['state'])){ echo "selected"; } ?>
                          value="<?php echo $state_list['id']; ?>"><?php echo $state_list['name']; ?></option>
                      <?php } }  ?>
                  </select>
              </div>
                            
              
              <div class="form-group">
              <button type="submit" class="btn btn-success">Search</button>
                <a href="<?php echo base_url(); ?>manager/team/leads" class="btn btn-danger"> Reset</a>
              </div>

              </form>
          </div>
          </div>
      </div>


        <div class="box">
         
          <div class="box-body"> <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> <?php echo get_message($this->session->flashdata('message'),'message'); ?>
            <?php if(isset($product_list) && !empty($product_list))       {?>
            <div>
              <table id="example_lead" class="table table-hover table-bordered table-striped" style="width:100%">
                <thead>
                  <tr>
                   
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Service Type</th>
                    <th>MRR</th>
                    <th>POC Name</th>
                    <th>Contact Number</th>
                    <th>Next FU date</th>    
                    <th>Date</th>                   
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
                                       
                    <td><?php echo $li['lead_id']; ?></td>
                    <td><?php echo $li['customer']; ?></td>
                    <td><?php echo $li['servicecat']; ?></td>
                    <td><?php echo $li['mrr']; ?></td>
                     <td><?php echo $li['poc']; ?></td>
                      <td><?php echo $li['contact1']; ?></td>
                      <td><span class="no_show"><?php echo date('Ymd',strtotime($li['followup_date'])); ?></span><?php echo simple_date($li['followup_date']); ?></td>
                      <td><span class="no_show"><?php echo date('Ymd',strtotime($li['created'])); ?></span><?php echo simple_date($li['created']); ?></td>                  
                      <td><?php echo $li['owner_name']; ?></td>
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

                     <div class="btn-group">
                  <button type="button" class="btn btn-sm btn-warning">Action</button>
                  <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li> <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($li['id']); ?>/tm" data-toggle="tooltip" title="View">Preview</a> </li>                  
                    <li> <a href="<?php echo base_url(); ?>manager/leads/edit/<?php echo base64_encode($li['id']); ?>/tm" data-toggle="tooltip" title="Edit">Edit</a>  </li>
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

$(document).ready(function()
{
  $('#select_val').on('change', function() {
    var value = $(this).val();
    if ($(this).val() == (value)) {
          $("#select_div").show();
      } else {
          $("#select_div").hide();
      }
  });
});

function ajax_get_state()
{
  var countryID = $("#select_val").val();   
    $.ajax({
          type: "POST",
    data: 'country_id='+countryID,
      url: "<?php echo base_url(); ?>" + "ci_admin_leads/ajax_get_state",
    success:function(result){
    if(result)
    {
    $('#select_state').html(result);
		$('#select_state').multiselect('rebuild');	
    }
    else
    {
    alert('error');
    }
    }
    });
}


 
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

     $(document).ready(function() {
    $('#example_lead').DataTable( {
      "pageLength":50,
      responsive: true,
        initComplete: function () {
            this.api().columns([3,8,9]).every( function () {
              
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
        	{ targets: [6,7,10], className:"dt-nowrap",}       
    	]
    } );
} );
   
 
  </script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>