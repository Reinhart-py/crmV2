<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper">
  <section class="content-header" style="display:flex; justify-content:space-between; align-items:center; padding:15px 15px 5px 15px;">
    <h1 style="margin:0; font-size:20px; font-weight:700; color:#0f172a;">
      <i class="fa fa-line-chart" style="color:#2563eb; margin-right:8px;"></i><?php echo $info['page_heading']; ?>
    </h1>
    <div>
      <a href="<?php echo base_url(); ?>manager/reports/revenue-list" class="btn btn-default" style="font-weight:600; padding:6px 14px; border-radius:5px;">
        <i class="fa fa-money"></i> View Revenue List
      </a>
    </div>
  </section>

  <section class="content" style="padding-top:10px;">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="row" style="margin-bottom:15px;">
      <div class="col-md-3 col-sm-6">
        <div class="box box-default" style="border-radius:6px; margin-bottom:10px; border-top:3px solid #2563eb;">
          <div class="box-body" style="padding:15px;">
            <div style="font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Active Pipeline Companies</div>
            <div style="font-size:22px; font-weight:700; color:#2563eb; margin-top:5px;"><?php echo $total_companies; ?></div>
            <div style="font-size:11px; color:#94a3b8; margin-top:4px;">Companies with qualified leads</div>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="box box-default" style="border-radius:6px; margin-bottom:10px; border-top:3px solid #0284c7;">
          <div class="box-body" style="padding:15px;">
            <div style="font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Qualified Deals</div>
            <div style="font-size:22px; font-weight:700; color:#0284c7; margin-top:5px;"><?php echo $total_deals; ?></div>
            <div style="font-size:11px; color:#94a3b8; margin-top:4px;">Strictly pipeline stage = lead</div>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="box box-default" style="border-radius:6px; margin-bottom:10px; border-top:3px solid #16a34a;">
          <div class="box-body" style="padding:15px;">
            <div style="font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Projected MRR</div>
            <div style="font-size:22px; font-weight:700; color:#16a34a; margin-top:5px;">AED <?php echo number_format($total_pipeline_mrr, 2); ?></div>
            <div style="font-size:11px; color:#94a3b8; margin-top:4px;">Monthly recurring potential</div>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="box box-default" style="border-radius:6px; margin-bottom:10px; border-top:3px solid #8b5cf6;">
          <div class="box-body" style="padding:15px;">
            <div style="font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Annualized Pipeline Value</div>
            <div style="font-size:22px; font-weight:700; color:#8b5cf6; margin-top:5px;">AED <?php echo number_format($total_pipeline_rev, 2); ?></div>
            <div style="font-size:11px; color:#94a3b8; margin-top:4px;">Weighted expected revenue</div>
          </div>
        </div>
      </div>
    </div>

    <div class="box box-default" style="border-radius:6px; margin-bottom:15px;">
      <div class="box-body" style="padding:15px;">
        <form method="get" action="<?php echo base_url(); ?>manager/reports/company-projections" class="form-inline" style="display:flex; flex-wrap:wrap; gap:10px; align-items:center;">
          <div class="form-group">
            <label style="font-size:12px; color:#475569; margin-right:5px;">Created Date:</label>
            <input type="date" name="d1" value="<?php echo htmlspecialchars($filter_d1); ?>" class="form-control input-sm" style="height:32px;">
            <span style="color:#94a3b8; margin:0 4px;">to</span>
            <input type="date" name="d2" value="<?php echo htmlspecialchars($filter_d2); ?>" class="form-control input-sm" style="height:32px;">
          </div>

          <div class="form-group">
            <input type="text" name="search" value="<?php echo htmlspecialchars($filter_search); ?>" placeholder="Search company, POC, category..." class="form-control input-sm" style="height:32px; min-width:200px;">
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
          <a href="<?php echo base_url(); ?>manager/reports/company-projections" class="btn btn-sm btn-default" style="height:32px;">Reset</a>
        </form>
      </div>
    </div>

    <div class="box box-default" style="border-radius:6px;">
      <div class="box-header with-border">
        <h3 class="box-title" style="font-size:15px; font-weight:600; color:#1e293b;">
          Company-Wise Projected Pipeline (Excludes Raw Database Records)
        </h3>
      </div>
      <div class="box-body" style="padding:15px;">
        <?php if(!empty($companies)){ ?>
          <div class="table-responsive">
            <table class="table table-hover table-bordered datatable-table" style="width:100%; font-size:12px;">
              <thead>
                <tr style="background:#f8fafc; color:#334155;">
                  <th style="min-width:180px;">Company / Customer</th>
                  <th>Contact Person (POC)</th>
                  <th>Verticals / Categories</th>
                  <th class="text-center" style="width:70px;">Deals</th>
                  <th>Projected MRR</th>
                  <th>Annualized Value</th>
                  <th>Target Close Date</th>
                  <th>Assigned Owner(s)</th>
                  <th class="text-center" style="width:60px;">Details</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($companies as $idx => $comp){ ?>
                  <tr>
                    <td>
                      <strong style="color:#0f172a; font-size:13px;"><?php echo htmlspecialchars($comp['customer']); ?></strong>
                      <?php if(!empty($comp['contact'])){ ?>
                        <div style="font-size:11px; color:#64748b; margin-top:2px;"><i class="fa fa-phone"></i> <?php echo htmlspecialchars($comp['contact']); ?></div>
                      <?php } ?>
                    </td>
                    <td><?php echo htmlspecialchars($comp['poc'] ?: '-'); ?></td>
                    <td>
                      <?php if(!empty($comp['categories'])){ 
                        foreach($comp['categories'] as $cat){ ?>
                          <span class="label label-info" style="margin-right:2px;"><?php echo htmlspecialchars($cat); ?></span>
                      <?php } } else { echo '-'; } ?>
                    </td>
                    <td class="text-center">
                      <span class="badge bg-blue"><?php echo $comp['deal_count']; ?></span>
                    </td>
                    <td><strong style="color:#16a34a;">AED <?php echo number_format($comp['total_mrr'], 2); ?></strong></td>
                    <td><strong style="color:#0f172a;">AED <?php echo number_format($comp['total_revenue'], 2); ?></strong></td>
                    <td><?php echo !empty($comp['nearest_close']) ? date('d-m-Y', strtotime($comp['nearest_close'])) : '-'; ?></td>
                    <td><?php echo !empty($comp['owners']) ? htmlspecialchars(implode(', ', $comp['owners'])) : '-'; ?></td>
                    <td class="text-center">
                      <button type="button" class="btn btn-xs btn-default btn-toggle-leads" data-target="#comp_leads_<?php echo $idx; ?>" title="View Deal Breakdown">
                        <i class="fa fa-chevron-down"></i>
                      </button>
                    </td>
                  </tr>
                  <tr id="comp_leads_<?php echo $idx; ?>" style="display:none; background:#f1f5f9;">
                    <td colspan="9" style="padding:12px 20px;">
                      <div style="font-weight:600; color:#334155; margin-bottom:8px;">Deals for <?php echo htmlspecialchars($comp['customer']); ?>:</div>
                      <table class="table table-condensed table-bordered" style="background:#ffffff; font-size:11px; margin-bottom:0;">
                        <thead>
                          <tr style="background:#e2e8f0; color:#334155;">
                            <th>Lead ID</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>MRR</th>
                            <th>Deal Revenue</th>
                            <th>Expected Close</th>
                            <th>Created</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach($comp['leads'] as $cld){ 
                            $clmrr = floatval($cld['mrr'] ?: $cld['mrc']);
                            $clrev = floatval($cld['revenue'] ?: $cld['r_amount']);
                          ?>
                            <tr>
                              <td><strong>#<?php echo htmlspecialchars($cld['lead_id']); ?></strong></td>
                              <td><?php echo htmlspecialchars($cld['category'] ?: 'General'); ?></td>
                              <td><span class="label label-default"><?php echo htmlspecialchars($cld['status_title'] ?: 'Qualified Lead'); ?></span></td>
                              <td>AED <?php echo number_format($clmrr, 2); ?></td>
                              <td>AED <?php echo number_format($clrev, 2); ?></td>
                              <td><?php echo !empty($cld['close_date']) ? date('d-m-Y', strtotime($cld['close_date'])) : '-'; ?></td>
                              <td><?php echo !empty($cld['created']) ? date('d-m-Y', strtotime($cld['created'])) : '-'; ?></td>
                              <td>
                                <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($cld['id']); ?>" class="btn btn-xs btn-primary" target="_blank"><i class="fa fa-external-link"></i> View</a>
                              </td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        <?php } else { ?>
          <div class="alert alert-info" style="margin-bottom:0;">No qualified company projections found for the selected filter. Only leads in qualified stage (pipeline_stage = 'lead') appear here.</div>
        <?php } ?>
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

  $(document).on('click', '.btn-toggle-leads', function() {
    var target = $(this).data('target');
    $(target).toggle();
    var icon = $(this).find('i');
    if ($(target).is(':visible')) {
      icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
    } else {
      icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
    }
  });
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
