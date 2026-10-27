<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1> <?php echo $info['page_heading']; ?> </h1>
  </section>
  <section class="content my-profile">

  <div class="container">



<div class=" table-border">
<div class="row">

<div class="box-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; padding:15px; border-bottom:1px solid #e2e8f0;">
  <div style="display:flex; align-items:center; gap:15px;">
    <div style="position:relative; width:64px; height:64px; border-radius:50%; overflow:hidden; border:2px solid #e2e8f0; background:#f8fafc;">
      <img id="profileAvatarImg" src="<?php echo (!empty($p_info['avatar']) && file_exists(FCPATH . $p_info['avatar'])) ? base_url() . $p_info['avatar'] : base_url() . 'assets/dist/img/pfp_male_1.svg'; ?>" style="width:100%; height:100%; object-fit:cover;" />
    </div>
    <div>
      <h3 class="box-title" style="text-transform:capitalize; margin:0 0 4px 0; font-weight:700;">
        <?php if((isset($p_info['name']))&&(!empty($p_info['name']))){echo htmlspecialchars($p_info['name']);}else echo ""; ?>
      </h3>
      <?php if (!empty($p_info['role_title'])) { ?>
        <div style="font-size:12px; font-weight:700; color:#0072f0; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.5px;"><?php echo htmlspecialchars($p_info['role_title']); ?></div>
      <?php } ?>
      <span style="font-size:12px; color:#64748b;"><?php echo htmlspecialchars($p_info['email'] ?? ''); ?></span>
    </div>
  </div>
  <div style="display:flex; gap:8px;">
    <label class="btn btn-sm btn-default" style="margin-bottom:0; cursor:pointer;" title="Upload or Replace Profile Photo">
      <i class="fa fa-camera"></i> Change Photo
      <input type="file" id="ajaxAvatarInput" accept="image/jpeg,image/png,image/webp" style="display:none;" />
    </label>
    <a href="<?php echo base_url(); ?>manager/profile?edit=1" class="btn btn-sm btn-primary">
      <i class="fa fa-pencil"></i> Edit Profile Details
    </a>
  </div>
