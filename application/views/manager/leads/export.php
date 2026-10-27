<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<div class="content-wrapper" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
  <section class="content-header" style="padding: 20px 24px 10px 24px;">
    <h1 style="font-size: 22px; font-weight: 700; color: #0f172a; margin: 0; letter-spacing: -0.02em;">
      Export Leads
    </h1>
  </section>

  <section class="content" style="padding: 16px 24px;">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="row">
      <div class="col-md-8 col-md-offset-2 col-sm-12">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
          <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; color: #64748b; margin-bottom: 6px;">
              Total Leads Database
            </div>
            <div style="font-size: 26px; font-weight: 700; color: #0f172a; line-height: 1;">
              <?php echo number_format((int)$total_count); ?>
            </div>
          </div>
          <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; color: #64748b; margin-bottom: 6px;">
              Unassigned Leads
            </div>
            <div style="font-size: 26px; font-weight: 700; color: #0f172a; line-height: 1;">
              <?php echo number_format((int)$unassigned_count); ?>
            </div>
          </div>
        </div>

        <div class="box" style="background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.04); overflow: hidden;">
          <div class="box-header" style="padding: 18px 24px; border-bottom: 1px solid #e2e8f0;">
            <h3 class="box-title" style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0;">
              Export Database
            </h3>
          </div>

          <form action="<?php echo base_url(); ?>manager/leads/export" method="get">
            <input type="hidden" name="download" value="1">

            <div class="box-body" style="padding: 24px;">
              <div class="form-group" style="margin-bottom: 0;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 10px;">
                  Export Scope
                </label>
                <select name="user_id" class="form-control select2" style="width: 100%; height: 42px; font-size: 14px; border-radius: 6px; border-color: #cbd5e1;">
                  <option value="all" selected>All Users (Complete Database &mdash; <?php echo number_format((int)$total_count); ?> Leads)</option>
                  <option value="unassigned">Unassigned Leads (<?php echo number_format((int)$unassigned_count); ?> Leads)</option>
                  
                  <?php if (!empty($users)) { ?>
                    <optgroup label="Export by Assigned User">
                      <?php foreach ($users as $u) { 
                        $lead_cnt = isset($user_counts[$u['id']]) ? (int)$user_counts[$u['id']] : 0;
                      ?>
                        <option value="<?php echo $u['id']; ?>">
                          <?php echo htmlspecialchars($u['name']); ?> (<?php echo htmlspecialchars($u['email']); ?>) &mdash; <?php echo number_format($lead_cnt); ?> leads
                        </option>
                      <?php } ?>
                    </optgroup>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="box-footer" style="padding: 16px 24px; background: #ffffff; border-top: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: flex-start; gap: 12px;">
              <button type="submit" class="btn" style="background: #000000; color: #ffffff; border: 1px solid #000000; font-size: 13px; font-weight: 600; padding: 9px 24px; border-radius: 6px;">
                <i class="fa fa-download" style="margin-right: 6px;"></i> Download CSV
              </button>
              <a href="<?php echo base_url(); ?>manager/leads" class="btn" style="background: #ffffff; color: #475569; border: 1px solid #e2e8f0; font-size: 13px; font-weight: 600; padding: 9px 20px; border-radius: 6px;">
                Cancel
              </a>
            </div>
          </form>

        </div>
      </div>
    </div>
  </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
