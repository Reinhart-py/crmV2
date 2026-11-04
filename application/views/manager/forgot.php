<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo !empty($info['title']) ? htmlspecialchars($info['title']) : 'Forgot Password - Seven Strokes CRM'; ?></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/logo.png" type="image/png">
  <link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/logo.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-auth.css?v=20261104_1">
</head>
<body class="auth-page">
  <div class="auth-shell">
<?php require(APPPATH . 'views/manager/elements/auth_visual.php'); ?>
    <div class="auth-panel">
      <div class="auth-brand"><img src="<?php echo base_url(); ?>assets/logo.png" alt="Seven Strokes"></div>
      <h1 class="auth-title">Reset your password</h1>
      <p class="auth-sub">Enter the email address registered to your account and we will send you a reset link.</p>

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
          <div class="auth-field-head"><label class="auth-label" for="forgot_email">Registered email address</label></div>
          <div class="auth-input-wrap">
            <i class="fa fa-envelope-o"></i>
            <input type="email" class="auth-input" id="forgot_email" name="email" placeholder="name@company.com" required autocomplete="email">
          </div>
        </div>

        <button type="submit" class="auth-btn" id="forgotBtn" style="margin-top: 6px;">Send reset link</button>
      </form>

      <p class="auth-alt">Remembered your password? <a href="<?php echo base_url(); ?>manager/login" class="auth-link">Sign in</a></p>
      <div class="auth-foot">&copy; <?php echo date('Y'); ?> Seven Strokes. Authorised personnel only.</div>
    </div>
  </div>
</body>
</html>