<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$route['default_controller'] = "ci_admin";
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['manager/login'] = "ci_admin/login";
$route['manager/forgot'] = "ci_admin/forgot";
$route['manager/bnr54dfdf/(:any)'] = 'ci_admin/reset/$1';
$route['manager/logout'] = "ci_admin/logout";
$route['manager/dashboard'] = "ci_admin/dashboard";
$route['manager/profile'] = "ci_admin/admin_profile";
$route['manager/profile/upload_avatar'] = "ci_admin/ajax_upload_avatar";
$route['manager/password'] = "ci_admin/change_password";

$route['manager/users'] = "ci_admin_user/index";
$route['manager/users/(:num)'] = 'ci_admin_user/index/$1';
$route['manager/users/add'] = "ci_admin_user/add";
$route['manager/users/edit/(:num)'] = 'ci_admin_user/edit/$1';
$route['manager/users/cedit/(:num)'] = 'ci_admin_user/cedit/$1';
$route['manager/users/view/(:num)'] = 'ci_admin_user/view/$1';
$route['manager/users/report'] = "ci_admin_report/users";

$route['manager/leads/import'] = "ci_admin_leads/import";
$route['manager/leads/import_action'] = "ci_admin_leads/import_action";
$route['manager/leads/export'] = "ci_admin_leads/export";
$route['manager/leads/download_sample_csv'] = "ci_admin_leads/download_sample_csv";
$route['manager/database'] = "ci_admin_leads/index";
$route['manager/database/(:num)'] = 'ci_admin_leads/index/$1';
$route['manager/database/add'] = "ci_admin_leads/add";
$route['manager/leads'] = "ci_admin_leads/index";
$route['manager/leads/(:num)'] = 'ci_admin_leads/index/$1';
$route['manager/leads/view_leads'] = "ci_admin_leads/view_leads";
$route['manager/leads/view_leads/(:num)'] = 'ci_admin_leads/view_leads/$1';
$route['manager/leads/review_leads'] = "ci_admin_leads/review_leads";
$route['manager/leads/review_leads/(:num)'] = "ci_admin_leads/review_leads/$1";
$route['manager/leads/convert_to_lead'] = "ci_admin_leads/convert_to_lead";
$route['manager/leads/request_conversion'] = "ci_admin_leads/request_conversion";
$route['manager/leads/reject_conversion'] = "ci_admin_leads/reject_conversion";
$route['manager/leads/request_order'] = "ci_admin_leads/request_order";
$route['manager/leads/transfer_to_lead'] = "ci_admin_leads/convert_to_lead";
$route['manager/leads/add'] = "ci_admin_leads/add";
$route['manager/leads/edit/(:any)'] = 'ci_admin_leads/edit/$1';
$route['manager/leads/editLimited/(:any)'] = 'ci_admin_leads/editLimited/$1';
$route['manager/leads/view/(:any)'] = 'ci_admin_leads/view/$1';
$route['manager/leads/ajax_search_companies'] = "ci_admin_leads/ajax_search_companies";
$route['manager/deleteleads'] = "ci_admin_leads/leadDeleteAjax";
$route['manager/deleteleads/(:any)'] = "ci_admin_leads/leadDeleteAjax/$1";
$route['manager/calls'] = "ci_admin_calls/index";
$route['manager/calls/ajax_save_call'] = "ci_admin_calls/ajax_save_call";
$route['manager/calls/ajax_get_lead'] = "ci_admin_calls/ajax_get_lead";
$route['manager/calls/(:any)'] = "ci_admin_calls/$1";

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

$route['manager/team/verticals'] = "ci_admin_verticals/teams";
$route['manager/team/verticals/(:any)'] = 'ci_admin_verticals/teams/$1';

$route['manager/verticals/report'] = "ci_admin_verticals/report";
$route['manager/verticals/report/fixed/(:any)'] = "ci_admin_verticals/report_fixed/$1";

$route['manager/users'] = "ci_admin_user/index";
$route['manager/users/(:num)'] = 'ci_admin_user/index/$1';
$route['manager/users/add'] = "ci_admin_user/add";
$route['manager/users/edit/(:any)'] = 'ci_admin_user/edit/$1';
$route['manager/users/view/(:any)'] = 'ci_admin_user/view/$1';
$route['manager/users/delete/(:any)'] = 'ci_admin_user/delete/$1';
$route['manager/user/add'] = "ci_admin_user/add";
$route['manager/user/edit/(:any)'] = 'ci_admin_user/edit/$1';
$route['manager/user/view/(:any)'] = 'ci_admin_user/view/$1';
$route['manager/user/delete/(:any)'] = 'ci_admin_user/delete/$1';


