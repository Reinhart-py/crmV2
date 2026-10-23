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
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/plugins/table_export/css/tableexport.css" />

<script src="<?php echo base_url();?>assets/plugins/table_export/FileSaver.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/table_export/Blob.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/table_export/xls.core.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/table_export/js/tableexport.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/jQueryUI/jquery-ui.css">
<script src="<?php echo base_url();?>assets/bootstrap/js/jquery.validate.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/bootstrap/css/bootstrap-toggle.min.css">
<script src="<?php echo base_url();?>assets/bootstrap/js/bootstrap-toggle.min.js"></script>




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
  
  <button type="button" class="crm-mobile-hamburger" id="crmMobileHamburger" aria-label="Toggle navigation">
    <i class="fa fa-bars"></i>
  </button>

  <div class="main-logo">
    <a href="<?php echo base_url(); ?>manager/dashboard"> 
      <img src="<?php echo base_url(); ?>assets/logo-7strokes.png" alt="7 Strokes" />
    </a> 
  </div>

  
  <nav class="navbar navbar-static-top"> 
    <ul class="munk_menu">
      <?php  
        $route1=$this->uri->segment(2);
	      $route2=$this->uri->segment(3);
        $m_role = (int)$this->session->userdata('manager_role');
        if (empty($m_role) && $this->session->userdata('manager_id') == 1) {
            $m_role = 1;
        }

        $current_user_id = (int)$this->session->userdata('manager_id');
        $current_user_name = '';
        $current_user_gender = '';
        $current_user_email = '';
        if ($current_user_id) {
          $u_row = $this->db->select('name, gender, email, c_email')->get_where('ci_user', array('id' => $current_user_id))->row_array();
          if (!empty($u_row)) {
            $current_user_name = !empty($u_row['name']) ? $u_row['name'] : '';
            $current_user_gender = !empty($u_row['gender']) ? trim($u_row['gender']) : '';
            $current_user_email = !empty($u_row['email']) ? $u_row['email'] : (!empty($u_row['c_email']) ? $u_row['c_email'] : '');
          }
        }
        if (empty($current_user_name)) {
          $current_user_name = $this->session->userdata('manager_name');
        }
        if (empty($current_user_name)) {
          $current_user_name = 'User';
        }

        $male_avatars = array(
          'assets/dist/img/pfp_male_1.svg',
          'assets/dist/img/pfp_male_2.svg',
          'assets/dist/img/pfp_male_3.svg'
        );
        $female_avatars = array(
          'assets/dist/img/pfp_female_1.svg',
          'assets/dist/img/pfp_female_2.svg',
          'assets/dist/img/pfp_female_3.svg'
        );
        $neutral_avatars = array(
          'assets/dist/img/pfp_male_1.svg',
          'assets/dist/img/pfp_female_1.svg',
          'assets/dist/img/pfp_male_2.svg'
        );

        $user_avatar = 'assets/dist/img/pfp_male_1.svg';
        if (strcasecmp($current_user_gender, 'female') === 0) {
          $idx = abs($current_user_id) % count($female_avatars);
          $user_avatar = $female_avatars[$idx];
        } elseif (strcasecmp($current_user_gender, 'male') === 0) {
          $idx = abs($current_user_id) % count($male_avatars);
          $user_avatar = $male_avatars[$idx];
        } else {
          $idx = abs($current_user_id) % count($neutral_avatars);
          $user_avatar = $neutral_avatars[$idx];
        }
      ?>
      <?php if(has_section_perm('dashboard')){ ?>
      <li class="<?php if($route1=="dashboard"){ echo "active";}else{ echo "";}?>"> <a href="<?php echo base_url();?>manager/dashboard"> <i class="fa fa-tachometer"></i> <span>Dashboard</span>  </a> </li>
      <?php } ?>

      <?php if(has_section_perm('my_zone') || has_section_perm('database') || has_section_perm('leads') || has_section_perm('tasks') || has_section_perm('tickets') || has_section_perm('internal_tasks') || has_section_perm('external_tasks')){ ?>
      <li class="<?php if($route1=="leads" || $route1=="verticals" || $route1=="database-transfer" || in_array($route1, array('tasks', 'tickets', 'operations'))){ echo "active";}else{ echo "";}?>"> 
        <a href="javascript:void(0);"><i class="fa fa-th-large"></i> <span>My Zone</span> </a>
        <ul>
          <?php if(has_section_perm('database', 'write')){ ?>
          <li><a href="<?php echo base_url();?>manager/leads/add"><i class="fa fa-plus"></i>Database Add</a></li>
          <?php } ?>
          <?php if(has_section_perm('database')){ ?>
          <li><a href="<?php echo base_url();?>manager/leads"><i class="fa fa-list"></i>Database View</a></li>
          <li><a href="<?php echo base_url();?>manager/leads/view_leads"><i class="fa fa-bullseye"></i>Lead View</a></li>
          <li><a href="<?php echo base_url();?>manager/leads/followups"><i class="fa fa-clock-o"></i>Database Follow-up</a></li>
          <li><a href="<?php echo base_url();?>manager/leads/meetings"><i class="fa fa-calendar"></i>Database Meetings</a></li>
          <?php } ?>
          <?php if(has_section_perm('internal_tasks', 'write') || has_section_perm('tasks', 'write') || has_section_perm('tasks')){ ?>
          <li><a href="<?php echo base_url();?>manager/tasks/add?scope=internal"><i class="fa fa-plus-circle"></i>Add Internal Task</a></li>
          <?php } ?>
          <?php if(has_section_perm('external_tasks', 'write') || has_section_perm('tasks', 'write') || has_section_perm('tasks')){ ?>
          <li><a href="<?php echo base_url();?>manager/tasks/add?scope=external"><i class="fa fa-plus-square"></i>Add External Task</a></li>
          <?php } ?>
          <?php if(has_section_perm('internal_tasks') || has_section_perm('tasks')){ ?>
          <li><a href="<?php echo base_url();?>manager/tasks/internal"><i class="fa fa-building-o"></i>View Internal Task</a></li>
          <?php } ?>
          <?php if(has_section_perm('external_tasks') || has_section_perm('tasks')){ ?>
          <li><a href="<?php echo base_url();?>manager/tasks/external"><i class="fa fa-briefcase"></i>View External Task</a></li>
          <?php } ?>
          <?php if(has_section_perm('tickets', 'write')){ ?>
          <li><a href="<?php echo base_url();?>manager/tickets/add"><i class="fa fa-plus"></i>Raise Ticket</a></li>
          <?php } ?>
          <?php if(has_section_perm('tickets')){ ?>
          <li><a href="<?php echo base_url();?>manager/tickets?status_filter=open"><i class="fa fa-ticket"></i>Open Ticket</a></li>
          <?php } ?>
          <?php if(has_section_perm('database', 'write')){ ?>
          <li><a href="<?php echo base_url();?>manager/verticals/add"><i class="fa fa-plus"></i>Vertical Add</a></li>
          <?php } ?>
          <?php if(has_section_perm('database')){ ?>
          <li><a href="<?php echo base_url();?>manager/verticals"><i class="fa fa-list"></i>Vertical View</a></li>
          <li><a href="<?php echo base_url();?>manager/database-transfer"><i class="fa fa-exchange"></i>Database Transfer</a></li>
          <?php } ?>
        </ul>
      </li>
      <?php } ?>

      <?php if(has_section_perm('system')){ ?>
      <li class="<?php if($route1=="roles" || $route1=="terms" || $route1=="users" || $route1=="assigned-leads" || $route1=="admin-logs" || ($route1=="system" && ($route2=="calling-api" || $route2=="calling_api")) || ($route1=="leads" && in_array($route2, array("import", "export")))){ echo "active";}else{ echo "";}?>"> 
        <a href="<?php echo ($m_role == 1)? base_url().'manager/roles' : base_url().'manager/leads/import'; ?>"><i class="fa fa-paperclip"></i> <span>System</span> </a>
        <ul>        
          <?php if(has_section_perm('users', 'write')){ ?>
            <li><a href="<?php echo base_url();?>manager/users/add"><i class="fa fa-plus"></i>User Add</a></li>
          <?php } ?>
          <?php if(has_section_perm('users')){ ?>
            <li><a href="<?php echo base_url();?>manager/users"><i class="fa fa-list"></i>User View</a></li>
          <?php } ?>
          <?php if(has_section_perm('roles')){ ?>
            <li><a href="<?php echo base_url();?>manager/roles"><i class="fa fa-list"></i>Roles</a></li>
          <?php } ?>
          <?php if($m_role == 1){ ?>
            <li><a href="<?php echo base_url();?>manager/terms"><i class="fa fa-list"></i>Category</a></li>
          <?php } ?>
          <li><a href="<?php echo base_url();?>manager/leads/import"><i class="fa fa-upload"></i>Import Leads</a></li>
          <?php if($m_role == 1){ ?>
            <li><a href="<?php echo base_url();?>manager/leads/export"><i class="fa fa-download"></i>Export Leads</a></li>
            <li><a href="<?php echo base_url();?>manager/assigned-leads"><i class="fa fa-tasks"></i>Allotted Database</a></li>
            <li><a href="<?php echo base_url();?>manager/assigned-leads?alloc_filter=non_allotted"><i class="fa fa-folder-open-o"></i>Non-Allotted Database</a></li>
            <li><a href="<?php echo base_url();?>manager/admin-logs"><i class="fa fa-history"></i>Admin Logs</a></li>
          <?php } ?>
          <li><a href="<?php echo base_url();?>manager/system/calling-api"><i class="fa fa-phone-square"></i>Calling API</a></li>
        </ul>
      </li>
      <?php } ?>

      <?php if(has_section_perm('reports')){ ?>
      <li class="<?php if($route1=="reports" || $route1=="download-report" || $route1=="admin-reports"){ echo "active";}else{ echo "";}?>"> <a href="<?php echo base_url();?>manager/download-report"><i class="fa fa-bar-chart"></i> <span>Reports</span> </a>
        <ul>
          <li><a href="https://7strokes.maxvendor47.workers.dev/" target="_blank"><i class="fa fa-database"></i>Generate Data</a></li>
          <li><a href="<?php echo base_url();?>manager/download-report"><i class="fa fa-file-pdf-o"></i>Download Reports</a></li>
        </ul>
      </li>
      <?php } ?>

    </ul>
  </nav>

  <div class="header-quick-actions">
    <?php if(has_section_perm('calls')){ ?>
    <a href="<?php echo base_url();?>manager/calls" class="global-theme-btn" title="Phone Dialer & Calls" style="display:inline-flex; align-items:center; justify-content:center; text-decoration:none; margin-right:6px; color:#2563eb;">
      <i class="fa fa-phone"></i>
    </a>
    <?php } ?>
    <button type="button" class="global-theme-btn" id="globalThemeToggle" title="Toggle Dark/Light Mode">
      <i class="fa fa-moon-o"></i>
    </button>

    <div class="profile-menu-container">
      <button type="button" class="profile-avatar-btn" id="profileMenuToggle" aria-haspopup="true" aria-expanded="false" title="<?php echo htmlspecialchars($current_user_name); ?>">
        <img src="<?php echo base_url() . $user_avatar; ?>" alt="<?php echo htmlspecialchars($current_user_name); ?>" class="profile-avatar-img" />
      </button>

      <div class="google-profile-dropdown" id="googleProfileDropdown">
        <div class="google-profile-card">
          <div class="google-avatar-wrap">
            <img src="<?php echo base_url() . $user_avatar; ?>" alt="<?php echo htmlspecialchars($current_user_name); ?>" class="google-profile-large-img" />
          </div>
          <div class="google-greeting-name">Hi, <?php echo htmlspecialchars($current_user_name); ?></div>
          <?php if (!empty($current_user_email)) { ?>
            <div class="google-user-email"><?php echo htmlspecialchars($current_user_email); ?></div>
          <?php } ?>
        </div>

        <div class="google-menu-divider"></div>

        <ul class="google-profile-options">
          <li>
            <a href="<?php echo base_url(); ?>manager/profile" class="google-profile-item">
              <i class="fa fa-user-circle"></i>
              <span>View Profile</span>
            </a>
          </li>
          <li>
            <a href="<?php echo base_url(); ?>manager/password" class="google-profile-item">
              <i class="fa fa-key"></i>
              <span>Change Password</span>
            </a>
          </li>
        </ul>

        <div class="google-menu-divider"></div>

        <div class="google-logout-wrap">
          <a href="#" class="logout google-logout-btn">
            <i class="fa fa-power-off"></i>
            <span>Logout</span>
          </a>
        </div>
      </div>
    </div>
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

  $(document).on('click', '#profileMenuToggle', function(e) {
    e.preventDefault();
    e.stopPropagation();
    $('#googleProfileDropdown').fadeToggle(150);
  });

  $(document).on('click', function(e) {
    if (!$(e.target).closest('.profile-menu-container').length) {
      $('#googleProfileDropdown').fadeOut(120);
    }
  });
});
</script>
<div class="clearfix"></div>

