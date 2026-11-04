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
   

<!-- HR & Employee Services Portal Section -->
<div class="row" style="margin-top: 25px;">
  <div class="col-xs-12">
    <div class="box box-default" style="border: 1px solid #e2e8f0; border-radius: 6px; background: #ffffff; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
      <div class="box-header with-border" style="padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <h3 class="box-title" style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0;">
          <i class="fa fa-id-badge" style="color: #0072f0; margin-right: 8px;"></i> HR &amp; Employee Services
        </h3>
        <span style="font-size: 12px; color: #64748b; font-weight: 500;">Access your personal employee workspace and development tools</span>
      </div>

      <div class="box-body" style="padding: 20px;">
        <!-- HR Navigation Tabs -->
        <ul class="nav nav-pills" id="hrProfileTabs" style="margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; gap: 6px; display: flex; flex-wrap: wrap;">
          <li class="active"><a href="#tabAttendance" data-toggle="pill" style="border-radius: 4px; font-weight: 600; padding: 8px 16px;"><i class="fa fa-map-marker" style="margin-right: 5px;"></i> Attendance</a></li>
          <li><a href="#tabShifts" data-toggle="pill" style="border-radius: 4px; font-weight: 600; padding: 8px 16px;"><i class="fa fa-clock-o" style="margin-right: 5px;"></i> Shifts</a></li>
          <li><a href="#tabHoliday" data-toggle="pill" style="border-radius: 4px; font-weight: 600; padding: 8px 16px;"><i class="fa fa-calendar-check-o" style="margin-right: 5px;"></i> Holiday Request</a></li>
          <li><a href="#tabEvents" data-toggle="pill" style="border-radius: 4px; font-weight: 600; padding: 8px 16px;"><i class="fa fa-calendar" style="margin-right: 5px;"></i> Events and Plans</a></li>
          <li><a href="#tabTraining" data-toggle="pill" style="border-radius: 4px; font-weight: 600; padding: 8px 16px;"><i class="fa fa-graduation-cap" style="margin-right: 5px;"></i> Training</a></li>
          <li><a href="#tabTopics" data-toggle="pill" style="border-radius: 4px; font-weight: 600; padding: 8px 16px;"><i class="fa fa-search" style="margin-right: 5px;"></i> Search Topics</a></li>
        </ul>

        <div class="tab-content" style="padding-top: 10px;">
          <!-- 1. Attendance Tab -->
          <div class="tab-pane active" id="tabAttendance">
            <div class="row">
              <div class="col-md-4 col-sm-12" style="margin-bottom: 15px;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 18px; text-align: center;">
                  <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 8px;">Today's Attendance</span>
                  <div style="font-size: 22px; font-weight: 800; color: #16a34a; margin-bottom: 10px;">
                    <i class="fa fa-check-circle"></i> Clocked In
                  </div>
                  <span style="font-size: 12px; color: #64748b; display: block; margin-bottom: 14px;">First Check-in Today: <strong>08:58 AM</strong></span>
                  <a href="<?php echo base_url('manager/hr/attendance'); ?>" class="btn btn-primary" style="background: #0072f0; border-color: #0072f0; border-radius: 4px; font-weight: 600; width: 100%;">
                    <i class="fa fa-history"></i> View Full Attendance Log
                  </a>
                </div>
              </div>
              <div class="col-md-8 col-sm-12">
                <div class="row">
                  <div class="col-sm-4" style="margin-bottom: 15px;">
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px; text-align: center;">
                      <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Days Present</span>
                      <h3 style="font-size: 24px; font-weight: 800; color: #0072f0; margin: 6px 0 0 0;">22</h3>
                    </div>
                  </div>
                  <div class="col-sm-4" style="margin-bottom: 15px;">
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px; text-align: center;">
                      <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Late Logins</span>
                      <h3 style="font-size: 24px; font-weight: 800; color: #d97706; margin: 6px 0 0 0;">0</h3>
                    </div>
                  </div>
                  <div class="col-sm-4" style="margin-bottom: 15px;">
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px; text-align: center;">
                      <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">GPS Compliance</span>
                      <h3 style="font-size: 24px; font-weight: 800; color: #16a34a; margin: 6px 0 0 0;">100%</h3>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- 2. Shifts Tab -->
          <div class="tab-pane" id="tabShifts">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 18px;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 8px;">
                <h4 style="margin: 0; font-size: 15px; font-weight: 700; color: #0f172a;">Assigned Shift Roster</h4>
                <a href="<?php echo base_url('manager/hr/shifts'); ?>" class="btn btn-sm btn-default" style="border: 1px solid #cbd5e1; font-weight: 600; border-radius: 4px;">
                  Manage Shifts
                </a>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <table class="table table-bordered" style="background: #ffffff; margin-bottom: 0;">
                    <tr><th style="width: 140px; font-weight: 600;">Shift Name</th><td>General Corporate Shift</td></tr>
                    <tr><th style="font-weight: 600;">Working Hours</th><td>09:00 AM - 06:00 PM (9.0 Hours)</td></tr>
                    <tr><th style="font-weight: 600;">Grace Period</th><td>15 Minutes</td></tr>
                  </table>
                </div>
                <div class="col-md-6">
                  <table class="table table-bordered" style="background: #ffffff; margin-bottom: 0;">
                    <tr><th style="width: 140px; font-weight: 600;">Working Days</th><td>Monday to Friday</td></tr>
                    <tr><th style="font-weight: 600;">Weekly Off</th><td>Saturday, Sunday</td></tr>
                    <tr><th style="font-weight: 600;">Shift Status</th><td><span class="label label-success" style="border-radius: 3px;">Active</span></td></tr>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <!-- 3. Holiday Request Tab -->
          <div class="tab-pane" id="tabHoliday">
            <div class="row">
              <div class="col-md-6" style="margin-bottom: 15px;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 18px;">
                  <h4 style="margin: 0 0 12px 0; font-size: 15px; font-weight: 700; color: #0f172a;">Annual &amp; Statutory Leave Balances</h4>
                  <table class="table table-bordered" style="background: #ffffff; margin-bottom: 0;">
                    <tr><th style="font-weight: 600;">Annual Leave</th><td><strong>22 Days</strong> Remaining (8 Days Used)</td></tr>
                    <tr><th style="font-weight: 600;">Sick Leave</th><td><strong>13 Days</strong> Remaining (2 Days Used)</td></tr>
                    <tr><th style="font-weight: 600;">Public Holidays</th><td>Observed as per UAE Official Federal Calendar</td></tr>
                  </table>
                </div>
              </div>
              <div class="col-md-6" style="margin-bottom: 15px;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 18px; text-align: center; display: flex; flex-direction: column; justify-content: center; height: 100%;">
                  <span style="font-size: 13px; color: #475569; margin-bottom: 14px;">Need time off for vacation, emergency, or medical rest?</span>
                  <a href="<?php echo base_url('manager/hr/leaves'); ?>" class="btn btn-primary" style="background: #0072f0; border-color: #0072f0; border-radius: 4px; font-weight: 700; padding: 10px 20px;">
                    <i class="fa fa-calendar-plus-o"></i> Submit New Holiday / Leave Request
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- 4. Events and Plans Tab -->
          <div class="tab-pane" id="tabEvents">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 18px;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                <h4 style="margin: 0; font-size: 15px; font-weight: 700; color: #0f172a;">Upcoming Company Events &amp; Team Plans</h4>
                <a href="<?php echo base_url('manager/hr/events_plans'); ?>" class="btn btn-sm btn-default" style="border: 1px solid #cbd5e1; font-weight: 600; border-radius: 4px;">
                  View All Events
                </a>
              </div>
              <div class="row">
                <div class="col-md-4 col-sm-6" style="margin-bottom: 10px;">
                  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px;">
                    <span class="label" style="background: #0072f0; font-size: 10px; border-radius: 2px;">Company Event</span>
                    <h5 style="font-weight: 700; margin: 8px 0 4px 0; color: #0f172a;">Annual Gala &amp; Awards</h5>
                    <span style="font-size: 12px; color: #64748b;"><i class="fa fa-calendar-o"></i> 20 Dec 2026</span>
                  </div>
                </div>
                <div class="col-md-4 col-sm-6" style="margin-bottom: 10px;">
                  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px;">
                    <span class="label" style="background: #16a34a; font-size: 10px; border-radius: 2px;">Strategic Plan</span>
                    <h5 style="font-weight: 700; margin: 8px 0 4px 0; color: #0f172a;">Q1 Sales Kickoff</h5>
                    <span style="font-size: 12px; color: #64748b;"><i class="fa fa-calendar-o"></i> 11 Nov 2026</span>
                  </div>
                </div>
                <div class="col-md-4 col-sm-6" style="margin-bottom: 10px;">
                  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px;">
                    <span class="label" style="background: #7c3aed; font-size: 10px; border-radius: 2px;">Workshop</span>
                    <h5 style="font-weight: 700; margin: 8px 0 4px 0; color: #0f172a;">Client Retention &amp; SLA</h5>
                    <span style="font-size: 12px; color: #64748b;"><i class="fa fa-calendar-o"></i> 18 Nov 2026</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- 5. Training Tab -->
          <div class="tab-pane" id="tabTraining">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 18px;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                <h4 style="margin: 0; font-size: 15px; font-weight: 700; color: #0f172a;">Learning &amp; Professional Development</h4>
                <a href="<?php echo base_url('manager/hr/training'); ?>" class="btn btn-sm btn-default" style="border: 1px solid #cbd5e1; font-weight: 600; border-radius: 4px;">
                  Access LMS
                </a>
              </div>
              <div class="row">
                <div class="col-md-6" style="margin-bottom: 10px;">
                  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                      <strong style="font-size: 13.5px; color: #0f172a;">Seven Strokes CRM Mastery</strong>
                      <span class="label label-success" style="border-radius: 3px;">100% Completed</span>
                    </div>
                    <span style="font-size: 12px; color: #64748b;">Core pipeline and order processing certification</span>
                  </div>
                </div>
                <div class="col-md-6" style="margin-bottom: 10px;">
                  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                      <strong style="font-size: 13.5px; color: #0f172a;">Enterprise Sales &amp; Closing</strong>
                      <span class="label label-primary" style="background: #0072f0; border-radius: 3px;">65% In Progress</span>
                    </div>
                    <span style="font-size: 12px; color: #64748b;">Objection handling &amp; enterprise client pitching</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- 6. Search Topics Tab -->
          <div class="tab-pane" id="tabTopics">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 18px;">
              <h4 style="margin: 0 0 12px 0; font-size: 15px; font-weight: 700; color: #0f172a;">Search Employee Handbook &amp; Policies</h4>
              <form method="get" action="<?php echo base_url('manager/hr/search_topics'); ?>">
                <div class="input-group">
                  <input type="text" name="q" class="form-control" placeholder="Search leave rules, health insurance, code of conduct, shift timings..." style="border: 1px solid #cbd5e1; border-radius: 4px 0 0 4px; height: 40px;">
                  <span class="input-group-btn">
                    <button type="submit" class="btn btn-primary" style="background: #0072f0; border-color: #0072f0; border-radius: 0 4px 4px 0; height: 40px; font-weight: 700;">
                      <i class="fa fa-search"></i> Search Topics
                    </button>
                  </span>
                </div>
              </form>
            </div>
          </div>
        </div>
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

