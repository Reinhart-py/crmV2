<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<div class="content-wrapper sx">
  <?php echo crm_tabbar('database', 'leads/import'); ?>
  <section class="sx-head">
    <h1>Import Database<small>Add many database records at once from a CSV spreadsheet</small></h1>
    <div class="sx-btns">
      <a href="<?php echo base_url(); ?>manager/leads/download_sample_csv" class="btn btn-default"><i class="fa fa-download"></i> Download template</a>
    </div>
  </section>

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'),'message'); ?>

    <div class="sx-narrow">
      <div class="sx-card">
        <div class="sx-card-h"><h3><i class="fa fa-upload"></i> Upload CSV file</h3></div>
        <?php if (!empty($can_import)) { ?>
        <form action="<?php echo base_url(); ?>manager/leads/import_action" method="post" enctype="multipart/form-data" id="import_form">
          <div class="sx-card-b">
            <label for="csv_file" class="sx-drop" id="drop_zone" style="display:block; cursor:pointer; font-weight:500;">
              <i class="fa fa-file-text-o"></i>
              <b id="file_selected_text">Choose a CSV file or drop it here</b>
              <small>Comma-separated file with a header row, like the template</small>
              <span class="btn btn-default btn-sm">Browse computer</span>
            </label>
            <input type="file" name="csv_file" id="csv_file" accept=".csv,text/csv" required style="position:absolute; left:-9999px;">
          </div>
          <div class="sx-card-b" style="padding-top:0;">
            <p class="sx-help"><i class="fa fa-info-circle"></i> Customer Name is required on every row. Columns are matched to fields by their header names, so start from the template. You will see every row and any duplicates before anything is saved.</p>
          </div>
          <div class="sx-card-f">
            <button type="submit" class="btn btn-primary" id="import_go"><i class="fa fa-search"></i> Upload and check</button>
            <a href="<?php echo base_url(); ?>manager/leads" class="btn btn-default">Cancel</a>
          </div>
        </form>
        <?php } else { ?>
          <div class="sx-card-b"><div class="sx-empty"><i class="fa fa-lock"></i> You do not have access to Import Database.</div></div>
        <?php } ?>
      </div>
    </div>

    <?php $h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }; $batches = isset($batches) ? $batches : array(); ?>
    <div class="sx-card">
      <div class="sx-card-h"><h3><i class="fa fa-history"></i> Recent imports</h3></div>
      <?php if (!$batches) { ?>
        <div class="sx-card-b"><div class="sx-empty"><i class="fa fa-inbox"></i> No imports yet.</div></div>
      <?php } else { ?>
      <div class="sx-table-wrap">
        <table class="sx-table sx-cards">
          <thead><tr><th>When</th><th>File</th><th>By</th><th>Imported</th><th>Skipped</th><th>Status</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($batches as $b) { ?>
            <tr>
              <td data-l="When" class="nw"><?php echo $h(date('d-m-Y H:i', strtotime($b['created_at']))); ?></td>
              <td data-l="File"><?php echo $h($b['file_name']); ?></td>
              <td data-l="By"><?php echo $h($b['by_name']); ?></td>
              <td data-l="Imported"><span><b><?php echo number_format((int)$b['imported']); ?></b> of <?php echo number_format((int)$b['total_rows']); ?></span></td>
              <td data-l="Skipped"><?php
                $sk = array();
                if ((int)$b['skipped_dups']) $sk[] = number_format((int)$b['skipped_dups']) . ' duplicates';
                if ((int)$b['errors']) $sk[] = number_format((int)$b['errors']) . ' with problems';
                echo $sk ? $h(implode(', ', $sk)) : '<span class="text-muted">-</span>';
              ?></td>
              <td data-l="Status"><?php if (!empty($b['undone_at'])) { ?>
                <span class="chip na">Undone</span>
                <div class="text-muted" style="font-size:12.5px;"><?php echo $h(date('d-m-Y H:i', strtotime($b['undone_at'])) . ($b['undone_name'] ? ' by ' . $b['undone_name'] : '') . ': ' . number_format((int)$b['undo_removed']) . ' removed' . ((int)$b['undo_kept'] ? ', ' . number_format((int)$b['undo_kept']) . ' kept (already worked on)' : '')); ?></div>
              <?php } else { ?><span class="chip ok">Imported</span><?php } ?></td>
              <td data-l="" class="nw"><?php if (!empty($b['can_undo'])) { ?>
                <form action="<?php echo base_url(); ?>manager/leads/import_undo" method="post" class="imp-undo" style="margin:0;">
                  <input type="hidden" name="batch_id" value="<?php echo (int)$b['id']; ?>">
                  <button type="submit" class="btn btn-default btn-sm" data-n="<?php echo (int)$b['imported']; ?>"><i class="fa fa-undo"></i> Undo</button>
                </form>
              <?php } ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
      <?php } ?>
    </div>
    <p class="sx-help"><i class="fa fa-info-circle"></i> Undo removes the records of that import that nobody has worked on yet. Records that were edited, moved, or have follow-ups, calls, meetings or orders are kept.</p>
  </section>
</div>

<script>
(function () {
  var input = document.getElementById('csv_file'), zone = document.getElementById('drop_zone');
  if (!input || !zone) return;
  function show() {
    var f = input.files && input.files[0];
    if (!f) return;
    var t = document.getElementById('file_selected_text');
    t.textContent = f.name + ' (' + (f.size / 1024).toFixed(1) + ' KB)';
    zone.classList.add('is-set');
  }
  input.addEventListener('change', show);
  ['dragenter', 'dragover'].forEach(function (e) { zone.addEventListener(e, function (ev) { ev.preventDefault(); zone.classList.add('is-over'); }); });
  ['dragleave', 'drop'].forEach(function (e) { zone.addEventListener(e, function (ev) { ev.preventDefault(); zone.classList.remove('is-over'); }); });
  zone.addEventListener('drop', function (ev) {
    if (ev.dataTransfer && ev.dataTransfer.files.length) { input.files = ev.dataTransfer.files; show(); }
  });
  var form = document.getElementById('import_form');
  if (form) form.addEventListener('submit', function () {
    var b = document.getElementById('import_go');
    setTimeout(function () { b.disabled = true; b.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Checking...'; }, 0);
  });
})();
document.querySelectorAll('form.imp-undo').forEach(function (f) {
  f.addEventListener('submit', function (ev) {
    var n = f.querySelector('button').getAttribute('data-n');
    if (!confirm('Undo this import? Up to ' + n + ' records will be removed. Records someone has already worked on are kept.')) ev.preventDefault();
  });
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
