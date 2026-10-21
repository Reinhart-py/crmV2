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
                <?php if (isset($is_admin_or_support) && $is_admin_or_support) { ?>
                <div class="btn-group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-weight: 600; border-radius: 4px; padding: 6px 14px; font-size: 12.5px; background: #ffffff; border-color: #d1d5db; color: #1e293b; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa fa-download"></i> Export <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" style="border-radius: 6px; box-shadow: 0 4px 14px rgba(0,0,0,0.1); min-width: 210px; padding: 6px 0; margin-top: 4px; border: 1px solid #e2e8f0;">
                        <?php if (!empty($report_data['pivot_data'])) { ?>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/download-report/<?php echo $dept; ?>/<?php echo $report; ?>?<?php echo http_build_query(array_merge($_GET, array('export' => 'pivot_excel'))); ?>" style="padding: 8px 16px; font-size: 12.5px; font-weight: 500; color: #1e293b;">
                                <i class="fa fa-file-excel-o text-success" style="margin-right: 8px; width: 14px;"></i> Export Summary Excel
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/download-report/<?php echo $dept; ?>/<?php echo $report; ?>?<?php echo http_build_query(array_merge($_GET, array('export' => 'pivot'))); ?>" style="padding: 8px 16px; font-size: 12.5px; font-weight: 500; color: #1e293b;">
                                <i class="fa fa-table text-primary" style="margin-right: 8px; width: 14px;"></i> Export Summary CSV
                            </a>
                        </li>
                        <li role="separator" class="divider" style="margin: 4px 0;"></li>
                        <?php } ?>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/download-report/<?php echo $dept; ?>/<?php echo $report; ?>?<?php echo http_build_query(array_merge($_GET, array('export' => 'excel'))); ?>" style="padding: 8px 16px; font-size: 12.5px; font-weight: 500; color: #1e293b;">
                                <i class="fa fa-file-excel-o text-success" style="margin-right: 8px; width: 14px;"></i> Detailed Records Excel
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>manager/download-report/<?php echo $dept; ?>/<?php echo $report; ?>?<?php echo http_build_query(array_merge($_GET, array('export' => 'csv'))); ?>" style="padding: 8px 16px; font-size: 12.5px; font-weight: 500; color: #1e293b;">
                                <i class="fa fa-list-alt text-info" style="margin-right: 8px; width: 14px;"></i> Detailed Records CSV
                            </a>
                        </li>
                    </ul>
                </div>
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
                            <option value="future" <?php if(isset($filters['expiry']) && $filters['expiry']=='future') echo 'selected'; ?>>All Future</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <select class="form-control input-sm" name="tl_emirate" id="filter_tl_emirate" style="border-radius: 4px; font-size: 12px;">
                            <option value="">All Emirates</option>
                            <?php 
                            $uae_emirates = array('Abu Dhabi', 'Dubai', 'Sharjah', 'Ajman', 'Umm Al Quwain', 'Ras Al Khaimah', 'Fujairah');
                            foreach ($uae_emirates as $em) {
                            ?>
                            <option value="<?php echo $em; ?>" <?php if(isset($filters['tl_emirate']) && $filters['tl_emirate'] == $em) echo 'selected'; ?>><?php echo $em; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <select class="form-control input-sm" name="tl_zone" id="filter_tl_zone" style="border-radius: 4px; font-size: 12px;">
                            <option value="">All Zones</option>
                            <?php 
                            $tl_zones = array('Mainland', 'Freezone', 'Offshore', 'Other');
                            foreach ($tl_zones as $zn) {
                            ?>
                            <option value="<?php echo $zn; ?>" <?php if(isset($filters['tl_zone']) && $filters['tl_zone'] == $zn) echo 'selected'; ?>><?php echo $zn; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <select class="form-control input-sm select2" name="tl_authority" id="filter_tl_authority" style="border-radius: 4px; font-size: 12px; min-width: 170px;">
                            <option value="">All Licensing Authorities</option>
                            <optgroup label="Mainland">
                                <option value="ADDED" data-emirate="Abu Dhabi" data-zone="Mainland" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'ADDED') echo 'selected'; ?>>ADDED</option>
                                <option value="DET" data-emirate="Dubai" data-zone="Mainland" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'DET') echo 'selected'; ?>>DET</option>
                                <option value="SEDD" data-emirate="Sharjah" data-zone="Mainland" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'SEDD') echo 'selected'; ?>>SEDD</option>
                                <option value="Ajman DED" data-emirate="Ajman" data-zone="Mainland" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'Ajman DED') echo 'selected'; ?>>Ajman DED</option>
                                <option value="UAQ DED" data-emirate="Umm Al Quwain" data-zone="Mainland" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'UAQ DED') echo 'selected'; ?>>UAQ DED</option>
                                <option value="RAK DED" data-emirate="Ras Al Khaimah" data-zone="Mainland" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'RAK DED') echo 'selected'; ?>>RAK DED</option>
                                <option value="FM" data-emirate="Fujairah" data-zone="Mainland" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'FM') echo 'selected'; ?>>FM</option>
                                <option value="DM" data-emirate="Fujairah" data-zone="Mainland" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'DM') echo 'selected'; ?>>DM</option>
                            </optgroup>
                            <optgroup label="Freezone">
                                <option value="Masdar" data-emirate="Abu Dhabi" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'Masdar') echo 'selected'; ?>>Masdar</option>
                                <option value="TCA" data-emirate="Abu Dhabi" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'TCA') echo 'selected'; ?>>TCA</option>
                                <option value="ADAFZ" data-emirate="Abu Dhabi" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'ADAFZ') echo 'selected'; ?>>ADAFZ</option>
                                <option value="KEZAD" data-emirate="Abu Dhabi" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'KEZAD') echo 'selected'; ?>>KEZAD</option>
                                <option value="ADGM" data-emirate="Abu Dhabi" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'ADGM') echo 'selected'; ?>>ADGM</option>
                                <option value="TIP" data-emirate="Abu Dhabi" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'TIP') echo 'selected'; ?>>TIP</option>
                                <option value="DDA" data-emirate="Dubai" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'DDA') echo 'selected'; ?>>DDA</option>
                                <option value="DAFZA" data-emirate="Dubai" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'DAFZA') echo 'selected'; ?>>DAFZA</option>
                                <option value="JAFZA" data-emirate="Dubai" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'JAFZA') echo 'selected'; ?>>JAFZA</option>
                                <option value="DMCC" data-emirate="Dubai" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'DMCC') echo 'selected'; ?>>DMCC</option>
                                <option value="DIFC" data-emirate="Dubai" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'DIFC') echo 'selected'; ?>>DIFC</option>
                                <option value="DSO" data-emirate="Dubai" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'DSO') echo 'selected'; ?>>DSO</option>
                                <option value="DHCC" data-emirate="Dubai" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'DHCC') echo 'selected'; ?>>DHCC</option>
                                <option value="DMCA" data-emirate="Dubai" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'DMCA') echo 'selected'; ?>>DMCA</option>
                                <option value="DS" data-emirate="Dubai" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'DS') echo 'selected'; ?>>DS</option>
                                <option value="MFZ" data-emirate="Dubai" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'MFZ') echo 'selected'; ?>>MFZ</option>
                                <option value="DWTC" data-emirate="Dubai" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'DWTC') echo 'selected'; ?>>DWTC</option>
                                <option value="DH" data-emirate="Dubai" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'DH') echo 'selected'; ?>>DH</option>
                                <option value="Trakhees" data-emirate="Dubai" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'Trakhees') echo 'selected'; ?>>Trakhees</option>
                                <option value="NIP" data-emirate="Dubai" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'NIP') echo 'selected'; ?>>NIP</option>
                                <option value="Expo City" data-emirate="Dubai" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'Expo City') echo 'selected'; ?>>Expo City</option>
                                <option value="SAIF Zone" data-emirate="Sharjah" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'SAIF Zone') echo 'selected'; ?>>SAIF Zone</option>
                                <option value="HFZA" data-emirate="Sharjah" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'HFZA') echo 'selected'; ?>>HFZA</option>
                                <option value="SPC" data-emirate="Sharjah" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'SPC') echo 'selected'; ?>>SPC</option>
                                <option value="SHAMS" data-emirate="Sharjah" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'SHAMS') echo 'selected'; ?>>SHAMS</option>
                                <option value="SRTIP" data-emirate="Sharjah" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'SRTIP') echo 'selected'; ?>>SRTIP</option>
                                <option value="SHCC" data-emirate="Sharjah" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'SHCC') echo 'selected'; ?>>SHCC</option>
                                <option value="SOTI" data-emirate="Sharjah" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'SOTI') echo 'selected'; ?>>SOTI</option>
                                <option value="AFZ" data-emirate="Ajman" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'AFZ') echo 'selected'; ?>>AFZ</option>
                                <option value="AMCFZ" data-emirate="Ajman" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'AMCFZ') echo 'selected'; ?>>AMCFZ</option>
                                <option value="UAQ FTZ" data-emirate="Umm Al Quwain" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'UAQ FTZ') echo 'selected'; ?>>UAQ FTZ</option>
                                <option value="RAKEZ" data-emirate="Ras Al Khaimah" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'RAKEZ') echo 'selected'; ?>>RAKEZ</option>
                                <option value="RAKMC" data-emirate="Ras Al Khaimah" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'RAKMC') echo 'selected'; ?>>RAKMC</option>
                                <option value="IC" data-emirate="Ras Al Khaimah" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'IC') echo 'selected'; ?>>IC</option>
                                <option value="RAK DAO" data-emirate="Ras Al Khaimah" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'RAK DAO') echo 'selected'; ?>>RAK DAO</option>
                                <option value="FFZ" data-emirate="Fujairah" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'FFZ') echo 'selected'; ?>>FFZ</option>
                                <option value="CCF" data-emirate="Fujairah" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'CCF') echo 'selected'; ?>>CCF</option>
                                <option value="IFZA" data-emirate="Dubai" data-zone="Freezone" <?php if(isset($filters['tl_authority']) && $filters['tl_authority'] == 'IFZA') echo 'selected'; ?>>IFZA</option>
                            </optgroup>
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
                                <?php echo htmlspecialchars(isset($st['status']) ? $st['status'] : (isset($st['name']) ? $st['name'] : (isset($st['title']) ? $st['title'] : ''))); ?>
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

        <?php if ($report == 'custom' && !empty($report_data['available_fields'])) { ?>
        <div class="box" style="border-radius: 6px; border: 1px solid var(--crm-border); background: #ffffff; margin-bottom: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div class="box-header with-border" style="padding: 10px 16px; background: #f8fafc; border-bottom: 1px solid var(--crm-border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                <h4 class="box-title" style="font-size: 13px; font-weight: 700; color: #1e293b; margin: 0;">
                    <i class="fa fa-columns" style="color: #2563eb; margin-right: 6px;"></i> Configurable Column Selector (PowerSheet)
                </h4>
                <div style="display: flex; gap: 6px;">
                    <button type="button" class="btn btn-xs btn-default" onclick="selectAllCols(true)" style="font-weight: 600; font-size: 11px;">Select All</button>
                    <button type="button" class="btn btn-xs btn-default" onclick="selectAllCols(false)" style="font-weight: 600; font-size: 11px;">Clear All</button>
                    <button type="button" class="btn btn-xs btn-default" onclick="resetDefaultCols()" style="font-weight: 600; font-size: 11px;">Default Columns</button>
                </div>
            </div>
            <div class="box-body" style="padding: 14px 16px;">
                <form method="get" action="<?php echo base_url(); ?>manager/download-report/<?php echo $dept; ?>/<?php echo $report; ?>" id="customColForm">
                    <input type="hidden" name="period" value="<?php echo isset($filters['period']) ? htmlspecialchars($filters['period']) : ''; ?>" />
                    <input type="hidden" name="d1" value="<?php echo isset($filters['d1']) ? htmlspecialchars($filters['d1']) : ''; ?>" />
                    <input type="hidden" name="d2" value="<?php echo isset($filters['d2']) ? htmlspecialchars($filters['d2']) : ''; ?>" />
                    <input type="hidden" name="search" value="<?php echo isset($filters['search']) ? htmlspecialchars($filters['search']) : ''; ?>" />
                    <?php if (!empty($filters['employee_id'])) { ?>
                        <input type="hidden" name="employee_id" value="<?php echo htmlspecialchars(is_array($filters['employee_id']) ? implode(',', $filters['employee_id']) : $filters['employee_id']); ?>" />
                    <?php } ?>

                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 8px 14px; margin-bottom: 12px;">
                        <?php 
                        $active_cols = !empty($report_data['fields']) ? $report_data['fields'] : array();
                        foreach ($report_data['available_fields'] as $col_key => $col_label) { 
                            $is_checked = in_array($col_key, $active_cols);
                        ?>
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: normal; color: #334155; margin: 0; cursor: pointer; padding: 5px 8px; border-radius: 4px; background: <?php echo $is_checked ? '#f0fdf4' : '#ffffff'; ?>; border: 1px solid <?php echo $is_checked ? '#86efac' : '#e2e8f0'; ?>;">
                            <input type="checkbox" name="columns[]" value="<?php echo $col_key; ?>" class="custom-col-cb" <?php echo $is_checked ? 'checked' : ''; ?> onchange="this.parentElement.style.background = this.checked ? '#f0fdf4' : '#ffffff'; this.parentElement.style.borderColor = this.checked ? '#86efac' : '#e2e8f0';" />
                            <span><?php echo htmlspecialchars($col_label); ?></span>
                        </label>
                        <?php } ?>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 8px; border-top: 1px solid var(--crm-border); padding-top: 10px;">
                        <button type="submit" class="btn btn-sm btn-primary" style="background: #2563eb; border-color: #2563eb; font-weight: 600; font-size: 12px; border-radius: 4px; padding: 5px 16px;">
                            <i class="fa fa-check"></i> Apply Selected Columns
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php } ?>

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
                        $cat_url = !empty($grp['category_url']) ? $grp['category_url'] : '';
                    ?>
                    <tr class="pivot-group-row" onclick="togglePivotGroup('grp_<?php echo $grp_idx; ?>', this);">
                        <td colspan="<?php echo $col_count; ?>" style="padding-left: 12px;">
                            <span class="pivot-toggle-btn">-</span>
                            <strong><?php echo htmlspecialchars($cat_name); ?></strong>
                            <span style="font-size: 11px; font-weight: normal; color: #64748b; margin-left: 8px;">(<?php echo count($grp['rows']); ?> <?php echo in_array($report, array('database', 'quote', 'lost', 'lead')) ? 'records' : 'members'; ?>)</span>
                        </td>
                    </tr>
                    <?php if (!empty($grp['rows'])) { ?>
                    <?php foreach ($grp['rows'] as $r_item) { ?>
                    <tr class="pivot-item-row grp_<?php echo $grp_idx; ?>">
                        <?php 
                        $cell_idx = 0;
                        foreach ($r_item as $cell) { 
                            $align = ($report == 'quote') ? (($cell_idx < 4) ? 'left' : 'right') : (($report == 'database') ? (($cell_idx < 3) ? 'left' : 'right') : (($cell_idx < 2) ? 'left' : 'right'));
                            $indent = ($cell_idx == 0) ? 'padding-left: 28px;' : '';
                            $cell_val = is_array($cell) ? (isset($cell['val']) ? $cell['val'] : '') : $cell;
                            $cell_url = is_array($cell) && isset($cell['url']) ? $cell['url'] : '';
                            $is_numeric = (bool)preg_match('/[0-9]/', (string)$cell_val) && !preg_match('/[a-zA-Z]{3,}/', (string)$cell_val);
                        ?>
                        <td style="text-align: <?php echo $align; ?>; <?php echo $indent; ?>">
                            <?php if (!empty($cell_url) && $is_numeric) { ?>
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
                            $align = ($report == 'quote') ? (($cell_idx < 4) ? 'left' : 'right') : (($report == 'database') ? (($cell_idx < 3) ? 'left' : 'right') : (($cell_idx < 2) ? 'left' : 'right'));
                            $cell_val = is_array($cell) ? (isset($cell['val']) ? $cell['val'] : '') : $cell;
                            $cell_url = is_array($cell) && isset($cell['url']) ? $cell['url'] : '';
                            $is_numeric = (bool)preg_match('/[0-9]/', (string)$cell_val) && !preg_match('/[a-zA-Z]{3,}/', (string)$cell_val);
                        ?>
                        <td style="text-align: <?php echo $align; ?>;">
                            <?php if (!empty($cell_url) && $is_numeric) { ?>
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
                    <?php } ?>
                </tbody>
                <?php if (!empty($report_data['pivot_data']['grand_total'])) { ?>
                <tfoot>
                    <tr class="pivot-grand-total-row">
                        <?php 
                        $cell_idx = 0;
                        foreach ($report_data['pivot_data']['grand_total'] as $cell) { 
                            $align = ($report == 'quote') ? (($cell_idx < 4) ? 'left' : 'right') : (($report == 'database') ? (($cell_idx < 3) ? 'left' : 'right') : (($cell_idx < 2) ? 'left' : 'right'));
                            $cell_val = is_array($cell) ? (isset($cell['val']) ? $cell['val'] : '') : $cell;
                            $cell_url = is_array($cell) && isset($cell['url']) ? $cell['url'] : '';
                            $is_numeric = (bool)preg_match('/[0-9]/', (string)$cell_val) && !preg_match('/[a-zA-Z]{3,}/', (string)$cell_val);
                        ?>
                        <td style="text-align: <?php echo $align; ?>;">
                            <?php if (!empty($cell_url) && $is_numeric) { ?>
                            <a href="<?php echo $cell_url; ?>" style="color: #2563eb; font-weight: 800; text-decoration: underline;">
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
                </tfoot>
                <?php } ?>
            </table>
        </div>
        <?php } ?>
        <?php if (empty($report_data['pivot_data']) || empty($report_data['pivot_data']['groups'])) { ?>
        <div style="background: #ffffff; border: 1px solid var(--crm-border); border-radius: 6px; padding: 36px 20px; text-align: center; color: var(--crm-text-muted); font-size: 13px;">
            <i class="fa fa-info-circle" style="font-size: 24px; color: #94a3b8; margin-bottom: 8px;"></i>
            <div style="font-weight: 600; color: #1e293b;">No records available for the selected filters.</div>
        </div>
        <?php } ?>

    </section>
</div>

<script>

function selectAllCols(check) {
    document.querySelectorAll('.custom-col-cb').forEach(function(cb) {
        cb.checked = check;
        cb.parentElement.style.background = check ? '#f0fdf4' : '#ffffff';
        cb.parentElement.style.borderColor = check ? '#86efac' : '#e2e8f0';
    });
}

function resetDefaultCols() {
    var defs = ['lead_id', 'customer', 'poc', 'contact1', 'email', 'servicecat', 'mrr', 'lead_status', 'assigned_name', 'created'];
    document.querySelectorAll('.custom-col-cb').forEach(function(cb) {
        var isDef = defs.indexOf(cb.value) !== -1;
        cb.checked = isDef;
        cb.parentElement.style.background = isDef ? '#f0fdf4' : '#ffffff';
        cb.parentElement.style.borderColor = isDef ? '#86efac' : '#e2e8f0';
    });
}

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

    function filterTradeLicenseAuthorities() {
        var em = $('#filter_tl_emirate').val();
        var zn = $('#filter_tl_zone').val();
        var currentAuth = $('#filter_tl_authority').val();
        var matchFound = false;

        $('#filter_tl_authority option').each(function() {
            var optEm = $(this).attr('data-emirate');
            var optZn = $(this).attr('data-zone');
            if (!optEm && !optZn) return;

            var show = true;
            if (em && optEm && optEm !== em) {
                show = false;
            }
            if (zn && optZn && optZn.toLowerCase() !== zn.toLowerCase()) {
                show = false;
            }

            if (show) {
                $(this).prop('disabled', false).show();
                if ($(this).val() === currentAuth) matchFound = true;
            } else {
                $(this).prop('disabled', true).hide();
            }
        });

        if (currentAuth && !matchFound) {
            $('#filter_tl_authority').val('');
        }
        if ($.fn.select2 && $('#filter_tl_authority').hasClass('select2')) {
            $('#filter_tl_authority').trigger('change.select2');
        }
    }

    $('#filter_tl_emirate, #filter_tl_zone').on('change', function() {
        filterTradeLicenseAuthorities();
    });
    filterTradeLicenseAuthorities();

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
