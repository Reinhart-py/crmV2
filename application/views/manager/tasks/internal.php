<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
    <section class="content-header">
        <h1><?php echo htmlspecialchars($info['page_heading']); ?></h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box p-4" style="border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 20px;">
                    <div class="x_row">
                        <div class="x_col">
                            <form class="form-inline head_filter form-inline-mob" method="get">
                                <div class="form-group">
                                    <div class="input-daterange input-group" id="datepicker" style="width: 100%;">
                                        <input type="text" class="input-md form-control datepicker" name="d1"
                                            placeholder="From"
                                            value="<?php if(isset($_GET['d1']) && (!empty($_GET['d1']))){ echo htmlspecialchars($_GET['d1']); } ?>"
                                            autocomplete="off" />
                                        <span class="input-group-addon">To</span>
                                        <input type="text" class="input-md form-control datepicker" name="d2"
                                            placeholder="To"
                                            value="<?php if(isset($_GET['d2']) && (!empty($_GET['d2']))){ echo htmlspecialchars($_GET['d2']); } ?>"
                                            autocomplete="off" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <select class="form-control" name="department">
                                        <option value="">All Departments</option>
                                        <option value="Procurement" <?php if(isset($_GET['department']) && $_GET['department']=='Procurement'){ echo 'selected'; } ?>>Procurement</option>
                                        <option value="Sales" <?php if(isset($_GET['department']) && $_GET['department']=='Sales'){ echo 'selected'; } ?>>Sales</option>
                                        <option value="HR" <?php if(isset($_GET['department']) && $_GET['department']=='HR'){ echo 'selected'; } ?>>HR</option>
                                        <option value="Admin" <?php if(isset($_GET['department']) && $_GET['department']=='Admin'){ echo 'selected'; } ?>>Admin</option>
                                        <option value="IT" <?php if(isset($_GET['department']) && $_GET['department']=='IT'){ echo 'selected'; } ?>>IT</option>
                                        <option value="Accounts" <?php if(isset($_GET['department']) && $_GET['department']=='Accounts'){ echo 'selected'; } ?>>Accounts</option>
                                        <option value="Operations" <?php if(isset($_GET['department']) && $_GET['department']=='Operations'){ echo 'selected'; } ?>>Operations</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <select class="form-control" name="invoiced">
                                        <option value="">All Invoicing</option>
                                        <option value="yes" <?php if(isset($_GET['invoiced']) && $_GET['invoiced']=='yes'){ echo 'selected'; } ?>>Invoiced</option>
                                        <option value="no" <?php if(isset($_GET['invoiced']) && $_GET['invoiced']=='no'){ echo 'selected'; } ?>>To Be Invoiced</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <select class="form-control" name="status">
                                        <option value="">All Statuses</option>
                                        <option value="Open" <?php if(isset($_GET['status']) && $_GET['status']=='Open'){ echo 'selected'; } ?>>Open</option>
                                        <option value="In Progress" <?php if(isset($_GET['status']) && $_GET['status']=='In Progress'){ echo 'selected'; } ?>>In Progress</option>
                                        <option value="Completed" <?php if(isset($_GET['status']) && $_GET['status']=='Completed'){ echo 'selected'; } ?>>Completed</option>
                                        <option value="Closed" <?php if(isset($_GET['status']) && $_GET['status']=='Closed'){ echo 'selected'; } ?>>Closed</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <select class="form-control" name="assigned_to">
                                        <option value="">All Assignees</option>
                                        <?php if(!empty($u_list)){ foreach($u_list as $usr){ ?>
                                            <option value="<?php echo $usr['id']; ?>" <?php if(isset($_GET['assigned_to']) && $_GET['assigned_to']==$usr['id']){ echo 'selected'; } ?>><?php echo htmlspecialchars($usr['name']); ?></option>
                                        <?php }} ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <input type="text" class="input-md form-control" name="search" placeholder="Search task, ID, remark"
                                        value="<?php if(isset($_GET['search']) && (!empty($_GET['search']))){ echo htmlspecialchars($_GET['search']); } ?>"
                                        autocomplete="off" />
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary" style="background:#2563eb; border-color:#2563eb;">Search</button>
                                    <a href="<?php echo base_url(); ?>manager/tasks/internal" class="btn btn-default">Reset</a>
                                </div>
                            </form>
                        </div>
                        <div class="x_col-sm-auto text-center pt-4 pt-md-0">
                            <?php if(!empty($can_create)){ ?>
                            <div>
                                <a href="<?php echo base_url(); ?>manager/tasks/add?scope=internal" class="btn btn-primary" style="background:#2563eb; border-color:#2563eb;">
                                    <i class="fa fa-plus"></i> Add Internal Task
                                </a>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="box" style="border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div class="box-body table-responsive">
                        <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?>
                        <?php echo get_message($this->session->flashdata('message'),'message'); ?>

                        <table id="example_lead" class="table table-hover table-bordered table-striped" style="width:100%; font-size:13px;">
                            <thead>
                                <tr style="background:#f8fafc; color:#334155;">
                                    <th>Date</th>
                                    <th>Department</th>
                                    <th>Task</th>
                                    <th>Informed By</th>
                                    <th>Assigned To</th>
                                    <th>Completion By</th>
                                    <th>Due Deadline</th>
                                    <th>Status</th>
                                    <th>Remarks</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(isset($list) && !empty($list)) { 
                                    foreach($list as $li) { 
                                        $statusClass = 'label-info';
                                        if($li['status'] == 'Completed') { $statusClass = 'label-success'; }
                                        elseif($li['status'] == 'Closed') { $statusClass = 'label-default'; }
                                        elseif($li['status'] == 'In Progress') { $statusClass = 'label-warning'; }
                                        $deptDisplay = !empty($li['a_department']) ? $li['a_department'] : (!empty($li['category']) ? $li['category'] : 'Admin');
                                ?>
                                <tr>
                                    <td style="white-space:nowrap;"><?php echo !empty($li['created']) ? simple_date($li['created']) : '-'; ?></td>
                                    <td>
                                        <span class="label label-default" style="font-weight:600; font-size:11px; background:#e2e8f0; color:#334155;">
                                            <?php echo htmlspecialchars($deptDisplay); ?>
                                        </span>
                                        <?php if($deptDisplay === 'Accounts' || (!empty($li['a_department']) && $li['a_department'] === 'Accounts')) { ?>
                                            <div style="margin-top:4px;">
                                                <?php if(!empty($li['invoiced'])){ ?>
                                                    <span class="label label-success" style="font-size:10px; font-weight:normal;">Invoiced<?php if(!empty($li['invoice_ref'])) echo ': '.htmlspecialchars($li['invoice_ref']); ?></span>
                                                <?php } else { ?>
                                                    <span class="label label-warning" style="font-size:10px; font-weight:normal; background:#fef3c7; color:#92400e; border:1px solid #fde68a;">To Be Invoiced</span>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo base_url(); ?>manager/tasks/view/<?php echo $li['id']; ?>" style="font-weight:600; color:#2563eb;">
                                            <?php echo htmlspecialchars($li['customer']); ?>
                                        </a>
                                        <?php if(!empty($li['task_id'])){ ?>
                                            <div style="font-size:11px; color:#64748b;"><?php echo htmlspecialchars($li['task_id']); ?></div>
                                        <?php } ?>
                                        <?php if(!empty($li['is_recurring'])){ ?>
                                            <span class="label label-primary" style="font-size:10px; padding:2px 5px;"><i class="fa fa-refresh"></i> <?php echo htmlspecialchars($li['recurrence_freq']); ?></span>
                                        <?php } ?>
                                    </td>
                                    <td><?php echo !empty($li['creator_name']) ? htmlspecialchars($li['creator_name']) : 'System'; ?></td>
                                    <td><?php echo !empty($li['assigned_name']) ? htmlspecialchars($li['assigned_name']) : 'Unassigned'; ?></td>
                                    <td style="white-space:nowrap;"><?php echo !empty($li['close_date']) && $li['close_date'] != '0000-00-00' ? simple_date($li['close_date']) : '-'; ?></td>
                                    <td style="white-space:nowrap;">
                                        <?php if(!empty($li['followup_date']) && $li['followup_date'] != '0000-00-00'){ ?>
                                            <div><?php echo simple_date($li['followup_date']); ?></div>
                                            <?php if(!empty($li['followup_time'])){ ?>
                                                <small style="color:#64748b;"><?php echo htmlspecialchars($li['followup_time']); ?></small>
                                            <?php } ?>
                                        <?php } else { echo '-'; } ?>
                                    </td>
                                    <td><span class="label <?php echo $statusClass; ?>"><?php echo htmlspecialchars($li['status']); ?></span></td>
                                    <td style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?php echo htmlspecialchars($li['remark']); ?>">
                                        <?php echo htmlspecialchars($li['remark']); ?>
                                    </td>
                                    <td style="white-space:nowrap;">
                                        <div class="btn-group">
                                            <a href="<?php echo base_url(); ?>manager/tasks/view/<?php echo $li['id']; ?>" class="btn btn-xs btn-default" title="View"><i class="fa fa-eye"></i></a>
                                            <?php if(!empty($can_create)){ ?>
                                            <a href="<?php echo base_url(); ?>manager/tasks/edit/<?php echo $li['id']; ?>" class="btn btn-xs btn-default" title="Edit"><i class="fa fa-pencil"></i></a>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php } } else { ?>
                                <tr>
                                    <td colspan="10" class="text-center" style="padding:24px; color:#64748b;">No internal tasks found.</td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
