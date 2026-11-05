/* Permission editor behaviour (application/views/manager/roles/_perm_editor.php). */
(function () {
  var editors = {};
  var RANK = { no_access: 0, read: 1, write: 2 };

  function Editor(root) {
    this.root = root;
    this.listeners = [];
    this.baseline = null; // values to compare against ("Changed" badges)
    var self = this;

    root.addEventListener('change', function (e) {
      if (e.target && e.target.type === 'radio') { self.refresh(); self.emit(); }
    });
    root.querySelectorAll('[data-pe-all]').forEach(function (b) {
      b.addEventListener('click', function () { self.setAll(b.getAttribute('data-pe-all')); });
    });
    root.querySelectorAll('[data-pe-gset]').forEach(function (b) {
      b.addEventListener('click', function () {
        var g = b.closest('[data-pe-group]');
        self.setAll(b.getAttribute('data-pe-gset'), g);
      });
    });
    var search = root.querySelector('[data-pe-search]');
    if (search) search.addEventListener('input', function () { self.filter(search.value); });

    // Highlight the group in view.
    var links = root.querySelectorAll('[data-pe-nav]');
    links.forEach(function (a) {
      a.addEventListener('click', function (e) {
        var t = root.querySelector('#' + a.getAttribute('href').slice(1));
        if (t) { e.preventDefault(); t.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
      });
    });
    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (en) {
          if (en.isIntersecting) {
            var gk = en.target.getAttribute('data-pe-group');
            links.forEach(function (a) { a.classList.toggle('is-on', a.getAttribute('data-pe-nav') === gk); });
          }
        });
      }, { rootMargin: '-140px 0px -60% 0px' });
      root.querySelectorAll('[data-pe-group]').forEach(function (g) { io.observe(g); });
    }
    this.refresh();
  }

  Editor.prototype.rows = function (scope) { return (scope || this.root).querySelectorAll('[data-pe-key]'); };

  Editor.prototype.get = function () {
    var out = {};
    this.rows().forEach(function (r) {
      var c = r.querySelector('input:checked');
      out[r.getAttribute('data-pe-key')] = c ? c.value : 'no_access';
    });
    return out;
  };

  Editor.prototype.set = function (map) {
    map = map || {};
    this.rows().forEach(function (r) {
      var k = r.getAttribute('data-pe-key');
      var v = map[k] || 'no_access';
      var input = r.querySelector('input[value="' + v + '"]') || (v === 'write' ? r.querySelector('input[value="read"]') : null) || r.querySelector('input[value="no_access"]');
      if (input) input.checked = true;
    });
    this.refresh();
  };

  Editor.prototype.setAll = function (v, scope) {
    if (this.root.classList.contains('is-readonly')) return;
    this.rows(scope).forEach(function (r) {
      if (r.style.display === 'none') return; // respect the search filter
      var input = r.querySelector('input[value="' + v + '"]') || (v === 'write' ? r.querySelector('input[value="read"]') : null);
      if (input) input.checked = true;
    });
    this.refresh();
    this.emit();
  };

  Editor.prototype.setReadonly = function (on) {
    this.root.classList.toggle('is-readonly', !!on);
    this.root.querySelectorAll('input[type="radio"]').forEach(function (i) { i.disabled = !!on; });
  };

  Editor.prototype.setBaseline = function (map) { this.baseline = map; this.refresh(); };

  Editor.prototype.refresh = function () {
    var totals = { no_access: 0, read: 0, write: 0 };
    var base = this.baseline;
    var root = this.root;
    root.querySelectorAll('[data-pe-group]').forEach(function (g) {
      var on = 0, all = 0;
      g.querySelectorAll('[data-pe-key]').forEach(function (r) {
        var c = r.querySelector('input:checked');
        var v = c ? c.value : 'no_access';
        totals[v]++; all++;
        if (v !== 'no_access') on++;
        r.classList.toggle('is-diff', !!base && (base[r.getAttribute('data-pe-key')] || 'no_access') !== v);
      });
      var gk = g.getAttribute('data-pe-group');
      var meta = root.querySelector('[data-pe-gmeta="' + gk + '"]');
      if (meta) meta.textContent = on === 0 ? 'No access' : (on === all ? 'All ' + all + ' allowed' : on + ' of ' + all + ' allowed');
      var cnt = root.querySelector('[data-pe-gcount="' + gk + '"]');
      if (cnt) cnt.textContent = on + '/' + all;
    });
    Object.keys(totals).forEach(function (k) {
      var el = root.querySelector('[data-pe-count="' + k + '"]');
      if (el) el.textContent = totals[k];
    });
  };

  Editor.prototype.filter = function (q) {
    q = (q || '').trim().toLowerCase();
    var any = false;
    this.root.querySelectorAll('[data-pe-group]').forEach(function (g) {
      var shown = 0;
      g.querySelectorAll('[data-pe-key]').forEach(function (r) {
        var hit = !q || r.getAttribute('data-pe-text').indexOf(q) !== -1;
        r.style.display = hit ? '' : 'none';
        if (hit) shown++;
      });
      g.style.display = shown ? '' : 'none';
      if (shown) any = true;
    });
    var empty = this.root.querySelector('[data-pe-empty]');
    if (empty) empty.style.display = any ? 'none' : 'block';
  };

  Editor.prototype.onChange = function (fn) { this.listeners.push(fn); };
  Editor.prototype.emit = function () { var m = this.get(); this.listeners.forEach(function (fn) { fn(m); }); };

  window.CrmPerms = {
    get: function (id) {
      if (!editors[id]) {
        var el = document.getElementById(id);
        if (!el) return null;
        editors[id] = new Editor(el);
      }
      return editors[id];
    },
    rank: RANK
  };

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-pe]').forEach(function (el) { window.CrmPerms.get(el.id); });
  });
})();
