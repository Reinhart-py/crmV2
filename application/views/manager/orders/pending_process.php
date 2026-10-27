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
                                    <input type="text" class="input-md form-control" name="client" placeholder="Search Client / Contact / Ref No." value="<?php echo htmlspecialchars($this->input->get('client') ?? ''); ?>" autocomplete="off" />
                                </div>
                                <div class="form-group">
                                    <div class="input-daterange input-group" id="datepicker" style="width: 100%;">
                                        <input type="text" class="input-md form-control datepicker" name="d1" placeholder="Create From" value="<?php echo htmlspecialchars($this->input->get('d1') ?? ''); ?>" autocomplete="off" />
                                        <span class="input-group-addon">To</span>
                                        <input type="text" class="input-md form-control datepicker" name="d2" placeholder="Create To" value="<?php echo htmlspecialchars($this->input->get('d2') ?? ''); ?>" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Search</button>
                                    <a href="<?php echo htmlspecialchars($reset_url ?? base_url('manager/orders/pending_process')); ?>" class="btn btn-danger"> Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="box-body">
                        <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
                        <?php echo get_message($this->session->flashdata('message'), 'message'); ?>
                        <?php if(isset($pending_leads) && !empty($pending_leads)) { ?>
                        <div>
                            <table id="example_pending_process" class="table table-hover table-bordered table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Database No.</th>
                                        <th>Lead No.</th>
                                        <th>Customer</th>
                                        <th>Phone / Contact</th>
                                        <th>Requirements &amp; Remarks</th>
                                        <th>Requested By</th>
                                        <th>Request Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($pending_leads as $p){ ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($p['db_ref_no'] ?: '-'); ?></strong></td>
                                        <td><strong><?php echo htmlspecialchars($p['lead_ref_no'] ?: '-'); ?></strong></td>
                                        <td>
                                            <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($p['id']); ?>"><strong><?php echo htmlspecialchars($p['customer'] ?: 'Client'); ?></strong></a>
                                            <?php if(!empty($p['company_name'])){ ?>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($p['company_name']); ?></small>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($p['contact1'] ?: '-'); ?></td>
                                        <td><?php echo htmlspecialchars($p['order_request_remarks'] ?: '-'); ?></td>
                                        <td><?php echo htmlspecialchars($p['requested_by_name'] ?: 'Staff'); ?></td>
                                        <td><span class="no_show"><?php echo !empty($p['order_requested_at']) ? date('Ymd', strtotime($p['order_requested_at'])) : ''; ?></span><?php echo !empty($p['order_requested_at']) ? date('d-m-Y', strtotime($p['order_requested_at'])) : '-'; ?></td>
                                        <td>
                                            <div class="btn-group" style="min-width: 85px;">
                                                <button type="button" class="btn btn-sm btn-warning">Action</button>
                                                <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($p['id']); ?>" data-toggle="tooltip" title="View Lead">View Lead</a></li>
                                                    <li><a href="<?php echo base_url(); ?>manager/orders/edit_pending/<?php echo $p['id']; ?>" data-toggle="tooltip" title="Process Order">Process Order</a></li>
                                                </ul>
                                            </div>
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
    if ($.fn.DataTable.isDataTable('#example_pending_process')) {
        $('#example_pending_process').DataTable().destroy();
    }
    $('#example_pending_process').DataTable({
        "pageLength": 50,
        responsive: true
    });
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
