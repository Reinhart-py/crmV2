<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<style>
  .ms-card { background: var(--crm-surface, #fff); border: 1px solid var(--crm-line, #e2e8f0); border-radius: 10px; overflow: hidden; display: flex; flex-direction: column; height: calc(100vh - 150px); min-height: 380px; }
  .ms-head { padding: 12px 16px; background: #1e3a8a; color: #fff; display: flex; justify-content: space-between; align-items: center; gap: 10px; }
  .ms-head small { opacity: .8; font-size: 12px; }
  .ms-msgs { flex: 1; min-height: 0; overflow-y: auto; padding: 16px; background: #f8fafc; display: flex; flex-direction: column; gap: 10px; }
  .ms-msg { max-width: 75%; display: flex; flex-direction: column; }
  .ms-meta { font-size: 11.5px; color: #64748b; margin: 0 4px 3px; }
  .ms-bubble { padding: 9px 12px; border-radius: 12px; font-size: 14px; line-height: 1.5; white-space: pre-wrap; word-wrap: break-word; }
  .ms-me { align-self: flex-end; align-items: flex-end; }
  .ms-me .ms-bubble { background: #1e3a8a; color: #fff; border-bottom-right-radius: 4px; }
  .ms-them { align-self: flex-start; }
  .ms-them .ms-bubble { background: #fff; border: 1px solid #e2e8f0; color: #0f172a; border-bottom-left-radius: 4px; }
  .ms-system { align-self: center; max-width: 90%; }
  .ms-system .ms-bubble { color: #64748b; font-size: 12.5px; text-align: center; padding: 2px 6px; }
  .ms-empty { margin: auto; color: #94a3b8; font-size: 14px; text-align: center; }
  .ms-compose { display: flex; gap: 8px; padding: 12px; border-top: 1px solid var(--crm-line, #e2e8f0); }
  .ms-compose textarea { resize: none; height: 44px; }
  @media (max-width: 767px) { .ms-card { height: calc(100vh - 130px); } .ms-msg { max-width: 90%; } }
</style>

<div class="content-wrapper">
  <section class="content">
    <div class="ms-card">
      <div class="ms-head"><strong>Support</strong><small id="msRef"></small></div>
      <div class="ms-msgs" id="msMsgs" aria-live="polite"><div class="ms-empty" id="msEmpty">Type your message below to chat with support.</div></div>
      <form class="ms-compose" id="msCompose">
        <textarea class="form-control" id="msText" maxlength="2000" placeholder="Type your message" aria-label="Message"></textarea>
        <button type="submit" class="btn btn-primary" aria-label="Send message"><i class="fa fa-paper-plane"></i></button>
      </form>
    </div>
  </section>
</div>

<script>
(function () {
  var BASE = '<?php echo base_url(); ?>manager/my-support/';
  var CSRF_NAME = '<?php echo $this->security->get_csrf_token_name(); ?>';
  var CSRF_FALLBACK = '<?php echo $this->security->get_csrf_hash(); ?>';
  var lastId = 0, timer = null;
  function el(id) { return document.getElementById(id); }
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
        r.messages.forEach(function (m) { lastId = Math.max(lastId, m.id); add(m); });
        el('msRef').textContent = r.ref_no ? r.ref_no + (r.status === 'closed' ? ' · closed (send a message to reopen)' : '') : '';
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

  window.crmIdleExempt = true; // keep the session while the chat is open
  poll();
})();
</script>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
