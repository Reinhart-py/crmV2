<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper px-4">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> <?php echo $info['page_heading']; ?> </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box p-4">
                    <div class="x_row">
                        <div class="x_col">
                            <form class="form-inline head_filter form-inline-mob" method="get">
                                <div class="form-group">
                                    <div class="input-daterange input-group" id="datepicker" style="width: 100%;">
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
                                    <button type="submit" class="btn btn-success">Search</button>
                                    <a href="<?php echo base_url(); ?>manager/tasks/followups" class="btn btn-danger">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="box-body">
                        <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?>
                        <?php echo get_message($this->session->flashdata('message'),'message'); ?>

                        <div>
                            <table class="table table-hover table-bordered table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Task / Customer</th>
                                        <th>Follow Up Date</th>
                                        <th>Follow Up Time</th>
                                        <th>Remark</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(isset($list) && !empty($list)) { 
                                        foreach($list as $li) { ?>
                                    <tr>
                                        <td><a href="<?php echo base_url(); ?>manager/tasks/view/<?php echo $li['id']; ?>"><strong><?php echo !empty($li['task_id']) ? htmlspecialchars($li['task_id']) : $li['id']; ?></strong></a></td>
                                        <td><a href="<?php echo base_url(); ?>manager/tasks/view/<?php echo $li['id']; ?>"><?php echo isset($li['customer']) ? htmlspecialchars($li['customer']) : ''; ?></a></td>
                                        <td><?php echo isset($li['followup_date']) ? simple_date($li['followup_date']) : '-'; ?></td>
                                        <td><?php echo isset($li['followup_time']) ? htmlspecialchars($li['followup_time']) : '-'; ?></td>
                                        <td><?php echo isset($li['remark']) ? htmlspecialchars($li['remark']) : ''; ?></td>
                                        <td><span class="label label-info"><?php echo isset($li['status']) ? htmlspecialchars($li['status']) : 'Pending'; ?></span></td>
                                        <td>
                                            <a href="<?php echo base_url(); ?>manager/tasks/view/<?php echo $li['id']; ?>" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i> View</a>
                                            <a href="<?php echo base_url(); ?>manager/tasks/edit/<?php echo $li['id']; ?>" class="btn btn-xs btn-warning"><i class="fa fa-edit"></i> Edit</a>
                                        </td>
                                    </tr>
                                    <?php } } else { ?>
                                    <tr>
                                        <td colspan="7" style="text-align:center; padding: 25px; color: #777;">
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
