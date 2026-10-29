<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
    <section class="content-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h1><?php echo htmlspecialchars($info['page_heading'] ?? 'Onboarded Client Assignees'); ?></h1>
        <?php if ($can_edit) { ?>
        <div>
            <button type="button" class="btn btn-primary" id="btnAssignNewCompany" style="background:#0072f0; border-color:#0072f0; font-weight:600; border-radius:4px;">
                <i class="fa fa-user-plus"></i> Assign Company
            </button>
        </div>
        <?php } ?>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box p-4">
                    <div class="x_row">
                        <div class="x_col">
                            <form class="form-inline head_filter form-inline-mob" method="get" action="<?php echo base_url('manager/system/assignee'); ?>">
                                <div class="form-group">
                                    <input type="text" class="input-md form-control" name="search" placeholder="Search Company / Owner / POC" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" autocomplete="off" style="min-width: 260px;" />
                                </div>

                                <div class="form-group">
                                    <div class="input-daterange input-group" id="datepicker">
                                        <input type="text" class="input-md form-control datepicker" name="d1" placeholder="Onboarded From" value="<?php echo htmlspecialchars($_GET['d1'] ?? ''); ?>" autocomplete="off" />
                                        <span class="input-group-addon">To</span>
                                        <input type="text" class="input-md form-control datepicker" name="d2" placeholder="Onboarded To" value="<?php echo htmlspecialchars($_GET['d2'] ?? ''); ?>" autocomplete="off" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
                                    <a href="<?php echo htmlspecialchars($reset_url); ?>" class="btn btn-danger"><i class="fa fa-refresh"></i> Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="box-body table-responsive">
                        <?php echo get_message($this->session->flashdata('message'), 'message'); ?>
                        <?php if (!empty($clients)) { ?>
                            <table id="example_assignee" class="table table-hover table-bordered table-striped" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>Company / Client</th>
                                        <th>Owner Name</th>
                                        <th>Primary Assignee</th>
                                        <th>Collaborators</th>
                                        <th>Status</th>
                                        <th>Onboarded Date</th>
                                        <th style="width: 110px; text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $idx = 1;
                                    foreach ($clients as $c) { 
                                        $kam_id = (int)$c['id'];
                                        $primary_id = (int)($c['executive_id'] ?? 0);
                                        $primary_name = !empty($c['primary_assignee_name']) ? $c['primary_assignee_name'] : (!empty($c['executive_name']) ? $c['executive_name'] : 'Unassigned');
                                        
                                        $collab_ids = array();
                                        $collab_names = array();
                                        if (!empty($c['collaborators'])) {
                                            $parts = explode(',', (string)$c['collaborators']);
                                            foreach ($parts as $p) {
                                                $pid = (int)trim($p);
                                                if ($pid > 0) {
                                                    $collab_ids[] = $pid;
                                                    if (isset($user_map[$pid])) {
                                                        $collab_names[] = $user_map[$pid];
                                                    }
                                                }
                                            }
                                        }
                                        $collab_str = !empty($collab_names) ? implode(', ', $collab_names) : 'None';
                                        $collab_json = htmlspecialchars(json_encode($collab_ids), ENT_QUOTES, 'UTF-8');
                                        $client_name = !empty($c['client_name']) ? $c['client_name'] : 'Unknown Company';
                                        $owner_name = !empty($c['owner_name']) ? $c['owner_name'] : (!empty($c['lead_poc']) ? $c['lead_poc'] : '-');
                                    ?>
                                        <tr>
                                            <td><?php echo $idx++; ?></td>
                                            <td>
                                                <strong style="color: #0f172a; font-weight: 600;"><?php echo htmlspecialchars($client_name); ?></strong>
                                                <?php if (!empty($c['lead_id'])) { ?>
                                                    <br><small class="text-muted">Lead #<?php echo (int)$c['lead_id']; ?></small>
                                                <?php } ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($owner_name); ?></td>
                                            <td>
                                                <?php if ($primary_id > 0) { ?>
                                                    <span style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600; color: #1e293b;">
                                                        <i class="fa fa-user-circle" style="color: #0072f0; font-size: 15px;"></i>
                                                        <?php echo htmlspecialchars($primary_name); ?>
                                                    </span>
                                                <?php } else { ?>
                                                    <span class="text-muted" style="font-style: italic;">Unassigned</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($collab_names)) { ?>
                                                    <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                                        <?php foreach ($collab_names as $cn) { ?>
                                                            <span style="display: inline-block; padding: 2px 8px; font-size: 11px; font-weight: 500; color: #334155; background: #f1f5f9; border-radius: 4px; border: 1px solid #e2e8f0;"><?php echo htmlspecialchars($cn); ?></span>
                                                        <?php } ?>
                                                    </div>
                                                <?php } else { ?>
                                                    <span class="text-muted" style="font-size: 12px;">None</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <span class="label label-success" style="background:#16a34a; font-weight: 500; font-size: 11px; padding: 3px 7px;">Active</span>
                                            </td>
                                            <td>
                                                <?php echo !empty($c['created_at']) ? date('d-m-Y', strtotime($c['created_at'])) : '-'; ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php if ($can_edit) { ?>
                                                    <button type="button" class="btn btn-xs btn-primary btn-open-assign" 
                                                            style="background:#0072f0; border-color:#0072f0; padding: 4px 10px; font-size: 12px; font-weight: 500; border-radius: 4px;"
                                                            data-kam-id="<?php echo $kam_id; ?>"
                                                            data-client-name="<?php echo htmlspecialchars($client_name, ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-owner-name="<?php echo htmlspecialchars($owner_name, ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-primary-id="<?php echo $primary_id; ?>"
                                                            data-collabs='<?php echo $collab_json; ?>'>
                                                        <i class="fa fa-pencil"></i> Assign
                                                    </button>
                                                <?php } else { ?>
                                                    <span class="text-muted" style="font-size: 11px;"><i class="fa fa-lock"></i> View Only</span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <div class="alert alert-info" style="margin: 15px 0;"> No Data Found </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php if ($can_edit) { ?>
<div class="modal fade" id="assignModal" tabindex="-1" role="dialog" aria-labelledby="assignModalLabel">
    <div class="modal-dialog" role="document" style="max-width: 520px;">
        <div class="modal-content" style="border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
            <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 16px 20px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size: 24px; color: #64748b; opacity: 0.8;"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="assignModalLabel" style="font-weight: 700; color: #0f172a; font-size: 17px;">
                    <i class="fa fa-users" style="color: #0072f0; margin-right: 6px;"></i> Assign Client Team
                </h4>
            </div>
            <form id="assignTeamForm" method="post">
                <div class="modal-body" style="padding: 20px;">
                    <input type="hidden" name="kam_id" id="modal_kam_id" value="" />
                    <input type="hidden" name="lead_id" id="modal_lead_id" value="" />

                    <div class="form-group" style="margin-bottom: 16px;" id="group_client_name_static">
                        <label style="font-weight: 600; color: #334155; font-size: 13px;">Company Name</label>
                        <input type="text" id="modal_client_name" name="company_name" class="form-control" readonly style="background: #f8fafc; border: 1px solid #e2e8f0; font-weight: 600; color: #0f172a;" />
                    </div>

                    <div class="form-group" style="margin-bottom: 16px; display: none;" id="group_client_name_select">
                        <label style="font-weight: 600; color: #334155; font-size: 13px;">Search Any Company <span style="color: #dc2626;">*</span></label>
                        <select id="modal_company_search" class="form-control" style="width: 100%;">
                            <option value="">Type 2+ letters to search company...</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 16px;">
                        <label style="font-weight: 600; color: #334155; font-size: 13px;">Owner / Contact Name</label>
                        <input type="text" name="owner_name" id="modal_owner_name" class="form-control" style="background: #f8fafc; border: 1px solid #e2e8f0; color: #475569;" />
                    </div>

                    <div class="form-group" style="margin-bottom: 16px;">
                        <label style="font-weight: 600; color: #334155; font-size: 13px;">Primary Assignee <span style="color: #dc2626;">*</span></label>
                        <select name="primary_assignee" id="modal_primary_assignee" class="form-control" style="width: 100%;" required>
                            <option value="">-- Select Active System User --</option>
                            <?php if (!empty($users_list)) { 
                                foreach ($users_list as $u) { ?>
                                    <option value="<?php echo (int)$u['id']; ?>">
                                        <?php echo htmlspecialchars($u['name']); ?> <?php echo !empty($u['department']) ? '('.htmlspecialchars($u['department']).')' : ''; ?>
                                    </option>
                            <?php } } ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 8px;">
                        <label style="font-weight: 600; color: #334155; font-size: 13px;">Collaborators</label>
                        <select name="collaborators[]" id="modal_collaborators" class="form-control" multiple="multiple" style="width: 100%;">
                            <?php if (!empty($users_list)) { 
                                foreach ($users_list as $u) { ?>
                                    <option value="<?php echo (int)$u['id']; ?>">
                                        <?php echo htmlspecialchars($u['name']); ?> <?php echo !empty($u['department']) ? '('.htmlspecialchars($u['department']).')' : ''; ?>
                                    </option>
                            <?php } } ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #f1f5f9; padding: 14px 20px; background: #fafafa; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="border-color: #cbd5e1; color: #475569; font-weight: 500;">Cancel</button>
                    <button type="submit" id="btnSaveAssignee" class="btn btn-primary" style="background: #0072f0; border-color: #0072f0; font-weight: 600; padding: 6px 18px;">
                        <i class="fa fa-check"></i> Save Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>

<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#example_assignee')) {
        $('#example_assignee').DataTable().destroy();
    }
    $('#example_assignee').DataTable({
        "pageLength": 25,
        "responsive": true,
        "order": [[0, "asc"]],
        "columnDefs": [
            { "orderable": false, "targets": [7] }
        ]
    });

    if ($.fn.select2) {
        $('#modal_primary_assignee').select2({
            dropdownParent: $('#assignModal'),
            width: '100%'
        });
        $('#modal_collaborators').select2({
            dropdownParent: $('#assignModal'),
            width: '100%',
            placeholder: 'Select Collaborators'
        });
        $('#modal_company_search').select2({
            dropdownParent: $('#assignModal'),
            width: '100%',
            placeholder: 'Type 2+ letters to search any company...',
            allowClear: true,
            ajax: {
                url: '<?php echo base_url("manager/system/ajax_search_all_companies"); ?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return { results: data.results || [] };
                },
                cache: true
            }
        }).on('select2:select', function(e) {
            var data = e.params.data;
            $('#modal_client_name').val(data.id);
            $('#modal_lead_id').val(data.lead_id || '');
            if (data.owner) {
                $('#modal_owner_name').val(data.owner);
            }
        });
    }

    $(document).on('click', '#btnAssignNewCompany', function(e) {
        e.preventDefault();
        $('#modal_kam_id').val('0');
        $('#modal_lead_id').val('');
        $('#modal_client_name').val('');
        $('#modal_owner_name').val('').removeAttr('readonly').css('background', '#ffffff');
        $('#group_client_name_static').hide();
        $('#group_client_name_select').show();
        if ($.fn.select2) {
            $('#modal_company_search').val(null).trigger('change');
            $('#modal_primary_assignee').val('').trigger('change');
            $('#modal_collaborators').val([]).trigger('change');
        }
        $('#assignModalLabel').html('<i class="fa fa-user-plus" style="color: #0072f0; margin-right: 6px;"></i> Assign Company');
        $('#assignModal').modal('show');
    });

    $(document).on('click', '.btn-open-assign', function(e) {
        e.preventDefault();
        var kamId = $(this).data('kam-id');
        var clientName = $(this).data('client-name');
        var ownerName = $(this).data('owner-name');
        var primaryId = $(this).data('primary-id');
        var collabs = $(this).data('collabs');

        $('#modal_kam_id').val(kamId);
        $('#modal_lead_id').val('');
        $('#modal_client_name').val(clientName);
        $('#modal_owner_name').val(ownerName).attr('readonly', 'readonly').css('background', '#f8fafc');
        $('#group_client_name_static').show();
        $('#group_client_name_select').hide();

        if ($.fn.select2) {
            $('#modal_primary_assignee').val(primaryId ? String(primaryId) : '').trigger('change');
            var collabArr = Array.isArray(collabs) ? collabs.map(String) : [];
            $('#modal_collaborators').val(collabArr).trigger('change');
        } else {
            $('#modal_primary_assignee').val(primaryId);
            $('#modal_collaborators').val(collabs);
        }

        $('#assignModalLabel').html('<i class="fa fa-users" style="color: #0072f0; margin-right: 6px;"></i> Assign Client Team');
        $('#assignModal').modal('show');
    });

    $('#assignTeamForm').on('submit', function(e) {
        e.preventDefault();
        var btn = $('#btnSaveAssignee');
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        var formData = $(this).serialize();
        $.ajax({
            url: '<?php echo base_url("manager/system/ajax_update_assignee"); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(resp) {
                btn.prop('disabled', false).html('<i class="fa fa-check"></i> Save Assignment');
                if (resp && resp.success) {
                    $('#assignModal').modal('hide');
                    if (typeof swal === 'function') {
                        swal({
                            title: "Success",
                            text: resp.message,
                            type: "success",
                            timer: 1500,
                            showConfirmButton: false
                        }, function() {
                            location.reload();
                        });
                    } else {
                        alert(resp.message);
                        location.reload();
                    }
                } else {
                    var msg = (resp && resp.message) ? resp.message : 'Failed to update assignment';
                    if (typeof swal === 'function') {
                        swal("Error", msg, "error");
                    } else {
                        alert(msg);
                    }
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<i class="fa fa-check"></i> Save Assignment');
                if (typeof swal === 'function') {
                    swal("Error", "Network or server error while updating assignment.", "error");
                } else {
                    alert("Network error while updating assignment.");
                }
            }
        });
    });
});
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
