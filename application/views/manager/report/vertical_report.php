<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper"> 
 
  <!-- Content Header (Page header) -->
  
  <section class="content-header">
    <h1> <?php echo $info['page_heading']; ?> </h1>
  </section>
  
  <!-- Main content -->
  
  <section class="content"> 
    <div class="row"> 
    <div class="col-md-12">
<form name="player_report_form" action="<?php echo base_url(); ?>manager/leads/report" method="get" enctype="multipart/form-data" >
 <div class="box">
            <div class="box-header">
            <h3 class="box-title pull-left" style="text-transform:capitalize">Custom Report</h3>
            <div class="pull-right">
            <a href="<?php echo base_url(); ?>manager/verticals/report/fixed/1" class="btn btn-danger">All Missed</a>
            <a href="<?php echo base_url(); ?>manager/verticals/report/fixed/2" class="btn btn-danger">Last 7 Days</a>
            <a href="<?php echo base_url(); ?>manager/verticals/report/fixed/3" class="btn btn-danger">Today</a>
            <a href="<?php echo base_url(); ?>manager/verticals/report/fixed/4" class="btn btn-danger">Next 7 Days</a>
            <a href="<?php echo base_url(); ?>manager/verticals/report/fixed/5" class="btn btn-danger">All Future</a>
            </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
          
             <div class="form-group col-md-6">
        <label> Lead Date </label>
        <div class="input-daterange input-group" id="datepicker" style="width: 100%;" >
    <input type="text" class="input-md form-control" name="d1" placeholder="From Date" value="<?php if(isset($_GET['d1'])&&(!empty($_GET['d1']))){ echo htmlspecialchars($_GET['d1'], ENT_QUOTES, 'UTF-8');}?>" autocomplete="off"/>
    <span class="input-group-addon">To</span>
    <input type="text" class="input-md form-control" name="d2" placeholder="To Date" value="<?php if(isset($_GET['d2'])&&(!empty($_GET['d2']))){ echo htmlspecialchars($_GET['d2'], ENT_QUOTES, 'UTF-8');}?>" autocomplete="off"/>

      </div>
     </div>   
        

         <div class="form-group col-md-6">
        <label> Followup Date </label>
        <div class="input-daterange input-group" id="datepicker" style="width: 100%;" >
    <input type="text" class="input-md form-control" name="f1" placeholder="From Date" value="<?php if(isset($_GET['f1'])&&(!empty($_GET['f1']))){ echo htmlspecialchars($_GET['f1'], ENT_QUOTES, 'UTF-8');}?>" autocomplete="off"/>
    <span class="input-group-addon">To</span>
    <input type="text" class="input-md form-control" name="f2" placeholder="To Date" value="<?php if(isset($_GET['f2'])&&(!empty($_GET['f2']))){ echo htmlspecialchars($_GET['f2'], ENT_QUOTES, 'UTF-8');}?>" autocomplete="off"/>

      </div>
     </div> 
        
        
        <div class="form-group col-md-3">
  
            <label>Category</label>

            <select class="form-control" style="width: 100%;" name="category">
              <option value="">All</option>                
                  <option <?php if(!empty($_GET['category']) && ($_GET['category']=='New')) { echo "selected"; } ?> value="New">New</option>
                  <option <?php if(!empty($_GET['category']) && ($_GET['category']=='Existing')) { echo "selected"; } ?> value="Existing">Existing</option>                  
                </select>

          </div>
    <div class="form-group col-md-3">
  
            <label>Lead Generator</label>

            <select class="form-control" name="created_by" style="width: 100%;">
            <option value="">All</option>
            <?php if(isset($u_list) && !empty($u_list))
              { 
                foreach($u_list as $li){
              ?>

              <option value="<?php echo $li['id']; ?>"  <?php if(!empty($_GET['created_by']) && ($_GET['created_by']==$li['id'])) { echo "selected"; } ?>><?php echo $li['name']; ?></option>          

              <?php } } ?>
            </select>

          </div>
         <div class="col-md-3">
         <div class="form-group">
       <label>Status</label>
       <select class="form-control" name="status">
                <option value="">All Status</option>

                  <?php if(isset($st_list) && !empty($st_list)){
                    foreach($st_list as $cli){
                  ?>
                  <option <?php if(!empty($_GET['status']) && ($_GET['status']==$li['id'])) { echo "selected"; } ?> value="<?php echo $cli['id']; ?>" ><?php echo $cli['status']; ?></option>
              <?php } }  ?>
            </select>
              </div>
      </div>
             <div class="col-md-3">
         <div class="form-group">
       <label>Service</label>
      <select class="form-control" style="width: 100%;" name="s_cat">
        <option value="">All</option>

                  <option <?php if(!empty($_GET['s_cat']) && ($_GET['s_cat']==$li['id'])) { echo "selected"; } ?> value="Telecom">Telecom</option>
                  <option <?php if(!empty($_GET['s_cat']) && ($_GET['s_cat']==$li['id'])) { echo "selected"; } ?> value="Accounting">Accounting</option>
                  <option <?php if(!empty($_GET['s_cat']) && ($_GET['s_cat']==$li['id'])) { echo "selected"; } ?> value="Other">Other</option>                   
                </select>
              </div>
      </div>
           
          
       </div>
        <div class="box-footer"> <div class="form-group col-md-12">

            <button type="submit" class="btn btn-md btn-primary" name="search" value="report">Submit</button>

            <button type="reset" class="btn btn-md btn-danger">Reset</button>

           </div>

         </div>
        </div>
  </form> 
 </div>
        <div class="col-md-12"> 
          <div class="box">
            <div class="box-header">
             <h4><?php echo $info['page_heading']; ?></h4>
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
                 <th>Customer</th>
                 <th>Product Type</th>
                 <th>MRR</th>
                 <th>POC Name</th>
                 <th>Contact </th>
                 <th>Next FU</th>
                 <th>Status</th>
               
                </tr>
              </thead>
              <tbody>
                <?php 
        
        foreach($list as $k=>$li){ ?>
        
                <tr>
                <td><?php echo $li['customer']; ?></td>
              <td><?php echo $li['p_cat']; ?></td>
               <td><?php echo $li['mrr']; ?></td>
               <td><?php echo $li['poc']; ?></td>
                 <td><?php echo $li['contact1']; ?></td>
                  <td><?php echo $li['followup_date']; ?></td>
                   <td><?php echo $li['status_name']; ?></td>
            
               
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
  fileName: "SRJ-lead-report-<?php echo date('d-m-Y'); ?>",
  bootstrap: true,                   // (Boolean), style buttons using bootstrap
    position: "top" ,                // (top, bottom), position of the caption element relative to tabl
    trimWhitespace: false 
   });
 
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
