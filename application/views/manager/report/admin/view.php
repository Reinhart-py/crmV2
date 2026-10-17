<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
    <section class="content-header" style="margin-bottom: 16px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
            <div>
                <h1 style="font-weight: 700; color: var(--crm-text); margin: 0 0 5px 0;">
                    <?php echo htmlspecialchars($report_data['title']); ?>
                </h1>
                <ol class="breadcrumb" style="background: transparent; padding: 0; margin: 0;">
                    <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url(); ?>manager/download-report">Download Report</a></li>
                    <li><a href="<?php echo base_url(); ?>manager/download-report/<?php echo $dept; ?>"><?php echo htmlspecialchars($report_data['department_title']); ?></a></li>
                    <li class="active"><?php echo htmlspecialchars($report_data['title']); ?></li>
                </ol>
            </div>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <a href="<?php echo base_url(); ?>manager/download-report" class="btn btn-default" style="font-weight: 600; border-radius: 4px; padding: 6px 14px; color: var(--crm-text);">
                    <i class="fa fa-arrow-left" style="margin-right: 5px;"></i> Back to Download Report
                </a>
                <a href="<?php echo base_url(); ?>manager/download-report/<?php echo $dept; ?>/<?php echo $report; ?>?<?php echo http_build_query(array_merge($_GET, array('export' => 'csv'))); ?>" class="btn btn-success" style="font-weight: 600; border-radius: 4px; padding: 6px 14px;">
                    <i class="fa fa-file-excel-o" style="margin-right: 5px;"></i> Export CSV
                </a>
            </div>
        </div>
    </section>

    <section class="content" style="padding-top: 0;">
        <?php if (!empty($report_data['summary'])) { ?>
            <?php if ($report == 'database') { ?>
            <div style="background: #ffffff; border: 1px solid var(--crm-border); border-top: 3px solid #d4f938; border-radius: 6px; padding: 10px 18px; margin-bottom: 16px; display: flex; flex-direction: row; align-items: center; justify-content: flex-start; gap: 36px; font-size: 13px; white-space: nowrap; overflow-x: auto;">
                <?php foreach ($report_data['summary'] as $sum_key => $sum_val) { 
                    $label = is_array($sum_val) ? (isset($sum_val['label']) ? $sum_val['label'] : (isset($sum_val['name']) ? $sum_val['name'] : '')) : $sum_key;
                    $value = is_array($sum_val) ? (isset($sum_val['value']) ? $sum_val['value'] : '') : $sum_val;
                ?>
                <div style="display: inline-flex; align-items: center; gap: 8px;">
                    <span style="color: var(--crm-text-muted); font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.4px;"><?php echo htmlspecialchars($label); ?>:</span>
                    <span style="color: #000000; font-weight: 700; font-size: 14px;"><?php echo htmlspecialchars($value); ?></span>
                </div>
                <?php } ?>
            </div>
            <?php } else { ?>
            <div class="row" style="margin-bottom: 16px;">
                <?php 
                $count_summary = count($report_data['summary']);
                $col_size = ($count_summary <= 4) ? 12 / max(1, $count_summary) : 3;
                foreach ($report_data['summary'] as $sum_key => $sum_val) { 
                    $label = is_array($sum_val) ? (isset($sum_val['label']) ? $sum_val['label'] : (isset($sum_val['name']) ? $sum_val['name'] : '')) : $sum_key;
                    $value = is_array($sum_val) ? (isset($sum_val['value']) ? $sum_val['value'] : '') : $sum_val;
                ?>
                <div class="col-lg-<?php echo $col_size; ?> col-md-6 col-sm-6 col-xs-12" style="margin-bottom: 10px;">
                    <div style="background: #ffffff; border: 1px solid var(--crm-border); border-top: 3px solid #d4f938; border-radius: 6px; padding: 12px 14px;">
                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--crm-text-muted); font-weight: 600; margin-bottom: 4px;">
                            <?php echo htmlspecialchars($label); ?>
                        </div>
                        <div style="font-size: 17px; font-weight: 700; color: #000000;">
                            <?php echo htmlspecialchars($value); ?>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
            <?php } ?>
        <?php } ?>

        <div class="box" style="margin-bottom: 16px; border-radius: 6px; border: 1px solid var(--crm-border); background: #ffffff;">
            <div class="box-body" style="padding: 14px;">
                <form class="form-inline" action="<?php echo base_url(); ?>manager/download-report/<?php echo $dept; ?>/<?php echo $report; ?>" method="get" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">

                    <div class="form-group" style="margin: 0;">
                        <select class="form-control input-sm" name="period" id="adminReportPeriod" onchange="toggleCustomDates(this.value);" style="border-radius: 4px; font-size: 12px;">
                            <option value="">Period: All Time</option>
                            <option value="daily" <?php if(isset($filters['period']) && $filters['period']=='daily') echo 'selected'; ?>>Today</option>
                            <option value="weekly" <?php if(isset($filters['period']) && $filters['period']=='weekly') echo 'selected'; ?>>Last 7 Days</option>
                            <option value="monthly" <?php if(isset($filters['period']) && $filters['period']=='monthly') echo 'selected'; ?>>This Month</option>
                            <option value="custom" <?php if(isset($filters['period']) && $filters['period']=='custom') echo 'selected'; ?>>Custom Dates</option>
                        </select>
                    </div>

                    <div class="form-group" id="adminDateRangeWrap" style="margin: 0; <?php echo (isset($filters['period']) && $filters['period']=='custom') ? 'display:inline-flex;' : 'display:none;'; ?>">
                        <div class="input-daterange input-group" style="width: 230px;">
                            <input type="text" class="input-sm form-control datepicker" name="d1" placeholder="From" value="<?php echo isset($filters['d1']) ? htmlspecialchars($filters['d1']) : ''; ?>" autocomplete="off" style="border-radius: 4px 0 0 4px; font-size: 12px;" />
                            <span class="input-group-addon" style="padding: 4px 8px; font-size: 11px;">to</span>
                            <input type="text" class="input-sm form-control datepicker" name="d2" placeholder="To" value="<?php echo isset($filters['d2']) ? htmlspecialchars($filters['d2']) : ''; ?>" autocomplete="off" style="border-radius: 0 4px 4px 0; font-size: 12px;" />
                        </div>
                    </div>

                    <?php 
                    $is_perf = ($report == 'performance');
                    $selected_emps = array();
                    if (!empty($filters['employee_ids'])) {
                        $selected_emps = is_array($filters['employee_ids']) ? $filters['employee_ids'] : array($filters['employee_ids']);
                    } elseif (!empty($filters['employee_id'])) {
                        $selected_emps = is_array($filters['employee_id']) ? $filters['employee_id'] : array($filters['employee_id']);
                    }
                    ?>
                    <?php if (isset($is_admin_or_support) && $is_admin_or_support) { ?>
                        <?php if ($is_perf) { ?>
                        <div class="form-group" style="margin: 0; min-width: 220px;">
                            <select class="form-control input-sm select2" name="employee_ids[]" multiple="multiple" data-placeholder="Filter Employees (Multiple)" style="width: 100%; border-radius: 4px; font-size: 12px;">
                                <?php foreach ($users as $u) { ?>
                                <option value="<?php echo $u['id']; ?>" <?php if(in_array($u['id'], $selected_emps)) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($u['name']); ?> (<?php echo htmlspecialchars($u['department']); ?>)
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                        <?php } else { ?>
                        <div class="form-group" style="margin: 0;">
                            <select class="form-control input-sm" name="employee_id" style="border-radius: 4px; font-size: 12px; max-width: 180px;">
                                <option value="">All Employees / Users</option>
                                <?php foreach ($users as $u) { ?>
                                <option value="<?php echo $u['id']; ?>" <?php if(isset($filters['employee_id']) && $filters['employee_id'] == $u['id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($u['name']); ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="form-group" style="margin: 0;">
                            <span style="display: inline-flex; align-items: center; gap: 5px; background: #f1f5f9; color: #111827; border: 1px solid #cbd5e1; border-radius: 4px; padding: 5px 10px; font-size: 12px; font-weight: 600;">
                                <i class="fa fa-user"></i> Staff: <?php echo htmlspecialchars(isset($current_user_name) && !empty($current_user_name) ? $current_user_name : 'Me'); ?>
                            </span>
                            <input type="hidden" name="employee_id" value="<?php echo isset($manager_id) ? $manager_id : ''; ?>">
                        </div>
                    <?php } ?>

                    <?php if ($report == 'trade-license') { ?>
                    <div class="form-group" style="margin: 0;">
                        <select class="form-control input-sm" name="expiry" style="border-radius: 4px; font-size: 12px;">
                            <option value="">All Expiry Statuses</option>
                            <option value="expired" <?php if(isset($filters['expiry']) && $filters['expiry']=='expired') echo 'selected'; ?>>Expired</option>
                            <option value="30days" <?php if(isset($filters['expiry']) && $filters['expiry']=='30days') echo 'selected'; ?>>Expiring in 30 Days</option>
                            <option value="60days" <?php if(isset($filters['expiry']) && $filters['expiry']=='60days') echo 'selected'; ?>>Expiring in 60 Days</option>
                            <option value="valid" <?php if(isset($filters['expiry']) && $filters['expiry']=='valid') echo 'selected'; ?>>Valid &amp; Active</option>
                        </select>
                    </div>
                    <?php } ?>

                    <?php if ($dept == 'support' || $report == 'lead' || $report == 'service-delivery' || $report == 'billing') { ?>
                    <div class="form-group" style="margin: 0;">
                        <select class="form-control input-sm" name="status" style="border-radius: 4px; font-size: 12px;">
                            <option value="">All Statuses</option>
                            <?php if ($dept == 'support') { ?>
                            <option value="Open" <?php if(isset($filters['status']) && $filters['status']=='Open') echo 'selected'; ?>>Open</option>
                            <option value="In Progress" <?php if(isset($filters['status']) && $filters['status']=='In Progress') echo 'selected'; ?>>In Progress</option>
                            <option value="Resolved" <?php if(isset($filters['status']) && $filters['status']=='Resolved') echo 'selected'; ?>>Resolved</option>
                            <option value="Closed" <?php if(isset($filters['status']) && $filters['status']=='Closed') echo 'selected'; ?>>Closed</option>
                            <?php } elseif ($report == 'billing') { ?>
                            <option value="Paid" <?php if(isset($filters['status']) && $filters['status']=='Paid') echo 'selected'; ?>>Paid</option>
                            <option value="Unpaid" <?php if(isset($filters['status']) && $filters['status']=='Unpaid') echo 'selected'; ?>>Unpaid</option>
                            <option value="Overdue" <?php if(isset($filters['status']) && $filters['status']=='Overdue') echo 'selected'; ?>>Overdue</option>
                            <option value="Partially Paid" <?php if(isset($filters['status']) && $filters['status']=='Partially Paid') echo 'selected'; ?>>Partially Paid</option>
                            <?php } elseif (!empty($statuses)) { ?>
                            <?php foreach ($statuses as $st) { ?>
                            <option value="<?php echo $st['id']; ?>" <?php if(isset($filters['status']) && $filters['status']==$st['id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($st['name']); ?>
                            </option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                    <?php } ?>

                    <div class="form-group" style="margin: 0; flex-grow: 1; min-width: 160px;">
                        <input type="text" class="form-control input-sm" name="search" placeholder="Search keywords..." value="<?php echo isset($filters['search']) ? htmlspecialchars($filters['search']) : ''; ?>" style="width: 100%; border-radius: 4px; font-size: 12px;" />
                    </div>

                    <div class="form-group" style="margin: 0; display: flex; gap: 6px;">
                        <button type="submit" class="btn btn-sm btn-primary" style="background: #09090b; border-color: #09090b; color: #ffffff; font-weight: 600; border-radius: 4px; padding: 5px 14px;">
                            <i class="fa fa-filter"></i> Apply Filters
                        </button>
                        <a href="<?php echo base_url(); ?>manager/download-report/<?php echo $dept; ?>/<?php echo $report; ?>" class="btn btn-sm btn-default" style="font-weight: 600; border-radius: 4px; padding: 5px 12px; color: var(--crm-text);">
                            Reset
                        </a>
                    </div>

                </form>
            </div>
        </div>

        <div class="box" style="border-radius: 6px; border: 1px solid var(--crm-border); background: #ffffff;">
            <div class="box-header with-border" style="padding: 12px 16px;">
                <h3 class="box-title" style="font-size: 14px; font-weight: 700; color: var(--crm-text); margin: 0;">
                    Report Records
                </h3>
            </div>

            <div class="box-body no-padding">
                <?php if (!empty($report_data['rows'])) { ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" style="margin-bottom: 0; font-size: 12.5px;">
                        <thead style="background: var(--crm-bg); color: var(--crm-text);">
                            <tr>
                                <?php foreach ($report_data['headers'] as $hdr) { ?>
                                <th style="white-space: nowrap; font-weight: 700; padding: 10px 12px; border-bottom: 2px solid var(--crm-border); text-align: left;">
                                    <?php echo htmlspecialchars($hdr); ?>
                                </th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report_data['rows'] as $row) { ?>
                            <tr>
                                <?php foreach ($report_data['fields'] as $fld) { 
                                    $val = isset($row[$fld]) ? $row[$fld] : '';
                                ?>
                                <td style="padding: 9px 12px; vertical-align: middle; text-align: left;">
                                    <?php echo $val; ?>
                                </td>
                                <?php } ?>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php } else { ?>
                <div style="padding: 30px 20px; text-align: center; color: var(--crm-text-muted); font-size: 13px;">
                    No records available for the selected filters.
                </div>
                <?php } ?>
            </div>
        </div>
    </section>
</div>

<script>
function toggleCustomDates(val) {
    var elem = document.getElementById('adminDateRangeWrap');
    if (elem) {
        if (val === 'custom') {
            elem.style.display = 'inline-flex';
        } else {
            elem.style.display = 'none';
        }
    }
}
$(document).ready(function() {
    if ($.fn.datepicker) {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
    }
    if ($.fn.select2) {
        $('.select2').select2({
            width: '100%'
        });
    }
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
