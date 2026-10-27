<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper">
  <section class="content-header" style="display:flex; justify-content:space-between; align-items:center; padding:15px;">
    <h1 style="margin:0; font-size:22px; font-weight:700; color:#000000;">
      <?php echo $info['page_heading']; ?>
    </h1>
    <div>
      <a href="<?php echo base_url(); ?>manager/reports/revenue-list" class="btn btn-default" style="font-weight:700; color:#000000; border:1px solid #000000; background:#ffffff; border-radius:4px; padding:6px 14px;">
        Revenue List
      </a>
    </div>
  </section>

  <section class="content" style="padding-top:0;">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="box p-4" style="border:1px solid #e2e8f0; border-radius:4px; margin-bottom:15px; background:#ffffff;">
      <div class="x_row">
        <div class="x_col">
          <form class="form-inline head_filter form-inline-mob" method="get" action="<?php echo base_url(); ?>manager/reports/company-projections">
            <div class="form-group">
              <div class="input-daterange input-group" style="width:100%;">
                <input type="text" class="input-md form-control datepicker" name="d1" placeholder="Created From" value="<?php echo htmlspecialchars($filter_d1 ?? ''); ?>" autocomplete="off" />
                <span class="input-group-addon">To</span>
                <input type="text" class="input-md form-control datepicker" name="d2" placeholder="Created To" value="<?php echo htmlspecialchars($filter_d2 ?? ''); ?>" autocomplete="off" />
              </div>
            </div>

            <div class="form-group">
              <input type="text" class="input-md form-control" name="search" placeholder="Search Company / POC" value="<?php echo htmlspecialchars($filter_search ?? ''); ?>" autocomplete="off" />
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
              <a href="<?php echo base_url(); ?>manager/reports/company-projections" class="btn btn-danger">Reset</a>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="row" style="margin-bottom:15px;">
      <div class="col-md-3 col-sm-6">
        <div class="box" style="margin-bottom:10px; border:1px solid #e2e8f0; border-radius:4px; padding:14px; background:#ffffff;">
          <div style="font-size:11px; font-weight:700; color:#555555; text-transform:uppercase;">Active Companies</div>
          <div style="font-size:22px; font-weight:800; color:#000000; margin-top:4px;"><?php echo $total_companies; ?></div>
          <div style="font-size:11px; color:#777777; margin-top:2px;">Companies with qualified deals</div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="box" style="margin-bottom:10px; border:1px solid #e2e8f0; border-radius:4px; padding:14px; background:#ffffff;">
          <div style="font-size:11px; font-weight:700; color:#555555; text-transform:uppercase;">Qualified Deals</div>
          <div style="font-size:22px; font-weight:800; color:#000000; margin-top:4px;"><?php echo $total_deals; ?></div>
          <div style="font-size:11px; color:#777777; margin-top:2px;">Active deals in pipeline</div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="box" style="margin-bottom:10px; border:1px solid #e2e8f0; border-radius:4px; padding:14px; background:#ffffff;">
          <div style="font-size:11px; font-weight:700; color:#555555; text-transform:uppercase;">Projected MRR</div>
          <div style="font-size:22px; font-weight:800; color:#000000; margin-top:4px;">AED <?php echo number_format($total_pipeline_mrr, 2); ?></div>
          <div style="font-size:11px; color:#777777; margin-top:2px;">Monthly recurring potential</div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="box" style="margin-bottom:10px; border:1px solid #e2e8f0; border-radius:4px; padding:14px; background:#ffffff;">
          <div style="font-size:11px; font-weight:700; color:#555555; text-transform:uppercase;">Annual Pipeline Value</div>
          <div style="font-size:22px; font-weight:800; color:#000000; margin-top:4px;">AED <?php echo number_format($total_pipeline_rev, 2); ?></div>
          <div style="font-size:11px; color:#777777; margin-top:2px;">Annualized value</div>
        </div>
      </div>
    </div>

    <div class="box" style="border:1px solid #e2e8f0; border-radius:4px; background:#ffffff;">
      <div class="box-body" style="padding:15px;">
        <?php if(!empty($companies)){ ?>
        <div class="table-responsive">
          <table id="example_company_projections" class="table table-hover table-bordered table-striped" style="width:100%; font-size:13px;">
            <thead>
              <tr style="background:#000000; color:#ffffff;">
                <th style="color:#ffffff;">Company / Client</th>
                <th style="color:#ffffff;">POC</th>
                <th style="color:#ffffff;">Categories</th>
                <th style="color:#ffffff; text-align:center;">Deals</th>
                <th style="color:#ffffff;">Projected MRR</th>
                <th style="color:#ffffff;">Annualized Value</th>
                <th style="color:#ffffff;">Expected Close</th>
                <th style="color:#ffffff;">Assigned Staff</th>
                <th style="color:#ffffff; text-align:center;">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($companies as $idx => $comp){ ?>
                <tr>
                  <td>
                    <strong style="color:#000000;"><?php echo htmlspecialchars($comp['customer']); ?></strong>
                    <?php if(!empty($comp['contact'])){ ?>
                      <div style="font-size:11px; color:#666666; margin-top:2px;"><?php echo htmlspecialchars($comp['contact']); ?></div>
                    <?php } ?>
                  </td>
                  <td><?php echo htmlspecialchars($comp['poc'] ?: '-'); ?></td>
                  <td>
                    <?php if(!empty($comp['categories'])){ 
                      echo htmlspecialchars(implode(', ', $comp['categories']));
                    } else { echo '-'; } ?>
                  </td>
                  <td style="text-align:center;">
                    <span class="label" style="background:#000000; color:#ffffff; font-weight:700; padding:2px 7px;"><?php echo $comp['deal_count']; ?></span>
                  </td>
                  <td><strong>AED <?php echo number_format($comp['total_mrr'], 2); ?></strong></td>
                  <td><strong>AED <?php echo number_format($comp['total_revenue'], 2); ?></strong></td>
                  <td><?php echo !empty($comp['nearest_close']) ? date('d-m-Y', strtotime($comp['nearest_close'])) : '-'; ?></td>
                  <td><?php echo !empty($comp['owners']) ? htmlspecialchars(implode(', ', $comp['owners'])) : '-'; ?></td>
                  <td style="text-align:center;">
                    <?php if (!empty($comp['leads'][0]['id'])) { ?>
                      <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($comp['leads'][0]['id']); ?>" class="btn btn-xs" style="background:#000000; color:#ffffff; font-weight:700; border-radius:3px; padding:3px 8px;">View</a>
                    <?php } else { ?>
                      -
                    <?php } ?>
                  </td>
                </tr>
              <?php } ?>
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
    if ($.fn.DataTable.isDataTable('#example_company_projections')) {
      $('#example_company_projections').DataTable().destroy();
    }
    $('#example_company_projections').DataTable({
      "pageLength": 50,
      responsive: true
    });
  }
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
