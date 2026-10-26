<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
  <section class="content-header">
    <h1> <?php echo !empty($info['page_heading']) ? $info['page_heading'] : 'View Payments'; ?> </h1>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box p-4">
          <div class="x_row">
            <div class="x_col">
              <form class="form-inline head_filter form-inline-mob" method="get" action="<?php echo base_url(); ?>manager/orders/payments">
                <div class="form-group">
                  <div class="input-daterange input-group" id="datepicker" style="width: 100%;">
                    <input type="text" class="input-md form-control datepicker" name="d1" placeholder="From Date" value="<?php echo htmlspecialchars($this->input->get('d1')); ?>" autocomplete="off" />
                    <span class="input-group-addon">To</span>
                    <input type="text" class="input-md form-control datepicker" name="d2" placeholder="To Date" value="<?php echo htmlspecialchars($this->input->get('d2')); ?>" autocomplete="off" />
                  </div>
                </div>

                <div class="form-group">
                  <select name="payment_mode" class="form-control" style="min-width: 140px;">
                    <option value="">Payment Mode</option>
                    <option value="Bank Transfer" <?php echo ($this->input->get('payment_mode') == 'Bank Transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                    <option value="Credit Card" <?php echo ($this->input->get('payment_mode') == 'Credit Card') ? 'selected' : ''; ?>>Credit Card</option>
                    <option value="Cheque" <?php echo ($this->input->get('payment_mode') == 'Cheque') ? 'selected' : ''; ?>>Cheque</option>
                    <option value="Cash" <?php echo ($this->input->get('payment_mode') == 'Cash') ? 'selected' : ''; ?>>Cash</option>
                  </select>
                </div>

                <div class="form-group">
                  <input type="text" class="form-control" name="search" placeholder="Search Receipt / Invoice / Customer..." value="<?php echo htmlspecialchars($this->input->get('search')); ?>" />
                </div>

                <div class="form-group">
                  <button type="submit" class="btn btn-success">Search</button>
                  <a href="<?php echo base_url(); ?>manager/orders/payments" class="btn btn-danger"> Reset</a>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="box">
          <div class="box-body table-responsive">
            <?php if (!empty($payments)) { ?>
              <table id="example_payments" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Receipt No</th>
                    <th>Invoice No</th>
                    <th>Date</th>
                    <th>Customer Name</th>
                    <th>Payment Mode</th>
                    <th>Reference / Cheque No</th>
                    <th>Amount Paid</th>
                    <th>Remarks</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i = 1; foreach ($payments as $p) { ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><strong><?php echo htmlspecialchars($p['receipt_no']); ?></strong></td>
                      <td><?php echo htmlspecialchars(!empty($p['invoice_no']) ? $p['invoice_no'] : '—'); ?></td>
                      <td><?php echo htmlspecialchars(date('d M Y', strtotime($p['payment_date']))); ?></td>
                      <td><?php echo htmlspecialchars($p['customer_name']); ?></td>
                      <td><?php echo htmlspecialchars($p['payment_mode']); ?></td>
                      <td><?php echo htmlspecialchars(!empty($p['reference_no']) ? $p['reference_no'] : '—'); ?></td>
                      <td><strong>AED <?php echo number_format($p['amount'], 2); ?></strong></td>
                      <td><?php echo htmlspecialchars(!empty($p['remarks']) ? $p['remarks'] : '—'); ?></td>
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
