<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
  <section class="content-header" style="padding: 15px 15px 5px 15px;">
    <h1 style="font-size: 22px; font-weight: 800; color: #000000; margin: 0; letter-spacing: -0.02em;"> <?php echo !empty($info['page_heading']) ? $info['page_heading'] : 'View Payments'; ?> </h1>
  </section>

  <section class="content" style="padding-top: 10px;">
    <div class="row">
      <div class="col-xs-12">
        <div class="box p-4" style="border: 1px solid #e2e8f0; border-radius: 4px; margin-bottom: 15px; background: #ffffff;">
          <div class="x_row">
            <div class="x_col">
              <form class="form-inline head_filter form-inline-mob" method="get" action="<?php echo base_url(); ?>manager/orders/payments">
                <div class="form-group">
                  <div class="input-daterange input-group" id="datepicker" style="width: 100%;">
                    <input type="text" class="input-md form-control datepicker" name="d1" placeholder="From Date" value="<?php echo htmlspecialchars($this->input->get('d1') ?? ''); ?>" autocomplete="off" style="border: 1px solid #cbd5e1; border-radius: 4px 0 0 4px;" />
                    <span class="input-group-addon" style="background: #f8fafc; border: 1px solid #cbd5e1; border-left: none; border-right: none; color: #475569; font-weight: 600;">To</span>
                    <input type="text" class="input-md form-control datepicker" name="d2" placeholder="To Date" value="<?php echo htmlspecialchars($this->input->get('d2') ?? ''); ?>" autocomplete="off" style="border: 1px solid #cbd5e1; border-radius: 0 4px 4px 0;" />
                  </div>
                </div>

                <div class="form-group">
                  <select name="payment_mode" class="form-control" style="min-width: 140px; border: 1px solid #cbd5e1; border-radius: 4px;">
                    <option value="">Payment Mode</option>
                    <option value="Bank Transfer" <?php echo ($this->input->get('payment_mode') == 'Bank Transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                    <option value="Credit Card" <?php echo ($this->input->get('payment_mode') == 'Credit Card') ? 'selected' : ''; ?>>Credit Card</option>
                    <option value="Cheque" <?php echo ($this->input->get('payment_mode') == 'Cheque') ? 'selected' : ''; ?>>Cheque</option>
                    <option value="Cash" <?php echo ($this->input->get('payment_mode') == 'Cash') ? 'selected' : ''; ?>>Cash</option>
                  </select>
                </div>

                <div class="form-group">
                  <input type="text" class="form-control" name="search" placeholder="Search Receipt / Invoice / Customer..." value="<?php echo htmlspecialchars($this->input->get('search') ?? ''); ?>" style="border: 1px solid #cbd5e1; border-radius: 4px;" />
                </div>

                <div class="form-group">
                  <button type="submit" class="btn btn-default" style="background: #000000; color: #ffffff; border: 1px solid #000000; font-weight: 600; padding: 6px 16px;">Search</button>
                  <a href="<?php echo base_url(); ?>manager/orders/payments" class="btn btn-default" style="background: #ffffff; color: #000000; border: 1px solid #000000; font-weight: 600; padding: 6px 14px;"> Reset</a>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="box" style="border: 1px solid #e2e8f0; border-radius: 4px; background: #ffffff;">
          <div class="box-body table-responsive" style="padding: 15px;">
            <?php if (!empty($payments)) { ?>
              <table id="example_payments" class="table table-hover table-bordered table-striped" style="width:100%; font-size: 13px;">
                <thead>
                  <tr style="background: #000000; color: #ffffff;">
                    <th style="color: #ffffff; font-weight: 700;">#</th>
                    <th style="color: #ffffff; font-weight: 700;">Receipt No</th>
                    <th style="color: #ffffff; font-weight: 700;">Payment Date</th>
                    <th style="color: #ffffff; font-weight: 700;">Customer Name</th>
                    <th style="color: #ffffff; font-weight: 700;">Payment Mode</th>
                    <th style="color: #ffffff; font-weight: 700;">Reference No</th>
                    <th style="color: #ffffff; font-weight: 700;">Amount Paid</th>
                    <th style="color: #ffffff; font-weight: 700;">Recorded By</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i = 1; foreach ($payments as $pay) { ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><strong style="color: #000000; font-family: monospace;"><?php echo htmlspecialchars($pay['receipt_no']); ?></strong></td>
                      <td><?php echo htmlspecialchars(date('d M Y', strtotime($pay['payment_date']))); ?></td>
                      <td style="color: #000000; font-weight: 700;"><?php echo htmlspecialchars($pay['customer_name']); ?></td>
                      <td><span class="label" style="background: #f1f5f9; color: #000000; border: 1px solid #cbd5e1; font-weight: 700; padding: 4px 8px; font-size: 11px;"><?php echo htmlspecialchars($pay['payment_mode']); ?></span></td>
                      <td><?php echo htmlspecialchars($pay['reference_no'] ?: '-'); ?></td>
                      <td><strong style="color: #000000;">AED <?php echo number_format($pay['amount'], 2); ?></strong></td>
                      <td><?php echo htmlspecialchars($pay['recorded_by_name'] ?: 'Staff'); ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            <?php } else { ?>
              <div class="alert alert-info" style="margin-bottom:0;"> No Data Found </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
