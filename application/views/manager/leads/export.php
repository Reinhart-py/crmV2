<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$users = isset($users) ? $users : array();
$opt_list = function ($rows, $label_key = 'v', $value_key = 'v') use ($h) {
  $o = '';
  foreach ((array)$rows as $r) $o .= '<option value="' . $h($r[$value_key]) . '">' . $h($r[$label_key]) . (isset($r['n']) ? ' (' . number_format((int)$r['n']) . ')' : '') . '</option>';
  return $o;
};
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<style>
  .ex-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px 16px; }
  .ex-grid .form-group { margin: 0; min-width: 0; }
  .ex-grid .input-group { width: 100%; }
  .ex-grid .select2-container { width: 100% !important; max-width: 100%; }
  .ex-sec { font-size: 12px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--crm-muted, #64748b); margin: 18px 0 10px; }
  .ex-sec:first-child { margin-top: 0; }
  .ex-cols { display: flex; gap: 10px; flex-wrap: wrap; }
  .ex-cols label { display: flex; gap: 10px; align-items: flex-start; flex: 1 1 240px; padding: 10px 12px; border: 1px solid var(--crm-line, #e2e8f0); border-radius: 10px; cursor: pointer; font-weight: 500 !important; margin: 0; }
  .ex-cols label.is-on { border-color: var(--crm-ocean, #1e3a8a); background: var(--crm-ocean-soft, rgba(30, 58, 138, .06)); }
  .ex-cols input { margin-top: 3px; }
  .ex-cols b { display: block; font-size: 13.5px; }
  .ex-cols small { color: var(--crm-muted, #64748b); }
  .ex-foot { position: sticky; bottom: 0; z-index: 5; background: var(--crm-surface, #fff); display: flex; align-items: center; gap: 10px; flex-wrap: wrap; justify-content: space-between; }
  .ex-count { font-size: 14px; }
  .ex-count b { font-size: 18px; }
  .ex-count.zero b { color: #dc2626; }
  .sx .select2-container--default .select2-selection--multiple { min-height: 36px; border-radius: 8px; border-color: var(--crm-line, #cbd5e1); }
  @media (max-width: 991px) { .ex-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
  @media (max-width: 767px) { .ex-grid { grid-template-columns: 1fr; } .ex-foot .btn { flex: 1; } .ex-count { width: 100%; } }
</style>
<div class="content-wrapper sx">
  <?php echo crm_tabbar('database', 'leads/export'); ?>
  <section class="sx-head">
    <h1>Export Database<small>Pick the records you need and download them as a CSV file for Excel</small></h1>
  </section>

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="sx-kpis">
      <div class="sx-kpi"><span class="ic"><i class="fa fa-database"></i></span><div><b><?php echo number_format((int)$total_count); ?></b><span>Records in total</span></div></div>
      <div class="sx-kpi a"><span class="ic"><i class="fa fa-user-times"></i></span><div><b><?php echo number_format((int)$unassigned_count); ?></b><span>Not assigned to anyone</span></div></div>
    </div>

    <form id="exForm" action="<?php echo base_url(); ?>manager/leads/export" method="get" class="sx-card">
      <input type="hidden" name="download" value="1">
      <div class="sx-card-h">
        <h3><i class="fa fa-filter"></i> Which records</h3>
        <div class="sx-tools"><button type="button" class="btn btn-default btn-sm" id="exReset"><i class="fa fa-undo"></i> Clear filters</button></div>
      </div>
      <div class="sx-card-b">
        <div class="ex-sec">People</div>
        <div class="ex-grid">
          <div class="form-group">
            <label for="exAssign">Assigned to</label>
            <select name="assign[]" id="exAssign" class="form-control ex-multi" multiple data-placeholder="Anyone">
              <option value="none">Not assigned to anyone (<?php echo number_format((int)$unassigned_count); ?>)</option>
              <?php foreach ($users as $u) { $n = isset($user_counts[(int)$u['id']]) ? $user_counts[(int)$u['id']] : 0; ?>
                <option value="<?php echo (int)$u['id']; ?>"><?php echo $h($u['name'] . ((int)$u['status'] !== 1 ? ' - disabled' : '') . ' (' . number_format($n) . ')'); ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label for="exBy">Added by</label>
            <select name="added_by[]" id="exBy" class="form-control ex-multi" multiple data-placeholder="Anyone">
              <?php foreach ($users as $u) { ?><option value="<?php echo (int)$u['id']; ?>"><?php echo $h($u['name'] . ((int)$u['status'] !== 1 ? ' - disabled' : '')); ?></option><?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label for="exQ">Search</label>
            <input type="text" name="q" id="exQ" class="form-control" placeholder="Company, contact, phone, email or ID" autocomplete="off">
          </div>
        </div>

        <div class="ex-sec">Status and category</div>
        <div class="ex-grid">
          <div class="form-group">
            <label for="exStatus">Status</label>
            <select name="status[]" id="exStatus" class="form-control ex-multi" multiple data-placeholder="Any status">
              <?php echo $opt_list(isset($statuses) ? $statuses : array(), 'status', 'id'); ?>
            </select>
          </div>
          <div class="form-group">
            <label for="exTemp">Lead temperature</label>
            <select name="temp[]" id="exTemp" class="form-control ex-multi" multiple data-placeholder="Any">
              <?php echo $opt_list(isset($temps) ? $temps : array(), 'title', 'id'); ?>
            </select>
          </div>
          <div class="form-group">
            <label for="exService">Service type</label>
            <select name="service[]" id="exService" class="form-control ex-multi" multiple data-placeholder="Any service">
              <?php echo $opt_list(isset($services) ? $services : array(), 'title', 'id'); ?>
            </select>
          </div>
          <div class="form-group">
            <label for="exProduct">Product</label>
            <select name="product[]" id="exProduct" class="form-control ex-multi" multiple data-placeholder="Any product">
              <?php foreach ((array)(isset($products) ? $products : array()) as $p) { ?><option value="<?php echo (int)$p['id']; ?>"><?php echo $h(($p['parent'] ? $p['parent'] . ' - ' : '') . $p['title']); ?></option><?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label for="exDbCat">Database category</label>
            <select name="db_cat[]" id="exDbCat" class="form-control ex-multi" multiple data-placeholder="Any">
              <?php echo $opt_list(isset($db_cats) ? $db_cats : array()); ?>
            </select>
          </div>
          <div class="form-group">
            <label for="exSource">Data source</label>
            <select name="source[]" id="exSource" class="form-control ex-multi" multiple data-placeholder="Any source">
              <?php echo $opt_list(isset($sources) ? $sources : array()); ?>
            </select>
          </div>
          <div class="form-group">
            <label for="exBiz">Business category</label>
            <select name="biz[]" id="exBiz" class="form-control ex-multi" multiple data-placeholder="Any">
              <?php echo $opt_list(isset($biz_cats) ? $biz_cats : array()); ?>
            </select>
          </div>
          <div class="form-group">
            <label for="exCountry">Country</label>
            <select name="country[]" id="exCountry" class="form-control ex-multi" multiple data-placeholder="Any country">
              <?php echo $opt_list(isset($countries) ? $countries : array(), 'name', 'id'); ?>
            </select>
          </div>
        </div>

        <div class="ex-sec">Dates and value</div>
        <div class="ex-grid">
          <div class="form-group">
            <label>Added between</label>
            <div class="input-daterange input-group">
              <input type="text" class="form-control datepicker" name="d1" placeholder="From" autocomplete="off">
              <span class="input-group-addon">to</span>
              <input type="text" class="form-control datepicker" name="d2" placeholder="To" autocomplete="off">
            </div>
          </div>
          <div class="form-group">
            <label>MRR between</label>
            <div class="input-group">
              <input type="number" step="any" min="0" class="form-control" name="mrr1" placeholder="Min">
              <span class="input-group-addon">to</span>
              <input type="number" step="any" min="0" class="form-control" name="mrr2" placeholder="Max">
            </div>
          </div>
        </div>

        <div class="ex-sec">Columns in the file</div>
        <div class="ex-cols">
          <label class="is-on"><input type="radio" name="columns" value="full" checked><span><b>All details</b><small>Every field: contacts, status, services, licence, MRR, follow-ups and more (52 columns)</small></span></label>
          <label><input type="radio" name="columns" value="contact"><span><b>Contact list</b><small>Company, contact person, phone, email, status, assigned to, location (14 columns)</small></span></label>
        </div>
      </div>
      <div class="sx-card-f ex-foot">
        <div class="ex-count" id="exCount" aria-live="polite"><b><?php echo number_format((int)$total_count); ?></b> records will be exported</div>
        <button type="submit" class="btn btn-primary" id="exGo"><i class="fa fa-download"></i> Download CSV</button>
      </div>
    </form>
    <p class="sx-help"><i class="fa fa-shield"></i> Every export is recorded in Admin Logs with who downloaded what. The file contains personal contact details: keep it safe and do not share it outside the company.</p>
  </section>
</div>

<script>
$(function () {
  var $f = $('#exForm'), timer = null, seq = 0;
  // after the shared footer script (it sets up every ".select2" element, which would include these boxes)
  if ($.fn.select2) setTimeout(function () {
    $('.ex-multi').each(function () {
      $(this).select2({ width: '100%', placeholder: $(this).data('placeholder') || 'Any', allowClear: true, closeOnSelect: false });
    });
  }, 0);
  function refresh() {
    clearTimeout(timer);
    timer = setTimeout(function () {
      var my = ++seq, data = $f.serializeArray().filter(function (x) { return x.name !== 'download' && x.name !== 'columns'; });
      data.push({ name: 'count', value: 1 });
      $('#exCount').css('opacity', .5);
      $.getJSON($f.attr('action'), $.param(data)).done(function (r) {
        if (my !== seq) return;
        var n = parseInt(r.count, 10) || 0;
        $('#exCount').toggleClass('zero', n === 0).html('<b>' + n.toLocaleString() + '</b> record' + (n === 1 ? '' : 's') + ' will be exported');
        $('#exGo').prop('disabled', n === 0);
      }).always(function () { if (my === seq) $('#exCount').css('opacity', 1); });
    }, 300);
  }
  $f.on('change', 'select, input', refresh).on('input', 'input[type=text], input[type=number]', refresh);
  $f.find('.datepicker').on('changeDate', refresh);
  $f.on('change', 'input[name=columns]', function () { $f.find('.ex-cols label').removeClass('is-on'); $(this).closest('label').addClass('is-on'); });
  $('#exReset').on('click', function () {
    $f[0].reset();
    $f.find('.ex-multi').val(null).trigger('change.select2');
    $f.find('.ex-cols label').removeClass('is-on').first().addClass('is-on');
    refresh();
  });
  $f.on('submit', function () {
    var b = $('#exGo').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Preparing...');
    setTimeout(function () { b.prop('disabled', false).html('<i class="fa fa-download"></i> Download CSV'); }, 2500);
  });
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
