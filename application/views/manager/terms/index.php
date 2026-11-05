<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
  $csrf_name = $this->security->get_csrf_token_name();
  $csrf_hash = $this->security->get_csrf_hash();
  $L = $lists[$type];
  $h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
  $n_off = 0; foreach ($rows as $r) { if ((int)$r['status'] !== 1) $n_off++; }
  $edit_id = (int)$this->input->get('edit');
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-perms.css?v=20261106_1">
<style>
  .ls-tabs { overflow-x: auto; flex-wrap: nowrap; scrollbar-width: thin; }
  .ls-tabs a { white-space: nowrap; }
  .ls-intro { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; flex-wrap: wrap; margin-bottom: 12px; }
  .ls-intro h2 { margin: 0 0 2px; font-size: 18px; font-weight: 800; }
  .ls-intro p { margin: 0; color: var(--crm-muted); font-size: 13px; }
  .ls-bar { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; margin-bottom: 10px; }
  .ls-bar input[type=search] { max-width: 280px; }
  .ls-bar label { margin: 0; font-weight: 600; font-size: 13px; display: inline-flex; gap: 6px; align-items: center; }
  .ls-table { width: 100%; font-size: 14px; }
  .ls-table th { font-size: 12px; color: var(--crm-muted); font-weight: 700; padding: 8px 10px; border-bottom: 1px solid var(--crm-line); white-space: nowrap; }
  .ls-table td { padding: 8px 10px; border-bottom: 1px solid var(--crm-line-soft); vertical-align: middle; }
  .ls-table .nm { font-weight: 700; color: var(--crm-ink); }
  .ls-table tr.is-sub .nm { font-weight: 500; padding-left: 22px; position: relative; }
  .ls-table tr.is-sub .nm::before { content: ''; position: absolute; left: 6px; top: 2px; width: 9px; height: 9px; border-left: 2px solid var(--crm-line); border-bottom: 2px solid var(--crm-line); }
  .ls-table tr.is-off .nm { color: var(--crm-muted); text-decoration: line-through; }
  .ls-table .num { text-align: right; white-space: nowrap; font-variant-numeric: tabular-nums; }
  .ls-table .act { text-align: right; white-space: nowrap; }
  .ls-table .act form { display: inline; }
  .ls-hide-off tr.is-off { display: none; }
  @media (max-width: 600px) {
    .ls-table thead { display: none; }
    .ls-table tr { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 2px 8px; padding: 8px 0; border-bottom: 1px solid var(--crm-line-soft); }
    .ls-hide-off tr.is-off { display: none; }
    .ls-table td { border: 0; padding: 2px 0; }
    .ls-table td.num, .ls-table td.st { text-align: left; font-size: 12.5px; color: var(--crm-muted); }
    .ls-table td.act { grid-row: 1 / span 3; grid-column: 2; align-self: center; }
    .ls-bar input[type=search] { max-width: none; }
  }
