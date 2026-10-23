<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Ci_admin_user extends CI_Controller {
 function __construct() 
	{
    parent::__construct();
	$this->load->library(array('Session','Pagination'));
	$this->load->helper(array('form','url','text','date'));
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
			'title'=>'Users',
			'page_heading'=>'All Users'
		);
		
		$data['info2']=$this->if_not_login();
		
		$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'A.id';
		$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'DESC';
		$search = isset($_GET['search']) ? $_GET['search'] : '';		
		$data['product_list2']= $this->ci_admin_model->get_users('','',$order_by,$sort_by,$search); 
		
		
		$params = array();
        $limit_per_page = 2000;
        $page = ($this->uri->segment(3)) ? ($this->uri->segment(3) - 1) : 0;
		 $page2 = ($this->uri->segment(3)) ? $this->uri->segment(3): 0;
        $total_records = count($data['product_list2']);
		$page_index= $page*$limit_per_page;
		
		if ($total_records > 0) 
        {
            
         $data['product_list']= $this->ci_admin_model->get_users($limit_per_page, $page_index,$order_by,$sort_by,$search);
		 	$con_q= '';
			 if(!empty($_SERVER['QUERY_STRING']))
			 {
				$con_q.='?'.$_SERVER['QUERY_STRING'];
			 }		
		 
			$this->session->set_userdata('manager_return_string', urlencode($page2.$con_q));
			$config['suffix'] = $con_q;
            $config['base_url'] = base_url() . 'manager/users';
            $config['total_rows'] = $total_records;
            $config['per_page'] = $limit_per_page;
            $config["uri_segment"] = 3;
			
			 
            $config['num_links'] = 2;
            $config['use_page_numbers'] = TRUE;
            $config['reuse_query_string'] = TRUE;
             
            $config['full_tag_open'] = '<ul class="pagination">';
            $config['full_tag_close'] = '</ul>';
             
            $config['first_link'] = 'First';
            $config['first_tag_open'] = '<li class="firstlink">';
            $config['first_tag_close'] = '</li>';
             
            $config['last_link'] = 'Last';
            $config['last_tag_open'] = '<li class="lastlink">';
            $config['last_tag_close'] = '</li>';
             
            $config['next_link'] = 'Next';
            $config['next_tag_open'] = '<li class="nextlink">';
            $config['next_tag_close'] = '</li>';
 
            $config['prev_link'] = 'Prev';
            $config['prev_tag_open'] = '<li class="prevlink">';
            $config['prev_tag_close'] = '</li>';
 
            $config['cur_tag_open'] = '<li class="curlink"><span>';
            $config['cur_tag_close'] = '</span></li>';
 
            $config['num_tag_open'] = '<li class="numlink">';
            $config['num_tag_close'] = '</li>';
             
            $this->pagination->initialize($config);
             
            
            $data["links"] = $this->pagination->create_links();
        }
	
		
		$this->load->view('manager/user/userlist',$data);
	}

	
