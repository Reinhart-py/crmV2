<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<!-- Content Wrapper. Contains page content -->
<div class="container">
<div class="content-wrapper"> 
  
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1> Tickets </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i>Home</a></li>
      <li><a href="<?php echo base_url(); ?>manager/tickets">Tickets</a></li>
      <li class="active">Edit Ticket #<?php echo htmlspecialchars($ticket['ticket_number']); ?></li>
    </ol>
  </section>
  
  <!-- Main content -->
  <section class="content"> 
    
    <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
    <?php echo get_message($this->session->flashdata('message'),'message'); ?>
     
    <form role="form" action="<?php echo base_url(); ?>manager/tickets/edit/<?php echo $ticket['id']; ?>" method="post" enctype="multipart/form-data" name="edit_form">
      
      <!-- Primary Ticket Information Box -->
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Ticket Information (#<?php echo htmlspecialchars($ticket['ticket_number']); ?>)</h3>
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
                <select class="form-control select2" name="company_name" required style="width: 100%;">
                  <option value="">Select Company</option>
                  <?php 
                  $current_comp = !empty($ticket['company_name']) ? $ticket['company_name'] : $ticket['customer'];
                  if(isset($companies_list) && !empty($companies_list)){
                    foreach($companies_list as $comp){ ?>
                      <option value="<?php echo htmlspecialchars($comp['name']); ?>" <?php if($comp['name'] == $current_comp) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($comp['name']); ?>
                      </option>
                  <?php } } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Department<span>*</span></label>
                <select class="form-control select2" name="department" required style="width: 100%;">
                  <?php 
                  $depts = array('Sales', 'Back Office', 'Operations', 'IT', 'HR', 'Support', 'Accounts');
                  foreach($depts as $d){ ?>
                    <option value="<?php echo $d; ?>" <?php if(!empty($ticket['department']) && $ticket['department']==$d) echo 'selected'; ?>><?php echo $d; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Ticket Type<span>*</span></label>
                <select class="form-control select2" name="ticket_type" required style="width: 100%;">
                  <?php 
                  $ttypes = array(
                    'Bug / Technical Issue',
                    'Feature Request',
                    'System Access / Permission',
                    'Billing / Invoice Inquiry',
                    'Client Query / Onboarding',
                    'Operational Support',
                    'Hardware / Equipment',
                    'General Inquiry',
                    'Escalation'
                  );
                  foreach($ttypes as $tt){ ?>
                    <option value="<?php echo $tt; ?>" <?php if(!empty($ticket['ticket_type']) && $ticket['ticket_type']==$tt) echo 'selected'; ?>><?php echo $tt; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Category<span>*</span></label>
                <select class="form-control select2" name="category" required style="width: 100%;">
                  <?php 
                  $cats = array('Software', 'Hardware', 'Network', 'Lead Management', 'Calling System', 'CRM Access', 'Data Correction', 'Attendance / HR', 'Security / Breach', 'Other');
                  foreach($cats as $c){ ?>
                    <option value="<?php echo $c; ?>" <?php if(!empty($ticket['category']) && $ticket['category']==$c) echo 'selected'; ?>><?php echo $c; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Priority<span>*</span></label>
                <select class="form-control select2" name="priority" required style="width: 100%;">
                  <option value="Low" <?php if($ticket['priority']=='Low') echo 'selected'; ?>>Low</option>
                  <option value="Medium" <?php if($ticket['priority']=='Medium') echo 'selected'; ?>>Medium</option>
                  <option value="High" <?php if($ticket['priority']=='High') echo 'selected'; ?>>High</option>
                  <option value="Urgent" <?php if($ticket['priority']=='Urgent') echo 'selected'; ?>>Urgent</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Deadline Date</label>
                <input type="text" class="form-control datepicker" name="deadline_date" autocomplete="off" value="<?php echo htmlspecialchars($ticket['deadline_date'] ?? ''); ?>" placeholder="YYYY-MM-DD">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Expected Closure Date</label>
                <input type="text" class="form-control datepicker" name="expected_close_date" autocomplete="off" value="<?php echo htmlspecialchars($ticket['expected_close_date'] ?? ''); ?>" placeholder="YYYY-MM-DD">
              </div>
            </div>

            <div class="col-lg-12 col-sm-12">
              <div class="form-group">
                <label>Ticket Title<span>*</span></label>
                <input type="text" name="ticket_title" class="form-control" value="<?php echo htmlspecialchars(!empty($ticket['ticket_title']) ? $ticket['ticket_title'] : $ticket['subject']); ?>" required>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- Assignment & Notification Box -->
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Assignment & Notifications</h3>
        </div>
        
        <div class="box-body">
          <div class="row">
            
            <?php 
              $assigned_arr = !empty($ticket['assigned_users']) ? explode(',', $ticket['assigned_users']) : (!empty($ticket['assigned_to']) ? array($ticket['assigned_to']) : array());
              $tagged_arr = !empty($ticket['tagged_users']) ? explode(',', $ticket['tagged_users']) : array();
            ?>

            <div class="col-lg-6 col-sm-12">
              <div class="form-group">
                <label>Assigned To (Multiple Users Supported)<span>*</span></label>
                <select class="form-control select2" name="assigned_users[]" multiple="multiple" data-placeholder="Select assignees" style="width: 100%;" required>
                  <?php if(isset($users_list) && !empty($users_list)){
                    foreach($users_list as $u){ ?>
                      <option value="<?php echo $u['id']; ?>" <?php if(in_array((string)$u['id'], $assigned_arr)) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($u['name']); ?> (<?php echo htmlspecialchars($u['email']); ?>)
                      </option>
                  <?php } } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-6 col-sm-12">
              <div class="form-group">
                <label>Notify / Tag Users</label>
                <select class="form-control select2" name="tagged_users[]" multiple="multiple" data-placeholder="Select users to notify" style="width: 100%;">
                  <?php if(isset($users_list) && !empty($users_list)){
                    foreach($users_list as $u){ ?>
                      <option value="<?php echo $u['id']; ?>" <?php if(in_array((string)$u['id'], $tagged_arr)) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($u['name']); ?>
                      </option>
                  <?php } } ?>
                </select>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- Description & Notes Box -->
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Details & Attachments</h3>
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
                <?php if(!empty($ticket['attachment'])){ ?>
                  <p class="help-block">Current: <a href="<?php echo base_url(); ?>assets/admin/tickets/<?php echo htmlspecialchars($ticket['attachment']); ?>" target="_blank"><?php echo htmlspecialchars($ticket['attachment']); ?></a></p>
                <?php } ?>
                <input type="file" name="attachment" class="form-control">
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
