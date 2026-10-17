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

	public function downloadReport()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Download Report',
			'page_heading' => 'Download Report'
		);
		$data['info2'] = $this->if_not_login();
		$manager_id = (int)$this->session->userdata('manager_id');

		if (($this->input->server('REQUEST_METHOD') === 'GET') && ($this->input->get('search') === 'report')) {
			$d1 = '';
			$d2 = '';

			if ($this->input->get('d1') && $this->input->get('d2')) {
				$d1 = date("Y-m-d", strtotime($this->input->get('d1')));
				$d2 = date("Y-m-d", strtotime($this->input->get('d2')));
			}

			// Enforce strictly individual user's own data at the backend level
			$data['list'] = $this->report_model->getPerformanceReportForUser($d1, $d2, $manager_id);
		}

		$this->load->view('manager/report/download_report', $data);
	}

	public function adminReports($dept = '', $report = '')
	{
		$data = array();
		$data['info2'] = $this->if_not_login();

		if ($this->session->userdata('manager_role') != 1) {
			$this->session->set_flashdata('error_message', 'Access denied. Admin access required.');
			redirect('manager/dashboard');
			return;
		}

		$dept = strtolower(trim($dept));
		$report = strtolower(trim($report));

		if (empty($dept)) {
			$data['info'] = array(
				'title' => 'Admin Reports',
				'page_heading' => 'Admin Reports'
			);
			$data['departments'] = $this->admin_reports_model->getAdminReportsOverview();
			$this->load->view('manager/report/admin/index', $data);
			return;
		}

		$valid_depts = array('sales', 'operations', 'support', 'hr', 'it');
		if (!in_array($dept, $valid_depts)) {
			redirect('manager/admin-reports');
			return;
		}

		if (empty($report)) {
			$dept_meta = $this->admin_reports_model->getDepartmentReportsMeta($dept);
			if (empty($dept_meta)) {
				redirect('manager/admin-reports');
				return;
			}
			$data['info'] = array(
				'title' => $dept_meta['dept_title'] . ' Reports',
				'page_heading' => $dept_meta['dept_title'] . ' Reports'
			);
			$data['dept_meta'] = $dept_meta;
			$data['dept'] = $dept;
			$this->load->view('manager/report/admin/department', $data);
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

		if ($period == 'daily') {
			$d1 = date('Y-m-d');
			$d2 = date('Y-m-d');
		} else if ($period == 'weekly') {
			$d1 = date('Y-m-d', strtotime('-7 days'));
			$d2 = date('Y-m-d');
		} else if ($period == 'monthly') {
			$d1 = date('Y-m-01');
			$d2 = date('Y-m-t');
		} else {
			if ($this->input->get('d1') && $this->input->get('d2')) {
				$d1 = date('Y-m-d', strtotime($this->input->get('d1')));
				$d2 = date('Y-m-d', strtotime($this->input->get('d2')));
			}
		}

		$emp = $this->input->get('employee_id');
		$emp_multi = $this->input->get('employee_ids');
		if (!empty($emp_multi)) {
			$emp = $emp_multi;
		}

		$filters = array(
			'period' => $period,
			'd1' => $d1,
			'd2' => $d2,
			'employee_id' => $emp,
			'employee_ids' => is_array($emp) ? $emp : array(),
			'status' => $this->input->get('status'),
			'search' => $this->input->get('search'),
			'expiry' => $this->input->get('expiry'),
			'category' => $this->input->get('category'),
			'department' => $this->input->get('department'),
		);

		$report_data = $this->admin_reports_model->getReportData($dept, $report, $filters);

		if ($this->input->get('export') == 'csv') {
			$filename = 'admin_report_' . $dept . '_' . $report . '_' . date('Ymd_His') . '.csv';
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
		$data['info'] = array(
			'title' => $report_data['title'],
			'page_heading' => $report_data['title']
		);

		$this->load->view('manager/report/admin/view', $data);
	}
}