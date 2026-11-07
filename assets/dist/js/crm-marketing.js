/* System > Marketing page. Needs window.MK = { base, page, acc, edit, cur, canWrite, hasAds }. acc: Meta account for Posts / Ads / Autopilot; edit: account open in Connections. */
$(function () {
  var B = MK.base;
  function esc(s) { return $('<div>').text(s == null ? '' : String(s)).html(); }
  function money(v) { return v == null ? '–' : MK.cur + ' ' + Number(v).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
  function post(url, data, done, btn, msgEl) {
    // every call names the account it is about (conn=); the Meta account in use unless the caller says otherwise
    if (typeof data === "string") { if (!/(^|&)conn=/.test(data)) data += (data ? "&" : "") + "conn=" + MK.acc; }
    else { data = data || {}; if (data.conn == null) data.conn = MK.acc; }
    if (btn) $(btn).prop('disabled', true);
    if (msgEl) $(msgEl).text('Working…');
    return $.post(B + url, data, function (r) {
      if (msgEl) $(msgEl).text((r && r.message) || '');
      done && done(r || {});
    }, 'json').fail(function () { if (msgEl) $(msgEl).text('The server did not answer. Try again.'); })
      .always(function () { if (btn) $(btn).prop('disabled', false); });
  }

  /* tabs (remembered per viewer) */
  var loadedAds = false;
  function tab(t) {
    $('.ai-tabs button').removeClass('is-on').attr('aria-selected', 'false').filter('[data-tab="' + t + '"]').addClass('is-on').attr('aria-selected', 'true');
    $('.ai-pane').attr('hidden', true).filter('[data-pane="' + t + '"]').removeAttr('hidden');
    try { localStorage.setItem('mk_tab', t); history.replaceState(null, '', '#' + t); } catch (e) {}
    if (t === 'ads' && MK.hasAds && !loadedAds) { loadedAds = true; loadAds(); }
  }
  $('.ai-tabs').on('click', 'button', function () { tab($(this).data('tab')); });
  $(document).on('click', '[data-go]', function () { tab($(this).data('go')); });
  var start = location.hash.replace('#', '');
  if (!start) { try { start = localStorage.getItem('mk_tab') || ''; } catch (e) {} }
  // after the other ready handlers (the CSRF helper) have run
  if ($('.ai-pane[data-pane="' + start + '"]').length) setTimeout(function () { tab(start); }, 0);

  /* drawers */
  function openD(id) { $(id).addClass('open'); $(id).find('input,select,textarea,button').filter(':visible').first().trigger('focus'); }
  $(document).on('click', '.ai-drawer [data-close]', function () { $(this).closest('.ai-drawer').removeClass('open'); });
  $(document).on('keydown', function (e) { if (e.key === 'Escape') $('.ai-drawer.open').removeClass('open'); });

  /* copy buttons */
  $(document).on('click', '[data-copy]', function () {
    var i = $($(this).data('copy'))[0], b = $(this);
    i.select();
    (navigator.clipboard ? navigator.clipboard.writeText(i.value) : Promise.reject()).catch(function () { document.execCommand('copy'); });
    b.text('Copied');
    setTimeout(function () { b.text('Copy'); }, 1500);
  });

  /* ---------- leads ---------- */
  function filterLeads() {
    var q = $.trim($('#lSearch').val()).toLowerCase(), cn = $('#lConn').val(), ow = $('#lOwner').val(), st = $('#lSt').val(), shown = 0;
    $('#leadList > li').each(function () {
      var ok = (!q || String($(this).data('q')).indexOf(q) > -1) && (cn === '' || String($(this).data('conn')) === cn) && (ow === '' || String($(this).data('owner')) === ow) && (!st || $(this).data('st') === st);
      if (!ok) $(this).find('.lPick').prop('checked', false);
      $(this).toggle(ok);
      if (ok) shown++;
    });
    $('#leadNone').toggle(shown === 0 && $('#leadList > li').length > 0);
    picked();
  }
  $('#lSearch').on('input', filterLeads);
  $('#lConn, #lOwner, #lSt').on('change', filterLeads);
  $('#btnSync').on('click', function () {
    var b = this;
    $(b).find('i').addClass('fa-spin');
    post('ajax_sync_leads', {}, function (r) {
      $(b).find('i').removeClass('fa-spin');
      alert(r.message || 'Done');
      if (r.new) location.reload();
    }, b);
  });
  $('#btnAddLead').on('click', function () { $('#leadForm')[0].reset(); $('#leadMsg').text(''); openD('#leadDrawer'); });
  $('#btnLeadSave').on('click', function () {
    post('ajax_add_lead', $('#leadForm').serialize(), function (r) { if (r.status) setTimeout(function () { location.reload(); }, 700); }, this, '#leadMsg');
  });

  /* ---------- posts ---------- */
  function count() { $('#pCount').text($('#pMsg').val().length + ' characters'); }
  function preview() {
    var u = $.trim($('#pImg').val());
    if (/^https:\/\//i.test(u)) { $('#pPrev').removeAttr('hidden').find('img').attr('src', u); } else { $('#pPrev').attr('hidden', true); }
  }
  $('#pMsg').on('input', count);
  $('#pImg').on('change blur', preview);
  function resetPost() {
    var f = $('#postForm')[0];
    if (!f) return;
    f.reset();
    $(f).find('[name=id]').val(0);
    $('#postForm h4').text('New post');
    $('#btnPostReset').attr('hidden', true);
    count(); preview();
  }
  $('#btnPostReset').on('click', resetPost);
  $(document).on('click', '[data-edit-post]', function () {
    var p = $(this).closest('li').data('post');
    var f = $('#postForm');
    f.find('[name=id]').val(p.id);
    f.find('[name="channels[]"]').each(function () { if (!this.disabled) this.checked = p.channels.indexOf(this.value) > -1; });
    $('#pMsg').val(p.message); $('#pImg').val(p.image_url); $('#pLink').val(p.link); $('#pWhen').val(p.scheduled_at);
    $('#postForm h4').text('Edit post');
    $('#btnPostReset').removeAttr('hidden');
    count(); preview();
    f[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
  $(document).on('click', '[data-del-post]', function () {
    if (!confirm('Delete this post from the CRM?')) return;
    post('ajax_delete_post', { id: $(this).data('del-post') }, function (r) { if (r.status) location.reload(); else alert(r.message); }, this);
  });
  $('#postForm').on('click', '[data-act]', function () {
    var act = $(this).data('act');
    if (act === 'now' && !confirm('Publish this post now?')) return;
    var d = $('#postForm').serialize() + '&act=' + act;
    post('ajax_save_post', d, function (r) { if (r.reload && (r.status || act === 'now')) setTimeout(function () { location.reload(); }, 900); }, this, '#postMsg');
  });
  $('#btnWritePost').on('click', function () {
    var topic = $.trim($('#pTopic').val());
    if (!topic) { $('#pTopic').trigger('focus'); $('#postMsg').text('Say what the post is about.'); return; }
    var ch = $('#postForm [name="channels[]"]:checked').map(function () { return this.value; }).get();
    var b = this;
    $(b).find('i').attr('class', 'fa fa-spinner fa-spin');
    post('ajax_ai_write', { kind: 'post', topic: topic, language: $('#pLang').val(), channels: ch }, function (r) {
      $(b).find('i').attr('class', 'fa fa-magic');
      if (r.status) { $('#pMsg').val(r.text); count(); $('#postMsg').text('Claude wrote a draft. Check it before posting.'); }
    }, b, '#postMsg').fail(function () { $(b).find('i').attr('class', 'fa fa-magic'); });
  });

  /* ---------- Meta Ads ---------- */
  var camps = {};
  function stChip(c) {
    var map = { ACTIVE: ['st-Active', 'Running'], PAUSED: ['st-Paused', 'Paused'], CAMPAIGN_PAUSED: ['st-Paused', 'Paused'], ADSET_PAUSED: ['st-Paused', 'Paused'], IN_PROCESS: ['st-Scheduled', 'Processing'], WITH_ISSUES: ['oc-bad', 'Has issues'], DISAPPROVED: ['oc-bad', 'Rejected'], PENDING_REVIEW: ['st-Scheduled', 'In review'], ARCHIVED: ['st-Ended', 'Archived'] };
    var k = c.effective || c.status, m = map[k] || ['s', k];
    return '<span class="chip ' + m[0] + '">' + esc(m[1]) + '</span>';
  }
  function loadAds() {
    $('#adsBody').html('<div class="sx-empty"><i class="fa fa-spinner fa-spin"></i>Loading campaigns…</div>');
    post('ajax_campaigns', { preset: $('#adPreset').val() }, function (r) {
      if (!r.rows && !r.message) r.message = 'The server did not answer. Press Refresh.';
      if (r.currency) MK.cur = r.currency;
      if (!r.status) { $('#adsBody').html('<div class="sx-empty"><i class="fa fa-exclamation-triangle"></i>' + esc(r.message) + '</div>'); return; }
      if (!r.rows.length) { $('#adsBody').html('<div class="sx-empty"><i class="fa fa-bullhorn"></i>No campaigns in this ad account yet.</div>'); return; }
      var tot = { spend: 0, leads: 0 }, h = '<ul class="cp-list">';
      camps = {};
      r.rows.forEach(function (c) {
        camps[c.id] = c;
        tot.spend += c.spend; tot.leads += c.leads;
        var budget = c.daily_budget != null ? money(c.daily_budget) + ' a day' : (c.lifetime_budget != null ? money(c.lifetime_budget) + ' total' : 'Budget on ad sets');
        h += '<li class="cp-row" data-id="' + esc(c.id) + '"><div><span class="cp-name">' + esc(c.name) + '</span><div class="cp-tags">' + stChip(c) +
          (c.objective ? '<span class="chip s">' + esc(c.objective.replace('OUTCOME_', '').toLowerCase()) + '</span>' : '') + '</div>' +
          '<div class="cp-meta"><span><i class="fa fa-money"></i>' + esc(budget) + '</span><span><i class="fa fa-eye"></i>' + c.impressions.toLocaleString() + ' seen</span><span><i class="fa fa-mouse-pointer"></i>' + c.clicks.toLocaleString() + ' clicks · CTR ' + c.ctr.toFixed(2) + '%</span></div></div>' +
          '<div class="cp-nums"><div><b>' + money(c.spend) + '</b><span>Spent</span></div><div><b>' + c.leads + '</b><span>Leads</span></div><div><b>' + (c.cpl != null ? money(c.cpl) : '–') + '</b><span>Per lead</span></div></div>' +
          '<div class="cp-acts">' + (MK.canWrite ? (c.status === 'ACTIVE' ? '<button type="button" class="btn btn-default btn-sm" data-ad-st="PAUSED"><i class="fa fa-pause"></i> Pause</button>' : (c.status === 'PAUSED' ? '<button type="button" class="btn btn-primary btn-sm" data-ad-st="ACTIVE"><i class="fa fa-play"></i> Run</button>' : '')) +
          (c.daily_budget != null ? '<button type="button" class="btn btn-default btn-sm" data-ad-bud><i class="fa fa-sliders"></i> Budget</button>' : '') : '') +
          '<button type="button" class="btn btn-default btn-sm" data-ad-sets>Ad sets</button></div></li>';
      });
      h += '</ul><div class="mk-tot">Total: <b>' + money(tot.spend) + '</b> spent · <b>' + tot.leads + '</b> leads' + (tot.leads ? ' · <b>' + money(tot.spend / tot.leads) + '</b> per lead' : '') + '</div>';
      $('#adsBody').html(h);
    }).fail(function () { $('#adsBody').html('<div class="sx-empty"><i class="fa fa-exclamation-triangle"></i>The server did not answer. Press Refresh.</div>'); });
  }
  $('#btnAdsLoad').on('click', loadAds);
  $('#adPreset').on('change', loadAds);
  $(document).on('click', '[data-ad-st]', function () {
    var row = $(this).closest('[data-id]'), id = row.data('id'), st = $(this).data('ad-st'), name = row.find('.cp-name').first().text() || row.data('name');
    if (!confirm((st === 'ACTIVE' ? 'Start running "' : 'Pause "') + name + '"?' + (st === 'ACTIVE' ? ' Meta will start spending the budget.' : ''))) return;
    post('ajax_ad_status', { object_id: id, status: st, name: name }, function (r) {
      if (!r.status) { alert(r.message); return; }
      if (row.closest('#setBody').length) loadSets(row.data('campaign')); else loadAds();
    }, this);
  });
  $(document).on('click', '[data-ad-bud]', function () {
    var row = $(this).closest('[data-id]'), id = row.data('id'), name = row.find('.cp-name').first().text(), cur = row.data('budget') || (camps[id] && camps[id].daily_budget);
    var v = prompt('New daily budget for "' + name + '" (' + MK.cur + ')', cur || '');
    if (v === null) return;
    v = parseFloat(String(v).replace(',', '.'));
    if (!(v > 0)) { alert('Enter an amount.'); return; }
    if (cur && v > cur * 2 && !confirm('That more than doubles the budget. Continue?')) return;
    post('ajax_ad_budget', { object_id: id, amount: v, name: name }, function (r) {
      if (!r.status) { alert(r.message); return; }
      if (row.closest('#setBody').length) loadSets(row.data('campaign')); else loadAds();
    }, this);
  });
  function loadSets(cid) {
    var c = camps[cid] || { name: 'Campaign' };
    $('#sdTitle').text('Ad sets · ' + c.name);
    $('#setBody').html('<div class="sx-empty"><i class="fa fa-spinner fa-spin"></i>Loading…</div>');
    openD('#setDrawer');
    post('ajax_adsets', { campaign_id: cid }, function (r) {
      if (!r.status) { $('#setBody').html('<p>' + esc(r.message) + '</p>'); return; }
      if (!r.rows.length) { $('#setBody').html('<p>No ad sets.</p>'); return; }
      var h = '<ul class="cp-list">';
      r.rows.forEach(function (s) {
        h += '<li class="cp-row" data-id="' + esc(s.id) + '" data-campaign="' + esc(cid) + '" data-budget="' + (s.daily_budget || '') + '"><div><span class="cp-name">' + esc(s.name) + '</span><div class="cp-tags">' + stChip({ status: s.status }) + '</div>' +
          '<div class="cp-meta"><span><i class="fa fa-globe"></i>' + esc(s.countries || '–') + '</span><span><i class="fa fa-user"></i>Age ' + esc(s.ages) + '</span><span><i class="fa fa-money"></i>' + (s.daily_budget != null ? money(s.daily_budget) + ' a day' : 'Campaign budget') + '</span></div>' +
          '<div class="cp-meta"><span>7 days: ' + money(s.spend) + ' · ' + s.leads + ' leads' + (s.cpl != null ? ' · ' + money(s.cpl) + ' per lead' : '') + '</span></div></div>' +
          '<div class="cp-acts">' + (MK.canWrite ? (s.status === 'ACTIVE' ? '<button type="button" class="btn btn-default btn-sm" data-ad-st="PAUSED"><i class="fa fa-pause"></i> Pause</button>' : '<button type="button" class="btn btn-primary btn-sm" data-ad-st="ACTIVE"><i class="fa fa-play"></i> Run</button>') +
          (s.daily_budget != null ? '<button type="button" class="btn btn-default btn-sm" data-ad-bud><i class="fa fa-sliders"></i> Budget</button>' : '') : '') + '</div></li>';
      });
      $('#setBody').html(h + '</ul><p class="hint">A campaign spends only when it and at least one ad set are running.</p>');
    });
  }
  $(document).on('click', '[data-ad-sets]', function () { loadSets($(this).closest('[data-id]').data('id')); });
  $('#btnAdsReview').on('click', function () {
    var b = this;
    $(b).find('i').attr('class', 'fa fa-spinner fa-spin');
    $('#aiReview').removeAttr('hidden').html('<i class="fa fa-spinner fa-spin"></i> Claude is reading the last 7 days…');
    post('ajax_ai_review', {}, function (r) {
      $(b).find('i').attr('class', 'fa fa-magic');
      $('#aiReview').html(r.status ? '<b><i class="fa fa-magic"></i> Claude’s review (last 7 days)</b><div class="tx">' + esc(r.text) + '</div><small>Advice only. Nothing was changed.</small>' : esc(r.message));
    }, b).fail(function () { $(b).find('i').attr('class', 'fa fa-magic'); });
  });

  /* new campaign */
  function loadForms(pick) {
    $('#nForm').html('<option value="">Loading forms…</option>');
    post('ajax_forms', {}, function (r) {
      var h = '<option value="">' + (r.status ? (r.rows.length ? 'Choose a form' : 'No forms yet: press New form') : esc(r.message)) + '</option>';
      (r.rows || []).forEach(function (f) { if (f.status !== 'ARCHIVED') h += '<option value="' + esc(f.id) + '">' + esc(f.name) + (f.leads_count != null ? ' (' + f.leads_count + ' leads)' : '') + '</option>'; });
      $('#nForm').html(h);
      if (pick) $('#nForm').val(pick);
    });
  }
  $('#btnNewCamp').on('click', function () { $('#campMsg').text(''); openD('#campDrawer'); loadForms(); });
  $('#btnNewForm').on('click', function () { $('#newForm').removeAttr('hidden'); $('#fName').trigger('focus'); });
  $('#btnFormSave').on('click', function () {
    var q = $('#newForm .fq:checked').map(function () { return this.value; }).get();
    post('ajax_create_form', { name: $('#fName').val(), privacy_url: $('#fPriv').val(), thanks: $('#fThanks').val(), questions: q }, function (r) {
      if (r.status) { $('#newForm').attr('hidden', true); loadForms(r.id); }
    }, this, '#formMsg');
  });
  $('#btnWriteAd').on('click', function () {
    var topic = $.trim($('#nTopic').val());
    if (!topic) { $('#nTopic').trigger('focus'); $('#campMsg').text('Say what the ad is about.'); return; }
    var b = this;
    $(b).find('i').attr('class', 'fa fa-spinner fa-spin');
    post('ajax_ai_write', { kind: 'ad', topic: topic, language: $('#nLang').val() }, function (r) {
      $(b).find('i').attr('class', 'fa fa-magic');
      if (r.status) { $('#nText').val(r.primary || r.text); if (r.headline) $('#nHead').val(r.headline); $('#campMsg').text('Claude wrote the ad. Check it.'); }
    }, b, '#campMsg').fail(function () { $(b).find('i').attr('class', 'fa fa-magic'); });
  });
  $('#btnCampSave').on('click', function () {
    post('ajax_create_campaign', $('#campForm').serialize(), function (r) {
      if (r.status) { setTimeout(function () { $('#campDrawer').removeClass('open'); loadAds(); }, 1200); }
    }, this, '#campMsg');
  });

  /* ---------- Meta account picker ---------- */
  $('#accSel').on('change', function () { location.href = MK.page + '?acc=' + encodeURIComponent(this.value) + location.hash; });

  /* ---------- assigning (several leads, owner + co-workers) ---------- */
  function picked() {
    var n = $('#leadList .lPick:checked').length;
    $('#lPicked').text(n + ' selected');
    $('#btnAssign').prop('disabled', n === 0);
    var vis = $('#leadList > li:visible .lPick');
    $('#lAll').prop('checked', vis.length > 0 && vis.filter(':checked').length === vis.length);
  }
  $(document).on('change', '.lPick', picked);
  $('#lAll').on('change', function () { $('#leadList > li:visible .lPick').prop('checked', this.checked); picked(); });
  $('#btnAssign').on('click', function () {
    var n = $('#leadList .lPick:checked').length;
    $('#asgForm')[0].reset(); $('#asgMsg').text('');
    $('#agSub').text(n + ' record' + (n === 1 ? '' : 's') + ': choose the owner and the people who work on them too.');
    openD('#asgDrawer');
  });
  $('#gOwner').on('change', function () { $('#asgForm [name="agents[]"]').prop('disabled', false).filter('[value="' + this.value + '"]').prop({ checked: false, disabled: true }); });
  $('#btnAsgSave').on('click', function () {
    var ids = $('#leadList .lPick:checked').map(function () { return this.value; }).get();
    post('ajax_assign', $('#asgForm').serialize() + '&' + $.param({ ids: ids }), function (r) { if (r.reload) setTimeout(function () { location.reload(); }, 700); }, this, '#asgMsg');
  });

  /* ---------- report ---------- */
  var repLoaded = false;
  function repArgs() { return $('#repForm').serialize(); }
  function loadReport() {
    repLoaded = true;
    $('#btnRepCsv').attr('href', B + 'report_csv?' + repArgs());
    $('#repBody').html('<div class="sx-empty"><i class="fa fa-spinner fa-spin"></i>Loading…</div>');
    post('ajax_report', repArgs(), function (r) {
      if (!r.status) { $('#repBody').html('<div class="sx-empty"><i class="fa fa-exclamation-triangle"></i>' + esc(r.message || 'Not available.') + '</div>'); return; }
      if (!r.rows.length) { $('#repBody').html('<div class="sx-empty"><i class="fa fa-bar-chart"></i>No marketing leads in this period.</div>'); return; }
      var own = r.group === 'owner', head = { owner: 'Owner', account: 'Account', source: 'Source Name' }[r.group];
      var cols = [['received', 'Leads'], ['created', 'New records'], ['dup', 'Already in CRM'], ['database', 'In Database'], ['lead', 'In Lead'], ['further', 'Further stage'], ['orders', 'Orders']];
      if (own) cols.push(['co_work', 'Co-worker on']);
      var tot = {}, h = '<div class="sx-table-wrap"><table class="sx-table"><thead><tr><th>' + head + '</th>';
      cols.forEach(function (c) { tot[c[0]] = 0; h += '<th class="num">' + c[1] + '</th>'; });
      h += '</tr></thead><tbody>';
      r.rows.forEach(function (x) {
        h += '<tr><td data-l="' + head + '"><b>' + esc(x.label) + '</b></td>';
        cols.forEach(function (c) { tot[c[0]] += x[c[0]]; h += '<td class="num" data-l="' + c[1] + '">' + Number(x[c[0]]).toLocaleString() + '</td>'; });
        h += '</tr>';
      });
      h += '</tbody><tfoot><tr><td>Total</td>';
      cols.forEach(function (c) { h += '<td class="num">' + (c[0] === 'co_work' ? '' : tot[c[0]].toLocaleString()) + '</td>'; });
      $('#repBody').html(h + '</tr></tfoot></table></div>');
    }).fail(function () { $('#repBody').html('<div class="sx-empty"><i class="fa fa-exclamation-triangle"></i>The server did not answer. Press Show.</div>'); });
  }
  $('#btnRep').on('click', loadReport);
  $('#repForm').on('change', 'select, input', loadReport);
  $('.ai-tabs').on('click', 'button[data-tab="report"]', function () { if (!repLoaded) loadReport(); });
  if (location.hash === '#report') setTimeout(function () { if (!repLoaded) loadReport(); }, 0);

  /* ---------- autopilot ---------- */
  $(document).on('click', '[data-approve-post]', function () {
    post('ajax_approve_post', { id: $(this).data('approve-post') }, function (r) { if (r.reload) location.reload(); else alert(r.message); }, this);
  });
  $('#apForm').on('submit', function (e) {
    e.preventDefault();
    post('ajax_save_autopilot', $(this).serialize(), function (r) { if (r.status) setTimeout(function () { location.hash = 'auto'; location.reload(); }, 700); }, $(this).find('[type=submit]'), '#apMsg');
  });
  $('[data-ap-run]').on('click', function () {
    var w = $(this).data('ap-run');
    if (!confirm(w === 'ads' ? 'Let Claude review the running campaigns now? With "Make the changes" on, it applies them within your limits.' : 'Let Claude write the missing posts for the next 7 days now?')) return;
    post('ajax_ap_run', { what: w }, function (r) {
      $('#apMsg').text(r.message || '');
      if (r.ok) setTimeout(function () { location.hash = w === 'ads' ? 'ads' : 'posts'; location.reload(); }, 1200);
    }, this, '#apMsg');
  });

  /* ---------- connections (accounts) ---------- */
  function editConn() { return parseInt($('#connForm [name=conn]').val(), 10) || 0; }
  function route() {
    var r = $('#connForm [name=route]:checked').val();
    $('.rt-fixed').toggle(r !== 'round');
    $('.rt-round').toggle(r === 'round');
  }
  $('#connForm [name=route]').on('change', route);
  route();
  function pageChosen() {
    var o = $('#mPage option:selected'), f = $('#connForm');
    f.find('[name=page_id]').val(o.val() || '');
    f.find('[name=page_name]').val(o.data('name') || '');
    f.find('[name=ig_id]').val(o.data('ig') || '');
    f.find('[name=ig_name]').val(o.data('ign') || '');
    $('#igHint').text(o.data('ig') ? 'Instagram: @' + o.data('ign') : 'No Instagram account linked to this page.');
    if (o.data('name') && !$.trim($('#eName').val())) $('#eName').val('Meta – ' + o.data('name'));
  }
  $('#mPage').on('change', pageChosen);
  $('#mAct').on('change', function () { var c = $('#mAct option:selected').data('cur'); if (c) $('#connForm [name=currency]').val(c); });
  $('#btnDiscover').on('click', function () {
    post('ajax_discover', { conn: editConn(), token: $('#mToken').val(), name: $('#eName').val(), source_name: $('#eSrc').val(), ref_month: $('#eRef').val() }, function (r) {
      if (r.id) $('#connForm [name=conn]').val(r.id);
      if (!r.status) return;
      $('#mToken').val('').attr('placeholder', 'Saved. Leave empty to keep it');
      var cur = $('#connForm [name=page_id]').val(), h = '';
      r.data.pages.forEach(function (p) { h += '<option value="' + esc(p.id) + '" data-name="' + esc(p.name) + '" data-ig="' + esc(p.ig_id) + '" data-ign="' + esc(p.ig_name) + '"' + (p.id === cur ? ' selected' : '') + '>' + esc(p.name) + (p.ig_name ? ' + @' + esc(p.ig_name) : '') + '</option>'; });
      $('#mPage').html(h || '<option value="">No pages found for this token</option>');
      pageChosen();
      var ca = $('#mAct').val(), a = '';
      r.data.ad_accounts.forEach(function (x) { a += '<option value="' + esc(x.id) + '" data-cur="' + esc(x.currency) + '"' + (x.id === ca ? ' selected' : '') + '>' + esc(x.name) + ' · ' + esc(x.currency) + (x.active ? '' : ' (not active)') + '</option>'; });
      $('#mAct').html(a + '<option value="">No ads</option>');
      if (!ca && r.data.ad_accounts.length) $('#mAct').val(r.data.ad_accounts[0].id);
      $('#mAct').trigger('change');
    }, this, '#discMsg');
  });
  function reloadConn(id) { location.href = MK.page + '?edit=' + (id || editConn()) + '#conn'; }
  $('#connForm').on('submit', function (e) {
    e.preventDefault();
    post('ajax_save_conn', $(this).serialize(), function (r) { if (r.status) { $('#mToken, #mSecret').val(''); setTimeout(function () { reloadConn(r.id); }, 600); } }, $(this).find('[type=submit]'), '#metaMsg');
  });
  $('#btnSubscribe').on('click', function () { post('ajax_subscribe', { conn: editConn() }, null, this, '#metaMsg'); });
  $('#btnDisconnect').on('click', function () {
    if (!confirm('Disconnect this account? Its token and app secret are removed from the CRM and it is switched off. Leads already taken in stay.')) return;
    post('ajax_disconnect', { conn: editConn() }, function (r) { if (r.status) setTimeout(function () { reloadConn(); }, 600); }, this, '#metaMsg');
  });
  $('#btnDelConn').on('click', function () {
    if (!confirm('Remove the account "' + $('#eName').val() + '"? Its keys are deleted and its unpublished posts stop. Leads and CRM records stay.')) return;
    post('ajax_delete_conn', { conn: editConn() }, function (r) { if (r.status) setTimeout(function () { location.href = MK.page + '#conn'; }, 600); }, this, '#metaMsg');
  });
  $('#claudeForm').on('submit', function (e) {
    e.preventDefault();
    post('ajax_save_claude', $(this).serialize(), function (r) { if (r.status) { $('#cKey').val(''); setTimeout(function () { location.hash = 'conn'; location.reload(); }, 600); } }, $(this).find('[type=submit]'), '#claudeMsg');
  });
  $('#btnTestClaude').on('click', function () { post('ajax_test_claude', {}, null, this, '#claudeMsg'); });
  $('#btnRenew').on('click', function () {
    if (!confirm('Make a new key for this account? Update its links in Meta, Google Ads, your website or Zapier afterwards.')) return;
    post('ajax_renew_keys', { conn: editConn() }, function (r) {
      if (!r.status) return;
      $('#hkMeta').val(r.meta_hook); $('#hkLead').val(r.lead_hook); $('#hkVerify').val(r.verify_token); $('#hkKey').val(r.webhook_key);
    }, this);
  });
  $('#btnRenewCron').on('click', function () {
    if (!confirm('Make a new cron key? Update the cron job on the server afterwards.')) return;
    post('ajax_renew_keys', { what: 'cron' }, function (r) { if (r.status) $('#hkCron').val(r.cron_url); }, this);
  });
});
