<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
    
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url(); ?>assets/favicon.ico">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url(); ?>assets/favicon.ico">

    
<title><?php echo $info['title']; ?></title>
<!-- Tell the browser to be responsive to screen width -->
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/slimmenu.min.css" >   

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/font-awesome.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/ionicons.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/skins/_all-skins.min.css">

<script src="<?php echo base_url(); ?>assets/plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/jQueryUI/jquery-ui.js"></script>


<link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/jQueryUI/jquery-ui.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/bootstrap/spacing.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.bootstrap.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/dist/css/skins/_all-skins.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/dist/css/skins/skin-yellow.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/select2/select2.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/daterangepicker/daterangepicker.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/dist/css/AdminLTE.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/dist/css/x_grid.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/datepicker/datepicker3.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/timepicker/bootstrap-timepicker.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/iCheck/all.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/iCheck/flat/blue.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/morris/morris.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/datatables/dataTables.bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.2.7/css/rowReorder.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.5/css/responsive.dataTables.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/bootstrap/developer.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/multiselect/multiselect.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/dist/css/crm-dashboard.css">

<script src="<?php echo base_url(); ?>assets/plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/jQueryUI/jquery-ui.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/plugins/table_export/css/tableexport.css" />
<script src="<?php echo base_url();?>assets/dist/js/jquery-1.10.2.min.js"></script>

<!-- Table export -->
<script src="<?php echo base_url();?>assets/plugins/table_export/FileSaver.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/table_export/Blob.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/table_export/xls.core.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/table_export/js/tableexport.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/jQueryUI/jquery-ui.css">
<script src="<?php echo base_url();?>assets/bootstrap/js/jquery.validate.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/bootstrap/css/bootstrap-toggle.min.css">
<script src="<?php echo base_url();?>assets/bootstrap/js/bootstrap-toggle.min.js"></script>

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

<header class="main-header"> 

<div class="container">
<div class="x_row align-items-center">
<div class="x_col-12 x_col-md-4 text-center text-md-left">
<div class="main-logo">
<a href="<?php echo base_url(); ?>manager/dashboard"> <span class="logo-lg"> <img src="https://crm.rolextogroup.com/assets/dist/img/manager-logo-new.png" height=""/> </span> <span class="logo-mini">SRJ</span> </a> 
</div>
</div>
<div class="X_col-12 x_col-md-8 ">
  
<div class="head-co text-center text-md-left">
<p><a href="mailto:hr@rolextogroup.com"><i class="fa fa-envelope" aria-hidden="true"></i> hr@rolextogroup.com</a> | <a href="tel:+971585637737"><i class="fa fa-phone-square" aria-hidden="true"></i> +971 58 563 7737</a></p>
<p> <strong> Hello, Rolexto Admin<?php //echo $this->session->userdata['manager_name']; ?></strong></p>


</div>
  
