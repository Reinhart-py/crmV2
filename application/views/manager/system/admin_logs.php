<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$g = function ($k) { $v = $this->input->get($k); return is_string($v) ? $v : ''; };
$logs = isset($logs) && is_array($logs) ? $logs : array();
$cnt = array('SUCCESS' => 0, 'FAILED' => 0, 'OTHER' => 0);
foreach ($logs as $l) {
  $s = strtoupper((string)$l['status']);
  if (isset($cnt[$s])) $cnt[$s]++; else $cnt['OTHER']++;
}
$n_filters = 0;
foreach (array('d1', 'd2', 'user_id', 'activity', 'status') as $k) if ($g($k) !== '') $n_filters++;
$base = base_url() . 'manager/admin-logs';
$chip = array('SUCCESS' => 'ok', 'FAILED' => 'x', 'TIMEOUT' => 'w', 'TERMINATED' => 'na');
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<style>
  .lg-filters { padding: 12px 14px; }
  .lg-filters .sx-grid { grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); }
  .lg-filters .input-group { width: 100%; }
  .lg-filters .lg-btns { display: flex; gap: 8px; align-items: flex-end; }
  .no_show { display: none; }
  .lg-det { max-width: 340px; white-space: normal; word-break: break-word; }
  @media (max-width: 767px) { .lg-filters .lg-btns .btn { flex: 1; } .lg-det { max-width: none; } }
