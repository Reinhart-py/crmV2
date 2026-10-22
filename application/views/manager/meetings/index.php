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
            <div class="pull-right">
             
               <a href="<?php echo base_url(); ?>manager/meetings/add" class="btn btn-success"> Add New</a>
            </div>
          </div>
          <div class="box-body"> 
          <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
          <?php echo get_message($this->session->flashdata('message'),'message'); ?>
          
            <?php if(isset($product_list) && !empty($product_list))       {?>
            <div>
              <table id="example_lead" class="table table-hover table-bordered table-striped" style="width:100%">
              <thead>
                  <tr>
                    <th>S.N.</th>
                    <th>Lead ID</th>                    
                    <th>Date</th>
                    <th>Time</th>
                    <th>POC</th>
                    <th>Mobile</th>
                    <th>Address</th>                                                   
                    <th>Action</th>               
                    
                   
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
                   
                    
                   
                  </tr>
                </tfoot>
                <tbody>
                  <?php foreach($product_list as $k=>$li){ ?>
                  <tr>
                    <td><?php echo ($k+1); ?></td>
                    <td><?php echo $li['lead_id']; ?></td> 
                    <td><span class="no_show"><?php echo date('Ymd',strtotime($li['m_date'])); ?></span><?php echo simple_date($li['m_date']); ?></td>                 
                    <td><?php echo $li['m_time']; ?></td>
                    <td><?php echo $li['m_poc'] ?? $li['poc'] ?? ''; ?></td>
                    <td><?php echo $li['m_mobile'] ?? $li['mobile'] ?? ''; ?></td>
                    <td><?php echo $li['m_address'] ?? $li['address'] ?? ''; ?></td>
                     
                    
                                           
                  <td>

                     <div class="btn-group">
                     <a href="<?php echo base_url(); ?>manager/meetings/edit/<?php echo base64_encode($li['id']); ?>" class="btn btn-sm btn-success" title="Edit">Edit</a>
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

    $(document).ready(function() {
    $('#example_lead').DataTable( {
      "pageLength":50,
        initComplete: function () {
            this.api().columns([5,6,9]).every( function () {
              
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

 