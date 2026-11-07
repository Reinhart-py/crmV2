/* Header search: opens with the search button, "/" or Ctrl+K; arrows move, Enter opens, Esc closes. */
(function () {
  function init() {
    var wrap = document.getElementById('gsWrap');
    if (!wrap) return;
    var btn = document.getElementById('gsToggle'), panel = document.getElementById('gsPanel'),
        input = document.getElementById('gsInput'), box = document.getElementById('gsResults');
    var base = (document.querySelector('.main-logo a') || {}).href || '/';
    base = base.replace(/manager\/dashboard\/?$/, '');
    var hint = box.innerHTML, timer = null, seq = 0, cur = -1;

    function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
    function mark(s, q) {
      var t = esc(s); if (!q) return t;
      var i = String(s).toLowerCase().indexOf(q.toLowerCase());
      if (i < 0) return t;
      return esc(String(s).slice(0, i)) + '<mark>' + esc(String(s).slice(i, i + q.length)) + '</mark>' + esc(String(s).slice(i + q.length));
    }
    function open() {
      panel.hidden = false; btn.setAttribute('aria-expanded', 'true');
      input.focus(); input.select();
    }
    function close() { panel.hidden = true; btn.setAttribute('aria-expanded', 'false'); }
    function items() { return box.querySelectorAll('.gs-item'); }
    function setCur(i) {
      var all = items(); if (!all.length) { cur = -1; return; }
      cur = (i + all.length) % all.length;
      for (var k = 0; k < all.length; k++) all[k].classList.toggle('is-on', k === cur);
      all[cur].scrollIntoView({ block: 'nearest' });
    }
    function render(data, q) {
      var g = (data && data.groups) || [], h = '';
      if (!g.length) { box.innerHTML = '<div class="gs-hint">Nothing found for “' + esc(q) + '”.</div>'; cur = -1; return; }
      g.forEach(function (grp) {
        h += '<div class="gs-group"><i class="fa ' + esc(grp.icon) + '"></i>' + esc(grp.label) + '</div>';
        grp.items.forEach(function (it) {
          h += '<a class="gs-item" role="option" href="' + esc(it.url) + '"><div class="gs-main"><div class="gs-title">' + mark(it.title, q) + '</div>' +
            (it.sub ? '<div class="gs-sub">' + mark(it.sub, q) + '</div>' : '') + '</div>' +
            '<div class="gs-side">' + (it.tag ? '<span class="gs-tag">' + esc(it.tag) + '</span>' : '') + (it.meta ? '<div class="gs-meta">' + esc(it.meta) + '</div>' : '') + '</div></a>';
        });
        if (grp.more) h += '<div class="gs-more">More match; type more to narrow it down.</div>';
      });
      box.innerHTML = h; setCur(0);
    }
    function run() {
      var q = input.value.trim();
      if (q.length < 2) { box.innerHTML = hint; cur = -1; return; }
      var my = ++seq;
      box.innerHTML = '<div class="gs-hint">Searching…</div>';
      var x = new XMLHttpRequest();
      x.open('GET', base + 'manager/search?q=' + encodeURIComponent(q));
      x.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
      x.onload = function () {
        if (my !== seq) return;
        var d = null; try { d = JSON.parse(x.responseText); } catch (e) {}
        if (x.status === 401 || !d) { box.innerHTML = '<div class="gs-hint">' + (x.status === 401 ? 'Your session ended. Please sign in again.' : 'Search is not available right now.') + '</div>'; return; }
        render(d, q);
      };
      x.onerror = function () { if (my === seq) box.innerHTML = '<div class="gs-hint">Search is not available right now.</div>'; };
      x.send();
    }

    btn.addEventListener('click', function (e) { e.stopPropagation(); panel.hidden ? open() : close(); });
    panel.addEventListener('click', function (e) { e.stopPropagation(); });
    document.addEventListener('click', function () { if (!panel.hidden) close(); });
    input.addEventListener('input', function () { clearTimeout(timer); timer = setTimeout(run, 250); });
    input.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowDown') { e.preventDefault(); setCur(cur + 1); }
      else if (e.key === 'ArrowUp') { e.preventDefault(); setCur(cur - 1); }
      else if (e.key === 'Enter') { var all = items(); if (cur >= 0 && all[cur]) { e.preventDefault(); window.location.href = all[cur].href; } }
      else if (e.key === 'Escape') { close(); btn.focus(); }
    });
    document.addEventListener('keydown', function (e) {
      var t = e.target, typing = t && (t.isContentEditable || /^(INPUT|TEXTAREA|SELECT)$/.test(t.tagName));
      if ((e.key === '/' && !typing && !e.ctrlKey && !e.metaKey && !e.altKey) || ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K'))) {
        e.preventDefault(); open();
      } else if (e.key === 'Escape' && !panel.hidden) { close(); }
    });
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
