<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
    
<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/logo.png" type="image/png">
<link rel="icon" type="image/png" sizes="64x64" href="<?php echo base_url(); ?>assets/logo.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url(); ?>assets/logo.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url(); ?>assets/logo.png">
<link rel="apple-touch-icon" href="<?php echo base_url(); ?>assets/logo.png">
<meta property="og:title" content="<?php echo !empty($info['title']) ? htmlspecialchars($info['title']) : 'Seven Strokes CRM'; ?>">
<meta property="og:description" content="Seven Strokes CRM Platform">
<meta property="og:image" content="<?php echo base_url(); ?>assets/logo.png">
<meta property="og:type" content="website">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:image" content="<?php echo base_url(); ?>assets/logo.png">

    
<title><?php echo $info['title']; ?></title>

<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/slimmenu.min.css" >   
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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
<link rel="stylesheet" href="<?php echo base_url();?>assets/dist/css/crm-dashboard.css?v=20261031_v1810">
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
      <img src="<?php echo base_url(); ?>assets/logo.png" alt="7 Strokes" />
    </a> 
  </div>

  
  <nav class="navbar navbar-static-top"> 
    <ul class="munk_menu">
      <?php  
        $route1=$this->uri->segment(2);
	      $route2=$this->uri->segment(3);
        $m_role = (int)$this->session->userdata('manager_role');
        $current_user_id = (int)$this->session->userdata('manager_id');
        if (empty($m_role) && $current_user_id === 1) {
            $m_role = 1;
        }
        $is_admin = ($m_role === 1 || $current_user_id === 1);
        $is_support = ($m_role === 4);
        $user_team_count = $current_user_id ? count($this->ci_admin_model->fetchTeamList($current_user_id, '')) : 0;
        $can_see_review = ($is_admin || $m_role === 2 || $m_role === 4 || has_section_perm('back_office') || has_section_perm('lead_review'));
        $is_hr = ($is_admin || $m_role === 7 || has_section_perm('hr'));

        $current_user_name = '';
        $current_user_gender = '';
        $current_user_email = '';
        $current_user_role_name = '';
        $custom_avatar = '';
        if ($current_user_id) {
          $u_q = $this->db->query("SELECT U.name, U.gender, U.email, U.c_email, U.avatar, R.title as role_title FROM ci_user U LEFT JOIN ci_roles R ON U.user_type = R.id WHERE U.id = " . (int)$current_user_id);
          $u_row = ($u_q && $u_q->num_rows() > 0) ? $u_q->row_array() : null;
          if (!empty($u_row)) {
            $current_user_name = !empty($u_row['name']) ? $u_row['name'] : '';
            $current_user_gender = !empty($u_row['gender']) ? trim($u_row['gender']) : '';
            $current_user_email = !empty($u_row['c_email']) ? $u_row['c_email'] : (!empty($u_row['email']) ? $u_row['email'] : '');
            $current_user_role_name = !empty($u_row['role_title']) ? $u_row['role_title'] : '';
            if (!empty($u_row['avatar']) && file_exists(FCPATH . $u_row['avatar'])) {
              $custom_avatar = $u_row['avatar'];
            }
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

        if (!empty($custom_avatar)) {
          $user_avatar = $custom_avatar;
        } else {
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
        }
      ?>
      <?php if($is_admin || has_section_perm('dashboard')){ ?>
      <li class="<?php if($route1=="dashboard"){ echo "active";}else{ echo "";}?>"> <a href="<?php echo base_url();?>manager/dashboard"> <svg class="crm-svg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-2px; margin-right:4px;"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="5" rx="1"></rect><rect x="14" y="12" width="7" height="9" rx="1"></rect><rect x="3" y="16" width="7" height="5" rx="1"></rect></svg> <span>Dashboard</span>  </a> </li>
      <?php } ?>

      <?php if($is_admin || has_section_perm('my_zone') || has_section_perm('database') || has_section_perm('leads') || has_section_perm('tasks') || has_section_perm('internal_tasks') || has_section_perm('external_tasks')){ ?>
      <li class="<?php if(($route1=="leads" && $route2!="review_leads") || $route1=="verticals" || $route1=="database-transfer" || in_array($route1, array('tasks')) || ($route1=="orders" && ($route2=="my" || (isset($_GET['scope']) && $_GET['scope']=="my"))) || $route1=="sales"){ echo "active";}else{ echo "";}?>"> 
        <a href="javascript:void(0);"><svg class="crm-svg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-2px; margin-right:4px;"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg> <span>My Zone</span> </a>
        <ul>
          <?php if($is_admin || has_section_perm('database', 'write')){ ?>
          <li><a href="<?php echo base_url();?>manager/leads/add"><i class="fa fa-plus"></i>Add Database</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('database')){ ?>
          <li><a href="<?php echo base_url();?>manager/leads"><i class="fa fa-database"></i>View Database</a></li>
          <li><a href="<?php echo base_url();?>manager/leads/view_leads"><i class="fa fa-bullseye"></i>View Leads</a></li>
          <li><a href="<?php echo base_url();?>manager/orders/my"><i class="fa fa-shopping-cart"></i>View Orders</a></li>
          <li><a href="<?php echo base_url();?>manager/sales"><i class="fa fa-line-chart"></i>View Sales</a></li>
          <li><a href="<?php echo base_url();?>manager/leads/followups"><i class="fa fa-clock-o"></i>My Follow-Up</a></li>
          <li><a href="<?php echo base_url();?>manager/leads/meetings"><i class="fa fa-calendar"></i>My Meetings</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('database', 'write')){ ?>
          <li><a href="<?php echo base_url();?>manager/verticals/add"><i class="fa fa-plus"></i>Add Vertical</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('database')){ ?>
          <li><a href="<?php echo base_url();?>manager/verticals"><i class="fa fa-list"></i>View Vertical</a></li>
          <li><a href="<?php echo base_url();?>manager/verticals/followups"><i class="fa fa-clock-o"></i>Vertical Follow-up</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('internal_tasks') || has_section_perm('tasks')){ ?>
          <li><a href="<?php echo base_url();?>manager/tasks/internal"><i class="fa fa-building-o"></i>View Internal Task</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('external_tasks') || has_section_perm('tasks')){ ?>
          <li><a href="<?php echo base_url();?>manager/tasks/external"><i class="fa fa-briefcase"></i>View External Task</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('database')){ ?>
          <li><a href="<?php echo base_url();?>manager/database-transfer"><i class="fa fa-exchange"></i>Database Transfer</a></li>
          <?php } ?>
        </ul>
      </li>
      <?php } ?>

      <?php if($is_admin || has_section_perm('team')){ ?>
      <li class="<?php if(in_array($route1, array('team')) || ($route1=="orders" && ($route2=="team" || (isset($_GET['scope']) && $_GET['scope']=="team")))){ echo "active";}else{ echo "";}?>"> <a href="javascript:void(0);"><svg class="crm-svg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-2px; margin-right:4px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg> <span>Team</span> </a>
        <ul>
          <li><a href="<?php echo base_url();?>manager/team/chart"><i class="fa fa-sitemap"></i>Chart</a></li>
          <li><a href="<?php echo base_url();?>manager/team/database"><i class="fa fa-database"></i>Database</a></li>
          <li><a href="<?php echo base_url();?>manager/team/leads"><i class="fa fa-list"></i>Leads</a></li>
          <li><a href="<?php echo base_url();?>manager/team/orders"><i class="fa fa-shopping-cart"></i>Orders</a></li>
          <li><a href="<?php echo base_url();?>manager/team/verticals"><i class="fa fa-list"></i>Verticals</a></li>
          <li><a href="<?php echo base_url();?>manager/team/followups"><i class="fa fa-clock-o"></i>Follow up</a></li>
          <li><a href="<?php echo base_url();?>manager/team/meetings"><i class="fa fa-calendar"></i>Meetings</a></li>
          <li><a href="<?php echo base_url();?>manager/team/assignmeetings"><i class="fa fa-calendar-check-o"></i>Assigned Meetings</a></li>
          <li><a href="<?php echo base_url();?>manager/team/assignleads"><i class="fa fa-tasks"></i>Assign Database</a></li>
          <li><a href="<?php echo base_url();?>manager/tasks"><i class="fa fa-tasks"></i>Task</a></li>
          <li><a href="<?php echo base_url();?>manager/team/tickets"><i class="fa fa-ticket"></i>Tickets</a></li>
        </ul>
      </li>
      <?php } ?>

      <?php if($can_see_review){ ?>
      <li class="<?php if(($route1=="leads" && $route2=="review_leads") || $route1=="back-office" || ($route1=="orders" && $route2!="my" && $route2!="team" && (!isset($_GET['scope']) || !in_array($_GET['scope'], array('my', 'team'))))){ echo "active";}else{ echo "";}?>"> 
        <a href="javascript:void(0);"><svg class="crm-svg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-2px; margin-right:4px;"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg> <span>Back Office</span> </a>
        <ul>
          <li><a href="<?php echo base_url();?>manager/leads/review_leads"><i class="fa fa-check-square-o"></i>Pending Leads Approval</a></li>
          <li><a href="<?php echo base_url();?>manager/orders/pending_process"><i class="fa fa-hourglass-half"></i>Pending Order Process</a></li>
          <li><a href="<?php echo base_url();?>manager/orders/pending_sales_approval"><i class="fa fa-clock-o"></i>Pending Sales Approval</a></li>
          <?php if($is_admin || has_section_perm('kam', 'write')){ ?>
          <li><a href="<?php echo base_url();?>manager/kam/onboard"><i class="fa fa-user-plus"></i>Onboard Client</a></li>
          <?php } ?>
          <li><a href="<?php echo base_url();?>manager/orders/open"><i class="fa fa-folder-open-o"></i>Open Orders</a></li>
          <li><a href="<?php echo base_url();?>manager/orders/followups"><i class="fa fa-calendar-check-o"></i>Orders Follow-up</a></li>
          <li><a href="<?php echo base_url();?>manager/orders?status=completed"><i class="fa fa-check-circle"></i>Completed Orders</a></li>
          <li><a href="<?php echo base_url();?>manager/orders?status=closed"><i class="fa fa-flag-checkered"></i>Closed Orders</a></li>
          <li><a href="<?php echo base_url();?>manager/orders?status=rejected_lost"><i class="fa fa-times-circle"></i>Rejected / Lost Orders</a></li>
          <li><a href="<?php echo base_url();?>manager/orders"><i class="fa fa-list"></i>View All Orders</a></li>
        </ul>
      </li>
      <?php } ?>

      <?php if($is_admin || has_section_perm('kam')){ ?>
      <li class="<?php if($route1=="kam"){ echo "active";}else{ echo "";}?>"> 
        <a href="javascript:void(0);"><svg class="crm-svg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-2px; margin-right:4px;"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg> <span>KAM</span> </a>
        <ul>
          <li><a href="<?php echo base_url();?>manager/kam/my-client"><i class="fa fa-user"></i>My Client</a></li>
          <?php if($is_admin || has_section_perm('kam', 'write')){ ?>
          <li><a href="<?php echo base_url();?>manager/kam/onboard"><i class="fa fa-user-plus"></i>Onboard Client</a></li>
          <li><a href="<?php echo base_url();?>manager/kam/schedule"><i class="fa fa-calendar"></i>Create Schedule</a></li>
          <?php } ?>
          <li><a href="<?php echo base_url();?>manager/kam/task-followup"><i class="fa fa-tasks"></i>Task Follow-up</a></li>
          <li><a href="<?php echo base_url();?>manager/kam/reminders"><i class="fa fa-bell-o"></i>Reminders</a></li>
        </ul>
      </li>
      <?php } ?>

      <?php if($is_hr){ ?>
      <li class="<?php if($route1=="hr"){ echo "active";}else{ echo "";}?>"> 
        <a href="javascript:void(0);"><svg class="crm-svg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-2px; margin-right:4px;"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><line x1="15" y1="8" x2="17" y2="8"></line><line x1="15" y1="12" x2="17" y2="12"></line><line x1="7" y1="16" x2="17" y2="16"></line></svg> <span>HR</span> </a>
        <ul>
          <li><a href="<?php echo base_url();?>manager/hr"><i class="fa fa-th-large"></i>Overview</a></li>
          <li><a href="<?php echo base_url();?>manager/hr/targets"><i class="fa fa-crosshairs"></i>Targets</a></li>
          <li><a href="<?php echo base_url();?>manager/hr/lifecycle"><svg class="crm-svg-ico" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-2px; margin-right:6px;"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg>Employee Lifecycle</a></li>
          <li><a href="<?php echo base_url();?>manager/hr/attendance"><i class="fa fa-map-marker"></i>Attendance & GPS</a></li>
          <li><a href="<?php echo base_url();?>manager/hr/shifts"><i class="fa fa-clock-o"></i>Shifts</a></li>
          <li><a href="<?php echo base_url();?>manager/hr/leaves"><i class="fa fa-calendar-check-o"></i>Leaves</a></li>
          <li><a href="<?php echo base_url();?>manager/hr/payroll"><i class="fa fa-money"></i>Payroll</a></li>
          <li><a href="<?php echo base_url();?>manager/hr/taxation"><i class="fa fa-calculator"></i>Taxation & Gratuity</a></li>
          <li><a href="<?php echo base_url();?>manager/hr/appraisals"><i class="fa fa-star-o"></i>Appraisals</a></li>
          <li><a href="<?php echo base_url();?>manager/hr/documents"><i class="fa fa-folder-open-o"></i>Document Vault</a></li>
        </ul>
      </li>
      <?php } ?>

      <?php if($is_admin || has_section_perm('system')){ ?>
      <li class="<?php if($route1=="roles" || $route1=="terms" || $route1=="users" || $route1=="assigned-leads" || $route1=="admin-logs" || ($route1=="system" && ($route2=="calling-api" || $route2=="calling_api" || $route2=="integrations" || $route2=="assignee" || $route2=="kam_clients")) || $route1=="integrations" || ($route1=="leads" && in_array($route2, array("import", "export")))){ echo "active";}else{ echo "";}?>"> 
        <a href="javascript:void(0);"><svg class="crm-svg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-2px; margin-right:4px;"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg> <span>System</span> </a>
        <ul>        
          <?php if($is_admin || has_section_perm('users')){ ?>
          <li><a href="<?php echo base_url();?>manager/users/add"><i class="fa fa-plus"></i>User Add</a></li>
          <li><a href="<?php echo base_url();?>manager/users"><i class="fa fa-list"></i>User View</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('roles')){ ?>
          <li><a href="<?php echo base_url();?>manager/roles"><i class="fa fa-list"></i>Roles</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('terms')){ ?>
          <li><a href="<?php echo base_url();?>manager/terms"><i class="fa fa-list"></i>Category</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('leads_import')){ ?>
          <li><a href="<?php echo base_url();?>manager/leads/import"><i class="fa fa-upload"></i>Import Leads</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('leads_export')){ ?>
          <li><a href="<?php echo base_url();?>manager/leads/export"><i class="fa fa-download"></i>Export Leads</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('assigned_leads')){ ?>
          <li><a href="<?php echo base_url();?>manager/assigned-leads"><i class="fa fa-tasks"></i>Allotted Database</a></li>
          <li><a href="<?php echo base_url();?>manager/assigned-leads?alloc_filter=non_allotted"><i class="fa fa-folder-open-o"></i>Non-Allotted Database</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('admin_logs')){ ?>
          <li><a href="<?php echo base_url();?>manager/admin-logs"><i class="fa fa-history"></i>Admin Logs</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('kam_clients') || has_section_perm('system')){ ?>
          <li><a href="<?php echo base_url();?>manager/system/kam_clients"><i class="fa fa-briefcase"></i>KAM Clients</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('calling_api')){ ?>
          <li><a href="<?php echo base_url();?>manager/system/calling-api"><i class="fa fa-phone-square"></i>Calling API</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('integrations')){ ?>
          <li><a href="<?php echo base_url();?>manager/system/integrations"><i class="fa fa-plug"></i>Third-Party API</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('system')){ ?>
          <li><a href="<?php echo base_url();?>manager/system/qc"><i class="fa fa-check-square-o"></i>QC Panel</a></li>
          <?php } ?>
        </ul>
      </li>
      <?php } ?>

      <?php if($is_admin || has_section_perm('reports')){ ?>
      <li class="<?php if($route1=="reports" || $route1=="download-report" || $route1=="admin-reports" || $route1=="revenue-list" || $route1=="company-projections"){ echo "active";}else{ echo "";}?>"> <a href="javascript:void(0);"><svg class="crm-svg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-2px; margin-right:4px;"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg> <span>Reports</span> </a>
        <ul>
          <li><a href="<?php echo base_url();?>manager/reports/revenue-list"><i class="fa fa-money"></i>Revenue List</a></li>
          <li><a href="<?php echo base_url();?>manager/reports/company-projections"><i class="fa fa-line-chart"></i>Company Projections</a></li>
          <li><a href="https://7strokes.maxvendor47.workers.dev/" target="_blank"><i class="fa fa-database"></i>Generate Data</a></li>
          <li><a href="<?php echo base_url();?>manager/download-report"><i class="fa fa-file-pdf-o"></i>Report</a></li>
        </ul>
      </li>
      <?php } ?>

    </ul>
  </nav>

  <div class="header-quick-actions">
    <a href="<?php echo base_url();?>manager/live" class="live-nav-badge <?php echo ($route1 === 'live') ? 'active' : ''; ?>" title="Live Operations Report" style="display:inline-flex; align-items:center; justify-content:center; text-decoration:none; margin-right:8px; padding:4px 12px; border-radius:6px; <?php echo ($route1 === 'live') ? 'background:#00c6f0; color:#ffffff; border:1px solid #00c6f0;' : 'background:transparent; color:#00c6f0; border:1px solid #00c6f0;'; ?> font-weight:700; font-size:12px; letter-spacing:0.06em; transition:all 0.2s ease;">LIVE</a>
    <?php if(has_section_perm('calls')){ ?>
    <a href="<?php echo base_url();?>manager/calls" class="global-theme-btn" title="Phone Dialer & Calls" style="display:inline-flex; align-items:center; justify-content:center; text-decoration:none; margin-right:6px; color:#2563eb;">
      <i class="fa fa-phone"></i>
    </a>
    <?php } ?>
    <div class="notif-dropdown-wrap" style="position:relative; display:inline-block; margin-right:6px;">
      <button type="button" class="global-theme-btn" id="notifBellToggle" title="Notifications" style="position:relative; display:inline-flex; align-items:center; justify-content:center;">
        <i class="fa fa-bell-o"></i>
        <span class="notif-badge" id="notifBadge" style="display:none; position:absolute; top:-4px; right:-4px; background:#dc2626; color:#ffffff; font-size:10px; font-weight:700; border-radius:10px; min-width:16px; height:16px; line-height:16px; text-align:center; padding:0 3px;"></span>
      </button>
      <div class="smartphone-notif-dropdown" id="smartphoneNotifDropdown" style="display:none; position:absolute; right:0; top:44px; width:440px; max-width:calc(100vw - 24px); background:#f8fafc; border:1px solid #e2e8f0; border-radius:18px; box-shadow:0 20px 50px rgba(0,0,0,0.18); z-index:999999; overflow:hidden;">
        <div style="padding:14px 18px; background:#0f172a; color:#ffffff; display:flex; align-items:center; justify-content:space-between;">
          <span style="font-weight:700; font-size:13.5px; letter-spacing:-0.2px;">Notification Centre</span>
          <div style="display:flex; align-items:center; gap:8px;">
            <button type="button" id="btnRequestNotifPerm" style="background:rgba(255,255,255,0.12); border:none; color:#38bdf8; font-size:11px; font-weight:600; padding:4px 10px; border-radius:6px; cursor:pointer; display:none;">Enable Alerts</button>
            <button type="button" id="btnMarkNotifRead" style="background:rgba(255,255,255,0.12); border:none; color:#cbd5e1; font-size:11px; font-weight:600; padding:4px 10px; border-radius:6px; cursor:pointer;">Mark all as read</button>
          </div>
        </div>
        <div id="notifListContainer" style="max-height:440px; overflow-y:auto; padding:8px 10px; background:#f8fafc;">
          <div style="padding:28px 16px; text-align:center; color:#94a3b8; font-size:12px;">Loading notifications...</div>
        </div>
      </div>
    </div>
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
          <?php if (!empty($current_user_role_name)) { ?>
            <div class="google-user-role" style="font-size:12px; font-weight:700; color:#0072f0; margin-top:2px; text-transform:uppercase; letter-spacing:0.5px;"><?php echo htmlspecialchars($current_user_role_name); ?></div>
          <?php } ?>
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
            <a href="<?php echo base_url(); ?>manager/events" class="google-profile-item">
              <i class="fa fa-calendar-check-o"></i>
              <span>Event and Awards</span>
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
  $(document).on('click', '.munk_menu > li:has(ul) > a', function(e) {
    e.preventDefault();
  });
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
    if (!$(e.target).closest('.notif-dropdown-wrap').length) {
      $('#smartphoneNotifDropdown').fadeOut(120);
    }
  });

  var lastSeenNotifId = 0;
  var notifInitialized = false;

  function playAlertChime() {
    try {
      var AudioContext = window.AudioContext || window.webkitAudioContext;
      if (!AudioContext) return;
      var ctx = new AudioContext();
      var osc = ctx.createOscillator();
      var gain = ctx.createGain();
      osc.type = 'sine';
      osc.frequency.setValueAtTime(587.33, ctx.currentTime);
      osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.12);
      gain.gain.setValueAtTime(0.08, ctx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.28);
      osc.connect(gain);
      gain.connect(ctx.destination);
      osc.start();
      osc.stop(ctx.currentTime + 0.3);
    } catch (e) {}
  }

  function triggerSystemNotification(n) {
    if (window.Notification && Notification.permission === 'granted') {
      try {
        var notif = new Notification(n.title, {
          body: n.message,
          icon: '<?php echo base_url(); ?>assets/logo.png'
        });
        notif.onclick = function() {
          window.focus();
          if (n.link && n.link !== '#' && n.link !== 'javascript:void(0);') {
            window.location.href = n.link;
          }
          notif.close();
        };
      } catch (err) {}
    }
  }

  function checkNotifPermBtn() {
    if (window.Notification && Notification.permission === 'default') {
      $('#btnRequestNotifPerm').show();
    } else {
      $('#btnRequestNotifPerm').hide();
    }
  }
  checkNotifPermBtn();

  $(document).on('click', '#btnRequestNotifPerm', function(e) {
    e.preventDefault();
    e.stopPropagation();
    if (window.Notification && Notification.permission === 'default') {
      Notification.requestPermission().then(function() {
        checkNotifPermBtn();
      });
    }
  });

  function loadNotifications() {
    $.ajax({
      url: '<?php echo base_url(); ?>manager/ajax_get_notifications',
      type: 'GET',
      dataType: 'json',
      success: function(res) {
        if (res && res.status) {
          var unread = parseInt(res.unread_count) || 0;
          if (unread > 0) {
            $('#notifBadge').text(unread > 99 ? '99+' : unread).show();
          } else {
            $('#notifBadge').hide();
          }
          var html = '';
          var maxId = 0;
          if (res.notifications && res.notifications.length > 0) {
            var newArrivals = [];
            $.each(res.notifications, function(i, n) {
              var nid = parseInt(n.id) || 0;
              if (nid > maxId) maxId = nid;
              if (notifInitialized && n.is_read == 0 && nid > lastSeenNotifId) {
                newArrivals.push(n);
              }
              var isUnread = (n.is_read == 0);
              var cardBorder = isUnread ? 'border:1px solid #bfdbfe; border-left:3px solid #0072f0;' : 'border:1px solid #e2e8f0;';
              var cardBg = isUnread ? '#ffffff' : '#f8fafc';
              html += '<a href="' + (n.link || 'javascript:void(0);') + '" class="notif-item-link" data-notif-id="' + n.id + '" data-unread="' + (isUnread ? '1' : '0') + '" style="display:flex; align-items:flex-start; margin-bottom:8px; padding:12px 14px; border-radius:12px; background:' + cardBg + '; ' + cardBorder + ' box-shadow:0 1px 3px rgba(0,0,0,0.03); text-decoration:none; color:inherit; transition:background 0.15s ease;">';
              html += '  <div style="width:34px; height:34px; border-radius:8px; background:' + (n.color || '#0072f0') + '18; color:' + (n.color || '#0072f0') + '; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-right:12px; font-size:14px;"><i class="' + (n.icon || 'fa fa-bell') + '"></i></div>';
              html += '  <div style="flex:1; min-width:0;">';
              html += '    <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:4px;">';
              html += '      <span style="font-size:12.5px; font-weight:700; color:#0f172a; letter-spacing:-0.2px;">' + $('<div>').text(n.title).html() + '</span>';
              html += '      <div style="display:inline-flex; align-items:center; gap:6px;">';
              if (isUnread) {
                html += '        <button type="button" class="btn-single-notif-read" data-notif-id="' + n.id + '" title="Mark as read" style="background:rgba(0,114,240,0.08); border:none; border-radius:4px; padding:2px 6px; color:#0072f0; font-size:10px; font-weight:600; cursor:pointer; line-height:1.2;">Mark read</button>';
              }
              html += '        <span style="font-size:10.5px; color:#94a3b8; font-weight:500; margin-left:2px; white-space:nowrap;">' + (n.time_ago || '') + '</span>';
              html += '      </div>';
              html += '    </div>';
              html += '    <div style="font-size:12px; color:#334155; line-height:1.45; white-space:normal; word-wrap:break-word; word-break:break-word;">' + $('<div>').text(n.message).html() + '</div>';
              html += '  </div>';
              html += '</a>';
            });

            if (newArrivals.length > 0) {
              playAlertChime();
              triggerSystemNotification(newArrivals[0]);
            }
          } else {
            html = '<div style="padding:24px 16px; text-align:center; color:#94a3b8; font-size:12px;">No notifications yet</div>';
          }
          if (maxId > lastSeenNotifId) {
            lastSeenNotifId = maxId;
          }
          notifInitialized = true;
          $('#notifListContainer').html(html);
        }
      }
    });
  }

  loadNotifications();
  setInterval(loadNotifications, 20000);

  function sendMarkNotifRead(notifId) {
    var csrfMatch = document.cookie.match(/(?:^|;\s*)csrf_cookie_name=([^;]+)/);
    var csrfToken = csrfMatch ? decodeURIComponent(csrfMatch[1]) : '<?php echo $this->security->get_csrf_hash(); ?>';
    var url = '<?php echo base_url(); ?>manager/ajax_mark_notifications_read';
    var postData = {
      '<?php echo $this->security->get_csrf_token_name(); ?>': csrfToken
    };
    if (notifId) {
      postData.notif_id = notifId;
    }
    $.ajax({
      url: url,
      type: 'POST',
      data: postData,
      dataType: 'json',
      success: function() {
        loadNotifications();
      },
      error: function() {
        var getUrl = url + (notifId ? '?notif_id=' + encodeURIComponent(notifId) : '');
        $.getJSON(getUrl, function() {
          loadNotifications();
        });
      }
    });
  }

  $(document).on('click', '#notifBellToggle', function(e) {
    e.preventDefault();
    e.stopPropagation();
    if (window.Notification && Notification.permission === 'default') {
      Notification.requestPermission().then(function() {
        checkNotifPermBtn();
      });
    }
    $('#googleProfileDropdown').fadeOut(100);
    $('#smartphoneNotifDropdown').fadeToggle(150);
  });

  $(document).on('click', '#btnMarkNotifRead', function(e) {
    e.preventDefault();
    e.stopPropagation();
    $('#notifBadge').hide();
    $('.notif-item-link').each(function() {
      $(this).attr('data-unread', '0').css({
        'border': '1px solid #e2e8f0',
        'background': '#f8fafc'
      });
      $(this).find('.btn-single-notif-read').remove();
    });
    sendMarkNotifRead('');
  });

  $(document).on('click', '.btn-single-notif-read', function(e) {
    e.preventDefault();
    e.stopPropagation();
    var nid = $(this).attr('data-notif-id');
    var $card = $(this).closest('.notif-item-link');
    $card.attr('data-unread', '0').css({
      'border': '1px solid #e2e8f0',
      'background': '#f8fafc'
    });
    $(this).remove();
    var curCount = parseInt($('#notifBadge').text()) || 0;
    if (curCount > 1) {
      $('#notifBadge').text(curCount - 1);
    } else {
      $('#notifBadge').hide();
    }
    sendMarkNotifRead(nid);
  });

  $(document).on('click', '.notif-item-link', function(e) {
    if ($(this).attr('data-unread') === '1') {
      var nid = $(this).attr('data-notif-id');
      sendMarkNotifRead(nid);
    }
  });
});
</script>
<div class="clearfix"></div>

