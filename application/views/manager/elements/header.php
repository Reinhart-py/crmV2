<?php $crm_view_now = function_exists('crm_view') ? crm_view() : 'classic'; $crm_is_modern = ($crm_view_now === 'modern'); ?>
<!DOCTYPE html>
<html<?php echo $crm_is_modern ? ' class="crm-modern"' : ''; ?>>
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

    
<title><?php echo htmlspecialchars((string)$info['title'], ENT_QUOTES, 'UTF-8', false); ?></title>

<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/slimmenu.min.css" >   
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/font-awesome.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/ionicons.min.css">

<script src="<?php echo base_url(); ?>assets/plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/jQueryUI/jquery-ui.js"></script>


<link rel="stylesheet" href="<?php echo base_url();?>assets/bootstrap/spacing.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/dist/css/skins/_all-skins.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/dist/css/skins/skin-yellow.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/select2/select2.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/dist/css/AdminLTE.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/dist/css/x_grid.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/datepicker/datepicker3.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/timepicker/bootstrap-timepicker.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/datatables/dataTables.bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.2.7/css/rowReorder.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.5/css/responsive.dataTables.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/bootstrap/developer.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/multiselect/multiselect.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/dist/css/crm-dashboard.css?v=20261105_3">
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/plugins/table_export/css/tableexport.css" />

<script src="<?php echo base_url();?>assets/plugins/table_export/FileSaver.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/table_export/Blob.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/table_export/xls.core.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/table_export/js/tableexport.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/plugins/jQueryUI/jquery-ui.css?v=20261107">
<script src="<?php echo base_url();?>assets/bootstrap/js/jquery.validate.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/dist/css/crm-theme.css?v=20261108_1">
<link rel="stylesheet" href="<?php echo base_url();?>assets/dist/css/crm-modes.css?v=20261110_5">
<?php if ($crm_is_modern) { ?><link rel="stylesheet" href="<?php echo base_url();?>assets/dist/css/crm-modern.css?v=20261110_21"><?php } ?>
<link rel="stylesheet" href="<?php echo base_url();?>assets/dist/css/crm-search.css?v=20261107_4">
<script src="<?php echo base_url();?>assets/dist/js/crm-search.js?v=20261107_2" defer></script>




<script>
(function() {
    var theme = localStorage.getItem('crm_theme_preference');
    if (theme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
    }
<?php if ($crm_is_modern) { ?>
    try { if (localStorage.getItem('crm_side_mini') === '1') document.documentElement.classList.add('crm-side-mini'); } catch (e) {}
<?php } ?>
})();
</script>
</head>
<body class="hold-transition skin-blue sidebar-mini<?php if ($crm_is_modern) { echo ' crm-modern'; $crm_seg2 = (string)$this->uri->segment(2); if ($crm_seg2 === '' || $crm_seg2 === 'dashboard') echo ' crm-dash-page'; } ?>">
<div class="wrapper">

<header class="main-header"> 

