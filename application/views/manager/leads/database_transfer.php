<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
    <section class="content">
        <div class="row">
            <div class="col-xs-12">

                <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
                <?php echo get_message($this->session->flashdata('success_message'),'message'); ?>
                <?php echo get_message($this->session->flashdata('message'),'message'); ?>

                <div class="box p-4">
                    <div class="x_row">
                        <div class="x_col">
                            <form class="form-inline head_filter form-inline-mob" method="get" action="<?php echo base_url(); ?>manager/database-transfer">

                                <div class="form-group">
                                    <div class="input-daterange input-group" id="datepicker" style="width: 100%;">
                                        <input type="text" class="input-md form-control datepicker" name="d1"
                                            placeholder="From Date"
                                            value="<?php if(isset($_GET['d1'])&&(!empty($_GET['d1']))){ echo htmlspecialchars($_GET['d1']);}?>"
                                            autocomplete="off" />
                                        <span class="input-group-addon">To</span>
                                        <input type="text" class="input-md form-control datepicker" name="d2"
                                            placeholder="To Date"
                                            value="<?php if(isset($_GET['d2'])&&(!empty($_GET['d2']))){ echo htmlspecialchars($_GET['d2']);}?>"
                                            autocomplete="off" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <select id="dates-field3" class="multiselect-ui form-control" multiple="multiple" name="status[]" data-placeholder="Status">
                                        <?php if(isset($st_list) && !empty($st_list)){
                                            foreach($st_list as $cli){ ?>
                                                <option <?php if(isset($_GET['status']) && !empty($_GET['status']) && in_array($cli['id'], (array)$_GET['status'])){ echo "selected"; } ?> value="<?php echo $cli['id']; ?>"><?php echo htmlspecialchars($cli['status']); ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <input type="text" class="input-md form-control" name="search" placeholder="Search customer, phone, email..." value="<?php if(isset($_GET['search'])&&(!empty($_GET['search']))){ echo htmlspecialchars($_GET['search']);}?>" autocomplete="off" style="min-width: 200px;" />
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Search</button>
                                    <a href="<?php echo base_url(); ?>manager/database-transfer" class="btn btn-danger">Reset</a>
                                </div>

                            </form>
                        </div>
                        <div class="x_col-sm-auto text-center pt-4 pt-md-0">
                            <div>
                                <button type="button" class="btn btn-primary" id="btnTransferLeads" disabled data-toggle="modal" data-target="#modalTransferLeads" style="font-weight:600; background:#2563eb; border-color:#2563eb;">
                                    <i class="fa fa-exchange"></i> Transfer Selected (<span id="selectedCount">0</span>)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="box-body">
                        <?php if(isset($product_list) && !empty($product_list)) { ?>
                        <div>
                            <table id="example_lead" class="table table-hover table-bordered table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width: 35px; text-align: center;">
                                            <input type="checkbox" id="checkAllLeads" title="Select All">
                                        </th>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Current Assigned</th>
                                        <th>Service Type</th>
                                        <th>MRR</th>
                                        <th>POC Name</th>
                                        <th>Contact Number</th>
                                        <th>Created</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($product_list as $k => $li) { ?>
                                    <tr>
                                        <td style="text-align: center;">
                                            <input type="checkbox" name="lead_ids[]" value="<?php echo $li['id']; ?>" class="lead-check">
                                        </td>
                                        <td><a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($li['id']); ?>"><strong><?php echo $li['lead_id']; ?></strong></a></td>
                                        <td>
                                            <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($li['id']); ?>"><?php echo $li['customer']; ?></a>
                                            <?php if(!empty($li['dnd_status']) && strtolower($li['dnd_status']) == 'yes'){ ?>
                                                <span class="label label-danger" style="background:#dc2626; color:#ffffff; font-weight:700; padding:1px 5px; border-radius:3px; font-size:10px; margin-left:4px; letter-spacing:0.5px;">DNCR</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if(!empty($li['assigned_to_name'])){ ?>
                                                <span class="label label-success" style="font-size:11px;"><?php echo htmlspecialchars($li['assigned_to_name']); ?></span>
                                            <?php } else { ?>
                                                <span class="label label-warning" style="font-size:11px; background:#f59e0b; color:#fff;">Unassigned</span>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo isset($li['servicecat']) ? $li['servicecat'] : (isset($li['p_cat']) ? $li['p_cat'] : '-'); ?></td>
                                        <td><?php echo isset($li['mrr']) ? $li['mrr'] : '0'; ?></td>
                                        <td><?php echo $li['poc']; ?></td>
                                        <td><?php echo $li['contact1']; ?></td>
                                        <td><span class="no_show"><?php echo date('Ymd',strtotime($li['created'])); ?></span><?php echo simple_date($li['created']); ?></td>
                                        <td>
                                            <?php echo isset($li['lead_status']) ? $li['lead_status'] : (isset($li['status']) ? $li['status'] : ''); ?>
                                            <?php 
                                             $cat_label = !empty($li['status_category']) ? $li['status_category'] : (!empty($li['lost_reason']) ? $li['lost_reason'] : '');
                                            if (!empty($cat_label)): ?>
                                                <br><small class="text-muted" style="font-weight: 500;">Category: <?php echo htmlspecialchars($cat_label); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" style="min-width: 85px;">
                                                <button type="button" class="btn btn-sm btn-warning">Action</button>
                                                <button type="button" class="btn btn-sm btn-warning dropdown-toggle"
                                                    data-toggle="dropdown" aria-expanded="false">
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li> <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($li['id']); ?>"
                                                            data-toggle="tooltip" title="View">Preview</a> </li>
                                                    <li> <a href="<?php echo base_url(); ?>manager/leads/edit/<?php echo base64_encode($li['id']); ?>"
                                                            data-toggle="tooltip" title="Edit">Edit</a> </li>
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

<div class="modal fade" id="modalTransferLeads" tabindex="-1" role="dialog" aria-labelledby="modalTransferLabel">
    <div class="modal-dialog" role="document">
        <form method="post" action="<?php echo base_url(); ?>manager/leads/bulk-transfer" id="formBulkTransfer">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modalTransferLabel"><i class="fa fa-exchange" style="color:#2563eb;"></i> Transfer Selected Leads</h4>
                </div>
                <div class="modal-body">
                    <p style="font-size:14px; margin-bottom:15px;">
                        You have selected <strong id="modalSelectedLeadCount" style="color:#2563eb;">0</strong> lead record(s) to transfer.
                    </p>
                    <div class="form-group">
                        <label style="font-weight:600;">Transfer To Team Member:</label>
                        <select name="target_user" class="form-control" required style="width:100%;">
                            <option value="">-- Select Team Member --</option>
                            <?php if (!empty($users_list)) {
                                foreach ($users_list as $u) {
                                    echo '<option value="'.$u['id'].'">'.htmlspecialchars($u['name']).' ('.htmlspecialchars($u['email']).')</option>';
                                }
                            } ?>
                        </select>
                    </div>
                    <div id="selectedLeadInputsContainer"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background:#2563eb; border-color:#2563eb;">Confirm Transfer</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(function() {
    function updateTransferButtonState() {
        var checked = $('.lead-check:checked');
        var count = checked.length;
        $('#selectedCount').text(count);
        $('#modalSelectedLeadCount').text(count);

        if (count > 0) {
            $('#btnTransferLeads').prop('disabled', false);
        } else {
            $('#btnTransferLeads').prop('disabled', true);
        }

        var total = $('.lead-check').length;
        if (total > 0 && count === total) {
            $('#checkAllLeads').prop('checked', true);
        } else {
            $('#checkAllLeads').prop('checked', false);
        }
    }

    $('#checkAllLeads').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('.lead-check').prop('checked', isChecked);
        updateTransferButtonState();
    });

    $(document).on('change', '.lead-check', function() {
        updateTransferButtonState();
    });

    $('#modalTransferLeads').on('show.bs.modal', function() {
        var container = $('#selectedLeadInputsContainer');
        container.empty();
        $('.lead-check:checked').each(function() {
            var val = $(this).val();
            container.append('<input type="hidden" name="lead_ids[]" value="' + val + '">');
        });
    });

    updateTransferButtonState();

    if (!$.fn.DataTable.isDataTable('#example_lead')) {
        $('#example_lead').DataTable({
            "pageLength": 50,
            responsive: true,
            order: [],
            columnDefs: [
                { targets: 0, orderable: false }
            ]
        });
    }
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
