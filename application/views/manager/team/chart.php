<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-team.css?v=20261106_5">

<div class="content-wrapper px-4">
<?php echo crm_mz_tabbar(); ?>
  <section class="content-header">
    <h1><?php echo htmlspecialchars($info['page_heading']); ?></h1>
  </section>

  <section class="content">
    <div class="tm">
      <div class="tc" id="tc">
        <div class="tc-bar">
          <div class="tc-sum" aria-live="polite"></div>
          <div class="tc-find">
            <i class="fa fa-search" aria-hidden="true"></i>
            <input type="search" placeholder="Find a person…  ( / )" aria-label="Find a person on the chart" autocomplete="off">
            <div class="tc-res" hidden></div>
          </div>
          <div class="tm-seg" role="group" aria-label="View">
            <button type="button" data-mode="chart" title="Chart"><i class="fa fa-sitemap"></i><span class="hide-sm"> Chart</span></button>
            <button type="button" data-mode="list" title="List"><i class="fa fa-list-ul"></i><span class="hide-sm"> List</span></button>
          </div>
          <button type="button" class="tm-btn hide-sm" data-act="expand" title="Open every branch"><i class="fa fa-plus-square-o"></i> Expand all</button>
          <button type="button" class="tm-btn hide-sm" data-act="collapse" title="Close every branch"><i class="fa fa-minus-square-o"></i> Collapse</button>
          <button type="button" class="tm-btn ic hide-sm" data-act="full" title="Full screen"><i class="fa fa-arrows-alt"></i></button>
        </div>
        <div class="tc-crumb" hidden></div>
        <div class="tc-main">
          <div class="tc-vp">
            <div class="tc-cv"></div>
          </div>
          <div class="tc-zoom" role="group" aria-label="Zoom">
            <button type="button" data-act="zout" title="Zoom out"><i class="fa fa-minus"></i></button>
            <span>100%</span>
            <button type="button" data-act="zin" title="Zoom in"><i class="fa fa-plus"></i></button>
            <button type="button" data-act="fit" title="Fit to screen"><i class="fa fa-expand"></i></button>
          </div>
          <aside class="tc-side" aria-label="Person details"></aside>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
window.TM_CHART = {
  people: <?php echo json_encode($people, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
  me: <?php echo (int)$me; ?>,
  focus: <?php echo (int)$focus; ?>,
  seeAll: <?php echo $see_all ? 'true' : 'false'; ?>,
  base: <?php echo json_encode(base_url()); ?>
};
</script>
<script src="<?php echo base_url(); ?>assets/dist/js/crm-team-chart.js?v=20261106_3"></script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
