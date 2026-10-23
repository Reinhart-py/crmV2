<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<div class="content-wrapper"> 
  <section class="content-header">
    <h1> Import Leads </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?php echo base_url(); ?>manager/leads">Leads</a></li>
      <li class="active">Import</li>
    </ol>
  </section>
  
  <section class="content"> 
    <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
    <?php echo get_message($this->session->flashdata('message'),'message'); ?>

    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="box box-primary" style="border-radius: 8px; box-shadow: 0 4px 14px rgba(0,0,0,0.06);">
          <div class="box-header with-border" style="padding: 16px 20px;">
            <h3 class="box-title" style="font-weight: 700; font-size: 16px;">
              <i class="fa fa-upload text-primary" style="margin-right: 8px;"></i> Import Leads Data
            </h3>
            <div class="box-tools pull-right">
              <a href="<?php echo base_url(); ?>manager/leads/download_sample_csv" class="btn btn-default btn-sm" style="border-radius: 4px; font-weight: 600;">
                <i class="fa fa-download text-success"></i> Download Sample CSV
              </a>
            </div>
          </div>
          
          <div class="box-body" style="padding: 24px;">
            <?php if (!empty($is_admin) || !empty($is_support)) { ?>
              <form action="<?php echo base_url(); ?>manager/leads/import_action" method="post" enctype="multipart/form-data">
                <div class="form-group" style="margin-bottom: 24px;">
                  <label for="csv_file" style="font-weight: 600; font-size: 14px; margin-bottom: 8px; display: block;">
                    Choose CSV File <span class="text-danger">*</span>
                  </label>
                  <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv" required style="padding: 8px; height: auto;">
                  <span class="help-block" style="font-size: 12px; color: #64748b; margin-top: 6px;">
                    Only valid .csv files formatted per the template specifications are supported.
                  </span>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                  <button type="submit" class="btn btn-primary" style="padding: 9px 24px; font-weight: 600; border-radius: 5px;">
                    <i class="fa fa-cloud-upload" style="margin-right: 6px;"></i> Upload and Import Records
                  </button>
                  <a href="<?php echo base_url(); ?>manager/leads" class="btn btn-default" style="padding: 9px 20px; border-radius: 5px; margin-left: 8px;">
                    Cancel
                  </a>
                </div>
              </form>
            <?php } else { ?>
              <div class="alert alert-warning" style="border-radius: 6px; font-weight: 500;">
                <i class="fa fa-lock" style="margin-right: 6px;"></i> You do not have permission to import leads into the CRM. Only Admin and Support roles may execute bulk lead imports.
              </div>
            <?php } ?>

            <div style="margin-top: 32px; border-top: 1px solid #e2e8f0; padding-top: 20px;">
              <h4 style="font-size: 14px; font-weight: 700; color: #0f172a; margin-top: 0; margin-bottom: 12px;">
                <i class="fa fa-lightbulb-o text-warning" style="margin-right: 6px;"></i> Import Specifications & Instructions:
              </h4>
              <ul style="font-size: 13px; color: #475569; padding-left: 20px; line-height: 1.8;">
                <li>The CSV file must contain a header row. You can download the <a href="<?php echo base_url(); ?>manager/leads/download_sample_csv">Sample CSV template</a> for the exact columns.</li>
                <li><strong>Customer Name</strong> is mandatory for every record. Rows with an empty Customer Name will be skipped.</li>
                <li><strong>Initial Category fields</strong> (POC Name, Contact Number, Email, Account Number, Category, MRC, Revenue, MRR, Status, Exp Close Date) and <strong>Additional Category fields</strong> (Address, City, Country, State, Website Source, Follow-up Date/Time) are automatically mapped.</li>
                <li>Dates should be formatted as <code>DD-MM-YYYY</code> or <code>YYYY-MM-DD</code>.</li>
                <li>Imported leads will be assigned unique system Lead IDs and attributed to your account.</li>
              </ul>
            </div>

          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
