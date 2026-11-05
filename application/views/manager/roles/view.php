<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
  $rid = (int)$role['id'];
  $rt = $role['role_type'];
  $csrf_name = $this->security->get_csrf_token_name();
  $csrf_hash = $this->security->get_csrf_hash();
  $can_move = $can_write && ($rid !== 1 || $is_full_admin);
  $active = 0; $off = 0;
  foreach ($people as $p) { if (!empty($p['left_on']) || (int)$p['admin_status'] !== 1) $off++; else $active++; }
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-perms.css?v=20261106_1">
<style>
  .rv-top { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 16px; }
  .rv-tile { background: var(--crm-surface, #fff); border: 1px solid var(--crm-line); border-radius: 12px; padding: 12px 14px; }
  .rv-tile small { display: block; font-size: 12px; color: var(--crm-muted); font-weight: 600; }
  .rv-tile b { font-size: 20px; color: var(--crm-ink); }
  .rv-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; }
  .rv-head::before, .rv-head::after { display: none; }
  .rv-table { width: 100%; font-size: 14px; }
  .rv-table th { font-size: 12px; color: var(--crm-muted); font-weight: 700; padding: 8px 10px; border-bottom: 1px solid var(--crm-line); white-space: nowrap; }
  .rv-table td { padding: 9px 10px; border-bottom: 1px solid var(--crm-line-soft); vertical-align: middle; }
  .rv-table .nm { font-weight: 700; color: var(--crm-ink); }
  .rv-table .sub { display: block; font-size: 12px; color: var(--crm-muted); }
  .rv-move { display: none; align-items: center; gap: 8px; flex-wrap: wrap; padding: 10px 12px; margin-bottom: 10px; border-radius: 10px; background: var(--crm-ocean-soft); }
  .rv-move.is-on { display: flex; }
  .rv-move select { width: auto; min-width: 180px; }
  .rv-groups { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 12px; }
  .rv-group { border: 1px solid var(--crm-line); border-radius: 12px; padding: 12px 14px; }
  .rv-group h4 { margin: 0 0 8px; font-size: 14px; font-weight: 800; display: flex; justify-content: space-between; gap: 8px; }
  .rv-group ul { list-style: none; margin: 0; padding: 0; }
  .rv-group li { display: flex; justify-content: space-between; gap: 8px; font-size: 13px; padding: 3px 0; }
  .rv-group li.none { color: var(--crm-muted); }
  .rv-v { font-size: 11px; font-weight: 700; padding: 1px 8px; border-radius: 999px; white-space: nowrap; }
  .rv-w { background: var(--crm-ocean); color: #fff; }
  .rv-r { background: #dbeafe; color: #1e40af; }
  .rv-n { color: var(--crm-muted); }
  @media (max-width: 600px) {
    .rv-table thead { display: none; }
    .rv-table tr { display: grid; grid-template-columns: 28px minmax(0, 1fr); gap: 2px 8px; padding: 8px 0; border-bottom: 1px solid var(--crm-line-soft); }
    .rv-table td { border: 0; padding: 2px 0; }
    .rv-table td.c-extra { grid-column: 2; font-size: 12.5px; color: var(--crm-muted); }
    .rv-table td.c-extra:empty { display: none; }
    .rv-move select, .rv-move .btn { width: 100%; }
  }
</style>
<div class="content-wrapper">
  <section class="content-header crm-list-head">
    <h1><?php echo htmlspecialchars($role['title']); ?> <span class="rp-chip <?php echo $rt === 'admin' ? 'rp-chip-ink' : ($rt === 'system' ? 'rp-chip-amber' : 'rp-chip-ocean'); ?>" style="vertical-align:middle;"><?php echo htmlspecialchars($role_types[$rt]['label']); ?></span></h1>
    <div style="display:flex; gap:6px; flex-wrap:wrap;">
      <a href="<?php echo base_url('manager/roles'); ?>" class="btn btn-sm btn-default"><i class="fa fa-arrow-left"></i> All roles</a>
      <?php if ($can_write && $rid !== 1) { ?><a href="<?php echo base_url('manager/roles/edit/' . $rid); ?>" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i> Edit access</a><?php } ?>
      <?php if ($can_write) { ?><a href="<?php echo base_url('manager/roles/add?copy=' . $rid); ?>" class="btn btn-sm btn-default"><i class="fa fa-clone"></i> Copy as new role</a><?php } ?>
    </div>
  </section>

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="rv-top">
      <div class="rv-tile"><small>Type</small><b style="font-size:15px;"><?php echo htmlspecialchars($role_types[$rt]['label']); ?></b><small style="font-weight:500;"><?php echo htmlspecialchars($role_types[$rt]['desc']); ?></small></div>
      <div class="rv-tile"><small>Working</small><b><?php echo $active; ?></b></div>
      <div class="rv-tile"><small>Disabled or left</small><b><?php echo $off; ?></b></div>
      <div class="rv-tile"><small>Access</small><b style="font-size:15px;"><?php echo $rid === 1 ? 'Everything' : ((int)$role['n_write'] . ' Full &middot; ' . (int)$role['n_read'] . ' View'); ?></b></div>
    </div>

    <div class="box crm-list-box" id="people">
      <div class="box-header with-border rv-head">
        <h3 class="box-title">People in this role</h3>
        <?php if ($can_move && $people) { ?><span class="crm-sub">Tick people to move them to another role.</span><?php } ?>
      </div>
      <div class="box-body">
        <?php if (!$people) { ?>
          <div class="crm-empty-state"><i class="fa fa-user-o"></i> Nobody has this role.<?php if ($can_write && $rid !== 1) { ?> It can be deleted from the Roles list.<?php } ?></div>
        <?php } else { ?>
          <?php if ($can_move) { ?>
          <form method="post" action="<?php echo base_url('manager/roles/move'); ?>" id="rvMoveForm">
            <input type="hidden" name="<?php echo $csrf_name; ?>" value="<?php echo $csrf_hash; ?>">
            <input type="hidden" name="from_role" value="<?php echo $rid; ?>">
            <div class="rv-move" id="rvMove">
              <b id="rvCount">0 selected</b>
              <span>Move to</span>
              <select name="to_role" class="form-control input-sm" required>
                <option value="">Choose role</option>
                <?php foreach ($roles as $r) { if ((int)$r['id'] === $rid || ((int)$r['id'] === 1 && !$is_full_admin)) continue; ?>
                <option value="<?php echo (int)$r['id']; ?>"><?php echo htmlspecialchars($r['title']); ?></option>
                <?php } ?>
              </select>
              <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-exchange"></i> Move</button>
              <button type="button" class="btn btn-default btn-sm" id="rvClear">Clear</button>
            </div>
          <?php } ?>
          <div class="table-responsive" style="border:0;">
            <table class="rv-table">
              <thead><tr>
                <th style="width:28px;"><?php if ($can_move) { ?><input type="checkbox" id="rvAll" aria-label="Tick everyone"><?php } ?></th>
                <th>Name</th><th>Employee ID</th><th>Manager</th><th>Status</th>
              </tr></thead>
              <tbody>
              <?php foreach ($people as $p) {
                $pid = (int)$p['id'];
                $locked = ($pid === $me || $pid === 1);
                $st = !empty($p['left_on']) ? '<span class="rp-chip">Left</span>' : ((int)$p['admin_status'] === 1 ? '<span class="rp-chip rp-chip-green">Active</span>' : '<span class="rp-chip rp-chip-amber">Disabled</span>');
              ?>
                <tr>
                  <td><?php if ($can_move) { ?><input type="checkbox" class="rv-pick" name="ids[]" value="<?php echo $pid; ?>" form="rvMoveForm" <?php echo $locked ? 'disabled title="' . ($pid === $me ? 'You cannot move yourself' : 'The main administrator stays Admin') . '"' : ''; ?> aria-label="Tick <?php echo htmlspecialchars($p['name']); ?>"><?php } ?></td>
                  <td>
                    <?php if ($profile_base) { ?><a class="nm" href="<?php echo base_url($profile_base . $pid); ?>"><?php echo htmlspecialchars($p['name']); ?></a><?php } else { ?><span class="nm"><?php echo htmlspecialchars($p['name']); ?></span><?php } ?>
                    <span class="sub"><?php echo htmlspecialchars((string)$p['email']); ?></span>
                  </td>
                  <td class="c-extra"><?php echo htmlspecialchars((string)$p['emp_id']); ?></td>
                  <td class="c-extra"><?php echo htmlspecialchars((string)$p['manager_name']); ?></td>
                  <td class="c-extra"><?php echo $st; ?></td>
                </tr>
              <?php } ?>
              </tbody>
            </table>
          </div>
          <?php if ($can_move) { ?></form><?php } ?>
        <?php } ?>
      </div>
    </div>

    <div class="box crm-list-box">
      <div class="box-header with-border rv-head">
        <h3 class="box-title">What this role can open</h3>
        <?php if ($can_write && $rid !== 1) { ?><a href="<?php echo base_url('manager/roles/edit/' . $rid); ?>" class="btn btn-default btn-sm"><i class="fa fa-pencil"></i> Edit access</a><?php } ?>
      </div>
      <div class="box-body">
        <?php if ($rid === 1) { ?>
          <p style="margin:0;">The Admin role has full access to everything and cannot be limited.</p>
        <?php } else { ?>
        <p class="crm-sub" style="margin:0 0 10px;">People set to <b>Custom</b> or <b>Full admin</b> in User permissions do not follow this list.</p>
        <div class="rv-groups">
          <?php foreach (crm_perm_catalog() as $gk => $g) {
            $w = 0; $r = 0;
            foreach ($g['items'] as $k => $it) { $v = isset($role['expanded'][$k]) ? $role['expanded'][$k] : 'no_access'; if ($v === 'write') $w++; elseif ($v === 'read') $r++; }
          ?>
          <div class="rv-group">
            <h4><span><?php echo htmlspecialchars($g['title']); ?></span><small class="crm-sub"><?php echo ($w + $r) . ' / ' . count($g['items']); ?></small></h4>
            <ul>
              <?php foreach ($g['items'] as $k => $it) { $v = isset($role['expanded'][$k]) ? $role['expanded'][$k] : 'no_access'; $only_r = isset($it['levels']) && $it['levels'] === 'r'; ?>
              <li class="<?php echo $v === 'no_access' ? 'none' : ''; ?>" title="<?php echo htmlspecialchars($it['desc']); ?>"><span><?php echo htmlspecialchars($it['label']); ?></span>
                <?php echo $v === 'write' ? '<span class="rv-v rv-w">Full</span>' : ($v === 'read' ? '<span class="rv-v rv-r">' . ($only_r ? 'Yes' : 'View') . '</span>' : '<span class="rv-v rv-n">&ndash;</span>'); ?></li>
              <?php } ?>
            </ul>
          </div>
          <?php } ?>
        </div>
        <?php } ?>
      </div>
    </div>
  </section>
</div>
<script>
(function () {
  var bar = document.getElementById('rvMove');
  if (!bar) return;
  var picks = function () { return Array.prototype.slice.call(document.querySelectorAll('.rv-pick:not([disabled])')); };
  function sync() {
    var n = picks().filter(function (c) { return c.checked; }).length;
    bar.classList.toggle('is-on', n > 0);
    document.getElementById('rvCount').textContent = n + ' selected';
    var all = document.getElementById('rvAll');
    if (all) { all.checked = n > 0 && n === picks().length; all.indeterminate = n > 0 && n < picks().length; }
  }
  document.querySelectorAll('.rv-pick').forEach(function (c) { c.addEventListener('change', sync); });
  var all = document.getElementById('rvAll');
  if (all) all.addEventListener('change', function () { var on = this.checked; picks().forEach(function (c) { c.checked = on; }); sync(); });
  document.getElementById('rvClear').addEventListener('click', function () { picks().forEach(function (c) { c.checked = false; }); sync(); });
  document.getElementById('rvMoveForm').addEventListener('submit', function (e) {
    var sel = this.querySelector('[name=to_role]');
    var n = picks().filter(function (c) { return c.checked; }).length;
    if (!confirm('Move ' + n + (n === 1 ? ' person' : ' people') + ' to ' + sel.options[sel.selectedIndex].text + '? Their access changes on their next page.')) e.preventDefault();
  });
})();
</script>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
