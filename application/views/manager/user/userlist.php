<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$can_write = !empty($can_write);
$is_hr = ($base === 'manager/hr/staff');
$rows = (isset($product_list) && is_array($product_list)) ? $product_list : array();
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
// Filter choices with counts, built from the list itself
$f_mgr = array(); $f_cat = array(); $f_st = array('Enabled' => 0, 'Disabled' => 0);
foreach ($rows as $li) {
	$m = trim((string)$li['manager_name']) !== '' ? trim($li['manager_name']) : '(No manager)';
	$c = trim((string)$li['role']) !== '' ? trim($li['role']) : '(No category)';
	$f_mgr[$m] = isset($f_mgr[$m]) ? $f_mgr[$m] + 1 : 1;
	$f_cat[$c] = isset($f_cat[$c]) ? $f_cat[$c] + 1 : 1;
	$f_st[((int)$li['admin_status'] === 1) ? 'Enabled' : 'Disabled']++;
}
ksort($f_mgr, SORT_NATURAL | SORT_FLAG_CASE); ksort($f_cat, SORT_NATURAL | SORT_FLAG_CASE);
$filters = array('mgr' => array('Manager', $f_mgr), 'cat' => array('Category', $f_cat), 'st' => array('Status', $f_st));
?>
<style>
/* View Users: compact list, sortable columns, tick-box filters */
.ulist .content-header{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px 15px 6px}
.ulist .content-header h1{font-size:20px;margin:0}
.ulist .content-header .btn{height:32px;font-weight:600}
.ulist .content{padding-top:0}
.ulist .ul-card{background:var(--crm-surface,#fff);border:1px solid var(--crm-line,#e2e8f0);border-radius:10px;padding:0}
.ulist .ul-bar{display:flex;align-items:center;flex-wrap:wrap;gap:8px;padding:9px 12px;border-bottom:1px solid var(--crm-line-soft,#eef2f7)}
.ulist .ul-bar .lbl{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--crm-muted,#64748b);margin-right:2px}
.ulist .ul-f{position:relative}
.ulist .ul-f > button{height:30px;padding:0 10px;border:1px solid var(--crm-line,#cbd5e1);border-radius:6px;background:var(--crm-canvas,#f8fafc);color:var(--crm-ink,#0f172a);font-size:12.5px;font-weight:600;display:flex;align-items:center;gap:6px}
.ulist .ul-f > button .n{display:none;min-width:18px;height:18px;padding:0 5px;border-radius:9px;background:var(--crm-ocean,#1e3a8a);color:#fff;font-size:11px;line-height:18px;text-align:center}
.ulist .ul-f.active > button{border-color:var(--crm-ocean,#1e3a8a);color:var(--crm-ocean,#1e3a8a)}
.ulist .ul-f.active > button .n{display:inline-block}
.ulist .ul-pop{display:none;position:absolute;top:34px;left:0;z-index:30;min-width:230px;max-width:300px;background:var(--crm-surface,#fff);border:1px solid var(--crm-line,#e2e8f0);border-radius:8px;box-shadow:0 10px 24px rgba(15,23,42,.14);padding:6px}
.ulist .ul-f.open .ul-pop{display:block}
.ulist .ul-pop .q{width:100%;height:28px;border:1px solid var(--crm-line,#cbd5e1);border-radius:5px;padding:0 8px;font-size:12.5px;margin-bottom:4px;background:var(--crm-canvas,#f8fafc);color:inherit}
.ulist .ul-pop .opts{max-height:260px;overflow:auto}
.ulist .ul-pop label{display:flex;align-items:center;gap:7px;margin:0;padding:4px 6px;border-radius:5px;font-size:12.5px;font-weight:500;cursor:pointer;color:var(--crm-ink,#0f172a)}
.ulist .ul-pop label:hover{background:var(--crm-ocean-soft,rgba(30,58,138,.08))}
.ulist .ul-pop label .c{margin-left:auto;color:var(--crm-muted,#64748b);font-size:11.5px}
.ulist .ul-pop .ft{display:flex;justify-content:space-between;border-top:1px solid var(--crm-line-soft,#eef2f7);margin-top:4px;padding:5px 4px 1px;font-size:12px}
.ulist .ul-pop .ft a{cursor:pointer}
.ulist .ul-clear{font-size:12px;cursor:pointer;display:none}
.ulist .ul-count{margin-left:auto;font-size:12px;color:var(--crm-muted,#64748b)}
.ulist .ul-body{padding:6px 12px 10px}
.ulist table.dataTable{margin-top:4px !important;border-collapse:collapse !important}
.ulist #example_user thead th{background:var(--crm-canvas,#f8fafc);color:var(--crm-ink-2,#334155);font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:7px 22px 7px 8px !important;border-bottom:1px solid var(--crm-line,#e2e8f0) !important;white-space:nowrap}
.ulist #example_user tbody td{padding:5px 8px !important;font-size:13px;vertical-align:middle;border-color:var(--crm-line-soft,#eef2f7) !important}
.ulist #example_user tbody tr:hover td{background:var(--crm-ocean-soft,rgba(30,58,138,.05))}
.ulist .emp{font-family:ui-monospace,Consolas,monospace;font-size:12px;color:var(--crm-ocean,#1e3a8a);font-weight:600;white-space:nowrap}
.ulist .nm{font-weight:600;color:var(--crm-ink,#0f172a);text-transform:capitalize}
.ulist .nm:hover{color:var(--crm-ocean,#1e3a8a)}
.ulist .lg{font-size:12px;color:var(--crm-muted,#64748b)}
.ulist .chip{display:inline-block;padding:1px 8px;border-radius:999px;font-size:11.5px;font-weight:600;white-space:nowrap;background:var(--crm-ocean-soft,rgba(30,58,138,.1));color:var(--crm-ocean,#1e3a8a)}
.ulist .chip.on{background:rgba(22,163,74,.12);color:#15803d}
.ulist .chip.off{background:rgba(220,38,38,.12);color:#b91c1c}
.ulist .acts{display:flex;gap:4px;white-space:nowrap}
.ulist .acts a{width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;border:1px solid var(--crm-line,#e2e8f0);color:var(--crm-ink-2,#334155);background:var(--crm-surface,#fff)}
.ulist .acts a:hover{border-color:var(--crm-ocean,#1e3a8a);color:var(--crm-ocean,#1e3a8a)}
.ulist .acts a.danger:hover{border-color:#dc2626;color:#dc2626}
.ulist .no_show{display:none}
.ulist .dataTables_wrapper .dataTables_filter input{height:30px;border-radius:6px}
.ulist .dataTables_wrapper .dataTables_length select{height:30px;border-radius:6px}
@media (max-width:767px){.ulist .ul-count{margin-left:0;width:100%}.ulist .ul-pop{min-width:200px}}
</style>
<div class="content-wrapper ulist">
  <section class="content-header">
    <h1><?php echo $is_hr ? 'Staff' : 'Users'; ?></h1>
    <?php if ($can_write) { ?><a href="<?php echo base_url().$base; ?>/add" class="btn btn-primary"><i class="fa fa-user-plus"></i> Add <?php echo $is_hr ? 'Staff' : 'User'; ?></a><?php } ?>
  </section>
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'),'message'); ?>
    <div class="ul-card">
      <?php if (!empty($rows)) { ?>
      <div class="ul-bar">
        <span class="lbl"><i class="fa fa-filter"></i> Filter</span>
        <?php foreach ($filters as $fk => $fd) { ?>
        <div class="ul-f" data-f="<?php echo $fk; ?>">
          <button type="button"><?php echo $fd[0]; ?> <span class="n">0</span> <i class="fa fa-caret-down"></i></button>
          <div class="ul-pop">
            <?php if (count($fd[1]) > 8) { ?><input type="text" class="q" placeholder="Find <?php echo strtolower($fd[0]); ?>..."><?php } ?>
            <div class="opts">
              <?php foreach ($fd[1] as $opt => $cnt) { ?>
              <label><input type="checkbox" value="<?php echo $h($opt); ?>"> <span><?php echo $h($opt); ?></span> <span class="c"><?php echo (int)$cnt; ?></span></label>
              <?php } ?>
            </div>
            <div class="ft"><a class="all">Tick all</a><a class="none">Clear</a></div>
          </div>
        </div>
        <?php } ?>
        <a class="ul-clear">Clear filters</a>
        <span class="ul-count"></span>
      </div>
      <div class="ul-body">
        <table id="example_user" class="table table-hover" style="width:100%">
          <thead>
            <tr>
              <th>Emp. No</th>
              <th>Name</th>
              <th>Login</th>
              <th>Manager</th>
              <th>Category</th>
              <th>Added</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $li) {
              $on = ((int)$li['admin_status'] === 1);
              $mgr = trim((string)$li['manager_name']) !== '' ? trim($li['manager_name']) : '(No manager)';
              $cat = trim((string)$li['role']) !== '' ? trim($li['role']) : '(No category)'; ?>
            <tr data-mgr="<?php echo $h($mgr); ?>" data-cat="<?php echo $h($cat); ?>" data-st="<?php echo $on ? 'Enabled' : 'Disabled'; ?>">
              <td><span class="emp"><?php echo sprintf('EMP-%05d', $li['id']); ?></span></td>
              <td><a class="nm" href="<?php echo base_url().$base; ?>/view/<?php echo (int)$li['id']; ?>"><?php echo $h($li['name']); ?></a></td>
              <td class="lg"><?php echo $h($li['c_username']); ?></td>
              <td><?php echo $h($li['manager_name']); ?></td>
              <td><span class="chip"><?php echo $h($li['role']); ?></span></td>
              <td data-order="<?php echo date('YmdHis', strtotime($li['created'])); ?>" style="white-space:nowrap"><?php echo simple_date($li['created']); ?></td>
              <td><span class="chip <?php echo $on ? 'on' : 'off'; ?>"><?php echo $on ? 'Enabled' : 'Disabled'; ?></span></td>
              <td>
                <div class="acts">
                  <a href="<?php echo base_url().$base; ?>/view/<?php echo (int)$li['id']; ?>" title="View details"><i class="fa fa-eye"></i></a>
                  <?php if ($can_write) { ?>
                  <a href="<?php echo base_url().$base; ?>/edit/<?php echo (int)$li['id']; ?>" title="Edit user"><i class="fa fa-pencil"></i></a>
                  <a href="<?php echo base_url().$base; ?>/cedit/<?php echo (int)$li['id']; ?>" title="Change login or password"><i class="fa fa-key"></i></a>
                  <a href="javascript:void(0);" class="js-status" data-id="<?php echo (int)$li['id']; ?>" data-admin_status="<?php echo $on ? 1 : 0; ?>" title="<?php echo $on ? 'Disable user' : 'Enable user'; ?>"><i class="fa <?php echo $on ? 'fa-ban' : 'fa-check'; ?>"></i></a>
                  <a href="<?php echo base_url().$base; ?>/delete/<?php echo (int)$li['id']; ?>" class="danger" onclick="return do_confirm();" title="Delete user"><i class="fa fa-trash"></i></a>
                  <?php } ?>
                </div>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <?php } else { ?>
      <div class="alert alert-info" style="margin:12px">No users found.</div>
      <?php } ?>
    </div>
  </section>
</div>
<script>
function do_confirm(){
  return confirm("Are you sure to delete this user?");
}
$(document).ready(function(){
  if (!$("#example_user").length) return;
  // Tick-box filters: a row shows when it matches every filter that has something ticked
  var picked = {mgr: [], cat: [], st: []};
  $.fn.dataTable.ext.search.push(function(settings, data, idx){
    if (settings.nTable.id !== 'example_user') return true;
    var tr = settings.aoData[idx].nTr;
    for (var k in picked) {
      if (picked[k].length && picked[k].indexOf(tr.getAttribute('data-' + k)) === -1) return false;
    }
    return true;
  });
  var table = $("#example_user").DataTable({
    pageLength: 50,
    order: [[0, 'desc']],
    autoWidth: false,
    columnDefs: [{ targets: [7], orderable: false, searchable: false }]
  });
  function count(){
    var shown = table.rows({search: 'applied'}).count(), all = table.rows().count();
    $('.ul-count').text(shown === all ? all + ' users' : shown + ' of ' + all + ' users');
  }
  function apply(){
    var any = false;
    $('.ul-f').each(function(){
      var k = $(this).data('f');
      picked[k] = $(this).find('.opts input:checked').map(function(){ return this.value; }).get();
      $(this).toggleClass('active', picked[k].length > 0).find('.n').text(picked[k].length);
      if (picked[k].length) any = true;
    });
    $('.ul-clear').toggle(any);
    table.draw();
    count();
  }
  $('.ul-f > button').on('click', function(e){
    e.stopPropagation();
    var f = $(this).parent();
    $('.ul-f').not(f).removeClass('open');
    f.toggleClass('open');
    f.find('.q').trigger('focus');
  });
  $('.ul-pop').on('click', function(e){ e.stopPropagation(); });
  $(document).on('click', function(){ $('.ul-f').removeClass('open'); });
  $(document).on('keydown', function(e){ if (e.key === 'Escape') $('.ul-f').removeClass('open'); });
  $('.ul-pop .opts input').on('change', apply);
  $('.ul-pop .all').on('click', function(){ $(this).closest('.ul-pop').find('.opts label:visible input').prop('checked', true); apply(); });
  $('.ul-pop .none').on('click', function(){ $(this).closest('.ul-pop').find('.opts input').prop('checked', false); apply(); });
  $('.ul-clear').on('click', function(){ $('.ul-pop .opts input').prop('checked', false); apply(); });
  $('.ul-pop .q').on('input', function(){
    var q = this.value.toLowerCase();
    $(this).siblings('.opts').find('label').each(function(){ $(this).toggle($(this).text().toLowerCase().indexOf(q) !== -1); });
  });
  table.on('search.dt', function(){ setTimeout(count, 0); });
  count();

  $(document).on("click", ".js-status", function(){
    var id = $(this).data("id"), st = $(this).data("admin_status");
    if (!confirm(st == 1 ? "Are you sure to disable this user?" : "Are you sure to enable this user?")) return;
    $.ajax({
      type: "POST",
      url: "<?php echo base_url(); ?>ci_admin_user/ajax_edit_status",
      data: { admin_status: st, id: id, <?php echo $this->security->get_csrf_token_name(); ?>: "<?php echo $this->security->get_csrf_hash(); ?>" },
      success: function(result){
        if ($.trim(result) === 'ok') location.reload();
        else alert("Error: Unable to update status");
      }
    });
  });
});
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
