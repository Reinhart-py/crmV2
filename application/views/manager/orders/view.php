<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper">
  <section class="content-header" style="display:flex; justify-content:space-between; align-items:center; padding:15px 15px 5px 15px;">
    <div>
      <h1 style="margin:0; font-size:20px; font-weight:700; color:#0f172a;">
        <i class="fa fa-shopping-cart" style="color:#2563eb; margin-right:8px;"></i><?php echo $info['page_heading']; ?>
        <span class="label" style="font-size:12px; margin-left:8px; vertical-align:middle; background:#f1f5f9; color:#334155; border:1px solid #cbd5e1;">
          <?php echo htmlspecialchars(strtoupper($order['vertical_code'] ?: 'GENERAL')); ?>
        </span>
      </h1>
    </div>
    <div style="display:flex; gap:8px;">
      <?php 
        $back_url = base_url('manager/orders');
        if (empty($can_edit)) {
          $back_url = ($order['status'] === 'Sales') ? base_url('manager/sales') : base_url('manager/orders/my');
        }
      ?>
      <a href="<?php echo $back_url; ?>" class="btn btn-default" style="font-weight:600; border-color:#cbd5e1;">
        <i class="fa fa-arrow-left"></i> <?php echo empty($can_edit) ? 'Back' : 'Back to Orders'; ?>
      </a>
      <?php if(!empty($can_approve) && $order['approval_status'] === 'pending'){ ?>
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#approveOrderModal" style="background:#16a34a; border-color:#15803d; font-weight:600;">
          <i class="fa fa-check"></i> Approve Order
        </button>
        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectOrderModal" style="background:#dc2626; border-color:#b91c1c; font-weight:600;">
          <i class="fa fa-times"></i> Reject
        </button>
      <?php } ?>
      <?php if(!empty($can_edit)){ ?>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#updateStatusModal" style="background:#2563eb; border-color:#1d4ed8; font-weight:600;">
          <i class="fa fa-refresh"></i> Update Status
        </button>
      <?php } ?>
      <?php if(!empty($can_edit) && !empty($can_approve) && $order['status'] !== 'Sales'){ ?>
        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#convertToSalesModal" style="background:#f59e0b; border-color:#d97706; font-weight:600; color:#ffffff;">
          <i class="fa fa-line-chart"></i> Convert to Sales
        </button>
      <?php } ?>
    </div>
  </section>

  <section class="content" style="padding-top:10px;">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="box box-default" style="border-radius:6px; margin-bottom:15px; border-left:4px solid #2563eb;">
      <div class="box-body" style="padding:12px 18px; display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px;">
        <div style="display:flex; flex-wrap:wrap; align-items:center; gap:20px;">
          <div>
            <span style="font-size:11px; text-transform:uppercase; color:#64748b; font-weight:700; letter-spacing:0.5px; display:block;">Database Ref</span>
            <span class="label" style="background:#f1f5f9; color:#334155; border:1px solid #cbd5e1; font-family:monospace; font-size:12px;">
              <?php echo htmlspecialchars($order['db_ref_no'] ?: ('D' . str_pad($order['lead_id'] ?: $order['id'], 10, '0', STR_PAD_LEFT))); ?>
            </span>
          </div>
          <div>
            <span style="font-size:11px; text-transform:uppercase; color:#64748b; font-weight:700; letter-spacing:0.5px; display:block;">Lead Ref</span>
            <span class="label" style="background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; font-family:monospace; font-size:12px;">
              <?php echo htmlspecialchars($order['lead_ref_no'] ?: ('L' . str_pad($order['lead_id'] ?: $order['id'], 10, '0', STR_PAD_LEFT))); ?>
            </span>
          </div>
          <div>
            <span style="font-size:11px; text-transform:uppercase; color:#64748b; font-weight:700; letter-spacing:0.5px; display:block;">Order Ref</span>
            <span class="label" style="background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; font-family:monospace; font-size:12px;">
              <?php echo htmlspecialchars($order['order_no']); ?>
            </span>
          </div>
          <div>
            <span style="font-size:11px; text-transform:uppercase; color:#64748b; font-weight:700; letter-spacing:0.5px; display:block;">Sales Ref</span>
            <?php if(!empty($order['sales_ref_no'])){ ?>
              <span class="label" style="background:#fef3c7; color:#92400e; border:1px solid #fde68a; font-family:monospace; font-size:12px;">
                <?php echo htmlspecialchars($order['sales_ref_no']); ?>
              </span>
            <?php } else { ?>
              <span style="color:#94a3b8; font-size:12px; font-style:italic;">Pending Sales Completion</span>
            <?php } ?>
          </div>
        </div>
        <div>
          <span class="label" style="font-size:12px; padding:5px 10px; background:<?php echo ($order['status'] === 'Sales') ? '#16a34a' : '#2563eb'; ?>; color:#ffffff;">
            Status: <?php echo htmlspecialchars($order['status']); ?>
          </span>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-8">
        <div class="box box-default" style="border-radius:6px; margin-bottom:18px;">
          <div class="box-header" style="background:#f8fafc; border-bottom:1px solid #e2e8f0; padding:12px 18px;">
            <h3 class="box-title" style="font-size:15px; font-weight:700; color:#0f172a;">
              <i class="fa fa-info-circle" style="color:#2563eb; margin-right:6px;"></i>Order &amp; Service Details
            </h3>
          </div>
          <div class="box-body" style="padding:18px;">
            <div class="row" style="margin-bottom:12px;">
              <div class="col-sm-4" style="color:#64748b; font-size:13px;">Client Name:</div>
              <div class="col-sm-8" style="font-size:14px; font-weight:600; color:#0f172a;">
                <?php echo htmlspecialchars($order['client_name'] ?: ($order['lead_customer'] ?: 'N/A')); ?>
                <?php if(!empty($order['lead_id'])){ ?>
                  <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($order['lead_id']); ?>" target="_blank" style="font-size:12px; margin-left:6px; color:#2563eb;">
                    <i class="fa fa-external-link"></i> View Lead
                  </a>
                <?php } ?>
              </div>
            </div>

            <div class="row" style="margin-bottom:12px;">
              <div class="col-sm-4" style="color:#64748b; font-size:13px;">Service Title:</div>
              <div class="col-sm-8" style="font-size:14px; font-weight:700; color:#2563eb;">
                <?php echo htmlspecialchars($order['service_title']); ?>
              </div>
            </div>

            <div class="row" style="margin-bottom:12px;">
              <div class="col-sm-4" style="color:#64748b; font-size:13px;">Scope of Deliverables:</div>
              <div class="col-sm-8" style="font-size:13px; color:#334155; line-height:1.5;">
                <?php echo nl2br(htmlspecialchars($order['service_scope'] ?: 'Standard operational scope.')); ?>
              </div>
            </div>

            <div class="row" style="margin-bottom:12px;">
              <div class="col-sm-4" style="color:#64748b; font-size:13px;">Operational Instructions:</div>
              <div class="col-sm-8" style="font-size:13px; color:#334155; line-height:1.5;">
                <?php echo nl2br(htmlspecialchars($order['operational_instructions'] ?: 'None specified.')); ?>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-4" style="color:#64748b; font-size:13px;">Creation Timestamp:</div>
              <div class="col-sm-8" style="font-size:13px; color:#334155;">
                <?php echo date('d-m-Y h:i A', strtotime($order['created_at'])); ?>
              </div>
            </div>
          </div>
        </div>

        <div class="box box-default" style="border-radius:6px; margin-bottom:18px;">
          <div class="box-header" style="background:#f8fafc; border-bottom:1px solid #e2e8f0; padding:12px 18px;">
            <h3 class="box-title" style="font-size:15px; font-weight:700; color:#0f172a;">
              <i class="fa fa-file-text-o" style="color:#2563eb; margin-right:6px;"></i>Required Documents Checklist
            </h3>
          </div>
          <div class="box-body" style="padding:15px;">
            <?php if(empty($docs)){ ?>
              <p style="color:#94a3b8; margin-bottom:0;">No mandatory document requirements assigned.</p>
            <?php } else { ?>
              <div class="table-responsive">
                <table class="table table-bordered table-hover" style="margin-bottom:0;">
                  <thead>
                    <tr style="background:#f8fafc; font-size:12px;">
                      <th>Document Item</th>
                      <th style="width:140px;">Collection Status</th>
                      <th style="width:170px;">Last Updated</th>
                      <?php if(!empty($can_edit)){ ?>
                        <th style="width:100px; text-align:right;">Toggle</th>
                      <?php } ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach($docs as $d_key => $d_val){ ?>
                      <?php $is_col = (isset($d_val['status']) && $d_val['status'] === 'collected'); ?>
                      <tr id="doc_row_<?php echo $d_key; ?>">
                        <td style="font-weight:600; color:#1e293b; vertical-align:middle;">
                          <i class="fa <?php echo $is_col ? 'fa-check-circle' : 'fa-circle-o'; ?>" style="color:<?php echo $is_col ? '#16a34a' : '#f59e0b'; ?>; margin-right:6px;"></i>
                          <?php echo htmlspecialchars($d_val['title'] ?? $d_key); ?>
                        </td>
                        <td style="vertical-align:middle;" id="doc_badge_<?php echo $d_key; ?>">
                          <span class="label" style="background:<?php echo $is_col ? '#ecfdf5' : '#fffbeb'; ?>; color:<?php echo $is_col ? '#065f46' : '#92400e'; ?>; border:1px solid <?php echo $is_col ? '#a7f3d0' : '#fde68a'; ?>; font-weight:600; padding:3px 7px; font-size:11px;">
                            <?php echo $is_col ? 'Collected' : 'Pending'; ?>
                          </span>
                        </td>
                        <td style="font-size:11px; color:#64748b; vertical-align:middle;">
                          <?php echo !empty($d_val['updated_at']) ? date('d-m-Y H:i', strtotime($d_val['updated_at'])) : '-'; ?>
                        </td>
                        <?php if(!empty($can_edit)){ ?>
                          <td style="text-align:right; vertical-align:middle;">
                            <button type="button" class="btn btn-xs btn-default btn-toggle-doc" data-order="<?php echo $order['id']; ?>" data-key="<?php echo $d_key; ?>" data-status="<?php echo $is_col ? 'pending' : 'collected'; ?>" style="font-weight:600; font-size:11px;">
                              Mark <?php echo $is_col ? 'Pending' : 'Collected'; ?>
                            </button>
                          </td>
                        <?php } ?>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            <?php } ?>
          </div>
        </div>

        <div class="box box-default" style="border-radius:6px; margin-bottom:18px;">
          <div class="box-header" style="background:#f8fafc; border-bottom:1px solid #e2e8f0; padding:12px 18px;">
            <h3 class="box-title" style="font-size:15px; font-weight:700; color:#0f172a;">
              <i class="fa fa-history" style="color:#2563eb; margin-right:6px;"></i>Activity &amp; Operational Notes
            </h3>
          </div>
          <div class="box-body" style="padding:18px;">
            <?php if(!empty($can_edit)){ ?>
              <form action="<?php echo base_url(); ?>manager/orders/add_note/<?php echo $order['id']; ?>" method="post" style="margin-bottom:20px;">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="form-group">
                  <label style="font-weight:600; font-size:12px; color:#475569;">Add Activity Note / Operational Update</label>
                  <textarea name="notes" rows="2" class="form-control" placeholder="Enter status update or client handover notes..." required></textarea>
                </div>
                <button type="submit" class="btn btn-sm btn-primary" style="background:#2563eb; border-color:#1d4ed8; font-weight:600;">
                  <i class="fa fa-comment-o"></i> Post Note
                </button>
              </form>
            <?php } ?>

            <?php if(empty($activity_logs)){ ?>
              <p style="color:#94a3b8; font-size:13px; margin-bottom:0;">No operational history logged yet.</p>
            <?php } else { ?>
              <ul class="timeline timeline-inverse" style="margin-bottom:0;">
                <?php foreach($activity_logs as $act){ ?>
                  <li>
                    <i class="fa fa-check bg-blue" style="font-size:11px; width:24px; height:24px; line-height:24px;"></i>
                    <div class="timeline-item" style="box-shadow:none; border:1px solid #e2e8f0; border-radius:4px; padding:10px;">
                      <span class="time" style="font-size:11px; color:#94a3b8;"><i class="fa fa-clock-o"></i> <?php echo date('d-m-Y h:i A', strtotime($act['created_at'])); ?></span>
                      <h4 class="timeline-header" style="font-size:13px; font-weight:700; border-bottom:none; padding:0; color:#1e293b;">
                        <?php echo htmlspecialchars($act['action_by_name'] ?? 'System'); ?>:
                        <span style="font-weight:400; color:#475569;"><?php echo htmlspecialchars($act['notes']); ?></span>
                      </h4>
                    </div>
                  </li>
                <?php } ?>
              </ul>
            <?php } ?>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="box box-default" style="border-radius:6px; margin-bottom:18px;">
          <div class="box-header" style="background:#f8fafc; border-bottom:1px solid #e2e8f0; padding:12px 18px;">
            <h3 class="box-title" style="font-size:15px; font-weight:700; color:#0f172a;">
              <i class="fa fa-money" style="color:#16a34a; margin-right:6px;"></i>Commercials
            </h3>
          </div>
          <div class="box-body" style="padding:18px;">
            <div style="margin-bottom:15px;">
              <div style="font-size:12px; color:#64748b; font-weight:600; text-transform:uppercase;">Contract Value</div>
              <div style="font-size:24px; font-weight:700; color:#0f172a;">
                <?php echo htmlspecialchars($order['currency'] ?: 'AED'); ?> <?php echo number_format($order['pricing_amount'], 2); ?>
              </div>
            </div>
            <div class="row" style="margin-bottom:12px;">
              <div class="col-xs-6">
                <div style="font-size:11px; color:#64748b; font-weight:600;">Monthly Recurring (MRR)</div>
                <div style="font-size:16px; font-weight:700; color:#16a34a;">
                  <?php echo number_format($order['mrr'], 2); ?>
                </div>
              </div>
              <div class="col-xs-6">
                <div style="font-size:11px; color:#64748b; font-weight:600;">One-Time Charge (OTC)</div>
                <div style="font-size:16px; font-weight:700; color:#0284c7;">
                  <?php echo number_format($order['otc'], 2); ?>
                </div>
              </div>
            </div>
            <div style="font-size:12px; color:#64748b; padding-top:8px; border-top:1px solid #f1f5f9;">
              Billing Cycle: <strong><?php echo htmlspecialchars($order['billing_type'] ?: 'Monthly'); ?></strong>
            </div>
          </div>
        </div>

        <div class="box box-default" style="border-radius:6px; margin-bottom:18px;">
          <div class="box-header" style="background:#f8fafc; border-bottom:1px solid #e2e8f0; padding:12px 18px;">
            <h3 class="box-title" style="font-size:15px; font-weight:700; color:#0f172a;">
              <i class="fa fa-users" style="color:#2563eb; margin-right:6px;"></i>Assignment &amp; Approval
            </h3>
          </div>
          <div class="box-body" style="padding:18px;">
            <div style="margin-bottom:12px;">
              <div style="font-size:11px; color:#64748b; font-weight:600;">Operational Status</div>
              <div style="margin-top:4px;">
                <span class="label" style="font-size:12px; font-weight:600; padding:4px 8px; background:#f1f5f9; color:#0f172a; border:1px solid #cbd5e1;">
                  <?php echo htmlspecialchars($order['status']); ?>
                </span>
              </div>
            </div>

            <div style="margin-bottom:12px;">
              <div style="font-size:11px; color:#64748b; font-weight:600;">Approval Status</div>
              <div style="margin-top:4px;">
                <?php if($order['approval_status'] === 'approved'){ ?>
                  <span class="label" style="background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; font-weight:600; padding:4px 8px; font-size:12px;">
                    <i class="fa fa-check"></i> Approved
                  </span>
                <?php } elseif($order['approval_status'] === 'rejected'){ ?>
                  <span class="label" style="background:#fef2f2; color:#991b1b; border:1px solid #fecaca; font-weight:600; padding:4px 8px; font-size:12px;">
                    <i class="fa fa-times"></i> Rejected
                  </span>
                <?php } else { ?>
                  <span class="label" style="background:#fffbeb; color:#92400e; border:1px solid #fde68a; font-weight:600; padding:4px 8px; font-size:12px;">
                    <i class="fa fa-clock-o"></i> Pending Review
                  </span>
                <?php } ?>
              </div>
            </div>

            <div style="margin-bottom:12px;">
              <div style="font-size:11px; color:#64748b; font-weight:600;">Assigned Staff</div>
              <div style="font-size:13px; font-weight:600; color:#1e293b; margin-top:3px;">
                <?php echo htmlspecialchars($order['staff_name'] ?? 'Unassigned'); ?>
              </div>
              <div style="font-size:11px; color:#64748b;">
                Dept: <?php echo htmlspecialchars($order['assigned_dept'] ?: 'Back Office'); ?>
              </div>
            </div>
          </div>
        </div>

        <?php if(!empty($approval_history)){ ?>
          <div class="box box-default" style="border-radius:6px; margin-bottom:18px;">
            <div class="box-header" style="background:#f8fafc; border-bottom:1px solid #e2e8f0; padding:12px 18px;">
              <h3 class="box-title" style="font-size:15px; font-weight:700; color:#0f172a;">
                <i class="fa fa-gavel" style="color:#2563eb; margin-right:6px;"></i>Approval History
              </h3>
            </div>
            <div class="box-body" style="padding:15px;">
              <?php foreach($approval_history as $ah){ ?>
                <div style="padding-bottom:10px; margin-bottom:10px; border-bottom:1px solid #f1f5f9; font-size:12px;">
                  <div style="display:flex; justify-content:space-between;">
                    <strong><?php echo htmlspecialchars($ah['user_name'] ?? 'Staff'); ?></strong>
                    <span style="color:#94a3b8; font-size:11px;"><?php echo !empty($ah['timestamp']) ? date('d-m-Y H:i', strtotime($ah['timestamp'])) : ''; ?></span>
                  </div>
                  <div style="color:#475569; margin-top:2px;">
                    Action: <span class="label label-default" style="font-size:10px;"><?php echo htmlspecialchars(ucfirst($ah['action'] ?? '')); ?></span>
                  </div>
                  <?php if(!empty($ah['remarks'])){ ?>
                    <div style="color:#64748b; font-style:italic; margin-top:2px;">"<?php echo htmlspecialchars($ah['remarks']); ?>"</div>
                  <?php } ?>
                </div>
              <?php } ?>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
  </section>
</div>

<div class="modal fade" id="approveOrderModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="border-radius:6px; border:1px solid #111827;">
      <form action="<?php echo base_url(); ?>manager/orders/approve/<?php echo $order['id']; ?>" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="modal-header" style="background:#000000; border-bottom:1px solid #111827; border-radius:6px 6px 0 0;">
          <button type="button" class="close" data-dismiss="modal" style="color:#ffffff; opacity:0.8;">&times;</button>
          <h4 class="modal-title" style="font-weight:700; color:#ffffff;"><i class="fa fa-check-circle" style="color:#ffffff;"></i> Approve Order</h4>
        </div>
        <div class="modal-body" style="padding:20px; background:#ffffff; color:#111827;">
          <p style="font-size:14px; color:#334155;">Confirm Back Office operational approval for <strong><?php echo htmlspecialchars($order['order_no']); ?></strong>.</p>
          <div class="form-group" style="margin-bottom:0;">
            <label style="font-weight:600; color:#334155;">Approval Remarks</label>
            <textarea name="remarks" rows="3" class="form-control" placeholder="Optional approval remarks..."></textarea>
          </div>
        </div>
        <div class="modal-footer" style="background:#f9fafb; border-top:1px solid #e5e7eb;">
          <button type="button" class="btn btn-default" data-dismiss="modal" style="background:#ffffff; border:1px solid #d1d5db; color:#111827;">Cancel</button>
          <button type="submit" class="btn btn-default" style="background:#000000; border-color:#000000; color:#ffffff; font-weight:600;">Confirm Approval</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="rejectOrderModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="border-radius:6px; border:1px solid #111827;">
      <form action="<?php echo base_url(); ?>manager/orders/reject/<?php echo $order['id']; ?>" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="modal-header" style="background:#000000; border-bottom:1px solid #111827; border-radius:6px 6px 0 0;">
          <button type="button" class="close" data-dismiss="modal" style="color:#ffffff; opacity:0.8;">&times;</button>
          <h4 class="modal-title" style="font-weight:700; color:#ffffff;"><i class="fa fa-times-circle" style="color:#ffffff;"></i> Reject Order</h4>
        </div>
        <div class="modal-body" style="padding:20px; background:#ffffff; color:#111827;">
          <p style="font-size:14px; color:#334155;">Specify reason for returning order <strong><?php echo htmlspecialchars($order['order_no']); ?></strong> to draft.</p>
          <div class="form-group" style="margin-bottom:0;">
            <label style="font-weight:600; color:#334155;">Rejection Reason</label>
            <textarea name="remarks" rows="3" class="form-control" required placeholder="Specify what is missing or required before approval..."></textarea>
          </div>
        </div>
        <div class="modal-footer" style="background:#f9fafb; border-top:1px solid #e5e7eb;">
          <button type="button" class="btn btn-default" data-dismiss="modal" style="background:#ffffff; border:1px solid #d1d5db; color:#111827;">Cancel</button>
          <button type="submit" class="btn btn-default" style="background:#000000; border-color:#000000; color:#ffffff; font-weight:600;">Reject Order</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="border-radius:6px; border:1px solid #111827;">
      <form action="<?php echo base_url(); ?>manager/orders/update_status/<?php echo $order['id']; ?>" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="modal-header" style="background:#000000; border-bottom:1px solid #111827; border-radius:6px 6px 0 0;">
          <button type="button" class="close" data-dismiss="modal" style="color:#ffffff; opacity:0.8;">&times;</button>
          <h4 class="modal-title" style="font-weight:700; color:#ffffff;"><i class="fa fa-refresh" style="color:#ffffff;"></i> Update Operational Status</h4>
        </div>
        <div class="modal-body" style="padding:20px; background:#ffffff; color:#111827;">
          <div class="form-group">
            <label style="font-weight:600; color:#334155;">New Operational Status</label>
            <select name="status" class="form-control" required>
              <option value="Draft" <?php echo ($order['status'] === 'Draft') ? 'selected' : ''; ?>>Draft</option>
              <option value="Submitted" <?php echo ($order['status'] === 'Submitted') ? 'selected' : ''; ?>>Pending Review (Submitted)</option>
              <option value="Under Review" <?php echo ($order['status'] === 'Under Review') ? 'selected' : ''; ?>>Under Review</option>
              <option value="Verification" <?php echo ($order['status'] === 'Verification') ? 'selected' : ''; ?>>Verification</option>
              <option value="Document Collection" <?php echo ($order['status'] === 'Document Collection') ? 'selected' : ''; ?>>Document Collection</option>
              <option value="Approved" <?php echo ($order['status'] === 'Approved') ? 'selected' : ''; ?>>Approved</option>
              <option value="In Progress" <?php echo ($order['status'] === 'In Progress') ? 'selected' : ''; ?>>Ongoing Processing</option>
              <option value="Returned for Correction" <?php echo ($order['status'] === 'Returned for Correction') ? 'selected' : ''; ?>>Returned for Correction</option>
              <option value="Rejected" <?php echo ($order['status'] === 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
              <option value="Completed" <?php echo ($order['status'] === 'Completed' || $order['status'] === 'Fulfilled') ? 'selected' : ''; ?>>Completed</option>
              <option value="Sales" <?php echo ($order['status'] === 'Sales') ? 'selected' : ''; ?>>Sales</option>
              <option value="Cancelled" <?php echo ($order['status'] === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
            </select>
          </div>
          <div class="form-group" style="margin-bottom:0;">
            <label style="font-weight:600; color:#334155;">Status Transition Notes</label>
            <textarea name="notes" rows="2" class="form-control" placeholder="Optional transition remarks..."></textarea>
          </div>
        </div>
        <div class="modal-footer" style="background:#f9fafb; border-top:1px solid #e5e7eb;">
          <button type="button" class="btn btn-default" data-dismiss="modal" style="background:#ffffff; border:1px solid #d1d5db; color:#111827;">Cancel</button>
          <button type="submit" class="btn btn-default" style="background:#000000; border-color:#000000; color:#ffffff; font-weight:600;">Update Status</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="convertToSalesModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="border-radius:6px; border:1px solid #111827;">
      <form action="<?php echo base_url(); ?>manager/orders/convert_to_sales/<?php echo $order['id']; ?>" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="modal-header" style="background:#000000; border-bottom:1px solid #111827; border-radius:6px 6px 0 0;">
          <button type="button" class="close" data-dismiss="modal" style="color:#ffffff; opacity:0.8;">&times;</button>
          <h4 class="modal-title" style="font-weight:700; color:#ffffff;"><i class="fa fa-line-chart" style="color:#ffffff;"></i> Convert Order to Sales</h4>
        </div>
        <div class="modal-body" style="padding:20px; background:#ffffff; color:#111827;">
          <p style="font-size:14px; color:#334155;">Confirm that Order <strong><?php echo htmlspecialchars($order['order_no']); ?></strong> has completed all client requirements, payment has been received, and documents are verified.</p>
          <div class="alert alert-info" style="font-size:12px; margin-bottom:15px;">
            A unique sequential Sales referral number (e.g. <code><?php echo sprintf('S%010d', $order['id']); ?></code>) will be generated and assigned to this customer record.
          </div>
          <div class="form-group" style="margin-bottom:0;">
            <label style="font-weight:600; color:#334155;">Sales Remarks &amp; Completion Notes</label>
            <textarea name="notes" rows="3" class="form-control" placeholder="Enter deal closure, payment receipt, and document verification notes..."></textarea>
          </div>
        </div>
        <div class="modal-footer" style="background:#f9fafb; border-top:1px solid #e5e7eb;">
          <button type="button" class="btn btn-default" data-dismiss="modal" style="background:#ffffff; border:1px solid #d1d5db; color:#111827;">Cancel</button>
          <button type="submit" class="btn btn-default" style="background:#000000; border-color:#000000; font-weight:600; color:#ffffff;">Confirm Sales Conversion</button>
        </div>
      </form>
    </div>
  </div>
</div>


<script>
$(function() {
  $(document).on('click', '.btn-toggle-doc', function(e) {
    e.preventDefault();
    var btn = $(this);
    var orderId = btn.data('order');
    var docKey = btn.data('key');
    var targetStatus = btn.data('status');
    btn.prop('disabled', true);

    $.ajax({
      url: '<?php echo base_url(); ?>manager/orders/ajax_toggle_doc',
      type: 'POST',
      dataType: 'json',
      data: {
        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
        order_id: orderId,
        doc_key: docKey,
        status: targetStatus
      },
      success: function(resp) {
        btn.prop('disabled', false);
        if (resp && resp.success) {
          var isCol = (resp.new_status === 'collected');
          var badgeHtml = isCol 
            ? '<span class="label" style="background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; font-weight:600; padding:3px 7px; font-size:11px;">Collected</span>'
            : '<span class="label" style="background:#fffbeb; color:#92400e; border:1px solid #fde68a; font-weight:600; padding:3px 7px; font-size:11px;">Pending</span>';
          $('#doc_badge_' + docKey).html(badgeHtml);
          btn.data('status', isCol ? 'pending' : 'collected');
          btn.text('Mark ' + (isCol ? 'Pending' : 'Collected'));
          window.location.reload();
        } else {
          alert('Error: ' + (resp.message || 'Could not update document status'));
        }
      },
      error: function() {
        btn.prop('disabled', false);
        alert('Server connection error.');
      }
    });
  });
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
