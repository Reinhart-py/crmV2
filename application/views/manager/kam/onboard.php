<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="container">
<div class="content-wrapper"> 
  
  <section class="content-header">
    <h1> Onboard Client </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?php echo base_url(); ?>manager/kam/my-client">KAM</a></li>
      <li class="active">Onboard Client</li>
    </ol>
  </section>
  
  <section class="content"> 
    
    <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
    <?php echo get_message($this->session->flashdata('message'),'message'); ?>
     
    <form role="form" action="<?php echo base_url(); ?>manager/kam/onboard" method="post" name="onboard_client_form">
      
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Client and Group Details</h3>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Client Name <span>*</span></label>
                <select name="customer" id="kam_company_select" class="form-control select2-company-ajax" style="width: 100%;" required>
                  <option value=""></option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Group Name</label>
                <input type="text" class="form-control" name="group_name">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Status Category</label>
                <select class="form-control select2" style="width: 100%;" name="status_category">
                  <option value="">Select Category</option>
                  <option value="Active" selected="selected">Active</option>
                  <option value="In Progress">In Progress</option>
                  <option value="Pending Review">Pending Review</option>
                  <option value="Follow up">Follow up</option>
                  <option value="Inactive">Inactive</option>
                  <option value="Closed">Closed</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Client Status</label>
                <select class="form-control select2" style="width: 100%;" name="client_status">
                  <option value="Active" selected="selected">Active</option>
                  <option value="Inactive">Inactive</option>
                  <option value="Closed">Closed</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>EOS Date</label>
                <input type="text" class="form-control datepicker" name="eos_date" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>On-Boarding Date</label>
                <input type="text" class="form-control datepicker" name="onboarding_date" value="<?php echo date('d-m-Y'); ?>" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>EL Expiry</label>
                <input type="text" class="form-control datepicker" name="el_expiry" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>EL Due</label>
                <input type="text" class="form-control" name="el_due">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Inactive Reason</label>
                <input type="text" class="form-control" name="inactive_reason">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Amount</label>
                <input type="text" class="form-control only_number" name="amount">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>20-Oct-26</label>
                <input type="text" class="form-control datepicker" name="f_20_oct_26" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>18-Jan-27</label>
                <input type="text" class="form-control datepicker" name="f_18_jan_27" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Group (Y/N)</label>
                <select class="form-control select2" style="width: 100%;" name="group_yn">
                  <option value="N" selected="selected">No (N)</option>
                  <option value="Y">Yes (Y)</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group" style="position: relative;">
                <label>Primary Assignee / Executive <span>*</span></label>
                <div class="input-group">
                  <input type="text" id="onboard_assign_input" class="form-control" placeholder="Type 3+ letters to search..." autocomplete="off">
                  <span class="input-group-btn">
                    <button class="btn btn-default" type="button" onclick="clearOnboardAssign()"><i class="fa fa-times"></i></button>
                  </span>
                </div>
                <input type="hidden" name="lead_assign" id="onboard_lead_assign_val" value="<?php echo (int)$this->session->userdata('manager_id'); ?>" required>
                <div id="onboard_assign_dropdown" style="display:none; position:absolute; top:100%; left:0; right:0; z-index:9999; background:#fff; border:1px solid #cbd5e1; border-radius:4px; box-shadow:0 8px 16px rgba(0,0,0,0.12); max-height:220px; overflow-y:auto; margin-top:2px;"></div>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Collaborators / Additional Assignees</label>
                <select class="form-control select2" name="agents[]" multiple="multiple" data-placeholder="Select Additional Assignees" style="width: 100%;">
                  <?php if(isset($u_list) && !empty($u_list)) {
                    foreach($u_list as $u){ ?>
                      <option value="<?php echo $u['id']; ?>">
                        <?php echo htmlspecialchars($u['name']); ?>
                      </option>
                  <?php } } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Executive Designation</label>
                <input type="text" class="form-control" name="executive" placeholder="Designation / Notes">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>MA Dec-25</label>
                <input type="text" class="form-control" name="ma_dec_25">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>MA Jan-26</label>
                <input type="text" class="form-control" name="ma_jan_26">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>MA Feb-26</label>
                <input type="text" class="form-control" name="ma_feb_26">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>MA Mar-26</label>
                <input type="text" class="form-control" name="ma_mar_26">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Trade License and Compliance</h3>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Trade License No</label>
                <input type="text" class="form-control" name="tl_number">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Trade License Expiry</label>
                <input type="text" class="form-control datepicker" name="tl_expire_date" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>TL Due</label>
                <input type="text" class="form-control" name="tl_due">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>TL Authority</label>
                <input type="text" class="form-control" name="tl_authority">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Last MOL</label>
                <input type="text" class="form-control datepicker" name="last_mol" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>No of MOL Employee</label>
                <input type="text" class="form-control only_number" name="no_of_mol_employee">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Year Ending</label>
                <input type="text" class="form-control" name="year_ending">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>CT Reg</label>
                <input type="text" class="form-control" name="ct_reg">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>TRC Reg</label>
                <input type="text" class="form-control" name="trc_reg">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>VAT Qtr</label>
                <input type="text" class="form-control" name="vat_qtr">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>FTA Remark</label>
                <input type="text" class="form-control" name="fta_remark">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Due Date</label>
                <input type="text" class="form-control datepicker" name="due_date" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Month</label>
                <input type="text" class="form-control" name="month">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Services and FTA Details</h3>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>WA</label>
                <input type="text" class="form-control" name="wa">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>DB</label>
                <input type="text" class="form-control" name="db_field">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>DB Storage</label>
                <input type="text" class="form-control" name="db_storage">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>SFT</label>
                <input type="text" class="form-control" name="sft">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>AML</label>
                <input type="text" class="form-control" name="aml">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>ICV</label>
                <input type="text" class="form-control" name="icv">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>FTA Update</label>
                <input type="text" class="form-control" name="fta_update">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>FTA Username</label>
                <input type="text" class="form-control" name="fta_username">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>FTA Password</label>
                <input type="password" class="form-control" name="fta_password">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Audit 2025</label>
                <input type="text" class="form-control" name="audit_2025">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>SBR</label>
                <input type="text" class="form-control" name="sbr">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Revenue</label>
                <input type="text" class="form-control only_number" name="revenue">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Expected Date</label>
                <input type="text" class="form-control datepicker" name="expected_date" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Remarks</label>
                <input type="text" class="form-control" name="remarks">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Software Details</h3>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Software Name</label>
                <input type="text" class="form-control" name="software_name">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Software Access</label>
                <input type="text" class="form-control" name="software_access">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Soft ID</label>
                <input type="text" class="form-control" name="soft_id">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Soft Pwd</label>
                <input type="password" class="form-control" name="soft_pwd">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Tasks and Reviews</h3>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Pending Task</label>
                <input type="text" class="form-control" name="pending_task">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Pending Task - Meeting Review</label>
                <input type="text" class="form-control" name="pending_task_meeting">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Fee Discussion</label>
                <input type="text" class="form-control" name="fee_discussion">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Last MA Update</label>
                <input type="text" class="form-control datepicker" name="last_ma_update" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Last Review Date</label>
                <input type="text" class="form-control datepicker" name="last_review_date" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>CFO</label>
                <input type="text" class="form-control" name="cfo">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Audit / Review</label>
                <input type="text" class="form-control" name="audit_review">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Accounting</label>
                <input type="text" class="form-control" name="accounting">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Bookkeeping</label>
                <input type="text" class="form-control" name="bookkeeping">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>YE</label>
                <input type="text" class="form-control" name="ye">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Vat</label>
                <input type="text" class="form-control" name="vat">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>MA Frequency</label>
                <input type="text" class="form-control" name="ma_frequency">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>MIS</label>
                <input type="text" class="form-control" name="mis">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Queries/Review</label>
                <input type="text" class="form-control" name="queries_review">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Visits</label>
                <input type="text" class="form-control" name="visits">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Stock</label>
                <input type="text" class="form-control" name="stock">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Operations</label>
                <input type="text" class="form-control" name="operations">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>AML</label>
                <input type="text" class="form-control" name="aml_task">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>CFO/Consulting</label>
                <input type="text" class="form-control" name="cfo_consulting">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Internal Review</label>
                <input type="text" class="form-control" name="internal_review">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Contact and Visit Details</h3>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Location</label>
                <input type="text" class="form-control" name="location">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Number</label>
                <input type="text" class="form-control" name="number">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" name="email">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>V-Man</label>
                <input type="text" class="form-control" name="v_man">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>V-Exe</label>
                <input type="text" class="form-control" name="v_exe">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Frequency</label>
                <input type="text" class="form-control" name="frequency">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Monthly Visits</label>
                <input type="text" class="form-control only_number" name="monthly_visits">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Day</label>
                <input type="text" class="form-control" name="day">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="box-footer text-center" style="padding: 20px 15px; background: transparent;">
        <button type="submit" class="btn btn-danger btn-lg" style="min-width: 180px;"><i class="fa fa-user-plus"></i> Onboard Client</button>
        <a href="<?php echo base_url(); ?>manager/kam/my-client" class="btn btn-default btn-lg" style="margin-left: 10px;">Cancel</a>
      </div>

    </form>
    
  </section>
  