$route['manager/team/members'] = "ci_admin_team/members";
$route['manager/team/members/(:num)'] = 'ci_admin_team/members/$1';
$route['manager/team/members/details/(:any)'] = 'ci_admin_team/membersdetails/$1';
$route['manager/team/database'] = "ci_admin_team/database";
$route['manager/team/database/(:any)'] = "ci_admin_team/database/$1";
$route['manager/team/leads'] = "ci_admin_team/leads/$1";
$route['manager/team/leads/(:any)'] = "ci_admin_team/leads/$1";
$route['manager/team/orders'] = "ci_admin_team/orders";
$route['manager/team/orders/(:any)'] = "ci_admin_team/orders/$1";
$route['manager/team/sales'] = "ci_admin_team/sales";
$route['manager/team/sales/(:any)'] = "ci_admin_team/sales/$1";
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
$route['manager/roles/delete/(:num)'] = 'ci_admin_roles/delete/$1';

$route['manager/meetings'] = 'ci_admin_meetings/index';
$route['manager/meetings/(:any)'] = 'ci_admin_meetings/index/$1';
$route['manager/meetings/add'] = 'ci_admin_meetings/add';
$route['manager/meetings/edit/(:any)'] = 'ci_admin_meetings/edit/$1';
$route['manager/meetings/delete/(:any)'] = 'ci_admin_meetings/delete/$1';

$route['manager/terms'] = 'ci_admin_terms/index';
$route['manager/terms/add'] = 'ci_admin_terms/add';
$route['manager/terms/edit/(:any)'] = 'ci_admin_terms/edit/$1';
$route['manager/terms/delete/(:any)'] = 'ci_admin_terms/delete/$1';
$route['manager/category-delete/(:any)'] = 'ci_admin_terms/delete/$1';
$route['manager/category-tree'] = 'ci_admin_terms/index';
$route['manager/categories'] = 'ci_admin_terms/index';

$route['manager/tickets'] = "ci_admin_tickets/index";
$route['manager/tickets/add'] = "ci_admin_tickets/add";
$route['manager/tickets/quick_add'] = "ci_admin_tickets/quick_add";
$route['manager/tickets/edit/(:any)'] = "ci_admin_tickets/edit/$1";
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
$route['manager/tasks/internal'] = "ci_admin_tasks/internal";
$route['manager/tasks/internal/(:num)'] = "ci_admin_tasks/internal/$1";
$route['manager/tasks/external'] = "ci_admin_tasks/external";
$route['manager/tasks/external/(:num)'] = "ci_admin_tasks/external/$1";
$route['manager/tasks/add'] = "ci_admin_tasks/add";
$route['manager/tasks/view/(:any)'] = "ci_admin_tasks/view/$1";
$route['manager/tasks/edit/(:any)'] = "ci_admin_tasks/edit/$1";
$route['manager/tasks/ajax_preview_recurrence'] = "ci_admin_tasks/ajax_preview_recurrence";
$route['manager/tasks/followups'] = "ci_admin_tasks/followups";

$route['manager/kam'] = "ci_admin_kam/index";
$route['manager/kam/my-client'] = "ci_admin_kam/my_client";
$route['manager/kam/my_client'] = "ci_admin_kam/my_client";
$route['manager/kam/onboard'] = "ci_admin_kam/onboard";
$route['manager/kam/ajax_get_company_pocs'] = "ci_admin_kam/ajax_get_company_pocs";
$route['manager/kam/schedule'] = "ci_admin_kam/schedule";
$route['manager/kam/pitch-schedule'] = "ci_admin_kam/schedule";
$route['manager/kam/ajax_get_client_schedules'] = "ci_admin_kam/ajax_get_client_schedules";
$route['manager/kam/schedule/delete/(:num)'] = "ci_admin_kam/delete_schedule/$1";
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
$route['manager/reports/revenue-list'] = "ci_admin_report/revenue_list";
$route['manager/reports/company-projections'] = "ci_admin_report/company_projections";
$route['manager/revenue-list'] = "ci_admin_report/revenue_list";
$route['manager/company-projections'] = "ci_admin_report/company_projections";

