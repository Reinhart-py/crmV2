<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper px-4">
  <section class="content-header">
    <h1> <?php echo isset($info['page_heading']) ? $info['page_heading'] : 'My Clients'; ?> </h1>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-body"> 
            <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
            <?php echo get_message($this->session->flashdata('message'),'message'); ?>
          
            <?php if(isset($product_list) && !empty($product_list)) { ?>
            <div>
              <table id="example_lead" class="table table-hover table-bordered table-striped" style="width:100%">
                <thead>
                  <tr>
                    <th>ID</th>                    
                    <th>Customer</th>
                    <th>Service Type</th>
                    <th>MRR</th>
                    <th>POC Name</th>
                    <th>Contact Number</th>
                    <th>Next FU date</th>
                    <th>Date</th>
                    <th>Created</th>
                    <th>Status</th>                                    
                    <th width="150">Action</th>
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
                    <th></th>
                    <th></th>                                    
                    <th></th>               
                  </tr>
                </tfoot>
                <tbody>
                  <?php foreach($product_list as $k => $li) { ?>
                  <tr>
                    <td><a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($li['id']); ?>"><strong><?php echo !empty($li['lead_id']) ? $li['lead_id'] : $li['id']; ?></strong></a></td>
                    <td>
                      <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($li['id']); ?>"><?php echo $li['customer']; ?></a>
                      <?php if(!empty($li['dnd_status']) && strtolower($li['dnd_status']) == 'yes'){ ?>
                        <span class="label label-danger" style="background:#dc2626; color:#ffffff; font-weight:700; padding:1px 5px; border-radius:3px; font-size:10px; margin-left:4px; letter-spacing:0.5px;">DNCR</span>
                      <?php } ?>
                    </td>
                    <td><?php echo isset($li['servicecat']) ? $li['servicecat'] : ''; ?></td>
                    <td><?php echo isset($li['mrr']) ? $li['mrr'] : '0'; ?></td>
                    <td><?php echo isset($li['poc']) ? $li['poc'] : ''; ?></td>
                    <td><?php echo isset($li['contact1']) ? $li['contact1'] : ''; ?></td>
                    <td><span class="no_show"><?php echo !empty($li['followup_date']) ? date('Ymd', strtotime($li['followup_date'])) : ''; ?></span><?php echo !empty($li['followup_date']) ? simple_date($li['followup_date']) : '-'; ?></td>
                    <td><span class="no_show"><?php echo !empty($li['created']) ? date('Ymd', strtotime($li['created'])) : ''; ?></span><?php echo !empty($li['created']) ? simple_date($li['created']) : '-'; ?></td>                  
                    <td>
                      <?php if (!empty($li['owner_name'])) {
                        echo htmlspecialchars($li['owner_name']);
                      } else { ?>
                        <span class="label label-warning" style="background:#f59e0b; color:#fff;">Non-Allotted</span>
                      <?php } ?>
                    </td>
                    <td><?php echo isset($li['lead_status']) ? $li['lead_status'] : (!empty($li['status']) ? $li['status'] : ''); ?></td>                                  
                    <td>
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-warning">Action</button>
                        <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                          <span class="caret"></span>
                          <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                          <li> <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($li['id']); ?>">Preview</a> </li>                  
                          <li> <a href="<?php echo base_url(); ?>manager/leads/edit/<?php echo base64_encode($li['id']); ?>">Edit</a> </li>
                        </ul>
                      </div>
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
    if ($('#example_lead').length) {
        $('#example_lead').DataTable({
            "pageLength": 50,
            responsive: true,
            initComplete: function () {
                this.api().columns([3,8,9]).every(function () {
                    var column = this;
                    var select = $('<select><option value="">Select </option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? '^'+val+'$' : '', true, false).draw();
                        });
                    column.data().unique().sort().each(function (d, j) {
                        select.append('<option value="'+d+'">'+d+'</option>');
                    });
                });
            },
            columnDefs: [
                { targets: [6,7,10], className: "dt-nowrap" }
            ]
        });
    }
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
