/* Long lists: show 50 rows at a time with Prev / Next.
   Works with each page's own search and filters (they hide rows with style.display = 'none');
   paging only hides the rows outside the current page, so counts and filters stay as they are. */
(function () {
  var css = document.createElement('style');
  css.textContent = 'table tbody tr.pg-off{display:none !important}'
    + '.crm-pg{display:flex;align-items:center;justify-content:flex-end;gap:6px;flex-wrap:wrap;padding:8px 10px;font-size:12.5px;color:var(--crm-muted,#64748b)}'
    + '.crm-pg[hidden]{display:none}'
    + '.crm-pg .pg-i{margin-right:auto}'
    + '.crm-pg button{height:30px;min-width:30px;padding:0 10px;border:1px solid var(--crm-line,#e2e8f0);border-radius:6px;background:var(--crm-surface,#fff);color:var(--crm-ink,#0f172a);font:inherit;font-weight:600;cursor:pointer}'
    + '.crm-pg button:hover:not(:disabled){border-color:var(--crm-ocean,#1e3a8a)}'
    + '.crm-pg button:disabled{opacity:.45;cursor:default}'
    + '.crm-pg button.is-on{background:var(--crm-ocean,#1e3a8a);border-color:var(--crm-ocean,#1e3a8a);color:#fff}';
  document.head.appendChild(css);

  window.crmPager = function (tbl, size) {
    if (typeof tbl === 'string') tbl = document.querySelector(tbl);
    if (!tbl || !tbl.tBodies[0] || tbl.__pg) return;
    tbl.__pg = 1; size = size || 50;
    var tb = tbl.tBodies[0], page = 0, t = null;
    var nav = document.createElement('nav');
    nav.className = 'crm-pg'; nav.setAttribute('aria-label', 'Pages');
    var anchor = tbl.closest('.sx-table-wrap, .table-responsive') || tbl;
    anchor.parentNode.insertBefore(nav, anchor.nextSibling);

    function shown() { return Array.prototype.filter.call(tb.rows, function (r) { return r.style.display !== 'none'; }); }
    function btn(label, p, on, off, aria) {
      var b = document.createElement('button'); b.type = 'button'; b.textContent = label;
      if (aria) b.setAttribute('aria-label', aria);
      if (on) { b.className = 'is-on'; b.setAttribute('aria-current', 'page'); }
      b.disabled = !!off;
      b.addEventListener('click', function () { page = p; apply(); var y = tbl.getBoundingClientRect().top + window.pageYOffset - 90; if (window.pageYOffset > y) window.scrollTo(0, y); });
      return b;
    }
    function apply() {
      var rs = shown(), pages = Math.max(1, Math.ceil(rs.length / size));
      if (page >= pages) page = pages - 1;
      Array.prototype.forEach.call(tb.rows, function (r) { r.classList.remove('pg-off'); });
      rs.forEach(function (r, i) { if (i < page * size || i >= (page + 1) * size) r.classList.add('pg-off'); });
      nav.innerHTML = '';
      nav.hidden = pages < 2;
      if (pages < 2) return;
      var info = document.createElement('span'); info.className = 'pg-i';
      info.textContent = (page * size + 1) + '–' + Math.min(rs.length, (page + 1) * size) + ' of ' + rs.length;
      nav.appendChild(info);
      nav.appendChild(btn('‹', page - 1, false, page === 0, 'Previous page'));
      var from = Math.max(0, Math.min(page - 2, pages - 5)), to = Math.min(pages, from + 5);
      for (var p = from; p < to; p++) nav.appendChild(btn(String(p + 1), p, p === page, false, 'Page ' + (p + 1)));
      nav.appendChild(btn('›', page + 1, false, page >= pages - 1, 'Next page'));
    }
    // a filter changed (rows shown or hidden): back to page 1
    new MutationObserver(function (ms) { if (!ms.some(function (m) { return m.type === 'childList' || (m.target.parentNode === tb); })) return; clearTimeout(t); t = setTimeout(function () { page = 0; apply(); }, 0); })
      .observe(tb, { attributes: true, attributeFilter: ['style'], subtree: true, childList: true });
    // typing in a search or changing a filter also starts again from page 1
    ['input', 'change'].forEach(function (ev) { document.addEventListener(ev, function (e) { if (tb.contains(e.target)) return; clearTimeout(t); t = setTimeout(function () { page = 0; apply(); }, 0); }); });
    apply();
  };
})();
