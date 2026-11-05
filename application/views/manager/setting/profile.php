<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
// My Profile: the signed-in person's own information. Details are view-only (HR / Admin edit them in Users);
// only the photo can be changed here.
$pf_uid = (int)$this->session->userdata('manager_id');
$pf_v = function ($key) use ($p_info) {
  $val = isset($p_info[$key]) ? trim((string)$p_info[$key]) : '';
  return ($val === '' || $val === '0000-00-00') ? '<span class="pf-dash">-</span>' : htmlspecialchars($val);
};
$pf_date = function ($key) use ($p_info) {
  $val = isset($p_info[$key]) ? trim((string)$p_info[$key]) : '';
  if ($val === '' || $val === '0000-00-00' || strtotime($val) === false) return '<span class="pf-dash">-</span>';
  return date('d M Y', strtotime($val));
};
$pf_sections = array(
  array('Personal', 'fa-user', array(
    array('Full name', $pf_v('name')), array('Gender', $pf_v('gender')), array('Date of birth', $pf_date('dob')),
    array('Nationality', $pf_v('national')), array('Blood group', $pf_v('blood')),
  )),
  array('Contact', 'fa-phone', array(
    array('Email', $pf_v('email')), array('Company email', $pf_v('c_email')), array('Mobile', $pf_v('mobile')),
    array('UAE alternate number', $pf_v('uae_mobile')), array('Address', $pf_v('address')), array('City', $pf_v('city')),
    array('State / Emirate', $pf_v('state_name')), array('Country', $pf_v('country_name')),
  )),
  array('Employment', 'fa-briefcase', array(
    array('Employee ID', $pf_v('emp_id')), array('Role', $pf_v('role_title')), array('Department', $pf_v('department')),
    array('Office / work location', $pf_v('office')), array('User name', $pf_v('c_username')), array('Date of joining', $pf_date('c_doj')),
    array('Salary', $pf_v('c_salary')), array('Date of leaving', $pf_date('c_dol')),
  )),
  array('Passport, visa and ID', 'fa-book', array(
    array('Passport number', $pf_v('p_no')), array('Passport issue date', $pf_date('p_date')), array('Passport expiry', $pf_date('p_exdate')),
    array('Visa type', $pf_v('visa_type')), array('Visa issued by', $pf_v('visa_by')), array('Visa reference no.', $pf_v('visa_no')),
    array('Visa expiry', $pf_date('visa_expiry')), array('National ID / EID number', $pf_v('eid_no')), array('EID expiry', $pf_date('eid_expiry')),
  )),
  array('Emergency contact', 'fa-heartbeat', array(
    array('Name', $pf_v('h_contact_name')), array('Relation', $pf_v('h_contact_relation')),
    array('Contact number', $pf_v('h_contact_no')), array('Alternate number', $pf_v('h_contact_no2')),
  )),
  array('Bank', 'fa-university', array(
    array('Bank name', $pf_v('bank')), array('Bank country', $pf_v('bank_country')), array('Account number', $pf_v('bank_no')),
    array('IBAN', $pf_v('iban')), array('IFSC / sort / branch code', $pf_v('ifsc')), array('SWIFT', $pf_v('swift')),
  )),
);

// Attendance
$my_att = $this->ci_admin_model->hr_attendance_summary($pf_uid);
$my_att_recent = $this->db->select('attendance_date, in_time, out_time, total_hours, status')
  ->where('user_id', $pf_uid)->order_by('attendance_date', 'DESC')->order_by('id', 'DESC')->limit(10)->get('ci_attendance')->result_array();

