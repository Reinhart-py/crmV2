<?php
/*
 * Database and Leads lists: a Request button on each row, without opening the record.
 * Database row → request Lead conversion (manager/leads/request_conversion).
 * Lead row     → request Order (manager/leads/request_order).
 * Same forms and checks as the record preview; after sending, the user comes back to this list.
 * Use: crm_list_request_btn($li) in the row, and require this file once before the footer.
 */
if (!function_exists('crm_list_request_btn')) {
    function crm_list_request_btn($li)
    {
        $stage = isset($li['pipeline_stage']) ? (string)$li['pipeline_stage'] : '';
        $conv = isset($li['conversion_status']) ? (string)$li['conversion_status'] : '';
        $ors = isset($li['order_request_status']) ? strtolower(trim((string)$li['order_request_status'])) : '';
        $name = htmlspecialchars(isset($li['customer']) ? $li['customer'] : '', ENT_QUOTES);
        if ($stage === '' || $stage === 'database') {
            if ($conv === 'pending') {
                return '<span class="lrq-sent" title="Lead conversion requested, waiting for review">' . '<svg class="ico" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>' . '</span>';
            }
            return '<button type="button" class="btn btn-default btn-sm lrq-btn" data-lrq="lead" data-id="' . (int)$li['id'] . '" data-name="' . $name . '" title="Request Lead conversion" aria-label="Request Lead conversion">' . '<svg class="ico" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4z"/></svg>' . '</button>';
        }
        if ($stage === 'lead' && ($ors === '' || $ors === 'none')) {
            return '<button type="button" class="btn btn-default btn-sm lrq-btn" data-lrq="order" data-id="' . (int)$li['id'] . '" data-name="' . $name . '" title="Request Order" aria-label="Request Order">' . '<svg class="ico" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/></svg>' . '</button>';
        }
        return '';
    }
}
if (!empty($crm_list_request_modal)) {
?>
<div class="modal fade" id="lrqModal" tabindex="-1" role="dialog" aria-labelledby="lrqTitle">
  <div class="modal-dialog" role="document">
    <div class="modal-content lrq-box">
      <form method="post" id="lrqForm" data-lead="<?php echo base_url(); ?>manager/leads/request_conversion" data-order="<?php echo base_url(); ?>manager/leads/request_order">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="lead_id" id="lrqId" value="">
        <input type="hidden" name="return_to" value="list">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="lrqTitle">Request</h4>
        </div>
        <div class="modal-body">
          <p class="lrq-for" id="lrqFor"></p>
          <div class="form-group lrq-order-only">
            <label for="lrqService">Service / Requirement</label>
            <input type="text" class="form-control" name="service_requirements" id="lrqService" placeholder="e.g. Corporate Bank Account, Trade License Renewal, Golden Visa...">
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label for="lrqRemarks" id="lrqRemarksLabel">Remarks</label>
            <textarea class="form-control" name="remarks" id="lrqRemarks" rows="3" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary lrq-send">Send request</button>
        </div>
      </form>
    </div>
  </div>
</div>
<style>
.lrq-btn,.lrq-sent{width:30px;height:30px;padding:0 !important;display:inline-flex;align-items:center;justify-content:center;border-radius:7px}
.ico{display:block;flex:none}
.lrq-sent{background:#fef3c7;color:#92400e;cursor:help}
.lrq-btn:hover{border-color:var(--crm-ocean,#1e3a8a)!important;color:var(--crm-ocean,#1e3a8a)!important}
.lrq-box{border-radius:12px;border:1px solid var(--crm-line,#e2e8f0);font-family:"Plus Jakarta Sans","Inter",-apple-system,"Segoe UI",sans-serif}
.lrq-box .modal-header{border-bottom:1px solid var(--crm-line,#e2e8f0);padding:14px 18px}
.lrq-box .modal-title{font-size:17px;font-weight:700;color:var(--crm-ink,#0f172a)}
.lrq-box .modal-body{padding:16px 18px}
.lrq-box .lrq-for{font-size:13.5px;color:#334155;margin:0 0 12px}
.lrq-box label{font-weight:600;color:var(--crm-ink,#0f172a);font-size:13px}
.lrq-box .modal-footer{border-top:1px solid var(--crm-line,#e2e8f0);padding:12px 18px}
.lrq-box .btn{border-radius:8px;font-weight:600}
.lrq-box .lrq-send{background:var(--crm-ocean,#1e3a8a);border-color:var(--crm-ocean,#1e3a8a)}
</style>
<script>
jQuery(function ($) {
  var $f = $('#lrqForm');
  $(document).on('click', '.lrq-btn', function () {
    var kind = $(this).data('lrq'), name = $(this).attr('data-name');
    $f.attr('action', $f.data(kind));
    $('#lrqId').val($(this).data('id'));
    $('#lrqRemarks').val('');
    $('#lrqService').val('');
    if (kind === 'order') {
      $('#lrqTitle').text('Request Order');
      $('#lrqFor').text('Send ' + name + ' for Order processing. The lead is locked for editing while the order is in progress.');
      $('#lrqRemarksLabel').text('Remarks / handover notes');
      $('#lrqRemarks').attr('placeholder', 'Notes, documents gathered, or special client instructions...');
      $('.lrq-order-only').show();
    } else {
      $('#lrqTitle').text('Request Lead conversion');
      $('#lrqFor').text('Ask Back Office to review ' + name + ' and convert it to a Lead.');
      $('#lrqRemarksLabel').text('Why does it qualify?');
      $('#lrqRemarks').attr('placeholder', 'Reasons why this database record qualifies to be a Lead...');
      $('.lrq-order-only').hide();
    }
    $('#lrqModal').modal('show');
  });
  $('#lrqModal').on('shown.bs.modal', function () { $('#lrqRemarks').trigger('focus'); });
  $f.on('submit', function () { $(this).find('.lrq-send').prop('disabled', true).text('Sending...'); });
});
</script>
<?php } ?>
