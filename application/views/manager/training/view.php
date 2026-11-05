<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
$k = isset($kinds[$mod['content_type']]) ? $mod['content_type'] : 'guide';
$kd = $kinds[$k];
$done = (!empty($progress) && $progress['status'] === 'Completed');
$body = trim((string)$mod['body']);
$file_url = base_url('manager/self-training/file/' . (int)$mod['id']);
$file_ext = !empty($mod['file_path']) ? strtolower(pathinfo($mod['file_path'], PATHINFO_EXTENSION)) : '';
// YouTube links play right on the page.
$yt = '';
if (!empty($mod['content_url']) && preg_match('#(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{11})#', $mod['content_url'], $mm)) {
  $yt = $mm[1];
}
// Step-by-step guides: one step per line (leading "1.", "Step 1:", "-" are dropped).
$steps = array();
if ($k === 'guide' && $body !== '') {
  foreach (preg_split('/\r\n|\r|\n/', $body) as $line) {
    $line = trim(preg_replace('/^\s*(?:step\s*\d+\s*[:.)-]?|\d+\s*[.)-]|[-*\x{2022}])\s*/iu', '', $line));
    if ($line !== '') $steps[] = $line;
  }
}
?>
<style>
  .sv-page { max-width:980px; }
  .sv-page .sv-back { display:inline-flex; align-items:center; gap:6px; margin-bottom:12px; font-weight:600; }
  .sv-page .sv-head { background:#fff; border:1px solid #e2e8f0; border-top:4px solid <?php echo $kd[2]; ?>; border-radius:10px; padding:16px 18px; margin-bottom:14px; }
  .sv-page .sv-type { font-size:12px; font-weight:700; color:<?php echo $kd[2]; ?>; text-transform:uppercase; letter-spacing:.3px; }
  .sv-page .sv-title { margin:4px 0 6px; font-size:21px; font-weight:700; color:#0f172a; word-break:break-word; }
  .sv-page .sv-meta { display:flex; flex-wrap:wrap; gap:8px 14px; font-size:12.5px; color:#64748b; }
  .sv-page .sv-desc { margin:10px 0 0; color:#334155; }
  .sv-page .sv-actions { display:flex; gap:8px; flex-wrap:wrap; align-items:center; margin-top:14px; }
  .sv-page .sv-done { display:inline-flex; align-items:center; gap:6px; background:#dcfce7; color:#15803d; font-weight:700; border-radius:8px; padding:6px 12px; }
  .sv-page .sv-box { background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:16px 18px; margin-bottom:14px; }
  .sv-page .sv-box h4 { margin:0 0 12px; font-size:14px; font-weight:700; color:#0f172a; display:flex; align-items:center; gap:8px; justify-content:space-between; }
  .sv-page .sv-script { white-space:pre-wrap; font-size:14.5px; line-height:1.7; color:#1e293b; background:#faf5ff; border-left:4px solid #7c3aed; border-radius:6px; padding:14px 16px; word-break:break-word; }
  .sv-page .sv-text { white-space:pre-wrap; line-height:1.65; color:#1e293b; word-break:break-word; }
  .sv-page .sv-steps { list-style:none; margin:0; padding:0; counter-reset:step; }
  .sv-page .sv-steps li { counter-increment:step; display:flex; gap:12px; padding:10px 0; border-bottom:1px solid #f1f5f9; align-items:flex-start; }
  .sv-page .sv-steps li:last-child { border-bottom:0; }
  .sv-page .sv-steps li::before { content:counter(step); flex-shrink:0; width:28px; height:28px; border-radius:50%; background:#0891b2; color:#fff; font-weight:700; display:flex; align-items:center; justify-content:center; font-size:13px; }
  .sv-page .sv-steps li span { padding-top:4px; color:#1e293b; word-break:break-word; }
  .sv-page .sv-media { position:relative; width:100%; padding-top:56.25%; background:#000; border-radius:8px; overflow:hidden; }
  .sv-page .sv-media iframe { position:absolute; inset:0; width:100%; height:100%; border:0; }
  .sv-page video, .sv-page audio, .sv-page .sv-img { width:100%; border-radius:8px; }
  .sv-page .sv-pdf { width:100%; height:70vh; border:1px solid #e2e8f0; border-radius:8px; }
  .sv-page .sv-file { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
  .sv-page .sv-file i.fa-2x { color:#2563eb; }
  .sv-page .sv-file b { word-break:break-all; }
  .sv-page .sv-archived { background:#fef3c7; color:#92400e; border-radius:8px; padding:8px 12px; margin-bottom:12px; font-weight:600; }
  @media (max-width: 767px) {
    .sv-page .sv-title { font-size:18px; }
    .sv-page .sv-head, .sv-page .sv-box { padding:14px; }
    .sv-page .sv-pdf { height:60vh; }
  }
</style>

<div class="content-wrapper">
  <section class="content-header">
    <h1><?php echo $info['page_heading']; ?></h1>
  </section>
  <section class="content sv-page">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <a class="sv-back" href="<?php echo base_url('manager/self-training'); ?>"><i class="fa fa-arrow-left"></i> All training</a>
    <?php if ($mod['status'] !== 'active') { ?><div class="sv-archived"><i class="fa fa-archive"></i> Archived: users no longer see this.</div><?php } ?>

    <div class="sv-head">
      <span class="sv-type"><i class="fa <?php echo $kd[1]; ?>"></i> <?php echo $kd[0]; ?></span>
      <h2 class="sv-title"><?php echo htmlspecialchars($mod['title']); ?></h2>
      <div class="sv-meta">
        <span><i class="fa fa-folder-o"></i> <?php echo htmlspecialchars($mod['category']); ?></span>
        <?php if ((int)$mod['duration_minutes'] > 0) { ?><span><i class="fa fa-clock-o"></i> <?php echo (int)$mod['duration_minutes']; ?> min</span><?php } ?>
        <?php if ((int)$mod['is_mandatory'] === 1) { ?><span style="color:#b91c1c; font-weight:700;"><i class="fa fa-exclamation-circle"></i> Mandatory</span><?php } ?>
        <?php if (!empty($mod['due_date'])) { ?><span><i class="fa fa-calendar"></i> Due <?php echo date('d M Y', strtotime($mod['due_date'])); ?></span><?php } ?>
      </div>
      <?php if (trim((string)$mod['description']) !== '') { ?><p class="sv-desc"><?php echo nl2br(htmlspecialchars($mod['description'])); ?></p><?php } ?>
      <?php if ($mod['status'] === 'active') { ?>
      <div class="sv-actions">
        <?php if ($done) { ?>
          <span class="sv-done"><i class="fa fa-check-circle"></i> Done<?php echo !empty($progress['completed_at']) ? ' on ' . date('d M Y', strtotime($progress['completed_at'])) : ''; ?></span>
          <?php echo form_open('manager/self-training/done/' . (int)$mod['id'] . '/undo', array('style' => 'display:inline;')); ?>
            <button type="submit" class="btn btn-sm btn-link">Not done yet</button>
          <?php echo form_close(); ?>
        <?php } else { ?>
          <?php echo form_open('manager/self-training/done/' . (int)$mod['id'], array('style' => 'display:inline;')); ?>
            <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Mark as done</button>
          <?php echo form_close(); ?>
        <?php } ?>
        <?php if ($can_manage) { ?><a class="btn btn-sm btn-default" href="<?php echo base_url('manager/self-training/manage?edit=' . (int)$mod['id']); ?>"><i class="fa fa-pencil"></i> Edit</a><?php } ?>
      </div>
      <?php } ?>
    </div>

    <?php if ($yt !== '') { ?>
      <div class="sv-box"><div class="sv-media"><iframe src="https://www.youtube-nocookie.com/embed/<?php echo $yt; ?>" title="<?php echo htmlspecialchars($mod['title']); ?>" allow="accelerometer; encrypted-media; picture-in-picture" allowfullscreen></iframe></div></div>
    <?php } ?>

    <?php if ($file_ext !== '') { ?>
      <div class="sv-box">
        <?php if (in_array($file_ext, array('mp4', 'webm'), true)) { ?>
          <video controls preload="metadata" src="<?php echo $file_url; ?>"></video>
        <?php } elseif ($file_ext === 'mp3') { ?>
          <audio controls preload="metadata" src="<?php echo $file_url; ?>"></audio>
        <?php } elseif (in_array($file_ext, array('png', 'jpg', 'jpeg'), true)) { ?>
          <img class="sv-img" src="<?php echo $file_url; ?>" alt="<?php echo htmlspecialchars($mod['title']); ?>">
        <?php } elseif ($file_ext === 'pdf') { ?>
          <iframe class="sv-pdf hidden-xs" src="<?php echo $file_url; ?>" title="<?php echo htmlspecialchars($mod['file_name']); ?>"></iframe>
        <?php } ?>
        <div class="sv-file" style="<?php echo in_array($file_ext, array('mp4', 'webm', 'mp3', 'png', 'jpg', 'jpeg', 'pdf'), true) ? 'margin-top:10px;' : ''; ?>">
          <i class="fa fa-file-o fa-2x"></i>
          <b><?php echo htmlspecialchars($mod['file_name']); ?></b>
          <?php if ($file_inline) { ?><a class="btn btn-sm btn-primary" href="<?php echo $file_url; ?>" target="_blank" rel="noopener"><i class="fa fa-external-link"></i> Open</a><?php } ?>
          <a class="btn btn-sm btn-default" href="<?php echo $file_url; ?>?download=1"><i class="fa fa-download"></i> Download</a>
        </div>
      </div>
    <?php } ?>

    <?php if (!empty($mod['content_url']) && $yt === '') { ?>
      <div class="sv-box">
        <div class="sv-file">
          <i class="fa fa-link fa-2x"></i>
          <b><?php echo htmlspecialchars($mod['content_url']); ?></b>
          <a class="btn btn-sm btn-primary" href="<?php echo htmlspecialchars($mod['content_url']); ?>" target="_blank" rel="noopener noreferrer"><i class="fa fa-external-link"></i> Open link</a>
        </div>
      </div>
    <?php } ?>

    <?php if ($body !== '') { ?>
      <div class="sv-box">
        <?php if ($k === 'pitch') { ?>
          <h4><span><i class="fa fa-bullhorn"></i> Script</span><button type="button" class="btn btn-xs btn-default" id="svCopy"><i class="fa fa-copy"></i> Copy</button></h4>
          <div class="sv-script" id="svScript"><?php echo htmlspecialchars($body); ?></div>
        <?php } elseif ($k === 'guide' && count($steps) > 1) { ?>
          <h4><span><i class="fa fa-list-ol"></i> Steps</span><small class="text-muted"><?php echo count($steps); ?> steps</small></h4>
          <ol class="sv-steps"><?php foreach ($steps as $st) { ?><li><span><?php echo htmlspecialchars($st); ?></span></li><?php } ?></ol>
        <?php } else { ?>
          <h4><span><i class="fa fa-align-left"></i> Notes</span></h4>
          <div class="sv-text"><?php echo htmlspecialchars($body); ?></div>
        <?php } ?>
      </div>
    <?php } ?>
  </section>
</div>

<script>
$(function () {
  $('#svCopy').on('click', function () {
    var btn = $(this), text = $('#svScript').text();
    var ok = function () { btn.html('<i class="fa fa-check"></i> Copied'); setTimeout(function () { btn.html('<i class="fa fa-copy"></i> Copy'); }, 1500); };
    if (navigator.clipboard) { navigator.clipboard.writeText(text).then(ok); }
    else { var t = $('<textarea>').val(text).appendTo('body').select(); document.execCommand('copy'); t.remove(); ok(); }
  });
});
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
