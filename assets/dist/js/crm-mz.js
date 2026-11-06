/* My Zone pages: build the compact top bar. Moves the page's own buttons into the bar; nothing is removed or changed in the forms. */
(function () {
  var w = document.querySelector('.content-wrapper.mz-on');
  var bar = document.getElementById('mzBar');
  if (!w || !bar) return;
  var tools = bar.querySelector('.mz-tools'), nav = bar.querySelector('.mz-nav');
  function el(tag, cls, html) { var e = document.createElement(tag); if (cls) e.className = cls; if (html) e.innerHTML = html; return e; }

  // 1. Page title row: title (only when there are no tabs), record count and buttons move into the bar
  var hd = w.querySelector(':scope > section.ldv-head, :scope > section.content-header');
  if (hd) {
    var h1 = hd.querySelector('h1');
    var small = h1 && h1.querySelector('small');
    if (!nav.querySelector('.sx-tabs') && h1) {
      var t = h1.cloneNode(true); var s = t.querySelector('small'); if (s) s.remove();
      nav.appendChild(el('h1', 'mz-title')).textContent = t.textContent.trim();
    }
    if (small) tools.appendChild(el('span', 'mz-count')).textContent = small.textContent.trim();
    hd.querySelectorAll('a.btn, button.btn').forEach(function (b) {
      if (b.id === 'ldvFilterBtn') return;
      b.removeAttribute('style'); b.classList.add('mz-act'); tools.appendChild(b);
    });
  }

  // 2. Filters panel: folds away; open when a filter is in use or the user left it open
  var box = null;
  w.querySelectorAll('section.content .box.p-4').forEach(function (b) { if (!box && b.querySelector('form')) box = b; });
  if (box) {
    box.classList.add('mz-filters');
    // buttons that sat beside the filters (Add task, Raise ticket, quick ranges) go to the bar
    var side = w.classList.contains('ldv') ? null : box.querySelector('.x_col-sm-auto'); // Database list: its own header already has Add New (with the add right)
    var links = side ? side.querySelectorAll('a.btn, button.btn') : [];
    if (links.length >= 3) {
      var seg = el('nav', 'mz-seg'); seg.setAttribute('aria-label', 'Quick range');
      links.forEach(function (a) {
        var q = el('a'); q.href = a.href; q.textContent = a.textContent.trim();
        if (a.href === location.href) { q.className = 'is-on'; q.setAttribute('aria-current', 'true'); }
        seg.appendChild(q);
      });
      tools.appendChild(seg);
    } else {
      links.forEach(function (a) { a.removeAttribute('style'); a.classList.add('mz-act'); tools.appendChild(a); });
    }
    var form = box.querySelector('form'), names = {}, on = 0, qs = new URLSearchParams(location.search);
    form.querySelectorAll('[name]').forEach(function (f) { names[f.name.replace('[]', '')] = 1; });
    Object.keys(names).forEach(function (n) {
      var v = qs.getAll(n).concat(qs.getAll(n + '[]')).filter(function (x) { return x !== ''; });
      if (v.length) on++;
    });
    var key = 'mzf:' + location.pathname;
    var fb = el('button', 'btn mz-fbtn', '<i class="fa fa-filter"></i> Filters' + (on ? ' <span class="mz-badge">' + on + '</span>' : '') + ' <i class="fa fa-angle-down"></i>');
    fb.type = 'button'; fb.setAttribute('aria-controls', 'mzFilters'); if (!box.id) box.id = 'mzFilters'; else fb.setAttribute('aria-controls', box.id);
    function set(open, save) {
      w.classList.toggle('mz-f-open', open); w.classList.toggle('mz-f-closed', !open);
      box.classList.toggle('is-open', open);
      fb.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (save) { try { localStorage.setItem(key, open ? '1' : '0'); } catch (e) {} }
      if (open && window.jQuery) { var $ = window.jQuery; $(box).find('select.select2-hidden-accessible').each(function () { $(this).next('.select2-container').css('width', '100%'); }); }
    }
    var stored = null; try { stored = localStorage.getItem(key); } catch (e) {}
    set(on > 0 || stored === '1', false);
    fb.addEventListener('click', function () { set(!w.classList.contains('mz-f-open'), true); });
    tools.insertBefore(fb, tools.querySelector('.mz-act, .mz-seg'));
  } else {
    w.classList.remove('mz-f-closed');
  }
  var old = document.getElementById('ldvFilterBtn'); if (old) old.style.display = 'none';
})();
