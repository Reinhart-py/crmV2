<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$chip = array('Requested' => 'w', 'Issued' => 'ok', 'Rejected' => 'x');
$n_req = 0;
foreach ($rows as $r) if ($r['status'] === 'Requested') $n_req++;
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_8">
<div class="content-wrapper sx sx-hr">
  <?php echo crm_tabbar('hr_people', 'hr/letters'); ?>
  <section class="sx-head">
    <h1>Letters</h1>
    <?php if ($can_edit) { ?><div class="sx-btns"><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ltNew"><i class="fa fa-plus"></i> New letter</button></div><?php } ?>
  </section>
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>
    <div class="sx-card">
      <div class="sx-bar">
        <div class="sx-chips" id="ltChips">
          <button type="button" data-f="Requested" class="<?php echo $n_req ? 'is-on' : ''; ?>">To issue <span class="n"><?php echo $n_req; ?></span></button>
          <button type="button" data-f="" class="<?php echo $n_req ? '' : 'is-on'; ?>">All <span class="n"><?php echo count($rows); ?></span></button>
        </div>
        <input type="search" class="form-control sx-q" id="ltQ" placeholder="Employee" autocomplete="off">
        <span class="sx-count" id="ltCount"></span>
      </div>
      <div class="sx-table-wrap">
        <table class="sx-table sx-cards" id="ltTable">
          <thead><tr><th>Employee</th><th>Letter</th><th>For</th><th>Asked</th><th>Status</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($rows as $r) { ?>
            <tr data-s="<?php echo $h($r['status']); ?>" data-q="<?php echo $h(strtolower($r['name'] . ' ' . $r['emp_id'])); ?>">
              <td data-l="Employee"><b><?php echo $h($r['name']); ?></b></td>
              <td data-l="Letter"><?php echo $h($types[$r['type']]); ?><?php if ($r['ref_no']) { ?><span class="text-muted" style="display:block;font-size:12px;"><?php echo $h($r['ref_no']); ?></span><?php } ?></td>
              <td data-l="For"><?php echo $h($r['purpose'] ?: '-'); ?><?php if ($r['addressed_to']) { ?><span class="text-muted" style="display:block;font-size:12px;">To: <?php echo $h($r['addressed_to']); ?></span><?php } ?></td>
              <td data-l="Asked" class="nw"><?php echo $h(date('d M Y', strtotime($r['created_at']))); ?></td>
              <td data-l="Status"><span class="chip <?php echo $chip[$r['status']]; ?>"><?php echo $r['status'] === 'Requested' ? 'To issue' : $h($r['status']); ?></span><?php if ($r['issued_name'] && $r['status'] !== 'Requested') { ?><span class="text-muted" style="display:block;font-size:12px;">by <?php echo $h($r['issued_name']); ?></span><?php } ?></td>
              <td data-l="" class="nw"><a class="btn btn-<?php echo $r['status'] === 'Requested' && $can_edit ? 'primary' : 'default'; ?> btn-sm" href="<?php echo base_url(); ?>manager/hr/letter/<?php echo (int)$r['id']; ?>"><?php echo $r['status'] === 'Requested' && $can_edit ? '<i class="fa fa-pencil"></i> Issue' : '<i class="fa fa-eye"></i> Open'; ?></a></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
        <div class="sx-empty" id="ltNone" style="display:none;">Nothing here.</div>
      </div>
    </div>
    <p class="sx-help"><i class="fa fa-info-circle"></i> Staff ask for letters from My HR (profile menu). The text is filled in from the HR file; you can change it before issuing. Company name comes from HR Settings.</p>
  </section>
</div>
<?php if ($can_edit) { ?>
<div class="modal fade" id="ltNew" tabindex="-1" role="dialog" aria-labelledby="ltNewT">
  <div class="modal-dialog" role="document"><form class="modal-content" method="post" action="<?php echo base_url(); ?>manager/hr/letter_new">
    <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="ltNewT">New letter</h4></div>
    <div class="modal-body sx">
      <div class="form-group"><label for="ln1">Employee</label><select id="ln1" name="user_id" class="form-control" required><option value="">Choose</option><?php foreach ($people as $p) { ?><option value="<?php echo (int)$p['id']; ?>"><?php echo $h($p['name']); ?></option><?php } ?></select></div>
      <div class="form-group"><label for="ln2">Letter</label><select id="ln2" name="type" class="form-control"><?php foreach ($types as $k => $v) { ?><option value="<?php echo $k; ?>"><?php echo $h($v); ?></option><?php } ?></select></div>
      <div class="form-group"><label for="ln3">Addressed to</label><input id="ln3" name="addressed_to" class="form-control" maxlength="150" placeholder="Empty: to whom it may concern"></div>
      <div class="form-group"><label for="ln4">What it is for</label><input id="ln4" name="purpose" class="form-control" maxlength="255"></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Write it</button></div>
  </form></div>
</div>
<?php } ?>
<script>
$(function () {
  var rows = [].slice.call(document.querySelectorAll('#ltTable tbody tr')), f = $('#ltChips .is-on').data('f') || '';
  function run() {
    var s = $('#ltQ').val().trim().toLowerCase(), n = 0;
    rows.forEach(function (r) { var on = (!f || r.getAttribute('data-s') === f) && (!s || r.getAttribute('data-q').indexOf(s) !== -1); r.style.display = on ? '' : 'none'; if (on) n++; });
    $('#ltCount').text(n + (n === 1 ? ' letter' : ' letters')); $('#ltNone').toggle(n === 0);
  }
  $('#ltChips button').on('click', function () { $('#ltChips button').removeClass('is-on'); $(this).addClass('is-on'); f = $(this).data('f') || ''; run(); });
  $('#ltQ').on('input', run); run();
});
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
