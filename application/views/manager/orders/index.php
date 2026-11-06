<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4 tlv">
<?php echo crm_mz_tabbar(); ?>
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
                                <?php
                                // Status choice only on All Orders: the Open, Follow-up, Completed, Closed and Rejected / Lost tabs are fixed lists
                                if (empty($scope_filter) && (empty($status_filter) || !in_array($status_filter, array('sales', 'Sales', 'open', 'followups', 'completed', 'closed', 'rejected_lost', 'ongoing'), true))) { ?>
                                <div class="form-group">
                                    <select class="form-control" name="status" aria-label="Status">
                                        <option value="">All statuses</option>
                                        <?php foreach (array('Submitted', 'Approved', 'In Progress', 'Onboarding', 'Pending Sales Approval', 'Fulfilled', 'Cancelled') as $so) { ?>
                                        <option value="<?php echo $so; ?>"<?php echo $this->input->get('status') === $so ? ' selected' : ''; ?>><?php echo $so; ?></option>
                                        <?php } ?>
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
                            <table id="example_orders" class="table table-hover table-bordered crm-table tl-t" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>Order No.</th>
                                        <th>Customer</th>
                                        <th>Service Type</th>
                                        <th>MRR</th>
                                        <th>Assigned Staff</th>
                                        <th>Created</th>
                                        <th>Next Follow-Up</th>
                                        <th>Status</th>
                                        <th style="text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Back Office asks KAM to approve an order as a sale (only KAM approves)
                                    $req_ret = (isset($status_filter) && $status_filter === 'completed') ? 'manager/orders?status=completed' : ((isset($status_filter) && in_array($status_filter, array('open', 'followups'), true)) ? 'manager/orders/' . $status_filter : 'manager/orders');
                                    $req_ok = !empty($can_edit_orders) || (isset($status_filter) && $status_filter === 'followups');
                                    $req_sales = function ($o) use ($req_ret, $req_ok) {
                                        if (!$req_ok) return '';
                                        if ($o['status'] === 'Sales' || !empty($o['sales_ref_no']) || $o['status'] === 'Pending Sales Approval' || $o['approval_status'] === 'pending_approval' || in_array($o['status'], array('Cancelled', 'Rejected', 'Lost', 'Closed'), true)) return '';
                                        $ci =& get_instance();
                                        return '<li><form method="post" action="' . base_url() . 'manager/orders/request_sales_approval/' . (int)$o['id'] . '" style="margin:0" onsubmit="return confirm(\'Send this order to KAM for sales approval?\');">'
                                            . '<input type="hidden" name="' . $ci->security->get_csrf_token_name() . '" value="' . $ci->security->get_csrf_hash() . '">'
                                            . '<input type="hidden" name="return_to" value="' . htmlspecialchars($req_ret) . '">'
                                            . '<button type="submit" class="btn btn-link" style="display:block;width:100%;text-align:left;padding:7px 10px;font-size:13px;font-weight:600;color:inherit;text-decoration:none;">Request Sales Approval</button></form></li>';
                                    };
                                    ?>
                                    <?php $fu_return =!empty($scope_filter) && $scope_filter === 'my' ? 'manager/orders/my' : ((isset($status_filter) && in_array($status_filter, array('open', 'followups'), true)) ? 'manager/orders/' . $status_filter : 'manager/orders'); ?>
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
                                        <td data-order="<?php echo (float)($ord['mrr'] ?: $ord['pricing_amount']); ?>">AED <?php echo number_format((float)($ord['mrr'] ?: $ord['pricing_amount']), 2); ?></td>
                                        <td style="color: #0f172a; font-weight: 600;"><?php echo htmlspecialchars($ord['staff_name'] ?: ($ord['assigned_dept'] ?: '-')); ?></td>
                                        <td data-order="<?php echo htmlspecialchars((string)($ord['created_at'] ?? '')); ?>"><span class="no_show"><?php echo !empty($ord['created_at']) ? date('Ymd', strtotime($ord['created_at'])) : ''; ?></span><?php echo !empty($ord['created_at']) ? date('d-m-Y', strtotime($ord['created_at'])) : '-'; ?></td>
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
                                                $badge_color = 'neutral';
                                                if(in_array($st, ['Open','Submitted','In Progress'])) $badge_color = 'ocean';
                                                elseif(in_array($st, ['Approved','Fulfilled','Completed'])) $badge_color = 'ink';
                                                elseif(in_array($st, ['Closed','Sales'])) $badge_color = 'ink';
                                                elseif(in_array($st, ['Cancelled','Rejected','Lost'])) $badge_color = 'danger';
                                                elseif($st === 'Pending Sales Approval') $badge_color = 'neutral';
                                            ?>
                                            <?php echo htmlspecialchars($st); ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <?php if (isset($status_filter) && $status_filter === 'followups'): ?>
                                                <div class="btn-group" style="min-width: 85px;">
                                                    <button type="button" class="btn btn-sm btn-warning">Action</button>
                                                    <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                        <span class="caret"></span>
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-right" role="menu" style="border: 1px solid #cbd5e1; border-radius: 4px; box-shadow: 0 4px 14px rgba(0,0,0,0.12);">
                                                        <li><a href="<?php echo base_url(); ?>manager/orders/view/<?php echo $ord['id']; ?>" style="font-weight: 600;">Preview</a></li>
                                                        <li><a href="<?php echo base_url(); ?>manager/orders/add_followup/<?php echo $ord['id']; ?>?return=<?php echo urlencode($fu_return); ?>">Add Follow-Up</a></li>
                                                        <?php echo $req_sales($ord); ?>
                                                    </ul>
                                                </div>
                                            <?php elseif (!empty($can_edit_orders)): ?>
                                                <div class="btn-group" style="min-width: 85px;">
                                                    <button type="button" class="btn btn-sm btn-warning">Action</button>
                                                    <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                        <span class="caret"></span>
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-right" role="menu" style="border: 1px solid #000000; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                                        <li><a href="<?php echo base_url(); ?>manager/orders/view/<?php echo $ord['id']; ?>" data-toggle="tooltip" title="View" style="font-weight: 600;">View</a></li>
                                                        <li><a href="<?php echo base_url(); ?>manager/orders/add_followup/<?php echo $ord['id']; ?>?return=<?php echo urlencode($fu_return); ?>">Add Follow-Up</a></li>
                                                        <?php echo $req_sales($ord); ?>
                                                        <?php if (($status_filter === 'closed' || $ord['status'] === 'Closed' || $ord['status'] === 'Sales') && ($this->session->userdata('manager_role') == 1 || $this->session->userdata('manager_id') == 1)): ?>
                                                            <li><a href="javascript:void(0);" class="btn-open-assign-order" data-order-id="<?php echo $ord['id']; ?>" data-order-no="<?php echo htmlspecialchars($ord['order_no']); ?>" data-assigned-to="<?php echo (int)($ord['assigned_to'] ?? 0); ?>" data-assigned-dept="<?php echo htmlspecialchars($ord['assigned_dept'] ?? 'Operations'); ?>" style="font-weight: 600;">Assign Order</a></li>
                                                        <?php endif; ?>
                                                        <?php if ($ord['approval_status'] === 'pending' || $ord['status'] === 'Submitted'): ?>
                                                            <li><a href="<?php echo base_url(); ?>manager/orders/quick_accept/<?php echo $ord['id']; ?>" onclick="return confirm('Accept and approve this order?');" data-toggle="tooltip" title="Accept" style="font-weight: 600;">Accept</a></li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </div>
                                            <?php else: ?>
                                                <div class="btn-group" style="min-width: 85px;">
                                                    <button type="button" class="btn btn-sm btn-warning">Action</button>
                                                    <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                        <span class="caret"></span>
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-right" role="menu" style="border: 1px solid #000000; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                                        <li><a href="<?php echo base_url(); ?>manager/orders/view/<?php echo $ord['id']; ?>" data-toggle="tooltip" title="Preview" style="font-weight: 600;">Preview</a></li>
                                                        <li><a href="<?php echo base_url(); ?>manager/orders/add_followup/<?php echo $ord['id']; ?>?return=<?php echo urlencode($fu_return); ?>">Add Follow-Up</a></li>
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

<div class="modal fade" id="assignClosedOrderModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 460px;">
        <div class="modal-content" style="border-radius: 6px; border: 1px solid #000000; overflow: hidden;">
            <form action="<?php echo base_url(); ?>manager/orders/assign_closed_order" method="post">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="order_id" id="assign_order_id" value="">
                <div class="modal-header" style="background: #000000; color: #ffffff; padding: 14px 18px; border-bottom: 1px solid #000000;">
                    <button type="button" class="close" data-dismiss="modal" style="color: #ffffff; opacity: 0.8;">&times;</button>
                    <h4 class="modal-title" style="font-size: 15px; font-weight: 700; color: #ffffff; margin: 0;">
                        Assign Closed Order (<span id="assign_order_no_display"></span>)
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
        "order": <?php echo (isset($status_filter) && $status_filter === 'followups') ? '[]' : '[[0, "desc"]]'; ?>,
        responsive: window.innerWidth >= 768 // phones: each order is a card (crm-tlist.css)
    });

    if ($.fn.datepicker) {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
    }

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

    });
</script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-tlist.css?v=20261106_4">
<script src="<?php echo base_url(); ?>assets/dist/js/crm-tlist.js?v=20261106_3"></script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
