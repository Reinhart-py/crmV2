<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
  <section class="content-header">
    <h1> <?php echo !empty($info['page_heading']) ? $info['page_heading'] : 'Admin Logs'; ?> </h1>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
      <div class="box p-4">
      <div class="x_row">
      <div class="x_col">      
          <form class="form-inline head_filter form-inline-mob" method="get" action="<?php echo base_url(); ?>manager/admin-logs">

              <div class="form-group">
                <div class="input-daterange input-group" id="datepicker" style="width: 100%;">
                  <input type="text" class="input-md form-control datepicker" name="d1" placeholder="From Date" value="<?php echo htmlspecialchars($this->input->get('d1')); ?>" autocomplete="off" />
                  <span class="input-group-addon">To</span>
                  <input type="text" class="input-md form-control datepicker" name="d2" placeholder="To Date" value="<?php echo htmlspecialchars($this->input->get('d2')); ?>" autocomplete="off" />
                </div>
              </div>

              <div class="form-group">
                <select name="user_id" class="form-control select2" style="min-width: 170px;">
                  <option value="">Select User</option>
                  <?php if (!empty($users_list)) {
                    foreach ($users_list as $u) {
                      $sel = ($this->input->get('user_id') == $u['id']) ? 'selected' : '';
                      echo '<option value="'.$u['id'].'" '.$sel.'>'.htmlspecialchars($u['name']).'</option>';
                    }
                  } ?>
                </select>
              </div>

              <div class="form-group">
                <select name="activity" class="form-control" style="min-width: 150px;">
                  <option value="">Select Activity</option>
                  <?php if (!empty($activities)) {
                    foreach ($activities as $act) {
                      $sel = ($this->input->get('activity') == $act) ? 'selected' : '';
                      echo '<option value="'.$act.'" '.$sel.'>'.ucwords(str_replace('_', ' ', $act)).'</option>';
                    }
                  } ?>
                </select>
              </div>

              <div class="form-group">
                <select name="status" class="form-control">
                  <option value="">Select Status</option>
                  <option value="SUCCESS" <?php echo ($this->input->get('status') == 'SUCCESS') ? 'selected' : ''; ?>>Success</option>
                  <option value="FAILED" <?php echo ($this->input->get('status') == 'FAILED') ? 'selected' : ''; ?>>Failed</option>
                  <option value="TIMEOUT" <?php echo ($this->input->get('status') == 'TIMEOUT') ? 'selected' : ''; ?>>Timeout</option>
                  <option value="TERMINATED" <?php echo ($this->input->get('status') == 'TERMINATED') ? 'selected' : ''; ?>>Terminated</option>
                </select>
              </div>

              <div class="form-group">
                <button type="submit" class="btn btn-success">Search</button>
                <a href="<?php echo base_url(); ?>manager/admin-logs" class="btn btn-danger"> Reset</a>
              </div>

          </form>
      </div>
      </div>
      </div>

      <div class="box">
        <div class="box-body"> <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> <?php echo get_message($this->session->flashdata('message'),'message'); ?>
          <?php if(isset($logs) && !empty($logs)) { ?>
          <div class="table-responsive">
            <table id="example_lead" class="table table-hover table-bordered table-striped" style="width:100%">
              <thead>
                <tr>
                  <th style="width: 50px;">#</th>
                  <th>Date &amp; Time</th>
                  <th>User</th>
                  <th>Activity</th>
                  <th>Status</th>
                  <th>IP Address</th>
                  <th>Location</th>
                  <th>Details</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                </tr>
              </tfoot>
              <tbody>
                <?php foreach ($logs as $log) { ?>
                <tr>
                  <td><strong><?php echo $log['id']; ?></strong></td>
                  <td>
                    <span class="no_show"><?php echo !empty($log['created_at']) ? date('YmdHi', strtotime($log['created_at'])) : ''; ?></span>
                    <?php echo !empty($log['created_at']) ? date('d M Y, h:i A', strtotime($log['created_at'])) : '-'; ?>
                  </td>
                  <td>
                    <strong><?php echo htmlspecialchars(!empty($log['username']) ? $log['username'] : 'System'); ?></strong>
                    <?php if (!empty($log['user_id'])) { ?>
                      <br><small class="text-muted">ID: <?php echo $log['user_id']; ?></small>
                    <?php } ?>
                  </td>
                  <td>
                    <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $log['activity']))); ?>
                  </td>
                  <td style="color:#000000; font-weight:500;">
                    <?php echo htmlspecialchars(ucfirst(strtolower($log['status']))); ?>
                  </td>
                  <td><code><?php echo htmlspecialchars($log['ip_address']); ?></code></td>
                  <td>
                    <?php if (!empty($log['latitude']) && !empty($log['longitude']) && is_numeric($log['latitude']) && is_numeric($log['longitude'])) { ?>
                      <a href="https://www.google.com/maps?q=<?php echo urlencode($log['latitude'] . ',' . $log['longitude']); ?>" target="_blank" class="btn btn-xs btn-default" style="font-weight:600; color:#2563eb; border-color:#bfdbfe;" title="Accuracy: <?php echo !empty($log['accuracy']) ? round($log['accuracy']) . 'm' : 'Standard'; ?>">
                        <i class="fa fa-map-marker text-danger"></i> View Map
                      </a>
                      <div style="font-size:10px; color:#64748b; margin-top:2px;">
                        <?php echo number_format((float)$log['latitude'], 4) . ', ' . number_format((float)$log['longitude'], 4); ?>
                      </div>
                    <?php } elseif (!empty($log['location_status'])) { ?>
                      <span class="label label-default" style="font-size:10px; text-transform:capitalize;"><?php echo htmlspecialchars($log['location_status']); ?></span>
                    <?php } else { ?>
                      <span class="text-muted" style="font-size:11px;">-</span>
                    <?php } ?>
                  </td>
                  <td>
                    <?php echo htmlspecialchars($log['details']); ?>
                    <?php if (!empty($log['logout_time'])) { ?>
                      <br><small class="text-muted">Logout: <?php echo date('d M Y, h:i A', strtotime($log['logout_time'])); ?></small>
                    <?php } ?>
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
          <?php } else { ?>
          <div class="alert alert-info"> No Data Found </div>
          <?php } ?>
        </div>
      </div>
      </div>
    </div>
  </section>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $('#example_lead').DataTable( {
      "pageLength": 50,
      responsive: true,
      initComplete: function () {
          this.api().columns([2,3,4]).every( function () {
              var column = this;
              var select = $('<select><option value="">Select </option></select>')
                  .appendTo( $(column.footer()).empty() )
                  .on( 'change', function () {
                      var val = $.fn.dataTable.util.escapeRegex(
                          $(this).val()
                      );
                      column
                          .search( val ? '^'+val+'$' : '', true, false )
                          .draw();
                  } );

              column.data().unique().sort().each( function ( d, j ) {
                  select.append( '<option value="'+d+'">'+d+'</option>' )
              } );
          } );
      },
      columnDefs: [
          { targets: [1, 5], className: "dt-nowrap" }
      ]
    } );
} );
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
