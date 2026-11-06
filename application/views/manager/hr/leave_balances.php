<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$fd = function ($d) { $d = (float)$d; return ((floor($d) == $d) ? (int)$d : rtrim(rtrim(number_format($d, 1), '0'), '.')); };
// Columns: types with a yearly limit, plus types somebody used this year
$cols = array();
foreach ($types as $n => $t) {
  if ((float)$t['days_per_year'] > 0) { $cols[] = $n; continue; }
  foreach ($bal as $b) if (!empty($b[$n]['taken']) || !empty($b[$n]['pending'])) { $cols[] = $n; break; }
}
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_8">
<style>
  .lb-c b { font-size: 14px; }
  .lb-c small { display: block; font-size: 11.5px; color: var(--crm-muted, #64748b); white-space: nowrap; }
  .lb-c.neg b { color: #b91c1c; }
</style>
<div class="content-wrapper sx sx-hr">
  <?php echo crm_tabbar('hr_time', 'hr/leave_balances'); ?>
  <section class="sx-head">
    <h1>Leave Balances<small><?php echo (int)$year; ?></small></h1>
    <div class="sx-btns">
      <form method="get" class="at-pick" style="display:flex;gap:6px;"><select name="year" class="form-control" onchange="this.form.submit()" aria-label="Year"><?php for ($y = (int)date('Y') + 1; $y >= (int)date('Y') - 3; $y--) { ?><option<?php echo $y === (int)$year ? ' selected' : ''; ?>><?php echo $y; ?></option><?php } ?></select></form>
      <?php if ($can_edit) { ?><button type="button" class="btn btn-primary" id="lbAdd"><i class="fa fa-plus"></i> Adjust balance</button><?php } ?>
    </div>
  </section>
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>
    <div class="sx-card">
      <div class="sx-bar">
        <input type="search" class="form-control sx-q" id="lbQ" placeholder="Employee" autocomplete="off">
        <select class="form-control" id="lbDept" aria-label="Department"><option value="">All departments</option><?php foreach ($depts as $d) { ?><option value="<?php echo $h(strtolower($d)); ?>"><?php echo $h($d); ?></option><?php } ?></select>
        <span class="sx-count" id="lbCount"></span>
      </div>
      <div class="sx-table-wrap">
        <table class="sx-table sx-cards" id="lbTable">
          <thead><tr><th>Employee</th><?php foreach ($cols as $c) { ?><th><?php echo $h($c); ?></th><?php } ?><?php if ($can_edit) { ?><th></th><?php } ?></tr></thead>
          <tbody>
          <?php foreach ($people as $u) { $uid = (int)$u['id']; ?>
            <tr data-q="<?php echo $h(strtolower($u['name'] . ' ' . $u['emp_id'])); ?>" data-d="<?php echo $h(strtolower(trim((string)$u['department']))); ?>">
              <td data-l="Employee"><b><?php echo $h($u['name']); ?></b><span class="text-muted" style="display:block;font-size:12px;"><?php echo $u['c_doj'] ? 'Joined ' . $h(date('d-m-Y', strtotime($u['c_doj']))) : 'Joining date not set'; ?></span></td>
              <?php foreach ($cols as $c) { $b = isset($bal[$uid][$c]) ? $bal[$uid][$c] : null; ?>
                <td data-l="<?php echo $h($c); ?>" class="lb-c<?php echo $b && $b['left'] !== null && $b['left'] < 0 ? ' neg' : ''; ?>"><?php
                  if (!$b) echo '<span class="text-muted">n/a</span>';
                  elseif ($b['left'] === null) echo '<b>' . $fd($b['taken']) . '</b><small>taken' . ($b['pending'] ? ', ' . $fd($b['pending']) . ' pending' : '') . '</small>';
                  else echo '<b>' . $fd($b['left']) . '</b><small>of ' . $fd($b['entitled'] + $b['adjust']) . ($b['taken'] ? ', ' . $fd($b['taken']) . ' taken' : '') . ($b['pending'] ? ', ' . $fd($b['pending']) . ' pending' : '') . '</small>';
                ?></td>
              <?php } ?>
              <?php if ($can_edit) { ?><td data-l="" class="nw"><button type="button" class="btn btn-default btn-sm lbAdj" data-u="<?php echo $uid; ?>" aria-label="Adjust balance of <?php echo $h($u['name']); ?>"><i class="fa fa-sliders"></i> Adjust</button></td><?php } ?>
            </tr>
          <?php } ?>
          </tbody>
        </table>
        <div class="sx-empty" id="lbNone" style="display:none;"><i class="fa fa-search"></i> Nobody matches.</div>
      </div>
    </div>

    <div class="sx-card">
      <div class="sx-card-h"><h3><i class="fa fa-sliders"></i> Adjustments in <?php echo (int)$year; ?></h3></div>
      <?php if (!$adjust) { ?><div class="sx-card-b"><div class="sx-empty">No adjustments. Use them for opening balances, days carried forward or corrections.</div></div><?php } else { ?>
      <div class="sx-table-wrap"><table class="sx-table sx-cards">
        <thead><tr><th>Employee</th><th>Leave</th><th>Days</th><th>Reason</th><th>By</th></tr></thead>
        <tbody><?php foreach ($adjust as $a) { ?>
          <tr><td data-l="Employee"><?php echo $h($a['name']); ?></td><td data-l="Leave"><?php echo $h($a['leave_type']); ?></td><td data-l="Days"><b style="color:<?php echo $a['days'] < 0 ? '#b91c1c' : '#15803d'; ?>"><?php echo ($a['days'] > 0 ? '+' : '') . $fd($a['days']); ?></b></td><td data-l="Reason"><?php echo $h($a['reason']); ?></td><td data-l="By"><?php echo $h($a['by_name']); ?><span class="text-muted" style="display:block;font-size:12px;"><?php echo $h(date('d-m-Y', strtotime($a['created_at']))); ?></span></td></tr>
        <?php } ?></tbody>
      </table></div>
      <?php } ?>
    </div>
    <p class="sx-help"><i class="fa fa-info-circle"></i> Annual leave follows UAE law: none in the first 6 months, then 2 days for each month worked, and the full yearly days after one year (30 by default; set per person in the HR file or in HR Settings). Leave years run January to December.</p>
  </section>
</div>

<?php if ($can_edit) { ?>
<div class="modal fade" id="lbModal" tabindex="-1" role="dialog" aria-labelledby="lbModalT">
  <div class="modal-dialog" role="document"><form class="modal-content" method="post" action="<?php echo base_url(); ?>manager/hr/leave_adjust">
    <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="lbModalT">Adjust leave balance (<?php echo (int)$year; ?>)</h4></div>
    <div class="modal-body sx">
      <input type="hidden" name="year" value="<?php echo (int)$year; ?>">
      <div class="form-group"><label for="lb1">Employee</label><select id="lb1" name="user_id" class="form-control" required><option value="">Choose</option><?php foreach ($people as $u) { ?><option value="<?php echo (int)$u['id']; ?>"><?php echo $h($u['name']); ?></option><?php } ?></select></div>
      <div class="form-group"><label for="lb2">Leave type</label><select id="lb2" name="leave_type" class="form-control"><?php foreach ($types as $n => $t) { ?><option><?php echo $h($n); ?></option><?php } ?></select></div>
      <div class="form-group"><label for="lb3">Days</label><input id="lb3" type="number" step="0.5" name="days" class="form-control" required placeholder="e.g. 5 to add, -2 to remove"></div>
      <div class="form-group"><label for="lb4">Reason</label><input id="lb4" name="reason" class="form-control" maxlength="255" required placeholder="e.g. Carried forward from last year"></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
  </form></div>
</div>
<?php } ?>
<script>
$(function () {
  var rows = [].slice.call(document.querySelectorAll('#lbTable tbody tr'));
  function run() {
    var s = $('#lbQ').val().trim().toLowerCase(), d = $('#lbDept').val(), n = 0;
    rows.forEach(function (r) { var on = (!s || r.getAttribute('data-q').indexOf(s) !== -1) && (!d || r.getAttribute('data-d') === d); r.style.display = on ? '' : 'none'; if (on) n++; });
    $('#lbCount').text(n + (n === 1 ? ' person' : ' people')); $('#lbNone').toggle(n === 0);
  }
  $('#lbQ').on('input', run); $('#lbDept').on('change', run); run();
  $('#lbAdd').on('click', function () { $('#lb1').val(''); $('#lbModal').modal('show'); });
  $(document).on('click', '.lbAdj', function () { $('#lb1').val($(this).data('u')); $('#lbModal').modal('show'); });
});
</script>
<script src="<?php echo base_url(); ?>assets/dist/js/crm-pager.js?v=20261106_2"></script>
<script>crmPager('#lbTable');</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
