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
                            <form class="form-inline head_filter form-inline-mob" action="<?php echo base_url(); ?>manager/admin-reports" method="get">

                                <div class="form-group">
                                    <select class="form-control" name="period" onchange="if(this.value!='custom'){ $('#adminCustomDates').hide(); } else { $('#adminCustomDates').show(); }">
                                        <option value="">Select Period</option>
                                        <option value="daily" <?php if(isset($_GET['period']) && $_GET['period']=='daily') echo 'selected'; ?>>Daily (Today)</option>
                                        <option value="weekly" <?php if(isset($_GET['period']) && $_GET['period']=='weekly') echo 'selected'; ?>>Weekly (Last 7 Days)</option>
                                        <option value="monthly" <?php if(isset($_GET['period']) && $_GET['period']=='monthly') echo 'selected'; ?>>Monthly (This Month)</option>
                                        <option value="custom" <?php if(isset($_GET['period']) && $_GET['period']=='custom') echo 'selected'; ?>>Custom Date Range</option>
                                    </select>
                                </div>

                                <div class="form-group" id="adminCustomDates" style="<?php echo (isset($_GET['period']) && $_GET['period']=='custom') ? '' : 'display:none;'; ?>">
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
                                    <select class="form-control" style="width: 100%;" name="department">
                                        <option value="">All Department</option>
                                        <option <?php if(!empty($_GET['department']) && ($_GET['department']=="2")) { echo "selected"; } ?> value="2">Administration</option>
                                        <option <?php if(!empty($_GET['department']) && ($_GET['department']=="3")) { echo "selected"; } ?> value="3">Operations</option>
                                        <option <?php if(!empty($_GET['department']) && ($_GET['department']=="4")) { echo "selected"; } ?> value="4">IT</option>
                                        <option <?php if(!empty($_GET['department']) && ($_GET['department']=="5")) { echo "selected"; } ?> value="5">Human Resource</option>
                                        <option <?php if(!empty($_GET['department']) && ($_GET['department']=="6")) { echo "selected"; } ?> value="6">Back Office</option>
                                        <option <?php if(!empty($_GET['department']) && ($_GET['department']=="7")) { echo "selected"; } ?> value="7">Sales</option>
                                        <option <?php if(!empty($_GET['department']) && ($_GET['department']=="8")) { echo "selected"; } ?> value="8">Business Development</option>
                                        <option <?php if(!empty($_GET['department']) && ($_GET['department']=="9")) { echo "selected"; } ?> value="9">Field Staff</option>
                                        <option <?php if(!empty($_GET['department']) && ($_GET['department']=="10")) { echo "selected"; } ?> value="10">Other</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-success" name="search" value="report"><i class="fa fa-search"></i> Generate Report</button>
                                    <a href="<?php echo base_url(); ?>manager/admin-reports" class="btn btn-danger">Reset</a>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-bar-chart"></i> Department &amp; User Performance Summary</h3>
                    </div>
                    <div class="box-body no-padding">
                        <?php if(isset($list) && !empty($list)) { ?>
                        <div class="table-responsive" style="padding:0px 15px 15px">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Team Member / Manager</th>
                                        <th>Department</th>
                                        <th>Total Leads</th>
                                        <th>Active Leads</th>
                                        <th>Closed Leads</th>
                                        <th>Missed Followups</th>
                                        <th>Attended Followups</th>
                                        <th>Total MRR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; foreach($list as $row) { ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><strong><?php echo isset($row['name']) ? htmlspecialchars($row['name']) : ''; ?></strong></td>
                                        <td><span class="label label-default"><?php echo isset($row['department']) ? htmlspecialchars($row['department']) : ''; ?></span></td>
                                        <td><?php echo isset($row['totalLead']) ? (int)$row['totalLead'] : 0; ?></td>
                                        <td><span class="badge bg-green"><?php echo isset($row['activeLead']) ? (int)$row['activeLead'] : 0; ?></span></td>
                                        <td><span class="badge bg-blue"><?php echo isset($row['closedLead']) ? (int)$row['closedLead'] : 0; ?></span></td>
                                        <td><span class="badge bg-red"><?php echo isset($row['total_missed']) ? (int)$row['total_missed'] : 0; ?></span></td>
                                        <td><span class="badge bg-purple"><?php echo isset($row['attendedFollowups']) ? (int)$row['attendedFollowups'] : 0; ?></span></td>
                                        <td>AED <?php echo isset($row['amount']) ? number_format((float)$row['amount'], 2) : '0.00'; ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php } else if(isset($_GET['search']) || isset($_GET['period']) || isset($_GET['department'])) { ?>
                        <div style="padding: 30px; text-align: center; color: #777;">
                            No records found for the selected department / period.
                        </div>
                        <?php } else { ?>
                        <div style="padding: 30px; text-align: center; color: #777;">
                            Select period (Daily, Weekly, Monthly) or Department above to view report.
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
