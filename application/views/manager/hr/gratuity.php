<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$money = function ($v) { return number_format((float)$v, 2); };
$cur = $h($set['currency']);
$chip = array('Draft' => 'w', 'Approved' => 'on', 'Paid' => 'ok');
$svc = function ($days) { if ($days <= 0) return '-'; $y = floor($days / 365); $m = floor(($days - $y * 365) / 30.4); return ($y ? $y . ' yr ' : '') . $m . ' mo'; };
$no_doj = 0; $no_sal = 0;
foreach ($rows as $r) { if (!$r['doj']) $no_doj++; if ($r['basic'] <= 0) $no_sal++; }
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<style>.gr-sub { display: block; font-size: 12px; color: var(--crm-muted, #64748b); }</style>
<div class="content-wrapper sx">
  <?php echo crm_tabbar('hr_pay', 'hr/taxation'); ?>
  <section class="sx-head">
    <h1>Gratuity &amp; Settlement<small>End-of-service gratuity earned so far by each person (UAE law), and final settlements for people who leave</small></h1>
    <?php if ($can_edit) { ?><div class="sx-btns"><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#grNew"><i class="fa fa-file-text-o"></i> Final settlement</button></div><?php } ?>
  </section>
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>
    <div class="sx-kpis">
      <div class="sx-kpi"><span class="ic"><i class="fa fa-calculator"></i></span><div><b><?php echo $money($total); ?></b><span>Gratuity owed today, <?php echo $cur; ?></span></div></div>
      <div class="sx-kpi s"><span class="ic"><i class="fa fa-users"></i></span><div><b><?php echo count($rows); ?></b><span>Working people</span></div></div>
      <?php if ($no_doj || $no_sal) { ?><div class="sx-kpi a"><span class="ic"><i class="fa fa-exclamation-triangle"></i></span><div><b><?php echo max($no_doj, $no_sal); ?></b><span><?php echo $no_doj ? $no_doj . ' without joining date' : ''; ?><?php echo $no_doj && $no_sal ? ', ' : ''; ?><?php echo $no_sal ? $no_sal . ' without salary' : ''; ?></span></div></div><?php } ?>
    </div>

    <div class="sx-card">
      <div class="sx-card-h"><h3><i class="fa fa-file-text-o"></i> Final settlements</h3></div>
      <?php if (!$settlements) { ?><div class="sx-card-b"><div class="sx-empty">No final settlements yet.</div></div><?php } else { ?>
      <div class="sx-table-wrap"><table class="sx-table sx-cards">
        <thead><tr><th>Employee</th><th>Last day</th><th>Reason</th><th class="text-right">Gratuity</th><th class="text-right">Total</th><th>Status</th><th></th></tr></thead>
        <tbody><?php foreach ($settlements as $st) { ?>
          <tr><td data-l="Employee"><b><?php echo $h($st['name']); ?></b></td><td data-l="Last day" class="nw"><?php echo $h(date('d M Y', strtotime($st['last_day']))); ?></td><td data-l="Reason"><?php echo $h($st['reason']); ?></td>
          <td data-l="Gratuity" class="text-right mono"><?php echo $money($st['gratuity']); ?></td><td data-l="Total" class="text-right mono"><b><?php echo $money($st['total']); ?></b></td>
          <td data-l="Status"><span class="chip <?php echo $chip[$st['status']]; ?>"><?php echo $h($st['status']); ?></span></td>
          <td data-l="" class="nw"><a class="btn btn-default btn-sm" href="<?php echo base_url(); ?>manager/hr/settlement/<?php echo (int)$st['user_id']; ?>?id=<?php echo (int)$st['id']; ?>"><i class="fa fa-folder-open-o"></i> Open</a></td></tr>
        <?php } ?></tbody>
      </table></div>
      <?php } ?>
    </div>

    <div class="sx-card">
      <div class="sx-bar">
        <input type="search" class="form-control sx-q" id="grQ" placeholder="Employee" autocomplete="off">
        <span class="sx-count" id="grCount"></span>
      </div>
      <div class="sx-table-wrap">
        <table class="sx-table sx-cards" id="grTable">
          <thead><tr><th>Employee</th><th>Joined</th><th>Service</th><th class="text-right">Basic pay</th><th class="text-right">Gratuity today</th><?php if ($can_edit) { ?><th></th><?php } ?></tr></thead>
          <tbody>
          <?php foreach ($rows as $r) { ?>
            <tr data-q="<?php echo $h(strtolower($r['name'] . ' ' . $r['emp_id'] . ' ' . $r['department'])); ?>">
              <td data-l="Employee"><b><?php echo $h($r['name']); ?></b><span class="gr-sub"><?php echo $h($r['department']); ?></span></td>
              <td data-l="Joined" class="nw"><?php echo $r['doj'] ? $h(date('d M Y', strtotime($r['doj']))) : '<a href="' . base_url() . 'manager/hr/employees/' . (int)$r['id'] . '#job" class="text-muted">Not set</a>'; ?></td>
              <td data-l="Service"><?php echo $svc($r['g']['days']); ?><?php if ($r['g']['days'] > 0 && $r['g']['years'] < 1) { ?><span class="gr-sub">under 1 year: none yet</span><?php } ?></td>
              <td data-l="Basic pay" class="text-right mono"><?php echo $r['basic'] > 0 ? $money($r['basic']) . ($r['source'] === 'staff' ? '<span class="gr-sub">staff salary</span>' : '') : '<a href="' . base_url() . 'manager/hr/employees/' . (int)$r['id'] . '#salary" class="text-muted">Not set</a>'; ?></td>
              <td data-l="Gratuity today" class="text-right mono"><b><?php echo $money($r['g']['amount']); ?></b></td>
              <?php if ($can_edit) { ?><td data-l="" class="nw"><a class="btn btn-default btn-sm" href="<?php echo base_url(); ?>manager/hr/settlement/<?php echo (int)$r['id']; ?>"><i class="fa fa-calculator"></i> Settle</a></td><?php } ?>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
    <p class="sx-help"><i class="fa fa-info-circle"></i> UAE gratuity: nothing in the first year; then <?php echo $h($set['gratuity_days_first5']); ?> days of basic pay for each of the first 5 years and <?php echo $h($set['gratuity_days_after5']); ?> days for each year after, parts of a year pro rata; unpaid leave does not count as service; at most 2 years of pay. A day's pay is basic / 30. Change the days in HR Settings.</p>
  </section>
</div>

<?php if ($can_edit) { ?>
<div class="modal fade" id="grNew" tabindex="-1" role="dialog" aria-labelledby="grNewT">
  <div class="modal-dialog modal-sm" role="document"><form class="modal-content" method="get" id="grNewF" action="<?php echo base_url(); ?>manager/hr/settlement/0">
    <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="grNewT">Final settlement</h4></div>
    <div class="modal-body sx">
      <div class="form-group"><label for="gr1">Employee</label><select id="gr1" class="form-control" required><option value="">Choose</option><?php foreach ($all_people as $p) { ?><option value="<?php echo (int)$p['id']; ?>"><?php echo $h($p['name']); ?></option><?php } ?></select></div>
      <div class="form-group"><label for="gr2">Last working day</label><input id="gr2" type="date" name="last_day" class="form-control"><p class="help-block">Empty: the leaving date, or today.</p></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Work it out</button></div>
  </form></div>
</div>
<?php } ?>
<script>
$(function () {
  var rows = [].slice.call(document.querySelectorAll('#grTable tbody tr'));
  function run() { var s = $('#grQ').val().trim().toLowerCase(), n = 0; rows.forEach(function (r) { var on = !s || r.getAttribute('data-q').indexOf(s) !== -1; r.style.display = on ? '' : 'none'; if (on) n++; }); $('#grCount').text(n + (n === 1 ? ' person' : ' people')); }
  $('#grQ').on('input', run); run();
  $('#grNewF').on('submit', function () { var id = $('#gr1').val(); if (!id) return false; this.action = '<?php echo base_url(); ?>manager/hr/settlement/' + id; if (!$('#gr2').val()) $('#gr2').prop('disabled', true); });
});
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
