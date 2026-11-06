<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php $psa_url = base_url() . 'manager/orders/' . $this->router->fetch_method(); $n = count($orders); ?>
<div class="content-wrapper px-4 tlv">
<?php echo crm_mz_tabbar(); ?>
    <section class="content-header">
        <h1><?php echo htmlspecialchars($info['page_heading']); ?><small><?php echo $n; ?> waiting</small></h1>
    </section>

    <section class="content">
        <div class="box p-4">
            <form class="form-inline head_filter form-inline-mob" action="<?php echo $psa_url; ?>" method="get">
                <div class="form-group">
                    <input type="text" name="search" class="form-control" placeholder="Order #, company or service" aria-label="Search" value="<?php echo htmlspecialchars($this->input->get('search') ?? ''); ?>">
                </div>
                <div class="form-group">
                    <div class="input-daterange input-group">
                        <input type="text" name="d1" class="form-control datepicker" placeholder="From" aria-label="From" autocomplete="off" value="<?php echo htmlspecialchars($this->input->get('d1') ?? ''); ?>">
                        <span class="input-group-addon">to</span>
                        <input type="text" name="d2" class="form-control datepicker" placeholder="To" aria-label="To" autocomplete="off" value="<?php echo htmlspecialchars($this->input->get('d2') ?? ''); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="<?php echo $psa_url; ?>" class="btn btn-default">Reset</a>
                </div>
            </form>
        </div>

        <div class="box">
            <div class="box-body">
                <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
                <?php echo get_message($this->session->flashdata('message'), 'message'); ?>
                <?php if ($n) { ?>
                <table id="example_orders_pending_sales" class="table table-bordered table-hover tl-t" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Company</th>
                            <th>Contact</th>
                            <th>Service</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Submitted by</th>
                            <th>Assigned to</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $ord) {
                            $ord_status = trim((string)($ord['status'] ?? ''));
                            if ($ord_status === '' || strtolower($ord_status) === 'pending_sales_approval') {
                                $ord_status = 'Pending Sales Approval';
                            }
                            $amt = (float)($ord['pricing_amount'] ?: $ord['mrr']);
                        ?>
                        <tr>
                            <td><a href="<?php echo base_url(); ?>manager/orders/view/<?php echo (int)$ord['id']; ?>"><?php echo htmlspecialchars($ord['order_no']); ?></a></td>
                            <td><?php echo htmlspecialchars(!empty($ord['company_name']) ? $ord['company_name'] : $ord['client_name']); ?></td>
                            <td><?php echo htmlspecialchars($ord['poc'] ?? '-'); ?><?php if (!empty($ord['contact1'])) { ?><br><small class="text-muted"><?php echo htmlspecialchars($ord['contact1']); ?></small><?php } ?></td>
                            <td><?php echo htmlspecialchars($ord['service_title'] ?: 'General'); ?></td>
                            <td data-order="<?php echo $amt; ?>">AED <?php echo number_format($amt, 2); ?></td>
                            <td><?php echo htmlspecialchars($ord_status); ?></td>
                            <td><?php echo htmlspecialchars($ord['created_by_name'] ?: 'Staff'); ?></td>
                            <td><?php echo htmlspecialchars($ord['assigned_to_name'] ?: 'Unassigned'); ?></td>
                            <td data-order="<?php echo htmlspecialchars((string)$ord['created_at']); ?>"><?php echo date('d-m-Y', strtotime($ord['created_at'])); ?></td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Action <span class="caret"></span></button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        <?php if (!empty($can_approve_sales)) { ?><li><a href="<?php echo base_url(); ?>manager/orders/approve_sales_order/<?php echo (int)$ord['id']; ?>"><i class="fa fa-check text-success"></i> Approve &amp; Onboard</a></li><?php } ?>
                                        <li><a href="<?php echo base_url(); ?>manager/orders/view/<?php echo (int)$ord['id']; ?>"><i class="fa fa-eye"></i> View Order</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php } else { ?>
                <div class="alert alert-info" style="margin-bottom:0;">No orders waiting for sales approval.</div>
                <?php } ?>
            </div>
        </div>
    </section>
</div>
<script type="text/javascript">
$(document).ready(function () {
    if ($('#example_orders_pending_sales').length && !$.fn.DataTable.isDataTable('#example_orders_pending_sales')) {
        $('#example_orders_pending_sales').DataTable({ pageLength: 50, order: [[8, 'desc']], columnDefs: [{ orderable: false, targets: [9] }], responsive: window.innerWidth >= 768 });
    }
});
</script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-tlist.css?v=20261106_4">
<script src="<?php echo base_url(); ?>assets/dist/js/crm-tlist.js?v=20261106_3"></script>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
