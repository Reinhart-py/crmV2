<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1> <?php echo $info['page_heading']; ?> </h1>
  </section>
  <section class="content  my-profile">


  <div class="container">
















  <div class=" table-border">
<div class="row">

<div class="box-header">
        <h3 class="box-title" style="text-transform:capitalize"><b>
          <?php if((isset($p_info['name']))&&(!empty($p_info['name']))){echo $p_info['name'];}else echo ""; ?>
          </b></h3>
          <?php if($this->session->userdata['manager_role']==1){ ?>
          <div class="pull-right">
          <a href="<?php echo base_url(); ?>manager/users/edit/<?php echo $p_info['id']; ?>" class="btn btn-danger">Edit User</a>
         
         </div>
          <?php } ?>
          </div>
  <div class="col-md-6">
      <div class="table-responsive border">
          <table class="table">
              <tr>
                <th scope="col">Employee ID</th>
                <td><span class="label label-primary" style="font-size:12px; letter-spacing:0.5px;"><?php echo sprintf('EMP-%05d', $p_info['id']); ?></span></td>
              </tr>
              <tr>
                <th scope="col">Name</th>
                <td><?php echo $p_info['name']; ?></td>
              </tr>
   
            
              <tr>
              <th scope="col">DOB</th>
                <td scope="col"><?php echo $p_info['dob']; ?></td>
            
              </tr>
         
              <tr>
                <th scope="col">Mobile</th>
                <td><?php echo $p_info['mobile']; ?></td>

                
              </tr>
      
          
              <tr>
              <th scope="col">Address</th>
                <td scope="col"><?php echo $p_info['address']; ?></td>
            
              </tr>
            
              <tr>
                <th scope="col">Country</th>
                <td scope="col"><?php echo $p_info['country_name']; ?></td>

                
              </tr>
      
              
            
              <tr>
              <th scope="col">User Name</th>
                <td scope="col"><?php echo $p_info['c_username']; ?></th>
            
              </tr>
          
       
              <tr>
                <th scope="col">Salary</th>
                <td><?php echo $p_info['c_salary']; ?></td>

                
              </tr>
          
          
              <tr>
              <th scope="col">Date of Leaving</th>
              <td><?php echo $p_info['c_dol']; ?>
            </td>
            
              </tr>
           
              <tr>
                <th scope="col">Office Code / Work Location</th>
                <td scope="col"><?php echo $p_info['office']; ?></td>

                
              </tr>
           

              <tr>
              <th scope="col">Blood Group</th>
                <td scope="col"><?php echo $p_info['blood']; ?></td>
            
              </tr>


              <tr>
                <th scope="col">Passport Expiry Date</th>
                <td scope="col"><?php echo $p_info['p_exdate']; ?></td>
                
              </tr>

              <tr>
              <th scope="col">Visa Reference No.</th>
              <td scope="col"><?php echo $p_info['visa_no']; ?></td>              
            
              </tr>

            <tr>
              <th scope="col">Visa Expiry Date </th>
              <td scope="col"><?php echo $p_info['visa_expiry']; ?></td>              
              </tr>

              <tr>
              <th scope="col">EID Expiry Date</th>
              <td scope="col"><?php echo $p_info['eid_expiry']; ?></td>              
            
              </tr>


              <th scope="col">Emg. Contact Person Relation</th>
              <td scope="col"><?php echo $p_info['h_contact_relation']; ?></td>              
            
              </tr>

               <tr>
              <th scope="col">Emg. Contact Person Alternate Contact No.  </th>
              <td scope="col"><?php echo $p_info['h_contact_no2']; ?></td>              
            
              </tr>

              <tr>
              <th scope="col">Bank Country  </th>
              <td scope="col"><?php echo $p_info['bank']; ?></td>              
            
              </tr>



              <tr>
              <th scope="col">IFSC/Sort Code/Branch Code</th>
              <td scope="col"><?php echo $p_info['ifsc']; ?></td>              
            
              </tr>





              <tr>
              <th scope="col">SWIFT</th>
              <td scope="col"><?php echo $p_info['swift']; ?></td>              
            
              </tr>


          </table>
      </div>
  </div>


  <div class="col-md-6">
      <div class="table-responsive border">
          <table class="table">
    
         
          <tr>
              <th scope="col">Gender</th>
                <td scope="col"><?php echo $p_info['gender']; ?></td>
              
              </tr>

               <tr>
              <th scope="col">Email</th>
                <td scope="col"><?php echo $p_info['email']; ?></td>
            
              </tr>

             <tr>
              <th scope="col">UAE Alternate Contect No</th>
                <td scope="col"><?php echo $p_info['uae_mobile']; ?></td>
              
              </tr>

            <tr>
              <th scope="col">City</th>
                <td scope="col"><?php echo $p_info['city']; ?></td>
            
              </tr>

                 <tr>
              <th scope="col">State / Province / Emirate</th>
                <td scope="col"><?php echo $p_info['state_name']; ?></td>
              
              </tr>

                <tr>
              <th scope="col">Employee ID</th>
                <td scope="col"><?php echo $p_info['emp_id']; ?></td>
            
              </tr>

              <tr>
              <th scope="col">Date of Joining</th>
              <td><?php echo $p_info['c_doj']; ?></td>
              
              </tr>

              <tr>
              <th scope="col">Department</th>
              <td scope="col"><?php echo $p_info['department']; ?></td>
            
              </tr>


              <tr>
              <th scope="col">Nationality</th>
                <td scope="col"><?php echo $p_info['national']; ?></td>
              
              </tr>
              <tr>
              <th scope="col">Passport Number</th>
                <td scope="col"><?php echo $p_info['p_no']; ?></td>
            
              </tr>
           

              
              <tr>
              <th scope="col">Visa Type</th>
              <td scope="col"><?php echo $p_info['visa_type']; ?></td>              
              </tr>
          
              <tr>
              <th scope="col">Visa Issue By</th>
              <td scope="col"><?php echo $p_info['visa_by']; ?></td>              
            
              </tr>



          
              <tr>
              <th scope="col">National ID / EID Number</th>
              <td scope="col"><?php echo $p_info['eid_no']; ?></td>              
            
              </tr>
          

              <tr>

              <th scope="col">Emg. Contact Person Name</th>
              <td scope="col"><?php echo $p_info['h_contact_name']; ?></td>              
            
              </tr>
              <tr>


              <tr>
              <th scope="col">Emg. Contact Person Contact No.  </th>
              <td scope="col"><?php echo $p_info['h_contact_no']; ?></td>              
            
              </tr>

            


              <tr>
              <th scope="col">Bank Name  </th>
              <td scope="col"><?php echo $p_info['iban']; ?></td>              
            
              </tr>





              <tr>
              <th scope="col">Bank Account Number </th>
              <td scope="col"><?php echo $p_info['iban']; ?></td>              
            
              </tr>



              <tr>
              <th scope="col">IBAN </th>
              <td scope="col"><?php echo $p_info['iban']; ?></td>              
            
              </tr>




          </table>
      </div>
  </div>

</div>
</div>
























  </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
