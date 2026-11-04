<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
  $return_to = trim((string)$this->input->get('return'));
  $allowed_returns = array('manager/orders/followups', 'manager/orders/open', 'manager/orders/my', 'manager/orders', 'manager/team/orders');
  if (!in_array($return_to, $allowed_returns, true)) {
    $return_to = 'manager/orders/followups';
  }
  $client_label = !empty($order['client_name']) ? $order['client_name'] : (!empty($order['lead_customer']) ? $order['lead_customer'] : 'Client #' . $order['client_id']);
  $status_options = array('Approved', 'In Progress', 'Follow Up', 'Under Review', 'Pending Sales Approval', 'Completed', 'Rejected', 'Lost', 'Cancelled');
  $current_status = (string)$order['status'];
?>

<div class="content-wrapper">
  <div class="container crm-page">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <form action="<?php echo base_url('manager/orders/add_followup/' . $order['id']); ?>" method="post" name="order_followup_form" class="crm-compact-form">
      <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
      <input type="hidden" name="return_to" value="<?php echo htmlspecialchars($return_to); ?>">

      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Add Follow-Up</h3>
          <a href="<?php echo base_url($return_to); ?>" class="btn btn-default btn-sm">Back</a>
        </div>
        <div class="box-body">
          <div class="crm-summary">
            <div><span>Order</span><strong><?php echo htmlspecialchars($order['order_no']); ?></strong></div>
            <div><span>Client</span><strong><?php echo htmlspecialchars($client_label); ?></strong></div>
            <div><span>Contact</span><strong><?php echo htmlspecialchars(trim((!empty($order['poc']) ? $order['poc'] . ' ' : '') . (!empty($order['contact1']) ? $order['contact1'] : '')) ?: '-'); ?></strong></div>
            <div><span>Current status</span><strong><?php echo htmlspecialchars($current_status); ?></strong></div>
          </div>

          <div class="row">
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
              <div class="form-group">
                <label for="ofDate">Next follow-up date *</label>
                <input type="text" id="ofDate" name="followup_date" class="form-control datepicker" required autocomplete="off" value="<?php echo !empty($order['followup_date']) ? date('d-m-Y', strtotime($order['followup_date'])) : date('d-m-Y'); ?>">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
              <div class="form-group">
                <label for="ofTime">Next follow-up time</label>
                <input type="text" id="ofTime" name="followup_time" class="form-control timepicker" autocomplete="off" value="<?php echo !empty($order['followup_time']) ? htmlspecialchars($order['followup_time']) : '11:00 AM'; ?>">
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
              <div class="form-group">
                <label for="ofStatus">Order status</label>
                <select id="ofStatus" name="order_status" class="form-control">
                  <?php foreach ($status_options as $opt) { ?>
                  <option value="<?php echo $opt; ?>" <?php echo ($current_status === $opt || ($opt === 'Follow Up' && $current_status === 'Open')) ? 'selected' : ''; ?>><?php echo $opt; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-xs-12">
              <div class="form-group">
                <label for="ofRemark">Follow-up remarks / operational instructions *</label>
                <textarea id="ofRemark" name="followup_remark" rows="3" class="form-control" required placeholder="Client conversation notes, commitments and next actions"></textarea>
              </div>
            </div>
          </div>

          <div class="crm-addl">
            <button type="button" class="crm-addl-toggle" aria-expanded="false" onclick="var p=this.parentNode;var o=!p.classList.contains('is-open');p.classList.toggle('is-open',o);this.setAttribute('aria-expanded',o?'true':'false');">Schedule a meeting<span class="crm-collapse-caret" aria-hidden="true"></span></button>
            <div class="crm-addl-body">
              <div class="row crm-addl-grid">
                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                  <div class="form-group">
                    <label for="ofMeetDate">Meeting date</label>
                    <input type="text" id="ofMeetDate" name="m_date" class="form-control datepicker" autocomplete="off">
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                  <div class="form-group">
                    <label for="ofMeetTime">Meeting time</label>
                    <input type="text" id="ofMeetTime" name="m_time" class="form-control" placeholder="e.g. 03:00 PM" autocomplete="off">
                  </div>
                </div>
                <div class="col-lg-6 col-md-4 col-xs-12">
                  <div class="form-group">
                    <label for="ofMeetRemark">Meeting location / remark</label>
                    <input type="text" id="ofMeetRemark" name="m_remark" class="form-control" placeholder="Client office or online meeting">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="box-footer crm-form-actions">
          <a href="<?php echo base_url($return_to); ?>" class="btn btn-default">Cancel</a>
          <button type="submit" class="btn btn-primary">Save follow-up</button>
        </div>
      </div>
    </form>

    <div class="box crm-plain-box">
      <div class="box-header with-border">
        <h3 class="box-title">Follow-up history</h3>
      </div>
      <div class="box-body" style="padding: 0;">
        <?php if (!empty($history)): ?>
          <div class="table-responsive">
            <table class="table table-bordered crm-table" style="margin-bottom: 0;">
              <thead>
                <tr>
                  <th style="width: 130px;">Date</th>
                  <th style="width: 100px;">Time</th>
                  <th style="width: 160px;">Result / Type</th>
                  <th>Remarks</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($history as $h): ?>
                  <tr>
                    <td><?php echo !empty($h['followup_date']) ? date('d-m-Y', strtotime($h['followup_date'])) : '-'; ?></td>
                    <td><?php echo htmlspecialchars($h['followup_time'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($h['followup_result'] ?? 'Follow-up'); ?></td>
                    <td style="white-space: normal; line-height: 1.45;"><?php echo nl2br(htmlspecialchars($h['followup_remark'] ?? '')); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div style="padding: 24px; text-align: center; color: #94a3b8; font-size: 13px;">No follow-ups recorded for this order yet.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
