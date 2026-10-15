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
                            <form class="form-inline head_filter form-inline-mob" method="get" action="<?php echo base_url(); ?>manager/kam/onboard">
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
                                    <div class="input-daterange input-group" style="width: 100%;">
                                        <input type="text" class="input-md form-control only_number" name="mrr1" placeholder="MRR Min" value="<?php if(isset($_GET['mrr1']) && (!empty($_GET['mrr1']))){ echo htmlspecialchars($_GET['mrr1']); } ?>" autocomplete="off" />
                                        <span class="input-group-addon">To</span>
                                        <input type="text" class="input-md form-control only_number" name="mrr2" placeholder="MRR Max" value="<?php if(isset($_GET['mrr2']) && (!empty($_GET['mrr2']))){ echo htmlspecialchars($_GET['mrr2']); } ?>" autocomplete="off" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <input type="text" class="input-md form-control" name="search" placeholder="Search Clients"
                                        value="<?php if(isset($_GET['search']) && (!empty($_GET['search']))){ echo htmlspecialchars($_GET['search']); } ?>"
                                        autocomplete="off" />
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Search</button>
                                    <a href="<?php echo base_url(); ?>manager/kam/onboard" class="btn btn-danger">Reset</a>
                                </div>
                            </form>
                        </div>
                        <div class="x_col-sm-auto text-center pt-4 pt-md-0">
                            <div>
                                <a href="javascript:void(0);" class="btn btn-success"> Add Client</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="box-body table-responsive">
                        <table id="example_kam" class="table table-hover table-bordered table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Service Type</th>
                                    <th>MRR</th>
                                    <th>POC Name</th>
                                    <th>Contact Number</th>
                                    <th>Next FU date</th>
                                    <th>Created</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(isset($product_list) && !empty($product_list)){ ?>
                                    <?php foreach($product_list as $li){ ?>
                                    <tr>
                                        <td><?php echo $li['id']; ?></td>
                                        <td><?php echo htmlspecialchars($li['customer'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($li['servicecat'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($li['mrr'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($li['poc'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($li['contact1'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($li['followup_date'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($li['created'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($li['status'] ?? ''); ?></td>
                                        <td>
                                            <div class="btn-group" style="min-width: 85px;">
                                                <button type="button" class="btn btn-sm btn-warning">Action</button>
                                                <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="javascript:void(0);">Preview</a></li>
                                                    <li><a href="javascript:void(0);">Edit</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="10" class="text-center" style="padding: 25px; color: #64748b;">No onboarded clients found</td>
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
