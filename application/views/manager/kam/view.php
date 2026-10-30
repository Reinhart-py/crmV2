<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<?php
$kam_data = array();
if (!empty($kam['kam_data'])) {
    $decoded = json_decode($kam['kam_data'], true);
    if (is_array($decoded)) {
        $kam_data = $decoded;
    }
}
$display_company = !empty($lead['customer']) ? $lead['customer'] : (!empty($kam['client_name']) ? $kam['client_name'] : '-');
$display_owner = !empty($kam['owner_name']) ? $kam['owner_name'] : (!empty($lead['poc']) ? $lead['poc'] : '-');
$display_group = !empty($kam['group_name']) ? $kam['group_name'] : (!empty($kam_data['group_name']) ? $kam_data['group_name'] : '-');
$display_poc = !empty($lead['poc']) ? $lead['poc'] : (!empty($kam_data['poc']) ? $kam_data['poc'] : '-');
$display_mobile = !empty($lead['contact1']) ? $lead['contact1'] : (!empty($lead['mobile']) ? $lead['mobile'] : (!empty($kam_data['number']) ? $kam_data['number'] : '-'));
$display_email = !empty($lead['email']) ? $lead['email'] : (!empty($kam_data['email']) ? $kam_data['email'] : '-');
$display_location = !empty($lead['location']) ? $lead['location'] : (!empty($kam_data['location']) ? $kam_data['location'] : '-');
$display_assignee = !empty($kam['executive_name']) ? $kam['executive_name'] : (!empty($lead['owner_name']) ? $lead['owner_name'] : '-');
$display_collaborators = !empty($kam['collaborators']) ? $kam['collaborators'] : (!empty($lead['agents']) ? $lead['agents'] : '-');
$display_mrr = !empty($lead['mrr']) ? $lead['mrr'] : '0';
$display_status = !empty($lead['company_status']) ? $lead['company_status'] : (!empty($lead['lead_status']) ? $lead['lead_status'] : 'Active');
$display_tl_no = !empty($lead['tl_number']) ? $lead['tl_number'] : (!empty($kam_data['tl_number']) ? $kam_data['tl_number'] : '-');
$display_tl_emirate = !empty($lead['tl_emirate']) ? $lead['tl_emirate'] : (!empty($kam_data['tl_emirate']) ? $kam_data['tl_emirate'] : '-');
$display_tl_zone = !empty($lead['tl_zone']) ? $lead['tl_zone'] : (!empty($kam_data['tl_zone']) ? $kam_data['tl_zone'] : '-');
$display_tl_auth = !empty($lead['tl_authority']) ? $lead['tl_authority'] : (!empty($kam_data['tl_authority']) ? $kam_data['tl_authority'] : '-');
$display_tl_exp = !empty($lead['tl_expire_date']) && $lead['tl_expire_date'] != '0000-00-00' ? date('d-m-Y', strtotime($lead['tl_expire_date'])) : '-';
?>

