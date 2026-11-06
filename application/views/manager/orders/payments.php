<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php $n = !empty($payments) ? count($payments) : 0; $g = function ($k) { return htmlspecialchars((string)($this->input->get($k) ?? '')); }; ?>
<div class="content-wrapper px-4 tlv">
<?php echo crm_mz_tabbar(); ?>
  <section class="content-header">
    <h1><?php echo !empty($info['page_heading']) ? $info['page_heading'] : 'Payments'; ?><small><?php echo $n; ?> payment<?php echo $n === 1 ? '' : 's'; ?></small></h1>
  </section>

  <section class="content">
    <div class="box p-4">
      <form class="form-inline head_filter form-inline-mob" method="get" action="<?php echo base_url(); ?>manager/orders/payments">
        <div class="form-group">
          <input type="text" class="form-control" name="search" placeholder="Receipt, invoice or customer" aria-label="Search" value="<?php echo $g('search'); ?>" />
        </div>
        <div class="form-group">
          <select name="payment_mode" class="form-control" aria-label="Payment mode">
            <option value="">All modes</option>
            <?php foreach (array('Bank Transfer', 'Credit Card', 'Cheque', 'Cash') as $so) { ?>
            <option value="<?php echo $so; ?>"<?php echo $this->input->get('payment_mode') === $so ? ' selected' : ''; ?>><?php echo $so; ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="form-group">
          <div class="input-daterange input-group">
            <input type="text" class="form-control datepicker" name="d1" placeholder="From" aria-label="From" value="<?php echo $g('d1'); ?>" autocomplete="off" />
            <span class="input-group-addon">to</span>
            <input type="text" class="form-control datepicker" name="d2" placeholder="To" aria-label="To" value="<?php echo $g('d2'); ?>" autocomplete="off" />
          </div>
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-primary">Search</button>
          <a href="<?php echo base_url(); ?>manager/orders/payments" class="btn btn-default">Reset</a>
        </div>
      </form>
    </div>

    <div class="box">
      <div class="box-body">
        <?php if ($n) { ?>
          <table id="example_payments" class="table table-hover table-bordered tl-t" style="width:100%;">
            <thead>
              <tr>
                <th>Receipt No</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Mode</th>
                <th>Reference</th>
                <th>Recorded by</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($payments as $pay) { ?>
                <tr>
                  <td><b class="mono"><?php echo htmlspecialchars($pay['receipt_no']); ?></b></td>
                  <td><?php echo htmlspecialchars($pay['customer_name']); ?></td>
                  <td data-order="<?php echo htmlspecialchars((string)$pay['payment_date']); ?>"><?php echo htmlspecialchars(date('d M Y', strtotime($pay['payment_date']))); ?></td>
                  <td data-order="<?php echo (float)$pay['amount']; ?>"><b>AED <?php echo number_format($pay['amount'], 2); ?></b></td>
                  <td><?php echo htmlspecialchars($pay['payment_mode']); ?></td>
                  <td><?php echo htmlspecialchars($pay['reference_no'] ?: '-'); ?></td>
                  <td><?php echo htmlspecialchars($pay['recorded_by_name'] ?: 'Staff'); ?></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        <?php } else { ?>
          <div class="alert alert-info" style="margin-bottom:0;">No payments found.</div>
        <?php } ?>
      </div>
    </div>
  </section>
</div>
<script type="text/javascript">
$(document).ready(function () {
  if ($('#example_payments').length && !$.fn.DataTable.isDataTable('#example_payments')) {
    $('#example_payments').DataTable({ pageLength: 50, order: [[2, 'desc']], responsive: window.innerWidth >= 768 });
  }
});
</script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-tlist.css?v=20261106_4">
<script src="<?php echo base_url(); ?>assets/dist/js/crm-tlist.js?v=20261106_3"></script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
