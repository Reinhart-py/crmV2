<?php require_once(APPPATH."views/manager/elements/header.php"); ?>


<?php
$ldv_seg = $this->uri->segment(3);
$ldv_title = (isset($stage) && $stage === 'database' && $info['page_heading'] === 'All Leads') ? 'Database' : $info['page_heading'];
$ldv_add = crm_can('database', 'write') && !($ldv_seg == 'followups' || $ldv_seg == 'meetings' || $ldv_seg == 'review_leads' || (isset($stage) && $stage === 'review_leads'));
$ldv_n = (isset($product_list) && is_array($product_list)) ? count($product_list) : 0;
$ldv_on = 0;
foreach (array('d1', 'd2', 'mrr1', 'mrr2', 'category', 'lCat', 'status', 'user_name', 'country', 'state', 'type') as $ldv_k) {
    if (isset($_GET[$ldv_k]) && $_GET[$ldv_k] !== '' && $_GET[$ldv_k] !== array() && $_GET[$ldv_k] !== array('')) $ldv_on++;
}
?>
<?php require_once(APPPATH . 'views/manager/leads/list_request.php'); ?>
<div class="content-wrapper px-4 ldv">
<?php echo crm_mz_tabbar(); ?>

    <section class="ldv-head">
        <h1><?php echo htmlspecialchars($ldv_title); ?><small><?php echo $ldv_n; ?> <?php echo $ldv_n === 1 ? 'record' : 'records'; ?><?php echo $ldv_on ? ' &middot; ' . $ldv_on . ' filter' . ($ldv_on === 1 ? '' : 's') . ' on' : ''; ?></small></h1>
        <div class="ldv-head-btns">
            <button type="button" class="btn btn-default ldv-fbtn" id="ldvFilterBtn" aria-controls="ldvFilters" aria-expanded="false"><i class="fa fa-filter"></i> Filters<?php if ($ldv_on) { ?> <span class="ldv-badge"><?php echo $ldv_on; ?></span><?php } ?></button>
            <?php if ($ldv_add) { ?><a href="<?php echo base_url(); ?>manager/leads/add" class="btn btn-primary"><i class="fa fa-plus"></i> Add New</a><?php } ?>
        </div>
    </section>

    <section class="content">

        <div class="row">

            <div class="col-xs-12">


                <div class="box  p-4 ldv-card ldv-filters" id="ldvFilters">
                    <div class="x_row">

                    <div class="x_col">
                    <form class="form-inline head_filter form-inline-mob" method="get">

<div class="form-group">

    <div class="input-daterange input-group" id="datepicker" style="width: 100%;">
        <?php if($this->uri->segment(3)=='followups') { ?>

        <select class="form-control" name="type">
            <option value="">Time Period</option>
            <option
                <?php if(isset($_GET['type']) && !empty($_GET['type']) && $_GET['type']==1){ echo "selected"; } ?>
                value="1">All Missed</option>
            <option
                <?php if(isset($_GET['type']) && !empty($_GET['type']) && $_GET['type']==2){ echo "selected"; } ?>
                value="2">Last 7 Days</option>
            <option
                <?php if(isset($_GET['type']) && !empty($_GET['type']) && $_GET['type']==3){ echo "selected"; } ?>
                value="3">Today</option>
            <option
                <?php if(isset($_GET['type']) && !empty($_GET['type']) && $_GET['type']==4){ echo "selected"; } ?>
                value="4">Next 7 Days</option>
            <option
                <?php if(isset($_GET['type']) && !empty($_GET['type']) && $_GET['type']==5){ echo "selected"; } ?>
                value="5">All Futures</option>
        </select>

        <?php }elseif($this->uri->segment(3)=='meetings') { ?>

        <select class="form-control" name="type">
            <option value="">Time Period</option>
            <option
                <?php if(isset($_GET['type']) && !empty($_GET['type']) && $_GET['type']==1){ echo "selected"; } ?>
                value="1">All Missed</option>
            <option
                <?php if(isset($_GET['type']) && !empty($_GET['type']) && $_GET['type']==2){ echo "selected"; } ?>
                value="2">Last 7 Days</option>
            <option
                <?php if(isset($_GET['type']) && !empty($_GET['type']) && $_GET['type']==3){ echo "selected"; } ?>
                value="3">Today</option>
            <option
                <?php if(isset($_GET['type']) && !empty($_GET['type']) && $_GET['type']==4){ echo "selected"; } ?>
                value="4">Next 7 Days</option>
            <option
                <?php if(isset($_GET['type']) && !empty($_GET['type']) && $_GET['type']==5){ echo "selected"; } ?>
                value="5">All Futures</option>
        </select>

        <?php } else { ?>

        <input type="text" class="input-md form-control datepicker" name="d1"
            placeholder="Create From"
            value="<?php if(isset($_GET['d1'])&&(!empty($_GET['d1']))){ echo $_GET['d1'];}?>"
            autocomplete="off" />
        <span class="input-group-addon">To</span>
        <input type="text" class="input-md form-control datepicker" name="d2"
            placeholder="Create To"
            value="<?php if(isset($_GET['d2'])&&(!empty($_GET['d2']))){ echo $_GET['d2'];}?>"
            autocomplete="off" />

        <?php } ?>

    </div>
