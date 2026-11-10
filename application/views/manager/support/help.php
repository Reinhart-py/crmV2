<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
// Help & Support: self-help topics (searchable, published by HR) next to the user's own chat with support.
$hp_cats = array();
foreach ($topics as $tp) { $hp_cats[$tp['category']] = true; }
?>
<style>
  .hp-intro { margin:2px 0 0; font-size:13px; color:#64748b; text-align:left; }
  [data-theme="dark"] .hp-intro { color:#94a3b8; }
  .hp-page .hp-switch { display:none; }
  .hp-page .hp-grid { display:grid; grid-template-columns:minmax(0, 7fr) minmax(0, 5fr); gap:16px; align-items:start; }
  .hp-page .hp-card { background:#fff; border:1px solid #e2e8f0; border-radius:10px; display:flex; flex-direction:column; height:calc(100vh - 160px); min-height:420px; overflow:hidden; }
  .hp-page .hp-card-head { padding:12px 16px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; }
  .hp-page .hp-card-head h4 { margin:0; font-size:15px; font-weight:700; color:#0f172a; display:flex; align-items:center; gap:8px; }
  .hp-page .hp-card-head h4 i { color:#1e3a8a; }
  .hp-page .hp-search { padding:12px 16px 0; }
  .hp-page .hp-search .input-group-addon { background:#fff; }
  .hp-page .hp-chips { display:flex; gap:6px; flex-wrap:wrap; padding:10px 16px; border-bottom:1px solid #f1f5f9; }
  .hp-page .hp-chip { border:1px solid #cbd5e1; background:#fff; border-radius:999px; padding:4px 11px; font-size:12px; font-weight:700; color:#475569; cursor:pointer; }
  .hp-page .hp-chip.active { background:#1e3a8a; border-color:#1e3a8a; color:#fff; }
  .hp-page .hp-topics { flex:1; min-height:0; overflow-y:auto; padding:12px 16px; }
  .hp-page .hp-none { text-align:center; padding:28px 12px; color:#64748b; font-size:13px; }
  .hp-page .hp-none i { display:block; font-size:24px; color:#94a3b8; margin-bottom:8px; }
  .hp-page .hp-hidden { display:none !important; }
  /* Chat */
  .hp-page .hp-chat .hp-card-head { background:#1e3a8a; border-bottom:0; }
  .hp-page .hp-chat .hp-card-head h4, .hp-page .hp-chat .hp-card-head h4 i { color:#fff; }
  .hp-page .hp-chat .hp-card-head small { color:rgba(255,255,255,.8); font-size:12px; }
  .hp-page .ms-msgs { flex:1; min-height:0; overflow-y:auto; padding:16px; background:#f8fafc; display:flex; flex-direction:column; gap:10px; }
  .hp-page .ms-msg { max-width:80%; display:flex; flex-direction:column; }
  .hp-page .ms-meta { font-size:11.5px; color:#64748b; margin:0 4px 3px; }
  .hp-page .ms-bubble { padding:9px 12px; border-radius:12px; font-size:14px; line-height:1.5; white-space:pre-wrap; word-wrap:break-word; }
  .hp-page .ms-me { align-self:flex-end; align-items:flex-end; }
  .hp-page .ms-me .ms-bubble { background:#1e3a8a; color:#fff; border-bottom-right-radius:4px; }
  .hp-page .ms-them { align-self:flex-start; }
  .hp-page .ms-them .ms-bubble { background:#fff; border:1px solid #e2e8f0; color:#0f172a; border-bottom-left-radius:4px; }
  .hp-page .ms-system { align-self:center; max-width:90%; }
  .hp-page .ms-system .ms-bubble { color:#64748b; font-size:12.5px; text-align:center; padding:2px 6px; }
  .hp-page .ms-empty { margin:auto; color:#94a3b8; font-size:14px; text-align:center; padding:0 10px; }
  .hp-page .ms-compose { display:flex; gap:8px; padding:12px; border-top:1px solid #e2e8f0; }
  .hp-page .ms-compose textarea { resize:none; height:44px; }
  /* Live chat offline: send a message instead */
  .hp-page .ms-off { padding:12px 14px; border-top:1px solid #e2e8f0; background:#fff; }
  .hp-page .ms-off-note { display:flex; gap:9px; align-items:flex-start; background:#fff7ed; border:1px solid #fed7aa; color:#9a3412; border-radius:10px; padding:9px 11px; font-size:13px; line-height:1.45; margin-bottom:10px; }
  .hp-page .ms-off-note i { margin-top:3px; }
  .hp-page .ms-off label { font-size:12px; font-weight:600; color:#475569; margin:0 0 3px; }
  .hp-page .ms-off .form-group { margin-bottom:8px; }
  .hp-page .ms-off textarea { resize:vertical; min-height:70px; }
  .hp-page .ms-off .ms-err { color:#b91c1c; font-size:12px; margin-top:3px; }
  .hp-page .ms-off .has-error .form-control { border-color:#dc2626; }
  .hp-page .ms-off-done { background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; border-radius:10px; padding:10px 12px; font-size:13px; line-height:1.45; }
  .hp-page .ms-live { display:inline-flex; align-items:center; gap:6px; }
  .hp-page .ms-live::before { content:""; width:8px; height:8px; border-radius:50%; background:#94a3b8; }
  .hp-page .ms-live.is-on::before { background:#22c55e; }
  [data-theme="dark"] .hp-page .ms-off { background:transparent; border-top-color:rgba(255,255,255,.08); }
  [data-theme="dark"] .hp-page .ms-off label { color:#cbd5e1; }
  [data-theme="dark"] .hp-page .ms-off-note { background:rgba(251,146,60,.12); border-color:rgba(251,146,60,.35); color:#fdba74; }
  [data-theme="dark"] .hp-page .ms-off-done { background:rgba(16,185,129,.12); border-color:rgba(16,185,129,.35); color:#6ee7b7; }
  @media (max-width: 991px) {
    .hp-page .hp-switch { display:grid; grid-template-columns:1fr 1fr; border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; margin-bottom:12px; background:#fff; }
    .hp-page .hp-switch button { border:0; background:#fff; padding:9px 6px; font-weight:700; color:#475569; display:inline-flex; align-items:center; justify-content:center; gap:6px; }
    .hp-page .hp-switch button.active { background:#1e3a8a; color:#fff; }
    .hp-page .hp-grid { grid-template-columns:1fr; }
    .hp-page .hp-grid > .hp-card:not(.is-shown) { display:none; }
    .hp-page .hp-card { height:calc(100vh - 240px); min-height:360px; }
    .hp-page .ms-msg { max-width:90%; }
  }
</style>

<div class="content-wrapper">
  <section class="content-header">
    <h1><?php echo $info['page_heading']; ?></h1>
    <p class="hp-intro">Look for an answer in the help topics, or chat with the support team.</p>
  </section>
  <section class="content hp-page">

    <div class="hp-switch" role="tablist">
      <button type="button" data-show="hpTopics" class="active" role="tab"><i class="fa fa-book"></i> Help Topics</button>
      <button type="button" data-show="hpChat" role="tab"><i class="fa fa-comments"></i> Chat <span class="my-sup-unread" style="display:none;"></span></button>
    </div>

    <div class="hp-grid">
      <!-- Help topics -->
      <div class="hp-card is-shown" id="hpTopics">
        <div class="hp-card-head">
          <h4><i class="fa fa-book"></i> Help Topics</h4>
          <?php if ($can_manage_topics) { ?>
            <a href="<?php echo base_url('manager/hr/search_topics?manage=1'); ?>" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i> Manage topics</a>
          <?php } ?>
        </div>
        <div class="hp-search">
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-search"></i></span>
            <input type="search" id="hpQ" class="form-control" placeholder="Search: leave, shift timings, insurance, how to..." aria-label="Search help topics">
          </div>
        </div>
        <div class="hp-chips" id="hpChips">
          <button type="button" class="hp-chip active" data-cat="">All</button>
          <?php foreach (array_keys($hp_cats) as $c) { ?>
            <button type="button" class="hp-chip" data-cat="<?php echo htmlspecialchars($c); ?>"><?php echo htmlspecialchars($c); ?></button>
          <?php } ?>
        </div>
        <div class="hp-topics">
          <?php if (!empty($topics)) { ?>
          <div class="tp-list" id="hpList">
            <?php foreach ($topics as $tp) { ?>
            <details class="tp-item" data-cat="<?php echo htmlspecialchars($tp['category']); ?>" data-text="<?php echo htmlspecialchars(strtolower($tp['title'] . ' ' . $tp['category'] . ' ' . $tp['summary'] . ' ' . $tp['body'])); ?>">
              <summary>
                <span class="pf-item-type"><?php echo htmlspecialchars($tp['category']); ?></span>
                <span class="tp-title"><?php echo htmlspecialchars($tp['title']); ?></span>
                <?php if (!empty($tp['summary'])) { ?><span class="tp-summary"><?php echo htmlspecialchars($tp['summary']); ?></span><?php } ?>
              </summary>
              <div class="tp-body">
                <?php echo !empty($tp['body']) ? nl2br(htmlspecialchars($tp['body'])) : '<span class="text-muted">No further details yet. Ask support in the chat.</span>'; ?>
              </div>
            </details>
            <?php } ?>
          </div>
          <?php } ?>
          <div class="hp-none<?php echo !empty($topics) ? ' hp-hidden' : ''; ?>" id="hpNone">
            <i class="fa fa-question-circle-o"></i>
            <?php echo !empty($topics) ? 'No topic matched your search.' : 'No help topics have been published yet.'; ?>
            <div style="margin-top:10px;"><button type="button" class="btn btn-sm btn-primary" id="hpAsk"><i class="fa fa-comments"></i> Ask support</button></div>
          </div>
        </div>
      </div>

      <!-- Chat with support -->
      <div class="hp-card hp-chat" id="hpChat">
        <div class="hp-card-head"><h4><i class="fa fa-comments"></i> Chat with Support</h4><small><span class="ms-live" id="msLive"></span> <span id="msRef"></span></small></div>
        <div class="ms-msgs" id="msMsgs" aria-live="polite"><div class="ms-empty" id="msEmpty">Type your message below to chat with support.</div></div>
        <form class="ms-compose" id="msCompose">
          <textarea class="form-control" id="msText" maxlength="2000" placeholder="Type your message" aria-label="Message"></textarea>
          <button type="submit" class="btn btn-primary" aria-label="Send message"><i class="fa fa-paper-plane"></i></button>
        </form>
        <form class="ms-off" id="msOffline" style="display:none;" novalidate>
          <div class="ms-off-note"><i class="fa fa-clock-o"></i><span>Live chat is offline right now. Kindly send us a message and we will get back to you at the earliest.</span></div>
          <div id="msOffFields">
            <div class="form-group" id="msOffEmailG"><label for="msOffEmail">Your email</label><input type="email" class="form-control input-sm" id="msOffEmail" maxlength="150"><div class="ms-err" id="msOffEmailE"></div></div>
            <div class="form-group" id="msOffBodyG"><label for="msOffBody">Message</label><textarea class="form-control" id="msOffBody" maxlength="3000" placeholder="Tell us what you need help with"></textarea><div class="ms-err" id="msOffBodyE"></div></div>
            <button type="submit" class="btn btn-primary btn-sm" id="msOffSend"><i class="fa fa-envelope"></i> Send message</button>
          </div>
          <div class="ms-off-done" id="msOffDone" style="display:none;"></div>
        </form>
      </div>
    </div>
  </section>
</div>

<script>
(function () {
  function el(id) { return document.getElementById(id); }

  /* ---- Phones / tablets: switch between topics and chat ---- */
  function show(id) {
    $('.hp-switch button').removeClass('active').filter('[data-show="' + id + '"]').addClass('active');
    $('.hp-grid > .hp-card').removeClass('is-shown');
    $('#' + id).addClass('is-shown');
    if (id === 'hpChat') { el('msMsgs').scrollTop = el('msMsgs').scrollHeight; }
    if (history.replaceState) history.replaceState(null, '', id === 'hpChat' ? '#chat' : '#topics');
  }
  $('.hp-switch').on('click', 'button', function () { show($(this).data('show')); });
  $('#hpAsk').on('click', function () { show('hpChat'); el('msText').focus(); });
  if (location.hash === '#chat') show('hpChat');

  /* ---- Topic search: words and category, on this page ---- */
  var cat = '';
  function filter() {
    var words = el('hpQ').value.toLowerCase().split(/\s+/).filter(Boolean), shown = 0;
    $('#hpList .tp-item').each(function () {
      var t = $(this).data('text') + '', ok = (!cat || $(this).data('cat') === cat);
      for (var i = 0; ok && i < words.length; i++) { if (t.indexOf(words[i]) === -1) ok = false; }
      $(this).toggleClass('hp-hidden', !ok);
      if (ok) shown++;
    });
    if ($('#hpList').length) $('#hpNone').toggleClass('hp-hidden', shown > 0);
  }
  $('#hpQ').on('input', filter);
  $('#hpChips').on('click', '.hp-chip', function () {
    $('#hpChips .hp-chip').removeClass('active'); $(this).addClass('active');
    cat = $(this).data('cat') + ''; filter();
  });

  /* ---- Chat with support (same chat as before) ---- */
  var BASE = '<?php echo base_url(); ?>manager/my-support/';
  var CSRF_NAME = '<?php echo $this->security->get_csrf_token_name(); ?>';
  var CSRF_FALLBACK = '<?php echo $this->security->get_csrf_hash(); ?>';
  var lastId = 0, timer = null;
  function csrf() { var m = document.cookie.match(/(?:^|;\s*)csrf_cookie_name=([^;]+)/); return m ? decodeURIComponent(m[1]) : CSRF_FALLBACK; }

  function add(m) {
    if (el('msEmpty')) el('msEmpty').remove();
    var w = document.createElement('div');
    w.className = 'ms-msg ' + (m.sender === 'visitor' ? 'ms-me' : m.sender === 'agent' ? 'ms-them' : 'ms-system');
    if (m.sender !== 'system') {
      var meta = document.createElement('div');
      meta.className = 'ms-meta';
      meta.textContent = (m.sender === 'visitor' ? 'You' : m.name) + ' · ' + m.time;
      w.appendChild(meta);
    }
    var b = document.createElement('div');
    b.className = 'ms-bubble'; b.textContent = m.body;
    w.appendChild(b);
    el('msMsgs').appendChild(w);
    el('msMsgs').scrollTop = el('msMsgs').scrollHeight;
  }

  function poll() {
    if (timer) clearTimeout(timer);
    fetch(BASE + 'poll?after=' + lastId, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (r) { return r.json(); })
      .then(function (r) {
        if (!r.ok) return;
        r.messages.forEach(function (m) { if (m.id <= lastId) return; lastId = m.id; add(m); });
        el('msRef').textContent = r.ref_no ? '· ' + r.ref_no + (r.status === 'closed' ? ' · closed (send a message to reopen)' : '') : '';
        if (window.crmMySupBadge) window.crmMySupBadge(0);
        timer = setTimeout(poll, 3000);
      })
      .catch(function () { timer = setTimeout(poll, 5000); });
  }

  el('msCompose').addEventListener('submit', function (e) {
    e.preventDefault();
    var t = el('msText').value.trim();
    if (!t) return;
    el('msText').value = '';
    var b = new URLSearchParams({ body: t }); b.append(CSRF_NAME, csrf());
    fetch(BASE + 'send', { method: 'POST', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: b })
      .then(function (r) { return r.json(); })
      .then(function (r) { if (!r.ok) { el('msText').value = t; alert(r.error || 'Message not sent.'); return; } poll(); })
      .catch(function () { el('msText').value = t; alert('Could not reach the server.'); });
  });
  el('msText').addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); el('msCompose').requestSubmit(); }
  });

  /* ---- Is anyone on live chat? If not, the user sends a message by email instead ---- */
  var offline = null, emailSet = false;
  function setMode(isOffline) {
    offline = isOffline;
    el('msCompose').style.display = isOffline ? 'none' : '';
    el('msOffline').style.display = isOffline ? '' : 'none';
    el('msLive').className = 'ms-live' + (isOffline ? '' : ' is-on');
    el('msLive').textContent = isOffline ? 'Offline' : 'Online';
    if (el('msEmpty')) el('msEmpty').textContent = isOffline ? 'Live chat is offline. Send us a message below.' : 'Type your message below to chat with support.';
  }
  function status() {
    fetch(BASE + 'status', { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (r) { return r.json(); })
      .then(function (r) {
        if (!r.ok) return;
        if (!emailSet && r.email) { el('msOffEmail').value = r.email; emailSet = true; }
        if (offline !== !r.online) setMode(!r.online);
      })
      .catch(function () {})
      .then(function () { setTimeout(status, 20000); });
  }
  function fieldErr(name, msg) {
    var g = el('msOff' + name + 'G'), e = el('msOff' + name + 'E');
    g.classList.toggle('has-error', !!msg); e.textContent = msg || '';
  }
  el('msOffline').addEventListener('submit', function (e) {
    e.preventDefault();
    var email = el('msOffEmail').value.trim(), body = el('msOffBody').value.trim(), bad = false;
    fieldErr('Email', ''); fieldErr('Body', '');
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { fieldErr('Email', 'Please enter a valid email address.'); bad = true; }
    if (body.length < 10) { fieldErr('Body', 'Please write your message (at least 10 characters).'); bad = true; }
    if (bad) return;
    var btn = el('msOffSend'); btn.disabled = true;
    var b = new URLSearchParams({ email: email, body: body }); b.append(CSRF_NAME, csrf());
    fetch(BASE + 'message', { method: 'POST', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: b })
      .then(function (r) { return r.json(); })
      .then(function (r) {
        btn.disabled = false;
        if (r.ok) {
          el('msOffBody').value = '';
          el('msOffFields').style.display = 'none';
          var d = el('msOffDone');
          d.textContent = 'Thank you. Your message has been sent (ref. ' + r.ref_no + '). We will get back to you at ' + email + ' at the earliest.';
          d.style.display = '';
          setTimeout(function () { d.style.display = 'none'; el('msOffFields').style.display = ''; }, 15000);
          poll();
          return;
        }
        if (r.online) { setMode(false); el('msText').value = body; el('msOffBody').value = ''; el('msText').focus(); alert(r.error); return; }
        if (r.errors) { fieldErr('Email', r.errors.email); fieldErr('Body', r.errors.body); return; }
        alert(r.error || 'Message not sent.');
      })
      .catch(function () { btn.disabled = false; alert('Could not reach the server.'); });
  });

  window.crmIdleExempt = true; // keep the session while the chat is open
  status();
  poll();
})();
</script>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
