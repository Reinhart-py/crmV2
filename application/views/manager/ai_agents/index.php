<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$n = function ($v) { return number_format((int)$v); };
$base = base_url() . 'manager/';
$flash = $this->session->flashdata('error_message');
$hot = Ai_agent_model::$hot;
$oc = function ($o) use ($hot) {
  if (in_array($o, $hot, true)) return 'oc-hot';
  if (in_array($o, array('Not interested', 'Wrong number', 'Do not call', 'Failed'), true)) return 'oc-bad';
  return $o ? 'oc-mid' : 'oc';
};
$when = function ($r) {
  $t = $r['ended_at'] ?: ($r['started_at'] ?: $r['queued_at']);
  if (!$t) return '';
  return date('Y-m-d', strtotime($t)) === date('Y-m-d') ? 'Today ' . date('H:i', strtotime($t)) : date('d M H:i', strtotime($t));
};
$call_row = function ($r, $is_inbox) use ($h, $oc, $when, $base) {
  $label = $r['status'] === 'Calling' ? 'Calling now' : ($r['outcome'] ?: $r['status']);
  ob_start(); ?>
  <li class="ai-call<?php echo $is_inbox ? ' is-hot' : ''; ?>" data-call="<?php echo (int)$r['id']; ?>" data-oc="<?php echo $h($r['outcome']); ?>" data-ag="<?php echo (int)$r['agent_id']; ?>" data-q="<?php echo $h(strtolower($r['customer'] . ' ' . $r['contact'] . ' ' . $r['phone'] . ' ' . $r['owner_name'] . ' ' . $r['campaign_name'])); ?>">
    <div class="who">
      <b><?php echo $h($r['customer'] ?: $r['phone']); ?></b>
      <span><?php echo $h(trim($r['contact'] . ' · ' . $r['phone'], ' ·')); ?></span>
    </div>
    <div>
      <span class="chip <?php echo $r['status'] === 'Calling' ? 'st-Scheduled' : $oc($r['outcome']); ?>"><?php echo $h($label); ?></span>
      <?php if ($r['is_test']) { ?><span class="chip s">Test</span><?php } ?>
      <?php if ($r['summary'] !== null && $r['summary'] !== '') { ?><div class="sum"><?php echo $h($r['summary']); ?></div><?php } elseif ($r['error']) { ?><div class="sum"><?php echo $h($r['error']); ?></div><?php } ?>
      <div class="meta">
        <span><i class="fa fa-clock-o"></i> <?php echo $h($when($r)); ?><?php echo $r['duration'] > 0 ? ' · ' . gmdate($r['duration'] >= 3600 ? 'G:i:s' : 'i:s', (int)$r['duration']) : ''; ?></span>
        <?php if ($r['owner_name']) { ?><span><i class="fa fa-user"></i> <?php echo $h($r['owner_name']); ?></span><?php } ?>
        <?php if ($r['campaign_name']) { ?><span><i class="fa fa-flag-o"></i> <?php echo $h($r['campaign_name']); ?></span><?php } ?>
        <?php if ($r['followup_date']) { ?><span><i class="fa fa-calendar"></i> Follow up <?php echo $h(date('d M', strtotime($r['followup_date']))); ?></span><?php } ?>
      </div>
    </div>
    <div class="cp-acts">
      <button type="button" class="btn btn-default btn-sm" data-open-call="<?php echo (int)$r['id']; ?>"><i class="fa fa-file-text-o"></i> Details</button>
      <?php if ($is_inbox && $r['lead_id']) { ?><a class="btn btn-primary btn-sm" href="<?php echo $base; ?>calls?lead=<?php echo (int)$r['lead_id']; ?>"><i class="fa fa-phone"></i> Call</a><?php } ?>
      <?php if ($is_inbox) { ?><button type="button" class="btn btn-default btn-sm" data-done="<?php echo (int)$r['id']; ?>" title="A person has taken this over"><i class="fa fa-check"></i> Done</button><?php } ?>
    </div>
  </li>
  <?php return ob_get_clean();
};
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-campaigns.css?v=20261107_2">
<div class="content-wrapper sx cp">
  <section class="sx-head">
    <h1>AI Calling Agents<small>Voice agents call campaign records and pass interested customers back to your team</small></h1>
    <div class="sx-btns">
      <a class="btn btn-default" href="<?php echo $base; ?>campaigns"><i class="fa fa-flag-o"></i> Campaigns</a>
      <?php if ($can_settings) { ?><a class="btn btn-default" href="<?php echo $base; ?>ai-agents/settings"><i class="fa fa-cog"></i> Settings</a><?php } ?>
      <?php if ($can_write) { ?><a class="btn btn-primary" href="<?php echo $base; ?>ai-agents/new"><i class="fa fa-plus"></i> New agent</a><?php } ?>
    </div>
  </section>

  <section class="content">
    <?php if ($flash) { ?><div class="alert alert-warning"><?php echo $h($flash); ?></div><?php } ?>
    <?php if ($not_ready) { ?>
      <div class="ai-off"><i class="fa fa-plug"></i><span><b>AI calls are off.</b> <?php echo $h($not_ready); ?> You can set up agents now; calls start once a provider is connected.</span>
        <?php if ($can_settings) { ?><a class="btn btn-default btn-sm" href="<?php echo $base; ?>ai-agents/settings">Connect a provider</a><?php } ?></div>
    <?php } ?>

    <div class="sx-kpis">
      <div class="sx-kpi"><span class="ic"><i class="fa fa-phone"></i></span><div><b id="kToday"><?php echo $n($sum['today']); ?></b><span>AI calls today</span></div></div>
      <div class="sx-kpi g"><span class="ic"><i class="fa fa-comments-o"></i></span><div><b><?php echo $sum['week'] ? round($sum['talked'] * 100 / $sum['week']) : 0; ?>%</b><span>Answered (7 days)</span></div></div>
      <button type="button" class="sx-kpi g" data-tab="inbox"><span class="ic"><i class="fa fa-star"></i></span><div><b id="kHot"><?php echo $n($sum['open_hot']); ?></b><span>Interested, to follow up</span></div></button>
      <div class="sx-kpi a"><span class="ic"><i class="fa fa-hourglass-half"></i></span><div><b id="kQueue"><?php echo $n($sum['queued'] + $sum['calling']); ?></b><span>Queued or calling</span></div></div>
    </div>

    <div class="sx-card">
      <nav class="ai-tabs" role="tablist">
        <button type="button" role="tab" data-tab="agents" class="is-on">Agents <span class="n"><?php echo count($agents); ?></span></button>
        <button type="button" role="tab" data-tab="inbox">To follow up <span class="n" id="nHot"><?php echo count($inbox); ?></span></button>
        <button type="button" role="tab" data-tab="log">Call log</button>
      </nav>

      <div class="ai-pane" data-pane="agents">
        <?php if (!$agents) { ?>
          <div class="sx-empty"><i class="fa fa-microphone"></i>No AI agents yet.<?php if ($can_write) { ?><br><a class="btn btn-primary" style="margin-top:12px" href="<?php echo $base; ?>ai-agents/new"><i class="fa fa-plus"></i> Create the first agent</a><?php } ?></div>
        <?php } else { ?>
        <ul class="cp-list">
          <?php foreach ($agents as $a) { ?>
          <li class="cp-row">
            <div>
              <a class="cp-name" href="<?php echo $base; ?>ai-agents/view/<?php echo (int)$a['id']; ?>"><?php echo $h($a['name']); ?></a>
              <div class="cp-tags">
                <span class="chip st-<?php echo $h($a['status']); ?>"><?php echo $h($a['status']); ?></span>
                <span class="chip s"><?php echo $h(isset($goals[$a['goal']]) ? $goals[$a['goal']] : $a['goal']); ?></span>
                <span class="chip s"><?php echo $h(isset($languages[$a['language']]) ? $languages[$a['language']] : $a['language']); ?></span>
              </div>
              <div class="cp-meta">
                <span><i class="fa fa-clock-o"></i><?php echo $h($a['call_from'] . '–' . $a['call_to']); ?>, up to <?php echo (int)$a['daily_limit']; ?> calls a day</span>
                <?php if ($a['campaigns']) { ?><span><i class="fa fa-flag-o"></i><?php echo $h(implode(', ', array_map(function ($c) { return $c['name']; }, array_slice($a['campaigns'], 0, 2)))) . (count($a['campaigns']) > 2 ? ' +' . (count($a['campaigns']) - 2) : ''); ?></span><?php } else { ?><span><i class="fa fa-flag-o"></i>No campaign yet</span><?php } ?>
              </div>
            </div>
            <div class="cp-nums">
              <div><b><?php echo $n($a['calls7']); ?></b><span>Calls, 7 days</span></div>
              <div><b><?php echo $n($a['talked7']); ?></b><span>Answered</span></div>
              <div><b><?php echo $n($a['hot7']); ?></b><span>Interested</span></div>
              <div><b><?php echo $n($a['queued']); ?></b><span>Queued</span></div>
            </div>
            <div class="cp-acts">
              <a class="btn btn-default btn-sm" href="<?php echo $base; ?>ai-agents/view/<?php echo (int)$a['id']; ?>"><i class="fa fa-bar-chart"></i> Results</a>
              <?php if ($can_write) { ?>
              <div class="cp-more">
                <button type="button" class="btn btn-default btn-sm" aria-haspopup="true" aria-label="More actions"><i class="fa fa-ellipsis-h"></i></button>
                <div class="cp-menu" role="menu">
                  <a href="<?php echo $base; ?>ai-agents/edit/<?php echo (int)$a['id']; ?>"><i class="fa fa-pencil"></i> Edit</a>
                  <?php if ($a['status'] === 'Active') { ?><button type="button" data-st="Paused" data-id="<?php echo (int)$a['id']; ?>"><i class="fa fa-pause"></i> Pause</button>
                  <?php } else { ?><button type="button" data-st="Active" data-id="<?php echo (int)$a['id']; ?>"><i class="fa fa-play"></i> Activate</button><?php } ?>
                  <a href="<?php echo $base; ?>ai-agents/new?copy=<?php echo (int)$a['id']; ?>"><i class="fa fa-clone"></i> Duplicate</a>
                  <?php if ($a['queued']) { ?><button type="button" data-stopq="<?php echo (int)$a['id']; ?>"><i class="fa fa-stop"></i> Clear its queue</button><?php } ?>
                  <?php if (!$a['calls7'] && !$a['queued']) { ?><button type="button" class="dg" data-del="<?php echo (int)$a['id']; ?>"><i class="fa fa-trash-o"></i> Delete</button><?php } ?>
                </div>
              </div>
              <?php } ?>
            </div>
          </li>
          <?php } ?>
        </ul>
        <?php } ?>
      </div>

      <div class="ai-pane" data-pane="inbox" hidden>
        <ul class="ai-calls" id="inboxList">
          <?php foreach ($inbox as $r) echo $call_row($r, true); ?>
        </ul>
        <div class="sx-empty" id="inboxNone"<?php echo $inbox ? ' style="display:none"' : ''; ?>><i class="fa fa-check-circle-o"></i>Nothing to follow up. Interested customers from AI calls show here, and as follow-ups for the record owner.</div>
      </div>

      <div class="ai-pane" data-pane="log" hidden>
        <div class="ai-bar">
          <select id="fOc" class="form-control" aria-label="Result">
            <option value="">All results</option>
            <?php foreach ($outcomes as $o) { ?><option><?php echo $h($o); ?></option><?php } ?>
          </select>
          <?php if (count($agents) > 1) { ?><select id="fAg" class="form-control" aria-label="Agent"><option value="">All agents</option><?php foreach ($agents as $a) { ?><option value="<?php echo (int)$a['id']; ?>"><?php echo $h($a['name']); ?></option><?php } ?></select><?php } ?>
          <input type="search" id="fQ" class="form-control sx-q" placeholder="Search customer, phone, owner" aria-label="Search calls">
          <span class="sx-count" id="logCount"></span>
        </div>
        <ul class="ai-calls" id="logList">
          <?php foreach ($log as $r) echo $call_row($r, false); ?>
        </ul>
        <div class="sx-empty" id="logNone"<?php echo $log ? ' style="display:none"' : ''; ?>><i class="fa fa-phone"></i><?php echo $log ? 'No calls match.' : 'No AI calls yet.'; ?></div>
      </div>
    </div>
  </section>