</div>

<div class="form-group">
    <div class="input-daterange input-group" style="width: 100%;">
    <input type="text" class="input-md form-control only_number" name="mrr1" placeholder="MRR Min" value="<?php if(isset($_GET['mrr1'])&&(!empty($_GET['mrr1']) OR $_GET['mrr1']=='0')){ echo $_GET['mrr1'];}?>"  autocomplete="off" />
    <span class="input-group-addon">To</span>
    <input type="text" class="input-md form-control only_number" name="mrr2" placeholder="MRR Max" value="<?php if(isset($_GET['mrr2'])&&(!empty($_GET['mrr2']))){ echo $_GET['mrr2'];}?>"  autocomplete="off" />
    
    </div>
</div>




<div class="form-group">

    <select class="multiselect-ui form-control" multiple="multiple" name="category[]"
        data-placeholder="Service Type">
        
        <?php if(isset($pc_list) && !empty($pc_list)){
foreach($pc_list as $pli){
?>
        <option
            <?php if(isset($_GET['category']) && (is_array($_GET['category']) ? in_array($pli['id'], $_GET['category']) : $_GET['category'] == $pli['id'])){ echo "selected"; } ?>
            value="<?php echo $pli['id']; ?>"><?php echo $pli['title']; ?></option>
        <?php } }  ?>

    </select>

</div>

<div class="form-group">

    <select id="dates-field2" class="multiselect-ui form-control" multiple="multiple"
        name="lCat[]" data-placeholder="Lead Category">
        <?php if(isset($lc_list) && !empty($lc_list)){
        foreach($lc_list as $cli){
        ?>
        <option
            <?php if(isset($_GET['lCat']) && (is_array($_GET['lCat']) ? in_array($cli['id'], $_GET['lCat']) : $_GET['lCat'] == $cli['id'])){ echo "selected"; } ?>
            value="<?php echo $cli['id']; ?>"><?php echo $cli['title']; ?></option>
        <?php } }  ?>
    </select>

</div>

<div class="form-group">

    <select id="dates-field2" class="multiselect-ui form-control" multiple="multiple"
        name="status[]" data-placeholder="Status">
        <?php if(isset($st_list) && !empty($st_list)){
foreach($st_list as $cli){
?>
        <option
            <?php if(isset($_GET['status']) && (is_array($_GET['status']) ? in_array($cli['id'], $_GET['status']) : $_GET['status'] == $cli['id'])){ echo "selected"; } ?>
            value="<?php echo $cli['id']; ?>"><?php echo $cli['status']; ?></option>
        <?php } }  ?>
    </select>
</div>

<div class="form-group">
    <input type="text" class="input-md form-control" name="user_name" placeholder="User Name" value="<?php if(isset($_GET['user_name'])&&(!empty($_GET['user_name']))){ echo $_GET['user_name'];}?>"  autocomplete="off" />
</div>

<div class="form-group">
    <select class="form-control select2" name="country[]" data-placeholder="Select Country" id="select_val" onchange="ajax_get_state()">
    <option value="">Select Country</option>
        <?php if(isset($c_list) && !empty($c_list)){
        foreach($c_list as $c_list){
        ?>
        <option <?php if(isset($_GET['country']) && !empty($_GET['country']) && in_array($c_list['id'], $_GET['country'])){ echo "selected"; } ?>
            value="<?php echo $c_list['id']; ?>"><?php echo $c_list['name']; ?></option>
        <?php } }  ?>
    </select>
</div>


