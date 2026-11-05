<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
  $csrf_name = $this->security->get_csrf_token_name();
  $csrf_hash = $this->security->get_csrf_hash();
  $et = !empty($edit_topic) ? $edit_topic : array();
  $base = base_url('manager/hr/search_topics');
  $qs = function ($extra) use ($query, $category, $manage) {
    $p = array_filter(array('q' => $query, 'category' => $category, 'manage' => $manage ? '1' : ''), 'strlen');
    $p = array_merge($p, $extra);
    $p = array_filter($p, 'strlen');
    return $p ? '?' . http_build_query($p) : '';
  };
?>
<div class="content-wrapper">
  <section class="content-header crm-list-head">
    <h1><?php echo $info['page_heading']; ?></h1>
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
      <?php if (!empty($is_hr)) { ?>
        <a href="<?php echo $base . ($manage ? '' : '?manage=1'); ?>" class="btn btn-sm <?php echo $manage ? 'btn-primary' : 'btn-default'; ?>"><i class="fa fa-pencil"></i> <?php echo $manage ? 'Done managing' : 'Manage topics'; ?></a>
      <?php } ?>
      <a href="<?php echo base_url('manager/profile'); ?>" class="btn btn-sm btn-default"><i class="fa fa-user"></i> My Profile</a>
    </div>
  </section>

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="box crm-filter-box">
      <form class="crm-filter-bar" method="get" action="<?php echo $base; ?>">
        <?php if ($manage) { ?><input type="hidden" name="manage" value="1"><?php } ?>
        <div class="crm-filter-field is-wide">
          <label for="tpQ">Search</label>
          <input type="text" id="tpQ" name="q" class="form-control" placeholder="Leave, insurance, shift timings, code of conduct..." value="<?php echo htmlspecialchars($query ?? ''); ?>">
        </div>
        <div class="crm-filter-field">
          <label for="tpCat">Category</label>
          <select id="tpCat" name="category" class="form-control">
            <option value="">All categories</option>
            <?php foreach ($categories as $c) { ?><option value="<?php echo htmlspecialchars($c); ?>" <?php echo $category === $c ? 'selected' : ''; ?>><?php echo htmlspecialchars($c); ?></option><?php } ?>
          </select>
        </div>
        <div class="crm-filter-actions">
          <button type="submit" class="btn btn-primary">Search</button>
          <a href="<?php echo $base . ($manage ? '?manage=1' : ''); ?>" class="btn btn-default">Reset</a>
        </div>
      </form>
    </div>

    <div class="row">
      <div class="<?php echo $manage ? 'col-lg-8 col-md-7' : 'col-xs-12'; ?>">
        <div class="box crm-list-box">
          <div class="box-header with-border">
            <h3 class="box-title"><?php echo count($topics); ?> <?php echo count($topics) === 1 ? 'topic' : 'topics'; ?><?php echo ($query !== '' || $category !== '') ? ' found' : ''; ?></h3>
          </div>
          <div class="box-body">
            <?php if (!empty($topics)) { ?>
              <div class="tp-list">
                <?php foreach ($topics as $tp) { $archived = ($tp['status'] !== 'active'); ?>
                <details class="tp-item<?php echo $archived ? ' is-archived' : ''; ?>"<?php echo (count($topics) === 1) ? ' open' : ''; ?>>
                  <summary>
                    <span class="pf-item-type"><?php echo htmlspecialchars($tp['category']); ?><?php echo $archived ? ' &middot; Hidden' : ''; ?></span>
                    <span class="tp-title"><?php echo htmlspecialchars($tp['title']); ?></span>
                    <?php if (!empty($tp['summary'])) { ?><span class="tp-summary"><?php echo htmlspecialchars($tp['summary']); ?></span><?php } ?>
                  </summary>
                  <div class="tp-body">
                    <?php echo !empty($tp['body']) ? nl2br(htmlspecialchars($tp['body'])) : '<span class="text-muted">No further details yet. Contact HR for more information.</span>'; ?>
                    <div class="tp-foot">
                      <span class="crm-sub">Updated <?php echo !empty($tp['updated_at']) ? date('d M Y', strtotime($tp['updated_at'])) : '-'; ?></span>
                      <?php if ($manage) { ?>
                        <a href="<?php echo $base . $qs(array('edit' => (string)(int)$tp['id'])); ?>#topicForm" class="btn btn-warning btn-xs">Edit</a>
                        <form method="post" action="<?php echo base_url('manager/hr/topic_status/' . (int)$tp['id'] . '/' . ($archived ? 'active' : 'archived')); ?>" style="display:inline;">
                          <input type="hidden" name="<?php echo $csrf_name; ?>" value="<?php echo $csrf_hash; ?>">
                          <button type="submit" class="btn btn-default btn-xs"><?php echo $archived ? 'Show again' : 'Hide'; ?></button>
                        </form>
                      <?php } ?>
                    </div>
                  </div>
                </details>
                <?php } ?>
              </div>
            <?php } else { ?>
              <div class="crm-empty-state">No topics matched. Try words like "leave", "shift" or "insurance".</div>
            <?php } ?>
          </div>
        </div>
      </div>

      <?php if ($manage) { ?>
      <div class="col-lg-4 col-md-5">
        <div class="box crm-list-box" id="topicForm">
          <div class="box-header with-border"><h3 class="box-title"><?php echo !empty($et) ? 'Edit topic' : 'Add a topic'; ?></h3></div>
          <div class="box-body">
            <form method="post" action="<?php echo base_url('manager/hr/topic_save'); ?>">
              <input type="hidden" name="<?php echo $csrf_name; ?>" value="<?php echo $csrf_hash; ?>">
              <input type="hidden" name="id" value="<?php echo (int)($et['id'] ?? 0); ?>">
              <div class="form-group">
                <label for="tfTitle">Title <span class="text-danger">*</span></label>
                <input type="text" id="tfTitle" name="title" class="form-control" maxlength="255" required value="<?php echo htmlspecialchars($et['title'] ?? ''); ?>">
              </div>
              <div class="form-group">
                <label for="tfCat">Category</label>
                <input type="text" id="tfCat" name="category" class="form-control" maxlength="100" list="tfCats" value="<?php echo htmlspecialchars($et['category'] ?? ''); ?>">
                <datalist id="tfCats"><?php foreach ($categories as $c) { ?><option value="<?php echo htmlspecialchars($c); ?>"><?php } ?></datalist>
              </div>
              <div class="form-group">
                <label for="tfSum">Short summary</label>
                <textarea id="tfSum" name="summary" class="form-control" rows="2"><?php echo htmlspecialchars($et['summary'] ?? ''); ?></textarea>
              </div>
              <div class="form-group">
                <label for="tfBody">Full details</label>
                <textarea id="tfBody" name="body" class="form-control" rows="6"><?php echo htmlspecialchars($et['body'] ?? ''); ?></textarea>
              </div>
              <div style="display:flex; gap:8px;">
                <button type="submit" class="btn btn-primary" style="flex:1;"><?php echo !empty($et) ? 'Save changes' : 'Publish'; ?></button>
                <?php if (!empty($et)) { ?><a href="<?php echo $base; ?>?manage=1" class="btn btn-default">Cancel</a><?php } ?>
              </div>
            </form>
          </div>
        </div>
      </div>
      <?php } ?>
    </div>
  </section>
</div>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
