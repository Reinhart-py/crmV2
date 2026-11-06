<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper px-4 hrx-page">
  <?php
  // Tabs of the HR & Payroll area this page belongs to (People / Time & Leave / Payroll / Performance)
  $hr_tab_of = array('lifecycle' => array('hr_people', 'hr/lifecycle'), 'attendance' => array('hr_time', 'hr/attendance'), 'shifts' => array('hr_time', 'hr/shifts'),
    'leaves' => array('hr_time', 'hr/leaves'), 'payroll' => array('hr_pay', 'hr/payroll'), 'taxation' => array('hr_pay', 'hr/taxation'),
    'appraisals' => array('hr_perf', 'hr/appraisals'), 'targets' => array('hr_perf', 'hr/targets'));
  $hr_tab = isset($hr_tab_of[$active_tab ?? '']) ? $hr_tab_of[$active_tab] : null;
  $hr_bar = ($hr_tab && (!isset($is_hr) || !empty($is_hr))) ? crm_tabbar($hr_tab[0], $hr_tab[1]) : '';
  if ($hr_bar !== '') { ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
  <div class="sx" style="margin:0 -10px;"><?php echo $hr_bar; ?></div>
  <?php } ?>

  <section class="content-header">
    <h1><?php echo htmlspecialchars($info['page_heading'] ?? 'HR Management'); ?></h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
      <?php if (!isset($is_hr) || !empty($is_hr)) { ?>
      <li><a href="<?php echo base_url(); ?>manager/hr">HR</a></li>
      <?php } else { ?>
      <li><a href="<?php echo base_url(); ?>manager/profile">My Profile</a></li>
      <?php } ?>
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
        background: #1e3a8a;
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
      /* Whole HR area: same font and sizes as View Staff */
      .hrx-page, .hrx-page .btn, .hrx-page .form-control, .hrx-page table, .hrx-page .box-title, .hrx-page .modal-content{font-family:"Plus Jakarta Sans","Inter",-apple-system,"Segoe UI",sans-serif}
      .hrx-page .box{border-radius:10px;border:1px solid var(--crm-line,#e2e8f0);box-shadow:none}
      .hrx-page .box-header.with-border{display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;padding:10px 14px}
      .hrx-page .box-header.with-border::before, .hrx-page .box-header.with-border::after{display:none}
      .hrx-page .box-header .box-title{float:none;text-align:left;font-size:16px;font-weight:700;margin:0}
      .hrx-page .box-header .box-tools{position:static;float:none}
      .hrx-page .table-hr td{white-space:nowrap}
      .hrx-page .table-hr td.hrx-wrap{white-space:normal;min-width:180px;max-width:320px}
      .hrx-page .table-hr td strong, .hrx-page .table-hr td b{font-size:14.5px}
      .hrx-page .table-hr td .text-muted, .hrx-page .table-hr td small{font-size:12.5px !important}
      .hrx-page .table-hr tbody tr:nth-child(even) td{background:var(--crm-canvas,#fafbfd)}
      .hrx-page .hr-stat-card{border-radius:10px;padding:14px 16px}
      .hrx-page .hr-stat-label{font-size:12.5px !important;font-weight:600;letter-spacing:.04em}
      .hrx-page .hr-stat-value{font-size:24px !important;font-weight:700;letter-spacing:-.02em}
      @media (max-width:767px){
        .hrx-page.content-wrapper{padding-left:10px !important;padding-right:10px !important}
        .hrx-page .content-header h1{font-size:19px}
        .hrx-page .hr-stat-card{margin-bottom:10px;padding:10px 12px}
        .hrx-page .hr-stat-value{font-size:20px !important}
        .hrx-page .box-header.with-border .btn{width:100%}
      }
      /* HR pages in the same look as View Staff: one font, readable sizes, chips, phone friendly */
      .hrx, .hrx .form-control, .hrx .btn, .hrx table{font-family:"Plus Jakarta Sans","Inter",-apple-system,"Segoe UI",sans-serif}
      .hrx-chips{display:flex;flex-wrap:wrap;gap:6px;margin:0 0 10px}
      .hrx-chip{height:32px;padding:0 12px;border-radius:999px;border:1px solid var(--crm-line,#e2e8f0);background:var(--crm-surface,#fff);color:var(--crm-ink,#0f172a);font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:6px}
      .hrx-chip b{font-weight:700;font-size:12px;padding:1px 7px;border-radius:999px;background:var(--crm-canvas,#f1f5f9);color:var(--crm-muted,#475569)}
      .hrx-chip.on{border-color:var(--crm-ocean,#1e3a8a);background:var(--crm-ocean,#1e3a8a);color:#fff}
      .hrx-chip.on b{background:rgba(255,255,255,.2);color:#fff}
      .hrx-card{background:var(--crm-surface,#fff);border:1px solid var(--crm-line,#e2e8f0);border-radius:10px;margin-bottom:14px}
      .hrx-bar{display:flex;flex-wrap:wrap;gap:8px;align-items:center;padding:10px 12px;border-bottom:1px solid var(--crm-line-soft,#eef2f7)}
      .hrx-bar .form-control{height:34px;width:auto !important;flex:0 1 260px;min-width:180px;border-radius:6px;font-size:13.5px}
      .hrx-count{margin-left:auto;font-size:13px;color:var(--crm-muted,#64748b)}
      .hrx-note{margin:8px 12px 0;padding:7px 10px;border-radius:8px;background:var(--crm-canvas,#f8fafc);border:1px solid var(--crm-line-soft,#eef2f7);font-size:12.5px;color:var(--crm-muted,#64748b)}
      .hrx-table{margin:4px 0 0 !important}
      .hrx-table thead th, .table-hr thead th, .table-hr th{font-size:12px !important;font-weight:700;text-transform:uppercase;letter-spacing:.05em;padding:9px 10px !important;white-space:nowrap;text-align:left !important}
      .hrx-table tbody td, .table-hr tbody td, .table-hr td{font-size:14px !important;padding:9px 10px !important;vertical-align:middle !important;line-height:1.35;text-align:left !important}
      .hrx-table tbody tr:nth-child(even) td{background:var(--crm-canvas,#fafbfd)}
      .hrx-who{display:flex;flex-direction:column;min-width:0}
      .hrx-emp{font-family:ui-monospace,Consolas,monospace;font-size:12.5px;font-weight:600;color:var(--crm-ocean,#1e3a8a)}
      .hrx-nm{font-size:15px;font-weight:700;color:var(--crm-ink,#0f172a);text-transform:capitalize}
      a.hrx-nm:hover{color:var(--crm-ocean,#1e3a8a)}
      .hrx-sub{display:block;font-size:12.5px;color:var(--crm-muted,#64748b);margin-top:1px}
      .hrx-strong{font-weight:600}
      .hrx-miss{font-size:12.5px;color:#b45309}
      .hrx-st{display:inline-block;padding:2px 10px;border-radius:999px;font-size:12.5px;font-weight:600;white-space:nowrap}
      .st-b{color:#1d4ed8}.hrx-st.st-b{background:rgba(37,99,235,.12)}
      .st-a{color:#b45309}.hrx-st.st-a{background:rgba(245,158,11,.16)}
      .st-g{color:#15803d}.hrx-st.st-g{background:rgba(22,163,74,.12)}
      .st-p{color:#7c3aed}.hrx-st.st-p{background:rgba(124,58,237,.12)}
      .st-t{color:#0f766e}.hrx-st.st-t{background:rgba(13,148,136,.12)}
      .st-r{color:#b91c1c}.hrx-st.st-r{background:rgba(220,38,38,.12)}
      .st-s{color:#475569}.hrx-st.st-s{background:rgba(100,116,139,.15)}
      .hrx-chip.on[class*=st-]{color:#fff}
      .hrx-act{white-space:nowrap;text-align:right !important}
      .hrx-act .btn{height:30px;font-weight:600}
      .hrx-file{font-weight:600;white-space:nowrap}
      .hrx-bar .btn{height:34px;font-weight:600}
      .content-wrapper .content-header h1{font-family:"Plus Jakarta Sans","Inter",-apple-system,"Segoe UI",sans-serif;font-size:22px;font-weight:700;text-align:left}
      .content-wrapper .box .box-title{font-family:"Plus Jakarta Sans","Inter",-apple-system,"Segoe UI",sans-serif;font-size:16px;font-weight:700}
      .content-wrapper .table-hr .label{font-size:12px;padding:3px 9px;border-radius:999px;font-weight:600}
      @media (max-width:767px){
        .hrx-bar .form-control{min-width:0;flex:1 1 100%;width:100% !important}
        .hrx-count{margin-left:0}
        .hrx-chip{height:30px;font-size:12.5px;padding:0 10px}
        .hrx-table thead{display:none}
        .hrx-table, .hrx-table tbody, .hrx-table tr, .hrx-table td{display:block;width:100%}
        .hrx-table tbody tr{border-bottom:1px solid var(--crm-line,#e2e8f0);padding:8px 4px;display:grid !important;grid-template-columns:minmax(0,1fr) minmax(0,1fr);column-gap:6px}
        .hrx-table tbody td:first-child, .hrx-table tbody td.hrx-act, .hrx-table tbody td[colspan]{grid-column:1 / -1}
        .hrx-table tbody tr[style*="display: none"]{display:none !important}
        .hrx-table tbody tr:nth-child(even) td{background:transparent}
        .hrx-table tbody td{border:0 !important;padding:3px 8px !important;font-size:13.5px !important}
        .hrx-table tbody td:empty{display:none}
        .hrx .table-responsive{overflow:visible;border:0;margin:0}
        .hrx-table{width:100% !important;max-width:100%}
        .hrx-table tbody td{min-width:0 !important;max-width:100%;overflow-wrap:anywhere}
        .hrx-table tbody td[data-l]:not(:first-child):not(.hrx-act)::before{content:attr(data-l);display:block;white-space:normal;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--crm-muted,#94a3b8);margin-top:2px}
        .hrx-act{text-align:left !important}
        .content-wrapper .table-hr td, .content-wrapper .table-hr th{font-size:13px !important;padding:7px 8px !important}
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

      <div class="modal fade" id="modalAssignTarget" role="dialog">
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
                  <select name="user_id" id="target_user_id" class="form-control select2" style="width:100%;" required>
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

    <?php elseif (($active_tab ?? '') === 'lifecycle'):
      $lcList = !empty($lifecycle_list) ? $lifecycle_list : array();
      $lcH = function ($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };
      $lcD = function ($v) { return ($v && ($t = strtotime($v))) ? date('d-M-Y', $t) : ''; };
      $lcCls = array('Onboarding' => 'b', 'Probation' => 'a', 'Confirmed' => 'g', 'Promotion' => 'p', 'Transfer' => 't', 'Notice' => 'r', 'Exited' => 's');
      $lcAll = array_merge($lc_stages, array('Exited'));
    ?>
      <div class="hrx">
        <div class="hrx-chips">
          <button type="button" class="hrx-chip js-lc-stage<?php echo $lc_stage === '' ? ' on' : ''; ?>" data-v="">All <b><?php echo count($lcList); ?></b></button>
          <?php foreach ($lcAll as $st) { ?>
          <button type="button" class="hrx-chip js-lc-stage st-<?php echo $lcCls[$st]; ?><?php echo $lc_stage === $st ? ' on' : ''; ?>" data-v="<?php echo $lcH($st); ?>"><?php echo $lcH($st); ?> <b><?php echo (int)($lc_counts[$st] ?? 0); ?></b></button>
          <?php } ?>
        </div>
        <div class="hrx-card">
          <div class="hrx-bar">
            <select class="form-control js-lc-dept"><option value="">All departments</option><?php foreach ($lc_departments as $dp) { ?><option<?php echo $lc_dept === $dp ? ' selected' : ''; ?>><?php echo $lcH($dp); ?></option><?php } ?></select>
            <input type="search" class="form-control js-lc-q" placeholder="Search name, Emp No, designation">
            <span class="hrx-count js-lc-count"></span>
          </div>
          <div class="hrx-note"><i class="fa fa-info-circle"></i> Designation, department, joining date and contacts come from each person's staff profile - edit them there. To record someone leaving, use <b>Mark as left</b> on their profile.</div>
          <div class="table-responsive">
          <table class="table hrx-table">
            <thead><tr><th>Employee</th><th>Designation / Department</th><th>Stage</th><th>Joined</th><th>Contract / Probation</th><th></th></tr></thead>
            <tbody>
            <?php if (!empty($lcList)): foreach ($lcList as $lc):
              $uid = (int)$lc['id']; $st = $lc['stage']; $doj = $lcD($lc['date_of_joining']);
              $ten = ''; if ($lc['date_of_joining'] && ($t = strtotime($lc['date_of_joining']))) { $m = (int)floor((time() - $t) / (86400 * 30.44)); $ten = $m >= 12 ? floor($m / 12) . ' yr' . ($m % 12 ? ' ' . ($m % 12) . ' mo' : '') : max(0, $m) . ' mo'; } ?>
              <tr data-stage="<?php echo $lcH($st); ?>" data-dept="<?php echo $lcH($lc['department']); ?>" data-q="<?php echo $lcH(strtolower($lc['name'] . ' ' . sprintf('EMP-%05d', $uid) . ' ' . $lc['emp_id'] . ' ' . $lc['designation'])); ?>">
                <td><div class="hrx-who"><span class="hrx-emp"><?php echo sprintf('EMP-%05d', $uid); ?></span>
                  <?php if ($profile_base !== '') { ?><a class="hrx-nm" href="<?php echo base_url() . $profile_base . $uid; ?>"><?php echo $lcH($lc['name']); ?></a><?php } else { ?><span class="hrx-nm"><?php echo $lcH($lc['name']); ?></span><?php } ?>
                  <span class="hrx-sub"><?php echo $lcH($lc['email']); ?><?php echo $lc['contact'] !== '' ? ' &middot; ' . $lcH($lc['contact']) : ''; ?></span></div></td>
                <td><?php echo $lc['designation'] !== '' ? '<b class="hrx-strong">' . $lcH($lc['designation']) . '</b>' : '<span class="hrx-miss">No designation</span>'; ?><span class="hrx-sub"><?php echo $lc['department'] !== '' ? $lcH($lc['department']) : 'No department'; ?></span></td>
                <td><span class="hrx-st st-<?php echo $lcCls[$st] ?? 'g'; ?>"><?php echo $lcH($st); ?></span><?php if ($lc['left']) { ?><span class="hrx-sub"><?php echo $lcD($lc['left_on']) . ' &middot; ' . $lcH($lc['left_reason']); ?></span><?php } ?></td>
                <td data-order="<?php echo $lcH($lc['date_of_joining']); ?>"><?php echo $doj !== '' ? $doj . '<span class="hrx-sub">' . $ten . '</span>' : '<span class="hrx-miss">Not set</span>'; ?></td>
                <td><?php $pe = $lcD($lc['probation_end_date']); $cd = $lcD($lc['confirmation_date']); echo $lc['contract_type'] ? $lcH($lc['contract_type']) : '<span class="hrx-sub">Not set</span>'; if ($pe !== '') echo '<span class="hrx-sub">Probation till ' . $pe . '</span>'; if ($cd !== '') echo '<span class="hrx-sub">Confirmed ' . $cd . '</span>'; ?></td>
                <td class="hrx-act"><?php if (!$lc['left']) { ?><button type="button" class="btn btn-default btn-sm btn-update-stage" data-user-id="<?php echo $uid; ?>" data-user-name="<?php echo $lcH($lc['name']); ?>" data-stage="<?php echo $lcH($st); ?>" data-probation="<?php echo $lcH($lc['probation_end_date']); ?>" data-confirmed="<?php echo $lcH($lc['confirmation_date']); ?>" data-contract="<?php echo $lcH($lc['contract_type']); ?>" data-notes="<?php echo $lcH($lc['lifecycle_notes']); ?>" title="Update stage, probation and contract"><i class="fa fa-pencil"></i> Update</button><?php } elseif ($profile_base !== '') { ?><a class="btn btn-default btn-sm" href="<?php echo base_url() . $profile_base . $uid; ?>" title="Open the profile (Rejoin is there)"><i class="fa fa-user"></i> Profile</a><?php } ?></td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colspan="6" class="text-center text-muted">No employees found</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
          </div>
        </div>
      </div>

      <div class="modal fade" id="modalLifecycleStage" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document" style="max-width:520px">
          <div class="modal-content">
            <form action="<?php echo base_url(); ?>manager/hr/update_lifecycle" method="POST">
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
              <input type="hidden" name="user_id" id="lc_user_id">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Lifecycle: <span id="lc_user_name"></span></h4>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-sm-6 form-group"><label>Stage <span class="text-danger">*</span></label>
                    <select name="stage" id="lc_stage" class="form-control" required><?php foreach ($lc_stages as $st) { ?><option><?php echo $lcH($st); ?></option><?php } ?></select></div>
                  <div class="col-sm-6 form-group"><label>Contract type</label>
                    <select name="contract_type" id="lc_contract" class="form-control"><?php foreach (array('Permanent', 'Fixed term', 'Part time', 'Intern', 'Consultant') as $ct) { ?><option><?php echo $ct; ?></option><?php } ?></select></div>
                  <div class="col-sm-6 form-group"><label>Probation ends</label><input type="date" name="probation_end_date" id="lc_probation" class="form-control"></div>
                  <div class="col-sm-6 form-group"><label>Confirmed on</label><input type="date" name="confirmation_date" id="lc_confirmed" class="form-control"></div>
                </div>
                <div class="form-group"><label>Notes</label><textarea name="notes" id="lc_notes" class="form-control" rows="2" maxlength="1000"></textarea></div>
                <div class="hrx-note" style="margin:0"><i class="fa fa-info-circle"></i> Leaving is recorded with <b>Mark as left</b> on the staff profile; it shows here as <b>Exited</b>.</div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
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
      <?php
        $lv_is_hr = !empty($is_hr);
        $lv_mine = ($this->input->get('mine') === '1');
        $lv_status_f = (string)$this->input->get('status');
        $lv_ml = isset($my_leave) ? $my_leave : array('used_by_type' => array(), 'pending' => 0);
        $lv_used = 0;
        foreach ($lv_ml['used_by_type'] as $u) { $lv_used += (int)$u['days']; }
      ?>
      <div class="crm-summary">
        <div><span>Approved days this year</span><strong><?php echo $lv_used; ?></strong></div>
        <div><span>Awaiting approval</span><strong><?php echo (int)$lv_ml['pending']; ?></strong></div>
        <div><span>Most used type</span><strong><?php echo !empty($lv_ml['used_by_type']) ? htmlspecialchars($lv_ml['used_by_type'][0]['leave_type']) : '-'; ?></strong></div>
        <div><span>Approver</span><strong>HR / Administrator</strong></div>
      </div>

      <div class="box box-default crm-list-box">
        <div class="box-header with-border" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
          <h3 class="box-title"><?php echo $lv_is_hr && !$lv_mine ? 'All holiday requests' : 'My holiday requests'; ?></h3>
          <div style="display:flex; gap:6px; flex-wrap:wrap; align-items:center;">
            <?php if ($lv_is_hr) { ?>
              <a href="<?php echo base_url('manager/hr/leaves'); ?>" class="btn btn-sm <?php echo !$lv_mine ? 'btn-primary' : 'btn-default'; ?>">All employees</a>
              <a href="<?php echo base_url('manager/hr/leaves?mine=1'); ?>" class="btn btn-sm <?php echo $lv_mine ? 'btn-primary' : 'btn-default'; ?>">Only mine</a>
            <?php } ?>
            <form method="get" action="<?php echo base_url('manager/hr/leaves'); ?>" style="margin:0;">
              <?php if ($lv_mine) { ?><input type="hidden" name="mine" value="1"><?php } ?>
              <select name="status" class="form-control input-sm" onchange="this.form.submit()" aria-label="Filter by status">
                <option value="">All statuses</option>
                <?php foreach (array('Pending', 'Approved', 'Rejected', 'Cancelled') as $opt) { ?>
                  <option value="<?php echo $opt; ?>" <?php echo $lv_status_f === $opt ? 'selected' : ''; ?>><?php echo $opt; ?></option>
                <?php } ?>
              </select>
            </form>
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalApplyLeave"><i class="fa fa-plus"></i> New holiday request</button>
          </div>
        </div>
        <div class="box-body table-responsive">
          <table class="table table-hover table-hr crm-list-table" style="width:100%;">
            <thead>
              <tr>
                <?php if ($lv_is_hr) { ?><th>Employee</th><?php } ?>
                <th>Type</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Days</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Decided By</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($leaves)): ?>
                <?php foreach ($leaves as $l): ?>
                  <?php
                    $lEmp = ($l['employee_name'] ?? '') ?: 'User #' . ($l['user_id'] ?? 0);
                    $lType = $l['leave_type'] ?? 'Leave';
                    $lFrom = ($l['from_date'] ?? $l['start_date'] ?? '') ?: '';
                    $lTo = ($l['to_date'] ?? $l['end_date'] ?? '') ?: '';
                    $lDays = (int)($l['total_days'] ?? 1);
                    $lReason = ($l['reason'] ?? '') ?: '-';
                    $lStatus = $l['status'] ?? 'Pending';
                    $lId = (int)($l['id'] ?? 0);
                    $lOwn = ((int)($l['user_id'] ?? 0) === (int)($cur_uid ?? 0));
                    $lCls = ($lStatus === 'Approved') ? 'crm-status-ink' : (($lStatus === 'Rejected') ? 'crm-status-danger' : (($lStatus === 'Pending') ? 'crm-status-ocean' : ''));
                  ?>
                  <tr>
                    <?php if ($lv_is_hr) { ?><td><strong><?php echo htmlspecialchars($lEmp); ?></strong></td><?php } ?>
                    <td><?php echo htmlspecialchars($lType); ?></td>
                    <td data-order="<?php echo htmlspecialchars($lFrom); ?>"><?php echo $lFrom ? date('d M Y', strtotime($lFrom)) : '-'; ?></td>
                    <td data-order="<?php echo htmlspecialchars($lTo); ?>"><?php echo $lTo ? date('d M Y', strtotime($lTo)) : '-'; ?></td>
                    <td><?php echo $lDays; ?></td>
                    <td><?php echo htmlspecialchars($lReason); ?></td>
                    <td><span class="crm-status <?php echo $lCls; ?>"><?php echo htmlspecialchars($lStatus); ?></span></td>
                    <td><?php echo htmlspecialchars(($l['approver_name'] ?? '') ?: '-'); ?></td>
                    <td style="white-space:nowrap;">
                      <?php if ($lStatus === 'Pending'): ?>
                        <?php if ($lv_is_hr && (!$lOwn || !empty($is_admin))): ?>
                          <a href="<?php echo base_url(); ?>manager/hr/leave_action/<?php echo $lId; ?>/Approved" class="btn btn-success btn-xs">Approve</a>
                          <a href="<?php echo base_url(); ?>manager/hr/leave_action/<?php echo $lId; ?>/Rejected" class="btn btn-danger btn-xs">Reject</a>
                        <?php endif; ?>
                        <?php if ($lOwn): ?>
                          <form method="post" action="<?php echo base_url('manager/hr/cancel_leave/' . $lId); ?>" style="display:inline;" onsubmit="return confirm('Cancel this holiday request?');">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <button type="submit" class="btn btn-default btn-xs">Cancel</button>
                          </form>
                        <?php endif; ?>
                      <?php else: ?>
                        <span class="text-muted">-</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="<?php echo $lv_is_hr ? 9 : 8; ?>" class="text-center text-muted">No holiday requests yet.</td></tr>
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
                <h4 class="modal-title">New holiday request</h4>
              </div>
              <div class="modal-body">
                <?php if ($lv_is_hr && !empty($employees)): ?>
                <div class="form-group">
                  <label>Employee <span class="text-danger">*</span></label>
                  <select name="user_id" class="form-control select2" style="width:100%;" required>
                    <?php foreach ($employees as $emp): ?>
                      <option value="<?php echo (int)($emp['id'] ?? 0); ?>" <?php echo ((int)$emp['id'] === (int)($cur_uid ?? 0)) ? 'selected' : ''; ?>><?php echo htmlspecialchars($emp['name'] ?? ''); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <?php endif; ?>
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
                      <input type="date" name="from_date" id="leaveFrom" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>End Date <span class="text-danger">*</span></label>
                      <input type="date" name="to_date" id="leaveTo" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label>Reason</label>
                  <textarea name="reason" class="form-control" rows="2"></textarea>
                </div>
                <span class="pf-note" id="leaveDays">1 day</span>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit request</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <script>
      (function () {
        var f = document.getElementById('leaveFrom'), t = document.getElementById('leaveTo'), o = document.getElementById('leaveDays');
        function upd() {
          if (t.value < f.value) { t.value = f.value; }
          t.min = f.value;
          var d = Math.round((new Date(t.value) - new Date(f.value)) / 86400000) + 1;
          o.textContent = (isNaN(d) || d < 1) ? '' : d + (d === 1 ? ' day' : ' days');
        }
        f.addEventListener('change', upd); t.addEventListener('change', upd); upd();
        if (location.hash === '#new') { $(function () { $('#modalApplyLeave').modal('show'); }); }
      })();
      </script>

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
                <div id="gratuityResultValue" style="font-size:24px; font-weight:700; color:#1e3a8a; margin-top:4px;">0.00</div>
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

    <?php elseif (($active_tab ?? '') === 'documents'):
      $dvH = function ($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };
      $dvRows = !empty($documents) ? $documents : array();
      $dvChip = array('all' => 'All', 'expired' => 'Expired', 'd30' => 'Within 30 days', 'd60' => 'Within 60 days', 'nofile' => 'No file');
      $dvCls = array('all' => '', 'expired' => 'st-r', 'd30' => 'st-a', 'd60' => 'st-a', 'nofile' => 'st-s');
    ?>
      <div class="hrx">
        <div class="hrx-chips">
          <?php foreach ($dvChip as $k => $lbl) { ?>
          <button type="button" class="hrx-chip js-dv <?php echo $dvCls[$k]; ?><?php echo $k === 'all' ? ' on' : ''; ?>" data-v="<?php echo $k; ?>"><?php echo $lbl; ?> <b><?php echo (int)($doc_counts[$k] ?? 0); ?></b></button>
          <?php } ?>
        </div>
        <div class="hrx-card">
          <div class="hrx-bar">
            <input type="search" class="form-control js-dv-q" placeholder="Search name or document">
            <?php if ($doc_base !== '') { ?>
            <select class="form-control js-dv-who"><option value="">Upload for...</option><?php foreach ($employees as $e) { ?><option value="<?php echo (int)$e['id']; ?>"><?php echo $dvH($e['name']); ?></option><?php } ?></select>
            <button type="button" class="btn btn-primary js-dv-up"><i class="fa fa-upload"></i> Upload document</button>
            <?php } ?>
            <span class="hrx-count js-dv-count"></span>
          </div>
          <div class="hrx-note"><i class="fa fa-info-circle"></i> Numbers and expiry dates come from each person's staff profile (Identity &amp; Visa); files are uploaded in the profile's Documents section (JPG, PNG or PDF, max 1 MB).</div>
          <div class="table-responsive">
          <table class="table hrx-table">
            <thead><tr><th>Employee</th><th>Document</th><th>Expiry</th><th>File</th></tr></thead>
            <tbody>
            <?php if ($dvRows): foreach ($dvRows as $d):
              $st = $d['state']; $dd = $d['days'];
              $chip = $st === 'expired' ? array('st-r', 'Expired') : ($st === 'd30' || $st === 'd60' ? array('st-a', $dd . ' day' . ($dd == 1 ? '' : 's') . ' left') : ($st === 'valid' ? array('st-g', 'Valid') : array('st-s', 'No date'))); ?>
              <tr data-state="<?php echo $st; ?>" data-file="<?php echo $d['file'] ? 1 : 0; ?>" data-q="<?php echo $dvH(strtolower($d['name'] . ' ' . $d['type'] . ' ' . $d['number'])); ?>">
                <td><div class="hrx-who"><span class="hrx-emp"><?php echo sprintf('EMP-%05d', $d['user_id']); ?></span>
                  <?php if ($doc_base !== '') { ?><a class="hrx-nm" href="<?php echo base_url() . $doc_base . 'view/' . $d['user_id']; ?>#docs"><?php echo $dvH($d['name']); ?></a><?php } else { ?><span class="hrx-nm"><?php echo $dvH($d['name']); ?></span><?php } ?></div></td>
                <td><b class="hrx-strong"><?php echo $dvH($d['type']); ?></b><span class="hrx-sub"><?php echo $d['number'] !== '' ? $dvH($d['number']) : 'No number'; ?></span></td>
                <td data-order="<?php echo $dd === null ? 999999 : $dd; ?>"><?php echo $d['expiry'] !== '' ? date('d-M-Y', strtotime($d['expiry'])) : ''; ?> <span class="hrx-st <?php echo $chip[0]; ?>"><?php echo $chip[1]; ?></span></td>
                <td><?php if ($d['file']) { $isPdf = ($d['file']['mime'] === 'application/pdf'); ?>
                  <?php if ($doc_base !== '') { ?><a class="hrx-file" href="<?php echo base_url() . $doc_base . 'doc_file/' . (int)$d['file']['id']; ?>" target="_blank" rel="noopener"><i class="fa <?php echo $isPdf ? 'fa-file-pdf-o' : 'fa-file-image-o'; ?>"></i> Open</a><?php } else { ?><span class="hrx-sub"><i class="fa fa-paperclip"></i> On file</span><?php } ?>
                  <span class="hrx-sub"><?php echo date('d-M-Y', strtotime($d['file']['uploaded_at'])); ?></span>
                <?php } else { ?><span class="hrx-miss">No file</span><?php if ($doc_base !== '') { ?> <a class="hrx-sub" style="display:inline" href="<?php echo base_url() . $doc_base . 'view/' . $d['user_id']; ?>#docs">Upload</a><?php } ?><?php } ?></td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colspan="4" class="text-center text-muted">No documents yet. Passport, visa and Emirates ID details are added on each staff profile.</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
          </div>
        </div>
      </div>
    <?php endif; ?>

  </section>
</div>

<script>
// HR tables: short values (dates, amounts, months) stay on one line; long text such as reasons wraps in a readable column
$(function () {
  // phone cards: each value gets its column name as a small label
  $('.hrx-table').each(function () {
    var h = $(this).find('thead th').map(function () { return $.trim($(this).text()); }).get();
    $(this).find('tbody tr').each(function () { $(this).children('td').each(function (i) { if (h[i]) $(this).attr('data-l', h[i]); }); });
  });
  $('.hrx-page .table-hr tbody td').each(function () {
    if ($.trim($(this).text()).length > 28 && !$(this).find('.btn,select').length) $(this).addClass('hrx-wrap');
  });
});
</script>
<script>
$(function() {
  // Document Vault: chips, search and "Upload for" (opens that person's Documents section)
  (function () {
    var rows = $('.hrx-table tbody tr[data-state]'); if (!rows.length && !$('.js-dv').length) return;
    var pick = 'all';
    function run() {
      var q = ($('.js-dv-q').val() || '').toLowerCase().trim(), n = 0;
      rows.each(function () {
        var r = $(this), s = r.data('state');
        var ok = (pick === 'all' || (pick === 'nofile' ? String(r.data('file')) === '0' : s === pick)) && (!q || String(r.data('q')).indexOf(q) !== -1);
        r.toggle(ok); if (ok) n++;
      });
      $('.js-dv-count').text(n + ' of ' + rows.length);
    }
    $('.js-dv').on('click', function () { $('.js-dv').removeClass('on'); $(this).addClass('on'); pick = $(this).data('v'); run(); });
    $('.js-dv-q').on('input', run);
    $('.js-dv-up').on('click', function () {
      var id = $('.js-dv-who').val();
      if (!id) { $('.js-dv-who').focus(); return; }
      location.href = '<?php echo base_url(); ?>manager/hr/staff/view/' + id + '#docs';
    });
    run();
  })();
  // Lifecycle: stage chips, department and search filter the rows in place
  (function () {
    var rows = $('.hrx-table tbody tr[data-stage]'); if (!rows.length) return;
    var stage = $('.js-lc-stage.on').data('v') || '';
    function run() {
      var dep = $('.js-lc-dept').val() || '', q = ($('.js-lc-q').val() || '').toLowerCase().trim(), n = 0;
      rows.each(function () {
        var r = $(this), ok = (!stage || r.data('stage') === stage) && (!dep || r.data('dept') === dep) && (!q || String(r.data('q')).indexOf(q) !== -1);
        r.toggle(ok); if (ok) n++;
      });
      $('.js-lc-count').text(n + ' of ' + rows.length);
    }
    $('.js-lc-stage').on('click', function () { $('.js-lc-stage').removeClass('on'); $(this).addClass('on'); stage = $(this).data('v') || ''; run(); });
    $('.js-lc-dept').on('change', run); $('.js-lc-q').on('input', run);
    run();
  })();
  $('.btn-update-stage').on('click', function() {
    var uid = $(this).data('user-id');
    var uname = $(this).data('user-name');
    var stg = $(this).data('stage');
    var b = $(this);

    $('#lc_user_id').val(uid);
    $('#lc_user_name').text(uname);
    $('#lc_stage').val(stg === 'Exited' ? 'Confirmed' : stg);
    $('#lc_contract').val(b.data('contract') || 'Permanent');
    $('#lc_probation').val(b.data('probation') || '');
    $('#lc_confirmed').val(b.data('confirmed') || '');
    $('#lc_notes').val(b.data('notes') || '');
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

  if ($.fn.modal && $.fn.modal.Constructor) {
    $.fn.modal.Constructor.prototype.enforceFocus = function () {};
  }
  $(document).on('shown.bs.modal', '.modal', function () {
    var $modal = $(this);
    $modal.find('.select2').each(function() {
      var $sel = $(this);
      if ($sel.hasClass('select2-hidden-accessible')) {
        $sel.select2('destroy');
      }
      $sel.select2({
        dropdownParent: $modal,
        placeholder: $sel.find('option:first').text() || 'Select Employee',
        allowClear: true,
        width: '100%'
      });
    });
  });
  $('#modalAssignTarget').on('shown.bs.modal', function () {
    setTimeout(function() {
      if ($('#target_user_id').length) {
        $('#target_user_id').select2('open');
      }
    }, 150);
  });
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
