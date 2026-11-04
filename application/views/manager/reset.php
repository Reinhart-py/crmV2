<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $info['title']; ?></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/logo.png" type="image/png">
  <link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/logo.png">
  <link rel="apple-touch-icon" href="<?php echo base_url(); ?>assets/logo.png">
  <meta property="og:title" content="<?php echo !empty($info['title']) ? htmlspecialchars($info['title']) : 'Seven Strokes CRM - Reset Password'; ?>">
  <meta property="og:description" content="Seven Strokes CRM Platform">
  <meta property="og:image" content="<?php echo base_url(); ?>assets/logo.png">
  <meta property="og:type" content="website">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:image" content="<?php echo base_url(); ?>assets/logo.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-auth.css?v=20261104_2">
</head>
<body class="auth-page">
  <div class="auth-shell">
<?php require(APPPATH . 'views/manager/elements/auth_visual.php'); ?>
    <div class="auth-panel">
      <div class="auth-brand"><img src="<?php echo base_url(); ?>assets/logo.png" alt="Seven Strokes"></div>
      <h1 class="auth-title">Set a new password</h1>
      <p class="auth-sub">Choose a new password for your CRM account.</p>
      <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> <?php echo get_message($this->session->flashdata('message'),'message'); ?>
      <form action="<?php echo base_url(); ?>ci_admin/fchange_password" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <div class="auth-field">
          <div class="auth-field-head"><label class="auth-label" for="reset_password">New password</label></div>
          <div class="auth-input-wrap">
            <i class="fa fa-lock"></i>
            <input type="password" class="auth-input" id="reset_password" name="password" placeholder="Enter new password" required>
          </div>
        </div>
        <div class="auth-field">
          <div class="auth-field-head"><label class="auth-label" for="reset_password_repeat">Repeat password</label></div>
          <div class="auth-input-wrap">
            <i class="fa fa-lock"></i>
            <input type="password" class="auth-input" id="reset_password_repeat" name="change_password" placeholder="Repeat new password" required>
          </div>
        </div>
        <button type="submit" class="auth-btn" style="margin-top: 6px;">Save password</button>
      </form>
      <p class="auth-alt">Back to <a href="<?php echo base_url(); ?>manager/login" class="auth-link">Sign in</a></p>
    </div>
  </div>
</body>
</html>