</div>
</div>
</div>



  
  <!-- Header Navbar: style can be found in header.less -->
  
  <nav class="navbar navbar-static-top"> 
    
    <!-- Sidebar toggle button--> 
    
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button"> <span class="sr-only">Toggle navigation</span> </a> 
  
  
    <ul class="munk_menu">
      <?php  $route1=$this->uri->segment(2);
	  $route2=$this->uri->segment(3);?>
      <li class="<?php if($route1=="dashboard"){ echo "active";}else{ echo "";}?>"> <a href="<?php echo base_url();?>manager/dashboard"> <i class="fa fa-tachometer"></i> <span>Dashboard</span>  </a> </li>


     <?php if($this->session->userdata['manager_role']==1){ ?>


     <li class="<?php if($route1=="users"){ echo "active";}else{ echo "";}?> "> <a href="#"><i class="fa fa-users"></i> <span>Users</span> </a>
        <ul>
          <li><a href="<?php echo base_url();?>manager/users/add"><i class="fa fa-plus"></i>Add </a></li>
          <li><a href="<?php echo base_url();?>manager/users"><i class="fa fa-list"></i>View</a></li>
          <?php /* <li><a href="<?php echo base_url();?>manager/users/report"><i class="fa fa-list"></i>Report</a></li> */ ?>
        </ul>
      </li>

      


   <?php }   ?>




      <li class="<?php if($route1=="leads"){ echo "active";}else{ echo "";}?>"> <a href="#"><i class="fa fa-list"></i> <span>Leads</span> </a>
        <ul>
          <li><a href="<?php echo base_url();?>manager/leads/add"><i class="fa fa-plus"></i>Add </a></li>
          <li><a href="<?php echo base_url();?>manager/leads"><i class="fa fa-list"></i>View</a></li>
          <?php /* <li><a href="<?php echo base_url();?>manager/leads/report"><i class="fa fa-list"></i>Report</a></li> */ ?>
          <li><a href="<?php echo base_url();?>manager/leads/followups"><i class="fa fa-list"></i>Follow up</a></li>
          <li><a href="<?php echo base_url();?>manager/leads/meetings"><i class="fa fa-list"></i>Meetings</a></li>
          <li><a href="<?php echo base_url();?>manager/performance-report/individual"><i class="fa fa-list"></i>Performance Report</a></li>          
        </ul>
      </li>


      
    <li class="<?php if($route1=="users"){ echo "active";}else{ echo "";}?> "> <a href="#"><i class="fa fa-users"></i> <span>Verticals</span> </a>
        <ul>
          <li><a href="<?php echo base_url();?>manager/verticals/add"><i class="fa fa-plus"></i>Add </a></li>
          <li><a href="<?php echo base_url();?>manager/verticals"><i class="fa fa-list"></i>View</a></li>
          <li><a href="<?php echo base_url();?>manager/verticals"><i class="fa fa-list"></i>Follow up</a></li>
          <?php /*<li><a href="<?php echo base_url();?>manager/verticals/report"><i class="fa fa-list"></i>Report</a></li> */ ?>
        </ul>
      </li>


      <li class="<?php if($route1=="team"){ echo "active";}else{ echo "";}?>"> <a href="#"><i class="fa fa-list"></i> <span>Team</span> </a>
        <ul>
          <li><a href="<?php echo base_url();?>manager/team/members"><i class="fa fa-plus"></i>Members </a></li>
          <li><a href="<?php echo base_url();?>manager/team/chart"><i class="fa fa-list"></i>Chart</a></li>
          <li><a href="<?php echo base_url();?>manager/team/leads"><i class="fa fa-list"></i>Leads</a></li>
          <li><a href="<?php echo base_url();?>manager/team/verticals"><i class="fa fa-list"></i>Verticals</a></li>
          <li><a href="<?php echo base_url();?>manager/team/followups"><i class="fa fa-list"></i>Follow up</a></li>
          <li><a href="<?php echo base_url();?>manager/team/meetings"><i class="fa fa-list"></i>Meetings</a></li>
          <li><a href="<?php echo base_url();?>manager/team/assignmeetings"><i class="fa fa-list"></i>Assigned Meetings</a></li>
          <li><a href="<?php echo base_url();?>manager/team/assignleads"><i class="fa fa-list"></i>Assigned Leads</a></li>
          <li><a href="<?php echo base_url();?>manager/performance-report"><i class="fa fa-list"></i>Performance Report</a></li>

        </ul>
      </li>
     

     <?php if($this->session->userdata['manager_role']==1){ ?>
     

      <li class="<?php if($route1=="roles"){ echo "active";}else{ echo "";}?>"> <a href="#"><i class="fa fa-paperclip"></i> <span>System</span> </a>
        <ul>        
          <li><a href="<?php echo base_url();?>manager/roles"><i class="fa fa-list"></i>Roles</a></li>
          <li><a href="<?php echo base_url();?>manager/terms"><i class="fa fa-list"></i>Category</a></li>
        </ul>
      </li>
     
      <?php } ?>
   
      
      
      <li class="<?php if($route1=="profile"){ echo "active";}else{ echo "";}?>"> <a href="#"> <i class="fa fa-user" aria-hidden="true"></i><span>Profile</span></a>
      <ul>        
          <li><a href="<?php echo base_url();?>manager/profile"><i class="fa fa-eye"></i>View Profile</a></li>
          <li><a href="<?php echo base_url();?>manager/password"><i class="fa fa-list"></i>Change Password</a></li>
        </ul>
         </li>

    
      <li> <a class="logout" href="#"> <i class="fa fa-power-off"></i> <span>Logout</span></a> </li>

    </ul>

  </nav>
  
 
