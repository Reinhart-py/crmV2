<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
$h = function ($x) { return htmlspecialchars((string)$x, ENT_QUOTES, 'UTF-8'); };
$n = function ($v) { return number_format((int)$v); };
$sel = function ($x, $y) { return (string)$x === (string)$y ? ' selected' : ''; };
$base = base_url() . 'manager/';
$src = array('meta' => 'Meta', 'google' => 'Google Ads', 'web' => 'Website', 'manual' => 'Added here');
$stc = array('Created' => 'oc-hot', 'Duplicate' => 'oc-mid', 'Error' => 'oc-bad', 'New' => 'oc-mid');
$stl = array('Created' => 'New record', 'Duplicate' => 'Already in CRM', 'Error' => 'Not saved', 'New' => 'New');
$pst = array('Draft' => 'st-Draft', 'Scheduled' => 'st-Scheduled', 'Posted' => 'st-Active', 'Failed' => 'oc-bad', 'Posting' => 'st-Scheduled');
$rr = array_map('intval', (array)$m['rr_users']);
$connected = $has_token && $m['page_id'] !== '';
$dis = $can_write ? '' : ' disabled';
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-campaigns.css?v=20261107_7">
<div class="content-wrapper sx cp mk">
  <section class="sx-head">
    <h1>Marketing<small>Leads from Meta, Google Ads and your website, page posts and Meta Ads</small></h1>
    <div class="sx-btns">
      <?php if ($can_write && $connected) { ?><button type="button" class="btn btn-default" id="btnSync"><i class="fa fa-refresh"></i> Get new leads</button><?php } ?>
      <?php if ($can_write) { ?><button type="button" class="btn btn-primary" id="btnAddLead"><i class="fa fa-plus"></i> Add lead</button><?php } ?>
    </div>
  </section>

  <section class="content">
    <?php if (!$connected) { ?>
      <div class="ai-off"><i class="fa fa-plug"></i><span><b>Meta is not connected yet.</b> Website and Google Ads leads already work with the links in Connections. Connect Meta to get lead ads, post and run ads.</span>
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
        <button type="button" role="tab" data-tab="posts">Posts</button>
        <button type="button" role="tab" data-tab="ads">Meta Ads</button>
        <button type="button" role="tab" data-tab="conn">Connections</button>
      </nav>

      <!-- Leads -->
      <div class="ai-pane" data-pane="leads">
        <div class="ai-bar">
          <input type="search" id="lSearch" class="form-control" placeholder="Search name, phone, campaign" aria-label="Search leads">
          <select id="lSrc" class="form-control" aria-label="Source"><option value="">All sources</option><?php foreach ($src as $k => $v) { ?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php } ?></select>
          <select id="lSt" class="form-control" aria-label="Result"><option value="">All results</option><?php foreach ($stl as $k => $v) if ($k !== 'New') { ?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php } ?></select>
        </div>
        <?php if (!$leads) { ?>
          <div class="sx-empty"><i class="fa fa-inbox"></i>No marketing leads yet. They appear here as soon as a form is filled on Meta, Google Ads or your website.</div>
        <?php } else { ?>
        <ul class="ai-calls" id="leadList">
          <?php foreach ($leads as $r) { $other = json_decode((string)$r['fields_json'], true) ?: array(); ?>
          <li class="ai-call" data-src="<?php echo $h($r['source']); ?>" data-st="<?php echo $h($r['status']); ?>" data-q="<?php echo $h(strtolower($r['full_name'] . ' ' . $r['company'] . ' ' . $r['phone'] . ' ' . $r['email'] . ' ' . $r['campaign_name'] . ' ' . $r['ad_name'] . ' ' . $r['form_name'])); ?>">
            <div class="who">
              <b><?php echo $h($r['company'] !== '' ? $r['company'] : ($r['full_name'] !== '' ? $r['full_name'] : ($r['phone'] !== '' ? $r['phone'] : ($src[$r['source']] ?? 'Marketing') . ' lead ' . $r['external_id']))); ?></b>
              <?php if ($r['company'] !== '' && $r['full_name'] !== '') { ?><span><?php echo $h($r['full_name']); ?></span><?php } ?>
            </div>
            <div>
              <span class="chip <?php echo $stc[$r['status']] ?? 'oc-mid'; ?>"><?php echo $h($stl[$r['status']] ?? $r['status']); ?></span>
              <span class="chip s"><?php echo $h(($src[$r['source']] ?? $r['source']) . ($r['platform'] !== '' && $r['source'] === 'meta' ? ' · ' . ($r['platform'] === 'ig' ? 'Instagram' : ($r['platform'] === 'fb' ? 'Facebook' : $r['platform'])) : '')); ?></span>
              <?php if ($r['error'] !== '') { ?><div class="sum"><?php echo $h($r['error']); ?></div><?php } ?>
              <?php if ($other) { ?><div class="sum"><?php echo $h(implode(' · ', array_map(function ($k, $v) { return ucfirst(str_replace('_', ' ', $k)) . ': ' . (is_array($v) ? implode(', ', $v) : $v); }, array_keys($other), $other))); ?></div><?php } ?>
              <div class="meta">
                <span><i class="fa fa-clock-o"></i> <?php echo $h(date('d M H:i', strtotime($r['received_at']))); ?></span>
                <?php if ($r['phone'] !== '') { ?><span><i class="fa fa-phone"></i> <?php echo $h($r['phone']); ?></span><?php } ?>
                <?php if ($r['email'] !== '') { ?><span><i class="fa fa-envelope-o"></i> <?php echo $h($r['email']); ?></span><?php } ?>
                <?php if ($r['campaign_name'] !== '') { ?><span><i class="fa fa-bullhorn"></i> <?php echo $h($r['campaign_name']); ?></span><?php } ?>
                <?php if ($r['form_name'] !== '') { ?><span><i class="fa fa-wpforms"></i> <?php echo $h($r['form_name']); ?></span><?php } ?>
                <?php if ($r['owner_name']) { ?><span><i class="fa fa-user"></i> <?php echo $h($r['owner_name']); ?></span><?php } ?>
              </div>
            </div>
            <div class="cp-acts">
              <?php if ($r['lead_row_id']) { ?><a class="btn btn-default btn-sm" href="<?php echo $base; ?>leads/view_leads/view/<?php echo (int)$r['lead_row_id']; ?>"><i class="fa fa-external-link"></i> Record</a>
              <a class="btn btn-default btn-sm" href="<?php echo $base; ?>calls?lead=<?php echo (int)$r['lead_row_id']; ?>"><i class="fa fa-phone"></i> Call</a><?php } ?>
            </div>
          </li>
          <?php } ?>
        </ul>
        <div class="sx-empty" id="leadNone" style="display:none"><i class="fa fa-search"></i>No leads match.</div>
        <?php } ?>
      </div>

      <!-- Posts -->
      <div class="ai-pane" data-pane="posts" hidden>
        <div class="mk-grid">
          <form id="postForm" class="mk-compose" autocomplete="off" novalidate>
            <input type="hidden" name="id" value="0">
            <h4>New post</h4>
            <div class="pills" style="margin-bottom:10px">
              <label><input type="checkbox" name="channels[]" value="fb" checked<?php echo $dis; ?>><span><i class="fa fa-facebook-official"></i> Facebook<?php echo $m['page_name'] !== '' ? ' · ' . $h($m['page_name']) : ''; ?></span></label>
              <label><input type="checkbox" name="channels[]" value="ig"<?php echo $m['ig_id'] === '' ? ' disabled' : $dis; ?>><span><i class="fa fa-instagram"></i> Instagram<?php echo $m['ig_name'] !== '' ? ' · @' . $h($m['ig_name']) : ($m['ig_id'] === '' ? ' (not linked)' : ''); ?></span></label>
            </div>
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
              <li class="ai-call mk-post" data-post='<?php echo $h(json_encode(array('id' => (int)$p['id'], 'channels' => explode(',', $p['channels']), 'message' => $p['message'], 'image_url' => $p['image_url'], 'link' => $p['link'], 'scheduled_at' => $p['scheduled_at'] ? date('Y-m-d\TH:i', strtotime($p['scheduled_at'])) : ''))); ?>'>
                <?php if ($p['image_url'] !== '') { ?><img class="mk-thumb" src="<?php echo $h($p['image_url']); ?>" alt="" loading="lazy" referrerpolicy="no-referrer"><?php } else { ?><span class="mk-thumb"><i class="fa fa-align-left"></i></span><?php } ?>
                <div>
                  <span class="chip <?php echo $pst[$p['status']] ?? 'st-Draft'; ?>"><?php echo $h($p['status']); ?></span>
                  <?php foreach (explode(',', $p['channels']) as $chn) { ?><span class="chip s"><?php echo $chn === 'ig' ? 'Instagram' : 'Facebook'; ?></span><?php } ?>
                  <div class="sum"><?php echo $h(mb_strimwidth((string)$p['message'], 0, 160, '…')); ?></div>
                  <?php if ($p['error'] !== '') { ?><div class="sum mk-err"><?php echo $h($p['error']); ?></div><?php } ?>
                  <div class="meta"><span><i class="fa fa-clock-o"></i> <?php echo $h($p['status'] === 'Posted' ? 'Posted ' . date('d M H:i', strtotime($p['posted_at'])) : ($p['status'] === 'Scheduled' ? 'For ' . date('d M H:i', strtotime($p['scheduled_at'])) : 'Saved ' . date('d M H:i', strtotime($p['created_at'])))); ?></span><?php if ($p['by_name']) { ?><span><i class="fa fa-user"></i> <?php echo $h($p['by_name']); ?></span><?php } ?>
                    <?php if ($p['fb_post_id'] !== '') { ?><a href="https://www.facebook.com/<?php echo $h($p['fb_post_id']); ?>" target="_blank" rel="noopener">Open on Facebook</a><?php } ?></div>
                </div>
                <?php if ($can_write && !in_array($p['status'], array('Posted', 'Posting'), true)) { ?>
                <div class="cp-acts">
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
      </div>

      <!-- Meta Ads -->
      <div class="ai-pane" data-pane="ads" hidden>
        <?php if (!$has_token || $m['ad_account_id'] === '') { ?>
          <div class="sx-empty"><i class="fa fa-bullhorn"></i>Connect Meta and choose the ad account in Connections to see and run your ads here.<br><button type="button" class="btn btn-default" style="margin-top:12px" data-go="conn">Open Connections</button></div>
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
          <?php if (!empty($m['auto_pause'])) { ?>Auto-pause is on: running campaigns that cost more than <b><?php echo $h($m['currency'] . ' ' . number_format((float)$m['max_cpl'], 2)); ?></b> a lead (last 7 days) are paused every hour by the cron job.
          <?php } else { ?>Auto-pause is off. Turn it on in Connections to stop campaigns that cost too much per lead.<?php } ?>
        </div>
        <div id="aiReview" class="mk-review" hidden></div>
        <div id="adsBody"><div class="sx-empty"><i class="fa fa-spinner fa-spin"></i>Loading campaigns…</div></div>
        <?php } ?>
      </div>

      <!-- Connections -->
      <div class="ai-pane" data-pane="conn" hidden>
        <div class="mk-conn">
          <div>
            <form id="metaForm" autocomplete="off" novalidate>
              <div class="sx-card">
                <div class="sx-card-h"><h3><i class="fa fa-facebook-square"></i> Meta (Facebook, Instagram, Ads)<?php if ($connected) { ?> <span class="chip st-Active">Connected</span><?php } ?></h3></div>
                <div class="sx-card-b">
                  <div class="f2">
                    <div class="form-group wide">
                      <label for="mToken">Access token</label>
                      <div class="mk-pw"><input type="password" id="mToken" name="token" class="form-control" autocomplete="new-password" placeholder="<?php echo $has_token ? 'Saved. Leave empty to keep it' : 'System user token from Meta Business settings'; ?>"<?php echo $dis; ?>></div>
                      <span class="hint">Business settings › Users › System users › Generate token, with pages_manage_posts, pages_read_engagement, pages_manage_metadata, leads_retrieval, ads_management, instagram_basic and instagram_content_publish. Stored in the CRM database only.</span>
                    </div>
                    <div class="form-group">
                      <label for="mSecret">App secret</label>
                      <div class="mk-pw"><input type="password" id="mSecret" name="app_secret" class="form-control" autocomplete="new-password" placeholder="<?php echo $has_secret ? 'Saved. Leave empty to keep it' : 'App settings › Basic'; ?>"<?php echo $dis; ?>></div>
                      <span class="hint">Checks that lead notifications really come from Meta.</span>
                    </div>
                    <div class="form-group">
                      <label for="mApp">App ID</label>
                      <input type="text" id="mApp" name="app_id" class="form-control" inputmode="numeric" maxlength="30" value="<?php echo $h($m['app_id']); ?>"<?php echo $dis; ?>>
                    </div>
                    <div class="form-group wide">
                      <button type="button" class="btn btn-default" id="btnDiscover"<?php echo $dis; ?>><i class="fa fa-search"></i> Connect and find my pages</button>
                      <span class="sx-help" id="discMsg" role="status"></span>
                    </div>
                    <div class="form-group">
                      <label for="mPage">Facebook page</label>
                      <select id="mPage" class="form-control"<?php echo $dis; ?>>
                        <?php if ($m['page_id'] !== '') { ?><option value="<?php echo $h($m['page_id']); ?>" data-name="<?php echo $h($m['page_name']); ?>" data-ig="<?php echo $h($m['ig_id']); ?>" data-ign="<?php echo $h($m['ig_name']); ?>"><?php echo $h($m['page_name'] ?: $m['page_id']); ?></option><?php } else { ?><option value="">Press "Connect and find my pages"</option><?php } ?>
                      </select>
                      <span class="hint" id="igHint"><?php echo $m['ig_id'] !== '' ? 'Instagram: @' . $h($m['ig_name']) : 'No Instagram account linked to this page.'; ?></span>
                    </div>
                    <div class="form-group">
                      <label for="mAct">Ad account</label>
                      <select id="mAct" name="ad_account_id" class="form-control"<?php echo $dis; ?>>
                        <?php if ($m['ad_account_id'] !== '') { ?><option value="<?php echo $h($m['ad_account_id']); ?>" data-cur="<?php echo $h($m['currency']); ?>">act_<?php echo $h($m['ad_account_id']); ?> (<?php echo $h($m['currency']); ?>)</option><?php } ?>
                        <option value=""<?php echo $m['ad_account_id'] === '' ? ' selected' : ''; ?>>No ads</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="mWeb">Website</label>
                      <input type="url" id="mWeb" name="website" class="form-control" maxlength="255" value="<?php echo $h($m['website']); ?>" placeholder="https://"<?php echo $dis; ?>>
                    </div>
                    <div class="form-group">
                      <label for="mPriv">Privacy policy link</label>
                      <input type="url" id="mPriv" name="privacy_url" class="form-control" maxlength="255" value="<?php echo $h($m['privacy_url']); ?>" placeholder="Needed for lead forms"<?php echo $dis; ?>>
                    </div>
                    <div class="form-group">
                      <label for="mVer">Graph API version</label>
                      <input type="text" id="mVer" name="graph_version" class="form-control" maxlength="8" value="<?php echo $h($m['graph_version']); ?>"<?php echo $dis; ?>>
                    </div>
                  </div>
                  <input type="hidden" name="page_id" value="<?php echo $h($m['page_id']); ?>"><input type="hidden" name="page_name" value="<?php echo $h($m['page_name']); ?>">
                  <input type="hidden" name="ig_id" value="<?php echo $h($m['ig_id']); ?>"><input type="hidden" name="ig_name" value="<?php echo $h($m['ig_name']); ?>">
                  <input type="hidden" name="currency" value="<?php echo $h($m['currency']); ?>">
                </div>
              </div>

              <div class="sx-card">
                <div class="sx-card-h"><h3><i class="fa fa-random"></i> Who gets new leads</h3></div>
                <div class="sx-card-b">
                  <div class="seg" style="margin-bottom:12px">
                    <label><input type="radio" name="route" value="fixed"<?php echo $m['route'] !== 'round' ? ' checked' : ''; echo $dis; ?>><span>One person</span></label>
                    <label><input type="radio" name="route" value="round"<?php echo $m['route'] === 'round' ? ' checked' : ''; echo $dis; ?>><span>Take turns</span></label>
                  </div>
                  <div class="form-group rt-fixed">
                    <label for="mOwner">Owner of new records</label>
                    <select id="mOwner" name="owner_id" class="form-control"<?php echo $dis; ?>>
                      <option value="">Choose…</option>
                      <?php foreach ($users as $u) { ?><option value="<?php echo (int)$u['id']; ?>"<?php echo $sel($m['owner_id'], $u['id']); ?>><?php echo $h($u['name']); ?></option><?php } ?>
                    </select>
                  </div>
                  <div class="form-group rt-round">
                    <label>People who take turns</label>
                    <div class="people"><div class="people-list">
                      <?php foreach ($users as $u) { ?><label><input type="checkbox" name="rr_users[]" value="<?php echo (int)$u['id']; ?>"<?php echo in_array((int)$u['id'], $rr, true) ? ' checked' : ''; echo $dis; ?>><span><?php echo $h($u['name']); ?></span></label><?php } ?>
                    </div></div>
                  </div>
                  <p class="hint" style="margin:0">New records start in Database stage. A phone or e-mail already in the CRM is not added again; it is listed as "Already in CRM".</p>
                </div>
              </div>

              <div class="sx-card">
                <div class="sx-card-h"><h3><i class="fa fa-shield"></i> Ads safety rule</h3></div>
                <div class="sx-card-b">
                  <div class="f2">
                    <div class="form-group">
                      <label class="tog"><input type="checkbox" name="auto_pause" value="1"<?php echo !empty($m['auto_pause']) ? ' checked' : ''; echo $dis; ?>><span>Pause costly campaigns<small>Checked every hour by the cron job, using the last 7 days.</small></span></label>
                    </div>
                    <div class="form-group">
                      <label for="mCpl">Highest cost per lead (<?php echo $h($m['currency']); ?>)</label>
                      <input type="number" id="mCpl" name="max_cpl" class="form-control" min="0" step="0.01" value="<?php echo (float)$m['max_cpl'] ?: ''; ?>"<?php echo $dis; ?>>
                    </div>
                  </div>
                </div>
                <?php if ($can_write) { ?>
                <div class="sx-card-f">
                  <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Save</button>
                  <button type="button" class="btn btn-default" id="btnSubscribe"<?php echo $connected ? '' : ' disabled'; ?>><i class="fa fa-bolt"></i> Send page leads to the CRM</button>
                  <?php if ($has_token) { ?><button type="button" class="btn btn-link" id="btnDisconnect">Disconnect Meta</button><?php } ?>
                  <span class="sx-help" id="metaMsg" role="status"></span>
                </div>
                <?php } ?>
              </div>
            </form>

            <form id="claudeForm" autocomplete="off" novalidate>
              <div class="sx-card">
                <div class="sx-card-h"><h3><i class="fa fa-magic"></i> Claude (writing and ad reviews)<?php if ($has_claude) { ?> <span class="chip st-Active">Key saved</span><?php } ?></h3></div>
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
                <p class="hint" style="margin:0 0 6px"><b>Meta webhook</b> (App › Webhooks › Page › leadgen): callback URL and verify token.</p>
                <div class="copy"><input type="text" class="form-control" id="hkMeta" readonly value="<?php echo $h($meta_hook); ?>"><button type="button" class="btn btn-default btn-sm" data-copy="#hkMeta">Copy</button></div>
                <div class="copy" style="margin-top:6px"><input type="text" class="form-control" id="hkVerify" readonly value="<?php echo $h($m['verify_token']); ?>"><button type="button" class="btn btn-default btn-sm" data-copy="#hkVerify">Copy</button></div>
                <p class="hint" style="margin:12px 0 6px"><b>Website forms, Zapier, Google Ads lead forms</b>: POST name, phone, email, company, city, message. In Google Ads use this URL and the key below.</p>
                <div class="copy"><input type="text" class="form-control" id="hkLead" readonly value="<?php echo $h($lead_hook); ?>"><button type="button" class="btn btn-default btn-sm" data-copy="#hkLead">Copy</button></div>
                <div class="copy" style="margin-top:6px"><input type="text" class="form-control" id="hkKey" readonly value="<?php echo $h($webhook_key); ?>"><button type="button" class="btn btn-default btn-sm" data-copy="#hkKey">Copy</button></div>
                <p class="hint" style="margin:12px 0 6px"><b>Cron job</b> every 5 minutes: scheduled posts, lead backup sync, ads rule.</p>
                <div class="copy"><input type="text" class="form-control" id="hkCron" readonly value="<?php echo $h($cron_url); ?>"><button type="button" class="btn btn-default btn-sm" data-copy="#hkCron">Copy</button></div>
                <?php if ($can_write) { ?><button type="button" class="btn btn-link btn-sm" id="btnRenew" style="padding-left:0;margin-top:8px">Renew the key</button><?php } ?>
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
      <div class="form-group wide"><label for="aNote">Note</label><textarea id="aNote" name="note" class="form-control" rows="2" maxlength="500"></textarea></div>
    </div>
  </form>
  <div class="ft"><button type="button" class="btn btn-primary" id="btnLeadSave"><i class="fa fa-check"></i> Add</button> <span class="sx-help" id="leadMsg" role="status"></span></div>
</div></div>

<!-- New lead campaign -->
<div class="ai-drawer" id="campDrawer" role="dialog" aria-modal="true" aria-labelledby="cdTitle"><div class="bg" data-close></div><div class="pn">
  <div class="hd"><h3 id="cdTitle">New lead campaign<small>Builds the campaign, audience and ad in Meta. Everything starts paused.</small></h3><button type="button" data-close aria-label="Close">&times;</button></div>
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

<script>window.MK = { base: '<?php echo $base; ?>system/marketing/', cur: '<?php echo $h($m['currency']); ?>', canWrite: <?php echo $can_write ? 'true' : 'false'; ?>, hasAds: <?php echo $has_token && $m['ad_account_id'] !== '' ? 'true' : 'false'; ?> };</script>
<script src="<?php echo base_url(); ?>assets/dist/js/crm-marketing.js?v=20261107_3"></script>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
