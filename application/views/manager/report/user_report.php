<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<?php require(APPPATH."views/manager/report/_rp.php"); ?>
<?php
$enabled = 0; $roles = array();
foreach ($list as $li) { if ((int)$li['admin_status'] === 1) $enabled++; if (!empty($li['role_name'])) $roles[$li['role_name']] = 1; }
?>
<div class="content-wrapper">
  <div class="rp-head">
    <h1><?php echo $e($info['page_heading']); ?></h1>
    <?php if ($list) { ?><a class="btn btn-default btn-sm" href="<?php echo $e($csv_url()); ?>"><i class="fa fa-download"></i> Export CSV</a><?php } ?>
  </div>
  <section class="content" style="padding-top:0;">
    <div class="rp-box">
      <form class="rp-filter" method="get" action="<?php echo base_url(); ?>manager/users/report">
        <div><label for="urD1">Joined from</label><input id="urD1" type="date" class="form-control" name="d1" value="<?php echo $e($f['d1']); ?>"></div>
        <div><label for="urD2">Joined to</label><input id="urD2" type="date" class="form-control" name="d2" value="<?php echo $e($f['d2']); ?>"></div>
        <div><label for="urRole">Role</label>
          <select id="urRole" class="form-control" name="role">
            <option value="">All</option>
            <?php foreach ($r_list as $r) { ?><option value="<?php echo (int)$r['id']; ?>" <?php echo (string)$f['role'] === (string)$r['id'] ? 'selected' : ''; ?>><?php echo $e($r['title']); ?></option><?php } ?>
          </select>
        </div>
        <?php if (count($u_list) > 1) { ?>
        <div><label for="urMgr">Manager</label>
          <select id="urMgr" class="form-control" name="manager">
            <option value="">All</option>
            <?php foreach ($u_list as $u) { ?><option value="<?php echo (int)$u['id']; ?>" <?php echo (string)$f['manager'] === (string)$u['id'] ? 'selected' : ''; ?>><?php echo $e($u['name']); ?></option><?php } ?>
          </select>
        </div>
        <?php } ?>
        <?php if ($offices) { ?>
        <div><label for="urOff">Center</label>
          <select id="urOff" class="form-control" name="office">
            <option value="">All</option>
            <?php foreach ($offices as $o) { ?><option <?php echo $f['office'] === $o ? 'selected' : ''; ?>><?php echo $e($o); ?></option><?php } ?>
          </select>
        </div>
        <?php } ?>
        <div><label for="urSt">Login</label>
          <select id="urSt" class="form-control" name="status">
            <option value="">All</option>
            <option value="1" <?php echo $f['status'] === '1' ? 'selected' : ''; ?>>Enabled</option>
            <option value="0" <?php echo $f['status'] === '0' ? 'selected' : ''; ?>>Disabled</option>
          </select>
        </div>
        <div class="rp-btns"><button type="submit" class="btn btn-primary">Show</button><a class="btn btn-default" href="<?php echo base_url(); ?>manager/users/report">Reset</a></div>
      </form>
    </div>

    <div class="rp-kpis">
      <div class="rp-kpi"><span>Users</span><b><?php echo $num(count($list)); ?></b></div>
      <div class="rp-kpi"><span>Login enabled</span><b><?php echo $num($enabled); ?></b></div>
      <div class="rp-kpi"><span>Login disabled</span><b><?php echo $num(count($list) - $enabled); ?></b></div>
      <div class="rp-kpi"><span>Roles</span><b><?php echo $num(count($roles)); ?></b></div>
    </div>

    <div class="rp-box">
      <?php if (!$list) { ?>
        <div class="alert alert-info rp-empty">No users for this selection.</div>
      <?php } else { ?>
      <div class="table-responsive">
        <table class="table table-bordered table-striped rp-table" id="urTable">
          <thead><tr><th>Name</th><th>Employee ID</th><th>Email</th><th>Role</th><th>Center</th><th>Manager</th><th>Joined</th><th>Login</th></tr></thead>
          <tbody>
          <?php foreach ($list as $li) { ?>
            <tr>
              <td><a href="<?php echo base_url(); ?>manager/users/view/<?php echo (int)$li['id']; ?>"><?php echo $e($li['name']); ?></a></td>
              <td class="nw"><?php echo $e($li['emp_id']); ?></td>
              <td><?php echo $e($li['email']); ?></td>
              <td><?php echo $e($li['role_name']); ?></td>
              <td><?php echo $e($li['office']); ?></td>
              <td><?php echo $e($li['manager_name']); ?></td>
              <td class="nw"><?php echo $e($day($li['created'])); ?></td>
              <td><?php echo (int)$li['admin_status'] === 1 ? 'Enabled' : '<span class="rp-muted">Disabled</span>'; ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
      <?php } ?>
    </div>
  </section>
</div>
<script src="<?php echo base_url(); ?>assets/dist/js/crm-pager.js?v=20261106_2"></script>
<script>crmPager('#urTable', 50);</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
