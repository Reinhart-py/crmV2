<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$state_chip = array('open' => array('w', 'Waiting for review'), 'done' => array('ok', 'Done'), 'cancelled' => array('s', 'Discarded'), 'expired' => array('s', 'Expired'));
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<div class="content-wrapper sx">
  <?php echo crm_tabbar('database', 'leads/lead-finder'); ?>
  <section class="sx-head">
    <h1>Lead Finder<small>Leads sent from the Lead Finder app, checked against the database before they are added</small></h1>
  </section>

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <?php if (!$settings['on']) { ?>
      <div class="alert alert-warning">The Lead Finder link is switched off, so Lead Finder cannot send leads.<?php if ($can_link) { ?> Switch it on in <a href="<?php echo base_url(); ?>manager/system/lead-finder">Integrations &gt; Lead Finder link</a>.<?php } else { ?> Ask an administrator to switch it on.<?php } ?></div>
    <?php } ?>

    <div class="sx-card">
      <div class="sx-card-h"><h3><i class="fa fa-inbox"></i> <?php echo $all ? 'All batches' : 'My batches'; ?></h3></div>
      <div class="sx-table-wrap">
        <table class="sx-table sx-cards">
          <thead><tr><th>Batch</th><th>Sent</th><?php if ($all) { ?><th>By</th><?php } ?><th>Search</th><th>New</th><th>Possible</th><th>Duplicates</th><th>State</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($batches as $b) { $sc = $state_chip[$b['state']] ?? array('s', $b['state']); ?>
            <tr>
              <td data-l="Batch" class="mono">LF-<?php echo (int)$b['id']; ?></td>
              <td data-l="Sent" class="nw"><?php echo $h(date('d M Y H:i', strtotime($b['created_at']))); ?></td>
              <?php if ($all) { ?><td data-l="By"><?php echo $h($b['sender_name'] ?: $b['sent_email']); ?></td><?php } ?>
              <td data-l="Search"><?php echo $h(Lead_finder_model::engine_name($b['engine'])); ?> · "<?php echo $h($b['search_query']); ?>"</td>
              <td data-l="New"><?php echo number_format($b['n_new']); ?></td>
              <td data-l="Possible"><?php echo number_format($b['n_possible']); ?></td>
              <td data-l="Duplicates"><?php echo number_format($b['n_dup']); ?></td>
              <td data-l="State"><span class="chip <?php echo $sc[0]; ?>"><?php echo $h($sc[1]); ?></span><?php if ($b['state'] === 'done') { ?><div class="text-muted" style="font-size:12px;"><?php echo (int)$b['n_added'] + (int)$b['n_forced']; ?> added, <?php echo (int)$b['n_merged']; ?> filled in</div><?php } ?></td>
              <td data-l=""><a class="btn btn-sm <?php echo $b['state'] === 'open' ? 'btn-primary' : 'btn-default'; ?>" href="<?php echo base_url(); ?>manager/leads/lead-finder/review/<?php echo (int)$b['id']; ?>"><?php echo $b['state'] === 'open' ? 'Review' : 'View'; ?></a></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
        <?php if (!$batches) { ?><div class="sx-empty">Nothing yet. In Lead Finder, run a search and click <b>Send to CRM</b>.</div><?php } ?>
      </div>
    </div>
    <p class="sx-help"><i class="fa fa-info-circle"></i> Batches waiting for review expire after <?php echo Lead_finder_model::EXPIRE_DAYS; ?> days. Lead details of finished batches are removed after <?php echo Lead_finder_model::KEEP_DAYS; ?> days; the records you added stay in the database.</p>
  </section>
</div>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
