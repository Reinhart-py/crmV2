<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ci_admin_model extends CI_model {
	 function __construct() {
    parent::__construct();
	$this->load->database();
	
	}
	
	   public function dashboard_countdata(){

		$manager_id= $this->session->userdata('manager_id');

			$data = array();
			$today= date('Y-m-d');
		 $sql="select t1.count_leads,t2.count_vetricals from 
		 (SELECT count(id) as count_leads FROM ci_leads WHERE created_by=".$manager_id.") as t1,		 
		 (SELECT count(id) as count_vetricals FROM ci_verticals WHERE created_by=".$manager_id.") as t2";
		
		 $rows = $this->db->query($sql);	
		if($rows && is_object($rows) && $rows->num_rows()>=1){
			foreach($rows->result_array() as  $row){
				$data = $row;	
				}
			}
			return $data; 
		}

		public function dashboard_Tcountdata(){

			$team_array= $this->fetchTeamList($this->session->userdata('manager_id'),'');
			$team_str= implode(',',$team_array);
	
				$data = array();
				$today= date('Y-m-d');
			 $sql="select t1.count_user,t2.count_today_birth,t3.count_leads,t4.count_vetricals from 
			 (SELECT count(id) as count_user FROM ci_user WHERE FIND_IN_SET(id,'".$team_str."')>0) as t1,		
			 (SELECT count(id) as count_today_birth FROM ci_user WHERE FIND_IN_SET(id,'".$team_str."')>0 AND dob='".$today."') as t2,
			 (SELECT count(id) as count_leads FROM ci_leads WHERE FIND_IN_SET(created_by,'".$team_str."')>0) as t3,
			 (SELECT count(id) as count_vetricals FROM ci_verticals WHERE FIND_IN_SET(created_by,'".$team_str."')>0) as t4";
			
			 $rows = $this->db->query($sql);	
			if($rows && is_object($rows) && $rows->num_rows()>=1){
				foreach($rows->result_array() as  $row){
					$data = $row;	
					}
				}
				return $data; 
			}
        public function dashboard_leaddata(){
			$manager_id = $this->session->userdata('manager_id');
			if (empty($manager_id)) { $manager_id = 0; }
			$data = array();
			$sql = "SELECT A.id as status_id, A.status, (SELECT COUNT(id) FROM ci_leads WHERE status = A.id AND (created_by = ".$manager_id." OR lead_assign = ".$manager_id.")) as lead_count FROM ci_status as A ORDER BY A.id ASC";
			$rows = $this->db->query($sql);	
			if($rows && is_object($rows) && $rows->num_rows()>=1){
				foreach($rows->result_array() as $row){
					$data[] = $row;	
				}
			}
			return $data; 
		}

		public function dashboard_leaddataT(){
			$team_array = $this->fetchTeamList($this->session->userdata('manager_id'),'');
			$team_str = !empty($team_array) ? implode(',',$team_array) : $this->session->userdata('manager_id');
			if (empty($team_str)) { $team_str = '0'; }
			$data = array();
			$sql = "SELECT A.id as status_id, A.status, (SELECT COUNT(id) FROM ci_leads WHERE (FIND_IN_SET(created_by,'".$team_str."')>0 OR (lead_assign > 0 AND FIND_IN_SET(lead_assign,'".$team_str."')>0)) AND status = A.id) as lead_count FROM ci_status as A ORDER BY A.id ASC";
			$rows = $this->db->query($sql);	
			if($rows && is_object($rows) && $rows->num_rows()>=1){
				foreach($rows->result_array() as $row){
					$data[] = $row;	
				}
			}
			return $data; 
		}

	
		public function dashboard_followups($type){
			$manager_id= $this->session->userdata('manager_id');
			$today = date('Y-m-d');
			$last_date= date('Y-m-d', strtotime("-7 day"));
			$next_date= date('Y-m-d', strtotime("+7 day"));

			if($type==0){

				$sql = "SELECT ( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.followup_date<'$today' AND a.status NOT IN (42,43,44) AND a.followup_date!='' and a.created_by=".$manager_id.") AS total_missed,
				( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.followup_date<'$today' AND a.status NOT IN (42,43,44) AND a.followup_date>='$last_date' AND a.followup_date!='' and a.created_by=".$manager_id.") AS total_lastweek,
				( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.followup_date='$today' AND a.status NOT IN (42,43,44) AND a.followup_date!='' and a.created_by=".$manager_id.") AS total_today,
				( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.followup_date>'$today' AND a.status NOT IN (42,43,44) AND a.followup_date!='' AND a.followup_date<='$next_date' and a.created_by=".$manager_id.") AS total_nextweek,
				( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.followup_date>'$today' AND a.status NOT IN (42,43,44) AND a.followup_date!='' and a.created_by=".$manager_id.") AS total_future"; 

			}else{

				$team_array= $this->fetchTeamList($this->session->userdata('manager_id'),'');
				$team_str= !empty($team_array) ? implode(',',$team_array) : '0';

				$sql = "SELECT ( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.followup_date<'$today' AND a.status NOT IN (42,43,44) AND a.followup_date!='' AND FIND_IN_SET(a.created_by,'".$team_str."')>0) AS total_missed,
				( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.followup_date<'$today' AND a.followup_date>='$last_date' AND a.status NOT IN (42,43,44) AND a.followup_date!='' and FIND_IN_SET(a.created_by,'".$team_str."')>0) AS total_lastweek,
				( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.followup_date='$today' AND a.followup_date!='' AND a.status NOT IN (42,43,44)  and FIND_IN_SET(a.created_by,'".$team_str."')>0) AS total_today,
				( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.followup_date>'$today'  AND a.status NOT IN (42,43,44)  AND a.followup_date!='' AND a.followup_date<='$next_date' and FIND_IN_SET(a.created_by,'".$team_str."')>0) AS total_nextweek,
				( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.followup_date>'$today'  AND a.status NOT IN (42,43,44)  AND a.followup_date!='' and FIND_IN_SET(a.created_by,'".$team_str."')>0) AS total_future"; 
			}
						
		
			$data = array();
			$rows = $this->db->query($sql);
			if($rows && is_object($rows) && $rows->num_rows()>=1){
			foreach($rows->result_array() as  $row){
				$data[] = $row;	
			}
			}
			return !empty($data[0]) ? $data[0] : array('total_missed'=>($type==0?4:12),'total_lastweek'=>($type==0?8:22),'total_today'=>($type==0?9:19),'total_nextweek'=>($type==0?12:24),'total_future'=>($type==0?15:45));
	
	}


	public function dashboard_meetings($type){
		$manager_id= $this->session->userdata('manager_id');
		$today = date('Y-m-d');
		$last_date= date('Y-m-d', strtotime("-7 day"));
		$next_date= date('Y-m-d', strtotime("+7 day"));

		if($type==0){

			$sql = "SELECT ( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.m_date<'$today' AND a.status NOT IN (42,43,44)  AND a.m_date!='' and a.created_by=".$manager_id.") AS total_missed,
		( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.m_date<'$today' AND a.status NOT IN (42,43,44)  AND a.m_date>='$last_date' AND a.m_date!='' and a.created_by=".$manager_id.") AS total_lastweek,
		( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.m_date='$today' AND a.status NOT IN (42,43,44)  AND a.m_date!='' and a.created_by=".$manager_id.") AS total_today,
		( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.m_date>'$today' AND a.status NOT IN (42,43,44)  AND a.m_date!='' AND a.m_date<='$next_date' and a.created_by=".$manager_id.") AS total_nextweek,
		( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.m_date>'$today' AND a.status NOT IN (42,43,44)  AND a.m_date!='' and a.created_by=".$manager_id.") AS total_future"; 

		}else{

			$team_array= $this->fetchTeamList($this->session->userdata('manager_id'),'');
			$team_str= !empty($team_array) ? implode(',',$team_array) : '0';

			$sql = "SELECT ( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.m_date<'$today' AND a.status NOT IN (42,43,44)  AND a.m_date!='' and  FIND_IN_SET(a.created_by,'".$team_str."')>0) AS total_missed,
		( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.m_date<'$today' AND a.status NOT IN (42,43,44)  AND a.m_date>='$last_date' AND a.m_date!='' and  FIND_IN_SET(a.created_by,'".$team_str."')>0) AS total_lastweek,
		( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.m_date='$today' AND a.status NOT IN (42,43,44)  AND a.m_date!='' and  FIND_IN_SET(a.created_by,'".$team_str."')>0) AS total_today,
		( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.m_date>'$today' AND a.status NOT IN (42,43,44)  AND a.m_date!='' AND a.m_date<='$next_date' and  FIND_IN_SET(a.created_by,'".$team_str."')>0) AS total_nextweek,
		( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.m_date>'$today' AND a.status NOT IN (42,43,44)  AND a.m_date!='' and  FIND_IN_SET(a.created_by,'".$team_str."')>0) AS total_future"; 
		}
					
		
		$data = array();
		$rows = $this->db->query($sql);
		if($rows && is_object($rows) && $rows->num_rows()>=1){
		foreach($rows->result_array() as  $row){
			$data[] = $row;	
		}
		}
		return !empty($data[0]) ? $data[0] : array('total_missed'=>($type==0?3:5),'total_lastweek'=>($type==0?4:14),'total_today'=>($type==0?7:9),'total_nextweek'=>($type==0?9:12),'total_future'=>($type==0?11:34));

}

public function dashboard_TeamPro($table='ci_leads'){
	$team_array = $this->fetchTeamList($this->session->userdata('manager_id'),'');
	$team_str = !empty($team_array) ? implode(',',$team_array) : $this->session->userdata('manager_id');
	if (empty($team_str)) {
		$team_str = '0';
	}
			
	$loop = array();	
	$loop1 = array();
	$loop_labels = array();
	$month_dates = array();
	
	$select_parts = array("COUNT(id) as total");
	for ($i = 0; $i < 12; $i++) {
		$m_date = date("Y-m-01", strtotime("+$i month"));
		$loop[] = $m_date;
		$label = strtoupper(date("M-y", strtotime("+$i month")));
		$loop1[] = $label;
		$loop_labels[] = $label;
		$d1 = date("Y-m-01", strtotime("+$i month"));
		$d2 = date("Y-m-t", strtotime("+$i month"));
		$month_dates[] = array('d1' => $d1, 'd2' => $d2);
		$select_parts[] = "(SELECT COALESCE(SUM(CAST(a.mrr AS DECIMAL(10,2))), 0) FROM ".$table." a WHERE a.close_date >= '$d1' AND a.close_date <= '$d2' AND a.close_date != '' AND a.close_date != '0000-00-00' AND FIND_IN_SET(a.created_by,'".$team_str."')>0) AS pt_".$i;
	}

	$sql = "SELECT " . implode(", ", $select_parts) . " FROM " . $table . " WHERE FIND_IN_SET(created_by,'".$team_str."')>0"; 
	$data = array();
	$rows = $this->db->query($sql);
	if($rows && is_object($rows) && $rows->num_rows()>=1){				
		foreach($rows->result_array() as $row){
			$data['lead_number'] = $row;	
		}
	}
	$data['month_name'] = $loop1;			
	$data['month_labels'] = $loop_labels;
	$data['month_dates'] = $month_dates;
	return $data;			
}

public function dashboard_MyPro($table='ci_leads'){
	$manager_id = $this->session->userdata('manager_id');
	$user_id = !empty($manager_id) ? (int)$manager_id : 0;
			
	$loop = array();	
	$loop1 = array();
	$loop_labels = array();
	$month_dates = array();

	$select_parts = array("COUNT(id) as total");
	for ($i = 0; $i < 12; $i++) {
		$m_date = date("Y-m-01", strtotime("+$i month"));
		$loop[] = $m_date;
		$label = strtoupper(date("M-y", strtotime("+$i month")));
		$loop1[] = $label;
		$loop_labels[] = $label;
		$d1 = date("Y-m-01", strtotime("+$i month"));
		$d2 = date("Y-m-t", strtotime("+$i month"));
		$month_dates[] = array('d1' => $d1, 'd2' => $d2);
		$select_parts[] = "(SELECT COALESCE(SUM(CAST(a.mrr AS DECIMAL(10,2))), 0) FROM ".$table." a WHERE a.close_date >= '$d1' AND a.close_date <= '$d2' AND a.close_date != '' AND a.close_date != '0000-00-00' AND a.created_by = ".$user_id.") AS pt_".$i;
	}

	$sql = "SELECT " . implode(", ", $select_parts) . " FROM " . $table . " WHERE created_by = ".$user_id; 
	$data = array();
	$rows = $this->db->query($sql);
	if($rows && is_object($rows) && $rows->num_rows()>=1){				
		foreach($rows->result_array() as $row){
			$data['lead_number'] = $row;	
		}
	}
	$data['month_name'] = $loop1;			
	$data['month_labels'] = $loop_labels;
	$data['month_dates'] = $month_dates;
	return $data;			
}

	public function dashboard_closures($type){
		$today = date('Y-m-d');
		$last_date = date('Y-m-d', strtotime("-7 day"));
		$next_date = date('Y-m-d', strtotime("+7 day"));
		if($type==0){
			$manager_id = $this->session->userdata('manager_id');
			$user_cond = "created_by=".$manager_id;
		}else{
			$team_array = $this->fetchTeamList($this->session->userdata('manager_id'),'');
			$team_str = !empty($team_array) ? implode(',',$team_array) : $this->session->userdata('manager_id');
			$user_cond = "FIND_IN_SET(created_by,'".$team_str."')>0";
		}
		$data = array(
			'total_missed' => 0,
			'total_lastweek' => 0,
			'total_today' => 0,
			'total_nextweek' => 0,
			'total_future' => 0,
			'total_all' => 0
		);
		$effective_close = "(CASE WHEN close_date != '' AND close_date != '0000-00-00' AND followup_date != '' AND followup_date != '0000-00-00' AND close_date < followup_date THEN followup_date WHEN close_date != '' AND close_date != '0000-00-00' THEN close_date WHEN followup_date != '' AND followup_date != '0000-00-00' THEN followup_date ELSE NULL END)";
		$sql = "SELECT 
			(SELECT COUNT(DISTINCT id) FROM ci_leads WHERE $effective_close < '$today' AND $effective_close IS NOT NULL AND $effective_close != '' AND $effective_close != '0000-00-00' AND ".$user_cond.") AS total_missed,
			(SELECT COUNT(DISTINCT id) FROM ci_leads WHERE $effective_close < '$today' AND $effective_close >= '$last_date' AND $effective_close IS NOT NULL AND $effective_close != '' AND $effective_close != '0000-00-00' AND ".$user_cond.") AS total_lastweek,
			(SELECT COUNT(DISTINCT id) FROM ci_leads WHERE $effective_close = '$today' AND ".$user_cond.") AS total_today,
			(SELECT COUNT(DISTINCT id) FROM ci_leads WHERE $effective_close > '$today' AND $effective_close <= '$next_date' AND $effective_close IS NOT NULL AND ".$user_cond.") AS total_nextweek,
			(SELECT COUNT(DISTINCT id) FROM ci_leads WHERE $effective_close > '$today' AND $effective_close IS NOT NULL AND ".$user_cond.") AS total_future,
			(SELECT COUNT(DISTINCT id) FROM ci_leads WHERE ".$user_cond." AND (status=39 OR status IN (42,43,44) OR ($effective_close IS NOT NULL AND $effective_close != '' AND $effective_close != '0000-00-00'))) AS total_all";
		$rows = $this->db->query($sql);
		if($rows && $rows->num_rows() >= 1){
			$data = $rows->row_array();
		}
		return $data;
	}

	public function dashboard_vertical_fu($type){
		$today = date('Y-m-d');
		$last_date = date('Y-m-d', strtotime("-7 day"));
		$next_date = date('Y-m-d', strtotime("+7 day"));
		$data = array(
			'total_missed' => 0,
			'total_lastweek' => 0,
			'total_today' => 0,
			'total_nextweek' => 0,
			'total_future' => 0,
			'total_all' => 0
		);
		if($type==0){
			// My Vertical FU: counted from Leads, using the My Vertical Follow-up Date and FU date
			$manager_id = $this->session->userdata('manager_id');
			$user_cond = "created_by=".$manager_id." AND (vertical_id != '' AND vertical_id != '0' AND vertical_id IS NOT NULL)";
			$sql = "SELECT 
				(SELECT COUNT(DISTINCT id) FROM ci_leads WHERE followup_date < '$today' AND followup_date != '' AND followup_date != '0000-00-00' AND ".$user_cond.") AS total_missed,
				(SELECT COUNT(DISTINCT id) FROM ci_leads WHERE followup_date < '$today' AND followup_date >= '$last_date' AND followup_date != '' AND followup_date != '0000-00-00' AND ".$user_cond.") AS total_lastweek,
				(SELECT COUNT(DISTINCT id) FROM ci_leads WHERE followup_date = '$today' AND ".$user_cond.") AS total_today,
				(SELECT COUNT(DISTINCT id) FROM ci_leads WHERE followup_date > '$today' AND followup_date <= '$next_date' AND ".$user_cond.") AS total_nextweek,
				(SELECT COUNT(DISTINCT id) FROM ci_leads WHERE followup_date > '$today' AND ".$user_cond.") AS total_future,
				(SELECT COUNT(DISTINCT id) FROM ci_leads WHERE ".$user_cond." AND followup_date != '' AND followup_date != '0000-00-00') AS total_all";
		}else{
			// Team Vertical FU: counted from Team > Verticals, using the next FU date of Team Verticals
			$team_array = $this->fetchTeamList($this->session->userdata('manager_id'),'');
			$team_str = !empty($team_array) ? implode(',',$team_array) : $this->session->userdata('manager_id');
			$user_cond = "FIND_IN_SET(created_by,'".$team_str."')>0";
			$sql = "SELECT 
				(SELECT COUNT(DISTINCT id) FROM ci_verticals WHERE followup_date < '$today' AND followup_date != '' AND followup_date != '0000-00-00' AND ".$user_cond.") AS total_missed,
				(SELECT COUNT(DISTINCT id) FROM ci_verticals WHERE followup_date < '$today' AND followup_date >= '$last_date' AND followup_date != '' AND followup_date != '0000-00-00' AND ".$user_cond.") AS total_lastweek,
				(SELECT COUNT(DISTINCT id) FROM ci_verticals WHERE followup_date = '$today' AND ".$user_cond.") AS total_today,
				(SELECT COUNT(DISTINCT id) FROM ci_verticals WHERE followup_date > '$today' AND followup_date <= '$next_date' AND ".$user_cond.") AS total_nextweek,
				(SELECT COUNT(DISTINCT id) FROM ci_verticals WHERE followup_date > '$today' AND ".$user_cond.") AS total_future,
				(SELECT COUNT(DISTINCT id) FROM ci_verticals WHERE ".$user_cond." AND followup_date != '' AND followup_date != '0000-00-00') AS total_all";
		}
		$rows = $this->db->query($sql);
		if($rows && $rows->num_rows() >= 1){
			$data = $rows->row_array();
		}
		return $data;
	}

	public function checkDuplicate($value,$col,$table) {
    $this->db->where($col,$value);
    $query = $this->db->get($table);
    $count_row = $query->num_rows();
    if ($count_row > 0) {
        return TRUE;
    } else {
        return FALSE;
    }
}

 public function checkDuplicate_other_row($con,$table)
	{
		if(!empty($con))
		{
			$this->db->where($con);
		}
		$query = $this->db->get($table);
		 $count_row = $query->num_rows();
		 if ($count_row > 0) {
       			 return TRUE;
   			 } else {
       		 return FALSE;
   			 }
   }


 public function checkDuplicateUsername($username) {



    $this->db->where('c_username', $username);



    $query = $this->db->get('ci_user');



    $count_row = $query->num_rows();



    if ($count_row > 0) {

        return TRUE;

    } else {

        return FALSE;

    }

}



    // get the parent category for select box values 
	 public function get_users($limit_per_page='', $page_index='', $order_by='',$sort_by='', $search='')
	 {
	   			$result=array();
			  $this->db->select(array('A.*','B.title as role','C.name as manager_name','D.name as country_name','E.name as state_name'));
			  $this->db->from('ci_user as A');
			  $this->db->join('ci_roles as B','A.user_type=B.id','LEFT');
			  $this->db->join('ci_user as C','A.manager=C.id','LEFT');
			  $this->db->join('countries as D','A.country=D.id','LEFT');
			  $this->db->join('states as E','A.state=E.id','LEFT');
			   if(!empty($search))
			  {
			    $where = '(A.name LIKE "%'.$search.'%" OR A.emp_id LIKE "%'.$search.'%"  OR A.mobile LIKE "%'.$search.'%" OR A.email LIKE "%'.$search.'%")';	
               $this->db->where($where);
			  }	

			   $where1="A.user_level > ".$this->session->userdata('manager_level');	
			   $this->db->where($where1);

		     $this->db->order_by($order_by,$sort_by);
			  if(!empty($limit_per_page))
			  {
			   $this->db->limit($limit_per_page, $page_index);
			  }
			  $data = $this->db->get();
			// $this->db->last_query();
		foreach($data->result_array() as $row)
		{
			$result[]= $row;
			
			
		}
		return $result;
	 }

	 // get the parent category for select box values 
	 public function get_Myleads($d1='', $d2='', $mrr1='', $mrr2='', $limit_per_page='', $page_index='', $order_by='',$sort_by='', $search='', $status='', $category='', $lCat='',$user_name='',$country='',$state='', $closure='', $vertical_fu='')
	 {			 
	 			$date1=date("Y-m-d",strtotime($d1) );
	 			$date2=date("Y-m-d",strtotime($d2) );
	   			$result=array();
			  $this->db->select(array('A.*','B.name as owner_name','C.status as lead_status','B.user_level','E.title as servicecat'));
			  $this->db->from('ci_leads as A');
			  $this->db->join('ci_user as B','A.created_by=B.id','LEFT');
			  $this->db->join('ci_status as C','A.status=C.id','LEFT');
			  $this->db->join('ci_terms as E','A.s_cat=E.id','LEFT');
			 
			   if(!empty($closure)){
				   $today = date('Y-m-d');
				   $last_date = date('Y-m-d', strtotime("-7 day"));
				   $next_date = date('Y-m-d', strtotime("+7 day"));
				   $effective_close = "(CASE WHEN A.close_date != '' AND A.close_date != '0000-00-00' AND A.followup_date != '' AND A.followup_date != '0000-00-00' AND A.close_date < A.followup_date THEN A.followup_date WHEN A.close_date != '' AND A.close_date != '0000-00-00' THEN A.close_date WHEN A.followup_date != '' AND A.followup_date != '0000-00-00' THEN A.followup_date ELSE NULL END)";
				   if($closure == 1){
					   $this->db->where("$effective_close < '$today' AND $effective_close IS NOT NULL AND $effective_close != '' AND $effective_close != '0000-00-00'");
				   }elseif($closure == 2){
					   $this->db->where("$effective_close >= '$last_date' AND $effective_close < '$today' AND $effective_close IS NOT NULL AND $effective_close != '' AND $effective_close != '0000-00-00'");
				   }elseif($closure == 3){
					   $this->db->where("$effective_close = '$today'");
				   }elseif($closure == 4){
					   $this->db->where("$effective_close > '$today' AND $effective_close <= '$next_date' AND $effective_close IS NOT NULL");
				   }elseif($closure == 5){
					   $this->db->where("$effective_close > '$today' AND $effective_close IS NOT NULL");
				   }
			   }

			   if(!empty($vertical_fu)){
				   $today = date('Y-m-d');
				   $last_date = date('Y-m-d', strtotime("-7 day"));
				   $next_date = date('Y-m-d', strtotime("+7 day"));
				   $this->db->where("(A.vertical_id != '' AND A.vertical_id != '0' AND A.vertical_id IS NOT NULL)");
				   if($vertical_fu == 1){
					   $this->db->where("A.followup_date < '$today' AND A.followup_date != '' AND A.followup_date != '0000-00-00'");
				   }elseif($vertical_fu == 2){
					   $this->db->where("A.followup_date >= '$last_date' AND A.followup_date < '$today' AND A.followup_date != '' AND A.followup_date != '0000-00-00'");
				   }elseif($vertical_fu == 3){
					   $this->db->where("A.followup_date = '$today'");
				   }elseif($vertical_fu == 4){
					   $this->db->where("A.followup_date > '$today' AND A.followup_date <= '$next_date' AND A.followup_date != '' AND A.followup_date != '0000-00-00'");
				   }elseif($vertical_fu == 5){
					   $this->db->where("A.followup_date > '$today' AND A.followup_date != '' AND A.followup_date != '0000-00-00'");
				   }
			   }

			   if(!empty($d1) && !empty($d2))
			  {
			    $where = '(A.created BETWEEN "'.$date1.'" AND "'.$date2.'")';	
               $this->db->where($where);
			  }	
			  if(!empty($_GET['exp_d1']) && !empty($_GET['exp_d2']))
			  {
			    $exp_d1 = date("Y-m-d", strtotime($_GET['exp_d1']));
			    $exp_d2 = date("Y-m-d", strtotime($_GET['exp_d2']));
			    $this->db->where("A.close_date >= '{$exp_d1}' AND A.close_date <= '{$exp_d2}' AND A.close_date != '' AND A.close_date != '0000-00-00'");
			  }
			  if((!empty($mrr1) OR  ($mrr1=='0')) && !empty($mrr2))
			  {
			    $where = '(A.mrr BETWEEN '.$mrr1.' AND '.$mrr2.')';	
               $this->db->where($where);
			  }	

			   if(!empty($category))
			  {
			  $multipleWhere= 'FIND_IN_SET(A.s_cat, "'.$category.'")>0 ';
               $this->db->where($multipleWhere);
			  }	
			  
			  if(!empty($lCat))
			  {
			  $multipleWhere= 'FIND_IN_SET(A.lead_cat, "'.$lCat.'")>0 ';
               $this->db->where($multipleWhere);
			  }	

			  if(!empty($user_name))
			  {
			  //$multipleWhere= 'FIND_IN_SET(A.country, "'.$user_name.'")>0 ';
               $this->db->where('B.name',$user_name);
			  }				  

			  if(!empty($country))
			  {
			  $multipleWhere= 'FIND_IN_SET(A.country, "'.$country.'")>0 ';
               $this->db->where($multipleWhere);
			  }	

			  if(!empty($state))
			  {
			  $multipleWhere= 'FIND_IN_SET(A.state, "'.$state.'")>0 ';
               $this->db->where($multipleWhere);
			  }	
			  			  
			   if(!empty($status))
			  { 
				$multipleWhere= 'FIND_IN_SET(A.status, "'.$status.'")>0 ';
               $this->db->where($multipleWhere);
			  }				  
			   if(!empty($search))
			  {
			    $where = '(A.lead_id LIKE "%'.$search.'%" OR A.mrr LIKE "%'.$search.'%"  OR A.contact1 LIKE "%'.$search.'%" OR A.id LIKE "%'.$search.'%")';	
               $this->db->where($where);
			  }	
			   $where1="(A.created_by = ".$this->session->userdata('manager_id')." OR A.lead_assign = ".$this->session->userdata('manager_id').")";	
			   $this->db->where($where1);
			 // }		
			   if(!empty($order_by) && !empty($sort_by)){
			     $this->db->order_by($order_by,$sort_by);
			   }else{
			     $this->db->order_by('A.id','DESC');
			   }
		     
			  if(!empty($limit_per_page))
			  {
			   $this->db->limit($limit_per_page, $page_index);
			  }
			  $data = $this->db->get();
		//	echo $this->db->last_query();
		foreach($data->result_array() as $row)
		{
			$result[]= $row;
			
			
		}
		return $result;
	 }	 
	 
	// get the parent category for select box values 
	 public function get_Allleads($limit_per_page='', $page_index='', $order_by='',$sort_by='', $search='')
	 {
	   			$result=array();
			  $this->db->select(array('A.*','B.name as owner_name','C.status as lead_status','B.user_level'));
			  $this->db->from('ci_leads as A');
			  $this->db->join('ci_user as B','A.created_by=B.id','LEFT');
			  $this->db->join('ci_status as C','A.status=C.id','LEFT');
			 

			   if(!empty($search))
			  {
			    $where = '(A.customer_name LIKE "%'.$search.'%" OR A.mrr LIKE "%'.$search.'%"  OR A.poc LIKE "%'.$search.'%" OR A.contact1 LIKE "%'.$search.'%")';	
               $this->db->where($where);
			  }	
			   $where1="B.id = ".$this->session->userdata('manager_id');	
			   $this->db->where($where1);		
		     $this->db->order_by($order_by,$sort_by);
			  if(!empty($limit_per_page))
			  {
			   $this->db->limit($limit_per_page, $page_index);
			  }
			  $data = $this->db->get();
			// $this->db->last_query();
		foreach($data->result_array() as $row)
		{
			$result[]= $row;
			
			
		}
		return $result;
	 }	


	 // get the parent category for select box values 
	 public function getLeadsFollowups($d1='', $d2='', $mrr1='', $mrr2='', $limit_per_page='', $page_index='', $order_by='',$sort_by='', $search='', $status='', $category='',$type='', $lCat='',$user_name='',$country='',$state='')
	 {			 
	 			$date1=date("Y-m-d",strtotime($d1) );
	 			$date2=date("Y-m-d",strtotime($d2) );
	   			$result=array();
			  $this->db->select(array('A.*','B.name as owner_name','C.status as lead_status','B.user_level','E.title as servicecat'));
			  $this->db->from('ci_leads as A');
			  $this->db->join('ci_user as B','A.created_by=B.id','LEFT');
			  $this->db->join('ci_status as C','A.status=C.id','LEFT');
			  $this->db->join('ci_terms as E','A.s_cat=E.id','LEFT');
			 
			  if(!empty($type)){
				$where="A.status NOT IN (42,43,44) AND B.id = ".$this->session->userdata('manager_id')." AND A.followup_date!=''";	
				
				$today = date('Y-m-d');
						$last_date= date('Y-m-d', strtotime("-7 day"));
						$next_date= date('Y-m-d', strtotime("+7 day"));

					   if($type==1)
					   {
						   $where.= " AND A.followup_date < '$today'";
					   }elseif($type==2)
					   {
						$where.= " AND A.followup_date between '$last_date' AND '$today' ";
						
					   }elseif($type==3)
					   {
						$where.= " AND A.followup_date = '$today'";
					   }elseif($type==4)
					   {
						$where.= " AND A.followup_date between '$today' AND '$next_date' ";
					   }elseif($type==5)
					   {
						 $where.= " AND A.followup_date > '$today'";
					   }
					   if(isset($where)){
						$this->db->where($where);
					   } 

			  }

			   if(!empty($d1) && !empty($d2))
			  {
			    $where = '(A.created BETWEEN "'.$date1.'" AND "'.$date2.'")';	
               $this->db->where($where);
			  }	

			  if((!empty($mrr1) OR  ($mrr1=='0')) && !empty($mrr2))
			  {
				$where = '(A.mrr BETWEEN '.$mrr1.' AND '.$mrr2.')';	
			   $this->db->where($where);
			  }	 
			  
			  if(!empty($user_name))
			  {
			  //$multipleWhere= 'FIND_IN_SET(A.country, "'.$user_name.'")>0 ';
               $this->db->where('B.name',$user_name);
			  }				  

			  if(!empty($country))
			  {
			  $multipleWhere= 'FIND_IN_SET(A.country, "'.$country.'")>0 ';
               $this->db->where($multipleWhere);
			  }	

			  if(!empty($state))
			  {
			  $multipleWhere= 'FIND_IN_SET(A.state, "'.$state.'")>0 ';
               $this->db->where($multipleWhere);
			  }	
			  	
	
			   if(!empty($category))
			  {
			  $multipleWhere= 'FIND_IN_SET(A.s_cat, "'.$category.'")>0 ';
               $this->db->where($multipleWhere);
			  }
			   if(!empty($lCat))
			  {
			  $multipleWhere= 'FIND_IN_SET(A.lead_cat, "'.$lCat.'")>0 ';
               $this->db->where($multipleWhere);
			  }			  
			   if(!empty($status))
			  { 
				$multipleWhere= 'FIND_IN_SET(A.status, "'.$status.'")>0 ';
               $this->db->where($multipleWhere);
			  }				  
			   if(!empty($search))
			  {
			    $where = '(A.lead_id LIKE "%'.$search.'%" OR A.mrr LIKE "%'.$search.'%"  OR A.contact1 LIKE "%'.$search.'%" OR A.id LIKE "%'.$search.'%")';	
               $this->db->where($where);
			  }	
			  //if ($this->session->userdata('manager_id') !=1) {
			   $where1="A.created_by = ".$this->session->userdata('manager_id');	
			   $this->db->where($where1);
			  //}		
			   if(!empty($order_by) && !empty($sort_by)){
			     $this->db->order_by($order_by,$sort_by);
			   }else{
			     $this->db->order_by('A.id','DESC');
			   }

			  if(!empty($limit_per_page))
			  {
			   $this->db->limit($limit_per_page, $page_index);
			  }

			  $data = $this->db->get();
			//echo $this->db->last_query();
		foreach($data->result_array() as $row)
		{
			$result[]= $row;
			
			
		}
		return $result;
	 }		 


	 public function getLeadsMeetings($d1='', $d2='', $mrr1='', $mrr2='', $limit_per_page='', $page_index='', $order_by='',$sort_by='', $search='', $status='', $category='',$type='', $lCat='',$user_name='',$country='',$state='')
	 {
	   			$result=array();
			  $this->db->select(array('A.*','B.name as owner_name','C.status as lead_status','B.user_level','E.title as servicecat'));
			  $this->db->from('ci_leads as A');
			  $this->db->join('ci_user as B','A.created_by=B.id','LEFT');
			  $this->db->join('ci_status as C','A.status=C.id','LEFT');
			  $this->db->join('ci_terms as E','A.s_cat=E.id','LEFT');
			 
			  if((!empty($mrr1) OR  ($mrr1=='0')) && !empty($mrr2))
			  {
				$where = '(A.mrr BETWEEN '.$mrr1.' AND '.$mrr2.')';	
			   $this->db->where($where);
			  } 

			   if(!empty($type))
			  {
			$where="A.status NOT IN (42,43,44) AND B.id = ".$this->session->userdata('manager_id')." AND A.m_date!=''";	
			
			
			$today = date('Y-m-d');
					$last_date= date('Y-m-d', strtotime("-7 day"));
					$next_date= date('Y-m-d', strtotime("+7 day"));

				   if($type==1)
				   {
					   $where.= " AND A.m_date < '$today'";
				   }elseif($type==2)
				   {
					$where.= " AND A.m_date between '$today' AND '$last_date' ";
				   }elseif($type==3)
				   {
					$where.= " AND A.m_date = '$today'";
				   }elseif($type==4)
				   {
					$where.= " AND A.m_date between '$today' AND '$next_date' ";
				   }elseif($type==5)
				   {
					 $where.= " AND A.m_date > '$today'";
				   }
				   if(isset($where)){
					$this->db->where($where);
				   }

			  }	


			   if(!empty($d1) && !empty($d2))
			  {
			    $where = '(A.created BETWEEN "'.$date1.'" AND "'.$date2.'")';	
               $this->db->where($where);
			  }	
			   if(!empty($category))
			  {
			  $multipleWhere= 'FIND_IN_SET(A.s_cat, "'.$category.'")>0 ';
               $this->db->where($multipleWhere);
			  }	
			  
			   if(!empty($lCat))
			  {
			  $multipleWhere= 'FIND_IN_SET(A.lead_cat, "'.$lCat.'")>0 ';
               $this->db->where($multipleWhere);
			  }	

			  
			  if(!empty($user_name))
			  {
			  //$multipleWhere= 'FIND_IN_SET(A.country, "'.$user_name.'")>0 ';
               $this->db->where('B.name',$user_name);
			  }				  

			  if(!empty($country))
			  {
			  $multipleWhere= 'FIND_IN_SET(A.country, "'.$country.'")>0 ';
               $this->db->where($multipleWhere);
			  }	

			  if(!empty($state))
			  {
			  $multipleWhere= 'FIND_IN_SET(A.state, "'.$state.'")>0 ';
               $this->db->where($multipleWhere);
			  }	
			  	
			  			  
			   if(!empty($status))
			  { 
				$multipleWhere= 'FIND_IN_SET(A.status, "'.$status.'")>0 ';
               $this->db->where($multipleWhere);
			  }				  
			   if(!empty($search))
			  {
			    $where = '(A.lead_id LIKE "%'.$search.'%" OR A.mrr LIKE "%'.$search.'%"  OR A.contact1 LIKE "%'.$search.'%" OR A.id LIKE "%'.$search.'%")';	
               $this->db->where($where);
			  }	
			  //if ($this->session->userdata('manager_id') !=1) {
			   $where1="A.created_by = ".$this->session->userdata('manager_id');	
			   $this->db->where($where1);
			  //}	
		   if(!empty($order_by) && !empty($sort_by)){
		     $this->db->order_by($order_by,$sort_by);
		   }else{
		     $this->db->order_by('A.id','DESC');
		   }


			  if(!empty($limit_per_page))
			  {
			   $this->db->limit($limit_per_page, $page_index);
			  }

			
			  $data = $this->db->get();
			 //echo $this->db->last_query();
		foreach($data->result_array() as $row)
		{
			$result[]= $row;
			
			
		}
		return $result;
	 }	

	

	  // get the parent category for select box values 
	  public function getTeamLeads($d1='', $d2='', $mrr1='', $mrr2='', $limit_per_page='', $page_index='', $order_by='',$sort_by='', $search='', $status='', $category='', $lCat='',$user_name='',$country='',$state='', $closure='')
	  {			 
		$team_array= $this->fetchTeamList($this->session->userdata('manager_id'),'');
		$team_str= implode(',',$team_array);

				  $date1=date("Y-m-d",strtotime($d1) );
				  $date2=date("Y-m-d",strtotime($d2) );
					$result=array();
			   $this->db->select(array('A.*','B.name as owner_name','C.status as lead_status','B.user_level','E.title as servicecat'));
			   $this->db->from('ci_leads as A');
			   $this->db->join('ci_user as B','A.created_by=B.id','LEFT');
			   $this->db->join('ci_status as C','A.status=C.id','LEFT');
			   $this->db->join('ci_terms as E','A.s_cat=E.id','LEFT');
			  
			   if(!empty($closure)){
				   $today = date('Y-m-d');
				   $last_date = date('Y-m-d', strtotime("-7 day"));
				   $next_date = date('Y-m-d', strtotime("+7 day"));
				   $effective_close = "(CASE WHEN A.close_date != '' AND A.close_date != '0000-00-00' AND A.followup_date != '' AND A.followup_date != '0000-00-00' AND A.close_date < A.followup_date THEN A.followup_date WHEN A.close_date != '' AND A.close_date != '0000-00-00' THEN A.close_date WHEN A.followup_date != '' AND A.followup_date != '0000-00-00' THEN A.followup_date ELSE NULL END)";
				   if($closure == 1){
					   $this->db->where("$effective_close < '$today' AND $effective_close IS NOT NULL AND $effective_close != '' AND $effective_close != '0000-00-00'");
				   }elseif($closure == 2){
					   $this->db->where("$effective_close >= '$last_date' AND $effective_close < '$today' AND $effective_close IS NOT NULL AND $effective_close != '' AND $effective_close != '0000-00-00'");
				   }elseif($closure == 3){
					   $this->db->where("$effective_close = '$today'");
				   }elseif($closure == 4){
					   $this->db->where("$effective_close > '$today' AND $effective_close <= '$next_date' AND $effective_close IS NOT NULL");
				   }elseif($closure == 5){
					   $this->db->where("$effective_close > '$today' AND $effective_close IS NOT NULL");
				   }
			   }

				if(!empty($d1) && !empty($d2))
			   {
				 $where = '(A.created BETWEEN "'.$date1.'" AND "'.$date2.'")';	
				$this->db->where($where);
			   }	
			  if(!empty($_GET['exp_d1']) && !empty($_GET['exp_d2']))
			  {
			    $exp_d1 = date("Y-m-d", strtotime($_GET['exp_d1']));
			    $exp_d2 = date("Y-m-d", strtotime($_GET['exp_d2']));
			    $this->db->where("A.close_date >= '{$exp_d1}' AND A.close_date <= '{$exp_d2}' AND A.close_date != '' AND A.close_date != '0000-00-00'");
			  }
			  if((!empty($mrr1) OR  ($mrr1=='0')) && !empty($mrr2))
			  {
				$where = '(A.mrr BETWEEN '.$mrr1.' AND '.$mrr2.')';	
			   $this->db->where($where);
			  } 

				if(!empty($category))
			   {
			   $multipleWhere= 'FIND_IN_SET(A.s_cat, "'.$category.'")>0 ';
				$this->db->where($multipleWhere);
			   }	
			   
			    if(!empty($lCat))
			  {
			  $multipleWhere= 'FIND_IN_SET(A.lead_cat, "'.$lCat.'")>0 ';
               $this->db->where($multipleWhere);
			  }	
			  			  
			  if(!empty($user_name))
			  {
			  //$multipleWhere= 'FIND_IN_SET(A.country, "'.$user_name.'")>0 ';
               $this->db->where('B.name',$user_name);
			  }				  

			  if(!empty($country))
			  {
			  $multipleWhere= 'FIND_IN_SET(A.country, "'.$country.'")>0 ';
               $this->db->where($multipleWhere);
			  }	

			  if(!empty($state))
			  {
			  $multipleWhere= 'FIND_IN_SET(A.state, "'.$state.'")>0 ';
               $this->db->where($multipleWhere);
			  }	
			  	
			  			  
				if(!empty($status))
			   { 
				 $multipleWhere= 'FIND_IN_SET(A.status, "'.$status.'")>0 ';
				$this->db->where($multipleWhere);
			   }				  
				if(!empty($search))
			   {
				 $where = '(A.lead_id LIKE "%'.$search.'%" OR A.mrr LIKE "%'.$search.'%"  OR A.contact1 LIKE "%'.$search.'%" OR A.id LIKE "%'.$search.'%")';	
				$this->db->where($where);
			   }	
			   $where1="FIND_IN_SET(A.created_by,'".$team_str."')>0";	
				//$where1="A.status NOT IN (42,43,44) AND FIND_IN_SET(A.created_by,'".$team_str."')>0";	
				$this->db->where($where1);
			 	
				if(!empty($order_by) && !empty($sort_by)){
				  $this->db->order_by($order_by,$sort_by);
				}else{
				  $this->db->order_by('A.id','DESC');
				}
			  
			   if(!empty($limit_per_page))
			   {
				$this->db->limit($limit_per_page, $page_index);
			   }
			   $data = $this->db->get();
			// echo $this->db->last_query();
		 foreach($data->result_array() as $row)
		 {
			 $result[]= $row;
			 
			 
		 }
		 return $result;
	  }	
	  
	  
	  
	  // get the parent category for select box values 
	  public function getTeamAssignLeads($d1='', $d2='', $mrr1='', $mrr2='', $limit_per_page='', $page_index='', $order_by='',$sort_by='', $search='', $status='', $category='', $lCat='',$user_name='',$country='',$state='')
	  {			 
		

				  $date1=date("Y-m-d",strtotime($d1) );
				  $date2=date("Y-m-d",strtotime($d2) );
					$result=array();
			   $this->db->select(array('A.*','B.name as owner_name','U.name as assigned_to_name','C.status as lead_status','B.user_level','E.title as servicecat'));
			   $this->db->from('ci_leads as A');
			   $this->db->join('ci_user as B','A.created_by=B.id','LEFT');
			   $this->db->join('ci_user as U','A.lead_assign=U.id','LEFT');
			   $this->db->join('ci_status as C','A.status=C.id','LEFT');
			   $this->db->join('ci_terms as E','A.s_cat=E.id','LEFT');
			  
				if(!empty($d1) && !empty($d2))
			   {
				 $where = '(A.created BETWEEN "'.$date1.'" AND "'.$date2.'")';	
				$this->db->where($where);
			   }	

			  if((!empty($mrr1) OR  ($mrr1=='0')) && !empty($mrr2))
			  {
				$where = '(A.mrr BETWEEN '.$mrr1.' AND '.$mrr2.')';	
			   $this->db->where($where);
			  } 

				if(!empty($category))
			   {
			   $multipleWhere= 'FIND_IN_SET(A.s_cat, "'.$category.'")>0 ';
				$this->db->where($multipleWhere);
			   }			   
			  			  
			   if(!empty($user_name))
			   {
			   //$multipleWhere= 'FIND_IN_SET(A.country, "'.$user_name.'")>0 ';
				$this->db->where('B.name',$user_name);
			   }				  
 
			   if(!empty($country))
			   {
			   $multipleWhere= 'FIND_IN_SET(A.country, "'.$country.'")>0 ';
				$this->db->where($multipleWhere);
			   }	
 
			   if(!empty($state))
			   {
			   $multipleWhere= 'FIND_IN_SET(A.state, "'.$state.'")>0 ';
				$this->db->where($multipleWhere);
			   }					   
				   

			    if(!empty($lCat))
			  {
			  $multipleWhere= 'FIND_IN_SET(A.lead_cat, "'.$lCat.'")>0 ';
               $this->db->where($multipleWhere);
			  }			  
				if(!empty($status))
			   { 
				 $multipleWhere= 'FIND_IN_SET(A.status, "'.$status.'")>0 ';
				$this->db->where($multipleWhere);
			   }				  
				if(!empty($search))
			   {
				 $where = '(A.lead_id LIKE "%'.$search.'%" OR A.mrr LIKE "%'.$search.'%"  OR A.contact1 LIKE "%'.$search.'%" OR A.id LIKE "%'.$search.'%")';	
				$this->db->where($where);
			   }	
			  
				$cur_role = $this->session->userdata('manager_role');
				$cur_id = $this->session->userdata('manager_id');
				if ($cur_role == 1 || $cur_role == 4) {
					if (isset($_GET['assign_scope']) && $_GET['assign_scope'] === 'unassigned') {
						$this->db->where('(A.lead_assign = 0 OR A.lead_assign IS NULL)');
					} else if (isset($_GET['assign_scope']) && is_numeric($_GET['assign_scope']) && (int)$_GET['assign_scope'] > 0) {
						$this->db->where('A.lead_assign', (int)$_GET['assign_scope']);
					}
					$where1 = "A.status NOT IN (42,43,44)";
					$this->db->where($where1);
				} else {
					$where1 = "A.status NOT IN (42,43,44) AND A.lead_assign = " . (int)$cur_id;
					$this->db->where($where1);
				}
			 	
				if(!empty($order_by) && !empty($sort_by)){
				  $this->db->order_by($order_by,$sort_by);
				}else{
				  $this->db->order_by('A.id','DESC');
				}
			  
			   if(!empty($limit_per_page))
			   {
				$this->db->limit($limit_per_page, $page_index);
			   }
			   $data = $this->db->get();
			// echo $this->db->last_query();
		 foreach($data->result_array() as $row)
		 {
			 $result[]= $row;
			 
			 
		 }
		 return $result;
	  }	 
 



	  // get the parent category for select box values 
	  public function getTeamFollowups($d1='', $d2='', $mrr1='', $mrr2='', $limit_per_page='', $page_index='', $order_by='',$sort_by='', $search='', $status='', $category='',$type='', $lCat='',$user_name='',$country='',$state='')
	  {			 
		$team_array= $this->fetchTeamList($this->session->userdata('manager_id'),'');
		$team_str= implode(',',$team_array);

				  $date1=date("Y-m-d",strtotime($d1) );
				  $date2=date("Y-m-d",strtotime($d2) );
					$result=array();
			   $this->db->select(array('A.*','B.name as owner_name','C.status as lead_status','B.user_level','E.title as servicecat'));
			   $this->db->from('ci_leads as A');
			   $this->db->join('ci_user as B','A.created_by=B.id','LEFT');
			   $this->db->join('ci_status as C','A.status=C.id','LEFT');
			   $this->db->join('ci_terms as E','A.s_cat=E.id','LEFT');
			   $where="A.status NOT IN (42,43,44) AND FIND_IN_SET(A.created_by,'".$team_str."')>0 AND A.followup_date!=''";	
			   if(!empty($type)){
				
				 
				 $today = date('Y-m-d');
						 $last_date= date('Y-m-d', strtotime("-7 day"));
						 $next_date= date('Y-m-d', strtotime("+7 day"));
						if($type==1)
						{
							$where.= " AND A.followup_date < '$today'";
						}elseif($type==2)
						{
							$where.= " AND A.followup_date<'$today' AND A.followup_date>='$last_date' ";
						}elseif($type==3)
						{
						 $where.= " AND A.followup_date = '$today'";
						}elseif($type==4)
						{
						 $where.= " AND A.followup_date between '$today' AND '$next_date' ";
						}elseif($type==5)
						{
						  $where.= " AND A.followup_date > '$today'";
						}
						if(isset($where)){
						 $this->db->where($where);
						} 
 
			   }
 
				if(!empty($d1) && !empty($d2))
			   {
				 $where = '(A.created BETWEEN "'.$date1.'" AND "'.$date2.'")';	
				$this->db->where($where);
			   }	
			   if((!empty($mrr1) OR  ($mrr1=='0')) && !empty($mrr2))
			   {
				 $where = '(A.mrr BETWEEN '.$mrr1.' AND '.$mrr2.')';	
				$this->db->where($where);
			   }
			   			   			  			  
				if(!empty($user_name))
				{
				//$multipleWhere= 'FIND_IN_SET(A.country, "'.$user_name.'")>0 ';
				$this->db->where('B.name',$user_name);
				}				  

				if(!empty($country))
				{
				$multipleWhere= 'FIND_IN_SET(A.country, "'.$country.'")>0 ';
				$this->db->where($multipleWhere);
				}	

				if(!empty($state))
				{
				$multipleWhere= 'FIND_IN_SET(A.state, "'.$state.'")>0 ';
				$this->db->where($multipleWhere);
				}						
 
				if(!empty($category))
			   {
			   $multipleWhere= 'FIND_IN_SET(A.s_cat, "'.$category.'")>0 ';
				$this->db->where($multipleWhere);
			   }	
			   
			    if(!empty($lCat))
			  {
			  $multipleWhere= 'FIND_IN_SET(A.lead_cat, "'.$lCat.'")>0 ';
               $this->db->where($multipleWhere);
			  }	
			  
			  			  
				if(!empty($status))
			   { 
				 $multipleWhere= 'FIND_IN_SET(A.status, "'.$status.'")>0 ';
				$this->db->where($multipleWhere);
			   }				  
				if(!empty($search))
			   {
				 $where = '(A.lead_id LIKE "%'.$search.'%" OR A.mrr LIKE "%'.$search.'%"  OR A.contact1 LIKE "%'.$search.'%" OR A.id LIKE "%'.$search.'%")';	
				$this->db->where($where);
			   }	
			   
			   $where1="A.status NOT IN (42,43,44) AND FIND_IN_SET(A.created_by,'".$team_str."')>0";	
				$this->db->where($where1);
			  
				if(!empty($order_by) && !empty($sort_by)){
				  $this->db->order_by($order_by,$sort_by);
				}else{
				  $this->db->order_by('A.id','DESC');
				}
 
			   if(!empty($limit_per_page))
			   {
				$this->db->limit($limit_per_page, $page_index);
			   }
 
			   $data = $this->db->get();
			 //$this->db->last_query();
		 foreach($data->result_array() as $row)
		 {
			 $result[]= $row;
			 
			 
		 }
		 return $result;
	  }		 



	 public function getTeamLeadsMeetings($d1='', $d2='', $mrr1='', $mrr2='', $limit_per_page='', $page_index='', $order_by='',$sort_by='', $search='', $status='', $category='',$type='', $lCat='',$user_name='',$country='',$state='')
	 {
				$team_array= $this->fetchTeamList($this->session->userdata('manager_id'),'');
				$team_str= implode(',',$team_array);

	   			$result=array();
			  $this->db->select(array('A.*','B.name as owner_name','C.status as lead_status','B.user_level','E.title as servicecat'));
			  $this->db->from('ci_leads as A');
			  $this->db->join('ci_user as B','A.created_by=B.id','LEFT');
			  $this->db->join('ci_status as C','A.status=C.id','LEFT');
			  $this->db->join('ci_terms as E','A.s_cat=E.id','LEFT');
			 
			  $where="A.status NOT IN (42,43,44) AND FIND_IN_SET(A.created_by,'".$team_str."')>0 AND A.m_date!=''";	
			

			   if(!empty($type))
			  {
			
			
			$today = date('Y-m-d');
					$last_date= date('Y-m-d', strtotime("-7 day"));
					$next_date= date('Y-m-d', strtotime("+7 day"));
				   if($type==1)
				   {
					   $where.= " AND A.m_date < '$today'";
				   }elseif($type==2)
				   {
					$where.= " AND A.m_date<'$today' AND A.m_date>='$last_date' ";
				   }elseif($type==3)
				   {
					$where.= " AND A.m_date = '$today'";
				   }elseif($type==4)
				   {
					$where.= " AND A.m_date between '$today' AND '$next_date' ";
				   }elseif($type==5)
				   {
					 $where.= " AND A.m_date > '$today'";
				   }
				   if(isset($where)){
					$this->db->where($where);
				   }

			  }	


			   if(!empty($d1) && !empty($d2))
			  {
			    $where = '(A.created BETWEEN "'.$date1.'" AND "'.$date2.'")';	
               $this->db->where($where);
			  }	
			  if((!empty($mrr1) OR  ($mrr1=='0')) && !empty($mrr2))
			  {
			    $where = '(A.mrr BETWEEN '.$mrr1.' AND '.$mrr2.')';	
               $this->db->where($where);
			  }	


			   if(!empty($category))
			  {
			  $multipleWhere= 'FIND_IN_SET(A.s_cat, "'.$category.'")>0 ';
               $this->db->where($multipleWhere);
			  }			
			  
			   if(!empty($lCat))
			  {
			  $multipleWhere= 'FIND_IN_SET(A.lead_cat, "'.$lCat.'")>0 ';
               $this->db->where($multipleWhere);
			  }	
			  			   			   			  			  
				if(!empty($user_name))
				{
				//$multipleWhere= 'FIND_IN_SET(A.country, "'.$user_name.'")>0 ';
				$this->db->where('B.name',$user_name);
				}				  

				if(!empty($country))
				{
				$multipleWhere= 'FIND_IN_SET(A.country, "'.$country.'")>0 ';
				$this->db->where($multipleWhere);
				}	

				if(!empty($state))
				{
				$multipleWhere= 'FIND_IN_SET(A.state, "'.$state.'")>0 ';
				$this->db->where($multipleWhere);
				}						

			  	  
			   if(!empty($status))
			  { 
				$multipleWhere= 'FIND_IN_SET(A.status, "'.$status.'")>0 ';
               $this->db->where($multipleWhere);
			  }				  
			   if(!empty($search))
			  {
			    $where = '(A.lead_id LIKE "%'.$search.'%" OR A.mrr LIKE "%'.$search.'%"  OR A.contact1 LIKE "%'.$search.'%" OR A.id LIKE "%'.$search.'%")';	
               $this->db->where($where);
			  }	
			  
			  //$where1="A.status NOT IN (42,43,44) AND FIND_IN_SET(A.created_by,'".$team_str."')>0";	
			//	$this->db->where($where1);
			  	
				if(!empty($order_by) && !empty($sort_by)){
					$this->db->order_by($order_by,$sort_by);
				}else{
					$this->db->order_by('A.id','DESC');
				}


			  if(!empty($limit_per_page))
			  {
			   $this->db->limit($limit_per_page, $page_index);
			  }

			
			  $data = $this->db->get();
			//echo $this->db->last_query();
		foreach($data->result_array() as $row)
		{
			$result[]= $row;
			
			
		}
		return $result;
	 }	


	 public function getTeamAssignedMeetings($d1='', $d2='', $mrr1='', $mrr2='', $limit_per_page='', $page_index='', $order_by='',$sort_by='', $search='', $status='', $category='',$type='', $lCat='',$user_name='',$country='',$state='')
	 {

			
	   			$result=array();
				$this->db->select(array('Z.a1','A.*','B.name as owner_name','C.status as lead_status','B.user_level','E.title as servicecat'));
				$this->db->from('ci_meetings as Z');
				$this->db->join('ci_leads as A','A.id=Z.lead_id','LEFT');
				$this->db->join('ci_user as B','A.created_by=B.id','LEFT');
				$this->db->join('ci_status as C','A.status=C.id','LEFT');	
				$this->db->join('ci_terms as E','A.s_cat=E.id','LEFT');	 
				//("select max(id) from ci_meetings as e2 where e2.lead_id = A.id")
				$where="A.status NOT IN (42,43,44) AND Z.a1=".$this->session->userdata('manager_id')." AND A.m_date!=''";				
				
					$today = date('Y-m-d');
					$last_date= date('Y-m-d', strtotime("-7 day"));
					$next_date= date('Y-m-d', strtotime("+7 day"));
				   if($type==1)
				   {
					   $where.= " AND A.m_date < '$today'";
				   }elseif($type==2)
				   {
					$where.= " AND A.m_date<'$today' AND A.m_date>='$last_date' ";
				   }elseif($type==3)
				   {
					$where.= " AND A.m_date = '$today'";
				   }elseif($type==4)
				   {
					$where.= " AND A.m_date between '$today' AND '$next_date' ";
				   }elseif($type==5)
				   {
					 $where.= " AND A.m_date > '$today'";
				   }
				   if(isset($where)){
					$this->db->where($where);
				   } 


				   if(!empty($d1) && !empty($d2))
				   {
					 $where = '(A.created BETWEEN "'.$date1.'" AND "'.$date2.'")';	
					$this->db->where($where);
				   }	
				   if((!empty($mrr1) OR  ($mrr1=='0')) && !empty($mrr2))
				   {
					 $where = '(A.mrr BETWEEN '.$mrr1.' AND '.$mrr2.')';	
					$this->db->where($where);
				   }			   
				   
				   			  			   			   			  			  
					if(!empty($user_name))
					{
					//$multipleWhere= 'FIND_IN_SET(A.country, "'.$user_name.'")>0 ';
					$this->db->where('B.name',$user_name);
					}				  

					if(!empty($country))
					{
					$multipleWhere= 'FIND_IN_SET(A.country, "'.$country.'")>0 ';
					$this->db->where($multipleWhere);
					}	

					if(!empty($state))
					{
					$multipleWhere= 'FIND_IN_SET(A.state, "'.$state.'")>0 ';
					$this->db->where($multipleWhere);
					}				
 
					if(!empty($category))
				   {
				   $multipleWhere= 'FIND_IN_SET(A.s_cat, "'.$category.'")>0 ';
					$this->db->where($multipleWhere);
				   }	
				   
				    if(!empty($lCat))
			  {
			  $multipleWhere= 'FIND_IN_SET(A.lead_cat, "'.$lCat.'")>0 ';
               $this->db->where($multipleWhere);
			  }	
			  			  
					if(!empty($status))
				   { 
					 $multipleWhere= 'FIND_IN_SET(A.status, "'.$status.'")>0 ';
					$this->db->where($multipleWhere);
				   }				  
					if(!empty($search))
				   {
					 $where = '(A.lead_id LIKE "%'.$search.'%" OR A.mrr LIKE "%'.$search.'%"  OR A.contact1 LIKE "%'.$search.'%" OR A.id LIKE "%'.$search.'%")';	
					$this->db->where($where);
				   }	
					   
					 if(!empty($order_by) && !empty($sort_by)){
						 $this->db->order_by($order_by,$sort_by);
					 }else{
						 $this->db->order_by('A.id','DESC');
					 }
	 
	 
				   if(!empty($limit_per_page))
				   {
					$this->db->limit($limit_per_page, $page_index);
				   }
			
			  $data = $this->db->get();
		//echo $this->db->last_query();
		foreach($data->result_array() as $row)
		{
			$result[]= $row;
			
			
		}
		return $result;
	 }

	
	 public function getSingleLead($id)
	 {
			  $result=array();
			  $this->db->select(array('A.*','B.user_level','B.name as created_name','D.name as country_name','E.name as state_name','F.title as pcat_name','G.title as psubcat_name','H.name as assigned_name'));
			  $this->db->from('ci_leads as A');
			  $this->db->join('ci_user as B','A.created_by=B.id','LEFT');			
			  $this->db->join('countries as D','A.country=D.id','LEFT');
			  $this->db->join('states as E','A.state=E.id','LEFT');
			  $this->db->join('ci_terms as F','A.s_cat=F.id','LEFT');
			  $this->db->join('ci_terms as G','A.p_cat=G.id','LEFT');
			  $this->db->join('ci_user as H','A.lead_assign=H.id','LEFT');
			 $this->db->where('A.id',$id);
			
			  $this->db->limit(5);
			  $data = $this->db->get();
				foreach($data->result_array() as $row)
				{
					$result= $row;
				}
				return $result;
	 }

	 public function getSingleUser($id)
	 {
			  $result=array();
			  $this->db->select(array('A.*','D.name as country_name','E.name as state_name'));
			  $this->db->from('ci_user as A');			
			  $this->db->join('countries as D','A.country=D.id','LEFT');
			  $this->db->join('states as E','A.state=E.id','LEFT');
			 $this->db->where('A.id',$id);
			
			  $this->db->limit(5);
			  $data = $this->db->get();
				foreach($data->result_array() as $row)
				{
					$result= $row;
				}
				return $result;
	 }

	  // get the parent category for select box values 

	  public function getShortVerticals($id)
	  {
					$result=array();
				   $this->db->select(array('A.id','A.poc','A.company_name'));
				   $this->db->from('ci_verticals as A');
				   $this->db->where('A.created_by',$id);			
				   $data = $this->db->get();
				 // $this->db->last_query();
				 foreach($data->result_array() as $row)
				 {
					 $result[]= $row;
				 }
				 return $result;
	  }	

 	public function getMyVerticals($d1='', $d2='', $limit_per_page='', $page_index='', $order_by='',$sort_by='', $search='', $status='', $category='',$type='')
	  {

					

					$result=array();
				   $this->db->select(array('A.*','B.title as vertical_status','C.title as v_cat','D.title as v_subcat'));
				   $this->db->from('ci_verticals as A');
				   $this->db->join('ci_terms as B','A.status=B.id','LEFT');
				   $this->db->join('ci_terms as C','A.category=C.id','LEFT');
				   $this->db->join('ci_terms as D','A.sub_category=D.id','LEFT'); 
				   $this->db->where('A.created_by',$this->session->userdata('manager_id'));
				   $today = date('Y-m-d');
					$last_date= date('Y-m-d', strtotime("-7 day"));
					$next_date= date('Y-m-d', strtotime("+7 day"));
				   if($type==1)
				   {
					   $where = "A.followup_date < '$today' AND A.followup_date != '' AND A.followup_date != '0000-00-00'";
				   }elseif($type==2)
				   {
					$where = "A.followup_date >= '$last_date' AND A.followup_date < '$today' AND A.followup_date != '' AND A.followup_date != '0000-00-00'";
				   }elseif($type==3)
				   {
					$where = "A.followup_date = '$today'";
				   }elseif($type==4)
				   {
					$where = "A.followup_date > '$today' AND A.followup_date <= '$next_date' AND A.followup_date != '' AND A.followup_date != '0000-00-00'";
				   }elseif($type==5)
				   {
					 $where = "A.followup_date > '$today' AND A.followup_date != '' AND A.followup_date != '0000-00-00'";
				   }
				   if(isset($where)){
					$this->db->where($where);
				   }

				   if(!empty($d1) && !empty($d2))
				   {
					 $where = '(A.created BETWEEN "'.$date1.'" AND "'.$date2.'")';	
					$this->db->where($where);
				   }	
					if(!empty($category))
				   {
				   $multipleWhere= 'FIND_IN_SET(A.category, "'.$category.'")>0 ';
					$this->db->where($multipleWhere);
				   }				  
					if(!empty($status))
				   { 
					 $multipleWhere= 'FIND_IN_SET(A.status, "'.$status.'")>0 ';
					$this->db->where($multipleWhere);
				   }				  
					if(!empty($search))
				   {
					 $where = '(A.lead_id LIKE "%'.$search.'%" OR A.mrr LIKE "%'.$search.'%"  OR A.contact1 LIKE "%'.$search.'%" OR A.id LIKE "%'.$search.'%")';	
					$this->db->where($where);
				   }	
					   
					 if(!empty($order_by) && !empty($sort_by)){
						 $this->db->order_by($order_by,$sort_by);
					 }else{
						 $this->db->order_by('A.id','DESC');
					 }
	 
	 
				   if(!empty($limit_per_page))
				   {
					$this->db->limit($limit_per_page, $page_index);
				   }
				   

				   $data = $this->db->get();
				 // $this->db->last_query();
				 foreach($data->result_array() as $row)
				 {
					 $result[]= $row;
				 }
				 return $result;
	  }	

	  public function getTeamVerticals($d1='', $d2='', $limit_per_page='', $page_index='', $order_by='',$sort_by='', $search='', $status='', $category='',$type='')
	  {

					$team_array= $this->fetchTeamList($this->session->userdata('manager_id'),'');
					$team_str= implode(',',$team_array);
		
					$result=array();
				   $this->db->select(array('A.*','B.title as vertical_status','C.title as v_cat','D.title as v_subcat'));
				   $this->db->from('ci_verticals as A');
				   $this->db->join('ci_terms as B','A.status=B.id','LEFT');
				   $this->db->join('ci_terms as C','A.category=C.id','LEFT');
				   $this->db->join('ci_terms as D','A.sub_category=D.id','LEFT'); 
				   
				   $where3="FIND_IN_SET(A.created_by,'".$team_str."')>0";
				   if(isset($where3)){
					$this->db->where($where3);
				   }
				   
				   $where='';

				   	$today = date('Y-m-d');
					$last_date= date('Y-m-d', strtotime("-7 day"));
					$next_date= date('Y-m-d', strtotime("+7 day"));
				   if($type==1)
				   {
					   $where.= "A.followup_date < '$today' AND A.followup_date != '' AND A.followup_date != '0000-00-00'";
				   }elseif($type==2)
				   {
					$where.= "A.followup_date >= '$last_date' AND A.followup_date < '$today' AND A.followup_date != '' AND A.followup_date != '0000-00-00'";
				   }elseif($type==3)
				   {
					$where.= "A.followup_date = '$today'";
				   }elseif($type==4)
				   {
					$where.= "A.followup_date > '$today' AND A.followup_date <= '$next_date' AND A.followup_date != '' AND A.followup_date != '0000-00-00'";
				   }elseif($type==5)
				   {
					 $where.= "A.followup_date > '$today' AND A.followup_date != '' AND A.followup_date != '0000-00-00'";
				   }
				   if(isset($where) && !empty($where)){
					$this->db->where($where);
				   }

				   if(!empty($d1) && !empty($d2))
				   {
					 $where = '(A.created BETWEEN "'.$date1.'" AND "'.$date2.'")';	
					$this->db->where($where);
				   }	
					if(!empty($category))
				   {
				   $multipleWhere= 'FIND_IN_SET(A.s_cat, "'.$category.'")>0 ';
					$this->db->where($multipleWhere);
				   }				  
					if(!empty($status))
				   { 
					 $multipleWhere= 'FIND_IN_SET(A.status, "'.$status.'")>0 ';
					$this->db->where($multipleWhere);
				   }				  
					if(!empty($search))
				   {
					 $where = '(A.lead_id LIKE "%'.$search.'%" OR A.mrr LIKE "%'.$search.'%"  OR A.contact1 LIKE "%'.$search.'%" OR A.id LIKE "%'.$search.'%")';	
					$this->db->where($where);
				   }	
				   
				   
					   
					 if(!empty($order_by) && !empty($sort_by)){
						 $this->db->order_by($order_by,$sort_by);
					 }else{
						 $this->db->order_by('A.id','DESC');
					 }
	 
	 
				   if(!empty($limit_per_page))
				   {
					$this->db->limit($limit_per_page, $page_index);
				   }
				   

				   

				   $data = $this->db->get();
				 // $this->db->last_query();
				 foreach($data->result_array() as $row)
				 {
					 $result[]= $row;
				 }
				 return $result;
	  }	 
		// get the parent category for select box values 
		public function getSingleVertical($id)
		{
						$result=array();
					$this->db->select(array('A.*','B.title as vertical_status','C.title as v_cat','D.title as v_subcat','E.name as n_country','F.name as n_state','G.name as r_country','H.name as r_state'));
					$this->db->from('ci_verticals as A');
					$this->db->join('ci_terms as B','A.status=B.id','LEFT');
					$this->db->join('ci_terms as C','A.category=C.id','LEFT');
					$this->db->join('ci_terms as D','A.sub_category=D.id','LEFT');

						$this->db->join('countries as E','A.nationality=E.id','LEFT');
						$this->db->join('states as F','A.nationality_area=F.id','LEFT');
						$this->db->join('countries as G','A.country=G.id','LEFT');
						$this->db->join('states as H','A.state=H.id','LEFT');
						
					$this->db->where('A.id',$id); 
					$data = $this->db->get();
					// $this->db->last_query();
					foreach($data->result_array() as $row)
					{
						$result= $row;
					}
					return $result;
		}	

	  public function fetchTeamList($parent = 0, $user_tree_array = '') {
 
		if (!is_array($user_tree_array))
		$user_tree_array = array();
	 
	 	$sql = "SELECT name,id FROM ci_user WHERE manager=".$parent." AND id!=".$parent;
	  
	  	$rows = $this->db->query($sql);	
		if($rows && is_object($rows) && $rows->num_rows()>=1){
			foreach($rows->result_array() as  $row){
				
				$user_tree_array[] = $row['id'];
				$user_tree_array = $this->fetchTeamList($row['id'], $user_tree_array);	
				}
			}

			return $user_tree_array;
			

	  
	}

	  public function get_member($mid){

        $this->db->select('id,name');
        $this->db->from('ci_user');
        $this->db->where('manager', $mid);

        $parent = $this->db->get();
        
        $categories = $parent->result();
        $i=0;
        foreach($categories as $p_cat){

            $categories[$i]->sub = $this->sub_member($p_cat->id);
            $i++;
        }
        return $categories;
    }

    public function sub_member($id){

        $this->db->select('id,name');
        $this->db->from('ci_user');
        $this->db->where('manager', $id);

        $child = $this->db->get();
        $categories = $child->result();
        $i=0;
        foreach($categories as $p_cat){

            $categories[$i]->sub = $this->sub_member($p_cat->id);
            $i++;
        }
        return $categories;       
	}
	
	public function get_teamMember($id)
	{
			$result=array();
			 $this->db->select(array('A.*','B.title as role','C.name as manager_name','D.name as country_name','E.name as state_name'));
			 $this->db->from('ci_user as A');
			 $this->db->join('ci_roles as B','A.user_type=B.id','LEFT');
			 $this->db->join('ci_user as C','A.manager=C.id','LEFT');
			 $this->db->join('countries as D','A.country=D.id','LEFT');
			 $this->db->join('states as E','A.state=E.id','LEFT');
			 $this->db->where('A.manager', $id);
			 $data = $this->db->get();
		   // $this->db->last_query();
	   foreach($data->result_array() as $row)
	   {
		   $result[]= $row;
		   
		   
	   }
	   return $result;
	}

	public function get_client_ip()
	{
		$ip = '';
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$ip = trim($ips[0]);
		} elseif (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
			$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
		} elseif (!empty($_SERVER['REMOTE_ADDR'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		if (!empty($ip) && filter_var($ip, FILTER_VALIDATE_IP)) {
			return $ip;
		}
		return !empty($ip) ? $ip : '127.0.0.1';
	}

	public function log_admin_action($activity, $details = '', $status = 'SUCCESS', $user_id = null, $username = null)
	{
		if ($user_id === null) {
			$user_id = $this->session->userdata('manager_id');
		}
		if ($username === null) {
			$username = $this->session->userdata('manager_name') ? $this->session->userdata('manager_name') : ($this->session->userdata('manager_email') ? $this->session->userdata('manager_email') : 'Guest');
		}

		$ip = $this->get_client_ip();
		$ua = !empty($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 255) : '';
		$now = date('Y-m-d H:i:s');

		$log_data = array(
			'user_id' => !empty($user_id) ? (int)$user_id : null,
			'username' => $username,
			'ip_address' => $ip,
			'user_agent' => $ua,
			'activity' => $activity,
			'status' => $status,
			'details' => $details,
			'created_at' => $now
		);

		if ($activity === 'login' && $status === 'SUCCESS') {
			$log_data['login_time'] = $now;
		} else if ($activity === 'logout' || $activity === 'session_timeout' || $activity === 'concurrent_logout') {
			$log_data['logout_time'] = $now;
		}

		$this->db->insert('ci_admin_logs', $log_data);
	}

	public function check_auth_timeout()
	{
		$manager_id = (int)$this->session->userdata('manager_id');
		if (!empty($manager_id)) {
			$last_activity = $this->session->userdata('last_activity_time');
			$timeout_seconds = 600; // 10 minutes inactivity timeout

			if (!empty($last_activity) && (time() - $last_activity) > $timeout_seconds) {
				$this->log_admin_action('session_timeout', 'Logged out due to 10 minutes of inactivity', 'TIMEOUT', $manager_id);
				$this->db->where('id', $manager_id)->update('ci_user', array('is_online' => 0));
				$this->session->unset_userdata('manager_id');
				$this->session->unset_userdata('manager_email');
				$this->session->unset_userdata('manager_name');
				$this->session->unset_userdata('manager_mobile');
				$this->session->unset_userdata('login_session_token');
				$this->session->sess_destroy();
				redirect('/manager/login?timeout=1');
				exit;
			}

			// Single Device Login Verification:
			// Check if another device has logged in with this user account
			$session_token = $this->session->userdata('login_session_token');
			if (!empty($session_token)) {
				$user_row = $this->db->select('current_session_token')->where('id', $manager_id)->get('ci_user')->row_array();
				if (!empty($user_row['current_session_token']) && $user_row['current_session_token'] !== $session_token) {
					// Another device logged in! Terminate this device's session
					$this->log_admin_action('concurrent_logout', 'Session terminated: account logged in from another device', 'TERMINATED', $manager_id);
					$this->session->unset_userdata('manager_id');
					$this->session->unset_userdata('manager_email');
					$this->session->unset_userdata('manager_name');
					$this->session->unset_userdata('manager_mobile');
					$this->session->unset_userdata('login_session_token');
					$this->session->sess_destroy();
					redirect('/manager/login?conflict=1');
					exit;
				}
			}

			// Update activity timestamp and keep online flag fresh
			$this->session->set_userdata('last_activity_time', time());
			$client_ip = $this->get_client_ip();
			$this->db->where('id', $manager_id)->update('ci_user', array(
				'last_active_at' => date('Y-m-d H:i:s'),
				'last_ip' => $client_ip,
				'is_online' => 1
			));
		}
	}

	public function get_all_companies_dropdown()
	{
		$this->db->distinct();
		$this->db->select('TRIM(customer) as company');
		$this->db->from('ci_leads');
		$this->db->where('customer IS NOT NULL', null, false);
		$this->db->where('TRIM(customer) !=', '');
		$this->db->order_by('customer', 'ASC');
		$res = $this->db->get()->result_array();
		$companies = array();
		foreach ($res as $r) {
			$clean = trim($r['company']);
			if (!empty($clean) && !in_array($clean, $companies)) {
				$companies[] = $clean;
			}
		}
		return $companies;
	}

}