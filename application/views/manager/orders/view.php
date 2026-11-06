<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper crm-rec" data-rec="order">

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="container">
      <?php require(APPPATH . 'views/manager/orders/view_head.php'); ?>

      <div class="my-profile">
        <?php
        $ov_h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
        $ov_dt = function ($d, $f = 'd-m-Y') { return (!empty($d) && strpos((string)$d, '0000') !== 0) ? date($f, strtotime($d)) : ''; };
        $ov_fu = $ov_dt($order['followup_date']);
        if ($ov_fu !== '' && !empty($order['followup_time'])) $ov_fu .= ' ' . $order['followup_time'];
        $ov_groups = array(
          'Order' => array(
            array('Order No.', $order['order_no']),
            array('Sales No.', $order['sales_ref_no'] ?? ''),
            array('Database No.', $order['db_ref_no']),
            array('Lead No.', $order['lead_ref_no'], !empty($order['lead_id']) ? base_url() . 'manager/leads/view/' . base64_encode($order['lead_id']) : ''),
            array('Service', $order['service_title']),
            array('Vertical', $order['vertical_code'] ?: 'General'),
            array('MRR', 'AED ' . number_format((float)($order['mrr'] ?: $order['pricing_amount']), 2)),
            array('Contract value', (float)$order['pricing_amount'] > 0 ? 'AED ' . number_format((float)$order['pricing_amount'], 2) : ''),
          ),
          'Assignment & contact' => array(
            array('Assigned to', $order['staff_name']),
            array('Department', $order['assigned_dept']),
            array('Phone', $order['lead_contact']),
            array('Email', $order['lead_email']),
            array('Next follow-up', $ov_fu),
            array('Created', $ov_dt($order['created_at'], 'd-m-Y H:i')),
            array('Updated', $ov_dt($order['updated_at'], 'd-m-Y H:i')),
          ),
        );
        ?>
        <div class="box box-default">
          <div class="box-body svd">
            <?php foreach ($ov_groups as $gt => $fs) { ?>
            <div class="svd-g">
              <h4><?php echo $ov_h($gt); ?></h4>
              <div class="svd-grid">
                <?php foreach ($fs as $f) { $v = trim((string)$f[1]); ?>
                <div class="svd-f<?php echo $v === '' ? ' is-empty' : ''; ?>">
                  <span class="svd-l"><?php echo $ov_h($f[0]); ?></span>
                  <span class="svd-v"><?php if ($v === '') { echo '&mdash;'; } elseif (!empty($f[2])) { ?><a href="<?php echo $f[2]; ?>"><?php echo $ov_h($v); ?></a><?php } else { echo $ov_h($v); } ?></span>
                </div>
                <?php } ?>
              </div>
            </div>
            <?php } ?>
            <?php if (!empty($order['service_scope']) || !empty($order['operational_instructions'])) { ?>
            <div class="svd-g">
              <h4>Scope &amp; instructions</h4>
              <div class="svd-grid">
                <?php if (!empty($order['service_scope'])) { ?><div class="svd-f is-wide"><span class="svd-l">Scope of work</span><span class="svd-v" style="font-weight:500"><?php echo nl2br($ov_h($order['service_scope'])); ?></span></div><?php } ?>
                <?php if (!empty($order['operational_instructions'])) { ?><div class="svd-f is-wide"><span class="svd-l">Instructions</span><span class="svd-v" style="font-weight:500"><?php echo nl2br($ov_h($order['operational_instructions'])); ?></span></div><?php } ?>
              </div>
            </div>
            <?php } ?>
          </div>
        </div>

        <div class="box box-default rec-card">
          <div class="box-header"><h3 class="box-title">History</h3></div>
          <div class="box-body" style="padding:6px 14px 10px;">
            <?php if (!empty($activity_logs)) { ?>
              <ul class="ov-log">
                <?php foreach ($activity_logs as $log) { ?>
                  <li>
                    <span class="ov-when"><?php echo $ov_h($ov_dt($log['created_at'], 'd M Y, H:i')); ?></span>
                    <span class="ov-what"><b><?php echo $ov_h(ucwords(str_replace('_', ' ', $log['action']))); ?></b> · <?php echo $ov_h($log['action_by_name'] ?: 'User #' . $log['action_by']); ?>
                      <?php if (!empty($log['notes'])) { ?><small><?php echo nl2br($ov_h($log['notes'])); ?></small><?php } ?></span>
                  </li>
                <?php } ?>
              </ul>
            <?php } else { ?>
              <p class="text-muted" style="margin:6px 0;">No history yet.</p>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<style>
  .ov-log { list-style: none; margin: 0; padding: 0; }
  .ov-log li { display: flex; gap: 14px; padding: 7px 0; border-bottom: 1px solid var(--crm-line-soft, #eef2f7); font-size: 13px; }
  .ov-log li:last-child { border-bottom: 0; }
  .ov-when { flex: 0 0 130px; color: var(--crm-muted, #64748b); font-size: 12.5px; white-space: nowrap; }
  .ov-what { min-width: 0; color: var(--crm-ink, #0f172a); }
  .ov-what small { display: block; margin-top: 2px; font-size: 12.5px; color: var(--crm-ink-2, #475569); overflow-wrap: anywhere; }
  @media (max-width: 575px) { .ov-log li { flex-direction: column; gap: 2px; } .ov-when { flex-basis: auto; } }
</style>

<!-- Modals -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width: 480px;">
    <div class="modal-content" style="border-radius: 6px; overflow: hidden;">
      <form action="<?php echo base_url(); ?>manager/orders/update_status/<?php echo $order['id']; ?>" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="modal-header" style="background: #1e3a8a; color: #ffffff; padding: 14px 18px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #ffffff; opacity: 0.8;"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" style="font-size: 16px; font-weight: 700; color: #ffffff; margin: 0;">Update Order Status</h4>
        </div>
        <div class="modal-body" style="padding: 20px; background: #ffffff;">
          <div class="form-group">
            <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #475569;">Order Status</label>
            <select name="status" class="form-control" style="border: 1px solid #cbd5e1; border-radius: 4px;">
              <?php
              // A Sale can only be closed; Sales and Pending Sales Approval come from the Approve / Convert to Sales buttons
              $os_is_sale = ($order['status'] === 'Sales' || !empty($order['sales_ref_no']));
              $os_opts = $os_is_sale ? array('Completed', 'Fulfilled', 'Cancelled') : array('Open', 'In Progress', 'Follow Up', 'Under Review', 'Completed', 'Fulfilled', 'Cancelled', 'Rejected', 'Lost');
              if (!in_array($order['status'], $os_opts)) { ?><option value="<?php echo htmlspecialchars($order['status']); ?>" selected><?php echo htmlspecialchars($order['status']); ?> (current)</option><?php }
              foreach ($os_opts as $os_o) { ?>
              <option value="<?php echo $os_o; ?>" <?php echo ($order['status'] === $os_o) ? 'selected' : ''; ?>><?php echo $os_o; ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group" style="margin-bottom: 0;">
            <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #475569;">Remarks / Update Notes</label>
            <textarea name="remarks" class="form-control" rows="3" placeholder="Enter notes about this update..." style="border: 1px solid #cbd5e1; border-radius: 4px; resize: vertical;"></textarea>
          </div>
        </div>
        <div class="modal-footer" style="padding: 12px 18px; background: #f8fafc; border-top: 1px solid #e2e8f0;">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" style="background:#1e3a8a; border-color:#1e3a8a;">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="rejectOrderModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width: 480px;">
    <div class="modal-content" style="border-radius: 6px; overflow: hidden;">
      <form action="<?php echo base_url(); ?>manager/orders/reject/<?php echo $order['id']; ?>" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="modal-header" style="background: #dc2626; color: #ffffff; padding: 14px 18px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #ffffff; opacity: 0.8;"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" style="font-size: 16px; font-weight: 700; color: #ffffff; margin: 0;">Reject Order</h4>
        </div>
        <div class="modal-body" style="padding: 20px; background: #ffffff;">
          <p style="font-size: 13px; color: #475569; margin-bottom: 14px;">Please specify the reason for rejecting this order:</p>
          <div class="form-group" style="margin-bottom: 0;">
            <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #475569;">Rejection Reason *</label>
            <textarea name="remarks" class="form-control" rows="3" placeholder="Enter rejection reason..." required style="border: 1px solid #cbd5e1; border-radius: 4px; resize: vertical;"></textarea>
          </div>
        </div>
        <div class="modal-footer" style="padding: 12px 18px; background: #f8fafc; border-top: 1px solid #e2e8f0;">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger" style="background:#dc2626; border-color:#b91c1c;">Confirm Rejection</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="assignOperationModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width: 480px;">
    <div class="modal-content" style="border-radius: 6px; overflow: hidden;">
      <form action="<?php echo base_url(); ?>manager/orders/assign_to_operation/<?php echo $order['id']; ?>" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="modal-header" style="background: #1e3a8a; color: #ffffff; padding: 14px 18px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #ffffff; opacity: 0.8;"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" style="font-size: 16px; font-weight: 700; color: #ffffff; margin: 0;">Assign to Operations</h4>
        </div>
        <div class="modal-body" style="padding: 20px; background: #ffffff;">
          <div class="form-group">
            <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #475569;">Assign Staff Member</label>
            <select name="assigned_to" class="form-control" style="border: 1px solid #cbd5e1; border-radius: 4px;" required>
              <option value="">Select Staff...</option>
              <?php foreach($operations_staff as $stf){ ?>
                <option value="<?php echo $stf['id']; ?>" <?php echo ((int)$order['assigned_to'] === (int)$stf['id']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($stf['name'] . ' (' . ($stf['department'] ?: 'Staff') . ')'); ?>
                </option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group" style="margin-bottom: 0;">
            <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #475569;">Instructions</label>
            <textarea name="operational_instructions" class="form-control" rows="3" style="border: 1px solid #cbd5e1; border-radius: 4px; resize: vertical;"><?php echo htmlspecialchars((string)($order['operational_instructions'] ?? '')); ?></textarea>
          </div>
        </div>
        <div class="modal-footer" style="padding: 12px 18px; background: #f8fafc; border-top: 1px solid #e2e8f0;">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" style="background:#1e3a8a; border-color:#1e3a8a;">Assign</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php if (!empty($can_request_sales)) { ?>
<div class="modal fade" id="requestSalesModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width: 480px;">
    <div class="modal-content" style="border-radius: 6px; overflow: hidden;">
      <form action="<?php echo base_url(); ?>manager/orders/request_sales_approval/<?php echo (int)$order['id']; ?>" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="modal-header" style="background: #1e3a8a; color: #ffffff; padding: 14px 18px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #ffffff; opacity: 0.8;"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" style="font-size: 16px; font-weight: 700; color: #ffffff; margin: 0;">Request Sales Approval</h4>
        </div>
        <div class="modal-body" style="padding: 20px; background: #ffffff;">
          <div class="form-group" style="margin-bottom: 0;">
            <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #475569;">Note for KAM</label>
            <textarea name="remarks" class="form-control" rows="3" style="border: 1px solid #cbd5e1; border-radius: 4px; resize: vertical;"></textarea>
          </div>
        </div>
        <div class="modal-footer" style="padding: 12px 18px; background: #f8fafc; border-top: 1px solid #e2e8f0;">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" style="background:#1e3a8a; border-color:#1e3a8a;">Send to KAM</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php } ?>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
