<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="container">
<div class="content-wrapper"> 
  <section class="content-header">
    <h1><?php echo htmlspecialchars($info['page_heading']); ?></h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i>Home</a></li>
      <li><a href="<?php echo base_url(); ?>manager/tasks">Tasks</a></li>
      <li class="active">Add Task</li>
    </ol>
  </section>
  
  <section class="content"> 
    <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
    <?php echo get_message($this->session->flashdata('message'),'message'); ?>
     
    <form role="form" action="<?php echo base_url(); ?>manager/tasks/add" method="post" name="add_form">
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Task Details</h3>
          <div class="pull-right">
            <a href="<?php echo base_url(); ?>manager/tasks" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
          </div>
        </div>
      
        <div class="box-body">
          <div class="row">
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Task Title / Customer<span>*</span></label>
                <input type="text" name="customer" class="form-control" placeholder="Enter task title or client name" required autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>POC Contact Person</label>
                <input type="text" class="form-control" name="poc" placeholder="Person to contact" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Contact Phone</label>
                <input type="text" class="form-control" name="contact1" placeholder="Phone number" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" name="email" placeholder="Email address" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Department / Category<span>*</span></label>
                <select class="form-control" name="category" required>
                  <option value="Back Office Tasks" selected>Back Office Tasks</option>
                  <option value="Operations Tasks">Operations Tasks</option>
                  <option value="Sales Tasks">Sales Tasks</option>
                  <option value="KAM / Client Tasks">KAM / Client Tasks</option>
                  <option value="IT & Tech Tasks">IT & Tech Tasks</option>
                  <option value="Accounts & Finance Tasks">Accounts & Finance Tasks</option>
                  <option value="HR & Admin Tasks">HR & Admin Tasks</option>
                  <option value="Support Tasks">Support Tasks</option>
                  <option value="General Tasks">General Tasks</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Task Type</label>
                <select class="form-control" name="business_category">
                  <option value="Task" selected>Task</option>
                  <option value="Follow-up">Follow-up</option>
                  <option value="Client Verification">Client Verification</option>
                  <option value="Documentation">Documentation</option>
                  <option value="Meeting / Review">Meeting / Review</option>
                  <option value="Audit">Audit</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
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

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Priority</label>
                <select class="form-control" name="dnd_status">
                  <option value="Normal" selected>Normal</option>
                  <option value="High">High</option>
                  <option value="Urgent">Urgent</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group" style="position: relative;">
                <label>Assigned To</label>
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
                <label>Follow-up Date</label>
                <input type="text" class="form-control datepicker" name="followup_date" value="<?php echo date('d-m-Y'); ?>" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Follow-up Time</label>
                <div class="input-group bootstrap-timepicker timepicker">
                  <input type="text" class="form-control input-small timepicker" name="followup_time" value="<?php echo date('h:i A'); ?>" autocomplete="off">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Due / Close Date</label>
                <input type="text" class="form-control datepicker" name="close_date" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-12 col-sm-12">
              <div class="form-group">
                <label>Task Details / Instructions</label>
                <textarea name="remark" class="form-control" rows="3" placeholder="Enter full task details and instructions..."></textarea>
              </div>
            </div>
          </div>
        </div>
        
        <div class="box-footer">
          <button type="submit" class="btn btn-primary" style="background:#2563eb; border-color:#2563eb;">Create Task</button>
          <a href="<?php echo base_url(); ?>manager/tasks" class="btn btn-default">Cancel</a>
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
});

function clearUserSelection() {
  document.getElementById("user_search_input").value = "";
  document.getElementById("assigned_to_input").value = "0";
  document.getElementById("user_search_dropdown").style.display = "none";
  document.getElementById("user_search_input").focus();
}
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