</style>
<div class="content-wrapper">
  <section class="content-header crm-list-head">
    <h1>Lists</h1>
    <?php if ($can_write) { ?><button type="button" class="btn btn-sm btn-primary js-add"><i class="fa fa-plus"></i> Add to <?php echo $h($L['label']); ?></button><?php } ?>
  </section>

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="rp-tabs ls-tabs" role="tablist">
      <?php foreach ($lists as $k => $l) { $c = isset($counts[$k]) ? $counts[$k]['active'] : 0; ?>
        <a role="tab" href="<?php echo base_url('manager/terms?list=' . $k); ?>" class="<?php echo $k === $type ? 'is-on' : ''; ?>" aria-selected="<?php echo $k === $type ? 'true' : 'false'; ?>"><?php echo $h($l['label']); ?> <span class="rp-tab-count"><?php echo $c; ?></span></a>
      <?php } ?>
    </div>

    <div class="box crm-list-box">
      <div class="box-body">
        <div class="ls-intro">
          <div>
            <h2><?php echo $h($L['label']); ?></h2>
            <p><?php echo $h($L['desc']); ?>. Used in: <?php echo $h($L['used']); ?>.<?php if ($L['tree']) { ?> Entries can have sub-entries.<?php } ?></p>
          </div>
        </div>
        <div class="ls-bar">
          <input type="search" id="lsSearch" class="form-control input-sm" placeholder="Search <?php echo $h(strtolower($L['label'])); ?>" aria-label="Search this list">
          <?php if ($n_off) { ?><label><input type="checkbox" id="lsOff"> Show disabled (<?php echo $n_off; ?>)</label><?php } ?>
        </div>

        <?php if (!$rows) { ?>
          <div class="crm-empty-state"><i class="fa fa-list"></i> This list is empty.</div>
        <?php } else { ?>
        <div class="table-responsive" style="border:0;">
          <table class="ls-table ls-hide-off" id="lsTable">
            <thead><tr><th>Name</th><th class="num">Used on</th><th class="st">Status</th><th class="act"><?php echo $can_write ? 'Actions' : ''; ?></th></tr></thead>
            <tbody>
            <?php foreach ($rows as $r) {
              $id = (int)$r['id'];
              $on = (int)$r['status'] === 1;
              $used = isset($usage[$id]) ? (int)$usage[$id] : 0;
            ?>
              <tr class="<?php echo $r['depth'] ? 'is-sub' : ''; ?> <?php echo $on ? '' : 'is-off'; ?>" data-text="<?php echo $h(strtolower($r['title'] . ' ' . (isset($r['parent_title']) ? $r['parent_title'] : ''))); ?>" data-parent="<?php echo (int)$r['parent_id']; ?>" data-id="<?php echo $id; ?>">
                <td><span class="nm"><?php echo $h($r['title']); ?></span></td>
                <td class="num"><?php echo $used ? number_format($used) . ' record' . ($used === 1 ? '' : 's') : '<span class="crm-sub">Not used</span>'; ?></td>
                <td class="st"><?php echo $on ? '<span class="rp-chip rp-chip-green">Active</span>' : '<span class="rp-chip">Disabled</span>'; ?></td>
                <td class="act">
                  <?php if ($can_write) { ?>
                    <button type="button" class="btn btn-default btn-xs js-edit" data-id="<?php echo $id; ?>" data-title="<?php echo $h($r['title']); ?>" data-parent="<?php echo (int)$r['parent_id']; ?>" data-used="<?php echo $used; ?>"><i class="fa fa-pencil"></i> Edit</button>
                    <form method="post" action="<?php echo base_url('manager/terms/toggle/' . $id); ?>" onsubmit="return confirm(<?php echo $h(json_encode($on ? 'Disable "' . $r['title'] . '"? It will no longer be offered for new records; records that use it keep it.' : 'Enable "' . $r['title'] . '" again?')); ?>);">
                      <input type="hidden" name="<?php echo $csrf_name; ?>" value="<?php echo $csrf_hash; ?>">
                      <button type="submit" class="btn btn-default btn-xs"><?php echo $on ? '<i class="fa fa-ban"></i> Disable' : '<i class="fa fa-check"></i> Enable'; ?></button>
                    </form>
                  <?php } ?>
                </td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
        <div class="crm-sub" id="lsNone" style="display:none; padding:12px 0;">Nothing matches your search.</div>
        <?php } ?>
      </div>
    </div>
  </section>
</div>

<?php if ($can_write) { ?>
<div class="modal fade" id="lsModal" tabindex="-1" role="dialog" aria-labelledby="lsModalTitle">
  <div class="modal-dialog" role="document">
    <form class="modal-content" method="post" action="<?php echo base_url('manager/terms/save'); ?>">
      <input type="hidden" name="<?php echo $csrf_name; ?>" value="<?php echo $csrf_hash; ?>">
      <input type="hidden" name="cat_type" value="<?php echo $h($type); ?>">
      <input type="hidden" name="id" value="0">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="lsModalTitle">Add to <?php echo $h($L['label']); ?></h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="lsTitle">Name *</label>
          <input type="text" id="lsTitle" name="title" class="form-control" maxlength="120" required>
        </div>
        <?php if ($L['tree']) { ?>
        <div class="form-group">
          <label for="lsParent">Belongs under</label>
          <select id="lsParent" name="parent_id" class="form-control">
            <option value="0">Nothing (main entry)</option>
            <?php foreach ($parents as $p) { ?><option value="<?php echo (int)$p['id']; ?>"><?php echo $h($p['title']); ?><?php echo (int)$p['status'] === 1 ? '' : ' (disabled)'; ?></option><?php } ?>
          </select>
        </div>
        <?php } ?>
        <p class="crm-sub" id="lsNote" style="margin:0;"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
      </div>
    </form>
  </div>
</div>
<?php } ?>

<script>
(function () {
  var table = document.getElementById('lsTable');
  var search = document.getElementById('lsSearch');
  function filter() {
    if (!table) return;
    var q = search.value.trim().toLowerCase(), n = 0;
    var showOff = !table.classList.contains('ls-hide-off');
    table.querySelectorAll('tbody tr').forEach(function (tr) {
      var ok = !q || tr.getAttribute('data-text').indexOf(q) !== -1;
      tr.style.display = ok ? '' : 'none';
      if (ok && (showOff || !tr.classList.contains('is-off'))) n++;
    });
    document.getElementById('lsNone').style.display = n ? 'none' : '';
  }
  if (search) search.addEventListener('input', filter);
  var off = document.getElementById('lsOff');
  if (off) off.addEventListener('change', function () { table.classList.toggle('ls-hide-off', !this.checked); filter(); });

  var modal = window.jQuery ? jQuery('#lsModal') : null;
  if (!modal || !modal.length) return;
  var form = modal.find('form')[0];
  var BY_NAME = <?php echo $L['by'] === 'name' ? 'true' : 'false'; ?>;
  function open(id, title, parent, used) {
    var F = form.elements;
    F['id'].value = id || 0;
    F['title'].value = title || '';
    if (F['parent_id']) {
      F['parent_id'].value = String(parent || 0);
      // an entry cannot sit under itself
      Array.prototype.forEach.call(F['parent_id'].options, function (o) { o.disabled = id && o.value === String(id); });
    }
    document.getElementById('lsModalTitle').textContent = id ? 'Edit entry' : 'Add to <?php echo $h($L['label']); ?>';
    var note = '';
    if (id && used > 0) note = BY_NAME ? 'Renaming also renames it on the ' + used + ' record(s) that use it.' : 'The ' + used + ' record(s) that use it will show the new name.';
    document.getElementById('lsNote').textContent = note;
    modal.modal('show');
    setTimeout(function () { F['title'].focus(); }, 300);
  }
  document.querySelectorAll('.js-add').forEach(function (b) { b.addEventListener('click', function () { open(0, '', 0, 0); }); });
  document.querySelectorAll('.js-edit').forEach(function (b) {
    b.addEventListener('click', function () { open(+b.getAttribute('data-id'), b.getAttribute('data-title'), +b.getAttribute('data-parent'), +b.getAttribute('data-used')); });
  });
  var EDIT = <?php echo $edit_id; ?>;
  if (EDIT) { var b = document.querySelector('.js-edit[data-id="' + EDIT + '"]'); if (b) b.click(); }
  if (/[?&]add=1/.test(location.search)) open(0, '', 0, 0);
})();
</script>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
