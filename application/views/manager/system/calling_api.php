<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>

<div class="content-wrapper" style="min-height: calc(100vh - 120px); background: #f8fafc; padding: 20px;">
  <div class="container-fluid" style="max-width: 900px; margin: 0 auto;">
    
    <div style="margin-bottom: 20px;">
      <h3 style="margin: 0; font-size: 22px; font-weight: 700; color: #0f172a;">
        <i class="fa fa-phone-square" style="color: #2563eb; margin-right: 8px;"></i>System - Calling API Settings
      </h3>
    </div>

    <div class="box box-primary" style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 24px;">
      <form id="threecxConfigForm">
        
        <div style="margin-bottom: 20px;">
          <label style="font-size: 13px; font-weight: 700; color: #1e293b; display: block; margin-bottom: 6px;">
            3CX PBX Domain / FQDN <span style="color: #ef4444;">*</span>
          </label>
          <div class="input-group">
            <span class="input-group-addon" style="background: #f1f5f9; border-color: #cbd5e1; font-weight: 600; color: #475569;">https://</span>
            <input type="text" name="pbx_fqdn" id="pbx_fqdn" class="form-control" style="border-color: #cbd5e1; font-size: 13px;" placeholder="e.g. yourcompany.3cx.ae" value="<?php echo htmlspecialchars(!empty($threecx_config['pbx_fqdn']) ? $threecx_config['pbx_fqdn'] : ''); ?>" required />
          </div>
        </div>

        <div class="row">
          <div class="col-md-6" style="margin-bottom: 20px;">
            <label style="font-size: 13px; font-weight: 700; color: #1e293b; display: block; margin-bottom: 6px;">
              Client ID <span style="color: #ef4444;">*</span>
            </label>
            <input type="text" name="client_id" id="client_id" class="form-control" style="border-color: #cbd5e1; font-size: 13px;" placeholder="e.g. client_id_key" value="<?php echo htmlspecialchars(!empty($threecx_config['client_id']) ? $threecx_config['client_id'] : ''); ?>" required />
          </div>

          <div class="col-md-6" style="margin-bottom: 20px;">
            <label style="font-size: 13px; font-weight: 700; color: #1e293b; display: block; margin-bottom: 6px;">
              Client Secret <span style="color: #ef4444;">*</span>
            </label>
            <input type="password" name="client_secret" id="client_secret" class="form-control" style="border-color: #cbd5e1; font-size: 13px;" placeholder="Enter Client Secret" value="<?php echo htmlspecialchars(!empty($threecx_config['client_secret']) ? $threecx_config['client_secret'] : ''); ?>" required />
          </div>
        </div>

        <div class="row">
          <div class="col-md-6" style="margin-bottom: 20px;">
            <label style="font-size: 13px; font-weight: 700; color: #1e293b; display: block; margin-bottom: 6px;">
              Default Extension
            </label>
            <input type="text" name="default_extension" id="default_extension" class="form-control" style="border-color: #cbd5e1; font-size: 13px;" placeholder="e.g. 100 or 101" value="<?php echo htmlspecialchars(!empty($threecx_config['default_extension']) ? $threecx_config['default_extension'] : ''); ?>" />
          </div>

          <div class="col-md-6" style="margin-bottom: 20px;">
            <label style="font-size: 13px; font-weight: 700; color: #1e293b; display: block; margin-bottom: 6px;">
              Browser Calling Mode
            </label>
            <div style="padding-top: 6px;">
              <label style="font-weight: 500; font-size: 13px; color: #334155; cursor: pointer;">
                <input type="checkbox" name="auto_open_webclient" value="1" <?php echo (!empty($threecx_config['auto_open_webclient'])) ? 'checked' : ''; ?> />
                Automatically open 3CX WebClient on Call
              </label>
            </div>
          </div>
        </div>

        <div style="margin-bottom: 20px;">
          <label style="font-size: 13px; font-weight: 700; color: #1e293b; display: block; margin-bottom: 6px;">
            Multiple Lines / Agent Extension Mapping
          </label>
          <textarea name="multiple_lines" id="multiple_lines" class="form-control" rows="3" style="border-color: #cbd5e1; font-size: 12px; font-family: monospace;" placeholder="user_id=extension (e.g. 1=101, 2=102)"><?php echo htmlspecialchars(!empty($threecx_config['multiple_lines']) ? $threecx_config['multiple_lines'] : ''); ?></textarea>
        </div>

        <div id="testConnectionStatus" style="display: none; margin-bottom: 20px; padding: 12px 16px; border-radius: 8px; font-size: 13px; font-weight: 500;"></div>

        <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #e2e8f0; padding-top: 18px; flex-wrap: wrap; gap: 10px;">
          <button type="button" class="btn btn-default" id="btnTestConnection" style="font-weight: 600; border-radius: 6px;">
            <i class="fa fa-bolt" style="color: #f59e0b; margin-right: 4px;"></i> Test 3CX Connection
          </button>

          <button type="submit" class="btn btn-primary" id="btnSaveConfig" style="font-weight: 700; border-radius: 6px; background: #2563eb; border-color: #1d4ed8; padding: 8px 24px;">
            <i class="fa fa-save" style="margin-right: 4px;"></i> Save 3CX Configuration
          </button>
        </div>

      </form>
    </div>

  </div>
