<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="content-wrapper">
  <section class="content-header" style="display:flex; justify-content:space-between; align-items:center; padding:15px 15px 5px 15px;">
    <h1 style="margin:0; font-size:20px; font-weight:700; color:#0f172a;">
      <i class="fa fa-trophy" style="color:#f59e0b; margin-right:8px;"></i><?php echo $info['page_heading']; ?>
    </h1>
    <div style="display:flex; gap:8px;">
      <a href="<?php echo base_url(); ?>manager/events" class="btn btn-default" style="font-weight:600; padding:6px 14px; border-radius:5px;">
        <i class="fa fa-calendar-check-o" style="color:#2563eb;"></i> Events Calendar
      </a>
      <?php if($can_manage){ ?>
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addAwardModal" style="background:#f59e0b; border-color:#d97706; font-weight:600; padding:6px 14px; border-radius:5px; color:#ffffff;">
        <i class="fa fa-star"></i> Concur Award
      </button>
      <?php } ?>
    </div>
  </section>

  <section class="content" style="padding-top:10px;">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="row">
      <div class="col-md-8">
        <div class="box box-default" style="border-radius:6px; margin-bottom:15px;">
          <div class="box-body" style="padding:15px;">
            <form method="get" action="<?php echo base_url(); ?>manager/awards" class="form-inline" style="display:flex; flex-wrap:wrap; gap:10px; align-items:center;">
              <div class="form-group">
                <select name="cat" class="form-control input-sm" style="height:32px; min-width:160px;">
                  <option value="" <?php echo empty($filter_cat) ? 'selected' : ''; ?>>All Categories</option>
                  <option value="Top Performer" <?php echo ($filter_cat == 'Top Performer') ? 'selected' : ''; ?>>Top Performer</option>
                  <option value="Deal Closer" <?php echo ($filter_cat == 'Deal Closer') ? 'selected' : ''; ?>>Deal Closer</option>
                  <option value="Customer Champion" <?php echo ($filter_cat == 'Customer Champion') ? 'selected' : ''; ?>>Customer Champion</option>
                  <option value="Rising Star" <?php echo ($filter_cat == 'Rising Star') ? 'selected' : ''; ?>>Rising Star</option>
                  <option value="Innovation" <?php echo ($filter_cat == 'Innovation') ? 'selected' : ''; ?>>Innovation</option>
                  <option value="Team Player" <?php echo ($filter_cat == 'Team Player') ? 'selected' : ''; ?>>Team Player</option>
                </select>
              </div>

              <?php if(!empty($users_list)){ ?>
              <div class="form-group">
                <select name="user_id" class="form-control input-sm" style="height:32px; min-width:160px;">
                  <option value="0">All Employees</option>
                  <?php foreach($users_list as $u){ ?>
                    <option value="<?php echo $u['id']; ?>" <?php echo ($filter_user == $u['id']) ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($u['name']); ?>
                    </option>
                  <?php } ?>
                </select>
              </div>
              <?php } ?>

              <button type="submit" class="btn btn-sm btn-primary" style="background:#2563eb; border-color:#1d4ed8; font-weight:600; height:32px;">
                <i class="fa fa-filter"></i> Filter
              </button>
              <a href="<?php echo base_url(); ?>manager/awards" class="btn btn-sm btn-default" style="height:32px;">Reset</a>
            </form>
          </div>
        </div>

        <div class="box box-default" style="border-radius:6px;">
          <div class="box-header with-border" style="display:flex; justify-content:space-between; align-items:center;">
            <h3 class="box-title" style="font-size:15px; font-weight:600; color:#1e293b;">
              Awards &amp; Recognition History
            </h3>
            <span class="label label-warning"><?php echo count($awards); ?> Award<?php echo count($awards) == 1 ? '' : 's'; ?></span>
          </div>
          <div class="box-body" style="padding:15px;">
            <?php if(!empty($awards)){ ?>
              <div class="table-responsive">
                <table class="table table-hover table-bordered datatable-table" style="width:100%; font-size:12px;">
                  <thead>
                    <tr style="background:#f8fafc; color:#334155;">
                      <th>Employee</th>
                      <th>Award Title</th>
                      <th>Category</th>
                      <th>Date</th>
                      <th class="text-center">Points</th>
                      <th>Conferred By</th>
                      <?php if($can_manage){ ?>
                      <th class="text-center" style="width:50px;">Action</th>
                      <?php } ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach($awards as $aw){ ?>
                      <tr>
                        <td>
                          <strong style="color:#0f172a;"><?php echo htmlspecialchars($aw['employee_name'] ?: 'Unknown'); ?></strong>
                          <?php if(!empty($aw['department'])){ ?>
                            <div style="font-size:11px; color:#64748b;"><?php echo htmlspecialchars($aw['department']); ?></div>
                          <?php } ?>
                        </td>
                        <td>
                          <div style="font-weight:600; color:#1e293b;"><?php echo htmlspecialchars($aw['title']); ?></div>
                          <?php if(!empty($aw['description'])){ ?>
                            <div style="font-size:11px; color:#64748b; margin-top:2px;"><?php echo htmlspecialchars($aw['description']); ?></div>
                          <?php } ?>
                        </td>
                        <td>
                          <span class="label label-warning" style="background:#fef3c7; color:#b45309; border:1px solid #fde68a;">
                            <i class="fa fa-trophy"></i> <?php echo htmlspecialchars($aw['category']); ?>
                          </span>
                        </td>
                        <td><?php echo date('d-m-Y', strtotime($aw['award_date'])); ?></td>
                        <td class="text-center">
                          <strong style="color:#16a34a;">+<?php echo $aw['reward_points']; ?></strong>
                        </td>
                        <td><?php echo htmlspecialchars($aw['creator_name'] ?: 'Management'); ?></td>
                        <?php if($can_manage){ ?>
                        <td class="text-center">
                          <a href="<?php echo base_url(); ?>manager/awards/delete/<?php echo $aw['id']; ?>" class="btn btn-xs btn-danger" onclick="return confirm('Remove this award?');" title="Delete Award">
                            <i class="fa fa-trash"></i>
                          </a>
                        </td>
                        <?php } ?>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            <?php } else { ?>
              <div class="alert alert-info" style="margin-bottom:0;">No awards found. Use the "Concur Award" button to recognize outstanding team performance.</div>
            <?php } ?>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="box box-default" style="border-radius:6px; border-top:3px solid #f59e0b;">
          <div class="box-header with-border">
            <h3 class="box-title" style="font-size:15px; font-weight:600; color:#1e293b;">
              <i class="fa fa-star" style="color:#f59e0b; margin-right:6px;"></i>Recognition Leaderboard
            </h3>
          </div>
          <div class="box-body" style="padding:15px;">
            <?php if(!empty($leaderboard)){ ?>
              <ul class="list-group" style="margin-bottom:0;">
                <?php foreach($leaderboard as $rank => $lb){ 
                  $medal_color = '#94a3b8';
                  if($rank == 0) $medal_color = '#f59e0b';
                  elseif($rank == 1) $medal_color = '#94a3b8';
                  elseif($rank == 2) $medal_color = '#cd7f32';
                ?>
                  <li class="list-group-item" style="display:flex; justify-content:space-between; align-items:center; border:none; border-bottom:1px solid #f1f5f9; padding:10px 0;">
                    <div style="display:flex; align-items:center; gap:10px;">
                      <span style="font-size:14px; font-weight:700; color:<?php echo $medal_color; ?>; width:28px; text-align:center;">
                        Rank <?php echo $rank + 1; ?>
                      </span>
                      <div>
                        <strong style="color:#0f172a; font-size:13px;"><?php echo htmlspecialchars($lb['employee_name'] ?: 'Unknown'); ?></strong>
                        <div style="font-size:11px; color:#64748b;"><?php echo htmlspecialchars($lb['department'] ?: 'Sales'); ?> &bull; <?php echo $lb['total_awards']; ?> award<?php echo $lb['total_awards'] == 1 ? '' : 's'; ?></div>
                      </div>
                    </div>
                    <span class="badge" style="background:#fef3c7; color:#92400e; font-size:12px; font-weight:700; padding:4px 8px;">
                      <?php echo number_format($lb['total_points']); ?> pts
                    </span>
                  </li>
                <?php } ?>
              </ul>
            <?php } else { ?>
              <div style="text-align:center; padding:20px; color:#94a3b8;">
                <i class="fa fa-trophy" style="font-size:32px; margin-bottom:8px; display:block;"></i>
                No achievements recorded yet.
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php if($can_manage){ ?>
<div class="modal fade" id="addAwardModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="border-radius:8px;">
      <form action="<?php echo base_url(); ?>manager/awards/add" method="post">
        <div class="modal-header" style="border-bottom:1px solid #e2e8f0;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" style="font-weight:700; color:#0f172a;"><i class="fa fa-trophy" style="color:#f59e0b;"></i> Concur Employee Award</h4>
        </div>
        <div class="modal-body" style="padding:20px;">
          <div class="form-group">
            <label style="font-size:13px; font-weight:600; color:#334155;">Employee / Recipient <span class="text-danger">*</span></label>
            <select name="user_id" required class="form-control" style="border-radius:5px;">
              <option value="">Select Employee...</option>
              <?php foreach($users_list as $u){ ?>
                <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['name'] . ' (' . ($u['department'] ?: 'Sales') . ')'); ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label style="font-size:13px; font-weight:600; color:#334155;">Award Title <span class="text-danger">*</span></label>
            <input type="text" name="title" required class="form-control" placeholder="e.g. Employee of the Month / Star Closer" style="border-radius:5px;">
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label style="font-size:13px; font-weight:600; color:#334155;">Award Category</label>
                <select name="category" class="form-control" style="border-radius:5px;">
                  <option value="Top Performer">Top Performer</option>
                  <option value="Deal Closer">Deal Closer</option>
                  <option value="Customer Champion">Customer Champion</option>
                  <option value="Rising Star">Rising Star</option>
                  <option value="Innovation">Innovation</option>
                  <option value="Team Player">Team Player</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label style="font-size:13px; font-weight:600; color:#334155;">Reward Points</label>
                <input type="number" name="reward_points" value="100" min="10" step="10" class="form-control" style="border-radius:5px;">
              </div>
            </div>
          </div>
          <div class="form-group">
            <label style="font-size:13px; font-weight:600; color:#334155;">Award Date <span class="text-danger">*</span></label>
            <input type="date" name="award_date" value="<?php echo date('Y-m-d'); ?>" required class="form-control" style="border-radius:5px;">
          </div>
          <div class="form-group">
            <label style="font-size:13px; font-weight:600; color:#334155;">Recognition Notes / Citation</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Explain the key achievements and milestones that earned this recognition..." style="border-radius:5px;"></textarea>
          </div>
        </div>
        <div class="modal-footer" style="border-top:1px solid #e2e8f0;">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" style="background:#f59e0b; border-color:#d97706; font-weight:600; color:#ffffff;">Concur Award</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php } ?>

<script>
$(document).ready(function() {
  if ($.fn.DataTable) {
    $('.datatable-table').DataTable({
      "pageLength": 25,
      "order": [],
      responsive: true
    });
  }
});
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
