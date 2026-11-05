<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo htmlspecialchars($info['title']); ?></title>
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/logo.png" type="image/png">
  <link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/logo.png">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-auth.css?v=20261105_17">
</head>
<body class="auth-page">
  <div class="auth-shell">
<?php require(APPPATH . 'views/manager/elements/auth_visual.php'); ?>
    <main class="auth-panel">
      <div class="sp-top">
        <a href="<?php echo base_url(); ?>manager/login" class="auth-link sp-back">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
          Back to Sign In
        </a>
        <span class="sp-status <?php echo $online ? 'is-online' : 'is-offline'; ?>" id="spStatus"><i></i><span><?php echo $online ? 'Support online' : 'Support offline'; ?></span></span>
      </div>

      <div class="auth-panel-inner sp-inner">
        <div class="auth-brand"><img src="<?php echo base_url(); ?>assets/logo.png" alt="Seven Strokes"></div>
        <h1 class="auth-title">Support</h1>
        <p class="auth-sub" id="spIntro">
          <?php if ($online) { ?>
            Type your message below to chat with our support team.
          <?php } else { ?>
            Our support team is offline. Leave your details and the IT team will get back to you.
          <?php } ?>
        </p>

        <!-- Step 1: request form -->
        <form id="spForm" novalidate>
          <input type="text" name="website" value="" tabindex="-1" autocomplete="off" class="sp-hp" aria-hidden="true">

          <div class="auth-field">
            <label class="auth-label" for="sp_name">Name</label>
            <div class="auth-input-wrap">
              <svg class="auth-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
              <input type="text" class="auth-input has-icon" id="sp_name" name="name" maxlength="120" placeholder="Your full name" autocomplete="name" required>
            </div>
            <div class="sp-err" data-for="name"></div>
          </div>

          <div class="auth-field">
            <label class="auth-label" for="sp_phone">Phone Number</label>
            <div class="auth-input-wrap">
              <svg class="auth-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
              <input type="tel" class="auth-input has-icon" id="sp_phone" name="phone" maxlength="40" placeholder="+971 50 123 4567" autocomplete="tel" required>
            </div>
            <div class="sp-err" data-for="phone"></div>
          </div>

          <div class="auth-field">
            <label class="auth-label" for="sp_email">Email Address <span class="sp-opt">(optional)</span></label>
            <div class="auth-input-wrap">
              <svg class="auth-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="m22 7-10 6L2 7"></path></svg>
              <input type="email" class="auth-input has-icon" id="sp_email" name="email" maxlength="190" placeholder="name@company.com" autocomplete="email">
            </div>
            <div class="sp-err" data-for="email"></div>
          </div>

          <div class="auth-field">
            <label class="auth-label" for="sp_issue">Describe the Issue</label>
            <textarea class="auth-input sp-textarea" id="sp_issue" name="issue" maxlength="3000" rows="4" placeholder="For example: I cannot sign in, I get 'wrong username or password'." required></textarea>
            <div class="sp-err" data-for="issue"></div>
          </div>

          <div class="auth-alert auth-alert-danger" id="spFormError" style="display:none;"></div>
          <button type="submit" class="auth-btn" id="spSubmit"><?php echo $online ? 'Start Live Chat' : 'Send to IT Team'; ?></button>
        </form>

        <!-- Step 2: chat -->
        <section class="sp-chat" id="spChat" hidden>
          <div class="sp-chat-head">
            <div>
              <strong>Live Support</strong>
              <span class="sp-ref" id="spRef"></span>
            </div>
            <button type="button" class="sp-end" id="spEnd">End chat</button>
          </div>
          <div class="sp-msgs" id="spMsgs" aria-live="polite"></div>
          <div class="sp-notice" id="spNotice" hidden></div>
          <form class="sp-compose" id="spCompose">
            <input type="text" class="auth-input" id="spText" maxlength="2000" placeholder="Type your message" autocomplete="off">
            <button type="submit" class="auth-btn sp-send" aria-label="Send message">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m22 2-7 20-4-9-9-4z"></path><path d="M22 2 11 13"></path></svg>
            </button>
          </form>
          <button type="button" class="auth-btn sp-new" id="spNew" hidden>Start a New Request</button>
        </section>

        <p class="auth-alt">You can also email <a class="auth-link" href="mailto:<?php echo htmlspecialchars($it_email); ?>"><?php echo htmlspecialchars($it_email); ?></a></p>
      </div>
      <div class="auth-foot">&copy; <?php echo date('Y'); ?> Seven Strokes</div>
    </main>
  </div>

