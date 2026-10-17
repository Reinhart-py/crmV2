<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
    <section class="content-header" style="margin-bottom: 20px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
            <div>
                <h1 style="font-weight: 700; color: var(--crm-text); margin: 0 0 5px 0;">
                    <i class="fa fa-folder-open-o" style="color: #b91c1c; margin-right: 8px;"></i><?php echo htmlspecialchars($dept_meta['dept_title']); ?>
                </h1>
                <ol class="breadcrumb" style="background: transparent; padding: 0; margin: 0;">
                    <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url(); ?>manager/admin-reports">Admin Reports</a></li>
                    <li class="active"><?php echo htmlspecialchars($dept_meta['dept_title']); ?></li>
                </ol>
            </div>
            <div>
                <a href="<?php echo base_url(); ?>manager/admin-reports" class="btn btn-default" style="font-weight: 600; border-radius: 4px; padding: 6px 14px; color: var(--crm-text);">
                    <i class="fa fa-arrow-left" style="margin-right: 6px;"></i> All Departments
                </a>
            </div>
        </div>
    </section>

    <section class="content" style="padding-top: 0;">
        <div class="row">
            <?php 
            $reports = isset($dept_meta['reports']) ? $dept_meta['reports'] : array();
            foreach ($reports as $key => $rep) { 
            ?>
            <div class="col-lg-4 col-md-6 col-sm-12" style="margin-bottom: 20px;">
                <div class="crm-modern-stat-card" style="border-top: 2px solid #b91c1c !important; cursor: pointer; height: 100%; display: flex; flex-direction: column; justify-content: space-between;" onclick="window.location.href='<?php echo base_url(); ?>manager/admin-reports/<?php echo $dept; ?>/<?php echo $rep['slug']; ?>';">
                    <div>
                        <div class="card-top" style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 36px; height: 36px; border-radius: 6px; background: rgba(185, 28, 28, 0.08); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fa <?php echo $rep['icon']; ?>" style="font-size: 16px; color: #b91c1c;"></i>
                                </div>
                                <h3 class="card-title" style="font-size: 15px !important; font-weight: 700; margin: 0; color: var(--crm-text); line-height: 1.3;">
                                    <?php echo htmlspecialchars($rep['title']); ?>
                                </h3>
                            </div>
                            <?php if (!empty($rep['badge'])) { ?>
                            <span class="label label-danger" style="font-size: 11px; padding: 3px 8px; border-radius: 9999px; white-space: nowrap; font-weight: 600;">
                                <?php echo htmlspecialchars($rep['badge']); ?>
                            </span>
                            <?php } ?>
                        </div>

                        <div class="crm-seg-bar" style="margin-bottom: 12px;">
                            <div class="crm-seg" style="width: 100%;"></div>
                        </div>

                        <p style="font-size: 12.5px; color: var(--crm-text-muted); line-height: 1.5; margin: 0 0 16px 0; min-height: 38px;">
                            <?php echo htmlspecialchars($rep['desc']); ?>
                        </p>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 10px; border-top: 1px solid var(--crm-border);">
                        <span style="font-size: 12px; color: #b91c1c; font-weight: 600;">
                            Live Database Connected
                        </span>
                        <a href="<?php echo base_url(); ?>manager/admin-reports/<?php echo $dept; ?>/<?php echo $rep['slug']; ?>" class="btn btn-sm btn-primary" style="background-color: #b91c1c; border-color: #991b1b; font-weight: 600; border-radius: 4px; padding: 4px 12px;">
                            View Report <i class="fa fa-chevron-right" style="font-size: 10px; margin-left: 4px;"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
