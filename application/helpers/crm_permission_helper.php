<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * CRM permission catalogue: the one list of everything that can be granted.
 *
 * Used by: the role editor, the user-wise editor, the navigation menu (top bar
 * and side menu), the page guard (hooks/crm_permission_guard.php) and
 * Ci_admin_model::check_permission().
 *
 * Levels: no_access (None), read (View), write (Full: add, edit, delete).
 *
 * Item fields
 *   label      Name shown in the editor.
 *   desc       One line explaining what it unlocks.
 *   nav        Menu links: array(url, label, font-awesome icon, level needed).
 *   pages      Page URLs (after manager/) that need View. "path?key=value" also
 *              requires that query value. Opening one without the right is refused.
 *   write      Page URLs that need Full (add / create pages).
 *   fallback   Older key used when a role was saved before this item existed,
 *              so existing roles keep the access they had.
 *   levels     'rw' = None/View/Full (default), 'r' = None/Allow.
 *
 * Group "key" is the older section key that controllers still check
 * (e.g. has_section_perm('back_office')). It is derived from the group's items:
 * Full if any item is Full, View if any item is View.
 */

if (!function_exists('crm_perm_catalog')) {
	function crm_perm_catalog()
	{
		static $cat = null;
		if ($cat !== null) return $cat;

		$cat = array(
			'core' => array(
				'title' => 'Core Modules', 'key' => '', 'menu' => false,
				'icon' => '<rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="5" rx="1"></rect><rect x="14" y="12" width="7" height="9" rx="1"></rect><rect x="3" y="16" width="7" height="5" rx="1"></rect>',
				'items' => array(
					'dashboard' => array('label' => 'Dashboard', 'desc' => 'Home page with pipeline counts and charts', 'pages' => array('dashboard'), 'levels' => 'r'),
					'calls' => array('label' => 'Phone Dialer & Calls', 'desc' => 'Dialer, softphone and call logs (phone button in the top bar)', 'pages' => array('calls')),
					'live' => array('label' => 'Live Operations Report', 'desc' => 'LIVE button in the top bar and the TV presentation screen', 'pages' => array('live'), 'levels' => 'r'),
					'help_support' => array('label' => 'Help & Support', 'desc' => 'Help topics and chat with support (profile menu)', 'fallback' => 'dashboard', 'pages' => array('help'), 'levels' => 'r'),
					'self_training' => array('label' => 'Self Training', 'desc' => 'Company training library: pitch scripts, step-by-step guides, documents and videos (profile menu); view only', 'fallback' => 'dashboard', 'pages' => array('self-training'), 'levels' => 'r'),
					'events_view' => array('label' => 'Events & Festivals', 'desc' => 'View company holidays, festivals, events and awards (profile menu); view only', 'fallback' => 'dashboard', 'pages' => array('events'), 'levels' => 'r'),
				),
			),
			'my_zone' => array(
				'title' => 'My Zone', 'key' => 'my_zone', 'match' => array('leads', 'verticals', 'database-transfer', 'sales', 'tasks', 'tickets', 'database'),
				'icon' => '<rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect>',
				'items' => array(
					'database' => array('label' => 'Database', 'desc' => 'View own database records; Full can add and edit records',
						'nav' => array(array('leads/add', 'Add Database', 'fa-plus', 'write'), array('leads', 'View Database', 'fa-database', 'read')),
						'pages' => array('leads', 'database'), 'write' => array('leads/add', 'database/add')),
					'leads' => array('label' => 'Leads', 'desc' => 'Records approved as leads', 'fallback' => 'database',
						'nav' => array(array('leads/view_leads', 'View Leads', 'fa-bullseye')), 'pages' => array('leads/view_leads')),
					'orders' => array('label' => 'My Orders', 'desc' => 'Orders created from own leads', 'fallback' => 'database',
						'nav' => array(array('orders/my', 'View Orders', 'fa-shopping-cart')), 'pages' => array('orders/my')),
					'sales' => array('label' => 'My Sales', 'desc' => 'Orders that became sales', 'fallback' => 'database',
						'nav' => array(array('sales', 'View Sales', 'fa-line-chart')), 'pages' => array('sales', 'orders/sales')),
					'followups' => array('label' => 'My Follow-Up', 'desc' => 'Follow-up reminders on own records', 'fallback' => 'database',
						'nav' => array(array('leads/followups', 'My Follow-Up', 'fa-clock-o')), 'pages' => array('leads/followups')),
					'meetings' => array('label' => 'My Meetings', 'desc' => 'Meetings booked on own records', 'fallback' => 'database',
						'nav' => array(array('leads/meetings', 'My Meetings', 'fa-calendar')), 'pages' => array('leads/meetings', 'meetings'), 'write' => array('meetings/add')),
					'verticals' => array('label' => 'Verticals', 'desc' => 'Vertical records; Full can add them', 'fallback' => 'database',
						'nav' => array(array('verticals/add', 'Add Vertical', 'fa-plus', 'write'), array('verticals', 'View Vertical', 'fa-list', 'read')),
						'pages' => array('verticals'), 'write' => array('verticals/add')),
					'vertical_followups' => array('label' => 'Vertical Follow-up', 'desc' => 'Follow-ups on verticals', 'fallback' => 'database',
						'nav' => array(array('verticals/followups', 'Vertical Follow-up', 'fa-clock-o')), 'pages' => array('verticals/followups')),
					'internal_tasks' => array('label' => 'Internal Tasks', 'desc' => 'Tasks between colleagues', 'fallback' => 'tasks',
						'nav' => array(array('tasks/internal', 'View Internal Task', 'fa-building-o')), 'pages' => array('tasks/internal')),
					'external_tasks' => array('label' => 'External Tasks', 'desc' => 'Tasks for clients', 'fallback' => 'tasks',
						'nav' => array(array('tasks/external', 'View External Task', 'fa-briefcase')), 'pages' => array('tasks/external')),
					'task_followups' => array('label' => 'Task Follow-up', 'desc' => 'Follow-up list for tasks', 'fallback' => 'internal_tasks',
						'nav' => array(array('tasks/followups', 'Task Follow-up', 'fa-check-square-o')), 'pages' => array('tasks/followups')),
					'tickets' => array('label' => 'Tickets', 'desc' => 'Support tickets: raise, open and follow up', 'fallback' => 'team_tickets',
						'nav' => array(array('tickets', 'Tickets', 'fa-ticket'), array('tickets/open', 'Open Tickets', 'fa-folder-open-o')),
						'pages' => array('tickets', 'tickets/open', 'tickets/followups'), 'write' => array('tickets/add')),
					'database_transfer' => array('label' => 'Database Transfer', 'desc' => 'Move records between users', 'fallback' => 'database',
						'nav' => array(array('database-transfer', 'Database Transfer', 'fa-exchange')), 'pages' => array('database-transfer')),
				),
			),
			'team' => array(
				'title' => 'Team', 'key' => 'team', 'match' => array('team'),
				'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
				'items' => array(
					'team_members' => array('label' => 'Members', 'desc' => 'People in your team', 'fallback' => 'team', 'nav' => array(array('team/members', 'Members', 'fa-users')), 'pages' => array('team/members')),
					'team_chart' => array('label' => 'Team Chart', 'desc' => 'Reporting structure', 'fallback' => 'team', 'nav' => array(array('team/chart', 'Chart', 'fa-sitemap')), 'pages' => array('team/chart')),
					'team_database' => array('label' => 'Team Database', 'desc' => 'Database records of the team', 'fallback' => 'team', 'nav' => array(array('team/database', 'Database', 'fa-database')), 'pages' => array('team/database')),
					'team_leads' => array('label' => 'Team Leads', 'desc' => 'Leads of the team', 'fallback' => 'team', 'nav' => array(array('team/leads', 'Leads', 'fa-list')), 'pages' => array('team/leads')),
					'team_orders' => array('label' => 'Team Orders', 'desc' => 'Orders of the team', 'fallback' => 'team', 'nav' => array(array('team/orders', 'Orders', 'fa-shopping-cart')), 'pages' => array('team/orders', 'orders/team')),
					'team_verticals' => array('label' => 'Team Verticals', 'desc' => 'Verticals of the team', 'fallback' => 'team', 'nav' => array(array('team/verticals', 'Verticals', 'fa-list')), 'pages' => array('team/verticals')),
					'team_followups' => array('label' => 'Team Follow-up', 'desc' => 'Follow-ups of the team', 'fallback' => 'team', 'nav' => array(array('team/followups', 'Follow up', 'fa-clock-o')), 'pages' => array('team/followups')),
					'team_meetings' => array('label' => 'Team Meetings', 'desc' => 'Meetings of the team', 'fallback' => 'team', 'nav' => array(array('team/meetings', 'Meetings', 'fa-calendar')), 'pages' => array('team/meetings')),
					'team_assignmeetings' => array('label' => 'Assigned Meetings', 'desc' => 'Meetings assigned to team members', 'fallback' => 'team', 'nav' => array(array('team/assignmeetings', 'Assigned Meetings', 'fa-calendar-check-o')), 'pages' => array('team/assignmeetings')),
					'team_assignleads' => array('label' => 'Assign Database', 'desc' => 'Give database records to team members', 'fallback' => 'team', 'nav' => array(array('team/assignleads', 'Assign Database', 'fa-tasks')), 'pages' => array('team/assignleads')),
					'team_tasks' => array('label' => 'Team Tasks', 'desc' => 'All tasks of the team', 'fallback' => 'team', 'nav' => array(array('tasks', 'Task', 'fa-tasks')), 'pages' => array('tasks'), 'write' => array('tasks/add')),
					'team_tickets' => array('label' => 'Team Tickets', 'desc' => 'Tickets of the team', 'fallback' => 'team', 'nav' => array(array('team/tickets', 'Tickets', 'fa-ticket')), 'pages' => array('team/tickets')),
				),
			),
			'back_office' => array(
				'title' => 'Back Office', 'key' => 'back_office', 'match' => array('back-office', 'back_office', 'invoices', 'payments'),
				'icon' => '<rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>',
				'items' => array(
					'lead_review' => array('label' => 'Pending Leads Approval', 'desc' => 'Approve database records as leads', 'fallback' => 'back_office', 'nav' => array(array('leads/review_leads', 'Pending Leads Approval', 'fa-check-square-o')), 'pages' => array('leads/review_leads', 'back-office', 'back_office')),
					'orders_pending' => array('label' => 'Pending Order Process', 'desc' => 'Orders waiting to be processed', 'fallback' => 'back_office', 'nav' => array(array('orders/pending_process', 'Pending Order Process', 'fa-hourglass-half')), 'pages' => array('orders/pending_process')),
					'orders_pending_sales' => array('label' => 'View Sales Approvals', 'desc' => 'Orders sent for sales approval', 'fallback' => 'back_office', 'nav' => array(array('orders/view_sales_approvals', 'View Sales Approvals', 'fa-eye')), 'pages' => array('orders/view_sales_approvals')),
					'kam_onboard' => array('label' => 'Onboard Client', 'desc' => 'Turn a sale into a KAM client', 'fallback' => 'kam', 'nav' => array(array('kam/onboard', 'Onboard Client', 'fa-user-plus')), 'pages' => array('kam/onboard')),
					'orders_open' => array('label' => 'Open Orders', 'desc' => 'Orders in progress', 'fallback' => 'back_office', 'nav' => array(array('orders/open', 'Open Orders', 'fa-folder-open-o')), 'pages' => array('orders/open')),
					'orders_followups' => array('label' => 'Orders Follow-up', 'desc' => 'Follow-ups on orders', 'fallback' => 'back_office', 'nav' => array(array('orders/followups', 'Orders Follow-up', 'fa-calendar-check-o')), 'pages' => array('orders/followups')),
					'orders_completed' => array('label' => 'Completed Orders', 'desc' => 'Finished orders', 'fallback' => 'back_office', 'nav' => array(array('orders?status=completed', 'Completed Orders', 'fa-check-circle')), 'pages' => array('orders?status=completed')),
					'orders_closed' => array('label' => 'Closed Orders', 'desc' => 'Closed orders', 'fallback' => 'back_office', 'nav' => array(array('orders?status=closed', 'Closed Orders', 'fa-flag-checkered')), 'pages' => array('orders?status=closed')),
					'orders_rejected' => array('label' => 'Rejected / Lost Orders', 'desc' => 'Rejected or lost orders', 'fallback' => 'back_office', 'nav' => array(array('orders?status=rejected_lost', 'Rejected / Lost Orders', 'fa-times-circle')), 'pages' => array('orders?status=rejected_lost')),
					'orders_management' => array('label' => 'All Orders', 'desc' => 'Every order in the company', 'fallback' => 'back_office', 'nav' => array(array('orders', 'View All Orders', 'fa-list')), 'pages' => array('orders'), 'write' => array('orders/add')),
					'orders_invoices' => array('label' => 'Invoices', 'desc' => 'Order invoices', 'fallback' => 'back_office', 'nav' => array(array('orders/invoices', 'Invoices', 'fa-file-text-o')), 'pages' => array('orders/invoices', 'invoices')),
					'orders_payments' => array('label' => 'Payments', 'desc' => 'Payments received on orders', 'fallback' => 'back_office', 'nav' => array(array('orders/payments', 'Payments', 'fa-credit-card')), 'pages' => array('orders/payments', 'payments')),
				),
			),
			'kam' => array(
				'title' => 'KAM', 'key' => 'kam', 'match' => array('kam'),
				'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
				'items' => array(
					'kam_clients' => array('label' => 'My Client', 'desc' => 'Key account clients assigned to you', 'fallback' => 'kam', 'nav' => array(array('kam/my-client', 'My Client', 'fa-user')), 'pages' => array('kam/my-client', 'kam/my_client', 'kam')),
					'kam_schedule' => array('label' => 'Create Schedule', 'desc' => 'Plan client visits and pitches', 'fallback' => 'kam', 'nav' => array(array('kam/schedule', 'Create Schedule', 'fa-calendar')), 'pages' => array('kam/schedule', 'kam/pitch-schedule')),
					'kam_sales_approval' => array('label' => 'Pending Sales Approval', 'desc' => 'Approve orders as sales', 'fallback' => 'kam', 'nav' => array(array('orders/pending_sales_approval', 'Pending Sales Approval', 'fa-check-circle')), 'pages' => array('orders/pending_sales_approval')),
					'kam_task_followup' => array('label' => 'Task Follow-up', 'desc' => 'Client task follow-ups', 'fallback' => 'kam', 'nav' => array(array('kam/task-followup', 'Task Follow-up', 'fa-tasks')), 'pages' => array('kam/task-followup')),
					'kam_reminders' => array('label' => 'Reminders', 'desc' => 'Client reminders', 'fallback' => 'kam', 'nav' => array(array('kam/reminders', 'Reminders', 'fa-bell-o')), 'pages' => array('kam/reminders')),
					'kam_report' => array('label' => 'KAM Report', 'desc' => 'Report of KAM activity', 'fallback' => 'kam', 'nav' => array(array('kam/report', 'KAM Report', 'fa-bar-chart')), 'pages' => array('kam/report'), 'levels' => 'r'),
				),
			),
			'hr' => array(
				'title' => 'HR & Training', 'key' => 'hr', 'match' => array('hr'),
				'icon' => '<rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><line x1="15" y1="8" x2="17" y2="8"></line><line x1="15" y1="12" x2="17" y2="12"></line><line x1="7" y1="16" x2="17" y2="16"></line>',
				'note' => 'Holiday requests stay open to every user from the profile menu for their own data; Self Training and Search Topics (Help & Support) are rights in Core Modules. The rights here add the HR menu and managing everyone.',
				'items' => array(
					'hr' => array('label' => 'HR Overview', 'desc' => 'HR dashboard', 'nav' => array(array('hr', 'Overview', 'fa-th-large')), 'pages' => array('hr')),
					'hr_staff' => array('label' => 'Staff (Add / View)', 'desc' => 'Staff list and each person\'s profile; Full can add, edit, reset logins and enable or disable staff (never Admin accounts)', 'fallback' => 'users', 'nav' => array(array('hr/staff/add', 'Add Staff', 'fa-user-plus', 'write'), array('hr/staff', 'View Staff', 'fa-id-card-o', 'read')), 'pages' => array('hr/staff'), 'write' => array('hr/staff/add')),
					'hr_attendance' => array('label' => 'Attendance & GPS', 'desc' => 'Attendance with location', 'fallback' => 'hr', 'nav' => array(array('hr/attendance', 'Attendance & GPS', 'fa-map-marker')), 'pages' => array('hr/attendance')),
					'hr_shifts' => array('label' => 'Shifts', 'desc' => 'Create and assign shifts', 'fallback' => 'hr', 'nav' => array(array('hr/shifts', 'Shifts', 'fa-clock-o')), 'pages' => array('hr/shifts'), 'write' => array('hr/shifts/add', 'hr/shifts/assign')),
					'hr_leaves' => array('label' => 'Holiday Requests (all staff)', 'desc' => 'See and approve everyone\'s leave', 'fallback' => 'hr', 'nav' => array(array('hr/leaves', 'Holiday Request', 'fa-calendar-check-o'))),
					'hr_training' => array('label' => 'Manage Self Training', 'desc' => 'Upload training material and see everyone\'s progress', 'fallback' => 'hr', 'nav' => array(array('self-training/manage', 'Self Training', 'fa-graduation-cap', 'write')), 'write' => array('self-training/manage')),
					'hr_topics' => array('label' => 'Search Topics', 'desc' => 'HR help topics; Full can edit topics', 'fallback' => 'hr', 'nav' => array(array('hr/search_topics', 'Search Topics', 'fa-search'))),
					'hr_targets' => array('label' => 'Targets', 'desc' => 'Sales targets per person', 'fallback' => 'hr', 'nav' => array(array('hr/targets', 'Targets', 'fa-crosshairs')), 'pages' => array('hr/targets'), 'write' => array('hr/targets/add')),
					'hr_lifecycle' => array('label' => 'Employee Lifecycle', 'desc' => 'Joining, probation and exit', 'fallback' => 'hr', 'nav' => array(array('hr/lifecycle', 'Employee Lifecycle', 'fa-users')), 'pages' => array('hr/lifecycle')),
					'hr_payroll' => array('label' => 'Payroll', 'desc' => 'Salaries and payslips', 'fallback' => 'hr', 'nav' => array(array('hr/payroll', 'Payroll', 'fa-money')), 'pages' => array('hr/payroll'), 'write' => array('hr/payroll/generate')),
					'hr_taxation' => array('label' => 'Taxation & Gratuity', 'desc' => 'Tax and end-of-service figures', 'fallback' => 'hr', 'nav' => array(array('hr/taxation', 'Taxation & Gratuity', 'fa-calculator')), 'pages' => array('hr/taxation')),
					'hr_appraisals' => array('label' => 'Appraisals', 'desc' => 'Performance reviews', 'fallback' => 'hr', 'nav' => array(array('hr/appraisals', 'Appraisals', 'fa-star-o')), 'pages' => array('hr/appraisals'), 'write' => array('hr/appraisals/add')),
					'hr_documents' => array('label' => 'Document Vault', 'desc' => 'Employee documents', 'fallback' => 'hr', 'nav' => array(array('hr/documents', 'Document Vault', 'fa-folder-open-o')), 'pages' => array('hr/documents'), 'write' => array('hr/documents/add')),
					'events_list' => array('label' => 'Manage Events & Festivals', 'desc' => 'Publish company holidays, festivals, events and awards for everyone', 'fallback' => 'events', 'nav' => array(array('events/manage', 'Events & Festivals', 'fa-calendar', 'write')), 'write' => array('events/manage')),
				),
			),
			'reports' => array(
				'title' => 'Reports', 'key' => 'reports', 'match' => array('reports', 'download-report', 'admin-reports', 'revenue-list', 'company-projections', 'performance-report'),
				'icon' => '<line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line>',
				'items' => array(
					'reports_revenue' => array('label' => 'Revenue List', 'desc' => 'Revenue by order', 'fallback' => 'reports', 'nav' => array(array('reports/revenue-list', 'Revenue List', 'fa-money')), 'pages' => array('reports/revenue-list', 'revenue-list'), 'levels' => 'r'),
					'reports_projections' => array('label' => 'Company Projections', 'desc' => 'Forecast figures', 'fallback' => 'reports', 'nav' => array(array('reports/company-projections', 'Company Projections', 'fa-line-chart')), 'pages' => array('reports/company-projections', 'company-projections'), 'levels' => 'r'),
					'reports_download' => array('label' => 'Report Downloads', 'desc' => 'Download report centre', 'fallback' => 'reports', 'nav' => array(array('download-report', 'Report', 'fa-file-pdf-o')), 'pages' => array('download-report', 'download-report/drilldown', 'admin-reports', 'reports/admin'), 'levels' => 'r'),
					'reports_insights' => array('label' => 'Sales & Activity', 'desc' => 'Sales vs target, calls, lead funnel, services, overdue follow-ups (own and team only)', 'fallback' => 'reports', 'nav' => array(array('reports/insights', 'Sales & Activity', 'fa-area-chart')), 'pages' => array('reports/insights'), 'levels' => 'r'),
					'reports_leads' => array('label' => 'Leads Report', 'desc' => 'Lead counts by stage and person', 'fallback' => 'reports', 'nav' => array(array('leads/report', 'Leads Report', 'fa-bullseye')), 'pages' => array('leads/report'), 'levels' => 'r'),
					'reports_users' => array('label' => 'Users Report', 'desc' => 'Activity per user', 'fallback' => 'reports_leads', 'nav' => array(array('users/report', 'Users Report', 'fa-user')), 'pages' => array('users/report'), 'levels' => 'r'),
					'reports_perf_dep' => array('label' => 'Department Performance', 'desc' => 'Performance by department', 'fallback' => 'reports', 'nav' => array(array('performance-report', 'Department Performance', 'fa-building-o')), 'pages' => array('performance-report'), 'levels' => 'r'),
					'reports_perf_ind' => array('label' => 'Individual Performance', 'desc' => 'Performance per person', 'fallback' => 'reports', 'nav' => array(array('performance-report/individual', 'Individual Performance', 'fa-user-circle-o')), 'pages' => array('performance-report/individual'), 'levels' => 'r'),
				),
			),
			'system' => array(
				'title' => 'System', 'key' => 'system', 'match' => array('roles', 'terms', 'users', 'user', 'assigned-leads', 'admin-logs', 'system', 'categories', 'category-tree', 'integrations'),
				'icon' => '<circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>',
				'items' => array(
					'users' => array('label' => 'Users', 'desc' => 'User list; Full can add and edit users', 'nav' => array(array('users/add', 'Add Staff', 'fa-user-plus', 'write'), array('users', 'View Staff', 'fa-id-card-o', 'read')), 'pages' => array('users'), 'write' => array('users/add', 'user/add')),
					'roles' => array('label' => 'Roles & Permissions', 'desc' => 'This page. Full lets the person change roles and user rights', 'nav' => array(array('roles', 'Roles & Permissions', 'fa-shield')), 'pages' => array('roles'), 'write' => array('roles/add')),
					'terms' => array('label' => 'Category', 'desc' => 'Categories and terms used in forms', 'nav' => array(array('terms', 'Category', 'fa-list')), 'pages' => array('terms', 'categories', 'category-tree'), 'write' => array('terms/add')),
					'leads_import' => array('label' => 'Import Database', 'desc' => 'Upload records from a file', 'nav' => array(array('leads/import', 'Import Database', 'fa-upload')), 'pages' => array('leads/import'), 'levels' => 'r'),
					'leads_export' => array('label' => 'Export Leads', 'desc' => 'Download records to a file', 'nav' => array(array('leads/export', 'Export Leads', 'fa-download')), 'pages' => array('leads/export'), 'levels' => 'r'),
					'assigned_leads' => array('label' => 'Allotted Database', 'desc' => 'Allotted and non-allotted records', 'nav' => array(array('assigned-leads', 'Allotted Database', 'fa-tasks'), array('assigned-leads?alloc_filter=non_allotted', 'Non-Allotted Database', 'fa-folder-open-o')), 'pages' => array('assigned-leads')),
					'admin_logs' => array('label' => 'Admin Logs', 'desc' => 'Audit log of changes', 'nav' => array(array('admin-logs', 'Admin Logs', 'fa-history')), 'pages' => array('admin-logs', 'system/admin-logs', 'system/admin_logs'), 'levels' => 'r'),
					'live_location' => array('label' => 'Live Location', 'desc' => 'Where staff signed in from', 'fallback' => 'admin_logs', 'nav' => array(array('admin-logs/live-location', 'Live Location', 'fa-map-marker')), 'pages' => array('admin-logs/live-location', 'admin-logs/live_location', 'system/live-location', 'system/live_location'), 'levels' => 'r'),
					'system_assignee' => array('label' => 'Assignee Management', 'desc' => 'Move clients between owners', 'nav' => array(array('system/assign', 'Assignee Management', 'fa-random')), 'pages' => array('system/assign', 'system/assignee')),
					'system_kam_clients' => array('label' => 'KAM Clients (all)', 'desc' => 'Every KAM client in the company', 'fallback' => 'kam_clients', 'nav' => array(array('system/kam_clients', 'KAM Clients', 'fa-briefcase')), 'pages' => array('system/kam_clients', 'system/kam-clients')),
					'calling_api' => array('label' => 'Calling API', 'desc' => 'Phone provider settings', 'nav' => array(array('system/calling-api', 'Calling API', 'fa-phone-square')), 'pages' => array('system/calling-api', 'system/calling_api')),
					'integrations' => array('label' => 'Third-Party API', 'desc' => 'Email SMTP, helpdesk and other integrations', 'nav' => array(array('system/integrations', 'Third-Party API', 'fa-plug')), 'pages' => array('system/integrations', 'integrations')),
					'qc_panel' => array('label' => 'QC Panel', 'desc' => 'Quality check of the CRM pages', 'fallback' => 'system', 'nav' => array(array('system/qc', 'QC Panel', 'fa-check-square-o')), 'pages' => array('system/qc', 'system/qoc'), 'levels' => 'r'),
				),
			),
		);
		return $cat;
	}
}

