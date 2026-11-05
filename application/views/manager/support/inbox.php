<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<style>
  .ls-wrap { display: grid; grid-template-columns: 340px minmax(0, 1fr); gap: 16px; }
  .ls-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; }
  .ls-avail { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
  .ls-switch { position: relative; display: inline-flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 600; margin: 0; }
  .ls-switch input { position: absolute; opacity: 0; pointer-events: none; }
  .ls-track { width: 42px; height: 24px; border-radius: 999px; background: #cbd5e1; position: relative; transition: background .15s; }
  .ls-track::after { content: ""; position: absolute; top: 3px; left: 3px; width: 18px; height: 18px; border-radius: 50%; background: #fff; transition: transform .15s; box-shadow: 0 1px 2px rgba(0,0,0,.2); }
  .ls-switch input:checked + .ls-track { background: #16a34a; }
  .ls-switch input:checked + .ls-track::after { transform: translateX(18px); }
  .ls-switch input:focus-visible + .ls-track { outline: 3px solid rgba(30,58,138,.3); outline-offset: 2px; }
  .ls-switch #lsAvailText { color: #475569; }
  .ls-switch input:checked ~ #lsAvailText { color: #15803d; }
  .ls-hint { color: #64748b; font-size: 13px; }
  .ls-list-head { padding: 12px 14px; border-bottom: 1px solid #e2e8f0; font-weight: 700; display: flex; justify-content: space-between; align-items: center; }
  .ls-list { max-height: 600px; overflow-y: auto; }
  .ls-item { display: block; width: 100%; text-align: left; border: 0; border-bottom: 1px solid #f1f5f9; background: #fff; padding: 12px 14px; cursor: pointer; font: inherit; }
  .ls-item:hover { background: #f8fafc; }
  .ls-item.is-active { background: #eff6ff; box-shadow: inset 3px 0 0 #1e3a8a; }
  .ls-item-top { display: flex; justify-content: space-between; gap: 8px; align-items: center; }
  .ls-name { font-weight: 700; color: #0f172a; font-size: 14px; }
  .ls-time { color: #94a3b8; font-size: 12px; white-space: nowrap; }
  .ls-last { color: #64748b; font-size: 13px; margin-top: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .ls-badge { display: inline-block; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 999px; text-transform: uppercase; letter-spacing: .04em; }
  .ls-b-waiting { background: #fef3c7; color: #92400e; }
  .ls-b-active { background: #dcfce7; color: #166534; }
  .ls-b-emailed { background: #e0e7ff; color: #3730a3; }
  .ls-b-closed { background: #f1f5f9; color: #475569; }
  .ls-empty { padding: 30px 16px; text-align: center; color: #94a3b8; font-size: 14px; }
  .ls-chat { display: flex; flex-direction: column; min-height: 560px; }
  .ls-chat-head { padding: 12px 16px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; align-items: flex-start; }
  .ls-contact { font-size: 13px; color: #475569; margin-top: 4px; }
  .ls-contact a { color: #1e3a8a; }
  .ls-msgs { flex: 1; overflow-y: auto; padding: 16px; background: #f8fafc; display: flex; flex-direction: column; gap: 10px; max-height: 470px; min-height: 300px; }
  .ls-msg { max-width: 75%; display: flex; flex-direction: column; }
  .ls-meta { font-size: 11.5px; color: #64748b; margin: 0 4px 3px; }
  .ls-bubble { padding: 9px 12px; border-radius: 12px; font-size: 14px; line-height: 1.5; white-space: pre-wrap; word-wrap: break-word; }
  .ls-visitor { align-self: flex-start; }
  .ls-visitor .ls-bubble { background: #fff; border: 1px solid #e2e8f0; color: #0f172a; border-bottom-left-radius: 4px; }
  .ls-agent { align-self: flex-end; align-items: flex-end; }
  .ls-agent .ls-bubble { background: #1e3a8a; color: #fff; border-bottom-right-radius: 4px; }
  .ls-system { align-self: center; max-width: 90%; }
  .ls-system .ls-bubble { color: #64748b; font-size: 12.5px; text-align: center; padding: 2px 6px; }
  .ls-compose { display: flex; gap: 8px; padding: 12px; border-top: 1px solid #e2e8f0; }
  .ls-compose textarea { resize: none; height: 44px; }
  .ls-placeholder { flex: 1; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 14px; padding: 40px; text-align: center; }
  @media (max-width: 991px) { .ls-wrap { grid-template-columns: 1fr; } .ls-list { max-height: 320px; } }
</style>

<?php $cb_tab = 'inbox'; $can_status = !empty($rights['status']); $wait_min = (int)$settings['no_reply_minutes']; ?>
<div class="content-wrapper">
  <section class="content-header crm-list-head">
    <h1>Chatbot</h1>
    <div class="ls-avail">
      <?php if ($can_status) { ?>
      <label class="ls-switch" for="lsAvailable">
        <input type="checkbox" id="lsAvailable" <?php echo $available ? 'checked' : ''; ?>>
        <span class="ls-track" aria-hidden="true"></span>
        <span id="lsAvailText"><?php echo $available ? 'Active' : 'Inactive'; ?></span>
      </label>
      <?php } ?>
      <span class="ls-hint" id="lsOnline"></span>
    </div>
  </section>

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>
    <?php require(APPPATH . "views/manager/support/_tabs.php"); ?>
    <p class="ls-hint" style="margin:0 0 12px;">
      Chats come from the Support button on the sign-in page.<?php if ($can_status) { ?> Switch to <strong>Active</strong> to receive new chats, and keep this page open.<?php } ?>
      If nobody is Active, or nobody replies within <?php echo $wait_min; ?> minute<?php echo $wait_min === 1 ? '' : 's'; ?>, the request is emailed to <strong><?php echo htmlspecialchars($it_email); ?></strong>.
    </p>
    <div class="ls-wrap">
      <div class="ls-card">
        <div class="ls-list-head"><span>Conversations</span><span class="ls-hint" id="lsCount"></span></div>
        <div class="ls-list" id="lsList"><div class="ls-empty">Loading...</div></div>
      </div>
      <div class="ls-card ls-chat" id="lsChat">
        <div class="ls-placeholder" id="lsPlaceholder">Select a conversation to view it and reply.</div>
      </div>
    </div>
  </section>
</div>

<script>
(function () {
  var BASE = '<?php echo base_url(); ?>manager/chatbot/';
  var CAN = <?php echo json_encode(array('status' => !empty($rights['status']), 'reply' => !empty($rights['reply']), 'close' => !empty($rights['close']), 'history' => !empty($rights['history']))); ?>;
  var OPEN_CHAT = <?php echo (int)$open_chat; ?>;
  var CSRF_NAME = '<?php echo $this->security->get_csrf_token_name(); ?>';
  var CSRF_FALLBACK = '<?php echo $this->security->get_csrf_hash(); ?>';
  var current = 0, lastId = 0, timer = null, knownWaiting = {}, firstLoad = true;

  function el(id) { return document.getElementById(id); }
  function esc(s) { var d = document.createElement('div'); d.textContent = s == null ? '' : String(s); return d.innerHTML; }
  function csrf() { var m = document.cookie.match(/(?:^|;\s*)csrf_cookie_name=([^;]+)/); return m ? decodeURIComponent(m[1]) : CSRF_FALLBACK; }
  function post(path, data) {
    var b = new URLSearchParams(data); b.append(CSRF_NAME, csrf());
    return fetch(BASE + path, { method: 'POST', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: b }).then(function (r) { return r.json(); });
  }

  function setAvailableUi(on) {
    window.crmIdleExempt = !!on; // stay signed in while on duty
    if (!el('lsAvailable')) return;
    el('lsAvailable').checked = !!on;
    el('lsAvailText').textContent = on ? 'Active' : 'Inactive';
  }
  setAvailableUi(el('lsAvailable') && el('lsAvailable').checked);
  function onlineText(n) { return n + (n === 1 ? ' person' : ' people') + ' active'; }

  if (el('lsAvailable')) el('lsAvailable').addEventListener('change', function () {
    var on = this.checked ? 1 : 0;
    if (on && 'Notification' in window && Notification.permission === 'default') { Notification.requestPermission(); }
    post('availability', { available: on }).then(function (r) {
      if (!r.ok) { alert(r.error || 'Could not change your status.'); setAvailableUi(!on); return; }
      setAvailableUi(r.available);
      el('lsOnline').textContent = onlineText(r.online_agents);
    });
  });

  function fmtTime(dt) { return dt ? dt.substr(11, 5) + (dt.substr(0, 10) !== new Date().toISOString().substr(0, 10) ? ' ' + dt.substr(8, 2) + '/' + dt.substr(5, 2) : '') : ''; }

  var lastListKey = '';
  function renderList(chats) {
    var key = current + '|' + JSON.stringify(chats);
    if (key === lastListKey) return; // nothing changed: keep the list as is
    lastListKey = key;
    var waiting = 0;
    if (!chats.length) { el('lsList').innerHTML = '<div class="ls-empty">No conversations in the last 7 days.</div>'; }
    else {
      el('lsList').innerHTML = chats.map(function (c) {
        if (c.status === 'waiting') waiting++;
        var last = (c.last_sender === 'agent' ? 'You: ' : '') + (c.last_message || '');
        return '<button type="button" class="ls-item' + (+c.id === current ? ' is-active' : '') + '" data-id="' + c.id + '">' +
          '<div class="ls-item-top"><span class="ls-name">' + esc(c.name) + '</span><span class="ls-time">' + esc(fmtTime(c.updated_at)) + '</span></div>' +
          '<div class="ls-item-top" style="margin-top:4px;"><span class="ls-hint">' + esc(c.ref_no) + ' &middot; ' + esc(c.phone) + '</span><span class="ls-badge ls-b-' + esc(c.status) + '">' + esc(c.status) + '</span></div>' +
          '<div class="ls-last">' + esc(last) + '</div></button>';
      }).join('');
    }
    el('lsCount').textContent = waiting ? waiting + ' waiting' : '';
    document.title = (waiting ? '(' + waiting + ') ' : '') + 'Chatbot Inbox';

    // Alert on new waiting chats.
    chats.forEach(function (c) {
      if (c.status === 'waiting' && !knownWaiting[c.id]) {
        knownWaiting[c.id] = true;
        if (!firstLoad) notify(c);
      }
    });
    firstLoad = false;
  }

  function notify(c) {
    try {
      var ctx = new (window.AudioContext || window.webkitAudioContext)();
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.frequency.value = 880; o.connect(g); g.connect(ctx.destination);
      g.gain.setValueAtTime(0.15, ctx.currentTime); g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.6);
      o.start(); o.stop(ctx.currentTime + 0.6);
    } catch (e) {}
    if ('Notification' in window && Notification.permission === 'granted') {
      new Notification('New support chat', { body: c.name + ': ' + (c.last_message || '') });
    }
  }

  el('lsList').addEventListener('click', function (e) {
    var b = e.target.closest('.ls-item'); if (!b) return;
    openChat(+b.getAttribute('data-id'));
  });

  function openChat(id) {
    current = id; lastId = 0;
    el('lsChat').innerHTML =
      '<div class="ls-chat-head"><div><strong id="lsTitle"></strong> <span class="ls-badge" id="lsBadge"></span><div class="ls-contact" id="lsContact"></div></div>' +
      '<div style="display:flex;gap:6px;flex-wrap:wrap;">' +
      (CAN.history ? '<a class="btn btn-sm btn-default" href="' + BASE + 'history/' + id + '"><i class="fa fa-file-text-o"></i> Transcript</a>' : '') +
      (CAN.close ? '<button type="button" class="btn btn-sm btn-default" id="lsClose"><i class="fa fa-check"></i> Close chat</button>' : '') + '</div></div>' +
      '<div class="ls-msgs" id="lsMsgs"></div>' +
      (CAN.reply
        ? '<form class="ls-compose" id="lsCompose"><textarea class="form-control" id="lsText" maxlength="2000" placeholder="Type a reply. Enter to send, Shift+Enter for a new line"></textarea>' +
          '<button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane"></i> Send</button></form>'
        : '<div class="ls-compose ls-hint">You can read chats but you do not have the right to reply.</div>');
    if (CAN.reply) {
      el('lsCompose').addEventListener('submit', sendReply);
      el('lsText').addEventListener('keydown', function (e) { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendReply(e); } });
    }
    if (CAN.close) el('lsClose').addEventListener('click', function () {
      if (!confirm('Close this chat? The visitor will see that the chat has ended.')) return;
      post('close', { chat_id: current }).then(function () { tick(); });
    });
    tick();
  }

  function sendReply(e) {
    e.preventDefault();
    var t = el('lsText'), text = t.value.trim(); if (!text) return;
    t.value = '';
    post('reply', { chat_id: current, body: text }).then(function (r) {
      if (!r.ok) { t.value = text; alert(r.error || 'Reply not sent.'); return; }
      tick();
    });
  }

  function renderChat(chat, msgs) {
    if (!el('lsMsgs')) return;
    el('lsTitle').textContent = chat.name + ' (' + chat.ref_no + ')';
    el('lsBadge').className = 'ls-badge ls-b-' + chat.status; el('lsBadge').textContent = chat.status;
    el('lsContact').innerHTML = '<i class="fa fa-phone"></i> <a href="tel:' + esc(chat.phone) + '">' + esc(chat.phone) + '</a>' +
      (chat.email ? ' &nbsp; <i class="fa fa-envelope-o"></i> <a href="mailto:' + esc(chat.email) + '">' + esc(chat.email) + '</a>' : '') +
      (chat.emailed_at ? ' &nbsp; <span class="ls-hint">Emailed to IT at ' + esc(chat.emailed_at.substr(11, 5)) + '</span>' : '');
    var closed = chat.status === 'closed';
    if (el('lsCompose')) el('lsCompose').style.display = closed ? 'none' : '';
    if (el('lsClose')) el('lsClose').style.display = closed ? 'none' : '';
    msgs.forEach(function (m) {
      lastId = Math.max(lastId, m.id);
      var w = document.createElement('div'); w.className = 'ls-msg ls-' + m.sender;
      if (m.sender !== 'system') { var meta = document.createElement('div'); meta.className = 'ls-meta'; meta.textContent = m.name + ' · ' + m.time; w.appendChild(meta); }
      var b = document.createElement('div'); b.className = 'ls-bubble'; b.textContent = m.body; w.appendChild(b);
      el('lsMsgs').appendChild(w);
    });
    if (msgs.length) el('lsMsgs').scrollTop = el('lsMsgs').scrollHeight;
  }

  function tick() {
    if (timer) clearTimeout(timer);
    var q = current ? '?chat=' + current + '&after=' + lastId : '';
    fetch(BASE + 'poll' + q, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (r) { return r.json(); })
      .then(function (r) {
        if (!r.ok) { if (r.error) el('lsOnline').textContent = r.error; return; }
        setAvailableUi(r.available);
        el('lsOnline').textContent = r.enabled ? onlineText(r.online_agents) : 'Chatbot is off';
        renderList(r.chats);
        if (r.chat) renderChat(r.chat, r.messages || []);
        timer = setTimeout(tick, 3000);
      })
      .catch(function () { timer = setTimeout(tick, 5000); });
  }
  if (OPEN_CHAT) openChat(OPEN_CHAT); else tick();
})();
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
