<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<style>
.drilldown-table-wrap {
    background: #ffffff;
    border: 1px solid var(--crm-border);
    border-radius: 6px;
    overflow-x: auto;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.drilldown-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12.5px;
}
.drilldown-table th {
    background: #607d9b;
    color: #ffffff;
    font-weight: 700;
    padding: 10px 14px;
    border: 1px solid #4a637d;
    white-space: nowrap;
}
.drilldown-table td {
    padding: 9px 14px;
    border: 1px solid #e2e8f0;
    color: #1e293b;
    vertical-align: middle;
}
.drilldown-table tr:hover {
    background: #f8fafc;
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
    .content-header .badge,
    .main-footer {
        display: none !important;
    }
    .content-wrapper {
        margin: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
    }
    .drilldown-table-wrap {
        border: none !important;
        box-shadow: none !important;
    }
    .drilldown-table th {
        background: #607d9b !important;
        color: #ffffff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>

<div class="content-wrapper px-4">
    <section class="content-header" style="margin-bottom: 16px; padding-top: 14px; position: relative;">
        <div style="display: flex; align-items: center; justify-content: space-between; position: relative; min-height: 38px;">
            <div style="z-index: 2;">
                <a href="javascript:window.history.back();" class="btn btn-default" style="font-weight: 600; border-radius: 4px; padding: 6px 14px; font-size: 13px; color: var(--crm-text); border-color: #d1d5db; background: #ffffff;">
                    <i class="fa fa-arrow-left" style="margin-right: 5px;"></i> Back
                </a>
            </div>
            <div style="position: absolute; left: 0; right: 0; text-align: center; pointer-events: none;">
                <h1 style="font-weight: 700; color: var(--crm-text); margin: 0; font-size: 20px; pointer-events: auto; display: inline-block;">
                    <?php echo htmlspecialchars($drilldown_data['title']); ?>
                </h1>
            </div>
            <div style="z-index: 2; display: flex; align-items: center; gap: 8px;">
                <span class="badge" style="background: #2563eb; color: #ffffff; padding: 7px 12px; font-size: 12.5px; font-weight: 600; border-radius: 4px;">
                    <i class="fa fa-list" style="margin-right: 5px;"></i> <?php echo number_format($drilldown_data['count']); ?> Records
                </span>
                <button type="button" onclick="window.print();" class="btn btn-default" style="font-weight: 600; border-radius: 4px; padding: 6px 12px; font-size: 12.5px; background: #ffffff; border-color: #d1d5db; color: #374151;">
                    <i class="fa fa-print"></i> Print
                </button>
            </div>
        </div>
    </section>

    <section class="content" style="padding-top: 0;">
        <div class="drilldown-table-wrap">
            <?php if (!empty($drilldown_data['rows'])) { ?>
            <table class="drilldown-table protected-crm-data">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">#</th>
                        <?php foreach ($drilldown_data['headers'] as $header) { ?>
                        <th><?php echo htmlspecialchars($header); ?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $row_idx = 1;
                    foreach ($drilldown_data['rows'] as $r) { 
                    ?>
                    <tr>
                        <td style="text-align: center; color: #64748b; font-weight: 600;"><?php echo $row_idx++; ?></td>
                        <?php foreach ($drilldown_data['fields'] as $f) { ?>
                        <td>
                            <?php 
                            if ($f == 'action' || $f == 'status') {
                                echo isset($r[$f]) ? $r[$f] : '';
                            } else {
                                echo isset($r[$f]) ? htmlspecialchars($r[$f]) : '-';
                            }
                            ?>
                        </td>
                        <?php } ?>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php } else { ?>
            <div style="padding: 48px; text-align: center; color: #64748b;">
                <i class="fa fa-folder-open-o" style="font-size: 42px; color: #94a3b8; display: block; margin-bottom: 12px;"></i>
                <h4 style="font-weight: 600; color: #334155; margin-bottom: 6px;">No Records Found</h4>
                <p style="margin-bottom: 0; font-size: 13px;">There are no records matching the selected metric and filter criteria.</p>
            </div>
            <?php } ?>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    <?php if (!isset($is_admin_or_support) || !$is_admin_or_support) { ?>
    document.addEventListener('copy', function(e) {
        var target = e.target;
        if (target && !target.matches('input, textarea, select, [contenteditable]')) {
            if (target.closest('.protected-crm-data') || target.closest('.drilldown-table-wrap')) {
                e.preventDefault();
                return false;
            }
        }
    });
    <?php } ?>
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
