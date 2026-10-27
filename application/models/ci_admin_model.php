<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ci_admin_model extends CI_model {
	 function __construct() {
    parent::__construct();
	$this->load->database();
	
	}
	
	   public function dashboard_countdata(){
		$manager_id = (int)$this->session->userdata('manager_id');
		$data = array(
			'count_database' => 0,
			'count_verticals' => 0,
			'count_vetricals' => 0,
			'count_leads' => 0,
			'count_orders' => 0
		);

		$sql = "SELECT 
			(SELECT count(A.id) FROM ci_leads as A 
			 LEFT JOIN ci_pipeline as P ON A.id = P.lead_id 
			 WHERE (P.pipeline_stage = 'database' OR (P.pipeline_stage IS NULL AND (A.database_category != 'KAM Onboarded' OR A.database_category IS NULL))) 
			 AND (A.lead_assign = " . $manager_id . " OR (A.created_by = " . $manager_id . " AND (A.lead_assign = 1 OR A.lead_assign IS NULL)) OR (A.agents IS NOT NULL AND A.agents != '' AND FIND_IN_SET(" . $manager_id . ", A.agents) > 0)) 
			 AND (A.database_category != 'KAM Onboarded' OR A.database_category IS NULL)) as count_database,

			(SELECT count(id) FROM ci_verticals WHERE created_by = " . $manager_id . ") as count_verticals,

			(SELECT count(A.id) FROM ci_leads as A 
			 LEFT JOIN ci_pipeline as P ON A.id = P.lead_id 
			 WHERE P.pipeline_stage = 'lead' 
			 AND (A.lead_assign = " . $manager_id . " OR (A.created_by = " . $manager_id . " AND (A.lead_assign = 1 OR A.lead_assign IS NULL)) OR (A.agents IS NOT NULL AND A.agents != '' AND FIND_IN_SET(" . $manager_id . ", A.agents) > 0)) 
			 AND (A.database_category != 'KAM Onboarded' OR A.database_category IS NULL)) as count_leads,

			(SELECT count(id) FROM ci_orders WHERE created_by = " . $manager_id . ") as count_orders";

		$rows = $this->db->query($sql);
		if ($rows && is_object($rows) && $rows->num_rows() >= 1) {
			$data = $rows->row_array();
		}
		$data['count_vetricals'] = isset($data['count_verticals']) ? $data['count_verticals'] : 0;
		return $data;
	   }

		public function dashboard_Tcountdata(){
			$manager_id = (int)$this->session->userdata('manager_id');
			$team_array = $this->fetchTeamList($manager_id, '');
			if (!is_array($team_array)) {
				$team_array = array();
			}
			$team_clean = array_filter(array_map('intval', $team_array));
			$team_str = !empty($team_clean) ? implode(',', $team_clean) : '0';

			$data = array(
				'count_user' => 0,
				'count_verticals' => 0,
				'count_vetricals' => 0,
				'count_leads' => 0,
				'count_orders' => 0,
				'count_today_birth' => 0
			);

			$today = date('Y-m-d');
			$sql = "SELECT 
				(SELECT count(id) FROM ci_user WHERE FIND_IN_SET(id, '" . $team_str . "') > 0) as count_user,
				(SELECT count(id) FROM ci_user WHERE FIND_IN_SET(id, '" . $team_str . "') > 0 AND dob = '" . $today . "') as count_today_birth,
				(SELECT count(A.id) FROM ci_leads as A 
				 LEFT JOIN ci_pipeline as P ON A.id = P.lead_id 
				 WHERE P.pipeline_stage = 'lead' 
				 AND ((A.lead_assign > 0 AND FIND_IN_SET(A.lead_assign, '" . $team_str . "') > 0) OR ((A.lead_assign = 0 OR A.lead_assign IS NULL) AND FIND_IN_SET(A.created_by, '" . $team_str . "') > 0)) 
				 AND (A.company_status IS NULL OR LOWER(A.company_status) != 'closed')) as count_leads,
				(SELECT count(id) FROM ci_verticals WHERE FIND_IN_SET(created_by, '" . $team_str . "') > 0) as count_verticals,
				(SELECT count(id) FROM ci_orders WHERE FIND_IN_SET(created_by, '" . $team_str . "') > 0) as count_orders";

			$rows = $this->db->query($sql);
			if ($rows && is_object($rows) && $rows->num_rows() >= 1) {
				$data = $rows->row_array();
			}
			$data['count_vetricals'] = isset($data['count_verticals']) ? $data['count_verticals'] : 0;
			return $data;
		}
        public function dashboard_leaddata(){
			$manager_id = $this->session->userdata('manager_id');
			if (empty($manager_id)) { $manager_id = 0; }
			$data = array();
			$sql = "SELECT A.id as status_id, A.status, (SELECT COUNT(id) FROM ci_leads WHERE status = A.id AND (lead_assign = ".$manager_id." OR (created_by = ".$manager_id." AND (lead_assign = 0 OR lead_assign IS NULL)) OR (agents IS NOT NULL AND agents != '' AND FIND_IN_SET(".$manager_id.", agents) > 0)) AND (company_status IS NULL OR LOWER(company_status) != 'closed')) as lead_count FROM ci_status as A ORDER BY A.id ASC";
			$rows = $this->db->query($sql);	
			if($rows && is_object($rows) && $rows->num_rows()>=1){
				foreach($rows->result_array() as $row){
					$data[] = $row;	
				}
			}
			return $data; 
		}

		public function dashboard_leaddataT(){
			$manager_id = (int)$this->session->userdata('manager_id');
			$user_role = (int)$this->session->userdata('user_role');
			if ($user_role === 1 || $user_role === 4) {
				$sql = "SELECT A.id as status_id, A.status, (SELECT COUNT(id) FROM ci_leads WHERE status = A.id AND (company_status IS NULL OR LOWER(company_status) != 'closed')) as lead_count FROM ci_status as A ORDER BY A.id ASC";
			} else {
				$team_array = $this->fetchTeamList($manager_id, '');
				if (!is_array($team_array)) {
					$team_array = array();
				}
				$team_clean = array_filter(array_map('intval', $team_array));
				$team_str = !empty($team_clean) ? implode(',', $team_clean) : '0';
				$sql = "SELECT A.id as status_id, A.status, (SELECT COUNT(id) FROM ci_leads WHERE (FIND_IN_SET(created_by,'".$team_str."')>0 OR (lead_assign > 0 AND FIND_IN_SET(lead_assign,'".$team_str."')>0)) AND status = A.id AND (company_status IS NULL OR LOWER(company_status) != 'closed')) as lead_count FROM ci_status as A ORDER BY A.id ASC";
			}
			$data = array();
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

				$sql = "SELECT ( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.followup_date<'$today' AND a.status NOT IN (42,43,44) AND a.followup_date!='' and (a.lead_assign=".$manager_id." OR (a.created_by=".$manager_id." AND (a.lead_assign=0 OR a.lead_assign IS NULL)) OR (a.agents IS NOT NULL AND a.agents!='' AND FIND_IN_SET(".$manager_id.", a.agents)>0)) AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')) AS total_missed,
				( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.followup_date<'$today' AND a.status NOT IN (42,43,44) AND a.followup_date>='$last_date' AND a.followup_date!='' and (a.lead_assign=".$manager_id." OR (a.created_by=".$manager_id." AND (a.lead_assign=0 OR a.lead_assign IS NULL)) OR (a.agents IS NOT NULL AND a.agents!='' AND FIND_IN_SET(".$manager_id.", a.agents)>0)) AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')) AS total_lastweek,
				( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.followup_date='$today' AND a.status NOT IN (42,43,44) AND a.followup_date!='' and (a.lead_assign=".$manager_id." OR (a.created_by=".$manager_id." AND (a.lead_assign=0 OR a.lead_assign IS NULL)) OR (a.agents IS NOT NULL AND a.agents!='' AND FIND_IN_SET(".$manager_id.", a.agents)>0)) AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')) AS total_today,
				( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.followup_date>'$today' AND a.status NOT IN (42,43,44) AND a.followup_date!='' AND a.followup_date<='$next_date' and (a.lead_assign=".$manager_id." OR (a.created_by=".$manager_id." AND (a.lead_assign=0 OR a.lead_assign IS NULL)) OR (a.agents IS NOT NULL AND a.agents!='' AND FIND_IN_SET(".$manager_id.", a.agents)>0)) AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')) AS total_nextweek,
				( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.followup_date>'$today' AND a.status NOT IN (42,43,44) AND a.followup_date!='' and (a.lead_assign=".$manager_id." OR (a.created_by=".$manager_id." AND (a.lead_assign=0 OR a.lead_assign IS NULL)) OR (a.agents IS NOT NULL AND a.agents!='' AND FIND_IN_SET(".$manager_id.", a.agents)>0)) AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')) AS total_future"; 

			}else{

				$team_array= $this->fetchTeamList($this->session->userdata('manager_id'),'');
				$team_str= !empty($team_array) ? implode(',',$team_array) : '0';

				$sql = "SELECT ( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.followup_date<'$today' AND a.status NOT IN (42,43,44) AND a.followup_date!='' AND FIND_IN_SET(a.created_by,'".$team_str."')>0 AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')) AS total_missed,
				( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.followup_date<'$today' AND a.followup_date>='$last_date' AND a.status NOT IN (42,43,44) AND a.followup_date!='' and FIND_IN_SET(a.created_by,'".$team_str."')>0 AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')) AS total_lastweek,
				( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.followup_date='$today' AND a.followup_date!='' AND a.status NOT IN (42,43,44)  and FIND_IN_SET(a.created_by,'".$team_str."')>0 AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')) AS total_today,
				( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.followup_date>'$today'  AND a.status NOT IN (42,43,44)  AND a.followup_date!='' AND a.followup_date<='$next_date' and FIND_IN_SET(a.created_by,'".$team_str."')>0 AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')) AS total_nextweek,
				( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.followup_date>'$today'  AND a.status NOT IN (42,43,44)  AND a.followup_date!='' and FIND_IN_SET(a.created_by,'".$team_str."')>0 AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')) AS total_future"; 
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

			$sql = "SELECT ( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.m_date<'$today' AND a.status NOT IN (42,43,44)  AND a.m_date!='' and (a.lead_assign=".$manager_id." OR (a.created_by=".$manager_id." AND (a.lead_assign=0 OR a.lead_assign IS NULL)) OR (a.agents IS NOT NULL AND a.agents!='' AND FIND_IN_SET(".$manager_id.", a.agents)>0)) AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')) AS total_missed,
		( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.m_date<'$today' AND a.status NOT IN (42,43,44)  AND a.m_date>='$last_date' AND a.m_date!='' and (a.lead_assign=".$manager_id." OR (a.created_by=".$manager_id." AND (a.lead_assign=0 OR a.lead_assign IS NULL)) OR (a.agents IS NOT NULL AND a.agents!='' AND FIND_IN_SET(".$manager_id.", a.agents)>0)) AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')) AS total_lastweek,
		( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.m_date='$today' AND a.status NOT IN (42,43,44)  AND a.m_date!='' and (a.lead_assign=".$manager_id." OR (a.created_by=".$manager_id." AND (a.lead_assign=0 OR a.lead_assign IS NULL)) OR (a.agents IS NOT NULL AND a.agents!='' AND FIND_IN_SET(".$manager_id.", a.agents)>0)) AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')) AS total_today,
		( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.m_date>'$today' AND a.status NOT IN (42,43,44)  AND a.m_date!='' AND a.m_date<='$next_date' and (a.lead_assign=".$manager_id." OR (a.created_by=".$manager_id." AND (a.lead_assign=0 OR a.lead_assign IS NULL)) OR (a.agents IS NOT NULL AND a.agents!='' AND FIND_IN_SET(".$manager_id.", a.agents)>0)) AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')) AS total_nextweek,
		( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.m_date>'$today' AND a.status NOT IN (42,43,44)  AND a.m_date!='' and (a.lead_assign=".$manager_id." OR (a.created_by=".$manager_id." AND (a.lead_assign=0 OR a.lead_assign IS NULL)) OR (a.agents IS NOT NULL AND a.agents!='' AND FIND_IN_SET(".$manager_id.", a.agents)>0)) AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')) AS total_future"; 

		}else{

			$team_array= $this->fetchTeamList($this->session->userdata('manager_id'),'');
			$team_str= !empty($team_array) ? implode(',',$team_array) : '0';

			$sql = "SELECT ( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.m_date<'$today' AND a.status NOT IN (42,43,44)  AND a.m_date!='' and  FIND_IN_SET(a.created_by,'".$team_str."')>0 AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')) AS total_missed,
		( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.m_date<'$today' AND a.status NOT IN (42,43,44)  AND a.m_date>='$last_date' AND a.m_date!='' and  FIND_IN_SET(a.created_by,'".$team_str."')>0 AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')) AS total_lastweek,
		( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.m_date='$today' AND a.status NOT IN (42,43,44)  AND a.m_date!='' and  FIND_IN_SET(a.created_by,'".$team_str."')>0 AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')) AS total_today,
		( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.m_date>'$today' AND a.status NOT IN (42,43,44)  AND a.m_date!='' AND a.m_date<='$next_date' and  FIND_IN_SET(a.created_by,'".$team_str."')>0 AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')) AS total_nextweek,
		( SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.m_date>'$today' AND a.status NOT IN (42,43,44)  AND a.m_date!='' and  FIND_IN_SET(a.created_by,'".$team_str."')>0 AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')) AS total_future"; 
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
	$manager_id = (int)$this->session->userdata('manager_id');
	$user_role = (int)$this->session->userdata('manager_role');
	$team_array = $this->fetchTeamList($manager_id, '');
	if (!is_array($team_array)) {
		$team_array = array();
	}
	$team_clean = array_filter(array_map('intval', $team_array));
	$team_str = !empty($team_clean) ? implode(',', $team_clean) : '0';
			
	$loop = array();	
	$loop1 = array();
	$loop_labels = array();
	$month_dates = array();
	
	$select_parts = array("COUNT(a.id) as total");
	for ($i = 0; $i < 12; $i++) {
		$m_date = date("Y-m-01", strtotime("+$i month"));
		$loop[] = $m_date;
		$label = strtoupper(date("M y", strtotime("+$i month")));
		$loop1[] = $label;
		$loop_labels[] = $label;
		$d1 = date("Y-m-01", strtotime("+$i month"));
		$d2 = date("Y-m-t", strtotime("+$i month"));
		$month_dates[] = array('d1' => $d1, 'd2' => $d2);
		if ($user_role === 1 || $user_role === 4) {
			$select_parts[] = "(SELECT COALESCE(SUM(CAST(a.mrr AS DECIMAL(10,2))), 0) FROM ci_leads a INNER JOIN ci_pipeline P ON P.lead_id = a.id AND P.pipeline_stage = 'lead' WHERE a.close_date >= '$d1' AND a.close_date <= '$d2' AND a.close_date != '' AND a.close_date != '0000-00-00' AND a.status NOT IN (42,43,44) AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')) AS pt_".$i;
		} else {
			$select_parts[] = "(SELECT COALESCE(SUM(CAST(a.mrr AS DECIMAL(10,2))), 0) FROM ci_leads a INNER JOIN ci_pipeline P ON P.lead_id = a.id AND P.pipeline_stage = 'lead' WHERE a.close_date >= '$d1' AND a.close_date <= '$d2' AND a.close_date != '' AND a.close_date != '0000-00-00' AND a.status NOT IN (42,43,44) AND (FIND_IN_SET(a.created_by,'".$team_str."')>0 OR (a.lead_assign > 0 AND FIND_IN_SET(a.lead_assign,'".$team_str."')>0)) AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')) AS pt_".$i;
		}
	}

	if ($user_role === 1 || $user_role === 4) {
		$sql = "SELECT " . implode(", ", $select_parts) . " FROM ci_leads a INNER JOIN ci_pipeline P ON P.lead_id = a.id AND P.pipeline_stage = 'lead' WHERE a.status NOT IN (42,43,44) AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')"; 
	} else {
		$sql = "SELECT " . implode(", ", $select_parts) . " FROM ci_leads a INNER JOIN ci_pipeline P ON P.lead_id = a.id AND P.pipeline_stage = 'lead' WHERE a.status NOT IN (42,43,44) AND (FIND_IN_SET(a.created_by,'".$team_str."')>0 OR (a.lead_assign > 0 AND FIND_IN_SET(a.lead_assign,'".$team_str."')>0)) AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')"; 
	}
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

	$select_parts = array("COUNT(a.id) as total");
	for ($i = 0; $i < 12; $i++) {
		$m_date = date("Y-m-01", strtotime("+$i month"));
		$loop[] = $m_date;
		$label = strtoupper(date("M y", strtotime("+$i month")));
		$loop1[] = $label;
		$loop_labels[] = $label;
		$d1 = date("Y-m-01", strtotime("+$i month"));
		$d2 = date("Y-m-t", strtotime("+$i month"));
		$month_dates[] = array('d1' => $d1, 'd2' => $d2);
		$select_parts[] = "(SELECT COALESCE(SUM(CAST(a.mrr AS DECIMAL(10,2))), 0) FROM ci_leads a INNER JOIN ci_pipeline P ON P.lead_id = a.id AND P.pipeline_stage = 'lead' WHERE a.close_date >= '$d1' AND a.close_date <= '$d2' AND a.close_date != '' AND a.close_date != '0000-00-00' AND a.status NOT IN (42,43,44) AND (a.created_by = ".$user_id." OR a.lead_assign = ".$user_id.") AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')) AS pt_".$i;
	}

	$sql = "SELECT " . implode(", ", $select_parts) . " FROM ci_leads a INNER JOIN ci_pipeline P ON P.lead_id = a.id AND P.pipeline_stage = 'lead' WHERE a.status NOT IN (42,43,44) AND (a.created_by = ".$user_id." OR a.lead_assign = ".$user_id.") AND (a.company_status IS NULL OR LOWER(a.company_status) != 'closed')"; 
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
		$closed_cond = " AND (company_status IS NULL OR LOWER(company_status) != 'closed')";
		$sql = "SELECT 
			(SELECT COUNT(DISTINCT id) FROM ci_leads WHERE $effective_close < '$today' AND $effective_close IS NOT NULL AND $effective_close != '' AND $effective_close != '0000-00-00' AND status NOT IN (42,43,44) AND ".$user_cond.$closed_cond.") AS total_missed,
			(SELECT COUNT(DISTINCT id) FROM ci_leads WHERE $effective_close < '$today' AND $effective_close >= '$last_date' AND $effective_close IS NOT NULL AND $effective_close != '' AND $effective_close != '0000-00-00' AND status NOT IN (42,43,44) AND ".$user_cond.$closed_cond.") AS total_lastweek,
			(SELECT COUNT(DISTINCT id) FROM ci_leads WHERE $effective_close = '$today' AND status NOT IN (42,43,44) AND ".$user_cond.$closed_cond.") AS total_today,
			(SELECT COUNT(DISTINCT id) FROM ci_leads WHERE $effective_close > '$today' AND $effective_close <= '$next_date' AND $effective_close IS NOT NULL AND status NOT IN (42,43,44) AND ".$user_cond.$closed_cond.") AS total_nextweek,
			(SELECT COUNT(DISTINCT id) FROM ci_leads WHERE $effective_close > '$today' AND $effective_close IS NOT NULL AND status NOT IN (42,43,44) AND ".$user_cond.$closed_cond.") AS total_future,
			(SELECT COUNT(DISTINCT id) FROM ci_leads WHERE ".$user_cond.$closed_cond." AND status NOT IN (42,43,44) AND ($effective_close IS NOT NULL AND $effective_close != '' AND $effective_close != '0000-00-00')) AS total_all";
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
			$manager_id = $this->session->userdata('manager_id');
			$user_cond = "created_by=".$manager_id." AND status NOT IN (42,43,44) AND (vertical_id != '' AND vertical_id != '0' AND vertical_id IS NOT NULL) AND (company_status IS NULL OR LOWER(company_status) != 'closed')";
			$sql = "SELECT 
				(SELECT COUNT(DISTINCT id) FROM ci_leads WHERE followup_date < '$today' AND followup_date != '' AND followup_date != '0000-00-00' AND ".$user_cond.") AS total_missed,
				(SELECT COUNT(DISTINCT id) FROM ci_leads WHERE followup_date < '$today' AND followup_date >= '$last_date' AND followup_date != '' AND followup_date != '0000-00-00' AND ".$user_cond.") AS total_lastweek,
				(SELECT COUNT(DISTINCT id) FROM ci_leads WHERE followup_date = '$today' AND ".$user_cond.") AS total_today,
				(SELECT COUNT(DISTINCT id) FROM ci_leads WHERE followup_date > '$today' AND followup_date <= '$next_date' AND ".$user_cond.") AS total_nextweek,
				(SELECT COUNT(DISTINCT id) FROM ci_leads WHERE followup_date > '$today' AND ".$user_cond.") AS total_future,
				(SELECT COUNT(DISTINCT id) FROM ci_leads WHERE ".$user_cond." AND followup_date != '' AND followup_date != '0000-00-00') AS total_all";
		}else{
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

	public function dashboard_tasks($type){
		$manager_id = (int)$this->session->userdata('manager_id');
		$today = date('Y-m-d');
		$last_date = date('Y-m-d', strtotime("-7 day"));
		$next_date = date('Y-m-d', strtotime("+7 day"));
		$user_role = (int)$this->session->userdata('manager_role');

		if($type == 0){
			$user_cond = "(assigned_to = ".$manager_id." OR (assigned_users IS NOT NULL AND assigned_users != '' AND FIND_IN_SET('".$manager_id."', assigned_users) > 0))";
		}else{
			$not_me = "(assigned_to != ".$manager_id." AND (assigned_users IS NULL OR assigned_users = '' OR FIND_IN_SET('".$manager_id."', assigned_users) = 0))";
			if($user_role === 1 || $user_role === 4){
				$user_cond = "((assigned_to > 0 OR (assigned_users IS NOT NULL AND assigned_users != '')) AND ".$not_me.")";
			}else{
				$team_raw = $this->fetchTeamList($manager_id, '');
				if(!is_array($team_raw)){
					$team_raw = array();
				}
				$team_members = array();
				foreach($team_raw as $t_id){
					$t_id = (int)$t_id;
					if($t_id > 0 && $t_id !== $manager_id){
						$team_members[] = $t_id;
					}
				}
				$team_members = array_unique($team_members);
				if(empty($team_members)){
					$user_cond = "1 = 0";
				}else{
					$team_str = implode(',', $team_members);
					$or_team = array();
					$or_team[] = "assigned_to IN (".$team_str.")";
					foreach($team_members as $tm){
						$or_team[] = "FIND_IN_SET('".$tm."', assigned_users) > 0";
					}
					$user_cond = "((".implode(" OR ", $or_team).") AND ".$not_me.")";
				}
			}
		}

		$data = array(
			'total_missed' => 0,
			'total_lastweek' => 0,
			'total_today' => 0,
			'total_nextweek' => 0,
			'total_future' => 0,
			'total_all' => 0
		);

		$sql = "SELECT 
			(SELECT COUNT(DISTINCT id) FROM ci_tasks WHERE followup_date < '$today' AND followup_date != '' AND followup_date != '0000-00-00' AND followup_date IS NOT NULL AND status != 'Completed' AND status != 'Closed' AND ".$user_cond.") AS total_missed,
			(SELECT COUNT(DISTINCT id) FROM ci_tasks WHERE followup_date < '$today' AND followup_date >= '$last_date' AND followup_date != '' AND followup_date != '0000-00-00' AND followup_date IS NOT NULL AND status != 'Completed' AND status != 'Closed' AND ".$user_cond.") AS total_lastweek,
			(SELECT COUNT(DISTINCT id) FROM ci_tasks WHERE followup_date = '$today' AND status != 'Completed' AND status != 'Closed' AND ".$user_cond.") AS total_today,
			(SELECT COUNT(DISTINCT id) FROM ci_tasks WHERE followup_date > '$today' AND followup_date <= '$next_date' AND followup_date != '' AND followup_date != '0000-00-00' AND followup_date IS NOT NULL AND status != 'Completed' AND status != 'Closed' AND ".$user_cond.") AS total_nextweek,
			(SELECT COUNT(DISTINCT id) FROM ci_tasks WHERE followup_date > '$today' AND followup_date != '' AND followup_date != '0000-00-00' AND followup_date IS NOT NULL AND status != 'Completed' AND status != 'Closed' AND ".$user_cond.") AS total_future,
			(SELECT COUNT(DISTINCT id) FROM ci_tasks WHERE ".$user_cond.") AS total_all";

		$rows = $this->db->query($sql);
		if($rows && $rows->num_rows() >= 1){
			$data = $rows->row_array();
		}
		return $data;
	}

	public function dashboard_tickets($type){
		$manager_id = (int)$this->session->userdata('manager_id');
		$today = date('Y-m-d');
		$last_date = date('Y-m-d', strtotime("-7 day"));
		$next_date = date('Y-m-d', strtotime("+7 day"));
		$user_role = (int)$this->session->userdata('manager_role');

		if($type == 0){
			$user_cond = "(assigned_to = ".$manager_id." OR (assigned_users IS NOT NULL AND assigned_users != '' AND FIND_IN_SET('".$manager_id."', assigned_users) > 0))";
		}else{
			$not_me = "(assigned_to != ".$manager_id." AND (assigned_users IS NULL OR assigned_users = '' OR FIND_IN_SET('".$manager_id."', assigned_users) = 0))";
			if($user_role === 1 || $user_role === 4){
				$user_cond = "((assigned_to > 0 OR (assigned_users IS NOT NULL AND assigned_users != '')) AND ".$not_me.")";
			}else{
				$team_raw = $this->fetchTeamList($manager_id, '');
				if(!is_array($team_raw)){
					$team_raw = array();
				}
				$team_members = array();
				foreach($team_raw as $t_id){
					$t_id = (int)$t_id;
					if($t_id > 0 && $t_id !== $manager_id){
						$team_members[] = $t_id;
					}
				}
				$team_members = array_unique($team_members);
				if(empty($team_members)){
					$user_cond = "1 = 0";
				}else{
					$team_str = implode(',', $team_members);
					$or_team = array();
					$or_team[] = "assigned_to IN (".$team_str.")";
					foreach($team_members as $tm){
						$or_team[] = "FIND_IN_SET('".$tm."', assigned_users) > 0";
					}
					$user_cond = "((".implode(" OR ", $or_team).") AND ".$not_me.")";
				}
			}
		}

		$data = array(
			'total_missed' => 0,
			'total_lastweek' => 0,
			'total_today' => 0,
			'total_nextweek' => 0,
			'total_future' => 0,
			'total_all' => 0
		);

		$sql = "SELECT 
			(SELECT COUNT(DISTINCT id) FROM ci_tickets WHERE deadline_date < '$today' AND deadline_date != '' AND deadline_date != '0000-00-00' AND deadline_date IS NOT NULL AND status NOT IN ('Resolved', 'Closed') AND ".$user_cond.") AS total_missed,
			(SELECT COUNT(DISTINCT id) FROM ci_tickets WHERE deadline_date < '$today' AND deadline_date >= '$last_date' AND deadline_date != '' AND deadline_date != '0000-00-00' AND deadline_date IS NOT NULL AND status NOT IN ('Resolved', 'Closed') AND ".$user_cond.") AS total_lastweek,
			(SELECT COUNT(DISTINCT id) FROM ci_tickets WHERE deadline_date = '$today' AND status NOT IN ('Resolved', 'Closed') AND ".$user_cond.") AS total_today,
			(SELECT COUNT(DISTINCT id) FROM ci_tickets WHERE deadline_date > '$today' AND deadline_date <= '$next_date' AND deadline_date != '' AND deadline_date != '0000-00-00' AND deadline_date IS NOT NULL AND status NOT IN ('Resolved', 'Closed') AND ".$user_cond.") AS total_nextweek,
			(SELECT COUNT(DISTINCT id) FROM ci_tickets WHERE deadline_date > '$today' AND deadline_date != '' AND deadline_date != '0000-00-00' AND deadline_date IS NOT NULL AND status NOT IN ('Resolved', 'Closed') AND ".$user_cond.") AS total_future,
			(SELECT COUNT(DISTINCT id) FROM ci_tickets WHERE status NOT IN ('Resolved', 'Closed') AND ".$user_cond.") AS total_all";

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
			
		foreach($data->result_array() as $row)
		{
			$result[]= $row;
			
			
		}
		return $result;
	 }

	 
	 public function get_Myleads($d1='', $d2='', $mrr1='', $mrr2='', $limit_per_page='', $page_index='', $order_by='',$sort_by='', $search='', $status='', $category='', $lCat='',$user_name='',$country='',$state='', $closure='', $vertical_fu='', $stage='')
	 {			 
	 			$date1=date("Y-m-d",strtotime($d1) );
	 			$date2=date("Y-m-d",strtotime($d2) );
	   			$result=array();
			  $this->db->select(array('A.*','B.name as owner_name','C.status as lead_status','B.user_level','E.title as servicecat','P.pipeline_stage','P.vertical_code','P.conversion_status','P.conversion_reason','P.conversion_requested_by','P.conversion_requested_at','P.conversion_reviewed_by','P.conversion_reviewed_at','P.bo_remarks','P.vertical_data','ReqU.name as requested_by_name'));
			  $this->db->from('ci_leads as A');
			  $this->db->join('ci_pipeline as P','P.lead_id=A.id','LEFT');
			  $this->db->join('ci_user as B','A.created_by=B.id','LEFT');
			  $this->db->join('ci_user as ReqU','P.conversion_requested_by=ReqU.id','LEFT');
			  $this->db->join('ci_status as C','A.status=C.id','LEFT');
			  $this->db->join('ci_terms as E','A.s_cat=E.id','LEFT');
			 
			   if(!empty($closure)){
				   $this->db->where_not_in('A.status', array(42, 43, 44));
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
				   $this->db->where_not_in('A.status', array(42, 43, 44));
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
			   if ($stage === 'database') {
				   $this->db->where("(P.pipeline_stage = 'database' OR (P.pipeline_stage IS NULL AND (A.database_category != 'KAM Onboarded' OR A.database_category IS NULL)))");
			   } elseif ($stage === 'review_leads') {
				   $this->db->where("P.conversion_status", 'pending');
			   } elseif ($stage === 'lead') {
				   $this->db->where("P.pipeline_stage", 'lead');
			   } elseif ($stage === 'pending_approval') {
				   $this->db->where("P.conversion_status", 'pending');
			   } elseif ($stage === 'back_office') {
				   $this->db->where("P.pipeline_stage", 'back_office');
			   } elseif ($stage === 'kam') {
				   $this->db->where("(P.pipeline_stage = 'kam' OR A.database_category = 'KAM Onboarded')");
			   }

			   $uid = (int)$this->session->userdata('manager_id');
			   $urole = (int)$this->session->userdata('manager_role');
			   if ($urole == 2 || $urole == 5) {
				   if ($stage !== 'back_office') {
					   $this->db->where('A.id', -1);
				   }
			   } elseif ($stage === 'pending_approval') {
				   if ($urole !== 1 && $urole !== 4 && $uid !== 1) {
					   $team_members = $this->fetchTeamList($uid, '');
					   if (!is_array($team_members)) $team_members = array();
					   if (!empty($team_members)) {
						   $this->db->where_in('P.conversion_requested_by', $team_members);
					   } else {
						   $this->db->where('A.id', -1);
					   }
				   }
			   } elseif ($stage === 'review_leads') {
				   if ($urole !== 1 && $urole !== 4 && $uid !== 1) {
					   $team_members = $this->fetchTeamList($uid, '');
					   if (!is_array($team_members)) $team_members = array();
					   if (!empty($team_members)) {
						   $team_str = implode(',', array_map('intval', $team_members));
						   $this->db->where("(A.created_by IN ($team_str) OR A.lead_assign IN ($team_str))");
					   } else {
						   $this->db->where('A.id', -1);
					   }
				   }
			   } elseif ($stage === 'database' || $stage === 'lead') {
				   $where1 = "(A.lead_assign = $uid OR (A.created_by = $uid AND (A.lead_assign = 0 OR A.lead_assign IS NULL)) OR (A.agents IS NOT NULL AND A.agents != '' AND FIND_IN_SET($uid, A.agents) > 0))";	
				   $this->db->where($where1);
				   $this->db->where("(A.database_category != 'KAM Onboarded' OR A.database_category IS NULL)");
			   } else {
				   if ($urole !== 1 && $urole !== 4 && $uid !== 1) {
					   $where1 = "(A.lead_assign = $uid OR (A.created_by = $uid AND (A.lead_assign = 0 OR A.lead_assign IS NULL)) OR (A.agents IS NOT NULL AND A.agents != '' AND FIND_IN_SET($uid, A.agents) > 0))";	
					   $this->db->where($where1);
				   }
				   if ($stage !== 'kam') {
					   $this->db->where("(A.database_category != 'KAM Onboarded' OR A.database_category IS NULL)");
				   }
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
		
		foreach($data->result_array() as $row)
		{
			$result[]= $row;
			
			
		}
		return $result;
	 }	 
	 
	
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
			
		foreach($data->result_array() as $row)
		{
			$result[]= $row;
			
			
		}
		return $result;
	 }	


	 
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
			   $uid_fu = (int)$this->session->userdata('manager_id');
			   $urole_fu = (int)$this->session->userdata('manager_role');
			   if ($urole_fu == 2 || $urole_fu == 5) {
				   $this->db->where('A.id', -1);
			   } else {
				   $where1 = "(A.lead_assign = $uid_fu OR (A.created_by = $uid_fu AND (A.lead_assign = 0 OR A.lead_assign IS NULL)) OR (A.agents IS NOT NULL AND A.agents != '' AND FIND_IN_SET($uid_fu, A.agents) > 0))";	
				   $this->db->where($where1);
				   $this->db->where("(A.database_category != 'KAM Onboarded' OR A.database_category IS NULL)");
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
					$where.= " AND A.m_date between '$last_date' AND '$today' ";
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
			   $uid_mtg = (int)$this->session->userdata('manager_id');
			   $urole_mtg = (int)$this->session->userdata('manager_role');
			   if ($urole_mtg == 2 || $urole_mtg == 5) {
				   $this->db->where('A.id', -1);
			   } else {
				   $where1 = "(A.lead_assign = $uid_mtg OR (A.created_by = $uid_mtg AND (A.lead_assign = 0 OR A.lead_assign IS NULL)) OR (A.agents IS NOT NULL AND A.agents != '' AND FIND_IN_SET($uid_mtg, A.agents) > 0))";	
				   $this->db->where($where1);
				   $this->db->where("(A.database_category != 'KAM Onboarded' OR A.database_category IS NULL)");
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
			 
		foreach($data->result_array() as $row)
		{
			$result[]= $row;
			
			
		}
		return $result;
	 }	

	public function getAllocationLeads($d1='', $d2='', $mrr1='', $mrr2='', $limit_per_page='', $page_index='', $order_by='', $sort_by='', $search='', $status='', $category='', $lCat='', $user_name='', $country='', $state='', $alloc_filter='')
	{
		$result = array();
		$this->db->select(array('A.*', 'B.name as owner_name', 'U2.name as assigned_to_name', 'C.status as lead_status', 'B.user_level', 'E.title as servicecat'));
		$this->db->from('ci_leads as A');
		$this->db->join('ci_user as B', 'A.created_by=B.id', 'LEFT');
		$this->db->join('ci_user as U2', 'A.lead_assign=U2.id', 'LEFT');
		$this->db->join('ci_status as C', 'A.status=C.id', 'LEFT');
		$this->db->join('ci_terms as E', 'A.s_cat=E.id', 'LEFT');

		if (!empty($alloc_filter)) {
			if ($alloc_filter == 'non_allotted') {
				$this->db->where("(A.created_by = 0 OR A.created_by IS NULL OR A.created_by = '' OR A.database_category = 'Non-Allotted Database' OR (A.created_by = 1 AND (A.lead_assign = 0 OR A.lead_assign IS NULL)))");
			} elseif ($alloc_filter == 'allotted') {
				$this->db->where("((A.created_by > 1 OR (A.created_by = 1 AND A.lead_assign > 0)) AND (A.database_category != 'Non-Allotted Database' OR A.database_category IS NULL))");
			}
		}

		if (!empty($d1) && !empty($d2)) {
			$date1 = date("Y-m-d", strtotime($d1));
			$date2 = date("Y-m-d", strtotime($d2));
			$this->db->where('(A.created BETWEEN "'.$date1.'" AND "'.$date2.'")');
		}

		if ((!empty($mrr1) || ($mrr1 == '0')) && !empty($mrr2)) {
			$this->db->where('(A.mrr BETWEEN '.$mrr1.' AND '.$mrr2.')');
		}

		if (!empty($category)) {
			$this->db->where('FIND_IN_SET(A.s_cat, "'.$category.'")>0');
		}

		if (!empty($lCat)) {
			$this->db->where('FIND_IN_SET(A.lead_cat, "'.$lCat.'")>0');
		}

		if (!empty($user_name)) {
			$this->db->where('(B.name LIKE "%'.$user_name.'%" OR U2.name LIKE "%'.$user_name.'%")');
		}

		if (!empty($country)) {
			$this->db->where('FIND_IN_SET(A.country, "'.$country.'")>0');
		}

		if (!empty($state)) {
			$this->db->where('FIND_IN_SET(A.state, "'.$state.'")>0');
		}

		if (!empty($status)) {
			$this->db->where('FIND_IN_SET(A.status, "'.$status.'")>0');
		}

		if (!empty($search)) {
			$where = '(A.lead_id LIKE "%'.$search.'%" OR A.customer LIKE "%'.$search.'%" OR A.mrr LIKE "%'.$search.'%" OR A.contact1 LIKE "%'.$search.'%" OR A.id LIKE "%'.$search.'%")';
			$this->db->where($where);
		}

		if (!empty($order_by) && !empty($sort_by)) {
			$this->db->order_by($order_by, $sort_by);
		} else {
			$this->db->order_by('A.id', 'DESC');
		}

		if (!empty($limit_per_page)) {
			$this->db->limit($limit_per_page, $page_index);
		}

		$data = $this->db->get();
		foreach ($data->result_array() as $row) {
			$result[] = $row;
		}
		return $result;
	}

	  public function getTeamLeads($d1='', $d2='', $mrr1='', $mrr2='', $limit_per_page='', $page_index='', $order_by='',$sort_by='', $search='', $status='', $category='', $lCat='',$user_name='',$country='',$state='', $closure='', $stage='lead')
	  {			 
		$manager_id = (int)$this->session->userdata('manager_id');
		$user_role = (int)$this->session->userdata('user_role');
		$team_array = $this->fetchTeamList($manager_id, '');
		if (!is_array($team_array)) {
			$team_array = array();
		}
		$team_clean = array_unique(array_filter(array_map('intval', $team_array)));
		$team_str = !empty($team_clean) ? implode(',', $team_clean) : '';

				  $date1=date("Y-m-d",strtotime($d1) );
				  $date2=date("Y-m-d",strtotime($d2) );
					$result=array();
			   $this->db->select(array('A.*','B.name as owner_name','C.status as lead_status','B.user_level','E.title as servicecat', 'P.db_ref_no', 'P.lead_ref_no', 'P.pipeline_stage', 'P.order_request_status', 'P.is_locked'));
			   $this->db->from('ci_leads as A');
			   $this->db->join('ci_user as B','A.created_by=B.id','LEFT');
			   $this->db->join('ci_status as C','A.status=C.id','LEFT');
			   $this->db->join('ci_terms as E','A.s_cat=E.id','LEFT');
			   $this->db->join('ci_pipeline as P','A.id=P.lead_id','LEFT');

			   if ($stage === 'database') {
				   $this->db->where("(P.pipeline_stage = 'database' OR P.pipeline_stage IS NULL)");
			   } elseif ($stage === 'lead') {
				   $this->db->where("P.pipeline_stage", 'lead');
			   }
			  
			   if(!empty($closure)){
				   $this->db->where_not_in('A.status', array(42, 43, 44));
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
			   $team_clean = array_unique(array_filter(array_map('intval', $team_array)));
			   if (!empty($team_clean)) {
				   $team_str = implode(',', $team_clean);
				   $where1 = "((A.lead_assign > 0 AND FIND_IN_SET(A.lead_assign, '{$team_str}') > 0) OR ((A.lead_assign = 0 OR A.lead_assign IS NULL) AND FIND_IN_SET(A.created_by, '{$team_str}') > 0))";
			   } else {
				   $where1 = "A.id = -1";
			   }
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
			
		 foreach($data->result_array() as $row)
		 {
			 $result[]= $row;
			 
			 
		 }
		 return $result;
	  }	
	  
	  
	  
	  
	  public function getTeamAssignLeads($d1='', $d2='', $mrr1='', $mrr2='', $limit_per_page='', $page_index='', $order_by='',$sort_by='', $search='', $status='', $category='', $lCat='',$user_name='',$country='',$state='', $stage='')
	  {			 
				  $date1=date("Y-m-d",strtotime($d1) );
				  $date2=date("Y-m-d",strtotime($d2) );
					$result=array();
			   $this->db->select(array('A.*','B.name as owner_name','U.name as assigned_to_name','C.status as lead_status','B.user_level','E.title as servicecat','P.pipeline_stage','P.vertical_code','P.conversion_status'));
			   $this->db->from('ci_leads as A');
			   $this->db->join('ci_pipeline as P','P.lead_id=A.id','LEFT');
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

			   if ($stage === 'lead') {
				   $this->db->where("P.pipeline_stage", 'lead');
			   } elseif ($stage === 'database') {
				   $this->db->where("(P.pipeline_stage = 'database' OR (P.pipeline_stage IS NULL AND (A.database_category != 'KAM Onboarded' OR A.database_category IS NULL)))");
			   } elseif ($stage === 'review_leads' || $stage === 'pending_approval') {
				   $this->db->where("P.conversion_status", 'pending');
			   } elseif ($stage === 'back_office') {
				   $this->db->where("P.pipeline_stage", 'back_office');
			   } elseif ($stage === 'kam') {
				   $this->db->where("(P.pipeline_stage = 'kam' OR A.database_category = 'KAM Onboarded')");
			   }
			  
				$cur_role = (int)$this->session->userdata('manager_role');
				$cur_id = (int)$this->session->userdata('manager_id');
				if ($cur_role === 1 || $cur_role === 4) {
					if (isset($_GET['assign_scope']) && $_GET['assign_scope'] === 'unassigned') {
						$this->db->where('(A.lead_assign = 0 OR A.lead_assign IS NULL)');
					} else if (isset($_GET['assign_scope']) && is_numeric($_GET['assign_scope']) && (int)$_GET['assign_scope'] > 0) {
						$target_user_id = (int)$_GET['assign_scope'];
						$this->db->where('(A.lead_assign = ' . $target_user_id . ' OR FIND_IN_SET(' . $target_user_id . ', A.agents) > 0)');
					}
					$where1 = "A.status NOT IN (42,43,44)";
					$this->db->where($where1);
				} else {
					$team_ids = $this->fetchTeamList($cur_id, '');
					if (!is_array($team_ids)) {
						$team_ids = array();
					}
					if (!in_array($cur_id, $team_ids)) {
						$team_ids[] = $cur_id;
					}
					$team_str = implode(',', array_filter(array_map('intval', $team_ids)));
					if (empty($team_str)) {
						$team_str = (string)$cur_id;
					}
					$where1 = "A.status NOT IN (42,43,44) AND (FIND_IN_SET(A.created_by, '{$team_str}') > 0 OR FIND_IN_SET(A.lead_assign, '{$team_str}') > 0)";
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
			
		 foreach($data->result_array() as $row)
		 {
			 $result[]= $row;
			 
			 
		 }
		 return $result;
	  }	 
 



	  
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
			   
			   $where1="A.status NOT IN (42,43,44) AND ((A.lead_assign > 0 AND FIND_IN_SET(A.lead_assign,'".$team_str."')>0) OR ((A.lead_assign = 0 OR A.lead_assign IS NULL) AND FIND_IN_SET(A.created_by,'".$team_str."')>0) OR (A.agents IS NOT NULL AND A.agents != '' AND FIND_IN_SET('".(int)$this->session->userdata('manager_id')."', A.agents)>0))";	
				$this->db->where($where1);
				$this->db->where("(A.database_category != 'KAM Onboarded' OR A.database_category IS NULL)");
			  
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
					
					$this->db->where('B.name',$user_name);
					}				  

					if(!empty($country))
					{
					$clean_country = $this->db->escape_str($country);
					$multipleWhere= 'FIND_IN_SET(A.country, "'.$clean_country.'")>0 ';
					$this->db->where($multipleWhere);
					}	

					if(!empty($state))
					{
					$clean_state = $this->db->escape_str($state);
					$multipleWhere= 'FIND_IN_SET(A.state, "'.$clean_state.'")>0 ';
					$this->db->where($multipleWhere);
					}				
 
					if(!empty($category))
				   {
				   $clean_category = $this->db->escape_str($category);
				   $multipleWhere= 'FIND_IN_SET(A.s_cat, "'.$clean_category.'")>0 ';
					$this->db->where($multipleWhere);
				   }	
				   
				    if(!empty($lCat))
			  {
			  $clean_lCat = $this->db->escape_str($lCat);
			  $multipleWhere= 'FIND_IN_SET(A.lead_cat, "'.$clean_lCat.'")>0 ';
               $this->db->where($multipleWhere);
			  }	
			  			  
					if(!empty($status))
				   { 
					 $clean_status = $this->db->escape_str($status);
					 $multipleWhere= 'FIND_IN_SET(A.status, "'.$clean_status.'")>0 ';
					$this->db->where($multipleWhere);
				   }				  
					if(!empty($search))
				   {
					 $clean_search = $this->db->escape_str($this->db->escape_like_str($search));
					 $where = '(A.lead_id LIKE "%'.$clean_search.'%" OR A.mrr LIKE "%'.$clean_search.'%"  OR A.contact1 LIKE "%'.$clean_search.'%" OR A.id LIKE "%'.$clean_search.'%")';	
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
		
		foreach($data->result_array() as $row)
		{
			$result[]= $row;
			
			
		}
		return $result;
	 }

	
	 public function getSingleLead($id)
	 {
			  $result=array();
			  $this->db->select(array('A.*','B.user_level','B.name as created_name','D.name as country_name','E.name as state_name','F.title as pcat_name','G.title as psubcat_name','H.name as assigned_name','P.pipeline_stage','P.vertical_code','P.conversion_status','P.conversion_reason','P.conversion_requested_by','P.conversion_requested_at','P.conversion_reviewed_by','P.conversion_reviewed_at','P.bo_remarks','P.vertical_data','P.bo_status','P.work_order_no','ReqU.name as requested_by_name'));
			  $this->db->from('ci_leads as A');
			  $this->db->join('ci_pipeline as P','P.lead_id=A.id','LEFT');
			  $this->db->join('ci_user as B','A.created_by=B.id','LEFT');
			  $this->db->join('ci_user as ReqU','P.conversion_requested_by=ReqU.id','LEFT');
			  $this->db->join('countries as D','A.country=D.id','LEFT');
			  $this->db->join('states as E','A.state=E.id','LEFT');
			  $this->db->join('ci_terms as F','A.s_cat=F.id','LEFT');
			  $this->db->join('ci_terms as G','A.p_cat=G.id','LEFT');
			  $this->db->join('ci_user as H','A.lead_assign=H.id','LEFT');
			 $this->db->where('A.id',$id);
			
			  $this->db->limit(1);
			  $data = $this->db->get();
				foreach($data->result_array() as $row)
				{
					$result= $row;
				}
				return $result;
	 }

	 public function can_approve_conversion($requester_id, $approver_id = null)
	 {
		if ($approver_id === null) {
			$approver_id = (int)$this->session->userdata('manager_id');
		}
		$approver_role = (int)$this->session->userdata('manager_role');
		if ($approver_role === 1 || $approver_role === 4) {
			return true;
		}
		if ((int)$requester_id === (int)$approver_id) {
			return false;
		}
		$team_members = $this->fetchTeamList($approver_id, '');
		if (is_array($team_members) && in_array((int)$requester_id, array_map('intval', $team_members))) {
			return true;
		}
		return false;
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

	  

	  public function getShortVerticals($id)
	  {
					$result=array();
				   $this->db->select(array('A.id','A.poc','A.company_name'));
				   $this->db->from('ci_verticals as A');
				   $this->db->where('A.created_by',$id);			
				   $data = $this->db->get();
				 
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
					 $where = '(A.created BETWEEN '.$this->db->escape(db_date($d1)).' AND '.$this->db->escape(db_date($d2)).')';	
					$this->db->where($where);
				   }	
					if(!empty($category))
				   {
				   $clean_cat = preg_replace('/[^0-9,]/', '', $category);
				   $multipleWhere= 'FIND_IN_SET(A.category, "'.$clean_cat.'")>0 ';
					$this->db->where($multipleWhere);
				   }				  
					if(!empty($status))
				   { 
				     $clean_st = preg_replace('/[^0-9,]/', '', $status);
					 $multipleWhere= 'FIND_IN_SET(A.status, "'.$clean_st.'")>0 ';
					$this->db->where($multipleWhere);
				   }				  
					if(!empty($search))
				   {
					 $escaped_s = $this->db->escape_like_str($search);
					 $where = '(A.lead_id LIKE "%'.$escaped_s.'%" OR A.mrr LIKE "%'.$escaped_s.'%"  OR A.contact1 LIKE "%'.$escaped_s.'%" OR A.id LIKE "%'.$escaped_s.'%")';	
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
				 
				 foreach($data->result_array() as $row)
				 {
					 $result[]= $row;
				 }
				 return $result;
	  }	 
		
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
	   foreach($data->result_array() as $row)
	   {
		   $result[]= $row;
	   }
	   return $result;
	}

	public function fetchTeamHierarchyTree($root_id, $visited = array())
	{
		if (in_array($root_id, $visited)) {
			return array();
		}
		$visited[] = $root_id;

		$this->db->select(array('A.id', 'A.name', 'A.email', 'A.mobile', 'A.office', 'A.department', 'A.manager', 'A.user_type', 'A.status', 'B.title as role', 'C.name as manager_name'));
		$this->db->from('ci_user as A');
		$this->db->join('ci_roles as B', 'A.user_type=B.id', 'LEFT');
		$this->db->join('ci_user as C', 'A.manager=C.id', 'LEFT');
		$this->db->where('A.manager', $root_id);
		$this->db->where('A.id !=', $root_id);
		$this->db->order_by('A.name', 'ASC');
		$rows = $this->db->get()->result_array();

		$tree = array();
		foreach ($rows as $row) {
			$children = $this->fetchTeamHierarchyTree((int)$row['id'], $visited);
			$row['children'] = $children;
			$row['has_children'] = !empty($children);
			$row['child_count'] = count($children);
			$tree[] = $row;
		}
		return $tree;
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

	public function log_admin_action($activity, $details = '', $status = 'SUCCESS', $user_id = null, $username = null, $lat = null, $lng = null, $accuracy = null, $loc_status = null)
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

		if ($lat === null && isset($_POST['latitude']) && $_POST['latitude'] !== '') {
			$lat = trim($_POST['latitude']);
		}
		if ($lng === null && isset($_POST['longitude']) && $_POST['longitude'] !== '') {
			$lng = trim($_POST['longitude']);
		}
		if ($accuracy === null && isset($_POST['accuracy']) && $_POST['accuracy'] !== '') {
			$accuracy = (float)$_POST['accuracy'];
		}
		if ($loc_status === null && isset($_POST['location_status']) && $_POST['location_status'] !== '') {
			$loc_status = trim($_POST['location_status']);
		}

		if ($lat === null && $this->session->userdata('user_last_latitude')) {
			$lat = $this->session->userdata('user_last_latitude');
		}
		if ($lng === null && $this->session->userdata('user_last_longitude')) {
			$lng = $this->session->userdata('user_last_longitude');
		}

		$log_data = array(
			'user_id' => !empty($user_id) ? (int)$user_id : null,
			'username' => $username,
			'ip_address' => $ip,
			'user_agent' => $ua,
			'activity' => $activity,
			'status' => $status,
			'details' => $details,
			'created_at' => $now,
			'latitude' => (!empty($lat) && is_numeric($lat)) ? $lat : null,
			'longitude' => (!empty($lng) && is_numeric($lng)) ? $lng : null,
			'accuracy' => (!empty($accuracy) && is_numeric($accuracy)) ? (float)$accuracy : null,
			'location_status' => !empty($loc_status) ? substr($loc_status, 0, 50) : null
		);

		if ($activity === 'login' && $status === 'SUCCESS') {
			$log_data['login_time'] = $now;
			if (!empty($lat) && !empty($lng)) {
				$this->session->set_userdata('user_last_latitude', $lat);
				$this->session->set_userdata('user_last_longitude', $lng);
			}
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
			$timeout_seconds = 600; 

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

			
			
			$session_token = $this->session->userdata('login_session_token');
			if (!empty($session_token)) {
				$user_row = $this->db->select('current_session_token')->where('id', $manager_id)->get('ci_user')->row_array();
				if (!empty($user_row['current_session_token']) && $user_row['current_session_token'] !== $session_token) {
					
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
			if (strpos($clean, "\t") !== false || strpos($clean, "\n") !== false) {
				$parts = preg_split('/[\t\r\n]/', $clean);
				$clean = trim($parts[0]);
			}
			if (!empty($clean) && !in_array($clean, $companies)) {
				$companies[] = $clean;
			}
		}
		return $companies;
	}

	public function search_companies($term = '')
	{
		$term = trim($term);
		if (strlen($term) < 3) {
			return array();
		}
		$escaped = $this->db->escape_like_str($term);
		$this->db->distinct();
		$this->db->select('TRIM(customer) as company');
		$this->db->from('ci_leads');
		$this->db->where('customer IS NOT NULL', null, false);
		$this->db->where('TRIM(customer) !=', '');
		$this->db->like('customer', $escaped);
		$this->db->order_by('customer', 'ASC');
		$this->db->limit(40);
		$res = $this->db->get()->result_array();
		$companies = array();
		foreach ($res as $r) {
			$clean = trim($r['company']);
			if (strpos($clean, "\t") !== false || strpos($clean, "\n") !== false) {
				$parts = preg_split('/[\t\r\n]/', $clean);
				$clean = trim($parts[0]);
			}
			if (!empty($clean) && !in_array($clean, $companies)) {
				$companies[] = $clean;
			}
		}
		return $companies;
	}

	public function getMyTransferLeads($user_id, $d1='', $d2='', $mrr1='', $mrr2='', $limit_per_page='', $page_index='', $order_by='', $sort_by='', $search='', $status='', $category='', $lCat='')
	{
		$result = array();
		$this->db->select(array('A.*', 'B.name as owner_name', 'C.status as lead_status', 'B.user_level', 'E.title as servicecat'));
		$this->db->from('ci_leads as A');
		$this->db->join('ci_user as B', 'A.created_by=B.id', 'LEFT');
		$this->db->join('ci_status as C', 'A.status=C.id', 'LEFT');
		$this->db->join('ci_terms as E', 'A.s_cat=E.id', 'LEFT');

		$this->db->where("(A.lead_assign = $user_id OR (A.created_by = $user_id AND (A.lead_assign = 0 OR A.lead_assign IS NULL)))");
		$this->db->where('(A.company_status IS NULL OR LOWER(A.company_status) != "closed")');

		if (!empty($d1) && !empty($d2)) {
			$date1 = date("Y-m-d", strtotime($d1));
			$date2 = date("Y-m-d", strtotime($d2));
			$this->db->where('(A.created BETWEEN "'.$date1.'" AND "'.$date2.'")');
		}

		if ((!empty($mrr1) || ($mrr1 == '0')) && !empty($mrr2)) {
			$this->db->where('(A.mrr BETWEEN '.$mrr1.' AND '.$mrr2.')');
		}

		if (!empty($category)) {
			$this->db->where('FIND_IN_SET(A.s_cat, "'.$category.'")>0');
		}

		if (!empty($lCat)) {
			$this->db->where('FIND_IN_SET(A.lead_cat, "'.$lCat.'")>0');
		}

		if (!empty($status)) {
			$this->db->where('FIND_IN_SET(A.status, "'.$status.'")>0');
		}

		if (!empty($search)) {
			$where = '(A.lead_id LIKE "%'.$search.'%" OR A.customer LIKE "%'.$search.'%" OR A.mrr LIKE "%'.$search.'%" OR A.contact1 LIKE "%'.$search.'%" OR A.id LIKE "%'.$search.'%")';
			$this->db->where($where);
		}

		if (!empty($order_by) && !empty($sort_by)) {
			$this->db->order_by($order_by, $sort_by);
		} else {
			$this->db->order_by('A.id', 'DESC');
		}

		if (!empty($limit_per_page)) {
			$this->db->limit($limit_per_page, $page_index);
		}

		$data = $this->db->get();
		foreach ($data->result_array() as $row) {
			$result[] = $row;
		}
		return $result;
	}

	public function getMeetings()
	{
		$this->db->select('*');
		$this->db->from('ci_meetings');
		$this->db->order_by('id', 'DESC');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_role_permissions($role_id)
	{
		$role_id = (int)$role_id;
		$sections = array(
			'dashboard', 'my_zone', 'database', 'leads', 'lead_review',
			'orders', 'calls', 'team', 'kam', 'tasks', 'tickets',
			'internal_tasks', 'external_tasks', 'reports', 'events',
			'awards', 'integrations', 'system', 'users', 'roles'
		);

		if ($role_id === 1 || $role_id === 2) {
			$res = array();
			foreach ($sections as $s) {
				$res[$s] = 'write';
			}
			return $res;
		}

		$role_row = $this->db->select('permissions')->get_where('ci_roles', array('id' => $role_id))->row_array();
		if (!empty($role_row['permissions'])) {
			$decoded = json_decode($role_row['permissions'], true);
			if (is_array($decoded)) {
				return $decoded;
			}
		}

		$res = array();
		foreach ($sections as $s) {
			$res[$s] = 'read';
		}
		return $res;
	}

	public function check_permission($section, $required = 'read', $redirect = false)
	{
		$role = (int)$this->session->userdata('manager_role');
		$user_id = (int)$this->session->userdata('manager_id');
		if ($role === 1 || $role === 2 || $user_id === 1) {
			return true;
		}

		$perms = $this->get_role_permissions($role);
		$user_perm = isset($perms[$section]) ? $perms[$section] : 'no_access';

		$allowed = false;
		if ($required === 'read') {
			if ($user_perm === 'read' || $user_perm === 'write') {
				$allowed = true;
			}
		} elseif ($required === 'write') {
			if ($user_perm === 'write') {
				$allowed = true;
			}
		}

		if (!$allowed && $redirect) {
			if ($this->input->is_ajax_request()) {
				header('Content-Type: application/json');
				echo json_encode(array('status' => 'error', 'message' => 'Permission denied. You do not have ' . $required . ' access for this section.'));
				exit;
			} else {
				$this->session->set_flashdata('error_message', 'Permission denied. You do not have ' . $required . ' access for ' . ucwords(str_replace('_', ' ', $section)) . '.');
				redirect('manager/dashboard');
			}
		}

		return $allowed;
	}

	public function get_user_owned_records_count($user_id)
	{
		$user_id = (int)$user_id;
		if ($user_id <= 0) {
			return array('leads' => 0, 'kam' => 0, 'tasks' => 0, 'total' => 0);
		}

		$this->db->where("(lead_assign = $user_id OR (created_by = $user_id AND (lead_assign = 0 OR lead_assign IS NULL)))");
		$leads_count = $this->db->count_all_results('ci_leads');

		$this->db->where('executive_id', $user_id);
		$kam_count = $this->db->count_all_results('ci_kam_client_details');

		$this->db->where('assigned_to', $user_id);
		$this->db->where_not_in('status', array('Completed', 'Closed'));
		$tasks_count = $this->db->count_all_results('ci_tasks');

		$total = $leads_count + $kam_count + $tasks_count;
		return array(
			'leads' => $leads_count,
			'kam' => $kam_count,
			'tasks' => $tasks_count,
			'total' => $total
		);
	}

	public function dashboard_pipeline_summary() {
		$manager_id = (int)$this->session->userdata('manager_id');
		$role = (int)$this->session->userdata('manager_role');
		$is_admin = ($role === 1 || $role === 4 || $manager_id === 1);

		$team_array = $this->fetchTeamList($manager_id, '');
		if (!is_array($team_array)) {
			$team_array = array();
		}
		$team_array[] = $manager_id;
		$team_array = array_unique(array_filter(array_map('intval', $team_array)));
		$team_str = implode(',', $team_array);

		$db_scope = '';
		$lead_scope = '';
		$order_scope = '';
		if (!$is_admin) {
			$db_scope = " AND (L.lead_assign = {$manager_id} OR (L.created_by = {$manager_id} AND (L.lead_assign = 0 OR L.lead_assign IS NULL)) OR (L.agents IS NOT NULL AND L.agents != '' AND FIND_IN_SET({$manager_id}, L.agents) > 0) OR FIND_IN_SET(L.created_by, '{$team_str}') > 0 OR FIND_IN_SET(L.lead_assign, '{$team_str}') > 0)";
			$lead_scope = " AND (L.lead_assign = {$manager_id} OR (L.created_by = {$manager_id} AND (L.lead_assign = 0 OR L.lead_assign IS NULL)) OR (L.agents IS NOT NULL AND L.agents != '' AND FIND_IN_SET({$manager_id}, L.agents) > 0) OR FIND_IN_SET(L.created_by, '{$team_str}') > 0 OR FIND_IN_SET(L.lead_assign, '{$team_str}') > 0)";
			$order_scope = " AND (O.created_by = {$manager_id} OR O.assigned_to = {$manager_id} OR FIND_IN_SET(O.created_by, '{$team_str}') > 0 OR FIND_IN_SET(O.assigned_to, '{$team_str}') > 0)";
		}

		$db_q = $this->db->query("SELECT COUNT(L.id) as cnt, COALESCE(SUM(CAST(L.mrr AS DECIMAL(10,2))), 0) as val FROM ci_leads L INNER JOIN ci_pipeline P ON P.lead_id = L.id WHERE P.pipeline_stage = 'database' AND (L.company_status IS NULL OR LOWER(L.company_status) != 'closed') {$db_scope}")->row_array();
		$lead_q = $this->db->query("SELECT COUNT(L.id) as cnt, COALESCE(SUM(CAST(L.mrr AS DECIMAL(10,2))), 0) as val FROM ci_leads L INNER JOIN ci_pipeline P ON P.lead_id = L.id WHERE P.pipeline_stage = 'lead' AND (L.company_status IS NULL OR LOWER(L.company_status) != 'closed') {$lead_scope}")->row_array();
		$order_q = $this->db->query("SELECT COUNT(O.id) as cnt, COALESCE(SUM(CAST(O.pricing_amount AS DECIMAL(10,2))), 0) as val FROM ci_orders O WHERE O.status != 'Sales' AND O.status != 'Cancelled' {$order_scope}")->row_array();
		$sales_q = $this->db->query("SELECT COUNT(O.id) as cnt, COALESCE(SUM(CAST(O.pricing_amount AS DECIMAL(10,2))), 0) as val FROM ci_orders O WHERE O.status = 'Sales' {$order_scope}")->row_array();

		return array(
			'database' => array('count' => (int)$db_q['cnt'], 'value' => (float)$db_q['val']),
			'leads'    => array('count' => (int)$lead_q['cnt'], 'value' => (float)$lead_q['val']),
			'orders'   => array('count' => (int)$order_q['cnt'], 'value' => (float)$order_q['val']),
			'sales'    => array('count' => (int)$sales_q['cnt'], 'value' => (float)$sales_q['val']),
		);
	}

	public function generate_next_ref($type = 'database')
	{
		$prefix_map = array(
			'database' => array('table' => 'ci_pipeline', 'col' => 'db_ref_no', 'prefix' => 'D'),
			'lead' => array('table' => 'ci_pipeline', 'col' => 'lead_ref_no', 'prefix' => 'L'),
			'order' => array('table' => 'ci_orders', 'col' => 'order_no', 'prefix' => 'O'),
			'sales' => array('table' => 'ci_orders', 'col' => 'sales_ref_no', 'prefix' => 'S')
		);
		if (!isset($prefix_map[$type])) {
			$type = 'database';
		}
		$conf = $prefix_map[$type];
		$table = $conf['table'];
		$col = $conf['col'];
		$prefix = $conf['prefix'];

		$max_num = 0;
		if ($type === 'database') {
			$q1 = $this->db->query("SELECT MAX(CAST(SUBSTRING(db_ref_no, 2) AS UNSIGNED)) as max_num FROM ci_pipeline WHERE db_ref_no REGEXP '^D[0-9]{10}$'")->row_array();
			$d1 = !empty($q1['max_num']) ? (int)$q1['max_num'] : 0;
			$q2 = $this->db->query("SELECT MAX(CAST(SUBSTRING(db_ref_no, 9) AS UNSIGNED)) as max_num FROM ci_pipeline WHERE db_ref_no REGEXP '^DB-10000[0-9]+$'")->row_array();
			$d2 = !empty($q2['max_num']) ? (int)$q2['max_num'] : 0;
			$max_num = max($d1, $d2);
		} elseif ($type === 'lead') {
			$q = $this->db->query("SELECT MAX(CAST(SUBSTRING(lead_ref_no, 2) AS UNSIGNED)) as max_num FROM ci_pipeline WHERE lead_ref_no REGEXP '^L[0-9]{10}$'")->row_array();
			$max_num = !empty($q['max_num']) ? (int)$q['max_num'] : 0;
		} elseif ($type === 'order') {
			$q = $this->db->query("SELECT MAX(CAST(SUBSTRING(order_no, 2) AS UNSIGNED)) as max_num FROM ci_orders WHERE order_no REGEXP '^O[0-9]{10}$'")->row_array();
			$max_num = !empty($q['max_num']) ? (int)$q['max_num'] : 0;
		} elseif ($type === 'sales') {
			$q = $this->db->query("SELECT MAX(CAST(SUBSTRING(sales_ref_no, 2) AS UNSIGNED)) as max_num FROM ci_orders WHERE sales_ref_no REGEXP '^S[0-9]{10}$'")->row_array();
			$max_num = !empty($q['max_num']) ? (int)$q['max_num'] : 0;
		}

		$candidate_num = $max_num + 1;
		do {
			$candidate = sprintf($prefix . '%010d', $candidate_num);
			$cnt = $this->db->where($col, $candidate)->count_all_results($table);
			if ($cnt > 0) {
				$candidate_num++;
			}
		} while ($cnt > 0);

		return $candidate;
	}

	public function add_notification($user_id = 0, $type = 'activity', $title = '', $message = '', $link = '', $icon = 'fa fa-bell', $color = '#0072f0')
	{
		$data = array(
			'user_id' => (int)$user_id,
			'type' => $type,
			'title' => $title,
			'message' => $message,
			'link' => $link,
			'icon' => $icon,
			'color' => $color,
			'is_read' => 0,
			'created_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('ci_notifications', $data);
		return $this->db->insert_id();
	}

	public function get_user_notifications($user_id = 0, $limit = 12)
	{
		$user_id = (int)$user_id;
		if ($user_id <= 0) {
			$user_id = (int)$this->session->userdata('manager_id');
		}
		$user_role = (int)$this->session->userdata('manager_role');
		$is_admin = ($user_role === 1 || $user_id === 1);
		$is_backoffice = ($is_admin || $user_role === 2 || $user_role === 4 || has_section_perm('kam', 'write') || has_section_perm('back_office'));

		$notifs = array();

		if ($is_backoffice) {
			$pending_leads_count = $this->db->where('pipeline_stage', 'pending_lead')->count_all_results('ci_pipeline');
			if ($pending_leads_count > 0) {
				$notifs[] = array(
					'id' => 'action_pending_leads',
					'type' => 'action_required',
					'icon' => 'fa fa-check-square-o',
					'color' => '#000000',
					'title' => 'Pending Leads Approval',
					'message' => $pending_leads_count . ' database lead' . ($pending_leads_count == 1 ? '' : 's') . ' awaiting approval',
					'time' => date('Y-m-d H:i:s'),
					'time_ago' => 'Action Required',
					'link' => base_url('manager/leads/review_leads'),
					'is_read' => 0
				);
			}

			$pending_orders_count = $this->db->where("(order_request_status = 'pending' OR (is_locked = 1 AND approval_status = 'pending'))")->count_all_results('ci_leads');
			if ($pending_orders_count > 0) {
				$notifs[] = array(
					'id' => 'action_pending_orders',
					'type' => 'action_required',
					'icon' => 'fa fa-hourglass-half',
					'color' => '#000000',
					'title' => 'Pending Order Process',
					'message' => $pending_orders_count . ' order request' . ($pending_orders_count == 1 ? '' : 's') . ' awaiting processing',
					'time' => date('Y-m-d H:i:s'),
					'time_ago' => 'Action Required',
					'link' => base_url('manager/orders/pending_process'),
					'is_read' => 0
				);
			}
		}

		$due_followups = $this->db->select('O.id, O.order_no, O.client_name, O.followup_date, O.followup_time')
			->from('ci_orders O')
			->where_in('O.status', array('Approved', 'In Progress', 'Under Review'))
			->where('O.followup_date IS NOT NULL', null, false)
			->where('O.followup_date <=', date('Y-m-d'))
			->where("(O.assigned_to = {$user_id} OR O.created_by = {$user_id})")
			->limit(5)
			->get()
			->result_array();
		if (!empty($due_followups)) {
			foreach ($due_followups as $df) {
				$notifs[] = array(
					'id' => 'order_fu_' . $df['id'],
					'type' => 'followup_due',
					'icon' => 'fa fa-clock-o',
					'color' => '#000000',
					'title' => 'Order Follow-up Due',
					'message' => '#' . $df['order_no'] . ' - ' . $df['client_name'] . ' (Due: ' . date('d M', strtotime($df['followup_date'])) . ')',
					'time' => $df['followup_date'] . ' ' . (!empty($df['followup_time']) ? $df['followup_time'] : '09:00:00'),
					'time_ago' => 'Due Today',
					'link' => base_url('manager/orders/followups'),
					'is_read' => 0
				);
			}
		}

		$db_notifs = $this->db->select('*')
			->from('ci_notifications')
			->where("(user_id = " . $user_id . " OR user_id = 0)")
			->order_by('id', 'DESC')
			->limit(15)
			->get()
			->result_array();

		if (!empty($db_notifs)) {
			foreach ($db_notifs as $dn) {
				$notifs[] = array(
					'id' => 'notif_' . $dn['id'],
					'type' => $dn['type'],
					'icon' => !empty($dn['icon']) ? $dn['icon'] : 'fa fa-bell',
					'color' => !empty($dn['color']) ? $dn['color'] : '#0072f0',
					'title' => $dn['title'],
					'message' => $dn['message'],
					'time' => $dn['created_at'],
					'time_ago' => !empty($dn['created_at']) ? date('d M, H:i', strtotime($dn['created_at'])) : '',
					'link' => !empty($dn['link']) ? $dn['link'] : base_url('manager/dashboard'),
					'is_read' => (int)($dn['is_read'] ?? 0)
				);
			}
		}

		$this->db->select('P.*, L.customer, L.lead_id as lead_code, L.created_by as lead_owner');
		$this->db->from('ci_pipeline_logs P');
		$this->db->join('ci_leads L', 'L.id = P.lead_id', 'LEFT');
		if (!$is_backoffice) {
			$this->db->where("(P.conversion_requested_by = {$user_id} OR P.order_requested_by = {$user_id} OR L.created_by = {$user_id})");
		}
		$pipeline_logs = $this->db->order_by('P.id', 'DESC')->limit(10)->get()->result_array();

		if (!empty($pipeline_logs)) {
			foreach ($pipeline_logs as $pl) {
				$act = $pl['action'];
				$icon = 'fa fa-info-circle';
				$color = '#2563eb';
				$title = 'Pipeline Update';
				if ($act === 'convert_to_lead') {
					$icon = 'fa fa-check-circle';
					$color = '#16a34a';
					$title = 'Lead Approved';
				} elseif ($act === 'reject_conversion') {
					$icon = 'fa fa-times-circle';
					$color = '#dc2626';
					$title = 'Lead Approval Rejected';
				} elseif ($act === 'request_conversion') {
					$icon = 'fa fa-clock-o';
					$color = '#d97706';
					$title = 'Conversion Requested';
				}
				$notifs[] = array(
					'id' => 'pipe_' . $pl['id'],
					'type' => 'pipeline',
					'icon' => $icon,
					'color' => $color,
					'title' => $title,
					'message' => (!empty($pl['customer']) ? $pl['customer'] . ': ' : '') . ($pl['remarks'] ?: 'Action updated'),
					'time' => $pl['created_at'],
					'time_ago' => !empty($pl['created_at']) ? date('d M, H:i', strtotime($pl['created_at'])) : '',
					'link' => base_url('manager/leads/view/' . base64_encode($pl['lead_id'])),
					'is_read' => 0
				);
			}
		}

		$this->db->select('OL.*, O.order_no, O.client_name, O.created_by as ord_creator, O.assigned_to as ord_assigned');
		$this->db->from('ci_orders_logs OL');
		$this->db->join('ci_orders O', 'O.id = OL.order_id', 'LEFT');
		if (!$is_backoffice) {
			$this->db->where("(O.created_by = {$user_id} OR O.assigned_to = {$user_id} OR O.lead_id IN (SELECT id FROM ci_leads WHERE created_by = {$user_id}))");
		}
		$order_logs = $this->db->order_by('OL.id', 'DESC')->limit(10)->get()->result_array();

		if (!empty($order_logs)) {
			foreach ($order_logs as $ol) {
				$act = $ol['action'];
				$icon = 'fa fa-shopping-cart';
				$color = '#2563eb';
				$title = 'Order Update';
				if ($act === 'convert_to_sales') {
					$icon = 'fa fa-line-chart';
					$color = '#16a34a';
					$title = 'Order Converted to Sales';
				} elseif ($act === 'reject' || $act === 'cancel') {
					$icon = 'fa fa-ban';
					$color = '#dc2626';
					$title = 'Order Rejected/Cancelled';
				} elseif ($act === 'approve') {
					$icon = 'fa fa-check-circle';
					$color = '#16a34a';
					$title = 'Order Approved';
				} elseif ($act === 'quick_accept') {
					$icon = 'fa fa-handshake-o';
					$color = '#0072f0';
					$title = 'Order Accepted';
				}
				$notifs[] = array(
					'id' => 'ord_' . $ol['id'],
					'type' => 'order',
					'icon' => $icon,
					'color' => $color,
					'title' => $title,
					'message' => (!empty($ol['order_no']) ? '#' . $ol['order_no'] . ' - ' : '') . (!empty($ol['client_name']) ? $ol['client_name'] . ': ' : '') . ($ol['notes'] ?: 'Order status updated'),
					'time' => $ol['created_at'],
					'time_ago' => !empty($ol['created_at']) ? date('d M, H:i', strtotime($ol['created_at'])) : '',
					'link' => base_url('manager/orders/view/' . $ol['order_id']),
					'is_read' => 0
				);
			}
		}

		if ($is_backoffice) {
			$admin_logs = $this->db->select('AL.*')
				->from('ci_admin_logs AL')
				->where_in('AL.action', array('lead_assign', 'status_change', 'convert_to_lead', 'convert_to_sales', 'order_status_update'))
				->order_by('AL.id', 'DESC')
				->limit(10)
				->get()
				->result_array();

			if (!empty($admin_logs)) {
				foreach ($admin_logs as $al) {
					$notifs[] = array(
						'id' => 'admin_' . $al['id'],
						'type' => 'activity',
						'icon' => 'fa fa-bolt',
						'color' => '#0084ff',
						'title' => ucwords(str_replace('_', ' ', $al['action'])),
						'message' => $al['details'],
						'time' => $al['created_at'],
						'time_ago' => !empty($al['created_at']) ? date('d M, H:i', strtotime($al['created_at'])) : '',
						'link' => base_url('manager/dashboard'),
						'is_read' => 0
					);
				}
			}
		}

		usort($notifs, function($a, $b) {
			$t1 = !empty($a['time']) ? strtotime($a['time']) : 0;
			$t2 = !empty($b['time']) ? strtotime($b['time']) : 0;
			return $t2 - $t1;
		});

		return array_slice($notifs, 0, (int)$limit);
	}

	public function get_accessible_user_ids($manager_id = 0)
	{
		$manager_id = (int)$manager_id;
		if (!$manager_id) {
			$manager_id = (int)$this->session->userdata('manager_id');
		}
		$team = $this->fetchTeamList($manager_id, '');
		if (!is_array($team)) {
			$team = array();
		}
		$clean = array_unique(array_filter(array_map('intval', $team)));
		if (!in_array($manager_id, $clean)) {
			$clean[] = $manager_id;
		}
		return array_values($clean);
	}
}

if (!function_exists('has_section_perm')) {
	function has_section_perm($section, $required = 'read') {
		$ci =& get_instance();
		if (!isset($ci->ci_admin_model)) {
			$ci->load->model('ci_admin_model');
		}
		return $ci->ci_admin_model->check_permission($section, $required);
	}
}