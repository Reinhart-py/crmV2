<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
    
<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/logo-7strokes.png" type="image/png">
<link rel="icon" type="image/png" sizes="64x64" href="<?php echo base_url(); ?>assets/logo-7strokes.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url(); ?>assets/logo-7strokes.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url(); ?>assets/logo-7strokes.png">
<link rel="apple-touch-icon" href="<?php echo base_url(); ?>assets/logo-7strokes.png">

    
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
<script>
(function() {
    var theme = localStorage.getItem('crm_theme_preference');
    if (theme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
    }
})();
</script>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

<header class="main-header"> 

<div class="main-header-bar">
  <!-- 3-line hamburger bar button for Android only (strictly hidden on desktop) -->
  <button type="button" class="crm-mobile-hamburger" id="crmMobileHamburger" aria-label="Toggle navigation">
    <i class="fa fa-bars"></i>
  </button>

  <div class="main-logo">
    <a href="<?php echo base_url(); ?>manager/dashboard"> 
      <img src="<?php echo base_url(); ?>assets/logo-7strokes.png" alt="7 Strokes" />
    </a> 
  </div>

  <!-- Header Navbar -->
  <nav class="navbar navbar-static-top"> 
    <ul class="munk_menu">
      <?php  
        $route1=$this->uri->segment(2);
	      $route2=$this->uri->segment(3);
        $m_role = (int)$this->session->userdata('manager_role');
        if (empty($m_role) && $this->session->userdata('manager_id') == 1) {
            $m_role = 1;
        }
      ?>
      <li class="<?php if($route1=="dashboard"){ echo "active";}else{ echo "";}?>"> <a href="<?php echo base_url();?>manager/dashboard"> <i class="fa fa-tachometer"></i> <span>Dashboard</span>  </a> </li>


      <li class="<?php if(in_array($route1, array('tasks', 'tickets', 'operations'))){ echo "active";}else{ echo "";}?>"> <a href="javascript:void(0);"><i class="fa fa-cogs"></i> <span>Operations</span> </a>
        <ul>
          <li><a href="<?php echo base_url();?>manager/tasks/add"><i class="fa fa-plus"></i>Add Task</a></li>
          <li><a href="<?php echo base_url();?>manager/tasks"><i class="fa fa-list"></i>Task View</a></li>
          <li><a href="<?php echo base_url();?>manager/tasks/followups"><i class="fa fa-calendar-check-o"></i>Task Follow-up</a></li>
          <li><a href="<?php echo base_url();?>manager/tickets/add"><i class="fa fa-plus"></i>Raise Ticket</a></li>
          <li><a href="<?php echo base_url();?>manager/tickets"><i class="fa fa-ticket"></i>Ticket View</a></li>
          <li><a href="<?php echo base_url();?>manager/tickets/followups"><i class="fa fa-calendar-check-o"></i>Open Tickets</a></li>
        </ul>
      </li>

      <?php if($m_role == 1 || $m_role == 2 || $m_role == 4){ ?>
      <li class="<?php if($route1=="kam"){ echo "active";}else{ echo "";}?>"> <a href="javascript:void(0);"><i class="fa fa-briefcase"></i> <span>KAM</span> </a>
        <ul>
          <li><a href="<?php echo base_url();?>manager/kam/my-client"><i class="fa fa-user"></i>My Client</a></li>
          <?php if($m_role == 1 || $m_role == 4){ ?>
          <li><a href="<?php echo base_url();?>manager/kam/onboard"><i class="fa fa-user-plus"></i>Onboard Client</a></li>
          <li><a href="<?php echo base_url();?>manager/kam/schedule"><i class="fa fa-calendar"></i>Create Schedule</a></li>
          <li><a href="<?php echo base_url();?>manager/kam/report"><i class="fa fa-bar-chart"></i>Report</a></li>
          <?php } ?>
        </ul>
      </li>
      <?php } ?>

      <li class="<?php if($route1=="leads"){ echo "active";}else{ echo "";}?>"> <a href="<?php echo base_url();?>manager/leads"><i class="fa fa-list"></i> <span>Leads</span> </a>
        <ul>
          <li><a href="<?php echo base_url();?>manager/leads/add"><i class="fa fa-plus"></i>Add Lead</a></li>
          <li><a href="<?php echo base_url();?>manager/leads"><i class="fa fa-list"></i>View Leads</a></li>
          <li><a href="<?php echo base_url();?>manager/leads/followups"><i class="fa fa-calendar-check-o"></i>Follow-up Meetings</a></li>
        </ul>
      </li>


      
    <li class="<?php if($route1=="verticals"){ echo "active";}else{ echo "";}?> "> <a href="<?php echo base_url();?>manager/verticals"><i class="fa fa-users"></i> <span>Verticals</span> </a>
        <ul>
          <li><a href="<?php echo base_url();?>manager/verticals/add"><i class="fa fa-plus"></i>Add </a></li>
          <li><a href="<?php echo base_url();?>manager/verticals"><i class="fa fa-list"></i>View</a></li>
          <li><a href="<?php echo base_url();?>manager/verticals"><i class="fa fa-list"></i>Follow up</a></li>
        </ul>
      </li>


      <li class="<?php if($route1=="team"){ echo "active";}else{ echo "";}?>"> <a href="<?php echo base_url();?>manager/team/members"><i class="fa fa-list"></i> <span>Team</span> </a>
        <ul>
          <li><a href="<?php echo base_url();?>manager/team/members"><i class="fa fa-plus"></i>Members </a></li>
          <li><a href="<?php echo base_url();?>manager/team/chart"><i class="fa fa-list"></i>Chart</a></li>
          <li><a href="<?php echo base_url();?>manager/team/leads"><i class="fa fa-list"></i>Leads</a></li>
          <li><a href="<?php echo base_url();?>manager/team/verticals"><i class="fa fa-list"></i>Verticals</a></li>
          <li><a href="<?php echo base_url();?>manager/team/followups"><i class="fa fa-list"></i>Follow up</a></li>
          <li><a href="<?php echo base_url();?>manager/team/meetings"><i class="fa fa-list"></i>Meetings</a></li>
          <li><a href="<?php echo base_url();?>manager/team/assignmeetings"><i class="fa fa-list"></i>Assigned Meetings</a></li>
          <li><a href="<?php echo base_url();?>manager/team/assignleads"><i class="fa fa-list"></i>Assigned Leads</a></li>
          <li><a href="<?php echo base_url();?>manager/tasks"><i class="fa fa-tasks"></i>Task</a></li>
          <li><a href="<?php echo base_url();?>manager/team/tickets"><i class="fa fa-ticket"></i>Tickets</a></li>
        </ul>
      </li>

      <li class="<?php if($route1=="reports" || $route1=="download-report" || $route1=="admin-reports"){ echo "active";}else{ echo "";}?>"> <a href="#"><i class="fa fa-bar-chart"></i> <span>Reports</span> </a>
        <ul>
          <li><a href="https://7strokes.maxvendor47.workers.dev/" target="_blank"><i class="fa fa-database"></i>Generate Data</a></li>
          <li><a href="<?php echo base_url();?>manager/download-report"><i class="fa fa-file-pdf-o"></i>Reports</a></li>
        </ul>
      </li>
     

     <?php 
       if($m_role == 1 || $m_role == 4){ 
     ?>
      <li class="<?php if($route1=="roles" || $route1=="terms" || $route1=="users" || $route1=="assigned-leads" || $route1=="admin-logs" || ($route1=="leads" && in_array($route2, array("import", "export")))){ echo "active";}else{ echo "";}?>"> 
        <a href="<?php echo ($m_role == 1)? base_url().'manager/roles' : base_url().'manager/leads/import'; ?>"><i class="fa fa-paperclip"></i> <span>System</span> </a>
        <ul>        
          <?php if($m_role == 1){ ?>
            <li><a href="<?php echo base_url();?>manager/users/add"><i class="fa fa-plus"></i>User Add</a></li>
            <li><a href="<?php echo base_url();?>manager/users"><i class="fa fa-list"></i>User View</a></li>
            <li><a href="<?php echo base_url();?>manager/roles"><i class="fa fa-list"></i>Roles</a></li>
            <li><a href="<?php echo base_url();?>manager/terms"><i class="fa fa-list"></i>Category</a></li>
          <?php } ?>
          <li><a href="<?php echo base_url();?>manager/leads/import"><i class="fa fa-upload"></i>Import Leads</a></li>
          <?php if($m_role == 1){ ?>
            <li><a href="<?php echo base_url();?>manager/leads/export"><i class="fa fa-download"></i>Export Leads</a></li>
            <li><a href="<?php echo base_url();?>manager/assigned-leads"><i class="fa fa-tasks"></i>Assigned Leads</a></li>
            <li><a href="<?php echo base_url();?>manager/admin-logs"><i class="fa fa-history"></i>Admin Logs</a></li>
          <?php } ?>
        </ul>
      </li>
      <?php } ?>
   
      
      
      <li class="<?php if($route1=="profile"){ echo "active";}else{ echo "";}?>"> <a href="<?php echo base_url();?>manager/profile"> <i class="fa fa-user" aria-hidden="true"></i><span>Profile</span></a>
      <ul>        
          <li><a href="<?php echo base_url();?>manager/profile"><i class="fa fa-eye"></i>View Profile</a></li>
          <li><a href="<?php echo base_url();?>manager/password"><i class="fa fa-list"></i>Change Password</a></li>
        </ul>
         </li>


      <li> <a class="logout" href="#"> <i class="fa fa-power-off"></i> <span>Logout</span></a> </li>

    </ul>
  </nav>

  <!-- Global Header Quick Actions: Dynamic User Greeting + Dark/Light Mode Toggle -->
  <div class="header-quick-actions">
    <span class="header-user-greeting">Hi, <?php 
      $current_user_id = $this->session->userdata('manager_id');
      $current_user_name = '';
      if ($current_user_id) {
        $u_row = $this->db->select('name')->get_where('ci_user', array('id' => $current_user_id))->row_array();
        if (!empty($u_row['name'])) {
          $current_user_name = $u_row['name'];
        }
      }
      if (empty($current_user_name)) {
        $current_user_name = $this->session->userdata('manager_name');
      }
      echo !empty($current_user_name) ? htmlspecialchars($current_user_name) : 'User';
    ?></span>
    <button type="button" class="global-theme-btn" id="globalThemeToggle" title="Toggle Dark/Light Mode">
      <i class="fa fa-moon-o"></i>
    </button>
  </div>
