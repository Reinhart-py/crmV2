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
            <p class="sx-help"><i class="fa fa-info-circle"></i> Customer Name is required on every row. Columns are matched to fields by their header names, so start from the template.</p>
          </div>
          <div class="sx-card-f">
            <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> Import records</button>
            <a href="<?php echo base_url(); ?>manager/leads" class="btn btn-default">Cancel</a>
          </div>
        </form>
        <?php } else { ?>
          <div class="sx-card-b"><div class="sx-empty"><i class="fa fa-lock"></i> You do not have access to Import Database.</div></div>
        <?php } ?>
      </div>
    </div>
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
})();
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
