<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1> <?php echo $info['page_heading']; ?> </h1>
  </section>
  <section class="content ">

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
         <?php if($this->session->userdata('manager_id')==$p_info['created_by']){ ?>
          <?php if($action=='tm'){ ?>
            <a href="<?php echo base_url(); ?>manager/leads/edit/<?php echo base64_encode($p_info['id']); ?>/tm" class="btn btn-danger">Edit Lead</a>
         <?php }else{ ?>
          <a href="<?php echo base_url(); ?>manager/leads/edit/<?php echo base64_encode($p_info['id']); ?>" class="btn btn-danger">Edit Lead</a>
         <?php }  ?>
         
         
         <?php } elseif(in_array($p_info['created_by'],$p_team)){ ?>
         
         <a href="<?php echo base_url(); ?>manager/leads/edit/<?php echo base64_encode($p_info['id']); ?>/tm" class="btn btn-danger">Edit Lead</a>
         <?php }else{ ?>
          <a href="<?php echo base_url(); ?>manager/leads/edit/<?php echo base64_encode($p_info['id']); ?>" class="btn btn-danger">Edit Lead</a>
          
         
         <?php } ?>
        
         
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
                <th scope="col">Lead No.</th>
                <td><?php echo $p_info['lead_id']; ?></td>
              </tr>
   
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


    
    </div>
  
  </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
