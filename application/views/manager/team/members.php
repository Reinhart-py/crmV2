<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-team.css?v=20261106_5">
<?php
$list = isset($product_list) && is_array($product_list) ? $product_list : array();
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$deps = array(); $online = 0;
foreach ($list as $li) {
	$d = trim((string)$li['department']);
	if ($d !== '') $deps[$d] = isset($deps[$d]) ? $deps[$d] + 1 : 1;
	if (!empty($li['online_now'])) $online++;
}
arsort($deps);
$colors = array('#1e3a8a', '#0f766e', '#7c3aed', '#be185d', '#b45309', '#0369a1', '#4d7c0f', '#9f1239', '#334155', '#6d28d9');
$color = function ($s) use ($colors) { $x = 0; $s = (string)$s; for ($i = 0; $i < strlen($s); $i++) $x = ($x * 31 + ord($s[$i])) % 4294967296; return $colors[$x % count($colors)]; };
$initials = function ($n) { $w = preg_split('/\s+/', trim((string)$n)); $w = array_values(array_filter($w, function ($x) { return preg_match('/[A-Za-z0-9]/', $x); })); $a = isset($w[0]) ? $w[0][0] : '?'; if (count($w) > 1) $a .= $w[count($w) - 1][0]; return strtoupper($a); };
$base = base_url();
?>