<aside class="main-sidebar"> 
  
  <div class="crm-sidebar-header">
    <img src="<?php echo base_url(); ?>assets/logo.png" alt="7 Strokes" />
    <button type="button" class="crm-sidebar-close" id="crmSidebarClose" aria-label="Close navigation">&times;</button>
  </div>
  
  
  
  <section class="sidebar">
    <ul class="sidebar-menu">
      <?php  
        $route1=$this->uri->segment(2);
	      $route2=$this->uri->segment(3);
        $m_role = (int)$this->session->userdata('manager_role');
        $current_user_id = (int)$this->session->userdata('manager_id');
        if (empty($m_role) && $current_user_id === 1) {
            $m_role = 1;
        }
        $is_admin = ($m_role === 1 || $current_user_id === 1);
        $is_support = ($m_role === 4);
        $user_team_count = $current_user_id ? count($this->ci_admin_model->fetchTeamList($current_user_id, '')) : 0;
        $can_see_review = ($is_admin || $m_role === 2 || $m_role === 4 || has_section_perm('back_office') || has_section_perm('lead_review'));
      ?>
      <?php if($is_admin || has_section_perm('dashboard')){ ?>
      <li class="<?php if($route1=="dashboard"){ echo "active";}else{ echo "";}?>"> 
        <a href="<?php echo base_url();?>manager/dashboard"> <svg class="crm-svg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-2px; margin-right:6px;"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="5" rx="1"></rect><rect x="14" y="12" width="7" height="9" rx="1"></rect><rect x="3" y="16" width="7" height="5" rx="1"></rect></svg> <span>Dashboard</span> </a> 
      </li>
      <?php } ?>

      <li class="<?php if($route1=="calls"){ echo "active";}else{ echo "";}?>"> 
        <a href="<?php echo base_url();?>manager/calls"> <i class="fa fa-phone" style="margin-right:6px; color:#2563eb;"></i> <span>Phone Dialer</span> </a> 
      </li>

      <?php if($is_admin || has_section_perm('my_zone') || has_section_perm('database') || has_section_perm('leads') || has_section_perm('tasks') || has_section_perm('internal_tasks') || has_section_perm('external_tasks')){ ?>
      <li class="<?php if(($route1=="leads" && $route2!="review_leads") || $route1=="verticals" || $route1=="database-transfer" || in_array($route1, array('tasks')) || ($route1=="orders" && ($route2=="my" || (isset($_GET['scope']) && $_GET['scope']=="my"))) || $route1=="sales"){ echo "active";}else{ echo "";}?> treeview"> 
        <a href="javascript:void(0);"><svg class="crm-svg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-2px; margin-right:6px;"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg> <span>My Zone</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right sidebar-arrow"></i></span> </a>
        <ul class="treeview-menu">
          <?php if($is_admin || has_section_perm('database', 'write')){ ?>
          <li><a href="<?php echo base_url();?>manager/leads/add"><i class="fa fa-plus"></i>Add Database</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('database')){ ?>
          <li><a href="<?php echo base_url();?>manager/leads"><i class="fa fa-database"></i>View Database</a></li>
          <li><a href="<?php echo base_url();?>manager/leads/view_leads"><i class="fa fa-bullseye"></i>View Leads</a></li>
          <li><a href="<?php echo base_url();?>manager/orders/my"><i class="fa fa-shopping-cart"></i>View Orders</a></li>
          <li><a href="<?php echo base_url();?>manager/sales"><i class="fa fa-line-chart"></i>View Sales</a></li>
          <li><a href="<?php echo base_url();?>manager/leads/followups"><i class="fa fa-clock-o"></i>My Follow-Up</a></li>
          <li><a href="<?php echo base_url();?>manager/leads/meetings"><i class="fa fa-calendar"></i>My Meetings</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('database', 'write')){ ?>
          <li><a href="<?php echo base_url();?>manager/verticals/add"><i class="fa fa-plus"></i>Add Vertical</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('database')){ ?>
          <li><a href="<?php echo base_url();?>manager/verticals"><i class="fa fa-list"></i>View Vertical</a></li>
          <li><a href="<?php echo base_url();?>manager/verticals/followups"><i class="fa fa-clock-o"></i>Vertical Follow-up</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('internal_tasks') || has_section_perm('tasks')){ ?>
          <li><a href="<?php echo base_url();?>manager/tasks/internal"><i class="fa fa-building-o"></i>View Internal Task</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('external_tasks') || has_section_perm('tasks')){ ?>
          <li><a href="<?php echo base_url();?>manager/tasks/external"><i class="fa fa-briefcase"></i>View External Task</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('database')){ ?>
          <li><a href="<?php echo base_url();?>manager/database-transfer"><i class="fa fa-exchange"></i>Database Transfer</a></li>
          <?php } ?>
        </ul>
      </li>
      <?php } ?>

      <?php if($is_admin || has_section_perm('team')){ ?>
      <li class="<?php if(in_array($route1, array('team')) || ($route1=="orders" && ($route2=="team" || (isset($_GET['scope']) && $_GET['scope']=="team")))){ echo "active";}else{ echo "";}?> treeview"> 
        <a href="javascript:void(0);"><svg class="crm-svg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-2px; margin-right:6px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg> <span>Team</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right sidebar-arrow"></i></span> </a>
        <ul class="treeview-menu">
          <li><a href="<?php echo base_url();?>manager/team/chart"><i class="fa fa-sitemap"></i>Chart</a></li>
          <li><a href="<?php echo base_url();?>manager/team/database"><i class="fa fa-database"></i>Database</a></li>
          <li><a href="<?php echo base_url();?>manager/team/leads"><i class="fa fa-list"></i>Leads</a></li>
          <li><a href="<?php echo base_url();?>manager/team/orders"><i class="fa fa-shopping-cart"></i>Orders</a></li>
          <li><a href="<?php echo base_url();?>manager/team/verticals"><i class="fa fa-list"></i>Verticals</a></li>
          <li><a href="<?php echo base_url();?>manager/team/followups"><i class="fa fa-clock-o"></i>Follow up</a></li>
          <li><a href="<?php echo base_url();?>manager/team/meetings"><i class="fa fa-calendar"></i>Meetings</a></li>
          <li><a href="<?php echo base_url();?>manager/team/assignmeetings"><i class="fa fa-calendar-check-o"></i>Assigned Meetings</a></li>
          <li><a href="<?php echo base_url();?>manager/team/assignleads"><i class="fa fa-tasks"></i>Assign Database</a></li>
          <li><a href="<?php echo base_url();?>manager/tasks"><i class="fa fa-tasks"></i>Task</a></li>
          <li><a href="<?php echo base_url();?>manager/team/tickets"><i class="fa fa-ticket"></i>Tickets</a></li>
        </ul>
      </li>
      <?php } ?>

      <?php if($can_see_review){ ?>
      <li class="<?php if(($route1=="leads" && $route2=="review_leads") || ($route1=="tasks" && $route2=="external" && isset($_GET['review'])) || $route1=="back-office" || ($route1=="orders" && $route2!="my" && $route2!="team" && (!isset($_GET['scope']) || !in_array($_GET['scope'], array('my', 'team'))))){ echo "active";}else{ echo "";}?> treeview"> 
        <a href="javascript:void(0);"><svg class="crm-svg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-2px; margin-right:6px;"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg> <span>Back Office</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right sidebar-arrow"></i></span> </a>
        <ul class="treeview-menu">
          <li><a href="<?php echo base_url();?>manager/leads/review_leads"><i class="fa fa-check-square-o"></i>Pending Leads Approval</a></li>
          <li><a href="<?php echo base_url();?>manager/orders/pending_process"><i class="fa fa-hourglass-half"></i>Pending Order Process</a></li>
          <li><a href="<?php echo base_url();?>manager/orders/pending_sales_approval"><i class="fa fa-clock-o"></i>Pending Sales Approval</a></li>
          <?php if($is_admin || has_section_perm('kam', 'write')){ ?>
          <li><a href="<?php echo base_url();?>manager/kam/onboard"><i class="fa fa-user-plus"></i>Onboard Client</a></li>
          <?php } ?>
          <li><a href="<?php echo base_url();?>manager/orders/open"><i class="fa fa-folder-open-o"></i>Open Orders</a></li>
          <li><a href="<?php echo base_url();?>manager/orders/followups"><i class="fa fa-calendar-check-o"></i>Orders Follow-up</a></li>
          <li><a href="<?php echo base_url();?>manager/orders?status=completed"><i class="fa fa-check-circle"></i>Completed Orders</a></li>
          <li><a href="<?php echo base_url();?>manager/orders?status=closed"><i class="fa fa-flag-checkered"></i>Closed Orders</a></li>
          <li><a href="<?php echo base_url();?>manager/orders?status=rejected_lost"><i class="fa fa-times-circle"></i>Rejected / Lost Orders</a></li>
          <li><a href="<?php echo base_url();?>manager/orders"><i class="fa fa-list"></i>View All Orders</a></li>
        </ul>
      </li>
      <?php } ?>

      <?php if($is_admin || has_section_perm('kam')){ ?>
      <li class="<?php if($route1=="kam"){ echo "active";}else{ echo "";}?> treeview"> 
        <a href="javascript:void(0);"><svg class="crm-svg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-2px; margin-right:6px;"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg> <span>KAM</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right sidebar-arrow"></i></span> </a>
        <ul class="treeview-menu">
          <li><a href="<?php echo base_url();?>manager/kam/my-client"><i class="fa fa-user"></i>My Client</a></li>
          <?php if($is_admin || has_section_perm('kam', 'write')){ ?>
          <li><a href="<?php echo base_url();?>manager/kam/onboard"><i class="fa fa-user-plus"></i>Onboard Client</a></li>
          <li><a href="<?php echo base_url();?>manager/kam/schedule"><i class="fa fa-calendar"></i>Create Schedule</a></li>
          <?php } ?>
          <li><a href="<?php echo base_url();?>manager/kam/task-followup"><i class="fa fa-tasks"></i>Task Follow-up</a></li>
          <li><a href="<?php echo base_url();?>manager/kam/reminders"><i class="fa fa-bell-o"></i>Reminders</a></li>
        </ul>
      </li>
      <?php } ?>

      <?php if($is_hr){ ?>
      <li class="<?php if($route1=="hr"){ echo "active menu-open";}else{ echo "";}?> treeview"> 
        <a href="javascript:void(0);"><svg class="crm-svg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-2px; margin-right:6px;"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><line x1="15" y1="8" x2="17" y2="8"></line><line x1="15" y1="12" x2="17" y2="12"></line><line x1="7" y1="16" x2="17" y2="16"></line></svg> <span>HR</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right sidebar-arrow"></i></span> </a>
        <ul class="treeview-menu" style="<?php if($route1=="hr"){ echo "display: block;"; } ?>">
          <li><a href="<?php echo base_url();?>manager/hr"><i class="fa fa-th-large"></i>Overview</a></li>
          <li><a href="<?php echo base_url();?>manager/hr/targets"><i class="fa fa-crosshairs"></i>Targets</a></li>
          <li><a href="<?php echo base_url();?>manager/hr/lifecycle"><svg class="crm-svg-ico" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-2px; margin-right:6px;"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg>Employee Lifecycle</a></li>
          <li><a href="<?php echo base_url();?>manager/hr/attendance"><i class="fa fa-map-marker"></i>Attendance & GPS</a></li>
          <li><a href="<?php echo base_url();?>manager/hr/shifts"><i class="fa fa-clock-o"></i>Shifts</a></li>
          <li><a href="<?php echo base_url();?>manager/hr/leaves"><i class="fa fa-calendar-check-o"></i>Leaves</a></li>
          <li><a href="<?php echo base_url();?>manager/hr/payroll"><i class="fa fa-money"></i>Payroll</a></li>
          <li><a href="<?php echo base_url();?>manager/hr/taxation"><i class="fa fa-calculator"></i>Taxation & Gratuity</a></li>
          <li><a href="<?php echo base_url();?>manager/hr/appraisals"><i class="fa fa-star-o"></i>Appraisals</a></li>
          <li><a href="<?php echo base_url();?>manager/hr/documents"><i class="fa fa-folder-open-o"></i>Document Vault</a></li>
        </ul>
      </li>
      <?php } ?>

      <?php if($is_admin || has_section_perm('system')){ ?>
      <li class="<?php if($route1=="roles" || $route1=="terms" || $route1=="users" || $route1=="assigned-leads" || $route1=="admin-logs" || ($route1=="system" && ($route2=="calling-api" || $route2=="calling_api" || $route2=="integrations")) || $route1=="integrations" || ($route1=="leads" && in_array($route2, array("import", "export")))){ echo "active";}else{ echo "";}?> treeview"> 
        <a href="javascript:void(0);"><svg class="crm-svg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-2px; margin-right:6px;"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg> <span>System</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right sidebar-arrow"></i></span> </a>
        <ul class="treeview-menu">        
          <?php if($is_admin || has_section_perm('users')){ ?>
          <li><a href="<?php echo base_url();?>manager/users/add"><i class="fa fa-plus"></i>User Add</a></li>
          <li><a href="<?php echo base_url();?>manager/users"><i class="fa fa-list"></i>User View</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('roles')){ ?>
          <li><a href="<?php echo base_url();?>manager/roles"><i class="fa fa-list"></i>Roles</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('terms')){ ?>
          <li><a href="<?php echo base_url();?>manager/terms"><i class="fa fa-list"></i>Category</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('leads_import')){ ?>
          <li><a href="<?php echo base_url();?>manager/leads/import"><i class="fa fa-upload"></i>Import Leads</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('leads_export')){ ?>
          <li><a href="<?php echo base_url();?>manager/leads/export"><i class="fa fa-download"></i>Export Leads</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('assigned_leads')){ ?>
          <li><a href="<?php echo base_url();?>manager/assigned-leads"><i class="fa fa-tasks"></i>Allotted Database</a></li>
          <li><a href="<?php echo base_url();?>manager/assigned-leads?alloc_filter=non_allotted"><i class="fa fa-folder-open-o"></i>Non-Allotted Database</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('admin_logs')){ ?>
          <li><a href="<?php echo base_url();?>manager/admin-logs"><i class="fa fa-history"></i>Admin Logs</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('system_assignee') || has_section_perm('system')){ ?>
          <li><a href="<?php echo base_url();?>manager/system/assign"><i class="fa fa-user-plus"></i>Assign</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('kam_clients') || has_section_perm('system')){ ?>
          <li><a href="<?php echo base_url();?>manager/system/kam_clients"><i class="fa fa-briefcase"></i>KAM Client</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('calling_api')){ ?>
          <li><a href="<?php echo base_url();?>manager/system/calling-api"><i class="fa fa-phone-square"></i>Calling API</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('integrations')){ ?>
          <li><a href="<?php echo base_url();?>manager/system/integrations"><i class="fa fa-plug"></i>Third-Party API</a></li>
          <?php } ?>
          <?php if($is_admin || has_section_perm('system')){ ?>
          <li><a href="<?php echo base_url();?>manager/system/qc"><i class="fa fa-check-square-o"></i>QC Panel</a></li>
          <?php } ?>
        </ul>
      </li>
      <?php } ?>

      <?php if($is_admin || has_section_perm('reports')){ ?>
      <li class="<?php if($route1=="reports" || $route1=="download-report" || $route1=="admin-reports" || $route1=="revenue-list" || $route1=="company-projections"){ echo "active menu-open";}else{ echo "";}?> treeview"> 
        <a href="javascript:void(0);"><svg class="crm-svg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-2px; margin-right:6px;"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg> <span>Reports</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right sidebar-arrow"></i></span> </a>
        <ul class="treeview-menu" style="<?php if($route1=="reports" || $route1=="download-report" || $route1=="admin-reports" || $route1=="revenue-list" || $route1=="company-projections"){ echo "display: block;"; } ?>">
          <li><a href="<?php echo base_url();?>manager/reports/revenue-list"><i class="fa fa-money"></i>Revenue List</a></li>
          <li><a href="<?php echo base_url();?>manager/reports/company-projections"><i class="fa fa-line-chart"></i>Company Projections</a></li>
          <li><a href="https://7strokes.maxvendor47.workers.dev/" target="_blank"><i class="fa fa-database"></i>Generate Data</a></li>
          <li><a href="<?php echo base_url();?>manager/download-report"><i class="fa fa-file-pdf-o"></i>Report</a></li>
        </ul>
      </li>
      <?php } ?>
    </ul>
  </section>
  
   
  
</aside>
