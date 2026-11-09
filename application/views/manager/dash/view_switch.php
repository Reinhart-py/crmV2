<?php
// CRM view choice (Classic / Modern), shown in the profile menu under the Dashboard choice. Saved per user in ci_user_prefs.
$vs_ci =& get_instance();
$vs_now = function_exists('crm_view') ? crm_view() : 'classic';
$vs_back = trim(preg_replace('~^/?(index\.php/)?~', '', $vs_ci->uri->uri_string()), '/');
?>
<form class="google-profile-item ds-switch" method="post" action="<?php echo base_url(); ?>manager/crm-view">
  <input type="hidden" name="<?php echo $vs_ci->security->get_csrf_token_name(); ?>" value="<?php echo $vs_ci->security->get_csrf_hash(); ?>">
  <input type="hidden" name="return" value="<?php echo htmlspecialchars($vs_back, ENT_QUOTES, 'UTF-8'); ?>">
  <span class="ds-top"><i class="fa fa-columns"></i><span>CRM view</span></span>
  <span class="ds-seg" role="group" aria-label="CRM view">
    <button type="submit" name="view" value="classic" class="<?php echo $vs_now === 'classic' ? 'is-on' : ''; ?>" aria-pressed="<?php echo $vs_now === 'classic' ? 'true' : 'false'; ?>" title="Menu across the top">Classic</button>
    <button type="submit" name="view" value="modern" class="<?php echo $vs_now === 'modern' ? 'is-on' : ''; ?>" aria-pressed="<?php echo $vs_now === 'modern' ? 'true' : 'false'; ?>" title="Menu in a side pane, new colours">Modern</button>
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
