<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="container">
<div class="content-wrapper"> 
  
  <section class="content-header">
    <h1> Tickets </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i>Home</a></li>
      <li><a href="<?php echo base_url(); ?>manager/tickets">Tickets</a></li>
      <li class="active">Edit Ticket #<?php echo htmlspecialchars($ticket['ticket_number']); ?></li>
    </ol>
  </section>
  
  <section class="content"> 
    
    <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
    <?php echo get_message($this->session->flashdata('message'),'message'); ?>
     
    <form role="form" action="<?php echo base_url(); ?>manager/tickets/edit/<?php echo $ticket['id']; ?>" method="post" enctype="multipart/form-data" name="edit_form">
      
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Client & Company Information</h3>
        </div>
        
        <div class="box-body">
          <div class="row">
            
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Client Type<span>*</span></label>
                <select class="form-control select2" name="client_type" required style="width: 100%;">
                  <option value="Onboarded" <?php if(!empty($ticket['client_type']) && $ticket['client_type']=='Onboarded') echo 'selected'; ?>>Onboarded</option>
                  <option value="Non-Onboarded" <?php if(!empty($ticket['client_type']) && $ticket['client_type']=='Non-Onboarded') echo 'selected'; ?>>Non-Onboarded</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Company Name<span>*</span></label>
                <?php $current_comp = !empty($ticket['company_name']) ? $ticket['company_name'] : (!empty($ticket['customer']) ? $ticket['customer'] : ''); ?>
                <select class="form-control select2-company-ajax" name="company_name" required style="width: 100%;">
                  <?php if(!empty($current_comp)){ ?>
                    <option value="<?php echo htmlspecialchars($current_comp); ?>" selected><?php echo htmlspecialchars($current_comp); ?></option>
                  <?php } else { ?>
                    <option value="">Type at least 3 characters...</option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>POC Name</label>
                <input type="text" class="form-control" name="poc" value="<?php echo htmlspecialchars($ticket['poc'] ?? ''); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>POC Contact Number</label>
                <input type="text" class="form-control" name="contact1" value="<?php echo htmlspecialchars($ticket['contact1'] ?? ''); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Alternative Contact Number</label>
                <input type="text" class="form-control" name="alt_contact" value="<?php echo htmlspecialchars($ticket['alt_contact'] ?? ''); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($ticket['email'] ?? ''); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>City / Location</label>
                <input type="text" class="form-control" name="city" value="<?php echo htmlspecialchars($ticket['city'] ?? ''); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Ticket Source</label>
                <select class="form-control select2" name="lead_source" style="width: 100%;">
                  <?php 
                  $src = !empty($ticket['lead_source']) ? $ticket['lead_source'] : 'Internal Request';
                  $sources = array('Internal Request', 'Phone Call', 'Email', 'WhatsApp', 'Client Portal', 'Direct / Meeting');
                  foreach($sources as $s){ ?>
                    <option value="<?php echo $s; ?>" <?php if($src == $s) echo 'selected'; ?>><?php echo $s; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Ticket Classification</h3>
        </div>
        
        <div class="box-body">
          <div class="row">

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Department<span>*</span></label>
                <select class="form-control select2" name="department" required style="width: 100%;">
                  <?php 
                  $cur_dept = $ticket['department'] ?? 'Support';
                  $depts = array('Sales', 'Back Office', 'Operations', 'IT', 'HR', 'Support', 'Accounts');
                  foreach($depts as $dept){ ?>
                    <option value="<?php echo $dept; ?>" <?php if($cur_dept == $dept) echo 'selected'; ?>><?php echo $dept; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Ticket Type<span>*</span></label>
                <select class="form-control select2" name="ticket_type" required style="width: 100%;">
                  <?php 
                  $cur_type = $ticket['ticket_type'] ?? 'Technical Support';
                  $types = array('Bug / Technical Issue', 'Feature Request', 'System Access / Permission', 'Billing / Invoice Inquiry', 'Client Query / Onboarding', 'Operational Support', 'Hardware / Equipment', 'General Inquiry', 'Escalation');
                  foreach($types as $t){ ?>
                    <option value="<?php echo $t; ?>" <?php if($cur_type == $t) echo 'selected'; ?>><?php echo $t; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Category<span>*</span></label>
                <select class="form-control select2" name="category" required style="width: 100%;">
                  <?php 
                  $cur_cat = $ticket['category'] ?? 'General';
                  $cats = array('Software', 'Hardware', 'Network', 'Lead Management', 'Calling System', 'CRM Access', 'Data Correction', 'Attendance / HR', 'Security / Breach', 'Other');
                  foreach($cats as $c){ ?>
                    <option value="<?php echo $c; ?>" <?php if($cur_cat == $c) echo 'selected'; ?>><?php echo $c; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Priority<span>*</span></label>
                <select class="form-control select2" name="priority" required style="width: 100%;">
                  <?php 
                  $cur_pri = $ticket['priority'] ?? 'Medium';
                  $pris = array('Low', 'Medium', 'High', 'Urgent');
                  foreach($pris as $p){ ?>
                    <option value="<?php echo $p; ?>" <?php if($cur_pri == $p) echo 'selected'; ?>><?php echo $p; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Status<span>*</span></label>
                <select class="form-control select2" name="status" required style="width: 100%;">
                  <?php 
                  $cur_status = $ticket['status'] ?? 'Open';
                  $statuses = array('Open', 'Assigned', 'In Progress', 'Pending', 'On Hold', 'Resolved');
                  if(!empty($can_close) || strtolower($cur_status) == 'closed'){
                    $statuses[] = 'Closed';
                  }
                  foreach($statuses as $st){ ?>
                    <option value="<?php echo $st; ?>" <?php if($cur_status == $st) echo 'selected'; ?>><?php echo $st; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-9 col-sm-6">
              <div class="form-group">
                <label>Ticket Title<span>*</span></label>
                <input type="text" name="ticket_title" class="form-control" value="<?php echo htmlspecialchars($ticket['ticket_title'] ?? $ticket['subject'] ?? ''); ?>" required>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Assignment & Deadlines</h3>
        </div>
        
        <div class="box-body">
          <div class="row">
            
            <div class="col-lg-6 col-sm-12">
              <div class="form-group">
                <label>Assigned To (Multiple Users Supported)<span>*</span></label>
                <?php 
                $selected_assigned = array();
                if(!empty($ticket['assigned_users'])){
                  $selected_assigned = array_map('trim', explode(',', $ticket['assigned_users']));
                } elseif(!empty($ticket['assigned_to'])){
                  $selected_assigned = array((string)$ticket['assigned_to']);
                }
                ?>
                <select class="form-control select2" name="assigned_users[]" multiple="multiple" data-placeholder="Select assignees" style="width: 100%;" required>
                  <?php if(isset($users_list) && !empty($users_list)){
                    foreach($users_list as $u){ ?>
                      <option value="<?php echo $u['id']; ?>" <?php if(in_array((string)$u['id'], $selected_assigned)) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($u['name']); ?> (<?php echo htmlspecialchars($u['email']); ?>)
                      </option>
                  <?php } } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-6 col-sm-12">
              <div class="form-group">
                <label>Notify / Tag Users</label>
                <?php 
                $selected_tagged = array();
                if(!empty($ticket['tagged_users'])){
                  $selected_tagged = array_map('trim', explode(',', $ticket['tagged_users']));
                }
                ?>
                <select class="form-control select2" name="tagged_users[]" multiple="multiple" data-placeholder="Select users to tag" style="width: 100%;">
                  <?php if(isset($users_list) && !empty($users_list)){
                    foreach($users_list as $u){ ?>
                      <option value="<?php echo $u['id']; ?>" <?php if(in_array((string)$u['id'], $selected_tagged)) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($u['name']); ?>
                      </option>
                  <?php } } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Raised By</label>
                <?php 
                $raised_name = 'User';
                if(!empty($ticket['raised_by'])){
                  $u_row = $this->db->get_where('ci_user', array('id' => (int)$ticket['raised_by']))->row_array();
                  if(!empty($u_row['name'])) $raised_name = $u_row['name'];
                } elseif(!empty($ticket['created_by'])){
                  $u_row = $this->db->get_where('ci_user', array('id' => (int)$ticket['created_by']))->row_array();
                  if(!empty($u_row['name'])) $raised_name = $u_row['name'];
                }
                ?>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($raised_name); ?>" readonly>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Created Date</label>
                <?php 
                $cdate = !empty($ticket['created_date']) ? date('d-m-Y', strtotime($ticket['created_date'])) : (!empty($ticket['created_at']) ? date('d-m-Y', strtotime($ticket['created_at'])) : '');
                ?>
                <input type="text" class="form-control" value="<?php echo $cdate; ?>" readonly>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Last Updated By</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($this->session->userdata('manager_name')); ?>" readonly>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Last Updated Date</label>
                <input type="text" class="form-control" value="<?php echo date('d-m-Y'); ?>" readonly>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Deadline Date</label>
                <input type="text" class="form-control datepicker" name="deadline_date" value="<?php echo !empty($ticket['deadline_date']) ? htmlspecialchars($ticket['deadline_date']) : ''; ?>" autocomplete="off" placeholder="YYYY-MM-DD">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Expected Closure Date</label>
                <input type="text" class="form-control datepicker" name="expected_close_date" value="<?php echo !empty($ticket['expected_close_date']) ? htmlspecialchars($ticket['expected_close_date']) : ''; ?>" autocomplete="off" placeholder="YYYY-MM-DD">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Initial Follow-up Date</label>
                <input type="text" class="form-control datepicker" name="followup_date" value="<?php echo !empty($ticket['followup_date']) ? htmlspecialchars($ticket['followup_date']) : ''; ?>" autocomplete="off" placeholder="YYYY-MM-DD">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Initial Follow-up Time</label>
                <div class="input-group bootstrap-timepicker timepicker">
                  <input type="text" class="form-control input-small timepicker" name="followup_time" value="<?php echo htmlspecialchars($ticket['followup_time'] ?? ''); ?>">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Description & Attachments</h3>
        </div>
        
        <div class="box-body">
          <div class="row">
            
            <div class="col-lg-6 col-sm-12">
              <div class="form-group">
                <label>Description<span>*</span></label>
                <textarea class="form-control" name="description" rows="5" required><?php echo htmlspecialchars($ticket['description']); ?></textarea>
              </div>
            </div>

            <div class="col-lg-6 col-sm-12">
              <div class="form-group">
                <label>Internal Notes</label>
                <textarea class="form-control" name="internal_notes" rows="3"><?php echo htmlspecialchars($ticket['internal_notes'] ?? ''); ?></textarea>
              </div>
              <div class="form-group">
                <label>Attach File / Document</label>
                <input type="file" name="attachment" class="form-control">
                <?php if(!empty($ticket['attachment'])){ ?>
                  <p class="help-block"><a href="<?php echo base_url(); ?>assets/uploads/tickets/<?php echo $ticket['attachment']; ?>" target="_blank"><i class="fa fa-paperclip"></i> View Current: <?php echo htmlspecialchars($ticket['attachment']); ?></a></p>
                <?php } ?>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="box-footer text-center">
        <button id="add_btn" type="submit" class="btn btn-info">Update Ticket</button>
        <a href="<?php echo base_url(); ?>manager/tickets/view/<?php echo $ticket['id']; ?>" class="btn btn-default">Cancel</a>
      </div>
      
    </form>
    
  </section>
</div>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>

<script>
$(function(){
  $('.select2-company-ajax').select2({
    placeholder: 'Type at least 3 characters...',
    minimumInputLength: 3,
    ajax: {
      url: '<?php echo base_url(); ?>manager/leads/ajax_search_companies',
      dataType: 'json',
      delay: 250,
      data: function(params){
        return { q: params.term };
      },
      processResults: function(data){
        return { results: data.results || [] };
      },
      cache: true
    }
  });
});
</script>
