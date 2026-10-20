<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-history" style="color:#b91c1c;"></i> Admin Logs
            <small>System Security & Activity Audit Trail</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li>System</li>
            <li class="active">Admin Logs</li>
        </ol>
    </section>

    <section class="content">
        <!-- Filter Card -->
        <div class="box box-info" style="border-top-color: #b91c1c;">
            <div class="box-header with-border">
                <h3 class="box-title" style="font-weight: 600; font-size: 15px;">
                    <i class="fa fa-filter" style="color: #b91c1c;"></i> Filter Logs
                </h3>
                <div class="box-tools pull-right">
                    <span class="label" style="background:#0f172a; color:#fff; padding:4px 10px; border-radius:4px; font-size:12px; font-weight:600;">
                        Total: <?php echo isset($total_logs) ? (int)$total_logs : 0; ?>
                    </span>
                </div>
            </div>
            <div class="box-body">
                <form method="get" action="<?php echo base_url(); ?>manager/admin-logs" class="row">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label style="font-size:12px; font-weight:600; color:#475569;">User</label>
                            <select name="user_id" class="form-control select2" style="width:100%;" onchange="this.form.submit();">
                                <option value="">-- All Users --</option>
                                <?php if (!empty($users_list)) {
                                    foreach ($users_list as $u) {
                                        $sel = ($this->input->get('user_id') == $u['id']) ? 'selected' : '';
                                        echo '<option value="'.$u['id'].'" '.$sel.'>'.htmlspecialchars($u['name']).' ('.htmlspecialchars($u['email']).')</option>';
                                    }
                                } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label style="font-size:12px; font-weight:600; color:#475569;">Activity</label>
                            <select name="activity" class="form-control" onchange="this.form.submit();">
                                <option value="">-- All Activities --</option>
                                <?php if (!empty($activities)) {
                                    foreach ($activities as $act) {
                                        $sel = ($this->input->get('activity') == $act) ? 'selected' : '';
                                        echo '<option value="'.$act.'" '.$sel.'>'.ucwords(str_replace('_', ' ', $act)).'</option>';
                                    }
                                } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label style="font-size:12px; font-weight:600; color:#475569;">Status</label>
                            <select name="status" class="form-control" onchange="this.form.submit();">
                                <option value="">-- All Statuses --</option>
                                <option value="SUCCESS" <?php echo ($this->input->get('status') == 'SUCCESS') ? 'selected' : ''; ?>>SUCCESS</option>
                                <option value="FAILED" <?php echo ($this->input->get('status') == 'FAILED') ? 'selected' : ''; ?>>FAILED</option>
                                <option value="TIMEOUT" <?php echo ($this->input->get('status') == 'TIMEOUT') ? 'selected' : ''; ?>>TIMEOUT</option>
                                <option value="TERMINATED" <?php echo ($this->input->get('status') == 'TERMINATED') ? 'selected' : ''; ?>>TERMINATED</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label style="font-size:12px; font-weight:600; color:#475569;">From Date</label>
                            <input type="date" name="d1" value="<?php echo htmlspecialchars($this->input->get('d1')); ?>" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label style="font-size:12px; font-weight:600; color:#475569;">To Date</label>
                            <input type="date" name="d2" value="<?php echo htmlspecialchars($this->input->get('d2')); ?>" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-1 col-sm-6 col-xs-12" style="margin-top: 25px;">
                        <button type="submit" class="btn btn-danger btn-block" style="background:#b91c1c; border-color:#b91c1c;">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Logs Table Card -->
        <div class="box" style="border-top: 3px solid #b91c1c; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
            <div class="box-body table-responsive no-padding">
                <?php if (!empty($logs)) { ?>
                    <table class="table table-hover table-striped" style="margin-bottom:0; font-size:13px;">
                        <thead>
                            <tr style="background:#f8fafc; color:#334155; border-bottom: 2px solid #e2e8f0;">
                                <th style="width: 50px; text-align:center;">#</th>
                                <th style="width: 160px;">Date & Time</th>
                                <th style="width: 150px;">User</th>
                                <th style="width: 140px;">Activity</th>
                                <th style="width: 90px; text-align:center;">Status</th>
                                <th style="width: 130px;">IP Address</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log) { 
                                $st = strtoupper(trim($log['status']));
                                if ($st === 'SUCCESS') {
                                    $statusBadge = '<span class="badge" style="background:#10b981; color:#fff; font-size:11px; font-weight:600; padding:3px 8px;">SUCCESS</span>';
                                } elseif ($st === 'FAILED') {
                                    $statusBadge = '<span class="badge" style="background:#ef4444; color:#fff; font-size:11px; font-weight:600; padding:3px 8px;">FAILED</span>';
                                } elseif ($st === 'TIMEOUT') {
                                    $statusBadge = '<span class="badge" style="background:#f59e0b; color:#fff; font-size:11px; font-weight:600; padding:3px 8px;">TIMEOUT</span>';
                                } elseif ($st === 'TERMINATED') {
                                    $statusBadge = '<span class="badge" style="background:#8b5cf6; color:#fff; font-size:11px; font-weight:600; padding:3px 8px;">TERMINATED</span>';
                                } else {
                                    $statusBadge = '<span class="badge" style="background:#64748b; color:#fff; font-size:11px; font-weight:600; padding:3px 8px;">' . htmlspecialchars($st) . '</span>';
                                }
                            ?>
                                <tr>
                                    <td style="text-align:center; color:#94a3b8; font-weight:600;"><?php echo $log['id']; ?></td>
                                    <td style="color:#475569; white-space:nowrap;">
                                        <i class="fa fa-clock-o" style="color:#94a3b8; margin-right:4px;"></i>
                                        <?php echo !empty($log['created_at']) ? date('d M Y, h:i A', strtotime($log['created_at'])) : '-'; ?>
                                    </td>
                                    <td>
                                        <strong style="color:#0f172a;"><?php echo htmlspecialchars(!empty($log['username']) ? $log['username'] : 'System'); ?></strong>
                                        <?php if (!empty($log['user_id'])) { ?>
                                            <span style="font-size:11px; color:#94a3b8; display:block;">ID: <?php echo $log['user_id']; ?></span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <span class="label" style="background:#f1f5f9; color:#334155; border:1px solid #cbd5e1; font-weight:600; font-size:11px; padding:3px 8px;">
                                            <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $log['activity']))); ?>
                                        </span>
                                    </td>
                                    <td style="text-align:center;"><?php echo $statusBadge; ?></td>
                                    <td style="color:#64748b; font-family:monospace;"><?php echo htmlspecialchars($log['ip_address']); ?></td>
                                    <td style="color:#334155;">
                                        <?php echo htmlspecialchars($log['details']); ?>
                                        <?php if (!empty($log['logout_time'])) { ?>
                                            <span style="font-size:11px; color:#64748b; display:block; margin-top:2px;">
                                                Logout: <?php echo date('d M Y, h:i A', strtotime($log['logout_time'])); ?>
                                            </span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div style="padding: 40px 20px; text-align: center; background: #ffffff;">
                        <i class="fa fa-history" style="font-size: 42px; color: #cbd5e1; margin-bottom: 15px; display:block;"></i>
                        <h4 style="font-weight: 600; color: #334155; margin-bottom: 6px;">No logs found</h4>
                        <p style="color: #64748b; font-size: 13px; max-width: 400px; margin: 0 auto;">
                            There are currently no system activity or audit log records matching the selected criteria.
                        </p>
                        <?php if ($this->input->get('user_id') || $this->input->get('activity') || $this->input->get('status') || $this->input->get('d1') || $this->input->get('d2')) { ?>
                            <div style="margin-top: 15px;">
                                <a href="<?php echo base_url(); ?>manager/admin-logs" class="btn btn-default btn-sm" style="border-radius:4px;">
                                    <i class="fa fa-times"></i> Clear Filters
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>

            <?php if (!empty($pagination)) { ?>
                <div class="box-footer clearfix" style="border-top: 1px solid #f1f5f9; background:#fff;">
                    <?php echo $pagination; ?>
                </div>
            <?php } ?>
        </div>
    </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
