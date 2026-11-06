<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$n = function ($v) { return number_format((int)$v); };
$d = function ($v) { return $v ? date('d M', strtotime($v)) : ''; };
$base = base_url() . 'manager/';
$flash = $this->session->flashdata('error_message');
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-campaigns.css?v=20261107_2">
<div class="content-wrapper sx cp">
  <section class="sx-head">
    <h1>Campaigns<small>Plan calling campaigns, follow progress and start dialing</small></h1>
    <div class="sx-btns">
      <a class="btn btn-default" href="<?php echo $base; ?>calls"><i class="fa fa-phone"></i> Dialer</a>
      <?php if ($can_write) { ?><a class="btn btn-primary" href="<?php echo $base; ?>campaigns/new"><i class="fa fa-plus"></i> New campaign</a><?php } ?>
    </div>
  </section>

  <section class="content">
    <?php if ($flash) { ?><div class="alert alert-warning"><?php echo $h($flash); ?></div><?php } ?>
    <div class="sx-kpis">
      <div class="sx-kpi g"><span class="ic"><i class="fa fa-play"></i></span><div><b><?php echo $n($sum['Active']); ?></b><span>Running now</span></div></div>
      <div class="sx-kpi"><span class="ic"><i class="fa fa-phone"></i></span><div><b><?php echo $n($sum['calls_today']); ?></b><span>Campaign calls today</span></div></div>
      <div class="sx-kpi g"><span class="ic"><i class="fa fa-check"></i></span><div><b><?php echo $sum['calls'] ? round($sum['connected'] * 100 / $sum['calls']) : 0; ?>%</b><span>Connected (all calls)</span></div></div>
      <div class="sx-kpi a"><span class="ic"><i class="fa fa-pause"></i></span><div><b><?php echo $n($sum['Paused'] + $sum['Scheduled']); ?></b><span>Paused or scheduled</span></div></div>
    </div>

    <div class="sx-card">
      <div class="sx-bar">
        <div class="sx-chips" id="cpFilter">
          <?php foreach (array('' => 'All', 'Active' => 'Running', 'Scheduled' => 'Scheduled', 'Paused' => 'Paused', 'Ended' => 'Ended', 'Completed' => 'Completed') as $k => $lbl) {
            $cnt = $k === '' ? $sum['all'] : $sum[$k];
            if ($k !== '' && $k !== 'Active' && !$cnt) continue; ?>
            <button type="button" data-f="<?php echo $k; ?>" class="<?php echo $k === '' ? 'is-on' : ''; ?>"><?php echo $lbl; ?> <span class="n"><?php echo $cnt; ?></span></button>
          <?php } ?>
        </div>
        <input type="search" id="cpSearch" class="form-control sx-q" placeholder="Search name, reference or person" aria-label="Search campaigns">
        <span class="sx-count" id="cpCount"></span>
      </div>

      <?php if (empty($campaigns)) { ?>
        <div class="sx-empty"><i class="fa fa-flag-o"></i>No campaigns yet.<?php if ($can_write) { ?><br><a class="btn btn-primary" style="margin-top:12px" href="<?php echo $base; ?>campaigns/new"><i class="fa fa-plus"></i> Create the first campaign</a><?php } ?></div>
      <?php } else { ?>
      <ul class="cp-list" id="cpList">
        <?php foreach ($campaigns as $c) {
          $s = $c['stats'];
          $st = $c['phase'];
          $people = count($c['people']);
          $target = (int)$c['daily_target'] * max(1, $people);
          $search = strtolower($c['name'] . ' ' . $c['reference_no'] . ' ' . implode(' ', $c['people']) . ' ' . $c['created_by_name']);
          $dates = trim($d($c['start_date']) . ($c['end_date'] ? ' – ' . $d($c['end_date']) : ($c['start_date'] ? ' onward' : '')));
        ?>
        <li class="cp-row" data-st="<?php echo $h($st); ?>" data-q="<?php echo $h($search); ?>">
          <div>
            <a class="cp-name" href="<?php echo $base; ?>campaigns/view/<?php echo (int)$c['id']; ?>"><?php echo $h($c['name']); ?></a>
            <div class="cp-tags">
              <span class="chip st-<?php echo $h($st); ?>"><?php echo $st === 'Active' ? 'Running' : $h($st); ?></span>
              <?php if ($c['priority'] !== 'Normal') { ?><span class="chip pr-<?php echo $h($c['priority']); ?>"><?php echo $h($c['priority']); ?> priority</span><?php } ?>
              <span class="chip s"><?php echo $h(isset($objectives[$c['objective']]) ? $objectives[$c['objective']] : $c['objective']); ?></span>
              <?php if (!empty($c['ai_agent_id'])) { ?><span class="chip s" title="An AI agent calls this campaign"><i class="fa fa-microphone"></i> AI</span><?php } ?>
            </div>
            <div class="cp-meta">
              <span title="People"><i class="fa fa-users"></i><?php echo $people <= 2 ? $h(implode(', ', $c['people'])) : $h($c['people'][0]) . ' +' . ($people - 1); ?></span>
              <?php if ($dates) { ?><span><i class="fa fa-calendar"></i><?php echo $h($dates); ?></span><?php } ?>
              <?php if ($c['reference_no']) { ?><span><i class="fa fa-hashtag"></i><?php echo $h($c['reference_no']); ?></span><?php } ?>
            </div>
          </div>
          <div>
            <div class="cp-nums">
              <div><b><?php echo $n($s['total_records']); ?></b><span>Records</span></div>
              <div><b><?php echo $n($s['leads_called']); ?></b><span>Reached</span></div>
              <div><b><?php echo $n($s['connected']); ?></b><span>Connected</span></div>
              <div><b><?php echo $n($s['calls_today']); ?><?php if ($target) { ?><small style="font-weight:500;color:var(--cp-mut)">/<?php echo $n($target); ?></small><?php } ?></b><span>Today</span></div>
            </div>
            <div class="cp-prog"><div class="bar<?php echo $s['progress'] >= 100 ? ' g' : ''; ?>"><i style="width:<?php echo (float)$s['progress']; ?>%"></i></div><?php echo (float)$s['progress']; ?>%</div>
          </div>
          <div class="cp-acts">
            <?php if ($st === 'Active') { ?><a class="btn btn-primary btn-sm" href="<?php echo $base; ?>calls?campaign=<?php echo (int)$c['id']; ?>"><i class="fa fa-phone"></i> Dial</a><?php } ?>
            <a class="btn btn-default btn-sm" href="<?php echo $base; ?>campaigns/view/<?php echo (int)$c['id']; ?>"><i class="fa fa-bar-chart"></i> Results</a>
            <?php if ($c['can_manage']) { ?>
            <div class="cp-more">
              <button type="button" class="btn btn-default btn-sm" aria-haspopup="true" aria-label="More actions"><i class="fa fa-ellipsis-h"></i></button>
              <div class="cp-menu" role="menu">
                <a href="<?php echo $base; ?>campaigns/edit/<?php echo (int)$c['id']; ?>"><i class="fa fa-pencil"></i> Edit</a>
                <?php if ($c['status'] === 'Active') { ?><button type="button" data-act="Paused" data-id="<?php echo (int)$c['id']; ?>"><i class="fa fa-pause"></i> Pause</button><?php } ?>
                <?php if ($c['status'] !== 'Active') { ?><button type="button" data-act="Active" data-id="<?php echo (int)$c['id']; ?>"><i class="fa fa-play"></i> <?php echo $c['status'] === 'Completed' ? 'Reopen' : 'Resume'; ?></button><?php } ?>
                <?php if ($c['status'] !== 'Completed') { ?><button type="button" data-act="Completed" data-id="<?php echo (int)$c['id']; ?>"><i class="fa fa-flag-checkered"></i> Mark completed</button><?php } ?>
                <?php if ($can_write) { ?><a href="<?php echo $base; ?>campaigns/new?copy=<?php echo (int)$c['id']; ?>"><i class="fa fa-clone"></i> Duplicate</a><?php } ?>
                <a href="<?php echo $base; ?>campaigns/export/<?php echo (int)$c['id']; ?>"><i class="fa fa-download"></i> Export calls (CSV)</a>
                <?php if (!$s['calls']) { ?><button type="button" class="dg" data-del="<?php echo (int)$c['id']; ?>"><i class="fa fa-trash-o"></i> Delete</button><?php } ?>
              </div>
            </div>
            <?php } ?>
          </div>
        </li>
        <?php } ?>
      </ul>
      <div class="sx-empty" id="cpNone" style="display:none"><i class="fa fa-search"></i>No campaigns match.</div>
      <?php } ?>
    </div>
  </section>