/** Flat list key => item (with 'group'). */
if (!function_exists('crm_perm_items')) {
	function crm_perm_items()
	{
		static $flat = null;
		if ($flat !== null) return $flat;
		$flat = array();
		foreach (crm_perm_catalog() as $gk => $g) {
			foreach ($g['items'] as $k => $it) {
				$it['group'] = $gk;
				$flat[$k] = $it;
			}
		}
		return $flat;
	}
}

if (!function_exists('crm_perm_rank')) {
	function crm_perm_rank($v)
	{
		return $v === 'write' ? 2 : ($v === 'read' ? 1 : 0);
	}
}

/**
 * Value of one key in a stored permission map:
 * stored value, else the item's fallback key, else (group/legacy key) the best of its items.
 */
if (!function_exists('crm_perm_resolve')) {
	function crm_perm_resolve($map, $key, $depth = 0)
	{
		if (!is_array($map)) $map = array();
		if (isset($map[$key]) && in_array($map[$key], array('no_access', 'read', 'write'), true)) {
			return $map[$key];
		}
		if ($depth > 4) return 'no_access';
		$items = crm_perm_items();
		if (isset($items[$key]) && !empty($items[$key]['fallback'])) {
			return crm_perm_resolve($map, $items[$key]['fallback'], $depth + 1);
		}
		// Group key or older combined key: best of the items it covers.
		$children = array();
		foreach (crm_perm_catalog() as $g) {
			if ($g['key'] === $key) $children = array_keys($g['items']);
		}
		if ($key === 'tasks') $children = array('internal_tasks', 'external_tasks', 'team_tasks');
		if ($key === 'events') $children = array('events_list', 'events_view');
		$best = 'no_access';
		foreach ($children as $c) {
			if (!isset($map[$c])) continue;
			if (crm_perm_rank($map[$c]) > crm_perm_rank($best)) $best = $map[$c];
		}
		return $best;
	}
}

