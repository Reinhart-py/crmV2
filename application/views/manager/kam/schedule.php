<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$sc_h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$sc_tab = (isset($filter_tab) && $filter_tab === 'my') ? 'my' : 'all';
$sc_n = isset($schedules) ? count($schedules) : 0;
$sc_client = trim((string)$this->input->get('client'));
$my_id = (int)$this->session->userdata('manager_id');
$curYear = (int)date('Y');
$tw = isset($task_window) ? $task_window : array();
// month heading: the year shows on the first column and on January
$tw_head = function ($w, $i) { return $w['label'] . (($i === 0 || $w['m'] === 'Jan') ? '<small>' . $w['y'] . '</small>' : ''); };
?>
<div class="content-wrapper px-4 tlv ksc">
<?php echo crm_mz_tabbar(); ?>
  <section class="content-header">
    <h1><?php echo $sc_h($info['page_heading'] ?? 'Client Tasks'); ?><small><?php echo $sc_n; ?> tasks</small></h1>
  </section>

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <form role="form" action="<?php echo base_url(); ?>manager/kam/schedule" method="post" id="kamScheduleForm" class="ksc-form">
      <div class="box ksc-box">
        <div class="ksc-top">
          <div class="ksc-f ksc-client">
            <label for="client_select">Client<span>*</span></label>
            <select class="form-control select2" name="client_name" id="client_select" required style="width:100%;">
              <option value="">Select client</option>
              <?php foreach ((array)($client_list ?? array()) as $cl) { ?>
                <option value="<?php echo $sc_h($cl['client_name']); ?>"<?php echo $sc_client !== '' && $sc_client === $cl['client_name'] ? ' selected' : ''; ?>><?php echo $sc_h($cl['client_name']); ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="ksc-f ksc-who">
            <label for="sched_assign">Assigned to<span>*</span></label>
            <select class="form-control select2" name="assigned_to" id="sched_assign" required style="width:100%;">
              <?php foreach ((array)($u_list ?? array()) as $u) { ?>
                <option value="<?php echo (int)$u['id']; ?>"<?php echo (int)$u['id'] === $my_id ? ' selected' : ''; ?>><?php echo $sc_h($u['name']); ?></option>
              <?php } ?>
            </select>
          </div>
        </div>

        <div class="ksc-bar">
          <span class="ksc-bar-t">Last 12 months</span>
          <span class="ksc-presets" role="group" aria-label="Load a task set">
            <button type="button" class="btn btn-xs btn-default btn-apply-preset" data-preset="tax">Tax &amp; Accounting</button>
            <button type="button" class="btn btn-xs btn-default btn-apply-preset" data-preset="corporate">Corporate</button>
            <button type="button" class="btn btn-xs btn-default btn-apply-preset" data-preset="audit">Audit</button>
          </span>
          <button type="button" class="btn btn-xs btn-primary" id="btn_add_sched_row"><i class="fa fa-plus"></i> Add task</button>
        </div>
        <div class="table-responsive ksc-wrap">
          <table class="table ksc-m" id="table_create_schedule">
            <thead>
              <tr>
                <th class="ksc-name">Task</th>
                <?php foreach ($tw as $i => $w) { ?><th<?php echo $i === count($tw) - 1 ? ' class="is-now"' : ''; ?>><?php echo $tw_head($w, $i); ?></th><?php } ?>
                <th aria-label="Remove"></th>
              </tr>
            </thead>
            <tbody id="sched_tbody"></tbody>
          </table>
        </div>
        <div class="ksc-foot">
          <a href="<?php echo base_url(); ?>manager/kam/my-client" class="btn btn-default">Cancel</a>
          <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Save tasks</button>
        </div>
      </div>
    </form>

    <div class="box ksc-box">
      <div class="ksc-lhead">
        <nav class="ksc-seg" aria-label="Tasks">
          <a href="<?php echo base_url(); ?>manager/kam/schedule?tab=all"<?php echo $sc_tab === 'all' ? ' class="is-on" aria-current="page"' : ''; ?>>All</a>
          <a href="<?php echo base_url(); ?>manager/kam/schedule?tab=my"<?php echo $sc_tab === 'my' ? ' class="is-on" aria-current="page"' : ''; ?>>Assigned to me</a>
        </nav>
      </div>
      <div class="box-body">
        <?php if ($sc_n) { ?>
          <table id="example_kam_sched" class="table table-hover table-bordered tl-t" style="width:100%;">
            <thead>
              <tr>
                <th>ID</th>
                <th>Client</th>
                <th>Task</th>
                <th>Year</th>
                <th>Assigned To</th>
                <th>Added</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($schedules as $sch) { ?>
                <tr>
                  <td>#<?php echo (int)$sch['id']; ?></td>
                  <td><b><?php echo $sc_h($sch['client_name'] ?? ''); ?></b></td>
                  <td><?php echo $sc_h($sch['title'] ?? ''); ?></td>
                  <td><?php echo $sc_h($sch['sched_year'] ?? ''); ?></td>
                  <td><?php echo $sc_h(!empty($sch['assigned_name']) ? $sch['assigned_name'] : 'Self'); ?></td>
                  <td data-order="<?php echo !empty($sch['created_at']) ? date('Ymd', strtotime($sch['created_at'])) : '0'; ?>"><?php echo !empty($sch['created_at']) ? date('d-m-Y', strtotime($sch['created_at'])) : '—'; ?></td>
                  <td><?php echo $sc_h($sch['status'] ?? ''); ?></td>
                  <td class="text-nowrap">
                    <button type="button" class="btn btn-xs btn-default btn-preview-matrix" data-client="<?php echo $sc_h($sch['client_name'] ?? ''); ?>" title="Preview the last 12 months"><i class="fa fa-th"></i> Preview</button>
                    <a href="<?php echo base_url(); ?>manager/kam/schedule/delete/<?php echo (int)$sch['id']; ?>" class="btn btn-xs btn-default text-danger" onclick="return confirm('Delete this schedule row?');" title="Delete" aria-label="Delete"><i class="fa fa-trash"></i></a>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        <?php } else { ?>
          <p class="text-muted" style="margin:6px 0;">No tasks yet.</p>
        <?php } ?>
      </div>
    </div>

    <div class="modal fade" id="modalMatrixPresentation" tabindex="-1" role="dialog" aria-labelledby="modalClientTitle">
      <div class="modal-dialog modal-lg ksc-modal">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
            <h4 class="modal-title" id="modalClientTitle">Tasks</h4>
          </div>
          <div class="modal-body" id="modalMatrixBody"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" id="ksc_edit_from_preview"><i class="fa fa-pencil"></i> Edit</button>
            <button type="button" class="btn btn-default" onclick="window.print();"><i class="fa fa-print"></i> Print</button>
            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<style>
  .ksc .ksc-box { border: 1px solid var(--crm-line, #e2e8f0); border-radius: 10px; box-shadow: none; margin-bottom: 12px; background: var(--crm-surface, #fff); }
  .ksc .ksc-top { display: flex; flex-wrap: wrap; gap: 8px 14px; padding: 10px 12px 8px; border-bottom: 1px solid var(--crm-line-soft, #eef2f7); }
  .ksc .ksc-f { display: flex; align-items: center; gap: 8px; min-width: 0; }
  .ksc .ksc-f > label { margin: 0; font-size: 12.5px; font-weight: 500; color: var(--crm-ink-2, #475569); white-space: nowrap; }
  .ksc .ksc-f > label span { color: #dc2626; font-weight: 700; }
  .ksc .ksc-client { flex: 1 1 320px; }
  .ksc .ksc-who { flex: 0 1 260px; }
  .ksc .ksc-f .form-control, .ksc .ksc-f .select2-container .select2-selection--single { height: 30px !important; min-height: 30px; font-size: 13px; }
  .ksc .ksc-f .select2-container { flex: 1 1 auto; min-width: 0; }
  .ksc .ksc-f { position: relative; }
  .ksc .ksc-f select.select2-hidden-accessible { position: absolute !important; width: 1px !important; height: 1px !important; overflow: hidden; }
  .ksc .ksc-bar { display: flex; align-items: center; flex-wrap: wrap; gap: 6px; padding: 8px 12px 4px; }
  .ksc .ksc-bar-t { margin-right: auto; font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--crm-ocean, #1e3a8a); }
  .ksc .ksc-presets { display: inline-flex; gap: 4px; flex-wrap: wrap; }
  .ksc .ksc-wrap { margin: 0; border: 0; padding: 0 12px; }
  .ksc .ksc-m { margin: 0; font-size: 12px; }
  .ksc .ksc-m > thead > tr > th { padding: 4px 2px !important; font-size: 11px !important; font-weight: 600; text-align: center; color: var(--crm-muted, #64748b); background: none !important; border-bottom: 1px solid var(--crm-line, #e2e8f0) !important; text-transform: none; }
  .ksc .ksc-m > thead > tr > th.ksc-name { text-align: center; }
  .ksc .ksc-m > thead > tr > th small, .ksc-pv th small { display: block; font-size: 10px; font-weight: 500; opacity: .8; line-height: 1.1; }
  .ksc .ksc-m > thead > tr > th.is-now { box-shadow: inset 0 -3px 0 #f59e0b; }
  .ksc-pv th.is-now { color: var(--crm-ocean, #1e3a8a); }
  .ksc .ksc-m > tbody > tr > td { padding: 3px 2px !important; vertical-align: middle; border-top: 1px solid var(--crm-line-soft, #eef2f7) !important; }
  .ksc .ksc-m > tbody > tr > td:first-child { padding-left: 0 !important; min-width: 170px; }
  .ksc .ksc-m .sched-name-input { height: 28px; font-size: 12.5px; padding: 3px 7px; }
  .ksc .ksc-cell { display: flex; align-items: center; gap: 2px; min-width: 86px; }
  .ksc .ksc-cell select { flex: 1 1 auto; min-width: 0; height: 26px; padding: 0 2px; font-size: 11px; border: 1px solid var(--crm-line, #e2e8f0); border-radius: 4px; background: #fff; color: #94a3b8; }
  .ksc .ksc-cell select.s-p { background: #fef3c7; border-color: #fcd34d; color: #92400e; font-weight: 600; }
  .ksc .ksc-cell select.s-a { background: #dcfce7; border-color: #86efac; color: #166534; font-weight: 600; }
  .ksc .ksc-cell select.s-e { background: #fee2e2; border-color: #fca5a5; color: #991b1b; font-weight: 600; }
  .ksc .ksc-cell select.s-o { background: #e0e7ff; border-color: #a5b4fc; color: #3730a3; font-weight: 600; }
  .ksc .ksc-cell input[type=checkbox] { margin: 0; flex: 0 0 auto; }
  .ksc .btn-remove-sched-row { border: 0; background: none; color: #94a3b8; padding: 2px 6px; }
  .ksc .btn-remove-sched-row:hover { color: #dc2626; }
  .ksc .ksc-foot { position: sticky; bottom: 0; z-index: 5; display: flex; justify-content: flex-end; gap: 8px; padding: 8px 12px; border-top: 1px solid var(--crm-line-soft, #eef2f7); background: var(--crm-surface, #fff); border-radius: 0 0 10px 10px; }
  .ksc .ksc-foot .btn { min-width: 110px; height: 32px; font-weight: 600; }
  .ksc .ksc-lhead { display: flex; align-items: center; padding: 8px 12px 0; }
  .ksc .ksc-seg { display: inline-flex; gap: 2px; padding: 2px; border-radius: 8px; background: var(--crm-canvas, #f1f5f9); }
  .ksc .ksc-seg a { padding: 4px 12px; border-radius: 6px; font-size: 12.5px; font-weight: 600; color: var(--crm-ink-2, #475569); }
  .ksc .ksc-seg a.is-on { background: #fff; color: var(--crm-ocean, #1e3a8a); box-shadow: 0 1px 2px rgba(15,23,42,.12); }
  .ksc-modal { width: auto; max-width: 1100px; margin: 30px auto; padding: 0 10px; }
  .ksc-pv { width: 100%; min-width: 760px; table-layout: fixed; border-collapse: separate; border-spacing: 2px; font-size: 12px; }
  .ksc-pv th:first-child { width: 190px; }
  .ksc-pv th { padding: 4px; font-size: 11px; font-weight: 600; text-align: center; color: var(--crm-muted, #64748b); }
  .ksc-pv th:first-child, .ksc-pv td:first-child { text-align: left; font-weight: 600; color: var(--crm-ink, #0f172a); padding-right: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .ksc-pv th:first-child { text-align: center; }
  .ksc-pv td { padding: 5px 2px; text-align: center; border-radius: 4px; font-size: 11px; font-weight: 600; }
  .ksc-pv td.s-n { color: #cbd5e1; }
  .ksc-pv td.s-p { background: #fef3c7; color: #92400e; }
  .ksc-pv td.s-a { background: #dcfce7; color: #166534; }
  .ksc-pv td.s-e { background: #fee2e2; color: #991b1b; }
  .ksc-pv td.s-o { background: #e0e7ff; color: #3730a3; }
  .ksc-pv td i { margin-left: 2px; font-size: 10px; }
  .ksc-legend { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 8px; font-size: 11.5px; color: var(--crm-muted, #64748b); }
  .ksc-legend b { display: inline-block; width: 10px; height: 10px; border-radius: 2px; margin-right: 4px; vertical-align: -1px; }
  @media (max-width: 767px) { .ksc .ksc-f { flex: 1 1 100%; } .ksc .ksc-f > label { min-width: 74px; } }
</style>

<script>
$(function () {
  if ($.fn.select2) { $('.ksc .select2').select2(); }

  // the last 12 months, oldest first; cells are keyed "YYYY-Mon"
  var TW = <?php echo json_encode(array_values($tw)); ?>;
  var schedMonths = TW.map(function (w) { return w.key; });
  function mLabel(k) { for (var i = 0; i < TW.length; i++) if (TW[i].key === k) return TW[i].label + ' ' + TW[i].y; return k; }
  function mHead(i) { var w = TW[i]; return w.label + ((i === 0 || w.m === 'Jan') ? '<small>' + w.y + '</small>' : ''); }
  // value => short label in the cells
  var STATUS = [['Not Applicable', '—', 's-n'], ['Pending', 'Pending', 's-p'], ['Approved', 'Approved', 's-a'], ['Expired', 'Expired', 's-e'], ['Other', 'Other', 's-o']];
  function cls(v) { for (var i = 0; i < STATUS.length; i++) if (STATUS[i][0] === v) return STATUS[i][2]; return 's-n'; }
  function esc(s) { return $('<div>').text(s == null ? '' : s).html(); }
  var presetProfiles = {
    tax: ['VAT Return Filing', 'Corporate Tax Filing', 'Monthly Bookkeeping', 'Financial Statements & MIS', 'Document Col/Sub', 'Audit & Year-End Verification'],
    corporate: ['Trade License Renewal', 'Establishment Card Renewal', 'MoL & Immigration Quota', 'Tenancy Contract & Ejari', 'Corporate Bank KYC Update', 'AML Compliance Review'],
    audit: ['Interim Audit Review', 'Stock Taking & Verification', 'Final Audit Preparation', 'Draft Audit Report Submission', 'Management Representation Letter', 'FTA Regulatory Filing']
  };
  var defaultSchedules = ['VAT Return Filing', 'Corporate Tax Filing', 'Monthly Bookkeeping', 'Financial Statements & MIS', 'Compliance Review & AML', 'Audit & Year-End Verification'];

  function rowHtml(idx, item) {
    var h = '<tr class="sched-row" data-row-index="' + idx + '"><td><input type="text" class="form-control input-sm sched-name-input" name="sched_name[' + idx + ']" value="' + esc(item.name || item.title || '') + '" placeholder="Task name" required aria-label="Task name"></td>';
    schedMonths.forEach(function (mn) {
      var md = (item.months && item.months[mn]) ? item.months[mn] : null;
      var st = md ? (md.status || 'Not Applicable') : 'Not Applicable';
      var ck = md ? parseInt(md.checked, 10) === 1 : false;
      h += '<td><div class="ksc-cell"><select name="sched_status[' + idx + '][' + mn + ']" class="' + cls(st) + '" aria-label="' + mLabel(mn) + ' status">';
      STATUS.forEach(function (s) { h += '<option value="' + s[0] + '"' + (s[0] === st ? ' selected' : '') + '>' + s[1] + '</option>'; });
      h += '</select><input type="checkbox" name="sched_check[' + idx + '][' + mn + ']" value="1"' + (ck ? ' checked' : '') + ' title="' + mLabel(mn) + ' done" aria-label="' + mLabel(mn) + ' done"></div></td>';
    });
    return h + '<td><button type="button" class="btn-remove-sched-row" title="Remove task" aria-label="Remove task"><i class="fa fa-times"></i></button></td></tr>';
  }
  function renderScheduleRows(list) {
    if (!list || !list.length) list = defaultSchedules.map(function (n) { return { name: n, months: {} }; });
    $('#sched_tbody').html(list.map(function (it, i) { return rowHtml(i, it); }).join(''));
  }
  renderScheduleRows([]);

  $(document).on('change', '.ksc-cell select', function () { this.className = cls(this.value); });

  $('.btn-apply-preset').on('click', function () {
    var p = presetProfiles[$(this).data('preset')];
    if (p) renderScheduleRows(p.map(function (n) { return { name: n, months: {} }; }));
  });

  $('#btn_add_sched_row').on('click', function (e) {
    e.preventDefault();
    // next free index (rows can be removed, so not the row count)
    var next = 0;
    $('#sched_tbody tr').each(function () { next = Math.max(next, parseInt($(this).data('row-index'), 10) + 1); });
    $('#sched_tbody').append(rowHtml(next, { name: '', months: {} }));
    $('#sched_tbody tr:last .sched-name-input').focus();
  });

  $(document).on('click', '.btn-remove-sched-row', function (e) {
    e.preventDefault();
    if ($('#sched_tbody tr').length > 1) $(this).closest('tr').remove();
    else alert('At least one task is required.');
  });

  function loadClient() {
    var cname = $('#client_select').val();
    if (!cname) return;
    $.getJSON('<?php echo base_url(); ?>manager/kam/ajax_get_client_schedules', { client_name: cname }, function (resp) {
      renderScheduleRows(resp && resp.status === 'success' ? resp.schedules : []);
    });
  }
  $('#client_select').on('change', loadClient);
  if ($('#client_select').val()) loadClient();

  var pv = null;
  $(document).on('click', '.btn-preview-matrix', function () {
    pv = { client: $(this).data('client') };
    $('#modalClientTitle').text(pv.client + ' · ' + mLabel(schedMonths[0]) + ' – ' + mLabel(schedMonths[schedMonths.length - 1]));
    $('#modalMatrixBody').html('<p class="text-muted" style="margin:0;"><i class="fa fa-spinner fa-spin"></i> Loading…</p>');
    $('#modalMatrixPresentation').modal('show');
    $.getJSON('<?php echo base_url(); ?>manager/kam/ajax_get_client_schedules', { client_name: pv.client })
      .done(function (resp) {
        if (!(resp && resp.status === 'success' && resp.schedules && resp.schedules.length)) {
          $('#modalMatrixBody').html('<p class="text-muted" style="margin:0;">No tasks for this year.</p>');
          return;
        }
        var h = '<div class="table-responsive" style="border:0;margin:0;"><table class="ksc-pv"><thead><tr><th>Task</th>';
        schedMonths.forEach(function (m, i) { h += '<th' + (i === schedMonths.length - 1 ? ' class="is-now"' : '') + '>' + mHead(i) + '</th>'; });
        h += '</tr></thead><tbody>';
        resp.schedules.forEach(function (it) {
          h += '<tr><td>' + esc(it.name) + '</td>';
          schedMonths.forEach(function (m) {
            var md = (it.months && it.months[m]) ? it.months[m] : null;
            var st = md ? (md.status || 'Not Applicable') : 'Not Applicable';
            var lab = st === 'Not Applicable' ? '—' : st;
            h += '<td class="' + cls(st) + '" title="' + mLabel(m) + ': ' + esc(st) + '">' + esc(lab) + (md && parseInt(md.checked, 10) === 1 ? '<i class="fa fa-check" aria-label="done"></i>' : '') + '</td>';
          });
          h += '</tr>';
        });
        h += '</tbody></table></div><div class="ksc-legend"><span><b style="background:#fef3c7"></b>Pending</span><span><b style="background:#dcfce7"></b>Approved</span><span><b style="background:#fee2e2"></b>Expired</span><span><b style="background:#e0e7ff"></b>Other</span><span><i class="fa fa-check"></i> Done</span></div>';
        $('#modalMatrixBody').html(h);
      })
      .fail(function () { $('#modalMatrixBody').html('<p class="text-danger" style="margin:0;">Could not load the schedule.</p>'); });
  });
  // open the previewed schedule in the form above
  $('#ksc_edit_from_preview').on('click', function () {
    if (!pv) return;
    $('#modalMatrixPresentation').modal('hide');
    $('#client_select').val(pv.client).trigger('change');
    $('html, body').animate({ scrollTop: $('#kamScheduleForm').offset().top - 70 }, 200);
  });

  var $t = $('#example_kam_sched');
  if ($t.length && !$.fn.DataTable.isDataTable($t)) {
    $t.DataTable({ pageLength: 25, order: [[0, 'desc']], columnDefs: [{ orderable: false, targets: [7] }], responsive: window.innerWidth >= 768 });
  }
});
</script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-tlist.css?v=20261106_4">
<script src="<?php echo base_url(); ?>assets/dist/js/crm-tlist.js?v=20261106_4"></script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
