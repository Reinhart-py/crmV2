<?php
// Dashboard choice (Modern / Classic / Classic 2 / Classic 3 / Classic 5 / Classic 6), shown in the profile menu. Saved per user in ci_user_prefs.
$ds_now = 'classic6'; // until the person picks one (see Dashboard_model::DEFAULT_STYLE)
$ds_list = array('modern' => 'Modern', 'classic' => 'Classic', 'classic2' => 'Classic 2', 'classic3' => 'Classic 3', 'classic5' => 'Classic 5', 'classic6' => 'Classic 6', 'modern6' => 'Modern 6');
$ds_ci =& get_instance();
$ds_uid = (int)$ds_ci->session->userdata('manager_id');
if ($ds_uid > 0 && $ds_ci->db->table_exists('ci_user_prefs')) {
  $ds_row = $ds_ci->db->select('dash_style')->where('user_id', $ds_uid)->get('ci_user_prefs')->row_array();
  if (!empty($ds_row) && isset($ds_list[$ds_row['dash_style']])) $ds_now = $ds_row['dash_style'];
}
?>
<form class="google-profile-item ds-switch" method="post" action="<?php echo base_url(); ?>manager/dashboard/style">
  <input type="hidden" name="<?php echo $ds_ci->security->get_csrf_token_name(); ?>" value="<?php echo $ds_ci->security->get_csrf_hash(); ?>">
  <span class="ds-top"><i class="fa fa-th-large"></i><span>Dashboard</span></span>
  <span class="ds-seg" role="group" aria-label="Dashboard style">
    <?php foreach ($ds_list as $ds_k => $ds_l) { ?>
    <button type="submit" name="style" value="<?php echo $ds_k; ?>" class="<?php echo $ds_now === $ds_k ? 'is-on' : ''; ?>" aria-pressed="<?php echo $ds_now === $ds_k ? 'true' : 'false'; ?>"><?php echo $ds_l; ?></button>
    <?php } ?>
  </span>
</form>
<style>
.ds-switch { margin: 0; cursor: default; padding-right: 8px !important; flex-wrap: wrap; row-gap: 6px; }
.ds-switch .ds-top { display: inline-flex; align-items: center; gap: inherit; flex: 1 0 100%; white-space: nowrap; }
.ds-switch .ds-top > i { width: 18px; text-align: center; flex: none; }
.ds-switch .ds-seg { display: flex; flex-wrap: wrap; gap: 2px; width: 100%; padding: 2px; flex: none; border: 1px solid var(--crm-line, #e2e8f0); border-radius: 14px; }
.ds-switch .ds-seg button { flex: 1 1 auto; height: 24px; padding: 0 3px; border: 0; border-radius: 999px; background: transparent; color: var(--crm-ink-2, #334155); font-size: 11px; white-space: nowrap; font-weight: 600; cursor: pointer; }
.ds-switch .ds-seg button:hover { color: var(--crm-ocean, #1e3a8a); }
.ds-switch .ds-seg button.is-on { background: var(--crm-ocean, #1e3a8a); color: #fff; cursor: default; }
[data-theme="dark"] .ds-switch .ds-seg { border-color: rgba(148, 163, 184, .3); }
[data-theme="dark"] .ds-switch .ds-seg button { color: #cbd5e1; }
[data-theme="dark"] .ds-switch .ds-seg button.is-on { background: #3b82f6; color: #fff; }
</style>
