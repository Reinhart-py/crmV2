<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ci_admin_roles extends CI_Controller {
 function __construct() 
	{
    parent::__construct();
	$this->load->library(array('Session'));
	$this->load->helper(array('form','url','text'));
	$this->load->model(array('ci_admin_model','comman_model'));
 	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
 	header("Cache-Control: no-store, no-cache, must-revalidate"); 
 	header("Cache-Control: post-check=0, pre-check=0", false);
 	header("Pragma: no-cache");
 	header('Cache-Control: no-cache');

	$mgr_id = (int)$this->session->userdata('manager_id');
	$mgr_role = (int)$this->session->userdata('manager_role');
	if (empty($mgr_id)) {
		redirect('manager/login');
		exit;
	}
	if ($mgr_role !== 1 && $mgr_id !== 1 && !has_section_perm('roles')) {
		$this->session->set_flashdata('error_message', 'Access denied. Only Administrators can manage roles and permissions.');
		redirect('manager/dashboard');
		exit;
	}
	}
	
	public function if_not_login($url = '')
    {
        $mgr_id = (int)$this->session->userdata('manager_id');
        $mgr_role = (int)$this->session->userdata('manager_role');
        if (empty($mgr_id)) {
            if ($url != '') {
                redirect($url);
            } else {
                redirect('/manager/login');
            }
        } elseif ($mgr_role !== 1 && $mgr_id !== 1 && !has_section_perm('roles')) {
        	$this->session->set_flashdata('error_message', 'Access denied. Only Administrators can manage roles and permissions.');
        	redirect('/manager/dashboard');
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
	
 			public function index()
		    {
				
	  			$data=array();
				$data['info']=array(
					'title'=>'Manager- Roles',
					'page_heading'=>'All Roles'
				);
				$data['info2']=$this->if_not_login();
				$data['user_list'] =$this->comman_model->getAll(array("status"=>1),array('id','ASC'),'ci_roles');				
	           	$this->load->view('manager/roles/index',$data);
				
			}
    public function add()
		    {
				
	  $data=array();
		$data['info']=array(
			'title'=>'Manager- Roles',
			'page_heading'=>'Add Role'
			);
		$data['info2']=$this->if_not_login();
		$data['r_list'] =$this->comman_model->getAll(array('status'=>1),array('id','ASC'),'ci_roles');
		
		if($this->input->server('REQUEST_METHOD')==='POST')
		            {
		                extract($_POST);
					 $resp=$this->ci_admin_model->checkDuplicate($title,'title','ci_roles');
					 if($resp)
					 {
						  $this->session->set_flashdata('error_message', 'Role Name Already Exist...');
						   redirect("manager/roles/add");
					 }
					else
					 { 
						 	if($parent_id!=0){
						 	$data['role'] =$this->comman_model->getAll(array('id'=>$parent_id),'','ci_roles','first');
						 	$level= $data['role']['level']+1;
						 }else{
						 	$level=1;
						 }

						 $clean_perms = $this->_normalize_permissions($this->input->post('permissions'));
					 
		                 $save= array(	                 		
							'title'=>$title,
							'parent_id'=>$parent_id,
							'level'=> $level,
							'status'=>1,
							'permissions' => json_encode($clean_perms),
							'created' =>date('Y-m-d')							
							);
						  
			              $response= $this->comman_model->saveData('ci_roles',$save);
							if($response)
							{
							 $this->session->set_flashdata('message', 'New Role added successfully!');
						    	redirect('manager/roles');
							}
						}
					}
	                $this->load->view('manager/roles/add',$data);
				
			}
			
			 public function edit($id='')
		    {
	 	$data=array();
		$data['info']=array(
			'title'=>'Manager - Roles',
			'page_heading'=>'Edit Role'
			);
		$data['info2']=$this->if_not_login();
		$data['r_list'] =$this->comman_model->getAll(array('status'=>1),array('id','ASC'),'ci_roles');
		$data['role'] =$this->comman_model->getAll(array('id'=>$id),array('id','ASC'),'ci_roles','first');
		if(empty($data['role'])){
			$this->session->set_flashdata('error_message', 'Not allowed');
			redirect("manager/roles/");
		}		
			
		if($this->input->server('REQUEST_METHOD')==='POST')
		            {
		                extract($_POST);
						$chk=$this->ci_admin_model->checkDuplicate_other_row(array('id !='=>$id,'title'=>$title),'ci_roles');
		   if($chk)
			 {
				  $this->session->set_flashdata('error_message', 'Role Name Already Exist...');
				  redirect("manager/roles/edit/".$id);
			 }
			else
			 {
			 			if($parent_id!=0){
						 	$data['role'] =$this->comman_model->getAll(array('id'=>$parent_id),'','ci_roles','first');
						 	$level= $data['role']['level']+1;
						 }else{
						 	$level=1;
						 }

						 $clean_perms = $this->_normalize_permissions($this->input->post('permissions'));

		                 $save= array(	                 		
							'title'=>$title,
							'parent_id'=>$parent_id,
							'level'=> $level,							
							'status'=>$status,
							'permissions' => json_encode($clean_perms)
							);
						  
			              $response= $this->comman_model->updateData('id', $id,$save, 'ci_roles');
							if($response)
							{
							 $this->session->set_flashdata('message', 'Role updated successfully!');
						    	redirect('manager/roles');
							}
	                 }
					}
	                $this->load->view('manager/roles/edit',$data);
				
			}
			
			 public function delete($id='')
		    {
				
				$data=array();
				$data['info']=array(
					'title'=>'Manager- Roles',
					'page_heading'=>''
					);
				$data['info2']=$this->if_not_login();				
		                 $save= array(	                 		
							'status'=>0,
							);
			              $response= $this->comman_model->updateData('id', $id,$save, 'ci_roles');
							if($response)
							{
							 $this->ci_admin_model->log_admin_action('role_delete', 'Deleted role ID #'.$id, 'SUCCESS', $this->session->userdata('manager_id'), $this->session->userdata('manager_name'));
							 $this->session->set_flashdata('message', 'Role Removed successfully!');
						    	redirect('manager/roles');
							}
			}
								
		
   
	private function _normalize_permissions($raw_perms)
	{
		$clean = array();
		if (is_array($raw_perms)) {
			foreach ($raw_perms as $sec => $val) {
				$clean[$sec] = in_array($val, array('no_access', 'read', 'write')) ? $val : 'no_access';
			}
		}

		$derive = function($keys, $map) {
			$has_write = false;
			$has_read = false;
			foreach ($keys as $k) {
				if (isset($map[$k])) {
					if ($map[$k] === 'write') {
						$has_write = true;
					} elseif ($map[$k] === 'read') {
						$has_read = true;
					}
				}
			}
			if ($has_write) return 'write';
			if ($has_read) return 'read';
			return 'no_access';
		};

		$clean['my_zone'] = $derive(array('database', 'leads', 'orders', 'sales', 'followups', 'meetings', 'verticals', 'vertical_followups', 'internal_tasks', 'external_tasks', 'database_transfer'), $clean);
		$clean['team'] = $derive(array('team_members', 'team_chart', 'team_database', 'team_leads', 'team_orders', 'team_verticals', 'team_followups', 'team_meetings', 'team_assignmeetings', 'team_assignleads', 'team_tasks', 'team_tickets'), $clean);
		$clean['back_office'] = $derive(array('lead_review', 'orders_pending', 'kam_onboard', 'orders_management'), $clean);
		$clean['kam'] = $derive(array('kam_clients', 'kam_schedule', 'kam_task_followup', 'kam_reminders'), $clean);
		$clean['reports'] = $derive(array('reports_revenue', 'reports_projections', 'reports_download'), $clean);
		$clean['system'] = $derive(array('users', 'roles', 'terms', 'leads_import', 'leads_export', 'assigned_leads', 'admin_logs', 'calling_api', 'integrations'), $clean);

		return $clean;
	}
}