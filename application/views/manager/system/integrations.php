<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
$x = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$can_edit = !empty($can_edit);
$S = $settings;
$cv = function ($srv, $k, $d = '') use ($S) { return isset($S[$srv]['config'][$k]) && $S[$srv]['config'][$k] !== '' ? $S[$srv]['config'][$k] : $d; };
$on = function ($srv) use ($S) { return (int)$S[$srv]['status'] === 1; };
// Secrets are only shown to people who may change them.
$secret = function ($val) use ($can_edit) { return $can_edit ? (string)$val : 'Hidden (Full access needed)'; };
$tabs = array(
  'groq' => array('AI engine (Groq)', 'fa-microchip'),
  'pbx_3cx' => array('3CX telephony', 'fa-phone-square'),
  'email_smtp' => array('Email (SMTP)', 'fa-envelope'),
  'whatsapp' => array('WhatsApp Business', 'fa-whatsapp'),
  'schedule_cron' => array('Schedules & reminders', 'fa-clock-o'),
  'helpdesk' => array('Help centre', 'fa-life-ring'),
);
$n_on = 0; foreach ($tabs as $k => $t) if ($on($k)) $n_on++;
$cron_key = $S['schedule_cron']['api_key'] !== '' ? $S['schedule_cron']['api_key'] : bin2hex(random_bytes(12));
$pbx_hook = $cv('pbx_3cx', 'webhook_secret', bin2hex(random_bytes(12)));
$wa_verify = $S['whatsapp']['api_secret'] !== '' ? $S['whatsapp']['api_secret'] : bin2hex(random_bytes(12));
$stat = function ($srv, $yes = 'Connected', $no = 'Not connected') use ($on) { return '<span class="chip ' . ($on($srv) ? 'ok' : 'na') . '">' . ($on($srv) ? $yes : $no) . '</span>'; };
$status_sel = function ($srv, $yes = 'Enabled', $no = 'Disabled') use ($on) {
  return '<select name="status" class="form-control"><option value="1"' . ($on($srv) ? ' selected' : '') . '>' . $yes . '</option><option value="0"' . (!$on($srv) ? ' selected' : '') . '>' . $no . '</option></select>';
};
$foot = function ($srv, $test_label) use ($can_edit) {
  if (!$can_edit) return '';
  return '<div class="sx-card-f"><button type="button" class="btn btn-default btn-test-connection" data-service="' . $srv . '"><i class="fa fa-refresh"></i> ' . $test_label . '</button><button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save settings</button></div>';
};
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<style>
  .in-layout { display: grid; grid-template-columns: 240px minmax(0, 1fr); gap: 16px; align-items: start; }
  .in-layout > * { min-width: 0; }
  .in-nav { display: flex; flex-direction: column; gap: 4px; padding: 8px; }
  .in-nav a { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 9px; color: var(--crm-ink, #0f172a); font-weight: 600; font-size: 13.5px; text-decoration: none; }
  .in-nav a:hover { background: var(--crm-bg-soft, #f1f5f9); }
  .in-nav a.is-on { background: var(--crm-ocean-soft, rgba(30, 58, 138, .08)); color: var(--crm-ocean, #1e3a8a); }
  .in-nav a i { width: 18px; text-align: center; }
  .in-nav a .dot { margin-left: auto; width: 8px; height: 8px; border-radius: 50%; background: #cbd5e1; flex: none; }
  .in-nav a .dot.ok { background: #16a34a; }
  .in-pane { display: none; }
  .in-pane.is-on { display: block; }
  .in-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px 16px; }
  .in-grid .wide { grid-column: 1 / -1; }
  .in-grid .form-group { margin: 0; }
  .in-grid input[readonly] { background: var(--crm-bg-soft, #f8fafc); }
  .in-copy { display: flex; gap: 6px; }
  .in-copy .form-control { flex: 1; min-width: 0; }
  .sx .sx-card-f { justify-content: space-between; }
  .in-sub { padding: 14px 16px; border-top: 1px solid var(--crm-line-soft, #eef2f7); }
  .in-sub .in-copy .btn { white-space: nowrap; }
  @media (max-width: 991px) {
    .in-layout { grid-template-columns: 1fr; }
    .in-nav { flex-direction: row; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .in-nav a { white-space: nowrap; flex: none; }
  }
  @media (max-width: 767px) { .in-grid { grid-template-columns: 1fr; } .in-sub { padding: 12px; } .in-sub .in-copy { flex-wrap: wrap; } .in-sub .in-copy .btn { flex: 1; } }
</style>
<div class="content-wrapper sx">
  <?php echo crm_tabbar('integrations', 'system/integrations'); ?>
  <section class="sx-head">
    <h1>Third-Party API<small>Keys and settings for the outside services the CRM talks to</small></h1>
  </section>

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="sx-kpis">
      <div class="sx-kpi g"><span class="ic"><i class="fa fa-plug"></i></span><div><b><?php echo $n_on; ?> of <?php echo count($tabs); ?></b><span>Services switched on</span></div></div>
      <div class="sx-kpi <?php echo $on('email_smtp') ? 'g' : 'a'; ?>"><span class="ic"><i class="fa fa-envelope"></i></span><div><b><?php echo $on('email_smtp') ? 'On' : 'Off'; ?></b><span>Email sending</span></div></div>
      <?php if (!$can_edit) { ?><div class="sx-kpi s"><span class="ic"><i class="fa fa-lock"></i></span><div><b>View only</b><span>Ask for Full access to change</span></div></div><?php } ?>
    </div>

    <div class="in-layout">
      <div class="sx-card">
        <nav class="in-nav" id="integrationNavTabs" role="tablist">
          <?php $first = true; foreach ($tabs as $k => $t) { ?>
            <a href="#tab_<?php echo $k; ?>" role="tab" class="<?php echo $first ? 'is-on' : ''; ?>"><i class="fa <?php echo $t[1]; ?>"></i> <?php echo $x($t[0]); ?><span class="dot <?php echo $on($k) ? 'ok' : ''; ?>" title="<?php echo $on($k) ? 'On' : 'Off'; ?>"></span></a>
          <?php $first = false; } ?>
        </nav>
      </div>

      <div>
        <!-- Groq -->
        <div class="in-pane is-on" id="tab_groq">
          <div class="sx-card">
            <div class="sx-card-h"><h3><i class="fa fa-microchip"></i> Groq AI (call transcripts and summaries)</h3><div class="sx-tools"><?php echo $stat('groq'); ?></div></div>
            <form action="<?php echo base_url(); ?>manager/system/integrations/save" method="post" autocomplete="off">
              <input type="hidden" name="service_name" value="groq" />
              <fieldset <?php echo $can_edit ? '' : 'disabled'; ?>>
              <div class="sx-card-b"><div class="in-grid">
                <div class="form-group wide"><label>API address</label><input type="text" name="endpoint" class="form-control" value="<?php echo $x($S['groq']['endpoint'] ?: 'https://api.groq.com/openai/v1'); ?>" required /></div>
                <div class="form-group wide"><label>API key</label><input type="password" name="api_key" class="form-control" autocomplete="new-password" placeholder="<?php echo !empty($S['groq']['api_key']) ? 'Saved - leave blank to keep it' : 'gsk_... (or set GROQ_API_KEY on the server)'; ?>" /></div>
                <div class="form-group"><label>Transcription model</label><input type="text" name="config[transcription_model]" class="form-control" value="<?php echo $x($cv('groq', 'transcription_model', 'whisper-large-v3-turbo')); ?>" required /></div>
                <div class="form-group"><label>Summary model</label><input type="text" name="config[summary_model]" class="form-control" value="<?php echo $x($cv('groq', 'summary_model', 'openai/gpt-oss-20b')); ?>" required /></div>
                <div class="form-group"><label>Fallback model</label><input type="text" name="config[fallback_summary_model]" class="form-control" value="<?php echo $x($cv('groq', 'fallback_summary_model', 'openai/gpt-oss-120b')); ?>" /></div>
                <div class="form-group"><label>Temperature (0 - 1)</label><input type="number" step="0.1" min="0" max="1" name="config[temperature]" class="form-control" value="<?php echo $x($cv('groq', 'temperature', '0')); ?>" /></div>
                <div class="form-group"><label>Status</label><?php echo $status_sel('groq'); ?></div>
              </div></div>
              </fieldset>
              <?php echo $foot('groq', 'Test connection'); ?>
            </form>
          </div>
        </div>

        <!-- 3CX -->
        <div class="in-pane" id="tab_pbx_3cx">
          <div class="sx-card">
            <div class="sx-card-h"><h3><i class="fa fa-phone-square"></i> 3CX call recordings and call logs</h3><div class="sx-tools"><?php echo $stat('pbx_3cx'); ?></div></div>
            <?php if (crm_can('calling_api')) { ?><div class="sx-card-b" style="padding-bottom:0;"><p class="sx-help"><i class="fa fa-info-circle"></i> Click-to-call and the auto-dialer are set up on <a href="<?php echo base_url(); ?>manager/system/calling-api">Calling API</a>.</p></div><?php } ?>
            <form action="<?php echo base_url(); ?>manager/system/integrations/save" method="post" autocomplete="off">
              <input type="hidden" name="service_name" value="pbx_3cx" />
              <fieldset <?php echo $can_edit ? '' : 'disabled'; ?>>
              <div class="sx-card-b"><div class="in-grid">
                <div class="form-group wide"><label>3CX server address</label><input type="text" name="endpoint" class="form-control" placeholder="https://pbx.example.com:5001" value="<?php echo $x($S['pbx_3cx']['endpoint']); ?>" required /></div>
                <div class="form-group"><label>Client ID / extension user</label><input type="text" name="api_key" class="form-control" value="<?php echo $x($S['pbx_3cx']['api_key']); ?>" /></div>
                <div class="form-group"><label>Client secret / password</label><input type="password" name="api_secret" class="form-control" autocomplete="new-password" placeholder="<?php echo !empty($S['pbx_3cx']['api_secret']) ? 'Saved - leave blank to keep it' : 'Enter the 3CX secret'; ?>" /></div>
                <div class="form-group wide"><label>API bearer token (to download recordings)</label><input type="password" name="config[bearer_token]" class="form-control" autocomplete="new-password" placeholder="<?php echo $cv('pbx_3cx', 'bearer_token') !== '' ? 'Saved - leave blank to keep it' : 'Enter the 3CX API bearer token'; ?>" /></div>
                <div class="form-group wide"><label>Webhook shared secret</label><input type="text" name="config[webhook_secret]" class="form-control mono" value="<?php echo $x($secret($pbx_hook)); ?>" /></div>
                <div class="form-group wide"><label>Recording webhook address (give this to 3CX)</label><div class="in-copy"><input type="text" class="form-control mono" readonly value="<?php echo base_url(); ?>api/calls/recording_webhook" /><button type="button" class="btn btn-default in-copy-btn" title="Copy"><i class="fa fa-copy"></i></button></div></div>
                <div class="form-group wide"><label>Call log (CDR) webhook address</label><div class="in-copy"><input type="text" class="form-control mono" readonly value="<?php echo base_url(); ?>api/calls/cdr_webhook" /><button type="button" class="btn btn-default in-copy-btn" title="Copy"><i class="fa fa-copy"></i></button></div></div>
                <div class="form-group"><label>Status</label><?php echo $status_sel('pbx_3cx'); ?></div>
              </div></div>
              </fieldset>
              <?php echo $foot('pbx_3cx', 'Test 3CX connection'); ?>
            </form>
          </div>
        </div>

        <!-- Email -->
        <div class="in-pane" id="tab_email_smtp">
          <div class="sx-card">
            <div class="sx-card-h"><h3><i class="fa fa-envelope"></i> Email sending (SMTP)</h3><div class="sx-tools"><?php echo $stat('email_smtp', 'Active', 'Inactive'); ?></div></div>
            <form action="<?php echo base_url(); ?>manager/system/integrations/save" method="post" autocomplete="off">
              <input type="hidden" name="service_name" value="email_smtp" />
              <fieldset <?php echo $can_edit ? '' : 'disabled'; ?>>
              <div class="sx-card-b"><div class="in-grid">
                <div class="form-group"><label>SMTP server</label><input type="text" name="config[smtp_host]" class="form-control" placeholder="mail.example.com" value="<?php echo $x($cv('email_smtp', 'smtp_host')); ?>" required /></div>
                <div class="form-group"><label>Port</label><input type="number" name="config[smtp_port]" class="form-control" value="<?php echo $x($cv('email_smtp', 'smtp_port', '587')); ?>" required /></div>
                <div class="form-group"><label>Username</label><input type="text" name="config[smtp_user]" class="form-control" value="<?php echo $x($cv('email_smtp', 'smtp_user')); ?>" /></div>
                <div class="form-group"><label>Password</label><input type="password" name="config[smtp_pass]" class="form-control" autocomplete="new-password" placeholder="<?php echo $cv('email_smtp', 'smtp_pass') !== '' ? 'Saved - leave blank to keep it' : 'Enter the SMTP password'; ?>" /></div>
                <div class="form-group"><label>From email</label><input type="email" name="config[from_email]" class="form-control" value="<?php echo $x($cv('email_smtp', 'from_email', 'noreply@7strokes.com')); ?>" /></div>
                <div class="form-group"><label>From name</label><input type="text" name="config[from_name]" class="form-control" value="<?php echo $x($cv('email_smtp', 'from_name', 'Seven Strokes CRM')); ?>" /></div>
                <div class="form-group"><label>Encryption</label>
                  <select name="config[smtp_crypto]" class="form-control">
                    <?php foreach (array('tls' => 'TLS (port 587)', 'ssl' => 'SSL (port 465)', 'none' => 'None (port 25)') as $k => $l) { ?><option value="<?php echo $k; ?>" <?php echo $cv('email_smtp', 'smtp_crypto') === $k ? 'selected' : ''; ?>><?php echo $l; ?></option><?php } ?>
                  </select>
                </div>
                <div class="form-group"><label>Status</label><?php echo $status_sel('email_smtp'); ?></div>
              </div></div>
              </fieldset>
              <?php if ($can_edit) { ?>
              <div class="in-sub">
                <label for="smtpTestTo">Send a test email</label>
                <div class="in-copy">
                  <input type="email" id="smtpTestTo" class="form-control" placeholder="you@example.com" value="<?php echo $x((string)$this->session->userdata('manager_email') ?: (string)$this->session->userdata('manager_username')); ?>" />
                  <button type="button" class="btn btn-default" id="btnSmtpSendTest"><i class="fa fa-paper-plane"></i> Send test</button>
                </div>
                <p class="sx-help" style="margin:6px 0 0;">Uses the saved settings, so save first. Password reset emails are sent the same way.</p>
              </div>
              <?php } ?>
              <?php echo $foot('email_smtp', 'Test server reachable'); ?>
            </form>
          </div>
        </div>

        <!-- WhatsApp -->
        <div class="in-pane" id="tab_whatsapp">
          <div class="sx-card">
            <div class="sx-card-h"><h3><i class="fa fa-whatsapp"></i> WhatsApp Business Cloud API</h3><div class="sx-tools"><?php echo $stat('whatsapp', 'Active', 'Inactive'); ?></div></div>
            <form action="<?php echo base_url(); ?>manager/system/integrations/save" method="post" autocomplete="off">
              <input type="hidden" name="service_name" value="whatsapp" />
              <fieldset <?php echo $can_edit ? '' : 'disabled'; ?>>
              <div class="sx-card-b"><div class="in-grid">
                <div class="form-group wide"><label>Cloud API address</label><input type="text" name="endpoint" class="form-control" value="<?php echo $x($S['whatsapp']['endpoint'] ?: 'https://graph.facebook.com/v19.0'); ?>" required /></div>
                <div class="form-group wide"><label>Permanent access token</label><input type="password" name="api_key" class="form-control" autocomplete="new-password" placeholder="<?php echo !empty($S['whatsapp']['api_key']) ? 'Saved - leave blank to keep it' : 'EAA...'; ?>" /></div>
                <div class="form-group"><label>Phone number ID</label><input type="text" name="config[phone_number_id]" class="form-control" value="<?php echo $x($cv('whatsapp', 'phone_number_id')); ?>" /></div>
                <div class="form-group"><label>Business account ID</label><input type="text" name="config[waba_id]" class="form-control" value="<?php echo $x($cv('whatsapp', 'waba_id')); ?>" /></div>
                <div class="form-group wide"><label>Webhook verify token</label><input type="text" name="api_secret" class="form-control mono" value="<?php echo $x($secret($wa_verify)); ?>" /></div>
                <div class="form-group wide"><label>Webhook callback address (give this to Meta)</label><div class="in-copy"><input type="text" class="form-control mono" readonly value="<?php echo base_url(); ?>api/whatsapp/webhook" /><button type="button" class="btn btn-default in-copy-btn" title="Copy"><i class="fa fa-copy"></i></button></div></div>
                <div class="form-group"><label>Status</label><?php echo $status_sel('whatsapp'); ?></div>
              </div></div>
              </fieldset>
              <?php echo $foot('whatsapp', 'Test token'); ?>
            </form>
          </div>
        </div>

        <!-- Schedules -->
        <div class="in-pane" id="tab_schedule_cron">
          <div class="sx-card">
            <div class="sx-card-h"><h3><i class="fa fa-clock-o"></i> Automatic schedule emails and reminders</h3><div class="sx-tools"><?php echo $stat('schedule_cron', 'Running', 'Paused'); ?></div></div>
            <form action="<?php echo base_url(); ?>manager/system/integrations/save" method="post" autocomplete="off">
              <input type="hidden" name="service_name" value="schedule_cron" />
              <fieldset <?php echo $can_edit ? '' : 'disabled'; ?>>
              <div class="sx-card-b"><div class="in-grid">
                <div class="form-group wide"><label>Cron secret key</label><input type="text" name="api_key" class="form-control mono" value="<?php echo $x($secret($cron_key)); ?>" required /></div>
                <?php if ($can_edit) { ?>
                <div class="form-group wide"><label>Daily reminder address (run once a day from the server's cron)</label><div class="in-copy"><input type="text" class="form-control mono" readonly value="<?php echo base_url(); ?>cron/daily_schedule_reminders?key=<?php echo $x($cron_key); ?>" /><button type="button" class="btn btn-default in-copy-btn" title="Copy"><i class="fa fa-copy"></i></button></div></div>
                <?php } ?>
                <div class="form-group"><label>Remind this many days before</label><input type="number" min="1" max="30" name="config[notice_days]" class="form-control" value="<?php echo $x($cv('schedule_cron', 'notice_days', '3')); ?>" required /></div>
                <div class="form-group"><label>Admin notification email</label><input type="email" name="config[notify_email]" class="form-control" value="<?php echo $x($cv('schedule_cron', 'notify_email', 'compliance@7strokes.com')); ?>" /></div>
                <div class="form-group"><label>Daily sending</label><?php echo $status_sel('schedule_cron', 'Running', 'Paused'); ?></div>
              </div></div>
              </fieldset>
              <?php echo $foot('schedule_cron', 'Check cron address'); ?>
            </form>
          </div>
        </div>

        <!-- Help centre -->
        <div class="in-pane" id="tab_helpdesk">
          <div class="sx-card">
            <div class="sx-card-h"><h3><i class="fa fa-life-ring"></i> Help centre and support routing</h3><div class="sx-tools"><?php echo $stat('helpdesk', 'Active', 'Inactive'); ?></div></div>
            <form action="<?php echo base_url(); ?>manager/system/integrations/save" method="post" autocomplete="off">
              <input type="hidden" name="service_name" value="helpdesk" />
              <fieldset <?php echo $can_edit ? '' : 'disabled'; ?>>
              <div class="sx-card-b"><div class="in-grid">
                <div class="form-group"><label>Support email</label><input type="email" name="config[support_email]" class="form-control" value="<?php echo $x($cv('helpdesk', 'support_email', 'support@7strokes.com')); ?>" required /></div>
                <div class="form-group"><label>Escalation manager email</label><input type="email" name="config[escalation_email]" class="form-control" value="<?php echo $x($cv('helpdesk', 'escalation_email', 'escalations@7strokes.com')); ?>" /></div>
                <div class="form-group wide"><label>External ticket portal (optional)</label><input type="url" name="endpoint" class="form-control" placeholder="https://support.example.com" value="<?php echo $x($S['helpdesk']['endpoint']); ?>" /></div>
                <div class="form-group"><label>Status</label><?php echo $status_sel('helpdesk'); ?></div>
              </div></div>
              </fieldset>
              <?php echo $foot('helpdesk', 'Check routing'); ?>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
$(function () {
  // tabs (remembers the open one in the address, so it stays open after Save)
  function show(id) {
    if (!id || !$(id).hasClass('in-pane')) return;
    $('.in-pane').removeClass('is-on'); $(id).addClass('is-on');
    var a = $('#integrationNavTabs a').removeClass('is-on').filter('[href="' + id + '"]').addClass('is-on')[0];
    var nav = document.getElementById('integrationNavTabs'); if (a && nav.scrollWidth > nav.clientWidth) nav.scrollLeft = a.offsetLeft - 8;
  }
  $('#integrationNavTabs a').on('click', function (e) { e.preventDefault(); var id = $(this).attr('href'); show(id); try { history.replaceState(null, '', id); } catch (er) {} });
  show(location.hash);
  $('form[action$="integrations/save"]').on('submit', function () { var id = '#' + $(this).closest('.in-pane').attr('id'); this.action = this.action.split('#')[0] + id; });

  $('.in-copy-btn').on('click', function () {
    var inp = $(this).siblings('input')[0], btn = $(this);
    var done = function () { btn.html('<i class="fa fa-check"></i>'); setTimeout(function () { btn.html('<i class="fa fa-copy"></i>'); }, 1500); };
    if (navigator.clipboard) navigator.clipboard.writeText(inp.value).then(done, function () { inp.select(); });
    else { inp.select(); try { document.execCommand('copy'); done(); } catch (e) {} }
  });

  $('#btnSmtpSendTest').on('click', function () {
    var $btn = $(this), t = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');
    $.ajax({
      url: '<?php echo base_url(); ?>manager/system/integrations/ajax_send_test_email', type: 'POST', dataType: 'json',
      data: { to_email: $('#smtpTestTo').val() },
      success: function (res) { alert((res && res.status ? 'Sent: ' : 'Failed: ') + (res && res.message ? res.message : 'Unknown response')); },
      error: function () { alert('The server could not send the test email.'); },
      complete: function () { $btn.prop('disabled', false).html(t); }
    });
  });

  $('.btn-test-connection').on('click', function () {
    var $btn = $(this), t = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Testing...');
    $.ajax({
      url: '<?php echo base_url(); ?>manager/system/integrations/ajax_test_connection', type: 'POST', dataType: 'json',
      data: { service_name: $btn.data('service') },
      success: function (res) {
        if (res && res.status) { alert('Success: ' + res.message); location.reload(); }
        else alert('Failed: ' + (res && res.message ? res.message : 'Unknown response'));
      },
      error: function () { alert('The server could not run the test.'); },
      complete: function () { $btn.prop('disabled', false).html(t); }
    });
  });
});
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
