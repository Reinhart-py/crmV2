<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
    <section class="content-header">
        <h1><?php echo htmlspecialchars($info['page_heading'] ?? 'KAM Clients'); ?></h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box p-4">
                    <div class="x_row">
                        <div class="x_col">
                            <form class="form-inline head_filter form-inline-mob" method="get" action="<?php echo base_url('manager/system/kam_clients'); ?>">
                                <div class="form-group" style="min-width: 320px;">
                                    <select name="company" id="kamCompanySearch" class="form-control" style="width: 100%;">
                                        <?php if (!empty($_GET['company'])) { ?>
                                            <option value="<?php echo htmlspecialchars($_GET['company']); ?>" selected="selected"><?php echo htmlspecialchars($_GET['company']); ?></option>
                                        <?php } else { ?>
                                            <option value="">Type 3+ letters to search company...</option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <input type="text" class="input-md form-control" name="search" placeholder="Search Owner / POC / Contact" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" autocomplete="off" style="min-width: 240px;" />
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
                            <table id="example_kam_clients" class="table table-hover table-bordered table-striped" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>Company Name</th>
                                        <th>Owner / POC</th>
                                        <th>Contact Details</th>
                                        <th>Primary Assignee</th>
                                        <th>Collaborators</th>
                                        <th>Trade License / Emirate</th>
                                        <th>Onboarded</th>
                                        <th style="width: 110px; text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $idx = 1;
                                    foreach ($clients as $c) {
                                        $lead_id = (int)($c['lead_id'] ?? 0);
                                        $encoded_lead_id = $lead_id > 0 ? base64_encode($lead_id) : '';
                                        $primary_id = (int)($c['executive_id'] ?? 0);
                                        $collab_ids = array();
                                        if (!empty($c['collaborators'])) {
                                            $parts = explode(',', (string)$c['collaborators']);
                                            foreach ($parts as $p) {
                                                $pid = (int)trim($p);
                                                if ($pid > 0) {
                                                    $collab_ids[] = $pid;
                                                }
                                            }
                                        }

                                        $is_assigned = ($is_admin || $primary_id === $cur_uid || in_array($cur_uid, $collab_ids));
                                        $client_name = !empty($c['client_name']) ? $c['client_name'] : 'Unknown Company';
                                        $owner_name = !empty($c['owner_name']) ? $c['owner_name'] : (!empty($c['lead_poc']) ? $c['lead_poc'] : '-');
                                        $contact = !empty($c['lead_contact']) ? $c['lead_contact'] : '-';
                                        $email = !empty($c['lead_email']) ? $c['lead_email'] : '';
                                        $tl = !empty($c['tl_number']) ? $c['tl_number'] : '-';
                                        $emirate = !empty($c['tl_emirate']) ? $c['tl_emirate'] : '';
                                    ?>
                                        <tr>
                                            <td><?php echo $idx++; ?></td>
                                            <td>
                                                <strong style="color: #0f172a; font-weight: 600;"><?php echo htmlspecialchars($client_name); ?></strong>
                                                <?php if ($lead_id > 0) { ?>
                                                    <br><small class="text-muted">Lead #<?php echo $lead_id; ?></small>
                                                <?php } ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($owner_name); ?></td>
                                            <td>
                                                <span style="font-weight: 500; color: #1e293b;"><?php echo htmlspecialchars($contact); ?></span>
                                                <?php if (!empty($email)) { ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($email); ?></small>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($c['assignee_name'])) { ?>
                                                    <span style="display: inline-flex; align-items: center; gap: 5px; font-weight: 600; color: #1e293b;">
                                                        <i class="fa fa-user" style="color: #0072f0;"></i>
                                                        <?php echo htmlspecialchars($c['assignee_name']); ?>
                                                    </span>
                                                <?php } else { ?>
                                                    <span class="text-muted" style="font-style: italic;">Unassigned</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($collab_ids)) { ?>
                                                    <span class="label label-default" style="font-weight: 500; font-size: 11px;"><?php echo count($collab_ids); ?> assigned</span>
                                                <?php } else { ?>
                                                    <span class="text-muted" style="font-size: 11px;">None</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <span><?php echo htmlspecialchars($tl); ?></span>
                                                <?php if (!empty($emirate)) { ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($emirate); ?></small>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php echo !empty($c['created_at']) ? date('d-m-Y', strtotime($c['created_at'])) : '-'; ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php if ($is_assigned) { ?>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border: 1px solid #cbd5e1; padding: 4px 10px; border-radius: 4px; font-weight: 500;">
                                                            Action <span class="caret"></span>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-right" style="border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: 1px solid #e2e8f0;">
                                                            <?php if (!empty($encoded_lead_id)) { ?>
                                                                <li>
                                                                    <a href="<?php echo base_url('manager/leads/view/' . $encoded_lead_id); ?>">
                                                                        <i class="fa fa-eye" style="color: #64748b; margin-right: 6px;"></i> View Details
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="<?php echo base_url('manager/leads/edit/' . $encoded_lead_id); ?>">
                                                                        <i class="fa fa-pencil" style="color: #0072f0; margin-right: 6px;"></i> Edit Details
                                                                    </a>
                                                                </li>
                                                            <?php } else { ?>
                                                                <li>
                                                                    <a href="<?php echo base_url('manager/kam/onboard?company_id=' . (int)$c['id']); ?>">
                                                                        <i class="fa fa-pencil" style="color: #0072f0; margin-right: 6px;"></i> Manage Company
                                                                    </a>
                                                                </li>
                                                            <?php } ?>
                                                        </ul>
                                                    </div>
                                                <?php } else { ?>
                                                    <?php if (!empty($encoded_lead_id)) { ?>
                                                        <a href="<?php echo base_url('manager/leads/view/' . $encoded_lead_id); ?>" class="btn btn-default btn-xs" style="border: 1px solid #cbd5e1; padding: 4px 10px; border-radius: 4px; font-weight: 500;" title="View Only">
                                                            <i class="fa fa-eye"></i> View
                                                        </a>
                                                    <?php } else { ?>
                                                        <span class="text-muted" style="font-size: 11px;"><i class="fa fa-lock"></i> View Only</span>
                                                    <?php } ?>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <?php if (!empty($searched) && strlen($search_term) >= 3) { ?>
                                <div class="alert alert-info" style="margin: 15px 0;"> No matching companies found for "<?php echo htmlspecialchars($search_term); ?>" </div>
                            <?php } else { ?>
                                <div class="alert alert-info" style="margin: 15px 0;"> Please type at least 3 letters to search for a company. </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#example_kam_clients')) {
        $('#example_kam_clients').DataTable().destroy();
    }
    $('#example_kam_clients').DataTable({
        "pageLength": 25,
        "responsive": true,
        "order": [[0, "asc"]],
        "columnDefs": [
            { "orderable": false, "targets": [8] }
        ]
    });

    if ($.fn.select2) {
        $('#kamCompanySearch').select2({
            placeholder: 'Type 3+ letters to search company...',
            minimumInputLength: 3,
            allowClear: true,
            ajax: {
                url: '<?php echo base_url("manager/system/ajax_search_kam_companies"); ?>',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        term: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.results || []
                    };
                },
                cache: true
            }
        });
    }
});
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
