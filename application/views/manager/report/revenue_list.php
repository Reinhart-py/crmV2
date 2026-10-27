<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper">
  <section class="content-header" style="display:flex; justify-content:space-between; align-items:center; padding:15px;">
    <h1 style="margin:0; font-size:22px; font-weight:700; color:#000000;">
      <?php echo $info['page_heading']; ?>
    </h1>
    <div>
      <a href="<?php echo base_url(); ?>manager/reports/company-projections" class="btn btn-default" style="font-weight:700; color:#000000; border:1px solid #000000; background:#ffffff; border-radius:4px; padding:6px 14px;">
        Company Projections
      </a>
    </div>
  </section>

  <section class="content" style="padding-top:0;">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="box p-4" style="border:1px solid #e2e8f0; border-radius:4px; margin-bottom:15px; background:#ffffff;">
      <div class="x_row">
        <div class="x_col">
          <form class="form-inline head_filter form-inline-mob" method="get" action="<?php echo base_url(); ?>manager/reports/revenue-list">
            <div class="form-group">
              <div class="input-daterange input-group" style="width:100%;">
                <input type="text" class="input-md form-control datepicker" name="d1" placeholder="Date From" value="<?php echo htmlspecialchars($filter_d1 ?? ''); ?>" autocomplete="off" />
                <span class="input-group-addon">To</span>
                <input type="text" class="input-md form-control datepicker" name="d2" placeholder="Date To" value="<?php echo htmlspecialchars($filter_d2 ?? ''); ?>" autocomplete="off" />
              </div>
            </div>

            <div class="form-group">
              <input type="text" class="input-md form-control" name="client" placeholder="Search Client / Company" value="<?php echo htmlspecialchars($filter_client ?? ''); ?>" autocomplete="off" />
            </div>

            <div class="form-group">
              <select name="type" class="form-control">
                <option value="" <?php echo empty($filter_type) ? 'selected' : ''; ?>>All Revenue Types</option>
                <option value="confirmed" <?php echo ($filter_type === 'confirmed') ? 'selected' : ''; ?>>Confirmed Orders</option>
                <option value="projected" <?php echo ($filter_type === 'projected') ? 'selected' : ''; ?>>Projected Leads</option>
              </select>
            </div>

            <?php if(!empty($users_list)){ ?>
            <div class="form-group">
              <select name="user_id" class="form-control">
                <option value="">All Executives</option>
                <?php foreach($users_list as $u){ ?>
                  <option value="<?php echo $u['id']; ?>" <?php echo ($filter_user == $u['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($u['name']); ?>
                  </option>
                <?php } ?>
              </select>
            </div>
            <?php } ?>

            <div class="form-group">
              <button type="submit" class="btn btn-success">Search</button>
              <a href="<?php echo base_url(); ?>manager/reports/revenue-list" class="btn btn-danger">Reset</a>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="row" style="margin-bottom:15px;">
      <div class="col-md-3 col-sm-6">
        <div class="box" style="margin-bottom:10px; border:1px solid #e2e8f0; border-radius:4px; padding:14px; background:#ffffff;">
          <div style="font-size:11px; font-weight:700; color:#555555; text-transform:uppercase;">Confirmed MRR</div>
          <div style="font-size:22px; font-weight:800; color:#000000; margin-top:4px;">AED <?php echo number_format($total_confirmed_mrr, 2); ?></div>
          <div style="font-size:11px; color:#777777; margin-top:2px;"><?php echo count($orders); ?> confirmed orders</div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="box" style="margin-bottom:10px; border:1px solid #e2e8f0; border-radius:4px; padding:14px; background:#ffffff;">
          <div style="font-size:11px; font-weight:700; color:#555555; text-transform:uppercase;">Confirmed OTC</div>
          <div style="font-size:22px; font-weight:800; color:#000000; margin-top:4px;">AED <?php echo number_format($total_confirmed_otc, 2); ?></div>
          <div style="font-size:11px; color:#777777; margin-top:2px;">One-time billed charges</div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="box" style="margin-bottom:10px; border:1px solid #e2e8f0; border-radius:4px; padding:14px; background:#ffffff;">
          <div style="font-size:11px; font-weight:700; color:#555555; text-transform:uppercase;">Projected MRR</div>
          <div style="font-size:22px; font-weight:800; color:#000000; margin-top:4px;">AED <?php echo number_format($total_projected_mrr, 2); ?></div>
          <div style="font-size:11px; color:#777777; margin-top:2px;"><?php echo count($leads); ?> qualified leads</div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="box" style="margin-bottom:10px; border:1px solid #e2e8f0; border-radius:4px; padding:14px; background:#ffffff;">
          <div style="font-size:11px; font-weight:700; color:#555555; text-transform:uppercase;">Total Pipeline Value</div>
          <div style="font-size:22px; font-weight:800; color:#000000; margin-top:4px;">AED <?php echo number_format($total_projected_revenue, 2); ?></div>
          <div style="font-size:11px; color:#777777; margin-top:2px;">Projected total pipeline</div>
        </div>
      </div>
    </div>

    <div class="box" style="border:1px solid #e2e8f0; border-radius:4px; background:#ffffff;">
      <div class="box-body" style="padding:15px;">
        <?php 
          $has_data = (!empty($orders) || !empty($leads));
          if ($has_data) {
        ?>
        <div class="table-responsive">
          <table id="example_revenue" class="table table-hover table-bordered table-striped" style="width:100%; font-size:13px;">
            <thead>
              <tr style="background:#000000; color:#ffffff;">
                <th style="color:#ffffff;">Type</th>
                <th style="color:#ffffff;">Reference</th>
                <th style="color:#ffffff;">Client / Company</th>
                <th style="color:#ffffff;">Vertical / Category</th>
                <th style="color:#ffffff;">MRR</th>
                <th style="color:#ffffff;">OTC</th>
                <th style="color:#ffffff;">Total Value</th>
                <th style="color:#ffffff;">Status</th>
                <th style="color:#ffffff;">Executive</th>
                <th style="color:#ffffff;">Date</th>
                <th style="color:#ffffff; text-align:center;">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if(!empty($orders)){ foreach($orders as $ord){ 
                $omrr = floatval($ord['mrr'] ?: $ord['mrc']);
                $ootc = floatval($ord['otc']);
                $ototal = ($omrr * 12) + $ootc;
              ?>
                <tr>
                  <td><strong style="color:#000000;">Order</strong></td>
                  <td><strong><?php echo htmlspecialchars($ord['order_no']); ?></strong></td>
                  <td><?php echo htmlspecialchars($ord['client_name']); ?></td>
                  <td><?php echo !empty($ord['vertical_code']) ? htmlspecialchars($ord['vertical_code']) : (!empty($ord['service_title']) ? htmlspecialchars($ord['service_title']) : '-'); ?></td>
                  <td><strong>AED <?php echo number_format($omrr, 2); ?></strong></td>
                  <td>AED <?php echo number_format($ootc, 2); ?></td>
                  <td><strong>AED <?php echo number_format($ototal, 2); ?></strong></td>
                  <td><span class="label" style="background:#000000; color:#ffffff; font-weight:700;"><?php echo htmlspecialchars($ord['status']); ?></span></td>
                  <td><?php echo htmlspecialchars($ord['assigned_staff_name'] ?: '-'); ?></td>
                  <td><?php echo date('d-m-Y', strtotime($ord['created_at'])); ?></td>
                  <td style="text-align:center;">
                    <a href="<?php echo base_url(); ?>manager/orders/view/<?php echo $ord['id']; ?>" class="btn btn-xs" style="background:#000000; color:#ffffff; font-weight:700; border-radius:3px; padding:3px 8px;">View</a>
                  </td>
                </tr>
              <?php } } ?>

              <?php if(!empty($leads)){ foreach($leads as $ld){ 
                $lmrr = floatval($ld['mrr'] ?: $ld['mrc']);
                $lotc = floatval($ld['otc']);
                $lrev = floatval($ld['revenue'] ?: $ld['r_amount']);
                $ltotal = ($lrev > 0) ? $lrev : (($lmrr * 12) + $lotc);
              ?>
                <tr>
                  <td><span style="color:#555555; font-weight:700;">Lead</span></td>
                  <td><strong>#<?php echo htmlspecialchars($ld['lead_id']); ?></strong></td>
                  <td><?php echo htmlspecialchars($ld['customer']); ?></td>
                  <td><?php echo htmlspecialchars($ld['category'] ?: '-'); ?></td>
                  <td><strong>AED <?php echo number_format($lmrr, 2); ?></strong></td>
                  <td>AED <?php echo number_format($lotc, 2); ?></td>
                  <td><strong>AED <?php echo number_format($ltotal, 2); ?></strong></td>
                  <td><span class="label" style="background:#555555; color:#ffffff; font-weight:700;"><?php echo htmlspecialchars($ld['status_title'] ?: 'Qualified'); ?></span></td>
                  <td><?php echo htmlspecialchars($ld['owner_name'] ?: '-'); ?></td>
                  <td><?php echo !empty($ld['created']) ? date('d-m-Y', strtotime($ld['created'])) : '-'; ?></td>
                  <td style="text-align:center;">
                    <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($ld['id']); ?>" class="btn btn-xs" style="background:#000000; color:#ffffff; font-weight:700; border-radius:3px; padding:3px 8px;">View</a>
                  </td>
                </tr>
              <?php } } ?>
            </tbody>
          </table>
        </div>
        <?php } else { ?>
          <div class="alert alert-info" style="margin-bottom:0;"> No Data Found </div>
        <?php } ?>
      </div>
    </div>
  </section>
</div>

<script type="text/javascript">
$(document).ready(function() {
  if ($.fn.DataTable) {
    if ($.fn.DataTable.isDataTable('#example_revenue')) {
      $('#example_revenue').DataTable().destroy();
    }
    $('#example_revenue').DataTable({
      "pageLength": 50,
      responsive: true
    });
  }
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
