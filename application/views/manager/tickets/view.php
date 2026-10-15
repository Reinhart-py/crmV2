<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1> <?php echo $info['page_heading']; ?> </h1>
  </section>
  
  <section class="content">
    <div class="container">
      
      <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
      <?php echo get_message($this->session->flashdata('message'),'message'); ?>

      <div class="row">
        <div class="box box-default">
          <div class="box-header">
            <div class="col-sm-12">
              <div class="pull-left">
                <a href="<?php echo base_url(); ?>manager/tickets" class="btn btn-info">Back</a>
              </div>
              <div class="pull-right">
                <a href="<?php echo base_url(); ?>manager/tickets/edit/<?php echo $ticket['id']; ?>" class="btn btn-danger">Edit Ticket</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row my-profile">
        <div class="box box-default">
          <div class="box-header">
            <div class="col-sm-12">
              <h3 class="box-title" style="text-transform:capitalize">
                <b><?php echo htmlspecialchars(!empty($ticket['company_name']) ? $ticket['company_name'] : (!empty($ticket['customer']) ? $ticket['customer'] : 'Ticket')); ?> - <?php echo htmlspecialchars($ticket['ticket_number']); ?></b>
              </h3>
            </div>
          </div>
          <div class="box-body">
            <div class="row">
              
              <div class="col-md-6">
                <div class="table-responsive border">
                  <table class="table">
                    <tr>
                      <th scope="col" style="width: 40%;">Ticket No.</th>
                      <td><?php echo htmlspecialchars($ticket['ticket_number']); ?></td>
                    </tr>
                    <tr>
                      <th scope="col">Client Type</th>
                      <td><?php echo htmlspecialchars(!empty($ticket['client_type']) ? $ticket['client_type'] : 'Onboarded'); ?></td>
                    </tr>
                    <tr>
                      <th scope="col">Company Name</th>
                      <td><?php echo htmlspecialchars(!empty($ticket['company_name']) ? $ticket['company_name'] : (!empty($ticket['customer']) ? $ticket['customer'] : '-')); ?></td>
                    </tr>
                    <tr>
                      <th scope="col">POC Name</th>
                      <td><?php echo htmlspecialchars(!empty($ticket['poc']) ? $ticket['poc'] : '-'); ?></td>
                    </tr>
                    <tr>
                      <th scope="col">POC Contact Number</th>
                      <td><?php echo htmlspecialchars(!empty($ticket['contact1']) ? $ticket['contact1'] : '-'); ?></td>
                    </tr>
                    <tr>
                      <th scope="col">Alternative Contact Number</th>
                      <td><?php echo htmlspecialchars(!empty($ticket['alt_contact']) ? $ticket['alt_contact'] : '-'); ?></td>
                    </tr>
                    <tr>
                      <th scope="col">Email</th>
                      <td><?php echo htmlspecialchars(!empty($ticket['email']) ? $ticket['email'] : '-'); ?></td>
                    </tr>
                    <tr>
                      <th scope="col">City / Location</th>
                      <td><?php echo htmlspecialchars(!empty($ticket['city']) ? $ticket['city'] : '-'); ?></td>
                    </tr>
                    <tr>
                      <th scope="col">Ticket Source</th>
                      <td><?php echo htmlspecialchars(!empty($ticket['lead_source']) ? $ticket['lead_source'] : 'Internal Request'); ?></td>
                    </tr>
                  </table>
                </div>
              </div>

              <div class="col-md-6">
                <div class="table-responsive border">
                  <table class="table">
                    <tr>
                      <th scope="col" style="width: 40%;">Ticket Title</th>
                      <td><?php echo htmlspecialchars(!empty($ticket['ticket_title']) ? $ticket['ticket_title'] : $ticket['subject']); ?></td>
                    </tr>
                    <tr>
                      <th scope="col">Department</th>
                      <td><?php echo htmlspecialchars(!empty($ticket['department']) ? $ticket['department'] : 'General'); ?></td>
                    </tr>
                    <tr>
                      <th scope="col">Ticket Type</th>
                      <td><?php echo htmlspecialchars(!empty($ticket['ticket_type']) ? $ticket['ticket_type'] : 'Technical Issue'); ?></td>
                    </tr>
                    <tr>
                      <th scope="col">Category</th>
                      <td><?php echo htmlspecialchars(!empty($ticket['category']) ? $ticket['category'] : 'Other'); ?></td>
                    </tr>
                    <tr>
                      <th scope="col">Priority</th>
                      <td><?php echo htmlspecialchars($ticket['priority']); ?></td>
                    </tr>
                    <tr>
                      <th scope="col">Status</th>
                      <td><strong><?php echo htmlspecialchars($ticket['status']); ?></strong></td>
                    </tr>
                    <tr>
                      <th scope="col">Assigned To</th>
                      <td>
                        <?php 
                          $assignee_names = array();
                          if(!empty($assigned_user_names)){
                            foreach($assigned_user_names as $au){ $assignee_names[] = $au['name']; }
                          }
                          echo !empty($assignee_names) ? htmlspecialchars(implode(', ', $assignee_names)) : 'Unassigned';
                        ?>
                      </td>
                    </tr>
                    <tr>
                      <th scope="col">Tagged / Notify Users</th>
                      <td>
                        <?php 
                          $tagged_names = array();
                          if(!empty($tagged_user_names)){
                            foreach($tagged_user_names as $tu){ $tagged_names[] = $tu['name']; }
                          }
                          echo !empty($tagged_names) ? htmlspecialchars(implode(', ', $tagged_names)) : 'None';
                        ?>
                      </td>
                    </tr>
                    <tr>
                      <th scope="col">Raised By</th>
                      <td><?php echo htmlspecialchars(!empty($ticket['creator_name']) ? $ticket['creator_name'] : 'User'); ?></td>
                    </tr>
                    <tr>
                      <th scope="col">Created Date</th>
                      <td><?php echo simple_date(!empty($ticket['created_date']) ? $ticket['created_date'] : $ticket['created_at']); ?></td>
                    </tr>
                    <tr>
                      <th scope="col">Deadline Date</th>
                      <td><?php echo !empty($ticket['deadline_date']) ? simple_date($ticket['deadline_date']) : '-'; ?></td>
                    </tr>
                    <tr>
                      <th scope="col">Expected Closure Date</th>
                      <td><?php echo !empty($ticket['expected_close_date']) ? simple_date($ticket['expected_close_date']) : '-'; ?></td>
                    </tr>
                  </table>
                </div>
              </div>

              <div class="col-md-12" style="margin-top: 15px;">
                <div class="table-responsive border">
                  <table class="table">
                    <tr>
                      <th scope="col" style="width: 20%;">Description</th>
                      <td><?php echo nl2br(htmlspecialchars($ticket['description'])); ?></td>
                    </tr>
                    <?php if(!empty($ticket['internal_notes'])){ ?>
                    <tr>
                      <th scope="col">Internal Notes</th>
                      <td><?php echo nl2br(htmlspecialchars($ticket['internal_notes'])); ?></td>
                    </tr>
                    <?php } ?>
                    <?php if(!empty($ticket['attachment'])){ ?>
                    <tr>
                      <th scope="col">Attachment</th>
                      <td>
                        <a href="<?php echo base_url(); ?>assets/uploads/tickets/<?php echo htmlspecialchars($ticket['attachment']); ?>" target="_blank" class="btn btn-xs btn-primary">
                          <i class="fa fa-download"></i> Download Attachment (<?php echo htmlspecialchars($ticket['attachment']); ?>)
                        </a>
                      </td>
                    </tr>
                    <?php } ?>
                    <?php if(!empty($ticket['resolution_remarks'])){ ?>
                    <tr>
                      <th scope="col">Resolution Remarks</th>
                      <td>
                        <?php echo nl2br(htmlspecialchars($ticket['resolution_remarks'])); ?>
                        <?php if(!empty($ticket['reviewer_name'])){ ?>
                          <div style="margin-top: 5px; color: #555; font-size: 12px;">
                            Reviewed & Confirmed By: <strong><?php echo htmlspecialchars($ticket['reviewer_name']); ?></strong> on <?php echo simple_date($ticket['resolution_confirmed_at']); ?>
                          </div>
                        <?php } ?>
                      </td>
                    </tr>
                    <?php } ?>
                  </table>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="box box-default">
          <div class="box-header with-border">
            <h3 class="box-title">Update Status & Progress</h3>
          </div>
          <div class="box-body">
            <form action="<?php echo base_url(); ?>manager/tickets/update_status" method="post">
              <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
              <div class="row">
                <div class="col-md-4 col-sm-6">
                  <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control select2" style="width: 100%;">
                      <option value="Open" <?php if($ticket['status'] == 'Open') echo 'selected'; ?>>Open</option>
                      <option value="Assigned" <?php if($ticket['status'] == 'Assigned') echo 'selected'; ?>>Assigned</option>
                      <option value="In Progress" <?php if($ticket['status'] == 'In Progress') echo 'selected'; ?>>In Progress</option>
                      <option value="Pending" <?php if($ticket['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                      <option value="On Hold" <?php if($ticket['status'] == 'On Hold') echo 'selected'; ?>>On Hold</option>
                      <option value="Resolved" <?php if($ticket['status'] == 'Resolved') echo 'selected'; ?>>Resolved</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6 col-sm-6">
                  <div class="form-group">
                    <label>Progress Remarks</label>
                    <input type="text" name="remarks" class="form-control" placeholder="Enter remarks on action taken...">
                  </div>
                </div>
                <div class="col-md-2 col-sm-12" style="padding-top: 25px;">
                  <button type="submit" class="btn btn-warning btn-block">Update</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

      <?php if(!empty($can_manage_tickets)){ ?>
      <div class="row">
        <div class="box box-default">
          <div class="box-header with-border">
            <h3 class="box-title">Support Review & Ticket Closure</h3>
          </div>
          <div class="box-body">
            <?php if($ticket['status'] === 'Closed'){ ?>
              <p class="text-success"><i class="fa fa-check-circle"></i> This ticket is verified and Closed.</p>
            <?php } else { ?>
              <form action="<?php echo base_url(); ?>manager/tickets/update_status" method="post">
                <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                <input type="hidden" name="status" value="Closed">

                <div class="form-group">
                  <label>Resolution & Closing Remarks<span>*</span></label>
                  <textarea name="resolution_remarks" class="form-control" rows="3" placeholder="Provide final verification remarks..." required><?php echo htmlspecialchars($ticket['resolution_remarks'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" name="confirm_review" value="1" required> 
                      I confirm that I have manually reviewed and verified the resolution before closing this ticket.
                    </label>
                  </div>
                </div>

                <button type="submit" class="btn btn-success">Confirm Review & Close Ticket</button>
              </form>
            <?php } ?>
          </div>
        </div>
      </div>
      <?php } ?>

      <div class="row" id="followup">
        <div class="box box-default">
          <div class="box-header with-border">
            <h3 class="box-title">Follow-up History</h3>
          </div>
          <div class="box-body">
            
            <?php if(!empty($followups)){ ?>
              <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Follow-up Date</th>
                      <th>Next Follow-up Date</th>
                      <th>Assigned User</th>
                      <th>Remarks</th>
                      <th>Status</th>
                      <th>Next Action</th>
                      <th>Logged By</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach($followups as $fu){ ?>
                      <tr>
                        <td><?php echo simple_date($fu['followup_date']); ?></td>
                        <td><?php echo !empty($fu['next_followup_date']) ? simple_date($fu['next_followup_date']) : '-'; ?></td>
                        <td><?php echo htmlspecialchars(!empty($fu['assigned_name']) ? $fu['assigned_name'] : 'Staff'); ?></td>
                        <td><?php echo htmlspecialchars($fu['remarks']); ?></td>
                        <td><?php echo htmlspecialchars($fu['status']); ?></td>
                        <td><?php echo htmlspecialchars(!empty($fu['next_action']) ? $fu['next_action'] : '-'); ?></td>
                        <td><?php echo htmlspecialchars(!empty($fu['creator_name']) ? $fu['creator_name'] : 'User'); ?> (<?php echo simple_date($fu['created_at']); ?>)</td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            <?php } else { ?>
              <div class="alert alert-info">No follow-ups recorded yet.</div>
            <?php } ?>

            <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee;">
              <h4>Add Follow-up</h4>
              <form action="<?php echo base_url(); ?>manager/tickets/add_followup" method="post">
                <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                
                <div class="row">
                  <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                      <label>Follow-up Date<span>*</span></label>
                      <input type="text" name="followup_date" class="form-control datepicker" value="<?php echo date('Y-m-d'); ?>" required autocomplete="off">
                    </div>
                  </div>
                  <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                      <label>Next Follow-up Date</label>
                      <input type="text" name="next_followup_date" class="form-control datepicker" placeholder="YYYY-MM-DD" autocomplete="off">
                    </div>
                  </div>
                  <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                      <label>Assigned User</label>
                      <select name="assigned_user_id" class="form-control select2" style="width: 100%;">
                        <?php if(!empty($users_list)){
                          foreach($users_list as $u){ ?>
                            <option value="<?php echo $u['id']; ?>" <?php if($u['id'] == $this->session->userdata('manager_id')) echo 'selected'; ?>>
                              <?php echo htmlspecialchars($u['name']); ?>
                            </option>
                        <?php } } ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                      <label>Status</label>
                      <select name="status" class="form-control select2" style="width: 100%;">
                        <?php 
                        $stat_opts = array('Open', 'Assigned', 'In Progress', 'Pending', 'On Hold', 'Resolved');
                        if (!empty($can_manage_tickets)) $stat_opts[] = 'Closed';
                        foreach($stat_opts as $sopt){ ?>
                          <option value="<?php echo $sopt; ?>" <?php if($sopt == $ticket['status']) echo 'selected'; ?>><?php echo $sopt; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-8 col-sm-12">
                    <div class="form-group">
                      <label>Remarks<span>*</span></label>
                      <textarea name="remarks" class="form-control" rows="3" required placeholder="Enter follow-up details..."></textarea>
                    </div>
                  </div>
                  <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                      <label>Next Action</label>
                      <input type="text" name="next_action" class="form-control" placeholder="Action to be taken...">
                    </div>
                    <div style="margin-top: 15px;">
                      <button type="submit" class="btn btn-success">Save Follow-up</button>
                    </div>
                  </div>
                </div>

              </form>
            </div>

          </div>
        </div>
      </div>

      <div class="row">
        <div class="box box-default">
          <div class="box-header with-border">
            <h3 class="box-title">Ticket History</h3>
          </div>
          <div class="box-body">
            <?php if(!empty($history)){ ?>
              <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Date & Time</th>
                      <th>Action</th>
                      <th>Old Status</th>
                      <th>New Status</th>
                      <th>Remarks</th>
                      <th>User</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach($history as $h){ ?>
                      <tr>
                        <td><?php echo date('d-M-Y H:i:s', strtotime($h['created_at'])); ?></td>
                        <td><?php echo htmlspecialchars($h['action']); ?></td>
                        <td><?php echo htmlspecialchars($h['old_status'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($h['new_status'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($h['remarks']); ?></td>
                        <td><?php echo htmlspecialchars($h['user_name']); ?> (<?php echo htmlspecialchars($h['user_role']); ?>)</td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            <?php } else { ?>
              <div class="alert alert-info">No history recorded yet.</div>
            <?php } ?>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
