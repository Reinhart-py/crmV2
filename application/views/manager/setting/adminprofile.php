<?php require_once(APPPATH."views/manager/elements/header.php"); ?>



  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

      <h1> My Profile  </h1>

      <ol class="breadcrumb">

        <li><a href="#"><i class="fa fa-dashboard"></i>Home</a></li>

        <li class="active">Users</li>

      </ol>

    </section>



    <!-- Main content --> 

     <section class="content"> 
    
    <!-- SELECT2 EXAMPLE -->
    
   
     <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> <?php echo get_message($this->session->flashdata('message'),'message'); ?>
     
      <form role="form" action="<?php echo base_url(); ?>manager/profile" method="post" name="edit_form" enctype="multipart/form-data">

      
       <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Personal Details</h3>
      </div>
      
      <div class="box-body" >
      
        <div class="row">
         
            <div class="col-md-12">
              <div class="form-group" style="display:flex; align-items:center; gap:16px; margin-bottom:20px;">
                <div style="width:64px; height:64px; border-radius:50%; overflow:hidden; border:2px solid #e2e8f0; background:#f8fafc; flex-shrink:0;">
                  <img src="<?php echo (!empty($list['avatar']) && file_exists(FCPATH . $list['avatar'])) ? base_url() . $list['avatar'] : base_url() . 'assets/dist/img/pfp_male_1.svg'; ?>" style="width:100%; height:100%; object-fit:cover;" />
                </div>
                <div>
                  <label style="display:block; margin-bottom:4px; font-weight:600;">Profile Photo</label>
                  <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" class="form-control" style="height:auto; padding:4px;" />
                </div>
              </div>
            </div>
         
          
            <div class="col-md-6">
              <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo $list['name']; ?>" >
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Gender</label>
                <select class="form-control" style="width: 100%;" name="gender">
                  <option <?php if($list['gender']=='Male'){ echo "selected"; } ?> value="Male">Male</option>
                  <option <?php if($list['gender']=='Female'){ echo "selected"; } ?> value="Female">Female</option>
                  <option <?php if($list['gender']=='Other'){ echo "selected"; } ?> value="Other">Other</option>
                </select>
              </div>
            </div>
             <div class="col-md-6">
              <div class="form-group">
                <label>Date of Birth</label>
                <input type="text" class="form-control" name="dob" value="<?php echo $list['dob']; ?>"  >
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control"  name="email"  value="<?php echo $list['email']; ?>" >
              </div>
            </div>
             
            
            <div class="col-lg-6">
              <div class="form-group">
                 <label>Contect No</label>
                  <div class="input-group">
                        <span class="input-group-addon">
                         <select name="country_code">                          

                            <?php if(isset($c_list) && !empty($c_list)){
                              foreach($c_list as $cli){
                            ?>
                            <option value="<?php echo $cli['phonecode']; ?>" ><?php echo $cli['name']; ?>[+<?php echo $cli['phonecode']; ?>]</option>
                        <?php } }  ?>
                      </select>
                        </span>
                    <input type="text" class="form-control only_number" name="mobile"  value="<?php echo $list['mobile']; ?>">
                  </div> 
                  </div>                
                </div>

                <div class="col-lg-6">
              <div class="form-group">
                 <label>UAE Alternate Contect No</label>
                  <div class="input-group">
                        <span class="input-group-addon">
                         <select name="country_code2">                          

                            <?php if(isset($c_list) && !empty($c_list)){
                              foreach($c_list as $cli){
                            ?>
                            <option value="<?php echo $cli['phonecode']; ?>" ><?php echo $cli['name']; ?>[+<?php echo $cli['phonecode']; ?>]</option>
                        <?php } }  ?>
                      </select>
                        </span>
                    <input type="text" class="form-control only_number" name="uae_mobile"  value="<?php echo $list['uae_mobile']; ?>">
                  </div> 
                  </div>                
                </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Address</label>
                <input type="text" class="form-control"  name="address" value="<?php echo $list['address']; ?>" />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>City</label>
                <input type="text" class="form-control" name="city" value="<?php echo $list['city']; ?>"  >
              </div>
            </div>
           
           
            <div class="col-md-6">
              <div class="form-group">
                <label>Country</label>
                <select id="country" class="form-control" name="country" onchange="get_state()">                          

                            <?php if(isset($c_list) && !empty($c_list)){
                              foreach($c_list as $cli){
                            ?>
                            <option value="<?php echo $cli['id']; ?>" ><?php echo $cli['name']; ?></option>
                        <?php } }  ?>
                      </select>
              </div>
            </div>


            <div class="col-md-6">
              <div class="form-group">
                <label>State</label>
                 <select id="state" class="form-control" name="state">
                 <option value="">-Select-</option> 
                 </select>
                
              </div>
            </div>
           </div>
           </div>
           
           </div>
           
            <div class="box box-default">
           
           <div class="box-header with-border">
        <h3 class="box-title">Company Details</h3>
      </div>
      
       <div class="box-body" >
      
        <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Employee ID</label>
                <input type="text" class="form-control" name="emp_id" value="<?php echo $list['emp_id']; ?>"  >
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label>Salary</label>
                <input type="text" class="form-control" name="c_salary" value="<?php echo $list['c_salary']; ?>"  >
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Date of Joining</label>
                <input type="text" class="form-control datepicker" name="c_doj"  value="<?php echo $list['c_doj']; ?>" >
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Date of Leaving</label>
                <input type="text" class="form-control datepicker" name="c_dol" value="<?php echo $list['c_dol']; ?>" >
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Department</label>
                <select class="form-control" style="width: 100%;" name="department">
                  <option  <?php if($list['department']=='Board of Director'){ echo "selected"; } ?> value="Board of Director" selected="selected">Board of Director</option>
                  <option <?php if($list['department']=='Administration'){ echo "selected"; } ?> value="Administration">Administration</option>
                  <option <?php if($list['department']=='Operations'){ echo "selected"; } ?> value="Operations">Operations</option>
                  <option <?php if($list['department']=='IT'){ echo "selected"; } ?> value="IT">IT</option>
                  <option <?php if($list['department']=='Human Resource'){ echo "selected"; } ?> value="Human Resource">Human Resource</option>
                  <option <?php if($list['department']=='Back Office'){ echo "selected"; } ?> value="Back Office">Back Office</option>
                  <option <?php if($list['department']=='Sales'){ echo "selected"; } ?> value="Sales">Sales</option>
                  <option <?php if($list['department']=='Business Development'){ echo "selected"; } ?> value="Business Development">Business Development</option>
                  <option <?php if($list['department']=='Field Staff'){ echo "selected"; } ?> value="Field Staff">Field Staff</option>
                  <option <?php if($list['department']=='Other'){ echo "selected"; } ?> value="Other">Other</option>
                </select>
              </div>
            </div>
  <div class="col-md-6">
              <div class="form-group">
                <label>Office Code / Work Location</label>
                <select class="form-control" style="width: 100%;" name="office">
                  <option <?php if($list['office']=='01 - UAE-Dubai-SZR'){ echo "selected"; } ?> value="01 - UAE-Dubai-SZR" selected="selected">01 - UAE-Dubai-SZR</option>
                  <option <?php if($list['office']=='07 - UAE-WFH'){ echo "selected"; } ?> value="07 - UAE-WFH">07 - UAE-WFH</option>
                  <option <?php if($list['office']=='08 - UAE-Remote'){ echo "selected"; } ?> value="08 - UAE-Remote">08 - UAE-Remote</option>
                  <option <?php if($list['office']=='09 - UAE-Other'){ echo "selected"; } ?> value="09 - UAE-Other">09 - UAE-Other</option>
                   <option <?php if($list['office']=='11 - IN-Jaipur'){ echo "selected"; } ?> value="11 - IN-Jaipur">11 - IN-Jaipur</option>
                    <option <?php if($list['office']=='12 - IN - Delhi'){ echo "selected"; } ?> value="12 - IN - Delhi">12 - IN - Delhi</option>
                     <option <?php if($list['office']=='13 - IN-Indore'){ echo "selected"; } ?> value="13 - IN-Indore">13 - IN-Indore</option>
                      <option <?php if($list['office']=='17 - IN-WFH'){ echo "selected"; } ?> value="17 - IN-WFH">17 - IN-WFH</option>
                       <option <?php if($list['office']=='18 - IN-Remote'){ echo "selected"; } ?> value="18 - IN-Remote">18 - IN-Remote</option>
                       <option <?php if($list['office']=='19 - IN-Other'){ echo "selected"; } ?> value="19 - IN-Other">19 - IN-Other</option>
                </select>
              </div>
            </div>
            
            </div>
             </div>
             </div>
           
            <div class="box box-default">
           
           <div class="box-header with-border">
        <h3 class="box-title">Other Details</h3>
      </div>
      <div class="box-body" >
             <div class="col-md-6">
              <div class="form-group">
                <label>Nationality</label>
                 <select class="form-control" name="national">                          

                            <?php if(isset($c_list) && !empty($c_list)){
                              foreach($c_list as $cli){
                            ?>
                            <option <?php if($list['national']==$cli['name']){ echo "selected"; } ?> value="<?php echo $cli['name']; ?>" ><?php echo $cli['name']; ?></option>
                        <?php } }  ?>
                      </select>
              </div>
            </div>
             <div class="col-md-6">
              <div class="form-group">
                <label>Blood Group</label>
               <select class="form-control" style="width: 100%;" name="blood">
                  <option <?php if($list['blood']=='O+'){ echo "selected"; } ?> value="O+">O+</option>
                 <option <?php if($list['blood']=='O-'){ echo "selected"; } ?> value="O-">O-</option>
                 <option <?php if($list['blood']=='A+'){ echo "selected"; } ?> value="A+">A+</option>
                 <option <?php if($list['blood']=='A-'){ echo "selected"; } ?> value="A-">A-</option>
                 <option <?php if($list['blood']=='B+'){ echo "selected"; } ?> value="B+">B+</option>
                 <option <?php if($list['blood']=='B-'){ echo "selected"; } ?> value="B-">B-</option>
                 <option <?php if($list['blood']=='AB+'){ echo "selected"; } ?> value="AB+">AB+</option>
                 <option <?php if($list['blood']=='AB-'){ echo "selected"; } ?> value="AB-">AB-</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>PassportNumber</label>
                <input type="text" class="form-control" name="p_no" value="<?php echo $list['p_no']; ?>" >
              </div>
            </div>
           
            <div class="col-md-6">
              <div class="form-group">
                <label>Passport Expiry Date</label>
                <input type="text" class="form-control datepicker" name="p_exdate" value="<?php echo $list['p_exdate']; ?>"  >
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Visa Type</label>
                <input type="text" class="form-control" name="visa_type" value="<?php echo $list['visa_type']; ?>"  >
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Visa Reference No</label>
                <input type="text" class="form-control" name="visa_no" value="<?php echo $list['visa_no']; ?>" >
              </div>
            </div>
             <div class="col-md-6">
              <div class="form-group">
                <label>Visa Issue By</label>
                <input type="text" class="form-control" name="visa_by" value="<?php echo $list['visa_by']; ?>"  >
              </div>
            </div>
             <div class="col-md-6">
              <div class="form-group">
                <label>Visa Expiry Date</label>
                <input type="text" class="form-control datepicker" name="visa_expiry"  value="<?php echo $list['visa_expiry']; ?>" >
              </div>
            </div>
             <div class="col-md-6">
              <div class="form-group">
                <label>National ID / EID Number</label>
                <input type="text" class="form-control" name="eid_no" value="<?php echo $list['eid_no']; ?>" >
              </div>
            </div>
             <div class="col-md-6">
              <div class="form-group">
                <label>EID Expiry Date</label>
                <input type="text" class="form-control datepicker" name="eid_expiry" value="<?php echo $list['eid_expiry']; ?>" >
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Emergency Contact Person Name</label>
                <input type="text" class="form-control" name="h_contact_name" value="<?php echo $list['h_contact_name']; ?>" >
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Emergency Contact Person Relation</label>
                <input type="text" class="form-control" name="h_contact_relation" value="<?php echo $list['h_contact_relation']; ?>" >
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Emergency Contact Person Contact Number</label>
                <input type="text" class="form-control only_digit" name="h_contact_no" value="<?php echo $list['h_contact_no']; ?>" >
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Emergency Contact Person ALternate Contact Number</label>
                <input type="text" class="form-control only_digit" name="h_contact_no2" value="<?php echo $list['h_contact_no2']; ?>" >
              </div>
            </div>
           <div class="col-md-6">
              <div class="form-group">
                <label>Bank Name</label>
                <input type="text" class="form-control" name="bank" value="<?php echo $list['h_contact_no2']; ?>" >
              </div>
            </div>
          <div class="col-md-6">
              <div class="form-group">
                <label>Bank Country</label>
                <select class="form-control" name="bank_country">                          

                            <?php if(isset($c_list) && !empty($c_list)){
                              foreach($c_list as $cli){
                            ?>
                            <option <?php if($list['bank_country']==$cli['name']){ echo "selected"; } ?> value="<?php echo $cli['name']; ?>" ><?php echo $cli['name']; ?></option>
                        <?php } }  ?>
                      </select>
              </div>
            </div>
             
             
            <div class="col-md-6">
              <div class="form-group">
                <label> Bank Account Number</label>
                <input type="text" class="form-control" name="bank_no" value="<?php echo $list['bank_no']; ?>" >
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>IFSC / Sort Code / Bank Branch Code</label>
                <input type="text" class="form-control" name="ifsc" value="<?php echo $list['ifsc']; ?>" >
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>IBAN</label>
                <input type="text" class="form-control" name="iban" value="<?php echo $list['iban']; ?>" >
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>SWIFT</label>
                <input type="text" class="form-control" name="swift" value="<?php echo $list['swift']; ?>" >
              </div>
            </div>
            
            
           
           
        
        </div>
        
        <!-- /.row --> 
        
      </div>
      
      <div class="box-footer text-center">
                <button type="submit" class="btn btn-info">Submit</button>
                <button type="reset" class="btn btn-default">Cancel</button>
              </div>
              
         
              
     </form>
  
    
    <!-- /.box --> 
    
  </section>

    <!-- /.content -->

  </div>

  <!-- /.content-wrapper -->

  


<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>

 