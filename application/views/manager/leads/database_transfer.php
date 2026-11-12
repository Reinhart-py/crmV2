<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
<?php echo crm_mz_tabbar(); ?>
    <section class="content-header"><h1>Database Transfer</h1></section>
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

                                <?php 
                                $m_role = (int)$this->session->userdata('manager_role');
                                if(($m_role === 1 || crm_can('all_data')) && !empty($all_users)){ ?>
                                <div class="form-group">
                                    <select class="form-control" name="from_user" onchange="this.form.submit()">
                                        <option value="<?php echo (int)$this->session->userdata('manager_id'); ?>">Current User (Self)</option>
                                        <?php foreach($all_users as $au){ ?>
                                            <option value="<?php echo $au['id']; ?>" <?php if(isset($from_user) && (int)$from_user === (int)$au['id']){ echo 'selected'; } ?>>
                                                From: <?php echo htmlspecialchars($au['name']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <?php } ?>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Search</button>
                                    <a href="<?php echo base_url(); ?>manager/database-transfer" class="btn btn-danger">Reset</a>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="box-header with-border" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; padding:10px 15px;">
                        <h3 class="box-title" style="margin:0; font-size:16px; font-weight:600;">Database Records</h3>
                        <?php $can_copy = crm_is_full_admin(); ?>
                        <form method="post" action="<?php echo base_url(); ?>manager/leads/bulk-transfer" id="formBulkTransfer" class="form-inline" style="margin:0;">
                            <div id="inlineTransferLeadInputs"></div>
                            <?php if ($can_copy) { ?>
                            <input type="hidden" name="mode" id="dt_mode" value="transfer">
                            <input type="hidden" name="from_user" value="<?php echo (int)$from_user; ?>">
                            <div id="copyTargetInputs"></div>
                            <?php } ?>
                            <div style="display:inline-flex; align-items:center; gap:8px; flex-wrap:wrap; max-width:100%;">
                                <span id="selectedCountText" class="label label-default" style="font-size:12px; font-weight:600; padding:4px 8px;">0 selected</span>
                                <?php if ($can_copy) { ?>
                                <div class="btn-group btn-group-sm dt-mode" role="group" aria-label="Transfer or copy">
                                    <button type="button" class="btn btn-default active" data-mode="transfer" title="Move the records to one person. The current owner no longer has them.">Transfer</button>
                                    <button type="button" class="btn btn-default" data-mode="copy" title="Give a copy to one or more people. The original records stay where they are.">Copy</button>
                                </div>
                                <?php } ?>

                                <div style="position:relative; display:inline-block; max-width:100%;">
                                    <div class="input-group input-group-sm" style="width:240px; max-width:100%;">
                                        <input type="text" id="transfer_user_search" class="form-control" placeholder="Type 3+ letters to search..." autocomplete="off">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button" id="btnClearTransferUser" title="Clear"><i class="fa fa-times"></i></button>
                                        </span>
                                    </div>
                                    <input type="hidden" name="target_user" id="target_user_val" required>
                                    <div id="transfer_user_dropdown" style="display:none; position:absolute; top:100%; left:0; width:100%; z-index:9999; background:#fff; border:1px solid #cbd5e1; border-radius:4px; box-shadow:0 8px 16px rgba(0,0,0,0.12); max-height:200px; overflow-y:auto; margin-top:2px;"></div>
                                </div>

                                <button type="submit" id="btnConfirmTransfer" class="btn btn-sm btn-primary" disabled style="background:#1e3a8a; border-color:#1e3a8a; font-weight:600;">
                                    <i class="fa fa-exchange"></i> <span id="btnTransferText">Transfer</span>
                                </button>
                            </div>
                            <?php if ($can_copy) { ?>
                            <div id="copyTargetChips" class="dt-chips" style="display:none;"></div>
                            <?php } ?>
                        </form>
                    </div>
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
                                            <input type="checkbox" name="lead_ids_table[]" value="<?php echo $li['id']; ?>" class="lead-check" data-customer="<?php echo htmlspecialchars($li['customer']); ?>">
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
                                                <span class="label label-warning" style="font-size:11px; background:#475569; color:#fff;">Unassigned</span>
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
                                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                                    <li> <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($li['id']); ?>"
                                                            data-toggle="tooltip" title="View">Preview</a> </li>
                                                    <li> <a href="<?php echo base_url(); ?>manager/leads/edit/<?php echo base64_encode($li['id']); ?>"
                                                            data-toggle="tooltip" title="Edit">Edit</a> </li>
                                                    <li> <a href="javascript:void(0);" onclick="transferSingleLead(<?php echo $li['id']; ?>, '<?php echo addslashes(htmlspecialchars($li['customer'])); ?>')">
                                                            <i class="fa fa-exchange" style="color:#1e3a8a; margin-right:4px;"></i> Transfer</a> </li>
                                                    <?php if ($can_copy) { ?>
                                                    <li> <a href="javascript:void(0);" onclick="copySingleLead(<?php echo (int)$li['id']; ?>)">
                                                            <i class="fa fa-copy" style="color:#1e3a8a; margin-right:4px;"></i> Copy</a> </li>
                                                    <?php } ?>
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

<style>
.dt-mode .btn { font-weight: 600; }
.dt-mode .btn.active { background: #1e3a8a; border-color: #1e3a8a; color: #fff; box-shadow: none; }
.dt-chips { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
.dt-chip { display: inline-flex; align-items: center; gap: 4px; padding: 3px 4px 3px 10px; border-radius: 999px; background: #eef2ff; color: #1e3a8a; font-size: 12px; font-weight: 600; }
.dt-chip button { border: 0; background: transparent; color: #64748b; font-size: 15px; line-height: 1; padding: 0 4px; cursor: pointer; }
.dt-chip button:hover { color: #dc2626; }
[data-theme="dark"] .dt-chip { background: #1e293b; color: #c7d2fe; }
</style>
<script>
var transferUsers = <?php
  $clean_tu = array();
  if(!empty($users_list)){
    foreach($users_list as $u){
      $clean_tu[] = array(
        'id' => (int)$u['id'],
        'name' => htmlspecialchars($u['name']),
        'email' => htmlspecialchars($u['email'])
      );
    }
  }
  echo json_encode($clean_tu);
?>;

var dtMode = 'transfer';
var copyTargets = [];

$(function() {
    function syncTransferState() {
        var checked = $('.lead-check:checked');
        var count = checked.length;
        $('#selectedCountText').text(count + ' selected');
        if (count > 0) {
            $('#selectedCountText').removeClass('label-default').addClass('label-primary');
        } else {
            $('#selectedCountText').removeClass('label-primary').addClass('label-default');
        }

        var container = $('#inlineTransferLeadInputs');
        container.empty();
        checked.each(function() {
            var val = $(this).val();
            container.append('<input type="hidden" name="lead_ids[]" value="' + val + '">');
        });

        var targetUser = $('#target_user_val').val();
        var ready = dtMode === 'copy' ? copyTargets.length > 0 : targetUser !== '';
        $('#btnConfirmTransfer').prop('disabled', !(count > 0 && ready));
        if (dtMode === 'copy') {
            $('#btnTransferText').text(copyTargets.length > 1 ? 'Copy to ' + copyTargets.length + ' people' : 'Copy');
        }

        var total = $('.lead-check').length;
        $('#checkAllLeads').prop('checked', (total > 0 && count === total));
    }

    $('#checkAllLeads').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('.lead-check').prop('checked', isChecked);
        syncTransferState();
    });

    $(document).on('change', '.lead-check', function() {
        syncTransferState();
    });

    var searchInput = document.getElementById("transfer_user_search");
    var hiddenInput = document.getElementById("target_user_val");
    var dropdown = document.getElementById("transfer_user_dropdown");

    if (searchInput) {
        searchInput.addEventListener("input", function() {
            var query = this.value.trim().toLowerCase();
            dropdown.innerHTML = "";
            
            if (query.length < 3) {
                dropdown.style.display = "none";
                hiddenInput.value = "";
                syncTransferState();
                return;
            }

            var matches = transferUsers.filter(function(u) {
                return u.name.toLowerCase().indexOf(query) !== -1 || u.email.toLowerCase().indexOf(query) !== -1;
            });

            if (matches.length === 0) {
                dropdown.innerHTML = '<div style="padding:8px 12px; color:#64748b; font-size:12px;">No matching member</div>';
                dropdown.style.display = "block";
                return;
            }

            matches.forEach(function(u) {
                var item = document.createElement("div");
                item.style.padding = "8px 12px";
                item.style.cursor = "pointer";
                item.style.borderBottom = "1px solid #f1f5f9";
                item.style.fontSize = "12px";
                item.innerHTML = '<strong>' + u.name + '</strong> <span style="color:#64748b; font-size:11px;">(' + u.email + ')</span>';
                
                item.addEventListener("mouseenter", function() {
                    this.style.background = "#eff6ff";
                });
                item.addEventListener("mouseleave", function() {
                    this.style.background = "#fff";
                });

                item.addEventListener("click", function() {
                    if (dtMode === 'copy') {
                        // Copy: several people can be chosen; each shows as a chip
                        if (!copyTargets.some(function(x) { return x.id === u.id; })) copyTargets.push(u);
                        searchInput.value = "";
                        dropdown.style.display = "none";
                        renderCopyTargets();
                        searchInput.focus();
                        return;
                    }
                    searchInput.value = u.name;
                    hiddenInput.value = u.id;
                    dropdown.style.display = "none";
                    syncTransferState();
                });

                dropdown.appendChild(item);
            });

            dropdown.style.display = "block";
        });
    }

    var btnClear = document.getElementById("btnClearTransferUser");
    if (btnClear && searchInput) {
        btnClear.addEventListener("click", function() {
            searchInput.value = "";
            hiddenInput.value = "";
            dropdown.style.display = "none";
            syncTransferState();
            searchInput.focus();
        });
    }

    document.addEventListener("click", function(e) {
        if (searchInput && dropdown && !searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = "none";
        }
    });

    // Transfer / Copy switch (admins only)
    function renderCopyTargets() {
        var chips = $('#copyTargetChips').empty();
        var inputs = $('#copyTargetInputs').empty();
        copyTargets.forEach(function(u) {
            inputs.append($('<input type="hidden" name="target_users[]">').val(u.id));
            var chip = $('<span class="dt-chip"></span>').html(u.name + ' ');
            var x = $('<button type="button" title="Remove">&times;</button>').on('click', function() {
                copyTargets = copyTargets.filter(function(y) { return y.id !== u.id; });
                renderCopyTargets();
            });
            chips.append(chip.append(x));
        });
        chips.toggle(dtMode === 'copy' && copyTargets.length > 0);
        syncTransferState();
    }
    window.renderCopyTargets = renderCopyTargets;

    window.setDtMode = function(mode) {
        dtMode = mode === 'copy' ? 'copy' : 'transfer';
        $('#dt_mode').val(dtMode);
        $('.dt-mode .btn').removeClass('active').filter('[data-mode="' + dtMode + '"]').addClass('active');
        $('#btnConfirmTransfer i').attr('class', dtMode === 'copy' ? 'fa fa-copy' : 'fa fa-exchange');
        $('#btnTransferText').text(dtMode === 'copy' ? 'Copy' : 'Transfer');
        $('#transfer_user_search').attr('placeholder', dtMode === 'copy' ? 'Add people: type 3+ letters...' : 'Type 3+ letters to search...').val('');
        $('#target_user_val').val('');
        if (dtMode === 'transfer') copyTargets = [];
        renderCopyTargets();
    };
    $('.dt-mode .btn').on('click', function() { setDtMode($(this).data('mode')); });

    $('#formBulkTransfer').on('submit', function(e) {
        if (dtMode !== 'copy') return;
        var n = $('#inlineTransferLeadInputs input').length;
        var names = copyTargets.map(function(u) { return $('<div>').html(u.name).text(); }).join(', ');
        if (!confirm('Copy ' + n + ' record' + (n === 1 ? '' : 's') + ' to ' + names + '?\n\nThe original records stay where they are. Each person gets their own copy.')) e.preventDefault();
    });

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

function copySingleLead(id) {
    if (window.setDtMode && dtMode !== 'copy') setDtMode('copy');
    transferSingleLead(id, '');
}

function transferSingleLead(id, customerName) {
    $('.lead-check').prop('checked', false);
    var targetCheck = $('.lead-check[value="' + id + '"]');
    if (targetCheck.length) {
        targetCheck.prop('checked', true);
    }
    var container = $('#inlineTransferLeadInputs');
    container.empty();
    container.append('<input type="hidden" name="lead_ids[]" value="' + id + '">');
    $('#selectedCountText').text('1 selected').removeClass('label-default').addClass('label-primary');

    var targetUser = $('#target_user_val').val();
    var ready = dtMode === 'copy' ? copyTargets.length > 0 : targetUser !== '';
    $('#btnConfirmTransfer').prop('disabled', !ready);
    $('#transfer_user_search').focus();
    $('html, body').animate({
        scrollTop: $('#formBulkTransfer').offset().top - 80
    }, 200);
}
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
