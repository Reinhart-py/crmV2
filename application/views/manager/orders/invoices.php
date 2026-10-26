<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
  <section class="content-header">
    <h1> <?php echo !empty($info['page_heading']) ? $info['page_heading'] : 'View Invoices'; ?> </h1>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box p-4">
          <div class="x_row">
            <div class="x_col">
              <form class="form-inline head_filter form-inline-mob" method="get" action="<?php echo base_url(); ?>manager/orders/invoices">
                <div class="form-group">
                  <div class="input-daterange input-group" id="datepicker" style="width: 100%;">
                    <input type="text" class="input-md form-control datepicker" name="d1" placeholder="From Date" value="<?php echo htmlspecialchars($this->input->get('d1')); ?>" autocomplete="off" />
                    <span class="input-group-addon">To</span>
                    <input type="text" class="input-md form-control datepicker" name="d2" placeholder="To Date" value="<?php echo htmlspecialchars($this->input->get('d2')); ?>" autocomplete="off" />
                  </div>
                </div>

                <div class="form-group">
                  <select name="status" class="form-control" style="min-width: 140px;">
                    <option value="">Select Status</option>
                    <option value="Paid" <?php echo ($this->input->get('status') == 'Paid') ? 'selected' : ''; ?>>Paid</option>
                    <option value="Unpaid" <?php echo ($this->input->get('status') == 'Unpaid') ? 'selected' : ''; ?>>Unpaid</option>
                    <option value="Partially Paid" <?php echo ($this->input->get('status') == 'Partially Paid') ? 'selected' : ''; ?>>Partially Paid</option>
                  </select>
                </div>

                <div class="form-group">
                  <input type="text" class="form-control" name="search" placeholder="Search Invoice / Customer..." value="<?php echo htmlspecialchars($this->input->get('search')); ?>" />
                </div>

                <div class="form-group">
                  <button type="submit" class="btn btn-success">Search</button>
                  <a href="<?php echo base_url(); ?>manager/orders/invoices" class="btn btn-danger"> Reset</a>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="box">
          <div class="box-body table-responsive">
            <?php if (!empty($invoices)) { ?>
              <table id="example_invoices" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Invoice No</th>
                    <th>Date</th>
                    <th>Due Date</th>
                    <th>Customer Name</th>
                    <th>Subtotal</th>
                    <th>VAT</th>
                    <th>Total Amount</th>
                    <th>Paid</th>
                    <th>Balance Due</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i = 1; foreach ($invoices as $inv) { ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><strong><?php echo htmlspecialchars($inv['invoice_no']); ?></strong></td>
                      <td><?php echo htmlspecialchars(date('d M Y', strtotime($inv['invoice_date']))); ?></td>
                      <td><?php echo htmlspecialchars(date('d M Y', strtotime($inv['due_date']))); ?></td>
                      <td><?php echo htmlspecialchars($inv['customer_name']); ?></td>
                      <td>AED <?php echo number_format($inv['subtotal'], 2); ?></td>
                      <td>AED <?php echo number_format($inv['vat_amount'], 2); ?></td>
                      <td><strong>AED <?php echo number_format($inv['total_amount'], 2); ?></strong></td>
                      <td>AED <?php echo number_format($inv['amount_paid'], 2); ?></td>
                      <td>AED <?php echo number_format($inv['balance_due'], 2); ?></td>
                      <td><?php echo htmlspecialchars($inv['status']); ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            <?php } else { ?>
              <div class="alert alert-info"> No Data Found </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
