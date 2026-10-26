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
                                    <input type="text" class="input-md form-control" name="search" placeholder="Search Reminder..." value="<?php echo htmlspecialchars($this->input->get('search') ?? ''); ?>" autocomplete="off" />
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
                                    <a href="<?php echo htmlspecialchars($reset_url ?? base_url('manager/kam/reminders')); ?>" class="btn btn-danger"> Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="box-body">
                        <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
                        <?php echo get_message($this->session->flashdata('message'), 'message'); ?>
                        <?php if (isset($reminders) && !empty($reminders)) { ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="example_reminders">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Reminder</th>
                                            <th>Client</th>
                                            <th>Due Time</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($reminders as $idx => $r) { ?>
                                            <tr>
                                                <td><?php echo $idx + 1; ?></td>
                                                <td><?php echo htmlspecialchars($r['title'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($r['client_name'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($r['due_time'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($r['status'] ?? ''); ?></td>
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
