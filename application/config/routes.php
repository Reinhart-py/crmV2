<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$route['default_controller'] = "ci_admin";
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['manager/login'] = "ci_admin/login";
$route['manager/forgot'] = "ci_admin/forgot";
$route['manager/bnr54dfdf/(:num)'] = 'ci_admin/reset/$1';
$route['manager/logout'] = "ci_admin/logout";
$route['manager/dashboard'] = "ci_admin/dashboard";
$route['manager/profile'] = "ci_admin/admin_profile";
$route['manager/password'] = "ci_admin/change_password";

$route['manager/users'] = "ci_admin_user/index";
$route['manager/users/(:num)'] = 'ci_admin_user/index/$1';
$route['manager/users/add'] = "ci_admin_user/add";
$route['manager/users/edit/(:num)'] = 'ci_admin_user/edit/$1';
$route['manager/users/cedit/(:num)'] = 'ci_admin_user/cedit/$1';
$route['manager/users/view/(:num)'] = 'ci_admin_user/view/$1';
$route['manager/users/report'] = "ci_admin_report/users";

//My Leads
$route['manager/leads/import'] = "ci_admin_leads/import";
$route['manager/leads/import_action'] = "ci_admin_leads/import_action";
$route['manager/leads/export'] = "ci_admin_leads/export";
$route['manager/leads/download_sample_csv'] = "ci_admin_leads/download_sample_csv";
$route['manager/leads'] = "ci_admin_leads/index";
$route['manager/leads/(:num)'] = 'ci_admin_leads/index/$1';
$route['manager/leads/add'] = "ci_admin_leads/add";
$route['manager/leads/edit/(:any)'] = 'ci_admin_leads/edit/$1';
$route['manager/leads/editLimited/(:any)'] = 'ci_admin_leads/editLimited/$1';
$route['manager/leads/view/(:any)'] = 'ci_admin_leads/view/$1';
$route['manager/deleteleads'] = "ci_admin_leads/leadDeleteAjax";
$route['manager/deleteleads/(:any)'] = "ci_admin_leads/leadDeleteAjax/$1";
$route['manager/calls'] = "ci_admin_calls/index";
$route['manager/calls/ajax_save_call'] = "ci_admin_calls/ajax_save_call";
$route['manager/calls/ajax_get_lead'] = "ci_admin_calls/ajax_get_lead";
$route['manager/calls/(:any)'] = "ci_admin_calls/$1";

//My Followups
$route['manager/leads/followups'] = "ci_admin_leads/followups/";
$route['manager/leads/followups/(:num)'] = "ci_admin_leads/followups/$1";
$route['manager/leads/meetings'] = "ci_admin_leads/meetings/";
$route['manager/leads/meetings/(:any)'] = "ci_admin_leads/meetings/$1";
$route['manager/leads/report'] = "ci_admin_report/leads";

$route['manager/performance-report'] = 'ci_admin_report/performanceReportDep';
$route['manager/performance-report/individual'] = 'ci_admin_report/performanceReportIndividual';




$route['manager/verticals'] = "ci_admin_verticals/index";
$route['manager/verticals/(:num)'] = 'ci_admin_verticals/index/$1';
$route['manager/verticals/add'] = "ci_admin_verticals/add";
$route['manager/verticals/edit/(:any)'] = 'ci_admin_verticals/edit/$1';
$route['manager/verticals/view/(:any)'] = 'ci_admin_verticals/view/$1';
$route['manager/verticals/followups'] = "ci_admin_verticals/followups";
$route['manager/verticals/followups/(:num)'] = "ci_admin_verticals/followups/$1";

$route['manager/database-transfer'] = "ci_admin_leads/database_transfer";
$route['manager/database-transfer/(:num)'] = "ci_admin_leads/database_transfer/$1";
$route['manager/leads/bulk-transfer'] = "ci_admin_leads/bulk_transfer";

$route['manager/team/verticals'] = "ci_admin_verticals/teams/$1";
$route['manager/team/verticals/(:any)'] = 'ci_admin_verticals/teams/$1';

$route['manager/verticals/report'] = "ci_admin_verticals/report";
$route['manager/verticals/report/fixed/(:any)'] = "ci_admin_verticals/report_fixed/$1";


