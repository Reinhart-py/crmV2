<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$b = $batch;
$open = $b['state'] === 'open' && !$expired;
$csrf = '<input type="hidden" name="' . $this->security->get_csrf_token_name() . '" value="' . $this->security->get_csrf_hash() . '">';
$cat_chip = array('new' => array('ok', 'New'), 'possible' => array('w', 'Possible duplicate'), 'dup' => array('x', 'Duplicate'));
$dec_label = array('add' => 'Added', 'force' => 'Added (duplicate)', 'merge' => 'Filled in existing', 'skip' => 'Skipped', '' => '-');
$opts = array(
	'new' => array('add' => 'Add', 'skip' => 'Skip'),
	'possible' => array('skip' => 'Skip', 'add' => 'Add as new'),
	'dup' => array('skip' => 'Skip'),
);
if ($can_force) {
	$opts['possible']['merge'] = 'Fill in existing record';
	$opts['dup']['merge'] = 'Fill in existing record';
	$opts['dup']['force'] = 'Add anyway (give reason)';
}
$default = array('new' => 'add', 'possible' => 'skip', 'dup' => 'skip');
$engine = Lead_finder_model::engine_name($b['engine']);
$all_fields = count($labels);
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<style>
  .lf-foot { position: sticky; bottom: 0; z-index: 5; background: var(--crm-surface, #fff); display: flex; align-items: center; gap: 10px; flex-wrap: wrap; justify-content: space-between; }
  .lf-sum { font-size: 14px; } .lf-sum b { font-size: 16px; }
  .lf-bulk { display: flex; gap: 10px 16px; flex-wrap: wrap; align-items: flex-end; }
  .lf-bulk .form-group { margin: 0; min-width: 200px; }
  .lf-bulk label { font-size: 12.5px; color: var(--crm-muted, #64748b); font-weight: 600; }
  .lf-f { display: inline-block; margin: 1px 2px 1px 0; padding: 0 7px; border-radius: 999px; font-size: 11.5px; background: rgba(100, 116, 139, .12); color: #475569; }
  .lf-f.hit { background: rgba(220, 38, 38, .12); color: #b91c1c; font-weight: 600; }
  .lf-m { font-size: 12.5px; line-height: 1.5; }
  .lf-m a { font-weight: 600; }
  .lf-act select { min-width: 150px; height: 32px; padding: 4px 8px; }
  .lf-act input { margin-top: 6px; height: 30px; font-size: 12.5px; }
  .lf-sub { color: var(--crm-muted, #64748b); font-size: 12px; }
  tr.c-dup td:first-child { box-shadow: inset 3px 0 0 #dc2626; }
  tr.c-possible td:first-child { box-shadow: inset 3px 0 0 #f59e0b; }
  tr.c-new td:first-child { box-shadow: inset 3px 0 0 #16a34a; }
  @media (max-width: 767px) { .lf-foot .btn { flex: 1; } .lf-sum { width: 100%; } .lf-bulk .form-group { min-width: 100%; } }
</style>
<div class="content-wrapper sx">
  <?php echo crm_tabbar('database', 'leads/lead-finder'); ?>
  <section class="sx-head">
    <h1>Check before adding<small>LF-<?php echo (int)$b['id']; ?> · <?php echo $h($engine); ?> · "<?php echo $h($b['search_query']); ?>" · sent <?php echo $h(date('d M Y H:i', strtotime($b['created_at']))); ?> by <?php echo $h($b['sent_email']); ?></small></h1>
  </section>

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="sx-kpis">
      <div class="sx-kpi s"><span class="ic"><i class="fa fa-list"></i></span><div><b><?php echo number_format($b['total']); ?></b><span>Leads checked against the database</span></div></div>
      <div class="sx-kpi g"><span class="ic"><i class="fa fa-check"></i></span><div><b><?php echo number_format($b['n_new']); ?></b><span>New: no field matches any record</span></div></div>
      <div class="sx-kpi a"><span class="ic"><i class="fa fa-question-circle"></i></span><div><b><?php echo number_format($b['n_possible']); ?></b><span>Possible duplicates: some details match</span></div></div>
      <div class="sx-kpi r"><span class="ic"><i class="fa fa-clone"></i></span><div><b><?php echo number_format($b['n_dup']); ?></b><span>Duplicates: already in the database</span></div></div>
    </div>

    <?php if ($b['state'] !== 'open') { ?>
      <div class="sx-card"><div class="sx-card-b">
        <?php if ($b['state'] === 'done') { ?>
          <b>Done <?php echo $h(date('d M Y H:i', strtotime($b['finished_at']))); ?>:</b>
          <?php echo number_format($b['n_added']); ?> added, <?php echo number_format($b['n_forced']); ?> duplicates added on purpose, <?php echo number_format($b['n_merged']); ?> existing records filled in, <?php echo number_format($b['n_skipped']); ?> skipped.
          <?php if ($b['import_batch_id']) { ?> To undo the added records, use <a href="<?php echo base_url(); ?>manager/leads/import">Database &gt; Import</a> (batch #<?php echo (int)$b['import_batch_id']; ?>).<?php } ?>
        <?php } else { ?>
          This batch was <?php echo $h($b['state']); ?>. Nothing was added.
        <?php } ?>
      </div></div>
    <?php } elseif ($expired) { ?>
      <div class="alert alert-warning">This batch is more than <?php echo Lead_finder_model::EXPIRE_DAYS; ?> days old. Send the leads again from Lead Finder so they are checked against today's database.</div>
    <?php } ?>

    <form action="<?php echo base_url(); ?>manager/leads/lead-finder/confirm/<?php echo (int)$b['id']; ?>" method="post" id="lfForm" class="sx-card">
      <?php echo $csrf; ?>
      <?php if ($open) { ?>
      <div class="sx-card-b">
        <div class="lf-bulk">
          <div class="form-group">
            <label for="lfAssign">Give the added leads to</label>
            <select name="assign_to" id="lfAssign" class="form-control">
              <option value="0">Nobody yet (Non-Allotted Database)</option>
              <?php foreach ($assignees as $a) { ?><option value="<?php echo (int)$a['id']; ?>"<?php echo (int)$a['id'] === (int)$this->session->userdata('manager_id') ? ' data-me="1"' : ''; ?>><?php echo $h($a['name']); ?><?php echo (int)$a['id'] === (int)$this->session->userdata('manager_id') ? ' (me)' : ''; ?></option><?php } ?>
            </select>
          </div>
          <?php foreach (array('new' => 'All new', 'possible' => 'All possible duplicates', 'dup' => 'All duplicates') as $ck => $cl) { if (!(int)$b[$ck === 'new' ? 'n_new' : ($ck === 'possible' ? 'n_possible' : 'n_dup')]) continue; ?>
          <div class="form-group">
            <label><?php echo $cl; ?></label>
            <select class="form-control lf-all" data-c="<?php echo $ck; ?>">
              <option value="">Choose row by row</option>
              <?php foreach ($opts[$ck] as $v => $t) { ?><option value="<?php echo $v; ?>"><?php echo $h($t); ?></option><?php } ?>
            </select>
          </div>
          <?php } ?>
        </div>
        <?php if (!$can_force && (int)$b['n_dup'] > 0) { ?><p class="lf-sub" style="margin:10px 0 0;"><i class="fa fa-lock"></i> Adding duplicates or filling in existing records needs Full access to Lead Finder. Ask an administrator if a duplicate should be added.</p><?php } ?>
      </div>
      <?php } ?>

      <div class="sx-bar">
        <div class="sx-chips" id="lfChips">
          <button type="button" class="is-on" data-f="all">All <span class="n"><?php echo number_format($b['total']); ?></span></button>
          <button type="button" data-f="new">New <span class="n"><?php echo number_format($b['n_new']); ?></span></button>
          <button type="button" data-f="possible">Possible <span class="n"><?php echo number_format($b['n_possible']); ?></span></button>
          <button type="button" data-f="dup">Duplicates <span class="n"><?php echo number_format($b['n_dup']); ?></span></button>
        </div>
      </div>
      <div class="sx-table-wrap">
        <table class="sx-table sx-cards" id="lfTable">
          <thead><tr><th>#</th><th>Result</th><th>Company</th><th>Phone / Email</th><th>Address</th><th>Matching record in the CRM</th><th><?php echo $open ? 'What to do' : 'Done'; ?></th></tr></thead>
          <tbody>
          <?php foreach ($rows as $r) { $d = $r['data']; $m = $r['match']; $cc = $cat_chip[$r['category']]; $hits = isset($m['fields']) ? $m['fields'] : array(); ?>
            <tr class="c-<?php echo $r['category']; ?>" data-c="<?php echo $r['category']; ?>">
              <td data-l="#" class="mono"><?php echo (int)$r['line']; ?></td>
              <td data-l="Result"><span class="chip <?php echo $cc[0]; ?>"><?php echo $cc[1]; ?></span><?php if ($hits) { ?><div class="lf-sub"><?php echo count($hits); ?> of <?php echo $all_fields; ?> fields match</div><?php } ?></td>
              <td data-l="Company"><b><?php echo $h($d['customer']); ?></b><?php if (!empty($d['business_category'])) { ?><div class="lf-sub"><?php echo $h($d['business_category']); ?></div><?php } ?><?php if (!empty($d['lead_source'])) { ?><div class="lf-sub"><a href="<?php echo $h(preg_match('~^https?://~i', $d['lead_source']) ? $d['lead_source'] : 'http://' . $d['lead_source']); ?>" target="_blank" rel="noopener noreferrer">Open link</a></div><?php } ?></td>
              <td data-l="Phone / Email" class="nw"><?php echo $h($d['contact1']); ?><?php if (!empty($d['alt_contact'])) echo '<br>' . $h($d['alt_contact']); ?><?php if (!empty($d['email'])) echo '<br><span class="lf-sub">' . $h($d['email']) . '</span>'; ?></td>
              <td data-l="Address"><?php echo $h($d['address']); ?></td>
              <td data-l="Matching record" class="full lf-m">
                <?php if (!empty($m['in_batch'])) { ?>
                  Same company as row <?php echo (int)$m['in_batch']; ?> of this batch.
                <?php } ?>
                <?php if (!empty($m['record'])) { $rec = $m['record']; ?>
                  <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo (int)$rec['id']; ?>" target="_blank"><?php echo $h($rec['customer']); ?></a>
                  <span class="lf-sub">#<?php echo $h($rec['lead_id']); ?><?php echo $rec['created'] ? ' · added ' . $h($rec['created']) : ''; ?></span><br>
                  <?php foreach ($labels as $fk => $fl) { ?><span class="lf-f<?php echo in_array($fk, $hits, true) ? ' hit' : ''; ?>"><?php echo in_array($fk, $hits, true) ? '&#10003; ' : ''; ?><?php echo $h($fl); ?></span><?php } ?>
                <?php } elseif (empty($m['in_batch'])) { ?>
                  <span class="text-muted">No record shares a name, phone, email, website, listing, address or map location.</span>
                <?php } ?>
              </td>
              <td data-l="<?php echo $open ? 'What to do' : 'Done'; ?>" class="lf-act">
                <?php if ($open && $r['decision'] === '') { ?>
                  <select name="choice[<?php echo (int)$r['id']; ?>]" class="form-control lf-choice" data-c="<?php echo $r['category']; ?>">
                    <?php foreach ($opts[$r['category']] as $v => $t) { if ($v === 'merge' && empty($m['record'])) continue; ?><option value="<?php echo $v; ?>"<?php echo $v === $default[$r['category']] ? ' selected' : ''; ?>><?php echo $h($t); ?></option><?php } ?>
                  </select>
                  <?php if ($r['category'] === 'dup' && $can_force) { ?><input type="text" name="reason[<?php echo (int)$r['id']; ?>]" class="form-control lf-reason" maxlength="200" placeholder="Why add it again?" style="display:none;"><?php } ?>
                <?php } else { ?>
                  <?php echo $h($dec_label[$r['decision']]); ?><?php if ($r['result_lead_id']) { ?> · <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo (int)$r['result_lead_id']; ?>" target="_blank">open</a><?php } ?>
                  <?php if ($r['note'] !== '') { ?><div class="lf-sub"><?php echo $h($r['note']); ?></div><?php } ?>
                <?php } ?>
              </td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
        <div class="sx-empty" id="lfNone" style="display:none;">No leads here.</div>
      </div>

      <?php if ($open) { ?>
      <div class="sx-card-f lf-foot">
        <div class="lf-sum" id="lfSum"></div>
        <div>
          <button type="button" class="btn btn-default" id="lfDiscard"><i class="fa fa-trash-o"></i> Discard batch</button>
          <button type="submit" class="btn btn-primary" id="lfGo"><i class="fa fa-check"></i> Confirm</button>
        </div>
      </div>
      <?php } ?>
    </form>
    <?php if ($open) { ?>
    <form action="<?php echo base_url(); ?>manager/leads/lead-finder/cancel/<?php echo (int)$b['id']; ?>" method="post" id="lfCancel"><?php echo $csrf; ?></form>
    <?php } ?>
    <p class="sx-help"><i class="fa fa-info-circle"></i> Each lead was compared with every record on <?php echo $all_fields; ?> fields: <?php echo $h(implode(', ', $labels)); ?>.
      <b>Duplicate</b> = <?php echo (int)$settings['threshold']; ?> or more fields match, or the same map listing, or the same name with the same phone, email or website.
      <b>Possible</b> = some identifying details match (city and country alone do not count). <b>New</b> = nothing identifying matches.
      Added leads are kept as one import batch: it can be undone from Database &gt; Import while nobody has worked on them.</p>
  </section>
</div>

<script>
$(function () {
  function sync(sel) {
    var $s = $(sel), $r = $s.siblings('.lf-reason');
    if ($r.length) { $r.toggle($s.val() === 'force'); $r.prop('required', $s.val() === 'force'); }
  }
  function sum() {
    var n = { add: 0, force: 0, merge: 0, skip: 0 };
    $('.lf-choice').each(function () { n[this.value] = (n[this.value] || 0) + 1; });
    var parts = ['<b>' + (n.add + n.force).toLocaleString() + '</b> will be added'];
    if (n.force) parts.push(n.force + ' of them duplicates');
    if (n.merge) parts.push(n.merge + ' existing record' + (n.merge === 1 ? '' : 's') + ' filled in');
    parts.push(n.skip + ' skipped');
    var who = $('#lfAssign option:selected').text();
    $('#lfSum').html(parts.join(', ') + '<br><span class="lf-sub">Given to: ' + $('<i>').text(who).html() + '</span>');
    var any = n.add + n.force + n.merge;
    $('#lfGo').prop('disabled', any === 0).html('<i class="fa fa-check"></i> ' + (any ? 'Confirm' : 'Nothing to add'));
  }
  $('#lfTable').on('change', '.lf-choice', function () { sync(this); sum(); });
  $('.lf-all').on('change', function () {
    var c = $(this).data('c'), v = this.value;
    if (!v) return;
    $('.lf-choice[data-c="' + c + '"]').each(function () { if ($(this).find('option[value="' + v + '"]').length) { this.value = v; sync(this); } });
    sum();
  });
  $('#lfAssign').on('change', sum);
  sum();
  $('#lfChips').on('click', 'button', function () {
    var f = $(this).data('f'), shown = 0;
    $('#lfChips button').removeClass('is-on'); $(this).addClass('is-on');
    $('#lfTable tbody tr').each(function () { var on = f === 'all' || this.getAttribute('data-c') === f; this.style.display = on ? '' : 'none'; if (on) shown++; });
    $('#lfNone').toggle(shown === 0);
  });
  $('#lfForm').on('submit', function (e) {
    var missing = $('.lf-reason:visible').filter(function () { return $.trim(this.value).length < 5; });
    if (missing.length) { e.preventDefault(); alert('Write why each duplicate should be added anyway (at least 5 letters).'); missing.first().focus(); return; }
    $('#lfGo').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
  });
  $('#lfDiscard').on('click', function () {
    if (confirm('Discard this batch? Nothing will be added. You can send the leads again from Lead Finder.')) $('#lfCancel').submit();
  });
});
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