</div>

<script>
$(document).ready(function() {
  $('#btnTestConnection').on('click', function() {
    var fqdn = $('#pbx_fqdn').val().trim();
    var clientId = $('#client_id').val().trim();
    var clientSecret = $('#client_secret').val().trim();

    if (!fqdn || !clientId || !clientSecret) {
      alert('Please fill in FQDN, Client ID, and Client Secret before testing.');
      return;
    }

    var btn = $(this);
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Testing Connection...');
    $('#testConnectionStatus').hide();

    $.ajax({
      url: '<?php echo base_url(); ?>manager/calls/ajax_test_3cx',
      type: 'POST',
      data: {
        pbx_fqdn: fqdn,
        client_id: clientId,
        client_secret: clientSecret
      },
      dataType: 'json',
      success: function(res) {
        btn.prop('disabled', false).html('<i class="fa fa-bolt" style="color: #f59e0b; margin-right: 4px;"></i> Test 3CX Connection');
        var box = $('#testConnectionStatus');
        box.show();
        if (res.status) {
          box.css({'background': '#ecfdf5', 'color': '#065f46', 'border': '1px solid #a7f3d0'});
          box.html('<i class="fa fa-check-circle"></i> ' + (res.message || 'Connection to 3CX PBX successful!'));
        } else {
          box.css({'background': '#fef2f2', 'color': '#991b1b', 'border': '1px solid #fecaca'});
          box.html('<i class="fa fa-exclamation-triangle"></i> ' + (res.message || 'Failed to authenticate with 3CX.'));
        }
      },
      error: function() {
        btn.prop('disabled', false).html('<i class="fa fa-bolt" style="color: #f59e0b; margin-right: 4px;"></i> Test 3CX Connection');
        $('#testConnectionStatus').show().css({'background': '#fef2f2', 'color': '#991b1b', 'border': '1px solid #fecaca'}).html('<i class="fa fa-times-circle"></i> Request failed. Check server network connectivity.');
      }
    });
  });

  $('#threecxConfigForm').on('submit', function(e) {
    e.preventDefault();
    var btn = $('#btnSaveConfig');
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

    $.ajax({
      url: '<?php echo base_url(); ?>manager/calls/ajax_save_3cx_config',
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function(res) {
        btn.prop('disabled', false).html('<i class="fa fa-save" style="margin-right: 4px;"></i> Save 3CX Configuration');
        if (res.status) {
          alert('3CX Configuration saved successfully!');
        } else {
          alert('Failed to save: ' + (res.message || 'Error'));
        }
      },
      error: function() {
        btn.prop('disabled', false).html('<i class="fa fa-save" style="margin-right: 4px;"></i> Save 3CX Configuration');
        alert('Server error occurred while saving.');
      }
    });
  });
});
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
