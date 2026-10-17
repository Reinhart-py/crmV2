<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
    <section class="content-header" style="margin-bottom: 20px;">
        <h1 style="font-weight: 700; color: var(--crm-text); margin: 0 0 5px 0;">
            <?php echo $info['page_heading']; ?>
        </h1>
        <ol class="breadcrumb" style="background: transparent; padding: 0; margin: 0;">
            <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Download Report</li>
        </ol>
    </section>

    <section class="content" style="padding-top: 0;">
        <div class="row">
            <?php 
            $dept_list = isset($departments) ? $departments : array();
            foreach ($dept_list as $key => $dept) { 
            ?>
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 24px;">
                <div class="crm-modern-stat-card" style="border-top: 3px solid #d4f938 !important; cursor: pointer; height: 100%; display: flex; flex-direction: column; justify-content: space-between;" onclick="window.location.href='<?php echo base_url(); ?>manager/download-report/<?php echo $dept['slug']; ?>';">
                    <div>
                        <div class="card-top" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                            <h3 class="card-title" style="font-size: 16px !important; font-weight: 700; margin: 0; color: var(--crm-text);">
                                <?php echo htmlspecialchars($dept['title']); ?>
                            </h3>
                            <span class="label" style="background: #d4f938; color: #09090b; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 9999px;">
                                <?php echo $dept['reports_count']; ?> Reports
                            </span>
                        </div>

                        <div class="crm-seg-bar" style="margin-bottom: 16px;">
                            <div class="crm-seg" style="width: 100%; background: #d4f938 !important;"></div>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 16px;">
                            <?php foreach ($dept['stats'] as $label => $val) { ?>
                            <div style="background: #ffffff; border: 1px solid var(--crm-border); border-radius: 6px; padding: 10px 8px; text-align: center;">
                                <div style="font-size: 11px; color: var(--crm-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                                    <?php echo htmlspecialchars($label); ?>
                                </div>
                                <div style="font-size: 13.5px; font-weight: 700; color: #000000;">
                                    <?php echo htmlspecialchars($val); ?>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 12px; border-top: 1px solid var(--crm-border);">
                        <span style="font-size: 12px; color: var(--crm-text-muted); font-weight: 500;">
                            Access all department reports
                        </span>
                        <a href="<?php echo base_url(); ?>manager/download-report/<?php echo $dept['slug']; ?>" class="btn btn-sm btn-default" style="font-weight: 600; border-radius: 4px; padding: 4px 12px; color: var(--crm-text);">
                            Open <i class="fa fa-arrow-right" style="margin-left: 4px;"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php } ?>

            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 24px;">
                <div class="crm-modern-stat-card" style="border-top: 3px solid #d4f938 !important; height: 100%; min-height: 220px;">
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
