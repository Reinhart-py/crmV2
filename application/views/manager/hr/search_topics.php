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
    <!-- Search Bar -->
    <div class="box" style="border: 1px solid #e2e8f0; border-radius: 4px; background: #ffffff; margin-bottom: 20px;">
      <div class="box-body" style="padding: 20px;">
        <form method="get" action="<?php echo base_url('manager/hr/search_topics'); ?>">
          <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Search HR policies, holiday rules, insurance guidelines, company benefits..." value="<?php echo htmlspecialchars($query ?? ''); ?>" style="border: 1px solid #cbd5e1; border-radius: 4px 0 0 4px; height: 42px; font-size: 14px;">
            <span class="input-group-btn">
              <button type="submit" class="btn btn-primary" style="background: #1e3a8a; border-color: #1e3a8a; border-radius: 0 4px 4px 0; height: 42px; padding: 0 20px; font-weight: 700;">
                <i class="fa fa-search"></i> Search
              </button>
            </span>
          </div>
        </form>
      </div>
    </div>

    <!-- Topics Results -->
    <div class="box" style="border: 1px solid #e2e8f0; border-radius: 4px; background: #ffffff;">
      <div class="box-header with-border" style="border-bottom: 1px solid #f1f5f9; padding: 14px 20px;">
        <h3 class="box-title" style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0;">
          <i class="fa fa-book" style="color: #1e3a8a; margin-right: 6px;"></i> Employee Handbook &amp; HR Topics
        </h3>
      </div>
      <div class="box-body" style="padding: 20px;">
        <?php if (!empty($topics)): ?>
          <div style="display: flex; flex-direction: column; gap: 16px;">
            <?php foreach ($topics as $tp): ?>
              <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 6px;">
                  <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #1e3a8a;">
                    <?php echo htmlspecialchars($tp['category']); ?>
                  </span>
                </div>
                <h4 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 6px 0;">
                  <?php echo htmlspecialchars($tp['title']); ?>
                </h4>
                <p style="font-size: 13.5px; color: #475569; line-height: 1.5; margin: 0;">
                  <?php echo htmlspecialchars($tp['summary']); ?>
                </p>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div style="padding: 30px; text-align: center; color: #94a3b8;">
            No HR topics matched your search query. Try searching for "leave", "attendance", "shift", or "insurance".
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</div>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
