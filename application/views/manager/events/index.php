<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
// Events & Festivals (view only). Everything here is published by HR / Admin from "Manage Events & Festivals".
$ev_types = $types + array('award' => array('Award', '#7c3aed'));
$ev_icons = array('public_holiday' => 'fa-flag', 'festival' => 'fa-star', 'company_event' => 'fa-users', 'award' => 'fa-trophy');
$today = date('Y-m-d');
$now_year = (int)date('Y');
$counts = array();
$by_month = array();
foreach ($items as $it) {
  $counts[$it['type']] = (isset($counts[$it['type']]) ? $counts[$it['type']] : 0) + 1;
  $by_month[(int)date('n', strtotime($it['start']))][] = $it;
}
$ev_when = function ($it) {
  $s = strtotime($it['start']);
  $out = date('D, d M', $s);
  if (!empty($it['end']) && substr($it['end'], 0, 10) > substr($it['start'], 0, 10)) {
    $out .= ' - ' . date('D, d M', strtotime($it['end']));
  } elseif (empty($it['all_day'])) {
    $out .= ' &middot; ' . date('h:i A', $s) . (!empty($it['end']) ? ' - ' . date('h:i A', strtotime($it['end'])) : '');
  }
  return $out;
};
$ev_card = function ($it, $compact = false) use ($ev_types, $ev_icons, $ev_when, $today) {
  $t = $ev_types[$it['type']];
  $s = substr($it['start'], 0, 10);
  $e = !empty($it['end']) ? substr($it['end'], 0, 10) : $s;
  $state = $e < $today ? ' is-past' : (($s <= $today && $e >= $today) ? ' is-today' : '');
  $h = '<div class="ef-card' . $state . '" data-type="' . $it['type'] . '" style="--ef-c:' . $t[1] . ';">';
  $h .= '<div class="ef-date"><strong>' . date('d', strtotime($it['start'])) . '</strong><span>' . date('M', strtotime($it['start'])) . '</span></div>';
  $h .= '<div class="ef-main">';
  $h .= '<span class="ef-type"><i class="fa ' . $ev_icons[$it['type']] . '"></i> ' . $t[0] . ($state === ' is-today' ? ' &middot; Today' : '') . '</span>';
  $h .= '<h5 class="ef-title">' . htmlspecialchars($it['title']) . '</h5>';
  $meta = $ev_when($it);
  if ($it['kind'] === 'award' && $it['employee'] !== '') $meta = '<i class="fa fa-user"></i> ' . htmlspecialchars($it['employee']) . ($it['category'] !== '' ? ' &middot; ' . htmlspecialchars($it['category']) : '');
  elseif ($it['location'] !== '') $meta .= ' &middot; <i class="fa fa-map-marker"></i> ' . htmlspecialchars($it['location']);
  $h .= '<span class="ef-meta">' . $meta . '</span>';
  if (!$compact && $it['description'] !== '') $h .= '<p class="ef-desc">' . nl2br(htmlspecialchars($it['description'])) . '</p>';
  $h .= '</div></div>';
  return $h;
};
?>
<style>
  .ef-page .ef-top { display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:14px; }
  .ef-page .ef-years { display:inline-flex; border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#fff; }
  .ef-page .ef-years a { padding:7px 16px; font-weight:700; color:#475569; text-decoration:none; border-right:1px solid #e2e8f0; }
  .ef-page .ef-years a:last-child { border-right:0; }
  .ef-page .ef-years a.active { background:#1e3a8a; color:#fff; }
  .ef-page .ef-highlights { display:grid; grid-template-columns:1fr 2fr; gap:12px; margin-bottom:16px; }
  .ef-page .ef-box { background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:14px; min-width:0; }
  .ef-page .ef-box h4 { margin:0 0 10px; font-size:13px; font-weight:700; color:#0f172a; display:flex; align-items:center; gap:7px; }
  .ef-page .ef-box h4 i { color:#1e3a8a; }
  .ef-page .ef-next { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:8px; }
  .ef-page .ef-none { font-size:13px; color:#64748b; padding:10px; background:#f8fafc; border-radius:8px; }
  .ef-page .ef-chips { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:14px; }
  .ef-page .ef-chip { border:1px solid #cbd5e1; background:#fff; border-radius:999px; padding:5px 12px; font-size:12px; font-weight:700; color:#475569; cursor:pointer; display:inline-flex; align-items:center; gap:6px; }
  .ef-page .ef-chip i { color:var(--ef-c, #1e3a8a); }
  .ef-page .ef-chip b { background:#f1f5f9; border-radius:10px; padding:0 6px; font-size:11px; }
  .ef-page .ef-chip.active { background:#1e3a8a; border-color:#1e3a8a; color:#fff; }
  .ef-page .ef-chip.active i { color:#fff; }
  .ef-page .ef-chip.active b { background:rgba(255,255,255,.2); }
  .ef-page .ef-month { margin-bottom:16px; }
  .ef-page .ef-month-head { display:flex; align-items:center; gap:8px; margin:0 0 8px; font-size:14px; font-weight:700; color:#0f172a; }
  .ef-page .ef-month-head span { font-size:11px; font-weight:700; color:#64748b; background:#f1f5f9; border-radius:10px; padding:1px 8px; }
  .ef-page .ef-month-head.is-now { color:#1e3a8a; }
  .ef-page .ef-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(260px, 1fr)); gap:10px; }
  .ef-page .ef-card { display:flex; gap:12px; background:#fff; border:1px solid #e2e8f0; border-left:4px solid var(--ef-c); border-radius:10px; padding:12px; min-width:0; }
  .ef-page .ef-card.is-past { opacity:.55; }
  .ef-page .ef-card.is-today { box-shadow:0 0 0 2px var(--ef-c); }
  .ef-page .ef-date { flex-shrink:0; width:46px; text-align:center; border-radius:8px; background:#f8fafc; padding:5px 0; align-self:flex-start; }
  .ef-page .ef-date strong { display:block; font-size:19px; line-height:1.1; color:#0f172a; }
  .ef-page .ef-date span { font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; }
  .ef-page .ef-main { min-width:0; }
  .ef-page .ef-type { font-size:11px; font-weight:700; color:var(--ef-c); text-transform:uppercase; letter-spacing:.3px; }
  .ef-page .ef-title { margin:2px 0 3px; font-size:14px; font-weight:700; color:#0f172a; word-break:break-word; }
  .ef-page .ef-meta { display:block; font-size:12px; color:#64748b; }
  .ef-page .ef-desc { margin:6px 0 0; font-size:12.5px; color:#334155; }
  .ef-page .ef-empty { text-align:center; padding:40px 16px; background:#fff; border:1px dashed #cbd5e1; border-radius:10px; color:#64748b; }
  .ef-page .ef-empty i { font-size:28px; color:#94a3b8; display:block; margin-bottom:8px; }
  .ef-page .ef-hidden { display:none !important; }
  @media (max-width: 991px) {
    .ef-page .ef-highlights { grid-template-columns:1fr; }
  }
  @media (max-width: 767px) {
    .ef-page .ef-next { grid-template-columns:1fr; }
    .ef-page .ef-grid { grid-template-columns:1fr; }
    .ef-page .ef-years a { padding:7px 12px; }
  }
</style>

<div class="content-wrapper">
  <section class="content-header">
    <h1><?php echo $info['page_heading']; ?></h1>
  </section>
  <section class="content ef-page">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="ef-top">
      <div class="ef-years" role="tablist" aria-label="Year">
        <?php for ($y = $now_year - 1; $y <= $now_year + 1; $y++) { ?>
          <a href="<?php echo base_url('manager/events?year=' . $y); ?>" class="<?php echo $y === $year ? 'active' : ''; ?>"><?php echo $y; ?></a>
        <?php } ?>
      </div>
      <?php if ($can_manage) { ?>
        <a href="<?php echo base_url('manager/events/manage?year=' . $year); ?>" class="btn btn-sm btn-default"><i class="fa fa-pencil"></i> Manage events</a>
      <?php } ?>
    </div>

    <div class="ef-highlights">
      <div class="ef-box">
        <h4><i class="fa fa-sun-o"></i> Today &middot; <?php echo date('D, d M'); ?></h4>
        <?php if (!empty($today_items)) { foreach ($today_items as $it) { echo $ev_card($it, true); } } else { ?>
          <div class="ef-none">Nothing scheduled for today.</div>
        <?php } ?>
      </div>
      <div class="ef-box">
        <h4><i class="fa fa-forward"></i> Coming up next</h4>
        <?php if (!empty($next_items)) { ?>
          <div class="ef-next"><?php foreach ($next_items as $it) { echo $ev_card($it, true); } ?></div>
        <?php } else { ?>
          <div class="ef-none">Nothing has been announced yet.</div>
        <?php } ?>
      </div>
    </div>

    <div class="ef-chips" id="efChips">
      <button type="button" class="ef-chip active" data-filter="all">All <b><?php echo count($items); ?></b></button>
      <?php foreach ($ev_types as $k => $t) { ?>
        <button type="button" class="ef-chip" data-filter="<?php echo $k; ?>" style="--ef-c:<?php echo $t[1]; ?>;"><i class="fa <?php echo $ev_icons[$k]; ?>"></i> <?php echo $t[0]; ?>s <b><?php echo isset($counts[$k]) ? $counts[$k] : 0; ?></b></button>
      <?php } ?>
    </div>

    <?php if (empty($items)) { ?>
      <div class="ef-empty"><i class="fa fa-calendar-o"></i>Nothing has been published for <?php echo $year; ?> yet.</div>
    <?php } else { ?>
      <?php foreach ($by_month as $m => $list) { ?>
        <div class="ef-month">
          <h4 class="ef-month-head<?php echo ($year === $now_year && $m === (int)date('n')) ? ' is-now' : ''; ?>"><?php echo date('F', mktime(0, 0, 0, $m, 1, $year)) . ' ' . $year; ?> <span><?php echo count($list); ?></span></h4>
          <div class="ef-grid"><?php foreach ($list as $it) { echo $ev_card($it); } ?></div>
        </div>
      <?php } ?>
      <div class="ef-empty ef-hidden" id="efNoMatch"><i class="fa fa-filter"></i>Nothing of this type in <?php echo $year; ?>.</div>
    <?php } ?>
  </section>
</div>

<script>
$(function () {
  // Filter chips: show one type (the month headings hide when they have nothing left).
  $('#efChips').on('click', '.ef-chip', function () {
    var f = $(this).data('filter');
    $('#efChips .ef-chip').removeClass('active');
    $(this).addClass('active');
    var shown = 0;
    $('.ef-month').each(function () {
      var cards = $(this).find('.ef-card');
      cards.each(function () { $(this).toggleClass('ef-hidden', f !== 'all' && $(this).data('type') !== f); });
      var visible = cards.not('.ef-hidden').length;
      $(this).toggleClass('ef-hidden', visible === 0);
      $(this).find('.ef-month-head span').text(visible);
      shown += visible;
    });
    $('#efNoMatch').toggleClass('ef-hidden', shown > 0);
  });
});
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