/** Full map (every catalogue key + group keys) resolved from a stored map. */
if (!function_exists('crm_perm_expand')) {
	function crm_perm_expand($map)
	{
		$out = array();
		foreach (crm_perm_items() as $k => $it) {
			$v = crm_perm_resolve($map, $k);
			if (isset($it['levels']) && $it['levels'] === 'r' && $v === 'write') $v = 'read';
			$out[$k] = $v;
		}
		return $out;
	}
}

/**
 * Clean a submitted map: only catalogue keys, valid levels, then the group keys
 * (and older combined keys) derived from the items so older checks keep working.
 */
if (!function_exists('crm_perm_normalize')) {
	function crm_perm_normalize($raw)
	{
		$clean = array();
		foreach (crm_perm_items() as $k => $it) {
			$v = (is_array($raw) && isset($raw[$k])) ? $raw[$k] : 'no_access';
			if (!in_array($v, array('no_access', 'read', 'write'), true)) $v = 'no_access';
			if (isset($it['levels']) && $it['levels'] === 'r' && $v === 'write') $v = 'read';
			$clean[$k] = $v;
		}
		$best = function ($keys) use ($clean) {
			$b = 'no_access';
			foreach ($keys as $k) {
				if (isset($clean[$k]) && crm_perm_rank($clean[$k]) > crm_perm_rank($b)) $b = $clean[$k];
			}
			return $b;
		};
		$items = crm_perm_items();
		foreach (crm_perm_catalog() as $g) {
			// "hr" is both the HR Overview item and the group key: never overwrite an item.
			if ($g['key'] !== '' && !isset($items[$g['key']])) $clean[$g['key']] = $best(array_keys($g['items']));
		}
		$clean['tasks'] = $best(array('internal_tasks', 'external_tasks', 'team_tasks'));
		$clean['events'] = $best(array('events_list', 'events_view'));
		return $clean;
	}
}

