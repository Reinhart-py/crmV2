<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Report_model extends CI_model {

	 function __construct() {

    	parent::__construct();
		$this->load->database();	
	}
    
    
	public function fetchTeamList($parent = 0, $user_tree_array = '') {
 
		if (!is_array($user_tree_array))
		$user_tree_array = array();
	 
	 	$sql = "SELECT name,id FROM ci_user WHERE manager=".$parent;
	  
	  	$rows = $this->db->query($sql);	
		if($rows->num_rows()>=1){
			foreach($rows->result_array() as  $row){
				
				$user_tree_array[] = $row['id'];
				$user_tree_array = $this->fetchTeamList($row['id'], $user_tree_array);	
				}
			}

			return $user_tree_array;
			

	}


	    
	public function fetchTeamListByDepartment($department = 0, $user_tree_array = '') {
 
		if (!is_array($user_tree_array))
		$user_tree_array = array();
	 

		 if(empty($department)){
			$sql = "SELECT name,id FROM ci_user WHERE department!=''";
		 }else{
			$sql = "SELECT name,id FROM ci_user WHERE department='$department'";
		 }

	  	$rows = $this->db->query($sql);	
		  if($rows->num_rows()>=1){
			foreach($rows->result_array() as  $row){
				
				$user_tree_array[] = $row['id'];
				$user_tree_array = $this->fetchTeamListByDepartment($row['id'], $user_tree_array);	
				}
			}

			return $user_tree_array;
			

	}


	public function getPerformanceReportByIndividual($d1='',$d2='',$manager_id)
	{
		$data = array();
		$cond = array();
		$con='';

		$today = date('Y-m-d');
		$last_date= date('Y-m-d', strtotime("-7 day"));
		$next_date= date('Y-m-d', strtotime("+7 day"));

	
		if((isset($d1) and $d1!='') AND (isset($d2) and $d2!=''))
		{
			$cond['created'] = "A.created between '$d1' AND '$d2' ";
		}	
		
		if(isset($manager_id) and $manager_id!='')
		{
			$cond['manager_id'] = "A.created_by ='$manager_id'";
		}

		//$cond['users'] = "B.role=3"; 
		if(!empty($cond)){
		$con = implode(' AND ',$cond);	
		$con = "WHERE ".$con;
		}

		$order = 'ORDER BY A.created DESC';	

	
		$sql = "SELECT A.*, COUNT(id) AS totalLead, ( SELECT COUNT(DISTINCT id) FROM ci_followup as a WHERE a.followup_date<'$today' AND a.followup_date!='' and a.lead_id=A.id ) AS missedFollowups,( SELECT COUNT(DISTINCT id) FROM ci_followup as a WHERE a.followup_remark!='' and a.lead_id=A.id) AS attendedFollowups,( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.m_date<'$today' AND a.m_date!='' and FIND_IN_SET(a.created_by,'".$manager_id."')>0) AS totalMissedMeetings,( SELECT COUNT(DISTINCT id) FROM ci_meetings as a WHERE a.m_remark!='' and FIND_IN_SET(a.created_by,'".$manager_id."')>0) AS attendedMeetings
			FROM ci_leads as A  			
			$con GROUP BY A.created $order" ; 

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



	public function getPerformanceReportByDepartment($d1='',$d2='', $department='')
	{
		$data = array();
		$cond = array();
		$con='';

		$today = date('Y-m-d');
		$last_date= date('Y-m-d', strtotime("-7 day"));
		$next_date= date('Y-m-d', strtotime("+7 day"));

				   
		if((isset($d1) and $d1!='') AND (isset($d2) and $d2!='') )
		{
			$cond['created'] = "A.created between '$d1' AND '$d2' ";
		}
		
		
		if(isset($department) and $department!='')
		{
			$cond['department'] = "B.department ='$department'";
		}

		$team_array= $this->fetchTeamListByDepartment($department,'');	
		$team_str= implode(',',$team_array);			

		$cond['my']="FIND_IN_SET(A.created_by,'".$team_str."')>0";
		$wh="FIND_IN_SET(A.created_by,'".$team_str."')>0";
		//$cond['users'] = "B.role=3"; 
		if(!empty($cond)){
		$con = implode(' AND ',$cond);	
		$con = "WHERE ".$con;
		}




		$order = 'ORDER BY B.id DESC';	
	
		$sql = "SELECT A.*, COUNT(A.id) as totalLead, SUM(A.mrr) as amount, B.name,B.id,B.department ,( SELECT COUNT(id) FROM ci_leads a WHERE FIND_IN_SET(a.status, 42)>0 and a.created_by=B.id ) AS activeLead,( SELECT COUNT(DISTINCT id) FROM ci_leads a WHERE a.status=39 and a.created_by=B.id ) AS closedLead,( SELECT COUNT(DISTINCT id) FROM ci_followup as a WHERE a.followup_date<'$today' AND a.followup_date!='' and a.lead_id=A.id) AS total_missed,( SELECT COUNT(DISTINCT id) FROM ci_followup as a WHERE a.followup_remark!='' and a.lead_id=A.id) AS attendedFollowups,( SELECT COUNT(DISTINCT id) FROM ci_meetings as a WHERE a.m_date<'$today' AND a.m_date!='' and a.lead_id=A.id) AS totalMissedMeetings,( SELECT COUNT(DISTINCT id) FROM ci_meetings as a WHERE a.m_remark!='' and a.lead_id=A.id) AS attendedMeetings,( SELECT COUNT(id) FROM ci_verticals a WHERE a.created_by=B.id ) AS totalVerticals FROM ci_leads as A  LEFT JOIN  ci_user as B ON A.created_by=B.id  $con AND  B.department!='' GROUP BY B.id $order" ; 

		$rows = $this->db->query($sql);
		//echo $this->db->last_query();
			if($rows->num_rows()>=1)
			{
			foreach($rows->result_array() as  $row)
			{
				$data[] = $row; 
			}
			}
			return $data;
			
		  }	
	
	public function getPerformanceReportForUser($d1='', $d2='', $user_id=0)
	{
		$data = array();
		$today = date('Y-m-d');
		$user_id = (int)$user_id;
		if ($user_id <= 0) return $data;

		$cond = array("A.created_by = $user_id");
		if (!empty($d1) && !empty($d2)) {
			$cond[] = "A.created BETWEEN '$d1' AND '$d2'";
		}
		$con = "WHERE " . implode(' AND ', $cond);

		$sql = "SELECT A.*, COUNT(A.id) as totalLead, SUM(CAST(A.mrr AS DECIMAL(10,2))) as amount, B.name, B.id as user_id, B.department,
			(SELECT COUNT(id) FROM ci_leads a WHERE FIND_IN_SET(a.status, 42)>0 AND a.created_by = B.id) AS activeLead,
			(SELECT COUNT(DISTINCT id) FROM ci_leads a WHERE a.status = 39 AND a.created_by = B.id) AS closedLead,
			(SELECT COUNT(DISTINCT id) FROM ci_followup a WHERE a.followup_date < '$today' AND a.followup_date != '' AND a.lead_id = A.id) AS total_missed,
			(SELECT COUNT(DISTINCT id) FROM ci_followup a WHERE a.followup_remark != '' AND a.lead_id = A.id) AS attendedFollowups
			FROM ci_leads as A
			LEFT JOIN ci_user as B ON A.created_by = B.id
			$con
			GROUP BY B.id";
		$rows = $this->db->query($sql);
		if ($rows && $rows->num_rows() >= 1) {
			return $rows->result_array();
		}
		return $data;
	}	
	

	 
	 	public function get_user_report($d1='',$d2='',$role='',$manager='',$status='',$department='',$office='')
		{
				      $data = array();
                      $cond = array();
					  $con='';
					
				   if((isset($d1) and $d1!='') AND (isset($d2) and $d2!='') )
				   {
					   $cond['created'] = "A.created between '$d1' AND '$d2' ";
			       }
				  
				  
				  if(isset($role) and $role!='')
				   {
                 		  $cond['role'] = "A.user_type ='$role'";
				   }
				   
				    if(isset($manager) and $manager!='')
				   {
                 		  $cond['manager'] = "A.manager ='$manager'";
				   }
				   
				   if(isset($status) and $status!='')
				   {
                 		  $cond['status'] = "A.admin_status ='$status'";
				   }
				   
				   if(isset($department) and $department!='')
				   {
                 		  $cond['department'] = "A.department ='$department'";
				   }
				  
				 
				   
				    if(isset($office) and $office!='')
				   {
                 		 $cond['office'] = "A.office='$office'";	
						  
				   }
				   
				   $team_array= $this->fetchTeamList($this->session->userdata('manager_id'),'');
					$team_str= implode(',',$team_array);
					$cond['my']="FIND_IN_SET(A.id,'".$team_str."')>0";
				  
				  //$cond['users'] = "B.role=3"; 
				   if(!empty($cond)){
				$con = implode(' AND ',$cond);	
				$con = "WHERE ".$con;
			}
				
			$order = 'ORDER BY A.id DESC';				
		
			$sql = "SELECT A.*,B.title as role_name, C.name as manager_name
			 FROM ci_user as A  
			 LEFT JOIN ci_roles as B ON A.user_type = B.id LEFT JOIN ci_user as C ON A.manager = C.id
			 $con GROUP BY A.id $order" ; 
		
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
	  
	  
	 	public function get_lead_report($d1='',$d2='',$f1='',$f2='',$category='',$created_by='',$status='',$s_cat='')
{
				      $data = array();
                      $cond = array();
					  $con='';
					
				   if((isset($d1) and $d1!='') AND (isset($d2) and $d2!='') )
				   {
					   $cond['created'] = "A.created between '$d1' AND '$d2' ";
			       }

			       if((isset($f1) and $f1!='') AND (isset($f2) and $f2!='') )
				   {
					   $cond['followup_date'] = "A.followup_date between '$f1' AND '$f2' ";
			       }
				  
				  
				  if(isset($category) and $category!='')
				   {
                 		  $cond['category'] = "A.category ='$category'";
				   }
				   
				    if(isset($created_by) and $created_by!='')
				   {
                 		  $cond['created_by'] = "A.created_by ='$created_by'";
				   }
				   
				   if(isset($status) and $status!='')
				   {
                 		  $cond['status'] = "A.status ='$status'";
				   }
				   
				   if(isset($s_cat) and $s_cat!='')
				   {
                 		  $cond['s_cat'] = "A.s_cat ='$s_cat'";
				   }

				   $team_array= $this->fetchTeamList($this->session->userdata('manager_id'),'');
					$team_str= implode(',',$team_array);
					$cond['my']="FIND_IN_SET(A.created_by,'".$team_str."')>0";
				   //$cond['my']="B.user_level >= ".$this->session->userdata('manager_level');
				 
				  
				  //$cond['users'] = "B.role=3"; 
				   if(!empty($cond)){
				$con = implode(' AND ',$cond);	
				$con = "WHERE ".$con;
			}
				
			$order = 'ORDER BY A.id DESC';				
		
			$sql = "SELECT A.*,B.name as lead_maker,C.status as status_name

			 FROM ci_leads as A  
			 LEFT JOIN ci_user as B ON A.created_by = B.id
			 LEFT JOIN ci_status as C ON A.status = C.id 
			 $con GROUP BY A.id $order" ; 
		
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
	  
	  public function get_vertical_report($d1='',$d2='',$f1='',$f2='',$category='',$created_by='',$status='',$s_cat='')
{
				      $data = array();
                      $cond = array();
					  $con='';
					
				   if((isset($d1) and $d1!='') AND (isset($d2) and $d2!='') )
				   {
					   $cond['created'] = "A.created between '$d1' AND '$d2' ";
			       }

			       if((isset($f1) and $f1!='') AND (isset($f2) and $f2!='') )
				   {
					   $cond['followup_date'] = "A.followup_date between '$f1' AND '$f2' ";
			       }
				  
				  
				  if(isset($category) and $category!='')
				   {
                 		  $cond['category'] = "A.category ='$category'";
				   }
				   
				    if(isset($created_by) and $created_by!='')
				   {
                 		  $cond['created_by'] = "A.created_by ='$created_by'";
				   }
				   
				   if(isset($status) and $status!='')
				   {
                 		  $cond['status'] = "A.status ='$status'";
				   }
				   
				   if(isset($s_cat) and $s_cat!='')
				   {
                 		  $cond['s_cat'] = "A.s_cat ='$s_cat'";
				   }
				  
				 
				  
				  //$cond['users'] = "B.role=3"; 
				   if(!empty($cond)){
				$con = implode(' AND ',$cond);	
				$con = "WHERE ".$con;
			}
				
			$order = 'ORDER BY A.id DESC';				
		
			$sql = "SELECT A.*,B.name as lead_maker,C.status as status_name

			 FROM ci_leads as A  
			 LEFT JOIN ci_user as B ON A.created_by = B.id
			 LEFT JOIN ci_status as C ON A.status = C.id 
			 $con GROUP BY A.id $order" ; 
		
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
	  
	  public function get_vertical_report_fixed($type)
{
				      $data = array();
                      $cond = array();
					  $con='';
					
				   if((isset($d1) and $d1!='') AND (isset($d2) and $d2!='') )
				   {
					   $cond['created'] = "A.created between '$d1' AND '$d2' ";
			       }

			       if((isset($f1) and $f1!='') AND (isset($f2) and $f2!='') )
				   {
					   $cond['followup_date'] = "A.followup_date between '$f1' AND '$f2' ";
			       }
				  
				  
				  if(isset($category) and $category!='')
				   {
                 		  $cond['category'] = "A.category ='$category'";
				   }
				   
				    if(isset($created_by) and $created_by!='')
				   {
                 		  $cond['created_by'] = "A.created_by ='$created_by'";
				   }
				   
				   if(isset($status) and $status!='')
				   {
                 		  $cond['status'] = "A.status ='$status'";
				   }
				   
				   if(isset($s_cat) and $s_cat!='')
				   {
                 		  $cond['s_cat'] = "A.s_cat ='$s_cat'";
				   }
				  
				 
				  
				  //$cond['users'] = "B.role=3"; 
				   if(!empty($cond)){
				$con = implode(' AND ',$cond);	
				$con = "WHERE ".$con;
			}
				
			$order = 'ORDER BY A.id DESC';				
		
			$sql = "SELECT A.*,B.name as lead_maker,C.status as status_name

			 FROM ci_leads as A  
			 LEFT JOIN ci_user as B ON A.created_by = B.id
			 LEFT JOIN ci_status as C ON A.status = C.id 
			 $con GROUP BY A.id $order" ; 
		
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
	  

	 
			public function get_followup_report($d1='',$d2='',$f1='',$f2='',$category='',$created_by='',$status='',$s_cat='')
			{
								  $data = array();
								  $cond = array();
								  $con='';
								
							   if((isset($d1) and $d1!='') AND (isset($d2) and $d2!='') )
							   {
								   $cond['created'] = "A.created between '$d1' AND '$d2' ";
							   }
			
							   if((isset($f1) and $f1!='') AND (isset($f2) and $f2!='') )
							   {
								   $cond['followup_date'] = "A.followup_date between '$f1' AND '$f2' ";
							   }
							  
							  
							  if(isset($category) and $category!='')
							   {
									   $cond['category'] = "A.category ='$category'";
							   }
							   
								if(isset($created_by) and $created_by!='')
							   {
									   $cond['created_by'] = "A.created_by ='$created_by'";
							   }
							   
							   if(isset($status) and $status!='')
							   {
									   $cond['status'] = "A.status ='$status'";
							   }
							   
							   if(isset($s_cat) and $s_cat!='')
							   {
									   $cond['s_cat'] = "A.s_cat ='$s_cat'";
							   }
							  
							 
							  
							  //$cond['users'] = "B.role=3"; 
							   if(!empty($cond)){
							$con = implode(' AND ',$cond);	
							$con = "WHERE ".$con;
						}
							
						$order = 'ORDER BY A.id DESC';				
					
						$sql = "SELECT A.*,B.name as lead_maker,C.status as status_name
			
						 FROM ci_leads as A  
						 LEFT JOIN ci_user as B ON A.created_by = B.id
						 LEFT JOIN ci_status as C ON A.status = C.id 
						 $con GROUP BY A.id $order" ; 
					
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
				  
			public function get_followup_report_fixed($type)
			{
								  $data = array();
								  $cond = array();
								  $today = date('Y-m-d');
								  $last_date= date('Y-m-d', strtotime("-7 day"));
								  $next_date= date('Y-m-d', strtotime("+7 day"));
								  $con='';
								
							   if($type==1)
							   {
								   $cond['followup_date'] = "A.followup_date < '$today'";
							   }elseif($type==2)
							   {
								$cond['followup_date'] = "A.followup_date between '$today' AND '$last_date' ";
							   }elseif($type==3)
							   {
								$cond['followup_date'] = "A.followup_date = '$today'";
							   }elseif($type==4)
							   {
								$cond['followup_date'] = "A.followup_date between '$today' AND '$next_date' ";
							   }elseif($type==5)
							   {
								$cond['followup_date'] = "A.followup_date > '$today'";
							   }
			
						if(!empty($cond)){
							$con = implode(' AND ',$cond);	
							$con = "WHERE ".$con;
						}
							
						$order = 'ORDER BY A.id DESC';				
					
						$sql = "SELECT A.*,B.name as lead_maker,C.status as status_name
			
						 FROM ci_leads as A  
						 LEFT JOIN ci_user as B ON A.created_by = B.id
						 LEFT JOIN ci_status as C ON A.status = C.id 
						 $con GROUP BY A.id $order" ; 
					
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
	  
	 
}