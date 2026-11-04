<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo !empty($info['title']) ? htmlspecialchars($info['title']) : 'Forgot Password - Seven Strokes CRM'; ?></title>
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
      margin-bottom: 20px;
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
      margin-top: 10px;
    }
    .auth-submit-btn:hover {
      background: #6d28d9;
    }
    .auth-back-link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      margin-top: 20px;
      font-size: 13px;
      font-weight: 600;
      color: #7c3aed;
      text-decoration: none;
    }
    .auth-back-link:hover {
      text-decoration: underline;
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
        padding: 24px;
      }
      .auth-image-side img {
        max-height: 220px;
      }
      .auth-form-side {
        padding: 30px 24px;
      }
    }
  </style>
</head>
<body class="login-page">
  <div class="auth-card">
    <div class="auth-form-side">
      <div class="auth-header">
        <h1>Forgot Password</h1>
        <p>Remember your password? <a href="<?php echo base_url(); ?>manager/login">Back to Login</a></p>
      </div>

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

        <div class="auth-group">
          <div class="auth-group-header">
            <label class="auth-label">Registered Email Address</label>
          </div>
          <div class="auth-input-wrap">
            <input type="email" class="auth-input" name="email" placeholder="you@example.com" required autocomplete="email">
          </div>
        </div>

        <button type="submit" class="auth-submit-btn" id="forgotBtn">RESET PASSWORD</button>

        <div style="text-align: center; margin-top: 18px;">
          <a href="<?php echo base_url(); ?>manager/login" class="auth-back-link">
            <i class="fa fa-arrow-left"></i> Return to Sign In
          </a>
        </div>
      </form>
    </div>

    <div class="auth-image-side">
      <img src="<?php echo base_url(); ?>assets/dist/img/login_illustration.png" alt="Password Recovery Illustration">
    </div>
  </div>
</body>
</html>
