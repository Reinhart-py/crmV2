<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
  <section class="content-header" style="padding: 15px 15px 5px 15px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
      <h1 style="font-size: 20px; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.02em;">
        <?php echo $info['page_heading']; ?>: <?php echo htmlspecialchars($order['order_no']); ?>
      </h1>
      <a href="<?php echo base_url('manager/orders/followups'); ?>" class="btn btn-default" style="border: 1px solid #cbd5e1; font-weight: 600; border-radius: 4px; padding: 6px 14px;">
        <i class="fa fa-arrow-left"></i> Back to Orders Follow-up
      </a>
    </div>
  </section>

  <section class="content" style="padding-top: 10px;">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <!-- 1. Order & Client Details Pill Box -->
    <div class="box" style="border: 1px solid #e2e8f0; border-radius: 4px; background: #ffffff; margin-bottom: 20px;">
      <div class="box-body" style="padding: 18px 20px;">
        <div class="row">
          <div class="col-md-3 col-sm-6">
            <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 2px;">Order Number</span>
            <span style="font-size: 14px; font-weight: 800; color: #0072f0;"><?php echo htmlspecialchars($order['order_no']); ?></span>
          </div>
          <div class="col-md-3 col-sm-6">
            <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 2px;">Company / Client</span>
            <span style="font-size: 14px; font-weight: 700; color: #0f172a;">
              <?php echo htmlspecialchars(!empty($order['client_name']) ? $order['client_name'] : (!empty($order['lead_customer']) ? $order['lead_customer'] : 'Client #' . $order['client_id'])); ?>
            </span>
          </div>
          <div class="col-md-3 col-sm-6">
            <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 2px;">Contact Person / Phone</span>
            <span style="font-size: 13px; font-weight: 600; color: #334155;">
              <?php echo htmlspecialchars(!empty($order['poc']) ? $order['poc'] . ' - ' : ''); ?><?php echo htmlspecialchars($order['contact1'] ?? '-'); ?>
            </span>
          </div>
          <div class="col-md-3 col-sm-6">
            <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 2px;">Current Status</span>
            <span class="label" style="background: #0072f0; color: #ffffff; font-weight: 700; font-size: 12px; padding: 4px 10px; border-radius: 3px;">
              <?php echo htmlspecialchars($order['status']); ?>
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- 2. Add Follow-Up Form -->
    <div class="box" style="border: 1px solid #e2e8f0; border-radius: 4px; background: #ffffff; margin-bottom: 20px;">
      <div class="box-header with-border" style="border-bottom: 1px solid #f1f5f9; padding: 14px 20px;">
        <h3 class="box-title" style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0;">
          <i class="fa fa-calendar-plus-o" style="color: #0072f0; margin-right: 6px;"></i> Schedule Follow-up / Update Status
        </h3>
      </div>
      <form action="<?php echo base_url('manager/orders/add_followup/' . $order['id']); ?>" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

        <div class="box-body" style="padding: 20px;">
          <div class="row">
            <div class="col-md-4 col-sm-6">
              <div class="form-group">
                <label style="font-weight: 700; font-size: 12px; color: #334155; margin-bottom: 4px;">Next Follow-up Date *</label>
                <div class="input-group">
                  <input type="text" name="followup_date" class="form-control datepicker" required value="<?php echo !empty($order['followup_date']) ? date('d-m-Y', strtotime($order['followup_date'])) : date('d-m-Y'); ?>" style="border: 1px solid #cbd5e1; border-radius: 4px 0 0 4px; height: 38px;">
                  <span class="input-group-addon" style="background: #f8fafc; border: 1px solid #cbd5e1; border-left: none; border-radius: 0 4px 4px 0;"><i class="fa fa-calendar"></i></span>
                </div>
              </div>
            </div>

            <div class="col-md-4 col-sm-6">
              <div class="form-group">
                <label style="font-weight: 700; font-size: 12px; color: #334155; margin-bottom: 4px;">Next Follow-up Time</label>
                <div class="input-group bootstrap-timepicker timepicker">
                  <input type="text" name="followup_time" class="form-control input-small timepicker" value="<?php echo !empty($order['followup_time']) ? htmlspecialchars($order['followup_time']) : '11:00 AM'; ?>" style="border: 1px solid #cbd5e1; border-radius: 4px 0 0 4px; height: 38px;">
                  <span class="input-group-addon" style="background: #f8fafc; border: 1px solid #cbd5e1; border-left: none; border-radius: 0 4px 4px 0;"><i class="fa fa-clock-o"></i></span>
                </div>
              </div>
            </div>

            <div class="col-md-4 col-sm-12">
              <div class="form-group">
                <label style="font-weight: 700; font-size: 12px; color: #334155; margin-bottom: 4px;">Order Status</label>
                <select name="order_status" class="form-control" style="border: 1px solid #cbd5e1; border-radius: 4px; height: 38px; font-weight: 600;">
                  <option value="Follow Up" <?php echo ($order['status'] === 'Follow Up' || $order['status'] === 'Open') ? 'selected' : ''; ?>>Follow Up</option>
                  <option value="In Progress" <?php echo ($order['status'] === 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                  <option value="Completed" <?php echo ($order['status'] === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                  <option value="Pending Sales Approval" <?php echo ($order['status'] === 'Pending Sales Approval') ? 'selected' : ''; ?>>Pending Sales Approval</option>
                  <option value="Cancelled" <?php echo ($order['status'] === 'Cancelled' || $order['status'] === 'Rejected') ? 'selected' : ''; ?>>Cancelled / Lost</option>
                </select>
              </div>
            </div>

            <div class="col-sm-12">
              <div class="form-group" style="margin-bottom: 0;">
                <label style="font-weight: 700; font-size: 12px; color: #334155; margin-bottom: 4px;">Follow-up Remarks / Operational Instructions *</label>
                <textarea name="followup_remark" rows="3" class="form-control" placeholder="Enter follow-up details, client conversation notes, or action items..." required style="border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px;"></textarea>
              </div>
            </div>
          </div>
        </div>

        <div class="box-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 14px 20px; display: flex; justify-content: flex-end; gap: 10px;">
          <a href="<?php echo base_url('manager/orders/followups'); ?>" class="btn btn-default" style="border: 1px solid #cbd5e1; border-radius: 4px; font-weight: 600; padding: 6px 18px;">Cancel</a>
          <button type="submit" class="btn btn-primary" style="background: #0072f0; border-color: #0072f0; border-radius: 4px; font-weight: 700; padding: 6px 22px;">
            <i class="fa fa-save" style="margin-right: 5px;"></i> Save Follow-up
          </button>
        </div>
      </form>
    </div>

    <!-- 3. Follow-up & Activity History -->
    <div class="box" style="border: 1px solid #e2e8f0; border-radius: 4px; background: #ffffff;">
      <div class="box-header with-border" style="border-bottom: 1px solid #f1f5f9; padding: 14px 20px;">
        <h3 class="box-title" style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0;">
          <i class="fa fa-history" style="color: #64748b; margin-right: 6px;"></i> Follow-up History &amp; Activity Log
        </h3>
      </div>
      <div class="box-body" style="padding: 0;">
        <?php if (!empty($history)): ?>
          <div class="table-responsive">
            <table class="table table-striped table-bordered" style="margin-bottom: 0;">
              <thead>
                <tr style="background: #f8fafc;">
                  <th style="font-weight: 700; width: 140px;">Follow-up Date</th>
                  <th style="font-weight: 700; width: 110px;">Time</th>
                  <th style="font-weight: 700; width: 130px;">Result / Type</th>
                  <th style="font-weight: 700;">Remarks / Notes</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($history as $h): ?>
                  <tr>
                    <td style="font-weight: 600;"><?php echo !empty($h['followup_date']) ? date('d-m-Y', strtotime($h['followup_date'])) : '-'; ?></td>
                    <td><?php echo htmlspecialchars($h['followup_time'] ?? '-'); ?></td>
                    <td><span class="label label-info" style="border-radius: 3px; font-weight: 600;"><?php echo htmlspecialchars($h['followup_result'] ?? 'Follow-up'); ?></span></td>
                    <td style="white-space: normal; line-height: 1.45;"><?php echo nl2br(htmlspecialchars($h['followup_remark'] ?? '')); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div style="padding: 30px; text-align: center; color: #94a3b8; font-size: 13px;">
            No past follow-ups recorded yet for this order.
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</div>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
