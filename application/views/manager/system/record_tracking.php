<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$base = base_url() . 'manager/system/record-tracking';
$qs = function ($over = array()) use ($f, $page) {
  $p = array_merge(array('q' => $f['q'], 'type' => $f['type'], 'dept' => $f['dept'] ?: '', 'user' => $f['user'] ?: '', 'action' => $f['action'], 'from' => $f['from'], 'to' => $f['to'], 'page' => $page), $over);
  if ((int)$p['page'] <= 1) unset($p['page']);
  return '?' . http_build_query(array_filter($p, function ($v) { return $v !== '' && $v !== null; }));
};
$short = function ($v) use ($h) {
  $v = (string)$v;
  if ($v === '') return '<span class="rt-empty">empty</span>';
  return '<span title="' . $h(mb_strlen($v) > 90 ? $v : '') . '">' . $h(mb_strimwidth($v, 0, 90, '...')) . '</span>';
};
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<style>
  .rt-feed { list-style:none; margin:0; padding:0; }
  .rt-item { display:grid; grid-template-columns:150px minmax(0, 1fr); gap:14px; padding:12px 16px; border-top:1px solid #eef2f7; }
  .rt-item:first-child { border-top:0; }
  .rt-when { font-size:12.5px; color:#64748b; line-height:1.45; }
  .rt-when b { display:block; color:#0f172a; font-weight:600; font-size:13px; }
  .rt-head { display:flex; flex-wrap:wrap; align-items:center; gap:6px 8px; font-size:13.5px; color:#0f172a; }
  .rt-head a.rt-rec { font-weight:600; color:#1e3a8a; }
  .rt-type { font-size:11px; font-weight:600; border-radius:999px; padding:2px 8px; background:#eef2ff; color:#3730a3; }
  .rt-type.t-order { background:#ecfdf5; color:#047857; } .rt-type.t-kam { background:#fff7ed; color:#c2410c; }
  .rt-type.t-backoffice { background:#f0f9ff; color:#0369a1; } .rt-type.t-ticket { background:#fdf2f8; color:#be185d; }
  .rt-act { font-size:11px; font-weight:700; border-radius:6px; padding:1px 7px; }
  .rt-act.a-create { background:#dcfce7; color:#166534; } .rt-act.a-delete { background:#fee2e2; color:#991b1b; }
  .rt-who { color:#475569; font-size:12.5px; }
  .rt-ch { margin:6px 0 0; padding:0; list-style:none; font-size:12.5px; color:#334155; }
  .rt-ch li { padding:2px 0; }
  .rt-ch .f { font-weight:600; color:#0f172a; }
  .rt-ch .arr { color:#94a3b8; margin:0 4px; }
  .rt-ch .old { color:#64748b; text-decoration:line-through; text-decoration-color:rgba(100,116,139,.5); }
  .rt-empty { color:#94a3b8; font-style:italic; }
  .rt-page { font-size:11.5px; color:#94a3b8; margin-top:4px; }
  .rt-more { color:#64748b; font-size:12px; }
  .rt-pager { display:flex; justify-content:space-between; align-items:center; padding:10px 16px; border-top:1px solid #eef2f7; font-size:12.5px; color:#64748b; }
  .rt-note { font-size:12.5px; color:#64748b; margin:0 0 12px; }
  @media (max-width: 767px) { .rt-item { grid-template-columns:1fr; gap:4px; } }
  [data-theme="dark"] .rt-item, [data-theme="dark"] .rt-pager { border-color:rgba(255,255,255,.07); }
  [data-theme="dark"] .rt-when b, [data-theme="dark"] .rt-head, [data-theme="dark"] .rt-ch .f { color:#e2e8f0; }
  [data-theme="dark"] .rt-ch, [data-theme="dark"] .rt-who { color:#cbd5e1; }
  [data-theme="dark"] .rt-head a.rt-rec { color:#93c5fd; }
  [data-theme="dark"] .rt-type { background:rgba(99,102,241,.18); color:#c7d2fe; }
  [data-theme="dark"] .rt-type.t-order { background:rgba(16,185,129,.15); color:#6ee7b7; } [data-theme="dark"] .rt-type.t-kam { background:rgba(251,146,60,.15); color:#fdba74; }
  [data-theme="dark"] .rt-type.t-backoffice { background:rgba(56,189,248,.15); color:#7dd3fc; } [data-theme="dark"] .rt-type.t-ticket { background:rgba(236,72,153,.15); color:#f9a8d4; }
</style>
<div class="content-wrapper sx">
  <?php echo crm_tabbar('activity', 'system/record-tracking'); ?>
  <section class="sx-head">
    <h1>Record Tracking<small>Every change to leads, database records, orders, KAM clients, back office and tickets: who changed what, and when</small></h1>
  </section>

  <section class="content">
    <div class="sx-kpis">
      <div class="sx-kpi"><span class="ic"><i class="fa fa-pencil"></i></span><div><b><?php echo number_format((int)$kpi['changes']); ?></b><span>Changes</span></div></div>
      <div class="sx-kpi s"><span class="ic"><i class="fa fa-folder-open-o"></i></span><div><b><?php echo number_format((int)$kpi['records']); ?></b><span>Records changed</span></div></div>
      <div class="sx-kpi g"><span class="ic"><i class="fa fa-users"></i></span><div><b><?php echo number_format((int)$kpi['people']); ?></b><span>People</span></div></div>
      <a class="sx-kpi r" href="<?php echo $base . $qs(array('action' => 'delete', 'page' => 1)); ?>"><span class="ic"><i class="fa fa-trash"></i></span><div><b><?php echo number_format((int)$kpi['deleted']); ?></b><span>Deleted</span></div></a>
    </div>

    <div class="sx-card">
      <form class="sx-bar" method="get" action="<?php echo $base; ?>">
        <input type="text" class="form-control sx-q" name="q" value="<?php echo $h($f['q']); ?>" placeholder="Company, ref, order no, phone or record no">
        <select name="type" class="form-control" aria-label="Record type">
          <option value="">All records</option>
          <?php foreach ($types as $k => $t) { ?><option value="<?php echo $k; ?>" <?php echo $f['type'] === $k ? 'selected' : ''; ?>><?php echo $h($t[0]); ?></option><?php } ?>
        </select>
        <select name="dept" class="form-control" aria-label="Department">
          <option value="">All departments</option>
          <?php foreach ($departments as $d) { ?><option value="<?php echo (int)$d['id']; ?>" <?php echo $f['dept'] === (int)$d['id'] ? 'selected' : ''; ?>><?php echo $h($d['title']); ?></option><?php } ?>
        </select>
        <select name="user" class="form-control" aria-label="Person">
          <option value="">Everyone</option>
          <?php foreach ($people as $p) { ?><option value="<?php echo (int)$p['id']; ?>" <?php echo $f['user'] === (int)$p['id'] ? 'selected' : ''; ?>><?php echo $h($p['name']); ?></option><?php } ?>
        </select>
        <select name="action" class="form-control" aria-label="Kind of change">
          <option value="">Any change</option>
          <option value="update" <?php echo $f['action'] === 'update' ? 'selected' : ''; ?>>Edited</option>
          <option value="create" <?php echo $f['action'] === 'create' ? 'selected' : ''; ?>>Created</option>
          <option value="delete" <?php echo $f['action'] === 'delete' ? 'selected' : ''; ?>>Deleted</option>
        </select>
        <input type="date" class="form-control" name="from" value="<?php echo $h($f['from']); ?>" aria-label="From">
        <input type="date" class="form-control" name="to" value="<?php echo $h($f['to']); ?>" aria-label="To">
        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Show</button>
        <a href="<?php echo $base; ?>" class="btn btn-default">Reset</a>
      </form>

      <?php if (!$feed) { ?>
        <div class="sx-empty"><i class="fa fa-search"></i>No changes found for these filters.</div>
      <?php } else { ?>
      <ul class="rt-feed">
        <?php foreach ($feed as $e) {
          $url = $base . '/' . $e['rtype'] . '/' . $e['rid'];
        ?>
        <li class="rt-item">
          <div class="rt-when"><b><?php echo date('d M Y', strtotime($e['at'])); ?></b><?php echo date('h:i:s A', strtotime($e['at'])); ?></div>
          <div>
            <div class="rt-head">
              <span class="rt-type t-<?php echo $h($e['rtype']); ?>"><?php echo $h($types[$e['rtype']][0]); ?></span>
              <a class="rt-rec" href="<?php echo $url; ?>"><?php echo $h($e['name'] !== '' ? $e['name'] : '#' . $e['rid']); ?></a>
              <?php if ($e['action'] === 'create') { ?><span class="rt-act a-create">Created</span><?php } elseif ($e['action'] === 'delete') { ?><span class="rt-act a-delete">Deleted</span><?php } ?>
              <span class="rt-who">by <?php echo $h($e['user'] !== '' ? $e['user'] : 'System'); ?><?php echo $e['dept'] !== '' ? ' (' . $h($e['dept']) . ')' : ''; ?></span>
            </div>
            <?php if ($e['items'] && $e['action'] !== 'create') { ?>
            <ul class="rt-ch">
              <?php foreach (array_slice($e['items'], 0, 6) as $it) { ?>
                <li><span class="f"><?php echo $h($it[0]); ?>:</span> <?php if ($e['action'] === 'delete') { echo $h($it[1]); } else { ?><span class="old"><?php echo $short($it[1]); ?></span><span class="arr">&rarr;</span><?php echo $short($it[2]); ?><?php } ?></li>
              <?php } ?>
              <?php if (count($e['items']) > 6) { ?><li class="rt-more"><a href="<?php echo $url; ?>">+<?php echo count($e['items']) - 6; ?> more changes</a></li><?php } ?>
            </ul>
            <?php } ?>
            <div class="rt-page">From: <?php echo $h($e['page'] !== '' ? $e['page'] : 'system'); ?></div>
          </div>
        </li>
        <?php } ?>
      </ul>
      <div class="rt-pager">
        <span>Page <?php echo (int)$page; ?></span>
        <span>
          <?php if ($page > 1) { ?><a class="btn btn-default btn-sm" href="<?php echo $base . $qs(array('page' => $page - 1)); ?>"><i class="fa fa-chevron-left"></i> Newer</a><?php } ?>
          <?php if ($more) { ?><a class="btn btn-default btn-sm" href="<?php echo $base . $qs(array('page' => $page + 1)); ?>">Older <i class="fa fa-chevron-right"></i></a><?php } ?>
        </span>
      </div>
      <?php } ?>
    </div>
    <p class="rt-note"><i class="fa fa-info-circle"></i> Field changes are recorded from <?php echo $since ? date('d M Y', strtotime($since)) : 'today'; ?>. Calls, follow-ups, stage changes, transfers and order steps from before that date show on each record's history.</p>
  </section>
</div>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
