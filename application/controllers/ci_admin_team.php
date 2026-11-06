<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ci_admin_team extends CI_Controller {
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
	public function index()
	{
		redirect('manager/team/chart');
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
		
	public function members()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Manager - Team Members',
			'page_heading' => 'Team Members'
		);
		$data['info2'] = $this->if_not_login();
		$data['product_list'] = $this->member_stats($this->ci_admin_model->get_teamMember($this->session->userdata('manager_id')));
		$data['viewing'] = null;
		$this->load->view('manager/team/members', $data);
	}

	public function membersdetails($id = '')
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Manager - Team Members',
			'page_heading' => 'Team Members'
		);
		$data['info2'] = $this->if_not_login();
		// only someone in your own team (Admin and "All company data": anyone)
		$me = (int)$this->session->userdata('manager_id');
		$id = (int)$id;
		$mine = $this->ci_admin_model->fetchTeamList($me, '');
		if (!((int)$this->session->userdata('manager_role') === 1 || $me === 1 || crm_can('all_data') || $id === $me || in_array($id, array_map('intval', (array)$mine)))) {
			$this->session->set_flashdata('error_message', 'You can only see the teams of people in your own team.');
			redirect('manager/team/members');
			return;
		}
		$data['viewing'] = $this->db->query('SELECT id, name FROM ci_user WHERE id = ' . $id)->row_array();
		if (!empty($data['viewing'])) $data['info']['page_heading'] = 'Team of ' . $data['viewing']['name'];
		$data['product_list'] = $this->member_stats($this->ci_admin_model->get_teamMember($id));
		$this->load->view('manager/team/members', $data);
	}

	/** Adds direct reports, whole-team size and online state to each member row. */
	private function member_stats($list)
	{
		$list = (array)$list;
		$kids = array();
		foreach ($this->db->query('SELECT id, manager FROM ci_user WHERE status = 1')->result_array() as $r) {
			$m = (int)$r['manager']; $i = (int)$r['id'];
			if ($m > 0 && $m !== $i) $kids[$m][] = $i;
		}
		$size = function ($id, $seen = array()) use (&$size, $kids) {
			if (empty($kids[$id]) || isset($seen[$id])) return 0;
			$seen[$id] = 1; $n = 0;
			foreach ($kids[$id] as $k) $n += 1 + $size($k, $seen);
			return $n;
		};
		foreach ($list as &$li) {
			$id = (int)$li['id'];
			$li['direct_n'] = isset($kids[$id]) ? count($kids[$id]) : 0;
			$li['team_n'] = $size($id);
			$li['online_now'] = ((int)$li['is_online'] === 1 && !empty($li['last_active_at']) && strtotime($li['last_active_at']) > time() - 600) ? 1 : 0;
			$li['avatar_url'] = (!empty($li['avatar']) && is_file(FCPATH . $li['avatar'])) ? base_url() . $li['avatar'] : '';
		}
		unset($li);
		return $list;
	}


	public function database()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Manager - Database',
			'page_heading' => 'Team Database'
		);
		$data['info2'] = $this->if_not_login();

		$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'A.id';
		$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'DESC';
		$search = isset($_GET['search']) ? $_GET['search'] : '';		

		$d1 = isset($_GET['d1']) ? $_GET['d1'] : (isset($_GET['exp_d1']) ? $_GET['exp_d1'] : '');
		$d2 = isset($_GET['d2']) ? $_GET['d2'] : (isset($_GET['exp_d2']) ? $_GET['exp_d2'] : '');

		$mrr1 = isset($_GET['mrr1']) ? $_GET['mrr1'] : '';
		$mrr2 = isset($_GET['mrr2']) ? $_GET['mrr2'] : '';

		$user_name = isset($_GET['user_name']) ? $_GET['user_name'] : '';	
		$country = isset($_GET['country']) ? $_GET['country'] : '';	
		$state = isset($_GET['state']) ? $_GET['state'] : '';	

		if (!empty($country)) {
			if (is_array($country)) {
				$country = implode(',', $country); 
			}
			$data['state_list'] = $this->comman_model->getAll(array('country_id' => $country), '', 'states');
		}
		if (!empty($state)) {
			if (is_array($state)) {
				$state = implode(',', $state); 
			}
		}

		$status = isset($_GET['status']) ? $_GET['status'] : '';		
		$category = isset($_GET['category']) ? $_GET['category'] : '';
		$lCat = isset($_GET['lCat']) ? $_GET['lCat'] : '';

		if (!empty($lCat)) {
			if (is_array($lCat)) {
				$lCat = implode(',', $lCat); 
			}
		}		
		if (!empty($category)) {
			if (is_array($category)) {
				$category = implode(',', $category); 
			}
		}
		if (!empty($status)) {
			if (is_array($status)) {
				$status = implode(',', $status); 
			}
		}
		$closure = isset($_GET['closure']) ? $_GET['closure'] : '';

		$data['st_list'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_status');
		$data['c_list'] = $this->comman_model->getAll('', array('id', 'ASC'), 'countries');
		$data['pc_list'] = $this->comman_model->getAll(array('cat_type' => 'p_cat', 'parent_id' => 0), '', 'ci_terms');
		$data['lc_list'] = $this->comman_model->getAll(array('cat_type' => 'lead_cat', 'parent_id' => 0), '', 'ci_terms');
		$data['product_list'] = $this->ci_admin_model->getTeamLeads($d1, $d2, $mrr1, $mrr2, '', '', $order_by, $sort_by, $search, $status, $category, $lCat, $user_name, $country, $state, $closure, 'database');
		$data['stage'] = 'database';

		$page2 = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;       
		$con_q = '';
		if (!empty($_SERVER['QUERY_STRING'])) {
			$con_q .= '?' . $_SERVER['QUERY_STRING'];
		}		
		$page_url = $this->uri->segment(2) . "/";
		$page_url1 = $this->uri->segment(3) . "/";
		$this->session->set_userdata('manager_return_string', urlencode($page_url . $page_url1 . $page2 . $con_q)); 	

		$this->load->view('manager/team/leads', $data);
	}

    public function leads()
	{

        $data=array();
		$data['info']=array(
			'title'=>'Manager - Leads',
			'page_heading'=>'Team Leads'
		);
        $data['info2']=$this->if_not_login();
        
        $order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'A.id';
		$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'DESC';
		$search = isset($_GET['search']) ? $_GET['search'] : '';		

		$d1 = isset($_GET['d1']) ? $_GET['d1'] : (isset($_GET['exp_d1']) ? $_GET['exp_d1'] : '');
		$d2 = isset($_GET['d2']) ? $_GET['d2'] : (isset($_GET['exp_d2']) ? $_GET['exp_d2'] : '');

		$mrr1 = isset($_GET['mrr1']) ? $_GET['mrr1'] : '';
		$mrr2 = isset($_GET['mrr2']) ? $_GET['mrr2'] : '';

		$user_name = isset($_GET['user_name']) ? $_GET['user_name'] : '';	
		$country = isset($_GET['country']) ? $_GET['country'] : '';	
		$state = isset($_GET['state']) ? $_GET['state'] : '';	
		
		if(!empty($country)){
			if(is_array($country)){
				$country= implode(',', $country); 
			}
			$data['state_list']= $this->comman_model->getAll(array('country_id'=>$country),'','states');
	   }
		if(!empty($state)){
			if(is_array($state)){
				$state= implode(',', $state); 
			}
		}

		$status = isset($_GET['status']) ? $_GET['status'] : '';		
		$category = isset($_GET['category']) ? $_GET['category'] : '';
		$lCat = isset($_GET['lCat']) ? $_GET['lCat'] : '';
		
		if(!empty($lCat)){
			if(is_array($lCat)){
				$lCat= implode(',', $lCat); 
			}
	     }		

        if(!empty($category)){
			if(is_array($category)){
				$category= implode(',', $category); 
			}
	     }

        if(!empty($status)){
			if(is_array($status)){
				$status= implode(',', $status); 
			}
		 }
		 $closure = isset($_GET['closure']) ? $_GET['closure'] : '';

		 $data['st_list']= $this->comman_model->getAll(array('active'=>1),'','ci_status');
		 $data['c_list'] =$this->comman_model->getAll('',array('id','ASC'),'countries');
		 $data['pc_list']= $this->comman_model->getAll(array('cat_type'=>'p_cat','parent_id'=>0),'','ci_terms');
		 $data['lc_list']= $this->comman_model->getAll(array('cat_type'=>'lead_cat','parent_id'=>0),'','ci_terms');
	  	 $data['product_list']= $this->ci_admin_model->getTeamLeads($d1, $d2, $mrr1, $mrr2, '','',$order_by,$sort_by,$search,$status,$category,$lCat,$user_name,$country,$state,$closure, 'lead');
		 $data['stage'] = 'lead';

		$page2 = ($this->uri->segment(4)) ? $this->uri->segment(4): 0;       
		 	$con_q= '';
			 if(!empty($_SERVER['QUERY_STRING']))
			 {
				$con_q.='?'.$_SERVER['QUERY_STRING'];
			 }		
			$page_url=$this->uri->segment(2)."/";
			$page_url1=$this->uri->segment(3)."/";

			$this->session->set_userdata('manager_return_string', urlencode($page_url.$page_url1.$page2.$con_q)); 	
        
		    
       $this->load->view('manager/team/leads',$data);
    }
	
	public function assignleads()
	{

        $data=array();
		$data['info']=array(
			'title'=>'Manager - Assigned Database',
			'page_heading'=>'Assigned Database'
		);
        $data['info2']=$this->if_not_login();
        
        $order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'A.id';
		$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'DESC';
		$search = isset($_GET['search']) ? $_GET['search'] : '';		

		$d1 = isset($_GET['d1']) ? $_GET['d1'] : (isset($_GET['exp_d1']) ? $_GET['exp_d1'] : '');
		$d2 = isset($_GET['d2']) ? $_GET['d2'] : (isset($_GET['exp_d2']) ? $_GET['exp_d2'] : '');

		$mrr1 = isset($_GET['mrr1']) ? $_GET['mrr1'] : '';
		$mrr2 = isset($_GET['mrr2']) ? $_GET['mrr2'] : '';

		
		$user_name = isset($_GET['user_name']) ? $_GET['user_name'] : '';	
		$country = isset($_GET['country']) ? $_GET['country'] : '';	
		$state = isset($_GET['state']) ? $_GET['state'] : '';	
		
		if(!empty($country)){
			if(is_array($country)){
				$country= implode(',', $country); 
			}
			$data['state_list']= $this->comman_model->getAll(array('country_id'=>$country),'','states');
	   }
		if(!empty($state)){
			if(is_array($state)){
				$state= implode(',', $state); 
			}
		}


		$status = isset($_GET['status']) ? $_GET['status'] : '';		
		$category = isset($_GET['category']) ? $_GET['category'] : '';	
		$lCat = isset($_GET['lCat']) ? $_GET['lCat'] : '';
		
		if(!empty($lCat)){
			if(is_array($lCat)){
				$lCat= implode(',', $lCat); 
			}
	     }	

        if(!empty($category)){
			if(is_array($category)){
				$category= implode(',', $category); 
			}
	     }

        if(!empty($status)){
			if(is_array($status)){
				$status= implode(',', $status); 
			}
		 }
		$data['st_list']= $this->comman_model->getAll(array('active'=>1),'','ci_status');
		$data['c_list'] =$this->comman_model->getAll('',array('id','ASC'),'countries');
		$data['pc_list']= $this->comman_model->getAll(array('cat_type'=>'p_cat','parent_id'=>0),'','ci_terms');
		$data['lc_list']= $this->comman_model->getAll(array('cat_type'=>'lead_cat','parent_id'=>0),'','ci_terms');
		$data['product_list']= $this->ci_admin_model->getTeamAssignLeads($d1, $d2, $mrr1, $mrr2, '','',$order_by,$sort_by,$search,$status,$category,$lCat,$user_name,$country,$state);

		$page2 = ($this->uri->segment(4)) ? $this->uri->segment(4): 0;       
		 	$con_q= '';
			 if(!empty($_SERVER['QUERY_STRING']))
			 {
				$con_q.='?'.$_SERVER['QUERY_STRING'];
			 }		
			$page_url=$this->uri->segment(2)."/";
			$page_url1=$this->uri->segment(3)."/";

			$this->session->set_userdata('manager_return_string', urlencode($page_url.$page_url1.$page2.$con_q)); 	
        
		    
       $this->load->view('manager/team/assignleads',$data);
       
		
    }
    
    public function followups($action = '', $type = 0)
	{

        $data=array();
		$data['info']=array(
			'title'=>'Manager - Follow ups',
			'page_heading'=>'Team Follow ups'
		);
		$data['info2']=$this->if_not_login();
		    
        $order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'A.id';
		$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'DESC';
		$search = isset($_GET['search']) ? $_GET['search'] : '';		

		$d1 = isset($_GET['d1']) ? $_GET['d1'] : '';
		$d2 = isset($_GET['d2']) ? $_GET['d2'] : '';
		
		$mrr1 = isset($_GET['mrr1']) ? $_GET['mrr1'] : '';
		$mrr2 = isset($_GET['mrr2']) ? $_GET['mrr2'] : '';

		
		$user_name = isset($_GET['user_name']) ? $_GET['user_name'] : '';	
		$country = isset($_GET['country']) ? $_GET['country'] : '';	
		$state = isset($_GET['state']) ? $_GET['state'] : '';	
		
		if(!empty($country)){
			if(is_array($country)){
				$country= implode(',', $country); 
			}
			$data['state_list']= $this->comman_model->getAll(array('country_id'=>$country),'','states');
	   }
		if(!empty($state)){
			if(is_array($state)){
				$state= implode(',', $state); 
			}
		}

		$status = isset($_GET['status']) ? $_GET['status'] : '';		
		$category = isset($_GET['category']) ? $_GET['category'] : '';	
		$type = isset($_GET['type']) ? $_GET['type'] : 0;
		$lCat = isset($_GET['lCat']) ? $_GET['lCat'] : '';
		
		if(!empty($lCat)){
			if(is_array($lCat)){
				$lCat= implode(',', $lCat); 
			}
	     }		

        if(!empty($category)){
			if(is_array($category)){
				$category= implode(',', $category); 
			}
	     }

        if(!empty($status)){
			if(is_array($status)){
				$status= implode(',', $status); 
			}
		 }
		$data['st_list']= $this->comman_model->getAll(array('active'=>1),'','ci_status');
		$data['c_list'] =$this->comman_model->getAll('',array('id','ASC'),'countries');
		$data['pc_list']= $this->comman_model->getAll(array('cat_type'=>'p_cat','parent_id'=>0),'','ci_terms');
		$data['lc_list']= $this->comman_model->getAll(array('cat_type'=>'lead_cat','parent_id'=>0),'','ci_terms');
		$data['product_list']= $this->ci_admin_model->getTeamFollowups($d1, $d2, $mrr1, $mrr2, '','',$order_by,$sort_by,$search,$status,$category,$type,$lCat,$user_name,$country,$state);		
		$page2 = ($this->uri->segment(4)) ? $this->uri->segment(4): 0;       
		 	$con_q= '';
			 if(!empty($_SERVER['QUERY_STRING']))
			 {
				$con_q.='?'.$_SERVER['QUERY_STRING'];
			 }		
			$page_url=$this->uri->segment(2)."/";
			$page_url1=$this->uri->segment(3)."/";

			$this->session->set_userdata('manager_return_string', urlencode($page_url.$page_url1.$page2.$con_q)); 
        $this->load->view('manager/team/leads_followup',$data);
       
		
    }
    

    public function meetings()
	{

        $data=array();
		$data['info']=array(
			'title'=>'Manager - Meetings',
			'page_heading'=>'Team Meetings'
		);
		$data['info2']=$this->if_not_login();
         
        $order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'A.id';
		$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'DESC';
		$search = isset($_GET['search']) ? $_GET['search'] : '';		

		$d1 = isset($_GET['d1']) ? $_GET['d1'] : '';
		$d2 = isset($_GET['d2']) ? $_GET['d2'] : '';
		
		$mrr1 = isset($_GET['mrr1']) ? $_GET['mrr1'] : '';
		$mrr2 = isset($_GET['mrr2']) ? $_GET['mrr2'] : '';

				
		$user_name = isset($_GET['user_name']) ? $_GET['user_name'] : '';	
		$country = isset($_GET['country']) ? $_GET['country'] : '';	
		$state = isset($_GET['state']) ? $_GET['state'] : '';	
		
		if(!empty($country)){
			if(is_array($country)){
				$country= implode(',', $country); 
			}
			$data['state_list']= $this->comman_model->getAll(array('country_id'=>$country),'','states');
	   }
		if(!empty($state)){
			if(is_array($state)){
				$state= implode(',', $state); 
			}
		}

		$status = isset($_GET['status']) ? $_GET['status'] : '';		
		$category = isset($_GET['category']) ? $_GET['category'] : '';	
		$type = isset($_GET['type']) ? $_GET['type'] : 0;
		$lCat = isset($_GET['lCat']) ? $_GET['lCat'] : '';
		
		if(!empty($lCat)){
			if(is_array($lCat)){
				$lCat= implode(',', $lCat); 
			}
	     }	
		 
        if(!empty($category)){
			if(is_array($category)){
				$category= implode(',', $category); 
			}
	     }
		 
        if(!empty($status)){
			if(is_array($status)){
				$status= implode(',', $status); 
			}
		 }
		$data['st_list']= $this->comman_model->getAll(array('active'=>1),'','ci_status');
		$data['c_list'] =$this->comman_model->getAll('',array('id','ASC'),'countries');
		$data['pc_list']= $this->comman_model->getAll(array('cat_type'=>'p_cat','parent_id'=>0),'','ci_terms');
		$data['lc_list']= $this->comman_model->getAll(array('cat_type'=>'lead_cat','parent_id'=>0),'','ci_terms');
		$data['product_list']= $this->ci_admin_model->getTeamLeadsMeetings($d1, $d2, $mrr1, $mrr2, '','',$order_by,$sort_by,$search,$status,$category,$type,$lCat,$user_name,$country,$state);
		$page2 = ($this->uri->segment(4)) ? $this->uri->segment(4): 0;
      	$con_q= '';
		if(!empty($_SERVER['QUERY_STRING'])){
				$con_q.='?'.$_SERVER['QUERY_STRING'];
		}		 
		$page_url=$this->uri->segment(2)."/";
		$page_url1=$this->uri->segment(3)."/";
        $this->session->set_userdata('manager_return_string', urlencode($page_url.$page_url1.$page2.$con_q));
        
       $this->load->view('manager/team/meetings',$data);
       
		
    }

    public function assignmeetings($type=0)
	{

        $data=array();
		$data['info']=array(
			'title'=>'Manager - Assigned Meetings',
			'page_heading'=>'Assigned Meetings'
		);
		$data['info2']=$this->if_not_login();
        
        $order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'A.id';
		$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'DESC';
		$search = isset($_GET['search']) ? $_GET['search'] : '';		

		$d1 = isset($_GET['d1']) ? $_GET['d1'] : '';
		$d2 = isset($_GET['d2']) ? $_GET['d2'] : '';
		
		$mrr1 = isset($_GET['mrr1']) ? $_GET['mrr1'] : '';
		$mrr2 = isset($_GET['mrr2']) ? $_GET['mrr2'] : '';		
				
		$user_name = isset($_GET['user_name']) ? $_GET['user_name'] : '';	
		$country = isset($_GET['country']) ? $_GET['country'] : '';	
		$state = isset($_GET['state']) ? $_GET['state'] : '';	
		
		if(!empty($country)){
			if(is_array($country)){
				$country= implode(',', $country); 
			}
			$data['state_list']= $this->comman_model->getAll(array('country_id'=>$country),'','states');
	   }
		if(!empty($state)){
			if(is_array($state)){
				$state= implode(',', $state); 
			}
		}

		$status = isset($_GET['status']) ? $_GET['status'] : '';		
		$category = isset($_GET['category']) ? $_GET['category'] : '';	
		$type = isset($_GET['type']) ? $_GET['type'] : 0;
		$lCat = isset($_GET['lCat']) ? $_GET['lCat'] : '';
		
		if(!empty($lCat)){
			if(is_array($lCat)){
				$lCat= implode(',', $lCat); 
			}
	     }	
		 
        if(!empty($category)){
			if(is_array($category)){
				$category= implode(',', $category); 
			}
	     }
		 
        if(!empty($status)){
			if(is_array($status)){
				$status= implode(',', $status); 
			}
		 }
		$data['st_list']= $this->comman_model->getAll(array('active'=>1),'','ci_status');
		$data['c_list'] =$this->comman_model->getAll('',array('id','ASC'),'countries');
		$data['pc_list']= $this->comman_model->getAll(array('cat_type'=>'p_cat','parent_id'=>0),'','ci_terms');
		$data['lc_list']= $this->comman_model->getAll(array('cat_type'=>'lead_cat','parent_id'=>0),'','ci_terms');
		$data['product_list']= $this->ci_admin_model->getTeamAssignedMeetings($d1, $d2, $mrr1, $mrr2, '','',$order_by,$sort_by,$search,$status,$category,$type, $lCat,$user_name,$country,$state);
		$page2 = ($this->uri->segment(4)) ? $this->uri->segment(4): 0;
      	$con_q= '';
		if(!empty($_SERVER['QUERY_STRING'])){
				$con_q.='?'.$_SERVER['QUERY_STRING'];
		}		 
		$page_url=$this->uri->segment(2)."/";
		$page_url1=$this->uri->segment(3)."/";
        $this->session->set_userdata('manager_return_string', urlencode($page_url.$page_url1.$page2.$con_q));

      
       $this->load->view('manager/team/meetings_assign.php',$data);
       
		
    }

	public function chart()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Manager - Team Chart',
			'page_heading' => 'Team Chart'
		);
		$data['info2'] = $this->if_not_login();

		$me = (int)$this->session->userdata('manager_id');
		$role = (int)$this->session->userdata('manager_role');
		$see_all = ($role === 1 || $me === 1 || crm_can('all_data'));

		// Who is on the chart: Admin / "All company data" see the whole company; everyone else sees
		// themselves, the people under them, and (for context only) the person they report to.
		$rows = $this->db->query('SELECT U.id, U.name, U.manager, U.department, U.office, U.avatar, U.email, U.mobile, U.is_online, U.last_active_at, R.title AS role'
			. ' FROM ci_user U LEFT JOIN ci_roles R ON R.id = U.user_type WHERE U.status = 1 ORDER BY U.name ASC')->result_array();
		$by_id = array();
		foreach ($rows as $r) $by_id[(int)$r['id']] = $r;

		$ctx = 0;
		if ($see_all) {
			$keep = array_keys($by_id);
		} else {
			$keep = array_map('intval', (array)$this->ci_admin_model->fetchTeamList($me, ''));
			$keep[] = $me;
			if (isset($by_id[$me])) {
				$m = (int)$by_id[$me]['manager'];
				if ($m > 0 && $m !== $me && isset($by_id[$m]) && !in_array($m, $keep, true)) {
					$keep[] = $m;
					$ctx = $m;
				}
			}
		}
		$keep = array_flip(array_unique($keep));

		$people = array();
		foreach ($by_id as $id => $r) {
			if (!isset($keep[$id])) continue;
			$mgr = (int)$r['manager'];
			if ($mgr === $id || !isset($keep[$mgr])) $mgr = 0; // top of the visible chart
			$avatar = '';
			if (!empty($r['avatar']) && is_file(FCPATH . $r['avatar'])) $avatar = base_url() . $r['avatar'];
			$online = ((int)$r['is_online'] === 1 && !empty($r['last_active_at']) && strtotime($r['last_active_at']) > time() - 600);
			$people[] = array(
				'id' => $id,
				'm' => $mgr,
				'n' => (string)$r['name'],
				'r' => (string)$r['role'],
				'd' => (string)$r['department'],
				'o' => (string)$r['office'],
				'e' => (string)$r['email'],
				'p' => (string)$r['mobile'],
				'a' => $avatar,
				'on' => $online ? 1 : 0,
				'ctx' => ($id === $ctx) ? 1 : 0,
			);
		}

		$focus = isset($_GET['id']) ? (int)$_GET['id'] : 0;
		if (!isset($keep[$focus])) $focus = 0;

		$data['people'] = $people;
		$data['me'] = $me;
		$data['focus'] = $focus;
		$data['see_all'] = $see_all;
		$this->load->view('manager/team/chart', $data);
	}

	public function orders()
	{
		redirect('manager/orders/team');
	}

	public function sales()
	{
		redirect('manager/sales');
	}
}