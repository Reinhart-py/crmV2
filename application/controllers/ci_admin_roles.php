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
  
	}
	
	
	public function if_not_login($url = '')
    {
        if ($this->session->userdata('manager_id') == '') {
            if ($url != '') {
                redirect($url);
            } else {
                redirect('/manager/login');
            }
        } elseif ($this->session->userdata('manager_role') != 1) {
        	$this->session->set_flashdata('error_message', 'Unauthorized Access!');
        		redirect('/manager/dashboard');

        }else {
            $data = array();
           // $data['logo'] = $this->ci_admin_model->getpostdata2(array('id'=>1),'','ci_logo');
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
						//print_r($_POST);
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
					 
		                 $save= array(	                 		
							'title'=>$title,
							'parent_id'=>$parent_id,
							'level'=> $level,
							'status'=>1,												
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
						//print_r($_POST);
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

		                 $save= array(	                 		
							'title'=>$title,
							'parent_id'=>$parent_id,
							'level'=> $level,							
							'status'=>$status					
													
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
								
		
   
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */