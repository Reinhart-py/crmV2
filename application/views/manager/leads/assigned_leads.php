<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$g = function ($k) { return isset($_GET[$k]) && !is_array($_GET[$k]) ? (string)$_GET[$k] : ''; };
$ga = function ($k) { return isset($_GET[$k]) && is_array($_GET[$k]) ? $_GET[$k] : array(); };
$alloc_filter = isset($alloc_filter) ? $alloc_filter : '';
$counts = isset($alloc_counts) ? $alloc_counts : array('' => 0, 'allotted' => 0, 'non_allotted' => 0);
$can_allocate = !empty($can_allocate);
$base = base_url() . 'manager/assigned-leads';
$n_filters = 0;
foreach (array('d1', 'd2', 'mrr1', 'mrr2', 'user_name') as $k) if ($g($k) !== '') $n_filters++;
foreach (array('category', 'lCat', 'status', 'country', 'state') as $k) if (array_filter($ga($k))) $n_filters++;
$sub = $alloc_filter === 'non_allotted' ? 'Records nobody is working on yet' : ($alloc_filter === 'allotted' ? 'Records given to a salesperson' : 'Every database record and who it is given to');
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<style>
  .al-filters { padding: 12px 14px; border-bottom: 1px solid var(--crm-line-soft, #eef2f7); }
  .al-filters .sx-grid { grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); }
  .al-filters .input-group { width: 100%; }
  .al-filters .btn-group, .al-filters .multiselect { width: 100% !important; }
  .al-filters .multiselect { text-align: left; border-radius: 8px; min-height: 36px; }
  .al-filters .al-btns { display: flex; gap: 8px; align-items: flex-end; }
  .al-sel { display: none; align-items: center; gap: 8px; flex-wrap: wrap; padding: 9px 12px; margin: 10px 14px 0; border-radius: 10px; background: var(--crm-ocean-soft, rgba(30, 58, 138, .08)); border: 1px solid rgba(30, 58, 138, .25); }
  .al-sel.is-on { display: flex; }
  .al-sel b { font-size: 14px; }
  #example_lead tbody td input[type=checkbox], #checkAllLeads { width: 16px; height: 16px; accent-color: var(--crm-ocean, #1e3a8a); cursor: pointer; }
  .al-dnc { display: inline-block; margin-left: 4px; padding: 0 6px; border-radius: 4px; background: #dc2626; color: #fff; font-size: 10.5px; font-weight: 700; letter-spacing: .04em; vertical-align: middle; }
  .no_show { display: none; }
  @media (max-width: 767px) { .al-filters .al-btns .btn { flex: 1; } .al-sel { margin: 10px 10px 0; } .al-sel .btn { flex: 1; } }
</style>
<div class="content-wrapper sx">
  <?php echo crm_tabbar('database', $alloc_filter === 'non_allotted' ? 'assigned-leads?alloc_filter=non_allotted' : 'assigned-leads'); ?>
  <section class="sx-head">
    <h1><?php echo $h(isset($info['page_heading']) ? $info['page_heading'] : 'Allotted Database'); ?><small><?php echo $h($sub); ?></small></h1>
  </section>

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'),'message'); ?>

    <div class="sx-kpis">
      <a class="sx-kpi <?php echo $alloc_filter === '' ? 'is-on' : ''; ?>" href="<?php echo $base; ?>"><span class="ic"><i class="fa fa-database"></i></span><div><b><?php echo number_format($counts['']); ?></b><span>All records</span></div></a>
      <a class="sx-kpi g <?php echo $alloc_filter === 'allotted' ? 'is-on' : ''; ?>" href="<?php echo $base; ?>?alloc_filter=allotted"><span class="ic"><i class="fa fa-user"></i></span><div><b><?php echo number_format($counts['allotted']); ?></b><span>Allotted</span></div></a>
      <a class="sx-kpi a <?php echo $alloc_filter === 'non_allotted' ? 'is-on' : ''; ?>" href="<?php echo $base; ?>?alloc_filter=non_allotted"><span class="ic"><i class="fa fa-folder-open-o"></i></span><div><b><?php echo number_format($counts['non_allotted']); ?></b><span>Non-allotted</span></div></a>
    </div>

    <div class="sx-card">
      <div class="sx-card-h">
        <h3><i class="fa fa-filter"></i> Filters <?php if ($n_filters) { ?><span class="chip"><?php echo $n_filters; ?> on</span><?php } ?></h3>
        <div class="sx-tools"><button type="button" class="btn btn-default btn-sm" id="alToggle" aria-expanded="<?php echo $n_filters ? 'true' : 'false'; ?>" aria-controls="alFilters"><?php echo $n_filters ? 'Hide filters' : 'Show filters'; ?></button></div>
      </div>
      <form class="al-filters" id="alFilters" method="get" action="<?php echo $base; ?>" <?php echo $n_filters ? '' : 'hidden'; ?>>
        <?php if ($alloc_filter !== '') { ?><input type="hidden" name="alloc_filter" value="<?php echo $h($alloc_filter); ?>" /><?php } ?>
        <div class="sx-grid">
          <div class="form-group">
            <label>Added between</label>
            <div class="input-daterange input-group" id="datepicker">
              <input type="text" class="form-control datepicker" name="d1" placeholder="From" value="<?php echo $h($g('d1')); ?>" autocomplete="off" />
              <span class="input-group-addon">to</span>
              <input type="text" class="form-control datepicker" name="d2" placeholder="To" value="<?php echo $h($g('d2')); ?>" autocomplete="off" />
            </div>
          </div>
          <div class="form-group">
            <label>MRR between</label>
            <div class="input-group">
              <input type="text" class="form-control only_number" name="mrr1" placeholder="Min" value="<?php echo $h($g('mrr1')); ?>" autocomplete="off" />
              <span class="input-group-addon">to</span>
              <input type="text" class="form-control only_number" name="mrr2" placeholder="Max" value="<?php echo $h($g('mrr2')); ?>" autocomplete="off" />
            </div>
          </div>
          <div class="form-group">
            <label>Service type</label>
            <select class="multiselect-ui form-control" multiple="multiple" name="category[]" data-placeholder="Service type">
              <?php foreach ((array)$pc_list as $pli) { ?><option <?php echo in_array($pli['id'], $ga('category')) ? 'selected' : ''; ?> value="<?php echo (int)$pli['id']; ?>"><?php echo $h($pli['title']); ?></option><?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label>Lead temperature</label>
            <select id="dates-field2" class="multiselect-ui form-control" multiple="multiple" name="lCat[]" data-placeholder="Lead temperature">
              <?php foreach ((array)$lc_list as $cli) { ?><option <?php echo in_array($cli['id'], $ga('lCat')) ? 'selected' : ''; ?> value="<?php echo (int)$cli['id']; ?>"><?php echo $h($cli['title']); ?></option><?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select id="dates-field3" class="multiselect-ui form-control" multiple="multiple" name="status[]" data-placeholder="Status">
              <?php foreach ((array)$st_list as $cli) { ?><option <?php echo in_array($cli['id'], $ga('status')) ? 'selected' : ''; ?> value="<?php echo (int)$cli['id']; ?>"><?php echo $h($cli['status']); ?></option><?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label>Person (added by or assigned to)</label>
            <input type="text" class="form-control" name="user_name" placeholder="Name" value="<?php echo $h($g('user_name')); ?>" autocomplete="off" />
          </div>
          <div class="form-group">
            <label>Country</label>
            <select class="form-control select2" name="country[]" data-placeholder="Country" id="select_val" onchange="ajax_get_state()" style="width:100%;">
              <option value="">Any country</option>
              <?php foreach ((array)$c_list as $cl_item) { ?><option <?php echo in_array($cl_item['id'], $ga('country')) ? 'selected' : ''; ?> value="<?php echo (int)$cl_item['id']; ?>"><?php echo $h($cl_item['name']); ?></option><?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label>State / Emirate</label>
            <select class="form-control multiselect-ui select3" multiple="multiple" name="state[]" data-placeholder="State" id="select_state">
              <?php if (!empty($state_list)) foreach ($state_list as $st_item) { ?><option <?php echo in_array($st_item['id'], $ga('state')) ? 'selected' : ''; ?> value="<?php echo (int)$st_item['id']; ?>"><?php echo $h($st_item['name']); ?></option><?php } ?>
            </select>
          </div>
          <div class="form-group al-btns">
            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Apply</button>
            <a href="<?php echo $base . ($alloc_filter !== '' ? '?alloc_filter=' . urlencode($alloc_filter) : ''); ?>" class="btn btn-default">Reset</a>
          </div>
        </div>
      </form>

      <?php if ($can_allocate) { ?>
      <div class="al-sel" id="alSel">
        <b><span id="selectedCount">0</span> selected</b>
        <button type="button" class="btn btn-primary btn-sm" id="btnMoveLeads" disabled data-toggle="modal" data-target="#modalMoveLeads"><i class="fa fa-share-square-o"></i> Allocate to...</button>
        <button type="button" class="btn btn-default btn-sm" id="alClear">Clear</button>
      </div>
      <?php } ?>

      <div class="sx-card-b">
        <?php if (!empty($product_list)) { ?>
        <table id="example_lead" class="table sx-table" style="width:100%">
          <thead>
            <tr>
              <th style="width:34px;"><?php if ($can_allocate) { ?><input type="checkbox" id="checkAllLeads" title="Tick all on this page" aria-label="Tick all"><?php } ?></th>
              <th>Ref.</th>
              <th>Customer</th>
              <th>Service</th>
              <th>MRR</th>
              <th>Contact</th>
              <th>Next follow-up</th>
              <th>Added</th>
              <th>Assigned to</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($product_list as $li) {
              $enc = base64_encode($li['id']);
              $cat_label = !empty($li['status_category']) ? $li['status_category'] : (!empty($li['lost_reason']) ? $li['lost_reason'] : '');
              $assigned = !empty($li['assigned_to_name']) && (int)$li['lead_assign'] > 0 && (string)$li['database_category'] !== 'Non-Allotted Database';
            ?>
            <tr>
              <td><?php if ($can_allocate) { ?><input type="checkbox" name="lead_ids[]" value="<?php echo (int)$li['id']; ?>" class="lead-check" aria-label="Tick <?php echo $h($li['customer']); ?>"><?php } ?></td>
              <td class="nw"><a class="mono" href="<?php echo base_url(); ?>manager/leads/view/<?php echo $enc; ?>/tm"><b><?php echo $h($li['lead_id']); ?></b></a></td>
              <td>
                <a class="nm" href="<?php echo base_url(); ?>manager/leads/view/<?php echo $enc; ?>/tm"><?php echo $h($li['customer']); ?></a><?php if (!empty($li['dnd_status']) && strtolower($li['dnd_status']) == 'yes') { ?><span class="al-dnc" title="Do not call (DNCR)">DNCR</span><?php } ?>
                <?php if (!empty($li['poc'])) { ?><span class="sub"><?php echo $h($li['poc']); ?></span><?php } ?>
              </td>
              <td><?php echo $h(isset($li['servicecat']) ? $li['servicecat'] : ''); ?></td>
              <td class="nw"><?php echo $h(isset($li['mrr']) && $li['mrr'] !== '' ? $li['mrr'] : '0'); ?></td>
              <td class="nw"><?php echo $h(isset($li['contact1']) ? $li['contact1'] : ''); ?></td>
              <td class="nw"><span class="no_show"><?php echo !empty($li['followup_date']) ? date('Ymd', strtotime($li['followup_date'])) : ''; ?></span><?php echo !empty($li['followup_date']) ? simple_date($li['followup_date']) : '<span class="chip na">-</span>'; ?></td>
              <td class="nw"><span class="no_show"><?php echo !empty($li['created']) ? date('Ymd', strtotime($li['created'])) : ''; ?></span><?php echo !empty($li['created']) ? simple_date($li['created']) : '-'; ?><?php if (!empty($li['owner_name'])) { ?><span class="sub">by <?php echo $h($li['owner_name']); ?></span><?php } ?></td>
              <td><?php echo $assigned ? '<b>' . $h($li['assigned_to_name']) . '</b>' : '<span class="chip w">Non-allotted</span>'; ?></td>
              <td>
                <?php echo $h(isset($li['lead_status']) ? $li['lead_status'] : ''); ?>
                <?php if ($cat_label !== '') { ?><span class="sub"><?php echo $h($cat_label); ?></span><?php } ?>
                <?php if (!empty($li['quote_status'])) { ?><span class="chip" style="margin-top:3px;"><?php echo $h($li['quote_status']); ?></span><?php } ?>
              </td>
              <td><div class="acts">
                <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo $enc; ?>/tm" title="Open"><i class="fa fa-eye"></i></a>
                <a href="<?php echo base_url(); ?>manager/leads/edit/<?php echo $enc; ?>/tm" title="Edit"><i class="fa fa-pencil"></i></a>
              </div></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
        <?php if (isset($links)) { echo $links; } ?>
        <?php } else { ?>
          <div class="sx-empty"><i class="fa fa-inbox"></i> No records match these filters.</div>
        <?php } ?>
      </div>
    </div>
  </section>
</div>

<?php if ($can_allocate) { ?>
<div class="modal fade" id="modalMoveLeads" role="dialog" aria-labelledby="modalMoveLeadsLabel">
  <div class="modal-dialog" role="document">
    <form action="<?php echo base_url(); ?>manager/leads/bulk-move" method="post" id="formBulkMove">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="modalMoveLeadsLabel"><i class="fa fa-share-square-o"></i> Allocate records</h4>
        </div>
        <div class="modal-body">
          <p style="font-size:14px;">Give <b id="modalSelectedCount">0</b> record(s) to:</p>
          <div id="hiddenLeadInputs"></div>
          <div class="form-group">
            <label for="target_user">Person</label>
            <select name="target_user" id="target_user" class="form-control select2" style="width:100%;" required>
              <option value="">Choose a person</option>
              <option value="0">Nobody (back to Non-allotted)</option>
              <?php foreach ((array)$users_list as $u) { ?><option value="<?php echo (int)$u['id']; ?>"><?php echo $h($u['name']); ?> (<?php echo $h($u['email']); ?>)</option><?php } ?>
            </select>
            <p class="sx-help" style="margin-top:8px;">Only people who are still working here are listed.</p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Allocate</button>
        </div>
      </div>
    </form>
  </div>
</div>
<?php } ?>

<script type="text/javascript">
function ajax_get_state() {
  var countryID = $("#select_val").val();
  if (!countryID) return;
  $.ajax({
    type: "POST",
    data: 'country_id=' + countryID,
    url: "<?php echo base_url(); ?>ci_admin_leads/ajax_get_state",
    success: function (result) {
      if (result) { $('#select_state').html(result); $('#select_state').multiselect('rebuild'); }
    }
  });
}

$(document).ready(function () {
  $('#alToggle').on('click', function () {
    var f = document.getElementById('alFilters'), open = f.hidden;
    f.hidden = !open; this.textContent = open ? 'Hide filters' : 'Show filters'; this.setAttribute('aria-expanded', open ? 'true' : 'false');
  });

  if ($('#example_lead').length) {
    $('#example_lead').DataTable({
      pageLength: 50,
      responsive: true,
      order: [],
      columnDefs: [
        { orderable: false, targets: [0, 10] },
        { responsivePriority: 1, targets: 2 },
        { responsivePriority: 2, targets: 0 },
        { responsivePriority: 3, targets: 8 },
        { responsivePriority: 4, targets: 10 },
        { responsivePriority: 5, targets: 1 }
      ],
      language: { search: '', searchPlaceholder: 'Search this list' }
    });
  }

  function updateSelection() {
    var count = $('.lead-check:checked').length;
    $('#selectedCount').text(count);
    $('#modalSelectedCount').text(count);
    $('#btnMoveLeads').prop('disabled', count === 0);
    $('#alSel').toggleClass('is-on', count > 0);
  }
  $('#checkAllLeads').on('change', function () {
    $('#example_lead tbody .lead-check:visible').prop('checked', $(this).is(':checked'));
    updateSelection();
  });
  $(document).on('change', '.lead-check', function () {
    var total = $('.lead-check').length, checked = $('.lead-check:checked').length;
    $('#checkAllLeads').prop('checked', total > 0 && total === checked);
    updateSelection();
  });
  $('#alClear').on('click', function () { $('.lead-check, #checkAllLeads').prop('checked', false); updateSelection(); });

  $('#btnMoveLeads').on('click', function () {
    var container = $('#hiddenLeadInputs').empty();
    $('.lead-check:checked').each(function () {
      container.append('<input type="hidden" name="lead_ids[]" value="' + parseInt($(this).val(), 10) + '">');
    });
  });

  if ($.fn.modal && $.fn.modal.Constructor) { $.fn.modal.Constructor.prototype.enforceFocus = function () {}; }
  $('#modalMoveLeads').on('shown.bs.modal', function () {
    if ($('#target_user').hasClass('select2-hidden-accessible')) $('#target_user').select2('destroy');
    $('#target_user').select2({ dropdownParent: $('#modalMoveLeads'), placeholder: 'Choose a person', allowClear: true, width: '100%' });
    setTimeout(function () { $('#target_user').select2('open'); }, 150);
  });
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