/**
 * Role, level and name are copied into the session at login. Re-read them once per request,
 * so a change made in Users or Roles applies on the user's next page load or refresh.
 */
if (!function_exists('crm_sync_session_user')) {
	function crm_sync_session_user()
	{
		static $done = false;
		if ($done) return;
		$ci =& get_instance();
		if (!isset($ci->session)) return;
		$done = true;
		$uid = (int)$ci->session->userdata('manager_id');
		if ($uid <= 0) return;
		if (!isset($ci->db)) $ci->load->database();
		$u = $ci->db->select('user_type, user_level, name')->where('id', $uid)->get('ci_user')->row_array();
		if (empty($u)) return;
		$set = array();
		if ((string)$ci->session->userdata('manager_role') !== (string)$u['user_type']) $set['manager_role'] = $u['user_type'];
		if ((string)$ci->session->userdata('manager_level') !== (string)$u['user_level']) $set['manager_level'] = $u['user_level'];
		if ((string)$ci->session->userdata('manager_name') !== (string)$u['name']) $set['manager_name'] = $u['name'];
		if ($set) $ci->session->set_userdata($set);
	}
}

/** Can the signed-in user (or $user_id) use $key at $level ('read' or 'write')? */
if (!function_exists('crm_can')) {
	function crm_can($key, $level = 'read', $user_id = null)
	{
		$ci =& get_instance();
		if (!isset($ci->ci_admin_model)) $ci->load->model('ci_admin_model');
		return $ci->ci_admin_model->check_permission($key, $level, false, $user_id);
	}
}

