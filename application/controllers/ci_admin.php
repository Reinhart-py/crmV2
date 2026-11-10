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

	/** Logout popup: signed-in time, today's attendance, and what is still due today (own follow-ups and meetings). */
	public function logout_info()
	{
		header('Content-Type: application/json');
		$uid = (int)$this->session->userdata('manager_id');
		if ($uid <= 0) { echo json_encode(array('ok' => false)); exit; }
		$t = date('Y-m-d');
		$out = array('ok' => true, 'since' => '', 'clock_in' => '', 'clocked_out' => false, 'fu_today' => 0, 'fu_missed' => 0, 'mt_today' => 0);

		$row = $this->db->query("SELECT COALESCE(login_time, created_at) AS t FROM ci_admin_logs WHERE user_id = $uid AND activity = 'login' AND status = 'SUCCESS' ORDER BY id DESC LIMIT 1")->row_array();
		if (!empty($row['t'])) $out['since'] = date('c', strtotime($row['t']));

		if ($this->db->table_exists('ci_attendance')) {
			$att = $this->db->query("SELECT in_time, out_time FROM ci_attendance WHERE user_id = $uid AND attendance_date = '$t' ORDER BY id DESC LIMIT 1")->row_array();
			if (!empty($att['in_time'])) {
				$out['clock_in'] = date('H:i', strtotime($att['in_time']));
				$out['clocked_out'] = !empty($att['out_time']);
			}
		}

		$mine = "(A.lead_assign = $uid OR (A.created_by = $uid AND (A.lead_assign = 0 OR A.lead_assign IS NULL)) OR (A.agents IS NOT NULL AND A.agents != '' AND FIND_IN_SET($uid, A.agents) > 0))";
		$open = "A.status NOT IN (42,43,44) AND (A.company_status IS NULL OR LOWER(A.company_status) != 'closed')";
		$c = $this->db->query("SELECT
				SUM(A.followup_date = '$t') AS fu_today,
				SUM(A.followup_date >= '2000-01-01' AND A.followup_date < '$t') AS fu_missed,
				SUM(A.m_date = '$t') AS mt_today
			FROM ci_leads A WHERE $open AND $mine")->row_array();
		foreach (array('fu_today', 'fu_missed', 'mt_today') as $k) $out[$k] = (int)(isset($c[$k]) ? $c[$k] : 0);
		echo json_encode($out);
		exit;
	}

	public function index()
	{
		// Site root and /manager: someone already signed in goes to their start page
		if ((int)$this->session->userdata('manager_id') > 0) {
			redirect(crm_landing_url());
			return;
		}
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
				// Login disabled, or marked as left (on the staff profile or by the HR link): no sign-in
				require_once APPPATH . 'helpers/crm_permission_helper.php';
				$closed = crm_account_closed_msg((int)$data['users']['id']);
				if ($closed !== '') {
					$this->ci_admin_model->log_admin_action('login_failed', 'Sign-in refused, login disabled or left: ' . $email, 'FAILED', $data['users']['id'], $data['users']['name']);
					$this->session->set_flashdata('error_message', $closed);
					redirect('manager/login');
				}
				// Sales CRM subscription on the subscription portal (only when switched on in Third-Party API > Subscription).
				// Asked fresh at every sign-in. Stopped by the portal (payment issue): nobody signs in, they go to the renew page.
				// Portal can't be asked properly (wrong secret, down): admins can still sign in to sort it out.
				$this->load->model('subscription_model');
				$sub = $this->subscription_model->state(true);
				if (empty($sub['allowed']) && !empty($sub['portal_refused'])) {
					$this->ci_admin_model->log_admin_action('login_failed', 'Sign-in refused, Sales CRM subscription paused: ' . $email, 'FAILED', $data['users']['id'], $data['users']['name']);
					$renew = $this->subscription_model->renew_url($sub);
					if ($renew !== '') redirect($renew);
					$this->session->set_flashdata('error_message', 'Access paused: payment issue with the Sales CRM subscription. Please renew it, or check with your administrator.');
					redirect('manager/login');
				}
				if (empty($sub['allowed']) && !crm_is_full_admin((int)$data['users']['id'])) {
					$this->ci_admin_model->log_admin_action('login_failed', 'Sign-in refused, Sales CRM subscription not active: ' . $email, 'FAILED', $data['users']['id'], $data['users']['name']);
					$this->session->set_flashdata('error_message', 'The Sales CRM subscription is not active' . (!empty($sub['reason']) ? ' (' . rtrim($sub['reason'], '. ') . ')' : '') . '. Please ask your administrator to renew it.');
					redirect("manager/login");
				}
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
				// Presentation (TV screen) users land on the Live presentation.
				redirect(crm_is_presentation_user($data['users']['user_type']) ? 'manager/live/presentation' : 'manager/dashboard');
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
	
	/** Dashboard picked by role: Admin, Sales, Back Office or KAM. The previous dashboard stays at manager/dashboard/classic. */
	public function dashboard()
	{
		$data = array();
		$data['info'] = array('title' => 'Dashboard');
		$data['info2'] = $this->if_not_login();
		if (crm_is_presentation_user()) {
			redirect('manager/live/presentation');
		}
		if (!crm_is_full_admin() && crm_can('start_live') && crm_can('live', 'read')) {
			redirect('manager/live');
		}
		$this->load->model('dashboard_model', 'dash');
		$uid = (int)$this->session->userdata('manager_id');
		// The user's choice: Modern (default), Classic, Classic 2 (a separate copy of Classic) Classic 3 (a separate copy of Classic 2) or Classic 5 (a copy of Classic 3 with extra blocks).
		$style = $this->dash->get_style($uid);
		if ($style === 'classic' || $style === 'classic2' || $style === 'classic3' || $style === 'classic5' || $style === 'classic6') {
			return $this->dashboard_classic($style === 'classic' ? false : $style);
		}
		if ($style === 'modern6') return $this->dashboard_modern6();
		$role = (int)$this->session->userdata('manager_role');
		$is_admin = crm_is_full_admin();
		$all = $is_admin || crm_can('all_data');

		$key = $is_admin ? 'admin' : $this->dash->role_key($role);
		// Admins can open the other role dashboards to check them.
		$as = (string)$this->input->get('as');
		if ($is_admin && in_array($as, array('sales', 'backoffice', 'kam'), true)) $key = $as;

		$win = $this->dash->month_window((string)$this->input->get('m'));
		$team_ids = $this->dash->scope_ids($uid, $all);
		$has_team = count($team_ids) > 1;
		$only_me = $has_team && $this->input->get('me') === '1';
		$ids = $only_me ? array($uid) : $team_ids;

		$data['dkey'] = $key;
		$data['dash_style'] = $style;
		// Figures are confidential: never kept in the browser cache or a proxy.
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, private');
		$this->output->set_header('Pragma: no-cache');
		$data['is_admin'] = $is_admin;
		$data['has_team'] = $has_team;
		$data['only_me'] = $only_me;
		$data['win'] = $win;
		$data['uid'] = $uid;
		$data['late_days'] = 3;
		$data['my_fu'] = $this->dash->my_followups($uid);
		$data['my_list'] = $this->dash->my_followup_list($uid, 12);
		$data['my_tasks'] = $this->dash->open_tasks(array($uid));
		$data['targets'] = $this->dash->targets($key === 'kam' && !$is_admin ? array($uid) : $ids, $win);
		$data['tracker'] = ($has_team && !$only_me) ? $this->dash->tracker($ids, $win) : array();

		if ($key === 'admin' || $key === 'sales') {
			$data['funnel'] = $this->dash->funnel($ids, $win);
			$data['by_day'] = $this->dash->sales_by_day($ids, $win);
		}
		if ($key === 'admin' || $key === 'backoffice' || $key === 'kam') {
			$data['reviews'] = $this->dash->pending_reviews();
		}
		if ($key === 'admin' || $key === 'backoffice') {
			$data['flow'] = $this->dash->order_flow($data['late_days']);
			$data['late_orders'] = $this->dash->late_orders($data['late_days'], 12);
			$data['orders_done'] = $this->dash->orders_done($win);
		}
		if ($key === 'kam') {
			$data['kam_clients'] = $this->dash->kam_clients($uid, $is_admin);
		}
		$this->load->view('manager/dash/index', $data);
	}

	/** One person's open work for the staff tracker (only people the viewer may see). */
	public function dashboard_person($id = 0)
	{
		$this->if_not_login();
		$this->load->model('dashboard_model', 'dash');
		$uid = (int)$this->session->userdata('manager_id');
		$id = (int)$id;
		$all = crm_is_full_admin() || crm_can('all_data');
		$this->output->set_content_type('application/json');
		if ($id <= 0 || !in_array($id, $this->dash->scope_ids($uid, $all), true)) {
			$this->output->set_status_header(403)->set_output(json_encode(array('ok' => false, 'msg' => 'Not in your team')));
			return;
		}
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, private');
		$d = $this->dash->person_detail($id);
		// Only what the panel shows: no phone numbers or remarks in full.
		$mask = function ($p) { $p = preg_replace('/[^0-9+]/', '', (string)$p); return strlen($p) > 4 ? str_repeat('*', strlen($p) - 3) . substr($p, -3) : $p; };
		$fu = array();
		foreach ($d['followups'] as $r) $fu[] = array('enc' => base64_encode($r['id']), 'customer' => $r['customer'], 'poc' => $r['poc'], 'lead_id' => $r['lead_id'], 'followup_date' => $r['followup_date']);
		$d['followups'] = $fu;
		foreach ($d['calls'] as &$r) { $r['phone_number'] = $mask($r['phone_number']); }
		unset($r);
		foreach ($d['done'] as &$r) { $r['followup_remark'] = mb_substr((string)$r['followup_remark'], 0, 80); }
		unset($r);
		if ($id !== $uid) {
			$who = $this->db->select('name')->where('id', $id)->get('ci_user')->row_array();
			$this->ci_admin_model->log_admin_action('dashboard_view_person', 'Opened staff tracker details of ' . (!empty($who['name']) ? $who['name'] : 'user') . ' (#' . $id . ')');
		}
		$this->output->set_output(json_encode(array('ok' => true) + $d));
	}

	/** Save the user's choice of CRM look: Classic or Modern (profile menu). Only the look changes. */
	public function crm_view_save()
	{
		$this->if_not_login();
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$uid = (int)$this->session->userdata('manager_id');
			$v = $this->input->post('view') === 'modern' ? 'modern' : 'classic';
			$this->load->model('dashboard_model', 'dash'); // makes sure ci_user_prefs exists
			$this->dash->prefs_columns();
			$this->db->query("INSERT INTO ci_user_prefs (user_id, crm_view, updated_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE crm_view = VALUES(crm_view), updated_at = NOW()", array($uid, $v));
			$this->session->set_userdata('crm_view', $v);
		}
		// Back to the page the switch was used on (profile menu); only CRM pages
		$ret = trim((string)$this->input->post('return'), '/');
		redirect(preg_match('#^manager(/[A-Za-z0-9_\-/]*)?$#', $ret) ? $ret : 'manager/dashboard');
	}

	/** Save the user's choice of Modern or Classic dashboard. */
	public function dashboard_style()
	{
		$this->if_not_login();
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$this->load->model('dashboard_model', 'dash');
			$this->dash->set_style((int)$this->session->userdata('manager_id'), (string)$this->input->post('style'));
		}
		redirect('manager/dashboard');
	}

	/** Modern 6: a short version of Classic 6. Same views per department and the same rights, one screen each. */
	private function dashboard_modern6()
	{
		$data = array();
		$data['info'] = array('title' => 'Dashboard');
		$data['info2'] = $this->if_not_login();
		$this->load->model('dashboard6_model', 'dash6');
		$c6 = $this->dash6->allowed();
		$v = (string)$this->input->get('view');
		if (!in_array($v, $c6['views'], true)) $v = in_array($c6['own'], $c6['views'], true) ? $c6['own'] : 's';
		$data['m6'] = $this->dash6->build_short($v) + array('views' => $c6['views'], 'own' => $c6['own'], 'names' => $this->dash6->names());
		$data['dash_style'] = 'modern6';
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, private');
		$this->load->view('manager/dashboard_modern6', $data);
	}

	/** Classic dashboard. Classic 2 / 3 / 5 / 6 ($copy) are the same data shown with their own copy of the page (manager/dashboard_classic2.php, _classic3.php, _classic5.php). Classic 5 also loads a few extra blocks. */
	public function dashboard_classic($copy = false)
	{
		$copy = ($copy === true) ? 'classic2' : (in_array($copy, array('classic2', 'classic3', 'classic5', 'classic6'), true) ? $copy : false);
	  	$data=array();
		$data['info']=array(
			'title'=>'Manager Dashboard'
			);
		$data['info2']=$this->if_not_login();
		// "Start on Live screen" (Special rights) opens Live instead of the dashboard, but only with the Live right
		// (otherwise Live would send them back here and the two pages would loop).
		if (crm_is_presentation_user()) {
			redirect('manager/live/presentation');
		}
		if (!crm_is_full_admin() && crm_can('start_live') && crm_can('live', 'read')) {
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
		$data['dash_style'] = $copy ? $copy : 'classic';
		if ($copy === 'classic5') {
			// Extra blocks shown only on Classic 5.
			$this->load->model('dashboard5_model', 'dash5');
			$data['c5'] = $this->dash5->extras();
		}
		if ($copy === 'classic6') {
			// Classic 6: the Classic 5 page plus one view per department (?view=a / s / b / k), only the views this user may open.
			$this->load->model('dashboard5_model', 'dash5');
			$this->load->model('dashboard6_model', 'dash6');
			$data['c5'] = $this->dash5->extras();
			$c6 = $this->dash6->allowed();
			$v = (string)$this->input->get('view');
			if (!in_array($v, $c6['views'], true)) $v = in_array($c6['own'], $c6['views'], true) ? $c6['own'] : 's';
			$data['c6'] = $this->dash6->build($v) + array('views' => $c6['views'], 'own' => $c6['own'], 'names' => $this->dash6->names());
		}

		$this->load->view($copy ? 'manager/dashboard_' . $copy : 'manager/dashboard',$data);
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
				. '<p>We received a request to reset the password of your Sales CRM account (<strong>' . htmlspecialchars($user['c_username']) . '</strong>).</p>'
				. '<p style="margin:24px 0;"><a href="' . $link . '" style="background:#1e3a8a;color:#ffffff;text-decoration:none;padding:12px 22px;border-radius:6px;font-weight:bold;display:inline-block;">Reset Password</a></p>'
				. '<p>This link is valid for 15 minutes and can be used once. If the button does not work, copy this address into your browser:<br><span style="color:#1e3a8a;word-break:break-all;">' . $link . '</span></p>'
				. '<p>If you did not request a password reset, you can ignore this email. Your password will not change.</p>';

			$resp = $this->mail_model->sendmail($to, 'Reset your Sales CRM password', array(), $mail_str, $user['name']);

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

	/** j***@example.com */
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
		// Own profile is view-only: details cannot be edited here (only the photo, via upload_avatar).
		$is_editing = false;

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
		// Private CRM: only the sign-in page is listed (robots.txt also asks search engines to stay out)
		$urls = array(
			array('loc' => $base . 'manager/login', 'priority' => '1.0', 'changefreq' => 'monthly')
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
			'title' => 'CRM Sitemap',
			'page_heading' => 'CRM Sitemap & Directory'
		);
		$data['info2'] = $this->if_not_login();
		$this->load->view('manager/sitemap', $data);
	}
}
