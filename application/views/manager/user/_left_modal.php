<?php
/* "Mark as left" dialog, shared by the users list and View User. Opened by any .js-left (data-id, data-name). */
$csrf_name = $this->security->get_csrf_token_name(); $csrf_hash = $this->security->get_csrf_hash();
?>
<style>
#leftModal .modal-content{border-radius:12px;border:1px solid var(--crm-line,#e2e8f0);background:var(--crm-surface,#fff);color:var(--crm-ink,#0f172a);font-family:"Plus Jakarta Sans","Inter",-apple-system,"Segoe UI",sans-serif}
#leftModal .modal-header{border-bottom:1px solid var(--crm-line-soft,#eef2f7);padding:12px 16px}
#leftModal .modal-title{font-size:16px;font-weight:700}
#leftModal .modal-body{padding:14px 16px}
#leftModal label{font-size:12.5px;font-weight:600;color:var(--crm-ink-2,#334155);margin-bottom:3px}
#leftModal .form-control{height:34px;border-radius:6px;font-size:13.5px}
#leftModal textarea.form-control{height:70px}
#leftModal .lm-note{font-size:12.5px;color:var(--crm-muted,#64748b);background:var(--crm-canvas,#f8fafc);border:1px solid var(--crm-line-soft,#eef2f7);border-radius:8px;padding:8px 10px;margin-top:4px}
#leftModal .modal-footer{border-top:1px solid var(--crm-line-soft,#eef2f7);padding:10px 16px}
</style>
<div class="modal fade" id="leftModal" tabindex="-1" role="dialog" aria-labelledby="leftModalTitle">
  <div class="modal-dialog" role="document" style="max-width:460px">
    <form class="modal-content" method="post" action="">
      <input type="hidden" name="<?php echo $csrf_name; ?>" value="<?php echo $csrf_hash; ?>">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="leftModalTitle">Mark as left</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-6 form-group"><label>Leaving date <span style="color:#dc2626">*</span></label><input type="date" class="form-control" name="left_on" required></div>
          <div class="col-sm-6 form-group"><label>Reason <span style="color:#dc2626">*</span></label>
            <select class="form-control" name="reason" required><option value="">Select</option><?php foreach ($exit_reasons as $er) { ?><option><?php echo htmlspecialchars($er, ENT_QUOTES, 'UTF-8'); ?></option><?php } ?></select></div>
        </div>
        <div class="form-group" style="margin-bottom:6px"><label>Note</label><textarea class="form-control" name="note" maxlength="500" placeholder="Optional - e.g. notice period, handover"></textarea></div>
        <div class="lm-note"><i class="fa fa-info-circle"></i> The login is switched off and they leave the manager list. Their database and leads stay with them until you transfer them; orders and sales always stay with them. You can undo this with <b>Rejoin</b>.</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-danger"><i class="fa fa-sign-out"></i> Mark as left</button>
      </div>
    </form>
  </div>
</div>
<script>
$(function () {
  $(document).on('click', '.js-left', function (e) {
    e.preventDefault();
    var m = $('#leftModal'), id = $(this).data('id');
    m.find('form').attr('action', '<?php echo base_url() . $base; ?>/left/' + id);
    m.find('.modal-title').text('Mark ' + $(this).data('name') + ' as left');
    m.find('[name=left_on]').val(new Date().toISOString().slice(0, 10));
    m.find('[name=reason]').val(''); m.find('[name=note]').val('');
    m.modal('show');
  });
});
</script>