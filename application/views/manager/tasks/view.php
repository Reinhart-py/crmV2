<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<?php
$deptDisplay = !empty($task['a_department']) ? $task['a_department'] : (!empty($task['category']) ? $task['category'] : 'Admin');
$isAccounts = ($deptDisplay === 'Accounts' || strpos($deptDisplay, 'Accounts') !== false);
$isTelecom = ($task['task_scope'] === 'external' && ($task['business_category'] === 'Telecom' || !empty($task['p_cat'])));
?>

<div class="content-wrapper px-4">
    <section class="content-header">
        <h1><?php echo htmlspecialchars($info['page_heading']); ?></h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo ($task['task_scope'] === 'external') ? base_url().'manager/tasks/external' : base_url().'manager/tasks/internal'; ?>">Tasks</a></li>
            <li class="active">View</li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-default" style="border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div class="box-header with-border">
                <div class="pull-left">
                    <a href="<?php echo ($task['task_scope'] === 'external') ? base_url().'manager/tasks/external' : base_url().'manager/tasks/internal'; ?>" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to Tasks
                    </a>
                </div>
                <div class="pull-right">
                    <?php if(!empty($can_edit)){ ?>
                    <a href="<?php echo base_url(); ?>manager/tasks/edit/<?php echo $task['id']; ?>" class="btn btn-primary btn-sm" style="background:#2563eb; border-color:#2563eb;">
                        <i class="fa fa-pencil"></i> Edit Task
                    </a>
                    <?php } ?>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6 col-xs-12">
                        <table class="table table-bordered table-striped" style="font-size: 13px;">
                            <tr>
                                <th style="width:35%;">Task ID</th>
                                <td><strong><?php echo !empty($task['task_id']) ? htmlspecialchars($task['task_id']) : '#' . $task['id']; ?></strong></td>
                            </tr>
                            <tr>
                                <th>Task Scope</th>
                                <td>
                                    <?php if($task['task_scope'] === 'external'){ ?>
                                        <span class="label label-primary"><i class="fa fa-users"></i> External (Client) Task</span>
                                    <?php } else { ?>
                                        <span class="label label-default"><i class="fa fa-building"></i> Internal Task</span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Task Title</th>
                                <td><strong><?php echo htmlspecialchars($task['customer']); ?></strong></td>
                            </tr>
                            <?php if($task['task_scope'] === 'external' && !empty($task['client_company'])){ ?>
                            <tr>
                                <th>Client Company</th>
                                <td><?php echo htmlspecialchars($task['client_company']); ?></td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <th>POC Name</th>
                                <td><?php echo htmlspecialchars($task['poc']); ?></td>
                            </tr>
                            <tr>
                                <th>Contact Phone</th>
                                <td><?php echo htmlspecialchars($task['contact1']); ?></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td><?php echo htmlspecialchars($task['email']); ?></td>
                            </tr>
                            <tr>
                                <th>Department</th>
                                <td><span class="label label-default" style="font-size:12px; font-weight:600;"><?php echo htmlspecialchars($deptDisplay); ?></span></td>
                            </tr>
                            <tr>
                                <th>Assigned To</th>
                                <td><?php echo !empty($task['assigned_name']) ? htmlspecialchars($task['assigned_name']) : (!empty($task['assigned_to']) ? htmlspecialchars($task['assigned_to']) : 'Unassigned'); ?></td>
                            </tr>
                            <tr>
                                <th>Created By</th>
                                <td><?php echo !empty($task['creator_name']) ? htmlspecialchars($task['creator_name']) : '-'; ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6 col-xs-12">
                        <table class="table table-bordered table-striped" style="font-size: 13px;">
                            <tr>
                                <th style="width:35%;">Status</th>
                                <td>
                                    <?php
                                        $sc = 'label-info';
                                        if($task['status'] == 'Completed') $sc = 'label-success';
                                        elseif($task['status'] == 'Closed') $sc = 'label-default';
                                        elseif($task['status'] == 'In Progress') $sc = 'label-warning';
                                    ?>
                                    <span class="label <?php echo $sc; ?>" style="font-size:12px;"><?php echo htmlspecialchars($task['status']); ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th>Priority</th>
                                <td><?php echo !empty($task['dnd_status']) ? htmlspecialchars($task['dnd_status']) : 'Normal'; ?></td>
                            </tr>
                            <tr>
                                <th>Schedule Type</th>
                                <td>
                                    <?php if(!empty($task['is_recurring'])){ ?>
                                        <span class="label label-primary"><i class="fa fa-refresh"></i> Recurring (<?php echo htmlspecialchars($task['recurrence_freq']); ?>)</span>
                                        <?php if(!empty($task['next_occurrence'])){ ?>
                                            <div style="font-size:11px; color:#2563eb; margin-top:3px;">
                                                Next run: <strong><?php echo simple_date($task['next_occurrence']); ?></strong>
                                            </div>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <span class="label label-default">One-Time Task</span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Follow-up Date & Time</th>
                                <td>
                                    <?php echo (!empty($task['followup_date']) && $task['followup_date'] != '0000-00-00') ? simple_date($task['followup_date']) : '-'; ?>
                                    <?php if(!empty($task['followup_time'])) echo ' ' . htmlspecialchars($task['followup_time']); ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Close / Due Date</th>
                                <td><?php echo (!empty($task['close_date']) && $task['close_date'] != '0000-00-00') ? simple_date($task['close_date']) : '-'; ?></td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td><?php echo !empty($task['created']) ? simple_date($task['created']) : '-'; ?></td>
                            </tr>
                            <tr>
                                <th>Modified At</th>
                                <td><?php echo !empty($task['modified']) ? simple_date($task['modified']) : '-'; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <?php if($isAccounts){ ?>
                <div class="row" style="margin-top: 15px;">
                    <div class="col-xs-12">
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px 18px;">
                            <h4 style="margin-top: 0; margin-bottom: 12px; font-size: 14px; font-weight: 600; color: #0f172a;">
                                <i class="fa fa-calculator" style="margin-right: 6px;"></i> Accounts & Invoicing
                            </h4>
                            <div class="row" style="font-size: 13px;">
                                <div class="col-md-3 col-sm-6">
                                    <span style="color:#64748b;">Invoiced:</span><br>
                                    <?php if(!empty($task['invoiced']) && $task['invoiced'] === 'Yes'){ ?>
                                        <span class="label label-success" style="font-size:11px;">Invoiced</span>
                                    <?php } else { ?>
                                        <span class="label label-warning" style="font-size:11px; background:#fef3c7; color:#92400e; border:1px solid #fde68a;">To Be Invoiced</span>
                                    <?php } ?>
                                </div>
                                <?php if(!empty($task['invoiced']) && $task['invoiced'] === 'Yes'){ ?>
                                <div class="col-md-3 col-sm-6">
                                    <span style="color:#64748b;">Invoice Ref:</span><br>
                                    <strong><?php echo !empty($task['invoice_ref']) ? htmlspecialchars($task['invoice_ref']) : '-'; ?></strong>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <span style="color:#64748b;">Invoice Date:</span><br>
                                    <strong><?php echo (!empty($task['invoice_date']) && $task['invoice_date'] != '0000-00-00') ? simple_date($task['invoice_date']) : '-'; ?></strong>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <span style="color:#64748b;">Invoice Amount:</span><br>
                                    <strong><?php echo !empty($task['invoice_amount']) ? number_format($task['invoice_amount'], 2) : '-'; ?></strong>
                                </div>
                                <div class="col-md-3 col-sm-6" style="margin-top:8px;">
                                    <span style="color:#64748b;">Invoice Value:</span><br>
                                    <strong><?php echo !empty($task['invoice_value']) ? number_format($task['invoice_value'], 2) : '-'; ?></strong>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <?php if($isTelecom){ ?>
                <div class="row" style="margin-top: 15px;">
                    <div class="col-xs-12">
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px 18px;">
                            <h4 style="margin-top: 0; margin-bottom: 12px; font-size: 14px; font-weight: 600; color: #0f172a;">
                                <i class="fa fa-signal" style="margin-right: 6px;"></i> Telecom Connection Details
                            </h4>
                            <div class="row" style="font-size: 13px;">
                                <div class="col-md-3 col-sm-6">
                                    <span style="color:#64748b;">Connection Type:</span><br>
                                    <span class="label label-primary" style="font-size:11px;">New Connection</span>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <span style="color:#64748b;">Product Category:</span><br>
                                    <strong><?php echo !empty($task['p_cat']) ? htmlspecialchars($task['p_cat']) : '-'; ?></strong>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <span style="color:#64748b;">Quantity:</span><br>
                                    <strong><?php echo !empty($task['qty']) ? (int)$task['qty'] : '1'; ?></strong>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <span style="color:#64748b;">Monthly Recurring Charge (MRC):</span><br>
                                    <strong><?php echo !empty($task['mrc']) ? number_format($task['mrc'], 2) : '-'; ?></strong>
                                </div>
                                <?php if(!empty($task['p_details'])){ ?>
                                <div class="col-xs-12" style="margin-top:10px;">
                                    <span style="color:#64748b;">Plan & Connection Details:</span><br>
                                    <strong><?php echo htmlspecialchars($task['p_details']); ?></strong>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <div class="row" style="margin-top: 15px;">
                    <div class="col-xs-12">
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px 18px;">
                            <h5 style="margin-top: 0; font-weight: 600; color: #334155;">Task Remarks / Instructions</h5>
                            <p style="margin-bottom: 0; white-space: pre-wrap; font-size: 13px; color: #1e293b;"><?php echo !empty($task['remark']) ? htmlspecialchars($task['remark']) : 'No remarks provided.'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
