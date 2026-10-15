<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<!-- Content Wrapper. Contains page content -->
<div class="container">
<div class="content-wrapper"> 
  
  <!-- Content Header (Page header) -->
  
  <section class="content-header">
    <h1> Leads </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i>Home</a></li>
      <li class="active">Leads</li>
    </ol>
  </section>
  
  <!-- Main content -->
  
  <section class="content"> 
    
    <!-- SELECT2 EXAMPLE -->
    
   
     <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> <?php echo get_message($this->session->flashdata('message'),'message'); ?>
     
      <form role="form" action="<?php echo base_url(); ?>manager/leads/add" method="post" name="add_form">
      
       <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Initial Category Fields</h3>
      </div>
      
      <!-- /.box-header -->
      
      <div class="box-body" >
      
        <div class="row">
         
          
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Customer Name<span>*</span></label>
                <input type="text" name="customer" class="form-control" style="text-transform: capitalize;" required="" >
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>POC Name</label>
                <input type="text" class="form-control" name="poc">
              </div>
            </div>
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>POC Contact Number</label>
                <input type="text" class="form-control" name="contact1">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Alternative Mobile Number</label>
                <input type="text" class="form-control" name="alt_mobile" placeholder="Alternative Mobile">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Alternative Contact Number</label>
                <input type="text" class="form-control" name="alt_contact" placeholder="Alternative Contact">
              </div>
            </div>

             <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" name="email">
              </div>
            </div>
           
           
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Account Number</label>
                <input type="text" class="form-control" name="a_number">
              </div>
            </div>
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Account Under Vertical</label>
                <input type="text" class="form-control" name="a_department">
              </div>
            </div>
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Current Monthly Revenue Amount</label>
                <input type="text" class="form-control only_number" name="r_amount">
              </div>
            </div>
              <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Current Monthly Revenue Date</label>
                <input type="text" class="form-control datepicker" name="r_date">
              </div>
            </div>

             <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Category</label>
                <select class="form-control" style="width: 100%;" name="category">
                <option value="">Select</option>
                  <option value="New" selected="selected">New</option>
                  <option value="Existing">Existing</option>                  
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Business Category</label>
                <select class="form-control" style="width: 100%;" name="business_category">
                  <option value="">Select Business Category</option>
                  <?php if(isset($business_categories) && !empty($business_categories)){
                    foreach($business_categories as $bcat){ ?>
                      <option value="<?php echo htmlspecialchars($bcat['title']); ?>"><?php echo htmlspecialchars($bcat['title']); ?></option>
                  <?php } } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>TNCR/TND</label>
                <select class="form-control" name="dnd_status">
                  <option value="No" selected="selected">No</option>
                  <option value="Yes">Yes</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>TNCR Check Date</label>
                <input type="text" class="form-control datepicker" name="dnd_check_date" placeholder="DD-MM-YYYY">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Service Category</label>
                <select class="form-control" style="width: 100%;" name="s_cat" id="pcatselect" onchange="getpSubCat()">
                <option value="">Select Category</option>
               
                  <?php if(isset($p_cat) && !empty($p_cat)){
                    foreach($p_cat as $cli){
                  ?>
                  <option value="<?php echo $cli['id']; ?>" ><?php echo $cli['title']; ?></option>
              <?php } }  ?>                 
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Product Category</label>
                <select class="form-control" style="width: 100%;" name="p_cat" id="psubcatselect">
                <option value="">Select</option>
                                              
                </select>
              </div>
            </div>
              <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Product Details</label>
                <input type="text" class="form-control" name="p_details"  >
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Trade License Number</label>
                <input type="text" class="form-control" name="tl_number" placeholder="Enter Trade License Number">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Trade License Authority</label>
                <input type="text" class="form-control" name="tl_authority" placeholder="Enter Trade License Authority">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Trade License Issue Date</label>
                <input type="text" class="form-control datepicker" name="tl_issue_date" placeholder="DD-MM-YYYY">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Trade License Expiry</label>
                <input type="text" class="form-control datepicker" name="tl_expire_date" placeholder="DD-MM-YYYY">
              </div>
            </div>

             <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Qty</label>
                <input type="text" class="form-control only_number" name="qty" >
              </div>
            </div>
             <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>MRC</label>
                <input type="text" name="mrc" class="form-control only_number" >
              </div>
            </div>
             <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Revenue</label>
                <input type="text" name="revenue" class="form-control only_number" >
              </div>
            </div>
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>MRR</label>
                <input type="text" name="mrr" class="form-control only_number" readonly >
              </div>
            </div>
            
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Location</label>
                <input type="text" class="form-control" name="location"  >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Status<span>*</span></label>
                <select class="form-control" required name="status">
                <option value="">Select Status</option>
               
                  <?php if(isset($st_list) && !empty($st_list)){
                    foreach($st_list as $cli){
                  ?>
                  <option value="<?php echo $cli['id']; ?>" ><?php echo $cli['status']; ?></option>
              <?php } }  ?>
            </select>
              </div>
            </div>
           
             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Exp. Close Date</label>
                <input type="text" class="form-control datepicker2" name="close_date" id="close_date"  >
              </div>
            </div>

            <div class="col-md-12">
              <div class="form-group">
                <label>Remarks</label>
                <textarea class="form-control" name="remark"></textarea>
              </div>
            </div>

          </div>
        </div>
      </div>

       <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">More Category Field</h3>
      </div>
      
      <!-- /.box-header -->
      
      <div class="box-body" >
      
        <div class="row">


           <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" class="form-control" />
              </div>
            </div>

           
           

           <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Country</label>
                <select id="country" class="form-control" name="country" onchange="get_state()">                          
                <option value="">Select</option>
                            <?php if(isset($c_list) && !empty($c_list)){
                              foreach($c_list as $cli){
                            ?>
                            <option value="<?php echo $cli['id']; ?>" ><?php echo $cli['name']; ?></option>
                        <?php } }  ?>
                      </select>
              </div>
            </div>


            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>State / Province / Emirate</label>
                 <select id="state" class="form-control" name="state">
                 <option value="">Select</option> 
                 </select>
                
              </div>
            </div>

             <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>City</label>
                <input type="text" class="form-control" name="city"  >
              </div>
            </div>

             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Alternate POC Name</label>
                <input type="text" class="form-control" name="poc2">
              </div>
            </div>
             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Alternate POC Contact Number</label>
                <input type="text" class="form-control" name="contact2">
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Channel partner</label>
               
                <select class="form-control" style="width: 100%;" name="c_partner">
                  <option value="SRJ Electronics Trading LLC" selected="selected">SRJ Electronics Trading LLC</option>
                                    
                </select>
              </div>
            </div>

             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>One time charges</label>
                <input type="text" class="form-control" name="otc"  >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Lead Category</label>
               
                 <select class="form-control" style="width: 100%;" name="lead_cat" >
                <option value="">Select Category</option>
               
                  <?php if(isset($lc_list) && !empty($lc_list)){
                    foreach($lc_list as $cli){
                  ?>
                  <option value="<?php echo $cli['id']; ?>" ><?php echo $cli['title']; ?></option>
              <?php } }  ?>                 
                </select>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Lead Assigned To</label>
                <?php 
                  $cur_role = $this->session->userdata('manager_role');
                  if ($cur_role == 1 || $cur_role == 4) { 
                ?>               
                  <select class="form-control select2" name="lead_assign" style="width: 100%;">
                    <option value="0">Unassigned (Distribute Later)</option>
                    <?php if(isset($u_list) && !empty($u_list)) { 
                      foreach($u_list as $li){
                    ?>
                      <option value="<?php echo $li['id']; ?>" <?php if($li['id'] == $this->session->userdata('manager_id')){ echo 'selected'; } ?>><?php echo $li['name']; ?></option>          
                    <?php } } ?>
                  </select>
                <?php } else { ?>
                  <input type="text" class="form-control" value="<?php echo htmlspecialchars($this->session->userdata('manager_name')); ?>" readonly style="background: #f8fafc; font-weight: 600;">
                  <input type="hidden" name="lead_assign" value="<?php echo (int)$this->session->userdata('manager_id'); ?>">
                <?php } ?>
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Website / Source Link</label>
                <input type="text" class="form-control" name="lead_source"  >
              </div>
            </div>
           
        
        </div>
      </div>
        
        <!-- /.row --> 
        
      </div>


      <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Followup Field</h3>
      </div>
      
      <!-- /.box-header -->
      
      <div class="box-body" >
      
        <div class="row">

            <div class="col-lg-2 col-sm-6">
              <div class="form-group">
                <label>Next Follow Up Date</label>
                <input type="text" class="form-control datepicker2" name="followup_date" id="followup_date"  >
              </div>
            </div>

            <div class="col-lg-2 col-sm-6">
              <div class="form-group">
                <label>Next Follow Up Time</label>
               
                <div class="input-group bootstrap-timepicker timepicker">
                  <input  type="text" class="form-control input-small timepicker" name="followup_time">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                </div>
              </div>
            </div>

            <div class="col-lg-8 col-sm-6">
              <div class="form-group">
                <label>Remark</label>
                <input type="text" class="form-control" name="followup_remark"  >
              </div>
            </div>

        </div>
        </div>
        </div>


         <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Meeting Field</h3>
      </div>
      
      <!-- /.box-header -->
      
      <div class="box-body" >

      <div class="row remark_row">
          <div class="col-md-7">

        <div class="row">

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Next Meeting Date</label>
                <input type="text" class="form-control datepicker2" name="m_date"  >
              </div>
            </div>
            
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Next Meeting Time</label>
               
                <div class="input-group bootstrap-timepicker timepicker">
                  <input  type="text" class="form-control input-small timepicker" name="m_time">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                </div>
              </div>
            </div>

               <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>POC</label>
                <input type="text" class="form-control" name="m_poc"  >
              </div>
            </div>

		</div>
        <div class="row">
        <!--    <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Address</label>
                <input type="text" class="form-control" name="m_address"  >
              </div>
            </div> -->


            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Attendee 1</label>
                <select class="form-control select2" name="a1" style="width: 100%;">
                <option value="">Select</option>
                <?php if(isset($u_list) && !empty($u_list))
                  { 
                    foreach($u_list as $li){
                  ?>

                  <option value="<?php echo $li['id']; ?>"><?php echo $li['name']; ?></option>          

               <?php } } ?>
                </select>


              </div>
            </div>


            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Attendee 2</label>

                <select class="form-control select2" name="a2" style="width: 100%;">
                <option value="">Select</option>
                <?php if(isset($u_list) && !empty($u_list))
                  { 
                    foreach($u_list as $li){
                  ?>

                  <option value="<?php echo $li['id']; ?>"><?php echo $li['name']; ?></option>          

               <?php } } ?>
                </select>

              </div>
            </div>


            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Mobile</label>
                <input type="text" class="form-control" name="m_mobile"  >
              </div>
            </div>


        </div>



          </div>

          <div class="col-md-5">

          <div class="form-group remark-group">
                <label>Remark</label>
                <textarea type="text" class="form-control" name="m_remark"></textarea>
              </div>
          </div>

      </div>
      
        </div>
        </div>


         <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Vertical Selection</h3>
      </div>
      
      <!-- /.box-header -->
      
      <div class="box-body" >
      
        <div class="row">
       
          <div class="col-lg-2 col-sm-6">
              <div class="form-group">
                <label>Select Vertical</label>
                <select class="form-control select2" name="vertical_id" style="width: 100%;">
                <option value="">Select</option>
                <?php if(isset($v_list) && !empty($v_list))
                  { 
                    foreach($v_list as $li){
                  ?>

                  <option value="<?php echo $li['id']; ?>"><?php echo $li['poc']; ?>- <?php echo $li['company_name']; ?></option>          

               <?php } } ?>
            </select>
              </div>
            </div>

          <div class="col-lg-2 col-sm-6">
              <div class="form-group">
                <label>Amount to Pay</label>
                <input type="text" class="form-control only_number" name="v_amount"  >
              </div>
            </div>

            <div class="col-lg-8 col-sm-6">
              <div class="form-group">
                <label>Remark</label>
                <input type="text" class="form-control only_number" name="v_remark"  >
              </div>
            </div>
           

        </div>
        </div>
        </div>

      <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Back Office Update</h3>
      </div>
      
      <!-- /.box-header -->
      
      <div class="box-body" >
      
        <div class="row">

          <table class="table tale-border">
            <tr>
              <th>Category</th>
              <th>Date</th>
              <th>Status</th>
              <th>Reference</th>
              <th>Remarks</th>
            </tr>
            <tr>
              <td><input type="text" class="form-control" name="bo_cat[]" value="SPR" readonly=""></td>
              <td><input type="text" class="form-control datepicker" name="bo_date[]"  ></td>
              <td>
                <select class="form-control" style="width: 100%;" name="bo_status[]">
                  <option value="Not Applicable" selected="selected">Not Applicable</option>
                  <option value="Pending Approval">Pending Approval</option>
                  <option value="Query">Query</option>
                  <option value="Approved">Approved</option> 
                  <option value="Expired">Expired</option> 
                  <option value="Other">Other</option>                    
                </select>
              </td>
              <td><input type="text" class="form-control" name="bo_ref[]" ></td>
              <td><input type="text" class="form-control" name="bo_remark[]" ></td>
            </tr>

             <tr>
              <td><input type="text" class="form-control" name="bo_cat[]" value="Company Code" readonly=""></td>
              <td><input type="text" class="form-control datepicker" name="bo_date[]"  ></td>
              <td colspan="3">
                <select style="display:none;" class="form-control" style="width: 100%;" name="bo_status[]">
                  <option value="">Not Applicable</option>                                    
                </select>
             <input type="hidden" class="form-control" name="bo_ref[]" >
             <input type="text" class="form-control" name="bo_remark[]" ></td>
            </tr>

             <tr>
              <td><input type="text" class="form-control" name="bo_cat[]" value="Lead Activity" readonly=""></td>
              <td><input type="text" class="form-control datepicker" name="bo_date[]"  ></td>
              <td>
                <select class="form-control" style="width: 100%;" name="bo_status[]">
                  <option value="Not Applicable" selected="selected">Not Applicable</option>
                  <option value="Pending Approval">Pending Approval</option>
                  <option value="Query">Query</option>
                  <option value="Approved">Approved</option> 
                  <option value="Expired">Expired</option> 
                  <option value="Other">Other</option>                    
                </select>
              </td>
              <td><input type="text" class="form-control" name="bo_ref[]" ></td>
              <td><input type="text" class="form-control" name="bo_remark[]" ></td>
            </tr>

             <tr>
              <td><input type="text" class="form-control" name="bo_cat[]" value="Document Col/Sub" readonly=""></td>
              <td><input type="text" class="form-control datepicker" name="bo_date[]"  ></td>
              <td>
               <select class="form-control" style="width: 100%;" name="bo_status[]">
                  <option value="Not Applicable" selected="selected">Not Applicable</option>
                  <option value="Pending Approval">Pending Approval</option>
                  <option value="Query">Query</option>
                  <option value="Approved">Approved</option> 
                  <option value="Expired">Expired</option> 
                  <option value="Other">Other</option>                    
                </select>
              </td>
              <td><input type="text" class="form-control" name="bo_ref[]" ></td>
              <td><input type="text" class="form-control" name="bo_remark[]" ></td>
            </tr>

             <tr>
              <td><input type="text" class="form-control" name="bo_cat[]" value="Pending Documents" readonly=""></td>
              <td><input type="text" class="form-control datepicker" name="bo_date[]"  ></td>
              <td colspan="3">
                <select style="display:none;" class="form-control" style="width: 100%;" name="bo_status[]">
                  <option value="">Not Applicable</option>                                    
                </select>
             <input type="hidden" class="form-control" name="bo_ref[]" >
             <input type="text" class="form-control" name="bo_remark[]" ></td>
            </tr>

             <tr>
              <td><input type="text" class="form-control" name="bo_cat[]" value="Video Verification" readonly=""></td>
              <td><input type="text" class="form-control datepicker" name="bo_date[]"  ></td>
              <td>
                <select class="form-control" style="width: 100%;" name="bo_status[]">
                  <option value="Not Applicable" selected="selected">Not Applicable</option>
                  <option value="Pending Approval">Pending Approval</option>
                  <option value="Query">Query</option>
                  <option value="Approved">Approved</option> 
                  <option value="Expired">Expired</option> 
                  <option value="Other">Other</option>                    
                </select>
              </td>
              <td><input type="text" class="form-control" name="bo_ref[]" ></td>
              <td><input type="text" class="form-control" name="bo_remark[]" ></td>
            </tr>

             <tr>
              <td><input type="text" class="form-control" name="bo_cat[]" value="Post Verification" readonly=""></td>
              <td><input type="text" class="form-control datepicker" name="bo_date[]"  ></td>
              <td>
                <select class="form-control" style="width: 100%;" name="bo_status[]">
                  <option value="Not Applicable" selected="selected">Not Applicable</option>
                  <option value="Pending Approval">Pending Approval</option>
                  <option value="Query">Query</option>
                  <option value="Approved">Approved</option> 
                  <option value="Expired">Expired</option> 
                  <option value="Other">Other</option>                    
                </select>
              </td>
              <td><input type="text" class="form-control" name="bo_ref[]" ></td>
              <td><input type="text" class="form-control" name="bo_remark[]" ></td>
            </tr>
          </table>

        </div>
      </div>
    </div>
      
      <div class="box-footer text-center">
                <button id="add_btn" type="submit" class="btn btn-info">Submit</button>
                <button type="reset" class="btn btn-default">Cancel</button>
              </div>
              
         
              
     </form>
  
    
    <!-- /.box --> 
    
  </section>
  
  <!-- /.content --> 
  
