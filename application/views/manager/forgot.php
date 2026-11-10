<?php require_once APPPATH . 'helpers/crm_brand_helper.php'; ?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo !empty($info['title']) ? htmlspecialchars($info['title']) : 'Forgot Password - Sales CRM'; ?></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/sales-crm-mark.png" type="image/png">
  <link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/sales-crm-mark.png">
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
      <h1 class="auth-title">Forgot Password</h1>
      <p class="auth-sub">Enter your registered email address and we will send you a link to reset your password.</p>

      <?php 
        $err = $this->session->flashdata('error_message');
        if(!empty($err)) {
      ?>
        <div class="auth-alert auth-alert-danger"><?php echo htmlspecialchars($err); ?></div>
      <?php } ?>

      <?php 
        $msg = $this->session->flashdata('message');
        if(!empty($msg)) {
      ?>
        <div class="auth-alert auth-alert-success"><?php echo htmlspecialchars($msg); ?></div>
      <?php } ?>

      <form action="<?php echo base_url(); ?>manager/forgot" method="post" id="forgotForm">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

        <div class="auth-field">
          <label class="auth-label" for="forgot_email">Email address</label>
          <div class="auth-input-wrap">
            <svg class="auth-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="m22 7-10 6L2 7"></path></svg>
            <input type="email" class="auth-input has-icon" id="forgot_email" name="email" placeholder="name@company.com" required autocomplete="email">
          </div>
        </div>

        <button type="submit" class="auth-btn" id="forgotBtn" style="margin-top: 6px;">Send Reset Link</button>
      </form>

      <p class="auth-alt"><a href="<?php echo base_url(); ?>manager/login" class="auth-link">Back to Sign In</a></p>
     </div>
      <div class="auth-foot">&copy; <?php echo date('Y'); ?> <?php $crm_b = crm_brand(); echo htmlspecialchars($crm_b['name'], ENT_QUOTES, 'UTF-8'); ?></div>
    </main>
  </div>
</body>
</html>