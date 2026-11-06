<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-ureg.css?v=20261106_4">
<?php $h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }; ?>
<style>
  .ureg .co-head { display: flex; align-items: center; gap: 12px; }
  .ureg .co-head .co-back { margin-left: auto; font-weight: 600; font-size: 13px; }
  .ureg .box-body > .row > .col-md-12 .form-group { grid-template-columns: 112px minmax(0, 1fr); }
  @media (min-width: 1200px) { .ureg .co-wide { width: 50% !important; } }
</style>
<div class="content-wrapper ureg">
  <section class="content-header co-head">
    <h1><?php echo $h($info['page_heading']); ?></h1>
    <a class="co-back" href="<?php echo base_url(); ?>manager/orders"><i class="fa fa-arrow-left"></i> Orders</a>
  </section>

  <section class="content">
    <div class="container">
      <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
      <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

      <form action="<?php echo base_url(); ?>manager/orders/add" method="post" autocomplete="off">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

        <div class="box box-default">
          <div class="box-header with-border"><h3 class="box-title">Client &amp; service</h3></div>
          <div class="box-body">
            <div class="row">
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="client_id">Client</label>
                  <select name="client_id" id="client_id" class="form-control">
                    <option value="">Choose existing client</option>
                    <?php foreach ($clients_list as $cl) { ?>
                      <option value="<?php echo $cl['id']; ?>" data-name="<?php echo $h($cl['customer']); ?>" data-lead="<?php echo $cl['id']; ?>"><?php echo $h($cl['customer']); ?> (<?php echo $h($cl['lead_id']); ?>)</option>
                    <?php } ?>
                  </select></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="client_name">Client name</label>
                  <input type="text" name="client_name" id="client_name" class="form-control" placeholder="If not in the list"></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="co_vert">Vertical<span>*</span></label>
                  <select name="vertical_code" id="co_vert" class="form-control" required>
                    <option value="accounting">Accounting / VAT / Audit</option>
                    <option value="telecom">Telecom (Du / Etisalat)</option>
                    <option value="it">IT &amp; Cloud Infrastructure</option>
                    <option value="corporate">General Corporate Services</option>
                  </select></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="lead_id">Lead ID</label>
                  <input type="number" name="lead_id" id="lead_id" class="form-control"></div>
              </div>
              <div class="col-lg-4 col-sm-6 co-wide">
                <div class="form-group"><label for="co_title">Service<span>*</span></label>
                  <input type="text" name="service_title" id="co_title" class="form-control" required></div>
              </div>
              <div class="col-md-12">
                <div class="form-group"><label for="co_scope">Scope of work</label>
                  <textarea name="service_scope" id="co_scope" rows="2" class="form-control"></textarea></div>
              </div>
            </div>
          </div>
        </div>

        <div class="box box-default">
          <div class="box-header with-border"><h3 class="box-title">Pricing</h3></div>
          <div class="box-body">
            <div class="row">
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="co_total">Contract value<span>*</span></label>
                  <input type="number" step="0.01" name="pricing_amount" id="co_total" class="form-control" placeholder="0.00" required inputmode="decimal"></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="co_mrr">MRR / MRC</label>
                  <input type="number" step="0.01" name="mrr" id="co_mrr" class="form-control" placeholder="0.00" inputmode="decimal"></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="co_otc">One-time (OTC)</label>
                  <input type="number" step="0.01" name="otc" id="co_otc" class="form-control" placeholder="0.00" inputmode="decimal"></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="co_bill">Billing</label>
                  <select name="billing_type" id="co_bill" class="form-control">
                    <option value="Monthly">Monthly</option>
                    <option value="Quarterly">Quarterly</option>
                    <option value="Annual">Annual</option>
                    <option value="One-Time">One-Time</option>
                  </select></div>
              </div>
            </div>
          </div>
        </div>

        <div class="box box-default">
          <div class="box-header with-border"><h3 class="box-title">Assignment</h3></div>
          <div class="box-body">
            <div class="row">
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="co_dept">Department</label>
                  <select name="assigned_dept" id="co_dept" class="form-control">
                    <option value="Back Office">Back Office</option>
                    <option value="Operations">Operations</option>
                    <option value="Finance">Finance</option>
                    <option value="IT">IT</option>
                    <option value="Telecom">Telecom Operations</option>
                  </select></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="co_staff">Staff</label>
                  <select name="assigned_to" id="co_staff" class="form-control">
                    <option value="">Select</option>
                    <?php foreach ($staff_list as $st) { ?>
                      <option value="<?php echo $st['id']; ?>"><?php echo $h($st['name']); ?> (<?php echo $h($st['department'] ?: 'Staff'); ?>)</option>
                    <?php } ?>
                  </select></div>
              </div>
              <div class="col-md-12">
                <div class="form-group"><label for="co_instr">Instructions</label>
                  <textarea name="operational_instructions" id="co_instr" rows="2" class="form-control"></textarea></div>
              </div>
            </div>
          </div>
        </div>

        <div class="box-footer ureg-footer">
          <a href="<?php echo base_url(); ?>manager/orders" class="btn btn-default">Cancel</a>
          <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Create order</button>
        </div>
      </form>
    </div>
  </section>
</div>

<script>
$(function () {
  $('#client_id').on('change', function () {
    var opt = $(this).find('option:selected');
    var name = opt.data('name'), lead = opt.data('lead');
    if (name) { $('#client_name').val(name); }
    if (lead) { $('#lead_id').val(lead); }
  });
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
