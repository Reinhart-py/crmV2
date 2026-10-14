<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<style>
/* Strict Black & White Help Desk Chat Design */
.ticket-chat-container {
  background: #ffffff;
  color: #000000;
  padding: 15px 0 40px 0;
  font-family: inherit;
}
.ticket-chat-header {
  border-bottom: 2px solid #000000;
  padding-bottom: 12px;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
}
.ticket-chat-header h1 {
  font-size: 20px;
  font-weight: 800;
  color: #000000;
  margin: 0;
  letter-spacing: -0.02em;
}
.bw-card {
  background: #ffffff;
  border: 1px solid #000000;
  border-radius: 4px;
  margin-bottom: 20px;
}
.bw-card-header {
  background: #000000;
  color: #ffffff;
  padding: 10px 16px;
  font-size: 13.5px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.bw-card-body {
  padding: 18px;
  background: #ffffff;
}
.bw-badge {
  display: inline-block;
  padding: 4px 10px;
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

/* Chat timeline styling */
.chat-thread {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 24px;
}
.chat-bubble {
  border: 1px solid #000000;
  border-radius: 4px;
  padding: 14px 18px;
  background: #ffffff;
  position: relative;
}
.chat-bubble-user {
  background: #fbfbfb;
  border-left: 4px solid #000000;
}
.chat-bubble-staff {
  background: #ffffff;
  border-left: 4px solid #000000;
}
.chat-bubble-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #e5e5e5;
  padding-bottom: 8px;
  margin-bottom: 10px;
  font-size: 12px;
}
.chat-sender-name {
  font-weight: 800;
  color: #000000;
  text-transform: uppercase;
  letter-spacing: 0.3px;
}
.chat-time {
  color: #666666;
  font-size: 11px;
}
.chat-text {
  font-size: 13.5px;
  line-height: 1.65;
  color: #111111;
  white-space: pre-wrap;
  word-break: break-word;
}
.chat-reply-box {
  border: 1px solid #000000;
  border-radius: 4px;
  padding: 16px;
  background: #f8f9fa;
}
.btn-bw-primary {
  background: #000000 !important;
  color: #ffffff !important;
  border: 1px solid #000000 !important;
  font-weight: 700 !important;
  border-radius: 4px !important;
  padding: 8px 20px !important;
}
.btn-bw-primary:hover {
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
.btn-bw-outline:hover {
  background: #f1f1f1 !important;
  color: #000000 !important;
}

[data-theme="dark"] .ticket-chat-container {
  background: #0b0f17;
  color: #ffffff;
}
[data-theme="dark"] .ticket-chat-header {
  border-bottom-color: #ffffff;
}
[data-theme="dark"] .ticket-chat-header h1 {
  color: #ffffff;
}
[data-theme="dark"] .bw-card {
  background: #111827;
  border-color: #ffffff;
}
[data-theme="dark"] .bw-card-header {
  background: #ffffff;
  color: #000000;
}
[data-theme="dark"] .bw-card-body {
  background: #111827;
  color: #ffffff;
}
[data-theme="dark"] .chat-bubble {
  border-color: #ffffff;
  background: #111827;
}
[data-theme="dark"] .chat-bubble-user {
  background: #1f2937;
  border-left-color: #ffffff;
}
[data-theme="dark"] .chat-bubble-staff {
  background: #111827;
  border-left-color: #ffffff;
}
[data-theme="dark"] .chat-sender-name {
  color: #ffffff;
}
[data-theme="dark"] .chat-time {
  color: #9ca3af;
}
[data-theme="dark"] .chat-text {
  color: #f3f4f6;
}
[data-theme="dark"] .chat-reply-box {
  background: #1f2937;
  border-color: #ffffff;
}
</style>

<div class="container ticket-chat-container">
  
  <!-- Header Bar -->
  <div class="ticket-chat-header">
    <div>
      <h1>TICKET: <?php echo htmlspecialchars($ticket['ticket_number']); ?></h1>
      <div style="font-size: 12.5px; color: #555555; margin-top: 3px;">
        <?php echo htmlspecialchars($ticket['subject']); ?>
      </div>
    </div>
    <div style="display: flex; gap: 8px; align-items: center;">
      <a href="<?php echo base_url(); ?>manager/tickets" class="btn btn-bw-outline btn-sm">
        <i class="fa fa-arrow-left"></i> Back to Help Desk
      </a>

      <!-- Action Button Dropdown -->
      <div class="btn-group">
        <button type="button" class="btn btn-sm btn-warning">Action</button>
        <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right" role="menu" style="text-align: left; box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
          <li>
            <a href="#reply-box" onclick="setTimeout(function(){ var ta = document.querySelector('textarea[name=response]'); if(ta) ta.focus(); }, 100);">
              <i class="fa fa-reply" style="margin-right: 6px; color: #0284c7;"></i> Reply
            </a>
          </li>
          <li class="divider"></li>
          <?php if ($ticket['status'] != 'Resolved') { ?>
            <li>
              <a href="<?php echo base_url(); ?>manager/tickets/close/<?php echo $ticket['id']; ?>" onclick="return confirm('Clear and mark this ticket as Resolved?');">
                <i class="fa fa-check" style="margin-right: 6px; color: #16a34a;"></i> Clear / Mark Resolved
              </a>
            </li>
          <?php } ?>
          <?php if (!empty($can_manage_tickets)) { ?>
            <?php if ($ticket['status'] != 'In Progress') { ?>
              <li>
                <a href="<?php echo base_url(); ?>manager/tickets/status/<?php echo $ticket['id']; ?>/In%20Progress">
                  <i class="fa fa-clock-o" style="margin-right: 6px; color: #ea580c;"></i> Mark In Progress
                </a>
              </li>
            <?php } ?>
            <?php if ($ticket['status'] != 'Closed') { ?>
              <li>
                <a href="<?php echo base_url(); ?>manager/tickets/status/<?php echo $ticket['id']; ?>/Closed" onclick="return confirm('Mark this ticket as Closed?');">
                  <i class="fa fa-times-circle" style="margin-right: 6px; color: #64748b;"></i> Mark as Closed
                </a>
              </li>
            <?php } ?>
          <?php } ?>
          <?php if ($ticket['status'] == 'Resolved' || $ticket['status'] == 'Closed') { ?>
            <li>
              <a href="<?php echo base_url(); ?>manager/tickets/status/<?php echo $ticket['id']; ?>/Open">
                <i class="fa fa-refresh" style="margin-right: 6px; color: #2563eb;"></i> Re-Open Ticket
              </a>
            </li>
          <?php } ?>
          <?php if (!empty($is_admin)) { ?>
            <li class="divider"></li>
            <li>
              <a href="<?php echo base_url(); ?>manager/tickets/delete/<?php echo $ticket['id']; ?>" onclick="return confirm('Permanently delete ticket #<?php echo $ticket['ticket_number']; ?> and all chat history?');" style="color: #dc2626;">
                <i class="fa fa-trash" style="margin-right: 6px;"></i> Delete Ticket
              </a>
            </li>
          <?php } ?>
        </ul>
      </div>
    </div>
  </div>

  <!-- Flash Messages -->
  <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
  <?php echo get_message($this->session->flashdata('message'),'message'); ?>

  <div class="row">
    
    <!-- Left Column: Chat Conversation Thread -->
    <div class="col-md-8 col-sm-12">
      <div class="bw-card">
        <div class="bw-card-header">
          <span><i class="fa fa-comments" style="margin-right: 6px;"></i> CONVERSATION THREAD & REPLIES</span>
          <span>
            <?php
              $badge_class = 'bw-badge-open';
              if ($ticket['status'] == 'In Progress') $badge_class = 'bw-badge-progress';
              if ($ticket['status'] == 'Resolved') $badge_class = 'bw-badge-resolved';
              if ($ticket['status'] == 'Closed') $badge_class = 'bw-badge-closed';
            ?>
            <span class="bw-badge <?php echo $badge_class; ?>">
              Status: <?php echo htmlspecialchars($ticket['status']); ?>
            </span>
          </span>
        </div>
        <div class="bw-card-body">
          
          <!-- Chat Message Feed -->
          <div class="chat-thread">
            <?php if (!empty($messages)) { 
              foreach ($messages as $msg) {
                $is_staff = ($msg['sender_role'] == 1 || (isset($msg['sender_role']) && $msg['sender_role'] == 4));
            ?>
              <div class="chat-bubble <?php echo $is_staff ? 'chat-bubble-staff' : 'chat-bubble-user'; ?>">
                <div class="chat-bubble-meta">
                  <span class="chat-sender-name">
                    <i class="fa <?php echo $is_staff ? 'fa-shield' : 'fa-user'; ?>" style="margin-right: 4px;"></i>
                    <?php echo htmlspecialchars($msg['sender_name']); ?>
                  </span>
                  <span class="chat-time">
                    <?php echo date('d-M-Y h:i A', strtotime($msg['created_at'])); ?>
                  </span>
                </div>
                <div class="chat-text"><?php echo htmlspecialchars($msg['message']); ?></div>
              </div>
            <?php } } else { ?>
              <!-- Fallback to Initial Description -->
              <div class="chat-bubble chat-bubble-user">
                <div class="chat-bubble-meta">
                  <span class="chat-sender-name">
                    <i class="fa fa-user" style="margin-right: 4px;"></i>
                    <?php echo !empty($ticket['creator_name']) ? htmlspecialchars($ticket['creator_name']) : 'User'; ?> (Ticket Creator)
                  </span>
                  <span class="chat-time">
                    <?php echo date('d-M-Y h:i A', strtotime($ticket['created_at'])); ?>
                  </span>
                </div>
                <div class="chat-text"><?php echo htmlspecialchars($ticket['description']); ?></div>
              </div>
            <?php } ?>
          </div>

          <!-- Chat Reply Box -->
          <div class="chat-reply-box" id="reply-box">
            <h4 style="font-size: 13px; font-weight: 700; text-transform: uppercase; margin-top: 0; margin-bottom: 12px; color: #000000;">
              <i class="fa fa-reply" style="margin-right: 5px;"></i> Send Reply / Follow-up
            </h4>
            <form action="<?php echo base_url(); ?>manager/tickets/respond" method="post">
              <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">

              <div class="form-group" style="margin-bottom: 12px;">
                <textarea name="response" class="form-control" rows="4" placeholder="Write your follow-up message or response..." required style="border: 1px solid #000000; border-radius: 3px; font-size: 13px;"></textarea>
              </div>

              <div class="row" style="align-items: center;">
                <?php if (!empty($can_manage_tickets)) { ?>
                  <div class="col-sm-6">
                    <div class="form-group" style="margin-bottom: 0;">
                      <label style="font-size: 11px; font-weight: 700; text-transform: uppercase;">Update Ticket Status:</label>
                      <select name="status" class="form-control" style="border: 1px solid #000000; border-radius: 3px; font-size: 12px; height: 36px;">
                        <option value="Open" <?php if($ticket['status']=='Open') echo 'selected'; ?>>Open (Pending)</option>
                        <option value="In Progress" <?php if($ticket['status']=='In Progress') echo 'selected'; ?>>In Progress (Working on issue)</option>
                        <option value="Resolved" <?php if($ticket['status']=='Resolved') echo 'selected'; ?>>Resolved (Issue Fixed)</option>
                        <option value="Closed" <?php if($ticket['status']=='Closed') echo 'selected'; ?>>Closed</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-6 text-right" style="padding-top: 18px;">
                    <button type="submit" class="btn btn-bw-primary">
                      <i class="fa fa-paper-plane" style="margin-right: 4px;"></i> Post Response
                    </button>
                  </div>
                <?php } else { ?>
                  <div class="col-sm-12 text-right">
                    <button type="submit" class="btn btn-bw-primary">
                      <i class="fa fa-paper-plane" style="margin-right: 4px;"></i> Send Follow-up
                    </button>
                  </div>
                <?php } ?>
              </div>
            </form>
          </div>

        </div>
      </div>
    </div>

    <!-- Right Column: Ticket Meta Details (Black & White) -->
    <div class="col-md-4 col-sm-12">
      <div class="bw-card">
        <div class="bw-card-header">
          <span><i class="fa fa-info-circle" style="margin-right: 6px;"></i> TICKET DETAILS</span>
        </div>
        <div class="bw-card-body" style="padding: 16px;">
          <table class="table" style="margin-bottom: 0; font-size: 12.5px;">
            <tr>
              <td style="font-weight: 700; border-top: none; width: 45%;">Ticket Number:</td>
              <td style="font-weight: 800; border-top: none;"><?php echo htmlspecialchars($ticket['ticket_number']); ?></td>
            </tr>
            <tr>
              <td style="font-weight: 700;">Status:</td>
              <td>
                <span class="bw-badge <?php echo $badge_class; ?>">
                  <?php echo htmlspecialchars($ticket['status']); ?>
                </span>
              </td>
            </tr>
            <tr>
              <td style="font-weight: 700;">Priority:</td>
              <td style="font-weight: 700;"><?php echo htmlspecialchars($ticket['priority']); ?></td>
            </tr>
            <tr>
              <td style="font-weight: 700;">Category:</td>
              <td><?php echo htmlspecialchars($ticket['category']); ?></td>
            </tr>
            <tr>
              <td style="font-weight: 700;">Raised By:</td>
              <td><?php echo !empty($ticket['creator_name']) ? htmlspecialchars($ticket['creator_name']) : 'User #'.$ticket['created_by']; ?></td>
            </tr>
            <tr>
              <td style="font-weight: 700;">Email:</td>
              <td><small><?php echo htmlspecialchars($ticket['creator_email'] ?? 'N/A'); ?></small></td>
            </tr>
            <tr>
              <td style="font-weight: 700;">Created Time:</td>
              <td><?php echo date('d-M-Y h:i A', strtotime($ticket['created_at'])); ?></td>
            </tr>
            <tr>
              <td style="font-weight: 700;">Last Update:</td>
              <td><?php echo date('d-M-Y h:i A', strtotime($ticket['updated_at'])); ?></td>
            </tr>
            <?php if (!empty($ticket['resolved_at']) && $ticket['resolved_at'] != '0000-00-00 00:00:00') { ?>
              <tr>
                <td style="font-weight: 700; color: #000000;">Resolved At:</td>
                <td style="font-weight: 700;"><?php echo date('d-M-Y h:i A', strtotime($ticket['resolved_at'])); ?></td>
              </tr>
              <tr>
                <td style="font-weight: 700;">Resolved By:</td>
                <td><?php echo !empty($ticket['resolver_name']) ? htmlspecialchars($ticket['resolver_name']) : 'Support Team'; ?></td>
              </tr>
            <?php } ?>
          </table>

          <div style="margin-top: 18px;">
            <a href="<?php echo base_url(); ?>manager/tickets" class="btn btn-bw-outline btn-block" style="text-align: center;">
              <i class="fa fa-list"></i> View All Tickets
            </a>
          </div>
        </div>
      </div>
    </div>

  </div>

</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