$route['manager/assigned-leads'] = "ci_admin_leads/assigned_leads";
$route['manager/assigned-leads/(:num)'] = "ci_admin_leads/assigned_leads/$1";
$route['manager/leads/bulk-move'] = "ci_admin_leads/bulk_move_assign";

$route['manager/admin-logs'] = "ci_admin_system/logs";
$route['manager/admin-logs/(:num)'] = "ci_admin_system/logs/$1";
$route['manager/system/admin-logs'] = "ci_admin_system/logs";
$route['manager/system/admin-logs/(:num)'] = "ci_admin_system/logs/$1";
$route['manager/system/admin_logs'] = "ci_admin_system/logs";
$route['manager/system/admin_logs/(:num)'] = "ci_admin_system/logs/$1";

$route['manager/system/calling-api'] = "ci_admin_calls/calling_api";
$route['manager/system/calling_api'] = "ci_admin_calls/calling_api";
$route['manager/ping'] = "ci_admin/ping";

$route['manager/orders/my'] = 'ci_admin_orders/my';
$route['manager/team/orders'] = 'ci_admin_orders/team';
$route['manager/orders/team'] = 'ci_admin_orders/team';
$route['manager/orders'] = 'ci_admin_orders/index';
$route['manager/orders/add'] = 'ci_admin_orders/add';
$route['manager/orders/view/(:num)'] = 'ci_admin_orders/view/$1';
$route['manager/orders/approve/(:num)'] = 'ci_admin_orders/approve/$1';
$route['manager/orders/reject/(:num)'] = 'ci_admin_orders/reject/$1';
$route['manager/orders/update_status/(:num)'] = 'ci_admin_orders/update_status/$1';
$route['manager/orders/add_note/(:num)'] = 'ci_admin_orders/add_note/$1';
$route['manager/orders/pending_process'] = 'ci_admin_orders/pending_process';
$route['manager/orders/pending_process/(:any)'] = 'ci_admin_orders/pending_process/$1';
$route['manager/orders/edit_pending/(:num)'] = 'ci_admin_orders/edit_pending/$1';
$route['manager/orders/quick_accept/(:num)'] = 'ci_admin_orders/quick_accept/$1';
$route['manager/orders/convert_to_sales/(:num)'] = 'ci_admin_orders/convert_to_sales/$1';
$route['manager/orders/sales'] = 'ci_admin_orders/sales';
$route['manager/sales'] = 'ci_admin_orders/sales';
$route['manager/sales/(:any)'] = 'ci_admin_orders/sales/$1';
$route['manager/system/live-location'] = 'ci_admin_system/live_location';
$route['manager/system/ajax_update_live_location'] = 'ci_admin_system/ajax_update_live_location';
$route['manager/system/ajax_get_live_location'] = 'ci_admin_system/ajax_get_live_location';
$route['manager/orders/(:any)'] = 'ci_admin_orders/$1';

$route['manager/events'] = 'ci_admin_events/index';
$route['manager/events/add'] = 'ci_admin_events/add';
$route['manager/events/delete/(:num)'] = 'ci_admin_events/delete/$1';
$route['manager/events/ajax_events'] = 'ci_admin_events/ajax_get_events';
$route['manager/events/(:any)'] = 'ci_admin_events/$1';
$route['manager/awards'] = 'ci_admin_events/awards';
$route['manager/awards/add'] = 'ci_admin_events/add_award';
$route['manager/awards/delete/(:num)'] = 'ci_admin_events/delete_award/$1';

$route['manager/system/integrations'] = 'ci_admin_integrations/index';
$route['manager/system/integrations/save'] = 'ci_admin_integrations/save';
$route['manager/system/integrations/ajax_test_connection'] = 'ci_admin_integrations/ajax_test_connection';
$route['manager/integrations'] = 'ci_admin_integrations/index';

$route['api/calls/recording_webhook'] = 'ci_admin_calls/recording_webhook';
$route['api/calls/cdr_webhook'] = 'ci_admin_calls/cdr_webhook';
$route['api/calls/process_ai'] = 'ci_admin_calls/ajax_process_ai';
$route['api/calls/process_ai/(:num)'] = 'ci_admin_calls/ajax_process_ai/$1';