</header>
<div class="clearfix"></div>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar"> 
  
  <!-- sidebar: style can be found in sidebar.less -->
  
  <section class="sidebar">
    <ul class="sidebar-menu">
      <?php  $route1=$this->uri->segment(2);
	  $route2=$this->uri->segment(3);?>
      <li class="<?php if($route1=="dashboard"){ echo "active";}else{ echo "";}?>"> <a href="<?php echo base_url();?>manager/dashboard"> <i class="fa fa-tachometer"></i> <span>Dashboard</span> <span class="pull-right-container"> <small class="label pull-right"></small> </span> </a> </li>


     <?php if($this->session->userdata['manager_role']==1){ ?>


     <li class="<?php if($route1=="users"){ echo "active";}else{ echo "";}?> treeview"> <a href="#"><i class="fa fa-users"></i> <span>Users</span> <span class="pull-right-container"> <span class="label label-primary pull-right"></span> </span> </a>
        <ul class="treeview-menu">
        <li><a href="<?php echo base_url();?>manager/users/add"><i class="fa fa-plus"></i>Add </a></li>
          <li><a href="<?php echo base_url();?>manager/users"><i class="fa fa-list"></i>View</a></li>
          <?php /* <li><a href="<?php echo base_url();?>manager/users/report"><i class="fa fa-list"></i>Report</a></li> */ ?>
        </ul>
      </li>

   <?php }   ?>

      <li class="<?php if($route1=="leads"){ echo "active";}else{ echo "";}?> treeview"> <a href="#"><i class="fa fa-database"></i> <span>Leads</span> <span class="pull-right-container"> <span class="label label-primary pull-right"></span> </span> </a>
        <ul class="treeview-menu">
        <li><a href="<?php echo base_url();?>manager/leads/add"><i class="fa fa-plus"></i>Add </a></li>
          <li><a href="<?php echo base_url();?>manager/leads"><i class="fa fa-list"></i>View</a></li>
          <?php /* <li><a href="<?php echo base_url();?>manager/leads/report"><i class="fa fa-list"></i>Report</a></li> */ ?>
          <li><a href="<?php echo base_url();?>manager/leads/followups"><i class="fa fa-list"></i>Follow up</a></li>
          <li><a href="<?php echo base_url();?>manager/leads/meetings"><i class="fa fa-list"></i>Meetings</a></li>
          <li><a href="<?php echo base_url();?>manager/performance-report/individual"><i class="fa fa-list"></i>Performance Report</a></li>          
        </ul>
      </li>

      
    <li class="<?php if($route1=="users"){ echo "active";}else{ echo "";}?> treeview"> <a href="#"><i class="fa fa-users"></i> <span>Verticals</span> </a>
        <ul class="treeview-menu">
          <li><a href="<?php echo base_url();?>manager/verticals/add"><i class="fa fa-plus"></i>Add </a></li>
          <li><a href="<?php echo base_url();?>manager/verticals"><i class="fa fa-list"></i>View</a></li>
          <?php /*<li><a href="<?php echo base_url();?>manager/verticals/report"><i class="fa fa-list"></i>Report</a></li> */ ?>
        </ul>
      </li>

      <li class="<?php if($route1=="team"){ echo "active";}else{ echo "";}?> treeview"> <a href="#"><i class="fa fa-list"></i> <span>Team</span> </a>
        <ul class="treeview-menu">
        <li><a href="<?php echo base_url();?>manager/team/members"><i class="fa fa-plus"></i>Members </a></li>
          <li><a href="<?php echo base_url();?>manager/team/chart"><i class="fa fa-list"></i>Chart</a></li>
          <li><a href="<?php echo base_url();?>manager/team/leads"><i class="fa fa-list"></i>Leads</a></li>
          <li><a href="<?php echo base_url();?>manager/team/verticals"><i class="fa fa-list"></i>Verticals</a></li>
          <li><a href="<?php echo base_url();?>manager/team/followups"><i class="fa fa-list"></i>Follow up</a></li>
          <li><a href="<?php echo base_url();?>manager/team/meetings"><i class="fa fa-list"></i>Meetings</a></li>
          <li><a href="<?php echo base_url();?>manager/team/assignmeetings"><i class="fa fa-list"></i>Assigned Meetings</a></li>
          <li><a href="<?php echo base_url();?>manager/performance-report"><i class="fa fa-list"></i>Performance Report</a></li>
          
        </ul>
      </li>

     <?php if($this->session->userdata['manager_role']==1){ ?>
     

      <li class="<?php if($route1=="roles"){ echo "active";}else{ echo "";}?> treeview"> <a href="#"><i class="fa fa-paperclip"></i> <span>System</span> <span class="pull-right-container"> <span class="label label-primary pull-right"></span> </span> </a>
        <ul class="treeview-menu">        
        <li><a href="<?php echo base_url();?>manager/roles"><i class="fa fa-list"></i>Roles</a></li>
          <li><a href="<?php echo base_url();?>manager/terms"><i class="fa fa-list"></i>Category</a></li>
        </ul>
      </li>
     
      <?php } ?>
   
  
      <li class="<?php if($route1=="profile"){ echo "active";}else{ echo "";}?> treeview"> <a href="#"> <i class="fa fa-cog" aria-hidden="true"></i><span>Profile</span></a>
      <ul class="treeview-menu">        
          <li><a href="<?php echo base_url();?>manager/profile"><i class="fa fa-eye"></i>View Profile</a></li>
          <li><a href="<?php echo base_url();?>manager/password"><i class="fa fa-exchange"></i>Change Password</a></li>
        </ul>
         </li>
    
      <li> <a href="<?php echo base_url();?>manager/logout"> <i class="fa fa-power-off"></i> <span>Logout</span> <span class="pull-right-container"> <small class="label pull-right"></small> </span> </a> </li>

    </ul>
  </section>
  
  <!-- /.sidebar --> 
  
</aside>
