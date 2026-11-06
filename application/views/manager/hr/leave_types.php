<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$fd = function ($d) { $d = (float)$d; return ((floor($d) == $d) ? (int)$d : rtrim(rtrim(number_format($d, 1), '0'), '.')); };
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_8">
<div class="content-wrapper sx sx-hr">
  <?php echo crm_tabbar('hr_time', 'hr/leave_types'); ?>
  <section class="sx-head">
    <h1>Leave Types</h1>
    <?php if ($can_edit) { ?><div class="sx-btns"><button type="button" class="btn btn-primary" id="ltAdd"><i class="fa fa-plus"></i> Add leave type</button></div><?php } ?>
  </section>
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>
    <div class="sx-card">
      <div class="sx-table-wrap">
        <table class="sx-table sx-cards">
          <thead><tr><th>Leave type</th><th>Days a year</th><th>Pay</th><th>Counts</th><th>Rules</th><th>Used</th><?php if ($can_edit) { ?><th></th><?php } ?></tr></thead>
          <tbody>
          <?php foreach ($types as $t) { ?>
            <tr>
              <td data-l="Leave type"><b><?php echo $h($t['name']); ?></b> <?php if (!$t['active']) { ?><span class="chip na">Off</span><?php } ?><span class="text-muted" style="display:block;font-size:12px;"><?php echo $h($t['note']); ?></span></td>
              <td data-l="Days a year"><?php echo $t['accrual'] === 'uae_annual' ? $fd($set['annual_leave_days']) . ' <span class="text-muted" style="font-size:12px;">(builds up)</span>' : ((float)$t['days_per_year'] > 0 ? $fd($t['days_per_year']) : '<span class="text-muted">No limit</span>'); ?></td>
              <td data-l="Pay"><?php echo $h(isset($pay_labels[$t['pay']]) ? $pay_labels[$t['pay']] : $t['pay']); ?></td>
              <td data-l="Counts"><?php echo $t['count_mode'] === 'working' ? 'Working days' : 'Calendar days'; ?></td>
              <td data-l="Rules"><?php
                $rules = array();
                if ($t['gender'] !== 'any') $rules[] = ucfirst($t['gender']) . ' staff only';
                if ((int)$t['min_service_months'] > 0) $rules[] = 'After ' . (int)$t['min_service_months'] . ' months of service';
                if ($t['needs_document']) $rules[] = 'Document needed';
                echo $rules ? $h(implode(' · ', $rules)) : '<span class="text-muted">-</span>';
              ?></td>
              <td data-l="Used"><?php echo isset($used[$t['name']]) ? (int)$used[$t['name']] . ' request' . ($used[$t['name']] === 1 ? '' : 's') : '<span class="text-muted">0</span>'; ?></td>
              <?php if ($can_edit) { ?><td data-l="" class="nw"><button type="button" class="btn btn-default btn-sm ltEdit" data-t="<?php echo $h(json_encode($t)); ?>"><i class="fa fa-pencil"></i> Edit</button></td><?php } ?>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
    <p class="sx-help"><i class="fa fa-info-circle"></i> Working days leave out the weekend and public holidays. Annual leave builds up (2 days a month after 6 months) and its yearly days are set in HR Settings or per person in the HR file. Switch a type off to stop new requests; old requests keep it.</p>
  </section>
</div>

<?php if ($can_edit) { ?>
<div class="modal fade" id="ltModal" tabindex="-1" role="dialog" aria-labelledby="ltModalT">
  <div class="modal-dialog" role="document"><form class="modal-content" method="post" action="<?php echo base_url(); ?>manager/hr/leave_type_save">
    <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="ltModalT">Leave type</h4></div>
    <div class="modal-body sx">
      <input type="hidden" name="id" id="lt0">
      <div class="form-group"><label for="lt1">Name</label><input id="lt1" name="name" class="form-control" maxlength="50" required></div>
      <div class="sx-grid" style="grid-template-columns:1fr 1fr;">
        <div class="form-group"><label for="lt2">Days a year</label><input id="lt2" type="number" step="0.5" min="0" max="365" name="days_per_year" class="form-control"><p class="help-block" style="font-size:12px;margin:4px 0 0;">0 = no limit</p></div>
        <div class="form-group"><label for="lt3">Pay</label><select id="lt3" name="pay" class="form-control"><?php foreach ($pay_labels as $k => $v) { ?><option value="<?php echo $k; ?>"><?php echo $h($v); ?></option><?php } ?></select></div>
        <div class="form-group"><label for="lt4">Count</label><select id="lt4" name="count_mode" class="form-control"><option value="working">Working days</option><option value="calendar">Calendar days</option></select></div>
        <div class="form-group"><label for="lt5">Who</label><select id="lt5" name="gender" class="form-control"><option value="any">Everyone</option><option value="female">Female staff</option><option value="male">Male staff</option></select></div>
        <div class="form-group"><label for="lt6">After months of service</label><input id="lt6" type="number" min="0" max="120" name="min_service_months" class="form-control"></div>
      </div>
      <div class="form-group" style="margin-top:12px;"><label for="lt7">Note</label><input id="lt7" name="note" class="form-control" maxlength="255"></div>
      <div class="checkbox"><label style="font-weight:500;"><input type="checkbox" name="needs_document" value="1" id="lt8"> A document is needed (e.g. medical certificate)</label></div>
      <div class="checkbox"><label style="font-weight:500;"><input type="checkbox" name="active" value="1" id="lt9"> In use</label></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
  </form></div>
</div>
<script>
$(function () {
  function open(t) {
    t = t || { id: '', name: '', days_per_year: 0, pay: 'full', count_mode: 'working', gender: 'any', min_service_months: 0, note: '', needs_document: 0, active: 1 };
    $('#lt0').val(t.id); $('#lt1').val(t.name); $('#lt2').val(parseFloat(t.days_per_year) || 0); $('#lt3').val(t.pay); $('#lt4').val(t.count_mode);
    $('#lt5').val(t.gender); $('#lt6').val(t.min_service_months); $('#lt7').val(t.note); $('#lt8').prop('checked', +t.needs_document === 1); $('#lt9').prop('checked', +t.active === 1);
    $('#ltModalT').text(t.id ? 'Edit ' + t.name : 'Add leave type');
    $('#ltModal').modal('show');
  }
  $('#ltAdd').on('click', function () { open(null); });
  $(document).on('click', '.ltEdit', function () { open($(this).data('t')); });
});
</script>
<?php } ?>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
