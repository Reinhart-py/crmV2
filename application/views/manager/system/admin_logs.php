<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
    <section class="content-header">
        <h1><?php echo !empty($info['page_heading']) ? $info['page_heading'] : 'Admin Logs'; ?></h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box p-4">
                    <div class="x_row">
                        <div class="x_col">
                            <form class="form-inline head_filter form-inline-mob" method="get" action="<?php echo base_url(); ?>manager/admin-logs">
                                <div class="form-group">
                                    <div class="input-daterange input-group" id="datepicker" style="width: 100%;">
                                        <input type="text" class="input-md form-control datepicker" name="d1"
                                            placeholder="From Date"
                                            value="<?php echo htmlspecialchars($this->input->get('d1')); ?>"
                                            autocomplete="off" />
                                        <span class="input-group-addon">To</span>
                                        <input type="text" class="input-md form-control datepicker" name="d2"
                                            placeholder="To Date"
                                            value="<?php echo htmlspecialchars($this->input->get('d2')); ?>"
                                            autocomplete="off" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <select name="user_id" class="form-control select2" style="min-width: 170px;">
                                        <option value="">Select User</option>
                                        <?php if (!empty($users_list)) {
                                            foreach ($users_list as $u) {
                                                $sel = ($this->input->get('user_id') == $u['id']) ? 'selected' : '';
                                                echo '<option value="'.$u['id'].'" '.$sel.'>'.htmlspecialchars($u['name']).'</option>';
                                            }
                                        } ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <select name="activity" class="form-control" style="min-width: 150px;">
                                        <option value="">Select Activity</option>
                                        <?php if (!empty($activities)) {
                                            foreach ($activities as $act) {
                                                $sel = ($this->input->get('activity') == $act) ? 'selected' : '';
                                                echo '<option value="'.$act.'" '.$sel.'>'.ucwords(str_replace('_', ' ', $act)).'</option>';
                                            }
                                        } ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <select name="status" class="form-control">
                                        <option value="">Select Status</option>
                                        <option value="SUCCESS" <?php echo ($this->input->get('status') == 'SUCCESS') ? 'selected' : ''; ?>>Success</option>
                                        <option value="FAILED" <?php echo ($this->input->get('status') == 'FAILED') ? 'selected' : ''; ?>>Failed</option>
                                        <option value="TIMEOUT" <?php echo ($this->input->get('status') == 'TIMEOUT') ? 'selected' : ''; ?>>Timeout</option>
                                        <option value="TERMINATED" <?php echo ($this->input->get('status') == 'TERMINATED') ? 'selected' : ''; ?>>Terminated</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Search</button>
                                    <a href="<?php echo base_url(); ?>manager/admin-logs" class="btn btn-danger">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="box-body table-responsive no-padding">
                        <?php if (!empty($logs)) { ?>
                            <table class="table table-hover table-striped table-bordered" style="margin-bottom:0;">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">#</th>
                                        <th style="width: 170px;">Date & Time</th>
                                        <th style="width: 160px;">User</th>
                                        <th style="width: 150px;">Activity</th>
                                        <th style="width: 100px;">Status</th>
                                        <th style="width: 130px;">IP Address</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($logs as $log) { 
                                        $st = strtoupper(trim($log['status']));
                                        if ($st === 'SUCCESS') {
                                            $statusBadge = '<span class="label label-success">Success</span>';
                                        } elseif ($st === 'FAILED') {
                                            $statusBadge = '<span class="label label-danger">Failed</span>';
                                        } elseif ($st === 'TIMEOUT') {
                                            $statusBadge = '<span class="label label-warning">Timeout</span>';
                                        } elseif ($st === 'TERMINATED') {
                                            $statusBadge = '<span class="label label-info">Terminated</span>';
                                        } else {
                                            $statusBadge = '<span class="label label-default">' . htmlspecialchars(ucfirst(strtolower($st))) . '</span>';
                                        }
                                    ?>
                                        <tr>
                                            <td><strong><?php echo $log['id']; ?></strong></td>
                                            <td>
                                                <span class="no_show"><?php echo !empty($log['created_at']) ? date('YmdHi', strtotime($log['created_at'])) : ''; ?></span>
                                                <?php echo !empty($log['created_at']) ? date('d M Y, h:i A', strtotime($log['created_at'])) : '-'; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars(!empty($log['username']) ? $log['username'] : 'System'); ?></strong>
                                                <?php if (!empty($log['user_id'])) { ?>
                                                    <br><small class="text-muted">ID: <?php echo $log['user_id']; ?></small>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $log['activity']))); ?>
                                            </td>
                                            <td><?php echo $statusBadge; ?></td>
                                            <td><code><?php echo htmlspecialchars($log['ip_address']); ?></code></td>
                                            <td>
                                                <?php echo htmlspecialchars($log['details']); ?>
                                                <?php if (!empty($log['logout_time'])) { ?>
                                                    <br><small class="text-muted">Logout: <?php echo date('d M Y, h:i A', strtotime($log['logout_time'])); ?></small>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <?php if (!empty($pagination)) { ?>
                                <div class="box-footer clearfix">
                                    <?php echo $pagination; ?>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="alert alert-info">No Data Found</div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
