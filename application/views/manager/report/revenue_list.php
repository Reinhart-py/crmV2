<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper">
  <section class="content-header" style="display:flex; justify-content:space-between; align-items:center; padding:15px 15px 5px 15px;">
    <h1 style="margin:0; font-size:20px; font-weight:700; color:#0f172a;">
      <i class="fa fa-money" style="color:#2563eb; margin-right:8px;"></i><?php echo $info['page_heading']; ?>
    </h1>
    <div>
      <a href="<?php echo base_url(); ?>manager/reports/company-projections" class="btn btn-default" style="font-weight:600; padding:6px 14px; border-radius:5px;">
        <i class="fa fa-line-chart"></i> View Company Projections
      </a>
    </div>
  </section>

  <section class="content" style="padding-top:10px;">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="row" style="margin-bottom:15px;">
      <div class="col-md-3 col-sm-6">
        <div class="box box-default" style="border-radius:6px; margin-bottom:10px; border-top:3px solid #16a34a;">
          <div class="box-body" style="padding:15px;">
            <div style="font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Confirmed MRR (Orders)</div>
            <div style="font-size:22px; font-weight:700; color:#16a34a; margin-top:5px;">AED <?php echo number_format($total_confirmed_mrr, 2); ?></div>
            <div style="font-size:11px; color:#94a3b8; margin-top:4px;"><?php echo count($orders); ?> confirmed order<?php echo count($orders) == 1 ? '' : 's'; ?></div>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="box box-default" style="border-radius:6px; margin-bottom:10px; border-top:3px solid #2563eb;">
          <div class="box-body" style="padding:15px;">
            <div style="font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Confirmed OTC (Orders)</div>
            <div style="font-size:22px; font-weight:700; color:#2563eb; margin-top:5px;">AED <?php echo number_format($total_confirmed_otc, 2); ?></div>
            <div style="font-size:11px; color:#94a3b8; margin-top:4px;">One-time charges billed</div>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="box box-default" style="border-radius:6px; margin-bottom:10px; border-top:3px solid #f59e0b;">
          <div class="box-body" style="padding:15px;">
            <div style="font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Projected MRR (Leads)</div>
            <div style="font-size:22px; font-weight:700; color:#f59e0b; margin-top:5px;">AED <?php echo number_format($total_projected_mrr, 2); ?></div>
            <div style="font-size:11px; color:#94a3b8; margin-top:4px;"><?php echo count($leads); ?> qualified deal<?php echo count($leads) == 1 ? '' : 's'; ?> in pipeline</div>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="box box-default" style="border-radius:6px; margin-bottom:10px; border-top:3px solid #8b5cf6;">
          <div class="box-body" style="padding:15px;">
            <div style="font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Total Pipeline Value</div>
            <div style="font-size:22px; font-weight:700; color:#8b5cf6; margin-top:5px;">AED <?php echo number_format($total_projected_revenue, 2); ?></div>
            <div style="font-size:11px; color:#94a3b8; margin-top:4px;">Annualized &amp; one-off projected</div>
          </div>
        </div>
      </div>
    </div>

    <div class="box box-default" style="border-radius:6px; margin-bottom:15px;">
      <div class="box-body" style="padding:15px;">
        <form method="get" action="<?php echo base_url(); ?>manager/reports/revenue-list" class="form-inline" style="display:flex; flex-wrap:wrap; gap:10px; align-items:center;">
          <div class="form-group">
            <label style="font-size:12px; color:#475569; margin-right:5px;">Date Range:</label>
            <input type="date" name="d1" value="<?php echo htmlspecialchars($filter_d1); ?>" class="form-control input-sm" style="height:32px;">
            <span style="color:#94a3b8; margin:0 4px;">to</span>
            <input type="date" name="d2" value="<?php echo htmlspecialchars($filter_d2); ?>" class="form-control input-sm" style="height:32px;">
          </div>

          <div class="form-group">
            <input type="text" name="client" value="<?php echo htmlspecialchars($filter_client); ?>" placeholder="Search client / company..." class="form-control input-sm" style="height:32px; min-width:180px;">
          </div>

          <div class="form-group">
            <select name="type" class="form-control input-sm" style="height:32px;">
              <option value="" <?php echo empty($filter_type) ? 'selected' : ''; ?>>All Revenue Types</option>
              <option value="confirmed" <?php echo ($filter_type === 'confirmed') ? 'selected' : ''; ?>>Confirmed Orders Only</option>
              <option value="projected" <?php echo ($filter_type === 'projected') ? 'selected' : ''; ?>>Projected Leads Only</option>
            </select>
          </div>

          <?php if(!empty($users_list)){ ?>
          <div class="form-group">
            <select name="user_id" class="form-control input-sm" style="height:32px; min-width:150px;">
              <option value="">All Executives</option>
              <?php foreach($users_list as $u){ ?>
                <option value="<?php echo $u['id']; ?>" <?php echo ($filter_user == $u['id']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($u['name']); ?>
                </option>
              <?php } ?>
            </select>
          </div>
          <?php } ?>

          <button type="submit" class="btn btn-sm btn-primary" style="background:#2563eb; border-color:#1d4ed8; font-weight:600; height:32px;">
            <i class="fa fa-filter"></i> Filter
          </button>
          <a href="<?php echo base_url(); ?>manager/reports/revenue-list" class="btn btn-sm btn-default" style="height:32px;">Reset</a>
        </form>
      </div>
    </div>

    <div class="nav-tabs-custom" style="border-radius:6px; box-shadow:none;">
      <ul class="nav nav-tabs" style="border-bottom:2px solid #e2e8f0;">
        <li class="active"><a href="#tab_all" data-toggle="tab" style="font-weight:600;"><i class="fa fa-th-list"></i> All Records</a></li>
        <li><a href="#tab_confirmed" data-toggle="tab" style="font-weight:600;"><i class="fa fa-check-circle text-success"></i> Confirmed Orders (<?php echo count($orders); ?>)</a></li>
        <li><a href="#tab_projected" data-toggle="tab" style="font-weight:600;"><i class="fa fa-clock-o text-warning"></i> Projected Leads (<?php echo count($leads); ?>)</a></li>
      </ul>

      <div class="tab-content" style="padding:15px 0;">
        <div class="tab-pane active" id="tab_all">
          <div class="table-responsive">
            <table class="table table-hover table-bordered datatable-table" style="width:100%; font-size:12px;">
              <thead>
                <tr style="background:#f8fafc; color:#334155;">
                  <th>Classification</th>
                  <th>Reference / ID</th>
                  <th>Client / Company</th>
                  <th>Vertical / Scope</th>
                  <th>Monthly (MRR)</th>
                  <th>One-Time (OTC)</th>
                  <th>Annualized / Total</th>
                  <th>Status</th>
                  <th>Executive</th>
                  <th>Date</th>
                  <th class="text-center" style="width:70px;">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($orders as $ord){ 
                  $omrr = floatval($ord['mrr'] ?: $ord['mrc']);
                  $ootc = floatval($ord['otc']);
                  $ototal = ($omrr * 12) + $ootc;
                ?>
                  <tr>
                    <td><span class="label label-success"><i class="fa fa-check"></i> Confirmed Order</span></td>
                    <td><strong><?php echo htmlspecialchars($ord['order_no']); ?></strong></td>
                    <td><strong><?php echo htmlspecialchars($ord['client_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($ord['vertical_name']); ?></td>
                    <td><strong style="color:#16a34a;">AED <?php echo number_format($omrr, 2); ?></strong></td>
                    <td>AED <?php echo number_format($ootc, 2); ?></td>
                    <td>AED <?php echo number_format($ototal, 2); ?></td>
                    <td><span class="label label-primary"><?php echo htmlspecialchars($ord['status']); ?></span></td>
                    <td><?php echo htmlspecialchars($ord['assigned_staff_name'] ?: 'Unassigned'); ?></td>
                    <td><?php echo date('d-m-Y', strtotime($ord['created_at'])); ?></td>
                    <td class="text-center">
                      <a href="<?php echo base_url(); ?>manager/orders/view/<?php echo $ord['id']; ?>" class="btn btn-xs btn-default" title="View Order"><i class="fa fa-eye"></i></a>
                    </td>
                  </tr>
                <?php } ?>

                <?php foreach($leads as $ld){ 
                  $lmrr = floatval($ld['mrr'] ?: $ld['mrc']);
                  $lotc = floatval($ld['otc']);
                  $lrev = floatval($ld['revenue'] ?: $ld['r_amount']);
                  $ltotal = ($lrev > 0) ? $lrev : (($lmrr * 12) + $lotc);
                ?>
                  <tr>
                    <td><span class="label label-warning"><i class="fa fa-clock-o"></i> Projected Lead</span></td>
                    <td><strong>#<?php echo htmlspecialchars($ld['lead_id']); ?></strong></td>
                    <td><strong><?php echo htmlspecialchars($ld['customer']); ?></strong></td>
                    <td><?php echo htmlspecialchars($ld['category'] ?: 'General'); ?></td>
                    <td><strong style="color:#d97706;">AED <?php echo number_format($lmrr, 2); ?></strong></td>
                    <td>AED <?php echo number_format($lotc, 2); ?></td>
                    <td>AED <?php echo number_format($ltotal, 2); ?></td>
                    <td><span class="label label-default"><?php echo htmlspecialchars($ld['status_title'] ?: 'Qualified Lead'); ?></span></td>
                    <td><?php echo htmlspecialchars($ld['owner_name'] ?: 'Unassigned'); ?></td>
                    <td><?php echo !empty($ld['created']) ? date('d-m-Y', strtotime($ld['created'])) : '-'; ?></td>
                    <td class="text-center">
                      <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo $ld['id']; ?>" class="btn btn-xs btn-default" title="View Lead"><i class="fa fa-eye"></i></a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="tab-pane" id="tab_confirmed">
          <div class="table-responsive">
            <table class="table table-hover table-bordered datatable-table" style="width:100%; font-size:12px;">
              <thead>
                <tr style="background:#f8fafc; color:#334155;">
                  <th>Order No</th>
                  <th>Client / Company</th>
                  <th>Vertical</th>
                  <th>MRR</th>
                  <th>OTC</th>
                  <th>Status</th>
                  <th>Assigned Staff</th>
                  <th>Created Date</th>
                  <th class="text-center" style="width:70px;">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($orders as $ord){ 
                  $omrr = floatval($ord['mrr'] ?: $ord['mrc']);
                  $ootc = floatval($ord['otc']);
                ?>
                  <tr>
                    <td><strong><?php echo htmlspecialchars($ord['order_no']); ?></strong></td>
                    <td><strong><?php echo htmlspecialchars($ord['client_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($ord['vertical_name']); ?></td>
                    <td><strong style="color:#16a34a;">AED <?php echo number_format($omrr, 2); ?></strong></td>
                    <td>AED <?php echo number_format($ootc, 2); ?></td>
                    <td><span class="label label-primary"><?php echo htmlspecialchars($ord['status']); ?></span></td>
                    <td><?php echo htmlspecialchars($ord['assigned_staff_name'] ?: 'Unassigned'); ?></td>
                    <td><?php echo date('d-m-Y', strtotime($ord['created_at'])); ?></td>
                    <td class="text-center">
                      <a href="<?php echo base_url(); ?>manager/orders/view/<?php echo $ord['id']; ?>" class="btn btn-xs btn-default" title="View Order"><i class="fa fa-eye"></i></a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="tab-pane" id="tab_projected">
          <div class="table-responsive">
            <table class="table table-hover table-bordered datatable-table" style="width:100%; font-size:12px;">
              <thead>
                <tr style="background:#f8fafc; color:#334155;">
                  <th>Lead ID</th>
                  <th>Company / Customer</th>
                  <th>POC</th>
                  <th>Category</th>
                  <th>Projected MRR</th>
                  <th>Total Pipeline Value</th>
                  <th>Status</th>
                  <th>Assigned Owner</th>
                  <th>Created Date</th>
                  <th class="text-center" style="width:70px;">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($leads as $ld){ 
                  $lmrr = floatval($ld['mrr'] ?: $ld['mrc']);
                  $lrev = floatval($ld['revenue'] ?: $ld['r_amount']);
                  $ltotal = ($lrev > 0) ? $lrev : ($lmrr * 12);
                ?>
                  <tr>
                    <td><strong>#<?php echo htmlspecialchars($ld['lead_id']); ?></strong></td>
                    <td><strong><?php echo htmlspecialchars($ld['customer']); ?></strong></td>
                    <td><?php echo htmlspecialchars($ld['poc']); ?></td>
                    <td><?php echo htmlspecialchars($ld['category'] ?: 'General'); ?></td>
                    <td><strong style="color:#d97706;">AED <?php echo number_format($lmrr, 2); ?></strong></td>
                    <td>AED <?php echo number_format($ltotal, 2); ?></td>
                    <td><span class="label label-default"><?php echo htmlspecialchars($ld['status_title'] ?: 'Qualified Lead'); ?></span></td>
                    <td><?php echo htmlspecialchars($ld['owner_name'] ?: 'Unassigned'); ?></td>
                    <td><?php echo !empty($ld['created']) ? date('d-m-Y', strtotime($ld['created'])) : '-'; ?></td>
                    <td class="text-center">
                      <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo $ld['id']; ?>" class="btn btn-xs btn-default" title="View Lead"><i class="fa fa-eye"></i></a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
$(document).ready(function() {
  if ($.fn.DataTable) {
    $('.datatable-table').DataTable({
      "pageLength": 25,
      "order": [],
      responsive: true
    });
  }
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
