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
     
     <div class="my-profile">
<div class="box box-default">
      <div class="box-header">
		<div class="col-sm-12">
        <h3 class="box-title" style="text-transform:capitalize"><b>
          <?php if((isset($slist['customer']))&&(!empty($slist['customer']))){echo $slist['customer'];}else echo ""; ?>
          </b></h3>    
          </div>
          </div>
<div class="box-body" >
      
        <div class="row">
  <div class="col-md-6">
      <div class="table-responsive border">
          <table class="table">
        
              <tr>
                <th scope="col">Lead No.</th>
                <td><?php echo $slist['lead_id']; ?></td>

                
              </tr>
     <tr>
              <th scope="col">Lead Assigned To   </th>
              <td scope="col"><?php echo $slist['assigned_name']; ?></td>              
            
              </tr>
            

          </table>
      </div>
 </div>

  <div class="col-md-6">
      <div class="table-responsive border">
          <table class="table">
          
				
              <tr>
              <th scope="col">Created By </th>
              <td scope="col"><?php echo $slist['created_name']; ?></td>              
            
              </tr>

        
              <tr>
              <th scope="col"> Created At  </th>
              <td scope="col"><?php echo $slist['created']; ?></td>              
            
              </tr>






          </table>
      </div>
  </div>

</div>
</div>
</div>
</div>
     
      <form role="form" action="<?php echo base_url(); ?>manager/leads/edit/<?php echo base64_encode($list['id']); ?>/<?php echo $action; ?>" method="post" name="add_form">
      
       <div class="box box-default">
      <div class="box-header with-border pt-3">
        <h3 class="box-title">Initial Category Fields</h3>
      </div>
      
      <!-- /.box-header -->
      
      <div class="box-body" >
      
        <div class="row">
         
          
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Customer Name<span>*</span></label>
                <input type="text" name="customer" class="form-control" style="text-transform: capitalize;" required="" value="<?php echo $list['customer']; ?>" >
              </div>
            </div>

             <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>POC Name</label>
                <input type="text" class="form-control" name="poc" value="<?php echo $list['poc']; ?>" >
              </div>
            </div>
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>POC Contact Number<span>*</span></label>
                <input type="text" required class="form-control" name="contact1" value="<?php echo $list['contact1']; ?>" >
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Alternative Contact Number</label>
                <input type="text" class="form-control" name="alt_contact" value="<?php echo !empty($list['alt_contact']) ? htmlspecialchars($list['alt_contact']) : (isset($list['alt_mobile']) ? htmlspecialchars($list['alt_mobile']) : ''); ?>" placeholder="Alternative Contact Number">
              </div>
            </div>

             <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" name="email" value="<?php echo $list['email']; ?>">
              </div>
            </div>
           
           

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Account Number</label>
                <input type="text" class="form-control" name="a_number" value="<?php echo $list['a_number']; ?>" >
              </div>
            </div>
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Account Under Vertical</label>
                <input type="text" class="form-control" name="a_department" value="<?php echo $list['a_department']; ?>" >
              </div>
            </div>
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Current Monthly Revenue Amount</label>
                <input type="text" class="form-control" name="r_amount" value="<?php echo $list['r_amount']; ?>" >
              </div>
            </div>
              <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Current Monthly Revenue Date</label>
                <input type="text" class="form-control datepicker" name="r_date" value="<?php echo $list['r_date']; ?>" >
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Category</label>
                <select class="form-control" style="width: 100%;" name="category">
                  <option <?php if($list['category']=='New'){ echo "selected"; } ?> value="New" selected="selected">New</option>
                  <option <?php if($list['category']=='Existing'){ echo "selected"; } ?> value="Existing">Existing</option>                  
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Industry Category</label>
                <select class="form-control" style="width: 100%;" name="industry_category">
                  <option value="">Select Industry Category</option>
                  <?php 
                    $cur_ind = !empty($list['industry_category']) ? $list['industry_category'] : (!empty($list['business_category']) ? $list['business_category'] : '');
                    if(isset($business_categories) && !empty($business_categories)){
                      foreach($business_categories as $bcat){ 
                        $sel = ($cur_ind == $bcat['title']) ? 'selected' : '';
                      ?>
                        <option value="<?php echo htmlspecialchars($bcat['title']); ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($bcat['title']); ?></option>
                  <?php } } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Corporate Tax Status</label>
                <select class="form-control" style="width: 100%;" name="corporate_tax_status">
                  <?php $cur_ct = isset($list['corporate_tax_status']) ? $list['corporate_tax_status'] : 'Not Registered'; ?>
                  <option value="Not Registered" <?php if($cur_ct == 'Not Registered') echo 'selected'; ?>>Not Registered</option>
                  <option value="Registered" <?php if($cur_ct == 'Registered') echo 'selected'; ?>>Registered</option>
                  <option value="Not Applicable" <?php if($cur_ct == 'Not Applicable') echo 'selected'; ?>>Not Applicable</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>VAT Status</label>
                <select class="form-control" style="width: 100%;" name="vat_status">
                  <?php $cur_vat = isset($list['vat_status']) ? $list['vat_status'] : 'Not Registered'; ?>
                  <option value="Not Registered" <?php if($cur_vat == 'Not Registered') echo 'selected'; ?>>Not Registered</option>
                  <option value="Registered" <?php if($cur_vat == 'Registered') echo 'selected'; ?>>Registered</option>
                  <option value="Not Applicable" <?php if($cur_vat == 'Not Applicable') echo 'selected'; ?>>Not Applicable</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>DNCR/DND</label>
                <select class="form-control" name="dnd_status">
                  <option value="No" <?php echo (isset($list['dnd_status']) && $list['dnd_status'] == 'No') ? 'selected' : ''; ?>>No</option>
                  <option value="Yes" <?php echo (isset($list['dnd_status']) && $list['dnd_status'] == 'Yes') ? 'selected' : ''; ?>>Yes</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>DNCR Date</label>
                <input type="text" class="form-control datepicker" name="dnd_check_date" value="<?php echo (!empty($list['dnd_check_date']) && $list['dnd_check_date'] != '0000-00-00') ? date('d-m-Y', strtotime($list['dnd_check_date'])) : ''; ?>" placeholder="DD-MM-YYYY">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Service Category<span>*</span></label>
                <select class="form-control" required style="width: 100%;" name="s_cat" id="pcatselect" onchange="getpSubCat()">
                <option value="">Select Category</option>
             
             <?php if(isset($p_cat) && !empty($p_cat)){
               foreach($p_cat as $cli){
             ?>
             <option <?php if($list['s_cat']==$cli['id']){ echo "selected"; } ?> value="<?php echo $cli['id']; ?>" ><?php echo $cli['title']; ?></option>
         <?php } }  ?>                 
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Product Category</label>
                <select class="form-control"  style="width: 100%;" name="p_cat" id="psubcatselect">
                <option value="">Choose Sub Category</option>
              <?php if(isset($scat_list) && !empty($scat_list)){
                  foreach($scat_list as $cli){
                ?>
                <option <?php if($list['p_cat']==$cli['id']){ echo "selected"; } ?> value="<?php echo $cli['id']; ?>" ><?php echo $cli['title']; ?></option>
            <?php } }  ?>                             
                </select>
              </div>
            </div>
              <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Product Details</label>
                <input type="text" class="form-control" name="p_details" value="<?php echo $list['p_details']; ?>" >
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Trade License Number</label>
                <input type="text" class="form-control" name="tl_number" value="<?php echo isset($list['tl_number']) ? $list['tl_number'] : ''; ?>" placeholder="Enter Trade License Number">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Trade License Authority</label>
                <input type="text" class="form-control" name="tl_authority" value="<?php echo isset($list['tl_authority']) ? $list['tl_authority'] : ''; ?>" placeholder="Enter Trade License Authority">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Trade License Issue Date</label>
                <input type="text" class="form-control datepicker" name="tl_issue_date" value="<?php echo (!empty($list['tl_issue_date']) && $list['tl_issue_date'] != '0000-00-00') ? date('d-m-Y', strtotime($list['tl_issue_date'])) : ''; ?>" placeholder="DD-MM-YYYY">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Trade License Expiry</label>
                <input type="text" class="form-control datepicker" name="tl_expire_date" value="<?php echo (!empty($list['tl_expire_date']) && $list['tl_expire_date'] != '0000-00-00') ? date('d-m-Y', strtotime($list['tl_expire_date'])) : ''; ?>" placeholder="DD-MM-YYYY">
              </div>
            </div>

             <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Qty</label>
                <input type="text" class="form-control" name="qty" value="<?php echo $list['qty']; ?>">
              </div>
            </div>
             <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>MRC</label>
                <input type="text" name="mrc" class="form-control" value="<?php echo $list['mrc']; ?>">
              </div>
            </div>
             <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Revenue</label>
                <input type="text" name="revenue" class="form-control" value="<?php echo $list['revenue']; ?>">
              </div>
            </div>
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>MRR</label>
                <input type="text" name="mrr" class="form-control" value="<?php echo $list['mrr']; ?>" readonly>
              </div>
            </div>
           
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Location</label>
                <input type="text" class="form-control" name="location" value="<?php echo htmlspecialchars($list['location']); ?>">
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Quote Status</label>
                <?php $cur_qs = isset($list['quote_status']) ? $list['quote_status'] : 'Not Given'; ?>
                <select class="form-control" style="width: 100%;" name="quote_status">
                  <option value="Not Given" <?php if($cur_qs == 'Not Given') echo 'selected'; ?>>Not Given</option>
                  <option value="Quote Given" <?php if($cur_qs == 'Quote Given') echo 'selected'; ?>>Quote Given</option>
                  <option value="Proposal Sent" <?php if($cur_qs == 'Proposal Sent') echo 'selected'; ?>>Proposal Sent</option>
                  <option value="Under Review" <?php if($cur_qs == 'Under Review') echo 'selected'; ?>>Under Review</option>
                  <option value="Revised Quote" <?php if($cur_qs == 'Revised Quote') echo 'selected'; ?>>Revised Quote</option>
                  <option value="Accepted" <?php if($cur_qs == 'Accepted') echo 'selected'; ?>>Accepted</option>
                  <option value="Rejected" <?php if($cur_qs == 'Rejected') echo 'selected'; ?>>Rejected</option>
                </select>
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Quote Mode</label>
                <?php $cur_qm = isset($list['quote_mode']) ? $list['quote_mode'] : ''; ?>
                <select class="form-control" style="width: 100%;" name="quote_mode">
                  <option value="">Select Quote Mode</option>
                  <option value="Email" <?php if($cur_qm == 'Email') echo 'selected'; ?>>Email</option>
                  <option value="WhatsApp" <?php if($cur_qm == 'WhatsApp') echo 'selected'; ?>>WhatsApp</option>
                  <option value="SMS/Text" <?php if($cur_qm == 'SMS/Text') echo 'selected'; ?>>SMS/Text</option>
                  <option value="Phone Call/Verbal" <?php if($cur_qm == 'Phone Call/Verbal') echo 'selected'; ?>>Phone Call/Verbal</option>
                  <option value="Other" <?php if($cur_qm == 'Other') echo 'selected'; ?>>Other</option>
                </select>
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Quote Amount</label>
                <input type="number" step="0.01" class="form-control" name="quote_amount" value="<?php echo isset($list['quote_amount']) ? htmlspecialchars($list['quote_amount']) : ''; ?>" placeholder="0.00">
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Status<span>*</span></label>
                <select class="form-control" required name="status" id="lead_status_select">
                  <option value="">Select Status</option>
                  <?php if(isset($st_list) && !empty($st_list)){
                    foreach($st_list as $cli){ ?>
                      <option <?php if($list['status']==$cli['id']){ echo "selected"; } ?> value="<?php echo $cli['id']; ?>"><?php echo $cli['status']; ?></option>
                  <?php } } ?>
                </select>
              </div>
            </div>

            <?php 
              $existing_status_cat = !empty($list['status_category']) ? $list['status_category'] : (!empty($list['lost_reason']) ? $list['lost_reason'] : '');
            ?>
            <div class="col-lg-4 col-sm-6" id="status_category_col">
              <div class="form-group">
                <label>Status Category</label>
                <select class="form-control" id="status_category_dropdown" style="margin-bottom: 5px;">
                  <option value="">Select Status Category</option>
                </select>
                <input type="text" class="form-control" name="status_category" id="status_category_input" placeholder="Enter custom status category..." value="<?php echo htmlspecialchars($existing_status_cat); ?>" style="display:none;">
                <input type="hidden" name="lost_reason" id="hidden_lost_reason" value="<?php echo htmlspecialchars($existing_status_cat); ?>">
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Status Remarks</label>
                <input type="text" class="form-control" name="status_remarks" value="<?php echo isset($list['status_remarks']) ? htmlspecialchars($list['status_remarks']) : ''; ?>" placeholder="Enter status remarks / explanation">
              </div>
            </div>
            <?php /* <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Next Follow Up Date</label>
                <input type="text" class="form-control datepicker" name="followup_date" value="<?php echo $list['followup_date']; ?>"  >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Next Follow Up Time</label>               

                <div class="input-group bootstrap-timepicker timepicker">
                  <input  type="text" class="form-control input-small timepicker" name="followup_time" value="<?php echo $list['followup_time']; ?>">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                </div>

              </div>
            </div> */ ?>
             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Exp. Close Date</label>
                <input type="text" class="form-control datepicker2" name="close_date" id="close_date" value="<?php echo (!empty($list['close_date']) && $list['close_date'] != '0000-00-00') ? date('d-m-Y', strtotime($list['close_date'])) : '';  ?>" >
              </div>
            </div>

            <div class="col-md-12">
              <div class="form-group">
                <label>Remarks</label>
                <textarea class="form-control" name="remark"><?php echo $list['remark']; ?></textarea>
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
                <input type="text" name="address" class="form-control" value="<?php echo $list['address']; ?>" />
              </div>
            </div>

           
           

           <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Country</label>
                <select id="country" class="form-control" name="country" onchange="get_state()">                          

                            <?php if(isset($c_list) && !empty($c_list)){
                              foreach($c_list as $cli){
                            ?>
                            <option <?php if($list['country']==$cli['id']){ echo "selected"; } ?> value="<?php echo $cli['id']; ?>" ><?php echo $cli['name']; ?></option>
                        <?php } }  ?>
                      </select>
              </div>
            </div>


            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>State / Province / Emirate</label>
                 <select id="state" class="form-control" name="state">
                 <option value="">Select</option> 
                 <?php if(isset($state_list) && !empty($state_list)){
                              foreach($state_list as $cli){
                            ?>
                            <option <?php if($list['state']==$cli['id']){ echo "selected"; } ?> value="<?php echo $cli['id']; ?>" ><?php echo $cli['name']; ?></option>
                        <?php } }  ?>
                 </select>
                
              </div>
              
            </div>

             <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>City</label>
                <input type="text" class="form-control" name="city" value="<?php echo $list['city']; ?>"  >
              </div>
            </div>

             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Alternate POC Name</label>
                <input type="text" class="form-control" name="poc2" value="<?php echo $list['poc2']; ?>">
              </div>
            </div>
             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Alternate POC Contact Number</label>
                <input type="text" class="form-control" name="contact2" value="<?php echo $list['contact2']; ?>">
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
                <input type="text" class="form-control" name="otc"  value="<?php echo $list['otc']; ?>" >
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
                  <option <?php if($list['lead_cat']==$cli['id']){ echo "selected"; } ?> value="<?php echo $cli['id']; ?>" ><?php echo $cli['title']; ?></option>
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
                    <option value="0">Unassigned (Not Assigned to Anyone)</option>
                    <?php if(isset($u_list) && !empty($u_list)) { 
                      foreach($u_list as $li){
                    ?>
                      <option <?php if($list['lead_assign']==$li['id']){ echo "selected"; } ?> value="<?php echo $li['id']; ?>"><?php echo $li['name']; ?></option>          
                    <?php } } ?>
                  </select>
                <?php } else { 
                  $assigned_person_name = 'Unassigned';
                  if (!empty($list['lead_assign'])) {
                    $ap_row = $this->db->select('name')->get_where('ci_user', array('id' => $list['lead_assign']))->row_array();
                    if (!empty($ap_row['name'])) $assigned_person_name = $ap_row['name'];
                  }
                ?>
                  <input type="text" class="form-control" value="<?php echo htmlspecialchars($assigned_person_name); ?>" readonly style="background: #f8fafc; font-weight: 600;">
                  <input type="hidden" name="lead_assign" value="<?php echo (int)$list['lead_assign']; ?>">
                <?php } ?>
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Website / Source Link</label>
                <input type="text" class="form-control" name="lead_source" value="<?php echo htmlspecialchars($list['lead_source']); ?>">
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Data Source</label>
                <select class="form-control" style="width: 100%;" name="data_source">
                  <option value="">Select Data Source</option>
                  <?php 
                    $avail_sources = array();
                    if(isset($data_sources) && !empty($data_sources)){
                      foreach($data_sources as $ds_item){
                        $avail_sources[] = $ds_item['title'];
                      }
                    }
                    if(empty($avail_sources)){
                      $avail_sources = array('Google Maps', 'GPS', 'Referral', 'Website', 'Social Media', 'Other');
                    }
                    $cur_src = isset($list['data_source']) ? $list['data_source'] : '';
                    if(!empty($cur_src) && !in_array($cur_src, $avail_sources)){
                      $avail_sources[] = $cur_src;
                    }
                    foreach($avail_sources as $src_opt){ 
                      $sel = ($cur_src == $src_opt) ? 'selected' : '';
                    ?>
                      <option value="<?php echo htmlspecialchars($src_opt); ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($src_opt); ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Data Batch Referral Number</label>
                <input type="text" class="form-control" name="data_batch_referral_no" value="<?php echo isset($list['data_batch_referral_no']) ? htmlspecialchars($list['data_batch_referral_no']) : ''; ?>" placeholder="Enter Data Batch / Referral No">
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Database Category</label>
                <select class="form-control" style="width: 100%;" name="database_category">
                  <option value="">Select Database Category</option>
                  <?php 
                    $avail_db_cats = array();
                    if(isset($database_categories) && !empty($database_categories)){
                      foreach($database_categories as $dc_item){
                        $avail_db_cats[] = $dc_item['title'];
                      }
                    }
                    if(empty($avail_db_cats)){
                      $avail_db_cats = array('New Database', 'Online', 'Reference', 'Ported Out', 'Competitor DB', 'Contract Agent', 'Raw / Unverified', 'Cleansed', 'High Value', 'Re-engaged', 'Other');
                    }
                    $cur_db_cat = isset($list['database_category']) ? $list['database_category'] : '';
                    if(!empty($cur_db_cat) && !in_array($cur_db_cat, $avail_db_cats)){
                      $avail_db_cats[] = $cur_db_cat;
                    }
                    foreach($avail_db_cats as $db_opt){ 
                      $sel = ($cur_db_cat == $db_opt) ? 'selected' : '';
                    ?>
                      <option value="<?php echo htmlspecialchars($db_opt); ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($db_opt); ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

            
            
           
           
        
        </div>
        
        <!-- /.row --> 
        
      </div>

    </div>


    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Followup Update</h3>
      </div>
      
      <!-- /.box-header -->
      
      <div class="box-body" >
      
        <div class="row">

        
            <?php if(!empty($follow_list)){ 
                foreach($follow_list as $li){
              ?>
           <div class="col-lg-2 col-sm-6">
              <div class="form-group">
                <label>Date</label>
                <input type="hidden" name="flead_id[]" value="<?php echo $li['id']; ?>">
                <input type="text" class="form-control" name="followup_date[]" value="<?php echo simple_date($li['followup_date']); ?>" readonly="">
                </div>
            </div>

              <div class="col-lg-2 col-sm-6">
              <div class="form-group">
                <label>Time</label>              
                <input type="text" class="form-control" name="followup_time[]" value="<?php echo $li['followup_time']; ?>" readonly="">
                </div>
            </div>

              <div class="col-lg-8 col-sm-6">
              <div class="form-group">
                <label>Remark</label>               
                <input type="text" class="form-control" name="followup_remark[]" value="<?php echo $li['followup_remark']; ?>" >
                </div>
            </div>
            
           
          <?php } } ?>

            <div class="col-lg-2 col-sm-6">
              <div class="form-group">
                <label>Date</label>
                <input type="hidden" name="flead_id[]" value="0">
                <input type="text" class="form-control datepicker2" name="followup_date[]">
                </div>
            </div>

               <div class="col-lg-2 col-sm-6">
              <div class="form-group">
                <label>Time</label>
              <div class="input-group bootstrap-timepicker timepicker">              
                <input type="text" class="form-control input-small timepicker" name="followup_time[]">
                <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                </div>
                </div>
            </div>

               <div class="col-lg-8 col-sm-6">
              <div class="form-group">
                <label>Remark</label>             
                <input type="text" class="form-control" name="followup_remark[]">
                </div>
            </div>
          

        </div>
      </div>
    </div>


     <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Meeting Update</h3>
      </div>
      
      <!-- /.box-header -->
      
      <div class="box-body" >

        <?php if(!empty($meeting_list)){ 
          foreach($meeting_list as $lim){
        ?>

        <div class="row remark_row">
          <div class="col-md-7">

        <div class="row">

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Date</label>
                <input type="hidden" name="mlead_id[]" value="<?php echo $li['id']; ?>">
                <input type="text" class="form-control" name="m_date[]" value="<?php echo simple_date($lim['m_date']); ?>" readonly="">
              </div>
            </div>
            
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Time</label>
                <input type="text" class="form-control" name="m_time[]" value="<?php echo $lim['m_time']; ?>" readonly="">
              </div>
            </div>

               <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>POC</label>
                <input type="text" class="form-control" name="m_poc[]" value="<?php echo $lim['m_poc']; ?>" readonly="" >
              </div>
            </div>
            


        <!--    <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Address</label>
                <input type="text" class="form-control" name="m_address"  >
              </div>
            </div> -->


            <div class="col-lg-4 col-sm-6">
            <div class="form-group">
              <label>Attendee 1</label>   
              <select class="form-control select2" name="a1[]" style="width: 100%;" disabled="disabled">
              <option value="">Select</option>
              <?php if(isset($u_list) && !empty($u_list))
                { 
                  foreach($u_list as $li){
                ?>

                <option <?php if($lim['a1']==$li['id']){ echo "selected"; } ?> value="<?php echo $li['id']; ?>"><?php echo $li['name']; ?></option>          

            <?php } } ?>
            </select>

              </div>

            </div>


            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
              <label>Attendee 2</label>
              <select class="form-control select2" name="a2[]" style="width: 100%;" disabled="disabled">
              <option value="">Select</option>
              <?php if(isset($u_list) && !empty($u_list))
                { 
                  foreach($u_list as $li){
                ?>

                <option <?php if($lim['a2']==$li['id']){ echo "selected"; } ?> value="<?php echo $li['id']; ?>"><?php echo $li['name']; ?></option>          

            <?php } } ?>
            </select>

              </div>
            </div>


            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Mobile</label>
                <input type="text" class="form-control" name="m_mobile[]" value="<?php echo $lim['m_mobile']; ?>" readonly="">
              </div>
            </div>


        </div>



          </div>

          <div class="col-md-5">

          <div class="form-group remark-group">
                <label>Remark</label>
                <textarea type="text" class="form-control" name="m_remark[]" ><?php echo $lim['m_remark']; ?></textarea>
              </div>

          </div>

      </div>

    <?php } }  ?>


      <div class="row remark_row">
          <div class="col-md-7">

        <div class="row">

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Date</label>
                <input type="hidden" name="mlead_id[]" value="0">
                <input type="text" class="form-control datepicker2" name="m_date[]"  >
              </div>
            </div>
            
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Next Meeting Time</label>
               
                <div class="input-group bootstrap-timepicker timepicker">
                  <input  type="text" class="form-control input-small timepicker" name="m_time[]">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                </div>
              </div>
            </div>

               <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>POC</label>
                <input type="text" class="form-control" name="m_poc[]"  >
              </div>
            </div>
</div>
<div class="row">

        <!--    <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Address</label>
                <input type="text" class="form-control" name="m_address[]"  >
              </div>
            </div> -->


            <div class="col-lg-4 col-sm-6">
            <div class="form-group">
              <label>Attendee 1</label>

              <select class="form-control select2" name="a1[]" style="width: 100%;">
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

              <select class="form-control select2" name="a2[]" style="width: 100%;">
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
                <input type="text" class="form-control" name="m_mobile[]"  >
              </div>
            </div>


        </div>



          </div>

          <div class="col-md-5">
          <div class="form-group remark-group">
                <label>Remark</label>
                <textarea type="text" class="form-control"  name="m_remark[]"></textarea>
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

                  <option <?php if(isset($list['vertical_id']) AND ($list['vertical_id']==$li['id'])){ echo "selected"; } ?> value="<?php echo $li['id']; ?>"><?php echo $li['poc']; ?>- <?php echo $li['company_name']; ?></option>          

               <?php } } ?>
            </select>
              </div>
            </div>

            <div class="col-lg-2 col-sm-6">
              <div class="form-group">
                <label>Amount to Pay</label>
                <input type="text" class="form-control only_number" name="v_amount" value="<?php echo $list['v_amount']; ?>"  >
              </div>
            </div>

            <div class="col-lg-8 col-sm-6">
              <div class="form-group">
                <label>Remark</label>
                <input type="text" class="form-control only_number" name="v_remark" value="<?php echo $list['v_remark']; ?>"  >
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
            <?php if(!empty($bo_list)){ 
                for($i=0;$i<=6;$i++){
              ?>
            <tr>
              <td>
                <input type="hidden" name="bo_id[]" value="<?php echo $bo_list[$i]['id']; ?>">
                <input type="text" class="form-control" name="bo_cat[]" value="<?php echo $bo_list[$i]['bo_cat']; ?>" readonly="">
              </td>
              <td>
              <input type="text" class="form-control datepicker" name="bo_date[]" value="<?php echo $bo_list[$i]['bo_date']; ?>" ></td>
              <?php if($i==1 || $i==4){ ?>

              <td colspan="3">
                <select style="display:none;" class="form-control" style="width: 100%;" name="bo_status[]">
                  <option value="">Not Applicable</option>
                                     
                </select>
             <input type="hidden" class="form-control" name="bo_ref[]" value=""  >
             <input type="text" class="form-control" name="bo_remark[]" value="<?php echo $bo_list[$i]['bo_remark']; ?>"></td>
              <?php

              }else{ ?>
              <td>
                <select class="form-control" style="width: 100%;" name="bo_status[]">
                  <option <?php if($bo_list[$i]['bo_status']=="Not Applicable"){ echo "selected"; } ?> value="Not Applicable">Not Applicable</option>
                  <option <?php if($bo_list[$i]['bo_status']=="Pending Approval"){ echo "selected"; } ?> value="Pending Approval">Pending Approval</option>
                  <option <?php if($bo_list[$i]['bo_status']=="Query"){ echo "selected"; } ?> value="Query">Query</option>
                  <option <?php if($bo_list[$i]['bo_status']=="Approved"){ echo "selected"; } ?> value="Approved">Approved</option> 
                  <option <?php if($bo_list[$i]['bo_status']=="Expired"){ echo "selected"; } ?> value="Expired">Expired</option> 
                  <option <?php if($bo_list[$i]['bo_status']=="Other"){ echo "selected"; } ?> value="Other">Other</option>                    
                </select>
              </td>
              <td><input type="text" class="form-control" name="bo_ref[]" value="<?php echo $bo_list[$i]['bo_ref']; ?>"  ></td>
              <td><input type="text" class="form-control" name="bo_remark[]" value="<?php echo $bo_list[$i]['bo_remark']; ?>"></td>
              <?php } ?>
            </tr>
          <?php } } ?>

          </table>

        </div>
      </div>
    </div>
      
      <div class="box-footer text-center">
                <button id="add_btn" type="submit" class="btn btn-info">Submit</button>              
                <a href="<?php echo $info2['back_url']; ?>" class="btn btn-default">Cancel</button>   
              
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
     var cd = $('#close_date').val();
     if (!cd) return true;
     var cdP = cd.split('-');
     if (cdP.length !== 3) return true;
     var cdDate = new Date(cdP[2], cdP[1] - 1, cdP[0]);

     var maxFuDate = null;
     var maxFuStr = '';

     $('[name="followup_date[]"]').each(function() {
       var val = $(this).val();
       if (val) {
         var p = val.split('-');
         if (p.length === 3) {
           var d = new Date(p[2], p[1] - 1, p[0]);
           if (!maxFuDate || d > maxFuDate) {
             maxFuDate = d;
             maxFuStr = val;
           }
         }
       }
     });

     if (maxFuDate && cdDate < maxFuDate) {
       alert('Exp. Close Date cannot be previous or past of the follow up date (' + maxFuStr + ')!');
       $('#close_date').val(maxFuStr);
       return false;
     }
     return true;
   }

   $('#close_date, [name="followup_date[]"]').on('change changeDate', function() {
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

$(document).ready(function() {
  var statusCategoryMap = {
    '43': [
      'Pricing / Budget Constraints',
      'Competitor Chosen',
      'Product / Requirements Mismatch',
      'Client Postponed / Not Interested',
      'Unreachable / Dropped Contact',
      'Other'
    ],
    '44': [
      'Wrong Number / Invalid Contact',
      'Fake / Spam Enquiry',
      'Duplicate Lead Entry',
      'Not Interested / Out of Scope',
      'Out of Service Territory',
      'Other'
    ],
    '36': [
      'Raw Database',
      'Cleansed Lead',
      'Cold Prospect',
      'Direct Reference',
      'Other'
    ],
    '37': [
      'Proposal Under Discussion',
      'Decision Maker Pending',
      'Budget Approval Awaited',
      'Expected This Month',
      'Other'
    ],
    '38': [
      'Follow-up in Progress',
      'Negotiation Ongoing',
      'KYC / Document Collection',
      'Awaiting Client Feedback',
      'Other'
    ],
    '39': [
      'Successfully Converted',
      'Contract Signed',
      'Payment Received',
      'Other'
    ],
    '40': [
      'Documents Submitted',
      'Form Submitted to Operator',
      'Back Office Review',
      'Other'
    ],
    '41': [
      'Sim / Service Dispatched',
      'Activation Under Process',
      'Verification Pending',
      'Other'
    ],
    '42': [
      'Service Live',
      'Handover Completed',
      'First Bill Generated',
      'Other'
    ],
    'default': [
      'General Enquiry',
      'Follow-up Required',
      'High Priority',
      'Decision Maker Unavailable',
      'Other'
    ]
  };

  function updateStatusCategoryOptions(selectedStatus, preserveVal) {
    var opts = statusCategoryMap[selectedStatus] || statusCategoryMap['default'];
    var $dd = $('#status_category_dropdown');
    var currentInputVal = (preserveVal !== undefined && preserveVal !== null) ? preserveVal : $('#status_category_input').val();
    $dd.empty().append('<option value="">Select Status Category</option>');

    var matched = false;
    for (var i = 0; i < opts.length; i++) {
      var opt = opts[i];
      var isSel = (currentInputVal && currentInputVal === opt);
      if (isSel) matched = true;
      $dd.append($('<option></option>').attr('value', opt).text(opt).prop('selected', isSel));
    }

    if (currentInputVal && !matched) {
      $dd.val('Other');
      $('#status_category_input').show().val(currentInputVal);
    } else if (matched && currentInputVal !== 'Other') {
      $('#status_category_input').hide().val(currentInputVal);
    } else if (currentInputVal === 'Other') {
      $('#status_category_input').show().val('');
    } else {
      $('#status_category_input').hide();
    }
    $('#hidden_lost_reason').val($('#status_category_input').val());
  }

  $('#lead_status_select').on('change', function() {
    updateStatusCategoryOptions($(this).val(), null);
  });

  $('#status_category_dropdown').on('change', function() {
    var chosen = $(this).val();
    if (chosen === 'Other') {
      $('#status_category_input').show().val('').focus();
      $('#hidden_lost_reason').val('');
    } else if (chosen !== '') {
      $('#status_category_input').hide().val(chosen);
      $('#hidden_lost_reason').val(chosen);
    } else {
      $('#status_category_input').hide().val('');
      $('#hidden_lost_reason').val('');
    }
  });

  $('#status_category_input').on('input change', function() {
    $('#hidden_lost_reason').val($(this).val());
  });

  var initialVal = $('#status_category_input').val();
  updateStatusCategoryOptions($('#lead_status_select').val(), initialVal);
});
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