if (!function_exists('crm_is_full_admin')) {
	function crm_is_full_admin($user_id = null)
	{
		$ci =& get_instance();
		$uid = ($user_id === null) ? (int)$ci->session->userdata('manager_id') : (int)$user_id;
		$role = (int)$ci->session->userdata('manager_role');
		if ($user_id !== null) {
			$r = $ci->db->select('user_type')->where('id', $uid)->get('ci_user')->row_array();
			$role = !empty($r) ? (int)$r['user_type'] : 0;
		}
		if ($role === 1 || $uid === 1) return true;
		if (!isset($ci->ci_admin_model)) $ci->load->model('ci_admin_model');
		$pd = $ci->ci_admin_model->get_user_permission_data($uid);
		return $pd['mode'] === 'admin';
	}
}

/** Menu groups with the links this user may see (empty groups removed). */
if (!function_exists('crm_nav_menu')) {
	function crm_nav_menu()
	{
		$menu = array();
		foreach (crm_perm_catalog() as $gk => $g) {
			if (isset($g['menu']) && $g['menu'] === false) continue;
			$links = array();
			foreach ($g['items'] as $k => $it) {
				if (empty($it['nav'])) continue;
				foreach ($it['nav'] as $n) {
					$need = isset($n[3]) ? $n[3] : 'read';
					if (crm_can($k, $need)) {
						$links[] = array('url' => $n[0], 'label' => $n[1], 'icon' => $n[2], 'external' => (strpos($n[0], 'http') === 0));
					}
				}
			}
			if ($links) {
				$menu[$gk] = array('title' => $g['title'], 'icon' => $g['icon'], 'match' => isset($g['match']) ? $g['match'] : array(), 'links' => $links);
			}
		}
		return $menu;
	}
}

