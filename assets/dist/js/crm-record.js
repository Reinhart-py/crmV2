/*
 * Record previews (Database / Lead / Order / Sale): compact layout and the admin one-page A4 print.
 * Look only: values are moved as they are (links and labels kept), nothing is saved or changed.
 */
(function ($) {
  if (!$) return;
  $(function () {
    var $w = $('.content-wrapper.crm-rec');
    if (!$w.length) return;
    var page = $w.data('rec');

    // 1. Details: label/value lists become short groups of fields; empty fields fold away
    var GROUPS = {
      lead: [
        ['Company & contact', ['customer name', 'poc name', 'poc contact number', 'alternative contact number', 'email', 'alternate poc name', 'alternate poc contact', 'website']],
        ['Deal', ['service category', 'product category', 'product details', 'qty', 'mrc', 'mrr', 'revenue', 'one time charges', 'quote status', 'quote mode', 'quote amount', 'account number', 'account under vertical', 'current monthly revenue amount', 'current monthly revenue date']],
        ['Status & follow-up', ['status', 'status category', 'status remarks', 'next follow up date', 'next follow up time', 'exp. close date', 'remarks']],
        ['Address', ['location', 'address', 'city', 'state', 'country']],
        ['Company & compliance', ['company status', 'category', 'lead category', 'database category', 'industry category', 'dncr/dnd', 'dncr date', 'corporate tax status', 'vat status', 'trade license number', 'trade license authority', 'trade license emirates', 'trade license zone', 'tl issue date', 'tl expire date']],
        ['Owner & source', ['lead assigned to', 'created by', 'created at', 'modified at', 'previous owner', 'channel partner', 'data source', 'data batch referral no', 'database no', 'lead no']]
      ],
      order: [
        ['Order', ['order no', 'service title', 'vertical / category', 'monthly recurring revenue', 'order status']],
        ['Contact & owner', ['contact number', 'email', 'assigned staff', 'assigned department']],
        ['Dates', ['next follow-up date', 'created date', 'last updated']],
        ['References', ['database no', 'lead no']]
      ]
    };
    var groups = GROUPS[page] || [];
    var $first = $w.find('.my-profile > .box').first();
    var $body = $first.children('.box-body');
    var $tables = $body.find('table');
    var fields = [];
    function norm(t) { return $.trim(t).toLowerCase().replace(/\s+/g, ' ').replace(/\.$/, ''); }
    function isEmpty(t) { t = $.trim(t); return t === '' || t === '-' || t === '—' || t === 'N/A'; }
    $tables.find('tr').each(function () {
      var $c = $(this).children('th,td');
      if ($c.length < 2) return;
      var label = $.trim($c.eq(0).text());
      if (!label) return;
      fields.push({ label: label, key: norm(label), $v: $c.eq(1), empty: isEmpty($c.eq(1).text()) });
    });
    if (fields.length) {
      var used = [], nEmpty = 0;
      var $wrap = $('<div class="svd">');
      function fieldEl(f) {
        var $f = $('<div class="svd-f">').toggleClass('is-empty', f.empty);
        $('<span class="svd-l">').text(f.label).appendTo($f);
        var $v = $('<span class="svd-v">').append(f.$v.contents());
        if (f.empty) $v.text('—');
        if ($.trim($v.text()).length > 38) $f.addClass('is-wide');
        $f.append($v);
        f.$el = $f;
        if (f.empty) nEmpty++;
        return $f;
      }
      function addGroup(title, list) {
        if (!list.length) return;
        var $g = $('<section class="svd-g">');
        $('<h4>').text(title).appendTo($g);
        var $grid = $('<div class="svd-grid">').appendTo($g);
        var filled = 0;
        $.each(list, function (_, f) { $grid.append(fieldEl(f)); if (!f.empty) filled++; });
        if (!filled) $g.addClass('is-empty');
        $wrap.append($g);
      }
      $.each(groups, function (_, g) {
        var list = [];
        $.each(g[1], function (_, k) {
          $.each(fields, function (_, f) { if (used.indexOf(f) === -1 && f.key.indexOf(k) === 0) { list.push(f); used.push(f); } });
        });
        addGroup(g[0], list);
      });
      addGroup('Other', $.grep(fields, function (f) { return used.indexOf(f) === -1; }));
      if (nEmpty) {
        var $t = $('<button type="button" class="svd-toggle" aria-expanded="false">').html('<i class="fa fa-eye"></i> Show empty fields (' + nEmpty + ')');
        $t.on('click', function () {
          var on = !$wrap.hasClass('show-empty');
          $wrap.toggleClass('show-empty', on);
          $t.attr('aria-expanded', on ? 'true' : 'false').html(on ? '<i class="fa fa-eye-slash"></i> Hide empty fields' : '<i class="fa fa-eye"></i> Show empty fields (' + nEmpty + ')');
        });
        $wrap.append($('<div class="svd-foot">').append($t));
      }
      $tables.first().closest('.row').addClass('svd-old');
      $body.prepend($wrap);
    }

    // 2. Sections that sat inside one another become one list of cards, same order
    var $host = $first.closest('.my-profile').parent();
    var $cards = $('<div class="rec-cards">').appendTo($host);
    $w.find('.box').not($first).not('.modal .box').each(function () { $cards.append(this); });
    $host.children('[class*="col-"]').appendTo($cards).addClass('is-wide');
    $cards.children('.box').addClass('rec-card');
    $host.find('> .box, > [class*="col-"] > .box').addClass('rec-card');
    $cards.children().each(function () { if ($(this).css('display') === 'none') $(this).attr('data-rv-off', '1'); });

    // 3. Layout: a Summary card (key facts) beside tabs (Details, then one tab per section)
    function V(k) { for (var i = 0; i < fields.length; i++) if (fields[i].key.indexOf(k) === 0) return fields[i].empty ? null : fields[i]; return null; }
    function vEl(f) { return f && f.$el ? f.$el.children('.svd-v') : $(); }
    function vt(f) { return $.trim(vEl(f).text()).replace(/\s+/g, ' '); }
    function chipCls(t) { t = t.toLowerCase(); if (/lost|closed|reject|cancel|not interest|dead/.test(t)) return 'is-red'; if (/won|sale|approved|complete|fulfil|converted|active/.test(t)) return 'is-green'; if (/pending|hold|wait|follow/.test(t)) return 'is-amber'; return 'is-blue'; }
    var $side = $('<aside class="rv-side">'), $sc = $('<div class="rv-card rv-sum">').appendTo($side);
    $('<div class="rv-sum-h">').text('Summary').appendTo($sc);
    var $facts = $('<dl class="rv-facts">').appendTo($sc);
    function fact(label, icon, $val, cls) {
      if (!$val || !$val.length || !$.trim($val.text())) return;
      var $d = $('<div class="rv-fact">').addClass(cls || '');
      $('<dt>').html('<i class="fa fa-' + icon + '" aria-hidden="true"></i>').append(document.createTextNode(label)).appendTo($d);
      $('<dd>').append($val).appendTo($d);
      $facts.append($d);
    }
    function copy(k) { var f = V(k); return f ? $('<span>').append(vEl(f).clone(true).contents()) : null; }
    function chip(k) { var f = V(k); return f ? $('<span class="rv-chip">').addClass(chipCls(vt(f))).text(vt(f)) : null; }
    function link(k, kind) {
      var f = V(k); if (!f) return null;
      var t = vt(f);
      if (vEl(f).find('a').length || (kind === 'mail' && t.indexOf('@') < 0)) return copy(k);
      return $('<a>').attr('href', kind === 'tel' ? 'tel:' + t.replace(/[^0-9+]/g, '') : 'mailto:' + t).text(t);
    }
    function join(keys, sep) { var p = []; $.each(keys, function (_, k) { var f = V(k); if (f) p.push(vt(f)); }); return p.length ? $('<span>').text(p.join(sep)) : null; }
    function money(list) {
      var $m = $('<div class="rv-money">'), n = 0;
      $.each(list, function (_, k) { var f = V(k[0]); if (!f) return; n++; $('<div>').append($('<span>').text(k[1])).append($('<b>').text(vt(f))).appendTo($m); });
      if (n) $facts.append($('<div class="rv-fact is-full">').append($('<dt>').html('<i class="fa fa-money" aria-hidden="true"></i>Value')).append($('<dd>').append($m)));
    }
    if (page === 'lead') {
      fact('Status', 'flag', chip('status'));
      fact('Next follow-up', 'calendar', join(['next follow up date', 'next follow up time'], ', '));
      money([['mrc', 'MRC'], ['mrr', 'MRR'], ['revenue', 'Revenue']]);
      fact('Owner', 'user', copy('lead assigned to'));
      fact('Contact', 'male', join(['poc name'], ''));
      fact('Phone', 'phone', link('poc contact number', 'tel'));
      fact('Email', 'envelope-o', link('email', 'mail'));
      fact('Location', 'map-marker', join(['location', 'state'], ', '));
      fact('Expected close', 'flag-checkered', join(['exp. close date'], ''));
      var rf = V('remarks');
      if (rf) {
        var $r = $('<div class="rv-remark">').appendTo($sc);
        $('<div class="rv-remark-h">').html('<i class="fa fa-comment-o" aria-hidden="true"></i> Remarks').appendTo($r);
        var $p = $('<p>').text(vt(rf)).appendTo($r);
        if ($p.text().length > 200) {
          $p.addClass('is-clamp');
          $('<button type="button" class="rv-more" aria-expanded="false">').text('Show more').on('click', function () {
            var c = $p.toggleClass('is-clamp').hasClass('is-clamp');
            $(this).text(c ? 'Show more' : 'Show less').attr('aria-expanded', c ? 'false' : 'true');
          }).appendTo($r);
        }
      }
    } else {
      fact('Status', 'flag', chip('order status'));
      fact('Service', 'briefcase', join(['service title'], ''));
      var vf = V('vertical / category'), sf = V('service title');
      if (!sf || !vf || vt(vf).toLowerCase() !== vt(sf).toLowerCase()) fact('Vertical', 'th-large', join(['vertical / category'], ''));
      money([['monthly recurring revenue', 'MRR']]);
      fact('Assigned to', 'user', join(['assigned staff'], ''));
      fact('Department', 'sitemap', join(['assigned department'], ''));
      fact('Phone', 'phone', link('contact number', 'tel'));
      fact('Email', 'envelope-o', link('email', 'mail'));
      fact('Next follow-up', 'calendar', join(['next follow-up date'], ''));
    }
    var $rv = $('<div class="rv">'), $main = $('<div class="rv-main rv-card">');
    if ($facts.children().length || $sc.find('.rv-remark').length) $side.appendTo($rv); else $rv.addClass('no-side');
    var $tabs = $('<div class="rv-tabs" role="tablist" aria-label="Record sections">').appendTo($main);
    var $panels = $('<div class="rv-panels">').appendTo($main);
    var NAMES = [[/follow/i, 'Follow-ups', 'calendar-check-o'], [/meeting/i, 'Meetings', 'users'], [/call/i, 'Call logs', 'phone'], [/ownership|transfer/i, 'Ownership', 'exchange'], [/pipeline|conversion/i, 'History', 'history'], [/instruction|scope/i, 'Instructions', 'list-alt'], [/activity/i, 'Activity', 'history']];
    var tabN = 0;
    function addTab(name, icon, count, $content) {
      var id = 'rvp' + (tabN++);
      var $b = $('<button type="button" role="tab" class="rv-tab">').attr({ id: id + 't', 'aria-controls': id, 'data-name': name })
        .html('<i class="fa fa-' + icon + '" aria-hidden="true"></i><span>' + $('<i>').text(name).html() + '</span>');
      if (count !== null) $('<em class="rv-n">').text(count).toggleClass('is-zero', count === 0).appendTo($b);
      $tabs.append($b);
      $('<div role="tabpanel" class="rv-panel">').attr({ id: id, 'aria-labelledby': id + 't' }).append($content).appendTo($panels);
    }
    addTab('Details', 'list-ul', null, $first.closest('.my-profile'));
    $cards.children().each(function () {
      var $c = $(this);
      if ($c.attr('data-rv-off')) { $c.appendTo($panels); return; } // hidden by the page: stays hidden
      var title = $.trim($c.find('.box-title, .box-header h4').first().text()).replace(/\s+/g, ' ');
      var name = title || 'More', icon = 'folder-o';
      $.each(NAMES, function (_, n) { if (n[0].test(title)) { name = n[1]; icon = n[2]; return false; } });
      var cnt = null;
      if ($c.find('table').length && name !== 'Meetings') cnt = $c.find('table tr').filter(function () { return $(this).children('td').length > 1 && $.trim($(this).text()) !== ''; }).length;
      var $hd = $c.children('.box-header');
      if ($hd.length && !$hd.find('a, button, input, select').length) $hd.addClass('rv-hd-off'); // the tab already names it
      addTab(name, icon, cnt, $c);
    });
    $cards.remove();
    $main.appendTo($rv);
    var $head = $host.children('.svh');
    if ($head.length) $rv.insertAfter($head); else $rv.prependTo($host);
    var KEY = 'rvTab:' + page;
    function show(i) {
      var $b = $tabs.children('.rv-tab');
      if (i < 0 || i >= $b.length) i = 0;
      $b.removeClass('is-on').attr({ 'aria-selected': 'false', tabindex: '-1' }).eq(i).addClass('is-on').attr({ 'aria-selected': 'true', tabindex: '0' });
      $panels.children('.rv-panel').removeClass('is-on').eq(i).addClass('is-on');
      var el = $b.get(i);
      if (el && $tabs[0].scrollWidth > $tabs[0].clientWidth) $tabs.scrollLeft(el.offsetLeft - 16);
      try { sessionStorage.setItem(KEY, $b.eq(i).attr('data-name')); } catch (e) {}
    }
    $tabs.on('click', '.rv-tab', function () { show($(this).index()); });
    $tabs.on('keydown', '.rv-tab', function (e) {
      var n = $tabs.children().length, i = $(this).index();
      if (e.key === 'ArrowRight') i = (i + 1) % n; else if (e.key === 'ArrowLeft') i = (i - 1 + n) % n; else return;
      e.preventDefault(); show(i); $tabs.children().eq(i).trigger('focus');
    });
    var start = 0;
    try { var s = sessionStorage.getItem(KEY); if (s) $tabs.children().each(function (i) { if ($(this).attr('data-name') === s) start = i; }); } catch (e) {}
    show(start);
    $w.addClass('rv-ready');

    // 3. Admin: one-page A4 print (Print button, or Ctrl+P)
    var $btn = $('.rec-print');
    if (!$btn.length) return;
    var MM = 96 / 25.4;
    function txt(el) { return $.trim($(el).text()).replace(/\s+/g, ' '); }
    function build() {
      $('#recSheet').remove();
      var $s = $('<div id="recSheet" aria-hidden="true">');
      var kind = txt('.svh-kind'), name = txt($('.svh-id h1').clone().children('.svh-tag').remove().end());
      var tags = $('.svh-id .svh-tag').map(function () { return txt(this); }).get();
      var $h = $('<header class="rs-head">').appendTo($s);
      $('<div class="rs-kind">').text(kind).appendTo($h);
      $('<h1>').text(name).appendTo($h);
      if (tags.length) $('<div class="rs-tags">').text(tags.join(' · ')).appendTo($h);
      $('<div class="rs-date">').text('Printed ' + new Date().toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })).appendTo($h);
      var refs = $('.svh-steps li').map(function () { var b = txt($(this).find('.svh-st b')), v = txt($(this).find('.svh-st small')); return '<span><b>' + $('<i>').text(b).html() + '</b> ' + $('<i>').text(v).html() + '</span>'; }).get();
      if (refs.length) $('<div class="rs-refs">').html(refs.join('')).appendTo($s);
      $('.svh-note').each(function () { $('<div class="rs-note">').text(txt(this)).appendTo($s); });
      // details: filled fields only
      $('.svd-g').each(function () {
        var $f = $(this).find('.svd-f').not('.is-empty');
        if (!$f.length) return;
        var $g = $('<section class="rs-g">').appendTo($s);
        $('<h2>').text(txt($(this).children('h4'))).appendTo($g);
        var $grid = $('<div class="rs-grid">').appendTo($g);
        $f.each(function () {
          var v = txt($(this).find('.svd-v').clone().find('a:has(i.fa), .btn').remove().end()); // leave out on-screen links such as View Lead
          var $c = $('<div class="rs-f">').toggleClass('is-wide', v.length > 40);
          $('<span>').text(txt($(this).find('.svd-l'))).appendTo($c);
          $('<b>').text(v.length > 160 ? v.slice(0, 157) + '...' : v).appendTo($c);
          $grid.append($c);
        });
      });
      // other sections: title + latest rows of their table, or a short text
      $panels.find('.rec-card').each(function () {
        var $card = $(this);
        if ($card.is($first) || $card.attr('data-rv-off')) return;
        var title = txt($card.find('.box-title, h4').first());
        var $tb = $card.find('table').first();
        var $g = $('<section class="rs-g">');
        $('<h2>').text(title || 'Details').appendTo($g);
        if ($tb.length) {
          var $t = $tb.clone();
          $t.find('audio, video, button, .btn, input, select, textarea, form, script, style, .no_show, .dropdown-menu').remove();
          $t.removeAttr('id style class').addClass('rs-t').find('*').removeAttr('style class id');
          var $rows = $t.find('tr').filter(function () { return $(this).children('td').length > 0; });
          if (!$rows.filter(function () { return txt(this) !== ''; }).length) return;
          var more = $rows.length - 5;
          if (more > 0) $rows.slice(5).remove();
          $t.find('td, th').each(function () { var v = txt(this); $(this).text(v.length > 90 ? v.slice(0, 87) + '...' : v); });
          $g.append($t);
          if (more > 0) $('<div class="rs-more">').text('+ ' + more + ' more row' + (more === 1 ? '' : 's') + ' on screen').appendTo($g);
        } else {
          var v = txt($card.find('.box-body').first());
          if (!v) return;
          $('<p class="rs-p">').text(v.length > 600 ? v.slice(0, 597) + '...' : v).appendTo($g);
        }
        $s.append($g);
      });
      $('body').append($s);
      fit($s[0]);
      $('body').addClass('rec-printing');
    }
    // fit on one A4 page (210 x 297 mm, 10 mm margins): shrink, and widen by the same amount so it still fills the page width
    function fit(el) {
      var availH = 277 * MM * 0.9, z = 1;
      el.style.zoom = '1';
      el.style.maxHeight = 'none';
      el.style.width = '190mm';
      for (var i = 0; i < 5; i++) {
        var nz = Math.max(0.3, Math.min(1, availH / el.scrollHeight)); // height at zoom 1 and the current width
        if (i > 0 && Math.abs(nz - z) < 0.005) break;
        z = nz;
        el.style.width = (190 / z) + 'mm';
      }
      z = Math.max(0.3, Math.min(z, availH / el.scrollHeight)); // final check at the chosen width
      el.style.zoom = String(z);
      // never more than one page: anything past the page edge is cut instead of starting page 2
      el.style.maxHeight = (274 / z) + 'mm';
      return z;
    }
    window.crmRecFit = fit;
    function done() { $('body').removeClass('rec-printing'); $('#recSheet').remove(); }
    $btn.on('click', function () { build(); window.print(); });
    window.addEventListener('beforeprint', function () { if (!$('#recSheet').length) build(); });
    window.addEventListener('afterprint', done);
  });
})(window.jQuery);
