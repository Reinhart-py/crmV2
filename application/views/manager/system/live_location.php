<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
  <section class="content-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
    <h1 style="margin:0;"> <?php echo !empty($info['page_heading']) ? $info['page_heading'] : 'Staff Live Location'; ?> </h1>
    <div>
      <a href="<?php echo base_url(); ?>manager/admin-logs" class="btn btn-default" style="font-weight:600; display:inline-flex; align-items:center; gap:6px;">
        <i class="fa fa-arrow-left"></i> Back to Admin Logs
      </a>
    </div>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box p-4">
          <div class="x_row">
            <div class="x_col">
              <form class="form-inline head_filter form-inline-mob" method="get" action="<?php echo base_url(); ?>manager/system/live-location">
                <div class="form-group">
                  <input type="text" class="form-control" name="search" placeholder="Search staff name..." value="<?php echo htmlspecialchars($this->input->get('search') ?? ''); ?>" />
                </div>
                <div class="form-group">
                  <select name="department" class="form-control">
                    <option value="">All Departments</option>
                    <?php if (!empty($departments)): ?>
                      <?php foreach ($departments as $d): ?>
                        <option value="<?php echo htmlspecialchars($d['department']); ?>" <?php echo ($this->input->get('department') === $d['department']) ? 'selected' : ''; ?>>
                          <?php echo htmlspecialchars($d['department']); ?>
                        </option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                </div>
                <div class="form-group">
                  <button type="submit" class="btn btn-success">Search</button>
                  <a href="<?php echo base_url(); ?>manager/system/live-location" class="btn btn-danger"> Reset</a>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="box">
          <div class="box-body table-responsive">
            <?php if (!empty($locations)): ?>
              <table id="example_location" class="table table-bordered table-striped" style="width:100%;">
                <thead>
                  <tr>
                    <th style="width:40px;">#</th>
                    <th>Staff Member</th>
                    <th>Department</th>
                    <th>Contact</th>
                    <th>Today Clock In / Out</th>
                    <th>Live Coordinates</th>
                    <th>Address / Location</th>
                    <th>Last Seen</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i = 1; foreach ($locations as $loc): ?>
                    <?php 
                      $hasCoords = (!empty($loc['latitude']) && !empty($loc['longitude']) && is_numeric($loc['latitude']) && is_numeric($loc['longitude']));
                      $clockIn = !empty($loc['clock_in_time']) ? date('h:i A', strtotime($loc['clock_in_time'])) : '';
                      $clockOut = !empty($loc['clock_out_time']) ? date('h:i A', strtotime($loc['clock_out_time'])) : '';
                    ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td>
                        <strong><?php echo htmlspecialchars($loc['name'] ?? ''); ?></strong>
                        <?php if (!empty($loc['role_title'])): ?>
                          <div class="text-muted" style="font-size:11px;"><?php echo htmlspecialchars($loc['role_title']); ?></div>
                        <?php endif; ?>
                        <div class="text-muted" style="font-size:11px;"><?php echo htmlspecialchars($loc['email'] ?? ''); ?></div>
                      </td>
                      <td><?php echo htmlspecialchars(!empty($loc['department']) ? $loc['department'] : 'General'); ?></td>
                      <td><?php echo htmlspecialchars(!empty($loc['mobile']) ? $loc['mobile'] : '—'); ?></td>
                      <td>
                        <?php if (!empty($clockIn)): ?>
                          <div><span class="label label-success" style="font-size:10px;">IN</span> <?php echo $clockIn; ?></div>
                          <?php if (!empty($clockOut)): ?>
                            <div style="margin-top:3px;"><span class="label label-default" style="font-size:10px;">OUT</span> <?php echo $clockOut; ?></div>
                          <?php else: ?>
                            <div style="margin-top:3px;"><span class="label label-primary" style="font-size:10px;">WORKING</span></div>
                          <?php endif; ?>
                        <?php else: ?>
                          <span class="text-muted" style="font-size:12px;">Not clocked in today</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if ($hasCoords): ?>
                          <div style="font-weight:600; color:#0f172a; font-size:12px;">
                            <?php echo number_format((float)$loc['latitude'], 5) . ', ' . number_format((float)$loc['longitude'], 5); ?>
                          </div>
                          <?php if (!empty($loc['accuracy'])): ?>
                            <div class="text-muted" style="font-size:10px;">&plusmn; <?php echo round((float)$loc['accuracy']); ?>m accuracy</div>
                          <?php endif; ?>
                        <?php else: ?>
                          <span class="text-muted" style="font-size:11px;">No GPS lock</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if (!empty($loc['location_address'])): ?>
                          <span style="font-size:12px;"><?php echo htmlspecialchars($loc['location_address']); ?></span>
                        <?php else: ?>
                          <span class="text-muted" style="font-size:11px;">—</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if (!empty($loc['last_seen'])): ?>
                          <span style="font-size:12px;"><?php echo date('d M Y, h:i A', strtotime($loc['last_seen'])); ?></span>
                        <?php else: ?>
                          <span class="text-muted" style="font-size:11px;">—</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if ($hasCoords): ?>
                          <a href="https://www.google.com/maps?q=<?php echo urlencode($loc['latitude'].','.$loc['longitude']); ?>" target="_blank" class="btn btn-xs btn-primary" style="font-weight:600; display:inline-flex; align-items:center; gap:4px;">
                            <i class="fa fa-map-marker"></i> View on Map
                          </a>
                        <?php else: ?>
                          <button type="button" class="btn btn-xs btn-default" disabled style="opacity:0.6;">
                            <i class="fa fa-map-marker"></i> No Location
                          </button>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php else: ?>
              <div class="alert alert-info"> No Data Found </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script type="text/javascript">
$(document).ready(function() {
  if ($('#example_location').length > 0) {
    $('#example_location').DataTable({
      pageLength: 25,
      responsive: true
    });
  }
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
