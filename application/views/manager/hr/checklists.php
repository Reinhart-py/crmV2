<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$kname = array('onboarding' => 'Onboarding', 'offboarding' => 'Exit');
$open = 0;
foreach ($lists as $l) if ($l['status'] === 'Open') $open++;
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_8">
<style>
  .ck-bar { height: 6px; border-radius: 3px; background: var(--crm-line, #e2e8f0); overflow: hidden; margin-top: 5px; max-width: 180px; }
  .ck-bar i { display: block; height: 100%; background: #16a34a; }
  .ck-sub { display: block; font-size: 12px; color: var(--crm-muted, #64748b); }
  .ck-t { display: grid; grid-template-columns: 1fr 120px 90px 36px; gap: 6px; margin-bottom: 6px; }
  @media (max-width: 600px) { .ck-t { grid-template-columns: 1fr 1fr; } .ck-t input:first-child { grid-column: 1 / -1; } }
</style>
<div class="content-wrapper sx sx-hr">
  <?php echo crm_tabbar('hr_people', 'hr/checklists'); ?>
  <section class="sx-head">
    <h1>Onboarding &amp; Exit</h1>
    <?php if ($can_edit) { ?><div class="sx-btns"><button type="button" class="btn btn-default" data-toggle="modal" data-target="#ckTpl"><i class="fa fa-list-ul"></i> Default steps</button><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ckNew"><i class="fa fa-plus"></i> Start checklist</button></div><?php } ?>
  </section>
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>
    <div class="sx-card">
      <?php if (!$lists) { ?><div class="sx-card-b"><div class="sx-empty"><i class="fa fa-check-square-o"></i> No checklists yet. Start one when someone joins or hands in their notice.</div></div><?php } else { ?>
      <div class="sx-table-wrap">
        <table class="sx-table sx-cards">
          <thead><tr><th>Employee</th><th>Checklist</th><th>Date</th><th>Progress</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($lists as $l) { $pc = $l['n'] ? round(100 * $l['done'] / $l['n']) : 0; ?>
            <tr>
              <td data-l="Employee"><b><?php echo $h($l['name']); ?></b><span class="ck-sub"><?php echo $h($l['department']); ?></span></td>
              <td data-l="Checklist"><?php echo $kname[$l['kind']]; ?> <?php if ($l['status'] === 'Done') { ?><span class="chip ok">Done</span><?php } elseif ((int)$l['late']) { ?><span class="chip x"><?php echo (int)$l['late']; ?> late</span><?php } ?></td>
              <td data-l="Date" class="nw"><?php echo $h(date('d M Y', strtotime($l['start_date']))); ?><span class="ck-sub"><?php echo $l['kind'] === 'onboarding' ? 'joining' : 'last day'; ?></span></td>
              <td data-l="Progress"><?php echo (int)$l['done']; ?> of <?php echo (int)$l['n']; ?> steps<div class="ck-bar" aria-hidden="true"><i style="width:<?php echo $pc; ?>%"></i></div></td>
              <td data-l="" class="nw"><a class="btn btn-default btn-sm" href="<?php echo base_url(); ?>manager/hr/checklist/<?php echo (int)$l['id']; ?>"><i class="fa fa-folder-open-o"></i> Open</a></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
      <?php } ?>
    </div>
    <p class="sx-help"><i class="fa fa-info-circle"></i> The employee ticks their own steps in My HR. Change the default steps (UAE: MOHRE contract, visa, Emirates ID, insurance, WPS, cancellations) with Default steps.</p>
  </section>
</div>

<?php if ($can_edit) { ?>
<div class="modal fade" id="ckNew" tabindex="-1" role="dialog" aria-labelledby="ckNewT">
  <div class="modal-dialog" role="document"><form class="modal-content" method="post" action="<?php echo base_url(); ?>manager/hr/checklist_new">
    <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="ckNewT">Start a checklist</h4></div>
    <div class="modal-body sx">
      <div class="form-group"><label for="ck1">Employee</label><select id="ck1" name="user_id" class="form-control" required><option value="">Choose</option><?php foreach ($people as $p) { ?><option value="<?php echo (int)$p['id']; ?>"><?php echo $h($p['name']); ?></option><?php } ?></select></div>
      <div class="form-group"><label for="ck2">Checklist</label><select id="ck2" name="kind" class="form-control"><option value="onboarding">Onboarding (joining)</option><option value="offboarding">Exit (leaving)</option></select></div>
      <div class="form-group"><label for="ck3" id="ck3l">Joining date</label><input id="ck3" type="date" name="start_date" class="form-control"><p class="help-block">Empty: the joining date from the staff profile, or the leaving date.</p></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Start</button></div>
  </form></div>
</div>
<div class="modal fade" id="ckTpl" tabindex="-1" role="dialog" aria-labelledby="ckTplT">
  <div class="modal-dialog modal-lg" role="document"><form class="modal-content" method="post" action="<?php echo base_url(); ?>manager/hr/checklist_template_save">
    <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="ckTplT">Default steps</h4></div>
    <div class="modal-body sx">
      <div class="sx-chips" id="ckKinds" style="margin-bottom:12px;"><button type="button" class="is-on" data-k="onboarding">Onboarding</button><button type="button" data-k="offboarding">Exit</button></div>
      <input type="hidden" name="kind" id="ckKind" value="onboarding">
      <p class="help-block" style="margin-top:0;">Days: from the joining date or the last working day (minus = before).</p>
      <?php foreach ($templates as $k => $rows) { ?>
      <div class="ck-k" data-k="<?php echo $k; ?>"<?php echo $k === 'onboarding' ? '' : ' style="display:none;"'; ?>>
        <div class="ck-list"><?php foreach ($rows ? $rows : array(array('title' => '', 'owner' => 'HR', 'days_offset' => 0)) as $t) { ?>
          <div class="ck-t"><input name="title[]" class="form-control" maxlength="150" value="<?php echo $h($t['title']); ?>" aria-label="Step"<?php echo $k === 'onboarding' ? '' : ' disabled'; ?>><select name="owner[]" class="form-control" aria-label="Who"<?php echo $k === 'onboarding' ? '' : ' disabled'; ?>><?php foreach ($owners as $o) { ?><option<?php echo $o === $t['owner'] ? ' selected' : ''; ?>><?php echo $o; ?></option><?php } ?></select><input name="days[]" type="number" class="form-control" value="<?php echo (int)$t['days_offset']; ?>" aria-label="Days"<?php echo $k === 'onboarding' ? '' : ' disabled'; ?>><button type="button" class="btn btn-default ckDel" aria-label="Remove step"><i class="fa fa-times"></i></button></div>
        <?php } ?></div>
        <button type="button" class="btn btn-default btn-sm ckAdd"><i class="fa fa-plus"></i> Add step</button>
      </div>
      <?php } ?>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
  </form></div>
</div>
<script>
$(function () {
  $('#ck2').on('change', function () { $('#ck3l').text(this.value === 'onboarding' ? 'Joining date' : 'Last working day'); });
  $('#ckKinds button').on('click', function () {
    var k = $(this).data('k'); $('#ckKinds button').removeClass('is-on'); $(this).addClass('is-on'); $('#ckKind').val(k);
    $('.ck-k').each(function () { var on = $(this).data('k') === k; $(this).toggle(on).find('input,select').prop('disabled', !on); });
  });
  $(document).on('click', '.ckAdd', function () { var l = $(this).siblings('.ck-list'), r = l.find('.ck-t').first().clone(); r.find('input').val(''); r.find('input[type=number]').val(0); l.append(r); });
  $(document).on('click', '.ckDel', function () { var l = $(this).closest('.ck-list'); if (l.find('.ck-t').length > 1) $(this).closest('.ck-t').remove(); });
});
</script>
<?php } ?>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
