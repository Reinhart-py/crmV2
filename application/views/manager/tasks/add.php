<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="container">
<div class="content-wrapper"> 
  <section class="content-header">
    <h1 id="page_heading_title"><?php echo ($default_scope === 'external') ? 'Add External Task' : 'Add Internal Task'; ?></h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i>Home</a></li>
      <li><a href="<?php echo ($default_scope === 'external') ? base_url().'manager/tasks/external' : base_url().'manager/tasks/internal'; ?>">Tasks</a></li>
      <li class="active">Add Task</li>
    </ol>
  </section>
  
  <section class="content"> 
    <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
    <?php echo get_message($this->session->flashdata('message'),'message'); ?>
     
    <form role="form" action="<?php echo base_url(); ?>manager/tasks/add" method="post" name="add_form" id="task_add_form">
      <div class="box box-default" style="border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div class="box-header with-border">
          <h3 class="box-title" id="box_section_title" style="font-weight:600; color:#0f172a;">
            <?php echo ($default_scope === 'external') ? 'External Task Configuration' : 'Internal Task Configuration'; ?>
          </h3>
          <div class="pull-right">
            <a href="<?php echo ($default_scope === 'external') ? base_url().'manager/tasks/external' : base_url().'manager/tasks/internal'; ?>" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
          </div>
        </div>
      
        <div class="box-body">
          <div class="row">
            <div class="col-xs-12" style="margin-bottom: 20px;">
              <label style="font-size: 13px; font-weight: 600; color: #334155; margin-right: 25px;">Task Scope:</label>
              <?php if(!empty($can_create_internal)){ ?>
              <label class="radio-inline" style="font-weight: 600; color: #1e293b;">
                <input type="radio" name="task_scope" id="scope_internal" value="internal" <?php echo ($default_scope === 'internal') ? 'checked' : ''; ?> onchange="toggleTaskScope()"> Internal Task
              </label>
              <?php } ?>
              <?php if(!empty($can_create_external)){ ?>
              <label class="radio-inline" style="font-weight: 600; color: #1e293b;">
                <input type="radio" name="task_scope" id="scope_external" value="external" <?php echo ($default_scope === 'external' || empty($can_create_internal)) ? 'checked' : ''; ?> onchange="toggleTaskScope()"> External (Client) Task
              </label>
              <?php } ?>
            </div>
          </div>

          <div id="external_section" style="<?php echo ($default_scope === 'external' || empty($can_create_internal)) ? '' : 'display:none;'; ?>">
            <div class="row">
              <div class="col-lg-5 col-sm-6">
                <div class="form-group">
                  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:5px;">
                    <label style="margin-bottom:0;">Select Company<span>*</span></label>
                    <a href="<?php echo base_url(); ?>manager/kam/onboard" target="_blank" style="font-size:12px; color:#2563eb; font-weight:600;">
                      <i class="fa fa-plus"></i> Onboard New Client
                    </a>
                  </div>
                  <select class="form-control" name="client_id" id="client_select" onchange="onClientSelected()">
                    <option value="">-- Choose Company --</option>
                    <?php if(!empty($client_list)){ foreach($client_list as $cli){ ?>
                      <option value="<?php echo $cli['id']; ?>" data-poc="<?php echo htmlspecialchars($cli['poc']); ?>" data-contact="<?php echo htmlspecialchars($cli['contact1']); ?>" data-email="<?php echo htmlspecialchars($cli['email']); ?>" data-name="<?php echo htmlspecialchars($cli['customer']); ?>">
                        <?php echo htmlspecialchars($cli['customer']); ?>
                      </option>
                    <?php }} ?>
                  </select>
                </div>
              </div>

              <div class="col-lg-3 col-sm-6">
                <div class="form-group">
                  <label>Client POC</label>
                  <input type="text" class="form-control" name="poc" id="client_poc" placeholder="Contact person" autocomplete="off">
                </div>
              </div>

              <div class="col-lg-2 col-sm-6">
                <div class="form-group">
                  <label>Client Phone</label>
                  <input type="text" class="form-control" name="contact1" id="client_phone" placeholder="Phone number" autocomplete="off">
                </div>
              </div>

              <div class="col-lg-2 col-sm-6">
                <div class="form-group">
                  <label>Client Email</label>
                  <input type="email" class="form-control" name="email" id="client_email" placeholder="Email" autocomplete="off">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-4 col-sm-6">
                <div class="form-group">
                  <label>Task Category / Vertical</label>
                  <select class="form-control" name="business_category" id="business_category_select" onchange="onBusinessCategoryChanged()">
                    <option value="General" selected>General</option>
                    <option value="Telecom">Telecom</option>
                  </select>
                </div>
              </div>
            </div>

            <div id="telecom_section" style="display:none; margin-bottom:15px;">
              <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:14px 16px;">
                <h4 style="margin-top:0; margin-bottom:12px; font-size:14px; font-weight:600; color:#0f172a;">Telecom Connection Details</h4>
                <div class="row">
                  <div class="col-lg-3 col-sm-6">
                    <div class="form-group">
                      <label>Connection Type</label>
                      <div style="padding-top:7px;">
                        <span class="label label-primary" style="font-size:12px; font-weight:600; padding:4px 8px;">New Connection</span>
                        <input type="hidden" name="connection_type" value="New Connection">
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-3 col-sm-6">
                    <div class="form-group">
                      <label>Product Category</label>
                      <select class="form-control" name="p_cat" id="telecom_p_cat">
                        <option value="">-- Select Product --</option>
                        <?php if(!empty($telecom_cats)){ foreach($telecom_cats as $tc){ ?>
                          <option value="<?php echo htmlspecialchars($tc['name']); ?>"><?php echo htmlspecialchars($tc['name']); ?></option>
                        <?php }} else { ?>
                          <option value="Fixed">Fixed</option>
                          <option value="Mobile">Mobile</option>
                          <option value="Managed">Managed</option>
                          <option value="Other">Other</option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-3 col-sm-6">
                    <div class="form-group">
                      <label>Quantity</label>
                      <input type="number" min="1" class="form-control" name="qty" id="telecom_qty" value="1">
                    </div>
                  </div>
                  <div class="col-lg-3 col-sm-6">
                    <div class="form-group">
                      <label>Monthly Recurring Charge (MRC)</label>
                      <input type="number" step="0.01" class="form-control" name="mrc" id="telecom_mrc" placeholder="0.00">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-12">
                    <div class="form-group" style="margin-bottom:0;">
                      <label>Plan & Connection Details</label>
                      <input type="text" class="form-control" name="p_details" id="telecom_p_details" placeholder="Plan name, speed, allowance, or connection specifications" autocomplete="off">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Department<span>*</span></label>
                <select class="form-control" name="a_department" id="task_department" onchange="onDepartmentChanged()" required>
                  <option value="Procurement">Procurement</option>
                  <option value="Sales" selected>Sales</option>
                  <option value="HR">HR</option>
                  <option value="Admin">Admin</option>
                  <option value="IT">IT</option>
                  <option value="Accounts">Accounts</option>
                  <option value="Operations">Operations</option>
                </select>
                <input type="hidden" name="category" id="hidden_category" value="Sales">
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Task Title<span>*</span></label>
                <input type="text" name="customer" id="task_title_input" class="form-control" placeholder="Enter task title" required autocomplete="off">
              </div>
            </div>

            <div id="internal_employee_box" class="col-lg-4 col-sm-6" style="<?php echo ($default_scope === 'external') ? 'display:none;' : ''; ?>">
              <div class="form-group">
                <label>Person Task Is For / Employee Responsible</label>
                <select class="form-control" name="raised_by" id="raised_by_select">
                  <option value="">-- Select Employee --</option>
                  <?php if(!empty($u_list)){ foreach($u_list as $usr){ ?>
                    <option value="<?php echo $usr['id']; ?>" <?php echo ((int)$usr['id'] === (int)$this->session->userdata('manager_id')) ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($usr['name']); ?>
                    </option>
                  <?php }} ?>
                </select>
              </div>
            </div>

            <div class="col-lg-2 col-sm-6">
              <div class="form-group">
                <label>Priority</label>
                <select class="form-control" name="dnd_status">
                  <option value="Normal" selected>Normal</option>
                  <option value="High">High</option>
                  <option value="Urgent">Urgent</option>
                </select>
              </div>
            </div>

            <div class="col-lg-2 col-sm-6">
              <div class="form-group">
                <label>Status</label>
                <select class="form-control" name="status">
                  <option value="Open" selected>Open</option>
                  <option value="In Progress">In Progress</option>
                  <option value="Completed">Completed</option>
                  <option value="Closed">Closed</option>
                </select>
              </div>
            </div>
          </div>

          <div id="accounts_section" style="display:none; margin-top:10px; margin-bottom:15px;">
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:16px;">
              <h4 style="margin-top:0; margin-bottom:14px; font-size:14px; font-weight:600; color:#0f172a;">Accounts & Invoicing</h4>
              <div class="row">
                <div class="col-lg-3 col-sm-6">
                  <div class="form-group">
                    <label>Invoiced<span>*</span></label>
                    <select class="form-control" name="invoiced" id="accounts_invoiced_select" onchange="onInvoicedChanged()">
                      <option value="No" selected>No (To Be Invoiced)</option>
                      <option value="Yes">Yes</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row" id="invoice_details_row" style="display:none;">
                <div class="col-lg-3 col-sm-6">
                  <div class="form-group">
                    <label>Invoice Reference</label>
                    <input type="text" class="form-control" name="invoice_ref" id="invoice_ref_input" placeholder="INV-00123" autocomplete="off">
                  </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                  <div class="form-group">
                    <label>Invoice Date</label>
                    <input type="text" class="form-control datepicker" name="invoice_date" id="invoice_date_input" placeholder="DD-MM-YYYY" autocomplete="off">
                  </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                  <div class="form-group">
                    <label>Invoice Amount</label>
                    <input type="number" step="0.01" class="form-control" name="invoice_amount" id="invoice_amount_input" placeholder="0.00">
                  </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                  <div class="form-group">
                    <label>Invoice Value</label>
                    <input type="number" step="0.01" class="form-control" name="invoice_value" id="invoice_value_input" placeholder="0.00">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-4 col-sm-6">
              <div class="form-group" style="position: relative;">
                <label>Assign To Person<span>*</span></label>
                <div class="input-group">
                  <input type="text" id="user_search_input" class="form-control" placeholder="Type 3+ letters to search user..." autocomplete="off">
                  <span class="input-group-btn">
                    <button class="btn btn-default" type="button" onclick="clearUserSelection()"><i class="fa fa-times"></i></button>
                  </span>
                </div>
                <input type="hidden" name="assigned_to" id="assigned_to_input" value="<?php echo (int)$this->session->userdata('manager_id'); ?>">
                <div id="user_search_dropdown" style="display:none; position:absolute; top:100%; left:0; right:0; z-index:9999; background:#fff; border:1px solid #cbd5e1; border-radius:4px; box-shadow:0 8px 16px rgba(0,0,0,0.12); max-height:220px; overflow-y:auto; margin-top:2px;"></div>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Follow-up Date / Start</label>
                <input type="text" class="form-control datepicker" name="followup_date" id="followup_date_input" value="<?php echo date('d-m-Y'); ?>" placeholder="DD-MM-YYYY" autocomplete="off" onchange="renderRecurrencePreview()">
              </div>
            </div>

            <div class="col-lg-2 col-sm-6">
              <div class="form-group">
                <label>Follow-up Time</label>
                <div class="input-group bootstrap-timepicker timepicker">
                  <input type="text" class="form-control input-small timepicker" name="followup_time" id="followup_time_input" value="<?php echo date('h:i A'); ?>" autocomplete="off" onchange="renderRecurrencePreview()">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Due / Completion Date</label>
                <input type="text" class="form-control datepicker" name="close_date" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>
          </div>

          <div class="row" style="margin-top: 10px; margin-bottom: 10px;">
            <div class="col-xs-12">
              <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 16px;">
                <input type="hidden" name="is_recurring" id="is_recurring_input" value="0">
                <div class="row">
                  <div class="col-lg-4 col-sm-6">
                    <div class="form-group">
                      <label>Recurrence<span>*</span></label>
                      <select class="form-control" name="recurrence_freq" id="rec_freq_select" onchange="onRecurrenceFreqChange()">
                        <option value="One-Time" selected>One-Time</option>
                        <option value="Weekly">Weekly</option>
                        <option value="Twice a Week">Twice a Week</option>
                        <option value="Three Times a Week">Three Times a Week</option>
                        <option value="Monthly">Monthly</option>
                        <option value="Twice a Month">Twice a Month</option>
                        <option value="Three Times a Month">Three Times a Month</option>
                        <option value="Quarterly">Quarterly</option>
                        <option value="Half-Yearly">Half-Yearly</option>
                        <option value="Yearly">Yearly</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-lg-8 col-sm-6">
                    <div id="rec_days_box" class="form-group" style="display:none;">
                      <label id="rec_days_label">Select Weekday</label>
                      <div style="padding-top: 4px;" id="weekday_checkboxes_container">
                        <?php foreach(array('Mon'=>'Mon', 'Tue'=>'Tue', 'Wed'=>'Wed', 'Thu'=>'Thu', 'Fri'=>'Fri', 'Sat'=>'Sat', 'Sun'=>'Sun') as $k=>$v){ ?>
                          <label class="checkbox-inline" style="font-weight:normal; margin-left:0; margin-right:12px;">
                            <input type="checkbox" name="rec_days[]" value="<?php echo $k; ?>" onchange="onWeekdayChanged()"> <?php echo $v; ?>
                          </label>
                        <?php } ?>
                      </div>
                      <div id="weekday_help_text" style="font-size:12px; color:#64748b; margin-top:4px;"></div>
                    </div>

                    <div id="rec_month_box" class="form-group" style="display:none;">
                      <label>Day of Month (1-31)</label>
                      <div class="row">
                        <div class="col-xs-6">
                          <select class="form-control" name="rec_month_day" id="rec_month_day_select" onchange="renderRecurrencePreview()">
                            <?php for($d=1; $d<=31; $d++){ ?>
                              <option value="<?php echo $d; ?>"><?php echo $d . date('S', mktime(0,0,0,1,$d,2026)); ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <div class="col-xs-6" style="padding-top: 6px;">
                          <label class="checkbox-inline" style="font-weight:normal;">
                            <input type="checkbox" name="rec_is_last" id="rec_is_last_chk" value="1" onchange="renderRecurrencePreview()"> Last day of month
                          </label>
                        </div>
                      </div>
                    </div>

                    <div id="rec_twice_month_box" class="form-group" style="display:none;">
                      <label>Select 2 Dates in Month</label>
                      <div class="row">
                        <div class="col-xs-6">
                          <input type="number" min="1" max="31" class="form-control" name="rec_twice_d1" id="rec_twice_d1" value="1" placeholder="1st Date" onchange="renderRecurrencePreview()">
                        </div>
                        <div class="col-xs-6">
                          <input type="number" min="1" max="31" class="form-control" name="rec_twice_d2" id="rec_twice_d2" value="15" placeholder="2nd Date" onchange="renderRecurrencePreview()">
                        </div>
                      </div>
                    </div>

                    <div id="rec_thrice_month_box" class="form-group" style="display:none;">
                      <label>Select 3 Dates in Month</label>
                      <div class="row">
                        <div class="col-xs-4">
                          <input type="number" min="1" max="31" class="form-control" name="rec_thrice_d1" id="rec_thrice_d1" value="1" placeholder="1st Date" onchange="renderRecurrencePreview()">
                        </div>
                        <div class="col-xs-4">
                          <input type="number" min="1" max="31" class="form-control" name="rec_thrice_d2" id="rec_thrice_d2" value="10" placeholder="2nd Date" onchange="renderRecurrencePreview()">
                        </div>
                        <div class="col-xs-4">
                          <input type="number" min="1" max="31" class="form-control" name="rec_thrice_d3" id="rec_thrice_d3" value="20" placeholder="3rd Date" onchange="renderRecurrencePreview()">
                        </div>
                      </div>
                    </div>

                    <div id="rec_year_box" class="form-group" style="display:none;">
                      <label>Starting Month & Date</label>
                      <div class="row">
                        <div class="col-xs-6">
                          <select class="form-control" name="rec_start_month" id="rec_start_month" onchange="renderRecurrencePreview()">
                            <?php 
                              $months = array(1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
                              foreach($months as $num=>$mname){
                            ?>
                              <option value="<?php echo $num; ?>" <?php echo $num==(int)date('n')?'selected':''; ?>><?php echo $mname; ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <div class="col-xs-6">
                          <input type="number" min="1" max="31" class="form-control" name="rec_year_day" id="rec_year_day" value="<?php echo (int)date('j'); ?>" placeholder="Day" onchange="renderRecurrencePreview()">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div id="upcoming_schedules_card" style="display:none; background:#ffffff; border:1px solid #e2e8f0; border-radius:6px; padding:14px 16px; margin-top:12px;">
                  <div style="font-weight:600; font-size:13px; color:#0f172a; margin-bottom:8px;">Upcoming Schedules</div>
                  <div id="upcoming_schedules_list" style="display:flex; flex-direction:column; gap:6px;"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12 col-sm-12">
              <div class="form-group">
                <label>Task Details / Instructions</label>
                <textarea name="remark" class="form-control" rows="3" placeholder="Enter task instructions, milestones, or deliverables..."></textarea>
              </div>
            </div>
          </div>
        </div>
        
        <div class="box-footer" style="background:#fff; border-top:1px solid #e2e8f0; padding:15px 20px;">
          <button type="submit" class="btn btn-primary" style="background:#2563eb; border-color:#2563eb; font-weight:600; padding:8px 20px;">Create Task</button>
          <a href="<?php echo ($default_scope === 'external') ? base_url().'manager/tasks/external' : base_url().'manager/tasks/internal'; ?>" class="btn btn-default" style="padding:8px 20px;">Cancel</a>
        </div>
      </div>
    </form>
  </section>
</div>
</div>

<script>
var systemUsers = <?php 
  $clean_users = array();
  if(!empty($u_list)){
    foreach($u_list as $u){
      $clean_users[] = array(
        'id' => (int)$u['id'],
        'name' => htmlspecialchars($u['name']),
        'email' => htmlspecialchars($u['email'])
      );
    }
  }
  echo json_encode($clean_users);
?>;

var currentUserId = <?php echo (int)$this->session->userdata('manager_id'); ?>;
var currentUserName = "<?php echo addslashes($this->session->userdata('manager_name')); ?>";

function toggleTaskScope() {
  var extRadio = document.getElementById("scope_external");
  var isExternal = extRadio ? extRadio.checked : false;
  var extSection = document.getElementById("external_section");
  var empBox = document.getElementById("internal_employee_box");
  var titleElem = document.getElementById("page_heading_title");
  var boxTitle = document.getElementById("box_section_title");

  if (isExternal) {
    if (extSection) extSection.style.display = "block";
    if (empBox) empBox.style.display = "none";
    if (titleElem) titleElem.textContent = "Add External Task";
    if (boxTitle) boxTitle.textContent = "External Task Configuration";
    onBusinessCategoryChanged();
  } else {
    if (extSection) extSection.style.display = "none";
    if (empBox) empBox.style.display = "block";
    if (titleElem) titleElem.textContent = "Add Internal Task";
    if (boxTitle) boxTitle.textContent = "Internal Task Configuration";
    document.getElementById("telecom_section").style.display = "none";
  }
}

function onClientSelected() {
  var select = document.getElementById("client_select");
  var selected = select.options[select.selectedIndex];
  if (selected && selected.value !== "") {
    var poc = selected.getAttribute("data-poc") || "";
    var contact = selected.getAttribute("data-contact") || "";
    var email = selected.getAttribute("data-email") || "";
    var name = selected.getAttribute("data-name") || "";

    document.getElementById("client_poc").value = poc;
    document.getElementById("client_phone").value = contact;
    document.getElementById("client_email").value = email;

    var titleInput = document.getElementById("task_title_input");
    if (titleInput.value.trim() === "") {
      titleInput.value = name + " - Deliverable";
    }
  }
}

function onBusinessCategoryChanged() {
  var extRadio = document.getElementById("scope_external");
  var isExternal = extRadio ? extRadio.checked : false;
  var bCat = document.getElementById("business_category_select").value;
  var telSection = document.getElementById("telecom_section");
  if (isExternal && bCat === "Telecom") {
    telSection.style.display = "block";
  } else {
    telSection.style.display = "none";
  }
}

function onDepartmentChanged() {
  var dept = document.getElementById("task_department").value;
  document.getElementById("hidden_category").value = dept;
  var accountsSection = document.getElementById("accounts_section");
  if (dept === "Accounts") {
    accountsSection.style.display = "block";
    onInvoicedChanged();
  } else {
    accountsSection.style.display = "none";
  }
}

function onInvoicedChanged() {
  var invoiced = document.getElementById("accounts_invoiced_select").value;
  var detailsRow = document.getElementById("invoice_details_row");
  if (invoiced === "Yes") {
    detailsRow.style.display = "block";
  } else {
    detailsRow.style.display = "none";
  }
}

function getRequiredWeekdayCount() {
  var freq = document.getElementById("rec_freq_select").value;
  if (freq === "Weekly") return 1;
  if (freq === "Twice a Week") return 2;
  if (freq === "Three Times a Week") return 3;
  return 0;
}

function onWeekdayChanged() {
  var reqCount = getRequiredWeekdayCount();
  if (reqCount === 0) return;

  var allCbs = document.querySelectorAll("input[name='rec_days[]']");
  var checkedCbs = document.querySelectorAll("input[name='rec_days[]']:checked");
  var count = checkedCbs.length;

  allCbs.forEach(function(cb) {
    if (!cb.checked) {
      cb.disabled = (count >= reqCount);
    } else {
      cb.disabled = false;
    }
  });

  var help = document.getElementById("weekday_help_text");
  if (count < reqCount) {
    help.innerHTML = '<span style="color:#d97706;">' + (reqCount - count) + ' more day(s) required.</span>';
  } else {
    help.innerHTML = '<span style="color:#16a34a;"><i class="fa fa-check"></i> ' + reqCount + ' day(s) selected.</span>';
  }

  renderRecurrencePreview();
}

function onRecurrenceFreqChange() {
  var freq = document.getElementById("rec_freq_select").value;
  var isRecInput = document.getElementById("is_recurring_input");
  var daysBox = document.getElementById("rec_days_box");
  var monthBox = document.getElementById("rec_month_box");
  var twiceBox = document.getElementById("rec_twice_month_box");
  var thriceBox = document.getElementById("rec_thrice_month_box");
  var yearBox = document.getElementById("rec_year_box");
  var schedCard = document.getElementById("upcoming_schedules_card");

  daysBox.style.display = "none";
  monthBox.style.display = "none";
  twiceBox.style.display = "none";
  thriceBox.style.display = "none";
  yearBox.style.display = "none";

  if (freq === "One-Time") {
    isRecInput.value = "0";
    schedCard.style.display = "none";
    return;
  }

  isRecInput.value = "1";
  schedCard.style.display = "block";

  if (freq === "Weekly" || freq === "Twice a Week" || freq === "Three Times a Week") {
    daysBox.style.display = "block";
    var reqCount = getRequiredWeekdayCount();
    var labelText = "Select Weekdays";
    if (reqCount === 1) labelText = "Select Exactly 1 Weekday";
    else if (reqCount === 2) labelText = "Select Exactly 2 Weekdays";
    else if (reqCount === 3) labelText = "Select Exactly 3 Weekdays";
    document.getElementById("rec_days_label").textContent = labelText;

    var allCbs = document.querySelectorAll("input[name='rec_days[]']");
    var checkedCbs = document.querySelectorAll("input[name='rec_days[]']:checked");
    if (checkedCbs.length === 0) {
      allCbs[0].checked = true;
      if (reqCount >= 2 && allCbs.length > 2) allCbs[2].checked = true;
      if (reqCount >= 3 && allCbs.length > 4) allCbs[4].checked = true;
    } else if (checkedCbs.length > reqCount) {
      for (var i = reqCount; i < checkedCbs.length; i++) {
        checkedCbs[i].checked = false;
      }
    }
    onWeekdayChanged();
  } else if (freq === "Monthly") {
    monthBox.style.display = "block";
  } else if (freq === "Twice a Month") {
    twiceBox.style.display = "block";
  } else if (freq === "Three Times a Month") {
    thriceBox.style.display = "block";
  } else if (freq === "Quarterly" || freq === "Half-Yearly" || freq === "Yearly") {
    yearBox.style.display = "block";
  }

  renderRecurrencePreview();
}

function renderRecurrencePreview() {
  var freq = document.getElementById("rec_freq_select").value;
  var schedCard = document.getElementById("upcoming_schedules_card");
  var listBox = document.getElementById("upcoming_schedules_list");

  if (freq === "One-Time") {
    schedCard.style.display = "none";
    return;
  }
  schedCard.style.display = "block";

  var dateVal = document.getElementById("followup_date_input").value;
  var timeVal = document.getElementById("followup_time_input").value || "10:00 AM";

  var startDate = new Date();
  if (dateVal && dateVal.indexOf("-") !== -1) {
    var parts = dateVal.split("-");
    if (parts.length === 3) {
      startDate = new Date(parseInt(parts[2]), parseInt(parts[1]) - 1, parseInt(parts[0]));
    }
  }

  var occurrences = [];
  var dayNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
  var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

  function formatDisplayDate(d) {
    return dayNames[d.getDay()] + ", " + ("0" + d.getDate()).slice(-2) + " " + monthNames[d.getMonth()] + " " + d.getFullYear();
  }

  function getDaysInMonth(year, month) {
    return new Date(year, month + 1, 0).getDate();
  }

  if (freq === "Weekly" || freq === "Twice a Week" || freq === "Three Times a Week") {
    var checkedBoxes = document.querySelectorAll("input[name='rec_days[]']:checked");
    var targetDays = [];
    var dayMap = { "Sun": 0, "Mon": 1, "Tue": 2, "Wed": 3, "Thu": 4, "Fri": 5, "Sat": 6 };
    checkedBoxes.forEach(function(cb) {
      if (dayMap.hasOwnProperty(cb.value)) targetDays.push(dayMap[cb.value]);
    });
    if (targetDays.length === 0) targetDays.push(1);

    var cur = new Date(startDate.getTime());
    for (var i = 0; occurrences.length < 5 && i < 90; i++) {
      cur.setDate(cur.getDate() + 1);
      if (targetDays.indexOf(cur.getDay()) !== -1) {
        occurrences.push(new Date(cur.getTime()));
      }
    }
  } else if (freq === "Monthly") {
    var daySelect = parseInt(document.getElementById("rec_month_day_select").value) || 1;
    var isLast = document.getElementById("rec_is_last_chk").checked;
    var curYear = startDate.getFullYear();
    var curMonth = startDate.getMonth();

    for (var m = 0; occurrences.length < 5 && m < 24; m++) {
      var checkMonth = curMonth + m;
      var y = curYear + Math.floor(checkMonth / 12);
      var mNorm = checkMonth % 12;
      var maxD = getDaysInMonth(y, mNorm);
      var actD = isLast ? maxD : Math.min(daySelect, maxD);
      var cand = new Date(y, mNorm, actD);
      if (cand >= startDate) {
        occurrences.push(cand);
      }
    }
  } else if (freq === "Twice a Month" || freq === "Three Times a Month") {
    var daysArr = [];
    if (freq === "Twice a Month") {
      daysArr.push(parseInt(document.getElementById("rec_twice_d1").value) || 1);
      daysArr.push(parseInt(document.getElementById("rec_twice_d2").value) || 15);
    } else {
      daysArr.push(parseInt(document.getElementById("rec_thrice_d1").value) || 1);
      daysArr.push(parseInt(document.getElementById("rec_thrice_d2").value) || 10);
      daysArr.push(parseInt(document.getElementById("rec_thrice_d3").value) || 20);
    }
    daysArr.sort(function(a, b) { return a - b; });
    var curYear = startDate.getFullYear();
    var curMonth = startDate.getMonth();

    for (var m = 0; occurrences.length < 5 && m < 24; m++) {
      var checkMonth = curMonth + m;
      var y = curYear + Math.floor(checkMonth / 12);
      var mNorm = checkMonth % 12;
      var maxD = getDaysInMonth(y, mNorm);
      for (var k = 0; k < daysArr.length; k++) {
        var actD = Math.min(daysArr[k], maxD);
        var cand = new Date(y, mNorm, actD);
        if (cand >= startDate) {
          occurrences.push(cand);
          if (occurrences.length >= 5) break;
        }
      }
    }
  } else if (freq === "Quarterly" || freq === "Half-Yearly" || freq === "Yearly") {
    var stepMonths = (freq === "Quarterly") ? 3 : ((freq === "Half-Yearly") ? 6 : 12);
    var startMonth = parseInt(document.getElementById("rec_start_month").value) - 1;
    var dayVal = parseInt(document.getElementById("rec_year_day").value) || 1;
    var curYear = startDate.getFullYear();

    for (var step = 0; occurrences.length < 5 && step < 24; step++) {
      var totalMonths = startMonth + (step * stepMonths);
      var y = curYear + Math.floor(totalMonths / 12);
      var mNorm = totalMonths % 12;
      var maxD = getDaysInMonth(y, mNorm);
      var actD = Math.min(dayVal, maxD);
      var cand = new Date(y, mNorm, actD);
      if (cand >= startDate) {
        occurrences.push(cand);
      }
    }
  }

  listBox.innerHTML = "";
  if (occurrences.length === 0) {
    listBox.innerHTML = '<div style="color:#64748b; font-size:13px;">No upcoming schedules calculated.</div>';
  } else {
    occurrences.forEach(function(dt) {
      var item = document.createElement("div");
      item.style.fontSize = "13px";
      item.style.color = "#1e293b";
      item.innerHTML = '<i class="fa fa-calendar-check-o" style="color:#2563eb; margin-right:8px;"></i> ' + formatDisplayDate(dt) + ' at ' + timeVal;
      listBox.appendChild(item);
    });
  }
}

document.addEventListener("DOMContentLoaded", function() {
  var searchInput = document.getElementById("user_search_input");
  var hiddenInput = document.getElementById("assigned_to_input");
  var dropdown = document.getElementById("user_search_dropdown");

  if (currentUserId > 0 && currentUserName !== "") {
    searchInput.value = currentUserName + " (Self)";
  }

  searchInput.addEventListener("input", function() {
    var query = this.value.trim().toLowerCase();
    dropdown.innerHTML = "";
    
    if (query.length < 3) {
      dropdown.style.display = "none";
      return;
    }

    var matches = systemUsers.filter(function(u) {
      return u.name.toLowerCase().indexOf(query) !== -1 || u.email.toLowerCase().indexOf(query) !== -1;
    });

    if (matches.length === 0) {
      dropdown.innerHTML = '<div style="padding:10px 14px; color:#64748b; font-size:13px;">No user found</div>';
      dropdown.style.display = "block";
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
        searchInput.value = u.name;
        hiddenInput.value = u.id;
        dropdown.style.display = "none";
      });

      dropdown.appendChild(item);
    });

    dropdown.style.display = "block";
  });

  document.addEventListener("click", function(e) {
    if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
      dropdown.style.display = "none";
    }
  });

  document.getElementById("task_add_form").addEventListener("submit", function(e) {
    var freq = document.getElementById("rec_freq_select").value;
    if (freq === "Weekly" || freq === "Twice a Week" || freq === "Three Times a Week") {
      var reqCount = getRequiredWeekdayCount();
      var checkedCbs = document.querySelectorAll("input[name='rec_days[]']:checked");
      if (checkedCbs.length !== reqCount) {
        e.preventDefault();
        alert("Please select exactly " + reqCount + " weekday(s) for " + freq + ".");
        return false;
      }
    }
    if (freq === "Twice a Month") {
      var d1 = parseInt(document.getElementById("rec_twice_d1").value) || 0;
      var d2 = parseInt(document.getElementById("rec_twice_d2").value) || 0;
      if (d1 < 1 || d1 > 31 || d2 < 1 || d2 > 31 || d1 === d2) {
        e.preventDefault();
        alert("Please select 2 different valid dates (1-31) for Twice a Month.");
        return false;
      }
    }
    if (freq === "Three Times a Month") {
      var t1 = parseInt(document.getElementById("rec_thrice_d1").value) || 0;
      var t2 = parseInt(document.getElementById("rec_thrice_d2").value) || 0;
      var t3 = parseInt(document.getElementById("rec_thrice_d3").value) || 0;
      if (t1 < 1 || t1 > 31 || t2 < 1 || t2 > 31 || t3 < 1 || t3 > 31 || t1 === t2 || t2 === t3 || t1 === t3) {
        e.preventDefault();
        alert("Please select 3 different valid dates (1-31) for Three Times a Month.");
        return false;
      }
    }
  });

  toggleTaskScope();
  onDepartmentChanged();
  onRecurrenceFreqChange();
});

function clearUserSelection() {
  document.getElementById("user_search_input").value = "";
  document.getElementById("assigned_to_input").value = "0";
  document.getElementById("user_search_dropdown").style.display = "none";
  document.getElementById("user_search_input").focus();
}
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
