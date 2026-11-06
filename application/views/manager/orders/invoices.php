<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php $n = !empty($invoices) ? count($invoices) : 0; $g = function ($k) { return htmlspecialchars((string)($this->input->get($k) ?? '')); }; ?>
<div class="content-wrapper px-4 tlv">
<?php echo crm_mz_tabbar(); ?>
  <section class="content-header">
    <h1><?php echo !empty($info['page_heading']) ? $info['page_heading'] : 'Invoices'; ?><small><?php echo $n; ?> invoice<?php echo $n === 1 ? '' : 's'; ?></small></h1>
  </section>

  <section class="content">
    <div class="box p-4">
      <form class="form-inline head_filter form-inline-mob" method="get" action="<?php echo base_url(); ?>manager/orders/invoices">
        <div class="form-group">
          <input type="text" class="form-control" name="search" placeholder="Invoice no. or customer" aria-label="Search" value="<?php echo $g('search'); ?>" />
        </div>
        <div class="form-group">
          <select name="status" class="form-control" aria-label="Status">
            <option value="">All statuses</option>
            <?php foreach (array('Paid', 'Unpaid', 'Partially Paid', 'Overdue') as $so) { ?>
            <option value="<?php echo $so; ?>"<?php echo $this->input->get('status') === $so ? ' selected' : ''; ?>><?php echo $so; ?></option>
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
          <a href="<?php echo base_url(); ?>manager/orders/invoices" class="btn btn-default">Reset</a>
        </div>
      </form>
    </div>

    <div class="box">
      <div class="box-body">
        <?php if ($n) { ?>
          <table id="example_invoices" class="table table-hover table-bordered tl-t" style="width:100%;">
            <thead>
              <tr>
                <th>Invoice No</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Due</th>
                <th>Subtotal</th>
                <th>VAT</th>
                <th>Total</th>
                <th>Paid</th>
                <th>Balance</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($invoices as $inv) { ?>
                <tr>
                  <td><b class="mono"><?php echo htmlspecialchars($inv['invoice_no']); ?></b></td>
                  <td><?php echo htmlspecialchars($inv['customer_name']); ?></td>
                  <td data-order="<?php echo htmlspecialchars((string)$inv['invoice_date']); ?>"><?php echo htmlspecialchars(date('d M Y', strtotime($inv['invoice_date']))); ?></td>
                  <td data-order="<?php echo htmlspecialchars((string)$inv['due_date']); ?>"><?php echo htmlspecialchars(date('d M Y', strtotime($inv['due_date']))); ?></td>
                  <td data-order="<?php echo (float)$inv['subtotal']; ?>">AED <?php echo number_format($inv['subtotal'], 2); ?></td>
                  <td data-order="<?php echo (float)$inv['vat_amount']; ?>">AED <?php echo number_format($inv['vat_amount'], 2); ?></td>
                  <td data-order="<?php echo (float)$inv['total_amount']; ?>"><b>AED <?php echo number_format($inv['total_amount'], 2); ?></b></td>
                  <td data-order="<?php echo (float)$inv['amount_paid']; ?>">AED <?php echo number_format($inv['amount_paid'], 2); ?></td>
                  <td data-order="<?php echo (float)$inv['balance_due']; ?>">AED <?php echo number_format($inv['balance_due'], 2); ?></td>
                  <td><?php echo htmlspecialchars($inv['status']); ?></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        <?php } else { ?>
          <div class="alert alert-info" style="margin-bottom:0;">No invoices found.</div>
        <?php } ?>
      </div>
    </div>
  </section>
</div>
<script type="text/javascript">
$(document).ready(function () {
  if ($('#example_invoices').length && !$.fn.DataTable.isDataTable('#example_invoices')) {
    $('#example_invoices').DataTable({ pageLength: 50, order: [[2, 'desc']], responsive: window.innerWidth >= 768 });
  }
});
</script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-tlist.css?v=20261106_4">
<script src="<?php echo base_url(); ?>assets/dist/js/crm-tlist.js?v=20261106_3"></script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
