<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
  <section class="content-header" style="padding: 16px 0 10px 0;">
    <h1 style="font-weight: 700; font-size: 22px; color: var(--crm-text, #0f172a);">
      <i class="fa fa-download" style="margin-right: 8px;"></i> Export Leads Data
      <small style="font-size: 13px; color: var(--crm-text-muted, #64748b);">Admin Exclusive: Filter and download leads database in CSV format</small>
    </h1>
    <ol class="breadcrumb" style="background: transparent;">
      <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?php echo base_url(); ?>manager/leads">Leads</a></li>
      <li class="active">Export</li>
    </ol>
  </section>

  <section class="content" style="padding-top: 10px;">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="row">
      <div class="col-md-8 col-md-offset-2 col-sm-12">
        <div class="box box-default" style="border-radius: 8px; border: 1px solid var(--crm-border, #e2e8f0); box-shadow: 0 4px 16px rgba(0,0,0,0.06); overflow: hidden;">
          <div class="box-header with-border" style="padding: 18px 24px; background: var(--crm-card-bg, #ffffff); border-bottom: 1px solid var(--crm-border, #e2e8f0);">
            <h3 class="box-title" style="font-weight: 700; font-size: 16px; color: var(--crm-text, #0f172a);">
              <i class="fa fa-filter" style="margin-right: 6px;"></i> Configure Leads Export
            </h3>
          </div>

          <form action="<?php echo base_url(); ?>manager/leads/export" method="get">
            <input type="hidden" name="download" value="1">

            <div class="box-body" style="padding: 24px;">
              <p style="color: var(--crm-text-muted, #64748b); font-size: 13px; line-height: 1.6; margin-bottom: 22px;">
                As an Administrator, you can export the full leads database or filter downloads by a specific user. Select your target user below to generate a real-time CSV spreadsheet.
              </p>

              <div class="form-group" style="margin-bottom: 24px;">
                <label style="font-weight: 700; font-size: 13px; margin-bottom: 8px; display: block; color: var(--crm-text, #0f172a);">
                  Select Data Scope / User Filter:
                </label>
                <select name="user_id" class="form-control select2" style="width: 100%; height: 42px; font-size: 14px;">
                  <option value="all" selected>All Users (Complete Leads Database &mdash; <?php echo (int)$total_count; ?> Total Leads)</option>
                  <option value="unassigned">Unassigned Leads Only (Not Assigned to Anyone &mdash; <?php echo (int)$unassigned_count; ?> Leads)</option>
                  
                  <?php if (!empty($users)) { ?>
                    <optgroup label="Export by Assigned User / Sales Rep">
                      <?php foreach ($users as $u) { 
                        $lead_cnt = isset($user_counts[$u['id']]) ? (int)$user_counts[$u['id']] : 0;
                      ?>
                        <option value="<?php echo $u['id']; ?>">
                          <?php echo htmlspecialchars($u['name']); ?> (<?php echo htmlspecialchars($u['email']); ?>) &mdash; <?php echo $lead_cnt; ?> leads
                        </option>
                      <?php } ?>
                    </optgroup>
                  <?php } ?>
                </select>
              </div>

              <!-- Information Callout Box -->
              <div class="callout callout-info" style="border-radius: 6px; background: #f8fafc; border-left: 4px solid #000000; color: #1e293b; padding: 14px 18px; margin-bottom: 20px;">
                <h5 style="margin-top: 0; font-weight: 700; color: #000000;">
                  <i class="fa fa-info-circle"></i> Export Guidelines
                </h5>
                <ul style="padding-left: 18px; font-size: 12.5px; margin-bottom: 0; line-height: 1.6;">
                  <li>Only verified Administrators have authorization to export data.</li>
                  <li>Selecting an individual user exports exclusively the leads currently assigned to them.</li>
                  <li>The exported file includes all contact details, MRR calculations, vertical statuses, and follow-up dates.</li>
                </ul>
              </div>

            </div>

            <div class="box-footer" style="padding: 16px 24px; background: #f8fafc; border-top: 1px solid var(--crm-border, #e2e8f0); display: flex; justify-content: space-between; align-items: center;">
              <a href="<?php echo base_url(); ?>manager/leads" class="btn btn-default" style="border-radius: 4px; font-weight: 600;">
                <i class="fa fa-arrow-left"></i> Back to Leads
              </a>
              <button type="submit" class="btn btn-primary" style="border-radius: 4px; font-weight: 700; padding: 8px 22px; background: #000000; border-color: #000000;">
                <i class="fa fa-download" style="margin-right: 6px;"></i> Download CSV Export
              </button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
