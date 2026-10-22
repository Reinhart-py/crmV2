<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
  <section class="content-header">
    <h1> <?php echo isset($info['page_heading']) ? $info['page_heading'] : 'Create Schedule'; ?> </h1>
  </section>

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?> 
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <form role="form" action="<?php echo base_url(); ?>manager/kam/schedule" method="post" name="kam_schedule_form">
      
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Client Information</h3>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-lg-6 col-sm-12">
              <div class="form-group">
                <label>Client / Company Name<span>*</span></label>
                <select class="form-control select2-company-ajax" name="client_name" required style="width: 100%;">
                  <option value="">Type company name (min 3 characters)...</option>
                </select>
              </div>
            </div>
            <div class="col-lg-6 col-sm-12">
              <div class="form-group">
                <label>Schedule Title / Purpose<span>*</span></label>
                <input type="text" class="form-control" name="title" placeholder="e.g. Weekly Accounts Review / VAT Filing Audit" required>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Recurrence & Timing</h3>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Service Category</label>
                <select class="form-control select2" name="service_type" style="width: 100%;">
                  <option value="">Select Category</option>
                  <?php if(isset($p_cat) && !empty($p_cat)){
                    foreach($p_cat as $cat){ ?>
                      <option value="<?php echo htmlspecialchars($cat['title']); ?>"><?php echo htmlspecialchars($cat['title']); ?></option>
                  <?php } } ?>
                  <option value="Accounting">Accounting</option>
                  <option value="Audit & Tax">Audit & Tax</option>
                  <option value="VAT Compliance">VAT Compliance</option>
                  <option value="Corporate Tax">Corporate Tax</option>
                  <option value="Consulting / CFO">Consulting / CFO</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Frequency (Recurrence)<span>*</span></label>
                <select class="form-control select2" name="frequency" id="sch_frequency" style="width: 100%;" required>
                  <option value="Once">Does not repeat (Once)</option>
                  <option value="Daily">Daily</option>
                  <option value="Weekly">Weekly</option>
                  <option value="Monthly">Monthly</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Start Date<span>*</span></label>
                <input type="text" class="form-control datepicker" name="start_date" value="<?php echo date('d-m-Y'); ?>" required autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>End / Until Date</label>
                <input type="text" class="form-control datepicker" name="end_date" placeholder="Leave blank if indefinite" autocomplete="off">
              </div>
            </div>
          </div>

          <div class="row" id="weekly_days_row" style="display: none; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px 10px; margin: 0 0 15px 0;">
            <div class="col-xs-12">
              <label style="display: block; margin-bottom: 8px;">Repeat on Days:</label>
              <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <?php 
                $days = array('Mon' => 'Monday', 'Tue' => 'Tuesday', 'Wed' => 'Wednesday', 'Thu' => 'Thursday', 'Fri' => 'Friday', 'Sat' => 'Saturday', 'Sun' => 'Sunday');
                foreach($days as $k => $dname){ ?>
                  <label style="font-weight: normal; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                    <input type="checkbox" name="repeat_days[]" value="<?php echo $k; ?>"> <?php echo $k; ?>
                  </label>
                <?php } ?>
              </div>
            </div>
          </div>

          <div class="row" id="monthly_day_row" style="display: none;">
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Day of Month (1 - 31)</label>
                <select class="form-control" name="repeat_day_month">
                  <option value="">Select Day</option>
                  <?php for($day_i = 1; $day_i <= 31; $day_i++){ ?>
                    <option value="<?php echo $day_i; ?>"><?php echo $day_i . date('S', mktime(0, 0, 0, 1, $day_i)); ?> of every month</option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Start Time</label>
                <div class="input-group bootstrap-timepicker timepicker">
                  <input type="text" class="form-control input-small timepicker" name="start_time" value="10:00 AM">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>End Time</label>
                <div class="input-group bootstrap-timepicker timepicker">
                  <input type="text" class="form-control input-small timepicker" name="end_time" value="11:00 AM">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-sm-6">
              <div class="form-group" style="position: relative;">
                <label>Assigned Executive<span>*</span></label>
                <div class="input-group">
                  <input type="text" id="exec_search_input" class="form-control" placeholder="Type 3+ letters to search..." autocomplete="off">
                  <span class="input-group-btn">
                    <button class="btn btn-default" type="button" onclick="clearExecSelection()"><i class="fa fa-times"></i></button>
                  </span>
                </div>
                <input type="hidden" name="assigned_to" id="exec_assigned_to_input" value="<?php echo (int)$this->session->userdata('manager_id'); ?>">
                <div id="exec_search_dropdown" style="display:none; position:absolute; top:100%; left:0; right:0; z-index:9999; background:#fff; border:1px solid #cbd5e1; border-radius:4px; box-shadow:0 8px 16px rgba(0,0,0,0.12); max-height:220px; overflow-y:auto; margin-top:2px;"></div>
              </div>
            </div>
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Status</label>
                <select class="form-control" name="status">
                  <option value="Scheduled">Scheduled</option>
                  <option value="In Progress">In Progress</option>
                  <option value="Completed">Completed</option>
                  <option value="Cancelled">Cancelled</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-12">
              <div class="form-group">
                <label>Schedule Remarks & Client Instructions</label>
                <textarea class="form-control" name="remarks" rows="3" placeholder="Enter deliverables, preparation guidelines, or agenda notes..."></textarea>
              </div>
            </div>
          </div>

        </div>
      </div>

      <div class="box-footer text-center" style="padding: 15px; background: transparent;">
        <button type="submit" class="btn btn-danger btn-lg" style="min-width: 180px;"><i class="fa fa-calendar-check-o"></i> Save Schedule</button>
        <a href="<?php echo base_url(); ?>manager/kam/my-client" class="btn btn-default btn-lg" style="margin-left: 10px;">Cancel</a>
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
                  <th>ID</th>
                  <th>Client / Company</th>
                  <th>Schedule Title</th>
                  <th>Service</th>
                  <th>Frequency</th>
                  <th>Start Date</th>
                  <th>Time Slot</th>
                  <th>Assigned To</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($schedules as $sch){ ?>
                  <tr>
                    <td><strong>#<?php echo $sch['id']; ?></strong></td>
                    <td><?php echo htmlspecialchars($sch['client_name'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($sch['title'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($sch['service_type'] ?? '-'); ?></td>
                    <td>
                      <span class="label label-primary"><?php echo htmlspecialchars($sch['frequency']); ?></span>
                      <?php if(!empty($sch['repeat_days'])){ ?>
                        <br><small class="text-muted"><?php echo htmlspecialchars($sch['repeat_days']); ?></small>
                      <?php } ?>
                    </td>
                    <td><?php echo !empty($sch['start_date']) ? simple_date($sch['start_date']) : '-'; ?></td>
                    <td><?php echo (!empty($sch['start_time']) ? htmlspecialchars($sch['start_time']) : '') . (!empty($sch['end_time']) ? ' - '.htmlspecialchars($sch['end_time']) : ''); ?></td>
                    <td><?php echo htmlspecialchars($sch['assigned_name'] ?? 'Self'); ?></td>
                    <td>
                      <?php 
                      $badge = 'label-info';
                      if($sch['status'] == 'Completed') $badge = 'label-success';
                      elseif($sch['status'] == 'Cancelled') $badge = 'label-danger';
                      ?>
                      <span class="label <?php echo $badge; ?>"><?php echo htmlspecialchars($sch['status']); ?></span>
                    </td>
                    <td>
                      <div class="btn-group" style="min-width: 85px;">
                        <button type="button" class="btn btn-sm btn-warning">Action</button>
                        <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                          <span class="caret"></span>
                          <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                          <li><a href="<?php echo base_url(); ?>manager/kam/my-client">View Client</a></li>
                        </ul>
                      </div>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        <?php } else { ?>
          <div class="alert alert-info">No schedules created yet. Create a schedule using the form above.</div>
        <?php } ?>
      </div>
    </div>

  </section>
</div>

<script>
$(document).ready(function() {
  if ($.fn.select2) {
    $('.select2').select2();

    $('.select2-company-ajax').select2({
      placeholder: 'Type company name (min 3 characters)...',
      minimumInputLength: 3,
      ajax: {
        url: '<?php echo base_url(); ?>manager/leads/ajax_search_companies',
        dataType: 'json',
        delay: 250,
        data: function(params) {
          return { q: params.term };
        },
        processResults: function(data) {
          return { results: data.results || [] };
        },
        cache: true
      }
    });
  }

  $('#sch_frequency').on('change', function() {
    var freq = $(this).val();
    if (freq === 'Weekly') {
      $('#weekly_days_row').slideDown(150);
      $('#monthly_day_row').slideUp(150);
    } else if (freq === 'Monthly') {
      $('#weekly_days_row').slideUp(150);
      $('#monthly_day_row').slideDown(150);
    } else {
      $('#weekly_days_row').slideUp(150);
      $('#monthly_day_row').slideUp(150);
    }
  });

  if ($('#example_lead').length) {
    $('#example_lead').DataTable({
      "pageLength": 25,
      responsive: true
    });
  }
});

var execUsers = <?php 
  $clean_u = array();
  if(!empty($u_list)){
    foreach($u_list as $u){
      $clean_u[] = array(
        'id' => (int)$u['id'],
        'name' => htmlspecialchars($u['name']),
        'email' => htmlspecialchars($u['email'])
      );
    }
  }
  echo json_encode($clean_u);
?>;

var defaultExecId = <?php echo (int)$this->session->userdata('manager_id'); ?>;
var defaultExecName = "<?php echo addslashes($this->session->userdata('manager_name')); ?>";

document.addEventListener("DOMContentLoaded", function() {
  var execInput = document.getElementById("exec_search_input");
  var execHidden = document.getElementById("exec_assigned_to_input");
  var execDropdown = document.getElementById("exec_search_dropdown");

  if (defaultExecId > 0 && defaultExecName !== "") {
    execInput.value = defaultExecName;
  }

  execInput.addEventListener("input", function() {
    var query = this.value.trim().toLowerCase();
    execDropdown.innerHTML = "";
    
    if (query.length < 3) {
      execDropdown.style.display = "none";
      return;
    }

    var matches = execUsers.filter(function(u) {
      return u.name.toLowerCase().indexOf(query) !== -1 || u.email.toLowerCase().indexOf(query) !== -1;
    });

    if (matches.length === 0) {
      execDropdown.innerHTML = '<div style="padding:10px 14px; color:#64748b; font-size:13px;">No executive found</div>';
      execDropdown.style.display = "block";
      return;
    }

    matches.forEach(function(u) {
      var item = document.createElement("div");
      item.style.padding = "9px 14px";
      item.style.cursor = "pointer";
      item.style.borderBottom = "1px solid #f1f5f9";
      item.style.fontSize = "13px";
      item.innerHTML = '<strong>' + u.name + '</strong> <span style="color:#64748b; font-size:12px;">(' + u.email + ')</span>';
      
      item.addEventListener("mouseenter", function() {
        this.style.background = "#eff6ff";
      });
      item.addEventListener("mouseleave", function() {
        this.style.background = "#fff";
      });

      item.addEventListener("click", function() {
        execInput.value = u.name;
        execHidden.value = u.id;
        execDropdown.style.display = "none";
      });

      execDropdown.appendChild(item);
    });

    execDropdown.style.display = "block";
  });

  document.addEventListener("click", function(e) {
    if (!execInput.contains(e.target) && !execDropdown.contains(e.target)) {
      execDropdown.style.display = "none";
    }
  });
});

function clearExecSelection() {
  document.getElementById("exec_search_input").value = "";
  document.getElementById("exec_assigned_to_input").value = "0";
  document.getElementById("exec_search_dropdown").style.display = "none";
  document.getElementById("exec_search_input").focus();
}
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