</div>

</header>

<script>
$(function() {
  function applyCrmTheme(t) {
    if (t === 'dark') {
      $('html, body, .main-header').attr('data-theme', 'dark');
      $('#globalThemeToggle').html('<i class="fa fa-sun-o"></i>');
      $('#dashThemeToggle').html('<i class="fa fa-sun-o"></i> <span>Light Mode</span>');
      localStorage.setItem('crm_theme_preference', 'dark');
    } else {
      $('html, body, .main-header').removeAttr('data-theme');
      $('#globalThemeToggle').html('<i class="fa fa-moon-o"></i>');
      $('#dashThemeToggle').html('<i class="fa fa-moon-o"></i> <span>Dark Mode</span>');
      localStorage.setItem('crm_theme_preference', 'light');
    }
  }

  var curTheme = localStorage.getItem('crm_theme_preference') || 'light';
  applyCrmTheme(curTheme);

  $(document).on('click', '#globalThemeToggle', function(e) {
    e.preventDefault();
    var current = localStorage.getItem('crm_theme_preference') || 'light';
    var nextTheme = (current === 'dark') ? 'light' : 'dark';
    applyCrmTheme(nextTheme);
  });
});
</script>
<div class="clearfix"></div>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar"> 
  
  <div class="crm-sidebar-header">
    <img src="<?php echo base_url(); ?>assets/logo-7strokes.png" alt="7 Strokes" />
    <button type="button" class="crm-sidebar-close" id="crmSidebarClose" aria-label="Close navigation">&times;</button>
  </div>
  
  <!-- sidebar: style can be found in sidebar.less -->
  
  <section class="sidebar">
    <ul class="sidebar-menu">
      <?php  
        $route1=$this->uri->segment(2);
	      $route2=$this->uri->segment(3);
        $m_role = $this->session->userdata['manager_role'];
      ?>
      <li class="<?php if($route1=="dashboard"){ echo "active";}else{ echo "";}?>"> 
        <a href="<?php echo base_url();?>manager/dashboard"> <i class="fa fa-tachometer"></i> <span>Dashboard</span> </a> 
      </li>


      <li class="<?php if(in_array($route1, array('tasks', 'tickets', 'operations'))){ echo "active";}else{ echo "";}?> treeview"> 
        <a href="javascript:void(0);"><i class="fa fa-cogs"></i> <span>Operations</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right sidebar-arrow"></i></span> </a>
        <ul class="treeview-menu">
          <li><a href="<?php echo base_url();?>manager/tasks/add"><i class="fa fa-plus"></i>Add Task</a></li>
          <li><a href="<?php echo base_url();?>manager/tasks"><i class="fa fa-list"></i>Task View</a></li>
          <li><a href="<?php echo base_url();?>manager/tasks/followups"><i class="fa fa-calendar-check-o"></i>Task Follow-up</a></li>
          <li><a href="<?php echo base_url();?>manager/tickets/add"><i class="fa fa-plus"></i>Raise Ticket</a></li>
          <li><a href="<?php echo base_url();?>manager/tickets"><i class="fa fa-ticket"></i>Ticket View</a></li>
          <li><a href="<?php echo base_url();?>manager/tickets/followups"><i class="fa fa-calendar-check-o"></i>Open Tickets</a></li>
        </ul>
      </li>

      <?php if($m_role == 1 || $m_role == 2 || $m_role == 4){ ?>
      <li class="<?php if($route1=="kam"){ echo "active";}else{ echo "";}?> treeview"> 
        <a href="javascript:void(0);"><i class="fa fa-briefcase"></i> <span>KAM</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right sidebar-arrow"></i></span> </a>
        <ul class="treeview-menu">
          <li><a href="<?php echo base_url();?>manager/kam/my-client"><i class="fa fa-user"></i>My Client</a></li>
          <?php if($m_role == 1 || $m_role == 4){ ?>
          <li><a href="<?php echo base_url();?>manager/kam/onboard"><i class="fa fa-user-plus"></i>Onboard Client</a></li>
          <li><a href="<?php echo base_url();?>manager/kam/schedule"><i class="fa fa-calendar"></i>Create Schedule</a></li>
          <li><a href="<?php echo base_url();?>manager/kam/report"><i class="fa fa-bar-chart"></i>Report</a></li>
          <?php } ?>
        </ul>
      </li>
      <?php } ?>

      <li class="<?php if($route1=="leads"){ echo "active";}else{ echo "";}?> treeview"> 
        <a href="javascript:void(0);"><i class="fa fa-database"></i> <span>Leads</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right sidebar-arrow"></i></span> </a>
        <ul class="treeview-menu">
          <li><a href="<?php echo base_url();?>manager/leads/add"><i class="fa fa-plus"></i>Add Lead</a></li>
          <li><a href="<?php echo base_url();?>manager/leads"><i class="fa fa-list"></i>View Leads</a></li>
          <li><a href="<?php echo base_url();?>manager/leads/followups"><i class="fa fa-calendar-check-o"></i>Follow-up Meetings</a></li>
        </ul>
      </li>

      <li class="<?php if($route1=="verticals"){ echo "active";}else{ echo "";}?> treeview"> 
        <a href="javascript:void(0);"><i class="fa fa-users"></i> <span>Verticals</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right sidebar-arrow"></i></span> </a>
        <ul class="treeview-menu">
          <li><a href="<?php echo base_url();?>manager/verticals/add"><i class="fa fa-plus"></i>Add </a></li>
          <li><a href="<?php echo base_url();?>manager/verticals"><i class="fa fa-list"></i>View</a></li>
          <li><a href="<?php echo base_url();?>manager/verticals"><i class="fa fa-list"></i>Follow up</a></li>
        </ul>
      </li>

      <li class="<?php if($route1=="team"){ echo "active";}else{ echo "";}?> treeview"> 
        <a href="javascript:void(0);"><i class="fa fa-list"></i> <span>Team</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right sidebar-arrow"></i></span> </a>
        <ul class="treeview-menu">
          <li><a href="<?php echo base_url();?>manager/team/members"><i class="fa fa-plus"></i>Members </a></li>
          <li><a href="<?php echo base_url();?>manager/team/chart"><i class="fa fa-list"></i>Chart</a></li>
          <li><a href="<?php echo base_url();?>manager/team/leads"><i class="fa fa-list"></i>Leads</a></li>
          <li><a href="<?php echo base_url();?>manager/team/verticals"><i class="fa fa-list"></i>Verticals</a></li>
          <li><a href="<?php echo base_url();?>manager/team/followups"><i class="fa fa-list"></i>Follow up</a></li>
          <li><a href="<?php echo base_url();?>manager/team/meetings"><i class="fa fa-list"></i>Meetings</a></li>
          <li><a href="<?php echo base_url();?>manager/team/assignmeetings"><i class="fa fa-list"></i>Assigned Meetings</a></li>
          <li><a href="<?php echo base_url();?>manager/team/assignleads"><i class="fa fa-list"></i>Assigned Leads</a></li>
          <li><a href="<?php echo base_url();?>manager/tasks"><i class="fa fa-tasks"></i>Task</a></li>
          <li><a href="<?php echo base_url();?>manager/team/tickets"><i class="fa fa-ticket"></i>Tickets</a></li>
        </ul>
      </li>

      <li class="<?php if($route1=="reports" || $route1=="download-report" || $route1=="admin-reports"){ echo "active";}else{ echo "";}?> treeview"> 
        <a href="javascript:void(0);"><i class="fa fa-bar-chart"></i> <span>Reports</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right sidebar-arrow"></i></span> </a>
        <ul class="treeview-menu">
          <li><a href="https://7strokes.maxvendor47.workers.dev/" target="_blank"><i class="fa fa-database"></i>Generate Data</a></li>
          <li><a href="<?php echo base_url();?>manager/download-report"><i class="fa fa-file-pdf-o"></i>Reports</a></li>
        </ul>
      </li>

     <?php if($m_role == 1 || $m_role == 4){ ?>
      <li class="<?php if($route1=="roles" || $route1=="terms" || $route1=="users" || $route1=="assigned-leads" || $route1=="admin-logs" || ($route1=="leads" && in_array($route2, array("import", "export")))){ echo "active";}else{ echo "";}?> treeview"> 
        <a href="javascript:void(0);"><i class="fa fa-paperclip"></i> <span>System</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right sidebar-arrow"></i></span> </a>
        <ul class="treeview-menu">        
          <?php if($m_role == 1){ ?>
            <li><a href="<?php echo base_url();?>manager/users/add"><i class="fa fa-plus"></i>User Add</a></li>
            <li><a href="<?php echo base_url();?>manager/users"><i class="fa fa-list"></i>User View</a></li>
            <li><a href="<?php echo base_url();?>manager/roles"><i class="fa fa-list"></i>Roles</a></li>
            <li><a href="<?php echo base_url();?>manager/terms"><i class="fa fa-list"></i>Category</a></li>
          <?php } ?>
          <li><a href="<?php echo base_url();?>manager/leads/import"><i class="fa fa-upload"></i>Import Leads</a></li>
          <?php if($m_role == 1){ ?>
            <li><a href="<?php echo base_url();?>manager/leads/export"><i class="fa fa-download"></i>Export Leads</a></li>
            <li><a href="<?php echo base_url();?>manager/assigned-leads"><i class="fa fa-tasks"></i>Assigned Leads</a></li>
            <li><a href="<?php echo base_url();?>manager/admin-logs"><i class="fa fa-history"></i>Admin Logs</a></li>
          <?php } ?>
        </ul>
      </li>
     <?php } ?>

      <li class="<?php if($route1=="profile"){ echo "active";}else{ echo "";}?> treeview"> 
        <a href="javascript:void(0);"> <i class="fa fa-cog" aria-hidden="true"></i><span>Profile</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right sidebar-arrow"></i></span></a>
        <ul class="treeview-menu">        
          <li><a href="<?php echo base_url();?>manager/profile"><i class="fa fa-eye"></i>View Profile</a></li>
          <li><a href="<?php echo base_url();?>manager/password"><i class="fa fa-exchange"></i>Change Password</a></li>
        </ul>
      </li>
    
      <li> 
        <a class="logout" href="#"> <i class="fa fa-power-off"></i> <span>Logout</span> </a> 
      </li>

    </ul>
  </section>
  
  <!-- /.sidebar --> 
  
</aside>
