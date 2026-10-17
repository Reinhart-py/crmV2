<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<style>
.pivot-table-wrap {
    background: #ffffff;
    border: 1px solid var(--crm-border);
    border-radius: 6px;
    overflow-x: auto;
    margin-bottom: 20px;
}
.pivot-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12.5px;
}
.pivot-table th {
    background: #607d9b;
    color: #ffffff;
    font-weight: 700;
    padding: 10px 14px;
    text-align: right;
    border: 1px solid #4a637d;
    white-space: nowrap;
}
.pivot-table th:first-child,
.pivot-table th:nth-child(2) {
    text-align: left;
}
.pivot-table td {
    padding: 8px 14px;
    border: 1px solid #e2e8f0;
    color: #1e293b;
    white-space: nowrap;
}
.pivot-group-row {
    background: #f1f5f9;
    cursor: pointer;
    font-weight: 700;
}
.pivot-group-row td {
    border-top: 2px solid #cbd5e1;
    border-bottom: 1px solid #cbd5e1;
    color: #0f172a;
    font-size: 13px;
    padding: 9px 14px;
}
.pivot-group-row:hover {
    background: #e2e8f0;
}
.pivot-item-row:hover {
    background: #f8fafc;
}
.pivot-subtotal-row td {
    background: #f8fafc;
    font-weight: 700;
    border-top: 1px solid #cbd5e1;
    border-bottom: 2px solid #94a3b8;
    color: #0f172a;
}
.pivot-grand-total-row td {
    background: #e2e8f0;
    font-weight: 800;
    color: #0f172a;
    border-top: 2px solid #64748b;
    border-bottom: 2px solid #64748b;
    font-size: 13px;
}
.pivot-toggle-btn {
    display: inline-block;
    width: 18px;
    height: 18px;
    line-height: 16px;
    text-align: center;
    background: #0f172a;
    color: #ffffff;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 800;
    margin-right: 8px;
    user-select: none;
}
<?php if (!isset($is_admin_or_support) || !$is_admin_or_support) { ?>
.protected-crm-data, .protected-crm-data td, .protected-crm-data th {
    -webkit-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
    user-select: none !important;
}
<?php } ?>
@media print {
    .main-header,
    .main-sidebar,
    .content-header .btn,
    .content-header .btn-group,
    .filter-box-wrap,
    .main-footer {
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
    <section class="content-header" style="margin-bottom: 16px; padding-top: 14px; position: relative;">
        <div style="display: flex; align-items: center; justify-content: space-between; position: relative; min-height: 38px;">
            <div style="z-index: 2;">
                <a href="<?php echo base_url(); ?>manager/download-report" class="btn btn-default" style="font-weight: 600; border-radius: 4px; padding: 6px 14px; font-size: 13px; color: var(--crm-text); border-color: #d1d5db; background: #ffffff;">
                    <i class="fa fa-arrow-left" style="margin-right: 5px;"></i> Back
                </a>
            </div>
            <div style="position: absolute; left: 0; right: 0; text-align: center; pointer-events: none;">
                <h1 style="font-weight: 700; color: var(--crm-text); margin: 0; font-size: 20px; pointer-events: auto; display: inline-block;">
                    <?php echo htmlspecialchars($report_data['title']); ?>
                </h1>
            </div>
            <div style="z-index: 2; display: flex; align-items: center; gap: 8px;">
                <?php if (!empty($report_data['pivot_data'])) { ?>
                <a href="<?php echo base_url(); ?>manager/download-report/<?php echo $dept; ?>/<?php echo $report; ?>?<?php echo http_build_query(array_merge($_GET, array('export' => 'pivot'))); ?>" class="btn btn-primary" style="font-weight: 600; border-radius: 4px; padding: 6px 14px; font-size: 12.5px; background: #2563eb; border-color: #1d4ed8; color: #ffffff;">
                    <i class="fa fa-table" style="margin-right: 5px;"></i> Export Summary CSV
                </a>
                <?php } ?>
                <?php if (isset($is_admin_or_support) && $is_admin_or_support) { ?>
                <a href="<?php echo base_url(); ?>manager/download-report/<?php echo $dept; ?>/<?php echo $report; ?>?<?php echo http_build_query(array_merge($_GET, array('export' => 'csv'))); ?>" class="btn btn-success" style="font-weight: 600; border-radius: 4px; padding: 6px 14px; font-size: 12.5px; background: #10b981; border-color: #059669; color: #ffffff;">
                    <i class="fa fa-list-alt" style="margin-right: 5px;"></i> Detailed Records CSV
                </a>
                <?php } ?>
            </div>
        </div>
    </section>

    <section class="content" style="padding-top: 0;">
        <?php if (!empty($report_data['summary'])) { ?>
        <div style="display: flex; flex-direction: row; flex-wrap: nowrap; gap: 8px; overflow-x: auto; margin-bottom: 16px; width: 100%; padding-bottom: 4px;">
            <?php foreach ($report_data['summary'] as $sum_key => $sum_val) { 
                $label = is_array($sum_val) ? (isset($sum_val['label']) ? $sum_val['label'] : (isset($sum_val['name']) ? $sum_val['name'] : '')) : $sum_key;
                $value = is_array($sum_val) ? (isset($sum_val['value']) ? $sum_val['value'] : '') : $sum_val;
                $label = preg_replace('/^(count\s+of|counta\s+of)\s+/i', '', trim($label));
            ?>
            <div style="flex: 1 1 0; min-width: 105px; background: #ffffff; border: 1px solid var(--crm-border); border-top: 3px solid #d4f938; border-radius: 6px; padding: 8px 10px; text-align: left; box-sizing: border-box;">
                <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.3px; color: var(--crm-text-muted); font-weight: 600; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?php echo htmlspecialchars($label); ?>">
                    <?php echo htmlspecialchars($label); ?>
                </div>
                <div style="font-size: 14.5px; font-weight: 700; color: #000000; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <?php echo htmlspecialchars($value); ?>
                </div>
            </div>
            <?php } ?>
        </div>
        <?php } ?>

        <div class="box filter-box-wrap" style="margin-bottom: 16px; border-radius: 6px; border: 1px solid var(--crm-border); background: #ffffff;">
            <div class="box-body" style="padding: 12px 14px;">
                <form id="reportFilterForm" class="form-inline" action="<?php echo base_url(); ?>manager/download-report/<?php echo $dept; ?>/<?php echo $report; ?>" method="get" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">

                    <div class="form-group" style="margin: 0;">
                        <select class="form-control input-sm" name="period" id="adminReportPeriod" onchange="toggleCustomDates(this.value);" style="border-radius: 4px; font-size: 12px;">
                            <option value="">Period: All Time</option>
                            <option value="daily" <?php if(isset($filters['period']) && $filters['period']=='daily') echo 'selected'; ?>>Today</option>
                            <option value="weekly" <?php if(isset($filters['period']) && $filters['period']=='weekly') echo 'selected'; ?>>Last 7 Days</option>
                            <option value="monthly" <?php if(isset($filters['period']) && $filters['period']=='monthly') echo 'selected'; ?>>This Month</option>
                            <option value="custom" <?php if(isset($filters['period']) && $filters['period']=='custom') echo 'selected'; ?>>Custom Dates</option>
                        </select>
                    </div>

                    <div class="form-group" id="adminDateRangeWrap" style="margin: 0; <?php echo (isset($filters['period']) && $filters['period']=='custom') ? 'display:inline-flex; align-items: center; gap: 6px;' : 'display:none;'; ?>">
                        <div class="input-daterange input-group" style="width: 210px;">
                            <input type="text" class="input-sm form-control datepicker" name="d1" placeholder="From" value="<?php echo isset($filters['d1']) ? htmlspecialchars($filters['d1']) : ''; ?>" autocomplete="off" style="border-radius: 4px 0 0 4px; font-size: 12px;" />
                            <span class="input-group-addon" style="padding: 4px 8px; font-size: 11px;">to</span>
                            <input type="text" class="input-sm form-control datepicker" name="d2" placeholder="To" value="<?php echo isset($filters['d2']) ? htmlspecialchars($filters['d2']) : ''; ?>" autocomplete="off" style="border-radius: 0 4px 4px 0; font-size: 12px;" />
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary" style="background: #2563eb; border-color: #1d4ed8; color: #ffffff; font-weight: 600; border-radius: 4px; padding: 4px 10px; font-size: 11px;">
                            Run
                        </button>
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
                            <span style="display: inline-flex; align-items: center; gap: 5px; background: #f1f5f9; color: #111827; border: 1px solid #cbd5e1; border-radius: 4px; padding: 4px 8px; font-size: 12px; font-weight: 600;">
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
                            <option value="0-30" <?php if(isset($filters['expiry']) && $filters['expiry']=='0-30') echo 'selected'; ?>>0–30 Days</option>
                            <option value="30-60" <?php if(isset($filters['expiry']) && $filters['expiry']=='30-60') echo 'selected'; ?>>30–60 Days</option>
                            <option value="60-90" <?php if(isset($filters['expiry']) && $filters['expiry']=='60-90') echo 'selected'; ?>>60–90 Days</option>
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

                    <div class="form-group" style="margin: 0; flex-grow: 1; min-width: 140px;">
                        <input type="text" class="form-control input-sm" name="search" placeholder="Search keywords..." value="<?php echo isset($filters['search']) ? htmlspecialchars($filters['search']) : ''; ?>" style="width: 100%; border-radius: 4px; font-size: 12px;" />
                    </div>

                    <div class="form-group" style="margin: 0; display: flex; gap: 6px;">
                        <button type="submit" class="btn btn-sm btn-primary" style="background: #09090b; border-color: #09090b; color: #ffffff; font-weight: 600; border-radius: 4px; padding: 4px 12px; font-size: 12px;">
                            <i class="fa fa-filter"></i> Apply
                        </button>
                        <a href="<?php echo base_url(); ?>manager/download-report/<?php echo $dept; ?>/<?php echo $report; ?>" class="btn btn-sm btn-default" style="font-weight: 600; border-radius: 4px; padding: 4px 10px; font-size: 12px; color: var(--crm-text);">
                            Reset
                        </a>
                    </div>

                </form>
            </div>
        </div>

        <?php if (!empty($report_data['pivot_data']) && !empty($report_data['pivot_data']['groups'])) { ?>
        <div class="pivot-table-wrap protected-crm-data">
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
                        $cat_name = !empty($grp['category']) ? $grp['category'] : 'Group';
                    ?>
                    <tr class="pivot-group-row" onclick="togglePivotGroup('grp_<?php echo $grp_idx; ?>', this);">
                        <td colspan="<?php echo $col_count; ?>" style="padding-left: 12px;">
                            <span class="pivot-toggle-btn">-</span>
                            <strong><?php echo htmlspecialchars($cat_name); ?></strong>
                            <span style="font-size: 11px; font-weight: normal; color: #64748b; margin-left: 8px;">(<?php echo count($grp['rows']); ?> members)</span>
                        </td>
                    </tr>
                    <?php if (!empty($grp['rows'])) { ?>
                    <?php foreach ($grp['rows'] as $r_item) { ?>
                    <tr class="pivot-item-row grp_<?php echo $grp_idx; ?>">
                        <?php 
                        $cell_idx = 0;
                        foreach ($r_item as $cell) { 
                            $align = ($cell_idx < 2) ? 'left' : 'right';
                            $indent = ($cell_idx == 0) ? 'padding-left: 28px;' : '';
                            $cell_val = is_array($cell) ? (isset($cell['val']) ? $cell['val'] : '') : $cell;
                            $cell_url = is_array($cell) && isset($cell['url']) ? $cell['url'] : '';
                        ?>
                        <td style="text-align: <?php echo $align; ?>; <?php echo $indent; ?>">
                            <?php if (!empty($cell_url)) { ?>
                            <a href="<?php echo $cell_url; ?>" style="color: #2563eb; font-weight: 700; text-decoration: underline;">
                                <?php echo htmlspecialchars($cell_val); ?>
                            </a>
                            <?php } else { ?>
                            <?php echo htmlspecialchars($cell_val); ?>
                            <?php } ?>
                        </td>
                        <?php 
                            $cell_idx++;
                        } ?>
                    </tr>
                    <?php } ?>
                    <?php } else { ?>
                    <tr class="pivot-item-row grp_<?php echo $grp_idx; ?>">
                        <td colspan="<?php echo $col_count; ?>" style="text-align: center; color: #94a3b8; padding: 12px;">
                            No records in this group.
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if (!empty($grp['subtotal'])) { ?>
                    <tr class="pivot-subtotal-row grp_<?php echo $grp_idx; ?>">
                        <?php 
                        $cell_idx = 0;
                        foreach ($grp['subtotal'] as $cell) { 
                            $align = ($cell_idx < 2) ? 'left' : 'right';
                            $cell_val = is_array($cell) ? (isset($cell['val']) ? $cell['val'] : '') : $cell;
                        ?>
                        <td style="text-align: <?php echo $align; ?>;">
                            <?php echo htmlspecialchars($cell_val); ?>
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
                            $cell_val = is_array($cell) ? (isset($cell['val']) ? $cell['val'] : '') : $cell;
                        ?>
                        <td style="text-align: <?php echo $align; ?>;">
                            <?php echo htmlspecialchars($cell_val); ?>
                        </td>
                        <?php 
                            $cell_idx++;
                        } ?>
                    </tr>
                </tfoot>
                <?php } ?>
            </table>
        </div>
        <?php } elseif (!empty($report_data['rows'])) { ?>
        <div class="box protected-crm-data" style="border-radius: 6px; border: 1px solid var(--crm-border); background: #ffffff;">
            <div class="box-body no-padding">
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
            </div>
        </div>
        <?php } else { ?>
        <div style="background: #ffffff; border: 1px solid var(--crm-border); border-radius: 6px; padding: 36px 20px; text-align: center; color: var(--crm-text-muted); font-size: 13px;">
            <i class="fa fa-info-circle" style="font-size: 24px; color: #94a3b8; margin-bottom: 8px;"></i>
            <div style="font-weight: 600; color: #1e293b;">No records available for the selected filters.</div>
        </div>
        <?php } ?>

    </section>
</div>

<script>
function toggleCustomDates(val) {
    var elem = document.getElementById('adminDateRangeWrap');
    if (elem) {
        if (val === 'custom') {
            elem.style.display = 'inline-flex';
            elem.style.alignItems = 'center';
            elem.style.gap = '6px';
        } else {
            elem.style.display = 'none';
        }
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

    <?php if (!isset($is_admin_or_support) || !$is_admin_or_support) { ?>
    document.addEventListener('copy', function(e) {
        var target = e.target;
        if (target && !target.matches('input, textarea, select, [contenteditable]')) {
            if (target.closest('.protected-crm-data') || target.closest('.pivot-table-wrap') || target.closest('.table-responsive')) {
                e.preventDefault();
                return false;
            }
        }
    });
    <?php } ?>
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
