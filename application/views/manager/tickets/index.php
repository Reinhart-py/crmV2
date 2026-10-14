<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<style>
/* Strict Black & White Help Desk Design */
.helpdesk-container {
  background: #ffffff;
  color: #000000;
  padding: 15px 0 40px 0;
  font-family: inherit;
}
.helpdesk-header {
  border-bottom: 2px solid #000000;
  padding-bottom: 12px;
  margin-bottom: 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
}
.helpdesk-header h1 {
  font-size: 22px;
  font-weight: 800;
  color: #000000;
  margin: 0;
  letter-spacing: -0.02em;
}
.helpdesk-header .helpdesk-subtitle {
  font-size: 13px;
  color: #444444;
  margin-top: 4px;
}
.helpdesk-card {
  background: #ffffff;
  border: 1px solid #000000;
  border-radius: 4px;
  margin-bottom: 24px;
  box-shadow: none;
}
.helpdesk-card-header {
  background: #000000;
  color: #ffffff;
  padding: 10px 16px;
  font-size: 14px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.helpdesk-card-body {
  padding: 18px;
  background: #ffffff;
}
.helpdesk-stat-box {
  border: 1px solid #000000;
  padding: 14px;
  text-align: center;
  border-radius: 4px;
  background: #ffffff;
  margin-bottom: 16px;
}
.helpdesk-stat-val {
  font-size: 24px;
  font-weight: 800;
  color: #000000;
  line-height: 1;
}
.helpdesk-stat-label {
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #555555;
  margin-top: 6px;
  font-weight: 600;
}
.btn-bw-primary {
  background: #000000 !important;
  color: #ffffff !important;
  border: 1px solid #000000 !important;
  font-weight: 700 !important;
  border-radius: 4px !important;
  padding: 8px 18px !important;
  transition: all 0.15s ease !important;
}
.btn-bw-primary:hover, .btn-bw-primary:focus {
  background: #222222 !important;
  color: #ffffff !important;
}
.btn-bw-outline {
  background: #ffffff !important;
  color: #000000 !important;
  border: 1px solid #000000 !important;
  font-weight: 600 !important;
  border-radius: 4px !important;
  padding: 6px 14px !important;
}
.btn-bw-outline:hover, .btn-bw-outline:focus {
  background: #f1f1f1 !important;
  color: #000000 !important;
}
.bw-badge {
  display: inline-block;
  padding: 3px 8px;
  font-size: 11px;
  font-weight: 700;
  border-radius: 3px;
  text-transform: uppercase;
  letter-spacing: 0.3px;
}
.bw-badge-open {
  background: #000000;
  color: #ffffff;
  border: 1px solid #000000;
}
.bw-badge-progress {
  background: #444444;
  color: #ffffff;
  border: 1px solid #444444;
}
.bw-badge-resolved {
  background: #ffffff;
  color: #000000;
  border: 1px solid #000000;
}
.bw-badge-closed {
  background: #e5e5e5;
  color: #333333;
  border: 1px solid #cccccc;
}
.helpdesk-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}
.helpdesk-table th {
  background: #f4f4f4;
  color: #000000;
  font-weight: 700;
  border: 1px solid #000000;
  padding: 10px 12px;
  text-align: left;
}
.helpdesk-table td {
  border: 1px solid #000000;
  padding: 10px 12px;
  vertical-align: middle;
}
.helpdesk-table tr:hover td {
  background: #fafafa;
}
.faq-item {
  border: 1px solid #000000;
  border-radius: 4px;
  margin-bottom: 12px;
  overflow: hidden;
}
.faq-question {
  background: #f8f9fa;
  padding: 12px 16px;
  font-weight: 700;
  font-size: 13.5px;
  color: #000000;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.faq-question:hover {
  background: #eeeeee;
}
.faq-answer {
  display: none;
  padding: 14px 16px;
  font-size: 13px;
  line-height: 1.6;
  color: #333333;
  border-top: 1px solid #dddddd;
  background: #ffffff;
}
[data-theme="dark"] .helpdesk-container {
  background: #0b0f17;
  color: #ffffff;
}
[data-theme="dark"] .helpdesk-card {
  background: #111827;
  border-color: #ffffff;
}
[data-theme="dark"] .helpdesk-card-header {
  background: #ffffff;
  color: #000000;
}
[data-theme="dark"] .helpdesk-card-body {
  background: #111827;
  color: #ffffff;
}
[data-theme="dark"] .helpdesk-header {
  border-bottom-color: #ffffff;
}
[data-theme="dark"] .helpdesk-header h1 {
  color: #ffffff;
}
[data-theme="dark"] .helpdesk-header .helpdesk-subtitle {
  color: #cccccc;
}
[data-theme="dark"] .helpdesk-stat-box {
  background: #111827;
  border-color: #ffffff;
}
[data-theme="dark"] .helpdesk-stat-val {
  color: #ffffff;
}
[data-theme="dark"] .helpdesk-stat-label {
  color: #bbbbbb;
}
[data-theme="dark"] .helpdesk-table th {
  background: #1f2937;
  color: #ffffff;
  border-color: #ffffff;
}
[data-theme="dark"] .helpdesk-table td {
  border-color: #4b5563;
  color: #ffffff;
}
[data-theme="dark"] .faq-item {
  border-color: #ffffff;
}
[data-theme="dark"] .faq-question {
  background: #1f2937;
  color: #ffffff;
}
[data-theme="dark"] .faq-answer {
  background: #111827;
  color: #e5e5e5;
  border-top-color: #374151;
}
</style>

