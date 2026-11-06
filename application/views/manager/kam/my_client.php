<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$mc_h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$mc_get = function ($k) { return isset($_GET[$k]) ? $_GET[$k] : ''; };
$mc_sel = function ($k, $id) { $g = isset($_GET[$k]) ? $_GET[$k] : ''; return (is_array($g) ? in_array($id, $g) : (string)$g === (string)$id) ? ' selected' : ''; };
$n = isset($product_list) ? count($product_list) : 0;
?>
<div class="content-wrapper px-4 tlv">
<?php echo crm_mz_tabbar(); ?>
    <section class="content-header">
        <h1><?php echo $mc_h($info['page_heading'] ?? 'My Clients'); ?><small><?php echo $n; ?> clients</small></h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box p-4">
                    <div class="x_row">
                        <div class="x_col">
                            <form class="form-inline head_filter form-inline-mob" method="get" action="<?php echo base_url(); ?>manager/kam/my-client">
                                <div class="form-group">
                                    <input type="text" class="input-md form-control" name="search" placeholder="Client, ID or phone" aria-label="Search" value="<?php echo $mc_h($mc_get('search')); ?>" autocomplete="off" />
                                </div>
                                <div class="form-group">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="input-md form-control datepicker" name="d1" placeholder="Created from" aria-label="Created from" value="<?php echo $mc_h($mc_get('d1')); ?>" autocomplete="off" />
                                        <span class="input-group-addon">to</span>
                                        <input type="text" class="input-md form-control datepicker" name="d2" placeholder="Created to" aria-label="Created to" value="<?php echo $mc_h($mc_get('d2')); ?>" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="input-md form-control only_number" name="mrr1" placeholder="MRR min" aria-label="MRR min" value="<?php echo $mc_h($mc_get('mrr1')); ?>" autocomplete="off" />
                                        <span class="input-group-addon">to</span>
                                        <input type="text" class="input-md form-control only_number" name="mrr2" placeholder="MRR max" aria-label="MRR max" value="<?php echo $mc_h($mc_get('mrr2')); ?>" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <select class="multiselect-ui form-control" multiple="multiple" name="category[]" data-placeholder="Service Type" aria-label="Service type">
                                        <?php foreach ((array)($pc_list ?? array()) as $o) { ?><option value="<?php echo $o['id']; ?>"<?php echo $mc_sel('category', $o['id']); ?>><?php echo $mc_h($o['title']); ?></option><?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="multiselect-ui form-control" multiple="multiple" name="lCat[]" data-placeholder="Lead Category" aria-label="Lead category">
                                        <?php foreach ((array)($lc_list ?? array()) as $o) { ?><option value="<?php echo $o['id']; ?>"<?php echo $mc_sel('lCat', $o['id']); ?>><?php echo $mc_h($o['title']); ?></option><?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="multiselect-ui form-control" multiple="multiple" name="status[]" data-placeholder="Status" aria-label="Status">
                                        <?php foreach ((array)($st_list ?? array()) as $o) { ?><option value="<?php echo $o['id']; ?>"<?php echo $mc_sel('status', $o['id']); ?>><?php echo $mc_h($o['status']); ?></option><?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="input-md form-control" name="user_name" placeholder="Owner" aria-label="Owner" value="<?php echo $mc_h($mc_get('user_name')); ?>" autocomplete="off" />
                                </div>
                                <div class="form-group">
                                    <select class="form-control select2" name="country[]" id="select_val" aria-label="Country" onchange="ajax_get_state()">
                                        <option value="">Country</option>
                                        <?php foreach ((array)($c_list ?? array()) as $o) { ?><option value="<?php echo $o['id']; ?>"<?php echo $mc_sel('country', $o['id']); ?>><?php echo $mc_h($o['name']); ?></option><?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control multiselect-ui select3" multiple="multiple" name="state[]" id="select_state" data-placeholder="State" aria-label="State">
                                        <?php foreach ((array)($state_list ?? array()) as $o) { ?><option value="<?php echo $o['id']; ?>"<?php echo $mc_sel('state', $o['id']); ?>><?php echo $mc_h($o['name']); ?></option><?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                    <a href="<?php echo base_url(); ?>manager/kam/my-client" class="btn btn-default">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="box-body">
                        <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
                        <?php echo get_message($this->session->flashdata('message'), 'message'); ?>
                        <?php if ($n) { ?>
                        <table id="example_kam_clients" class="table table-hover table-bordered tl-t" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Client ID</th>
                                    <th>Client</th>
                                    <th>Service Type</th>
                                    <th>MRR</th>
                                    <th>Contact</th>
                                    <th>Phone</th>
                                    <th>Account Manager</th>
                                    <th>Next Follow-up</th>
                                    <th>Added</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tfoot><tr><?php echo str_repeat('<th></th>', 11); ?></tr></tfoot>
                            <tbody>
                                <?php foreach ($product_list as $li) {
                                    $url = base_url() . 'manager/kam/view/' . (int)$li['id'];
                                    $fu = !empty($li['followup_date']) && strpos($li['followup_date'], '0000') !== 0 ? $li['followup_date'] : '';
                                ?>
                                <tr>
                                    <td><a href="<?php echo $url; ?>"><b><?php echo $mc_h(!empty($li['lead_id']) ? $li['lead_id'] : $li['id']); ?></b></a></td>
                                    <td>
                                        <a href="<?php echo $url; ?>"><b><?php echo $mc_h($li['customer']); ?></b></a>
                                        <?php if (!empty($li['dnd_status']) && strtolower($li['dnd_status']) === 'yes') { ?><span class="label label-danger">DNCR</span><?php } ?>
                                        <?php if (!empty($li['group_name'])) { ?><small class="tl-sub"><?php echo $mc_h($li['group_name']); ?></small><?php } ?>
                                    </td>
                                    <td><?php echo $mc_h($li['servicecat'] ?? ''); ?></td>
                                    <td data-order="<?php echo (float)($li['mrr'] ?? 0); ?>"><?php echo number_format((float)($li['mrr'] ?? 0), 2); ?></td>
                                    <td><?php echo $mc_h($li['poc'] ?? ''); ?></td>
                                    <td><?php echo $mc_h($li['contact1'] ?? ''); ?></td>
                                    <td><?php echo $mc_h(!empty($li['executive_name']) ? $li['executive_name'] : ($li['owner_name'] ?? '')); ?></td>
                                    <td data-order="<?php echo $fu ? date('Ymd', strtotime($fu)) : '0'; ?>"><?php echo $fu ? simple_date($fu) : '—'; ?></td>
                                    <td data-order="<?php echo !empty($li['created']) ? date('Ymd', strtotime($li['created'])) : '0'; ?>"><?php echo !empty($li['created']) ? simple_date($li['created']) : '—'; ?></td>
                                    <td><?php echo $mc_h($li['lead_status'] ?? ''); ?></td>
                                    <td><a href="<?php echo $url; ?>" class="btn btn-xs btn-default"><i class="fa fa-eye"></i> View</a></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php } else { ?>
                        <div class="alert alert-info">No clients found.</div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function ajax_get_state() {
    $.ajax({
        type: "POST",
        data: 'country_id=' + $("#select_val").val(),
        url: "<?php echo base_url(); ?>ci_admin_leads/ajax_get_state",
        success: function (result) {
            if (result) {
                $('#select_state').html(result);
                if ($.fn.multiselect) $('#select_state').multiselect('rebuild');
            }
        }
    });
}
$(function () {
    var $t = $('#example_kam_clients');
    if ($t.length && !$.fn.DataTable.isDataTable($t)) {
        $t.DataTable({ pageLength: 50, order: [[1, 'asc']], columnDefs: [{ orderable: false, targets: [10] }], responsive: window.innerWidth >= 768,
            initComplete: function () {
                // footer filters: service type and status
                this.api().columns([2, 9]).every(function () {
                    var col = this;
                    var $s = $('<select><option value=""></option></select>').appendTo($(col.footer()).empty())
                        .on('change', function () { var v = $.fn.dataTable.util.escapeRegex($(this).val()); col.search(v ? '^' + v + '$' : '', true, false).draw(); });
                    col.data().unique().sort().each(function (d) { d = $('<div>').html(d).text().trim(); if (d) $s.append($('<option>').val(d).text(d)); });
                });
            }
        });
    }
});
</script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-tlist.css?v=20261106_4">
<script src="<?php echo base_url(); ?>assets/dist/js/crm-tlist.js?v=20261106_3"></script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
