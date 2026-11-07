<?php
// Modern / Classic dashboard switch. Saved per user ($dash_style: 'modern' or 'classic').
$ds_now = isset($dash_style) && $dash_style === 'classic' ? 'classic' : 'modern';
?>
<form class="ds-switch" method="post" action="<?php echo base_url(); ?>manager/dashboard/style" aria-label="Dashboard style">
  <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
  <button type="submit" name="style" value="modern" class="<?php echo $ds_now === 'modern' ? 'is-on' : ''; ?>"<?php echo $ds_now === 'modern' ? ' aria-pressed="true"' : ' aria-pressed="false"'; ?>>Modern</button>
  <button type="submit" name="style" value="classic" class="<?php echo $ds_now === 'classic' ? 'is-on' : ''; ?>"<?php echo $ds_now === 'classic' ? ' aria-pressed="true"' : ' aria-pressed="false"'; ?>>Classic</button>
</form>
<style>
.ds-switch { display: inline-flex; margin: 0; padding: 2px; border: 1px solid var(--crm-line, #e2e8f0); border-radius: 999px; background: var(--crm-surface, #fff); vertical-align: middle; }
.ds-switch button { height: 28px; padding: 0 14px; border: 0; border-radius: 999px; background: transparent; color: var(--crm-ink-2, #334155); font: 600 13px "Plus Jakarta Sans", "Inter", sans-serif; cursor: pointer; }
.ds-switch button:hover { color: var(--crm-ocean, #1e3a8a); }
.ds-switch button.is-on { background: var(--crm-ocean, #1e3a8a); color: #fff; cursor: default; }
.ds-bar { display: flex; justify-content: flex-end; align-items: center; gap: 8px; padding: 8px 15px 0; }
.ds-bar span { font-size: 12.5px; color: var(--crm-muted, #64748b); }
[data-theme="dark"] .ds-switch { background: transparent; }
</style>