public function view($id='') 

		{

			 $data=array();
				$data['info']=array(
					'title'=>'Manager - Users',
					'page_heading'=>'Preview User Information'
				);
			$data['info2']=$this->if_not_login();
			
			$data['p_info']=$this->ci_admin_model->getSingleUser($id);
			
			
			
			
			
    	 	   if(empty($data['p_info']))

			   {
			   		$this->session->set_flashdata('error_message', 'Not allowed');
				  	redirect('manager/users');
	           } 
	           $this->load->view('manager/user/user_view',$data);

}
	

	public function add()

	{


         $data=array();

				$data['info']=array(
					'title'=>'Manager- Users',
					'page_heading'=>'Add User'
				);
		$data['info2']=$this->if_not_login();
		$data['r_list'] =$this->comman_model->getAll(array('status'=>1),array('id','ASC'),'ci_roles');
		$data['c_list'] =$this->comman_model->getAll('',array('id','ASC'),'countries');
		$data['u_list'] =$this->comman_model->getAll('',array('id','ASC'),'ci_user');
		if($this->input->server('REQUEST_METHOD')==='POST')

		{

			extract($_POST);
			$data['role'] =$this->comman_model->getAll(array('id'=>$user_type),'','ci_roles','first');
			$data['levels'] =$this->comman_model->getAll(array('id'=>$manager),'','ci_user','first');
			$list= array(

			'name'=>$name,
			'gender' => $gender,
			'dob'=>db_date($dob),     		
			'email'=>$email,
			'mobile' =>$mobile,			
			'uae_mobile'=>$uae_mobile,
			'country_code'=>$country_code,
			'country_code2'=>$country_code2,
			'address'=>$address,
			'city'=>$city,
			'country'=>$country,
			'state'=>$state,

			'c_username'=>$c_username,
			'c_password'=>sha1($c_password),

			'emp_id'=>$emp_id,			
			'c_salary'=>$c_salary,
			'c_doj'=>db_date($c_doj),
			'c_dol'=>db_date($c_dol),
			'department'=>$department,
			'office'=>$office,



			'national'=>$national,
			'blood'=>$blood,
			'p_no'=>$p_no,
			
			'p_exdate'=>db_date($p_exdate),
			'visa_type'=>$visa_type,
			'visa_no'=>$visa_no,
			'visa_by'=>$visa_by,
			'visa_expiry'=>db_date($visa_expiry),
			'eid_no'=>$eid_no,
			'eid_expiry'=>db_date($eid_expiry),
			'h_contact_name'=>$h_contact_name,
			'h_contact_relation'=>$h_contact_relation,
			'h_contact_no'=>$h_contact_no,
			'h_contact_no2'=>$h_contact_no2,			
			
			'bank'=>$bank,
			'bank_country'=>$bank_country,
			'iban'=>$iban,
			'bank_no'=>$bank_no,
			'ifsc'=>$ifsc,
			'swift'=>$swift,


			'status'=>1,
			'admin_status'=>$admin_status,
			'user_type'=>$user_type,
			'user_level'=>$data['levels']['user_level']+1,
			'manager'=>$manager,			
			'created'=>date('Y-m-d')

			);
			 
			  $a1=$this->ci_admin_model->checkDuplicateUsername($c_username);
		     if($a1==FALSE){			 

			 	$this->comman_model->saveData('ci_user',$list);
			 	$this->session->set_flashdata('message', 'User has been added successfully!');
				redirect('manager/users');
			 }
			 else
			 {
			      $error = "Company Username is already exists.";
                  $this->session->set_flashdata('error_message',$error);

			 }
    	}   

        $this->load->view('manager/user/adduser',$data);
    }

	
		public function edit($id='') 

		{

			 $data=array();
				$data['info']=array(
					'title'=>'Manager - Users',
					'page_heading'=>'Edit User'
				);
			$data['info2']=$this->if_not_login();
			$data['r_list'] =$this->comman_model->getAll(array('status'=>1),array('id','ASC'),'ci_roles');
			$data['c_list'] =$this->comman_model->getAll('',array('id','ASC'),'countries');
			$data['u_list'] =$this->comman_model->getAll('',array('id','ASC'),'ci_user');
			$data['list']=$this->comman_model->getAll(array('id'=>$id),'','ci_user','first');
			if(!empty($data['list']['country'])){
				$data['state_list']= $this->comman_model->getAll(array('country_id'=>$data['list']['country']),'','states');
			}
    	 	   if(empty($data['list']))

			   {
			   		$this->session->set_flashdata('error_message', 'Not allowed');
				  	redirect('manager/users');
	           }    

			 if($this->input->server('REQUEST_METHOD')==='POST')
		     {
			   extract($_POST);
			   $data['role'] =$this->comman_model->getAll(array('id'=>$user_type),'','ci_roles','first');
			   $data['levels'] =$this->comman_model->getAll(array('id'=>$manager),'','ci_user','first');
				$save= array(		

			'name'=>$name,
			'gender' => $gender,
			'dob'=>db_date($dob),     		
			'email'=>$email,
			'mobile' =>$mobile,			
			'uae_mobile'=>$uae_mobile,
			'country_code'=>$country_code,
			'country_code2'=>$country_code2,
			'address'=>$address,
			'city'=>$city,
			'country'=>$country,
			'state'=>$state,
			

			'emp_id'=>$emp_id,			
			'c_salary'=>$c_salary,
			'c_doj'=>db_date($c_doj),
			'c_dol'=>db_date($c_dol),
			'department'=>$department,
			'office'=>$office,



			'national'=>$national,
			'blood'=>$blood,
			'p_no'=>$p_no,
			
			'p_exdate'=>db_date($p_exdate),
			'visa_type'=>$visa_type,
			'visa_no'=>$visa_no,
			'visa_by'=>$visa_by,
			'visa_expiry'=>db_date($visa_expiry),
			'eid_no'=>$eid_no,
			'eid_expiry'=>db_date($eid_expiry),
			'h_contact_name'=>$h_contact_name,
			'h_contact_relation'=>$h_contact_relation,
			'h_contact_no'=>$h_contact_no,
			'h_contact_no2'=>$h_contact_no2,			
			
			'bank'=>$bank,
			'bank_country'=>$bank_country,
			'iban'=>$iban,
			'bank_no'=>$bank_no,
			'ifsc'=>$ifsc,
			'swift'=>$swift,

			'status'=>1,
			'admin_status'=>$admin_status,
			'user_type'=>$user_type,
			'user_level'=>$data['levels']['user_level']+1,
			'manager'=>$manager,			
			'modified'=>date('Y-m-d')
			
				);			

				$response= $this->comman_model->updateData('id',$id,$save,'ci_user');
				if($response)
					{
						$this->ci_admin_model->log_admin_action('user_edit', 'Updated user ID #'.$id, 'SUCCESS', $this->session->userdata('manager_id'), $this->session->userdata('manager_name'));
						$this->session->set_flashdata('message', 'User has been updated successfully!');
						redirect('manager/users');
					}			

			}   

    	 $this->load->view('manager/user/useredit', $data);

	  }
	  

	  public function cedit($id='') 

		{

			 $data=array();
				$data['info']=array(
					'title'=>'Manager - Users',
					'page_heading'=>'Edit User'
				);
			$data['info2']=$this->if_not_login();			
			$data['list']=$this->comman_model->getAll(array('id'=>$id),'','ci_user','first');
			if(empty($data['list'])){
			   		$this->session->set_flashdata('error_message', 'Not allowed');
				  	redirect('manager/users');
			}
			if($this->input->server('REQUEST_METHOD')==='POST')
		     {
			   extract($_POST);	
			 
			   if(isset($submit) && ($submit=='phase1')){	
				$data['list']=$this->comman_model->getAll(array('c_username'=>$c_username),'','ci_user');
				if(empty($data['list'])){

					$save= array(
						'c_username'=>$c_username,							
						'modified'=>date('Y-m-d')
					);
					$response= $this->comman_model->updateData('id',$id,$save,'ci_user');

					$this->session->set_flashdata('message', 'User settings has been updated successfully!');
					redirect('manager/users');

				}else{

					$this->session->set_flashdata('error_message', 'Sorry! Username already exist');
					redirect('manager/users/cedit/'.$id);

				}
				
			}else{
				$save= array(
					
					'c_password'=>sha1($password),		
					'modified'=>date('Y-m-d')
				);

				$response= $this->comman_model->updateData('id',$id,$save,'ci_user');
				$this->session->set_flashdata('message', 'User settings has been updated successfully!');
				redirect('manager/users/cedit/'.$id);

			}
				
				
			}  
    	 $this->load->view('manager/user/usercedit', $data);
      }


	  



	public function delete($id='')
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Manager - Users',
			'page_heading' => 'All Users'
		);
		$data['info2'] = $this->if_not_login();

		$id = (int)$id;
		if (empty($id)) {
			redirect('manager/users');
			return;
		}

		$current_admin_id = (int)$this->session->userdata('manager_id');
		if ($id === $current_admin_id) {
			$this->session->set_flashdata('error_message', 'You cannot delete your own account while logged in.');
			redirect('manager/users');
			return;
		}

		$user_to_delete = $this->comman_model->getAll(array('id' => $id), '', 'ci_user', 'first');
		if (empty($user_to_delete)) {
			$this->session->set_flashdata('error_message', 'User not found.');
			redirect('manager/users');
			return;
		}
		$uname = !empty($user_to_delete['name']) ? $user_to_delete['name'] : 'User #' . $id;

		$owned_counts = $this->ci_admin_model->get_user_owned_records_count($id);
		if ($owned_counts['total'] > 0) {
			$details = array();
			if ($owned_counts['leads'] > 0) {
				$details[] = $owned_counts['leads'] . ' database record(s)/lead(s)';
			}
			if ($owned_counts['kam'] > 0) {
				$details[] = $owned_counts['kam'] . ' KAM client record(s)';
			}
			if ($owned_counts['tasks'] > 0) {
				$details[] = $owned_counts['tasks'] . ' active task(s)';
			}
			$reason = implode(', ', $details);

			$this->session->set_flashdata('error_message', 'Cannot delete user "' . htmlspecialchars($uname) . '". This user still owns ' . $reason . '. Their database must be transferred first before deletion.');
			redirect('manager/database-transfer?from_user=' . $id);
			return;
		}

		$this->db->trans_start();
		$this->db->query("UPDATE ci_leads SET agents = TRIM(BOTH ',' FROM REPLACE(CONCAT(',', IFNULL(agents, ''), ','), ',{$id},', ',')) WHERE FIND_IN_SET({$id}, agents) > 0");
		$this->ci_admin_model->deleteData('id', $id, 'ci_user');
		$this->db->trans_complete();

		if ($this->db->trans_status() !== FALSE) {
			$this->ci_admin_model->log_admin_action('user_deleted', 'User deleted: ' . $uname . ' after verifying zero owned records', 'SUCCESS');
			$this->session->set_flashdata('message', 'User ' . htmlspecialchars($uname) . ' has been deleted successfully.');
		} else {
			$this->session->set_flashdata('error_message', 'Failed to delete user.');
		}

		redirect('manager/users');
	}  
	


