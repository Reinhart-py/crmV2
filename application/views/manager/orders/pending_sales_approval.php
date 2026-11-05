<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php $psa_url = base_url() . 'manager/orders/' . $this->router->fetch_method(); ?>
<div class="content-wrapper">
    <section class="content-header crm-list-head">
        <h1><?php echo $info['page_heading']; ?></h1>
        <span class="crm-list-count"><?php echo count($orders); ?> <?php echo count($orders) === 1 ? 'order' : 'orders'; ?> waiting</span>
    </section>

    <section class="content">
        <div class="box crm-filter-box">
            <form class="crm-filter-bar" action="<?php echo $psa_url; ?>" method="get">
                <div class="crm-filter-field is-wide">
                    <label for="psa-search">Search</label>
                    <input type="text" id="psa-search" name="search" class="form-control" placeholder="Order #, company, client or service" value="<?php echo htmlspecialchars($this->input->get('search') ?? ''); ?>">
                </div>
                <div class="crm-filter-field">
                    <label for="psa-d1">From</label>
                    <input type="text" id="psa-d1" name="d1" class="form-control datepicker" placeholder="DD-MM-YYYY" autocomplete="off" value="<?php echo htmlspecialchars($this->input->get('d1') ?? ''); ?>">
                </div>
                <div class="crm-filter-field">
                    <label for="psa-d2">To</label>
                    <input type="text" id="psa-d2" name="d2" class="form-control datepicker" placeholder="DD-MM-YYYY" autocomplete="off" value="<?php echo htmlspecialchars($this->input->get('d2') ?? ''); ?>">
                </div>
                <div class="crm-filter-actions">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="<?php echo $psa_url; ?>" class="btn btn-default">Reset</a>
                </div>
            </form>
        </div>

        <div class="box crm-list-box">
            <div class="box-body">
                <?php if (!empty($orders)) { ?>
                <table id="example_orders_pending_sales" class="table table-bordered table-striped crm-list-table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Company / Client</th>
                            <th>POC &amp; Contact</th>
                            <th>Service Scope</th>
                            <th>Amount (AED)</th>
                            <th>Status</th>
                            <th>Submitted By</th>
                            <th>Assigned Staff</th>
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
                        ?>
                        <tr>
                            <td><strong class="crm-ref"><?php echo htmlspecialchars($ord['order_no']); ?></strong></td>
                            <td><strong><?php echo htmlspecialchars(!empty($ord['company_name']) ? $ord['company_name'] : $ord['client_name']); ?></strong></td>
                            <td>
                                <div><?php echo htmlspecialchars($ord['poc'] ?? '-'); ?></div>
                                <small class="crm-sub"><?php echo htmlspecialchars($ord['contact1'] ?? '-'); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($ord['service_title'] ?: 'General'); ?></td>
                            <td class="crm-amount" data-order="<?php echo (float)($ord['pricing_amount'] ?: $ord['mrr']); ?>">AED <?php echo number_format((float)($ord['pricing_amount'] ?: $ord['mrr']), 2); ?></td>
                            <td><span class="crm-status crm-status-ocean"><?php echo htmlspecialchars($ord_status); ?></span></td>
                            <td><?php echo htmlspecialchars($ord['created_by_name'] ?: 'Staff'); ?></td>
                            <td><?php echo htmlspecialchars($ord['assigned_to_name'] ?: 'Unassigned'); ?></td>
                            <td data-order="<?php echo htmlspecialchars((string)$ord['created_at']); ?>"><?php echo date('d-m-Y', strtotime($ord['created_at'])); ?></td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        Action <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        <li><a href="<?php echo base_url(); ?>manager/orders/view/<?php echo (int)$ord['id']; ?>"><i class="fa fa-eye"></i> View Order</a></li>
                                        <li><a href="<?php echo base_url(); ?>manager/orders/approve_sales_order/<?php echo (int)$ord['id']; ?>"><i class="fa fa-check text-success"></i> Approve &amp; Onboard</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php } else { ?>
                <div class="crm-empty-state">No orders are waiting for sales approval.</div>
                <?php } ?>
            </div>
        </div>
    </section>
</div>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
