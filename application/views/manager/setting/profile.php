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
        <div style="font-size:12px; font-weight:700; color:#1e3a8a; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.5px;"><?php echo htmlspecialchars($p_info['role_title']); ?></div>
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
<details class="pf-details col-xs-12">
<summary>Personal and company details</summary>
<div class="row">
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
                <td><span class="label label-primary" style="background:#1e3a8a; font-size:11px; padding:3px 8px;"><?php echo htmlspecialchars($p_info['role_title']); ?></span></td>
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
</details>
</div>
</div>

<div class="row" style="margin-top: 12px;">
  <div class="col-xs-12">
    <div class="box box-default" style="border: 1px solid #e2e8f0; border-radius: 6px; background: #ffffff; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
      <div class="box-header with-border" style="padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <h3 class="box-title" style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0;">
          <i class="fa fa-id-badge" style="color: #1e3a8a; margin-right: 8px;"></i> HR &amp; Employee Services
        </h3>
        <span style="font-size: 12px; color: #64748b; font-weight: 500;">Access your personal employee workspace and development tools</span>
      </div>

      <div class="box-body" style="padding: 20px;">
        <ul class="nav nav-pills" id="hrProfileTabs" style="margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; gap: 6px; display: flex; flex-wrap: wrap;">
          <li class="active"><a href="#tabAttendance" data-toggle="pill" style="border-radius: 4px; font-weight: 600; padding: 8px 16px;">Attendance</a></li>
          <li><a href="#tabShifts" data-toggle="pill" style="border-radius: 4px; font-weight: 600; padding: 8px 16px;">Shifts</a></li>
          <li><a href="#tabHoliday" data-toggle="pill" style="border-radius: 4px; font-weight: 600; padding: 8px 16px;">Holiday Request</a></li>
          <li><a href="#tabEvents" data-toggle="pill" style="border-radius: 4px; font-weight: 600; padding: 8px 16px;">Events and Plans</a></li>
          <li><a href="#tabTraining" data-toggle="pill" style="border-radius: 4px; font-weight: 600; padding: 8px 16px;">Training</a></li>
          <li><a href="#tabTopics" data-toggle="pill" style="border-radius: 4px; font-weight: 600; padding: 8px 16px;">Search Topics</a></li>
        </ul>

        <div class="tab-content" style="padding-top: 10px;">
          <div class="tab-pane active" id="tabAttendance">
            <?php $my_att = $this->ci_admin_model->hr_attendance_summary((int)$this->session->userdata('manager_id')); ?>
            <div class="row">
              <div class="col-md-4 col-sm-12" style="margin-bottom: 12px;">
                <div class="pf-panel pf-panel-center">
                  <span class="pf-item-type" style="color: var(--crm-muted);">Today</span>
                  <?php if (!empty($my_att['today'])) { ?>
                    <div class="pf-stat"><?php echo !empty($my_att['today']['out_time']) ? 'Clocked out' : 'Clocked in'; ?></div>
                    <span class="pf-note" style="margin-top: 0;">In <?php echo !empty($my_att['today']['in_time']) ? date('h:i A', strtotime($my_att['today']['in_time'])) : '-'; ?><?php echo !empty($my_att['today']['out_time']) ? ' &middot; Out ' . date('h:i A', strtotime($my_att['today']['out_time'])) : ''; ?></span>
                  <?php } else { ?>
                    <div class="pf-stat">Not clocked in</div>
                  <?php } ?>
                  <a href="<?php echo base_url('manager/hr/attendance'); ?>" class="btn btn-primary btn-sm" style="margin-top: 10px;">Open attendance</a>
                </div>
              </div>
              <div class="col-md-8 col-sm-12">
                <div class="row">
                  <div class="col-xs-4" style="margin-bottom: 12px;">
                    <div class="pf-panel pf-panel-center"><span class="pf-item-type" style="color: var(--crm-muted);">Days present</span><div class="pf-stat"><?php echo (int)$my_att['days_present']; ?></div><span class="pf-note" style="margin-top: 0;"><?php echo date('F'); ?></span></div>
                  </div>
                  <div class="col-xs-4" style="margin-bottom: 12px;">
                    <div class="pf-panel pf-panel-center"><span class="pf-item-type" style="color: var(--crm-muted);">Late logins</span><div class="pf-stat"><?php echo (int)$my_att['late_logins']; ?></div><span class="pf-note" style="margin-top: 0;"><?php echo date('F'); ?></span></div>
                  </div>
                  <div class="col-xs-4" style="margin-bottom: 12px;">
                    <div class="pf-panel pf-panel-center"><span class="pf-item-type" style="color: var(--crm-muted);">GPS compliance</span><div class="pf-stat"><?php echo $my_att['gps_compliance'] === null ? '-' : (int)$my_att['gps_compliance'] . '%'; ?></div><span class="pf-note" style="margin-top: 0;"><?php echo date('F'); ?></span></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="tab-pane" id="tabShifts">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 18px;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 8px;">
                <h4 style="margin: 0; font-size: 15px; font-weight: 700; color: #0f172a;">Assigned Shift Roster</h4>
                <a href="<?php echo base_url('manager/hr/shifts'); ?>" class="btn btn-sm btn-default" style="border: 1px solid #cbd5e1; font-weight: 600; border-radius: 4px;">
                  Manage Shifts
                </a>
              </div>
              <?php
                $my_shift_uid = (int)$this->session->userdata('manager_id');
                $my_shift = $this->db->select('S.shift_name, S.start_time, S.end_time, S.grace_period_mins, S.working_hours, S.status, SA.effective_from, SA.effective_to')
                  ->from('ci_hr_shift_assignments SA')
                  ->join('ci_hr_shifts S', 'S.id = SA.shift_id', 'inner')
                  ->where('SA.user_id', $my_shift_uid)
                  ->where('SA.effective_from <=', date('Y-m-d'))
                  ->where('(SA.effective_to IS NULL OR SA.effective_to >= "' . date('Y-m-d') . '")', null, false)
                  ->order_by('SA.effective_from', 'DESC')
                  ->limit(1)
                  ->get()->row_array();
              ?>
              <?php if (!empty($my_shift)) { ?>
              <div class="row">
                <div class="col-md-6">
                  <table class="table table-bordered" style="background: #ffffff; margin-bottom: 0;">
                    <tr><th style="width: 140px; font-weight: 600;">Shift Name</th><td><?php echo htmlspecialchars($my_shift['shift_name']); ?></td></tr>
                    <tr><th style="font-weight: 600;">Working Hours</th><td><?php echo date('h:i A', strtotime($my_shift['start_time'])) . ' - ' . date('h:i A', strtotime($my_shift['end_time'])) . ' (' . number_format((float)$my_shift['working_hours'], 1) . ' Hours)'; ?></td></tr>
                    <tr><th style="font-weight: 600;">Grace Period</th><td><?php echo (int)$my_shift['grace_period_mins']; ?> Minutes</td></tr>
                  </table>
                </div>
                <div class="col-md-6">
                  <table class="table table-bordered" style="background: #ffffff; margin-bottom: 0;">
                    <tr><th style="width: 140px; font-weight: 600;">Effective From</th><td><?php echo date('d M Y', strtotime($my_shift['effective_from'])); ?></td></tr>
                    <tr><th style="font-weight: 600;">Effective To</th><td><?php echo !empty($my_shift['effective_to']) ? date('d M Y', strtotime($my_shift['effective_to'])) : 'Ongoing'; ?></td></tr>
                    <tr><th style="font-weight: 600;">Shift Status</th><td><span class="label label-primary"><?php echo htmlspecialchars($my_shift['status']); ?></span></td></tr>
                  </table>
                </div>
              </div>
              <?php } else { ?>
              <div style="padding: 16px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; color: #64748b; font-size: 13px;">No shift is currently assigned to you. Contact HR to have a shift assigned.</div>
              <?php } ?>            </div>
          </div>

          <div class="tab-pane" id="tabHoliday">
            <?php $my_leave = $this->ci_admin_model->hr_leave_summary((int)$this->session->userdata('manager_id')); ?>
            <div class="row">
              <div class="col-md-7" style="margin-bottom: 12px;">
                <div class="pf-panel">
                  <h4 class="pf-panel-title">Leave taken this year</h4>
                  <?php if (!empty($my_leave['used_by_type'])) { ?>
                  <table class="table table-bordered" style="background: #ffffff; margin-bottom: 0;">
                    <?php foreach ($my_leave['used_by_type'] as $lv) { ?>
                    <tr><th style="font-weight: 600; width: 50%;"><?php echo htmlspecialchars($lv['leave_type']); ?></th><td><?php echo (int)$lv['days']; ?> day<?php echo ((int)$lv['days'] === 1) ? '' : 's'; ?> approved</td></tr>
                    <?php } ?>
                  </table>
                  <?php } else { ?>
                  <div class="pf-empty">No approved leave recorded for <?php echo date('Y'); ?>.</div>
                  <?php } ?>
                  <div class="pf-note"><?php echo (int)$my_leave['pending']; ?> request<?php echo ((int)$my_leave['pending'] === 1) ? '' : 's'; ?> awaiting approval</div>
                </div>
              </div>
              <div class="col-md-5" style="margin-bottom: 12px;">
                <div class="pf-panel pf-panel-center">
                  <span class="pf-note" style="margin: 0 0 12px;">Request time off for vacation, emergencies or medical rest.</span>
                  <a href="<?php echo base_url('manager/hr/leaves'); ?>" class="btn btn-primary">Submit holiday / leave request</a>
                </div>
              </div>
            </div>
          </div>

          <div class="tab-pane" id="tabEvents">
            <?php $my_events = $this->ci_admin_model->hr_upcoming_events((int)$this->session->userdata('manager_id'), 6); ?>
            <div class="pf-panel">
              <div class="pf-panel-head">
                <h4 class="pf-panel-title">Upcoming events and plans</h4>
                <a href="<?php echo base_url('manager/hr/events_plans'); ?>" class="btn btn-sm btn-default">View all</a>
              </div>
              <?php if (!empty($my_events)) { ?>
              <div class="row">
                <?php foreach ($my_events as $ev) { ?>
                <div class="col-md-4 col-sm-6" style="margin-bottom: 10px;">
                  <div class="pf-item">
                    <span class="pf-item-type"><?php echo htmlspecialchars($ev['event_type']); ?></span>
                    <h5 class="pf-item-title"><?php echo htmlspecialchars($ev['title']); ?></h5>
                    <span class="pf-note"><?php echo date('d M Y', strtotime($ev['event_date'])) . ' &middot; ' . htmlspecialchars($ev['event_time']); ?><?php echo !empty($ev['location']) ? ' &middot; ' . htmlspecialchars($ev['location']) : ''; ?></span>
                  </div>
                </div>
                <?php } ?>
              </div>
              <?php } else { ?>
              <div class="pf-empty">No upcoming events or plans have been scheduled.</div>
              <?php } ?>
            </div>
          </div>

          <div class="tab-pane" id="tabTraining">
            <div class="pf-panel">
              <div class="pf-panel-head">
                <h4 class="pf-panel-title">Learning and development</h4>
                <a href="<?php echo base_url('manager/hr/training'); ?>" class="btn btn-sm btn-default">Open training</a>
              </div>
              <div class="pf-empty">No training modules have been assigned to you yet.</div>
            </div>
          </div>

          <div class="tab-pane" id="tabTopics">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 18px;">
              <h4 style="margin: 0 0 12px 0; font-size: 15px; font-weight: 700; color: #0f172a;">Search Employee Handbook &amp; Policies</h4>
              <form method="get" action="<?php echo base_url('manager/hr/search_topics'); ?>">
                <div class="input-group">
                  <input type="text" name="q" class="form-control" placeholder="Search leave rules, health insurance, code of conduct, shift timings..." style="border: 1px solid #cbd5e1; border-radius: 4px 0 0 4px; height: 40px;">
                  <span class="input-group-btn">
                    <button type="submit" class="btn btn-primary" style="background: #1e3a8a; border-color: #1e3a8a; border-radius: 0 4px 4px 0; height: 40px; font-weight: 700;">
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

