/* Team > Chart: interactive reporting chart. Data comes from the page (window.TM_CHART), already limited
   on the server to what this user may see (Admin: whole company; others: themselves and their team). */
(function () {
  var D = window.TM_CHART; if (!D) return;
  var root = document.getElementById('tc'); if (!root) return;
  var $ = function (s, c) { return (c || root).querySelector(s); };
  var esc = function (s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); };
  var store = { get: function (k) { try { return localStorage.getItem(k); } catch (e) { return null; } }, set: function (k, v) { try { localStorage.setItem(k, v); } catch (e) {} } };

  // ---- data
  var P = {}, kids = {}, tops = [];
  D.people.forEach(function (p) { P[p.id] = p; kids[p.id] = []; });
  D.people.forEach(function (p) { if (p.m && P[p.m]) kids[p.m].push(p.id); else tops.push(p.id); });
  var total = {};
  function size(id) { if (total[id] != null) return total[id]; var n = 0; kids[id].forEach(function (k) { n += 1 + size(k); }); return (total[id] = n); }
  D.people.forEach(function (p) { size(p.id); });
  function sortIds(a) { return a.sort(function (x, y) { var kx = kids[x].length ? 0 : 1, ky = kids[y].length ? 0 : 1; return kx - ky || P[x].n.localeCompare(P[y].n); }); }
  Object.keys(kids).forEach(function (k) { sortIds(kids[k]); });
  sortIds(tops);
  function path(id) { var a = []; var g = 0; while (id && P[id] && g++ < 50) { a.unshift(id); id = P[id].m; } return a; }

  var COLORS = ['#1e3a8a', '#0f766e', '#7c3aed', '#be185d', '#b45309', '#0369a1', '#4d7c0f', '#9f1239', '#334155', '#6d28d9'];
  function color(s) { var h = 0; for (var i = 0; i < s.length; i++) h = (h * 31 + s.charCodeAt(i)) >>> 0; return COLORS[h % COLORS.length]; }
  function initials(n) { var w = String(n).trim().split(/\s+/).filter(function (x) { return /[A-Za-z0-9]/.test(x); }); return ((w[0] || '?')[0] + (w.length > 1 ? w[w.length - 1][0] : '')).toUpperCase(); }
  function av(p) {
    return '<span class="tm-avw"><span class="tm-av" style="background:' + color(p.n) + '">' + (p.a ? '<img src="' + esc(p.a) + '" alt="" loading="lazy">' : esc(initials(p.n))) + '</span>' + (p.on ? '<span class="tm-dot" title="Online"></span>' : '') + '</span>';
  }
  function sub(p) { return [p.r, p.d].filter(function (x, i, a) { return x && a.indexOf(x) === i; }).join(' · ') || 'Team member'; }

  // ---- state
  var open = {}, sel = 0, focus = D.focus || 0;
  var mode = store.get('tmChartMode') || (window.innerWidth < 768 ? 'list' : 'chart');
  var zoom = 1;
  function openPath(id) { path(id).forEach(function (x) { if (x !== id) open[x] = true; }); }
  // start: tops open; for a team member, the way down to them and their own team open
  tops.forEach(function (t) { open[t] = true; });
  if (P[D.me]) { openPath(D.me); open[D.me] = true; }
  if (focus) { open[focus] = true; }

  // ---- render
  var vp = $('.tc-vp'), cv = $('.tc-cv');
  function rootIds() { return focus && P[focus] ? [focus] : tops; }
  function card(id) {
    var p = P[id], n = kids[id].length;
    var cls = 'tm-card' + (id === D.me ? ' is-me' : '') + (p.ctx ? ' is-ctx' : '') + (id === sel ? ' is-sel' : '');
    return '<button type="button" class="' + cls + '" data-id="' + id + '" title="' + esc(p.n) + (p.ctx ? ' (your manager)' : '') + '">' + av(p) +
      '<span class="tx"><span class="nm">' + esc(p.n) + (id === D.me ? '<span class="you">You</span>' : '') + '</span><span class="sb">' + esc(p.ctx ? 'Your manager' : sub(p)) + '</span></span></button>' +
      (n ? '<button type="button" class="tm-tog' + (open[id] ? ' is-open' : '') + '" data-tog="' + id + '" aria-expanded="' + (open[id] ? 'true' : 'false') + '" title="' + (open[id] ? 'Hide' : 'Show') + ' ' + n + ' direct report' + (n > 1 ? 's' : '') + '">' + (open[id] ? '<i class="fa fa-minus"></i>' : '<i class="fa fa-plus"></i>') + n + '</button>' : '');
  }
  function treeHtml(ids, top) {
    var leaves = ids.every(function (i) { return !kids[i].length; });
    var h = '<ul' + (top ? ' class="tc-tree"' : leaves && ids.length > 2 ? ' class="stack"' : '') + '>';
    ids.forEach(function (id) {
      h += '<li' + (kids[id].length ? ' class="has-kids"' : '') + '>' + card(id) + (kids[id].length && open[id] ? treeHtml(kids[id]) : '') + '</li>';
    });
    return h + '</ul>';
  }
  function listHtml(ids) {
    var h = '<ul>';
    ids.forEach(function (id) {
      var p = P[id], n = kids[id].length;
      h += '<li><div class="tm-row' + (id === sel ? ' is-sel' : '') + '" data-id="' + id + '" tabindex="0" role="button">' +
        (n ? '<button type="button" class="tw" data-tog="' + id + '" aria-expanded="' + (open[id] ? 'true' : 'false') + '" title="' + (open[id] ? 'Hide' : 'Show') + ' team"><i class="fa fa-chevron-' + (open[id] ? 'down' : 'right') + '"></i></button>' : '<span class="tw"></span>') +
        av(p) + '<span class="tx"><span class="nm">' + esc(p.n) + (id === D.me ? ' <span class="tmm-chip">You</span>' : '') + '</span><span class="sb"> ' + esc(p.ctx ? 'Your manager' : sub(p)) + '</span></span>' +
        (n ? '<span class="ct">' + n + ' direct · ' + size(id) + ' total</span>' : '') + '</div>' +
        (n && open[id] ? listHtml(kids[id]) : '') + '</li>';
    });
    return h + '</ul>';
  }
  function render() {
    root.classList.toggle('is-list', mode === 'list');
    $('[data-mode="chart"]').classList.toggle('is-on', mode === 'chart');
    $('[data-mode="list"]').classList.toggle('is-on', mode === 'list');
    $('.tc-zoom').style.display = mode === 'chart' ? '' : 'none';
    cv.innerHTML = mode === 'chart' ? treeHtml(rootIds(), true) : '<div class="tc-list">' + listHtml(rootIds()) + '</div>';
    cv.style.transform = mode === 'chart' && zoom !== 1 ? 'scale(' + zoom + ')' : '';
    $('.tc-zoom span').textContent = Math.round(zoom * 100) + '%';
    crumb();
  }
  function crumb() {
    var c = $('.tc-crumb');
    if (!focus) { c.hidden = true; return; }
    c.hidden = false;
    var h = '<i class="fa fa-sitemap"></i> <button type="button" data-focus="0">' + (D.seeAll ? 'Whole company' : 'My chart') + '</button>';
    path(focus).forEach(function (id) { h += ' <i class="fa fa-angle-right"></i> ' + (id === focus ? '<b>' + esc(P[id].n) + '</b>' : '<button type="button" data-focus="' + id + '">' + esc(P[id].n) + '</button>'); });
    c.innerHTML = h;
  }
  function centerOn(id, flash) {
    var el = cv.querySelector('[data-id="' + id + '"]'); if (!el) return;
    var r = el.getBoundingClientRect(), v = vp.getBoundingClientRect();
    vp.scrollLeft += (r.left + r.width / 2) - (v.left + v.width / 2);
    vp.scrollTop += (r.top + r.height / 2) - (v.top + v.height / 3);
    if (flash) { el.classList.remove('is-hit'); void el.offsetWidth; el.classList.add('is-hit'); }
  }

  // ---- side panel
  var side = $('.tc-side');
  function panel(id) {
    sel = id;
    var p = P[id]; if (!p) return;
    var n = kids[id].length, t = size(id), base = D.base;
    var mgr = p.m && P[p.m] ? P[p.m] : null;
    var h = '<div class="tc-side-h"><button type="button" class="tm-btn ic" data-close title="Close"><i class="fa fa-times"></i></button></div><div class="tc-p">' +
      av(p).replace('class="tm-av"', 'class="tm-av" style="margin:0 auto 10px;width:64px;height:64px;font-size:22px;background:' + color(p.n) + '"') +
      '<h3>' + esc(p.n) + '</h3><div class="rl">' + esc(sub(p)) + '</div><div class="st' + (p.on ? ' on' : '') + '">' + (p.on ? '● Online now' : 'Offline') + '</div>' +
      '<dl class="tc-kv">' +
      (mgr ? '<div><dt>Reports to</dt><dd><button type="button" data-goto="' + mgr.id + '">' + esc(mgr.n) + '</button></dd></div>' : '') +
      (p.o ? '<div><dt>Office</dt><dd>' + esc(p.o) + '</dd></div>' : '') +
      (p.e ? '<div><dt>Email</dt><dd><a href="mailto:' + esc(p.e) + '">' + esc(p.e) + '</a></dd></div>' : '') +
      (p.p ? '<div><dt>Phone</dt><dd><a href="tel:' + esc(String(p.p).replace(/[^\d+]/g, '')) + '">' + esc(p.p) + '</a></dd></div>' : '') +
      '</dl>' +
      '<div class="tc-nums"><div><b>' + n + '</b><span>Direct reports</span></div><div><b>' + t + '</b><span>Whole team</span></div></div>' +
      '<div class="tc-acts">' +
      (n && focus !== id ? '<button type="button" class="tm-btn pri" data-focus="' + id + '"><i class="fa fa-crosshairs"></i> Show only their team</button>' : '') +
      (n && !p.ctx ? '<a class="tm-btn" href="' + base + 'manager/team/members/details/' + id + '"><i class="fa fa-users"></i> Team members list</a>' : '') +
      (!p.ctx ? '<div class="two"><a class="tm-btn" href="' + base + 'manager/team/database?user_name=' + encodeURIComponent(p.n) + '" title="Database records they own"><i class="fa fa-database"></i> Database</a>' +
        '<a class="tm-btn" href="' + base + 'manager/team/leads?user_name=' + encodeURIComponent(p.n) + '" title="Leads they own"><i class="fa fa-bullseye"></i> Leads</a></div>' : '') +
      '</div></div>';
    side.innerHTML = h;
    root.classList.add('has-sel');
    cv.querySelectorAll('.is-sel').forEach(function (e) { e.classList.remove('is-sel'); });
    var el = cv.querySelector('[data-id="' + id + '"]'); if (el) el.classList.add('is-sel');
  }
  function closePanel() { sel = 0; root.classList.remove('has-sel'); cv.querySelectorAll('.is-sel').forEach(function (e) { e.classList.remove('is-sel'); }); }
  function goTo(id) {
    if (focus && path(id).indexOf(focus) < 0) focus = 0;
    openPath(id); render(); panel(id);
    setTimeout(function () { centerOn(id, true); }, 30);
  }

  // ---- events
  root.addEventListener('click', function (e) {
    var t = e.target.closest('[data-tog],[data-id],[data-focus],[data-goto],[data-close],[data-mode],[data-act]');
    if (!t || !root.contains(t)) return;
    if (t.hasAttribute('data-tog')) { var id = +t.getAttribute('data-tog'); open[id] = !open[id]; render(); if (sel) panel(sel); setTimeout(function () { centerOn(id); }, 20); return; }
    if (t.hasAttribute('data-goto')) { goTo(+t.getAttribute('data-goto')); return; }
    if (t.hasAttribute('data-focus')) { focus = +t.getAttribute('data-focus'); if (focus) open[focus] = true; zoom = 1; render(); if (sel) panel(sel); vp.scrollTop = 0; setTimeout(fitWidth, 20); return; }
    if (t.hasAttribute('data-close')) { closePanel(); return; }
    if (t.hasAttribute('data-mode')) { mode = t.getAttribute('data-mode'); store.set('tmChartMode', mode); render(); if (sel) panel(sel); return; }
    if (t.hasAttribute('data-act')) {
      var a = t.getAttribute('data-act');
      if (a === 'expand') { Object.keys(kids).forEach(function (k) { if (kids[k].length) open[k] = true; }); render(); }
      if (a === 'collapse') { open = {}; rootIds().forEach(function (r) { open[r] = true; }); render(); vp.scrollLeft = 0; vp.scrollTop = 0; setTimeout(fitWidth, 20); }
      if (a === 'zin') setZoom(zoom + 0.1);
      if (a === 'zout') setZoom(zoom - 0.1);
      if (a === 'fit') fit();
      if (a === 'full') { if (!document.fullscreenElement && root.requestFullscreen) root.requestFullscreen(); else if (document.exitFullscreen) document.exitFullscreen(); }
      if (sel) panel(sel);
      return;
    }
    if (t.hasAttribute('data-id')) { if (drag.moved) return; var pid = +t.getAttribute('data-id'); if (pid === sel) closePanel(); else panel(pid); }
  });
  root.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && sel) { closePanel(); return; }
    var r = e.target.closest && e.target.closest('.tm-row');
    if (r && (e.key === 'Enter' || e.key === ' ')) { e.preventDefault(); panel(+r.getAttribute('data-id')); }
  });

  // zoom
  function setZoom(z) { zoom = Math.max(0.3, Math.min(1.6, Math.round(z * 100) / 100)); cv.style.transform = zoom !== 1 ? 'scale(' + zoom + ')' : ''; $('.tc-zoom span').textContent = Math.round(zoom * 100) + '%'; sizeCanvas(); }
  function natural() { var t = cv.style.transform; cv.style.transform = ''; var w = cv.scrollWidth, h = cv.scrollHeight; cv.style.transform = t; return { w: w, h: h }; }
  function sizeCanvas() { /* keep scroll area matching the scaled size */ var n = natural(); cv.style.marginRight = zoom < 1 ? -(n.w * (1 - zoom)) + 'px' : ''; cv.style.marginBottom = zoom < 1 ? -(n.h * (1 - zoom)) + 'px' : ''; }
  function fit() { if (mode !== 'chart') return; var n = natural(); setZoom(Math.min(1, (vp.clientWidth - 10) / n.w, (vp.clientHeight - 10) / n.h)); vp.scrollLeft = 0; vp.scrollTop = 0; }
  function fitWidth() { if (mode !== 'chart') return; var n = natural(); var z = Math.min(1, (vp.clientWidth - 10) / n.w); if (z < 1) setZoom(Math.max(z, 0.55)); if (vp.scrollWidth > vp.clientWidth) vp.scrollLeft = (vp.scrollWidth - vp.clientWidth) / 2; }
  vp.addEventListener('wheel', function (e) { if (!(e.ctrlKey || e.metaKey) || mode !== 'chart') return; e.preventDefault(); setZoom(zoom + (e.deltaY < 0 ? 0.08 : -0.08)); }, { passive: false });

  // drag to move around
  var drag = { on: false, moved: false };
  vp.addEventListener('pointerdown', function (e) {
    if (e.button !== 0 || e.pointerType === 'touch' || e.target.closest('.tm-tog,.tw,a,input')) return;
    drag = { on: true, moved: false, x: e.clientX, y: e.clientY, l: vp.scrollLeft, t: vp.scrollTop };
  });
  window.addEventListener('pointermove', function (e) {
    if (!drag.on) return;
    var dx = e.clientX - drag.x, dy = e.clientY - drag.y;
    if (!drag.moved && Math.abs(dx) + Math.abs(dy) < 5) return;
    drag.moved = true; vp.classList.add('is-drag');
    vp.scrollLeft = drag.l - dx; vp.scrollTop = drag.t - dy;
  });
  window.addEventListener('pointerup', function () { if (!drag.on) return; drag.on = false; vp.classList.remove('is-drag'); setTimeout(function () { drag.moved = false; }, 0); });

  // search
  var inp = $('.tc-find input'), res = $('.tc-res'), hits = [], hi = 0;
  function showRes() {
    var q = inp.value.trim().toLowerCase();
    if (!q) { res.hidden = true; return; }
    hits = D.people.filter(function (p) { return (p.n + ' ' + p.r + ' ' + p.d + ' ' + p.e).toLowerCase().indexOf(q) >= 0; }).slice(0, 8);
    hi = 0;
    res.innerHTML = hits.length ? hits.map(function (p, i) { return '<button type="button" data-hit="' + p.id + '"' + (i === 0 ? ' class="is-on"' : '') + '>' + av(p) + '<span><b>' + esc(p.n) + '</b><small>' + esc(sub(p)) + '</small></span></button>'; }).join('') : '<div class="none">No one matches “' + esc(inp.value.trim()) + '”.</div>';
    res.hidden = false;
  }
  function pick(id) { res.hidden = true; inp.value = ''; inp.blur(); goTo(id); }
  inp.addEventListener('input', showRes);
  inp.addEventListener('focus', showRes);
  inp.addEventListener('keydown', function (e) {
    if (res.hidden || !hits.length) return;
    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') { e.preventDefault(); hi = (hi + (e.key === 'ArrowDown' ? 1 : hits.length - 1)) % hits.length; res.querySelectorAll('[data-hit]').forEach(function (b, i) { b.classList.toggle('is-on', i === hi); }); }
    if (e.key === 'Enter') { e.preventDefault(); pick(hits[hi].id); }
    if (e.key === 'Escape') { res.hidden = true; }
  });
  res.addEventListener('mousedown', function (e) { var b = e.target.closest('[data-hit]'); if (b) { e.preventDefault(); pick(+b.getAttribute('data-hit')); } });
  inp.addEventListener('blur', function () { setTimeout(function () { res.hidden = true; }, 120); });
  document.addEventListener('keydown', function (e) { if (e.key === '/' && document.activeElement && !/INPUT|TEXTAREA|SELECT/.test(document.activeElement.tagName)) { e.preventDefault(); inp.focus(); } });

  // summary
  var deps = {}, on = 0;
  D.people.forEach(function (p) { if (!p.ctx) { if (p.d) deps[p.d] = 1; if (p.on) on++; } });
  var people = D.people.filter(function (p) { return !p.ctx; }).length;
  $('.tc-sum').innerHTML = '<span><b>' + people + '</b> ' + (people === 1 ? 'person' : 'people') + '</span><span><b>' + Object.keys(deps).length + '</b> departments</span><span class="on"><b>' + on + '</b> online</span>';

  render();
  if (focus) panel(focus);
  setTimeout(function () { fitWidth(); if (!focus && P[D.me] && !D.seeAll) centerOn(D.me); }, 30);
})();
