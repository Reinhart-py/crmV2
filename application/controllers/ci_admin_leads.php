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
		$is_authorized = ($cur_role === 1 || has_section_perm('back_office', 'write') || crm_can('all_data') || $cur_uid === 1 || has_section_perm('lead_review') || has_section_perm('back_office') || !empty($team));
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
		if (!empty($lead['pipeline_stage']) && $lead['pipeline_stage'] === 'kam') {
			$this->session->set_flashdata('error_message', 'This record is a KAM account, so it cannot be converted to a Lead.');
			redirect('manager/leads/view/' . base64_encode($lead_id));
			return;
		}

		$lead_owner = !empty($lead['lead_assign']) ? (int)$lead['lead_assign'] : (int)$lead['created_by'];
		$team_members = $this->ci_admin_model->fetchTeamList($cur_uid, '');
		if (!is_array($team_members)) {
			$team_members = array();
		}
		$can_convert = false;
		if ($cur_role === 1 || $cur_uid === 1 || has_section_perm('back_office', 'write') || crm_can('all_data') || has_section_perm('lead_review', 'write') || has_section_perm('back_office', 'write')) {
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
		if (!empty($lead['pipeline_stage']) && $lead['pipeline_stage'] === 'kam') {
			$this->session->set_flashdata('error_message', 'This record is a KAM account, so it cannot be converted to a Lead.');
			redirect('manager/leads/view/' . base64_encode($lead_id));
			return;
		}

		// only the owner, a collaborator, their team lead, Back Office or Admin; and not twice
		$rq_owner = !empty($lead['lead_assign']) ? (int)$lead['lead_assign'] : (int)$lead['created_by'];
		$rq_role = (int)$this->session->userdata('manager_role');
		$rq_team = $this->ci_admin_model->fetchTeamList($cur_uid, '');
		$rq_team = is_array($rq_team) ? array_map('intval', $rq_team) : array();
		$rq_collab = (!empty($lead['agents']) && in_array($cur_uid, array_map('intval', explode(',', $lead['agents']))));
		if (!($rq_role === 1 || $cur_uid === 1 || crm_can('all_data') || has_section_perm('back_office', 'write') || $cur_uid === $rq_owner || $rq_collab || in_array($rq_owner, $rq_team))) {
			$this->session->set_flashdata('error_message', 'Access denied. Only the record owner, their team lead, Back Office or Admin can request this conversion.');
			redirect('manager/leads/view/' . base64_encode($lead_id));
			return;
		}
		if (!empty($lead['conversion_status']) && $lead['conversion_status'] === 'pending') {
			$this->session->set_flashdata('message', 'Lead conversion is already requested and waiting for review.');
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
		// sent from a list row: go back to that list
		if ($this->input->post('return_to') === 'list' && ($back = urldecode((string)$this->session->userdata('manager_return_string'))) !== '') {
			redirect('manager/' . $back);
			return;
		}
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
		if ($cur_role === 1 || $cur_uid === 1 || has_section_perm('back_office', 'write') || crm_can('all_data') || has_section_perm('lead_review', 'write') || has_section_perm('back_office', 'write')) {
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
		$can_request = ($cur_role === 1 || $cur_uid === 1 || crm_can('all_data') || $cur_uid === $lead_owner || $is_collab || in_array($lead_owner, array_map('intval', $team_members)));

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
		// sent from a list row: go back to that list
		if ($this->input->post('return_to') === 'list' && ($back = urldecode((string)$this->session->userdata('manager_return_string'))) !== '') {
			redirect('manager/' . $back);
			return;
		}
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

	/**
	 * Add Database: are this phone / e-mail / new company name already in the CRM?
	 * Same matching as Import (last 9 phone digits, e-mail, company letters and digits).
	 * Returns who owns each match; a link only when the person may open that record.
	 */
	public function ajax_check_duplicate()
	{
		header('Content-Type: application/json; charset=utf-8');
		$uid = (int)$this->session->userdata('manager_id');
		if ($uid <= 0 || (!crm_can('database', 'write') && !crm_can('leads', 'write'))) { echo json_encode(array('status' => 'ok', 'matches' => array())); return; }

		$phones = array();
		foreach ((array)$this->input->get('phone') as $p) { $p = self::_imp_phone($p); if ($p !== '') $phones[$p] = true; }
		$email = strtolower(trim((string)$this->input->get('email')));
		if ($email !== '' && strpos($email, '@') === false) $email = '';
		$company = self::_imp_company($this->input->get('company'));

		$or = array();
		if ($phones) {
			$in = implode(',', array_map(array($this->db, 'escape'), array_map('strval', array_keys($phones))));
			foreach (array('contact1', 'contact2', 'alt_contact', 'mobile') as $c) $or[] = "RIGHT(REGEXP_REPLACE(IFNULL(A.$c,''), '[^0-9]', ''), 9) IN ($in)";
		}
		if ($email !== '') $or[] = 'LOWER(TRIM(A.email)) = ' . $this->db->escape($email);
		if ($company !== '') $or[] = "LOWER(REGEXP_REPLACE(IFNULL(A.customer,''), '[^A-Za-z0-9]', '')) = " . $this->db->escape($company);
		if (!$or) { echo json_encode(array('status' => 'ok', 'matches' => array())); return; }

		$rows = $this->db->query("SELECT A.id, A.lead_id, A.customer, A.contact1, A.contact2, A.alt_contact, A.mobile, A.email, A.agents, P.pipeline_stage,
				IF(A.lead_assign > 0, A.lead_assign, A.created_by) AS owner_id, U.name AS owner
			FROM ci_leads A LEFT JOIN ci_pipeline P ON P.lead_id = A.id LEFT JOIN ci_user U ON U.id = IF(A.lead_assign > 0, A.lead_assign, A.created_by)
			WHERE " . implode(' OR ', $or) . " ORDER BY A.id DESC LIMIT 6")->result_array();

		$role = (int)$this->session->userdata('manager_role');
		$see_all = ($role === 1 || $uid === 1 || crm_can('all_data') || has_section_perm('back_office') || has_section_perm('lead_review'));
		$team = array_map('intval', (array)$this->ci_admin_model->fetchTeamList($uid, ''));
		$out = array();
		foreach ($rows as $r) {
			$why = array();
			foreach (array('contact1', 'contact2', 'alt_contact', 'mobile') as $c) { $p = self::_imp_phone($r[$c]); if ($p !== '' && isset($phones[$p])) { $why['phone'] = 'phone'; } }
			if ($email !== '' && strtolower(trim((string)$r['email'])) === $email) $why['email'] = 'e-mail';
			if ($company !== '' && self::_imp_company($r['customer']) === $company) $why['company'] = 'company name';
			$owner = (int)$r['owner_id'];
			$agents = array_map('intval', explode(',', (string)$r['agents']));
			$can_open = ($see_all || $owner === $uid || in_array($owner, $team, true) || in_array($uid, $agents, true));
			$out[] = array(
				'company' => (string)$r['customer'],
				'ref' => (string)$r['lead_id'],
				'stage' => ($r['pipeline_stage'] === 'lead') ? 'Lead' : 'Database',
				'owner' => $owner === $uid ? 'you' : ((string)$r['owner'] !== '' ? $r['owner'] : 'nobody'),
				'same' => array_values($why),
				'url' => $can_open ? base_url() . 'manager/leads/view/' . base64_encode($r['id']) : '',
			);
		}
		echo json_encode(array('status' => 'ok', 'matches' => $out));
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
				$is_bo = ($cur_mgr_role === 1 || has_section_perm('back_office', 'write') || crm_can('all_data') || $cur_mgr_id === 1 || has_section_perm('back_office') || has_section_perm('lead_review'));
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

				$posted_followups = $this->_posted_followups();
				$next_fu = $this->_next_followup($posted_followups);
				$f_date = $next_fu['date'];
				$followup_time = $next_fu['time'];
				$c_date = !empty($close_date) ? db_date($close_date) : '';
				$latest_fu = $this->_latest_followup_date($posted_followups);
				if(!empty($latest_fu) && !empty($c_date) && strtotime($c_date) < strtotime($latest_fu)){
					$c_date = $latest_fu;
				}
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
				if (!empty($this->input->post('primary_owner'))) {
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
				$can_edit_bo = (has_section_perm('back_office', 'write') || $user_role === 1 || $user_id === 1 || strcasecmp($user_dept, 'Back Office') === 0);

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
				 
				 foreach ($posted_followups as $fu) {
					if ($fu['date'] === '') {
						continue;
					}
					$this->comman_model->saveData('ci_followup', array(
						'lead_id' => $lead_id,
						'lead_type' => 'lead',
						'followup_date' => $fu['date'],
						'followup_time' => $fu['time'],
						'followup_remark' => $fu['remark'],
						'followup_result' => $fu['status'],
						'user_id' => $this->session->userdata('manager_id')
					));
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

		

	private function _posted_followups()
	{
		$dates = $this->input->post('followup_date');
		$times = $this->input->post('followup_time');
		$remarks = $this->input->post('followup_remark');
		$statuses = $this->input->post('followup_status');
		$ids = $this->input->post('flead_id');
		if (!is_array($dates)) {
			$dates = ($dates === false || $dates === null) ? array() : array($dates);
			$times = array($times);
			$remarks = array($remarks);
			$statuses = array($statuses);
			$ids = array(0);
		}
		$rows = array();
		foreach (array_values($dates) as $i => $d) {
			$id = (is_array($ids) && isset($ids[$i])) ? (int)$ids[$i] : 0;
			$d = trim((string)$d);
			$rows[] = array(
				'id' => $id,
				'date' => ($id === 0 && $d !== '') ? db_date($d) : $d,
				'time' => (is_array($times) && isset($times[$i])) ? trim((string)$times[$i]) : '',
				'remark' => (is_array($remarks) && isset($remarks[$i])) ? trim((string)$remarks[$i]) : '',
				'status' => (is_array($statuses) && isset($statuses[$i])) ? trim((string)$statuses[$i]) : ''
			);
		}
		return $rows;
	}

	private function _next_followup($rows)
	{
		$today = date('Y-m-d');
		$upcoming = null;
		$latest = null;
		foreach ($rows as $fu) {
			if ($fu['id'] > 0 || $fu['date'] === '') {
				continue;
			}
			$ts = strtotime($fu['date']);
			if ($ts === false) {
				continue;
			}
			if ($fu['date'] >= $today && ($upcoming === null || $ts < strtotime($upcoming['date']))) {
				$upcoming = $fu;
			}
			if ($latest === null || $ts > strtotime($latest['date'])) {
				$latest = $fu;
			}
		}
		$pick = $upcoming !== null ? $upcoming : $latest;
		return array('date' => $pick ? $pick['date'] : '', 'time' => $pick ? $pick['time'] : '');
	}

	private function _latest_followup_date($rows)
	{
		$latest = '';
		foreach ($rows as $fu) {
			if ($fu['id'] > 0 || $fu['date'] === '' || strtotime($fu['date']) === false) {
				continue;
			}
			if ($latest === '' || strtotime($fu['date']) > strtotime($latest)) {
				$latest = $fu['date'];
			}
		}
		return $latest;
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
		$is_bo = ($cur_role === 1 || has_section_perm('back_office', 'write') || crm_can('all_data') || $cur_mgr_id === 1 || has_section_perm('back_office', 'write') || has_section_perm('lead_review', 'write'));

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
			if (!empty($this->input->post('primary_owner'))) {
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
				$can_edit_bo = (has_section_perm('back_office', 'write') || $user_role === 1 || $user_id === 1 || strcasecmp($user_dept, 'Back Office') === 0);

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
				 
				$fu_lead_id = (int)$id;
				$fu_delete = $this->input->post('followup_delete');
				if (is_array($fu_delete) && !empty($fu_delete)) {
					$fu_delete = array_values(array_filter(array_map('intval', $fu_delete)));
					if (!empty($fu_delete)) {
						$this->db->where('lead_id', $fu_lead_id)->where('lead_type', 'lead')->where_in('id', $fu_delete)->delete('ci_followup');
						$this->ci_admin_model->log_admin_action('followup_delete', 'Removed follow-up(s) #' . implode(', #', $fu_delete) . ' from lead #' . $fu_lead_id, 'SUCCESS');
					}
				}

				$new_followups = array();
				foreach ($this->_posted_followups() as $fu) {
					if ($fu['id'] > 0) {
						$this->db->where('id', $fu['id'])->where('lead_id', $fu_lead_id)->where('lead_type', 'lead')->update('ci_followup', array(
							'followup_remark' => $fu['remark'],
							'followup_result' => $fu['status']
						));
					} elseif ($fu['date'] !== '') {
						$this->comman_model->saveData('ci_followup', array(
							'lead_id' => $fu_lead_id,
							'lead_type' => 'lead',
							'followup_date' => $fu['date'],
							'followup_time' => $fu['time'],
							'followup_remark' => $fu['remark'],
							'followup_result' => $fu['status'],
							'user_id' => $this->session->userdata('manager_id')
						));
						$new_followups[] = $fu;
					}
				}
				if (!empty($new_followups)) {
					$next_fu = $this->_next_followup($new_followups);
					$lead_up = array('followup_date' => $next_fu['date'], 'followup_time' => $next_fu['time']);
					$latest_fu = $this->_latest_followup_date($new_followups);
					if (!empty($c_date) && !empty($latest_fu) && strtotime($c_date) < strtotime($latest_fu)) {
						$lead_up['close_date'] = $latest_fu;
					}
					$this->comman_model->updateData('id', $fu_lead_id, $lead_up, 'ci_leads');
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
		  $is_bo = ($cur_role === 1 || has_section_perm('back_office', 'write') || crm_can('all_data') || $cur_mgr_id === 1 || has_section_perm('back_office', 'write') || has_section_perm('lead_review', 'write'));
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
		if ($m_role != 1 && !crm_can('all_data') && $manager_id != 1) {
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
		if (!crm_can('leads_export')) {
			$this->session->set_flashdata('error_message', 'You do not have access to Export Database.');
			redirect('manager/dashboard');
			return;
		}

		
		if ($this->input->get('count') == 1) {
			$this->_export_filters();
			$n = $this->db->count_all_results('ci_leads');
			header('Content-Type: application/json');
			echo json_encode(array('count' => (int)$n));
			exit();
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

			$desc = $this->_export_filters($users_map);
			$contact_only = ($this->input->get('columns') === 'contact');
			$leads = $this->db->order_by('id', 'DESC')->get('ci_leads')->result_array();
			$file_prefix = 'database_export_' . $desc['slug'] . '_';
			$this->ci_admin_model->log_admin_action('lead_export', 'Exported ' . count($leads) . ' records' . ($contact_only ? ' (contact columns)' : '') . ': ' . ($desc['parts'] ? implode('; ', $desc['parts']) : 'all records'), 'SUCCESS');

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
			$keep = null;
			if ($contact_only) {
				$keep = array_keys(array_intersect($headers, array('Lead ID', 'Customer Name', 'POC Name', 'POC Contact Number', 'Email', 'Alternate POC Name', 'Alternate POC Contact Number', 'Status', 'Lead Category', 'Lead Assigned To', 'City', 'State', 'Country', 'Created Date')));
			}
			$pick = function ($r) use ($keep) {
				if ($keep === null) return $r;
				$o = array();
				foreach ($keep as $i) $o[] = $r[$i];
				return $o;
			};
			fputcsv($output, $pick($headers));

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
					fputcsv($output, $pick($row));
				}
			}

			fclose($output);
			exit();
		}

		
		$data = array();
		$data['info'] = array(
			'title' => 'Export Database',
			'page_heading' => 'Export Database'
		);
		$data['info2'] = $this->if_not_login();

		$data['total_count'] = $this->db->count_all_results('ci_leads');
		$this->db->where('(lead_assign = 0 OR lead_assign IS NULL)', null, false);
		$data['unassigned_count'] = $this->db->count_all_results('ci_leads');

		// Everyone who owns or added records (people who left included), active staff first.
		$data['users'] = $this->db->query("SELECT id, name, status FROM ci_user WHERE status = 1 OR id IN (SELECT DISTINCT lead_assign FROM ci_leads) OR id IN (SELECT DISTINCT created_by FROM ci_leads) ORDER BY (status = 1) DESC, name ASC")->result_array();
		$cnt = array();
		foreach ($this->db->query("SELECT lead_assign, COUNT(*) AS n FROM ci_leads GROUP BY lead_assign")->result_array() as $r) $cnt[(int)$r['lead_assign']] = (int)$r['n'];
		$data['user_counts'] = $cnt;

		$data['statuses'] = $this->db->query("SELECT S.id, S.status, COUNT(L.id) AS n FROM ci_status S LEFT JOIN ci_leads L ON L.status = S.id GROUP BY S.id ORDER BY S.id")->result_array();
		$data['temps'] = $this->db->query("SELECT id, title FROM ci_terms WHERE cat_type = 'lead_cat' AND parent_id = 0 ORDER BY title")->result_array();
		$data['services'] = $this->db->query("SELECT id, title FROM ci_terms WHERE cat_type = 'p_cat' AND parent_id = 0 ORDER BY title")->result_array();
		$data['products'] = $this->db->query("SELECT C.id, C.title, P.title AS parent FROM ci_terms C LEFT JOIN ci_terms P ON P.id = C.parent_id WHERE C.cat_type = 'p_cat' AND C.parent_id <> 0 ORDER BY P.title, C.title")->result_array();
		$vals = function ($col) {
			$out = array();
			foreach ($this->db->query("SELECT $col AS v, COUNT(*) AS n FROM ci_leads WHERE $col IS NOT NULL AND $col <> '' GROUP BY $col ORDER BY $col")->result_array() as $r) $out[] = $r;
			return $out;
		};
		$data['db_cats'] = $vals('database_category');
		$data['sources'] = $vals('data_source');
		$data['biz_cats'] = $vals('business_category');
		$data['countries'] = $this->db->query("SELECT C.id, C.name, COUNT(*) AS n FROM ci_leads L JOIN countries C ON C.id = L.country GROUP BY C.id ORDER BY C.name")->result_array();
		$this->load->view('manager/leads/export', $data);
	}

	



	/**
	 * Applies the Export Database filters (from the query string) to the next ci_leads query.
	 * Returns array('parts' => readable list for the admin log, 'slug' => file name part).
	 */
	private function _export_filters($users_map = array())
	{
		$arr = function ($k) {
			$v = $this->input->get($k);
			if ($v === null || $v === false || $v === '') return array();
			if (!is_array($v)) $v = array($v);
			$out = array();
			foreach ($v as $x) { if (is_string($x) && trim($x) !== '') $out[] = trim($x); }
			return array_values(array_unique($out));
		};
		$ints = function ($list) { return array_values(array_filter(array_map('intval', $list), function ($x) { return $x > 0; })); };
		$date = function ($k) {
			$v = trim((string)$this->input->get($k));
			if ($v === '') return '';
			$d = DateTime::createFromFormat('d-m-Y', $v);
			if (!$d) $d = DateTime::createFromFormat('Y-m-d', $v);
			return $d ? $d->format('Y-m-d') : '';
		};
		$parts = array();
		$slug = array();

		// Look-ups first: any query run below would use up the conditions already queued.
		$svc = $ints($arr('service'));
		$st = $ints($arr('status'));
		$tmp = $ints($arr('temp'));
		$prd = $ints($arr('product'));
		$cty = $ints($arr('country'));
		$term_ids = array_merge($svc, $tmp, $prd);
		$term = array();
		if ($term_ids) foreach ($this->db->select('id, title')->where_in('id', $term_ids)->get('ci_terms')->result_array() as $t) $term[(int)$t['id']] = $t['title'];
		$st_name = array();
		if ($st) foreach ($this->db->select('id, status')->where_in('id', $st)->get('ci_status')->result_array() as $t) $st_name[(int)$t['id']] = $t['status'];
		$cty_name = array();
		if ($cty) foreach ($this->db->select('id, name')->where_in('id', $cty)->get('countries')->result_array() as $t) $cty_name[(int)$t['id']] = $t['name'];
		$names = function ($ids, $map) { $o = array(); foreach ($ids as $i) $o[] = isset($map[$i]) ? $map[$i] : '#' . $i; return implode(', ', $o); };
		$svc_keys = array_map('strval', $svc);
		foreach ($svc as $i) if (isset($term[$i])) $svc_keys[] = $term[$i];

		$q = trim((string)$this->input->get('q'));
		if ($q !== '') {
			$this->db->group_start();
			foreach (array('customer', 'poc', 'contact1', 'contact2', 'email', 'lead_id') as $i => $col) {
				if ($i === 0) $this->db->like($col, $q); else $this->db->or_like($col, $q);
			}
			$this->db->group_end();
			$parts[] = 'search "' . $q . '"';
		}

		$d1 = $date('d1');
		$d2 = $date('d2');
		if ($d1 !== '') { $this->db->where('DATE(created) >= ' . $this->db->escape($d1), null, false); }
		if ($d2 !== '') { $this->db->where('DATE(created) <= ' . $this->db->escape($d2), null, false); }
		if ($d1 !== '' || $d2 !== '') $parts[] = 'added ' . ($d1 ?: 'start') . ' to ' . ($d2 ?: 'today');

		$assign = $arr('assign');
		$none = in_array('none', $assign, true);
		$aids = $ints($assign);
		if ($none || $aids) {
			$c = array();
			if ($aids) $c[] = 'lead_assign IN (' . implode(',', $aids) . ')';
			if ($none) $c[] = 'lead_assign = 0 OR lead_assign IS NULL';
			$this->db->where('(' . implode(' OR ', $c) . ')', null, false);
			$names = array();
			foreach ($aids as $id) $names[] = isset($users_map[$id]) ? $users_map[$id] : '#' . $id;
			if ($none) $names[] = 'not assigned';
			$parts[] = 'assigned to ' . implode(', ', $names);
			if (count($names) === 1) $slug[] = $none && !$aids ? 'unassigned' : $names[0];
		}

		$by = $ints($arr('added_by'));
		if ($by) { $this->db->where_in('created_by', $by); $parts[] = 'added by ' . $names($by, $users_map); }

		if ($st) { $this->db->where_in('status', $st); $parts[] = 'status ' . $names($st, $st_name); $slug[] = count($st) === 1 ? $names($st, $st_name) : 'status'; }

		if ($tmp) { $this->db->where_in('lead_cat', array_map('strval', $tmp)); $parts[] = 'temperature ' . $names($tmp, $term); }

		if ($svc) {
			// stored as the term id, or (older records) as its name
			$this->db->where_in('s_cat', $svc_keys);
			$parts[] = 'service ' . $names($svc, $term);
			$slug[] = count($svc) === 1 ? $names($svc, $term) : 'service';
		}
		if ($prd) { $this->db->where_in('p_cat', array_map('strval', $prd)); $parts[] = 'product ' . $names($prd, $term); }

		foreach (array('db_cat' => 'database_category', 'source' => 'data_source') as $k => $col) {
			$v = $arr($k);
			if ($v) { $this->db->where_in($col, $v); $parts[] = str_replace('_', ' ', $col) . ' ' . implode(', ', $v); }
		}
		$biz = $arr('biz');
		if ($biz) {
			$this->db->group_start();
			$this->db->where_in('business_category', $biz);
			$this->db->or_where_in('industry_category', $biz);
			$this->db->group_end();
			$parts[] = 'business category ' . implode(', ', $biz);
		}

		if ($cty) { $this->db->where_in('country', $cty); $parts[] = 'country ' . $names($cty, $cty_name); }

		$m1 = trim((string)$this->input->get('mrr1'));
		$m2 = trim((string)$this->input->get('mrr2'));
		if ($m1 !== '' && is_numeric($m1)) $this->db->where('CAST(mrr AS DECIMAL(15,2)) >=', (float)$m1, false);
		if ($m2 !== '' && is_numeric($m2)) $this->db->where('CAST(mrr AS DECIMAL(15,2)) <=', (float)$m2, false);
		if (($m1 !== '' && is_numeric($m1)) || ($m2 !== '' && is_numeric($m2))) $parts[] = 'MRR ' . ($m1 ?: '0') . ' to ' . ($m2 ?: 'any');

		$s = $slug ? implode('_', $slug) : ($parts ? 'filtered' : 'all');
		$s = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $s), '_'));
		return array('parts' => $parts, 'slug' => $s !== '' ? substr($s, 0, 40) : 'all');
	}

	public function import()
	{
		$this->if_not_login();
		$role = $this->session->userdata('manager_role');
		if (!crm_can('leads_import')) {
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
		$data['is_support'] = crm_can('all_data');
		$data['can_import'] = true; // checked above (Import Database right)

		// Recent imports: your own, or everyone's for people who see all data
		$this->_import_tables();
		$me = (int)$this->session->userdata('manager_id');
		$this->db->select('b.*, u.name AS by_name, u2.name AS undone_name')
			->from('ci_import_batches b')
			->join('ci_user u', 'u.id = b.created_by', 'left')
			->join('ci_user u2', 'u2.id = b.undone_by', 'left')
			->order_by('b.id', 'DESC')->limit(25);
		if (!crm_can('all_data')) $this->db->where('b.created_by', $me);
		$batches = $this->db->get()->result_array();
		foreach ($batches as &$b) $b['can_undo'] = empty($b['undone_at']) && (int)$b['imported'] > 0 && $this->_import_can_undo($b);
		unset($b);
		$data['batches'] = $batches;

		$this->load->view('manager/leads/import', $data);
	}

	/** Step 1: read the file, check every row and show what would happen. Nothing is saved yet. */
	public function import_action()
	{
		$this->if_not_login();
		if (!crm_can('leads_import')) {
			$this->session->set_flashdata('error_message', 'You do not have access to Import Database.');
			redirect('manager/leads/import');
			return;
		}

		if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
			$this->session->set_flashdata('error_message', 'Please select a valid CSV file to upload.');
			redirect('manager/leads/import');
			return;
		}

		$file_name = basename((string)$_FILES['csv_file']['name']);
		if (strtolower(pathinfo($file_name, PATHINFO_EXTENSION)) !== 'csv') {
			$this->session->set_flashdata('error_message', 'Invalid file type. Please upload a .csv file.');
			redirect('manager/leads/import');
			return;
		}

		// Keep the file on the server until the import is confirmed (old ones are cleaned up after a day)
		$dir = $this->_import_dir();
		foreach ((array)glob($dir . '*.csv') as $old) {
			if (is_file($old) && filemtime($old) < time() - 86400) @unlink($old);
		}
		$token = bin2hex(random_bytes(16));
		if (!@move_uploaded_file($_FILES['csv_file']['tmp_name'], $dir . $token . '.csv')) {
			$this->session->set_flashdata('error_message', 'Could not open the uploaded file.');
			redirect('manager/leads/import');
			return;
		}

		$parsed = $this->_import_parse($dir . $token . '.csv');
		if (!empty($parsed['fatal'])) {
			@unlink($dir . $token . '.csv');
			$this->session->set_flashdata('error_message', $parsed['fatal']);
			redirect('manager/leads/import');
			return;
		}

		$prev = $this->session->userdata('import_pending');
		if (is_array($prev) && !empty($prev['token']) && $prev['token'] !== $token) @unlink($dir . $prev['token'] . '.csv');
		$this->session->set_userdata('import_pending', array('token' => $token, 'name' => $file_name, 'at' => time()));

		$data = array();
		$data['info'] = array('title' => 'Manager - Leads', 'page_heading' => 'Import preview');
		$data['info2'] = $this->if_not_login();
		$data['token'] = $token;
		$data['file_name'] = $file_name;
		$data['rows'] = $parsed['rows'];
		$data['counts'] = $parsed['counts'];
		$this->load->view('manager/leads/import_preview', $data);
	}

	/** Step 2: import the checked file as one batch that can be undone later. */
	public function import_confirm()
	{
		$this->if_not_login();
		if (!crm_can('leads_import')) {
			$this->session->set_flashdata('error_message', 'You do not have access to Import Database.');
			redirect('manager/leads/import');
			return;
		}
		$token = (string)$this->input->post('token');
		$pending = $this->session->userdata('import_pending');
		$path = $this->_import_dir() . $token . '.csv';
		if (!preg_match('/^[a-f0-9]{32}$/', $token) || !is_array($pending) || $pending['token'] !== $token || !is_file($path)) {
			$this->session->set_flashdata('error_message', 'This import has expired or was already done. Please upload the file again.');
			redirect('manager/leads/import');
			return;
		}
		$with_dups = $this->input->post('dups') === 'import';

		$parsed = $this->_import_parse($path);
		if (!empty($parsed['fatal'])) {
			$this->session->set_flashdata('error_message', $parsed['fatal']);
			redirect('manager/leads/import');
			return;
		}

		$this->_import_tables();
		$me = (int)$this->session->userdata('manager_id');
		$this->db->insert('ci_import_batches', array(
			'file_name' => substr($pending['name'], 0, 255),
			'created_by' => $me,
			'created_at' => date('Y-m-d H:i:s'),
			'total_rows' => $parsed['counts']['total'],
			'errors' => $parsed['counts']['err'],
		));
		$batch_id = (int)$this->db->insert_id();

		$imported = 0;
		$skipped = 0;
		foreach ($parsed['rows'] as $r) {
			if ($r['state'] === 'err') continue;
			if ($r['state'] === 'dup' && !$with_dups) { $skipped++; continue; }
			$new_id = $this->comman_model->saveData('ci_leads', $r['data']);
			if ($new_id) {
				$this->comman_model->updateData('id', $new_id, array('lead_id' => 100000000 + $new_id), 'ci_leads');
				$this->db->insert('ci_import_batch_leads', array('batch_id' => $batch_id, 'lead_id' => (int)$new_id));
				$imported++;
			}
		}
		$this->db->where('id', $batch_id)->update('ci_import_batches', array('imported' => $imported, 'skipped_dups' => $skipped));

		@unlink($path);
		$this->session->unset_userdata('import_pending');

		$this->ci_admin_model->log_admin_action('lead_import', 'Imported ' . $imported . ' records from ' . $pending['name'] . ' (batch #' . $batch_id . '; ' . $skipped . ' duplicates skipped, ' . $parsed['counts']['err'] . ' rows with problems)', 'SUCCESS');

		if ($imported > 0) {
			$this->session->set_flashdata('message', 'Imported ' . self::_imp_n($imported, 'record') . ' from ' . html_escape($pending['name']) . '.'
				. ($skipped ? ' ' . self::_imp_n($skipped, 'duplicate') . ($skipped === 1 ? ' was' : ' were') . ' skipped.' : '')
				. ' If something is wrong you can undo this import below.');
		} else {
			$this->session->set_flashdata('error_message', 'Nothing was imported: every row was a duplicate or had a problem.');
		}
		redirect('manager/leads/import');
	}

	/** Undo an import: removes the records of that batch nobody has worked on yet. */
	public function import_undo()
	{
		$this->if_not_login();
		if (!crm_can('leads_import')) {
			$this->session->set_flashdata('error_message', 'You do not have access to Import Database.');
			redirect('manager/leads/import');
			return;
		}
		$this->_import_tables();
		$id = (int)$this->input->post('batch_id');
		$b = $id ? $this->db->get_where('ci_import_batches', array('id' => $id))->row_array() : array();
		if (empty($b) || !empty($b['undone_at']) || !$this->_import_can_undo($b)) {
			$this->session->set_flashdata('error_message', 'This import cannot be undone (it was already undone, or it is not yours).');
			redirect('manager/leads/import');
			return;
		}

		$ids = array();
		foreach ($this->db->select('lead_id')->get_where('ci_import_batch_leads', array('batch_id' => $id))->result_array() as $r) $ids[] = (int)$r['lead_id'];

		// A record counts as worked on when it was edited, moved, or has follow-ups, calls, meetings, orders or pipeline entries
		$keep = array();
		if ($ids) {
			foreach (array_chunk($ids, 500) as $chunk) {
				$this->db->select('id')->where_in('id', $chunk)
					->where("((modified IS NOT NULL AND modified <> '') OR (edited_by IS NOT NULL AND edited_by <> 0))", null, false);
				foreach ($this->db->get('ci_leads')->result_array() as $r) $keep[(int)$r['id']] = true;
				foreach (array('ci_boleads', 'ci_followup', 'ci_meetings', 'ci_call_logs', 'ci_orders', 'ci_pipeline', 'ci_pipeline_logs', 'ci_lead_transfers', 'ci_kam_client_details', 'ci_service_delivery') as $t) {
					if (!$this->db->table_exists($t)) continue;
					foreach ($this->db->select('DISTINCT lead_id', false)->where_in('lead_id', $chunk)->get($t)->result_array() as $r) $keep[(int)$r['lead_id']] = true;
				}
			}
		}
		$remove = array();
		foreach ($ids as $lid) if (!isset($keep[$lid])) $remove[] = $lid;
		$removed = 0;
		foreach (array_chunk($remove, 500) as $chunk) {
			$this->db->where_in('id', $chunk)->delete('ci_leads');
			$removed += $this->db->affected_rows();
		}
		$kept = count($ids) - count($remove);

		$this->db->where('id', $id)->update('ci_import_batches', array(
			'undone_at' => date('Y-m-d H:i:s'),
			'undone_by' => (int)$this->session->userdata('manager_id'),
			'undo_removed' => $removed,
			'undo_kept' => $kept,
		));
		$this->ci_admin_model->log_admin_action('lead_delete', 'Undid import batch #' . $id . ' (' . $b['file_name'] . '): ' . $removed . ' records removed, ' . $kept . ' kept because they were already worked on', 'SUCCESS');

		$msg = 'Import undone: ' . self::_imp_n($removed, 'record') . ' removed.';
		if ($kept) $msg .= ' ' . self::_imp_n($kept, 'record') . ($kept === 1 ? ' was' : ' were') . ' kept because someone has already worked on ' . ($kept === 1 ? 'it.' : 'them.');
		$this->session->set_flashdata('message', $msg);
		redirect('manager/leads/import');
	}

	/** Undo is for the person who imported, or for people who see all data. */
	private function _import_can_undo($batch)
	{
		return (int)$batch['created_by'] === (int)$this->session->userdata('manager_id') || crm_can('all_data');
	}

	private function _import_dir()
	{
		$dir = APPPATH . 'cache/imports/';
		if (!is_dir($dir)) {
			@mkdir($dir, 0700, true);
			@file_put_contents($dir . 'index.html', '<html><head><title>403 Forbidden</title></head><body><p>Directory access is forbidden.</p></body></html>');
		}
		return $dir;
	}

	private function _import_tables()
	{
		static $done = false;
		if ($done) return;
		$done = true;
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_import_batches (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			file_name VARCHAR(255) NOT NULL DEFAULT '',
			created_by INT NOT NULL DEFAULT 0,
			created_at DATETIME NOT NULL,
			total_rows INT NOT NULL DEFAULT 0,
			imported INT NOT NULL DEFAULT 0,
			skipped_dups INT NOT NULL DEFAULT 0,
			errors INT NOT NULL DEFAULT 0,
			undone_at DATETIME NULL,
			undone_by INT NULL,
			undo_removed INT NOT NULL DEFAULT 0,
			undo_kept INT NOT NULL DEFAULT 0,
			KEY created_by (created_by)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_import_batch_leads (
			batch_id INT UNSIGNED NOT NULL,
			lead_id INT NOT NULL,
			PRIMARY KEY (batch_id, lead_id),
			KEY lead_id (lead_id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
	}

	private static function _imp_n($n, $word)
	{
		return number_format($n) . ' ' . $word . ($n === 1 ? '' : 's');
	}

	private static function _imp_phone($v)
	{
		$d = preg_replace('/\D/', '', (string)$v);
		return strlen($d) >= 7 ? substr($d, -9) : '';
	}

	private static function _imp_company($v)
	{
		$c = preg_replace('/[^a-z0-9]/', '', strtolower((string)$v));
		return strlen($c) >= 3 ? $c : '';
	}

	/**
	 * Reads the CSV and returns every row with its record data and a state:
	 * 'ok' (ready), 'dup' (same company, phone or email as an existing record or an earlier row) or 'err' (cannot be imported).
	 */
	private function _import_parse($path)
	{
		$handle = @fopen($path, 'r');
		if (!$handle) return array('fatal' => 'Could not open the uploaded file.');

		$raw_headers = fgetcsv($handle, 5000, ',');
		if (!$raw_headers) {
			fclose($handle);
			return array('fatal' => 'The uploaded CSV file is empty.');
		}

		$col_index = array();
		foreach ($raw_headers as $idx => $hdr) {
			$clean = strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', (string)$hdr)));
			$clean = preg_replace('/[^a-z0-9]/', '', $clean);
			$col_index[$clean] = $idx;
		}
		$has_customer = false;
		foreach (array('customername', 'customer', 'clientname', 'client') as $a) if (isset($col_index[$a])) $has_customer = true;
		if (!$has_customer) {
			fclose($handle);
			return array('fatal' => 'The file has no "Customer Name" column. Please use the template (Download template) and keep its first row.');
		}

		$get_val = function ($row, $aliases) use ($col_index) {
			foreach ($aliases as $alias) {
				if (isset($col_index[$alias]) && isset($row[$col_index[$alias]])) {
					return trim($row[$col_index[$alias]]);
				}
			}
			return '';
		};

		$rows = array();
		$row_num = 1;
		while (($row = fgetcsv($handle, 5000, ',')) !== FALSE) {
			$row_num++;
			if (empty(array_filter($row, 'strlen'))) continue;
			$r = $this->_import_row($row, $get_val);
			$r['line'] = $row_num;
			$rows[] = $r;
		}
		fclose($handle);

		// Duplicates: phone (last 9 digits), company name (letters and digits only) or email
		$keys = array('phone' => array(), 'company' => array(), 'email' => array());
		foreach ($rows as &$r) {
			$r['keys'] = array('phone' => array(), 'company' => array(), 'email' => array());
			if ($r['state'] === 'err') continue;
			$d = $r['data'];
			foreach (array('contact1', 'contact2', 'alt_contact') as $f) {
				$p = self::_imp_phone(isset($d[$f]) ? $d[$f] : '');
				if ($p !== '') $r['keys']['phone'][$p] = true;
			}
			$c = self::_imp_company($d['customer']);
			if ($c !== '') $r['keys']['company'][$c] = true;
			$e = strtolower(trim((string)$d['email']));
			if ($e !== '' && strpos($e, '@') !== false) $r['keys']['email'][$e] = true;
			foreach ($r['keys'] as $k => $set) foreach ($set as $v => $_) $keys[$k][$v] = true;
		}
		unset($r);

		$found = array('phone' => array(), 'company' => array(), 'email' => array());
		$sql_expr = array(
			'phone' => array("RIGHT(REGEXP_REPLACE(contact1, '[^0-9]', ''), 9)", "RIGHT(REGEXP_REPLACE(contact2, '[^0-9]', ''), 9)", "RIGHT(REGEXP_REPLACE(alt_contact, '[^0-9]', ''), 9)", "RIGHT(REGEXP_REPLACE(mobile, '[^0-9]', ''), 9)"),
			'company' => array("LOWER(REGEXP_REPLACE(customer, '[^A-Za-z0-9]', ''))"),
			'email' => array('LOWER(TRIM(email))'),
		);
		$owners = array();
		foreach ($sql_expr as $kind => $exprs) {
			foreach (array_chunk(array_keys($keys[$kind]), 400) as $chunk) {
				$in = implode(',', array_map(array($this->db, 'escape'), array_map('strval', $chunk)));
				$sel = array();
				foreach ($exprs as $i => $ex) $sel[] = $ex . ' AS k' . $i;
				$where = array();
				foreach ($exprs as $ex) $where[] = $ex . ' IN (' . $in . ')';
				$res = $this->db->query('SELECT id, lead_id, customer, lead_assign, ' . implode(', ', $sel) . ' FROM ci_leads WHERE ' . implode(' OR ', $where) . ' ORDER BY id')->result_array();
				foreach ($res as $l) {
					foreach ($exprs as $i => $ex) {
						$v = (string)$l['k' . $i];
						if ($v !== '' && isset($keys[$kind][$v]) && !isset($found[$kind][$v])) $found[$kind][$v] = $l;
					}
					$owners[(int)$l['lead_assign']] = true;
				}
			}
		}
		$owner_name = array();
		unset($owners[0]);
		if ($owners) foreach ($this->db->select('id, name')->where_in('id', array_keys($owners))->get('ci_user')->result_array() as $u) $owner_name[(int)$u['id']] = $u['name'];

		$labels = array('phone' => 'phone', 'company' => 'company name', 'email' => 'email');
		$seen = array('phone' => array(), 'company' => array(), 'email' => array());
		$counts = array('total' => count($rows), 'ok' => 0, 'dup' => 0, 'err' => 0);
		foreach ($rows as &$r) {
			if ($r['state'] !== 'err') {
				$why = array();
				foreach ($r['keys'] as $kind => $set) {
					foreach ($set as $v => $_) {
						if (isset($found[$kind][$v])) {
							$l = $found[$kind][$v];
							$a = (int)$l['lead_assign'];
							$why[] = 'Same ' . $labels[$kind] . ' as ' . $l['lead_id'] . ' ' . $l['customer'] . ' (' . ($a && isset($owner_name[$a]) ? 'assigned to ' . $owner_name[$a] : 'not assigned') . ')';
							break;
						}
						if (isset($seen[$kind][$v])) {
							$why[] = 'Same ' . $labels[$kind] . ' as row ' . $seen[$kind][$v] . ' of this file';
							break;
						}
					}
				}
				foreach ($r['keys'] as $kind => $set) foreach ($set as $v => $_) if (!isset($seen[$kind][$v])) $seen[$kind][$v] = $r['line'];
				if ($why) {
					$r['state'] = 'dup';
					$r['reason'] = implode('; ', array_unique($why));
				}
			}
			unset($r['keys']);
			$counts[$r['state']]++;
		}
		unset($r);

		return array('rows' => $rows, 'counts' => $counts);
	}

	/** One CSV row to a ci_leads record (same mapping as before), or an error. */
	private function _import_row($row, $get_val)
	{
		static $user_cache = array(), $status_cache = array();
		$manager_id = $this->session->userdata('manager_id');

		foreach ($row as $k => $val) {
			$val = trim($val);
			if (strlen($val) > 0 && in_array($val[0], array('=', '+', '-', '@'))) {
				$row[$k] = "'" . $val;
			}
		}

		$customer = $get_val($row, array('customername', 'customer', 'clientname', 'client'));
		$out = array('state' => 'ok', 'reason' => '', 'customer' => $customer,
			'poc' => $get_val($row, array('pocname', 'poc', 'contactperson')),
			'phone' => $get_val($row, array('poccontactnumber', 'contact1', 'contactnumber', 'mobile', 'phone')),
			'email' => $get_val($row, array('email', 'emailid')),
			'assignee' => '');

		$username_val = $get_val($row, array('username', 'user', 'assigneduser', 'agent'));
		$assigned_user_id = 0;
		if (!empty($username_val)) {
			$username_clean = trim(ltrim($username_val, "'=@+-"));
			$ck = strtolower($username_clean);
			if (!array_key_exists($ck, $user_cache)) {
				$this->db->where("(email = '" . $this->db->escape_str($username_clean) . "' OR c_username = '" . $this->db->escape_str($username_clean) . "' OR name = '" . $this->db->escape_str($username_clean) . "' OR emp_id = '" . $this->db->escape_str($username_clean) . "')", null, false);
				$u_match = $this->db->get('ci_user')->row_array();
				$user_cache[$ck] = !empty($u_match['id']) ? array((int)$u_match['id'], $u_match['name']) : null;
			}
			if ($user_cache[$ck] === null) {
				$out['state'] = 'err';
				$out['reason'] = 'Username "' . $username_val . '" does not match any staff member';
				return $out;
			}
			$assigned_user_id = $user_cache[$ck][0];
			$out['assignee'] = $user_cache[$ck][1];
		}

		if (empty($customer)) {
			$out['state'] = 'err';
			$out['reason'] = 'Customer Name is empty';
			return $out;
		}

		$poc = $out['poc'];
		$contact1 = $out['phone'];
		$alt_contact = $get_val($row, array('alternativecontactnumber', 'altcontact', 'alternatecontact', 'alternativemobilenumber', 'altmobile', 'alternatemobile'));
		$alt_mobile = $alt_contact;
		$email = $out['email'];
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
			$sk = strtolower($status_raw);
			if (!array_key_exists($sk, $status_cache)) {
				$st_row = $this->db->like('status', $status_raw)->get('ci_status')->row_array();
				$status_cache[$sk] = !empty($st_row) ? $st_row['id'] : null;
			}
			if ($status_cache[$sk] !== null) {
				$status = $status_cache[$sk];
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

		$out['data'] = array(
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
			'c_partner' => !empty($c_partner) ? $c_partner : crm_brand_company(),
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
		return $out;
	}

	public function download_sample_csv()
	{
		$this->if_not_login();
		$role = $this->session->userdata('manager_role');
		if (!crm_can('leads_import')) {
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
			'agent@example.com',
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
		if (!crm_can('assigned_leads')) {
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

		$cnt = $this->db->query("SELECT COUNT(*) AS total,
			SUM(lead_assign > 0 AND (database_category != 'Non-Allotted Database' OR database_category IS NULL)) AS allotted,
			SUM(lead_assign = 0 OR lead_assign IS NULL OR database_category = 'Non-Allotted Database') AS non_allotted
			FROM ci_leads")->row_array();
		$data['alloc_counts'] = array('' => (int)$cnt['total'], 'allotted' => (int)$cnt['allotted'], 'non_allotted' => (int)$cnt['non_allotted']);
		$data['can_allocate'] = crm_can('assigned_leads', 'write');
		$this->db->select('id, name, email')->where('admin_status', 1)->order_by('name', 'ASC');
		if ($this->db->table_exists('ci_user_exit')) $this->db->where('id NOT IN (SELECT user_id FROM ci_user_exit)', NULL, FALSE);
		$data['users_list'] = $this->db->get('ci_user')->result_array(); // only people still working here
		$data['product_list'] = $this->ci_admin_model->getAllocationLeads($d1, $d2, $mrr1, $mrr2, '', '', $order_by, $sort_by, $search, $status, $category, $lCat, $user_name, $country, $state, $alloc_filter);

		$this->load->view('manager/leads/assigned_leads', $data);
	}

	public function bulk_move_assign()
	{
		$this->if_not_login();
		$role = $this->session->userdata('manager_role');
		if (!crm_can('assigned_leads', 'write')) {
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
		if (crm_can('all_data') && $from_user > 0) {
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

		// Copy (admins only): the selected records stay where they are; each chosen person gets a copy of their own
		if ($this->input->post('mode') === 'copy') {
			$this->_bulk_copy($lead_ids, $this->input->post('target_users'));
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
		if (!crm_can('all_data')) {
			// Only the owner moves a record (the same records the page lists); co-agents cannot
			$this->db->where("(lead_assign = $manager_id OR (created_by = $manager_id AND (lead_assign = 0 OR lead_assign IS NULL)))");
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

	/**
	 * Copy selected records to one or more people (Admin only). The originals are not changed.
	 * Each copy is a new record owned by that person, with the same details. A person who already
	 * owns the record, or already got a copy of it, is skipped. Every copy is kept in ci_lead_copies.
	 */
	private function _bulk_copy($lead_ids, $target_users)
	{
		$back = 'manager/database-transfer' . ((int)$this->input->post('from_user') > 0 ? '?from_user=' . (int)$this->input->post('from_user') : '');
		if (!crm_is_full_admin()) {
			$this->session->set_flashdata('error_message', 'Only an admin can copy records.');
			redirect($back);
			return;
		}
		$manager_id = (int)$this->session->userdata('manager_id');
		$manager_name = $this->session->userdata('manager_name');

		$ids = array_values(array_unique(array_filter(array_map('intval', (array)$lead_ids))));
		$tids = array_values(array_unique(array_filter(array_map('intval', is_array($target_users) ? $target_users : array()))));
		if (!$ids) {
			$this->session->set_flashdata('error_message', 'No records selected to copy.');
			redirect($back);
			return;
		}
		if (!$tids) {
			$this->session->set_flashdata('error_message', 'Please choose at least one person to copy the records to.');
			redirect($back);
			return;
		}
		if (count($ids) > 5000 || count($tids) > 50) {
			$this->session->set_flashdata('error_message', 'Too many at once: copy up to 5,000 records to up to 50 people at a time.');
			redirect($back);
			return;
		}
		// Only active users can receive copies
		$targets = $this->db->select('id, name')->where_in('id', $tids)->where('status', 1)->get('ci_user')->result_array();
		if (!$targets) {
			$this->session->set_flashdata('error_message', 'Please choose at least one active user.');
			redirect($back);
			return;
		}

		if (!$this->db->table_exists('ci_lead_copies')) {
			$this->db->query("CREATE TABLE IF NOT EXISTS ci_lead_copies (
				id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				lead_id INT NOT NULL,
				new_lead_id INT NOT NULL,
				from_user_id INT NOT NULL DEFAULT 0,
				to_user_id INT NOT NULL,
				copied_by INT NOT NULL,
				created_at DATETIME NOT NULL,
				KEY idx_lead_to (lead_id, to_user_id),
				KEY idx_new (new_lead_id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		}

		$leads = $this->db->where_in('id', $ids)->get('ci_leads')->result_array();
		$now_dt = date('Y-m-d H:i:s');
		$made = 0; $skipped = 0; $names = array();

		$this->db->trans_start();
		foreach ($targets as $t) {
			$tid = (int)$t['id'];
			$given = 0;
			// Records this person already has a copy of (copies of copies count too)
			$have = array();
			foreach ($this->db->select('lead_id')->where('to_user_id', $tid)->where_in('lead_id', $ids)->get('ci_lead_copies')->result_array() as $h) $have[(int)$h['lead_id']] = true;
			foreach ($leads as $l) {
				$lid = (int)$l['id'];
				$owner = !empty($l['lead_assign']) ? (int)$l['lead_assign'] : (int)$l['created_by'];
				if ($owner === $tid || isset($have[$lid])) { $skipped++; continue; }
				$row = $l;
				unset($row['id']);
				$row['lead_id'] = 100000000;
				$row['created_by'] = $tid;
				$row['lead_assign'] = $tid;
				$row['agents'] = '';
				$row['edited_by'] = $manager_id;
				$row['modified'] = date('Y-m-d');
				$this->db->insert('ci_leads', $row);
				$new_id = (int)$this->db->insert_id();
				if ($new_id <= 0) { $skipped++; continue; }
				$this->db->where('id', $new_id)->update('ci_leads', array('lead_id' => 100000000 + $new_id));
				$this->db->insert('ci_lead_copies', array(
					'lead_id' => $lid, 'new_lead_id' => $new_id, 'from_user_id' => $owner,
					'to_user_id' => $tid, 'copied_by' => $manager_id, 'created_at' => $now_dt,
				));
				$made++; $given++;
			}
			if ($given) $names[] = $t['name'] . ' (' . $given . ')';
		}
		$this->db->trans_complete();

		if (!$this->db->trans_status()) {
			$this->session->set_flashdata('error_message', 'The copy could not be finished. Nothing was copied. Please try again.');
			redirect($back);
			return;
		}

		$this->ci_admin_model->log_admin_action(
			'lead_copy',
			"Copied " . count($leads) . " record(s) to " . count($targets) . " user(s): $made copies made" . ($names ? ' - ' . implode(', ', $names) : '') . ($skipped ? ", $skipped skipped" : ''),
			'SUCCESS',
			$manager_id,
			$manager_name
		);

		$msg = $made ? "Made $made cop" . ($made === 1 ? 'y' : 'ies') . ': ' . implode(', ', $names) . '.' : 'No copies were made.';
		if ($skipped) $msg .= " $skipped skipped because that person already owns the record or already has a copy of it.";
		$this->session->set_flashdata($made ? 'success_message' : 'error_message', $msg);
		redirect($back);
	}
}




