<?php
/*
 * Permission editor. Set before including:
 *   $pe_id        unique element id
 *   $pe_values    key => no_access|read|write (from crm_perm_expand())
 *   $pe_readonly  true to show values only
 *   $pe_name      input name prefix for form posts (default "permissions")
 */
$pe_name = isset($pe_name) ? $pe_name : 'permissions';
$pe_readonly = !empty($pe_readonly);
$pe_values = isset($pe_values) && is_array($pe_values) ? $pe_values : array();
$pe_svg = function ($inner, $size = 16) {
	return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $inner . '</svg>';
};
?>
<div class="pe<?php echo $pe_readonly ? ' is-readonly' : ''; ?>" id="<?php echo $pe_id; ?>" data-pe>
  <div class="pe-toolbar">
    <div class="pe-search">
      <i class="fa fa-search" aria-hidden="true"></i>
      <input type="search" data-pe-search placeholder="Find a permission, e.g. payroll or orders" aria-label="Find a permission">
    </div>
    <div class="pe-summary" aria-live="polite">
      <span><b data-pe-count="write">0</b> Full</span><span>&middot;</span>
      <span><b data-pe-count="read">0</b> View</span><span>&middot;</span>
      <span><b data-pe-count="no_access">0</b> None</span>
    </div>
    <div class="pe-bulk">
      Set all
      <div class="pe-seg pe-seg-sm">
        <button type="button" data-pe-all="no_access">None</button>
        <button type="button" data-pe-all="read">View</button>
        <button type="button" data-pe-all="write">Full</button>
      </div>
    </div>
  </div>

  <div class="pe-layout">
    <nav class="pe-nav" aria-label="Permission groups">
      <?php foreach (crm_perm_catalog() as $gk => $g) { ?>
        <a href="#<?php echo $pe_id . '-' . $gk; ?>" data-pe-nav="<?php echo $gk; ?>"><?php echo $pe_svg($g['icon'], 16); ?><?php echo htmlspecialchars($g['title']); ?><span class="pe-nav-count" data-pe-gcount="<?php echo $gk; ?>"></span></a>
      <?php } ?>
    </nav>

    <div class="pe-groups">
      <?php foreach (crm_perm_catalog() as $gk => $g) { ?>
      <section class="pe-group" id="<?php echo $pe_id . '-' . $gk; ?>" data-pe-group="<?php echo $gk; ?>">
        <div class="pe-group-head">
          <span class="pe-group-ico"><?php echo $pe_svg($g['icon'], 18); ?></span>
          <div class="pe-group-title">
            <h3><?php echo htmlspecialchars($g['title']); ?></h3>
            <p data-pe-gmeta="<?php echo $gk; ?>"></p>
          </div>
          <div class="pe-seg pe-seg-sm" aria-label="Set every <?php echo htmlspecialchars($g['title']); ?> permission">
            <button type="button" data-pe-gset="no_access">None</button>
            <button type="button" data-pe-gset="read">View</button>
            <button type="button" data-pe-gset="write">Full</button>
          </div>
        </div>
        <?php if (!empty($g['note'])) { ?><p class="pe-note"><?php echo htmlspecialchars($g['note']); ?></p><?php } ?>
        <div class="pe-rows">
          <?php foreach ($g['items'] as $k => $it) {
            $v = isset($pe_values[$k]) ? $pe_values[$k] : 'no_access';
            $only_read = isset($it['levels']) && $it['levels'] === 'r';
            $opts = $only_read ? array('no_access' => 'None', 'read' => 'Allow') : array('no_access' => 'None', 'read' => 'View', 'write' => 'Full');
            if ($only_read && $v === 'write') $v = 'read';
          ?>
          <div class="pe-row" data-pe-key="<?php echo $k; ?>" data-pe-text="<?php echo htmlspecialchars(strtolower($it['label'] . ' ' . $it['desc'] . ' ' . $g['title'])); ?>">
            <div class="pe-row-text">
              <span class="pe-label" id="<?php echo $pe_id . '-l-' . $k; ?>"><?php echo htmlspecialchars($it['label']); ?></span>
              <span class="pe-desc"><?php echo htmlspecialchars($it['desc']); ?></span>
            </div>
            <div class="pe-seg" role="radiogroup" aria-labelledby="<?php echo $pe_id . '-l-' . $k; ?>">
              <?php foreach ($opts as $ov => $ol) { ?>
              <label><input type="radio" name="<?php echo $pe_name . '[' . $k . ']'; ?>" value="<?php echo $ov; ?>"<?php echo $v === $ov ? ' checked' : ''; ?><?php echo $pe_readonly ? ' disabled' : ''; ?>><span><?php echo $ol; ?></span></label>
              <?php } ?>
            </div>
          </div>
          <?php } ?>
        </div>
      </section>
      <?php } ?>
      <div class="pe-empty" data-pe-empty>No permission matches your search.</div>
    </div>
  </div>
</div>
