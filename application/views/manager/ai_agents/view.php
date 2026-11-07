<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$n = function ($v) { return number_format((int)$v); };
$base = base_url() . 'manager/';
$done = 0;
foreach ($outcomes as $o) $done += (int)$o['n'];
$hotn = 0;
foreach ($outcomes as $o) if (in_array($o['name'], Ai_agent_model::$hot, true)) $hotn += (int)$o['n'];
$oc = function ($o) {
  if (in_array($o, Ai_agent_model::$hot, true)) return 'oc-hot';
  if (in_array($o, array('Not interested', 'Wrong number', 'Do not call', 'Failed'), true)) return 'oc-bad';
  return 'oc-mid';
};
$days = array(1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun');
$day_names = array();
foreach (array_map('intval', explode(',', $a['call_days'])) as $d) if (isset($days[$d])) $day_names[] = $days[$d];
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-campaigns.css?v=20261107_11">
<div class="content-wrapper sx cp">
  <section class="sx-head">
    <h1><?php echo $h($a['name']); ?>
      <small class="cp-top">
        <span class="chip st-<?php echo $h($a['status']); ?>"><?php echo $h($a['status']); ?></span>
        <span class="chip s"><?php echo $h(isset($goals[$a['goal']]) ? $goals[$a['goal']] : $a['goal']); ?></span>
        <span class="chip s"><?php echo $h(isset($languages[$a['language']]) ? $languages[$a['language']] : $a['language']); ?></span>
      </small>
    </h1>
    <div class="sx-btns">
      <a class="btn btn-default" href="<?php echo $base; ?>ai-agents"><i class="fa fa-arrow-left"></i> AI agents</a>
      <?php if ($can_write) { ?><a class="btn btn-primary" href="<?php echo $base; ?>ai-agents/edit/<?php echo (int)$a['id']; ?>"><i class="fa fa-pencil"></i> Edit</a><?php } ?>
    </div>
  </section>

  <section class="content">
    <?php if ($not_ready) { ?><div class="ai-off"><i class="fa fa-plug"></i><span><b>AI calls are off.</b> <?php echo $h($not_ready); ?></span></div><?php } ?>
    <div class="sx-kpis">
      <div class="sx-kpi"><span class="ic"><i class="fa fa-phone"></i></span><div><b><?php echo $n($tot['calls'] - $tot['queued']); ?></b><span>Calls made</span></div></div>
      <div class="sx-kpi g"><span class="ic"><i class="fa fa-comments-o"></i></span><div><b><?php echo $done ? round($tot['talked'] * 100 / $done) : 0; ?>%</b><span>Answered</span></div></div>
      <div class="sx-kpi g"><span class="ic"><i class="fa fa-star"></i></span><div><b><?php echo $n($hotn); ?></b><span>Interested / call back</span></div></div>
      <div class="sx-kpi a"><span class="ic"><i class="fa fa-hourglass-half"></i></span><div><b><?php echo $n($tot['queued']); ?></b><span>Queued</span></div></div>
    </div>

    <div class="two">
      <div class="sx-card">
        <div class="sx-card-h"><h3>Recent calls</h3></div>
        <?php if (!$calls) { ?>
          <div class="sx-empty"><i class="fa fa-phone"></i>No calls yet. Link this agent to a campaign (Edit campaign, step 4) and queue records from the campaign page.</div>
        <?php } else { ?>
        <ul class="ai-calls">
          <?php foreach ($calls as $r) { ?>
          <li class="ai-call" style="grid-template-columns:minmax(0,1fr) auto">
            <div>
              <b style="font-size:14px"><?php echo $h($r['customer'] ?: $r['phone']); ?></b>
              <span class="chip <?php echo $r['status'] === 'Calling' ? 'st-Scheduled' : $oc($r['outcome']); ?>" style="margin-left:6px"><?php echo $h($r['status'] === 'Calling' ? 'Calling now' : ($r['outcome'] ?: $r['status'])); ?></span>
              <?php if ($r['is_test']) { ?><span class="chip s">Test</span><?php } ?>
              <?php if ($r['summary']) { ?><div class="sum"><?php echo $h($r['summary']); ?></div><?php } elseif ($r['error']) { ?><div class="sum"><?php echo $h($r['error']); ?></div><?php } ?>
              <div class="meta"><span><?php echo $h(date('d M H:i', strtotime($r['ended_at'] ?: ($r['started_at'] ?: $r['queued_at'])))); ?></span><?php if ($r['duration'] > 0) { ?><span><?php echo gmdate('i:s', (int)$r['duration']); ?></span><?php } ?><?php if ($r['campaign_name']) { ?><span><?php echo $h($r['campaign_name']); ?></span><?php } ?></div>
            </div>
            <div class="cp-acts"><button type="button" class="btn btn-default btn-sm" data-open-call="<?php echo (int)$r['id']; ?>">Details</button></div>
          </li>
          <?php } ?>
        </ul>
        <?php } ?>
      </div>

      <div>
        <div class="sx-card">
          <div class="sx-card-h"><h3>Call results</h3></div>
          <div class="sx-card-b">
            <?php if (!$outcomes) { ?><p class="hint" style="margin:0">No finished calls yet.</p><?php } else { ?>
            <ul class="res">
              <?php foreach ($outcomes as $o) { $pct = $done ? round($o['n'] * 100 / $done) : 0; ?>
              <li><span><?php echo $h($o['name'] ?: 'Not set'); ?></span><b><?php echo $n($o['n']); ?></b><div class="bar<?php echo in_array($o['name'], Ai_agent_model::$hot, true) ? ' g' : ''; ?>"><i style="width:<?php echo $pct; ?>%"></i></div></li>
              <?php } ?>
            </ul>
            <?php } ?>
          </div>
        </div>
        <div class="sx-card">
          <div class="sx-card-h"><h3>Setup</h3></div>
          <div class="sx-card-b">
            <dl class="dl">
              <dt>Calling hours</dt><dd><?php echo $h($a['call_from'] . '–' . $a['call_to'] . ', ' . implode(' ', $day_names)); ?></dd>
              <dt>Limits</dt><dd><?php echo (int)$a['daily_limit']; ?> calls a day, <?php echo (int)$a['max_minutes']; ?> minutes a call</dd>
              <dt>Transfer to</dt><dd><?php echo $a['transfer_number'] !== '' ? $h($a['transfer_number']) : 'No live transfer; follow-up for the record owner'; ?></dd>
              <dt>Says it is AI</dt><dd><?php echo $a['disclose_ai'] ? 'Yes' : 'No'; ?></dd>
              <dt>Do-not-call requests</dt><dd><?php echo $a['dnd_on_request'] ? 'Record marked DND' : 'Logged only'; ?></dd>
              <dt>Campaigns</dt><dd><?php if (!$campaigns) echo 'None yet'; foreach ($campaigns as $i => $c) { echo $i ? ', ' : ''; ?><a href="<?php echo $base; ?>campaigns/view/<?php echo (int)$c['id']; ?>"><?php echo $h($c['name']); ?></a><?php } ?></dd>
            </dl>
          </div>
        </div>
        <div class="sx-card">
          <div class="sx-card-h"><h3>What the agent says</h3></div>
          <div class="sx-card-b" style="display:block">
            <p class="say"><?php echo $h($first); ?></p>
            <details><summary style="cursor:pointer;font-size:13px;font-weight:600">Full instructions for the AI</summary><pre class="prompt-box" style="margin-top:8px"><?php echo $h($prompt); ?></pre></details>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<script>window.AI_BASE = '<?php echo $base; ?>';</script>
<script src="<?php echo base_url(); ?>assets/dist/js/crm-ai-agents.js?v=20261107_1"></script>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