</div>

<script>window.AI_BASE = '<?php echo $base; ?>';</script>
<script src="<?php echo base_url(); ?>assets/dist/js/crm-ai-agents.js?v=20261107_1"></script>
<script>
$(function () {
  var base = window.AI_BASE;
  function tab(t) {
    $('.ai-tabs button').removeClass('is-on').filter('[data-tab="' + t + '"]').addClass('is-on');
    $('.ai-pane').attr('hidden', true).filter('[data-pane="' + t + '"]').removeAttr('hidden');
    try { localStorage.setItem('aiTab', t); } catch (e) {}
  }
  $(document).on('click', '[data-tab]', function () { tab($(this).data('tab')); });
  var saved = null;
  try { saved = localStorage.getItem('aiTab'); } catch (e) {}
  if (location.hash === '#inbox') saved = 'inbox';
  if (saved && $('[data-pane="' + saved + '"]').length) tab(saved);

  function filter() {
    var oc = $('#fOc').val(), ag = $('#fAg').val() || '', q = $.trim($('#fQ').val()).toLowerCase(), rows = $('#logList > li'), shown = 0;
    rows.each(function () {
      var ok = (!oc || this.getAttribute('data-oc') === oc) && (!ag || this.getAttribute('data-ag') === ag) && (!q || this.getAttribute('data-q').indexOf(q) !== -1);
      this.style.display = ok ? '' : 'none';
      if (ok) shown++;
    });
    $('#logCount').text(rows.length ? shown + ' of ' + rows.length : '');
    if (rows.length) $('#logNone').toggle(shown === 0);
  }
  $('#fOc, #fAg').on('change', filter);
  $('#fQ').on('input', filter);
  filter();

  $(document).on('ai:handled', function () {
    var left = $('#inboxList > li').length;
    $('#nHot, #kHot').text(left);
    $('#inboxNone').toggle(left === 0);
  });

  $(document).on('click', '.cp-menu [data-st]', function () {
    $.post(base + 'ai-agents/ajax_status', { id: $(this).data('id'), status: $(this).data('st') }, function (r) {
      if (r && r.status) location.reload(); else alert((r && r.message) || 'Not saved');
    }, 'json');
  });
  $(document).on('click', '.cp-menu [data-stopq]', function () {
    if (!confirm('Remove every queued call of this agent?')) return;
    $.post(base + 'ai-agents/ajax_cancel_queue', { agent_id: $(this).data('stopq') }, function (r) {
      if (r && r.status) location.reload(); else alert((r && r.message) || 'Not done');
    }, 'json');
  });
  $(document).on('click', '.cp-menu [data-del]', function () {
    if (!confirm('Delete this agent? It has not made any calls.')) return;
    $.post(base + 'ai-agents/ajax_delete', { id: $(this).data('del') }, function (r) {
      if (r && r.status) location.reload(); else alert((r && r.message) || 'Not deleted');
    }, 'json');
  });

  <?php if ($can_write && !$not_ready) { ?>
  // keeps queued calls moving while the page is open (a cron job can do this too, see Settings)
  setInterval(function () {
    if (document.hidden) return;
    $.post(base + 'ai-agents/ajax_dispatch', {}, function (r) {
      if (!r || !r.sum) return;
      $('#kToday').text(r.sum.today);
      $('#kQueue').text(r.sum.queued + r.sum.calling);
    }, 'json');
  }, 30000);
  <?php } ?>
});
</script>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
