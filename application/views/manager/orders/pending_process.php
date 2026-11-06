<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php $n = isset($pending_leads) ? count($pending_leads) : 0; ?>
<div class="content-wrapper px-4 tlv">
<?php echo crm_mz_tabbar(); ?>
    <section class="content-header">
        <h1><?php echo htmlspecialchars($info['page_heading'] ?? 'Pending Order Process'); ?><small><?php echo $n; ?> waiting</small></h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box p-4">
                    <div class="x_row">
                        <div class="x_col">
                            <form class="form-inline head_filter form-inline-mob" method="get" action="<?php echo base_url('manager/orders/pending_process'); ?>">
                                <div class="form-group">
                                    <input type="text" class="input-md form-control" name="client" placeholder="Client, contact or ref no." aria-label="Search" value="<?php echo htmlspecialchars($this->input->get('client') ?? ''); ?>" autocomplete="off" />
                                </div>
                                <div class="form-group">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="input-md form-control datepicker" name="d1" placeholder="Requested from" aria-label="Requested from" value="<?php echo htmlspecialchars($this->input->get('d1') ?? ''); ?>" autocomplete="off" />
                                        <span class="input-group-addon">to</span>
                                        <input type="text" class="input-md form-control datepicker" name="d2" placeholder="Requested to" aria-label="Requested to" value="<?php echo htmlspecialchars($this->input->get('d2') ?? ''); ?>" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                    <a href="<?php echo htmlspecialchars($reset_url ?? base_url('manager/orders/pending_process')); ?>" class="btn btn-default">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="box-body">
                        <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
                        <?php echo get_message($this->session->flashdata('message'), 'message'); ?>
                        <?php if ($n) { ?>
                            <table id="example_pending_process" class="table table-hover table-bordered tl-t" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>Lead No.</th>
                                        <th>Customer</th>
                                        <th>Database No.</th>
                                        <th>Phone</th>
                                        <th>MRR</th>
                                        <th>Requirements</th>
                                        <th>Requested by</th>
                                        <th>Requested</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pending_leads as $p) {
                                        $lead_id = (int)$p['id'];
                                        $encoded_id = base64_encode($lead_id);
                                        $rem = (string)($p['order_request_remarks'] ?? '');
                                    ?>
                                    <tr>
                                        <td><a href="<?php echo base_url('manager/leads/view/' . $encoded_id); ?>"><?php echo htmlspecialchars(!empty($p['lead_ref_no']) ? $p['lead_ref_no'] : ('L' . str_pad($lead_id, 10, '0', STR_PAD_LEFT))); ?></a></td>
                                        <td>
                                            <a href="<?php echo base_url('manager/leads/view/' . $encoded_id); ?>"><?php echo htmlspecialchars($p['customer'] ?: 'Client'); ?></a>
                                            <?php if (!empty($p['poc'])) { ?><br><small class="text-muted"><?php echo htmlspecialchars($p['poc']); ?></small><?php } ?>
                                        </td>
                                        <td><?php echo htmlspecialchars(!empty($p['db_ref_no']) ? $p['db_ref_no'] : ('D' . str_pad($lead_id, 10, '0', STR_PAD_LEFT))); ?></td>
                                        <td><?php echo htmlspecialchars($p['contact1'] ?: '-'); ?></td>
                                        <td data-order="<?php echo (float)($p['mrr'] ?? 0); ?>">AED <?php echo number_format((float)($p['mrr'] ?? 0), 2); ?></td>
                                        <td><span title="<?php echo htmlspecialchars($rem); ?>"><?php echo htmlspecialchars(mb_strimwidth($rem !== '' ? $rem : '-', 0, 40, '…')); ?></span></td>
                                        <td><?php echo htmlspecialchars($p['requested_by_name'] ?: 'Staff'); ?></td>
                                        <td data-order="<?php echo htmlspecialchars((string)($p['order_requested_at'] ?? '')); ?>"><?php echo !empty($p['order_requested_at']) ? date('d-m-Y', strtotime($p['order_requested_at'])) : '-'; ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button>
                                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                                    <li><a href="<?php echo base_url('manager/orders/edit_pending/' . $lead_id); ?>"><i class="fa fa-cogs"></i> Process Order</a></li>
                                                    <li><a href="<?php echo base_url('manager/leads/view/' . $encoded_id); ?>"><i class="fa fa-eye"></i> View Lead</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <div class="alert alert-info" style="margin-bottom:0;">No orders waiting to be processed.</div>
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
        "pageLength": 25,
        "responsive": window.innerWidth >= 768, // phones: each request is a card (crm-tlist.css)
        "order": [[7, "desc"]],
        "columnDefs": [{ "orderable": false, "targets": [8] }]
    });
});
</script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-tlist.css?v=20261106_4">
<script src="<?php echo base_url(); ?>assets/dist/js/crm-tlist.js?v=20261106_4"></script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
