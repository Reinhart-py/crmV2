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

<?php
$client_ref = !empty($lead['lead_id']) ? $lead['lead_id'] : $lead['id'];
$client_sections = array(
    'Contact' => array(
        'Company Name' => $display_company,
        'Group Name' => $display_group,
        'Owner Name' => $display_owner,
        'Contact Person (POC)' => $display_poc,
        'Phone / Contact' => $display_mobile,
        'Email Address' => $display_email,
        'Location / City' => $display_location,
    ),
    'Account' => array(
        'Primary Assignee / Executive' => $display_assignee,
        'Collaborators' => $display_collaborators,
        'Service Type / Vertical' => !empty($lead['servicecat']) ? $lead['servicecat'] : '-',
    ),
    'Trade License & Compliance' => array(
        'Trade License Number' => $display_tl_no,
        'Emirate' => $display_tl_emirate,
        'Zone' => $display_tl_zone,
        'Licensing Authority' => $display_tl_auth,
        'License Expiry Date' => $display_tl_exp,
        'MOL Employees' => !empty($kam_data['no_of_mol_employee']) ? $kam_data['no_of_mol_employee'] : '-',
        'Corporate Tax (CT) Reg' => !empty($kam_data['ct_reg']) ? $kam_data['ct_reg'] : '-',
        'VAT Quarter' => !empty($kam_data['vat_qtr']) ? $kam_data['vat_qtr'] : '-',
    ),
);
?>
<div class="content-wrapper">
    <div class="cv-page">
        <div class="cv-head">
            <div class="cv-head-main">
                <h1 class="cv-title"><?php echo htmlspecialchars($display_company); ?></h1>
                <div class="cv-sub">
                    <span>Client ID #<?php echo htmlspecialchars($client_ref); ?></span>
                    <span class="cv-dot" aria-hidden="true"></span>
                    <span>Read-only profile</span>
                </div>
            </div>
            <div class="cv-head-stats">
                <div class="cv-stat">
                    <span class="cv-stat-label">Client Status</span>
                    <span class="cv-status"><?php echo htmlspecialchars($display_status); ?></span>
                </div>
                <div class="cv-stat">
                    <span class="cv-stat-label">MRR</span>
                    <span class="cv-stat-value">AED <?php echo number_format((float)$display_mrr, 2); ?></span>
                </div>
            </div>
            <div class="cv-actions">
                <a href="<?php echo base_url('manager/kam/task-followup?client=' . urlencode($display_company)); ?>" class="btn btn-primary btn-sm">Task Follow-Up</a>
                <a href="<?php echo base_url('manager/kam/my-client'); ?>" class="btn btn-default btn-sm">Back to My Clients</a>
            </div>
        </div>

        <div class="cv-card cv-details">
            <?php foreach ($client_sections as $section_title => $fields) { ?>
            <section class="cv-section">
                <h2 class="cv-section-title"><?php echo htmlspecialchars($section_title); ?></h2>
                <dl class="cv-list">
                    <?php foreach ($fields as $label => $value) { ?>
                    <div class="cv-row">
                        <dt><?php echo htmlspecialchars($label); ?></dt>
                        <dd><?php echo htmlspecialchars((string)$value); ?></dd>
                    </div>
                    <?php } ?>
                </dl>
            </section>
            <?php } ?>
        </div>

        <div class="cv-card">
            <div class="cv-card-head">
                <h2 class="cv-section-title" style="margin:0;">Upcoming Scheduled Tasks</h2>
                <a href="<?php echo base_url('manager/kam/task-followup?client=' . urlencode($display_company)); ?>" class="cv-link">View all in Task Follow-Up</a>
            </div>
            <?php if (!empty($upcoming_tasks)) { ?>
            <div class="table-responsive">
                <table class="table table-bordered cv-table">
                    <thead>
                        <tr>
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
                            <td><span class="label label-info"><?php echo !empty($task['status']) ? htmlspecialchars($task['status']) : 'Scheduled'; ?></span></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php } else { ?>
            <div class="cv-empty">No upcoming scheduled tasks found for this client. You can create scheduled tasks from <a href="<?php echo base_url('manager/kam/schedule'); ?>">Create Schedule</a>.</div>
            <?php } ?>
        </div>
    </div>
</div>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
