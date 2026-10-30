<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
    <section class="content-header">
        <h1> <?php echo isset($info['page_heading']) ? $info['page_heading'] : 'My Clients'; ?> </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">

                <div class="box p-4">
                    <div class="x_row">
                    <div class="x_col">
                    <form class="form-inline head_filter form-inline-mob" method="get" action="<?php echo base_url(); ?>manager/kam/my-client">

<div class="form-group">
    <div class="input-daterange input-group" id="datepicker" style="width: 100%;">
        <input type="text" class="input-md form-control datepicker" name="d1"
            placeholder="Create From"
            value="<?php if(isset($_GET['d1'])&&(!empty($_GET['d1']))){ echo htmlspecialchars($_GET['d1']);}?>"
            autocomplete="off" />
        <span class="input-group-addon">To</span>
        <input type="text" class="input-md form-control datepicker" name="d2"
            placeholder="Create To"
            value="<?php if(isset($_GET['d2'])&&(!empty($_GET['d2']))){ echo htmlspecialchars($_GET['d2']);}?>"
            autocomplete="off" />
    </div>
</div>

<div class="form-group">
    <div class="input-daterange input-group" style="width: 100%;">
        <input type="text" class="input-md form-control only_number" name="mrr1" placeholder="MRR Min" value="<?php if(isset($_GET['mrr1'])&&(!empty($_GET['mrr1']) OR $_GET['mrr1']=='0')){ echo htmlspecialchars($_GET['mrr1']);}?>" autocomplete="off" />
        <span class="input-group-addon">To</span>
        <input type="text" class="input-md form-control only_number" name="mrr2" placeholder="MRR Max" value="<?php if(isset($_GET['mrr2'])&&(!empty($_GET['mrr2']))){ echo htmlspecialchars($_GET['mrr2']);}?>" autocomplete="off" />
    </div>
</div>

<div class="form-group">
    <select class="multiselect-ui form-control" multiple="multiple" name="category[]" data-placeholder="Service Type">
        <?php if(isset($pc_list) && !empty($pc_list)){
            foreach($pc_list as $pli){ ?>
                <option <?php if(isset($_GET['category']) && (is_array($_GET['category']) ? in_array($pli['id'], $_GET['category']) : $_GET['category'] == $pli['id'])){ echo "selected"; } ?>
                    value="<?php echo $pli['id']; ?>"><?php echo htmlspecialchars($pli['title']); ?></option>
        <?php } } ?>
    </select>
</div>

<div class="form-group">
    <select id="dates-field2" class="multiselect-ui form-control" multiple="multiple" name="lCat[]" data-placeholder="Lead Category">
        <?php if(isset($lc_list) && !empty($lc_list)){
            foreach($lc_list as $cli){ ?>
                <option <?php if(isset($_GET['lCat']) && (is_array($_GET['lCat']) ? in_array($cli['id'], $_GET['lCat']) : $_GET['lCat'] == $cli['id'])){ echo "selected"; } ?>
                    value="<?php echo $cli['id']; ?>"><?php echo htmlspecialchars($cli['title']); ?></option>
        <?php } } ?>
    </select>
</div>

<div class="form-group">
    <select class="multiselect-ui form-control" multiple="multiple" name="status[]" data-placeholder="Status">
        <?php if(isset($st_list) && !empty($st_list)){
            foreach($st_list as $cli){ ?>
                <option <?php if(isset($_GET['status']) && (is_array($_GET['status']) ? in_array($cli['id'], $_GET['status']) : $_GET['status'] == $cli['id'])){ echo "selected"; } ?>
                    value="<?php echo $cli['id']; ?>"><?php echo htmlspecialchars($cli['status']); ?></option>
        <?php } } ?>
    </select>
</div>

<div class="form-group">
    <input type="text" class="input-md form-control" name="user_name" placeholder="User Name" value="<?php if(isset($_GET['user_name'])&&(!empty($_GET['user_name']))){ echo htmlspecialchars($_GET['user_name']);}?>" autocomplete="off" />
</div>

<div class="form-group">
    <select class="form-control select2" name="country[]" data-placeholder="Select Country" id="select_val" onchange="ajax_get_state()">
        <option value="">Select Country</option>
        <?php if(isset($c_list) && !empty($c_list)){
            foreach($c_list as $cl){ ?>
                <option <?php if(isset($_GET['country']) && (is_array($_GET['country']) ? in_array($cl['id'], $_GET['country']) : $_GET['country'] == $cl['id'])){ echo "selected"; } ?>
                    value="<?php echo $cl['id']; ?>"><?php echo htmlspecialchars($cl['name']); ?></option>
        <?php } } ?>
    </select>
</div>

