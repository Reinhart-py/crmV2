<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
$h = function ($x) { return htmlspecialchars((string)$x, ENT_QUOTES, 'UTF-8'); };
$sel = function ($x, $y) { return (string)$x === (string)$y ? ' selected' : ''; };
$base = base_url() . 'manager/';
$zones = array('Asia/Dubai', 'Asia/Riyadh', 'Asia/Qatar', 'Asia/Kuwait', 'Asia/Bahrain', 'Asia/Muscat', 'Asia/Kolkata', 'Asia/Karachi', 'Europe/London', 'UTC');
if (!in_array($s['timezone'], $zones, true)) $zones[] = $s['timezone'];
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-campaigns.css?v=20261107_2">
<div class="content-wrapper sx cp">
  <section class="sx-head">
    <h1>AI calling settings<small>Voice provider, caller number and limits</small></h1>
    <div class="sx-btns"><a class="btn btn-default" href="<?php echo $base; ?>ai-agents"><i class="fa fa-arrow-left"></i> AI agents</a></div>
  </section>

  <section class="content">
    <form id="stForm" class="cp-form" autocomplete="off" novalidate>
      <div>
        <div class="sx-card">
          <div class="sx-card-h"><h3><i class="fa fa-plug"></i> Voice provider</h3></div>
          <div class="sx-card-b">
            <div class="f2">
              <div class="form-group">
                <label for="fProv">Provider</label>
                <select id="fProv" name="provider" class="form-control">
                  <?php foreach ($providers as $k => $lbl) { ?><option value="<?php echo $k; ?>"<?php echo $sel($s['provider'], $k); ?>><?php echo $h($lbl); ?></option><?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label class="tog" style="margin-top:26px"><input type="checkbox" name="enabled" value="1"<?php echo $s['enabled'] ? ' checked' : ''; ?>><span>AI calling is on<small>Off: nothing is dialled; queued calls wait.</small></span></label>
              </div>
              <div class="form-group pv-google">
                <label for="fGProj">Google Cloud project ID</label>
                <input type="text" id="fGProj" name="google_project" class="form-control" maxlength="63" value="<?php echo $h($s['google_project']); ?>" placeholder="e.g. strokes-sales-ai">
              </div>
              <div class="form-group pv-google">
                <label for="fGLoc">Agent region</label>
                <select id="fGLoc" name="google_location" class="form-control">
                  <?php foreach ($google_locations as $k => $lbl) { ?><option value="<?php echo $k; ?>"<?php echo $sel($s['google_location'], $k); ?>><?php echo $h($lbl); ?></option><?php } ?>
                </select>
              </div>
              <div class="form-group pv-google">
                <label for="fGAgent">Agent ID</label>
                <input type="text" id="fGAgent" name="google_agent_id" class="form-control" maxlength="64" value="<?php echo $h($s['google_agent_id']); ?>" placeholder="From the agent's URL in the Google console">
              </div>
              <div class="form-group pv-google">
                <label for="fGLang">Default language code</label>
                <input type="text" id="fGLang" name="google_language" class="form-control" maxlength="5" value="<?php echo $h($s['google_language']); ?>" placeholder="en">
                <span class="hint">Used for agents set to English + Arabic.</span>
              </div>
              <div class="form-group wide pv-google">
                <label for="fGSa">Service account key (JSON)</label>
                <textarea id="fGSa" name="google_sa" class="form-control" rows="3" autocomplete="off" spellcheck="false" placeholder="<?php echo $has_sa ? 'Saved. Leave empty to keep it' : 'Paste the JSON key of a service account with the Dialogflow API Client role'; ?>"></textarea>
                <span class="hint">Only used to check the agent. Stored in the CRM database only and never shown again.</span>
              </div>
              <div class="form-group wide pv-key">
                <label for="fKey">API key</label>
                <input type="password" id="fKey" name="api_key" class="form-control" autocomplete="new-password" placeholder="<?php echo $has_key ? 'Saved. Leave empty to keep it' : 'Paste the private API key'; ?>">
                <span class="hint">Stored in the CRM database only. It is never shown again.</span>
              </div>
              <div class="form-group pv-vapi">
                <label for="fPhoneId">Vapi phone number ID</label>
                <input type="text" id="fPhoneId" name="phone_number_id" class="form-control" maxlength="100" value="<?php echo $h($s['phone_number_id']); ?>">
                <span class="hint">Vapi dashboard, Phone Numbers. Import your SIP trunk or 3CX number there.</span>
              </div>
              <div class="form-group pv-bland">
                <label for="fFrom">Caller number</label>
                <input type="tel" id="fFrom" name="from_number" class="form-control" maxlength="20" value="<?php echo $h($s['from_number']); ?>" placeholder="Optional">
                <span class="hint">A number in your Bland account. Empty: Bland picks one.</span>
              </div>
              <div class="form-group pv-vapi">
                <label for="fModel">AI model</label>
                <input type="text" id="fModel" name="model" class="form-control" maxlength="60" value="<?php echo $h($s['model']); ?>">
                <span class="hint">OpenAI model Vapi uses for the conversation.</span>
              </div>
              <div class="form-group wide pv-custom">
                <label for="fEnd">Endpoint</label>
                <input type="url" id="fEnd" name="endpoint" class="form-control" maxlength="255" value="<?php echo $h($s['endpoint']); ?>" placeholder="https://…">
                <span class="hint pv-google">Google agents answer calls but do not dial out. The calling bridge (a Cloud Run service, telephony partner, or 3CX SIP trunk into Google) receives each call as JSON (phone, google.session, parameters, webhook_url), dials the customer and connects the agent. In the agent, add a webhook to the results link below with tag <code>crm_context</code> at the start and <code>call_end</code> at the end, setting session parameters outcome, summary, transcript and followup_date.</span>
                <span class="hint pv-nogoogle">The CRM posts each call as JSON (phone, contact, prompt, first_message, webhook_url, crm_call_id). Your flow places the call and posts the result to the webhook below. The API key, if set, is sent as a Bearer token.</span>
              </div>
            </div>
          </div>
        </div>

        <div class="sx-card">
          <div class="sx-card-h"><h3><i class="fa fa-sliders"></i> Calling</h3></div>
          <div class="sx-card-b">
            <div class="f2">
              <div class="form-group">
                <label for="fCompany">Company name the agents use</label>
                <input type="text" id="fCompany" name="company" class="form-control" maxlength="100" value="<?php echo $h($s['company']); ?>">
              </div>
              <div class="form-group">
                <label for="fCc">Default country code</label>
                <input type="text" id="fCc" name="country_code" class="form-control" inputmode="numeric" maxlength="4" value="<?php echo $h($s['country_code']); ?>">
                <span class="hint">Added to local numbers such as 050…</span>
              </div>
              <div class="form-group">
                <label for="fTz">Time zone for calling hours</label>
                <select id="fTz" name="timezone" class="form-control">
                  <?php foreach ($zones as $z) { ?><option<?php echo $sel($s['timezone'], $z); ?>><?php echo $h($z); ?></option><?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label for="fConc">Calls at the same time</label>
                <input type="number" id="fConc" name="concurrency" class="form-control" min="1" max="20" value="<?php echo (int)$s['concurrency']; ?>">
                <span class="hint">Across all agents. Check your provider plan's limit.</span>
              </div>
            </div>
          </div>
          <div class="sx-card-f">
            <button type="submit" class="btn btn-primary" id="btnSave"><i class="fa fa-check"></i> Save</button>
            <button type="button" class="btn btn-default" id="btnTest"><i class="fa fa-plug"></i> Check connection</button>
            <span id="saveMsg" class="sx-help" role="status"><?php echo $not_ready ? $h($not_ready) : 'AI calling is on.'; ?></span>
          </div>
        </div>
      </div>

      <aside class="cp-side" style="order:0">
        <div class="sx-card">
          <div class="sx-card-h"><h3><i class="fa fa-link"></i> Results webhook</h3></div>
          <div class="sx-card-b" style="display:block">
            <p class="hint" style="margin:0 0 6px">Sent with every call automatically. Use it in a custom flow.</p>
            <div class="copy"><input type="text" class="form-control" id="hookUrl" readonly value="<?php echo $h($webhook_url); ?>"><button type="button" class="btn btn-default btn-sm" data-copy="#hookUrl">Copy</button></div>
            <p class="hint" style="margin:12px 0 6px">Optional cron job (every minute) so calls keep flowing when nobody has the AI page open:</p>
            <div class="copy"><input type="text" class="form-control" id="cronUrl" readonly value="<?php echo $h($cron_url); ?>"><button type="button" class="btn btn-default btn-sm" data-copy="#cronUrl">Copy</button></div>
            <button type="button" class="btn btn-link btn-sm" id="btnRenew" style="padding-left:0;margin-top:8px">Renew the key</button>
          </div>
        </div>
        <div class="sx-card">
          <div class="sx-card-h"><h3><i class="fa fa-magic"></i> Call results</h3></div>
          <div class="sx-card-b" style="display:block">
            <p class="hint" style="margin:0"><?php echo $groq ? 'Transcripts are read by the Groq AI set up in Third-Party API to decide the result, summary and follow-up date.' : 'Groq is not set up in Third-Party API, so results use the provider summary and simple rules only.'; ?></p>
          </div>
        </div>
      </aside>
    </form>
  </section>
</div>
<script>
$(function () {
  var base = '<?php echo $base; ?>';
  function prov() {
    var p = $('#fProv').val();
    $('.pv-vapi').toggle(p === 'vapi');
    $('.pv-bland').toggle(p === 'bland');
    $('.pv-google').toggle(p === 'google');
    $('.pv-nogoogle').toggle(p !== 'google');
    $('.pv-custom').toggle(p === 'custom' || p === 'google');
    $('.pv-key').toggle(p !== '');
    $('#fKey').closest('.form-group').find('label').text(p === 'custom' ? 'Bearer token (optional)' : (p === 'google' ? 'Calling bridge token (optional)' : 'API key'));
    $('#fEnd').closest('.form-group').find('label').text(p === 'google' ? 'Calling bridge URL' : 'Endpoint');
  }
  $('#fProv').on('change', prov);
  prov();
  $('#stForm').on('submit', function (e) {
    e.preventDefault();
    var b = $('#btnSave').prop('disabled', true);
    $('#saveMsg').text('Saving…');
    $.post(base + 'ai-agents/ajax_save_settings', $(this).serialize(), function (r) {
      $('#saveMsg').text((r && r.message) || 'Not saved');
      if (r && r.status) $('#fKey, #fGSa').val('');
      b.prop('disabled', false);
    }, 'json').fail(function () { $('#saveMsg').text('The server could not save.'); b.prop('disabled', false); });
  });
  $('#btnTest').on('click', function () {
    $('#saveMsg').text('Checking… (save first if you changed anything)');
    $.post(base + 'ai-agents/ajax_test_connection', {}, function (r) { $('#saveMsg').text((r && r.message) || 'No answer'); }, 'json');
  });
  $('[data-copy]').on('click', function () {
    var i = $($(this).data('copy'))[0], b = $(this);
    i.select();
    (navigator.clipboard ? navigator.clipboard.writeText(i.value) : Promise.reject()).catch(function () { document.execCommand('copy'); });
    b.text('Copied');
    setTimeout(function () { b.text('Copy'); }, 1500);
  });
  $('#btnRenew').on('click', function () {
    if (!confirm('Make a new key? Calls in progress and any cron job must use the new links.')) return;
    $.post(base + 'ai-agents/ajax_renew_key', {}, function (r) {
      if (r && r.status) { $('#hookUrl').val(r.webhook_url); $('#cronUrl').val(r.cron_url); }
    }, 'json');
  });
});
</script>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
