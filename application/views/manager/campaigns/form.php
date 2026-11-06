<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$c = is_array($campaign) ? $campaign : array();
$v = function ($k, $def = '') use ($c) { return isset($c[$k]) && $c[$k] !== null ? $c[$k] : $def; };
$id = (int)$v('id', 0);
$sel = function ($a, $b) { return (string)$a === (string)$b ? ' selected' : ''; };
$chk = function ($a, $b) { return (string)$a === (string)$b ? ' checked' : ''; };
$stages = array_filter(array_map('intval', explode(',', (string)$v('status_ids'))));
$people = array_filter(array_map('intval', explode(',', (string)$v('participants'))));
if (!$c) $people = array((int)$me);
$base = base_url() . 'manager/';
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-campaigns.css?v=20261107_2">
<div class="content-wrapper sx cp">
  <section class="sx-head">
    <h1><?php echo $id ? 'Edit campaign' : 'New campaign'; ?><small><?php echo $id ? $h($v('name')) : 'Choose who to call, who calls, and how'; ?></small></h1>
    <div class="sx-btns"><a class="btn btn-default" href="<?php echo $base; ?>campaigns<?php echo $id ? '/view/' . $id : ''; ?>"><i class="fa fa-times"></i> Cancel</a></div>
  </section>

  <section class="content">
    <nav class="cp-steps" id="cpSteps" aria-label="Form sections">
      <a href="#s1" class="is-on"><b>1</b> Basics</a>
      <a href="#s2"><b>2</b> Audience</a>
      <a href="#s3"><b>3</b> Team &amp; goals</a>
      <a href="#s4"><b>4</b> Calling rules &amp; script</a>
    </nav>

    <form id="cpForm" class="cp-form" autocomplete="off" novalidate>
      <input type="hidden" name="id" value="<?php echo $id; ?>">
      <div>
        <!-- 1 Basics -->
        <div class="sx-card" id="s1">
          <div class="sx-card-h"><h3><span class="n">1</span> Basics</h3></div>
          <div class="sx-card-b">
            <div class="f2">
              <div class="form-group wide">
                <label for="fName">Campaign name <span class="req">*</span></label>
                <input type="text" id="fName" name="name" class="form-control" maxlength="150" required value="<?php echo $h($v('name')); ?>" placeholder="e.g. Dubai trading companies – Q4 outreach">
              </div>
              <div class="form-group">
                <label for="fObjective">Objective</label>
                <select id="fObjective" name="objective" class="form-control">
                  <?php foreach ($objectives as $k => $lbl) { ?><option value="<?php echo $k; ?>"<?php echo $sel($v('objective', 'lead_generation'), $k); ?>><?php echo $h($lbl); ?></option><?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label>Priority</label>
                <div class="seg">
                  <?php foreach ($priorities as $p) { ?><label><input type="radio" name="priority" value="<?php echo $p; ?>"<?php echo $chk($v('priority', 'Normal'), $p); ?>><span><?php echo $p; ?></span></label><?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label for="fRef">Reference number</label>
                <input type="text" id="fRef" name="reference_no" class="form-control" inputmode="numeric" maxlength="20" value="<?php echo $h($v('reference_no')); ?>" placeholder="Optional, digits only">
              </div>
              <div class="form-group">
                <label>Status</label>
                <div class="seg">
                  <?php foreach (array('Active' => 'Running', 'Paused' => 'Paused', 'Completed' => 'Completed') as $k => $lbl) { if (!$id && $k === 'Completed') continue; ?><label><input type="radio" name="status" value="<?php echo $k; ?>"<?php echo $chk($v('status', 'Active'), $k); ?>><span><?php echo $lbl; ?></span></label><?php } ?>
                </div>
              </div>
              <div class="form-group wide">
                <label for="fDesc">Notes for the team</label>
                <textarea id="fDesc" name="description" class="form-control" rows="2" placeholder="What this campaign is about, offer details, anything callers should know"><?php echo $h($v('description')); ?></textarea>
              </div>
            </div>
          </div>
        </div>

        <!-- 2 Audience -->
        <div class="sx-card" id="s2">
          <div class="sx-card-h"><h3><span class="n">2</span> Audience <small>Records of the people in step 3 that match, with a phone number</small></h3></div>
          <div class="sx-card-b">
            <div class="form-group" style="margin-bottom:14px">
              <label>Record type</label>
              <div class="seg">
                <?php foreach (array('all' => 'Database and leads', 'database' => 'Database only', 'lead' => 'Leads only') as $k => $lbl) { ?><label><input type="radio" name="pipeline_type" value="<?php echo $k; ?>" class="aud"<?php echo $chk($v('pipeline_type', 'all'), $k); ?>><span><?php echo $lbl; ?></span></label><?php } ?>
              </div>
            </div>
            <div class="f3">
              <div class="form-group">
                <label for="fCountry">Country</label>
                <select id="fCountry" name="country" class="form-control aud">
                  <option value="0">Any country</option>
                  <?php foreach ($countries as $co) { ?><option value="<?php echo (int)$co['id']; ?>"<?php echo $sel($v('country', 0), $co['id']); ?>><?php echo $h($co['name']); ?></option><?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label for="fState">State / emirate</label>
                <select id="fState" name="state" class="form-control aud">
                  <option value="0">Any</option>
                  <?php foreach ($states as $st) { ?><option value="<?php echo (int)$st['id']; ?>"<?php echo $sel($v('state', 0), $st['id']); ?>><?php echo $h($st['name']); ?></option><?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label for="fCity">City / area</label>
                <input type="text" id="fCity" name="city" class="form-control aud" value="<?php echo $h($v('city')); ?>" placeholder="Any">
              </div>
              <div class="form-group">
                <label for="fIndustry">Industry</label>
                <select id="fIndustry" name="industry_category" class="form-control aud">
                  <option value="">Any industry</option>
                  <?php foreach ((array)$business_categories as $bc) { ?><option value="<?php echo $h($bc['title']); ?>"<?php echo $sel($v('industry_category'), $bc['title']); ?>><?php echo $h($bc['title']); ?></option><?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label for="fDbCat">Database category</label>
                <select id="fDbCat" name="database_category" class="form-control aud">
                  <option value="">Any category</option>
                  <?php foreach ((array)$database_categories as $dc) { ?><option value="<?php echo $h($dc['title']); ?>"<?php echo $sel($v('database_category'), $dc['title']); ?>><?php echo $h($dc['title']); ?></option><?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label for="fSource">Data source</label>
                <select id="fSource" name="data_source" class="form-control aud">
                  <option value="">Any source</option>
                  <?php foreach ($data_sources as $ds) { ?><option value="<?php echo $h($ds['data_source']); ?>"<?php echo $sel($v('data_source'), $ds['data_source']); ?>><?php echo $h($ds['data_source']); ?></option><?php } ?>
                </select>
              </div>
              <div class="form-group wide">
                <label>Stages <span class="hint" style="display:inline">(none ticked = every stage)</span></label>
                <div class="pills">
                  <?php foreach ($statuses as $st) { ?><label><input type="checkbox" name="status_ids[]" class="aud" value="<?php echo (int)$st['id']; ?>"<?php echo in_array((int)$st['id'], $stages, true) ? ' checked' : ''; ?>><span><?php echo $h($st['status']); ?></span></label><?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 3 Team & goals -->
        <div class="sx-card" id="s3">
          <div class="sx-card-h"><h3><span class="n">3</span> Team &amp; goals</h3></div>
          <div class="sx-card-b">
            <div class="form-group" style="margin-bottom:14px">
              <label>Who calls <span class="hint" style="display:inline">(each person dials their own matching records)</span></label>
              <div class="people">
                <div class="people-bar">
                  <input type="search" id="pplSearch" class="form-control" placeholder="Find a person" aria-label="Find a person">
                  <button type="button" id="pplAll">Select all</button>
                  <button type="button" id="pplNone">Clear</button>
                </div>
                <div class="people-list" id="pplList">
                  <?php $listed = array(); foreach ($users as $u) { $listed[(int)$u['id']] = true; ?>
                    <label data-n="<?php echo $h(strtolower($u['name'])); ?>"><input type="checkbox" name="participants[]" class="aud" value="<?php echo (int)$u['id']; ?>"<?php echo in_array((int)$u['id'], $people, true) ? ' checked' : ''; ?>><span><?php echo $h($u['name']) . ((int)$u['id'] === (int)$me ? ' (you)' : ''); ?></span></label>
                  <?php } ?>
                  <?php foreach ($people as $p) { if (isset($listed[$p])) continue; ?><input type="hidden" name="participants[]" value="<?php echo (int)$p; ?>"><?php } ?>
                </div>
              </div>
            </div>
            <div class="f3">
              <div class="form-group">
                <label for="fDaily">Daily calls per person</label>
                <input type="number" id="fDaily" name="daily_target" class="form-control" min="0" max="100000" value="<?php echo (int)$v('daily_target', 0) ?: ''; ?>" placeholder="e.g. 60">
              </div>
              <div class="form-group">
                <label for="fStart">Start date</label>
                <input type="date" id="fStart" name="start_date" class="form-control" value="<?php echo $h($v('start_date')); ?>">
              </div>
              <div class="form-group">
                <label for="fEnd">End date</label>
                <input type="date" id="fEnd" name="end_date" class="form-control" value="<?php echo $h($v('end_date')); ?>">
              </div>
              <div class="form-group">
                <label for="fGoalType">Campaign goal</label>
                <select id="fGoalType" name="goal_type" class="form-control">
                  <?php foreach ($goal_types as $k => $lbl) { ?><option value="<?php echo $k; ?>"<?php echo $sel($v('goal_type'), $k); ?>><?php echo $h($lbl); ?></option><?php } ?>
                </select>
              </div>
              <div class="form-group" id="goalWrap">
                <label for="fGoal">Goal number</label>
                <input type="number" id="fGoal" name="goal_target" class="form-control" min="0" value="<?php echo (int)$v('goal_target', 0) ?: ''; ?>" placeholder="e.g. 500">
              </div>
            </div>
          </div>
        </div>

        <!-- 4 Calling rules & script -->
        <div class="sx-card" id="s4">
          <div class="sx-card-h"><h3><span class="n">4</span> Calling rules &amp; script</h3></div>
          <div class="sx-card-b">
            <div class="f2" style="margin-bottom:14px">
              <div class="form-group">
                <label for="fAttempts">Tries per record</label>
                <select id="fAttempts" name="max_attempts" class="form-control">
                  <?php foreach (array(1, 2, 3, 4, 5, 6, 8, 10, 0) as $a) { ?><option value="<?php echo $a; ?>"<?php echo $sel($v('max_attempts', 3), $a); ?>><?php echo $a ? $a . ($a === 1 ? ' try' : ' tries') : 'No limit'; ?></option><?php } ?>
                </select>
                <span class="hint">After this many calls a record leaves the campaign queue.</span>
              </div>
              <div class="form-group">
                <label for="fRetry">Wait before calling again</label>
                <select id="fRetry" name="retry_hours" class="form-control">
                  <?php foreach (array(0 => 'No wait', 1 => '1 hour', 2 => '2 hours', 4 => '4 hours', 24 => '1 day', 48 => '2 days', 72 => '3 days', 168 => '1 week') as $k => $lbl) { ?><option value="<?php echo $k; ?>"<?php echo $sel($v('retry_hours', 24), $k); ?>><?php echo $lbl; ?></option><?php } ?>
                </select>
                <span class="hint">A record called recently is skipped until then.</span>
              </div>
            </div>
            <?php if ($can_ai) { ?>
            <div class="form-group" style="margin-bottom:14px">
              <label for="fAi">AI agent</label>
              <select id="fAi" name="ai_agent_id" class="form-control">
                <option value="0">None: people call this campaign</option>
                <?php foreach ($ai_agents as $ag) { ?><option value="<?php echo (int)$ag['id']; ?>"<?php echo $sel($v('ai_agent_id', 0), $ag['id']); ?>><?php echo $h($ag['name'] . ($ag['status'] !== 'Active' ? ' (' . $ag['status'] . ')' : '')); ?></option><?php } ?>
              </select>
              <span class="hint">The AI agent calls the records first; interested customers come back to the record owner as follow-ups. People can still dial the campaign.</span>
            </div>
            <?php } ?>
            <div class="form-group">
              <label for="fScript">Call script</label>
              <textarea id="fScript" name="script" class="form-control script" placeholder="Hello {contact}, this is {agent} from 7 Strokes. I'm calling {customer} about…&#10;&#10;Key points:&#10;- …&#10;&#10;If they are interested: book a meeting / send the offer"><?php echo $h($v('script')); ?></textarea>
              <div class="tokens" aria-label="Insert a field">
                <button type="button" data-t="{contact}" title="Contact person">{contact}</button>
                <button type="button" data-t="{customer}" title="Company name">{customer}</button>
                <button type="button" data-t="{agent}" title="Caller's name">{agent}</button>
              </div>
              <span class="hint">Shown in the dialer while calling; the fields are filled in for each record.</span>
            </div>
          </div>
          <div class="sx-card-f">
            <button type="submit" class="btn btn-primary" id="btnSave"><i class="fa fa-check"></i> <?php echo $id ? 'Save changes' : 'Create campaign'; ?></button>
            <button type="button" class="btn btn-default" id="btnSaveDial"><i class="fa fa-phone"></i> Save and start dialing</button>
            <span id="saveMsg" class="sx-help" role="status"></span>
          </div>
        </div>
      </div>

      <aside class="cp-side">
        <div class="sx-card">
          <div class="sx-card-h"><h3><i class="fa fa-crosshairs"></i> Audience</h3></div>
          <div class="sx-card-b">
            <div><div class="big" id="audTotal">–</div><div class="lbl">records to call</div></div>
            <ul id="audPeople"></ul>
            <div class="est" id="audEst"></div>
            <div class="warn" id="audWarn"><i class="fa fa-exclamation-triangle"></i> No records match. Widen the filters or add people.</div>
          </div>
        </div>
      </aside>
    </form>
  </section>
</div>

<script>
$(function () {
  var base = '<?php echo $base; ?>', form = $('#cpForm'), timer = null, req = null, total = 0;

  function estimate() {
    var daily = parseInt($('#fDaily').val(), 10) || 0, ppl = $('#pplList input:checked').length || 1, t = $('#audEst');
    if (!daily || !total) { t.text(''); return; }
    var days = Math.ceil(total / (daily * ppl));
    t.text('About ' + days + ' working day' + (days === 1 ? '' : 's') + ' to call everyone once at ' + daily + ' calls per person a day.');
  }
  function audience() {
    clearTimeout(timer);
    timer = setTimeout(function () {
      if (req) req.abort();
      $('#audTotal').addClass('ld');
      var data = form.find('.aud').serializeArray();
      req = $.post(base + 'campaigns/ajax_audience', $.param(data), function (r) {
        if (!r || !r.status) return;
        total = r.total;
        $('#audTotal').text(r.total.toLocaleString()).removeClass('ld');
        var ul = $('#audPeople').empty();
        $.each(r.people, function (_, p) { ul.append($('<li>').append($('<span>').text(p.name), $('<b>').text(p.n.toLocaleString()))); });
        $('#audWarn').toggle(r.total === 0);
        estimate();
      }, 'json');
    }, 350);
  }
  form.on('change', '.aud', audience);
  $('#fCity').on('input', audience);
  $('#fDaily').on('input', estimate);
  audience();

  $('#fCountry').on('change', function () {
    var s = $('#fState').html('<option value="0">Any</option>');
    if (+this.value) $.post('<?php echo base_url(); ?>ci_admin_user/get_state', { c_id: this.value }, function (html) { s.append(html); });
  });

  function goalUi() { $('#goalWrap').toggle($('#fGoalType').val() !== ''); }
  $('#fGoalType').on('change', goalUi);
  goalUi();

  $('#pplSearch').on('input', function () {
    var q = $.trim(this.value).toLowerCase();
    $('#pplList label').each(function () { this.style.display = !q || this.getAttribute('data-n').indexOf(q) !== -1 ? '' : 'none'; });
  });
  $('#pplAll, #pplNone').on('click', function () {
    var on = this.id === 'pplAll';
    $('#pplList label:visible input').prop('checked', on);
    audience();
  });

  $('.tokens').on('click', 'button', function () {
    var ta = document.getElementById('fScript'), t = $(this).data('t'), s = ta.selectionStart || ta.value.length;
    ta.value = ta.value.slice(0, s) + t + ta.value.slice(ta.selectionEnd || s);
    ta.focus();
    ta.selectionStart = ta.selectionEnd = s + t.length;
  });

  // highlight the section in view
  var steps = $('#cpSteps a');
  steps.on('click', function (e) {
    e.preventDefault();
    var el = document.querySelector(this.getAttribute('href'));
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (en) {
      en.forEach(function (x) {
        if (x.isIntersecting) { steps.removeClass('is-on'); steps.filter('[href="#' + x.target.id + '"]').addClass('is-on'); }
      });
    }, { rootMargin: '-30% 0px -60% 0px' });
    $('#s1,#s2,#s3,#s4').each(function () { io.observe(this); });
  }

  function save(dial) {
    var name = $.trim($('#fName').val());
    if (!name) { $('#fName').focus(); $('#saveMsg').text('Give the campaign a name.'); return; }
    var s = $('#fStart').val(), e = $('#fEnd').val();
    if (s && e && e < s) { $('#fEnd').focus(); $('#saveMsg').text('The end date is before the start date.'); return; }
    var btns = $('#btnSave, #btnSaveDial').prop('disabled', true);
    $('#saveMsg').text('Saving…');
    $.post(base + 'campaigns/ajax_save', form.serialize(), function (r) {
      if (r && r.status) {
        location.href = base + (dial ? 'calls?campaign=' + r.id : 'campaigns/view/' + r.id);
      } else {
        $('#saveMsg').text((r && r.message) || 'Not saved');
        btns.prop('disabled', false);
      }
    }, 'json').fail(function () { $('#saveMsg').text('The server could not save the campaign.'); btns.prop('disabled', false); });
  }
  form.on('submit', function (e) { e.preventDefault(); save(false); });
  $('#btnSaveDial').on('click', function () { save(true); });
});
</script>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
