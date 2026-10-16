<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper px-4">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">

                <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
                <?php echo get_message($this->session->flashdata('message'),'message'); ?>

                <div class="box p-4">
                    <div class="x_row">
                        <div class="x_col">
                            <form class="form-inline head_filter form-inline-mob" method="get" action="<?php echo base_url(); ?>manager/assigned-leads">

                                <div class="form-group">
                                    <div class="input-daterange input-group" id="datepicker" style="width: 100%;">
                                        <input type="text" class="input-md form-control datepicker" name="d1"
                                            placeholder="Create From"
                                            value="<?php if(isset($_GET['d1'])&&(!empty($_GET['d1']))){ echo htmlspecialchars($_GET['d1']);}?>"
                                            autocomplete="off" />
                                        <span class="input-group-addon">To</span>
                                        <input type="text" class="input-md form-control datepicker" name="d2"
                                            placeholder="Create To"
                                            value="<?php if(isset($_GET['d2'])&&(!empty($_GET['d2']))){ echo htmlspecialchars($_GET['d2']);}?>"
                                            autocomplete="off" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-daterange input-group" style="width: 100%;">
                                        <input type="text" class="input-md form-control only_number" name="mrr1" placeholder="MRR Min" value="<?php if(isset($_GET['mrr1'])&&(!empty($_GET['mrr1']) OR $_GET['mrr1']=='0')){ echo htmlspecialchars($_GET['mrr1']);}?>" autocomplete="off" />
                                        <span class="input-group-addon">To</span>
                                        <input type="text" class="input-md form-control only_number" name="mrr2" placeholder="MRR Max" value="<?php if(isset($_GET['mrr2'])&&(!empty($_GET['mrr2']))){ echo htmlspecialchars($_GET['mrr2']);}?>" autocomplete="off" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <select class="multiselect-ui form-control" multiple="multiple" name="category[]" data-placeholder="Service Type">
                                        <?php if(isset($pc_list) && !empty($pc_list)){
                                            foreach($pc_list as $pli){ ?>
                                                <option <?php if(isset($_GET['category']) && !empty($_GET['category']) && in_array($pli['id'], $_GET['category'])){ echo "selected"; } ?> value="<?php echo $pli['id']; ?>"><?php echo htmlspecialchars($pli['title']); ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <select id="dates-field2" class="multiselect-ui form-control" multiple="multiple" name="lCat[]" data-placeholder="Lead Category">
                                        <?php if(isset($lc_list) && !empty($lc_list)){
                                            foreach($lc_list as $cli){ ?>
                                                <option <?php if(isset($_GET['lCat']) && !empty($_GET['lCat']) && in_array($cli['id'], $_GET['lCat'])){ echo "selected"; } ?> value="<?php echo $cli['id']; ?>"><?php echo htmlspecialchars($cli['title']); ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <select id="dates-field3" class="multiselect-ui form-control" multiple="multiple" name="status[]" data-placeholder="Status">
                                        <?php if(isset($st_list) && !empty($st_list)){
                                            foreach($st_list as $cli){ ?>
                                                <option <?php if(isset($_GET['status']) && !empty($_GET['status']) && in_array($cli['id'], $_GET['status'])){ echo "selected"; } ?> value="<?php echo $cli['id']; ?>"><?php echo htmlspecialchars($cli['status']); ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <input type="text" class="input-md form-control" name="user_name" placeholder="User Name" value="<?php if(isset($_GET['user_name'])&&(!empty($_GET['user_name']))){ echo htmlspecialchars($_GET['user_name']);}?>" autocomplete="off" />
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Search</button>
                                    <a href="<?php echo base_url(); ?>manager/assigned-leads" class="btn btn-danger">Reset</a>
                                </div>

                            </form>
                        </div>
                        <div class="x_col-sm-auto text-center pt-4 pt-md-0">
                            <div>
                                <button type="button" class="btn btn-primary" id="btnMoveLeads" disabled data-toggle="modal" data-target="#modalMoveLeads" style="font-weight:600;">
                                    <i class="fa fa-arrows"></i> Move (<span id="selectedCount">0</span>)
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
                                        <th>Service Type</th>
                                        <th>MRR</th>
                                        <th>POC Name</th>
                                        <th>Contact Number</th>
                                        <th>Next FU date</th>
                                        <th>Created</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php foreach($product_list as $k => $li) { ?>
                                    <tr>
                                        <td style="text-align: center;">
                                            <input type="checkbox" name="lead_ids[]" value="<?php echo $li['id']; ?>" class="lead-check">
                                        </td>
                                        <td><?php echo $li['lead_id']; ?></td>
                                        <td><?php echo $li['customer']; ?></td>
                                        <td><?php echo $li['servicecat']; ?></td>
                                        <td><?php echo $li['mrr']; ?></td>
                                        <td><?php echo $li['poc']; ?></td>
                                        <td><?php echo $li['contact1']; ?></td>
                                        <td><span class="no_show"><?php echo date('Ymd',strtotime($li['followup_date'])); ?></span><?php echo simple_date($li['followup_date']); ?></td>
                                        <td>
                                            <?php echo $li['lead_status']; ?>
                                            <?php 
                                            $cat_label = !empty($li['status_category']) ? $li['status_category'] : (!empty($li['lost_reason']) ? $li['lost_reason'] : '');
                                            if (!empty($cat_label)): ?>
                                                <br><small class="text-muted" style="font-weight: 500;">Category: <?php echo htmlspecialchars($cat_label); ?></small>
                                            <?php endif; ?>
                                            <?php if (!empty($li['quote_status'])): ?>
                                                <br><span class="label label-info" style="font-size: 10px;"><?php echo htmlspecialchars($li['quote_status']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" style="min-width: 85px;">
                                                <button type="button" class="btn btn-sm btn-warning">Action</button>
                                                <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                                    <li> <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($li['id']); ?>" data-toggle="tooltip" title="View">Preview</a> </li>
                                                    <li> <a href="<?php echo base_url(); ?>manager/leads/edit/<?php echo base64_encode($li['id']); ?>" data-toggle="tooltip" title="Edit">Edit</a> </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <?php if (isset($links)) { echo $links; } ?>
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

<!-- Move Leads Modal -->
<div class="modal fade" id="modalMoveLeads" tabindex="-1" role="dialog" aria-labelledby="modalMoveLabel">
    <div class="modal-dialog" role="document">
        <form method="post" action="<?php echo base_url(); ?>manager/leads/bulk_move_assign" id="formBulkMove">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modalMoveLabel"><i class="fa fa-arrows" style="color:#b91c1c;"></i> Move Selected Leads</h4>
                </div>
                <div class="modal-body">
                    <p style="font-size:14px; margin-bottom:15px;">
                        You have selected <strong id="modalSelectedLeadCount" style="color:#b91c1c;">0</strong> lead(s) to move/reassign.
                    </p>
                    <div class="form-group">
                        <label style="font-weight:600;">Assign To User:</label>
                        <select name="target_user" class="form-control" required style="width:100%;">
                            <option value="">-- Select Destination User --</option>
                            <option value="0">Unassigned (Remove Assigned User)</option>
                            <?php if (!empty($users_list)) {
                                foreach ($users_list as $u) {
                                    echo '<option value="'.$u['id'].'">'.htmlspecialchars($u['name']).' ('.htmlspecialchars($u['email']).')</option>';
                                }
                            } ?>
                        </select>
                    </div>
                    <!-- Container for dynamic hidden inputs holding selected lead IDs -->
                    <div id="selectedLeadInputsContainer"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background:#b91c1c; border-color:#b91c1c;">Confirm Move</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(function() {
    function updateMoveButtonState() {
        var checked = $('.lead-check:checked');
        var count = checked.length;
        $('#selectedCount').text(count);
        $('#modalSelectedLeadCount').text(count);

        if (count > 0) {
            $('#btnMoveLeads').prop('disabled', false);
        } else {
            $('#btnMoveLeads').prop('disabled', true);
        }

        // Keep header checkbox in sync
        var total = $('.lead-check').length;
        if (total > 0 && count === total) {
            $('#checkAllLeads').prop('checked', true);
        } else {
            $('#checkAllLeads').prop('checked', false);
        }
    }

    // Header checkbox toggles all
    $('#checkAllLeads').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('.lead-check').prop('checked', isChecked);
        updateMoveButtonState();
    });

    // Individual checkbox change
    $(document).on('change', '.lead-check', function() {
        updateMoveButtonState();
    });

    // Before opening modal, populate hidden inputs with selected IDs
    $('#modalMoveLeads').on('show.bs.modal', function() {
        var container = $('#selectedLeadInputsContainer');
        container.empty();
        $('.lead-check:checked').each(function() {
            var val = $(this).val();
            container.append('<input type="hidden" name="lead_ids[]" value="' + val + '">');
        });
    });

    updateMoveButtonState();
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