<div class="content-wrapper px-4">
    <section class="content-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 style="font-size:22px; font-weight:700; margin:0; color:#0f172a;">
                <?php echo htmlspecialchars($display_company); ?>
            </h1>
            <p style="margin:4px 0 0 0; color:#64748b; font-size:13px;">
                Client ID: #<?php echo !empty($lead['lead_id']) ? htmlspecialchars($lead['lead_id']) : $lead['id']; ?> &nbsp;|&nbsp; Read-Only Profile
            </p>
        </div>
        <div style="display:flex; gap:8px;">
            <a href="<?php echo base_url('manager/kam/task-followup?client=' . urlencode($display_company)); ?>" class="btn btn-default" style="border:1px solid #cbd5e1; font-weight:600; color:#0072f0; background:#ffffff;">
                <i class="fa fa-calendar-check-o"></i> Task Follow-Up
            </a>
            <a href="<?php echo base_url('manager/kam/my-client'); ?>" class="btn btn-default" style="border:1px solid #cbd5e1; font-weight:600; background:#ffffff;">
                <i class="fa fa-arrow-left"></i> Back to My Clients
            </a>
        </div>
    </section>

    <section class="content">
        <div class="box" style="border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,0.03); margin-bottom:20px;">
            <div class="box-header with-border" style="background:#fafbfc; padding:14px 20px; border-bottom:1px solid #e2e8f0;">
                <h3 class="box-title" style="font-size:15px; font-weight:700; color:#1e293b; margin:0;">
                    Client & Account Details
                </h3>
            </div>
            <div class="box-body" style="padding:20px;">
                <div class="row" style="margin-bottom:15px;">
                    <div class="col-md-3 col-sm-6" style="margin-bottom:15px;">
                        <span style="display:block; font-size:11px; font-weight:600; text-transform:uppercase; color:#64748b; letter-spacing:0.5px;">Company Name</span>
                        <span style="font-size:14px; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($display_company); ?></span>
                    </div>
                    <div class="col-md-3 col-sm-6" style="margin-bottom:15px;">
                        <span style="display:block; font-size:11px; font-weight:600; text-transform:uppercase; color:#64748b; letter-spacing:0.5px;">Group Name</span>
                        <span style="font-size:14px; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($display_group); ?></span>
                    </div>
                    <div class="col-md-3 col-sm-6" style="margin-bottom:15px;">
                        <span style="display:block; font-size:11px; font-weight:600; text-transform:uppercase; color:#64748b; letter-spacing:0.5px;">Owner Name</span>
                        <span style="font-size:14px; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($display_owner); ?></span>
                    </div>
                    <div class="col-md-3 col-sm-6" style="margin-bottom:15px;">
                        <span style="display:block; font-size:11px; font-weight:600; text-transform:uppercase; color:#64748b; letter-spacing:0.5px;">Contact Person (POC)</span>
                        <span style="font-size:14px; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($display_poc); ?></span>
                    </div>
                </div>

                <div class="row" style="margin-bottom:15px;">
                    <div class="col-md-3 col-sm-6" style="margin-bottom:15px;">
                        <span style="display:block; font-size:11px; font-weight:600; text-transform:uppercase; color:#64748b; letter-spacing:0.5px;">Phone / Contact</span>
                        <span style="font-size:14px; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($display_mobile); ?></span>
                    </div>
                    <div class="col-md-3 col-sm-6" style="margin-bottom:15px;">
                        <span style="display:block; font-size:11px; font-weight:600; text-transform:uppercase; color:#64748b; letter-spacing:0.5px;">Email Address</span>
                        <span style="font-size:14px; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($display_email); ?></span>
                    </div>
                    <div class="col-md-3 col-sm-6" style="margin-bottom:15px;">
                        <span style="display:block; font-size:11px; font-weight:600; text-transform:uppercase; color:#64748b; letter-spacing:0.5px;">Location / City</span>
                        <span style="font-size:14px; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($display_location); ?></span>
                    </div>
                    <div class="col-md-3 col-sm-6" style="margin-bottom:15px;">
                        <span style="display:block; font-size:11px; font-weight:600; text-transform:uppercase; color:#64748b; letter-spacing:0.5px;">Client Status</span>
                        <span class="label" style="background:#0072f0; color:#ffffff; font-weight:600; padding:3px 8px; border-radius:4px; font-size:12px; display:inline-block; margin-top:2px;"><?php echo htmlspecialchars($display_status); ?></span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 col-sm-6" style="margin-bottom:15px;">
                        <span style="display:block; font-size:11px; font-weight:600; text-transform:uppercase; color:#64748b; letter-spacing:0.5px;">Primary Assignee / Executive</span>
                        <span style="font-size:14px; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($display_assignee); ?></span>
                    </div>
                    <div class="col-md-3 col-sm-6" style="margin-bottom:15px;">
                        <span style="display:block; font-size:11px; font-weight:600; text-transform:uppercase; color:#64748b; letter-spacing:0.5px;">Collaborators</span>
                        <span style="font-size:14px; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($display_collaborators); ?></span>
                    </div>
                    <div class="col-md-3 col-sm-6" style="margin-bottom:15px;">
                        <span style="display:block; font-size:11px; font-weight:600; text-transform:uppercase; color:#64748b; letter-spacing:0.5px;">Service Type / Vertical</span>
                        <span style="font-size:14px; font-weight:600; color:#0f172a;"><?php echo !empty($lead['servicecat']) ? htmlspecialchars($lead['servicecat']) : '-'; ?></span>
                    </div>
                    <div class="col-md-3 col-sm-6" style="margin-bottom:15px;">
                        <span style="display:block; font-size:11px; font-weight:600; text-transform:uppercase; color:#64748b; letter-spacing:0.5px;">Monthly Recurring Revenue (MRR)</span>
                        <span style="font-size:15px; font-weight:700; color:#16a34a;">AED <?php echo number_format((float)$display_mrr, 2); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="box" style="border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,0.03); margin-bottom:20px;">
            <div class="box-header with-border" style="background:#fafbfc; padding:14px 20px; border-bottom:1px solid #e2e8f0;">
                <h3 class="box-title" style="font-size:15px; font-weight:700; color:#1e293b; margin:0;">
                    Trade License & Regulatory Compliance
                </h3>
            </div>
            <div class="box-body" style="padding:20px;">
                <div class="row">
                    <div class="col-md-3 col-sm-6" style="margin-bottom:15px;">
                        <span style="display:block; font-size:11px; font-weight:600; text-transform:uppercase; color:#64748b; letter-spacing:0.5px;">Trade License Number</span>
                        <span style="font-size:14px; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($display_tl_no); ?></span>
                    </div>
                    <div class="col-md-3 col-sm-6" style="margin-bottom:15px;">
                        <span style="display:block; font-size:11px; font-weight:600; text-transform:uppercase; color:#64748b; letter-spacing:0.5px;">Emirate</span>
                        <span style="font-size:14px; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($display_tl_emirate); ?></span>
                    </div>
                    <div class="col-md-3 col-sm-6" style="margin-bottom:15px;">
                        <span style="display:block; font-size:11px; font-weight:600; text-transform:uppercase; color:#64748b; letter-spacing:0.5px;">Zone</span>
                        <span style="font-size:14px; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($display_tl_zone); ?></span>
                    </div>
                    <div class="col-md-3 col-sm-6" style="margin-bottom:15px;">
                        <span style="display:block; font-size:11px; font-weight:600; text-transform:uppercase; color:#64748b; letter-spacing:0.5px;">Licensing Authority</span>
                        <span style="font-size:14px; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($display_tl_auth); ?></span>
                    </div>
                    <div class="col-md-3 col-sm-6" style="margin-bottom:15px;">
                        <span style="display:block; font-size:11px; font-weight:600; text-transform:uppercase; color:#64748b; letter-spacing:0.5px;">License Expiry Date</span>
                        <span style="font-size:14px; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($display_tl_exp); ?></span>
                    </div>
                    <div class="col-md-3 col-sm-6" style="margin-bottom:15px;">
                        <span style="display:block; font-size:11px; font-weight:600; text-transform:uppercase; color:#64748b; letter-spacing:0.5px;">MOL Employees</span>
                        <span style="font-size:14px; font-weight:600; color:#0f172a;"><?php echo !empty($kam_data['no_of_mol_employee']) ? htmlspecialchars($kam_data['no_of_mol_employee']) : '-'; ?></span>
                    </div>
                    <div class="col-md-3 col-sm-6" style="margin-bottom:15px;">
                        <span style="display:block; font-size:11px; font-weight:600; text-transform:uppercase; color:#64748b; letter-spacing:0.5px;">Corporate Tax (CT) Reg</span>
                        <span style="font-size:14px; font-weight:600; color:#0f172a;"><?php echo !empty($kam_data['ct_reg']) ? htmlspecialchars($kam_data['ct_reg']) : '-'; ?></span>
                    </div>
                    <div class="col-md-3 col-sm-6" style="margin-bottom:15px;">
                        <span style="display:block; font-size:11px; font-weight:600; text-transform:uppercase; color:#64748b; letter-spacing:0.5px;">VAT Quarter</span>
                        <span style="font-size:14px; font-weight:600; color:#0f172a;"><?php echo !empty($kam_data['vat_qtr']) ? htmlspecialchars($kam_data['vat_qtr']) : '-'; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="box" style="border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
            <div class="box-header with-border" style="background:#fafbfc; padding:14px 20px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
                <h3 class="box-title" style="font-size:15px; font-weight:700; color:#1e293b; margin:0;">
                    Upcoming Scheduled Tasks
                </h3>
                <a href="<?php echo base_url('manager/kam/task-followup?client=' . urlencode($display_company)); ?>" class="btn btn-sm btn-primary" style="background:#0072f0; border-color:#0072f0; font-weight:600; border-radius:4px;">
                    <i class="fa fa-calendar-check-o"></i> View All in Task Follow-Up
                </a>
            </div>
            <div class="box-body" style="padding:20px;">
                <?php if (!empty($upcoming_tasks)) { ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" style="margin:0; width:100%;">
                            <thead>
                                <tr style="background:#f8fafc; color:#475569; font-size:12px; font-weight:700; text-transform:uppercase;">
                                    <th>Task Title</th>
                                    <th>Service Type</th>
                                    <th>Frequency</th>
                                    <th>Scheduled Date</th>
                                    <th>Scheduled Time</th>
                                    <th>Assigned Staff</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($upcoming_tasks as $task) { ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($task['title']); ?></strong></td>
                                        <td><?php echo !empty($task['service_type']) ? htmlspecialchars($task['service_type']) : '-'; ?></td>
                                        <td><?php echo !empty($task['frequency']) ? htmlspecialchars($task['frequency']) : 'Once'; ?></td>
                                        <td><?php echo !empty($task['start_date']) ? date('d-m-Y', strtotime($task['start_date'])) : '-'; ?></td>
                                        <td><?php echo !empty($task['start_time']) ? htmlspecialchars($task['start_time']) : '-'; ?></td>
                                        <td><?php echo !empty($task['assigned_name']) ? htmlspecialchars($task['assigned_name']) : '-'; ?></td>
                                        <td>
                                            <span class="label label-info" style="font-size:11px; font-weight:600; padding:2px 7px;">
                                                <?php echo !empty($task['status']) ? htmlspecialchars($task['status']) : 'Scheduled'; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <div class="alert alert-info" style="margin:0; border-radius:6px;">
                        No upcoming scheduled tasks found for this client. You can create scheduled tasks from <a href="<?php echo base_url('manager/kam/schedule'); ?>" style="font-weight:700; text-decoration:underline; color:#ffffff;">Create Schedule</a>.
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
