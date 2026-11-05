<?php
$fu_existing = (isset($follow_list) && is_array($follow_list)) ? $follow_list : array();
$fu_statuses = (isset($st_list) && is_array($st_list)) ? $st_list : array();
if (!function_exists('crm_fu_status_options')) {
	function crm_fu_status_options($statuses, $selected = '') {
		$html = '<option value="">Select status</option>';
		$found = ($selected === '');
		foreach ($statuses as $st) {
			$name = (string)$st['status'];
			$is = ($selected !== '' && strcasecmp($name, $selected) === 0);
			if ($is) {
				$found = true;
			}
			$html .= '<option value="' . htmlspecialchars($name) . '"' . ($is ? ' selected' : '') . '>' . htmlspecialchars($name) . '</option>';
		}
		if (!$found) {
			$html .= '<option value="' . htmlspecialchars($selected) . '" selected>' . htmlspecialchars($selected) . '</option>';
		}
		return $html;
	}
}
?>
<div class="crm-fu" data-crm-followups>
  <div class="crm-fu-list">
    <?php foreach ($fu_existing as $li) { ?>
    <div class="crm-fu-row is-saved">
      <input type="hidden" name="flead_id[]" value="<?php echo (int)$li['id']; ?>">
      <div class="crm-fu-field">
        <label>Date</label>
        <input type="text" class="form-control" name="followup_date[]" value="<?php echo htmlspecialchars(simple_date($li['followup_date'])); ?>" readonly>
      </div>
      <div class="crm-fu-field">
        <label>Time</label>
        <input type="text" class="form-control" name="followup_time[]" value="<?php echo htmlspecialchars($li['followup_time']); ?>" readonly>
      </div>
      <div class="crm-fu-field">
        <label>Status</label>
        <select class="form-control" name="followup_status[]"><?php echo crm_fu_status_options($fu_statuses, (string)$li['followup_result']); ?></select>
      </div>
      <div class="crm-fu-field crm-fu-remark">
        <label>Remark</label>
        <input type="text" class="form-control" name="followup_remark[]" value="<?php echo htmlspecialchars($li['followup_remark']); ?>">
      </div>
      <button type="button" class="crm-fu-remove" data-fu-remove title="Remove this follow-up" aria-label="Remove this follow-up">&times;</button>
    </div>
    <?php } ?>
  </div>
  <template class="crm-fu-template">
    <div class="crm-fu-row">
      <input type="hidden" name="flead_id[]" value="0">
      <div class="crm-fu-field">
        <label>Next follow-up date</label>
        <input type="text" class="form-control datepicker2" name="followup_date[]" autocomplete="off" placeholder="DD-MM-YYYY">
      </div>
      <div class="crm-fu-field">
        <label>Time</label>
        <div class="input-group bootstrap-timepicker timepicker">
          <input type="text" class="form-control input-small timepicker" name="followup_time[]" autocomplete="off">
          <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
        </div>
      </div>
      <div class="crm-fu-field">
        <label>Status</label>
        <select class="form-control" name="followup_status[]"><?php echo crm_fu_status_options($fu_statuses); ?></select>
      </div>
      <div class="crm-fu-field crm-fu-remark">
        <label>Remark</label>
        <input type="text" class="form-control" name="followup_remark[]">
      </div>
      <button type="button" class="crm-fu-remove" data-fu-remove title="Remove this follow-up" aria-label="Remove this follow-up">&times;</button>
    </div>
  </template>
  <button type="button" class="crm-fu-add" data-fu-add><span aria-hidden="true">+</span> Add follow-up</button>
</div>
