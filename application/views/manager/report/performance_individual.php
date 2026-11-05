<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper px-4">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> <?php echo $info['page_heading']; ?> </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- /.row -->
        <div class="row">
            <!-- form close-->
            <div class="col-xs-12">


                <div class="box  p-4">
                    <div class="x_row">

                    <div class="x_col">
                    <form class="form-inline head_filter form-inline-mob" action="<?php echo base_url(); ?>manager/performance-report/individual" method="get">

                    <div class="form-group">

                        <div class="input-daterange input-group" id="datepicker" style="width: 100%;">

                            <input type="text" class="input-md form-control datepicker" name="d1"
                                placeholder="Create From"
                                value="<?php if(isset($_GET['d1'])&&(!empty($_GET['d1']))){ echo $_GET['d1'];}?>"
                                autocomplete="off" />
                            <span class="input-group-addon">To</span>
                            <input type="text" class="input-md form-control datepicker" name="d2"
                                placeholder="Create To"
                                value="<?php if(isset($_GET['d2'])&&(!empty($_GET['d2']))){ echo $_GET['d2'];}?>"
                                autocomplete="off" />


                        </div>
                    </div>

                    <?php if(isset($_GET['manager_id'])&&(!empty($_GET['manager_id']))){ ?>
                    <input type="hidden" name="manager_id" value="<?php if(isset($_GET['manager_id'])&&(!empty($_GET['manager_id']))){ echo (int)$_GET['manager_id'];}?>" />
                    <?php  }else{ ?>
                      <input type="hidden" name="manager_id" value="<?php  echo $this->session->userdata('manager_id');?>" />

                    <?php }?>




                <div class="form-group">
                    <button type="submit" class="btn btn-success" name="search" value="report">Search</button>
                    <a href="<?php echo base_url(); ?>manager/performance-report/individual"  class="btn btn-danger"> Reset</a>
                </div>


              </form>

                    </div>
                    <div class="x_col-sm-auto  text-center pt-4 pt-md-0">
                    <div>
                    
                    <!--<a href="<?php echo base_url(); ?>manager/leads/add" class="btn btn-success"> Add New</a> -->



                        </div>
                    </div>  
                    
                    </div>
                       
      
                </div>

                <div class="box">
            <div class="box-header">
            </div>
            <!-- /.box-header -->
            <div class="box-body"> 
        <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
		  <?php echo get_message($this->session->flashdata('message'),'message'); ?>
        
          <?php if(isset($list) && !empty($list))
				{?>
           <div class="table-responsive" style="padding:0px 15px">
            <table class="table table-hover table-bordered">
              <thead>

              <tr>   
                <td colspan="6">
                  <h4>Individual Performance Report</h4>
                </td>         
              </tr>

              <?php if((isset($_GET['d2']) and $_GET['d1']!='') && (isset($_GET['d2']) and $_GET['d2']!='')) {	?>
              <tr>   
              <td colspan="3">
                  <h4>From Date  <?php echo date('d-M-Y',strtotime($_GET['d1'])); ?> </h4>
                </td>         
                <td colspan="3">
                  <h4>To <?php echo date('d-M-Y',strtotime($_GET['d2'])); ?></h4>
                </td>         
              </tr>

              <?php } ?> 

              <tr>
              <td colspan="3">
                  <h4>Report Date : </h4>
                </td>         
                <td colspan="3">
                  <h4> <?php echo date('d-M-Y'); ?> </h4>
                </td>         
              </tr>


              <tr>   
                <th>Date</th>  
                <th>Leads Generated</th>
                <th>Attended Followups</th>
                <th>Missed Followups</th>
                <th>Attended Meetings</th>
                <th>Missed Meetings</th>               
              </tr>

              </thead>
              <tbody>
                <?php 
				
				foreach($list as $k=>$li){ ?>
				
                <tr>
                  <td><?php echo date('d-M-Y',  strtotime($li['created'])); ?></td>          
                  <td><?php echo $li['totalLead']; ?></td>
                  <td><?php echo $li['attendedFollowups']; ?></td>
                  <td><?php echo $li['missedFollowups']; ?></td>
                  <td><?php echo $li['attendedMeetings']; ?></td>
                  <td><?php echo $li['totalMissedMeetings']; ?></td>
                </tr>
                <?php
					}
				?>
                  
         
              </tbody>
            </table>
           
          </div>
          <?php }
			else
			{
			  ?>
              <div class="alert alert-info">
          No Result Found
          </div>
<?php
			}?>
          
          <!-- /.box-body --> 
          
        </div>
       
    </div>



            </div>
        </div>
    </section>
</div>


<script>
				
$(function(){
$('.input-daterange').datepicker({
    autoclose: true
});
}); 
 
$("table").tableExport({
	formats: ["xlsx","xls", "csv", "txt"], 
	fileName: "SRJ-User-report-<?php echo date('d-m-Y'); ?>",
	bootstrap: true,                   // (Boolean), style buttons using bootstrap
    position: "top" ,                // (top, bottom), position of the caption element relative to tabl
    trimWhitespace: false 
   });
 
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>