<script>
(function () {
  var BASE = '<?php echo base_url(); ?>support/';
  var CSRF_NAME = '<?php echo $this->security->get_csrf_token_name(); ?>';
  var CSRF_FALLBACK = '<?php echo $this->security->get_csrf_hash(); ?>';
  var IT_EMAIL = <?php echo json_encode($it_email); ?>;
  var KEY = 'crm_support_chat';
  var token = null, lastId = 0, pollTimer = null, status = '', online = <?php echo $online ? 'true' : 'false'; ?>;

  function $(id) { return document.getElementById(id); }
  function csrf() {
    var m = document.cookie.match(/(?:^|;\s*)csrf_cookie_name=([^;]+)/);
    return m ? decodeURIComponent(m[1]) : CSRF_FALLBACK;
  }
  function store(v) { try { v ? localStorage.setItem(KEY, v) : localStorage.removeItem(KEY); } catch (e) {} }
  function stored() { try { return localStorage.getItem(KEY); } catch (e) { return null; } }
  function prevChat() { try { return localStorage.getItem(KEY + '_prev') || ''; } catch (e) { return ''; } }
  function rememberPrev(t) { try { localStorage.setItem(KEY + '_prev', t); } catch (e) {} }

  function post(path, data) {
    var body = new URLSearchParams(data);
    body.append(CSRF_NAME, csrf());
    return fetch(BASE + path, { method: 'POST', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: body })
      .then(function (r) { return r.json().catch(function () { return { ok: false, error: 'Unexpected server response.' }; }); });
  }
  function get(path) {
    return fetch(BASE + path, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (r) { return r.json(); });
  }

  function setOnline(on) {
    var changed = on !== online;
    online = on;
    if (changed && !token) showReady();
    var s = $('spStatus');
    s.className = 'sp-status ' + (on ? 'is-online' : 'is-offline');
    s.querySelector('span').textContent = on ? 'Support online' : 'Support offline';
    if (!token) {
      $('spSubmit').textContent = on ? 'Start Live Chat' : 'Send to IT Team';
      $('spIntro').textContent = on
        ? 'Type your message below to chat with our support team.'
        : 'Our support team is offline. Leave your details and the IT team will get back to you.';
    }
  }

  /* ---------- step 1 ---------- */
  $('spForm').addEventListener('submit', function (e) {
    e.preventDefault();
    var form = e.target, f = form.elements, btn = $('spSubmit');
    [].forEach.call(document.querySelectorAll('.sp-err'), function (el) { el.textContent = ''; });
    $('spFormError').style.display = 'none';
    btn.disabled = true; btn.textContent = 'Sending...';
    post('start', {
      name: f['name'].value, phone: f['phone'].value, email: f['email'].value, issue: f['issue'].value, website: f['website'].value, prev: prevChat()
    }).then(function (res) {
      btn.disabled = false; setOnline(online);
      if (!res.ok) {
        if (res.errors) {
          Object.keys(res.errors).forEach(function (k) {
            var el = document.querySelector('.sp-err[data-for="' + k + '"]');
            if (el) el.textContent = res.errors[k];
          });
        } else {
          $('spFormError').textContent = res.error || 'Something went wrong. Please try again.';
          $('spFormError').style.display = 'block';
        }
        return;
      }
      openChat(res.token);
      form.reset();
    }).catch(function () {
      btn.disabled = false; setOnline(online);
      $('spFormError').textContent = 'Could not reach the server. Please check your connection or email ' + IT_EMAIL + '.';
      $('spFormError').style.display = 'block';
    });
  });

  /* ---------- step 2 ---------- */
  function openChat(t) {
    token = t; lastId = 0; status = '';
    store(t); rememberPrev(t);
    $('spForm').hidden = true;
    $('spChat').hidden = false;
    $('spMsgs').innerHTML = '';
    $('spIntro').textContent = 'Keep this page open to continue the conversation.';
    poll();
  }

  function addMsg(m) {
    var wrap = document.createElement('div');
    wrap.className = 'sp-msg sp-' + m.sender;
    if (m.sender !== 'system') {
      var meta = document.createElement('div');
      meta.className = 'sp-meta';
      meta.textContent = m.name + ' · ' + m.time;
      wrap.appendChild(meta);
    }
    var b = document.createElement('div');
    b.className = 'sp-bubble';
    b.textContent = m.body;
    wrap.appendChild(b);
    $('spMsgs').appendChild(wrap);
    $('spMsgs').scrollTop = $('spMsgs').scrollHeight;
  }

  function applyStatus(st, ref) {
    status = st;
    $('spRef').textContent = ref ? ref : '';
    var ended = (st === 'emailed' || st === 'closed');
    $('spCompose').hidden = ended;
    $('spEnd').hidden = ended;
    $('spNew').hidden = !ended;
    var n = $('spNotice');
    if (st === 'emailed') {
      n.hidden = false;
      n.textContent = 'Support is not available right now. Your request ' + (ref || '') + ' has been sent to our IT team (' + IT_EMAIL + '). They will get back to you on the phone number you gave.';
    } else if (st === 'closed') {
      n.hidden = false;
      n.textContent = 'This chat has ended. Thank you for contacting Seven Strokes.';
    } else {
      n.hidden = true;
    }
    if (ended) {
      store(null); stopPoll();
      $('spIntro').textContent = st === 'emailed' ? 'We have received your request.' : 'Thank you for contacting us.';
    }
  }

  function poll() {
    if (!token) return;
    get('poll?token=' + encodeURIComponent(token) + '&after=' + lastId).then(function (res) {
      if (!res.ok) { resetToForm(); return; }
      res.messages.forEach(function (m) { lastId = Math.max(lastId, m.id); addMsg(m); });
      applyStatus(res.status, res.ref_no);
      if (res.status === 'waiting' || res.status === 'active') schedule();
    }).catch(function () { schedule(); });
  }
  function schedule() { stopPoll(); pollTimer = setTimeout(poll, 3000); }
  function stopPoll() { if (pollTimer) clearTimeout(pollTimer); pollTimer = null; }

  $('spCompose').addEventListener('submit', function (e) {
    e.preventDefault();
    var input = $('spText'), text = input.value.trim();
    if (!text) return;
    input.value = '';
    if (!token) {
      post('start', { chat: '1', issue: text, prev: prevChat() }).then(function (res) {
        if (!res.ok) { input.value = text; if (res.errors) { online = false; showReady(); } else alert(res.error || 'Message not sent.'); return; }
        openChat(res.token);
      }).catch(function () { input.value = text; alert('Could not reach the server.'); });
      return;
    }
    post('send', { token: token, body: text }).then(function (res) {
      if (!res.ok) { input.value = text; alert(res.error || 'Message not sent.'); return; }
      stopPoll(); poll();
    });
  });

  $('spEnd').addEventListener('click', function () {
    if (!token || !confirm('End this chat?')) return;
    post('end', { token: token }).then(function () { stopPoll(); poll(); });
  });

  function resetToForm() {
    stopPoll(); token = null; store(null);
    showReady();
  }

  // Online: the chat opens directly. Offline: the details form (sent to IT by email).
  function showReady() {
    var on = online && !token;
    $('spForm').hidden = on;
    $('spChat').hidden = !on;
    if (on) {
      $('spMsgs').innerHTML = '';
      $('spRef').textContent = '';
      $('spNotice').hidden = true;
      $('spCompose').hidden = false;
      $('spEnd').hidden = true;
      $('spNew').hidden = true;
      $('spText').focus();
    }
    setOnline(online);
  }
  $('spNew').addEventListener('click', resetToForm);

  // Refresh the online/offline badge while the form is shown.
  setInterval(function () {
    if (token) return;
    get('status').then(function (r) { setOnline(!!r.online); }).catch(function () {});
  }, 20000);

  // Resume an open chat after a page refresh.
  var saved = stored();
  if (saved && /^[a-f0-9]{48}$/.test(saved)) openChat(saved); else showReady();
})();
</script>
</body>
</html>
