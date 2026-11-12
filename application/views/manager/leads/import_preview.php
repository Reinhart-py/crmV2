<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$c = $counts;
$state_chip = array('ok' => array('ok', 'Ready'), 'dup' => array('w', 'Duplicate'), 'err' => array('x', 'Problem'));
$show_max = 1000;
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<style>
  .ip-opts { display: flex; gap: 10px; flex-wrap: wrap; }
  .ip-opts label { display: flex; gap: 10px; align-items: flex-start; flex: 1 1 260px; padding: 10px 12px; border: 1px solid var(--crm-line, #e2e8f0); border-radius: 10px; cursor: pointer; font-weight: 500 !important; margin: 0; }
  .ip-opts label.is-on { border-color: var(--crm-ocean, #1e3a8a); background: var(--crm-ocean-soft, rgba(30, 58, 138, .06)); }
  .ip-opts input { margin-top: 3px; }
  .ip-opts b { display: block; font-size: 13.5px; }
  .ip-opts small { color: var(--crm-muted, #64748b); }
  .ip-foot { position: sticky; bottom: 0; z-index: 5; background: var(--crm-surface, #fff); display: flex; align-items: center; gap: 10px; flex-wrap: wrap; justify-content: space-between; }
  .ip-sum { font-size: 14px; }
  .ip-sum b { font-size: 18px; }
  .ip-why { color: var(--crm-muted, #64748b); font-size: 13px; }
  tr.st-err .ip-why { color: #b91c1c; }
  tr.st-dup .ip-why { color: #b45309; }
  @media (max-width: 767px) { .ip-foot .btn { flex: 1; } .ip-sum { width: 100%; } }
</style>
<div class="content-wrapper sx">
  <?php echo crm_tabbar('database', 'leads/import'); ?>
  <section class="sx-head">
    <h1>Check before importing<small><?php echo $h($file_name); ?>: nothing has been saved yet</small></h1>
  </section>

  <section class="content">
    <div class="sx-kpis">
      <div class="sx-kpi s"><span class="ic"><i class="fa fa-file-text-o"></i></span><div><b><?php echo number_format($c['total']); ?></b><span>Rows in the file</span></div></div>
      <div class="sx-kpi g"><span class="ic"><i class="fa fa-check"></i></span><div><b><?php echo number_format($c['ok']); ?></b><span>Ready to import</span></div></div>
      <div class="sx-kpi a"><span class="ic"><i class="fa fa-clone"></i></span><div><b><?php echo number_format($c['dup']); ?></b><span>Possible duplicates</span></div></div>
      <div class="sx-kpi r"><span class="ic"><i class="fa fa-exclamation-triangle"></i></span><div><b><?php echo number_format($c['err']); ?></b><span>Rows with problems (skipped)</span></div></div>
    </div>

    <form action="<?php echo base_url(); ?>manager/leads/import_confirm" method="post" id="ipForm" class="sx-card">
      <input type="hidden" name="token" value="<?php echo $h($token); ?>">
      <?php if ($c['dup'] > 0) { ?>
      <div class="sx-card-b">
        <div class="ip-opts">
          <label class="is-on"><input type="radio" name="dups" value="skip" checked><span><b>Skip duplicates</b><small>Import only the <?php echo number_format($c['ok']); ?> new records</small></span></label>
          <label><input type="radio" name="dups" value="import"><span><b>Import duplicates too</b><small>Also add the <?php echo number_format($c['dup']); ?> rows that match an existing record or an earlier row</small></span></label>
        </div>
      </div>
      <?php } ?>

      <div class="sx-bar">
        <div class="sx-chips" id="ipChips">
          <button type="button" class="is-on" data-f="all">All <span class="n"><?php echo number_format($c['total']); ?></span></button>
          <button type="button" data-f="ok">Ready <span class="n"><?php echo number_format($c['ok']); ?></span></button>
          <button type="button" data-f="dup">Duplicates <span class="n"><?php echo number_format($c['dup']); ?></span></button>
          <button type="button" data-f="err">Problems <span class="n"><?php echo number_format($c['err']); ?></span></button>
        </div>
        <?php if ($c['total'] > $show_max) { ?><span class="sx-count">Showing the first <?php echo number_format($show_max); ?> rows</span><?php } ?>
      </div>
      <div class="sx-table-wrap">
        <table class="sx-table sx-cards" id="ipTable">
          <thead><tr><th>Row</th><th>Result</th><th>Company</th><th>Contact</th><th>Phone</th><th>Email</th><th>Assigned to</th><th>Why</th></tr></thead>
          <tbody>
          <?php foreach (array_slice($rows, 0, $show_max) as $r) { $sc = $state_chip[$r['state']]; ?>
            <tr class="st-<?php echo $r['state']; ?>" data-s="<?php echo $r['state']; ?>">
              <td data-l="Row" class="mono"><?php echo (int)$r['line']; ?></td>
              <td data-l="Result"><span class="chip <?php echo $sc[0]; ?>"><?php echo $sc[1]; ?></span></td>
              <td data-l="Company"><?php echo $r['customer'] !== '' ? $h($r['customer']) : '<span class="text-muted">-</span>'; ?></td>
              <td data-l="Contact"><?php echo $h($r['poc']); ?></td>
              <td data-l="Phone" class="nw"><?php echo $h(ltrim($r['phone'], "'")); ?></td>
              <td data-l="Email"><?php echo $h($r['email']); ?></td>
              <td data-l="Assigned to"><?php echo $r['assignee'] !== '' ? $h($r['assignee']) : '<span class="text-muted">Not assigned</span>'; ?></td>
              <td data-l="Why" class="full"><span class="ip-why"><?php echo $h($r['reason']); ?></span></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
        <div class="sx-empty" id="ipNone" style="display:none;">No rows here.</div>
      </div>

      <div class="sx-card-f ip-foot">
        <div class="ip-sum" id="ipSum"></div>
        <div>
          <a href="<?php echo base_url(); ?>manager/leads/import" class="btn btn-default">Cancel</a>
          <button type="submit" class="btn btn-primary" id="ipGo"><i class="fa fa-upload"></i> Import</button>
        </div>
      </div>
    </form>
    <p class="sx-help"><i class="fa fa-info-circle"></i> A row is a duplicate when it has the same phone number (any number in any phone column; +971, 00971 or a leading 0 do not matter), the same email, or the same company name (ignoring capitals, spaces, punctuation and words such as LLC, FZE, Trading, Est.). Lead Finder and adding a record by hand use the same rules. The import is kept as one batch: you can undo it from the Import page while nobody has worked on the records.</p>
  </section>
</div>

<script>
$(function () {
  var ok = <?php echo (int)$c['ok']; ?>, dup = <?php echo (int)$c['dup']; ?>;
  function sum() {
    var n = ok + ($('input[name=dups]:checked').val() === 'import' ? dup : 0);
    $('#ipSum').html('<b>' + n.toLocaleString() + '</b> record' + (n === 1 ? '' : 's') + ' will be imported');
    $('#ipGo').prop('disabled', n === 0).html('<i class="fa fa-upload"></i> Import ' + n.toLocaleString() + ' record' + (n === 1 ? '' : 's'));
  }
  $('input[name=dups]').on('change', function () { $('.ip-opts label').removeClass('is-on'); $(this).closest('label').addClass('is-on'); sum(); });
  sum();
  $('#ipChips').on('click', 'button', function () {
    var f = $(this).data('f'), shown = 0;
    $('#ipChips button').removeClass('is-on'); $(this).addClass('is-on');
    $('#ipTable tbody tr').each(function () { var on = f === 'all' || this.getAttribute('data-s') === f; this.style.display = on ? '' : 'none'; if (on) shown++; });
    $('#ipNone').toggle(shown === 0);
  });
  $('#ipForm').on('submit', function () { $('#ipGo').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Importing...'); });
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
