<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
  $csrf_name = $this->security->get_csrf_token_name();
  $csrf_hash = $this->security->get_csrf_hash();
  $type_labels = array('meeting' => 'Meeting', 'training' => 'Training', 'holiday' => 'Holiday', 'celebration' => 'Celebration', 'plan' => 'Plan', 'other' => 'Other');
?>
<div class="content-wrapper">
  <section class="content-header crm-list-head">
    <h1><?php echo $info['page_heading']; ?></h1>
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
      <a href="<?php echo base_url('manager/hr/events_plans'); ?>" class="btn btn-sm <?php echo $show === 'upcoming' ? 'btn-primary' : 'btn-default'; ?>">Upcoming</a>
      <a href="<?php echo base_url('manager/hr/events_plans?show=past'); ?>" class="btn btn-sm <?php echo $show === 'past' ? 'btn-primary' : 'btn-default'; ?>">Past 90 days</a>
      <a href="<?php echo base_url('manager/profile'); ?>" class="btn btn-sm btn-default"><i class="fa fa-user"></i> My Profile</a>
    </div>
  </section>

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="row">
      <div class="col-lg-8 col-md-7">
        <div class="box crm-list-box">
          <div class="box-body">
            <?php if (!empty($events)) { ?>
              <div class="ev-list">
                <?php foreach ($events as $ev) {
                  $ev_type = strtolower((string)$ev['event_type']);
                  $ev_label = isset($type_labels[$ev_type]) ? $type_labels[$ev_type] : ucwords(str_replace('_', ' ', $ev_type ?: 'event'));
                  $ev_private = ($ev['visibility'] !== 'public');
                  $ev_ts = strtotime($ev['start_date']);
                  $ev_can_remove = ((int)$ev['created_by'] === (int)$cur_uid || !empty($is_admin));
                ?>
                <div class="ev-item">
                  <div class="ev-date">
                    <span class="ev-day"><?php echo date('d', $ev_ts); ?></span>
                    <span class="ev-mon"><?php echo date('M Y', $ev_ts); ?></span>
                  </div>
                  <div class="ev-main">
                    <div class="ev-tags">
                      <span class="crm-status crm-status-ocean"><?php echo htmlspecialchars($ev_label); ?></span>
                      <span class="crm-status"><?php echo $ev_private ? 'My plan' : 'Company'; ?></span>
                    </div>
                    <h4 class="ev-title"><?php echo htmlspecialchars($ev['title']); ?></h4>
                    <?php if (!empty($ev['description'])) { ?><p class="ev-desc"><?php echo nl2br(htmlspecialchars($ev['description'])); ?></p><?php } ?>
                    <div class="ev-meta">
                      <span><i class="fa fa-clock-o"></i> <?php echo !empty($ev['all_day']) ? 'All day' : date('h:i A', $ev_ts) . (!empty($ev['end_date']) ? ' - ' . date('h:i A', strtotime($ev['end_date'])) : ''); ?></span>
                      <?php if (!empty($ev['location'])) { ?><span><i class="fa fa-map-marker"></i> <?php echo htmlspecialchars($ev['location']); ?></span><?php } ?>
                      <?php if (!empty($ev['created_by_name'])) { ?><span><i class="fa fa-user-o"></i> <?php echo htmlspecialchars($ev['created_by_name']); ?></span><?php } ?>
                    </div>
                  </div>
                  <?php if ($ev_can_remove) { ?>
                  <form method="post" action="<?php echo base_url('manager/hr/delete_plan/' . (int)$ev['id']); ?>" class="ev-remove" onsubmit="return confirm('Remove this event?');">
                    <input type="hidden" name="<?php echo $csrf_name; ?>" value="<?php echo $csrf_hash; ?>">
                    <input type="hidden" name="return" value="manager/hr/events_plans">
                    <button type="submit" class="btn btn-default btn-xs" title="Remove">Remove</button>
                  </form>
                  <?php } ?>
                </div>
                <?php } ?>
              </div>
            <?php } else { ?>
              <div class="crm-empty-state"><?php echo $show === 'past' ? 'No events in the last 90 days.' : 'No upcoming events or plans yet. Add one with the form.'; ?></div>
            <?php } ?>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-5">
        <div class="box crm-list-box">
          <div class="box-header with-border"><h3 class="box-title">Add a plan</h3></div>
          <div class="box-body">
            <form method="post" action="<?php echo base_url('manager/hr/save_plan'); ?>">
              <input type="hidden" name="<?php echo $csrf_name; ?>" value="<?php echo $csrf_hash; ?>">
              <div class="form-group">
                <label for="evTitle">Title <span class="text-danger">*</span></label>
                <input type="text" id="evTitle" name="title" class="form-control" maxlength="255" required>
              </div>
              <div class="row">
                <div class="col-xs-6 form-group">
                  <label for="evType">Type</label>
                  <select id="evType" name="event_type" class="form-control">
                    <?php foreach ($type_labels as $k => $v) { ?><option value="<?php echo $k; ?>" <?php echo $k === 'plan' ? 'selected' : ''; ?>><?php echo $v; ?></option><?php } ?>
                  </select>
                </div>
                <div class="col-xs-6 form-group">
                  <label for="evDate">Date <span class="text-danger">*</span></label>
                  <input type="date" id="evDate" name="event_date" class="form-control" min="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-6 form-group">
                  <label for="evTime">Start time</label>
                  <input type="time" id="evTime" name="event_time" class="form-control">
                </div>
                <div class="col-xs-6 form-group">
                  <label for="evEnd">End time</label>
                  <input type="time" id="evEnd" name="end_time" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <label for="evLoc">Location</label>
                <input type="text" id="evLoc" name="location" class="form-control" maxlength="255">
              </div>
              <div class="form-group">
                <label for="evDesc">Notes</label>
                <textarea id="evDesc" name="description" class="form-control" rows="2"></textarea>
              </div>
              <?php if (!empty($is_hr)) { ?>
              <div class="form-group">
                <label for="evVis">Who can see it</label>
                <select id="evVis" name="visibility" class="form-control">
                  <option value="private">Only me (personal plan)</option>
                  <option value="public">Everyone (company event)</option>
                </select>
              </div>
              <?php } else { ?>
              <span class="pf-note" style="margin:0 0 10px;">Plans you add are private to you. Company events are published by HR.</span>
              <?php } ?>
              <button type="submit" class="btn btn-primary btn-block">Save</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
