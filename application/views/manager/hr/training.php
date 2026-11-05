<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
  $csrf_name = $this->security->get_csrf_token_name();
  $csrf_hash = $this->security->get_csrf_hash();
  $em = !empty($edit_module) ? $edit_module : array();
  $fmt_dur = function ($m) { $m = (int)$m; if ($m <= 0) return 'Self-paced'; return $m >= 60 ? floor($m / 60) . ' h' . ($m % 60 ? ' ' . ($m % 60) . ' min' : '') : $m . ' min'; };
?>
<div class="content-wrapper">
  <section class="content-header crm-list-head">
    <h1><?php echo $info['page_heading']; ?></h1>
    <a href="<?php echo base_url('manager/profile'); ?>" class="btn btn-sm btn-default"><i class="fa fa-user"></i> My Profile</a>
  </section>

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="crm-summary">
      <div><span>Assigned to me</span><strong><?php echo (int)$summary['assigned']; ?></strong></div>
      <div><span>Completed</span><strong><?php echo (int)$summary['completed']; ?></strong></div>
      <div><span>In progress</span><strong><?php echo (int)$summary['in_progress']; ?></strong></div>
      <div><span>Mandatory still open</span><strong><?php echo (int)$summary['mandatory_open']; ?></strong></div>
    </div>

    <div class="box crm-list-box">
      <div class="box-header with-border"><h3 class="box-title">My training</h3></div>
      <div class="box-body">
        <?php if (!empty($modules)) { ?>
        <div class="tr-grid">
          <?php foreach ($modules as $mod) {
            $st = $mod['my_status'] ?: 'Not started';
            $pct = ($st === 'Completed') ? 100 : (($st === 'In Progress') ? 50 : 0);
            $overdue = (!empty($mod['due_date']) && $st !== 'Completed' && $mod['due_date'] < date('Y-m-d'));
          ?>
          <div class="tr-card">
            <div class="tr-top">
              <span class="pf-item-type"><?php echo htmlspecialchars($mod['category']); ?></span>
              <span class="crm-status <?php echo $st === 'Completed' ? 'crm-status-ink' : ($st === 'In Progress' ? 'crm-status-ocean' : ''); ?>"><?php echo htmlspecialchars($st); ?></span>
            </div>
            <h4 class="tr-title"><?php echo htmlspecialchars($mod['title']); ?></h4>
            <?php if (!empty($mod['description'])) { ?><p class="tr-desc"><?php echo nl2br(htmlspecialchars($mod['description'])); ?></p><?php } ?>
            <div class="tr-bar" aria-hidden="true"><span style="width:<?php echo $pct; ?>%;"></span></div>
            <div class="tr-meta">
              <span><i class="fa fa-clock-o"></i> <?php echo $fmt_dur($mod['duration_minutes']); ?></span>
              <?php if ((int)$mod['is_mandatory'] === 1) { ?><span class="crm-status crm-status-danger">Mandatory</span><?php } ?>
              <?php if (!empty($mod['due_date'])) { ?><span<?php echo $overdue ? ' style="color:#dc2626;font-weight:700;"' : ''; ?>>Due <?php echo date('d M Y', strtotime($mod['due_date'])); ?><?php echo $overdue ? ' (overdue)' : ''; ?></span><?php } ?>
              <?php if (!empty($mod['completed_at'])) { ?><span>Completed <?php echo date('d M Y', strtotime($mod['completed_at'])); ?></span><?php } ?>
            </div>
            <div class="tr-actions">
              <?php if (!empty($mod['content_url'])) { ?>
                <?php if ($st === 'Not started') { ?>
                  <form method="post" action="<?php echo base_url('manager/hr/training_progress/' . (int)$mod['id'] . '/start'); ?>" class="js-open-course" data-url="<?php echo htmlspecialchars($mod['content_url']); ?>">
                    <input type="hidden" name="<?php echo $csrf_name; ?>" value="<?php echo $csrf_hash; ?>">
                    <button type="submit" class="btn btn-primary btn-sm">Start course</button>
                  </form>
                <?php } else { ?>
                  <a href="<?php echo htmlspecialchars($mod['content_url']); ?>" target="_blank" rel="noopener" class="btn btn-default btn-sm">Open material</a>
                <?php } ?>
              <?php } elseif ($st === 'Not started') { ?>
                <form method="post" action="<?php echo base_url('manager/hr/training_progress/' . (int)$mod['id'] . '/start'); ?>">
                  <input type="hidden" name="<?php echo $csrf_name; ?>" value="<?php echo $csrf_hash; ?>">
                  <button type="submit" class="btn btn-primary btn-sm">Start</button>
                </form>
              <?php } ?>
              <?php if ($st !== 'Completed') { ?>
                <form method="post" action="<?php echo base_url('manager/hr/training_progress/' . (int)$mod['id'] . '/complete'); ?>" onsubmit="return confirm('Mark this module as completed?');">
                  <input type="hidden" name="<?php echo $csrf_name; ?>" value="<?php echo $csrf_hash; ?>">
                  <button type="submit" class="btn btn-default btn-sm">Mark completed</button>
                </form>
              <?php } ?>
            </div>
          </div>
          <?php } ?>
        </div>
        <?php } else { ?>
          <div class="crm-empty-state">No training modules have been assigned to you yet.</div>
        <?php } ?>
      </div>
    </div>

    <?php if (!empty($is_hr)) { ?>
    <div class="row">
      <div class="col-lg-8 col-md-7">
        <div class="box crm-list-box">
          <div class="box-header with-border"><h3 class="box-title">Manage modules</h3></div>
          <div class="box-body table-responsive">
            <?php if (!empty($all_modules)) { ?>
            <table class="table table-bordered crm-list-table" style="width:100%;">
              <thead>
                <tr><th>Module</th><th>Audience</th><th>Due</th><th>Completed</th><th>Status</th><th>Action</th></tr>
              </thead>
              <tbody>
                <?php foreach ($all_modules as $am) {
                  $aud = (int)$am['audience_count'];
                  $done = (int)$am['completed_count'];
                ?>
                <tr>
                  <td>
                    <strong><?php echo htmlspecialchars($am['title']); ?></strong>
                    <div class="crm-sub"><?php echo htmlspecialchars($am['category']); ?><?php echo (int)$am['is_mandatory'] ? ' &middot; Mandatory' : ''; ?></div>
                  </td>
                  <td><?php echo (int)$am['audience_role_id'] > 0 ? htmlspecialchars($am['audience_title'] ?: 'Role #' . (int)$am['audience_role_id']) : 'Everyone'; ?></td>
                  <td><?php echo !empty($am['due_date']) ? date('d M Y', strtotime($am['due_date'])) : '-'; ?></td>
                  <td><?php echo $done; ?> / <?php echo $aud; ?><?php echo $aud > 0 ? ' (' . round($done * 100 / $aud) . '%)' : ''; ?></td>
                  <td><span class="crm-status <?php echo $am['status'] === 'active' ? 'crm-status-ocean' : ''; ?>"><?php echo $am['status'] === 'active' ? 'Active' : 'Archived'; ?></span></td>
                  <td style="white-space:nowrap;">
                    <a href="<?php echo base_url('manager/hr/training?edit=' . (int)$am['id']); ?>#trainingForm" class="btn btn-warning btn-xs">Edit</a>
                    <form method="post" action="<?php echo base_url('manager/hr/training_status/' . (int)$am['id'] . '/' . ($am['status'] === 'active' ? 'archived' : 'active')); ?>" style="display:inline;">
                      <input type="hidden" name="<?php echo $csrf_name; ?>" value="<?php echo $csrf_hash; ?>">
                      <button type="submit" class="btn btn-default btn-xs"><?php echo $am['status'] === 'active' ? 'Archive' : 'Restore'; ?></button>
                    </form>
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
            <?php } else { ?>
              <div class="crm-empty-state">No modules yet. Publish the first one with the form.</div>
            <?php } ?>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-5">
        <div class="box crm-list-box" id="trainingForm">
          <div class="box-header with-border"><h3 class="box-title"><?php echo !empty($em) ? 'Edit module' : 'Publish a module'; ?></h3></div>
          <div class="box-body">
            <form method="post" action="<?php echo base_url('manager/hr/training_save'); ?>">
              <input type="hidden" name="<?php echo $csrf_name; ?>" value="<?php echo $csrf_hash; ?>">
              <input type="hidden" name="id" value="<?php echo (int)($em['id'] ?? 0); ?>">
              <div class="form-group">
                <label for="tmTitle">Title <span class="text-danger">*</span></label>
                <input type="text" id="tmTitle" name="title" class="form-control" maxlength="255" required value="<?php echo htmlspecialchars($em['title'] ?? ''); ?>">
              </div>
              <div class="row">
                <div class="col-xs-6 form-group">
                  <label for="tmCat">Category</label>
                  <input type="text" id="tmCat" name="category" class="form-control" maxlength="100" placeholder="e.g. Compliance" value="<?php echo htmlspecialchars($em['category'] ?? ''); ?>">
                </div>
                <div class="col-xs-6 form-group">
                  <label for="tmDur">Duration (minutes)</label>
                  <input type="number" id="tmDur" name="duration_minutes" class="form-control" min="0" value="<?php echo (int)($em['duration_minutes'] ?? 0); ?>">
                </div>
              </div>
              <div class="form-group">
                <label for="tmUrl">Course / material link</label>
                <input type="url" id="tmUrl" name="content_url" class="form-control" placeholder="https://" value="<?php echo htmlspecialchars($em['content_url'] ?? ''); ?>">
              </div>
              <div class="form-group">
                <label for="tmDesc">Description</label>
                <textarea id="tmDesc" name="description" class="form-control" rows="3"><?php echo htmlspecialchars($em['description'] ?? ''); ?></textarea>
              </div>
              <div class="row">
                <div class="col-xs-6 form-group">
                  <label for="tmAud">Assign to</label>
                  <select id="tmAud" name="audience_role_id" class="form-control">
                    <option value="0">Everyone</option>
                    <?php foreach ($roles as $r) { ?>
                      <option value="<?php echo (int)$r['id']; ?>" <?php echo (int)($em['audience_role_id'] ?? 0) === (int)$r['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($r['title']); ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-xs-6 form-group">
                  <label for="tmDue">Due date</label>
                  <input type="date" id="tmDue" name="due_date" class="form-control" value="<?php echo htmlspecialchars($em['due_date'] ?? ''); ?>">
                </div>
              </div>
              <div class="checkbox" style="margin-top:0;">
                <label><input type="checkbox" name="is_mandatory" value="1" <?php echo !empty($em['is_mandatory']) ? 'checked' : ''; ?>> Mandatory</label>
              </div>
              <div style="display:flex; gap:8px;">
                <button type="submit" class="btn btn-primary" style="flex:1;"><?php echo !empty($em) ? 'Save changes' : 'Publish'; ?></button>
                <?php if (!empty($em)) { ?><a href="<?php echo base_url('manager/hr/training'); ?>" class="btn btn-default">Cancel</a><?php } ?>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <?php } ?>
  </section>
</div>
<script>
// Starting a course records progress and opens the material in a new tab.
document.querySelectorAll('.js-open-course').forEach(function (f) {
  f.addEventListener('submit', function () { window.open(f.getAttribute('data-url'), '_blank', 'noopener'); });
});
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
