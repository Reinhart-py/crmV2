<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
    <section class="content-header">
        <h1> <?php echo $info['page_heading']; ?> </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box p-4">
                    <div class="x_row">
                        <div class="x_col">
                            <form class="form-inline head_filter form-inline-mob" method="get">
                                <div class="form-group">
                                    <input type="text" class="input-md form-control" name="search" placeholder="Search Task..." value="<?php echo htmlspecialchars($this->input->get('search') ?? ''); ?>" autocomplete="off" />
                                </div>
                                <div class="form-group">
                                    <div class="input-daterange input-group" id="datepicker" style="width: 100%;">
                                        <input type="text" class="input-md form-control datepicker" name="d1" placeholder="From Date" value="<?php echo htmlspecialchars($this->input->get('d1') ?? ''); ?>" autocomplete="off" />
                                        <span class="input-group-addon">To</span>
                                        <input type="text" class="input-md form-control datepicker" name="d2" placeholder="To Date" value="<?php echo htmlspecialchars($this->input->get('d2') ?? ''); ?>" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Search</button>
                                    <a href="<?php echo htmlspecialchars($reset_url ?? base_url('manager/kam/task-followup')); ?>" class="btn btn-danger"> Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="box-body">
                        <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
                        <?php echo get_message($this->session->flashdata('message'), 'message'); ?>
                        <?php if (isset($tasks) && !empty($tasks)) { ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="example_tasks">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th>Task / Customer</th>
                                            <th>Assigned To</th>
                                            <th>Follow-up Date</th>
                                            <th>Time</th>
                                            <th>Status</th>
                                            <th>Remark</th>
                                            <th style="width: 90px; text-align:center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tasks as $idx => $t) { ?>
                                            <tr>
                                                <td><?php echo $idx + 1; ?></td>
                                                <td>
                                                    <strong><a href="<?php echo base_url(); ?>manager/tasks/view/<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['customer'] ?? 'Task #' . $t['id']); ?></a></strong>
                                                    <?php if (!empty($t['client_name'])) { ?>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars($t['client_name']); ?></small>
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo htmlspecialchars(!empty($t['assigned_name']) ? $t['assigned_name'] : 'Self'); ?></td>
                                                <td><?php echo !empty($t['followup_date']) ? date('d-M-Y', strtotime($t['followup_date'])) : '-'; ?></td>
                                                <td><?php echo htmlspecialchars($t['followup_time'] ?? '-'); ?></td>
                                                <td>
                                                    <span class="label label-info"><?php echo htmlspecialchars($t['status'] ?? 'Pending'); ?></span>
                                                </td>
                                                <td><?php echo htmlspecialchars($t['remark'] ?? '-'); ?></td>
                                                <td style="text-align:center;">
                                                    <a href="<?php echo base_url(); ?>manager/tasks/view/<?php echo $t['id']; ?>" class="btn btn-xs btn-default" style="font-weight:600; border:1px solid #000000; color:#000000;" title="View Task Details">
                                                        <i class="fa fa-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-info"> No Data Found </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
