<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<div class="content-wrapper" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;"> 
  <section class="content-header" style="padding: 20px 24px 10px 24px;">
    <h1 style="font-size: 22px; font-weight: 700; color: #0f172a; margin: 0; letter-spacing: -0.02em;">
      Import Leads
    </h1>
  </section>
  
  <section class="content" style="padding: 16px 24px;"> 
    <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
    <?php echo get_message($this->session->flashdata('message'),'message'); ?>

    <div class="row">
      <div class="col-md-8 col-md-offset-2 col-sm-12">
        <div class="box" style="background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.04); overflow: hidden;">
          <div class="box-header" style="padding: 18px 24px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
            <h3 class="box-title" style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0;">
              Upload CSV Spreadsheet
            </h3>
            <div class="box-tools">
              <a href="<?php echo base_url(); ?>manager/leads/download_sample_csv" class="btn" style="background: #ffffff; color: #0f172a; border: 1px solid #e2e8f0; font-size: 12px; font-weight: 600; padding: 6px 14px; border-radius: 6px;">
                <i class="fa fa-download" style="margin-right: 6px; color: #64748b;"></i> Download Template
              </a>
            </div>
          </div>
          
          <div class="box-body" style="padding: 28px 24px;">
            <?php if (!empty($is_admin) || !empty($is_support)) { ?>
              <form action="<?php echo base_url(); ?>manager/leads/import_action" method="post" enctype="multipart/form-data" id="import_form">
                
                <div class="form-group" style="margin-bottom: 24px;">
                  <label for="csv_file" style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 10px;">
                    Select File
                  </label>
                  
                  <div style="border: 2px dashed #cbd5e1; border-radius: 8px; padding: 36px 20px; text-align: center; background: #f8fafc; transition: border-color 0.2s;" id="drop_zone">
                    <i class="fa fa-file-text-o" style="font-size: 32px; color: #94a3b8; margin-bottom: 12px; display: block;"></i>
                    <p style="font-size: 14px; font-weight: 600; color: #0f172a; margin: 0 0 6px 0;" id="file_selected_text">
                      Choose CSV file to upload
                    </p>
                    <p style="font-size: 12px; color: #64748b; margin: 0 0 16px 0;">
                      Standard CSV format with header row
                    </p>
                    <label for="csv_file" class="btn" style="background: #ffffff; color: #0f172a; border: 1px solid #cbd5e1; font-size: 13px; font-weight: 600; padding: 7px 18px; border-radius: 6px; cursor: pointer; margin: 0;">
                      Browse Computer
                    </label>
                    <input type="file" name="csv_file" id="csv_file" accept=".csv" required style="display: none;" onchange="handleFileSelect(this)">
                  </div>
                </div>

                <div style="display: flex; align-items: center; justify-content: flex-start; gap: 12px; margin-top: 24px;">
                  <button type="submit" class="btn" style="background: #000000; color: #ffffff; border: 1px solid #000000; font-size: 13px; font-weight: 600; padding: 9px 24px; border-radius: 6px;">
                    <i class="fa fa-upload" style="margin-right: 6px;"></i> Import Records
                  </button>
                  <a href="<?php echo base_url(); ?>manager/leads" class="btn" style="background: #ffffff; color: #475569; border: 1px solid #e2e8f0; font-size: 13px; font-weight: 600; padding: 9px 20px; border-radius: 6px;">
                    Cancel
                  </a>
                </div>

              </form>

              <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid #f1f5f9; display: flex; align-items: center; gap: 8px; font-size: 12px; color: #64748b;">
                <i class="fa fa-info-circle" style="color: #94a3b8;"></i>
                Customer Name is mandatory for all imported rows. Columns will map automatically.
              </div>

            <?php } else { ?>
              <div class="alert alert-info" style="margin: 0;">
                No Permission: Lead imports are restricted to Administrator and Support accounts.
              </div>
            <?php } ?>

          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
function handleFileSelect(input) {
  if (input.files && input.files[0]) {
    var fileName = input.files[0].name;
    var fileSize = (input.files[0].size / 1024).toFixed(1) + ' KB';
    document.getElementById('file_selected_text').innerHTML = '<span style="color:#0f172a; font-weight:700;">' + fileName + '</span> (' + fileSize + ')';
    document.getElementById('drop_zone').style.borderColor = '#000000';
    document.getElementById('drop_zone').style.background = '#f1f5f9';
  }
}
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
