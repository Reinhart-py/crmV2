<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Ci_admin_user extends CI_Controller {
	/** Address the pages are opened under: System (manager/users) or HR (manager/hr/staff). */
	private $base = 'manager/users';
	private $perm_key = 'users';
	private $is_admin = false;
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
		if ($this->uri->segment(2) === 'hr') { $this->base = 'manager/hr/staff'; $this->perm_key = 'hr_staff'; }
		$this->load->helper('crm_permission');
		$this->_ensure_designation();
		$this->_ensure_contact();
		$this->_ensure_exit();
  
	}
	
	
	public function if_not_login($url = '')
    {
        if ($this->session->userdata('manager_id') == '') {
            if ($url != '') {
                redirect($url);
            } else {
                redirect('/manager/login');
            }
        }
        // Admin: everything. Others: the Users (System) or Staff (HR) right from Roles & Permissions -
        // View for the list and profile, Full for add / edit / credentials / enable-disable / delete.
        $this->is_admin = ((int)$this->session->userdata('manager_role') === 1);
        $need = in_array($this->router->fetch_method(), array('index', 'view'), true) ? 'read' : 'write';
        if (!$this->is_admin && !crm_can($this->perm_key, $need)) {
        	$this->session->set_flashdata('error_message', 'Unauthorized Access!');
        		redirect('/manager/dashboard');

        }else {
            $data = array();
		   
		   
           return $data;
        }
    }
	/** Only an admin may open, change or create an Admin account. */
	private function _guard_target($user)
	{
		if (!$this->is_admin && !empty($user) && (int)$user['user_type'] === 1) {
			$this->session->set_flashdata('error_message', 'Only an admin can open or change an Admin account.');
			redirect($this->base);
		}
	}

	/** Pages share these: the address prefix, whether the person may change things, and the roles they may give. */
	private function _page_data(&$data)
	{
		$data['base'] = $this->base;
		$data['can_write'] = $this->is_admin || crm_can($this->perm_key, 'write');
		if (isset($data['r_list']) && !$this->is_admin) {
			$data['r_list'] = array_values(array_filter($data['r_list'], function ($r) { return (int)$r['id'] !== 1; }));
		}
		if (!$this->is_admin && $this->input->server('REQUEST_METHOD') === 'POST' && (int)$this->input->post('user_type') === 1) {
			$this->session->set_flashdata('error_message', 'Only an admin can give the Admin role.');
			redirect($this->base);
		}
	}

	/** Employee ID and Passport No must belong to one person only. Returns the problem, or '' when fine. */
	private function _duplicate_message($exclude_id = 0)
	{
		$fields = array('emp_id' => 'Employee ID', 'p_no' => 'Passport No');
		foreach ($fields as $k => $label) {
			$v = trim((string)$this->input->post($k));
			if ($v === '') continue;
			$this->db->where($k, $v);
			if ($exclude_id) $this->db->where('id !=', (int)$exclude_id);
			if ($this->db->count_all_results('ci_user') > 0) return $label . ' "' . $v . '" is already used by another user.';
		}
		return '';
	}

	/** Form check while typing: is this Employee ID / Passport No already used by someone else? */
	public function check_unique()
	{
		$me = (int)$this->session->userdata('manager_id');
		$ok = $me && ((int)$this->session->userdata('manager_role') === 1 || crm_can('users') || crm_can('hr_staff'));
		$field = (string)$this->input->post('field');
		$value = trim((string)$this->input->post('value'));
		if (!$ok || !in_array($field, array('emp_id', 'p_no'), true)) { echo 'error'; return; }
		if ($value === '') { echo 'ok'; return; }
		$this->db->where($field, $value);
		$id = (int)$this->input->post('id');
		if ($id) $this->db->where('id !=', $id);
		echo ($this->db->count_all_results('ci_user') > 0) ? 'taken' : 'ok';
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
			'title'=>'View Staff',
			'page_heading'=>'View Staff'
		);
		
		$data['info2']=$this->if_not_login();
		
		$order_by = in_array($this->input->get('order_by'), array('A.id', 'A.name', 'A.created'), true) ? $this->input->get('order_by') : 'A.id';
		$sort_by = (strtoupper((string)$this->input->get('sort_by')) === 'ASC') ? 'ASC' : 'DESC';
		$search = is_string($this->input->get('search')) ? trim($this->input->get('search')) : '';		
		$data['product_list2']= $this->ci_admin_model->get_users('','',$order_by,$sort_by,$search); 
		
		
		$params = array();
        $limit_per_page = 2000;
        $pseg = ($this->base === 'manager/users') ? 3 : 4; // page number position in the address
        $page = ($this->uri->segment($pseg)) ? ($this->uri->segment($pseg) - 1) : 0;
		 $page2 = ($this->uri->segment($pseg)) ? $this->uri->segment($pseg): 0;
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
            $config['base_url'] = base_url() . $this->base;
            $config['total_rows'] = $total_records;
            $config['per_page'] = $limit_per_page;
            $config["uri_segment"] = $pseg;
			
			 
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
	
		
		$this->_page_data($data);
		$data['exit_reasons'] = self::exit_reasons();
		$this->load->view('manager/user/userlist',$data);
	}

	
