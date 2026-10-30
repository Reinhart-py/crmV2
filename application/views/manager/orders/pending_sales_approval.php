<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<div class="container">
    <div class="content-wrapper">
        <section class="content-header">
            <h1> <?php echo $info['page_heading']; ?> </h1>
        </section>

        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box p-4" style="padding:15px; margin-bottom:15px; background:#ffffff; border:1px solid #e2e8f0; border-radius:4px;">
                        <form class="form-inline head_filter" action="<?php echo base_url(); ?>manager/orders/pending_sales_approval" method="get">
                            <div class="form-group" style="margin-right:10px;">
                                <label style="font-size:12px; font-weight:600; margin-right:4px;">Search:</label>
                                <input type="text" name="search" class="form-control" placeholder="Order #, Company, POC..." value="<?php echo htmlspecialchars($this->input->get('search') ?? ''); ?>" style="border:1px solid #cbd5e1; border-radius:4px; height:34px;">
                            </div>
                            <div class="form-group" style="margin-right:10px;">
                                <label style="font-size:12px; font-weight:600; margin-right:4px;">From:</label>
                                <input type="text" name="d1" class="form-control datepicker" placeholder="DD-MM-YYYY" value="<?php echo htmlspecialchars($this->input->get('d1') ?? ''); ?>" style="border:1px solid #cbd5e1; border-radius:4px; height:34px; width:120px;">
                            </div>
                            <div class="form-group" style="margin-right:10px;">
                                <label style="font-size:12px; font-weight:600; margin-right:4px;">To:</label>
                                <input type="text" name="d2" class="form-control datepicker" placeholder="DD-MM-YYYY" value="<?php echo htmlspecialchars($this->input->get('d2') ?? ''); ?>" style="border:1px solid #cbd5e1; border-radius:4px; height:34px; width:120px;">
                            </div>
                            <button type="submit" class="btn btn-success" style="font-weight:600; padding:6px 16px; border-radius:4px;">Search</button>
                            <a href="<?php echo base_url(); ?>manager/orders/pending_sales_approval" class="btn btn-danger" style="font-weight:600; padding:6px 16px; border-radius:4px; margin-left:4px;">Reset</a>
                        </form>
                    </div>

                    <div class="box" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:4px;">
                        <div class="box-body">
                            <?php if (!empty($orders)) { ?>
                            <div class="table-responsive">
                                <table id="example_orders_pending_sales" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Order #</th>
                                            <th>Company / Client</th>
                                            <th>POC &amp; Contact</th>
                                            <th>Service Scope</th>
                                            <th>Amount (AED)</th>
                                            <th>Submitted By</th>
                                            <th>Assigned Staff</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $ord) { ?>
                                        <tr>
                                            <td>
                                                <strong style="color:#0072f0;"><?php echo htmlspecialchars($ord['order_no']); ?></strong>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars(!empty($ord['company_name']) ? $ord['company_name'] : $ord['client_name']); ?></strong>
                                            </td>
                                            <td>
                                                <div><?php echo htmlspecialchars($ord['poc'] ?? '-'); ?></div>
                                                <small style="color:#64748b;"><?php echo htmlspecialchars($ord['contact1'] ?? '-'); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($ord['service_title'] ?: 'General'); ?></td>
                                            <td>
                                                <strong style="color:#16a34a;">AED <?php echo number_format((float)($ord['pricing_amount'] ?: $ord['mrr']), 2); ?></strong>
                                            </td>
                                            <td><?php echo htmlspecialchars($ord['created_by_name'] ?: 'Staff'); ?></td>
                                            <td><?php echo htmlspecialchars($ord['assigned_to_name'] ?: 'Unassigned'); ?></td>
                                            <td><?php echo date('d-m-Y', strtotime($ord['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-warning">Action</button>
                                                    <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                        <span class="caret"></span>
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                                        <li>
                                                            <a href="<?php echo base_url(); ?>manager/orders/view/<?php echo (int)$ord['id']; ?>"><i class="fa fa-eye"></i> View Order</a>
                                                        </li>
                                                        <li>
                                                            <a href="<?php echo base_url(); ?>manager/orders/approve_sales_order/<?php echo (int)$ord['id']; ?>"><i class="fa fa-check text-success"></i> Approve &amp; Onboard</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php } else { ?>
                            <div class="alert alert-info" style="margin:10px 0;"> No Data Found </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
