<?php
/*
 * Record preview: compact header. Title follows the record's stage (Database Record, Lead, Order, Sale, KAM Account),
 * a Database → Lead → Order → Sale strip with the reference numbers, and one next-step button.
 * Same rights, modals and forms as before (#convertLeadModal, #requestLeadModal, #rejectLeadModal, #requestOrderModal).
 */
$return_url = urldecode($this->session->userdata('manager_return_string'));
$sv_ors = isset($p_info['order_request_status']) ? strtolower(trim((string)$p_info['order_request_status'])) : '';
if ($sv_ors === 'none') $sv_ors = '';
$sv_is_kam = (!empty($p_info['pipeline_stage']) && $p_info['pipeline_stage'] === 'kam');
$sv_has_order = (!empty($order_ref_no) || in_array($sv_ors, array('pending', 'approved', 'processed', 'ordered')));
if ($sv_is_kam) $sv_stage = 'kam';
elseif (!empty($is_already_lead) && !empty($sales_ref_no)) $sv_stage = 'sale';
elseif (!empty($is_already_lead) && $sv_has_order) $sv_stage = 'order';
elseif (!empty($is_already_lead)) $sv_stage = 'lead';
else $sv_stage = 'database';
$sv_titles = array('database' => 'Database Record', 'lead' => 'Lead', 'order' => 'Order', 'sale' => 'Sale', 'kam' => 'KAM Account');

// labels beside the company name (same rules as before)
$disp_zone = !empty($p_info['tl_zone']) ? $p_info['tl_zone'] : '';
if (empty($disp_zone) && !empty($p_info['tl_authority'])) {
    if (stripos($p_info['tl_authority'], 'Free Zone') !== false || stripos($p_info['tl_authority'], 'Freezone') !== false || stripos($p_info['tl_authority'], 'DIFC') !== false || stripos($p_info['tl_authority'], 'ADGM') !== false) {
        $disp_zone = 'Freezone';
    } elseif (stripos($p_info['tl_authority'], 'Department of Economic') !== false || stripos($p_info['tl_authority'], 'Municipality') !== false || stripos($p_info['tl_authority'], 'DED') !== false || stripos($p_info['tl_authority'], 'DET') !== false) {
        $disp_zone = 'Mainland';
    }
}

// edit right (unchanged)
$cur_user = (int)$this->session->userdata('manager_id');
$cur_r = (int)$this->session->userdata('manager_role');
$lead_owner_id = !empty($p_info['lead_assign']) ? (int)$p_info['lead_assign'] : (int)$p_info['created_by'];
$is_agent_collab = (!empty($p_info['agents']) && in_array($cur_user, array_map('intval', explode(',', $p_info['agents']))));
$is_locked_for_user = (!empty($is_locked) && $cur_r !== 1 && $cur_r !== 4);
$can_edit = (!$is_locked_for_user) && ($cur_r == 1 || $cur_r == 4 || $cur_user === $lead_owner_id || $is_agent_collab);
$sv_meeting_member = (!empty($m_list) && ($m_list['a1'] == $cur_user || $m_list['a2'] == $cur_user || $m_list['a3'] == $cur_user));

