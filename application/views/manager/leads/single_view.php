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
                <td><?php echo $p_info['lead_id']; ?></td>

                
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
              <th scope="col">TL Issue Date</th>
              <td scope="col"><?php echo (!empty($p_info['tl_issue_date']) && $p_info['tl_issue_date'] != '0000-00-00') ? simple_date($p_info['tl_issue_date']) : '-'; ?></td>              
              </tr>

              <tr>
              <th scope="col">TL Expire Date</th>
              <td scope="col"><?php echo (!empty($p_info['tl_expire_date']) && $p_info['tl_expire_date'] != '0000-00-00') ? simple_date($p_info['tl_expire_date']) : '-'; ?></td>              
              </tr>

            


            





              <tr>
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
              <th scope="col">Status</th>
                <td scope="col"><?php echo $p_info['status']; ?></td>
              
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
