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

$today = date('Y-m-d');

// Holidays: leave summary, my requests, company holidays this year
$my_leave = $this->ci_admin_model->hr_leave_summary($pf_uid);
$my_leave_list = $this->db->select('id, leave_type, from_date, to_date, total_days, reason, status, created_at')
  ->where('user_id', $pf_uid)->order_by('from_date', 'DESC')->limit(50)->get('ci_leaves')->result_array();
$leave_types = array('Annual Leave', 'Sick Leave', 'Casual Leave', 'Maternity Leave', 'Emergency Leave', 'Unpaid Leave');
$co_holidays = $this->db->select('title, event_type, start_date, end_date')
  ->from('ci_events')
  ->where("LOWER(event_type) IN ('holiday', 'public_holiday', 'festival')", null, false)
  ->where('visibility', 'public')
  ->where('start_date >=', date('Y-01-01 00:00:00'))
  ->where('start_date <=', date('Y-12-31 23:59:59'))
  ->where("(status IS NULL OR status = 'active')", null, false)
  ->order_by('start_date', 'ASC')->get()->result_array();

$pf_badge = function ($status) {
  $s = strtolower((string)$status);
  $cls = strpos($s, 'approv') !== false ? 'ok' : (strpos($s, 'reject') !== false || strpos($s, 'cancel') !== false ? 'bad' : (strpos($s, 'late') !== false ? 'warn' : 'neutral'));
  return '<span class="pf-badge pf-badge-' . $cls . '">' . htmlspecialchars($status ?: '-') . '</span>';
};
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
  .my-profile .pf-hr-grid { display:grid; grid-template-columns:minmax(0, 5fr) minmax(0, 7fr); gap:14px; align-items:start; margin-bottom:16px; }
  .my-profile .pf-hr-card { border:1px solid #e2e8f0; border-radius:8px; padding:14px; min-width:0; }
  .my-profile .pf-hr-card h4 { font-size:13px; font-weight:700; color:#0f172a; margin:0 0 10px; display:flex; align-items:center; gap:7px; }
  .my-profile .pf-hr-card h4 i { color:#1e3a8a; }
  .my-profile .pf-hr-card label { font-size:12px; font-weight:600; color:#334155; margin-bottom:3px; }
  .my-profile .pf-hr-card .form-group { margin-bottom:10px; }
  .my-profile .pf-hr-dates { display:grid; grid-template-columns:minmax(0, 1fr) minmax(0, 1fr); gap:10px; }
  .my-profile .pf-hr-dates input { min-width:0; width:100%; padding-left:8px; padding-right:4px; }
  .my-profile .pf-hr-days { font-size:12px; font-weight:700; color:#1e3a8a; background:#eff6ff; border-radius:6px; padding:6px 10px; margin-bottom:10px; }
  .my-profile .pf-hr-scroll { max-height:320px; overflow-y:auto; }
  .my-profile .pf-note { display:block; font-size:12px; color:#64748b; margin-top:8px; }
  @media (max-width: 991px) { .my-profile .pf-hr-grid { grid-template-columns:1fr; } }
  @media (max-width: 767px) {
    /* Phones: two equal tabs, icon above the name. */
    .my-profile .pf-tabs { display:grid; grid-template-columns:repeat(2, 1fr); gap:0; }
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
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

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
      <li><a href="#holidays" data-toggle="tab" role="tab"><i class="fa fa-plane"></i> Holiday Request</a></li>
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

      <!-- Holiday Request: apply, my requests, company holidays -->
      <div class="tab-pane" id="holidays" role="tabpanel">
        <div class="pf-hr-grid">
          <div class="pf-hr-card" id="pfApply">
            <h4><i class="fa fa-paper-plane"></i> Request a holiday</h4>
            <form method="post" action="<?php echo base_url('manager/hr/leaves/apply'); ?>">
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
              <input type="hidden" name="return" value="manager/profile#holidays">
              <div class="form-group">
                <label for="pfLeaveType">Type</label>
                <select name="leave_type" id="pfLeaveType" class="form-control" required>
                  <?php foreach ($leave_types as $lt) { ?><option value="<?php echo $lt; ?>"><?php echo $lt; ?></option><?php } ?>
                </select>
              </div>
              <div class="pf-hr-dates">
                <div class="form-group">
                  <label for="pfLeaveFrom">From</label>
                  <input type="date" name="from_date" id="pfLeaveFrom" class="form-control" value="<?php echo $today; ?>" required>
                </div>
                <div class="form-group">
                  <label for="pfLeaveTo">To</label>
                  <input type="date" name="to_date" id="pfLeaveTo" class="form-control" value="<?php echo $today; ?>" required>
                </div>
              </div>
              <div class="pf-hr-days" id="pfLeaveDays">1 day</div>
              <div class="form-group">
                <label for="pfLeaveReason">Reason <small class="text-muted">(optional)</small></label>
                <textarea name="reason" id="pfLeaveReason" class="form-control" rows="2" maxlength="1000"></textarea>
              </div>
              <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Submit request</button>
              <span class="pf-note">HR and your administrator are notified and approve it.</span>
            </form>
          </div>
          <div style="min-width:0;">
            <div class="pf-hr-card" style="margin-bottom:14px;">
              <h4><i class="fa fa-pie-chart"></i> Leave taken in <?php echo date('Y'); ?></h4>
              <?php if (!empty($my_leave['used_by_type'])) { ?>
              <div class="pf-grid">
                <?php foreach ($my_leave['used_by_type'] as $lv) { ?>
                <div class="pf-field"><span><?php echo htmlspecialchars($lv['leave_type']); ?></span><strong><?php echo (int)$lv['days']; ?> day<?php echo ((int)$lv['days'] === 1) ? '' : 's'; ?></strong></div>
                <?php } ?>
              </div>
              <?php } else { ?>
              <div class="pf-empty">No approved leave recorded for <?php echo date('Y'); ?>.</div>
              <?php } ?>
              <span class="pf-note"><?php echo (int)$my_leave['pending']; ?> request<?php echo ((int)$my_leave['pending'] === 1) ? '' : 's'; ?> awaiting approval</span>
            </div>
            <div class="pf-hr-card">
              <h4><i class="fa fa-list-ul"></i> My requests</h4>
              <?php if (!empty($my_leave_list)) { ?>
              <div class="table-responsive pf-hr-scroll"><table class="table pf-table">
                <tr><th>Type</th><th>Dates</th><th>Days</th><th>Status</th><th></th></tr>
                <?php foreach ($my_leave_list as $lv) { ?>
                <tr>
                  <td><?php echo htmlspecialchars($lv['leave_type']); ?><?php if (trim((string)$lv['reason']) !== '') { ?><br><small class="text-muted"><?php echo htmlspecialchars($lv['reason']); ?></small><?php } ?></td>
                  <td style="white-space:nowrap;"><?php echo date('d M Y', strtotime($lv['from_date'])); ?><?php echo $lv['to_date'] !== $lv['from_date'] ? '<br>' . date('d M Y', strtotime($lv['to_date'])) : ''; ?></td>
                  <td><?php echo (int)$lv['total_days']; ?></td>
                  <td><?php echo $pf_badge($lv['status']); ?></td>
                  <td>
                    <?php if ($lv['status'] === 'Pending') { ?>
                    <form method="post" action="<?php echo base_url('manager/hr/cancel_leave/' . (int)$lv['id']); ?>" style="display:inline;" onsubmit="return confirm('Cancel this holiday request?');">
                      <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                      <input type="hidden" name="return" value="manager/profile#holidays">
                      <button type="submit" class="btn btn-default btn-xs">Cancel</button>
                    </form>
                    <?php } ?>
                  </td>
                </tr>
                <?php } ?>
              </table></div>
              <?php } else { ?>
              <div class="pf-empty">You have not requested any holiday yet.</div>
              <?php } ?>
            </div>
          </div>
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
  // Open the tab named in the address (#holidays) and keep it in the address when switching.
  var hash = window.location.hash;
  if (hash && $('#pfTabs a[href="' + hash + '"]').length) { $('#pfTabs a[href="' + hash + '"]').tab('show'); }
  (function () {
    var f = document.getElementById('pfLeaveFrom'), t = document.getElementById('pfLeaveTo'), o = document.getElementById('pfLeaveDays');
    if (!f || !t || !o) return;
    function upd() {
      if (t.value && f.value && t.value < f.value) { t.value = f.value; }
      t.min = f.value;
      var d = (f.value && t.value) ? Math.round((new Date(t.value) - new Date(f.value)) / 86400000) + 1 : 0;
      o.textContent = d > 0 ? d + (d === 1 ? ' day' : ' days') : 'Choose the dates';
    }
    f.addEventListener('change', upd); t.addEventListener('change', upd); upd();
  })();
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