// Work shifts: current and upcoming assignments
$today = date('Y-m-d');
$my_shifts = $this->db->select('S.shift_name, S.start_time, S.end_time, S.grace_period_mins, S.working_hours, SA.effective_from, SA.effective_to')
  ->from('ci_hr_shift_assignments SA')
  ->join('ci_hr_shifts S', 'S.id = SA.shift_id', 'inner')
  ->where('SA.user_id', $pf_uid)
  ->where('(SA.effective_to IS NULL OR SA.effective_to >= ' . $this->db->escape($today) . ')', null, false)
  ->order_by('SA.effective_from', 'ASC')
  ->get()->result_array();
$cur_shift = null; $next_shifts = array();
foreach ($my_shifts as $sh) {
  if ($sh['effective_from'] <= $today) { $cur_shift = $sh; } else { $next_shifts[] = $sh; }
}

// Holidays: leave summary, my requests, company holidays this year
$my_leave = $this->ci_admin_model->hr_leave_summary($pf_uid);
$my_leave_list = $this->db->select('leave_type, from_date, to_date, total_days, status, created_at')
  ->where('user_id', $pf_uid)->order_by('from_date', 'DESC')->limit(10)->get('ci_leaves')->result_array();
$co_holidays = $this->db->select('title, event_type, start_date, end_date')
  ->from('ci_events')
  ->where("LOWER(event_type) IN ('holiday', 'public_holiday', 'festival')", null, false)
  ->where('start_date >=', date('Y-01-01 00:00:00'))
  ->where('start_date <=', date('Y-12-31 23:59:59'))
  ->where("(status IS NULL OR status = 'active')", null, false)
  ->order_by('start_date', 'ASC')->get()->result_array();

