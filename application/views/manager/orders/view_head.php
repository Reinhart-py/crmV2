<?php
/*
 * Order / Sale preview: same compact header as the record preview (title by stage, Database → Lead → Order → Sale strip).
 * Same buttons, rights and modals as before (#approveOrderModal, #rejectOrderModal, #updateStatusModal, #assignOperationModal, #convertToSalesModal).
 */
$back_url = base_url('manager/orders');
if (empty($can_edit)) {
    $back_url = ($order['status'] === 'Sales') ? base_url('manager/sales') : base_url('manager/orders/my');
}
$ov_sale = ($order['status'] === 'Sales' || !empty($order['sales_ref_no']));
$ov_name = $order['client_name'] ?: ($order['lead_customer'] ?: 'Client #' . $order['client_id']);
$st = $order['status'];
$ov_tag = 'is-blue';
if (in_array($st, array('Completed', 'Fulfilled', 'Approved', 'Sales'))) $ov_tag = 'is-green';
elseif (in_array($st, array('Pending Sales Approval'))) $ov_tag = 'is-amber';
elseif (in_array($st, array('Cancelled', 'Rejected', 'Lost'))) $ov_tag = 'is-red';
$ov_admin = ((int)$this->session->userdata('manager_role') === 1);
$ov_steps = array(
    array('Database', $order['db_ref_no'] ?: '', 'is-done'),
    array('Lead', $order['lead_ref_no'] ?: '', 'is-done'),
    array('Order', $order['order_no'] ?: '', $ov_sale ? 'is-done' : 'is-now'),
    array('Sale', $order['sales_ref_no'] ?: ($ov_sale ? '' : 'Pending'), $ov_sale ? 'is-now' : ''),
);
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-record.css?v=20261106_30">
<div class="svh">
  <div class="svh-top">
    <a href="<?php echo $back_url; ?>" class="svh-back" title="Back" aria-label="Back"><i class="fa fa-arrow-left"></i></a>
    <div class="svh-id">
      <span class="svh-kind"><?php echo $ov_sale ? 'Sale' : 'Order'; ?></span>
      <h1><?php echo htmlspecialchars($ov_name); ?>
        <span class="svh-tag <?php echo $ov_tag; ?>"><?php echo htmlspecialchars($st); ?></span>
        <?php if (!empty($order['vertical_code'])) { ?><span class="svh-tag"><?php echo htmlspecialchars(strtoupper($order['vertical_code'])); ?></span><?php } ?>
      </h1>
    </div>
    <div class="svh-acts">
      <?php if ($ov_admin) { /* admin only: one-page A4 print */ ?>
        <button type="button" class="btn btn-default rec-print" title="Print or save as PDF (one A4 page)"><i class="fa fa-print"></i> Print</button>
      <?php } ?>
      <?php if (!empty($can_edit)) { ?>
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#updateStatusModal"><i class="fa fa-refresh"></i> Update Status</button>
      <?php } ?>
      <?php if (!empty($can_assign_operation)) { ?>
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#assignOperationModal"><i class="fa fa-user-plus"></i> Assign to Operations</button>
      <?php } ?>
      <?php if (!empty($can_approve) && ($order['approval_status'] === 'pending' || $order['status'] === 'Pending Sales Approval')) { ?>
        <button type="button" class="btn btn-default svh-reject" data-toggle="modal" data-target="#rejectOrderModal"><i class="fa fa-times"></i> Reject</button>
        <button type="button" class="btn btn-primary svh-next" data-toggle="modal" data-target="#approveOrderModal"><i class="fa fa-check"></i> Approve as Sale</button>
      <?php } ?>
      <?php if (!empty($can_edit) && !empty($can_approve) && $order['status'] !== 'Sales') { ?>
        <button type="button" class="btn btn-primary svh-next" data-toggle="modal" data-target="#convertToSalesModal"><i class="fa fa-line-chart"></i> Convert to Sales</button>
      <?php } ?>
    </div>
  </div>
  <ol class="svh-steps" aria-label="Progress">
    <?php foreach ($ov_steps as $i => $s) { ?>
      <li class="<?php echo $s[2]; ?>"<?php echo $s[2] === 'is-now' ? ' aria-current="step"' : ''; ?>>
        <span class="svh-dot"><?php if ($s[2] === 'is-done') { ?><i class="fa fa-check"></i><?php } else { echo $i + 1; } ?></span>
        <span class="svh-st"><b><?php echo $s[0]; ?></b><small><?php echo $s[1] !== '' ? htmlspecialchars($s[1]) : '&mdash;'; ?></small></span>
      </li>
    <?php } ?>
  </ol>
</div>
<script defer src="<?php echo base_url(); ?>assets/dist/js/crm-record.js?v=20261106_31"></script>