</div>
</div>

<script>
$(document).ready(function() {
  if ($.fn.select2) {
    $('.select2').select2();

    $('.select2-company-ajax').select2({
      placeholder: 'Type company name (min 3 characters)...',
      minimumInputLength: 3,
      allowClear: true,
      tags: true,
      createTag: function (params) {
        var term = $.trim(params.term);
        if (term === '' || term.length < 3) {
          return null;
        }
        return {
          id: term,
          text: term,
          newTag: true
        };
      },
      ajax: {
        url: '<?php echo base_url(); ?>manager/leads/ajax_search_companies',
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            term: params.term
          };
        },
        processResults: function (data) {
          return {
            results: (data || []).map(function(item) {
              return { id: item, text: item };
            })
          };
        },
        cache: true
      }
    });
  }

  if ($.fn.datepicker) {
    $('.datepicker').datepicker({
      format: 'dd-mm-yyyy',
      autoclose: true,
      todayHighlight: true
    });
  }

  $('.only_number').on('keyup keydown blur', function(e) {
    $(this).val($(this).val().replace(/[^0-9.]/g, ''));
  });
});

var onboardUsers = <?php 
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

var curOnboardUserId = <?php echo (int)$this->session->userdata('manager_id'); ?>;
var curOnboardUserName = "<?php echo addslashes($this->session->userdata('manager_name')); ?>";

