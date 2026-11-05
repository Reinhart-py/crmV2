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
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-auth.css?v=20261105_8">
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
      <div class="auth-brand"><img src="<?php echo base_url(); ?>assets/logo.png" alt="Seven Strokes"></div>
      <h1 class="auth-title">Sign In</h1>
      <p class="auth-sub">Get access to your account</p>

      <?php if(isset($_GET['conflict'])) { ?>
        <div class="auth-alert auth-alert-danger">
          You were signed out because your account was signed in on another device.
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
          <label class="auth-label" for="login_email">Username</label>
          <div class="auth-input-wrap">
            <svg class="auth-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            <input type="text" class="auth-input has-icon" id="login_email" name="email" placeholder="Enter your username" required autocomplete="username">
          </div>
        </div>

        <div class="auth-field">
          <label class="auth-label" for="login_password">Password</label>
          <div class="auth-input-wrap">
            <svg class="auth-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            <input type="password" class="auth-input has-icon has-toggle" name="password" id="login_password" placeholder="Enter your password" required autocomplete="current-password">
            <button type="button" class="auth-pwd-toggle" id="btnToggleLoginPwd" title="Show or hide password">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            </button>
          </div>
        </div>

        <div class="auth-row">
          <label class="auth-check" for="rememberMe"><input type="checkbox" id="rememberMe" name="remember_me"> Remember Me</label>
          <a href="<?php echo base_url(); ?>manager/forgot" class="auth-link">Forgot Password?</a>
        </div>

        <button type="submit" class="auth-btn" id="loginBtn">Sign In</button>
      </form>
     </div>
      <div class="auth-foot">&copy; <?php echo date('Y'); ?> Seven Strokes</div>
    </main>
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
