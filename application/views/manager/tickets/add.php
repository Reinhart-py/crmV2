<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper px-4">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1> <?php echo $info['page_heading']; ?> </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?php echo base_url(); ?>manager/tickets">Tickets</a></li>
      <li class="active">Raise Ticket</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?> 
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <form role="form" action="<?php echo base_url(); ?>manager/tickets/add" method="post" enctype="multipart/form-data">
      
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
                <select class="form-control" name="client_type" id="client_type" required>
                  <option value="Onboarded" selected>Onboarded</option>
                  <option value="Non-Onboarded">Non-Onboarded</option>
                </select>
              </div>
            </div>

            <div class="col-lg-6 col-sm-12">
              <div class="form-group">
                <label>Company Name (Database Dropdown & Filter)<span>*</span></label>
                <select class="form-control select2" name="company_name" id="company_select" style="width: 100%;">
                  <option value="">-- Select Company from Database --</option>
                  <?php if(isset($companies_list) && !empty($companies_list)) { 
                    foreach($companies_list as $cmp) { ?>
                      <option value="<?php echo htmlspecialchars($cmp); ?>"><?php echo htmlspecialchars($cmp); ?></option>
                  <?php } } ?>
                </select>
                <small class="text-muted">Or enter a new company below if not listed in database:</small>
                <input type="text" class="form-control" name="new_company_name" id="new_company_name" placeholder="Or type new company name..." style="margin-top: 5px;">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Department<span>*</span></label>
                <select class="form-control" name="department" required>
                  <option value="Sales">Sales</option>
                  <option value="Back Office">Back Office</option>
                  <option value="Operations" selected>Operations</option>
                  <option value="IT">IT</option>
                  <option value="HR">HR</option>
                  <option value="Support">Support</option>
                  <option value="Accounts & Finance">Accounts & Finance</option>
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
                  <option value="Technical Support" selected>Technical Support</option>
                  <option value="Billing & Invoicing">Billing & Invoicing</option>
                  <option value="Operations & Logistics">Operations & Logistics</option>
                  <option value="Sales & Onboarding">Sales & Onboarding</option>
                  <option value="IT & Hardware">IT & Hardware</option>
                  <option value="HR & Admin">HR & Admin</option>
                  <option value="Service Request">Service Request</option>
                  <option value="Complaint">Complaint</option>
                  <option value="Account Management">Account Management</option>
                </select>
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Category<span>*</span></label>
                <select class="form-control" name="category" required>
                  <option value="General" selected>General</option>
                  <option value="Account Access">Account Access</option>
                  <option value="Hardware Issue">Hardware Issue</option>
                  <option value="Software Bug">Software Bug</option>
                  <option value="Billing Query">Billing Query</option>
                  <option value="Contract & Renewal">Contract & Renewal</option>
                  <option value="Service Outage">Service Outage</option>
                  <option value="New Feature Request">New Feature Request</option>
                  <option value="Other">Other</option>
                </select>
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Priority<span>*</span></label>
                <select class="form-control" name="priority" required>
                  <option value="Low">Low</option>
                  <option value="Medium" selected>Medium</option>
                  <option value="High">High</option>
                  <option value="Urgent">Urgent</option>
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

            <div class="col-lg-6 col-sm-12">
              <div class="form-group">
                <label>Assigned To (Supports Multiple Users) <span class="text-muted">(Optional)</span></label>
                <select class="form-control select2" multiple="multiple" name="assigned_users[]" data-placeholder="Select one or more assignees..." style="width: 100%;">
                  <?php if(isset($users_list) && !empty($users_list)) { 
                    foreach($users_list as $u) { ?>
                      <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['name']); ?> (<?php echo htmlspecialchars($u['email']); ?>)</option>
                  <?php } } ?>
                </select>
                <small class="text-muted">Tickets can be simultaneously assigned to multiple people across departments.</small>
              </div>
            </div>

            <div class="col-lg-6 col-sm-12">
              <div class="form-group">
                <label>Notify / Tag Users <span class="text-muted">(Optional)</span></label>
                <select class="form-control select2" multiple="multiple" name="tagged_users[]" data-placeholder="Select users to notify/tag..." style="width: 100%;">
                  <?php if(isset($users_list) && !empty($users_list)) { 
                    foreach($users_list as $u) { ?>
                      <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['name']); ?> (<?php echo htmlspecialchars($u['email']); ?>)</option>
                  <?php } } ?>
                </select>
                <small class="text-muted">Keep stakeholders informed with notification tracking.</small>
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
                <input type="text" class="form-control datepicker" name="deadline_date" placeholder="DD-MM-YYYY" autocomplete="off">
                <small class="text-muted">Tickets exceeding this date will be highlighted as Overdue.</small>
              </div>
            </div>

            <div class="col-lg-6 col-sm-6">
              <div class="form-group">
                <label>Expected Closure Date</label>
                <input type="text" class="form-control datepicker" name="expected_close_date" placeholder="DD-MM-YYYY" autocomplete="off">
                <small class="text-muted">Estimated target resolution date.</small>
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
                <input type="text" class="form-control" name="ticket_title" placeholder="Brief summary of the issue or request..." required>
              </div>
            </div>

            <div class="col-sm-12">
              <div class="form-group">
                <label>Description<span>*</span></label>
                <textarea class="form-control" name="description" rows="5" placeholder="Detailed description of the requirement, problem, or inquiry..." required></textarea>
              </div>
            </div>

            <div class="col-sm-12">
              <div class="form-group">
                <label>Internal Notes <span class="text-muted">(Optional - Visible only to internal staff)</span></label>
                <textarea class="form-control" name="internal_notes" rows="3" placeholder="Add confidential notes, observations, or handover instructions..."></textarea>
              </div>
            </div>

            <div class="col-sm-12">
              <div class="form-group">
                <label>Attach Relevant Document / File <span class="text-muted">(PDF, Word, Excel, CSV, PNG, JPG, ZIP - Max 15MB)</span></label>
                <input type="file" name="attachment" class="form-control" style="padding: 5px;">
              </div>
            </div>

          </div>
        </div>

        <div class="box-footer" style="padding: 15px 20px;">
          <button type="submit" class="btn btn-success btn-lg"><i class="fa fa-check"></i> Submit Ticket</button>
          <a href="<?php echo base_url(); ?>manager/tickets" class="btn btn-default btn-lg pull-right">Cancel</a>
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
