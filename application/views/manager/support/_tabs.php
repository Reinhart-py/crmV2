<?php
/* Chatbot tab bar. Expects $rights, $settings, $counts and $cb_tab ('inbox'|'history'|'settings'). */
$cb_base = base_url('manager/chatbot');
?>
<style>
  .cb-tabs { display: flex; gap: 4px; flex-wrap: wrap; border-bottom: 1px solid var(--crm-line, #e2e8f0); margin: 0 0 14px; }
  .cb-tabs a { display: inline-flex; align-items: center; gap: 7px; padding: 10px 16px; font-weight: 600; font-size: 14px; color: var(--crm-muted, #64748b); border-bottom: 2px solid transparent; margin-bottom: -1px; text-decoration: none; }
  .cb-tabs a:hover { color: var(--crm-ink, #0f172a); }
  .cb-tabs a.is-on { color: var(--crm-ocean, #1e3a8a); border-bottom-color: var(--crm-ocean, #1e3a8a); }
  .cb-tabs .cb-count { background: #fef3c7; color: #92400e; border-radius: 999px; font-size: 11px; padding: 1px 7px; }
  .cb-off { display: flex; gap: 10px; align-items: center; padding: 10px 14px; margin: 0 0 14px; border: 1px solid #fde68a; background: #fffbeb; color: #92400e; border-radius: 8px; font-size: 13.5px; }
  .cb-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin: 0 0 14px; }
  .cb-stat { background: var(--crm-surface, #fff); border: 1px solid var(--crm-line, #e2e8f0); border-radius: 8px; padding: 12px 14px; }
  .cb-stat b { display: block; font-size: 22px; color: var(--crm-ink, #0f172a); line-height: 1.2; }
  .cb-stat span { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: var(--crm-muted, #64748b); }
  @media (max-width: 767px) { .cb-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
</style>
<nav class="cb-tabs" aria-label="Chatbot">
  <a href="<?php echo $cb_base; ?>" class="<?php echo $cb_tab === 'open' ? 'is-on' : ''; ?>"><i class="fa fa-comments"></i> Open Chats<?php if (!empty($counts['waiting'])) { ?> <span class="cb-count"><?php echo (int)$counts['waiting']; ?></span><?php } ?></a>
  <a href="<?php echo $cb_base; ?>/closed" class="<?php echo $cb_tab === 'closed' ? 'is-on' : ''; ?>"><i class="fa fa-check-circle-o"></i> Closed Chats</a>
  <?php if (!empty($rights['history'])) { ?><a href="<?php echo $cb_base; ?>/history" class="<?php echo $cb_tab === 'history' ? 'is-on' : ''; ?>"><i class="fa fa-history"></i> History</a><?php } ?>
  <?php if (!empty($rights['settings'])) { ?><a href="<?php echo $cb_base; ?>/settings" class="<?php echo $cb_tab === 'settings' ? 'is-on' : ''; ?>"><i class="fa fa-sliders"></i> Settings</a><?php } ?>
</nav>
<?php if (empty($settings['enabled'])) { ?>
  <div class="cb-off"><i class="fa fa-pause-circle"></i> The chatbot is switched off. Visitors on the Support page are sent straight to the IT email form.<?php if (!empty($rights['settings'])) { ?> <a href="<?php echo $cb_base; ?>/settings">Turn it on</a><?php } ?></div>
<?php } ?>