/** Key of the menu group for the current page: an exact link match wins, else the group owning the first URL part. */
if (!function_exists('crm_nav_active_group')) {
	function crm_nav_active_group($menu, $path, $query)
	{
		$rule = crm_page_rule($path, $query);
		if ($rule !== null) {
			$items = crm_perm_items();
			if (isset($items[$rule[0]]) && isset($menu[$items[$rule[0]]['group']])) return $items[$rule[0]]['group'];
		}
		$first = explode('/', $path);
		foreach ($menu as $gk => $g) {
			if (in_array($first[0], $g['match'], true)) return $gk;
		}
		return '';
	}
}

if (!function_exists('crm_page_matches')) {
	function crm_page_matches($spec, $path, $query)
	{
		$parts = explode('?', $spec, 2);
		if (trim($parts[0], '/') !== $path) return false;
		if (isset($parts[1])) {
			parse_str($parts[1], $need);
			foreach ($need as $k => $v) {
				if (!isset($query[$k]) || (string)$query[$k] !== (string)$v) return false;
			}
		}
		return true;
	}
}

/**
 * Which catalogue item and level guard this page? Returns array(key, level) or null.
 * Pages with a query condition (orders?status=closed) win over the plain path.
 */
if (!function_exists('crm_page_rule')) {
	function crm_page_rule($path, $query)
	{
		$best = null;
		$best_score = -1;
		foreach (crm_perm_items() as $k => $it) {
			foreach (array('pages' => 'read', 'write' => 'write') as $field => $lvl) {
				if (empty($it[$field])) continue;
				foreach ($it[$field] as $spec) {
					if (crm_page_matches($spec, $path, $query)) {
						$score = strpos($spec, '?') !== false ? 2 : 1;
						if ($score > $best_score) {
							$best = array($k, $lvl);
							$best_score = $score;
						}
					}
				}
			}
		}
		return $best;
	}
}

