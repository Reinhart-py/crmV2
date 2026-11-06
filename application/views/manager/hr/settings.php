<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$dis = empty($can_edit) ? ' disabled' : '';
$weekend = array_filter(explode(',', $set['weekend']));
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<style>
  .hs-days { display: flex; gap: 6px; flex-wrap: wrap; }
  .hs-days label { display: inline-flex; align-items: center; gap: 6px; height: 34px; padding: 0 12px; border: 1px solid var(--crm-line, #e2e8f0); border-radius: 999px; cursor: pointer; margin: 0; font-weight: 600; }
  .hs-days label.is-on { background: var(--crm-ocean, #1e3a8a); border-color: var(--crm-ocean, #1e3a8a); color: #fff; }
  .hs-days input { position: absolute; opacity: 0; pointer-events: none; }
  .sx .sx-grid .help-block { font-size: 12px; margin: 4px 0 0; color: var(--crm-muted, #64748b); }
</style>
<div class="content-wrapper sx">
  <?php echo crm_tabbar('hr_people', 'hr/settings'); ?>
  <section class="sx-head">
    <h1>HR Settings<small>Company details and HR policies used across HR &amp; Payroll</small></h1>
  </section>
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>
    <?php if (empty($can_edit)) { ?><div class="sx-help"><i class="fa fa-eye"></i> You can view these settings. Changing them needs Full on Company HR Settings.</div><?php } ?>

    <form action="<?php echo base_url(); ?>manager/hr/settings_save" method="post">
      <div class="sx-card">
        <div class="sx-card-h"><h3><i class="fa fa-building-o"></i> Company</h3></div>
        <div class="sx-card-b sx-grid">
          <div class="form-group"><label for="s1">Legal company name</label><input id="s1" class="form-control" name="company_name" maxlength="150" value="<?php echo $h($set['company_name']); ?>"<?php echo $dis; ?>><p class="help-block">As on the trade licence; printed on payslips and letters.</p></div>
          <div class="form-group"><label for="s2">Trade licence number</label><input id="s2" class="form-control" name="trade_licence_no" maxlength="60" value="<?php echo $h($set['trade_licence_no']); ?>"<?php echo $dis; ?>></div>
          <div class="form-group"><label for="s3">MOHRE establishment ID</label><input id="s3" class="form-control" name="mol_establishment_id" inputmode="numeric" maxlength="20" value="<?php echo $h($set['mol_establishment_id']); ?>"<?php echo $dis; ?>><p class="help-block">13-digit employer ID for the WPS salary file.</p></div>
        </div>
      </div>

      <div class="sx-card">
        <div class="sx-card-h"><h3><i class="fa fa-university"></i> Salary payment</h3></div>
        <div class="sx-card-b sx-grid">
          <div class="form-group"><label for="s4">Company bank</label><input id="s4" class="form-control" name="employer_bank_name" maxlength="120" value="<?php echo $h($set['employer_bank_name']); ?>"<?php echo $dis; ?>></div>
          <div class="form-group"><label for="s5">Company bank routing code</label><input id="s5" class="form-control" name="employer_routing_code" inputmode="numeric" maxlength="9" value="<?php echo $h($set['employer_routing_code']); ?>"<?php echo $dis; ?>><p class="help-block">9-digit routing code of the bank or exchange house that pays salaries.</p></div>
          <div class="form-group"><label for="s6">Currency</label><input id="s6" class="form-control" name="currency" maxlength="3" style="text-transform:uppercase" value="<?php echo $h($set['currency']); ?>"<?php echo $dis; ?>></div>
          <div class="form-group"><label for="s7">Salary paid on day</label><input id="s7" type="number" min="1" max="31" class="form-control" name="salary_day" value="<?php echo $h($set['salary_day']); ?>"<?php echo $dis; ?>><p class="help-block">Day of the month salaries are paid.</p></div>
        </div>
      </div>

      <div class="sx-card">
        <div class="sx-card-h"><h3><i class="fa fa-calendar"></i> Working week</h3></div>
        <div class="sx-card-b sx-grid">
          <div class="form-group" style="grid-column: 1 / -1;">
            <span class="sx-label">Weekend days</span>
            <div class="hs-days" id="hsDays">
              <?php foreach (array('Mon' => 'Monday', 'Tue' => 'Tuesday', 'Wed' => 'Wednesday', 'Thu' => 'Thursday', 'Fri' => 'Friday', 'Sat' => 'Saturday', 'Sun' => 'Sunday') as $d => $full) { $on = in_array($d, $weekend, true); ?>
                <label class="<?php echo $on ? 'is-on' : ''; ?>" title="<?php echo $full; ?>"><input type="checkbox" name="weekend[]" value="<?php echo $d; ?>"<?php echo $on ? ' checked' : ''; ?><?php echo $dis; ?>><?php echo $d; ?></label>
              <?php } ?>
            </div>
          </div>
          <div class="form-group"><label for="s8">Working hours per day</label><input id="s8" type="number" min="1" max="24" step="0.5" class="form-control" name="work_hours" value="<?php echo $h($set['work_hours']); ?>"<?php echo $dis; ?>><p class="help-block">Used for overtime and late marks.</p></div>
        </div>
      </div>

      <div class="sx-card">
        <div class="sx-card-h"><h3><i class="fa fa-file-text-o"></i> Policies</h3></div>
        <div class="sx-card-b sx-grid">
          <div class="form-group"><label for="s9">Probation (months)</label><input id="s9" type="number" min="0" max="12" class="form-control" name="probation_months" value="<?php echo $h($set['probation_months']); ?>"<?php echo $dis; ?>><p class="help-block">UAE law allows at most 6 months.</p></div>
          <div class="form-group"><label for="s10">Notice period (days)</label><input id="s10" type="number" min="0" max="180" class="form-control" name="notice_days" value="<?php echo $h($set['notice_days']); ?>"<?php echo $dis; ?>><p class="help-block">Default for new contracts; 30 to 90 days in the UAE.</p></div>
          <div class="form-group"><label for="s11">Annual leave (days a year)</label><input id="s11" type="number" min="0" max="60" class="form-control" name="annual_leave_days" value="<?php echo $h($set['annual_leave_days']); ?>"<?php echo $dis; ?>><p class="help-block">UAE: 30 days after one year of service.</p></div>
          <div class="form-group"><label for="s12">Warn about documents expiring within (days)</label><input id="s12" type="number" min="1" max="365" class="form-control" name="doc_alert_days" value="<?php echo $h($set['doc_alert_days']); ?>"<?php echo $dis; ?>></div>
        </div>
        <?php if (!empty($can_edit)) { ?>
        <div class="sx-card-f" style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
          <span class="text-muted" style="font-size:12.5px;"><?php if (!empty($last['updated_at'])) echo 'Last changed ' . $h(date('d-m-Y H:i', strtotime($last['updated_at']))) . (!empty($last['name']) ? ' by ' . $h($last['name']) : ''); ?></span>
          <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Save settings</button>
        </div>
        <?php } ?>
      </div>
    </form>
  </section>
</div>
<script>
document.querySelectorAll('#hsDays input').forEach(function (i) {
  i.addEventListener('change', function () { i.parentNode.classList.toggle('is-on', i.checked); });
});
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
