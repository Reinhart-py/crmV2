<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4 tlv">
<?php echo crm_mz_tabbar(); ?>
  <section class="content-header">
    <h1><?php echo !empty($info['page_heading']) ? $info['page_heading'] : 'KAM Reports'; ?><small><?php echo isset($reports) ? count($reports) : 0; ?> clients</small></h1>
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
                    <input type="text" class="input-md form-control datepicker" name="d1" placeholder="Onboarded from" aria-label="Onboarded from" value="<?php echo htmlspecialchars($this->input->get('d1')); ?>" autocomplete="off" />
                    <span class="input-group-addon">To</span>
                    <input type="text" class="input-md form-control datepicker" name="d2" placeholder="Onboarded to" aria-label="Onboarded to" value="<?php echo htmlspecialchars($this->input->get('d2')); ?>" autocomplete="off" />
                  </div>
                </div>

                <div class="form-group">
                  <select name="manager_id" class="form-control select2" style="min-width: 170px;">
                    <option value="">Account manager</option>
                    <?php if (!empty($managers_list)) {
                      foreach ($managers_list as $m) {
                        $sel = ($this->input->get('manager_id') == $m['id']) ? 'selected' : '';
                        echo '<option value="'.$m['id'].'" '.$sel.'>'.htmlspecialchars($m['name']).'</option>';
                      }
                    } ?>
                  </select>
                </div>

                <div class="form-group">
                  <input type="text" class="form-control" name="search" placeholder="Client or group" aria-label="Search" value="<?php echo htmlspecialchars($this->input->get('search')); ?>" />
                </div>

                <div class="form-group">
                  <button type="submit" class="btn btn-primary">Search</button>
                  <a href="<?php echo base_url(); ?>manager/kam/report" class="btn btn-default">Reset</a>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="box">
          <div class="box-body table-responsive">
            <?php if (!empty($reports)) { ?>
              <table id="example_kam_report" class="table table-hover table-bordered tl-t" style="width:100%;">
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
                        <a href="<?php echo base_url(); ?>manager/kam/view/<?php echo (int)$r['lead_id']; ?>" class="btn btn-xs btn-default">
                          <i class="fa fa-eye"></i> View
                        </a>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            <?php } else { ?>
              <p class="text-muted" style="margin:6px 0;">Nothing found.</p>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
$(function () {
    var $t = $('#example_kam_report');
    if ($t.length && !$.fn.DataTable.isDataTable($t)) $t.DataTable({ pageLength: 50, columnDefs: [{ orderable: false, targets: [8] }], responsive: window.innerWidth >= 768 });
});
</script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-tlist.css?v=20261106_4">
<script src="<?php echo base_url(); ?>assets/dist/js/crm-tlist.js?v=20261106_3"></script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
