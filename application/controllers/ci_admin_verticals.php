<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Ci_admin_verticals extends CI_Controller {
function __construct()
	{
    parent::__construct();
	$this->load->library(array('Session','Pagination'));
	$this->load->helper(array('form','url','text','date'));
	$this->load->model(array('ci_admin_model','comman_model','report_model'));
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
			'title'=>'Manager - Verticals',
			'page_heading'=>'All Verticals'
		);
		$data['info2']=$this->if_not_login();

		$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'A.id';
		$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'DESC';
		$search = isset($_GET['search']) ? $_GET['search'] : '';		

		$d1 = isset($_GET['d1']) ? $_GET['d1'] : '';
		$d2 = isset($_GET['d2']) ? $_GET['d2'] : '';
		$status = isset($_GET['status']) ? $_GET['status'] : '';		
		$category = isset($_GET['category']) ? $_GET['category'] : '';	
		$type = isset($_GET['type']) ? $_GET['type'] : 0;	

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
	//	$data['st_list']= $this->comman_model->getAll(array('active'=>1),'','ci_status');
		$data['st_list']= $this->comman_model->getAll(array('status'=>1,'cat_type'=>'v_status'),'','ci_terms');

		$data['pc_list']= $this->comman_model->getAll(array('cat_type'=>'p_cat','parent_id'=>0),'','ci_terms');
		$data['product_list']= $this->ci_admin_model->getMyVerticals($d1, $d2, '','',$order_by,$sort_by,$search,$status,$category,$type);		
		$page2 = ($this->uri->segment(4)) ? $this->uri->segment(4): 0;      
		//echo $page2; 
		 	$con_q= '';
			 if(!empty($_SERVER['QUERY_STRING']))
			 {
				$con_q.='?'.$_SERVER['QUERY_STRING'];
			 }		
			$page_url=$this->uri->segment(2)."/";

			$page_url1=$this->uri->segment(3);

			$this->session->set_userdata('manager_return_string', urlencode($page_url.$page_url1.$con_q)); 
			
		    
       // $data['product_list']= $this->ci_admin_model->getVerticals();
		$this->load->view('manager/verticals/index',$data);
	}

	public function followups()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Manager - Vertical Follow-ups',
			'page_heading' => 'Vertical Follow-ups'
		);
		$data['info2'] = $this->if_not_login();

		$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'A.id';
		$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'DESC';
		$search = isset($_GET['search']) ? $_GET['search'] : '';
		$d1 = isset($_GET['d1']) ? $_GET['d1'] : '';
		$d2 = isset($_GET['d2']) ? $_GET['d2'] : '';
		$status = isset($_GET['status']) ? $_GET['status'] : '';
		$category = isset($_GET['category']) ? $_GET['category'] : '';
		$type = isset($_GET['type']) ? $_GET['type'] : 0;

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

		$data['st_list'] = $this->comman_model->getAll(array('status' => 1, 'cat_type' => 'v_status'), '', 'ci_terms');
		$data['pc_list'] = $this->comman_model->getAll(array('cat_type' => 'p_cat', 'parent_id' => 0), '', 'ci_terms');
		$data['product_list'] = $this->ci_admin_model->getMyVerticals($d1, $d2, '', '', $order_by, $sort_by, $search, $status, $category, $type);

		$con_q = (!empty($_SERVER['QUERY_STRING'])) ? '?' . $_SERVER['QUERY_STRING'] : '';
		$page_url = $this->uri->segment(2) . "/" . $this->uri->segment(3);
		$this->session->set_userdata('manager_return_string', urlencode($page_url . $con_q));

		$this->load->view('manager/verticals/index', $data);
	}




	public function teams()
	{

        $data=array();
		$data['info']=array(
			'title'=>'Manager - Team Verticals',
			'page_heading'=>'All Team Verticals'
		);
		$data['info2']=$this->if_not_login();

		$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'A.id';
		$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'DESC';
		$search = isset($_GET['search']) ? $_GET['search'] : '';		

		$d1 = isset($_GET['d1']) ? $_GET['d1'] : '';
		$d2 = isset($_GET['d2']) ? $_GET['d2'] : '';
		$status = isset($_GET['status']) ? $_GET['status'] : '';		
		$category = isset($_GET['category']) ? $_GET['category'] : '';	
		$type = isset($_GET['type']) ? $_GET['type'] : 0;	

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
		//$data['st_list']= $this->comman_model->getAll(array('active'=>1),'','ci_status');
		$data['st_list']= $this->comman_model->getAll(array('status'=>1,'cat_type'=>'v_status'),'','ci_terms');

		$data['pc_list']= $this->comman_model->getAll(array('cat_type'=>'p_cat','parent_id'=>0),'','ci_terms');
		$data['product_list']= $this->ci_admin_model->getTeamVerticals($d1, $d2, '','',$order_by,$sort_by,$search,$status,$category,$type);		
		$page2 = ($this->uri->segment(4)) ? $this->uri->segment(4): 0;       
		 	$con_q= '';
			 if(!empty($_SERVER['QUERY_STRING']))
			 {
				$con_q.='?'.$_SERVER['QUERY_STRING'];
			 }		
			$page_url=$this->uri->segment(2)."/";
			$page_url1=$this->uri->segment(3)."/";

			$this->session->set_userdata('manager_return_string', urlencode($page_url.$page_url1.$page2.$con_q)); 
		    
        //$data['product_list']= $this->ci_admin_model->getTeamVerticals();
		$this->load->view('manager/verticals/team_index',$data);
	}



	public function verticals()
	{
        $data=array();
		$data['info']=array(
			'title'=>'Verticals Report',
			'page_heading'=>'Verticals Report'
		);
		$data['info2']=$this->if_not_login();
		
		if(($this->input->server('REQUEST_METHOD')==='GET')&&($this->input->get('search')=='report'))
		{ 
					$d1='';
					$d2='';
					$f1='';
					$f2='';
					$category='';
					$created_by='';
					$status='';
					$s_cat='';					
				
       				extract($_GET);					
				    if(!empty($d1) && !empty($d2))
					{					
						$d1=date("Y-m-d",strtotime($d1));
						$d2=date("Y-m-d",strtotime($d2));
					}					
					$data['list']=$this->report_model->get_vertical_report($d1,$d2,$f1, $f2,$category,$created_by,$status,$s_cat);
				
				}
		$this->load->view('manager/report/vertical_report',$data);
	}

	//to add category

	public function add()

	{



		$data=array();
		$data['info']=array(
			'title'=>'Manager- Verticals',
			'page_heading'=>'Add Vertical'
		);
		$data['info2']=$this->if_not_login();

		$data['st_list']= $this->comman_model->getAll(array('status'=>1,'cat_type'=>'v_status'),'','ci_terms');
		//print_r($data['st_list']);
		$data['cat_list']= $this->comman_model->getAll(array('parent_id'=>0,'status'=>1,'cat_type'=>'v_cat'),'','ci_terms');
		$data['c_list'] =$this->comman_model->getAll('',array('id','ASC'),'countries');
		

		if($this->input->server('REQUEST_METHOD')==='POST')
		{

				extract($_POST);

				$list= array(
				'v_id'=> 500000000,
				'company_name'=>$company_name,
				'category'=>$category,
				'sub_category'=>$sub_category,
				'poc'=>$poc,
				'contact1'=>$contact1,
				'contact2'=>$contact2,				
				'whatsapp_web'=>$whatsapp_web,
				'status'=>$status,
				'designation'=>$designation,			
				'lastfollowup_date'=>db_date($lastfollowup_date),
				'followup_date'=>db_date($followup_date),
				'remark'=>$remark,
				'nationality'=>$nationality,
				'nationality_area'=>$nationality_area,
				'lead_source'=>$lead_source,
				'address'=>$address,
				'address2'=>$address2,
				'city'=>$city,
				'country'=>$country,
				'state'=>$state,
				'email'=>$email,
				'created_by'=>$this->session->userdata('manager_id'),
				'admin_status'=>1,
				'created'=>date('Y-m-d')

				);
				$this->session->set_flashdata('message', 'Verticals has been added successfully!');
				 $v_id = $this->comman_model->saveData('ci_verticals',$list);

				$this->comman_model->updateData('id',$v_id,array('v_id'=>500000000+$v_id),'ci_verticals');
				redirect('manager/verticals');

       }

        $this->load->view('manager/verticals/add',$data);





	}

		//edit category

     public function edit($id='')
	 {

		$data=array();
		$data['info']=array(
			'title'=>'Manager- Verticals',
			'page_heading'=>'Edit Vertical'
		);
		$data['info2']=$this->if_not_login();
		$id= base64_decode($id);

		$data['st_list']= $this->comman_model->getAll(array('status'=>1,'cat_type'=>'v_status'),'','ci_terms');
		$data['cat_list']= $this->comman_model->getAll(array('parent_id'=>0,'status'=>1,'cat_type'=>'v_cat'),'','ci_terms');
		$data['c_list'] =$this->comman_model->getAll('',array('id','ASC'),'countries');
		$data['list']=$this->comman_model->getAll(array('id'=>$id),'','ci_verticals','first');
		if(empty($data['list'])){
				$this->session->set_flashdata('error_message', 'Not allowed');
			   redirect('manager/verticals');
		}
		if(!empty($data['list']['category'])){
			$data['scat_list']= $this->comman_model->getAll(array('parent_id'=>$data['list']['category'],'status'=>1,'cat_type'=>'v_cat'),'','ci_terms');
		}
		if(!empty($data['list']['country'])){
			$data['state_list']= $this->comman_model->getAll(array('country_id'=>$data['list']['country']),'','states');
		}
		if(!empty($data['list']['nationality'])){
			$data['area_list']= $this->comman_model->getAll(array('country_id'=>$data['list']['nationality']),'','states');
		}
	
			   
			 if($this->input->server('REQUEST_METHOD')==='POST')

			{

				extract($_POST);

				$list= array(					
					'company_name'=>$company_name,
					'category'=>$category,
					'sub_category'=>$sub_category,
					'poc'=>$poc,
					'contact1'=>$contact1,
					'contact2'=>$contact2,				
					'whatsapp_web'=>$whatsapp_web,
					'status'=>$status,
					'designation'=>$designation,			
					'lastfollowup_date'=>db_date($lastfollowup_date),
					'followup_date'=>db_date($followup_date),
					'remark'=>$remark,
					'nationality'=>$nationality,
					'nationality_area'=>$nationality_area,
					'lead_source'=>$lead_source,
					'address'=>$address,
					'address2'=>$address2,
					'city'=>$city,
					'country'=>$country,
					'state'=>$state,
					'email'=>$email						
					);

				$response= $this->comman_model->updateData('id',$id,$list,'ci_verticals');

				if($response)
					{
						$this->session->set_flashdata('message', 'Vertical has been updated successfully!');
						$return_url= urldecode($this->session->userdata('manager_return_string'));
						redirect('manager/'.$return_url);
					}

			}

		    $this->load->view('manager/verticals/edit',$data);

	   }


	   public function view($id='')
	   {

			$data=array();
			   $data['info']=array(
				   'title'=>'Manager - Verticals',
				   'page_heading'=>'Preview Vertical Information'
		   );
		   $data['info2']=$this->if_not_login();
		   $id= base64_decode($id);
		   $data['p_info']=$this->ci_admin_model->getSingleVertical($id);
			if(empty($data['p_info'])){
					  $this->session->set_flashdata('error_message', 'Not allowed');
					 redirect('manager/verticals');
			  }		  
			  $this->load->view('manager/verticals/view',$data);

		}



}



/* End of file welcome.php */

/* Location: ./application/controllers/welcome.php */