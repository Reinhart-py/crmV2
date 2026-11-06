<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$fd = function ($d) { $d = (float)$d; return ((floor($d) == $d) ? (int)$d : rtrim(rtrim(number_format($d, 1), '0'), '.')); };
$date = function ($d) { return $d && $d !== '0000-00-00' && strtotime($d) ? date('d M Y', strtotime($d)) : '-'; };
$hm = function ($t) { return $t ? date('h:i A', strtotime($t)) : ''; };
$money = function ($v) { return number_format((float)$v, 2); };
$cur = $h($set['currency']);
$annual = null;
foreach ($bal as $n => $b) if (stripos($n, 'annual') !== false) { $annual = $b; break; }
$soon = 0;
foreach ($ids as $d) if ($d['state'] === 'expired' || $d['state'] === 'soon') $soon++;
$stage_chip = array('Self review' => 'w', 'Manager review' => 'on', 'Completed' => 'ok');
$letter_chip = array('Requested' => 'w', 'Issued' => 'ok', 'Rejected' => 'x');
$doc_chip = array('expired' => array('x', 'Expired'), 'soon' => array('w', 'Renew soon'), 'valid' => array('ok', 'Valid'), 'none' => array('na', 'Not on file'));
$base = base_url() . 'manager/hr/';
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_8">
<style>
  .me-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(330px, 1fr)); gap: 16px; align-items: start; }
  .me-grid > .sx-card { margin: 0; }
  .me-dl { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 16px; margin: 0; }
  .me-dl dt { font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: .03em; color: var(--crm-muted, #64748b); }
  .me-dl dd { margin: 0; font-size: 14px; }
  .me-sub { display: block; font-size: 12px; color: var(--crm-muted, #64748b); }
  .me-list { list-style: none; margin: 0; padding: 0; }
  .me-list li { display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 9px 0; border-bottom: 1px solid var(--crm-line-soft, #eef2f7); }
  .me-list li:last-child { border-bottom: 0; }
  .me-clock { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; }
  .me-clock b { display: block; font-size: 15px; }
  .me-ck label { display: flex; gap: 10px; align-items: flex-start; font-weight: 500; margin: 0; cursor: pointer; }
  .me-ck input { margin-top: 3px; }
  .me-ck .done span { text-decoration: line-through; color: var(--crm-muted, #64748b); }
  @media (max-width: 480px) { .me-grid { grid-template-columns: 1fr; } .me-dl { grid-template-columns: 1fr; } }
</style>
<div class="content-wrapper sx sx-hr">
  <section class="sx-head">
    <h1>My HR<small><?php echo $h(trim($u['name'] . ($designation ? ', ' . $designation : '') . ($u['department'] ? ' · ' . $u['department'] : ''))); ?></small></h1>
    <div class="sx-btns"><a class="btn btn-default" href="<?php echo $base; ?>leaves"><i class="fa fa-calendar-check-o"></i> Request leave</a><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#meLetter"><i class="fa fa-envelope-o"></i> Ask for a letter</button></div>
  </section>
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="sx-card"><div class="sx-card-b me-clock">
      <div><b>Today, <?php echo date('l d M'); ?></b><span class="me-sub"><?php
        if (empty($today)) echo 'Not clocked in yet';
        elseif (empty($today['out_time'])) echo 'Clocked in at ' . $h($hm($today['in_time'])) . ($today['status'] === 'Late' ? ' (late)' : '');
        else echo 'In ' . $h($hm($today['in_time'])) . ', out ' . $h($hm($today['out_time'])) . ' (' . $h($today['total_hours']) . ' hours)'; ?></span></div>
      <?php if (empty($today)) { ?><button type="button" class="btn btn-success meClock" data-t="in"><i class="fa fa-sign-in"></i> Clock in</button>
      <?php } elseif (empty($today['out_time'])) { ?><button type="button" class="btn btn-danger meClock" data-t="out"><i class="fa fa-sign-out"></i> Clock out</button>
      <?php } else { ?><span class="chip ok">Day completed</span><?php } ?>
    </div></div>

    <div class="sx-kpis">
      <a class="sx-kpi g" href="<?php echo $base; ?>leaves"><span class="ic"><i class="fa fa-plane"></i></span><div><b><?php echo $annual && $annual['left'] !== null ? $fd($annual['left']) : '-'; ?></b><span>Annual leave days left</span></div></a>
      <div class="sx-kpi"><span class="ic"><i class="fa fa-check"></i></span><div><b><?php echo $att['Present'] + $att['Late'] + $att['Work From Home'] + $att['Half Day']; ?></b><span>Days worked in <?php echo date('F'); ?><?php echo $att['Late'] ? ' (' . $att['Late'] . ' late)' : ''; ?></span></div></div>
      <a class="sx-kpi s" href="#pay"><span class="ic"><i class="fa fa-money"></i></span><div><b><?php echo $slips ? $money($slips[0]['net']) : '-'; ?></b><span><?php echo $slips ? 'Net pay ' . $h(date('M Y', strtotime($slips[0]['month'] . '-01'))) : 'No payslip yet'; ?></span></div></a>
      <a class="sx-kpi <?php echo $soon ? 'r' : 'g'; ?>" href="#docs"><span class="ic"><i class="fa fa-folder-open-o"></i></span><div><b><?php echo $soon; ?></b><span>Documents to renew</span></div></a>
    </div>

    <?php if ($checklist && $cl_items) { $left = 0; foreach ($cl_items as $it) if (!$it['done_at']) $left++; ?>
    <div class="sx-card">
      <div class="sx-card-h"><h3><i class="fa fa-check-square-o"></i> <?php echo $checklist['kind'] === 'onboarding' ? 'Welcome! Your first steps' : 'Before you leave'; ?></h3><span class="sx-count" id="meClLeft"><?php echo $left; ?> to do</span></div>
      <div class="sx-card-b"><ul class="me-list me-ck">
        <?php foreach ($cl_items as $it) { $mine = $it['owner'] === 'Employee'; ?>
          <li class="<?php echo $it['done_at'] ? 'done' : ''; ?>"><label><input type="checkbox" class="meTick" data-id="<?php echo (int)$it['id']; ?>"<?php echo $it['done_at'] ? ' checked' : ''; ?><?php echo $mine ? '' : ' disabled'; ?>><span><?php echo $h($it['title']); ?></span></label><span class="me-sub nw"><?php echo $mine ? 'You' : $h($it['owner']); ?><?php echo $it['due_date'] ? ' · ' . $h(date('d M', strtotime($it['due_date']))) : ''; ?></span></li>
        <?php } ?>
      </ul></div>
    </div>
    <?php } ?>

    <div class="me-grid">
      <div class="sx-card">
        <div class="sx-card-h"><h3><i class="fa fa-user"></i> My details</h3></div>
        <div class="sx-card-b"><dl class="me-dl">
          <div><dt>Employee ID</dt><dd><?php echo $h($u['emp_id'] ?: '-'); ?></dd></div>
          <div><dt>Joined</dt><dd><?php echo $date($u['c_doj']); ?></dd></div>
          <div><dt>Manager</dt><dd><?php echo $h($u['manager_name'] ?: '-'); ?></dd></div>
          <div><dt>Office</dt><dd><?php echo $h($u['office'] ?: '-'); ?></dd></div>
          <div><dt>Contract</dt><dd><?php echo $h(!empty($life['contract_type']) ? $life['contract_type'] : '-'); ?><?php echo !empty($emp['contract_end']) ? '<span class="me-sub">until ' . $date($emp['contract_end']) . '</span>' : ''; ?></dd></div>
          <div><dt>Probation ends</dt><dd><?php echo $date(!empty($life['probation_end_date']) ? $life['probation_end_date'] : ''); ?></dd></div>
          <div><dt>Notice period</dt><dd><?php echo $h(!empty($emp['notice_days']) ? $emp['notice_days'] : $set['notice_days']); ?> days</dd></div>
          <div><dt>Salary paid to</dt><dd><?php $ib = (string)$u['iban']; echo $ib !== '' ? $h(substr($ib, 0, 4) . ' **** ' . substr($ib, -4)) : '-'; ?><?php echo $u['bank'] ? '<span class="me-sub">' . $h($u['bank']) . '</span>' : ''; ?></dd></div>
        </dl><p class="sx-help" style="margin:12px 0 0;">Something wrong? Tell HR; they keep your file up to date.</p></div>
      </div>

      <div class="sx-card" id="docs">
        <div class="sx-card-h"><h3><i class="fa fa-folder-open-o"></i> My documents</h3></div>
        <div class="sx-card-b"><ul class="me-list">
          <?php foreach ($ids as $d) { $c = $doc_chip[$d['state']]; ?>
            <li><div><b style="font-weight:600;"><?php echo $h($d['label']); ?></b><span class="me-sub"><?php echo $d['no'] ? $h($d['no']) . ' · ' : ''; ?><?php echo $d['expiry'] ? 'expires ' . $date($d['expiry']) : 'no expiry date'; ?></span></div>
            <span class="chip <?php echo $c[0]; ?>"><?php echo $d['state'] === 'soon' ? $h($d['left'] . ' days left') : $c[1]; ?></span></li>
          <?php } ?>
          <?php foreach ($files as $f) { ?>
            <li><div><i class="fa fa-file-o"></i> <?php echo $h($f['doc_type']); ?><span class="me-sub"><?php echo $h($f['orig_name']); ?></span></div><a class="btn btn-default btn-sm" href="<?php echo $base; ?>my_doc/<?php echo (int)$f['id']; ?>" target="_blank" rel="noopener"><i class="fa fa-eye"></i> Open</a></li>
          <?php } ?>
        </ul></div>
      </div>

      <div class="sx-card">
        <div class="sx-card-h"><h3><i class="fa fa-calendar-check-o"></i> My leave <?php echo date('Y'); ?></h3><div class="sx-tools"><a class="btn btn-default btn-sm" href="<?php echo $base; ?>leaves">All requests</a></div></div>
        <div class="sx-card-b"><ul class="me-list">
          <?php $shown = 0; foreach ($bal as $n => $b) { if ($b['entitled'] === null && !$b['taken'] && !$b['pending']) continue; if ($shown++ >= 5) break; ?>
            <li><span><?php echo $h($n); ?></span><span class="nw"><?php echo $b['left'] !== null ? '<b>' . $fd($b['left']) . '</b> <span class="me-sub" style="display:inline;">of ' . $fd($b['entitled'] + $b['adjust']) . ' left</span>' : $fd($b['taken']) . ' taken'; ?></span></li>
          <?php } ?>
          <?php foreach ($leaves as $l) { ?>
            <li><span><?php echo $h($l['leave_type']); ?><span class="me-sub"><?php echo $h(date('d M', strtotime($l['from_date'])) . ($l['to_date'] !== $l['from_date'] ? ' - ' . date('d M', strtotime($l['to_date'])) : '')); ?></span></span><span class="chip <?php echo array('Pending' => 'w', 'Approved' => 'ok', 'Rejected' => 'x', 'Cancelled' => 'na')[$l['status']] ?? 'na'; ?>"><?php echo $h($l['status']); ?></span></li>
          <?php } ?>
        </ul></div>
      </div>

      <div class="sx-card" id="pay">
        <div class="sx-card-h"><h3><i class="fa fa-money"></i> My payslips</h3></div>
        <div class="sx-card-b">
          <?php if (!$slips) { ?><div class="sx-empty">Your payslips appear here once payroll is approved.</div><?php } else { ?>
          <ul class="me-list"><?php foreach ($slips as $s) { ?>
            <li><span><b style="font-weight:600;"><?php echo $h(date('F Y', strtotime($s['month'] . '-01'))); ?></b><span class="me-sub"><?php echo $s['status'] === 'Paid' ? 'Paid' : 'Approved, payment on the way'; ?></span></span>
            <span class="nw"><span class="mono"><?php echo $cur . ' ' . $money($s['net']); ?></span> <a class="btn btn-default btn-sm" href="<?php echo $base; ?>payslip/<?php echo (int)$s['id']; ?>" target="_blank" rel="noopener"><i class="fa fa-file-text-o"></i> View</a></span></li>
          <?php } ?></ul>
          <?php } ?>
          <?php foreach ($loans as $l) { ?><p class="sx-help" style="margin:10px 0 0;"><i class="fa fa-credit-card"></i> <?php echo $h($l['kind']); ?>: <?php echo $cur . ' ' . $money(max(0, $l['amount'] - $l['repaid'])); ?> still to repay, <?php echo $money($l['monthly']); ?> a month.</p><?php } ?>
        </div>
      </div>

      <div class="sx-card" id="letters">
        <div class="sx-card-h"><h3><i class="fa fa-envelope-o"></i> My letters</h3><div class="sx-tools"><button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#meLetter"><i class="fa fa-plus"></i> Ask</button></div></div>
        <div class="sx-card-b">
          <?php if (!$letters) { ?><div class="sx-empty">Need a salary certificate, an NOC or an employment letter? Ask here and HR will issue it.</div><?php } else { ?>
          <ul class="me-list"><?php foreach ($letters as $l) { ?>
            <li><span><?php echo $h($letter_types[$l['type']]); ?><span class="me-sub"><?php echo $h(date('d M Y', strtotime($l['created_at']))); ?><?php echo $l['status'] === 'Rejected' && $l['note'] ? ': ' . $h($l['note']) : ''; ?></span></span>
            <span class="nw"><?php if ($l['status'] === 'Issued') { ?><a class="btn btn-default btn-sm" href="<?php echo $base; ?>letter/<?php echo (int)$l['id']; ?>" target="_blank" rel="noopener"><i class="fa fa-download"></i> Open</a><?php } else { ?><span class="chip <?php echo $letter_chip[$l['status']]; ?>"><?php echo $l['status'] === 'Requested' ? 'With HR' : $h($l['status']); ?></span><?php } ?></span></li>
          <?php } ?></ul>
          <?php } ?>
        </div>
      </div>

      <div class="sx-card" id="reviews">
        <div class="sx-card-h"><h3><i class="fa fa-star-o"></i> My reviews</h3></div>
        <div class="sx-card-b">
          <?php if (!$reviews && !$team_reviews) { ?><div class="sx-empty">No performance reviews yet.</div><?php } ?>
          <?php if ($reviews) { ?><ul class="me-list"><?php foreach ($reviews as $r) { $st = $r['stage'] ?: 'Completed'; ?>
            <li><span><?php echo $h($r['cycle_name']); ?><span class="me-sub"><?php echo $st === 'Self review' ? 'Your self review is needed' . ($r['due_date'] ? ' by ' . $date($r['due_date']) : '') : ($st === 'Manager review' ? 'With ' . $h($r['reviewer_name']) : 'Rating ' . $fd($r['rating']) . ' / 5' . ($r['ack_at'] ? '' : ' · please confirm you have read it')); ?></span></span>
            <a class="btn btn-<?php echo $st === 'Self review' || ($st === 'Completed' && !$r['ack_at']) ? 'primary' : 'default'; ?> btn-sm" href="<?php echo $base; ?>review/<?php echo (int)$r['id']; ?>"><?php echo $st === 'Self review' ? 'Start' : 'Open'; ?></a></li>
          <?php } ?></ul><?php } ?>
          <?php if ($team_reviews) { ?><span class="sx-label" style="margin-top:12px;">My team</span><ul class="me-list"><?php foreach ($team_reviews as $r) { ?>
            <li><span><?php echo $h($r['name']); ?><span class="me-sub"><?php echo $h($r['cycle_name']); ?> · <?php echo $r['stage'] === 'Self review' ? 'waiting for their self review' : 'your review is needed' . ($r['due_date'] ? ' by ' . $date($r['due_date']) : ''); ?></span></span>
            <a class="btn btn-<?php echo $r['stage'] === 'Manager review' ? 'primary' : 'default'; ?> btn-sm" href="<?php echo $base; ?>review/<?php echo (int)$r['id']; ?>">Open</a></li>
          <?php } ?></ul><?php } ?>
        </div>
      </div>
    </div>
  </section>
</div>

<div class="modal fade" id="meLetter" tabindex="-1" role="dialog" aria-labelledby="meLetterT">
  <div class="modal-dialog" role="document"><form class="modal-content" method="post" action="<?php echo $base; ?>letter_request">
    <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="meLetterT">Ask HR for a letter</h4></div>
    <div class="modal-body sx">
      <div class="form-group"><label for="ml1">Letter</label><select id="ml1" name="type" class="form-control"><?php foreach ($letter_types as $k => $v) { ?><option value="<?php echo $k; ?>"><?php echo $h($v); ?></option><?php } ?></select></div>
      <div class="form-group"><label for="ml2">Addressed to</label><input id="ml2" name="addressed_to" class="form-control" maxlength="150" placeholder="e.g. The Manager, Emirates NBD (empty: to whom it may concern)"></div>
      <div class="form-group"><label for="ml3">What it is for</label><input id="ml3" name="purpose" class="form-control" maxlength="255" placeholder="e.g. opening a bank account; travelling to India from 10 to 20 Dec"></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Send to HR</button></div>
  </form></div>
</div>
<script>
$(function () {
  $('.meClock').on('click', function () {
    var b = $(this), type = b.data('t'), txt = b.html();
    b.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Getting location...');
    function send(pos) {
      $.post('<?php echo $base; ?>ajax_clock', { type: type, latitude: pos.coords.latitude, longitude: pos.coords.longitude, accuracy: pos.coords.accuracy }, null, 'json')
        .done(function (r) { alert(r && r.message ? r.message : 'Done.'); if (r && r.status) location.reload(); else b.prop('disabled', false).html(txt); })
        .fail(function () { alert('Could not save. Please try again.'); b.prop('disabled', false).html(txt); });
    }
    if (!navigator.geolocation) { alert('This browser cannot share your location, so you cannot clock in here.'); b.prop('disabled', false).html(txt); return; }
    navigator.geolocation.getCurrentPosition(send, function (err) { alert('Your location could not be read (' + err.message + '). Allow location access and try again.'); b.prop('disabled', false).html(txt); }, { enableHighAccuracy: true, timeout: 15000 });
  });
  $('.meTick').on('change', function () {
    var c = $(this), li = c.closest('li');
    $.post('<?php echo $base; ?>checklist_tick', { id: c.data('id'), done: c.is(':checked') ? 1 : 0 }, null, 'json').done(function (r) {
      if (!r || !r.ok) { alert(r && r.message ? r.message : 'Could not save.'); c.prop('checked', !c.is(':checked')); return; }
      li.toggleClass('done', c.is(':checked')); $('#meClLeft').text(r.left + ' to do');
    }).fail(function () { alert('Could not save.'); c.prop('checked', !c.is(':checked')); });
  });
});
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
