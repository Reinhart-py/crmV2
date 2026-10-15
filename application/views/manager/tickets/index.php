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
                                        <input type="text" class="input-md form-control datepicker" name="d1"
                                            placeholder="Create From"
                                            value="<?php if(isset($_GET['d1']) && (!empty($_GET['d1']))){ echo htmlspecialchars($_GET['d1']); } ?>"
                                            autocomplete="off" />
                                        <span class="input-group-addon">To</span>
                                        <input type="text" class="input-md form-control datepicker" name="d2"
                                            placeholder="Create To"
                                            value="<?php if(isset($_GET['d2']) && (!empty($_GET['d2']))){ echo htmlspecialchars($_GET['d2']); } ?>"
                                            autocomplete="off" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <select class="form-control" name="status">
                                        <option value="">All Statuses</option>
                                        <option value="Open" <?php if(isset($_GET['status']) && $_GET['status']=='Open'){ echo 'selected'; } ?>>Open</option>
                                        <option value="In Progress" <?php if(isset($_GET['status']) && $_GET['status']=='In Progress'){ echo 'selected'; } ?>>In Progress</option>
                                        <option value="Resolved" <?php if(isset($_GET['status']) && $_GET['status']=='Resolved'){ echo 'selected'; } ?>>Resolved</option>
                                        <option value="Closed" <?php if(isset($_GET['status']) && $_GET['status']=='Closed'){ echo 'selected'; } ?>>Closed</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <input type="text" class="input-md form-control" name="search" placeholder="Search Tickets"
                                        value="<?php if(isset($_GET['search']) && (!empty($_GET['search']))){ echo htmlspecialchars($_GET['search']); } ?>"
                                        autocomplete="off" />
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Search</button>
                                    <a href="<?php echo base_url(); ?>manager/tickets" class="btn btn-danger">Reset</a>
                                </div>
                            </form>
                        </div>
                        <div class="x_col-sm-auto text-center pt-4 pt-md-0">
                            <div>
                                <a href="<?php echo base_url(); ?>manager/tickets/add" class="btn btn-success"> Raise Ticket</a>
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
                                        <th>Ticket #</th>
                                        <th>Subject</th>
                                        <th>Category</th>
                                        <th>Priority</th>
                                        <th>Raised By</th>
                                        <th>Created Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(isset($tickets) && !empty($tickets)) { 
                                        foreach($tickets as $t) { ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($t['ticket_number']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($t['subject']); ?></td>
                                        <td><?php echo htmlspecialchars($t['category']); ?></td>
                                        <td>
                                            <?php 
                                                $pr = strtolower($t['priority']);
                                                $pr_badge = 'label-info';
                                                if($pr == 'urgent' || $pr == 'high') $pr_badge = 'label-danger';
                                                elseif($pr == 'medium') $pr_badge = 'label-warning';
                                            ?>
                                            <span class="label <?php echo $pr_badge; ?>"><?php echo htmlspecialchars($t['priority']); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars(!empty($t['creator_name']) ? $t['creator_name'] : 'User'); ?></td>
                                        <td><?php echo date('d-M-Y H:i', strtotime($t['created_at'])); ?></td>
                                        <td>
                                            <?php 
                                                $st = strtolower($t['status']);
                                                $st_badge = 'label-default';
                                                if($st == 'open') $st_badge = 'label-primary';
                                                elseif($st == 'in progress') $st_badge = 'label-warning';
                                                elseif($st == 'resolved') $st_badge = 'label-success';
                                                elseif($st == 'closed') $st_badge = 'label-danger';
                                            ?>
                                            <span class="label <?php echo $st_badge; ?>"><?php echo htmlspecialchars($t['status']); ?></span>
                                        </td>
                                        <td>
                                            <div class="btn-group" style="min-width: 85px;">
                                                <button type="button" class="btn btn-sm btn-warning">Action</button>
                                                <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="<?php echo base_url(); ?>manager/tickets/view/<?php echo $t['id']; ?>"><i class="fa fa-eye"></i> View Ticket</a></li>
                                                    <?php if($can_manage_tickets) { ?>
                                                        <?php if($st != 'in progress') { ?>
                                                        <li><a href="<?php echo base_url(); ?>manager/tickets/status/<?php echo $t['id']; ?>/in_progress"><i class="fa fa-clock-o"></i> Mark In Progress</a></li>
                                                        <?php } ?>
                                                        <?php if($st != 'resolved') { ?>
                                                        <li><a href="<?php echo base_url(); ?>manager/tickets/status/<?php echo $t['id']; ?>/resolved"><i class="fa fa-check"></i> Mark Resolved</a></li>
                                                        <?php } ?>
                                                        <?php if($st != 'closed') { ?>
                                                        <li><a href="<?php echo base_url(); ?>manager/tickets/close/<?php echo $t['id']; ?>"><i class="fa fa-times"></i> Close Ticket</a></li>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } } else { ?>
                                    <tr>
                                        <td colspan="8" style="text-align:center; padding: 25px; color: #777;">
                                            No Ticket Found
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
