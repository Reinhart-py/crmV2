<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
    <section class="content-header">
        <h1><?php echo htmlspecialchars($info['page_heading'] ?? 'Pending Order Process'); ?></h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box p-4">
                    <div class="x_row">
                        <div class="x_col">
                            <form class="form-inline head_filter form-inline-mob" method="get" action="<?php echo base_url('manager/orders/pending_process'); ?>">
                                <div class="form-group">
                                    <input type="text" class="input-md form-control" name="client" placeholder="Search Client / Contact / Ref No." value="<?php echo htmlspecialchars($this->input->get('client') ?? ''); ?>" autocomplete="off" style="min-width: 250px;" />
                                </div>
                                <div class="form-group">
                                    <div class="input-daterange input-group" id="datepicker" style="width: 100%;">
                                        <input type="text" class="input-md form-control datepicker" name="d1" placeholder="Create From" value="<?php echo htmlspecialchars($this->input->get('d1') ?? ''); ?>" autocomplete="off" />
                                        <span class="input-group-addon">To</span>
                                        <input type="text" class="input-md form-control datepicker" name="d2" placeholder="Create To" value="<?php echo htmlspecialchars($this->input->get('d2') ?? ''); ?>" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
                                    <a href="<?php echo htmlspecialchars($reset_url ?? base_url('manager/orders/pending_process')); ?>" class="btn btn-danger"><i class="fa fa-refresh"></i> Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="box-body table-responsive">
                        <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
                        <?php echo get_message($this->session->flashdata('message'), 'message'); ?>
                        <?php if (isset($pending_leads) && !empty($pending_leads)) { ?>
                            <table id="example_pending_process" class="table table-hover table-bordered table-striped" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>Database No.</th>
                                        <th>Lead No.</th>
                                        <th>Customer</th>
                                        <th>Phone / Contact</th>
                                        <th>MRR</th>
                                        <th>Requirements &amp; Remarks</th>
                                        <th>Requested By</th>
                                        <th>Request Date</th>
                                        <th style="width: 100px; text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pending_leads as $p) { 
                                        $lead_id = (int)$p['id'];
                                        $encoded_id = base64_encode($lead_id);
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo base_url('manager/leads/view/' . $encoded_id); ?>">
                                                <strong><?php echo htmlspecialchars(!empty($p['db_ref_no']) ? $p['db_ref_no'] : ('D' . str_pad($lead_id, 10, '0', STR_PAD_LEFT))); ?></strong>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="<?php echo base_url('manager/leads/view/' . $encoded_id); ?>">
                                                <strong style="color: #2563eb;"><?php echo htmlspecialchars(!empty($p['lead_ref_no']) ? $p['lead_ref_no'] : ('L' . str_pad($lead_id, 10, '0', STR_PAD_LEFT))); ?></strong>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="<?php echo base_url('manager/leads/view/' . $encoded_id); ?>">
                                                <?php echo htmlspecialchars($p['customer'] ?: 'Client'); ?>
                                            </a>
                                            <?php if (!empty($p['poc'])) { ?>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($p['poc']); ?></small>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($p['contact1'] ?: '-'); ?></td>
                                        <td>AED <?php echo number_format((float)($p['mrr'] ?? 0), 2); ?></td>
                                        <td><span title="<?php echo htmlspecialchars($p['order_request_remarks'] ?? ''); ?>"><?php echo htmlspecialchars(mb_strimwidth($p['order_request_remarks'] ?: '-', 0, 35, '...')); ?></span></td>
                                        <td><?php echo htmlspecialchars($p['requested_by_name'] ?: 'Staff'); ?></td>
                                        <td><span class="no_show"><?php echo !empty($p['order_requested_at']) ? date('Ymd', strtotime($p['order_requested_at'])) : ''; ?></span><?php echo !empty($p['order_requested_at']) ? date('d-m-Y', strtotime($p['order_requested_at'])) : '-'; ?></td>
                                        <td style="text-align: center;">
                                            <div class="btn-group" style="min-width: 85px;">
                                                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border: 1px solid #cbd5e1; padding: 4px 10px; border-radius: 4px; font-weight: 500;">
                                                    Action <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right" role="menu" style="border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: 1px solid #e2e8f0;">
                                                    <li>
                                                        <a href="<?php echo base_url('manager/leads/view/' . $encoded_id); ?>" title="View Lead">
                                                            <i class="fa fa-eye" style="color: #64748b; margin-right: 6px;"></i> View Lead
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="<?php echo base_url('manager/orders/edit_pending/' . $lead_id); ?>" title="Process Order">
                                                            <i class="fa fa-cogs" style="color: #0072f0; margin-right: 6px;"></i> Process Order
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
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
        "pageLength": 25,
        "responsive": true,
        "order": [[7, "desc"]],
        "columnDefs": [
            { "orderable": false, "targets": [8] }
        ]
    });
});
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