<div class="container helpdesk-container">
  
  <!-- Header Title -->
  <div class="helpdesk-header">
    <div>
      <h1>HELP DESK</h1>
      <div class="helpdesk-subtitle">
        <?php echo !empty($can_manage_tickets) ? 'Administrative & Support Operations Desk' : 'Submit and follow up on your operational issues'; ?>
      </div>
    </div>
    <div>
      <a href="<?php echo base_url(); ?>manager/dashboard" class="btn btn-bw-outline btn-sm">
        <i class="fa fa-arrow-left"></i> Dashboard
      </a>
    </div>
  </div>

  <!-- Flash Messages -->
  <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
  <?php echo get_message($this->session->flashdata('message'),'message'); ?>

  <!-- KPI Counts Row (Black & White) -->
  <div class="row">
    <div class="col-xs-6 col-sm-3">
      <div class="helpdesk-stat-box">
        <div class="helpdesk-stat-val"><?php echo (int)$count_open; ?></div>
        <div class="helpdesk-stat-label">Open Tickets</div>
      </div>
    </div>
    <div class="col-xs-6 col-sm-3">
      <div class="helpdesk-stat-box">
        <div class="helpdesk-stat-val"><?php echo (int)$count_in_progress; ?></div>
        <div class="helpdesk-stat-label">In Progress</div>
      </div>
    </div>
    <div class="col-xs-6 col-sm-3">
      <div class="helpdesk-stat-box">
        <div class="helpdesk-stat-val"><?php echo (int)$count_resolved; ?></div>
        <div class="helpdesk-stat-label">Resolved</div>
      </div>
    </div>
    <div class="col-xs-6 col-sm-3">
      <div class="helpdesk-stat-box">
        <div class="helpdesk-stat-val"><?php echo (int)$count_all; ?></div>
        <div class="helpdesk-stat-label">Total Tickets</div>
      </div>
    </div>
  </div>

  <!-- Inline Submit An Issue Form (No popup, No separate page) -->
  <div class="helpdesk-card">
    <div class="helpdesk-card-header">
      <span><i class="fa fa-edit" style="margin-right: 6px;"></i> SUBMIT AN ISSUE / RAISE TICKET</span>
      <span style="font-size: 11px; font-weight: normal; opacity: 0.85;">Direct Help Desk Ticket Submission</span>
    </div>
    <div class="helpdesk-card-body">
      <form action="<?php echo base_url(); ?>manager/tickets/add" method="post" id="inlineTicketForm">
        <div class="row">
          <div class="col-md-6 col-sm-12">
            <div class="form-group">
              <label style="font-weight: 700; font-size: 12px; text-transform: uppercase;">Issue Subject <span class="text-danger">*</span></label>
              <input type="text" name="subject" class="form-control" placeholder="Describe the issue briefly..." required style="border: 1px solid #000; border-radius: 3px; font-size: 13px;">
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="form-group">
              <label style="font-weight: 700; font-size: 12px; text-transform: uppercase;">Category</label>
              <select name="category" class="form-control" style="border: 1px solid #000; border-radius: 3px; font-size: 13px;">
                <option value="Lead Management">Lead Management</option>
                <option value="Reports & Projections">Reports & Projections</option>
                <option value="Data Import / Export">Data Import / Export</option>
                <option value="Verticals">Verticals</option>
                <option value="User & Access">User & Access</option>
                <option value="General Issue" selected>General Issue</option>
              </select>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="form-group">
              <label style="font-weight: 700; font-size: 12px; text-transform: uppercase;">Priority</label>
              <select name="priority" class="form-control" style="border: 1px solid #000; border-radius: 3px; font-size: 13px;">
                <option value="Low">Low</option>
                <option value="Medium" selected>Medium</option>
                <option value="High">High</option>
                <option value="Urgent">Urgent</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-group" style="margin-bottom: 14px;">
          <label style="font-weight: 700; font-size: 12px; text-transform: uppercase;">Detailed Description <span class="text-danger">*</span></label>
          <textarea name="description" class="form-control" rows="4" placeholder="Provide full details of the problem so our team can resolve it..." required style="border: 1px solid #000; border-radius: 3px; font-size: 13px;"></textarea>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
          <span style="font-size: 12px; color: #555555;">
            <i class="fa fa-info-circle"></i> A tracking number will be assigned automatically. You can chat and follow up directly on this ticket.
          </span>
          <button type="submit" class="btn btn-bw-primary">
            <i class="fa fa-paper-plane" style="margin-right: 5px;"></i> SUBMIT TICKET
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Raised Tickets Table -->
  <div class="helpdesk-card">
    <div class="helpdesk-card-header">
      <span><i class="fa fa-list" style="margin-right: 6px;"></i> <?php echo !empty($can_manage_tickets) ? 'ALL TICKETS & INQUIRIES' : 'MY RAISED TICKETS'; ?></span>
      <span>
        <form method="get" action="<?php echo base_url(); ?>manager/tickets" class="form-inline" style="display: inline-block;">
          <select name="status" class="form-control input-sm" onchange="this.form.submit()" style="border: 1px solid #ffffff; background: #000000; color: #ffffff; font-size: 11px;">
            <option value="">All Statuses</option>
            <option value="Open" <?php if($this->input->get('status')=='Open') echo 'selected'; ?>>Open</option>
            <option value="In Progress" <?php if($this->input->get('status')=='In Progress') echo 'selected'; ?>>In Progress</option>
            <option value="Resolved" <?php if($this->input->get('status')=='Resolved') echo 'selected'; ?>>Resolved</option>
            <option value="Closed" <?php if($this->input->get('status')=='Closed') echo 'selected'; ?>>Closed</option>
          </select>
        </form>
      </span>
    </div>
    <div class="helpdesk-card-body" style="padding: 0;">
      <div class="table-responsive">
        <table class="helpdesk-table">
          <thead>
            <tr>
              <th style="width: 140px;">TICKET #</th>
              <th>SUBJECT & CATEGORY</th>
              <th style="width: 100px;">PRIORITY</th>
              <?php if (!empty($can_manage_tickets)) { ?>
                <th style="width: 160px;">RAISED BY</th>
              <?php } ?>
              <th style="width: 110px;">STATUS</th>
              <th style="width: 150px;">DATE CREATED</th>
              <th style="width: 120px; text-align: center;">ACTIONS</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($tickets)) { 
              foreach ($tickets as $t) { 
                $badge_class = 'bw-badge-open';
                if ($t['status'] == 'In Progress') $badge_class = 'bw-badge-progress';
                if ($t['status'] == 'Resolved') $badge_class = 'bw-badge-resolved';
                if ($t['status'] == 'Closed') $badge_class = 'bw-badge-closed';
            ?>
              <tr>
                <td>
                  <a href="<?php echo base_url(); ?>manager/tickets/view/<?php echo $t['id']; ?>" style="font-weight: 800; color: #000000; text-decoration: underline;">
                    <?php echo htmlspecialchars($t['ticket_number']); ?>
                  </a>
                </td>
                <td>
                  <div style="font-weight: 700; color: #000000;">
                    <a href="<?php echo base_url(); ?>manager/tickets/view/<?php echo $t['id']; ?>" style="color: inherit; text-decoration: none;">
                      <?php echo htmlspecialchars($t['subject']); ?>
                    </a>
                  </div>
                  <small style="color: #666666; font-size: 11px;">[<?php echo htmlspecialchars($t['category']); ?>]</small>
                </td>
                <td>
                  <span style="font-size: 12px; font-weight: 600;"><?php echo htmlspecialchars($t['priority']); ?></span>
                </td>
                <?php if (!empty($can_manage_tickets)) { ?>
                  <td>
                    <div style="font-weight: 600; font-size: 12.5px;"><?php echo !empty($t['creator_name']) ? htmlspecialchars($t['creator_name']) : 'User #'.$t['created_by']; ?></div>
                    <small style="color: #666666; font-size: 11px;"><?php echo htmlspecialchars($t['creator_email'] ?? ''); ?></small>
                  </td>
                <?php } ?>
                <td>
                  <span class="bw-badge <?php echo $badge_class; ?>">
                    <?php echo htmlspecialchars($t['status']); ?>
                  </span>
                </td>
                <td>
                  <small style="color: #444444;"><?php echo date('d-M-Y h:i A', strtotime($t['created_at'])); ?></small>
                </td>
                <td style="text-align: center;">
                  <div class="btn-group" style="min-width: 85px;">
                    <button type="button" class="btn btn-sm btn-warning">Action</button>
                    <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                      <span class="caret"></span>
                      <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu" style="text-align: left; box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
                      <li>
                        <a href="<?php echo base_url(); ?>manager/tickets/view/<?php echo $t['id']; ?>">
                          <i class="fa fa-eye" style="margin-right: 6px; color: #1e293b;"></i> View Ticket
                        </a>
                      </li>
                      <li>
                        <a href="<?php echo base_url(); ?>manager/tickets/view/<?php echo $t['id']; ?>#reply-box">
                          <i class="fa fa-reply" style="margin-right: 6px; color: #0284c7;"></i> Reply
                        </a>
                      </li>
                      <li class="divider"></li>
                      <?php if ($t['status'] != 'Resolved') { ?>
                        <li>
                          <a href="<?php echo base_url(); ?>manager/tickets/close/<?php echo $t['id']; ?>" onclick="return confirm('Clear and mark this ticket as Resolved?');">
                            <i class="fa fa-check" style="margin-right: 6px; color: #16a34a;"></i> Clear / Mark Resolved
                          </a>
                        </li>
                      <?php } ?>
                      <?php if (!empty($can_manage_tickets)) { ?>
                        <?php if ($t['status'] != 'In Progress') { ?>
                          <li>
                            <a href="<?php echo base_url(); ?>manager/tickets/status/<?php echo $t['id']; ?>/In%20Progress">
                              <i class="fa fa-clock-o" style="margin-right: 6px; color: #ea580c;"></i> Mark In Progress
                            </a>
                          </li>
                        <?php } ?>
                        <?php if ($t['status'] != 'Closed') { ?>
                          <li>
                            <a href="<?php echo base_url(); ?>manager/tickets/status/<?php echo $t['id']; ?>/Closed" onclick="return confirm('Mark this ticket as Closed?');">
                              <i class="fa fa-times-circle" style="margin-right: 6px; color: #64748b;"></i> Mark as Closed
                            </a>
                          </li>
                        <?php } ?>
                      <?php } ?>
                      <?php if ($t['status'] == 'Resolved' || $t['status'] == 'Closed') { ?>
                        <li>
                          <a href="<?php echo base_url(); ?>manager/tickets/status/<?php echo $t['id']; ?>/Open">
                            <i class="fa fa-refresh" style="margin-right: 6px; color: #2563eb;"></i> Re-Open Ticket
                          </a>
                        </li>
                      <?php } ?>
                      <?php if (!empty($is_admin)) { ?>
                        <li class="divider"></li>
                        <li>
                          <a href="<?php echo base_url(); ?>manager/tickets/delete/<?php echo $t['id']; ?>" onclick="return confirm('Permanently delete ticket #<?php echo $t['ticket_number']; ?> and all chat history?');" style="color: #dc2626;">
                            <i class="fa fa-trash" style="margin-right: 6px;"></i> Delete Ticket
                          </a>
                        </li>
                      <?php } ?>
                    </ul>
                  </div>
                </td>
              </tr>
            <?php } } else { ?>
              <tr>
                <td colspan="<?php echo !empty($can_manage_tickets) ? '7' : '6'; ?>" style="text-align: center; padding: 36px 20px; color: #555555;">
                  <i class="fa fa-ticket" style="font-size: 28px; display: block; margin-bottom: 8px;"></i>
                  No tickets found. Submit an issue above whenever you need assistance.
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Frequently Asked Questions (FAQ) Section -->
  <div class="helpdesk-card">
    <div class="helpdesk-card-header">
      <span><i class="fa fa-question-circle" style="margin-right: 6px;"></i> FREQUENTLY ASKED QUESTIONS (FAQ)</span>
      <span style="font-size: 11px; font-weight: normal; opacity: 0.85;">Common CRM Solutions</span>
    </div>
    <div class="helpdesk-card-body">
      
      <div class="faq-item">
        <div class="faq-question" onclick="toggleFaq(this)">
          <span>1. How do I create and manage a new lead?</span>
          <i class="fa fa-chevron-down"></i>
        </div>
        <div class="faq-answer">
          Go to <strong>Leads</strong> in the navigation menu and click <strong>Add Leads</strong>. Fill in the prospect's details (contact person, organization, phone number, email, and requirements) and click Save. You can track progress, view activity history, and update contact information from the Leads list.
        </div>
      </div>

      <div class="faq-item">
        <div class="faq-question" onclick="toggleFaq(this)">
          <span>2. How do I schedule and update follow-ups?</span>
          <i class="fa fa-chevron-down"></i>
        </div>
        <div class="faq-answer">
          When viewing a lead, click <strong>Add Followup</strong> to record interaction notes and set the next call or meeting date. All your upcoming follow-ups are organized chronologically under <strong>Followups</strong> so you never miss a client interaction.
        </div>
      </div>

      <div class="faq-item">
        <div class="faq-question" onclick="toggleFaq(this)">
          <span>3. Where do I schedule and log client meetings?</span>
          <i class="fa fa-chevron-down"></i>
        </div>
        <div class="faq-answer">
          Navigate to <strong>Meetings</strong> from the menu. From there, you can view your scheduled calendar, record new meeting appointments, and add post-meeting summaries and action items.
        </div>
      </div>

      <div class="faq-item">
        <div class="faq-question" onclick="toggleFaq(this)">
          <span>4. How do I record a closed deal or update stage status?</span>
          <i class="fa fa-chevron-down"></i>
        </div>
        <div class="faq-answer">
          Open the lead and change the stage status to <strong>Won / Closed</strong> or your relevant pipeline milestone. Enter the deal value and closed products so your revenue projections and performance metrics update immediately.
        </div>
      </div>

      <div class="faq-item">
        <div class="faq-question" onclick="toggleFaq(this)">
          <span>5. How do I generate and view my performance reports?</span>
          <i class="fa fa-chevron-down"></i>
        </div>
        <div class="faq-answer">
          Click <strong>Reports</strong> in the navigation bar. You can review your daily and monthly lead conversion metrics, export report files, and track your active sales pipeline progress.
        </div>
      </div>

      <div class="faq-item">
        <div class="faq-question" onclick="toggleFaq(this)">
          <span>6. How do I get assistance or report an issue through Help Desk?</span>
          <i class="fa fa-chevron-down"></i>
        </div>
        <div class="faq-answer">
          Submit your issue using the <strong>Submit an Issue</strong> form above on this page. Once submitted, click on your ticket number or <strong>View / Chat</strong> to follow up directly with the support team in real time.
        </div>
      </div>

    </div>
  </div>

</div>

<script>
function toggleFaq(el) {
  var $question = $(el);
  var $answer = $question.next('.faq-answer');
  var isCurrentlyVisible = $answer.is(':visible');

  // Auto-collapse any open answers and reset all chevron icons
  $('.faq-answer').slideUp(180);
  $('.faq-question i').removeClass('fa-chevron-up').addClass('fa-chevron-down');

  // If clicked item was not open, open it
  if (!isCurrentlyVisible) {
    $answer.slideDown(180);
    $question.find('i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
  }
}
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
