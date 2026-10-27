<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
    <section class="content-header" style="padding: 15px 15px 5px 15px;">
        <h1 style="font-size: 22px; font-weight: 800; color: #000000; margin: 0; letter-spacing: -0.02em;"> <?php echo $info['page_heading']; ?> </h1>
    </section>

    <section class="content" style="padding-top: 10px;">
        <div class="row">
            <div class="col-xs-12">
                <div class="box p-4" style="border: 1px solid #e2e8f0; border-radius: 4px; margin-bottom: 15px; background: #ffffff;">
                    <div class="x_row">
                        <div class="x_col">
                            <form class="form-inline head_filter form-inline-mob" method="get">
                                <div class="form-group">
                                    <input type="text" class="input-md form-control" name="client" placeholder="Search Client / Contact / Ref No." value="<?php echo htmlspecialchars($this->input->get('client') ?? ''); ?>" autocomplete="off" style="border: 1px solid #cbd5e1; border-radius: 4px;" />
                                </div>
                                <div class="form-group">
                                    <div class="input-daterange input-group" id="datepicker" style="width: 100%;">
                                        <input type="text" class="input-md form-control datepicker" name="d1" placeholder="Create From" value="<?php echo htmlspecialchars($this->input->get('d1') ?? ''); ?>" autocomplete="off" style="border: 1px solid #cbd5e1; border-radius: 4px 0 0 4px;" />
                                        <span class="input-group-addon" style="background: #f8fafc; border: 1px solid #cbd5e1; border-left: none; border-right: none; color: #475569; font-weight: 600;">To</span>
                                        <input type="text" class="input-md form-control datepicker" name="d2" placeholder="Create To" value="<?php echo htmlspecialchars($this->input->get('d2') ?? ''); ?>" autocomplete="off" style="border: 1px solid #cbd5e1; border-radius: 0 4px 4px 0;" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-default" style="background: #000000; color: #ffffff; border: 1px solid #000000; font-weight: 600; padding: 6px 16px;">Search</button>
                                    <a href="<?php echo htmlspecialchars($reset_url ?? base_url('manager/orders/pending_process')); ?>" class="btn btn-default" style="background: #ffffff; color: #000000; border: 1px solid #000000; font-weight: 600; padding: 6px 14px;"> Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="box" style="border: 1px solid #e2e8f0; border-radius: 4px; background: #ffffff;">
                    <div class="box-body" style="padding: 15px;">
                        <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
                        <?php echo get_message($this->session->flashdata('message'), 'message'); ?>
                        <?php if(isset($pending_leads) && !empty($pending_leads)) { ?>
                        <div class="table-responsive">
                            <table id="example_pending_process" class="table table-hover table-bordered table-striped" style="width:100%; font-size: 13px;">
                                <thead>
                                    <tr style="background: #000000; color: #ffffff;">
                                        <th style="color: #ffffff; font-weight: 700;">Database No.</th>
                                        <th style="color: #ffffff; font-weight: 700;">Lead No.</th>
                                        <th style="color: #ffffff; font-weight: 700;">Customer</th>
                                        <th style="color: #ffffff; font-weight: 700;">Phone / Contact</th>
                                        <th style="color: #ffffff; font-weight: 700;">Requirements &amp; Remarks</th>
                                        <th style="color: #ffffff; font-weight: 700;">Requested By</th>
                                        <th style="color: #ffffff; font-weight: 700;">Request Date</th>
                                        <th style="color: #ffffff; font-weight: 700; text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($pending_leads as $p){ ?>
                                    <tr>
                                        <td><span class="label" style="background: #f8fafc; color: #000000; border: 1px solid #000000; font-family: monospace; font-weight: 700; font-size: 12px;"><?php echo htmlspecialchars($p['db_ref_no'] ?: '-'); ?></span></td>
                                        <td><span class="label" style="background: #000000; color: #ffffff; border: 1px solid #000000; font-family: monospace; font-weight: 700; font-size: 12px;"><?php echo htmlspecialchars($p['lead_ref_no'] ?: '-'); ?></span></td>
                                        <td>
                                            <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($p['id']); ?>" style="color: #000000; font-weight: 700; text-decoration: underline;">
                                                <?php echo htmlspecialchars($p['customer'] ?: 'Client'); ?>
                                            </a>
                                            <?php if(!empty($p['company_name'])){ ?>
                                                <br><small style="color: #64748b; font-weight: 500;"><?php echo htmlspecialchars($p['company_name']); ?></small>
                                            <?php } ?>
                                        </td>
                                        <td style="color: #0f172a; font-weight: 600;"><?php echo htmlspecialchars($p['contact1'] ?: '-'); ?></td>
                                        <td style="color: #334155; font-size: 12px;"><?php echo htmlspecialchars($p['order_request_remarks'] ?: '-'); ?></td>
                                        <td style="color: #0f172a; font-weight: 600;"><?php echo htmlspecialchars($p['requested_by_name'] ?: 'Staff'); ?></td>
                                        <td><span class="no_show"><?php echo !empty($p['order_requested_at']) ? date('Ymd', strtotime($p['order_requested_at'])) : ''; ?></span><?php echo !empty($p['order_requested_at']) ? date('d-m-Y', strtotime($p['order_requested_at'])) : '-'; ?></td>
                                        <td style="text-align: center;">
                                            <div class="btn-group" style="min-width: 85px;">
                                                <button type="button" class="btn btn-sm btn-default" style="background: #000000; color: #ffffff; border: 1px solid #000000; font-weight: 600;">Action</button>
                                                <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false" style="background: #000000; color: #ffffff; border: 1px solid #000000;">
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right" role="menu" style="border: 1px solid #000000; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                                    <li><a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($p['id']); ?>" data-toggle="tooltip" title="View Lead" style="font-weight: 600; color: #000000;"><i class="fa fa-eye" style="margin-right: 6px;"></i> View Lead</a></li>
                                                    <li><a href="<?php echo base_url(); ?>manager/orders/edit_pending/<?php echo $p['id']; ?>" data-toggle="tooltip" title="Process Order" style="font-weight: 600; color: #000000;"><i class="fa fa-cogs" style="margin-right: 6px;"></i> Process Order</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php } else { ?>
                            <div class="alert alert-info"> No Data Found </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#example_pending_process')) {
        $('#example_pending_process').DataTable().destroy();
    }
    $('#example_pending_process').DataTable({
        "order": [[6, "desc"]],
        "pageLength": 25,
        "language": {
            "search": "_INPUT_",
            "searchPlaceholder": "Filter table records..."
        }
    });
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
