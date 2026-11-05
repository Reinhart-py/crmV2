<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
  $cb_tab = 'history';
  $label = array('waiting' => 'Waiting', 'active' => 'Active', 'emailed' => 'Emailed to IT', 'closed' => 'Closed');
  $dt = function ($v) { return $v ? date('d M Y, H:i', strtotime($v)) : '-'; };
?>
<style>
  .ls-badge { display: inline-block; font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 999px; text-transform: uppercase; letter-spacing: .04em; }
  .ls-b-waiting { background: #fef3c7; color: #92400e; }
  .ls-b-active { background: #dcfce7; color: #166534; }
  .ls-b-emailed { background: #e0e7ff; color: #3730a3; }
  .ls-b-closed { background: #f1f5f9; color: #475569; }
  .tr-info dt { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: var(--crm-muted, #64748b); margin-top: 12px; }
  .tr-info dt:first-child { margin-top: 0; }
  .tr-info dd { margin: 3px 0 0; font-size: 14px; color: var(--crm-ink, #0f172a); word-wrap: break-word; }
  .tr-msgs { display: flex; flex-direction: column; gap: 10px; padding: 16px; background: #f8fafc; border-radius: 8px; }
  .tr-day { align-self: center; font-size: 12px; font-weight: 600; color: #64748b; background: #e2e8f0; border-radius: 999px; padding: 2px 10px; }
  .ls-msg { max-width: 75%; display: flex; flex-direction: column; }
  .ls-meta { font-size: 11.5px; color: #64748b; margin: 0 4px 3px; }
  .ls-bubble { padding: 9px 12px; border-radius: 12px; font-size: 14px; line-height: 1.5; white-space: pre-wrap; word-wrap: break-word; }
  .ls-visitor { align-self: flex-start; }
  .ls-visitor .ls-bubble { background: #fff; border: 1px solid #e2e8f0; color: #0f172a; border-bottom-left-radius: 4px; }
  .ls-agent { align-self: flex-end; align-items: flex-end; }
  .ls-agent .ls-bubble { background: #1e3a8a; color: #fff; border-bottom-right-radius: 4px; }
  .ls-system { align-self: center; max-width: 90%; }
  .ls-system .ls-bubble { color: #64748b; font-size: 12.5px; text-align: center; padding: 2px 6px; }
</style>
<div class="content-wrapper">
  <section class="content-header crm-list-head">
    <h1>Chatbot</h1>
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
      <?php if ($chat['status'] !== 'closed') { ?><a href="<?php echo base_url('manager/chatbot') . '?chat=' . (int)$chat['id']; ?>" class="btn btn-sm btn-primary"><i class="fa fa-reply"></i> Open in Inbox</a><?php } ?>
      <a href="<?php echo base_url('manager/chatbot/history'); ?>" class="btn btn-sm btn-default"><i class="fa fa-arrow-left"></i> Back to History</a>
    </div>
  </section>

  <section class="content">
    <?php require(APPPATH . "views/manager/support/_tabs.php"); ?>
    <div class="row">
      <div class="col-lg-4 col-md-5">
        <div class="box crm-list-box">
          <div class="box-header with-border"><h3 class="box-title"><?php echo htmlspecialchars($chat['ref_no']); ?> <span class="ls-badge ls-b-<?php echo htmlspecialchars($chat['status']); ?>"><?php echo isset($label[$chat['status']]) ? $label[$chat['status']] : htmlspecialchars($chat['status']); ?></span></h3></div>
          <div class="box-body">
            <dl class="tr-info">
              <dt>Name</dt><dd><?php echo htmlspecialchars($chat['name']); ?></dd>
              <dt>Phone</dt><dd><a href="tel:<?php echo htmlspecialchars($chat['phone']); ?>"><?php echo htmlspecialchars($chat['phone']); ?></a></dd>
              <dt>Email</dt><dd><?php echo !empty($chat['email']) ? '<a href="mailto:' . htmlspecialchars($chat['email']) . '">' . htmlspecialchars($chat['email']) . '</a>' : '-'; ?></dd>
              <dt>Issue</dt><dd style="white-space:pre-wrap;"><?php echo htmlspecialchars($chat['issue']); ?></dd>
              <dt>Answered by</dt><dd><?php echo $chat['agent_name'] !== '' ? htmlspecialchars($chat['agent_name']) : 'Nobody'; ?></dd>
              <dt>Started</dt><dd><?php echo $dt($chat['created_at']); ?></dd>
              <?php if (!empty($chat['emailed_at'])) { ?><dt>Emailed to IT</dt><dd><?php echo $dt($chat['emailed_at']); ?></dd><?php } ?>
              <?php if (!empty($chat['closed_at'])) { ?><dt>Closed</dt><dd><?php echo $dt($chat['closed_at']); ?></dd><?php } ?>
            </dl>
          </div>
        </div>
      </div>
      <div class="col-lg-8 col-md-7">
        <div class="box crm-list-box">
          <div class="box-header with-border"><h3 class="box-title">Conversation</h3></div>
          <div class="box-body">
            <?php if (!empty($messages)) { ?>
            <div class="tr-msgs">
              <?php $day = ''; foreach ($messages as $m) { ?>
                <?php if ($m['date'] !== $day) { $day = $m['date']; ?><span class="tr-day"><?php echo date('d M Y', strtotime($day)); ?></span><?php } ?>
                <div class="ls-msg ls-<?php echo htmlspecialchars($m['sender']); ?>">
                  <?php if ($m['sender'] !== 'system') { ?><div class="ls-meta"><?php echo htmlspecialchars($m['name']); ?> &middot; <?php echo htmlspecialchars($m['time']); ?></div><?php } ?>
                  <div class="ls-bubble"><?php echo htmlspecialchars($m['body']); ?></div>
                </div>
              <?php } ?>
            </div>
            <?php } else { ?>
              <div class="crm-empty-state">No messages in this conversation.</div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
