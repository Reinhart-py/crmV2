<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-vertical.css?v=20261106_3">
<?php
$p = $p_info;
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$base = base_url();
$enc = base64_encode($p['id']);
$cur = $biz['cur'];
$T = $biz['tot'];
$money = function ($n) { $n = (float)$n; return number_format($n, (floor($n) == $n) ? 0 : 2); };
$dash = function ($s) use ($h) { $s = trim((string)$s); return $s === '' ? '<span class="vv-na">—</span>' : $h($s); };
$date = function ($d) { return (!empty($d) && $d !== '0000-00-00' && strtotime($d)) ? date('d M Y', strtotime($d)) : ''; };
$words = preg_split('/\s+/', trim((string)$p['company_name']));
$ini = strtoupper(substr($words[0], 0, 1) . (count($words) > 1 ? substr($words[1], 0, 1) : ''));
$st = strtolower((string)$p['vertical_status']);
$st_cls = (strpos($st, 'active') !== false && strpos($st, 'in') !== 0) ? 'ok' : ((strpos($st, 'inactive') !== false || strpos($st, 'block') !== false) ? 'x' : 'n');
$back = urldecode((string)$this->session->userdata('manager_return_string'));
$back = $back !== '' && strpos($back, 'verticals') !== false ? $base . 'manager/' . $back : $base . 'manager/verticals';
$balance = $T['owed'] - $T['paid'];
$tel = function ($n) { return preg_replace('/[^\d+]/', '', (string)$n); };
$next_fu = $date($p['followup_date']);
$overdue = $next_fu && strtotime($p['followup_date']) < strtotime(date('Y-m-d'));
?>
<div class="content-wrapper px-4">
  <section class="content">
    <div class="vv">
      <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> <?php echo get_message($this->session->flashdata('message'),'message'); ?>

      <!-- Header -->
      <header class="vv-head">
        <a class="vv-back" href="<?php echo $h($back); ?>" title="Back to the list" aria-label="Back to the list"><i class="fa fa-arrow-left"></i></a>
        <div class="vv-logo" aria-hidden="true"><?php echo $h($ini); ?></div>
        <div class="vv-title">
          <h1><?php echo $h($p['company_name']); ?></h1>
          <div class="vv-meta">
            <span class="vv-id">V-<?php echo $h($p['v_id']); ?></span>
            <?php if ($p['vertical_status'] !== '' && $p['vertical_status'] !== null) { ?><span class="vv-chip <?php echo $st_cls; ?>"><?php echo $h($p['vertical_status']); ?></span><?php } ?>
            <?php if (!empty($p['v_cat'])) { ?><span><?php echo $h($p['v_cat']); ?><?php if (!empty($p['v_subcat'])) { ?> <i class="fa fa-angle-right"></i> <?php echo $h($p['v_subcat']); ?><?php } ?></span><?php } ?>
            <?php if ($owner_name !== '') { ?><span><i class="fa fa-user"></i> <?php echo $h($owner_name); ?></span><?php } ?>
          </div>
        </div>
        <div class="vv-acts">
          <?php if (!empty($p['contact1'])) { ?><a class="vv-btn ic" href="tel:<?php echo $h($tel($p['contact1'])); ?>" title="Call <?php echo $h($p['poc']); ?>" aria-label="Call"><i class="fa fa-phone"></i></a><?php } ?>
          <?php if (!empty($p['email'])) { ?><a class="vv-btn ic" href="mailto:<?php echo $h($p['email']); ?>" title="Email <?php echo $h($p['poc']); ?>" aria-label="Email"><i class="fa fa-envelope-o"></i></a><?php } ?>
          <?php if (!empty($can_edit)) { ?><a class="vv-btn pri" href="<?php echo $base; ?>manager/verticals/edit/<?php echo $enc; ?>"><i class="fa fa-pencil"></i> Edit</a><?php } ?>
        </div>
      </header>

      <!-- Figures -->
      <div class="vv-kpis">
        <div class="vv-kpi">
          <span class="lb">Clients referred</span>
          <b><?php echo count($biz['clients']); ?></b>
          <small><?php echo (int)$T['sales']; ?> sale<?php echo $T['sales'] == 1 ? '' : 's'; ?></small>
        </div>
        <div class="vv-kpi wide">
          <span class="lb">Business achieved <em><?php echo $h($cur); ?></em></span>
          <div class="vv-per">
            <div><small>Month</small><b><?php echo $money($T['m']); ?></b></div>
            <div><small>Quarter</small><b><?php echo $money($T['q']); ?></b></div>
            <div><small>Year</small><b><?php echo $money($T['y']); ?></b></div>
            <div><small>Lifetime</small><b><?php echo $money($T['l']); ?></b></div>
          </div>
        </div>
        <?php if ($T['n_lead'] || $T['n_order']) { ?>
        <div class="vv-kpi">
          <span class="lb">Pending <em><?php echo $h($cur); ?></em></span>
          <?php if ($T['n_lead']) { ?><div class="vv-row"><small>Leads (<?php echo (int)$T['n_lead']; ?>)</small><b><?php echo $money($T['pend_lead']); ?></b></div><?php } ?>
          <?php if ($T['n_order']) { ?><div class="vv-row"><small>Orders (<?php echo (int)$T['n_order']; ?>)</small><b><?php echo $money($T['pend_order']); ?></b></div><?php } ?>
        </div>
        <?php } ?>
        <div class="vv-kpi">
          <span class="lb">Payouts <em><?php echo $h($cur); ?></em></span>
          <div class="vv-row"><small>Agreed</small><b><?php echo $money($T['owed']); ?></b></div>
          <div class="vv-row"><small>Paid so far</small><b class="ok"><?php echo $money($T['paid']); ?></b></div>
          <?php if (abs($balance) >= 0.005) { ?><div class="vv-row"><small><?php echo $balance > 0 ? 'Balance due' : 'Paid over'; ?></small><b class="<?php echo $balance > 0 ? 'due' : ''; ?>"><?php echo $money(abs($balance)); ?></b></div><?php } ?>
        </div>
      </div>

      <!-- Details -->
      <div class="vv-grid">
        <section class="vv-card">
          <h2><i class="fa fa-user"></i> Contact person</h2>
          <dl class="vv-dl">
            <div><dt>Name</dt><dd><?php echo $dash($p['poc']); ?></dd></div>
            <div><dt>Designation</dt><dd><?php echo $dash($p['designation']); ?></dd></div>
            <div><dt>Phone</dt><dd><?php echo !empty($p['contact1']) ? '<a href="tel:' . $h($tel($p['contact1'])) . '">' . $h($p['contact1']) . '</a>' : $dash(''); ?><?php if (!empty($p['contact2'])) { ?> <span class="vv-sep">·</span> <a href="tel:<?php echo $h($tel($p['contact2'])); ?>"><?php echo $h($p['contact2']); ?></a><?php } ?></dd></div>
            <div><dt>WhatsApp</dt><dd><?php echo !empty($p['whatsapp_web']) ? '<a href="https://wa.me/' . $h(ltrim($tel($p['whatsapp_web']), '+')) . '" target="_blank" rel="noopener">' . $h($p['whatsapp_web']) . '</a>' : $dash(''); ?></dd></div>
            <div><dt>Email</dt><dd><?php echo !empty($p['email']) ? '<a href="mailto:' . $h($p['email']) . '">' . $h($p['email']) . '</a>' : $dash(''); ?></dd></div>
            <div><dt>Nationality</dt><dd><?php echo $dash(trim($p['n_country'] . ($p['n_state'] ? ', ' . $p['n_state'] : ''), ', ')); ?></dd></div>
          </dl>
        </section>
        <section class="vv-card">
          <h2><i class="fa fa-map-marker"></i> Location &amp; follow-up</h2>
          <dl class="vv-dl">
            <div><dt>Location</dt><dd><?php echo $dash(implode(', ', array_filter(array($p['city'], $p['r_state'], $p['r_country'])))); ?></dd></div>
            <div><dt>Address</dt><dd><?php echo $dash(implode(', ', array_filter(array($p['address'], $p['address2'])))); ?></dd></div>
            <div><dt>Source</dt><dd><?php echo $dash($p['lead_source']); ?></dd></div>
            <div><dt>Last follow-up</dt><dd><?php echo $dash($date($p['lastfollowup_date'])); ?></dd></div>
            <div><dt>Next follow-up</dt><dd><?php echo $next_fu ? '<span class="' . ($overdue ? 'vv-late' : '') . '">' . $h($next_fu) . ($overdue ? ' · overdue' : '') . '</span>' : $dash(''); ?></dd></div>
            <div><dt>Added</dt><dd><?php echo $dash($date($p['created'])); ?></dd></div>
          </dl>
        </section>
      </div>
      <?php if (trim((string)$p['remark']) !== '') { ?>
      <section class="vv-card vv-remark"><h2><i class="fa fa-comment-o"></i> Remarks</h2><p><?php echo nl2br($h($p['remark'])); ?></p></section>
      <?php } ?>

      <!-- Clients -->
      <section class="vv-card vv-tablecard">
        <h2><i class="fa fa-users"></i> Clients referred <small><?php echo count($biz['clients']); ?></small></h2>
        <?php if (empty($biz['clients'])) { ?>
          <p class="vv-empty">No clients are linked to this vertical yet. A client is linked when this vertical is chosen under <b>Vertical Selection</b> on the client's record.</p>
        <?php } else { $show_pend = ($T['n_lead'] || $T['n_order']); ?>
        <div class="vv-scroll">
        <table class="vv-tbl">
          <thead>
            <tr>
              <th>Client</th>
              <th>Stage</th>
              <th class="n">Month</th><th class="n">Quarter</th><th class="n">Year</th><th class="n">Lifetime</th>
              <?php if ($show_pend) { ?><th class="n">Pending</th><?php } ?>
              <th class="n">Payout agreed</th><th class="n">Paid</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($biz['clients'] as $c) { $z = function ($n) use ($money) { return $n > 0 ? $money($n) : '<span class="vv-na">—</span>'; }; ?>
            <tr>
              <td data-l="Client"><a href="<?php echo $base; ?>manager/leads/view/<?php echo base64_encode($c['id']); ?>"><?php echo $h($c['name']); ?></a><small><?php echo $h($c['ref']); ?></small></td>
              <td data-l="Stage"><span class="vv-stage s-<?php echo strtolower($h($c['stage'])); ?>"><?php echo $h($c['stage']); ?></span></td>
              <td data-l="Month" class="n"><?php echo $z($c['m']); ?></td>
              <td data-l="Quarter" class="n"><?php echo $z($c['q']); ?></td>
              <td data-l="Year" class="n"><?php echo $z($c['y']); ?></td>
              <td data-l="Lifetime" class="n"><b><?php echo $z($c['l']); ?></b></td>
              <?php if ($show_pend) { ?><td data-l="Pending" class="n"><?php echo $c['pending'] > 0 ? '<span>' . $money($c['pending']) . ' <small class="vv-from">' . $h($c['pending_from']) . '</small></span>' : '<span class="vv-na">—</span>'; ?></td><?php } ?>
              <td data-l="Payout agreed" class="n"><?php echo $z($c['owed']); ?></td>
              <td data-l="Paid" class="n"><?php echo $z($c['paid']); ?></td>
            </tr>
          <?php } ?>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="2">Total <small><?php echo $h($cur); ?></small></th>
              <th class="n"><?php echo $money($T['m']); ?></th><th class="n"><?php echo $money($T['q']); ?></th><th class="n"><?php echo $money($T['y']); ?></th><th class="n"><?php echo $money($T['l']); ?></th>
              <?php if ($show_pend) { ?><th class="n"><?php echo $money($T['pend_lead'] + $T['pend_order']); ?></th><?php } ?>
              <th class="n"><?php echo $money($T['owed']); ?></th><th class="n"><?php echo $money($T['paid']); ?></th>
            </tr>
          </tfoot>
        </table>
        </div>
        <?php } ?>
      </section>

      <!-- Payouts -->
      <section class="vv-card vv-tablecard">
        <h2><i class="fa fa-money"></i> Payouts <small><?php echo count($biz['payouts']); ?></small>
          <?php if (!empty($can_pay)) { ?><a class="vv-btn sm" href="<?php echo $base; ?>manager/hr/vertical_payouts?vertical=<?php echo (int)$p['id']; ?>#add"><i class="fa fa-plus"></i> Record payout</a><?php } ?>
        </h2>
        <?php if (empty($biz['payouts'])) { ?>
          <p class="vv-empty">Nothing paid yet. Payouts to verticals are recorded by HR in <b>HR &amp; Payroll › Payroll › Vertical Payouts</b>.</p>
        <?php } else { ?>
        <div class="vv-scroll">
        <table class="vv-tbl">
          <thead><tr><th>Date</th><th class="n">Amount</th><th>For client</th><th>Method</th><th>Reference</th><th>Note</th><th>Recorded by</th></tr></thead>
          <tbody>
          <?php foreach ($biz['payouts'] as $py) { ?>
            <tr>
              <td data-l="Date"><?php echo $h($date($py['pay_date'])); ?></td>
              <td data-l="Amount" class="n"><b><?php echo $money($py['amount']); ?></b></td>
              <td data-l="For client"><?php echo $dash($py['customer']); ?></td>
              <td data-l="Method"><?php echo $dash($py['method']); ?></td>
              <td data-l="Reference"><?php echo $dash($py['reference']); ?></td>
              <td data-l="Note"><?php echo $dash($py['note']); ?></td>
              <td data-l="Recorded by"><?php echo $dash($py['recorded_name']); ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
        </div>
        <?php } ?>
      </section>
    </div>
  </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