/** Id of the "Presentation" role (TV screen users), 0 when there is none. */
if (!function_exists('crm_presentation_role_id')) {
	function crm_presentation_role_id()
	{
		static $id = null;
		if ($id !== null) return $id;
		$ci =& get_instance();
		if (!isset($ci->db)) $ci->load->database();
		$row = $ci->db->query("SELECT id FROM ci_roles WHERE LOWER(TRIM(title)) = 'presentation' ORDER BY id ASC LIMIT 1")->row_array();
		$id = !empty($row['id']) ? (int)$row['id'] : 0;
		return $id;
	}
}

/** True when the signed-in user (or the given role) is a Presentation user. */
if (!function_exists('crm_is_presentation_user')) {
	function crm_is_presentation_user($role_id = null)
	{
		if ($role_id === null) {
			$ci =& get_instance();
			$role_id = (int)$ci->session->userdata('manager_role');
		}
		$pid = crm_presentation_role_id();
		return $pid > 0 && (int)$role_id === $pid;
	}
}

/** Office codes (centers) offered on the user form: the standard list plus any already in use. */
if (!function_exists('crm_office_codes')) {
	function crm_office_codes()
	{
		$codes = array('01 - UAE-Dubai-SZR', '07 - UAE-WFH', '08 - UAE-Remote', '09 - UAE-Other', '11 - IN-Jaipur', '12 - IN - Delhi', '13 - IN-Indore', '17 - IN-WFH', '18 - IN-Remote', '19 - IN-Other');
		$ci =& get_instance();
		foreach ($ci->db->query("SELECT DISTINCT office FROM ci_user WHERE office IS NOT NULL AND office != ''")->result_array() as $r) {
			$o = trim($r['office']);
			if ($o !== '' && !in_array($o, $codes, true)) $codes[] = $o;
		}
		sort($codes);
		return $codes;
	}
}

