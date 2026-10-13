<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $info['title']; ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        
<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/7strokes-logo.png" type="image/png">
<link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/7strokes-logo.png">
    
    
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css">
  
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/mos.css"> 
    
  <!-- iCheck -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/iCheck/square/blue.css">
    
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  
</head>
    
    
<body class="login-page" style="height:auto">


  <!-- /.login-logo -->
    
    
  <div class="d-flex align-items-center m-height">

    <div class="login-box ">
        
    <div class="row"> 
 <div class="">
     <div class="login-box-body">
     <div class="login-logo mb-5">
    <a href="<?php echo base_url(); ?>"><img src="<?php echo base_url(); ?>assets/7strokes-logo.png" style="max-height: 70px; max-width: 100%;"></a>
  </div>
 
     <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> <?php echo get_message($this->session->flashdata('message'),'message'); ?>
    <form action="<?php echo base_url(); ?>manager/login" method="post">
      <div class="form-group has-feedback">
        <input type="text" class="form-control" name="email" placeholder="Enter Email">
        <!-- <span class="glyphicon glyphicon-envelope form-control-feedback"></span> -->
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" name="password" placeholder="Enter Password">
        <!-- <span class="glyphicon glyphicon-lock form-control-feedback"></span> -->
      </div>
 
 <div class="row">
 <div class="col-xs-6">
 <div class="text-left">
        <button type="submit" class="btn btn-danger">Sign In</button>   
     </div>   
 </div>
 <div class="col-xs-6">
 <div class="text-right ">
   <a href="<?php echo base_url(); ?>manager/forgot" class="fpass">Forgot Password?</a>
   </div> 
 </div>
 </div>
 


         
      </form> 
       
 </div>      
    </div>
         
    </div>      
 
  <!-- /.login-box-body -->
</div>
</div>
<!-- /.login-box -->
<!-- jQuery 2.2.3 -->
<script src="<?php echo base_url(); ?>assets/plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="<?php echo base_url(); ?>assets/plugins/iCheck/icheck.min.js"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });
</script>
</body>
</html>
