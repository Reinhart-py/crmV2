<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<style>
.ticket-view-wrap {
  padding: 10px 15px 40px 15px;
  background: #f4f6f9;
}
.ticket-top-bar {
  background: #ffffff;
  border: 1px solid #d2d6de;
  border-radius: 4px;
  padding: 14px 20px;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.ticket-card {
  background: #ffffff;
  border: 1px solid #d2d6de;
  border-radius: 4px;
  margin-bottom: 20px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.ticket-card-header {
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
  padding: 12px 18px;
  font-size: 14px;
  font-weight: 700;
  color: #1e293b;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 8px;
}
.ticket-card-body {
  padding: 18px;
}
.meta-table tr td {
  padding: 8px 10px;
  font-size: 13px;
  vertical-align: middle;
}
.meta-table tr td:first-child {
  font-weight: 600;
  color: #475569;
  width: 38%;
  background: #f8fafc;
  border-right: 1px solid #f1f5f9;
}
.audit-timeline {
  position: relative;
  border-left: 2px solid #cbd5e1;
  margin-left: 10px;
  padding-left: 20px;
}
.audit-item {
  position: relative;
  margin-bottom: 18px;
}
.audit-item::before {
  content: '';
  position: absolute;
  left: -27px;
  top: 4px;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #3b82f6;
  border: 2px solid #ffffff;
  box-shadow: 0 0 0 2px #3b82f6;
}
.audit-meta {
  font-size: 11px;
  color: #64748b;
  margin-bottom: 2px;
}
.audit-title {
  font-size: 13px;
  font-weight: 700;
  color: #1e293b;
}
.audit-remarks {
  font-size: 12.5px;
  color: #334155;
  margin-top: 4px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
  padding: 6px 10px;
}
.chat-bubble {
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 14px 16px;
  margin-bottom: 14px;
  background: #ffffff;
}
.chat-bubble.staff {
  background: #f0fdf4;
  border-color: #bbf7d0;
  border-left: 4px solid #16a34a;
}
.chat-bubble.user {
  background: #f8fafc;
  border-left: 4px solid #3b82f6;
}
</style>

<div class="content-wrapper px-4 ticket-view-wrap">
  
  <!-- Flash Notification Messages -->
  <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
  <?php echo get_message($this->session->flashdata('message'),'message'); ?>

  <?php
    $st = strtolower(trim($ticket['status']));
    $pr = strtolower(trim($ticket['priority']));
    $today_str = date('Y-m-d');
    $is_overdue = (!empty($ticket['deadline_date']) && $ticket['deadline_date'] < $today_str && !in_array($st, array('resolved', 'closed')));
    $is_due_soon = (!empty($ticket['deadline_date']) && $ticket['deadline_date'] >= $today_str && $ticket['deadline_date'] <= date('Y-m-d', strtotime('+2 days')) && !in_array($st, array('resolved', 'closed')));
  ?>

  <!-- Top Action Navigation Bar -->
  <div class="ticket-top-bar">
    <div>
      <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
        <h1 style="margin: 0; font-size: 20px; font-weight: 800; color: #0f172a;">
          <i class="fa fa-ticket text-primary"></i> TICKET #<?php echo htmlspecialchars($ticket['ticket_number']); ?>
        </h1>
        
        <!-- Status Badge -->
        <?php 
          $st_badge = 'label-default';
          if($st == 'open') $st_badge = 'label-primary';
          elseif($st == 'assigned') $st_badge = 'label-info" style="background: #6366f1;';
          elseif($st == 'in progress') $st_badge = 'label-warning" style="background: #d97706;';
          elseif($st == 'pending') $st_badge = 'label-info';
          elseif($st == 'on hold') $st_badge = 'label-default" style="background: #64748b;';
          elseif($st == 'resolved') $st_badge = 'label-success';
          elseif($st == 'closed') $st_badge = 'label-default" style="background: #1e293b;';
        ?>
        <span class="label <?php echo $st_badge; ?>" style="font-size: 13px; padding: 4px 10px; font-weight: 700;">
          <?php echo htmlspecialchars($ticket['status']); ?>
        </span>

        <!-- Overdue Alert Badge -->
        <?php if($is_overdue){ ?>
          <span class="label label-danger" style="background: #dc2626; font-size: 12px; font-weight: 800; padding: 4px 10px;">
            <i class="fa fa-exclamation-triangle"></i> OVERDUE
          </span>
        <?php } elseif($is_due_soon){ ?>
          <span class="label label-warning" style="background: #f59e0b; color: #000; font-size: 12px; font-weight: 700; padding: 4px 10px;">
            <i class="fa fa-clock-o"></i> Approaching Deadline
          </span>
        <?php } ?>
      </div>
      <div style="font-size: 13.5px; font-weight: 600; color: #475569; margin-top: 4px;">
        <?php echo htmlspecialchars(!empty($ticket['ticket_title']) ? $ticket['ticket_title'] : $ticket['subject']); ?>
      </div>
    </div>

    <!-- Quick Navigation / Edit Buttons -->
    <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
      <a href="<?php echo base_url(); ?>manager/tickets" class="btn btn-default btn-sm">
        <i class="fa fa-arrow-left"></i> All Tickets
      </a>
      <a href="<?php echo base_url(); ?>manager/tickets/edit/<?php echo $ticket['id']; ?>" class="btn btn-info btn-sm">
        <i class="fa fa-pencil"></i> Edit Ticket
      </a>
      <a href="#followup-section" class="btn btn-success btn-sm">
        <i class="fa fa-calendar-plus-o"></i> Add Follow-up
      </a>
      <?php if ($st != 'closed') { ?>
        <a href="#review_close_section" class="btn btn-danger btn-sm">
          <i class="fa fa-check-square-o"></i> Review & Close
        </a>
      <?php } ?>
    </div>
  </div>

  <div class="row">
    
    <!-- Left Column: Details, Follow-ups, Audit History, Conversation -->
    <div class="col-md-8 col-sm-12">
      
      <!-- Primary Ticket Information Card -->
      <div class="ticket-card">
        <div class="ticket-card-header">
          <span><i class="fa fa-file-text-o text-primary" style="margin-right: 6px;"></i> Ticket Overview & Description</span>
          <span style="font-size: 12px; color: #64748b; font-weight: normal;">
            Created: <?php echo date('d-M-Y h:i A', strtotime(!empty($ticket['created_date']) ? $ticket['created_date'] : $ticket['created_at'])); ?>
          </span>
        </div>
        <div class="ticket-card-body">
          <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 14px 16px; margin-bottom: 16px;">
            <h5 style="margin-top: 0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b;">
              <i class="fa fa-align-left"></i> Description:
            </h5>
            <div style="font-size: 13.5px; line-height: 1.6; color: #1e293b; white-space: pre-wrap;"><?php echo htmlspecialchars($ticket['description']); ?></div>
          </div>

          <!-- Internal Notes (If any) -->
          <?php if(!empty($ticket['internal_notes'])){ ?>
            <div style="background: #fffbeb; border: 1px solid #fef3c7; border-left: 4px solid #f59e0b; border-radius: 4px; padding: 12px 16px; margin-bottom: 16px;">
              <h5 style="margin-top: 0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #b45309;">
                <i class="fa fa-lock"></i> Internal Staff Notes (Confidential):
              </h5>
              <div style="font-size: 13px; color: #78350f; white-space: pre-wrap;"><?php echo htmlspecialchars($ticket['internal_notes']); ?></div>
            </div>
          <?php } ?>

          <!-- Attachment Download (If any) -->
          <?php if(!empty($ticket['attachment'])){ ?>
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 4px; padding: 10px 15px; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
              <div>
                <i class="fa fa-paperclip text-success" style="font-size: 16px; margin-right: 8px;"></i>
                <strong style="font-size: 13px; color: #166534;">Attached Document / File:</strong>
                <span style="font-size: 12px; color: #15803d; margin-left: 6px;"><?php echo htmlspecialchars($ticket['attachment']); ?></span>
              </div>
              <a href="<?php echo base_url(); ?>assets/admin/tickets/<?php echo htmlspecialchars($ticket['attachment']); ?>" target="_blank" class="btn btn-xs btn-success" download>
                <i class="fa fa-download"></i> Download Attachment
              </a>
            </div>
          <?php } ?>

          <!-- Resolution Remarks (If Resolved / Closed) -->
          <?php if(!empty($ticket['resolution_remarks'])){ ?>
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-left: 4px solid #2563eb; border-radius: 4px; padding: 12px 16px; margin-bottom: 16px;">
              <h5 style="margin-top: 0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #1e40af;">
                <i class="fa fa-check-circle"></i> Resolution Summary & Closing Remarks:
              </h5>
              <div style="font-size: 13px; color: #1e3a8a; white-space: pre-wrap;"><?php echo htmlspecialchars($ticket['resolution_remarks']); ?></div>
              <?php if(!empty($ticket['reviewer_name'])){ ?>
                <div style="margin-top: 8px; font-size: 11.5px; color: #3b82f6;">
                  <i class="fa fa-user-check"></i> Confirmed & Reviewed By: <strong><?php echo htmlspecialchars($ticket['reviewer_name']); ?> (Support)</strong> on <?php echo date('d-M-Y h:i A', strtotime($ticket['resolution_confirmed_at'])); ?>
                </div>
              <?php } ?>
            </div>
          <?php } ?>
        </div>
      </div>

      <!-- Multiple Follow-up History Section -->
      <div class="ticket-card" id="followup-section">
        <div class="ticket-card-header" style="background: #f0fdf4; border-bottom: 1px solid #bbf7d0;">
          <span style="color: #166534; font-weight: 700;">
            <i class="fa fa-calendar-check-o text-success" style="margin-right: 6px;"></i> Ticket Follow-up Entries (Multiple Entries Stored)
          </span>
          <span class="badge" style="background: #16a34a;"><?php echo count($followups); ?> Follow-ups</span>
        </div>
        <div class="ticket-card-body">
          
          <!-- Previous Follow-ups Table -->
          <?php if(!empty($followups)){ ?>
            <div class="table-responsive" style="margin-bottom: 20px;">
              <table class="table table-bordered table-striped" style="font-size: 12.5px; margin-bottom: 0;">
                <thead>
                  <tr style="background: #f8fafc;">
                    <th style="width: 100px;">Follow-up Date</th>
                    <th style="width: 100px;">Next Date</th>
                    <th style="width: 130px;">Assigned Staff</th>
                    <th>Remarks & Progress</th>
                    <th style="width: 100px;">Status</th>
                    <th style="width: 130px;">Next Action</th>
                    <th style="width: 110px;">Logged By</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($followups as $fu){ ?>
                    <tr>
                      <td><strong><?php echo date('d-M-Y', strtotime($fu['followup_date'])); ?></strong></td>
                      <td>
                        <?php if(!empty($fu['next_followup_date']) && $fu['next_followup_date'] != '0000-00-00'){ ?>
                          <span class="label label-info" style="font-size: 11px;">
                            <?php echo date('d-M-Y', strtotime($fu['next_followup_date'])); ?>
                          </span>
                        <?php } else { ?>
                          <span style="color: #94a3b8;">None</span>
                        <?php } ?>
                      </td>
                      <td>
                        <span style="font-weight: 600; color: #3730a3;">
                          <i class="fa fa-user"></i> <?php echo htmlspecialchars(!empty($fu['assigned_name']) ? $fu['assigned_name'] : 'Staff'); ?>
                        </span>
                      </td>
                      <td style="color: #1e293b;"><?php echo htmlspecialchars($fu['remarks']); ?></td>
                      <td>
                        <span class="label label-default" style="font-size: 10.5px;">
                          <?php echo htmlspecialchars($fu['status']); ?>
                        </span>
                      </td>
                      <td><?php echo htmlspecialchars(!empty($fu['next_action']) ? $fu['next_action'] : '-'); ?></td>
                      <td style="font-size: 11px; color: #64748b;">
                        <?php echo htmlspecialchars(!empty($fu['creator_name']) ? $fu['creator_name'] : 'User'); ?><br>
                        <?php echo date('d-M-y H:i', strtotime($fu['created_at'])); ?>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          <?php } else { ?>
            <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 4px; padding: 18px; text-align: center; color: #64748b; margin-bottom: 20px;">
              <i class="fa fa-calendar-times-o" style="font-size: 24px; color: #94a3b8; display: block; margin-bottom: 6px;"></i>
              No follow-up entries recorded yet. Add your first follow-up below.
            </div>
          <?php } ?>

          <!-- Add Follow-up Form -->
          <div style="background: #f8fafc; border: 1px solid #d2d6de; border-radius: 4px; padding: 16px;">
            <h4 style="margin-top: 0; font-size: 13.5px; font-weight: 700; color: #1e293b; margin-bottom: 14px;">
              <i class="fa fa-plus-circle text-success"></i> Add New Follow-up Entry
            </h4>
            <form action="<?php echo base_url(); ?>manager/tickets/add_followup" method="post">
              <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">

              <div class="row">
                <div class="col-md-3 col-sm-6 form-group">
                  <label style="font-size: 12px; font-weight: 600;">Follow-up Date *</label>
                  <input type="text" name="followup_date" class="form-control input-sm datepicker" value="<?php echo date('Y-m-d'); ?>" required autocomplete="off">
                </div>
                <div class="col-md-3 col-sm-6 form-group">
                  <label style="font-size: 12px; font-weight: 600;">Next Follow-up Date</label>
                  <input type="text" name="next_followup_date" class="form-control input-sm datepicker" placeholder="YYYY-MM-DD" autocomplete="off">
                </div>
                <div class="col-md-3 col-sm-6 form-group">
                  <label style="font-size: 12px; font-weight: 600;">Assigned User</label>
                  <select name="assigned_user_id" class="form-control input-sm select2" style="width: 100%;">
                    <?php if(!empty($users_list)){
                      foreach($users_list as $u){ ?>
                        <option value="<?php echo $u['id']; ?>" <?php if($u['id'] == $this->session->userdata('manager_id')) echo 'selected'; ?>>
                          <?php echo htmlspecialchars($u['name']); ?>
                        </option>
                    <?php } } ?>
                  </select>
                </div>
                <div class="col-md-3 col-sm-6 form-group">
                  <label style="font-size: 12px; font-weight: 600;">Status</label>
                  <select name="status" class="form-control input-sm select2" style="width: 100%;">
                    <?php 
                    $stat_opts = array('Open', 'Assigned', 'In Progress', 'Pending', 'On Hold', 'Resolved');
                    if (!empty($can_manage_tickets)) $stat_opts[] = 'Closed';
                    foreach($stat_opts as $sopt){ ?>
                      <option value="<?php echo $sopt; ?>" <?php if($sopt == $ticket['status']) echo 'selected'; ?>><?php echo $sopt; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <div class="row">
                <div class="col-md-8 col-sm-12 form-group">
                  <label style="font-size: 12px; font-weight: 600;">Follow-up Remarks / Progress Notes *</label>
                  <textarea name="remarks" class="form-control input-sm" rows="3" placeholder="Enter detailed follow-up remarks..." required></textarea>
                </div>
                <div class="col-md-4 col-sm-12 form-group">
                  <label style="font-size: 12px; font-weight: 600;">Next Action</label>
                  <input type="text" name="next_action" class="form-control input-sm" placeholder="e.g., Call client, test fix">
                  <div style="margin-top: 18px;">
                    <button type="submit" class="btn btn-success btn-sm btn-block" style="font-weight: 700;">
                      <i class="fa fa-save"></i> Save Follow-up
                    </button>
                  </div>
                </div>
              </div>
            </form>
          </div>

        </div>
      </div>

      <!-- Complete Ticket History & Audit Log Section -->
      <div class="ticket-card">
        <div class="ticket-card-header" style="background: #f8fafc;">
          <span><i class="fa fa-history text-primary" style="margin-right: 6px;"></i> Complete Ticket History & Audit Trail</span>
          <span class="badge" style="background: #3b82f6;"><?php echo count($history); ?> Events</span>
        </div>
        <div class="ticket-card-body" style="max-height: 400px; overflow-y: auto;">
          <?php if(!empty($history)){ ?>
            <div class="audit-timeline">
              <?php foreach($history as $h){ ?>
                <div class="audit-item">
                  <div class="audit-meta">
                    <i class="fa fa-clock-o"></i> <?php echo date('d-M-Y h:i:s A', strtotime($h['created_at'])); ?> &bull; 
                    <strong><?php echo htmlspecialchars($h['user_name']); ?></strong> (<?php echo htmlspecialchars($h['user_role']); ?>)
                  </div>
                  <div class="audit-title">
                    <?php echo htmlspecialchars($h['action']); ?>
                    <?php if(!empty($h['new_status'])){ ?>
                      <span class="label label-info" style="font-size: 10px; margin-left: 6px;">
                        <?php echo htmlspecialchars($h['old_status']); ?> &rarr; <?php echo htmlspecialchars($h['new_status']); ?>
                      </span>
                    <?php } ?>
                  </div>
                  <?php if(!empty($h['remarks'])){ ?>
                    <div class="audit-remarks"><?php echo htmlspecialchars($h['remarks']); ?></div>
                  <?php } ?>
                </div>
              <?php } ?>
            </div>
          <?php } else { ?>
            <div style="color: #64748b; font-size: 13px; text-align: center; padding: 15px;">
              No prior history recorded yet.
            </div>
          <?php } ?>
        </div>
      </div>

      <!-- Conversation Replies & Chat Thread -->
      <div class="ticket-card">
        <div class="ticket-card-header">
          <span><i class="fa fa-comments text-primary" style="margin-right: 6px;"></i> Ticket Conversation & Messages</span>
          <span class="badge" style="background: #64748b;"><?php echo count($messages); ?> Messages</span>
        </div>
        <div class="ticket-card-body">
          <div style="margin-bottom: 20px;">
            <?php if(!empty($messages)){ 
              foreach($messages as $msg){ 
                $is_staff_msg = ($msg['sender_role'] == 1 || $msg['sender_role'] == 4 || strpos($msg['sender_name'], '(Support') !== false);
            ?>
              <div class="chat-bubble <?php echo $is_staff_msg ? 'staff' : 'user'; ?>">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(0,0,0,0.06); padding-bottom: 6px; margin-bottom: 8px; font-size: 11.5px;">
                  <strong style="color: <?php echo $is_staff_msg ? '#15803d' : '#1d4ed8'; ?>;">
                    <i class="fa <?php echo $is_staff_msg ? 'fa-shield' : 'fa-user'; ?>"></i> <?php echo htmlspecialchars($msg['sender_name']); ?>
                  </strong>
                  <span style="color: #64748b;"><?php echo date('d-M-Y h:i A', strtotime($msg['created_at'])); ?></span>
                </div>
                <div style="font-size: 13px; color: #1e293b; white-space: pre-wrap;"><?php echo htmlspecialchars($msg['message']); ?></div>
              </div>
            <?php } } else { ?>
              <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 12px; color: #64748b; font-size: 13px;">
                <em>No conversation replies yet. Post a response below.</em>
              </div>
            <?php } ?>
          </div>

          <!-- Post Message / Reply Box -->
          <div style="background: #f8fafc; border: 1px solid #d2d6de; border-radius: 4px; padding: 15px;">
            <h4 style="margin-top: 0; font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 10px;">
              <i class="fa fa-reply text-primary"></i> Post Message / Response
            </h4>
            <form action="<?php echo base_url(); ?>manager/tickets/respond" method="post">
              <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
              <div class="form-group">
                <textarea name="response" class="form-control" rows="3" placeholder="Type your reply or response here..." required></textarea>
              </div>
              <div class="text-right">
                <button type="submit" class="btn btn-primary btn-sm" style="font-weight: 600;">
                  <i class="fa fa-paper-plane"></i> Send Reply
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>

    <!-- Right Column: Meta Information, Assignees, Deadlines, Status Updates & Review to Close -->
    <div class="col-md-4 col-sm-12">
      
      <!-- Ticket Key Details Card -->
      <div class="ticket-card">
        <div class="ticket-card-header">
          <span><i class="fa fa-info-circle text-primary" style="margin-right: 6px;"></i> Ticket Specifications</span>
        </div>
        <div class="ticket-card-body" style="padding: 0;">
          <table class="table meta-table" style="margin-bottom: 0;">
            <tr>
              <td>Ticket Number:</td>
              <td><strong style="color: #1e40af;"><?php echo htmlspecialchars($ticket['ticket_number']); ?></strong></td>
            </tr>
            <tr>
              <td>Client Type:</td>
              <td>
                <?php 
                $ctype = !empty($ticket['client_type']) ? $ticket['client_type'] : 'Onboarded';
                $cbadge = ($ctype === 'Onboarded') ? 'label-success' : 'label-warning';
                ?>
                <span class="label <?php echo $cbadge; ?>"><?php echo htmlspecialchars($ctype); ?></span>
              </td>
            </tr>
            <tr>
              <td>Company Name:</td>
              <td>
                <strong style="color: #111;">
                  <?php echo htmlspecialchars(!empty($ticket['company_name']) ? $ticket['company_name'] : (!empty($ticket['customer']) ? $ticket['customer'] : '-')); ?>
                </strong>
              </td>
            </tr>
            <tr>
              <td>Department:</td>
              <td><span class="label label-default" style="background: #e2e8f0; color: #334155;"><?php echo htmlspecialchars(!empty($ticket['department']) ? $ticket['department'] : 'General'); ?></span></td>
            </tr>
            <tr>
              <td>Ticket Type:</td>
              <td><?php echo htmlspecialchars(!empty($ticket['ticket_type']) ? $ticket['ticket_type'] : 'Technical Issue'); ?></td>
            </tr>
            <tr>
              <td>Category:</td>
              <td><?php echo htmlspecialchars(!empty($ticket['category']) ? $ticket['category'] : 'Other'); ?></td>
            </tr>
            <tr>
              <td>Priority:</td>
              <td>
                <span class="label <?php echo ($pr == 'urgent' || $pr == 'high') ? 'label-danger' : (($pr == 'medium') ? 'label-warning' : 'label-info'); ?>" style="font-weight: 700;">
                  <?php echo htmlspecialchars($ticket['priority']); ?>
                </span>
              </td>
            </tr>
            <tr>
              <td>Raised By:</td>
              <td>
                <strong><?php echo htmlspecialchars(!empty($ticket['creator_name']) ? $ticket['creator_name'] : 'User #'.$ticket['created_by']); ?></strong>
                <?php if(!empty($ticket['creator_email'])){ ?><br><small style="color: #64748b;"><?php echo htmlspecialchars($ticket['creator_email']); ?></small><?php } ?>
              </td>
            </tr>
            <tr>
              <td>Deadline Date:</td>
              <td>
                <?php if(!empty($ticket['deadline_date'])){ ?>
                  <strong style="color: <?php echo $is_overdue ? '#dc2626' : '#1e293b'; ?>;">
                    <i class="fa fa-calendar"></i> <?php echo date('d-M-Y', strtotime($ticket['deadline_date'])); ?>
                  </strong>
                  <?php if($is_overdue){ ?>
                    <span class="label label-danger" style="font-size: 10px; margin-left: 4px;">OVERDUE</span>
                  <?php } ?>
                <?php } else { ?>
                  <span style="color: #94a3b8;">None set</span>
                <?php } ?>
              </td>
            </tr>
            <tr>
              <td>Expected Closure:</td>
              <td>
                <?php if(!empty($ticket['expected_close_date'])){ ?>
                  <span><i class="fa fa-clock-o"></i> <?php echo date('d-M-Y', strtotime($ticket['expected_close_date'])); ?></span>
                <?php } else { ?>
                  <span style="color: #94a3b8;">Not specified</span>
                <?php } ?>
              </td>
            </tr>
            <tr>
              <td>Last Updated:</td>
              <td>
                <?php echo date('d-M-Y h:i A', strtotime(!empty($ticket['last_updated_date']) ? $ticket['last_updated_date'] : $ticket['updated_at'])); ?>
                <?php if(!empty($ticket['updater_name'])){ ?>
                  <br><small style="color: #64748b;">By: <?php echo htmlspecialchars($ticket['updater_name']); ?></small>
                <?php } ?>
              </td>
            </tr>
          </table>
        </div>
      </div>

      <!-- Multiple Assigned Users Card -->
      <div class="ticket-card">
        <div class="ticket-card-header">
          <span><i class="fa fa-users text-primary" style="margin-right: 6px;"></i> Assigned Staff (Multiple)</span>
          <a href="<?php echo base_url(); ?>manager/tickets/edit/<?php echo $ticket['id']; ?>" class="btn btn-xs btn-default">Manage</a>
        </div>
        <div class="ticket-card-body">
          <?php if(!empty($assigned_user_names)){ ?>
            <div style="display: flex; flex-direction: column; gap: 8px;">
              <?php foreach($assigned_user_names as $au){ ?>
                <div style="background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 4px; padding: 6px 12px; display: flex; align-items: center; justify-content: space-between;">
                  <div>
                    <i class="fa fa-user-circle text-primary" style="margin-right: 6px;"></i>
                    <strong style="color: #3730a3; font-size: 13px;"><?php echo htmlspecialchars($au['name']); ?></strong>
                  </div>
                  <span style="font-size: 11px; color: #4338ca;"><?php echo htmlspecialchars($au['email']); ?></span>
                </div>
              <?php } ?>
            </div>
          <?php } else { ?>
            <div style="color: #94a3b8; font-style: italic; font-size: 13px;">No users currently assigned to this ticket.</div>
          <?php } ?>

          <!-- Tagged / Notified Users -->
          <?php if(!empty($tagged_user_names)){ ?>
            <div style="margin-top: 14px; border-top: 1px solid #f1f5f9; padding-top: 10px;">
              <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 6px;">
                <i class="fa fa-bell-o text-warning"></i> Tagged / Notified Users:
              </div>
              <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                <?php foreach($tagged_user_names as $tu){ ?>
                  <span class="label label-default" style="background: #f1f5f9; color: #475569; font-size: 11px; padding: 3px 8px;">
                    @<?php echo htmlspecialchars($tu['name']); ?>
                  </span>
                <?php } ?>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>

      <!-- Quick Status Update Card for Assigned Users -->
      <div class="ticket-card">
        <div class="ticket-card-header" style="background: #fffbeb;">
          <span style="color: #92400e; font-weight: 700;">
            <i class="fa fa-refresh text-warning" style="margin-right: 6px;"></i> Update Progress / Status
          </span>
        </div>
        <div class="ticket-card-body">
          <form action="<?php echo base_url(); ?>manager/tickets/update_status" method="post">
            <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">

            <div class="form-group">
              <label style="font-size: 12px; font-weight: 600;">Status Option</label>
              <select name="status" class="form-control input-sm select2" style="width: 100%;">
                <option value="Open" <?php if($ticket['status'] == 'Open') echo 'selected'; ?>>Open</option>
                <option value="Assigned" <?php if($ticket['status'] == 'Assigned') echo 'selected'; ?>>Assigned</option>
                <option value="In Progress" <?php if($ticket['status'] == 'In Progress') echo 'selected'; ?>>In Progress</option>
                <option value="Pending" <?php if($ticket['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                <option value="On Hold" <?php if($ticket['status'] == 'On Hold') echo 'selected'; ?>>On Hold</option>
                <option value="Resolved" <?php if($ticket['status'] == 'Resolved') echo 'selected'; ?>>Resolved</option>
                <?php if(!empty($can_manage_tickets)){ ?>
                  <option value="Closed" <?php if($ticket['status'] == 'Closed') echo 'selected'; ?>>Closed (Support Review Required)</option>
                <?php } ?>
              </select>
              <small class="text-muted" style="display: block; margin-top: 4px;">
                * Only Support staff can permanently Close tickets after resolution review.
              </small>
            </div>

            <div class="form-group">
              <label style="font-size: 12px; font-weight: 600;">Progress Remarks / Notes</label>
              <textarea name="remarks" class="form-control input-sm" rows="2" placeholder="Describe the action or progress taken..."></textarea>
            </div>

            <button type="submit" class="btn btn-warning btn-sm btn-block" style="font-weight: 700; color: #000;">
              <i class="fa fa-refresh"></i> Record Progress / Update
            </button>
          </form>
        </div>
      </div>

      <!-- Support Role Resolution Review & Close Section -->
      <div class="ticket-card" id="review_close_section" style="border: 2px solid <?php echo !empty($can_manage_tickets) ? '#10b981' : '#cbd5e1'; ?>;">
        <div class="ticket-card-header" style="background: <?php echo !empty($can_manage_tickets) ? '#ecfdf5' : '#f8fafc'; ?>;">
          <span style="color: <?php echo !empty($can_manage_tickets) ? '#065f46' : '#475569'; ?>; font-weight: 800;">
            <i class="fa fa-shield"></i> Support Resolution Review & Closure Gate
          </span>
        </div>
        <div class="ticket-card-body">
          <?php if(!empty($can_manage_tickets)){ ?>
            <?php if($ticket['status'] === 'Closed'){ ?>
              <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 4px; padding: 12px; text-align: center;">
                <i class="fa fa-check-circle text-success" style="font-size: 24px; margin-bottom: 6px; display: block;"></i>
                <strong style="color: #166534;">This ticket is officially CLOSED.</strong>
                <div style="font-size: 12px; color: #15803d; margin-top: 4px;">
                  Reviewed and closed by <?php echo htmlspecialchars(!empty($ticket['reviewer_name']) ? $ticket['reviewer_name'] : 'Support Staff'); ?> on <?php echo date('d-M-Y h:i A', strtotime($ticket['resolution_confirmed_at'])); ?>.
                </div>
              </div>
            <?php } else { ?>
              <form action="<?php echo base_url(); ?>manager/tickets/update_status" method="post">
                <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                <input type="hidden" name="status" value="Closed">

                <div style="background: #fff7ed; border: 1px solid #fed7aa; border-radius: 4px; padding: 10px; font-size: 12px; color: #9a3412; margin-bottom: 12px;">
                  <i class="fa fa-exclamation-circle"></i> <strong>Support Verification Protocol:</strong>
                  You must thoroughly review resolution details with the assigned department and verify client satisfaction before closing this ticket.
                </div>

                <div class="form-group">
                  <label style="font-size: 12px; font-weight: 700; color: #1e293b;">Resolution & Closing Remarks *</label>
                  <textarea name="resolution_remarks" class="form-control input-sm" rows="3" placeholder="Provide final resolution notes, verification outcome, and closing comments..." required><?php echo htmlspecialchars($ticket['resolution_remarks'] ?? ''); ?></textarea>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                  <div class="checkbox" style="margin: 0;">
                    <label style="font-size: 12px; font-weight: 700; color: #047857;">
                      <input type="checkbox" name="confirm_review" value="1" required> 
                      I confirm that I have manually reviewed and verified the resolution of this ticket.
                    </label>
                  </div>
                </div>

                <button type="submit" class="btn btn-success btn-block" style="font-weight: 800; background: #059669; border-color: #047857;">
                  <i class="fa fa-check-circle"></i> Confirm Resolution & Close Ticket
                </button>
              </form>
            <?php } ?>
          <?php } else { ?>
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 14px; text-align: center; color: #64748b; font-size: 12.5px;">
              <i class="fa fa-lock" style="font-size: 22px; color: #94a3b8; display: block; margin-bottom: 6px;"></i>
              <strong>Restricted to Support Role</strong><br>
              Assigned users can record progress and mark tickets as <em>Resolved</em>, but only authorized users with the <strong>Support role</strong> (or Admin) can manually review and close tickets.
            </div>
          <?php } ?>
        </div>
      </div>

    </div>

  </div>

</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
