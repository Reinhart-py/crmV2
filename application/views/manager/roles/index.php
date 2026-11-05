<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
  $csrf_name = $this->security->get_csrf_token_name();
  $csrf_hash = $this->security->get_csrf_hash();
  $role_map = array();
  foreach ($roles as $r) { $role_map[(int)$r['id']] = array('title' => $r['title'], 'perms' => $r['expanded']); }
  $mode_label = array('role' => 'Role', 'custom' => 'Custom', 'admin' => 'Full admin');
  $initials = function ($n) {
    $p = preg_split('/\s+/', trim((string)$n));
    $s = strtoupper(substr($p[0], 0, 1) . (isset($p[1]) ? substr($p[1], 0, 1) : ''));
    return $s !== '' ? $s : '?';
  };
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-perms.css?v=20261106_1">
<div class="content-wrapper">
  <section class="content-header crm-list-head">
    <h1>Roles &amp; Permissions</h1>
    <?php if ($can_write) { ?>
      <a href="<?php echo base_url('manager/roles/add'); ?>" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add Role</a>
    <?php } ?>
  </section>

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="rp-tabs" role="tablist">
      <button type="button" role="tab" id="tabRoles" aria-controls="paneRoles" class="is-on" data-tab="roles"><i class="fa fa-sitemap"></i> Roles <span class="rp-tab-count"><?php echo count($roles); ?></span></button>
      <button type="button" role="tab" id="tabUsers" aria-controls="paneUsers" data-tab="users"><i class="fa fa-user"></i> User permissions <span class="rp-tab-count"><?php echo count($users); ?></span></button>
    </div>

    <!-- ===== Roles ===== -->
    <div id="paneRoles" role="tabpanel" aria-labelledby="tabRoles">
      <div class="rp-help">
        <div class="rp-help-item"><span class="rp-help-ico"><i class="fa fa-sitemap"></i></span><div><b>1. Roles set the default</b><span>Everyone gets the access of their role. Change a role once and it applies to all its people.</span></div></div>
        <div class="rp-help-item"><span class="rp-help-ico"><i class="fa fa-user"></i></span><div><b>2. Adjust one person</b><span>In User permissions, set someone to Custom to give or take away single areas, or to Full admin.</span></div></div>
        <div class="rp-help-item"><span class="rp-help-ico"><i class="fa fa-eye"></i></span><div><b>3. Only what they may use</b><span>Menus show only allowed pages, and a page opened without the right is refused. View opens it, Full also adds, edits and deletes.</span></div></div>
      </div>

      <div class="box crm-list-box">
        <div class="box-body">
          <div class="table-responsive">
            <table class="table table-hover crm-list-table">
              <thead><tr><th>Role</th><th>Reports to</th><th>People</th><th>Access</th><th class="text-right">Actions</th></tr></thead>
              <tbody>
              <?php foreach ($roles as $r) { $rid = (int)$r['id']; $pct = function ($n) use ($r) { return $r['n_total'] ? round(100 * $n / $r['n_total'], 1) : 0; }; ?>
                <tr>
                  <td><span class="rp-role-name"><?php echo htmlspecialchars($r['title']); ?></span><?php if ($rid === 1) { ?> <span class="rp-chip rp-chip-ink">Administrator</span><?php } ?></td>
                  <td><?php echo $r['parent_title'] !== '' ? htmlspecialchars($r['parent_title']) : '<span class="crm-sub">Top level</span>'; ?></td>
                  <td><a href="#" class="js-role-people" data-role="<?php echo $rid; ?>"><?php echo (int)$r['users']; ?> <?php echo (int)$r['users'] === 1 ? 'person' : 'people'; ?></a></td>
                  <td class="rp-access">
                    <?php if ($rid === 1) { ?>
                      <div class="rp-bar"><i class="w" style="width:100%"></i></div><small>Full access to everything</small>
                    <?php } elseif ($r['n_write'] + $r['n_read'] === 0) { ?>
                      <div class="rp-bar"></div><small>No access yet</small>
                    <?php } else { ?>
                      <div class="rp-bar"><i class="w" style="width:<?php echo $pct($r['n_write']); ?>%"></i><i class="r" style="width:<?php echo $pct($r['n_read']); ?>%"></i></div>
                      <small><?php echo (int)$r['n_write']; ?> Full &middot; <?php echo (int)$r['n_read']; ?> View &middot; <?php echo (int)($r['n_total'] - $r['n_write'] - $r['n_read']); ?> None</small>
                    <?php } ?>
                  </td>
                  <td>
                    <div class="rp-actions">
                      <a href="<?php echo base_url('manager/roles/edit/' . $rid); ?>" class="btn btn-default btn-xs"><i class="fa <?php echo ($can_write && $rid !== 1) ? 'fa-pencil' : 'fa-eye'; ?>"></i> <?php echo ($can_write && $rid !== 1) ? 'Edit permissions' : 'View'; ?></a>
                      <?php if ($can_write) { ?>
                        <a href="<?php echo base_url('manager/roles/add?copy=' . $rid); ?>" class="btn btn-default btn-xs" title="New role starting from <?php echo htmlspecialchars($r['title']); ?>"><i class="fa fa-clone"></i> Copy</a>
                        <?php if ($rid !== 1) { ?>
                        <form method="post" action="<?php echo base_url('manager/roles/delete/' . $rid); ?>" style="display:inline" onsubmit="return <?php echo (int)$r['users'] > 0 ? "alert('Move the " . (int)$r['users'] . " people in this role to another role first.'), false" : "confirm('Delete the role " . htmlspecialchars(addslashes($r['title']), ENT_QUOTES) . "?')"; ?>;">
                          <input type="hidden" name="<?php echo $csrf_name; ?>" value="<?php echo $csrf_hash; ?>">
                          <button type="submit" class="btn btn-default btn-xs" title="Delete role"><i class="fa fa-trash-o"></i></button>
                        </form>
                        <?php } ?>
                      <?php } ?>
                    </div>
                  </td>
                </tr>
              <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== User permissions ===== -->
    <div id="paneUsers" role="tabpanel" aria-labelledby="tabUsers" hidden>
      <div class="up-wrap">
        <div class="box crm-list-box up-people">
          <div class="box-body">
            <div class="up-filter">
              <input type="search" id="upSearch" class="form-control" placeholder="Search name or email" aria-label="Search people">
              <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                <select id="upRole" class="form-control" aria-label="Filter by role">
                  <option value="">All roles</option>
                  <?php foreach ($roles as $r) { ?><option value="<?php echo (int)$r['id']; ?>"><?php echo htmlspecialchars($r['title']); ?></option><?php } ?>
                </select>
                <select id="upMode" class="form-control" aria-label="Filter by access type">
                  <option value="">Any access</option>
                  <option value="role">Follows role</option>
                  <option value="custom">Custom</option>
                  <option value="admin">Full admin</option>
                  <option value="chatbot">Has Chatbot</option>
                </select>
              </div>
            </div>
            <div class="up-count" id="upCount"></div>
            <div class="up-list" id="upList">
              <?php foreach ($users as $u) {
                $mode = in_array($u['permission_mode'], array('custom', 'admin'), true) ? $u['permission_mode'] : 'role';
                $chat = !empty($u['chatbot_permissions']) && strpos($u['chatbot_permissions'], '"access"') !== false;
              ?>
              <button type="button" class="up-person" data-id="<?php echo (int)$u['id']; ?>" data-role="<?php echo (int)$u['user_type']; ?>" data-mode="<?php echo $mode; ?>" data-chat="<?php echo $chat ? 1 : 0; ?>" data-text="<?php echo htmlspecialchars(strtolower($u['name'] . ' ' . $u['email'])); ?>">
                <span class="up-avatar"><?php echo htmlspecialchars($initials($u['name'])); ?></span>
                <span class="up-person-text">
                  <span class="up-person-name"><?php echo htmlspecialchars($u['name']); ?></span>
                  <span class="up-person-sub"><?php echo htmlspecialchars($u['role_title'] ? $u['role_title'] : 'No role'); ?><?php echo $u['email'] ? ' &middot; ' . htmlspecialchars($u['email']) : ''; ?></span>
                </span>
                <span class="up-person-tags">
                  <?php if ($mode === 'custom') { ?><span class="rp-chip rp-chip-amber">Custom</span><?php } elseif ($mode === 'admin') { ?><span class="rp-chip rp-chip-ink">Full admin</span><?php } ?>
                  <?php if ($chat) { ?><span class="rp-chip rp-chip-ocean" title="Has Chatbot access"><i class="fa fa-comments"></i></span><?php } ?>
                </span>
              </button>
              <?php } ?>
            </div>
          </div>
        </div>

        <div>
          <div class="box crm-list-box" id="upEmpty">
            <div class="box-body"><div class="crm-empty-state"><i class="fa fa-hand-o-left"></i> Choose a person on the left to see and change what they can open.</div></div>
          </div>

          <div id="upDetail" hidden>
            <div class="box crm-list-box">
              <div class="up-head">
                <span class="up-avatar" id="upAvatar"></span>
                <div style="flex:1; min-width:180px;">
                  <h2 id="upName"></h2>
                  <p id="upMeta"></p>
                </div>
              </div>
              <div class="up-modes" role="radiogroup" aria-label="Access type">
                <label class="up-mode"><input type="radio" name="upModeChoice" value="role"><span class="up-mode-card"><b>Follow role</b><span id="upModeRoleText">Same access as their role. Changes to the role apply automatically.</span></span></label>
                <label class="up-mode"><input type="radio" name="upModeChoice" value="custom"><span class="up-mode-card"><b>Custom</b><span>Start from the role and change single areas for this person only.</span></span></label>
                <label class="up-mode" id="upModeAdminWrap"><input type="radio" name="upModeChoice" value="admin"><span class="up-mode-card"><b>Full admin</b><span>Everything, like the Admin role. Use only for administrators.</span></span></label>
              </div>
            </div>

            <div class="up-banner" id="upBanner"></div>
            <?php $pe_id = 'userPerms'; $pe_values = array(); $pe_readonly = true; $pe_name = 'up'; require(APPPATH . 'views/manager/roles/_perm_editor.php'); ?>

            <div class="box crm-list-box cb-card" id="upChat">
              <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-comments"></i> Chatbot (Live Support) <span class="rp-chip rp-chip-ocean">User-wise only</span></h3>
                <div class="pe-seg pe-seg-sm"><button type="button" data-chat-all="0">None</button><button type="button" data-chat-all="1">All</button></div>
              </div>
              <div class="box-body">
                <p class="crm-sub" style="margin:0 0 6px;">Answers visitors from the Support page on the sign-in screen. Given to chosen people only: not part of roles or Full admin. Every right needs <b>Open Chatbot</b>.</p>
                <div class="pe-rows">
                  <?php foreach (chatbot_rights() as $ck => $cl) { ?>
                  <div class="pe-row" data-chat-key="<?php echo $ck; ?>">
                    <div class="pe-row-text"><span class="pe-label" id="cbl-<?php echo $ck; ?>"><?php echo htmlspecialchars($cl[0]); ?></span><span class="pe-desc"><?php echo htmlspecialchars($cl[1]); ?></span></div>
                    <div class="pe-seg" role="radiogroup" aria-labelledby="cbl-<?php echo $ck; ?>">
                      <label><input type="radio" name="cb[<?php echo $ck; ?>]" value="0" checked><span>No</span></label>
                      <label><input type="radio" name="cb[<?php echo $ck; ?>]" value="1"><span>Yes</span></label>
                    </div>
                  </div>
                  <?php } ?>
                </div>
              </div>
            </div>

            <?php if ($can_write) { ?>
            <div class="rp-savebar" id="upSavebar">
              <span class="rp-savebar-text" id="upSaveText">No changes.</span>
              <div class="rp-savebar-actions">
                <button type="button" class="btn btn-default" id="upDiscard">Discard</button>
                <button type="button" class="btn btn-primary" id="upSave"><i class="fa fa-save"></i> Save changes</button>
              </div>
            </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script src="<?php echo base_url(); ?>assets/dist/js/crm-perms.js?v=20261106_1"></script>
<script>
(function () {
  var BASE = '<?php echo base_url(); ?>manager/roles/';
  var CSRF_NAME = '<?php echo $csrf_name; ?>';
  var CSRF_FALLBACK = '<?php echo $csrf_hash; ?>';
  var CAN_WRITE = <?php echo $can_write ? 'true' : 'false'; ?>;
  var IS_FULL_ADMIN = <?php echo $is_full_admin ? 'true' : 'false'; ?>;
  var ME = <?php echo (int)$this->session->userdata('manager_id'); ?>;
  var ROLES = <?php echo json_encode((object)$role_map); ?>;
  function $(id) { return document.getElementById(id); }
  function esc(s) { var d = document.createElement('div'); d.textContent = s == null ? '' : String(s); return d.innerHTML; }
  function csrf() { var m = document.cookie.match(/(?:^|;\s*)csrf_cookie_name=([^;]+)/); return m ? decodeURIComponent(m[1]) : CSRF_FALLBACK; }
  function initials(n) { var p = String(n || '').trim().split(/\s+/); return ((p[0] || '').charAt(0) + (p[1] || '').charAt(0)).toUpperCase() || '?'; }

  /* ---- tabs ---- */
  function showTab(name) {
    document.querySelectorAll('[data-tab]').forEach(function (b) {
      var on = b.getAttribute('data-tab') === name;
      b.classList.toggle('is-on', on); b.setAttribute('aria-selected', on ? 'true' : 'false');
    });
    $('paneRoles').hidden = name !== 'roles';
    $('paneUsers').hidden = name !== 'users';
    if (history.replaceState) history.replaceState(null, '', name === 'users' ? '#user-wise' : location.pathname + location.search);
  }
  document.querySelectorAll('[data-tab]').forEach(function (b) { b.addEventListener('click', function () { showTab(b.getAttribute('data-tab')); }); });
  if (/user/.test(location.hash)) showTab('users');

  /* ---- people list ---- */
  function filterPeople() {
    var q = $('upSearch').value.trim().toLowerCase(), role = $('upRole').value, mode = $('upMode').value, n = 0;
    document.querySelectorAll('.up-person').forEach(function (p) {
      var ok = (!q || p.getAttribute('data-text').indexOf(q) !== -1) &&
        (!role || p.getAttribute('data-role') === role) &&
        (!mode || (mode === 'chatbot' ? p.getAttribute('data-chat') === '1' : p.getAttribute('data-mode') === mode));
      p.style.display = ok ? '' : 'none';
      if (ok) n++;
    });
    $('upCount').textContent = n + (n === 1 ? ' person' : ' people');
  }
  ['upSearch', 'upRole', 'upMode'].forEach(function (id) { $(id).addEventListener('input', filterPeople); $(id).addEventListener('change', filterPeople); });
  filterPeople();
  document.querySelectorAll('.js-role-people').forEach(function (a) {
    a.addEventListener('click', function (e) { e.preventDefault(); $('upRole').value = a.getAttribute('data-role'); $('upMode').value = ''; $('upSearch').value = ''; filterPeople(); showTab('users'); });
  });

  /* ---- one person ---- */
  var ed = CrmPerms.get('userPerms');
  var cur = null; // loaded data
  var state = null; // { mode, custom, chat }
  var dirty = false;

  function chatGet() {
    var out = {};
    document.querySelectorAll('[data-chat-key]').forEach(function (r) {
      var c = r.querySelector('input:checked');
      if (c && c.value === '1') out[r.getAttribute('data-chat-key')] = 1;
    });
    if (!out.access) out = {};
    return out;
  }
  function chatSet(map) {
    var has = !!map.access;
    document.querySelectorAll('[data-chat-key]').forEach(function (r) {
      var k = r.getAttribute('data-chat-key');
      var on = !!map[k] && (k === 'access' || has);
      r.querySelector('input[value="' + (on ? 1 : 0) + '"]').checked = true;
      var lock = k !== 'access' && !has;
      r.style.opacity = lock ? '.5' : '';
      r.querySelectorAll('input').forEach(function (i) { i.disabled = lock || !CAN_WRITE; });
    });
  }
  document.querySelectorAll('[data-chat-key] input').forEach(function (i) {
    i.addEventListener('change', function () {
      var m = chatGet();
      if (this.name === 'cb[access]' && this.value === '0') m = {};
      if (this.name === 'cb[access]' && this.value === '1') m.access = 1;
      chatSet(m); markDirty();
    });
  });
  document.querySelectorAll('[data-chat-all]').forEach(function (b) {
    b.addEventListener('click', function () {
      if (!CAN_WRITE) return;
      var m = {};
      if (b.getAttribute('data-chat-all') === '1') document.querySelectorAll('[data-chat-key]').forEach(function (r) { m[r.getAttribute('data-chat-key')] = 1; });
      chatSet(m); markDirty();
    });
  });

  function setMode(mode, fromUser) {
    state.mode = mode;
    document.querySelectorAll('input[name="upModeChoice"]').forEach(function (i) { i.checked = i.value === mode; });
    var banner = $('upBanner'), role = cur.user.role_title;
    $('userPerms').style.display = mode === 'admin' ? 'none' : '';
    banner.className = 'up-banner' + (mode === 'admin' ? ' is-admin' : '');
    if (mode === 'role') {
      ed.set(cur.role_permissions); ed.setBaseline(null); ed.setReadonly(true);
      banner.innerHTML = '<span><i class="fa fa-link"></i> Showing the <b>' + esc(role) + '</b> role. To change only this person, choose <b>Custom</b>.</span>' +
        (CAN_WRITE && !locked() ? '<button type="button" class="btn btn-xs btn-primary" id="upToCustom">Customise for this person</button>' : '');
      var b = $('upToCustom'); if (b) b.addEventListener('click', function () { setMode('custom', true); markDirty(); });
    } else if (mode === 'custom') {
      if (fromUser && !state.customTouched) state.custom = JSON.parse(JSON.stringify(cur.mode === 'custom' ? cur.custom_permissions : cur.role_permissions));
      ed.set(state.custom); ed.setBaseline(cur.role_permissions); ed.setReadonly(!CAN_WRITE || locked());
      banner.innerHTML = '<span><i class="fa fa-sliders"></i> Custom access for this person. Areas marked <b>Changed</b> differ from the <b>' + esc(role) + '</b> role.</span>' +
        (CAN_WRITE && !locked() ? '<button type="button" class="btn btn-xs btn-default" id="upResetRole">Reset to role</button>' : '');
      var r = $('upResetRole'); if (r) r.addEventListener('click', function () { state.custom = JSON.parse(JSON.stringify(cur.role_permissions)); ed.set(state.custom); markDirty(); });
    } else {
      banner.innerHTML = '<span><i class="fa fa-shield"></i> <b>Full admin</b>: this person can open and change everything, including Roles &amp; Permissions.</span>';
    }
  }
  function locked() {
    // Only full admins can change administrators; nobody but a full admin can change their own access.
    if (!cur) return true;
    if (IS_FULL_ADMIN) return false;
    return cur.user.is_role_admin || cur.mode === 'admin' || cur.user.id === ME;
  }
  document.querySelectorAll('input[name="upModeChoice"]').forEach(function (i) {
    i.addEventListener('change', function () { if (this.checked) { setMode(this.value, true); markDirty(); } });
  });
  ed.onChange(function (m) { if (state && state.mode === 'custom') { state.custom = m; state.customTouched = true; markDirty(); } });

  function markDirty() {
    dirty = true;
    var bar = $('upSavebar'); if (!bar) return;
    bar.classList.add('is-dirty');
    $('upSaveText').innerHTML = '<b>Unsaved changes</b> for ' + esc(cur.user.name) + '.';
  }
  function clean(msg) {
    dirty = false;
    var bar = $('upSavebar'); if (!bar) return;
    bar.classList.remove('is-dirty');
    $('upSaveText').innerHTML = msg || 'No changes.';
  }

  function openPerson(id) {
    if (dirty && !confirm('Discard the unsaved changes for ' + cur.user.name + '?')) return;
    document.querySelectorAll('.up-person').forEach(function (p) { p.classList.toggle('is-on', +p.getAttribute('data-id') === id); });
    $('upEmpty').hidden = true; $('upDetail').hidden = false;
    $('upName').textContent = 'Loading...'; $('upMeta').textContent = '';
    fetch(BASE + 'ajax_get_user_permissions/' + id, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (r) { return r.json(); })
      .then(function (r) {
        if (r.status !== 'success') { alert(r.message || 'Could not load this person.'); return; }
        cur = r; state = { mode: r.mode, custom: JSON.parse(JSON.stringify(r.custom_permissions)), customTouched: false };
        $('upAvatar').textContent = initials(r.user.name);
        $('upName').textContent = r.user.name;
        $('upMeta').innerHTML = '<b>' + esc(r.user.role_title) + '</b> role' + (r.user.email ? ' &middot; ' + esc(r.user.email) : '') + (r.user.department ? ' &middot; ' + esc(r.user.department) : '');
        $('upModeRoleText').textContent = 'Same access as the ' + r.user.role_title + ' role. Changes to the role apply automatically.';
        var lk = locked() || !CAN_WRITE;
        document.querySelectorAll('input[name="upModeChoice"]').forEach(function (i) {
          i.disabled = lk || (i.value === 'admin' && !IS_FULL_ADMIN);
          i.closest('.up-mode').classList.toggle('is-disabled', i.disabled);
        });
        if (r.user.is_role_admin) {
          // Admin role users always have everything.
          document.querySelectorAll('input[name="upModeChoice"]').forEach(function (i) { i.disabled = true; i.closest('.up-mode').classList.add('is-disabled'); });
        }
        setMode(r.user.is_role_admin ? 'admin' : r.mode, false);
        if (r.user.is_role_admin) $('upBanner').innerHTML = '<span><i class="fa fa-shield"></i> In the <b>Admin</b> role: always full access. Change their role in Users to limit it.</span>';
        chatSet(r.chatbot_permissions || {});
        $('upChat').querySelectorAll('[data-chat-all]').forEach(function (b) { b.disabled = !CAN_WRITE; });
        clean(lk && CAN_WRITE ? 'Only a full administrator can change this person.' : 'No changes.');
        var save = $('upSave'); if (save) save.disabled = lk && !IS_FULL_ADMIN && cur.user.is_role_admin;
      })
      .catch(function () { alert('Could not load this person. Check your connection.'); });
  }
  document.querySelectorAll('.up-person').forEach(function (p) { p.addEventListener('click', function () { openPerson(+p.getAttribute('data-id')); }); });

  var discard = $('upDiscard');
  if (discard) discard.addEventListener('click', function () { if (cur) { dirty = false; openPerson(cur.user.id); } });

  var saveBtn = $('upSave');
  if (saveBtn) saveBtn.addEventListener('click', function () {
    if (!cur) return;
    var b = new URLSearchParams();
    b.append('user_id', cur.user.id);
    b.append('permission_mode', state.mode);
    if (state.mode === 'custom') { var m = ed.get(); Object.keys(m).forEach(function (k) { b.append('permissions[' + k + ']', m[k]); }); }
    var c = chatGet();
    if (c.access) Object.keys(c).forEach(function (k) { b.append('chatbot[' + k + ']', 1); }); else b.append('chatbot[access]', 0);
    b.append(CSRF_NAME, csrf());
    saveBtn.disabled = true;
    fetch(BASE + 'ajax_save_user_permissions', { method: 'POST', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: b })
      .then(function (r) { return r.json(); })
      .then(function (r) {
        saveBtn.disabled = false;
        if (r.status !== 'success') { alert(r.message || 'Not saved.'); return; }
        cur.mode = r.mode;
        if (r.mode === 'custom') cur.custom_permissions = ed.get();
        cur.chatbot_permissions = c;
        var row = document.querySelector('.up-person[data-id="' + cur.user.id + '"]');
        if (row) {
          row.setAttribute('data-mode', r.mode); row.setAttribute('data-chat', r.chatbot ? 1 : 0);
          var tags = row.querySelector('.up-person-tags');
          tags.innerHTML = (r.mode === 'custom' ? '<span class="rp-chip rp-chip-amber">Custom</span>' : r.mode === 'admin' ? '<span class="rp-chip rp-chip-ink">Full admin</span>' : '') +
            (r.chatbot ? '<span class="rp-chip rp-chip-ocean" title="Has Chatbot access"><i class="fa fa-comments"></i></span>' : '');
        }
        clean('<i class="fa fa-check" style="color:#16a34a"></i> ' + esc(r.message));
      })
      .catch(function () { saveBtn.disabled = false; alert('Not saved. Check your connection and try again.'); });
  });

  window.addEventListener('beforeunload', function (e) { if (dirty) { e.preventDefault(); e.returnValue = ''; } });
})();
</script>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
