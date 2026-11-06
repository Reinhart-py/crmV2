<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$c = $doc_counts;
$types = array();
foreach ($documents as $d) $types[$d['type']] = true;
foreach ($missing as $m) $types[$m['type']] = true;
ksort($types);
$who = function ($uid, $name, $emp_id) use ($h, $hr_file) {
  $n = $h($name);
  if ($hr_file) $n = '<a href="' . base_url() . 'manager/hr/employees/' . (int)$uid . '#identity">' . $n . '</a>';
  return $n . ($emp_id !== '' ? '<span class="text-muted" style="display:block;font-size:12px;">#' . $h($emp_id) . '</span>' : '');
};
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<div class="content-wrapper sx">
  <?php echo crm_tabbar('hr_people', 'hr/documents'); ?>
  <section class="sx-head">
    <h1>Documents &amp; Expiry<small>Passport, visa, Emirates ID, labour card and insurance of everyone working</small></h1>
    <div class="sx-btns">
      <a href="<?php echo base_url(); ?>manager/hr/documents?csv=1" class="btn btn-default"><i class="fa fa-download"></i> Download CSV</a>
      <?php if (!empty($can_add)) { ?><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#docAdd"><i class="fa fa-plus"></i> Add document</button><?php } ?>
    </div>
  </section>
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="sx-kpis" id="dcKpis">
      <button type="button" class="sx-kpi r" data-f="expired"><span class="ic"><i class="fa fa-exclamation-triangle"></i></span><div><b><?php echo number_format($c['expired']); ?></b><span>Expired</span></div></button>
      <button type="button" class="sx-kpi a" data-f="soon"><span class="ic"><i class="fa fa-clock-o"></i></span><div><b><?php echo number_format($c['soon']); ?></b><span>Expiring in <?php echo (int)$alert_days; ?> days</span></div></button>
      <button type="button" class="sx-kpi g" data-f="valid"><span class="ic"><i class="fa fa-check"></i></span><div><b><?php echo number_format($c['valid']); ?></b><span>Valid</span></div></button>
      <button type="button" class="sx-kpi s" data-f="missing"><span class="ic"><i class="fa fa-question"></i></span><div><b><?php echo number_format($c['missing']); ?></b><span>Missing (passport, visa, Emirates ID)</span></div></button>
    </div>

    <div class="sx-card">
      <div class="sx-bar">
        <input type="search" class="form-control sx-q" id="dcQ" placeholder="Employee or document number" autocomplete="off">
        <select class="form-control" id="dcType" aria-label="Document"><option value="">All documents</option><?php foreach (array_keys($types) as $t) { ?><option value="<?php echo $h($t); ?>"><?php echo $h($t); ?></option><?php } ?></select>
        <select class="form-control" id="dcState" aria-label="Status">
          <option value="">Any status</option><option value="expired">Expired</option><option value="soon">Expiring soon</option><option value="valid">Valid</option><option value="none">No expiry date</option><option value="missing">Missing</option>
        </select>
        <span class="sx-count" id="dcCount"></span>
      </div>
      <div class="sx-table-wrap">
        <table class="sx-table sx-cards" id="dcTable">
          <thead><tr><th>Employee</th><th>Document</th><th>Number</th><th>Expiry</th><th>Status</th><th>File</th></tr></thead>
          <tbody>
          <?php foreach ($documents as $d) { ?>
            <tr data-s="<?php echo $d['state']; ?>" data-t="<?php echo $h($d['type']); ?>" data-q="<?php echo $h(strtolower($d['name'] . ' ' . $d['emp_id'] . ' ' . $d['number'])); ?>">
              <td data-l="Employee"><?php echo $who($d['user_id'], $d['name'], $d['emp_id']); ?></td>
              <td data-l="Document"><b><?php echo $h($d['type']); ?></b></td>
              <td data-l="Number" class="mono"><?php echo $d['number'] !== '' ? $h($d['number']) : '<span class="text-muted">-</span>'; ?></td>
              <td data-l="Expiry" class="nw"><?php echo $d['expiry'] ? $h(date('d-m-Y', strtotime($d['expiry']))) : '<span class="text-muted">-</span>'; ?></td>
              <td data-l="Status"><?php
                if ($d['state'] === 'expired') echo '<span class="chip x">Expired ' . abs($d['days']) . ' days ago</span>';
                elseif ($d['state'] === 'soon') echo '<span class="chip w">' . ($d['days'] === 0 ? 'Expires today' : 'In ' . $d['days'] . ' days') . '</span>';
                elseif ($d['state'] === 'valid') echo '<span class="chip ok">Valid</span>';
                else echo '<span class="chip na">No expiry date</span>';
              ?></td>
              <td data-l="File" class="nw"><?php
                if ($d['file'] && $file_base) echo '<a class="btn btn-default btn-sm" target="_blank" rel="noopener" href="' . base_url() . $file_base . 'doc_file/' . (int)$d['file']['id'] . '"><i class="fa fa-eye"></i> View</a>';
                elseif ($d['file']) echo '<span class="chip ok">Uploaded</span>';
                else echo '<span class="text-muted">None</span>';
              ?></td>
            </tr>
          <?php } ?>
          <?php foreach ($missing as $m) { ?>
            <tr data-s="missing" data-t="<?php echo $h($m['type']); ?>" data-q="<?php echo $h(strtolower($m['name'] . ' ' . $m['emp_id'])); ?>">
              <td data-l="Employee"><?php echo $who($m['user_id'], $m['name'], $m['emp_id']); ?></td>
              <td data-l="Document"><b><?php echo $h($m['type']); ?></b></td>
              <td data-l="Number"><span class="text-muted">-</span></td>
              <td data-l="Expiry"><span class="text-muted">-</span></td>
              <td data-l="Status"><span class="chip na">Missing</span></td>
              <td data-l="File"><span class="text-muted">None</span></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
        <div class="sx-empty" id="dcNone" style="display:none;"><i class="fa fa-search"></i> No documents match.</div>
      </div>
    </div>
    <p class="sx-help"><i class="fa fa-info-circle"></i> Numbers and expiry dates are kept in each employee's HR file (Identity). The warning period (<?php echo (int)$alert_days; ?> days) is set in HR Settings.</p>
  </section>
</div>

<?php if (!empty($can_add)) { ?>
<div class="modal fade" id="docAdd" tabindex="-1" role="dialog" aria-labelledby="docAddT">
  <div class="modal-dialog" role="document"><form class="modal-content" method="post" action="<?php echo base_url(); ?>manager/hr/documents/add">
    <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="docAddT">Add another document</h4></div>
    <div class="modal-body sx">
      <p class="text-muted" style="font-size:13px;">For other documents (driving licence, certificates ...). Passport, visa, Emirates ID, labour card and insurance go in the employee's HR file.</p>
      <div class="form-group"><label for="da1">Employee</label><select id="da1" name="user_id" class="form-control" required><option value="">Choose</option><?php foreach ($employees as $u) { ?><option value="<?php echo (int)$u['id']; ?>"><?php echo $h($u['name']); ?></option><?php } ?></select></div>
      <div class="form-group"><label for="da2">Document</label><input id="da2" name="doc_type" class="form-control" required maxlength="60" placeholder="e.g. Driving licence"></div>
      <div class="form-group"><label for="da3">Number</label><input id="da3" name="doc_number" class="form-control" maxlength="60"></div>
      <div class="form-group"><label for="da4">Issue date</label><input id="da4" type="date" name="issue_date" class="form-control"></div>
      <div class="form-group"><label for="da5">Expiry date</label><input id="da5" type="date" name="expiry_date" class="form-control"></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Add document</button></div>
  </form></div>
</div>
<?php } ?>
<script>
(function () {
  var rows = [].slice.call(document.querySelectorAll('#dcTable tbody tr'));
  var q = document.getElementById('dcQ'), ty = document.getElementById('dcType'), st = document.getElementById('dcState');
  function run() {
    var s = q.value.trim().toLowerCase(), t = ty.value, ss = st.value, n = 0;
    rows.forEach(function (r) {
      var on = (!s || r.getAttribute('data-q').indexOf(s) !== -1) && (!t || r.getAttribute('data-t') === t)
        && (ss ? r.getAttribute('data-s') === ss : r.getAttribute('data-s') !== 'missing');
      r.style.display = on ? '' : 'none'; if (on) n++;
    });
    document.getElementById('dcCount').textContent = n + (n === 1 ? ' document' : ' documents');
    document.getElementById('dcNone').style.display = n ? 'none' : '';
    document.querySelectorAll('#dcKpis .sx-kpi').forEach(function (b) { b.classList.toggle('is-on', b.getAttribute('data-f') === ss); });
  }
  q.addEventListener('input', run); ty.addEventListener('change', run); st.addEventListener('change', run);
  document.querySelectorAll('#dcKpis .sx-kpi').forEach(function (b) { b.addEventListener('click', function () { st.value = st.value === b.getAttribute('data-f') ? '' : b.getAttribute('data-f'); run(); }); });
  run();
})();
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
