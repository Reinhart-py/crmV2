<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/** Calling campaigns: list, create / edit, results page and CSV export. Dialing happens in the dialer (manager/calls?campaign=ID). */
class Ci_admin_campaigns extends CI_Controller
{
	private $uid = 0;
	private $role = 0;

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('session');
		$this->load->helper(array('form', 'url'));
		$this->load->model(array('ci_admin_model', 'comman_model', 'dialer_campaign_model'));
		header('Cache-Control: no-store, no-cache, must-revalidate');
		$this->uid = (int)$this->session->userdata('manager_id');
		$this->role = (int)$this->session->userdata('manager_role');
		if ($this->uid <= 0) {
			if ($this->input->is_ajax_request()) { echo json_encode(array('status' => false, 'message' => 'Please sign in again')); exit; }
			redirect('/manager/login');
			exit;
		}
		if (!crm_can('campaigns', 'read')) {
			if ($this->input->is_ajax_request()) { echo json_encode(array('status' => false, 'message' => 'No access to campaigns')); exit; }
			$this->session->set_flashdata('error_message', 'You do not have access to Calling Campaigns.');
			redirect(crm_landing_url());
			exit;
		}
		$this->dialer_campaign_model->ensure_schema();
	}

	private function _json($a)
	{
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($a);
	}

	private function _can_write()
	{
		return crm_can('campaigns', 'write');
	}

	/** Campaign the user may see, or null. */
	private function _campaign($id)
	{
		return $this->dialer_campaign_model->get_campaign((int)$id, $this->uid, $this->role);
	}

	private function _manageable($id)
	{
		$c = $this->_campaign($id);
		if (empty($c) || !$this->_can_write() || !$this->dialer_campaign_model->can_manage($c, $this->uid, $this->role)) return null;
		return $c;
	}

	public function index()
	{
		$M = $this->dialer_campaign_model;
		$list = $M->get_campaigns($this->uid, $this->role);
		$names = array();
		foreach ($this->db->select('id, name')->get('ci_user')->result_array() as $u) $names[(int)$u['id']] = $u['name'];
		$sum = array('all' => 0, 'Active' => 0, 'Scheduled' => 0, 'Paused' => 0, 'Ended' => 0, 'Completed' => 0, 'calls_today' => 0, 'connected' => 0, 'calls' => 0);
		foreach ($list as &$c) {
			$c['stats'] = $M->stats($c);
			$c['phase'] = $M->display_status($c);
			$c['can_manage'] = $this->_can_write() && $M->can_manage($c, $this->uid, $this->role);
			$c['people'] = array();
			foreach ($M->participant_ids($c) as $pid) if (isset($names[$pid])) $c['people'][] = $names[$pid];
			$sum['all']++;
			if (isset($sum[$c['phase']])) $sum[$c['phase']]++;
			$sum['calls_today'] += $c['stats']['calls_today'];
			$sum['connected'] += $c['stats']['connected'];
			$sum['calls'] += $c['stats']['calls'];
		}
		unset($c);
		$data = array(
			'info' => array('title' => 'Campaigns', 'page_heading' => 'Campaigns'),
			'campaigns' => $list,
			'sum' => $sum,
			'can_write' => $this->_can_write(),
			'objectives' => Dialer_campaign_model::$objectives
		);
		$this->load->view('manager/campaigns/index', $data);
	}

	public function form($id = 0)
	{
		$id = (int)$id;
		if (!$this->_can_write()) {
			$this->session->set_flashdata('error_message', 'You do not have Full access to Calling Campaigns.');
			redirect('manager/campaigns');
			return;
		}
		$campaign = null;
		if ($id > 0) {
			$campaign = $this->_manageable($id);
			if (empty($campaign)) {
				$this->session->set_flashdata('error_message', 'You cannot edit this campaign.');
				redirect('manager/campaigns');
				return;
			}
		}
		$copy = (int)$this->input->get('copy');
		if ($id === 0 && $copy > 0) {
			$src = $this->_campaign($copy);
			if (!empty($src)) {
				$campaign = $src;
				$campaign['id'] = 0;
				$campaign['name'] = 'Copy of ' . $src['name'];
				$campaign['status'] = 'Active';
				$campaign['start_date'] = null;
				$campaign['end_date'] = null;
			}
		}
		$data = array(
			'info' => array('title' => $id ? 'Edit campaign' : 'New campaign', 'page_heading' => 'Campaigns'),
			'campaign' => $campaign,
			'statuses' => $this->db->order_by('id', 'ASC')->get('ci_status')->result_array(),
			'countries' => $this->db->select('id, name')->order_by('name', 'ASC')->get('countries')->result_array(),
			'states' => (!empty($campaign['country'])) ? $this->db->select('id, name')->where('country_id', (int)$campaign['country'])->order_by('name', 'ASC')->get('states')->result_array() : array(),
			'users' => $this->dialer_campaign_model->selectable_users($this->uid, $this->role),
			'data_sources' => $this->db->query("SELECT DISTINCT data_source FROM ci_leads WHERE data_source IS NOT NULL AND data_source != '' ORDER BY data_source")->result_array(),
			'business_categories' => $this->comman_model->getAll(array('cat_type' => 'business_cat', 'status' => 1), '', 'ci_terms'),
			'database_categories' => $this->comman_model->getAll(array('cat_type' => 'database_cat', 'status' => 1), '', 'ci_terms'),
			'objectives' => Dialer_campaign_model::$objectives,
			'goal_types' => Dialer_campaign_model::$goal_types,
			'priorities' => Dialer_campaign_model::$priorities,
			'me' => $this->uid
		);
		$this->load->view('manager/campaigns/form', $data);
	}

	/** Campaign fields from the form, checked. Returns array(data, error). */
	private function _fields_from_post($existing)
	{
		$in = $this->input;
		$name = trim((string)$in->post('name', true));
		if ($name === '') return array(null, 'Give the campaign a name.');
		if (mb_strlen($name) > 150) return array(null, 'The name can have at most 150 characters.');
		$ref = preg_replace('/\D/', '', (string)$in->post('reference_no'));
		if (strlen($ref) > 20) return array(null, 'The reference number can have at most 20 digits.');

		$allowed = array();
		foreach ($this->dialer_campaign_model->selectable_users($this->uid, $this->role) as $u) $allowed[(int)$u['id']] = true;
		$people = $in->post('participants');
		$people = is_array($people) ? array_values(array_unique(array_filter(array_map('intval', $people)))) : array();
		// People already in an edited campaign stay even if the editor cannot pick them
		$keep = !empty($existing) ? $this->dialer_campaign_model->participant_ids($existing) : array();
		foreach ($people as $p) {
			if (!isset($allowed[$p]) && !in_array($p, $keep, true)) return array(null, 'You can only add yourself and your team.');
		}
		if (empty($people)) $people = array($this->uid);

		$stages = $in->post('status_ids');
		$stages = is_array($stages) ? array_values(array_unique(array_filter(array_map('intval', $stages)))) : array();

		$pipeline = (string)$in->post('pipeline_type');
		if (!in_array($pipeline, array('database', 'lead', 'all'), true)) $pipeline = 'all';
		$status = (string)$in->post('status');
		if (!in_array($status, array('Active', 'Paused', 'Completed'), true)) $status = 'Active';
		$objective = (string)$in->post('objective');
		if (!isset(Dialer_campaign_model::$objectives[$objective])) $objective = 'other';
		$priority = (string)$in->post('priority');
		if (!in_array($priority, Dialer_campaign_model::$priorities, true)) $priority = 'Normal';
		$goal_type = (string)$in->post('goal_type');
		if (!isset(Dialer_campaign_model::$goal_types[$goal_type])) $goal_type = '';

		$date = function ($v) {
			$v = trim((string)$v);
			return ($v !== '' && strtotime($v)) ? date('Y-m-d', strtotime($v)) : null;
		};
		$start = $date($in->post('start_date'));
		$end = $date($in->post('end_date'));
		if ($start && $end && $end < $start) return array(null, 'The end date is before the start date.');

		$num = function ($v, $min, $max) { return max($min, min($max, (int)preg_replace('/\D/', '', (string)$v))); };

		return array(array(
			'name' => $name,
			'reference_no' => $ref,
			'objective' => $objective,
			'priority' => $priority,
			'description' => trim((string)$in->post('description', true)),
			'country' => (int)$in->post('country'),
			'state' => (int)$in->post('state'),
			'city' => trim((string)$in->post('city', true)),
			'industry_category' => trim((string)$in->post('industry_category', true)),
			'database_category' => trim((string)$in->post('database_category', true)),
			'data_source' => trim((string)$in->post('data_source', true)),
			'pipeline_type' => $pipeline,
			'status_ids' => implode(',', $stages),
			'participants' => implode(',', $people),
			'daily_target' => $num($in->post('daily_target'), 0, 100000),
			'goal_type' => $goal_type,
			'goal_target' => $goal_type === '' ? 0 : $num($in->post('goal_target'), 0, 10000000),
			'max_attempts' => $num($in->post('max_attempts'), 0, 20),
			'retry_hours' => $num($in->post('retry_hours'), 0, 720),
			'script' => trim((string)$in->post('script', true)),
			'start_date' => $start,
			'end_date' => $end,
			'status' => $status
		), '');
	}

	public function ajax_save()
	{
		if (!$this->_can_write()) return $this->_json(array('status' => false, 'message' => 'You do not have Full access to campaigns.'));
		$id = (int)$this->input->post('id');
		$existing = null;
		if ($id > 0) {
			$existing = $this->_manageable($id);
			if (empty($existing)) return $this->_json(array('status' => false, 'message' => 'You cannot edit this campaign.'));
		}
		list($data, $err) = $this->_fields_from_post($existing);
		if ($err !== '') return $this->_json(array('status' => false, 'message' => $err));
		if ($id <= 0) $data['created_by'] = $this->uid;
		$saved = $this->dialer_campaign_model->save_campaign($data, $id);
		$this->ci_admin_model->log_admin_action($id > 0 ? 'campaign_update' : 'campaign_create', 'Campaign #' . $saved . ' ' . $data['name'], 'SUCCESS');
		$this->_json(array('status' => true, 'id' => $saved, 'message' => $id > 0 ? 'Campaign saved' : 'Campaign created'));
	}

	/** Live audience size for the form: records with a phone number that match, per person. */
	public function ajax_audience()
	{
		$c = array(
			'country' => (int)$this->input->post('country'),
			'state' => (int)$this->input->post('state'),
			'city' => trim((string)$this->input->post('city', true)),
			'industry_category' => trim((string)$this->input->post('industry_category', true)),
			'database_category' => trim((string)$this->input->post('database_category', true)),
			'data_source' => trim((string)$this->input->post('data_source', true)),
			'pipeline_type' => (string)$this->input->post('pipeline_type'),
			'status_ids' => is_array($this->input->post('status_ids')) ? implode(',', array_map('intval', $this->input->post('status_ids'))) : ''
		);
		$allowed = array();
		foreach ($this->dialer_campaign_model->selectable_users($this->uid, $this->role) as $u) $allowed[(int)$u['id']] = $u['name'];
		$people = $this->input->post('participants');
		$people = is_array($people) ? array_values(array_filter(array_map('intval', $people), function ($p) use ($allowed) { return isset($allowed[$p]); })) : array();
		if (empty($people)) $people = array($this->uid);
		$r = $this->dialer_campaign_model->audience_counts($c, $people);
		$per = array();
		foreach ($r['people'] as $pid => $n) $per[] = array('name' => isset($allowed[$pid]) ? $allowed[$pid] : 'User #' . $pid, 'n' => $n);
		$this->_json(array('status' => true, 'total' => $r['total'], 'people' => $per));
	}

	/** Quick status change from the list or the campaign page. */
	public function ajax_status()
	{
		$c = $this->_manageable((int)$this->input->post('id'));
		$status = (string)$this->input->post('status');
		if (empty($c)) return $this->_json(array('status' => false, 'message' => 'You cannot change this campaign.'));
		if (!in_array($status, array('Active', 'Paused', 'Completed'), true)) return $this->_json(array('status' => false, 'message' => 'Unknown status'));
		$this->dialer_campaign_model->save_campaign(array('status' => $status), (int)$c['id']);
		$this->ci_admin_model->log_admin_action('campaign_status', 'Campaign #' . (int)$c['id'] . ' ' . $status, 'SUCCESS');
		$c['status'] = $status;
		$this->_json(array('status' => true, 'state' => $this->dialer_campaign_model->display_status($c)));
	}

	/** Delete: only campaigns that have no calls yet (otherwise mark them Completed). */
	public function ajax_delete()
	{
		$c = $this->_manageable((int)$this->input->post('id'));
		if (empty($c)) return $this->_json(array('status' => false, 'message' => 'You cannot delete this campaign.'));
		$calls = (int)$this->db->where('campaign_id', (int)$c['id'])->count_all_results('ci_call_logs');
		if ($calls > 0) return $this->_json(array('status' => false, 'message' => 'This campaign has calls. Mark it Completed instead.'));
		$this->db->where('id', (int)$c['id'])->delete('ci_dialer_campaigns');
		$this->db->like('queue_key', 'campaign:' . (int)$c['id'] . '|', 'after')->delete('ci_dialer_progress');
		$this->ci_admin_model->log_admin_action('campaign_delete', 'Campaign #' . (int)$c['id'] . ' ' . $c['name'], 'SUCCESS');
		$this->_json(array('status' => true));
	}

	public function view($id = 0)
	{
		$c = $this->_campaign($id);
		if (empty($c)) {
			$this->session->set_flashdata('error_message', 'Campaign not found.');
			redirect('manager/campaigns');
			return;
		}
		$M = $this->dialer_campaign_model;
		$c['phase'] = $M->display_status($c);
		$c['can_manage'] = $this->_can_write() && $M->can_manage($c, $this->uid, $this->role);
		$c['created_by_name'] = (string)$this->db->select('name')->where('id', (int)$c['created_by'])->get('ci_user')->row('name');
		$c['country_name'] = $c['country'] ? (string)$this->db->select('name')->where('id', (int)$c['country'])->get('countries')->row('name') : '';
		$c['state_name'] = $c['state'] ? (string)$this->db->select('name')->where('id', (int)$c['state'])->get('states')->row('name') : '';
		$stage_names = array();
		$ids = array_filter(array_map('intval', explode(',', (string)$c['status_ids'])));
		if ($ids) foreach ($this->db->select('status')->where_in('id', $ids)->get('ci_status')->result_array() as $s) $stage_names[] = $s['status'];
		$c['stage_names'] = $stage_names;
		$data = array(
			'info' => array('title' => $c['name'], 'page_heading' => 'Campaigns'),
			'c' => $c,
			'r' => $M->report($c),
			'objectives' => Dialer_campaign_model::$objectives,
			'goal_types' => Dialer_campaign_model::$goal_types
		);
		$this->load->view('manager/campaigns/view', $data);
	}

	/** CSV of every call made in the campaign. */
	public function export($id = 0)
	{
		$c = $this->_campaign($id);
		if (empty($c)) show_404();
		$rows = $this->db->query("SELECT L.created_at, U.name agent, L.customer_name, L.phone_number, L.call_type, L.call_status, L.disposition, L.duration, L.notes, A.lead_id ref
			FROM ci_call_logs L LEFT JOIN ci_user U ON U.id = L.user_id LEFT JOIN ci_leads A ON A.id = L.lead_id
			WHERE L.campaign_id = " . (int)$c['id'] . " AND L.call_status NOT IN ('Initiated', 'Dialing') ORDER BY L.id ASC")->result_array();
		$file = preg_replace('~[^A-Za-z0-9]+~', '-', $c['name']) . '-calls-' . date('Y-m-d') . '.csv';
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . trim($file, '-') . '"');
		$out = fopen('php://output', 'w');
		fwrite($out, "\xEF\xBB\xBF");
		fputcsv($out, array('Date', 'Agent', 'Customer', 'Phone', 'Record ID', 'Type', 'Status', 'Result', 'Seconds', 'Notes'));
		foreach ($rows as $r) {
			$cells = array($r['created_at'], $r['agent'], $r['customer_name'], $r['phone_number'], $r['ref'], $r['call_type'], $r['call_status'], $r['disposition'], (int)$r['duration'], $r['notes']);
			// keep spreadsheet formulas out of the file
			foreach ($cells as &$v) if (is_string($v) && $v !== '' && strpos('=+-@', $v[0]) !== false && !preg_match('~^\+?[\d\s()-]+$~', $v)) $v = "'" . $v;
			unset($v);
			fputcsv($out, $cells);
		}
		fclose($out);
	}
}
