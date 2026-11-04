<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
    <section class="content-header" style="padding: 15px 15px 5px 15px;">
        <h1 style="font-size: 22px; font-weight: 800; color: #000000; margin: 0; letter-spacing: -0.02em;"> <?php echo $info['page_heading']; ?> </h1>
    </section>

    <section class="content" style="padding-top: 10px;">
        <div class="row">
            <div class="col-xs-12">
                <div class="box p-4" style="border: 1px solid #e2e8f0; border-radius: 4px; margin-bottom: 15px; background: #ffffff;">
                    <div class="x_row">
                        <div class="x_col">
                            <form class="form-inline head_filter form-inline-mob" method="get">
                                <div class="form-group">
                                    <input type="text" class="input-md form-control" name="client" placeholder="Search Client / Order No." value="<?php echo htmlspecialchars($this->input->get('client') ?? ''); ?>" autocomplete="off" style="border: 1px solid #cbd5e1; border-radius: 4px;" />
                                </div>
                                <div class="form-group">
                                    <select class="form-control" name="vertical" style="border: 1px solid #cbd5e1; border-radius: 4px;">
                                        <option value="">All Verticals</option>
                                        <option value="accounting" <?php echo ($this->input->get('vertical') === 'accounting') ? 'selected' : ''; ?>>Accounting / VAT</option>
                                        <option value="telecom" <?php echo ($this->input->get('vertical') === 'telecom') ? 'selected' : ''; ?>>Telecom (Du/Etisalat)</option>
                                        <option value="it" <?php echo ($this->input->get('vertical') === 'it') ? 'selected' : ''; ?>>IT Services</option>
                                        <option value="corporate" <?php echo ($this->input->get('vertical') === 'corporate') ? 'selected' : ''; ?>>Corporate Services</option>
                                    </select>
                                </div>
                                <?php if (!isset($status_filter) || ($status_filter !== 'sales' && $status_filter !== 'Sales')) { ?>
                                <div class="form-group">
                                    <select class="form-control" name="status" style="border: 1px solid #cbd5e1; border-radius: 4px;">
                                        <option value="">All Statuses</option>
                                        <option value="open" <?php echo ($this->input->get('status') === 'open' || (isset($status_filter) && $status_filter === 'open')) ? 'selected' : ''; ?>>Open Orders</option>
                                        <option value="followups" <?php echo ($this->input->get('status') === 'followups' || (isset($status_filter) && $status_filter === 'followups')) ? 'selected' : ''; ?>>Orders Follow-up</option>
                                        <option value="completed" <?php echo ($this->input->get('status') === 'completed' || (isset($status_filter) && $status_filter === 'completed')) ? 'selected' : ''; ?>>Completed Orders</option>
                                        <option value="closed" <?php echo ($this->input->get('status') === 'closed' || (isset($status_filter) && $status_filter === 'closed')) ? 'selected' : ''; ?>>Closed Orders</option>
                                        <option value="rejected_lost" <?php echo ($this->input->get('status') === 'rejected_lost' || (isset($status_filter) && $status_filter === 'rejected_lost')) ? 'selected' : ''; ?>>Rejected / Lost Orders</option>
                                        <option value="Submitted" <?php echo ($this->input->get('status') === 'Submitted') ? 'selected' : ''; ?>>Submitted</option>
                                        <option value="Approved" <?php echo ($this->input->get('status') === 'Approved') ? 'selected' : ''; ?>>Approved</option>
                                        <option value="In Progress" <?php echo ($this->input->get('status') === 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                                        <option value="Fulfilled" <?php echo ($this->input->get('status') === 'Fulfilled') ? 'selected' : ''; ?>>Fulfilled</option>
                                        <option value="Sales" <?php echo ($this->input->get('status') === 'Sales') ? 'selected' : ''; ?>>Sales</option>
                                        <option value="Cancelled" <?php echo ($this->input->get('status') === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>
                                <?php } ?>
                                <div class="form-group">
                                    <div class="input-daterange input-group" id="datepicker" style="width: 100%;">
                                        <input type="text" class="input-md form-control datepicker" name="d1" placeholder="Create From" value="<?php echo htmlspecialchars($this->input->get('d1') ?? ''); ?>" autocomplete="off" style="border: 1px solid #cbd5e1; border-radius: 4px 0 0 4px;" />
                                        <span class="input-group-addon" style="background: #f8fafc; border: 1px solid #cbd5e1; border-left: none; border-right: none; color: #475569; font-weight: 600;">To</span>
                                        <input type="text" class="input-md form-control datepicker" name="d2" placeholder="Create To" value="<?php echo htmlspecialchars($this->input->get('d2') ?? ''); ?>" autocomplete="off" style="border: 1px solid #cbd5e1; border-radius: 0 4px 4px 0;" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-default" class="btn btn-primary" style="font-weight: 600; padding: 6px 16px;">Search</button>
                                    <a href="<?php echo htmlspecialchars($reset_url ?? base_url('manager/orders')); ?>" class="btn btn-default" class="btn btn-default" style="font-weight: 600; padding: 6px 14px;"> Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="box" style="border: 1px solid #e2e8f0; border-radius: 4px; background: #ffffff;">
                    <div class="box-body" style="padding: 15px;">
                        <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
                        <?php echo get_message($this->session->flashdata('message'), 'message'); ?>
                        <?php if(isset($orders_list) && !empty($orders_list)) { ?>
                        <div class="table-responsive">
                            <table id="example_orders" class="table table-hover table-bordered table-striped" style="width:100%; font-size: 13px;">
                                <thead>
                                    <tr style="background: #000000; color: #ffffff;">
                                        <th style="color: #ffffff; font-weight: 700;">Order No.</th>
                                        <th style="color: #ffffff; font-weight: 700;">Customer</th>
                                        <th style="color: #ffffff; font-weight: 700;">Service Type</th>
                                        <th style="color: #ffffff; font-weight: 700;">MRR</th>
                                        <th style="color: #ffffff; font-weight: 700;">Assigned Staff</th>
                                        <th style="color: #ffffff; font-weight: 700;">Created</th>
                                        <th style="color: #ffffff; font-weight: 700;">Next Follow-Up</th>
                                        <th style="color: #ffffff; font-weight: 700;">Status</th>
                                        <th style="color: #ffffff; font-weight: 700; text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($orders_list as $ord){ ?>
                                    <tr>
                                        <td><a href="<?php echo base_url(); ?>manager/orders/view/<?php echo $ord['id']; ?>" style="color: #000000; font-weight: 700; font-family: monospace;"><strong><?php echo !empty($ord['order_no']) ? htmlspecialchars($ord['order_no']) : ('O' . str_pad($ord['id'], 10, '0', STR_PAD_LEFT)); ?></strong></a></td>
                                        <td>
                                            <a href="<?php echo base_url(); ?>manager/orders/view/<?php echo $ord['id']; ?>" style="color: #000000; font-weight: 700; text-decoration: underline;"><?php echo htmlspecialchars($ord['client_name'] ?: ($ord['lead_customer'] ?: 'Client #'.$ord['client_id'])); ?></a>
                                            <?php if(!empty($ord['lead_code'])){ ?>
                                                <br><small style="color: #64748b; font-weight: 600;">Lead No: <?php echo htmlspecialchars($ord['lead_code']); ?></small>
                                            <?php } ?>
                                        </td>
                                        <td style="color: #0f172a; font-weight: 600;"><?php echo htmlspecialchars($ord['service_title'] ?: ($ord['vertical_code'] ?: '-')); ?></td>
                                        <td style="font-weight: 800; color: #000000;">AED <?php echo number_format((float)($ord['mrr'] ?: $ord['pricing_amount']), 2); ?></td>
                                        <td style="color: #0f172a; font-weight: 600;"><?php echo htmlspecialchars($ord['staff_name'] ?: ($ord['assigned_dept'] ?: '-')); ?></td>
                                        <td><span class="no_show"><?php echo !empty($ord['created_at']) ? date('Ymd', strtotime($ord['created_at'])) : ''; ?></span><?php echo !empty($ord['created_at']) ? date('d-m-Y', strtotime($ord['created_at'])) : '-'; ?></td>
                                        <td>
                                            <?php if (!empty($ord['followup_date'])) { ?>
                                                <strong style="color: #000000;"><?php echo date('d-m-Y', strtotime($ord['followup_date'])); ?></strong>
                                                <?php if (!empty($ord['followup_time'])) { ?>
                                                    <br><small style="color: #64748b; font-weight: 600;"><?php echo htmlspecialchars($ord['followup_time']); ?></small>
                                                <?php } ?>
                                            <?php } else { echo '-'; } ?>
                                        </td>
                                        <td>
                                            <?php 
                                                $st = $ord['status'];
                                            ?>
                                            <?php
                                                $badge_color = '#6b7280';
                                                if(in_array($st, ['Open','Submitted','In Progress'])) $badge_color = '#2563eb';
                                                elseif(in_array($st, ['Approved','Fulfilled','Completed'])) $badge_color = '#16a34a';
                                                elseif(in_array($st, ['Closed','Sales'])) $badge_color = '#7c3aed';
                                                elseif(in_array($st, ['Cancelled','Rejected','Lost'])) $badge_color = '#dc2626';
                                                elseif($st === 'Pending Sales Approval') $badge_color = '#d97706';
                                            ?>
                                            <span class="label" style="background: <?php echo $badge_color; ?>; color: #ffffff; font-weight: 600; font-size: 11px; padding: 3px 8px; border-radius: 3px;"><?php echo htmlspecialchars($st); ?></span>
                                        </td>
                                        <td style="text-align: center;">
                                            <?php if (isset($status_filter) && $status_filter === 'followups'): ?>
                                                <div class="btn-group" style="min-width: 85px;">
                                                    <button type="button" class="btn btn-sm btn-default" style="border: 1px solid #cbd5e1; font-weight: 600;">Action</button>
                                                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false" style="background:#0072f0; border-color:#0072f0;">
                                                        <span class="caret"></span>
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-right" role="menu" style="border: 1px solid #cbd5e1; border-radius: 4px; box-shadow: 0 4px 14px rgba(0,0,0,0.12);">
                                                        <li><a href="<?php echo base_url(); ?>manager/orders/view/<?php echo $ord['id']; ?>" style="font-weight: 600;"><i class="fa fa-eye" style="margin-right: 6px; color:#0072f0;"></i> Preview</a></li>
                                                        <li><a href="<?php echo base_url(); ?>manager/orders/edit_pending/<?php echo $ord['id']; ?>" style="font-weight: 600;"><i class="fa fa-pencil" style="margin-right: 6px; color:#0072f0;"></i> Edit</a></li>
                                                        <li><a href="<?php echo base_url(); ?>manager/orders/add_followup/<?php echo $ord['id']; ?>" style="font-weight: 600;"><i class="fa fa-commenting-o" style="margin-right: 6px; color:#0072f0;"></i> Add Follow-Up</a></li>
                                                    </ul>
                                                </div>
                                            <?php elseif (!empty($can_edit_orders)): ?>
                                                <div class="btn-group" style="min-width: 85px;">
                                                    <button type="button" class="btn btn-sm btn-default" >Action</button>
                                                    <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                        <span class="caret"></span>
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-right" role="menu" style="border: 1px solid #000000; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                                        <li><a href="<?php echo base_url(); ?>manager/orders/view/<?php echo $ord['id']; ?>" data-toggle="tooltip" title="View" style="font-weight: 600;"><i class="fa fa-eye" style="margin-right: 6px;"></i> View</a></li>
                                                        <li><a href="javascript:void(0);" class="btn-open-followup"
                                                            data-order-id="<?php echo $ord['id']; ?>" 
                                                            data-order-no="<?php echo htmlspecialchars($ord['order_no']); ?>" 
                                                            data-client-name="<?php echo htmlspecialchars($ord['client_name'] ?: ($ord['lead_customer'] ?: 'Client #'.$ord['client_id'])); ?>" 
                                                            data-status="<?php echo htmlspecialchars($ord['status']); ?>" 
                                                            data-fdate="<?php echo !empty($ord['followup_date']) ? $ord['followup_date'] : ''; ?>" 
                                                            data-ftime="<?php echo !empty($ord['followup_time']) ? htmlspecialchars($ord['followup_time']) : ''; ?>" 
                                                            data-remarks="<?php echo htmlspecialchars(!empty($ord['operational_instructions']) ? $ord['operational_instructions'] : ''); ?>" style="font-weight: 600;"><i class="fa fa-commenting-o" style="margin-right: 6px;"></i> Add Follow-up</a></li>
                                                        <li><a href="<?php echo base_url(); ?>manager/orders/edit_pending/<?php echo $ord['id']; ?>" data-toggle="tooltip" title="Make Changes" style="font-weight: 600;"><i class="fa fa-pencil" style="margin-right: 6px;"></i> Make Changes</a></li>
                                                        <?php if ($ord['status'] === 'Fulfilled' || $ord['status'] === 'Completed'): ?>
                                                            <li><a href="<?php echo base_url(); ?>manager/orders/mark_closed_sold/<?php echo $ord['id']; ?>" onclick="return confirm('Mark this completed order as Closed / Sold?');" data-toggle="tooltip" title="Mark as Closed / Sold" style="font-weight: 600;"><i class="fa fa-flag-checkered text-success" style="margin-right: 6px;"></i> Mark as Closed / Sold</a></li>
                                                        <?php endif; ?>
                                                        <?php if (($status_filter === 'closed' || $ord['status'] === 'Closed' || $ord['status'] === 'Sales') && ($this->session->userdata('manager_role') == 1 || $this->session->userdata('manager_id') == 1)): ?>
                                                            <li><a href="javascript:void(0);" class="btn-open-assign-order" data-order-id="<?php echo $ord['id']; ?>" data-order-no="<?php echo htmlspecialchars($ord['order_no']); ?>" data-assigned-to="<?php echo (int)($ord['assigned_to'] ?? 0); ?>" data-assigned-dept="<?php echo htmlspecialchars($ord['assigned_dept'] ?? 'Operations'); ?>" style="font-weight: 600;"><i class="fa fa-user-plus text-primary" style="margin-right: 6px;"></i> Assign Order</a></li>
                                                        <?php endif; ?>
                                                        <?php if ($ord['approval_status'] === 'pending' || $ord['status'] === 'Submitted'): ?>
                                                            <li><a href="<?php echo base_url(); ?>manager/orders/quick_accept/<?php echo $ord['id']; ?>" onclick="return confirm('Accept and approve this order?');" data-toggle="tooltip" title="Accept" style="font-weight: 600;"><i class="fa fa-check" style="margin-right: 6px;"></i> Accept</a></li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </div>
                                            <?php else: ?>
                                                <div class="btn-group" style="min-width: 85px;">
                                                    <button type="button" class="btn btn-sm btn-default" >Action</button>
                                                    <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                        <span class="caret"></span>
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-right" role="menu" style="border: 1px solid #000000; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                                        <li><a href="<?php echo base_url(); ?>manager/orders/view/<?php echo $ord['id']; ?>" data-toggle="tooltip" title="Preview" style="font-weight: 600;"><i class="fa fa-eye" style="margin-right: 6px;"></i> Preview</a></li>
                                                        <li><a href="javascript:void(0);" class="btn-open-followup"
                                                            data-order-id="<?php echo $ord['id']; ?>" 
                                                            data-order-no="<?php echo htmlspecialchars($ord['order_no']); ?>" 
                                                            data-client-name="<?php echo htmlspecialchars($ord['client_name'] ?: ($ord['lead_customer'] ?: 'Client #'.$ord['client_id'])); ?>" 
                                                            data-status="<?php echo htmlspecialchars($ord['status']); ?>" 
                                                            data-fdate="<?php echo !empty($ord['followup_date']) ? $ord['followup_date'] : ''; ?>" 
                                                            data-ftime="<?php echo !empty($ord['followup_time']) ? htmlspecialchars($ord['followup_time']) : ''; ?>" 
                                                            data-remarks="<?php echo htmlspecialchars(!empty($ord['operational_instructions']) ? $ord['operational_instructions'] : ''); ?>" style="font-weight: 600;"><i class="fa fa-commenting-o" style="margin-right: 6px;"></i> Add Follow-up</a></li>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php } else { ?>
                            <div class="alert alert-info" style="margin-bottom:0;"> No Data Found </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="orderFollowupModal" tabindex="-1" role="dialog" aria-labelledby="orderFollowupModalLabel">
    <div class="modal-dialog" role="document" style="max-width: 680px;">
        <div class="modal-content" style="border-radius: 6px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); overflow: hidden;">
            <div class="modal-header" style="background: #2563eb; color: #ffffff; padding: 14px 20px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #ffffff; opacity: 0.8;"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="orderFollowupModalLabel" style="font-size: 16px; font-weight: 700; color: #ffffff; margin: 0;">
                    <i class="fa fa-calendar-check-o" style="margin-right: 6px;"></i> Order Follow-Up
                </h4>
                <div style="font-size: 12px; color: #dbeafe; margin-top: 3px;">
                    Order: <strong id="modalOrderNoDisplay" style="color: #ffffff;"></strong> | Client: <strong id="modalClientNameDisplay" style="color: #ffffff;"></strong>
                </div>
            </div>
            <form id="orderFollowupForm" method="post">
                <input type="hidden" name="order_id" id="modalOrderId" value="">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-body" style="padding: 20px; background: #ffffff;">
                    <div id="modalFollowupAlert" style="display: none;" class="alert"></div>

                    <!-- Follow Up Field Box matching Add Database -->
                    <div class="box box-default with-border" style="border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 16px;">
                        <div class="box-header with-border" style="background: #f8fafc; padding: 10px 14px; border-bottom: 1px solid #e2e8f0;">
                            <h4 class="box-title" style="font-size: 13px; font-weight: 700; color: #0f172a; margin: 0;">Follow Up Field</h4>
                        </div>
                        <div class="box-body" style="padding: 14px;">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label style="font-size: 12px; font-weight: 600; color: #475569;">Next Follow Up Date</label>
                                        <input type="text" class="form-control datepicker" name="followup_date" id="modalFollowupDate" placeholder="YYYY-MM-DD" autocomplete="off" style="border-radius: 4px;">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label style="font-size: 12px; font-weight: 600; color: #475569;">Next Follow Up Time</label>
                                        <input type="text" class="form-control" name="followup_time" id="modalFollowupTime" placeholder="e.g. 11:30 AM" autocomplete="off" style="border-radius: 4px;">
                                    </div>
                                </div>
                                <div class="col-xs-12">
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label style="font-size: 12px; font-weight: 600; color: #475569;">Follow-Up Remark <span class="text-danger">*</span></label>
                                        <textarea name="remarks" id="modalFollowupRemarks" class="form-control" rows="3" placeholder="Enter follow-up details..." required style="border-radius: 4px; resize: vertical;"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Meeting Field Box matching Add Database -->
                    <div class="box box-default with-border" style="border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 16px;">
                        <div class="box-header with-border" style="background: #f8fafc; padding: 10px 14px; border-bottom: 1px solid #e2e8f0;">
                            <h4 class="box-title" style="font-size: 13px; font-weight: 700; color: #0f172a; margin: 0;">Meeting Field</h4>
                        </div>
                        <div class="box-body" style="padding: 14px;">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label style="font-size: 12px; font-weight: 600; color: #475569;">Next Meeting Date</label>
                                        <input type="text" class="form-control datepicker" name="m_date" id="modalMeetingDate" placeholder="YYYY-MM-DD" autocomplete="off" style="border-radius: 4px;">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label style="font-size: 12px; font-weight: 600; color: #475569;">Next Meeting Time</label>
                                        <input type="text" class="form-control" name="m_time" id="modalMeetingTime" placeholder="e.g. 03:00 PM" autocomplete="off" style="border-radius: 4px;">
                                    </div>
                                </div>
                                <div class="col-xs-12">
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label style="font-size: 12px; font-weight: 600; color: #475569;">Meeting Remark</label>
                                        <input type="text" class="form-control" name="m_remark" id="modalMeetingRemark" placeholder="e.g. Client Office / Online Zoom" style="border-radius: 4px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="font-size: 12px; font-weight: 600; color: #475569;">Order Status</label>
                        <select name="order_status" id="modalOrderStatus" class="form-control" style="border-radius: 4px;">
                            <option value="Approved">Approved</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Follow Up">Follow Up</option>
                            <option value="Under Review">Under Review</option>
                            <option value="Pending Sales Approval">Pending Sales Approval</option>
                            <option value="Completed">Completed</option>
                            <option value="Rejected">Rejected</option>
                            <option value="Lost">Lost</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 12px 18px;">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="btnSaveFollowup" class="btn btn-primary" style="background: #2563eb; border-color: #1d4ed8; color: #ffffff; font-weight: 700; padding: 6px 20px;">Save Follow-up</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="assignClosedOrderModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 460px;">
        <div class="modal-content" style="border-radius: 6px; border: 1px solid #000000; overflow: hidden;">
            <form action="<?php echo base_url(); ?>manager/orders/assign_closed_order" method="post">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="order_id" id="assign_order_id" value="">
                <div class="modal-header" style="background: #000000; color: #ffffff; padding: 14px 18px; border-bottom: 1px solid #000000;">
                    <button type="button" class="close" data-dismiss="modal" style="color: #ffffff; opacity: 0.8;">&times;</button>
                    <h4 class="modal-title" style="font-size: 15px; font-weight: 700; color: #ffffff; margin: 0;">
                        <i class="fa fa-user-plus" style="margin-right: 6px;"></i> Assign Closed Order (<span id="assign_order_no_display"></span>)
                    </h4>
                </div>
                <div class="modal-body" style="padding: 20px; background: #ffffff;">
                    <div class="form-group">
                        <label style="font-size: 12px; font-weight: 700; color: #000000; text-transform: uppercase;">Assign To Department <span class="text-danger">*</span></label>
                        <select name="assigned_dept" id="assign_order_dept" class="form-control" style="border: 1px solid #000000; border-radius: 4px;">
                            <option value="Operations">Operations</option>
                            <option value="Back Office">Back Office</option>
                            <option value="Finance">Finance</option>
                            <option value="IT">IT</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="font-size: 12px; font-weight: 700; color: #000000; text-transform: uppercase;">Assign To Staff <span class="text-danger">*</span></label>
                        <select name="assigned_to" id="assign_order_staff" class="form-control" style="border: 1px solid #000000; border-radius: 4px;" required>
                            <option value="">-- Select Staff User --</option>
                            <?php if (!empty($staff_list)): ?>
                                <?php foreach ($staff_list as $st): ?>
                                    <option value="<?php echo $st['id']; ?>"><?php echo htmlspecialchars($st['name']); ?> <?php echo !empty($st['department']) ? '('.htmlspecialchars($st['department']).')' : ''; ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 12px 18px; background: #f8fafc; border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="background: #ffffff; border: 1px solid #000000; color: #000000; font-weight: 600;">Cancel</button>
                    <button type="submit" class="btn btn-default" style="background: #000000; border: 1px solid #000000; color: #ffffff; font-weight: 700; padding: 6px 18px;">Confirm Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#example_orders')) {
        $('#example_orders').DataTable().destroy();
    }
    $('#example_orders').DataTable({
        "pageLength": 50,
        responsive: true
    });

    if ($.fn.datepicker) {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
    }

    $(document).on('click', '.btn-open-followup', function(e) {
        e.preventDefault();
        var orderId = $(this).data('order-id');
        var orderNo = $(this).data('order-no');
        var clientName = $(this).data('client-name');
        var status = $(this).data('status');
        var fdate = $(this).data('fdate');
        var ftime = $(this).data('ftime');
        var remarks = $(this).data('remarks');

        $('#modalOrderId').val(orderId);
        $('#modalOrderNoDisplay').text(orderNo);
        $('#modalClientNameDisplay').text(clientName);
        $('#modalOrderStatus').val(status);
        $('#modalFollowupDate').val(fdate);
        $('#modalFollowupTime').val(ftime);
        $('#modalFollowupRemarks').val('');
        $('#modalFollowupAlert').hide().removeClass('alert-success alert-danger').text('');
        $('#btnSaveFollowup').prop('disabled', false).text('Save Follow-up');

        $('#orderFollowupModal').modal('show');
    });

    
    $(document).on('click', '.btn-open-assign-order', function(e) {
        e.preventDefault();
        var orderId = $(this).data('order-id');
        var orderNo = $(this).data('order-no');
        var assignedTo = $(this).data('assigned-to');
        var assignedDept = $(this).data('assigned-dept');
        $('#assign_order_id').val(orderId);
        $('#assign_order_no_display').text(orderNo);
        if (assignedDept) {
            $('#assign_order_dept').val(assignedDept);
        }
        if (assignedTo) {
            $('#assign_order_staff').val(assignedTo);
        }
        $('#assignClosedOrderModal').modal('show');
    });

    $('#orderFollowupForm').on('submit', function(e) {
        e.preventDefault();
        var $btn = $('#btnSaveFollowup');
        $btn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: '<?php echo base_url(); ?>manager/orders/ajax_save_followup',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res && res.success) {
                    $('#modalFollowupAlert').removeClass('alert-danger').addClass('alert alert-success').text(res.message || 'Follow-up saved successfully.').show();
                    setTimeout(function() {
                        $('#orderFollowupModal').modal('hide');
                        location.reload();
                    }, 800);
                } else {
                    $('#modalFollowupAlert').removeClass('alert-success').addClass('alert alert-danger').text((res && res.message) ? res.message : 'Failed to save follow-up.').show();
                    $btn.prop('disabled', false).text('Save Follow-up');
                }
            },
            error: function() {
                $('#modalFollowupAlert').removeClass('alert-success').addClass('alert alert-danger').text('An error occurred while saving follow-up.').show();
                $btn.prop('disabled', false).text('Save Follow-up');
            }
        });
    });
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
