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
/* Details for Reporting: dropdown of the person's own details + verified tick */
.ureg .ureg-vw{display:flex;align-items:center;width:100%}
.ureg .ureg-vw > .form-control{flex:1 1 auto;min-width:0}
.ureg .form-group label.ureg-vf{flex:0 0 auto;display:flex !important;align-items:center;justify-content:center;width:30px;height:30px;margin:0 0 0 4px !important;border:1px solid var(--crm-line,#cbd5e1);border-radius:5px;cursor:pointer;color:var(--crm-muted,#94a3b8) !important;background:var(--crm-surface,#fff);font-size:15px !important}
.ureg .form-group label.ureg-vf input{position:absolute;opacity:0;width:0;height:0}
.ureg .form-group label.ureg-vf:hover{border-color:#16a34a}
.ureg .form-group label.ureg-vf.on{color:#fff !important;background:#16a34a;border-color:#16a34a}
.ureg .form-group label.ureg-vf.off{opacity:.45;cursor:not-allowed}
.ureg .ureg-code-err select{border-color:#dc2626 !important;color:#dc2626}
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
     
      <form role="form" action="<?php echo base_url().$base; ?>/add" method="post" name="add_form" autocomplete="off">

         

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
                <label>Gender</label>
                <select class="form-control" style="width: 100%;" name="gender">
                  <option value="" selected="selected">Select</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                  <option value="Other">Other</option>
                </select>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Date of Birth<span>*</span></label>
                <input type="text" class="form-control datepicker" name="dob"   required>
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
                <label>Email<span>*</span></label>
                <div class="ureg-vw"><input type="email" name="email" class="form-control"  required><label class="ureg-vf js-fvf" data-for="email" title="Verified: tick after confirming it with the person"><input type="checkbox" name="email_verified" value="1"><i class="fa fa-check-circle"></i></label></div>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                 <label>Contact No<span>*</span></label>
                  <div class="input-group">
                        <span class="input-group-addon country-code">
                         <select name="country_code">
                            <option value="">Code</option>
                            <?php if(isset($c_list) && !empty($c_list)){ foreach($c_list as $cli){ ?>
                            <option value="<?php echo $cli['phonecode']; ?>"><?php echo $cli['name']; ?>[+<?php echo $cli['phonecode']; ?>]</option>
                            <?php } } ?>
                          </select>
                        </span>
                    <input type="text" class="form-control only_number" name="mobile" required><label class="ureg-vf js-fvf" data-for="mobile" title="Verified: tick after confirming it with the person"><input type="checkbox" name="mobile_verified" value="1"><i class="fa fa-check-circle"></i></label>
                  </div> 
                  </div>                
                </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>WhatsApp No</label>
                <div class="input-group">
                  <span class="input-group-addon country-code">
                          <select name="whatsapp_code">
                            <option value="">Code</option>
                            <?php if(isset($c_list) && !empty($c_list)){ foreach($c_list as $cli){ ?>
                            <option value="<?php echo $cli['phonecode']; ?>"><?php echo $cli['name']; ?>[+<?php echo $cli['phonecode']; ?>]</option>
                            <?php } } ?>
                          </select>
                  </span>
                  <input type="text" class="form-control only_number" name="whatsapp" maxlength="20"><label class="ureg-vf js-fvf" data-for="wa" title="Verified: tick after confirming it with the person"><input type="checkbox" name="wa_verified" value="1"><i class="fa fa-check-circle"></i></label>
                </div>
              </div>
            </div>
                <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                 <label>Alternate No</label>
                  <div class="input-group">
                        <span class="input-group-addon country-code">
                         <select name="country_code2">
                            <option value="">Code</option>
                            <?php if(isset($c_list) && !empty($c_list)){ foreach($c_list as $cli){ ?>
                            <option value="<?php echo $cli['phonecode']; ?>"><?php echo $cli['name']; ?>[+<?php echo $cli['phonecode']; ?>]</option>
                            <?php } } ?>
                          </select>
                        </span>
                    <input type="text" class="form-control only_number" name="uae_mobile"><label class="ureg-vf js-fvf" data-for="alt" title="Verified: tick after confirming it with the person"><input type="checkbox" name="alt_verified" value="1"><i class="fa fa-check-circle"></i></label>
                  </div> 
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
            <div class="ureg-sub">Details for Reporting</div>
            <div class="ureg-line">
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Email</label>
                <div class="ureg-vw">
                  <select class="form-control js-rep" name="report_email" data-kind="email" data-cur=""><option value="">Select</option></select>
                  <label class="ureg-vf" title="Verified: tick after confirming it with the person"><input type="checkbox" name="report_email_verified" value="1"><i class="fa fa-check-circle"></i></label>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Phone No</label>
                <div class="ureg-vw">
                  <select class="form-control js-rep" name="report_phone" data-kind="phone" data-cur=""><option value="">Select</option></select>
                  <label class="ureg-vf" title="Verified: tick after confirming it with the person"><input type="checkbox" name="report_phone_verified" value="1"><i class="fa fa-check-circle"></i></label>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>WhatsApp</label>
                <div class="ureg-vw">
                  <select class="form-control js-rep" name="report_whatsapp" data-kind="phone" data-cur=""><option value="">Select</option></select>
                  <label class="ureg-vf" title="Verified: tick after confirming it with the person"><input type="checkbox" name="report_whatsapp_verified" value="1"><i class="fa fa-check-circle"></i></label>
                </div>
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
                <label>Designation<span>*</span></label>
                <input type="text" class="form-control" name="designation" maxlength="150" required list="designation_list" autocomplete="off" placeholder="e.g. Sales Executive">
              </div>
            </div>
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
            </div>
            <div class="ureg-line">
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
      if (el.tagName === "SELECT" && !el.multiple && !Array.prototype.some.call(el.options, function (o) { return o.value === ""; })) return false;
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
<script>
// Details for Reporting: each dropdown lists only this person's own email / numbers, filled from the form as it is typed.
// The tick can only be set once something is chosen, and choosing something else takes the tick off.
// A phone number also needs its country code before saving.
(function () {
  var form = document.forms.add_form || document.forms.edit_form; if (!form) return;
  function val(n) { var el = form.querySelector('[name="' + n + '"]'); return el ? el.value.trim() : ''; }
  function phone(code, num) { var c = val(code).replace(/\D+/g, ''), n = val(num).replace(/\D+/g, ''); return n ? (c ? '+' + c + ' ' : '') + n : ''; }
  function choices(kind) {
    var out = [];
    function add(label, v) { if (v && !out.some(function (o) { return o[1] === v; })) out.push([label, v]); }
    if (kind === 'email') { add('Email', val('email')); var lg = form.querySelector('[name="c_username"]') || document.getElementById('login_email'); add('Login Email', lg ? lg.value.trim() : ''); }
    else { add('Contact No', phone('country_code', 'mobile')); add('WhatsApp No', phone('whatsapp_code', 'whatsapp')); add('Alternate No', phone('country_code2', 'uae_mobile')); }
    return out;
  }
  var reps = Array.prototype.slice.call(form.querySelectorAll('select.js-rep'));
  function tickOf(sel) { return sel.parentNode.querySelector('input[type=checkbox]'); }
  function paint(sel) {
    var cb = tickOf(sel), lab = cb.closest('label');
    if (!sel.value) cb.checked = false;
    cb.disabled = !sel.value;
    lab.classList.toggle('on', cb.checked); lab.classList.toggle('off', !sel.value);
    lab.title = !sel.value ? 'Choose the ' + (sel.getAttribute('data-kind') === 'email' ? 'email' : 'number') + ' first' : (cb.checked ? 'Verified - untick if it is no longer right' : 'Not verified - tick after confirming it with the person');
  }
  function fill(sel, first) {
    var keep = first ? sel.getAttribute('data-cur') : sel.value, list = choices(sel.getAttribute('data-kind'));
    sel.innerHTML = '<option value="">Select</option>';
    list.forEach(function (o) { var op = document.createElement('option'); op.value = o[1]; op.textContent = o[1] + '  (' + o[0] + ')'; sel.appendChild(op); });
    var found = list.some(function (o) { return o[1] === keep; });
    sel.value = found ? keep : '';
    if (!found && keep) { var cb = tickOf(sel); cb.checked = false; }
    paint(sel);
  }
  reps.forEach(function (sel) {
    fill(sel, true);
    sel.addEventListener('change', function () { tickOf(sel).checked = false; paint(sel); });
    tickOf(sel).addEventListener('change', function () { paint(sel); });
  });
  var sources = ['email', 'c_username', 'country_code', 'mobile', 'country_code2', 'uae_mobile', 'whatsapp_code', 'whatsapp'];
  function refresh() { reps.forEach(function (sel) { fill(sel, false); }); }
  sources.forEach(function (n) {
    var el = form.querySelector('[name="' + n + '"]'); if (!el) return;
    el.addEventListener('input', refresh); el.addEventListener('change', refresh);
  });
  if (window.jQuery) jQuery(form).on('change', 'select[name^=country_code], select[name=whatsapp_code]', refresh);

  var phones = [['mobile', 'country_code'], ['uae_mobile', 'country_code2'], ['whatsapp', 'whatsapp_code']];
  document.addEventListener('submit', function (e) {
    if (e.target !== form) return;
    var bad = null;
    phones.forEach(function (p) {
      var num = form.querySelector('[name="' + p[0] + '"]'), code = form.querySelector('[name="' + p[1] + '"]');
      if (!num || !code || num.disabled) return;
      var miss = num.value.trim() !== '' && !code.value;
      code.closest('.input-group').classList.toggle('ureg-code-err', miss);
      if (miss && !bad) bad = code;
    });
    if (bad) { e.preventDefault(); e.stopImmediatePropagation(); bad.scrollIntoView({ block: 'center' }); bad.focus(); alert('Please choose the country code for the phone number.'); }
  }, true);
})();
</script>
<script>
// Verified ticks on Email, Contact No, WhatsApp No and Alternate No: green when ticked, only possible when filled,
// and changing the email / number (or its code) takes the tick off. Picking a verified one for reporting ticks that too.
(function () {
  var form = document.forms.add_form || document.forms.edit_form; if (!form) return;
  var src = { email: ['email'], mobile: ['mobile', 'country_code'], wa: ['whatsapp', 'whatsapp_code'], alt: ['uae_mobile', 'country_code2'] };
  function el(n) { return form.querySelector('[name="' + n + '"]'); }
  Object.keys(src).forEach(function (k) {
    var cb = el(k + '_verified'); if (!cb) return;
    var lab = cb.closest('label'), main = el(src[k][0]);
    var start = src[k].map(function (n) { var e = el(n); return e ? e.value : ''; }).join('|');
    function paint() {
      var has = main && main.value.trim() !== '';
      if (!has) cb.checked = false;
      cb.disabled = !has;
      lab.classList.toggle('on', cb.checked); lab.classList.toggle('off', !has);
      lab.title = !has ? 'Fill it in first' : (cb.checked ? 'Verified - untick if it is no longer right' : 'Not verified - tick after confirming it with the person');
    }
    function changed() {
      var now = src[k].map(function (n) { var e = el(n); return e ? e.value : ''; }).join('|');
      if (now !== start && cb.checked) cb.checked = false;
      start = now; paint();
    }
    src[k].forEach(function (n) { var e = el(n); if (!e) return; e.addEventListener('input', changed); e.addEventListener('change', changed); if (window.jQuery) jQuery(e).on('change', changed); });
    cb.addEventListener('change', paint);
    paint();
  });
  // reporting choice -> copy the tick of the field it came from
  var phoneOf = { mobile: ['country_code', 'mobile'], wa: ['whatsapp_code', 'whatsapp'], alt: ['country_code2', 'uae_mobile'] };
  function phoneText(code, num) { var c = (el(code) ? el(code).value : '').replace(/\D+/g, ''), n = (el(num) ? el(num).value : '').replace(/\D+/g, ''); return n ? (c ? '+' + c + ' ' : '') + n : ''; }
  Array.prototype.forEach.call(form.querySelectorAll('select.js-rep'), function (sel) {
    sel.addEventListener('change', function () {
      var v = sel.value, from = null;
      if (!v) return;
      if (sel.getAttribute('data-kind') === 'email') { if (el('email') && el('email').value.trim() === v) from = 'email'; }
      else Object.keys(phoneOf).forEach(function (k) { if (!from && phoneText(phoneOf[k][0], phoneOf[k][1]) === v) from = k; });
      var fcb = from ? el(from + '_verified') : null, rcb = sel.parentNode.querySelector('input[type=checkbox]');
      if (fcb && fcb.checked && rcb) { rcb.checked = true; rcb.dispatchEvent(new Event('change')); }
    });
  });
})();
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