<div class="main-header-bar">
  
  <button type="button" class="crm-mobile-hamburger" id="crmMobileHamburger" aria-label="Toggle navigation">
    <i class="fa fa-bars"></i>
  </button>

  <div class="main-logo">
    <a href="<?php echo base_url(); ?>manager/dashboard"> 
      <img src="<?php echo base_url(); ?>assets/logo-header.png" alt="7 Strokes" class="logo-light" />
      <img src="<?php echo base_url(); ?>assets/logo-header-dark.png" alt="7 Strokes" class="logo-dark" />
    </a> 
  </div>

  
  <!-- Live, Search and Notifications sit next to the logo; the rest stay with the profile photo. -->
  <?php
    // Same values the menu below works out; needed here first for the Live and Search buttons.
    $route1 = $this->uri->segment(2);
    $m_role = (int)$this->session->userdata('manager_role');
    $current_user_id = (int)$this->session->userdata('manager_id');
    if (empty($m_role) && $current_user_id === 1) {
        $m_role = 1;
    }
    $is_admin = ($m_role === 1 || $current_user_id === 1);
    $is_presentation = crm_is_presentation_user($m_role);
  ?>
  <div class="header-quick-actions hqa-left">
    <?php if($is_admin || has_section_perm('live')){ ?>
    <a href="<?php echo base_url();?>manager/live" class="live-nav-badge <?php echo ($route1 === 'live') ? 'active' : ''; ?>" title="Live Operations Report" style="display:inline-flex; align-items:center; justify-content:center; text-decoration:none; margin-right:8px; padding:4px 12px; border-radius:6px; font-weight:700; font-size:12px; letter-spacing:0.06em; transition:all 0.2s ease;">LIVE</a>
    <?php } ?>
    <?php if(!$is_presentation){ ?>
    <div class="gs-wrap" id="gsWrap">
      <button type="button" class="global-theme-btn" id="gsToggle" title="Search (press /)" aria-label="Search records" aria-expanded="false" style="margin-right:6px;"><i class="fa fa-search"></i></button>
      <div class="gs-panel" id="gsPanel" role="dialog" aria-label="Search" hidden>
        <div class="gs-bar">
          <i class="fa fa-search" aria-hidden="true"></i>
          <input type="search" id="gsInput" autocomplete="off" spellcheck="false" placeholder="Company, name, phone, e-mail or number" aria-label="Search" aria-controls="gsResults">
          <kbd class="gs-kbd">Esc</kbd>
        </div>
        <div class="gs-results" id="gsResults" role="listbox"><div class="gs-hint">Type at least 2 letters. Finds Database &amp; Leads, Orders and Tickets you can open.</div></div>
      </div>
    </div>
    <?php } ?>
    <div class="notif-dropdown-wrap" style="position:relative; display:inline-block; margin-right:6px;">
      <button type="button" class="global-theme-btn" id="notifBellToggle" title="Notifications" style="position:relative; display:inline-flex; align-items:center; justify-content:center;">
        <i class="fa fa-bell-o"></i>
        <span class="notif-badge" id="notifBadge" style="display:none; position:absolute; top:-4px; right:-4px; background:#dc2626; color:#ffffff; font-size:10px; font-weight:700; border-radius:10px; min-width:16px; height:16px; line-height:16px; text-align:center; padding:0 3px;"></span>
      </button>
      <div class="smartphone-notif-dropdown" id="smartphoneNotifDropdown" style="display:none; position:absolute; right:0; top:44px; width:440px; max-width:calc(100vw - 24px); background:#ffffff; border:1px solid #cbd5e1; border-radius:4px; box-shadow:0 12px 32px rgba(0,0,0,0.12); z-index:999999; overflow:hidden;">
        <div style="padding:12px 16px; background:#1e3a8a; color:#ffffff; display:flex; align-items:center; justify-content:space-between;">
          <span style="font-weight:700; font-size:13.5px; letter-spacing:-0.2px;">Notification Centre</span>
          <div style="display:flex; align-items:center; gap:8px;">
            <button type="button" id="btnRequestNotifPerm" style="background:rgba(255,255,255,0.2); border:none; color:#ffffff; font-size:11px; font-weight:600; padding:4px 10px; border-radius:3px; cursor:pointer; display:none;">Enable Alerts</button>
            <button type="button" id="btnMarkNotifRead" style="background:rgba(255,255,255,0.2); border:none; color:#ffffff; font-size:11px; font-weight:600; padding:4px 10px; border-radius:3px; cursor:pointer;">Mark all as read</button>
          </div>
        </div>
        <div id="notifListContainer" style="max-height:440px; overflow-y:auto; padding:8px 10px; background:#f8fafc;">
          <div style="padding:28px 16px; text-align:center; color:#94a3b8; font-size:12px;">Loading notifications...</div>
        </div>
      </div>
    </div>
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
        $is_support = (!$is_admin && crm_can('support_desk'));
        $is_presentation = crm_is_presentation_user($m_role);
        $user_team_count = $current_user_id ? count($this->ci_admin_model->fetchTeamList($current_user_id, '')) : 0;
        $can_see_review = ($is_admin || has_section_perm('back_office') || has_section_perm('lead_review'));
        $is_hr = ($is_admin || has_section_perm('hr'));

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
            <?php if($is_presentation){ ?>
      <li class="<?php if($route1=='profile'){ echo 'active';}?>"><a href="<?php echo base_url();?>manager/profile"><i class="fa fa-user" style="margin-right:6px;"></i> <span>Profile</span></a></li>
      <li class="<?php if($route1=='live'){ echo 'active';}?>"><a href="<?php echo base_url();?>manager/live"><i class="fa fa-television" style="margin-right:6px;"></i> <span>Live</span></a></li>
      <?php } ?>
      <?php if(!$is_presentation && crm_can('dashboard')){ ?>
      <li class="<?php if($route1=="dashboard"){ echo "active";}else{ echo "";}?>"> <a href="<?php echo base_url();?>manager/dashboard"> <svg class="crm-svg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-2px; margin-right:4px;"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="5" rx="1"></rect><rect x="14" y="12" width="7" height="9" rx="1"></rect><rect x="3" y="16" width="7" height="5" rx="1"></rect></svg> <span>Dashboard</span>  </a> </li>
      <?php } ?>

      <?php
        // Menu built from the permission catalogue (application/helpers/crm_permission_helper.php):
        // a link shows only when the person has the right that opens its page.
        if (!isset($crm_menu)) {
          $crm_menu = $is_presentation ? array() : crm_nav_menu();
          $crm_path = implode('/', array_slice($this->uri->segment_array(), 1));
          $crm_active = crm_nav_active_group($crm_menu, $crm_path, $_GET);
        }
      ?>
      <?php foreach ($crm_menu as $crm_gk => $crm_g) { ?>
      <li class="<?php echo $crm_gk === $crm_active ? 'active' : ''; ?>"> <a href="javascript:void(0);"><svg class="crm-svg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-2px; margin-right:4px;"><?php echo $crm_g['icon']; ?></svg> <span><?php echo htmlspecialchars($crm_g['title']); ?></span> </a>
        <ul>
          <?php foreach ($crm_g['links'] as $crm_l) { ?>
          <li><a href="<?php echo $crm_l['external'] ? $crm_l['url'] : base_url() . 'manager/' . $crm_l['url']; ?>"<?php echo $crm_l['external'] ? ' target="_blank" rel="noopener"' : ''; ?>><i class="fa <?php echo $crm_l['icon']; ?>"></i><?php echo htmlspecialchars($crm_l['label']); ?></a></li>
          <?php } ?>
        </ul>
      </li>
      <?php } ?>

      <?php if (function_exists('chatbot_can') && chatbot_can('access')) { ?>
      <li class="<?php echo ($route1 == 'chatbot' || $route1 == 'live-support') ? 'active' : ''; ?>"> <a href="javascript:void(0);"><svg class="crm-svg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-2px; margin-right:4px;"><path d="M12 8V4H8"></path><rect x="4" y="8" width="16" height="12" rx="2"></rect><path d="M2 14h2"></path><path d="M20 14h2"></path><path d="M9 13v2"></path><path d="M15 13v2"></path></svg> <span>Chatbot</span> <span class="cb-unread" style="display:none;"></span> </a>
        <ul>
          <li><a href="<?php echo base_url();?>manager/chatbot"><i class="fa fa-comments"></i>Open Chats <span class="cb-unread" style="display:none;"></span></a></li>
          <li><a href="<?php echo base_url();?>manager/chatbot/closed"><i class="fa fa-check-circle-o"></i>Closed Chats</a></li>
          <?php if (chatbot_can('history')) { ?><li><a href="<?php echo base_url();?>manager/chatbot/history"><i class="fa fa-history"></i>Chat History</a></li><?php } ?>
          <?php if (chatbot_can('settings')) { ?><li><a href="<?php echo base_url();?>manager/chatbot/settings"><i class="fa fa-sliders"></i>Settings</a></li><?php } ?>
        </ul>
      </li>
      <?php } ?>

    </ul>
  </nav>

  <div class="header-quick-actions">
    <?php if(!$is_presentation && has_section_perm('calls')){ ?>
    <a href="<?php echo base_url();?>manager/calls" class="global-theme-btn" title="Phone Dialer & Calls" style="display:inline-flex; align-items:center; justify-content:center; text-decoration:none; margin-right:6px; color:#1e3a8a;">
      <i class="fa fa-phone"></i>
    </a>
    <?php } ?>
    <?php if(!$is_presentation && crm_can('data_creation')){ ?>
    <a href="<?php echo base_url();?>manager/data-creation" class="global-theme-btn" title="Data Creation" style="display:inline-flex; align-items:center; justify-content:center; text-decoration:none; margin-right:6px; color:#1e3a8a;">
      <i class="fa fa-database"></i>
    </a>
    <?php } ?>
    <button type="button" class="global-theme-btn" id="globalThemeToggle" title="Toggle Dark/Light Mode">
      <i class="fa fa-moon-o"></i>
    </button>

    <div class="profile-menu-container">
      <button type="button" class="profile-avatar-btn" id="profileMenuToggle" aria-haspopup="true" aria-label="Profile" title="Profile">
        <img src="<?php echo base_url() . $user_avatar; ?>" alt="<?php echo htmlspecialchars($current_user_name); ?>" class="profile-avatar-img" />
        <span class="my-sup-unread" id="mySupAvatarBadge" style="display:none; position:absolute; top:-4px; right:-4px;"></span>
      </button>
      <button type="button" class="profile-caret-btn" id="profileMenuCaret" aria-haspopup="true" aria-expanded="false" aria-label="Profile" title="Profile"><span class="crm-collapse-caret" aria-hidden="true"></span></button>

      <div class="google-profile-dropdown" id="googleProfileDropdown">
        <div class="google-profile-card">
          <div class="google-avatar-wrap">
            <img src="<?php echo base_url() . $user_avatar; ?>" alt="<?php echo htmlspecialchars($current_user_name); ?>" class="google-profile-large-img" />
          </div>
          <div class="google-greeting-name">Hi, <?php echo htmlspecialchars($current_user_name); ?></div>
          <?php if (!empty($current_user_role_name)) { ?>
            <div class="google-user-role" style="font-size:12px; font-weight:700; color:#1e3a8a; margin-top:2px; text-transform:uppercase; letter-spacing:0.5px;"><?php echo htmlspecialchars($current_user_role_name); ?></div>
          <?php } ?>
          <?php if (!empty($current_user_email)) { ?>
            <div class="google-user-email"><?php echo htmlspecialchars($current_user_email); ?></div>
          <?php } ?>
        </div>

        <div class="google-menu-divider"></div>

        <ul class="google-profile-options">
          <li>
            <a href="<?php echo base_url(); ?>manager/profile" class="google-profile-item">
              <i class="fa fa-user"></i>
              <span>View Profile</span>
            </a>
          </li>
          <li>
            <a href="<?php echo base_url(); ?>manager/hr/me" class="google-profile-item">
              <i class="fa fa-briefcase"></i>
              <span>My HR</span>
            </a>
          </li>
          <?php if (crm_can('dashboard')) { ?>
          <li><?php require(APPPATH . 'views/manager/dash/style_switch.php'); ?></li>
          <?php } ?>
          <?php if (!$is_presentation) { ?>
          <li><?php require(APPPATH . 'views/manager/dash/view_switch.php'); ?></li>
          <?php } ?>
          <li>
            <a href="<?php echo base_url(); ?>manager/password" class="google-profile-item">
              <i class="fa fa-key"></i>
              <span>Change Password</span>
            </a>
          </li>
          <li>
            <a href="<?php echo base_url(); ?>manager/events" class="google-profile-item">
              <i class="fa fa-calendar"></i>
              <span>Events &amp; Festivals</span>
            </a>
          </li>
          <?php if (crm_can('help_support', 'read')) { ?>
          <li>
            <a href="<?php echo base_url(); ?>manager/help" class="google-profile-item">
              <i class="fa fa-life-ring"></i>
              <span>Help &amp; Support</span>
              <span class="my-sup-unread" style="display:none; margin-left:auto;"></span>
            </a>
          </li>
          <?php } ?>
          <?php if (crm_can('self_training', 'read') || crm_can('hr_training', 'write')) { ?>
          <li>
            <a href="<?php echo base_url(); ?>manager/self-training" class="google-profile-item">
              <i class="fa fa-graduation-cap"></i>
              <span>Self Training</span>
            </a>
          </li>
          <?php } ?>
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

  // Profile picture and arrow both open the account menu (View Profile is inside it).
  $(document).on('click', '#profileMenuToggle, #profileMenuCaret', function(e) {
    e.preventDefault();
    e.stopPropagation();
    var open = !$('#googleProfileDropdown').is(':visible');
    $('#googleProfileDropdown').fadeToggle(150);
    $('#profileMenuCaret').attr('aria-expanded', open ? 'true' : 'false');
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

  // One background check every 20 s: notifications, support replies and waiting chats (manager/pulse)
  var pulseFirst = true, pulseTimer = null;
  function loadNotifications() {
    var q = {};
    if (/\/manager\/(my-support|help)\/?$/.test(location.pathname)) q.sup = 0; // the chat page marks replies read itself
    if (!pulseFirst && /\/manager\/chatbot\/?$/.test(location.pathname)) q.cb = 0; // the chatbot page refreshes its own list
    pulseFirst = false;
    $.ajax({
      url: '<?php echo base_url(); ?>manager/pulse',
      type: 'GET',
      data: q,
      cache: false,
      dataType: 'json',
      success: function(p) {
        if (!p || !p.ok) { if (p && p.signed_out && pulseTimer) { clearInterval(pulseTimer); pulseTimer = null; } return; }
        if (p.sup !== undefined && window.crmMySupBadge) window.crmMySupBadge(p.sup);
        if (p.cb !== undefined && window.crmCbBadge) window.crmCbBadge(p.cb);
        var res = p.notif;
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
              var cardBorder = isUnread ? 'border:1px solid #bfdbfe; border-left:4px solid #1e3a8a;' : 'border:1px solid #e2e8f0;';
              var cardBg = isUnread ? '#ffffff' : '#f8fafc';
              html += '<a href="' + (n.link || 'javascript:void(0);') + '" class="notif-item-link" data-notif-id="' + n.id + '" data-unread="' + (isUnread ? '1' : '0') + '" style="display:block; margin-bottom:8px; padding:10px 14px; border-radius:4px; background:' + cardBg + '; ' + cardBorder + ' box-shadow:0 1px 2px rgba(0,0,0,0.04); text-decoration:none; color:inherit; transition:background 0.15s ease;">';
              html += '  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">';
              var cleanTitle = (n.title && n.title.toLowerCase() !== 'notification' && n.title.trim() !== n.message.trim()) ? $('<div>').text(n.title).html() : '';
              if (cleanTitle) {
                html += '    <span style="font-size:12px; font-weight:700; color:#1e3a8a; letter-spacing:-0.1px;">' + cleanTitle + '</span>';
              } else {
                html += '    <span></span>';
              }
              html += '    <div style="display:inline-flex; align-items:center; gap:6px;">';
              if (isUnread) {
                html += '      <button type="button" class="btn-single-notif-read" data-notif-id="' + n.id + '" title="Mark as read" style="background:rgba(0,114,240,0.1); border:none; border-radius:3px; padding:2px 6px; color:#1e3a8a; font-size:10px; font-weight:600; cursor:pointer; line-height:1.2;">Mark read</button>';
              }
              html += '      <span style="font-size:10.5px; color:#94a3b8; font-weight:500; white-space:nowrap;">' + (n.time_ago || '') + '</span>';
              html += '    </div>';
              html += '  </div>';
              html += '  <div style="font-size:12.5px; color:#1e293b; line-height:1.45; white-space:normal; word-wrap:break-word; word-break:break-word;">' + $('<div>').text(n.message).html() + '</div>';
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
  pulseTimer = setInterval(loadNotifications, 20000);

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
    var href = $(this).attr('href');
    var isUnread = $(this).attr('data-unread') === '1';
    var nid = $(this).attr('data-notif-id');
    if (isUnread && nid) {
      if (navigator.sendBeacon) {
        var fd = new FormData();
        fd.append('notif_id', nid);
        fd.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');
        navigator.sendBeacon('<?php echo base_url(); ?>manager/ajax_mark_notifications_read', fd);
      } else {
        sendMarkNotifRead(nid);
      }
    }
    if (href && href !== '#' && href !== 'javascript:void(0);') {
      window.location.href = href;
    }
  });
});
</script>
<style>.my-sup-unread{background:#dc2626;color:#fff;font-size:10px;font-weight:700;border-radius:10px;min-width:16px;height:16px;line-height:16px;text-align:center;padding:0 4px;}#profileMenuToggle{position:relative;}</style>
<script>
(function () {
  window.crmMySupBadge = function (n) {
    n = parseInt(n, 10) || 0;
    $('.my-sup-unread').text(n > 99 ? '99+' : n).css('display', n > 0 ? 'inline-block' : 'none');
  };
  // refreshed by the header's single background check (manager/pulse)
})();
</script>
<?php if (function_exists('chatbot_can') && chatbot_can('access')) { ?>
<style>.cb-unread{display:inline-block;background:#dc2626;color:#fff;font-size:10px;font-weight:700;border-radius:10px;min-width:16px;height:16px;line-height:16px;text-align:center;padding:0 4px;margin-left:4px;vertical-align:1px;}</style>
<script>
(function () {
  window.crmCbBadge = function (n) {
    n = parseInt(n, 10) || 0;
    $('.cb-unread').not('#lsList .cb-unread').text(n > 99 ? '99+' : n).css('display', n > 0 ? 'inline-block' : 'none');
  };
  // refreshed by the header's single background check (manager/pulse)
})();
</script>
<?php } ?><div class="clearfix"></div>

<aside class="main-sidebar"> 
  
  <div class="crm-sidebar-header">
    <img src="<?php echo base_url(); ?>assets/logo-header.png" alt="7 Strokes" class="logo-light" />
    <img src="<?php echo base_url(); ?>assets/logo-header-dark.png" alt="7 Strokes" class="logo-dark" />
    <button type="button" class="crm-sidebar-close" id="crmSidebarClose" aria-label="Close navigation">&times;</button>
    <?php if ($crm_is_modern) { ?><button type="button" class="md2-mini-btn" id="md2MiniBtn" title="Make the side pane smaller or larger" aria-label="Make the side pane smaller or larger"><i class="fa fa-angle-double-left"></i></button><?php } ?>
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
        $is_support = (!$is_admin && crm_can('support_desk'));
        $is_presentation = crm_is_presentation_user($m_role);
        $user_team_count = $current_user_id ? count($this->ci_admin_model->fetchTeamList($current_user_id, '')) : 0;
        $can_see_review = ($is_admin || has_section_perm('back_office') || has_section_perm('lead_review'));
      ?>
            <?php if($is_presentation){ ?>
      <li class="<?php if($route1=='profile'){ echo 'active';}?>"><a href="<?php echo base_url();?>manager/profile"><i class="fa fa-user" style="margin-right:6px;"></i> <span>Profile</span></a></li>
      <li class="<?php if($route1=='live'){ echo 'active';}?>"><a href="<?php echo base_url();?>manager/live"><i class="fa fa-television" style="margin-right:6px;"></i> <span>Live</span></a></li>
      <?php } ?>
      <?php if(!$is_presentation && crm_can('dashboard')){ ?>
      <li class="<?php if($route1=="dashboard"){ echo "active";}else{ echo "";}?>"> 
        <a href="<?php echo base_url();?>manager/dashboard"> <svg class="crm-svg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-2px; margin-right:6px;"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="5" rx="1"></rect><rect x="14" y="12" width="7" height="9" rx="1"></rect><rect x="3" y="16" width="7" height="5" rx="1"></rect></svg> <span>Dashboard</span> </a> 
      </li>
      <?php } ?>

      <?php if(!$is_presentation && ($is_admin || has_section_perm('calls'))){ ?>
      <li class="<?php if($route1=="calls"){ echo "active";}else{ echo "";}?>"> 
        <a href="<?php echo base_url();?>manager/calls"> <i class="fa fa-phone" style="margin-right:6px; color:#1e3a8a;"></i> <span>Phone Dialer</span> </a> 
      </li>
      <?php } ?>

      <?php
        // Menu built from the permission catalogue (application/helpers/crm_permission_helper.php):
        // a link shows only when the person has the right that opens its page.
        if (!isset($crm_menu)) {
          $crm_menu = $is_presentation ? array() : crm_nav_menu();
          $crm_path = implode('/', array_slice($this->uri->segment_array(), 1));
          $crm_active = crm_nav_active_group($crm_menu, $crm_path, $_GET);
        }
      ?>
      <?php foreach ($crm_menu as $crm_gk => $crm_g) { $crm_on = ($crm_gk === $crm_active); ?>
      <li class="<?php echo $crm_on ? 'active menu-open' : ''; ?> treeview">
        <a href="javascript:void(0);"><svg class="crm-svg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-2px; margin-right:6px;"><?php echo $crm_g['icon']; ?></svg> <span><?php echo htmlspecialchars($crm_g['title']); ?></span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right sidebar-arrow"></i></span> </a>
        <ul class="treeview-menu" style="<?php echo $crm_on ? 'display: block;' : ''; ?>">
          <?php foreach ($crm_g['links'] as $crm_l) { ?>
          <li><a href="<?php echo $crm_l['external'] ? $crm_l['url'] : base_url() . 'manager/' . $crm_l['url']; ?>"<?php echo $crm_l['external'] ? ' target="_blank" rel="noopener"' : ''; ?>><i class="fa <?php echo $crm_l['icon']; ?>"></i><?php echo htmlspecialchars($crm_l['label']); ?></a></li>
          <?php } ?>
        </ul>
      </li>
      <?php } ?>

      <?php if (function_exists('chatbot_can') && chatbot_can('access')) { $cb_on = ($route1 == 'chatbot' || $route1 == 'live-support'); ?>
      <li class="<?php echo $cb_on ? 'active menu-open' : ''; ?> treeview">
        <a href="javascript:void(0);"><svg class="crm-svg-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-2px; margin-right:6px;"><path d="M12 8V4H8"></path><rect x="4" y="8" width="16" height="12" rx="2"></rect><path d="M2 14h2"></path><path d="M20 14h2"></path><path d="M9 13v2"></path><path d="M15 13v2"></path></svg> <span>Chatbot</span> <span class="cb-unread" style="display:none;"></span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right sidebar-arrow"></i></span> </a>
        <ul class="treeview-menu" style="<?php echo $cb_on ? 'display: block;' : ''; ?>">
          <li><a href="<?php echo base_url();?>manager/chatbot"><i class="fa fa-comments"></i>Open Chats <span class="cb-unread" style="display:none;"></span></a></li>
          <li><a href="<?php echo base_url();?>manager/chatbot/closed"><i class="fa fa-check-circle-o"></i>Closed Chats</a></li>
          <?php if (chatbot_can('history')) { ?><li><a href="<?php echo base_url();?>manager/chatbot/history"><i class="fa fa-history"></i>Chat History</a></li><?php } ?>
          <?php if (chatbot_can('settings')) { ?><li><a href="<?php echo base_url();?>manager/chatbot/settings"><i class="fa fa-sliders"></i>Settings</a></li><?php } ?>
        </ul>
      </li>
      <?php } ?>
    </ul>
  </section>
  <?php if ($crm_is_modern) { ?>
  <a class="md2-me" href="<?php echo base_url(); ?>manager/profile" title="My Profile">
    <img src="<?php echo base_url() . $user_avatar; ?>" alt="" />
    <span><b><?php echo htmlspecialchars($current_user_name); ?></b><?php if (!empty($current_user_role_name)) { ?><small><?php echo htmlspecialchars($current_user_role_name); ?></small><?php } ?></span>
  </a>
  <?php } ?>
</aside>
<?php if ($crm_is_modern) { ?>
<script>
(function () {
  // Side pane: mark the page you are on, open its group, and remember a smaller pane.
  function clean(href) { try { var u = new URL(href, location.href); return u.origin === location.origin ? u.pathname.replace(/\/index\.php/, '').replace(/\/+$/, '') : ''; } catch (e) { return ''; } }
  function mark() {
  var here = clean(location.href);
  // A page with a tab row (HR, Reports ...) belongs to the side link that opens any of its tabs
  var tabs = {};
  document.querySelectorAll('.content-wrapper .sx-tabs a[href]').forEach(function (a) { var p = clean(a.href); if (p) tabs[p] = 1; });
  var best = null, bestLen = 0, tabHit = null;
  document.querySelectorAll('.main-sidebar .sidebar-menu a[href]').forEach(function (a) {
    var p = clean(a.href);
    if (!p) return;
    if (!tabHit && tabs[p]) tabHit = a;
    if ((here === p || here.indexOf(p + '/') === 0) && p.length > bestLen) { best = a; bestLen = p.length; }
  });
  if (tabHit && (!best || clean(best.href) !== here)) best = tabHit;
  if (best) {
    best.classList.add('is-current');
    var g = best.closest('li.treeview');
    if (g) {
      document.querySelectorAll('.main-sidebar .sidebar-menu > li.treeview').forEach(function (o) { if (o !== g) { o.classList.remove('active', 'menu-open'); var u2 = o.querySelector('.treeview-menu'); if (u2) u2.style.display = ''; } });
      g.classList.add('active', 'menu-open'); var ul = g.querySelector('.treeview-menu'); if (ul) ul.style.display = 'block';
    }
  }
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', mark); else mark();
  var btn = document.getElementById('md2MiniBtn');
  if (btn) btn.addEventListener('click', function () {
    var on = document.documentElement.classList.toggle('crm-side-mini');
    try { localStorage.setItem('crm_side_mini', on ? '1' : '0'); } catch (e) {}
  });
  // Small pane: hovering a group shows its links beside it
  document.querySelectorAll('.main-sidebar .sidebar-menu > li > a').forEach(function (a) {
    var t = a.querySelector('span'); if (t && !a.getAttribute('data-tip')) a.setAttribute('data-tip', t.textContent.trim());
  });
  // Pages that do not load the footer (Phone Dialer) still need the side pane to open groups and the phone menu
  document.addEventListener('click', function (e) {
    if (window.crmSideBound) return;
    var body = document.body, el = e.target;
    if (el.closest('#crmMobileHamburger')) { e.preventDefault(); body.classList.toggle('sidebar-open'); return; }
    if (el.closest('#crmSidebarClose')) { e.preventDefault(); body.classList.remove('sidebar-open'); return; }
    var a = el.closest('.main-sidebar .sidebar-menu > li.treeview > a');
    if (a) {
      e.preventDefault();
      var li = a.parentNode, open = li.classList.contains('menu-open') || li.classList.contains('is-expanded');
      document.querySelectorAll('.main-sidebar .sidebar-menu > li.treeview').forEach(function (o) {
        o.classList.remove('menu-open', 'is-expanded'); var u = o.querySelector(':scope > .treeview-menu'); if (u) u.style.display = 'none';
      });
      if (!open) { li.classList.add('menu-open', 'is-expanded'); var m = li.querySelector(':scope > .treeview-menu'); if (m) m.style.display = 'block'; }
      return;
    }
    if (body.classList.contains('sidebar-open') && !el.closest('.main-sidebar')) body.classList.remove('sidebar-open');
  });
})();
</script>
<?php } ?>

<!-- Hover help on every page: the text from title="..." shows right next to the cursor (the browser's own hover text can appear far away).
     The title is only taken off while hovering and put back after, so forms and other scripts still see it. -->
<style>
  .hdr-tip { position: fixed; left: 0; top: 0; z-index: 1000000; pointer-events: none; background: #0f172a; color: #fff; font-size: 12px; font-weight: 600; line-height: 1.35; padding: 5px 9px; border-radius: 6px; max-width: 320px; white-space: normal; overflow-wrap: anywhere; box-shadow: 0 6px 18px rgba(0,0,0,.18); opacity: 0; transition: opacity .1s; }
  .hdr-tip.show { opacity: 1; }
  [data-theme="dark"] .hdr-tip { background: #f8fafc; color: #0f172a; }
</style>
<script>
(function () {
  function init() {
    var tip = document.createElement('div'), cur = null, timer = null, mx = 0, my = 0;
    tip.className = 'hdr-tip';
    tip.setAttribute('role', 'tooltip');
    document.body.appendChild(tip);
    function place() {
      // The page can be scaled (body zoom); work in screen pixels, then convert back.
      var z = parseFloat(getComputedStyle(document.body).zoom) || 1;
      var w = tip.offsetWidth * z, h = tip.offsetHeight * z, x = mx + 12, y = my + 18;
      if (x + w > window.innerWidth - 6) x = Math.max(6, mx - w - 10);
      if (y + h > window.innerHeight - 6) y = Math.max(6, my - h - 10);
      tip.style.left = (x / z) + 'px';
      tip.style.top = (y / z) + 'px';
    }
    function hide() {
      clearTimeout(timer);
      if (cur) {
        if (!cur.hasAttribute('title') && cur.__crmTip != null) cur.setAttribute('title', cur.__crmTip);
        cur = null;
      }
      tip.classList.remove('show');
    }
    document.addEventListener('mouseover', function (e) {
      if (cur && cur.contains(e.target)) return;
      hide();
      var el = e.target && e.target.closest ? e.target.closest('[title]') : null;
      if (!el || el === document.documentElement || /^(OPTION|IFRAME|HTML|BODY)$/.test(el.tagName) || el.hasAttribute('data-original-title')) return;
      var t = el.getAttribute('title');
      if (!t || !t.trim()) return;
      cur = el;
      el.__crmTip = t;
      el.removeAttribute('title');
      if (!el.getAttribute('aria-label') && !el.textContent.trim()) el.setAttribute('aria-label', t);
      mx = e.clientX; my = e.clientY;
      timer = setTimeout(function () { tip.textContent = t; tip.classList.add('show'); place(); }, 120);
    }, true);
    document.addEventListener('mousemove', function (e) {
      mx = e.clientX; my = e.clientY;
      if (cur && tip.classList.contains('show')) place();
    }, { passive: true, capture: true });
    document.addEventListener('mouseout', function (e) {
      if (cur && (!e.relatedTarget || !cur.contains(e.relatedTarget))) hide();
    }, true);
    document.addEventListener('mousedown', hide, true);
    document.addEventListener('keydown', hide, true);
    window.addEventListener('scroll', hide, { passive: true, capture: true });
    window.addEventListener('blur', hide);
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
</script>
<script>
/* User activity (System > Activity > User Activity): once a minute, if the person clicked, typed or scrolled
   in the last minute (or is on a call in the dialler), the minute is recorded. Nothing else is sent. */
(function () {
  if (window.crmActPing) return;
  window.crmActPing = true;
  var URL = '<?php echo base_url(); ?>manager/activity/ping';
  var NAME = '<?php echo $this->security->get_csrf_token_name(); ?>';
  var last = Date.now();
  function seen() { last = Date.now(); }
  ['mousemove', 'mousedown', 'keydown', 'wheel', 'scroll', 'touchstart'].forEach(function (e) {
    document.addEventListener(e, seen, { passive: true, capture: true });
  });
  function onCall() {
    try { return typeof callState !== 'undefined' && /^(calling|connected|wrapup)$/.test(callState); } catch (e) { return false; }
  }
  function send() {
    var m = document.cookie.match(/(?:^|;\s*)csrf_cookie_name=([^;]+)/);
    var b = new URLSearchParams({ page: location.pathname.replace(/^.*?\/manager\//, '').slice(0, 120) });
    if (m) b.append(NAME, decodeURIComponent(m[1]));
    fetch(URL, { method: 'POST', body: b, credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' }, keepalive: true }).catch(function () {});
  }
  setTimeout(send, 2000);
  setInterval(function () {
    if (onCall() || (document.visibilityState === 'visible' && Date.now() - last < 60000)) send();
  }, 60000);
})();
</script>
