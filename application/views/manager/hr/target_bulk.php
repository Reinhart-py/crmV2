<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$cur = $h(!empty($set['currency']) ? $set['currency'] : 'AED');
$roles = array(); $depts = array();
foreach ($people as $p) {
  if ($p['role_title'] !== null && $p['role_title'] !== '') $roles[$p['role_title']] = true;
  if ($p['department'] !== null && $p['department'] !== '') $depts[$p['department']] = true;
}
ksort($roles); ksort($depts);
$m0 = date('Y-m');
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_8">
<style>
  .tb-steps { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 14px; margin-bottom: 14px; }
  .tb-steps .sx-card { margin: 0; }
  .tb-n { display: inline-flex; width: 22px; height: 22px; border-radius: 50%; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; background: var(--crm-ocean, #2563eb); color: #fff; margin-right: 6px; }
  .tb-row { display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end; }
  .tb-row .form-group { margin: 0; flex: 1 1 120px; }
  .tb-row label { font-size: 12px; font-weight: 600; color: var(--crm-muted, #64748b); margin-bottom: 4px; display: block; }
  .tb-months { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; min-height: 26px; }
  .tb-months span { font-size: 12px; padding: 3px 9px; border-radius: 12px; background: var(--crm-ocean-soft, #eff6ff); color: var(--crm-ocean, #1d4ed8); font-weight: 600; }
  .tb-quick { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 10px; }
  .tb-quick .btn { padding: 3px 10px; font-size: 12px; }
  .tb-mode { display: inline-flex; border: 1px solid var(--crm-line, #e2e8f0); border-radius: 8px; overflow: hidden; margin-bottom: 10px; }
  .tb-mode label { margin: 0; padding: 6px 12px; font-size: 13px; cursor: pointer; font-weight: 600; color: var(--crm-ink-2, #334155); }
  .tb-mode input { display: none; }
  .tb-mode label.on { background: var(--crm-ocean, #2563eb); color: #fff; }
  .tb-unit { position: relative; }
  .sx .tb-unit input.form-control { padding-left: 48px !important; }
  .tb-unit b { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 12px; color: var(--crm-muted, #64748b); }
  table.sx-table td input.form-control { height: 32px; padding: 4px 8px; min-width: 80px; text-align: right; }
  table.sx-table td input.tb-amt { min-width: 110px; }
  table.sx-table tr.off td:not(:first-child) { opacity: .45; }
  table.sx-table tr.hide { display: none; }
  .tb-tick { width: 18px; height: 18px; cursor: pointer; vertical-align: middle; }
  .tb-sub { display: block; font-size: 12px; color: var(--crm-muted, #64748b); }
  .tb-has { font-size: 11px; font-weight: 600; color: #b45309; }
  .tb-foot { position: sticky; bottom: 0; z-index: 5; display: flex; gap: 12px; align-items: center; flex-wrap: wrap; padding: 12px 16px; background: var(--crm-surface, #fff); border-top: 1px solid var(--crm-line, #e2e8f0); border-radius: 0 0 10px 10px; }
  .tb-foot .tb-sum { flex: 1 1 220px; font-size: 14px; }
  .tb-foot .tb-sum b { font-size: 16px; }
  .tb-foot label { margin: 0; font-weight: 500; font-size: 13px; }
  .tb-foot select { width: auto; display: inline-block; }
  .tb-total { font-size: 12px; color: var(--crm-muted, #64748b); }
  @media (max-width: 767px) {
    table.sx-table td input.form-control { min-width: 0; width: 100%; }
    .tb-foot { position: static; }
    .tb-foot .btn-primary { width: 100%; }
  }
</style>
<div class="content-wrapper sx sx-hr">
  <?php echo crm_tabbar('hr_perf', 'hr/targets'); ?>
  <section class="sx-head">
    <h1>Set Targets <small>many people and months at once</small></h1>
    <div class="sx-btns"><a href="<?php echo base_url(); ?>manager/hr/targets" class="btn btn-default"><i class="fa fa-list"></i> All targets</a></div>
  </section>
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <form method="post" action="<?php echo base_url(); ?>manager/hr/targets/save" id="tbForm">
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

    <div class="tb-steps">
      <div class="sx-card">
        <div class="sx-card-h"><h3><span class="tb-n">1</span>Months</h3></div>
        <div class="sx-card-b">
          <div class="tb-row">
            <div class="form-group"><label for="tbFrom">From</label><input type="month" id="tbFrom" name="from_month" class="form-control" value="<?php echo $m0; ?>" required></div>
            <div class="form-group"><label for="tbTo">To</label><input type="month" id="tbTo" name="to_month" class="form-control" value="<?php echo $m0; ?>" required></div>
          </div>
          <div class="tb-quick">
            <button type="button" class="btn btn-default" data-q="0">This month</button>
            <button type="button" class="btn btn-default" data-q="1">Next month</button>
            <button type="button" class="btn btn-default" data-q="3">Next 3 months</button>
            <button type="button" class="btn btn-default" data-q="6">Next 6 months</button>
            <button type="button" class="btn btn-default" data-q="y">Rest of year</button>
          </div>
          <div class="tb-months" id="tbMonths"></div>
        </div>
      </div>

      <div class="sx-card">
        <div class="sx-card-h"><h3><span class="tb-n">2</span>Same target for everyone ticked</h3></div>
        <div class="sx-card-b">
          <?php if ($see_pay) { ?>
          <div class="tb-mode" role="radiogroup" aria-label="Sales target">
            <label class="on"><input type="radio" name="tb_mode" value="fixed" checked> Fixed amount</label>
            <label><input type="radio" name="tb_mode" value="x"> Times salary</label>
          </div>
          <?php } ?>
          <div class="tb-row">
            <div class="form-group" id="tbFixedBox"><label for="tbAmt">Sales a month</label><div class="tb-unit"><b><?php echo $cur; ?></b><input type="number" min="0" step="any" id="tbAmt" class="form-control" placeholder="0"></div></div>
            <?php if ($see_pay) { ?>
            <div class="form-group" id="tbXBox" style="display:none"><label for="tbX">Times monthly salary</label><div class="tb-unit"><b>&times;</b><input type="number" min="0" step="any" id="tbX" class="form-control" placeholder="e.g. 5"></div></div>
            <?php } ?>
            <div class="form-group"><label for="tbCalls">Calls</label><input type="number" min="0" id="tbCalls" class="form-control" placeholder="0"></div>
            <div class="form-group"><label for="tbLeads">Leads</label><input type="number" min="0" id="tbLeads" class="form-control" placeholder="0"></div>
            <div class="form-group"><label for="tbMeet">Meetings</label><input type="number" min="0" id="tbMeet" class="form-control" placeholder="0"></div>
          </div>
          <div class="tb-quick">
            <button type="button" class="btn btn-primary" id="tbFill"><i class="fa fa-arrow-down"></i> Fill ticked people</button>
            <button type="button" class="btn btn-default" id="tbCopy"><i class="fa fa-clone"></i> Copy each person's last target</button>
          </div>
        </div>
      </div>
    </div>

    <div class="sx-card">
      <div class="sx-card-h"><h3><span class="tb-n">3</span>People <small>change any number for one person</small></h3></div>
      <div class="sx-bar" style="padding:10px 16px 0;">
        <input type="search" class="form-control sx-q" id="tbQ" placeholder="Search name" aria-label="Search name">
        <?php if ($roles) { ?><select class="form-control" id="tbRole" aria-label="Role"><option value="">All roles</option><?php foreach ($roles as $r => $_) { ?><option><?php echo $h($r); ?></option><?php } ?></select><?php } ?>
        <?php if ($depts) { ?><select class="form-control" id="tbDept" aria-label="Department"><option value="">All departments</option><?php foreach ($depts as $d => $_) { ?><option><?php echo $h($d); ?></option><?php } ?></select><?php } ?>
        <span class="sx-count" id="tbShown"></span>
      </div>
      <div class="sx-table-wrap">
        <table class="sx-table sx-cards" id="tbTable">
          <thead><tr>
            <th style="width:36px"><input type="checkbox" class="tb-tick" id="tbAll" aria-label="Tick everyone shown"></th>
            <th>Person</th>
            <?php if ($see_pay) { ?><th class="text-right">Salary</th><?php } ?>
            <th class="text-right">Sales a month (<?php echo $cur; ?>)</th>
            <th class="text-right">Calls</th><th class="text-right">Leads</th><th class="text-right">Meetings</th>
            <th>Already set</th>
          </tr></thead>
          <tbody>
          <?php foreach ($people as $p) { $u = (int)$p['id']; $l = $p['last']; ?>
            <tr class="off" data-name="<?php echo $h(strtolower($p['name'])); ?>" data-role="<?php echo $h($p['role_title']); ?>" data-dept="<?php echo $h($p['department']); ?>"
                data-sal="<?php echo $p['salary'] !== null ? $p['salary'] : ''; ?>" data-set="<?php echo $h(implode(',', $p['set'])); ?>"
                <?php if ($l) { ?>data-last="<?php echo $h(json_encode(array((float)$l['target_amount'], (int)$l['target_calls'], (int)$l['target_leads'], (int)$l['target_meetings']))); ?>"<?php } ?>>
              <td data-l=""><input type="checkbox" class="tb-tick tb-on" name="t[<?php echo $u; ?>][on]" value="1" aria-label="Tick <?php echo $h($p['name']); ?>"></td>
              <td data-l="Person"><b><?php echo $h($p['name']); ?></b><span class="tb-sub"><?php echo $h(trim($p['role_title'] . ($p['department'] ? ' · ' . $p['department'] : ''), ' ·')); ?></span></td>
              <?php if ($see_pay) { ?><td data-l="Salary" class="text-right mono"><?php echo $p['salary'] !== null ? number_format($p['salary'], 0) : '<span class="tb-sub">not set</span>'; ?></td><?php } ?>
              <td data-l="Sales a month"><input type="number" min="0" step="any" class="form-control tb-amt" name="t[<?php echo $u; ?>][amount]" data-k="0"></td>
              <td data-l="Calls"><input type="number" min="0" class="form-control" name="t[<?php echo $u; ?>][calls]" data-k="1"></td>
              <td data-l="Leads"><input type="number" min="0" class="form-control" name="t[<?php echo $u; ?>][leads]" data-k="2"></td>
              <td data-l="Meetings"><input type="number" min="0" class="form-control" name="t[<?php echo $u; ?>][meetings]" data-k="3"></td>
              <td data-l="Already set" class="tb-setcell"></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
      <div class="tb-foot">
        <div class="tb-sum"><span id="tbSum">Tick the people to set targets for.</span><br><span class="tb-total" id="tbTotal"></span></div>
        <label>If a month already has a target
          <select name="existing" class="form-control input-sm">
            <option value="replace">replace it</option>
            <option value="keep">keep it</option>
          </select>
        </label>
        <input type="text" name="notes" class="form-control" style="max-width:220px" placeholder="Note (optional)" aria-label="Note">
        <?php if ($can_edit) { ?><button type="submit" class="btn btn-primary" id="tbSave"><i class="fa fa-check"></i> Save targets</button><?php } ?>
      </div>
    </div>
    </form>
    <p class="sx-help"><i class="fa fa-info-circle"></i> Each person gets the same target in every month you choose. Ticked people with no numbers are skipped. Everyone ticked gets a notification.</p>
  </section>
</div>
<script>
(function () {
  var $ = function (s, c) { return (c || document).querySelector(s); }, $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
  var from = $('#tbFrom'), to = $('#tbTo'), rows = $$('#tbTable tbody tr');
  var MN = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  var fmt = function (n) { return Math.round(n).toLocaleString('en-US'); };
  function months() {
    var a = from.value, b = to.value, out = [];
    if (!a || !b) return out;
    if (b < a) { var x = a; a = b; b = x; }
    var y = +a.slice(0, 4), m = +a.slice(5, 7);
    while (out.length < 24) {
      var k = y + '-' + (m < 10 ? '0' : '') + m;
      if (k > b) break;
      out.push(k); m++; if (m > 12) { m = 1; y++; }
    }
    return out;
  }
  function addM(n) { var d = new Date(); d.setDate(1); d.setMonth(d.getMonth() + n); return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2); }
  function refresh() {
    var ms = months();
    $('#tbMonths').innerHTML = ms.map(function (k) { return '<span>' + MN[+k.slice(5) - 1] + ' ' + k.slice(0, 4) + '</span>'; }).join('');
    var on = 0, amt = 0;
    rows.forEach(function (r) {
      var t = $('.tb-on', r).checked;
      r.classList.toggle('off', !t);
      var have = (r.getAttribute('data-set') || '').split(',').filter(function (k) { return ms.indexOf(k) > -1; });
      $('.tb-setcell', r).innerHTML = have.length ? '<span class="tb-has">' + have.length + ' of ' + ms.length + ' months</span>' : '';
      if (t) { on++; amt += +($('.tb-amt', r).value || 0); }
    });
    var n = ms.length;
    $('#tbSum').innerHTML = on ? '<b>' + on + '</b> ' + (on == 1 ? 'person' : 'people') + ' &times; <b>' + n + '</b> ' + (n == 1 ? 'month' : 'months') + ' = <b>' + (on * n) + '</b> targets' : 'Tick the people to set targets for.';
    $('#tbTotal').textContent = on && amt ? 'Team sales target: <?php echo $cur; ?> ' + fmt(amt) + ' a month' + (n > 1 ? ', ' + fmt(amt * n) + ' in total' : '') : '';
    var shown = rows.filter(function (r) { return !r.classList.contains('hide'); });
    $('#tbShown').textContent = shown.length + ' shown';
    var all = $('#tbAll'); all.checked = shown.length && shown.every(function (r) { return $('.tb-on', r).checked; });
  }
  function filter() {
    var q = ($('#tbQ').value || '').toLowerCase().trim(), ro = $('#tbRole') ? $('#tbRole').value : '', de = $('#tbDept') ? $('#tbDept').value : '';
    rows.forEach(function (r) {
      var ok = (!q || r.getAttribute('data-name').indexOf(q) > -1) && (!ro || r.getAttribute('data-role') === ro) && (!de || r.getAttribute('data-dept') === de);
      r.classList.toggle('hide', !ok);
    });
    refresh();
  }
  $$('.tb-quick [data-q]').forEach(function (b) {
    b.addEventListener('click', function () {
      var q = b.getAttribute('data-q');
      if (q === '0') { from.value = to.value = addM(0); }
      else if (q === '1') { from.value = to.value = addM(1); }
      else if (q === 'y') { from.value = addM(0); to.value = new Date().getFullYear() + '-12'; }
      else { from.value = addM(1); to.value = addM(+q); }
      refresh();
    });
  });
  var mode = 'fixed';
  $$('input[name="tb_mode"]').forEach(function (r) {
    r.addEventListener('change', function () {
      mode = r.value;
      $$('.tb-mode label').forEach(function (l) { l.classList.toggle('on', $('input', l).checked); });
      $('#tbFixedBox').style.display = mode === 'x' ? 'none' : '';
      $('#tbXBox').style.display = mode === 'x' ? '' : 'none';
    });
  });
  $('#tbFill').addEventListener('click', function () {
    var ticked = rows.filter(function (r) { return $('.tb-on', r).checked; });
    if (!ticked.length) { alert('Tick the people first.'); return; }
    var x = $('#tbX') ? +($('#tbX').value || 0) : 0, nosal = 0;
    ticked.forEach(function (r) {
      var amt = $('#tbAmt').value;
      if (mode === 'x') {
        var s = +(r.getAttribute('data-sal') || 0);
        if (!s) { nosal++; amt = ''; } else amt = x ? String(Math.round(s * x)) : '';
      }
      if (amt !== '' || mode === 'x') $('[data-k="0"]', r).value = amt;
      if ($('#tbCalls').value !== '') $('[data-k="1"]', r).value = $('#tbCalls').value;
      if ($('#tbLeads').value !== '') $('[data-k="2"]', r).value = $('#tbLeads').value;
      if ($('#tbMeet').value !== '') $('[data-k="3"]', r).value = $('#tbMeet').value;
    });
    if (nosal) alert(nosal + (nosal == 1 ? ' person has' : ' people have') + ' no salary in the HR file, so their sales target is left empty. Type it in.');
    refresh();
  });
  $('#tbCopy').addEventListener('click', function () {
    var n = 0;
    rows.forEach(function (r) {
      if (r.classList.contains('hide')) return;
      var l = r.getAttribute('data-last'); if (!l) return;
      l = JSON.parse(l); n++;
      $('.tb-on', r).checked = true;
      for (var k = 0; k < 4; k++) $('[data-k="' + k + '"]', r).value = l[k] || '';
    });
    if (!n) alert('No earlier targets to copy for the people shown.');
    refresh();
  });
  $('#tbAll').addEventListener('change', function () {
    var v = this.checked;
    rows.forEach(function (r) { if (!r.classList.contains('hide')) $('.tb-on', r).checked = v; });
    refresh();
  });
  rows.forEach(function (r) {
    $('.tb-on', r).addEventListener('change', refresh);
    $$('input[type="number"]', r).forEach(function (i) {
      i.addEventListener('input', function () { if (i.value !== '') $('.tb-on', r).checked = true; refresh(); });
    });
  });
  [from, to].forEach(function (i) { i.addEventListener('change', refresh); });
  $('#tbQ').addEventListener('input', filter);
  if ($('#tbRole')) $('#tbRole').addEventListener('change', filter);
  if ($('#tbDept')) $('#tbDept').addEventListener('change', filter);
  $('#tbForm').addEventListener('submit', function (e) {
    var ticked = rows.filter(function (r) { return $('.tb-on', r).checked; });
    if (!ticked.length) { e.preventDefault(); alert('Tick at least one person.'); return; }
    var n = months().length;
    if (!confirm('Save targets for ' + ticked.length + (ticked.length == 1 ? ' person' : ' people') + ' over ' + n + (n == 1 ? ' month' : ' months') + '?')) e.preventDefault();
  });
  filter();
})();
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
