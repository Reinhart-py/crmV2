<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper">
  <section class="content-header" style="display:flex; justify-content:space-between; align-items:center; padding:15px 15px 5px 15px;">
    <h1 style="margin:0; font-size:22px; font-weight:800; color:#000000; letter-spacing:-0.02em;">
      <i class="fa fa-plus-circle" style="color:#000000; margin-right:8px;"></i><?php echo $info['page_heading']; ?>
    </h1>
    <div>
      <a href="<?php echo base_url(); ?>manager/orders" class="btn btn-default" style="font-weight:600; background:#ffffff; color:#000000; border:1px solid #000000;">
        <i class="fa fa-arrow-left"></i> Back to Orders
      </a>
    </div>
  </section>

  <section class="content" style="padding-top:10px;">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="box box-default" style="border-radius:4px; border:1px solid #e2e8f0; background:#ffffff;">
      <form action="<?php echo base_url(); ?>manager/orders/add" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        
        <div class="box-body" style="padding:25px;">
          <h4 style="font-size:12px; font-weight:700; color:#000000; text-transform:uppercase; letter-spacing:0.05em; margin-top:0; margin-bottom:18px; padding-bottom:8px; border-bottom:1px solid #000000;">
            1. Client &amp; Service Classification
          </h4>
          <div class="row">
            <div class="col-md-4 form-group">
              <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569;">Select Client Account</label>
              <select name="client_id" id="client_id" class="form-control" style="border:1px solid #cbd5e1; border-radius:4px;">
                <option value="">-- Choose Existing Client --</option>
                <?php foreach($clients_list as $cl){ ?>
                  <option value="<?php echo $cl['id']; ?>" data-name="<?php echo htmlspecialchars($cl['customer']); ?>" data-lead="<?php echo $cl['id']; ?>">
                    <?php echo htmlspecialchars($cl['customer']); ?> (<?php echo htmlspecialchars($cl['lead_id']); ?>)
                  </option>
                <?php } ?>
              </select>
            </div>
            <div class="col-md-4 form-group">
              <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569;">Client Name (Manual / Custom)</label>
              <input type="text" name="client_name" id="client_name" class="form-control" placeholder="Company or Client name" style="border:1px solid #cbd5e1; border-radius:4px;">
            </div>
            <div class="col-md-4 form-group">
              <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569;">Business Vertical *</label>
              <select name="vertical_code" class="form-control" required style="border:1px solid #cbd5e1; border-radius:4px;">
                <option value="accounting">Accounting / VAT / Audit</option>
                <option value="telecom">Telecom (Du / Etisalat)</option>
                <option value="it">IT &amp; Cloud Infrastructure</option>
                <option value="corporate">General Corporate Services</option>
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-md-8 form-group">
              <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569;">Service Title *</label>
              <input type="text" name="service_title" class="form-control" placeholder="e.g. Monthly VAT Filing & Corporate Tax Compliance" required style="border:1px solid #cbd5e1; border-radius:4px;">
            </div>
            <div class="col-md-4 form-group">
              <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569;">Associated Lead ID (Optional)</label>
              <input type="number" name="lead_id" id="lead_id" class="form-control" placeholder="Optional Lead ID" style="border:1px solid #cbd5e1; border-radius:4px;">
            </div>
          </div>

          <div class="form-group">
            <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569;">Detailed Scope of Work</label>
            <textarea name="service_scope" rows="3" class="form-control" placeholder="Outline specific deliverables, milestones, and scope specifications..." style="border:1px solid #cbd5e1; border-radius:4px;"></textarea>
          </div>

          <h4 style="font-size:12px; font-weight:700; color:#000000; text-transform:uppercase; letter-spacing:0.05em; margin-top:25px; margin-bottom:18px; padding-bottom:8px; border-bottom:1px solid #000000;">
            2. Commercials &amp; Pricing
          </h4>
          <div class="row">
            <div class="col-md-3 form-group">
              <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569;">Total Contract Value *</label>
              <input type="number" step="0.01" name="pricing_amount" class="form-control" placeholder="0.00" required style="border:1px solid #cbd5e1; border-radius:4px;">
            </div>
            <div class="col-md-3 form-group">
              <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569;">Monthly Recurring (MRR / MRC)</label>
              <input type="number" step="0.01" name="mrr" class="form-control" placeholder="0.00" style="border:1px solid #cbd5e1; border-radius:4px;">
            </div>
            <div class="col-md-3 form-group">
              <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569;">One-Time Charge (OTC)</label>
              <input type="number" step="0.01" name="otc" class="form-control" placeholder="0.00" style="border:1px solid #cbd5e1; border-radius:4px;">
            </div>
            <div class="col-md-3 form-group">
              <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569;">Billing Frequency</label>
              <select name="billing_type" class="form-control" style="border:1px solid #cbd5e1; border-radius:4px;">
                <option value="Monthly">Monthly</option>
                <option value="Quarterly">Quarterly</option>
                <option value="Annual">Annual</option>
                <option value="One-Time">One-Time</option>
              </select>
            </div>
          </div>

          <h4 style="font-size:12px; font-weight:700; color:#000000; text-transform:uppercase; letter-spacing:0.05em; margin-top:25px; margin-bottom:18px; padding-bottom:8px; border-bottom:1px solid #000000;">
            3. Department &amp; Staff Assignment
          </h4>
          <div class="row">
            <div class="col-md-6 form-group">
              <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569;">Assigned Department</label>
              <select name="assigned_dept" class="form-control" style="border:1px solid #cbd5e1; border-radius:4px;">
                <option value="Back Office">Back Office</option>
                <option value="Operations">Operations</option>
                <option value="Finance">Finance</option>
                <option value="IT">IT</option>
                <option value="Telecom">Telecom Operations</option>
              </select>
            </div>
            <div class="col-md-6 form-group">
              <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569;">Operational Staff Lead</label>
              <select name="assigned_to" class="form-control" style="border:1px solid #cbd5e1; border-radius:4px;">
                <option value="">-- Select Staff Assignee --</option>
                <?php foreach($staff_list as $st){ ?>
                  <option value="<?php echo $st['id']; ?>">
                    <?php echo htmlspecialchars($st['name']); ?> (<?php echo htmlspecialchars($st['department'] ?: 'Staff'); ?>)
                  </option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group" style="margin-bottom:0;">
            <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569;">Operational Instructions</label>
            <textarea name="operational_instructions" rows="3" class="form-control" placeholder="Enter special execution guidelines, customer SLA preferences, or compliance instructions..." style="border:1px solid #cbd5e1; border-radius:4px;"></textarea>
          </div>
        </div>

        <div class="box-footer" style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:15px 25px; display:flex; justify-content:flex-end; gap:10px;">
          <a href="<?php echo base_url(); ?>manager/orders" class="btn btn-default" style="background:#ffffff; color:#000000; border:1px solid #000000; font-weight:600;">Cancel</a>
          <button type="submit" class="btn btn-default" style="background:#000000; border:1px solid #000000; color:#ffffff; font-weight:700; padding:7px 24px;">
            <i class="fa fa-check"></i> Create &amp; Submit Order
          </button>
        </div>
      </form>
    </div>
  </section>
</div>

<script>
$(function() {
  $('#client_id').on('change', function() {
    var opt = $(this).find('option:selected');
    var name = opt.data('name');
    var lead = opt.data('lead');
    if (name) {
      $('#client_name').val(name);
    }
    if (lead) {
      $('#lead_id').val(lead);
    }
  });
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
