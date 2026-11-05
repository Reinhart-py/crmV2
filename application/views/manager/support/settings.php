<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
  $cb_tab = 'settings';
  $csrf_name = $this->security->get_csrf_token_name();
  $csrf_hash = $this->security->get_csrf_hash();
  $since = date('Y-m-d H:i:s', time() - 90);
?>
<style>
  .cb-switch { position: relative; display: inline-flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 600; margin: 0; }
  .cb-switch input { position: absolute; opacity: 0; pointer-events: none; }
  .cb-track { width: 42px; height: 24px; border-radius: 999px; background: #cbd5e1; position: relative; transition: background .15s; flex: none; }
  .cb-track::after { content: ""; position: absolute; top: 3px; left: 3px; width: 18px; height: 18px; border-radius: 50%; background: #fff; transition: transform .15s; box-shadow: 0 1px 2px rgba(0,0,0,.2); }
  .cb-switch input:checked + .cb-track { background: #16a34a; }
  .cb-switch input:checked + .cb-track::after { transform: translateX(18px); }
  .cb-switch input:focus-visible + .cb-track { outline: 3px solid rgba(30,58,138,.3); outline-offset: 2px; }
  .cb-switch > span:last-child { color: var(--crm-ink, #0f172a); }
  .cb-help { color: var(--crm-muted, #64748b); font-size: 12.5px; margin: 5px 0 0; }
  .cb-agent { display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px solid var(--crm-line, #f1f5f9); font-size: 14px; }
  .cb-agent:last-child { border-bottom: 0; }
  .cb-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 6px; background: #cbd5e1; }
  .cb-dot.is-on { background: #16a34a; }
</style>
<div class="content-wrapper">
  <section class="content-header crm-list-head">
    <h1>Chatbot</h1>
  </section>

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>
    <?php require(APPPATH . "views/manager/support/_tabs.php"); ?>

    <div class="row">
      <div class="col-lg-8 col-md-7">
        <div class="box crm-list-box">
          <div class="box-header with-border"><h3 class="box-title">Chatbot settings</h3></div>
          <div class="box-body">
            <form method="post" action="<?php echo base_url('manager/chatbot/settings'); ?>">
              <input type="hidden" name="<?php echo $csrf_name; ?>" value="<?php echo $csrf_hash; ?>">
              <div class="form-group">
                <label class="cb-switch" for="cbEnabled">
                  <input type="checkbox" id="cbEnabled" name="enabled" value="1" <?php echo $settings['enabled'] ? 'checked' : ''; ?>>
                  <span class="cb-track" aria-hidden="true"></span>
                  <span>Live chat on the Support page</span>
                </label>
                <p class="cb-help">When off, visitors always get the email form and their request goes straight to IT.</p>
              </div>
              <div class="row">
                <div class="col-sm-8 form-group">
                  <label for="cbIt">IT email address</label>
                  <input type="email" id="cbIt" name="it_email" class="form-control" maxlength="150" placeholder="<?php echo htmlspecialchars($it_email_effective); ?>" value="<?php echo htmlspecialchars($settings['it_email']); ?>">
                  <p class="cb-help">Requests are sent here when nobody is Active. Now sending to <strong><?php echo htmlspecialchars($it_email_effective); ?></strong>.</p>
                </div>
                <div class="col-sm-4 form-group">
                  <label for="cbWait">Wait before emailing IT</label>
                  <div class="input-group">
                    <input type="number" id="cbWait" name="no_reply_minutes" class="form-control" min="1" max="30" value="<?php echo (int)$settings['no_reply_minutes']; ?>">
                    <span class="input-group-addon">minutes</span>
                  </div>
                  <p class="cb-help">If nobody replies in time.</p>
                </div>
              </div>
              <div class="form-group">
                <label for="cbWelcome">Welcome message</label>
                <textarea id="cbWelcome" name="welcome_message" class="form-control" rows="2" maxlength="300"><?php echo htmlspecialchars($settings['welcome_message']); ?></textarea>
                <p class="cb-help">Shown when a chat starts and someone is Active.</p>
              </div>
              <div class="form-group">
                <label for="cbOffline">Offline message</label>
                <textarea id="cbOffline" name="offline_message" class="form-control" rows="2" maxlength="300"><?php echo htmlspecialchars($settings['offline_message']); ?></textarea>
                <p class="cb-help">Shown when nobody is Active and the request is emailed to IT. You can use {name}, {phone} and {ref} in both messages.</p>
              </div>
              <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save settings</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-5">
        <div class="box crm-list-box">
          <div class="box-header with-border"><h3 class="box-title">People with Chatbot access</h3></div>
          <div class="box-body">
            <?php if (!empty($agents)) { foreach ($agents as $a) { $on = (int)$a['available'] === 1 && $a['last_seen'] >= $since; ?>
              <div class="cb-agent"><span><?php echo htmlspecialchars($a['name']); ?></span><span class="crm-sub"><span class="cb-dot<?php echo $on ? ' is-on' : ''; ?>"></span><?php echo $on ? 'Active' : 'Inactive'; ?></span></div>
            <?php } } else { ?>
              <div class="crm-empty-state">Nobody yet.</div>
            <?php } ?>
            <p class="cb-help" style="margin-top:12px;">Access is given per user in System &rsaquo; Roles &amp; Permissions &rsaquo; User-wise &rsaquo; Chatbot.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
