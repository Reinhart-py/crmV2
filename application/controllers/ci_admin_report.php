<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ci_admin_report extends CI_Controller {
 function __construct() 
	{
    parent::__construct();
	$this->load->library(array('Session'));
	$this->load->helper(array('form','url','directory','text','date'));
	$this->load->model(array('comman_model','ci_admin_model','report_model','admin_reports_model'));
	 header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
		 header("Cache-Control: no-store, no-cache, must-revalidate"); 
		 header("Cache-Control: post-check=0, pre-check=0", false);
		 header("Pragma: no-cache");
		 header('Cache-Control: no-cache');
  
	}
	public function if_not_login($url = '')
    {
        $mgr_id = (int)$this->session->userdata('manager_id');
        $mgr_role = (int)$this->session->userdata('manager_role');
        if ($mgr_id === 0) {
            if ($url != '') {
                redirect($url);
            } else {
                redirect('/manager/login');
            }
        } elseif ($mgr_role !== 1 && $mgr_id !== 1 && !has_section_perm('reports')) {
            $this->session->set_flashdata('error_message', 'Access denied. You do not have permission to view Reports.');
            redirect('manager/dashboard');
            exit;
        } else {
            $data = array();
            return $data;
        }
    }
	
    public function is_user_login($url = '')
    {
        if ($this->session->userdata('manager_id') != '') {
            if ($url != '') {
                redirect($url);
            } else {
               redirect('manager/dashboard');
            }
        }
    }
	

	
	/** One text filter from the address bar ('' when missing or not plain text). */
	private function _q($key)
	{
		$v = $this->input->get($key);
		return is_string($v) ? trim($v) : '';
	}

	/** Users the signed-in person may pick in report filters (admin: everyone; others: self + team). */
	private function _scope_users()
	{
		$scope = $this->report_model->scope_ids();
		$this->db->order_by('id', 'ASC');
		if ($scope !== null) $this->db->where_in('id', $scope ? $scope : array(-1));
		return $this->db->get('ci_user')->result_array();
	}

	public function users()
	{
		$data = array();
		$data['info'] = array('title' => 'Users Report', 'page_heading' => 'Users Report');
		$data['info2'] = $this->if_not_login();
		$data['r_list'] = $this->comman_model->getAll(array('status' => 1), array('id', 'ASC'), 'ci_roles');
		$data['c_list'] = $this->comman_model->getAll('', array('id', 'ASC'), 'countries');
		$data['u_list'] = $this->_scope_users();
		if ($this->input->get('search') == 'report') {
			$data['list'] = $this->report_model->get_user_report($this->_q('d1'), $this->_q('d2'), $this->_q('role'), $this->_q('manager'), $this->_q('status'), $this->_q('department'), $this->_q('office'));
		}
		$this->load->view('manager/report/user_report', $data);
	}

	/** Lead filters shared by the Leads and Follow-up reports. */
	private function _lead_filters()
	{
		return array($this->_q('d1'), $this->_q('d2'), $this->_q('f1'), $this->_q('f2'), $this->_q('category'), $this->_q('created_by'), $this->_q('status'), $this->_q('s_cat'));
	}

	public function leads()
	{
		$data = array();
		$data['info'] = array('title' => 'Leads Report', 'page_heading' => 'Leads Report');
		$data['info2'] = $this->if_not_login();
		$data['st_list'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_status');
		$data['so_list'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_sources');
		$data['c_list'] = $this->comman_model->getAll('', array('id', 'ASC'), 'countries');
		$data['u_list'] = $this->_scope_users();
		if ($this->input->get('search') == 'report') {
			list($d1, $d2, $f1, $f2, $category, $created_by, $status, $s_cat) = $this->_lead_filters();
			$data['list'] = $this->report_model->get_lead_report($d1, $d2, $f1, $f2, $category, $created_by, $status, $s_cat);
		}
		$this->load->view('manager/report/lead_report', $data);
	}

	public function followup()
	{
		$data = array();
		$data['info'] = array('title' => 'Followup Report', 'page_heading' => 'Followup Report');
		$data['info2'] = $this->if_not_login();
		if ($this->input->get('search') == 'report') {
			list($d1, $d2, $f1, $f2, $category, $created_by, $status, $s_cat) = $this->_lead_filters();
			$data['list'] = $this->report_model->get_followup_report($d1, $d2, $f1, $f2, $category, $created_by, $status, $s_cat);
		}
		$this->load->view('manager/report/followup_report', $data);
	}

	public function performanceReportDep()
	{
		$data = array();
		$data['info'] = array('title' => 'Performance Report', 'page_heading' => 'Performance Report');
		$data['info2'] = $this->if_not_login();
		if ($this->input->get('search') == 'report') {
			$names = array(1 => 'Board of Director', 2 => 'Administration', 3 => 'Operations', 4 => 'IT', 5 => 'Human Resource',
				6 => 'Back Office', 7 => 'Sales', 8 => 'Business Development', 9 => 'Field Staff', 10 => 'Other');
			$dep = (int)$this->_q('department');
			$department = isset($names[$dep]) ? $names[$dep] : '';
			$data['list'] = $this->report_model->getPerformanceReportByDepartment($this->_q('d1'), $this->_q('d2'), $department, $this->report_model->scope_ids());
		}
		$this->load->view('manager/report/performance_report', $data);
	}

	public function performanceReportIndividual()
	{
		$data = array();
		$data['info'] = array('title' => 'Performance Report', 'page_heading' => 'Performance Report');
		$data['info2'] = $this->if_not_login();
		if ($this->input->get('search') == 'report') {
			// Only yourself or someone under you (admin: anyone).
			$me = (int)$this->session->userdata('manager_id');
			$who = (int)$this->_q('manager_id');
			$scope = $this->report_model->scope_ids();
			if ($who <= 0 || ($scope !== null && !in_array($who, $scope, true))) $who = $me;
			$data['list'] = $this->report_model->getPerformanceReportByIndividual($this->_q('d1'), $this->_q('d2'), $who);
		}
		$this->load->view('manager/report/performance_individual', $data);
	}

	public function downloadReport($dept = '', $report = '')
	{
		$data = array();
		$data['info2'] = $this->if_not_login();

		$m_role = (int)$this->session->userdata('manager_role');
		$manager_id = (int)$this->session->userdata('manager_id');
		if (empty($m_role) && $manager_id == 1) {
			$m_role = 1;
		}

		$is_admin_or_support = ($m_role == 1 || crm_can('all_data') || $manager_id == 1);

		$user_row = $this->db->select('name, department, user_type')->where('id', $manager_id)->get('ci_user')->row_array();
		$user_name = isset($user_row['name']) ? $user_row['name'] : '';
		$user_dept = !empty($user_row['department']) ? strtolower(trim($user_row['department'])) : '';

		// The person's own report department, from their role (falls back to the department name).
		$role_depts = array(5 => 'operations', 2 => 'support', 4 => 'support', 7 => 'hr', 6 => 'it', 3 => 'sales');
		$own_dept = isset($role_depts[$m_role]) ? $role_depts[$m_role] : '';
		if ($own_dept === '') {
			if (strpos($user_dept, 'operat') !== false) $own_dept = 'operations';
			else if (strpos($user_dept, 'support') !== false || strpos($user_dept, 'back') !== false) $own_dept = 'support';
			else if ($user_dept === 'hr' || strpos($user_dept, 'human') !== false) $own_dept = 'hr';
			else if ($user_dept === 'it') $own_dept = 'it';
		}

		$dept = strtolower(trim($dept));
		$report = strtolower(trim($report));

		if (empty($dept)) {
			$all_depts = array('sales', 'operations', 'support', 'hr', 'it');
			$visible_dept_slugs = array();

			if ($is_admin_or_support) {
				$visible_dept_slugs = $all_depts;
			} else {
				// Sales is open to everyone; plus the person's own department.
				$visible_dept_slugs = array_values(array_unique(array_filter(array('sales', $own_dept))));
			}

			$sections = array();
			foreach ($visible_dept_slugs as $d_slug) {
				$meta = $this->admin_reports_model->getDepartmentReportsMeta($d_slug);
				if (!empty($meta)) {
					$sections[$d_slug] = $meta;
				}
			}

			$data['sections'] = $sections;
			$data['is_admin_or_support'] = $is_admin_or_support;
			$data['user_name'] = $user_name;
			$data['manager_id'] = $manager_id;
			$data['info'] = array(
				'title' => 'Reports',
				'page_heading' => 'Reports'
			);

			$this->load->view('manager/report/download_report', $data);
			return;
		}

		$valid_depts = array('sales', 'operations', 'support', 'hr', 'it');
		if (!in_array($dept, $valid_depts)) {
			redirect('manager/download-report');
			return;
		}

		if (!$is_admin_or_support) {
			$allowed = ($dept == 'sales' || $dept === $own_dept);
			if (!$allowed) {
				redirect('manager/download-report');
				return;
			}
		}

		if (empty($report)) {
			$dept_meta = $this->admin_reports_model->getDepartmentReportsMeta($dept);
			if (!empty($dept_meta['reports'])) {
				$first_report = key($dept_meta['reports']);
				redirect('manager/download-report/' . $dept . '/' . $first_report);
				return;
			}
			redirect('manager/download-report');
			return;
		}

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
		// Only reports listed for this department can be opened.
		$dept_meta = $this->admin_reports_model->getDepartmentReportsMeta($dept);
		if (empty($dept_meta['reports'][$report])) {
			redirect('manager/download-report/' . $dept);
			return;
		}

		$period = $this->input->get('period');
		$d1 = '';
		$d2 = '';

		if ($period == 'daily' || $period == 'today') {
			$d1 = date('Y-m-d');
			$d2 = date('Y-m-d');
		} else if ($period == 'weekly' || $period == '7days') {
			$d1 = date('Y-m-d', strtotime('-7 days'));
			$d2 = date('Y-m-d');
		} else if ($period == 'monthly' || $period == 'month') {
			$d1 = date('Y-m-01');
			$d2 = date('Y-m-t');
		} else if ($period == 'yearly' || $period == 'year') {
			$d1 = date('Y-01-01');
			$d2 = date('Y-12-31');
		} else if ($period == 'all') {
			$d1 = '';
			$d2 = '';
		} else {
			if ($this->input->get('d1') && $this->input->get('d2')) {
				$d1 = date('Y-m-d', strtotime($this->input->get('d1')));
				$d2 = date('Y-m-d', strtotime($this->input->get('d2')));
			}
		}

		$user_team_ids = array($manager_id);
		if (!$is_admin_or_support) {
			$subordinates = $this->ci_admin_model->fetchTeamList($manager_id);
			if (!empty($subordinates) && is_array($subordinates)) {
				$user_team_ids = array_unique(array_merge($user_team_ids, $subordinates));
			}
			$direct_reports = $this->db->select('id')->where('manager', $manager_id)->get('ci_user')->result_array();
			if (!empty($direct_reports)) {
				foreach ($direct_reports as $dr) {
					$user_team_ids[] = (int)$dr['id'];
				}
				$user_team_ids = array_unique($user_team_ids);
			}
		}

		$emp = '';
		if ($is_admin_or_support) {
			$emp = $this->input->get('employee_id');
			$emp_multi = $this->input->get('employee_ids');
			if (!empty($emp_multi)) {
				$emp = $emp_multi;
			}
		} else {
			$selected_emp = $this->input->get('employee_id');
			if (!empty($selected_emp) && in_array((int)$selected_emp, $user_team_ids)) {
				$emp = (int)$selected_emp;
			} else {
				$emp = $user_team_ids;
			}
		}

		$filters = array(
			'period' => $period,
			'd1' => $d1,
			'd2' => $d2,
			'employee_id' => $emp,
			'employee_ids' => is_array($emp) ? $emp : array($emp),
			'status' => $this->input->get('status'),
			'search' => $this->input->get('search'),
			'expiry' => $this->input->get('expiry'),
			'category' => $this->input->get('category'),
			'department' => $this->input->get('department'),
			'tl_emirate' => $this->input->get('tl_emirate'),
			'tl_zone' => $this->input->get('tl_zone'),
			'tl_authority' => $this->input->get('tl_authority'),
			'columns' => $this->input->get('columns'),
			'manager_id' => $manager_id,
			'user_team_ids' => $user_team_ids,
			'is_admin_or_support' => $is_admin_or_support
		);

		$report_data = $this->admin_reports_model->getReportData($dept, $report, $filters);

		if ($this->input->get('export') == 'pivot_excel' && !empty($report_data['pivot_data'])) {
			$filename = 'pivot_summary_' . $dept . '_' . $report . '_' . date('Ymd_His') . '.xls';
			header('Content-Type: application/vnd.ms-excel; charset=utf-8');
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			header('Cache-Control: max-age=0');
			echo '<!DOCTYPE html><html><head><meta charset="utf-8"></head><body>';
			echo '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; font-family: Arial, sans-serif; font-size: 13px;">';
			echo '<thead><tr style="background-color: #607d9b; color: #ffffff; font-weight: bold; text-align: left;">';
			foreach ($report_data['pivot_data']['headers'] as $h) {
				echo '<th style="background-color: #607d9b; color: #ffffff; padding: 8px 12px; border: 1px solid #4a637d;">' . htmlspecialchars($h) . '</th>';
			}
			echo '</tr></thead><tbody>';
			if (!empty($report_data['pivot_data']['groups'])) {
				foreach ($report_data['pivot_data']['groups'] as $grp) {
					if (!empty($grp['category'])) {
						$col_span = count($report_data['pivot_data']['headers']);
						echo '<tr style="background-color: #f1f5f9; font-weight: bold;"><td colspan="' . $col_span . '" style="padding: 6px 10px; border: 1px solid #cbd5e1;">' . htmlspecialchars($grp['category']) . '</td></tr>';
					}
					if (!empty($grp['rows'])) {
						foreach ($grp['rows'] as $r) {
							echo '<tr>';
							foreach ($r as $cell) {
								$val = is_array($cell) ? (isset($cell['val']) ? $cell['val'] : '') : $cell;
								$val_clean = trim(strip_tags(str_replace('&nbsp;', ' ', $val)));
								echo '<td style="padding: 6px 10px; border: 1px solid #e2e8f0;">' . htmlspecialchars($val_clean) . '</td>';
							}
							echo '</tr>';
						}
					}
					if (!empty($grp['subtotal'])) {
						echo '<tr style="background-color: #f8fafc; font-weight: 600;">';
						foreach ($grp['subtotal'] as $cell) {
							$val = is_array($cell) ? (isset($cell['val']) ? $cell['val'] : '') : $cell;
							$val_clean = trim(strip_tags(str_replace('&nbsp;', ' ', $val)));
							echo '<td style="padding: 6px 10px; border: 1px solid #cbd5e1;">' . htmlspecialchars($val_clean) . '</td>';
						}
						echo '</tr>';
					}
				}
			}
			if (!empty($report_data['pivot_data']['grand_total'])) {
				echo '<tr style="background-color: #e2e8f0; font-weight: bold;">';
				foreach ($report_data['pivot_data']['grand_total'] as $cell) {
					$val = is_array($cell) ? (isset($cell['val']) ? $cell['val'] : '') : $cell;
					$val_clean = trim(strip_tags(str_replace('&nbsp;', ' ', $val)));
					echo '<td style="padding: 8px 10px; border: 1px solid #94a3b8;">' . htmlspecialchars($val_clean) . '</td>';
				}
				echo '</tr>';
			}
			echo '</tbody></table></body></html>';
			exit();
		}

		if ($this->input->get('export') == 'pivot' && !empty($report_data['pivot_data'])) {
			$filename = 'pivot_sheet_' . $dept . '_' . $report . '_' . date('Ymd_His') . '.csv';
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			$out = fopen('php://output', 'w');
			fputcsv($out, $report_data['pivot_data']['headers']);
			if (!empty($report_data['pivot_data']['groups'])) {
				foreach ($report_data['pivot_data']['groups'] as $grp) {
					if (!empty($grp['category'])) {
						fputcsv($out, array('[-] ' . $grp['category']));
					}
					if (!empty($grp['rows'])) {
						foreach ($grp['rows'] as $r) {
							$clean_r = array();
							foreach ($r as $cell) {
								$clean_r[] = is_array($cell) ? (isset($cell['val']) ? $cell['val'] : '') : $cell;
							}
							fputcsv($out, $clean_r);
						}
					}
					if (!empty($grp['subtotal'])) {
						$clean_sub = array();
						foreach ($grp['subtotal'] as $cell) {
							$clean_sub[] = is_array($cell) ? (isset($cell['val']) ? $cell['val'] : '') : $cell;
						}
						fputcsv($out, $clean_sub);
					}
				}
			}
			if (!empty($report_data['pivot_data']['grand_total'])) {
				$clean_gt = array();
				foreach ($report_data['pivot_data']['grand_total'] as $cell) {
					$clean_gt[] = is_array($cell) ? (isset($cell['val']) ? $cell['val'] : '') : $cell;
				}
				fputcsv($out, $clean_gt);
			}
			fclose($out);
			exit();
		}

		if ($this->input->get('export') == 'excel') {
			if (!$is_admin_or_support) {
				show_error('Access Denied: Detailed Excel exports are restricted to Administrators.', 403);
				exit();
			}
			$filename = 'report_' . $dept . '_' . $report . '_' . date('Ymd_His') . '.xls';
			header('Content-Type: application/vnd.ms-excel; charset=utf-8');
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			header('Cache-Control: max-age=0');
			echo '<!DOCTYPE html><html><head><meta charset="utf-8"></head><body>';
			echo '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; font-family: Arial, sans-serif; font-size: 13px;">';
			echo '<thead><tr style="background-color: #607d9b; color: #ffffff; font-weight: bold; text-align: left;">';
			foreach ($report_data['headers'] as $h) {
				if ($h == 'Action') continue;
				echo '<th style="background-color: #607d9b; color: #ffffff; padding: 8px 12px; border: 1px solid #4a637d;">' . htmlspecialchars($h) . '</th>';
			}
			echo '</tr></thead><tbody>';
			foreach ($report_data['rows'] as $row) {
				echo '<tr>';
				foreach ($report_data['fields'] as $fld) {
					if ($fld == 'action') continue;
					$val = isset($row[$fld]) ? $row[$fld] : '';
					$val_clean = trim(strip_tags(str_replace('&nbsp;', ' ', $val)));
					echo '<td style="padding: 6px 10px; border: 1px solid #d1d5db;">' . htmlspecialchars($val_clean) . '</td>';
				}
				echo '</tr>';
			}
			echo '</tbody></table></body></html>';
			exit();
		}

		if ($this->input->get('export') == 'csv') {
			if (!$is_admin_or_support) {
				show_error('Access Denied: Detailed CSV exports are restricted to Administrators.', 403);
				exit();
			}
			$filename = 'report_' . $dept . '_' . $report . '_' . date('Ymd_His') . '.csv';
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			$out = fopen('php://output', 'w');
			fputcsv($out, $report_data['headers']);
			foreach ($report_data['rows'] as $row) {
				$row_csv = array();
				foreach ($report_data['fields'] as $fld) {
					$val = isset($row[$fld]) ? $row[$fld] : '';
					$row_csv[] = trim(strip_tags(str_replace('&nbsp;', ' ', $val)));
				}
				fputcsv($out, $row_csv);
			}
			fclose($out);
			exit();
		}

		$data['report_data'] = $report_data;
		$data['dept'] = $dept;
		$data['report'] = $report;
		$data['filters'] = $filters;
		if ($is_admin_or_support) {
			$data['users'] = $this->comman_model->getAll(array('status' => 1), array('name', 'ASC'), 'ci_user');
		} else {
			$this->db->select('id, name, email, department, manager');
			if (!empty($user_team_ids)) {
				$this->db->where_in('id', $user_team_ids);
			} else {
				$this->db->where('id', $manager_id);
			}
			$this->db->where('status', 1);
			$this->db->order_by('name', 'ASC');
			$data['users'] = $this->db->get('ci_user')->result_array();
		}
		$data['statuses'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_status');
		$data['is_admin_or_support'] = $is_admin_or_support;
		$data['current_user_name'] = $user_name;
		$data['manager_id'] = $manager_id;
		$data['user_team_ids'] = $user_team_ids;
		$data['info'] = array(
			'title' => $report_data['title'],
			'page_heading' => $report_data['title']
		);

		$this->load->view('manager/report/admin/view', $data);
	}

	public function drilldown()
	{
		$data = array();
		$data['info2'] = $this->if_not_login();

		$m_role = (int)$this->session->userdata('manager_role');
		$manager_id = (int)$this->session->userdata('manager_id');
		if (empty($m_role) && $manager_id == 1) {
			$m_role = 1;
		}

		$is_admin_or_support = ($m_role == 1 || crm_can('all_data') || $manager_id == 1);

		$user_row = $this->db->select('name, department, user_type')->where('id', $manager_id)->get('ci_user')->row_array();
		$user_name = isset($user_row['name']) ? $user_row['name'] : '';

		$dept = $this->input->get('dept');
		if (empty($dept)) {
			$dept = 'sales';
		}
		$report = $this->input->get('report');
		$metric = $this->input->get('metric');
		$uid = (int)$this->input->get('uid');
		$auth = $this->input->get('auth');
		$window = $this->input->get('window');
		$cat = $this->input->get('cat');
		$sub = $this->input->get('sub');
		$d1 = $this->input->get('d1');
		$d2 = $this->input->get('d2');

		$this->load->model('admin_reports_model');
		$drilldown_data = $this->admin_reports_model->getDrilldownRecords($dept, $report, $metric, $uid, $auth, $window, $cat, $sub, $d1, $d2, $manager_id, $is_admin_or_support);

		if ($this->input->get('export')) {
			if (!$is_admin_or_support) {
				show_error('Access Denied: Exporting records is restricted to Administrators and Support.', 403);
				exit();
			}
		}

		if ($this->input->get('export') == 'excel') {
			$filename = 'drilldown_' . (!empty($report) ? $report : 'report') . '_' . date('Ymd_His') . '.xls';
			header('Content-Type: application/vnd.ms-excel; charset=utf-8');
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			header('Cache-Control: max-age=0');
			echo '<!DOCTYPE html><html><head><meta charset="utf-8"></head><body>';
			echo '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; font-family: Arial, sans-serif; font-size: 13px;">';
			echo '<thead><tr style="background-color: #607d9b; color: #ffffff; font-weight: bold; text-align: left;">';
			foreach ($drilldown_data['headers'] as $h) {
				if ($h == 'Action') continue;
				echo '<th style="background-color: #607d9b; color: #ffffff; padding: 8px 12px; border: 1px solid #4a637d;">' . htmlspecialchars($h) . '</th>';
			}
			echo '</tr></thead><tbody>';
			foreach ($drilldown_data['rows'] as $row) {
				echo '<tr>';
				foreach ($drilldown_data['fields'] as $fld) {
					if ($fld == 'action') continue;
					$val = isset($row[$fld]) ? $row[$fld] : '';
					$val_clean = trim(strip_tags(str_replace('&nbsp;', ' ', $val)));
					echo '<td style="padding: 6px 10px; border: 1px solid #d1d5db;">' . htmlspecialchars($val_clean) . '</td>';
				}
				echo '</tr>';
			}
			echo '</tbody></table></body></html>';
			exit();
		}

		if ($this->input->get('export') == 'csv') {
			$filename = 'drilldown_' . (!empty($report) ? $report : 'report') . '_' . date('Ymd_His') . '.csv';
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			$out = fopen('php://output', 'w');
			$csv_headers = array();
			foreach ($drilldown_data['headers'] as $h) {
				if ($h == 'Action') continue;
				$csv_headers[] = $h;
			}
			fputcsv($out, $csv_headers);
			foreach ($drilldown_data['rows'] as $row) {
				$row_csv = array();
				foreach ($drilldown_data['fields'] as $fld) {
					if ($fld == 'action') continue;
					$val = isset($row[$fld]) ? $row[$fld] : '';
					$row_csv[] = trim(strip_tags(str_replace('&nbsp;', ' ', $val)));
				}
				fputcsv($out, $row_csv);
			}
			fclose($out);
			exit();
		}

		$data['drilldown_data'] = $drilldown_data;
		$data['dept'] = $dept;
		$data['report'] = $report;
		$data['metric'] = $metric;
		$data['uid'] = $uid;
		$data['auth'] = $auth;
		$data['window'] = $window;
		$data['cat'] = $cat;
		$data['sub'] = $sub;
		$data['d1'] = $d1;
		$data['d2'] = $d2;
		$data['is_admin_or_support'] = $is_admin_or_support;
		$data['current_user_name'] = $user_name;
		$data['manager_id'] = $manager_id;
		$data['info'] = array(
			'title' => $drilldown_data['title'],
			'page_heading' => $drilldown_data['title']
		);

		$this->load->view('manager/report/admin/drilldown', $data);
	}

	public function adminReports($dept = '', $report = '')
	{
		if (!empty($dept) && !empty($report)) {
			redirect('manager/download-report/' . $dept . '/' . $report);
		} else if (!empty($dept)) {
			redirect('manager/download-report/' . $dept);
		} else {
			redirect('manager/download-report');
		}
	}

	/**
	 * Sales & Activity reports. Everyone sees their own numbers, team leaders also their team,
	 * admins everyone (and the Centers tab).
	 */
	public function insights($tab = '')
	{
		$this->if_not_login();
		$me = (int)$this->session->userdata('manager_id');
		$scope = $this->report_model->scope_ids();
		$is_admin = ($scope === null);

		$tabs = array(
			'sales-target' => 'Sales vs Target',
			'calls' => 'Call Activity',
			'funnel' => 'Lead Funnel',
			'services' => 'Revenue by Service',
			'centers' => 'Centers',
			'overdue' => 'Overdue Follow-ups',
		);
		if (!$is_admin) unset($tabs['centers']);
		if (!isset($tabs[$tab])) $tab = 'sales-target';

		$month = $this->_q('month');
		if (!preg_match('/^\d{4}-\d{2}$/', $month)) $month = date('Y-m');
		$d1 = $this->_q('d1'); $d2 = $this->_q('d2');
		$d1 = ($d1 !== '' && strtotime($d1)) ? date('Y-m-d', strtotime($d1)) : date('Y-m-01');
		$d2 = ($d2 !== '' && strtotime($d2)) ? date('Y-m-d', strtotime($d2)) : date('Y-m-d');

		$all = $this->report_model->insight_people();
		$person = (int)$this->_q('person');
		if (!isset($all[$person])) $person = 0;
		$people = $person ? array($person => $all[$person]) : $all;

		$rows = array();
		switch ($tab) {
			case 'sales-target': $rows = $this->report_model->insight_sales_target($people, $month); break;
			case 'calls': $rows = $this->report_model->insight_calls($people, $d1, $d2); break;
			case 'funnel': $rows = $this->report_model->insight_funnel($people, $d1, $d2); break;
			case 'services': $rows = $this->report_model->insight_services($people, $d1, $d2); break;
			case 'centers': $rows = $this->report_model->insight_centers($people, $month); break;
			case 'overdue': $rows = $this->report_model->insight_overdue($people); break;
		}

		$data = array();
		$data['info'] = array('title' => 'Sales & Activity', 'page_heading' => 'Sales & Activity');
		$data['tabs'] = $tabs;
		$data['tab'] = $tab;
		$data['rows'] = $rows;
		$data['month'] = $month;
		$data['d1'] = $d1;
		$data['d2'] = $d2;
		$data['person'] = $person;
		$data['people'] = $all;
		$data['is_admin'] = $is_admin;
		$data['scope_label'] = $is_admin ? 'Everyone' : (count($all) > 1 ? 'You and your team' : 'Only you');
		$data['groups'] = $this->report_model->insight_status_groups();
		$this->load->view('manager/report/insights', $data);
	}

	public function revenue_list()
	{
		$this->if_not_login();
		$manager_id = (int)$this->session->userdata('manager_id');
		$manager_role = (int)$this->session->userdata('manager_role');

		$data = array();
		$data['info'] = array(
			'title' => 'Revenue List & Projections',
			'page_heading' => 'Revenue List'
		);

		$d1 = trim($this->input->get('d1'));
		$d2 = trim($this->input->get('d2'));
		$client = trim($this->input->get('client'));
		$filter_type = trim($this->input->get('type'));
		$user_id = (int)$this->input->get('user_id');
		// Only yourself or someone under you can be picked (All company data: anyone).
		if ($user_id > 0 && !crm_can('all_data') && !in_array($user_id, $this->ci_admin_model->get_accessible_user_ids($manager_id), true)) $user_id = 0;

		$orders_data = array();
		if ($filter_type !== 'projected') {
			$this->db->select('O.*, C.customer as lead_customer, C.poc as lead_poc, Staff.name as assigned_staff_name');
			$this->db->from('ci_orders as O');
			$this->db->join('ci_leads as C', 'C.id = O.lead_id', 'left');
			$this->db->join('ci_user as Staff', 'Staff.id = O.assigned_to', 'left');
			if (!empty($d1) && !empty($d2)) {
				$this->db->where('DATE(O.created_at) >=', date('Y-m-d', strtotime($d1)));
				$this->db->where('DATE(O.created_at) <=', date('Y-m-d', strtotime($d2)));
			}
			if (!empty($client)) {
				$this->db->like('O.client_name', $client);
			}
			if ($user_id > 0) {
				$this->db->where('O.created_by', $user_id);
			} elseif (!crm_can('all_data')) {
				$accessible = $this->ci_admin_model->get_accessible_user_ids($manager_id);
				if (!empty($accessible)) {
					$this->db->where_in('O.created_by', $accessible);
				} else {
					$this->db->where('O.created_by', $manager_id);
				}
			}
			$this->db->order_by('O.id', 'DESC');
			$orders_data = $this->db->get()->result_array();
		}

		$leads_data = array();
		if ($filter_type !== 'confirmed') {
			$this->db->select('L.*, S.status as status_title, U.name as owner_name, P.pipeline_stage, P.lead_ref_no');
			$this->db->from('ci_leads as L');
			$this->db->join('ci_pipeline as P', 'P.lead_id = L.id', 'left');
			$this->db->join('ci_status as S', 'S.id = L.status', 'left');
			$this->db->join('ci_user as U', 'U.id = L.lead_assign', 'left');
			$this->db->where(crm_stage_lead_sql('L', 'P'), NULL, FALSE);
			if (!empty($d1) && !empty($d2)) {
				$this->db->where('DATE(L.created) >=', date('Y-m-d', strtotime($d1)));
				$this->db->where('DATE(L.created) <=', date('Y-m-d', strtotime($d2)));
			}
			if (!empty($client)) {
				$this->db->like('L.customer', $client);
			}
			if ($user_id > 0) {
				$this->db->where('L.lead_assign', $user_id);
			} elseif (!crm_can('all_data')) {
				$accessible = $this->ci_admin_model->get_accessible_user_ids($manager_id);
				if (!empty($accessible)) {
					$this->db->where_in('L.lead_assign', $accessible);
				} else {
					$this->db->where('L.lead_assign', $manager_id);
				}
			}
			$this->db->order_by('L.id', 'DESC');
			$leads_data = $this->db->get()->result_array();
		}

		$total_confirmed_mrr = 0;
		$total_confirmed_otc = 0;
		$total_projected_mrr = 0;
		$total_projected_revenue = 0;

		foreach ($orders_data as $ord) {
			$total_confirmed_mrr += floatval($ord['mrr'] ?: $ord['mrc']);
			$total_confirmed_otc += floatval($ord['otc']);
		}

		foreach ($leads_data as $ld) {
			$lmrr = floatval($ld['mrr'] ?: $ld['mrc']);
			$lrev = floatval($ld['revenue'] ?: $ld['r_amount']);
			$total_projected_mrr += $lmrr;
			$total_projected_revenue += ($lrev > 0) ? $lrev : ($lmrr * 12);
		}

		$data['orders'] = $orders_data;
		$data['leads'] = $leads_data;
		$data['total_confirmed_mrr'] = $total_confirmed_mrr;
		$data['total_confirmed_otc'] = $total_confirmed_otc;
		$data['total_projected_mrr'] = $total_projected_mrr;
		$data['total_projected_revenue'] = $total_projected_revenue;
		$data['filter_d1'] = $d1;
		$data['filter_d2'] = $d2;
		$data['filter_client'] = $client;
		$data['filter_type'] = $filter_type;
		$data['filter_user'] = $user_id;
		$data['users_list'] = $this->db->select('id, name, email')->where('status', 1)->order_by('name', 'ASC')->get('ci_user')->result_array();

		$this->load->view('manager/report/revenue_list', $data);
	}

	public function company_projections()
	{
		$this->if_not_login();
		$manager_id = (int)$this->session->userdata('manager_id');
		$manager_role = (int)$this->session->userdata('manager_role');

		$data = array();
		$data['info'] = array(
			'title' => 'Company Projections',
			'page_heading' => 'Company Projections'
		);

		$d1 = trim($this->input->get('d1'));
		$d2 = trim($this->input->get('d2'));
		$search = trim($this->input->get('search'));
		$user_id = (int)$this->input->get('user_id');
		// Only yourself or someone under you can be picked (All company data: anyone).
		if ($user_id > 0 && !crm_can('all_data') && !in_array($user_id, $this->ci_admin_model->get_accessible_user_ids($manager_id), true)) $user_id = 0;

		$this->db->select('L.*, S.status as status_title, U.name as owner_name, P.pipeline_stage, P.lead_ref_no');
		$this->db->from('ci_leads as L');
		$this->db->join('ci_pipeline as P', 'P.lead_id = L.id', 'left');
		$this->db->join('ci_status as S', 'S.id = L.status', 'left');
		$this->db->join('ci_user as U', 'U.id = L.lead_assign', 'left');
		$this->db->where(crm_stage_lead_sql('L', 'P'), NULL, FALSE);
		$this->db->where('L.customer IS NOT NULL AND L.customer != ""');

		if (!empty($d1) && !empty($d2)) {
			$this->db->where('DATE(L.created) >=', date('Y-m-d', strtotime($d1)));
			$this->db->where('DATE(L.created) <=', date('Y-m-d', strtotime($d2)));
		}
		if (!empty($search)) {
			$_safe = $this->db->escape_like_str($search);
			$this->db->where("(L.customer LIKE '%$_safe%' OR L.poc LIKE '%$_safe%' OR L.category LIKE '%$_safe%')", null, false);
		}
		if ($user_id > 0) {
			$this->db->where('L.lead_assign', $user_id);
		} elseif (!crm_can('all_data')) {
			$accessible = $this->ci_admin_model->get_accessible_user_ids($manager_id);
			if (!empty($accessible)) {
				$this->db->where_in('L.lead_assign', $accessible);
			} else {
				$this->db->where('L.lead_assign', $manager_id);
			}
		}

		$this->db->order_by('L.customer', 'ASC');
		$raw_leads = $this->db->get()->result_array();

		$companies = array();
		$total_pipeline_mrr = 0;
		$total_pipeline_rev = 0;

		foreach ($raw_leads as $row) {
			$cname = trim($row['customer']);
			if (!isset($companies[$cname])) {
				$companies[$cname] = array(
					'customer' => $cname,
					'poc' => $row['poc'],
					'contact' => $row['contact1'],
					'email' => $row['email'],
					'deal_count' => 0,
					'total_mrr' => 0,
					'total_revenue' => 0,
					'categories' => array(),
					'nearest_close' => '',
					'owners' => array(),
					'leads' => array()
				);
			}

			$lmrr = floatval($row['mrr'] ?: $row['mrc']);
			$lrev = floatval($row['revenue'] ?: $row['r_amount']);
			if ($lrev <= 0) {
				$lrev = $lmrr * 12;
			}

			$companies[$cname]['deal_count']++;
			$companies[$cname]['total_mrr'] += $lmrr;
			$companies[$cname]['total_revenue'] += $lrev;
			$total_pipeline_mrr += $lmrr;
			$total_pipeline_rev += $lrev;

			if (!empty($row['category']) && !in_array($row['category'], $companies[$cname]['categories'])) {
				$companies[$cname]['categories'][] = $row['category'];
			}
			if (!empty($row['owner_name']) && !in_array($row['owner_name'], $companies[$cname]['owners'])) {
				$companies[$cname]['owners'][] = $row['owner_name'];
			}
			if (!empty($row['close_date'])) {
				if (empty($companies[$cname]['nearest_close']) || strtotime($row['close_date']) < strtotime($companies[$cname]['nearest_close'])) {
					$companies[$cname]['nearest_close'] = $row['close_date'];
				}
			}

			$companies[$cname]['leads'][] = $row;
		}

		$data['companies'] = array_values($companies);
		$data['total_companies'] = count($companies);
		$data['total_deals'] = count($raw_leads);
		$data['total_pipeline_mrr'] = $total_pipeline_mrr;
		$data['total_pipeline_rev'] = $total_pipeline_rev;
		$data['filter_d1'] = $d1;
		$data['filter_d2'] = $d2;
		$data['filter_search'] = $search;
		$data['filter_user'] = $user_id;
		$data['users_list'] = $this->db->select('id, name, email')->where('status', 1)->order_by('name', 'ASC')->get('ci_user')->result_array();

		$this->load->view('manager/report/company_projections', $data);
	}
}