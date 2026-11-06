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
	public function get_lead_report($d1 = '', $d2 = '', $f1 = '', $f2 = '', $category = '', $created_by = '', $status = '', $s_cat = '', $owner = '')
	{
		$db = $this->db;
		$db->select('A.*, B.name as lead_maker, C.status as status_name, (SELECT O.name FROM ci_user O WHERE O.id = ' . $this->owner_sql('A') . ') AS owner_name', false)
			->from('ci_leads as A')
			->join('ci_user as B', 'A.created_by = B.id', 'left')
			->join('ci_status as C', 'A.status = C.id', 'left');
		if ($owner !== '') $db->where($this->owner_sql('A') . ' = ' . (int)$owner, null, false);
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
	  
	 

	/* ------------------------------------------------------------------
	 * Sales & Activity reports. Every function gets the people to report on
	 * from insight_people(), so nobody sees anyone outside their own scope.
	 * ------------------------------------------------------------------ */

	/** People in scope (admin: all active staff; others: self + team), keyed by id. $only: just that one person. */
	public function insight_people($only = 0)
	{
		$scope = $this->scope_ids();
		$this->db->select('id, name, office')->from('ci_user')->where('status', 1);
		$pres = function_exists('crm_presentation_role_id') ? crm_presentation_role_id() : 0;
		if ($pres > 0) $this->db->where('user_type !=', $pres);
		if ($scope !== null) $this->db->where_in('id', $scope ? $scope : array(-1));
		if ((int)$only > 0) $this->db->where('id', (int)$only);
		$this->db->order_by('name', 'ASC');
		$out = array();
		foreach ($this->db->get()->result_array() as $u) {
			$out[(int)$u['id']] = array('id' => (int)$u['id'], 'name' => $u['name'], 'office' => trim((string)$u['office']));
		}
		return $out;
	}

	private function _amount_sql($o = 'o')
	{
		return "CAST(COALESCE(NULLIF({$o}.pricing_amount, 0), NULLIF({$o}.mrr, 0), 0) AS DECIMAL(12,2))";
	}

	private function _is_sale_sql($o = 'o')
	{
		return "({$o}.status IN ('Sales', 'Closed / Sold', 'Completed') OR ({$o}.sales_ref_no IS NOT NULL AND {$o}.sales_ref_no != ''))";
	}

	/** Runs $sql (must return uid + columns) and merges each column into $rows[uid]. */
	private function _merge(&$rows, $sql)
	{
		$r = $this->db->query($sql);
		if (!$r || !is_object($r)) return;
		foreach ($r->result_array() as $row) {
			$id = (int)$row['uid'];
			if (!isset($rows[$id])) continue;
			foreach ($row as $k => $v) if ($k !== 'uid') $rows[$id][$k] = is_numeric($v) ? $v + 0 : $v;
		}
	}

	private function _range($d1, $d2)
	{
		$a = $this->_day($d1); $b = $this->_day($d2);
		if ($a === '') $a = date('Y-m-01');
		if ($b === '') $b = date('Y-m-d');
		if ($a > $b) { $t = $a; $a = $b; $b = $t; }
		return array($a, $b);
	}

	/** Sales vs target for one month ("Y-m"). Orders and sales always count for the person who made them. */
	public function insight_sales_target($people, $month)
	{
		if (!preg_match('/^\d{4}-\d{2}$/', (string)$month)) $month = date('Y-m');
		$m1 = $month . '-01'; $m2 = date('Y-m-t', strtotime($m1));
		$rows = array();
		foreach ($people as $id => $p) $rows[$id] = $p + array('orders' => 0, 'sales' => 0, 'sales_aed' => 0.0, 'target' => 0.0);
		if (!$rows) return array();
		$in = $this->_ids_sql(array_keys($rows));
		$amt = $this->_amount_sql('o'); $sale = $this->_is_sale_sql('o');
		$a = $this->db->escape($m1 . ' 00:00:00'); $b = $this->db->escape($m2 . ' 23:59:59');
		$this->_merge($rows, "SELECT o.created_by uid, COUNT(*) orders, SUM({$sale}) sales, COALESCE(SUM(CASE WHEN {$sale} THEN {$amt} ELSE 0 END), 0) sales_aed
			FROM ci_orders o WHERE o.created_at BETWEEN {$a} AND {$b} AND o.created_by IN ({$in}) GROUP BY o.created_by");
		$mk = $this->db->escape($month); $e1 = $this->db->escape($m1); $e2 = $this->db->escape($m2);
		$this->_merge($rows, "SELECT T.user_id uid, COALESCE(SUM(T.target_amount), 0) target FROM ci_hr_targets T
			WHERE T.status = 'Active' AND (T.period_month = {$mk} OR ({$e1} <= T.end_date AND {$e2} >= T.start_date)) AND T.user_id IN ({$in}) GROUP BY T.user_id");
		foreach ($rows as &$r) {
			$r['pct'] = $r['target'] > 0 ? (int)round($r['sales_aed'] * 100 / $r['target']) : null;
			$r['gap'] = $r['target'] > 0 ? max(0, $r['target'] - $r['sales_aed']) : null;
		}
		unset($r);
		usort($rows, function ($x, $y) {
			foreach (array('sales_aed', 'sales', 'orders', 'target') as $k) if ($x[$k] != $y[$k]) return $x[$k] < $y[$k] ? 1 : -1;
			return strcasecmp($x['name'], $y['name']);
		});
		return $rows;
	}

	/** Calls per person between two dates. */
	public function insight_calls($people, $d1, $d2)
	{
		list($a, $b) = $this->_range($d1, $d2);
		$rows = array();
		foreach ($people as $id => $p) $rows[$id] = $p + array('calls' => 0, 'completed' => 0, 'talk_sec' => 0, 'days' => 0);
		if (!$rows) return array();
		$in = $this->_ids_sql(array_keys($rows));
		$ea = $this->db->escape($a . ' 00:00:00'); $eb = $this->db->escape($b . ' 23:59:59');
		$this->_merge($rows, "SELECT user_id uid, COUNT(*) calls, SUM(call_status = 'Completed') completed, COALESCE(SUM(duration), 0) talk_sec, COUNT(DISTINCT DATE(created_at)) days
			FROM ci_call_logs WHERE created_at BETWEEN {$ea} AND {$eb} AND user_id IN ({$in}) GROUP BY user_id");
		foreach ($rows as &$r) $r['per_day'] = $r['days'] > 0 ? round($r['calls'] / $r['days'], 1) : 0;
		unset($r);
		usort($rows, function ($x, $y) { return $x['calls'] == $y['calls'] ? strcasecmp($x['name'], $y['name']) : ($x['calls'] < $y['calls'] ? 1 : -1); });
		return $rows;
	}

	/** Lead status groups (by status name, so renamed ids still work). */
	public function insight_status_groups()
	{
		$groups = array(
			'new' => array('Database'),
			'working' => array('Expected', 'In Process', 'Submitted', 'Pending Activation', 'Follow up / Call Back', 'Future', 'Busy / Not Responding'),
			'won' => array('Closed', 'Activated'),
			'lost' => array('Lost', 'Not Interested'),
			'invalid' => array('Invalid Entry', 'DNCR / DND', 'Not Valid'),
		);
		$ids = array();
		foreach ($this->db->select('id, status')->get('ci_status')->result_array() as $s) {
			foreach ($groups as $g => $names) if (in_array(trim($s['status']), $names, true)) $ids[$g][] = (int)$s['id'];
		}
		foreach ($groups as $g => $x) if (empty($ids[$g])) $ids[$g] = array(-1);
		return array('ids' => $ids, 'names' => $groups);
	}

	/** Lead funnel per current owner, for leads created between two dates. */
	public function insight_funnel($people, $d1, $d2)
	{
		list($a, $b) = $this->_range($d1, $d2);
		$g = $this->insight_status_groups();
		$rows = array();
		foreach ($people as $id => $p) $rows[$id] = $p + array('total' => 0, 'new' => 0, 'working' => 0, 'won' => 0, 'lost' => 0, 'invalid' => 0);
		if (!$rows) return array();
		$in = $this->_ids_sql(array_keys($rows));
		$own = $this->owner_sql('A');
		$cols = array();
		foreach (array('new', 'working', 'won', 'lost', 'invalid') as $k) $cols[] = "SUM(A.status IN (" . $this->_ids_sql($g['ids'][$k]) . ")) {$k}";
		$this->_merge($rows, "SELECT {$own} uid, COUNT(*) total, " . implode(', ', $cols) . "
			FROM ci_leads A WHERE LEFT(A.created, 10) BETWEEN " . $this->db->escape($a) . " AND " . $this->db->escape($b) . " AND {$own} IN ({$in}) GROUP BY uid");
		foreach ($rows as &$r) {
			$valid = $r['total'] - $r['invalid'];
			$r['conv'] = $valid > 0 ? round($r['won'] * 100 / $valid, 1) : null;
		}
		unset($r);
		$rows = array_values(array_filter($rows, function ($r) { return $r['total'] > 0; }));
		usort($rows, function ($x, $y) { return $x['total'] == $y['total'] ? strcasecmp($x['name'], $y['name']) : ($x['total'] < $y['total'] ? 1 : -1); });
		return $rows;
	}

	/** Orders and sales by service, between two dates (orders made by people in scope). */
	public function insight_services($people, $d1, $d2)
	{
		list($a, $b) = $this->_range($d1, $d2);
		if (!$people) return array();
		$in = $this->_ids_sql(array_keys($people));
		$amt = $this->_amount_sql('o'); $sale = $this->_is_sale_sql('o');
		$sql = "SELECT COALESCE(NULLIF(TRIM(o.service_title), ''), 'Not set') service, COUNT(*) orders, SUM({$sale}) sales,
			COALESCE(SUM(CASE WHEN {$sale} THEN {$amt} ELSE 0 END), 0) sales_aed, COALESCE(SUM({$amt}), 0) order_aed, COUNT(DISTINCT o.created_by) sellers
			FROM ci_orders o WHERE o.created_at BETWEEN " . $this->db->escape($a . ' 00:00:00') . " AND " . $this->db->escape($b . ' 23:59:59') . "
			AND o.created_by IN ({$in}) GROUP BY service ORDER BY sales_aed DESC, orders DESC";
		$r = $this->db->query($sql);
		return ($r && is_object($r)) ? $r->result_array() : array();
	}

	/** Centers side by side for one month (admin only - the controller checks). */
	public function insight_centers($people, $month)
	{
		if (!preg_match('/^\d{4}-\d{2}$/', (string)$month)) $month = date('Y-m');
		$m1 = $month . '-01'; $m2 = date('Y-m-t', strtotime($m1));
		$per = $this->insight_sales_target($people, $month);
		$calls = array();
		foreach ($this->insight_calls($people, $m1, $m2) as $c) $calls[$c['id']] = $c['calls'];
		$by = array();
		foreach ($per as $p) {
			$k = $p['office'] !== '' ? $p['office'] : 'No center set';
			if (!isset($by[$k])) $by[$k] = array('name' => $k, 'staff' => 0, 'calls' => 0, 'orders' => 0, 'sales' => 0, 'sales_aed' => 0.0, 'target' => 0.0);
			$by[$k]['staff']++;
			$by[$k]['calls'] += isset($calls[$p['id']]) ? $calls[$p['id']] : 0;
			foreach (array('orders', 'sales', 'sales_aed', 'target') as $f) $by[$k][$f] += $p[$f];
		}
		foreach ($by as &$c) $c['pct'] = $c['target'] > 0 ? (int)round($c['sales_aed'] * 100 / $c['target']) : null;
		unset($c);
		$by = array_values($by);
		usort($by, function ($x, $y) { return $x['sales_aed'] == $y['sales_aed'] ? strcmp($x['name'], $y['name']) : ($x['sales_aed'] < $y['sales_aed'] ? 1 : -1); });
		return $by;
	}

	/** Open leads whose follow-up date has passed, for their current owners. */
	public function insight_overdue($people, $limit = 1000)
	{
		if (!$people) return array();
		$g = $this->insight_status_groups();
		$closed = $this->_ids_sql(array_merge($g['ids']['won'], $g['ids']['lost'], $g['ids']['invalid']));
		$own = $this->owner_sql('A');
		$today = $this->db->escape(date('Y-m-d'));
		$sql = "SELECT A.id, A.customer, A.poc, LEFT(A.followup_date, 10) followup, DATEDIFF({$today}, LEFT(A.followup_date, 10)) days_over, S.status status_name, U.name owner
			FROM ci_leads A LEFT JOIN ci_status S ON S.id = A.status LEFT JOIN ci_user U ON U.id = {$own}
			WHERE A.followup_date IS NOT NULL AND A.followup_date != '' AND LEFT(A.followup_date, 10) < {$today}
			AND (A.status IS NULL OR A.status NOT IN ({$closed})) AND {$own} IN (" . $this->_ids_sql(array_keys($people)) . ")
			ORDER BY LEFT(A.followup_date, 10) DESC LIMIT " . (int)$limit;
		$r = $this->db->query($sql);
		return ($r && is_object($r)) ? $r->result_array() : array();
	}

	/* ------------------------------------------------------------------
	 * Performance and follow-up reports (same scope as above).
	 * ------------------------------------------------------------------ */

	/** People in scope with their department (role). $role: only that role; $only: only that person. */
	public function perf_people($role = 0, $only = 0)
	{
		$scope = $this->scope_ids();
		$this->db->select('U.id, U.name, U.user_type, R.title AS role_name')->from('ci_user U')
			->join('ci_roles R', 'R.id = U.user_type', 'left')->where('U.status', 1);
		$pres = function_exists('crm_presentation_role_id') ? crm_presentation_role_id() : 0;
		if ($pres > 0) $this->db->where('U.user_type !=', $pres);
		if ($scope !== null) $this->db->where_in('U.id', $scope ? $scope : array(-1));
		if ((int)$role > 0) $this->db->where('U.user_type', (int)$role);
		if ((int)$only > 0) $this->db->where('U.id', (int)$only);
		$this->db->order_by('R.title', 'ASC')->order_by('U.name', 'ASC');
		$out = array();
		foreach ($this->db->get()->result_array() as $u) {
			$dept = trim((string)$u['role_name']);
			$out[(int)$u['id']] = array('id' => (int)$u['id'], 'name' => $u['name'], 'role' => (int)$u['user_type'], 'dept' => $dept !== '' ? $dept : 'No department');
		}
		return $out;
	}

	/** Departments (roles) that have people in scope, for the filter. */
	public function perf_departments()
	{
		$out = array();
		foreach ($this->perf_people() as $p) $out[$p['role']] = $p['dept'];
		asort($out);
		return $out;
	}

	/**
	 * Activity per person between two dates: leads added (current owner), calls, follow-ups and meetings logged,
	 * orders and sales (always the person who made them), and open leads with an overdue follow-up (today).
	 */
	public function perf_rows($people, $d1, $d2)
	{
		list($a, $b) = $this->_range($d1, $d2);
		$rows = array();
		foreach ($people as $id => $p) $rows[$id] = $p + array('leads' => 0, 'calls' => 0, 'followups' => 0, 'meetings' => 0, 'meetings_missed' => 0, 'orders' => 0, 'sales' => 0, 'sales_aed' => 0.0, 'overdue' => 0);
		if (!$rows) return array();
		$in = $this->_ids_sql(array_keys($rows));
		$own = $this->owner_sql('A');
		$ea = $this->db->escape($a); $eb = $this->db->escape($b);
		$ta = $this->db->escape($a . ' 00:00:00'); $tb = $this->db->escape($b . ' 23:59:59');
		$today = $this->db->escape(date('Y-m-d'));
		$this->_merge($rows, "SELECT {$own} uid, COUNT(*) leads FROM ci_leads A WHERE LEFT(A.created, 10) BETWEEN {$ea} AND {$eb} AND {$own} IN ({$in}) GROUP BY uid");
		$this->_merge($rows, "SELECT user_id uid, COUNT(*) calls FROM ci_call_logs WHERE created_at BETWEEN {$ta} AND {$tb} AND user_id IN ({$in}) GROUP BY user_id");
		$this->_merge($rows, "SELECT user_id uid, COUNT(*) followups FROM ci_followup WHERE followup_remark != '' AND LEFT(followup_date, 10) BETWEEN {$ea} AND {$eb} AND user_id IN ({$in}) GROUP BY user_id");
		$this->_merge($rows, "SELECT created_by uid, COUNT(*) meetings, SUM(LEFT(m_date, 10) < {$today} AND (m_remark IS NULL OR m_remark = '')) meetings_missed
			FROM ci_meetings WHERE LEFT(m_date, 10) BETWEEN {$ea} AND {$eb} AND created_by IN ({$in}) GROUP BY created_by");
		$amt = $this->_amount_sql('o'); $sale = $this->_is_sale_sql('o');
		$this->_merge($rows, "SELECT o.created_by uid, COUNT(*) orders, SUM({$sale}) sales, COALESCE(SUM(CASE WHEN {$sale} THEN {$amt} ELSE 0 END), 0) sales_aed
			FROM ci_orders o WHERE o.created_at BETWEEN {$ta} AND {$tb} AND o.created_by IN ({$in}) GROUP BY o.created_by");
		$g = $this->insight_status_groups();
		$closed = $this->_ids_sql(array_merge($g['ids']['won'], $g['ids']['lost'], $g['ids']['invalid']));
		$this->_merge($rows, "SELECT {$own} uid, COUNT(*) overdue FROM ci_leads A WHERE A.followup_date IS NOT NULL AND A.followup_date != ''
			AND LEFT(A.followup_date, 10) < {$today} AND (A.status IS NULL OR A.status NOT IN ({$closed})) AND {$own} IN ({$in}) GROUP BY uid");
		foreach ($rows as &$r) $r['active'] = ($r['leads'] + $r['calls'] + $r['followups'] + $r['meetings'] + $r['orders']) > 0;
		unset($r);
		return array_values($rows);
	}

	/** One person's activity per day between two dates (days with nothing are left out), newest first. */
	public function perf_days($uid, $d1, $d2)
	{
		list($a, $b) = $this->_range($d1, $d2);
		$uid = (int)$uid;
		$ea = $this->db->escape($a); $eb = $this->db->escape($b);
		$ta = $this->db->escape($a . ' 00:00:00'); $tb = $this->db->escape($b . ' 23:59:59');
		$own = $this->owner_sql('A');
		$amt = $this->_amount_sql('o'); $sale = $this->_is_sale_sql('o');
		$queries = array(
			"SELECT LEFT(A.created, 10) d, COUNT(*) leads FROM ci_leads A WHERE LEFT(A.created, 10) BETWEEN {$ea} AND {$eb} AND {$own} = {$uid} GROUP BY d",
			"SELECT DATE(created_at) d, COUNT(*) calls FROM ci_call_logs WHERE created_at BETWEEN {$ta} AND {$tb} AND user_id = {$uid} GROUP BY d",
			"SELECT LEFT(followup_date, 10) d, COUNT(*) followups FROM ci_followup WHERE followup_remark != '' AND LEFT(followup_date, 10) BETWEEN {$ea} AND {$eb} AND user_id = {$uid} GROUP BY d",
			"SELECT LEFT(m_date, 10) d, COUNT(*) meetings FROM ci_meetings WHERE LEFT(m_date, 10) BETWEEN {$ea} AND {$eb} AND created_by = {$uid} GROUP BY d",
			"SELECT DATE(o.created_at) d, COUNT(*) orders, SUM({$sale}) sales, COALESCE(SUM(CASE WHEN {$sale} THEN {$amt} ELSE 0 END), 0) sales_aed FROM ci_orders o WHERE o.created_at BETWEEN {$ta} AND {$tb} AND o.created_by = {$uid} GROUP BY d",
		);
		$days = array();
		foreach ($queries as $sql) {
			$r = $this->db->query($sql);
			if (!$r || !is_object($r)) continue;
			foreach ($r->result_array() as $row) {
				$d = $row['d'];
				if (!isset($days[$d])) $days[$d] = array('d' => $d, 'leads' => 0, 'calls' => 0, 'followups' => 0, 'meetings' => 0, 'orders' => 0, 'sales' => 0, 'sales_aed' => 0.0);
				foreach ($row as $k => $v) if ($k !== 'd') $days[$d][$k] = $v + 0;
			}
		}
		krsort($days);
		return array_values($days);
	}

	/** Follow-ups logged by people in scope between two dates, newest first. $result: only that outcome. */
	public function followup_log($people, $d1, $d2, $result = '', $limit = 5000)
	{
		if (!$people) return array();
		list($a, $b) = $this->_range($d1, $d2);
		$this->db->select("F.id, LEFT(F.followup_date, 10) AS fdate, F.followup_time, F.followup_remark, F.followup_result, F.lead_type, F.lead_id, L.lead_id AS lead_ref, L.customer, L.poc, S.status AS status_name, U.name AS by_name", false)
			->from('ci_followup F')
			->join('ci_leads L', 'L.id = F.lead_id', 'left')
			->join('ci_status S', 'S.id = L.status', 'left')
			->join('ci_user U', 'U.id = F.user_id', 'left')
			->where("F.followup_remark != ''", null, false)
			->where('LEFT(F.followup_date, 10) BETWEEN ' . $this->db->escape($a) . ' AND ' . $this->db->escape($b), null, false)
			->where_in('F.user_id', array_keys($people));
		if ($result !== '') $this->db->where('F.followup_result', $result);
		$this->db->order_by('F.followup_date', 'DESC')->order_by('F.id', 'DESC')->limit((int)$limit);
		return $this->db->get()->result_array();
	}

	/** Outcomes used on follow-ups, for the filter. */
	public function followup_results()
	{
		$out = array();
		foreach ($this->db->query("SELECT DISTINCT followup_result r FROM ci_followup WHERE followup_result IS NOT NULL AND followup_result != '' ORDER BY r")->result_array() as $r) $out[] = $r['r'];
		return $out;
	}
}
