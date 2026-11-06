<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$money = function ($v) { return number_format((float)$v, 2); };
$cur = $h(!empty($set['currency']) ? $set['currency'] : 'AED');
$base = base_url();
$t_owed = 0; $t_paid = 0; $n_due = 0;
foreach ($verticals as $v) { $t_owed += (float)$v['owed']; $t_paid += (float)$v['paid']; if ((float)$v['owed'] - (float)$v['paid'] > 0.004) $n_due++; }
$vname = '';
foreach ($verticals as $v) if ((int)$v['id'] === (int)$vf) $vname = $v['company_name'];
?>
<link rel="stylesheet" href="<?php echo $base; ?>assets/dist/css/crm-sys.css?v=20261106_8">
<link rel="stylesheet" href="<?php echo $base; ?>assets/dist/css/crm-vertical.css?v=20261106_3">
<style>
  .vp-sub { display: block; font-size: 12px; color: var(--crm-muted, #64748b); }
  .vp-bar { height: 5px; border-radius: 3px; background: var(--crm-line, #e2e8f0); overflow: hidden; margin-top: 4px; min-width: 80px; }
  .vp-bar i { display: block; height: 100%; background: #16a34a; }
  .vp-due { color: #b45309; font-weight: 700; }
  .vp-on td { background: var(--crm-ocean-soft, rgba(30,58,138,.06)); }
  .sx .vp-card-b { padding: 12px 16px; }
</style>
<div class="content-wrapper sx sx-hr">
  <?php echo crm_tabbar('hr_pay', 'hr/vertical_payouts'); ?>
  <section class="sx-head">
    <h1>Vertical Payouts</h1>
  </section>
  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>
    <div class="sx-kpis">
      <div class="sx-kpi"><span class="ic"><i class="fa fa-file-text-o"></i></span><div><b><?php echo $money($t_owed); ?></b><span>Agreed, <?php echo $cur; ?></span></div></div>
      <div class="sx-kpi"><span class="ic"><i class="fa fa-check"></i></span><div><b><?php echo $money($t_paid); ?></b><span>Paid so far, <?php echo $cur; ?></span></div></div>
      <div class="sx-kpi a"><span class="ic"><i class="fa fa-hourglass-half"></i></span><div><b><?php echo $money(max(0, $t_owed - $t_paid)); ?></b><span>Still to pay · <?php echo $n_due; ?> vertical<?php echo $n_due === 1 ? '' : 's'; ?></span></div></div>
    </div>

    <?php if ($can_edit) { ?>
    <div class="sx-card" id="add">
      <div class="sx-card-h"><h3><i class="fa fa-plus-circle"></i> Record a payout</h3></div>
      <div class="vp-card-b">
        <form class="vp-form" method="post" action="<?php echo $base; ?>manager/hr/vertical_payout_save" autocomplete="off">
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
          <div class="wide"><label for="vp1">Vertical <span>*</span></label>
            <select id="vp1" name="vertical_id" class="form-control" required>
              <option value="">Choose</option>
              <?php foreach ($verticals as $v) { ?><option value="<?php echo (int)$v['id']; ?>"<?php echo (int)$v['id'] === (int)$vf ? ' selected' : ''; ?>><?php echo $h($v['company_name']); ?> · V-<?php echo $h($v['v_id']); ?></option><?php } ?>
            </select></div>
          <div class="wide"><label for="vp2">For client</label>
            <select id="vp2" name="lead_id" class="form-control"><option value="">All / not for one client</option></select></div>
          <div><label for="vp3">Date paid <span>*</span></label><input id="vp3" type="date" name="pay_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" required></div>
          <div><label for="vp4">Amount, <?php echo $cur; ?> <span>*</span></label><input id="vp4" type="number" name="amount" class="form-control" min="0.01" step="0.01" required inputmode="decimal"></div>
          <div><label for="vp5">Method</label><select id="vp5" name="method" class="form-control"><option>Bank transfer</option><option>Cash</option><option>Cheque</option><option>Other</option></select></div>
          <div><label for="vp6">Reference</label><input id="vp6" name="reference" class="form-control" maxlength="100" placeholder="Transfer or cheque no."></div>
          <div class="wide"><label for="vp7">Note</label><input id="vp7" name="note" class="form-control" maxlength="255"></div>
          <div class="act"><button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Record payout</button></div>
        </form>
      </div>
    </div>
    <?php } ?>

    <div class="sx-card">
      <div class="sx-card-h"><h3><i class="fa fa-users"></i> Verticals</h3></div>
      <?php if (!$verticals) { ?><div class="sx-card-b"><div class="sx-empty"><i class="fa fa-users"></i> No verticals yet.</div></div><?php } else { ?>
      <div class="sx-table-wrap">
        <table class="sx-table sx-cards">
          <thead><tr><th>Vertical</th><th class="text-right">Clients</th><th class="text-right">Agreed</th><th>Paid</th><th class="text-right">Still to pay</th><th>Last paid</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($verticals as $v) { $left = (float)$v['owed'] - (float)$v['paid']; $pc = (float)$v['owed'] > 0 ? min(100, round(100 * (float)$v['paid'] / (float)$v['owed'])) : 0; ?>
            <tr class="<?php echo (int)$v['id'] === (int)$vf ? 'vp-on' : ''; ?>">
              <td data-l="Vertical"><a href="<?php echo $base; ?>manager/verticals/view/<?php echo base64_encode($v['id']); ?>"><b><?php echo $h($v['company_name']); ?></b></a><span class="vp-sub">V-<?php echo $h($v['v_id']); ?><?php echo $v['poc'] !== '' ? ' · ' . $h($v['poc']) : ''; ?></span></td>
              <td data-l="Clients" class="text-right mono"><?php echo (int)$v['clients']; ?></td>
              <td data-l="Agreed" class="text-right mono"><?php echo $money($v['owed']); ?></td>
              <td data-l="Paid"><span class="mono"><?php echo $money($v['paid']); ?></span><?php if ((float)$v['owed'] > 0) { ?><div class="vp-bar" aria-hidden="true"><i style="width:<?php echo $pc; ?>%"></i></div><?php } ?></td>
              <td data-l="Still to pay" class="text-right mono"><?php echo $left > 0.004 ? '<span class="vp-due">' . $money($left) . '</span>' : ($left < -0.004 ? $money(abs($left)) . ' <span class="vp-sub">paid over</span>' : '—'); ?></td>
              <td data-l="Last paid"><?php echo !empty($v['last_paid']) ? $h(date('d M Y', strtotime($v['last_paid']))) : '—'; ?></td>
              <td data-l="" class="nw"><a class="btn btn-default btn-sm" href="<?php echo $base; ?>manager/hr/vertical_payouts?vertical=<?php echo (int)$v['id']; ?>#list">Payouts</a></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
      <?php } ?>
    </div>

    <div class="sx-card" id="list">
      <div class="sx-card-h"><h3><i class="fa fa-money"></i> <?php echo $vf ? 'Payouts to ' . $h($vname) : 'Recent payouts'; ?></h3><?php if ($vf) { ?><a class="btn btn-default btn-sm" href="<?php echo $base; ?>manager/hr/vertical_payouts#list">Show all</a><?php } ?></div>
      <?php if (!$payouts) { ?><div class="sx-card-b"><div class="sx-empty"><i class="fa fa-money"></i> No payouts recorded<?php echo $vf ? ' for this vertical' : ''; ?>.</div></div><?php } else { ?>
      <div class="sx-table-wrap">
        <table class="sx-table sx-cards">
          <thead><tr><th>Date</th><?php if (!$vf) { ?><th>Vertical</th><?php } ?><th class="text-right">Amount</th><th>For client</th><th>Method</th><th>Reference</th><th>Recorded by</th><?php if ($can_edit) { ?><th></th><?php } ?></tr></thead>
          <tbody>
          <?php foreach ($payouts as $p) { ?>
            <tr>
              <td data-l="Date" class="nw"><?php echo $h(date('d M Y', strtotime($p['pay_date']))); ?></td>
              <?php if (!$vf) { ?><td data-l="Vertical"><?php echo $h($p['company_name']); ?></td><?php } ?>
              <td data-l="Amount" class="text-right mono"><b><?php echo $money($p['amount']); ?></b></td>
              <td data-l="For client"><?php echo $p['customer'] !== null && $p['customer'] !== '' ? $h($p['customer']) : '—'; ?></td>
              <td data-l="Method"><?php echo $h($p['method']); ?><?php if ($p['note'] !== '') { ?><span class="vp-sub"><?php echo $h($p['note']); ?></span><?php } ?></td>
              <td data-l="Reference"><?php echo $p['reference'] !== '' ? $h($p['reference']) : '—'; ?></td>
              <td data-l="Recorded by"><?php echo $h($p['recorded_name']); ?><span class="vp-sub"><?php echo $h(date('d M Y', strtotime($p['created_at']))); ?></span></td>
              <?php if ($can_edit) { ?><td data-l="" class="nw">
                <form method="post" action="<?php echo $base; ?>manager/hr/vertical_payout_delete" onsubmit="return confirm('Remove this payout of <?php echo $money($p['amount']); ?>?');">
                  <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                  <input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
                  <button type="submit" class="btn btn-default btn-sm" title="Remove this payout" aria-label="Remove this payout"><i class="fa fa-trash-o"></i></button>
                </form></td><?php } ?>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
      <?php } ?>
    </div>
  </section>
</div>
<?php if ($can_edit) { ?>
<script>
(function () {
  var C = <?php echo json_encode($clients, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
  var v = document.getElementById('vp1'), c = document.getElementById('vp2'), a = document.getElementById('vp4');
  function fill() {
    var list = C[v.value] || [];
    c.innerHTML = '<option value="">All / not for one client</option>';
    list.forEach(function (x) { var o = document.createElement('option'); o.value = x.id; o.textContent = x.n + (x.a > 0 ? ' (agreed ' + x.a.toFixed(2) + ')' : ''); o.setAttribute('data-a', x.a); c.appendChild(o); });
    c.disabled = !list.length;
  }
  v.addEventListener('change', fill);
  c.addEventListener('change', function () { var o = c.options[c.selectedIndex]; var x = o && parseFloat(o.getAttribute('data-a')); if (x > 0 && !a.value) a.value = x.toFixed(2); });
  fill();
})();
</script>
<?php } ?>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
