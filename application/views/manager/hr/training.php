<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
  <section class="content-header" style="padding: 15px 15px 5px 15px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
      <h1 style="font-size: 20px; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.02em;">
        <?php echo $info['page_heading']; ?>
      </h1>
      <a href="<?php echo base_url('manager/profile'); ?>" class="btn btn-default" style="border: 1px solid #cbd5e1; font-weight: 600; border-radius: 4px;">
        <i class="fa fa-user"></i> My HR Profile
      </a>
    </div>
  </section>

  <section class="content" style="padding-top: 10px;">
    <div class="box" style="border: 1px solid #e2e8f0; border-radius: 4px; background: #ffffff;">
      <div class="box-header with-border" style="border-bottom: 1px solid #f1f5f9; padding: 14px 20px;">
        <h3 class="box-title" style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0;">
          <i class="fa fa-graduation-cap" style="color: #0072f0; margin-right: 6px;"></i> Professional Training Modules &amp; Compliance Courses
        </h3>
      </div>
      <div class="box-body" style="padding: 20px;">
        <div class="row">
          <?php if (!empty($modules)): ?>
            <?php foreach ($modules as $mod): ?>
              <div class="col-md-6 col-sm-12" style="margin-bottom: 20px;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 18px;">
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #0072f0;">
                      <?php echo htmlspecialchars($mod['category']); ?>
                    </span>
                    <span class="label" style="background: <?php echo $mod['progress'] == 100 ? '#16a34a' : ($mod['progress'] > 0 ? '#0072f0' : '#64748b'); ?>; color:#ffffff; font-weight:600; font-size:11px; border-radius:3px;">
                      <?php echo htmlspecialchars($mod['status']); ?> (<?php echo $mod['progress']; ?>%)
                    </span>
                  </div>
                  <h4 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0 0 6px 0;">
                    <?php echo htmlspecialchars($mod['title']); ?>
                  </h4>
                  <p style="font-size: 13px; color: #475569; line-height: 1.45; margin-bottom: 12px;">
                    <?php echo htmlspecialchars($mod['description']); ?>
                  </p>
                  
                  <div style="background: #e2e8f0; border-radius: 4px; height: 8px; overflow: hidden; margin-bottom: 12px;">
                    <div style="background: #0072f0; height: 100%; width: <?php echo $mod['progress']; ?>%; border-radius: 4px;"></div>
                  </div>

                  <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12px; color: #64748b;">
                    <span><i class="fa fa-clock-o"></i> Duration: <?php echo htmlspecialchars($mod['duration']); ?></span>
                    <button type="button" class="btn btn-sm btn-primary" style="background: #0072f0; border-color: #0072f0; border-radius: 4px; font-weight: 600; padding: 3px 12px;">
                      <?php echo $mod['progress'] == 100 ? 'Review Module' : 'Continue Course'; ?>
                    </button>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>
</div>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
