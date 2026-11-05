<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
// Manage Events & Festivals: HR / Admin publish company holidays, festivals, events and awards.
// Everything published here is shown to everyone on the view-only Events & Festivals page.
$now_year = (int)date('Y');
$events = array_values(array_filter($items, function ($it) { return $it['kind'] === 'event'; }));
$awards = array_values(array_filter($items, function ($it) { return $it['kind'] === 'award'; }));
$em_date = function ($it) {
  $out = date('d M Y', strtotime($it['start']));
  if (!empty($it['end']) && substr($it['end'], 0, 10) > substr($it['start'], 0, 10)) $out .= ' - ' . date('d M Y', strtotime($it['end']));
  elseif (empty($it['all_day'])) $out .= ', ' . date('h:i A', strtotime($it['start']));
  return $out;
};
?>
<style>
  .em-page .em-top { display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:14px; }
  .em-page .em-years { display:inline-flex; border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#fff; }
  .em-page .em-years a { padding:7px 16px; font-weight:700; color:#475569; text-decoration:none; border-right:1px solid #e2e8f0; }
  .em-page .em-years a:last-child { border-right:0; }
  .em-page .em-years a.active { background:#1e3a8a; color:#fff; }
  .em-page .em-card { background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:16px; margin-bottom:16px; }
  .em-page .em-card h4 { margin:0 0 12px; font-size:15px; font-weight:700; color:#0f172a; display:flex; align-items:center; gap:8px; }
  .em-page .em-card h4 i { color:#1e3a8a; }
  .em-page .em-form { display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:10px; align-items:end; }
  .em-page .em-form .em-wide { grid-column:span 2; }
  .em-page .em-form .em-full { grid-column:1 / -1; }
  .em-page .em-form label { display:block; font-size:12px; font-weight:600; color:#475569; margin-bottom:3px; }
  .em-page .em-form .form-control { height:36px; }
  .em-page .em-form textarea.form-control { height:auto; }
  .em-page .em-actions { display:flex; gap:8px; justify-content:flex-end; }
  .em-page .em-hint { font-size:12px; color:#64748b; margin:-4px 0 10px; }
  .em-page .em-table { width:100%; margin:0; }
  .em-page .em-table th { font-size:12px; color:#64748b; background:#f8fafc; font-weight:600; }
  .em-page .em-table td, .em-page .em-table th { padding:8px 10px; border-top:1px solid #eef2f7; vertical-align:middle; }
  .em-page .em-badge { display:inline-block; font-size:11px; font-weight:700; padding:2px 8px; border-radius:10px; color:#fff; white-space:nowrap; }
  .em-page .em-row-actions { white-space:nowrap; text-align:right; }
  .em-page .em-row-actions form { display:inline; }
  .em-page .em-empty { padding:14px; background:#f8fafc; border-radius:8px; font-size:13px; color:#64748b; }
  .em-page .em-editing { background:#eff6ff; }
  @media (max-width: 991px) { .em-page .em-form { grid-template-columns:repeat(2, minmax(0, 1fr)); } }
  @media (max-width: 560px) {
    .em-page .em-form { grid-template-columns:1fr; }
    .em-page .em-form .em-wide { grid-column:auto; }
  }
</style>

<div class="content-wrapper">
  <section class="content-header">
    <h1><?php echo $info['page_heading']; ?></h1>
  </section>
  <section class="content em-page">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="em-top">
      <div class="em-years" aria-label="Year">
        <?php for ($y = $now_year - 1; $y <= $now_year + 1; $y++) { ?>
          <a href="<?php echo base_url('manager/events/manage?year=' . $y); ?>" class="<?php echo $y === $year ? 'active' : ''; ?>"><?php echo $y; ?></a>
        <?php } ?>
      </div>
      <a href="<?php echo base_url('manager/events?year=' . $year); ?>" class="btn btn-sm btn-default"><i class="fa fa-eye"></i> See what everyone sees</a>
    </div>

    <!-- Holidays, festivals and company events -->
    <div class="em-card">
      <h4><i class="fa fa-calendar"></i> <span id="evFormTitle">Add a holiday, festival or event</span></h4>
      <p class="em-hint">Published straight away for everyone on Events &amp; Festivals. Public holidays and festivals also show in each person's profile under Holidays.</p>
      <?php echo form_open('manager/events/save_event', array('id' => 'evForm')); ?>
        <input type="hidden" name="id" value="0">
        <input type="hidden" name="year" value="<?php echo $year; ?>">
        <div class="em-form">
          <div>
            <label for="evType">Type</label>
            <select name="event_type" id="evType" class="form-control" required>
              <?php foreach ($types as $k => $t) { ?><option value="<?php echo $k; ?>"><?php echo $t[0]; ?></option><?php } ?>
            </select>
          </div>
          <div class="em-wide">
            <label for="evTitle">Title</label>
            <input type="text" name="title" id="evTitle" class="form-control" maxlength="255" required placeholder="e.g. Eid Al Adha, National Day, Annual party">
          </div>
          <div>
            <label for="evLocation">Location <small>(optional)</small></label>
            <input type="text" name="location" id="evLocation" class="form-control" maxlength="255">
          </div>
          <div>
            <label for="evFrom">Start date</label>
            <input type="date" name="start_date" id="evFrom" class="form-control" required>
          </div>
          <div>
            <label for="evTo">End date <small>(several days)</small></label>
            <input type="date" name="end_date" id="evTo" class="form-control">
          </div>
          <div>
            <label for="evTime">Start time <small>(optional)</small></label>
            <input type="time" name="start_time" id="evTime" class="form-control">
          </div>
          <div>
            <label for="evEndTime">End time <small>(optional)</small></label>
            <input type="time" name="end_time" id="evEndTime" class="form-control">
          </div>
          <div class="em-full">
            <label for="evDesc">Details <small>(optional)</small></label>
            <textarea name="description" id="evDesc" class="form-control" rows="2"></textarea>
          </div>
          <div class="em-full em-actions">
            <button type="button" class="btn btn-default btn-sm" id="evCancel" style="display:none;">Cancel edit</button>
            <button type="submit" class="btn btn-primary btn-sm" id="evSubmit"><i class="fa fa-check"></i> Publish</button>
          </div>
        </div>
      <?php echo form_close(); ?>
    </div>

    <div class="em-card">
      <h4><i class="fa fa-list"></i> Published in <?php echo $year; ?> <small style="color:#64748b; font-weight:600;">(<?php echo count($events); ?>)</small></h4>
      <?php if (!empty($events)) { ?>
      <div class="table-responsive"><table class="table em-table">
        <tr><th>Date</th><th>Title</th><th>Type</th><th>Location</th><th></th></tr>
        <?php foreach ($events as $it) { $t = $types[$it['type']]; $s = strtotime($it['start']); ?>
        <tr data-id="<?php echo $it['id']; ?>">
          <td style="white-space:nowrap;"><?php echo $em_date($it); ?></td>
          <td><strong><?php echo htmlspecialchars($it['title']); ?></strong></td>
          <td><span class="em-badge" style="background:<?php echo $t[1]; ?>;"><?php echo $t[0]; ?></span></td>
          <td><?php echo $it['location'] !== '' ? htmlspecialchars($it['location']) : '-'; ?></td>
          <td class="em-row-actions">
            <button type="button" class="btn btn-xs btn-default ev-edit" data-ev="<?php echo htmlspecialchars(json_encode(array(
              'id' => $it['id'], 'event_type' => $it['type'], 'title' => $it['title'], 'location' => $it['location'], 'description' => $it['description'],
              'start_date' => date('Y-m-d', $s),
              'end_date' => (!empty($it['end']) && substr($it['end'], 0, 10) > substr($it['start'], 0, 10)) ? substr($it['end'], 0, 10) : '',
              'start_time' => empty($it['all_day']) ? date('H:i', $s) : '',
              'end_time' => (empty($it['all_day']) && !empty($it['end']) && substr($it['end'], 0, 10) === substr($it['start'], 0, 10)) ? date('H:i', strtotime($it['end'])) : '',
            )), ENT_QUOTES); ?>"><i class="fa fa-pencil"></i> Edit</button>
            <?php echo form_open('manager/events/delete_event/' . $it['id'], array('onsubmit' => "return confirm('Remove this event for everyone?');")); ?>
              <input type="hidden" name="year" value="<?php echo $year; ?>">
              <button type="submit" class="btn btn-xs btn-default" style="color:#b91c1c;"><i class="fa fa-trash"></i> Remove</button>
            <?php echo form_close(); ?>
          </td>
        </tr>
        <?php } ?>
      </table></div>
      <?php } else { ?>
      <div class="em-empty">Nothing published for <?php echo $year; ?> yet.</div>
      <?php } ?>
    </div>

    <!-- Awards -->
    <div class="em-card">
      <h4><i class="fa fa-trophy"></i> <span id="awFormTitle">Add an award</span></h4>
      <?php echo form_open('manager/events/save_award', array('id' => 'awForm')); ?>
        <input type="hidden" name="id" value="0">
        <input type="hidden" name="year" value="<?php echo $year; ?>">
        <div class="em-form">
          <div>
            <label for="awUser">Employee</label>
            <select name="user_id" id="awUser" class="form-control" required>
              <option value="">Choose...</option>
              <?php foreach ($users as $u) { ?><option value="<?php echo (int)$u['id']; ?>"><?php echo htmlspecialchars($u['name']); ?></option><?php } ?>
            </select>
          </div>
          <div>
            <label for="awTitle">Award</label>
            <input type="text" name="title" id="awTitle" class="form-control" maxlength="255" required placeholder="e.g. Employee of the Month">
          </div>
          <div>
            <label for="awCat">Category <small>(optional)</small></label>
            <input type="text" name="category" id="awCat" class="form-control" maxlength="100" placeholder="e.g. Sales, Service">
          </div>
          <div>
            <label for="awDate">Date</label>
            <input type="date" name="award_date" id="awDate" class="form-control" required>
          </div>
          <div class="em-full">
            <label for="awDesc">Reason <small>(optional)</small></label>
            <textarea name="description" id="awDesc" class="form-control" rows="2"></textarea>
          </div>
          <div class="em-full em-actions">
            <button type="button" class="btn btn-default btn-sm" id="awCancel" style="display:none;">Cancel edit</button>
            <button type="submit" class="btn btn-primary btn-sm" id="awSubmit"><i class="fa fa-check"></i> Publish</button>
          </div>
        </div>
      <?php echo form_close(); ?>

      <?php if (!empty($awards)) { ?>
      <div class="table-responsive" style="margin-top:14px;"><table class="table em-table">
        <tr><th>Date</th><th>Employee</th><th>Award</th><th>Category</th><th></th></tr>
        <?php foreach ($awards as $it) { ?>
        <tr data-id="<?php echo $it['id']; ?>">
          <td style="white-space:nowrap;"><?php echo date('d M Y', strtotime($it['start'])); ?></td>
          <td><?php echo htmlspecialchars($it['employee']); ?></td>
          <td><strong><?php echo htmlspecialchars($it['title']); ?></strong></td>
          <td><?php echo $it['category'] !== '' ? htmlspecialchars($it['category']) : '-'; ?></td>
          <td class="em-row-actions">
            <button type="button" class="btn btn-xs btn-default aw-edit" data-aw="<?php echo htmlspecialchars(json_encode(array(
              'id' => $it['id'], 'user_id' => $it['user_id'], 'title' => $it['title'], 'category' => $it['category'],
              'award_date' => substr($it['start'], 0, 10), 'description' => $it['description'],
            )), ENT_QUOTES); ?>"><i class="fa fa-pencil"></i> Edit</button>
            <?php echo form_open('manager/events/delete_award/' . $it['id'], array('onsubmit' => "return confirm('Remove this award?');")); ?>
              <input type="hidden" name="year" value="<?php echo $year; ?>">
              <button type="submit" class="btn btn-xs btn-default" style="color:#b91c1c;"><i class="fa fa-trash"></i> Remove</button>
            <?php echo form_close(); ?>
          </td>
        </tr>
        <?php } ?>
      </table></div>
      <?php } else { ?>
      <div class="em-empty" style="margin-top:14px;">No awards published for <?php echo $year; ?> yet.</div>
      <?php } ?>
    </div>
  </section>
</div>

<script>
$(function () {
  // Edit: fill the form above with the row's values; Cancel puts it back to "add".
  function editMode(form, data, titleEl, addText, editText, cancelBtn, submitBtn) {
    var f = form[0];
    if (data) {
      $.each(data, function (k, v) { if (f.elements[k]) { f.elements[k].value = v === null ? '' : v; } });
      titleEl.text(editText); cancelBtn.show(); submitBtn.html('<i class="fa fa-check"></i> Save changes');
      $('html, body').animate({ scrollTop: form.closest('.em-card').offset().top - 80 }, 200);
    } else {
      f.reset(); f.elements.id.value = '0';
      titleEl.text(addText); cancelBtn.hide(); submitBtn.html('<i class="fa fa-check"></i> Publish');
    }
  }
  $('.ev-edit').on('click', function () { editMode($('#evForm'), $(this).data('ev'), $('#evFormTitle'), 'Add a holiday, festival or event', 'Edit event', $('#evCancel'), $('#evSubmit')); });
  $('#evCancel').on('click', function () { editMode($('#evForm'), null, $('#evFormTitle'), 'Add a holiday, festival or event', '', $('#evCancel'), $('#evSubmit')); });
  $('.aw-edit').on('click', function () { editMode($('#awForm'), $(this).data('aw'), $('#awFormTitle'), 'Add an award', 'Edit award', $('#awCancel'), $('#awSubmit')); });
  $('#awCancel').on('click', function () { editMode($('#awForm'), null, $('#awFormTitle'), 'Add an award', '', $('#awCancel'), $('#awSubmit')); });
});
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
