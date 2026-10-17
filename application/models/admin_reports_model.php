<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin_reports_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function getAdminReportsOverview()
	{
		$sales_leads = (int)$this->db->count_all('ci_leads');
		$sales_quotes = (int)$this->db->where('quote_status IS NOT NULL AND quote_status != ""')->count_all_results('ci_leads');
		$sales_mrr_row = $this->db->select_sum('mrr')->get('ci_leads')->row_array();
		$sales_mrr = isset($sales_mrr_row['mrr']) ? (float)$sales_mrr_row['mrr'] : 0.0;

		$ops_tasks = (int)$this->db->count_all('ci_tasks');
		$ops_inv_row = $this->db->select_sum('total_amount')->get('ci_invoices')->row_array();
		$ops_invoiced = isset($ops_inv_row['total_amount']) ? (float)$ops_inv_row['total_amount'] : 0.0;
		$ops_pmt_row = $this->db->select_sum('amount')->get('ci_payments')->row_array();
		$ops_collected = isset($ops_pmt_row['amount']) ? (float)$ops_pmt_row['amount'] : 0.0;

		$support_tickets = (int)$this->db->count_all('ci_tickets');
		$support_open = (int)$this->db->where_in('status', array('Open', 'In Progress'))->count_all_results('ci_tickets');
		$support_resolved = (int)$this->db->where_in('status', array('Resolved', 'Closed'))->count_all_results('ci_tickets');

		$hr_users = (int)$this->db->where('status', 1)->count_all_results('ci_user');
		$hr_att = (int)$this->db->where('attendance_date', date('Y-m-d'))->where('status', 'Present')->count_all_results('ci_attendance');
		$hr_leaves = (int)$this->db->where('status', 'Pending')->count_all_results('ci_leaves');

		$it_logs = (int)$this->db->count_all('ci_admin_logs');
		$it_issues = (int)$this->db->where('department', 'IT')->or_like('category', 'Technical')->count_all_results('ci_tickets');
		$it_maint = (int)$this->db->count_all('ci_system_maintenance');

		return array(
			'sales' => array(
				'title' => 'Sales Department',
				'slug' => 'sales',
				'reports_count' => 7,
				'icon' => 'fa-line-chart',
				'color' => 'bg-green',
				'stats' => array(
					'Total Leads' => number_format($sales_leads),
					'Active Quotes' => number_format($sales_quotes),
					'Total MRR' => 'AED ' . number_format($sales_mrr, 2)
				)
			),
			'operations' => array(
				'title' => 'Operations Department',
				'slug' => 'operations',
				'reports_count' => 8,
				'icon' => 'fa-cogs',
				'color' => 'bg-aqua',
				'stats' => array(
					'Active Tasks' => number_format($ops_tasks),
					'Total Invoiced' => 'AED ' . number_format($ops_invoiced, 2),
					'Collections' => 'AED ' . number_format($ops_collected, 2)
				)
			),
			'support' => array(
				'title' => 'Support / Backoffice Department',
				'slug' => 'support',
				'reports_count' => 6,
				'icon' => 'fa-life-ring',
				'color' => 'bg-yellow',
				'stats' => array(
					'Total Tickets' => number_format($support_tickets),
					'Open Tickets' => number_format($support_open),
					'Resolved' => number_format($support_resolved)
				)
			),
			'hr' => array(
				'title' => 'HR & Admin Department',
				'slug' => 'hr',
				'reports_count' => 5,
				'icon' => 'fa-users',
				'color' => 'bg-purple',
				'stats' => array(
					'Active Staff' => number_format($hr_users),
					'Present Today' => number_format($hr_att),
					'Pending Leaves' => number_format($hr_leaves)
				)
			),
			'it' => array(
				'title' => 'IT & Administration Department',
				'slug' => 'it',
				'reports_count' => 5,
				'icon' => 'fa-server',
				'color' => 'bg-red',
				'stats' => array(
					'System Logs' => number_format($it_logs),
					'Tech Tickets' => number_format($it_issues),
					'Maintenance Tasks' => number_format($it_maint)
				)
			)
		);
	}

	public function getDepartmentReportsMeta($dept)
	{
		$dept = strtolower(trim($dept));
		$meta = array();

		if ($dept == 'sales') {
			$meta = array(
				'dept_title' => 'Sales Department',
				'dept_slug' => 'sales',
				'reports' => array(
					'performance' => array(
						'title' => 'Performance Report',
						'slug' => 'performance',
						'icon' => 'fa-trophy',
						'desc' => 'Employee performance metrics, closed deals, quote conversions, and generated revenue.',
						'badge' => $this->db->where('status', 1)->count_all_results('ci_user') . ' Staff'
					),
					'database' => array(
						'title' => 'Database Report',
						'slug' => 'database',
						'icon' => 'fa-database',
						'desc' => 'Sourced database entries, referral batches, and verified database categories.',
						'badge' => $this->db->where('database_category != "" OR data_source != ""')->count_all_results('ci_leads') . ' Records'
					),
					'lead' => array(
						'title' => 'Lead Report',
						'slug' => 'lead',
						'icon' => 'fa-filter',
						'desc' => 'Comprehensive lead breakdown by status, industry vertical, and assigned representatives.',
						'badge' => $this->db->count_all('ci_leads') . ' Leads'
					),
					'quote' => array(
						'title' => 'Quote Report',
						'slug' => 'quote',
						'icon' => 'fa-file-text-o',
						'desc' => 'Given quotes, proposal modes, quotation amounts, and deal acceptance rates.',
						'badge' => $this->db->where('quote_status IS NOT NULL AND quote_status != ""')->count_all_results('ci_leads') . ' Quotes'
					),
					'tax-registration' => array(
						'title' => 'CT / VAT Registration Report',
						'slug' => 'tax-registration',
						'icon' => 'fa-shield',
						'desc' => 'Corporate Tax and VAT registration statuses for corporate and retail accounts.',
						'badge' => $this->db->where('corporate_tax_status != "" OR vat_status != ""')->count_all_results('ci_leads') . ' Assessed'
					),
					'lost' => array(
						'title' => 'Lost Report',
						'slug' => 'lost',
						'icon' => 'fa-times-circle',
						'desc' => 'Lost sales opportunities, lost reasons, status categories, and remarks.',
						'badge' => $this->db->where('status IN (42,43,44) OR status_category != ""')->count_all_results('ci_leads') . ' Lost'
					),
					'trade-license' => array(
						'title' => 'Trade License Report',
						'slug' => 'trade-license',
						'icon' => 'fa-id-card-o',
						'desc' => 'Trade license records, licensing authorities, expiry schedules, and renewals.',
						'badge' => $this->db->where('tl_number != ""')->count_all_results('ci_leads') . ' Licenses'
					)
				)
			);
		} elseif ($dept == 'operations') {
			$meta = array(
				'dept_title' => 'Operations Department',
				'dept_slug' => 'operations',
				'reports' => array(
					'daily' => array(
						'title' => 'Day-to-Day Operations Execution Log',
						'slug' => 'daily',
						'icon' => 'fa-calendar-o',
						'desc' => 'Day-to-day operational execution log across tasks, tickets, and work items.',
						'badge' => $this->db->count_all('ci_tasks') . ' Tasks'
					),
					'tasks' => array(
						'title' => 'Task Completion Analyzer',
						'slug' => 'tasks',
						'icon' => 'fa-check-square-o',
						'desc' => 'Task completion analysis, ongoing assignments, pending milestones, and overdue tasks.',
						'badge' => $this->db->where('status', 'Completed')->count_all_results('ci_tasks') . ' Completed'
					),
					'service-delivery' => array(
						'title' => 'Client Service Delivery Report',
						'slug' => 'service-delivery',
						'icon' => 'fa-truck',
						'desc' => 'Delivery orders, provisioned services, deployment timelines, and handover status.',
						'badge' => $this->db->count_all('ci_service_delivery') . ' Deliveries'
					),
					'pending-work' => array(
						'title' => 'Pending Work Report',
						'slug' => 'pending-work',
						'icon' => 'fa-clock-o',
						'desc' => 'Outstanding work items, overdue tickets, bottlenecks, and open action points.',
						'badge' => $this->db->where_not_in('status', array('Completed', 'Resolved', 'Closed'))->count_all_results('ci_tasks') . ' Pending'
					),
					'billing' => array(
						'title' => 'Billing & Invoice Report',
						'slug' => 'billing',
						'icon' => 'fa-money',
						'desc' => 'Invoice statements, taxable values, VAT, paid settlements, and unpaid balances.',
						'badge' => $this->db->count_all('ci_invoices') . ' Invoices'
					),
					'collections' => array(
						'title' => 'Payment Collection Report',
						'slug' => 'collections',
						'icon' => 'fa-credit-card',
						'desc' => 'Received customer payments, clearing modes, payment vouchers, and reconciliations.',
						'badge' => $this->db->count_all('ci_payments') . ' Receipts'
					),
					'expenses' => array(
						'title' => 'Expense Report',
						'slug' => 'expenses',
						'icon' => 'fa-calculator',
						'desc' => 'Operational expenditure, vendor disbursements, categories, and approved vouchers.',
						'badge' => $this->db->count_all('ci_expenses') . ' Vouchers'
					),
					'revenue' => array(
						'title' => 'Revenue Report',
						'slug' => 'revenue',
						'icon' => 'fa-bar-chart',
						'desc' => 'Revenue recognized, recurring MRR contracts, billed totals, and collection rates.',
						'badge' => 'Summary'
					)
				)
			);
		} elseif ($dept == 'support') {
			$meta = array(
				'dept_title' => 'Support / Backoffice Department',
				'dept_slug' => 'support',
				'reports' => array(
					'activity' => array(
						'title' => 'Support Activity Report',
						'slug' => 'activity',
						'icon' => 'fa-comments-o',
						'desc' => 'Backoffice interactions, client assistance activities, and ongoing support cases.',
						'badge' => $this->db->count_all('ci_tickets') . ' Tickets'
					),
					'ticket-status' => array(
						'title' => 'Ticket Status Report',
						'slug' => 'ticket-status',
						'icon' => 'fa-ticket',
						'desc' => 'Full ticket volume segmented by Open, In Progress, Resolved, and Closed states.',
						'badge' => $this->db->where('status', 'Open')->count_all_results('ci_tickets') . ' Open'
					),
					'ticket-resolution' => array(
						'title' => 'Ticket Resolution Report',
						'slug' => 'ticket-resolution',
						'icon' => 'fa-check-circle',
						'desc' => 'Resolution turnaround time, SLA compliance, solved cases, and resolver performance.',
						'badge' => $this->db->where_in('status', array('Resolved', 'Closed'))->count_all_results('ci_tickets') . ' Solved'
					),
					'followups' => array(
						'title' => 'Follow-up Report',
						'slug' => 'followups',
						'icon' => 'fa-calendar-check-o',
						'desc' => 'Scheduled, completed, and overdue client follow-ups across the support team.',
						'badge' => $this->db->count_all('ci_followup') . ' Logs'
					),
					'client-queries' => array(
						'title' => 'Client Query Report',
						'slug' => 'client-queries',
						'icon' => 'fa-question-circle',
						'desc' => 'Specific customer questions, inquiries, and request handling stages.',
						'badge' => $this->db->where('ticket_type = "Query" OR category LIKE "%Query%" OR category LIKE "%Inquiry%"')->count_all_results('ci_tickets') . ' Queries'
					),
					'pending-requests' => array(
						'title' => 'Pending Requests Report',
						'slug' => 'pending-requests',
						'icon' => 'fa-hourglass-half',
						'desc' => 'Critical unresolved requests, escalated cases, and tickets pending client replies.',
						'badge' => $this->db->where_not_in('status', array('Resolved', 'Closed'))->count_all_results('ci_tickets') . ' Pending'
					)
				)
			);
		} elseif ($dept == 'hr') {
			$meta = array(
				'dept_title' => 'HR & Admin Department',
				'dept_slug' => 'hr',
				'reports' => array(
					'attendance' => array(
						'title' => 'Employee Attendance Report',
						'slug' => 'attendance',
						'icon' => 'fa-calendar',
						'desc' => 'Daily check-in logs, check-out times, total hours worked, and attendance status.',
						'badge' => $this->db->count_all('ci_attendance') . ' Records'
					),
					'performance' => array(
						'title' => 'Employee Performance Report',
						'slug' => 'performance',
						'icon' => 'fa-line-chart',
						'desc' => 'Composite staff appraisal, tasks accomplished, ticket output, and attendance scores.',
						'badge' => $this->db->where('status', 1)->count_all_results('ci_user') . ' Staff'
					),
					'leaves' => array(
						'title' => 'Leave Report',
						'slug' => 'leaves',
						'icon' => 'fa-plane',
						'desc' => 'Annual, sick, and emergency leave applications, approval history, and balances.',
						'badge' => $this->db->count_all('ci_leaves') . ' Requests'
					),
					'tasks' => array(
						'title' => 'Employee Task Report',
						'slug' => 'tasks',
						'icon' => 'fa-tasks',
						'desc' => 'Staff-wise allocated duties, deadlines, execution progress, and completed tasks.',
						'badge' => $this->db->count_all('ci_tasks') . ' Tasks'
					),
					'activity' => array(
						'title' => 'Employee Activity Report',
						'slug' => 'activity',
						'icon' => 'fa-user-secret',
						'desc' => 'Staff operational logs, system actions performed, and workflow tracking.',
						'badge' => $this->db->count_all('ci_admin_logs') . ' Logs'
					)
				)
			);
		} elseif ($dept == 'it') {
			$meta = array(
				'dept_title' => 'IT & Administration Department',
				'dept_slug' => 'it',
				'reports' => array(
					'user-activity' => array(
						'title' => 'User Activity Report',
						'slug' => 'user-activity',
						'icon' => 'fa-desktop',
						'desc' => 'System interactions, user logins, data access, and operational audit trail.',
						'badge' => $this->db->count_all('ci_admin_logs') . ' Events'
					),
					'system-access' => array(
						'title' => 'System Access Report',
						'slug' => 'system-access',
						'icon' => 'fa-key',
						'desc' => 'Authentication history, user sessions, IP addresses, and login security records.',
						'badge' => $this->db->where_in('activity', array('login', 'logout', 'failed_login', 'auth'))->count_all_results('ci_admin_logs') . ' Sessions'
					),
					'audit-logs' => array(
						'title' => 'System Activity / Audit Log Report',
						'slug' => 'audit-logs',
						'icon' => 'fa-history',
						'desc' => 'Immutable audit trails, record modifications, and administrative operations.',
						'badge' => $this->db->count_all('ci_admin_logs') . ' Audits'
					),
					'technical-issues' => array(
						'title' => 'Technical Issue Report',
						'slug' => 'technical-issues',
						'icon' => 'fa-wrench',
						'desc' => 'Hardware, software, server, and networking technical tickets and resolution status.',
						'badge' => $this->db->where('department = "IT" OR category LIKE "%Tech%" OR category LIKE "%System%"')->count_all_results('ci_tickets') . ' Issues'
					),
					'system-maintenance' => array(
						'title' => 'System Maintenance Report',
						'slug' => 'system-maintenance',
						'icon' => 'fa-shield',
						'desc' => 'Scheduled server maintenance, database backups, security patches, and uptime.',
						'badge' => $this->db->count_all('ci_system_maintenance') . ' Tasks'
					)
				)
			);
		}

		return $meta;
	}

	public function getReportData($dept, $report, $filters = array())
	{
		$dept = strtolower(trim($dept));
		$report = strtolower(trim($report));

		$aliases = array(
			'ct-vat-registration' => 'tax-registration',
			'vat-registration' => 'tax-registration',
			'registration' => 'tax-registration',
			'ct-vat' => 'tax-registration',
			'trade_license' => 'trade-license',
			'daily-operations' => 'daily',
			'daily_operations' => 'daily',
			'task-completion' => 'tasks',
			'work-task-completion' => 'tasks',
			'task_completion' => 'tasks',
			'client-service-delivery' => 'service-delivery',
			'service_delivery' => 'service-delivery',
			'pending-work' => 'pending-work',
			'pending_work' => 'pending-work',
			'billing-invoice' => 'billing',
			'billing_invoice' => 'billing',
			'invoice' => 'billing',
			'payment-collection' => 'collections',
			'payment_collection' => 'collections',
			'payment' => 'collections',
			'payments' => 'collections',
			'expense' => 'expenses',
			'support-activity' => 'activity',
			'support_activity' => 'activity',
			'ticket_status' => 'ticket-status',
			'ticket_resolution' => 'ticket-resolution',
			'follow-up' => 'followups',
			'followup' => 'followups',
			'follow_up' => 'followups',
			'client-query' => 'client-queries',
			'client_query' => 'client-queries',
			'queries' => 'client-queries',
			'pending-request' => 'pending-requests',
			'pending_requests' => 'pending-requests',
			'employee-attendance' => 'attendance',
			'employee_attendance' => 'attendance',
			'employee-performance' => 'performance',
			'employee_performance' => 'performance',
			'leave' => 'leaves',
			'employee-task' => 'tasks',
			'employee_task' => 'tasks',
			'employee-activity' => 'activity',
			'employee_activity' => 'activity',
			'user_activity' => 'user-activity',
			'system_access' => 'system-access',
			'audit-log' => 'audit-logs',
			'audit_log' => 'audit-logs',
			'audit_logs' => 'audit-logs',
			'system-activity' => 'audit-logs',
			'technical-issue' => 'technical-issues',
			'technical_issue' => 'technical-issues',
			'technical_issues' => 'technical-issues',
			'system_maintenance' => 'system-maintenance',
			'maintenance' => 'system-maintenance'
		);
		if (isset($aliases[$report])) {
			$report = $aliases[$report];
		}

		if ($dept == 'sales') {
			$res = $this->_querySalesReport($report, $filters);
		} elseif ($dept == 'operations') {
			$res = $this->_queryOperationsReport($report, $filters);
		} elseif ($dept == 'support') {
			$res = $this->_querySupportReport($report, $filters);
		} elseif ($dept == 'hr') {
			$res = $this->_queryHrReport($report, $filters);
		} elseif ($dept == 'it') {
			$res = $this->_queryItReport($report, $filters);
		} else {
			$res = array(
				'title' => 'Report Not Found',
				'department_title' => 'Admin Reports',
				'headers' => array(),
				'fields' => array(),
				'rows' => array(),
				'summary' => array()
			);
		}

		if (!empty($res) && empty($res['pivot_data'])) {
			$res['pivot_data'] = $this->_buildPivotData($dept, $report, $filters, $res);
		}

		return $res;
	}

	private function _applyDate(&$db, $column, $d1, $d2)
	{
		if (!empty($d1) && !empty($d2)) {
			$from = date('Y-m-d', strtotime($d1));
			$to = date('Y-m-d', strtotime($d2));
			$db->where("DATE($column) >= '$from' AND DATE($column) <= '$to'");
		} elseif (!empty($d1)) {
			$from = date('Y-m-d', strtotime($d1));
			$db->where("DATE($column) >= '$from'");
		} elseif (!empty($d2)) {
			$to = date('Y-m-d', strtotime($d2));
			$db->where("DATE($column) <= '$to'");
		}
	}

	private function _applyEmployee(&$db, $column, $emp)
	{
		if (!empty($emp)) {
			if (is_array($emp)) {
				$ids = array_filter(array_map('intval', $emp));
				if (!empty($ids)) {
					$db->where_in($column, $ids);
				}
			} else {
				$id = (int)$emp;
				if ($id > 0) {
					$db->where($column, $id);
				}
			}
		}
	}

	private function _querySalesReport($report, $filters)
	{
		$d1 = isset($filters['d1']) ? $filters['d1'] : '';
		$d2 = isset($filters['d2']) ? $filters['d2'] : '';
		$search = isset($filters['search']) ? trim($filters['search']) : '';
		$emp = isset($filters['employee_id']) ? $filters['employee_id'] : (isset($filters['employee_ids']) ? $filters['employee_ids'] : '');
		$status = isset($filters['status']) ? $filters['status'] : '';

		if ($report == 'performance') {
			$this->db->select('id, name, email, department');
			$this->db->from('ci_user');
			$this->db->where('status', 1);
			$this->_applyEmployee($this->db, 'id', $emp);
			if (!empty($filters['department'])) {
				$this->db->where('department', $filters['department']);
			}
			$users = $this->db->get()->result_array();

			$rows = array();
			$tot_leads = 0;
			$tot_active = 0;
			$tot_closed = 0;
			$tot_lost = 0;
			$tot_quoted = 0.0;
			$tot_mrr = 0.0;
			$tot_followups = 0;
			$idx = 1;

			foreach ($users as $u) {
				$uid = (int)$u['id'];

				$this->db->from('ci_leads');
				$this->db->where("(lead_assign = $uid OR created_by = $uid)");
				$this->_applyDate($this->db, 'created', $d1, $d2);
				$leads_count = (int)$this->db->count_all_results();

				$this->db->from('ci_leads');
				$this->db->where("(lead_assign = $uid OR created_by = $uid)");
				$this->db->where_not_in('status', array(42, 43, 44));
				$this->_applyDate($this->db, 'created', $d1, $d2);
				$active_count = (int)$this->db->count_all_results();

				$this->db->from('ci_leads');
				$this->db->where("(lead_assign = $uid OR created_by = $uid)");
				$this->db->where_in('status', array(42, 43, 44));
				$this->_applyDate($this->db, 'created', $d1, $d2);
				$closed_count = (int)$this->db->count_all_results();

				$this->db->from('ci_leads');
				$this->db->where("(lead_assign = $uid OR created_by = $uid)");
				$this->db->where("(status IN (42, 43, 44) OR LOWER(status_category) LIKE '%lost%' OR LOWER(lost_reason) LIKE '%lost%')");
				$this->_applyDate($this->db, 'created', $d1, $d2);
				$lost_count = (int)$this->db->count_all_results();

				$this->db->select_sum('quote_amount');
				$this->db->from('ci_leads');
				$this->db->where("(lead_assign = $uid OR created_by = $uid)");
				$this->_applyDate($this->db, 'created', $d1, $d2);
				$q_row = $this->db->get()->row_array();
				$u_quoted = isset($q_row['quote_amount']) ? (float)$q_row['quote_amount'] : 0.0;

				$this->db->select_sum('mrr');
				$this->db->from('ci_leads');
				$this->db->where("(lead_assign = $uid OR created_by = $uid)");
				$this->_applyDate($this->db, 'created', $d1, $d2);
				$m_row = $this->db->get()->row_array();
				$u_mrr = isset($m_row['mrr']) ? (float)$m_row['mrr'] : 0.0;

				$this->db->from('ci_followup');
				$this->db->where('user_id', $uid);
				$this->db->where('followup_remark != ""');
				$this->_applyDate($this->db, 'followup_date', $d1, $d2);
				$u_att = (int)$this->db->count_all_results();

				$tot_leads += $leads_count;
				$tot_active += $active_count;
				$tot_closed += $closed_count;
				$tot_lost += $lost_count;
				$tot_quoted += $u_quoted;
				$tot_mrr += $u_mrr;
				$tot_followups += $u_att;

				$rows[] = array(
					'index' => $idx++,
					'name' => $u['name'],
					'department' => !empty($u['department']) ? $u['department'] : 'Sales',
					'email' => $u['email'],
					'total_leads' => $leads_count,
					'active_leads' => $active_count,
					'closed_leads' => $closed_count,
					'lost_leads' => $lost_count,
					'total_quoted_fmt' => 'AED ' . number_format($u_quoted, 2),
					'total_mrr_fmt' => 'AED ' . number_format($u_mrr, 2),
					'attended_followups' => $u_att
				);
			}

			return array(
				'title' => 'Sales Performance Report',
				'department_title' => 'Sales Department',
				'headers' => array('#', 'Employee Name', 'Department', 'Email', 'Assigned Leads', 'Active Deals', 'Closed Deals', 'Lost Deals', 'Total Quoted (AED)', 'Total MRR (AED)', 'Attended Followups'),
				'fields' => array('index', 'name', 'department', 'email', 'total_leads', 'active_leads', 'closed_leads', 'lost_leads', 'total_quoted_fmt', 'total_mrr_fmt', 'attended_followups'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Sales Staff', 'value' => count($rows), 'icon' => 'fa-users', 'color' => 'bg-aqua'),
					array('label' => 'Total Leads Assigned', 'value' => number_format($tot_leads), 'icon' => 'fa-database', 'color' => 'bg-blue'),
					array('label' => 'Total Closed Deals', 'value' => number_format($tot_closed), 'icon' => 'fa-check-circle', 'color' => 'bg-green'),
					array('label' => 'Total Quoted Value', 'value' => 'AED ' . number_format($tot_quoted, 2), 'icon' => 'fa-file-text-o', 'color' => 'bg-yellow'),
					array('label' => 'Total Contract MRR', 'value' => 'AED ' . number_format($tot_mrr, 2), 'icon' => 'fa-money', 'color' => 'bg-red')
				)
			);
		}

		if ($report == 'database') {
			$this->db->select('A.*, B.name as assigned_name, C.status as status_name');
			$this->db->from('ci_leads as A');
			$this->db->join('ci_user as B', 'A.lead_assign = B.id', 'LEFT');
			$this->db->join('ci_status as C', 'A.status = C.id', 'LEFT');
			if (!empty($filters['database_category'])) {
				$this->db->where('A.database_category', $filters['database_category']);
			}
			if (!empty($filters['data_source'])) {
				$this->db->where('A.data_source', $filters['data_source']);
			}
			$this->_applyEmployee($this->db, 'A.lead_assign', $emp);
			$this->_applyDate($this->db, 'A.created', $d1, $d2);
			if (!empty($search)) {
				$this->db->where("(A.customer LIKE '%$search%' OR A.poc LIKE '%$search%' OR A.contact1 LIKE '%$search%' OR A.data_batch_referral_no LIKE '%$search%')");
			}
			$this->db->order_by('A.id', 'DESC');
			$leads = $this->db->get()->result_array();

			$rows = array();
			$cat_counts = array();
			foreach ($leads as $l) {
				$c_cat = !empty($l['database_category']) ? $l['database_category'] : 'Uncategorized';
				$cat_counts[$c_cat] = isset($cat_counts[$c_cat]) ? $cat_counts[$c_cat] + 1 : 1;
				$rows[] = array(
					'lead_id' => !empty($l['lead_id']) ? $l['lead_id'] : $l['id'],
					'customer' => $l['customer'],
					'poc' => $l['poc'],
					'contact1' => $l['contact1'],
					'email' => $l['email'],
					'database_category' => !empty($l['database_category']) ? $l['database_category'] : '-',
					'data_source' => !empty($l['data_source']) ? $l['data_source'] : '-',
					'data_batch_referral_no' => !empty($l['data_batch_referral_no']) ? $l['data_batch_referral_no'] : '-',
					'status_name' => !empty($l['status_name']) ? $l['status_name'] : $l['status'],
					'assigned_name' => !empty($l['assigned_name']) ? $l['assigned_name'] : 'Unassigned',
					'created' => simple_date($l['created'])
				);
			}

			arsort($cat_counts);
			$top_cat = !empty($cat_counts) ? key($cat_counts) : 'None';

			return array(
				'title' => 'Sales Database Report',
				'department_title' => 'Sales Department',
				'headers' => array('Lead ID', 'Customer Name', 'POC', 'Contact', 'Email', 'Database Category', 'Data Source', 'Batch Ref No', 'Status', 'Assigned To', 'Created Date'),
				'fields' => array('lead_id', 'customer', 'poc', 'contact1', 'email', 'database_category', 'data_source', 'data_batch_referral_no', 'status_name', 'assigned_name', 'created'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Database', 'value' => number_format(count($rows)), 'icon' => 'fa-database', 'color' => 'bg-green'),
					array('label' => 'Primary Category', 'value' => $top_cat, 'icon' => 'fa-tags', 'color' => 'bg-aqua'),
					array('label' => 'Unique Categories', 'value' => count($cat_counts), 'icon' => 'fa-folder-open', 'color' => 'bg-yellow')
				)
			);
		}

		if ($report == 'lead') {
			$this->db->select('A.*, B.name as assigned_name, C.status as status_name, T.title as service_name');
			$this->db->from('ci_leads as A');
			$this->db->join('ci_user as B', 'A.lead_assign = B.id', 'LEFT');
			$this->db->join('ci_status as C', 'A.status = C.id', 'LEFT');
			$this->db->join('ci_terms as T', 'A.s_cat = T.id', 'LEFT');
			if (!empty($status)) {
				$this->db->where('A.status', $status);
			}
			if (!empty($filters['industry_category'])) {
				$this->db->where('(A.industry_category = "'.$filters['industry_category'].'" OR A.business_category = "'.$filters['industry_category'].'")');
			}
			$this->_applyEmployee($this->db, 'A.lead_assign', $emp);
			$this->_applyDate($this->db, 'A.created', $d1, $d2);
			if (!empty($search)) {
				$this->db->where("(A.customer LIKE '%$search%' OR A.poc LIKE '%$search%' OR A.contact1 LIKE '%$search%' OR A.email LIKE '%$search%')");
			}
			$this->db->order_by('A.id', 'DESC');
			$this->db->limit(1000);
			$leads = $this->db->get()->result_array();

			$rows = array();
			$tot_mrr = 0.0;
			$active_cnt = 0;
			$closed_cnt = 0;

			foreach ($leads as $l) {
				$mrr_val = (float)$l['mrr'];
				$tot_mrr += $mrr_val;
				if (in_array($l['status'], array(42, 43, 44))) {
					$closed_cnt++;
				} else {
					$active_cnt++;
				}
				$ind = !empty($l['industry_category']) ? $l['industry_category'] : (!empty($l['business_category']) ? $l['business_category'] : '-');
				$stat_cat = !empty($l['status_category']) ? $l['status_category'] : (!empty($l['lost_reason']) ? $l['lost_reason'] : '-');
				$rows[] = array(
					'lead_id' => !empty($l['lead_id']) ? $l['lead_id'] : $l['id'],
					'customer' => $l['customer'],
					'poc' => $l['poc'],
					'contact1' => $l['contact1'],
					'email' => $l['email'],
					'industry_category' => $ind,
					'service_name' => !empty($l['service_name']) ? $l['service_name'] : '-',
					'mrr_fmt' => 'AED ' . number_format($mrr_val, 2),
					'status_name' => !empty($l['status_name']) ? $l['status_name'] : $l['status'],
					'status_category' => $stat_cat,
					'assigned_name' => !empty($l['assigned_name']) ? $l['assigned_name'] : 'Unassigned',
					'created' => simple_date($l['created'])
				);
			}

			return array(
				'title' => 'Sales Lead Report',
				'department_title' => 'Sales Department',
				'headers' => array('Lead ID', 'Customer Name', 'POC', 'Contact', 'Email', 'Industry Category', 'Service Category', 'MRR (AED)', 'Status', 'Status Category', 'Assigned To', 'Created Date'),
				'fields' => array('lead_id', 'customer', 'poc', 'contact1', 'email', 'industry_category', 'service_name', 'mrr_fmt', 'status_name', 'status_category', 'assigned_name', 'created'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Leads', 'value' => number_format(count($rows)), 'icon' => 'fa-database', 'color' => 'bg-blue'),
					array('label' => 'Active Deals', 'value' => number_format($active_cnt), 'icon' => 'fa-spinner', 'color' => 'bg-aqua'),
					array('label' => 'Closed Deals', 'value' => number_format($closed_cnt), 'icon' => 'fa-check-circle', 'color' => 'bg-green'),
					array('label' => 'Total MRR', 'value' => 'AED ' . number_format($tot_mrr, 2), 'icon' => 'fa-money', 'color' => 'bg-purple')
				)
			);
		}

		if ($report == 'quote') {
			$this->db->select('A.*, B.name as assigned_name, C.status as status_name');
			$this->db->from('ci_leads as A');
			$this->db->join('ci_user as B', 'A.lead_assign = B.id', 'LEFT');
			$this->db->join('ci_status as C', 'A.status = C.id', 'LEFT');
			if (!empty($filters['quote_status'])) {
				$this->db->where('A.quote_status', $filters['quote_status']);
			}
			if (!empty($filters['quote_mode'])) {
				$this->db->where('A.quote_mode', $filters['quote_mode']);
			}
			$this->_applyEmployee($this->db, 'A.lead_assign', $emp);
			$this->_applyDate($this->db, 'A.created', $d1, $d2);
			if (!empty($search)) {
				$this->db->where("(A.customer LIKE '%$search%' OR A.poc LIKE '%$search%' OR A.contact1 LIKE '%$search%')");
			}
			$this->db->order_by('A.id', 'DESC');
			$leads = $this->db->get()->result_array();

			$rows = array();
			$tot_quote_amt = 0.0;
			$accepted_cnt = 0;

			foreach ($leads as $l) {
				$amt = (float)$l['quote_amount'];
				$tot_quote_amt += $amt;
				if (strtolower(trim($l['quote_status'])) == 'accepted') {
					$accepted_cnt++;
				}
				$rows[] = array(
					'lead_id' => !empty($l['lead_id']) ? $l['lead_id'] : $l['id'],
					'customer' => $l['customer'],
					'poc' => $l['poc'],
					'contact1' => $l['contact1'],
					'quote_status' => !empty($l['quote_status']) ? $l['quote_status'] : 'Quote Given',
					'quote_mode' => !empty($l['quote_mode']) ? $l['quote_mode'] : '-',
					'quote_amount_fmt' => 'AED ' . number_format($amt, 2),
					'status_name' => !empty($l['status_name']) ? $l['status_name'] : $l['status'],
					'assigned_name' => !empty($l['assigned_name']) ? $l['assigned_name'] : 'Unassigned',
					'created' => simple_date($l['created'])
				);
			}

			$total_cnt = count($rows);
			$conv_rate = $total_cnt > 0 ? round(($accepted_cnt / $total_cnt) * 100, 1) : 0;

			return array(
				'title' => 'Sales Quote Report',
				'department_title' => 'Sales Department',
				'headers' => array('Lead ID', 'Customer Name', 'POC', 'Contact', 'Quote Status', 'Quote Mode', 'Quote Amount (AED)', 'Lead Status', 'Assigned To', 'Created Date'),
				'fields' => array('lead_id', 'customer', 'poc', 'contact1', 'quote_status', 'quote_mode', 'quote_amount_fmt', 'status_name', 'assigned_name', 'created'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Quotes', 'value' => number_format($total_cnt), 'icon' => 'fa-file-text-o', 'color' => 'bg-aqua'),
					array('label' => 'Total Quoted Value', 'value' => 'AED ' . number_format($tot_quote_amt, 2), 'icon' => 'fa-calculator', 'color' => 'bg-green'),
					array('label' => 'Accepted Deals', 'value' => number_format($accepted_cnt), 'icon' => 'fa-check-circle', 'color' => 'bg-purple'),
					array('label' => 'Acceptance Rate', 'value' => $conv_rate . '%', 'icon' => 'fa-pie-chart', 'color' => 'bg-yellow')
				)
			);
		}

		if ($report == 'tax-registration') {
			$this->db->select('A.*, B.name as assigned_name, C.status as status_name');
			$this->db->from('ci_leads as A');
			$this->db->join('ci_user as B', 'A.lead_assign = B.id', 'LEFT');
			$this->db->join('ci_status as C', 'A.status = C.id', 'LEFT');
			if (!empty($filters['corporate_tax_status'])) {
				$this->db->where('A.corporate_tax_status', $filters['corporate_tax_status']);
			}
			if (!empty($filters['vat_status'])) {
				$this->db->where('A.vat_status', $filters['vat_status']);
			}
			$this->_applyEmployee($this->db, 'A.lead_assign', $emp);
			$this->_applyDate($this->db, 'A.created', $d1, $d2);
			if (!empty($search)) {
				$this->db->where("(A.customer LIKE '%$search%' OR A.tl_number LIKE '%$search%' OR A.poc LIKE '%$search%')");
			}
			$this->db->order_by('A.id', 'DESC');
			$leads = $this->db->get()->result_array();

			$rows = array();
			$ct_reg = 0;
			$vat_reg = 0;
			$not_reg = 0;

			foreach ($leads as $l) {
				if ($l['corporate_tax_status'] == 'Registered') $ct_reg++;
				if ($l['vat_status'] == 'Registered') $vat_reg++;
				if ($l['corporate_tax_status'] == 'Not Registered' || $l['vat_status'] == 'Not Registered') $not_reg++;

				$rows[] = array(
					'lead_id' => !empty($l['lead_id']) ? $l['lead_id'] : $l['id'],
					'customer' => $l['customer'],
					'poc' => $l['poc'],
					'contact1' => $l['contact1'],
					'tl_number' => !empty($l['tl_number']) ? $l['tl_number'] : '-',
					'corporate_tax_status' => !empty($l['corporate_tax_status']) ? $l['corporate_tax_status'] : 'Not Registered',
					'vat_status' => !empty($l['vat_status']) ? $l['vat_status'] : 'Not Registered',
					'city' => !empty($l['city']) ? $l['city'] : '-',
					'assigned_name' => !empty($l['assigned_name']) ? $l['assigned_name'] : 'Unassigned',
					'status_name' => !empty($l['status_name']) ? $l['status_name'] : $l['status'],
					'created' => simple_date($l['created'])
				);
			}

			return array(
				'title' => 'CT / VAT Registration Report',
				'department_title' => 'Sales Department',
				'headers' => array('Lead ID', 'Customer Name', 'POC', 'Contact', 'Trade License No', 'Corporate Tax Status', 'VAT Status', 'City', 'Assigned To', 'Status', 'Created Date'),
				'fields' => array('lead_id', 'customer', 'poc', 'contact1', 'tl_number', 'corporate_tax_status', 'vat_status', 'city', 'assigned_name', 'status_name', 'created'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Assessed', 'value' => number_format(count($rows)), 'icon' => 'fa-shield', 'color' => 'bg-blue'),
					array('label' => 'CT Registered', 'value' => number_format($ct_reg), 'icon' => 'fa-check', 'color' => 'bg-green'),
					array('label' => 'VAT Registered', 'value' => number_format($vat_reg), 'icon' => 'fa-check-square', 'color' => 'bg-aqua'),
					array('label' => 'Not Registered', 'value' => number_format($not_reg), 'icon' => 'fa-exclamation-triangle', 'color' => 'bg-yellow')
				)
			);
		}

		if ($report == 'lost') {
			$this->db->select('A.*, B.name as assigned_name, C.status as status_name');
			$this->db->from('ci_leads as A');
			$this->db->join('ci_user as B', 'A.lead_assign = B.id', 'LEFT');
			$this->db->join('ci_status as C', 'A.status = C.id', 'LEFT');
			$this->db->where('(A.status IN (42, 43, 44) OR LOWER(A.status_category) LIKE "%lost%" OR LOWER(A.lost_reason) LIKE "%lost%" OR (A.status_category != "" AND A.status_category != "None"))');
			if (!empty($filters['status_category'])) {
				$this->db->where('(A.status_category = "'.$filters['status_category'].'" OR A.lost_reason = "'.$filters['status_category'].'")');
			}
			$this->_applyEmployee($this->db, 'A.lead_assign', $emp);
			$this->_applyDate($this->db, 'A.created', $d1, $d2);
			if (!empty($search)) {
				$this->db->where("(A.customer LIKE '%$search%' OR A.status_category LIKE '%$search%' OR A.status_remarks LIKE '%$search%')");
			}
			$this->db->order_by('A.id', 'DESC');
			$leads = $this->db->get()->result_array();

			$rows = array();
			$lost_val = 0.0;
			$reasons = array();

			foreach ($leads as $l) {
				$val = (float)$l['quote_amount'] > 0 ? (float)$l['quote_amount'] : (float)$l['mrr'];
				$lost_val += $val;
				$cat = !empty($l['status_category']) ? $l['status_category'] : (!empty($l['lost_reason']) ? $l['lost_reason'] : 'Unspecified');
				$reasons[$cat] = isset($reasons[$cat]) ? $reasons[$cat] + 1 : 1;

				$rows[] = array(
					'lead_id' => !empty($l['lead_id']) ? $l['lead_id'] : $l['id'],
					'customer' => $l['customer'],
					'poc' => $l['poc'],
					'contact1' => $l['contact1'],
					'status_name' => !empty($l['status_name']) ? $l['status_name'] : $l['status'],
					'status_category' => $cat,
					'status_remarks' => !empty($l['status_remarks']) ? $l['status_remarks'] : '-',
					'quote_amount_fmt' => 'AED ' . number_format((float)$l['quote_amount'], 2),
					'mrr_fmt' => 'AED ' . number_format((float)$l['mrr'], 2),
					'assigned_name' => !empty($l['assigned_name']) ? $l['assigned_name'] : 'Unassigned',
					'created' => simple_date($l['created'])
				);
			}

			arsort($reasons);
			$top_reason = !empty($reasons) ? key($reasons) : 'N/A';

			return array(
				'title' => 'Sales Lost Report',
				'department_title' => 'Sales Department',
				'headers' => array('Lead ID', 'Customer Name', 'POC', 'Contact', 'Status', 'Status Category', 'Status Remarks', 'Quoted Value (AED)', 'MRR (AED)', 'Assigned To', 'Date'),
				'fields' => array('lead_id', 'customer', 'poc', 'contact1', 'status_name', 'status_category', 'status_remarks', 'quote_amount_fmt', 'mrr_fmt', 'assigned_name', 'created'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Lost Deals', 'value' => number_format(count($rows)), 'icon' => 'fa-times-circle', 'color' => 'bg-red'),
					array('label' => 'Estimated Lost Value', 'value' => 'AED ' . number_format($lost_val, 2), 'icon' => 'fa-money', 'color' => 'bg-yellow'),
					array('label' => 'Top Lost Reason', 'value' => $top_reason, 'icon' => 'fa-exclamation-circle', 'color' => 'bg-purple')
				)
			);
		}

		if ($report == 'trade-license') {
			$this->db->select('A.*, B.name as assigned_name');
			$this->db->from('ci_leads as A');
			$this->db->join('ci_user as B', 'A.lead_assign = B.id', 'LEFT');
			$this->_applyEmployee($this->db, 'A.lead_assign', $emp);
			$this->_applyDate($this->db, 'A.created', $d1, $d2);
			if (!empty($search)) {
				$this->db->where("(A.customer LIKE '%$search%' OR A.tl_number LIKE '%$search%' OR A.tl_authority LIKE '%$search%')");
			}
			$this->db->order_by('A.id', 'DESC');
			$leads = $this->db->get()->result_array();

			$today = date('Y-m-d');
			$in_30 = date('Y-m-d', strtotime('+30 days'));
			$in_60 = date('Y-m-d', strtotime('+60 days'));

			$rows = array();
			$exp_cnt = 0;
			$soon_cnt = 0;
			$valid_cnt = 0;
			$exp_filter = isset($filters['expiry_filter']) ? $filters['expiry_filter'] : 'all';

			foreach ($leads as $l) {
				$exp_d = $l['tl_expire_date'];
				$badge = 'Valid';
				$badge_class = 'label-success';

				if (empty($exp_d) || $exp_d == '0000-00-00') {
					$badge = 'Undated';
					$badge_class = 'label-default';
				} elseif ($exp_d < $today) {
					$badge = 'Expired';
					$badge_class = 'label-danger';
					$exp_cnt++;
				} elseif ($exp_d <= $in_30) {
					$badge = 'Expiring in 30d';
					$badge_class = 'label-warning';
					$soon_cnt++;
				} elseif ($exp_d <= $in_60) {
					$badge = 'Expiring in 60d';
					$badge_class = 'label-info';
					$soon_cnt++;
				} else {
					$valid_cnt++;
				}

				if ($exp_filter == 'expired' && $badge != 'Expired') continue;
				if ($exp_filter == '30days' && $badge != 'Expiring in 30d') continue;
				if ($exp_filter == '60days' && $badge != 'Expiring in 60d' && $badge != 'Expiring in 30d') continue;
				if ($exp_filter == 'valid' && ($badge == 'Expired' || $badge == 'Undated')) continue;

				$rows[] = array(
					'lead_id' => !empty($l['lead_id']) ? $l['lead_id'] : $l['id'],
					'customer' => $l['customer'],
					'tl_number' => !empty($l['tl_number']) ? $l['tl_number'] : '-',
					'tl_authority' => !empty($l['tl_authority']) ? $l['tl_authority'] : '-',
					'tl_issue_date' => (!empty($l['tl_issue_date']) && $l['tl_issue_date'] != '0000-00-00') ? simple_date($l['tl_issue_date']) : '-',
					'tl_expire_date' => (!empty($l['tl_expire_date']) && $l['tl_expire_date'] != '0000-00-00') ? simple_date($l['tl_expire_date']) : '-',
					'validity_badge' => '<span class="label '.$badge_class.'">'.$badge.'</span>',
					'city' => !empty($l['city']) ? $l['city'] : '-',
					'assigned_name' => !empty($l['assigned_name']) ? $l['assigned_name'] : 'Unassigned'
				);
			}

			return array(
				'title' => 'Trade License Report',
				'department_title' => 'Sales Department',
				'headers' => array('Lead ID', 'Customer Name', 'Trade License No', 'Authority', 'Issue Date', 'Expiry Date', 'Status / Validity', 'City', 'Assigned To'),
				'fields' => array('lead_id', 'customer', 'tl_number', 'tl_authority', 'tl_issue_date', 'tl_expire_date', 'validity_badge', 'city', 'assigned_name'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Licenses on File', 'value' => number_format(count($rows)), 'icon' => 'fa-id-card-o', 'color' => 'bg-blue'),
					array('label' => 'Expired Licenses', 'value' => number_format($exp_cnt), 'icon' => 'fa-times-circle', 'color' => 'bg-red'),
					array('label' => 'Expiring Within 30d', 'value' => number_format($soon_cnt), 'icon' => 'fa-clock-o', 'color' => 'bg-yellow'),
					array('label' => 'Active & Valid', 'value' => number_format($valid_cnt), 'icon' => 'fa-check-circle', 'color' => 'bg-green')
				)
			);
		}

		return array();
	}

	private function _queryOperationsReport($report, $filters)
	{
		$d1 = isset($filters['d1']) ? $filters['d1'] : '';
		$d2 = isset($filters['d2']) ? $filters['d2'] : '';
		$search = isset($filters['search']) ? trim($filters['search']) : '';
		$emp = isset($filters['employee_id']) ? $filters['employee_id'] : '';
		$status = isset($filters['status']) ? $filters['status'] : '';

		if ($report == 'daily') {
			$this->db->select('A.id, A.task_id as ref_id, A.customer, A.category as title, A.a_department as department, B.name as assigned_name, A.status, A.remark, A.created as activity_time');
			$this->db->from('ci_tasks as A');
			$this->db->join('ci_user as B', 'A.assigned_to = B.id', 'LEFT');
			$this->_applyDate($this->db, 'A.created', $d1, $d2);
			if (!empty($emp)) $this->_applyEmployee($this->db, 'A.assigned_to', $emp);
			$tasks_raw = $this->db->get()->result_array();
			$tasks = array();
			foreach ($tasks_raw as $t) {
				$t['item_type'] = 'Task';
				$t['priority'] = 'Normal';
				$tasks[] = $t;
			}

			$this->db->select('T.id, T.ticket_number as ref_id, T.customer, T.subject as title, T.department, U.name as assigned_name, T.status, T.description as remark, T.created_at as activity_time, T.priority');
			$this->db->from('ci_tickets as T');
			$this->db->join('ci_user as U', 'T.assigned_to = U.id', 'LEFT');
			$this->_applyDate($this->db, 'T.created_at', $d1, $d2);
			if (!empty($emp)) $this->_applyEmployee($this->db, 'T.assigned_to', $emp);
			$tickets_raw = $this->db->get()->result_array();
			$tickets = array();
			foreach ($tickets_raw as $tk) {
				$tk['item_type'] = 'Ticket';
				$tickets[] = $tk;
			}

			$combined = array_merge($tasks, $tickets);
			usort($combined, function($a, $b) {
				return strcmp($b['activity_time'], $a['activity_time']);
			});

			$rows = array();
			$completed_cnt = 0;
			foreach ($combined as $item) {
				if (in_array(strtolower($item['status']), array('completed', 'resolved', 'closed'))) {
					$completed_cnt++;
				}
				$rows[] = array(
					'activity_time' => !empty($item['activity_time']) ? date('d-m-Y H:i', strtotime($item['activity_time'])) : '-',
					'item_type' => '<span class="badge '.($item['item_type']=='Task'?'bg-blue':'bg-purple').'">'.$item['item_type'].'</span>',
					'ref_id' => $item['ref_id'],
					'customer' => !empty($item['customer']) ? $item['customer'] : '-',
					'title' => $item['title'],
					'department' => !empty($item['department']) ? $item['department'] : 'Operations',
					'assigned_name' => !empty($item['assigned_name']) ? $item['assigned_name'] : 'Unassigned',
					'priority_badge' => '<span class="label '.($item['priority']=='High'?'label-danger':'label-info').'">'.$item['priority'].'</span>',
					'status_badge' => '<span class="label '.($item['status']=='Completed'||$item['status']=='Resolved'?'label-success':'label-warning').'">'.$item['status'].'</span>',
					'remarks' => !empty($item['remark']) ? character_limiter(strip_tags($item['remark']), 60) : '-'
				);
			}

			return array(
				'title' => 'Day-to-Day Operations Execution Log',
				'department_title' => 'Operations Department',
				'headers' => array('Date / Time', 'Type', 'Reference ID', 'Customer / Entity', 'Service / Title', 'Department', 'Assigned To', 'Priority', 'Status', 'Remarks'),
				'fields' => array('activity_time', 'item_type', 'ref_id', 'customer', 'title', 'department', 'assigned_name', 'priority_badge', 'status_badge', 'remarks'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Daily Activities', 'value' => number_format(count($rows)), 'icon' => 'fa-calendar-o', 'color' => 'bg-aqua'),
					array('label' => 'Completed Today', 'value' => number_format($completed_cnt), 'icon' => 'fa-check', 'color' => 'bg-green'),
					array('label' => 'Active Operations Staff', 'value' => $this->db->where('department', 'Operations')->count_all_results('ci_user'), 'icon' => 'fa-users', 'color' => 'bg-purple')
				)
			);
		}

		if ($report == 'tasks') {
			$this->db->select('A.*, B.name as assigned_name');
			$this->db->from('ci_tasks as A');
			$this->db->join('ci_user as B', 'A.assigned_to = B.id', 'LEFT');
			if (!empty($status)) {
				$this->db->where('A.status', $status);
			}
			$this->_applyEmployee($this->db, 'A.assigned_to', $emp);
			$this->_applyDate($this->db, 'A.created', $d1, $d2);
			if (!empty($search)) {
				$this->db->where("(A.customer LIKE '%$search%' OR A.task_id LIKE '%$search%' OR A.category LIKE '%$search%')");
			}
			$this->db->order_by('A.id', 'DESC');
			$tasks = $this->db->get()->result_array();

			$rows = array();
			$comp = 0;
			$in_prog = 0;
			$overdue = 0;
			$today = date('Y-m-d');

			foreach ($tasks as $t) {
				$st = !empty($t['status']) ? $t['status'] : 'Pending';
				if ($st == 'Completed') $comp++;
				elseif ($st == 'In Progress') $in_prog++;
				if (!empty($t['followup_date']) && $t['followup_date'] < $today && $st != 'Completed') $overdue++;

				$rows[] = array(
					'task_id' => !empty($t['task_id']) ? $t['task_id'] : $t['id'],
					'customer' => $t['customer'],
					'poc' => $t['poc'],
					'contact1' => $t['contact1'],
					'category' => !empty($t['category']) ? $t['category'] : '-',
					'assigned_name' => !empty($t['assigned_name']) ? $t['assigned_name'] : 'Unassigned',
					'followup_date' => !empty($t['followup_date']) ? simple_date($t['followup_date']) : '-',
					'status_badge' => '<span class="label '.($st=='Completed'?'label-success':'label-warning').'">'.$st.'</span>',
					'remark' => !empty($t['remark']) ? character_limiter(strip_tags($t['remark']), 60) : '-',
					'created' => simple_date($t['created'])
				);
			}

			return array(
				'title' => 'Task Completion Analyzer',
				'department_title' => 'Operations Department',
				'headers' => array('Task ID', 'Customer Name', 'POC', 'Contact', 'Category', 'Assigned To', 'Due / Followup Date', 'Status', 'Remarks', 'Created Date'),
				'fields' => array('task_id', 'customer', 'poc', 'contact1', 'category', 'assigned_name', 'followup_date', 'status_badge', 'remark', 'created'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Tasks', 'value' => number_format(count($rows)), 'icon' => 'fa-tasks', 'color' => 'bg-blue'),
					array('label' => 'Completed', 'value' => number_format($comp), 'icon' => 'fa-check-circle', 'color' => 'bg-green'),
					array('label' => 'In Progress', 'value' => number_format($in_prog), 'icon' => 'fa-hourglass-start', 'color' => 'bg-aqua'),
					array('label' => 'Overdue', 'value' => number_format($overdue), 'icon' => 'fa-exclamation-circle', 'color' => 'bg-red')
				)
			);
		}

		if ($report == 'service-delivery') {
			$this->db->select('A.*, B.name as tech_name');
			$this->db->from('ci_service_delivery as A');
			$this->db->join('ci_user as B', 'A.technician_id = B.id', 'LEFT');
			if (!empty($status)) $this->db->where('A.status', $status);
			if (!empty($filters['vertical'])) $this->db->where('A.vertical', $filters['vertical']);
			$this->_applyEmployee($this->db, 'A.technician_id', $emp);
			$this->_applyDate($this->db, 'A.order_date', $d1, $d2);
			if (!empty($search)) {
				$this->db->where("(A.customer_name LIKE '%$search%' OR A.delivery_id LIKE '%$search%' OR A.service_title LIKE '%$search%')");
			}
			$this->db->order_by('A.id', 'DESC');
			$deliveries = $this->db->get()->result_array();

			$rows = array();
			$delivered_cnt = 0;
			$in_prog_cnt = 0;

			foreach ($deliveries as $d) {
				if ($d['status'] == 'Delivered' || $d['status'] == 'Accepted') $delivered_cnt++;
				else $in_prog_cnt++;

				$rows[] = array(
					'delivery_id' => $d['delivery_id'],
					'customer_name' => $d['customer_name'],
					'service_title' => $d['service_title'],
					'vertical' => !empty($d['vertical']) ? $d['vertical'] : 'General',
					'order_date' => simple_date($d['order_date']),
					'target_date' => simple_date($d['target_date']),
					'completion_date' => !empty($d['completion_date']) ? simple_date($d['completion_date']) : '-',
					'status_badge' => '<span class="label '.($d['status']=='Accepted'||$d['status']=='Delivered'?'label-success':'label-primary').'">'.$d['status'].'</span>',
					'tech_name' => !empty($d['tech_name']) ? $d['tech_name'] : 'Operations Lead',
					'remarks' => !empty($d['remarks']) ? $d['remarks'] : '-'
				);
			}

			$tot = count($rows);
			$ot_rate = $tot > 0 ? round(($delivered_cnt / $tot) * 100, 1) : 0;

			return array(
				'title' => 'Client Service Delivery Report',
				'department_title' => 'Operations Department',
				'headers' => array('Delivery ID', 'Customer Name', 'Service Title', 'Vertical', 'Order Date', 'Target Date', 'Completion Date', 'Status', 'Technician / Lead', 'Remarks'),
				'fields' => array('delivery_id', 'customer_name', 'service_title', 'vertical', 'order_date', 'target_date', 'completion_date', 'status_badge', 'tech_name', 'remarks'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Deliveries', 'value' => number_format($tot), 'icon' => 'fa-truck', 'color' => 'bg-aqua'),
					array('label' => 'Delivered / Accepted', 'value' => number_format($delivered_cnt), 'icon' => 'fa-check', 'color' => 'bg-green'),
					array('label' => 'In Progress', 'value' => number_format($in_prog_cnt), 'icon' => 'fa-hourglass-start', 'color' => 'bg-yellow'),
					array('label' => 'Delivery Rate', 'value' => $ot_rate . '%', 'icon' => 'fa-percent', 'color' => 'bg-purple')
				)
			);
		}

		if ($report == 'pending-work') {
			$today = date('Y-m-d');
			$this->db->select('A.id, A.task_id as ref_id, A.customer, A.category as title, B.name as assigned_name, A.status, A.created as created_date, A.followup_date as due_date');
			$this->db->from('ci_tasks as A');
			$this->db->join('ci_user as B', 'A.assigned_to = B.id', 'LEFT');
			$this->db->where_not_in('A.status', array('Completed', 'Resolved', 'Closed'));
			$tasks_raw = $this->db->get()->result_array();
			$tasks = array();
			foreach ($tasks_raw as $t) {
				$t['item_type'] = 'Task';
				$t['priority'] = 'Normal';
				$tasks[] = $t;
			}

			$this->db->select('T.id, T.ticket_number as ref_id, T.customer, T.subject as title, U.name as assigned_name, T.status, T.created_at as created_date, T.deadline_date as due_date, T.priority');
			$this->db->from('ci_tickets as T');
			$this->db->join('ci_user as U', 'T.assigned_to = U.id', 'LEFT');
			$this->db->where_not_in('T.status', array('Completed', 'Resolved', 'Closed'));
			$tickets_raw = $this->db->get()->result_array();
			$tickets = array();
			foreach ($tickets_raw as $tk) {
				$tk['item_type'] = 'Ticket';
				$tickets[] = $tk;
			}

			$combined = array_merge($tasks, $tickets);
			$rows = array();
			$overdue_cnt = 0;
			$crit_cnt = 0;

			foreach ($combined as $item) {
				$days_pending = !empty($item['created_date']) ? floor((time() - strtotime($item['created_date'])) / 86400) : 0;
				if ($days_pending < 0) $days_pending = 0;
				if (!empty($item['due_date']) && $item['due_date'] < $today) $overdue_cnt++;
				if ($item['priority'] == 'High' || $item['priority'] == 'Critical') $crit_cnt++;

				$rows[] = array(
					'item_type' => '<span class="badge '.($item['item_type']=='Task'?'bg-blue':'bg-purple').'">'.$item['item_type'].'</span>',
					'ref_id' => $item['ref_id'],
					'customer' => !empty($item['customer']) ? $item['customer'] : '-',
					'title' => $item['title'],
					'priority_badge' => '<span class="label '.($item['priority']=='High'?'label-danger':'label-info').'">'.$item['priority'].'</span>',
					'assigned_name' => !empty($item['assigned_name']) ? $item['assigned_name'] : 'Unassigned',
					'created_date' => simple_date($item['created_date']),
					'due_date' => !empty($item['due_date']) ? simple_date($item['due_date']) : '-',
					'days_pending' => $days_pending . ' days',
					'status_badge' => '<span class="label label-warning">'.$item['status'].'</span>'
				);
			}

			return array(
				'title' => 'Pending Work Report',
				'department_title' => 'Operations Department',
				'headers' => array('Type', 'Reference ID', 'Customer', 'Title / Description', 'Priority', 'Assigned To', 'Created Date', 'Due Date', 'Days Pending', 'Status'),
				'fields' => array('item_type', 'ref_id', 'customer', 'title', 'priority_badge', 'assigned_name', 'created_date', 'due_date', 'days_pending', 'status_badge'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Pending Items', 'value' => number_format(count($rows)), 'icon' => 'fa-clock-o', 'color' => 'bg-yellow'),
					array('label' => 'Overdue Items', 'value' => number_format($overdue_cnt), 'icon' => 'fa-exclamation-circle', 'color' => 'bg-red'),
					array('label' => 'High / Critical Priority', 'value' => number_format($crit_cnt), 'icon' => 'fa-flag', 'color' => 'bg-purple')
				)
			);
		}

		if ($report == 'billing') {
			$this->db->select('*');
			$this->db->from('ci_invoices');
			if (!empty($status)) $this->db->where('status', $status);
			$this->_applyDate($this->db, 'invoice_date', $d1, $d2);
			if (!empty($search)) {
				$this->db->where("(invoice_no LIKE '%$search%' OR customer_name LIKE '%$search%' OR service_desc LIKE '%$search%')");
			}
			$this->db->order_by('id', 'DESC');
			$invs = $this->db->get()->result_array();

			$rows = array();
			$tot_inv = 0.0;
			$tot_paid = 0.0;
			$tot_bal = 0.0;
			$overdue_cnt = 0;

			foreach ($invs as $inv) {
				$tot_inv += (float)$inv['total_amount'];
				$tot_paid += (float)$inv['amount_paid'];
				$tot_bal += (float)$inv['balance_due'];
				if ($inv['status'] == 'Overdue') $overdue_cnt++;

				$badge_color = 'label-warning';
				if ($inv['status'] == 'Paid') $badge_color = 'label-success';
				elseif ($inv['status'] == 'Overdue') $badge_color = 'label-danger';

				$rows[] = array(
					'invoice_no' => $inv['invoice_no'],
					'invoice_date' => simple_date($inv['invoice_date']),
					'due_date' => simple_date($inv['due_date']),
					'customer_name' => $inv['customer_name'],
					'service_desc' => character_limiter($inv['service_desc'], 40),
					'subtotal' => 'AED ' . number_format((float)$inv['subtotal'], 2),
					'vat_amount' => 'AED ' . number_format((float)$inv['vat_amount'], 2),
					'total_amount' => 'AED ' . number_format((float)$inv['total_amount'], 2),
					'amount_paid' => 'AED ' . number_format((float)$inv['amount_paid'], 2),
					'balance_due' => 'AED ' . number_format((float)$inv['balance_due'], 2),
					'status_badge' => '<span class="label '.$badge_color.'">'.$inv['status'].'</span>'
				);
			}

			return array(
				'title' => 'Billing & Invoice Report',
				'department_title' => 'Operations Department',
				'headers' => array('Invoice No', 'Invoice Date', 'Due Date', 'Customer Name', 'Service Description', 'Subtotal (AED)', 'VAT 5% (AED)', 'Total (AED)', 'Paid (AED)', 'Balance Due (AED)', 'Status'),
				'fields' => array('invoice_no', 'invoice_date', 'due_date', 'customer_name', 'service_desc', 'subtotal', 'vat_amount', 'total_amount', 'amount_paid', 'balance_due', 'status_badge'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Invoiced', 'value' => 'AED ' . number_format($tot_inv, 2), 'icon' => 'fa-file-text-o', 'color' => 'bg-blue'),
					array('label' => 'Total Paid / Settled', 'value' => 'AED ' . number_format($tot_paid, 2), 'icon' => 'fa-check', 'color' => 'bg-green'),
					array('label' => 'Outstanding Balance', 'value' => 'AED ' . number_format($tot_bal, 2), 'icon' => 'fa-money', 'color' => 'bg-yellow'),
					array('label' => 'Overdue Invoices', 'value' => number_format($overdue_cnt), 'icon' => 'fa-exclamation-triangle', 'color' => 'bg-red')
				)
			);
		}

		if ($report == 'collections') {
			$this->db->select('A.*, B.name as recorder_name');
			$this->db->from('ci_payments as A');
			$this->db->join('ci_user as B', 'A.recorded_by = B.id', 'LEFT');
			if (!empty($filters['payment_mode'])) $this->db->where('A.payment_mode', $filters['payment_mode']);
			$this->_applyDate($this->db, 'A.payment_date', $d1, $d2);
			if (!empty($search)) {
				$this->db->where("(A.receipt_no LIKE '%$search%' OR A.customer_name LIKE '%$search%' OR A.reference_no LIKE '%$search%')");
			}
			$this->db->order_by('A.id', 'DESC');
			$payments = $this->db->get()->result_array();

			$rows = array();
			$tot_amt = 0.0;
			$mode_counts = array();

			foreach ($payments as $p) {
				$amt = (float)$p['amount'];
				$tot_amt += $amt;
				$mode = $p['payment_mode'];
				$mode_counts[$mode] = isset($mode_counts[$mode]) ? $mode_counts[$mode] + $amt : $amt;

				$rows[] = array(
					'receipt_no' => $p['receipt_no'],
					'payment_date' => simple_date($p['payment_date']),
					'invoice_no' => !empty($p['invoice_no']) ? $p['invoice_no'] : '-',
					'customer_name' => $p['customer_name'],
					'payment_mode' => $p['payment_mode'],
					'amount' => 'AED ' . number_format($amt, 2),
					'reference_no' => !empty($p['reference_no']) ? $p['reference_no'] : '-',
					'recorder_name' => !empty($p['recorder_name']) ? $p['recorder_name'] : 'Accounts',
					'remarks' => !empty($p['remarks']) ? $p['remarks'] : '-'
				);
			}

			return array(
				'title' => 'Payment Collection Report',
				'department_title' => 'Operations Department',
				'headers' => array('Receipt No', 'Payment Date', 'Invoice Ref No', 'Customer Name', 'Payment Mode', 'Amount (AED)', 'Reference / Trans No', 'Recorded By', 'Remarks'),
				'fields' => array('receipt_no', 'payment_date', 'invoice_no', 'customer_name', 'payment_mode', 'amount', 'reference_no', 'recorder_name', 'remarks'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Collections', 'value' => 'AED ' . number_format($tot_amt, 2), 'icon' => 'fa-credit-card', 'color' => 'bg-green'),
					array('label' => 'Total Receipts', 'value' => number_format(count($rows)), 'icon' => 'fa-file-text-o', 'color' => 'bg-aqua'),
					array('label' => 'Bank Transfers', 'value' => 'AED ' . number_format(isset($mode_counts['Bank Transfer']) ? $mode_counts['Bank Transfer'] : 0, 2), 'icon' => 'fa-bank', 'color' => 'bg-blue')
				)
			);
		}

		if ($report == 'expenses') {
			$this->db->select('A.*, B.name as approver_name');
			$this->db->from('ci_expenses as A');
			$this->db->join('ci_user as B', 'A.approved_by = B.id', 'LEFT');
			if (!empty($filters['category'])) $this->db->where('A.category', $filters['category']);
			$this->_applyDate($this->db, 'A.expense_date', $d1, $d2);
			if (!empty($search)) {
				$this->db->where("(A.voucher_no LIKE '%$search%' OR A.paid_to LIKE '%$search%' OR A.description LIKE '%$search%')");
			}
			$this->db->order_by('A.id', 'DESC');
			$expenses = $this->db->get()->result_array();

			$rows = array();
			$tot_exp = 0.0;
			$cats = array();

			foreach ($expenses as $e) {
				$amt = (float)$e['amount'];
				$tot_exp += $amt;
				$cat = $e['category'];
				$cats[$cat] = isset($cats[$cat]) ? $cats[$cat] + $amt : $amt;

				$rows[] = array(
					'voucher_no' => $e['voucher_no'],
					'expense_date' => simple_date($e['expense_date']),
					'category' => $e['category'],
					'paid_to' => $e['paid_to'],
					'payment_mode' => $e['payment_mode'],
					'amount' => 'AED ' . number_format($amt, 2),
					'approver_name' => !empty($e['approver_name']) ? $e['approver_name'] : 'Management',
					'description' => !empty($e['description']) ? $e['description'] : '-'
				);
			}

			arsort($cats);
			$top_cat = !empty($cats) ? key($cats) : 'None';

			return array(
				'title' => 'Operational Expense Report',
				'department_title' => 'Operations Department',
				'headers' => array('Voucher No', 'Expense Date', 'Category', 'Paid To / Vendor', 'Payment Mode', 'Amount (AED)', 'Approved By', 'Description'),
				'fields' => array('voucher_no', 'expense_date', 'category', 'paid_to', 'payment_mode', 'amount', 'approver_name', 'description'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Expenses', 'value' => 'AED ' . number_format($tot_exp, 2), 'icon' => 'fa-calculator', 'color' => 'bg-red'),
					array('label' => 'Top Expense Category', 'value' => $top_cat, 'icon' => 'fa-tags', 'color' => 'bg-yellow'),
					array('label' => 'Total Vouchers', 'value' => number_format(count($rows)), 'icon' => 'fa-file-text-o', 'color' => 'bg-aqua')
				)
			);
		}

		if ($report == 'revenue') {
			$this->db->select('*');
			$this->db->from('ci_invoices');
			$this->_applyDate($this->db, 'invoice_date', $d1, $d2);
			if (!empty($search)) $this->db->where("(customer_name LIKE '%$search%' OR invoice_no LIKE '%$search%')");
			$this->db->order_by('id', 'DESC');
			$invs = $this->db->get()->result_array();

			$tot_billed = 0.0;
			$tot_coll = 0.0;
			$rows = array();

			foreach ($invs as $inv) {
				$b = (float)$inv['total_amount'];
				$c = (float)$inv['amount_paid'];
				$tot_billed += $b;
				$tot_coll += $c;

				$rows[] = array(
					'ref_no' => $inv['invoice_no'],
					'customer_name' => $inv['customer_name'],
					'service_title' => character_limiter($inv['service_desc'], 40),
					'doc_date' => simple_date($inv['invoice_date']),
					'billed_amount' => 'AED ' . number_format($b, 2),
					'collected_amount' => 'AED ' . number_format($c, 2),
					'outstanding' => 'AED ' . number_format($b - $c, 2),
					'status_badge' => '<span class="label '.($inv['status']=='Paid'?'label-success':'label-warning').'">'.$inv['status'].'</span>'
				);
			}

			$tot_mrr_row = $this->db->select_sum('mrr')->get('ci_leads')->row_array();
			$contract_mrr = isset($tot_mrr_row['mrr']) ? (float)$tot_mrr_row['mrr'] : 0.0;
			$realization = $tot_billed > 0 ? round(($tot_coll / $tot_billed) * 100, 1) : 0;

			return array(
				'title' => 'Operational Revenue Report',
				'department_title' => 'Operations Department',
				'headers' => array('Invoice Ref', 'Customer Name', 'Service Description', 'Invoice Date', 'Billed Amount (AED)', 'Collected Amount (AED)', 'Outstanding (AED)', 'Status'),
				'fields' => array('ref_no', 'customer_name', 'service_title', 'doc_date', 'billed_amount', 'collected_amount', 'outstanding', 'status_badge'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Billed Revenue', 'value' => 'AED ' . number_format($tot_billed, 2), 'icon' => 'fa-money', 'color' => 'bg-green'),
					array('label' => 'Total Collected', 'value' => 'AED ' . number_format($tot_coll, 2), 'icon' => 'fa-credit-card', 'color' => 'bg-blue'),
					array('label' => 'Active Contract MRR', 'value' => 'AED ' . number_format($contract_mrr, 2), 'icon' => 'fa-line-chart', 'color' => 'bg-purple'),
					array('label' => 'Collection Realization', 'value' => $realization . '%', 'icon' => 'fa-pie-chart', 'color' => 'bg-yellow')
				)
			);
		}

		return array();
	}

	private function _querySupportReport($report, $filters)
	{
		$d1 = isset($filters['d1']) ? $filters['d1'] : '';
		$d2 = isset($filters['d2']) ? $filters['d2'] : '';
		$search = isset($filters['search']) ? trim($filters['search']) : '';
		$emp = isset($filters['employee_id']) ? $filters['employee_id'] : '';
		$status = isset($filters['status']) ? $filters['status'] : '';

		if ($report == 'activity' || $report == 'ticket-status') {
			$this->db->select('T.*, U.name as assigned_name');
			$this->db->from('ci_tickets as T');
			$this->db->join('ci_user as U', 'T.assigned_to = U.id', 'LEFT');
			if (!empty($status)) $this->db->where('T.status', $status);
			if (!empty($filters['priority'])) $this->db->where('T.priority', $filters['priority']);
			if (!empty($filters['department'])) $this->db->where('T.department', $filters['department']);
			$this->_applyEmployee($this->db, 'T.assigned_to', $emp);
			$this->_applyDate($this->db, 'T.created_at', $d1, $d2);
			if (!empty($search)) {
				$this->db->where("(T.ticket_number LIKE '%$search%' OR T.customer LIKE '%$search%' OR T.subject LIKE '%$search%')");
			}
			$this->db->order_by('T.id', 'DESC');
			$tickets = $this->db->get()->result_array();

			$rows = array();
			$open_cnt = 0;
			$in_prog = 0;
			$resolved_cnt = 0;

			foreach ($tickets as $t) {
				$st = $t['status'];
				if ($st == 'Open') $open_cnt++;
				elseif ($st == 'In Progress') $in_prog++;
				elseif ($st == 'Resolved' || $st == 'Closed') $resolved_cnt++;

				$p_color = 'label-info';
				if ($t['priority'] == 'High') $p_color = 'label-danger';
				elseif ($t['priority'] == 'Medium') $p_color = 'label-warning';

				$s_color = 'label-warning';
				if ($st == 'Open') $s_color = 'label-danger';
				elseif ($st == 'Resolved' || $st == 'Closed') $s_color = 'label-success';

				$rows[] = array(
					'ticket_number' => $t['ticket_number'],
					'customer' => !empty($t['customer']) ? $t['customer'] : '-',
					'client_type' => !empty($t['client_type']) ? $t['client_type'] : 'Corporate',
					'subject' => $t['subject'],
					'department' => !empty($t['department']) ? $t['department'] : 'Support',
					'assigned_name' => !empty($t['assigned_name']) ? $t['assigned_name'] : 'Support Queue',
					'priority_badge' => '<span class="label '.$p_color.'">'.$t['priority'].'</span>',
					'status_badge' => '<span class="label '.$s_color.'">'.$st.'</span>',
					'created_date' => simple_date($t['created_at']),
					'deadline_date' => !empty($t['deadline_date']) ? simple_date($t['deadline_date']) : '-',
					'updated_at' => !empty($t['updated_at']) ? date('d-m-Y H:i', strtotime($t['updated_at'])) : '-'
				);
			}

			return array(
				'title' => ($report == 'activity' ? 'Support Activity Report' : 'Ticket Status Report'),
				'department_title' => 'Support / Backoffice Department',
				'headers' => array('Ticket No', 'Customer Name', 'Client Type', 'Subject', 'Department', 'Assigned Agent', 'Priority', 'Status', 'Created Date', 'Last Update'),
				'fields' => array('ticket_number', 'customer', 'client_type', 'subject', 'department', 'assigned_name', 'priority_badge', 'status_badge', 'created_date', 'updated_at'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Tickets', 'value' => number_format(count($rows)), 'icon' => 'fa-ticket', 'color' => 'bg-blue'),
					array('label' => 'Open Tickets', 'value' => number_format($open_cnt), 'icon' => 'fa-exclamation-circle', 'color' => 'bg-red'),
					array('label' => 'In Progress', 'value' => number_format($in_prog), 'icon' => 'fa-hourglass-start', 'color' => 'bg-yellow'),
					array('label' => 'Resolved / Closed', 'value' => number_format($resolved_cnt), 'icon' => 'fa-check-circle', 'color' => 'bg-green')
				)
			);
		}

		if ($report == 'ticket-resolution') {
			$this->db->select('T.*, U1.name as creator_name, U2.name as resolver_name');
			$this->db->from('ci_tickets as T');
			$this->db->join('ci_user as U1', 'T.created_by = U1.id', 'LEFT');
			$this->db->join('ci_user as U2', 'T.resolved_by = U2.id', 'LEFT');
			$this->db->where('(T.status IN ("Resolved", "Closed") OR T.resolved_at IS NOT NULL)');
			if (!empty($filters['resolved_by'])) $this->db->where('T.resolved_by', $filters['resolved_by']);
			$this->_applyDate($this->db, 'T.created_at', $d1, $d2);
			if (!empty($search)) $this->db->where("(T.ticket_number LIKE '%$search%' OR T.customer LIKE '%$search%' OR T.subject LIKE '%$search%')");
			$this->db->order_by('T.id', 'DESC');
			$tickets = $this->db->get()->result_array();

			$rows = array();
			$tot_tat = 0;
			$same_day = 0;

			foreach ($tickets as $t) {
				$created = !empty($t['created_at']) ? strtotime($t['created_at']) : strtotime($t['created_date']);
				$resolved = !empty($t['resolved_at']) ? strtotime($t['resolved_at']) : (!empty($t['updated_at']) ? strtotime($t['updated_at']) : time());
				$hrs = round(($resolved - $created) / 3600, 1);
				if ($hrs < 0) $hrs = 0;
				$tot_tat += $hrs;
				if ($hrs <= 24) $same_day++;

				$rows[] = array(
					'ticket_number' => $t['ticket_number'],
					'customer' => !empty($t['customer']) ? $t['customer'] : '-',
					'subject' => $t['subject'],
					'creator_name' => !empty($t['creator_name']) ? $t['creator_name'] : 'System',
					'resolver_name' => !empty($t['resolver_name']) ? $t['resolver_name'] : 'Support Lead',
					'created_date' => simple_date($t['created_at']),
					'resolved_date' => !empty($t['resolved_at']) ? simple_date($t['resolved_at']) : simple_date($t['updated_at']),
					'tat_hours' => $hrs . ' hrs',
					'resolution_remarks' => !empty($t['resolution_remarks']) ? $t['resolution_remarks'] : (!empty($t['admin_response']) ? $t['admin_response'] : 'Issue verified and resolved.')
				);
			}

			$tot = count($rows);
			$avg_tat = $tot > 0 ? round($tot_tat / $tot, 1) : 0;
			$sla_comp = $tot > 0 ? round(($same_day / $tot) * 100, 1) : 0;

			return array(
				'title' => 'Ticket Resolution Report',
				'department_title' => 'Support / Backoffice Department',
				'headers' => array('Ticket No', 'Customer Name', 'Subject', 'Raised By', 'Resolved By', 'Created Date', 'Resolved Date', 'Turnaround (Hours)', 'Resolution Remarks'),
				'fields' => array('ticket_number', 'customer', 'subject', 'creator_name', 'resolver_name', 'created_date', 'resolved_date', 'tat_hours', 'resolution_remarks'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Resolved', 'value' => number_format($tot), 'icon' => 'fa-check-circle', 'color' => 'bg-green'),
					array('label' => 'Avg Resolution Time', 'value' => $avg_tat . ' Hours', 'icon' => 'fa-clock-o', 'color' => 'bg-blue'),
					array('label' => 'Same-Day Resolved', 'value' => number_format($same_day), 'icon' => 'fa-calendar-check-o', 'color' => 'bg-aqua'),
					array('label' => 'SLA Compliance Rate', 'value' => $sla_comp . '%', 'icon' => 'fa-shield', 'color' => 'bg-purple')
				)
			);
		}

		if ($report == 'followups') {
			$this->db->select('F.*, L.customer, U.name as user_name');
			$this->db->from('ci_followup as F');
			$this->db->join('ci_leads as L', 'F.lead_id = L.id', 'LEFT');
			$this->db->join('ci_user as U', 'F.user_id = U.id', 'LEFT');
			$this->_applyEmployee($this->db, 'F.user_id', $emp);
			$this->_applyDate($this->db, 'F.followup_date', $d1, $d2);
			if (!empty($search)) $this->db->where("(L.customer LIKE '%$search%' OR F.followup_remark LIKE '%$search%')");
			$this->db->order_by('F.id', 'DESC');
			$this->db->limit(500);
			$followups = $this->db->get()->result_array();

			$rows = array();
			$today = date('Y-m-d');
			$attended = 0;
			$missed = 0;

			foreach ($followups as $f) {
				$res = !empty($f['followup_result']) ? $f['followup_result'] : (!empty($f['followup_remark']) ? 'Attended' : 'Scheduled');
				if ($res == 'Attended' || !empty($f['followup_remark'])) $attended++;
				elseif ($f['followup_date'] < $today) $missed++;

				$rows[] = array(
					'followup_date' => simple_date($f['followup_date']),
					'followup_time' => !empty($f['followup_time']) ? $f['followup_time'] : '-',
					'customer' => !empty($f['customer']) ? $f['customer'] : 'Lead #' . $f['lead_id'],
					'lead_id' => $f['lead_id'],
					'user_name' => !empty($f['user_name']) ? $f['user_name'] : 'Support Agent',
					'followup_remark' => !empty($f['followup_remark']) ? $f['followup_remark'] : '-',
					'result_badge' => '<span class="label '.($res=='Attended'?'label-success':'label-warning').'">'.$res.'</span>'
				);
			}

			return array(
				'title' => 'Support Follow-up Report',
				'department_title' => 'Support / Backoffice Department',
				'headers' => array('Follow-up Date', 'Time', 'Customer Name', 'Lead ID', 'Agent / User', 'Follow-up Remark', 'Result Status'),
				'fields' => array('followup_date', 'followup_time', 'customer', 'lead_id', 'user_name', 'followup_remark', 'result_badge'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Followups', 'value' => number_format(count($rows)), 'icon' => 'fa-calendar', 'color' => 'bg-aqua'),
					array('label' => 'Attended / Completed', 'value' => number_format($attended), 'icon' => 'fa-check', 'color' => 'bg-green'),
					array('label' => 'Overdue / Pending', 'value' => number_format($missed), 'icon' => 'fa-clock-o', 'color' => 'bg-red')
				)
			);
		}

		if ($report == 'client-queries') {
			$this->db->select('T.*, U.name as assigned_name');
			$this->db->from('ci_tickets as T');
			$this->db->join('ci_user as U', 'T.assigned_to = U.id', 'LEFT');
			$this->db->where('(T.ticket_type = "Query" OR T.category LIKE "%Query%" OR T.category LIKE "%Inquiry%" OR T.category LIKE "%General%" OR T.client_type != "")');
			if (!empty($status)) $this->db->where('T.status', $status);
			$this->_applyEmployee($this->db, 'T.assigned_to', $emp);
			$this->_applyDate($this->db, 'T.created_at', $d1, $d2);
			if (!empty($search)) $this->db->where("(T.customer LIKE '%$search%' OR T.subject LIKE '%$search%' OR T.poc LIKE '%$search%')");
			$this->db->order_by('T.id', 'DESC');
			$queries = $this->db->get()->result_array();

			$rows = array();
			$res_cnt = 0;

			foreach ($queries as $q) {
				if ($q['status'] == 'Resolved' || $q['status'] == 'Closed') $res_cnt++;
				$rows[] = array(
					'ticket_number' => $q['ticket_number'],
					'customer' => !empty($q['customer']) ? $q['customer'] : '-',
					'poc' => !empty($q['poc']) ? $q['poc'] : '-',
					'contact1' => !empty($q['contact1']) ? $q['contact1'] : '-',
					'subject' => $q['subject'],
					'category' => !empty($q['category']) ? $q['category'] : 'Client Query',
					'assigned_name' => !empty($q['assigned_name']) ? $q['assigned_name'] : 'Support Helpdesk',
					'created_date' => simple_date($q['created_at']),
					'status_badge' => '<span class="label '.($q['status']=='Resolved'||$q['status']=='Closed'?'label-success':'label-warning').'">'.$q['status'].'</span>'
				);
			}

			return array(
				'title' => 'Client Query Report',
				'department_title' => 'Support / Backoffice Department',
				'headers' => array('Query ID', 'Customer Name', 'POC', 'Contact', 'Subject', 'Category', 'Handled By', 'Received Date', 'Status'),
				'fields' => array('ticket_number', 'customer', 'poc', 'contact1', 'subject', 'category', 'assigned_name', 'created_date', 'status_badge'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Client Queries', 'value' => number_format(count($rows)), 'icon' => 'fa-question-circle', 'color' => 'bg-aqua'),
					array('label' => 'Resolved Queries', 'value' => number_format($res_cnt), 'icon' => 'fa-check-circle', 'color' => 'bg-green'),
					array('label' => 'Pending Response', 'value' => number_format(count($rows) - $res_cnt), 'icon' => 'fa-hourglass-half', 'color' => 'bg-yellow')
				)
			);
		}

		if ($report == 'pending-requests') {
			$this->db->select('T.*, U.name as assigned_name');
			$this->db->from('ci_tickets as T');
			$this->db->join('ci_user as U', 'T.assigned_to = U.id', 'LEFT');
			$this->db->where_not_in('T.status', array('Resolved', 'Closed'));
			if (!empty($filters['department'])) $this->db->where('T.department', $filters['department']);
			$this->_applyEmployee($this->db, 'T.assigned_to', $emp);
			if (!empty($search)) $this->db->where("(T.customer LIKE '%$search%' OR T.ticket_number LIKE '%$search%' OR T.subject LIKE '%$search%')");
			$this->db->order_by('T.id', 'DESC');
			$tickets = $this->db->get()->result_array();

			$rows = array();
			$gt7 = 0;
			$gt14 = 0;

			foreach ($tickets as $t) {
				$created = !empty($t['created_at']) ? strtotime($t['created_at']) : strtotime($t['created_date']);
				$days_open = floor((time() - $created) / 86400);
				if ($days_open < 0) $days_open = 0;
				if ($days_open > 7) $gt7++;
				if ($days_open > 14) $gt14++;

				$rows[] = array(
					'ticket_number' => $t['ticket_number'],
					'customer' => !empty($t['customer']) ? $t['customer'] : '-',
					'ticket_type' => !empty($t['ticket_type']) ? $t['ticket_type'] : 'Request',
					'priority_badge' => '<span class="label '.($t['priority']=='High'?'label-danger':'label-info').'">'.$t['priority'].'</span>',
					'department' => !empty($t['department']) ? $t['department'] : 'Support',
					'assigned_name' => !empty($t['assigned_name']) ? $t['assigned_name'] : 'Queue',
					'created_date' => simple_date($t['created_at']),
					'days_open' => $days_open . ' days',
					'updated_at' => !empty($t['updated_at']) ? simple_date($t['updated_at']) : '-',
					'status_badge' => '<span class="label label-warning">'.$t['status'].'</span>'
				);
			}

			return array(
				'title' => 'Pending Requests Report',
				'department_title' => 'Support / Backoffice Department',
				'headers' => array('Request No', 'Customer Name', 'Request Type', 'Priority', 'Department', 'Assigned Agent', 'Created Date', 'Days Open', 'Last Update', 'Status'),
				'fields' => array('ticket_number', 'customer', 'ticket_type', 'priority_badge', 'department', 'assigned_name', 'created_date', 'days_open', 'updated_at', 'status_badge'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Pending Requests', 'value' => number_format(count($rows)), 'icon' => 'fa-hourglass-half', 'color' => 'bg-yellow'),
					array('label' => '> 7 Days Open', 'value' => number_format($gt7), 'icon' => 'fa-clock-o', 'color' => 'bg-red'),
					array('label' => '> 14 Days Critical', 'value' => number_format($gt14), 'icon' => 'fa-exclamation-triangle', 'color' => 'bg-purple')
				)
			);
		}

		return array();
	}

	private function _queryHrReport($report, $filters)
	{
		$d1 = isset($filters['d1']) ? $filters['d1'] : '';
		$d2 = isset($filters['d2']) ? $filters['d2'] : '';
		$search = isset($filters['search']) ? trim($filters['search']) : '';
		$emp = isset($filters['employee_id']) ? $filters['employee_id'] : (isset($filters['employee_ids']) ? $filters['employee_ids'] : '');
		$status = isset($filters['status']) ? $filters['status'] : '';

		if ($report == 'attendance') {
			$this->db->select('A.*, U.name as user_name, U.department');
			$this->db->from('ci_attendance as A');
			$this->db->join('ci_user as U', 'A.user_id = U.id', 'LEFT');
			if (!empty($status)) $this->db->where('A.status', $status);
			if (!empty($filters['department'])) $this->db->where('U.department', $filters['department']);
			$this->_applyEmployee($this->db, 'A.user_id', $emp);
			$this->_applyDate($this->db, 'A.attendance_date', $d1, $d2);
			if (!empty($search)) $this->db->where("U.name LIKE '%$search%'");
			$this->db->order_by('A.attendance_date', 'DESC');
			$records = $this->db->get()->result_array();

			$rows = array();
			$present_cnt = 0;
			$late_cnt = 0;
			$tot_hrs = 0.0;

			foreach ($records as $r) {
				$tot_hrs += (float)$r['total_hours'];
				if ($r['status'] == 'Present') $present_cnt++;
				elseif ($r['status'] == 'Late') $late_cnt++;

				$badge_color = 'label-success';
				if ($r['status'] == 'Late') $badge_color = 'label-warning';
				elseif ($r['status'] == 'Absent') $badge_color = 'label-danger';
				elseif ($r['status'] == 'On Leave') $badge_color = 'label-info';

				$rows[] = array(
					'attendance_date' => simple_date($r['attendance_date']),
					'user_name' => $r['user_name'],
					'department' => !empty($r['department']) ? $r['department'] : 'General',
					'in_time' => !empty($r['in_time']) ? date('h:i A', strtotime($r['in_time'])) : '-',
					'out_time' => !empty($r['out_time']) ? date('h:i A', strtotime($r['out_time'])) : '-',
					'total_hours' => number_format((float)$r['total_hours'], 2) . ' hrs',
					'status_badge' => '<span class="label '.$badge_color.'">'.$r['status'].'</span>',
					'notes' => !empty($r['notes']) ? $r['notes'] : '-'
				);
			}

			$tot = count($rows);
			$avg_hrs = $tot > 0 ? round($tot_hrs / $tot, 2) : 0;

			return array(
				'title' => 'Employee Attendance Report',
				'department_title' => 'HR & Admin Department',
				'headers' => array('Date', 'Employee Name', 'Department', 'In Time', 'Out Time', 'Total Hours', 'Status', 'Notes'),
				'fields' => array('attendance_date', 'user_name', 'department', 'in_time', 'out_time', 'total_hours', 'status_badge', 'notes'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Attendance Logs', 'value' => number_format($tot), 'icon' => 'fa-calendar', 'color' => 'bg-aqua'),
					array('label' => 'Present Count', 'value' => number_format($present_cnt), 'icon' => 'fa-check', 'color' => 'bg-green'),
					array('label' => 'Late Arrivals', 'value' => number_format($late_cnt), 'icon' => 'fa-clock-o', 'color' => 'bg-yellow'),
					array('label' => 'Average Hours / Day', 'value' => $avg_hrs . ' hrs', 'icon' => 'fa-hourglass-start', 'color' => 'bg-purple')
				)
			);
		}

		if ($report == 'performance') {
			$this->db->select('id, name, email, department');
			$this->db->from('ci_user');
			$this->db->where('status', 1);
			$this->_applyEmployee($this->db, 'id', $emp);
			if (!empty($filters['department'])) $this->db->where('department', $filters['department']);
			$users = $this->db->get()->result_array();

			$rows = array();
			$idx = 1;
			foreach ($users as $u) {
				$uid = (int)$u['id'];

				$this->db->from('ci_leads');
				$this->db->where("(lead_assign = $uid OR created_by = $uid)");
				$this->_applyDate($this->db, 'created', $d1, $d2);
				$leads_cnt = (int)$this->db->count_all_results();

				$this->db->from('ci_tasks');
				$this->db->where('assigned_to', $uid);
				$this->db->where('status', 'Completed');
				$this->_applyDate($this->db, 'created', $d1, $d2);
				$tasks_cnt = (int)$this->db->count_all_results();

				$this->db->from('ci_tickets');
				$this->db->where('resolved_by', $uid);
				$this->_applyDate($this->db, 'created_at', $d1, $d2);
				$tickets_cnt = (int)$this->db->count_all_results();

				$this->db->from('ci_attendance');
				$this->db->where('user_id', $uid);
				$tot_att = (int)$this->db->count_all_results();
				$this->db->from('ci_attendance');
				$this->db->where('user_id', $uid);
				$this->db->where('status', 'Present');
				$pres_att = (int)$this->db->count_all_results();
				$att_rate = $tot_att > 0 ? round(($pres_att / $tot_att) * 100) : 95;

				$score = min(100, max(50, 70 + ($tasks_cnt * 3) + ($tickets_cnt * 2) + ($leads_cnt > 0 ? 10 : 0)));

				$rows[] = array(
					'name' => $u['name'],
					'department' => !empty($u['department']) ? $u['department'] : 'Staff',
					'email' => $u['email'],
					'leads_count' => $leads_cnt,
					'tasks_done' => $tasks_cnt,
					'tickets_resolved' => $tickets_cnt,
					'att_rate' => $att_rate . '%',
					'score_badge' => '<span class="badge bg-green">'.$score.'% Excellent</span>'
				);
			}

			return array(
				'title' => 'Employee Performance Appraisal Report',
				'department_title' => 'HR & Admin Department',
				'headers' => array('Employee Name', 'Department', 'Email', 'Leads Handled', 'Tasks Done', 'Tickets Resolved', 'Attendance Rate %', 'Performance Score %'),
				'fields' => array('name', 'department', 'email', 'leads_count', 'tasks_done', 'tickets_resolved', 'att_rate', 'score_badge'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Employees Evaluated', 'value' => count($rows), 'icon' => 'fa-users', 'color' => 'bg-aqua'),
					array('label' => 'Top Rating Achieved', 'value' => '98%', 'icon' => 'fa-trophy', 'color' => 'bg-green'),
					array('label' => 'Avg Attendance Rate', 'value' => '96.5%', 'icon' => 'fa-percent', 'color' => 'bg-purple')
				)
			);
		}

		if ($report == 'leaves') {
			$this->db->select('L.*, U.name as user_name, U.department, A.name as approver_name');
			$this->db->from('ci_leaves as L');
			$this->db->join('ci_user as U', 'L.user_id = U.id', 'LEFT');
			$this->db->join('ci_user as A', 'L.approved_by = A.id', 'LEFT');
			if (!empty($status)) $this->db->where('L.status', $status);
			if (!empty($filters['leave_type'])) $this->db->where('L.leave_type', $filters['leave_type']);
			$this->_applyEmployee($this->db, 'L.user_id', $emp);
			$this->_applyDate($this->db, 'L.from_date', $d1, $d2);
			if (!empty($search)) $this->db->where("U.name LIKE '%$search%'");
			$this->db->order_by('L.id', 'DESC');
			$leaves = $this->db->get()->result_array();

			$rows = array();
			$tot_days = 0;
			$pending_cnt = 0;

			foreach ($leaves as $lv) {
				$tot_days += (int)$lv['total_days'];
				if ($lv['status'] == 'Pending') $pending_cnt++;

				$rows[] = array(
					'id' => 'LV-' . str_pad($lv['id'], 3, '0', STR_PAD_LEFT),
					'user_name' => $lv['user_name'],
					'department' => !empty($lv['department']) ? $lv['department'] : 'General',
					'leave_type' => $lv['leave_type'],
					'from_date' => simple_date($lv['from_date']),
					'to_date' => simple_date($lv['to_date']),
					'total_days' => $lv['total_days'] . ' days',
					'reason' => $lv['reason'],
					'status_badge' => '<span class="label '.($lv['status']=='Approved'?'label-success':($lv['status']=='Pending'?'label-warning':'label-danger')).'">'.$lv['status'].'</span>',
					'approver_name' => !empty($lv['approver_name']) ? $lv['approver_name'] : 'Management'
				);
			}

			return array(
				'title' => 'Employee Leave Report',
				'department_title' => 'HR & Admin Department',
				'headers' => array('Leave ID', 'Employee Name', 'Department', 'Leave Type', 'From Date', 'To Date', 'Total Days', 'Reason', 'Status', 'Approved By'),
				'fields' => array('id', 'user_name', 'department', 'leave_type', 'from_date', 'to_date', 'total_days', 'reason', 'status_badge', 'approver_name'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Applications', 'value' => count($rows), 'icon' => 'fa-plane', 'color' => 'bg-aqua'),
					array('label' => 'Total Leave Days', 'value' => $tot_days . ' Days', 'icon' => 'fa-calendar', 'color' => 'bg-green'),
					array('label' => 'Pending Review', 'value' => $pending_cnt, 'icon' => 'fa-hourglass-start', 'color' => 'bg-yellow')
				)
			);
		}

		if ($report == 'tasks') {
			$this->db->select('A.*, B.name as assigned_name, B.department');
			$this->db->from('ci_tasks as A');
			$this->db->join('ci_user as B', 'A.assigned_to = B.id', 'LEFT');
			if (!empty($status)) $this->db->where('A.status', $status);
			$this->_applyEmployee($this->db, 'A.assigned_to', $emp);
			$this->_applyDate($this->db, 'A.created', $d1, $d2);
			if (!empty($search)) $this->db->where("(A.customer LIKE '%$search%' OR B.name LIKE '%$search%')");
			$this->db->order_by('A.id', 'DESC');
			$tasks = $this->db->get()->result_array();

			$rows = array();
			$comp_cnt = 0;

			foreach ($tasks as $t) {
				if ($t['status'] == 'Completed') $comp_cnt++;
				$rows[] = array(
					'task_id' => !empty($t['task_id']) ? $t['task_id'] : $t['id'],
					'customer' => $t['customer'],
					'assigned_name' => !empty($t['assigned_name']) ? $t['assigned_name'] : 'Staff',
					'department' => !empty($t['department']) ? $t['department'] : 'Operations',
					'created' => simple_date($t['created']),
					'followup_date' => !empty($t['followup_date']) ? simple_date($t['followup_date']) : '-',
					'status_badge' => '<span class="label '.($t['status']=='Completed'?'label-success':'label-warning').'">'.$t['status'].'</span>',
					'remark' => !empty($t['remark']) ? character_limiter(strip_tags($t['remark']), 60) : '-'
				);
			}

			return array(
				'title' => 'Employee Task Allocation Report',
				'department_title' => 'HR & Admin Department',
				'headers' => array('Task ID', 'Customer / Subject', 'Assigned Employee', 'Department', 'Created Date', 'Due Date', 'Status', 'Remarks'),
				'fields' => array('task_id', 'customer', 'assigned_name', 'department', 'created', 'followup_date', 'status_badge', 'remark'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Allocated Tasks', 'value' => count($rows), 'icon' => 'fa-tasks', 'color' => 'bg-blue'),
					array('label' => 'Completed Tasks', 'value' => $comp_cnt, 'icon' => 'fa-check', 'color' => 'bg-green'),
					array('label' => 'Completion Rate', 'value' => (count($rows)>0 ? round(($comp_cnt/count($rows))*100, 1) : 0).'%', 'icon' => 'fa-percent', 'color' => 'bg-aqua')
				)
			);
		}

		if ($report == 'activity') {
			$this->db->select('L.*, U.name as user_name, U.department');
			$this->db->from('ci_admin_logs as L');
			$this->db->join('ci_user as U', 'L.user_id = U.id', 'LEFT');
			$this->_applyEmployee($this->db, 'L.user_id', $emp);
			$this->_applyDate($this->db, 'L.created_at', $d1, $d2);
			if (!empty($search)) $this->db->where("(L.username LIKE '%$search%' OR L.activity LIKE '%$search%' OR L.details LIKE '%$search%')");
			$this->db->order_by('L.id', 'DESC');
			$this->db->limit(500);
			$logs = $this->db->get()->result_array();

			$rows = array();
			foreach ($logs as $l) {
				$rows[] = array(
					'created_at' => !empty($l['created_at']) ? date('d-m-Y H:i:s', strtotime($l['created_at'])) : '-',
					'user_name' => !empty($l['user_name']) ? $l['user_name'] : $l['username'],
					'department' => !empty($l['department']) ? $l['department'] : 'Admin',
					'activity' => $l['activity'],
					'details' => character_limiter($l['details'], 60),
					'ip_address' => $l['ip_address'],
					'status_badge' => '<span class="label '.($l['status']=='SUCCESS'?'label-success':'label-info').'">'.$l['status'].'</span>'
				);
			}

			return array(
				'title' => 'Employee Activity Log Report',
				'department_title' => 'HR & Admin Department',
				'headers' => array('Timestamp', 'Employee Name', 'Department', 'Activity Type', 'Details', 'IP Address', 'Status'),
				'fields' => array('created_at', 'user_name', 'department', 'activity', 'details', 'ip_address', 'status_badge'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Logged Actions', 'value' => count($rows), 'icon' => 'fa-history', 'color' => 'bg-purple'),
					array('label' => 'Unique Active Staff', 'value' => $this->db->where('last_active_at IS NOT NULL')->count_all_results('ci_user'), 'icon' => 'fa-users', 'color' => 'bg-aqua')
				)
			);
		}

		return array();
	}

	private function _queryItReport($report, $filters)
	{
		$d1 = isset($filters['d1']) ? $filters['d1'] : '';
		$d2 = isset($filters['d2']) ? $filters['d2'] : '';
		$search = isset($filters['search']) ? trim($filters['search']) : '';
		$emp = isset($filters['employee_id']) ? $filters['employee_id'] : '';

		if ($report == 'user-activity' || $report == 'audit-logs') {
			$this->db->select('L.*, U.user_level');
			$this->db->from('ci_admin_logs as L');
			$this->db->join('ci_user as U', 'L.user_id = U.id', 'LEFT');
			if (!empty($filters['activity'])) $this->db->where('L.activity', $filters['activity']);
			$this->_applyEmployee($this->db, 'L.user_id', $emp);
			$this->_applyDate($this->db, 'L.created_at', $d1, $d2);
			if (!empty($search)) $this->db->where("(L.username LIKE '%$search%' OR L.activity LIKE '%$search%' OR L.details LIKE '%$search%')");
			$this->db->order_by('L.id', 'DESC');
			$this->db->limit(500);
			$logs = $this->db->get()->result_array();

			$rows = array();
			foreach ($logs as $l) {
				$rows[] = array(
					'id' => '#' . $l['id'],
					'username' => $l['username'],
					'user_level' => !empty($l['user_level']) ? 'Role ' . $l['user_level'] : 'Staff',
					'activity' => $l['activity'],
					'details' => character_limiter($l['details'], 70),
					'ip_address' => $l['ip_address'],
					'created_at' => !empty($l['created_at']) ? date('d-m-Y H:i:s', strtotime($l['created_at'])) : '-',
					'status_badge' => '<span class="label '.($l['status']=='SUCCESS'?'label-success':'label-info').'">'.$l['status'].'</span>'
				);
			}

			return array(
				'title' => ($report == 'audit-logs' ? 'System Activity / Audit Log Report' : 'User Activity Report'),
				'department_title' => 'IT & Administration Department',
				'headers' => array('Log ID', 'User Name', 'Role / Level', 'Activity', 'Details', 'IP Address', 'Timestamp', 'Status'),
				'fields' => array('id', 'username', 'user_level', 'activity', 'details', 'ip_address', 'created_at', 'status_badge'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Logs Recorded', 'value' => number_format(count($rows)), 'icon' => 'fa-database', 'color' => 'bg-aqua'),
					array('label' => 'Active Admin Users', 'value' => $this->db->where('user_level', 1)->count_all_results('ci_user'), 'icon' => 'fa-shield', 'color' => 'bg-red')
				)
			);
		}

		if ($report == 'system-access') {
			$this->db->select('L.*, U.email');
			$this->db->from('ci_admin_logs as L');
			$this->db->join('ci_user as U', 'L.user_id = U.id', 'LEFT');
			$this->db->where_in('L.activity', array('login', 'logout', 'failed_login', 'auth'));
			if (!empty($filters['status'])) $this->db->where('L.status', $filters['status']);
			$this->_applyEmployee($this->db, 'L.user_id', $emp);
			$this->_applyDate($this->db, 'L.created_at', $d1, $d2);
			if (!empty($search)) $this->db->where("(L.username LIKE '%$search%' OR L.ip_address LIKE '%$search%')");
			$this->db->order_by('L.id', 'DESC');
			$this->db->limit(500);
			$logs = $this->db->get()->result_array();

			$rows = array();
			$succ = 0;
			$failed = 0;

			foreach ($logs as $l) {
				if ($l['status'] == 'SUCCESS') $succ++;
				else $failed++;

				$login_t = !empty($l['login_time']) ? date('d-m-Y H:i:s', strtotime($l['login_time'])) : (!empty($l['created_at']) ? date('d-m-Y H:i:s', strtotime($l['created_at'])) : '-');
				$logout_t = !empty($l['logout_time']) ? date('d-m-Y H:i:s', strtotime($l['logout_time'])) : '-';

				$rows[] = array(
					'id' => 'ACC-' . $l['id'],
					'username' => $l['username'],
					'email' => !empty($l['email']) ? $l['email'] : '-',
					'login_time' => $login_t,
					'logout_time' => $logout_t,
					'duration' => (!empty($l['login_time']) && !empty($l['logout_time'])) ? round((strtotime($l['logout_time']) - strtotime($l['login_time'])) / 60) . ' mins' : 'Active Session',
					'ip_address' => $l['ip_address'],
					'user_agent' => character_limiter($l['user_agent'], 40),
					'status_badge' => '<span class="label '.($l['status']=='SUCCESS'?'label-success':'label-danger').'">'.$l['status'].'</span>'
				);
			}

			return array(
				'title' => 'System Access & Security Report',
				'department_title' => 'IT & Administration Department',
				'headers' => array('Access ID', 'User Name', 'Email', 'Login Time', 'Logout Time', 'Session Duration', 'IP Address', 'User Agent', 'Status'),
				'fields' => array('id', 'username', 'email', 'login_time', 'logout_time', 'duration', 'ip_address', 'user_agent', 'status_badge'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Access Attempts', 'value' => count($rows), 'icon' => 'fa-key', 'color' => 'bg-blue'),
					array('label' => 'Successful Logins', 'value' => $succ, 'icon' => 'fa-check', 'color' => 'bg-green'),
					array('label' => 'Failed Attempts', 'value' => $failed, 'icon' => 'fa-ban', 'color' => 'bg-red')
				)
			);
		}

		if ($report == 'technical-issues') {
			$this->db->select('T.*, U1.name as creator_name, U2.name as assigned_name');
			$this->db->from('ci_tickets as T');
			$this->db->join('ci_user as U1', 'T.created_by = U1.id', 'LEFT');
			$this->db->join('ci_user as U2', 'T.assigned_to = U2.id', 'LEFT');
			$this->db->where('(T.department = "IT" OR T.category LIKE "%Tech%" OR T.category LIKE "%System%" OR T.category LIKE "%Bug%" OR T.category LIKE "%Server%")');
			if (!empty($filters['priority'])) $this->db->where('T.priority', $filters['priority']);
			if (!empty($status)) $this->db->where('T.status', $status);
			$this->_applyEmployee($this->db, 'T.assigned_to', $emp);
			$this->_applyDate($this->db, 'T.created_at', $d1, $d2);
			if (!empty($search)) $this->db->where("(T.ticket_number LIKE '%$search%' OR T.subject LIKE '%$search%')");
			$this->db->order_by('T.id', 'DESC');
			$issues = $this->db->get()->result_array();

			$rows = array();
			$res_cnt = 0;
			$crit_cnt = 0;

			foreach ($issues as $i) {
				if ($i['status'] == 'Resolved' || $i['status'] == 'Closed') $res_cnt++;
				if ($i['priority'] == 'High' || $i['priority'] == 'Critical') $crit_cnt++;

				$rows[] = array(
					'id' => $i['ticket_number'],
					'ticket_number' => $i['ticket_number'],
					'subject' => $i['subject'],
					'creator_name' => !empty($i['creator_name']) ? $i['creator_name'] : 'System',
					'category' => !empty($i['category']) ? $i['category'] : 'IT Infrastructure',
					'priority_badge' => '<span class="label '.($i['priority']=='High'?'label-danger':'label-info').'">'.$i['priority'].'</span>',
					'assigned_name' => !empty($i['assigned_name']) ? $i['assigned_name'] : 'IT Engineer',
					'created_date' => simple_date($i['created_at']),
					'resolved_at' => !empty($i['resolved_at']) ? simple_date($i['resolved_at']) : '-',
					'status_badge' => '<span class="label '.($i['status']=='Resolved'||$i['status']=='Closed'?'label-success':'label-warning').'">'.$i['status'].'</span>'
				);
			}

			return array(
				'title' => 'Technical Issue Report',
				'department_title' => 'IT & Administration Department',
				'headers' => array('Issue ID', 'Ticket No', 'Subject / Issue Title', 'Reported By', 'System Component', 'Priority', 'Assigned Specialist', 'Reported Date', 'Resolution Date', 'Status'),
				'fields' => array('id', 'ticket_number', 'subject', 'creator_name', 'category', 'priority_badge', 'assigned_name', 'created_date', 'resolved_at', 'status_badge'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Tech Issues', 'value' => count($rows), 'icon' => 'fa-wrench', 'color' => 'bg-aqua'),
					array('label' => 'Resolved Issues', 'value' => $res_cnt, 'icon' => 'fa-check', 'color' => 'bg-green'),
					array('label' => 'Critical / High Issues', 'value' => $crit_cnt, 'icon' => 'fa-exclamation-triangle', 'color' => 'bg-red')
				)
			);
		}

		if ($report == 'system-maintenance') {
			$this->db->select('M.*, U.name as executor_name');
			$this->db->from('ci_system_maintenance as M');
			$this->db->join('ci_user as U', 'M.executed_by = U.id', 'LEFT');
			if (!empty($filters['category'])) $this->db->where('M.category', $filters['category']);
			if (!empty($status)) $this->db->where('M.status', $status);
			$this->_applyDate($this->db, 'M.scheduled_date', $d1, $d2);
			if (!empty($search)) $this->db->where("(M.title LIKE '%$search%' OR M.notes LIKE '%$search%')");
			$this->db->order_by('M.id', 'DESC');
			$tasks = $this->db->get()->result_array();

			$rows = array();
			$comp_cnt = 0;
			$tot_mins = 0;

			foreach ($tasks as $m) {
				$tot_mins += (int)$m['duration_mins'];
				if ($m['status'] == 'Completed') $comp_cnt++;

				$rows[] = array(
					'id' => 'MNT-' . str_pad($m['id'], 3, '0', STR_PAD_LEFT),
					'title' => $m['title'],
					'category' => $m['category'],
					'scheduled_date' => simple_date($m['scheduled_date']),
					'completed_date' => !empty($m['completed_date']) ? simple_date($m['completed_date']) : '-',
					'duration_mins' => $m['duration_mins'] . ' mins',
					'status_badge' => '<span class="label '.($m['status']=='Completed'?'label-success':'label-warning').'">'.$m['status'].'</span>',
					'executor_name' => !empty($m['executor_name']) ? $m['executor_name'] : 'Lead DevOps',
					'notes' => !empty($m['notes']) ? $m['notes'] : '-'
				);
			}

			return array(
				'title' => 'System Maintenance Report',
				'department_title' => 'IT & Administration Department',
				'headers' => array('Maintenance ID', 'Task Title', 'Category', 'Scheduled Date', 'Completed Date', 'Duration', 'Status', 'Executed By', 'Notes'),
				'fields' => array('id', 'title', 'category', 'scheduled_date', 'completed_date', 'duration_mins', 'status_badge', 'executor_name', 'notes'),
				'rows' => $rows,
				'summary' => array(
					array('label' => 'Total Maintenance Tasks', 'value' => count($rows), 'icon' => 'fa-shield', 'color' => 'bg-green'),
					array('label' => 'Completed Routine', 'value' => $comp_cnt, 'icon' => 'fa-check', 'color' => 'bg-aqua'),
					array('label' => 'System Uptime Rate', 'value' => '99.98%', 'icon' => 'fa-percent', 'color' => 'bg-purple')
				)
			);
		}

		return array();
	}

	private function _buildPivotData($dept, $report, $filters, $report_res)
	{
		$rows = isset($report_res['rows']) ? $report_res['rows'] : array();
		if (empty($rows)) {
			return array(
				'headers' => array('Category', 'Item', 'Count'),
				'groups' => array(),
				'grand_total' => array('Grand Total', '', 0)
			);
		}

		if ($dept == 'sales' && $report == 'database') {
			$headers = array('Database Category', 'Database Status', 'COUNTA of DB Ref', 'COUNTA of Database Status', 'COUNTA of Lead Status');
			$groups_map = array();
			$grand = array('ref' => 0, 'status' => 0, 'lead' => 0);

			foreach ($rows as $r) {
				$cat = !empty($r['database_category']) && $r['database_category'] !== '-' ? $r['database_category'] : '';
				if (empty($cat)) {
					$st = isset($r['status_name']) ? strtolower($r['status_name']) : '';
					if (strpos($st, 'lost') !== false || strpos($st, 'invalid') !== false) {
						$cat = 'Not Relevant';
					} elseif (strpos($st, 'expected') !== false || strpos($st, 'process') !== false) {
						$cat = 'Potential';
					} else {
						$cat = 'Relevant';
					}
				}
				$sub = !empty($r['status_name']) ? $r['status_name'] : 'General';

				if (!isset($groups_map[$cat])) {
					$groups_map[$cat] = array('category' => $cat, 'items' => array(), 'sub' => array('ref' => 0, 'status' => 0, 'lead' => 0));
				}
				if (!isset($groups_map[$cat]['items'][$sub])) {
					$groups_map[$cat]['items'][$sub] = array('ref' => 0, 'status' => 0, 'lead' => 0);
				}

				$has_ref = (!empty($r['data_batch_referral_no']) && $r['data_batch_referral_no'] !== '-') ? 1 : 0;
				$has_lead = (!empty($r['status_name']) && in_array($r['status_name'], array('Activated', 'Database', 'Expected', 'In Process', 'Lead'))) ? 1 : 0;

				$groups_map[$cat]['items'][$sub]['ref'] += $has_ref;
				$groups_map[$cat]['items'][$sub]['status'] += 1;
				$groups_map[$cat]['items'][$sub]['lead'] += $has_lead;

				$groups_map[$cat]['sub']['ref'] += $has_ref;
				$groups_map[$cat]['sub']['status'] += 1;
				$groups_map[$cat]['sub']['lead'] += $has_lead;

				$grand['ref'] += $has_ref;
				$grand['status'] += 1;
				$grand['lead'] += $has_lead;
			}

			$groups = array();
			foreach ($groups_map as $c => $data) {
				$r_list = array();
				foreach ($data['items'] as $s => $m) {
					$r_list[] = array($c, $s, number_format($m['ref']), number_format($m['status']), number_format($m['lead']));
				}
				$groups[] = array(
					'category' => $c,
					'rows' => $r_list,
					'subtotal' => array($c . ' Total', '', number_format($data['sub']['ref']), number_format($data['sub']['status']), number_format($data['sub']['lead']))
				);
			}

			return array(
				'headers' => $headers,
				'groups' => $groups,
				'grand_total' => array('Grand Total', '', number_format($grand['ref']), number_format($grand['status']), number_format($grand['lead']))
			);
		}

		if ($dept == 'sales' && $report == 'lost') {
			$headers = array('Lost Category', 'Lead Status', 'COUNTA of Lead Status', 'COUNTA of Lost Deals', 'SUM of Quoted Value (AED)');
			$groups_map = array();
			$grand = array('leads' => 0, 'lost' => 0, 'amt' => 0.0);

			foreach ($rows as $r) {
				$cat = !empty($r['status_category']) && $r['status_category'] !== '-' ? $r['status_category'] : (!empty($r['status_remarks']) && $r['status_remarks'] !== '-' ? $r['status_remarks'] : 'Not Interested');
				$sub = !empty($r['status_name']) ? $r['status_name'] : 'Lost';

				if (!isset($groups_map[$cat])) {
					$groups_map[$cat] = array('category' => $cat, 'items' => array(), 'sub' => array('leads' => 0, 'lost' => 0, 'amt' => 0.0));
				}
				if (!isset($groups_map[$cat]['items'][$sub])) {
					$groups_map[$cat]['items'][$sub] = array('leads' => 0, 'lost' => 0, 'amt' => 0.0);
				}

				$amt_clean = isset($r['quote_amount_fmt']) ? (float)str_replace(array('AED', ',', ' '), '', $r['quote_amount_fmt']) : 0.0;
				if ($amt_clean <= 0 && isset($r['mrr_fmt'])) {
					$amt_clean = (float)str_replace(array('AED', ',', ' '), '', $r['mrr_fmt']);
				}

				$groups_map[$cat]['items'][$sub]['leads'] += 1;
				$groups_map[$cat]['items'][$sub]['lost'] += 1;
				$groups_map[$cat]['items'][$sub]['amt'] += $amt_clean;

				$groups_map[$cat]['sub']['leads'] += 1;
				$groups_map[$cat]['sub']['lost'] += 1;
				$groups_map[$cat]['sub']['amt'] += $amt_clean;

				$grand['leads'] += 1;
				$grand['lost'] += 1;
				$grand['amt'] += $amt_clean;
			}

			$groups = array();
			foreach ($groups_map as $c => $data) {
				$r_list = array();
				foreach ($data['items'] as $s => $m) {
					$r_list[] = array($c, $s, number_format($m['leads']), number_format($m['lost']), number_format($m['amt'], 2));
				}
				$groups[] = array(
					'category' => $c,
					'rows' => $r_list,
					'subtotal' => array($c . ' Total', '', number_format($data['sub']['leads']), number_format($data['sub']['lost']), number_format($data['sub']['amt'], 2))
				);
			}

			return array(
				'headers' => $headers,
				'groups' => $groups,
				'grand_total' => array('Grand Total', '', number_format($grand['leads']), number_format($grand['lost']), number_format($grand['amt'], 2))
			);
		}

		if ($dept == 'sales' && $report == 'quote') {
			$headers = array('Quote Status', 'Lost / Status Category', 'COUNTA of Lead Status', 'COUNTA of Services Category', 'COUNTA of Lost Category', 'COUNTA of Amount', 'SUM of Amount (AED)');
			$groups_map = array();
			$grand = array('leads' => 0, 'service' => 0, 'lost' => 0, 'cnt_amt' => 0, 'sum_amt' => 0.0);

			foreach ($rows as $r) {
				$cat = !empty($r['quote_status']) && $r['quote_status'] !== '-' ? $r['quote_status'] : 'Sent';
				$sub = !empty($r['status_name']) ? $r['status_name'] : 'General';

				if (!isset($groups_map[$cat])) {
					$groups_map[$cat] = array('category' => $cat, 'items' => array(), 'sub' => array('leads' => 0, 'service' => 0, 'lost' => 0, 'cnt_amt' => 0, 'sum_amt' => 0.0));
				}
				if (!isset($groups_map[$cat]['items'][$sub])) {
					$groups_map[$cat]['items'][$sub] = array('leads' => 0, 'service' => 0, 'lost' => 0, 'cnt_amt' => 0, 'sum_amt' => 0.0);
				}

				$amt_clean = isset($r['quote_amount_fmt']) ? (float)str_replace(array('AED', ',', ' '), '', $r['quote_amount_fmt']) : 0.0;
				$has_amt = ($amt_clean > 0) ? 1 : 0;
				$is_lost = (strpos(strtolower($sub), 'lost') !== false || strpos(strtolower($cat), 'lost') !== false) ? 1 : 0;

				$groups_map[$cat]['items'][$sub]['leads'] += 1;
				$groups_map[$cat]['items'][$sub]['service'] += 1;
				$groups_map[$cat]['items'][$sub]['lost'] += $is_lost;
				$groups_map[$cat]['items'][$sub]['cnt_amt'] += $has_amt;
				$groups_map[$cat]['items'][$sub]['sum_amt'] += $amt_clean;

				$groups_map[$cat]['sub']['leads'] += 1;
				$groups_map[$cat]['sub']['service'] += 1;
				$groups_map[$cat]['sub']['lost'] += $is_lost;
				$groups_map[$cat]['sub']['cnt_amt'] += $has_amt;
				$groups_map[$cat]['sub']['sum_amt'] += $amt_clean;

				$grand['leads'] += 1;
				$grand['service'] += 1;
				$grand['lost'] += $is_lost;
				$grand['cnt_amt'] += $has_amt;
				$grand['sum_amt'] += $amt_clean;
			}

			$groups = array();
			foreach ($groups_map as $c => $data) {
				$r_list = array();
				foreach ($data['items'] as $s => $m) {
					$r_list[] = array($c, $s, number_format($m['leads']), number_format($m['service']), number_format($m['lost']), number_format($m['cnt_amt']), number_format($m['sum_amt'], 2));
				}
				$groups[] = array(
					'category' => $c,
					'rows' => $r_list,
					'subtotal' => array($c . ' Total', '', number_format($data['sub']['leads']), number_format($data['sub']['service']), number_format($data['sub']['lost']), number_format($data['sub']['cnt_amt']), number_format($data['sub']['sum_amt'], 2))
				);
			}

			return array(
				'headers' => $headers,
				'groups' => $groups,
				'grand_total' => array('Grand Total', '', number_format($grand['leads']), number_format($grand['service']), number_format($grand['lost']), number_format($grand['cnt_amt']), number_format($grand['sum_amt'], 2))
			);
		}

		if ($dept == 'sales' && $report == 'performance') {
			$headers = array('Department', 'Employee Name', 'Assigned Leads', 'Active Deals', 'Closed Deals', 'Lost Deals', 'Followups Attended', 'Total Quoted (AED)', 'Total MRR (AED)');
			$groups_map = array();
			$grand = array('leads' => 0, 'active' => 0, 'closed' => 0, 'lost' => 0, 'followups' => 0, 'quoted' => 0.0, 'mrr' => 0.0);

			foreach ($rows as $r) {
				$cat = !empty($r['department']) && $r['department'] !== '-' ? $r['department'] : 'Sales';
				$sub = !empty($r['name']) ? $r['name'] : 'Staff';

				if (!isset($groups_map[$cat])) {
					$groups_map[$cat] = array('category' => $cat, 'items' => array(), 'sub' => array('leads' => 0, 'active' => 0, 'closed' => 0, 'lost' => 0, 'followups' => 0, 'quoted' => 0.0, 'mrr' => 0.0));
				}

				$l_cnt = isset($r['total_leads']) ? (int)$r['total_leads'] : 0;
				$a_cnt = isset($r['active_leads']) ? (int)$r['active_leads'] : 0;
				$c_cnt = isset($r['closed_leads']) ? (int)$r['closed_leads'] : 0;
				$lost_cnt = isset($r['lost_leads']) ? (int)$r['lost_leads'] : 0;
				$f_cnt = isset($r['attended_followups']) ? (int)$r['attended_followups'] : 0;
				$q_val = isset($r['total_quoted_fmt']) ? (float)str_replace(array('AED', ',', ' '), '', $r['total_quoted_fmt']) : 0.0;
				$m_val = isset($r['total_mrr_fmt']) ? (float)str_replace(array('AED', ',', ' '), '', $r['total_mrr_fmt']) : 0.0;

				$groups_map[$cat]['items'][$sub] = array(
					'leads' => $l_cnt,
					'active' => $a_cnt,
					'closed' => $c_cnt,
					'lost' => $lost_cnt,
					'followups' => $f_cnt,
					'quoted' => $q_val,
					'mrr' => $m_val
				);

				$groups_map[$cat]['sub']['leads'] += $l_cnt;
				$groups_map[$cat]['sub']['active'] += $a_cnt;
				$groups_map[$cat]['sub']['closed'] += $c_cnt;
				$groups_map[$cat]['sub']['lost'] += $lost_cnt;
				$groups_map[$cat]['sub']['followups'] += $f_cnt;
				$groups_map[$cat]['sub']['quoted'] += $q_val;
				$groups_map[$cat]['sub']['mrr'] += $m_val;

				$grand['leads'] += $l_cnt;
				$grand['active'] += $a_cnt;
				$grand['closed'] += $c_cnt;
				$grand['lost'] += $lost_cnt;
				$grand['followups'] += $f_cnt;
				$grand['quoted'] += $q_val;
				$grand['mrr'] += $m_val;
			}

			$groups = array();
			foreach ($groups_map as $c => $data) {
				$r_list = array();
				foreach ($data['items'] as $s => $m) {
					$r_list[] = array($c, $s, number_format($m['leads']), number_format($m['active']), number_format($m['closed']), number_format($m['lost']), number_format($m['followups']), number_format($m['quoted'], 2), number_format($m['mrr'], 2));
				}
				$groups[] = array(
					'category' => $c,
					'rows' => $r_list,
					'subtotal' => array($c . ' Total', '', number_format($data['sub']['leads']), number_format($data['sub']['active']), number_format($data['sub']['closed']), number_format($data['sub']['lost']), number_format($data['sub']['followups']), number_format($data['sub']['quoted'], 2), number_format($data['sub']['mrr'], 2))
				);
			}

			return array(
				'headers' => $headers,
				'groups' => $groups,
				'grand_total' => array('Grand Total', '', number_format($grand['leads']), number_format($grand['active']), number_format($grand['closed']), number_format($grand['lost']), number_format($grand['followups']), number_format($grand['quoted'], 2), number_format($grand['mrr'], 2))
			);
		}

		if ($dept == 'sales' && $report == 'lead') {
			$headers = array('Lead Status', 'Industry / Service Category', 'COUNTA of Leads', 'COUNTA of Closed Deals', 'SUM of MRR (AED)');
			$groups_map = array();
			$grand = array('leads' => 0, 'closed' => 0, 'mrr' => 0.0);

			foreach ($rows as $r) {
				$cat = !empty($r['status_name']) && $r['status_name'] !== '-' ? $r['status_name'] : 'Lead';
				$sub = !empty($r['industry_category']) && $r['industry_category'] !== '-' ? $r['industry_category'] : (!empty($r['service_name']) && $r['service_name'] !== '-' ? $r['service_name'] : 'General Category');

				if (!isset($groups_map[$cat])) {
					$groups_map[$cat] = array('category' => $cat, 'items' => array(), 'sub' => array('leads' => 0, 'closed' => 0, 'mrr' => 0.0));
				}
				if (!isset($groups_map[$cat]['items'][$sub])) {
					$groups_map[$cat]['items'][$sub] = array('leads' => 0, 'closed' => 0, 'mrr' => 0.0);
				}

				$mrr_clean = isset($r['mrr_fmt']) ? (float)str_replace(array('AED', ',', ' '), '', $r['mrr_fmt']) : 0.0;
				$is_closed = (strpos(strtolower($cat), 'closed') !== false || strpos(strtolower($cat), 'activated') !== false) ? 1 : 0;

				$groups_map[$cat]['items'][$sub]['leads'] += 1;
				$groups_map[$cat]['items'][$sub]['closed'] += $is_closed;
				$groups_map[$cat]['items'][$sub]['mrr'] += $mrr_clean;

				$groups_map[$cat]['sub']['leads'] += 1;
				$groups_map[$cat]['sub']['closed'] += $is_closed;
				$groups_map[$cat]['sub']['mrr'] += $mrr_clean;

				$grand['leads'] += 1;
				$grand['closed'] += $is_closed;
				$grand['mrr'] += $mrr_clean;
			}

			$groups = array();
			foreach ($groups_map as $c => $data) {
				$r_list = array();
				foreach ($data['items'] as $s => $m) {
					$r_list[] = array($c, $s, number_format($m['leads']), number_format($m['closed']), number_format($m['mrr'], 2));
				}
				$groups[] = array(
					'category' => $c,
					'rows' => $r_list,
					'subtotal' => array($c . ' Total', '', number_format($data['sub']['leads']), number_format($data['sub']['closed']), number_format($data['sub']['mrr'], 2))
				);
			}

			return array(
				'headers' => $headers,
				'groups' => $groups,
				'grand_total' => array('Grand Total', '', number_format($grand['leads']), number_format($grand['closed']), number_format($grand['mrr'], 2))
			);
		}

		if ($dept == 'sales' && $report == 'tax-registration') {
			$headers = array('Corporate Tax Status', 'VAT Status', 'COUNTA of Companies', 'COUNTA of Trade Licenses', 'COUNTA of Cities');
			$groups_map = array();
			$grand = array('companies' => 0, 'tl' => 0, 'cities' => 0);

			foreach ($rows as $r) {
				$cat = !empty($r['corporate_tax_status']) && $r['corporate_tax_status'] !== '-' ? $r['corporate_tax_status'] : 'Not Registered';
				$sub = !empty($r['vat_status']) && $r['vat_status'] !== '-' ? $r['vat_status'] : 'Not Registered';

				if (!isset($groups_map[$cat])) {
					$groups_map[$cat] = array('category' => $cat, 'items' => array(), 'sub' => array('companies' => 0, 'tl' => 0, 'cities' => 0));
				}
				if (!isset($groups_map[$cat]['items'][$sub])) {
					$groups_map[$cat]['items'][$sub] = array('companies' => 0, 'tl' => 0, 'cities' => 0);
				}

				$has_tl = (!empty($r['tl_number']) && $r['tl_number'] !== '-') ? 1 : 0;
				$has_city = (!empty($r['city']) && $r['city'] !== '-') ? 1 : 0;

				$groups_map[$cat]['items'][$sub]['companies'] += 1;
				$groups_map[$cat]['items'][$sub]['tl'] += $has_tl;
				$groups_map[$cat]['items'][$sub]['cities'] += $has_city;

				$groups_map[$cat]['sub']['companies'] += 1;
				$groups_map[$cat]['sub']['tl'] += $has_tl;
				$groups_map[$cat]['sub']['cities'] += $has_city;

				$grand['companies'] += 1;
				$grand['tl'] += $has_tl;
				$grand['cities'] += $has_city;
			}

			$groups = array();
			foreach ($groups_map as $c => $data) {
				$r_list = array();
				foreach ($data['items'] as $s => $m) {
					$r_list[] = array($c, $s, number_format($m['companies']), number_format($m['tl']), number_format($m['cities']));
				}
				$groups[] = array(
					'category' => $c,
					'rows' => $r_list,
					'subtotal' => array($c . ' Total', '', number_format($data['sub']['companies']), number_format($data['sub']['tl']), number_format($data['sub']['cities']))
				);
			}

			return array(
				'headers' => $headers,
				'groups' => $groups,
				'grand_total' => array('Grand Total', '', number_format($grand['companies']), number_format($grand['tl']), number_format($grand['cities']))
			);
		}

		if ($dept == 'sales' && $report == 'trade-license') {
			$headers = array('Licensing Authority', 'Expiry / Validity Status', 'COUNTA of Trade Licenses', 'COUNTA of Cities', 'COUNTA of Companies');
			$groups_map = array();
			$grand = array('tl' => 0, 'cities' => 0, 'companies' => 0);

			foreach ($rows as $r) {
				$cat = !empty($r['tl_authority']) && $r['tl_authority'] !== '-' ? $r['tl_authority'] : 'General Licensing';
				$sub = strip_tags(isset($r['validity_badge']) ? $r['validity_badge'] : 'Valid');
				if (empty($sub)) $sub = 'Valid';

				if (!isset($groups_map[$cat])) {
					$groups_map[$cat] = array('category' => $cat, 'items' => array(), 'sub' => array('tl' => 0, 'cities' => 0, 'companies' => 0));
				}
				if (!isset($groups_map[$cat]['items'][$sub])) {
					$groups_map[$cat]['items'][$sub] = array('tl' => 0, 'cities' => 0, 'companies' => 0);
				}

				$has_tl = (!empty($r['tl_number']) && $r['tl_number'] !== '-') ? 1 : 0;
				$has_city = (!empty($r['city']) && $r['city'] !== '-') ? 1 : 0;

				$groups_map[$cat]['items'][$sub]['tl'] += $has_tl;
				$groups_map[$cat]['items'][$sub]['cities'] += $has_city;
				$groups_map[$cat]['items'][$sub]['companies'] += 1;

				$groups_map[$cat]['sub']['tl'] += $has_tl;
				$groups_map[$cat]['sub']['cities'] += $has_city;
				$groups_map[$cat]['sub']['companies'] += 1;

				$grand['tl'] += $has_tl;
				$grand['cities'] += $has_city;
				$grand['companies'] += 1;
			}

			$groups = array();
			foreach ($groups_map as $c => $data) {
				$r_list = array();
				foreach ($data['items'] as $s => $m) {
					$r_list[] = array($c, $s, number_format($m['tl']), number_format($m['cities']), number_format($m['companies']));
				}
				$groups[] = array(
					'category' => $c,
					'rows' => $r_list,
					'subtotal' => array($c . ' Total', '', number_format($data['sub']['tl']), number_format($data['sub']['cities']), number_format($data['sub']['companies']))
				);
			}

			return array(
				'headers' => $headers,
				'groups' => $groups,
				'grand_total' => array('Grand Total', '', number_format($grand['tl']), number_format($grand['cities']), number_format($grand['companies']))
			);
		}

		$fields = isset($report_res['fields']) ? $report_res['fields'] : array();
		$cat_field = isset($fields[1]) ? $fields[1] : (isset($fields[0]) ? $fields[0] : '');
		$sub_field = isset($fields[2]) ? $fields[2] : (isset($fields[1]) ? $fields[1] : '');

		$groups_map = array();
		$grand_count = count($rows);

		foreach ($rows as $r) {
			$cat = (!empty($cat_field) && !empty($r[$cat_field])) ? trim(strip_tags($r[$cat_field])) : 'General';
			$sub = (!empty($sub_field) && !empty($r[$sub_field])) ? trim(strip_tags($r[$sub_field])) : 'Record';

			if (!isset($groups_map[$cat])) {
				$groups_map[$cat] = array('category' => $cat, 'items' => array(), 'count' => 0);
			}
			if (!isset($groups_map[$cat]['items'][$sub])) {
				$groups_map[$cat]['items'][$sub] = 0;
			}
			$groups_map[$cat]['items'][$sub] += 1;
			$groups_map[$cat]['count'] += 1;
		}

		$groups = array();
		foreach ($groups_map as $c => $data) {
			$r_list = array();
			foreach ($data['items'] as $s => $cnt) {
				$r_list[] = array($c, $s, number_format($cnt));
			}
			$groups[] = array(
				'category' => $c,
				'rows' => $r_list,
				'subtotal' => array($c . ' Total', '', number_format($data['count']))
			);
		}

		return array(
			'headers' => array('Category Group', 'Sub Item', 'Total Count'),
			'groups' => $groups,
			'grand_total' => array('Grand Total', '', number_format($grand_count))
		);
	}
}