$route['manager/team/members'] = "ci_admin_team/members";
$route['manager/team/members/(:num)'] = 'ci_admin_team/members/$1';
$route['manager/team/members/details/(:any)'] = 'ci_admin_team/membersdetails/$1';
$route['manager/team/leads'] = "ci_admin_team/leads/$1";
$route['manager/team/leads/(:any)'] = "ci_admin_team/leads/$1";
$route['manager/team/assignleads'] = "ci_admin_team/assignleads/$1";
$route['manager/team/assignleads/(:any)'] = "ci_admin_team/assignleads/$1";
$route['manager/team/followups'] = "ci_admin_team/followups/$1";
$route['manager/team/followups/(:any)'] = 'ci_admin_team/followups/$1';
$route['manager/team/meetings'] = "ci_admin_team/meetings/$1";
$route['manager/team/meetings/(:any)'] = 'ci_admin_team/meetings/$1';
$route['manager/team/assignmeetings'] = "ci_admin_team/assignmeetings/$1";
$route['manager/team/assignmeetings/(:any)'] = 'ci_admin_team/assignmeetings/$1';
$route['manager/team/chart'] = "ci_admin_team/chart";

$route['manager/roles'] = "ci_admin_roles/index";
$route['manager/roles/(:num)'] = 'ci_admin_roles/index/$1';
$route['manager/roles/add'] = "ci_admin_roles/add";
$route['manager/roles/edit/(:num)'] = 'ci_admin_roles/edit/$1';

// Manager Category
$route['manager/terms'] = 'ci_admin_terms/index';
$route['manager/terms/add'] = 'ci_admin_terms/add';
$route['manager/terms/edit/(:any)'] = 'ci_admin_terms/edit/$1';
$route['manager/category-delete/(:any)'] = 'ci_admin_category/delete/$1';

// Ticket Trace / Help Desk
$route['manager/tickets'] = "ci_admin_tickets/index";
$route['manager/tickets/add'] = "ci_admin_tickets/add";
$route['manager/tickets/quick_add'] = "ci_admin_tickets/quick_add";
$route['manager/tickets/view/(:any)'] = "ci_admin_tickets/view/$1";
$route['manager/tickets/respond'] = "ci_admin_tickets/respond";
$route['manager/tickets/close/(:any)'] = "ci_admin_tickets/close/$1";
$route['manager/tickets/status/(:any)/(:any)'] = "ci_admin_tickets/status/$1/$2";
$route['manager/tickets/delete/(:any)'] = "ci_admin_tickets/delete/$1";
$route['manager/tickets/followups'] = "ci_admin_tickets/followups";
$route['manager/tickets/open'] = "ci_admin_tickets/followups";
$route['manager/team/tickets'] = "ci_admin_tickets/index";
$route['manager/team/tickets/(:any)'] = "ci_admin_tickets/index/$1";

$route['manager/tasks'] = "ci_admin_tasks/index";
$route['manager/tasks/add'] = "ci_admin_tasks/add";
$route['manager/tasks/view/(:any)'] = "ci_admin_tasks/view/$1";
$route['manager/tasks/edit/(:any)'] = "ci_admin_tasks/edit/$1";
$route['manager/tasks/followups'] = "ci_admin_tasks/followups";

$route['manager/kam'] = "ci_admin_kam/index";
$route['manager/kam/my-client'] = "ci_admin_kam/my_client";
$route['manager/kam/my_client'] = "ci_admin_kam/my_client";
$route['manager/kam/onboard'] = "ci_admin_kam/onboard";
$route['manager/kam/schedule'] = "ci_admin_kam/schedule";
$route['manager/kam/pitch-schedule'] = "ci_admin_kam/schedule";
$route['manager/kam/report'] = "ci_admin_kam/report";

$route['manager/download-report/drilldown'] = "ci_admin_report/drilldown";
$route['manager/download-report'] = "ci_admin_report/downloadReport";
$route['manager/download-report/(:any)'] = "ci_admin_report/downloadReport/$1";
$route['manager/download-report/(:any)/(:any)'] = "ci_admin_report/downloadReport/$1/$2";
$route['manager/admin-reports'] = "ci_admin_report/adminReports";
$route['manager/admin-reports/(:any)'] = "ci_admin_report/adminReports/$1";
$route['manager/admin-reports/(:any)/(:any)'] = "ci_admin_report/adminReports/$1/$2";
$route['manager/reports/admin'] = "ci_admin_report/adminReports";
$route['manager/reports/admin/(:any)'] = "ci_admin_report/adminReports/$1";
$route['manager/reports/admin/(:any)/(:any)'] = "ci_admin_report/adminReports/$1/$2";

// System > Assigned Leads & Bulk Move
$route['manager/assigned-leads'] = "ci_admin_leads/assigned_leads";
$route['manager/assigned-leads/(:num)'] = "ci_admin_leads/assigned_leads/$1";
$route['manager/leads/bulk-move'] = "ci_admin_leads/bulk_move_assign";

// System > Admin Logs
$route['manager/admin-logs'] = "ci_admin_system/logs";
$route['manager/admin-logs/(:num)'] = "ci_admin_system/logs/$1";

// 5-minute inactivity session keepalive/ping
$route['manager/ping'] = "ci_admin/ping";

/* End of file routes.php */

/* Location: ./application/config/routes.php */