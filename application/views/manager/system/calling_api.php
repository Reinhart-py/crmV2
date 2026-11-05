<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$cfg = isset($threecx_config) && is_array($threecx_config) ? $threecx_config : array();
$v = function ($k, $d = '') use ($cfg) { return isset($cfg[$k]) && $cfg[$k] !== '' ? $cfg[$k] : $d; };
$can_edit = !empty($can_edit);
$has_secret = !empty($has_secret);
$configured = $v('pbx_fqdn') !== '' && $v('client_id') !== '' && $has_secret;
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_6">
<style>
  .ca-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px 16px; }
  .ca-grid .wide { grid-column: 1 / -1; }
  .ca-grid .form-group { margin: 0; }
  .ca-check { display: flex; gap: 8px; align-items: center; font-weight: 500 !important; padding-top: 8px; cursor: pointer; }
  .ca-check input { width: 16px; height: 16px; margin: 0; }
  #testConnectionStatus { display: none; margin: 0 16px 14px; padding: 10px 14px; border-radius: 10px; font-size: 13px; font-weight: 500; }
  #testConnectionStatus.ok { display: block; background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
  #testConnectionStatus.bad { display: block; background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
  .sx .sx-card-f { justify-content: space-between; }
  @media (max-width: 767px) { .ca-grid { grid-template-columns: 1fr; } #testConnectionStatus { margin: 0 12px 12px; } }
</style>
<div class="content-wrapper sx">
  <section class="sx-head">
    <h1>Calling API<small>Connect the CRM to your 3CX phone system for click-to-call and the auto-dialer</small></h1>
  </section>

  <section class="content">
    <div class="sx-narrow">
      <div class="sx-kpis">
        <div class="sx-kpi <?php echo $configured ? 'g' : 'a'; ?>"><span class="ic"><i class="fa <?php echo $configured ? 'fa-check' : 'fa-exclamation'; ?>"></i></span><div><b><?php echo $configured ? 'Set up' : 'Not set up'; ?></b><span>3CX connection</span></div></div>
        <div class="sx-kpi s"><span class="ic"><i class="fa fa-clock-o"></i></span><div><b><?php echo !empty($cfg['updated_at']) ? $h(date('d M Y', strtotime($cfg['updated_at']))) : '-'; ?></b><span>Last saved</span></div></div>
      </div>

      <div class="sx-card">
        <div class="sx-card-h"><h3><i class="fa fa-phone-square"></i> 3CX settings</h3><?php if (!$can_edit) { ?><div class="sx-tools"><span class="chip na"><i class="fa fa-lock"></i> View only</span></div><?php } ?></div>
        <form id="threecxConfigForm" autocomplete="off">
          <fieldset <?php echo $can_edit ? '' : 'disabled'; ?>>
          <div class="sx-card-b">
            <div class="ca-grid">
              <div class="form-group wide">
                <label for="pbx_fqdn">3CX address <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon">https://</span>
                  <input type="text" name="pbx_fqdn" id="pbx_fqdn" class="form-control" placeholder="yourcompany.3cx.ae" value="<?php echo $h($v('pbx_fqdn')); ?>" required />
                </div>
              </div>
              <div class="form-group">
                <label for="client_id">Client ID <span class="text-danger">*</span></label>
                <input type="text" name="client_id" id="client_id" class="form-control" value="<?php echo $h($v('client_id')); ?>" required />
              </div>
              <div class="form-group">
                <label for="client_secret">Client secret <?php echo $has_secret ? '' : '<span class="text-danger">*</span>'; ?></label>
                <input type="password" name="client_secret" id="client_secret" class="form-control" autocomplete="new-password" placeholder="<?php echo $has_secret ? 'Saved - leave blank to keep it' : 'Enter the client secret'; ?>" <?php echo $has_secret ? '' : 'required'; ?> />
              </div>
              <div class="form-group">
                <label for="default_extension">Default extension</label>
                <input type="text" name="default_extension" id="default_extension" class="form-control" placeholder="e.g. 101" value="<?php echo $h($v('default_extension')); ?>" />
              </div>
              <div class="form-group">
                <label for="autodialer_seconds">Auto-dialer countdown (seconds) <span class="text-danger">*</span></label>
                <input type="number" name="autodialer_seconds" id="autodialer_seconds" min="10" max="300" class="form-control" value="<?php echo (int)$v('autodialer_seconds', 60); ?>" required />
              </div>
              <div class="form-group wide">
                <label for="multiple_lines">Extension for each person</label>
                <textarea name="multiple_lines" id="multiple_lines" class="form-control mono" rows="3" placeholder="user_id=extension, e.g. 1=101, 2=102"><?php echo $h($v('multiple_lines')); ?></textarea>
                <p class="sx-help" style="margin-top:6px;">One pair per person: the staff ID, "=", then their 3CX extension. Separate pairs with commas.</p>
              </div>
              <div class="form-group wide">
                <label class="ca-check"><input type="checkbox" name="auto_open_webclient" value="1" <?php echo !empty($cfg['auto_open_webclient']) ? 'checked' : ''; ?> /> Open the 3CX web client automatically when a call starts</label>
              </div>
            </div>
          </div>
          </fieldset>
          <div id="testConnectionStatus" role="status"></div>
          <?php if ($can_edit) { ?>
          <div class="sx-card-f">
            <button type="button" class="btn btn-default" id="btnTestConnection"><i class="fa fa-bolt"></i> Test connection</button>
            <button type="submit" class="btn btn-primary" id="btnSaveConfig"><i class="fa fa-save"></i> Save settings</button>
          </div>
          <?php } ?>
        </form>
      </div>
      <p class="sx-help"><i class="fa fa-shield"></i> The client secret is stored on the server only and is never shown again after saving.</p>
    </div>
  </section>
</div>

<?php if ($can_edit) { ?>
<script>
$(function () {
  var box = $('#testConnectionStatus');
  function say(ok, msg) { box.removeClass('ok bad').addClass(ok ? 'ok' : 'bad').html('<i class="fa ' + (ok ? 'fa-check-circle' : 'fa-exclamation-triangle') + '"></i> ').append(document.createTextNode(msg)); }

  $('#btnTestConnection').on('click', function () {
    var fqdn = $.trim($('#pbx_fqdn').val()), cid = $.trim($('#client_id').val()), sec = $.trim($('#client_secret').val());
    if (!fqdn || !cid || (!sec && <?php echo $has_secret ? 'false' : 'true'; ?>)) { say(false, 'Fill in the 3CX address, Client ID and Client secret first.'); return; }
    var btn = $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Testing...');
    $.ajax({
      url: '<?php echo base_url(); ?>manager/calls/ajax_test_3cx', type: 'POST', dataType: 'json',
      data: { pbx_fqdn: fqdn, client_id: cid, client_secret: sec },
      success: function (res) { say(!!res.status, res.message || (res.status ? 'Connected to 3CX.' : 'Could not sign in to 3CX.')); },
      error: function () { say(false, 'The request failed. Check the server can reach the internet.'); },
      complete: function () { btn.prop('disabled', false).html('<i class="fa fa-bolt"></i> Test connection'); }
    });
  });

  $('#threecxConfigForm').on('submit', function (e) {
    e.preventDefault();
    var btn = $('#btnSaveConfig').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
    $.ajax({
      url: '<?php echo base_url(); ?>manager/calls/ajax_save_3cx_config', type: 'POST', dataType: 'json', data: $(this).serialize(),
      success: function (res) {
        if (res.status) { say(true, 'Settings saved.'); $('#client_secret').val('').attr('placeholder', 'Saved - leave blank to keep it').prop('required', false); }
        else say(false, 'Not saved: ' + (res.message || 'error'));
      },
      error: function () { say(false, 'The server could not save the settings.'); },
      complete: function () { btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save settings'); }
    });
  });
});
</script>
<?php } ?>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
