<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$fd = function ($d) { $d = (float)$d; return ((floor($d) == $d) ? (int)$d : rtrim(rtrim(number_format($d, 1), '0'), '.')); };
$chip = array('Self review' => 'w', 'Manager review' => 'on', 'Completed' => 'ok');
$cnt = array('Self review' => 0, 'Manager review' => 0, 'Completed' => 0);
foreach ($rows as &$r) { $r['stage'] = $r['stage'] ?: 'Completed'; $cnt[$r['stage']]++; } unset($r);
$q = date('n') <= 6 ? 'H1 ' . date('Y') : 'H2 ' . date('Y');
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_8">
<style>
  .rv-stars { color: #f59e0b; letter-spacing: 1px; white-space: nowrap; }
  .rv-bar { height: 6px; border-radius: 3px; background: var(--crm-line, #e2e8f0); overflow: hidden; margin-top: 5px; max-width: 160px; }
  .rv-bar i { display: block; height: 100%; background: #16a34a; }
  .rv-sub { display: block; font-size: 12px; color: var(--crm-muted, #64748b); }
  #rvPick { max-height: 220px; overflow: auto; border: 1px solid var(--crm-line, #e2e8f0); border-radius: 8px; padding: 6px 10px; }
  #rvPick label { display: block; font-weight: 500; margin: 3px 0; }
</style>
<div class="content-wrapper sx sx-hr">
  <?php echo crm_tabbar('hr_perf', 'hr/appraisals'); ?>
  <section class="sx-head">
    <h1>Performance Reviews</h1>
    <?php if ($can_edit) { ?><div class="sx-btns"><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#rvNew"><i class="fa fa-play"></i> Start a review round</button></div><?php } ?>
  </section>
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <?php if ($cycles) { ?>
    <div class="sx-card">
      <div class="sx-card-h"><h3><i class="fa fa-refresh"></i> Review rounds</h3></div>
      <div class="sx-table-wrap"><table class="sx-table sx-cards">
        <thead><tr><th>Round</th><th>Period</th><th>Due</th><th>Progress</th><th>Status</th><?php if ($can_edit) { ?><th></th><?php } ?></tr></thead>
        <tbody><?php foreach ($cycles as $c) { $pc = $c['n'] ? round(100 * $c['done'] / $c['n']) : 0; ?>
          <tr><td data-l="Round"><b><?php echo $h($c['name']); ?></b></td>
          <td data-l="Period" class="nw"><?php echo $c['period_from'] ? $h(date('d M Y', strtotime($c['period_from'])) . ' - ' . date('d M Y', strtotime($c['period_to']))) : '-'; ?></td>
          <td data-l="Due" class="nw"><?php echo $c['due_date'] ? $h(date('d M Y', strtotime($c['due_date']))) : '-'; ?></td>
          <td data-l="Progress"><?php echo (int)$c['done']; ?> of <?php echo (int)$c['n']; ?> finished<div class="rv-bar" aria-hidden="true"><i style="width:<?php echo $pc; ?>%"></i></div></td>
          <td data-l="Status"><span class="chip <?php echo $c['status'] === 'Open' ? 'on' : 'na'; ?>"><?php echo $h($c['status']); ?></span></td>
          <?php if ($can_edit) { ?><td data-l="" class="nw"><?php if ($c['status'] === 'Open') { ?><form method="post" action="<?php echo base_url(); ?>manager/hr/review_cycle_close/<?php echo (int)$c['id']; ?>" onsubmit="return confirm('Close this round? Unfinished reviews stay as they are.');"><button type="submit" class="btn btn-default btn-sm">Close</button></form><?php } ?></td><?php } ?></tr>
        <?php } ?></tbody>
      </table></div>
    </div>
    <?php } ?>

    <div class="sx-card">
      <div class="sx-bar">
        <div class="sx-chips" id="rvChips">
          <button type="button" data-f="Self review">Self review <span class="n"><?php echo $cnt['Self review']; ?></span></button>
          <button type="button" data-f="Manager review">Manager review <span class="n"><?php echo $cnt['Manager review']; ?></span></button>
          <button type="button" data-f="Completed">Completed <span class="n"><?php echo $cnt['Completed']; ?></span></button>
          <button type="button" data-f="" class="is-on">All <span class="n"><?php echo count($rows); ?></span></button>
        </div>
        <input type="search" class="form-control sx-q" id="rvQ" placeholder="Employee or round" autocomplete="off">
        <span class="sx-count" id="rvCount"></span>
      </div>
      <div class="sx-table-wrap">
        <table class="sx-table sx-cards" id="rvTable">
          <thead><tr><th>Employee</th><th>Round</th><th>Reviewer</th><th>Stage</th><th>Rating</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($rows as $r) { ?>
            <tr data-s="<?php echo $h($r['stage']); ?>" data-q="<?php echo $h(strtolower($r['name'] . ' ' . $r['cycle_name'] . ' ' . $r['reviewer_name'])); ?>">
              <td data-l="Employee"><b><?php echo $h($r['name']); ?></b><span class="rv-sub"><?php echo $h($r['department']); ?></span></td>
              <td data-l="Round"><?php echo $h($r['cycle_name']); ?></td>
              <td data-l="Reviewer"><?php echo $h($r['reviewer_name'] ?: '-'); ?></td>
              <td data-l="Stage"><span class="chip <?php echo $chip[$r['stage']]; ?>"><?php echo $h($r['stage']); ?></span><?php if ($r['stage'] === 'Completed' && $r['cycle_id']) { ?><span class="rv-sub"><?php echo $r['ack_at'] ? 'Read by employee' : 'Not read yet'; ?></span><?php } ?></td>
              <td data-l="Rating"><?php if ((float)$r['rating'] > 0) { ?><span class="rv-stars" aria-label="<?php echo $fd($r['rating']); ?> out of 5"><?php $st = (int)round((float)$r['rating']); echo str_repeat('&#9733;', $st) . '<span style="color:#cbd5e1">' . str_repeat('&#9733;', 5 - $st) . '</span>'; ?></span> <?php echo $fd($r['rating']); ?><?php } else { ?><span class="text-muted">-</span><?php } ?></td>
              <td data-l="" class="nw"><a class="btn btn-default btn-sm" href="<?php echo base_url(); ?>manager/hr/review/<?php echo (int)$r['id']; ?>"><i class="fa fa-folder-open-o"></i> Open</a></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
        <div class="sx-empty" id="rvNone" style="display:none;">No reviews here.</div>
      </div>
    </div>
  </section>
</div>

<?php if ($can_edit) { ?>
<div class="modal fade" id="rvNew" tabindex="-1" role="dialog" aria-labelledby="rvNewT">
  <div class="modal-dialog" role="document"><form class="modal-content" method="post" action="<?php echo base_url(); ?>manager/hr/appraisals/add">
    <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="rvNewT">Start a review round</h4></div>
    <div class="modal-body sx">
      <div class="form-group"><label for="rv1">Name</label><input id="rv1" name="name" class="form-control" maxlength="100" required value="<?php echo $h($q . ' review'); ?>"></div>
      <div class="sx-grid" style="grid-template-columns:1fr 1fr 1fr;">
        <div class="form-group"><label for="rv2">Period from</label><input id="rv2" type="date" name="period_from" class="form-control"></div>
        <div class="form-group"><label for="rv3">to</label><input id="rv3" type="date" name="period_to" class="form-control"></div>
        <div class="form-group"><label for="rv4">Finish by</label><input id="rv4" type="date" name="due_date" class="form-control"></div>
      </div>
      <div class="form-group"><span class="sx-label">Who is reviewed</span>
        <label style="font-weight:500;margin-right:14px;"><input type="radio" name="who" value="all" checked> Everyone (<?php echo count($people); ?>)</label>
        <label style="font-weight:500;margin-right:14px;"><input type="radio" name="who" value="dept"> A department</label>
        <label style="font-weight:500;"><input type="radio" name="who" value="pick"> Choose people</label>
      </div>
      <div class="form-group rv-w" data-w="dept" style="display:none;"><select name="department" class="form-control" aria-label="Department"><?php foreach ($depts as $d) { ?><option><?php echo $h($d); ?></option><?php } ?></select></div>
      <div class="form-group rv-w" data-w="pick" style="display:none;"><input type="search" class="form-control" id="rvPickQ" placeholder="Find" style="margin-bottom:6px;"><div id="rvPick"><?php foreach ($people as $p) { ?><label data-q="<?php echo $h(strtolower($p['name'])); ?>"><input type="checkbox" name="user_ids[]" value="<?php echo (int)$p['id']; ?>"> <?php echo $h($p['name']); ?></label><?php } ?></div></div>
      <p class="help-block">Each person's manager (from the staff profile) reviews them; people without a manager are reviewed by you.</p>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Start</button></div>
  </form></div>
</div>
<?php } ?>
<script>
$(function () {
  var rows = [].slice.call(document.querySelectorAll('#rvTable tbody tr')), f = '';
  function run() {
    var s = $('#rvQ').val().trim().toLowerCase(), n = 0;
    rows.forEach(function (r) { var on = (!f || r.getAttribute('data-s') === f) && (!s || r.getAttribute('data-q').indexOf(s) !== -1); r.style.display = on ? '' : 'none'; if (on) n++; });
    $('#rvCount').text(n + (n === 1 ? ' review' : ' reviews')); $('#rvNone').toggle(n === 0);
  }
  $('#rvChips button').on('click', function () { $('#rvChips button').removeClass('is-on'); $(this).addClass('is-on'); f = $(this).data('f') || ''; run(); });
  $('#rvQ').on('input', run); run();
  $('input[name=who]').on('change', function () { var w = $('input[name=who]:checked').val(); $('.rv-w').each(function () { $(this).toggle($(this).data('w') === w); }); });
  $('#rvPickQ').on('input', function () { var s = this.value.trim().toLowerCase(); $('#rvPick label').each(function () { $(this).toggle(!s || $(this).data('q').indexOf(s) !== -1); }); });
});
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
