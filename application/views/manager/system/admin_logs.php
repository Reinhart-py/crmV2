<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
  <section class="content-header">
    <h1> <?php echo $info['page_heading']; ?> </h1>
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
                  <input type="text" class="input-md form-control datepicker" name="d1" placeholder="Create From" value="<?php if(isset($_GET['d1'])&&(!empty($_GET['d1']))){ echo htmlspecialchars($_GET['d1']);}?>" autocomplete="off"/>
                  <span class="input-group-addon">To</span>
                  <input type="text" class="input-md form-control datepicker" name="d2" placeholder="Create To" value="<?php if(isset($_GET['d2'])&&(!empty($_GET['d2']))){ echo htmlspecialchars($_GET['d2']);}?>" autocomplete="off"/>
                </div>
              </div>

              <div class="form-group">
                <select class="multiselect-ui form-control" multiple="multiple" name="activity[]" data-placeholder="Activity">
                  <?php if(isset($activities) && !empty($activities)){
                    foreach($activities as $act){
                  ?>
                    <option <?php if(isset($_GET['activity']) && !empty($_GET['activity']) && in_array($act, (array)$_GET['activity'])){ echo "selected"; } ?> value="<?php echo $act; ?>" ><?php echo ucwords(str_replace('_', ' ', $act)); ?></option>
                  <?php } } ?>
                </select>
              </div>

              <div class="form-group">
                <?php 
                  $selected_status = isset($_GET['status']) ? (array)$_GET['status'] : array();
                  if (isset($_GET['online_status']) && !empty($_GET['online_status'])) {
                    $selected_status[] = $_GET['online_status'];
                  }
                ?>
                <select class="form-control select2" name="status" style="min-width: 130px;">
                  <option value="">Status</option>
                  <option value="online" <?php if(in_array('online', $selected_status)) echo 'selected'; ?>>Online</option>
                  <option value="offline" <?php if(in_array('offline', $selected_status)) echo 'selected'; ?>>Offline</option>
                  <option value="SUCCESS" <?php if(in_array('SUCCESS', $selected_status)) echo 'selected'; ?>>SUCCESS</option>
                  <option value="FAILED" <?php if(in_array('FAILED', $selected_status)) echo 'selected'; ?>>FAILED</option>
                </select>
              </div>

              <div class="form-group">
                <select class="form-control select2" name="user_id" data-placeholder="Select User" id="select_val" style="min-width: 160px;">
                  <option value="">Select User</option>
                  <?php if(isset($users_list) && !empty($users_list)){
                    foreach($users_list as $u){
                  ?>
                    <option <?php if(isset($_GET['user_id']) && $_GET['user_id'] == $u['id']){ echo "selected"; } ?> value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['name']); ?><?php if(!empty($u['email'])){ echo ' ('.htmlspecialchars($u['email']).')'; } ?></option>
                  <?php } } ?>
                </select>
              </div>

              <div class="form-group">
                <input type="text" class="input-md form-control" name="user_name" placeholder="User Name" value="<?php if(isset($_GET['user_name'])&&(!empty($_GET['user_name']))){ echo htmlspecialchars($_GET['user_name']);}?>" autocomplete="off" />
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
        <div class="box-body">
          <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?>
          <?php echo get_message($this->session->flashdata('message'),'message'); ?>
          <?php if(isset($logs) && !empty($logs)) { ?>
          <div>
            <table id="example_lead" class="table table-hover table-bordered table-striped" style="width:100%">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>User</th>
                  <th>Activity</th>
                  <th>Status</th>
                  <th>IP Address</th>
                  <th>Logs</th>
                  <th>Date & Time</th>
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
                </tr>
              </tfoot>
              <tbody>
                <?php foreach($logs as $k=>$li){ 
                  $u_id = isset($li['user_id']) ? (int)$li['user_id'] : 0;
                  $is_user_online = (isset($user_status_map[$u_id]) && !empty($user_status_map[$u_id]['is_online']));
                ?>
                <tr>
                  <td><?php echo $li['id']; ?></td>
                  <td>
                    <strong><?php echo !empty($li['username']) ? htmlspecialchars($li['username']) : (!empty($li['user_name']) ? htmlspecialchars($li['user_name']) : 'System'); ?></strong>
                  </td>
                  <td><?php echo ucwords(str_replace('_', ' ', $li['activity'])); ?></td>
                  <td>
                    <?php if($u_id > 0) { ?>
                      <span class="label <?php echo $is_user_online ? 'label-success' : 'label-default'; ?>"><?php echo $is_user_online ? 'Online' : 'Offline'; ?></span>
                    <?php } ?>
                    <span class="label <?php echo ($li['status'] == 'SUCCESS') ? 'label-info' : 'label-danger'; ?>" style="margin-left: 3px;"><?php echo htmlspecialchars($li['status']); ?></span>
                  </td>
                  <td><?php echo htmlspecialchars($li['ip_address']); ?></td>
                  <td>
                    <?php echo htmlspecialchars($li['details']); ?>
                    <?php if (!empty($li['logout_time'])) { ?>
                      <span class="text-muted" style="font-size:11px; display:block;">Logout: <?php echo date('d M Y, h:i A', strtotime($li['logout_time'])); ?></span>
                    <?php } ?>
                  </td>
                  <td>
                    <span class="no_show"><?php echo date('YmdHis', strtotime($li['created_at'])); ?></span>
                    <?php echo date('d M Y, h:i A', strtotime($li['created_at'])); ?>
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
            <?php if (isset($links)) { echo $links; } ?>
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
    if ($.fn.select2) {
        $('.select2').select2();
    }
    if ($.fn.multiselect) {
        $('.multiselect-ui').multiselect({
            includeSelectAllOption: true,
            numberDisplayed: 1
        });
    }
    if ($.fn.datepicker) {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    }

    if ($.fn.DataTable) {
        $('#example_lead').DataTable( {
            "pageLength": 50,
            "order": [[0, "desc"]],
            responsive: true,
            initComplete: function () {
                this.api().columns([1, 2, 3]).every( function () {
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
                        var text = $('<div>').html(d).text().trim();
                        if (text != '') {
                            select.append( '<option value="'+text+'">'+text+'</option>' );
                        }
                    } );
                } );
            },
            columnDefs: [
                { targets: [4, 6], className:"dt-nowrap" }
            ]
        } );
    }
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
