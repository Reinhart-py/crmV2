<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>

<div class="container">
<div class="content-wrapper">

  <section class="content-header">
    <h1> <?php echo isset($info['page_heading']) ? $info['page_heading'] : 'Process Order Conversion'; ?> </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?php echo base_url(); ?>manager/orders/pending_process">Pending Order Process</a></li>
      <li class="active">Process Order</li>
    </ol>
  </section>

  <section class="content">

    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <?php if (!empty($lead['salesperson_snapshot'])) {
      $orig = json_decode($lead['salesperson_snapshot'], true);
    } else {
      $orig = $lead;
    }
    ?>

    <div class="box box-default collapsed-box" style="border: 1px solid #cbd5e1; border-radius: 4px; margin-bottom: 16px;">
      <div class="box-header with-border" style="background: #f8fafc; padding: 10px 15px; cursor: pointer;" data-widget="collapse">
        <h3 class="box-title" style="font-size: 13px; font-weight: 700; color: #000000; text-transform: uppercase; letter-spacing: 0.03em;">
          <i class="fa fa-history" style="margin-right: 6px;"></i> Original Salesperson Submission (#<?php echo htmlspecialchars($lead['lead_ref_no'] ?: ($lead['db_ref_no'] ?: $lead['id'])); ?>) - Submitted by <?php echo htmlspecialchars($lead['requested_by_name'] ?: 'Salesperson'); ?>
        </h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
        </div>
      </div>
      <div class="box-body" style="background: #ffffff; padding: 18px; display: none;">
        <div class="row" style="font-size: 12px; line-height: 1.8;">
          <div class="col-sm-12" style="margin-bottom: 10px;">
            <h5 style="font-size: 13px; font-weight: 700; color: #1e293b; text-transform: uppercase; margin: 0 0 6px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px;">
              <i class="fa fa-building" style="color: #0072f0; margin-right: 6px;"></i> Primary &amp; Account Information
            </h5>
          </div>
          <div class="col-sm-3"><strong>Company:</strong> <?php echo htmlspecialchars($orig['customer'] ?? '-'); ?></div>
          <div class="col-sm-3"><strong>Owner Name:</strong> <?php echo htmlspecialchars($orig['owner_name'] ?? ($orig['poc'] ?? '-')); ?></div>
          <div class="col-sm-3"><strong>POC Name:</strong> <?php echo htmlspecialchars($orig['poc'] ?? '-'); ?></div>
          <div class="col-sm-3"><strong>Phone / Contact:</strong> <?php echo htmlspecialchars($orig['contact1'] ?? '-'); ?></div>
          <div class="col-sm-3"><strong>Alt Contact:</strong> <?php echo htmlspecialchars(!empty($orig['alt_contact']) ? $orig['alt_contact'] : ($orig['alt_mobile'] ?? '-')); ?></div>
          <div class="col-sm-3"><strong>Email Address:</strong> <?php echo htmlspecialchars($orig['email'] ?? '-'); ?></div>
          <div class="col-sm-3"><strong>Account Number:</strong> <?php echo htmlspecialchars($orig['a_number'] ?? '-'); ?></div>
          <div class="col-sm-3"><strong>Account Vertical:</strong> <?php echo htmlspecialchars($orig['a_department'] ?? '-'); ?></div>
          <div class="col-sm-3"><strong>Monthly Rev Amount:</strong> AED <?php echo number_format((float)($orig['r_amount'] ?? 0), 2); ?></div>
          <div class="col-sm-3"><strong>Monthly Rev Date:</strong> <?php echo !empty($orig['r_date']) && $orig['r_date'] != '0000-00-00' ? date('d-m-Y', strtotime($orig['r_date'])) : '-'; ?></div>
          <div class="col-sm-3"><strong>Category:</strong> <?php echo htmlspecialchars($orig['category'] ?? '-'); ?></div>
          <div class="col-sm-3"><strong>Company Status:</strong> <?php echo htmlspecialchars($orig['company_status'] ?? '-'); ?></div>
          <div class="col-sm-3"><strong>Industry Category:</strong> <?php echo htmlspecialchars(!empty($orig['industry_category']) ? $orig['industry_category'] : ($orig['business_category'] ?? '-')); ?></div>

          <div class="col-sm-12" style="margin-top: 14px; margin-bottom: 10px;">
            <h5 style="font-size: 13px; font-weight: 700; color: #1e293b; text-transform: uppercase; margin: 0 0 6px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px;">
              <i class="fa fa-id-card" style="color: #0072f0; margin-right: 6px;"></i> Trade License &amp; Corporate Compliance
            </h5>
          </div>
          <div class="col-sm-3"><strong>Trade License No:</strong> <?php echo htmlspecialchars($orig['tl_number'] ?? '-'); ?></div>
          <div class="col-sm-3"><strong>Trade License Emirate:</strong> <?php echo htmlspecialchars($orig['tl_emirate'] ?? '-'); ?></div>
          <div class="col-sm-3"><strong>Trade License Zone:</strong> <?php echo htmlspecialchars($orig['tl_zone'] ?? '-'); ?></div>
          <div class="col-sm-3"><strong>TL Authority:</strong> <?php echo htmlspecialchars($orig['tl_authority'] ?? '-'); ?></div>
          <div class="col-sm-3"><strong>TL Expire Date:</strong> <?php echo !empty($orig['tl_expire_date']) && $orig['tl_expire_date'] != '0000-00-00' ? date('d-m-Y', strtotime($orig['tl_expire_date'])) : '-'; ?></div>
          <div class="col-sm-3"><strong>TL Due Info:</strong> <?php echo htmlspecialchars($orig['tl_due'] ?? '-'); ?></div>
          <div class="col-sm-3"><strong>MOL Employees:</strong> <?php echo htmlspecialchars($orig['no_of_mol_employee'] ?? '-'); ?></div>
          <div class="col-sm-3"><strong>Last MOL Payment:</strong> <?php echo !empty($orig['last_mol']) && $orig['last_mol'] != '0000-00-00' ? date('d-m-Y', strtotime($orig['last_mol'])) : '-'; ?></div>
          <div class="col-sm-3"><strong>Year Ending:</strong> <?php echo htmlspecialchars($orig['year_ending'] ?? '-'); ?></div>
          <div class="col-sm-3"><strong>Corporate Tax Status:</strong> <?php echo htmlspecialchars($orig['corporate_tax_status'] ?? '-'); ?></div>
          <div class="col-sm-3"><strong>Corporate Tax Due:</strong> <?php echo htmlspecialchars($orig['corporate_tax_due'] ?? '-'); ?></div>
          <div class="col-sm-3"><strong>Corporate Tax Due Date:</strong> <?php echo !empty($orig['corporate_tax_due_date']) && $orig['corporate_tax_due_date'] != '0000-00-00' ? date('d-m-Y', strtotime($orig['corporate_tax_due_date'])) : '-'; ?></div>
          <div class="col-sm-3"><strong>VAT Status:</strong> <?php echo htmlspecialchars($orig['vat_status'] ?? '-'); ?></div>
          <div class="col-sm-3"><strong>VAT Frequency:</strong> <?php echo htmlspecialchars($orig['vat_frequency'] ?? '-'); ?></div>
          <div class="col-sm-3"><strong>VAT Period:</strong> <?php echo htmlspecialchars($orig['vat_period'] ?? '-'); ?></div>
          <div class="col-sm-3"><strong>VAT Period Due:</strong> <?php echo htmlspecialchars($orig['vat_period_due'] ?? '-'); ?></div>

          <div class="col-sm-12" style="margin-top: 14px; margin-bottom: 10px;">
            <h5 style="font-size: 13px; font-weight: 700; color: #1e293b; text-transform: uppercase; margin: 0 0 6px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px;">
              <i class="fa fa-map-marker" style="color: #0072f0; margin-right: 6px;"></i> Location &amp; Quotation Details
            </h5>
          </div>
          <div class="col-sm-3"><strong>Location / Area:</strong> <?php echo htmlspecialchars($orig['location'] ?? '-'); ?></div>
          <div class="col-sm-3"><strong>Full Address:</strong> <?php echo htmlspecialchars($orig['address'] ?? '-'); ?></div>
          <div class="col-sm-3"><strong>Website:</strong> <?php echo htmlspecialchars($orig['website'] ?? '-'); ?></div>
          <div class="col-sm-3"><strong>Service Category:</strong> <?php echo htmlspecialchars($orig['servicecat'] ?? '-'); ?></div>
          <div class="col-sm-3"><strong>Monthly Retainer (MRC):</strong> AED <?php echo number_format((float)($orig['mrc'] ?? 0), 2); ?></div>
          <div class="col-sm-3"><strong>One-Time Revenue:</strong> AED <?php echo number_format((float)($orig['revenue'] ?? 0), 2); ?></div>
          <div class="col-sm-3"><strong>Total MRR:</strong> <strong style="color: #16a34a;">AED <?php echo number_format((float)($orig['mrr'] ?? 0), 2); ?></strong></div>
          <div class="col-sm-3"><strong>Advance Amount:</strong> AED <?php echo number_format((float)($orig['advance_amount'] ?? 0), 2); ?></div>
          <div class="col-sm-3"><strong>Timeline:</strong> <?php echo htmlspecialchars($orig['timeline'] ?? '-'); ?></div>
          <div class="col-sm-3"><strong>Payment Terms:</strong> <?php echo htmlspecialchars($orig['payment_terms'] ?? '-'); ?></div>
          <div class="col-sm-6"><strong>Deliverables:</strong> <?php echo htmlspecialchars($orig['deliverables'] ?? '-'); ?></div>

          <div class="col-sm-12" style="margin-top: 14px; padding-top: 10px; border-top: 1px solid #f1f5f9;">
            <strong>Salesperson Scope &amp; Remarks:</strong>
            <div style="background: #f8fafc; padding: 10px 14px; border-radius: 4px; border: 1px solid #e2e8f0; margin-top: 4px; color: #334155; line-height: 1.6;">
              <?php echo nl2br(htmlspecialchars($lead['order_request_remarks'] ?? ($orig['remark'] ?? 'No remarks provided.'))); ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <form role="form" action="<?php echo base_url(); ?>manager/orders/edit_pending/<?php echo $lead['id']; ?>" method="post" name="process_order_form">
      <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
      <input type="hidden" name="action_type" value="send_for_approval">

      <div class="box box-default" style="border: 1px solid #00c6f0; border-radius: 6px; margin-bottom: 16px;">
        <div class="box-header with-border" style="background: #f0fbff; padding: 12px 18px;">
          <h3 class="box-title" style="color: #0369a1; font-weight: 700; font-size: 15px; margin: 0;">
            <i class="fa fa-user-plus" style="margin-right: 8px;"></i> Assign Order Onboard Client
          </h3>
        </div>
        <div class="box-body" style="padding: 18px;">
          <div class="row">
            <div class="col-md-6 col-sm-12">
              <div class="form-group">
                <label style="font-weight: 700; font-size: 12px; color: #334155;">Select Client / Company</label>
                <select name="selected_existing_company" id="selectExistingCompany" class="form-control select2" style="width: 100%;">
                  <option value="">-- Choose Existing Company or Keep Current --</option>
                  <?php if (!empty($company_list)) { foreach ($company_list as $cmp) { ?>
                    <option value="<?php echo htmlspecialchars($cmp['customer']); ?>" <?php echo (strcasecmp($cmp['customer'], $lead['customer']) === 0) ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($cmp['customer']); ?>
                    </option>
                  <?php } } ?>
                </select>
              </div>
            </div>
            <div class="col-md-6 col-sm-12">
              <div class="form-group">
                <label style="font-weight: 700; font-size: 12px; color: #334155;">Manager (Responsible for Onboarding)</label>
                <select name="onboard_manager_id" id="selectOnboardManager" class="form-control select2" style="width: 100%;">
                  <option value="">-- Select Manager with Onboard Rights --</option>
                  <?php if (!empty($staff_list)) { foreach ($staff_list as $mgr) { ?>
                    <option value="<?php echo (int)$mgr['id']; ?>" <?php echo ((int)$lead['lead_assign'] === (int)$mgr['id']) ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($mgr['name'] . (!empty($mgr['department']) ? ' (' . $mgr['department'] . ')' : '')); ?>
                    </option>
                  <?php } } ?>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="box box-default">
        <div class="box-header with-border" style="position: relative; text-align: center; padding: 14px 15px; min-height: 48px;">
          <h3 class="box-title" style="float: none; display: inline-block; font-weight: 600; font-size: 18px; margin: 0; line-height: 24px;">Initial Category Fields</h3>
        </div>

        <div class="box-body">
          <div class="row">

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Company Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="customer" id="customer_input" value="<?php echo htmlspecialchars($lead['customer'] ?? ''); ?>" required>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Owner Name</label>
                <input type="text" class="form-control" name="owner_name" id="owner_name_input" placeholder="Owner Name" value="<?php echo htmlspecialchars($lead['owner_name'] ?? ($lead['poc'] ?? '')); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>POC Name</label>
                <input type="text" class="form-control" name="poc" value="<?php echo htmlspecialchars($lead['poc'] ?? ''); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>POC Contact Number</label>
                <input type="text" class="form-control" name="contact1" value="<?php echo htmlspecialchars($lead['contact1'] ?? ''); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Alternative Contact Number</label>
                <input type="text" class="form-control" name="alt_contact" placeholder="Alternative Contact Number" value="<?php echo htmlspecialchars(!empty($lead['alt_contact']) ? $lead['alt_contact'] : ($lead['alt_mobile'] ?? '')); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($lead['email'] ?? ''); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Account Number</label>
                <input type="text" class="form-control" name="a_number" value="<?php echo htmlspecialchars($lead['a_number'] ?? ''); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Account Under Vertical</label>
                <input type="text" class="form-control" name="a_department" value="<?php echo htmlspecialchars($lead['a_department'] ?? ''); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Current Monthly Revenue Amount</label>
                <input type="text" class="form-control only_number" name="r_amount" value="<?php echo htmlspecialchars($lead['r_amount'] ?? ''); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Current Monthly Revenue Date</label>
                <input type="text" class="form-control datepicker" name="r_date" value="<?php echo !empty($lead['r_date']) && $lead['r_date'] != '0000-00-00' ? date('d-m-Y', strtotime($lead['r_date'])) : ''; ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Category</label>
                <select class="form-control" style="width: 100%;" name="category">
                  <option value="">Select</option>
                  <option value="New" <?php echo ($lead['category'] == 'New') ? 'selected' : ''; ?>>New</option>
                  <option value="Existing" <?php echo ($lead['category'] == 'Existing') ? 'selected' : ''; ?>>Existing</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Company Status</label>
                <select class="form-control" style="width: 100%;" name="company_status">
                  <option value="Active" <?php echo (!empty($lead['company_status']) && $lead['company_status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                  <option value="Inactive" <?php echo (!empty($lead['company_status']) && $lead['company_status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                  <option value="Closed" <?php echo (!empty($lead['company_status']) && $lead['company_status'] == 'Closed') ? 'selected' : ''; ?>>Closed</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Industry Category</label>
                <select class="form-control" style="width: 100%;" name="industry_category">
                  <option value="">Select</option>
                  <?php if (!empty($business_categories)) {
                    foreach ($business_categories as $bc) { ?>
                      <option value="<?php echo htmlspecialchars($bc['title']); ?>" <?php echo ((!empty($lead['industry_category']) && $lead['industry_category'] == $bc['title']) || (!empty($lead['business_category']) && $lead['business_category'] == $bc['title'])) ? 'selected' : ''; ?>><?php echo htmlspecialchars($bc['title']); ?></option>
                  <?php } } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Trade License Number</label>
                <input type="text" class="form-control" name="tl_number" value="<?php echo htmlspecialchars($lead['tl_number'] ?? ''); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Trade License Emirates</label>
                <select class="form-control" style="width: 100%;" name="tl_emirate">
                  <option value="">Select Emirate</option>
                  <?php 
                    $emirates = array('Abu Dhabi', 'Dubai', 'Sharjah', 'Ajman', 'Umm Al Quwain', 'Ras Al Khaimah', 'Fujairah');
                    foreach ($emirates as $em) {
                  ?>
                    <option value="<?php echo $em; ?>" <?php echo (!empty($lead['tl_emirate']) && $lead['tl_emirate'] == $em) ? 'selected' : ''; ?>><?php echo $em; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Trade License Zone</label>
                <select class="form-control" style="width: 100%;" name="tl_zone">
                  <option value="">Select Zone</option>
                  <option value="Mainland" <?php echo (!empty($lead['tl_zone']) && $lead['tl_zone'] == 'Mainland') ? 'selected' : ''; ?>>Mainland</option>
                  <option value="Freezone" <?php echo (!empty($lead['tl_zone']) && $lead['tl_zone'] == 'Freezone') ? 'selected' : ''; ?>>Freezone</option>
                  <option value="Offshore" <?php echo (!empty($lead['tl_zone']) && $lead['tl_zone'] == 'Offshore') ? 'selected' : ''; ?>>Offshore</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Trade License Authority</label>
                <input type="text" class="form-control" name="tl_authority" value="<?php echo htmlspecialchars($lead['tl_authority'] ?? ''); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Trade License Expire Date</label>
                <input type="text" class="form-control datepicker" name="tl_expire_date" value="<?php echo !empty($lead['tl_expire_date']) && $lead['tl_expire_date'] != '0000-00-00' ? date('d-m-Y', strtotime($lead['tl_expire_date'])) : ''; ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Trade License Due Info</label>
                <input type="text" class="form-control" name="tl_due" value="<?php echo htmlspecialchars($lead['tl_due'] ?? ''); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Number of MOL Employee</label>
                <input type="text" class="form-control only_number" name="no_of_mol_employee" value="<?php echo htmlspecialchars($lead['no_of_mol_employee'] ?? ''); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Last MOL Payment Date</label>
                <input type="text" class="form-control datepicker" name="last_mol" value="<?php echo !empty($lead['last_mol']) && $lead['last_mol'] != '0000-00-00' ? date('d-m-Y', strtotime($lead['last_mol'])) : ''; ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Year Ending</label>
                <input type="text" class="form-control" name="year_ending" value="<?php echo htmlspecialchars($lead['year_ending'] ?? ''); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Corporate Tax Status</label>
                <select class="form-control" style="width: 100%;" name="corporate_tax_status">
                  <option value="Not Registered" <?php echo (!empty($lead['corporate_tax_status']) && $lead['corporate_tax_status'] == 'Not Registered') ? 'selected' : ''; ?>>Not Registered</option>
                  <option value="Registered" <?php echo (!empty($lead['corporate_tax_status']) && $lead['corporate_tax_status'] == 'Registered') ? 'selected' : ''; ?>>Registered</option>
                  <option value="Exempt" <?php echo (!empty($lead['corporate_tax_status']) && $lead['corporate_tax_status'] == 'Exempt') ? 'selected' : ''; ?>>Exempt</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>TRC Registration</label>
                <input type="text" class="form-control" name="trc_reg" value="<?php echo htmlspecialchars($lead['trc_reg'] ?? ''); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>VAT Qtr</label>
                <input type="text" class="form-control" name="vat_qtr" value="<?php echo htmlspecialchars($lead['vat_qtr'] ?? ''); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>FTA Remark</label>
                <input type="text" class="form-control" name="fta_remark" value="<?php echo htmlspecialchars($lead['fta_remark'] ?? ''); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Due Date</label>
                <input type="text" class="form-control datepicker" name="due_date" value="<?php echo !empty($lead['due_date']) && $lead['due_date'] != '0000-00-00' ? date('d-m-Y', strtotime($lead['due_date'])) : ''; ?>">
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="box box-default">
        <div class="box-header with-border" style="position: relative; text-align: center; padding: 14px 15px; min-height: 48px;">
          <h3 class="box-title" style="float: none; display: inline-block; font-weight: 600; font-size: 18px; margin: 0; line-height: 24px;">Service &amp; Quotation Details</h3>
        </div>

        <div class="box-body">
          <div class="row">

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Service Category</label>
                <select class="form-control" style="width: 100%;" name="s_cat">
                  <option value="">Select Service Category</option>
                  <?php if (!empty($service_cats)) {
                    foreach ($service_cats as $sc) { ?>
                      <option value="<?php echo htmlspecialchars($sc['title']); ?>" <?php echo (!empty($lead['s_cat']) && $lead['s_cat'] == $sc['title']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($sc['title']); ?></option>
                  <?php } } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Product Category</label>
                <select class="form-control" style="width: 100%;" name="p_cat">
                  <option value="">Select Product Category</option>
                  <?php if (!empty($p_cat)) {
                    foreach ($p_cat as $pc) { ?>
                      <option value="<?php echo htmlspecialchars($pc['id']); ?>" <?php echo (!empty($lead['p_cat']) && $lead['p_cat'] == $pc['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($pc['title']); ?></option>
                  <?php } } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Product Details</label>
                <input type="text" class="form-control" name="p_details" value="<?php echo htmlspecialchars($lead['p_details'] ?? ''); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Quantity</label>
                <input type="text" class="form-control only_number" name="qty" value="<?php echo htmlspecialchars($lead['qty'] ?? '1'); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>MRC (Monthly Recurring Charge)</label>
                <input type="text" class="form-control only_number" name="mrc" id="input_mrc" value="<?php echo htmlspecialchars($lead['mrc'] ?? '0'); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Revenue</label>
                <input type="text" class="form-control only_number" name="revenue" id="input_revenue" value="<?php echo htmlspecialchars($lead['revenue'] ?? '0'); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>MRR (Total Monthly Recurring)</label>
                <input type="text" class="form-control only_number" name="mrr" id="input_mrr" readonly value="<?php echo htmlspecialchars($lead['mrr'] ?? '0'); ?>" style="font-weight: 700; background: #f8fafc;">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Quote Status</label>
                <select class="form-control" style="width: 100%;" name="quote_status">
                  <option value="">Select</option>
                  <option value="Pending" <?php echo (!empty($lead['quote_status']) && $lead['quote_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                  <option value="Shared" <?php echo (!empty($lead['quote_status']) && $lead['quote_status'] == 'Shared') ? 'selected' : ''; ?>>Shared</option>
                  <option value="Approved" <?php echo (!empty($lead['quote_status']) && $lead['quote_status'] == 'Approved') ? 'selected' : ''; ?>>Approved</option>
                  <option value="Rejected" <?php echo (!empty($lead['quote_status']) && $lead['quote_status'] == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Quote Mode</label>
                <select class="form-control" style="width: 100%;" name="quote_mode">
                  <option value="">Select</option>
                  <option value="Email" <?php echo (!empty($lead['quote_mode']) && $lead['quote_mode'] == 'Email') ? 'selected' : ''; ?>>Email</option>
                  <option value="WhatsApp" <?php echo (!empty($lead['quote_mode']) && $lead['quote_mode'] == 'WhatsApp') ? 'selected' : ''; ?>>WhatsApp</option>
                  <option value="In Person" <?php echo (!empty($lead['quote_mode']) && $lead['quote_mode'] == 'In Person') ? 'selected' : ''; ?>>In Person</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Quote Amount</label>
                <input type="text" class="form-control only_number" name="quote_amount" value="<?php echo htmlspecialchars($lead['quote_amount'] ?? ''); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Status (Call / Lead Status)</label>
                <select class="form-control" style="width: 100%;" name="status" id="process_status_select">
                  <option value="">Select Status</option>
                  <?php if (!empty($st_list)) {
                    foreach ($st_list as $st) { ?>
                      <option value="<?php echo $st['id']; ?>" <?php echo ($lead['status'] == $st['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($st['status']); ?></option>
                  <?php } } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Status Category</label>
                <input type="text" class="form-control" name="status_category" value="<?php echo htmlspecialchars($lead['status_category'] ?? ''); ?>">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Exp. Close Date</label>
                <input type="text" class="form-control datepicker" name="close_date" id="close_date" value="<?php echo !empty($lead['close_date']) && $lead['close_date'] != '0000-00-00' ? date('d-m-Y', strtotime($lead['close_date'])) : ''; ?>">
              </div>
            </div>

            <div class="col-lg-6 col-sm-12">
              <div class="form-group">
                <label>Remarks</label>
                <textarea class="form-control" name="remarks" rows="2"><?php echo htmlspecialchars($lead['remark'] ?? ''); ?></textarea>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="box box-default">
        <div class="box-header with-border" style="position: relative; text-align: center; padding: 14px 15px; min-height: 48px;">
          <h3 class="box-title" style="float: none; display: inline-block; font-weight: 600; font-size: 18px; margin: 0; line-height: 24px;">Back Office Updates</h3>
        </div>

        <div class="box-body">
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr style="background: #f8fafc;">
                  <th style="font-weight: 700; width: 180px;">Category</th>
                  <th style="font-weight: 700; width: 140px;">Date</th>
                  <th style="font-weight: 700; width: 180px;">Status</th>
                  <th style="font-weight: 700; width: 180px;">Reference</th>
                  <th style="font-weight: 700;">Remarks</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $default_categories = array(
                    'SPR',
                    'Company Code',
                    'Lead Activity',
                    'Document Col/Sub',
                    'Pending Documents',
                    'Video Verification',
                    'Post Verification'
                  );
                  for ($i = 0; $i < 7; $i++) {
                    $b_id = isset($bo_list[$i]['id']) ? $bo_list[$i]['id'] : '';
                    $b_cat = isset($bo_list[$i]['bo_cat']) && !empty($bo_list[$i]['bo_cat']) ? $bo_list[$i]['bo_cat'] : $default_categories[$i];
                    $b_date = isset($bo_list[$i]['bo_date']) ? $bo_list[$i]['bo_date'] : '';
                    $b_status = isset($bo_list[$i]['bo_status']) ? $bo_list[$i]['bo_status'] : 'Not Applicable';
                    $b_ref = isset($bo_list[$i]['bo_ref']) ? $bo_list[$i]['bo_ref'] : '';
                    $b_rem = isset($bo_list[$i]['bo_remark']) ? $bo_list[$i]['bo_remark'] : '';
                ?>
                <tr>
                  <td>
                    <input type="hidden" name="bo_id[]" value="<?php echo htmlspecialchars($b_id); ?>">
                    <input type="text" class="form-control" name="bo_cat[]" value="<?php echo htmlspecialchars($b_cat); ?>" readonly style="background-color:#f8fafc; font-weight:600;">
                  </td>
                  <td><input type="text" class="form-control datepicker" name="bo_date[]" value="<?php echo htmlspecialchars($b_date); ?>"></td>
                  <td>
                    <select class="form-control" style="width: 100%;" name="bo_status[]">
                      <option value="Not Applicable" <?php echo ($b_status == 'Not Applicable') ? 'selected' : ''; ?>>Not Applicable</option>
                      <option value="Pending Approval" <?php echo ($b_status == 'Pending Approval') ? 'selected' : ''; ?>>Pending Approval</option>
                      <option value="Query" <?php echo ($b_status == 'Query') ? 'selected' : ''; ?>>Query</option>
                      <option value="Approved" <?php echo ($b_status == 'Approved') ? 'selected' : ''; ?>>Approved</option>
                      <option value="Expired" <?php echo ($b_status == 'Expired') ? 'selected' : ''; ?>>Expired</option>
                      <option value="Other" <?php echo ($b_status == 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                  </td>
                  <?php if ($i == 1 || $i == 4) { ?>
                    <td colspan="2"><input type="text" class="form-control" name="bo_remark[]" value="<?php echo htmlspecialchars($b_rem); ?>" placeholder="Remarks"></td>
                  <?php } else { ?>
                    <td><input type="text" class="form-control" name="bo_ref[]" value="<?php echo htmlspecialchars($b_ref); ?>" placeholder="Ref No"></td>
                    <td><input type="text" class="form-control" name="bo_remark[]" value="<?php echo htmlspecialchars($b_rem); ?>" placeholder="Remarks"></td>
                  <?php } ?>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="box box-default" style="border: 1px solid #e2e8f0; border-radius: 6px;">
        <input type="hidden" name="vertical_code" value="<?php echo htmlspecialchars($lead['servicecat'] ?? 'general'); ?>">
        <input type="hidden" name="service_title" value="<?php echo htmlspecialchars(!empty($lead['servicecat']) ? $lead['servicecat'] : 'Standard Service Scope'); ?>">
        <input type="hidden" name="pricing_amount" value="<?php echo htmlspecialchars($lead['mrr'] ?? '0.00'); ?>">
        <input type="hidden" name="assigned_dept" value="Back Office">

        <div class="box-body" style="padding: 20px;">
          <div class="row">

            <div class="col-md-4 col-sm-12">
              <div class="form-group">
                <label style="font-weight: 700; font-size: 11px; text-transform: uppercase; color: #475569;">Assigned Staff</label>
                <select name="assigned_to" class="form-control" style="border: 1px solid #cbd5e1; border-radius: 4px;">
                  <option value="0">Unassigned</option>
                  <?php 
                    $cur_assigned = !empty($lead['assigned_to']) ? (int)$lead['assigned_to'] : (!empty($lead['lead_assign']) ? (int)$lead['lead_assign'] : 0);
                    if (!empty($staff_list)) {
                      foreach ($staff_list as $st) {
                  ?>
                    <option value="<?php echo $st['id']; ?>" <?php echo ($cur_assigned === (int)$st['id']) ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($st['name'] . (!empty($st['department']) ? ' (' . $st['department'] . ')' : '')); ?>
                    </option>
                  <?php } } ?>
                </select>
              </div>
            </div>

            <div class="col-md-8 col-sm-12">
              <div class="form-group">
                <label style="font-weight: 700; font-size: 11px; text-transform: uppercase; color: #475569;">Service Description Scope</label>
                <textarea name="service_scope" class="form-control" rows="2" placeholder="Detailed scope of service..." style="border: 1px solid #cbd5e1; border-radius: 4px;"><?php echo htmlspecialchars($lead['order_request_remarks'] ?? ''); ?></textarea>
              </div>
            </div>

            <div class="col-sm-12">
              <div class="form-group" style="margin-bottom: 0;">
                <label style="font-weight: 700; font-size: 11px; text-transform: uppercase; color: #475569;">Approval Processing Notice</label>
                <textarea name="approval_notes" class="form-control" rows="2" placeholder="Notes for order history and audit trail..." style="border: 1px solid #cbd5e1; border-radius: 4px;"></textarea>
              </div>
            </div>

          </div>
        </div>

        <div class="box-footer text-center" style="background: #f8fafc; padding: 16px; border-top: 1px solid #e2e8f0;">
          <button type="submit" name="action_type" value="send_for_approval" class="btn btn-primary" style="background: #0072f0; border-color: #0072f0; color: #ffffff; font-weight: 700; padding: 10px 24px; border-radius: 4px; margin-right: 8px;">
            <i class="fa fa-send"></i> Send for Approval
          </button>
          <button type="button" class="btn btn-default" data-toggle="modal" data-target="#rejectModal" style="background: #ffffff; color: #dc2626; font-weight: 600; padding: 10px 18px; border: 1px solid #dc2626; border-radius: 4px; margin-right: 8px;">
            <i class="fa fa-times-circle"></i> Reject
          </button>
          <a href="<?php echo base_url(); ?>manager/orders/pending_process" class="btn btn-default" style="background: #ffffff; color: #0f172a; font-weight: 600; padding: 10px 18px; border: 1px solid #cbd5e1; border-radius: 4px;">
            Cancel
          </a>
        </div>
      </div>

    </form>

  </section>

</div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width: 440px;">
    <div class="modal-content" style="border-radius: 6px; border: 1px solid #000000; overflow: hidden;">
      <form action="<?php echo base_url(); ?>manager/orders/edit_pending/<?php echo $lead['id']; ?>" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="action_type" value="reject_order">
        <div class="modal-header" style="background: #000000; color: #ffffff; padding: 14px 18px; border-bottom: 1px solid #000000;">
          <button type="button" class="close" data-dismiss="modal" style="color: #ffffff; opacity: 0.8;">&times;</button>
          <h4 class="modal-title" style="font-size: 15px; font-weight: 700; color: #ffffff; margin: 0;"><i class="fa fa-times-circle"></i> Reject Order Request</h4>
        </div>
        <div class="modal-body" style="padding: 20px; background: #ffffff; color: #000000;">
          <p style="font-size: 13px; color: #475569; margin-bottom: 14px;">Please specify the reason for rejecting this Lead-to-Order conversion request. The lead will be returned to normal state and unlocked.</p>
          <div class="form-group" style="margin-bottom: 0;">
            <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #475569;">Rejection Reason *</label>
            <textarea name="reject_reason" class="form-control" rows="3" placeholder="Provide clear reason for rejection..." required style="border: 1px solid #cbd5e1; border-radius: 4px; resize: vertical;"></textarea>
          </div>
        </div>
        <div class="modal-footer" style="padding: 12px 18px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 8px;">
          <button type="button" class="btn btn-default" data-dismiss="modal" style="background: #ffffff; border: 1px solid #000000; color: #000000; font-weight: 600;">Cancel</button>
          <button type="submit" class="btn btn-default" style="background: #000000; border: 1px solid #000000; color: #ffffff; font-weight: 600;">Confirm Rejection</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
  $('#input_mrc, #input_revenue').on('input change', function() {
    var m = parseFloat($('#input_mrc').val()) || 0;
    var r = parseFloat($('#input_revenue').val()) || 0;
    $('#input_mrr').val(m + r);
  });

  $('#selectExistingCompany').on('change', function() {
    var comp = $(this).val();
    if (!comp) return;
    $.ajax({
      url: '<?php echo base_url(); ?>manager/kam/ajax_get_company_details',
      type: 'POST',
      data: {
        company: comp,
        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
      },
      dataType: 'json',
      success: function(resp) {
        if (resp && resp.status === 'success') {
          if (resp.company_name) $('#customer_input').val(resp.company_name);
          if (resp.owner_name) $('#owner_name_input').val(resp.owner_name);
          if (resp.poc) $('input[name="poc"]').val(resp.poc);
          if (resp.contact) $('input[name="contact1"]').val(resp.contact);
          if (resp.email) $('input[name="email"]').val(resp.email);
          if (resp.tl_number) $('input[name="tl_number"]').val(resp.tl_number);
          if (resp.tl_emirate) $('select[name="tl_emirate"]').val(resp.tl_emirate);
          if (resp.tl_zone) $('select[name="tl_zone"]').val(resp.tl_zone);
          if (resp.tl_authority) $('select[name="tl_authority"]').val(resp.tl_authority);
          if (resp.tl_expire_date) $('input[name="tl_expire_date"]').val(resp.tl_expire_date);
          if (resp.location) $('input[name="location"]').val(resp.location);
          if (resp.status_category) $('select[name="status_category"]').val(resp.status_category);
          if (resp.client_status) $('select[name="company_status"]').val(resp.client_status);
          if (resp.primary_assignee) $('#selectOnboardManager').val(resp.primary_assignee).trigger('change');
        }
      }
    });
  });
});
</script>

<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
