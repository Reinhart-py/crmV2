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
/* Presentation: one font, calmer labels, slightly larger text in the fields */
.ureg, .ureg .content-header h1, .ureg .form-control, .ureg .btn, .ureg select, .ureg-footer{font-family:"Plus Jakarta Sans","Inter",-apple-system,"Segoe UI",sans-serif}
.ureg .content-header h1{text-align:left;font-size:22px !important;font-weight:700;letter-spacing:-.02em;color:var(--crm-ink,#0f172a)}
.ureg .box-header .box-title{font-size:12.5px;letter-spacing:.06em}
.ureg .form-group > label{font-size:12.5px !important;font-weight:500 !important;color:var(--crm-ink-2,#475569) !important}
.ureg .form-group > label span{font-weight:700}
.ureg .form-control{font-size:13.5px !important;color:var(--crm-ink,#0f172a)}
.ureg .form-control::placeholder{color:var(--crm-muted,#94a3b8);opacity:1}
.ureg .ureg-sub{font-size:11.5px;letter-spacing:.07em}
.ureg-progress{font-size:12.5px}
</style>
  <div class="content-wrapper ureg">

    <!-- Content Header (Page header) -->

    <section class="content-header">
      <h1>Edit User</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i>Home</a></li>
        <li class="active">Users</li>
      </ol>
    </section>


    <!-- Main content -->
     <section class="content"> 
    
    <!-- SELECT2 EXAMPLE -->
    
   <div class="container">
     <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> <?php echo get_message($this->session->flashdata('message'),'message'); ?>
     
      <form role="form" action="<?php echo base_url().$base; ?>/edit/<?php echo $list['id']; ?>" method="post" name="edit_form" autocomplete="off">


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
                <input  class="form-control" value="<?php echo $list['c_username']; ?>" readonly>
              </div>
            </div>
        
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>User Type<span>*</span></label>
                <select class="form-control" name="user_type">
                          
                            <?php if(isset($r_list) && !empty($r_list)){
                              foreach($r_list as $cli){
                            ?>
                            <option <?php if($list['user_type']==$cli['id']){ echo "selected"; } ?> value="<?php echo $cli['id']; ?>" ><?php echo $cli['title']; ?></option>
                        <?php } }  ?>
                      </select>
              </div>
            </div>

             <div class="col-lg-4 col-sm-6 js-manager-col">
              <div class="form-group select">
                <label>Upline / Manager<span>*</span></label>
                <select class="form-control select2" required="" name="manager">                          

                            <?php if(isset($u_list) && !empty($u_list)){
                              foreach($u_list as $cli){
                            ?>
                            <option <?php if($list['manager']==$cli['id']){ echo "selected"; } ?> value="<?php echo $cli['id']; ?>" ><?php echo $cli['name']; ?></option>
                        <?php } }  ?>
                      </select>
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Status</label>
                <select class="form-control" name="admin_status">

                      <option value="1" <?php if($list['admin_status']==1){ echo "selected"; } ?>>Active</option>

                      <option value="0" <?php if($list['admin_status']==0){ echo "selected"; } ?>>Inactive</option>

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
                <input type="text" name="name" class="form-control" maxlength="100" placeholder="e.g. Dubai Office TV" value="<?php echo htmlspecialchars((string)$list['name']); ?>">
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Password<span>*</span></label>
                <?php $has_pw = !empty($list['c_password']) && $list['c_password'] !== sha1(''); ?>
                <input type="password" name="pres_password" class="form-control" autocomplete="new-password" <?php echo $has_pw ? 'placeholder="Leave empty to keep the current password"' : 'required placeholder="Set a password to sign in"'; ?>>
              </div>
            </div>
          </div>
          <label>Office Codes<span>*</span> <small class="text-muted" style="font-weight:normal;">(one or more)</small></label>
          <div class="pres-offices">
            <?php foreach ($office_codes as $oc) { ?>
            <label class="pres-office"><input type="checkbox" name="live_offices[]" value="<?php echo htmlspecialchars($oc); ?>" <?php echo in_array($oc, $live_offices, true) ? 'checked' : ''; ?>> <span><?php echo htmlspecialchars($oc); ?></span></label>
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
                <input type="text" name="name" class="form-control" required value="<?php echo $list['name']; ?>" >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Email<span>*</span></label>
                <input type="email" class="form-control"  name="email"  value="<?php echo $list['email']; ?>"  required>
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
                            <option <?php if($list['country_code']==$cli['phonecode']){ echo "selected"; } ?> value="<?php echo $cli['phonecode']; ?>" ><?php echo $cli['name']; ?>[+<?php echo $cli['phonecode']; ?>]</option>
                        <?php } }  ?>
                      </select>
                        </span>
                    <input type="text" class="form-control only_number" name="mobile"  value="<?php echo $list['mobile']; ?>" required>
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
                            <option  <?php if($list['country_code2']==$cli['phonecode']){ echo "selected"; } ?> value="<?php echo $cli['phonecode']; ?>" ><?php echo $cli['name']; ?>[+<?php echo $cli['phonecode']; ?>]</option>
                        <?php } }  ?>
                      </select>
                        </span>
                    <input type="text" class="form-control only_number" name="uae_mobile"  value="<?php echo $list['uae_mobile']; ?>">
                  </div> 
                  </div>                
                </div>
            </div>
            <div class="ureg-line">
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Date of Birth<span>*</span></label>
                <input type="text" class="form-control" name="dob" value="<?php echo $list['dob']; ?>"   required>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Gender</label>
                <select class="form-control" style="width: 100%;" name="gender">
                  <option <?php if($list['gender']=='Male'){ echo "selected"; } ?> value="Male">Male</option>
                  <option <?php if($list['gender']=='Female'){ echo "selected"; } ?> value="Female">Female</option>
                  <option <?php if($list['gender']=='Other'){ echo "selected"; } ?> value="Other">Other</option>
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
                            <option <?php if($list['national']==$cli['name']){ echo "selected"; } ?> value="<?php echo $cli['name']; ?>" ><?php echo $cli['name']; ?></option>
                        <?php } }  ?>
                      </select>
              </div>
            </div>
             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Blood Group</label>
               <select class="form-control" style="width: 100%;" name="blood">
               <option value="">Select</option>
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
            </div>
            <div class="ureg-line">
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Address</label>
                <input type="text" class="form-control"  name="address" value="<?php echo $list['address']; ?>" />
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>City</label>
                <input type="text" class="form-control" name="city" value="<?php echo $list['city']; ?>"  >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Country<span>*</span></label>
                <select id="country" class="form-control" required name="country" onchange="get_state()">                          
                      <option value="">Select</option>
                            <?php if(isset($c_list) && !empty($c_list)){
                              foreach($c_list as $cli){
                            ?>
                            <option  <?php if($list['country']==$cli['id']){ echo "selected"; } ?> value="<?php echo $cli['id']; ?>" ><?php echo $cli['name']; ?></option>
                        <?php } }  ?>
                      </select>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>State / Emirate<span>*</span></label>
                 <select id="state" class="form-control" required name="state">
                 <option value="">Select</option>
                 <?php if(isset($state_list) && !empty($state_list)){
                              foreach($state_list as $cli){
                            ?>
                            <option <?php if($list['state']==$cli['id']){ echo "selected"; } ?> value="<?php echo $cli['id']; ?>" ><?php echo $cli['name']; ?></option>
                        <?php } }  ?>
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
                <input type="text" class="form-control" name="emp_id" value="<?php echo $list['emp_id']; ?>"   required>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Department<span>*</span></label>
                <select class="form-control" style="width: 100%;" name="department" required>
                <option value="">Select</option>
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
  <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Office / Location<span>*</span></label>
                <select class="form-control" style="width: 100%;" name="office" required>
                <option value="">Select</option>
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
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Salary</label>
                <input type="text" class="form-control" name="c_salary" value="<?php echo $list['c_salary']; ?>"  >
              </div>
            </div>
            </div>
            <div class="ureg-line">
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Designation<span>*</span></label>
                <input type="text" class="form-control" name="designation" maxlength="150" required list="designation_list" autocomplete="off" placeholder="e.g. Sales Executive" value="<?php echo htmlspecialchars(isset($list['designation']) ? $list['designation'] : '', ENT_QUOTES, 'UTF-8'); ?>">
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Date of Joining<span>*</span></label>
                <input type="text" class="form-control datepicker" name="c_doj"  value="<?php echo $list['c_doj']; ?>"  required>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Date of Leaving</label>
                <input type="text" class="form-control datepicker" name="c_dol" value="<?php echo $list['c_dol']; ?>" >
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
                <input type="text" class="form-control" name="p_no" value="<?php echo $list['p_no']; ?>"  required>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Passport Expiry</label>
                <input type="text" class="form-control datepicker" name="p_exdate" value="<?php echo $list['p_exdate']; ?>"  >
              </div>
            </div>
             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Emirates ID</label>
                <input type="text" class="form-control" name="eid_no" value="<?php echo $list['eid_no']; ?>" >
              </div>
            </div>
             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>ID Expiry</label>
                <input type="text" class="form-control datepicker" name="eid_expiry" value="<?php echo $list['eid_expiry']; ?>" >
              </div>
            </div>
            </div>
            <div class="ureg-line">
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Visa Type</label>
                <input type="text" class="form-control" name="visa_type" value="<?php echo $list['visa_type']; ?>"  >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Visa Ref. No</label>
                <input type="text" class="form-control" name="visa_no" value="<?php echo $list['visa_no']; ?>" >
              </div>
            </div>
             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Visa Issued By</label>
                <input type="text" class="form-control" name="visa_by" value="<?php echo $list['visa_by']; ?>"  >
              </div>
            </div>
             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Visa Expiry Date</label>
                <input type="text" class="form-control datepicker" name="visa_expiry"  value="<?php echo $list['visa_expiry']; ?>" >
              </div>
            </div>
            </div>
            <div class="ureg-sub">Emergency Contact</div>
            <div class="ureg-line">
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Contact Name<span>*</span></label>
                <input type="text" class="form-control" name="h_contact_name" value="<?php echo $list['h_contact_name']; ?>"  required>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Relation</label>
                <input type="text" class="form-control" name="h_contact_relation" value="<?php echo $list['h_contact_relation']; ?>" >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Contact No<span>*</span></label>
                <input type="text" class="form-control only_digit" name="h_contact_no" value="<?php echo $list['h_contact_no']; ?>"  required>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Alternate No</label>
                <input type="text" class="form-control only_digit" name="h_contact_no2" value="<?php echo $list['h_contact_no2']; ?>" >
              </div>
            </div>
            </div>
            <div class="ureg-sub">Bank Details</div>
            <div class="ureg-line">
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Bank Name</label>
                <input type="text" class="form-control" name="bank" value="<?php echo $list['h_contact_no2']; ?>" >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Account No</label>
                <input type="text" class="form-control" name="bank_no" value="<?php echo $list['bank_no']; ?>" >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>IBAN</label>
                <input type="text" class="form-control" name="iban" value="<?php echo $list['iban']; ?>" >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>SWIFT</label>
                <input type="text" class="form-control" name="swift" value="<?php echo $list['swift']; ?>" >
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
                            <option <?php if($list['bank_country']==$cli['name']){ echo "selected"; } ?> value="<?php echo $cli['name']; ?>" ><?php echo $cli['name']; ?></option>
                        <?php } }  ?>
                      </select>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>IFSC / Branch</label>
                <input type="text" class="form-control" name="ifsc" value="<?php echo $list['ifsc']; ?>" >
              </div>
            </div>
            </div>
            
            
           
           
        
        </div>
        
        <!-- /.row --> 
        
      </div>
      
      <div class="box-footer text-center ureg-footer">
                <button type="submit" class="btn btn-info">Submit</button>
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
</script>


<script>
// Presentation user type: hide the personal, company and other details (and the upline);
// show only the screen name and the office codes.
(function () {
  var PRES = <?php echo (int)$pres_role_id; ?>;
  var form = $('form[name=edit_form]');
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
<script>
// Employee ID and Passport No must be unique: checked while typing, and saving is stopped while one is taken.
(function () {
  var form = document.forms.add_form || document.forms.edit_form; if (!form) return;
  var selfId = <?php echo isset($list['id']) ? (int)$list['id'] : 0; ?>;
  var labels = { emp_id: 'Employee ID', p_no: 'Passport No' }, state = {}, pending = {};
  Object.keys(labels).forEach(function (name) {
    var el = form.querySelector('[name="' + name + '"]'); if (!el) return;
    var msg = document.createElement('div');
    msg.className = 'ureg-dup'; msg.style.cssText = 'grid-column:2;color:#dc2626;font-size:11.5px;margin-top:2px;display:none';
    el.parentNode.appendChild(msg);
    function check() {
      var v = el.value.trim();
      if (!v) { state[name] = false; msg.style.display = 'none'; return; }
      pending[name] = $.post('<?php echo base_url(); ?>ci_admin_user/check_unique',
        { field: name, value: v, id: selfId, <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>' })
        .done(function (r) {
          state[name] = ($.trim(r) === 'taken');
          msg.textContent = labels[name] + ' "' + v + '" is already used by another user.';
          msg.style.display = state[name] ? 'block' : 'none';
          el.style.borderColor = state[name] ? '#dc2626' : '';
        })
        .always(function () { pending[name] = null; });
    }
    el.addEventListener('change', check);
    if (el.value.trim()) check();
  });
  // Runs before the form's own checks and saving.
  document.addEventListener('submit', function (e) {
    if (e.target !== form) return;
    var bad = Object.keys(state).filter(function (k) { return state[k]; });
    if (bad.length) {
      e.preventDefault(); e.stopImmediatePropagation();
      var el = form.querySelector('[name="' + bad[0] + '"]'); if (el) { el.scrollIntoView({ block: 'center' }); el.focus(); }
    }
  }, true);
})();
</script>
<datalist id="designation_list"><?php if (!empty($designations)) foreach ($designations as $dg) { ?><option value="<?php echo htmlspecialchars($dg, ENT_QUOTES, 'UTF-8'); ?>"><?php } ?></datalist>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>

 