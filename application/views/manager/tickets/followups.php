<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
    <section class="content-header">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <h1 style="margin: 0; font-size: 22px; font-weight: 700;">
                <i class="fa fa-calendar-check-o text-primary"></i> <?php echo htmlspecialchars($info['page_heading']); ?>
            </h1>
            <div>
                <a href="<?php echo base_url(); ?>manager/tickets/add" class="btn btn-success" style="font-weight: 600;">
                    <i class="fa fa-plus-circle"></i> Raise Ticket
                </a>
                <a href="<?php echo base_url(); ?>manager/tickets" class="btn btn-default" style="font-weight: 600; margin-left: 5px;">
                    <i class="fa fa-list"></i> View All Tickets
                </a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-default p-4" style="border-top: 3px solid #00a65a; margin-bottom: 20px;">
                    <form class="form-inline head_filter form-inline-mob" method="get" action="<?php echo base_url(); ?>manager/tickets/followups">
                        <div class="form-group">
                            <select class="form-control select2" name="type" style="min-width: 140px;">
                                <option value="">Time Period</option>
                                <option <?php if(isset($_GET['type']) && $_GET['type']=='1'){ echo "selected"; } ?> value="1">All Missed</option>
                                <option <?php if(isset($_GET['type']) && $_GET['type']=='2'){ echo "selected"; } ?> value="2">Last 7 Days</option>
                                <option <?php if(isset($_GET['type']) && $_GET['type']=='3'){ echo "selected"; } ?> value="3">Today</option>
                                <option <?php if(isset($_GET['type']) && $_GET['type']=='4'){ echo "selected"; } ?> value="4">Next 7 Days</option>
                                <option <?php if(isset($_GET['type']) && $_GET['type']=='5'){ echo "selected"; } ?> value="5">All Futures</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <div class="input-daterange input-group" style="width: 250px;">
                                <input type="text" class="input-md form-control datepicker" name="d1" placeholder="From Date"
                                    value="<?php if(isset($_GET['d1'])){ echo htmlspecialchars($_GET['d1']); } ?>" autocomplete="off"/>
                                <span class="input-group-addon">To</span>
                                <input type="text" class="input-md form-control datepicker" name="d2" placeholder="To Date"
                                    value="<?php if(isset($_GET['d2'])){ echo htmlspecialchars($_GET['d2']); } ?>" autocomplete="off"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <select class="form-control select2" name="status" style="min-width: 120px;">
                                <option value="">All Statuses</option>
                                <?php 
                                $statuses = array('Open', 'Assigned', 'In Progress', 'Pending', 'On Hold', 'Resolved', 'Closed');
                                foreach($statuses as $st){ ?>
                                    <option value="<?php echo $st; ?>" <?php if(isset($_GET['status']) && $_GET['status']==$st){ echo 'selected'; } ?>><?php echo $st; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <input type="text" name="search" class="form-control" placeholder="Search ticket # or company..."
                                value="<?php if(isset($_GET['search'])){ echo htmlspecialchars($_GET['search']); } ?>" style="min-width: 200px;">
                        </div>

                        <button type="submit" class="btn btn-primary" style="margin-left: 5px;">Filter</button>
                        <a href="<?php echo base_url(); ?>manager/tickets/followups" class="btn btn-default" style="margin-left: 5px;">Reset</a>
                    </form>
                </div>

                <div class="box box-default">
                    <div class="box-body" style="padding: 0;">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" style="margin-bottom: 0;">
                                <thead>
                                    <tr style="background-color: #f8fafc; font-size: 12px; text-transform: uppercase; color: #475569;">
                                        <th style="width: 110px;">Ticket #</th>
                                        <th>Company / Client</th>
                                        <th>Title / Department</th>
                                        <th style="width: 140px;">Follow-up Date</th>
                                        <th style="width: 130px;">Next Date</th>
                                        <th>Latest Remarks</th>
                                        <th style="width: 100px;">Status</th>
                                        <th style="width: 130px; text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(isset($tickets) && !empty($tickets)) { 
                                        foreach($tickets as $t) { 
                                            $fu_date = !empty($t['followup_date']) ? $t['followup_date'] : null;
                                            $is_missed = ($fu_date && $fu_date < $today_str && !in_array(strtolower($t['status']), array('resolved', 'closed')));
                                            $is_today = ($fu_date === $today_str);
                                            $latest_fu = isset($latest_fu_map[$t['id']]) ? $latest_fu_map[$t['id']] : null;
                                    ?>
                                    <tr style="<?php echo $is_missed ? 'background-color: #fff1f2;' : ''; ?>">
                                        <td>
                                            <a href="<?php echo base_url(); ?>manager/tickets/view/<?php echo $t['id']; ?>" style="font-weight: 700; color: #1d4ed8;">
                                                <?php echo htmlspecialchars($t['ticket_number']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars(!empty($t['company_name']) ? $t['company_name'] : (!empty($t['customer']) ? $t['customer'] : '-')); ?></strong>
                                            <div style="font-size: 11px; margin-top: 2px;">
                                                <span class="label <?php echo (!empty($t['client_type']) && $t['client_type']=='Non-Onboarded') ? 'label-warning' : 'label-success'; ?>" style="font-size: 10px;">
                                                    <?php echo htmlspecialchars(!empty($t['client_type']) ? $t['client_type'] : 'Onboarded'); ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #1e293b;">
                                                <a href="<?php echo base_url(); ?>manager/tickets/view/<?php echo $t['id']; ?>" style="color: inherit;">
                                                    <?php echo htmlspecialchars(!empty($t['ticket_title']) ? $t['ticket_title'] : $t['subject']); ?>
                                                </a>
                                            </div>
                                            <div style="margin-top: 2px; font-size: 11px; color: #64748b;">
                                                <i class="fa fa-building-o"></i> <?php echo htmlspecialchars(!empty($t['department']) ? $t['department'] : 'General'); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if($fu_date){ ?>
                                                <div style="font-weight: 700; color: <?php echo $is_missed ? '#dc2626' : ($is_today ? '#047857' : '#1e293b'); ?>;">
                                                    <i class="fa fa-calendar"></i> <?php echo date('d-M-Y', strtotime($fu_date)); ?>
                                                </div>
                                                <?php if($is_missed){ ?>
                                                    <span class="label label-danger" style="font-size: 9.5px; padding: 2px 5px;">MISSED</span>
                                                <?php } elseif($is_today){ ?>
                                                    <span class="label label-success" style="font-size: 9.5px; padding: 2px 5px;">TODAY</span>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <span style="color: #94a3b8;">Not Scheduled</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if(!empty($latest_fu['next_followup_date']) && $latest_fu['next_followup_date'] != '0000-00-00'){ ?>
                                                <span class="label label-info" style="font-size: 11px;">
                                                    <?php echo date('d-M-Y', strtotime($latest_fu['next_followup_date'])); ?>
                                                </span>
                                            <?php } else { ?>
                                                <span style="color: #94a3b8;">-</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if(!empty($latest_fu['remarks'])){ ?>
                                                <div style="color: #1e293b; font-size: 12px; margin-bottom: 2px;">
                                                    <?php echo htmlspecialchars($latest_fu['remarks']); ?>
                                                </div>
                                            <?php } elseif(!empty($t['remark'])){ ?>
                                                <div style="color: #1e293b; font-size: 12px;">
                                                    <?php echo htmlspecialchars($t['remark']); ?>
                                                </div>
                                            <?php } else { ?>
                                                <span style="color: #94a3b8; font-style: italic;">No remarks yet</span>
                                            <?php } ?>

                                            <?php if(!empty($latest_fu['next_action'])){ ?>
                                                <div style="font-size: 11px; color: #4338ca;">
                                                    <strong>Next Action:</strong> <?php echo htmlspecialchars($latest_fu['next_action']); ?>
                                                </div>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <span class="label label-default" style="font-size: 11px;">
                                                <?php echo htmlspecialchars($t['status']); ?>
                                            </span>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="<?php echo base_url(); ?>manager/tickets/view/<?php echo $t['id']; ?>" class="btn btn-xs btn-primary" style="margin-right: 3px;">
                                                <i class="fa fa-eye"></i> View
                                            </a>
                                            <a href="<?php echo base_url(); ?>manager/tickets/view/<?php echo $t['id']; ?>#followup" class="btn btn-xs btn-success">
                                                <i class="fa fa-plus"></i> Follow-up
                                            </a>
                                        </td>
                                    </tr>
                                    <?php } } else { ?>
                                    <tr>
                                        <td colspan="8" style="text-align:center; padding: 30px; color: #777;">
                                            <i class="fa fa-calendar-times-o" style="font-size: 28px; color: #cbd5e1; display: block; margin-bottom: 6px;"></i>
                                            No Ticket Follow-ups Found.
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
