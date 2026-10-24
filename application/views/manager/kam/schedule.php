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
        <div class="box-header with-border" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
          <h3 class="box-title" style="margin:0;"><i class="fa fa-calendar-check-o text-primary"></i> Client Schedule Matrix</h3>
          <div class="box-tools" style="position:static; display:flex; gap:6px; flex-wrap:wrap;">
            <button type="button" class="btn btn-xs btn-default btn-apply-preset" data-preset="tax"><i class="fa fa-calculator"></i> Tax & Accounting</button>
            <button type="button" class="btn btn-xs btn-default btn-apply-preset" data-preset="corporate"><i class="fa fa-building"></i> Corporate Services</button>
            <button type="button" class="btn btn-xs btn-default btn-apply-preset" data-preset="audit"><i class="fa fa-balance-scale"></i> Audit & Compliance</button>
            <button type="button" class="btn btn-sm btn-primary" id="btn_add_sched_row"><i class="fa fa-plus"></i> Add Task Row</button>
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
                    'VAT Return Filing',
                    'Corporate Tax Filing',
                    'Monthly Bookkeeping',
                    'Financial Statements & MIS',
                    'Compliance Review & AML',
                    'Audit & Year-End Verification'
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
      <div class="box-header with-border" style="padding-bottom:0;">
        <ul class="nav nav-tabs" style="border-bottom:none; margin-bottom:-1px;">
          <li class="<?php echo (!isset($filter_tab) || $filter_tab === 'all') ? 'active' : ''; ?>">
            <a href="<?php echo base_url(); ?>manager/kam/schedule?tab=all" style="font-weight:600;"><i class="fa fa-list"></i> All Schedules</a>
          </li>
          <li class="<?php echo (isset($filter_tab) && $filter_tab === 'my') ? 'active' : ''; ?>">
            <a href="<?php echo base_url(); ?>manager/kam/schedule?tab=my" style="font-weight:600;"><i class="fa fa-user"></i> My Assigned Schedules</a>
          </li>
        </ul>
      </div>
      <div class="box-body" style="padding-top:15px;">
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
                  <th style="width:100px;" class="text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($schedules as $sch){ ?>
                  <tr>
                    <td><strong>#<?php echo $sch['id']; ?></strong></td>
                    <td><strong><?php echo htmlspecialchars($sch['client_name'] ?? ''); ?></strong></td>
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
                      <button type="button" class="btn btn-xs btn-primary btn-preview-matrix" data-client="<?php echo htmlspecialchars($sch['client_name'] ?? ''); ?>" data-year="<?php echo htmlspecialchars($sch['sched_year'] ?? '2026'); ?>" title="View Presentation Matrix"><i class="fa fa-th"></i></button>
                      <a href="<?php echo base_url(); ?>manager/kam/schedule/delete/<?php echo $sch['id']; ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete this schedule row?');" title="Delete Schedule"><i class="fa fa-trash"></i></a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        <?php } else { ?>
          <div class="alert alert-info" style="margin-top:10px;">No schedules recorded under this view. Create a schedule using the schedule matrix above.</div>
        <?php } ?>
      </div>
    </div>

    <div class="modal fade" id="modalMatrixPresentation" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" style="width:90%; max-width:1100px;">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><i class="fa fa-calendar-check-o text-primary"></i> <span id="modalClientTitle">Client Presentation Matrix</span></h4>
          </div>
          <div class="modal-body" id="modalMatrixBody" style="padding:15px; overflow-x:auto;">
            <div style="text-align:center; padding:20px; color:#64748b;">Loading schedule presentation...</div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="window.print();"><i class="fa fa-print"></i> Print Matrix</button>
          </div>
        </div>
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
  var presetProfiles = {
    tax: [
      'VAT Return Filing',
      'Corporate Tax Filing',
      'Monthly Bookkeeping',
      'Financial Statements & MIS',
      'Document Col/Sub',
      'Audit & Year-End Verification'
    ],
    corporate: [
      'Trade License Renewal',
      'Establishment Card Renewal',
      'MoL & Immigration Quota',
      'Tenancy Contract & Ejari',
      'Corporate Bank KYC Update',
      'AML Compliance Review'
    ],
    audit: [
      'Interim Audit Review',
      'Stock Taking & Verification',
      'Final Audit Preparation',
      'Draft Audit Report Submission',
      'Management Representation Letter',
      'FTA Regulatory Filing'
    ]
  };

  var defaultSchedules = presetProfiles.tax;

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
      rowHtml += '<input type="text" class="form-control input-sm sched-name-input" name="sched_name[' + idx + ']" value="' + $('<div>').text(item.name || item.title || '').html() + '" required style="font-size:12px; height:28px;">';
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

  $('.btn-apply-preset').on('click', function() {
    var pType = $(this).data('preset');
    if (presetProfiles[pType]) {
      var customList = presetProfiles[pType].map(function(sname) {
        return { name: sname, months: {} };
      });
      renderScheduleRows(customList);
    }
  });

  $(document).on('click', '.btn-preview-matrix', function() {
    var client = $(this).data('client');
    var year = $(this).data('year');
    $('#modalClientTitle').text(client + ' - ' + year + ' Annual Presentation Matrix');
    $('#modalMatrixBody').html('<div style="text-align:center; padding:20px; color:#64748b;"><i class="fa fa-spinner fa-spin"></i> Loading schedule data...</div>');
    $('#modalMatrixPresentation').modal('show');

    $.ajax({
      url: '<?php echo base_url(); ?>manager/kam/ajax_get_client_schedules',
      type: 'GET',
      data: { client_name: client, sched_year: year },
      dataType: 'json',
      success: function(resp) {
        if (resp && resp.status === 'success' && resp.schedules && resp.schedules.length > 0) {
          var mHtml = '<div class="table-responsive"><table class="table table-bordered table-striped" style="font-size:12px; margin-bottom:0;">';
          mHtml += '<thead><tr style="background:#0f172a; color:#ffffff;"><th style="min-width:180px;">Task / Service Deliverable</th>';
          schedMonths.forEach(function(m) {
            mHtml += '<th class="text-center" style="min-width:70px;">' + m + '</th>';
          });
          mHtml += '</tr></thead><tbody>';

          resp.schedules.forEach(function(item) {
            mHtml += '<tr><td style="font-weight:600; color:#1e293b;">' + $('<div>').text(item.name).html() + '</td>';
            schedMonths.forEach(function(m) {
              var mData = (item.months && item.months[m]) ? item.months[m] : null;
              var st = mData ? (mData.status || 'Not Applicable') : 'Not Applicable';
              var color = '#94a3b8';
              var bg = '#f8fafc';
              if (st === 'Approved') { color = '#16a34a'; bg = '#dcfce7'; }
              else if (st === 'Pending') { color = '#d97706'; bg = '#fef3c7'; }
              else if (st === 'Expired') { color = '#dc2626'; bg = '#fee2e2'; }
              mHtml += '<td class="text-center" style="background:' + bg + '; color:' + color + '; font-weight:600; font-size:11px; padding:6px 2px;">' + st + '</td>';
            });
            mHtml += '</tr>';
          });

          mHtml += '</tbody></table></div>';
          $('#modalMatrixBody').html(mHtml);
        } else {
          $('#modalMatrixBody').html('<div class="alert alert-warning" style="margin:0;">No detailed matrix rows found for this client schedule.</div>');
        }
      },
      error: function() {
        $('#modalMatrixBody').html('<div class="alert alert-danger" style="margin:0;">Error retrieving schedule data.</div>');
      }
    });
  });

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