</div>

<!-- /.content-wrapper --> 
</div>
<script>







 $(document).ready(function () { 

  $(document).on("click", "#add_btn", function () {

    

   function checkCloseDateValidity() {
     var fu = $('#followup_date').val();
     var cd = $('#close_date').val();
     if (fu && cd) {
       var fuP = fu.split('-');
       var cdP = cd.split('-');
       if (fuP.length === 3 && cdP.length === 3) {
         var fuDate = new Date(fuP[2], fuP[1] - 1, fuP[0]);
         var cdDate = new Date(cdP[2], cdP[1] - 1, cdP[0]);
         if (cdDate < fuDate) {
           alert('Exp. Close Date cannot be previous or past of the Follow Up Date (' + fu + ')!');
           $('#close_date').val(fu);
           return false;
         }
       }
     }
     return true;
   }

   $('#close_date, #followup_date').on('change changeDate', function() {
     checkCloseDateValidity();
   });

   $('form[name=add_form]').validate({         
     submitHandler: function(form) {
       if (!checkCloseDateValidity()) {
         return false;
       }
       form.submit();
     }
   });

 });


var input = $('[name="mrc"],[name="revenue"]'),
    input1 = $('[name="mrc"]'),
    input2 = $('[name="revenue"]'),
    input3 = $('[name="mrr"]');
input.change(function () {
    input3.val((parseInt(input1.val()) || 0) + (parseInt(input2.val()) || 0));
});



});




</script>
<script>

   function get_state()
{
  
      var stateID = $("#country").val();     
        $.ajax({
              type: "POST",
        data: 'c_id='+stateID,
          url: "<?php echo base_url(); ?>" + "ci_admin_user/get_state",
        success:function(result){
        if(result)
        {
        $('#state').html(result);
        }
        else
        {
        alert('error');
        }
        }
          });


}

function check_cname()
{
  
      var c_name =$(".c_username").val();     
        $.ajax({
              type: "POST",
        data: 'c_name='+c_name,
          url: "<?php echo base_url(); ?>" + "ci_admin_user/check_name",
        success:function(data){
        if(data=='done')
        {
        $('.alert_msg').text('');
        }
        else
        {
        $('.c_username').val('');
        $('.alert_msg').text('Company name already exist.');
        }
        }
          });


}

function getpSubCat()
{  
        var stateID = $("#pcatselect").val();     
        $.ajax({
          type: "POST",
          data: 'c_id='+stateID,
          url: "<?php echo base_url(); ?>" + "ci_admin_terms/getSubCatAjax",
          success:function(result){
            if(result){
              $('#psubcatselect').html(result);
            }           
        }
        });
}
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
