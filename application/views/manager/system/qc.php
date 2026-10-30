<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>

<div class="content-wrapper">
  <section class="content-header" style="padding-bottom: 14px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
      <h1 style="margin:0; font-size:22px; font-weight:700; color:#0f172a;">Quality Check</h1>
      <div style="text-align:right;">
        <span id="qcProgressText" style="font-size:14px; font-weight:700; color:#0072f0;"><?php echo $checked_items; ?> / <?php echo $total_items; ?> Verified (<?php echo $pct; ?>%)</span>
        <div style="width:180px; height:8px; background:#e2e8f0; border-radius:4px; overflow:hidden; margin-top:4px;">
          <div id="qcProgressBar" style="width:<?php echo $pct; ?>%; height:100%; background:#0072f0; transition:width 0.25s ease;"></div>
        </div>
      </div>
    </div>
  </section>

  <section class="content" style="padding-top:0;">
    <div class="row">
      <div class="col-xs-12">
        <div class="box" style="border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 4px rgba(0,0,0,0.03); background:#ffffff; overflow:hidden;">
          <div class="box-body" style="padding:20px;">
            <div class="qc-tree-container">
              <?php if(!empty($tree)){ foreach($tree as $pIndex => $parent){ 
                $pChildren = !empty($parent['children']) ? $parent['children'] : array();
                $pTotal = count($pChildren);
                $pChecked = 0;
                foreach($pChildren as $c){
                  if(!empty($checked_map[$c['key']])) $pChecked++;
                }
              ?>
                <div class="qc-parent-node" id="parent_node_<?php echo $pIndex; ?>" style="border:1px solid #e2e8f0; border-radius:10px; margin-bottom:12px; overflow:hidden; background:#ffffff;">
                  <div class="qc-parent-header" onclick="toggleQcParent('<?php echo $pIndex; ?>')" style="padding:14px 18px; background:#f8fafc; display:flex; align-items:center; justify-content:space-between; cursor:pointer; user-select:none;">
                    <div style="display:flex; align-items:center; gap:12px;">
                      <span class="qc-toggle-icon" id="toggle_icon_<?php echo $pIndex; ?>" style="width:24px; height:24px; border-radius:6px; background:#ffffff; border:1px solid #cbd5e1; display:flex; align-items:center; justify-content:center; font-size:12px; color:#475569; font-weight:700;">&minus;</span>
                      <i class="<?php echo $parent['icon']; ?>" style="font-size:16px; color:#0072f0; width:18px; text-align:center;"></i>
                      <span style="font-size:14.5px; font-weight:700; color:#1e293b;"><?php echo htmlspecialchars($parent['title']); ?></span>
                    </div>
                    <div style="display:flex; align-items:center; gap:12px;">
                      <span class="badge" id="badge_<?php echo $pIndex; ?>" style="background:#e0f2fe; color:#0369a1; font-weight:600; font-size:11px; padding:4px 10px; border-radius:12px;"><?php echo $pChecked; ?> / <?php echo $pTotal; ?></span>
                      <?php if(!empty($parent['url'])){ ?>
                        <a href="<?php echo (strpos($parent['url'], 'http') === 0) ? $parent['url'] : base_url($parent['url']); ?>" target="_blank" onclick="event.stopPropagation();" class="btn btn-xs btn-default" title="Open section in new tab" style="border-radius:6px; padding:3px 8px; color:#64748b; border:1px solid #cbd5e1;">
                          <i class="fa fa-external-link"></i>
                        </a>
                      <?php } ?>
                    </div>
                  </div>

                  <div class="qc-children-container" id="children_<?php echo $pIndex; ?>" style="display:block; padding:10px 18px 14px 44px; background:#ffffff; border-top:1px solid #f1f5f9;">
                    <table class="table table-hover" style="margin-bottom:0; font-size:13px;">
                      <tbody>
                        <?php foreach($pChildren as $child){ 
                          $isChecked = !empty($checked_map[$child['key']]);
                        ?>
                          <tr id="row_<?php echo $child['key']; ?>">
                            <td style="width:36px; vertical-align:middle; padding:8px 6px;">
                              <input type="checkbox" class="qc-checkbox" data-key="<?php echo $child['key']; ?>" data-parent="<?php echo $pIndex; ?>" <?php echo $isChecked ? 'checked' : ''; ?> onchange="onQcCheckboxChange(this)" style="width:17px; height:17px; cursor:pointer; accent-color:#0072f0;">
                            </td>
                            <td style="vertical-align:middle; padding:8px 8px;">
                              <span class="qc-node-title <?php echo $isChecked ? 'qc-checked-text' : ''; ?>" id="label_<?php echo $child['key']; ?>" style="font-weight:600; color:#334155;"><?php echo htmlspecialchars($child['title']); ?></span>
                              <span style="font-size:11.5px; color:#94a3b8; margin-left:8px;">(<?php echo htmlspecialchars($child['url']); ?>)</span>
                            </td>
                            <td style="width:60px; text-align:right; vertical-align:middle; padding:8px 6px;">
                              <a href="<?php echo (strpos($child['url'], 'http') === 0) ? $child['url'] : base_url($child['url']); ?>" target="_blank" class="btn btn-xs btn-default" title="Open in new tab" style="border-radius:6px; padding:4px 9px; color:#0072f0; border:1px solid #cbd5e1; background:#ffffff;">
                                <i class="fa fa-external-link"></i>
                              </a>
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
        </div>
      </div>
    </div>
  </section>
</div>

<style>
.qc-checked-text {
  text-decoration: line-through;
  color: #94a3b8 !important;
}
.qc-parent-header:hover {
  background: #f1f5f9 !important;
}
.qc-checkbox:hover {
  transform: scale(1.08);
}
</style>

<script>
function toggleQcParent(pIndex) {
  var c = $('#children_' + pIndex);
  var icon = $('#toggle_icon_' + pIndex);
  if (c.is(':visible')) {
    c.slideUp(150);
    icon.html('+');
  } else {
    c.slideDown(150);
    icon.html('&minus;');
  }
}

function onQcCheckboxChange(el) {
  var nodeKey = $(el).data('key');
  var pIndex = $(el).data('parent');
  var isChecked = $(el).is(':checked') ? 1 : 0;
  var label = $('#label_' + nodeKey);

  if (isChecked) {
    label.addClass('qc-checked-text');
  } else {
    label.removeClass('qc-checked-text');
  }

  var pContainer = $('#children_' + pIndex);
  var total = pContainer.find('.qc-checkbox').length;
  var checked = pContainer.find('.qc-checkbox:checked').length;
  $('#badge_' + pIndex).text(checked + ' / ' + total);

  var allTotal = $('.qc-checkbox').length;
  var allChecked = $('.qc-checkbox:checked').length;
  var pct = allTotal > 0 ? Math.round((allChecked / allTotal) * 100) : 0;
  $('#qcProgressText').text(allChecked + ' / ' + allTotal + ' Verified (' + pct + '%)');
  $('#qcProgressBar').css('width', pct + '%');

  $.ajax({
    url: '<?php echo base_url("manager/system/ajax_save_qc"); ?>',
    type: 'POST',
    dataType: 'json',
    data: {
      node_key: nodeKey,
      is_checked: isChecked,
      csrf_test_name: '<?php echo $this->security->get_csrf_hash(); ?>'
    }
  });
}
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