$sv_date = function ($d) { return (!empty($d) && strtotime($d)) ? date('d M Y', strtotime($d)) : ''; };
$sv_pending = (!$sv_is_kam && !empty($is_pending_review));
$sv_steps = array(
    array('database', 'Database', !empty($db_ref_no) ? $db_ref_no : ''),
    array('lead', 'Lead', !empty($lead_ref_no) ? $lead_ref_no : ($sv_pending ? 'Waiting for review' : '')),
    array('order', 'Order', !empty($order_ref_no) ? $order_ref_no : ($sv_has_order ? 'Requested' : '')),
    array('sale', 'Sale', !empty($sales_ref_no) ? $sales_ref_no : ''),
);
$sv_order = array('database' => 0, 'lead' => 1, 'order' => 2, 'sale' => 3);
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-record.css?v=20261106_21">
<div class="svh svh-<?php echo $sv_stage; ?>">
  <div class="svh-top">
    <a href="<?php echo base_url(); ?>manager/<?php echo $return_url; ?>" class="svh-back" title="Back" aria-label="Back"><i class="fa fa-arrow-left"></i></a>
    <div class="svh-id">
      <span class="svh-kind"><?php echo $sv_titles[$sv_stage]; ?></span>
      <h1><?php echo htmlspecialchars(isset($p_info['customer']) ? $p_info['customer'] : ''); ?>
        <?php if (!empty($disp_zone) && in_array(strtolower($disp_zone), array('mainland', 'freezone'))) { ?><span class="svh-tag"><?php echo ucfirst(strtolower($disp_zone)); ?></span><?php } ?>
        <?php if (!empty($p_info['dnd_status']) && strtolower($p_info['dnd_status']) == 'yes') { ?><span class="svh-tag is-red">DNCR</span><?php } ?>
        <?php if (!empty($p_info['company_status']) && strtolower($p_info['company_status']) == 'closed') { ?><span class="svh-tag is-blue">Closed</span><?php } ?>
      </h1>
    </div>
    <div class="svh-acts">
      <?php if ($sv_meeting_member) { ?>
        <a href="<?php echo base_url(); ?>manager/leads/editLimited/<?php echo base64_encode($p_info['id']); ?>/tm" class="btn btn-default"><i class="fa fa-refresh"></i> Update Status</a>
      <?php } ?>
      <?php if ($cur_r === 1) { /* admin only: one-page A4 print */ ?>
        <button type="button" class="btn btn-default rec-print" title="Print or save as PDF (one A4 page)"><i class="fa fa-print"></i> Print</button>
      <?php } ?>
      <?php if ($can_edit) { ?>
        <a href="<?php echo base_url(); ?>manager/leads/edit/<?php echo base64_encode($p_info['id']); ?><?php echo ($action == 'tm') ? '/tm' : ''; ?>" class="btn btn-default"><i class="fa fa-pencil"></i> Edit</a>
      <?php } ?>
      <?php if ($sv_is_kam) { ?>
        <?php /* KAM accounts are past the Database → Lead step: no conversion buttons */ ?>
      <?php } elseif (!empty($is_already_lead)) { ?>
        <?php if (!empty($order_info['id']) && ($sv_stage === 'sale' || $sv_stage === 'order')) { ?>
          <a href="<?php echo base_url(); ?>manager/orders/view/<?php echo $order_info['id']; ?>" class="btn btn-primary svh-next"><i class="fa fa-shopping-cart"></i> View Order</a>
        <?php } elseif ($sv_stage === 'lead') { ?>
          <button type="button" class="btn btn-primary svh-next" data-toggle="modal" data-target="#requestOrderModal"><i class="fa fa-shopping-cart"></i> Request Order</button>
        <?php } ?>
      <?php } elseif (!empty($is_pending_review)) { ?>
        <?php if (!empty($can_review_conversion)) { ?>
          <button type="button" class="btn btn-default svh-reject" data-toggle="modal" data-target="#rejectLeadModal"><i class="fa fa-times"></i> Reject</button>
          <button type="button" class="btn btn-primary svh-next" data-toggle="modal" data-target="#convertLeadModal"><i class="fa fa-check"></i> Approve &amp; Convert</button>
        <?php } ?>
      <?php } elseif (!empty($can_convert_to_lead)) { ?>
        <button type="button" class="btn btn-primary svh-next" data-toggle="modal" data-target="#convertLeadModal"><i class="fa fa-arrow-right"></i> Convert to Lead</button>
      <?php } else { ?>
        <button type="button" class="btn btn-primary svh-next" data-toggle="modal" data-target="#requestLeadModal"><i class="fa fa-paper-plane"></i> Request Lead Conversion</button>
      <?php } ?>
    </div>
  </div>

  <?php if ($sv_is_kam) { ?>
  <div class="svh-note"><i class="fa fa-briefcase"></i> Key account, managed under KAM. Database No. <b><?php echo htmlspecialchars($db_ref_no); ?></b></div>
  <?php } else { ?>
  <ol class="svh-steps" aria-label="Progress">
    <?php foreach ($sv_steps as $st) {
      $i = $sv_order[$st[0]]; $cur = $sv_order[$sv_stage];
      $cls = $i < $cur ? 'is-done' : ($i === $cur ? 'is-now' : '');
      if ($sv_pending && $st[0] === 'lead') $cls = 'is-wait';
      if ($st[0] === 'order' && $sv_stage === 'order' && empty($order_ref_no)) $cls = 'is-wait';
    ?>
      <li class="<?php echo $cls; ?>"<?php echo $i === $cur ? ' aria-current="step"' : ''; ?>>
        <span class="svh-dot"><?php if ($cls === 'is-done') { ?><i class="fa fa-check"></i><?php } elseif ($cls === 'is-wait') { ?><i class="fa fa-clock-o"></i><?php } else { echo $i + 1; } ?></span>
        <span class="svh-st"><b><?php echo $st[1]; ?></b><small><?php echo $st[2] !== '' ? htmlspecialchars($st[2]) : '&mdash;'; ?></small></span>
      </li>
    <?php } ?>
  </ol>
  <?php if ($sv_pending) { ?>
  <div class="svh-note is-wait"><i class="fa fa-clock-o"></i> Lead conversion requested<?php echo !empty($p_info['requested_by_name']) ? ' by <b>' . htmlspecialchars($p_info['requested_by_name']) . '</b>' : ''; ?><?php echo $sv_date($p_info['conversion_requested_at']) ? ' on ' . $sv_date($p_info['conversion_requested_at']) : ''; ?>. Waiting for Back Office review.</div>
  <?php } ?>
  <?php } ?>
  <?php if (!empty($is_locked_for_user)) { ?>
  <div class="svh-note"><i class="fa fa-lock"></i> Locked while the order is in progress. Only Back Office can edit it.</div>
  <?php } ?>
</div>
<script defer src="<?php echo base_url(); ?>assets/dist/js/crm-record.js?v=20261106_21"></script>
