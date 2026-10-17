<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
    <section class="content-header" style="margin-bottom: 20px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
            <div>
                <h1 style="font-weight: 700; color: var(--crm-text); margin: 0 0 5px 0;">
                    <?php echo htmlspecialchars($dept_meta['dept_title']); ?>
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
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 20px;">
                <div class="crm-modern-stat-card" style="border-top: 3px solid #d4f938 !important; cursor: pointer; height: 100%; min-height: 125px; display: flex; flex-direction: column; justify-content: space-between;" onclick="window.location.href='<?php echo base_url(); ?>manager/admin-reports/<?php echo $dept; ?>/<?php echo $rep['slug']; ?>';">
                    <div>
                        <div class="card-top" style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 10px;">
                            <h3 class="card-title" style="font-size: 15px !important; font-weight: 700; margin: 0; color: var(--crm-text); line-height: 1.35;">
                                <?php echo htmlspecialchars($rep['title']); ?>
                            </h3>
                        </div>

                        <div class="crm-seg-bar" style="margin-bottom: 14px;">
                            <div class="crm-seg" style="width: 100%; background: #d4f938 !important;"></div>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: flex-end; padding-top: 10px; border-top: 1px solid var(--crm-border);">
                        <a href="<?php echo base_url(); ?>manager/admin-reports/<?php echo $dept; ?>/<?php echo $rep['slug']; ?>" class="btn btn-sm btn-default" style="font-weight: 600; border-radius: 4px; padding: 4px 14px; color: var(--crm-text);">
                            Open <i class="fa fa-arrow-right" style="margin-left: 4px;"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
