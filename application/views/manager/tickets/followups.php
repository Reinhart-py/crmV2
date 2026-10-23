<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">
  <section class="content-header">
    <h1> <?php echo $info['page_heading']; ?> </h1>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box p-4">
          <div class="x_row">
            <div class="x_col">
              <form class="form-inline head_filter form-inline-mob" method="get" action="<?php echo base_url(); ?>manager/tickets/followups">
                
                <div class="form-group">
                  <div class="input-daterange input-group" id="datepicker" style="width: 100%;">
                    <input type="text" class="input-md form-control datepicker" name="d1" placeholder="Create From" value="<?php if(isset($_GET['d1'])&&(!empty($_GET['d1']))){ echo htmlspecialchars($_GET['d1']);}?>" autocomplete="off"/>
                    <span class="input-group-addon">To</span>
                    <input type="text" class="input-md form-control datepicker" name="d2" placeholder="Create To" value="<?php if(isset($_GET['d2'])&&(!empty($_GET['d2']))){ echo htmlspecialchars($_GET['d2']);}?>" autocomplete="off"/>
                  </div>
                </div>

                <div class="form-group">
                  <select class="form-control select2" name="client_type" style="min-width: 130px;">
                    <option value="">Client Type</option>
                    <option value="Onboarded" <?php if(isset($_GET['client_type']) && $_GET['client_type']=='Onboarded'){ echo 'selected'; } ?>>Onboarded</option>
                    <option value="Non-Onboarded" <?php if(isset($_GET['client_type']) && $_GET['client_type']=='Non-Onboarded'){ echo 'selected'; } ?>>Non-Onboarded</option>
                  </select>
                </div>

                <div class="form-group">
                  <select class="form-control select2" name="company" style="min-width: 150px;">
                    <option value="">Select Company</option>
                    <?php if(isset($companies_list) && !empty($companies_list)){
                      foreach($companies_list as $comp){ 
                        $cname = is_array($comp) ? ($comp['name'] ?? $comp['company'] ?? '') : $comp;
                        if(empty($cname)) continue;
                      ?>
                        <option value="<?php echo htmlspecialchars($cname); ?>" <?php if(isset($_GET['company']) && $_GET['company']==$cname){ echo 'selected'; } ?>>
                          <?php echo htmlspecialchars($cname); ?>
                        </option>
                    <?php } } ?>
                  </select>
                </div>

                <div class="form-group">
                  <select class="form-control select2" name="department" style="min-width: 130px;">
                    <option value="">Department</option>
                    <?php 
                    $depts = array('Sales', 'Back Office', 'Operations', 'IT', 'HR', 'Support', 'Accounts');
                    foreach($depts as $d){ ?>
                      <option value="<?php echo $d; ?>" <?php if(isset($_GET['department']) && $_GET['department']==$d){ echo 'selected'; } ?>><?php echo $d; ?></option>
                    <?php } ?>
                  </select>
                </div>

                <div class="form-group">
                  <select class="form-control select2" name="ticket_type" style="min-width: 140px;">
                    <option value="">Ticket Type</option>
                    <?php 
                    $ttypes = array(
                      'Bug / Technical Issue',
                      'Feature Request',
                      'System Access / Permission',
                      'Billing / Invoice Inquiry',
                      'Client Query / Onboarding',
                      'Operational Support',
                      'Hardware / Equipment',
                      'General Inquiry',
                      'Escalation'
                    );
                    foreach($ttypes as $tt){ ?>
                      <option value="<?php echo $tt; ?>" <?php if(isset($_GET['ticket_type']) && $_GET['ticket_type']==$tt){ echo 'selected'; } ?>><?php echo $tt; ?></option>
                    <?php } ?>
                  </select>
                </div>

                <div class="form-group">
                  <select class="form-control select2" name="priority" style="min-width: 110px;">
                    <option value="">Priority</option>
                    <option value="Low" <?php if(isset($_GET['priority']) && $_GET['priority']=='Low'){ echo 'selected'; } ?>>Low</option>
                    <option value="Medium" <?php if(isset($_GET['priority']) && $_GET['priority']=='Medium'){ echo 'selected'; } ?>>Medium</option>
                    <option value="High" <?php if(isset($_GET['priority']) && $_GET['priority']=='High'){ echo 'selected'; } ?>>High</option>
                    <option value="Urgent" <?php if(isset($_GET['priority']) && $_GET['priority']=='Urgent'){ echo 'selected'; } ?>>Urgent</option>
                  </select>
                </div>

                <div class="form-group">
                  <select class="form-control select2" name="assigned_user" style="min-width: 140px;">
                    <option value="">Assigned User</option>
                    <?php if(isset($users_list) && !empty($users_list)){
                      foreach($users_list as $u){ ?>
                        <option value="<?php echo $u['id']; ?>" <?php if(isset($_GET['assigned_user']) && $_GET['assigned_user']==$u['id']){ echo 'selected'; } ?>>
                          <?php echo htmlspecialchars($u['name']); ?>
                        </option>
                    <?php } } ?>
                  </select>
                </div>

                <div class="form-group">
                  <select class="form-control select2" name="created_by" style="min-width: 140px;">
                    <option value="">Created By</option>
                    <?php if(isset($users_list) && !empty($users_list)){
                      foreach($users_list as $u){ ?>
                        <option value="<?php echo $u['id']; ?>" <?php if((isset($_GET['created_by']) && $_GET['created_by']==$u['id']) || (isset($_GET['raised_by']) && $_GET['raised_by']==$u['id'])){ echo 'selected'; } ?>>
                          <?php echo htmlspecialchars($u['name']); ?>
                        </option>
                    <?php } } ?>
                  </select>
                </div>

                <div class="form-group">
                  <select class="form-control select2" name="status" style="min-width: 140px;">
                    <option value="">All Open Statuses</option>
                    <option value="Open" <?php if(isset($_GET['status']) && $_GET['status']=='Open'){ echo 'selected'; } ?>>Open</option>
                    <option value="Assigned" <?php if(isset($_GET['status']) && $_GET['status']=='Assigned'){ echo 'selected'; } ?>>Assigned</option>
                    <option value="In Progress" <?php if(isset($_GET['status']) && $_GET['status']=='In Progress'){ echo 'selected'; } ?>>In Progress</option>
                    <option value="Pending" <?php if(isset($_GET['status']) && $_GET['status']=='Pending'){ echo 'selected'; } ?>>Pending</option>
                    <option value="On Hold" <?php if(isset($_GET['status']) && $_GET['status']=='On Hold'){ echo 'selected'; } ?>>On Hold</option>
                    <option value="Resolved" <?php if(isset($_GET['status']) && $_GET['status']=='Resolved'){ echo 'selected'; } ?>>Resolved (Ready for Review)</option>
                  </select>
                </div>

                <div class="form-group">
                  <input type="text" class="input-md form-control" name="search" placeholder="Search Tickets" value="<?php if(isset($_GET['search'])&&(!empty($_GET['search']))){ echo htmlspecialchars($_GET['search']);}?>" autocomplete="off" />
                </div>

                <div class="form-group">
                  <button type="submit" class="btn btn-success">Search</button>
                  <a href="<?php echo base_url(); ?>manager/tickets/followups" class="btn btn-danger"> Reset</a>
                </div>

              </form>
            </div>
            <div class="x_col-sm-auto text-center pt-4 pt-md-0">
              <div>
                <a href="<?php echo base_url(); ?>manager/tickets/add" class="btn btn-success"> Raise Ticket</a>
              </div>
            </div>
          </div>
        </div>

        <div class="box">
          <div class="box-body">
            <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
            <?php echo get_message($this->session->flashdata('message'),'message'); ?>

            <?php 
            $user_map = array();
            if(!empty($users_list)){
              foreach($users_list as $ul){
                $user_map[$ul['id']] = $ul['name'];
              }
            }
            $today_str = date('Y-m-d');
            ?>

            <?php if(isset($tickets) && !empty($tickets)) { ?>
            <div>
              <table id="example_lead" class="table table-hover table-bordered table-striped" style="width:100%">
                <thead>
                  <tr>
                    <th>Ticket #</th>
                    <th>Customer / Company</th>
                    <th>Client Type</th>
                    <th>Title</th>
                    <th>Department</th>
                    <th>Priority</th>
                    <th>Assigned To</th>
                    <th>Deadline</th>
                    <th>Date</th>
                    <th>Created By</th>
                    <th>Status</th>
                    <th width="150">Action</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                  </tr>
                </tfoot>
                <tbody>
                  <?php foreach($tickets as $t){ 
                    $assignees = array();
                    if(!empty($t['assigned_users'])){
                      $arr_ids = explode(',', $t['assigned_users']);
                      foreach($arr_ids as $aid){
                        $aid = trim($aid);
                        if(isset($user_map[$aid])){
                          $assignees[] = $user_map[$aid];
                        }
                      }
                    } elseif(!empty($t['assigned_to']) && isset($user_map[$t['assigned_to']])){
                      $assignees[] = $user_map[$t['assigned_to']];
                    }

                    $is_overdue = (!empty($t['deadline_date']) && $t['deadline_date'] < $today_str && !in_array(strtolower($t['status']), array('resolved', 'closed')));
                  ?>
                  <tr>
                    <td><?php echo htmlspecialchars($t['ticket_number']); ?></td>
                    <td><?php echo htmlspecialchars(!empty($t['company_name']) ? $t['company_name'] : (!empty($t['customer']) ? $t['customer'] : '-')); ?></td>
                    <td><?php echo htmlspecialchars(!empty($t['client_type']) ? $t['client_type'] : 'Onboarded'); ?></td>
                    <td><?php echo htmlspecialchars(!empty($t['ticket_title']) ? $t['ticket_title'] : $t['subject']); ?></td>
                    <td><?php echo htmlspecialchars(!empty($t['department']) ? $t['department'] : 'General'); ?></td>
                    <td><?php echo htmlspecialchars($t['priority']); ?></td>
                    <td><?php echo !empty($assignees) ? htmlspecialchars(implode(', ', $assignees)) : '-'; ?></td>
                    <td>
                      <?php if(!empty($t['deadline_date'])){ ?>
                        <span class="no_show"><?php echo date('Ymd', strtotime($t['deadline_date'])); ?></span>
                        <?php echo simple_date($t['deadline_date']); ?>
                        <?php if($is_overdue){ ?><span class="label label-danger" style="margin-left: 3px;">Overdue</span><?php } ?>
                      <?php } else { echo '-'; } ?>
                    </td>
                    <td>
                      <span class="no_show"><?php echo date('Ymd', strtotime(!empty($t['created_date']) ? $t['created_date'] : $t['created_at'])); ?></span>
                      <?php echo simple_date(!empty($t['created_date']) ? $t['created_date'] : $t['created_at']); ?>
                    </td>
                    <td><?php echo htmlspecialchars(!empty($t['creator_name']) ? $t['creator_name'] : (!empty($user_map[$t['raised_by']]) ? $user_map[$t['raised_by']] : '-')); ?></td>
                    <td>
                      <?php 
                        $s_cls = 'label-default';
                        if($t['status'] == 'Open') $s_cls = 'label-info';
                        elseif($t['status'] == 'In Progress') $s_cls = 'label-primary';
                        elseif($t['status'] == 'Pending' || $t['status'] == 'Assigned') $s_cls = 'label-warning';
                        elseif($t['status'] == 'Resolved') $s_cls = 'label-success';
                        elseif($t['status'] == 'On Hold') $s_cls = 'label-default';
                      ?>
                      <span class="label <?php echo $s_cls; ?>" style="font-size:11px; padding:3px 6px;"><?php echo htmlspecialchars($t['status']); ?></span>
                    </td>
                    <td>
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-warning">Action</button>
                        <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                          <span class="caret"></span>
                          <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                          <li> <a href="<?php echo base_url(); ?>manager/tickets/view/<?php echo $t['id']; ?>" data-toggle="tooltip" title="Preview">Preview</a> </li>
                          <li> <a href="<?php echo base_url(); ?>manager/tickets/edit/<?php echo $t['id']; ?>" data-toggle="tooltip" title="Edit">Edit</a> </li>
                          <?php if(!empty($can_manage_tickets) && $t['status'] !== 'Closed'){ ?>
                            <li> <a href="<?php echo base_url(); ?>manager/tickets/view/<?php echo $t['id']; ?>#review_close_section" data-toggle="tooltip" title="Close Ticket"><i class="fa fa-check-circle text-success"></i> Close Ticket</a> </li>
                          <?php } ?>
                        </ul>
                      </div>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
            <?php } else { ?>
              <div class="alert alert-info"> No Data Found </div>
            <?php } ?>
          </div>
        </div>

      </div>
    </div>
  </section>
</div>

<script type="text/javascript">
$(document).ready(function() {
  $('#example_lead').DataTable({
    "pageLength": 50,
    responsive: true,
    initComplete: function () {
      this.api().columns([2, 4, 5, 9, 10]).every(function () {
        var column = this;
        var select = $('<select><option value="">Select </option></select>')
          .appendTo($(column.footer()).empty())
          .on('change', function () {
            var val = $.fn.dataTable.util.escapeRegex($(this).val());
            column.search(val ? '^' + val + '$' : '', true, false).draw();
          });

        column.data().unique().sort().each(function (d, j) {
          select.append('<option value="' + d + '">' + d + '</option>');
        });
      });
    },
    columnDefs: [
      { targets: [7, 8, 11], className: "dt-nowrap" }
    ]
  });
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
