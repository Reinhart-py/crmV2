<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<style>
.pivot-table-wrap {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    overflow-x: auto;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}
.pivot-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12.5px;
    color: #1e293b;
    margin: 0;
}
.pivot-table th {
    background: #607d9b;
    color: #ffffff;
    font-weight: 700;
    font-size: 12.5px;
    padding: 10px 14px;
    border: 1px solid #78909c;
    letter-spacing: 0.3px;
    white-space: nowrap;
}
.pivot-table th:first-child,
.pivot-table th:nth-child(2) {
    text-align: left;
}
.pivot-table th:not(:first-child):not(:nth-child(2)) {
    text-align: right;
}
.pivot-table td {
    padding: 7px 14px;
    border: 1px solid #e2e8f0;
    vertical-align: middle;
}
.pivot-group-row {
    background: #f8fafc;
    font-weight: 700;
    color: #0f172a;
    cursor: pointer;
    user-select: none;
}
.pivot-group-row:hover {
    background: #f1f5f9;
}
.pivot-toggle-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 15px;
    height: 15px;
    border: 1px solid #64748b;
    background: #ffffff;
    color: #0f172a;
    border-radius: 2px;
    font-size: 11px;
    font-weight: 800;
    line-height: 1;
    margin-right: 8px;
    vertical-align: middle;
}
.pivot-item-row td {
    background: #ffffff;
}
.pivot-item-row:hover td {
    background: #fbfcfe;
}
.pivot-subtotal-row td {
    background: #e2e8f0;
    font-weight: 700;
    color: #0f172a;
    border-top: 1px solid #cbd5e1;
    border-bottom: 1px solid #cbd5e1;
}
.pivot-grand-total-row td {
    background: #f1f5f9;
    font-weight: 800;
    font-size: 13.5px;
    color: #000000;
    border-top: 2px solid #000000;
    border-bottom: 3px double #000000;
}
.period-preset-btn {
    border: 1px solid #d1d5db;
    background: #ffffff;
    color: #374151;
    font-weight: 600;
    padding: 4px 11px;
    border-radius: 14px;
    font-size: 11.5px;
    cursor: pointer;
    transition: all 0.15s ease;
}
.period-preset-btn:hover {
    background: #f3f4f6;
    color: #111827;
}
.period-preset-btn.active-preset {
    background: #111827;
    color: #ffffff;
    border-color: #111827;
}
.view-mode-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 16px;
    font-size: 13px;
    font-weight: 600;
    color: #4b5563;
    background: #ffffff;
    border: 1px solid #d1d5db;
    cursor: pointer;
    transition: all 0.15s ease;
}
.view-mode-pill:first-child {
    border-radius: 5px 0 0 5px;
    border-right: none;
}
.view-mode-pill:last-child {
    border-radius: 0 5px 5px 0;
}
.view-mode-pill.active {
    background: #2563eb;
    color: #ffffff;
    border-color: #2563eb;
}
@media print {
    .main-header,
    .main-sidebar,
    .content-header .btn,
    .content-header .btn-group,
    .filter-box-wrap,
    .view-mode-wrap,
    .main-footer,
    .box-header {
        display: none !important;
    }
    .content-wrapper {
        margin: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
    }
    .pivot-table-wrap, .box {
        border: none !important;
        box-shadow: none !important;
    }
    .pivot-table th {
        background: #607d9b !important;
        color: #ffffff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .pivot-subtotal-row td, .pivot-grand-total-row td {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>

<div class="content-wrapper px-4">
    <section class="content-header" style="margin-bottom: 16px; padding-top: 14px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: nowrap; gap: 14px;">
            <div style="min-width: 0; flex: 1;">
                <h1 style="font-weight: 700; color: #111827; margin: 0; font-size: 22px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <?php echo htmlspecialchars($report_data['title']); ?>
                </h1>
            </div>
            <div style="display: flex; align-items: center; gap: 10px; flex-shrink: 0;">
                <a href="<?php echo base_url(); ?>manager/download-report" class="btn btn-default" style="font-weight: 600; border-radius: 4px; padding: 7px 14px; font-size: 13px; color: #111827; border-color: #d1d5db; background: #ffffff;">
                    <i class="fa fa-arrow-left" style="margin-right: 5px;"></i> Back to Download Report
                </a>
                <div class="btn-group">
                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-weight: 600; border-radius: 4px; padding: 7px 16px; font-size: 13px; background: #10b981; border-color: #059669; color: #ffffff;">
                        <i class="fa fa-download" style="margin-right: 6px;"></i> Choose What to Download <span class="caret" style="margin-left: 6px;"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" style="border-radius: 6px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); border: 1px solid #e5e7eb; padding: 6px 0; min-width: 235px;">
                        <?php if (!empty($report_data['pivot_data'])) { ?>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/download-report/<?php echo $dept; ?>/<?php echo $report; ?>?<?php echo http_build_query(array_merge($_GET, array('export' => 'pivot'))); ?>" style="padding: 10px 16px; font-weight: 600; color: #111827; font-size: 13px;">
                                <i class="fa fa-table" style="color: #2563eb; margin-right: 8px;"></i> Download Pivot Sheet (CSV)
                            </a>
                        </li>
                        <?php } ?>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/download-report/<?php echo $dept; ?>/<?php echo $report; ?>?<?php echo http_build_query(array_merge($_GET, array('export' => 'csv'))); ?>" style="padding: 10px 16px; font-weight: 600; color: #111827; font-size: 13px;">
                                <i class="fa fa-list-alt" style="color: #059669; margin-right: 8px;"></i> Download Detailed Records (CSV)
                            </a>
                        </li>
                        <li role="separator" class="divider" style="margin: 4px 0;"></li>
                        <li>
                            <a href="javascript:void(0);" onclick="window.print();" style="padding: 10px 16px; font-weight: 600; color: #111827; font-size: 13px;">
                                <i class="fa fa-print" style="color: #7c3aed; margin-right: 8px;"></i> Print / Save as PDF
                            </a>
                        </li>
                    </ul>
                </div>
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

        <div class="box filter-box-wrap" style="margin-bottom: 16px; border-radius: 6px; border: 1px solid var(--crm-border); background: #ffffff;">
            <div class="box-body" style="padding: 14px;">
                <form id="reportFilterForm" class="form-inline" action="<?php echo base_url(); ?>manager/download-report/<?php echo $dept; ?>/<?php echo $report; ?>" method="get" style="display: flex; flex-direction: column; gap: 12px;">

                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                        <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                            <span style="font-size: 12px; font-weight: 700; color: #64748b; margin-right: 4px; text-transform: uppercase; letter-spacing: 0.4px;">Period:</span>
                            <button type="button" class="period-preset-btn <?php echo (empty($filters['period']) || $filters['period'] == 'all') ? 'active-preset' : ''; ?>" onclick="setFilterPeriod('all');">All Time</button>
                            <button type="button" class="period-preset-btn <?php echo (isset($filters['period']) && ($filters['period'] == 'today' || $filters['period'] == 'daily')) ? 'active-preset' : ''; ?>" onclick="setFilterPeriod('today');">Today</button>
                            <button type="button" class="period-preset-btn <?php echo (isset($filters['period']) && ($filters['period'] == '7days' || $filters['period'] == 'weekly')) ? 'active-preset' : ''; ?>" onclick="setFilterPeriod('7days');">7 Days</button>
                            <button type="button" class="period-preset-btn <?php echo (isset($filters['period']) && ($filters['period'] == 'month' || $filters['period'] == 'monthly')) ? 'active-preset' : ''; ?>" onclick="setFilterPeriod('month');">This Month</button>
                            <button type="button" class="period-preset-btn <?php echo (isset($filters['period']) && ($filters['period'] == 'year' || $filters['period'] == 'yearly')) ? 'active-preset' : ''; ?>" onclick="setFilterPeriod('year');">This Year</button>
                            <button type="button" class="period-preset-btn <?php echo (isset($filters['period']) && $filters['period'] == 'custom') ? 'active-preset' : ''; ?>" onclick="toggleCustomDates('custom');">Custom</button>
                            <input type="hidden" name="period" id="adminReportPeriod" value="<?php echo isset($filters['period']) ? htmlspecialchars($filters['period']) : ''; ?>" />
                        </div>

                        <div id="adminDateRangeWrap" style="<?php echo (isset($filters['period']) && $filters['period'] == 'custom') ? 'display:inline-flex;' : 'display:none;'; ?>">
                            <div class="input-daterange input-group" style="width: 240px;">
                                <input type="text" class="input-sm form-control datepicker" name="d1" placeholder="From" value="<?php echo isset($filters['d1']) ? htmlspecialchars($filters['d1']) : ''; ?>" autocomplete="off" style="border-radius: 4px 0 0 4px; font-size: 12px;" />
                                <span class="input-group-addon" style="padding: 4px 8px; font-size: 11px;">to</span>
                                <input type="text" class="input-sm form-control datepicker" name="d2" placeholder="To" value="<?php echo isset($filters['d2']) ? htmlspecialchars($filters['d2']) : ''; ?>" autocomplete="off" style="border-radius: 0 4px 4px 0; font-size: 12px;" />
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 10px;">
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
                    </div>

                </form>
            </div>
        </div>

        <div class="view-mode-wrap" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; flex-wrap: wrap; gap: 10px;">
            <div style="display: inline-flex;">
                <button type="button" id="btnModePivot" class="view-mode-pill active" onclick="switchReportView('pivot');">
                    <i class="fa fa-table" style="color: inherit;"></i> Pivot Sheet View
                </button>
                <button type="button" id="btnModeDetails" class="view-mode-pill" onclick="switchReportView('details');">
                    <i class="fa fa-list" style="color: inherit;"></i> Detailed Records (<?php echo number_format(count($report_data['rows'])); ?>)
                </button>
            </div>
            <div>
                <span style="font-size: 12px; color: #64748b; font-weight: 600;">
                    <?php if (!empty($report_data['rows'])) { ?>
                    Showing <?php echo number_format(count($report_data['rows'])); ?> records
                    <?php } else { ?>
                    0 records
                    <?php } ?>
                </span>
            </div>
        </div>

        <div id="pivotViewContainer">
            <?php if (!empty($report_data['pivot_data']) && !empty($report_data['pivot_data']['groups'])) { ?>
            <div class="pivot-table-wrap">
                <table class="pivot-table">
                    <thead>
                        <tr>
                            <?php foreach ($report_data['pivot_data']['headers'] as $hdr) { ?>
                            <th><?php echo htmlspecialchars($hdr); ?></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $col_count = count($report_data['pivot_data']['headers']);
                        $grp_idx = 0;
                        foreach ($report_data['pivot_data']['groups'] as $grp) { 
                            $grp_idx++;
                            $cat_name = !empty($grp['category']) ? $grp['category'] : 'Category';
                        ?>
                        <tr class="pivot-group-row" onclick="togglePivotGroup('grp_<?php echo $grp_idx; ?>', this);">
                            <td colspan="<?php echo $col_count; ?>" style="padding-left: 12px;">
                                <span class="pivot-toggle-btn">-</span>
                                <strong><?php echo htmlspecialchars($cat_name); ?></strong>
                            </td>
                        </tr>
                        <?php foreach ($grp['rows'] as $r_item) { ?>
                        <tr class="pivot-item-row grp_<?php echo $grp_idx; ?>">
                            <?php 
                            $cell_idx = 0;
                            foreach ($r_item as $cell) { 
                                $align = ($cell_idx < 2) ? 'left' : 'right';
                                $indent = ($cell_idx == 1) ? 'padding-left: 28px;' : '';
                            ?>
                            <td style="text-align: <?php echo $align; ?>; <?php echo $indent; ?>">
                                <?php echo htmlspecialchars($cell); ?>
                            </td>
                            <?php 
                                $cell_idx++;
                            } ?>
                        </tr>
                        <?php } ?>
                        <?php if (!empty($grp['subtotal'])) { ?>
                        <tr class="pivot-subtotal-row grp_<?php echo $grp_idx; ?>">
                            <?php 
                            $cell_idx = 0;
                            foreach ($grp['subtotal'] as $cell) { 
                                $align = ($cell_idx < 2) ? 'left' : 'right';
                            ?>
                            <td style="text-align: <?php echo $align; ?>;">
                                <?php echo htmlspecialchars($cell); ?>
                            </td>
                            <?php 
                                $cell_idx++;
                            } ?>
                        </tr>
                        <?php } ?>
                        <?php } ?>
                    </tbody>
                    <?php if (!empty($report_data['pivot_data']['grand_total'])) { ?>
                    <tfoot>
                        <tr class="pivot-grand-total-row">
                            <?php 
                            $cell_idx = 0;
                            foreach ($report_data['pivot_data']['grand_total'] as $cell) { 
                                $align = ($cell_idx < 2) ? 'left' : 'right';
                            ?>
                            <td style="text-align: <?php echo $align; ?>;">
                                <?php echo htmlspecialchars($cell); ?>
                            </td>
                            <?php 
                                $cell_idx++;
                            } ?>
                        </tr>
                    </tfoot>
                    <?php } ?>
                </table>
            </div>
            <?php } else { ?>
            <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 40px 20px; text-align: center; color: #64748b;">
                <i class="fa fa-table" style="font-size: 28px; color: #94a3b8; margin-bottom: 10px;"></i>
                <h4 style="font-weight: 700; color: #111827; margin: 0 0 4px 0;">No Pivot Data Available</h4>
                <p style="font-size: 13px; margin: 0;">Try selecting a different date range or period.</p>
            </div>
            <?php } ?>
        </div>

        <div id="detailsViewContainer" style="display: none;">
            <div class="box" style="border-radius: 6px; border: 1px solid var(--crm-border); background: #ffffff;">
                <div class="box-header with-border" style="padding: 12px 16px;">
                    <h3 class="box-title" style="font-size: 14px; font-weight: 700; color: var(--crm-text); margin: 0;">
                        Detailed Records
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
        </div>
    </section>
</div>

<script>
function toggleCustomDates(val) {
    var elem = document.getElementById('adminDateRangeWrap');
    var pInput = document.getElementById('adminReportPeriod');
    if (pInput) {
        pInput.value = val;
    }
    if (elem) {
        elem.style.display = (val === 'custom') ? 'inline-flex' : 'none';
    }
    var btns = document.querySelectorAll('.period-preset-btn');
    btns.forEach(function(b) {
        b.classList.remove('active-preset');
    });
    event.target.classList.add('active-preset');
}

function setFilterPeriod(period) {
    var pInput = document.getElementById('adminReportPeriod');
    var form = document.getElementById('reportFilterForm');
    if (pInput) {
        pInput.value = period;
    }
    if (period !== 'custom') {
        var d1Input = document.querySelector('input[name="d1"]');
        var d2Input = document.querySelector('input[name="d2"]');
        if (d1Input && period === 'all') d1Input.value = '';
        if (d2Input && period === 'all') d2Input.value = '';
        if (form) {
            form.submit();
        }
    }
}

function switchReportView(mode) {
    var pivotWrap = document.getElementById('pivotViewContainer');
    var detailsWrap = document.getElementById('detailsViewContainer');
    var btnPivot = document.getElementById('btnModePivot');
    var btnDetails = document.getElementById('btnModeDetails');

    if (mode === 'pivot') {
        if (pivotWrap) pivotWrap.style.display = 'block';
        if (detailsWrap) detailsWrap.style.display = 'none';
        if (btnPivot) btnPivot.classList.add('active');
        if (btnDetails) btnDetails.classList.remove('active');
    } else {
        if (pivotWrap) pivotWrap.style.display = 'none';
        if (detailsWrap) detailsWrap.style.display = 'block';
        if (btnPivot) btnPivot.classList.remove('active');
        if (btnDetails) btnDetails.classList.add('active');
    }
}

function togglePivotGroup(cls, rowElem) {
    var rows = document.querySelectorAll('.' + cls);
    var toggleBtn = rowElem.querySelector('.pivot-toggle-btn');
    var isHidden = false;
    rows.forEach(function(r) {
        if (r.style.display === 'none') {
            r.style.display = '';
        } else {
            r.style.display = 'none';
            isHidden = true;
        }
    });
    if (toggleBtn) {
        toggleBtn.textContent = isHidden ? '+' : '-';
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