/** Centers a Presentation user may show (table ci_user_live_offices, JSON list; ci_user has no room for another column). */
if (!function_exists('crm_user_live_offices')) {
	function crm_user_live_offices($user)
	{
		$list = array();
		$raw = '';
		if (!empty($user['id'])) {
			$CI =& get_instance();
			if ($CI->db->table_exists('ci_user_live_offices')) {
				$row = $CI->db->select('offices')->where('user_id', (int)$user['id'])->get('ci_user_live_offices')->row_array();
				if ($row) $raw = (string)$row['offices'];
			}
		}
		if ($raw !== '') {
			$dec = json_decode($raw, true);
			if (is_array($dec)) $list = array_values(array_filter(array_map('trim', $dec), 'strlen'));
		}
		if (empty($list) && !empty($user['office'])) $list = array(trim($user['office']));
		return $list;
	}
}

/** First page this user may open (used when a page is refused). */
if (!function_exists('crm_landing_url')) {
	function crm_landing_url()
	{
		if (crm_is_presentation_user()) return 'manager/live/presentation';
		if (crm_can('dashboard')) return 'manager/dashboard';
		foreach (crm_nav_menu() as $g) {
			foreach ($g['links'] as $l) {
				if (!$l['external']) return 'manager/' . $l['url'];
			}
		}
		return 'manager/profile';
	}
}
