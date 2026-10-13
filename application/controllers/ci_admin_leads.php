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

		$data['st_list']= $this->comman_model->getAll(array('active'=>1),'','ci_status');
		$data['c_list'] =$this->comman_model->getAll('',array('id','ASC'),'countries');
		$data['pc_list']= $this->comman_model->getAll(array('cat_type'=>'p_cat','parent_id'=>0),'','ci_terms');
		$data['lc_list']= $this->comman_model->getAll(array('cat_type'=>'lead_cat','parent_id'=>0),'','ci_terms');
		$data['product_list']= $this->ci_admin_model->get_Myleads($d1, $d2, $mrr1, $mrr2, '','',$order_by,$sort_by,$search,$status,$category,$lCat,$user_name,$country,$state);
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

		if($this->input->server('REQUEST_METHOD')==='POST')
		{	 			

				extract($_POST);				

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
				'mrr'=>($mrc+$revenue),
				'poc'=>$poc,
				'contact1'=>$contact1,
				'location'=>$location,
				'status'=>$status,

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
		$data['action']= $action;
		$data['follow_list']=$this->comman_model->getAll(array('lead_id'=>$id,'lead_type'=>'lead'),'','ci_followup');
		$data['meeting_list']=$this->comman_model->getAll(array('lead_id'=>$id),'','ci_meetings');
		if($this->input->server('REQUEST_METHOD')==='POST'){
			extract($_POST);

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
				'mrr'=>($mrc+$revenue),
				'poc'=>$poc,
				'contact1'=>$contact1,

				'location'=>$location,
				'status'=>$status,				
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

	

	

	

}



/* End of file welcome.php */

/* Location: ./application/controllers/welcome.php */