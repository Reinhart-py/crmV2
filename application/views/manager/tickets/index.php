<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper px-4">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <h1 style="margin: 0; font-size: 22px; font-weight: 700;">
                <i class="fa fa-ticket text-primary"></i> <?php echo htmlspecialchars($info['page_heading']); ?>
            </h1>
            <div>
                <a href="<?php echo base_url(); ?>manager/tickets/add" class="btn btn-success" style="font-weight: 600;">
                    <i class="fa fa-plus-circle"></i> Raise New Ticket
                </a>
                <a href="<?php echo base_url(); ?>manager/tickets/followups" class="btn btn-primary" style="font-weight: 600; margin-left: 5px;">
                    <i class="fa fa-calendar-check-o"></i> Ticket Follow-ups
                </a>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Ticket Metric Stat Cards -->
        <div class="row" style="margin-bottom: 15px;">
            <div class="col-md-2 col-sm-4 col-xs-6">
                <a href="<?php echo base_url(); ?>manager/tickets" style="text-decoration: none; color: inherit;">
                    <div style="background: #ffffff; border: 1px solid #d2d6de; border-left: 4px solid #3c8dbc; border-radius: 4px; padding: 12px 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <div style="font-size: 11px; text-transform: uppercase; color: #666; font-weight: 700;">Total Tickets</div>
                        <div style="font-size: 22px; font-weight: 800; color: #3c8dbc; margin-top: 4px;"><?php echo isset($count_all) ? $count_all : 0; ?></div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <a href="<?php echo base_url(); ?>manager/tickets?status=Open" style="text-decoration: none; color: inherit;">
                    <div style="background: #ffffff; border: 1px solid #d2d6de; border-left: 4px solid #00a65a; border-radius: 4px; padding: 12px 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <div style="font-size: 11px; text-transform: uppercase; color: #666; font-weight: 700;">Open & Assigned</div>
                        <div style="font-size: 22px; font-weight: 800; color: #00a65a; margin-top: 4px;">
                            <?php echo (isset($count_open) ? $count_open : 0) + (isset($count_assigned) ? $count_assigned : 0); ?>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <a href="<?php echo base_url(); ?>manager/tickets?status=In+Progress" style="text-decoration: none; color: inherit;">
                    <div style="background: #ffffff; border: 1px solid #d2d6de; border-left: 4px solid #f39c12; border-radius: 4px; padding: 12px 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <div style="font-size: 11px; text-transform: uppercase; color: #666; font-weight: 700;">In Progress / Pending</div>
                        <div style="font-size: 22px; font-weight: 800; color: #f39c12; margin-top: 4px;">
                            <?php echo (isset($count_in_progress) ? $count_in_progress : 0) + (isset($count_pending) ? $count_pending : 0) + (isset($count_on_hold) ? $count_on_hold : 0); ?>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <a href="<?php echo base_url(); ?>manager/tickets?overdue=1" style="text-decoration: none; color: inherit;">
                    <div style="background: #fff5f5; border: 1px solid #fca5a5; border-left: 4px solid #dd4b39; border-radius: 4px; padding: 12px 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <div style="font-size: 11px; text-transform: uppercase; color: #b91c1c; font-weight: 800;">
                            <i class="fa fa-exclamation-circle"></i> Overdue Tickets
                        </div>
                        <div style="font-size: 22px; font-weight: 800; color: #dd4b39; margin-top: 4px;">
                            <?php echo isset($count_overdue) ? $count_overdue : 0; ?>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <a href="<?php echo base_url(); ?>manager/tickets?status=Resolved" style="text-decoration: none; color: inherit;">
                    <div style="background: #ffffff; border: 1px solid #d2d6de; border-left: 4px solid #00c0ef; border-radius: 4px; padding: 12px 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <div style="font-size: 11px; text-transform: uppercase; color: #666; font-weight: 700;">Resolved</div>
                        <div style="font-size: 22px; font-weight: 800; color: #00c0ef; margin-top: 4px;"><?php echo isset($count_resolved) ? $count_resolved : 0; ?></div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <a href="<?php echo base_url(); ?>manager/tickets?status=Closed" style="text-decoration: none; color: inherit;">
                    <div style="background: #ffffff; border: 1px solid #d2d6de; border-left: 4px solid #6b7280; border-radius: 4px; padding: 12px 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <div style="font-size: 11px; text-transform: uppercase; color: #666; font-weight: 700;">Closed (Verified)</div>
                        <div style="font-size: 22px; font-weight: 800; color: #6b7280; margin-top: 4px;"><?php echo isset($count_closed) ? $count_closed : 0; ?></div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Filter Toolbar -->
        <div class="box box-default p-4" style="border-top: 3px solid #3c8dbc; margin-bottom: 20px;">
            <div class="box-header with-border" style="padding-bottom: 8px;">
                <h3 class="box-title" style="font-size: 14px; font-weight: 700;">
                    <i class="fa fa-filter text-primary"></i> Filter & Search Tickets
                </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body" style="padding-top: 15px;">
                <form method="get" action="<?php echo base_url(); ?>manager/tickets">
                    <div class="row">
                        <!-- Client Type -->
                        <div class="col-md-2 col-sm-4 col-xs-6 form-group">
                            <label style="font-size: 12px; color: #444;">Client Type</label>
                            <select class="form-control input-sm select2" name="client_type">
                                <option value="">All Client Types</option>
                                <option value="Onboarded" <?php if(isset($_GET['client_type']) && $_GET['client_type']=='Onboarded'){ echo 'selected'; } ?>>Onboarded</option>
                                <option value="Non-Onboarded" <?php if(isset($_GET['client_type']) && $_GET['client_type']=='Non-Onboarded'){ echo 'selected'; } ?>>Non-Onboarded</option>
                            </select>
                        </div>

                        <!-- Company Name with Search & Filter -->
                        <div class="col-md-3 col-sm-4 col-xs-6 form-group">
                            <label style="font-size: 12px; color: #444;">Company Name</label>
                            <select class="form-control input-sm select2" name="company" style="width: 100%;">
                                <option value="">All Companies</option>
                                <?php if(!empty($companies_list)){ 
                                    foreach($companies_list as $comp){ ?>
                                        <option value="<?php echo htmlspecialchars($comp['name']); ?>" <?php if(isset($_GET['company']) && $_GET['company']==$comp['name']){ echo 'selected'; } ?>>
                                            <?php echo htmlspecialchars($comp['name']); ?>
                                        </option>
                                <?php } } ?>
                            </select>
                        </div>

                        <!-- Department -->
                        <div class="col-md-2 col-sm-4 col-xs-6 form-group">
                            <label style="font-size: 12px; color: #444;">Department</label>
                            <select class="form-control input-sm select2" name="department">
                                <option value="">All Departments</option>
                                <?php 
                                $depts = array('Sales', 'Back Office', 'Operations', 'IT', 'HR', 'Support', 'Accounts');
                                foreach($depts as $d){ ?>
                                    <option value="<?php echo $d; ?>" <?php if(isset($_GET['department']) && $_GET['department']==$d){ echo 'selected'; } ?>><?php echo $d; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Ticket Type -->
                        <div class="col-md-3 col-sm-4 col-xs-6 form-group">
                            <label style="font-size: 12px; color: #444;">Ticket Type</label>
                            <select class="form-control input-sm select2" name="ticket_type" style="width: 100%;">
                                <option value="">All Ticket Types</option>
                                <?php 
                                $ttypes = array(
                                    'Bug / Technical Issue',
                                    'Feature Request',
                                    'System Access / Permission',
                                    'Billing / Invoice Inquiry',
                                    'Client Query / Onboarding',
                                    'Operational Support',
                                    'Hardware / Equipment',
                                    'General Inquiry',
                                    'Escalation'
                                );
                                foreach($ttypes as $tt){ ?>
                                    <option value="<?php echo $tt; ?>" <?php if(isset($_GET['ticket_type']) && $_GET['ticket_type']==$tt){ echo 'selected'; } ?>><?php echo $tt; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Category -->
                        <div class="col-md-2 col-sm-4 col-xs-6 form-group">
                            <label style="font-size: 12px; color: #444;">Category</label>
                            <select class="form-control input-sm select2" name="category">
                                <option value="">All Categories</option>
                                <?php 
                                $cats = array('Hardware', 'Software', 'Network', 'Lead Management', 'Calling System', 'CRM Access', 'Data Correction', 'Attendance / HR', 'Security / Breach', 'Other');
                                foreach($cats as $c){ ?>
                                    <option value="<?php echo $c; ?>" <?php if(isset($_GET['category']) && $_GET['category']==$c){ echo 'selected'; } ?>><?php echo $c; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Priority -->
                        <div class="col-md-2 col-sm-4 col-xs-6 form-group">
                            <label style="font-size: 12px; color: #444;">Priority</label>
                            <select class="form-control input-sm select2" name="priority">
                                <option value="">All Priorities</option>
                                <option value="Low" <?php if(isset($_GET['priority']) && $_GET['priority']=='Low'){ echo 'selected'; } ?>>Low</option>
                                <option value="Medium" <?php if(isset($_GET['priority']) && $_GET['priority']=='Medium'){ echo 'selected'; } ?>>Medium</option>
                                <option value="High" <?php if(isset($_GET['priority']) && $_GET['priority']=='High'){ echo 'selected'; } ?>>High</option>
                                <option value="Urgent" <?php if(isset($_GET['priority']) && $_GET['priority']=='Urgent'){ echo 'selected'; } ?>>Urgent</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="col-md-2 col-sm-4 col-xs-6 form-group">
                            <label style="font-size: 12px; color: #444;">Ticket Status</label>
                            <select class="form-control input-sm select2" name="status">
                                <option value="">All Statuses</option>
                                <?php 
                                $statuses = array('Open', 'Assigned', 'In Progress', 'Pending', 'On Hold', 'Resolved', 'Closed');
                                foreach($statuses as $st){ ?>
                                    <option value="<?php echo $st; ?>" <?php if(isset($_GET['status']) && $_GET['status']==$st){ echo 'selected'; } ?>><?php echo $st; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Assigned User -->
                        <div class="col-md-2 col-sm-4 col-xs-6 form-group">
                            <label style="font-size: 12px; color: #444;">Assigned User</label>
                            <select class="form-control input-sm select2" name="assigned_user" style="width: 100%;">
                                <option value="">All Assigned Users</option>
                                <?php if(!empty($users_list)){ 
                                    foreach($users_list as $u){ ?>
                                        <option value="<?php echo $u['id']; ?>" <?php if(isset($_GET['assigned_user']) && $_GET['assigned_user']==$u['id']){ echo 'selected'; } ?>>
                                            <?php echo htmlspecialchars($u['name']); ?>
                                        </option>
                                <?php } } ?>
                            </select>
                        </div>

                        <!-- Raised By -->
                        <div class="col-md-2 col-sm-4 col-xs-6 form-group">
                            <label style="font-size: 12px; color: #444;">Raised By</label>
                            <select class="form-control input-sm select2" name="raised_by" style="width: 100%;">
                                <option value="">All Creators</option>
                                <?php if(!empty($users_list)){ 
                                    foreach($users_list as $u){ ?>
                                        <option value="<?php echo $u['id']; ?>" <?php if(isset($_GET['raised_by']) && $_GET['raised_by']==$u['id']){ echo 'selected'; } ?>>
                                            <?php echo htmlspecialchars($u['name']); ?>
                                        </option>
                                <?php } } ?>
                            </select>
                        </div>

                        <!-- Overdue / Deadline Status -->
                        <div class="col-md-2 col-sm-4 col-xs-6 form-group">
                            <label style="font-size: 12px; color: #444;">Deadline Alert</label>
                            <select class="form-control input-sm select2" name="overdue">
                                <option value="">All Deadlines</option>
                                <option value="1" <?php if(isset($_GET['overdue']) && $_GET['overdue']=='1'){ echo 'selected'; } ?>>Overdue Tickets</option>
                                <option value="soon" <?php if(isset($_GET['overdue']) && $_GET['overdue']=='soon'){ echo 'selected'; } ?>>Approaching (&le; 48 hrs)</option>
                            </select>
                        </div>

                        <!-- Keyword Search -->
                        <div class="col-md-2 col-sm-4 col-xs-6 form-group">
                            <label style="font-size: 12px; color: #444;">Keyword Search</label>
                            <input type="text" class="input-sm form-control" name="search" placeholder="ID, Title, Company..."
                                value="<?php if(isset($_GET['search'])){ echo htmlspecialchars($_GET['search']); } ?>" autocomplete="off" />
                        </div>
                    </div>

                    <div class="row">
                        <!-- Date Range -->
                        <div class="col-md-4 col-sm-6 form-group">
                            <label style="font-size: 12px; color: #444;">Created Date Range</label>
                            <div class="input-daterange input-group" style="width: 100%;">
                                <input type="text" class="input-sm form-control datepicker" name="d1" placeholder="From Date"
                                    value="<?php if(isset($_GET['d1'])){ echo htmlspecialchars($_GET['d1']); } ?>" autocomplete="off" />
                                <span class="input-group-addon">To</span>
                                <input type="text" class="input-sm form-control datepicker" name="d2" placeholder="To Date"
                                    value="<?php if(isset($_GET['d2'])){ echo htmlspecialchars($_GET['d2']); } ?>" autocomplete="off" />
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="col-md-8 col-sm-6 form-group text-right" style="padding-top: 24px;">
                            <button type="submit" class="btn btn-primary btn-sm" style="font-weight: 600; padding: 6px 18px;">
                                <i class="fa fa-filter"></i> Apply Filters
                            </button>
                            <a href="<?php echo base_url(); ?>manager/tickets" class="btn btn-default btn-sm" style="font-weight: 600; margin-left: 5px;">
                                <i class="fa fa-refresh"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Ticket Listing Table -->
        <div class="box box-solid">
            <div class="box-body" style="padding: 10px;">
                <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?>
                <?php echo get_message($this->session->flashdata('message'),'message'); ?>

                <?php 
                // Build fast user id => name lookup map
                $user_map = array();
                if(!empty($users_list)){
                    foreach($users_list as $ul){
                        $user_map[$ul['id']] = $ul['name'];
                    }
                }
                $today_str = date('Y-m-d');
                $soon_str = date('Y-m-d', strtotime('+2 days'));
                ?>

                <div class="table-responsive">
                    <table id="example_lead" class="table table-hover table-bordered table-striped" style="width:100%; font-size: 12.5px;">
                        <thead>
                            <tr style="background: #f8f9fa;">
                                <th style="width: 90px;">Ticket #</th>
                                <th>Client & Company</th>
                                <th>Title & Department</th>
                                <th style="width: 80px;">Priority</th>
                                <th style="width: 95px;">Status</th>
                                <th>Assigned To</th>
                                <th>Raised By</th>
                                <th style="width: 140px;">Deadline & Alerts</th>
                                <th style="width: 95px;">Created</th>
                                <th style="width: 80px; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($tickets) && !empty($tickets)) { 
                                foreach($tickets as $t) { 
                                    $st = strtolower(trim($t['status']));
                                    $pr = strtolower(trim($t['priority']));

                                    // Overdue calculation
                                    $is_overdue = false;
                                    $is_due_soon = false;
                                    if(!empty($t['deadline_date']) && !in_array($st, array('resolved', 'closed'))){
                                        if($t['deadline_date'] < $today_str){
                                            $is_overdue = true;
                                        } elseif($t['deadline_date'] <= $soon_str){
                                            $is_due_soon = true;
                                        }
                                    }

                                    // Row highlight for overdue
                                    $row_style = $is_overdue ? 'background-color: #fff1f2;' : '';

                                    // Multi-user assignment badges
                                    $assignees = array();
                                    if(!empty($t['assigned_users'])){
                                        $arr_ids = explode(',', $t['assigned_users']);
                                        foreach($arr_ids as $aid){
                                            $aid = trim($aid);
                                            if(isset($user_map[$aid])){
                                                $assignees[] = $user_map[$aid];
                                            }
                                        }
                                    } elseif(!empty($t['assigned_to']) && isset($user_map[$t['assigned_to']])){
                                        $assignees[] = $user_map[$t['assigned_to']];
                                    }
                            ?>
                            <tr style="<?php echo $row_style; ?>">
                                <!-- Ticket Number -->
                                <td>
                                    <a href="<?php echo base_url(); ?>manager/tickets/view/<?php echo $t['id']; ?>" style="font-weight: 700; color: #1e40af;">
                                        <?php echo htmlspecialchars($t['ticket_number']); ?>
                                    </a>
                                </td>

                                <!-- Client & Company -->
                                <td>
                                    <?php 
                                    $ctype = !empty($t['client_type']) ? $t['client_type'] : 'Onboarded';
                                    $cbadge = ($ctype === 'Onboarded') ? 'label-success' : 'label-warning';
                                    ?>
                                    <span class="label <?php echo $cbadge; ?>" style="font-size: 10px; margin-right: 4px;">
                                        <?php echo htmlspecialchars($ctype); ?>
                                    </span>
                                    <strong style="color: #111;">
                                        <?php echo htmlspecialchars(!empty($t['company_name']) ? $t['company_name'] : (!empty($t['customer']) ? $t['customer'] : '-')); ?>
                                    </strong>
                                </td>

                                <!-- Title, Department & Category -->
                                <td>
                                    <div style="font-weight: 600; color: #1f2937;">
                                        <a href="<?php echo base_url(); ?>manager/tickets/view/<?php echo $t['id']; ?>" style="color: inherit;">
                                            <?php echo htmlspecialchars(!empty($t['ticket_title']) ? $t['ticket_title'] : $t['subject']); ?>
                                        </a>
                                    </div>
                                    <div style="margin-top: 3px; font-size: 11px;">
                                        <?php if(!empty($t['department'])){ ?>
                                            <span class="label label-default" style="background: #e2e8f0; color: #334155; margin-right: 3px;">
                                                <i class="fa fa-building-o"></i> <?php echo htmlspecialchars($t['department']); ?>
                                            </span>
                                        <?php } ?>
                                        <?php if(!empty($t['category'])){ ?>
                                            <span class="label label-default" style="background: #f1f5f9; color: #475569;">
                                                <i class="fa fa-tag"></i> <?php echo htmlspecialchars($t['category']); ?>
                                            </span>
                                        <?php } ?>
                                    </div>
                                </td>

                                <!-- Priority -->
                                <td>
                                    <?php 
                                        $pr_badge = 'label-info';
                                        if($pr == 'urgent') $pr_badge = 'label-danger" style="background: #b91c1c;';
                                        elseif($pr == 'high') $pr_badge = 'label-danger';
                                        elseif($pr == 'medium') $pr_badge = 'label-warning';
                                        elseif($pr == 'low') $pr_badge = 'label-info';
                                    ?>
                                    <span class="label <?php echo $pr_badge; ?>" style="font-size: 11px;">
                                        <?php echo htmlspecialchars($t['priority']); ?>
                                    </span>
                                </td>

                                <!-- Status -->
                                <td>
                                    <?php 
                                        $st_badge = 'label-default';
                                        if($st == 'open') $st_badge = 'label-primary';
                                        elseif($st == 'assigned') $st_badge = 'label-info" style="background: #6366f1;';
                                        elseif($st == 'in progress') $st_badge = 'label-warning" style="background: #d97706;';
                                        elseif($st == 'pending') $st_badge = 'label-info';
                                        elseif($st == 'on hold') $st_badge = 'label-default" style="background: #64748b;';
                                        elseif($st == 'resolved') $st_badge = 'label-success';
                                        elseif($st == 'closed') $st_badge = 'label-default" style="background: #374151;';
                                    ?>
                                    <span class="label <?php echo $st_badge; ?>" style="font-size: 11px;">
                                        <?php echo htmlspecialchars($t['status']); ?>
                                    </span>
                                </td>

                                <!-- Assigned To (Multiple Users) -->
                                <td>
                                    <?php if(!empty($assignees)){ 
                                        foreach($assignees as $aname){ ?>
                                            <span class="label label-default" style="display: inline-block; background: #e0e7ff; color: #3730a3; margin: 1px 2px; font-size: 10.5px; border-radius: 3px; font-weight: 600;">
                                                <i class="fa fa-user"></i> <?php echo htmlspecialchars($aname); ?>
                                            </span>
                                    <?php } } else { ?>
                                        <span style="color: #94a3b8; font-style: italic;">Unassigned</span>
                                    <?php } ?>
                                </td>

                                <!-- Raised By -->
                                <td>
                                    <div style="font-weight: 600; color: #334155;">
                                        <?php echo htmlspecialchars(!empty($t['creator_name']) ? $t['creator_name'] : 'User #'.$t['created_by']); ?>
                                    </div>
                                </td>

                                <!-- Deadline & Alerts -->
                                <td>
                                    <?php if(!empty($t['deadline_date'])){ ?>
                                        <div style="font-weight: 600; color: #1e293b;">
                                            <i class="fa fa-calendar"></i> <?php echo date('d-M-Y', strtotime($t['deadline_date'])); ?>
                                        </div>
                                    <?php } else { ?>
                                        <span style="color: #94a3b8;">No Deadline</span>
                                    <?php } ?>

                                    <?php if($is_overdue){ ?>
                                        <div style="margin-top: 3px;">
                                            <span class="label label-danger" style="background: #dc2626; font-size: 10px; font-weight: 800; letter-spacing: 0.3px; padding: 2px 6px;">
                                                <i class="fa fa-exclamation-triangle"></i> OVERDUE
                                            </span>
                                        </div>
                                    <?php } elseif($is_due_soon){ ?>
                                        <div style="margin-top: 3px;">
                                            <span class="label label-warning" style="background: #f59e0b; color: #000; font-size: 10px; font-weight: 700; padding: 2px 6px;">
                                                <i class="fa fa-clock-o"></i> Due Soon
                                            </span>
                                        </div>
                                    <?php } ?>
                                </td>

                                <!-- Created Date -->
                                <td style="font-size: 11.5px; color: #64748b;">
                                    <?php echo date('d-M-Y', strtotime(!empty($t['created_date']) ? $t['created_date'] : $t['created_at'])); ?>
                                </td>

                                <!-- Actions -->
                                <td style="text-align: center;">
                                    <div class="btn-group" style="min-width: 75px;">
                                        <button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false" style="padding: 4px 10px; font-weight: 600;">
                                            Action <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right" role="menu" style="text-align: left; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                            <li>
                                                <a href="<?php echo base_url(); ?>manager/tickets/view/<?php echo $t['id']; ?>">
                                                    <i class="fa fa-eye text-primary"></i> View Ticket & History
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?php echo base_url(); ?>manager/tickets/edit/<?php echo $t['id']; ?>">
                                                    <i class="fa fa-edit text-info"></i> Edit Details & Assignees
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?php echo base_url(); ?>manager/tickets/view/<?php echo $t['id']; ?>#followup-section">
                                                    <i class="fa fa-calendar-plus-o text-success"></i> Add Follow-up
                                                </a>
                                            </li>
                                            <li class="divider"></li>

                                            <!-- Progress Statuses -->
                                            <?php if($st != 'in progress'){ ?>
                                                <li>
                                                    <a href="<?php echo base_url(); ?>manager/tickets/status/<?php echo $t['id']; ?>/in_progress">
                                                        <i class="fa fa-spinner text-warning"></i> Mark In Progress
                                                    </a>
                                                </li>
                                            <?php } ?>
                                            <?php if($st != 'on hold'){ ?>
                                                <li>
                                                    <a href="<?php echo base_url(); ?>manager/tickets/status/<?php echo $t['id']; ?>/on_hold">
                                                        <i class="fa fa-pause text-muted"></i> Put On Hold
                                                    </a>
                                                </li>
                                            <?php } ?>
                                            <?php if($st != 'resolved'){ ?>
                                                <li>
                                                    <a href="<?php echo base_url(); ?>manager/tickets/status/<?php echo $t['id']; ?>/resolved">
                                                        <i class="fa fa-check text-success"></i> Mark Resolved
                                                    </a>
                                                </li>
                                            <?php } ?>

                                            <!-- Close Ticket (Support or Admin Review Gate) -->
                                            <?php if($st != 'closed'){ ?>
                                                <li>
                                                    <a href="<?php echo base_url(); ?>manager/tickets/view/<?php echo $t['id']; ?>#review_close_section">
                                                        <i class="fa fa-lock text-danger"></i> Review & Close (Support)
                                                    </a>
                                                </li>
                                            <?php } ?>

                                            <?php if(!empty($is_admin)){ ?>
                                                <li class="divider"></li>
                                                <li>
                                                    <a href="<?php echo base_url(); ?>manager/tickets/delete/<?php echo $t['id']; ?>" onclick="return confirm('Permanently delete ticket #<?php echo $t['ticket_number']; ?>?');" style="color: #dc2626;">
                                                        <i class="fa fa-trash"></i> Delete Ticket
                                                    </a>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <?php } } else { ?>
                            <tr>
                                <td colspan="10" style="text-align:center; padding: 35px; color: #777;">
                                    <i class="fa fa-ticket" style="font-size: 32px; color: #cbd5e1; display: block; margin-bottom: 8px;"></i>
                                    No Tickets Found matching your filter criteria.
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
