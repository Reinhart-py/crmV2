<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$fd = function ($d) { $d = (float)$d; return ((floor($d) == $d) ? (int)$d : rtrim(rtrim(number_format($d, 1), '0'), '.')); };
$st_chip = array('Pending' => 'w', 'Approved' => 'ok', 'Rejected' => 'x', 'Cancelled' => 'na');
$show_who = $see_all || $has_team;
$counts = array('Pending' => 0, 'Approved' => 0, 'Rejected' => 0, 'Cancelled' => 0);
foreach ($rows as $r) if (isset($counts[$r['status']])) $counts[$r['status']]++;
$type_js = array();
foreach ($types as $n => $t) $type_js[$n] = array('mode' => $t['count_mode'], 'doc' => (int)$t['needs_document'], 'note' => $t['note'], 'pay' => isset($pay_labels[$t['pay']]) ? $pay_labels[$t['pay']] : '');
$bal_js = array();
foreach ($my_bal as $n => $b) $bal_js[$n] = $b['left'];
$start_filter = $pending_n ? 'Pending' : '';
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_8">
<style>
  .lv-bal { display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 10px; }
  @media (max-width: 480px) { .lv-bal { grid-template-columns: 1fr 1fr; gap: 8px; } }
  .lv-bal div { border: 1px solid var(--crm-line, #e2e8f0); border-radius: 10px; padding: 10px 12px; }
  .lv-bal b { display: block; font-size: 20px; line-height: 1.2; }
  .lv-bal span { font-size: 13px; font-weight: 600; }
  .lv-bal small { display: block; font-size: 12px; color: var(--crm-muted, #64748b); }
  .lv-sub { display: block; font-size: 12px; color: var(--crm-muted, #64748b); }
  .lv-days { font-size: 14px; padding: 10px 12px; border-radius: 10px; background: var(--crm-ocean-soft, rgba(30, 58, 138, .06)); margin: 4px 0 12px; }
  .lv-days.bad { background: rgba(220, 38, 38, .08); color: #b91c1c; }
</style>
<div class="content-wrapper sx sx-hr">
  <?php echo crm_tabbar('hr_time', 'hr/leaves'); ?>
  <section class="sx-head">
    <h1><?php echo $see_all ? 'Leave Requests' : 'My Holiday Requests'; ?></h1>
    <div class="sx-btns"><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#lvApply"><i class="fa fa-plus"></i> <?php echo $is_hr ? 'Request or record leave' : 'Request leave'; ?></button></div>
  </section>
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="sx-card">
      <div class="sx-card-h"><h3><i class="fa fa-pie-chart"></i> My leave in <?php echo date('Y'); ?></h3></div>
      <div class="sx-card-b">
        <div class="lv-bal">
          <?php $shown = 0; foreach ($my_bal as $n => $b) { if ($b['entitled'] === null && !$b['taken'] && !$b['pending']) continue; $shown++; ?>
            <div>
              <span><?php echo $h($n); ?></span>
              <?php if ($b['left'] !== null) { ?><b><?php echo $fd($b['left']); ?></b><small>days left of <?php echo $fd($b['entitled'] + $b['adjust']); ?></small>
              <?php } else { ?><b><?php echo $fd($b['taken']); ?></b><small>days taken (no fixed limit)</small><?php } ?>
              <?php if ($b['pending']) { ?><small><?php echo $fd($b['pending']); ?> waiting for approval</small><?php } ?>
            </div>
          <?php } if (!$shown) { ?><div class="sx-empty" style="grid-column:1/-1;">No leave balances yet.</div><?php } ?>
        </div>
      </div>
    </div>

    <div class="sx-card">
      <div class="sx-bar">
        <div class="sx-chips" id="lvChips">
          <button type="button" data-f="Pending" class="<?php echo $start_filter === 'Pending' ? 'is-on' : ''; ?>">Pending <span class="n"><?php echo $counts['Pending']; ?></span></button>
          <button type="button" data-f="Approved">Approved <span class="n"><?php echo $counts['Approved']; ?></span></button>
          <button type="button" data-f="Rejected">Rejected <span class="n"><?php echo $counts['Rejected']; ?></span></button>
          <button type="button" data-f="Cancelled">Cancelled <span class="n"><?php echo $counts['Cancelled']; ?></span></button>
          <button type="button" data-f="" class="<?php echo $start_filter === '' ? 'is-on' : ''; ?>">All <span class="n"><?php echo count($rows); ?></span></button>
        </div>
        <?php if ($show_who) { ?><input type="search" class="form-control sx-q" id="lvQ" placeholder="Employee" autocomplete="off"><?php } ?>
        <select class="form-control" id="lvType" aria-label="Leave type"><option value="">All types</option><?php foreach (array_keys($types) as $n) { ?><option><?php echo $h($n); ?></option><?php } ?></select>
        <span class="sx-count" id="lvCount"></span>
      </div>
      <?php if ($pending_n) { ?><div class="sx-card-b" style="padding-bottom:0;"><div class="sx-help" style="margin:0;"><i class="fa fa-bell-o"></i> <?php echo $pending_n; ?> request<?php echo $pending_n === 1 ? ' is' : 's are'; ?> waiting for your decision.</div></div><?php } ?>
      <div class="sx-table-wrap">
        <table class="sx-table sx-cards" id="lvTable">
          <thead><tr><?php if ($show_who) { ?><th>Employee</th><?php } ?><th>Leave</th><th>Dates</th><th>Days</th><th>Reason</th><th>Status</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($rows as $r) { ?>
            <tr data-s="<?php echo $h($r['status']); ?>" data-t="<?php echo $h($r['leave_type']); ?>" data-q="<?php echo $h(strtolower($r['employee_name'] . ' ' . $r['emp_id'])); ?>">
              <?php if ($show_who) { ?><td data-l="Employee"><b><?php echo $h($r['employee_name']); ?></b><?php echo (int)$r['user_id'] === $me ? ' <span class="chip">You</span>' : ''; ?><span class="lv-sub"><?php echo $h($r['department']); ?></span></td><?php } ?>
              <td data-l="Leave"><?php echo $h($r['leave_type']); ?></td>
              <td data-l="Dates" class="nw"><?php echo $h(date('d M Y', strtotime($r['from_date']))); ?><?php if ($r['to_date'] !== $r['from_date']) { ?><span class="lv-sub">to <?php echo $h(date('d M Y', strtotime($r['to_date']))); ?></span><?php } ?></td>
              <td data-l="Days"><b><?php echo $fd($r['total_days']); ?></b></td>
              <td data-l="Reason" class="full"><?php echo $h($r['reason']); ?></td>
              <td data-l="Status"><span class="chip <?php echo isset($st_chip[$r['status']]) ? $st_chip[$r['status']] : 'na'; ?>"><?php echo $h($r['status']); ?></span>
                <?php if ($r['status'] !== 'Pending' && ($r['approver_name'] || $r['action_note'])) { ?><span class="lv-sub"><?php echo $h(trim(($r['approver_name'] ? 'by ' . $r['approver_name'] : '') . ($r['action_note'] ? ': ' . $r['action_note'] : ''), ': ')); ?></span><?php } ?>
                <span class="lv-sub">Asked <?php echo $h(date('d M', strtotime($r['created_at']))); ?></span></td>
              <td data-l="" class="nw">
                <?php if ($r['can_decide']) { ?>
                  <button type="button" class="btn btn-success btn-sm lvAct" data-a="approve" data-id="<?php echo (int)$r['id']; ?>" data-n="<?php echo $h($r['employee_name'] . ': ' . $r['leave_type'] . ', ' . $fd($r['total_days']) . ' days'); ?>"><i class="fa fa-check"></i> Approve</button>
                  <button type="button" class="btn btn-default btn-sm lvAct" data-a="reject" data-id="<?php echo (int)$r['id']; ?>" data-n="<?php echo $h($r['employee_name'] . ': ' . $r['leave_type'] . ', ' . $fd($r['total_days']) . ' days'); ?>"><i class="fa fa-times"></i> Reject</button>
                <?php } ?>
                <?php if ($r['can_cancel']) { ?>
                  <form method="post" action="<?php echo base_url(); ?>manager/hr/cancel_leave/<?php echo (int)$r['id']; ?>" class="lvCancel" style="display:inline;margin:0;"><input type="hidden" name="return" value="manager/hr/leaves"><button type="submit" class="btn btn-default btn-sm"><i class="fa fa-ban"></i> Cancel</button></form>
                <?php } ?>
              </td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
        <div class="sx-empty" id="lvNone" style="display:none;"><i class="fa fa-calendar-o"></i> No requests here.</div>
      </div>
    </div>
  </section>
</div>

<div class="modal fade" id="lvApply" tabindex="-1" role="dialog" aria-labelledby="lvApplyT">
  <div class="modal-dialog" role="document"><form class="modal-content" method="post" action="<?php echo base_url(); ?>manager/hr/leaves/apply" id="lvForm">
    <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="lvApplyT"><?php echo $is_hr ? 'Request or record leave' : 'Request leave'; ?></h4></div>
    <div class="modal-body sx">
      <input type="hidden" name="return" value="manager/hr/leaves">
      <?php if ($is_hr) { ?>
      <div class="form-group"><label for="la0">For</label><select id="la0" name="user_id" class="form-control"><option value="<?php echo (int)$me; ?>">Myself</option><?php foreach ($people as $p) if ((int)$p['id'] !== (int)$me) { ?><option value="<?php echo (int)$p['id']; ?>"><?php echo $h($p['name']); ?></option><?php } ?></select></div>
      <?php } ?>
      <div class="form-group"><label for="la1">Leave type</label><select id="la1" name="leave_type" class="form-control" required><?php foreach ($types as $n => $t) { ?><option value="<?php echo $h($n); ?>"><?php echo $h($n); ?><?php echo isset($bal_js[$n]) && $bal_js[$n] !== null ? ' (' . $fd($bal_js[$n]) . ' left)' : ''; ?></option><?php } ?></select><p class="help-block" id="laNote" style="font-size:12px;margin:4px 0 0;color:#64748b;"></p></div>
      <div class="sx-grid" style="grid-template-columns:1fr 1fr;">
        <div class="form-group"><label for="la2">From</label><input id="la2" type="date" name="from_date" class="form-control" required></div>
        <div class="form-group"><label for="la3">To</label><input id="la3" type="date" name="to_date" class="form-control" required></div>
      </div>
      <div class="checkbox" id="laHalfW" style="display:none;"><label style="font-weight:500;"><input type="checkbox" name="half_day" value="1" id="laHalf"> Half day</label></div>
      <div class="lv-days" id="laDays" style="display:none;"></div>
      <div class="form-group"><label for="la4">Reason</label><textarea id="la4" name="reason" class="form-control" rows="2" maxlength="1000"></textarea></div>
      <?php if ($is_hr) { ?><div class="checkbox" id="laNowW" style="display:none;"><label style="font-weight:500;"><input type="checkbox" name="approve_now" value="1"> Approve straight away (leave already agreed)</label></div><?php } ?>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary" id="laGo">Send request</button></div>
  </form></div>
</div>

<div class="modal fade" id="lvDecide" tabindex="-1" role="dialog" aria-labelledby="lvDecideT">
  <div class="modal-dialog modal-sm" role="document"><form class="modal-content" method="post" id="lvDecideF">
    <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="lvDecideT"></h4></div>
    <div class="modal-body sx"><p id="lvDecideN" style="font-weight:600;"></p><div class="form-group"><label for="ld1" id="ld1L">Note to the employee</label><input id="ld1" name="note" class="form-control" maxlength="255"></div></div>
    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><button type="submit" class="btn" id="lvDecideGo"></button></div>
  </form></div>
</div>

<script>
$(function () {
  var TYPES = <?php echo json_encode($type_js); ?>, BAL = <?php echo json_encode($bal_js); ?>;
  var HOL = <?php echo json_encode((object)$holidays); ?>, WE = <?php echo json_encode(array_values($weekend)); ?>, ME = <?php echo (int)$me; ?>;
  var DN = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
  var rows = [].slice.call(document.querySelectorAll('#lvTable tbody tr')), f = <?php echo json_encode($start_filter); ?>;
  function run() {
    var s = ($('#lvQ').val() || '').trim().toLowerCase(), t = $('#lvType').val(), n = 0;
    rows.forEach(function (r) {
      var on = (!f || r.getAttribute('data-s') === f) && (!t || r.getAttribute('data-t') === t) && (!s || r.getAttribute('data-q').indexOf(s) !== -1);
      r.style.display = on ? '' : 'none'; if (on) n++;
    });
    $('#lvCount').text(n + (n === 1 ? ' request' : ' requests')); $('#lvNone').toggle(n === 0);
  }
  $('#lvChips').on('click', 'button', function () { f = $(this).data('f'); $('#lvChips button').removeClass('is-on'); $(this).addClass('is-on'); run(); });
  $('#lvQ').on('input', run); $('#lvType').on('change', run); run();

  function ymd(d) { return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2); }
  function calc() {
    var t = $('#la1').val(), a = $('#la2').val(), b = $('#la3').val(), info = TYPES[t] || {};
    $('#laNote').text([info.pay, info.note, info.doc ? 'Bring a supporting document.' : ''].filter(Boolean).join(' · '));
    var forMe = !$('#la0').length || +$('#la0').val() === ME;
    $('#laNowW').toggle(!forMe);
    $('#laHalfW').toggle(!!a && a === b);
    if (!a || !b) { $('#laDays').hide(); return; }
    if (b < a) { $('#laDays').addClass('bad').text('The end date is before the start date.').show(); $('#laGo').prop('disabled', true); return; }
    var n = 0, skipped = 0;
    for (var d = new Date(a + 'T00:00:00'); ymd(d) <= b; d.setDate(d.getDate() + 1)) {
      if (info.mode === 'working' && (WE.indexOf(DN[d.getDay()]) !== -1 || HOL[ymd(d)])) { skipped++; continue; }
      n++;
    }
    if ($('#laHalf').is(':checked') && a === b) n = 0.5;
    var left = forMe ? BAL[t] : undefined, msg = n + (n === 1 ? ' day' : ' days') + (skipped ? ' (weekend and public holidays not counted)' : ''), bad = n === 0;
    if (left !== undefined && left !== null) { msg += ' · ' + left + ' left'; if (n > left) { msg += ' · not enough'; bad = true; } }
    $('#laDays').toggleClass('bad', bad).text(msg).show();
    $('#laGo').prop('disabled', n === 0 || (bad && !$('#la0').length));
  }
  $('#lvForm').on('change input', 'select, input', calc); calc();
  $('#lvForm').on('submit', function () { $('#laGo').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...'); });

  $(document).on('click', '.lvAct', function () {
    var a = $(this).data('a');
    $('#lvDecideF').attr('action', '<?php echo base_url(); ?>manager/hr/leave_action/' + $(this).data('id') + '/' + a);
    $('#lvDecideT').text(a === 'approve' ? 'Approve leave' : 'Reject leave');
    $('#lvDecideN').text($(this).data('n'));
    $('#ld1').val('').prop('required', a === 'reject');
    $('#ld1L').text(a === 'reject' ? 'Reason (sent to the employee)' : 'Note to the employee (optional)');
    $('#lvDecideGo').attr('class', 'btn ' + (a === 'approve' ? 'btn-success' : 'btn-danger')).text(a === 'approve' ? 'Approve' : 'Reject');
    $('#lvDecide').modal('show');
  });
  $('.lvCancel').on('submit', function (ev) { if (!confirm('Cancel this leave?')) ev.preventDefault(); });
});
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
