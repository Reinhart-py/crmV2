<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$base = base_url() . 'manager/';
$open = '';
if ($rec['row']) {
  if ($rec['type'] === 'lead' || $rec['type'] === 'backoffice') $open = $rec['lead_id'] ? $base . 'leads/view/' . (int)$rec['lead_id'] : '';
  elseif ($rec['type'] === 'order') $open = $base . 'orders/view/' . (int)$rec['id'];
  elseif ($rec['type'] === 'kam') $open = $rec['lead_id'] ? $base . 'kam/view/' . (int)$rec['lead_id'] : '';
  elseif ($rec['type'] === 'ticket') $open = $base . 'tickets/view/' . (int)$rec['id'];
}
$kinds = array('change' => 'Edits', 'call' => 'Calls', 'followup' => 'Follow-ups', 'pipeline' => 'Stage changes', 'transfer' => 'Transfers', 'order' => 'Orders');
$count = array();
foreach ($rec['events'] as $e) { $count[$e['kind']] = (isset($count[$e['kind']]) ? $count[$e['kind']] : 0) + 1; }
$val = function ($v) use ($h) { $v = (string)$v; return $v === '' ? '<span class="rh-empty">empty</span>' : nl2br($h(mb_strimwidth($v, 0, 400, '...'))); };
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<style>
  .rh-facts { display:flex; flex-wrap:wrap; gap:8px 22px; padding:14px 16px; font-size:13px; }
  .rh-facts div span { display:block; font-size:11.5px; color:#64748b; }
  .rh-facts div b { font-weight:600; color:#0f172a; }
  .rh-filter { display:flex; flex-wrap:wrap; gap:6px; padding:12px 16px; border-bottom:1px solid #eef2f7; }
  .rh-filter button { border:1px solid #cbd5e1; background:#fff; border-radius:999px; padding:4px 11px; font-size:12px; font-weight:600; color:#475569; }
  .rh-filter button.on { background:#1e3a8a; border-color:#1e3a8a; color:#fff; }
  .rh-day { padding:10px 16px 2px; font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.04em; }
  .rh-ev { display:grid; grid-template-columns:70px 34px minmax(0, 1fr); gap:10px; padding:8px 16px; align-items:start; }
  .rh-time { font-size:12px; color:#64748b; padding-top:7px; text-align:right; }
  .rh-ic { width:30px; height:30px; border-radius:50%; display:grid; place-items:center; background:#eef2ff; color:#3730a3; font-size:13px; }
  .rh-ev.k-call .rh-ic { background:#ecfdf5; color:#047857; } .rh-ev.k-followup .rh-ic { background:#fff7ed; color:#c2410c; }
  .rh-ev.k-pipeline .rh-ic { background:#f0f9ff; color:#0369a1; } .rh-ev.k-transfer .rh-ic { background:#fdf4ff; color:#a21caf; }
  .rh-ev.k-delete .rh-ic { background:#fee2e2; color:#b91c1c; } .rh-ev.k-create .rh-ic { background:#dcfce7; color:#15803d; }
  .rh-t { font-size:13.5px; font-weight:600; color:#0f172a; }
  .rh-t small { font-weight:400; color:#64748b; font-size:12.5px; }
  .rh-lines { margin:3px 0 0; padding:0; list-style:none; font-size:12.5px; color:#334155; }
  .rh-lines li { padding:1px 0; }
  .rh-lines .f { font-weight:600; }
  .rh-lines .old { color:#64748b; text-decoration:line-through; text-decoration-color:rgba(100,116,139,.5); }
  .rh-lines .arr { color:#94a3b8; margin:0 4px; }
  .rh-empty { color:#94a3b8; font-style:italic; }
  [data-theme="dark"] .rh-facts div b, [data-theme="dark"] .rh-t { color:#e2e8f0; }
  [data-theme="dark"] .rh-lines { color:#cbd5e1; }
  [data-theme="dark"] .rh-filter { border-color:rgba(255,255,255,.07); }
  [data-theme="dark"] .rh-filter button { background:transparent; border-color:rgba(255,255,255,.18); color:#cbd5e1; }
  [data-theme="dark"] .rh-filter button.on { background:#3b5bdb; border-color:#3b5bdb; color:#fff; }
  [data-theme="dark"] .rh-ic { background:rgba(99,102,241,.18); color:#c7d2fe; }
</style>
<div class="content-wrapper sx">
  <?php echo crm_tabbar('activity', 'system/record-tracking'); ?>
  <section class="sx-head">
    <h1><?php echo $h($rec['name']); ?><small><?php echo $h($types[$rec['type']][0]); ?> #<?php echo (int)$rec['id']; ?> &middot; full history</small></h1>
    <div class="sx-btns">
      <a href="<?php echo $base; ?>system/record-tracking" class="btn btn-default"><i class="fa fa-arrow-left"></i> All changes</a>
      <?php if ($open) { ?><a href="<?php echo $open; ?>" class="btn btn-primary"><i class="fa fa-external-link"></i> Open record</a><?php } ?>
    </div>
  </section>

  <section class="content">
    <div class="sx-card">
      <?php if (!$rec['row']) { ?>
        <div class="sx-help"><i class="fa fa-info-circle"></i> This record no longer exists. Its history is shown below.</div>
      <?php } elseif ($rec['facts']) { ?>
        <div class="rh-facts">
          <?php foreach ($rec['facts'] as $k => $v) { if ($v === '' || $v === null) continue; ?>
            <div><span><?php echo $h($k); ?></span><b><?php echo $h(preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:/', (string)$v) ? date('d M Y, h:i A', strtotime($v)) : $v); ?></b></div>
          <?php } ?>
        </div>
      <?php } ?>
    </div>

    <div class="sx-card">
      <div class="rh-filter" id="rhFilter">
        <button type="button" class="on" data-k="">All <?php echo count($rec['events']); ?></button>
        <?php foreach ($kinds as $k => $label) { if (empty($count[$k])) continue; ?>
          <button type="button" data-k="<?php echo $k; ?>"><?php echo $h($label); ?> <?php echo (int)$count[$k]; ?></button>
        <?php } ?>
      </div>
      <?php if (!$rec['events']) { ?>
        <div class="sx-empty"><i class="fa fa-history"></i>Nothing recorded for this record yet.</div>
      <?php } else { $lastDay = ''; ?>
      <div id="rhList">
        <?php foreach ($rec['events'] as $e) {
          $day = date('Y-m-d', strtotime($e['at']));
          if ($day !== $lastDay) { $lastDay = $day; ?>
            <div class="rh-day" data-day="1"><?php echo date('l, d M Y', strtotime($day)); ?></div>
          <?php } ?>
          <div class="rh-ev k-<?php echo $h($e['kind']); ?>" data-k="<?php echo $h($e['kind'] === 'delete' ? 'change' : $e['kind']); ?>">
            <div class="rh-time"><?php echo date('h:i A', strtotime($e['at'])); ?></div>
            <div class="rh-ic"><i class="fa <?php echo $h($e['icon']); ?>"></i></div>
            <div>
              <div class="rh-t"><?php echo $h($e['title']); ?><?php if ($e['who'] !== '') { ?> <small>by <?php echo $h($e['who']); ?></small><?php } ?></div>
              <?php if ($e['lines']) { ?>
              <ul class="rh-lines">
                <?php foreach ($e['lines'] as $l) { ?>
                  <li><span class="f"><?php echo $h($l[0]); ?>:</span>
                    <?php if ($e['kind'] === 'change' || $e['kind'] === 'transfer' || ($e['kind'] === 'pipeline' && $l[0] === 'Stage')) { ?><span class="old"><?php echo $val($l[1]); ?></span><span class="arr">&rarr;</span><?php echo $val($l[2]); ?>
                    <?php } else { echo $val($l[2] !== '' ? $l[2] : $l[1]); } ?>
                  </li>
                <?php } ?>
              </ul>
              <?php } ?>
            </div>
          </div>
        <?php } ?>
      </div>
      <?php } ?>
    </div>
  </section>
</div>
<script>
(function () {
  var bar = document.getElementById('rhFilter');
  if (!bar) return;
  bar.addEventListener('click', function (e) {
    var b = e.target.closest('button'); if (!b) return;
    [].forEach.call(bar.querySelectorAll('button'), function (x) { x.classList.toggle('on', x === b); });
    var k = b.getAttribute('data-k');
    var rows = document.querySelectorAll('#rhList > div');
    var lastDay = null, shown = 0;
    [].forEach.call(rows, function (r) {
      if (r.hasAttribute('data-day')) { if (lastDay) lastDay.style.display = shown ? '' : 'none'; lastDay = r; shown = 0; return; }
      var on = !k || r.getAttribute('data-k') === k;
      r.style.display = on ? '' : 'none';
      if (on) shown++;
    });
    if (lastDay) lastDay.style.display = shown ? '' : 'none';
  });
})();
</script>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
