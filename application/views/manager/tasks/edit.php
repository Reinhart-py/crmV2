<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="container">
<div class="content-wrapper"> 
  <section class="content-header">
    <h1><?php echo htmlspecialchars($info['page_heading']); ?></h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i>Home</a></li>
      <li><a href="<?php echo base_url(); ?>manager/tasks">Tasks</a></li>
      <li class="active">Edit Task</li>
    </ol>
  </section>
  
  <section class="content"> 
    <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
    <?php echo get_message($this->session->flashdata('message'),'message'); ?>
     
    <form role="form" action="<?php echo base_url(); ?>manager/tasks/edit/<?php echo $task['id']; ?>" method="post" name="edit_form">
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
                <input type="text" name="customer" class="form-control" value="<?php echo htmlspecialchars($task['customer']); ?>" required autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>POC Contact Person</label>
                <input type="text" class="form-control" name="poc" value="<?php echo htmlspecialchars($task['poc']); ?>" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Contact Phone</label>
                <input type="text" class="form-control" name="contact1" value="<?php echo htmlspecialchars($task['contact1']); ?>" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($task['email']); ?>" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Department / Category<span>*</span></label>
                <select class="form-control" name="category" required>
                  <option value="Back Office Tasks" <?php echo ($task['category']=='Back Office Tasks' || $task['category']=='New') ? 'selected' : ''; ?>>Back Office Tasks</option>
                  <option value="Operations Tasks" <?php echo ($task['category']=='Operations Tasks') ? 'selected' : ''; ?>>Operations Tasks</option>
                  <option value="Sales Tasks" <?php echo ($task['category']=='Sales Tasks') ? 'selected' : ''; ?>>Sales Tasks</option>
                  <option value="KAM / Client Tasks" <?php echo ($task['category']=='KAM / Client Tasks') ? 'selected' : ''; ?>>KAM / Client Tasks</option>
                  <option value="IT & Tech Tasks" <?php echo ($task['category']=='IT & Tech Tasks') ? 'selected' : ''; ?>>IT & Tech Tasks</option>
                  <option value="Accounts & Finance Tasks" <?php echo ($task['category']=='Accounts & Finance Tasks') ? 'selected' : ''; ?>>Accounts & Finance Tasks</option>
                  <option value="HR & Admin Tasks" <?php echo ($task['category']=='HR & Admin Tasks') ? 'selected' : ''; ?>>HR & Admin Tasks</option>
                  <option value="Support Tasks" <?php echo ($task['category']=='Support Tasks') ? 'selected' : ''; ?>>Support Tasks</option>
                  <option value="General Tasks" <?php echo ($task['category']=='General Tasks' || $task['category']=='Existing') ? 'selected' : ''; ?>>General Tasks</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Task Type</label>
                <select class="form-control" name="business_category">
                  <option value="Task" <?php echo (empty($task['business_category']) || $task['business_category']=='Task') ? 'selected' : ''; ?>>Task</option>
                  <option value="Follow-up" <?php echo ($task['business_category']=='Follow-up') ? 'selected' : ''; ?>>Follow-up</option>
                  <option value="Client Verification" <?php echo ($task['business_category']=='Client Verification') ? 'selected' : ''; ?>>Client Verification</option>
                  <option value="Documentation" <?php echo ($task['business_category']=='Documentation') ? 'selected' : ''; ?>>Documentation</option>
                  <option value="Meeting / Review" <?php echo ($task['business_category']=='Meeting / Review') ? 'selected' : ''; ?>>Meeting / Review</option>
                  <option value="Audit" <?php echo ($task['business_category']=='Audit') ? 'selected' : ''; ?>>Audit</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Status</label>
                <select class="form-control" name="status">
                  <option value="Open" <?php echo ($task['status']=='Open') ? 'selected' : ''; ?>>Open</option>
                  <option value="In Progress" <?php echo ($task['status']=='In Progress') ? 'selected' : ''; ?>>In Progress</option>
                  <option value="Completed" <?php echo ($task['status']=='Completed') ? 'selected' : ''; ?>>Completed</option>
                  <option value="Closed" <?php echo ($task['status']=='Closed') ? 'selected' : ''; ?>>Closed</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Priority</label>
                <select class="form-control" name="dnd_status">
                  <option value="Normal" <?php echo ($task['dnd_status']=='Normal' || empty($task['dnd_status']) || $task['dnd_status']=='Free') ? 'selected' : ''; ?>>Normal</option>
                  <option value="High" <?php echo ($task['dnd_status']=='High' || $task['dnd_status']=='Busy') ? 'selected' : ''; ?>>High</option>
                  <option value="Urgent" <?php echo ($task['dnd_status']=='Urgent') ? 'selected' : ''; ?>>Urgent</option>
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
                <input type="hidden" name="assigned_to" id="assigned_to_input" value="<?php echo (int)$task['assigned_to']; ?>">
                <div id="user_search_dropdown" style="display:none; position:absolute; top:100%; left:0; right:0; z-index:9999; background:#fff; border:1px solid #cbd5e1; border-radius:4px; box-shadow:0 8px 16px rgba(0,0,0,0.12); max-height:220px; overflow-y:auto; margin-top:2px;"></div>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Follow-up Date</label>
                <input type="text" class="form-control datepicker" name="followup_date" value="<?php echo (!empty($task['followup_date']) && $task['followup_date']!='0000-00-00') ? date('d-m-Y', strtotime($task['followup_date'])) : ''; ?>" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Follow-up Time</label>
                <div class="input-group bootstrap-timepicker timepicker">
                  <input type="text" class="form-control input-small timepicker" name="followup_time" value="<?php echo htmlspecialchars($task['followup_time']); ?>" autocomplete="off">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Due / Close Date</label>
                <input type="text" class="form-control datepicker" name="close_date" value="<?php echo (!empty($task['close_date']) && $task['close_date']!='0000-00-00') ? date('d-m-Y', strtotime($task['close_date'])) : ''; ?>" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-12 col-sm-12">
              <div class="form-group">
                <label>Task Details / Instructions</label>
                <textarea name="remark" class="form-control" rows="3" placeholder="Enter full task details and instructions..."><?php echo htmlspecialchars($task['remark']); ?></textarea>
              </div>
            </div>
          </div>
        </div>
        
        <div class="box-footer">
          <button type="submit" class="btn btn-primary" style="background:#2563eb; border-color:#2563eb;">Update Task</button>
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

var initialAssignedId = <?php echo (int)$task['assigned_to']; ?>;

document.addEventListener("DOMContentLoaded", function() {
  var searchInput = document.getElementById("user_search_input");
  var hiddenInput = document.getElementById("assigned_to_input");
  var dropdown = document.getElementById("user_search_dropdown");

  if (initialAssignedId > 0) {
    var assignedUser = systemUsers.find(function(u) { return u.id === initialAssignedId; });
    if (assignedUser) {
      searchInput.value = assignedUser.name;
    }
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
