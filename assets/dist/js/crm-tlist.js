/* Team lists: phone card labels, status chips and owner initials.
   Display only; DataTables sorting, search and filters still use the original values. */
jQuery(function ($) {
  var $t = $('.tlv #example_lead');
  if (!$t.length || !$.fn.dataTable || !$.fn.dataTable.isDataTable($t)) return;
  var dt = $t.DataTable();
  var heads = $t.find('thead th').map(function () { return $.trim($(this).text()); }).get();
  var stCol = heads.lastIndexOf('Status');
  var ownCol = -1;
  $.each(heads, function (i, h) { if (/^(owner|created by|user|created)$/i.test(h)) ownCol = i; });
  $t.find('tfoot th').each(function (i) {
    var $s = $(this).find('select');
    if ($s.length) { $(this).addClass('tl-has'); $s.attr('aria-label', heads[i] || 'Filter').find('option').first().text((heads[i] || 'All') + ': all'); }
  });
  var COLORS = ['#1e3a8a', '#0f766e', '#7c3aed', '#be185d', '#b45309', '#0369a1', '#4d7c0f', '#9f1239', '#334155', '#6d28d9'];
  function color(s) { var h = 0; for (var i = 0; i < s.length; i++) h = (h * 31 + s.charCodeAt(i)) >>> 0; return COLORS[h % COLORS.length]; }
  function chip(t) {
    t = t.toLowerCase();
    if (/won|converted|approved|active|completed|sale/.test(t)) return 's-ok';
    if (/lost|reject|dead|cancel|dncr|not interested/.test(t)) return 's-x';
    if (/pending|review|expected|follow|progress|hold|process/.test(t)) return 's-w';
    if (/database|new/.test(t)) return 's-na';
    return '';
  }
  function textNode(td) { var n = null; $.each(td.childNodes, function () { if (this.nodeType === 3 && $.trim(this.nodeValue) !== '') { n = this; return false; } }); return n; }
  $(dt.rows().nodes()).each(function () {
    $(this).children('td').each(function (i) {
      $(this).attr('data-l', heads[i] || '');
      var n;
      if (i === stCol && !$(this).find('.tl-st').length && (n = textNode(this))) {
        var v = $.trim(n.nodeValue);
        $(n).replaceWith($('<span class="tl-st">').addClass(chip(v)).text(v));
      }
      if (i === ownCol && $(this).children().length === 0 && (n = textNode(this))) {
        var o = $.trim(n.nodeValue), w = o.split(/\s+/);
        if (!/[a-z]/i.test(o) || /\d{4}/.test(o)) return; // a date, not a name
        var ini = ((w[0] || '?')[0] + (w.length > 1 ? w[w.length - 1][0] : '')).toUpperCase();
        $(n).replaceWith($('<span class="tl-own">').append($('<i aria-hidden="true">').css('background', color(o)).text(ini), document.createTextNode(o)));
      }
    });
  });
});
