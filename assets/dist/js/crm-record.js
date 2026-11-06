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
    $cards.children('.box').addClass('rec-card').each(function () {
      // wide tables take the full width; short ones sit two per row
      var cols = $(this).find('table tr').first().children().length;
      if (cols >= 5) $(this).addClass('is-wide');
    });
    $host.find('> .box, > [class*="col-"] > .box').addClass('rec-card');

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
      $host.find('.rec-cards').children().each(function () {
        var $card = $(this);
        if ($card.is($first) || !$card.is(':visible')) return;
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
