<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/



/* End of file hooks.php */
/* Location: ./application/config/hooks.php */
// Permission page guard: see application/hooks/crm_permission_guard.php
$hook['post_controller_constructor'][] = array(
	'class'    => '',
	'function' => 'crm_permission_guard',
	'filename' => 'crm_permission_guard.php',
	'filepath' => 'hooks',
);

// Record tracking: who changed which field of a lead, order, KAM client... (see application/hooks/crm_track_db.php)
$hook['pre_system'][] = array(
	'class'    => '',
	'function' => 'crm_track_db',
	'filename' => 'crm_track_db.php',
	'filepath' => 'hooks',
);

// Sales CRM subscription on the subscription portal: see application/hooks/crm_subscription_guard.php
$hook['post_controller_constructor'][] = array(
	'class'    => '',
	'function' => 'crm_subscription_guard',
	'filename' => 'crm_subscription_guard.php',
	'filepath' => 'hooks',
);
