<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $info['title']; ?></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/logo.png" type="image/png">
  <link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/logo.png">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }
    body.login-page {
      margin: 0;
      padding: 20px;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      background: linear-gradient(135deg, #a78bfa 0%, #8b5cf6 50%, #7c3aed 100%);
    }
    .auth-card {
      width: 100%;
      max-width: 960px;
      background: #ffffff;
      border-radius: 18px;
      box-shadow: 0 25px 50px -12px rgba(91, 33, 182, 0.25);
      overflow: hidden;
      display: flex;
      flex-direction: row;
      align-items: stretch;
      min-height: 560px;
    }
    .auth-form-side {
      flex: 1 1 48%;
      padding: 44px 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background: #ffffff;
      z-index: 2;
    }
    .auth-image-side {
      flex: 1 1 52%;
      background: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 30px;
      position: relative;
    }
    .auth-image-side img {
      max-width: 100%;
      height: auto;
      object-fit: contain;
    }
    .auth-header {
      margin-bottom: 24px;
    }
    .auth-header h1 {
      font-size: 28px;
      font-weight: 700;
      color: #0f172a;
      margin: 0 0 6px 0;
      letter-spacing: -0.5px;
    }
    .auth-header p {
      font-size: 13.5px;
      color: #64748b;
      margin: 0;
    }
    .auth-header p a {
      color: #7c3aed;
      font-weight: 600;
      text-decoration: none;
    }
    .auth-header p a:hover {
      text-decoration: underline;
    }
    .auth-group {
      margin-bottom: 18px;
    }
    .auth-group-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 6px;
    }
    .auth-label {
      font-size: 13px;
      font-weight: 600;
      color: #334155;
      margin: 0;
    }
    .auth-forgot {
      font-size: 12.5px;
      font-weight: 500;
      color: #7c3aed;
      text-decoration: none;
    }
    .auth-forgot:hover {
      text-decoration: underline;
    }
    .auth-input-wrap {
      position: relative;
    }
    .auth-input {
      width: 100%;
      height: 44px;
      padding: 0 14px;
      border: 1.5px solid #e2e8f0;
      border-radius: 8px;
      font-size: 14px;
      color: #1e293b;
      background-color: #ffffff;
      transition: border-color 0.15s ease, box-shadow 0.15s ease;
      outline: none;
    }
    .auth-input:focus {
      border-color: #7c3aed;
      box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.12);
    }
    .auth-input::placeholder {
      color: #94a3b8;
    }
    .auth-pwd-toggle {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #94a3b8;
      cursor: pointer;
      padding: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .auth-pwd-toggle:hover {
      color: #475569;
    }
    .auth-check-wrap {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 20px;
    }
    .auth-check-wrap input[type="checkbox"] {
      width: 16px;
      height: 16px;
      accent-color: #7c3aed;
      cursor: pointer;
    }
    .auth-check-wrap label {
      font-size: 13px;
      color: #64748b;
      margin: 0;
      cursor: pointer;
      user-select: none;
    }
    .auth-submit-btn {
      width: 100%;
      height: 46px;
      background: #7c3aed;
      border: none;
      border-radius: 8px;
      color: #ffffff;
      font-size: 14px;
      font-weight: 600;
      letter-spacing: 0.5px;
      cursor: pointer;
      transition: background-color 0.15s ease;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .auth-submit-btn:hover {
      background: #6d28d9;
    }
    .auth-alert {
      padding: 10px 14px;
      border-radius: 8px;
      font-size: 13px;
      margin-bottom: 16px;
    }
    .auth-alert-danger {
      background: #fef2f2;
      border: 1px solid #fee2e2;
      color: #991b1b;
    }
    .auth-alert-warning {
      background: #fffbeb;
      border: 1px solid #fef3c7;
      color: #92400e;
    }
    .auth-alert-success {
      background: #f0fdf4;
      border: 1px solid #dcfce7;
      color: #166534;
    }
    @media (max-width: 820px) {
      .auth-card {
        flex-direction: column-reverse;
      }
      .auth-image-side {
        padding: 24px 20px 0 20px;
      }
      .auth-image-side img {
        max-height: 220px;
      }
      .auth-form-side {
        padding: 24px 20px 32px 20px;
      }
    }
  </style>
</head>
<body class="login-page">
  <div class="auth-card">
    <div class="auth-form-side">
      <div class="auth-header">
        <h1>Login</h1>
        <p>Enter your credentials to access your account.</p>
      </div>

      <?php if(isset($_GET['conflict'])) { ?>
        <div class="auth-alert auth-alert-danger">
          <strong>Session Terminated:</strong> You have been logged out because another device logged into your account.
        </div>
      <?php } ?>

      <?php if(isset($_GET['timeout'])) { ?>
        <div class="auth-alert auth-alert-warning">
          Your session has expired due to 5 minutes of inactivity. Please log in again.
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

        <div class="auth-group">
          <div class="auth-group-header">
            <label class="auth-label">Email Address</label>
          </div>
          <div class="auth-input-wrap">
            <input type="text" class="auth-input" name="email" placeholder="you@example.com" required autocomplete="username">
          </div>
        </div>

        <div class="auth-group">
          <div class="auth-group-header">
            <label class="auth-label">Password</label>
            <a href="<?php echo base_url(); ?>manager/forgot" class="auth-forgot">Forgot Password?</a>
          </div>
          <div class="auth-input-wrap">
            <input type="password" class="auth-input" name="password" id="login_password" placeholder="Enter 6 character or more" required autocomplete="current-password" style="padding-right: 40px;">
            <button type="button" class="auth-pwd-toggle" id="btnToggleLoginPwd" title="Toggle password visibility">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
            </button>
          </div>
        </div>

        <div class="auth-check-wrap">
          <input type="checkbox" id="rememberMe" name="remember_me">
          <label for="rememberMe">Remember me</label>
        </div>

        <button type="submit" class="auth-submit-btn" id="loginBtn">LOGIN</button>

      </form>
    </div>

    <div class="auth-image-side">
      <img src="<?php echo base_url(); ?>assets/dist/img/login_illustration.png" alt="Login Illustration">
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
