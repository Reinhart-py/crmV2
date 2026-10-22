<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

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
                                    <div class="input-daterange input-group" style="width: 100%;">
                                        <select class="form-control" name="type">
                                            <option value="">Time Period</option>
                                            <option <?php if(isset($_GET['type']) && $_GET['type']==1){ echo "selected"; } ?> value="1">All Missed</option>
                                            <option <?php if(isset($_GET['type']) && $_GET['type']==2){ echo "selected"; } ?> value="2">Last 7 Days</option>
                                            <option <?php if(isset($_GET['type']) && $_GET['type']==3){ echo "selected"; } ?> value="3">Today</option>
                                            <option <?php if(isset($_GET['type']) && $_GET['type']==4){ echo "selected"; } ?> value="4">Next 7 Days</option>
                                            <option <?php if(isset($_GET['type']) && $_GET['type']==5){ echo "selected"; } ?> value="5">All Futures</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <input type="text" class="input-md form-control datepicker" name="d1" placeholder="From Date"
                                        value="<?php if(isset($_GET['d1']) && (!empty($_GET['d1']))){ echo htmlspecialchars($_GET['d1']); } ?>" autocomplete="off"/>
                                    <span class="input-group-addon">To</span>
                                    <input type="text" class="input-md form-control datepicker" name="d2" placeholder="To Date"
                                        value="<?php if(isset($_GET['d2']) && (!empty($_GET['d2']))){ echo htmlspecialchars($_GET['d2']); } ?>" autocomplete="off"/>
                                </div>

                                <div class="form-group">
                                    <select class="form-control" name="status">
                                        <option value="">Status</option>
                                        <option value="Open" <?php if(isset($_GET['status']) && $_GET['status']=='Open'){ echo 'selected'; } ?>>Open</option>
                                        <option value="In Progress" <?php if(isset($_GET['status']) && $_GET['status']=='In Progress'){ echo 'selected'; } ?>>In Progress</option>
                                        <option value="Pending" <?php if(isset($_GET['status']) && $_GET['status']=='Pending'){ echo 'selected'; } ?>>Pending</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <input type="text" class="input-md form-control" name="search" placeholder="Search Tasks"
                                        value="<?php if(isset($_GET['search']) && (!empty($_GET['search']))){ echo htmlspecialchars($_GET['search']); } ?>" autocomplete="off"/>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Search</button>
                                    <a href="<?php echo base_url(); ?>manager/tasks/followups" class="btn btn-danger">Reset</a>
                                </div>
                            </form>
                        </div>
                        <div class="x_col-sm-auto text-center pt-4 pt-md-0">
                            <div>
                                <a href="<?php echo base_url(); ?>manager/tasks/followups?type=1" class="btn btn-danger btn-sm">All Missed</a>
                                <a href="<?php echo base_url(); ?>manager/tasks/followups?type=2" class="btn btn-warning btn-sm">Last 7 Days</a>
                                <a href="<?php echo base_url(); ?>manager/tasks/followups?type=3" class="btn btn-primary btn-sm">Today</a>
                                <a href="<?php echo base_url(); ?>manager/tasks/followups?type=4" class="btn btn-info btn-sm">Next 7 Days</a>
                                <a href="<?php echo base_url(); ?>manager/tasks/followups?type=5" class="btn btn-default btn-sm">All Future</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="box-body">
                        <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?>
                        <?php echo get_message($this->session->flashdata('message'),'message'); ?>

                        <div>
                            <table id="example_lead" class="table table-hover table-bordered table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">S.N.</th>
                                        <th>ID</th>
                                        <th>Task Title / Customer</th>
                                        <th>Assigned To</th>
                                        <th>Next FU Date</th>
                                        <th>FU Time</th>
                                        <th>Status</th>
                                        <th>Remark</th>
                                        <th style="width: 100px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(isset($list) && !empty($list)) { 
                                        foreach($list as $k => $li) { ?>
                                    <tr>
                                        <td><?php echo ($k + 1); ?></td>
                                        <td><a href="<?php echo base_url(); ?>manager/tasks/view/<?php echo $li['id']; ?>"><strong><?php echo !empty($li['task_id']) ? htmlspecialchars($li['task_id']) : $li['id']; ?></strong></a></td>
                                        <td><a href="<?php echo base_url(); ?>manager/tasks/view/<?php echo $li['id']; ?>"><?php echo isset($li['customer']) ? htmlspecialchars($li['customer']) : ''; ?></a></td>
                                        <td><?php echo !empty($li['assigned_name']) ? htmlspecialchars($li['assigned_name']) : (!empty($li['assigned_to']) ? htmlspecialchars($li['assigned_to']) : 'Self'); ?></td>
                                        <td><span class="no_show"><?php echo date('Ymd', strtotime($li['followup_date'])); ?></span><?php echo simple_date($li['followup_date']); ?></td>
                                        <td><?php echo isset($li['followup_time']) ? htmlspecialchars($li['followup_time']) : '-'; ?></td>
                                        <td><span class="label label-info"><?php echo isset($li['status']) ? htmlspecialchars($li['status']) : 'Pending'; ?></span></td>
                                        <td><?php echo isset($li['remark']) ? htmlspecialchars($li['remark']) : ''; ?></td>
                                        <td>
                                            <div class="btn-group" style="min-width: 85px;">
                                                <button type="button" class="btn btn-sm btn-warning">Action</button>
                                                <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                                    <li><a href="<?php echo base_url(); ?>manager/tasks/view/<?php echo $li['id']; ?>" data-toggle="tooltip" title="View">Preview</a></li>
                                                    <li><a href="<?php echo base_url(); ?>manager/tasks/edit/<?php echo $li['id']; ?>" data-toggle="tooltip" title="Edit">Edit</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } } else { ?>
                                    <tr>
                                        <td colspan="9" style="text-align:center; padding: 25px; color: #777;">
                                            No Task Found
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
