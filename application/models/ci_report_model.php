<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ci_report_model extends CI_model {

	 function __construct() {
    parent::__construct();
	$this->load->database();
	
	}
	 public function getAll($con,$order=array(),$table='')
	 {
		 $result= array();
		if(!empty($con))
		{
			$this->db->where($con);
		}
		if(!empty($order))
		{
			$this->db->order_by($order[0],$order[1]);
		}
		$data = $this->db->get($table);
		foreach($data->result_array() as $row)
		{
			$result[]= $row;
		}
		
		return $result;
		
	 }
	 
	 public function getpostdata($con,$order=array(),$table='')
	 {
		// print_r($con);
		 $result= array();
		if(!empty($con))
		{
			$this->db->where($con);
		}
		if(!empty($order))
		{
			$this->db->order_by($order[0],$order[1]);
		}
		$data = $this->db->get($table);
		foreach($data->result_array() as $row)
		{
			$result[]= $row;
		}
		
		return $result[0];
		
	 }
	
				public function srch_data($city='',$stage='',$status='',$d1='',$d2='',$user_id='',$channel_id='',$company_id='')
				{
				      $data = array();
                      $cond = array();
					  $con='';
					
				  if(isset($city) and $city!='')
				   {
                          $cond['city'] = "F.city = '$city'  ";
                   }
				   
				   if(isset($stage) and $stage!='')
				   {
                          $cond['stage'] = "A.stage = '$stage' ";
                   }
				   
				   if(isset($status) and $status!='')
				   {
                          $cond['status'] = "F.status = '$status' ";
                   }
				   
				   if((isset($d1) and $d1!='') AND (isset($d2) and $d2!='') )
				   {
					   $cond['created'] = "A.created between '$d1' AND '$d2' ";
			       }
				
			      if(isset($user_id) and $user_id!='')
				   {
                          $cond['user_id'] = "A.user_id = '$user_id' ";
                   }
				   
				   if(isset($channel_id) and $channel_id!='')
				   {
                          $cond['channel_id'] = "A.channel_id = '$channel_id' ";
                   }
				   if(isset($company_id) and $company_id!='')
				   {
                          $cond['company_id'] = "A.company_id = '$company_id' ";
                   }
				   
				    
					if(!empty($cond))
					{
					 $con = implode('AND ',$cond); 
					 $con = "WHERE ".$con;
					}
		
		         	 $sql= "select A.*,B.channel,C.username,F.*,F.id as comp_id from ci_leads as A 
					 LEFT JOIN ci_company as F ON A.company_id = F.id 
					 LEFT JOIN ci_channel as B ON A.channel_id = B.id 
					 LEFT JOIN ci_state as D ON F.state = D.id 
					 LEFT JOIN ci_city as E ON F.city = E.id 
					 LEFT JOIN ci_users as C ON A.user_id = C.id $con";
					 
					
					
	                      $rows = $this->db->query($sql);
						 if($rows->num_rows()>=1)
						 {
						   foreach($rows->result_array() as  $row)
						   {
							  $data[] = $row; 
						    }
						 }
						 return $data;
      }
	 
	   public function getCity()
	 {
		   $result=array();
			 
		      $this->db->select('A.city');
			  $this->db->from('ci_leads as A');
			  $this->db->distinct('A.city');
			  
			  $data = $this->db->get();
		foreach($data->result_array() as $row)
		{
			$result[]= $row;
		}
		
		return $result;
		 
	 
	 }
	 
	 public function get_user_data($id)
	 {
		  $result=array();
			 
		      $this->db->select('A.*');
			  $this->db->from('ci_users as A');
			  $this->db->where('id',$id);
			   $data = $this->db->get();
		foreach($data->result_array() as $row)
		{
			$result[]= $row;
		}
		
		return $result;
	 }
	 
	 
	 
	  public function saveData($data,$table)
	 {
		$this->db->insert($table,$data);
		return true;
	 }
	 
	   public function updateData($col,$value,$data,$table)
	 {
		 $this->db->where($col,$value);
		$this->db->update($table,$data);
		return true;
	 }
	 
	   public function deleteData($col,$value,$table)
	 {
		 $this->db->where($col,$value);
		$this->db->delete($table);
		return true;
	 }
	 
	 
}