</div>
  <div class="col-md-6">
      <div class="table-responsive border">
          <table class="table">
        
              <tr>
                <th scope="col">Name</th>
                <td><?php echo $p_info['name']; ?></td>
              </tr>

              <?php if (!empty($p_info['role_title'])) { ?>
              <tr>
                <th scope="col">Role</th>
                <td><span class="label label-primary" style="background:#0072f0; font-size:11px; padding:3px 8px;"><?php echo htmlspecialchars($p_info['role_title']); ?></span></td>
              </tr>
              <?php } ?>
   
            
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

    <!-- <div class="box product-box-default box-solid">
      <div class="box-header with-border">
        <h3 class="box-title" style="text-transform:capitalize"><b>
          <?php //if((isset($p_info['name']))&&(!empty($p_info['name']))){echo $p_info['name'];}else echo ""; ?>
          </b></h3>
          
          </div>
        <div class="box-body">
          <div class="row user-view-information">

              <div class="col-xs-4 col-md-2">Name</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['name']; ?></div>
              <div class="col-xs-4 col-md-2">Gender</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['gender']; ?></div>

              <div class="col-xs-4 col-md-2">DOB</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['dob']; ?></div>

              <div class="col-xs-4 col-md-2">Email</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['email']; ?></div>


              <div class="col-xs-4 col-md-2">Mobile</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['mobile']; ?></div>

              <div class="col-xs-4 col-md-3">UAE Alternate Contect No</div>
              <div class="col-xs-8 col-md-3"><?//php echo $p_info['uae_mobile']; ?></div>


              <div class="col-xs-4 col-md-2">Address</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['address']; ?></div>

              <div class="col-xs-4 col-md-2">City</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['city']; ?></div>

              <div class="col-xs-4 col-md-2">Country</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['country_name']; ?></div>

              <div class="col-xs-4 col-md-2">State / Province / Emirate</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['state_name']; ?></div>


              <div class="col-xs-4 col-md-2">User Name</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['c_username']; ?></div>
              <div class="col-xs-4 col-md-2">Employee ID</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['emp_id']; ?></div>
              <div class="col-xs-4 col-md-2">Salary</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['c_salary']; ?></div>
              <div class="col-xs-4 col-md-2">Date of Joining</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['c_doj']; ?></div>
              <div class="col-xs-4 col-md-2">Date of Leaving</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['c_dol']; ?></div>
              <div class="col-xs-4 col-md-2">Department</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['department']; ?></div>
              <div class="col-xs-4 col-md-2">Office Code / Work Location</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['office']; ?></div>
              <div class="col-xs-4 col-md-2">Nationality</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['national']; ?></div>
              <div class="col-xs-4 col-md-2">Blood Group</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['blood']; ?></div>
              <div class="col-xs-4 col-md-2">Passport Number</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['p_no']; ?></div>


              <div class="col-xs-4 col-md-2">Passport Expiry Date</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['p_exdate']; ?></div>

              <div class="col-xs-4 col-md-2">Visa Type</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['visa_type']; ?></div>


              <div class="col-xs-4 col-md-2">Visa Reference No.</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['visa_no']; ?></div>

              <div class="col-xs-4 col-md-2">Visa Issue By</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['visa_by']; ?></div>

              <div class="col-xs-4 col-md-2">Visa Expiry Date</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['visa_expiry']; ?></div>

              <div class="col-xs-4 col-md-2">National ID / EID Number</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['eid_no']; ?></div>

              <div class="col-xs-4 col-md-2">EID Expiry Date</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['eid_expiry']; ?></div>

              <div class="col-xs-4 col-md-3">Emg. Contact Person Name</div>
              <div class="col-xs-8 col-md-3"><?//php echo $p_info['h_contact_name']; ?></div>


              <div class="col-xs-4 col-md-3">Emg. Contact Person Relation</div>
              <div class="col-xs-8 col-md-3"><?//php echo $p_info['h_contact_relation']; ?></div>

              <div class="col-xs-4 col-md-3">Emg. Contact Person Contact No.</div>
              <div class="col-xs-8 col-md-3"><?//php echo $p_info['h_contact_no']; ?></div>

              <div class="col-xs-4 col-md-3">Emg. Contact Person Alternate Contact No.</div>
              <div class="col-xs-8 col-md-3"><?//php echo $p_info['h_contact_no2']; ?></div>

              <div class="col-xs-4 col-md-2">Bank Name</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['bank']; ?></div>

              <div class="col-xs-4 col-md-2">Bank Country</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['bank_country']; ?></div>

              <div class="col-xs-4 col-md-2">Bank Account Number</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['bank_no']; ?></div>

              <div class="col-xs-4 col-md-3">IFSC/Sort Code/Branch Code</div>
              <div class="col-xs-8 col-md-3"><?//php echo $p_info['ifsc']; ?></div>

              <div class="col-xs-4 col-md-2">IBAN</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['iban']; ?></div>

              <div class="col-xs-4 col-md-2">SWIFT</div>
              <div class="col-xs-8 col-md-4"><?//php echo $p_info['swift']; ?></div>





          </div>
        </div>
      </div>
    </div>
   
  </section>
</div>

<script>
$(document).ready(function() {
  $('#ajaxAvatarInput').on('change', function() {
    var file = this.files[0];
    if (!file) return;
    var formData = new FormData();
    formData.append('avatar', file);
    $.ajax({
      url: '<?php echo base_url(); ?>manager/profile/upload_avatar',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function(res) {
        if (res && res.status && res.avatar_url) {
          $('#profileAvatarImg').attr('src', res.avatar_url);
          $('.profile-avatar-img').attr('src', res.avatar_url);
        } else {
          alert(res && res.message ? res.message : 'Error uploading profile photo');
        }
      },
      error: function() {
        alert('Server communication error uploading profile photo');
      }
    });
  });
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>

