<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4">

  <section class="content-header">
    <h1><?php echo htmlspecialchars($info['page_heading'] ?? 'HR Management'); ?></h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?php echo base_url(); ?>manager/hr">HR</a></li>
      <li class="active"><?php echo htmlspecialchars(ucfirst($active_tab ?? 'overview')); ?></li>
    </ol>
  </section>

  <section class="content">

    <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'),'message'); ?>

    <style>
      .hr-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 18px 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
      }
      .hr-stat-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
      }
      .hr-stat-value {
        font-size: 26px;
        font-weight: 700;
        color: #0f172a;
        margin-top: 4px;
      }
      .hr-progress-track {
        background: #f1f5f9;
        border-radius: 999px;
        height: 6px;
        overflow: hidden;
        margin-top: 6px;
      }
      .hr-progress-fill {
        background: #0072f0;
        height: 100%;
        border-radius: 999px;
        transition: width 0.3s ease;
      }
      .hr-geo-box {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
      }
      .table-hr th {
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        font-weight: 600;
        border-bottom: 1px solid #e2e8f0 !important;
        text-transform: uppercase;
        letter-spacing: 0.4px;
      }
      .table-hr td {
        vertical-align: middle !important;
        font-size: 13px;
        color: #1e293b;
        border-top: 1px solid #f1f5f9 !important;
      }
    </style>

    <?php if (($active_tab ?? '') === 'overview'): ?>
      <div class="row">
        <div class="col-lg-3 col-sm-6">
          <div class="hr-stat-card">
            <div class="hr-stat-label">Total Employees</div>
            <div class="hr-stat-value"><?php echo (int)($total_employees ?? 0); ?></div>
          </div>
        </div>
        <div class="col-lg-3 col-sm-6">
          <div class="hr-stat-card">
            <div class="hr-stat-label">Today's Attendance</div>
            <div class="hr-stat-value"><?php echo (int)($today_attendance ?? 0); ?></div>
          </div>
        </div>
        <div class="col-lg-3 col-sm-6">
          <div class="hr-stat-card">
            <div class="hr-stat-label">Pending Leaves</div>
            <div class="hr-stat-value"><?php echo (int)($pending_leaves ?? 0); ?></div>
          </div>
        </div>
        <div class="col-lg-3 col-sm-6">
          <div class="hr-stat-card">
            <div class="hr-stat-label">Active Targets</div>
            <div class="hr-stat-value"><?php echo (int)($active_targets ?? 0); ?></div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-7">
          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">Recent Attendance Logs</h3>
              <div class="box-tools pull-right">
                <a href="<?php echo base_url(); ?>manager/hr/attendance" class="btn btn-default btn-xs">View All</a>
              </div>
            </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover table-hr">
                <thead>
                  <tr>
                    <th>Employee</th>
                    <th>Date</th>
                    <th>In</th>
                    <th>Out</th>
                    <th>Location</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($recent_attendance)): ?>
                    <?php foreach ($recent_attendance as $ra): ?>
                      <?php 
                        $inTime = ($ra['clock_in'] ?? $ra['in_time'] ?? '') ?: '-';
                        $outTime = ($ra['clock_out'] ?? $ra['out_time'] ?? '') ?: '-';
                        $empName = ($ra['employee_name'] ?? '') ?: 'Employee #' . ($ra['user_id'] ?? 0);
                        $attDate = $ra['attendance_date'] ?? '';
                        $lat = $ra['latitude'] ?? '';
                        $lng = $ra['longitude'] ?? '';
                      ?>
                      <tr>
                        <td><strong><?php echo htmlspecialchars($empName); ?></strong></td>
                        <td><?php echo htmlspecialchars($attDate); ?></td>
                        <td><?php echo htmlspecialchars($inTime); ?></td>
                        <td><?php echo htmlspecialchars($outTime); ?></td>
                        <td>
                          <?php if (!empty($lat) && !empty($lng)): ?>
                            <a href="https://maps.google.com/?q=<?php echo urlencode($lat.','.$lng); ?>" target="_blank" class="btn btn-default btn-xs"><i class="fa fa-map-marker text-primary"></i> Map</a>
                          <?php else: ?>
                            <span class="text-muted">-</span>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="5" class="text-center text-muted">No attendance records yet</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-md-5">
          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">Pending Leave Requests</h3>
              <div class="box-tools pull-right">
                <a href="<?php echo base_url(); ?>manager/hr/leaves" class="btn btn-default btn-xs">View All</a>
              </div>
            </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover table-hr">
                <thead>
                  <tr>
                    <th>Employee</th>
                    <th>Type</th>
                    <th>Dates</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($pending_leave_list)): ?>
                    <?php foreach ($pending_leave_list as $pl): ?>
                      <?php 
                        $lEmpName = ($pl['employee_name'] ?? '') ?: 'Employee #' . ($pl['user_id'] ?? 0);
                        $lType = $pl['leave_type'] ?? 'Leave';
                        $lDate = ($pl['start_date'] ?? $pl['from_date'] ?? '') ?: '-';
                        $lId = (int)($pl['id'] ?? 0);
                      ?>
                      <tr>
                        <td><strong><?php echo htmlspecialchars($lEmpName); ?></strong></td>
                        <td><?php echo htmlspecialchars($lType); ?></td>
                        <td><?php echo htmlspecialchars($lDate); ?></td>
                        <td>
                          <a href="<?php echo base_url(); ?>manager/hr/leave_action/<?php echo $lId; ?>/Approved" class="btn btn-success btn-xs">Approve</a>
                          <a href="<?php echo base_url(); ?>manager/hr/leave_action/<?php echo $lId; ?>/Rejected" class="btn btn-danger btn-xs">Reject</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="4" class="text-center text-muted">No pending leave requests</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    <?php elseif (($active_tab ?? '') === 'targets'): ?>
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Targets Assignment</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalAssignTarget">
              <i class="fa fa-plus"></i> Assign New Target
            </button>
          </div>
        </div>
        <div class="box-body table-responsive no-padding">
          <table class="table table-hover table-hr">
            <thead>
              <tr>
                <th>Employee</th>
                <th>Month</th>
                <th>Revenue Target</th>
                <th>Calls Target</th>
                <th>Leads Target</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($targets)): ?>
                <?php foreach ($targets as $t): ?>
                  <?php 
                    $tEmpName = ($t['employee_name'] ?? '') ?: 'User #' . ($t['user_id'] ?? 0);
                    $tEmpEmail = $t['employee_email'] ?? '';
                    $tMonth = $t['period_month'] ?? '';
                    $tActualRev = floatval($t['actual_revenue'] ?? $t['achieved_amount'] ?? 0);
                    $tTargetRev = floatval($t['target_revenue'] ?? $t['target_amount'] ?? 0);
                    $tRevPct = floatval($t['revenue_pct'] ?? (($tTargetRev > 0) ? round(($tActualRev / $tTargetRev) * 100, 1) : 0));
                    $tActualCalls = (int)($t['actual_calls'] ?? $t['achieved_calls'] ?? 0);
                    $tTargetCalls = (int)($t['target_calls'] ?? 0);
                    $tCallsPct = floatval($t['calls_pct'] ?? (($tTargetCalls > 0) ? round(($tActualCalls / $tTargetCalls) * 100, 1) : 0));
                    $tActualLeads = (int)($t['actual_leads'] ?? $t['achieved_leads'] ?? 0);
                    $tTargetLeads = (int)($t['target_leads'] ?? 0);
                    $tLeadsPct = floatval($t['leads_pct'] ?? (($tTargetLeads > 0) ? round(($tActualLeads / $tTargetLeads) * 100, 1) : 0));
                    $tStatus = $t['status'] ?? 'Active';
                    $tId = (int)($t['id'] ?? 0);
                  ?>
                  <tr>
                    <td>
                      <strong><?php echo htmlspecialchars($tEmpName); ?></strong>
                      <div class="text-muted" style="font-size:11px;"><?php echo htmlspecialchars($tEmpEmail); ?></div>
                    </td>
                    <td><?php echo htmlspecialchars($tMonth); ?></td>
                    <td>
                      <div>AED <?php echo number_format($tActualRev, 2); ?> / AED <?php echo number_format($tTargetRev, 2); ?></div>
                      <div class="hr-progress-track">
                        <div class="hr-progress-fill" style="width: <?php echo min(100, $tRevPct); ?>%;"></div>
                      </div>
                      <div class="text-muted" style="font-size:10px; margin-top:2px;"><?php echo $tRevPct; ?>% Achieved</div>
                    </td>
                    <td>
                      <?php echo $tActualCalls; ?> / <?php echo $tTargetCalls; ?>
                      <div class="text-muted" style="font-size:10px;"><?php echo $tCallsPct; ?>%</div>
                    </td>
                    <td>
                      <?php echo $tActualLeads; ?> / <?php echo $tTargetLeads; ?>
                      <div class="text-muted" style="font-size:10px;"><?php echo $tLeadsPct; ?>%</div>
                    </td>
                    <td>
                      <span class="label <?php echo ($tStatus === 'Active') ? 'label-success' : 'label-default'; ?>"><?php echo htmlspecialchars($tStatus); ?></span>
                    </td>
                    <td>
                      <a href="<?php echo base_url(); ?>manager/hr/delete_target/<?php echo $tId; ?>" class="btn btn-default btn-xs text-danger" onclick="return confirm('Are you sure you want to remove this target?');"><i class="fa fa-trash"></i></a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="7" class="text-center text-muted" style="padding:24px;">No targets assigned yet. Click "Assign New Target" above.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal fade" id="modalAssignTarget" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <form action="<?php echo base_url(); ?>manager/hr/add_target" method="POST">
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Assign Target to Employee</h4>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <label>Employee <span class="text-danger">*</span></label>
                  <select name="user_id" class="form-control select2" style="width:100%;" required>
                    <option value="">Select Employee</option>
                    <?php if (!empty($employees)): ?>
                      <?php foreach ($employees as $emp): ?>
                        <option value="<?php echo (int)($emp['id'] ?? 0); ?>"><?php echo htmlspecialchars($emp['name'] ?? ''); ?> (<?php echo htmlspecialchars($emp['email'] ?? ''); ?>)</option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                </div>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Target Period Month <span class="text-danger">*</span></label>
                      <input type="month" name="period_month" class="form-control" value="<?php echo date('Y-m'); ?>" required>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Revenue Target (AED) <span class="text-danger">*</span></label>
                      <input type="number" step="0.01" name="target_revenue" class="form-control" placeholder="e.g. 50000" required>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Calls Target</label>
                      <input type="number" name="target_calls" class="form-control" value="0">
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Leads Converted Target</label>
                      <input type="number" name="target_leads" class="form-control" value="0">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Meetings Target</label>
                      <input type="number" name="target_meetings" class="form-control" value="0">
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Notes</label>
                      <input type="text" name="notes" class="form-control" placeholder="Optional notes">
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Target</button>
              </div>
            </form>
          </div>
        </div>
      </div>

    <?php elseif (($active_tab ?? '') === 'lifecycle'): ?>
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Employee Lifecycle Directory</h3>
        </div>
        <div class="box-body table-responsive no-padding">
          <table class="table table-hover table-hr">
            <thead>
              <tr>
                <th>Employee</th>
                <th>Role / Designation</th>
                <th>Department</th>
                <th>Lifecycle Stage</th>
                <th>Join Date</th>
                <th>Contact</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php 
                $lcList = !empty($lifecycle_list) ? $lifecycle_list : (!empty($employees) ? $employees : array());
              ?>
              <?php if (!empty($lcList)): ?>
                <?php foreach ($lcList as $lc): ?>
                  <?php 
                    $lcUid = (int)($lc['id'] ?? $lc['user_id'] ?? 0);
                    $lcName = $lc['name'] ?? '';
                    $lcEmail = $lc['email'] ?? '';
                    $lcDesig = ($lc['designation'] ?? $lc['role_title'] ?? '') ?: 'Staff';
                    $lcDept = ($lc['department'] ?? '') ?: 'General';
                    $lcStage = ($lc['stage'] ?? '') ?: 'Confirmed';
                    $lcDate = ($lc['date_of_joining'] ?? $lc['join_date'] ?? '') ?: substr($lc['created'] ?? '', 0, 10);
                    $lcContact = ($lc['mobile'] ?? $lc['c_mobile'] ?? '') ?: '-';
                  ?>
                  <tr>
                    <td>
                      <strong><?php echo htmlspecialchars($lcName); ?></strong>
                      <div class="text-muted" style="font-size:11px;"><?php echo htmlspecialchars($lcEmail); ?></div>
                    </td>
                    <td><?php echo htmlspecialchars($lcDesig); ?></td>
                    <td><?php echo htmlspecialchars($lcDept); ?></td>
                    <td>
                      <span class="label label-primary"><?php echo htmlspecialchars($lcStage); ?></span>
                    </td>
                    <td><?php echo htmlspecialchars($lcDate); ?></td>
                    <td><?php echo htmlspecialchars($lcContact); ?></td>
                    <td>
                      <button type="button" class="btn btn-default btn-xs btn-update-stage" 
                        data-user-id="<?php echo $lcUid; ?>"
                        data-user-name="<?php echo htmlspecialchars($lcName); ?>"
                        data-stage="<?php echo htmlspecialchars($lcStage); ?>"
                        data-dept="<?php echo htmlspecialchars($lcDept); ?>"
                        data-desig="<?php echo htmlspecialchars($lcDesig); ?>">
                        <i class="fa fa-edit"></i> Edit
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="7" class="text-center text-muted">No employees found</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal fade" id="modalLifecycleStage" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <form action="<?php echo base_url(); ?>manager/hr/update_lifecycle" method="POST">
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
              <input type="hidden" name="user_id" id="lc_user_id">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Update Lifecycle: <span id="lc_user_name"></span></h4>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <label>Stage <span class="text-danger">*</span></label>
                  <select name="stage" id="lc_stage" class="form-control" required>
                    <option value="Onboarding">Onboarding</option>
                    <option value="Probation">Probation</option>
                    <option value="Confirmed">Confirmed</option>
                    <option value="Promotion">Promotion</option>
                    <option value="Transfer">Transfer</option>
                    <option value="Notice">Notice</option>
                    <option value="Relieved">Relieved</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Department</label>
                  <input type="text" name="department" id="lc_dept" class="form-control">
                </div>
                <div class="form-group">
                  <label>Designation</label>
                  <input type="text" name="designation" id="lc_desig" class="form-control">
                </div>
                <div class="form-group">
                  <label>Remarks</label>
                  <textarea name="remarks" class="form-control" rows="2"></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update</button>
              </div>
            </form>
          </div>
        </div>
      </div>

    <?php elseif (($active_tab ?? '') === 'attendance'): ?>
      <div class="hr-geo-box">
        <div>
          <div style="font-weight:600; font-size:14px; color:#0f172a;">Live Geolocation Punch</div>
          <div style="font-size:12px; color:#64748b; margin-top:2px;">
            Today: <strong><?php echo date('l, d M Y'); ?></strong> | Status: 
            <strong><?php echo (!empty($today_punch_in)) ? 'Clocked In at '.$today_punch_in : 'Not Clocked In'; ?></strong>
          </div>
        </div>
        <div>
          <?php if (empty($today_punch_in)): ?>
            <button type="button" id="btnGeoClockIn" class="btn btn-success btn-sm"><i class="fa fa-sign-in"></i> Clock In (with GPS)</button>
          <?php elseif (empty($today_punch_out)): ?>
            <button type="button" id="btnGeoClockOut" class="btn btn-danger btn-sm"><i class="fa fa-sign-out"></i> Clock Out (with GPS)</button>
          <?php else: ?>
            <span class="btn btn-default btn-sm disabled"><i class="fa fa-check"></i> Day Completed (<?php echo htmlspecialchars($today_punch_out); ?>)</span>
          <?php endif; ?>
        </div>
      </div>

      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Attendance Records</h3>
        </div>
        <div class="box-body table-responsive no-padding">
          <table class="table table-hover table-hr">
            <thead>
              <tr>
                <th>Employee</th>
                <th>Date</th>
                <th>In</th>
                <th>Out</th>
                <th>Hours</th>
                <th>Status</th>
                <th>GPS Geolocation</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($attendance_records)): ?>
                <?php foreach ($attendance_records as $ar): ?>
                  <?php 
                    $arEmpName = ($ar['employee_name'] ?? '') ?: 'User #' . ($ar['user_id'] ?? 0);
                    $arEmail = $ar['employee_email'] ?? '';
                    $arDate = $ar['attendance_date'] ?? '';
                    $arIn = ($ar['clock_in'] ?? $ar['in_time'] ?? '') ?: '-';
                    $arOut = ($ar['clock_out'] ?? $ar['out_time'] ?? '') ?: '-';
                    $arHours = ($ar['total_hours'] ?? '') ?: '-';
                    $arStatus = $ar['status'] ?? 'Present';
                    $arLat = $ar['latitude'] ?? '';
                    $arLng = $ar['longitude'] ?? '';
                    $arAcc = $ar['accuracy'] ?? 0;
                  ?>
                  <tr>
                    <td>
                      <strong><?php echo htmlspecialchars($arEmpName); ?></strong>
                      <div class="text-muted" style="font-size:11px;"><?php echo htmlspecialchars($arEmail); ?></div>
                    </td>
                    <td><?php echo htmlspecialchars($arDate); ?></td>
                    <td><?php echo htmlspecialchars($arIn); ?></td>
                    <td><?php echo htmlspecialchars($arOut); ?></td>
                    <td><?php echo htmlspecialchars($arHours); ?></td>
                    <td>
                      <span class="label <?php echo ($arStatus === 'Present') ? 'label-success' : 'label-warning'; ?>"><?php echo htmlspecialchars($arStatus); ?></span>
                    </td>
                    <td>
                      <?php if (!empty($arLat) && !empty($arLng)): ?>
                        <a href="https://maps.google.com/?q=<?php echo urlencode($arLat.','.$arLng); ?>" target="_blank" class="btn btn-default btn-xs">
                          <i class="fa fa-map-marker text-primary"></i> View Map (±<?php echo round($arAcc); ?>m)
                        </a>
                      <?php else: ?>
                        <span class="text-muted">-</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="7" class="text-center text-muted">No attendance logs found</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    <?php elseif (($active_tab ?? '') === 'shifts'): ?>
      <div class="row">
        <div class="col-md-5">
          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">Defined Shifts</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#modalAddShift"><i class="fa fa-plus"></i> Add Shift</button>
              </div>
            </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover table-hr">
                <thead>
                  <tr>
                    <th>Shift</th>
                    <th>Hours</th>
                    <th>Grace</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($shifts)): ?>
                    <?php foreach ($shifts as $s): ?>
                      <?php 
                        $sName = ($s['shift_name'] ?? $s['name'] ?? '') ?: 'Shift #' . ($s['id'] ?? 0);
                        $sStart = $s['start_time'] ?? '09:00';
                        $sEnd = $s['end_time'] ?? '18:00';
                        $sGrace = (int)($s['grace_period_mins'] ?? 15);
                      ?>
                      <tr>
                        <td><strong><?php echo htmlspecialchars($sName); ?></strong></td>
                        <td><?php echo htmlspecialchars($sStart); ?> - <?php echo htmlspecialchars($sEnd); ?></td>
                        <td><?php echo $sGrace; ?> mins</td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="3" class="text-center text-muted">No shifts defined</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-md-7">
          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">Shift Allocations</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#modalAssignShift"><i class="fa fa-user-plus"></i> Assign Shift</button>
              </div>
            </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover table-hr">
                <thead>
                  <tr>
                    <th>Employee</th>
                    <th>Shift</th>
                    <th>Timing</th>
                    <th>From</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($assignments)): ?>
                    <?php foreach ($assignments as $a): ?>
                      <?php 
                        $aEmpName = ($a['employee_name'] ?? '') ?: 'Employee #' . ($a['user_id'] ?? 0);
                        $aShiftName = ($a['shift_name'] ?? '') ?: 'Standard Shift';
                        $aStart = $a['start_time'] ?? '09:00';
                        $aEnd = $a['end_time'] ?? '18:00';
                        $aFrom = $a['effective_from'] ?? '-';
                      ?>
                      <tr>
                        <td><strong><?php echo htmlspecialchars($aEmpName); ?></strong></td>
                        <td><?php echo htmlspecialchars($aShiftName); ?></td>
                        <td><?php echo htmlspecialchars($aStart); ?> - <?php echo htmlspecialchars($aEnd); ?></td>
                        <td><?php echo htmlspecialchars($aFrom); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="4" class="text-center text-muted">No shift allocations yet</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="modalAddShift" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <form action="<?php echo base_url(); ?>manager/hr/add_shift" method="POST">
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Create Shift</h4>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <label>Shift Name <span class="text-danger">*</span></label>
                  <input type="text" name="shift_name" class="form-control" placeholder="e.g. Morning Regular" required>
                </div>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Start Time <span class="text-danger">*</span></label>
                      <input type="time" name="start_time" class="form-control" value="09:00" required>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>End Time <span class="text-danger">*</span></label>
                      <input type="time" name="end_time" class="form-control" value="18:00" required>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label>Grace Period (Minutes)</label>
                  <input type="number" name="grace_period_mins" class="form-control" value="15">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Shift</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="modal fade" id="modalAssignShift" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <form action="<?php echo base_url(); ?>manager/hr/assign_shift" method="POST">
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Assign Shift to Employee</h4>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <label>Employee <span class="text-danger">*</span></label>
                  <select name="user_id" class="form-control select2" style="width:100%;" required>
                    <option value="">Select Employee</option>
                    <?php if (!empty($employees)): ?>
                      <?php foreach ($employees as $emp): ?>
                        <option value="<?php echo (int)($emp['id'] ?? 0); ?>"><?php echo htmlspecialchars($emp['name'] ?? ''); ?></option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label>Shift <span class="text-danger">*</span></label>
                  <select name="shift_id" class="form-control" required>
                    <option value="">Select Shift</option>
                    <?php if (!empty($shifts)): ?>
                      <?php foreach ($shifts as $s): ?>
                        <option value="<?php echo (int)($s['id'] ?? 0); ?>"><?php echo htmlspecialchars($s['shift_name'] ?? $s['name'] ?? ''); ?> (<?php echo htmlspecialchars($s['start_time'] ?? ''); ?> - <?php echo htmlspecialchars($s['end_time'] ?? ''); ?>)</option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label>Effective From <span class="text-danger">*</span></label>
                  <input type="date" name="effective_from" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Assign</button>
              </div>
            </form>
          </div>
        </div>
      </div>

    <?php elseif (($active_tab ?? '') === 'leaves'): ?>
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Leave Management</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalApplyLeave"><i class="fa fa-plus"></i> Apply Leave</button>
          </div>
        </div>
        <div class="box-body table-responsive no-padding">
          <table class="table table-hover table-hr">
            <thead>
              <tr>
                <th>Employee</th>
                <th>Type</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Days</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($leaves)): ?>
                <?php foreach ($leaves as $l): ?>
                  <?php 
                    $lEmp = ($l['employee_name'] ?? '') ?: 'User #' . ($l['user_id'] ?? 0);
                    $lType = $l['leave_type'] ?? 'Leave';
                    $lFrom = ($l['from_date'] ?? $l['start_date'] ?? '') ?: '-';
                    $lTo = ($l['to_date'] ?? $l['end_date'] ?? '') ?: '-';
                    $lDays = (int)($l['total_days'] ?? 1);
                    $lReason = $l['reason'] ?? '-';
                    $lStatus = $l['status'] ?? 'Pending';
                    $lId = (int)($l['id'] ?? 0);
                  ?>
                  <tr>
                    <td><strong><?php echo htmlspecialchars($lEmp); ?></strong></td>
                    <td><?php echo htmlspecialchars($lType); ?></td>
                    <td><?php echo htmlspecialchars($lFrom); ?></td>
                    <td><?php echo htmlspecialchars($lTo); ?></td>
                    <td><?php echo $lDays; ?></td>
                    <td><?php echo htmlspecialchars($lReason); ?></td>
                    <td>
                      <span class="label <?php echo ($lStatus === 'Approved') ? 'label-success' : (($lStatus === 'Rejected') ? 'label-danger' : 'label-warning'); ?>"><?php echo htmlspecialchars($lStatus); ?></span>
                    </td>
                    <td>
                      <?php if ($lStatus === 'Pending'): ?>
                        <a href="<?php echo base_url(); ?>manager/hr/leave_action/<?php echo $lId; ?>/Approved" class="btn btn-success btn-xs">Approve</a>
                        <a href="<?php echo base_url(); ?>manager/hr/leave_action/<?php echo $lId; ?>/Rejected" class="btn btn-danger btn-xs">Reject</a>
                      <?php else: ?>
                        <span class="text-muted">-</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="8" class="text-center text-muted">No leave applications recorded</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal fade" id="modalApplyLeave" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <form action="<?php echo base_url(); ?>manager/hr/apply_leave" method="POST">
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Apply for Leave</h4>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <label>Employee <span class="text-danger">*</span></label>
                  <select name="user_id" class="form-control select2" style="width:100%;" required>
                    <option value="">Select Employee</option>
                    <?php if (!empty($employees)): ?>
                      <?php foreach ($employees as $emp): ?>
                        <option value="<?php echo (int)($emp['id'] ?? 0); ?>"><?php echo htmlspecialchars($emp['name'] ?? ''); ?></option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label>Leave Type <span class="text-danger">*</span></label>
                  <select name="leave_type" class="form-control" required>
                    <option value="Annual Leave">Annual Leave</option>
                    <option value="Sick Leave">Sick Leave</option>
                    <option value="Casual Leave">Casual Leave</option>
                    <option value="Maternity Leave">Maternity Leave</option>
                    <option value="Emergency Leave">Emergency Leave</option>
                    <option value="Unpaid Leave">Unpaid Leave</option>
                  </select>
                </div>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Start Date <span class="text-danger">*</span></label>
                      <input type="date" name="from_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>End Date <span class="text-danger">*</span></label>
                      <input type="date" name="to_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label>Reason</label>
                  <textarea name="reason" class="form-control" rows="2"></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit Application</button>
              </div>
            </form>
          </div>
        </div>
      </div>

    <?php elseif (($active_tab ?? '') === 'payroll'): ?>
      <?php $curMonthStr = $selected_month ?? $filter_month ?? date('Y-m'); ?>
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Payroll Management (<?php echo htmlspecialchars($curMonthStr); ?>)</h3>
          <div class="box-tools pull-right">
            <form action="<?php echo base_url(); ?>manager/hr/generate_payroll" method="POST" style="display:inline-block;">
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
              <input type="hidden" name="month_year" value="<?php echo htmlspecialchars($curMonthStr); ?>">
              <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i> Generate / Recalculate Month</button>
            </form>
          </div>
        </div>
        <div class="box-body table-responsive no-padding">
          <table class="table table-hover table-hr">
            <thead>
              <tr>
                <th>Employee</th>
                <th>Month</th>
                <th>Basic (AED)</th>
                <th>Allowances (AED)</th>
                <th>Gross (AED)</th>
                <th>Deductions (AED)</th>
                <th>Net Pay (AED)</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($payroll_records)): ?>
                <?php foreach ($payroll_records as $p): ?>
                  <?php 
                    $pEmpName = ($p['employee_name'] ?? '') ?: 'User #' . ($p['user_id'] ?? 0);
                    $pEmail = $p['employee_email'] ?? '';
                    $pMonth = $p['month_year'] ?? $p['period_month'] ?? '';
                    $pBasic = floatval($p['basic_salary'] ?? 0);
                    $pAllow = floatval($p['allowances'] ?? (floatval($p['housing_allowance'] ?? 0) + floatval($p['transport_allowance'] ?? 0) + floatval($p['other_allowance'] ?? 0)));
                    $pGross = floatval($p['gross_salary'] ?? ($pBasic + $pAllow));
                    $pDeduct = floatval($p['deductions'] ?? (floatval($p['tax_deduction'] ?? 0) + floatval($p['other_deductions'] ?? 0)));
                    $pNet = floatval($p['net_salary'] ?? ($pGross - $pDeduct));
                    $pStatus = $p['status'] ?? 'Pending';
                    $pId = (int)($p['id'] ?? 0);
                  ?>
                  <tr>
                    <td>
                      <strong><?php echo htmlspecialchars($pEmpName); ?></strong>
                      <div class="text-muted" style="font-size:11px;"><?php echo htmlspecialchars($pEmail); ?></div>
                    </td>
                    <td><?php echo htmlspecialchars($pMonth); ?></td>
                    <td><?php echo number_format($pBasic, 2); ?></td>
                    <td><?php echo number_format($pAllow, 2); ?></td>
                    <td><strong><?php echo number_format($pGross, 2); ?></strong></td>
                    <td class="text-danger"><?php echo number_format($pDeduct, 2); ?></td>
                    <td><strong class="text-success">AED <?php echo number_format($pNet, 2); ?></strong></td>
                    <td>
                      <span class="label <?php echo ($pStatus === 'Paid') ? 'label-success' : 'label-warning'; ?>"><?php echo htmlspecialchars($pStatus); ?></span>
                    </td>
                    <td>
                      <?php if ($pStatus !== 'Paid'): ?>
                        <a href="<?php echo base_url(); ?>manager/hr/payroll_status/<?php echo $pId; ?>/Paid" class="btn btn-success btn-xs"><i class="fa fa-check"></i> Mark Paid</a>
                      <?php else: ?>
                        <span class="text-muted"><i class="fa fa-check-circle text-success"></i> Disbursed</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="9" class="text-center text-muted" style="padding:24px;">No payroll records for this month. Click "Generate / Recalculate Month" above.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    <?php elseif (($active_tab ?? '') === 'taxation'): ?>
      <?php 
        $pStats = $payroll_stats ?? array();
        $totRecs = (int)($pStats['total_records'] ?? 0);
        $totGross = floatval($pStats['total_gross'] ?? 0);
        $totDeduct = floatval($pStats['total_deductions'] ?? 0);
        $totNet = floatval($pStats['total_net'] ?? 0);
      ?>
      <div class="row">
        <div class="col-md-6">
          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">Universal Gratuity & Severance Calculator</h3>
            </div>
            <div class="box-body">
              <div class="form-group">
                <label>Calculation Preset / Formula Standard</label>
                <select id="calc_formula_preset" class="form-control">
                  <option value="universal" selected>Universal Standard (Custom Days / Year)</option>
                  <option value="india">India (Payment of Gratuity Act, 1972: 15/26 days/yr)</option>
                  <option value="uae">UAE Labour Law (Decree Law No. 33: 21d first 5y, 30d after)</option>
                  <option value="custom">Custom Multiplier</option>
                </select>
              </div>
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>Currency</label>
                    <select id="calc_currency" class="form-control">
                      <option value="INR ₹">INR (₹)</option>
                      <option value="USD $">USD ($)</option>
                      <option value="AED" selected>AED</option>
                      <option value="EUR €">EUR (€)</option>
                      <option value="GBP £">GBP (£)</option>
                      <option value="SAR">SAR</option>
                      <option value="SGD $">SGD ($)</option>
                      <option value="CAD $">CAD ($)</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>Entitlement Days / Year</label>
                    <input type="number" id="calc_days_per_year" class="form-control" value="21">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>Monthly Basic Salary</label>
                    <input type="number" id="calc_basic_salary" class="form-control" value="10000">
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>Completed Years of Service</label>
                    <input type="number" step="0.1" id="calc_service_years" class="form-control" value="3.5">
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label>Reason for Separation</label>
                <select id="calc_separation_reason" class="form-control">
                  <option value="termination">Termination by Employer</option>
                  <option value="resignation">Resignation by Employee</option>
                  <option value="retirement">Retirement / Superannuation</option>
                  <option value="contract_end">Contract Completion</option>
                </select>
              </div>
              <button type="button" id="btnCalculateGratuity" class="btn btn-primary btn-block"><i class="fa fa-calculator"></i> Calculate Gratuity & Severance Benefit</button>

              <div id="gratuityResultBox" style="margin-top:20px; padding:16px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; display:none;">
                <div style="font-size:12px; color:#64748b; text-transform:uppercase;">Estimated Gratuity Payable</div>
                <div id="gratuityResultValue" style="font-size:24px; font-weight:700; color:#0072f0; margin-top:4px;">0.00</div>
                <div id="gratuityResultFormula" style="font-size:11px; color:#475569; margin-top:4px;"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">Universal Taxation & Payroll Overview</h3>
            </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover table-hr">
                <thead>
                  <tr>
                    <th>Metric</th>
                    <th>Count / Amount</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Total Processed Payroll Records</td>
                    <td><strong><?php echo $totRecs; ?></strong></td>
                  </tr>
                  <tr>
                    <td>Total Gross Payroll (All-Time)</td>
                    <td><strong><?php echo number_format($totGross, 2); ?></strong></td>
                  </tr>
                  <tr>
                    <td>Total Statutory / Policy Deductions</td>
                    <td class="text-danger"><strong><?php echo number_format($totDeduct, 2); ?></strong></td>
                  </tr>
                  <tr>
                    <td>Total Net Disbursed</td>
                    <td class="text-success"><strong><?php echo number_format($totNet, 2); ?></strong></td>
                  </tr>
                  <tr>
                    <td>Taxation & Compliance Framework</td>
                    <td><span class="label label-info">Universal Multi-Jurisdiction</span></td>
                  </tr>
                  <tr>
                    <td>Statutory Deductions & Slabs</td>
                    <td><span class="label label-success">Configurable per Territory</span></td>
                  </tr>
                  <tr>
                    <td>Severance & Gratuity Model</td>
                    <td><span class="label label-primary">Accrual Enabled</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    <?php elseif (($active_tab ?? '') === 'appraisals'): ?>
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Performance Appraisals</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalAddAppraisal"><i class="fa fa-plus"></i> New Appraisal</button>
          </div>
        </div>
        <div class="box-body table-responsive no-padding">
          <table class="table table-hover table-hr">
            <thead>
              <tr>
                <th>Employee</th>
                <th>Cycle</th>
                <th>Score</th>
                <th>Rating</th>
                <th>Reviewer</th>
                <th>Date</th>
                <th>Remarks</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($appraisals)): ?>
                <?php foreach ($appraisals as $ap): ?>
                  <?php 
                    $apEmp = ($ap['employee_name'] ?? '') ?: 'User #' . ($ap['user_id'] ?? 0);
                    $apCycle = ($ap['cycle_name'] ?? $ap['appraisal_cycle'] ?? '') ?: 'Review';
                    $apScore = floatval($ap['kpi_score'] ?? $ap['score'] ?? 0);
                    $apRating = (int)($ap['rating'] ?? 0);
                    $apRev = ($ap['reviewer_name'] ?? '') ?: 'Reviewer #' . ($ap['reviewer_id'] ?? 0);
                    $apDate = ($ap['review_date'] ?? substr($ap['created_at'] ?? '', 0, 10)) ?: '-';
                    $apFeedback = ($ap['feedback'] ?? '') ?: '-';
                  ?>
                  <tr>
                    <td><strong><?php echo htmlspecialchars($apEmp); ?></strong></td>
                    <td><?php echo htmlspecialchars($apCycle); ?></td>
                    <td><strong><?php echo number_format($apScore, 1); ?>/100</strong></td>
                    <td>
                      <?php for($i=1; $i<=5; $i++): ?>
                        <i class="fa <?php echo ($i <= $apRating) ? 'fa-star text-warning' : 'fa-star-o text-muted'; ?>"></i>
                      <?php endfor; ?>
                    </td>
                    <td><?php echo htmlspecialchars($apRev); ?></td>
                    <td><?php echo htmlspecialchars($apDate); ?></td>
                    <td><?php echo htmlspecialchars($apFeedback); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="7" class="text-center text-muted">No appraisals recorded yet</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal fade" id="modalAddAppraisal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <form action="<?php echo base_url(); ?>manager/hr/add_appraisal" method="POST">
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Record Performance Appraisal</h4>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <label>Employee <span class="text-danger">*</span></label>
                  <select name="user_id" class="form-control select2" style="width:100%;" required>
                    <option value="">Select Employee</option>
                    <?php if (!empty($employees)): ?>
                      <?php foreach ($employees as $emp): ?>
                        <option value="<?php echo (int)($emp['id'] ?? 0); ?>"><?php echo htmlspecialchars($emp['name'] ?? ''); ?></option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                </div>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Cycle / Period <span class="text-danger">*</span></label>
                      <input type="text" name="cycle_name" class="form-control" value="Q4 <?php echo date('Y'); ?>" required>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>KPI Score (0-100) <span class="text-danger">*</span></label>
                      <input type="number" step="0.1" name="kpi_score" class="form-control" value="85" required>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label>Overall Rating (1 to 5 Stars)</label>
                  <select name="rating" class="form-control">
                    <option value="5">5 - Outstanding</option>
                    <option value="4" selected>4 - Exceeds Expectations</option>
                    <option value="3">3 - Meets Expectations</option>
                    <option value="2">2 - Needs Improvement</option>
                    <option value="1">1 - Unsatisfactory</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Review Feedback</label>
                  <textarea name="feedback" class="form-control" rows="3"></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Appraisal</button>
              </div>
            </form>
          </div>
        </div>
      </div>

    <?php elseif (($active_tab ?? '') === 'documents'): ?>
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Document Vault</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalAddDocument"><i class="fa fa-upload"></i> Upload Document</button>
          </div>
        </div>
        <div class="box-body table-responsive no-padding">
          <table class="table table-hover table-hr">
            <thead>
              <tr>
                <th>Employee</th>
                <th>Type</th>
                <th>Title / Number</th>
                <th>Issue Date</th>
                <th>Expiry Date</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($documents)): ?>
                <?php foreach ($documents as $doc): ?>
                  <?php 
                    $docEmp = ($doc['employee_name'] ?? '') ?: 'User #' . ($doc['user_id'] ?? 0);
                    $docType = ($doc['document_type'] ?? $doc['doc_type'] ?? '') ?: 'Document';
                    $docTitle = ($doc['title'] ?? '') ?: $docType;
                    $docNum = ($doc['document_number'] ?? $doc['doc_number'] ?? '') ?: '-';
                    $docIssue = ($doc['issue_date'] ?? '') ?: '-';
                    $docExpiry = ($doc['expiry_date'] ?? '') ?: 'No Expiry';

                    $today_ts = strtotime(date('Y-m-d'));
                    $exp_ts = !empty($doc['expiry_date']) ? strtotime($doc['expiry_date']) : 0;
                    $diff_days = $exp_ts ? floor(($exp_ts - $today_ts) / 86400) : 9999;
                    if ($exp_ts && $diff_days < 0) {
                      $status_badge = '<span class="label label-danger">Expired</span>';
                    } elseif ($exp_ts && $diff_days <= 30) {
                      $status_badge = '<span class="label label-warning">Expiring in '.$diff_days.'d</span>';
                    } else {
                      $status_badge = '<span class="label label-success">Valid</span>';
                    }
                  ?>
                  <tr>
                    <td><strong><?php echo htmlspecialchars($docEmp); ?></strong></td>
                    <td><?php echo htmlspecialchars($docType); ?></td>
                    <td>
                      <div><strong><?php echo htmlspecialchars($docTitle); ?></strong></div>
                      <div class="text-muted" style="font-size:11px;"><?php echo htmlspecialchars($docNum); ?></div>
                    </td>
                    <td><?php echo htmlspecialchars($docIssue); ?></td>
                    <td><?php echo htmlspecialchars($docExpiry); ?></td>
                    <td><?php echo $status_badge; ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="6" class="text-center text-muted">No documents stored in vault</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal fade" id="modalAddDocument" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <form action="<?php echo base_url(); ?>manager/hr/add_document" method="POST">
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Employee Document</h4>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <label>Employee <span class="text-danger">*</span></label>
                  <select name="user_id" class="form-control select2" style="width:100%;" required>
                    <option value="">Select Employee</option>
                    <?php if (!empty($employees)): ?>
                      <?php foreach ($employees as $emp): ?>
                        <option value="<?php echo (int)($emp['id'] ?? 0); ?>"><?php echo htmlspecialchars($emp['name'] ?? ''); ?></option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label>Document Type <span class="text-danger">*</span></label>
                  <select name="doc_type" class="form-control" required>
                    <option value="National ID">National ID (Aadhaar / Emirates ID / SSN / PAN)</option>
                    <option value="Passport">Passport</option>
                    <option value="Work Permit / Visa">Work Permit / Visa / Residency</option>
                    <option value="Employment Contract">Employment / Labour Contract</option>
                    <option value="Tax Document">Tax Document / Form 16 / PAN</option>
                    <option value="Educational Certificate">Educational / Degree Certificate</option>
                    <option value="Bank / Payroll Document">Bank / Payroll Document</option>
                    <option value="Other">Other Document</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Document Title <span class="text-danger">*</span></label>
                  <input type="text" name="title" class="form-control" placeholder="e.g. Identification Card / Work Permit / Contract Copy" required>
                </div>
                <div class="form-group">
                  <label>Document Number</label>
                  <input type="text" name="doc_number" class="form-control" placeholder="e.g. ID or Registration Number">
                </div>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Issue Date</label>
                      <input type="date" name="issue_date" class="form-control">
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Expiry Date</label>
                      <input type="date" name="expiry_date" class="form-control">
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Document</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    <?php endif; ?>

  </section>
