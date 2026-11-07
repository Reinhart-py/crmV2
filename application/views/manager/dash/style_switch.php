<?php
// Modern / Classic dashboard switch, shown in the profile menu. Saved per user in ci_user_prefs.
$ds_now = 'modern';
$ds_ci =& get_instance();
$ds_uid = (int)$ds_ci->session->userdata('manager_id');
if ($ds_uid > 0 && $ds_ci->db->table_exists('ci_user_prefs')) {
  $ds_row = $ds_ci->db->select('dash_style')->where('user_id', $ds_uid)->get('ci_user_prefs')->row_array();
  if (!empty($ds_row) && $ds_row['dash_style'] === 'classic') $ds_now = 'classic';
}
?>
<form class="google-profile-item ds-switch" method="post" action="<?php echo base_url(); ?>manager/dashboard/style">
  <input type="hidden" name="<?php echo $ds_ci->security->get_csrf_token_name(); ?>" value="<?php echo $ds_ci->security->get_csrf_hash(); ?>">
  <i class="fa fa-th-large"></i>
  <span>Dashboard</span>
  <span class="ds-seg" role="group" aria-label="Dashboard style">
    <button type="submit" name="style" value="modern" class="<?php echo $ds_now === 'modern' ? 'is-on' : ''; ?>" aria-pressed="<?php echo $ds_now === 'modern' ? 'true' : 'false'; ?>">Modern</button>
    <button type="submit" name="style" value="classic" class="<?php echo $ds_now === 'classic' ? 'is-on' : ''; ?>" aria-pressed="<?php echo $ds_now === 'classic' ? 'true' : 'false'; ?>">Classic</button>
  </span>
</form>
<style>
.ds-switch { margin: 0; cursor: default; padding-right: 8px !important; }
.ds-switch > i { width: 18px; text-align: center; flex: none; }
.ds-switch > span:not(.ds-seg) { white-space: nowrap; }
.ds-switch .ds-seg { display: inline-flex; margin-left: auto; padding: 2px; flex: none; border: 1px solid var(--crm-line, #e2e8f0); border-radius: 999px; }
.ds-switch .ds-seg button { height: 22px; padding: 0 7px; border: 0; border-radius: 999px; background: transparent; color: var(--crm-ink-2, #334155); font-size: 11px; font-weight: 600; cursor: pointer; }
.ds-switch .ds-seg button:hover { color: var(--crm-ocean, #1e3a8a); }
.ds-switch .ds-seg button.is-on { background: var(--crm-ocean, #1e3a8a); color: #fff; cursor: default; }
[data-theme="dark"] .ds-switch .ds-seg { border-color: rgba(148, 163, 184, .3); }
[data-theme="dark"] .ds-switch .ds-seg button { color: #cbd5e1; }
[data-theme="dark"] .ds-switch .ds-seg button.is-on { background: #3b82f6; color: #fff; }
</style>
