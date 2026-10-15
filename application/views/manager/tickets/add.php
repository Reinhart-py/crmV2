<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="container">
<div class="content-wrapper"> 
  
  <section class="content-header">
    <h1> Tickets </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i>Home</a></li>
      <li><a href="<?php echo base_url(); ?>manager/tickets">Tickets</a></li>
      <li class="active">Raise Ticket</li>
    </ol>
  </section>
  
  <section class="content"> 
    
    <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
    <?php echo get_message($this->session->flashdata('message'),'message'); ?>
     
    <form role="form" action="<?php echo base_url(); ?>manager/tickets/add" method="post" enctype="multipart/form-data" name="add_form">
      
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
                  <option value="Onboarded">Onboarded</option>
                  <option value="Non-Onboarded">Non-Onboarded</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Company Name<span>*</span></label>
                <select class="form-control select2" name="company_name" required style="width: 100%;">
                  <option value="">Select Company</option>
                  <?php if(isset($companies_list) && !empty($companies_list)){
                    foreach($companies_list as $comp){ ?>
                      <option value="<?php echo htmlspecialchars($comp['name']); ?>"><?php echo htmlspecialchars($comp['name']); ?></option>
                  <?php } } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>POC Name</label>
                <input type="text" class="form-control" name="poc">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>POC Contact Number</label>
                <input type="text" class="form-control" name="contact1">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Alternative Contact Number</label>
                <input type="text" class="form-control" name="alt_contact">
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
                <label>City / Location</label>
                <input type="text" class="form-control" name="city">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Ticket Source</label>
                <select class="form-control select2" name="lead_source" style="width: 100%;">
                  <option value="Internal Request">Internal Request</option>
                  <option value="Phone Call">Phone Call</option>
                  <option value="Email">Email</option>
                  <option value="WhatsApp">WhatsApp</option>
                  <option value="Client Portal">Client Portal</option>
                  <option value="Direct / Meeting">Direct / Meeting</option>
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
                  <option value="Sales">Sales</option>
                  <option value="Back Office">Back Office</option>
                  <option value="Operations">Operations</option>
                  <option value="IT">IT</option>
                  <option value="HR">HR</option>
                  <option value="Support">Support</option>
                  <option value="Accounts">Accounts</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Ticket Type<span>*</span></label>
                <select class="form-control select2" name="ticket_type" required style="width: 100%;">
                  <option value="Bug / Technical Issue">Bug / Technical Issue</option>
                  <option value="Feature Request">Feature Request</option>
                  <option value="System Access / Permission">System Access / Permission</option>
                  <option value="Billing / Invoice Inquiry">Billing / Invoice Inquiry</option>
                  <option value="Client Query / Onboarding">Client Query / Onboarding</option>
                  <option value="Operational Support">Operational Support</option>
                  <option value="Hardware / Equipment">Hardware / Equipment</option>
                  <option value="General Inquiry">General Inquiry</option>
                  <option value="Escalation">Escalation</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Category<span>*</span></label>
                <select class="form-control select2" name="category" required style="width: 100%;">
                  <option value="Software">Software</option>
                  <option value="Hardware">Hardware</option>
                  <option value="Network">Network</option>
                  <option value="Lead Management">Lead Management</option>
                  <option value="Calling System">Calling System</option>
                  <option value="CRM Access">CRM Access</option>
                  <option value="Data Correction">Data Correction</option>
                  <option value="Attendance / HR">Attendance / HR</option>
                  <option value="Security / Breach">Security / Breach</option>
                  <option value="Other">Other</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Priority<span>*</span></label>
                <select class="form-control select2" name="priority" required style="width: 100%;">
                  <option value="Low">Low</option>
                  <option value="Medium" selected>Medium</option>
                  <option value="High">High</option>
                  <option value="Urgent">Urgent</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Status<span>*</span></label>
                <select class="form-control select2" name="status" required style="width: 100%;">
                  <option value="Open" selected>Open</option>
                  <option value="Assigned">Assigned</option>
                  <option value="In Progress">In Progress</option>
                  <option value="Pending">Pending</option>
                  <option value="On Hold">On Hold</option>
                  <option value="Resolved">Resolved</option>
                </select>
              </div>
            </div>

            <div class="col-lg-9 col-sm-6">
              <div class="form-group">
                <label>Ticket Title<span>*</span></label>
                <input type="text" name="ticket_title" class="form-control" placeholder="Enter ticket title" required>
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
                <select class="form-control select2" name="assigned_users[]" multiple="multiple" data-placeholder="Select assignees" style="width: 100%;" required>
                  <?php if(isset($users_list) && !empty($users_list)){
                    foreach($users_list as $u){ ?>
                      <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['name']); ?> (<?php echo htmlspecialchars($u['email']); ?>)</option>
                  <?php } } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-6 col-sm-12">
              <div class="form-group">
                <label>Notify / Tag Users</label>
                <select class="form-control select2" name="tagged_users[]" multiple="multiple" data-placeholder="Select users to tag" style="width: 100%;">
                  <?php if(isset($users_list) && !empty($users_list)){
                    foreach($users_list as $u){ ?>
                      <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['name']); ?></option>
                  <?php } } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Raised By</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($this->session->userdata('manager_name')); ?>" readonly>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Created Date</label>
                <input type="text" class="form-control" value="<?php echo date('d-m-Y'); ?>" readonly>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Deadline Date</label>
                <input type="text" class="form-control datepicker" name="deadline_date" autocomplete="off" placeholder="YYYY-MM-DD">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Expected Closure Date</label>
                <input type="text" class="form-control datepicker" name="expected_close_date" autocomplete="off" placeholder="YYYY-MM-DD">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Initial Follow-up Date</label>
                <input type="text" class="form-control datepicker" name="followup_date" autocomplete="off" placeholder="YYYY-MM-DD">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Initial Follow-up Time</label>
                <input type="text" class="form-control" name="followup_time" placeholder="HH:MM">
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
                <textarea class="form-control" name="description" rows="5" placeholder="Enter detailed ticket description..." required></textarea>
              </div>
            </div>

            <div class="col-lg-6 col-sm-12">
              <div class="form-group">
                <label>Internal Notes</label>
                <textarea class="form-control" name="internal_notes" rows="3" placeholder="Enter optional internal notes..."></textarea>
              </div>
              <div class="form-group">
                <label>Attach File / Document</label>
                <input type="file" name="attachment" class="form-control">
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="box-footer text-center">
        <button id="add_btn" type="submit" class="btn btn-info">Submit</button>
        <a href="<?php echo base_url(); ?>manager/tickets" class="btn btn-default">Cancel</a>
      </div>
      
    </form>
    
  </section>
</div>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
