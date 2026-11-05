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
		if (!is_array($user_tree_array)) {
			$user_tree_array = array();
		}
		if (empty($department)) {
			$this->db->select('id');
			$this->db->from('ci_user');
			$this->db->where("department != ''");
			$this->db->where('status', 1);
		} else {
			$this->db->select('id');
			$this->db->from('ci_user');
			$this->db->where('department', $department);
			$this->db->where('status', 1);
		}
		$rows = $this->db->get();
		if ($rows->num_rows() >= 1) {
			foreach ($rows->result_array() as $row) {
				$user_tree_array[] = (int)$row['id'];
			}
		}
		return array_values(array_unique($user_tree_array));
	}


	/**
	 * Who the signed-in user may see in reports.
	 * null = everyone (admin). Otherwise: the user themself + everyone under them (any level).
	 */
	public function scope_ids()
	{
		$me = (int)$this->session->userdata('manager_id');
		$role = (int)$this->session->userdata('manager_role');
		if ($role === 1 || $me === 1) return null;
		$ids = $this->ci_admin_model->fetchTeamList($me, '');
		$ids = is_array($ids) ? array_map('intval', $ids) : array();
		$ids[] = $me;
		return array_values(array_unique(array_filter($ids, function ($x) { return $x > 0; })));
	}

	/** Current owner of a lead: the person it is assigned (transferred) to, else who created it. */
	public function owner_sql($a = 'A')
	{
		return "(CASE WHEN {$a}.lead_assign > 0 THEN {$a}.lead_assign ELSE {$a}.created_by END)";
	}

	private function _ids_sql($ids)
	{
		$ids = array_filter(array_map('intval', (array)$ids), function ($x) { return $x > 0; });
		return $ids ? implode(',', $ids) : '-1';
	}

	/** A date as Y-m-d, or '' when empty / not a date. */
	private function _day($v)
	{
		$v = is_string($v) ? trim($v) : '';
		if ($v === '') return '';
		$t = strtotime($v);
		return $t ? date('Y-m-d', $t) : '';
	}

	/** "column between d1 and d2", for columns that hold "Y-m-d" or "Y-m-d H:i:s". */
	private function _between($col, $d1, $d2)
	{
		$d1 = $this->_day($d1); $d2 = $this->_day($d2);
		if ($d1 === '' || $d2 === '') return '';
		return "LEFT({$col}, 10) BETWEEN " . $this->db->escape($d1) . " AND " . $this->db->escape($d2);
	}

	public function getPerformanceReportByIndividual($d1 = '', $d2 = '', $manager_id = 0)
	{
		$mid = (int)$manager_id;
		$today = $this->db->escape(date('Y-m-d'));
		$own = $this->owner_sql('A');
		$cond = array();
		$b = $this->_between('A.created', $d1, $d2);
		if ($b !== '') $cond[] = $b;
		if ($mid > 0) $cond[] = "{$own} = {$mid}";
		$con = $cond ? 'WHERE ' . implode(' AND ', $cond) : '';

		$sql = "SELECT A.*, COUNT(id) AS totalLead,
			(SELECT COUNT(DISTINCT id) FROM ci_followup as a WHERE a.followup_date < {$today} AND a.followup_date != '' AND a.lead_id = A.id) AS missedFollowups,
			(SELECT COUNT(DISTINCT id) FROM ci_followup as a WHERE a.followup_remark != '' AND a.lead_id = A.id) AS attendedFollowups,
			(SELECT COUNT(DISTINCT id) FROM ci_leads as a WHERE a.m_date < {$today} AND a.m_date != '' AND " . $this->owner_sql('a') . " = {$mid}) AS totalMissedMeetings,
			(SELECT COUNT(DISTINCT id) FROM ci_meetings as a WHERE a.m_remark != '' AND a.created_by = {$mid}) AS attendedMeetings
			FROM ci_leads as A {$con} GROUP BY A.created ORDER BY A.created DESC";
		$rows = $this->db->query($sql);
		return ($rows && is_object($rows)) ? $rows->result_array() : array();
	}

	public function getPerformanceReportByDepartment($d1 = '', $d2 = '', $department = '', $scope = null)
	{
		$department = is_string($department) ? trim($department) : '';
		$today = $this->db->escape(date('Y-m-d'));
		$team = $this->fetchTeamListByDepartment($department, '');
		if (is_array($scope)) $team = array_values(array_intersect($team, array_map('intval', $scope)));
		$in = $this->_ids_sql($team);
		$own = $this->owner_sql('A');

		$cond = array("{$own} IN ({$in})", "B.department != ''");
		$b = $this->_between('A.created', $d1, $d2);
		if ($b !== '') $cond[] = $b;
		if ($department !== '') $cond[] = 'B.department = ' . $this->db->escape($department);

		$act_row = $this->db->select('id')->from('ci_status')->like('status', 'Activate', 'after')->get()->row_array();
		$active_id = !empty($act_row['id']) ? (int)$act_row['id'] : 42;
		$cls_row = $this->db->select('id')->from('ci_status')->where('status', 'Closed')->get()->row_array();
		$closed_id = !empty($cls_row['id']) ? (int)$cls_row['id'] : 39;
		$a_own = $this->owner_sql('a');

		$sql = "SELECT A.*, COUNT(A.id) as totalLead, SUM(A.mrr) as amount, B.name, B.id, B.department,
			(SELECT COUNT(id) FROM ci_leads a WHERE a.status = {$active_id} AND {$a_own} = B.id) AS activeLead,
			(SELECT COUNT(DISTINCT id) FROM ci_leads a WHERE a.status = {$closed_id} AND {$a_own} = B.id) AS closedLead,
			(SELECT COUNT(DISTINCT id) FROM ci_followup as a WHERE a.followup_date < {$today} AND a.followup_date != '' AND a.lead_id = A.id) AS total_missed,
			(SELECT COUNT(DISTINCT id) FROM ci_followup as a WHERE a.followup_remark != '' AND a.lead_id = A.id) AS attendedFollowups,
			(SELECT COUNT(DISTINCT id) FROM ci_meetings as a WHERE a.m_date < {$today} AND a.m_date != '' AND a.lead_id = A.id) AS totalMissedMeetings,
			(SELECT COUNT(DISTINCT id) FROM ci_meetings as a WHERE a.m_remark != '' AND a.lead_id = A.id) AS attendedMeetings,
			(SELECT COUNT(id) FROM ci_verticals a WHERE a.created_by = B.id) AS totalVerticals
			FROM ci_leads as A LEFT JOIN ci_user as B ON {$own} = B.id
			WHERE " . implode(' AND ', $cond) . " GROUP BY B.id ORDER BY B.id DESC";
		$rows = $this->db->query($sql);
		return ($rows && is_object($rows)) ? $rows->result_array() : array();
	}

	/** Users report: admin sees everyone, others see themself and the people under them. */
	public function get_user_report($d1 = '', $d2 = '', $role = '', $manager = '', $status = '', $department = '', $office = '')
	{
		$db = $this->db;
		$db->select('A.*, B.title as role_name, C.name as manager_name')
			->from('ci_user as A')
			->join('ci_roles as B', 'A.user_type = B.id', 'left')
			->join('ci_user as C', 'A.manager = C.id', 'left');
		$b = $this->_between('A.created', $d1, $d2);
		if ($b !== '') $db->where($b, null, false);
		if ($role !== '') $db->where('A.user_type', (int)$role);
		if ($manager !== '') $db->where('A.manager', (int)$manager);
		if ($status !== '') $db->where('A.admin_status', (int)$status);
		if ($department !== '') $db->where('A.department', $department);
		if ($office !== '') $db->where('A.office', $office);
		$scope = $this->scope_ids();
		if ($scope !== null) $db->where_in('A.id', $scope ? $scope : array(-1));
		$db->group_by('A.id')->order_by('A.id', 'DESC');
		return $db->get()->result_array();
	}

	/**
	 * Leads report: a lead belongs to its current owner (after a transfer, the new owner).
	 * Admin sees all; others see leads owned by themself or the people under them.
	 */
	public function get_lead_report($d1 = '', $d2 = '', $f1 = '', $f2 = '', $category = '', $created_by = '', $status = '', $s_cat = '')
	{
		$db = $this->db;
		$db->select('A.*, B.name as lead_maker, C.status as status_name')
			->from('ci_leads as A')
			->join('ci_user as B', 'A.created_by = B.id', 'left')
			->join('ci_status as C', 'A.status = C.id', 'left');
		$b = $this->_between('A.created', $d1, $d2);
		if ($b !== '') $db->where($b, null, false);
		$b = $this->_between('A.followup_date', $f1, $f2);
		if ($b !== '') $db->where($b, null, false);
		if ($category !== '') $db->where('A.category', $category);
		if ($created_by !== '') $db->where('A.created_by', (int)$created_by);
		if ($status !== '') $db->where('A.status', (int)$status);
		if ($s_cat !== '') $db->where('A.s_cat', $s_cat);
		$scope = $this->scope_ids();
		if ($scope !== null) $db->where($this->owner_sql('A') . ' IN (' . $this->_ids_sql($scope) . ')', null, false);
		$db->group_by('A.id')->order_by('A.id', 'DESC');
		return $db->get()->result_array();
	}

	/** Verticals report (same lead list and filters, same visibility). */
	public function get_vertical_report($d1 = '', $d2 = '', $f1 = '', $f2 = '', $category = '', $created_by = '', $status = '', $s_cat = '')
	{
		return $this->get_lead_report($d1, $d2, $f1, $f2, $category, $created_by, $status, $s_cat);
	}

	/** Follow-up report (same lead list and filters, same visibility). */
	public function get_followup_report($d1 = '', $d2 = '', $f1 = '', $f2 = '', $category = '', $created_by = '', $status = '', $s_cat = '')
	{
		return $this->get_lead_report($d1, $d2, $f1, $f2, $category, $created_by, $status, $s_cat);
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