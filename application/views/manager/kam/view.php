<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$kv_h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$kam_data = array();
if (!empty($kam['kam_data'])) {
    $decoded = json_decode($kam['kam_data'], true);
    if (is_array($decoded)) $kam_data = $decoded;
}
// first non-empty value
$pick = function () { foreach (func_get_args() as $v) { if (trim((string)$v) !== '') return trim((string)$v); } return ''; };
$kd = function ($k) use ($kam_data) { return isset($kam_data[$k]) ? $kam_data[$k] : ''; };

$company = $pick($lead['customer'] ?? '', $kam['client_name'] ?? '', 'Client #' . $lead['id']);
$status = $pick($lead['company_status'] ?? '', $lead['lead_status'] ?? '', 'Active');
$client_ref = $pick($lead['lead_id'] ?? '', $lead['id']);
$tl_exp = (!empty($lead['tl_expire_date']) && strpos($lead['tl_expire_date'], '0000') !== 0) ? date('d-m-Y', strtotime($lead['tl_expire_date'])) : '';
$phone = $pick($lead['contact1'] ?? '', $lead['mobile'] ?? '', $kd('number'));
$email = $pick($lead['email'] ?? '', $kd('email'));

$groups = array(
    'Contact' => array(
        array('Group', $pick($kam['group_name'] ?? '', $kd('group_name'))),
        array('Owner', $pick($kam['owner_name'] ?? '', $lead['poc'] ?? '')),
        array('Contact person', $pick($lead['poc'] ?? '', $kd('poc'))),
        array('Phone', $phone, $phone !== '' ? 'tel:' . preg_replace('~[^0-9+]~', '', $phone) : ''),
        array('Email', $email, $email !== '' ? 'mailto:' . $email : ''),
        array('Location', $pick($lead['location'] ?? '', $kd('location'))),
    ),
    'Account' => array(
        array('Account manager', $pick($kam['executive_name'] ?? '', $lead['owner_name'] ?? '')),
        array('Collaborators', $pick($kam['collaborators'] ?? '', $lead['agents'] ?? '')),
        array('Service', $lead['servicecat'] ?? ''),
        array('MRR', 'AED ' . number_format((float)($lead['mrr'] ?? 0), 2)),
    ),
    'Trade licence & compliance' => array(
        array('Licence no.', $pick($lead['tl_number'] ?? '', $kd('tl_number'))),
        array('Emirate', $pick($lead['tl_emirate'] ?? '', $kd('tl_emirate'))),
        array('Zone', $pick($lead['tl_zone'] ?? '', $kd('tl_zone'))),
        array('Authority', $pick($lead['tl_authority'] ?? '', $kd('tl_authority'))),
        array('Licence expiry', $tl_exp),
        array('MOL employees', $kd('no_of_mol_employee')),
        array('CT registration', $kd('ct_reg')),
        array('VAT quarter', $kd('vat_qtr')),
    ),
);
$n_empty = 0;
foreach ($groups as $fs) foreach ($fs as $f) if (trim((string)$f[1]) === '') $n_empty++;
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-record.css?v=20261106_30">
<div class="content-wrapper kv">
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="svh">
      <div class="svh-top">
        <a href="<?php echo base_url('manager/kam/my-client'); ?>" class="svh-back" title="My Clients" aria-label="Back to My Clients"><i class="fa fa-arrow-left"></i></a>
        <div class="svh-id">
          <span class="svh-kind">Client · <?php echo $kv_h($client_ref); ?></span>
          <h1><?php echo $kv_h($company); ?> <span class="svh-tag is-green"><?php echo $kv_h($status); ?></span></h1>
        </div>
        <div class="svh-acts">
          <a href="<?php echo base_url('manager/kam/schedule?client=' . urlencode($company)); ?>" class="btn btn-default"><i class="fa fa-calendar"></i> Tasks</a>
          <a href="<?php echo base_url('manager/kam/task-followup?search=' . urlencode($company)); ?>" class="btn btn-primary svh-next"><i class="fa fa-tasks"></i> Task Follow-up</a>
        </div>
      </div>
    </div>

    <div class="box box-default kv-card">
      <div class="box-body svd" id="kvFields">
        <?php foreach ($groups as $gt => $fs) {
          $filled = 0; foreach ($fs as $f) if (trim((string)$f[1]) !== '') $filled++; ?>
        <div class="svd-g<?php echo $filled ? '' : ' is-empty'; ?>">
          <h4><?php echo $kv_h($gt); ?></h4>
          <div class="svd-grid">
            <?php foreach ($fs as $f) { $v = trim((string)$f[1]); ?>
            <div class="svd-f<?php echo $v === '' ? ' is-empty' : ''; ?>">
              <span class="svd-l"><?php echo $kv_h($f[0]); ?></span>
              <span class="svd-v"><?php if ($v === '') { echo '&mdash;'; } elseif (!empty($f[2])) { ?><a href="<?php echo $kv_h($f[2]); ?>"><?php echo $kv_h($v); ?></a><?php } else { echo $kv_h($v); } ?></span>
            </div>
            <?php } ?>
          </div>
        </div>
        <?php } ?>
        <?php if ($n_empty) { ?>
        <div class="svd-foot"><button type="button" class="svd-toggle" aria-expanded="false" data-n="<?php echo $n_empty; ?>"><i class="fa fa-eye"></i> Show empty fields (<?php echo $n_empty; ?>)</button></div>
        <?php } ?>
      </div>
    </div>

    <div class="box box-default kv-card">
      <div class="box-header kv-head">
        <h3 class="box-title">Upcoming tasks</h3>
        <a href="<?php echo base_url('manager/kam/task-followup?search=' . urlencode($company)); ?>">Task Follow-up</a>
      </div>
      <div class="box-body" style="padding:4px 14px 10px;">
        <?php if (!empty($upcoming_tasks)) { ?>
        <div class="table-responsive">
          <table class="table kv-table">
            <thead><tr><th>Task</th><th>Service</th><th>Frequency</th><th>Date</th><th>Assigned to</th><th>Status</th></tr></thead>
            <tbody>
              <?php foreach ($upcoming_tasks as $task) { ?>
              <tr>
                <td><b><?php echo $kv_h($task['title']); ?></b></td>
                <td><?php echo $kv_h($task['service_type'] ?: '—'); ?></td>
                <td><?php echo $kv_h($task['frequency'] ?: 'Once'); ?></td>
                <td><?php echo !empty($task['start_date']) ? date('d-m-Y', strtotime($task['start_date'])) . (!empty($task['start_time']) ? ' ' . $kv_h($task['start_time']) : '') : '—'; ?></td>
                <td><?php echo $kv_h($task['assigned_name'] ?: '—'); ?></td>
                <td><span class="label label-info"><?php echo $kv_h($task['status'] ?: 'Scheduled'); ?></span></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <?php } else { ?>
        <p class="text-muted" style="margin:6px 0;">No upcoming tasks.</p>
        <?php } ?>
      </div>
    </div>
  </section>
</div>
<style>
  .kv > .content { max-width: 1400px; margin: 0 auto; padding: 12px 16px; }
  .kv .svh { margin-bottom: 10px; }
  .kv .kv-card { border: 1px solid var(--crm-line, #e2e8f0); border-radius: 12px; box-shadow: none; margin-bottom: 12px; }
  .kv .kv-card > .svd { padding: 4px 14px 10px; }
  .kv .kv-head { display: flex; justify-content: space-between; align-items: center; padding: 9px 14px; border-bottom: 1px solid var(--crm-line-soft, #eef2f7); }
  .kv .kv-head .box-title { flex: 1; margin: 0; text-align: left; font-size: 14px; font-weight: 700; }
  .kv .kv-head a { font-size: 12.5px; font-weight: 600; }
  .kv .kv-table { margin: 0; font-size: 13px; }
  .kv .kv-table > thead > tr > th { font-size: 11.5px; font-weight: 600; color: var(--crm-muted, #64748b); border-bottom-width: 1px; }
  @media (max-width: 767px) { .kv > .content { padding: 10px; } }
</style>
<script>
$(function () {
  $('#kvFields .svd-toggle').on('click', function () {
    var $w = $('#kvFields'), on = !$w.hasClass('show-empty'), n = $(this).data('n');
    $w.toggleClass('show-empty', on);
    $(this).attr('aria-expanded', on ? 'true' : 'false').html(on ? '<i class="fa fa-eye-slash"></i> Hide empty fields' : '<i class="fa fa-eye"></i> Show empty fields (' + n + ')');
  });
});
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