<div class="content-wrapper px-4">
<?php echo crm_mz_tabbar(); ?>
  <section class="content-header">
    <h1><?php echo $h($info['page_heading']); ?></h1>
  </section>
  <section class="content">
    <div class="tm" id="tmm">
      <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> <?php echo get_message($this->session->flashdata('message'),'message'); ?>

      <?php if (!empty($viewing)) { ?>
      <div class="tmm-back">
        <a href="<?php echo $base; ?>manager/team/members"><i class="fa fa-arrow-left"></i> All my team</a>
        <span style="color:var(--tm-faint)">/</span>
        <b>Team of <?php echo $h($viewing['name']); ?></b>
        <a class="tm-btn" style="margin-left:auto" href="<?php echo $base; ?>manager/team/chart?id=<?php echo (int)$viewing['id']; ?>"><i class="fa fa-sitemap"></i> On the chart</a>
      </div>
      <?php } ?>

      <?php if (!empty($list)) { ?>
      <div class="tmm-kpis" role="group" aria-label="Show">
        <button type="button" class="tmm-kpi is-on" data-f="">All <b><?php echo count($list); ?></b></button>
        <button type="button" class="tmm-kpi" data-f="on"><span class="dt"></span>Online <b><?php echo $online; ?></b></button>
        <button type="button" class="tmm-kpi" data-f="lead">Lead a team <b><?php echo count(array_filter($list, function ($x) { return $x['direct_n'] > 0; })); ?></b></button>
        <?php foreach ($deps as $d => $n) { ?>
        <button type="button" class="tmm-kpi" data-f="dep:<?php echo $h(strtolower($d)); ?>"><?php echo $h($d); ?> <b><?php echo $n; ?></b></button>
        <?php } ?>
      </div>

      <div class="tmm-card">
        <div class="tmm-top">
          <div class="ttl"><span id="tmmCount"><?php echo count($list); ?></span> <small>people</small></div>
          <div class="tc-find">
            <i class="fa fa-search" aria-hidden="true"></i>
            <input type="search" id="tmmQ" placeholder="Search name, email, phone…" aria-label="Search members" autocomplete="off">
          </div>
        </div>
        <div style="overflow:auto">
        <table class="tmm-tbl" id="tmmT">
          <thead>
            <tr>
              <th data-k="n">Member <i class="fa fa-sort ar"></i></th>
              <th data-k="d" class="c-dep">Department <i class="fa fa-sort ar"></i></th>
              <th data-k="" class="c-ct">Contact</th>
              <th data-k="m" class="c-mgr">Reports to <i class="fa fa-sort ar"></i></th>
              <th data-k="o" class="c-loc">Office <i class="fa fa-sort ar"></i></th>
              <th data-k="t" class="c-team" title="Direct reports / whole team">Team <i class="fa fa-sort ar"></i></th>
              <th data-k="" class="c-act"><span class="sr-only">Actions</span></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($list as $li) {
            $id = (int)$li['id'];
            $q = strtolower(implode(' ', array($li['name'], $li['email'], $li['mobile'], $li['role'], $li['department'], $li['office'], $li['manager_name'], sprintf('EMP-%05d', $id))));
          ?>
            <tr data-q="<?php echo $h($q); ?>" data-dep="<?php echo $h(strtolower(trim((string)$li['department']))); ?>" data-on="<?php echo (int)$li['online_now']; ?>" data-lead="<?php echo $li['direct_n'] > 0 ? 1 : 0; ?>"
                data-n="<?php echo $h(strtolower($li['name'])); ?>" data-d="<?php echo $h(strtolower($li['department'])); ?>" data-m="<?php echo $h(strtolower($li['manager_name'])); ?>" data-o="<?php echo $h(strtolower($li['office'])); ?>" data-t="<?php echo (int)$li['team_n']; ?>">
              <td class="c-who">
                <div class="tmm-who">
                  <span class="tm-avw"><span class="tm-av" style="background:<?php echo $color($li['name']); ?>"><?php if ($li['avatar_url']) { ?><img src="<?php echo $h($li['avatar_url']); ?>" alt="" loading="lazy"><?php } else { echo $h($initials($li['name'])); } ?></span><?php if ($li['online_now']) { ?><span class="tm-dot" title="Online"></span><?php } ?></span>
                  <div>
                    <div class="nm"><?php echo $h($li['name']); ?></div>
                    <div class="sb"><?php echo $h($li['role']); ?><span class="c-id"> · <?php echo sprintf('EMP-%05d', $id); ?></span></div>
                  </div>
                </div>
              </td>
              <td class="c-dep"><?php if (trim((string)$li['department']) !== '') { ?><span class="tmm-chip"><?php echo $h($li['department']); ?></span><?php } ?></td>
              <td class="c-ct tmm-ct">
                <?php if (!empty($li['mobile'])) { ?><a href="tel:<?php echo $h(preg_replace('/[^\d+]/', '', $li['mobile'])); ?>"><i class="fa fa-phone" style="width:14px;color:var(--tm-faint)"></i> <?php echo $h($li['mobile']); ?></a><?php } ?>
                <?php if (!empty($li['email'])) { ?><a href="mailto:<?php echo $h($li['email']); ?>"><?php echo $h($li['email']); ?></a><?php } ?>
              </td>
              <td class="c-mgr"><?php echo $h($li['manager_name']); ?></td>
              <td class="c-loc"><?php echo $h($li['office']); ?></td>
              <td class="c-team<?php echo $li['direct_n'] > 0 ? '' : ' z'; ?>"><?php if ($li['direct_n'] > 0) { ?><span class="tmm-n"><?php echo (int)$li['direct_n']; ?></span> <span style="color:var(--tm-muted)">/ <?php echo (int)$li['team_n']; ?></span><?php } else { ?><span class="tmm-n z">—</span><?php } ?></td>
              <td class="c-act">
                <div class="tmm-acts">
                  <a class="tm-btn ic" href="<?php echo $base; ?>manager/team/chart?id=<?php echo $id; ?>" title="Show <?php echo $h($li['name']); ?> on the chart" aria-label="Show on the chart"><i class="fa fa-sitemap"></i></a>
                  <?php if ($li['direct_n'] > 0) { ?>
                  <a class="tm-btn" href="<?php echo $base; ?>manager/team/members/details/<?php echo $id; ?>" title="People in <?php echo $h($li['name']); ?>'s team"><i class="fa fa-users"></i><span class="t"> Team</span></a>
                  <?php } ?>
                </div>
              </td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
        </div>
        <div class="tmm-empty" id="tmmNone" hidden><i class="fa fa-search"></i>No one matches. <button type="button" class="tm-btn" id="tmmClear" style="margin-left:6px">Clear</button></div>
      </div>
      <?php } else { ?>
      <div class="tmm-card"><div class="tmm-empty"><i class="fa fa-users"></i>No one reports to <?php echo !empty($viewing) ? $h($viewing['name']) : 'you'; ?> yet.</div></div>
      <?php } ?>
    </div>
  </section>
</div>

<script>
(function () {
  var t = document.getElementById('tmmT'); if (!t) return;
  var rows = [].slice.call(t.tBodies[0].rows), q = document.getElementById('tmmQ'), f = '', sk = '', sd = 1;
  var key = 'tmmF:' + location.pathname;
  function apply() {
    var s = q.value.trim().toLowerCase(), n = 0;
    rows.forEach(function (r) {
      var ok = (!s || r.getAttribute('data-q').indexOf(s) >= 0) &&
        (!f || (f === 'on' && r.getAttribute('data-on') === '1') || (f === 'lead' && r.getAttribute('data-lead') === '1') || (f.indexOf('dep:') === 0 && r.getAttribute('data-dep') === f.slice(4)));
      r.hidden = !ok; if (ok) n++;
    });
    document.getElementById('tmmCount').textContent = n;
    document.getElementById('tmmNone').hidden = n > 0;
  }
  document.querySelectorAll('.tmm-kpi').forEach(function (b) {
    b.addEventListener('click', function () {
      f = b.getAttribute('data-f');
      document.querySelectorAll('.tmm-kpi').forEach(function (x) { x.classList.toggle('is-on', x === b); x.setAttribute('aria-pressed', x === b ? 'true' : 'false'); });
      try { sessionStorage.setItem(key, f); } catch (e) {}
      apply();
    });
  });
  q.addEventListener('input', apply);
  document.getElementById('tmmClear').addEventListener('click', function () { q.value = ''; document.querySelector('.tmm-kpi[data-f=""]').click(); });
  t.tHead.querySelectorAll('th[data-k]').forEach(function (th) {
    var k = th.getAttribute('data-k'); if (!k) return;
    th.tabIndex = 0;
    function go() {
      sd = sk === k ? -sd : (k === 't' ? -1 : 1); sk = k;
      t.tHead.querySelectorAll('th').forEach(function (x) { x.classList.toggle('is-sort', x === th); var i = x.querySelector('.ar'); if (i) i.className = 'fa ar ' + (x === th ? (sd > 0 ? 'fa-sort-asc' : 'fa-sort-desc') : 'fa-sort'); });
      rows.sort(function (a, b) {
        var x = a.getAttribute('data-' + k), y = b.getAttribute('data-' + k);
        if (k === 't') return (x - y) * sd;
        if (!x !== !y) return x ? -1 : 1; // blanks last
        return x.localeCompare(y) * sd;
      });
      rows.forEach(function (r) { t.tBodies[0].appendChild(r); });
    }
    th.addEventListener('click', go);
    th.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); go(); } });
  });
  try { var saved = sessionStorage.getItem(key); if (saved) { var b = document.querySelector('.tmm-kpi[data-f="' + saved.replace(/"/g, '') + '"]'); if (b) b.click(); } } catch (e) {}
  document.addEventListener('keydown', function (e) { if (e.key === '/' && !/INPUT|TEXTAREA|SELECT/.test(document.activeElement.tagName)) { e.preventDefault(); q.focus(); } });
})();
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