</div>

<script>
$(function () {
  var base = '<?php echo $base; ?>', f = '', rows = $('#cpList .cp-row');
  function apply() {
    var q = $.trim($('#cpSearch').val()).toLowerCase(), shown = 0;
    rows.each(function () {
      var ok = (!f || this.getAttribute('data-st') === f) && (!q || this.getAttribute('data-q').indexOf(q) !== -1);
      this.style.display = ok ? '' : 'none';
      if (ok) shown++;
    });
    $('#cpCount').text(shown + ' of ' + rows.length);
    $('#cpNone').toggle(rows.length > 0 && shown === 0);
  }
  $('#cpFilter').on('click', 'button', function () {
    $('#cpFilter button').removeClass('is-on');
    $(this).addClass('is-on');
    f = $(this).data('f') || '';
    apply();
  });
  $('#cpSearch').on('input', apply);
  apply();

  $(document).on('click', '.cp-more > .btn', function (e) {
    e.stopPropagation();
    var m = $(this).parent();
    $('.cp-more').not(m).removeClass('open');
    m.toggleClass('open');
  });
  $(document).on('click keydown', function (e) {
    if (e.type === 'click' || e.key === 'Escape') $('.cp-more').removeClass('open');
  });
  $(document).on('click', '.cp-menu [data-act]', function () {
    var b = $(this);
    if (b.data('act') === 'Completed' && !confirm('Mark this campaign completed? Nobody can dial it until it is reopened.')) return;
    $.post(base + 'campaigns/ajax_status', { id: b.data('id'), status: b.data('act') }, function (r) {
      if (r && r.status) location.reload(); else alert((r && r.message) || 'Not saved');
    }, 'json');
  });
  $(document).on('click', '.cp-menu [data-del]', function () {
    if (!confirm('Delete this campaign? It has no calls yet.')) return;
    $.post(base + 'campaigns/ajax_delete', { id: $(this).data('del') }, function (r) {
      if (r && r.status) location.reload(); else alert((r && r.message) || 'Not deleted');
    }, 'json');
  });
});
</script>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
