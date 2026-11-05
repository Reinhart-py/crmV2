<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<!-- Content Wrapper. Contains page content -->

<style>
/* User Registration / Edit User: compact form, label beside field (look only - same fields, names and behaviour) */
.ureg .content-header{padding:10px 15px 6px}
.ureg .content-header h1{font-size:20px}
.ureg .content{padding-top:0}
.ureg .container{width:100%;max-width:1500px;padding:0 6px}
.ureg .box{margin-bottom:8px !important;border-radius:8px;box-shadow:none;border:1px solid var(--crm-line, #e2e8f0);background:var(--crm-surface, #fff);overflow:visible}
.ureg .box-header.with-border{padding:7px 12px !important;border-bottom:1px solid var(--crm-line-soft, #eef2f7);text-align:left;border-left:3px solid var(--crm-ocean, #1e3a8a);border-radius:8px 8px 0 0}
.ureg .box-header .box-title{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--crm-ink, #0f172a)}
.ureg .box-body{padding:8px 6px 2px !important;display:flex;flex-wrap:wrap}
.ureg .box-body > .row{display:flex;flex-wrap:wrap;margin:0;flex-basis:100%;width:100%}
.ureg .ureg-line{display:flex;flex-wrap:wrap;flex-basis:100%;width:100%}
.ureg .ureg-line > [class*="col-"],.ureg .box-body > [class*="col-"],.ureg .box-body > .row > *{padding:0 6px}
.ureg .box-body > :not(.row):not([class*="col-"]),.ureg .box-body > .row > :not([class*="col-"]){flex-basis:100%}
.ureg .form-group{margin-bottom:6px !important}
.ureg .form-group label{font-size:12px !important;margin-bottom:2px !important;color:var(--crm-ink-2, #334155);font-weight:600}
.ureg .form-group label span{color:#dc2626}
.ureg .form-control{height:30px !important;min-height:30px !important;padding:3px 8px !important;font-size:13px !important;border-radius:5px}
.ureg .form-control:focus{border-color:var(--crm-ocean, #1e3a8a) !important;box-shadow:0 0 0 3px var(--crm-ocean-soft, rgba(30,58,138,.12)) !important;outline:0}
.ureg .form-control[readonly],.ureg .form-control[disabled]{opacity:.75}
.ureg textarea.form-control{height:auto;min-height:30px}
.ureg .input-group{display:flex !important;width:100%}
.ureg .input-group-addon{display:flex !important;align-items:center;width:auto !important;padding:0 !important;border:0 !important;background:none !important}
.ureg .input-group-addon select{height:30px !important;max-width:115px !important;font-size:12px !important;border:1px solid var(--crm-line, #cbd5e1) !important;border-right:0 !important;border-radius:5px 0 0 5px !important;padding:0 4px !important;background:var(--crm-canvas, #f8fafc) !important;color:inherit}
.ureg .input-group > .form-control{flex:1 1 auto;min-width:0;width:auto !important;border-radius:0 5px 5px 0 !important}
.ureg .select2-container .select2-selection--single{height:30px !important}
/* Sub-groups inside a section */
.ureg .ureg-sub{margin:4px 6px 6px;padding-bottom:3px;border-bottom:1px dashed var(--crm-line, #e2e8f0);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--crm-ocean, #1e3a8a)}
.ureg .ureg-sub:first-child{margin-top:0}
/* Buttons always in view at the bottom */
.ureg-footer.box-footer{position:sticky;bottom:0;z-index:20;display:flex;justify-content:center;gap:10px;padding:10px 12px !important;margin:6px 0 12px;border:1px solid var(--crm-line, #e2e8f0);border-radius:8px;background:var(--crm-surface, #fff);box-shadow:0 -6px 16px rgba(15,23,42,.08)}
.ureg-footer.box-footer .btn{min-width:110px;height:34px;font-weight:600}
/* Label on the left, field on the right */
.ureg .form-group{display:grid;grid-template-columns:112px minmax(0,1fr);column-gap:8px;align-items:center;position:relative}
.ureg .form-group > label{margin:0 !important;line-height:1.2;text-align:right;word-break:break-word}
.ureg .form-group > :not(label){grid-column:2}
.ureg .form-group .btn-pwd-eye{top:50% !important;transform:translateY(-50%);bottom:auto !important}
.ureg .form-group .select2-container{width:100% !important}
@media (max-width:767px){.ureg .box-body [class*="col-"]{flex:0 0 100%;width:100%}}
@media (min-width:1200px){.ureg .col-lg-4{width:25%}}
@media (min-width:1200px) and (max-width:1399px){.ureg .form-group{grid-template-columns:96px minmax(0,1fr)}}
@media (max-width:575px){.ureg .form-group{grid-template-columns:1fr}.ureg .form-group > label{text-align:left;margin-bottom:2px !important}.ureg .form-group > :not(label){grid-column:1}}
.ureg-progress{display:flex;align-items:center;gap:10px;margin-right:auto;font-size:12px;color:var(--crm-ink-2,#334155);min-width:0}
.ureg-progress .bar{width:160px;height:6px;border-radius:3px;background:var(--crm-line,#e2e8f0);overflow:hidden;flex:0 0 auto}
.ureg-progress .bar span{display:block;height:100%;width:0;background:#f59e0b;transition:width .25s}
.ureg-progress.done .bar span{background:#16a34a}
.ureg-progress .todo{color:var(--crm-muted,#64748b);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.ureg-progress .cnt{white-space:nowrap}
@media (max-width:575px){.ureg-progress .bar,.ureg-progress .todo{display:none}.ureg-footer.box-footer .btn{min-width:84px}}
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
     
      <form role="form" action="<?php echo base_url(); ?>manager/users/add" method="post" name="add_form" autocomplete="off">

         

 <div class="box box-default js-sys-box">
      <div class="box-header with-border">
        <h3 class="box-title">System Details</h3>
      </div>
      
      <!-- /.box-header -->
      
      <div class="box-body" >
      
        <div class="row">
        <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Login Email<span>*</span></label>
                <input type="email" class="form-control c_username" name="c_username" required="" onblur="check_cname()" autocomplete="off">
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Password<span>*</span></label>
                <input type="password" class="form-control" name="c_password" required="" autocomplete="new-password">
              </div>
            </div>
         <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>User Type<span>*</span></label>
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
         
          
            <div class="ureg-line">
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Name<span>*</span></label>
                <input type="text" name="name" class="form-control" style="text-transform: capitalize;" required="" >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Email<span>*</span></label>
                <input type="email" name="email" class="form-control"  required>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                 <label>Contact No<span>*</span></label>
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
                    <input type="text" class="form-control only_number" name="mobile" required>
                  </div> 
                  </div>                
                </div>
                <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                 <label>Alternate No</label>
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
            </div>
            <div class="ureg-line">
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Date of Birth<span>*</span></label>
                <input type="text" class="form-control datepicker" name="dob"   required>
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
                <label>Nationality<span>*</span></label>
                 <select class="form-control" name="national" required>  
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
            </div>
            <div class="ureg-line">
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
                <label>State / Emirate<span>*</span></label>
                 <select id="state" class="form-control" name="state" required >
                 <option value="">Select</option> 
                 </select>
                
              </div>
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
         
            <div class="ureg-line">
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Employee ID<span>*</span></label>
                <input type="text" class="form-control" name="emp_id"   required>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Department<span>*</span></label>
                <select class="form-control" style="width: 100%;" name="department" required>
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
                <label>Office / Location<span>*</span></label>
                <select class="form-control" style="width: 100%;" name="office" required>
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
              <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Salary</label>
                <input type="text" class="form-control" name="c_salary"  >
              </div>
            </div>
            </div>
            <div class="ureg-line">
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Date of Joining<span>*</span></label>
                <input type="text" class="form-control datepicker" name="c_doj"   required>
              </div>
            </div>            
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Date of Leaving</label>
                <input type="text" class="form-control datepicker" name="c_dol"  >
              </div>
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
            <div class="ureg-sub">Identity &amp; Visa</div>
            <div class="ureg-line">
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Passport No<span>*</span></label>
                <input type="text" class="form-control" name="p_no"   required>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Passport Expiry</label>
                <input type="text" class="form-control datepicker" name="p_exdate"  >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Emirates ID</label>
                <input type="text" class="form-control" name="eid_no"  >
              </div>
            </div>
             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>ID Expiry</label>
                <input type="text" class="form-control datepicker" name="eid_expiry"  >
              </div>
            </div>
            </div>
            <div class="ureg-line">
             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Visa Type</label>
                <input type="text" class="form-control" name="visa_type"  >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Visa Ref. No</label>
                <input type="text" class="form-control" name="visa_no"  >
              </div>
            </div>
             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Visa Issued By</label>
                <input type="text" class="form-control" name="visa_by"  >
              </div>
            </div>
             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Visa Expiry Date</label>
                <input type="text" class="form-control datepicker" name="visa_expiry"  >
              </div>
            </div>
            </div>
            <div class="ureg-sub">Emergency Contact</div>
            <div class="ureg-line">
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Contact Name<span>*</span></label>
                <input type="text" class="form-control" name="h_contact_name"   required>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Relation</label>
                <input type="text" class="form-control" name="h_contact_relation"  >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Contact No<span>*</span></label>
                <input type="text" class="form-control only_digit" name="h_contact_no"   required>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Alternate No</label>
                <input type="text" class="form-control only_digit" name="h_contact_no2"  >
              </div>
            </div>
            </div>
            <div class="ureg-sub">Bank Details</div>
            <div class="ureg-line">
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Bank Name</label>
                <input type="text" class="form-control" name="bank"  >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Account No</label>
                <input type="text" class="form-control" name="bank_no"  >
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
            <div class="ureg-line">
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
                <label>IFSC / Branch</label>
                <input type="text" class="form-control" name="ifsc"  >
              </div>
            </div>
            </div>
            
            </div>
            </div> 
           
        
        </div>
        
        <!-- /.row --> 
        
      </div>
      
      <div class="box-footer btn-center ureg-footer">
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
<script>
// A new user form must start empty: undo the browser filling in the signed-in person's own saved login.
(function () {
  var f = document.forms.add_form; if (!f) return;
  var fields = [f.querySelector('[name="c_username"]'), f.querySelector('[name="c_password"]')];
  var touched = false;
  fields.forEach(function (el) { if (el) ["keydown", "paste"].forEach(function (ev) { el.addEventListener(ev, function () { touched = true; }); }); });
  function clear() { if (touched) return; fields.forEach(function (el) { if (el && el.value) el.value = ""; }); }
  [0, 300, 1000, 2500].forEach(function (ms) { setTimeout(clear, ms); });
})();
</script>
<script>
// Bottom bar: how many fields are filled, and which sections still have empty fields. Display only.
(function () {
  var form = document.forms.add_form || document.forms.edit_form; var foot = document.querySelector(".ureg-footer");
  if (!form || !foot) return;
  var box = document.createElement("div"); box.className = "ureg-progress";
  box.innerHTML = '<div class="bar"><span></span></div><strong class="cnt"></strong><span class="todo"></span>';
  foot.insertBefore(box, foot.firstChild);
  function fields() {
    return Array.prototype.filter.call(form.elements, function (el) {
      if (!/^(INPUT|SELECT|TEXTAREA)$/.test(el.tagName)) return false;
      if (el.type === "hidden" || el.type === "submit" || el.type === "button" || el.type === "checkbox" || el.type === "radio") return false;
      if (el.disabled || el.readOnly || el.closest(".input-group-addon")) return false;
      var g = el.closest(".form-group"); return !!(g && g.offsetParent);
    });
  }
  function update() {
    var all = fields(), filled = 0, empty = {};
    all.forEach(function (el) {
      var v = (el.value || "").trim();
      if (v !== "") { filled++; return; }
      var b = el.closest(".box"); var t = b && b.querySelector(".box-title");
      var name = t ? t.textContent.trim() : "Other"; empty[name] = (empty[name] || 0) + 1;
    });
    var pct = all.length ? Math.round(filled * 100 / all.length) : 0;
    box.querySelector(".bar span").style.width = pct + "%";
    box.querySelector(".cnt").textContent = filled + " of " + all.length + " filled";
    var parts = Object.keys(empty).map(function (k) { return k.charAt(0) + k.slice(1).toLowerCase() + ": " + empty[k]; });
    box.querySelector(".todo").textContent = parts.length ? "Empty \u2013 " + parts.join(", ") : "All fields filled";
    box.classList.toggle("done", filled === all.length);
  }
  form.addEventListener("input", update); form.addEventListener("change", update);
  if (window.jQuery) jQuery(form).on("change", "select", update);
  update(); setTimeout(update, 800); setTimeout(update, 2600);
})();
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
