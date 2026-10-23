<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
  <section class="content-header">
    <h1><?php echo isset($info['page_heading']) ? $info['page_heading'] : 'Create Schedule'; ?></h1>
  </section>

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?> 
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <form role="form" action="<?php echo base_url(); ?>manager/kam/schedule" method="post" id="kamScheduleForm">
      
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Client &amp; Schedule Details</h3>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-lg-5 col-sm-12">
              <div class="form-group">
                <label>Client / Company Name<span>*</span></label>
                <select class="form-control select2" name="client_name" id="client_select" required style="width: 100%;">
                  <option value="">Select Client / Company...</option>
                  <?php if(isset($client_list) && !empty($client_list)){
                    foreach($client_list as $cl){ ?>
                      <option value="<?php echo htmlspecialchars($cl['client_name']); ?>" data-poc="<?php echo htmlspecialchars($cl['poc'] ?? ''); ?>" data-contact="<?php echo htmlspecialchars($cl['contact1'] ?? ''); ?>" data-email="<?php echo htmlspecialchars($cl['email'] ?? ''); ?>">
                        <?php echo htmlspecialchars($cl['client_name']); ?>
                      </option>
                  <?php } } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Schedule Year<span>*</span></label>
                <select class="form-control" name="sched_year" id="sched_year" style="width: 100%;">
                  <?php 
                    $curYear = (int)date('Y');
                    for($y = $curYear - 1; $y <= $curYear + 2; $y++){ 
                  ?>
                    <option value="<?php echo $y; ?>" <?php echo ($y == $curYear) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Assigned Executive<span>*</span></label>
                <select class="form-control select2" name="assigned_to" style="width: 100%;" required>
                  <?php 
                    $my_id = (int)$this->session->userdata('manager_id');
                    if(isset($u_list) && !empty($u_list)){
                      foreach($u_list as $u){ ?>
                        <option value="<?php echo $u['id']; ?>" <?php echo ($u['id'] == $my_id) ? 'selected' : ''; ?>>
                          <?php echo htmlspecialchars($u['name']); ?> (<?php echo htmlspecialchars($u['email']); ?>)
                        </option>
                  <?php } } ?>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-12">
              <div class="form-group" style="margin-bottom:0;">
                <label>Schedule Remarks &amp; Client Instructions</label>
                <textarea class="form-control" name="remarks" rows="2" placeholder="Enter deliverables, preparation guidelines, or agenda notes..."></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="box box-default">
        <div class="box-header with-border" style="display:flex; justify-content:space-between; align-items:center;">
          <h3 class="box-title" style="margin:0;"><i class="fa fa-calendar-check-o text-primary"></i> 12-Month Schedule Matrix</h3>
          <div class="box-tools pull-right" style="position:static;">
            <button type="button" class="btn btn-sm btn-primary" id="btn_add_sched_row"><i class="fa fa-plus"></i> Add Schedule Name</button>
          </div>
        </div>
        <div class="box-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped" id="table_create_schedule" style="font-size:12px; margin-bottom:0;">
              <thead>
                <tr style="background:#f1f5f9; color:#1e293b;">
                  <th style="min-width:180px; vertical-align:middle;">Schedule / Task Name</th>
                  <?php 
                    $sched_months = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
                    foreach($sched_months as $m_name){ 
                  ?>
                    <th class="text-center" style="min-width:92px; vertical-align:middle;"><?php echo $m_name; ?></th>
                  <?php } ?>
                  <th class="text-center" style="width:45px; vertical-align:middle;">Action</th>
                </tr>
              </thead>
              <tbody id="sched_tbody">
                <?php 
                  $default_scheds = array(
                    'SPR',
                    'Company Code',
                    'Lead Activity',
                    'Document Col/Sub',
                    'Pending Documents',
                    'Video Verification',
                    'Post Verification'
                  );
                  foreach($default_scheds as $idx => $sname){
                ?>
                <tr class="sched-row" data-row-index="<?php echo $idx; ?>">
                  <td style="vertical-align:middle;">
                    <input type="text" class="form-control input-sm sched-name-input" name="sched_name[<?php echo $idx; ?>]" value="<?php echo htmlspecialchars($sname); ?>" required style="font-size:12px; height:28px;">
                  </td>
                  <?php foreach($sched_months as $m_name){ ?>
                  <td class="text-center" style="padding:4px 3px; vertical-align:middle;">
                    <div style="display:flex; flex-direction:column; align-items:center; gap:2px;">
                      <select class="form-control input-sm" name="sched_status[<?php echo $idx; ?>][<?php echo $m_name; ?>]" style="font-size:11px; height:24px; padding:1px 3px; min-width:82px; width:100%;">
                        <option value="Not Applicable" selected>Not Applicable</option>
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Expired">Expired</option>
                        <option value="Other">Other</option>
                      </select>
                      <label style="font-weight:normal; margin:0; cursor:pointer;" title="<?php echo $m_name; ?> Active">
                        <input type="checkbox" name="sched_check[<?php echo $idx; ?>][<?php echo $m_name; ?>]" value="1" style="margin:0;">
                      </label>
                    </div>
                  </td>
                  <?php } ?>
                  <td class="text-center" style="vertical-align:middle;">
                    <button type="button" class="btn btn-xs btn-danger btn-remove-sched-row" title="Remove row"><i class="fa fa-times"></i></button>
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="box-footer text-center" style="padding: 15px; background: transparent;">
          <button type="submit" class="btn btn-danger btn-lg" style="min-width: 180px;"><i class="fa fa-calendar-check-o"></i> Save Schedule</button>
          <a href="<?php echo base_url(); ?>manager/kam/my-client" class="btn btn-default btn-lg" style="margin-left: 10px;">Cancel</a>
        </div>
      </div>

    </form>

    <div class="box box-default" style="margin-top: 25px;">
      <div class="box-header with-border">
        <h3 class="box-title">Active Client Schedules</h3>
      </div>
      <div class="box-body">
        <?php if(isset($schedules) && !empty($schedules)){ ?>
          <div class="table-responsive">
            <table id="example_lead" class="table table-hover table-bordered table-striped" style="width: 100%;">
              <thead>
                <tr>
                  <th style="width:60px;">ID</th>
                  <th>Client / Company</th>
                  <th>Schedule Name</th>
                  <th style="width:70px;">Year</th>
                  <th>Assigned To</th>
                  <th style="width:100px;">Status</th>
                  <th style="width:90px;">Created</th>
                  <th style="width:60px;" class="text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($schedules as $sch){ ?>
                  <tr>
                    <td><strong>#<?php echo $sch['id']; ?></strong></td>
                    <td><?php echo htmlspecialchars($sch['client_name'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($sch['title'] ?? ''); ?></td>
                    <td><span class="label label-default"><?php echo htmlspecialchars($sch['sched_year'] ?? '2026'); ?></span></td>
                    <td><?php echo htmlspecialchars($sch['assigned_name'] ?? 'Self'); ?></td>
                    <td>
                      <?php 
                      $badge = 'label-default';
                      if($sch['status'] == 'Approved' || $sch['status'] == 'Completed' || $sch['status'] == 'Active') $badge = 'label-success';
                      elseif($sch['status'] == 'Pending' || $sch['status'] == 'Scheduled' || $sch['status'] == 'In Progress') $badge = 'label-warning';
                      elseif($sch['status'] == 'Expired' || $sch['status'] == 'Cancelled') $badge = 'label-danger';
                      elseif($sch['status'] == 'Other') $badge = 'label-info';
                      ?>
                      <span class="label <?php echo $badge; ?>"><?php echo htmlspecialchars($sch['status']); ?></span>
                    </td>
                    <td><?php echo !empty($sch['created_at']) ? date('d-m-Y', strtotime($sch['created_at'])) : '-'; ?></td>
                    <td class="text-center">
                      <a href="<?php echo base_url(); ?>manager/kam/schedule/delete/<?php echo $sch['id']; ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete this schedule row?');" title="Delete Schedule"><i class="fa fa-trash"></i></a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        <?php } else { ?>
          <div class="alert alert-info">No schedules recorded yet. Create a schedule using the 12-month matrix above.</div>
        <?php } ?>
      </div>
    </div>

  </section>
</div>

<script>
$(document).ready(function() {
  if ($.fn.select2) {
    $('.select2').select2();
  }

  if ($('#example_lead').length) {
    $('#example_lead').DataTable({
      "pageLength": 25,
      responsive: true
    });
  }

  var schedMonths = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  var defaultSchedules = [
    'SPR',
    'Company Code',
    'Lead Activity',
    'Document Col/Sub',
    'Pending Documents',
    'Video Verification',
    'Post Verification'
  ];

  function renderScheduleRows(schedulesList) {
    var tbody = $('#sched_tbody');
    tbody.empty();

    if (!schedulesList || schedulesList.length === 0) {
      schedulesList = defaultSchedules.map(function(name) {
        return { name: name, months: {} };
      });
    }

    schedulesList.forEach(function(item, idx) {
      var rowHtml = '<tr class="sched-row" data-row-index="' + idx + '">';
      rowHtml += '<td style="vertical-align:middle;">';
      rowHtml += '<input type="text" class="form-control input-sm sched-name-input" name="sched_name[' + idx + ']" value="' + $('<div>').text(item.name).html() + '" required style="font-size:12px; height:28px;">';
      rowHtml += '</td>';

      schedMonths.forEach(function(mn) {
        var mData = (item.months && item.months[mn]) ? item.months[mn] : null;
        var curStatus = mData ? (mData.status || 'Not Applicable') : 'Not Applicable';
        var isChecked = mData ? (parseInt(mData.checked, 10) === 1) : false;

        rowHtml += '<td class="text-center" style="padding:4px 3px; vertical-align:middle;">';
        rowHtml += '<div style="display:flex; flex-direction:column; align-items:center; gap:2px;">';
        rowHtml += '<select class="form-control input-sm" name="sched_status[' + idx + '][' + mn + ']" style="font-size:11px; height:24px; padding:1px 3px; min-width:82px; width:100%;">';
        rowHtml += '<option value="Not Applicable"' + (curStatus === 'Not Applicable' ? ' selected' : '') + '>Not Applicable</option>';
        rowHtml += '<option value="Pending"' + (curStatus === 'Pending' ? ' selected' : '') + '>Pending</option>';
        rowHtml += '<option value="Approved"' + (curStatus === 'Approved' ? ' selected' : '') + '>Approved</option>';
        rowHtml += '<option value="Expired"' + (curStatus === 'Expired' ? ' selected' : '') + '>Expired</option>';
        rowHtml += '<option value="Other"' + (curStatus === 'Other' ? ' selected' : '') + '>Other</option>';
        rowHtml += '</select>';
        rowHtml += '<label style="font-weight:normal; margin:0; cursor:pointer;" title="' + mn + ' Active">';
        rowHtml += '<input type="checkbox" name="sched_check[' + idx + '][' + mn + ']" value="1"' + (isChecked ? ' checked' : '') + ' style="margin:0;">';
        rowHtml += '</label>';
        rowHtml += '</div>';
        rowHtml += '</td>';
      });

      rowHtml += '<td class="text-center" style="vertical-align:middle;">';
      rowHtml += '<button type="button" class="btn btn-xs btn-danger btn-remove-sched-row" title="Remove row"><i class="fa fa-times"></i></button>';
      rowHtml += '</td>';
      rowHtml += '</tr>';

      tbody.append(rowHtml);
    });
  }

  $('#btn_add_sched_row').on('click', function(e) {
    e.preventDefault();
    var nextIdx = $('#sched_tbody tr').length;
    var rowHtml = '<tr class="sched-row" data-row-index="' + nextIdx + '">';
    rowHtml += '<td style="vertical-align:middle;"><input type="text" class="form-control input-sm sched-name-input" name="sched_name[' + nextIdx + ']" placeholder="Enter Schedule Name" required style="font-size:12px; height:28px;"></td>';
    for (var m = 0; m < schedMonths.length; m++) {
      var mn = schedMonths[m];
      rowHtml += '<td class="text-center" style="padding:4px 3px; vertical-align:middle;">';
      rowHtml += '<div style="display:flex; flex-direction:column; align-items:center; gap:2px;">';
      rowHtml += '<select class="form-control input-sm" name="sched_status[' + nextIdx + '][' + mn + ']" style="font-size:11px; height:24px; padding:1px 3px; min-width:82px; width:100%;">';
      rowHtml += '<option value="Not Applicable" selected>Not Applicable</option>';
      rowHtml += '<option value="Pending">Pending</option>';
      rowHtml += '<option value="Approved">Approved</option>';
      rowHtml += '<option value="Expired">Expired</option>';
      rowHtml += '<option value="Other">Other</option>';
      rowHtml += '</select>';
      rowHtml += '<label style="font-weight:normal; margin:0; cursor:pointer;" title="' + mn + ' Active">';
      rowHtml += '<input type="checkbox" name="sched_check[' + nextIdx + '][' + mn + ']" value="1" style="margin:0;">';
      rowHtml += '</label>';
      rowHtml += '</div>';
      rowHtml += '</td>';
    }
    rowHtml += '<td class="text-center" style="vertical-align:middle;"><button type="button" class="btn btn-xs btn-danger btn-remove-sched-row" title="Remove row"><i class="fa fa-times"></i></button></td>';
    rowHtml += '</tr>';
    $('#sched_tbody').append(rowHtml);
  });

  $(document).on('click', '.btn-remove-sched-row', function(e) {
    e.preventDefault();
    if ($('#sched_tbody tr').length > 1) {
      $(this).closest('tr').remove();
    } else {
      alert('At least one schedule row is required.');
    }
  });

  $('#client_select, #sched_year').on('change', function() {
    var cname = $('#client_select').val();
    var syear = $('#sched_year').val();
    if (!cname) return;

    $.ajax({
      url: '<?php echo base_url(); ?>manager/kam/ajax_get_client_schedules',
      type: 'GET',
      data: { client_name: cname, sched_year: syear },
      dataType: 'json',
      success: function(resp) {
        if (resp && resp.status === 'success' && resp.schedules && resp.schedules.length > 0) {
          renderScheduleRows(resp.schedules);
        } else {
          renderScheduleRows([]);
        }
      }
    });
  });
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
