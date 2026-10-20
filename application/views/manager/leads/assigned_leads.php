<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper px-4">
    <section class="content-header">
        <h1> <?php echo isset($info['page_heading']) ? $info['page_heading'] : 'Allotted Database'; ?> </h1>
    </section>

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
                                    <select class="form-control select2" name="country[]" data-placeholder="Select Country" id="select_val" onchange="ajax_get_state()">
                                        <option value="">Select Country</option>
                                        <?php if(isset($c_list) && !empty($c_list)){
                                            foreach($c_list as $cl_item){ ?>
                                                <option <?php if(isset($_GET['country']) && !empty($_GET['country']) && in_array($cl_item['id'], $_GET['country'])){ echo "selected"; } ?> value="<?php echo $cl_item['id']; ?>"><?php echo htmlspecialchars($cl_item['name']); ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <select class="form-control multiselect-ui select3" multiple="multiple" name="state[]" data-placeholder="Select State" id="select_state">
                                        <?php if(isset($state_list) && !empty($state_list)){
                                            foreach($state_list as $st_item){ ?>
                                                <option <?php if(isset($_GET['state']) && !empty($_GET['state']) && in_array($st_item['id'], $_GET['state'])){ echo "selected"; } ?> value="<?php echo $st_item['id']; ?>"><?php echo htmlspecialchars($st_item['name']); ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Search</button>
                                    <a href="<?php echo base_url(); ?>manager/assigned-leads" class="btn btn-danger">Reset</a>
                                </div>

                            </form>
                        </div>
                        <div class="x_col-sm-auto text-center pt-4 pt-md-0">
                            <div>
                                <button type="button" class="btn btn-primary" id="btnMoveLeads" disabled data-toggle="modal" data-target="#modalMoveLeads" style="font-weight:600; background:#b91c1c; border-color:#b91c1c;">
                                    <i class="fa fa-share-square-o"></i> Allocate Selected (<span id="selectedCount">0</span>)
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
                                        <th>Date</th>
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
                                        <th></th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php foreach($product_list as $k => $li) { ?>
                                    <tr>
                                        <td style="text-align: center;">
                                            <input type="checkbox" name="lead_ids[]" value="<?php echo $li['id']; ?>" class="lead-check">
                                        </td>
                                        <td><a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($li['id']); ?>/tm"><strong><?php echo $li['lead_id']; ?></strong></a></td>
                                        <td>
                                            <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($li['id']); ?>/tm"><?php echo $li['customer']; ?></a>
                                            <?php if(!empty($li['dnd_status']) && strtolower($li['dnd_status']) == 'yes'){ ?>
                                                <span class="label label-danger" style="background:#dc2626; color:#ffffff; font-weight:700; padding:1px 5px; border-radius:3px; font-size:10px; margin-left:4px; letter-spacing:0.5px;">DNCR</span>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo isset($li['servicecat']) ? $li['servicecat'] : ''; ?></td>
                                        <td><?php echo isset($li['mrr']) ? $li['mrr'] : '0'; ?></td>
                                        <td><?php echo isset($li['poc']) ? $li['poc'] : ''; ?></td>
                                        <td><?php echo isset($li['contact1']) ? $li['contact1'] : ''; ?></td>
                                        <td><span class="no_show"><?php echo !empty($li['followup_date']) ? date('Ymd', strtotime($li['followup_date'])) : ''; ?></span><?php echo !empty($li['followup_date']) ? simple_date($li['followup_date']) : '-'; ?></td>
                                        <td><span class="no_show"><?php echo !empty($li['created']) ? date('Ymd', strtotime($li['created'])) : ''; ?></span><?php echo !empty($li['created']) ? simple_date($li['created']) : '-'; ?></td>
                                        <td>
                                            <?php if(!empty($li['owner_name'])){ ?>
                                                <?php echo htmlspecialchars($li['owner_name']); ?>
                                            <?php } else { ?>
                                                <span class="label label-warning" style="background:#f59e0b; color:#fff;">Non-Allotted</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php echo isset($li['lead_status']) ? $li['lead_status'] : ''; ?>
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
                                                    <li> <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($li['id']); ?>/tm" data-toggle="tooltip" title="View">Preview</a> </li>
                                                    <li> <a href="<?php echo base_url(); ?>manager/leads/edit/<?php echo base64_encode($li['id']); ?>/tm" data-toggle="tooltip" title="Edit">Edit</a> </li>
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

<!-- Modal: Allocate Selected Leads -->
<div class="modal fade" id="modalMoveLeads" tabindex="-1" role="dialog" aria-labelledby="modalMoveLeadsLabel">
    <div class="modal-dialog" role="document">
        <form action="<?php echo base_url(); ?>manager/leads/bulk-move" method="post" id="formBulkMove">
            <div class="modal-content">
                <div class="modal-header" style="background:#b91c1c; color:#fff;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff; opacity:0.9;"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modalMoveLeadsLabel"><i class="fa fa-share-square-o"></i> Allocate Selected Leads</h4>
                </div>
                <div class="modal-body">
                    <p style="font-size:14px;">
                        You have selected <strong id="modalSelectedCount" style="color:#b91c1c;">0</strong> database record(s).
                    </p>
                    <div id="hiddenLeadInputs"></div>
                    <div class="form-group" style="margin-top:15px;">
                        <label for="target_user" style="font-weight:600;">Allocate To User (Lead Owner):</label>
                        <select name="target_user" id="target_user" class="form-control select2" style="width:100%;" required>
                            <option value="">-- Select Target User --</option>
                            <option value="0">Unassigned (Non-Allotted Database)</option>
                            <?php if(!empty($users_list)){
                                foreach($users_list as $u){ ?>
                                    <option value="<?php echo $u['id']; ?>">
                                        <?php echo htmlspecialchars($u['name']); ?> (<?php echo htmlspecialchars($u['email']); ?>)
                                    </option>
                            <?php } } ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background:#b91c1c; border-color:#b91c1c;">Confirm Allocation</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
function ajax_get_state() {
    var countryID = $("#select_val").val();
    if (!countryID) return;
    $.ajax({
        type: "POST",
        data: 'country_id=' + countryID,
        url: "<?php echo base_url(); ?>ci_admin_leads/ajax_get_state",
        success: function(result) {
            if (result) {
                $('#select_state').html(result);
                $('#select_state').multiselect('rebuild');
            }
        }
    });
}

$(document).ready(function() {
    if ($('#example_lead').length) {
        var table = $('#example_lead').DataTable({
            "pageLength": 50,
            responsive: true,
            "order": [],
            columnDefs: [
                { orderable: false, targets: 0 },
                { targets: [7, 8, 11], className: "dt-nowrap" }
            ],
            initComplete: function () {
                this.api().columns([4, 9, 10]).every(function () {
                    var column = this;
                    var select = $('<select><option value="">Select </option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });
                    column.data().unique().sort().each(function (d, j) {
                        var textVal = $('<div>').html(d).text().trim();
                        if (textVal) {
                            select.append('<option value="' + textVal + '">' + textVal + '</option>');
                        }
                    });
                });
            }
        });
    }

    function updateSelection() {
        var checked = $('.lead-check:checked');
        var count = checked.length;
        $('#selectedCount').text(count);
        $('#modalSelectedCount').text(count);
        if (count > 0) {
            $('#btnMoveLeads').prop('disabled', false);
        } else {
            $('#btnMoveLeads').prop('disabled', true);
        }
    }

    $('#checkAllLeads').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('.lead-check').prop('checked', isChecked);
        updateSelection();
    });

    $(document).on('change', '.lead-check', function() {
        var total = $('.lead-check').length;
        var checked = $('.lead-check:checked').length;
        $('#checkAllLeads').prop('checked', total > 0 && total === checked);
        updateSelection();
    });

    $('#btnMoveLeads').on('click', function() {
        var container = $('#hiddenLeadInputs');
        container.empty();
        $('.lead-check:checked').each(function() {
            var val = $(this).val();
            container.append('<input type="hidden" name="lead_ids[]" value="' + val + '">');
        });
    });
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
