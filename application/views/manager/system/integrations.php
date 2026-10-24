<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Third-Party API</h1>
  </section>

  <section class="content" style="padding:15px;">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="row">
      <div class="col-md-3">
        <div class="box box-solid" style="border-radius:8px; border:1px solid #e2e8f0;">
          <div class="box-body no-padding">
            <ul class="nav nav-pills nav-stacked" id="integrationNavTabs" style="font-weight:600;">
              <li class="active"><a href="#tab_groq" data-toggle="tab"><i class="fa fa-microchip text-primary"></i> AI Engine (Groq)</a></li>
              <li><a href="#tab_pbx" data-toggle="tab"><i class="fa fa-phone-square text-success"></i> 3CX PBX Telephony</a></li>
              <li><a href="#tab_email" data-toggle="tab"><i class="fa fa-envelope text-info"></i> Email &amp; SMTP</a></li>
              <li><a href="#tab_whatsapp" data-toggle="tab"><i class="fa fa-whatsapp text-success"></i> WhatsApp Business</a></li>
              <li><a href="#tab_cron" data-toggle="tab"><i class="fa fa-clock-o text-warning"></i> Schedules &amp; Reminders</a></li>
              <li><a href="#tab_helpdesk" data-toggle="tab"><i class="fa fa-life-ring text-danger"></i> Help Center &amp; Support</a></li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col-md-9">
        <div class="tab-content">
          <div class="tab-pane active" id="tab_groq">
            <?php $g = $settings['groq']; ?>
            <div class="box box-default" style="border-radius:8px; border:1px solid #e2e8f0;">
              <div class="box-header with-border" style="display:flex; justify-content:space-between; align-items:center;">
                <h3 class="box-title" style="font-weight:700;">Groq AI Speech &amp; Intelligence</h3>
                <div>
                  <?php if ((int)$g['status'] === 1): ?>
                    <span class="label label-success" style="font-size:11px; padding:4px 8px;">Connected</span>
                  <?php else: ?>
                    <span class="label label-default" style="font-size:11px; padding:4px 8px;">Not Connected</span>
                  <?php endif; ?>
                </div>
              </div>
              <form action="<?php echo base_url(); ?>manager/system/integrations/save" method="post">
                <input type="hidden" name="service_name" value="groq" />
                <div class="box-body">
                  <div class="form-group">
                    <label>API Endpoint</label>
                    <input type="text" name="endpoint" class="form-control" value="<?php echo htmlspecialchars($g['endpoint'] ?: 'https://api.groq.com/openai/v1'); ?>" required />
                  </div>
                  <div class="form-group">
                    <label>Groq API Key (Or configured in GROQ_API_KEY env)</label>
                    <input type="password" name="api_key" class="form-control" placeholder="<?php echo !empty($g['api_key']) ? '••••••••••••••••••••' : 'Enter Groq API Key (gsk_...)'; ?>" />
                  </div>
                  <div class="row">
                    <div class="col-md-6 form-group">
                      <label>Transcription Model</label>
                      <input type="text" name="config[transcription_model]" class="form-control" value="<?php echo htmlspecialchars($g['config']['transcription_model'] ?? 'whisper-large-v3-turbo'); ?>" required />
                    </div>
                    <div class="col-md-6 form-group">
                      <label>Chat Completion Model</label>
                      <input type="text" name="config[summary_model]" class="form-control" value="<?php echo htmlspecialchars($g['config']['summary_model'] ?? 'openai/gpt-oss-20b'); ?>" required />
                    </div>
                    <div class="col-md-6 form-group">
                      <label>Fallback Model</label>
                      <input type="text" name="config[fallback_summary_model]" class="form-control" value="<?php echo htmlspecialchars($g['config']['fallback_summary_model'] ?? 'openai/gpt-oss-120b'); ?>" />
                    </div>
                    <div class="col-md-6 form-group">
                      <label>Temperature</label>
                      <input type="number" step="0.1" min="0" max="1" name="config[temperature]" class="form-control" value="<?php echo htmlspecialchars($g['config']['temperature'] ?? '0'); ?>" />
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                      <option value="1" <?php if ((int)$g['status'] === 1) echo 'selected'; ?>>Enabled</option>
                      <option value="0" <?php if ((int)$g['status'] === 0) echo 'selected'; ?>>Disabled</option>
                    </select>
                  </div>
                </div>
                <div class="box-footer" style="display:flex; justify-content:space-between; align-items:center;">
                  <button type="button" class="btn btn-default btn-test-connection" data-service="groq"><i class="fa fa-refresh"></i> Test Connection</button>
                  <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Settings</button>
                </div>
              </form>
            </div>
          </div>

          <div class="tab-pane" id="tab_pbx">
            <?php $p = $settings['pbx_3cx']; ?>
            <div class="box box-default" style="border-radius:8px; border:1px solid #e2e8f0;">
              <div class="box-header with-border" style="display:flex; justify-content:space-between; align-items:center;">
                <h3 class="box-title" style="font-weight:700;">3CX PBX Telephony Integration</h3>
                <div>
                  <?php if ((int)$p['status'] === 1): ?>
                    <span class="label label-success" style="font-size:11px; padding:4px 8px;">Connected</span>
                  <?php else: ?>
                    <span class="label label-default" style="font-size:11px; padding:4px 8px;">Not Connected</span>
                  <?php endif; ?>
                </div>
              </div>
              <form action="<?php echo base_url(); ?>manager/system/integrations/save" method="post">
                <input type="hidden" name="service_name" value="pbx_3cx" />
                <div class="box-body">
                  <div class="form-group">
                    <label>PBX FQDN / Server Host</label>
                    <input type="text" name="endpoint" class="form-control" placeholder="https://pbx.rolextogroup.com:5001" value="<?php echo htmlspecialchars($p['endpoint']); ?>" required />
                  </div>
                  <div class="row">
                    <div class="col-md-6 form-group">
                      <label>Client ID / Extension Username</label>
                      <input type="text" name="api_key" class="form-control" value="<?php echo htmlspecialchars($p['api_key']); ?>" />
                    </div>
                    <div class="col-md-6 form-group">
                      <label>Client Secret / Password</label>
                      <input type="password" name="api_secret" class="form-control" placeholder="<?php echo !empty($p['api_secret']) ? '••••••••••••••••••••' : 'Enter 3CX Secret'; ?>" />
                    </div>
                  </div>
                  <div class="form-group">
                    <label>API Bearer Token (For Recording Downloads)</label>
                    <input type="password" name="config[bearer_token]" class="form-control" placeholder="<?php echo !empty($p['config']['bearer_token']) ? '••••••••••••••••••••' : 'Enter 3CX API Bearer Token'; ?>" />
                  </div>
                  <div class="form-group">
                    <label>Webhook Shared Secret</label>
                    <input type="text" name="config[webhook_secret]" class="form-control" value="<?php echo htmlspecialchars($p['config']['webhook_secret'] ?? '7strokes_3cx_shared_key_2026'); ?>" />
                  </div>
                  <div class="form-group">
                    <label>Recording Webhook URL (Give this to 3CX Event Notification)</label>
                    <input type="text" class="form-control" readonly value="<?php echo base_url(); ?>api/calls/recording_webhook" style="background:#f8fafc;" />
                  </div>
                  <div class="form-group">
                    <label>CDR Webhook URL</label>
                    <input type="text" class="form-control" readonly value="<?php echo base_url(); ?>api/calls/cdr_webhook" style="background:#f8fafc;" />
                  </div>
                  <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                      <option value="1" <?php if ((int)$p['status'] === 1) echo 'selected'; ?>>Enabled</option>
                      <option value="0" <?php if ((int)$p['status'] === 0) echo 'selected'; ?>>Disabled</option>
                    </select>
                  </div>
                </div>
                <div class="box-footer" style="display:flex; justify-content:space-between; align-items:center;">
                  <button type="button" class="btn btn-default btn-test-connection" data-service="pbx_3cx"><i class="fa fa-refresh"></i> Test PBX Connection</button>
                  <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Settings</button>
                </div>
              </form>
            </div>
          </div>

          <div class="tab-pane" id="tab_email">
            <?php $e = $settings['email_smtp']; ?>
            <div class="box box-default" style="border-radius:8px; border:1px solid #e2e8f0;">
              <div class="box-header with-border" style="display:flex; justify-content:space-between; align-items:center;">
                <h3 class="box-title" style="font-weight:700;">Email &amp; SMTP Configuration</h3>
                <div>
                  <?php if ((int)$e['status'] === 1): ?>
                    <span class="label label-success" style="font-size:11px; padding:4px 8px;">Active</span>
                  <?php else: ?>
                    <span class="label label-default" style="font-size:11px; padding:4px 8px;">Inactive</span>
                  <?php endif; ?>
                </div>
              </div>
              <form action="<?php echo base_url(); ?>manager/system/integrations/save" method="post">
                <input type="hidden" name="service_name" value="email_smtp" />
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-8 form-group">
                      <label>SMTP Host</label>
                      <input type="text" name="config[smtp_host]" class="form-control" placeholder="mail.rolextogroup.com" value="<?php echo htmlspecialchars($e['config']['smtp_host'] ?? ''); ?>" required />
                    </div>
                    <div class="col-md-4 form-group">
                      <label>SMTP Port</label>
                      <input type="number" name="config[smtp_port]" class="form-control" value="<?php echo htmlspecialchars($e['config']['smtp_port'] ?? '587'); ?>" required />
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6 form-group">
                      <label>SMTP Username</label>
                      <input type="text" name="config[smtp_user]" class="form-control" value="<?php echo htmlspecialchars($e['config']['smtp_user'] ?? ''); ?>" />
                    </div>
                    <div class="col-md-6 form-group">
                      <label>SMTP Password</label>
                      <input type="password" name="config[smtp_pass]" class="form-control" placeholder="<?php echo !empty($e['config']['smtp_pass']) ? '••••••••••••••••••••' : 'Enter SMTP Password'; ?>" />
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6 form-group">
                      <label>From Email</label>
                      <input type="email" name="config[from_email]" class="form-control" value="<?php echo htmlspecialchars($e['config']['from_email'] ?? 'noreply@rolextogroup.com'); ?>" />
                    </div>
                    <div class="col-md-6 form-group">
                      <label>From Name</label>
                      <input type="text" name="config[from_name]" class="form-control" value="<?php echo htmlspecialchars($e['config']['from_name'] ?? 'Seven Strokes CRM'); ?>" />
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Encryption</label>
                    <select name="config[smtp_crypto]" class="form-control">
                      <option value="tls" <?php if (($e['config']['smtp_crypto'] ?? '') === 'tls') echo 'selected'; ?>>TLS (Port 587)</option>
                      <option value="ssl" <?php if (($e['config']['smtp_crypto'] ?? '') === 'ssl') echo 'selected'; ?>>SSL (Port 465)</option>
                      <option value="none" <?php if (($e['config']['smtp_crypto'] ?? '') === 'none') echo 'selected'; ?>>None (Port 25)</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                      <option value="1" <?php if ((int)$e['status'] === 1) echo 'selected'; ?>>Enabled</option>
                      <option value="0" <?php if ((int)$e['status'] === 0) echo 'selected'; ?>>Disabled</option>
                    </select>
                  </div>
                </div>
                <div class="box-footer" style="display:flex; justify-content:space-between; align-items:center;">
                  <button type="button" class="btn btn-default btn-test-connection" data-service="email_smtp"><i class="fa fa-refresh"></i> Test SMTP Reachability</button>
                  <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Settings</button>
                </div>
              </form>
            </div>
          </div>

          <div class="tab-pane" id="tab_whatsapp">
            <?php $w = $settings['whatsapp']; ?>
            <div class="box box-default" style="border-radius:8px; border:1px solid #e2e8f0;">
              <div class="box-header with-border" style="display:flex; justify-content:space-between; align-items:center;">
                <h3 class="box-title" style="font-weight:700;">WhatsApp Business Cloud API</h3>
                <div>
                  <?php if ((int)$w['status'] === 1): ?>
                    <span class="label label-success" style="font-size:11px; padding:4px 8px;">Active</span>
                  <?php else: ?>
                    <span class="label label-default" style="font-size:11px; padding:4px 8px;">Inactive</span>
                  <?php endif; ?>
                </div>
              </div>
              <form action="<?php echo base_url(); ?>manager/system/integrations/save" method="post">
                <input type="hidden" name="service_name" value="whatsapp" />
                <div class="box-body">
                  <div class="form-group">
                    <label>Cloud API Endpoint</label>
                    <input type="text" name="endpoint" class="form-control" value="<?php echo htmlspecialchars($w['endpoint'] ?: 'https://graph.facebook.com/v19.0'); ?>" required />
                  </div>
                  <div class="form-group">
                    <label>Permanent Access Token</label>
                    <input type="password" name="api_key" class="form-control" placeholder="<?php echo !empty($w['api_key']) ? '••••••••••••••••••••' : 'Enter WhatsApp Access Token (EAA...)'; ?>" />
                  </div>
                  <div class="row">
                    <div class="col-md-6 form-group">
                      <label>Phone Number ID</label>
                      <input type="text" name="config[phone_number_id]" class="form-control" value="<?php echo htmlspecialchars($w['config']['phone_number_id'] ?? ''); ?>" />
                    </div>
                    <div class="col-md-6 form-group">
                      <label>Business Account ID</label>
                      <input type="text" name="config[waba_id]" class="form-control" value="<?php echo htmlspecialchars($w['config']['waba_id'] ?? ''); ?>" />
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Webhook Verify Token</label>
                    <input type="text" name="api_secret" class="form-control" value="<?php echo htmlspecialchars($w['api_secret'] ?: '7strokes_wa_verify_token_2026'); ?>" />
                  </div>
                  <div class="form-group">
                    <label>Webhook Callback URL</label>
                    <input type="text" class="form-control" readonly value="<?php echo base_url(); ?>api/whatsapp/webhook" style="background:#f8fafc;" />
                  </div>
                  <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                      <option value="1" <?php if ((int)$w['status'] === 1) echo 'selected'; ?>>Enabled</option>
                      <option value="0" <?php if ((int)$w['status'] === 0) echo 'selected'; ?>>Disabled</option>
                    </select>
                  </div>
                </div>
                <div class="box-footer" style="display:flex; justify-content:space-between; align-items:center;">
                  <button type="button" class="btn btn-default btn-test-connection" data-service="whatsapp"><i class="fa fa-refresh"></i> Test Token</button>
                  <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Settings</button>
                </div>
              </form>
            </div>
          </div>

          <div class="tab-pane" id="tab_cron">
            <?php $c = $settings['schedule_cron']; ?>
            <div class="box box-default" style="border-radius:8px; border:1px solid #e2e8f0;">
              <div class="box-header with-border" style="display:flex; justify-content:space-between; align-items:center;">
                <h3 class="box-title" style="font-weight:700;">Automatic Schedule Emails &amp; Reminders</h3>
                <div>
                  <?php if ((int)$c['status'] === 1): ?>
                    <span class="label label-success" style="font-size:11px; padding:4px 8px;">Active</span>
                  <?php else: ?>
                    <span class="label label-default" style="font-size:11px; padding:4px 8px;">Inactive</span>
                  <?php endif; ?>
                </div>
              </div>
              <form action="<?php echo base_url(); ?>manager/system/integrations/save" method="post">
                <input type="hidden" name="service_name" value="schedule_cron" />
                <div class="box-body">
                  <div class="form-group">
                    <label>Cron Secret Key</label>
                    <input type="text" name="api_key" class="form-control" value="<?php echo htmlspecialchars($c['api_key'] ?: bin2hex(random_bytes(12))); ?>" required />
                  </div>
                  <div class="form-group">
                    <label>Automated Reminder Cron URL (Execute Daily via Server Cron)</label>
                    <input type="text" class="form-control" readonly value="<?php echo base_url(); ?>cron/daily_schedule_reminders?key=<?php echo htmlspecialchars($c['api_key'] ?: 'key'); ?>" style="background:#f8fafc;" />
                  </div>
                  <div class="row">
                    <div class="col-md-6 form-group">
                      <label>Notice Days Before Schedule Due</label>
                      <input type="number" min="1" max="30" name="config[notice_days]" class="form-control" value="<?php echo htmlspecialchars($c['config']['notice_days'] ?? '3'); ?>" required />
                    </div>
                    <div class="col-md-6 form-group">
                      <label>Admin Notification Email</label>
                      <input type="email" name="config[notify_email]" class="form-control" value="<?php echo htmlspecialchars($c['config']['notify_email'] ?? 'compliance@rolextogroup.com'); ?>" />
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Automatic Daily Dispatch Status</label>
                    <select name="status" class="form-control">
                      <option value="1" <?php if ((int)$c['status'] === 1) echo 'selected'; ?>>Active &amp; Running</option>
                      <option value="0" <?php if ((int)$c['status'] === 0) echo 'selected'; ?>>Paused</option>
                    </select>
                  </div>
                </div>
                <div class="box-footer" style="display:flex; justify-content:space-between; align-items:center;">
                  <button type="button" class="btn btn-default btn-test-connection" data-service="schedule_cron"><i class="fa fa-refresh"></i> Verify Cron Endpoint</button>
                  <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Settings</button>
                </div>
              </form>
            </div>
          </div>

          <div class="tab-pane" id="tab_helpdesk">
            <?php $h = $settings['helpdesk']; ?>
            <div class="box box-default" style="border-radius:8px; border:1px solid #e2e8f0;">
              <div class="box-header with-border" style="display:flex; justify-content:space-between; align-items:center;">
                <h3 class="box-title" style="font-weight:700;">Help Center &amp; Support Routing</h3>
                <div>
                  <?php if ((int)$h['status'] === 1): ?>
                    <span class="label label-success" style="font-size:11px; padding:4px 8px;">Active</span>
                  <?php else: ?>
                    <span class="label label-default" style="font-size:11px; padding:4px 8px;">Inactive</span>
                  <?php endif; ?>
                </div>
              </div>
              <form action="<?php echo base_url(); ?>manager/system/integrations/save" method="post">
                <input type="hidden" name="service_name" value="helpdesk" />
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-6 form-group">
                      <label>Primary Support Routing Email</label>
                      <input type="email" name="config[support_email]" class="form-control" value="<?php echo htmlspecialchars($h['config']['support_email'] ?? 'support@rolextogroup.com'); ?>" required />
                    </div>
                    <div class="col-md-6 form-group">
                      <label>Escalation Manager Email</label>
                      <input type="email" name="config[escalation_email]" class="form-control" value="<?php echo htmlspecialchars($h['config']['escalation_email'] ?? 'escalations@rolextogroup.com'); ?>" />
                    </div>
                  </div>
                  <div class="form-group">
                    <label>External Ticketing Portal URL (Optional)</label>
                    <input type="url" name="endpoint" class="form-control" placeholder="https://support.rolextogroup.com" value="<?php echo htmlspecialchars($h['endpoint']); ?>" />
                  </div>
                  <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                      <option value="1" <?php if ((int)$h['status'] === 1) echo 'selected'; ?>>Enabled</option>
                      <option value="0" <?php if ((int)$h['status'] === 0) echo 'selected'; ?>>Disabled</option>
                    </select>
                  </div>
                </div>
                <div class="box-footer" style="display:flex; justify-content:space-between; align-items:center;">
                  <button type="button" class="btn btn-default btn-test-connection" data-service="helpdesk"><i class="fa fa-refresh"></i> Validate Routing</button>
                  <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Settings</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
$(document).ready(function() {
  $('.btn-test-connection').on('click', function() {
    var $btn = $(this);
    var srv = $btn.data('service');
    var originalText = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Testing...');

    $.ajax({
      url: '<?php echo base_url(); ?>manager/system/integrations/ajax_test_connection',
      type: 'POST',
      data: { service_name: srv },
      dataType: 'json',
      success: function(res) {
        $btn.prop('disabled', false).html(originalText);
        if (res && res.status) {
          alert('Success: ' + res.message);
          location.reload();
        } else {
          alert('Connection Failed: ' + (res && res.message ? res.message : 'Unknown response'));
        }
      },
      error: function() {
        $btn.prop('disabled', false).html(originalText);
        alert('Server communication error during connection test.');
      }
    });
  });
});
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
