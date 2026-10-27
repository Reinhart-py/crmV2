<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>

<div class="content-wrapper">
  <section class="content-header" style="padding:15px 15px 5px 15px;">
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <div>
        <h1 style="margin:0; font-size:22px; font-weight:800; color:#000000; letter-spacing:-0.02em;">
          <i class="fa fa-hourglass-half" style="color:#000000; margin-right:8px;"></i>Process Lead-to-Order Conversion
        </h1>
      </div>
      <div>
        <a href="<?php echo base_url(); ?>manager/orders/pending_process" class="btn btn-default" style="font-weight:600; background:#ffffff; color:#000000; border:1px solid #000000;">
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
        <div class="box box-default" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff; margin-bottom:15px;">
          <div class="box-header" style="background:#f8fafc; border-bottom:1px solid #e2e8f0; padding:12px 18px;">
            <h3 class="box-title" style="font-size:13px; font-weight:700; color:#000000; text-transform:uppercase; letter-spacing:0.04em;">Customer &amp; Lead Information</h3>
          </div>
          <div class="box-body" style="padding:18px;">
            <table class="table table-bordered" style="margin-bottom:0; font-size:13px;">
              <tbody>
                <tr>
                  <th style="width:30%; background:#f8fafc; font-size:11px; text-transform:uppercase; color:#475569; font-weight:700;">Database Ref</th>
                  <td style="font-size:13px; font-weight:700; font-family:monospace; color:#000000;">
                    <?php echo htmlspecialchars($lead['db_ref_no'] ?: '-'); ?>
                  </td>
                </tr>
                <tr>
                  <th style="background:#f8fafc; font-size:11px; text-transform:uppercase; color:#475569; font-weight:700;">Lead Ref</th>
                  <td style="font-size:13px; font-weight:700; font-family:monospace; color:#000000;">
                    <?php echo htmlspecialchars($lead['lead_ref_no'] ?: '-'); ?>
                  </td>
                </tr>
                <tr>
                  <th style="background:#f8fafc; font-size:11px; text-transform:uppercase; color:#475569; font-weight:700;">Company / Client</th>
                  <td style="font-size:14px; font-weight:700; color:#000000;">
                    <?php echo htmlspecialchars($lead['customer'] ?: 'Unnamed'); ?>
                    <?php if (!empty($lead['company_name'])) { ?>
                      <div style="font-size:12px; font-weight:500; color:#64748b;"><?php echo htmlspecialchars($lead['company_name']); ?></div>
                    <?php } ?>
                  </td>
                </tr>
                <tr>
                  <th style="background:#f8fafc; font-size:11px; text-transform:uppercase; color:#475569; font-weight:700;">Contact Person (POC)</th>
                  <td style="color:#0f172a; font-weight:600;"><?php echo htmlspecialchars($lead['poc'] ?: '-'); ?></td>
                </tr>
                <tr>
                  <th style="background:#f8fafc; font-size:11px; text-transform:uppercase; color:#475569; font-weight:700;">Phone Numbers</th>
                  <td style="color:#0f172a; font-weight:600;">
                    <?php echo htmlspecialchars($lead['contact1'] ?: '-'); ?>
                    <?php if (!empty($lead['contact2'])) { ?>
                      / <?php echo htmlspecialchars($lead['contact2']); ?>
                    <?php } ?>
                  </td>
                </tr>
                <tr>
                  <th style="background:#f8fafc; font-size:11px; text-transform:uppercase; color:#475569; font-weight:700;">Email Address</th>
                  <td style="color:#0f172a; font-weight:600;"><?php echo htmlspecialchars($lead['email'] ?: '-'); ?></td>
                </tr>
                <tr>
                  <th style="background:#f8fafc; font-size:11px; text-transform:uppercase; color:#475569; font-weight:700;">Service Vertical</th>
                  <td>
                    <span class="label" style="background:#f1f5f9; color:#000000; border:1px solid #cbd5e1; font-weight:700; padding:4px 8px; font-size:11px;">
                      <?php echo htmlspecialchars(!empty($lead['servicecat']) ? $lead['servicecat'] : 'General'); ?>
                    </span>
                  </td>
                </tr>
                <tr>
                  <th style="background:#f8fafc; font-size:11px; text-transform:uppercase; color:#475569; font-weight:700;">Estimated MRR</th>
                  <td style="font-weight:800; color:#000000; font-size:15px;">AED <?php echo number_format((float)($lead['mrr'] ?? 0), 2); ?></td>
                </tr>
                <tr>
                  <th style="background:#f8fafc; font-size:11px; text-transform:uppercase; color:#475569; font-weight:700;">Requested By</th>
                  <td>
                    <strong style="color:#000000;"><?php echo htmlspecialchars($lead['requested_by_name'] ?: 'Staff'); ?></strong>
                    <div style="font-size:11px; color:#64748b;"><?php echo !empty($lead['order_requested_at']) ? date('d M Y, h:i A', strtotime($lead['order_requested_at'])) : '-'; ?></div>
                  </td>
                </tr>
                <tr>
                  <th style="background:#f8fafc; font-size:11px; text-transform:uppercase; color:#475569; font-weight:700;">Submitted Scope &amp; Remarks</th>
                  <td style="white-space:pre-wrap; background:#f8fafc; border:1px solid #e2e8f0; color:#0f172a; font-size:12px; font-weight:500; line-height:1.5; border-radius:4px; padding:10px;"><?php echo htmlspecialchars($lead['order_request_remarks'] ?: 'No notes provided by requester.'); ?></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="box box-default" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff; margin-bottom:15px;">
          <div class="box-header" style="background:#f8fafc; border-bottom:1px solid #e2e8f0; padding:12px 18px;">
            <h3 class="box-title" style="font-size:13px; font-weight:700; color:#000000; text-transform:uppercase; letter-spacing:0.04em;">Remarks &amp; Activity Timeline</h3>
          </div>
          <div class="box-body" style="padding:18px; max-height:350px; overflow-y:auto;">
            <?php if (!empty($remarks_history)) { ?>
              <ul class="timeline" style="margin-bottom:0;">
                <?php foreach($remarks_history as $rem){ ?>
                  <li>
                    <i class="fa fa-comment" style="background:#000000; color:#ffffff; font-size:12px; width:26px; height:26px; line-height:26px; border-radius:50%; text-align:center;"></i>
                    <div class="timeline-item" style="border-radius:4px; box-shadow:none; border:1px solid #e2e8f0; background:#ffffff;">
                      <span class="time" style="font-size:11px; color:#64748b;"><i class="fa fa-clock-o"></i> <?php echo date('d M Y, h:i A', strtotime($rem['created'])); ?></span>
                      <h3 class="timeline-header" style="font-size:12px; font-weight:700; border-bottom:1px solid #f1f5f9; padding:8px 12px; color:#000000;">
                        <?php echo htmlspecialchars($rem['user_name'] ?? 'Staff'); ?>
                        <?php if (!empty($rem['status'])) { ?>
                          <span class="label" style="font-size:10px; margin-left:6px; background:#f1f5f9; color:#000000; border:1px solid #cbd5e1; font-weight:700;"><?php echo htmlspecialchars($rem['status']); ?></span>
                        <?php } ?>
                      </h3>
                      <div class="timeline-body" style="font-size:12px; padding:10px 12px; color:#334155; line-height:1.5;">
                        <?php echo nl2br(htmlspecialchars($rem['remark'] ?? '')); ?>
                      </div>
                    </div>
                  </li>
                <?php } ?>
                <li><i class="fa fa-clock-o" style="background:#e2e8f0; color:#000000; font-size:12px; width:26px; height:26px; line-height:26px; border-radius:50%; text-align:center;"></i></li>
              </ul>
            <?php } else { ?>
              <div style="text-align:center; padding:20px; color:#64748b; font-size:13px;">No past follow-up remarks recorded.</div>
            <?php } ?>
          </div>
          <div class="box-footer" style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:14px 18px;">
            <form action="<?php echo base_url(); ?>manager/orders/edit_pending/<?php echo $lead['id']; ?>" method="post">
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
              <input type="hidden" name="action_type" value="add_remark">
              <div class="form-group" style="margin-bottom:10px;">
                <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569;">Add Review Remark</label>
                <textarea name="new_remark" class="form-control" rows="2" placeholder="Enter remarks regarding this order process..." required style="border:1px solid #cbd5e1; border-radius:4px;"></textarea>
              </div>
              <button type="submit" class="btn btn-default" style="font-weight:600; background:#000000; color:#ffffff; border:1px solid #000000;">
                <i class="fa fa-comment-o"></i> Post Remark
              </button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-md-5">
        <div class="box box-default" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff; margin-bottom:15px;">
          <div class="box-header" style="background:#f8fafc; border-bottom:1px solid #e2e8f0; padding:12px 18px;">
            <h3 class="box-title" style="font-size:13px; font-weight:700; color:#000000; text-transform:uppercase; letter-spacing:0.04em;">Process &amp; Create Order</h3>
          </div>
          <form action="<?php echo base_url(); ?>manager/orders/edit_pending/<?php echo $lead['id']; ?>" method="post">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="action_type" value="approve_order">
            <div class="box-body" style="padding:18px;">
              <div class="form-group">
                <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569;">Vertical *</label>
                <select name="vertical_code" class="form-control" required style="border:1px solid #cbd5e1; border-radius:4px;">
                  <option value="accounting" <?php echo (stripos($lead['servicecat'] ?? '', 'account') !== false) ? 'selected' : ''; ?>>Accounting / VAT</option>
                  <option value="telecom" <?php echo (stripos($lead['servicecat'] ?? '', 'telecom') !== false) ? 'selected' : ''; ?>>Telecom (Du / Etisalat)</option>
                  <option value="corporate" <?php echo (stripos($lead['servicecat'] ?? '', 'corporate') !== false) ? 'selected' : ''; ?>>Corporate Services</option>
                  <option value="it" <?php echo (stripos($lead['servicecat'] ?? '', 'it') !== false) ? 'selected' : ''; ?>>IT Services</option>
                  <option value="general">General</option>
                </select>
              </div>

              <div class="form-group">
                <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569;">Service Scope Title *</label>
                <input type="text" name="service_title" class="form-control" value="<?php echo htmlspecialchars(!empty($lead['servicecat']) ? $lead['servicecat'] : 'Standard Service Scope'); ?>" required style="border:1px solid #cbd5e1; border-radius:4px;">
              </div>

              <div class="form-group">
                <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569;">Service Description / Scope</label>
                <textarea name="service_scope" class="form-control" rows="3" placeholder="Detailed scope of service..." style="border:1px solid #cbd5e1; border-radius:4px;"><?php echo htmlspecialchars($lead['order_request_remarks'] ?? ''); ?></textarea>
              </div>

              <div class="row">
                <div class="col-xs-6 form-group">
                  <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569;">Pricing Amount (AED) *</label>
                  <input type="number" step="0.01" min="0" name="pricing_amount" class="form-control" value="<?php echo htmlspecialchars($lead['mrr'] ?? '0.00'); ?>" required style="border:1px solid #cbd5e1; border-radius:4px;">
                </div>
                <div class="col-xs-6 form-group">
                  <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569;">Recurring MRR (AED)</label>
                  <input type="number" step="0.01" min="0" name="mrr" class="form-control" value="<?php echo htmlspecialchars($lead['mrr'] ?? '0.00'); ?>" style="border:1px solid #cbd5e1; border-radius:4px;">
                </div>
              </div>

              <div class="row">
                <div class="col-xs-6 form-group">
                  <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569;">Assigned Department</label>
                  <select name="assigned_dept" class="form-control" style="border:1px solid #cbd5e1; border-radius:4px;">
                    <option value="Back Office">Back Office</option>
                    <option value="Operations">Operations</option>
                    <option value="Finance">Finance</option>
                    <option value="IT">IT</option>
                  </select>
                </div>
                <div class="col-xs-6 form-group">
                  <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569;">Assigned Staff</label>
                  <select name="assigned_to" class="form-control" style="border:1px solid #cbd5e1; border-radius:4px;">
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

              <div class="form-group" style="margin-bottom:0;">
                <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569;">Approval / Processing Notes</label>
                <textarea name="approval_notes" class="form-control" rows="2" placeholder="Optional notes for order history..." style="border:1px solid #cbd5e1; border-radius:4px;"></textarea>
              </div>
            </div>
            <div class="box-footer" style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:14px 18px; display:flex; justify-content:space-between; align-items:center;">
              <button type="submit" class="btn btn-default" style="background:#000000; border:1px solid #000000; color:#ffffff; font-weight:700; padding:8px 20px;">
                <i class="fa fa-check-circle"></i> Approve &amp; Create Order
              </button>
              <button type="button" class="btn btn-default" data-toggle="modal" data-target="#rejectModal" style="background:#ffffff; border:1px solid #000000; color:#000000; font-weight:600; padding:8px 16px;">
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
    <div class="modal-content" style="border-radius:6px; border:1px solid #000000; overflow:hidden;">
      <form action="<?php echo base_url(); ?>manager/orders/edit_pending/<?php echo $lead['id']; ?>" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="action_type" value="reject_order">
        <div class="modal-header" style="background:#000000; color:#ffffff; padding:14px 18px; border-bottom:1px solid #000000;">
          <button type="button" class="close" data-dismiss="modal" style="color:#ffffff; opacity:0.8;">&times;</button>
          <h4 class="modal-title" style="font-size:15px; font-weight:700; color:#ffffff; margin:0;"><i class="fa fa-times-circle" style="color:#ffffff;"></i> Reject Order Request</h4>
        </div>
        <div class="modal-body" style="padding:20px; background:#ffffff; color:#000000;">
          <p style="font-size:13px; color:#475569; margin-bottom:14px;">Please specify the reason for rejecting this Lead-to-Order conversion request. The lead will be returned to normal state and unlocked.</p>
          <div class="form-group" style="margin-bottom:0;">
            <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569;">Rejection Reason *</label>
            <textarea name="reject_reason" class="form-control" rows="3" placeholder="Provide clear reason for rejection..." required style="border:1px solid #cbd5e1; border-radius:4px; resize:vertical;"></textarea>
          </div>
        </div>
        <div class="modal-footer" style="padding:12px 18px; background:#f8fafc; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:8px;">
          <button type="button" class="btn btn-default" data-dismiss="modal" style="background:#ffffff; border:1px solid #000000; color:#000000; font-weight:600;">Cancel</button>
          <button type="submit" class="btn btn-default" style="background:#000000; border:1px solid #000000; color:#ffffff; font-weight:600;">Confirm Rejection</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
