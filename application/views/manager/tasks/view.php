<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
    <section class="content-header">
        <h1><?php echo htmlspecialchars($info['page_heading']); ?></h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url(); ?>manager/tasks">Tasks</a></li>
            <li class="active">View</li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border">
                <div class="pull-left">
                    <a href="<?php echo base_url(); ?>manager/tasks" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Back to Tasks</a>
                </div>
                <div class="pull-right">
                    <a href="<?php echo base_url(); ?>manager/tasks/edit/<?php echo $task['id']; ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit Task</a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6 col-xs-12">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th style="width:35%;">Task ID</th>
                                <td><strong><?php echo !empty($task['task_id']) ? htmlspecialchars($task['task_id']) : '#' . $task['id']; ?></strong></td>
                            </tr>
                            <tr>
                                <th>Customer Name</th>
                                <td><?php echo htmlspecialchars($task['customer']); ?></td>
                            </tr>
                            <tr>
                                <th>POC Name</th>
                                <td><?php echo htmlspecialchars($task['poc']); ?></td>
                            </tr>
                            <tr>
                                <th>Contact Number</th>
                                <td><?php echo htmlspecialchars($task['contact1']); ?></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td><?php echo htmlspecialchars($task['email']); ?></td>
                            </tr>
                            <tr>
                                <th>Category</th>
                                <td><?php echo htmlspecialchars($task['category']); ?></td>
                            </tr>
                            <tr>
                                <th>Business Category</th>
                                <td><?php echo htmlspecialchars($task['business_category']); ?></td>
                            </tr>
                            <tr>
                                <th>Assigned Authority</th>
                                <td><?php echo !empty($task['assigned_name']) ? htmlspecialchars($task['assigned_name']) : (!empty($task['assigned_to']) ? htmlspecialchars($task['assigned_to']) : 'Self'); ?></td>
                            </tr>
                            <tr>
                                <th>Created By</th>
                                <td><?php echo !empty($task['creator_name']) ? htmlspecialchars($task['creator_name']) : '-'; ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6 col-xs-12">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th style="width:35%;">Status</th>
                                <td><span class="label label-info" style="font-size:12px;"><?php echo htmlspecialchars($task['status']); ?></span></td>
                            </tr>
                            <tr>
                                <th>Follow-up Date</th>
                                <td><?php echo (!empty($task['followup_date']) && $task['followup_date'] != '0000-00-00') ? simple_date($task['followup_date']) : '-'; ?></td>
                            </tr>
                            <tr>
                                <th>Follow-up Time</th>
                                <td><?php echo !empty($task['followup_time']) ? htmlspecialchars($task['followup_time']) : '-'; ?></td>
                            </tr>
                            <tr>
                                <th>Close / Due Date</th>
                                <td><?php echo (!empty($task['close_date']) && $task['close_date'] != '0000-00-00') ? simple_date($task['close_date']) : '-'; ?></td>
                            </tr>
                            <tr>
                                <th>Location / City</th>
                                <td><?php echo htmlspecialchars($task['city'] . ' ' . $task['location']); ?></td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td><?php echo htmlspecialchars($task['address']); ?></td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td><?php echo !empty($task['created']) ? simple_date($task['created']) : '-'; ?></td>
                            </tr>
                            <tr>
                                <th>Modified At</th>
                                <td><?php echo !empty($task['modified']) ? simple_date($task['modified']) : '-'; ?></td>
                            </tr>
                            <tr>
                                <th>Remarks / Notes</th>
                                <td><?php echo nl2br(htmlspecialchars($task['remark'])); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
