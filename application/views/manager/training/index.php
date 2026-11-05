<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
// Self Training library (view only): material published by HR / Admin from "Manage Self Training".
$today = date('Y-m-d');
$cats = array();
$kind_counts = array();
foreach ($modules as $m) {
  $cats[$m['category']] = true;
  $k = isset($kinds[$m['content_type']]) ? $m['content_type'] : 'guide';
  $kind_counts[$k] = (isset($kind_counts[$k]) ? $kind_counts[$k] : 0) + 1;
}
ksort($cats);
$todo = $summary['assigned'] - $summary['completed'];
$pct = $summary['assigned'] > 0 ? round($summary['completed'] * 100 / $summary['assigned']) : 0;
?>
<style>
  .st-page .st-top { display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:14px; }
  .st-page .st-stats { display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:10px; margin-bottom:14px; }
  .st-page .st-stat { background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px; min-width:0; }
  .st-page .st-stat b { display:block; font-size:22px; color:#0f172a; line-height:1.2; }
  .st-page .st-stat span { font-size:12px; color:#64748b; font-weight:600; }
  .st-page .st-bar { height:6px; background:#e2e8f0; border-radius:4px; overflow:hidden; margin-top:6px; }
  .st-page .st-bar i { display:block; height:100%; background:#16a34a; }
  .st-page .st-tools { display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:10px; }
  .st-page .st-search { position:relative; flex:1 1 260px; max-width:420px; }
  .st-page .st-search i { position:absolute; left:11px; top:50%; transform:translateY(-50%); color:#94a3b8; }
  .st-page .st-search input { width:100%; padding:8px 10px 8px 32px; border:1px solid #cbd5e1; border-radius:8px; }
  .st-page .st-tools select.st-select { width:auto; flex:0 1 200px; min-width:0; padding:8px 10px; border:1px solid #cbd5e1; border-radius:8px; background:#fff; max-width:100%; }
  .st-page .st-chips { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:14px; }
  .st-page .st-chip { border:1px solid #cbd5e1; background:#fff; border-radius:999px; padding:5px 12px; font-size:12px; font-weight:700; color:#475569; cursor:pointer; display:inline-flex; align-items:center; gap:6px; }
  .st-page .st-chip i { color:var(--st-c, #1e3a8a); }
  .st-page .st-chip b { background:#f1f5f9; border-radius:10px; padding:0 6px; font-size:11px; }
  .st-page .st-chip.active { background:#1e3a8a; border-color:#1e3a8a; color:#fff; }
  .st-page .st-chip.active i { color:#fff; }
  .st-page .st-chip.active b { background:rgba(255,255,255,.2); }
  .st-page .st-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:12px; }
  .st-page .st-card { display:flex; flex-direction:column; gap:6px; background:#fff; border:1px solid #e2e8f0; border-top:4px solid var(--st-c); border-radius:10px; padding:14px; color:inherit; text-decoration:none; min-width:0; transition:box-shadow .15s; }
  .st-page .st-card:hover, .st-page .st-card:focus { box-shadow:0 4px 14px rgba(15,23,42,.1); text-decoration:none; color:inherit; }
  .st-page .st-type { font-size:11px; font-weight:700; color:var(--st-c); text-transform:uppercase; letter-spacing:.3px; display:flex; align-items:center; gap:6px; }
  .st-page .st-title { margin:0; font-size:15px; font-weight:700; color:#0f172a; word-break:break-word; }
  .st-page .st-desc { margin:0; font-size:12.5px; color:#475569; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
  .st-page .st-meta { margin-top:auto; padding-top:6px; display:flex; flex-wrap:wrap; gap:6px; align-items:center; font-size:11.5px; color:#64748b; }
  .st-page .st-tag { border-radius:10px; padding:2px 8px; font-weight:700; font-size:11px; }
  .st-page .st-tag.done { background:#dcfce7; color:#15803d; }
  .st-page .st-tag.prog { background:#fef3c7; color:#b45309; }
  .st-page .st-tag.new { background:#e0e7ff; color:#3730a3; }
  .st-page .st-tag.must { background:#fee2e2; color:#b91c1c; }
  .st-page .st-tag.late { background:#b91c1c; color:#fff; }
  .st-page .st-tag.cat { background:#f1f5f9; color:#475569; }
  .st-page .st-empty { text-align:center; padding:40px 16px; background:#fff; border:1px dashed #cbd5e1; border-radius:10px; color:#64748b; }
  .st-page .st-empty i { font-size:28px; color:#94a3b8; display:block; margin-bottom:8px; }
  .st-page .st-hidden { display:none !important; }
  @media (max-width: 767px) {
    .st-page .st-stats { grid-template-columns:repeat(2, minmax(0, 1fr)); }
    .st-page .st-grid { grid-template-columns:1fr; }
    .st-page .st-search { max-width:none; }
    .st-page .st-tools select.st-select { flex:1 1 140px; }
  }
</style>

<div class="content-wrapper">
  <section class="content-header">
    <h1><?php echo $info['page_heading']; ?></h1>
  </section>
  <section class="content st-page">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="st-top">
      <span class="text-muted">Pitch scripts, step-by-step guides, documents and videos from the company. Open one, learn it, then mark it done.</span>
      <?php if ($can_manage) { ?>
        <a href="<?php echo base_url('manager/self-training/manage'); ?>" class="btn btn-sm btn-default"><i class="fa fa-pencil"></i> Manage material</a>
      <?php } ?>
    </div>

    <div class="st-stats">
      <div class="st-stat"><b><?php echo $summary['assigned']; ?></b><span>Available to you</span></div>
      <div class="st-stat"><b><?php echo $summary['completed']; ?></b><span>Done (<?php echo $pct; ?>%)</span><div class="st-bar"><i style="width:<?php echo $pct; ?>%"></i></div></div>
      <div class="st-stat"><b><?php echo $todo; ?></b><span>Still to do</span></div>
      <div class="st-stat"><b style="<?php echo $summary['mandatory_open'] > 0 ? 'color:#b91c1c;' : ''; ?>"><?php echo $summary['mandatory_open']; ?></b><span>Mandatory not done</span></div>
    </div>

    <?php if (empty($modules)) { ?>
      <div class="st-empty"><i class="fa fa-graduation-cap"></i>No training material has been published for you yet.</div>
    <?php } else { ?>
      <div class="st-tools">
        <label class="st-search"><i class="fa fa-search"></i><input type="search" id="stSearch" placeholder="Search training..." aria-label="Search training"></label>
        <select id="stCat" class="st-select" aria-label="Category">
          <option value="">All categories</option>
          <?php foreach (array_keys($cats) as $c) { ?><option value="<?php echo htmlspecialchars($c); ?>"><?php echo htmlspecialchars($c); ?></option><?php } ?>
        </select>
        <select id="stState" class="st-select" aria-label="Status">
          <option value="">Any status</option>
          <option value="todo">Still to do</option>
          <option value="done">Done</option>
          <option value="must">Mandatory</option>
        </select>
      </div>
      <div class="st-chips" id="stChips">
        <button type="button" class="st-chip active" data-kind="">All <b><?php echo count($modules); ?></b></button>
        <?php foreach ($kinds as $k => $kd) { if (empty($kind_counts[$k])) continue; ?>
          <button type="button" class="st-chip" data-kind="<?php echo $k; ?>" style="--st-c:<?php echo $kd[2]; ?>;"><i class="fa <?php echo $kd[1]; ?>"></i> <?php echo $kd[0]; ?>s <b><?php echo $kind_counts[$k]; ?></b></button>
        <?php } ?>
      </div>

      <div class="st-grid" id="stGrid">
        <?php foreach ($modules as $m) {
          $k = isset($kinds[$m['content_type']]) ? $m['content_type'] : 'guide';
          $kd = $kinds[$k];
          $done = ($m['my_status'] === 'Completed');
          $late = !$done && !empty($m['due_date']) && $m['due_date'] < $today;
          $text = strtolower($m['title'] . ' ' . $m['category'] . ' ' . $m['description'] . ' ' . $kd[0]);
        ?>
          <a class="st-card" href="<?php echo base_url('manager/self-training/view/' . (int)$m['id']); ?>" style="--st-c:<?php echo $kd[2]; ?>;"
             data-kind="<?php echo $k; ?>" data-cat="<?php echo htmlspecialchars($m['category']); ?>" data-done="<?php echo $done ? 1 : 0; ?>" data-must="<?php echo (int)$m['is_mandatory']; ?>" data-text="<?php echo htmlspecialchars($text); ?>">
            <span class="st-type"><i class="fa <?php echo $kd[1]; ?>"></i> <?php echo $kd[0]; ?></span>
            <h4 class="st-title"><?php echo htmlspecialchars($m['title']); ?></h4>
            <?php if ($m['description'] !== '' && $m['description'] !== null) { ?><p class="st-desc"><?php echo htmlspecialchars($m['description']); ?></p><?php } ?>
            <span class="st-meta">
              <?php if ($done) { ?><span class="st-tag done"><i class="fa fa-check"></i> Done</span>
              <?php } elseif ($m['my_status'] === 'In Progress') { ?><span class="st-tag prog">Started</span>
              <?php } else { ?><span class="st-tag new">New</span><?php } ?>
              <?php if ((int)$m['is_mandatory'] === 1) { ?><span class="st-tag must">Mandatory</span><?php } ?>
              <?php if ($late) { ?><span class="st-tag late">Overdue</span><?php } ?>
              <span class="st-tag cat"><?php echo htmlspecialchars($m['category']); ?></span>
              <?php if ((int)$m['duration_minutes'] > 0) { ?><span><i class="fa fa-clock-o"></i> <?php echo (int)$m['duration_minutes']; ?> min</span><?php } ?>
              <?php if (!empty($m['due_date'])) { ?><span><i class="fa fa-calendar"></i> Due <?php echo date('d M Y', strtotime($m['due_date'])); ?></span><?php } ?>
            </span>
          </a>
        <?php } ?>
      </div>
      <div class="st-empty st-hidden" id="stNoMatch"><i class="fa fa-search"></i>Nothing matches. Try another word or filter.</div>
    <?php } ?>
  </section>
</div>

<script>
$(function () {
  var kind = '';
  function apply() {
    var q = $.trim($('#stSearch').val() || '').toLowerCase();
    var cat = $('#stCat').val() || '';
    var st = $('#stState').val() || '';
    var shown = 0;
    $('#stGrid .st-card').each(function () {
      var c = $(this);
      var ok = (!kind || c.data('kind') === kind)
        && (!cat || String(c.data('cat')) === cat)
        && (!q || String(c.data('text')).indexOf(q) !== -1)
        && (!st || (st === 'done' && c.data('done') == 1) || (st === 'todo' && c.data('done') == 0) || (st === 'must' && c.data('must') == 1));
      c.toggleClass('st-hidden', !ok);
      if (ok) shown++;
    });
    $('#stNoMatch').toggleClass('st-hidden', shown > 0);
  }
  $('#stChips').on('click', '.st-chip', function () {
    $('#stChips .st-chip').removeClass('active');
    $(this).addClass('active');
    kind = $(this).data('kind') || '';
    apply();
  });
  $('#stSearch').on('input', apply);
  $('#stCat, #stState').on('change', apply);
});
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
