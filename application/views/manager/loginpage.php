<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $info['title']; ?></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/logo.png" type="image/png">
  <link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/logo.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-auth.css?v=20261104_2">
</head>
<body class="auth-page">
  <div class="auth-shell">
<?php require(APPPATH . 'views/manager/elements/auth_visual.php'); ?>
    <div class="auth-panel">
      <div class="auth-brand"><img src="<?php echo base_url(); ?>assets/logo.png" alt="Seven Strokes"></div>
      <h1 class="auth-title">Welcome back</h1>
      <p class="auth-sub">Sign in with your CRM username and password to continue.</p>

      <?php if(isset($_GET['conflict'])) { ?>
        <div class="auth-alert auth-alert-danger">
          <strong>Session terminated:</strong> you were signed out because another device signed in to your account.
        </div>
      <?php } ?>

      <?php if(isset($_GET['timeout'])) { ?>
        <div class="auth-alert auth-alert-warning">
          Your session expired after 5 minutes of inactivity. Please sign in again.
        </div>
      <?php } ?>

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

      <form action="<?php echo base_url(); ?>manager/login" method="post" id="loginForm">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="latitude" id="login_lat" value="">
        <input type="hidden" name="longitude" id="login_lng" value="">
        <input type="hidden" name="accuracy" id="login_acc" value="">
        <input type="hidden" name="location_status" id="login_loc_status" value="unrequested">

        <div class="auth-field">
          <div class="auth-field-head"><label class="auth-label" for="login_email">Username</label></div>
          <div class="auth-input-wrap">
            <i class="fa fa-user-o"></i>
            <input type="text" class="auth-input" id="login_email" name="email" placeholder="Enter your username" required autocomplete="username">
          </div>
        </div>

        <div class="auth-field">
          <div class="auth-field-head">
            <label class="auth-label" for="login_password">Password</label>
            <a href="<?php echo base_url(); ?>manager/forgot" class="auth-link">Forgot password?</a>
          </div>
          <div class="auth-input-wrap">
            <i class="fa fa-lock"></i>
            <input type="password" class="auth-input" name="password" id="login_password" placeholder="Enter your password" required autocomplete="current-password" style="padding-right: 42px;">
            <button type="button" class="auth-pwd-toggle" id="btnToggleLoginPwd" title="Show or hide password">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            </button>
          </div>
        </div>

        <div class="auth-row">
          <label class="auth-check" for="rememberMe"><input type="checkbox" id="rememberMe" name="remember_me"> Remember me</label>
        </div>

        <button type="submit" class="auth-btn" id="loginBtn">Sign in</button>      </form>
      <div class="auth-foot">&copy; <?php echo date('Y'); ?> Seven Strokes. Authorised personnel only.</div>
    </div>
  </div>

  <script src="<?php echo base_url(); ?>assets/plugins/jQuery/jquery-2.2.3.min.js"></script>
  <script>
    $(function () {
      if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(function(pos) {
          $('#login_lat').val(pos.coords.latitude);
          $('#login_lng').val(pos.coords.longitude);
          $('#login_acc').val(pos.coords.accuracy);
          $('#login_loc_status').val('granted');
        }, function(err) {
          if (err.code === 1) {
            $('#login_loc_status').val('denied');
          } else if (err.code === 2) {
            $('#login_loc_status').val('unavailable');
          } else if (err.code === 3) {
            $('#login_loc_status').val('timeout');
          } else {
            $('#login_loc_status').val('error');
          }
        }, {
          enableHighAccuracy: true,
          timeout: 6000,
          maximumAge: 60000
        });
      } else {
        $('#login_loc_status').val('unsupported');
      }

      $('#btnToggleLoginPwd').on('click', function(e) {
        e.preventDefault();
        var $inp = $('#login_password');
        if ($inp.attr('type') === 'password') {
          $inp.attr('type', 'text');
          $(this).html('<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>');
        } else {
          $inp.attr('type', 'password');
          $(this).html('<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>');
        }
      });
    });
  </script>
</body>
</html>
