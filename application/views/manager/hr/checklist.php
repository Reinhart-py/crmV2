<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$left = 0;
foreach ($items as $it) if (!$it['done_at']) $left++;
$today = date('Y-m-d');
$base = base_url() . 'manager/hr/';
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_8">
<style>
  .ck-list { list-style: none; margin: 0; padding: 0; }
  .ck-list li { display: grid; grid-template-columns: 28px 1fr auto auto; gap: 10px; align-items: center; padding: 10px 14px; border-bottom: 1px solid var(--crm-line-soft, #eef2f7); }
  .ck-list li:last-child { border-bottom: 0; }
  .ck-list input[type=checkbox] { width: 18px; height: 18px; margin: 0; }
  .ck-list .done .t { text-decoration: line-through; color: var(--crm-muted, #64748b); }
  .ck-sub { display: block; font-size: 12px; color: var(--crm-muted, #64748b); }
  .ck-late { color: #b91c1c; font-weight: 600; }
  .ck-list li > .chip { justify-self: start; }
  @media (max-width: 600px) { .ck-list li { grid-template-columns: 28px 1fr; } .ck-list li > :nth-child(n+3) { grid-column: 2; } }
</style>
<div class="content-wrapper sx sx-hr">
  <?php echo crm_tabbar('hr_people', 'hr/checklists'); ?>
  <section class="sx-head">
    <h1><a href="<?php echo $base; ?>checklists" class="text-muted" aria-label="Back" style="margin-right:6px;"><i class="fa fa-angle-left"></i></a><?php echo $c['kind'] === 'onboarding' ? 'Onboarding' : 'Exit'; ?>: <?php echo $h($c['name']); ?> <?php if ($c['status'] === 'Done') { ?><span class="chip ok" style="vertical-align:middle;">Done</span><?php } ?>
      <small><?php echo $c['kind'] === 'onboarding' ? 'Joining' : 'Last working day'; ?> <?php echo $h(date('d M Y', strtotime($c['start_date']))); ?><?php echo $c['department'] ? ' · ' . $h($c['department']) : ''; ?></small></h1>
    <div class="sx-btns">
      <?php if ($c['kind'] === 'offboarding') { ?><a class="btn btn-default" href="<?php echo $base; ?>settlement/<?php echo (int)$c['user_id']; ?>?last_day=<?php echo $h($c['start_date']); ?>"><i class="fa fa-calculator"></i> Final settlement</a><?php } ?>
      <?php if ($can_edit) { ?><form method="post" action="<?php echo $base; ?>checklist_delete/<?php echo (int)$c['id']; ?>" onsubmit="return confirm('Delete this checklist?');"><button type="submit" class="btn btn-default" aria-label="Delete checklist"><i class="fa fa-trash-o"></i></button></form><?php } ?>
    </div>
  </section>
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>
    <div class="sx-card">
      <div class="sx-card-h"><h3><i class="fa fa-check-square-o"></i> Steps</h3><span class="sx-count" id="ckLeft"><?php echo $left; ?> to do</span></div>
      <ul class="ck-list">
        <?php foreach ($items as $it) { $late = !$it['done_at'] && $it['due_date'] && $it['due_date'] < $today; ?>
          <li class="<?php echo $it['done_at'] ? 'done' : ''; ?>">
            <input type="checkbox" class="ckTick" data-id="<?php echo (int)$it['id']; ?>" aria-label="<?php echo $h($it['title']); ?>"<?php echo $it['done_at'] ? ' checked' : ''; ?><?php echo $can_edit ? '' : ' disabled'; ?>>
            <span><span class="t"><?php echo $h($it['title']); ?></span><span class="ck-sub ckBy"><?php echo $it['done_at'] ? 'Done ' . $h(date('d M Y', strtotime($it['done_at']))) . ($it['done_name'] ? ' by ' . $h($it['done_name']) : '') : ''; ?></span></span>
            <span class="chip s"><?php echo $h($it['owner']); ?></span>
            <span class="nw <?php echo $late ? 'ck-late' : 'ck-sub'; ?>" style="display:flex;align-items:center;gap:6px;"><?php echo $it['due_date'] ? ($late ? 'Late: ' : 'By ') . $h(date('d M', strtotime($it['due_date']))) : ''; ?>
              <?php if ($can_edit) { ?><form method="post" action="<?php echo $base; ?>checklist_item_add/<?php echo (int)$c['id']; ?>" style="display:inline;" onsubmit="return confirm('Remove this step?');"><input type="hidden" name="remove" value="<?php echo (int)$it['id']; ?>"><button type="submit" class="btn btn-link btn-xs text-muted" aria-label="Remove step"><i class="fa fa-times"></i></button></form><?php } ?></span>
          </li>
        <?php } ?>
      </ul>
      <?php if ($can_edit) { ?>
      <form class="sx-card-f" method="post" action="<?php echo $base; ?>checklist_item_add/<?php echo (int)$c['id']; ?>" style="display:flex;gap:8px;flex-wrap:wrap;">
        <input name="title" class="form-control" maxlength="150" placeholder="Add a step" required style="flex:1 1 220px;" aria-label="New step">
        <select name="owner" class="form-control" style="width:auto;" aria-label="Who"><?php foreach ($owners as $o) { ?><option><?php echo $o; ?></option><?php } ?></select>
        <input type="date" name="due_date" class="form-control" style="width:auto;" aria-label="Due date">
        <button type="submit" class="btn btn-default"><i class="fa fa-plus"></i> Add</button>
      </form>
      <?php } ?>
    </div>
  </section>
</div>
<script>
$(function () {
  $('.ckTick').on('change', function () {
    var c = $(this), li = c.closest('li');
    $.post('<?php echo $base; ?>checklist_tick', { id: c.data('id'), done: c.is(':checked') ? 1 : 0 }, null, 'json').done(function (r) {
      if (!r || !r.ok) { alert(r && r.message ? r.message : 'Could not save.'); c.prop('checked', !c.is(':checked')); return; }
      li.toggleClass('done', c.is(':checked')); li.find('.ckBy').text(r.by ? 'Done ' + r.at + ' by ' + r.by : '');
      $('#ckLeft').text(r.left + ' to do');
    }).fail(function () { alert('Could not save.'); c.prop('checked', !c.is(':checked')); });
  });
});
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