<div class="form-group" >
    <select  class="form-control multiselect-ui select3" multiple="multiple" name="state[]"  data-placeholder="Select State" id="select_state">
    <?php if(isset($state_list) && !empty($state_list)){
        foreach($state_list as $state_list){
        ?>
        <option <?php if(isset($_GET['state']) && !empty($_GET['state']) && in_array($state_list['id'], $_GET['state'])){ echo "selected"; } ?>
            value="<?php echo $state_list['id']; ?>"><?php echo $state_list['name']; ?></option>
        <?php } }  ?>
    </select>
</div>




<?php  ?>

<div class="form-group">
        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
    <a href="<?php echo base_url(); ?>manager/leads<?php if($this->uri->segment(3)=='followups'){ echo '/followups'; }elseif($this->uri->segment(3)=='meetings'){ echo '/meetings'; } ?>"
        class="btn btn-danger"> Reset</a>
</div>







</form>
                    </div>
                    <div class="x_col-sm-auto  text-center pt-4 pt-md-0">
                    <div>
                            <?php if($this->uri->segment(3)=='followups'){ ?>

                            


                            <?php } 
       elseif($this->uri->segment(3)=='meetings' || $this->uri->segment(3)=='review_leads' || (isset($stage) && $stage === 'review_leads')){ ?>

                            <?php } else { ?>

                            <a href="<?php echo base_url(); ?>manager/leads/add" class="btn btn-success"> Add New</a>
                            <?php } ?>


                        </div>
                    </div>  
                    
                    </div>
                       
      
                </div>


                                <div class="box ldv-card ldv-list">


                    <div class="box-body">
                        <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?>
                        <?php echo get_message($this->session->flashdata('message'),'message'); ?>
                        <?php if(isset($product_list) && !empty($product_list))       {?>
                        <div>
                            <table id="example_lead" class="table table-hover table-bordered table-striped"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Database No.</th>
                                        <th>Customer</th>
                                        <th>Service Type</th>
                                        <th>MRR</th>
                                        <?php if(isset($stage) && $stage === 'review_leads'){ ?>
                                            <th>Requested By</th>
                                            <th>Requested Date</th>
                                            <th>Rationale</th>
                                            <th>Status</th>
                                        <?php } else { ?>
                                            <th>POC Name</th>
                                            <th>Contact Number</th>
                                            <th>Next FU date</th>
                                            <th>Created Date</th>
                                            <th>Status</th>
                                        <?php } ?>
                                        <th>Action</th>
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



                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php foreach($product_list as $k=>$li){ ?>
                                    <tr>

                                        <td><a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($li['id']); ?>"><strong><?php echo !empty($li['db_ref_no']) ? htmlspecialchars($li['db_ref_no']) : ('D' . str_pad($li['id'], 10, '0', STR_PAD_LEFT)); ?></strong></a></td>
                                        <td>
                                            <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($li['id']); ?>"><?php echo $li['customer']; ?></a>
                                            <?php if(!empty($li['dnd_status']) && strtolower($li['dnd_status']) == 'yes'){ ?>
                                                <span class="label label-danger" style="background:#dc2626; color:#ffffff; font-weight:700; padding:1px 5px; border-radius:3px; font-size:10px; margin-left:4px; letter-spacing:0.5px;">DNCR</span>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo $li['servicecat']; ?></td>
                                        <td><?php echo $li['mrr']; ?></td>
                                        <?php if(isset($stage) && $stage === 'review_leads'){ ?>
                                            <td><?php echo htmlspecialchars($li['requested_by_name'] ?? 'Staff'); ?></td>
                                            <td><?php echo !empty($li['conversion_requested_at']) ? date('d-m-Y H:i', strtotime($li['conversion_requested_at'])) : '-'; ?></td>
                                            <td><span title="<?php echo htmlspecialchars($li['conversion_reason'] ?? ''); ?>"><?php echo htmlspecialchars(mb_strimwidth($li['conversion_reason'] ?? '-', 0, 35, '...')); ?></span></td>
                                            <td><span class="label label-warning" style="background:#475569; color:#ffffff; font-weight:600; font-size:11px;">Pending Review</span></td>
                                            <td>
                                                                                                <div class="ldv-act">
                                                <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($li['id']); ?>" class="btn btn-default btn-sm" title="Preview" aria-label="Preview"><i class="fa fa-eye"></i><span> Preview</span></a>
                                                <a href="<?php echo base_url(); ?>manager/leads/edit/<?php echo base64_encode($li['id']); ?>" class="btn btn-default btn-sm" title="Edit" aria-label="Edit"><i class="fa fa-pencil"></i><span> Edit</span></a>
                                                </div>
                                            </td>
                                        <?php } else { ?>
                                            <td><?php echo $li['poc']; ?></td>
                                            <td><?php echo $li['contact1']; ?></td>
                                            <td><span class="no_show"><?php echo date('Ymd',strtotime($li['followup_date'])); ?></span><?php echo simple_date($li['followup_date']); ?></td>
                                            <td><span class="no_show"><?php echo date('Ymd',strtotime($li['created'])); ?></span><?php echo simple_date($li['created']); ?></td>
                                            <td>
                                                <?php echo $li['lead_status']; ?>
                                                <?php 
                                                $cat_label = !empty($li['status_category']) ? $li['status_category'] : (!empty($li['lost_reason']) ? $li['lost_reason'] : '');
                                                if (!empty($cat_label)): ?>
                                                    <br><small class="text-muted" style="font-weight: 500;">Category: <?php echo htmlspecialchars($cat_label); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="ldv-act">
                                                    <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($li['id']); ?>" class="btn btn-default btn-sm" title="Preview" aria-label="Preview"><i class="fa fa-eye"></i><span> Preview</span></a>
                                                    <a href="<?php echo base_url(); ?>manager/leads/edit/<?php echo base64_encode($li['id']); ?>" class="btn btn-default btn-sm" title="Edit" aria-label="Edit"><i class="fa fa-pencil"></i><span> Edit</span></a>
                                                    <?php if (isset($stage) && $stage === 'database') echo crm_list_request_btn($li); ?>
                                                </div>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <?php if (isset($links)) {  echo $links; } ?>
                        </div>
                        <?php } else{  ?>

                        <div class="alert alert-info"> No Data Found </div>
                        <?php }?>
                        
                    </div>
                </div>
                
            </div>
        </div>
    </section>
</div>
<script>
function do_confirm() {
    job = confirm("Are you sure to delete this record... ");
    if (job != true) {
        return false;
    }
}
</script>
<script type="text/javascript">

$(document).ready(function()
{
  $('#select_val').on('change', function() {
    var value = $(this).val();
    if ($(this).val() == (value)) {
          $("#select_div").show();
      } else {
          $("#select_div").hide();
      }
  });
});

function ajax_get_state()
{
  var countryID = $("#select_val").val();   
    $.ajax({
          type: "POST",
    data: 'country_id='+countryID,
      url: "<?php echo base_url(); ?>" + "ci_admin_leads/ajax_get_state",
    success:function(result){
    if(result)
    {
    $('#select_state').html(result);
		$('#select_state').multiselect('rebuild');	
    }
    else
    {
    alert('error');
    }
    }
    });
}

$(document).ready(function() {
    $(document).on("click", "#btn_block", function() {

        var id = $(this).data("id");
        var admin_status = $(this).data("admin_status");

        if (confirm("Are you sure to unblock User?")) {

            $.ajax({
                type: "POST",
                url: '<?php  echo base_url(); ?>ci_admin_user/ajax_edit_status',
                data: "admin_status=" + admin_status + "&id=" +
                id, 

            }).done(function(msg) {

                if (msg == 'success' || msg == 'ok') {
                    location.reload();
                } else {
                    alert("Error : Not update Confirm status")
                }
            });
        }
    });

});

$(document).ready(function() {
    $(document).on("click", "#btn_unblock", function() {

        var id = $(this).data("id");
        var admin_status = $(this).data("admin_status");

        if (confirm("Are you sure to block User?")) {

            $.ajax({
                type: "POST",
                url: '<?php  echo base_url(); ?>ci_admin_user/ajax_edit_status',
                data: "admin_status=" + admin_status + "&id=" +
                id, 

            }).done(function(msg) {

                if (msg == 'success' || msg == 'ok') {
                    location.reload();
                } else {
                    alert("Error : Not update Confirm status")
                }
            });
        }
    });

});


$(".remove").click(function() {
    var id = $(this).data('value');
    swal({
            title: "Are you sure?",
            text: "You want to delete this lead!",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: '<?php echo base_url(); ?>manager/deleteleads/' + id,
                    type: 'GET',
                    error: function() {
                        alert('Something is wrong');
                    },
                    success: function(data) {
                        $("#"+id).remove();
                        swal("Deleted!", "", "success");
                    }
                });
            }
        });

});

