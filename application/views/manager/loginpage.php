<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $info['title']; ?></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/7strokes-logo.png" type="image/png">
  <link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/7strokes-logo.png">
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
              <a href="<?php echo base_url(); ?>"><img src="<?php echo base_url(); ?>assets/7strokes-logo.png" style="max-height: 70px; max-width: 100%;"></a>
            </div>
            <?php if(isset($_GET['conflict'])) { ?>
              <div class="alert alert-danger" style="font-size: 13px; margin-bottom: 15px; border-radius: 4px;">
                <i class="fa fa-exclamation-triangle"></i> <strong>Session Terminated:</strong> You have been logged out because another device logged into your account. Only one device session is permitted at a time.
              </div>
            <?php } ?>
            <?php if(isset($_GET['timeout'])) { ?>
              <div class="alert alert-warning" style="font-size: 13px; margin-bottom: 15px; border-radius: 4px;">
                <i class="fa fa-clock-o"></i> Your session has expired due to 10 minutes of inactivity. Please log in again.
              </div>
            <?php } ?>
            <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> <?php echo get_message($this->session->flashdata('message'),'message'); ?>
            <form action="<?php echo base_url(); ?>manager/login" method="post">
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
              <div class="form-group has-feedback">
                <input type="text" class="form-control" name="email" placeholder="Enter Email" required>
              </div>
              <div class="form-group has-feedback">
                <input type="password" class="form-control" name="password" placeholder="Enter Password" required>
              </div>
              <div class="row">
                <div class="col-xs-6">
                  <div class="text-left">
                    <button type="submit" class="btn btn-danger">Sign In</button>   
                  </div>   
                </div>
                <div class="col-xs-6">
                  <div class="text-right">
                    <a href="<?php echo base_url(); ?>manager/forgot" class="fpass">Forgot Password?</a>
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
