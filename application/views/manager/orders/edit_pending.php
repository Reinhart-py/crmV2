<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>

<div class="content-wrapper">
  <section class="content-header">
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <div>
        <h1 style="margin:0; font-size:20px; font-weight:700; color:#0f172a;">
          <i class="fa fa-hourglass-half" style="color:#2563eb; margin-right:8px;"></i>Process Lead-to-Order Conversion
        </h1>
      </div>
      <div>
        <a href="<?php echo base_url(); ?>manager/orders/pending_process" class="btn btn-default btn-sm" style="font-weight:600;">
          <i class="fa fa-arrow-left"></i> Back to Pending List
        </a>
      </div>
    </div>
  </section>

  <section class="content" style="padding-top:10px;">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="row">
      <div class="col-md-7">
        <div class="box box-default" style="border-radius:6px; border-top:3px solid #2563eb; margin-bottom:15px;">
          <div class="box-header with-border">
            <h3 class="box-title" style="font-size:15px; font-weight:700;">Customer &amp; Lead Information</h3>
          </div>
          <div class="box-body" style="padding:15px;">
            <table class="table table-bordered" style="margin-bottom:0;">
              <tbody>
                <tr>
                  <th style="width:30%; background:#f8fafc; font-size:12px;">Database Ref</th>
                  <td style="font-size:13px; font-weight:700; font-family:monospace; color:#0f172a;">
                    <?php echo htmlspecialchars($lead['db_ref_no'] ?: '-'); ?>
                  </td>
                </tr>
                <tr>
                  <th style="background:#f8fafc; font-size:12px;">Lead Ref</th>
                  <td style="font-size:13px; font-weight:700; font-family:monospace; color:#0072f0;">
                    <?php echo htmlspecialchars($lead['lead_ref_no'] ?: '-'); ?>
                  </td>
                </tr>
                <tr>
                  <th style="background:#f8fafc; font-size:12px;">Company / Client</th>
                  <td style="font-size:13px; font-weight:700;">
                    <?php echo htmlspecialchars($lead['customer'] ?: 'Unnamed'); ?>
                    <?php if (!empty($lead['company_name'])) { ?>
                      <div style="font-size:12px; font-weight:500; color:#64748b;"><?php echo htmlspecialchars($lead['company_name']); ?></div>
                    <?php } ?>
                  </td>
                </tr>
                <tr>
                  <th style="background:#f8fafc; font-size:12px;">Contact Person (POC)</th>
                  <td><?php echo htmlspecialchars($lead['poc'] ?: '-'); ?></td>
                </tr>
                <tr>
                  <th style="background:#f8fafc; font-size:12px;">Phone Numbers</th>
                  <td>
                    <?php echo htmlspecialchars($lead['contact1'] ?: '-'); ?>
                    <?php if (!empty($lead['contact2'])) { ?>
                      / <?php echo htmlspecialchars($lead['contact2']); ?>
                    <?php } ?>
                  </td>
                </tr>
                <tr>
                  <th style="background:#f8fafc; font-size:12px;">Email Address</th>
                  <td><?php echo htmlspecialchars($lead['email'] ?: '-'); ?></td>
                </tr>
                <tr>
                  <th style="background:#f8fafc; font-size:12px;">Service Vertical</th>
                  <td>
                    <span class="label" style="background:#e0e7ff; color:#3730a3; font-weight:600; padding:3px 7px; font-size:11px;">
                      <?php echo htmlspecialchars(!empty($lead['servicecat']) ? $lead['servicecat'] : 'General'); ?>
                    </span>
                  </td>
                </tr>
                <tr>
                  <th style="background:#f8fafc; font-size:12px;">Estimated MRR</th>
                  <td style="font-weight:700; color:#16a34a;">AED <?php echo number_format((float)($lead['mrr'] ?? 0), 2); ?></td>
                </tr>
                <tr>
                  <th style="background:#f8fafc; font-size:12px;">Requested By</th>
                  <td>
                    <strong><?php echo htmlspecialchars($lead['requested_by_name'] ?: 'Staff'); ?></strong>
                    <div style="font-size:11px; color:#64748b;"><?php echo !empty($lead['order_requested_at']) ? date('d M Y, h:i A', strtotime($lead['order_requested_at'])) : '-'; ?></div>
                  </td>
                </tr>
                <tr>
                  <th style="background:#f8fafc; font-size:12px;">Submitted Scope &amp; Remarks</th>
                  <td style="white-space:pre-wrap; background:#fffbeb; color:#92400e; font-size:12px;"><?php echo htmlspecialchars($lead['order_request_remarks'] ?: 'No notes provided by requester.'); ?></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="box box-default" style="border-radius:6px; margin-bottom:15px;">
          <div class="box-header with-border">
            <h3 class="box-title" style="font-size:15px; font-weight:700;">Remarks &amp; Activity Timeline</h3>
          </div>
          <div class="box-body" style="padding:15px; max-height:350px; overflow-y:auto;">
            <?php if (!empty($remarks_history)) { ?>
              <ul class="timeline" style="margin-bottom:0;">
                <?php foreach($remarks_history as $rem){ ?>
                  <li>
                    <i class="fa fa-comment bg-blue"></i>
                    <div class="timeline-item" style="border-radius:6px; box-shadow:none; border:1px solid #e2e8f0;">
                      <span class="time" style="font-size:11px; color:#64748b;"><i class="fa fa-clock-o"></i> <?php echo date('d M Y, h:i A', strtotime($rem['created'])); ?></span>
                      <h3 class="timeline-header" style="font-size:12px; font-weight:700; border-bottom:1px solid #f1f5f9; padding:6px 10px;">
                        <?php echo htmlspecialchars($rem['user_name'] ?? 'Staff'); ?>
                        <?php if (!empty($rem['status'])) { ?>
                          <span class="label label-default" style="font-size:10px; margin-left:6px;"><?php echo htmlspecialchars($rem['status']); ?></span>
                        <?php } ?>
                      </h3>
                      <div class="timeline-body" style="font-size:12px; padding:8px 10px; color:#334155;">
                        <?php echo nl2br(htmlspecialchars($rem['remark'] ?? '')); ?>
                      </div>
                    </div>
                  </li>
                <?php } ?>
                <li><i class="fa fa-clock-o bg-gray"></i></li>
              </ul>
            <?php } else { ?>
              <div style="text-align:center; padding:20px; color:#94a3b8; font-size:13px;">No past follow-up remarks recorded.</div>
            <?php } ?>
          </div>
          <div class="box-footer" style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:12px 15px;">
            <form action="<?php echo base_url(); ?>manager/orders/edit_pending/<?php echo $lead['id']; ?>" method="post">
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
              <input type="hidden" name="action_type" value="add_remark">
              <div class="form-group" style="margin-bottom:8px;">
                <label style="font-size:12px; font-weight:600;">Add Review Remark</label>
                <textarea name="new_remark" class="form-control" rows="2" placeholder="Enter remarks regarding this order process..." required></textarea>
              </div>
              <button type="submit" class="btn btn-default btn-sm" style="font-weight:600;">
                <i class="fa fa-comment-o"></i> Post Remark
              </button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-md-5">
        <div class="box box-default" style="border-radius:6px; border-top:3px solid #16a34a; margin-bottom:15px;">
          <div class="box-header with-border">
            <h3 class="box-title" style="font-size:15px; font-weight:700;">Process &amp; Create Order</h3>
          </div>
          <form action="<?php echo base_url(); ?>manager/orders/edit_pending/<?php echo $lead['id']; ?>" method="post">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="action_type" value="approve_order">
            <div class="box-body" style="padding:15px;">
              <div class="form-group">
                <label style="font-size:12px; font-weight:600;">Vertical *</label>
                <select name="vertical_code" class="form-control input-sm" required>
                  <option value="accounting" <?php echo (stripos($lead['servicecat'] ?? '', 'account') !== false) ? 'selected' : ''; ?>>Accounting / VAT</option>
                  <option value="telecom" <?php echo (stripos($lead['servicecat'] ?? '', 'telecom') !== false) ? 'selected' : ''; ?>>Telecom (Du / Etisalat)</option>
                  <option value="corporate" <?php echo (stripos($lead['servicecat'] ?? '', 'corporate') !== false) ? 'selected' : ''; ?>>Corporate Services</option>
                  <option value="it" <?php echo (stripos($lead['servicecat'] ?? '', 'it') !== false) ? 'selected' : ''; ?>>IT Services</option>
                  <option value="general">General</option>
                </select>
              </div>

              <div class="form-group">
                <label style="font-size:12px; font-weight:600;">Service Scope Title *</label>
                <input type="text" name="service_title" class="form-control input-sm" value="<?php echo htmlspecialchars(!empty($lead['servicecat']) ? $lead['servicecat'] : 'Standard Service Scope'); ?>" required>
              </div>

              <div class="form-group">
                <label style="font-size:12px; font-weight:600;">Service Description / Scope</label>
                <textarea name="service_scope" class="form-control input-sm" rows="3" placeholder="Detailed scope of service..."><?php echo htmlspecialchars($lead['order_request_remarks'] ?? ''); ?></textarea>
              </div>

              <div class="row">
                <div class="col-xs-6 form-group">
                  <label style="font-size:12px; font-weight:600;">Pricing Amount (AED) *</label>
                  <input type="number" step="0.01" min="0" name="pricing_amount" class="form-control input-sm" value="<?php echo htmlspecialchars($lead['mrr'] ?? '0.00'); ?>" required>
                </div>
                <div class="col-xs-6 form-group">
                  <label style="font-size:12px; font-weight:600;">Recurring MRR (AED)</label>
                  <input type="number" step="0.01" min="0" name="mrr" class="form-control input-sm" value="<?php echo htmlspecialchars($lead['mrr'] ?? '0.00'); ?>">
                </div>
              </div>

              <div class="row">
                <div class="col-xs-6 form-group">
                  <label style="font-size:12px; font-weight:600;">Assigned Department</label>
                  <select name="assigned_dept" class="form-control input-sm">
                    <option value="Back Office">Back Office</option>
                    <option value="Operations">Operations</option>
                    <option value="Finance">Finance</option>
                    <option value="IT">IT</option>
                  </select>
                </div>
                <div class="col-xs-6 form-group">
                  <label style="font-size:12px; font-weight:600;">Assigned Staff</label>
                  <select name="assigned_to" class="form-control input-sm">
                    <option value="0">Unassigned</option>
                    <?php 
                      $cur_assigned = !empty($lead['assigned_to']) ? (int)$lead['assigned_to'] : (!empty($lead['lead_assign']) ? (int)$lead['lead_assign'] : 0);
                      foreach($staff_list as $st){ 
                    ?>
                      <option value="<?php echo $st['id']; ?>" <?php echo ($cur_assigned === (int)$st['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($st['name']); ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label style="font-size:12px; font-weight:600;">Approval / Processing Notes</label>
                <textarea name="approval_notes" class="form-control input-sm" rows="2" placeholder="Optional notes for order history..."></textarea>
              </div>
            </div>
            <div class="box-footer" style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:12px 15px; display:flex; justify-content:space-between; align-items:center;">
              <button type="submit" class="btn btn-success" style="font-weight:700; padding:6px 16px;">
                <i class="fa fa-check-circle"></i> Approve &amp; Create Order
              </button>
              <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal" style="font-weight:600;">
                <i class="fa fa-times"></i> Reject Request
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width:440px;">
    <div class="modal-content" style="border-radius:12px; border:1px solid #e2e8f0; box-shadow:0 20px 40px rgba(0,0,0,0.12); overflow:hidden;">
      <form action="<?php echo base_url(); ?>manager/orders/edit_pending/<?php echo $lead['id']; ?>" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="action_type" value="reject_order">
        <div class="modal-header" style="background:#000000; color:#ffffff; padding:14px 18px; border-bottom:1px solid #1e293b;">
          <button type="button" class="close" data-dismiss="modal" style="color:#ffffff; opacity:0.8;">&times;</button>
          <h4 class="modal-title" style="font-size:15px; font-weight:700; color:#ffffff; margin:0;">Reject Order Request</h4>
        </div>
        <div class="modal-body" style="padding:20px;">
          <p style="font-size:13px; color:#475569; margin-bottom:14px;">Please specify the reason for rejecting this Lead-to-Order conversion request. The lead will be returned to normal state and unlocked.</p>
          <div class="form-group" style="margin-bottom:0;">
            <label style="font-size:12px; font-weight:600; color:#0f172a;">Rejection Reason *</label>
            <textarea name="reject_reason" class="form-control" rows="3" placeholder="Provide clear reason for rejection..." required style="border-radius:6px; resize:vertical;"></textarea>
          </div>
        </div>
        <div class="modal-footer" style="padding:12px 18px; background:#f8fafc; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:8px;">
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" style="font-weight:600; border-radius:6px;">Cancel</button>
          <button type="submit" class="btn btn-danger btn-sm" style="font-weight:600; border-radius:6px;">Confirm Rejection</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
