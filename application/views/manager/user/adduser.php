<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<!-- Content Wrapper. Contains page content -->

<style>
/* User Registration: compact layout (look only - same fields, names and behaviour) */
.ureg .content-header{padding:10px 15px 6px}
.ureg .content-header h1{font-size:20px}
.ureg .content{padding-top:0}
.ureg .container{width:100%;max-width:1500px;padding:0 6px}
.ureg .box{margin-bottom:10px !important;border-radius:6px;box-shadow:none;border:1px solid #e2e8f0}
.ureg .box-header.with-border{padding:6px 12px !important;border-bottom:1px solid #eef2f7;text-align:left}
.ureg .box-header .box-title{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:#334155}
.ureg .box-body{padding:8px 6px 0 !important;display:flex;flex-wrap:wrap}
.ureg .box-body > .row{display:flex;flex-wrap:wrap;margin:0;flex-basis:100%;width:100%}
.ureg .box-body > [class*="col-"],.ureg .box-body > .row > *{padding:0 6px}
.ureg .box-body > :not(.row):not([class*="col-"]),.ureg .box-body > .row > :not([class*="col-"]){flex-basis:100%}
.ureg .form-group{margin-bottom:7px !important}
.ureg .form-group label{font-size:12px !important;margin-bottom:2px !important;color:#475569;font-weight:600}
.ureg .form-control{height:30px !important;min-height:30px !important;padding:3px 8px !important;font-size:13px !important}
.ureg textarea.form-control{height:auto;min-height:32px}
.ureg .input-group{display:flex !important;width:100%}
.ureg .input-group-addon{display:flex !important;align-items:center;width:auto !important;padding:0 !important;border:0 !important;background:none !important}
.ureg .input-group-addon select{height:30px !important;max-width:115px !important;font-size:12px !important;border:1px solid #cbd5e1 !important;border-right:0 !important;border-radius:4px 0 0 4px !important;padding:0 4px !important;background:#f8fafc !important}
.ureg .input-group > .form-control{flex:1 1 auto;min-width:0;width:auto !important;border-radius:0 4px 4px 0 !important}
.ureg .select2-container .select2-selection--single{height:30px !important}
.ureg .box-footer{padding:8px 12px;margin-bottom:12px;border-radius:6px}
@media (max-width:767px){.ureg .box-body [class*="col-"]{flex:0 0 100%;width:100%}}
@media (min-width:1200px){.ureg .col-lg-4{width:25%}}
@media (min-width:1600px){.ureg .col-lg-4{width:20%}}
</style>
<div class="content-wrapper ureg"> 
  
  <!-- Content Header (Page header) -->
  
  <section class="content-header">
    <h1> User Registration	</h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i>Home</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>
  
  <!-- Main content -->
  
  <section class="content"> 
    <div class="container">
    
    <!-- SELECT2 EXAMPLE -->
    
   
     <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> <?php echo get_message($this->session->flashdata('message'),'message'); ?>
     
      <form role="form" action="<?php echo base_url(); ?>manager/users/add" method="post" name="add_form">

         

 <div class="box box-default js-sys-box">
      <div class="box-header with-border">
        <h3 class="box-title">System Details</h3>
      </div>
      
      <!-- /.box-header -->
      
      <div class="box-body" >
      
        <div class="row">
        <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>User Name (Official Email)<span>*</span></label>
                <input type="email" class="form-control c_username" name="c_username" required="" onblur="check_cname()"  >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Password<span>*</span></label>
                <input type="password" class="form-control" name="c_password" required=""  >
              </div>
            </div>
         <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Category / User Type<span>*</span></label>
                <select class="form-control" name="user_type" required>                          
                          <option value="">Select</option>
                            <?php if(isset($r_list) && !empty($r_list)){
                              foreach($r_list as $cli){
                            ?>
                            <option value="<?php echo $cli['id']; ?>" ><?php echo $cli['title']; ?></option>
                        <?php } }  ?>
                      </select>
              </div>
            </div>

            <div class="col-lg-4 col-sm-6 js-manager-col">
              <div class="form-group select">
                <label>Upline / Manager<span>*</span></label>
                <select class="form-control select2" required="" name="manager">                          
                <option value="">Select</option>
                            <?php if(isset($u_list) && !empty($u_list)){
                              foreach($u_list as $cli){
                            ?>
                            <option value="<?php echo $cli['id']; ?>" ><?php echo $cli['name']; ?></option>
                        <?php } }  ?>
                      </select>
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Status</label>
                <select class="form-control" name="admin_status">

                      <option value="1">Active</option>

                      <option value="0">Inactive</option>

                      </select>
              </div>
            </div>

         </div>
       </div>
     </div>

      
      <!-- Presentation (TV screen) user: only a screen name and the centers it shows. -->
      <div class="box box-default js-pres-box" style="display:none;">
        <div class="box-header with-border">
          <h3 class="box-title">Presentation Centers</h3>
        </div>
        <div class="box-body">
          <p class="text-muted" style="margin-top:0;">A Presentation user only opens the Live presentation. It shows the numbers of the office codes ticked here.</p>
          <div class="row">
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Screen Name<span>*</span></label>
                <input type="text" name="name" class="form-control" maxlength="100" placeholder="e.g. Dubai Office TV">
              </div>
            </div>
          </div>
          <label>Office Codes<span>*</span> <small class="text-muted" style="font-weight:normal;">(one or more)</small></label>
          <div class="pres-offices">
            <?php foreach ($office_codes as $oc) { ?>
            <label class="pres-office"><input type="checkbox" name="live_offices[]" value="<?php echo htmlspecialchars($oc); ?>" > <span><?php echo htmlspecialchars($oc); ?></span></label>
            <?php } ?>
          </div>
          <div class="text-danger js-pres-error" style="display:none; margin-top:6px;">Tick at least one office code.</div>
        </div>
      </div>
      <style>
        .pres-offices { display:grid; grid-template-columns:repeat(auto-fill, minmax(220px, 1fr)); gap:8px; }
        .pres-office { display:flex; align-items:center; gap:8px; margin:0; padding:9px 12px; border:1px solid #cbd5e1; border-radius:8px; background:#fff; font-weight:600; cursor:pointer; }
        .pres-office input { margin:0; }
        .pres-office span { color:#1e293b !important; }
        .pres-office:has(input:checked) { border-color:#1e3a8a; background:#eff6ff; }
      </style>
       <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Personal Details</h3>
      </div>
      
      <!-- /.box-header -->
      
      <div class="box-body" >
      
        <div class="row">
         
          
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Name<span>*</span></label>
                <input type="text" name="name" class="form-control" style="text-transform: capitalize;" required="" >
              </div>
            </div>


            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Date of Birth</label>
                <input type="text" class="form-control datepicker" name="dob"  >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Gender</label>
                <select class="form-control" style="width: 100%;" name="gender">
                  <option value="Male" selected="selected">Male</option>
                  <option value="Female">Female</option>
                  <option value="Other">Other</option>
                </select>
              </div>
            </div>
        

      

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                 <label>Contect No</label>
                  <div class="input-group">
                        <span class="input-group-addon country-code">
                         <select name="country_code">                          

                            <?php if(isset($c_list) && !empty($c_list)){
                              foreach($c_list as $cli){
                            ?>
                            <option value="<?php echo $cli['phonecode']; ?>" ><?php echo $cli['name']; ?>[+<?php echo $cli['phonecode']; ?>]</option>
                        <?php } }  ?>
                      </select>
                        </span>
                    <input type="text" class="form-control only_number" name="mobile">
                  </div> 
                  </div>                
                </div>

                <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                 <label>UAE Alternate Contect No</label>
                  <div class="input-group">
                        <span class="input-group-addon country-code">
                         <select name="country_code2">                          

                            <?php if(isset($c_list) && !empty($c_list)){
                              foreach($c_list as $cli){
                            ?>
                            <option value="<?php echo $cli['phonecode']; ?>" ><?php echo $cli['name']; ?>[+<?php echo $cli['phonecode']; ?>]</option>
                        <?php } }  ?>
                      </select>
                        </span>
                    <input type="text" class="form-control only_number" name="uae_mobile">
                  </div> 
                  </div>                
                </div>
            

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" class="form-control" />
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>City</label>
                <input type="text" class="form-control" name="city"  >
              </div>
            </div>
           

           <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Country<span>*</span></label>
                <select id="country" class="form-control" required  name="country" onchange="get_state()">                          
                        <option value="">Select</option>
                            <?php if(isset($c_list) && !empty($c_list)){
                              foreach($c_list as $cli){
                            ?>
                            <option value="<?php echo $cli['id']; ?>" ><?php echo $cli['name']; ?></option>
                        <?php } }  ?>
                      </select>
              </div>
            </div>


            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>State / Province / Emirate<span>*</span></label>
                 <select id="state" class="form-control" name="state" required >
                 <option value="">Select</option> 
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
         
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Employee ID</label>
                <input type="text" class="form-control" name="emp_id"  >
              </div>
            </div>
              <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Salary</label>
                <input type="text" class="form-control" name="c_salary"  >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Date of Joining</label>
                <input type="text" class="form-control datepicker" name="c_doj"  >
              </div>
            </div>            
            
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Date of Leaving</label>
                <input type="text" class="form-control datepicker" name="c_dol"  >
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Department</label>
                <select class="form-control" style="width: 100%;" name="department">
                <option value="">Select</option>
                  <option value="Board of Director">Board of Director</option>
                  <option value="Administration">Administration</option>
                  <option value="Operations">Operations</option>
                  <option value="IT">IT</option>
                  <option value="Human Resource">Human Resource</option>
                  <option value="Back Office">Back Office</option>
                  <option value="Sales">Sales</option>
                  <option value="Business Development">Business Development</option>
                  <option value="Field Staff">Field Staff</option>
                  <option value="Other">Other</option>
                </select>
              </div>
            </div>
  <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Office Code / Work Location</label>
                <select class="form-control" style="width: 100%;" name="office">
                <option value="">Select</option>
                  <option value="01 - UAE-Dubai-SZR">01 - UAE-Dubai-SZR</option>
                  <option value="07 - UAE-WFH">07 - UAE-WFH</option>
                  <option value="08 - UAE-Remote">08 - UAE-Remote</option>
                  <option value="09 - UAE-Other">09 - UAE-Other</option>
                   <option value="11 - IN-Jaipur">11 - IN-Jaipur</option>
                    <option value="12 - IN - Delhi">12 - IN - Delhi</option>
                     <option value="13 - IN-Indore">13 - IN-Indore</option>
                      <option value="17 - IN-WFH">17 - IN-WFH</option>
                       <option value="18 - IN-Remote">18 - IN-Remote</option>
                       <option value="19 - IN-Other">19 - IN-Other</option>
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
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Nationality</label>
                 <select class="form-control" name="national">  
                 <option value="">Select</option>                        

                            <?php if(isset($c_list) && !empty($c_list)){
                              foreach($c_list as $cli){
                            ?>
                            <option value="<?php echo $cli['name']; ?>" ><?php echo $cli['name']; ?></option>
                        <?php } }  ?>
                      </select>
              </div>
            </div>
             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Blood Group</label>
               <select class="form-control" style="width: 100%;" name="blood">
               <option value="">Select</option>
                  <option value="O+">O+</option>
                 <option value="O-">O-</option>
                 <option value="A+">A+</option>
                 <option value="A-">A-</option>
                 <option value="B+">B+</option>
                 <option value="B-">B-</option>
                 <option value="AB+">AB+</option>
                 <option value="AB-">AB-</option>
                </select>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Passport Number</label>
                <input type="text" class="form-control" name="p_no"  >
              </div>
            </div>
            
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Passport Expiry Date</label>
                <input type="text" class="form-control datepicker" name="p_exdate"  >
              </div>
            </div>
             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Visa Type</label>
                <input type="text" class="form-control" name="visa_type"  >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Visa Reference No.</label>
                <input type="text" class="form-control" name="visa_no"  >
              </div>
            </div>
             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Visa Issue By</label>
                <input type="text" class="form-control" name="visa_by"  >
              </div>
            </div>
             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Visa Expiry Date</label>
                <input type="text" class="form-control datepicker" name="visa_expiry"  >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>National ID / EID Number</label>
                <input type="text" class="form-control" name="eid_no"  >
              </div>
            </div>
             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>EID Expiry Date</label>
                <input type="text" class="form-control datepicker" name="eid_expiry"  >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Emergency Contact Person Name</label>
                <input type="text" class="form-control" name="h_contact_name"  >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Emergency Contact Person Relation</label>
                <input type="text" class="form-control" name="h_contact_relation"  >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Emergency Contact Person Contact Number</label>
                <input type="text" class="form-control only_digit" name="h_contact_no"  >
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Emergency Contact Person ALternate Contact Number</label>
                <input type="text" class="form-control only_digit" name="h_contact_no2"  >
              </div>
            </div>
           
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Bank Name</label>
                <input type="text" class="form-control" name="bank"  >
              </div>
            </div>
          <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Bank Country</label>
                <select class="form-control" name="bank_country">                          
                <option value="">Select</option>
                            <?php if(isset($c_list) && !empty($c_list)){
                              foreach($c_list as $cli){
                            ?>
                            <option value="<?php echo $cli['name']; ?>" ><?php echo $cli['name']; ?></option>
                        <?php } }  ?>
                      </select>
              </div>
            </div>
             
             
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label> Bank Account Number</label>
                <input type="text" class="form-control" name="bank_no"  >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>IFSC / Sort Code / Bank Branch Code</label>
                <input type="text" class="form-control" name="ifsc"  >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>IBAN</label>
                <input type="text" class="form-control" name="iban"  >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>SWIFT</label>
                <input type="text" class="form-control" name="swift"  >
              </div>
            </div>
            
            </div>
            </div> 
           
        
        </div>
        
        <!-- /.row --> 
        
      </div>
      
      <div class="box-footer btn-center">
                <button id="add_btn" type="submit" class="btn btn-info">Submit</button>
                <button type="reset" class="btn btn-default">Cancel</button>
              </div>
              
         
              
     </form>
  
    
    <!-- /.box --> 
    
                              </div>
 
  </section>
  
  <!-- /.content --> 
  
</div>

<!-- /.content-wrapper --> 

<script>







 $(document).ready(function () { 

  $(document).on("click", "#add_btn", function () {

    

   $('form[name=add_form]').validate({         

  submitHandler: function(form) {

      form.submit();

    }

  });

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
</script>


<script>
// Presentation user type: hide the personal, company and other details (and the upline);
// show only the screen name and the office codes.
(function () {
  var PRES = <?php echo (int)$pres_role_id; ?>;
  var form = $('form[name=add_form]');
  function isPres() { return PRES > 0 && parseInt(form.find('select[name=user_type]').val(), 10) === PRES; }
  function apply() {
    var p = isPres();
    form.find('.box').not('.js-sys-box, .js-pres-box').toggle(!p).find(':input').prop('disabled', p);
    form.find('.js-manager-col').toggle(!p).find(':input').prop('disabled', p);
    form.find('.js-pres-box').toggle(p).find(':input').prop('disabled', !p);
    form.find('.js-pres-error').hide();
  }
  form.on('change', 'select[name=user_type]', apply);
  // Bound before the validation plugin, so it can stop a submit with no office ticked.
  form.on('submit', function (e) {
    if (isPres() && !form.find('input[name="live_offices[]"]:checked').length) {
      e.preventDefault();
      e.stopImmediatePropagation();
      form.find('.js-pres-error').show();
      return false;
    }
  });
  apply();
})();
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
