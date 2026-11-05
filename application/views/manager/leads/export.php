<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_6">
<div class="content-wrapper sx">
  <section class="sx-head">
    <h1>Export Leads<small>Download database records as a CSV file for Excel</small></h1>
  </section>

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="sx-narrow">
      <div class="sx-kpis">
        <div class="sx-kpi"><span class="ic"><i class="fa fa-database"></i></span><div><b><?php echo number_format((int)$total_count); ?></b><span>Records in total</span></div></div>
        <div class="sx-kpi a"><span class="ic"><i class="fa fa-user-times"></i></span><div><b><?php echo number_format((int)$unassigned_count); ?></b><span>Not assigned to anyone</span></div></div>
      </div>

      <div class="sx-card">
        <div class="sx-card-h"><h3><i class="fa fa-download"></i> What to export</h3></div>
        <form action="<?php echo base_url(); ?>manager/leads/export" method="get">
          <input type="hidden" name="download" value="1">
          <div class="sx-card-b">
            <label for="exportScope">Records</label>
            <select name="user_id" id="exportScope" class="form-control select2" style="width:100%;">
              <option value="all" selected>Everything (<?php echo number_format((int)$total_count); ?> records)</option>
              <option value="unassigned">Not assigned to anyone (<?php echo number_format((int)$unassigned_count); ?>)</option>
              <?php if (!empty($users)) { ?>
                <optgroup label="Assigned to one person">
                  <?php foreach ($users as $u) { $lead_cnt = isset($user_counts[$u['id']]) ? (int)$user_counts[$u['id']] : 0; ?>
                    <option value="<?php echo (int)$u['id']; ?>"><?php echo htmlspecialchars($u['name']); ?> (<?php echo htmlspecialchars($u['email']); ?>) - <?php echo number_format($lead_cnt); ?></option>
                  <?php } ?>
                </optgroup>
              <?php } ?>
            </select>
            <p class="sx-help" style="margin-top:10px;"><i class="fa fa-info-circle"></i> The file opens in Excel. It contains personal contact details: keep it safe and do not share it outside the company.</p>
          </div>
          <div class="sx-card-f">
            <button type="submit" class="btn btn-primary"><i class="fa fa-download"></i> Download CSV</button>
            <a href="<?php echo base_url(); ?>manager/leads" class="btn btn-default">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