$(document).ready(function() {
    $('#example_lead').DataTable({
                "pageLength": 50,
        responsive: window.innerWidth >= 768,

        initComplete: function() {
            this.api().columns([2, 8]).every(function() {

                var column = this;
                var select = $('<select><option value="">Select </option></select>')
                    .appendTo($(column.footer()).empty())
                    .on('change', function() {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );

                        column
                            .search(val ? '^' + val + '$' : '', true, false)
                            .draw();
                    });

                column.data().unique().sort().each(function(d, j) {
                    select.append('<option value="' + d + '">' + d + '</option>')
                });
            });
        },
		columnDefs: [
        	{ targets: [6,7,9], className:"dt-nowrap",}       
    	]
    });
});
</script>

<style>
/* Database list: clearer look (same filters, table, sorting, paging and links) */
.ldv, .ldv .btn, .ldv .form-control, .ldv table{font-family:"Plus Jakarta Sans","Inter",-apple-system,"Segoe UI",sans-serif}
.ldv .ldv-head{display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;padding:14px 15px 8px}
.ldv .ldv-head h1{margin:0;font-size:22px;font-weight:700;letter-spacing:-.02em;color:var(--crm-ink,#0f172a);text-align:left}
.ldv .ldv-head h1 small{display:block;margin-top:2px;font-size:13px;font-weight:500;color:var(--crm-muted,#64748b);letter-spacing:0}
.ldv .ldv-head-btns{display:flex;gap:8px;align-items:center}
.ldv .ldv-head-btns .btn{height:36px;font-weight:600;border-radius:8px;display:inline-flex;align-items:center;gap:6px}
.ldv .ldv-head-btns .btn-primary{background:var(--crm-ocean,#1e3a8a);border-color:var(--crm-ocean,#1e3a8a)}
.ldv .ldv-fbtn{display:none !important}
.ldv .ldv-badge{display:inline-block;min-width:18px;height:18px;line-height:18px;padding:0 5px;border-radius:9px;background:var(--crm-ocean,#1e3a8a);color:#fff;font-size:11px;text-align:center}
.ldv .content{padding-top:4px}
.ldv .ldv-card{border:1px solid var(--crm-line,#e2e8f0);border-radius:10px;box-shadow:none;background:var(--crm-surface,#fff);margin-bottom:12px}
.ldv .x_col-sm-auto{display:none}
/* Filters: tidy grid, same fields */
.ldv .ldv-filters{padding:12px !important}
.ldv .ldv-filters .x_row,.ldv .ldv-filters .x_col{display:block;width:100%;margin:0;padding:0}
.ldv .head_filter{display:grid !important;grid-template-columns:repeat(auto-fill,minmax(190px,1fr));gap:8px;align-items:center}
.ldv .head_filter > .form-group{margin:0 !important;width:auto !important;display:block;min-width:0}
.ldv .head_filter > .form-group *{max-width:100%}
.ldv .head_filter .multiselect-native-select{position:relative}
.ldv .head_filter .multiselect-native-select > select{left:0 !important;width:100% !important;max-width:100% !important}
.ldv .head_filter .input-group > .form-control,.ldv .head_filter .input-group > .input-group-addon{max-width:none}
.ldv .head_filter > .form-group:nth-child(1),.ldv .head_filter > .form-group:nth-child(2){grid-column:span 2}
.ldv .head_filter > .form-group:last-child{display:flex;gap:8px}
.ldv .head_filter .form-control{max-width:none !important}
.ldv .head_filter .form-control,.ldv .head_filter .btn,.ldv .head_filter .select2-container .select2-selection--single{height:34px !important;border-radius:7px;font-size:13px}
.ldv .head_filter .input-group{display:flex !important;width:100% !important}
.ldv .head_filter .input-group .form-control{flex:1 1 0;min-width:0;max-width:none !important;width:auto !important;border-radius:7px 0 0 7px}
.ldv .head_filter .input-group .form-control:last-child{border-radius:0 7px 7px 0}
.ldv .head_filter .input-group-addon{display:flex;align-items:center;width:auto;padding:0 8px;font-size:12px;color:var(--crm-muted,#64748b);background:var(--crm-canvas,#f8fafc)}
.ldv .head_filter .btn-group,.ldv .head_filter .multiselect-native-select,.ldv .head_filter .multiselect-native-select > .btn-group{display:block;width:100% !important}
.ldv .head_filter .btn-group > .multiselect{width:100% !important;text-align:left;display:flex;align-items:center;justify-content:space-between;overflow:hidden;background:var(--crm-surface,#fff);font-weight:500}
.ldv .head_filter .select2-container{width:100% !important}
.ldv .head_filter > .form-group:last-child .btn{flex:1 1 0;font-weight:600}
.ldv .head_filter .btn-success{background:var(--crm-ocean,#1e3a8a);border-color:var(--crm-ocean,#1e3a8a)}
/* Table */
.ldv .ldv-list > .box-body{padding:12px !important}
.ldv #example_lead_wrapper .dataTables_length select,.ldv #example_lead_wrapper .dataTables_filter input{height:32px;border-radius:7px;border:1px solid var(--crm-line,#cbd5e1)}
.ldv #example_lead{border:0 !important;border-collapse:separate;border-spacing:0}
.ldv #example_lead > thead > tr > th{text-align:left !important;font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;padding:9px 10px !important;white-space:nowrap;vertical-align:middle}
.ldv #example_lead > tfoot > tr > th{padding:6px 10px !important;border:0 !important;background:var(--crm-canvas,#f8fafc)}
.ldv #example_lead > tfoot select{width:100%;max-width:170px;height:28px;border-radius:6px;border:1px solid var(--crm-line,#cbd5e1);font-size:12px;padding:0 6px}
.ldv #example_lead > tbody > tr > td{text-align:left !important;font-size:13.5px;padding:9px 10px !important;vertical-align:middle !important;border-left:0 !important;border-right:0 !important;border-color:var(--crm-line-soft,#eef2f7) !important}
.ldv #example_lead > tbody > tr > td:first-child a{font-weight:700;white-space:nowrap}
.ldv #example_lead > tbody > tr > td:nth-child(2) a{font-weight:600;color:var(--crm-ink,#0f172a)}
.ldv #example_lead > tbody > tr:hover > td{background:var(--crm-ocean-soft,rgba(30,58,138,.05)) !important}
.ldv .ldv-st{display:inline-block;padding:2px 10px;border-radius:999px;font-size:12px;font-weight:600;background:#e0e7ff;color:#1e3a8a;white-space:nowrap}
.ldv .ldv-st.s-ok{background:#dcfce7;color:#166534}
.ldv .ldv-st.s-x{background:#fee2e2;color:#991b1b}
.ldv .ldv-st.s-w{background:#fef3c7;color:#92400e}
.ldv .ldv-st.s-na{background:#f1f5f9;color:#475569}
.ldv .ldv-act{display:flex;gap:6px;white-space:nowrap}
.ldv .ldv-act .btn{border-radius:7px;font-weight:600;font-size:12.5px}
.ldv .ldv-act .btn:hover{border-color:var(--crm-ocean,#1e3a8a);color:var(--crm-ocean,#1e3a8a)}
@media (max-width:1280px){.ldv .ldv-act .btn span{display:none}}
/* Phones: filters fold away, each record is a card with labelled values */
@media (max-width:767px){
  .ldv .ldv-head{padding:10px 2px 6px}
  .ldv .ldv-head h1{font-size:19px}
  .ldv .ldv-fbtn{display:inline-flex !important}
  .ldv .ldv-filters:not(.is-open){display:none}
  .ldv .head_filter{grid-template-columns:minmax(0,1fr)}
  .ldv .head_filter > .form-group:nth-child(1),.ldv .head_filter > .form-group:nth-child(2){grid-column:auto}
  .ldv .content{padding-left:0;padding-right:0}
  .ldv .ldv-list > .box-body{padding:8px !important}
  .ldv #example_lead_wrapper .row{margin:0}
  .ldv #example_lead_wrapper .col-sm-6,.ldv #example_lead_wrapper .col-sm-5,.ldv #example_lead_wrapper .col-sm-7,.ldv #example_lead_wrapper .col-sm-12{padding:0;width:100%;float:none;text-align:left}
  .ldv #example_lead_wrapper .dataTables_filter,.ldv #example_lead_wrapper .dataTables_filter label,.ldv #example_lead_wrapper .dataTables_filter input{width:100%;text-align:left;margin:0}
  .ldv #example_lead_wrapper .dataTables_length{margin-bottom:6px}
  .ldv #example_lead_wrapper .dataTables_paginate{text-align:center;margin-top:8px}
  .ldv #example_lead,.ldv #example_lead > tbody,.ldv #example_lead > tbody > tr,.ldv #example_lead > tbody > tr > td{display:block;width:100% !important}
  .ldv #example_lead > thead{display:none}
  .ldv #example_lead > tfoot{display:block;margin-bottom:8px}
  .ldv #example_lead > tfoot > tr{display:flex;gap:8px}
  .ldv #example_lead > tfoot > tr > th{display:none}
  .ldv #example_lead > tfoot > tr > th.ldv-has{display:block;flex:1 1 0;padding:0 !important;background:none}
  .ldv #example_lead > tfoot select{max-width:none;height:32px}
  .ldv #example_lead > tbody > tr{border:1px solid var(--crm-line,#e2e8f0);border-radius:10px;margin-bottom:10px;padding:8px 12px;background:var(--crm-surface,#fff) !important}
  .ldv #example_lead > tbody > tr > td{display:flex;gap:10px;align-items:baseline;padding:3px 0 !important;border:0 !important;background:none !important;word-break:break-word}
  .ldv #example_lead > tbody > tr > td::before{content:attr(data-l);flex:0 0 105px;font-size:12px;font-weight:500;color:var(--crm-muted,#64748b)}
  .ldv #example_lead > tbody > tr > td:first-child::before,.ldv #example_lead > tbody > tr > td:nth-child(2)::before{display:none}
  .ldv #example_lead > tbody > tr > td:nth-child(2){font-size:15px;padding-bottom:5px !important}
  .ldv #example_lead > tbody > tr > td:last-child{padding-top:8px !important}
  .ldv #example_lead > tbody > tr > td:last-child::before{display:none}
  .ldv #example_lead > tbody > tr > td:last-child .ldv-act{width:100%}
  .ldv #example_lead > tbody > tr > td:last-child .ldv-act .btn{flex:1 1 0}
  .ldv #example_lead > tbody > tr > td:last-child .ldv-act .btn span{display:inline}
  .ldv #example_lead > tbody > tr > td .no_show{display:none}
}
</style>
<script>
jQuery(function ($) {
  // Filters button (phones)
  $('#ldvFilterBtn').on('click', function () {
    var open = !$('#ldvFilters').hasClass('is-open');
    $('#ldvFilters').toggleClass('is-open', open);
    $(this).attr('aria-expanded', open ? 'true' : 'false');
  });
  var $t = $('#example_lead');
  if (!$t.length || !$.fn.dataTable || !$.fn.dataTable.isDataTable($t)) return;
  var dt = $t.DataTable();
  var heads = $t.find('thead th').map(function () { return $.trim($(this).text()); }).get();
  var stCol = heads.lastIndexOf('Status');
  // Column filters (Service Type, Status) stay visible on phones
  $t.find('tfoot th').each(function (i) { var $sel = $(this).find('select'); if ($sel.length) { $(this).addClass('ldv-has'); $sel.attr('aria-label', heads[i] || 'Filter').find('option').first().text((heads[i] || 'All') + ': all'); } });
  function chip(txt) {
    var t = txt.toLowerCase();
    if (/won|closed won|converted|approved|active|completed/.test(t)) return 's-ok';
    if (/lost|reject|dead|cancel|dncr|not interested/.test(t)) return 's-x';
    if (/pending|review|expected|follow|progress|hold/.test(t)) return 's-w';
    if (/database|new/.test(t)) return 's-na';
    return '';
  }
  // Labels for the phone cards and a status chip (display only; sorting, search and the filters use the original values)
  $(dt.rows().nodes()).each(function () {
    $(this).children('td').each(function (i) {
      $(this).attr('data-l', heads[i] || '');
      if (i === stCol && !$(this).find('.ldv-st').length) {
        var node = null;
        $.each(this.childNodes, function () { if (this.nodeType === 3 && $.trim(this.nodeValue) !== '') { node = this; return false; } });
        if (node) { var v = $.trim(node.nodeValue); $(node).replaceWith($('<span class="ldv-st">').addClass(chip(v)).text(v)); }
      }
    });
  });
});
</script>
<?php if (isset($stage) && $stage === 'database') { $crm_list_request_modal = true; require(APPPATH . 'views/manager/leads/list_request.php'); } ?>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>