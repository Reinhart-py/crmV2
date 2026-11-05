<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$g = function ($k) { $v = $this->input->get($k); return is_string($v) ? $v : ''; };
$locations = isset($locations) && is_array($locations) ? $locations : array();
$k_in = 0; $k_work = 0; $k_gps = 0;
foreach ($locations as $l) {
  if (!empty($l['clock_in_time'])) { $k_in++; if (empty($l['clock_out_time'])) $k_work++; }
  if (!empty($l['latitude']) && !empty($l['longitude']) && is_numeric($l['latitude']) && is_numeric($l['longitude'])) $k_gps++;
}
$base = base_url() . 'manager/system/live-location';
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<div class="content-wrapper sx">
  <?php echo crm_tabbar('activity', 'system/live-location'); ?>
  <section class="sx-head">
    <h1>Staff Live Location<small>Last known position of each person and today's clock in / out</small></h1>
  </section>

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'),'message'); ?>

    <div class="sx-kpis">
      <div class="sx-kpi"><span class="ic"><i class="fa fa-users"></i></span><div><b><?php echo number_format(count($locations)); ?></b><span>Staff listed</span></div></div>
      <div class="sx-kpi g"><span class="ic"><i class="fa fa-sign-in"></i></span><div><b><?php echo number_format($k_work); ?></b><span>Working now</span></div></div>
      <div class="sx-kpi s"><span class="ic"><i class="fa fa-calendar-check-o"></i></span><div><b><?php echo number_format($k_in); ?></b><span>Clocked in today</span></div></div>
      <div class="sx-kpi a"><span class="ic"><i class="fa fa-map-marker"></i></span><div><b><?php echo number_format($k_gps); ?></b><span>With a GPS position</span></div></div>
    </div>

    <div class="sx-card">
      <form class="sx-bar" method="get" action="<?php echo $base; ?>">
        <input type="text" class="form-control sx-q" name="search" placeholder="Search staff name" value="<?php echo $h($g('search')); ?>" />
        <select name="department" class="form-control">
          <option value="">All departments</option>
          <?php foreach ((array)(isset($departments) ? $departments : array()) as $d) { ?>
            <option value="<?php echo $h($d['department']); ?>" <?php echo $g('department') === (string)$d['department'] ? 'selected' : ''; ?>><?php echo $h($d['department']); ?></option>
          <?php } ?>
        </select>
        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
        <a href="<?php echo $base; ?>" class="btn btn-default">Reset</a>
      </form>

      <?php if (!empty($locations)) { ?>
      <div class="sx-table-wrap">
        <table id="example_location" class="sx-table sx-cards" style="width:100%;">
          <thead>
            <tr>
              <th>Staff member</th>
              <th>Department</th>
              <th>Mobile</th>
              <th>Today</th>
              <th>Position</th>
              <th>Address</th>
              <th>Last seen</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($locations as $loc) {
              $hasCoords = (!empty($loc['latitude']) && !empty($loc['longitude']) && is_numeric($loc['latitude']) && is_numeric($loc['longitude']));
              $clockIn = !empty($loc['clock_in_time']) ? date('h:i A', strtotime($loc['clock_in_time'])) : '';
              $clockOut = !empty($loc['clock_out_time']) ? date('h:i A', strtotime($loc['clock_out_time'])) : '';
              $nm = trim((string)($loc['name'] ?? ''));
            ?>
            <tr>
              <td data-l="Staff" class="full">
                <div class="who">
                  <span class="av"><?php echo $h(strtoupper(substr($nm !== '' ? $nm : '?', 0, 1))); ?></span>
                  <div>
                    <span class="nm"><?php echo $h($nm); ?></span>
                    <div class="sub"><?php echo $h(trim(($loc['role_title'] ?? '') . (!empty($loc['role_title']) && !empty($loc['email']) ? ' · ' : '') . ($loc['email'] ?? ''))); ?></div>
                  </div>
                </div>
              </td>
              <td data-l="Department"><?php echo $h(!empty($loc['department']) ? $loc['department'] : 'General'); ?></td>
              <td data-l="Mobile" class="nw"><?php echo !empty($loc['mobile']) ? '<a href="tel:' . $h(preg_replace('/[^0-9+]/', '', $loc['mobile'])) . '">' . $h($loc['mobile']) . '</a>' : '<span class="sub">-</span>'; ?></td>
              <td data-l="Today" class="nw" data-order="<?php echo $clockIn ? ($clockOut ? 1 : 2) : 0; ?>">
                <?php if ($clockIn) { ?>
                  <span class="chip ok">In <?php echo $clockIn; ?></span>
                  <?php if ($clockOut) { ?><span class="chip na">Out <?php echo $clockOut; ?></span><?php } else { ?><span class="chip on">Working</span><?php } ?>
                <?php } else { ?>
                  <span class="chip na">Not clocked in</span>
                <?php } ?>
              </td>
              <td data-l="Position">
                <?php if ($hasCoords) { ?>
                  <span class="mono"><?php echo number_format((float)$loc['latitude'], 5) . ', ' . number_format((float)$loc['longitude'], 5); ?></span>
                  <?php if (!empty($loc['accuracy'])) { ?><div class="sub">&plusmn; <?php echo round((float)$loc['accuracy']); ?> m</div><?php } ?>
                <?php } else { ?>
                  <span class="sub">No GPS position</span>
                <?php } ?>
              </td>
              <td data-l="Address"><?php echo !empty($loc['location_address']) ? $h($loc['location_address']) : '<span class="sub">-</span>'; ?></td>
              <td data-l="Last seen" class="nw" data-order="<?php echo !empty($loc['last_seen']) ? date('YmdHis', strtotime($loc['last_seen'])) : 0; ?>"><?php echo !empty($loc['last_seen']) ? date('d M Y, h:i A', strtotime($loc['last_seen'])) : '<span class="sub">-</span>'; ?></td>
              <td data-l="" class="full">
                <?php if ($hasCoords) { ?>
                  <a href="https://www.google.com/maps?q=<?php echo urlencode($loc['latitude'] . ',' . $loc['longitude']); ?>" target="_blank" rel="noopener" class="btn btn-default btn-sm"><i class="fa fa-map-marker"></i> View on map</a>
                <?php } ?>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <?php } else { ?>
        <div class="sx-card-b"><div class="sx-empty"><i class="fa fa-map-marker"></i> No staff match these filters.</div></div>
      <?php } ?>
    </div>
  </section>
</div>

<script type="text/javascript">
$(function () {
  if (!$('#example_location').length) return;
  $('#example_location').DataTable({
    pageLength: 25,
    order: [[6, 'desc']],
    autoWidth: false,
    columnDefs: [{ targets: [7], orderable: false }],
    language: { search: '', searchPlaceholder: 'Search this list' }
  });
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
