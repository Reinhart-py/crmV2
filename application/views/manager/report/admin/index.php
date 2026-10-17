<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
    <section class="content-header" style="margin-bottom: 20px;">
        <h1 style="font-weight: 700; color: var(--crm-text); margin: 0 0 5px 0;">
            <i class="fa fa-tachometer" style="color: #b91c1c; margin-right: 8px;"></i><?php echo $info['page_heading']; ?>
        </h1>
        <ol class="breadcrumb" style="background: transparent; padding: 0; margin: 0;">
            <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Admin Reports</li>
        </ol>
    </section>

    <section class="content" style="padding-top: 0;">
        <div class="row">
            <?php 
            $dept_list = isset($departments) ? $departments : array();
            $dept_colors = array(
                'sales' => array('border' => '#10b981', 'icon_bg' => '#ecfdf5', 'icon_color' => '#059669', 'badge' => 'label-success'),
                'operations' => array('border' => '#0ea5e9', 'icon_bg' => '#f0f9ff', 'icon_color' => '#0284c7', 'badge' => 'label-info'),
                'support' => array('border' => '#f59e0b', 'icon_bg' => '#fffbeb', 'icon_color' => '#d97706', 'badge' => 'label-warning'),
                'hr' => array('border' => '#8b5cf6', 'icon_bg' => '#f5f3ff', 'icon_color' => '#7c3aed', 'badge' => 'label-primary'),
                'it' => array('border' => '#ef4444', 'icon_bg' => '#fef2f2', 'icon_color' => '#dc2626', 'badge' => 'label-danger')
            );
            foreach ($dept_list as $key => $dept) { 
                $scheme = isset($dept_colors[$key]) ? $dept_colors[$key] : array('border' => '#b91c1c', 'icon_bg' => '#fee2e2', 'icon_color' => '#b91c1c', 'badge' => 'label-default');
            ?>
            <div class="col-lg-4 col-md-6 col-sm-12" style="margin-bottom: 24px;">
                <div class="crm-modern-stat-card" style="border-top: 3px solid <?php echo $scheme['border']; ?> !important; cursor: pointer; height: 100%; display: flex; flex-direction: column; justify-content: space-between;" onclick="window.location.href='<?php echo base_url(); ?>manager/admin-reports/<?php echo $dept['slug']; ?>';">
                    <div>
                        <div class="card-top" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 38px; height: 38px; border-radius: 8px; background: <?php echo $scheme['icon_bg']; ?>; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa <?php echo $dept['icon']; ?>" style="font-size: 18px; color: <?php echo $scheme['icon_color']; ?>;"></i>
                                </div>
                                <h3 class="card-title" style="font-size: 16px !important; font-weight: 700; margin: 0; color: var(--crm-text);">
                                    <?php echo htmlspecialchars($dept['title']); ?>
                                </h3>
                            </div>
                            <span class="label <?php echo $scheme['badge']; ?>" style="font-size: 11px; padding: 4px 8px; border-radius: 9999px;">
                                <?php echo $dept['reports_count']; ?> Reports
                            </span>
                        </div>

                        <div class="crm-seg-bar" style="margin-bottom: 16px;">
                            <div class="crm-seg" style="width: 100%; background: <?php echo $scheme['border']; ?> !important;"></div>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 16px;">
                            <?php foreach ($dept['stats'] as $label => $val) { ?>
                            <div style="background: var(--crm-bg); border: 1px solid var(--crm-border); border-radius: 6px; padding: 10px 8px; text-align: center;">
                                <div style="font-size: 11px; color: var(--crm-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                                    <?php echo htmlspecialchars($label); ?>
                                </div>
                                <div style="font-size: 13px; font-weight: 700; color: var(--crm-text);">
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
                        <a href="<?php echo base_url(); ?>manager/admin-reports/<?php echo $dept['slug']; ?>" class="btn btn-sm btn-default" style="font-weight: 600; border-radius: 4px; padding: 4px 12px; color: var(--crm-text);">
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
