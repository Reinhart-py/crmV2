<?php require_once(APPPATH . "views/manager/elements/header.php"); ?>
<?php
$x = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$s = $settings;
$csrf = '<input type="hidden" name="' . $this->security->get_csrf_token_name() . '" value="' . $this->security->get_csrf_hash() . '">';
$act = base_url() . 'manager/system/lead-finder/';
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-sys.css?v=20261106_7">
<style>
  .lk-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px 16px; }
  .lk-grid .wide { grid-column: 1 / -1; }
  .lk-grid .form-group { margin: 0; }
  .lk-key { display: flex; gap: 6px; } .lk-key input { font-family: monospace; }
  .lk-note { color: #64748b; font-size: 12.5px; }
  @media (max-width: 767px) { .lk-grid { grid-template-columns: 1fr; } }
</style>
<div class="content-wrapper sx">
  <?php echo crm_tabbar('integrations', 'system/lead-finder'); ?>
  <section class="sx-head">
    <h1>Lead Finder link<small>Lets the Lead Finder app send leads here for a duplicate check and approval</small></h1>
  </section>

  <section class="content">
    <?php echo get_message($this->session->flashdata('error_message'), 'error_message'); ?>
    <?php echo get_message($this->session->flashdata('message'), 'message'); ?>

    <div class="sx-card">
      <div class="sx-card-h"><h3><i class="fa fa-cog"></i> Settings</h3><div class="sx-tools"><span class="chip <?php echo $s['on'] ? 'ok' : 'na'; ?>"><?php echo $s['on'] ? 'On' : 'Off'; ?></span></div></div>
      <form method="post" action="<?php echo $act; ?>save" class="sx-card-b">
        <?php echo $csrf; ?>
        <div class="lk-grid">
          <div class="form-group">
            <label>Link</label>
            <select name="status" class="form-control"<?php echo $can_edit ? '' : ' disabled'; ?>>
              <option value="1"<?php echo $s['on'] ? ' selected' : ''; ?>>On</option>
              <option value="0"<?php echo !$s['on'] ? ' selected' : ''; ?>>Off</option>
            </select>
          </div>
          <div class="form-group">
            <label>Duplicate when this many fields match</label>
            <select name="threshold" class="form-control"<?php echo $can_edit ? '' : ' disabled'; ?>>
              <?php for ($i = 3; $i <= 9; $i++) { ?><option value="<?php echo $i; ?>"<?php echo $i === $s['threshold'] ? ' selected' : ''; ?>><?php echo $i; ?> of 9<?php echo $i === 5 ? ' (recommended)' : ''; ?></option><?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label>Most leads in one batch</label>
            <input type="number" name="max_batch" min="50" max="5000" class="form-control" value="<?php echo (int)$s['max_batch']; ?>"<?php echo $can_edit ? '' : ' disabled'; ?>>
          </div>
          <?php if ($s['secret'] !== '') { ?>
          <div class="form-group wide">
            <label>Give these to Lead Finder (Admin &gt; CRM link)</label>
            <div class="lk-grid" style="grid-template-columns: 1fr 2fr;">
              <div class="lk-key"><input type="text" readonly class="form-control" value="<?php echo $x($crm_url); ?>" onclick="this.select()"></div>
              <div class="lk-key"><input type="password" readonly class="form-control" id="lkSecret" value="<?php echo $x($can_edit ? $s['secret'] : str_repeat('*', 20)); ?>" onclick="this.select()">
                <?php if ($can_edit) { ?><button type="button" class="btn btn-default" onclick="var i=document.getElementById('lkSecret');i.type=i.type==='password'?'text':'password';">Show</button><?php } ?></div>
            </div>
            <p class="lk-note">CRM address and link key. Keep the key secret: anyone with it can send leads in the name of a CRM user who has the Lead Finder right (leads are still only added after that user approves them in the CRM).</p>
          </div>
          <?php } ?>
        </div>
        <?php if ($can_edit) { ?>
        <div style="margin-top:14px; display:flex; gap:8px; flex-wrap:wrap;">
          <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
        </div>
        <?php } ?>
      </form>
      <?php if ($can_edit && $s['secret'] !== '') { ?>
      <form method="post" action="<?php echo $act; ?>regenerate" class="sx-card-f" onsubmit="return confirm('Make a new key? Lead Finder stops working until the new key is pasted there.');">
        <?php echo $csrf; ?>
        <button type="submit" class="btn btn-default"><i class="fa fa-refresh"></i> Make a new key</button>
        <span class="lk-note">Use this if the key may have leaked.</span>
      </form>
      <?php } ?>
    </div>

    <div class="sx-card">
      <div class="sx-card-h"><h3><i class="fa fa-users"></i> Who can send leads (<?php echo count($users_with_right); ?>)</h3></div>
      <div class="sx-card-b">
        <p class="lk-note" style="margin-top:0;">Only active users with the <b>Lead Finder</b> right (Roles &amp; Permissions &gt; System) can send leads, and Lead Finder sends the email they sign in with. Full access also lets them add confirmed duplicates and fill in existing records.</p>
        <?php if ($users_with_right) { ?>
        <div class="sx-table-wrap"><table class="sx-table">
          <thead><tr><th>Name</th><th>Email</th><th>Access</th></tr></thead>
          <tbody><?php foreach ($users_with_right as $u) { ?><tr><td><?php echo $x($u['name']); ?></td><td><?php echo $x($u['email']); ?></td><td><span class="chip <?php echo $u['full'] ? 'ok' : 's'; ?>"><?php echo $u['full'] ? 'Full (can add duplicates)' : 'Add new and possible'; ?></span></td></tr><?php } ?></tbody>
        </table></div>
        <?php } else { ?><div class="sx-empty">Nobody has the Lead Finder right yet.</div><?php } ?>
      </div>
    </div>
  </section>
</div>
<?php require_once(APPPATH . "views/manager/elements/footer.php"); ?>
