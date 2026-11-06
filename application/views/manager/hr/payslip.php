<?php
$h = function ($x) { return htmlspecialchars((string)$x, ENT_QUOTES, 'UTF-8'); };
$money = function ($v) { return number_format((float)$v, 2); };
$cur = $h($i['currency'] ?: $set['currency']);
$mname = date('F Y', strtotime($s['month'] . '-01'));
$company = $set['company_name'] !== '' ? $set['company_name'] : 'Company name (set it in HR Settings)';
$iban = (string)$i['iban'];
$iban_m = strlen($iban) > 8 ? substr($iban, 0, 4) . str_repeat('*', max(0, strlen($iban) - 8)) . substr($iban, -4) : $iban;
?><!doctype html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Payslip <?php echo $h($mname . ' - ' . $i['name']); ?></title>
<style>
  * { box-sizing: border-box; }
  body { margin: 0; background: #eef2f7; font: 14px/1.45 -apple-system, "Segoe UI", Roboto, Arial, sans-serif; color: #0f172a; }
  .bar { max-width: 820px; margin: 16px auto 0; padding: 0 16px; display: flex; gap: 8px; justify-content: space-between; flex-wrap: wrap; }
  .bar a, .bar button { font: inherit; padding: 8px 14px; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; color: #0f172a; text-decoration: none; cursor: pointer; }
  .bar button { background: #1e3a8a; border-color: #1e3a8a; color: #fff; }
  .sheet { max-width: 820px; margin: 12px auto 24px; background: #fff; border-radius: 12px; padding: 28px; box-shadow: 0 4px 20px rgba(15, 23, 42, .06); }
  .top { display: flex; justify-content: space-between; gap: 16px; flex-wrap: wrap; border-bottom: 2px solid #1e3a8a; padding-bottom: 14px; }
  .top h1 { margin: 0; font-size: 20px; }
  .top .t { text-align: right; }
  .top .t b { display: block; font-size: 18px; color: #1e3a8a; }
  .muted { color: #64748b; font-size: 12.5px; }
  .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 10px 18px; margin: 16px 0; }
  .grid div span { display: block; font-size: 11.5px; color: #64748b; text-transform: uppercase; letter-spacing: .03em; }
  .cols { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
  table { width: 100%; border-collapse: collapse; }
  th { text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: .03em; color: #64748b; border-bottom: 1px solid #e2e8f0; padding: 6px 0; }
  td { padding: 6px 0; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
  td.n, th.n { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; padding-left: 10px; }
  tr.tot td { border-top: 1px solid #cbd5e1; border-bottom: 0; font-weight: 700; }
  .net { margin-top: 18px; display: flex; justify-content: space-between; align-items: center; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 12px 16px; font-size: 16px; }
  .net b { font-size: 22px; color: #1e3a8a; }
  .note { margin-top: 14px; font-size: 12.5px; color: #64748b; }
  @media (max-width: 600px) { .sheet { padding: 18px; border-radius: 0; } .cols { grid-template-columns: 1fr; } .top .t { text-align: left; } }
  @media print { body { background: #fff; } .bar { display: none; } .sheet { box-shadow: none; margin: 0; max-width: none; padding: 0; } @page { margin: 14mm; } }
</style>
</head><body>
<div class="bar"><a href="<?php echo base_url() . $h($back); ?>">&larr; Back</a><button type="button" onclick="window.print()">Print / Save as PDF</button></div>
<div class="sheet">
  <div class="top">
    <div><h1><?php echo $h($company); ?></h1><?php if ($set['trade_licence_no'] !== '') { ?><div class="muted">Trade licence <?php echo $h($set['trade_licence_no']); ?></div><?php } ?></div>
    <div class="t"><b>Payslip</b><?php echo $h($mname); ?><div class="muted"><?php echo $s['run_status'] === 'Paid' ? 'Paid' : ($s['run_status'] === 'Approved' ? 'Approved' : 'Draft, not final'); ?></div></div>
  </div>
  <div class="grid">
    <div><span>Employee</span><?php echo $h($i['name']); ?></div>
    <div><span>Employee ID</span><?php echo $h($i['emp_id'] ?: '-'); ?></div>
    <div><span>Designation</span><?php echo $h($i['designation'] ?: '-'); ?></div>
    <div><span>Department</span><?php echo $h($i['department'] ?: '-'); ?></div>
    <div><span>Joined</span><?php echo $i['doj'] && strtotime($i['doj']) ? $h(date('d M Y', strtotime($i['doj']))) : '-'; ?></div>
    <div><span>Days paid</span><?php echo 0 + $s['days_paid']; ?> of <?php echo (int)$s['days_in_month']; ?><?php echo (float)$s['leave_days'] > 0 ? ' (' . (0 + $s['leave_days']) . ' on leave)' : ''; ?></div>
    <div><span>Paid by</span><?php echo $h($i['payment_method'] ?: 'Bank transfer'); ?></div>
    <div><span>Account</span><?php echo $h($iban_m ?: '-'); ?><?php echo $i['bank'] ? '<div class="muted">' . $h($i['bank']) . '</div>' : ''; ?></div>
  </div>
  <div class="cols">
    <table><thead><tr><th>Earnings</th><th class="n"><?php echo $cur; ?></th></tr></thead><tbody>
      <?php foreach ((array)$e as $l) { ?><tr><td><?php echo $h($l[0]); ?></td><td class="n"><?php echo $money($l[1]); ?></td></tr><?php } ?>
      <tr class="tot"><td>Total earnings</td><td class="n"><?php echo $money($s['gross']); ?></td></tr>
    </tbody></table>
    <table><thead><tr><th>Deductions</th><th class="n"><?php echo $cur; ?></th></tr></thead><tbody>
      <?php if (!$d) { ?><tr><td class="muted">None</td><td class="n">0.00</td></tr><?php } ?>
      <?php foreach ((array)$d as $l) { ?><tr><td><?php echo $h($l[0]); ?></td><td class="n"><?php echo $money($l[1]); ?></td></tr><?php } ?>
      <tr class="tot"><td>Total deductions</td><td class="n"><?php echo $money($s['total_deductions']); ?></td></tr>
    </tbody></table>
  </div>
  <div class="net"><span>Net pay</span><b><?php echo $cur . ' ' . $money($s['net']); ?></b></div>
  <?php if (!empty($i['notes'])) { ?><div class="note"><?php foreach ((array)$i['notes'] as $n) echo $h($n) . '<br>'; ?></div><?php } ?>
  <div class="note">This payslip was made by the HR system and needs no signature. Period <?php echo $h(date('d M Y', strtotime($i['start'])) . ' to ' . date('d M Y', strtotime($i['end']))); ?>.</div>
</div>
</body></html>
