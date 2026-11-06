<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$hm = function ($t) { return $t ? substr($t, 0, 5) : ''; };
$chip = array('P' => 'ok', 'L' => 'w', 'H' => 'w', 'W' => 'on', 'A' => 'x', 'LV' => 's', 'HO' => 'na', 'WE' => 'na', 'NY' => 'na', 'F' => 'na', 'NJ' => 'na');
$base = base_url() . 'manager/hr/attendance';
$tr = $today_rec;
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<style>
  .at-me { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
  .at-me b { font-size: 15px; }
  .at-me small { display: block; color: var(--crm-muted, #64748b); font-size: 13px; }
  .at-pick { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
  .at-pick .form-control { width: auto; height: 34px; }
  .at-grid { border-collapse: separate; border-spacing: 0; font-size: 12px; }
  .at-grid th, .at-grid td { padding: 4px 3px !important; text-align: center !important; border-bottom: 1px solid var(--crm-line-soft, #eef2f7); white-space: nowrap; }
  .at-grid th.nm, .at-grid td.nm { position: sticky; left: 0; z-index: 2; background: var(--crm-surface, #fff); text-align: left !important; padding-left: 10px !important; min-width: 170px; max-width: 220px; overflow: hidden; text-overflow: ellipsis; }
  .at-grid th.we, .at-grid td.we { background: var(--crm-bg-soft, #f8fafc); }
  .at-grid th.ho, .at-grid td.ho { background: rgba(220, 38, 38, .05); }
  .at-c { display: inline-block; min-width: 24px; padding: 2px 3px; border-radius: 6px; font-weight: 700; font-size: 11px; }
  .at-c.P { background: rgba(22, 163, 74, .12); color: #15803d; }
  .at-c.L, .at-c.H { background: rgba(217, 119, 6, .14); color: #b45309; }
  .at-c.W { background: rgba(37, 99, 235, .12); color: #1d4ed8; }
  .at-c.A { background: rgba(220, 38, 38, .12); color: #b91c1c; }
  .at-c.LV { background: rgba(124, 58, 237, .12); color: #6d28d9; }
  .at-c.HO, .at-c.WE { color: var(--crm-muted, #94a3b8); font-weight: 600; }
  .at-legend { display: flex; flex-wrap: wrap; gap: 10px; font-size: 12.5px; color: var(--crm-muted, #64748b); padding: 10px 14px; }
  .at-sub { display: block; font-size: 12px; color: var(--crm-muted, #64748b); }
</style>
<div class="content-wrapper sx">
  <?php echo crm_tabbar('hr_time', 'hr/attendance'); ?>
  <section class="sx-head">
    <h1>Attendance<small><?php echo $view === 'day' ? $h(date('l, d M Y', strtotime($date))) : $h(date('F Y', strtotime($month . '-01'))); ?></small></h1>
    <div class="sx-btns">
      <div class="sx-chips">
        <a href="<?php echo $base; ?>?date=<?php echo $h($view === 'day' ? $date : date('Y-m-d')); ?>" class="<?php echo $view === 'day' ? 'is-on' : ''; ?>">Day</a>
        <a href="<?php echo $base; ?>?view=month&amp;month=<?php echo $h($view === 'day' ? substr($date, 0, 7) : $month); ?>" class="<?php echo $view === 'month' ? 'is-on' : ''; ?>">Month</a>
      </div>
    </div>
  </section>
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="sx-card">
      <div class="sx-card-b at-me">
        <div>
          <b>My attendance today</b>
          <small><?php
            if (empty($tr)) echo 'Not clocked in yet';
            elseif (empty($tr['out_time'])) echo 'Clocked in at ' . $h($hm($tr['in_time'])) . ($tr['status'] === 'Late' ? ' (late)' : '');
            else echo 'In ' . $h($hm($tr['in_time'])) . ', out ' . $h($hm($tr['out_time'])) . ' (' . $h($tr['total_hours']) . ' hours)';
          ?></small>
        </div>
        <?php if (empty($tr)) { ?><button type="button" class="btn btn-success" id="atIn" data-t="in"><i class="fa fa-sign-in"></i> Clock in</button>
        <?php } elseif (empty($tr['out_time'])) { ?><button type="button" class="btn btn-danger" id="atOut" data-t="out"><i class="fa fa-sign-out"></i> Clock out</button>
        <?php } else { ?><span class="chip ok">Day completed</span><?php } ?>
      </div>
    </div>

    <?php if ($view === 'day') { ?>
    <div class="sx-kpis" id="atKpis">
      <button type="button" class="sx-kpi g" data-f="P"><span class="ic"><i class="fa fa-check"></i></span><div><b><?php echo $kpi['P'] + $kpi['W'] + $kpi['H']; ?></b><span>Present</span></div></button>
      <button type="button" class="sx-kpi a" data-f="L"><span class="ic"><i class="fa fa-clock-o"></i></span><div><b><?php echo $kpi['L']; ?></b><span>Late</span></div></button>
      <button type="button" class="sx-kpi r" data-f="A"><span class="ic"><i class="fa fa-user-times"></i></span><div><b><?php echo $kpi['A']; ?></b><span>Absent</span></div></button>
      <button type="button" class="sx-kpi s" data-f="LV"><span class="ic"><i class="fa fa-plane"></i></span><div><b><?php echo $kpi['LV']; ?></b><span>On leave</span></div></button>
      <?php if ($kpi['NY']) { ?><button type="button" class="sx-kpi" data-f="NY"><span class="ic"><i class="fa fa-hourglass-start"></i></span><div><b><?php echo $kpi['NY']; ?></b><span>Not in yet</span></div></button><?php } ?>
      <?php if ($kpi['out']) { ?><button type="button" class="sx-kpi a" data-f="OUT"><span class="ic"><i class="fa fa-sign-out"></i></span><div><b><?php echo $kpi['out']; ?></b><span>Did not clock out</span></div></button><?php } ?>
    </div>
    <?php if (isset($holidays[$date]) || in_array(date('D', strtotime($date)), $weekend, true)) { ?>
      <p class="sx-help"><i class="fa fa-flag"></i> <?php echo isset($holidays[$date]) ? 'Public holiday: ' . $h($holidays[$date]) : 'Weekend'; ?>. Nobody is marked absent.</p>
    <?php } ?>
    <?php } ?>

    <div class="sx-card">
      <div class="sx-bar">
        <form class="at-pick" method="get" action="<?php echo $base; ?>">
          <?php if ($view === 'day') { ?>
            <a class="btn btn-default" href="<?php echo $base; ?>?date=<?php echo date('Y-m-d', strtotime($date . ' -1 day')); ?>" aria-label="Previous day"><i class="fa fa-angle-left"></i></a>
            <input type="date" name="date" class="form-control" value="<?php echo $h($date); ?>" max="<?php echo date('Y-m-d'); ?>" onchange="this.form.submit()" aria-label="Date">
            <?php if ($date < date('Y-m-d')) { ?><a class="btn btn-default" href="<?php echo $base; ?>?date=<?php echo date('Y-m-d', strtotime($date . ' +1 day')); ?>" aria-label="Next day"><i class="fa fa-angle-right"></i></a><?php } ?>
          <?php } else { ?>
            <input type="hidden" name="view" value="month">
            <a class="btn btn-default" href="<?php echo $base; ?>?view=month&amp;month=<?php echo date('Y-m', strtotime($month . '-01 -1 month')); ?>" aria-label="Previous month"><i class="fa fa-angle-left"></i></a>
            <input type="month" name="month" class="form-control" value="<?php echo $h($month); ?>" max="<?php echo date('Y-m'); ?>" onchange="this.form.submit()" aria-label="Month">
            <?php if ($month < date('Y-m')) { ?><a class="btn btn-default" href="<?php echo $base; ?>?view=month&amp;month=<?php echo date('Y-m', strtotime($month . '-01 +1 month')); ?>" aria-label="Next month"><i class="fa fa-angle-right"></i></a><?php } ?>
          <?php } ?>
        </form>
        <input type="search" class="form-control sx-q" id="atQ" placeholder="Employee" autocomplete="off">
        <select class="form-control" id="atDept" aria-label="Department"><option value="">All departments</option><?php foreach ($depts as $d) { ?><option value="<?php echo $h(strtolower($d)); ?>"><?php echo $h($d); ?></option><?php } ?></select>
        <?php if ($view === 'month') { ?><a class="btn btn-default" href="<?php echo $base; ?>?view=month&amp;month=<?php echo $h($month); ?>&amp;csv=1"><i class="fa fa-download"></i> CSV</a><?php } ?>
        <?php if ($can_edit && $view === 'day') { ?><button type="button" class="btn btn-primary" id="atAdd"><i class="fa fa-plus"></i> Add / correct</button><?php } ?>
        <span class="sx-count" id="atCount"></span>
      </div>

      <?php if ($view === 'day') { ?>
      <div class="sx-table-wrap">
        <table class="sx-table sx-cards" id="atTable">
          <thead><tr><th>Employee</th><th>Shift</th><th>In</th><th>Out</th><th>Hours</th><th>Status</th><th>Location</th><?php if ($can_edit) { ?><th></th><?php } ?></tr></thead>
          <tbody>
          <?php foreach ($rows as $r) { $u = $r['u']; $c = $r['days'][$date]; $rec = $c['rec']; $sh = $c['shift'];
            $f = $c['code'] . ($rec && !empty($rec['in_time']) && empty($rec['out_time']) && $date < date('Y-m-d') ? ' OUT' : ''); ?>
            <tr data-f="<?php echo $f; ?>" data-q="<?php echo $h(strtolower($u['name'] . ' ' . $u['emp_id'])); ?>" data-d="<?php echo $h(strtolower(trim((string)$u['department']))); ?>">
              <td data-l="Employee"><b><?php echo $h($u['name']); ?></b><span class="at-sub"><?php echo $h($u['department']); ?></span></td>
              <td data-l="Shift"><?php echo $sh ? $h($hm($sh['start_time']) . '-' . $hm($sh['end_time'])) . '<span class="at-sub">' . $h($sh['shift_name']) . '</span>' : '<span class="text-muted">-</span>'; ?></td>
              <td data-l="In" class="mono"><?php echo $rec ? $h($hm($rec['in_time'])) : ''; ?></td>
              <td data-l="Out" class="mono"><?php echo $rec ? ($rec['out_time'] ? $h($hm($rec['out_time'])) : ($rec['in_time'] && $date < date('Y-m-d') ? '<span class="chip w">Missing</span>' : '')) : ''; ?></td>
              <td data-l="Hours"><?php echo $rec && (float)$rec['total_hours'] > 0 ? $h(number_format((float)$rec['total_hours'], 2)) : ''; ?></td>
              <td data-l="Status"><span class="chip <?php echo $chip[$c['code']]; ?>"><?php echo $h($c['label']); ?></span><?php if ($rec && !empty($rec['notes'])) { ?><span class="at-sub"><?php echo $h($rec['notes']); ?></span><?php } ?></td>
              <td data-l="Location"><?php if ($rec && !empty($rec['latitude'])) { ?><a href="https://maps.google.com/?q=<?php echo urlencode($rec['latitude'] . ',' . $rec['longitude']); ?>" target="_blank" rel="noopener" class="btn btn-default btn-sm"><i class="fa fa-map-marker"></i> Map<?php echo $rec['accuracy'] ? ' (±' . round($rec['accuracy']) . ' m)' : ''; ?></a><?php } ?></td>
              <?php if ($can_edit) { ?><td data-l="" class="nw"><?php if ($c['code'] !== 'F') { ?><button type="button" class="btn btn-default btn-sm atEdit" data-u="<?php echo (int)$u['id']; ?>" data-n="<?php echo $h($u['name']); ?>" data-s="<?php echo $h($rec ? $rec['status'] : ($c['code'] === 'A' ? 'Absent' : 'Present')); ?>" data-i="<?php echo $h($rec ? $hm($rec['in_time']) : ''); ?>" data-o="<?php echo $h($rec ? $hm($rec['out_time']) : ''); ?>" aria-label="Correct attendance of <?php echo $h($u['name']); ?>"><i class="fa fa-pencil"></i></button><?php } ?></td><?php } ?>
            </tr>
          <?php } ?>
          </tbody>
        </table>
        <div class="sx-empty" id="atNone" style="display:none;"><i class="fa fa-search"></i> Nobody matches.</div>
      </div>
      <?php } else { ?>
      <div class="sx-table-wrap">
        <table class="sx-table at-grid" id="atTable">
          <thead><tr><th class="nm">Employee</th>
            <?php foreach ($days as $dd) { $cls = isset($holidays[$dd]) ? 'ho' : (in_array(date('D', strtotime($dd)), $weekend, true) ? 'we' : ''); ?><th class="<?php echo $cls; ?>" title="<?php echo $h(date('D d M', strtotime($dd)) . (isset($holidays[$dd]) ? ': ' . $holidays[$dd] : '')); ?>"><?php echo date('j', strtotime($dd)); ?><br><small><?php echo substr(date('D', strtotime($dd)), 0, 2); ?></small></th><?php } ?>
            <th title="Present (incl. late, half day, from home)">P</th><th title="Late">L</th><th title="Absent">A</th><th title="Leave">LV</th><th title="Hours worked">Hrs</th></tr></thead>
          <tbody>
          <?php foreach ($rows as $r) { $u = $r['u']; $t = $r['tot']; ?>
            <tr data-q="<?php echo $h(strtolower($u['name'] . ' ' . $u['emp_id'])); ?>" data-d="<?php echo $h(strtolower(trim((string)$u['department']))); ?>" data-f="">
              <td class="nm" title="<?php echo $h($u['name']); ?>"><?php echo $h($u['name']); ?></td>
              <?php foreach ($days as $dd) { $c = $r['days'][$dd]; $cls = isset($holidays[$dd]) ? 'ho' : (in_array(date('D', strtotime($dd)), $weekend, true) ? 'we' : ''); $show = !in_array($c['code'], array('F', 'NJ', 'NY'), true); ?>
                <td class="<?php echo $cls; ?>"><?php if ($show) { ?><span class="at-c <?php echo $c['code']; ?>" title="<?php echo $h(date('d M', strtotime($dd)) . ': ' . $c['label'] . ($c['rec'] && $c['rec']['in_time'] ? ' ' . $hm($c['rec']['in_time']) . '-' . $hm($c['rec']['out_time']) : '')); ?>"><?php echo $c['code'] === 'WE' ? '·' : $c['code']; ?></span><?php } ?></td>
              <?php } ?>
              <td><b><?php echo $t['P'] + $t['L'] + $t['H'] + $t['W']; ?></b></td><td><?php echo $t['L']; ?></td><td><?php echo $t['A'] ? '<b style="color:#b91c1c">' . $t['A'] . '</b>' : 0; ?></td><td><?php echo $t['LV']; ?></td><td><?php echo number_format($t['hours'], 1); ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
        <div class="sx-empty" id="atNone" style="display:none;"><i class="fa fa-search"></i> Nobody matches.</div>
      </div>
      <div class="at-legend"><span><span class="at-c P">P</span> Present</span><span><span class="at-c L">L</span> Late</span><span><span class="at-c H">H</span> Half day</span><span><span class="at-c W">W</span> From home</span><span><span class="at-c A">A</span> Absent</span><span><span class="at-c LV">LV</span> Leave</span><span><span class="at-c HO">HO</span> Public holiday</span><span><span class="at-c WE">·</span> Weekend</span></div>
      <?php } ?>
    </div>
    <p class="sx-help"><i class="fa fa-info-circle"></i> Late is worked out from each person's shift (Shifts tab) and its grace minutes. Weekend days and working hours are set in HR Settings; public holidays come from Events &amp; Festivals.</p>
  </section>
</div>

<?php if ($can_edit && $view === 'day') { ?>
<div class="modal fade" id="atModal" tabindex="-1" role="dialog" aria-labelledby="atModalT">
  <div class="modal-dialog" role="document"><form class="modal-content" method="post" action="<?php echo base_url(); ?>manager/hr/attendance_save">
    <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="atModalT">Add or correct attendance</h4></div>
    <div class="modal-body sx">
      <input type="hidden" name="date" value="<?php echo $h($date); ?>">
      <div class="form-group"><label for="am1">Employee</label><select id="am1" name="user_id" class="form-control" required><option value="">Choose</option><?php foreach ($rows as $r) { ?><option value="<?php echo (int)$r['u']['id']; ?>"><?php echo $h($r['u']['name']); ?></option><?php } ?></select></div>
      <p class="text-muted" style="font-size:13px;margin-top:-6px;"><?php echo $h(date('l, d M Y', strtotime($date))); ?></p>
      <div class="form-group"><label for="am2">Status</label><select id="am2" name="status" class="form-control"><?php foreach ($statuses as $s) { ?><option><?php echo $h($s); ?></option><?php } ?></select></div>
      <div class="sx-grid" style="grid-template-columns:1fr 1fr;">
        <div class="form-group"><label for="am3">In</label><input id="am3" type="time" name="in_time" class="form-control"></div>
        <div class="form-group"><label for="am4">Out</label><input id="am4" type="time" name="out_time" class="form-control"></div>
      </div>
      <div class="form-group" style="margin-top:12px;"><label for="am5">Reason for the change</label><input id="am5" name="note" class="form-control" maxlength="200" placeholder="e.g. Forgot to clock out"></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
  </form></div>
</div>
<?php } ?>

<script>
$(function () {
  var rows = [].slice.call(document.querySelectorAll('#atTable tbody tr')), f = '';
  function run() {
    var s = $('#atQ').val().trim().toLowerCase(), d = $('#atDept').val(), n = 0;
    rows.forEach(function (r) {
      var on = (!s || r.getAttribute('data-q').indexOf(s) !== -1) && (!d || r.getAttribute('data-d') === d)
        && (!f || (' ' + r.getAttribute('data-f') + ' ').indexOf(' ' + f + ' ') !== -1 || (f === 'P' && /(^| )(W|H)( |$)/.test(r.getAttribute('data-f'))));
      r.style.display = on ? '' : 'none'; if (on) n++;
    });
    $('#atCount').text(n + (n === 1 ? ' person' : ' people'));
    $('#atNone').toggle(n === 0);
  }
  $('#atQ').on('input', run); $('#atDept').on('change', run);
  $('#atKpis').on('click', '.sx-kpi', function () {
    f = $(this).hasClass('is-on') ? '' : $(this).data('f');
    $('#atKpis .sx-kpi').removeClass('is-on'); if (f) $(this).addClass('is-on');
    run();
  });
  run();

  function edit(u, s, i, o) {
    $('#am1').val(u || ''); $('#am2').val(s || 'Present'); $('#am3').val(i || ''); $('#am4').val(o || ''); $('#am5').val('');
    $('#atModal').modal('show');
  }
  $('#atAdd').on('click', function () { edit('', 'Present', '', ''); });
  $(document).on('click', '.atEdit', function () { var b = $(this); edit(b.data('u'), b.data('s'), b.data('i'), b.data('o')); });

  $('#atIn, #atOut').on('click', function () {
    var b = $(this), type = b.data('t'), txt = b.html();
    b.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Getting location...');
    function send(pos) {
      $.post('<?php echo base_url(); ?>manager/hr/ajax_clock', { type: type, latitude: pos ? pos.coords.latitude : '', longitude: pos ? pos.coords.longitude : '', accuracy: pos ? pos.coords.accuracy : '' }, null, 'json')
        .done(function (r) { alert(r && r.message ? r.message : 'Done.'); if (r && r.status) location.reload(); else b.prop('disabled', false).html(txt); })
        .fail(function () { alert('Could not save. Please try again.'); b.prop('disabled', false).html(txt); });
    }
    // Clocking needs the location (same rule as before)
    if (!navigator.geolocation) { alert('This browser cannot share your location, so you cannot clock in here.'); b.prop('disabled', false).html(txt); return; }
    navigator.geolocation.getCurrentPosition(send, function (err) {
      alert('Your location could not be read (' + err.message + '). Allow location access and try again.');
      b.prop('disabled', false).html(txt);
    }, { enableHighAccuracy: true, timeout: 15000 });
  });
});
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
