(function (window, $) {
  'use strict';

  var PRIMARY_FIELDS = [
    'customer', 'poc', 'contact1', 'alt_contact', 'email', 'location',
    'dnd_status', 'dnd_check_date', 'tl_authority', 'qty', 'mrc', 'mrr',
    'status', 'followup_date', 'followup_time', 'remark'
  ];

  function fieldColumn(el) {
    var $col = $(el).closest('[class*="col-"]');
    while ($col.length && $col.parent().closest('[class*="col-"]').length && !$col.parent().hasClass('row')) {
      $col = $col.parent().closest('[class*="col-"]');
    }
    return $col;
  }

  function primaryIndex($col) {
    var best = -1;
    $col.find('[name]').each(function () {
      var name = String($(this).attr('name')).replace('[]', '');
      var idx = PRIMARY_FIELDS.indexOf(name);
      if (idx !== -1 && (best === -1 || idx < best)) {
        best = idx;
      }
    });
    return best;
  }

  function mirrorColumn($original) {
    var $clone = $original.clone(false, false);
    $clone.find('[name]').each(function () {
      var $copy = $(this);
      var name = $copy.attr('name');
      var $source = $original.find('[name="' + name + '"]');
      $copy.removeAttr('name').removeAttr('id').removeAttr('required').attr('data-mirror-of', name);
      $copy.on('input change changeDate changeTime.timepicker', function () {
        if ($source.val() !== $copy.val()) {
          $source.val($copy.val()).trigger('change');
        }
      });
      $source.on('input change changeDate changeTime.timepicker', function () {
        if ($copy.val() !== $source.val()) {
          $copy.val($source.val());
        }
      });
    });
    $clone.find('label').removeAttr('for');
    return $clone;
  }

  function makeCollapsible($box, startOpen) {
    var $header = $box.children('.box-header').first();
    var $body = $box.children('.box-body').first();
    if (!$header.length || !$body.length || $box.hasClass('crm-collapsible')) {
      return;
    }
    $box.addClass('crm-collapsible' + (startOpen ? ' is-open' : ''));
    $header.attr({ role: 'button', tabindex: '0', 'aria-expanded': startOpen ? 'true' : 'false' });
    $header.append('<span class="crm-collapse-caret" aria-hidden="true"></span>');
    $header.on('click keydown', function (e) {
      if (e.type === 'keydown' && e.which !== 13 && e.which !== 32) {
        return;
      }
      if ($(e.target).closest('a, button, input, select, label').length && e.type === 'click') {
        return;
      }
      e.preventDefault();
      var open = !$box.hasClass('is-open');
      $box.toggleClass('is-open', open);
      $header.attr('aria-expanded', open ? 'true' : 'false');
    });
  }

  function compact(formSelector) {
    var $form = $(formSelector);
    if (!$form.length || $form.data('crmCompacted')) {
      return;
    }
    $form.data('crmCompacted', true).addClass('crm-compact-form');

    var $boxes = $form.children('.box');
    var $initial = $boxes.first();
    var $primaryRow = $initial.children('.box-body').children('.row').first();
    if (!$primaryRow.length) {
      return;
    }

    var $addl = $('<div class="crm-addl"><button type="button" class="crm-addl-toggle" aria-expanded="false">Additional Details<span class="crm-collapse-caret" aria-hidden="true"></span></button><div class="crm-addl-body"><div class="row crm-addl-grid"></div></div></div>');
    var $addlGrid = $addl.find('.crm-addl-grid');

    var primaries = [];
    var others = [];
    var seen = [];

    var $sources = $initial.add($boxes.filter(function () {
      return /more category/i.test($(this).find('.box-title').first().text());
    }));
    var $followBox = $boxes.filter(function () {
      return /followup/i.test($(this).find('.box-title').first().text());
    }).first();

    $sources.find('[name]').add($followBox.find('[name="followup_date"], [name="followup_time"]')).each(function () {
      var $col = fieldColumn(this);
      if (!$col.length || seen.indexOf($col[0]) !== -1) {
        return;
      }
      seen.push($col[0]);
      var idx = primaryIndex($col);
      if (idx !== -1 && ($followBox.has($col).length === 0 || /followup_(date|time)/.test($col.find('[name]').first().attr('name')))) {
        primaries.push({ idx: idx, col: $col });
      } else if ($followBox.has($col).length === 0) {
        others.push($col);
      }
    });

    primaries.sort(function (a, b) { return a.idx - b.idx; });
    $.each(primaries, function (_, item) {
      var $col = item.col;
      if ($followBox.has($col).length) {
        $col = mirrorColumn($col);
      }
      $col.attr('class', $col.find('textarea').length ? 'col-xs-12' : 'col-lg-3 col-md-4 col-sm-6 col-xs-12');
      $primaryRow.append($col);
    });
    $.each(others, function (_, $col) {
      $col.attr('class', 'col-lg-3 col-md-4 col-sm-6 col-xs-12');
      $addlGrid.append($col);
    });

    $('#btnToggleAdditional').closest('[class*="col-"]').remove();
    $('#addlWrap').remove();
    $initial.children('.box-body').append($addl);

    $sources.not($initial).each(function () {
      if (!$(this).find('[name]').length) {
        $(this).remove();
      }
    });

    $addl.find('.crm-addl-toggle').on('click', function () {
      var open = !$addl.hasClass('is-open');
      $addl.toggleClass('is-open', open);
      $(this).attr('aria-expanded', open ? 'true' : 'false');
    });

    $form.children('.box').not($initial).each(function () {
      makeCollapsible($(this), $followBox.length > 0 && this === $followBox[0]);
    });

    $form.find('input, select, textarea').on('invalid', function () {
      $(this).closest('.crm-addl').addClass('is-open');
      $(this).closest('.crm-collapsible').addClass('is-open');
    });
    $form.on('submit', function () {
      $form.find(':invalid').each(function () {
        $(this).closest('.crm-addl').addClass('is-open');
        $(this).closest('.crm-collapsible').addClass('is-open');
      });
    });
  }

  window.crmCompactLeadForm = compact;
})(window, jQuery);