document.addEventListener("DOMContentLoaded", function() {
  var assignInput = document.getElementById("onboard_assign_input");
  var assignHidden = document.getElementById("onboard_lead_assign_val");
  var assignDropdown = document.getElementById("onboard_assign_dropdown");

  if (curOnboardUserId > 0 && curOnboardUserName !== "") {
    assignInput.value = curOnboardUserName;
  }

  assignInput.addEventListener("input", function() {
    var query = this.value.trim().toLowerCase();
    assignDropdown.innerHTML = "";
    
    if (query.length < 3) {
      assignDropdown.style.display = "none";
      return;
    }

    var matches = onboardUsers.filter(function(u) {
      return u.name.toLowerCase().indexOf(query) !== -1 || u.email.toLowerCase().indexOf(query) !== -1;
    });

    if (matches.length === 0) {
      assignDropdown.innerHTML = '<div style="padding:10px 14px; color:#64748b; font-size:13px;">No assignee found</div>';
      assignDropdown.style.display = "block";
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
        assignInput.value = u.name;
        assignHidden.value = u.id;
        assignDropdown.style.display = "none";
      });

      assignDropdown.appendChild(item);
    });

    assignDropdown.style.display = "block";
  });

  document.addEventListener("click", function(e) {
    if (!assignInput.contains(e.target) && !assignDropdown.contains(e.target)) {
      assignDropdown.style.display = "none";
    }
  });
});

function clearOnboardAssign() {
  document.getElementById("onboard_assign_input").value = "";
  document.getElementById("onboard_lead_assign_val").value = "";
  document.getElementById("onboard_assign_dropdown").style.display = "none";
  document.getElementById("onboard_assign_input").focus();
}
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
