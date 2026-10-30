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
			'title' => 'Users',
			'page_heading' => 'My Team Members'
		);
		$data['info2'] = $this->if_not_login();
		$data['product_list'] = $this->ci_admin_model->get_teamMember($this->session->userdata('manager_id'));
		$this->load->view('manager/team/members', $data);
	}

	public function membersdetails($id = '')
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Users',
			'page_heading' => 'My Team Members'
		);
		$data['info2'] = $this->if_not_login();
		$data['product_list'] = $this->ci_admin_model->get_teamMember($id);
		$this->load->view('manager/team/members', $data);
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
			'title' => 'Users',
			'page_heading' => 'My Team Members Chart'
		);
		$data['info2'] = $this->if_not_login();

		$logged_in_id = (int)$this->session->userdata('manager_id');
		$logged_in_role = (int)$this->session->userdata('manager_role');
		$is_admin = ($logged_in_role === 1 || $logged_in_id === 1);

		$logged_in_user = $this->db->select('id, name, manager, department, office, avatar, user_type')->where('id', $logged_in_id)->get('ci_user')->row_array();
		$top_manager_id = (!empty($logged_in_user['manager']) && (int)$logged_in_user['manager'] !== $logged_in_id) ? (int)$logged_in_user['manager'] : 0;

		$req_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
		if ($req_id === 0) {
			$req_id = $logged_in_id;
		}

		$is_subordinate = function($parent_id, $child_id) use (&$is_subordinate) {
			if ($parent_id === $child_id) return true;
			$directs = $this->db->select('id')->from('ci_user')->where('manager', (int)$parent_id)->where('status', 1)->get()->result_array();
			foreach ($directs as $d) {
				$sub_id = (int)$d['id'];
				if ($sub_id === (int)$child_id) return true;
				if ($is_subordinate($sub_id, $child_id)) return true;
			}
			return false;
		};

		if (!$is_admin && $req_id !== $logged_in_id && $req_id !== $top_manager_id) {
			if (!$is_subordinate($logged_in_id, $req_id)) {
				$req_id = $logged_in_id;
			}
		}

		$target_user = $this->comman_model->getpostdata2(array('id' => $req_id), '', 'ci_user');
		if (empty($target_user)) {
			$target_user = $logged_in_user;
			$req_id = $logged_in_id;
		}

		$data['main_user'] = $target_user;
		$main_user_id = (int)$target_user['id'];
		$data['logged_in_id'] = $logged_in_id;
		$data['is_viewing_self'] = ($main_user_id === $logged_in_id);
		$data['is_viewing_manager'] = ($main_user_id === $top_manager_id && $top_manager_id > 0);

		$manager_node = null;
		$manager_nav_id = 0;

		if ($main_user_id === $logged_in_id) {
			if ($top_manager_id > 0) {
				$manager_node = $this->db->select('id, name, department, office, avatar, user_type')->where('id', $top_manager_id)->get('ci_user')->row_array();
				$manager_nav_id = $top_manager_id;
			}
		} elseif ($main_user_id === $top_manager_id) {
			$manager_node = null;
			$manager_nav_id = 0;
		} else {
			$t_mgr_id = !empty($target_user['manager']) ? (int)$target_user['manager'] : 0;
			if ($t_mgr_id > 0) {
				$manager_node = $this->db->select('id, name, department, office, avatar, user_type')->where('id', $t_mgr_id)->get('ci_user')->row_array();
				$manager_nav_id = ($t_mgr_id === $logged_in_id) ? 0 : $t_mgr_id;
			}
		}

		$data['manager_node'] = $manager_node;
		$data['manager_nav_id'] = $manager_nav_id;

		$tree_builder = function($user_id, $depth = 0, $max_depth = 5) use (&$tree_builder) {
			if ($depth > $max_depth || empty($user_id)) {
				return array();
			}
			$subs = $this->db->select('id, name, manager, department, office, avatar, user_level, user_type')
				->from('ci_user')
				->where('manager', (int)$user_id)
				->where('id !=', (int)$user_id)
				->where('status', 1)
				->order_by('name', 'ASC')
				->get()
				->result_array();
			$nodes = array();
			foreach ($subs as $sub) {
				$sub['children'] = $tree_builder($sub['id'], $depth + 1, $max_depth);
				$nodes[] = $sub;
			}
			return $nodes;
		};

		if ($main_user_id === $top_manager_id && !$is_admin) {
			$logged_in_node = $logged_in_user;
			$logged_in_node['children'] = $tree_builder($logged_in_id, 0, 5);
			$children_list = array($logged_in_node);
		} else {
			$children_list = $tree_builder($main_user_id, 0, 5);
		}

		$data['team_tree'] = array(
			'id' => $main_user_id,
			'name' => isset($target_user['name']) ? $target_user['name'] : '',
			'manager' => isset($target_user['manager']) ? (int)$target_user['manager'] : 0,
			'department' => isset($target_user['department']) ? $target_user['department'] : '',
			'office' => isset($target_user['office']) ? $target_user['office'] : '',
			'avatar' => isset($target_user['avatar']) ? $target_user['avatar'] : '',
			'user_type' => isset($target_user['user_type']) ? $target_user['user_type'] : '',
			'children' => $children_list
		);

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