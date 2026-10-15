<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper px-4"> <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1> <?php echo $info['page_heading']; ?> </h1>
  </section>
  <!-- Main content -->
  <section class="content"> <!-- /.row -->
    <div class="row"> <!-- form close-->
      <div class="col-xs-12">
      <div class="box p-4">
      <div class="x_row">
      <div class="x_col">      
          <form class="form-inline head_filter form-inline-mob" method="get" action="<?php echo base_url(); ?>manager/admin-logs">

              <div class="form-group">
                <div class="input-daterange input-group" id="datepicker" style="width: 100%;">             
                  <input type="text" class="input-md form-control datepicker" name="d1" placeholder="Create From" value="<?php if(isset($_GET['d1'])&&(!empty($_GET['d1']))){ echo $_GET['d1'];}?>" autocomplete="off"/>
                  <span class="input-group-addon">To</span>
                  <input type="text" class="input-md form-control datepicker" name="d2" placeholder="Create To" value="<?php if(isset($_GET['d2'])&&(!empty($_GET['d2']))){ echo $_GET['d2'];}?>" autocomplete="off"/>
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
                <select id="dates-field2" class="multiselect-ui form-control" multiple="multiple" name="status[]" data-placeholder="Status">
                  <option <?php if(isset($_GET['status']) && in_array('SUCCESS', (array)$_GET['status'])){ echo "selected"; } ?> value="SUCCESS">SUCCESS</option>
                  <option <?php if(isset($_GET['status']) && in_array('FAILED', (array)$_GET['status'])){ echo "selected"; } ?> value="FAILED">FAILED</option>
                </select>
              </div>

              <div class="form-group">
                <select class="form-control select2" name="user_id" data-placeholder="Select User" id="select_val">
                  <option value="">Select User</option>
                  <?php if(isset($users_list) && !empty($users_list)){
                    foreach($users_list as $u){
                  ?>
                    <option <?php if(isset($_GET['user_id']) && $_GET['user_id'] == $u['id']){ echo "selected"; } ?> value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['name']); ?><?php if(!empty($u['email'])){ echo ' ('.htmlspecialchars($u['email']).')'; } ?></option>
                  <?php } } ?>
                </select>
              </div>

              <div class="form-group">
                <input type="text" class="input-md form-control" name="user_name" placeholder="User Name" value="<?php if(isset($_GET['user_name'])&&(!empty($_GET['user_name']))){ echo $_GET['user_name'];}?>" autocomplete="off" />
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
                <?php foreach($logs as $k=>$li){ ?>
                <tr>
                  <td><?php echo $li['id']; ?></td>
                  <td><?php echo !empty($li['username']) ? htmlspecialchars($li['username']) : (!empty($li['user_name']) ? htmlspecialchars($li['user_name']) : 'System'); ?></td>
                  <td><?php echo ucwords(str_replace('_', ' ', $li['activity'])); ?></td>
                  <td><?php echo $li['status']; ?></td>
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
