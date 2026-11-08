/* Logout popup: who is signed in, what is still due today, optional clock out, then log out.
   Needs window.CRM_LO = { base: '<base_url>', csrf: '<token name>' } set in the footer. */
(function ($) {
  if (!$ || !window.CRM_LO) return;
  var cfg = window.CRM_LO, base = cfg.base, open = false, busy = false, $back = null, lastFocus = null;

  function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
  function csrf() {
    var m = document.cookie.match(/(?:^|;\s*)csrf_cookie_name=([^;]+)/);
    var o = {}; if (m && cfg.csrf) o[cfg.csrf] = decodeURIComponent(m[1]);
    return o;
  }
  function pad(n) { return (n < 10 ? '0' : '') + n; }
  function since(iso) {
    var d = new Date(iso); if (isNaN(d)) return '';
    var now = new Date(), mins = Math.max(0, Math.round((now - d) / 60000));
    var dur = mins < 1 ? 'just now' : mins < 60 ? mins + ' min' : Math.floor(mins / 60) + ' h' + (mins % 60 ? ' ' + (mins % 60) + ' min' : '');
    var same = d.toDateString() === now.toDateString();
    var when = (same ? '' : d.toLocaleDateString(undefined, { weekday: 'short', day: 'numeric', month: 'short' }) + ', ') + pad(d.getHours()) + ':' + pad(d.getMinutes());
    return 'Signed in since ' + when + ' (' + dur + ')';
  }

  function who() {
    var img = $('.google-profile-large-img').first();
    var name = img.attr('alt') || $.trim($('.google-greeting-name').first().text()).replace(/^Hi,\s*/, '');
    return { ava: img.attr('src') || '', name: name || '', role: $.trim($('.google-user-role').first().text()) };
  }

  function tile(href, n, label, warn) {
    var cls = 'lo-tile' + (n > 0 && warn ? ' is-warn' : '') + (n === 0 ? ' is-zero' : '');
    return '<a class="' + cls + '" href="' + base + href + '"><b>' + n + '</b><span>' + label + '</span></a>';
  }

  function render(info) {
    var $s = $back.find('.lo-status');
    if (!info || !info.ok) { $s.empty(); return; }
    if (info.since) $back.find('.lo-since').html('<i class="fa fa-clock-o"></i>' + esc(since(info.since))).show();
    var any = info.fu_today + info.fu_missed + info.mt_today;
    if (any > 0) {
      $s.html('<div class="lo-label">Before you go</div><div class="lo-tiles">' +
        tile('manager/leads/followups?type=3', info.fu_today, 'Follow-ups today', false) +
        tile('manager/leads/followups?type=1', info.fu_missed, 'Missed follow-ups', true) +
        tile('manager/leads/meetings?type=3', info.mt_today, 'Meetings today', false) + '</div>');
    } else {
      $s.html('<div class="lo-clear"><i class="fa fa-check-circle"></i> Nothing due today. All clear.</div>');
    }
    if (info.clock_in && !info.clocked_out) {
      $back.find('.lo-clock').html('<label class="lo-opt"><span class="lo-opt-text"><b>Clock out too</b><span>Clocked in today at ' + esc(info.clock_in) + '</span></span>' +
        '<span class="lo-sw"><input type="checkbox" id="loClockOut"><i></i></span></label>');
    }
  }

  function show() {
    if (open) return;
    open = true; busy = false; lastFocus = document.activeElement;
    var u = who();
    var touch = window.matchMedia && window.matchMedia('(hover: none)').matches;
    $back = $('<div class="lo-back" role="dialog" aria-modal="true" aria-labelledby="loTitle">' +
      '<div class="lo-card">' +
        '<button type="button" class="lo-x" aria-label="Close"><i class="fa fa-times"></i></button>' +
        '<div class="lo-main">' +
          '<div class="lo-head">' +
            (u.ava ? '<img class="lo-ava" src="' + esc(u.ava) + '" alt="">' : '') +
            '<div class="lo-who"><h3 class="lo-title" id="loTitle">Log out?</h3>' +
              '<div class="lo-name"><b>' + esc(u.name) + '</b>' + (u.role ? '<span class="lo-role">' + esc(u.role) + '</span>' : '') + '</div>' +
              '<div class="lo-since" style="display:none"></div></div>' +
          '</div>' +
          '<div class="lo-body"><div class="lo-status"><div class="lo-skel"></div></div><div class="lo-clock"></div></div>' +
          '<div class="lo-foot">' +
            '<button type="button" class="lo-btn lo-stay">Stay signed in</button>' +
            '<button type="button" class="lo-btn lo-go"><i class="fa fa-power-off"></i> Log out</button>' +
          '</div>' +
          (touch ? '' : '<div class="lo-keys"><kbd>Enter</kbd> log out &nbsp;·&nbsp; <kbd>Esc</kbd> stay</div>') +
        '</div>' +
      '</div></div>');
    $('body').append($back).addClass('lo-open');
    $('#googleProfileDropdown').fadeOut(100); $('#profileMenuCaret').attr('aria-expanded', 'false');
    requestAnimationFrame(function () { $back.addClass('is-in'); });
    setTimeout(function () { $back.find('.lo-go').trigger('focus'); }, 30);

    $.ajax({ url: base + 'manager/logout-info', dataType: 'json', cache: false, timeout: 6000 })
      .done(function (d) { if (open && $back) render(d); })
      .fail(function () { if ($back) $back.find('.lo-status').empty(); });

    $back.on('click', function (e) { if (e.target === this && !busy) hide(); });
    $back.on('click', '.lo-x, .lo-stay', function () { if (!busy) hide(); });
    $back.on('click', '.lo-go', go);
  }

  function hide() {
    if (!open || !$back) return;
    var $b = $back; open = false; $back = null;
    $b.removeClass('is-in'); $('body').removeClass('lo-open');
    setTimeout(function () { $b.remove(); }, 180);
    if (lastFocus && lastFocus.focus) try { lastFocus.focus(); } catch (e) {}
  }

  function done(msg, sub, spin) {
    $back.find('.lo-x').hide();
    $back.find('.lo-main').html('<div class="lo-done" role="status">' + (spin ? '<div class="lo-spin"></div>' : '<div class="lo-tick"><i class="fa fa-check"></i></div>') +
      '<b>' + esc(msg) + '</b>' + (sub ? '<span>' + esc(sub) + '</span>' : '') + '</div>');
  }

  function toLogin() { window.location.href = base + 'manager/login'; }

  function go() {
    if (busy || !$back) return;
    busy = true;
    var clockOut = $back.find('#loClockOut').is(':checked');
    done('Signing you out…', clockOut ? 'Clocking you out first' : '', true);

    function finish(geo) {
      function logout() {
        $.ajax({ url: base + 'manager/logout', type: 'POST', data: $.extend({}, geo, csrf()) })
          .done(function () { done('Signed out', 'See you next time.', false); setTimeout(toLogin, 600); })
          .fail(toLogin);
      }
      if (!clockOut) return logout();
      var g = { type: 'out' };
      if (geo.location_status === 'granted') { g.latitude = geo.latitude; g.longitude = geo.longitude; g.accuracy = geo.accuracy; }
      $.ajax({ url: base + 'manager/hr/attendance/ajax_clock', type: 'POST', data: $.extend(g, csrf()), timeout: 8000 }).always(logout);
    }

    if ('geolocation' in navigator) {
      var sent = false;
      var once = function (g) { if (!sent) { sent = true; finish(g); } };
      navigator.geolocation.getCurrentPosition(function (pos) {
        once({ latitude: pos.coords.latitude, longitude: pos.coords.longitude, accuracy: pos.coords.accuracy, location_status: 'granted' });
      }, function (err) {
        once({ location_status: (err.code === 1 ? 'denied' : (err.code === 2 ? 'unavailable' : 'timeout')) });
      }, { timeout: 2500, enableHighAccuracy: true });
      setTimeout(function () { once({ location_status: 'timeout' }); }, 4000);
    } else {
      finish({ location_status: 'unsupported' });
    }
  }

  $(document).on('click', ".logout, a[href*='manager/logout']", function (e) {
    e.preventDefault(); e.stopPropagation();
    show();
    return false;
  });

  $(document).on('keydown', function (e) {
    if (!open || busy) return;
    if (e.key === 'Escape') { e.preventDefault(); hide(); }
    else if (e.key === 'Enter') {
      var t = e.target;
      if (t && (t.tagName === 'A' || (t.tagName === 'BUTTON' && !$(t).hasClass('lo-go')) || t.tagName === 'INPUT')) return;
      e.preventDefault(); go();
    } else if (e.key === 'Tab' && $back) {
      var f = $back.find('a, button, input').filter(':visible'); if (!f.length) return;
      var first = f[0], last = f[f.length - 1];
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
      else if (!$.contains($back[0], document.activeElement)) { e.preventDefault(); first.focus(); }
    }
  });
})(window.jQuery);
