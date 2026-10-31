<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>

<style>
.qc-page-wrapper {
  background: #f8fafc;
  min-height: calc(100vh - 80px);
  padding: 16px 20px 60px 20px;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}
.qc-top-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
  margin-bottom: 16px;
  padding-bottom: 12px;
  border-bottom: 1px solid #e2e8f0;
}
.qc-page-title {
  font-size: 20px;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.02em;
  margin: 0;
}
.qc-progress-card {
  display: flex;
  align-items: center;
  gap: 14px;
  background: #ffffff;
  padding: 8px 16px;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.qc-progress-bar-wrap {
  width: 140px;
  height: 8px;
  background: #e2e8f0;
  border-radius: 4px;
  overflow: hidden;
}
.qc-progress-bar-fill {
  height: 100%;
  background: #0072f0;
  transition: width 0.25s ease;
}
.qc-section-card {
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 1px 4px rgba(0,0,0,0.03);
  margin-bottom: 18px;
}
.qc-section-header {
  padding: 12px 16px;
  background: #f8fafc;
  border-bottom: 1.5px solid #cbd5e1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
}
.qc-section-header-left {
  display: flex;
  align-items: center;
  gap: 10px;
}
.qc-section-title {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
}
.qc-table-responsive {
  width: 100%;
  overflow-x: auto;
}
.qc-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 12.5px;
  text-align: left;
}
.qc-table th {
  background: #ffffff;
  color: #475569;
  font-weight: 700;
  font-size: 11px;
  letter-spacing: 0.03em;
  text-transform: uppercase;
  padding: 8px 10px;
  border-bottom: 1px solid #e2e8f0;
  border-right: 1px solid #f1f5f9;
  text-align: center;
  vertical-align: middle;
}
.qc-table th.th-page {
  text-align: left;
  min-width: 220px;
}
.qc-table th.th-select-all {
  width: 70px;
}
.qc-table th.th-chk {
  width: 78px;
}
.qc-table th.th-remarks {
  width: 90px;
}
.qc-table td {
  padding: 8px 10px;
  border-bottom: 1px solid #f1f5f9;
  border-right: 1px solid #f1f5f9;
  color: #1e293b;
  vertical-align: middle;
  text-align: center;
}
.qc-table td.td-page {
  text-align: left;
}
.qc-table tr:hover td {
  background: #f8fafc;
}
.qc-table tr.row-all-checked td {
  background: #f0fdf4;
}
.qc-table td:last-child, .qc-table th:last-child {
  border-right: none;
}
.qc-page-link {
  color: #0072f0;
  font-weight: 600;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: color 0.12s ease;
}
.qc-page-link:hover {
  color: #0056b3;
  text-decoration: underline;
}
.qc-route-sub {
  display: block;
  font-size: 10.5px;
  color: #64748b;
  margin-top: 2px;
}
.qc-chk-box {
  width: 16px;
  height: 16px;
  cursor: pointer;
  accent-color: #0072f0;
  vertical-align: middle;
  margin: 0;
}
.btn-remarks-trigger {
  padding: 3px 8px;
  font-size: 11px;
  font-weight: 600;
  border-radius: 6px;
  border: 1px solid #cbd5e1;
  background: #ffffff;
  color: #334155;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  transition: all 0.15s ease;
}
.btn-remarks-trigger:hover {
  background: #f1f5f9;
  border-color: #94a3b8;
}
.btn-remarks-trigger.has-remarks {
  background: #eff6ff;
  border-color: #93c5fd;
  color: #0072f0;
}
.qc-modal-overlay {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(15, 23, 42, 0.45);
  backdrop-filter: blur(4px);
  z-index: 999999;
  align-items: center;
  justify-content: center;
}
.qc-modal-card {
  background: #ffffff;
  border-radius: 14px;
  width: 520px;
  max-width: 92vw;
  box-shadow: 0 20px 45px rgba(0,0,0,0.18);
  border: 1px solid #e2e8f0;
  overflow: hidden;
}
.qc-modal-header {
  padding: 14px 18px;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.qc-modal-title {
  margin: 0;
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
}
.qc-modal-body {
  padding: 16px 18px;
}
.qc-modal-footer {
  padding: 12px 18px;
  background: #f8fafc;
  border-top: 1px solid #e2e8f0;
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}
</style>

<div class="content-wrapper qc-page-wrapper">
  <div class="qc-top-header">
    <div>
      <h1 class="qc-page-title">Quality Check</h1>
    </div>

    <div class="qc-progress-card">
      <div style="font-size:12.5px; font-weight:700; color:#0f172a;" id="qcStatsText">
        <span id="qcPassedChecks"><?php echo $passed_checks; ?></span> / <span id="qcTotalChecks"><?php echo $total_checks; ?></span> Checks Passed (<span id="qcPct"><?php echo $pct; ?></span>%)
      </div>
      <div class="qc-progress-bar-wrap">
        <div class="qc-progress-bar-fill" id="qcProgressFill" style="width:<?php echo $pct; ?>%;"></div>
      </div>
    </div>
  </div>

  <div>
    <?php 
    $section_svgs = array(
      'm_dashboard' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#0072f0;"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="5" rx="1"></rect><rect x="14" y="12" width="7" height="9" rx="1"></rect><rect x="3" y="16" width="7" height="5" rx="1"></rect></svg>',
      'm_my_zone' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#0072f0;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>',
      'm_team' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#0072f0;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
      'm_backoffice' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#0072f0;"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>',
      'm_kam' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#0072f0;"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
      'm_hr' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#0072f0;"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><line x1="15" y1="8" x2="17" y2="8"></line><line x1="15" y1="12" x2="17" y2="12"></line><line x1="7" y1="16" x2="17" y2="16"></line></svg>',
      'm_calls_live' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#0072f0;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>',
      'm_reports' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#0072f0;"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>',
      'm_events' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#0072f0;"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>',
      'm_system' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#0072f0;"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>',
      'm_account' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#0072f0;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>'
    );
    ?>

    <?php if(!empty($tree)){ foreach($tree as $pIndex => $parent){ 
      $pChildren = !empty($parent['children']) ? $parent['children'] : array();
      $child_keys = array();
      $sec_passed = 0;
      $sec_total = count($pChildren) * 7;
      foreach ($pChildren as $c) {
        $child_keys[] = $c['key'];
        if (!empty($checked_map[$c['key']])) {
          $r = $checked_map[$c['key']];
          $sec_passed += ($r['chk_ui'] + $r['chk_ux'] + $r['chk_func'] + $r['chk_nav'] + $r['chk_data'] + $r['chk_perm'] + $r['chk_err']);
        }
      }
      $keys_json = htmlspecialchars(json_encode($child_keys), ENT_QUOTES, 'UTF-8');
      $parent_svg = isset($section_svgs[$parent['key']]) ? $section_svgs[$parent['key']] : '<i class="'.$parent['icon'].'" style="color:#0072f0;"></i>';
    ?>
      <div class="qc-section-card" id="section_card_<?php echo $pIndex; ?>">
        <div class="qc-section-header">
          <div class="qc-section-header-left">
            <span><?php echo $parent_svg; ?></span>
            <span class="qc-section-title"><?php echo htmlspecialchars($parent['title']); ?></span>
            <span class="badge" style="background:#e0f2fe; color:#0369a1; font-weight:700; font-size:11px; padding:3px 8px; border-radius:10px;">
              <?php echo count($pChildren); ?> Pages
            </span>
          </div>

          <div style="display:flex; align-items:center; gap:12px;">
            <label style="display:inline-flex; align-items:center; gap:6px; margin:0; cursor:pointer; font-size:12px; font-weight:600; color:#475569;">
              <input type="checkbox" class="qc-chk-box qc-sec-select-all" data-sec="<?php echo $pIndex; ?>" data-keys='<?php echo $keys_json; ?>' onchange="toggleSectionSelectAll(this)">
              <span>Select All Section</span>
            </label>
            <?php if(!empty($parent['url'])){ ?>
              <a href="<?php echo (strpos($parent['url'], 'http') === 0) ? $parent['url'] : base_url($parent['url']); ?>" target="_blank" class="btn btn-xs btn-default" title="Open Section in new tab" style="border-radius:6px; padding:3px 8px; color:#64748b; border:1px solid #cbd5e1;">
                <i class="fa fa-external-link"></i>
              </a>
            <?php } ?>
          </div>
        </div>

        <div class="qc-table-responsive">
          <table class="qc-table" id="qc_table_<?php echo $pIndex; ?>">
            <thead>
              <tr>
                <th class="th-select-all">All</th>
                <th class="th-page">Page / Route</th>
                <th class="th-chk">UI</th>
                <th class="th-chk">UX</th>
                <th class="th-chk">Func</th>
                <th class="th-chk">Nav</th>
                <th class="th-chk">Data</th>
                <th class="th-chk">Perm</th>
                <th class="th-chk">Error</th>
                <th class="th-remarks">Remarks</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($pChildren as $child){ 
                $k = $child['key'];
                $row_data = !empty($checked_map[$k]) ? $checked_map[$k] : array('chk_ui'=>0,'chk_ux'=>0,'chk_func'=>0,'chk_nav'=>0,'chk_data'=>0,'chk_perm'=>0,'chk_err'=>0,'remarks'=>'','is_checked'=>0);
                $is_row_all = ($row_data['chk_ui'] && $row_data['chk_ux'] && $row_data['chk_func'] && $row_data['chk_nav'] && $row_data['chk_data'] && $row_data['chk_perm'] && $row_data['chk_err']);
                $has_remarks = !empty($row_data['remarks']);
                $target_url = (strpos($child['url'], 'http') === 0) ? $child['url'] : base_url($child['url']);
              ?>
                <tr id="row_<?php echo $k; ?>" class="<?php echo $is_row_all ? 'row-all-checked' : ''; ?>" data-key="<?php echo $k; ?>" data-sec="<?php echo $pIndex; ?>">
                  <td>
                    <input type="checkbox" class="qc-chk-box qc-row-select-all" data-key="<?php echo $k; ?>" <?php echo $is_row_all ? 'checked' : ''; ?> onchange="toggleRowSelectAll(this)" title="Toggle all checks for this page">
                  </td>
                  <td class="td-page">
                    <a href="<?php echo $target_url; ?>" target="_blank" class="qc-page-link" title="Open page in new tab">
                      <span><?php echo htmlspecialchars($child['title']); ?></span>
                      <i class="fa fa-external-link" style="font-size:11px; opacity:0.75;"></i>
                    </a>
                    <span class="qc-route-sub"><?php echo htmlspecialchars($child['url']); ?></span>
                  </td>
                  <td>
                    <input type="checkbox" class="qc-chk-box qc-chk-single" data-key="<?php echo $k; ?>" data-field="chk_ui" <?php echo $row_data['chk_ui'] ? 'checked' : ''; ?> onchange="onSingleCheckChange(this)">
                  </td>
                  <td>
                    <input type="checkbox" class="qc-chk-box qc-chk-single" data-key="<?php echo $k; ?>" data-field="chk_ux" <?php echo $row_data['chk_ux'] ? 'checked' : ''; ?> onchange="onSingleCheckChange(this)">
                  </td>
                  <td>
                    <input type="checkbox" class="qc-chk-box qc-chk-single" data-key="<?php echo $k; ?>" data-field="chk_func" <?php echo $row_data['chk_func'] ? 'checked' : ''; ?> onchange="onSingleCheckChange(this)">
                  </td>
                  <td>
                    <input type="checkbox" class="qc-chk-box qc-chk-single" data-key="<?php echo $k; ?>" data-field="chk_nav" <?php echo $row_data['chk_nav'] ? 'checked' : ''; ?> onchange="onSingleCheckChange(this)">
                  </td>
                  <td>
                    <input type="checkbox" class="qc-chk-box qc-chk-single" data-key="<?php echo $k; ?>" data-field="chk_data" <?php echo $row_data['chk_data'] ? 'checked' : ''; ?> onchange="onSingleCheckChange(this)">
                  </td>
                  <td>
                    <input type="checkbox" class="qc-chk-box qc-chk-single" data-key="<?php echo $k; ?>" data-field="chk_perm" <?php echo $row_data['chk_perm'] ? 'checked' : ''; ?> onchange="onSingleCheckChange(this)">
                  </td>
                  <td>
                    <input type="checkbox" class="qc-chk-box qc-chk-single" data-key="<?php echo $k; ?>" data-field="chk_err" <?php echo $row_data['chk_err'] ? 'checked' : ''; ?> onchange="onSingleCheckChange(this)">
                  </td>
                  <td>
                    <button type="button" class="btn-remarks-trigger <?php echo $has_remarks ? 'has-remarks' : ''; ?>" id="btn_remarks_<?php echo $k; ?>" data-key="<?php echo $k; ?>" data-title="<?php echo htmlspecialchars($child['title'], ENT_QUOTES, 'UTF-8'); ?>" data-remarks="<?php echo htmlspecialchars($row_data['remarks'], ENT_QUOTES, 'UTF-8'); ?>" onclick="openRemarksModal(this)">
                      <i class="fa fa-comment-o"></i>
                      <span>Remarks</span>
                    </button>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php } } ?>
  </div>
</div>

<div class="qc-modal-overlay" id="qcRemarksModal" onclick="closeRemarksModal(event)">
  <div class="qc-modal-card" onclick="event.stopPropagation();">
    <div class="qc-modal-header">
      <h3 class="qc-modal-title" id="qcModalTitle">Page Remarks</h3>
      <button type="button" class="close" onclick="closeRemarksModal()" style="font-size:20px; line-height:1; border:none; background:transparent;">&times;</button>
    </div>
    <div class="qc-modal-body">
      <input type="hidden" id="modalRemarkNodeKey" value="">
      <label style="font-size:12px; font-weight:600; color:#475569; display:block; margin-bottom:6px;">Observations, Bug Notes, or Review Remarks:</label>
      <textarea id="modalRemarkText" rows="6" class="form-control" style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:10px; font-size:13px; outline:none; resize:vertical;" placeholder="Write observations about UI responsiveness, functionality, UX, or errors encountered..."></textarea>
    </div>
    <div class="qc-modal-footer">
      <button type="button" class="btn btn-default" onclick="closeRemarksModal()" style="border-radius:6px; font-size:12.5px; font-weight:600;">Cancel</button>
      <button type="button" class="btn btn-primary" onclick="saveCurrentRemarks()" id="btnSaveRemarks" style="border-radius:6px; font-size:12.5px; font-weight:600; background:#0072f0; border-color:#0072f0;">Save Remarks</button>
    </div>
  </div>
</div>

<script>
var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

function updateQcProgressBar() {
  var total = $('.qc-chk-single').length;
  var passed = $('.qc-chk-single:checked').length;
  var pct = total > 0 ? Math.round((passed / total) * 100) : 0;
  $('#qcPassedChecks').text(passed);
  $('#qcTotalChecks').text(total);
  $('#qcPct').text(pct);
  $('#qcProgressFill').css('width', pct + '%');
}

function onSingleCheckChange(el) {
  var $el = $(el);
  var nodeKey = $el.data('key');
  var field = $el.data('field');
  var isChecked = $el.is(':checked') ? 1 : 0;

  var $row = $('#row_' + nodeKey);
  var rowSingles = $row.find('.qc-chk-single');
  var rowChecked = $row.find('.qc-chk-single:checked');
  var isAll = (rowSingles.length === rowChecked.length);

  $row.find('.qc-row-select-all').prop('checked', isAll);
  if (isAll) {
    $row.addClass('row-all-checked');
  } else {
    $row.removeClass('row-all-checked');
  }

  updateQcProgressBar();

  var postData = {
    node_key: nodeKey,
    chk_field: field,
    is_checked: isChecked
  };
  postData[csrfName] = csrfHash;

  $.ajax({
    url: '<?php echo base_url("manager/system/ajax_save_qc"); ?>',
    type: 'POST',
    dataType: 'json',
    data: postData
  });
}

function toggleRowSelectAll(el) {
  var $el = $(el);
  var nodeKey = $el.data('key');
  var isChecked = $el.is(':checked') ? 1 : 0;

  var $row = $('#row_' + nodeKey);
  $row.find('.qc-chk-single').prop('checked', isChecked === 1);

  if (isChecked === 1) {
    $row.addClass('row-all-checked');
  } else {
    $row.removeClass('row-all-checked');
  }

  updateQcProgressBar();

  var postData = {
    node_key: nodeKey,
    select_all_row: 1,
    is_checked: isChecked
  };
  postData[csrfName] = csrfHash;

  $.ajax({
    url: '<?php echo base_url("manager/system/ajax_save_qc"); ?>',
    type: 'POST',
    dataType: 'json',
    data: postData
  });
}

function toggleSectionSelectAll(el) {
  var $el = $(el);
  var secIdx = $el.data('sec');
  var keys = $el.data('keys');
  var isChecked = $el.is(':checked') ? 1 : 0;

  var $card = $('#section_card_' + secIdx);
  $card.find('.qc-chk-single').prop('checked', isChecked === 1);
  $card.find('.qc-row-select-all').prop('checked', isChecked === 1);

  if (isChecked === 1) {
    $card.find('tbody tr').addClass('row-all-checked');
  } else {
    $card.find('tbody tr').removeClass('row-all-checked');
  }

  updateQcProgressBar();

  var postData = {
    select_all_section: 1,
    keys: keys,
    is_checked: isChecked
  };
  postData[csrfName] = csrfHash;

  $.ajax({
    url: '<?php echo base_url("manager/system/ajax_save_qc"); ?>',
    type: 'POST',
    dataType: 'json',
    data: postData
  });
}

var currentActiveRemarksBtn = null;

function openRemarksModal(btn) {
  currentActiveRemarksBtn = $(btn);
  var key = currentActiveRemarksBtn.data('key');
  var title = currentActiveRemarksBtn.data('title');
  var remarks = currentActiveRemarksBtn.data('remarks') || '';

  $('#modalRemarkNodeKey').val(key);
  $('#qcModalTitle').text('QC Remarks: ' + title);
  $('#modalRemarkText').val(remarks);
  $('#qcRemarksModal').css('display', 'flex');
  setTimeout(function() {
    $('#modalRemarkText').focus();
  }, 100);
}

function closeRemarksModal() {
  $('#qcRemarksModal').hide();
  currentActiveRemarksBtn = null;
}

function saveCurrentRemarks() {
  var key = $('#modalRemarkNodeKey').val();
  var remarks = $('#modalRemarkText').val().trim();

  var postData = {
    node_key: key,
    remarks: remarks
  };
  postData[csrfName] = csrfHash;

  $('#btnSaveRemarks').prop('disabled', true).text('Saving...');

  $.ajax({
    url: '<?php echo base_url("manager/system/ajax_save_qc_remarks"); ?>',
    type: 'POST',
    dataType: 'json',
    data: postData,
    success: function(res) {
      $('#btnSaveRemarks').prop('disabled', false).text('Save Remarks');
      if (currentActiveRemarksBtn) {
        currentActiveRemarksBtn.data('remarks', remarks);
        if (remarks.length > 0) {
          currentActiveRemarksBtn.addClass('has-remarks');
        } else {
          currentActiveRemarksBtn.removeClass('has-remarks');
        }
      }
      closeRemarksModal();
    },
    error: function() {
      $('#btnSaveRemarks').prop('disabled', false).text('Save Remarks');
      alert('Failed to save remarks. Please check network connection.');
    }
  });
}
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