<div class="form-group">
    <select class="form-control multiselect-ui select3" multiple="multiple" name="state[]" data-placeholder="Select State" id="select_state">
        <?php if(isset($state_list) && !empty($state_list)){
            foreach($state_list as $sl){ ?>
                <option <?php if(isset($_GET['state']) && (is_array($_GET['state']) ? in_array($sl['id'], $_GET['state']) : $_GET['state'] == $sl['id'])){ echo "selected"; } ?>
                    value="<?php echo $sl['id']; ?>"><?php echo htmlspecialchars($sl['name']); ?></option>
        <?php } } ?>
    </select>
</div>

<div class="form-group">
    <input type="text" class="input-md form-control" name="search" placeholder="Search" value="<?php if(isset($_GET['search'])&&(!empty($_GET['search']))){ echo htmlspecialchars($_GET['search']);}?>" autocomplete="off" />
</div>

<div class="form-group">
    <button type="submit" class="btn btn-success">Search</button>
    <a href="<?php echo base_url(); ?>manager/kam/my-client" class="btn btn-danger">Reset</a>
</div>

                    </form>
                    </div>
                    </div>
                </div>

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
                                        <th>Created</th>
                                        <th>Status</th>
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
                                        <td><a href="<?php echo base_url(); ?>manager/kam/view/<?php echo $li['id']; ?>"><strong><?php echo !empty($li['lead_id']) ? htmlspecialchars($li['lead_id']) : $li['id']; ?></strong></a></td>
                                        <td>
                                            <a href="<?php echo base_url(); ?>manager/kam/view/<?php echo $li['id']; ?>"><strong><?php echo htmlspecialchars($li['customer']); ?></strong></a>
                                            <?php if(!empty($li['dnd_status']) && strtolower($li['dnd_status']) == 'yes'){ ?>
                                                <span class="label label-danger" style="background:#dc2626; color:#ffffff; font-weight:700; padding:1px 5px; border-radius:3px; font-size:10px; margin-left:4px; letter-spacing:0.5px;">DNCR</span>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo isset($li['servicecat']) ? htmlspecialchars($li['servicecat']) : ''; ?></td>
                                        <td><?php echo isset($li['mrr']) ? htmlspecialchars($li['mrr']) : '0'; ?></td>
                                        <td><?php echo isset($li['poc']) ? htmlspecialchars($li['poc']) : ''; ?></td>
                                        <td><?php echo isset($li['contact1']) ? htmlspecialchars($li['contact1']) : ''; ?></td>
                                        <td><span class="no_show"><?php echo !empty($li['followup_date']) ? date('Ymd', strtotime($li['followup_date'])) : ''; ?></span><?php echo !empty($li['followup_date']) ? simple_date($li['followup_date']) : '-'; ?></td>
                                        <td><span class="no_show"><?php echo !empty($li['created']) ? date('Ymd', strtotime($li['created'])) : ''; ?></span><?php echo !empty($li['created']) ? simple_date($li['created']) : '-'; ?></td>
                                        <td>
                                            <?php echo isset($li['lead_status']) ? htmlspecialchars($li['lead_status']) : ''; ?>
                                            <?php 
                                             $cat_label = !empty($li['status_category']) ? $li['status_category'] : (!empty($li['lost_reason']) ? $li['lost_reason'] : '');
                                             if (!empty($cat_label)): ?>
                                                <br><small class="text-muted" style="font-weight: 500;">Category: <?php echo htmlspecialchars($cat_label); ?></small>
                                            <?php endif; ?>
                                            <?php if (!empty($li['quote_status'])): ?>
                                                <br><span class="label label-info" style="font-size: 10px;"><?php echo htmlspecialchars($li['quote_status']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo base_url(); ?>manager/kam/view/<?php echo $li['id']; ?>" class="btn btn-sm btn-default" style="color:#0072f0; font-weight:600; border:1px solid #cbd5e1; border-radius:4px; padding:4px 12px; display:inline-block;" title="View Client Details">
                                                <i class="fa fa-eye"></i> View
                                            </a>
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
function ajax_get_state() {
    var countryID = $("#select_val").val();   
    $.ajax({
        type: "POST",
        data: 'country_id=' + countryID,
        url: "<?php echo base_url(); ?>ci_admin_leads/ajax_get_state",
        success: function(result) {
            if (result) {
                $('#select_state').html(result);
                if ($.fn.multiselect) {
                    $('#select_state').multiselect('rebuild');
                }
            }
        }
    });
}

$(document).ready(function() {
    if ($('#example_lead').length) {
        $('#example_lead').DataTable({
            "pageLength": 50,
            responsive: true,
            initComplete: function() {
                this.api().columns([2, 8]).every(function() {
                    var column = this;
                    var select = $('<select><option value="">Select </option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function() {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });
                    column.data().unique().sort().each(function(d, j) {
                        select.append('<option value="' + d + '">' + d + '</option>');
                    });
                });
            },
            columnDefs: [
                { targets: [6, 7, 9], className: "dt-nowrap" }
            ]
        });
    }
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
