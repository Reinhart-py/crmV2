<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1> <?php echo $info['page_heading']; ?> </h1>
  </section>
  <section class="content ">
    <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'),'message'); ?>
  <div class="container">







        

<div class="row">
<div class="box box-default">
      <div class="box-header">
<div class="col-sm-12">
     
          <?php $return_url= urldecode($this->session->userdata('manager_return_string')); ?>
          <div class="pull-left">
            <a href="<?php echo base_url(); ?>manager/<?php echo $return_url; ?>" class="btn btn-info">Back</a>
          </div>

        
          <div class="pull-right">
            <?php if(!empty($m_list)){ if($m_list['a1']==$this->session->userdata('manager_id') || $m_list['a2']==$this->session->userdata('manager_id') || $m_list['a3']==$this->session->userdata('manager_id')){ ?>
        	 <a href="<?php echo base_url(); ?>manager/leads/editLimited/<?php echo base64_encode($p_info['id']); ?>/tm" class="btn btn-warning">Update Status</a>
       
       <?php } } ?>
          <?php if (!empty($is_already_lead)) { ?>
            <span class="btn btn-default" style="cursor:default; background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; font-weight:600; margin-right:5px;">
              <i class="fa fa-check-circle" style="color:#10b981;"></i> Converted to Lead
            </span>
            <?php if (!empty($sales_ref_no)) { ?>
              <span class="btn btn-default" style="cursor:default; background:#f0fdf4; color:#15803d; border:1px solid #86efac; font-weight:600; margin-right:5px;">
                <i class="fa fa-trophy" style="color:#16a34a;"></i> Converted to Sales (<?php echo htmlspecialchars($sales_ref_no); ?>)
              </span>
              <?php if (!empty($order_info['id'])) { ?>
              <a href="<?php echo base_url(); ?>manager/orders/view/<?php echo $order_info['id']; ?>" class="btn btn-default" style="background:#f8fafc; color:#2563eb; border:1px solid #cbd5e1; font-weight:600; margin-right:5px;">
                <i class="fa fa-shopping-cart"></i> View Order (<?php echo htmlspecialchars($order_ref_no); ?>)
              </a>
              <?php } ?>
            <?php } elseif (!empty($order_ref_no) || (!empty($p_info['order_request_status']) && in_array($p_info['order_request_status'], array('pending', 'approved', 'processed', 'ordered')))) { ?>
              <span class="btn btn-default" style="cursor:default; background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; font-weight:600; margin-right:5px;">
                <i class="fa fa-hourglass-half" style="color:#2563eb;"></i> Order <?php echo !empty($order_ref_no) ? htmlspecialchars($order_ref_no) : 'Pending Process'; ?>
              </span>
              <?php if (!empty($order_info['id'])) { ?>
              <a href="<?php echo base_url(); ?>manager/orders/view/<?php echo $order_info['id']; ?>" class="btn btn-default" style="background:#f8fafc; color:#2563eb; border:1px solid #cbd5e1; font-weight:600; margin-right:5px;">
                <i class="fa fa-shopping-cart"></i> View Order (<?php echo htmlspecialchars($order_ref_no); ?>)
              </a>
              <?php } ?>
            <?php } else { ?>
              <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#requestOrderModal" style="background:#f59e0b; border-color:#d97706; color:#ffffff; font-weight:600; margin-right:5px;">
                <i class="fa fa-shopping-cart"></i> Request Order
              </button>
            <?php } ?>
          <?php } elseif (!empty($is_pending_review)) { ?>
            <span class="btn btn-default" style="cursor:default; background:#fffbeb; color:#92400e; border:1px solid #fde68a; font-weight:600; margin-right:5px;">
              <i class="fa fa-clock-o" style="color:#f59e0b;"></i> Conversion Request Pending
            </span>
            <?php if (!empty($can_review_conversion)) { ?>
              <button type="button" class="btn btn-success" data-toggle="modal" data-target="#convertLeadModal" style="background:#16a34a; border-color:#15803d; font-weight:600; margin-right:5px;">
                <i class="fa fa-check-circle"></i> Approve &amp; Convert
              </button>
              <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectLeadModal" style="background:#dc2626; border-color:#b91c1c; font-weight:600; margin-right:5px;">
                <i class="fa fa-times-circle"></i> Reject
              </button>
            <?php } ?>
          <?php } else { ?>
            <?php if (!empty($can_convert_to_lead)) { ?>
              <button type="button" class="btn btn-success" data-toggle="modal" data-target="#convertLeadModal" style="background:#16a34a; border-color:#15803d; font-weight:600; margin-right:5px;">
                <i class="fa fa-check-circle"></i> Convert to Lead
              </button>
            <?php } ?>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#requestLeadModal" style="background:#2563eb; border-color:#1d4ed8; font-weight:600; margin-right:5px;">
              <i class="fa fa-paper-plane"></i> Request Conversion
            </button>
          <?php } ?>
          <?php 
          $cur_user = (int)$this->session->userdata('manager_id');
          $cur_r = (int)$this->session->userdata('manager_role');
          $lead_owner_id = !empty($p_info['lead_assign']) ? (int)$p_info['lead_assign'] : (int)$p_info['created_by'];
          $is_agent_collab = (!empty($p_info['agents']) && in_array($cur_user, array_map('intval', explode(',', $p_info['agents']))));
          $is_locked_for_user = (!empty($is_locked) && $cur_r !== 1 && $cur_r !== 4);
          $can_edit = (!$is_locked_for_user) && ($cur_r == 1 || $cur_r == 4 || $cur_user === $lead_owner_id || $is_agent_collab);
          if($can_edit){ ?>
           <?php if($action=='tm'){ ?>
             <a href="<?php echo base_url(); ?>manager/leads/edit/<?php echo base64_encode($p_info['id']); ?>/tm" class="btn btn-danger">Edit Lead</a>
           <?php }else{ ?>
             <a href="<?php echo base_url(); ?>manager/leads/edit/<?php echo base64_encode($p_info['id']); ?>" class="btn btn-danger">Edit Lead</a>
           <?php } ?>
          <?php } ?>
          </div>

           </div>
           </div>
