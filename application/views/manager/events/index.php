<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/fullcalendar/fullcalendar.min.css">

<style>
.gcal-wrapper {
  padding: 15px 20px 30px 20px;
}
.gcal-header-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}
.gcal-title {
  margin: 0;
  font-size: 20px;
  font-weight: 700;
  color: #0f172a;
  display: flex;
  align-items: center;
  gap: 10px;
}
body[data-theme="dark"] .gcal-title {
  color: #f8fafc;
}
.gcal-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
body[data-theme="dark"] .gcal-card {
  background: #1e293b;
  border-color: #334155;
  box-shadow: 0 1px 3px rgba(0,0,0,0.25);
}
.fc-toolbar {
  margin-bottom: 18px;
}
.fc-toolbar h2 {
  font-size: 18px;
  font-weight: 700;
  color: #0f172a;
}
body[data-theme="dark"] .fc-toolbar h2 {
  color: #f8fafc;
}
.fc-button {
  background: #ffffff !important;
  border: 1px solid #cbd5e1 !important;
  color: #334155 !important;
  box-shadow: none !important;
  text-shadow: none !important;
  font-weight: 600 !important;
  border-radius: 5px !important;
  height: 32px !important;
  padding: 0 12px !important;
  transition: all 0.15s ease;
}
.fc-button:hover, .fc-button.fc-state-hover {
  background: #f1f5f9 !important;
  color: #0f172a !important;
}
.fc-button.fc-state-active {
  background: #2563eb !important;
  border-color: #1d4ed8 !important;
  color: #ffffff !important;
}
body[data-theme="dark"] .fc-button {
  background: #334155 !important;
  border-color: #475569 !important;
  color: #f8fafc !important;
}
body[data-theme="dark"] .fc-button:hover {
  background: #475569 !important;
}
body[data-theme="dark"] .fc-button.fc-state-active {
  background: #2563eb !important;
  border-color: #1d4ed8 !important;
  color: #ffffff !important;
}
.fc th {
  padding: 10px 0 !important;
  font-weight: 600 !important;
  color: #64748b !important;
  background: #f8fafc !important;
  border-color: #e2e8f0 !important;
  font-size: 12px !important;
  text-transform: uppercase !important;
}
body[data-theme="dark"] .fc th {
  background: #0f172a !important;
  color: #94a3b8 !important;
  border-color: #334155 !important;
}
.fc td {
  border-color: #e2e8f0 !important;
}
body[data-theme="dark"] .fc td {
  border-color: #334155 !important;
}
body[data-theme="dark"] .fc-day-number {
  color: #cbd5e1 !important;
}
.fc-day.fc-today {
  background: rgba(37, 99, 235, 0.04) !important;
}
body[data-theme="dark"] .fc-day.fc-today {
  background: rgba(37, 99, 235, 0.15) !important;
}
.fc-event {
  border: none !important;
  border-radius: 4px !important;
  padding: 3px 6px !important;
  font-size: 11px !important;
  font-weight: 600 !important;
  box-shadow: 0 1px 2px rgba(0,0,0,0.08) !important;
  cursor: pointer !important;
  transition: transform 0.1s ease !important;
}
.fc-event:hover {
  transform: translateY(-1px) !important;
  filter: brightness(1.05) !important;
}
.gcal-legend {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  align-items: center;
  margin-top: 15px;
  padding-top: 15px;
  border-top: 1px solid #e2e8f0;
  font-size: 12px;
  color: #64748b;
}
body[data-theme="dark"] .gcal-legend {
  border-top-color: #334155;
  color: #94a3b8;
}
.gcal-legend-item {
  display: flex;
  align-items: center;
  gap: 6px;
}
.gcal-legend-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  display: inline-block;
}
.gcal-nav-tabs {
  display: flex;
  border-bottom: 2px solid #e2e8f0;
  margin-bottom: 15px;
}
body[data-theme="dark"] .gcal-nav-tabs {
  border-bottom-color: #334155;
}
.gcal-tab-btn {
  padding: 8px 18px;
  font-weight: 600;
  font-size: 13px;
  color: #64748b;
  border: none;
  background: transparent;
  cursor: pointer;
  border-bottom: 2px solid transparent;
  margin-bottom: -2px;
  transition: all 0.2s;
}
.gcal-tab-btn.active {
  color: #2563eb;
  border-bottom-color: #2563eb;
}
body[data-theme="dark"] .gcal-tab-btn {
  color: #94a3b8;
}
body[data-theme="dark"] .gcal-tab-btn.active {
  color: #60a5fa;
  border-bottom-color: #60a5fa;
}
</style>

