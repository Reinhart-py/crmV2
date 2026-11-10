<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
$x = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$on = (int)$row['status'] === 1;
$csrf = '<input type="hidden" name="' . $this->security->get_csrf_token_name() . '" value="' . $this->security->get_csrf_hash() . '">';
$act = base_url() . 'manager/system/hr-link/';
$dmy = function ($d) { $t = strtotime((string)$d); return $t ? date('d M Y', $t) : ''; };
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<style>
  .hl-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px 16px; }
  .hl-grid .wide { grid-column: 1 / -1; }
  .hl-grid .form-group { margin: 0; }
  .hl-row-form { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
  .hl-row-form select { max-width: 200px; height: 32px; padding: 4px 8px; }
  .hl-note { color: #64748b; font-size: 12.5px; }
  .hl-tag { display: inline-block; padding: 1px 8px; border-radius: 999px; font-size: 11.5px; font-weight: 600; background: #eef2ff; color: #3730a3; }
  .hl-tag.is-red { background: #fee2e2; color: #b91c1c; } .hl-tag.is-green { background: #dcfce7; color: #166534; } .hl-tag.is-amber { background: #fef3c7; color: #92400e; }
  .hl-log td { font-size: 12.5px; vertical-align: top; }
  @media (max-width: 767px) { .hl-grid { grid-template-columns: 1fr; } }
</style>
<div class="content-wrapper sx">
  <?php echo crm_tabbar('integrations', 'system/hr-link'); ?>
  <section class="sx-head">
    <h1>HR link<small>Staff, leave and commissions shared with HR &amp; Payroll</small></h1>
  </section>

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="sx-kpis">
      <div class="sx-kpi <?php echo $on ? 'g' : 'a'; ?>"><span class="ic"><i class="fa fa-handshake-o"></i></span><div><b><?php echo $on ? 'On' : 'Off'; ?></b><span>HR link</span></div></div>
      <div class="sx-kpi <?php echo $pending ? 'a' : 'g'; ?>"><span class="ic"><i class="fa fa-user-plus"></i></span><div><b><?php echo count($pending); ?></b><span>Waiting for approval</span></div></div>
      <div class="sx-kpi g"><span class="ic"><i class="fa fa-link"></i></span><div><b><?php echo (int)$linked; ?></b><span>Staff linked to HR</span></div></div>
      <div class="sx-kpi s"><span class="ic"><i class="fa fa-plane"></i></span><div><b><?php echo count($leave); ?></b><span>On leave now or soon</span></div></div>
    </div>

    <div class="sx-card">
      <div class="sx-card-h"><h3><i class="fa fa-cog"></i> Settings</h3><div class="sx-tools"><span class="chip <?php echo $on ? 'ok' : 'na'; ?>"><?php echo $on ? 'On' : 'Off'; ?></span></div></div>
      <div class="sx-card-b" style="padding-bottom:0;">
        <p class="sx-help"><i class="fa fa-info-circle"></i> HR can suggest changes, but only a CRM admin gives or takes away access. People added in HR wait here for an admin; nobody gets a login on their own. When HR says someone has left, their sign-in is blocked straight away and their leads and deals stay for you to reassign. HR updates only name, phone and job title; role, team, manager and rights are changed only in the CRM. Admin accounts are never switched off from HR.</p>
        <p class="sx-help">Messages are signed with the portal app secret from <a href="<?php echo base_url(); ?>manager/system/integrations#tab_subscription">Third-Party API &gt; Subscription</a> and only this company's messages are used<?php echo $trust['company'] !== '' ? ' (' . $x($trust['company']) . ')' : ''; ?>. HR sends to <code><?php echo $x($events_url); ?></code>.</p>
        <?php if ($missing !== '') { ?><div class="alert alert-warning" style="margin:0 0 12px;"><i class="fa fa-exclamation-triangle"></i> <?php echo $x($missing); ?></div><?php } ?>
      </div>
      <form action="<?php echo $act; ?>save" method="post" autocomplete="off">
        <?php echo $csrf; ?>
        <fieldset <?php echo $can_edit ? '' : 'disabled'; ?>>
        <div class="sx-card-b"><div class="hl-grid">
          <div class="form-group"><label>HR address</label><input type="url" name="endpoint" class="form-control" placeholder="https://hr.example.com" value="<?php echo $x($hr_url); ?>" required></div>
          <div class="form-group"><label>Status</label><select name="status" class="form-control"><option value="1"<?php echo $on ? ' selected' : ''; ?>>On</option><option value="0"<?php echo !$on ? ' selected' : ''; ?>>Off</option></select></div>
        </div></div>
        </fieldset>
        <?php if ($can_edit) { ?>
        <div class="sx-card-f">
          <span>
            <button type="submit" formaction="<?php echo $act; ?>ping" formnovalidate class="btn btn-default"><i class="fa fa-refresh"></i> Test connection</button>
            <button type="submit" formaction="<?php echo $act; ?>load" formnovalidate class="btn btn-default"><i class="fa fa-download"></i> Load staff from HR</button>
          </span>
          <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save settings</button>
        </div>
        <?php } ?>
      </form>
    </div>

    <div class="sx-card">
      <div class="sx-card-h"><h3><i class="fa fa-user-plus"></i> Waiting for approval</h3></div>
      <div class="sx-card-b">
        <?php if (!$pending) { ?><p class="hl-note" style="margin:0;">Nobody is waiting. People added in HR show here unless a CRM user already has their email (then they are linked straight away).</p><?php } else { ?>
        <p class="hl-note">Approving gives a login with the <b>Sales</b> role under the manager you choose. You can change the role later on their profile.</p>
        <div class="sx-table-wrap"><table class="sx-table" style="width:100%">
          <thead><tr><th>Name</th><th>Email</th><th>Job title / department</th><th>Joined</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($pending as $p) { ?>
            <tr>
              <td><b><?php echo $x($p['full_name']); ?></b><?php echo $p['emp_code'] !== '' ? ' <span class="hl-note">' . $x($p['emp_code']) . '</span>' : ''; ?><?php echo (int)$p['looks_sales'] ? ' <span class="hl-tag is-green">Sales</span>' : ''; ?><?php echo $p['note'] !== '' ? '<div class="hl-note">' . $x($p['note']) . '</div>' : ''; ?></td>
              <td><?php echo $x($p['email']); ?></td>
              <td><?php echo $x(trim($p['designation'] . ($p['department'] !== '' ? ' / ' . $p['department'] : ''), ' /')); ?></td>
              <td><?php echo $x($dmy($p['join_date'])); ?></td>
              <td>
                <?php if ($can_edit) { ?>
                <div class="hl-row-form">
                  <?php if ($is_admin) { ?>
                  <form action="<?php echo $act; ?>approve" method="post" class="hl-row-form" onsubmit="return confirm('Give <?php echo $x(addslashes($p['full_name'])); ?> a CRM login with the Sales role?');">
                    <?php echo $csrf; ?><input type="hidden" name="hr_id" value="<?php echo $x($p['hr_id']); ?>">
                    <select name="manager" class="form-control" required><option value="">Manager...</option><?php foreach ($managers as $m) { ?><option value="<?php echo (int)$m['id']; ?>"><?php echo $x($m['name']); ?></option><?php } ?></select>
                    <button type="submit" class="btn btn-primary btn-sm"<?php echo $p['email'] === '' ? ' disabled' : ''; ?>>Approve</button>
                  </form>
                  <?php } ?>
                  <form action="<?php echo $act; ?>dismiss" method="post"><?php echo $csrf; ?><input type="hidden" name="hr_id" value="<?php echo $x($p['hr_id']); ?>"><button type="submit" class="btn btn-default btn-sm" title="No CRM login needed">No login</button></form>
                </div>
                <?php } ?>
              </td>
            </tr>
          <?php } ?>
          </tbody>
        </table></div>
        <?php } ?>
      </div>
    </div>

    <div class="sx-card">
      <div class="sx-card-h"><h3><i class="fa fa-sign-out"></i> Left in HR</h3></div>
      <div class="sx-card-b">
        <?php if (!$exited) { ?><p class="hl-note" style="margin:0;">Nobody has left through HR.</p><?php } else { ?>
        <div class="sx-table-wrap"><table class="sx-table" style="width:100%">
          <thead><tr><th>Name</th><th>Left on</th><th>Reason</th><th>CRM</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($exited as $p) { $uid = (int)$p['user_id']; ?>
            <tr>
              <td><b><?php echo $x($p['crm_name'] ?: $p['full_name']); ?></b><div class="hl-note"><?php echo $x($p['email']); ?></div></td>
              <td><?php echo $x($dmy($p['exit_date'])); ?></td>
              <td><?php echo $x($p['exit_reason']); ?></td>
              <td><?php if ($p['state'] === 'exited') { ?><span class="hl-tag is-red">Sign-in blocked</span><?php } else { ?><span class="hl-tag is-amber">Admin account, not switched off</span><?php } ?><?php echo $p['note'] !== '' ? '<div class="hl-note">' . $x($p['note']) . '</div>' : ''; ?></td>
              <td><?php if ($uid) { ?><a class="btn btn-default btn-sm" href="<?php echo base_url(); ?>manager/hr/staff/view/<?php echo $uid; ?>">Open profile<?php echo (int)$p['open_records'] > 0 ? ' to reassign' : ''; ?></a><?php } ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table></div>
        <?php } ?>
      </div>
    </div>

    <div class="sx-card">
      <div class="sx-card-h"><h3><i class="fa fa-plane"></i> Leave from HR</h3></div>
      <div class="sx-card-b">
        <?php if (!$leave) { ?><p class="hl-note" style="margin:0;">No approved leave now or coming up.</p><?php } else { ?>
        <p class="hl-note">On these days the person is skipped in lead sharing and cannot start the auto-dialler, and shows "On leave" on User Activity.</p>
        <div class="sx-table-wrap"><table class="sx-table" style="width:100%">
          <thead><tr><th>Name</th><th>Leave</th><th>From</th><th>To</th><th>Days</th></tr></thead>
          <tbody>
          <?php foreach ($leave as $l) { ?>
            <tr>
              <td><b><?php echo $x($l['crm_name'] ?: ($l['full_name'] ?: $l['email'])); ?></b><?php echo (int)$l['user_id'] ? '' : ' <span class="hl-tag is-amber">No CRM user</span>'; ?></td>
              <td><?php echo $x($l['leave_type']); ?></td>
              <td><?php echo $x($dmy($l['start_date'])); ?></td>
              <td><?php echo $x($dmy($l['end_date'])); ?></td>
              <td><?php echo $x(rtrim(rtrim((string)$l['days'], '0'), '.')); ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table></div>
        <?php } ?>
      </div>
    </div>

    <div class="sx-card">
      <div class="sx-card-h"><h3><i class="fa fa-money"></i> Commissions sent to HR payroll</h3></div>
      <div class="sx-card-b">
        <?php if (!$commissions) { ?><p class="hl-note" style="margin:0;">None yet. An admin approves a commission on an approved order's page.</p><?php } else { ?>
        <div class="sx-table-wrap"><table class="sx-table" style="width:100%">
          <thead><tr><th>Order</th><th>Salesperson</th><th>Month</th><th>Amount</th><th>Status</th></tr></thead>
          <tbody>
          <?php foreach ($commissions as $c) { $tag = array('sent' => 'is-green', 'withdrawn' => '', 'failed' => 'is-red', 'withdraw_failed' => 'is-red'); ?>
            <tr>
              <td><a href="<?php echo base_url(); ?>manager/orders/view/<?php echo (int)$c['order_id']; ?>">#<?php echo $x($c['order_no'] ?: $c['order_id']); ?></a></td>
              <td><?php echo $x($c['rep'] ?: $c['email']); ?></td>
              <td><?php echo $x($c['month']); ?></td>
              <td><?php echo number_format((float)$c['amount'], 2); ?></td>
              <td><span class="hl-tag <?php echo isset($tag[$c['state']]) ? $tag[$c['state']] : ''; ?>"><?php echo $x(ucfirst(str_replace('_', ' ', $c['state']))); ?></span><div class="hl-note"><?php echo $x($c['hr_message']); ?></div></td>
            </tr>
          <?php } ?>
          </tbody>
        </table></div>
        <?php } ?>
      </div>
    </div>

    <div class="sx-card">
      <div class="sx-card-h"><h3><i class="fa fa-list"></i> Activity</h3></div>
      <div class="sx-card-b">
        <?php if (!$log) { ?><p class="hl-note" style="margin:0;">Nothing yet.</p><?php } else { ?>
        <div class="sx-table-wrap"><table class="sx-table hl-log" style="width:100%">
          <thead><tr><th>When</th><th>Direction</th><th>Event</th><th>What happened</th></tr></thead>
          <tbody>
          <?php foreach ($log as $g) { ?>
            <tr>
              <td style="white-space:nowrap;"><?php echo date('d M, h:i A', strtotime($g['at'])); ?></td>
              <td><?php echo $g['dir'] === 'in' ? 'From HR' : 'To HR'; ?></td>
              <td><?php echo $x($g['event']); ?></td>
              <td><?php echo (int)$g['ok'] ? '' : '<span class="hl-tag is-red">Failed</span> '; ?><?php echo $x($g['summary']); ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table></div>
        <?php } ?>
      </div>
    </div>
  </section>
</div>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
