<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper">
  <section class="content-header" style="display:flex; justify-content:space-between; align-items:center; padding:15px 15px 5px 15px;">
    <h1 style="margin:0; font-size:20px; font-weight:700; color:#0f172a;">
      <i class="fa fa-hourglass-half" style="color:#2563eb; margin-right:8px;"></i><?php echo $info['page_heading']; ?>
    </h1>
    <div>
      <a href="<?php echo base_url(); ?>manager/orders" class="btn btn-default" style="font-weight:600; padding:6px 14px; border-radius:5px;">
        <i class="fa fa-list"></i> View All Orders
      </a>
    </div>
  </section>

  <section class="content" style="padding-top:10px;">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="box box-default" style="border-radius:6px;">
      <div class="box-header with-border" style="display:flex; justify-content:space-between; align-items:center;">
        <h3 class="box-title" style="font-size:15px; font-weight:600; color:#1e293b;">
          Leads Awaiting Back Office Order Processing
        </h3>
        <span class="label label-warning" style="font-size:12px; padding:4px 8px;"><?php echo count($pending_leads); ?> Pending Request<?php echo count($pending_leads) == 1 ? '' : 's'; ?></span>
      </div>
      <div class="box-body" style="padding:15px;">
        <?php if(!empty($pending_leads)){ ?>
          <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped" style="width:100%; font-size:12px;">
              <thead>
                <tr style="background:#f8fafc; color:#334155;">
                  <th>#</th>
                  <th>Database Ref</th>
                  <th>Lead Ref</th>
                  <th>Client / Contact</th>
                  <th>Mobile / Phone</th>
                  <th>Service Requirements &amp; Remarks</th>
                  <th>Requested By</th>
                  <th>Request Date</th>
                  <th style="width:170px; text-align:center;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($pending_leads as $idx => $p){ ?>
                  <tr>
                    <td><?php echo $idx + 1; ?></td>
                    <td>
                      <span class="label label-default" style="background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; font-family:monospace;">
                        <?php echo htmlspecialchars($p['db_ref_no'] ?: ('DB-' . $p['id'])); ?>
                      </span>
                    </td>
                    <td>
                      <span class="label label-info" style="font-family:monospace;">
                        <?php echo htmlspecialchars($p['lead_ref_no'] ?: ('LEAD-' . $p['id'])); ?>
                      </span>
                    </td>
                    <td>
                      <strong><?php echo htmlspecialchars($p['customer'] ?: 'Unnamed'); ?></strong>
                      <?php if(!empty($p['company_name'])){ ?>
                        <br><small class="text-muted"><?php echo htmlspecialchars($p['company_name']); ?></small>
                      <?php } ?>
                    </td>
                    <td><?php echo htmlspecialchars($p['contact1'] ?: '-'); ?></td>
                    <td>
                      <div style="max-width:320px; white-space:pre-wrap; word-break:break-word;">
                        <?php echo htmlspecialchars($p['order_request_remarks'] ?: 'No additional notes provided'); ?>
                      </div>
                    </td>
                    <td>
                      <strong><?php echo htmlspecialchars($p['requested_by_name'] ?: 'Staff'); ?></strong>
                    </td>
                    <td>
                      <?php echo !empty($p['order_requested_at']) ? date('d M Y, h:i A', strtotime($p['order_requested_at'])) : '-'; ?>
                    </td>
                    <td style="text-align:center;">
                      <a href="<?php echo base_url(); ?>manager/leads/view/<?php echo base64_encode($p['id']); ?>" class="btn btn-xs btn-default" style="margin-right:4px;">Preview</a>
                      <a href="<?php echo base_url(); ?>manager/orders/edit_pending/<?php echo $p['id']; ?>" class="btn btn-xs btn-primary" style="background:#2563eb; border-color:#1d4ed8; font-weight:600;">Edit</a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        <?php } else { ?>
          <div style="text-align:center; padding:40px 20px; color:#64748b;">
            <i class="fa fa-check-circle" style="font-size:36px; color:#16a34a; margin-bottom:10px;"></i>
            <h4 style="font-weight:600; color:#1e293b; margin:0 0 6px 0;">No Pending Order Requests</h4>
            <p style="margin:0; font-size:13px;">All lead order requests have been processed by Back Office.</p>
          </div>
        <?php } ?>
      </div>
    </div>
  </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
