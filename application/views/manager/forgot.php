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
  <meta property="og:title" content="<?php echo !empty($info['title']) ? htmlspecialchars($info['title']) : 'Seven Strokes CRM - Password Recovery'; ?>">
  <meta property="og:description" content="Seven Strokes CRM Platform">
  <meta property="og:image" content="<?php echo base_url(); ?>assets/logo.png">
  <meta property="og:type" content="website">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:image" content="<?php echo base_url(); ?>assets/logo.png">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/mos.css"> 
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/iCheck/square/blue.css">
</head>
<body class="login-page" style="height:auto">
  <div class="d-flex align-items-center m-height">
    <div class="login-box ">
      <div class="row"> 
        <div>
          <div class="login-box-body">
            <div class="login-logo mb-5">
              <a href="<?php echo base_url(); ?>"><img src="<?php echo base_url(); ?>assets/logo.png" alt="7 Strokes" style="max-height: 80px; max-width: 100%;"></a>
            </div>
            <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?>
            <form action="<?php echo base_url(); ?>manager/forgot" method="post">
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
              <div class="form-group has-feedback">
                <input type="text" class="form-control" name="email" placeholder="Enter Email" required>
              </div>
              <div class="row">
                <div class="col-xs-6">
                  <div class="text-left">
                    <button type="submit" class="btn btn-danger">Forgot</button>   
                  </div>   
                </div>
                <div class="col-xs-6">
                  <div class="text-right">
                    <a href="<?php echo base_url(); ?>manager/login" class="fpass">Login</a>
                  </div> 
                </div>
              </div>
            </form> 
          </div>      
        </div>
      </div>      
    </div>
  </div>
  <script src="<?php echo base_url(); ?>assets/plugins/jQuery/jquery-2.2.3.min.js"></script>
  <script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js"></script>
  <script src="<?php echo base_url(); ?>assets/plugins/iCheck/icheck.min.js"></script>
  <script>
    $(function () {
      $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%'
      });
    });
  </script>
</body>
</html>
