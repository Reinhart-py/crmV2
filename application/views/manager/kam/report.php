<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
  <section class="content-header">
    <h1> <?php echo !empty($info['page_heading']) ? $info['page_heading'] : 'KAM Reports'; ?> </h1>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box p-4">
          <div class="x_row">
            <div class="x_col">
              <form class="form-inline head_filter form-inline-mob" method="get" action="<?php echo base_url(); ?>manager/kam/report">
                <div class="form-group">
                  <div class="input-daterange input-group" id="datepicker" style="width: 100%;">
                    <input type="text" class="input-md form-control datepicker" name="d1" placeholder="From Date" value="<?php echo htmlspecialchars($this->input->get('d1')); ?>" autocomplete="off" />
                    <span class="input-group-addon">To</span>
                    <input type="text" class="input-md form-control datepicker" name="d2" placeholder="To Date" value="<?php echo htmlspecialchars($this->input->get('d2')); ?>" autocomplete="off" />
                  </div>
                </div>

                <div class="form-group">
                  <select name="manager_id" class="form-control select2" style="min-width: 170px;">
                    <option value="">Select Account Manager</option>
                    <?php if (!empty($managers_list)) {
                      foreach ($managers_list as $m) {
                        $sel = ($this->input->get('manager_id') == $m['id']) ? 'selected' : '';
                        echo '<option value="'.$m['id'].'" '.$sel.'>'.htmlspecialchars($m['name']).'</option>';
                      }
                    } ?>
                  </select>
                </div>

                <div class="form-group">
                  <input type="text" class="form-control" name="search" placeholder="Search Client / Group..." value="<?php echo htmlspecialchars($this->input->get('search')); ?>" />
                </div>

                <div class="form-group">
                  <button type="submit" class="btn btn-success">Search</button>
                  <a href="<?php echo base_url(); ?>manager/kam/report" class="btn btn-danger"> Reset</a>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="box">
          <div class="box-body table-responsive">
            <?php if (!empty($reports)) { ?>
              <table id="example_kam_report" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Client Name</th>
                    <th>Group</th>
                    <th>Account Manager</th>
                    <th>Category</th>
                    <th>Contact</th>
                    <th>MRR / Revenue</th>
                    <th>Onboarded</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i = 1; foreach ($reports as $r) { ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><strong><?php echo htmlspecialchars(!empty($r['client_name']) ? $r['client_name'] : $r['customer']); ?></strong></td>
                      <td><?php echo htmlspecialchars(!empty($r['group_name']) ? $r['group_name'] : '—'); ?></td>
                      <td><?php echo htmlspecialchars(!empty($r['account_manager']) ? $r['account_manager'] : (!empty($r['executive_name']) ? $r['executive_name'] : 'Unassigned')); ?></td>
                      <td><?php echo htmlspecialchars(!empty($r['category']) ? $r['category'] : '—'); ?></td>
                      <td><?php echo htmlspecialchars(!empty($r['contact1']) ? $r['contact1'] : '—'); ?></td>
                      <td><?php echo !empty($r['mrr']) ? htmlspecialchars($r['mrr']) : (!empty($r['r_amount']) ? htmlspecialchars($r['r_amount']) : '—'); ?></td>
                      <td><?php echo htmlspecialchars(date('d M Y', strtotime($r['created_at']))); ?></td>
                      <td>
                        <a href="<?php echo base_url(); ?>manager/kam/client_details/<?php echo $r['lead_id']; ?>" class="btn btn-sm btn-primary">
                          <i class="fa fa-eye"></i> View Details
                        </a>
                      </td>
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
