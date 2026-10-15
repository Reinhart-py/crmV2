<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ci_admin_report extends CI_Controller {
 function __construct() 
	{
    parent::__construct();
	$this->load->library(array('Session'));
	$this->load->helper(array('form','url','directory','text','date'));
	$this->load->model(array('comman_model','ci_admin_model','report_model'));
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

	public function adminReports()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Admin Reports',
			'page_heading' => 'Admin Reports'
		);
		$data['info2'] = $this->if_not_login();

		// Restricted to Admin
		if ($this->session->userdata('manager_role') != 1) {
			$this->session->set_flashdata('error_message', 'Access denied. Admin access required.');
			redirect('manager/dashboard');
			return;
		}

		$data['users'] = $this->comman_model->getAll(array('status' => 1), array('name', 'ASC'), 'ci_user');

		$d1 = '';
		$d2 = '';
		$dept_name = '';
		$period = $this->input->get('period');

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
				$d1 = date("Y-m-d", strtotime($this->input->get('d1')));
				$d2 = date("Y-m-d", strtotime($this->input->get('d2')));
			}
		}

		$department = $this->input->get('department');
		$dept_map = array(
			'2' => 'Administration',
			'3' => 'Operations',
			'4' => 'IT',
			'5' => 'Human Resource',
			'6' => 'Back Office',
			'7' => 'Sales',
			'8' => 'Business Development',
			'9' => 'Field Staff',
			'10' => 'Other'
		);
		if (!empty($department) && isset($dept_map[$department])) {
			$dept_name = $dept_map[$department];
		}

		if (($this->input->server('REQUEST_METHOD') === 'GET') && ($this->input->get('search') === 'report' || !empty($period) || !empty($department))) {
			$data['list'] = $this->report_model->getPerformanceReportByDepartment($d1, $d2, $dept_name);
		}

		$this->load->view('manager/report/admin_report', $data);
	}
	 
}
/* End of file ci_admin_report.php */
/* Location: ./application/controllers/ci_admin_report.php */