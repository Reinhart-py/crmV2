<?php require_once APPPATH . 'helpers/crm_brand_helper.php'; ?><!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $info['title']; ?></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/sales-crm-mark.png" type="image/png">
  <link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/sales-crm-mark.png">
  <link rel="apple-touch-icon" href="<?php echo base_url(); ?>assets/sales-crm-mark.png">
  <meta property="og:title" content="<?php echo !empty($info['title']) ? htmlspecialchars($info['title']) : 'Sales CRM - Reset Password'; ?>">
  <meta property="og:description" content="Sales CRM Platform">
  <meta property="og:image" content="<?php echo base_url(); ?>assets/sales-crm-mark.png">
  <meta property="og:type" content="website">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:image" content="<?php echo base_url(); ?>assets/sales-crm-mark.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-auth.css?v=20261105_17">
</head>
<body class="auth-page">
  <div class="auth-shell">
<?php require(APPPATH . 'views/manager/elements/auth_visual.php'); ?>
    <main class="auth-panel">
      <div class="sp-top sp-top-end">
        <a href="<?php echo base_url(); ?>support" class="auth-support-btn" title="Get help from our support team">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 14v-3a9 9 0 0 1 18 0v3"></path><path d="M21 15a2 2 0 0 1-2 2h-1v-5h1a2 2 0 0 1 2 2z"></path><path d="M3 15a2 2 0 0 0 2 2h1v-5H5a2 2 0 0 0-2 2z"></path><path d="M18 17v1a3 3 0 0 1-3 3h-3"></path></svg>
          Support
        </a>
      </div>
     <div class="auth-panel-inner">
      <div class="auth-brand"><?php echo crm_brand_mark('auth'); ?></div>
      <h1 class="auth-title">Set New Password</h1>
      <p class="auth-sub">Choose a new password for your CRM account.</p>
      <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> <?php echo get_message($this->session->flashdata('message'),'message'); ?>
      <form action="<?php echo base_url(); ?>ci_admin/fchange_password" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <div class="auth-field">
          <label class="auth-label" for="reset_password">New password</label>
          <div class="auth-input-wrap">
            <svg class="auth-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            <input type="password" class="auth-input has-icon" id="reset_password" name="password" placeholder="Enter new password" required>
          </div>
        </div>
        <div class="auth-field">
          <label class="auth-label" for="reset_password_repeat">Repeat password</label>
          <div class="auth-input-wrap">
            <svg class="auth-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            <input type="password" class="auth-input has-icon" id="reset_password_repeat" name="change_password" placeholder="Repeat new password" required>
          </div>
        </div>
        <button type="submit" class="auth-btn" style="margin-top: 6px;">Save password</button>
      </form>
      <p class="auth-alt"><a href="<?php echo base_url(); ?>manager/login" class="auth-link">Back to Sign In</a></p>
     </div>
      <div class="auth-foot">&copy; <?php echo date('Y'); ?> <?php $crm_b = crm_brand(); echo htmlspecialchars($crm_b['name'], ENT_QUOTES, 'UTF-8'); ?></div>
    </main>
  </div>
</body>
</html>