<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
$e = $edit;
$ev = function ($f, $d = '') use ($e) { return htmlspecialchars(isset($e[$f]) && $e[$f] !== null ? (string)$e[$f] : $d); };
$cur_type = !empty($e['content_type']) ? $e['content_type'] : 'pitch';
?>
<style>
  .sm-page .sm-tabs { display:flex; gap:6px; margin-bottom:14px; flex-wrap:wrap; }
  .sm-page .sm-tabs a { padding:7px 14px; border:1px solid #cbd5e1; border-radius:8px; font-weight:700; color:#475569; background:#fff; text-decoration:none; }
  .sm-page .sm-tabs a.active { background:#1e3a8a; border-color:#1e3a8a; color:#fff; }
  .sm-page .sm-layout { display:grid; grid-template-columns:minmax(0, 5fr) minmax(0, 7fr); gap:14px; align-items:start; }
  .sm-page .sm-card { background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:16px; min-width:0; }
  .sm-page .sm-card h4 { margin:0 0 12px; font-size:15px; font-weight:700; color:#0f172a; display:flex; justify-content:space-between; align-items:center; gap:8px; }
  .sm-page .sm-row { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
  .sm-page .form-group { margin-bottom:10px; }
  .sm-page label { font-size:12px; font-weight:700; color:#334155; margin-bottom:4px; }
  .sm-page .sm-types { display:grid; grid-template-columns:repeat(5, minmax(0, 1fr)); gap:6px; }
  .sm-page .sm-types label { margin:0; cursor:pointer; }
  .sm-page .sm-types input { position:absolute; opacity:0; }
  .sm-page .sm-types span { display:flex; flex-direction:column; align-items:center; gap:4px; text-align:center; border:1px solid #cbd5e1; border-radius:8px; padding:8px 4px; font-size:11px; font-weight:700; color:#475569; height:100%; }
  .sm-page .sm-types span i { font-size:17px; color:var(--c); }
  .sm-page .sm-types input:checked + span { border-color:var(--c); box-shadow:0 0 0 2px var(--c); color:#0f172a; }
  .sm-page .sm-types input:focus-visible + span { outline:2px solid #1e3a8a; outline-offset:2px; }
  .sm-page .sm-hint { font-size:11.5px; color:#64748b; margin-top:3px; }
  .sm-page .sm-list { display:flex; flex-direction:column; gap:8px; }
  .sm-page .sm-item { border:1px solid #e2e8f0; border-left:4px solid var(--c); border-radius:8px; padding:10px 12px; display:flex; gap:10px; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; }
  .sm-page .sm-item.is-archived { opacity:.6; background:#f8fafc; }
  .sm-page .sm-item-main { min-width:0; flex:1 1 220px; }
  .sm-page .sm-item-main b { color:#0f172a; word-break:break-word; }
  .sm-page .sm-item-meta { font-size:11.5px; color:#64748b; display:flex; flex-wrap:wrap; gap:4px 10px; margin-top:3px; }
  .sm-page .sm-item-act { display:flex; gap:4px; flex-wrap:wrap; align-items:center; }
  .sm-page .sm-bar { height:6px; background:#e2e8f0; border-radius:4px; overflow:hidden; min-width:80px; }
  .sm-page .sm-bar i { display:block; height:100%; background:#16a34a; }
  .sm-page .sm-table { width:100%; }
  .sm-page .sm-table th { font-size:11.5px; color:#64748b; text-transform:uppercase; font-weight:700; border-bottom:1px solid #e2e8f0; padding:6px 8px; }
  .sm-page .sm-table td { padding:8px; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
  .sm-page .sm-st { border-radius:10px; padding:2px 8px; font-weight:700; font-size:11px; white-space:nowrap; }
  .sm-page .sm-st.done { background:#dcfce7; color:#15803d; }
  .sm-page .sm-st.prog { background:#fef3c7; color:#b45309; }
  .sm-page .sm-st.none { background:#f1f5f9; color:#64748b; }
  .sm-page .sm-progress { margin-bottom:14px; border-color:#1e3a8a; }
  .sm-page .sm-scroll { max-height:360px; overflow-y:auto; }
  .sm-page .sm-scroll thead th { position:sticky; top:0; background:#fff; }
  .sm-page .sm-hidden { display:none !important; }
  @media (max-width: 991px) {
    .sm-page .sm-layout { grid-template-columns:1fr; }
  }
  @media (max-width: 767px) {
    .sm-page .sm-types { grid-template-columns:repeat(3, minmax(0, 1fr)); }
    .sm-page .sm-row { grid-template-columns:1fr; }
    .sm-page .sm-table thead { display:none; }
    .sm-page .sm-table tr { display:block; border-bottom:1px solid #e2e8f0; padding:6px 0; }
    .sm-page .sm-table td { display:block; border:0; padding:2px 4px; }
    .sm-page .sm-table td[data-label]::before { content:attr(data-label) ": "; color:#64748b; font-size:12px; }
  }
</style>

<div class="content-wrapper">
  <section class="content-header">
    <h1><?php echo $info['page_heading']; ?></h1>
  </section>
  <section class="content sm-page">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="sm-tabs" role="tablist">
      <a href="#material" data-tab="material" class="active">Material</a>
      <a href="#people" data-tab="people">People progress</a>
      <a href="<?php echo base_url('manager/self-training'); ?>" style="margin-left:auto;"><i class="fa fa-eye"></i> Open library</a>
    </div>

    <?php if (!empty($progress_mod)) {
      $pd = 0; foreach ($progress_people as $p) { if ($p['status'] === 'Completed') $pd++; } ?>
      <div class="sm-card sm-progress" id="smProgress">
        <h4><span><i class="fa fa-users"></i> Progress: <?php echo htmlspecialchars($progress_mod['title']); ?></span>
          <a href="<?php echo base_url('manager/self-training/manage'); ?>" class="btn btn-xs btn-default" aria-label="Close"><i class="fa fa-times"></i></a></h4>
        <p class="text-muted" style="margin-top:-6px;"><?php echo $pd; ?> of <?php echo count($progress_people); ?> people done.</p>
        <?php if (empty($progress_people)) { ?><p class="text-muted">No active users are in this audience.</p><?php } else { ?>
        <div class="sm-scroll">
        <table class="sm-table">
          <thead><tr><th>Name</th><th>Role</th><th>Status</th><th>Started</th><th>Done</th></tr></thead>
          <tbody>
          <?php foreach ($progress_people as $p) {
            $cls = $p['status'] === 'Completed' ? 'done' : ($p['status'] ? 'prog' : 'none');
            $lbl = $p['status'] === 'Completed' ? 'Done' : ($p['status'] ? 'Started' : 'Not opened'); ?>
            <tr>
              <td><b><?php echo htmlspecialchars($p['name']); ?></b></td>
              <td><?php echo htmlspecialchars((string)$p['role_title']); ?></td>
              <td><span class="sm-st <?php echo $cls; ?>"><?php echo $lbl; ?></span></td>
              <td data-label="Started"><?php echo !empty($p['started_at']) ? date('d M Y', strtotime($p['started_at'])) : '-'; ?></td>
              <td data-label="Done"><?php echo !empty($p['completed_at']) ? date('d M Y', strtotime($p['completed_at'])) : '-'; ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
        </div>
        <?php } ?>
      </div>
    <?php } ?>

    <div data-pane="material">
      <div class="sm-layout">
        <div class="sm-card" id="smForm">
          <h4><span><?php echo !empty($e) ? '<i class="fa fa-pencil"></i> Edit material' : '<i class="fa fa-plus"></i> Add material'; ?></span>
            <?php if (!empty($e)) { ?><a href="<?php echo base_url('manager/self-training/manage'); ?>" class="btn btn-xs btn-default">Cancel</a><?php } ?></h4>
          <?php echo form_open_multipart('manager/self-training/save'); ?>
            <input type="hidden" name="id" value="<?php echo !empty($e) ? (int)$e['id'] : 0; ?>">
            <div class="form-group">
              <label>Type</label>
              <div class="sm-types">
                <?php foreach ($kinds as $k => $kd) { ?>
                  <label style="--c:<?php echo $kd[2]; ?>;"><input type="radio" name="content_type" value="<?php echo $k; ?>" <?php echo $cur_type === $k ? 'checked' : ''; ?>><span><i class="fa <?php echo $kd[1]; ?>"></i><?php echo $kd[0]; ?></span></label>
                <?php } ?>
              </div>
            </div>
            <div class="form-group">
              <label for="smTitle">Title</label>
              <input type="text" id="smTitle" name="title" class="form-control" maxlength="255" required value="<?php echo $ev('title'); ?>" placeholder="e.g. Cold call opening pitch">
            </div>
            <div class="sm-row">
              <div class="form-group">
                <label for="smCat">Category</label>
                <input type="text" id="smCat" name="category" class="form-control" maxlength="100" list="smCats" value="<?php echo $ev('category'); ?>" placeholder="e.g. Sales">
                <datalist id="smCats"><?php foreach ($categories as $c) { ?><option value="<?php echo htmlspecialchars($c); ?>"><?php } ?></datalist>
              </div>
              <div class="form-group">
                <label for="smAud">Who can see it</label>
                <select id="smAud" name="audience_role_id" class="form-control">
                  <option value="0">Everyone</option>
                  <?php foreach ($roles as $r) { ?><option value="<?php echo (int)$r['id']; ?>" <?php echo (!empty($e) && (int)$e['audience_role_id'] === (int)$r['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($r['title']); ?> only</option><?php } ?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="smDesc">Short description</label>
              <textarea id="smDesc" name="description" class="form-control" rows="2" placeholder="What will they learn?"><?php echo $ev('description'); ?></textarea>
            </div>
            <div class="form-group">
              <label for="smBody" id="smBodyLabel">Content</label>
              <textarea id="smBody" name="body" class="form-control" rows="7"><?php echo $ev('body'); ?></textarea>
              <div class="sm-hint" id="smBodyHint"></div>
            </div>
            <div class="form-group">
              <label for="smUrl">Link <small class="text-muted">(YouTube links play on the page)</small></label>
              <input type="text" id="smUrl" name="content_url" class="form-control" maxlength="500" value="<?php echo $ev('content_url'); ?>" placeholder="https://...">
            </div>
            <div class="form-group">
              <label for="smFile">File <small class="text-muted">(max <?php echo (int)$max_mb; ?> MB)</small></label>
              <?php if (!empty($e['file_path'])) { ?>
                <div class="sm-hint" style="margin:0 0 6px;"><i class="fa fa-paperclip"></i> <?php echo htmlspecialchars($e['file_name']); ?> &middot; <label style="font-weight:600; display:inline;"><input type="checkbox" name="remove_file" value="1"> Remove</label> &middot; or choose a new file to replace it</div>
              <?php } ?>
              <input type="file" id="smFile" name="file" class="form-control" accept=".<?php echo implode(',.', $file_exts); ?>">
              <div class="sm-hint"><?php echo strtoupper(implode(', ', $file_exts)); ?></div>
            </div>
            <div class="sm-row">
              <div class="form-group">
                <label for="smMin">Time to learn (minutes)</label>
                <input type="number" id="smMin" name="duration_minutes" class="form-control" min="0" max="10000" value="<?php echo $ev('duration_minutes', '0'); ?>">
              </div>
              <div class="form-group">
                <label for="smDue">Finish by <small class="text-muted">(optional)</small></label>
                <input type="date" id="smDue" name="due_date" class="form-control" value="<?php echo $ev('due_date'); ?>">
              </div>
            </div>
            <div class="form-group">
              <label style="font-weight:600;"><input type="checkbox" name="is_mandatory" value="1" <?php echo !empty($e['is_mandatory']) ? 'checked' : ''; ?>> Mandatory</label>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> <?php echo !empty($e) ? 'Save changes' : 'Publish'; ?></button>
          <?php echo form_close(); ?>
        </div>

        <div class="sm-card">
          <h4><span><i class="fa fa-book"></i> Library</span><small class="text-muted"><?php echo count($modules); ?> items</small></h4>
          <?php if (empty($modules)) { ?>
            <p class="text-muted">Nothing yet. Add the first pitch script or guide.</p>
          <?php } else { ?>
            <div class="sm-list">
              <?php foreach ($modules as $m) {
                $k = isset($kinds[$m['content_type']]) ? $m['content_type'] : 'guide';
                $kd = $kinds[$k];
                $active = $m['status'] === 'active';
                $pct = $m['audience_count'] > 0 ? round($m['completed_count'] * 100 / $m['audience_count']) : 0; ?>
                <div class="sm-item<?php echo $active ? '' : ' is-archived'; ?>" style="--c:<?php echo $kd[2]; ?>;">
                  <div class="sm-item-main">
                    <b><i class="fa <?php echo $kd[1]; ?>" style="color:<?php echo $kd[2]; ?>;"></i> <?php echo htmlspecialchars($m['title']); ?></b>
                    <div class="sm-item-meta">
                      <span><?php echo $kd[0]; ?></span>
                      <span><?php echo htmlspecialchars($m['category']); ?></span>
                      <span><?php echo (int)$m['audience_role_id'] > 0 ? htmlspecialchars((string)$m['audience_title']) . ' only' : 'Everyone'; ?></span>
                      <?php if ((int)$m['is_mandatory'] === 1) { ?><span style="color:#b91c1c; font-weight:700;">Mandatory</span><?php } ?>
                      <?php if (!$active) { ?><span style="font-weight:700;">Archived</span><?php } ?>
                    </div>
                    <div style="display:flex; align-items:center; gap:8px; margin-top:6px; font-size:11.5px; color:#475569;">
                      <div class="sm-bar" style="flex:1;"><i style="width:<?php echo $pct; ?>%"></i></div>
                      <span><?php echo (int)$m['completed_count']; ?>/<?php echo (int)$m['audience_count']; ?> done</span>
                    </div>
                  </div>
                  <div class="sm-item-act">
                    <a class="btn btn-xs btn-default" href="<?php echo base_url('manager/self-training/manage?progress=' . (int)$m['id']); ?>" title="Who has done it"><i class="fa fa-users"></i></a>
                    <a class="btn btn-xs btn-default" href="<?php echo base_url('manager/self-training/view/' . (int)$m['id']); ?>" title="Open"><i class="fa fa-eye"></i></a>
                    <a class="btn btn-xs btn-default" href="<?php echo base_url('manager/self-training/manage?edit=' . (int)$m['id']); ?>" title="Edit"><i class="fa fa-pencil"></i></a>
                    <?php echo form_open('manager/self-training/status/' . (int)$m['id'] . '/' . ($active ? 'archived' : 'active'), array('style' => 'display:inline;')); ?>
                      <button type="submit" class="btn btn-xs btn-default" title="<?php echo $active ? 'Archive' : 'Restore'; ?>"><i class="fa <?php echo $active ? 'fa-archive' : 'fa-undo'; ?>"></i></button>
                    <?php echo form_close(); ?>
                  </div>
                </div>
              <?php } ?>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>

    <div data-pane="people" class="sm-hidden">
      <div class="sm-card">
        <h4><span><i class="fa fa-users"></i> People progress</span><input type="search" id="smPeopleQ" class="form-control input-sm" style="max-width:220px;" placeholder="Search name..." aria-label="Search name"></h4>
        <div class="sm-scroll" style="max-height:560px;">
        <table class="sm-table" id="smPeople">
          <thead><tr><th>Name</th><th>Role</th><th style="width:40%;">Done</th><th>Mandatory open</th></tr></thead>
          <tbody>
          <?php foreach ($people as $p) { $pct = $p['assigned'] > 0 ? round($p['completed'] * 100 / $p['assigned']) : 0; ?>
            <tr data-name="<?php echo htmlspecialchars(strtolower($p['name'])); ?>">
              <td><b><?php echo htmlspecialchars($p['name']); ?></b></td>
              <td><?php echo htmlspecialchars((string)$p['role_title']); ?></td>
              <td><div style="display:flex; align-items:center; gap:8px;"><div class="sm-bar" style="flex:1;"><i style="width:<?php echo $pct; ?>%"></i></div><span style="font-size:12px; white-space:nowrap;"><?php echo $p['completed']; ?>/<?php echo $p['assigned']; ?></span></div></td>
              <td><?php echo $p['mandatory_open'] > 0 ? '<span class="sm-st prog">' . $p['mandatory_open'] . ' open</span>' : '<span class="sm-st done">None</span>'; ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
$(function () {
  var hints = {
    pitch: ['Script', 'Write the pitch the way it should be said. Users can copy it.'],
    guide: ['Steps', 'One step per line. They are shown as numbered steps.'],
    document: ['Notes', 'Optional notes. Upload the document below.'],
    video: ['Notes', 'Optional notes. Add a YouTube link or upload an MP4 below.'],
    link: ['Notes', 'Optional notes. Add the link below.']
  };
  function setType() {
    var t = $('input[name=content_type]:checked').val() || 'pitch';
    $('#smBodyLabel').text(hints[t][0]);
    $('#smBodyHint').text(hints[t][1]);
  }
  $('input[name=content_type]').on('change', setType);
  setType();

  function showTab(t) {
    $('.sm-tabs a[data-tab]').removeClass('active').filter('[data-tab=' + t + ']').addClass('active');
    $('[data-pane]').addClass('sm-hidden').filter('[data-pane=' + t + ']').removeClass('sm-hidden');
  }
  $('.sm-tabs a[data-tab]').on('click', function (ev) {
    ev.preventDefault();
    var t = $(this).data('tab');
    showTab(t);
    if (history.replaceState) history.replaceState(null, '', '#' + t);
  });
  if (location.hash === '#people') showTab('people');

  $('#smPeopleQ').on('input', function () {
    var q = $.trim(this.value).toLowerCase();
    $('#smPeople tbody tr').each(function () { $(this).toggleClass('sm-hidden', q !== '' && String($(this).data('name')).indexOf(q) === -1); });
  });
});
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