$pf_badge = function ($status) {
  $s = strtolower((string)$status);
  $cls = strpos($s, 'approv') !== false ? 'ok' : (strpos($s, 'reject') !== false || strpos($s, 'cancel') !== false ? 'bad' : (strpos($s, 'late') !== false ? 'warn' : 'neutral'));
  return '<span class="pf-badge pf-badge-' . $cls . '">' . htmlspecialchars($status ?: '-') . '</span>';
};
$pf_time = function ($t) { return !empty($t) && $t !== '00:00:00' ? date('h:i A', strtotime($t)) : '-'; };
?>
<style>
  .my-profile .pf-head { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; padding:16px 18px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; }
  .my-profile .pf-who { display:flex; align-items:center; gap:14px; min-width:0; }
  .my-profile .pf-avatar { width:64px; height:64px; border-radius:50%; overflow:hidden; border:2px solid #e2e8f0; background:#f8fafc; flex-shrink:0; }
  .my-profile .pf-avatar img { width:100%; height:100%; object-fit:cover; }
  .my-profile .pf-name { margin:0 0 2px; font-size:18px; font-weight:700; color:#0f172a; text-transform:capitalize; }
  .my-profile .pf-role { font-size:11px; font-weight:700; color:#1e3a8a; text-transform:uppercase; letter-spacing:.5px; }
  .my-profile .pf-mail { font-size:12px; color:#64748b; word-break:break-all; }
  .my-profile .pf-tabs { display:flex; gap:6px; flex-wrap:wrap; margin:14px 0 0; padding:0; list-style:none; border-bottom:1px solid #e2e8f0; }
  .my-profile .pf-tabs > li > a { display:inline-flex; align-items:center; gap:7px; padding:10px 14px; font-weight:600; color:#475569; border-bottom:2px solid transparent; margin-bottom:-1px; text-decoration:none; }
  .my-profile .pf-tabs > li.active > a { color:#1e3a8a; border-bottom-color:#1e3a8a; }
  .my-profile .pf-body { background:#fff; border:1px solid #e2e8f0; border-top:0; border-radius:0 0 8px 8px; padding:18px; }
  .my-profile .pf-lock { display:flex; align-items:center; gap:8px; font-size:12px; color:#475569; background:#f1f5f9; border-radius:6px; padding:8px 12px; margin-bottom:14px; }
  .my-profile .pf-sec { margin-bottom:16px; }
  .my-profile .pf-sec h4 { font-size:13px; font-weight:700; color:#0f172a; margin:0 0 8px; display:flex; align-items:center; gap:7px; }
  .my-profile .pf-sec h4 i { color:#1e3a8a; }
  .my-profile .pf-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(220px, 1fr)); gap:8px; }
  .my-profile .pf-field { background:#f8fafc; border:1px solid #eef2f7; border-radius:6px; padding:8px 10px; min-width:0; }
  .my-profile .pf-field span { display:block; font-size:11px; color:#64748b; font-weight:600; margin-bottom:2px; }
  .my-profile .pf-field strong { display:block; font-size:13px; color:#0f172a; font-weight:600; word-break:break-word; }
  .my-profile .pf-dash { color:#94a3b8; font-weight:400; }
  .my-profile .pf-table { width:100%; background:#fff; margin:0; }
  .my-profile .pf-table th { font-size:12px; color:#64748b; font-weight:600; background:#f8fafc; }
  .my-profile .pf-table td, .my-profile .pf-table th { padding:8px 10px; border-top:1px solid #eef2f7; }
  .my-profile .pf-badge { display:inline-block; font-size:11px; font-weight:700; padding:2px 8px; border-radius:10px; }
  .my-profile .pf-badge-ok { background:#dcfce7; color:#166534; }
  .my-profile .pf-badge-bad { background:#fee2e2; color:#991b1b; }
  .my-profile .pf-badge-warn { background:#fef3c7; color:#92400e; }
  .my-profile .pf-badge-neutral { background:#e0e7ff; color:#3730a3; }
  .my-profile .pf-actions { display:flex; gap:8px; flex-wrap:wrap; }
  @media (max-width: 767px) {
    /* Phones: four equal tabs, icon above the name, all visible without scrolling. */
    .my-profile .pf-tabs { display:grid; grid-template-columns:repeat(4, 1fr); gap:0; }
    .my-profile .pf-tabs > li > a { display:flex; flex-direction:column; align-items:center; gap:3px; padding:8px 2px; font-size:12px; white-space:nowrap; text-align:center; }
    .my-profile .pf-tabs > li > a i { font-size:15px; }
    .my-profile .pf-body { padding:12px; }
    .my-profile .pf-grid { grid-template-columns:1fr 1fr; }
  }
  @media (max-width: 420px) { .my-profile .pf-grid { grid-template-columns:1fr; } }
</style>

<div class="content-wrapper">
  <section class="content-header">
    <h1> <?php echo $info['page_heading']; ?> </h1>
  </section>
  <section class="content my-profile">
  <div class="container">

    <div class="pf-head">
      <div class="pf-who">
        <div class="pf-avatar">
          <img id="profileAvatarImg" src="<?php echo (!empty($p_info['avatar']) && file_exists(FCPATH . $p_info['avatar'])) ? base_url() . $p_info['avatar'] : base_url() . 'assets/dist/img/pfp_male_1.svg'; ?>" alt="" />
        </div>
        <div style="min-width:0;">
          <h3 class="pf-name"><?php echo htmlspecialchars($p_info['name'] ?? ''); ?></h3>
          <?php if (!empty($p_info['role_title'])) { ?><div class="pf-role"><?php echo htmlspecialchars($p_info['role_title']); ?></div><?php } ?>
          <div class="pf-mail"><?php echo htmlspecialchars($p_info['email'] ?? ''); ?></div>
        </div>
      </div>
      <label class="btn btn-sm btn-default" style="margin-bottom:0; cursor:pointer;" title="Upload or replace your profile photo">
        <i class="fa fa-camera"></i> Change Photo
        <input type="file" id="ajaxAvatarInput" accept="image/jpeg,image/png,image/webp" style="display:none;" />
      </label>
    </div>

    <ul class="pf-tabs" id="pfTabs" role="tablist">
      <li class="active"><a href="#overview" data-toggle="tab" role="tab"><i class="fa fa-user"></i> Overview</a></li>
      <li><a href="#attendance" data-toggle="tab" role="tab"><i class="fa fa-clock-o"></i> Attendance</a></li>
      <li><a href="#shifts" data-toggle="tab" role="tab"><i class="fa fa-calendar-o"></i> Work Shifts</a></li>
      <li><a href="#holidays" data-toggle="tab" role="tab"><i class="fa fa-plane"></i> Holidays</a></li>
    </ul>

    <div class="tab-content pf-body">

      <!-- Overview: view-only details -->
      <div class="tab-pane active" id="overview" role="tabpanel">
        <div class="pf-lock"><i class="fa fa-lock"></i> These details are managed by HR. To correct anything, please contact HR or your administrator.</div>
        <?php foreach ($pf_sections as $sec) { ?>
        <div class="pf-sec">
          <h4><i class="fa <?php echo $sec[1]; ?>"></i> <?php echo $sec[0]; ?></h4>
          <div class="pf-grid">
            <?php foreach ($sec[2] as $f) { ?>
            <div class="pf-field"><span><?php echo $f[0]; ?></span><strong><?php echo $f[1]; ?></strong></div>
            <?php } ?>
          </div>
        </div>
        <?php } ?>
      </div>

      <!-- Attendance -->
      <div class="tab-pane" id="attendance" role="tabpanel">
        <div class="row">
          <div class="col-md-4 col-sm-12" style="margin-bottom:12px;">
            <div class="pf-panel pf-panel-center">
              <span class="pf-item-type" style="color:var(--crm-muted);">Today</span>
              <?php if (!empty($my_att['today'])) { ?>
                <div class="pf-stat"><?php echo !empty($my_att['today']['out_time']) ? 'Clocked out' : 'Clocked in'; ?></div>
                <span class="pf-note" style="margin-top:0;">In <?php echo $pf_time($my_att['today']['in_time']); ?><?php echo !empty($my_att['today']['out_time']) ? ' &middot; Out ' . $pf_time($my_att['today']['out_time']) : ''; ?></span>
              <?php } else { ?>
                <div class="pf-stat">Not clocked in</div>
              <?php } ?>
              <a href="<?php echo base_url('manager/hr/attendance'); ?>" class="btn btn-primary btn-sm" style="margin-top:10px;">Open attendance</a>
            </div>
          </div>
          <div class="col-md-8 col-sm-12">
            <div class="row">
              <div class="col-xs-4" style="margin-bottom:12px;"><div class="pf-panel pf-panel-center"><span class="pf-item-type" style="color:var(--crm-muted);">Days present</span><div class="pf-stat"><?php echo (int)$my_att['days_present']; ?></div><span class="pf-note" style="margin-top:0;"><?php echo date('F'); ?></span></div></div>
              <div class="col-xs-4" style="margin-bottom:12px;"><div class="pf-panel pf-panel-center"><span class="pf-item-type" style="color:var(--crm-muted);">Late logins</span><div class="pf-stat"><?php echo (int)$my_att['late_logins']; ?></div><span class="pf-note" style="margin-top:0;"><?php echo date('F'); ?></span></div></div>
              <div class="col-xs-4" style="margin-bottom:12px;"><div class="pf-panel pf-panel-center"><span class="pf-item-type" style="color:var(--crm-muted);">GPS compliance</span><div class="pf-stat"><?php echo $my_att['gps_compliance'] === null ? '-' : (int)$my_att['gps_compliance'] . '%'; ?></div><span class="pf-note" style="margin-top:0;"><?php echo date('F'); ?></span></div></div>
            </div>
          </div>
        </div>
        <div class="pf-sec">
          <h4><i class="fa fa-history"></i> Recent attendance</h4>
          <?php if (!empty($my_att_recent)) { ?>
          <div class="table-responsive"><table class="table pf-table">
            <tr><th>Date</th><th>In</th><th>Out</th><th>Hours</th><th>Status</th></tr>
            <?php foreach ($my_att_recent as $a) { ?>
            <tr>
              <td><?php echo date('d M Y, D', strtotime($a['attendance_date'])); ?></td>
              <td><?php echo $pf_time($a['in_time']); ?></td>
              <td><?php echo $pf_time($a['out_time']); ?></td>
              <td><?php echo $a['total_hours'] !== null && $a['total_hours'] !== '' ? number_format((float)$a['total_hours'], 2) : '-'; ?></td>
              <td><?php echo $pf_badge($a['status']); ?></td>
            </tr>
            <?php } ?>
          </table></div>
          <?php } else { ?>
          <div class="pf-empty">No attendance recorded yet.</div>
          <?php } ?>
        </div>
      </div>

      <!-- Work shifts -->
      <div class="tab-pane" id="shifts" role="tabpanel">
        <div class="pf-sec">
          <h4><i class="fa fa-clock-o"></i> Current shift</h4>
          <?php if (!empty($cur_shift)) { ?>
          <div class="pf-grid">
            <div class="pf-field"><span>Shift</span><strong><?php echo htmlspecialchars($cur_shift['shift_name']); ?></strong></div>
            <div class="pf-field"><span>Timing</span><strong><?php echo $pf_time($cur_shift['start_time']) . ' - ' . $pf_time($cur_shift['end_time']); ?></strong></div>
            <div class="pf-field"><span>Working hours</span><strong><?php echo number_format((float)$cur_shift['working_hours'], 1); ?> hours</strong></div>
            <div class="pf-field"><span>Grace period</span><strong><?php echo (int)$cur_shift['grace_period_mins']; ?> minutes</strong></div>
            <div class="pf-field"><span>From</span><strong><?php echo date('d M Y', strtotime($cur_shift['effective_from'])); ?></strong></div>
            <div class="pf-field"><span>Until</span><strong><?php echo !empty($cur_shift['effective_to']) ? date('d M Y', strtotime($cur_shift['effective_to'])) : 'Ongoing'; ?></strong></div>
          </div>
          <?php } else { ?>
          <div class="pf-empty">No shift is assigned to you right now. Contact HR to have a shift assigned.</div>
          <?php } ?>
        </div>
        <div class="pf-sec">
          <h4><i class="fa fa-calendar-plus-o"></i> Upcoming shifts</h4>
          <?php if (!empty($next_shifts)) { ?>
          <div class="table-responsive"><table class="table pf-table">
            <tr><th>Shift</th><th>Timing</th><th>From</th><th>Until</th></tr>
            <?php foreach ($next_shifts as $sh) { ?>
            <tr>
              <td><?php echo htmlspecialchars($sh['shift_name']); ?></td>
              <td><?php echo $pf_time($sh['start_time']) . ' - ' . $pf_time($sh['end_time']); ?></td>
              <td><?php echo date('d M Y', strtotime($sh['effective_from'])); ?></td>
              <td><?php echo !empty($sh['effective_to']) ? date('d M Y', strtotime($sh['effective_to'])) : 'Ongoing'; ?></td>
            </tr>
            <?php } ?>
          </table></div>
          <?php } else { ?>
          <div class="pf-empty">No upcoming shift changes.</div>
          <?php } ?>
        </div>
      </div>

      <!-- Holidays -->
      <div class="tab-pane" id="holidays" role="tabpanel">
        <div class="row">
          <div class="col-md-7" style="margin-bottom:12px;">
            <div class="pf-panel">
              <h4 class="pf-panel-title">Leave taken in <?php echo date('Y'); ?></h4>
              <?php if (!empty($my_leave['used_by_type'])) { ?>
              <div class="pf-grid">
                <?php foreach ($my_leave['used_by_type'] as $lv) { ?>
                <div class="pf-field"><span><?php echo htmlspecialchars($lv['leave_type']); ?></span><strong><?php echo (int)$lv['days']; ?> day<?php echo ((int)$lv['days'] === 1) ? '' : 's'; ?></strong></div>
                <?php } ?>
              </div>
              <?php } else { ?>
              <div class="pf-empty">No approved leave recorded for <?php echo date('Y'); ?>.</div>
              <?php } ?>
              <div class="pf-note"><?php echo (int)$my_leave['pending']; ?> request<?php echo ((int)$my_leave['pending'] === 1) ? '' : 's'; ?> awaiting approval</div>
            </div>
          </div>
          <div class="col-md-5" style="margin-bottom:12px;">
            <div class="pf-panel pf-panel-center">
              <span class="pf-note" style="margin:0 0 12px;">Request time off for vacation, emergencies or medical rest.</span>
              <div class="pf-actions" style="justify-content:center;">
                <a href="<?php echo base_url('manager/hr/leaves#new'); ?>" class="btn btn-primary btn-sm">Request leave</a>
                <a href="<?php echo base_url('manager/hr/leaves'); ?>" class="btn btn-default btn-sm">All my requests</a>
              </div>
            </div>
          </div>
        </div>
        <div class="pf-sec">
          <h4><i class="fa fa-list-ul"></i> My recent requests</h4>
          <?php if (!empty($my_leave_list)) { ?>
          <div class="table-responsive"><table class="table pf-table">
            <tr><th>Type</th><th>From</th><th>To</th><th>Days</th><th>Status</th></tr>
            <?php foreach ($my_leave_list as $lv) { ?>
            <tr>
              <td><?php echo htmlspecialchars($lv['leave_type']); ?></td>
              <td><?php echo date('d M Y', strtotime($lv['from_date'])); ?></td>
              <td><?php echo date('d M Y', strtotime($lv['to_date'])); ?></td>
              <td><?php echo (int)$lv['total_days']; ?></td>
              <td><?php echo $pf_badge($lv['status']); ?></td>
            </tr>
            <?php } ?>
          </table></div>
          <?php } else { ?>
          <div class="pf-empty">You have not requested any leave yet.</div>
          <?php } ?>
        </div>
        <div class="pf-sec">
          <h4><i class="fa fa-star-o"></i> Company holidays <?php echo date('Y'); ?></h4>
          <?php if (!empty($co_holidays)) { ?>
          <div class="table-responsive"><table class="table pf-table">
            <tr><th>Holiday</th><th>Date</th><th>Day</th></tr>
            <?php foreach ($co_holidays as $h) { $past = substr($h['start_date'], 0, 10) < $today; ?>
            <tr<?php echo $past ? ' style="opacity:.55;"' : ''; ?>>
              <td><?php echo htmlspecialchars($h['title']); ?></td>
              <td><?php echo date('d M Y', strtotime($h['start_date'])); ?><?php echo (!empty($h['end_date']) && substr($h['end_date'], 0, 10) > substr($h['start_date'], 0, 10)) ? ' - ' . date('d M Y', strtotime($h['end_date'])) : ''; ?></td>
              <td><?php echo date('l', strtotime($h['start_date'])); ?></td>
            </tr>
            <?php } ?>
          </table></div>
          <?php } else { ?>
          <div class="pf-empty">Company holidays for <?php echo date('Y'); ?> have not been published yet.</div>
          <?php } ?>
        </div>
      </div>

    </div>
  </div>
  </section>
</div>

<script>
$(document).ready(function() {
  // Open the tab named in the address (#attendance, #shifts, #holidays) and keep it in the address when switching.
  var hash = window.location.hash;
  if (hash && $('#pfTabs a[href="' + hash + '"]').length) { $('#pfTabs a[href="' + hash + '"]').tab('show'); }
  $('#pfTabs a').on('shown.bs.tab', function (e) {
    if (history.replaceState) { history.replaceState(null, '', $(e.target).attr('href')); }
  });

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
