/* AI calling agents: call drawer, "done" button, row menus. Needs window.AI_BASE (manager/ url). */
(function ($) {
  var base = window.AI_BASE || '';
  var hot = ['Meeting booked', 'Interested', 'Call back'], bad = ['Not interested', 'Wrong number', 'Do not call', 'Failed'];
  window.aiOutcomeClass = function (o) { return hot.indexOf(o) >= 0 ? 'oc-hot' : bad.indexOf(o) >= 0 ? 'oc-bad' : o ? 'oc-mid' : 'oc'; };
  function secs(s) { s = parseInt(s, 10) || 0; return s >= 60 ? Math.floor(s / 60) + ' min ' + (s % 60) + ' s' : s + ' s'; }

  function drawer() {
    var d = $('#aiDrawer');
    if (d.length) return d;
    d = $('<div class="ai-drawer" id="aiDrawer" role="dialog" aria-modal="true" aria-labelledby="aiDrTitle"><div class="bg"></div><div class="pn">' +
      '<div class="hd"><h3 id="aiDrTitle"></h3><button type="button" class="x" aria-label="Close">&times;</button></div><div class="bd"></div><div class="ft"></div></div></div>').appendTo('body');
    d.on('click', '.bg, .x', function () { d.removeClass('open'); });
    $(document).on('keydown', function (e) { if (e.key === 'Escape') d.removeClass('open'); });
    return d;
  }

  window.aiOpenCall = function (id) {
    var d = drawer().addClass('open');
    d.find('#aiDrTitle').text('Loading…');
    d.find('.bd, .ft').empty();
    $.post(base + 'ai-agents/ajax_call', { id: id }, function (r) {
      if (!r || !r.status) { d.find('#aiDrTitle').text((r && r.message) || 'Not found'); return; }
      var c = r.call, bd = d.find('.bd'), ft = d.find('.ft');
      d.find('#aiDrTitle').text(c.customer || c.contact || c.phone).append($('<small>').text([c.contact, c.phone].filter(Boolean).join(' · ')));
      var dl = $('<dl class="dl">');
      function row(k, v) { if (v) dl.append($('<dt>').text(k), $('<dd>').text(v)); }
      row('Result', c.outcome || c.status);
      row('Duration', c.duration > 0 ? secs(c.duration) : '');
      row('When', c.ended_at || c.started_at || c.queued_at);
      row('AI agent', c.agent_name);
      row('Campaign', c.campaign_name);
      row('Record owner', c.owner_name);
      row('Sentiment', c.sentiment);
      row('Next step', c.next_action);
      row('Follow-up date', c.followup_date);
      row('Problem', c.error);
      bd.append(dl);
      if (c.summary) bd.append($('<h4>').text('Summary'), $('<p>').text(c.summary));
      if (c.recording_url && /^https:\/\//i.test(c.recording_url)) bd.append($('<h4>').text('Recording'), $('<audio controls preload="none">').attr('src', c.recording_url));
      bd.append($('<h4>').text('Transcript'), c.transcript ? $('<div class="tr">').text(c.transcript) : $('<p class="text-muted">').text('No transcript.'));
      if (+c.lead_id > 0) ft.append($('<a class="btn btn-primary btn-sm"><i class="fa fa-phone"></i> Call in dialer</a>').attr('href', base + 'calls?lead=' + c.lead_id));
      if (hot.indexOf(c.outcome) >= 0 && +c.handled_by === 0 && +c.is_test === 0) {
        ft.append($('<button type="button" class="btn btn-default btn-sm"><i class="fa fa-check"></i> Mark done</button>').on('click', function () { aiHandled(c.id); d.removeClass('open'); }));
      }
      d.find('.x').trigger('focus');
    }, 'json');
  };

  window.aiHandled = function (id) {
    $.post(base + 'ai-agents/ajax_handled', { id: id }, function (r) {
      if (!r || !r.status) { alert((r && r.message) || 'Not saved'); return; }
      var li = $('.is-hot[data-call="' + id + '"]');
      li.slideUp(150, function () { li.remove(); $(document).trigger('ai:handled'); });
    }, 'json');
  };

  $(document).on('click', '[data-open-call]', function (e) { e.preventDefault(); aiOpenCall($(this).data('open-call')); });
  $(document).on('click', '[data-done]', function () { aiHandled($(this).data('done')); });
  $(document).on('click', '.cp-more > .btn', function (e) {
    e.stopPropagation();
    var m = $(this).parent();
    $('.cp-more').not(m).removeClass('open');
    m.toggleClass('open');
  });
  $(document).on('click keydown', function (e) { if (e.type === 'click' || e.key === 'Escape') $('.cp-more').removeClass('open'); });
})(jQuery);