</div>

<?php if (!empty($is_locked_for_user)) { ?>
<div class="alert alert-warning" style="background:#fffbeb; border:1px solid #fde68a; color:#92400e; font-weight:600; padding:12px 16px; margin-bottom:15px; border-radius:6px;">
  <i class="fa fa-lock" style="font-size:16px; margin-right:8px; color:#d97706;"></i>
  This Lead is currently locked because an Order conversion process is active. Editing is restricted to Back Office administrators.
</div>
<?php } ?>

<div class="box box-default" style="border-radius:6px; margin-bottom:15px; border-top:3px solid #2563eb;">
  <div class="box-body" style="padding:12px 18px;">
    <div class="row text-center">
      <div class="col-xs-6 col-sm-3" style="border-right:1px solid #e2e8f0;">
        <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Database Ref</div>
        <div style="font-size:15px; font-weight:700; color:#0f172a; margin-top:3px;">
          <span class="label" style="background:#f1f5f9; color:#0f172a; border:1px solid #cbd5e1; font-size:13px; font-weight:700;"><?php echo htmlspecialchars($db_ref_no); ?></span>
        </div>
      </div>
      <div class="col-xs-6 col-sm-3" style="border-right:1px solid #e2e8f0;">
        <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Lead Ref</div>
        <div style="font-size:15px; font-weight:700; color:#0f172a; margin-top:3px;">
          <?php if (!empty($lead_ref_no)) { ?>
            <span class="label" style="background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; font-size:13px; font-weight:700;"><?php echo htmlspecialchars($lead_ref_no); ?></span>
          <?php } else { ?>
            <span style="color:#94a3b8; font-size:13px;">—</span>
          <?php } ?>
        </div>
      </div>
      <div class="col-xs-6 col-sm-3" style="border-right:1px solid #e2e8f0;">
        <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Order Ref</div>
        <div style="font-size:15px; font-weight:700; color:#0f172a; margin-top:3px;">
          <?php if (!empty($order_ref_no)) { ?>
            <span class="label" style="background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; font-size:13px; font-weight:700;"><?php echo htmlspecialchars($order_ref_no); ?></span>
          <?php } elseif (!empty($p_info['order_request_status'])) { ?>
            <span class="label label-warning" style="font-size:11px;">Pending Process</span>
          <?php } else { ?>
            <span style="color:#94a3b8; font-size:13px;">—</span>
          <?php } ?>
        </div>
      </div>
      <div class="col-xs-6 col-sm-3">
        <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Sales Ref</div>
        <div style="font-size:15px; font-weight:700; color:#0f172a; margin-top:3px;">
          <?php if (!empty($sales_ref_no)) { ?>
            <span class="label" style="background:#f0fdf4; color:#15803d; border:1px solid #86efac; font-size:13px; font-weight:700;"><?php echo htmlspecialchars($sales_ref_no); ?></span>
          <?php } else { ?>
            <span style="color:#94a3b8; font-size:13px;">—</span>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="my-profile">
<div class="box box-default">
      <div class="box-header">
		<div class="col-sm-12">
        <h3 class="box-title" style="text-transform:capitalize"><b>
          <?php if((isset($p_info['customer']))&&(!empty($p_info['customer']))){echo $p_info['customer'];}else echo ""; ?>
          </b>
          <?php 
          $disp_zone = !empty($p_info['tl_zone']) ? $p_info['tl_zone'] : '';
          if (empty($disp_zone) && !empty($p_info['tl_authority'])) {
              if (stripos($p_info['tl_authority'], 'Free Zone') !== false || stripos($p_info['tl_authority'], 'Freezone') !== false || stripos($p_info['tl_authority'], 'DIFC') !== false || stripos($p_info['tl_authority'], 'ADGM') !== false) {
                  $disp_zone = 'Freezone';
              } elseif (stripos($p_info['tl_authority'], 'Department of Economic') !== false || stripos($p_info['tl_authority'], 'Municipality') !== false || stripos($p_info['tl_authority'], 'DED') !== false || stripos($p_info['tl_authority'], 'DET') !== false) {
                  $disp_zone = 'Mainland';
              }
          }
          if (!empty($disp_zone) && in_array(strtolower($disp_zone), array('mainland', 'freezone'))) { ?>
            <span class="label" style="background:#000000; color:#ffffff; font-weight:700; padding:2px 7px; border-radius:3px; font-size:12px; margin-left:6px; letter-spacing:0.5px;"><?php echo ucfirst(strtolower($disp_zone)); ?></span>
          <?php } ?>
          <?php if(!empty($p_info['dnd_status']) && strtolower($p_info['dnd_status']) == 'yes'){ ?>
            <span class="label label-danger" style="background:#dc2626; color:#ffffff; font-weight:700; padding:2px 7px; border-radius:3px; font-size:12px; margin-left:6px; letter-spacing:0.5px;">DNCR</span>
          <?php } ?>
          <?php if(!empty($p_info['company_status']) && strtolower($p_info['company_status']) == 'closed'){ ?>
            <span class="label label-primary" style="background:#2563eb; color:#ffffff; font-weight:700; padding:2px 7px; border-radius:3px; font-size:12px; margin-left:6px; letter-spacing:0.5px;">Closed</span>
          <?php } ?>
        </h3>    
          </div>
          </div>
<div class="box-body" >
      
        <div class="row">
  <div class="col-md-6">
      <div class="table-responsive border">
          <table class="table">
        
              <tr>
                <th scope="col">Database No.</th>
                <td><?php echo !empty($db_ref_no) ? htmlspecialchars($db_ref_no) : ('D' . str_pad($p_info['id'], 10, '0', STR_PAD_LEFT)); ?></td>
              </tr>
              <?php if (!empty($lead_ref_no)) { ?>
              <tr>
                <th scope="col">Lead No.</th>
                <td><?php echo htmlspecialchars($lead_ref_no); ?></td>
              </tr>
              <?php } ?>
   
              <tr>
                <th scope="col">Company Status</th>
                <td scope="col"><?php echo !empty($p_info['company_status']) ? htmlspecialchars($p_info['company_status']) : 'Active'; ?></td>
              </tr>

              <tr>
              <th scope="col">Category</th>
                <td scope="col"><?php echo $p_info['category']; ?></td>
              </tr>
         
          
          
              <tr>
              <th scope="col">Account Under Vertical</th>
                <td scope="col"><?php echo $p_info['a_department']; ?></td>
            
              </tr>
            
              <tr>
                <th scope="col">Current Monthly Revenue Date</th>
                <td scope="col"><?php echo $p_info['r_date']; ?></td>

                
              </tr>
      
              
            
              <tr>
              <th scope="col">Product Category </th>
                <td scope="col"><?php echo $p_info['psubcat_name']; ?></th>
            
              </tr>
          
       
              <tr>
                <th scope="col">Qty </th>
                <td><?php echo $p_info['qty']; ?></td>

                
              </tr>
          
          
              <tr>
              <th scope="col">Revenue </th>
              <td><?php echo $p_info['revenue']; ?>
            </td>
            
              </tr>
           









           
              <tr>
                <th scope="col">POC Name</th>
                <td scope="col"> 
                <?php echo $p_info['poc']; ?></td>

                
              </tr>
           

              <tr>
              <th scope="col">Location  </th>
                <td scope="col"><?php echo $p_info['location']; ?> </td>
            
              </tr>


              <tr>
                <th scope="col">Next Follow Up Date  </th>
                <td scope="col"><?php echo simple_date($p_info['followup_date']); ?> </td>
                
              </tr>

              <tr>
              <th scope="col">Exp. Close Date</th>
              <td scope="col"><?php echo simple_date($p_info['close_date']); ?> </td>              
            
              </tr>

            <tr>
              <th scope="col">Address  </th>
              <td scope="col"><?php echo $p_info['address']; ?> 
 </td>              
              </tr>

              <tr>
              <th scope="col">Country  </th>
              <td scope="col"><?php echo $p_info['country_name']; ?> </td>              
            
              </tr>


              <th scope="col">  Alternate POC Name</th>
              <td scope="col"><?php echo $p_info['poc2']; ?>  </td>              
            
              </tr>

               <tr>
              <th scope="col">Channel partner  </th>
              <td scope="col"><?php echo $p_info['c_partner']; ?> </td>              
            
              </tr>

              <tr>
              <th scope="col"> Lead Category  </th>
              <td scope="col">  <?php echo $p_info['lead_cat']; ?> </td>              
            
              </tr>



              <tr>
              <th scope="col">Website / Source Link </th>
              <td scope="col"><?php echo $p_info['lead_source']; ?> </td>              
            
              </tr>

              <tr>
              <th scope="col">One time charges   </th>
              <td scope="col"><?php echo $p_info['otc']; ?></td>              
              </tr>

              <tr>
              <th scope="col">Trade License Number</th>
              <td scope="col"><?php echo !empty($p_info['tl_number']) ? $p_info['tl_number'] : '-'; ?></td>              
              </tr>

              <tr>
              <th scope="col">Trade License Authority</th>
              <td scope="col"><?php echo !empty($p_info['tl_authority']) ? $p_info['tl_authority'] : '-'; ?></td>              
              </tr>

              <tr>
              <th scope="col">Trade License Emirates</th>
              <td scope="col"><?php echo !empty($p_info['tl_emirate']) ? $p_info['tl_emirate'] : '-'; ?></td>              
              </tr>

              <tr>
              <th scope="col">Trade License Zone</th>
              <td scope="col">
                <?php if(!empty($disp_zone)){ ?>
                  <span class="label <?php echo (strtolower($disp_zone)=='mainland'||strtolower($disp_zone)=='freezone') ? '' : 'label-default'; ?>" style="<?php echo (strtolower($disp_zone)=='mainland'||strtolower($disp_zone)=='freezone') ? 'background:#000000; color:#ffffff; font-weight:700;' : ''; ?> padding: 3px 8px; border-radius: 3px; font-size: 11.5px;">
                    <?php echo htmlspecialchars($disp_zone); ?>
                  </span>
                <?php } else { echo '-'; } ?>
              </td>              
              </tr>

              <tr>
              <th scope="col">TL Issue Date</th>
              <td scope="col"><?php echo (!empty($p_info['tl_issue_date']) && $p_info['tl_issue_date'] != '0000-00-00') ? simple_date($p_info['tl_issue_date']) : '-'; ?></td>              
              </tr>

              <tr>
              <th scope="col">TL Expire Date</th>
              <td scope="col"><?php echo (!empty($p_info['tl_expire_date']) && $p_info['tl_expire_date'] != '0000-00-00') ? simple_date($p_info['tl_expire_date']) : '-'; ?></td>              
              <tr>
              <th scope="col">Industry Category</th>
              <td scope="col"><?php echo !empty($p_info['industry_category']) ? htmlspecialchars($p_info['industry_category']) : (!empty($p_info['business_category']) ? htmlspecialchars($p_info['business_category']) : '-'); ?></td>              
              </tr>

              <tr>
              <th scope="col">Corporate Tax Status</th>
              <td scope="col"><?php echo !empty($p_info['corporate_tax_status']) ? htmlspecialchars($p_info['corporate_tax_status']) : '-'; ?></td>              
              </tr>

              <tr>
              <th scope="col">VAT Status</th>
              <td scope="col"><?php echo !empty($p_info['vat_status']) ? htmlspecialchars($p_info['vat_status']) : '-'; ?></td>              
              </tr>

              <tr>
              <th scope="col">Quote Status</th>
              <td scope="col"><?php echo !empty($p_info['quote_status']) ? htmlspecialchars($p_info['quote_status']) : '-'; ?></td>              
              </tr>

              <tr>
              <th scope="col">Quote Mode</th>
              <td scope="col"><?php echo !empty($p_info['quote_mode']) ? htmlspecialchars($p_info['quote_mode']) : '-'; ?></td>              
              </tr>

              <tr>
              <th scope="col">Quote Amount</th>
              <td scope="col"><?php echo (isset($p_info['quote_amount']) && $p_info['quote_amount'] !== '' && $p_info['quote_amount'] !== null) ? htmlspecialchars($p_info['quote_amount']) : '-'; ?></td>              
              </tr>

              <th scope="col">Modified At</th>
              <td scope="col"><?php echo simple_date($p_info['modified']); ?>  </td>              
            
              </tr>


          </table>
      </div>
 </div>

  <div class="col-md-6">
      <div class="table-responsive border">
          <table class="table">


      
          <tr>
              <th scope="col">Customer Name</th>
                <td scope="col"><?php echo $p_info['customer']; ?> </td>
              
              </tr>

        
    
               <tr>
              <th scope="col">Account Number </th>
                <td scope="col"><?php echo $p_info['a_number']; ?></td>
            
              </tr>
         
             <tr>
              <th scope="col">Current Monthly Revenue Amount</th>
                <td scope="col"><?php echo $p_info['r_amount']; ?></td>
              
              </tr>

            <tr>
              <th scope="col">Service Category</th>
                <td scope="col"><?php echo $p_info['pcat_name']; ?></td>
            
              </tr>

                 <tr>
              <th scope="col">Product Details </th>
                <td scope="col"><?php echo $p_info['p_details']; ?></td>
              
              </tr>


                <tr>
              <th scope="col">MRC</th>
                <td scope="col"><?php echo $p_info['mrc']; ?></td>
            
              </tr>

              <tr>
              <th scope="col">MRR </th>
              <td><?php echo $p_info['mrr']; ?></td>
              
              </tr>



  


              <tr>
              <th scope="col">POC Contact Number</th>
              <td scope="col"><?php echo $p_info['contact1']; ?></td>
            
              </tr>

              <tr>
              <th scope="col">Alternative Contact Number</th>
              <td scope="col"><?php echo !empty($p_info['alt_contact']) ? $p_info['alt_contact'] : (!empty($p_info['alt_mobile']) ? $p_info['alt_mobile'] : '-'); ?></td>
              </tr>

              <tr>
              <th scope="col">Email</th>
              <td scope="col"><?php echo !empty($p_info['email']) ? $p_info['email'] : '-'; ?></td>
              </tr>

              <tr>
              <th scope="col">DNCR/DND</th>
              <td scope="col"><?php echo !empty($p_info['dnd_status']) ? $p_info['dnd_status'] : 'No'; ?> <?php if(!empty($p_info['dnd_status']) && strtolower($p_info['dnd_status']) == 'yes'){ ?><span class="label label-danger" style="background:#dc2626; color:#ffffff; font-weight:700; padding:2px 7px; border-radius:3px; font-size:11px; margin-left:6px; letter-spacing:0.5px;">DNCR</span><?php } ?></td>
              </tr>

              <tr>
              <th scope="col">DNCR Date</th>
              <td scope="col"><?php echo (!empty($p_info['dnd_check_date']) && $p_info['dnd_check_date'] != '0000-00-00') ? simple_date($p_info['dnd_check_date']) : '-'; ?></td>
              </tr>


              <tr>
              <th scope="col">Status</th>
                <td scope="col"><?php echo $p_info['status']; ?></td>
              </tr>
              <tr>
              <th scope="col">Status Category</th>
                <td scope="col"><?php echo !empty($p_info['status_category']) ? htmlspecialchars($p_info['status_category']) : (!empty($p_info['lost_reason']) ? htmlspecialchars($p_info['lost_reason']) : '-'); ?></td>
              </tr>
              <tr>
              <th scope="col">Status Remarks</th>
                <td scope="col"><?php echo !empty($p_info['status_remarks']) ? htmlspecialchars($p_info['status_remarks']) : '-'; ?></td>
              </tr>
              <tr>
              <th scope="col">Data Source</th>
                <td scope="col"><?php echo !empty($p_info['data_source']) ? htmlspecialchars($p_info['data_source']) : '-'; ?></td>
              </tr>
              <tr>
              <th scope="col">Data Batch Referral No.</th>
                <td scope="col"><?php echo !empty($p_info['data_batch_referral_no']) ? htmlspecialchars($p_info['data_batch_referral_no']) : '-'; ?></td>
              </tr>
              <tr>
              <th scope="col">Database Category</th>
                <td scope="col"><?php echo !empty($p_info['database_category']) ? htmlspecialchars($p_info['database_category']) : '-'; ?></td>
              </tr>

              <tr>
              <th scope="col">Next Follow Up Time </th>
                <td scope="col"><?php echo $p_info['followup_time']; ?></td>
            
              </tr>
           

              
              <tr>
              <th scope="col">Remarks </th>
              <td scope="col"><?php echo $p_info['remark']; ?></td>              
              </tr>
          


              <tr>
              <th scope="col">City  </th>
              <td scope="col"><?php echo $p_info['city']; ?></td>              
            
              </tr>



          
              <tr>
              <th scope="col">State / Province / Emirate </th>
              <td scope="col"><?php echo $p_info['state_name']; ?></td>              
            
              </tr>





              <tr>

              <th scope="col">Alternate POC Contact Number</th>
              <td scope="col"><?php echo $p_info['contact2']; ?></td>              
            
              </tr>
       

				  <tr>
              <th scope="col">Lead Assigned To   </th>
              <td scope="col"><?php echo $p_info['assigned_name']; ?></td>              
            
              </tr>
        


              <tr>
              <th scope="col"> Created At  </th>
              <td scope="col"><?php echo $p_info['created']; ?></td>              
            
              </tr>



              <tr>
              <th scope="col">Created By </th>
              <td scope="col"><?php echo $p_info['created_name']; ?></td>              
            
              </tr>

              <tr>
              <th scope="col">Previous Owner(s)</th>
              <td scope="col">
                <?php 
                if(!empty($transfer_history)){
                  $prev_names = array();
                  foreach($transfer_history as $th){
                    if(!empty($th['from_user_name']) && !in_array($th['from_user_name'], $prev_names)){
                      $prev_names[] = $th['from_user_name'];
                    }
                  }
                  if(!empty($prev_names)){
                    echo htmlspecialchars(implode(' &rarr; ', $prev_names));
                  } else {
                    echo '-';
                  }
                } else {
                  echo '-';
                }
                ?>
              </td>
              </tr>




          </table>
      </div>
  </div>

</div>
</div>
</div>
</div>
<div class="box box-default">
      	<div class="box-header">
		<div class="col-sm-12">
       		<h3 class="box-title">Followup Field</h3>
        </div>
        </div>
<div class="box-body" >
      
        <div class="row">
  
  <div class="table-responsive">
    <table class="table">
            
            <?php if(!empty($follow_list)){ 
              ?>
              <tr>
              <th>Followup Date</th>
              <th>Time</th>
              <th>Remark</th>              
            </tr>
              <?php
                foreach($follow_list as $list){
              ?>
            <tr>
              <td><?php echo simple_date($list['followup_date']); ?></td>
              <td><?php echo $list['followup_time']; ?></td>             
                <td><?php echo $list['followup_remark']; ?></td>
            
             
            </tr>
          <?php } } ?>

          </table>
          </div>

</div>
</div>

<div class="box box-default">
  <div class="box-header with-border">
    <div class="col-sm-12">
      <h3 class="box-title"><i class="fa fa-phone" style="margin-right:6px; color:#0f172a;"></i> Call Logs</h3>
    </div>
  </div>
  <div class="box-body">
    <div class="row">
      <div class="col-sm-12">
        <?php if(!empty($call_logs)){ ?>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th style="width:130px;">Date & Time</th>
                  <th>Agent</th>
                  <th>Phone / Destination</th>
                  <th>Duration</th>
                  <th>Status</th>
                  <th>Notes</th>
                  <th>Recording / Transcript</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($call_logs as $cl){ ?>
                  <tr>
                    <td><?php echo date('d-m-Y H:i', strtotime($cl['created_at'])); ?></td>
                    <td><strong><?php echo htmlspecialchars(!empty($cl['agent_name']) ? $cl['agent_name'] : 'System'); ?></strong></td>
                    <td><?php echo htmlspecialchars($cl['phone_number']); ?></td>
                    <td><span class="badge bg-gray"><?php echo (int)$cl['duration']; ?>s</span></td>
                    <td><span class="label label-default"><?php echo htmlspecialchars($cl['call_status'] ?: 'Completed'); ?></span></td>
                    <td>
                      <?php 
                        $note_text = !empty($cl['notes']) ? $cl['notes'] : (!empty($cl['ai_summary']) ? $cl['ai_summary'] : '');
                        echo !empty($note_text) ? htmlspecialchars($note_text) : '-';
                      ?>
                    </td>
                    <td>
                      <?php if(!empty($cl['recording_file']) || !empty($cl['recording_url'])){ 
                        $rec_url = !empty($cl['recording_file']) ? base_url($cl['recording_file']) : $cl['recording_url'];
                      ?>
                        <div style="margin-bottom:6px;">
                          <audio controls style="height:28px; width:190px;" src="<?php echo htmlspecialchars($rec_url); ?>"></audio>
                        </div>
                      <?php } ?>
                      <?php if(!empty($cl['transcript'])){ ?>
                        <button type="button" class="btn btn-xs btn-default" onclick="$(this).next('.lead-transcript-drawer').slideToggle(120);">
                          <i class="fa fa-file-text-o"></i> View Transcript
                        </button>
                        <div class="lead-transcript-drawer" style="display:none; margin-top:6px; padding:8px; background:#f1f5f9; border-radius:4px; font-size:11px; max-height:120px; overflow-y:auto; white-space:pre-wrap; color:#334155;">
                          <?php echo htmlspecialchars($cl['transcript']); ?>
                        </div>
                      <?php } ?>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        <?php } else { ?>
          <div style="padding:16px; text-align:center; color:#94a3b8; font-size:13px;">
            <i class="fa fa-phone" style="font-size:24px; display:block; margin-bottom:6px; opacity:0.6;"></i>
            No past connected call records for this contact.
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
</div>

<div class="box box-default">
      <div class="box-header">
		<div class="col-sm-12">
       		<h3 class="box-title">Meeting Field</h3>
        </div>
        </div>
<div class="box-body" >
      
        <div class="row">
  
    <div class="table-responsive">

 <table class="table table-border">
            
            <?php if(!empty($bo_list)){ 
              ?>
              <tr>
              <th>Category</th>
              <th>Date</th>
              <th>Status</th>
              <th>Reference</th>
              <th>Remarks</th>
            </tr>
              <?php
                for($i=0;$i<=6;$i++){
              ?>
            <tr>
              <td><?php echo $bo_list[$i]['bo_cat']; ?></td>
              <td><?php echo $bo_list[$i]['bo_date']; ?></td>
              <?php if($i==1 || $i==4){ ?>              
              <td colspan="3"><?php echo $bo_list[$i]['bo_remark']; ?></td>
              <?php }else{ ?>
                <td><?php echo $bo_list[$i]['bo_status']; ?></td>
              <td><?php echo $bo_list[$i]['bo_ref']; ?></td>
              <td><?php echo $bo_list[$i]['bo_remark']; ?></td>
              <?php } ?>
            </tr>
          <?php } } ?>

          </table>


      </div>

</div>
</div>
</div>

<?php if(!empty($transfer_history)){ ?>
<div class="box box-default">
      <div class="box-header">
		<div class="col-sm-12">
       		<h3 class="box-title"><i class="fa fa-history" style="color:#2563eb;"></i> Ownership &amp; Transfer History</h3>
        </div>
        </div>
<div class="box-body">
    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead>
          <tr style="background:#f8fafc;">
            <th>Date &amp; Time</th>
            <th>Transferred From (Previous Owner)</th>
            <th>Transferred To (New Owner)</th>
            <th>Transferred By</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($transfer_history as $th){ ?>
            <tr>
              <td><?php echo date('d-m-Y h:i A', strtotime($th['created_at'])); ?></td>
              <td><span class="label label-default" style="font-size:12px;"><?php echo htmlspecialchars($th['from_user_name'] ?? 'User #'.$th['from_user_id']); ?></span></td>
              <td><span class="label label-success" style="font-size:12px;"><?php echo htmlspecialchars($th['to_user_name'] ?? 'User #'.$th['to_user_id']); ?></span></td>
              <td><?php echo htmlspecialchars($th['transferred_by_name'] ?? 'User #'.$th['transferred_by']); ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
</div>
</div>
<?php } ?>
<?php if(!empty($pipeline_logs)){ ?>
<div class="box box-default">
      <div class="box-header">
		<div class="col-sm-12">
       		<h3 class="box-title"><i class="fa fa-history" style="color:#16a34a;"></i> Pipeline &amp; Conversion History</h3>
        </div>
        </div>
<div class="box-body">
    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead>
          <tr style="background:#f8fafc;">
            <th>Date &amp; Time</th>
            <th>Action</th>
            <th>Stage Change</th>
            <th>Performed By</th>
            <th>Remarks</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($pipeline_logs as $pl){ ?>
            <tr>
              <td><?php echo date('d-m-Y h:i A', strtotime($pl['created_at'])); ?></td>
              <td><span class="label label-primary" style="font-size:11px;"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $pl['action']))); ?></span></td>
              <td><?php echo htmlspecialchars(ucfirst($pl['stage_from'] ?? 'database')); ?> &rarr; <strong style="color:#16a34a;"><?php echo htmlspecialchars(ucfirst($pl['stage_to'] ?? 'lead')); ?></strong></td>
              <td><?php echo htmlspecialchars($pl['action_by_name'] ?? ('User #'.$pl['action_by'])); ?></td>
              <td><?php echo htmlspecialchars($pl['remarks'] ?? '-'); ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
</div>
</div>
<?php } ?>

<?php if(!empty($call_logs)){ ?>
<div class="row" style="margin-top:20px;">
<div class="col-md-12">
    <div class="table-responsive border" style="background:#ffffff; border-radius:8px; border:1px solid #e2e8f0; padding:15px; margin-bottom:20px;">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; border-bottom:1px solid #f1f5f9; padding-bottom:10px;">
        <h4 style="margin:0; font-weight:700; color:#0f172a; font-size:16px;">
          <i class="fa fa-phone" style="color:#2563eb; margin-right:6px;"></i> Call History &amp; Post-Call AI Intelligence
        </h4>
        <span class="badge" style="background:#f1f5f9; color:#475569; font-weight:600;"><?php echo count($call_logs); ?> Calls</span>
      </div>
      <table class="table table-bordered table-striped" style="margin-bottom:0;">
        <thead>
          <tr style="background:#f8fafc; color:#334155; font-size:12px; text-transform:uppercase;">
            <th>Date &amp; Time</th>
            <th>Agent / Extension</th>
            <th>Call Details</th>
            <th>Status (Call vs AI)</th>
            <th>Recording</th>
            <th>AI Intelligence &amp; Analysis</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($call_logs as $cl){ 
            $sentiment_badge = 'default';
            if(strcasecmp($cl['ai_sentiment'] ?? '', 'positive') === 0) $sentiment_badge = 'success';
            elseif(strcasecmp($cl['ai_sentiment'] ?? '', 'negative') === 0) $sentiment_badge = 'danger';
            elseif(strcasecmp($cl['ai_sentiment'] ?? '', 'neutral') === 0) $sentiment_badge = 'info';

            $ai_status_badge = 'default';
            $p_status = $cl['ai_processing_status'] ?? 'pending';
            if($p_status === 'completed') $ai_status_badge = 'success';
            elseif($p_status === 'failed') $ai_status_badge = 'danger';
            elseif($p_status === 'retry_pending') $ai_status_badge = 'warning';
            elseif(in_array($p_status, array('transcribing', 'analyzing'))) $ai_status_badge = 'primary';
          ?>
            <tr>
              <td style="white-space:nowrap; font-size:12px;">
                <?php echo date('d-m-Y h:i A', strtotime($cl['created_at'])); ?><br>
                <small class="text-muted">Duration: <?php echo (int)($cl['duration'] ?? 0); ?>s</small>
              </td>
              <td>
                <strong><?php echo htmlspecialchars($cl['agent_name'] ?? 'Agent'); ?></strong><br>
                <small class="text-muted">Ext: <?php echo htmlspecialchars($cl['extension'] ?? '-'); ?></small>
              </td>
              <td>
                <span class="label label-<?php echo ($cl['direction'] ?? '') === 'inbound' ? 'info' : 'primary'; ?>" style="font-size:10px;">
                  <?php echo strtoupper($cl['direction'] ?? 'OUTBOUND'); ?>
                </span><br>
                <span style="font-size:12px; font-weight:600;"><?php echo htmlspecialchars($cl['phone_number'] ?? ''); ?></span>
              </td>
              <td>
                <div><small style="color:#64748b; font-size:10px; text-transform:uppercase; font-weight:600;">CRM Status:</small></div>
                <span class="label label-default" style="font-size:11px;"><?php echo htmlspecialchars($cl['disposition'] ?? $cl['call_status'] ?? 'Completed'); ?></span>
                <?php if(!empty($cl['ai_status'])){ ?>
                  <div style="margin-top:6px;"><small style="color:#64748b; font-size:10px; text-transform:uppercase; font-weight:600;">AI Suggested:</small></div>
                  <span class="label label-info" style="font-size:11px;"><?php echo htmlspecialchars($cl['ai_status']); ?></span>
                <?php } ?>
              </td>
              <td style="min-width:180px;">
                <?php if(!empty($cl['recording_file']) && file_exists(FCPATH . $cl['recording_file'])){ ?>
                  <audio controls style="width:180px; height:32px;">
                    <source src="<?php echo base_url() . $cl['recording_file']; ?>" type="audio/mpeg">
                    Your browser does not support audio playback.
                  </audio>
                <?php } elseif(!empty($cl['recording_url'])){ ?>
                  <a href="<?php echo htmlspecialchars($cl['recording_url']); ?>" target="_blank" class="btn btn-xs btn-default">
                    <i class="fa fa-play"></i> 3CX Audio
                  </a>
                <?php } else { ?>
                  <span class="text-muted" style="font-size:11px;">No recording</span>
                <?php } ?>
              </td>
              <td style="max-width:320px;">
                <div style="display:flex; gap:6px; align-items:center; margin-bottom:4px;">
                  <span class="label label-<?php echo $ai_status_badge; ?>" style="font-size:10px; text-transform:capitalize;">
                    <?php echo htmlspecialchars($p_status); ?>
                  </span>
                  <?php if(!empty($cl['ai_sentiment'])){ ?>
                    <span class="label label-<?php echo $sentiment_badge; ?>" style="font-size:10px;">
                      <?php echo htmlspecialchars(ucfirst($cl['ai_sentiment'])); ?>
                    </span>
                  <?php } ?>
                  <span class="badge" style="background:#e0e7ff; color:#3730a3; font-size:9px;">AI Generated</span>
                </div>
                <?php if(!empty($cl['ai_summary'])){ ?>
                  <div style="font-size:12px; font-weight:600; color:#1e293b; margin-bottom:4px; line-height:1.35;">
                    <?php echo htmlspecialchars($cl['ai_summary']); ?>
                  </div>
                <?php } ?>
                <?php if(!empty($cl['ai_remarks'])){ ?>
                  <div style="font-size:11px; color:#475569; margin-bottom:4px; line-height:1.35;">
                    <?php echo htmlspecialchars($cl['ai_remarks']); ?>
                  </div>
                <?php } ?>
                <?php if(!empty($cl['ai_next_action'])){ ?>
                  <div style="font-size:11px; color:#2563eb; font-weight:500;">
                    <i class="fa fa-arrow-right"></i> Next: <?php echo htmlspecialchars($cl['ai_next_action']); ?>
                    <?php if(!empty($cl['ai_follow_up_date'])){ echo ' (' . date('d-m-Y', strtotime($cl['ai_follow_up_date'])) . ')'; } ?>
                  </div>
                <?php } ?>
                <?php if(!empty($cl['transcript'])){ ?>
                  <details style="margin-top:6px; font-size:11px; color:#64748b;">
                    <summary style="cursor:pointer; color:#2563eb; font-weight:500;">View Full Transcript</summary>
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px; padding:6px; margin-top:4px; max-height:120px; overflow-y:auto; font-family:monospace; white-space:pre-wrap;">
                      <?php echo htmlspecialchars($cl['transcript']); ?>
                    </div>
                  </details>
                <?php } ?>
                <?php if($p_status === 'failed' && !empty($cl['ai_error_message'])){ ?>
                  <div class="text-danger" style="font-size:10px; margin-top:4px;">
                    <i class="fa fa-exclamation-triangle"></i> <?php echo htmlspecialchars($cl['ai_error_message']); ?>
                  </div>
                <?php } ?>
              </td>
              <td style="white-space:nowrap;">
                <button type="button" class="btn btn-xs btn-default btn-regenerate-ai" data-call-id="<?php echo $cl['id']; ?>" title="Regenerate speech transcription and analysis via Groq">
                  <i class="fa fa-refresh"></i> <?php echo ($p_status === 'failed') ? 'Retry AI' : 'Regenerate'; ?>
                </button>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
</div>
</div>
<?php } ?>

<div class="modal fade" id="convertLeadModal" tabindex="-1" role="dialog" aria-labelledby="convertLeadModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="border-radius:6px; border:1px solid #111827;">
      <form action="<?php echo base_url(); ?>manager/leads/convert_to_lead" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="lead_id" value="<?php echo $p_info['id']; ?>">
        <div class="modal-header" style="border-bottom:1px solid #111827; background:#000000; border-top-left-radius:6px; border-top-right-radius:6px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#ffffff; opacity:0.8;"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="convertLeadModalLabel" style="font-weight:700; color:#ffffff;"><i class="fa fa-check-circle" style="color:#ffffff;"></i> Convert to Lead</h4>
        </div>
        <div class="modal-body" style="padding:20px; background:#ffffff; color:#111827;">
          <p style="font-size:14px; color:#334155; margin-bottom:15px;">
            Qualifying <strong><?php echo htmlspecialchars($p_info['customer']); ?></strong> (Record #<?php echo htmlspecialchars($p_info['lead_id']); ?>) as a verified Lead.
          </p>
          <div class="form-group" style="margin-bottom:0;">
            <label style="font-weight:600; color:#0f172a;">Qualification Remarks</label>
            <textarea class="form-control" name="remarks" rows="3" placeholder="Enter qualification remarks or rationale for converting into a Lead..."></textarea>
          </div>
        </div>
        <div class="modal-footer" style="border-top:1px solid #e5e7eb; background:#f9fafb; border-bottom-left-radius:6px; border-bottom-right-radius:6px;">
          <button type="button" class="btn btn-default" data-dismiss="modal" style="background:#ffffff; border:1px solid #d1d5db; color:#111827;">Cancel</button>
          <button type="submit" class="btn btn-default" style="background:#000000; border-color:#000000; color:#ffffff; font-weight:600;">Confirm Conversion</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="requestLeadModal" tabindex="-1" role="dialog" aria-labelledby="requestLeadModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="border-radius:6px; border:1px solid #111827;">
      <form action="<?php echo base_url(); ?>manager/leads/request_conversion" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="lead_id" value="<?php echo $p_info['id']; ?>">
        <div class="modal-header" style="border-bottom:1px solid #111827; background:#000000; border-top-left-radius:6px; border-top-right-radius:6px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#ffffff; opacity:0.8;"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="requestLeadModalLabel" style="font-weight:700; color:#ffffff;"><i class="fa fa-paper-plane" style="color:#ffffff;"></i> Request Conversion to Lead</h4>
        </div>
        <div class="modal-body" style="padding:20px; background:#ffffff; color:#111827;">
          <p style="font-size:14px; color:#334155; margin-bottom:15px;">
            Submit a formal conversion request for <strong><?php echo htmlspecialchars($p_info['customer']); ?></strong> (Record #<?php echo htmlspecialchars($p_info['lead_id']); ?>) to Back Office / Manager review queue.
          </p>
          <div class="form-group" style="margin-bottom:0;">
            <label style="font-weight:600; color:#0f172a;">Conversion Rationale &amp; Qualification Details</label>
            <textarea class="form-control" name="remarks" rows="3" required placeholder="Enter reasons why this database record qualifies to be a Lead..."></textarea>
          </div>
        </div>
        <div class="modal-footer" style="border-top:1px solid #e5e7eb; background:#f9fafb; border-bottom-left-radius:6px; border-bottom-right-radius:6px;">
          <button type="button" class="btn btn-default" data-dismiss="modal" style="background:#ffffff; border:1px solid #d1d5db; color:#111827;">Cancel</button>
          <button type="submit" class="btn btn-default" style="background:#000000; border-color:#000000; color:#ffffff; font-weight:600;">Submit Request</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="rejectLeadModal" tabindex="-1" role="dialog" aria-labelledby="rejectLeadModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="border-radius:6px; border:1px solid #111827;">
      <form action="<?php echo base_url(); ?>manager/leads/reject_conversion" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="lead_id" value="<?php echo $p_info['id']; ?>">
        <div class="modal-header" style="border-bottom:1px solid #111827; background:#000000; border-top-left-radius:6px; border-top-right-radius:6px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#ffffff; opacity:0.8;"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="rejectLeadModalLabel" style="font-weight:700; color:#ffffff;"><i class="fa fa-times-circle" style="color:#ffffff;"></i> Reject Conversion Request</h4>
        </div>
        <div class="modal-body" style="padding:20px; background:#ffffff; color:#111827;">
          <p style="font-size:14px; color:#334155; margin-bottom:15px;">
            Rejecting the conversion request for <strong><?php echo htmlspecialchars($p_info['customer']); ?></strong> (Record #<?php echo htmlspecialchars($p_info['lead_id']); ?>).
          </p>
          <div class="form-group" style="margin-bottom:0;">
            <label style="font-weight:600; color:#0f172a;">Rejection Reason</label>
            <textarea class="form-control" name="remarks" rows="3" required placeholder="Specify why the conversion request is being rejected..."></textarea>
          </div>
        </div>
        <div class="modal-footer" style="border-top:1px solid #e5e7eb; background:#f9fafb; border-bottom-left-radius:6px; border-bottom-right-radius:6px;">
          <button type="button" class="btn btn-default" data-dismiss="modal" style="background:#ffffff; border:1px solid #d1d5db; color:#111827;">Cancel</button>
          <button type="submit" class="btn btn-default" style="background:#000000; border-color:#000000; color:#ffffff; font-weight:600;">Reject Request</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="requestOrderModal" tabindex="-1" role="dialog" aria-labelledby="requestOrderModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="border-radius:6px; border:1px solid #111827;">
      <form action="<?php echo base_url(); ?>manager/leads/request_order" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="lead_id" value="<?php echo $p_info['id']; ?>">
        <div class="modal-header" style="border-bottom:1px solid #111827; background:#000000; border-top-left-radius:6px; border-top-right-radius:6px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#ffffff; opacity:0.8;"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="requestOrderModalLabel" style="font-weight:700; color:#ffffff;"><i class="fa fa-shopping-cart" style="color:#ffffff;"></i> Request Lead to Order Conversion</h4>
        </div>
        <div class="modal-body" style="padding:20px; background:#ffffff; color:#111827;">
          <p style="font-size:14px; color:#334155; margin-bottom:15px;">
            Submitting <strong><?php echo htmlspecialchars($p_info['customer']); ?></strong> (Lead Ref: <?php echo htmlspecialchars($lead_ref_no); ?>) for Order creation.
            Once submitted, this Lead will be locked while Back Office processes the Order.
          </p>
          <div class="form-group">
            <label style="font-weight:600; color:#0f172a;">Service / Requirement</label>
            <input type="text" class="form-control" name="service_requirements" placeholder="e.g. Corporate Bank Account, Trade License Renewal, Golden Visa..." value="<?php echo htmlspecialchars($p_info['psubcat_name'] ?? ''); ?>">
          </div>
          <div class="form-group" style="margin-bottom:0;">
            <label style="font-weight:600; color:#0f172a;">Remarks / Handover Notes</label>
            <textarea class="form-control" name="remarks" rows="3" required placeholder="Enter notes, documents gathered, or special client instructions..."></textarea>
          </div>
        </div>
        <div class="modal-footer" style="border-top:1px solid #e5e7eb; background:#f9fafb; border-bottom-left-radius:6px; border-bottom-right-radius:6px;">
          <button type="button" class="btn btn-default" data-dismiss="modal" style="background:#ffffff; border:1px solid #d1d5db; color:#111827;">Cancel</button>
          <button type="submit" class="btn btn-default" style="background:#000000; border-color:#000000; color:#ffffff; font-weight:600;"><i class="fa fa-paper-plane"></i> Submit Order Request</button>
        </div>
      </form>
    </div>
  </div>
</div>


    </div>
  
  </section>
</div>

<script>
$(document).ready(function() {
  $('.btn-regenerate-ai').on('click', function() {
    var $btn = $(this);
    var callId = $btn.data('call-id');
    var orig = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
    $.ajax({
      url: '<?php echo base_url(); ?>manager/calls/ajax_process_ai',
      type: 'POST',
      data: { call_log_id: callId },
      dataType: 'json',
      success: function(res) {
        $btn.prop('disabled', false).html(orig);
        if (res && res.status) {
          alert('Success: ' + (res.message || 'AI processing completed successfully.'));
          location.reload();
        } else {
          alert('AI Processing: ' + (res && res.message ? res.message : 'Processing failed.'));
        }
      },
      error: function() {
        $btn.prop('disabled', false).html(orig);
        alert('Server communication error during AI processing.');
      }
    });
  });
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>