<div class="content-wrapper">
  <div class="gcal-wrapper">
    <div class="gcal-header-bar">
      <h1 class="gcal-title">
        <i class="fa fa-calendar-check-o" style="color:#2563eb;"></i>
        <span>Events &amp; Awards Calendar</span>
      </h1>
      <div style="display:flex; gap:10px; align-items:center;">
        <button type="button" class="btn btn-primary" id="openCreateModalBtn" style="background:#2563eb; border-color:#1d4ed8; font-weight:600; padding:7px 16px; border-radius:6px;">
          <i class="fa fa-plus"></i> Create Event / Award
        </button>
      </div>
    </div>

    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="gcal-card">
      <div id="crmCalendar"></div>

      <div class="gcal-legend">
        <span style="font-weight:700; margin-right:4px;">Legend:</span>
        <span class="gcal-legend-item"><i class="fa fa-users" style="color:#2563eb;"></i> Meeting</span>
        <span class="gcal-legend-item"><i class="fa fa-building-o" style="color:#16a34a;"></i> Company Event</span>
        <span class="gcal-legend-item"><i class="fa fa-graduation-cap" style="color:#0284c7;"></i> Training</span>
        <span class="gcal-legend-item"><i class="fa fa-flag-checkered" style="color:#8b5cf6;"></i> Milestone</span>
        <span class="gcal-legend-item"><i class="fa fa-plane" style="color:#dc2626;"></i> Holiday</span>
        <span class="gcal-legend-item"><i class="fa fa-bell" style="color:#0d9488;"></i> Reminder</span>
        <span class="gcal-legend-item"><i class="fa fa-trophy" style="color:#d97706;"></i> Award / Achievement</span>
        <span class="gcal-legend-item"><i class="fa fa-birthday-cake" style="color:#ec4899;"></i> Birthday</span>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width:550px;">
    <div class="modal-content" style="border-radius:8px; border:none; box-shadow:0 10px 25px rgba(0,0,0,0.15);">
      <div class="modal-header" style="border-bottom:none; padding:15px 20px 0 20px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size:24px;">&times;</button>
        <div class="gcal-nav-tabs">
          <button type="button" class="gcal-tab-btn active" id="tabBtnEvent">Event / Meeting</button>
          <button type="button" class="gcal-tab-btn" id="tabBtnAward">Award / Achievement</button>
        </div>
      </div>
      
      <div class="modal-body" style="padding:10px 20px 20px 20px;">
        <form id="eventForm" style="display:block;">
          <input type="hidden" name="id" id="event_id" value="0">
          <div class="form-group">
            <label style="font-size:12px; font-weight:600; color:#475569;">Title *</label>
            <input type="text" name="title" id="event_title" class="form-control" placeholder="e.g. Weekly Strategy Sync, System Upgrade" required>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label style="font-size:12px; font-weight:600; color:#475569;">Event Type</label>
                <select name="event_type" id="event_type" class="form-control">
                  <option value="Meeting">Meeting</option>
                  <option value="Company Event">Company Event</option>
                  <option value="Training">Training</option>
                  <option value="Milestone">Milestone</option>
                  <option value="Holiday">Holiday</option>
                  <option value="Reminder">Reminder</option>
                </select>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label style="font-size:12px; font-weight:600; color:#475569;">Location</label>
                <input type="text" name="location" id="event_location" class="form-control" placeholder="Conference Room / Zoom">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label style="font-size:12px; font-weight:600; color:#475569;">Start Date &amp; Time *</label>
                <input type="datetime-local" name="start_date" id="event_start" class="form-control" required>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label style="font-size:12px; font-weight:600; color:#475569;">End Date &amp; Time</label>
                <input type="datetime-local" name="end_date" id="event_end" class="form-control">
              </div>
            </div>
          </div>
          <div class="form-group">
            <label style="font-weight:500; font-size:12px; cursor:pointer;">
              <input type="checkbox" name="all_day" id="event_all_day" value="1"> All-Day Event
            </label>
          </div>
          <div class="form-group">
            <label style="font-size:12px; font-weight:600; color:#475569;">Description</label>
            <textarea name="description" id="event_description" class="form-control" rows="3" placeholder="Add agenda or details..."></textarea>
          </div>
          <div style="display:flex; justify-content:flex-end; gap:8px;">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" id="saveEventBtn" style="background:#2563eb; border-color:#1d4ed8; font-weight:600;">Save Event</button>
          </div>
        </form>

        <form id="awardForm" style="display:none;">
          <div class="form-group">
            <label style="font-size:12px; font-weight:600; color:#475569;">Employee *</label>
            <select name="user_id" id="award_user_id" class="form-control" required>
              <option value="">-- Select Employee --</option>
              <?php if(!empty($users_list)){ foreach($users_list as $u){ ?>
                <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['name']) . (!empty($u['department']) ? ' ('.$u['department'].')' : ''); ?></option>
              <?php }} ?>
            </select>
          </div>
          <div class="form-group">
            <label style="font-size:12px; font-weight:600; color:#475569;">Award / Achievement Title *</label>
            <input type="text" name="title" id="award_title" class="form-control" placeholder="e.g. Top Closer of the Month, Best Customer Retention" required>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label style="font-size:12px; font-weight:600; color:#475569;">Category</label>
                <select name="category" id="award_category" class="form-control">
                  <option value="Top Performer">Top Performer</option>
                  <option value="Star Closer">Star Closer</option>
                  <option value="Excellence in Service">Excellence in Service</option>
                  <option value="Employee of the Month">Employee of the Month</option>
                  <option value="Special Milestone">Special Milestone</option>
                </select>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label style="font-size:12px; font-weight:600; color:#475569;">Award Date *</label>
                <input type="date" name="award_date" id="award_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label style="font-size:12px; font-weight:600; color:#475569;">Reward Points</label>
            <input type="number" name="reward_points" id="award_points" class="form-control" value="100" min="0" step="50">
          </div>
          <div class="form-group">
            <label style="font-size:12px; font-weight:600; color:#475569;">Recognition Remarks</label>
            <textarea name="description" id="award_description" class="form-control" rows="3" placeholder="State the reason or recognition citation..."></textarea>
          </div>
          <div style="display:flex; justify-content:flex-end; gap:8px;">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" id="saveAwardBtn" style="background:#d97706; border-color:#b45309; font-weight:600;">Record Award</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width:480px;">
    <div class="modal-content" style="border-radius:8px; border:none; box-shadow:0 10px 25px rgba(0,0,0,0.15);">
      <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:15px 20px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size:24px;">&times;</button>
        <h4 class="modal-title" id="detailModalTitle" style="font-size:16px; font-weight:700; color:#0f172a;"></h4>
      </div>
      <div class="modal-body" style="padding:20px;">
        <div id="detailTypeBadge" style="margin-bottom:12px;"></div>
        <div style="margin-bottom:10px; font-size:13px; color:#475569;">
          <i class="fa fa-clock-o" style="width:16px; color:#64748b; margin-right:6px;"></i>
          <span id="detailTime"></span>
        </div>
        <div id="detailLocationRow" style="margin-bottom:10px; font-size:13px; color:#475569; display:none;">
          <i class="fa fa-map-marker" style="width:16px; color:#ef4444; margin-right:6px;"></i>
          <span id="detailLocation"></span>
        </div>
        <div id="detailEmployeeRow" style="margin-bottom:10px; font-size:13px; color:#475569; display:none;">
          <i class="fa fa-user" style="width:16px; color:#2563eb; margin-right:6px;"></i>
          <span id="detailEmployee"></span>
        </div>
        <div id="detailDescriptionRow" style="margin-top:15px; padding:10px 14px; background:#f8fafc; border-radius:6px; font-size:13px; color:#334155; line-height:1.5;"></div>
      </div>
      <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:12px 20px; display:flex; justify-content:space-between;">
        <button type="button" class="btn btn-danger btn-sm" id="deleteItemBtn" style="font-weight:600;"><i class="fa fa-trash"></i> Delete</button>
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>

