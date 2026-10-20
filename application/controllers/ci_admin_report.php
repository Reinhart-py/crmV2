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
        if ($this->session->userdata('manager_id') == '') {
            if ($url != '') {
                redirect($url);
            } else {
                redirect('/manager/login');
            }
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
	

	
   public function users()
	{
		//die;
		
        $data=array();
		$data['info']=array(
			'title'=>'Users Report',
			'page_heading'=>'Users Report'
		);
		$data['info2']=$this->if_not_login();
		$data['r_list'] =$this->comman_model->getAll(array('status'=>1),array('id','ASC'),'ci_roles');
		$data['c_list'] =$this->comman_model->getAll('',array('id','ASC'),'countries');
		$data['u_list'] =$this->comman_model->getAll('',array('id','ASC'),'ci_user');
		if(($this->input->server('REQUEST_METHOD')==='GET')&&($this->input->get('search')=='report'))
		{ 
					$d1='';
					$d2='';
					$role='';
					$manager='';
					$status='';
					$department='';
					$office='';
					
				
       				extract($_GET);
					
				    if(!empty($d1) && !empty($d2))
					{
					
					$d1=date("Y-m-d",strtotime($d1));
					$d2=date("Y-m-d",strtotime($d2));
					}
					
					$data['list']=$this->report_model->get_user_report($d1,$d2,$role,$manager,$status,$department,$office);
				//print_r($data['list']);
				}
		$this->load->view('manager/report/user_report',$data);
	}
	
	  public function leads()
	{
		die;
		
        $data=array();
		$data['info']=array(
			'title'=>'Leads Report',
			'page_heading'=>'Leads Report'
		);
		$data['info2']=$this->if_not_login();
		 $data['st_list']= $this->comman_model->getAll(array('active'=>1),'','ci_status');
			$data['so_list']= $this->comman_model->getAll(array('active'=>1),'','ci_sources'); 
		$data['c_list'] =$this->comman_model->getAll('',array('id','ASC'),'countries');
		$data['u_list'] =$this->comman_model->getAll('',array('id','ASC'),'ci_user');
		if(($this->input->server('REQUEST_METHOD')==='GET')&&($this->input->get('search')=='report'))
		{ 
					$d1='';
					$d2='';
					$f1='';
					$f2='';
					$category='';
					$created_by='';
					$status='';
					$s_cat='';
						
				
       				extract($_GET);
					
				    if(!empty($d1) && !empty($d2))
					{					
						$d1=date("Y-m-d",strtotime($d1));
						$d2=date("Y-m-d",strtotime($d2));
					}
					
					$data['list']=$this->report_model->get_lead_report($d1,$d2,$f1, $f2,$category,$created_by,$status,$s_cat);
				//print_r($data['list']);
				}
		$this->load->view('manager/report/lead_report',$data);
	}

	




	public function followup()
	{
		die;
        $data=array();
		$data['info']=array(
			'title'=>'Followup Report',
			'page_heading'=>'Followup Report'
		);
		$data['info2']=$this->if_not_login();
		
		if(($this->input->server('REQUEST_METHOD')==='GET')&&($this->input->get('search')=='report'))
		{ 
					$d1='';
					$d2='';
					$f1='';
					$f2='';
					$category='';
					$created_by='';
					$status='';
					$s_cat='';					
				
       				extract($_GET);					
				    if(!empty($d1) && !empty($d2))
					{					
						$d1=date("Y-m-d",strtotime($d1));
						$d2=date("Y-m-d",strtotime($d2));
					}					
					$data['list']=$this->report_model->get_followup_report($d1,$d2,$f1, $f2,$category,$created_by,$status,$s_cat);
				
				}
		$this->load->view('manager/report/followup_report',$data);
	}


	public function performanceReportDep()
	{
		
        $data=array();
		$data['info']=array(
			'title'=>'Performance Report',
			'page_heading'=>'Performance Report'
		);
		$data['info2']=$this->if_not_login();
		if(($this->input->server('REQUEST_METHOD')==='GET')&&($this->input->get('search')=='report'))
		{ 
			$d1='';
			$d2='';
			$department='';					
		
			extract($_GET);			
			if(!empty($d1) && !empty($d2))
			{
			$d1=date("Y-m-d",strtotime($d1));
			$d2=date("Y-m-d",strtotime($d2));
			}

			if($department==1){
				$department="Board of Director";
			}
			if($department==2){
				$department="Administration";
			}
			if($department==3){
				$department="Operations";
			}
			if($department==4){
				$department="IT";
			}
			if($department==5){
				$department="Human Resource";
			}
			if($department==6){
				$department="Back Office";
			}
			if($department==7){
				$department="Sales";
			}
			if($department==8){
				$department="Business Development";
			}
			if($department==9){
				$department="Field Staff";
			}
			if($department==10){
				$department="Other";
			}
		
			$data['list']=$this->report_model->getPerformanceReportByDepartment($d1,$d2,$department);
			//print_r($data['list']);
	    }

		$this->load->view('manager/report/performance_report',$data);
	}

	public function performanceReportIndividual()
	{
		
        $data=array();
		$data['info']=array(
			'title'=>'Performance Report',
			'page_heading'=>'Performance Report'
		);
		$data['info2']=$this->if_not_login();
		if(($this->input->server('REQUEST_METHOD')==='GET')&&($this->input->get('search')=='report'))
		{ 
			$d1='';
			$d2='';
			$manager_id='';
		
			extract($_GET);			
			if(!empty($d1) && !empty($d2))
			{
			$d1=date("Y-m-d",strtotime($d1));
			$d2=date("Y-m-d",strtotime($d2));
			}
			
			$data['list']=$this->report_model->getPerformanceReportByIndividual($d1,$d2,$manager_id);
			//print_r($data['list']);
	    }

		$this->load->view('manager/report/performance_individual',$data);
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

		$is_admin_or_support = ($m_role == 1 || $m_role == 4 || $manager_id == 1);

		$user_row = $this->db->select('name, department, user_type')->where('id', $manager_id)->get('ci_user')->row_array();
		$user_name = isset($user_row['name']) ? $user_row['name'] : '';
		$user_dept = !empty($user_row['department']) ? strtolower(trim($user_row['department'])) : '';

		$dept = strtolower(trim($dept));
		$report = strtolower(trim($report));

		if (empty($dept)) {
			$all_depts = array('sales', 'operations', 'support', 'hr', 'it');
			$visible_dept_slugs = array();

			if ($is_admin_or_support) {
				$visible_dept_slugs = $all_depts;
			} else {
				if (strpos($user_dept, 'operat') !== false) {
					$visible_dept_slugs = array('operations');
				} else if (strpos($user_dept, 'support') !== false || strpos($user_dept, 'back') !== false) {
					$visible_dept_slugs = array('support');
				} else if (strpos($user_dept, 'hr') !== false) {
					$visible_dept_slugs = array('hr');
				} else if (strpos($user_dept, 'it') !== false) {
					$visible_dept_slugs = array('it');
				} else {
					$visible_dept_slugs = array('sales');
				}
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
			$allowed = false;
			if (strpos($user_dept, 'operat') !== false && $dept == 'operations') {
				$allowed = true;
			} else if ((strpos($user_dept, 'support') !== false || strpos($user_dept, 'back') !== false) && $dept == 'support') {
				$allowed = true;
			} else if (strpos($user_dept, 'hr') !== false && $dept == 'hr') {
				$allowed = true;
			} else if (strpos($user_dept, 'it') !== false && $dept == 'it') {
				$allowed = true;
			} else if ($dept == 'sales') {
				$allowed = true;
			}
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

		$emp = '';
		if ($is_admin_or_support) {
			$emp = $this->input->get('employee_id');
			$emp_multi = $this->input->get('employee_ids');
			if (!empty($emp_multi)) {
				$emp = $emp_multi;
			}
		} else {
			$emp = $manager_id;
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
			'manager_id' => $manager_id,
			'is_admin_or_support' => $is_admin_or_support
		);

		$report_data = $this->admin_reports_model->getReportData($dept, $report, $filters);

		if ($this->input->get('export')) {
			if (!$is_admin_or_support) {
				show_error('Access Denied: Exporting reports is restricted to Administrators and Support.', 403);
				exit();
			}
		}

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
		$data['users'] = $this->comman_model->getAll(array('status' => 1), array('name', 'ASC'), 'ci_user');
		$data['statuses'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_status');
		$data['is_admin_or_support'] = $is_admin_or_support;
		$data['current_user_name'] = $user_name;
		$data['manager_id'] = $manager_id;
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

		$is_admin_or_support = ($m_role == 1 || $m_role == 4 || $manager_id == 1);

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
}