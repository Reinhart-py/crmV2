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
          <i class="fa fa-calendar" style="color: #0072f0; margin-right: 6px;"></i> Scheduled Corporate Events &amp; Strategic Plans
        </h3>
      </div>
      <div class="box-body" style="padding: 20px;">
        <div class="row">
          <?php if (!empty($events)): ?>
            <?php foreach ($events as $ev): ?>
              <div class="col-md-4 col-sm-6" style="margin-bottom: 20px;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 18px; height: 100%; display: flex; flex-direction: column;">
                  <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                    <span class="label" style="background: #0072f0; color: #ffffff; font-weight: 700; font-size: 11px; padding: 3px 8px; border-radius: 3px;">
                      <?php echo htmlspecialchars($ev['event_type']); ?>
                    </span>
                    <span style="font-size: 12px; font-weight: 600; color: #64748b;">
                      <i class="fa fa-calendar-o"></i> <?php echo date('d M Y', strtotime($ev['event_date'])); ?>
                    </span>
                  </div>
                  <h4 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0 0 8px 0; line-height: 1.35;">
                    <?php echo htmlspecialchars($ev['title']); ?>
                  </h4>
                  <p style="font-size: 13px; color: #475569; line-height: 1.45; flex: 1; margin-bottom: 12px;">
                    <?php echo htmlspecialchars($ev['description']); ?>
                  </p>
                  <div style="border-top: 1px solid #e2e8f0; padding-top: 10px; font-size: 12px; color: #64748b; display: flex; justify-content: space-between;">
                    <span><i class="fa fa-clock-o"></i> <?php echo htmlspecialchars($ev['event_time']); ?></span>
                    <span><i class="fa fa-map-marker"></i> <?php echo htmlspecialchars($ev['location']); ?></span>
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