</style>
<div class="content-wrapper sx">
  <?php echo crm_tabbar('activity', 'admin-logs'); ?>
  <section class="sx-head">
    <h1>Admin Logs<small>Sign-ins, sign-outs and important changes, newest first (up to 2,000 entries)</small></h1>
  </section>

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'),'message'); ?>

    <div class="sx-kpis">
      <div class="sx-kpi"><span class="ic"><i class="fa fa-history"></i></span><div><b><?php echo number_format(count($logs)); ?></b><span>Entries shown</span></div></div>
      <a class="sx-kpi g <?php echo $g('status') === 'SUCCESS' ? 'is-on' : ''; ?>" href="<?php echo $base; ?>?status=SUCCESS"><span class="ic"><i class="fa fa-check"></i></span><div><b><?php echo number_format($cnt['SUCCESS']); ?></b><span>Successful</span></div></a>
      <a class="sx-kpi r <?php echo $g('status') === 'FAILED' ? 'is-on' : ''; ?>" href="<?php echo $base; ?>?status=FAILED"><span class="ic"><i class="fa fa-times"></i></span><div><b><?php echo number_format($cnt['FAILED']); ?></b><span>Failed</span></div></a>
      <div class="sx-kpi a"><span class="ic"><i class="fa fa-clock-o"></i></span><div><b><?php echo number_format($cnt['OTHER']); ?></b><span>Timed out / ended</span></div></div>
    </div>

    <div class="sx-card">
      <div class="sx-card-h">
        <h3><i class="fa fa-filter"></i> Filters <?php if ($n_filters) { ?><span class="chip"><?php echo $n_filters; ?> on</span><?php } ?></h3>
      </div>
      <form class="lg-filters" method="get" action="<?php echo $base; ?>">
        <div class="sx-grid">
          <div class="form-group">
            <label>Date</label>
            <div class="input-daterange input-group" id="datepicker">
              <input type="text" class="form-control datepicker" name="d1" placeholder="From" value="<?php echo $h($g('d1')); ?>" autocomplete="off" />
              <span class="input-group-addon">to</span>
              <input type="text" class="form-control datepicker" name="d2" placeholder="To" value="<?php echo $h($g('d2')); ?>" autocomplete="off" />
            </div>
          </div>
          <div class="form-group">
            <label>Person</label>
            <select name="user_id" class="form-control select2" style="width:100%;">
              <option value="">Everyone</option>
              <?php foreach ((array)(isset($users_list) ? $users_list : array()) as $u) { ?>
                <option value="<?php echo (int)$u['id']; ?>" <?php echo $g('user_id') === (string)$u['id'] ? 'selected' : ''; ?>><?php echo $h($u['name']); ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label>Activity</label>
            <select name="activity" class="form-control">
              <option value="">Any activity</option>
              <?php foreach ((array)(isset($activities) ? $activities : array()) as $act) { ?>
                <option value="<?php echo $h($act); ?>" <?php echo $g('activity') === $act ? 'selected' : ''; ?>><?php echo $h(ucwords(str_replace('_', ' ', $act))); ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label>Result</label>
            <select name="status" class="form-control">
              <option value="">Any result</option>
              <?php foreach (array('SUCCESS' => 'Success', 'FAILED' => 'Failed', 'TIMEOUT' => 'Timeout', 'TERMINATED' => 'Terminated') as $k => $v) { ?>
                <option value="<?php echo $k; ?>" <?php echo $g('status') === $k ? 'selected' : ''; ?>><?php echo $v; ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group lg-btns">
            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
            <a href="<?php echo $base; ?>" class="btn btn-default">Reset</a>
          </div>
        </div>
      </form>
    </div>

    <div class="sx-card">
      <?php if (!empty($logs)) { ?>
      <div class="sx-table-wrap">
        <table id="example_lead" class="sx-table sx-cards" style="width:100%">
          <thead>
            <tr>
              <th>#</th>
              <th>Date &amp; time</th>
              <th>Person</th>
              <th>Activity</th>
              <th>Result</th>
              <th>IP address</th>
              <th>Location</th>
              <th>Details</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($logs as $log) {
              $st = strtoupper((string)$log['status']);
              $hasGeo = !empty($log['latitude']) && !empty($log['longitude']) && is_numeric($log['latitude']) && is_numeric($log['longitude']);
            ?>
            <tr>
              <td data-l="#" class="mono"><?php echo (int)$log['id']; ?></td>
              <td data-l="When" class="nw" data-order="<?php echo !empty($log['created_at']) ? date('YmdHis', strtotime($log['created_at'])) : ''; ?>">
                <?php echo !empty($log['created_at']) ? date('d M Y, h:i A', strtotime($log['created_at'])) : '-'; ?>
              </td>
              <td data-l="Person">
                <span class="nm"><?php echo $h(!empty($log['username']) ? $log['username'] : 'System'); ?></span>
                <?php if (!empty($log['user_id'])) { ?><div class="sub">ID <?php echo (int)$log['user_id']; ?></div><?php } ?>
              </td>
              <td data-l="Activity"><?php echo $h(ucwords(str_replace('_', ' ', $log['activity']))); ?></td>
              <td data-l="Result"><span class="chip <?php echo isset($chip[$st]) ? $chip[$st] : 'na'; ?>"><?php echo $h(ucfirst(strtolower($st))); ?></span></td>
              <td data-l="IP" class="mono"><?php echo $h($log['ip_address']); ?></td>
              <td data-l="Location">
                <?php if ($hasGeo) { ?>
                  <a href="https://www.google.com/maps?q=<?php echo urlencode($log['latitude'] . ',' . $log['longitude']); ?>" target="_blank" rel="noopener" title="Accuracy: <?php echo !empty($log['accuracy']) ? round($log['accuracy']) . 'm' : 'standard'; ?>"><i class="fa fa-map-marker"></i> View map</a>
                  <div class="sub mono"><?php echo number_format((float)$log['latitude'], 4) . ', ' . number_format((float)$log['longitude'], 4); ?></div>
                <?php } elseif (!empty($log['location_status'])) { ?>
                  <span class="chip na"><?php echo $h(ucfirst($log['location_status'])); ?></span>
                <?php } else { ?>
                  <span class="sub">-</span>
                <?php } ?>
              </td>
              <td data-l="Details" class="full">
                <div class="lg-det"><?php echo $h($log['details']); ?></div>
                <?php if (!empty($log['logout_time'])) { ?><div class="sub">Signed out <?php echo date('d M Y, h:i A', strtotime($log['logout_time'])); ?></div><?php } ?>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <?php } else { ?>
        <div class="sx-card-b"><div class="sx-empty"><i class="fa fa-history"></i> No log entries match these filters.</div></div>
      <?php } ?>
    </div>
  </section>
</div>
<script type="text/javascript">
$(function () {
  if (!$('#example_lead').length) return;
  $('#example_lead').DataTable({
    pageLength: 50,
    order: [[0, 'desc']],
    autoWidth: false,
    language: { search: '', searchPlaceholder: 'Search these entries' }
  });
});
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
