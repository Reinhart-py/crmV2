<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1> <?php echo $info['page_heading']; ?> </h1>
  </section>

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="container">
      <div class="row">
        <div class="box box-default">
          <div class="box-header">
            <div class="col-sm-12">
              <div class="pull-left">
                <?php 
                  $back_url = base_url('manager/orders');
                  if (empty($can_edit)) {
                    $back_url = ($order['status'] === 'Sales') ? base_url('manager/sales') : base_url('manager/orders/my');
                  }
                ?>
                <a href="<?php echo $back_url; ?>" class="btn btn-default" style="background:#1e3a8a; border-color:#1e3a8a; color:#ffffff; font-weight:600; border-radius:4px; padding:6px 16px;">Back</a>
              </div>

              <div class="pull-right">
                <?php if(!empty($can_approve) && ($order['approval_status'] === 'pending' || $order['status'] === 'Pending Sales Approval')){ ?>
                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#approveOrderModal" style="background:#1e3a8a; border-color:#1e3a8a; color:#ffffff; font-weight:600; border-radius:4px; margin-right:5px; padding:6px 14px;">
                    <i class="fa fa-check"></i> Approve Order
                  </button>
                  <button type="button" class="btn btn-default" data-toggle="modal" data-target="#rejectOrderModal" style="background:#f1f5f9; border-color:#cbd5e1; color:#334155; font-weight:600; border-radius:4px; margin-right:5px; padding:6px 14px;">
                    <i class="fa fa-times"></i> Reject
                  </button>
                <?php } ?>

                <?php if(!empty($can_edit)){ ?>
                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#updateStatusModal" style="background:#1e3a8a; border-color:#1e3a8a; color:#ffffff; font-weight:600; border-radius:4px; margin-right:5px; padding:6px 14px;">
                    <i class="fa fa-refresh"></i> Update Status
                  </button>
                <?php } ?>

                <?php if(!empty($can_assign_operation)){ ?>
                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#assignOperationModal" style="background:#1e3a8a; border-color:#1e3a8a; color:#ffffff; font-weight:600; border-radius:4px; margin-right:5px; padding:6px 14px;">
                    <i class="fa fa-user-plus"></i> Assign to Operations
                  </button>
                <?php } ?>

                <?php if(!empty($can_edit) && !empty($can_approve) && $order['status'] !== 'Sales'){ ?>
                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#convertToSalesModal" style="background:#1e3a8a; border-color:#1e3a8a; color:#ffffff; font-weight:600; border-radius:4px; margin-right:5px; padding:6px 14px;">
                    <i class="fa fa-line-chart"></i> Convert to Sales
                  </button>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="box box-default" style="border-radius:6px; margin-bottom:15px; border-top:3px solid #1e3a8a;">
        <div class="box-body" style="padding:12px 18px;">
          <div class="row text-center">
            <div class="col-xs-6 col-sm-3" style="border-right:1px solid #e2e8f0;">
              <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Database Ref</div>
              <div style="font-size:15px; font-weight:700; color:#0f172a; margin-top:3px;">
                <span class="label" style="background:#f1f5f9; color:#0f172a; border:1px solid #cbd5e1; font-size:13px; font-weight:700;">
                  <?php echo htmlspecialchars($order['db_ref_no'] ?: '-'); ?>
                </span>
              </div>
            </div>

            <div class="col-xs-6 col-sm-3" style="border-right:1px solid #e2e8f0;">
              <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Lead Ref</div>
              <div style="font-size:15px; font-weight:700; color:#0f172a; margin-top:3px;">
                <?php if (!empty($order['lead_ref_no'])) { ?>
                  <span class="label" style="background:#f8fafc; color:#065f46; border:1px solid #e2e8f0; font-size:13px; font-weight:700;">
                    <?php echo htmlspecialchars($order['lead_ref_no']); ?>
                  </span>
                <?php } else { ?>
                  <span style="color:#94a3b8; font-size:13px;">-</span>
                <?php } ?>
              </div>
            </div>

            <div class="col-xs-6 col-sm-3" style="border-right:1px solid #e2e8f0;">
              <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Order Ref</div>
              <div style="font-size:15px; font-weight:700; color:#0f172a; margin-top:3px;">
                <span class="label" style="background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; font-size:13px; font-weight:700;">
                  <?php echo htmlspecialchars($order['order_no']); ?>
                </span>
              </div>
            </div>

            <div class="col-xs-6 col-sm-3">
              <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Sales Ref</div>
              <div style="font-size:15px; font-weight:700; color:#0f172a; margin-top:3px;">
                <?php if (!empty($order['sales_ref_no'])) { ?>
                  <span class="label" style="background:#f8fafc; color:#0f172a; border:1px solid #86efac; font-size:13px; font-weight:700;">
                    <?php echo htmlspecialchars($order['sales_ref_no']); ?>
                  </span>
                <?php } else { ?>
                  <span style="color:#94a3b8; font-size:13px;">Pending Sales</span>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="my-profile">
        <div class="box box-default">
          <div class="box-header">
            <div class="col-sm-12">
              <h3 class="box-title" style="text-transform:capitalize">
                <b><?php echo htmlspecialchars($order['client_name'] ?: ($order['lead_customer'] ?: 'Client #'.$order['client_id'])); ?></b>
                <?php 
                  $st = $order['status'];
                  $badge_bg = '#1e3a8a';
                  if (in_array($st, array('Completed', 'Fulfilled', 'Approved'))) $badge_bg = '#0f172a';
                  elseif (in_array($st, array('Sales'))) $badge_bg = '#0f172a';
                  elseif (in_array($st, array('Pending Sales Approval'))) $badge_bg = '#475569';
                  elseif (in_array($st, array('Cancelled', 'Rejected', 'Lost'))) $badge_bg = '#dc2626';
                ?>
                <span class="label" style="background:<?php echo $badge_bg; ?>; color:#ffffff; font-weight:700; padding:2px 8px; border-radius:3px; font-size:12px; margin-left:6px; letter-spacing:0.5px;">
                  <?php echo htmlspecialchars($st); ?>
                </span>
                <?php if(!empty($order['vertical_code'])){ ?>
                  <span class="label label-default" style="font-weight:600; font-size:11px; margin-left:4px;">
                    <?php echo htmlspecialchars(strtoupper($order['vertical_code'])); ?>
                  </span>
                <?php } ?>
              </h3>
            </div>
          </div>

          <div class="box-body">
            <div class="row">
              <div class="col-md-6">
                <div class="table-responsive border">
                  <table class="table">
                    <tr>
                      <th scope="col" style="width:40%;">Order No.</th>
                      <td><strong><?php echo htmlspecialchars($order['order_no']); ?></strong></td>
                    </tr>
                    <tr>
                      <th scope="col">Database No.</th>
                      <td><?php echo htmlspecialchars($order['db_ref_no'] ?: '-'); ?></td>
                    </tr>
                    <?php if (!empty($order['lead_ref_no'])) { ?>
                    <tr>
                      <th scope="col">Lead No.</th>
                      <td>
                        <?php echo htmlspecialchars($order['lead_ref_no']); ?>
                        <?php if(!empty($order['lead_id'])){ ?>
                          <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($order['lead_id']); ?>" target="_blank" style="margin-left:6px; font-size:12px;">
                            <i class="fa fa-external-link"></i> View Lead
                          </a>
                        <?php } ?>
                      </td>
                    </tr>
                    <?php } ?>
                    <tr>
                      <th scope="col">Service Title</th>
                      <td><?php echo htmlspecialchars($order['service_title'] ?: '-'); ?></td>
                    </tr>
                    <tr>
                      <th scope="col">Vertical / Category</th>
                      <td><?php echo htmlspecialchars($order['vertical_code'] ?: 'General'); ?></td>
                    </tr>
                    <tr>
                      <th scope="col">Monthly Recurring Revenue</th>
                      <td><strong style="color:#0f172a;">AED <?php echo number_format((float)($order['mrr'] ?: $order['pricing_amount']), 2); ?></strong></td>
                    </tr>
                    <tr>
                      <th scope="col">Order Status</th>
                      <td>
                        <span class="label" style="background:<?php echo $badge_bg; ?>; color:#ffffff; font-weight:600; font-size:11px;">
                          <?php echo htmlspecialchars($order['status']); ?>
                        </span>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>

              <div class="col-md-6">
                <div class="table-responsive border">
                  <table class="table">
                    <tr>
                      <th scope="col" style="width:40%;">Assigned Staff</th>
                      <td><?php echo htmlspecialchars($order['staff_name'] ?: '-'); ?></td>
                    </tr>
                    <tr>
                      <th scope="col">Assigned Department</th>
                      <td><?php echo htmlspecialchars($order['assigned_dept'] ?: '-'); ?></td>
                    </tr>
                    <tr>
                      <th scope="col">Contact Number</th>
                      <td><?php echo htmlspecialchars($order['lead_contact'] ?: '-'); ?></td>
                    </tr>
                    <tr>
                      <th scope="col">Email</th>
                      <td><?php echo htmlspecialchars($order['lead_email'] ?: '-'); ?></td>
                    </tr>
                    <tr>
                      <th scope="col">Next Follow-Up Date</th>
                      <td>
                        <?php if(!empty($order['followup_date'])){ ?>
                          <strong><?php echo date('d-m-Y', strtotime($order['followup_date'])); ?></strong>
                          <?php if(!empty($order['followup_time'])){ echo ' ' . htmlspecialchars($order['followup_time']); } ?>
                        <?php } else { echo '-'; } ?>
                      </td>
                    </tr>
                    <tr>
                      <th scope="col">Created Date</th>
                      <td><?php echo !empty($order['created_at']) ? date('d-m-Y H:i', strtotime($order['created_at'])) : '-'; ?></td>
                    </tr>
                    <tr>
                      <th scope="col">Last Updated</th>
                      <td><?php echo !empty($order['updated_at']) ? date('d-m-Y H:i', strtotime($order['updated_at'])) : '-'; ?></td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <?php if(!empty($order['service_scope']) || !empty($order['operational_instructions'])){ ?>
        <div class="box box-default">
          <div class="box-header">
            <h3 class="box-title">Order Instructions &amp; Service Scope</h3>
          </div>
          <div class="box-body">
            <div class="row">
              <?php if(!empty($order['service_scope'])){ ?>
              <div class="col-md-6">
                <label style="font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase;">Service Scope</label>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px; padding:12px; font-size:13px; color:#1e293b; min-height:80px;">
                  <?php echo nl2br(htmlspecialchars($order['service_scope'])); ?>
                </div>
              </div>
              <?php } ?>
              <?php if(!empty($order['operational_instructions'])){ ?>
              <div class="col-md-6">
                <label style="font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase;">Operational Instructions / Remarks</label>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px; padding:12px; font-size:13px; color:#1e293b; min-height:80px;">
                  <?php echo nl2br(htmlspecialchars($order['operational_instructions'])); ?>
                </div>
              </div>
              <?php } ?>
            </div>
          </div>
        </div>
        <?php } ?>

        <div class="box box-default">
          <div class="box-header">
            <h3 class="box-title">Order Activity &amp; History</h3>
          </div>
          <div class="box-body">
            <?php if(!empty($activity_logs)){ ?>
              <div class="table-responsive">
                <table class="table table-striped table-bordered" style="font-size:13px;">
                  <thead>
                    <tr>
                      <th style="width:18%;">Date &amp; Time</th>
                      <th style="width:20%;">Action</th>
                      <th style="width:18%;">Action By</th>
                      <th>Notes / Remarks</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach($activity_logs as $log){ ?>
                      <tr>
                        <td><?php echo !empty($log['created_at']) ? date('d-m-Y H:i', strtotime($log['created_at'])) : '-'; ?></td>
                        <td><span class="label label-info"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $log['action']))); ?></span></td>
                        <td><?php echo htmlspecialchars($log['action_by_name'] ?: 'User #'.$log['action_by']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($log['notes'] ?: '-')); ?></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            <?php } else { ?>
              <div class="alert alert-info" style="margin-bottom:0;">No activity history recorded for this order yet.</div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

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
              <option value="Open" <?php echo ($order['status'] === 'Open') ? 'selected' : ''; ?>>Open</option>
              <option value="In Progress" <?php echo ($order['status'] === 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
              <option value="Follow Up" <?php echo ($order['status'] === 'Follow Up') ? 'selected' : ''; ?>>Follow Up</option>
              <option value="Under Review" <?php echo ($order['status'] === 'Under Review') ? 'selected' : ''; ?>>Under Review</option>
              <option value="Pending Sales Approval" <?php echo ($order['status'] === 'Pending Sales Approval') ? 'selected' : ''; ?>>Pending Sales Approval</option>
              <option value="Completed" <?php echo ($order['status'] === 'Completed') ? 'selected' : ''; ?>>Completed</option>
              <option value="Fulfilled" <?php echo ($order['status'] === 'Fulfilled') ? 'selected' : ''; ?>>Fulfilled</option>
              <option value="Sales" <?php echo ($order['status'] === 'Sales') ? 'selected' : ''; ?>>Sales</option>
              <option value="Cancelled" <?php echo ($order['status'] === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
              <option value="Rejected" <?php echo ($order['status'] === 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
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

<div class="modal fade" id="approveOrderModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width: 480px;">
    <div class="modal-content" style="border-radius: 6px; overflow: hidden;">
      <form action="<?php echo base_url(); ?>manager/orders/approve/<?php echo $order['id']; ?>" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="modal-header" style="background: #0f172a; color: #ffffff; padding: 14px 18px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #ffffff; opacity: 0.8;"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" style="font-size: 16px; font-weight: 700; color: #ffffff; margin: 0;">Approve Order</h4>
        </div>
        <div class="modal-body" style="padding: 20px; background: #ffffff;">
          <p style="font-size: 13px; color: #475569; margin-bottom: 14px;">Are you sure you want to approve this order? This will authorize processing and move it to approved state.</p>
          <div class="form-group" style="margin-bottom: 0;">
            <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #475569;">Approval Notes (Optional)</label>
            <textarea name="remarks" class="form-control" rows="3" placeholder="Enter approval remarks..." style="border: 1px solid #cbd5e1; border-radius: 4px; resize: vertical;"></textarea>
          </div>
        </div>
        <div class="modal-footer" style="padding: 12px 18px; background: #f8fafc; border-top: 1px solid #e2e8f0;">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success" style="background:#0f172a; border-color:#0f172a;">Confirm Approval</button>
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
      <form action="<?php echo base_url(); ?>manager/orders/assign_operation/<?php echo $order['id']; ?>" method="post">
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
            <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #475569;">Handover Notes (Optional)</label>
            <textarea name="remarks" class="form-control" rows="3" placeholder="Enter instructions for assigned staff..." style="border: 1px solid #cbd5e1; border-radius: 4px; resize: vertical;"></textarea>
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

<div class="modal fade" id="convertToSalesModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width: 480px;">
    <div class="modal-content" style="border-radius: 6px; overflow: hidden;">
      <form action="<?php echo base_url(); ?>manager/orders/convert_to_sales/<?php echo $order['id']; ?>" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="modal-header" style="background: #0f172a; color: #ffffff; padding: 14px 18px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #ffffff; opacity: 0.8;"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" style="font-size: 16px; font-weight: 700; color: #ffffff; margin: 0;">Convert to Sales</h4>
        </div>
        <div class="modal-body" style="padding: 20px; background: #ffffff;">
          <p style="font-size: 13px; color: #475569; margin-bottom: 14px;">Confirm conversion of this order into a completed Sales record? A unique Sales Reference Number will be generated.</p>
          <div class="form-group" style="margin-bottom: 0;">
            <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #475569;">Sales Completion Notes (Optional)</label>
            <textarea name="remarks" class="form-control" rows="3" placeholder="Enter completion notes..." style="border: 1px solid #cbd5e1; border-radius: 4px; resize: vertical;"></textarea>
          </div>
        </div>
        <div class="modal-footer" style="padding: 12px 18px; background: #f8fafc; border-top: 1px solid #e2e8f0;">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success" style="background:#0f172a; border-color:#0f172a;">Confirm Sales</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
