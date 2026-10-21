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
		
	   public function members()
	{
		
        $data=array();
		$data['info']=array(
			'title'=>'Users',
			'page_heading'=>'My Team Members'
		);
		$data['info2']=$this->if_not_login();
		$data['product_list']= $this->ci_admin_model->get_teamMember($this->session->userdata('manager_id'));
		$this->load->view('manager/team/members',$data);
	}
	public function membersdetails($id)
	{
		
        $data=array();
		$data['info']=array(
			'title'=>'Users',
			'page_heading'=>'My Team Members'
		);
		$data['info2']=$this->if_not_login();
		$data['product_list']= $this->ci_admin_model->get_teamMember($id);
		$this->load->view('manager/team/members',$data);
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
	  	 $data['product_list']= $this->ci_admin_model->getTeamLeads($d1, $d2, $mrr1, $mrr2, '','',$order_by,$sort_by,$search,$status,$category,$lCat,$user_name,$country,$state,$closure);

		$page2 = ($this->uri->segment(4)) ? $this->uri->segment(4): 0;       
		 	$con_q= '';
			 if(!empty($_SERVER['QUERY_STRING']))
			 {
				$con_q.='?'.$_SERVER['QUERY_STRING'];
			 }		
			$page_url=$this->uri->segment(2)."/";
			$page_url1=$this->uri->segment(3)."/";

			$this->session->set_userdata('manager_return_string', urlencode($page_url.$page_url1.$page2.$con_q)); 	
        
		    
        //$data['product_list']= $this->ci_admin_model->getTeamLeadsFixed($type);
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

		$d1 = isset($_GET['d1']) ? $_GET['d1'] : '';
		$d2 = isset($_GET['d2']) ? $_GET['d2'] : '';

		$mrr1 = isset($_GET['mrr1']) ? $_GET['mrr1'] : '';
		$mrr2 = isset($_GET['mrr2']) ? $_GET['mrr2'] : '';

		
		$user_name = isset($_GET['user_name']) ? $_GET['user_name'] : '';	
		$country = isset($_GET['country']) ? $_GET['country'] : '';	
		$state = isset($_GET['state']) ? $_GET['state'] : '';	
		
		if(!empty($country)){
			$country= implode(',', $country); 
			$data['state_list']= $this->comman_model->getAll(array('country_id'=>$country),'','states');
	   }
		if(!empty($state)){
			 $state= implode(',', $state); 
		}


		$status = isset($_GET['status']) ? $_GET['status'] : '';		
		$category = isset($_GET['category']) ? $_GET['category'] : '';	
		$lCat = isset($_GET['lCat']) ? $_GET['lCat'] : '';
		
		if(!empty($lCat)){
	         $lCat= implode(',', $lCat); 
	     }	

        if(!empty($category)){
	         $category= implode(',', $category); 
	     }

        if(!empty($status)){
	         $status= implode(',', $status); 
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
        
		    
        //$data['product_list']= $this->ci_admin_model->getTeamLeadsFixed($type);
       $this->load->view('manager/team/assignleads',$data);
       
		
    }
    
    public function followups($action, $type=0)
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
			$country= implode(',', $country); 
			$data['state_list']= $this->comman_model->getAll(array('country_id'=>$country),'','states');
	   }
		if(!empty($state)){
			 $state= implode(',', $state); 
		}

		$status = isset($_GET['status']) ? $_GET['status'] : '';		
		$category = isset($_GET['category']) ? $_GET['category'] : '';	
		$type = isset($_GET['type']) ? $_GET['type'] : 0;
		$lCat = isset($_GET['lCat']) ? $_GET['lCat'] : '';
		
		if(!empty($lCat)){
	         $lCat= implode(',', $lCat); 
	     }		

        if(!empty($category)){
	         $category= implode(',', $category); 
	     }

        if(!empty($status)){
	         $status= implode(',', $status); 
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
			$country= implode(',', $country); 
			$data['state_list']= $this->comman_model->getAll(array('country_id'=>$country),'','states');
	   }
		if(!empty($state)){
			 $state= implode(',', $state); 
		}

		$status = isset($_GET['status']) ? $_GET['status'] : '';		
		$category = isset($_GET['category']) ? $_GET['category'] : '';	
		$type = isset($_GET['type']) ? $_GET['type'] : 0;
		$lCat = isset($_GET['lCat']) ? $_GET['lCat'] : '';
		
		if(!empty($lCat)){
	         $lCat= implode(',', $lCat); 
	     }	
		 
        if(!empty($category)){
	         $category= implode(',', $category); 
	     }
		 
        if(!empty($status)){
	         $status= implode(',', $status); 
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
        
       // $data['product_list']= $this->ci_admin_model->getTeamLeadsMeetings($type);
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
			$country= implode(',', $country); 
			$data['state_list']= $this->comman_model->getAll(array('country_id'=>$country),'','states');
	   }
		if(!empty($state)){
			 $state= implode(',', $state); 
		}

		$status = isset($_GET['status']) ? $_GET['status'] : '';		
		$category = isset($_GET['category']) ? $_GET['category'] : '';	
		$type = isset($_GET['type']) ? $_GET['type'] : 0;
		$lCat = isset($_GET['lCat']) ? $_GET['lCat'] : '';
		
		if(!empty($lCat)){
	         $lCat= implode(',', $lCat); 
	     }	
		 
        if(!empty($category)){
	         $category= implode(',', $category); 
	     }
		 
        if(!empty($status)){
	         $status= implode(',', $status); 
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
		
        $data=array();
		$data['info']=array(
			'title'=>'Users',
			'page_heading'=>'My Team Members Chart'
		);
		
		$data['info2']=$this->if_not_login();
		$id = isset($_GET['id']) ? $_GET['id'] : '';
		if(empty($id)){
			$data['main_user'] = $this->comman_model->getpostdata2(array('id'=>$this->session->userdata('manager_id')),'','ci_user');
		}else{
			$data['main_user'] = $this->comman_model->getpostdata2(array('id'=>$id),'','ci_user');

		}
	    $data['List2']= $this->comman_model->getAll(array('manager'=>$data['main_user']['id']),'','ci_user');
		$data['back_user'] = $this->comman_model->getpostdata2(array('id'=>$data['main_user']['id']),'','ci_user');

		$this->load->view('manager/team/chart',$data);
	}
	

}