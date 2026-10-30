<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Quality Operations Checklist (QC)</h1>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box" style="border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 4px 16px rgba(0,0,0,0.03); background:#ffffff; overflow:hidden;">
          <div class="box-header" style="background:#f8fafc; border-bottom:1px solid #e2e8f0; padding:18px 24px;">
            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;">
              <div>
                <h3 class="box-title" style="font-size:17px; font-weight:700; color:#1e293b; margin:0;">System Modules &amp; Feature Verification</h3>
                <div style="font-size:12.5px; color:#64748b; margin-top:4px;">
                  Audit checklist for module functionality, routing, and access verification.
                </div>
              </div>
              <div style="display:flex; align-items:center; gap:16px;">
                <div style="text-align:right;">
                  <span id="qcProgressText" style="font-size:14px; font-weight:700; color:#0072f0;"><?php echo $checked_items; ?> / <?php echo $total_items; ?> Verified</span>
                  <div style="width:160px; height:8px; background:#e2e8f0; border-radius:4px; overflow:hidden; margin-top:4px;">
                    <div id="qcProgressBar" style="width:<?php echo $pct; ?>%; height:100%; background:#0072f0; transition:width 0.25s ease;"></div>
                  </div>
                </div>
                <button type="button" class="btn btn-sm btn-default" onclick="toggleAllQcTree(true)" style="border-radius:8px; font-weight:600;"><i class="fa fa-expand"></i> Expand All</button>
                <button type="button" class="btn btn-sm btn-default" onclick="toggleAllQcTree(false)" style="border-radius:8px; font-weight:600;"><i class="fa fa-compress"></i> Collapse All</button>
              </div>
            </div>
          </div>

          <div class="box-body" style="padding:24px;">
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
                      <span class="qc-toggle-icon" id="toggle_icon_<?php echo $pIndex; ?>" style="width:24px; height:24px; border-radius:6px; background:#ffffff; border:1px solid #cbd5e1; display:flex; align-items:center; justify-content:center; font-size:12px; color:#475569; font-weight:700;">+</span>
                      <i class="<?php echo $parent['icon']; ?>" style="font-size:16px; color:#0072f0; width:18px; text-align:center;"></i>
                      <span style="font-size:14.5px; font-weight:700; color:#1e293b;"><?php echo htmlspecialchars($parent['title']); ?></span>
                    </div>
                    <div style="display:flex; align-items:center; gap:12px;">
                      <span class="badge" id="badge_<?php echo $pIndex; ?>" style="background:#e0f2fe; color:#0369a1; font-weight:600; font-size:11px; padding:4px 10px; border-radius:12px;"><?php echo $pChecked; ?> / <?php echo $pTotal; ?></span>
                      <?php if(!empty($parent['url'])){ ?>
                        <a href="<?php echo base_url($parent['url']); ?>" target="_blank" onclick="event.stopPropagation();" class="btn btn-xs btn-default" title="Open section in new tab" style="border-radius:6px; padding:3px 8px; color:#64748b; border:1px solid #cbd5e1;">
                          <i class="fa fa-external-link"></i>
                        </a>
                      <?php } ?>
                    </div>
                  </div>

                  <div class="qc-children-container" id="children_<?php echo $pIndex; ?>" style="display:none; padding:10px 18px 14px 44px; background:#ffffff; border-top:1px solid #f1f5f9;">
                    <table class="table table-hover" style="margin-bottom:0; font-size:13px;">
                      <tbody>
                        <?php foreach($pChildren as $child){ 
                          $isChecked = !empty($checked_map[$child['key']]);
                        ?>
                          <tr id="row_<?php echo $child['key']; ?>">
                            <td style="width:36px; vertical-align:middle; padding:10px 6px;">
                              <input type="checkbox" class="qc-checkbox" data-key="<?php echo $child['key']; ?>" data-parent="<?php echo $pIndex; ?>" <?php echo $isChecked ? 'checked' : ''; ?> onchange="onQcCheckboxChange(this)" style="width:17px; height:17px; cursor:pointer; accent-color:#0072f0;">
                            </td>
                            <td style="vertical-align:middle; padding:10px 8px;">
                              <span class="qc-node-title <?php echo $isChecked ? 'qc-checked-text' : ''; ?>" id="label_<?php echo $child['key']; ?>" style="font-weight:600; color:#334155;"><?php echo htmlspecialchars($child['title']); ?></span>
                              <span style="font-size:11.5px; color:#94a3b8; margin-left:8px;">(<?php echo htmlspecialchars($child['url']); ?>)</span>
                            </td>
                            <td style="width:60px; text-align:right; vertical-align:middle; padding:10px 6px;">
                              <a href="<?php echo base_url($child['url']); ?>" target="_blank" class="btn btn-xs btn-default" title="Open in new tab" style="border-radius:6px; padding:4px 9px; color:#0072f0; border:1px solid #cbd5e1; background:#ffffff;">
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
</style>

<script>
function toggleQcParent(pIndex) {
  var el = document.getElementById('children_' + pIndex);
  var icon = document.getElementById('toggle_icon_' + pIndex);
  if (!el || !icon) return;
  if (el.style.display === 'none' || el.style.display === '') {
    el.style.display = 'block';
    icon.textContent = '-';
  } else {
    el.style.display = 'none';
    icon.textContent = '+';
  }
}

function toggleAllQcTree(expand) {
  var allKids = document.querySelectorAll('.qc-children-container');
  var allIcons = document.querySelectorAll('.qc-toggle-icon');
  for (var i = 0; i < allKids.length; i++) {
    allKids[i].style.display = expand ? 'block' : 'none';
  }
  for (var j = 0; j < allIcons.length; j++) {
    allIcons[j].textContent = expand ? '-' : '+';
  }
}

function onQcCheckboxChange(cb) {
  var key = cb.getAttribute('data-key');
  var pIndex = cb.getAttribute('data-parent');
  var isChecked = cb.checked ? 1 : 0;

  var lbl = document.getElementById('label_' + key);
  if (lbl) {
    if (isChecked) {
      lbl.classList.add('qc-checked-text');
    } else {
      lbl.classList.remove('qc-checked-text');
    }
  }

  updateParentBadge(pIndex);
  updateOverallProgress();

  $.ajax({
    url: '<?php echo base_url(); ?>manager/system/ajax_save_qc',
    type: 'POST',
    data: {
      node_key: key,
      is_checked: isChecked
    },
    dataType: 'json',
    success: function(res) {
    },
    error: function() {
      cb.checked = !isChecked;
      if (lbl) {
        if (!isChecked) lbl.classList.add('qc-checked-text');
        else lbl.classList.remove('qc-checked-text');
      }
      updateParentBadge(pIndex);
      updateOverallProgress();
    }
  });
}

function updateParentBadge(pIndex) {
  var parentBox = document.getElementById('parent_node_' + pIndex);
  if (!parentBox) return;
  var boxes = parentBox.querySelectorAll('.qc-checkbox');
  var total = boxes.length;
  var checked = 0;
  for (var i = 0; i < boxes.length; i++) {
    if (boxes[i].checked) checked++;
  }
  var badge = document.getElementById('badge_' + pIndex);
  if (badge) {
    badge.textContent = checked + ' / ' + total;
  }
}

function updateOverallProgress() {
  var allBoxes = document.querySelectorAll('.qc-checkbox');
  var total = allBoxes.length;
  var checked = 0;
  for (var i = 0; i < allBoxes.length; i++) {
    if (allBoxes[i].checked) checked++;
  }
  var pct = total > 0 ? Math.round((checked / total) * 100) : 0;
  var bar = document.getElementById('qcProgressBar');
  var txt = document.getElementById('qcProgressText');
  if (bar) bar.style.width = pct + '%';
  if (txt) txt.textContent = checked + ' / ' + total + ' Verified (' + pct + '%)';
}
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
