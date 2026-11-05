<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
  $cb_tab = 'history';
  $base = base_url('manager/chatbot/history');
  $qs = function ($extra = array()) use ($filters) {
    $p = array_merge(array('q' => $filters['q'], 'status' => $filters['status'], 'from' => $filters['from'], 'to' => $filters['to'], 'agent' => $filters['agent'] ? (string)$filters['agent'] : ''), $extra);
    $p = array_filter($p, 'strlen');
    return $p ? '?' . http_build_query($p) : '';
  };
  $badge = array('waiting' => 'ls-b-waiting', 'active' => 'ls-b-active', 'emailed' => 'ls-b-emailed', 'closed' => 'ls-b-closed');
  $label = array('waiting' => 'Waiting', 'active' => 'Active', 'emailed' => 'Emailed to IT', 'closed' => 'Closed');
?>
<style>
  .ls-badge { display: inline-block; font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 999px; text-transform: uppercase; letter-spacing: .04em; white-space: nowrap; }
  .ls-b-waiting { background: #fef3c7; color: #92400e; }
  .ls-b-active { background: #dcfce7; color: #166534; }
  .ls-b-emailed { background: #e0e7ff; color: #3730a3; }
  .ls-b-closed { background: #f1f5f9; color: #475569; }
  .cb-pager { display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; margin-top: 12px; }
</style>
<div class="content-wrapper">
  <section class="content-header crm-list-head">
    <h1>Chatbot</h1>
    <span class="crm-list-count"><?php echo (int)$total; ?> <?php echo $total == 1 ? 'conversation' : 'conversations'; ?></span>
  </section>

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>
    <?php require(APPPATH . "views/manager/support/_tabs.php"); ?>

    <div class="cb-stats">
      <div class="cb-stat"><b><?php echo (int)$counts['today']; ?></b><span>Today</span></div>
      <div class="cb-stat"><b><?php echo (int)$counts['waiting'] + (int)$counts['active']; ?></b><span>Open</span></div>
      <div class="cb-stat"><b><?php echo (int)$counts['emailed']; ?></b><span>Emailed to IT</span></div>
      <div class="cb-stat"><b><?php echo (int)$counts['closed']; ?></b><span>Closed</span></div>
    </div>

    <div class="box crm-filter-box">
      <form class="crm-filter-bar" method="get" action="<?php echo $base; ?>">
        <div class="crm-filter-field is-wide">
          <label for="hQ">Search</label>
          <input type="text" id="hQ" name="q" class="form-control" placeholder="Reference, name, phone, email or issue" value="<?php echo htmlspecialchars($filters['q']); ?>">
        </div>
        <div class="crm-filter-field">
          <label for="hStatus">Status</label>
          <select id="hStatus" name="status" class="form-control">
            <option value="">All statuses</option>
            <?php foreach ($label as $k => $v) { ?><option value="<?php echo $k; ?>" <?php echo $filters['status'] === $k ? 'selected' : ''; ?>><?php echo $v; ?></option><?php } ?>
          </select>
        </div>
        <div class="crm-filter-field">
          <label for="hAgent">Answered by</label>
          <select id="hAgent" name="agent" class="form-control">
            <option value="">Anyone</option>
            <?php foreach ($agents as $a) { ?><option value="<?php echo (int)$a['id']; ?>" <?php echo (int)$filters['agent'] === (int)$a['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($a['name']); ?></option><?php } ?>
          </select>
        </div>
        <div class="crm-filter-field">
          <label for="hFrom">From</label>
          <input type="date" id="hFrom" name="from" class="form-control" value="<?php echo htmlspecialchars($filters['from']); ?>">
        </div>
        <div class="crm-filter-field">
          <label for="hTo">To</label>
          <input type="date" id="hTo" name="to" class="form-control" value="<?php echo htmlspecialchars($filters['to']); ?>">
        </div>
        <div class="crm-filter-actions">
          <button type="submit" class="btn btn-primary">Search</button>
          <a href="<?php echo $base; ?>" class="btn btn-default">Reset</a>
        </div>
      </form>
    </div>

    <div class="box crm-list-box">
      <div class="box-body">
        <?php if (!empty($rows)) { ?>
        <div class="table-responsive">
          <table class="table table-hover crm-list-table">
            <thead><tr><th>Reference</th><th>Visitor</th><th>Contact</th><th>Status</th><th>Answered by</th><th>Messages</th><th>Started</th><th></th></tr></thead>
            <tbody>
              <?php foreach ($rows as $r) { ?>
              <tr>
                <td><a class="crm-ref" href="<?php echo $base . '/' . (int)$r['id']; ?>"><strong><?php echo htmlspecialchars($r['ref_no']); ?></strong></a></td>
                <td><?php echo htmlspecialchars($r['name']); ?></td>
                <td><?php echo htmlspecialchars($r['phone']); ?><?php if (!empty($r['email'])) { ?><div class="crm-sub"><?php echo htmlspecialchars($r['email']); ?></div><?php } ?></td>
                <td><span class="ls-badge <?php echo isset($badge[$r['status']]) ? $badge[$r['status']] : ''; ?>"><?php echo isset($label[$r['status']]) ? $label[$r['status']] : htmlspecialchars($r['status']); ?></span></td>
                <td><?php echo !empty($r['agent_name']) ? htmlspecialchars($r['agent_name']) : '<span class="crm-sub">Nobody</span>'; ?></td>
                <td><?php echo (int)$r['msg_count']; ?></td>
                <td><?php echo date('d M Y', strtotime($r['created_at'])); ?><div class="crm-sub"><?php echo date('H:i', strtotime($r['created_at'])); ?></div></td>
                <td class="text-right"><a href="<?php echo $base . '/' . (int)$r['id']; ?>" class="btn btn-default btn-xs">View</a></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <?php if ($pages > 1) { ?>
        <div class="cb-pager">
          <span class="crm-sub">Page <?php echo (int)$page; ?> of <?php echo (int)$pages; ?></span>
          <div class="btn-group">
            <a class="btn btn-default btn-sm<?php echo $page <= 1 ? ' disabled' : ''; ?>" href="<?php echo $page > 1 ? $base . $qs(array('page' => (string)($page - 1))) : '#'; ?>">&laquo; Previous</a>
            <a class="btn btn-default btn-sm<?php echo $page >= $pages ? ' disabled' : ''; ?>" href="<?php echo $page < $pages ? $base . $qs(array('page' => (string)($page + 1))) : '#'; ?>">Next &raquo;</a>
          </div>
        </div>
        <?php } ?>
        <?php } else { ?>
          <div class="crm-empty-state">No conversations match these filters.</div>
        <?php } ?>
      </div>
    </div>
  </section>
</div>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
