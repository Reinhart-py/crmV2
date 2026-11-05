<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
  $is_new = empty($role['id']);
  $rid = (int)$role['id'];
  $csrf_name = $this->security->get_csrf_token_name();
  $csrf_hash = $this->security->get_csrf_hash();
  $readonly = !empty($readonly);
  $action = $is_new ? base_url('manager/roles/add') : base_url('manager/roles/edit/' . $rid);
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-perms.css?v=20261106_1">
<div class="content-wrapper">
  <section class="content-header crm-list-head">
    <h1><?php echo $is_new ? 'Add Role' : 'Role: ' . htmlspecialchars($role['title']); ?></h1>
    <a href="<?php echo base_url('manager/roles'); ?>" class="btn btn-sm btn-default"><i class="fa fa-arrow-left"></i> Back to Roles</a>
  </section>

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <?php if ($rid === 1) { ?>
      <div class="up-banner is-admin"><span><i class="fa fa-shield"></i> The Admin role always has full access to everything. It cannot be limited.</span></div>
    <?php } elseif ($readonly) { ?>
      <div class="up-banner"><span><i class="fa fa-eye"></i> You can view this role. Changing it needs Full on Roles &amp; Permissions.</span></div>
    <?php } ?>

    <form method="post" action="<?php echo $action; ?>" id="roleForm">
      <input type="hidden" name="<?php echo $csrf_name; ?>" value="<?php echo $csrf_hash; ?>">

      <div class="box crm-list-box">
        <div class="box-header with-border"><h3 class="box-title">Role details</h3></div>
        <div class="box-body">
          <div class="rp-form-grid">
            <div>
              <label for="rTitle">Role name *</label>
              <input type="text" id="rTitle" name="title" class="form-control" maxlength="60" required value="<?php echo htmlspecialchars($role['title']); ?>" <?php echo $readonly ? 'disabled' : ''; ?> placeholder="e.g. Sales Manager">
            </div>
            <div>
              <label for="rParent">Reports to</label>
              <select id="rParent" name="parent_id" class="form-control" <?php echo $readonly ? 'disabled' : ''; ?>>
                <option value="0">Nobody (top level)</option>
                <?php foreach ($roles as $r) { if ((int)$r['id'] === $rid) continue; ?>
                <option value="<?php echo (int)$r['id']; ?>" <?php echo (int)$role['parent_id'] === (int)$r['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($r['title']); ?></option>
                <?php } ?>
              </select>
            </div>
            <?php if (!$is_new) { ?>
            <div>
              <label for="rStatus">Status</label>
              <select id="rStatus" name="status" class="form-control" <?php echo ($readonly || $rid === 1) ? 'disabled' : ''; ?>>
                <option value="1" <?php echo (int)$role['status'] === 1 ? 'selected' : ''; ?>>Active</option>
                <option value="0" <?php echo (int)$role['status'] === 0 ? 'selected' : ''; ?>>Inactive</option>
              </select>
            </div>
            <?php } ?>
            <?php if (!$readonly) { ?>
            <div>
              <label for="rCopy">Start from another role</label>
              <select id="rCopy" class="form-control">
                <option value="">Keep current choices</option>
                <?php foreach ($roles as $r) { if ((int)$r['id'] === $rid) continue; ?>
                <option value="<?php echo (int)$r['id']; ?>">Copy from <?php echo htmlspecialchars($r['title']); ?></option>
                <?php } ?>
              </select>
            </div>
            <?php } ?>
          </div>
          <?php if (!$is_new) { ?>
          <p class="crm-sub" style="margin:12px 0 0;"><?php echo (int)$users_in_role; ?> <?php echo (int)$users_in_role === 1 ? 'person uses' : 'people use'; ?> this role. People set to <b>Custom</b> or <b>Full admin</b> in User permissions are not affected by changes here.</p>
          <?php } ?>
        </div>
      </div>

      <?php $pe_id = 'rolePerms'; $pe_values = $values; $pe_readonly = $readonly; $pe_name = 'permissions'; require(APPPATH . 'views/manager/roles/_perm_editor.php'); ?>

      <?php if (!$readonly) { ?>
      <div class="rp-savebar" id="roleSavebar">
        <span class="rp-savebar-text" id="roleSaveText">Choose None, View or Full for each area. <b>View</b> opens the page, <b>Full</b> also allows adding, editing and deleting.</span>
        <div class="rp-savebar-actions">
          <a href="<?php echo base_url('manager/roles'); ?>" class="btn btn-default">Cancel</a>
          <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> <?php echo $is_new ? 'Create role' : 'Save role'; ?></button>
        </div>
      </div>
      <?php } ?>
    </form>
  </section>
</div>
<script src="<?php echo base_url(); ?>assets/dist/js/crm-perms.js?v=20261106_1"></script>
<script>
(function () {
  var ROLES = <?php $rm = array(); foreach ($roles as $r) { $rm[(int)$r['id']] = $r['expanded']; } echo json_encode((object)$rm); ?>;
  var ed = CrmPerms.get('rolePerms');
  var dirty = false;
  function markDirty() {
    if (dirty) return;
    dirty = true;
    var bar = document.getElementById('roleSavebar');
    if (bar) { bar.classList.add('is-dirty'); document.getElementById('roleSaveText').innerHTML = '<b>Unsaved changes.</b> Save the role to apply them.'; }
  }
  ed.onChange(markDirty);
  var copy = document.getElementById('rCopy');
  if (copy) copy.addEventListener('change', function () {
    if (!this.value || !ROLES[this.value]) return;
    ed.set(ROLES[this.value]); markDirty();
  });
  ['rTitle', 'rParent', 'rStatus'].forEach(function (id) { var e = document.getElementById(id); if (e) e.addEventListener('change', markDirty); });
  var form = document.getElementById('roleForm');
  form.addEventListener('submit', function () { dirty = false; });
  window.addEventListener('beforeunload', function (e) { if (dirty) { e.preventDefault(); e.returnValue = ''; } });
})();
</script>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
