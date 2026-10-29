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

		$d1 = isset($_GET['d1']) ? $_GET['d1'] : (isset($_GET['exp_d1']) ? $_GET['exp_d1'] : '');
		$d2 = isset($_GET['d2']) ? $_GET['d2'] : (isset($_GET['exp_d2']) ? $_GET['exp_d2'] : '');

		$mrr1 = isset($_GET['mrr1']) ? $_GET['mrr1'] : '';
		$mrr2 = isset($_GET['mrr2']) ? $_GET['mrr2'] : '';

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
		$vertical_fu = isset($_GET['vertical_fu']) ? $_GET['vertical_fu'] : '';

		$data['st_list']= $this->comman_model->getAll(array('active'=>1),'','ci_status');
		$data['c_list'] =$this->comman_model->getAll('',array('id','ASC'),'countries');
		$data['pc_list']= $this->comman_model->getAll(array('cat_type'=>'p_cat','parent_id'=>0),'','ci_terms');
		$data['lc_list']= $this->comman_model->getAll(array('cat_type'=>'lead_cat','parent_id'=>0),'','ci_terms');
		$data['product_list']= $this->ci_admin_model->get_Myleads($d1, $d2, $mrr1, $mrr2, '','',$order_by,$sort_by,$search,$status,$category,$lCat,$user_name,$country,$state,$closure,$vertical_fu, 'database');
		$data['stage'] = 'database';
		
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

	public function qualified()
	{
        $data=array();
		$data['info']=array(
			'title'=>'Manager - Leads',
			'page_heading'=>'Lead View'
		);
		$data['info2']=$this->if_not_login();
		
		$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'A.id';
		$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'DESC';
		$search = isset($_GET['search']) ? $_GET['search'] : '';	
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

		$d1 = isset($_GET['d1']) ? $_GET['d1'] : (isset($_GET['exp_d1']) ? $_GET['exp_d1'] : '');
		$d2 = isset($_GET['d2']) ? $_GET['d2'] : (isset($_GET['exp_d2']) ? $_GET['exp_d2'] : '');
		$mrr1 = isset($_GET['mrr1']) ? $_GET['mrr1'] : '';
		$mrr2 = isset($_GET['mrr2']) ? $_GET['mrr2'] : '';
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
		$vertical_fu = isset($_GET['vertical_fu']) ? $_GET['vertical_fu'] : '';

		$data['st_list']= $this->comman_model->getAll(array('active'=>1),'','ci_status');
		$data['c_list'] =$this->comman_model->getAll('',array('id','ASC'),'countries');
		$data['pc_list']= $this->comman_model->getAll(array('cat_type'=>'p_cat','parent_id'=>0),'','ci_terms');
		$data['lc_list']= $this->comman_model->getAll(array('cat_type'=>'lead_cat','parent_id'=>0),'','ci_terms');
		$data['product_list']= $this->ci_admin_model->get_Myleads($d1, $d2, $mrr1, $mrr2, '','',$order_by,$sort_by,$search,$status,$category,$lCat,$user_name,$country,$state,$closure,$vertical_fu, 'lead');
		$data['stage'] = 'lead';
		$data['is_leads_view'] = true;

		$con_q= '';
		if(!empty($_SERVER['QUERY_STRING']))
		{
			$con_q.='?'.$_SERVER['QUERY_STRING'];
		}		
		$page_url=$this->uri->segment(2)."/";
		$page2 = ($this->uri->segment(4)) ? $this->uri->segment(4): 0;
		$this->session->set_userdata('manager_return_string', urlencode($page_url.$page2.$con_q));		
		
		$this->load->view('manager/leads/lead_view',$data);
	}

	public function approvals()
	{
        $data=array();
		$data['info']=array(
			'title'=>'Manager - Pending Lead Approvals',
			'page_heading'=>'Pending Lead Approvals'
		);
		$data['info2']=$this->if_not_login();
		
		$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'A.id';
		$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'DESC';
		$search = isset($_GET['search']) ? $_GET['search'] : '';	
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

		$d1 = isset($_GET['d1']) ? $_GET['d1'] : (isset($_GET['exp_d1']) ? $_GET['exp_d1'] : '');
		$d2 = isset($_GET['d2']) ? $_GET['d2'] : (isset($_GET['exp_d2']) ? $_GET['exp_d2'] : '');
		$mrr1 = isset($_GET['mrr1']) ? $_GET['mrr1'] : '';
		$mrr2 = isset($_GET['mrr2']) ? $_GET['mrr2'] : '';
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
		$vertical_fu = isset($_GET['vertical_fu']) ? $_GET['vertical_fu'] : '';

		$data['st_list']= $this->comman_model->getAll(array('active'=>1),'','ci_status');
		$data['c_list'] =$this->comman_model->getAll('',array('id','ASC'),'countries');
		$data['pc_list']= $this->comman_model->getAll(array('cat_type'=>'p_cat','parent_id'=>0),'','ci_terms');
		$data['lc_list']= $this->comman_model->getAll(array('cat_type'=>'lead_cat','parent_id'=>0),'','ci_terms');
		$data['product_list']= $this->ci_admin_model->get_Myleads($d1, $d2, $mrr1, $mrr2, '','',$order_by,$sort_by,$search,$status,$category,$lCat,$user_name,$country,$state,$closure,$vertical_fu, 'pending_approval');
		$data['stage'] = 'pending_approval';
		$data['is_approvals_view'] = true;

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

	public function view_leads()
	{
		$this->qualified();
	}

	public function review_leads()
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Manager - Review Leads',
			'page_heading' => 'Review Submitted Leads'
		);
		$data['info2'] = $this->if_not_login();

		$cur_uid = (int)$this->session->userdata('manager_id');
		$cur_role = (int)$this->session->userdata('manager_role');
		$team = $this->ci_admin_model->fetchTeamList($cur_uid, '');
		$is_authorized = ($cur_role === 1 || $cur_role === 2 || $cur_role === 4 || $cur_uid === 1 || has_section_perm('lead_review') || has_section_perm('back_office') || !empty($team));
		if (!$is_authorized) {
			$this->session->set_flashdata('error_message', 'Access denied. You do not have permission to review leads.');
			redirect('manager/dashboard');
			return;
		}

		$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'A.id';
		$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'DESC';
		$search = isset($_GET['search']) ? $_GET['search'] : '';
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
		$d1 = isset($_GET['d1']) ? $_GET['d1'] : (isset($_GET['exp_d1']) ? $_GET['exp_d1'] : '');
		$d2 = isset($_GET['d2']) ? $_GET['d2'] : (isset($_GET['exp_d2']) ? $_GET['exp_d2'] : '');
		$mrr1 = isset($_GET['mrr1']) ? $_GET['mrr1'] : '';
		$mrr2 = isset($_GET['mrr2']) ? $_GET['mrr2'] : '';
		$status = isset($_GET['status']) ? $_GET['status'] : '';
		$category = isset($_GET['category']) ? $_GET['category'] : '';
		$lCat = isset($_GET['lCat']) ? $_GET['lCat'] : '';
		if (!empty($lCat) && is_array($lCat)) {
			$lCat = implode(',', $lCat);
		}
		if (!empty($category) && is_array($category)) {
			$category = implode(',', $category);
		}
		if (!empty($status) && is_array($status)) {
			$status = implode(',', $status);
		}
		$closure = isset($_GET['closure']) ? $_GET['closure'] : '';
		$vertical_fu = isset($_GET['vertical_fu']) ? $_GET['vertical_fu'] : '';

		$data['st_list'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_status');
		$data['c_list'] = $this->comman_model->getAll('', array('id', 'ASC'), 'countries');
		$data['pc_list'] = $this->comman_model->getAll(array('cat_type' => 'p_cat', 'parent_id' => 0), '', 'ci_terms');
		$data['lc_list'] = $this->comman_model->getAll(array('cat_type' => 'lead_cat', 'parent_id' => 0), '', 'ci_terms');
		$data['product_list'] = $this->ci_admin_model->get_Myleads($d1, $d2, $mrr1, $mrr2, '', '', $order_by, $sort_by, $search, $status, $category, $lCat, $user_name, $country, $state, $closure, $vertical_fu, 'review_leads');
		$data['stage'] = 'review_leads';
		$data['is_review_view'] = true;

		$this->session->set_userdata('manager_return_string', 'leads/review_leads');
		$this->load->view('manager/leads/leadsview', $data);
	}

	public function convert_to_lead()
	{
		$this->if_not_login();
		$lead_id = (int)$this->input->post('lead_id');
		$remarks = trim($this->input->post('remarks'));
		$cur_uid = (int)$this->session->userdata('manager_id');
		$cur_role = (int)$this->session->userdata('manager_role');
		$cur_name = $this->session->userdata('manager_name');

		if (empty($lead_id)) {
			$this->session->set_flashdata('error_message', 'Invalid record selected.');
			redirect('manager/leads');
			return;
		}

		$lead = $this->ci_admin_model->getSingleLead($lead_id);
		if (empty($lead)) {
			$this->session->set_flashdata('error_message', 'Database record not found.');
			redirect('manager/leads');
			return;
		}

		if (!empty($lead['pipeline_stage']) && $lead['pipeline_stage'] === 'lead') {
			$this->session->set_flashdata('error_message', 'This record has already been converted to a Lead.');
			redirect('manager/leads/view/' . base64_encode($lead_id));
			return;
		}

		$lead_owner = !empty($lead['lead_assign']) ? (int)$lead['lead_assign'] : (int)$lead['created_by'];
		$team_members = $this->ci_admin_model->fetchTeamList($cur_uid, '');
		if (!is_array($team_members)) {
			$team_members = array();
		}
		$can_convert = false;
		if ($cur_role === 1 || $cur_uid === 1 || $cur_role === 2 || $cur_role === 4 || has_section_perm('lead_review', 'write') || has_section_perm('back_office', 'write')) {
			$can_convert = true;
		} elseif ($lead_owner > 0 && $cur_uid !== $lead_owner && in_array($lead_owner, array_map('intval', $team_members))) {
			$can_convert = true;
		}

		if (!$can_convert) {
			$this->session->set_flashdata('error_message', 'Access denied. You do not have permission to convert this record to a Lead.');
			redirect('manager/leads/view/' . base64_encode($lead_id));
			return;
		}

		$pipe = $this->db->get_where('ci_pipeline', array('lead_id' => $lead_id))->row_array();
		if (empty($remarks)) {
			$remarks = 'Approved and converted to Lead upon qualification review';
		}

		$now = date('Y-m-d H:i:s');
		$db_ref = (!empty($pipe['db_ref_no'])) ? $pipe['db_ref_no'] : (!empty($lead['lead_id']) && strpos($lead['lead_id'], 'D') === 0 ? $lead['lead_id'] : sprintf('D%010d', $lead_id));
		$lead_ref = (!empty($pipe['lead_ref_no'])) ? $pipe['lead_ref_no'] : $this->ci_admin_model->generate_next_ref('lead');

		$exists = $this->db->where('lead_id', $lead_id)->count_all_results('ci_pipeline');
		if ($exists > 0) {
			$this->db->where('lead_id', $lead_id)->update('ci_pipeline', array(
				'pipeline_stage' => 'lead',
				'db_ref_no' => $db_ref,
				'lead_ref_no' => $lead_ref,
				'conversion_status' => 'approved',
				'conversion_reason' => $remarks,
				'conversion_reviewed_by' => $cur_uid,
				'conversion_reviewed_at' => $now,
				'updated_at' => $now
			));
		} else {
			$this->db->insert('ci_pipeline', array(
				'lead_id' => $lead_id,
				'pipeline_stage' => 'lead',
				'db_ref_no' => $db_ref,
				'lead_ref_no' => $lead_ref,
				'conversion_status' => 'approved',
				'conversion_reason' => $remarks,
				'conversion_requested_by' => $lead_owner ?: $cur_uid,
				'conversion_requested_at' => $now,
				'conversion_reviewed_by' => $cur_uid,
				'conversion_reviewed_at' => $now,
				'created_at' => $now,
				'updated_at' => $now
			));
		}

		$this->db->where('id', $lead_id)->update('ci_leads', array(
			'lead_id' => $lead_ref
		));

		$this->db->insert('ci_pipeline_logs', array(
			'lead_id' => $lead_id,
			'action' => 'convert_to_lead',
			'stage_from' => 'database',
			'stage_to' => 'lead',
			'action_by' => $cur_uid,
			'action_by_name' => $cur_name,
			'remarks' => $remarks,
			'created_at' => $now
		));

		$this->ci_admin_model->add_notification(
			$lead_owner ?: $cur_uid,
			'lead_approved',
			'Lead Approved',
			'Lead for ' . (!empty($lead['customer']) ? $lead['customer'] : 'Record #' . $lead_id) . ' has been approved by Back Office.',
			base_url('manager/leads/view/' . base64_encode($lead_id)),
			'fa fa-check-circle',
			'#16a34a'
		);

		$this->ci_admin_model->log_admin_action('convert_to_lead', "Converted record #$lead_id to Lead (Ref: $lead_ref)", 'SUCCESS', $cur_uid, $cur_name);
		$this->session->set_flashdata('message', 'Database record has been successfully converted into a Lead (' . $lead_ref . ').');
		redirect('manager/leads/view/' . base64_encode($lead_id));
	}

	public function transfer_to_lead()
	{
		$this->convert_to_lead();
	}

	public function request_conversion()
	{
		$this->if_not_login();
		$lead_id = (int)$this->input->post('lead_id');
		$remarks = trim((string)$this->input->post('remarks'));
		$cur_uid = (int)$this->session->userdata('manager_id');
		$cur_name = (string)$this->session->userdata('manager_name');

		if (empty($lead_id)) {
			$this->session->set_flashdata('error_message', 'Invalid record selected.');
			redirect('manager/leads');
			return;
		}

		$lead = $this->ci_admin_model->getSingleLead($lead_id);
		if (empty($lead)) {
			$this->session->set_flashdata('error_message', 'Database record not found.');
			redirect('manager/leads');
			return;
		}

		if (!empty($lead['pipeline_stage']) && $lead['pipeline_stage'] === 'lead') {
			$this->session->set_flashdata('error_message', 'This record is already a Lead.');
			redirect('manager/leads/view/' . base64_encode($lead_id));
			return;
		}

		$now = date('Y-m-d H:i:s');
		$exists = $this->db->where('lead_id', $lead_id)->count_all_results('ci_pipeline');
		if ($exists == 0) {
			$this->db->insert('ci_pipeline', array(
				'lead_id' => $lead_id,
				'pipeline_stage' => 'database',
				'conversion_status' => 'pending',
				'conversion_reason' => $remarks,
				'conversion_requested_by' => $cur_uid,
				'conversion_requested_at' => $now,
				'created_at' => $now,
				'updated_at' => $now
			));
		} else {
			$this->db->where('lead_id', $lead_id)->update('ci_pipeline', array(
				'conversion_status' => 'pending',
				'conversion_reason' => $remarks,
				'conversion_requested_by' => $cur_uid,
				'conversion_requested_at' => $now,
				'updated_at' => $now
			));
		}

		$this->db->insert('ci_pipeline_logs', array(
			'lead_id' => $lead_id,
			'action' => 'request_conversion',
			'stage_from' => 'database',
			'stage_to' => 'pending_review',
			'action_by' => $cur_uid,
			'action_by_name' => $cur_name,
			'remarks' => !empty($remarks) ? $remarks : 'Submitted conversion request for review',
			'created_at' => $now
		));

		$this->ci_admin_model->log_admin_action('request_conversion', "Requested conversion for record #$lead_id", 'SUCCESS', $cur_uid, $cur_name);
		$this->session->set_flashdata('message', 'Conversion request has been submitted for review.');
		redirect('manager/leads/view/' . base64_encode($lead_id));
	}

	public function reject_conversion()
	{
		$this->if_not_login();
		$lead_id = (int)$this->input->post('lead_id');
		$remarks = trim((string)$this->input->post('remarks'));
		$cur_uid = (int)$this->session->userdata('manager_id');
		$cur_role = (int)$this->session->userdata('manager_role');
		$cur_name = (string)$this->session->userdata('manager_name');

		if (empty($lead_id)) {
			$this->session->set_flashdata('error_message', 'Invalid record selected.');
			redirect('manager/leads');
			return;
		}

		$lead = $this->ci_admin_model->getSingleLead($lead_id);
		if (empty($lead)) {
			$this->session->set_flashdata('error_message', 'Database record not found.');
			redirect('manager/leads');
			return;
		}

		$lead_owner = !empty($lead['lead_assign']) ? (int)$lead['lead_assign'] : (int)$lead['created_by'];
		$team_members = $this->ci_admin_model->fetchTeamList($cur_uid, '');
		if (!is_array($team_members)) {
			$team_members = array();
		}
		$can_review = false;
		if ($cur_role === 1 || $cur_uid === 1 || $cur_role === 2 || $cur_role === 4 || has_section_perm('lead_review', 'write') || has_section_perm('back_office', 'write')) {
			$can_review = true;
		} elseif ($lead_owner > 0 && $cur_uid !== $lead_owner && in_array($lead_owner, array_map('intval', $team_members))) {
			$can_review = true;
		}

		if (!$can_review) {
			$this->session->set_flashdata('error_message', 'Access denied. You do not have permission to review this conversion request.');
			redirect('manager/leads/view/' . base64_encode($lead_id));
			return;
		}

		$now = date('Y-m-d H:i:s');
		$this->db->where('lead_id', $lead_id)->update('ci_pipeline', array(
			'conversion_status' => 'rejected',
			'conversion_reviewed_by' => $cur_uid,
			'conversion_reviewed_at' => $now,
			'updated_at' => $now
		));

		$this->db->insert('ci_pipeline_logs', array(
			'lead_id' => $lead_id,
			'action' => 'reject_conversion',
			'stage_from' => 'pending_review',
			'stage_to' => 'database',
			'action_by' => $cur_uid,
			'action_by_name' => $cur_name,
			'remarks' => !empty($remarks) ? $remarks : 'Conversion request rejected',
			'created_at' => $now
		));

		$this->ci_admin_model->add_notification(
			$lead_owner ?: $cur_uid,
			'lead_rejected',
			'Lead Conversion Rejected',
			'Conversion request for record #' . $lead_id . ' (' . (!empty($lead['customer']) ? $lead['customer'] : '') . ') was rejected: ' . $remarks,
			base_url('manager/leads/view/' . base64_encode($lead_id)),
			'fa fa-times-circle',
			'#dc2626'
		);

		$this->ci_admin_model->log_admin_action('reject_conversion', "Rejected conversion for record #$lead_id", 'SUCCESS', $cur_uid, $cur_name);
		$this->session->set_flashdata('message', 'Conversion request has been rejected.');
		redirect('manager/leads/view/' . base64_encode($lead_id));
	}

	public function request_order()
	{
		$this->if_not_login();
		$lead_id = (int)$this->input->post('lead_id');
		$service_requirements = trim((string)$this->input->post('service_requirements'));
		$remarks = trim((string)$this->input->post('remarks'));
		$cur_uid = (int)$this->session->userdata('manager_id');
		$cur_role = (int)$this->session->userdata('manager_role');
		$cur_name = (string)$this->session->userdata('manager_name');

		if (empty($lead_id)) {
			$this->session->set_flashdata('error_message', 'Invalid record selected.');
			redirect('manager/leads/view_leads');
			return;
		}

		$lead = $this->ci_admin_model->getSingleLead($lead_id);
		if (empty($lead)) {
			$this->session->set_flashdata('error_message', 'Lead record not found.');
			redirect('manager/leads/view_leads');
			return;
		}

		if (empty($lead['pipeline_stage']) || $lead['pipeline_stage'] !== 'lead') {
			$this->session->set_flashdata('error_message', 'Only approved Leads can be converted to an Order.');
			redirect('manager/leads/view/' . base64_encode($lead_id));
			return;
		}

		if (!empty($lead['order_request_status']) && in_array($lead['order_request_status'], array('pending', 'approved'))) {
			$this->session->set_flashdata('error_message', 'An Order process is already active for this Lead.');
			redirect('manager/leads/view/' . base64_encode($lead_id));
			return;
		}

		$lead_owner = !empty($lead['lead_assign']) ? (int)$lead['lead_assign'] : (int)$lead['created_by'];
		$is_collab = (!empty($lead['agents']) && in_array($cur_uid, array_map('intval', explode(',', $lead['agents']))));
		$team_members = $this->ci_admin_model->fetchTeamList($cur_uid, '');
		if (!is_array($team_members)) {
			$team_members = array();
		}
		$can_request = ($cur_role === 1 || $cur_uid === 1 || $cur_role === 4 || $cur_uid === $lead_owner || $is_collab || in_array($lead_owner, array_map('intval', $team_members)));

		if (!$can_request) {
			$this->session->set_flashdata('error_message', 'Access denied. You do not have permission to request Order conversion.');
			redirect('manager/leads/view/' . base64_encode($lead_id));
			return;
		}

		$now = date('Y-m-d H:i:s');
		$full_remarks = $remarks;
		if (!empty($service_requirements)) {
			$full_remarks = 'Service: ' . $service_requirements . ($remarks ? ' | Note: ' . $remarks : '');
		}

		$this->db->where('lead_id', $lead_id)->update('ci_pipeline', array(
			'order_request_status' => 'pending',
			'order_requested_by' => $cur_uid,
			'order_requested_at' => $now,
			'order_request_remarks' => $full_remarks,
			'is_locked' => 1,
			'locked_reason' => 'Order conversion requested',
			'locked_at' => $now,
			'updated_at' => $now
		));

		$this->db->insert('ci_pipeline_logs', array(
			'lead_id' => $lead_id,
			'action' => 'request_order',
			'stage_from' => 'lead',
			'stage_to' => 'pending_order_process',
			'action_by' => $cur_uid,
			'action_by_name' => $cur_name,
			'remarks' => !empty($full_remarks) ? $full_remarks : 'Submitted Lead for Order creation and processing',
			'created_at' => $now
		));

		$this->ci_admin_model->log_admin_action('request_order', "Submitted Lead #$lead_id for Order creation", 'SUCCESS', $cur_uid, $cur_name);
		$this->session->set_flashdata('message', 'Lead has been submitted for Order processing. This Lead is now locked for editing.');
		redirect('manager/leads/view/' . base64_encode($lead_id));
	}

	public function ajax_get_state()
	{

	   $str='<option value="" disabled>Select State</option>';
	  
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

	public function ajax_search_companies()
	{
		$term = trim((string)$this->input->get('term', true));
		if (empty($term)) {
			$term = trim((string)$this->input->get('q', true));
		}
		$results = array();
		if (strlen($term) >= 1) {
			$this->db->select('DISTINCT(customer) as company', false);
			$this->db->from('ci_leads');
			$this->db->like('customer', $term);
			$this->db->where('customer IS NOT NULL', null, false);
			$this->db->where("customer != ''", null, false);
			$this->db->limit(30);
			$query = $this->db->get();
			if ($query && $query->num_rows() > 0) {
				foreach ($query->result_array() as $row) {
					$c = trim((string)$row['company']);
					if (!empty($c) && !in_array($c, $results)) {
						$results[] = $c;
					}
				}
			}
		}
		header('Content-Type: application/json');
		echo json_encode($results);
		exit;
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
		public function view($id='',$action='') 

		{

			 $data=array();
				$data['info']=array(
					'title'=>'Manager - Leads',
					'page_heading'=>'Preview Lead Information'
			);
			$data['info2']=$this->if_not_login();

			if (!is_numeric($id)) {
				$decoded = base64_decode($id, true);
				if ($decoded !== false && is_numeric($decoded)) {
					$id = $decoded;
				}
			}
			$id = (int)$id;
			$data['p_info']=$this->ci_admin_model->getSingleLead($id);
			$data['p_team']= $this->ci_admin_model->fetchTeamList($this->session->userdata('manager_id'),'');
			if(!empty($data['p_info'])){
				$cur_mgr_id = (int)$this->session->userdata('manager_id');
				$cur_mgr_role = (int)$this->session->userdata('manager_role');
				$is_bo = ($cur_mgr_role === 1 || $cur_mgr_role === 2 || $cur_mgr_role === 4 || $cur_mgr_id === 1 || has_section_perm('back_office') || has_section_perm('lead_review'));
				$lead_owner = !empty($data['p_info']['lead_assign']) ? (int)$data['p_info']['lead_assign'] : (int)$data['p_info']['created_by'];
				$team_members = is_array($data['p_team']) ? array_map('intval', $data['p_team']) : array();
				$is_mgr_above_owner = (!empty($team_members) && in_array($lead_owner, $team_members));
				if (!$is_bo) {
					$is_collab = (!empty($data['p_info']['agents']) && in_array($cur_mgr_id, array_map('intval', explode(',', $data['p_info']['agents']))));
					if ($lead_owner !== $cur_mgr_id && !$is_collab && !$is_mgr_above_owner) {
						$this->session->set_flashdata('error_message', 'You do not have access to this lead.');
						$return_url = urldecode($this->session->userdata('manager_return_string'));
						redirect('/manager/' . ($return_url ?: 'leads'));
						return;
					}
				}

				$can_convert = false;
				if ($is_bo) {
					$can_convert = true;
				} elseif ($lead_owner > 0 && $cur_mgr_id !== $lead_owner && $is_mgr_above_owner) {
					$can_convert = true;
				}
				$is_lead = (!empty($data['p_info']['pipeline_stage']) && $data['p_info']['pipeline_stage'] === 'lead');
				$conv_status = !empty($data['p_info']['conversion_status']) ? $data['p_info']['conversion_status'] : '';
				$data['can_convert_to_lead'] = $can_convert;
				$data['is_already_lead'] = $is_lead;
				$data['is_pending_review'] = (!$is_lead && $conv_status === 'pending');
				$data['can_review_conversion'] = ($can_convert && $data['is_pending_review']);
				$data['can_request_conversion'] = (!$is_lead && $conv_status !== 'pending');
				$data['pipeline_logs'] = $this->db->where('lead_id', $id)->order_by('id', 'DESC')->get('ci_pipeline_logs')->result_array();

				$data['transfer_history'] = $this->db->select('T.*, U1.name as from_user_name, U2.name as to_user_name, U3.name as transferred_by_name')
					->from('ci_lead_transfers as T')
					->join('ci_user as U1', 'T.from_user_id = U1.id', 'LEFT')
					->join('ci_user as U2', 'T.to_user_id = U2.id', 'LEFT')
					->join('ci_user as U3', 'T.transferred_by = U3.id', 'LEFT')
					->where('T.lead_id', $id)
					->order_by('T.id', 'DESC')
					->get()
					->result_array();

				$data['bo_list']=$this->comman_model->getAll(array('lead_id'=>$id),'','ci_boleads');
				$data['follow_list']=$this->comman_model->getAll(array('lead_id'=>$id,'lead_type'=>'lead'),'','ci_followup');
				$data['m_list']=$this->comman_model->getAll(array('lead_id'=>$id),array('id','desc'),'ci_meetings','first');
				$data['call_logs'] = $this->db->select('L.*, U.name as agent_name')
					->from('ci_call_logs as L')
					->join('ci_user as U', 'U.id = L.user_id', 'left')
					->where('L.lead_id', $id)
					->where('L.duration >', 0)
					->where_in('L.call_status', array('Completed', 'Answered', 'Connected'))
					->where_not_in('L.disposition', array('Dialing', 'In Process', 'processing'))
					->order_by('L.id', 'DESC')
					->get()
					->result_array();
				$data['order_info'] = $this->db->get_where('ci_orders', array('lead_id' => $id))->row_array();
				$raw_db_ref = !empty($data['p_info']['db_ref_no']) ? $data['p_info']['db_ref_no'] : '';
				if (!empty($raw_db_ref) && preg_match('/^D[0-9]{10}$/', $raw_db_ref)) {
					$data['db_ref_no'] = $raw_db_ref;
				} else {
					$data['db_ref_no'] = sprintf('D%010d', $id);
				}
				$raw_lead_ref = !empty($data['p_info']['lead_ref_no']) ? $data['p_info']['lead_ref_no'] : (!empty($data['p_info']['lead_id']) ? $data['p_info']['lead_id'] : '');
				if (!empty($raw_lead_ref) && preg_match('/^L[0-9]{10}$/', $raw_lead_ref)) {
					$data['lead_ref_no'] = $raw_lead_ref;
				} elseif (!empty($data['is_already_lead']) && !empty($raw_lead_ref)) {
					$data['lead_ref_no'] = $raw_lead_ref;
				} else {
					$data['lead_ref_no'] = '';
				}
				$data['order_ref_no'] = !empty($data['order_info']['order_no']) ? $data['order_info']['order_no'] : '';
				$data['sales_ref_no'] = !empty($data['order_info']['sales_ref_no']) ? $data['order_info']['sales_ref_no'] : '';
				$data['is_locked'] = (!empty($data['p_info']['is_locked']) && (int)$data['p_info']['is_locked'] === 1);
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
	

	public function add()

	{

		

		$data=array();
		$data['info']=array(
			'title'=>'Manager - Database',
			'page_heading'=>'Add Database'
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
		$data['data_sources'] = $this->comman_model->getAll(array('cat_type'=>'data_source','status'=>1),'','ci_terms');
		$data['database_categories'] = $this->comman_model->getAll(array('cat_type'=>'database_cat','status'=>1),'','ci_terms');
		$data['company_list'] = $this->db->distinct()->select('customer')->from('ci_leads')->where('customer IS NOT NULL', null, false)->where("customer != ''", null, false)->order_by('customer', 'ASC')->limit(500)->get()->result_array();

		if($this->input->server('REQUEST_METHOD')==='POST')
		{	 			

				extract($_POST);

				$customer_val = !empty($this->input->post('customer')) ? trim($this->input->post('customer')) : (!empty($this->input->post('company_name')) ? trim($this->input->post('company_name')) : '');

				$lead_assign = $this->input->post('lead_assign');

				$f_date = !empty($followup_date) ? db_date($followup_date) : '';
				$c_date = !empty($close_date) ? db_date($close_date) : '';
				if(!empty($f_date) && !empty($c_date)){
					if(strtotime($c_date) < strtotime($f_date)){
						$c_date = $f_date;
					}
				}

				$ind_cat = !empty($industry_category) ? trim($industry_category) : (isset($business_category) ? trim($business_category) : '');
				$stat_cat = !empty($status_category) ? trim($status_category) : (isset($lost_reason) ? trim($lost_reason) : '');
				$q_amt = (isset($quote_amount) && is_numeric($quote_amount) && $quote_amount !== '') ? floatval($quote_amount) : null;

				if (empty($tl_emirate)) {
					$tl_zone = '';
					$tl_authority = '';
				} elseif (empty($tl_zone)) {
					$tl_authority = '';
				}

				$creator_id = (int)$this->session->userdata('manager_id');
				$manager_role = (int)$this->session->userdata('manager_role');
				if (($manager_role == 1 || $manager_role == 4) && !empty($this->input->post('primary_owner'))) {
					$assigned_primary = (int)$this->input->post('primary_owner');
				} else {
					$assigned_primary = $creator_id;
				}
				$assigned_agents = '';
				if (!empty($lead_assign)) {
					if (is_array($lead_assign)) {
						$clean_assigns = array_values(array_diff(array_unique(array_filter(array_map('intval', $lead_assign))), array($assigned_primary)));
						$assigned_agents = implode(',', $clean_assigns);
					} else {
						$single_val = (int)$lead_assign;
						if ($single_val > 0 && $single_val !== $assigned_primary) {
							$assigned_agents = (string)$single_val;
						}
					}
				}

				$list= array(				
				'lead_id'=> 100000000,
				'customer'=>$customer_val,
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
				'industry_category'=>$ind_cat,
				'business_category'=>$ind_cat,
				'quote_status'=>!empty($quote_status) ? trim($quote_status) : null,
				'quote_mode'=>!empty($quote_mode) ? trim($quote_mode) : null,
				'quote_amount'=>$q_amt,
				'call_status'=>!empty($call_status) ? trim($call_status) : null,
				'corporate_tax_status'=>!empty($corporate_tax_status) ? trim($corporate_tax_status) : 'Not Registered',
				'vat_status'=>!empty($vat_status) ? trim($vat_status) : 'Not Registered',
				'data_source'=>!empty($data_source) ? trim($data_source) : '',
				'data_batch_referral_no'=>!empty($data_batch_referral_no) ? trim($data_batch_referral_no) : '',
				'database_category'=>!empty($database_category) ? trim($database_category) : '',
				'status_category'=>$stat_cat,
				'status_remarks'=>!empty($status_remarks) ? trim($status_remarks) : '',
				'company_status'=>!empty($company_status) ? trim($company_status) : 'Active',
				'dnd_status'=>isset($dnd_status) ? $dnd_status : 'No',
				'dnd_check_date'=>!empty($dnd_check_date) ? db_date($dnd_check_date) : '',
				'location'=>$location,
				'status'=>$status,
				'lost_reason'=>!empty($stat_cat) ? $stat_cat : null,

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
				'lead_assign'=>$assigned_primary,
				'agents'=>$assigned_agents,
				'lead_source'=>$lead_source,

				'tl_number'=>isset($tl_number) ? $tl_number : '',
				'tl_authority'=>isset($tl_authority) ? $tl_authority : '',
				'tl_emirate'=>isset($tl_emirate) ? $tl_emirate : '',
				'tl_zone'=>isset($tl_zone) ? $tl_zone : '',
				'tl_issue_date'=>!empty($tl_issue_date) ? db_date($tl_issue_date) : '',
				'tl_expire_date'=>!empty($tl_expire_date) ? db_date($tl_expire_date) : '',

				'v_amount'=>$v_amount,
				'v_remark'=>$v_remark,

				'created_by'=>$this->session->userdata('manager_id'),
				'admin_status'=>1,
				'created'=>date('Y-m-d')			

				);		
				$this->session->set_flashdata('message', 'Lead has been added successfully!');
				$lead_id = $this->comman_model->saveData('ci_leads',$list);
				$db_ref = $this->ci_admin_model->generate_next_ref('database');
				$this->comman_model->updateData('id',$lead_id,array('lead_id'=>$db_ref),'ci_leads');
				$this->db->insert('ci_pipeline', array(
					'lead_id' => $lead_id,
					'pipeline_stage' => 'database',
					'db_ref_no' => $db_ref,
					'conversion_status' => 'none',
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				));
				$this->ci_admin_model->log_admin_action(
					'lead_create',
					"Created lead #$lead_id ($customer)",
					'SUCCESS',
					$this->session->userdata('manager_id'),
					$this->session->userdata('manager_name')
				);

				$user_role = (int)$this->session->userdata('manager_role');
				$user_id = (int)$this->session->userdata('manager_id');
				$user_dept = (string)$this->session->userdata('manager_department');
				$can_edit_bo = ($user_role === 2 || $user_role === 1 || $user_id === 1 || strcasecmp($user_dept, 'Back Office') === 0);

		     	if(!empty($bo_cat) && $can_edit_bo){
		     		for($i=0;$i<=6;$i++){
		     			$so= array(
		     				'lead_id'=>$lead_id,
		     				'bo_cat'=>isset($bo_cat[$i]) ? $bo_cat[$i] : '',
		     				'bo_date'=>isset($bo_date[$i]) ? $bo_date[$i] : '',
		     				'bo_status'=>isset($bo_status[$i]) ? $bo_status[$i] : 'Not Applicable',
		     				'bo_ref'=>isset($bo_ref[$i]) ? $bo_ref[$i] : '',
		     				'bo_remark'=>isset($bo_remark[$i]) ? $bo_remark[$i] : ''		     				
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
					
						'a1'=>$a1,
						'a2'=>$a2,
						'created'=>date('Y-m-d'),
						'created_by'=>$this->session->userdata('manager_id')		     				
					);
					$this->comman_model->saveData('ci_meetings',$mo);
			
			}

			 redirect('manager/leads');

       }

        $this->load->view('manager/leads/add-database',$data);

   

	   

	}

		

     public function edit($id='',$action='')

	 {       	

		$data=array();
		$data['info']=array(
			'title'=>'Manager- Leads',
			'page_heading'=>'Edit Lead'
		);
		$data['info2']=$this->if_not_login();
		if (!is_numeric($id)) {
			$decoded = base64_decode($id, true);
			if ($decoded !== false && is_numeric($decoded)) {
				$id = $decoded;
			}
		}
		$id = (int)$id;
		$data['list']=$this->comman_model->getAll(array('id'=>$id),'','ci_leads','first');
		
		$data['slist']=$this->ci_admin_model->getSingleLead($id);
		$data['v_list'] =$this->ci_admin_model->getShortVerticals($this->session->userdata('manager_id'));

		if(empty($data['list'])){
			$this->session->set_flashdata('error_message', 'Not allowed');
			if($action=='tm'){
				redirect('/manager/team/leads');
			}else{
				redirect('/manager/leads');
			}
			return;
		}  
		
		$cur_role = (int)$this->session->userdata('manager_role');
		$cur_mgr_id = (int)$this->session->userdata('manager_id');
		$is_bo = ($cur_role === 1 || $cur_role === 2 || $cur_role === 4 || $cur_mgr_id === 1 || has_section_perm('back_office', 'write') || has_section_perm('lead_review', 'write'));

		$pipe_lock = $this->db->get_where('ci_pipeline', array('lead_id' => $id))->row_array();
		if (!empty($pipe_lock['is_locked']) && (int)$pipe_lock['is_locked'] === 1 && !$is_bo) {
			$this->session->set_flashdata('error_message', 'This Lead is locked because an Order conversion process is active. Editing is disabled.');
			redirect('manager/leads/view/' . base64_encode($id));
			return;
		}

		if (!$is_bo) {
			$lead_owner = !empty($data['list']['lead_assign']) ? (int)$data['list']['lead_assign'] : (int)$data['list']['created_by'];
			$is_collab = (!empty($data['list']['agents']) && in_array($cur_mgr_id, array_map('intval', explode(',', $data['list']['agents']))));
			$subordinates = $this->ci_admin_model->fetchTeamList($cur_mgr_id, '');
			$is_team_lead = (is_array($subordinates) && in_array($lead_owner, array_map('intval', $subordinates)));
			if ($lead_owner !== $cur_mgr_id && !$is_collab && !$is_team_lead) {
				$this->session->set_flashdata('error_message', 'You do not have permission to edit this lead.');
				redirect($action == 'tm' ? '/manager/team/leads' : '/manager/leads');
				return;
			}
			if (!empty($data['slist']['user_level']) && $data['slist']['user_level'] < $this->session->userdata('manager_level')) {
				$this->session->set_flashdata('error_message', 'Not allowed');
				if ($action == 'tm') {
					redirect('/manager/team/leads');
				} else {
					redirect('/manager/leads');
				}
				return;
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
		$data['data_sources'] = $this->comman_model->getAll(array('cat_type'=>'data_source','status'=>1),'','ci_terms');
		$data['database_categories'] = $this->comman_model->getAll(array('cat_type'=>'database_cat','status'=>1),'','ci_terms');
		$data['action']= $action;
		$data['follow_list']=$this->comman_model->getAll(array('lead_id'=>$id,'lead_type'=>'lead'),'','ci_followup');
		$data['meeting_list']=$this->comman_model->getAll(array('lead_id'=>$id),'','ci_meetings');
		if($this->input->server('REQUEST_METHOD')==='POST'){
			extract($_POST);

			$existing_owner = !empty($data['list']['lead_assign']) ? (int)$data['list']['lead_assign'] : (!empty($data['list']['created_by']) ? (int)$data['list']['created_by'] : (int)$this->session->userdata('manager_id'));
			$manager_role = (int)$this->session->userdata('manager_role');
			if (($manager_role == 1 || $manager_role == 4) && !empty($this->input->post('primary_owner'))) {
				$assigned_primary = (int)$this->input->post('primary_owner');
			} else {
				$assigned_primary = $existing_owner;
			}
			$assigned_agents = '';
			if (!empty($lead_assign)) {
				if (is_array($lead_assign)) {
					$clean_assigns = array_values(array_diff(array_unique(array_filter(array_map('intval', $lead_assign))), array($assigned_primary)));
					$assigned_agents = implode(',', $clean_assigns);
				} else {
					$single_val = (int)$lead_assign;
					if ($single_val > 0 && $single_val !== $assigned_primary) {
						$assigned_agents = (string)$single_val;
					}
				}
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
					$c_date = $max_fu;
				}
			}

			$ind_cat = !empty($industry_category) ? trim($industry_category) : (isset($business_category) ? trim($business_category) : '');
			$stat_cat = !empty($status_category) ? trim($status_category) : (isset($lost_reason) ? trim($lost_reason) : '');
			$q_amt = (isset($quote_amount) && is_numeric($quote_amount) && $quote_amount !== '') ? floatval($quote_amount) : null;

			if (empty($tl_emirate)) {
				$tl_zone = '';
				$tl_authority = '';
			} elseif (empty($tl_zone)) {
				$tl_authority = '';
			}

			$list= array(
				'customer'=>$customer,
				'vertical_id'=>$vertical_id,
				'category'=>$category,
				'a_number'=>$a_number,
				'a_department'=>$a_department,
				'r_amount'=>$r_amount,
				'r_date'=>!empty($r_date) ? db_date($r_date) : '',
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
				'industry_category'=>$ind_cat,
				'business_category'=>$ind_cat,
				'quote_status'=>!empty($quote_status) ? trim($quote_status) : null,
				'quote_mode'=>!empty($quote_mode) ? trim($quote_mode) : null,
				'quote_amount'=>$q_amt,
				'call_status'=>!empty($call_status) ? trim($call_status) : null,
				'corporate_tax_status'=>!empty($corporate_tax_status) ? trim($corporate_tax_status) : 'Not Registered',
				'vat_status'=>!empty($vat_status) ? trim($vat_status) : 'Not Registered',
				'data_source'=>!empty($data_source) ? trim($data_source) : '',
				'data_batch_referral_no'=>!empty($data_batch_referral_no) ? trim($data_batch_referral_no) : '',
				'database_category'=>!empty($database_category) ? trim($database_category) : '',
				'status_category'=>$stat_cat,
				'status_remarks'=>!empty($status_remarks) ? trim($status_remarks) : '',
				'company_status'=>!empty($company_status) ? trim($company_status) : 'Active',
				'dnd_status'=>isset($dnd_status) ? $dnd_status : 'No',
				'dnd_check_date'=>!empty($dnd_check_date) ? db_date($dnd_check_date) : '',

				'location'=>$location,
				'status'=>$status,				
				'lost_reason'=>!empty($stat_cat) ? $stat_cat : null,
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
				'lead_assign'=>$assigned_primary,
				'agents'=>$assigned_agents,
				'lead_source'=>$lead_source,

				'tl_number'=>isset($tl_number) ? $tl_number : '',
				'tl_authority'=>isset($tl_authority) ? $tl_authority : '',
				'tl_emirate'=>isset($tl_emirate) ? $tl_emirate : '',
				'tl_zone'=>isset($tl_zone) ? $tl_zone : '',
				'tl_issue_date'=>!empty($tl_issue_date) ? db_date($tl_issue_date) : '',
				'tl_expire_date'=>!empty($tl_expire_date) ? db_date($tl_expire_date) : '',

				'v_amount'=>$v_amount,
				'v_remark'=>$v_remark,
				
				'edited_by'=>$this->session->userdata('manager_id'),
				'admin_status'=>1,
				'modified'=>date('Y-m-d')
				);
			$response= $this->comman_model->updateData('id',$id,$list,'ci_leads');	
			$this->ci_admin_model->log_admin_action(
				'lead_update',
				"Updated lead #$id ($customer)",
				'SUCCESS',
				$this->session->userdata('manager_id'),
				$this->session->userdata('manager_name')
			);	
				$user_role = (int)$this->session->userdata('manager_role');
				$user_id = (int)$this->session->userdata('manager_id');
				$user_dept = (string)$this->session->userdata('manager_department');
				$can_edit_bo = ($user_role === 2 || $user_role === 1 || $user_id === 1 || strcasecmp($user_dept, 'Back Office') === 0);

		     	if(!empty($bo_id) && $can_edit_bo){
		     		for($i=0;$i<=6;$i++){
		     			$so= array(		     				
		     				'bo_cat'=>isset($bo_cat[$i]) ? $bo_cat[$i] : '',
		     				'bo_date'=>isset($bo_date[$i]) ? $bo_date[$i] : '',
		     				'bo_status'=>isset($bo_status[$i]) ? $bo_status[$i] : 'Not Applicable',
		     				'bo_ref'=>isset($bo_ref[$i]) ? $bo_ref[$i] : '',
		     				'bo_remark'=>isset($bo_remark[$i]) ? $bo_remark[$i] : ''		     				
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
		  if (!is_numeric($id)) {
			  $decoded = base64_decode($id, true);
			  if ($decoded !== false && is_numeric($decoded)) {
				  $id = $decoded;
			  }
		  }
		  $id = (int)$id;
		  $data['list']=$this->comman_model->getAll(array('id'=>$id),'','ci_leads','first');
		  
		  $data['slist']=$this->ci_admin_model->getSingleLead($id);
		  $data['v_list'] =$this->ci_admin_model->getShortVerticals($this->session->userdata('manager_id'));

		  if(empty($data['list'])){
			  $this->session->set_flashdata('error_message', 'Not allowed');
			  if($action=='tm'){
				  redirect('/manager/team/leads');
			  }else{
				  redirect('/manager/leads');
			  }
			  return;
		  }  

		  $cur_role = (int)$this->session->userdata('manager_role');
		  $cur_mgr_id = (int)$this->session->userdata('manager_id');
		  $is_bo = ($cur_role === 1 || $cur_role === 2 || $cur_role === 4 || $cur_mgr_id === 1 || has_section_perm('back_office', 'write') || has_section_perm('lead_review', 'write'));
		  $pipe_lock = $this->db->get_where('ci_pipeline', array('lead_id' => $id))->row_array();
		  if (!empty($pipe_lock['is_locked']) && (int)$pipe_lock['is_locked'] === 1 && !$is_bo) {
			  $this->session->set_flashdata('error_message', 'This Lead is locked because an Order conversion process is active. Editing is disabled.');
			  redirect('manager/leads/view/' . base64_encode($id));
			  return;
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

			 redirect('manager/leads');

		 }	

		$response= $this->ci_admin_model->deleteData('id',$id,'ci_leads');

		if($response)

		{

			redirect('manager/leads');

		}

    }  

    public function leadDeleteAjax($id = '')
	{
		$manager_id = (int)$this->session->userdata('manager_id');
		$m_role = (int)$this->session->userdata('manager_role');
		if (empty($m_role) && $manager_id == 1) {
			$m_role = 1;
		}
		if ($m_role != 1 && $m_role != 4 && $manager_id != 1) {
			echo json_encode(array('status' => false, 'message' => 'Unauthorized access'));
			return;
		}

		$ids = array();
		if (!empty($id)) {
			if (!is_numeric($id)) {
				$decoded = base64_decode($id, true);
				if ($decoded !== false && is_numeric($decoded)) {
					$id = $decoded;
				}
			}
			$ids[] = (int)$id;
		}

		$post_ids = $this->input->post('ids');
		if (!empty($post_ids)) {
			if (is_array($post_ids)) {
				foreach ($post_ids as $pid) {
					$ids[] = (int)$pid;
				}
			} else {
				$parts = explode(',', $post_ids);
				foreach ($parts as $pid) {
					if (is_numeric(trim($pid))) {
						$ids[] = (int)trim($pid);
					}
				}
			}
		}

		$ids = array_unique(array_filter($ids));
		if (empty($ids)) {
			echo json_encode(array('status' => false, 'message' => 'No valid lead IDs provided'));
			return;
		}

		$this->db->where_in('id', $ids);
		$this->db->delete('ci_leads');

		$this->db->where_in('lead_id', $ids);
		$this->db->delete('ci_boleads');

		$this->db->where_in('lead_id', $ids);
		$this->db->delete('ci_followup');

		$this->db->where_in('lead_id', $ids);
		$this->db->delete('ci_meetings');

		$this->db->where_in('lead_id', $ids);
		$this->db->delete('ci_call_logs');

		$del_count = count($ids);
		$this->ci_admin_model->log_admin_action(
			'lead_delete',
			"Deleted $del_count leads (IDs: " . implode(',', $ids) . ")",
			'SUCCESS',
			$manager_id,
			$this->session->userdata('manager_name')
		);

		echo json_encode(array('status' => true, 'count' => $del_count, 'message' => "$del_count lead(s) deleted successfully"));
    }

	



	public function export()
	{
		$this->if_not_login();
		$role = (int)$this->session->userdata('manager_role');
		$mgr_id = (int)$this->session->userdata('manager_id');
		if ($role !== 1 && $mgr_id !== 1) {
			$this->session->set_flashdata('error_message', 'Access denied. Only Administrators have permission to export Leads data.');
			redirect('manager/dashboard');
			return;
		}

		
		if ($this->input->get('download') == 1) {
			$user_id = $this->input->get('user_id');

			
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
				'Industry Category',
				'Quote Status',
				'Quote Mode',
				'Quote Amount',
				'Corporate Tax Status',
				'VAT Status',
				'Data Source',
				'Data Batch Referral Number',
				'Database Category',
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
				'Status Category',
				'Status Remarks',
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

					$ind_val = !empty($l['industry_category']) ? $l['industry_category'] : (!empty($l['business_category']) ? $l['business_category'] : '');
					$stat_cat_val = !empty($l['status_category']) ? $l['status_category'] : (!empty($l['lost_reason']) ? $l['lost_reason'] : '');

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
						$ind_val,
						!empty($l['quote_status']) ? $l['quote_status'] : '',
						!empty($l['quote_mode']) ? $l['quote_mode'] : '',
						isset($l['quote_amount']) ? $l['quote_amount'] : '',
						!empty($l['corporate_tax_status']) ? $l['corporate_tax_status'] : '',
						!empty($l['vat_status']) ? $l['vat_status'] : '',
						!empty($l['data_source']) ? $l['data_source'] : '',
						!empty($l['data_batch_referral_no']) ? $l['data_batch_referral_no'] : '',
						!empty($l['database_category']) ? $l['database_category'] : '',
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
						$stat_cat_val,
						!empty($l['status_remarks']) ? $l['status_remarks'] : '',
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

		
		$raw_headers = fgetcsv($handle, 5000, ',');
		if (!$raw_headers) {
			fclose($handle);
			$this->session->set_flashdata('error_message', 'The uploaded CSV file is empty.');
			redirect('manager/leads/import');
			return;
		}

		
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

			$business_category = $get_val($row, array('industrycategory', 'industrycat', 'businesscategory', 'businesscat'));
			$industry_category = $business_category;
			$quote_status = $get_val($row, array('quotestatus', 'quotestat', 'quote'));
			$quote_mode = $get_val($row, array('quotemode', 'quotemod'));
			$quote_amount_val = $get_val($row, array('quoteamount', 'quoteamt', 'quotedamount'));
			$quote_amount = (is_numeric($quote_amount_val) && $quote_amount_val !== '') ? floatval($quote_amount_val) : null;
			$corporate_tax_status = $get_val($row, array('corporatetaxstatus', 'corporatetax', 'corptax'));
			$vat_status = $get_val($row, array('vatstatus', 'vat'));
			$data_source = $get_val($row, array('datasource', 'sourcecategory'));
			$data_batch_referral_no = $get_val($row, array('databatchreferralnumber', 'databatchreferralno', 'batchreferralno', 'batchno', 'referralno'));
			$database_category = $get_val($row, array('databasecategory', 'databasecat'));
			if ($assigned_user_id == 0 && empty($database_category)) {
				$database_category = 'Non-Allotted Database';
			}
			$status_category = $get_val($row, array('statuscategory', 'statuscat', 'lostreason'));
			$status_remarks = $get_val($row, array('statusremarks', 'statusremark'));

			$dnd_raw = strtolower(trim($get_val($row, array('dncrdnd', 'dncr', 'dnd', 'dndstatus', 'tncrtnd', 'tncr'))));
			if (in_array($dnd_raw, array('yes', 'y', '1', 'true', 'dnd', 'dncr'))) {
				$dnd_status = 'Yes';
			} else {
				$dnd_status = 'No';
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
			$status = 36;
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

			$company_status_raw = strtolower(trim($get_val($row, array('companystatus', 'company_status', 'cstatus'))));
			if (in_array($company_status_raw, array('closed', 'close'))) {
				$company_status = 'Closed';
			} elseif (in_array($company_status_raw, array('inactive', 'in-active', 'deactive'))) {
				$company_status = 'Inactive';
			} else {
				$company_status = 'Active';
			}

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
				'company_status' => $company_status,
				'industry_category' => $industry_category,
				'business_category' => $business_category,
				'quote_status' => !empty($quote_status) ? $quote_status : null,
				'quote_mode' => !empty($quote_mode) ? $quote_mode : null,
				'quote_amount' => $quote_amount,
				'corporate_tax_status' => !empty($corporate_tax_status) ? $corporate_tax_status : 'Not Registered',
				'vat_status' => !empty($vat_status) ? $vat_status : 'Not Registered',
				'data_source' => !empty($data_source) ? $data_source : '',
				'data_batch_referral_no' => !empty($data_batch_referral_no) ? $data_batch_referral_no : '',
				'database_category' => !empty($database_category) ? $database_category : '',
				'status_category' => !empty($status_category) ? $status_category : '',
				'status_remarks' => !empty($status_remarks) ? $status_remarks : '',
				'lost_reason' => !empty($status_category) ? $status_category : null,
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
				'c_partner' => !empty($c_partner) ? $c_partner : 'Seven Strokes',
				'otc' => $otc,
				'lead_cat' => $lead_cat,
				'lead_assign' => $assigned_user_id,
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
			'Company Status',
			'Industry Category',
			'Quote Status',
			'Quote Mode',
			'Quote Amount',
			'Corporate Tax Status',
			'VAT Status',
			'Data Source',
			'Data Batch Referral Number',
			'Database Category',
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
			'Status Category',
			'Status Remarks',
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
			'Active',
			'Trading',
			'Quote Given',
			'Email',
			'1500.00',
			'Registered',
			'Registered',
			'Google Maps',
			'REF-2026-001',
			'New Database',
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
			'Raw Database',
			'Verified direct company lead',
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
		$alloc_filter = isset($_GET['alloc_filter']) ? trim($_GET['alloc_filter']) : '';

		$heading = 'Allocate Database';
		if ($alloc_filter == 'non_allotted') {
			$heading = 'Non-Allotted Database';
		} elseif ($alloc_filter == 'allotted') {
			$heading = 'Allotted Database';
		}

		$data['info'] = array(
			'title' => $heading,
			'page_heading' => $heading
		);
		$data['alloc_filter'] = $alloc_filter;
		$data['info2'] = $this->if_not_login();

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

		$data['st_list'] = $this->comman_model->getAll(array('active' => 1), '', 'ci_status');
		$data['c_list'] = $this->comman_model->getAll('', array('id', 'ASC'), 'countries');
		$data['pc_list'] = $this->comman_model->getAll(array('cat_type' => 'p_cat', 'parent_id' => 0), '', 'ci_terms');
		$data['lc_list'] = $this->comman_model->getAll(array('cat_type' => 'lead_cat', 'parent_id' => 0), '', 'ci_terms');
		$data['users_list'] = $this->db->select('id, name, email')->get('ci_user')->result_array();

		$data['product_list'] = $this->ci_admin_model->getAllocationLeads($d1, $d2, $mrr1, $mrr2, '', '', $order_by, $sort_by, $search, $status, $category, $lCat, $user_name, $country, $state, $alloc_filter);

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
			$this->session->set_flashdata('error_message', 'No records selected for allocation.');
			redirect('manager/assigned-leads');
			return;
		}

		$target_user_id = (int)$target_user;
		$target_user_row = $this->db->get_where('ci_user', array('id' => $target_user_id))->row_array();
		$target_name = !empty($target_user_row['name']) ? $target_user_row['name'] : ($target_user_id === 0 ? 'Unassigned' : 'User #'.$target_user_id);

		$sanitized_ids = array_map('intval', $lead_ids);
		$this->db->where_in('id', $sanitized_ids);
		$update_payload = array(
			'created_by' => $target_user_id,
			'edited_by' => $this->session->userdata('manager_id'),
			'modified' => date('Y-m-d')
		);
		if ($target_user_id > 0) {
			$this->db->set('database_category', "CASE WHEN database_category = 'Non-Allotted Database' THEN 'Allotted Database' ELSE database_category END", FALSE);
		} else {
			$this->db->set('database_category', "'Non-Allotted Database'", FALSE);
		}
		$this->db->update('ci_leads', $update_payload);

		$count = count($sanitized_ids);
		$uid = $this->session->userdata('manager_id');
		$uname = $this->session->userdata('manager_name');
		$this->ci_admin_model->log_admin_action(
			'lead_allocate',
			"Allocated $count database leads to $target_name (ID: $target_user_id)",
			'SUCCESS',
			$uid,
			$uname
		);

		$this->session->set_flashdata('success_message', "Successfully allocated $count database leads to $target_name.");
		redirect('manager/assigned-leads');
	}

	public function database_transfer($page = 0)
	{
		$data = array();
		$data['info'] = array(
			'title' => 'Manager - Database Transfer',
			'page_heading' => 'Database Transfer'
		);
		$data['info2'] = $this->if_not_login();

		$manager_id = (int)$this->session->userdata('manager_id');
		$manager_role = (int)$this->session->userdata('manager_role');
		$from_user = (int)$this->input->get('from_user');

		$source_user_id = $manager_id;
		if (($manager_role === 1 || $manager_role === 4) && $from_user > 0) {
			$source_user_id = $from_user;
		}

		$d1 = $this->input->get('d1');
		$d2 = $this->input->get('d2');
		$search = $this->input->get('search');
		$status = $this->input->get('status');
		$status_str = is_array($status) ? implode(',', array_filter($status)) : (string)$status;

		$data['from_user'] = $source_user_id;
		$data['product_list'] = $this->ci_admin_model->getMyTransferLeads(
			$source_user_id,
			$d1,
			$d2,
			'',
			'',
			'',
			'',
			'',
			'',
			$search,
			$status_str,
			'',
			''
		);

		$data['st_list'] = $this->db->order_by('id', 'ASC')->get('ci_status')->result_array();
		$data['all_users'] = $this->db->select('id, name, email')->where('status', 1)->order_by('name', 'ASC')->get('ci_user')->result_array();
		$data['users_list'] = $this->db->select('id, name, email')->where('status', 1)->where('id !=', $source_user_id)->order_by('name', 'ASC')->get('ci_user')->result_array();

		$this->load->view('manager/leads/database_transfer', $data);
	}

	public function bulk_transfer()
	{
		$this->if_not_login();

		$lead_ids = $this->input->post('lead_ids');
		$target_user = $this->input->post('target_user');
		$manager_id = (int)$this->session->userdata('manager_id');
		$manager_name = $this->session->userdata('manager_name');
		$manager_role = (int)$this->session->userdata('manager_role');

		if (empty($lead_ids) || !is_array($lead_ids)) {
			$this->session->set_flashdata('error_message', 'No records selected for transfer.');
			redirect('manager/database-transfer');
			return;
		}

		$target_user_id = (int)$target_user;
		if ($target_user_id <= 0) {
			$this->session->set_flashdata('error_message', 'Please select a valid target user to transfer the leads to.');
			redirect('manager/database-transfer');
			return;
		}

		$target_user_row = $this->db->get_where('ci_user', array('id' => $target_user_id))->row_array();
		$target_name = !empty($target_user_row['name']) ? $target_user_row['name'] : 'User #' . $target_user_id;

		$sanitized_ids = array_unique(array_filter(array_map('intval', $lead_ids)));

		$this->db->where_in('id', $sanitized_ids);
		if ($manager_role != 1 && $manager_role != 4) {
			$this->db->where("(lead_assign = $manager_id OR (created_by = $manager_id AND (lead_assign = 0 OR lead_assign IS NULL)) OR (agents IS NOT NULL AND FIND_IN_SET($manager_id, agents) > 0))");
			$this->db->where("(database_category != 'KAM Onboarded' OR database_category IS NULL)");
		}
		$leads_to_transfer = $this->db->get('ci_leads')->result_array();

		$transferred_count = 0;
		$now_dt = date('Y-m-d H:i:s');

		foreach ($leads_to_transfer as $lead_row) {
			$lid = (int)$lead_row['id'];
			$orig_owner = !empty($lead_row['lead_assign']) ? (int)$lead_row['lead_assign'] : (int)$lead_row['created_by'];
			if ($orig_owner <= 0) {
				$orig_owner = $manager_id;
			}

			$existing_agents = !empty($lead_row['agents']) ? explode(',', $lead_row['agents']) : array();
			$cleaned_agents = array();
			foreach ($existing_agents as $ag) {
				$ag_int = (int)trim($ag);
				if ($ag_int > 0 && $ag_int !== $target_user_id && $ag_int !== $orig_owner) {
					$cleaned_agents[] = $ag_int;
				}
			}
			$new_agents_str = implode(',', array_unique($cleaned_agents));

			$this->db->where('id', $lid)->update('ci_leads', array(
				'created_by' => $target_user_id,
				'lead_assign' => $target_user_id,
				'agents' => $new_agents_str,
				'edited_by' => $manager_id,
				'modified' => date('Y-m-d')
			));

			$this->db->insert('ci_lead_transfers', array(
				'lead_id' => $lid,
				'from_user_id' => $orig_owner,
				'to_user_id' => $target_user_id,
				'transferred_by' => $manager_id,
				'created_at' => $now_dt
			));

			$transferred_count++;
		}

		$this->ci_admin_model->log_admin_action(
			'lead_transfer',
			"Transferred $transferred_count leads to $target_name (User ID: $target_user_id)",
			'SUCCESS',
			$manager_id,
			$manager_name
		);

		$this->session->set_flashdata('success_message', "Successfully transferred $transferred_count lead(s) to $target_name.");
		redirect('manager/database-transfer');
	}
}




