<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
$h = function ($x) { return htmlspecialchars((string)$x, ENT_QUOTES, 'UTF-8'); };
$n = function ($v) { return number_format((int)$v); };
$sel = function ($x, $y) { return (string)$x === (string)$y ? ' selected' : ''; };
$chk = function ($on) { return $on ? ' checked' : ''; };
$base = base_url() . 'manager/';
$src = array('meta' => 'Meta', 'google' => 'Google Ads', 'web' => 'Website', 'manual' => 'Added here', 'other' => 'Other');
$pfi = array('meta' => 'fa-facebook-square', 'google' => 'fa-google', 'web' => 'fa-globe', 'other' => 'fa-plug');
$stc = array('Created' => 'oc-hot', 'Duplicate' => 'oc-mid', 'Error' => 'oc-bad', 'New' => 'oc-mid');
$stl = array('Created' => 'New record', 'Duplicate' => 'Already in CRM', 'Error' => 'Not saved', 'New' => 'New');
$pst = array('Draft' => 'st-Draft', 'Scheduled' => 'st-Scheduled', 'Posted' => 'st-Active', 'Failed' => 'oc-bad', 'Posting' => 'st-Scheduled', 'Review' => 'oc-mid');
$uname = array();
foreach ($users as $u) $uname[(int)$u['id']] = $u['name'];
$connected = $m['exists'] && $has_token && $m['page_id'] !== '';
$dis = $can_write ? '' : ' disabled';
$is_meta = $e['platform'] === 'meta';
$e_rr = array_map('intval', (array)$e['rr_users']);
$e_ag = array_map('intval', (array)$e['agents']);
$e_tok = $e['token'] !== '';
$acc_q = $m['exists'] ? '?acc=' . (int)$m['id'] : '';
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-campaigns.css?v=20261107_9">
<div class="content-wrapper sx cp mk">
  <section class="sx-head">
    <h1>Marketing<small>Leads from all your Meta, Google Ads, website and other accounts in one place; posts, Meta Ads and the Claude autopilot</small></h1>
    <div class="sx-btns">
      <?php if (count($metas) > 1) { ?>
        <label class="mk-acc"><span>Meta account</span>
          <select id="accSel" class="form-control" aria-label="Meta account for Posts, Meta Ads and Autopilot">
            <?php foreach ($metas as $x) { ?><option value="<?php echo (int)$x['id']; ?>"<?php echo $sel($x['id'], $m['id']); ?>><?php echo $h($x['name'] . ($x['enabled'] ? '' : ' (off)')); ?></option><?php } ?>
          </select></label>
      <?php } ?>
      <?php if ($can_write && $metas) { ?><button type="button" class="btn btn-default" id="btnSync"><i class="fa fa-refresh"></i> Get new leads</button><?php } ?>
      <?php if ($can_write) { ?><button type="button" class="btn btn-primary" id="btnAddLead"><i class="fa fa-plus"></i> Add lead</button><?php } ?>
    </div>
  </section>

  <section class="content">
    <?php if (!$conns) { ?>
      <div class="ai-off"><i class="fa fa-plug"></i><span><b>No accounts yet.</b> Add your Meta pages, Google Ads, website forms or any other lead source in Connections. Each account has its own Source Name and Ref for CRM reports.</span>
        <button type="button" class="btn btn-default btn-sm" data-go="conn">Open Connections</button></div>
    <?php } ?>

    <div class="sx-kpis">
      <div class="sx-kpi"><span class="ic"><i class="fa fa-inbox"></i></span><div><b><?php echo $n($stats['today']); ?></b><span>Leads today</span></div></div>
      <div class="sx-kpi"><span class="ic"><i class="fa fa-calendar"></i></span><div><b><?php echo $n($stats['month']); ?></b><span>Leads this month</span></div></div>
      <div class="sx-kpi g"><span class="ic"><i class="fa fa-user-plus"></i></span><div><b><?php echo $n($stats['created']); ?></b><span>New CRM records this month</span></div></div>
      <div class="sx-kpi a"><span class="ic"><i class="fa fa-clone"></i></span><div><b><?php echo $n($stats['dup']); ?></b><span>Already in the CRM</span></div></div>
    </div>

    <div class="sx-card">
      <nav class="ai-tabs" role="tablist">
        <button type="button" role="tab" data-tab="leads" class="is-on">Leads <span class="n"><?php echo count($leads); ?></span></button>
        <button type="button" role="tab" data-tab="report">Report</button>
        <button type="button" role="tab" data-tab="posts">Posts</button>
        <button type="button" role="tab" data-tab="ads">Meta Ads</button>
        <button type="button" role="tab" data-tab="auto">Autopilot</button>
        <button type="button" role="tab" data-tab="conn">Connections <span class="n"><?php echo count($conns); ?></span></button>
      </nav>

      <!-- Leads (all accounts) -->
      <div class="ai-pane" data-pane="leads">
        <div class="ai-bar">
          <input type="search" id="lSearch" class="form-control" placeholder="Search name, phone, campaign, ref" aria-label="Search leads">
          <select id="lConn" class="form-control" aria-label="Account"><option value="">All accounts</option><?php foreach ($conns as $x) { ?><option value="<?php echo (int)$x['id']; ?>"><?php echo $h($x['name']); ?></option><?php } ?><option value="0">Added here</option></select>
          <select id="lOwner" class="form-control" aria-label="Owner"><option value="">All owners</option><?php foreach ($users as $u) { ?><option value="<?php echo (int)$u['id']; ?>"><?php echo $h($u['name']); ?></option><?php } ?></select>
          <select id="lSt" class="form-control" aria-label="Result"><option value="">All results</option><?php foreach ($stl as $k => $v) if ($k !== 'New') { ?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php } ?></select>
        </div>
        <?php if ($can_write && $leads) { ?>
        <div class="mk-bulk">
          <label class="mk-all"><input type="checkbox" id="lAll"> <span>Select shown</span></label>
          <span id="lPicked" class="hint">0 selected</span>
          <button type="button" class="btn btn-default btn-sm" id="btnAssign" disabled><i class="fa fa-users"></i> Assign</button>
          <span class="hint">Give new records to one owner plus co-workers who can work on them too.</span>
        </div>
        <?php } ?>
        <?php if (!$leads) { ?>
          <div class="sx-empty"><i class="fa fa-inbox"></i>No marketing leads yet. They appear here as soon as a form is filled on any connected account.</div>
        <?php } else { ?>
        <ul class="ai-calls" id="leadList">
          <?php foreach ($leads as $r) {
            $other = json_decode((string)$r['fields_json'], true) ?: array();
            $co = array();
            foreach (array_filter(array_map('intval', explode(',', (string)$r['agents']))) as $a) if ($a !== (int)$r['owner_id']) $co[] = $uname[$a] ?? ('User ' . $a);
            $acc = (int)$r['connection_id'] ? ($r['conn_name'] !== null ? $r['conn_name'] : 'Removed account') : '';
          ?>
          <li class="ai-call mk-lead" data-id="<?php echo (int)$r['id']; ?>" data-conn="<?php echo (int)$r['connection_id']; ?>" data-owner="<?php echo (int)$r['owner_id']; ?>" data-st="<?php echo $h($r['status']); ?>" data-q="<?php echo $h(strtolower($r['full_name'] . ' ' . $r['company'] . ' ' . $r['phone'] . ' ' . $r['email'] . ' ' . $r['campaign_name'] . ' ' . $r['ad_name'] . ' ' . $r['form_name'] . ' ' . $r['batch'] . ' ' . $r['ref'] . ' ' . $acc)); ?>">
            <div class="who<?php echo $can_write ? ' has-pick' : ''; ?>">
              <?php if ($can_write && $r['status'] === 'Created' && $r['lead_row_id']) { ?><input type="checkbox" class="lPick mk-pick" value="<?php echo (int)$r['id']; ?>" aria-label="Select"><?php } ?>
              <b><?php echo $h($r['company'] !== '' ? $r['company'] : ($r['full_name'] !== '' ? $r['full_name'] : ($r['phone'] !== '' ? $r['phone'] : ($src[$r['source']] ?? 'Marketing') . ' lead ' . $r['external_id']))); ?></b>
              <?php if ($r['company'] !== '' && $r['full_name'] !== '') { ?><span><?php echo $h($r['full_name']); ?></span><?php } ?>
            </div>
            <div>
              <span class="chip <?php echo $stc[$r['status']] ?? 'oc-mid'; ?>"><?php echo $h($stl[$r['status']] ?? $r['status']); ?></span>
              <?php if ($acc !== '') { ?><span class="chip s"><i class="fa <?php echo $pfi[$r['conn_platform']] ?? 'fa-plug'; ?>"></i> <?php echo $h($acc); ?></span><?php } ?>
              <span class="chip s"><?php echo $h(($src[$r['source']] ?? $r['source']) . ($r['platform'] !== '' && $r['source'] === 'meta' ? ' · ' . ($r['platform'] === 'ig' ? 'Instagram' : ($r['platform'] === 'fb' ? 'Facebook' : $r['platform'])) : '')); ?></span>
              <?php if ($r['error'] !== '') { ?><div class="sum"><?php echo $h($r['error']); ?></div><?php } ?>
              <?php if ($other) { ?><div class="sum"><?php echo $h(implode(' · ', array_map(function ($k, $v) { return ucfirst(str_replace('_', ' ', $k)) . ': ' . (is_array($v) ? implode(', ', $v) : $v); }, array_keys($other), $other))); ?></div><?php } ?>
              <div class="meta">
                <span><i class="fa fa-clock-o"></i> <?php echo $h(date('d M H:i', strtotime($r['received_at']))); ?></span>
                <?php if ($r['phone'] !== '') { ?><span><i class="fa fa-phone"></i> <?php echo $h($r['phone']); ?></span><?php } ?>
                <?php if ($r['email'] !== '') { ?><span><i class="fa fa-envelope-o"></i> <?php echo $h($r['email']); ?></span><?php } ?>
                <?php if ($r['campaign_name'] !== '') { ?><span><i class="fa fa-bullhorn"></i> <?php echo $h($r['campaign_name']); ?></span><?php } ?>
                <?php if ($r['form_name'] !== '') { ?><span><i class="fa fa-wpforms"></i> <?php echo $h($r['form_name']); ?></span><?php } ?>
                <?php if ($r['crm_source']) { ?><span title="Source Name"><i class="fa fa-tag"></i> <?php echo $h($r['crm_source']); ?></span><?php } ?>
                <?php if ($r['batch']) { ?><span title="Ref (Month)"><i class="fa fa-hashtag"></i> <?php echo $h($r['batch']); ?></span><?php } ?>
                <?php if ($r['owner_name']) { ?><span title="Owner"><i class="fa fa-user"></i> <?php echo $h($r['owner_name']); ?></span><?php } ?>
                <?php if ($co) { ?><span title="Co-workers"><i class="fa fa-users"></i> <?php echo $h(implode(', ', $co)); ?></span><?php } ?>
              </div>
            </div>
            <div class="cp-acts">
              <?php if ($r['lead_row_id']) { ?><a class="btn btn-default btn-sm" href="<?php echo $base; ?>leads/view/<?php echo base64_encode((string)(int)$r['lead_row_id']); ?>"><i class="fa fa-external-link"></i> Record</a>
              <a class="btn btn-default btn-sm" href="<?php echo $base; ?>calls?lead=<?php echo (int)$r['lead_row_id']; ?>"><i class="fa fa-phone"></i> Call</a><?php } ?>
            </div>
          </li>
          <?php } ?>
        </ul>
        <div class="sx-empty" id="leadNone" style="display:none"><i class="fa fa-search"></i>No leads match.</div>
        <?php } ?>
      </div>

      <!-- Report -->
      <div class="ai-pane" data-pane="report" hidden>
        <form class="ai-bar" id="repForm" onsubmit="return false">
          <label class="mk-lbl">From <input type="date" name="from" class="form-control" value="<?php echo date('Y-m-01'); ?>"></label>
          <label class="mk-lbl">To <input type="date" name="to" class="form-control" value="<?php echo date('Y-m-d'); ?>"></label>
          <select name="group" class="form-control" aria-label="Group by"><option value="owner">By owner</option><option value="account">By account</option><option value="source">By Source Name</option></select>
          <select name="account" class="form-control" aria-label="Account"><option value="0">All accounts</option><?php foreach ($conns as $x) { ?><option value="<?php echo (int)$x['id']; ?>"><?php echo $h($x['name']); ?></option><?php } ?><option value="-1">Added here</option></select>
          <button type="button" class="btn btn-default" id="btnRep"><i class="fa fa-refresh"></i> Show</button>
          <a class="btn btn-default" id="btnRepCsv" href="#"><i class="fa fa-download"></i> CSV</a>
        </form>
        <div id="repBody"><div class="sx-empty"><i class="fa fa-spinner fa-spin"></i>Loading…</div></div>
        <p class="hint mk-pad">Owner is the person who owns the CRM record now (after any assigning). "Co-worker on" counts records where the person was added as a co-worker. Stages and orders are read live from the CRM.</p>
      </div>

      <!-- Posts -->
      <div class="ai-pane" data-pane="posts" hidden>
        <?php if (!$m['exists']) { ?>
          <div class="sx-empty"><i class="fa fa-facebook-square"></i>Add a Meta account in Connections to post on Facebook and Instagram.<br><button type="button" class="btn btn-default" style="margin-top:12px" data-go="conn">Open Connections</button></div>
        <?php } else { ?>
        <div class="mk-rule"><i class="fa fa-facebook-square"></i>Account: <b><?php echo $h($m['name']); ?></b><?php echo $m['page_name'] !== '' ? ' · ' . $h($m['page_name']) : ''; ?><?php if ($m['ap_posts'] !== 'off') { ?> · Claude autopilot <?php echo $m['ap_posts'] === 'auto' ? 'schedules' : 'writes for your approval'; ?> <?php echo (int)$m['ap_per_week']; ?> posts a week<?php } ?></div>
        <div class="mk-grid">
          <form id="postForm" class="mk-compose" autocomplete="off" novalidate>
            <input type="hidden" name="id" value="0">
            <h4>New post</h4>
            <div class="pills" style="margin-bottom:10px">
              <label><input type="checkbox" name="channels[]" value="fb" checked<?php echo $dis; ?>><span><i class="fa fa-facebook-official"></i> Facebook<?php echo $m['page_name'] !== '' ? ' · ' . $h($m['page_name']) : ''; ?></span></label>
              <label><input type="checkbox" name="channels[]" value="ig"<?php echo $m['ig_id'] === '' ? ' disabled' : $dis; ?>><span><i class="fa fa-instagram"></i> Instagram<?php echo $m['ig_name'] !== '' ? ' · @' . $h($m['ig_name']) : ($m['ig_id'] === '' ? ' (not linked)' : ''); ?></span></label>
            </div>
            <div class="mk-ai mk-reuse">
              <select id="pReuse" class="form-control" aria-label="Earlier posts and ads"><option value="">Use an earlier post or ad…</option></select>
            </div>
            <input type="hidden" name="image_ref" value="">
            <div class="mk-ai">
              <input type="text" id="pTopic" class="form-control" maxlength="1500" placeholder="What is the post about? e.g. Free PRO services consultation this week">
              <select id="pLang" class="form-control" aria-label="Language"><option value="en">English</option><option value="ar">Arabic</option><option value="both">Both</option></select>
              <button type="button" class="btn btn-default" id="btnWritePost"<?php echo $has_claude ? '' : ' disabled title="Add the Claude API key in Connections"'; ?>><i class="fa fa-magic"></i> Write with Claude</button>
            </div>
            <div class="form-group">
              <label for="pMsg">Text</label>
              <textarea id="pMsg" name="message" class="form-control" rows="7" maxlength="5000"></textarea>
              <span class="hint" id="pCount">0 characters</span>
            </div>
            <div class="f2">
              <div class="form-group">
                <label for="pImg">Image link</label>
                <input type="url" id="pImg" name="image_url" class="form-control" maxlength="500" placeholder="https://… .jpg (needed for Instagram)">
              </div>
              <div class="form-group">
                <label for="pLink">Website link</label>
                <input type="url" id="pLink" name="link" class="form-control" maxlength="500" placeholder="Optional" value="">
              </div>
              <div class="form-group">
                <label for="pWhen">Schedule for</label>
                <input type="datetime-local" id="pWhen" name="scheduled_at" class="form-control">
              </div>
              <div class="form-group mk-prev" id="pPrev" hidden><img alt="Image preview"></div>
            </div>
            <?php if ($can_write) { ?>
            <div class="mk-btns">
              <button type="button" class="btn btn-primary" data-act="now"<?php echo $connected ? '' : ' disabled title="Connect Meta first"'; ?>><i class="fa fa-paper-plane"></i> Post now</button>
              <button type="button" class="btn btn-default" data-act="schedule"<?php echo $connected ? '' : ' disabled title="Connect Meta first"'; ?>><i class="fa fa-clock-o"></i> Schedule</button>
              <button type="button" class="btn btn-default" data-act="draft">Save draft</button>
              <button type="button" class="btn btn-link" id="btnPostReset" hidden>New post</button>
              <span class="sx-help" id="postMsg" role="status"></span>
            </div>
            <?php } ?>
          </form>
          <div>
            <h4 class="mk-h">Posts</h4>
            <?php if (!$posts) { ?><div class="sx-empty"><i class="fa fa-pencil-square-o"></i>No posts yet.</div><?php } else { ?>
            <ul class="ai-calls" id="postList">
              <?php foreach ($posts as $p) { ?>
              <li class="ai-call mk-post" data-post='<?php echo $h(json_encode(array('id' => (int)$p['id'], 'channels' => explode(',', $p['channels']), 'message' => $p['message'], 'image_url' => $p['image_url'], 'image_ref' => isset($p['image_ref']) ? $p['image_ref'] : '', 'link' => $p['link'], 'scheduled_at' => $p['scheduled_at'] ? date('Y-m-d\TH:i', strtotime($p['scheduled_at'])) : ''))); ?>'>
                <?php if ($p['image_url'] !== '') { ?><img class="mk-thumb" src="<?php echo $h($p['image_url']); ?>" alt="" loading="lazy" referrerpolicy="no-referrer"><?php } else { ?><span class="mk-thumb"><i class="fa fa-align-left"></i></span><?php } ?>
                <div>
                  <span class="chip <?php echo $pst[$p['status']] ?? 'st-Draft'; ?>"><?php echo $h($p['status'] === 'Review' ? 'Waiting for approval' : $p['status']); ?></span>
                  <?php foreach (explode(',', $p['channels']) as $chn) { ?><span class="chip s"><?php echo $chn === 'ig' ? 'Instagram' : 'Facebook'; ?></span><?php } ?>
                  <?php if (!empty($p['auto'])) { ?><span class="chip s"><i class="fa fa-magic"></i> Autopilot</span><?php } ?>
                  <div class="sum"><?php echo $h(mb_strimwidth((string)$p['message'], 0, 160, '…')); ?></div>
                  <?php if ($p['error'] !== '') { ?><div class="sum mk-err"><?php echo $h($p['error']); ?></div><?php } ?>
                  <div class="meta"><span><i class="fa fa-clock-o"></i> <?php echo $h($p['status'] === 'Posted' ? 'Posted ' . date('d M H:i', strtotime($p['posted_at'])) : (in_array($p['status'], array('Scheduled', 'Review'), true) && $p['scheduled_at'] ? 'For ' . date('d M H:i', strtotime($p['scheduled_at'])) : 'Saved ' . date('d M H:i', strtotime($p['created_at'])))); ?></span><?php if ($p['by_name']) { ?><span><i class="fa fa-user"></i> <?php echo $h($p['by_name']); ?></span><?php } ?>
                    <?php if ($p['fb_post_id'] !== '') { ?><a href="https://www.facebook.com/<?php echo $h($p['fb_post_id']); ?>" target="_blank" rel="noopener">Open on Facebook</a><?php } ?></div>
                </div>
                <?php if ($can_write && !in_array($p['status'], array('Posted', 'Posting'), true)) { ?>
                <div class="cp-acts">
                  <?php if ($p['status'] === 'Review') { ?><button type="button" class="btn btn-primary btn-sm" data-approve-post="<?php echo (int)$p['id']; ?>"><i class="fa fa-check"></i> Approve</button><?php } ?>
                  <button type="button" class="btn btn-default btn-sm" data-edit-post><i class="fa fa-pencil"></i> Edit</button>
                  <button type="button" class="btn btn-default btn-sm" data-del-post="<?php echo (int)$p['id']; ?>" aria-label="Delete"><i class="fa fa-trash-o"></i></button>
                </div>
                <?php } ?>
              </li>
              <?php } ?>
            </ul>
            <?php } ?>
          </div>
        </div>
        <?php } ?>
      </div>

      <!-- Meta Ads -->
      <div class="ai-pane" data-pane="ads" hidden>
        <?php if (!$has_token || $m['ad_account_id'] === '') { ?>
          <div class="sx-empty"><i class="fa fa-bullhorn"></i>Connect a Meta account and choose its ad account in Connections to see and run your ads here.<br><button type="button" class="btn btn-default" style="margin-top:12px" data-go="conn">Open Connections</button></div>
        <?php } else { ?>
        <div class="ai-bar">
          <select id="adPreset" class="form-control" aria-label="Period">
            <option value="today">Today</option><option value="yesterday">Yesterday</option><option value="last_7d" selected>Last 7 days</option><option value="last_30d">Last 30 days</option><option value="this_month">This month</option>
          </select>
          <button type="button" class="btn btn-default" id="btnAdsLoad"><i class="fa fa-refresh"></i> Refresh</button>
          <button type="button" class="btn btn-default" id="btnAdsReview"<?php echo $has_claude ? '' : ' disabled title="Add the Claude API key in Connections"'; ?>><i class="fa fa-magic"></i> Review with Claude</button>
          <?php if ($can_write) { ?><button type="button" class="btn btn-primary" id="btnNewCamp" style="margin-left:auto"><i class="fa fa-plus"></i> New lead campaign</button><?php } ?>
        </div>
        <div class="mk-rule"><i class="fa fa-shield"></i>
          <span><b><?php echo $h($m['name']); ?></b> · act_<?php echo $h($m['ad_account_id']); ?>.
          <?php if (!empty($m['auto_pause'])) { ?>Auto-pause is on: running campaigns that cost more than <b><?php echo $h($m['currency'] . ' ' . number_format((float)$m['max_cpl'], 2)); ?></b> a lead (last 7 days) are paused every hour.
          <?php } else { ?>Auto-pause is off.<?php } ?>
          <?php if ($m['ap_ads'] !== 'off') { ?> Claude autopilot <?php echo $m['ap_ads'] === 'auto' ? 'adjusts' : 'suggests changes to'; ?> campaigns once a day.<?php } ?></span>
        </div>
        <?php if ($m['ap_last_report'] !== '') { ?><div class="mk-review"><b><i class="fa fa-magic"></i> Autopilot, last review</b><div class="tx"><?php echo $h($m['ap_last_report']); ?></div></div><?php } ?>
        <div id="aiReview" class="mk-review" hidden></div>
        <div id="adsBody"><div class="sx-empty"><i class="fa fa-spinner fa-spin"></i>Loading campaigns…</div></div>
        <?php } ?>
      </div>

      <!-- Autopilot -->
      <div class="ai-pane" data-pane="auto" hidden>
        <?php if (!$m['exists']) { ?>
          <div class="sx-empty"><i class="fa fa-magic"></i>Add a Meta account in Connections first. The autopilot works per Meta account.<br><button type="button" class="btn btn-default" style="margin-top:12px" data-go="conn">Open Connections</button></div>
        <?php } else { $apc = (array)$m['ap_channels']; ?>
        <form id="apForm" class="mk-conn" autocomplete="off" novalidate>
          <input type="hidden" name="conn" value="<?php echo (int)$m['id']; ?>">
          <div>
            <?php if (!$has_claude) { ?><div class="ai-off" style="margin:0 0 14px"><i class="fa fa-key"></i><span>Add the Claude API key in Connections to switch the autopilot on.</span></div><?php } ?>
            <div class="sx-card">
              <div class="sx-card-h"><h3><i class="fa fa-pencil-square-o"></i> Posts on Facebook and Instagram · <?php echo $h($m['name']); ?></h3></div>
              <div class="sx-card-b">
                <div class="seg" style="margin-bottom:12px">
                  <label><input type="radio" name="ap_posts" value="off"<?php echo $chk($m['ap_posts'] === 'off'); echo $dis; ?>><span>Off</span></label>
                  <label><input type="radio" name="ap_posts" value="review"<?php echo $chk($m['ap_posts'] === 'review'); echo $dis; ?>><span>Claude writes, I approve</span></label>
                  <label><input type="radio" name="ap_posts" value="auto"<?php echo $chk($m['ap_posts'] === 'auto'); echo $dis; ?>><span>Fully automatic</span></label>
                </div>
                <div class="f2">
                  <div class="form-group"><label for="apPer">Posts a week</label><input type="number" id="apPer" name="ap_per_week" class="form-control" min="1" max="7" value="<?php echo (int)$m['ap_per_week']; ?>"<?php echo $dis; ?>></div>
                  <div class="form-group"><label for="apTime">Time of day</label><input type="time" id="apTime" name="ap_time" class="form-control" value="<?php echo $h($m['ap_time']); ?>"<?php echo $dis; ?>></div>
                  <div class="form-group"><label>Where</label>
                    <div class="pills"><label><input type="checkbox" name="ap_channels[]" value="fb"<?php echo $chk(in_array('fb', $apc, true)); echo $dis; ?>><span>Facebook</span></label>
                    <label><input type="checkbox" name="ap_channels[]" value="ig"<?php echo $chk(in_array('ig', $apc, true)); echo $m['ig_id'] === '' ? ' disabled' : $dis; ?>><span>Instagram<?php echo $m['ig_id'] === '' ? ' (not linked)' : ' · @' . $h($m['ig_name']); ?></span></label></div></div>
                  <div class="form-group"><label for="apLang">Language</label><select id="apLang" name="ap_lang" class="form-control"<?php echo $dis; ?>><option value="en"<?php echo $sel($m['ap_lang'], 'en'); ?>>English</option><option value="ar"<?php echo $sel($m['ap_lang'], 'ar'); ?>>Arabic</option><option value="both"<?php echo $sel($m['ap_lang'], 'both'); ?>>Both</option></select></div>
                  <div class="form-group wide"><label for="apTopics">Topics, one a line</label><textarea id="apTopics" name="ap_topics" class="form-control" rows="4" maxlength="3000" placeholder="Mainland company setup in 7 days&#10;Golden visa for investors&#10;Corporate tax registration before the deadline"<?php echo $dis; ?>><?php echo $h($m['ap_topics']); ?></textarea></div>
                  <div class="form-group wide"><label for="apImgs">Image links, one a line (used in turn)</label><textarea id="apImgs" name="ap_images" class="form-control" rows="3" maxlength="5000" placeholder="https://…/post-1.jpg"<?php echo $dis; ?>><?php echo $h($m['ap_images']); ?></textarea><span class="hint">Instagram posts need an image. Without images the autopilot posts on Facebook only.</span></div>
                  <div class="form-group wide"><label class="tog"><input type="checkbox" name="ap_reuse" value="1"<?php echo $chk(!empty($m['ap_reuse'])); echo $dis; ?>><span>Reuse earlier creatives<small>Pictures of earlier page posts and ads are used in turn with the links above, and Claude follows the voice of those posts without repeating them.</small></span></label></div>
                </div>
                <p class="hint" style="margin:0">Once a day Claude fills the next 7 days with posts (one a day at most) using the company details from the Claude settings, and avoids repeating recent posts. "I approve" puts them under Posts with an Approve button.</p>
              </div>
            </div>
            <div class="sx-card">
              <div class="sx-card-h"><h3><i class="fa fa-line-chart"></i> Meta Ads campaigns</h3></div>
              <div class="sx-card-b">
                <div class="seg" style="margin-bottom:12px">
                  <label><input type="radio" name="ap_ads" value="off"<?php echo $chk($m['ap_ads'] === 'off'); echo $dis; ?>><span>Off</span></label>
                  <label><input type="radio" name="ap_ads" value="suggest"<?php echo $chk($m['ap_ads'] === 'suggest'); echo $dis; ?>><span>Suggest only</span></label>
                  <label><input type="radio" name="ap_ads" value="auto"<?php echo $chk($m['ap_ads'] === 'auto'); echo $dis; ?>><span>Make the changes</span></label>
                </div>
                <div class="f2">
                  <div class="form-group"><label for="apMax">Most all running campaigns may spend a day (<?php echo $h($m['currency']); ?>)</label><input type="number" id="apMax" name="ap_max_daily" class="form-control" min="0" step="1" value="<?php echo (float)$m['ap_max_daily'] ?: ''; ?>"<?php echo $dis; ?>></div>
                  <div class="form-group"><label for="apStep">Biggest budget change a day (%)</label><input type="number" id="apStep" name="ap_step" class="form-control" min="5" max="50" value="<?php echo (int)$m['ap_step']; ?>"<?php echo $dis; ?>></div>
                  <div class="form-group"><label for="apMin">Judge a campaign after it spent (<?php echo $h($m['currency']); ?>, 7 days)</label><input type="number" id="apMin" name="ap_min_spend" class="form-control" min="0" step="1" value="<?php echo (float)$m['ap_min_spend'] ?: ''; ?>"<?php echo $dis; ?>></div>
                </div>
                <p class="hint" style="margin:0">Once a day Claude reads the last 7 days of running campaigns and pauses the ones wasting money or moves budgets towards the ones with the best cost per lead. Limits the CRM enforces: it never starts a paused campaign or makes new ones, each budget moves at most the % above, and raises stop at the daily total. The cost-per-lead target is the one in Connections. Every change is written in Recent changes.</p>
              </div>
              <?php if ($can_write) { ?>
              <div class="sx-card-f">
                <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Save</button>
                <button type="button" class="btn btn-default" data-ap-run="posts"<?php echo $has_claude ? '' : ' disabled'; ?>><i class="fa fa-pencil"></i> Plan posts now</button>
                <button type="button" class="btn btn-default" data-ap-run="ads"<?php echo $has_claude && $m['ad_account_id'] !== '' ? '' : ' disabled'; ?>><i class="fa fa-line-chart"></i> Review ads now</button>
                <span class="sx-help" id="apMsg" role="status"></span>
              </div>
              <?php } ?>
            </div>
          </div>
          <aside>
            <div class="sx-card">
              <div class="sx-card-h"><h3><i class="fa fa-history"></i> Autopilot</h3></div>
              <div class="sx-card-b" style="display:block">
                <p class="hint" style="margin:0 0 6px">Posts last planned: <b><?php echo $m['ap_last_plan'] !== '' ? $h(date('d M H:i', strtotime($m['ap_last_plan']))) : 'never'; ?></b><br>Ads last reviewed: <b><?php echo $m['ap_last_ads'] !== '' ? $h(date('d M H:i', strtotime($m['ap_last_ads']))) : 'never'; ?></b></p>
                <?php if ($m['ap_last_report'] !== '') { ?><div class="mk-log" style="white-space:pre-wrap"><?php echo $h($m['ap_last_report']); ?></div><?php } ?>
                <p class="hint" style="margin:8px 0 0">Runs from the cron job (link in Connections), so the cron job must be set up on the server.</p>
              </div>
            </div>
          </aside>
        </form>
        <?php } ?>
      </div>

      <!-- Connections -->
      <div class="ai-pane" data-pane="conn" hidden>
        <div class="mk-accs">
          <div class="mk-accs-h">
            <h4 class="mk-h" style="padding:0;margin:0">Accounts</h4>
            <?php if ($can_write) { ?><div class="mk-add">
              <?php foreach ($platforms as $k => $v) { ?><a class="btn btn-default btn-sm" href="<?php echo $base; ?>system/marketing?edit=new&amp;platform=<?php echo $k; ?>#conn"><i class="fa <?php echo $pfi[$k]; ?>"></i> Add <?php echo $h(preg_replace('/ \(.*$/', '', $v)); ?></a><?php } ?>
            </div><?php } ?>
          </div>
          <?php if ($conns) { ?>
          <div class="sx-table-wrap"><table class="sx-table">
            <thead><tr><th>Account</th><th>Platform</th><th>Source Name</th><th>Ref (Month)</th><th>Who gets leads</th><th class="num">Leads (month / all)</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($conns as $x) { $ct = $counts[$x['id']] ?? array('n' => 0, 'month' => 0); ?>
              <tr<?php echo $x['id'] === $e['id'] ? ' class="is-on"' : ''; ?>>
                <td data-l="Account"><b><?php echo $h($x['name']); ?></b> <?php echo $x['enabled'] ? '' : '<span class="chip st-Draft">Off</span>'; ?><?php if ($x['platform'] === 'meta' && $x['page_name'] !== '') { ?><br><small class="hint"><?php echo $h($x['page_name']); ?></small><?php } ?></td>
                <td data-l="Platform"><i class="fa <?php echo $pfi[$x['platform']] ?? 'fa-plug'; ?>"></i> <?php echo $h($platforms[$x['platform']] ?? $x['platform']); ?></td>
                <td data-l="Source Name"><?php echo $h($x['source_name']); ?></td>
                <td data-l="Ref (Month)"><?php echo $h($x['ref_month'] !== '' ? $x['ref_month'] : 'Auto: ' . date('M-y') . '/' . $x['source_name']); ?></td>
                <td data-l="Who gets leads"><?php
                  if ($x['route'] === 'round') { $nm = array(); foreach ((array)$x['rr_users'] as $u) $nm[] = $uname[(int)$u] ?? ('User ' . (int)$u); echo 'Turns: ' . $h(implode(', ', $nm)); }
                  else echo $h($uname[(int)$x['owner_id']] ?? '–');
                  if ($x['agents']) { $nm = array(); foreach ((array)$x['agents'] as $u) $nm[] = $uname[(int)$u] ?? ('User ' . (int)$u); echo '<br><small class="hint">Co-workers: ' . $h(implode(', ', $nm)) . '</small>'; } ?></td>
                <td data-l="Leads" class="num"><?php echo $n($ct['month']); ?> / <?php echo $n($ct['n']); ?></td>
                <td><a class="btn btn-default btn-sm" href="<?php echo $base; ?>system/marketing?edit=<?php echo (int)$x['id']; ?>#conn"><?php echo $can_write ? 'Edit' : 'Open'; ?></a></td>
              </tr>
            <?php } ?>
            </tbody>
          </table></div>
          <?php } else { ?><p class="hint" style="margin:6px 0 0">No accounts yet. Add one with the buttons above.</p><?php } ?>
        </div>

        <div class="mk-conn">
          <div>
            <form id="connForm" autocomplete="off" novalidate>
              <input type="hidden" name="conn" value="<?php echo (int)$e['id']; ?>">
              <input type="hidden" name="platform" value="<?php echo $h($e['platform']); ?>">
              <div class="sx-card">
                <div class="sx-card-h"><h3><i class="fa <?php echo $pfi[$e['platform']] ?? 'fa-plug'; ?>"></i> <?php echo $e['id'] ? $h($e['name']) : 'New ' . $h($platforms[$e['platform']]) . ' account'; ?><?php if ($e['id'] && $is_meta && $e_tok && $e['page_id'] !== '') { ?> <span class="chip st-Active">Connected</span><?php } ?></h3></div>
                <div class="sx-card-b">
                  <div class="f2">
                    <div class="form-group">
                      <label for="eName">Account name</label>
                      <input type="text" id="eName" name="name" class="form-control" maxlength="100" value="<?php echo $h($e['name']); ?>" placeholder="e.g. Meta – Company setup page"<?php echo $dis; ?>>
                    </div>
                    <div class="form-group">
                      <label class="tog"><input type="checkbox" name="enabled" value="1"<?php echo $chk($e['enabled']); echo $dis; ?>><span>Account on<small>Off: no leads taken in, no posts or autopilot.</small></span></label>
                    </div>
                    <div class="form-group">
                      <label for="eSrc">Source Name (CRM reports)</label>
                      <input type="text" id="eSrc" name="source_name" class="form-control" maxlength="150" list="srcTerms" value="<?php echo $h($e['source_name']); ?>" placeholder="e.g. Meta Ads – FB1"<?php echo $dis; ?>>
                      <datalist id="srcTerms"><?php foreach ($source_terms as $t) { ?><option value="<?php echo $h($t); ?>"><?php } ?></datalist>
                      <span class="hint">Saved as the record's Data Source and added to the CRM's Data Source list.</span>
                    </div>
                    <div class="form-group">
                      <label for="eRef">Ref (Month)</label>
                      <input type="text" id="eRef" name="ref_month" class="form-control" maxlength="100" value="<?php echo $h($e['ref_month']); ?>" placeholder="Empty: <?php echo date('M-y'); ?>/Source Name"<?php echo $dis; ?>>
                      <span class="hint">Saved as the record's Batch / Referral No. Empty uses the month the lead arrives, like the CRM does. {month} becomes <?php echo date('M-y'); ?>.</span>
                    </div>
                  </div>
                </div>
              </div>

              <?php if ($is_meta) { ?>
              <div class="sx-card">
                <div class="sx-card-h"><h3><i class="fa fa-facebook-square"></i> Meta (Facebook, Instagram, Ads)</h3></div>
                <div class="sx-card-b">
                  <div class="f2">
                    <div class="form-group wide">
                      <label for="mToken">Access token</label>
                      <div class="mk-pw"><input type="password" id="mToken" name="token" class="form-control" autocomplete="new-password" placeholder="<?php echo $e_tok ? 'Saved. Leave empty to keep it' : 'System user token from Meta Business settings'; ?>"<?php echo $dis; ?>></div>
                      <span class="hint">Business settings › Users › System users › Generate token, with pages_manage_posts, pages_read_engagement, pages_manage_metadata, leads_retrieval, ads_management, instagram_basic and instagram_content_publish. Stored in the CRM database only.</span>
                    </div>
                    <div class="form-group">
                      <label for="mSecret">App secret</label>
                      <div class="mk-pw"><input type="password" id="mSecret" name="app_secret" class="form-control" autocomplete="new-password" placeholder="<?php echo $e['app_secret'] !== '' ? 'Saved. Leave empty to keep it' : 'App settings › Basic'; ?>"<?php echo $dis; ?>></div>
                      <span class="hint">Checks that lead notifications really come from Meta.</span>
                    </div>
                    <div class="form-group">
                      <label for="mApp">App ID</label>
                      <input type="text" id="mApp" name="app_id" class="form-control" inputmode="numeric" maxlength="30" value="<?php echo $h($e['app_id']); ?>"<?php echo $dis; ?>>
                    </div>
                    <div class="form-group wide">
                      <button type="button" class="btn btn-default" id="btnDiscover"<?php echo $dis; ?>><i class="fa fa-search"></i> Connect and find my pages</button>
                      <span class="sx-help" id="discMsg" role="status"></span>
                    </div>
                    <div class="form-group">
                      <label for="mPage">Facebook page</label>
                      <select id="mPage" class="form-control"<?php echo $dis; ?>>
                        <?php if ($e['page_id'] !== '') { ?><option value="<?php echo $h($e['page_id']); ?>" data-name="<?php echo $h($e['page_name']); ?>" data-ig="<?php echo $h($e['ig_id']); ?>" data-ign="<?php echo $h($e['ig_name']); ?>"><?php echo $h($e['page_name'] ?: $e['page_id']); ?></option><?php } else { ?><option value="">Press "Connect and find my pages"</option><?php } ?>
                      </select>
                      <span class="hint" id="igHint"><?php echo $e['ig_id'] !== '' ? 'Instagram: @' . $h($e['ig_name']) . ' (found automatically)' : ($e['ig_ads_id'] !== '' ? 'No Instagram business account linked to this page. Ads still show on Instagram through the page.' : 'No Instagram account linked to this page.'); ?></span>
                      <?php if ($e['id'] && $e['page_id'] !== '' && $e_tok && $can_write) { ?><button type="button" class="btn btn-link btn-sm" id="btnIg" style="padding-left:0">Check Instagram again</button><?php } ?>
                    </div>
                    <div class="form-group">
                      <label for="mAct">Ad account</label>
                      <select id="mAct" name="ad_account_id" class="form-control"<?php echo $dis; ?>>
                        <?php if ($e['ad_account_id'] !== '') { ?><option value="<?php echo $h($e['ad_account_id']); ?>" data-cur="<?php echo $h($e['currency']); ?>">act_<?php echo $h($e['ad_account_id']); ?> (<?php echo $h($e['currency']); ?>)</option><?php } ?>
                        <option value=""<?php echo $e['ad_account_id'] === '' ? ' selected' : ''; ?>>No ads</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="mWeb">Website</label>
                      <input type="url" id="mWeb" name="website" class="form-control" maxlength="255" value="<?php echo $h($e['website']); ?>" placeholder="https://"<?php echo $dis; ?>>
                    </div>
                    <div class="form-group">
                      <label for="mPriv">Privacy policy link</label>
                      <input type="url" id="mPriv" name="privacy_url" class="form-control" maxlength="255" value="<?php echo $h($e['privacy_url']); ?>" placeholder="Needed for lead forms"<?php echo $dis; ?>>
                    </div>
                    <div class="form-group">
                      <label for="mVer">Graph API version</label>
                      <input type="text" id="mVer" name="graph_version" class="form-control" maxlength="8" value="<?php echo $h($e['graph_version']); ?>"<?php echo $dis; ?>>
                    </div>
                  </div>
                  <input type="hidden" name="page_id" value="<?php echo $h($e['page_id']); ?>"><input type="hidden" name="page_name" value="<?php echo $h($e['page_name']); ?>">
                  <input type="hidden" name="ig_id" value="<?php echo $h($e['ig_id']); ?>"><input type="hidden" name="ig_name" value="<?php echo $h($e['ig_name']); ?>">
                  <input type="hidden" name="currency" value="<?php echo $h($e['currency']); ?>">
                </div>
              </div>
              <?php } ?>

              <div class="sx-card">
                <div class="sx-card-h"><h3><i class="fa fa-random"></i> Who gets new leads</h3></div>
                <div class="sx-card-b">
                  <div class="seg" style="margin-bottom:12px">
                    <label><input type="radio" name="route" value="fixed"<?php echo $e['route'] !== 'round' ? ' checked' : ''; echo $dis; ?>><span>One person</span></label>
                    <label><input type="radio" name="route" value="round"<?php echo $e['route'] === 'round' ? ' checked' : ''; echo $dis; ?>><span>Take turns</span></label>
                  </div>
                  <div class="form-group rt-fixed">
                    <label for="mOwner">Owner of new records</label>
                    <select id="mOwner" name="owner_id" class="form-control"<?php echo $dis; ?>>
                      <option value="">Choose…</option>
                      <?php foreach ($users as $u) { ?><option value="<?php echo (int)$u['id']; ?>"<?php echo $sel($e['owner_id'], $u['id']); ?>><?php echo $h($u['name']); ?></option><?php } ?>
                    </select>
                  </div>
                  <div class="form-group rt-round">
                    <label>People who take turns</label>
                    <div class="people"><div class="people-list">
                      <?php foreach ($users as $u) { ?><label><input type="checkbox" name="rr_users[]" value="<?php echo (int)$u['id']; ?>"<?php echo in_array((int)$u['id'], $e_rr, true) ? ' checked' : ''; echo $dis; ?>><span><?php echo $h($u['name']); ?></span></label><?php } ?>
                    </div></div>
                  </div>
                  <div class="form-group">
                    <label>Co-workers on every new record <span class="hint">(optional)</span></label>
                    <div class="people"><div class="people-list">
                      <?php foreach ($users as $u) { ?><label><input type="checkbox" name="agents[]" value="<?php echo (int)$u['id']; ?>"<?php echo in_array((int)$u['id'], $e_ag, true) ? ' checked' : ''; echo $dis; ?>><span><?php echo $h($u['name']); ?></span></label><?php } ?>
                    </div></div>
                    <span class="hint">They can open and work the record next to the owner (CRM co-agents).</span>
                  </div>
                  <p class="hint" style="margin:0">New records start in Database stage. A phone or e-mail already in the CRM is not added again; it is listed as "Already in CRM".</p>
                </div>
              </div>

              <div class="sx-card">
                <?php if ($is_meta) { ?>
                <div class="sx-card-h"><h3><i class="fa fa-shield"></i> Ads safety rule</h3></div>
                <div class="sx-card-b">
                  <div class="f2">
                    <div class="form-group">
                      <label class="tog"><input type="checkbox" name="auto_pause" value="1"<?php echo !empty($e['auto_pause']) ? ' checked' : ''; echo $dis; ?>><span>Pause costly campaigns<small>Checked every hour by the cron job, using the last 7 days.</small></span></label>
                    </div>
                    <div class="form-group">
                      <label for="mCpl">Highest cost per lead (<?php echo $h($e['currency']); ?>)</label>
                      <input type="number" id="mCpl" name="max_cpl" class="form-control" min="0" step="0.01" value="<?php echo (float)$e['max_cpl'] ?: ''; ?>"<?php echo $dis; ?>>
                    </div>
                  </div>
                </div>
                <?php } ?>
                <?php if ($can_write) { ?>
                <div class="sx-card-f">
                  <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Save</button>
                  <?php if ($is_meta) { ?><button type="button" class="btn btn-default" id="btnSubscribe"<?php echo $e['id'] && $e_tok && $e['page_id'] !== '' ? '' : ' disabled'; ?>><i class="fa fa-bolt"></i> Send page leads to the CRM</button><?php } ?>
                  <?php if ($is_meta && $e_tok) { ?><button type="button" class="btn btn-link" id="btnDisconnect">Disconnect</button><?php } ?>
                  <?php if ($e['id']) { ?><button type="button" class="btn btn-link mk-err" id="btnDelConn">Remove account</button><?php } ?>
                  <span class="sx-help" id="metaMsg" role="status"></span>
                </div>
                <?php } ?>
              </div>
            </form>

            <form id="claudeForm" autocomplete="off" novalidate>
              <div class="sx-card">
                <div class="sx-card-h"><h3><i class="fa fa-magic"></i> Claude (writing, ad reviews, autopilot)<?php if ($has_claude) { ?> <span class="chip st-Active">Key saved</span><?php } ?></h3></div>
                <div class="sx-card-b">
                  <div class="f2">
                    <div class="form-group">
                      <label for="cKey">Claude API key</label>
                      <div class="mk-pw"><input type="password" id="cKey" name="api_key" class="form-control" autocomplete="new-password" placeholder="<?php echo $has_claude ? 'Saved. Leave empty to keep it' : 'sk-ant-… from console.anthropic.com'; ?>"<?php echo $dis; ?>></div>
                    </div>
                    <div class="form-group">
                      <label for="cModel">Model</label>
                      <select id="cModel" name="model" class="form-control"<?php echo $dis; ?>><?php foreach ($claude_models as $k => $v) { ?><option value="<?php echo $k; ?>"<?php echo $sel($c['model'], $k); ?>><?php echo $h($v); ?></option><?php } ?></select>
                    </div>
                    <div class="form-group wide">
                      <label for="cBrand">About the company</label>
                      <textarea id="cBrand" name="brand" class="form-control" rows="3" maxlength="1500" placeholder="Services, areas served, what makes you different. Claude uses this in every text."<?php echo $dis; ?>><?php echo $h($c['brand']); ?></textarea>
                    </div>
                    <div class="form-group">
                      <label for="cTone">Tone</label>
                      <input type="text" id="cTone" name="tone" class="form-control" maxlength="100" value="<?php echo $h($c['tone']); ?>"<?php echo $dis; ?>>
                    </div>
                  </div>
                </div>
                <?php if ($can_write) { ?>
                <div class="sx-card-f">
                  <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Save</button>
                  <button type="button" class="btn btn-default" id="btnTestClaude"><i class="fa fa-plug"></i> Check</button>
                  <span class="sx-help" id="claudeMsg" role="status"></span>
                </div>
                <?php } ?>
              </div>
            </form>
          </div>

          <aside>
            <div class="sx-card">
              <div class="sx-card-h"><h3><i class="fa fa-link"></i> Links for the outside</h3></div>
              <div class="sx-card-b" style="display:block">
                <?php if ($e['id']) { ?>
                  <p class="hint" style="margin:0 0 8px">For <b><?php echo $h($e['name']); ?></b>. Each account has its own key.</p>
                  <?php if ($is_meta) { ?>
                  <p class="hint" style="margin:0 0 6px"><b>Meta webhook</b> (App › Webhooks › Page › leadgen): callback URL and verify token. Several pages on the same Meta app need only one callback URL: each lead goes to the account of its page.</p>
                  <div class="copy"><input type="text" class="form-control" id="hkMeta" readonly value="<?php echo $h($hooks['meta_hook']); ?>"><button type="button" class="btn btn-default btn-sm" data-copy="#hkMeta">Copy</button></div>
                  <div class="copy" style="margin-top:6px"><input type="text" class="form-control" id="hkVerify" readonly value="<?php echo $h($hooks['verify_token']); ?>"><button type="button" class="btn btn-default btn-sm" data-copy="#hkVerify">Copy</button></div>
                  <?php } ?>
                  <p class="hint" style="margin:12px 0 6px"><b><?php echo $e['platform'] === 'google' ? 'Google Ads lead form' : ($is_meta ? 'Zapier / Make for this account' : 'Website forms, Zapier, Make or the platform\'s webhook'); ?></b>: POST name, phone, email, company, city, message.<?php echo $e['platform'] === 'google' ? ' In Google Ads use this URL and the key below.' : ''; ?></p>
                  <div class="copy"><input type="text" class="form-control" id="hkLead" readonly value="<?php echo $h($hooks['lead_hook']); ?>"><button type="button" class="btn btn-default btn-sm" data-copy="#hkLead">Copy</button></div>
                  <div class="copy" style="margin-top:6px"><input type="text" class="form-control" id="hkKey" readonly value="<?php echo $h($hooks['webhook_key']); ?>"><button type="button" class="btn btn-default btn-sm" data-copy="#hkKey">Copy</button></div>
                  <?php if ($can_write) { ?><button type="button" class="btn btn-link btn-sm" id="btnRenew" style="padding-left:0;margin-top:4px">Renew this account's key</button><?php } ?>
                <?php } else { ?>
                  <p class="hint" style="margin:0">Save the account to get its links.</p>
                <?php } ?>
                <p class="hint" style="margin:14px 0 6px"><b>Cron job</b> every 5 minutes, for all accounts: scheduled posts, lead backup sync, ads rule and the Claude autopilot.</p>
                <div class="copy"><input type="text" class="form-control" id="hkCron" readonly value="<?php echo $h($cron_url); ?>"><button type="button" class="btn btn-default btn-sm" data-copy="#hkCron">Copy</button></div>
                <?php if ($can_write) { ?><button type="button" class="btn btn-link btn-sm" id="btnRenewCron" style="padding-left:0;margin-top:4px">Renew the cron key</button><?php } ?>
              </div>
            </div>
            <div class="sx-card">
              <div class="sx-card-h"><h3><i class="fa fa-history"></i> Recent changes</h3></div>
              <div class="sx-card-b" style="display:block">
                <?php $shown = 0; foreach ($log as $l) { if ($l['action'] === 'rule_check') continue; $shown++; ?>
                  <div class="mk-log"><span><?php echo $h(date('d M H:i', strtotime($l['created_at']))); ?></span><?php echo $h($l['detail']); ?><?php if ($l['name']) { ?> <em>· <?php echo $h($l['name']); ?></em><?php } ?></div>
                <?php } if (!$shown) { ?><p class="hint" style="margin:0">Nothing yet.</p><?php } ?>
              </div>
            </div>
          </aside>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Add lead -->
<div class="ai-drawer" id="leadDrawer" role="dialog" aria-modal="true" aria-labelledby="ldTitle"><div class="bg" data-close></div><div class="pn">
  <div class="hd"><h3 id="ldTitle">Add lead<small>From an event, call or message; same rules as form leads.</small></h3><button type="button" data-close aria-label="Close">&times;</button></div>
  <form id="leadForm" class="bd cp" autocomplete="off" novalidate>
    <div class="f2">
      <div class="form-group"><label for="aName">Name</label><input type="text" id="aName" name="full_name" class="form-control" maxlength="150"></div>
      <div class="form-group"><label for="aCo">Company</label><input type="text" id="aCo" name="company" class="form-control" maxlength="255"></div>
      <div class="form-group"><label for="aPh">Phone</label><input type="tel" id="aPh" name="phone" class="form-control" maxlength="40"></div>
      <div class="form-group"><label for="aEm">E-mail</label><input type="email" id="aEm" name="email" class="form-control" maxlength="150"></div>
      <div class="form-group"><label for="aCity">City</label><input type="text" id="aCity" name="city" class="form-control" maxlength="100"></div>
      <div class="form-group"><label for="aCamp">Campaign</label><input type="text" id="aCamp" name="campaign_name" class="form-control" maxlength="255" placeholder="e.g. GITEX stand"></div>
      <div class="form-group"><label for="aConn">Account</label><select id="aConn" name="conn" class="form-control"><option value="0">None (owner: me)</option><?php foreach ($conns as $x) { ?><option value="<?php echo (int)$x['id']; ?>"><?php echo $h($x['name'] . ' · ' . $x['source_name']); ?></option><?php } ?></select><span class="hint">An account gives its Source Name, Ref and owner.</span></div>
      <div class="form-group"><label for="aOwner">Owner</label><select id="aOwner" name="owner_id" class="form-control"><option value="0">As the account says</option><?php foreach ($users as $u) { ?><option value="<?php echo (int)$u['id']; ?>"><?php echo $h($u['name']); ?></option><?php } ?></select></div>
      <div class="form-group wide"><label for="aNote">Note</label><textarea id="aNote" name="note" class="form-control" rows="2" maxlength="500"></textarea></div>
    </div>
  </form>
  <div class="ft"><button type="button" class="btn btn-primary" id="btnLeadSave"><i class="fa fa-check"></i> Add</button> <span class="sx-help" id="leadMsg" role="status"></span></div>
</div></div>

<!-- Assign -->
<div class="ai-drawer" id="asgDrawer" role="dialog" aria-modal="true" aria-labelledby="agTitle"><div class="bg" data-close></div><div class="pn">
  <div class="hd"><h3 id="agTitle">Assign leads<small id="agSub">Owner and co-workers for the selected records.</small></h3><button type="button" data-close aria-label="Close">&times;</button></div>
  <form id="asgForm" class="bd cp" autocomplete="off" novalidate>
    <div class="form-group"><label for="gOwner">Owner</label><select id="gOwner" name="owner_id" class="form-control"><option value="">Choose…</option><?php foreach ($users as $u) { ?><option value="<?php echo (int)$u['id']; ?>"><?php echo $h($u['name']); ?></option><?php } ?></select></div>
    <div class="form-group"><label>Co-workers <span class="hint">(can work the record too)</span></label>
      <div class="people"><div class="people-list"><?php foreach ($users as $u) { ?><label><input type="checkbox" name="agents[]" value="<?php echo (int)$u['id']; ?>"><span><?php echo $h($u['name']); ?></span></label><?php } ?></div></div>
    </div>
    <p class="hint">This replaces the owner and co-workers on these records. Their Source Name and Ref stay, so reports keep counting them under the account.</p>
  </form>
  <div class="ft"><button type="button" class="btn btn-primary" id="btnAsgSave"><i class="fa fa-check"></i> Assign</button> <span class="sx-help" id="asgMsg" role="status"></span></div>
</div></div>

<!-- New lead campaign -->
<div class="ai-drawer" id="campDrawer" role="dialog" aria-modal="true" aria-labelledby="cdTitle"><div class="bg" data-close></div><div class="pn">
  <div class="hd"><h3 id="cdTitle">New lead campaign<small>Builds the campaign, audience and ad in Meta (<?php echo $h($m['name']); ?>). Everything starts paused.</small></h3><button type="button" data-close aria-label="Close">&times;</button></div>
  <form id="campForm" class="bd cp" autocomplete="off" novalidate>
    <div class="f2">
      <div class="form-group wide"><label for="nName">Campaign name</label><input type="text" id="nName" name="name" class="form-control" maxlength="150" placeholder="e.g. Company setup – Nov"></div>
      <div class="form-group"><label for="nBudget">Daily budget (<?php echo $h($m['currency']); ?>)</label><input type="number" id="nBudget" name="daily_budget" class="form-control" min="5" step="1" value="50"></div>
      <div class="form-group"><label for="nStart">Start</label><input type="date" id="nStart" name="start" class="form-control"></div>
      <div class="form-group wide"><label>Countries</label>
        <div class="pills"><?php foreach ($countries as $k => $v) { ?><label><input type="checkbox" name="countries[]" value="<?php echo $k; ?>"<?php echo $k === 'AE' ? ' checked' : ''; ?>><span><?php echo $h($v); ?></span></label><?php } ?></div>
      </div>
      <div class="form-group"><label for="nAgeMin">Age from</label><input type="number" id="nAgeMin" name="age_min" class="form-control" min="18" max="65" value="25"></div>
      <div class="form-group"><label for="nAgeMax">Age to</label><input type="number" id="nAgeMax" name="age_max" class="form-control" min="18" max="65" value="60"></div>
      <div class="form-group wide"><label for="nForm">Lead form</label>
        <div class="copy"><select id="nForm" name="form_id" class="form-control"><option value="">Loading forms…</option></select><button type="button" class="btn btn-default btn-sm" id="btnNewForm">New form</button></div>
      </div>
      <div class="form-group wide mk-newform" id="newForm" hidden>
        <div class="f2">
          <div class="form-group"><label for="fName">Form name</label><input type="text" id="fName" class="form-control" maxlength="100"></div>
          <div class="form-group"><label for="fPriv">Privacy policy link</label><input type="url" id="fPriv" class="form-control" maxlength="255" value="<?php echo $h($m['privacy_url']); ?>"></div>
          <div class="form-group wide"><label>Questions</label>
            <div class="pills"><?php foreach (array('FULL_NAME' => 'Full name', 'PHONE' => 'Phone', 'EMAIL' => 'E-mail', 'COMPANY_NAME' => 'Company', 'CITY' => 'City', 'JOB_TITLE' => 'Job title') as $k => $v) { ?><label><input type="checkbox" class="fq" value="<?php echo $k; ?>"<?php echo in_array($k, array('FULL_NAME', 'PHONE', 'EMAIL', 'COMPANY_NAME'), true) ? ' checked' : ''; ?>><span><?php echo $v; ?></span></label><?php } ?></div>
          </div>
          <div class="form-group wide"><label for="fThanks">Thank-you message</label><input type="text" id="fThanks" class="form-control" maxlength="300" value="Thank you. Our team will call you shortly."></div>
          <div class="form-group wide"><button type="button" class="btn btn-default btn-sm" id="btnFormSave">Create form</button> <span class="sx-help" id="formMsg" role="status"></span></div>
        </div>
      </div>
      <div class="form-group wide"><label for="nReuse">Start from an earlier creative</label>
        <select id="nReuse" class="form-control"><option value="">None: write a new ad</option></select>
        <input type="hidden" name="image_hash" value=""><span class="hint" id="nReuseHint">Fills the text, headline and picture from an earlier ad or page post. Change anything before creating.</span>
      </div>
      <div class="form-group wide">
        <label>Ad text</label>
        <div class="mk-ai"><input type="text" id="nTopic" class="form-control" maxlength="1500" placeholder="Service and offer, e.g. Mainland company setup in 7 days"><select id="nLang" class="form-control" aria-label="Language"><option value="en">English</option><option value="ar">Arabic</option></select><button type="button" class="btn btn-default" id="btnWriteAd"<?php echo $has_claude ? '' : ' disabled title="Add the Claude API key in Connections"'; ?>><i class="fa fa-magic"></i> Write with Claude</button></div>
        <textarea id="nText" name="text" class="form-control" rows="4" maxlength="2000" placeholder="Primary text"></textarea>
      </div>
      <div class="form-group"><label for="nHead">Headline</label><input type="text" id="nHead" name="headline" class="form-control" maxlength="40"></div>
      <div class="form-group"><label for="nCta">Button</label><select id="nCta" name="cta" class="form-control"><?php foreach ($ctas as $k => $v) { ?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php } ?></select></div>
      <div class="form-group wide"><label for="nImg">Image link</label><input type="url" id="nImg" name="image_url" class="form-control" maxlength="500" placeholder="https://… .jpg, 1080×1080 works best"></div>
    </div>
  </form>
  <div class="ft"><button type="button" class="btn btn-primary" id="btnCampSave"><i class="fa fa-check"></i> Create (paused)</button> <span class="sx-help" id="campMsg" role="status"></span></div>
</div></div>

<!-- Ad sets -->
<div class="ai-drawer" id="setDrawer" role="dialog" aria-modal="true" aria-labelledby="sdTitle"><div class="bg" data-close></div><div class="pn">
  <div class="hd"><h3 id="sdTitle">Ad sets</h3><button type="button" data-close aria-label="Close">&times;</button></div>
  <div class="bd cp" id="setBody"></div>
</div></div>

<script>window.MK = { base: '<?php echo $base; ?>system/marketing/', page: '<?php echo $base; ?>system/marketing', acc: <?php echo (int)$m['id']; ?>, edit: <?php echo (int)$e['id']; ?>, cur: '<?php echo $h($m['currency']); ?>', canWrite: <?php echo $can_write ? 'true' : 'false'; ?>, hasAds: <?php echo $has_token && $m['ad_account_id'] !== '' ? 'true' : 'false'; ?> };</script>
<script src="<?php echo base_url(); ?>assets/dist/js/crm-marketing.js?v=20261107_5"></script>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