<aside class="main-sidebar"> 
  
  <div class="crm-sidebar-header">
    <img src="<?php echo base_url(); ?>assets/logo-7strokes.png" alt="7 Strokes" />
    <button type="button" class="crm-sidebar-close" id="crmSidebarClose" aria-label="Close navigation">&times;</button>
  </div>
  
  
  
  <section class="sidebar">
    <ul class="sidebar-menu">
      <?php  
        $route1=$this->uri->segment(2);
	      $route2=$this->uri->segment(3);
        $m_role = $this->session->userdata('manager_role');
      ?>
      <?php if(has_section_perm('dashboard')){ ?>
      <li class="<?php if($route1=="dashboard"){ echo "active";}else{ echo "";}?>"> 
        <a href="<?php echo base_url();?>manager/dashboard"> <i class="fa fa-tachometer"></i> <span>Dashboard</span> </a> 
      </li>
      <?php } ?>

      <?php if(has_section_perm('my_zone') || has_section_perm('database') || has_section_perm('leads') || has_section_perm('calls')){ ?>
      <li class="<?php if($route1=="leads" || $route1=="verticals" || $route1=="database-transfer" || $route1=="calls"){ echo "active";}else{ echo "";}?> treeview"> 
        <a href="javascript:void(0);"><i class="fa fa-th-large"></i> <span>My Zone</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right sidebar-arrow"></i></span> </a>
        <ul class="treeview-menu">
          <?php if(has_section_perm('calls')){ ?>
          <li><a href="<?php echo base_url();?>manager/calls"><i class="fa fa-phone"></i>Calls</a></li>
          <?php } ?>
          <?php if(has_section_perm('database', 'write')){ ?>
          <li><a href="<?php echo base_url();?>manager/leads/add"><i class="fa fa-plus"></i>Database Add</a></li>
          <?php } ?>
          <?php if(has_section_perm('database')){ ?>
          <li><a href="<?php echo base_url();?>manager/leads"><i class="fa fa-list"></i>Database View</a></li>
          <li><a href="<?php echo base_url();?>manager/leads/followups"><i class="fa fa-clock-o"></i>Database Follow-up</a></li>
          <li><a href="<?php echo base_url();?>manager/leads/meetings"><i class="fa fa-calendar"></i>Database Meetings</a></li>
          <?php } ?>
          <?php if(has_section_perm('database', 'write')){ ?>
          <li><a href="<?php echo base_url();?>manager/verticals/add"><i class="fa fa-plus"></i>Vertical Add</a></li>
          <?php } ?>
          <?php if(has_section_perm('database')){ ?>
          <li><a href="<?php echo base_url();?>manager/verticals"><i class="fa fa-list"></i>Vertical View</a></li>
          <li><a href="<?php echo base_url();?>manager/verticals/followups"><i class="fa fa-list"></i>Vertical Follow-up</a></li>
          <li><a href="<?php echo base_url();?>manager/database-transfer"><i class="fa fa-exchange"></i>Database Transfer</a></li>
          <?php } ?>
        </ul>
      </li>
      <?php } ?>

      <?php if(has_section_perm('team')){ ?>
      <li class="<?php if($route1=="team"){ echo "active";}else{ echo "";}?> treeview"> 
        <a href="javascript:void(0);"><i class="fa fa-users"></i> <span>Team</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right sidebar-arrow"></i></span> </a>
        <ul class="treeview-menu">
          <li><a href="<?php echo base_url();?>manager/team/members"><i class="fa fa-users"></i>Members </a></li>
          <li><a href="<?php echo base_url();?>manager/team/chart"><i class="fa fa-list"></i>Chart</a></li>
          <li><a href="<?php echo base_url();?>manager/team/leads"><i class="fa fa-list"></i>Leads</a></li>
          <li><a href="<?php echo base_url();?>manager/team/verticals"><i class="fa fa-list"></i>Verticals</a></li>
          <li><a href="<?php echo base_url();?>manager/team/followups"><i class="fa fa-list"></i>Follow up</a></li>
          <li><a href="<?php echo base_url();?>manager/team/meetings"><i class="fa fa-list"></i>Meetings</a></li>
          <li><a href="<?php echo base_url();?>manager/team/assignmeetings"><i class="fa fa-list"></i>Assigned Meetings</a></li>
          <li><a href="<?php echo base_url();?>manager/team/assignleads"><i class="fa fa-list"></i>Assign Database</a></li>
          <li><a href="<?php echo base_url();?>manager/tasks"><i class="fa fa-tasks"></i>Task</a></li>
          <li><a href="<?php echo base_url();?>manager/team/tickets"><i class="fa fa-ticket"></i>Tickets</a></li>
        </ul>
      </li>
      <?php } ?>

      <?php if(has_section_perm('kam')){ ?>
      <li class="<?php if($route1=="kam"){ echo "active";}else{ echo "";}?> treeview"> 
        <a href="<?php echo base_url();?>manager/kam/my-client"><i class="fa fa-briefcase"></i> <span>KAM</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right sidebar-arrow"></i></span> </a>
        <ul class="treeview-menu">
          <li><a href="<?php echo base_url();?>manager/kam/my-client"><i class="fa fa-user"></i>My Client</a></li>
          <?php if($m_role == 1 || has_section_perm('kam', 'write')){ ?>
          <li><a href="<?php echo base_url();?>manager/kam/onboard"><i class="fa fa-user-plus"></i>Onboard Client</a></li>
          <?php } ?>
          <li><a href="<?php echo base_url();?>manager/kam/schedule"><i class="fa fa-calendar"></i>Create Schedule</a></li>
        </ul>
      </li>
      <?php } ?>

      <?php if(has_section_perm('tasks') || has_section_perm('tickets') || has_section_perm('internal_tasks') || has_section_perm('external_tasks')){ ?>
      <li class="<?php if(in_array($route1, array('tasks', 'tickets', 'operations'))){ echo "active";}else{ echo "";}?> treeview"> 
        <a href="javascript:void(0);"><i class="fa fa-tasks"></i> <span>Tasks</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right sidebar-arrow"></i></span> </a>
        <ul class="treeview-menu">
          <?php if(has_section_perm('tickets', 'write')){ ?>
          <li><a href="<?php echo base_url();?>manager/tickets/add"><i class="fa fa-plus"></i>Raise Ticket</a></li>
          <?php } ?>
          <?php if(has_section_perm('tickets')){ ?>
          <li><a href="<?php echo base_url();?>manager/tickets?status_filter=open"><i class="fa fa-ticket"></i>Open Ticket</a></li>
          <?php } ?>
          <?php if(has_section_perm('internal_tasks') || has_section_perm('tasks')){ ?>
          <li><a href="<?php echo base_url();?>manager/tasks/internal"><i class="fa fa-building-o"></i>View Internal Task</a></li>
          <?php } ?>
          <?php if(has_section_perm('external_tasks') || has_section_perm('tasks')){ ?>
          <li><a href="<?php echo base_url();?>manager/tasks/external"><i class="fa fa-briefcase"></i>View External Task</a></li>
          <?php } ?>
        </ul>
      </li>
      <?php } ?>

      <?php if(has_section_perm('system')){ ?>
      <li class="<?php if($route1=="roles" || $route1=="terms" || $route1=="users" || $route1=="assigned-leads" || $route1=="admin-logs" || ($route1=="system" && ($route2=="calling-api" || $route2=="calling_api")) || ($route1=="leads" && in_array($route2, array("import", "export")))){ echo "active";}else{ echo "";}?> treeview"> 
        <a href="javascript:void(0);"><i class="fa fa-paperclip"></i> <span>System</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right sidebar-arrow"></i></span> </a>
        <ul class="treeview-menu">        
          <?php if(has_section_perm('users', 'write')){ ?>
            <li><a href="<?php echo base_url();?>manager/users/add"><i class="fa fa-plus"></i>User Add</a></li>
          <?php } ?>
          <?php if(has_section_perm('users')){ ?>
            <li><a href="<?php echo base_url();?>manager/users"><i class="fa fa-list"></i>User View</a></li>
          <?php } ?>
          <?php if(has_section_perm('roles')){ ?>
            <li><a href="<?php echo base_url();?>manager/roles"><i class="fa fa-list"></i>Roles</a></li>
          <?php } ?>
          <?php if($m_role == 1){ ?>
            <li><a href="<?php echo base_url();?>manager/terms"><i class="fa fa-list"></i>Category</a></li>
          <?php } ?>
          <li><a href="<?php echo base_url();?>manager/leads/import"><i class="fa fa-upload"></i>Import Leads</a></li>
          <?php if($m_role == 1){ ?>
            <li><a href="<?php echo base_url();?>manager/leads/export"><i class="fa fa-download"></i>Export Leads</a></li>
            <li><a href="<?php echo base_url();?>manager/assigned-leads"><i class="fa fa-tasks"></i>Allotted Database</a></li>
            <li><a href="<?php echo base_url();?>manager/assigned-leads?alloc_filter=non_allotted"><i class="fa fa-folder-open-o"></i>Non-Allotted Database</a></li>
            <li><a href="<?php echo base_url();?>manager/admin-logs"><i class="fa fa-history"></i>Admin Logs</a></li>
          <?php } ?>
          <li><a href="<?php echo base_url();?>manager/system/calling-api"><i class="fa fa-phone-square"></i>Calling API</a></li>
        </ul>
      </li>
      <?php } ?>

      <?php if(has_section_perm('reports')){ ?>
      <li class="<?php if($route1=="reports" || $route1=="download-report" || $route1=="admin-reports"){ echo "active menu-open";}else{ echo "";}?> treeview"> 
        <a href="javascript:void(0);"><i class="fa fa-bar-chart"></i> <span>Reports</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right sidebar-arrow"></i></span> </a>
        <ul class="treeview-menu" style="<?php if($route1=="reports" || $route1=="download-report" || $route1=="admin-reports"){ echo "display: block;"; } ?>">
          <li><a href="https://7strokes.maxvendor47.workers.dev/" target="_blank"><i class="fa fa-database"></i>Generate Data</a></li>
          <li><a href="<?php echo base_url();?>manager/download-report"><i class="fa fa-file-pdf-o"></i>Download Reports</a></li>
        </ul>
      </li>
      <?php } ?>
    </ul>
  </section>
  
   
  
</aside>
