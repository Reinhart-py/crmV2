<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
  <section class="content-header">
    <h1> <?php echo !empty($info['page_heading']) ? $info['page_heading'] : 'Staff Live Location'; ?> </h1>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box p-4">
          <div class="x_row">
            <div class="x_col">
              <form class="form-inline head_filter form-inline-mob" method="get" action="<?php echo base_url(); ?>manager/system/live-location">
                <div class="form-group">
                  <input type="text" class="form-control" name="search" placeholder="Search staff name..." value="<?php echo htmlspecialchars($this->input->get('search')); ?>" />
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
            <?php if (!empty($locations)) { ?>
              <table id="example_location" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Staff Member</th>
                    <th>Department</th>
                    <th>Mobile</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Accuracy</th>
                    <th>Last Check-in</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i = 1; foreach ($locations as $loc) { ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><strong><?php echo htmlspecialchars($loc['name']); ?></strong></td>
                      <td><?php echo htmlspecialchars(!empty($loc['department']) ? $loc['department'] : 'General'); ?></td>
                      <td><?php echo htmlspecialchars(!empty($loc['mobile']) ? $loc['mobile'] : '—'); ?></td>
                      <td><?php echo htmlspecialchars($loc['latitude']); ?></td>
                      <td><?php echo htmlspecialchars($loc['longitude']); ?></td>
                      <td><?php echo !empty($loc['accuracy']) ? htmlspecialchars($loc['accuracy']).'m' : '—'; ?></td>
                      <td><?php echo htmlspecialchars($loc['updated_at']); ?></td>
                      <td>
                        <a href="https://www.google.com/maps?q=<?php echo urlencode($loc['latitude'].','.$loc['longitude']); ?>" target="_blank" class="btn btn-sm btn-primary">
                          <i class="fa fa-map-marker"></i> View on Map
                        </a>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            <?php } else { ?>
              <div class="alert alert-info"> No Data Found </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