<script src="<?php echo base_url(); ?>assets/plugins/fullcalendar/fullcalendar.min.js"></script>

<script>
$(function() {
  var activeEventData = null;

  $('#tabBtnEvent').on('click', function() {
    $(this).addClass('active');
    $('#tabBtnAward').removeClass('active');
    $('#eventForm').show();
    $('#awardForm').hide();
  });

  $('#tabBtnAward').on('click', function() {
    $(this).addClass('active');
    $('#tabBtnEvent').removeClass('active');
    $('#awardForm').show();
    $('#eventForm').hide();
  });

  $('#openCreateModalBtn').on('click', function() {
    $('#eventForm')[0].reset();
    $('#awardForm')[0].reset();
    $('#event_id').val(0);
    var now = new Date();
    var nowIso = now.toISOString().slice(0, 16);
    $('#event_start').val(nowIso);
    $('#tabBtnEvent').trigger('click');
    $('#createModal').modal('show');
  });

  $('#crmCalendar').fullCalendar({
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'month,agendaWeek,agendaDay'
    },
    defaultView: 'month',
    editable: false,
    eventLimit: true,
    timezone: 'local',
    events: function(start, end, timezone, callback) {
      $.ajax({
        url: '<?php echo base_url(); ?>manager/events/ajax_events',
        dataType: 'json',
        data: {
          start: start.format('YYYY-MM-DD'),
          end: end.format('YYYY-MM-DD')
        },
        success: function(doc) {
          callback(doc);
        }
      });
    },
    dayClick: function(date) {
      $('#eventForm')[0].reset();
      $('#awardForm')[0].reset();
      $('#event_id').val(0);
      var dStr = date.format('YYYY-MM-DD') + 'T09:00';
      $('#event_start').val(dStr);
      $('#award_date').val(date.format('YYYY-MM-DD'));
      $('#tabBtnEvent').trigger('click');
      $('#createModal').modal('show');
    },
    eventClick: function(calEvent) {
      activeEventData = calEvent;
      $('#detailModalTitle').text(calEvent.title);
      
      var badgeHtml = '';
      if (calEvent.item_type === 'award') {
        badgeHtml = '<span class="label" style="background:#d97706; padding:4px 8px; font-size:11px;"><i class="fa fa-trophy"></i> ' + (calEvent.category || 'Award') + '</span>';
        if (calEvent.points) {
          badgeHtml += ' <span class="label" style="background:#2563eb; padding:4px 8px; font-size:11px;">+' + calEvent.points + ' Points</span>';
        }
      } else if (calEvent.item_type === 'birthday') {
        badgeHtml = '<span class="label" style="background:#ec4899; padding:4px 8px; font-size:11px;"><i class="fa fa-birthday-cake"></i> Employee Birthday</span>';
      } else {
        badgeHtml = '<span class="label" style="background:' + calEvent.color + '; padding:4px 8px; font-size:11px;">' + (calEvent.event_type || 'Event') + '</span>';
      }
      $('#detailTypeBadge').html(badgeHtml);

      var timeText = calEvent.start ? calEvent.start.format('MMMM D, YYYY') : '';
      if (!calEvent.allDay && calEvent.start) {
        timeText += ' at ' + calEvent.start.format('h:mm A');
        if (calEvent.end) {
          timeText += ' - ' + calEvent.end.format('h:mm A');
        }
      } else {
        timeText += ' (All Day)';
      }
      $('#detailTime').text(timeText);

      if (calEvent.location) {
        $('#detailLocation').text(calEvent.location);
        $('#detailLocationRow').show();
      } else {
        $('#detailLocationRow').hide();
      }

      if (calEvent.employee) {
        $('#detailEmployee').text(calEvent.employee + (calEvent.department ? ' (' + calEvent.department + ')' : ''));
        $('#detailEmployeeRow').show();
      } else {
        $('#detailEmployeeRow').hide();
      }

      if (calEvent.description) {
        $('#detailDescriptionRow').text(calEvent.description).show();
      } else {
        $('#detailDescriptionRow').hide();
      }

      if (calEvent.item_type === 'birthday') {
        $('#deleteItemBtn').hide();
      } else {
        $('#deleteItemBtn').show();
      }

      $('#detailModal').modal('show');
    }
  });

  $('#eventForm').on('submit', function(e) {
    e.preventDefault();
    var btn = $('#saveEventBtn');
    btn.prop('disabled', true).text('Saving...');
    $.ajax({
      url: '<?php echo base_url(); ?>manager/events/ajax_save_event',
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function(resp) {
        btn.prop('disabled', false).text('Save Event');
        if (resp.success) {
          $('#createModal').modal('hide');
          $('#crmCalendar').fullCalendar('refetchEvents');
        } else {
          alert(resp.message || 'Error saving event.');
        }
      },
      error: function() {
        btn.prop('disabled', false).text('Save Event');
        alert('Server connection error.');
      }
    });
  });

  $('#awardForm').on('submit', function(e) {
    e.preventDefault();
    var btn = $('#saveAwardBtn');
    btn.prop('disabled', true).text('Recording...');
    $.ajax({
      url: '<?php echo base_url(); ?>manager/events/ajax_save_award',
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function(resp) {
        btn.prop('disabled', false).text('Record Award');
        if (resp.success) {
          $('#createModal').modal('hide');
          $('#crmCalendar').fullCalendar('refetchEvents');
        } else {
          alert(resp.message || 'Error recording award.');
        }
      },
      error: function() {
        btn.prop('disabled', false).text('Record Award');
        alert('Server connection error.');
      }
    });
  });

  $('#deleteItemBtn').on('click', function() {
    if (!activeEventData || activeEventData.item_type === 'birthday') return;
    if (!confirm('Are you sure you want to remove this entry?')) return;
    $.ajax({
      url: '<?php echo base_url(); ?>manager/events/ajax_delete_item',
      type: 'POST',
      data: {
        id: activeEventData.raw_id,
        type: activeEventData.item_type
      },
      dataType: 'json',
      success: function(resp) {
        if (resp.success) {
          $('#detailModal').modal('hide');
          $('#crmCalendar').fullCalendar('refetchEvents');
        } else {
          alert(resp.message || 'Error deleting item.');
        }
      }
    });
  });
});
</script>
