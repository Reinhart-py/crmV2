<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$clients = isset($clients) && is_array($clients) ? $clients : array();
$q = isset($_GET['search']) && is_string($_GET['search']) ? $_GET['search'] : (isset($_GET['company']) && is_string($_GET['company']) ? $_GET['company'] : '');
$k_none = 0; $k_mine = 0;
foreach ($clients as $c) {
    $pid = (int)($c['executive_id'] ?? 0);
    if ($pid <= 0) $k_none++;
    $cids = array_map('intval', explode(',', (string)($c['collaborators'] ?? '')));
    if ($pid === (int)$cur_uid || in_array((int)$cur_uid, $cids, true)) $k_mine++;
}
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<div class="content-wrapper sx">
  <?php echo crm_tabbar('clients', 'system/kam_clients'); ?>
    <section class="sx-head">
        <h1>KAM Clients<small>Every onboarded client in the company and who looks after it</small></h1>
    </section>

    <section class="content">
        <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
        <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

        <div class="sx-kpis">
            <div class="sx-kpi"><span class="ic"><i class="fa fa-briefcase"></i></span><div><b><?php echo number_format(count($clients)); ?></b><span><?php echo $q === '' ? 'KAM clients' : 'Clients found'; ?></span></div></div>
            <div class="sx-kpi g"><span class="ic"><i class="fa fa-user"></i></span><div><b><?php echo number_format($k_mine); ?></b><span>Assigned to you</span></div></div>
            <div class="sx-kpi a"><span class="ic"><i class="fa fa-user-times"></i></span><div><b><?php echo number_format($k_none); ?></b><span>No owner yet</span></div></div>
        </div>

        <div class="sx-card">
            <form class="sx-bar" method="get" action="<?php echo base_url('manager/system/kam_clients'); ?>">
                <input type="text" class="form-control sx-q" name="search" placeholder="Company, owner or contact person" value="<?php echo $h($q); ?>" autocomplete="off" />
                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                <a href="<?php echo $h($reset_url); ?>" class="btn btn-default">Reset</a>
            </form>

            <?php if (!empty($clients)) { ?>
            <div class="sx-table-wrap">
                <table id="example_kam_clients" class="sx-table sx-cards" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Owner / contact</th>
                            <th>Phone / email</th>
                            <th>Primary assignee</th>
                            <th>Team</th>
                            <th>Trade licence</th>
                            <th>Onboarded</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $c) {
                            $lead_id = (int)($c['lead_id'] ?? 0);
                            $enc = $lead_id > 0 ? base64_encode($lead_id) : '';
                            $primary_id = (int)($c['executive_id'] ?? 0);
                            $collab_ids = array();
                            foreach (explode(',', (string)($c['collaborators'] ?? '')) as $p) { $pid = (int)trim($p); if ($pid > 0) $collab_ids[] = $pid; }
                            $is_assigned = ($is_admin || $primary_id === (int)$cur_uid || in_array((int)$cur_uid, $collab_ids, true));
                            $client_name = !empty($c['client_name']) ? $c['client_name'] : 'Unknown company';
                            $owner_name = !empty($c['owner_name']) ? $c['owner_name'] : (!empty($c['lead_poc']) ? $c['lead_poc'] : '-');
                        ?>
                        <tr>
                            <td data-l="Company" class="full">
                                <?php if ($enc !== '') { ?><a class="nm" href="<?php echo base_url('manager/leads/view/' . $enc); ?>"><?php echo $h($client_name); ?></a><?php } else { ?><span class="nm"><?php echo $h($client_name); ?></span><?php } ?>
                                <?php if ($lead_id > 0) { ?><div class="sub">Lead #<?php echo $lead_id; ?></div><?php } ?>
                            </td>
                            <td data-l="Owner"><?php echo $h($owner_name); ?></td>
                            <td data-l="Contact">
                                <?php if (!empty($c['lead_contact'])) { ?><a href="tel:<?php echo $h(preg_replace('/[^0-9+]/', '', $c['lead_contact'])); ?>"><?php echo $h($c['lead_contact']); ?></a><?php } else { ?><span class="sub">-</span><?php } ?>
                                <?php if (!empty($c['lead_email'])) { ?><div class="sub"><?php echo $h($c['lead_email']); ?></div><?php } ?>
                            </td>
                            <td data-l="Assignee">
                                <?php if (!empty($c['assignee_name'])) { ?>
                                    <span class="who"><span class="av"><?php echo $h(strtoupper(substr($c['assignee_name'], 0, 1))); ?></span><span><?php echo $h($c['assignee_name']); ?></span></span>
                                <?php } else { ?>
                                    <span class="chip w">No owner yet</span>
                                <?php } ?>
                            </td>
                            <td data-l="Team"><?php echo !empty($collab_ids) ? '<span class="chip na">' . count($collab_ids) . ' collaborator' . (count($collab_ids) === 1 ? '' : 's') . '</span>' : '<span class="sub">None</span>'; ?></td>
                            <td data-l="Licence">
                                <?php echo !empty($c['tl_number']) ? '<span class="mono">' . $h($c['tl_number']) . '</span>' : '<span class="sub">-</span>'; ?>
                                <?php if (!empty($c['tl_emirate'])) { ?><div class="sub"><?php echo $h($c['tl_emirate']); ?></div><?php } ?>
                            </td>
                            <td data-l="Onboarded" class="nw" data-order="<?php echo !empty($c['created_at']) ? date('YmdHis', strtotime($c['created_at'])) : 0; ?>"><?php echo !empty($c['created_at']) ? date('d M Y', strtotime($c['created_at'])) : '-'; ?></td>
                            <td data-l="" class="full">
                                <div class="acts">
                                <?php if ($enc !== '') { ?>
                                    <a href="<?php echo base_url('manager/leads/view/' . $enc); ?>" title="View details"><i class="fa fa-eye"></i></a>
                                    <?php if ($is_assigned) { ?><a href="<?php echo base_url('manager/leads/edit/' . $enc); ?>" title="Edit details"><i class="fa fa-pencil"></i></a><?php } ?>
                                <?php } elseif ($is_assigned) { ?>
                                    <a href="<?php echo base_url('manager/kam/onboard?company_id=' . (int)$c['id']); ?>" title="Manage company"><i class="fa fa-pencil"></i></a>
                                <?php } else { ?>
                                    <span class="sub"><i class="fa fa-lock"></i> View only</span>
                                <?php } ?>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php } else { ?>
                <div class="sx-card-b"><div class="sx-empty"><i class="fa fa-briefcase"></i> <?php echo $q === '' ? 'No KAM clients yet.' : 'No clients match this search.'; ?></div></div>
            <?php } ?>
        </div>
    </section>
</div>

<script>
$(function () {
    if (!$('#example_kam_clients').length) return;
    if ($.fn.DataTable.isDataTable('#example_kam_clients')) $('#example_kam_clients').DataTable().destroy();
    $('#example_kam_clients').DataTable({
        pageLength: 25,
        autoWidth: false,
        order: [[6, 'desc']],
        language: { search: '', searchPlaceholder: 'Search this list' },
        columnDefs: [{ orderable: false, targets: [7] }]
    });
});
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