</div>

<script>
$(function() {
  $('.btn-update-stage').on('click', function() {
    var uid = $(this).data('user-id');
    var uname = $(this).data('user-name');
    var stg = $(this).data('stage');
    var dept = $(this).data('dept');
    var dsg = $(this).data('desig');

    $('#lc_user_id').val(uid);
    $('#lc_user_name').text(uname);
    $('#lc_stage').val(stg);
    $('#lc_dept').val(dept);
    $('#lc_desig').val(dsg);
    $('#modalLifecycleStage').modal('show');
  });

  function performGeoClock(type) {
    if (!navigator.geolocation) {
      alert('Geolocation is not supported by your browser.');
      return;
    }
    var $btn = (type === 'in') ? $('#btnGeoClockIn') : $('#btnGeoClockOut');
    var origText = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Getting GPS...');

    navigator.geolocation.getCurrentPosition(
      function(pos) {
        var lat = pos.coords.latitude;
        var lng = pos.coords.longitude;
        var acc = pos.coords.accuracy;

        $.ajax({
          url: '<?php echo base_url(); ?>manager/hr/ajax_clock',
          type: 'POST',
          dataType: 'json',
          data: {
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
            type: type,
            latitude: lat,
            longitude: lng,
            accuracy: acc
          },
          success: function(res) {
            if (res && res.status) {
              alert(res.message);
              window.location.reload();
            } else {
              alert((res && res.message) ? res.message : 'Error clocking in/out.');
              $btn.prop('disabled', false).html(origText);
            }
          },
          error: function() {
            alert('Server error occurred while registering punch.');
            $btn.prop('disabled', false).html(origText);
          }
        });
      },
      function(err) {
        alert('Geolocation access error (' + err.code + '): ' + err.message);
        $btn.prop('disabled', false).html(origText);
      },
      { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
  }

  $('#btnGeoClockIn').on('click', function() {
    performGeoClock('in');
  });

  $('#btnGeoClockOut').on('click', function() {
    performGeoClock('out');
  });

  $('#calc_formula_preset').on('change', function() {
    var p = $(this).val();
    if (p === 'india') {
      $('#calc_currency').val('INR ₹');
      $('#calc_days_per_year').val(15);
    } else if (p === 'uae') {
      $('#calc_currency').val('AED');
      $('#calc_days_per_year').val(21);
    } else if (p === 'universal') {
      $('#calc_days_per_year').val(21);
    }
  });

  $('#btnCalculateGratuity').on('click', function() {
    var basic = parseFloat($('#calc_basic_salary').val()) || 0;
    var years = parseFloat($('#calc_service_years').val()) || 0;
    var reason = $('#calc_separation_reason').val();
    var preset = $('#calc_formula_preset').val();
    var cur = $('#calc_currency').val() || 'AED';
    var daysPerYear = parseFloat($('#calc_days_per_year').val()) || 15;

    var totalGratuity = 0;
    var formulaText = '';

    if (preset === 'india') {
      if (years < 5 && reason !== 'death_disability') {
        formulaText = 'In India (Payment of Gratuity Act, 1972), minimum 5 years continuous service is standard for gratuity entitlement.';
      }
      totalGratuity = (15 * basic * years) / 26;
      if (totalGratuity > 2000000) {
        totalGratuity = 2000000;
        formulaText = 'Statutory ceiling cap of ₹2,000,000 applied under Indian Payment of Gratuity Act.';
      } else if (!formulaText) {
        formulaText = 'Calculated using Indian Gratuity Formula: (15 × Basic × Years) ÷ 26.';
      }
    } else if (preset === 'uae') {
      if (years < 1) {
        totalGratuity = 0;
        formulaText = 'Less than 1 year continuous service: No gratuity entitlement under UAE Labour Law.';
      } else {
        var dailyWage = basic / 30;
        if (years <= 5) {
          totalGratuity = (21 * dailyWage) * years;
        } else {
          totalGratuity = (21 * dailyWage * 5) + (30 * dailyWage * (years - 5));
        }
        var maxGratuity = basic * 24;
        if (totalGratuity > maxGratuity) totalGratuity = maxGratuity;
        if (reason === 'resignation') {
          if (years >= 1 && years < 3) totalGratuity = totalGratuity * (1 / 3);
          else if (years >= 3 && years < 5) totalGratuity = totalGratuity * (2 / 3);
        }
        formulaText = 'Calculated per UAE Labour Law (Decree Law No. 33) based on ' + years + ' years at ' + cur + ' ' + basic.toFixed(2) + ' basic salary.';
      }
    } else {
      var dailyWage = basic / 30;
      totalGratuity = (daysPerYear * dailyWage) * years;
      formulaText = 'Calculated using Universal Formula: (' + daysPerYear + ' days wage/year × ' + years + ' years). Daily wage = ' + cur + ' ' + dailyWage.toFixed(2) + '.';
    }

    var formatted = cur + ' ' + totalGratuity.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    $('#gratuityResultValue').text(formatted);
    $('#gratuityResultFormula').text(formulaText);
    $('#gratuityResultBox').slideDown();
  });
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
