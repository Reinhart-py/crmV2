<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper px-4">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> <?php echo $info['page_heading']; ?> </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- /.row -->
        <div class="row">
            <div class="col-xs-12">

                <div class="box p-4">
                    <div class="x_row">
                        <div class="x_col">
                            <form class="form-inline head_filter form-inline-mob" action="<?php echo base_url(); ?>manager/download-report" method="get">

                                <div class="form-group">
                                    <div class="input-daterange input-group" id="datepicker" style="width: 100%;">
                                        <input type="text" class="input-md form-control datepicker" name="d1"
                                            placeholder="Created From"
                                            value="<?php if(isset($_GET['d1']) && (!empty($_GET['d1']))){ echo htmlspecialchars($_GET['d1']); } ?>"
                                            autocomplete="off" />
                                        <span class="input-group-addon">To</span>
                                        <input type="text" class="input-md form-control datepicker" name="d2"
                                            placeholder="Created To"
                                            value="<?php if(isset($_GET['d2']) && (!empty($_GET['d2']))){ echo htmlspecialchars($_GET['d2']); } ?>"
                                            autocomplete="off" />
                                    </div>
                                </div>


                                <div class="form-group">
                                    <button type="submit" class="btn btn-success" name="search" value="report">Search</button>
                                    <a href="<?php echo base_url(); ?>manager/download-report" class="btn btn-danger">Reset</a>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="box-header">
                    </div>
                    <div class="box-body">
                        <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
                        <?php echo get_message($this->session->flashdata('message'),'message'); ?>

                        <?php if(isset($list) && !empty($list)) { ?>
                        <div class="table-responsive" style="padding:0px 15px">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Manager</th>
                                        <th>Department</th>
                                        <th>Total Leads</th>
                                        <th>Total MRR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; foreach($list as $row) { ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo !empty($row['name']) ? htmlspecialchars($row['name']) : (isset($row['manager_name']) ? htmlspecialchars($row['manager_name']) : ''); ?></td>
                                        <td><?php echo isset($row['department']) ? htmlspecialchars($row['department']) : ''; ?></td>
                                        <td><?php echo isset($row['totalLead']) ? (int)$row['totalLead'] : (isset($row['total_leads']) ? (int)$row['total_leads'] : 0); ?></td>
                                        <td>AED <?php echo isset($row['amount']) ? number_format((float)$row['amount'], 2) : (isset($row['total_mrr']) ? htmlspecialchars($row['total_mrr']) : '0.00'); ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php } else if(isset($_GET['search'])) { ?>
                        <div style="padding: 20px; text-align: center; color: #777;">
                            No records found for the selected criteria.
                        </div>
                        <?php } else { ?>
                        <div style="padding: 20px; text-align: center; color: #777;">
                            Select date range to search and download report.
                        </div>
                        <?php } ?>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
