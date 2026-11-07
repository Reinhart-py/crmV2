<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$n = function ($v) { return number_format((int)$v); };
$dur = function ($s) { $s = (int)$s; return $s >= 3600 ? floor($s / 3600) . 'h ' . floor($s % 3600 / 60) . 'm' : ($s >= 60 ? floor($s / 60) . 'm ' . ($s % 60) . 's' : $s . 's'); };
$when = function ($t) { return $t ? date('d M, H:i', strtotime($t)) : '–'; };
$pct = function ($a, $b) { return $b > 0 ? round($a * 100 / $b) : 0; };
$base = base_url() . 'manager/';
$st = $c['phase'];
$people_n = max(1, count($r['people']));
$team_target = (int)$c['daily_target'] * $people_n;
$max_day = max(1, max($r['days']));
$max_out = 1;
foreach ($r['outcomes'] as $o) $max_out = max($max_out, (int)$o['n']);
$pipe = array('all' => 'Database and leads', 'database' => 'Database only', 'lead' => 'Leads only');
$filters = array_filter(array(
  'Record type' => isset($pipe[$c['pipeline_type']]) ? $pipe[$c['pipeline_type']] : $c['pipeline_type'],
  'Country' => $c['country_name'], 'State / emirate' => $c['state_name'], 'City / area' => $c['city'],
  'Industry' => $c['industry_category'], 'Database category' => $c['database_category'], 'Data source' => $c['data_source'],
  'Stages' => implode(', ', $c['stage_names'])
));
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-campaigns.css?v=20261107_11">
<div class="content-wrapper sx cp">
  <section class="sx-head">
    <h1><?php echo $h($c['name']); ?>
      <small class="cp-top">
        <span class="chip st-<?php echo $h($st); ?>"><?php echo $st === 'Active' ? 'Running' : $h($st); ?></span>
        <?php if ($c['priority'] !== 'Normal') { ?><span class="chip pr-<?php echo $h($c['priority']); ?>"><?php echo $h($c['priority']); ?> priority</span><?php } ?>
        <span class="chip s"><?php echo $h(isset($objectives[$c['objective']]) ? $objectives[$c['objective']] : $c['objective']); ?></span>
        <span>by <?php echo $h($c['created_by_name']); ?><?php if ($c['start_date'] || $c['end_date']) { ?> · <?php echo $c['start_date'] ? date('d M Y', strtotime($c['start_date'])) : 'now'; ?> – <?php echo $c['end_date'] ? date('d M Y', strtotime($c['end_date'])) : 'open'; ?><?php } ?></span>
      </small>
    </h1>
    <div class="sx-btns">
      <a class="btn btn-default" href="<?php echo $base; ?>campaigns"><i class="fa fa-arrow-left"></i> All campaigns</a>
      <a class="btn btn-default" href="<?php echo $base; ?>campaigns/export/<?php echo (int)$c['id']; ?>"><i class="fa fa-download"></i> CSV</a>
      <?php if ($c['can_manage']) { ?>
        <?php if ($c['status'] === 'Active') { ?><button type="button" class="btn btn-default" data-act="Paused"><i class="fa fa-pause"></i> Pause</button><?php } else { ?><button type="button" class="btn btn-default" data-act="Active"><i class="fa fa-play"></i> <?php echo $c['status'] === 'Completed' ? 'Reopen' : 'Resume'; ?></button><?php } ?>
        <a class="btn btn-default" href="<?php echo $base; ?>campaigns/edit/<?php echo (int)$c['id']; ?>"><i class="fa fa-pencil"></i> Edit</a>
      <?php } ?>
      <?php if ($st === 'Active') { ?><a class="btn btn-primary" href="<?php echo $base; ?>calls?campaign=<?php echo (int)$c['id']; ?>"><i class="fa fa-phone"></i> Start dialing</a><?php } ?>
    </div>
  </section>

  <section class="content">
    <div class="sx-kpis">
      <div class="sx-kpi"><span class="ic"><i class="fa fa-database"></i></span><div><b><?php echo $n($r['total_records']); ?></b><span>Records in campaign</span></div></div>
      <div class="sx-kpi"><span class="ic"><i class="fa fa-user"></i></span><div><b><?php echo $n($r['leads_called']); ?> <small style="font-size:13px;font-weight:600;color:var(--cp-mut)"><?php echo (float)$r['progress']; ?>%</small></b><span>Records reached</span></div></div>
      <div class="sx-kpi g"><span class="ic"><i class="fa fa-check"></i></span><div><b><?php echo $n($r['connected']); ?> <small style="font-size:13px;font-weight:600;color:var(--cp-mut)"><?php echo $pct($r['connected'], $r['calls']); ?>%</small></b><span>Connected calls</span></div></div>
      <div class="sx-kpi g"><span class="ic"><i class="fa fa-thumbs-o-up"></i></span><div><b><?php echo $n($r['interested']); ?></b><span>Interested / call back</span></div></div>
      <div class="sx-kpi a"><span class="ic"><i class="fa fa-phone"></i></span><div><b><?php echo $n($r['calls_today']); ?><?php if ($team_target) { ?> <small style="font-size:13px;font-weight:600;color:var(--cp-mut)">/ <?php echo $n($team_target); ?></small><?php } ?></b><span>Calls today</span></div></div>
      <div class="sx-kpi s"><span class="ic"><i class="fa fa-clock-o"></i></span><div><b><?php echo $dur($r['talk_seconds']); ?></b><span>Talk time · <?php echo $n($r['calls']); ?> calls</span></div></div>
    </div>

    <?php if ($r['goal_pct'] !== null) { ?>
    <div class="sx-card"><div class="sx-card-b goal">
      <span class="sx-help"><i class="fa fa-flag-checkered"></i>Goal: <?php echo $h($goal_types[$c['goal_type']]); ?></span>
      <div class="bar<?php echo $r['goal_pct'] >= 100 ? ' g' : ''; ?>"><i style="width:<?php echo (int)$r['goal_pct']; ?>%"></i></div>
      <b><?php echo $n($r['goal_done']); ?> / <?php echo $n($c['goal_target']); ?> · <?php echo (int)$r['goal_pct']; ?>%</b>
    </div></div>
    <?php } ?>

    <?php if (!empty($ai)) { $as = $ai['stats']; ?>
    <div class="sx-card" id="aiCard"><div class="sx-card-b ai-ai">
      <span class="sx-help"><i class="fa fa-microphone"></i>AI agent: <a href="<?php echo $base; ?>ai-agents/view/<?php echo (int)$ai['agent']['id']; ?>"><?php echo $h($ai['agent']['name']); ?></a><?php if ($ai['agent']['status'] !== 'Active') { ?> <span class="chip st-<?php echo $h($ai['agent']['status']); ?>"><?php echo $h($ai['agent']['status']); ?></span><?php } ?></span>
      <div class="nums">
        <span><b><?php echo $n($as['queued'] + $as['calling']); ?></b>queued</span>
        <span><b><?php echo $n($as['done']); ?></b>called</span>
        <span><b><?php echo $n($as['talked']); ?></b>answered</span>
        <span><b><?php echo $n($as['hot']); ?></b>interested</span>
        <?php if ($as['dnd']) { ?><span><b><?php echo $n($as['dnd']); ?></b>asked not to call</span><?php } ?>
      </div>
      <?php if ($ai['can_queue'] && $st === 'Active') { ?>
      <div class="cp-acts" style="margin-left:auto">
        <select id="aiLimit" class="form-control" style="width:auto;height:32px;min-height:32px" aria-label="How many records">
          <?php foreach (array(10, 25, 50, 100, 250, 500) as $q) { ?><option value="<?php echo $q; ?>"<?php echo $q === 50 ? ' selected' : ''; ?>><?php echo $q; ?> records</option><?php } ?>
        </select>
        <button type="button" class="btn btn-primary btn-sm" id="aiQueue"><i class="fa fa-play"></i> Queue for AI</button>
        <?php if ($as['queued']) { ?><button type="button" class="btn btn-default btn-sm" id="aiStop"><i class="fa fa-stop"></i> Clear queue</button><?php } ?>
      </div>
      <?php } ?>
      <?php if ($ai['not_ready']) { ?><span class="hint" style="width:100%;margin:0">AI calls are off: <?php echo $h($ai['not_ready']); ?></span><?php } ?>
      <span class="hint" id="aiMsg" role="status" style="width:100%;margin:0"></span>
    </div></div>
    <?php } ?>

    <div class="two">
      <div class="sx-card">
        <div class="sx-card-h"><h3><i class="fa fa-bar-chart"></i> Calls per day <small>last 14 days</small></h3></div>
        <div class="sx-card-b">
          <div class="days" role="img" aria-label="Calls per day for the last 14 days">
            <?php foreach ($r['days'] as $day => $cnt) { ?>
              <div title="<?php echo date('D d M', strtotime($day)) . ': ' . $cnt . ' calls'; ?>"><i class="<?php echo $cnt ? '' : 'z'; ?>" style="height:<?php echo max(2, round($cnt * 100 / $max_day)); ?>%"><?php if ($cnt) { ?><em><?php echo $cnt; ?></em><?php } ?></i><small><?php echo date('d', strtotime($day)); ?></small></div>
            <?php } ?>
          </div>
        </div>
      </div>
      <div class="sx-card">
        <div class="sx-card-h"><h3><i class="fa fa-pie-chart"></i> Call results</h3></div>
        <div class="sx-card-b">
          <?php if (empty($r['outcomes'])) { ?><div class="sx-empty" style="padding:16px 0"><i class="fa fa-phone"></i>No calls yet.</div><?php } else { ?>
          <ul class="res">
            <?php foreach ($r['outcomes'] as $o) { ?>
              <li><span><?php echo $h($o['name']); ?></span><b><?php echo $n($o['n']); ?></b><div class="bar"><i style="width:<?php echo round($o['n'] * 100 / $max_out); ?>%"></i></div></li>
            <?php } ?>
          </ul>
          <?php } ?>
        </div>
      </div>
    </div>

    <div class="sx-card">
      <div class="sx-card-h"><h3><i class="fa fa-users"></i> Team</h3><?php if ($c['daily_target']) { ?><div class="sx-tools"><span class="chip s">Target <?php echo $n($c['daily_target']); ?> calls / person / day</span></div><?php } ?></div>
      <div class="sx-table-wrap">
        <table class="sx-table sx-cards">
          <thead><tr><th>Person</th><th>Records</th><th>Calls</th><th>Reached</th><th>Connected</th><th>Today</th><th>Talk time</th><th>Last call</th></tr></thead>
          <tbody>
            <?php foreach ($r['people'] as $p) { $hit = $c['daily_target'] && $p['today'] >= $c['daily_target']; ?>
            <tr>
              <td data-l="Person"><span class="nm"><?php echo $h($p['name']); ?></span></td>
              <td data-l="Records" class="nw"><?php echo $n($p['records']); ?></td>
              <td data-l="Calls" class="nw"><?php echo $n($p['calls']); ?></td>
              <td data-l="Reached" class="nw"><?php echo $n($p['reached']); ?><?php if ($p['records']) { ?> <span class="tgt"><?php echo $pct($p['reached'], $p['records']); ?>%</span><?php } ?></td>
              <td data-l="Connected" class="nw"><?php echo $n($p['connected']); ?></td>
              <td data-l="Today" class="nw"><?php echo $n($p['today']); ?><?php if ($c['daily_target']) { ?> <span class="tgt<?php echo $hit ? ' ok' : ''; ?>">/ <?php echo $n($c['daily_target']); ?><?php echo $hit ? ' ✓' : ''; ?></span><?php } ?></td>
              <td data-l="Talk time" class="nw"><?php echo $dur($p['talk']); ?></td>
              <td data-l="Last call" class="nw"><?php echo $when($p['last']); ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="two">
      <div class="sx-card">
        <div class="sx-card-h"><h3><i class="fa fa-history"></i> Recent calls</h3></div>
        <?php if (empty($r['recent'])) { ?><div class="sx-empty"><i class="fa fa-phone"></i>No calls yet.</div><?php } else { ?>
        <div class="sx-table-wrap">
          <table class="sx-table sx-cards">
            <thead><tr><th>When</th><th>Customer</th><th>By</th><th>Result</th><th>Length</th></tr></thead>
            <tbody>
              <?php foreach ($r['recent'] as $l) { ?>
              <tr>
                <td data-l="When" class="nw"><?php echo $when($l['created_at']); ?></td>
                <td data-l="Customer"><div><span class="nm" style="font-size:14px"><?php echo $h($l['customer_name'] ?: 'Unknown'); ?></span><span class="sub mono"><?php echo $h($l['phone_number']); ?></span><?php if ($l['notes']) { ?><span class="sub"><?php echo $h(mb_strimwidth($l['notes'], 0, 90, '…')); ?></span><?php } ?></div></td>
                <td data-l="By"><?php echo $h($l['agent']); ?></td>
                <td data-l="Result"><span class="chip<?php echo in_array($l['disposition'], array('Connected', 'Expected', 'Follow up / Call Back'), true) ? ' ok' : ' s'; ?>"><?php echo $h($l['disposition'] ?: 'Not set'); ?></span></td>
                <td data-l="Length" class="nw"><?php echo $dur($l['duration']); ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <?php } ?>
      </div>

      <div>
        <div class="sx-card">
          <div class="sx-card-h"><h3><i class="fa fa-sliders"></i> Setup</h3></div>
          <div class="sx-card-b">
            <dl class="dl">
              <?php foreach ($filters as $k => $val) { ?><dt><?php echo $k; ?></dt><dd><?php echo $h($val); ?></dd><?php } ?>
              <dt>Tries per record</dt><dd><?php echo (int)$c['max_attempts'] ?: 'No limit'; ?></dd>
              <dt>Wait between tries</dt><dd><?php $rh = (int)$c['retry_hours']; echo $rh ? ($rh % 24 === 0 ? ($rh / 24) . ' day' . ($rh > 24 ? 's' : '') : $rh . ' hour' . ($rh > 1 ? 's' : '')) : 'No wait'; ?></dd>
              <?php if ($c['reference_no']) { ?><dt>Reference</dt><dd class="mono"><?php echo $h($c['reference_no']); ?></dd><?php } ?>
              <?php if ($c['description']) { ?><dt>Notes</dt><dd><?php echo nl2br($h($c['description'])); ?></dd><?php } ?>
            </dl>
          </div>
        </div>
        <?php if ($c['script']) { ?>
        <div class="sx-card">
          <div class="sx-card-h"><h3><i class="fa fa-file-text-o"></i> Call script</h3></div>
          <div class="sx-card-b"><pre class="script-box"><?php echo $h($c['script']); ?></pre></div>
        </div>
        <?php } ?>
      </div>
    </div>
  </section>
</div>
<?php if ($c['can_manage']) { ?>
<script>
$(function () {
  $('[data-act]').on('click', function () {
    $.post('<?php echo $base; ?>campaigns/ajax_status', { id: <?php echo (int)$c['id']; ?>, status: $(this).data('act') }, function (r) {
      if (r && r.status) location.reload(); else alert((r && r.message) || 'Not saved');
    }, 'json');
  });
});
</script>
<?php } ?>
<?php if (!empty($ai) && $ai['can_queue']) { ?>
<script>
$(function () {
  var cid = <?php echo (int)$c['id']; ?>, base = '<?php echo $base; ?>';
  $('#aiQueue').on('click', function () {
    var b = $(this).prop('disabled', true);
    $('#aiMsg').text('Adding records…');
    $.post(base + 'ai-agents/ajax_queue_campaign', { campaign_id: cid, limit: $('#aiLimit').val() }, function (r) {
      $('#aiMsg').text((r && r.message) || 'Not queued');
      b.prop('disabled', false);
      if (r && r.status && r.added) setTimeout(function () { location.reload(); }, 2500);
    }, 'json').fail(function () { $('#aiMsg').text('The server did not respond.'); b.prop('disabled', false); });
  });
  $('#aiStop').on('click', function () {
    if (!confirm('Remove the queued AI calls of this campaign? Calls in progress finish normally.')) return;
    $.post(base + 'ai-agents/ajax_cancel_queue', { campaign_id: cid }, function (r) {
      $('#aiMsg').text((r && r.message) || 'Not done');
      if (r && r.status) setTimeout(function () { location.reload(); }, 1200);
    }, 'json');
  });
});
</script>
<?php } ?>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
