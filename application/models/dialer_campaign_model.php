<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dialer_campaign_model extends CI_Model
{
	private $schema_checked = false;

	public function ensure_schema()
	{
		if ($this->schema_checked) {
			return;
		}
		$this->schema_checked = true;
		if (!$this->db->table_exists('ci_dialer_campaigns')) {
			$this->db->query("CREATE TABLE `ci_dialer_campaigns` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`name` VARCHAR(150) NOT NULL,
				`description` TEXT NULL,
				`reference_no` VARCHAR(20) NULL,
				`country` INT(11) NOT NULL DEFAULT 0,
				`state` INT(11) NOT NULL DEFAULT 0,
				`city` VARCHAR(150) NULL,
				`industry_category` VARCHAR(150) NULL,
				`database_category` VARCHAR(150) NULL,
				`data_source` VARCHAR(150) NULL,
				`pipeline_type` VARCHAR(20) NOT NULL DEFAULT 'database',
				`status_ids` VARCHAR(255) NULL,
				`participants` TEXT NULL,
				`daily_target` INT(11) NOT NULL DEFAULT 0,
				`start_date` DATE NULL,
				`end_date` DATE NULL,
				`status` VARCHAR(20) NOT NULL DEFAULT 'Active',
				`created_by` INT(11) NOT NULL,
				`created_at` DATETIME NOT NULL,
				`updated_at` DATETIME NOT NULL,
				PRIMARY KEY (`id`),
				KEY `idx_campaign_status` (`status`),
				KEY `idx_campaign_creator` (`created_by`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		}
		if (!$this->db->table_exists('ci_dialer_progress')) {
			$this->db->query("CREATE TABLE `ci_dialer_progress` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`user_id` INT(11) NOT NULL,
				`queue_key` VARCHAR(191) NOT NULL,
				`last_position` INT(11) NOT NULL DEFAULT 0,
				`last_lead_id` INT(11) NOT NULL DEFAULT 0,
				`updated_at` DATETIME NOT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `uniq_user_queue` (`user_id`, `queue_key`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		}
		if (!$this->db->field_exists('campaign_id', 'ci_call_logs')) {
			$this->db->query("ALTER TABLE `ci_call_logs` ADD `campaign_id` INT(11) NOT NULL DEFAULT 0, ADD KEY `idx_call_campaign` (`campaign_id`)");
		}
	}

	public function is_manager_role($role, $uid)
	{
		return ((int)$role === 1 || (int)$role === 4 || (int)$uid === 1);
	}

	public function get_campaigns($uid, $role)
	{
		$this->ensure_schema();
		$this->db->select('C.*, U.name AS created_by_name, CO.name AS country_name, ST.name AS state_name');
		$this->db->from('ci_dialer_campaigns C');
		$this->db->join('ci_user U', 'U.id = C.created_by', 'left');
		$this->db->join('countries CO', 'CO.id = C.country', 'left');
		$this->db->join('states ST', 'ST.id = C.state', 'left');
		if (!$this->is_manager_role($role, $uid)) {
			$this->db->where('(C.created_by = ' . (int)$uid . ' OR FIND_IN_SET(' . (int)$uid . ', C.participants) > 0)', null, false);
		}
		$this->db->order_by('C.id', 'DESC');
		return $this->db->get()->result_array();
	}

	public function get_campaign($id, $uid, $role)
	{
		$this->ensure_schema();
		$row = $this->db->where('id', (int)$id)->get('ci_dialer_campaigns')->row_array();
		if (empty($row)) {
			return null;
		}
		if ($this->is_manager_role($role, $uid) || (int)$row['created_by'] === (int)$uid) {
			return $row;
		}
		$participants = array_filter(array_map('intval', explode(',', (string)$row['participants'])));
		return in_array((int)$uid, $participants, true) ? $row : null;
	}

	public function can_manage($campaign, $uid, $role)
	{
		return $this->is_manager_role($role, $uid) || (int)$campaign['created_by'] === (int)$uid;
	}

	public function save_campaign($data, $id = 0)
	{
		$this->ensure_schema();
		$data['updated_at'] = date('Y-m-d H:i:s');
		if ((int)$id > 0) {
			$this->db->where('id', (int)$id)->update('ci_dialer_campaigns', $data);
			return (int)$id;
		}
		$data['created_at'] = $data['updated_at'];
		$this->db->insert('ci_dialer_campaigns', $data);
		return (int)$this->db->insert_id();
	}

	public function apply_filters($campaign)
	{
		if (!empty($campaign['country'])) {
			$this->db->where('A.country', (int)$campaign['country']);
		}
		if (!empty($campaign['state'])) {
			$this->db->where('A.state', (int)$campaign['state']);
		}
		if (!empty($campaign['city'])) {
			$this->db->like('A.city', trim($campaign['city']));
		}
		if (!empty($campaign['industry_category'])) {
			$this->db->where('A.industry_category', $campaign['industry_category']);
		}
		if (!empty($campaign['database_category'])) {
			$this->db->where('A.database_category', $campaign['database_category']);
		}
		if (!empty($campaign['data_source'])) {
			$this->db->where('A.data_source', $campaign['data_source']);
		}
		$status_ids = array_filter(array_map('intval', explode(',', (string)$campaign['status_ids'])));
		if (!empty($status_ids)) {
			$this->db->where_in('A.status', $status_ids);
		}
		if ($campaign['pipeline_type'] === 'lead') {
			$this->db->where('P.pipeline_stage', 'lead');
		} elseif ($campaign['pipeline_type'] === 'database') {
			$this->db->where('(P.pipeline_stage = "database" OR P.pipeline_stage IS NULL)', null, false);
		}
	}

	public function participant_ids($campaign)
	{
		$ids = array_filter(array_map('intval', explode(',', (string)$campaign['participants'])));
		if (empty($ids)) {
			$ids = array((int)$campaign['created_by']);
		}
		return array_values(array_unique($ids));
	}

	public function stats($campaign)
	{
		$ids = $this->participant_ids($campaign);
		$conditions = array();
		foreach ($ids as $pid) {
			$conditions[] = "A.lead_assign = {$pid} OR FIND_IN_SET({$pid}, A.agents) > 0";
		}
		$this->db->from('ci_leads A');
		$this->db->join('ci_pipeline P', 'P.lead_id = A.id', 'left');
		$this->db->where('(' . implode(' OR ', $conditions) . ')', null, false);
		$this->db->where('(A.contact1 != "" OR A.alt_contact != "" OR A.mobile != "" OR A.contact2 != "")', null, false);
		$this->apply_filters($campaign);
		$total = (int)$this->db->count_all_results();

		$cid = (int)$campaign['id'];
		$row = $this->db->query("SELECT COUNT(*) AS calls, COUNT(DISTINCT lead_id) AS leads_called, SUM(CASE WHEN duration > 0 THEN 1 ELSE 0 END) AS connected, MAX(created_at) AS last_call FROM ci_call_logs WHERE campaign_id = {$cid}")->row_array();
		$today = $this->db->query("SELECT COUNT(*) AS c FROM ci_call_logs WHERE campaign_id = {$cid} AND created_at >= '" . date('Y-m-d 00:00:00') . "'")->row_array();
		$per_user = $this->db->query("SELECT L.user_id, U.name, COUNT(*) AS calls, COUNT(DISTINCT L.lead_id) AS leads_called FROM ci_call_logs L LEFT JOIN ci_user U ON U.id = L.user_id WHERE L.campaign_id = {$cid} GROUP BY L.user_id, U.name ORDER BY calls DESC")->result_array();

		$called = !empty($row['leads_called']) ? (int)$row['leads_called'] : 0;
		return array(
			'total_records' => $total,
			'leads_called' => $called,
			'calls' => !empty($row['calls']) ? (int)$row['calls'] : 0,
			'connected' => !empty($row['connected']) ? (int)$row['connected'] : 0,
			'calls_today' => !empty($today['c']) ? (int)$today['c'] : 0,
			'last_call' => !empty($row['last_call']) ? $row['last_call'] : '',
			'progress' => $total > 0 ? min(100, round(($called / $total) * 100, 1)) : 0,
			'per_user' => $per_user
		);
	}

	public function get_progress($uid, $queue_key)
	{
		$this->ensure_schema();
		$row = $this->db->where('user_id', (int)$uid)->where('queue_key', substr($queue_key, 0, 191))->get('ci_dialer_progress')->row_array();
		return !empty($row) ? $row : array('last_position' => 0, 'last_lead_id' => 0, 'updated_at' => '');
	}

	public function save_progress($uid, $queue_key, $position, $lead_id)
	{
		$this->ensure_schema();
		$key = substr($queue_key, 0, 191);
		$now = date('Y-m-d H:i:s');
		$this->db->query(
			"INSERT INTO ci_dialer_progress (user_id, queue_key, last_position, last_lead_id, updated_at) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE last_position = VALUES(last_position), last_lead_id = VALUES(last_lead_id), updated_at = VALUES(updated_at)",
			array((int)$uid, $key, (int)$position, (int)$lead_id, $now)
		);
	}
}