public function view($id='') 

		{

			 $data=array();
				$data['info']=array(
					'title'=>'Staff',
					'page_heading'=>'View Staff'
				);
			$data['info2']=$this->if_not_login();
			
			$data['p_info']=$this->ci_admin_model->getSingleUser($id);
			
			
			
			
			
    	 	   if(empty($data['p_info']))

			   {
			   		$this->session->set_flashdata('error_message', 'Not allowed');
				  	redirect($this->base);
	           } 
	           $this->_guard_target($data['p_info']);
	           $this->_page_data($data);
	           $data['exit'] = $this->db->where('user_id', (int)$id)->get('ci_user_exit')->row_array();
	           if (!empty($data['exit'])) {
	           	$mb = $this->db->select('name')->where('id', (int)$data['exit']['marked_by'])->get('ci_user')->row_array();
	           	$data['exit']['marked_by_name'] = !empty($mb['name']) ? $mb['name'] : '';
	           	$data['owned'] = $this->ci_admin_model->get_user_owned_records_count((int)$id);
	           }
	           $data['exit_reasons'] = self::exit_reasons();
	           $data['is_self'] = ((int)$id === (int)$this->session->userdata('manager_id'));
	           $this->load->view('manager/user/user_view',$data);

}
	

	/** Presentation users keep their centers in ci_user_live_offices (JSON list per user). */
	/** WhatsApp number and the "Details for Reporting" (own table: ci_user is full).
	 *  Reporting email / number / WhatsApp are picked from the person's own details, each with a "verified" tick. */
	private function _ensure_contact()
	{
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_user_contact (
			user_id INT NOT NULL PRIMARY KEY,
			whatsapp_code VARCHAR(10) NOT NULL DEFAULT '', whatsapp VARCHAR(30) NOT NULL DEFAULT '',
			report_email VARCHAR(190) NOT NULL DEFAULT '', report_email_verified TINYINT(1) NOT NULL DEFAULT 0, report_email_verified_at DATETIME NULL, report_email_verified_by INT NULL,
			report_phone VARCHAR(40) NOT NULL DEFAULT '', report_phone_verified TINYINT(1) NOT NULL DEFAULT 0, report_phone_verified_at DATETIME NULL, report_phone_verified_by INT NULL,
			report_whatsapp VARCHAR(40) NOT NULL DEFAULT '', report_whatsapp_verified TINYINT(1) NOT NULL DEFAULT 0, report_whatsapp_verified_at DATETIME NULL, report_whatsapp_verified_by INT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		// Verified ticks on the contact fields themselves (added later: create the columns when missing)
		foreach (array('email', 'mobile', 'wa', 'alt') as $k) {
			if (!$this->db->field_exists($k . '_verified', 'ci_user_contact')) {
				$this->db->query("ALTER TABLE ci_user_contact ADD {$k}_verified TINYINT(1) NOT NULL DEFAULT 0, ADD {$k}_verified_at DATETIME NULL, ADD {$k}_verified_by INT NULL");
				$this->db->data_cache = array();
			}
		}
	}
	/** "+971 501234567" from a code and a number ('' when there is no number). Same format as the form. */
	private function _phone_text($code, $num)
	{
		$code = preg_replace('/\D+/', '', (string)$code); $num = preg_replace('/[^\d]+/', '', (string)$num);
		return $num === '' ? '' : ($code !== '' ? '+' . $code . ' ' : '') . $num;
	}
	/** Save WhatsApp and the reporting details. $old = the user (with these columns) before saving, empty when new.
	 *  Only the person's own email / numbers are accepted; a tick keeps its date while the choice stays the same. */
	private function _save_contact($user_id, $old = array())
	{
		if (!(int)$user_id || $this->input->post('whatsapp') === null) return;
		$me = (int)$this->session->userdata('manager_id');
		$p = function ($k) { return trim((string)$this->input->post($k)); };
		$row = array('user_id' => (int)$user_id,
			'whatsapp_code' => substr(preg_replace('/\D+/', '', $p('whatsapp_code')), 0, 10),
			'whatsapp' => substr(preg_replace('/\D+/', '', $p('whatsapp')), 0, 30));
		$emails = array_filter(array($p('email'), $p('c_username'), isset($old['c_username']) ? trim($old['c_username']) : ''));
		$phones = array_filter(array($this->_phone_text($p('country_code'), $p('mobile')), $this->_phone_text($p('country_code2'), $p('uae_mobile')), $this->_phone_text($row['whatsapp_code'], $row['whatsapp'])));
		$own = array('report_email' => $emails, 'report_phone' => $phones, 'report_whatsapp' => $phones);
		foreach ($own as $k => $choices) {
			$v = $p($k);
			if (!in_array($v, $choices, true)) $v = '';
			$row[$k] = $v;
			$ticked = ($v !== '' && $p($k . '_verified') === '1');
			$kept = $ticked && !empty($old[$k . '_verified']) && isset($old[$k]) && $old[$k] === $v;
			$row[$k . '_verified'] = $ticked ? 1 : 0;
			$row[$k . '_verified_at'] = $ticked ? ($kept ? $old[$k . '_verified_at'] : date('Y-m-d H:i:s')) : null;
			$row[$k . '_verified_by'] = $ticked ? ($kept ? $old[$k . '_verified_by'] : $me) : null;
		}
		// Verified ticks on Email, Contact No, WhatsApp No and Alternate No
		$fields = array(
			'email' => array($p('email'), isset($old['email']) ? trim((string)$old['email']) : ''),
			'mobile' => array($this->_phone_text($p('country_code'), $p('mobile')), $this->_phone_text(isset($old['country_code']) ? $old['country_code'] : '', isset($old['mobile']) ? $old['mobile'] : '')),
			'wa' => array($this->_phone_text($row['whatsapp_code'], $row['whatsapp']), $this->_phone_text(isset($old['whatsapp_code']) ? $old['whatsapp_code'] : '', isset($old['whatsapp']) ? $old['whatsapp'] : '')),
			'alt' => array($this->_phone_text($p('country_code2'), $p('uae_mobile')), $this->_phone_text(isset($old['country_code2']) ? $old['country_code2'] : '', isset($old['uae_mobile']) ? $old['uae_mobile'] : '')),
		);
		foreach ($fields as $k => $vals) {
			$ticked = ($vals[0] !== '' && $p($k . '_verified') === '1');
			$kept = $ticked && !empty($old[$k . '_verified']) && $vals[0] === $vals[1];
			$row[$k . '_verified'] = $ticked ? 1 : 0;
			$row[$k . '_verified_at'] = $ticked ? ($kept ? $old[$k . '_verified_at'] : date('Y-m-d H:i:s')) : null;
			$row[$k . '_verified_by'] = $ticked ? ($kept ? $old[$k . '_verified_by'] : $me) : null;
		}
		$this->db->replace('ci_user_contact', $row);
	}

	/** Designation (job title) lives in its own small table: ci_user is too wide to take more columns. */
	private function _ensure_designation()
	{
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_user_designation (user_id INT NOT NULL PRIMARY KEY, designation VARCHAR(150) NOT NULL DEFAULT '') ENGINE=InnoDB DEFAULT CHARSET=utf8");
	}
	private function _save_designation($user_id)
	{
		if (!(int)$user_id || $this->input->post('designation') === null) return;
		$d = mb_substr(trim(preg_replace('/\s+/', ' ', (string)$this->input->post('designation'))), 0, 150);
		$this->db->query('REPLACE INTO ci_user_designation (user_id, designation) VALUES (?, ?)', array((int)$user_id, $d));
	}
	/** Titles already in use, offered as suggestions so the same title is spelt the same way. */
	private function _designation_list()
	{
		$rows = $this->db->query("SELECT DISTINCT designation FROM ci_user_designation WHERE designation <> '' ORDER BY designation")->result_array();
		return array_map(function ($r) { return $r['designation']; }, $rows);
	}

	private function _ensure_live_offices()
	{
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_user_live_offices (user_id INT NOT NULL PRIMARY KEY, offices TEXT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8");
	}

	private $pres_offices = array();

	private function _save_live_offices($user_id, $offices)
	{
		$this->db->query('REPLACE INTO ci_user_live_offices (user_id, offices) VALUES (?, ?)', array((int)$user_id, json_encode(array_values($offices))));
	}

	/** The office codes ticked on the form, limited to known codes. */
	private function _posted_offices()
	{
		$allowed = crm_office_codes();
		$picked = (array)$this->input->post('live_offices');
		return array_values(array_unique(array_filter(array_map('trim', $picked), function ($o) use ($allowed) { return in_array($o, $allowed, true); })));
	}

	/**
	 * Presentation (TV screen) user: only login, name, status and centers are kept.
	 * Returns the row to save, or sets a flash error and returns false.
	 */
	private function _presentation_row($is_new)
	{
		$name = trim((string)$this->input->post('name'));
		$offices = $this->_posted_offices();
		$this->pres_offices = $offices;
		if ($name === '' || empty($offices)) {
			$this->session->set_flashdata('error_message', 'For a Presentation user, enter the screen name and tick at least one office code.');
			return false;
		}
		$row = array(
			'name' => $name,
			'office' => '',
			'admin_status' => (int)$this->input->post('admin_status') === 0 ? 0 : 1,
			'user_type' => crm_presentation_role_id(),
		);
		if ($is_new) {
			$me = (int)$this->session->userdata('manager_id');
			$mine = $this->db->select('user_level')->where('id', $me)->get('ci_user')->row_array();
			$row += array(
				'c_username' => trim((string)$this->input->post('c_username')),
				'c_password' => sha1((string)$this->input->post('c_password')),
				'status' => 1,
				'manager' => $me,
				'user_level' => (!empty($mine['user_level']) ? (int)$mine['user_level'] : 0) + 1,
				'created' => date('Y-m-d'),
				// Fields that have no default in the table.
				'address' => '', 'country_code' => '', 'country_code2' => '', 'c_email' => '', 'c_mobile' => '',
				'c_salary' => '', 'c_doj' => '', 'c_dol' => '', 'national' => '', 'p_no' => '', 'p_date' => '', 'p_exdate' => '',
			);
		} else {
			$row['modified'] = date('Y-m-d');
		}
		return $row;
	}

	public function add()

	{


         $data=array();

				$data['info']=array(
					'title'=>'Add Staff',
					'page_heading'=>'Add Staff'
				);
		$data['info2']=$this->if_not_login();
		$data['r_list'] =$this->comman_model->getAll(array('status'=>1),array('id','ASC'),'ci_roles');
		$data['c_list'] =$this->comman_model->getAll('',array('id','ASC'),'countries');
		$data['u_list'] =$this->comman_model->getAll('',array('id','ASC'),'ci_user');
		$this->_page_data($data);
		$data['designations'] = $this->_designation_list();
		$this->_drop_left($data['u_list']);
		$this->_ensure_live_offices();
		$data['pres_role_id'] = crm_presentation_role_id();
		$data['office_codes'] = crm_office_codes();
		if($this->input->server('REQUEST_METHOD')==='POST' && crm_is_presentation_user($this->input->post('user_type')))
		{
			$row = $this->_presentation_row(true);
			if ($row !== false) {
				if ($row['c_username'] === '' || (string)$this->input->post('c_password') === '') {
					$this->session->set_flashdata('error_message', 'Please enter the user name and password.');
				} elseif ($this->ci_admin_model->checkDuplicateUsername($row['c_username']) == FALSE) {
					$new_id = $this->comman_model->saveData('ci_user', $row);
					if ($new_id) $this->_save_live_offices($new_id, $this->pres_offices);
					$this->session->set_flashdata('message', 'Presentation user has been added successfully!');
					redirect($this->base);
				} else {
					$this->session->set_flashdata('error_message', 'Company Username is already exists.');
				}
			}
			redirect($this->base.'/add');
		}
		if($this->input->server('REQUEST_METHOD')==='POST')

		{

			extract($_POST);
			if (($dup = $this->_duplicate_message()) !== '') {
				$this->session->set_flashdata('error_message', $dup);
				redirect($this->base.'/add');
			}
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

			 	$new_id = $this->comman_model->saveData('ci_user',$list);
			 	$this->_save_designation($new_id);
			 	$this->_save_contact($new_id);
			 	$this->session->set_flashdata('message', 'User has been added successfully!');
				redirect($this->base);
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
					'title'=>'Staff',
					'page_heading'=>'Edit Staff'
				);
			$data['info2']=$this->if_not_login();
			$data['r_list'] =$this->comman_model->getAll(array('status'=>1),array('id','ASC'),'ci_roles');
			$data['c_list'] =$this->comman_model->getAll('',array('id','ASC'),'countries');
			$data['u_list'] =$this->comman_model->getAll('',array('id','ASC'),'ci_user');
			$data['list']=$this->comman_model->getAll(array('id'=>$id),'','ci_user','first');
			if(!empty($data['list']['country'])){
				$data['state_list']= $this->comman_model->getAll(array('country_id'=>$data['list']['country']),'','states');
			}
			$this->_guard_target($data['list']);
			$this->_page_data($data);
			if (!empty($data['list'])) {
				$dg = $this->db->select('designation')->where('user_id', (int)$id)->get('ci_user_designation')->row_array();
				$data['list']['designation'] = !empty($dg['designation']) ? $dg['designation'] : '';
				$ct = $this->db->where('user_id', (int)$id)->get('ci_user_contact')->row_array();
				foreach (array('whatsapp_code', 'whatsapp', 'report_email', 'report_email_verified', 'report_email_verified_at', 'report_email_verified_by', 'report_phone', 'report_phone_verified', 'report_phone_verified_at', 'report_phone_verified_by', 'report_whatsapp', 'report_whatsapp_verified', 'report_whatsapp_verified_at', 'report_whatsapp_verified_by', 'email_verified', 'email_verified_at', 'email_verified_by', 'mobile_verified', 'mobile_verified_at', 'mobile_verified_by', 'wa_verified', 'wa_verified_at', 'wa_verified_by', 'alt_verified', 'alt_verified_at', 'alt_verified_by') as $ck) {
					$data['list'][$ck] = isset($ct[$ck]) ? $ct[$ck] : '';
				}
			}
			$data['designations'] = $this->_designation_list();
			$this->_drop_left($data['u_list'], !empty($data['list']['manager']) ? $data['list']['manager'] : 0);
			$this->_ensure_live_offices();
			$data['pres_role_id'] = crm_presentation_role_id();
			$data['office_codes'] = crm_office_codes();
			$data['live_offices'] = !empty($data['list']) ? crm_user_live_offices($data['list']) : array();
			if (!empty($data['list']) && $this->input->server('REQUEST_METHOD') === 'POST' && crm_is_presentation_user($this->input->post('user_type'))) {
				$row = $this->_presentation_row(false);
				// The password is needed to sign in: keep the current one, or set a new one.
				$new_pw = (string)$this->input->post('pres_password');
				if ($row !== false && $new_pw !== '') {
					$row['c_password'] = sha1($new_pw);
				} elseif ($row !== false && (empty($data['list']['c_password']) || $data['list']['c_password'] === sha1(''))) {
					$this->session->set_flashdata('error_message', 'Please set a password for this Presentation user.');
					$row = false;
				}
				if ($row !== false) {
					$this->comman_model->updateData('id', $id, $row, 'ci_user');
					$this->_save_live_offices($id, $this->pres_offices);
					$this->ci_admin_model->log_admin_action('user_edit', 'Updated presentation user ID #'.$id, 'SUCCESS', $this->session->userdata('manager_id'), $this->session->userdata('manager_name'));
					$this->session->set_flashdata('message', 'User has been updated successfully!');
					redirect($this->base);
				}
				redirect($this->base.'/edit/' . (int)$id);
			}
    	 	   if(empty($data['list']))

			   {
			   		$this->session->set_flashdata('error_message', 'Not allowed');
				  	redirect($this->base);
	           }    

			 if($this->input->server('REQUEST_METHOD')==='POST')
		     {
			   extract($_POST);
			   if (($dup = $this->_duplicate_message($id)) !== '') {
			   	$this->session->set_flashdata('error_message', $dup);
			   	redirect($this->base.'/edit/'.(int)$id);
			   }
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
				$this->_save_designation($id);
				$this->_save_contact($id, $data['list']);
				if($response)
					{
						$this->ci_admin_model->log_admin_action('user_edit', 'Updated user ID #'.$id, 'SUCCESS', $this->session->userdata('manager_id'), $this->session->userdata('manager_name'));
						$this->session->set_flashdata('message', 'User has been updated successfully!');
						redirect($this->base);
					}			

			}   

    	 $this->load->view('manager/user/useredit', $data);

	  }
	  

	  public function cedit($id='') 

		{

			 $data=array();
				$data['info']=array(
					'title'=>'Staff',
					'page_heading'=>'Edit Staff'
				);
			$data['info2']=$this->if_not_login();			
			$data['list']=$this->comman_model->getAll(array('id'=>$id),'','ci_user','first');
			if(empty($data['list'])){
			   		$this->session->set_flashdata('error_message', 'Not allowed');
				  	redirect($this->base);
			}
			$this->_guard_target($data['list']);
			$this->_page_data($data);
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
					redirect($this->base);

				}else{

					$this->session->set_flashdata('error_message', 'Sorry! Username already exist');
					redirect($this->base.'/cedit/'.$id);

				}
				
			}else{
				$save= array(
					
					'c_password'=>sha1($password),		
					'modified'=>date('Y-m-d')
				);

				$response= $this->comman_model->updateData('id',$id,$save,'ci_user');
				$this->session->set_flashdata('message', 'User settings has been updated successfully!');
				redirect($this->base.'/cedit/'.$id);

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
			redirect($this->base);
			return;
		}

		$current_admin_id = (int)$this->session->userdata('manager_id');
		if ($id === $current_admin_id) {
			$this->session->set_flashdata('error_message', 'You cannot delete your own account while logged in.');
			redirect($this->base);
			return;
		}

		$user_to_delete = $this->comman_model->getAll(array('id' => $id), '', 'ci_user', 'first');
		if (empty($user_to_delete)) {
			$this->session->set_flashdata('error_message', 'User not found.');
			redirect($this->base);
			return;
		}
		$this->_guard_target($user_to_delete);
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

		redirect($this->base);
	}  
	


	/** Leaving the company: date, reason and note, kept in ci_user_exit. The login is switched off;
	 *  database and leads stay with the person until transferred, orders and sales always stay with them. */
	private function _ensure_exit()
	{
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_user_exit (user_id INT NOT NULL PRIMARY KEY, left_on DATE NOT NULL, reason VARCHAR(60) NOT NULL DEFAULT '', note VARCHAR(500) NOT NULL DEFAULT '', marked_by INT NULL, marked_at DATETIME NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->data_cache = array();
	}
	public static function exit_reasons()
	{
		return array('Resigned', 'Terminated', 'Contract ended', 'Absconded', 'Retired', 'Other');
	}
	/** Ids of people who have left (to keep them out of the Upline / Manager picker). */
	private function _left_ids()
	{
		return array_map('intval', array_column($this->db->select('user_id')->get('ci_user_exit')->result_array(), 'user_id'));
	}
	private function _drop_left(&$list, $keep_id = 0)
	{
		$left = array_flip($this->_left_ids());
		$list = array_values(array_filter((array)$list, function ($u) use ($left, $keep_id) { return !isset($left[(int)$u['id']]) || (int)$u['id'] === (int)$keep_id; }));
	}

	public function left($id = '')
	{
		$this->if_not_login();
		$id = (int)$id;
		$u = $id ? $this->comman_model->getAll(array('id' => $id), '', 'ci_user', 'first') : array();
		if (empty($u) || $this->input->server('REQUEST_METHOD') !== 'POST') redirect($this->base);
		$this->_guard_target($u);
		if ($id === (int)$this->session->userdata('manager_id')) {
			$this->session->set_flashdata('error_message', 'You cannot mark yourself as left.');
			redirect($this->base . '/view/' . $id);
		}
		$t = strtotime((string)$this->input->post('left_on'));
		$on = $t ? date('Y-m-d', $t) : date('Y-m-d');
		$reason = (string)$this->input->post('reason');
		if (!in_array($reason, self::exit_reasons(), true)) $reason = 'Other';
		$note = mb_substr(trim((string)$this->input->post('note')), 0, 500);
		$this->db->replace('ci_user_exit', array('user_id' => $id, 'left_on' => $on, 'reason' => $reason, 'note' => $note,
			'marked_by' => (int)$this->session->userdata('manager_id'), 'marked_at' => date('Y-m-d H:i:s')));
		$this->comman_model->updateData('id', $id, array('admin_status' => 0, 'c_dol' => $on), 'ci_user');
		$this->ci_admin_model->log_admin_action('user_left', 'Marked user ID #' . $id . ' as left on ' . $on . ' (' . $reason . ')', 'SUCCESS', $this->session->userdata('manager_id'), $this->session->userdata('manager_name'));
		$owned = $this->ci_admin_model->get_user_owned_records_count($id);
		$msg = $u['name'] . ' is marked as left on ' . date('d-M-Y', strtotime($on)) . ' and can no longer sign in.';
		if ($owned['total'] > 0) $msg .= ' They still own ' . $owned['total'] . ' record(s) - transfer them from this page.';
		$this->session->set_flashdata('message', $msg);
		redirect($this->base . '/view/' . $id);
	}

	public function rejoin($id = '')
	{
		$this->if_not_login();
		$id = (int)$id;
		$u = $id ? $this->comman_model->getAll(array('id' => $id), '', 'ci_user', 'first') : array();
		if (empty($u) || $this->input->server('REQUEST_METHOD') !== 'POST') redirect($this->base);
		$this->_guard_target($u);
		$this->db->where('user_id', $id)->delete('ci_user_exit');
		$this->comman_model->updateData('id', $id, array('admin_status' => 1, 'c_dol' => ''), 'ci_user');
		$this->ci_admin_model->log_admin_action('user_rejoined', 'User ID #' . $id . ' rejoined (leaving record removed, login enabled)', 'SUCCESS', $this->session->userdata('manager_id'), $this->session->userdata('manager_name'));
		$this->session->set_flashdata('message', $u['name'] . ' is back: the leaving record is removed and the login is enabled again.');
		redirect($this->base . '/view/' . $id);
	}

public function ajax_edit_status()
    {
		
			// Signed in, Full right on Users or Staff, never yourself, and only an admin may switch an Admin account.
			$me = (int)$this->session->userdata('manager_id');
			$is_admin = ((int)$this->session->userdata('manager_role') === 1);
			$uid = (int)$this->input->post('id');
			$target = $uid ? $this->comman_model->getAll(array('id' => $uid), '', 'ci_user', 'first') : array();
			if (!$me || empty($target) || $uid === $me || (!$is_admin && (!(crm_can('users', 'write') || crm_can('hr_staff', 'write')) || (int)$target['user_type'] === 1))) {
				echo "error";
				return;
			}
			if ($this->db->where('user_id', $uid)->count_all_results('ci_user_exit') > 0) { echo "error"; return; }
			if ($this->input->server('REQUEST_METHOD') === 'POST') { 
			
				$status = ($this->input->post('admin_status')==1) ? 0 : 1;
				$response= $this->comman_model->updateData('id',$uid,array('admin_status'=>$status),'ci_user');
				$this->ci_admin_model->log_admin_action('user_status_toggle', 'Toggled user status for ID #'.$uid.' to '.$status, 'SUCCESS', $this->session->userdata('manager_id'), $this->session->userdata('manager_name'));
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