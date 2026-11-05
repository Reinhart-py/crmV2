<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$e = function ($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };
$aed = function ($v) { return 'AED ' . number_format((float)$v, 0); };
$pct = function ($v) { return $v === null ? '<span class="ins-muted">No target</span>' : ((int)$v) . '%'; };
$base = base_url() . 'manager/reports/insights/';
$uses_month = in_array($tab, array('sales-target', 'centers'), true);
$uses_range = in_array($tab, array('calls', 'funnel', 'services'), true);
$q = array('person' => $person ?: '');
if ($uses_month) $q['month'] = $month;
if ($uses_range) { $q['d1'] = $d1; $q['d2'] = $d2; }
$keep = http_build_query(array_filter(array('person' => $person ?: '', 'month' => $month, 'd1' => $d1, 'd2' => $d2)));
?>
<style>
.ins-tabs{display:flex;flex-wrap:wrap;gap:6px;margin:0 0 12px;padding:0;list-style:none}
.ins-tabs a{display:inline-block;padding:7px 14px;border:1px solid #cbd5e1;border-radius:4px;background:#fff;color:#1e293b;font-weight:600;font-size:13px}
.ins-tabs a.on{background:#1e293b;color:#fff;border-color:#1e293b}
.ins-box{border:1px solid #e2e8f0;border-radius:4px;background:#fff;padding:14px;margin-bottom:14px}
.ins-filter{display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end}
.ins-filter label{display:block;font-size:12px;color:#475569;margin:0 0 3px;font-weight:600}
.ins-filter .form-control{height:34px}
.ins-scope{font-size:12px;color:#475569;margin-left:auto}
.ins-table th{background:#f1f5f9;color:#0f172a;font-weight:700;white-space:nowrap}
.ins-table td.n,.ins-table th.n{text-align:right;white-space:nowrap}
.ins-table tfoot td{font-weight:700;background:#f8fafc}
.ins-muted{color:#94a3b8}
.ins-bar{height:6px;background:#e2e8f0;border-radius:3px;min-width:70px;margin-top:3px}
.ins-bar span{display:block;height:6px;border-radius:3px;background:#16a34a}
.ins-note{font-size:12px;color:#475569;margin-top:8px}
@media (max-width:700px){.ins-scope{margin-left:0;width:100%}}
</style>

<div class="content-wrapper">
  <section class="content-header" style="padding:15px;">
    <h1 style="margin:0;font-size:22px;font-weight:700;color:#000;"><?php echo $e($info['page_heading']); ?></h1>
  </section>

  <section class="content" style="padding-top:0;">
    <ul class="ins-tabs">
      <?php foreach ($tabs as $k => $label) { ?>
        <li><a class="<?php echo $k === $tab ? 'on' : ''; ?>" href="<?php echo $base . $k . ($keep ? '?' . $e($keep) : ''); ?>"><?php echo $e($label); ?></a></li>
      <?php } ?>
    </ul>

    <div class="ins-box">
      <form class="ins-filter" method="get" action="<?php echo $base . $e($tab); ?>">
        <?php if ($uses_month) { ?>
          <div><label for="insMonth">Month</label><input id="insMonth" type="month" class="form-control" name="month" value="<?php echo $e($month); ?>"></div>
        <?php } ?>
        <?php if ($uses_range) { ?>
          <div><label for="insD1">From</label><input id="insD1" type="date" class="form-control" name="d1" value="<?php echo $e($d1); ?>"></div>
          <div><label for="insD2">To</label><input id="insD2" type="date" class="form-control" name="d2" value="<?php echo $e($d2); ?>"></div>
        <?php } ?>
        <?php if (count($people) > 1 && $tab !== 'centers') { ?>
          <div><label for="insPerson">Person</label>
            <select id="insPerson" name="person" class="form-control">
              <option value="">All<?php echo $is_admin ? '' : ' (you and your team)'; ?></option>
              <?php foreach ($people as $p) { ?>
                <option value="<?php echo (int)$p['id']; ?>" <?php echo $person === (int)$p['id'] ? 'selected' : ''; ?>><?php echo $e($p['name']); ?></option>
              <?php } ?>
            </select>
          </div>
        <?php } ?>
        <?php if ($tab !== 'overdue' || count($people) > 1) { ?>
          <div><button type="submit" class="btn btn-primary" style="height:34px;">Show</button></div>
        <?php } ?>
        <div class="ins-scope">Showing: <strong><?php echo $e($scope_label); ?></strong></div>
      </form>
    </div>

    <div class="ins-box">
      <?php if (empty($rows)) { ?>
        <div class="alert alert-info" style="margin:0;">No data for this selection.</div>
      <?php } else { ?>
      <div class="table-responsive">
        <table class="table table-bordered table-striped ins-table" id="insTable">

        <?php if ($tab === 'sales-target') { $t = array('orders' => 0, 'sales' => 0, 'sales_aed' => 0, 'target' => 0); ?>
          <thead><tr><th>Name</th><th>Center</th><th class="n">Orders</th><th class="n">Sales</th><th class="n">Sales (AED)</th><th class="n">Target (AED)</th><th class="n">Achieved</th><th class="n">Still needed</th></tr></thead>
          <tbody>
          <?php foreach ($rows as $r) { foreach ($t as $k => $x) $t[$k] += $r[$k]; ?>
            <tr><td><?php echo $e($r['name']); ?></td><td><?php echo $e($r['office']); ?></td>
              <td class="n"><?php echo (int)$r['orders']; ?></td><td class="n"><?php echo (int)$r['sales']; ?></td>
              <td class="n" data-order="<?php echo (float)$r['sales_aed']; ?>"><?php echo $aed($r['sales_aed']); ?></td>
              <td class="n" data-order="<?php echo (float)$r['target']; ?>"><?php echo $r['target'] > 0 ? $aed($r['target']) : '<span class="ins-muted">-</span>'; ?></td>
              <td class="n" data-order="<?php echo $r['pct'] === null ? -1 : (int)$r['pct']; ?>"><?php echo $pct($r['pct']); ?><?php if ($r['pct'] !== null) { ?><div class="ins-bar"><span style="width:<?php echo min(100, (int)$r['pct']); ?>%"></span></div><?php } ?></td>
              <td class="n" data-order="<?php echo (float)$r['gap']; ?>"><?php echo $r['gap'] === null ? '<span class="ins-muted">-</span>' : $aed($r['gap']); ?></td></tr>
          <?php } $tp = $t['target'] > 0 ? (int)round($t['sales_aed'] * 100 / $t['target']) : null; ?>
          </tbody>
          <tfoot><tr><td>Total</td><td></td><td class="n"><?php echo $t['orders']; ?></td><td class="n"><?php echo $t['sales']; ?></td><td class="n"><?php echo $aed($t['sales_aed']); ?></td><td class="n"><?php echo $t['target'] > 0 ? $aed($t['target']) : '-'; ?></td><td class="n"><?php echo $pct($tp); ?></td><td class="n"><?php echo $t['target'] > 0 ? $aed(max(0, $t['target'] - $t['sales_aed'])) : '-'; ?></td></tr></tfoot>

        <?php } elseif ($tab === 'calls') { $t = array('calls' => 0, 'completed' => 0, 'talk_sec' => 0); ?>
          <thead><tr><th>Name</th><th>Center</th><th class="n">Calls</th><th class="n">Completed</th><th class="n">Talk time</th><th class="n">Days with calls</th><th class="n">Calls per day</th></tr></thead>
          <tbody>
          <?php foreach ($rows as $r) { foreach ($t as $k => $x) $t[$k] += $r[$k]; ?>
            <tr><td><?php echo $e($r['name']); ?></td><td><?php echo $e($r['office']); ?></td>
              <td class="n"><?php echo (int)$r['calls']; ?></td><td class="n"><?php echo (int)$r['completed']; ?></td>
              <td class="n" data-order="<?php echo (int)$r['talk_sec']; ?>"><?php echo gmdate('H:i:s', (int)$r['talk_sec']); ?></td>
              <td class="n"><?php echo (int)$r['days']; ?></td><td class="n"><?php echo $e($r['per_day']); ?></td></tr>
          <?php } ?>
          </tbody>
          <tfoot><tr><td>Total</td><td></td><td class="n"><?php echo $t['calls']; ?></td><td class="n"><?php echo $t['completed']; ?></td><td class="n"><?php echo floor($t['talk_sec'] / 3600) . gmdate(':i:s', $t['talk_sec'] % 3600); ?></td><td></td><td></td></tr></tfoot>

        <?php } elseif ($tab === 'funnel') { $t = array('total' => 0, 'new' => 0, 'working' => 0, 'won' => 0, 'lost' => 0, 'invalid' => 0); ?>
          <thead><tr><th>Owner</th><th>Center</th><th class="n">Leads</th><th class="n">New</th><th class="n">Working</th><th class="n">Won</th><th class="n">Lost</th><th class="n">Invalid</th><th class="n">Conversion</th></tr></thead>
          <tbody>
          <?php foreach ($rows as $r) { foreach ($t as $k => $x) $t[$k] += $r[$k]; ?>
            <tr><td><?php echo $e($r['name']); ?></td><td><?php echo $e($r['office']); ?></td>
              <?php foreach (array('total', 'new', 'working', 'won', 'lost', 'invalid') as $k) { ?><td class="n"><?php echo (int)$r[$k]; ?></td><?php } ?>
              <td class="n" data-order="<?php echo $r['conv'] === null ? -1 : $r['conv']; ?>"><?php echo $r['conv'] === null ? '<span class="ins-muted">-</span>' : $e($r['conv']) . '%'; ?></td></tr>
          <?php } $valid = $t['total'] - $t['invalid']; ?>
          </tbody>
          <tfoot><tr><td>Total</td><td></td><?php foreach ($t as $v) { ?><td class="n"><?php echo $v; ?></td><?php } ?><td class="n"><?php echo $valid > 0 ? round($t['won'] * 100 / $valid, 1) . '%' : '-'; ?></td></tr></tfoot>

        <?php } elseif ($tab === 'services') { $t = array('orders' => 0, 'sales' => 0, 'sales_aed' => 0, 'order_aed' => 0); ?>
          <thead><tr><th>Service</th><th class="n">Orders</th><th class="n">Order value (AED)</th><th class="n">Sales</th><th class="n">Sales (AED)</th><th class="n">Sellers</th></tr></thead>
          <tbody>
          <?php foreach ($rows as $r) { foreach ($t as $k => $x) $t[$k] += $r[$k]; ?>
            <tr><td><?php echo $e($r['service']); ?></td><td class="n"><?php echo (int)$r['orders']; ?></td>
              <td class="n" data-order="<?php echo (float)$r['order_aed']; ?>"><?php echo $aed($r['order_aed']); ?></td>
              <td class="n"><?php echo (int)$r['sales']; ?></td>
              <td class="n" data-order="<?php echo (float)$r['sales_aed']; ?>"><?php echo $aed($r['sales_aed']); ?></td>
              <td class="n"><?php echo (int)$r['sellers']; ?></td></tr>
          <?php } ?>
          </tbody>
          <tfoot><tr><td>Total</td><td class="n"><?php echo $t['orders']; ?></td><td class="n"><?php echo $aed($t['order_aed']); ?></td><td class="n"><?php echo $t['sales']; ?></td><td class="n"><?php echo $aed($t['sales_aed']); ?></td><td></td></tr></tfoot>

        <?php } elseif ($tab === 'centers') { $t = array('staff' => 0, 'calls' => 0, 'orders' => 0, 'sales' => 0, 'sales_aed' => 0, 'target' => 0); ?>
          <thead><tr><th>Center</th><th class="n">Staff</th><th class="n">Calls</th><th class="n">Orders</th><th class="n">Sales</th><th class="n">Sales (AED)</th><th class="n">Target (AED)</th><th class="n">Achieved</th></tr></thead>
          <tbody>
          <?php foreach ($rows as $r) { foreach ($t as $k => $x) $t[$k] += $r[$k]; ?>
            <tr><td><?php echo $e($r['name']); ?></td>
              <?php foreach (array('staff', 'calls', 'orders', 'sales') as $k) { ?><td class="n"><?php echo (int)$r[$k]; ?></td><?php } ?>
              <td class="n" data-order="<?php echo (float)$r['sales_aed']; ?>"><?php echo $aed($r['sales_aed']); ?></td>
              <td class="n" data-order="<?php echo (float)$r['target']; ?>"><?php echo $r['target'] > 0 ? $aed($r['target']) : '-'; ?></td>
              <td class="n" data-order="<?php echo $r['pct'] === null ? -1 : (int)$r['pct']; ?>"><?php echo $pct($r['pct']); ?></td></tr>
          <?php } $tp = $t['target'] > 0 ? (int)round($t['sales_aed'] * 100 / $t['target']) : null; ?>
          </tbody>
          <tfoot><tr><td>Total</td><?php foreach (array('staff', 'calls', 'orders', 'sales') as $k) { ?><td class="n"><?php echo $t[$k]; ?></td><?php } ?><td class="n"><?php echo $aed($t['sales_aed']); ?></td><td class="n"><?php echo $t['target'] > 0 ? $aed($t['target']) : '-'; ?></td><td class="n"><?php echo $pct($tp); ?></td></tr></tfoot>

        <?php } elseif ($tab === 'overdue') { ?>
          <thead><tr><th>Customer</th><th>Contact person</th><th>Status</th><th>Follow-up was due</th><th class="n">Days overdue</th><th>Owner</th></tr></thead>
          <tbody>
          <?php foreach ($rows as $r) { ?>
            <tr><td><a href="<?php echo base_url() . 'manager/leads/view/' . $e(base64_encode($r['id'])); ?>"><?php echo $e($r['customer']); ?></a></td>
              <td><?php echo $e($r['poc']); ?></td><td><?php echo $e($r['status_name']); ?></td><td><?php echo $e($r['followup']); ?></td>
              <td class="n"><?php echo $r['days_over'] === null ? '-' : (int)$r['days_over']; ?></td><td><?php echo $e($r['owner']); ?></td></tr>
          <?php } ?>
          </tbody>
        <?php } ?>

        </table>
      </div>
      <?php } ?>

      <div class="ins-note">
        <?php if ($tab === 'sales-target' || $tab === 'services' || $tab === 'centers') { ?>
          Orders and sales always count for the person who made them, even if the lead was later transferred.
        <?php } elseif ($tab === 'funnel') { ?>
          Leads created in the dates chosen, counted for their current owner.
          New: <?php echo $e(implode(', ', $groups['names']['new'])); ?>.
          Working: <?php echo $e(implode(', ', $groups['names']['working'])); ?>.
          Won: <?php echo $e(implode(', ', $groups['names']['won'])); ?>.
          Lost: <?php echo $e(implode(', ', $groups['names']['lost'])); ?>.
          Invalid: <?php echo $e(implode(', ', $groups['names']['invalid'])); ?>.
          Conversion = Won out of all leads that are not Invalid.
        <?php } elseif ($tab === 'overdue') { ?>
          Open leads (not Won, Lost or Invalid) whose follow-up date has passed, for their current owner.
        <?php } elseif ($tab === 'calls') { ?>
          Calls made from the CRM dialer in the dates chosen.
        <?php } ?>
      </div>
    </div>
  </section>
</div>

<script type="text/javascript">
$(function () {
  if ($.fn.DataTable && $('#insTable').length) {
    $('#insTable').DataTable({ pageLength: 50, order: [], responsive: false });
  }
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
