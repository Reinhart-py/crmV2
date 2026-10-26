<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
    <section class="content-header">
        <h1> <?php echo $info['page_heading']; ?> </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box p-4">
                    <div class="x_row">
                        <div class="x_col">
                            <form class="form-inline head_filter form-inline-mob" method="get">
                                <div class="form-group">
                                    <input type="text" class="input-md form-control" name="client" placeholder="Search Client / Order No." value="<?php echo htmlspecialchars($this->input->get('client') ?? ''); ?>" autocomplete="off" />
                                </div>
                                <div class="form-group">
                                    <select class="form-control" name="vertical">
                                        <option value="">All Verticals</option>
                                        <option value="accounting" <?php echo ($this->input->get('vertical') === 'accounting') ? 'selected' : ''; ?>>Accounting / VAT</option>
                                        <option value="telecom" <?php echo ($this->input->get('vertical') === 'telecom') ? 'selected' : ''; ?>>Telecom (Du/Etisalat)</option>
                                        <option value="it" <?php echo ($this->input->get('vertical') === 'it') ? 'selected' : ''; ?>>IT Services</option>
                                        <option value="corporate" <?php echo ($this->input->get('vertical') === 'corporate') ? 'selected' : ''; ?>>Corporate Services</option>
                                    </select>
                                </div>
                                <?php if (!isset($status_filter) || ($status_filter !== 'sales' && $status_filter !== 'Sales')) { ?>
                                <div class="form-group">
                                    <select class="form-control" name="status">
                                        <option value="">All Statuses</option>
                                        <option value="ongoing" <?php echo ($this->input->get('status') === 'ongoing') ? 'selected' : ''; ?>>Ongoing Orders</option>
                                        <option value="completed" <?php echo ($this->input->get('status') === 'completed') ? 'selected' : ''; ?>>Completed Orders</option>
                                        <option value="closed" <?php echo ($this->input->get('status') === 'closed') ? 'selected' : ''; ?>>Closed Orders</option>
                                        <option value="rejected_lost" <?php echo ($this->input->get('status') === 'rejected_lost') ? 'selected' : ''; ?>>Rejected / Lost Orders</option>
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
                                        <input type="text" class="input-md form-control datepicker" name="d1" placeholder="Create From" value="<?php echo htmlspecialchars($this->input->get('d1') ?? ''); ?>" autocomplete="off" />
                                        <span class="input-group-addon">To</span>
                                        <input type="text" class="input-md form-control datepicker" name="d2" placeholder="Create To" value="<?php echo htmlspecialchars($this->input->get('d2') ?? ''); ?>" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Search</button>
                                    <a href="<?php echo htmlspecialchars($reset_url ?? base_url('manager/orders')); ?>" class="btn btn-danger"> Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="box-body">
                        <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
                        <?php echo get_message($this->session->flashdata('message'), 'message'); ?>
                        <?php if(isset($orders_list) && !empty($orders_list)) { ?>
                        <div>
                            <table id="example_orders" class="table table-hover table-bordered table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Order No.</th>
                                        <th>Customer</th>
                                        <th>Service Type</th>
                                        <th>MRR</th>
                                        <th>Assigned Staff</th>
                                        <th>Created</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($orders_list as $ord){ ?>
                                    <tr>
                                        <td><a href="<?php echo base_url(); ?>manager/orders/view/<?php echo $ord['id']; ?>"><strong><?php echo !empty($ord['order_no']) ? htmlspecialchars($ord['order_no']) : ('O' . str_pad($ord['id'], 10, '0', STR_PAD_LEFT)); ?></strong></a></td>
                                        <td>
                                            <a href="<?php echo base_url(); ?>manager/orders/view/<?php echo $ord['id']; ?>"><?php echo htmlspecialchars($ord['client_name'] ?: ($ord['lead_customer'] ?: 'Client #'.$ord['client_id'])); ?></a>
                                            <?php if(!empty($ord['lead_code'])){ ?>
                                                <br><small class="text-muted">Lead No: <?php echo htmlspecialchars($ord['lead_code']); ?></small>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($ord['service_title'] ?: ($ord['vertical_code'] ?: '-')); ?></td>
                                        <td>AED <?php echo number_format((float)($ord['mrr'] ?: $ord['pricing_amount']), 2); ?></td>
                                        <td><?php echo htmlspecialchars($ord['staff_name'] ?: ($ord['assigned_dept'] ?: '-')); ?></td>
                                        <td><span class="no_show"><?php echo !empty($ord['created_at']) ? date('Ymd', strtotime($ord['created_at'])) : ''; ?></span><?php echo !empty($ord['created_at']) ? date('d-m-Y', strtotime($ord['created_at'])) : '-'; ?></td>
                                        <td>
                                            <?php 
                                                $st = $ord['status'];
                                                $badge = 'label-default';
                                                if ($st === 'Submitted' || $st === 'Pending') $badge = 'label-warning';
                                                elseif ($st === 'Approved' || $st === 'In Progress') $badge = 'label-info';
                                                elseif ($st === 'Fulfilled' || $st === 'Completed' || $st === 'Sales') $badge = 'label-success';
                                                elseif ($st === 'Cancelled') $badge = 'label-danger';
                                            ?>
                                            <span class="label <?php echo $badge; ?>" style="font-size:11px; padding:3px 8px;"><?php echo htmlspecialchars($st); ?></span>
                                        </td>
                                        <td>
                                            <?php if (!empty($can_edit_orders)): ?>
                                                <div class="btn-group" style="min-width: 85px;">
                                                    <button type="button" class="btn btn-sm btn-warning">Action</button>
                                                    <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                        <span class="caret"></span>
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        <li><a href="<?php echo base_url(); ?>manager/orders/view/<?php echo $ord['id']; ?>" data-toggle="tooltip" title="View">View</a></li>
                                                        <li><a href="<?php echo base_url(); ?>manager/orders/edit_pending/<?php echo $ord['id']; ?>" data-toggle="tooltip" title="Make Changes">Make Changes</a></li>
                                                        <?php if ($ord['approval_status'] === 'pending' || $ord['status'] === 'Submitted'): ?>
                                                            <li><a href="<?php echo base_url(); ?>manager/orders/quick_accept/<?php echo $ord['id']; ?>" onclick="return confirm('Accept and approve this order?');" data-toggle="tooltip" title="Accept">Accept</a></li>
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
                                                    <ul class="dropdown-menu" role="menu">
                                                        <li><a href="<?php echo base_url(); ?>manager/orders/view/<?php echo $ord['id']; ?>" data-toggle="tooltip" title="Preview">Preview</a></li>
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
                            <div class="alert alert-info"> No Data Found </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
