<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$today = time();
$service = function ($doj) use ($today) {
  $t = strtotime((string)$doj);
  if (!$doj || !$t || $t > $today) return '';
  $m = (int)floor(($today - $t) / (86400 * 30.4375));
  return $m >= 12 ? floor($m / 12) . ' yr' . ($m % 12 ? ' ' . ($m % 12) . ' mo' : '') : $m . ' mo';
};
$base = base_url() . 'manager/hr/employees/';
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_8">
<style>
  .em-name { display: flex; align-items: center; gap: 10px; min-width: 0; }
  .em-av { width: 34px; height: 34px; border-radius: 50%; flex: 0 0 auto; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; background: var(--crm-ocean-soft, rgba(30, 58, 138, .1)); color: var(--crm-ocean, #1e3a8a); }
  .em-name a { font-weight: 600; color: var(--crm-ink, #0f172a); }
  .em-sub { display: block; font-size: 12px; color: var(--crm-muted, #64748b); }
  .sx-kpis button.sx-kpi { cursor: pointer; }
  @media (max-width: 767px) { .em-av { display: none; } }
</style>
<div class="content-wrapper sx sx-hr">
  <?php echo crm_tabbar('hr_people', 'hr/employees'); ?>
  <section class="sx-head">
    <h1>Employees</h1>
    <?php if (!empty($can_add)) { ?><div class="sx-btns"><a href="<?php echo base_url(); ?>manager/hr/staff/add" class="btn btn-primary"><i class="fa fa-user-plus"></i> Add employee</a></div><?php } ?>
  </section>
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="sx-kpis" id="emKpis">
      <button type="button" class="sx-kpi is-on" data-f="all"><span class="ic"><i class="fa fa-users"></i></span><div><b><?php echo number_format($kpi['working']); ?></b><span>Working</span></div></button>
      <button type="button" class="sx-kpi s" data-f="probation"><span class="ic"><i class="fa fa-hourglass-half"></i></span><div><b><?php echo number_format($kpi['probation']); ?></b><span>On probation</span></div></button>
      <button type="button" class="sx-kpi g" data-f="joined"><span class="ic"><i class="fa fa-user-plus"></i></span><div><b><?php echo number_format($kpi['joined']); ?></b><span>Joined this month</span></div></button>
      <button type="button" class="sx-kpi r" data-f="docs"><span class="ic"><i class="fa fa-credit-card"></i></span><div><b><?php echo number_format($kpi['docs']); ?></b><span>Documents expired or expiring in <?php echo (int)$alert_days; ?> days</span></div></button>
      <?php if ($see_pay) { ?><button type="button" class="sx-kpi a" data-f="nosalary"><span class="ic"><i class="fa fa-money"></i></span><div><b><?php echo number_format($kpi['nosalary']); ?></b><span>No salary structure yet</span></div></button><?php } ?>
    </div>

    <div class="sx-card">
      <div class="sx-bar">
        <input type="search" class="form-control sx-q" id="emQ" placeholder="Name, employee ID or designation" autocomplete="off">
        <select class="form-control" id="emDept" aria-label="Department">
          <option value="">All departments</option>
          <?php foreach ($depts as $d) { ?><option value="<?php echo $h(strtolower($d)); ?>"><?php echo $h($d); ?></option><?php } ?>
        </select>
        <select class="form-control" id="emStatus" aria-label="Status">
          <option value="working" selected>Working</option>
          <option value="disabled">Login disabled</option>
          <option value="left">Left the company</option>
          <option value="">Everyone</option>
        </select>
        <span class="sx-count" id="emCount"></span>
      </div>
      <div class="sx-table-wrap">
        <table class="sx-table sx-cards" id="emTable">
          <thead><tr><th>Employee</th><th>Designation</th><th>Joined</th><th>Contract</th><th>Documents</th><?php if ($see_pay) { ?><th>Salary / month</th><?php } ?><th></th></tr></thead>
          <tbody>
          <?php foreach ($rows as $r) {
            $initials = strtoupper(implode('', array_map(function ($w) { return mb_substr($w, 0, 1); }, array_slice(preg_split('/\s+/', trim($r['name'])), 0, 2))));
            $tags = array();
            if ($r['probation']) $tags[] = 'probation';
            if (substr((string)$r['c_doj'], 0, 7) === date('Y-m')) $tags[] = 'joined';
            if (in_array($r['doc_state'], array('expired', 'soon'), true)) $tags[] = 'docs';
            if ($see_pay && $r['salary'] === null) $tags[] = 'nosalary';
            $search = strtolower($r['name'] . ' ' . $r['emp_id'] . ' ' . $r['designation'] . ' ' . $r['role_title']);
          ?>
            <tr data-s="<?php echo $r['status']; ?>" data-d="<?php echo $h(strtolower(trim((string)$r['department']))); ?>" data-t="<?php echo implode(' ', $tags); ?>" data-q="<?php echo $h($search); ?>">
              <td data-l="Employee">
                <div class="em-name"><span class="em-av" aria-hidden="true"><?php echo $h($initials); ?></span>
                  <div><a href="<?php echo $base . (int)$r['id']; ?>"><?php echo $h($r['name']); ?></a>
                  <span class="em-sub"><?php echo $h(trim(($r['emp_id'] !== '' ? '#' . $r['emp_id'] : '') . ' · ' . $r['role_title'], ' ·')); ?></span></div>
                </div>
              </td>
              <td data-l="Designation"><?php echo $r['designation'] !== null && $r['designation'] !== '' ? $h($r['designation']) : '<span class="text-muted">-</span>'; ?><span class="em-sub"><?php echo $h($r['department']); ?></span></td>
              <td data-l="Joined" class="nw" data-order="<?php echo $h($r['c_doj']); ?>"><?php if ($r['c_doj'] && strtotime($r['c_doj'])) { echo $h(date('d-m-Y', strtotime($r['c_doj']))); ?><span class="em-sub"><?php echo $h($service($r['c_doj'])); ?></span><?php } else echo '<span class="text-muted">-</span>'; ?></td>
              <td data-l="Contract">
                <?php if ($r['status'] === 'left') { ?><span class="chip na">Left <?php echo $r['left_on'] ? $h(date('d-m-Y', strtotime($r['left_on']))) : ''; ?></span>
                <?php } elseif ($r['status'] === 'disabled') { ?><span class="chip na">Login disabled</span>
                <?php } elseif ($r['probation']) { ?><span class="chip w">Probation to <?php echo $h(date('d-m-Y', strtotime($r['probation_end_date']))); ?></span>
                <?php } else { ?><span><?php echo $h($r['contract_type'] ? $r['contract_type'] : 'Permanent'); ?></span><?php } ?>
                <?php if (!empty($r['contract_end'])) { ?><span class="em-sub">Ends <?php echo $h(date('d-m-Y', strtotime($r['contract_end']))); ?></span><?php } ?>
              </td>
              <td data-l="Documents">
                <?php if ($r['doc_state'] === 'expired') { ?><span class="chip x"><?php echo $h($r['doc_label']); ?> expired</span><span class="em-sub"><?php echo abs($r['doc_days']); ?> days ago</span>
                <?php } elseif ($r['doc_state'] === 'soon') { ?><span class="chip w"><?php echo $h($r['doc_label']); ?> in <?php echo (int)$r['doc_days']; ?> days</span>
                <?php } elseif ($r['doc_state'] === 'valid') { ?><span class="chip ok">Valid</span><span class="em-sub">Next: <?php echo $h($r['doc_label']); ?> in <?php echo (int)$r['doc_days']; ?> days</span>
                <?php } else { ?><span class="chip na">No dates</span><?php } ?>
              </td>
              <?php if ($see_pay) { ?>
              <td data-l="Salary / month" class="nw"><?php
                if ($r['salary'] !== null) echo '<b>' . $h($currency) . ' ' . number_format($r['salary'], 2) . '</b>';
                elseif ($r['salary_old'] !== null) echo $h($currency) . ' ' . number_format($r['salary_old'], 2) . '<span class="em-sub">Total only, no breakdown</span>';
                else echo '<span class="text-muted">Not set</span>';
              ?></td>
              <?php } ?>
              <td data-l="" class="nw"><a href="<?php echo $base . (int)$r['id']; ?>" class="btn btn-default btn-sm"><i class="fa fa-folder-open-o"></i> HR file</a></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
        <div class="sx-empty" id="emNone" style="display:none;"><i class="fa fa-search"></i> No employees match.</div>
      </div>
    </div>
  </section>
</div>
<script>
(function () {
  var rows = [].slice.call(document.querySelectorAll('#emTable tbody tr')), tag = 'all';
  var q = document.getElementById('emQ'), dept = document.getElementById('emDept'), st = document.getElementById('emStatus');
  function run() {
    var s = q.value.trim().toLowerCase(), d = dept.value, ss = st.value, n = 0;
    rows.forEach(function (r) {
      var on = (!s || r.getAttribute('data-q').indexOf(s) !== -1) && (!d || r.getAttribute('data-d') === d) && (!ss || r.getAttribute('data-s') === ss)
        && (tag === 'all' || (' ' + r.getAttribute('data-t') + ' ').indexOf(' ' + tag + ' ') !== -1);
      r.style.display = on ? '' : 'none';
      if (on) n++;
    });
    document.getElementById('emCount').textContent = n + (n === 1 ? ' employee' : ' employees');
    document.getElementById('emNone').style.display = n ? 'none' : '';
  }
  q.addEventListener('input', run); dept.addEventListener('change', run); st.addEventListener('change', run);
  document.querySelectorAll('#emKpis .sx-kpi').forEach(function (b) {
    b.addEventListener('click', function () {
      tag = b.classList.contains('is-on') && tag !== 'all' ? 'all' : b.getAttribute('data-f');
      document.querySelectorAll('#emKpis .sx-kpi').forEach(function (x) { x.classList.toggle('is-on', x.getAttribute('data-f') === tag); });
      if (tag !== 'all') st.value = 'working';
      run();
    });
  });
  run();
})();
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