public function ajax_edit_status()
    {
		
			if ($this->input->server('REQUEST_METHOD') === 'POST') { 
			
				$status = ($_POST['admin_status']==1) ? 0 : 1;
				$response= $this->comman_model->updateData('id',$_POST['id'],array('admin_status'=>$status),'ci_user');
				$this->ci_admin_model->log_admin_action('user_status_toggle', 'Toggled user status for ID #'.$_POST['id'].' to '.$status, 'SUCCESS', $this->session->userdata('manager_id'), $this->session->userdata('manager_name'));
				echo "ok";
			}
			else{
				echo "error";
			}
	}

       public function get_state()
 {
	
	 $str='<option value="">Choose State</option>';
   
	if(isset($_POST['c_id'])&&!empty($_POST['c_id']))
	{
	
	$data['city_list']= $this->comman_model->getAll(array('country_id'=>$_POST['c_id']),'','states');
	foreach($data['city_list'] as $list)
	{
		$str.="<option value=".$list['id'].">".$list['name']."</option>"; 
	}
	
	}
	echo $str;
 }

  public function check_name()
	 {       
		$data=array();			
			if($this->input->server('REQUEST_METHOD')==='POST'){
				extract($_POST);	
				$data['list']=$this->comman_model->getAll(array('c_username'=>$c_name),'','ci_user');
						if(empty($data['list'])){
							echo "done";
						}
						else{
							echo "error";
						}
			}
		   else{
				echo "error";
			}
       }
}