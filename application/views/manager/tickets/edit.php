<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper px-4">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1> <?php echo $info['page_heading']; ?> </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?php echo base_url(); ?>manager/tickets">Tickets</a></li>
      <li><a href="<?php echo base_url(); ?>manager/tickets/view/<?php echo $ticket['id']; ?>">Ticket #<?php echo $ticket['ticket_number']; ?></a></li>
      <li class="active">Edit</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?> 
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <form role="form" action="<?php echo base_url(); ?>manager/tickets/edit/<?php echo $ticket['id']; ?>" method="post" enctype="multipart/form-data">
      
      <!-- Box 1: Client & Company Information -->
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-building-o text-primary"></i> 1. Client & Company Details</h3>
        </div>
        <div class="box-body">
          <div class="row">
            
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Client Type<span>*</span></label>
                <select class="form-control" name="client_type" required>
                  <option value="Onboarded" <?php echo ($ticket['client_type'] == 'Onboarded') ? 'selected' : ''; ?>>Onboarded</option>
                  <option value="Non-Onboarded" <?php echo ($ticket['client_type'] == 'Non-Onboarded') ? 'selected' : ''; ?>>Non-Onboarded</option>
                </select>
              </div>
            </div>

            <div class="col-lg-6 col-sm-12">
              <div class="form-group">
                <label>Company Name (Database Dropdown & Filter)<span>*</span></label>
                <select class="form-control select2" name="company_name" style="width: 100%;">
                  <option value="">-- Select Company from Database --</option>
                  <?php 
                    $current_comp = !empty($ticket['company_name']) ? $ticket['company_name'] : $ticket['customer'];
                    if(isset($companies_list) && !empty($companies_list)) { 
                    foreach($companies_list as $cmp) { ?>
                      <option value="<?php echo htmlspecialchars($cmp); ?>" <?php echo ($current_comp == $cmp) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cmp); ?></option>
                  <?php } } ?>
                </select>
                <small class="text-muted">Or enter a new company name below if not listed:</small>
                <input type="text" class="form-control" name="new_company_name" value="<?php echo htmlspecialchars($current_comp); ?>" placeholder="Type company name..." style="margin-top: 5px;">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Department<span>*</span></label>
                <select class="form-control" name="department" required>
                  <?php 
                    $depts = array('Sales', 'Back Office', 'Operations', 'IT', 'HR', 'Support', 'Accounts & Finance');
                    foreach($depts as $d) {
                      $sel = (isset($ticket['department']) && $ticket['department'] == $d) ? 'selected' : '';
                      echo "<option value=\"$d\" $sel>$d</option>";
                    }
                  ?>
                </select>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- Box 2: Ticket Classification & Priority -->
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-tags text-primary"></i> 2. Ticket Classification & Priority</h3>
        </div>
        <div class="box-body">
          <div class="row">

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Ticket Type<span>*</span></label>
                <select class="form-control" name="ticket_type" required>
                  <?php 
                    $types = array('Technical Support', 'Billing & Invoicing', 'Operations & Logistics', 'Sales & Onboarding', 'IT & Hardware', 'HR & Admin', 'Service Request', 'Complaint', 'Account Management');
                    foreach($types as $tt) {
                      $sel = (isset($ticket['ticket_type']) && $ticket['ticket_type'] == $tt) ? 'selected' : '';
                      echo "<option value=\"$tt\" $sel>$tt</option>";
                    }
                  ?>
                </select>
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Category<span>*</span></label>
                <select class="form-control" name="category" required>
                  <?php 
                    $cats = array('General', 'Account Access', 'Hardware Issue', 'Software Bug', 'Billing Query', 'Contract & Renewal', 'Service Outage', 'New Feature Request', 'Other');
                    foreach($cats as $c) {
                      $sel = (isset($ticket['category']) && $ticket['category'] == $c) ? 'selected' : '';
                      echo "<option value=\"$c\" $sel>$c</option>";
                    }
                  ?>
                </select>
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Priority<span>*</span></label>
                <select class="form-control" name="priority" required>
                  <?php 
                    $priorities = array('Low', 'Medium', 'High', 'Urgent');
                    foreach($priorities as $p) {
                      $sel = (isset($ticket['priority']) && strtolower($ticket['priority']) == strtolower($p)) ? 'selected' : '';
                      echo "<option value=\"$p\" $sel>$p</option>";
                    }
                  ?>
                </select>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- Box 3: Multi-User Assignment & Notifications -->
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-users text-primary"></i> 3. Assignment & User Notifications</h3>
        </div>
        <div class="box-body">
          <div class="row">

            <?php 
              $cur_assigned = !empty($ticket['assigned_users']) ? explode(',', $ticket['assigned_users']) : ($ticket['assigned_to'] ? array($ticket['assigned_to']) : array());
              $cur_tagged = !empty($ticket['tagged_users']) ? explode(',', $ticket['tagged_users']) : array();
            ?>

            <div class="col-lg-6 col-sm-12">
              <div class="form-group">
                <label>Assigned To (Supports Multiple Users)</label>
                <select class="form-control select2" multiple="multiple" name="assigned_users[]" data-placeholder="Select assignees..." style="width: 100%;">
                  <?php if(isset($users_list) && !empty($users_list)) { 
                    foreach($users_list as $u) { 
                      $sel = in_array((string)$u['id'], $cur_assigned) ? 'selected' : '';
                    ?>
                      <option value="<?php echo $u['id']; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($u['name']); ?> (<?php echo htmlspecialchars($u['email']); ?>)</option>
                  <?php } } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-6 col-sm-12">
              <div class="form-group">
                <label>Notify / Tag Users</label>
                <select class="form-control select2" multiple="multiple" name="tagged_users[]" data-placeholder="Select users to tag..." style="width: 100%;">
                  <?php if(isset($users_list) && !empty($users_list)) { 
                    foreach($users_list as $u) { 
                      $sel = in_array((string)$u['id'], $cur_tagged) ? 'selected' : '';
                    ?>
                      <option value="<?php echo $u['id']; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($u['name']); ?> (<?php echo htmlspecialchars($u['email']); ?>)</option>
                  <?php } } ?>
                </select>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- Box 4: Action Deadlines & Schedule -->
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-calendar text-primary"></i> 4. Action Deadlines & Target Closures</h3>
        </div>
        <div class="box-body">
          <div class="row">

            <div class="col-lg-6 col-sm-6">
              <div class="form-group">
                <label>Deadline Date <span class="text-muted">(When action must be taken)</span></label>
                <input type="text" class="form-control datepicker" name="deadline_date" value="<?php echo (!empty($ticket['deadline_date']) && $ticket['deadline_date'] != '0000-00-00') ? date('d-m-Y', strtotime($ticket['deadline_date'])) : ''; ?>" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-6 col-sm-6">
              <div class="form-group">
                <label>Expected Closure Date</label>
                <input type="text" class="form-control datepicker" name="expected_close_date" value="<?php echo (!empty($ticket['expected_close_date']) && $ticket['expected_close_date'] != '0000-00-00') ? date('d-m-Y', strtotime($ticket['expected_close_date'])) : ''; ?>" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- Box 5: Content, Notes & Attachments -->
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-file-text-o text-primary"></i> 5. Ticket Content & Attachments</h3>
        </div>
        <div class="box-body">
          <div class="row">

            <div class="col-sm-12">
              <div class="form-group">
                <label>Ticket Title / Subject<span>*</span></label>
                <input type="text" class="form-control" name="ticket_title" value="<?php echo htmlspecialchars(!empty($ticket['ticket_title']) ? $ticket['ticket_title'] : $ticket['subject']); ?>" required>
              </div>
            </div>

            <div class="col-sm-12">
              <div class="form-group">
                <label>Description<span>*</span></label>
                <textarea class="form-control" name="description" rows="5" required><?php echo htmlspecialchars($ticket['description']); ?></textarea>
              </div>
            </div>

            <div class="col-sm-12">
              <div class="form-group">
                <label>Internal Notes <span class="text-muted">(Confidential to internal team)</span></label>
                <textarea class="form-control" name="internal_notes" rows="3"><?php echo htmlspecialchars($ticket['internal_notes']); ?></textarea>
              </div>
            </div>

            <div class="col-sm-12">
              <div class="form-group">
                <label>Update Attachment <span class="text-muted">(Optional)</span></label>
                <?php if(!empty($ticket['attachment'])) { ?>
                  <div style="margin-bottom: 8px;">
                    <span class="label label-info"><i class="fa fa-paperclip"></i> Current file: <?php echo htmlspecialchars($ticket['attachment']); ?></span>
                    <a href="<?php echo base_url(); ?>assets/uploads/tickets/<?php echo $ticket['attachment']; ?>" target="_blank" class="btn btn-xs btn-default"><i class="fa fa-download"></i> Download</a>
                  </div>
                <?php } ?>
                <input type="file" name="attachment" class="form-control" style="padding: 5px;">
              </div>
            </div>

          </div>
        </div>

        <div class="box-footer" style="padding: 15px 20px;">
          <button type="submit" class="btn btn-success btn-lg"><i class="fa fa-save"></i> Save Changes</button>
          <a href="<?php echo base_url(); ?>manager/tickets/view/<?php echo $ticket['id']; ?>" class="btn btn-default btn-lg pull-right">Cancel</a>
        </div>
      </div>

    </form>
  </section>
</div>

<script>
$(document).ready(function() {
  if ($.fn.select2) {
    $('.select2').select2({
      width: '100%',
      allowClear: true
    });
  }
  if ($.fn.datepicker) {
    $('.datepicker').datepicker({
      format: 'dd-mm-yyyy',
      autoclose: true,
      todayHighlight: true
    });
  }
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
