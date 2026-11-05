<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$g = function ($k) { return isset($_GET[$k]) && is_string($_GET[$k]) ? $_GET[$k] : ''; };
$clients = isset($clients) && is_array($clients) ? $clients : array();
$k_none = 0; $k_team = 0;
foreach ($clients as $c) {
    if ((int)($c['executive_id'] ?? 0) <= 0) $k_none++;
    if (trim((string)($c['collaborators'] ?? ''), ', ') !== '') $k_team++;
}
$base = base_url('manager/system/assignee');
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_6">
<style>
  .as-collab { display: flex; flex-wrap: wrap; gap: 4px; }
  .sx .sx-bar .input-daterange { width: auto; flex: 0 1 280px; }
  .sx .sx-bar .input-daterange .form-control { min-width: 0; width: 100%; }
  @media (max-width: 767px) { .sx .sx-bar .input-daterange { flex: 1 1 100%; width: 100%; } }
  #assignModal .modal-content { border-radius: 12px; }
  #assignModal label { font-weight: 600; font-size: 13px; }
</style>
<div class="content-wrapper sx">
    <section class="sx-head">
        <h1>Assignee Management<small>Who looks after each onboarded client, and who helps</small></h1>
        <?php if ($can_edit) { ?>
        <div class="sx-btns">
            <button type="button" class="btn btn-primary" id="btnAssignNewCompany"><i class="fa fa-user-plus"></i> Assign company</button>
        </div>
        <?php } ?>
    </section>

    <section class="content">
        <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
        <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

        <div class="sx-kpis">
            <a class="sx-kpi <?php echo $g('owner') === '' ? 'is-on' : ''; ?>" href="<?php echo $base; ?>"><span class="ic"><i class="fa fa-building"></i></span><div><b><?php echo number_format(count($clients)); ?></b><span><?php echo $g('owner') === '' && $g('search') === '' && $g('d1') === '' ? 'Onboarded clients' : 'Clients shown'; ?></span></div></a>
            <a class="sx-kpi a <?php echo $g('owner') === 'none' ? 'is-on' : ''; ?>" href="<?php echo $base; ?>?owner=none"><span class="ic"><i class="fa fa-user-times"></i></span><div><b><?php echo number_format($k_none); ?></b><span>No owner yet</span></div></a>
            <div class="sx-kpi g"><span class="ic"><i class="fa fa-users"></i></span><div><b><?php echo number_format($k_team); ?></b><span>With collaborators</span></div></div>
        </div>

        <div class="sx-card">
            <form class="sx-bar" method="get" action="<?php echo $base; ?>">
                <input type="text" class="form-control sx-q" name="search" placeholder="Company, owner, contact or phone" value="<?php echo $h($g('search')); ?>" autocomplete="off" />
                <div class="input-daterange input-group" id="datepicker">
                    <input type="text" class="form-control datepicker" name="d1" placeholder="Onboarded from" value="<?php echo $h($g('d1')); ?>" autocomplete="off" />
                    <span class="input-group-addon">to</span>
                    <input type="text" class="form-control datepicker" name="d2" placeholder="To" value="<?php echo $h($g('d2')); ?>" autocomplete="off" />
                </div>
                <select name="owner" class="form-control">
                    <option value="">Any owner</option>
                    <option value="none" <?php echo $g('owner') === 'none' ? 'selected' : ''; ?>>No owner yet</option>
                    <?php foreach ((array)$users_list as $u) { ?>
                        <option value="<?php echo (int)$u['id']; ?>" <?php echo $g('owner') === (string)$u['id'] ? 'selected' : ''; ?>><?php echo $h($u['name']); ?></option>
                    <?php } ?>
                </select>
                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                <a href="<?php echo $h($reset_url); ?>" class="btn btn-default">Reset</a>
            </form>

            <?php if (!empty($clients)) { ?>
            <div class="sx-table-wrap">
                <table id="example_assignee" class="sx-table sx-cards" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Company / client</th>
                            <th>Business owner</th>
                            <th>Primary assignee</th>
                            <th>Collaborators</th>
                            <th>Onboarded</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $c) {
                            $kam_id = (int)$c['id'];
                            $primary_id = (int)($c['executive_id'] ?? 0);
                            $primary_name = !empty($c['primary_assignee_name']) ? $c['primary_assignee_name'] : (!empty($c['executive_name']) ? $c['executive_name'] : '');
                            $collab_ids = array();
                            $collab_names = array();
                            foreach (explode(',', (string)($c['collaborators'] ?? '')) as $p) {
                                $pid = (int)trim($p);
                                if ($pid > 0) {
                                    $collab_ids[] = $pid;
                                    if (isset($user_map[$pid])) $collab_names[] = $user_map[$pid];
                                }
                            }
                            $collab_json = htmlspecialchars(json_encode($collab_ids), ENT_QUOTES, 'UTF-8');
                            $client_name = !empty($c['client_name']) ? $c['client_name'] : 'Unknown company';
                            $owner_name = !empty($c['owner_name']) ? $c['owner_name'] : (!empty($c['lead_poc']) ? $c['lead_poc'] : '-');
                        ?>
                        <tr>
                            <td data-l="Company" class="full">
                                <span class="nm"><?php echo $h($client_name); ?></span>
                                <?php if (!empty($c['lead_id'])) { ?><div class="sub">Lead #<?php echo (int)$c['lead_id']; ?><?php echo !empty($c['lead_contact']) ? ' · ' . $h($c['lead_contact']) : ''; ?></div><?php } ?>
                            </td>
                            <td data-l="Owner"><?php echo $h($owner_name); ?></td>
                            <td data-l="Assignee">
                                <?php if ($primary_id > 0 && $primary_name !== '') { ?>
                                    <span class="who"><span class="av"><?php echo $h(strtoupper(substr($primary_name, 0, 1))); ?></span><span><?php echo $h($primary_name); ?></span></span>
                                <?php } else { ?>
                                    <span class="chip w">No owner yet</span>
                                <?php } ?>
                            </td>
                            <td data-l="Collaborators">
                                <?php if (!empty($collab_names)) { ?>
                                    <div class="as-collab"><?php foreach ($collab_names as $cn) { ?><span class="chip na"><?php echo $h($cn); ?></span><?php } ?></div>
                                <?php } else { ?>
                                    <span class="sub">None</span>
                                <?php } ?>
                            </td>
                            <td data-l="Onboarded" class="nw" data-order="<?php echo !empty($c['created_at']) ? date('YmdHis', strtotime($c['created_at'])) : 0; ?>"><?php echo !empty($c['created_at']) ? date('d M Y', strtotime($c['created_at'])) : '-'; ?></td>
                            <td data-l="" class="full">
                                <?php if ($can_edit) { ?>
                                    <button type="button" class="btn btn-default btn-sm btn-open-assign"
                                            data-kam-id="<?php echo $kam_id; ?>"
                                            data-client-name="<?php echo $h($client_name); ?>"
                                            data-owner-name="<?php echo $h($owner_name); ?>"
                                            data-primary-id="<?php echo $primary_id; ?>"
                                            data-collabs='<?php echo $collab_json; ?>'>
                                        <i class="fa fa-pencil"></i> Change
                                    </button>
                                <?php } else { ?>
                                    <span class="sub"><i class="fa fa-lock"></i> View only</span>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php } else { ?>
                <div class="sx-card-b"><div class="sx-empty"><i class="fa fa-building"></i> No clients match these filters.</div></div>
            <?php } ?>
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
                    <i class="fa fa-users" style="color: #1e3a8a; margin-right: 6px;"></i> Assign Client Team
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
                    <button type="submit" id="btnSaveAssignee" class="btn btn-primary" style="background: #1e3a8a; border-color: #1e3a8a; font-weight: 600; padding: 6px 18px;">
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
        "autoWidth": false,
        "order": [[4, "desc"]],
        "language": { "search": "", "searchPlaceholder": "Search this list" },
        "columnDefs": [
            { "orderable": false, "targets": [5] }
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
        $('#assignModalLabel').html('<i class="fa fa-user-plus" style="color: #1e3a8a; margin-right: 6px;"></i> Assign Company');
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

        $('#assignModalLabel').html('<i class="fa fa-users" style="color: #1e3a8a; margin-right: 6px;"></i> Assign Client Team');
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
