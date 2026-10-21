<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-briefcase" style="color: #b91c1c;"></i> Key Account Management (KAM)
            <small>Client Portfolio & Corporate Operations</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li>Operations</li>
            <li class="active">KAM</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box" style="border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                    <span class="info-box-icon" style="background: #1e293b; color: #fff; border-radius: 6px 0 0 6px;"><i class="fa fa-building"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text" style="font-size: 12px; color: #64748b; font-weight: 600;">Managed Accounts</span>
                        <span class="info-box-number" style="font-size: 22px; color: #0f172a; font-weight: 700;"><?php echo count($clients); ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box" style="border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                    <span class="info-box-icon" style="background: #b91c1c; color: #fff; border-radius: 6px 0 0 6px;"><i class="fa fa-phone"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text" style="font-size: 12px; color: #64748b; font-weight: 600;">Calling Dashboard</span>
                        <span class="info-box-number" style="font-size: 14px; margin-top: 5px;">
                            <a href="<?php echo base_url(); ?>manager/calls" class="btn btn-xs btn-danger" style="background: #b91c1c;">Open Calls</a>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box" style="border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                    <span class="info-box-icon" style="background: #0284c7; color: #fff; border-radius: 6px 0 0 6px;"><i class="fa fa-calendar"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text" style="font-size: 12px; color: #64748b; font-weight: 600;">Meetings Scheduled</span>
                        <span class="info-box-number" style="font-size: 14px; margin-top: 5px;">
                            <a href="<?php echo base_url(); ?>manager/leads/meetings" class="btn btn-xs btn-info">View Meetings</a>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box" style="border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                    <span class="info-box-icon" style="background: #10b981; color: #fff; border-radius: 6px 0 0 6px;"><i class="fa fa-plus"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text" style="font-size: 12px; color: #64748b; font-weight: 600;">Onboard Account</span>
                        <span class="info-box-number" style="font-size: 14px; margin-top: 5px;">
                            <a href="<?php echo base_url(); ?>manager/leads/add" class="btn btn-xs btn-success">Add Client</a>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="box" style="border-top: 3px solid #b91c1c; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
            <div class="box-header with-border" style="background: #f8fafc;">
                <h3 class="box-title" style="font-weight: 600; font-size: 15px; color: #334155;">
                    <i class="fa fa-list"></i> Key Accounts Directory
                </h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover table-striped" style="font-size: 13px; margin-bottom: 0;">
                    <thead>
                        <tr style="background: #f1f5f9; color: #334155;">
                            <th style="width: 70px;">Lead #</th>
                            <th>Company Name</th>
                            <th>POC / Contact</th>
                            <th>Emirate / Zone</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($clients)): ?>
                            <?php foreach ($clients as $c): ?>
                                <tr>
                                    <td><span class="badge" style="background: #0f172a; color: #fff;"><?php echo htmlspecialchars($c['lead_id']); ?></span></td>
                                    <td><strong style="color: #0f172a;"><?php echo htmlspecialchars($c['customer']); ?></strong></td>
                                    <td>
                                        <div><i class="fa fa-user text-muted"></i> <?php echo !empty($c['poc']) ? htmlspecialchars($c['poc']) : '—'; ?></div>
                                        <div style="font-size: 11.5px; color: #64748b;"><i class="fa fa-phone text-muted"></i> <?php echo !empty($c['contact1']) ? htmlspecialchars($c['contact1']) : (!empty($c['alt_contact']) ? htmlspecialchars($c['alt_contact']) : '—'); ?></div>
                                    </td>
                                    <td>
                                        <?php 
                                            $ez = array_filter(array(!empty($c['tl_emirate']) ? $c['tl_emirate'] : '', !empty($c['tl_zone']) ? $c['tl_zone'] : ''));
                                            echo !empty($ez) ? htmlspecialchars(implode(' / ', $ez)) : '—';
                                        ?>
                                    </td>
                                    <td><span class="label label-info"><?php echo !empty($c['status_name']) ? htmlspecialchars($c['status_name']) : 'New'; ?></span></td>
                                    <td><?php echo !empty($c['owner_name']) ? htmlspecialchars($c['owner_name']) : 'Unassigned'; ?></td>
                                    <td>
                                        <div class="btn-group" style="min-width: 85px;">
                                            <button type="button" class="btn btn-sm btn-warning">Action</button>
                                            <button type="button" class="btn btn-sm btn-warning dropdown-toggle"
                                                data-toggle="dropdown" aria-expanded="false">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                <li> <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($c['id']); ?>"
                                                        data-toggle="tooltip" title="View">Preview</a> </li>
                                                <li> <a href="<?php echo base_url(); ?>manager/leads/edit/<?php echo base64_encode($c['id']); ?>"
                                                        data-toggle="tooltip" title="Edit">Edit</a> </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 30px; color: #94a3b8;">No client records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
