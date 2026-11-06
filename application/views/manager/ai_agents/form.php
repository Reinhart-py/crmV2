<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$a = is_array($agent) ? $agent : array();
$v = function ($k, $def = '') use ($a) { return isset($a[$k]) && $a[$k] !== null ? $a[$k] : $def; };
$id = (int)$v('id', 0);
$sel = function ($x, $y) { return (string)$x === (string)$y ? ' selected' : ''; };
$chk = function ($x, $y) { return (string)$x === (string)$y ? ' checked' : ''; };
$days = array_map('intval', explode(',', (string)$v('call_days', '1,2,3,4,5')));
$base = base_url() . 'manager/';
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-campaigns.css?v=20261107_2">
<div class="content-wrapper sx cp">
  <section class="sx-head">
    <h1><?php echo $id ? 'Edit AI agent' : 'New AI agent'; ?><small><?php echo $id ? $h($v('name')) : 'What the agent says, what it aims for, and when it calls'; ?></small></h1>
    <div class="sx-btns"><a class="btn btn-default" href="<?php echo $base; ?>ai-agents<?php echo $id ? '/view/' . $id : ''; ?>"><i class="fa fa-times"></i> Cancel</a></div>
  </section>

  <section class="content">
    <nav class="cp-steps" id="cpSteps" aria-label="Form sections">
      <a href="#s1" class="is-on"><b>1</b> Agent &amp; voice</a>
      <a href="#s2"><b>2</b> Pitch &amp; goal</a>
      <a href="#s3"><b>3</b> Conversation</a>
      <a href="#s4"><b>4</b> Calling rules</a>
    </nav>

    <form id="aiForm" class="cp-form" autocomplete="off" novalidate>
      <input type="hidden" name="id" value="<?php echo $id; ?>">
      <div>
        <div class="sx-card" id="s1">
          <div class="sx-card-h"><h3><span class="n">1</span> Agent &amp; voice</h3></div>
          <div class="sx-card-b">
            <div class="f2">
              <div class="form-group">
                <label for="fName">Agent name <span class="req">*</span></label>
                <input type="text" id="fName" name="name" class="form-control pv" maxlength="100" required value="<?php echo $h($v('name')); ?>" placeholder="e.g. Sara">
                <span class="hint">The name the agent uses on the call.</span>
              </div>
              <div class="form-group">
                <label>Status</label>
                <div class="seg">
                  <?php foreach (array('Draft', 'Active', 'Paused') as $st) { ?><label><input type="radio" name="status" value="<?php echo $st; ?>"<?php echo $chk($v('status', 'Draft'), $st); ?>><span><?php echo $st; ?></span></label><?php } ?>
                </div>
                <span class="hint">Only Active agents place calls.</span>
              </div>
              <div class="form-group">
                <label for="fLang">Language</label>
                <select id="fLang" name="language" class="form-control pv">
                  <?php foreach ($languages as $k => $lbl) { ?><option value="<?php echo $k; ?>"<?php echo $sel($v('language', 'en'), $k); ?>><?php echo $h($lbl); ?></option><?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label>Voice</label>
                <div class="seg">
                  <?php foreach ($voices as $k => $lbl) { ?><label><input type="radio" name="voice" value="<?php echo $k; ?>"<?php echo $chk($v('voice', 'female'), $k); ?>><span><?php echo $h($lbl); ?></span></label><?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label for="fVoiceId">Provider voice ID</label>
                <input type="text" id="fVoiceId" name="voice_id" class="form-control" maxlength="100" value="<?php echo $h($v('voice_id')); ?>" placeholder="Optional">
                <span class="hint">A specific voice from your provider's voice library. Leave empty for the default.</span>
              </div>
              <div class="form-group">
                <label class="tog"><input type="checkbox" name="disclose_ai" value="1" class="pv"<?php echo $v('disclose_ai', 1) ? ' checked' : ''; ?>><span>Say it is an AI assistant<small>Recommended: customers are told at the start of the call.</small></span></label>
              </div>
            </div>
          </div>
        </div>

        <div class="sx-card" id="s2">
          <div class="sx-card-h"><h3><span class="n">2</span> Pitch &amp; goal</h3></div>
          <div class="sx-card-b">
            <div class="form-group" style="margin-bottom:14px">
              <label>Goal of the call</label>
              <div class="pills">
                <?php foreach ($goals as $k => $lbl) { ?><label><input type="radio" name="goal" value="<?php echo $k; ?>" class="pv"<?php echo $chk($v('goal', 'book_meeting'), $k); ?>><span><?php echo $h($lbl); ?></span></label><?php } ?>
              </div>
            </div>
            <div class="form-group" style="margin-bottom:14px">
              <label for="fOpening">Opening line</label>
              <input type="text" id="fOpening" name="opening" class="form-control pv" maxlength="500" value="<?php echo $h($v('opening')); ?>" placeholder="Hello, is this {contact}? This is {agent} from {company}.">
              <span class="hint">The first thing the agent says. Fields: {contact}, {customer}, {agent}, {company}.</span>
            </div>
            <div class="form-group">
              <label for="fPitch">What we offer</label>
              <textarea id="fPitch" name="pitch" class="form-control script pv" maxlength="4000" placeholder="- Business mobile plans for companies in the UAE, from AED 99 a month per line&#10;- Free number transfer and a dedicated account manager&#10;- Offer this month: 3 months free on the 10+ line plan"><?php echo $h($v('pitch')); ?></textarea>
              <span class="hint">Short facts the agent may use. It will not invent prices or promises beyond this.</span>
            </div>
          </div>
        </div>

        <div class="sx-card" id="s3">
          <div class="sx-card-h"><h3><span class="n">3</span> Conversation</h3></div>
          <div class="sx-card-b">
            <div class="form-group" style="margin-bottom:14px">
              <label for="fQuestions">Questions to ask</label>
              <textarea id="fQuestions" name="questions" class="form-control pv" rows="4" maxlength="3000" placeholder="- How many mobile lines does the company use today?&#10;- Which provider are you with, and when does the contract end?&#10;- Who decides on telecom services?"><?php echo $h($v('questions')); ?></textarea>
            </div>
            <div class="form-group" style="margin-bottom:14px">
              <label for="fObj">Objections and answers</label>
              <textarea id="fObj" name="objections" class="form-control pv" rows="4" maxlength="4000" placeholder="- &quot;We already have a provider&quot;: Offer a free cost comparison against their current bill.&#10;- &quot;Send me an email&quot;: Confirm the email and offer a 10-minute call with a specialist."><?php echo $h($v('objections')); ?></textarea>
            </div>
            <div class="f2">
              <div class="form-group">
                <label for="fClosing">Closing</label>
                <textarea id="fClosing" name="closing" class="form-control pv" rows="2" maxlength="1000" placeholder="Confirm the next step, thank them, and say a colleague will be in touch."><?php echo $h($v('closing')); ?></textarea>
              </div>
              <div class="form-group">
                <label for="fTransfer">Transfer interested customers to</label>
                <input type="tel" id="fTransfer" name="transfer_number" class="form-control pv" maxlength="30" value="<?php echo $h($v('transfer_number')); ?>" placeholder="Optional, e.g. 04 123 4567">
                <span class="hint">A sales line or 3CX queue that can take live calls. Empty: the agent books a follow-up instead.</span>
              </div>
            </div>
          </div>
        </div>

        <div class="sx-card" id="s4">
          <div class="sx-card-h"><h3><span class="n">4</span> Calling rules</h3></div>
          <div class="sx-card-b">
            <div class="f3" style="margin-bottom:14px">
              <div class="form-group">
                <label for="fFrom">Call from</label>
                <input type="time" id="fFrom" name="call_from" class="form-control" value="<?php echo $h($v('call_from', '09:00')); ?>">
              </div>
              <div class="form-group">
                <label for="fTo">Until</label>
                <input type="time" id="fTo" name="call_to" class="form-control" value="<?php echo $h($v('call_to', '18:00')); ?>">
              </div>
              <div class="form-group">
                <label for="fMax">Longest call</label>
                <select id="fMax" name="max_minutes" class="form-control pv">
                  <?php foreach (array(2, 3, 5, 8, 10, 15) as $m) { ?><option value="<?php echo $m; ?>"<?php echo $sel($v('max_minutes', 5), $m); ?>><?php echo $m; ?> minutes</option><?php } ?>
                </select>
              </div>
              <div class="form-group wide">
                <label>Calling days</label>
                <div class="days-pick">
                  <?php foreach (array(1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun') as $k => $lbl) { ?><label><input type="checkbox" name="call_days[]" value="<?php echo $k; ?>"<?php echo in_array($k, $days, true) ? ' checked' : ''; ?>><span><?php echo $lbl; ?></span></label><?php } ?>
                </div>
                <span class="hint">Times are <?php echo $h($timezone); ?> time. Keep cold calls to business hours.</span>
              </div>
              <div class="form-group">
                <label for="fLimit">Calls per day, at most</label>
                <input type="number" id="fLimit" name="daily_limit" class="form-control" min="1" max="5000" value="<?php echo (int)$v('daily_limit', 100); ?>">
                <span class="hint">Caps provider cost.</span>
              </div>
            </div>
            <label class="tog"><input type="checkbox" name="dnd_on_request" value="1"<?php echo $v('dnd_on_request', 1) ? ' checked' : ''; ?>><span>Mark the record DND when the customer asks not to be called<small>The record leaves every AI queue and is skipped from then on.</small></span></label>
          </div>
          <div class="sx-card-f">
            <button type="submit" class="btn btn-primary" id="btnSave"><i class="fa fa-check"></i> <?php echo $id ? 'Save changes' : 'Create agent'; ?></button>
            <span id="saveMsg" class="sx-help" role="status"></span>
          </div>
        </div>
      </div>

      <aside class="cp-side">
        <div class="sx-card">
          <div class="sx-card-h"><h3><i class="fa fa-comment-o"></i> How it sounds</h3></div>
          <div class="sx-card-b" style="display:block">
            <p class="say" id="pvFirst">…</p>
            <details>
              <summary style="cursor:pointer;font-size:13px;font-weight:600">Full instructions for the AI</summary>
              <pre class="prompt-box" id="pvPrompt" style="margin-top:8px"></pre>
            </details>
            <p class="hint" style="margin-top:8px">Sample customer: Ahmed, Al Noor Trading LLC.</p>
          </div>
        </div>
        <?php if ($id) { ?>
        <div class="sx-card">
          <div class="sx-card-h"><h3><i class="fa fa-phone"></i> Test call</h3></div>
          <div class="sx-card-b" style="display:block">
            <?php if ($not_ready) { ?>
              <p class="hint" style="margin:0"><?php echo $h($not_ready); ?></p>
            <?php } else { ?>
              <p class="hint" style="margin:0">The agent calls your own phone with the saved settings.</p>
              <div class="test-row"><input type="tel" id="testPhone" class="form-control" placeholder="Your mobile number" aria-label="Your phone number"><button type="button" class="btn btn-default" id="btnTest">Call me</button></div>
              <p class="hint" id="testMsg" role="status"></p>
            <?php } ?>
          </div>
        </div>
        <?php } ?>
      </aside>
    </form>
  </section>
</div>

<script>
$(function () {
  var base = '<?php echo $base; ?>', form = $('#aiForm'), timer = null, req = null;
  function preview() {
    clearTimeout(timer);
    timer = setTimeout(function () {
      if (req) req.abort();
      req = $.post(base + 'ai-agents/ajax_preview', form.serialize(), function (r) {
        if (!r || !r.status) return;
        $('#pvFirst').text(r.first);
        $('#pvPrompt').text(r.prompt);
      }, 'json');
    }, 400);
  }
  form.on('input change', '.pv, [name=call_days\\[\\]]', preview);
  preview();

  var steps = $('#cpSteps a');
  steps.on('click', function (e) {
    e.preventDefault();
    var el = document.querySelector(this.getAttribute('href'));
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (en) {
      en.forEach(function (x) { if (x.isIntersecting) { steps.removeClass('is-on'); steps.filter('[href="#' + x.target.id + '"]').addClass('is-on'); } });
    }, { rootMargin: '-30% 0px -60% 0px' });
    $('#s1,#s2,#s3,#s4').each(function () { io.observe(this); });
  }

  form.on('submit', function (e) {
    e.preventDefault();
    if (!$.trim($('#fName').val())) { $('#fName').focus(); $('#saveMsg').text('Give the agent a name.'); return; }
    if ($('#fTo').val() <= $('#fFrom').val()) { $('#fTo').focus(); $('#saveMsg').text('The end time must be after the start time.'); return; }
    if (!$('[name="call_days[]"]:checked').length) { $('#saveMsg').text('Pick at least one calling day.'); return; }
    var b = $('#btnSave').prop('disabled', true);
    $('#saveMsg').text('Saving…');
    $.post(base + 'ai-agents/ajax_save', form.serialize(), function (r) {
      if (r && r.status) location.href = base + 'ai-agents/view/' + r.id;
      else { $('#saveMsg').text((r && r.message) || 'Not saved'); b.prop('disabled', false); }
    }, 'json').fail(function () { $('#saveMsg').text('The server could not save the agent.'); b.prop('disabled', false); });
  });

  $('#btnTest').on('click', function () {
    var p = $.trim($('#testPhone').val()), b = $(this);
    if (!p) { $('#testPhone').focus(); return; }
    b.prop('disabled', true);
    $('#testMsg').text('Starting the call…');
    $.post(base + 'ai-agents/ajax_test_call', { id: <?php echo $id; ?>, phone: p }, function (r) {
      $('#testMsg').text((r && r.message) || 'No answer from the server');
      b.prop('disabled', false);
    }, 'json').fail(function () { $('#testMsg').text('The server did not respond.'); b.prop('disabled', false); });
  });
});
</script>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
