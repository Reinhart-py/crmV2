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
		header('X-Content-Type-Options: nosniff');
		header('X-Frame-Options: SAMEORIGIN');
		header('X-XSS-Protection: 1; mode=block');
		header('Referrer-Policy: strict-origin-when-cross-origin');
		header('Permissions-Policy: geolocation=(self), microphone=(self)');
  
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
			if(!empty($data['users']))
			{
				$session_token = md5(uniqid(mt_rand(), true));
				$client_ip = $this->ci_admin_model->get_client_ip();

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
		if ((int)$this->session->userdata('manager_role') === 5) {
			redirect('manager/live');
		}
		$data['dashboard_count']=$this->ci_admin_model->dashboard_countdata();
		$data['dashboard_Tcount']=$this->ci_admin_model->dashboard_Tcountdata();
		$data['dashboard_lead']=$this->ci_admin_model->dashboard_leaddata();
		$data['dashboard_lead_team']=$this->ci_admin_model->dashboard_leaddataT();
		$data['dashboard_db_state']=$this->ci_admin_model->dashboard_db_state(0);
		$data['dashboard_db_state_team']=$this->ci_admin_model->dashboard_db_state(1);
		$data['dashboard_lead_state']=$this->ci_admin_model->dashboard_lead_state(0);
		$data['dashboard_lead_state_team']=$this->ci_admin_model->dashboard_lead_state(1);
		$data['dashboard_order_state']=$this->ci_admin_model->dashboard_order_state(0);
		$data['dashboard_order_state_team']=$this->ci_admin_model->dashboard_order_state(1);
		$data['dashboard_achievement_my']=$this->ci_admin_model->dashboard_achievement_graph(0);
		$data['dashboard_achievement_team']=$this->ci_admin_model->dashboard_achievement_graph(1);
		$data['dashboard_db_ops_my']=$this->ci_admin_model->dashboard_stage_followups(0, 'database');
		$data['dashboard_db_ops_team']=$this->ci_admin_model->dashboard_stage_followups(1, 'database');
		$data['dashboard_lead_ops_my']=$this->ci_admin_model->dashboard_stage_followups(0, 'lead');
		$data['dashboard_lead_ops_team']=$this->ci_admin_model->dashboard_stage_followups(1, 'lead');
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
		$data['dashboard_tickets']=$this->ci_admin_model->dashboard_tickets(0);
		$data['dashboard_Ttickets']=$this->ci_admin_model->dashboard_tickets(1);
		
		$data['dashboard_TeamPro']=$this->ci_admin_model->dashboard_TeamPro();
		$data['dashboard_MyPro']=$this->ci_admin_model->dashboard_MyPro();
		$data['projection_target_my']=$this->ci_admin_model->dashboard_projection_targets(0);
		$data['projection_target_team']=$this->ci_admin_model->dashboard_projection_targets(1);
		$data['dashboard_pipeline'] = $this->ci_admin_model->dashboard_pipeline_summary();
		$data['mylead_donut'] = $this->ci_admin_model->dashboard_mylead_donut_data();
		$data['myorder_donut'] = $this->ci_admin_model->dashboard_myorder_donut_data();

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
		$data = array();
		$data['info'] = array(
			'title' => 'Forgot Password'
		);

		if ($this->input->server('REQUEST_METHOD') === 'POST')
		{
			$login = trim((string)$this->security->xss_clean($this->input->post('email')));
			if ($login === '') {
				$this->session->set_flashdata('error_message', 'Please enter your registered email address.');
				redirect('manager/forgot');
			}

			// Match the account by login username or by the email saved on the profile.
			$q = $this->db->escape($login);
			$user = $this->db->where('status', 1)
				->where("(c_username = {$q} OR email = {$q} OR c_email = {$q})", NULL, FALSE)
				->order_by('id', 'asc')
				->limit(1)->get('ci_user')->row_array();

			if (empty($user)) {
				$this->ci_admin_model->log_admin_action('password_reset_request', 'Reset requested for unknown account: ' . $login, 'FAILED', 0, $login);
				$this->session->set_flashdata('error_message', 'No active account was found for that email address.');
				redirect('manager/forgot');
			}

			// Where to send: profile email first, then the username if it is an email.
			$to = '';
			foreach (array('email', 'c_email', 'c_username') as $col) {
				if (!empty($user[$col]) && filter_var(trim($user[$col]), FILTER_VALIDATE_EMAIL)) {
					$to = trim($user[$col]);
					break;
				}
			}
			if ($to === '') {
				$this->session->set_flashdata('error_message', 'Your account has no email address on file. Please ask your administrator to reset your password.');
				redirect('manager/forgot');
			}

			// Allow one new link per minute so the mailbox is not flooded.
			$parts = explode('_', (string)$user['fotp']);
			if (count($parts) >= 2 && ((int)$parts[1] - time()) > 840) {
				$this->session->set_flashdata('message', 'A reset link was sent less than a minute ago. Please check your inbox and spam folder.');
				redirect('manager/login');
			}

			$token = bin2hex(random_bytes(24)) . '_' . (time() + 900);
			$this->comman_model->updateData('id', $user['id'], array('fotp' => $token), 'ci_user');

			$link = base_url() . 'manager/bnr54dfdf/' . $token;
			$name = htmlspecialchars($user['name']);
			$mail_str = '<p>Hello ' . $name . ',</p>'
				. '<p>We received a request to reset the password of your Seven Strokes CRM account (<strong>' . htmlspecialchars($user['c_username']) . '</strong>).</p>'
				. '<p style="margin:24px 0;"><a href="' . $link . '" style="background:#1e3a8a;color:#ffffff;text-decoration:none;padding:12px 22px;border-radius:6px;font-weight:bold;display:inline-block;">Reset Password</a></p>'
				. '<p>This link is valid for 15 minutes and can be used once. If the button does not work, copy this address into your browser:<br><span style="color:#1e3a8a;word-break:break-all;">' . $link . '</span></p>'
				. '<p>If you did not request a password reset, you can ignore this email. Your password will not change.</p>';

			$resp = $this->mail_model->sendmail($to, 'Reset your Seven Strokes CRM password', array(), $mail_str, $user['name']);

			if ($resp !== 'ok') {
				// Do not leave a live token behind for an email that never went out.
				$this->comman_model->updateData('id', $user['id'], array('fotp' => ''), 'ci_user');
				$this->ci_admin_model->log_admin_action('password_reset_request', 'Reset email failed for ' . $to . ': ' . $this->mail_model->last_error, 'FAILED', $user['id'], $user['name']);
				$this->session->set_flashdata('error_message', 'We could not send the reset email right now. Please try again in a few minutes or contact your administrator.');
				redirect('manager/forgot');
			}

			$this->ci_admin_model->log_admin_action('password_reset_request', 'Reset link emailed to ' . $to, 'SUCCESS', $user['id'], $user['name']);
			$this->session->set_flashdata('message', 'A password reset link has been sent to ' . $this->mask_email($to) . '. It is valid for 15 minutes. Please also check your spam folder.');
			redirect('manager/login');
		}
		$this->load->view('manager/forgot', $data);
	}

	/** j***@7strokes.ae */
	private function mask_email($email)
	{
		$at = strpos($email, '@');
		if ($at === false || $at < 1) {
			return $email;
		}
		return substr($email, 0, 1) . str_repeat('*', max(3, $at - 1)) . substr($email, $at);
	}

	/** Returns the active user that owns a valid, unexpired reset token, or null. */
	private function reset_token_user($token)
	{
		$token = (string)$token;
		if (!preg_match('/^[a-f0-9]{48}_\d+$/', $token)) {
			return null;
		}
		$parts = explode('_', $token);
		$user = $this->comman_model->getAll(array('fotp' => $token, 'status' => 1), '', 'ci_user', 'first');
		if (empty($user)) {
			return null;
		}
		if (time() > (int)$parts[1]) {
			$this->comman_model->updateData('id', $user['id'], array('fotp' => ''), 'ci_user');
			return null;
		}
		return $user;
	}

	public function reset($token = "")
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Set New Password'
		);
		$data['token'] = $token;
		$data['users'] = $this->reset_token_user($token);
		if (empty($data['users'])) {
			$this->session->set_flashdata('error_message', 'This password reset link is invalid or has expired. Please request a new one.');
			redirect('manager/forgot');
		}
		$this->load->view('manager/reset', $data);
	}

	public function fchange_password()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') {
			redirect('manager/login');
		}
		$token = (string)$this->input->post('token');
		$password = (string)$this->input->post('password');
		$confirm_password = (string)$this->input->post('change_password');

		$user = $this->reset_token_user($token);
		if (empty($user)) {
			$this->session->set_flashdata('error_message', 'This password reset link is invalid or has expired. Please request a new one.');
			redirect('manager/forgot');
		}
		if (strlen($password) < 6) {
			$this->session->set_flashdata('error_message', 'Password must be at least 6 characters.');
			redirect('manager/bnr54dfdf/' . $token);
		}
		if ($password !== $confirm_password) {
			$this->session->set_flashdata('error_message', 'Password and Repeat password do not match.');
			redirect('manager/bnr54dfdf/' . $token);
		}

		// Update only the owner of this token; clear the token and any open session.
		$this->db->where('id', (int)$user['id'])->where('fotp', $token)->update('ci_user', array(
			'c_password' => sha1($password),
			'fotp' => '',
			'current_session_token' => null,
		));
		$this->ci_admin_model->log_admin_action('password_reset', 'Password changed with email reset link', 'SUCCESS', $user['id'], $user['name']);
		$this->session->set_flashdata('message', 'Your password has been changed. You can now sign in with your new password.');
		redirect('manager/login');
	}



	    public function admin_profile()
		    {
				
	  $data=array();
	

			$data['info']=array(
				'title'=>'My Profile',
				'page_heading'=>'My Profile'
			);
		$data['info2']=$this->if_not_login();
		$cur_role = (int)$this->session->userdata('manager_role');
		$cur_uid = (int)$this->session->userdata('manager_id');
		$is_editing = ($this->input->get('edit') == 1 || $this->input->server('REQUEST_METHOD') === 'POST');

		if ($is_editing) {
			$data['list'] = $this->ci_admin_model->getSingleUser($cur_uid);
			if (empty($data['list'])) {
				$data['list'] = $this->comman_model->getAll(array('id' => $cur_uid), '', 'ci_user', 'first');
			}
			$data['c_list'] = $this->comman_model->getAll('', array('id', 'ASC'), 'countries');

			if ($this->input->server('REQUEST_METHOD') === 'POST') {
				extract($_POST);
				$save = array(
					'name' => isset($name) ? $name : '',
					'gender' => isset($gender) ? $gender : '',
					'address' => isset($address) ? $address : '',
					'city' => isset($city) ? $city : '',
					'mobile' => isset($mobile) ? $mobile : '',
					'email' => isset($email) ? $email : '',
					'dob' => isset($dob) ? $dob : '',
					'uae_mobile' => isset($uae_mobile) ? $uae_mobile : '',
					'emp_id' => isset($emp_id) ? $emp_id : '',
					'c_email' => isset($c_email) ? $c_email : '',
					'c_mobile' => isset($c_mobile) ? $c_mobile : '',
					'national' => isset($national) ? $national : '',
					'p_no' => isset($p_no) ? $p_no : '',
					'p_date' => isset($p_date) ? $p_date : '',
					'p_exdate' => isset($p_exdate) ? $p_exdate : '',
					'visa_no' => isset($visa_no) ? $visa_no : '',
					'eid_no' => isset($eid_no) ? $eid_no : '',
					'h_contact_name' => isset($h_contact_name) ? $h_contact_name : '',
					'h_contact_no' => isset($h_contact_no) ? $h_contact_no : '',
					'blood' => isset($blood) ? $blood : '',
					'no_d_uae' => isset($no_d_uae) ? $no_d_uae : '',
					'bank' => isset($bank) ? $bank : '',
					'iban' => isset($iban) ? $iban : '',
					'bank_no' => isset($bank_no) ? $bank_no : '',
					'modified' => date('Y-m-d')
				);
				if ($cur_role === 1 || $cur_uid === 1) {
					if (isset($c_salary)) $save['c_salary'] = $c_salary;
					if (isset($c_doj)) $save['c_doj'] = $c_doj;
					if (isset($c_dol)) $save['c_dol'] = $c_dol;
				}
				if (isset($_FILES['avatar']) && !empty($_FILES['avatar']['name']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
					$f_name = $_FILES['avatar']['name'];
					$ext = strtolower(pathinfo($f_name, PATHINFO_EXTENSION));
					if (in_array($ext, array('jpg', 'jpeg', 'png', 'webp'))) {
						$upload_dir = FCPATH . 'assets/uploads/avatars/';
						if (!is_dir($upload_dir)) {
							mkdir($upload_dir, 0777, true);
						}
						$new_filename = 'avatar_' . $cur_uid . '_' . time() . '.' . $ext;
						$dest_path = $upload_dir . $new_filename;
						if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dest_path)) {
							$old_user = $this->db->select('avatar')->where('id', $cur_uid)->get('ci_user')->row_array();
							if (!empty($old_user['avatar']) && file_exists(FCPATH . $old_user['avatar']) && strpos($old_user['avatar'], 'assets/uploads/avatars/') !== false) {
								@unlink(FCPATH . $old_user['avatar']);
							}
							$save['avatar'] = 'assets/uploads/avatars/' . $new_filename;
						}
					}
				}
				$response = $this->comman_model->updateData('id', $cur_uid, $save, 'ci_user');
				if ($response) {
					$this->session->set_flashdata('message', 'Your information updated successfully!');
					redirect('manager/profile');
					return;
				}
			}
			$this->load->view('manager/setting/adminprofile', $data);
		} else {
			$data['p_info'] = $this->ci_admin_model->getSingleUser($cur_uid);
			$this->load->view('manager/setting/profile', $data);
		}
				
			}

	public function ajax_upload_avatar()
	{
		$this->if_not_login();
		$cur_uid = (int)$this->session->userdata('manager_id');
		if (isset($_FILES['avatar']) && !empty($_FILES['avatar']['name']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
			$f_name = $_FILES['avatar']['name'];
			$ext = strtolower(pathinfo($f_name, PATHINFO_EXTENSION));
			if (in_array($ext, array('jpg', 'jpeg', 'png', 'webp'))) {
				$upload_dir = FCPATH . 'assets/uploads/avatars/';
				if (!is_dir($upload_dir)) {
					mkdir($upload_dir, 0777, true);
				}
				$new_filename = 'avatar_' . $cur_uid . '_' . time() . '.' . $ext;
				$dest_path = $upload_dir . $new_filename;
				if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dest_path)) {
					$old_user = $this->db->select('avatar')->where('id', $cur_uid)->get('ci_user')->row_array();
					if (!empty($old_user['avatar']) && file_exists(FCPATH . $old_user['avatar']) && strpos($old_user['avatar'], 'assets/uploads/avatars/') !== false) {
						@unlink(FCPATH . $old_user['avatar']);
					}
					$rel = 'assets/uploads/avatars/' . $new_filename;
					$this->db->where('id', $cur_uid)->update('ci_user', array('avatar' => $rel));
					echo json_encode(array('status' => true, 'avatar_url' => base_url() . $rel, 'message' => 'Profile photo updated successfully'));
					return;
				}
			}
		}
		echo json_encode(array('status' => false, 'message' => 'Failed to upload photo. Please upload JPG, PNG, or WEBP file.'));
	}
	   
	    
	   
	   
	       public function change_password()
		  {  
			 
	  $data=array();
		$data['info']=array(
			'title'=>'Manager Change Password'
			);
		$data['info2']=$this->if_not_login();
				
				   $id=(int)$this->session->userdata('manager_id');
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

	public function ajax_get_notifications()
	{
		$this->if_not_login();
		$uid = (int)$this->session->userdata('manager_id');
		$notifs = $this->ci_admin_model->get_user_notifications($uid, 15);
		$unread_count = 0;
		if (!empty($notifs)) {
			foreach ($notifs as $n) {
				if (isset($n['is_read']) && (int)$n['is_read'] === 0) {
					$unread_count++;
				}
			}
		}
		header('Content-Type: application/json');
		echo json_encode(array('status' => true, 'unread_count' => $unread_count, 'notifications' => $notifs));
	}

	public function ajax_mark_notifications_read()
	{
		$this->if_not_login();
		$uid = (int)$this->session->userdata('manager_id');
		$notif_id = $this->input->get_post('notif_id');
		$now = date('Y-m-d H:i:s');

		if (!empty($notif_id)) {
			if (strpos($notif_id, 'notif_') === 0) {
				$real_id = (int)str_replace('notif_', '', $notif_id);
				if ($real_id > 0) {
					$this->db->where('id', $real_id)->where("(user_id = {$uid} OR user_id = 0)")->update('ci_notifications', array('is_read' => 1));
				}
			}
			$uperm = $this->db->select('read_notif_ids')->where('user_id', $uid)->get('ci_user_permissions')->row_array();
			$read_ids = array();
			if (!empty($uperm['read_notif_ids'])) {
				$decoded = json_decode($uperm['read_notif_ids'], true);
				if (is_array($decoded)) {
					$read_ids = $decoded;
				}
			}
			if (!in_array($notif_id, $read_ids)) {
				$read_ids[] = $notif_id;
				if (count($read_ids) > 100) {
					$read_ids = array_slice($read_ids, -100);
				}
				$json = json_encode($read_ids);
				$this->session->set_userdata('read_notif_ids', $read_ids);
				if (!empty($uperm)) {
					$this->db->where('user_id', $uid)->update('ci_user_permissions', array('read_notif_ids' => $json));
				} else {
					$this->db->insert('ci_user_permissions', array('user_id' => $uid, 'permission_mode' => 'role', 'read_notif_ids' => $json));
				}
			}
		} else {
			$this->db->where('user_id', $uid)->or_where('user_id', 0)->update('ci_notifications', array('is_read' => 1));
			$this->session->set_userdata('last_notif_read_at', $now);
			$this->session->set_userdata('read_notif_ids', array());
			$exists = $this->db->where('user_id', $uid)->get('ci_user_permissions')->row_array();
			if (!empty($exists)) {
				$this->db->where('user_id', $uid)->update('ci_user_permissions', array('last_notif_read_at' => $now, 'read_notif_ids' => '[]'));
			} else {
				$this->db->insert('ci_user_permissions', array('user_id' => $uid, 'permission_mode' => 'role', 'last_notif_read_at' => $now, 'read_notif_ids' => '[]'));
			}
		}
		header('Content-Type: application/json');
		echo json_encode(array('status' => true, 'notif_id' => $notif_id));
	}

	public function sitemap_xml()
	{
		$base = base_url();
		$urls = array(
			array('loc' => $base, 'priority' => '1.0', 'changefreq' => 'daily'),
			array('loc' => $base . 'manager/login', 'priority' => '0.8', 'changefreq' => 'monthly'),
			array('loc' => $base . 'manager/dashboard', 'priority' => '0.9', 'changefreq' => 'always'),
			array('loc' => $base . 'manager/leads', 'priority' => '0.9', 'changefreq' => 'always'),
			array('loc' => $base . 'manager/leads/view_leads', 'priority' => '0.9', 'changefreq' => 'always'),
			array('loc' => $base . 'manager/orders/my', 'priority' => '0.8', 'changefreq' => 'daily'),
			array('loc' => $base . 'manager/sales', 'priority' => '0.8', 'changefreq' => 'daily'),
			array('loc' => $base . 'manager/calls', 'priority' => '0.8', 'changefreq' => 'daily'),
			array('loc' => $base . 'manager/leads/followups', 'priority' => '0.7', 'changefreq' => 'daily'),
			array('loc' => $base . 'manager/leads/meetings', 'priority' => '0.7', 'changefreq' => 'daily'),
			array('loc' => $base . 'manager/tasks/internal', 'priority' => '0.6', 'changefreq' => 'daily'),
			array('loc' => $base . 'manager/tasks/external', 'priority' => '0.6', 'changefreq' => 'daily'),
			array('loc' => $base . 'manager/database-transfer', 'priority' => '0.5', 'changefreq' => 'weekly'),
			array('loc' => $base . 'manager/sitemap', 'priority' => '0.5', 'changefreq' => 'weekly')
		);

		header('Content-Type: application/xml; charset=utf-8');
		$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		foreach ($urls as $u) {
			$xml .= "  <url>\n";
			$xml .= "    <loc>" . htmlspecialchars($u['loc'], ENT_XML1) . "</loc>\n";
			$xml .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
			$xml .= "    <changefreq>" . $u['changefreq'] . "</changefreq>\n";
			$xml .= "    <priority>" . $u['priority'] . "</priority>\n";
			$xml .= "  </url>\n";
		}
		$xml .= '</urlset>';
		echo $xml;
		exit;
	}

	public function sitemap_html()
	{
		$this->if_not_login();
		$data = array();
		$data['info'] = array(
			'title' => 'CRM Sitemap - Seven Strokes',
			'page_heading' => 'CRM Sitemap & Directory'
		);
		$data['info2'] = $this->if_not_login();
		$this->load->view('manager/sitemap', $data);
	}
}
