<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ci_admin_leads extends CI_Controller {
function __construct() 
	{
    parent::__construct();
	$this->load->library(array('Session','Pagination','User_agent'));
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
		   $data['back_url'] = $this->agent->referrer();
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
			'title'=>'Manager - Leads',
			'page_heading'=>'All Leads'
		);
		$data['info2']=$this->if_not_login();
		
		$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'A.id';
		$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'DESC';
		$search = isset($_GET['search']) ? $_GET['search'] : '';	

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

		$d1 = isset($_GET['d1']) ? $_GET['d1'] : '';
		$d2 = isset($_GET['d2']) ? $_GET['d2'] : '';

		$mrr1 = isset($_GET['mrr1']) ? $_GET['mrr1'] : '';
		$mrr2 = isset($_GET['mrr2']) ? $_GET['mrr2'] : '';

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

		$closure = isset($_GET['closure']) ? $_GET['closure'] : '';
		$vertical_fu = isset($_GET['vertical_fu']) ? $_GET['vertical_fu'] : '';

		$data['st_list']= $this->comman_model->getAll(array('active'=>1),'','ci_status');
		$data['c_list'] =$this->comman_model->getAll('',array('id','ASC'),'countries');
		$data['pc_list']= $this->comman_model->getAll(array('cat_type'=>'p_cat','parent_id'=>0),'','ci_terms');
		$data['lc_list']= $this->comman_model->getAll(array('cat_type'=>'lead_cat','parent_id'=>0),'','ci_terms');
		$data['product_list']= $this->ci_admin_model->get_Myleads($d1, $d2, $mrr1, $mrr2, '','',$order_by,$sort_by,$search,$status,$category,$lCat,$user_name,$country,$state,$closure,$vertical_fu);
		//print_r($data['product_list']);
		$con_q= '';
		 if(!empty($_SERVER['QUERY_STRING']))
		{
			$con_q.='?'.$_SERVER['QUERY_STRING'];
		}		
		$page_url=$this->uri->segment(2)."/";
		$page2 = ($this->uri->segment(4)) ? $this->uri->segment(4): 0;
		$this->session->set_userdata('manager_return_string', urlencode($page_url.$page2.$con_q));		
		
		$this->load->view('manager/leads/leadsview',$data);
	}	

	public function ajax_get_state()
	{

	   $str='<option value="" disabled>Select State</option>';
	  //Ajax call for Sub product where values are going to be fetch by parent_id
	   if(isset($_POST['country_id'])&&!empty($_POST['country_id']))
	   {   
	   $data['state_list']= $this->comman_model->getAll(array('country_id'=>$_POST['country_id']),'','states');
	   foreach($data['state_list'] as $sub)
	   {
		   $str.="<option value=".$sub['id'].">".$sub['name']."</option>";
	   }
	   }
	   echo $str;
	}

	public function followups()
	{

        $data=array();
		$data['info']=array(
			'title'=>'Manager - Leads',
			'page_heading'=>'All Follow ups'
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
		$data['product_list']= $this->ci_admin_model->getLeadsFollowups($d1, $d2, $mrr1, $mrr2, '','',$order_by,$sort_by,$search,$status,$category,$type,$lCat,$user_name,$country,$state);		
		$page2 = ($this->uri->segment(4)) ? $this->uri->segment(4): 0;       
		 	$con_q= '';
			 if(!empty($_SERVER['QUERY_STRING']))
			 {
				$con_q.='?'.$_SERVER['QUERY_STRING'];
			 }		
			$page_url=$this->uri->segment(2)."/";
			$page_url1=$this->uri->segment(3)."/";

			$this->session->set_userdata('manager_return_string', urlencode($page_url.$page_url1.$page2.$con_q));       
		$this->load->view('manager/leads/leadsview',$data);
	}


	public function meetings()
	{
		

        $data=array();
		$data['info']=array(
			'title'=>'Manager - Meetings',
			'page_heading'=>'All Meetings'
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
		$data['product_list']= $this->ci_admin_model->getLeadsMeetings($d1, $d2, $mrr1, $mrr2, '','',$order_by,$sort_by,$search,$status,$category,$type,$lCat,$user_name,$country,$state);
		$page2 = ($this->uri->segment(4)) ? $this->uri->segment(4): 0;
      	$con_q= '';
		if(!empty($_SERVER['QUERY_STRING'])){
				$con_q.='?'.$_SERVER['QUERY_STRING'];
		}		 
		$page_url=$this->uri->segment(2)."/";
		$page_url1=$this->uri->segment(3)."/";
		$this->session->set_userdata('manager_return_string', urlencode($page_url.$page_url1.$page2.$con_q));
		$this->load->view('manager/leads/leadsview',$data);
	}


	public function ajaxFetchLeads()
		{
			
			$data=array();
			$data['info']=array(
				'title'=>'Manager - Leads',
				'page_heading'=>'All Leads'
			);
			$data['info2']=$this->if_not_login();		
			$data['product_list']= $this->ci_admin_model->get_Myleads_ajax(); 

			
			$json = array(
				"draw" => $draw,
                "recordsTotal" => 4,
                "recordsFiltered" => 4,
				"data" => $data['product_list'],								
				"msg" => "success"
		);

			header('Content-type: application/json');
				array_walk_recursive($json, function(&$item, $key) {
						$item = null === $item ? '' : $item;
				});
				echo json_encode($json);
			
	}
		public function view($id='',$action='') //userloyee id

		{

			 $data=array();
				$data['info']=array(
					'title'=>'Manager - Leads',
					'page_heading'=>'Preview Lead Information'
			);
			$data['info2']=$this->if_not_login();

			$id= base64_decode($id);
			$data['p_info']=$this->ci_admin_model->getSingleLead($id);
			$data['p_team']= $this->ci_admin_model->fetchTeamList($this->session->userdata('manager_id'),'');
			if(!empty($data['p_info'])){
				$data['bo_list']=$this->comman_model->getAll(array('lead_id'=>$id),'','ci_boleads');
				$data['follow_list']=$this->comman_model->getAll(array('lead_id'=>$id,'lead_type'=>'lead'),'','ci_followup');
				$data['m_list']=$this->comman_model->getAll(array('lead_id'=>$id),array('id','desc'),'ci_meetings','first');
				$data['action']= $action;
    	 	   if(empty($data['p_info']))

			   {
			   		$this->session->set_flashdata('error_message', 'Not allowed');
					   $return_url= urldecode($this->session->userdata('manager_return_string'));
					   redirect('/manager/'.$return_url);
			   } 
			}else{
				$this->session->set_flashdata('error_message', 'Requested lead not exsit');
				$return_url= urldecode($this->session->userdata('manager_return_string'));
				redirect('/manager/'.$return_url);
			}
	           $this->load->view('manager/leads/single_view',$data);

}
	//to add category

	public function add()

	{

		

		$data=array();
		$data['info']=array(
			'title'=>'Manager- Leads',
			'page_heading'=>'Add Lead'
		);
		$data['info2']=$this->if_not_login();

		$data['st_list']= $this->comman_model->getAll(array('active'=>1),'','ci_status');
		$data['so_list']= $this->comman_model->getAll(array('active'=>1),'','ci_sources');
		$data['p_cat']= $this->comman_model->getAll(array('parent_id'=>0,'status'=>1,'cat_type'=>'p_cat'),'','ci_terms');
		$data['lc_list']= $this->comman_model->getAll(array('cat_type'=>'lead_cat','parent_id'=>0),'','ci_terms');
		$data['c_list'] =$this->comman_model->getAll('',array('id','ASC'),'countries');  
		$data['u_list'] =$this->comman_model->getAll('',array('id','ASC'),'ci_user');
		$data['v_list'] =$this->ci_admin_model->getShortVerticals($this->session->userdata('manager_id'));
		$data['business_categories'] = $this->comman_model->getAll(array('cat_type'=>'business_cat','status'=>1),'','ci_terms');

		if($this->input->server('REQUEST_METHOD')==='POST')
		{	 			

				extract($_POST);

				$cur_role = $this->session->userdata('manager_role');
				if ($cur_role != 1 && $cur_role != 4) {
					$lead_assign = (int)$this->session->userdata('manager_id');
				} else {
					$lead_assign = isset($lead_assign) ? (int)$lead_assign : 0;
				}

				$f_date = !empty($followup_date) ? db_date($followup_date) : '';
				$c_date = !empty($close_date) ? db_date($close_date) : '';
				if(!empty($f_date) && !empty($c_date)){
					if(strtotime($c_date) < strtotime($f_date)){
						$c_date = $f_date; // Exp. Close Date can never be previous or past of follow up dates
					}
				}

				$list= array(				
				'lead_id'=> 100000000,
				'customer'=>$customer,
				'category'=>isset($category) ? $category : '',
				'vertical_id'=>$vertical_id,
				'a_number'=>$a_number,
				'a_department'=>$a_department,
				'r_amount'=>$r_amount,
				'r_date'=>db_date($r_date),
				's_cat'=>$s_cat,
				'p_cat'=>$p_cat,
				'p_details'=>$p_details,
				'email'=>$email,
				'qty'=>$qty,
				'mrc'=>$mrc,
				'revenue'=>$revenue,
				'mrr'=>(floatval($mrc)+floatval($revenue)),
				'poc'=>$poc,
				'contact1'=>$contact1,
				'alt_mobile'=>!empty($alt_contact) ? $alt_contact : (isset($alt_mobile) ? $alt_mobile : ''),
				'alt_contact'=>!empty($alt_contact) ? $alt_contact : (isset($alt_mobile) ? $alt_mobile : ''),
				'business_category'=>isset($business_category) ? $business_category : '',
				'dnd_status'=>isset($dnd_status) ? $dnd_status : 'No',
				'dnd_check_date'=>!empty($dnd_check_date) ? db_date($dnd_check_date) : '',
				'location'=>$location,
				'status'=>$status,
				'lost_reason'=>($status == 43) ? trim($this->input->post('lost_reason')) : null,

				'followup_date'=>$f_date,
				'followup_time'=>$followup_time,

				'm_date'=>db_date($m_date),
				'm_time'=>$m_time,

				'close_date'=>$c_date,
				'remark'=>$remark,

				'address'=>$address,
				'city'=>$city,
				'country'=>$country,
				'state'=>$state,				
				'poc2'=>$poc2,
				'contact2'=>$contact2,
				'c_partner'=>$c_partner,
				'otc'=>$otc,
				'lead_cat'=>$lead_cat,
				'lead_assign'=>$lead_assign,
				'lead_source'=>$lead_source,

				'tl_number'=>isset($tl_number) ? $tl_number : '',
				'tl_authority'=>isset($tl_authority) ? $tl_authority : '',
				'tl_issue_date'=>!empty($tl_issue_date) ? db_date($tl_issue_date) : '',
				'tl_expire_date'=>!empty($tl_expire_date) ? db_date($tl_expire_date) : '',

				'v_amount'=>$v_amount,
				'v_remark'=>$v_remark,


				//'c_code'=>$c_code,				
				//'region'=>$region,
				//'acc_mgr'=>$acc_mgr,				 
				//'p_name'=>$p_name,				
				//'contact_details'=>$contact_details,				
				//'meetings'=>$meetings,
				//'reference'=>$reference,
				//'followup_by'=>$followup_by,
				
				'created_by'=>$this->session->userdata('manager_id'),
				'admin_status'=>1,
				'created'=>date('Y-m-d')			

				);		
				$this->session->set_flashdata('message', 'Lead has been added successfully!');
				 $lead_id = $this->comman_model->saveData('ci_leads',$list);
				 
				$this->comman_model->updateData('id',$lead_id,array('lead_id'=>100000000+$lead_id),'ci_leads');

		     	if(!empty($bo_cat)){

		     		for($i=0;$i<=6;$i++){
		     			$so= array(
		     				'lead_id'=>$lead_id,
		     				'bo_cat'=>$bo_cat[$i],
		     				'bo_date'=>$bo_date[$i],
		     				'bo_status'=>$bo_status[$i],
		     				'bo_ref'=>$bo_ref[$i],
		     				'bo_remark'=>$bo_remark[$i]		     				
		     			);
		     			$this->comman_model->saveData('ci_boleads',$so);
		     		}
				 }	
				 
				 if(!empty($followup_date)){
				
						$so= array(
							'lead_id'=>$lead_id,
							'lead_type'=>'lead',
							'followup_date'=>db_date($followup_date),
							'followup_time'=>$followup_time,
							'followup_remark'=>$followup_remark,
							'user_id'=>$this->session->userdata('manager_id')		     				
						);
						$this->comman_model->saveData('ci_followup',$so);
				
				}

				if(!empty($m_date)){
				
					$mo= array(
						'lead_id'=>$lead_id,						
						'm_date'=>db_date($m_date),
						'm_time'=>$m_time,
						'm_remark'=>$m_remark,
						'm_poc'=>$m_poc,
						'm_mobile'=>$m_mobile,
					//	'm_address'=>$m_address,
						'a1'=>$a1,
						'a2'=>$a2,
						'created'=>date('Y-m-d'),
						'created_by'=>$this->session->userdata('manager_id')		     				
					);
					$this->comman_model->saveData('ci_meetings',$mo);
			
			}

			 redirect('manager/leads');

       }

        $this->load->view('manager/leads/addleads',$data);	

   

	   

	}

		//edit category

     public function edit($id='',$action='')

	 {       	

		$data=array();
		$data['info']=array(
			'title'=>'Manager- Leads',
			'page_heading'=>'Edit Lead'
		);
		$data['info2']=$this->if_not_login();
		$id= base64_decode($id);		      
		$data['list']=$this->comman_model->getAll(array('id'=>$id),'','ci_leads','first');
		
		$data['slist']=$this->ci_admin_model->getSingleLead($id);
		$data['v_list'] =$this->ci_admin_model->getShortVerticals($this->session->userdata('manager_id'));
		//print_r($data['slist']);
		//die;

		if(empty($data['list'])){
			$this->session->set_flashdata('error_message', 'Not allowed');
			if($action=='tm'){
				redirect('/manager/team/leads');
			}else{
				redirect('/manager/leads');
			}
		}  
		
			if($data['slist']['user_level']<$this->session->userdata('manager_level')){
				$this->session->set_flashdata('error_message', 'Not allowed');
				if($action=='tm'){
					redirect('/manager/team/leads');
				}else{
					redirect('/manager/leads');
				}
			}
			
			if(!empty($data['list']['country'])){
				$data['state_list']= $this->comman_model->getAll(array('country_id'=>$data['list']['country']),'','states');
			}  
		$data['st_list']= $this->comman_model->getAll(array('active'=>1),'','ci_status');
		$data['so_list']= $this->comman_model->getAll(array('active'=>1),'','ci_sources');
		$data['p_cat']= $this->comman_model->getAll(array('parent_id'=>0,'status'=>1,'cat_type'=>'p_cat'),'','ci_terms');
		$data['lc_list']= $this->comman_model->getAll(array('cat_type'=>'lead_cat','parent_id'=>0),'','ci_terms');
		if(!empty($data['list']['s_cat'])){
			$data['scat_list']= $this->comman_model->getAll(array('parent_id'=>$data['list']['s_cat'],'status'=>1,'cat_type'=>'p_cat'),'','ci_terms');
		} 
		$data['c_list'] =$this->comman_model->getAll('',array('id','ASC'),'countries');			
		$data['bo_list']=$this->comman_model->getAll(array('lead_id'=>$id),'','ci_boleads');
		$data['u_list'] =$this->comman_model->getAll('',array('id','ASC'),'ci_user');
		$data['business_categories'] = $this->comman_model->getAll(array('cat_type'=>'business_cat','status'=>1),'','ci_terms');
		$data['action']= $action;
		$data['follow_list']=$this->comman_model->getAll(array('lead_id'=>$id,'lead_type'=>'lead'),'','ci_followup');
		$data['meeting_list']=$this->comman_model->getAll(array('lead_id'=>$id),'','ci_meetings');
		if($this->input->server('REQUEST_METHOD')==='POST'){
			extract($_POST);

			$cur_role = $this->session->userdata('manager_role');
			if ($cur_role != 1 && $cur_role != 4) {
				$lead_assign = isset($data['list']['lead_assign']) ? (int)$data['list']['lead_assign'] : (int)$this->session->userdata('manager_id');
			} else {
				$lead_assign = isset($lead_assign) ? (int)$lead_assign : 0;
			}

			$c_date = !empty($close_date) ? db_date($close_date) : '';
			$max_fu = '';
			if(!empty($followup_date)){
				foreach((array)$followup_date as $fd){
					if(!empty($fd)){
						$dfd = db_date($fd);
						if(empty($max_fu) || strtotime($dfd) > strtotime($max_fu)){
							$max_fu = $dfd;
						}
					}
				}
			}
			if(!empty($max_fu) && !empty($c_date)){
				if(strtotime($c_date) < strtotime($max_fu)){
					$c_date = $max_fu; // Exp. Close Date can never be previous or past of follow up dates
				}
			}

			$list= array(
				'customer'=>$customer,
				'vertical_id'=>$vertical_id,
				'category'=>$category,
				'a_number'=>$a_number,
				'a_department'=>$a_department,
				'r_amount'=>$r_amount,
				'r_date'=>$r_date,
				's_cat'=>$s_cat,
				'p_cat'=>$p_cat,
				'p_details'=>$p_details,
				'email'=>$email,
				'qty'=>$qty,
				'mrc'=>$mrc,
				'revenue'=>$revenue,
				'mrr'=>(floatval($mrc)+floatval($revenue)),
				'poc'=>$poc,
				'contact1'=>$contact1,
				'alt_mobile'=>!empty($alt_contact) ? $alt_contact : (isset($alt_mobile) ? $alt_mobile : ''),
				'alt_contact'=>!empty($alt_contact) ? $alt_contact : (isset($alt_mobile) ? $alt_mobile : ''),
				'business_category'=>isset($business_category) ? $business_category : '',
				'dnd_status'=>isset($dnd_status) ? $dnd_status : 'No',
				'dnd_check_date'=>!empty($dnd_check_date) ? db_date($dnd_check_date) : '',

				'location'=>$location,
				'status'=>$status,				
				'lost_reason'=>($status == 43) ? trim($this->input->post('lost_reason')) : null,
				'close_date'=>$c_date,
				'remark'=>$remark,
				'address'=>$address,
				'city'=>$city,
				'country'=>$country,
				'state'=>$state,				
				'poc2'=>$poc2,
				'contact2'=>$contact2,
				'c_partner'=>$c_partner,
				'otc'=>$otc,
				'lead_cat'=>$lead_cat,
				'lead_assign'=>$lead_assign,
				'lead_source'=>$lead_source,

				'tl_number'=>isset($tl_number) ? $tl_number : '',
				'tl_authority'=>isset($tl_authority) ? $tl_authority : '',
				'tl_issue_date'=>!empty($tl_issue_date) ? db_date($tl_issue_date) : '',
				'tl_expire_date'=>!empty($tl_expire_date) ? db_date($tl_expire_date) : '',

				'v_amount'=>$v_amount,
				'v_remark'=>$v_remark,
				
				'edited_by'=>$this->session->userdata('manager_id'),
				'admin_status'=>1,
				'modified'=>date('Y-m-d')
				);
			$response= $this->comman_model->updateData('id',$id,$list,'ci_leads');	
			if(!empty($bo_id)){

		     		for($i=0;$i<=6;$i++){
		     			$so= array(		     				
		     				'bo_cat'=>$bo_cat[$i],
		     				'bo_date'=>$bo_date[$i],
		     				'bo_status'=>$bo_status[$i],
		     				'bo_ref'=>$bo_ref[$i],
		     				'bo_remark'=>$bo_remark[$i]		     				
		     			);
		     			$this->comman_model->updateData('id',$bo_id[$i],$so,'ci_boleads');
		     		}
				 }
				 
				 if(!empty($flead_id)){

					for($i=0;$i<count($flead_id);$i++){
						if($flead_id[$i]!=0){

							$so= array(								
								'followup_remark'=>$followup_remark[$i]									     				
							);
							$this->comman_model->updateData('id',$flead_id[$i],$so,'ci_followup');

						}else{

							if(!empty($followup_date[$i])){
								$new_fu = db_date($followup_date[$i]);
								$so= array(		     				
									'lead_id'=>$id,
									'lead_type'=>'lead',
									'followup_date'=>$new_fu,
									'followup_time'=>$followup_time[$i],
									'followup_remark'=>$followup_remark[$i],
									'user_id'=>$this->session->userdata('manager_id')	     				
								);
								$this->comman_model->saveData('ci_followup',$so);
								$lead_up = array('followup_date'=>$new_fu,'followup_time'=>$followup_time[$i]);
								if(!empty($c_date) && strtotime($c_date) < strtotime($new_fu)){
									$lead_up['close_date'] = $new_fu;
								}
								$this->comman_model->updateData('id',$id,$lead_up,'ci_leads');
							}
							
							
						}
						
					}
				}

				if(!empty($mlead_id)){

					for($i=0;$i<count($mlead_id);$i++){
						if($mlead_id[$i]!=0){

							$mo= array(								
								'm_remark'=>$m_remark[$i]									     				
							);
							$this->comman_model->updateData('id',$mlead_id[$i],$mo,'ci_meetings');

						}else{
							

							if(!empty($m_date[$i])){
								$mo= array(	
									'lead_id'=>$id,						
									'm_date'=>db_date($m_date[$i]),
									'm_time'=>$m_time[$i],
									'm_remark'=>$m_remark[$i],
									'm_poc'=>$m_poc[$i],
									'm_mobile'=>$m_mobile[$i],
								//	'm_address'=>$m_address[$i],
									'a1'=>$a1[$i],
									'a2'=>$a2[$i],
									'created'=>date('Y-m-d'),
									'created_by'=>$this->session->userdata('manager_id')									    				
								);
								$this->comman_model->saveData('ci_meetings',$mo);
								$this->comman_model->updateData('id',$id,array('m_date'=>db_date($m_date[$i]),'m_time'=>$m_time[$i]),'ci_leads');
							}
						
						}
						
					}
				}
				
			if($response)
					{
						$this->session->set_flashdata('message', 'Lead has been updated successfully!');
						  

						if($action=='tm'){
							$return_url= urldecode($this->session->userdata('manager_return_string'));
							redirect('/manager/'.$return_url);
						}else{
							$return_url= urldecode($this->session->userdata('manager_return_string'));
							redirect('/manager/'.$return_url);
						}
						
					}		

			}

		    $this->load->view('manager/leads/editleads',$data);

	   }	
	   

	   public function editLimited($id='',$action='')

	   {       	
  
		  $data=array();
		  $data['info']=array(
			  'title'=>'Manager- Meeting',
			  'page_heading'=>'Edit Meeting'
		  );
		  $data['info2']=$this->if_not_login();
		  $id= base64_decode($id);		      
		  $data['list']=$this->comman_model->getAll(array('id'=>$id),'','ci_leads','first');
		  
		  $data['slist']=$this->ci_admin_model->getSingleLead($id);
		  $data['v_list'] =$this->ci_admin_model->getShortVerticals($this->session->userdata('manager_id'));
		  //print_r($data['slist']);
		  //die;
  
		  if(empty($data['list'])){
			  $this->session->set_flashdata('error_message', 'Not allowed');
			  if($action=='tm'){
				  redirect('/manager/team/leads');
			  }else{
				  redirect('/manager/leads');
			  }
		  }  
		  
		
		  $data['u_list'] =$this->comman_model->getAll('',array('id','ASC'),'ci_user');
		  $data['action']= $action;
		 
		  $data['meeting_list']=$this->comman_model->getAll(array('lead_id'=>$id),'','ci_meetings');
		  if($this->input->server('REQUEST_METHOD')==='POST'){
			  extract($_POST);
			 	
			
				  if(!empty($mlead_id)){
  
					  for($i=0;$i<count($mlead_id);$i++){						
  
							  $mo= array(								
								  'm_remark'=>$m_remark[$i]									     				
							  );
							  $response =  $this->comman_model->updateData('id',$mlead_id[$i],$mo,'ci_meetings');  
						
						  
					  }
				  }
				  
			  if($response)
					  {
						  $this->session->set_flashdata('message', 'Lead has been updated successfully!');
						  $return_url= urldecode($this->session->userdata('manager_return_string'));
						  redirect('/manager/'.$return_url);
						
						  
					  }		
  
			  }
  
			  $this->load->view('manager/leads/editleadslimited',$data);
  
		 }	

	   

	   

	  

	   

	// delete category of news using category id 	

    public function leads_del($id='')

	{		 

		$data=array();
		$data['info']=array(
			'title'=>'Manager Leads',
			'page_heading'=>'Delete Lead'
		);
$data['info2']=$this->if_not_login();
	

	   if(empty($id))

	     {

			 redirect('ci_admin_leads/leads_info');

		 }	

		$response= $this->ci_admin_model->deleteData('id',$id,'ci_leads');

		if($response)

		{

			redirect('ci_admin_leads/leads_info');

		}

    }  

    // delete category of news using category id 	

    public function leadDeleteAjax($id)

	{		 

		
		//$data['info2']=$this->if_not_login();
		//extract($_POST);	
		$this->ci_admin_model->deleteData('id',$id,'ci_leads');
		$this->ci_admin_model->deleteData('lead_id',$id,'ci_boleads');

		echo "done";

    }

	/**
	 * Export Leads in CSV format with actual DB values and scope filtering
	 * Strict Access: Admin only (manager_role == 1)
	 */
	public function export()
	{
		$this->if_not_login();
		$role = $this->session->userdata('manager_role');
		if ($role != 1) {
			show_error('Access Denied: Only Administrators have permission to export Leads data.', 403);
			return;
		}

		// If download parameter is requested, stream CSV
		if ($this->input->get('download') == 1) {
			$user_id = $this->input->get('user_id');

			// Pre-fetch lookups for readable CSV values
			$status_map = array();
			$statuses = $this->comman_model->getAll('', '', 'ci_status');
			if (!empty($statuses)) {
				foreach ($statuses as $st) {
					$status_map[$st['id']] = $st['status'];
				}
			}

			$terms_map = array();
			$terms = $this->comman_model->getAll('', '', 'ci_terms');
			if (!empty($terms)) {
				foreach ($terms as $tm) {
					$terms_map[$tm['id']] = $tm['title'];
				}
			}

			$countries_map = array();
			$countries = $this->comman_model->getAll('', '', 'countries');
			if (!empty($countries)) {
				foreach ($countries as $c) {
					$countries_map[$c['id']] = $c['name'];
				}
			}

			$states_map = array();
			$states = $this->comman_model->getAll('', '', 'states');
			if (!empty($states)) {
				foreach ($states as $s) {
					$states_map[$s['id']] = $s['name'];
				}
			}

			$users_map = array();
			$users = $this->comman_model->getAll('', '', 'ci_user');
			if (!empty($users)) {
				foreach ($users as $u) {
					$users_map[$u['id']] = $u['name'];
				}
			}

			$file_prefix = 'leads_export_';
			if ($user_id === 'unassigned') {
				$this->db->where('(lead_assign = 0 OR lead_assign IS NULL)');
				$file_prefix .= 'unassigned_';
			} else if (is_numeric($user_id) && (int)$user_id > 0) {
				$this->db->where('lead_assign', (int)$user_id);
				$uname = isset($users_map[(int)$user_id]) ? preg_replace('/[^a-zA-Z0-9_-]/', '_', $users_map[(int)$user_id]) : 'user_'.$user_id;
				$file_prefix .= strtolower($uname).'_';
			} else {
				$file_prefix .= 'all_users_';
			}

			$leads = $this->db->order_by('id', 'DESC')->get('ci_leads')->result_array();

			$filename = $file_prefix . date('Y-m-d_His') . '.csv';
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename=' . $filename);
			header('Pragma: no-cache');
			header('Expires: 0');

			$output = fopen('php://output', 'w');
			fputs($output, "\xEF\xBB\xBF");

			// CSV Column Headers matching Add Leads Initial & Additional Category fields
			$headers = array(
				'Lead ID',
				'Customer Name',
				'POC Name',
				'POC Contact Number',
				'Email',
				'Account Number',
				'Account Under Vertical',
				'Current Monthly Revenue Amount',
				'Current Monthly Revenue Date',
				'Category',
				'Service Category',
				'Product Category',
				'Product Details',
				'Trade License Number',
				'Trade License Authority',
				'Trade License Issue Date',
				'Trade License Expiry Date',
				'Qty',
				'MRC',
				'Revenue',
				'MRR',
				'Location',
				'Status',
				'Lost Reason',
				'Exp Close Date',
				'Remarks',
				'Address',
				'Country',
				'State',
				'City',
				'Alternate POC Name',
				'Alternate POC Contact Number',
				'Channel Partner',
				'One Time Charges',
				'Lead Category',
				'Lead Assigned To',
				'Website / Source Link',
				'Next Follow Up Date',
				'Next Follow Up Time',
				'Next Meeting Date',
				'Next Meeting Time',
				'Created Date'
			);
			fputcsv($output, $headers);

			if (!empty($leads)) {
				foreach ($leads as $l) {
					$s_cat_name = isset($terms_map[$l['s_cat']]) ? $terms_map[$l['s_cat']] : $l['s_cat'];
					$p_cat_name = isset($terms_map[$l['p_cat']]) ? $terms_map[$l['p_cat']] : $l['p_cat'];
					$lead_cat_name = isset($terms_map[$l['lead_cat']]) ? $terms_map[$l['lead_cat']] : $l['lead_cat'];
					$status_name = isset($status_map[$l['status']]) ? $status_map[$l['status']] : $l['status'];
					$country_name = isset($countries_map[$l['country']]) ? $countries_map[$l['country']] : $l['country'];
					$state_name = isset($states_map[$l['state']]) ? $states_map[$l['state']] : $l['state'];
					$assigned_name = !empty($l['lead_assign']) && isset($users_map[$l['lead_assign']]) ? $users_map[$l['lead_assign']] : 'Unassigned';

					$row = array(
						!empty($l['lead_id']) ? $l['lead_id'] : (100000000 + (int)$l['id']),
						$l['customer'],
						$l['poc'],
						$l['contact1'],
						$l['email'],
						$l['a_number'],
						$l['a_department'],
						$l['r_amount'],
						$l['r_date'],
						$l['category'],
						$s_cat_name,
						$p_cat_name,
						$l['p_details'],
						$l['tl_number'],
						$l['tl_authority'],
						$l['tl_issue_date'],
						$l['tl_expire_date'],
						$l['qty'],
						$l['mrc'],
						$l['revenue'],
						$l['mrr'],
						$l['location'],
						$status_name,
						!empty($l['lost_reason']) ? $l['lost_reason'] : '',
						$l['close_date'],
						$l['remark'],
						$l['address'],
						$country_name,
						$state_name,
						$l['city'],
						$l['poc2'],
						$l['contact2'],
						$l['c_partner'],
						$l['otc'],
						$lead_cat_name,
						$assigned_name,
						$l['lead_source'],
						$l['followup_date'],
						$l['followup_time'],
						$l['m_date'],
						$l['m_time'],
						$l['created']
					);
					fputcsv($output, $row);
				}
			}

			fclose($output);
			exit();
		}

		// Configure View Data
		$data = array();
		$data['info'] = array(
			'title' => 'Manager - Export Leads',
			'page_heading' => 'Export Leads Data'
		);
		$data['info2'] = $this->if_not_login();

		$data['total_count'] = $this->db->count_all_results('ci_leads');
		
		$this->db->where('(lead_assign = 0 OR lead_assign IS NULL)');
		$data['unassigned_count'] = $this->db->count_all_results('ci_leads');

		$data['users'] = $this->db->select('id, name, email')->get_where('ci_user', array('status' => 1))->result_array();

		// Count leads per user
		$user_counts_raw = $this->db->select('lead_assign, COUNT(*) as cnt')->group_by('lead_assign')->get('ci_leads')->result_array();
		$user_counts = array();
		foreach ($user_counts_raw as $uc) {
			if (!empty($uc['lead_assign'])) {
				$user_counts[$uc['lead_assign']] = $uc['cnt'];
			}
		}
		$data['user_counts'] = $user_counts;

		$this->load->view('manager/leads/export', $data);
	}

	/**
	 * Leads Import UI
	 * Admin has full upload permission; Managers have view-only access; others blocked.
	 */
	public function import()
	{
		$this->if_not_login();
		$role = $this->session->userdata('manager_role');
		if ($role != 1 && $role != 4) {
			show_error('Access Denied: You do not have permission to access Leads Import.', 403);
			return;
		}

		$data = array();
		$data['info'] = array(
			'title' => 'Manager - Leads',
			'page_heading' => 'Import Leads Data'
		);
		$data['info2'] = $this->if_not_login();
		$data['is_admin'] = ($role == 1);
		$data['is_support'] = ($role == 4);

		$this->load->view('manager/leads/import', $data);
	}

	/**
	 * Process Leads CSV Import
	 * Backend Permission: Admin and Support (manager_role == 1 || manager_role == 4)
	 */
	public function import_action()
	{
		$this->if_not_login();
		$role = $this->session->userdata('manager_role');
		if ($role != 1 && $role != 4) {
			$this->session->set_flashdata('error_message', 'Permission Denied: Only Admin and Support are permitted to import Leads data.');
			redirect('manager/leads/import');
			return;
		}

		if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
			$this->session->set_flashdata('error_message', 'Please select a valid CSV file to upload.');
			redirect('manager/leads/import');
			return;
		}

		$file_ext = strtolower(pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION));
		if ($file_ext !== 'csv') {
			$this->session->set_flashdata('error_message', 'Invalid file type. Please upload a .csv file.');
			redirect('manager/leads/import');
			return;
		}

		$handle = fopen($_FILES['csv_file']['tmp_name'], 'r');
		if (!$handle) {
			$this->session->set_flashdata('error_message', 'Could not open the uploaded file.');
			redirect('manager/leads/import');
			return;
		}

		// Read header line
		$raw_headers = fgetcsv($handle, 5000, ',');
		if (!$raw_headers) {
			fclose($handle);
			$this->session->set_flashdata('error_message', 'The uploaded CSV file is empty.');
			redirect('manager/leads/import');
			return;
		}

		// Normalize headers to map keys
		$col_index = array();
		foreach ($raw_headers as $idx => $hdr) {
			$clean = strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $hdr)));
			$clean = preg_replace('/[^a-z0-9]/', '', $clean);
			$col_index[$clean] = $idx;
		}

		$get_val = function($row, $aliases) use ($col_index) {
			foreach ($aliases as $alias) {
				if (isset($col_index[$alias]) && isset($row[$col_index[$alias]])) {
					return trim($row[$col_index[$alias]]);
				}
			}
			return '';
		};

		$imported_count = 0;
		$row_errors = array();
		$row_num = 1;
		$manager_id = $this->session->userdata('manager_id');

		while (($row = fgetcsv($handle, 5000, ',')) !== FALSE) {
			$row_num++;
			if (empty(array_filter($row))) {
				continue;
			}

			// Clean formula injection characters from all cells
			foreach ($row as $k => $val) {
				$val = trim($val);
				if (strlen($val) > 0 && in_array($val[0], array('=', '+', '-', '@'))) {
					$row[$k] = "'" . $val;
				}
			}

			$username_val = $get_val($row, array('username', 'user', 'assigneduser', 'agent'));
			$assigned_user_id = 0;
			if (!empty($username_val)) {
				$username_clean = ltrim($username_val, "'=@+-");
				$u_match = $this->db->where('email', $username_clean)
					->or_where('name', $username_clean)
					->get('ci_user')->row_array();
				if (!empty($u_match['id'])) {
					$assigned_user_id = (int)$u_match['id'];
				} else {
					$row_errors[] = "Row {$row_num}: Username '{$username_val}' does not match any registered user.";
					continue;
				}
			}

			$customer = $get_val($row, array('customername', 'customer', 'clientname', 'client'));
			if (empty($customer)) {
				continue;
			}

			$poc = $get_val($row, array('pocname', 'poc', 'contactperson'));
			$contact1 = $get_val($row, array('poccontactnumber', 'contact1', 'contactnumber', 'mobile', 'phone'));
			$alt_contact = $get_val($row, array('alternativecontactnumber', 'altcontact', 'alternatecontact', 'alternativemobilenumber', 'altmobile', 'alternatemobile'));
			$alt_mobile = $alt_contact;
			$email = $get_val($row, array('email', 'emailid'));
			$a_number = $get_val($row, array('accountnumber', 'anumber', 'accountno'));
			$a_department = $get_val($row, array('accountundervertical', 'adepartment', 'department', 'vertical'));
			$r_amount = $get_val($row, array('currentmonthlyrevenueamount', 'ramount', 'revenueamount'));
			$r_date = $get_val($row, array('currentmonthlyrevenuedate', 'rdate', 'revenuedate'));
			$category = $get_val($row, array('category', 'cat'));
			if (empty($category)) {
				$category = 'New';
			}

			$business_category = $get_val($row, array('businesscategory', 'businesscat'));
			$dnd_status = $get_val($row, array('dncrdnd', 'dncr', 'dnd', 'dndstatus', 'tncrtnd', 'tncr'));
			if (empty($dnd_status) || !in_array(strtolower($dnd_status), array('yes', 'no'))) {
				$dnd_status = 'No';
			} else {
				$dnd_status = ucfirst(strtolower($dnd_status));
			}
			$dnd_check_date = $get_val($row, array('dncrdate', 'dncrcheckdate', 'dndcheckdate', 'checkdate', 'tncrcheckdate'));

			$s_cat = $get_val($row, array('servicecategory', 'scat'));
			$p_cat = $get_val($row, array('productcategory', 'pcat'));
			$p_details = $get_val($row, array('productdetails', 'pdetails'));
			$tl_number = $get_val($row, array('tradelicensenumber', 'tlnumber', 'tradelicense'));
			$tl_authority = $get_val($row, array('tradelicenseauthority', 'tlauthority', 'authority'));
			$tl_issue_date = $get_val($row, array('tradelicenseissuedate', 'tlissuedate'));
			$tl_expire_date = $get_val($row, array('tradelicenseexpirydate', 'tlexpiredate', 'tradelicenseexpiry'));
			$qty = $get_val($row, array('qty', 'quantity'));
			$mrc = $get_val($row, array('mrc'));
			$revenue = $get_val($row, array('revenue'));
			$mrr = $get_val($row, array('mrr'));
			if (empty($mrr)) {
				$mrr = (float)$mrc + (float)$revenue;
			}

			$location = $get_val($row, array('location'));
			$status_raw = $get_val($row, array('status'));
			$status = 36; // Default to Database
			if (is_numeric($status_raw) && (int)$status_raw > 0) {
				$status = (int)$status_raw;
			} else if (!empty($status_raw)) {
				$st_row = $this->db->like('status', $status_raw)->get('ci_status')->row_array();
				if (!empty($st_row)) {
					$status = $st_row['id'];
				}
			}

			$close_date = $get_val($row, array('expclosedate', 'closedate', 'expectedclosedate'));
			$remark = $get_val($row, array('remarks', 'remark'));
			$address = $get_val($row, array('address'));
			$country = $get_val($row, array('country'));
			$state = $get_val($row, array('state'));
			$city = $get_val($row, array('city'));
			$poc2 = $get_val($row, array('alternatepocname', 'poc2', 'altpoc'));
			$contact2 = $get_val($row, array('alternatepoccontactnumber', 'contact2', 'altpoccontact'));
			$c_partner = $get_val($row, array('channelpartner', 'cpartner'));
			$otc = $get_val($row, array('onetimecharges', 'otc'));
			$lead_cat = $get_val($row, array('leadcategory', 'leadcat'));
			$lead_source = $get_val($row, array('websitesourcelink', 'leadsource', 'source', 'website'));
			$followup_date = $get_val($row, array('nextfollowupdate', 'followupdate', 'nextfu'));
			$followup_time = $get_val($row, array('nextfollowuptime', 'followuptime'));
			$m_date = $get_val($row, array('nextmeetingdate', 'mdate', 'meetingdate'));
			$m_time = $get_val($row, array('nextmeetingtime', 'mtime', 'meetingtime'));

			$insert_data = array(
				'lead_id' => 100000000,
				'customer' => $customer,
				'poc' => $poc,
				'contact1' => $contact1,
				'alt_mobile' => $alt_mobile,
				'alt_contact' => $alt_contact,
				'email' => $email,
				'a_number' => $a_number,
				'a_department' => $a_department,
				'r_amount' => $r_amount,
				'r_date' => !empty($r_date) ? db_date($r_date) : '',
				'category' => $category,
				'business_category' => $business_category,
				'dnd_status' => $dnd_status,
				'dnd_check_date' => !empty($dnd_check_date) ? db_date($dnd_check_date) : '',
				's_cat' => $s_cat,
				'p_cat' => $p_cat,
				'p_details' => $p_details,
				'tl_number' => $tl_number,
				'tl_authority' => $tl_authority,
				'tl_issue_date' => !empty($tl_issue_date) ? db_date($tl_issue_date) : '',
				'tl_expire_date' => !empty($tl_expire_date) ? db_date($tl_expire_date) : '',
				'qty' => $qty,
				'mrc' => $mrc,
				'revenue' => $revenue,
				'mrr' => $mrr,
				'location' => $location,
				'status' => $status,
				'close_date' => !empty($close_date) ? db_date($close_date) : '',
				'remark' => $remark,
				'address' => $address,
				'country' => is_numeric($country) ? $country : 0,
				'state' => is_numeric($state) ? $state : 0,
				'city' => $city,
				'poc2' => $poc2,
				'contact2' => $contact2,
				'c_partner' => !empty($c_partner) ? $c_partner : 'SRJ Electronics Trading LLC',
				'otc' => $otc,
				'lead_cat' => $lead_cat,
				'lead_assign' => $assigned_user_id, // Assigned to specified user or 0 (unassigned)
				'lead_source' => $lead_source,
				'followup_date' => !empty($followup_date) ? db_date($followup_date) : '',
				'followup_time' => $followup_time,
				'm_date' => !empty($m_date) ? db_date($m_date) : '',
				'm_time' => $m_time,
				'created_by' => $manager_id,
				'admin_status' => 1,
				'created' => date('Y-m-d')
			);

			$new_id = $this->comman_model->saveData('ci_leads', $insert_data);
			if ($new_id) {
				$this->comman_model->updateData('id', $new_id, array('lead_id' => 100000000 + $new_id), 'ci_leads');
				$imported_count++;
			}
		}

		fclose($handle);

		if ($imported_count > 0) {
			$msg = 'Success: ' . $imported_count . ' leads successfully imported!';
			if (!empty($row_errors)) {
				$msg .= '<br><br><strong>Row Warnings/Errors:</strong><br>' . implode('<br>', array_slice($row_errors, 0, 10));
			}
			$this->session->set_flashdata('message', $msg);
		} else {
			$err_msg = 'No valid lead rows could be imported.';
			if (!empty($row_errors)) {
				$err_msg .= '<br><br>' . implode('<br>', array_slice($row_errors, 0, 10));
			}
			$this->session->set_flashdata('error_message', $err_msg);
		}

		redirect('manager/leads');
	}

	/**
	 * Download sample CSV file template for Leads import
	 */
	public function download_sample_csv()
	{
		$this->if_not_login();
		$role = $this->session->userdata('manager_role');
		if ($role != 1 && $role != 4) {
			show_error('Access Denied', 403);
			return;
		}

		$filename = 'sample_leads_template.csv';
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=' . $filename);
		header('Pragma: no-cache');
		header('Expires: 0');

		$output = fopen('php://output', 'w');
		fputs($output, "\xEF\xBB\xBF");

		$headers = array(
			'Username',
			'Customer Name',
			'POC Name',
			'POC Contact Number',
			'Alternative Contact Number',
			'Email',
			'Account Number',
			'Account Under Vertical',
			'Current Monthly Revenue Amount',
			'Current Monthly Revenue Date',
			'Category',
			'Business Category',
			'DNCR/DND',
			'DNCR Date',
			'Service Category',
			'Product Category',
			'Product Details',
			'Trade License Number',
			'Trade License Authority',
			'Trade License Issue Date',
			'Trade License Expiry Date',
			'Qty',
			'MRC',
			'Revenue',
			'MRR',
			'Location',
			'Status',
			'Exp Close Date',
			'Remarks',
			'Address',
			'City',
			'Alternate POC Name',
			'Alternate POC Contact Number',
			'Channel Partner',
			'One Time Charges',
			'Website / Source Link',
			'Next Follow Up Date',
			'Next Follow Up Time'
		);
		fputcsv($output, $headers);

		$sample_row = array(
			'admin@rolextogroup.com',
			'Sample Enterprise LLC',
			'John Doe',
			'+971 50 123 4567',
			'+971 52 111 2222',
			'john@sample.com',
			'ACC-98765',
			'Telecom',
			'1500',
			date('d-m-Y'),
			'New',
			'Corporate Solutions',
			'No',
			date('d-m-Y'),
			'Voice Services',
			'SIP Trunk',
			'Corporate 10-line package',
			'TL-554433',
			'DED Dubai',
			date('d-m-Y', strtotime('-1 year')),
			date('d-m-Y', strtotime('+1 year')),
			'1',
			'500',
			'1000',
			'1500',
			'Dubai, Business Bay',
			'Database',
			date('d-m-Y', strtotime('+30 days')),
			'Prospect interested in corporate discount',
			'Office 402, Opal Tower',
			'Dubai',
			'Jane Smith',
			'+971 50 987 6543',
			'SRJ Electronics Trading LLC',
			'200',
			'https://example.com',
			date('d-m-Y', strtotime('+7 days')),
			'11:00 AM'
		);
		fputcsv($output, $sample_row);
		fclose($output);
		exit;
	}

	public function assigned_leads($page = 0)
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Assigned Leads',
			'page_heading' => 'Assigned Leads'
		);
		$data['info2'] = $this->if_not_login();

		// Restricted to Admin (role 1) and Support (role 4)
		$role = $this->session->userdata('manager_role');
		if ($role != 1 && $role != 4) {
			$this->session->set_flashdata('error_message', 'Access denied.');
			redirect('manager/dashboard');
			return;
		}

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

		if (!empty($country)) {
			$country = implode(',', $country);
			$data['state_list'] = $this->comman_model->getAll(array('country_id' => $country), '', 'states');
		}
		if (!empty($state)) {
			$state = implode(',', $state);
		}

		$status = isset($_GET['status']) ? $_GET['status'] : '';
		$category = isset($_GET['category']) ? $_GET['category'] : '';
		$type = isset($_GET['type']) ? $_GET['type'] : 0;
		$lCat = isset($_GET['lCat']) ? $_GET['lCat'] : '';

		if (!empty($lCat)) {
			$lCat = implode(',', $lCat);
		}
		if (!empty($category)) {
			$category = implode(',', $category);
		}
		if (!empty($status)) {
			$status = implode(',', $status);
		}

		$data['st_list'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_status');
		$data['c_list'] = $this->comman_model->getAll('', array('id', 'ASC'), 'countries');
		$data['pc_list'] = $this->comman_model->getAll(array('cat_type' => 'p_cat', 'parent_id' => 0), '', 'ci_terms');
		$data['lc_list'] = $this->comman_model->getAll(array('cat_type' => 'lead_cat', 'parent_id' => 0), '', 'ci_terms');
		$data['users_list'] = $this->db->select('id, name, email')->get('ci_user')->result_array();

		$data['product_list'] = $this->ci_admin_model->getLeadsFollowups($d1, $d2, $mrr1, $mrr2, '', '', $order_by, $sort_by, $search, $status, $category, $type, $lCat, $user_name, $country, $state);

		$this->load->view('manager/leads/assigned_leads', $data);
	}

	public function bulk_move_assign()
	{
		$this->if_not_login();
		$role = $this->session->userdata('manager_role');
		if ($role != 1 && $role != 4) {
			$this->session->set_flashdata('error_message', 'Access denied.');
			redirect('manager/dashboard');
			return;
		}

		$lead_ids = $this->input->post('lead_ids');
		$target_user = $this->input->post('target_user');

		if (empty($lead_ids) || !is_array($lead_ids)) {
			$this->session->set_flashdata('error_message', 'No leads selected for reassignment.');
			redirect('manager/assigned-leads');
			return;
		}

		$target_user_id = (int)$target_user;
		$target_user_row = $this->db->get_where('ci_user', array('id' => $target_user_id))->row_array();
		$target_name = !empty($target_user_row['name']) ? $target_user_row['name'] : ($target_user_id === 0 ? 'Unassigned' : 'User #'.$target_user_id);

		$sanitized_ids = array_map('intval', $lead_ids);
		$this->db->where_in('id', $sanitized_ids);
		$this->db->update('ci_leads', array(
			'lead_assign' => $target_user_id,
			'edited_by' => $this->session->userdata('manager_id'),
			'modified' => date('Y-m-d')
		));

		$count = count($sanitized_ids);
		$uid = $this->session->userdata('manager_id');
		$uname = $this->session->userdata('manager_name');
		$this->ci_admin_model->log_admin_action(
			'lead_reassign',
			"Bulk reassigned $count leads to $target_name (ID: $target_user_id)",
			'SUCCESS',
			$uid,
			$uname
		);

		$this->session->set_flashdata('success_message', "Successfully reassigned $count leads to $target_name.");
		redirect('manager/assigned-leads');
	}

}


/* End of file welcome.php */

/* Location: ./application/controllers/welcome.php */